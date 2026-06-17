<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    protected $fillable = [
        'bale_user_id',
        'chat_id',
        'intent',
        'first_name',
        'last_name',
        'username',
        'last_message',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function courseOrders(): HasMany
    {
        return $this->hasMany(CourseOrder::class);
    }

    public function webinarRegistrations(): HasMany
    {
        return $this->hasMany(WebinarRegistration::class);
    }
}
