<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmazonProducts extends Model
{
    use HasFactory;

    protected $fillable = ['amazon_lot_id', 'name', 'url', 'pvp', 'price'];
}
