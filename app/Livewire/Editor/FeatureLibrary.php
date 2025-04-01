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
        
        $featureType = FeatureType::where('name', $this->selectedCategory)->first();
        
        if (!$featureType) {
            return collect();
        }
        
        return FeatureCategory::where('feature_type_id', $featureType->id)->get();
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
        $this->selectedSubcategory = $categoryId;
    }
    
    /**
     * Handle feature selection.
     */
    public function selectFeature($featureId)
    {
        // Get the feature details
        $feature = \App\Models\FacialFeature::find($featureId);
        
        if (!$feature) {
            \Illuminate\Support\Facades\Log::error("Feature not found: {$featureId}");
            return;
        }
        
        // Log feature selection for debugging
        \Illuminate\Support\Facades\Log::info("Feature selected: {$featureId}", [
            'name' => $feature->name,
            'image_path' => $feature->image_path
        ]);
        
        // Dispatch both events to ensure it's caught
        $this->dispatch('feature-selected', $featureId);
        
        // Also dispatch a direct update-canvas event as a backup approach
        $this->dispatch('direct-update-canvas', [
            'feature' => [
                'id' => $feature->id,
                'image_path' => $feature->image_path,
                'name' => $feature->name,
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
        
        // Always scroll to top when changing categories
        if ($value) {
            $this->dispatch('scrollToTop');
        }
        
        Log::info("Category changed to: {$value}, scrolling to top");
    }
    
    public function render()
    {
        $query = FacialFeature::query();
        
        // Filter by search term if provided
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
        
        // Filter by main category if selected
        if ($this->selectedCategory) {
            $featureType = FeatureType::where('name', $this->selectedCategory)->first();
            
            if ($featureType) {
                $query->where('feature_type_id', $featureType->id);
                
                // Filter by subcategory if selected
                if ($this->selectedSubcategory) {
                    $query->where('feature_category_id', $this->selectedSubcategory);
                }
            }
        }
        
        $features = $query->get();
        
        // Get subcategories for the selected main category
        $subcategories = [];
        if ($this->selectedCategory) {
            $featureType = FeatureType::where('name', $this->selectedCategory)->first();
            if ($featureType) {
                $subcategories = FeatureCategory::where('feature_type_id', $featureType->id)->get();
            }
        }
        
        return view('livewire.editor.feature-library', [
            'features' => $features,
            'subcategories' => $subcategories
        ]);
    }
}
