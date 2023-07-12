<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketPurchased extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'cyborg_id', 'order_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cyborg()
    {
        return $this->belongsTo(Market::class, 'cyborg_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
