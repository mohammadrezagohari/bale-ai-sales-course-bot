<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseOrder extends Model
{
    protected $fillable = [
        'course_id',
        'lead_id',
        'amount',
        'status',
        'payment_reference',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
