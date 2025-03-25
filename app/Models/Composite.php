<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Composite extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'case_id',
        'witness_id',
        'user_id',
        'title',
        'description',
        'canvas_width',
        'canvas_height',
        'final_image_path',
        'suspect_gender',
        'suspect_ethnicity',
        'suspect_age_range',
        'suspect_height',
        'suspect_body_build',
        'suspect_additional_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'canvas_width' => 'integer',
        'canvas_height' => 'integer',
    ];

    /**
     * Get the case that owns the composite.
     */
    public function caseRecord(): BelongsTo
    {
        return $this->belongsTo(CaseRecord::class, 'case_id');
    }

    /**
     * Get the witness that owns the composite.
     */
    public function witness(): BelongsTo
    {
        return $this->belongsTo(Witness::class);
    }

    /**
     * Get the user that created the composite.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
