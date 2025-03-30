<?php

namespace App\Livewire\Editor;

use App\Models\FacialFeature;
use App\Models\FeatureType;
use App\Models\FeatureCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class FeatureLibrary extends Component
{
    use WithPagination;
    
    public $selectedCategory = '';
    public $selectedSubcategory = null;
    public $search = '';
    public $viewedCategories = [];
    
    /**
     * Get the feature types for the dropdown.
     */
    public function getFeatureTypesProperty()
    {
        return FeatureType::all();
    }
    
    /**
     * Get the subcategories based on the selected feature type.
     */
    public function getSubcategoriesProperty()
    {
        if (!$this->selectedCategory) {
            return collect();
        }
        
        return FeatureCategory::whereHas('featureType', function ($query) {
            $query->where('name', $this->selectedCategory);
        })->get();
    }
    
    /**
     * Get the features based on the selected category, subcategory and search term.
     */
    public function getFeaturesProperty()
    {
        if (!$this->selectedCategory) {
            return collect();
        }
        
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
        
        if ($this->search) {
            $query->where(function ($subquery) {
                $subquery->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('feature_code', 'like', '%' . $this->search . '%');
            });
        }
        
        $features = $query->with(['featureType', 'category'])->get();
        Log::info("Found {$features->count()} features");
        
        return $features;
    }
    
    /**
     * Select a subcategory.
     */
    public function selectSubcategory($categoryId)
    {
        if ($this->selectedSubcategory == $categoryId) {
            $this->selectedSubcategory = null; // Toggle off if already selected
        } else {
            $this->selectedSubcategory = $categoryId;
        }
    }
    
    /**
     * Handle feature selection.
     */
    public function selectFeature($featureId)
    {
        Log::info("Feature selected: {$featureId}");
        // Add logic for feature selection - this will be implemented later
        // This would involve updating the composite canvas with the selected feature
        $this->dispatch('feature-selected', featureId: $featureId);
    }
    
    /**
     * Handle when category is changed
     * Reset subcategory selection and manage scroll position
     */
    public function updatedSelectedCategory($value)
    {
        // Reset subcategory whenever category changes
        $this->selectedSubcategory = null;
        
        // Always scroll to top when changing categories
        if ($value) {
            $this->dispatch('scrollToTop');
        }
        
        Log::info("Category changed to: {$value}, scrolling to top");
    }
    
    public function render()
    {
        return view('livewire.editor.feature-library', [
            'features' => $this->features,
            'subcategories' => $this->subcategories,
        ]);
    }
}
