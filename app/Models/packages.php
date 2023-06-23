<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class packages extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'package',
        'description',
        'gain',
        'type',
        'level'
    ];
}
