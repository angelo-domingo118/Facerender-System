<?php

namespace App\Services;

use App\Models\Composite;
use App\Models\CompositeFacialFeature;
use App\Models\FacialFeature;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CompositeFeaturesService
{
    /**
     * Cache duration in seconds
     */
    protected const CACHE_DURATION = 3600; // 1 hour
    
    /**
     * Get all features for a composite
     */
    public function getCompositeFeatures(int $compositeId): Collection
    {
        return Cache::remember("composite.{$compositeId}.features", self::CACHE_DURATION, function () use ($compositeId) {
            return CompositeFacialFeature::with('facialFeature')
                ->where('composite_id', $compositeId)
                ->orderBy('z_index')
                ->get();
        });
    }
    
    /**
     * Add a feature to a composite
     */
    public function addFeature(int $compositeId, int $facialFeatureId, array $attributes = []): CompositeFacialFeature
    {
        // Find the highest z-index
        $maxZIndex = CompositeFacialFeature::where('composite_id', $compositeId)
            ->max('z_index') ?? 0;
        
        // Default visual adjustments if not provided
        $visualAdjustments = $attributes['visual_adjustments'] ?? [
            'brightness' => 0,
            'contrast' => 1.0,
            'saturation' => 1.0,
            'sharpness' => 0,
            'feathering' => 0,
            'skinTone' => 0
        ];
        
        // Create the feature
        $feature = CompositeFacialFeature::create([
            'composite_id' => $compositeId,
            'facial_feature_id' => $facialFeatureId,
            'position_x' => $attributes['position_x'] ?? 0,
            'position_y' => $attributes['position_y'] ?? 0,
            'z_index' => $maxZIndex + 1,
            'scale_x' => $attributes['scale_x'] ?? 1.0,
            'scale_y' => $attributes['scale_y'] ?? 1.0,
            'rotation' => $attributes['rotation'] ?? 0,
            'opacity' => $attributes['opacity'] ?? 1.0,
            'visible' => $attributes['visible'] ?? true,
            'locked' => $attributes['locked'] ?? false,
            'visual_adjustments' => $visualAdjustments,
        ]);
        
        // Clear the cache
        $this->clearCache($compositeId);
        
        return $feature;
    }
    
    /**
     * Update a feature's properties
     */
    public function updateFeature(int $featureId, array $attributes): bool
    {
        $feature = CompositeFacialFeature::find($featureId);
        
        if (!$feature) {
            return false;
        }
        
        // Handle visual adjustments separately if provided
        if (isset($attributes['visual_adjustments'])) {
            // If it's a specific adjustment, merge it with existing ones
            if (is_array($attributes['visual_adjustments']) && count(array_filter(array_keys($attributes['visual_adjustments']), 'is_string')) > 0) {
                $currentAdjustments = $feature->visual_adjustments ?? [];
                $attributes['visual_adjustments'] = array_merge($currentAdjustments, $attributes['visual_adjustments']);
            }
        }
        
        $result = $feature->update($attributes);
        
        // Clear the cache for this composite
        $this->clearCache($feature->composite_id);
        
        return $result;
    }
    
    /**
     * Remove a feature from a composite
     */
    public function removeFeature(int $featureId): bool
    {
        $feature = CompositeFacialFeature::find($featureId);
        
        if (!$feature) {
            return false;
        }
        
        $compositeId = $feature->composite_id;
        $result = $feature->delete();
        
        // Clear the cache
        $this->clearCache($compositeId);
        
        return $result;
    }
    
    /**
     * Update the order of features in a composite
     */
    public function updateFeatureOrder(int $compositeId, array $featureIds): bool
    {
        $zIndexMap = [];
        
        // Loop through the feature IDs in the order they were provided
        // The first ID should have the lowest z-index (back of canvas)
        foreach ($featureIds as $index => $featureId) {
            $zIndexMap[$featureId] = $index;
        }
        
        $result = CompositeFacialFeature::updateOrder($zIndexMap);
        
        // Clear the cache
        $this->clearCache($compositeId);
        
        return $result;
    }
    
    /**
     * Clear all features for a composite
     */
    public function clearFeatures(int $compositeId): bool
    {
        $result = CompositeFacialFeature::where('composite_id', $compositeId)->delete();
        
        // Clear the cache
        $this->clearCache($compositeId);
        
        return $result > 0;
    }
    
    /**
     * Clear cache for a composite
     */
    protected function clearCache(int $compositeId): void
    {
        Cache::forget("composite.{$compositeId}.features");
    }
} 