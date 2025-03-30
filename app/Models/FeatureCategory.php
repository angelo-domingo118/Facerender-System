<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeatureCategory extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'feature_type_id',
        'name',
    ];
    
    /**
     * Get the feature type that owns the category.
     */
    public function featureType(): BelongsTo
    {
        return $this->belongsTo(FeatureType::class);
    }
    
    /**
     * Get the features for the category.
     */
    public function features(): HasMany
    {
        return $this->hasMany(FacialFeature::class);
    }
}
