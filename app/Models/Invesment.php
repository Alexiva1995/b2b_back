<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invesment extends Model
{
    use HasFactory;

    protected $table = 'investments';
    protected $fillable = [
        'user_id',
        'package_id',
        'order_id',
        'capital',
        'invested',
        'expiration_date',
        'gain',
        'max_gain',
        'status',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function profit()
    {
        return$this->hasMany(Profitability::class);
    }
}
