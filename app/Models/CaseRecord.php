<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaseRecord extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cases';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'reference_number',
        'status',
        'incident_type',
        'incident_date',
        'incident_time',
        'location',
        'notes',
        'user_id',
        'is_pinned',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'incident_date' => 'date',
        'incident_time' => 'datetime',
        'is_pinned' => 'boolean',
    ];

    /**
     * Get the user that owns the case.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the witnesses for the case.
     */
    public function witnesses(): HasMany
    {
        return $this->hasMany(Witness::class, 'case_id');
    }

    /**
     * Get the composites for the case.
     */
    public function composites(): HasMany
    {
        return $this->hasMany(Composite::class, 'case_id');
    }
}
