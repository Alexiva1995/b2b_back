<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmazonInvestment extends Model
{
    use HasFactory;

    protected $fillable = ['amazon_category_id', 'user_id', 'invested', 'status', 'gain',' order_id', 'date_start'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
