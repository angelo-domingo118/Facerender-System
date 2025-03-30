<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacialFeature extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'feature_type_id',
        'feature_category_id',
        'feature_code',
        'name',
        'image_path',
        'gender',
    ];
    
    /**
     * Get the feature type that owns the facial feature.
     */
    public function featureType(): BelongsTo
    {
        return $this->belongsTo(FeatureType::class);
    }
    
    /**
     * Get the category that owns the facial feature.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FeatureCategory::class, 'feature_category_id');
    }
    
    /**
     * Get the image URL for the facial feature.
     */
    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . $this->image_path);
    }
}
