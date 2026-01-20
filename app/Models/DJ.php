<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DJ extends Model
{
    use HasFactory;
    
    /**
     * Explicit table name to avoid incorrect pluralization (DJ -> d_j_s).
     */
    protected $table = 'djs';
    
    protected $fillable = [
        'name',
        'slot',
        'date_id',
        'video_url',
        'video_path',
        'preview_video_path',
        'poster_path',
    ];

    public function date()
    {
        return $this->belongsTo(Date::class);
    }
}