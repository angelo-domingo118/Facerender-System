<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Witness extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'case_id',
        'name',
        'age',
        'gender',
        'contact_number',
        'address',
        'relationship_to_case',
        'interview_date',
        'interview_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'age' => 'integer',
        'interview_date' => 'date',
    ];

    /**
     * Get the case that owns the witness.
     */
    public function caseRecord(): BelongsTo
    {
        return $this->belongsTo(CaseRecord::class, 'case_id');
    }

    /**
     * Get the composites for the witness.
     */
    public function composites(): HasMany
    {
        return $this->hasMany(Composite::class);
    }
}
