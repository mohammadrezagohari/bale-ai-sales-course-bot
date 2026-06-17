<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebinarRegistration extends Model
{
    protected $fillable = [
        'webinar_id',
        'lead_id',
        'status',
        'notes',
    ];

    public function webinar(): BelongsTo
    {
        return $this->belongsTo(Webinar::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
