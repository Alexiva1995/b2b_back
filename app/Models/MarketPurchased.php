<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketPurchased extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'cyborg_id', 'order_id'];
}
