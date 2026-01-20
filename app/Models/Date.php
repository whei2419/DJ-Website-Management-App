<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Date extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'event_name', 'location'];

    protected $casts = [
        'date' => 'date',
    ];

    public function djs()
    {
        return $this->hasMany(DJ::class);
    }
}
