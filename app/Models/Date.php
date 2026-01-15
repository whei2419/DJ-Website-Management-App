<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Date extends Model
{
    use HasFactory;

    protected $fillable = ['date'];

    protected $casts = [
        'date' => 'date',
    ];

    // DJ assignment removed: dates are standalone now
}
