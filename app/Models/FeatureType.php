<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeatureType extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];
    
    /**
     * Get the categories for the feature type.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(FeatureCategory::class);
    }
    
    /**
     * Get the features for the feature type.
     */
    public function features(): HasMany
    {
        return $this->hasMany(FacialFeature::class);
    }
}
