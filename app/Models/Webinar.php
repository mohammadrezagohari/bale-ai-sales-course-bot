<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Webinar extends Model
{
    protected $fillable = [
        'title',
        'description',
        'starts_at',
        'registration_url',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(WebinarRegistration::class);
    }
}
