<?php

namespace App\Livewire\Editor;

use App\Models\FacialFeature;
use App\Models\FeatureType;
use App\Models\FeatureCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Reactive;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FeatureLibrary extends Component
{
    use WithPagination;
    
    public $selectedCategory = '';
    public $selectedSubcategory = null;
    public $search = '';
    public $viewedCategories = [];
    public $activeFeatureIds = [];
    protected $cachedFeatures = [];
    
    protected $listeners = [
        'refresh-feature-library' => '$refresh',
        'active-features-updated' => 'handleActiveFeaturesUpdate'
    ];

    // Make search reactive
    protected $queryString = ['search'];
    
    // Update on every keystroke for search
    protected function getListeners()
    {
        return array_merge($this->listeners, [
            'search-updated' => 'updatedSearch'
        ]);
    }

    /**
     * Hook for when search is updated
     */
    public function updatedSearch()
    {
        Log::info("Search updated: {$this->search}");
        $this->resetPage();
    }
    
    /**
     * Get the feature types for the dropdown.
     */
    public function getFeatureTypesProperty()
    {
        return Cache::remember('feature-types', 3600, function() {
            return FeatureType::all();
        });
    }
    
    /**
     * Get the subcategories based on the selected feature type.
     */
    public function getSubcategoriesProperty()
    {
        if (!$this->selectedCategory) {
            return collect();
        }
        
        $cacheKey = 'subcategories-' . $this->selectedCategory;
        
        return Cache::remember($cacheKey, 1800, function() {
            $featureType = FeatureType::where('name', $this->selectedCategory)->first();
            
            if (!$featureType) {
                return collect();
            }
            
            return FeatureCategory::where('feature_type_id', $featureType->id)->get();
        });
    }
    
    /**
     * Get the features based on the selected category, subcategory and search term.
     */
    public function getFeaturesProperty()
    {
        if (!$this->selectedCategory) {
            return collect();
        }
        
        // If search is active, don't use cache
        if (!empty($this->search)) {
            Log::info("Using search: '{$this->search}' in category {$this->selectedCategory}");
            return $this->fetchFeaturesFromDatabase();
        }
        
        $cacheKey = "features-{$this->selectedCategory}-{$this->selectedSubcategory}";
        
        if (isset($this->cachedFeatures[$cacheKey])) {
            return $this->cachedFeatures[$cacheKey];
        }
        
        // Short-lived cache to improve performance during user session
        $features = Cache::remember($cacheKey, 300, function() {
            return $this->fetchFeaturesFromDatabase();
        });
        
        // Store in memory for this component instance
        $this->cachedFeatures[$cacheKey] = $features;
        
        return $features;
    }
    
    /**
     * Fetch features from database with current filters
     */
    protected function fetchFeaturesFromDatabase()
    {
        $query = FacialFeature::query();
        
        // Log for debugging
        Log::info("Fetching features for category: {$this->selectedCategory}");
        
        $featureType = FeatureType::where('name', $this->selectedCategory)->first();
        
        if (!$featureType) {
            Log::error("Feature type not found: {$this->selectedCategory}");
            return collect();
        }
        
        $query->where('feature_type_id', $featureType->id);
        
        if ($this->selectedSubcategory) {
            $query->where('feature_category_id', $this->selectedSubcategory);
            Log::info("Filter by subcategory ID: {$this->selectedSubcategory}");
        }
        
        if (!empty($this->search)) {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function ($subquery) use ($searchTerm) {
                $subquery->where('name', 'like', $searchTerm)
                    ->orWhere('feature_code', 'like', $searchTerm);
            });
            
            Log::info("Applied search filter: {$this->search}");
        }
        
        // Eager load relationships to avoid N+1 issues
        $features = $query->with(['featureType', 'category'])->get();
        Log::info("Found {$features->count()} features");
        
        return $features;
    }
    
    /**
     * Select a subcategory.
     */
    public function selectSubcategory($categoryId)
    {
        $this->selectedSubcategory = $categoryId;
        $this->resetPage();
    }
    
    /**
     * Clear search
     */
    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    /**
     * Clear selected subcategory
     */
    public function clearSubcategory()
    {
        $this->selectedSubcategory = null;
        $this->resetPage();
    }
    
    /**
     * Handle feature selection.
     */
    public function selectFeature($featureId)
    {
        // Get the feature details
        $feature = FacialFeature::find($featureId);
        
        if (!$feature) {
            Log::error("Feature not found: {$featureId}");
            return;
        }
        
        // --- Optimistic Update --- 
        // Immediately update the checkmark locally for instant UI feedback
        $featureTypeId = $feature->feature_type_id;
        
        // Find and remove any existing active ID of the same feature type
        $this->activeFeatureIds = collect($this->activeFeatureIds)->filter(function ($activeId) use ($featureTypeId, $featureId) {
            // Find the feature associated with the active ID (check current rendered features)
            $activeFeature = $this->features->firstWhere('id', $activeId);
            
            // Keep if feature not found, or if it's a different type, or if it's the same feature being clicked again
            return !$activeFeature || $activeFeature->feature_type_id != $featureTypeId || $activeId == $featureId;
        })->all();

        // Add the newly selected feature ID
        if (!in_array($featureId, $this->activeFeatureIds)) {
            $this->activeFeatureIds[] = $featureId;
        }
        // --------------------------

        // Log feature selection for debugging
        Log::info("Feature selected: {$featureId}", [
            'name' => $feature->name,
            'image_path' => $feature->image_path,
            'feature_type_id' => $feature->feature_type_id
        ]);
        
        // Dispatch both events to ensure it's caught
        $this->dispatch('feature-selected', $featureId);
        
        // Also dispatch a direct update-canvas event as a backup approach
        $this->dispatch('direct-update-canvas', [
            'feature' => [
                'id' => $feature->id,
                'image_path' => $feature->image_path,
                'name' => $feature->name,
                'feature_type' => $feature->feature_type_id,
                'position' => [
                    'x' => 300,
                    'y' => 300,
                    'scale' => 1,
                    'rotation' => 0
                ]
            ]
        ]);
    }
    
    /**
     * Handle when category is changed
     * Reset subcategory selection and manage scroll position
     */
    public function updatedSelectedCategory($value)
    {
        // Reset subcategory whenever category changes
        $this->selectedSubcategory = null;
        $this->search = ''; // Clear search when changing categories
        $this->resetPage();
        
        // Always scroll to top when changing categories
        if ($value) {
            $this->dispatch('scrollToTop');
            
            // Prefetch features for the selected category
            $this->prefetchFeaturesForCategory($value);
        }
        
        Log::info("Category changed to: {$value}, scrolling to top");
    }
    
    /**
     * Navigate to the next category in sequence
     */
    public function nextCategory()
    {
        // Define the category sequence for facial composite construction
        $categorySequence = [
            'face',
            'hair',
            'eyes',
            'eyebrows',
            'nose',
            'mouth',
            'ears',
            'accessories'
        ];
        
        if (empty($this->selectedCategory)) {
            // If no category selected, start with the first one
            $this->selectedCategory = $categorySequence[0];
            return;
        }
        
        // Find current position in sequence
        $currentIndex = array_search($this->selectedCategory, $categorySequence);
        
        if ($currentIndex !== false && $currentIndex < count($categorySequence) - 1) {
            // Move to next category
            $this->selectedCategory = $categorySequence[$currentIndex + 1];
        } else {
            // Wrap around to the first category
            $this->selectedCategory = $categorySequence[0];
        }
        
        // Trigger scroll to top for new category
        $this->dispatch('scrollToTop');
    }
    
    /**
     * Prefetch features for a category to improve performance
     */
    protected function prefetchFeaturesForCategory($category)
    {
        // Create a background job to warmup the cache
        $cacheKey = "features-{$category}-null";
        
        if (!isset($this->cachedFeatures[$cacheKey])) {
            $featureType = FeatureType::where('name', $category)->first();
            
            if ($featureType) {
                $query = FacialFeature::query()
                    ->where('feature_type_id', $featureType->id)
                    ->with(['featureType', 'category']);
                
                $features = $query->get();
                
                // Store in memory
                $this->cachedFeatures[$cacheKey] = $features;
                
                // Also store in cache
                Cache::put($cacheKey, $features, 300);
            }
        }
    }
    
    /**
     * Update the list of active feature IDs.
     */
    public function handleActiveFeaturesUpdate($activeIds)
    {
        $this->activeFeatureIds = $activeIds;
        Log::info('Received active-features-updated', ['active_ids' => $this->activeFeatureIds]);
    }
    
    public function render()
    {
        return view('livewire.editor.feature-library', [
            'features' => $this->features,
            'subcategories' => $this->subcategories
        ]);
    }
}
