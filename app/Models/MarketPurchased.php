<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;




class MarketPurchased extends Model
{
    use HasFactory;
    const MATRIX_20 = 1;
    const MATRIX_200 = 2;
    const MATRIX_2000 =  3;

    protected $table = 'market_purchaseds';

    protected $fillable = ['user_id', 'cyborg_id', 'order_id','level','type'];

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

    public function getType()
    {
        switch ($this->attributes['type']) {
            case 1:
                return 'Matrix Initial';
                break;
            case 2:
                return 'Matrix 200';
                break;
            case 3:
                return 'Matrix 2000';
                break;

        }
    }

}
