<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteEvent extends Model
{
    protected $table = 'site_events';

    protected $fillable = ['event', 'meta', 'ip', 'user_agent'];

    protected $casts = [
        'meta' => 'array',
    ];
}
