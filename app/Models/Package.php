<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'package',
        'description',
        'gain',
        'amount',
        'type',
        'level',
        'investment_time',
        'max_amount',
    ];
}
