<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DJ extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'slot',
        'video_url',
        'video_path',
        'preview_video_path',
        'poster_path',
    ];

    public function dates()
    {
        return $this->hasMany(Date::class);
    }
}