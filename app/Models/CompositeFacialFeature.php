<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class CompositeFacialFeature extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'composite_id',
        'facial_feature_id',
        'position_x',
        'position_y',
        'z_index',
        'scale_x',
        'scale_y',
        'rotation',
        'opacity',
        'visible',
        'locked',
        'visual_adjustments',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'position_x' => 'float',
        'position_y' => 'float',
        'z_index' => 'integer',
        'scale_x' => 'float',
        'scale_y' => 'float',
        'rotation' => 'float',
        'opacity' => 'float',
        'visible' => 'boolean',
        'locked' => 'boolean',
        'visual_adjustments' => 'array',
    ];

    /**
     * Get the composite that owns the feature.
     */
    public function composite(): BelongsTo
    {
        return $this->belongsTo(Composite::class);
    }

    /**
     * Get the facial feature that is used.
     */
    public function facialFeature(): BelongsTo
    {
        return $this->belongsTo(FacialFeature::class);
    }

    /**
     * Get a specific visual adjustment value.
     */
    public function getVisualAdjustment(string $key, $default = null)
    {
        if (!is_array($this->visual_adjustments)) {
            return $default;
        }

        return $this->visual_adjustments[$key] ?? $default;
    }

    /**
     * Set a specific visual adjustment value.
     */
    public function setVisualAdjustment(string $key, $value): self
    {
        $adjustments = is_array($this->visual_adjustments) ? $this->visual_adjustments : [];
        $adjustments[$key] = $value;
        $this->visual_adjustments = $adjustments;
        
        return $this;
    }

    /**
     * Update the z-index values for multiple features in a single transaction.
     * 
     * @param array $featureOrders Array with feature IDs as keys and new z-index as values
     * @return bool
     */
    public static function updateOrder(array $featureOrders): bool
    {
        return DB::transaction(function () use ($featureOrders) {
            foreach ($featureOrders as $featureId => $zIndex) {
                static::where('id', $featureId)->update(['z_index' => $zIndex]);
            }
            return true;
        });
    }

    /**
     * Batch update multiple features at once.
     * 
     * @param array $features Array of features with their updates
     * @return bool
     */
    public static function batchUpdate(array $features): bool
    {
        return DB::transaction(function () use ($features) {
            foreach ($features as $feature) {
                if (isset($feature['id'])) {
                    $model = static::find($feature['id']);
                    if ($model) {
                        unset($feature['id']);
                        $model->update($feature);
                    }
                }
            }
            return true;
        });
    }
} 