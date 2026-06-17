<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'summary',
        'description',
        'price',
        'duration',
        'level',
        'starts_at',
        'checkout_url',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'integer',
        'starts_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(CourseOrder::class);
    }
}
