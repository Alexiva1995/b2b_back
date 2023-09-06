<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'hash',
        'image',
        'type',
        'status',
        'membership_packages_id',
        'coupon_id',
        'cyborg_id',
        'type_purchsed',
        'is_manual',
    ];

    public function user()
    {
        return $this->BelongsTo(User::class, 'user_id', 'id');
    }

    public function scopeFilter($query, $filter)
    {
        if($filter)
            $query->whereHas('user', function($query) use ($filter)
            {
                return $query->whereRaw("CONCAT(`name`,' ',`last_name`) LIKE ?",['%'.$filter.'%'])->orWhereRaw("email LIKE ?", ['%'.$filter.'%']);
            })
            ->orWhere('user_id', $filter);
    }

    public function user_referral()
    {
        return $this->user()->referrals()->first();
    }

    public function transaction()
    {
        return $this->hasOne(FutswapTransaction::class);
    }

    public function pagueloFacilTransaction()
    {
        return $this->hasOne(PagueloFacilTransaction::class, 'order_id', 'id');
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'order_id', 'id');
    }

    //Relacion de B2B
    public function packagesB2B()
    {
       return  $this->belongsTo(Market::class, 'cyborg_id');
    }
    //Fin

    public function packageMembership()
    {
        return $this->hasOne(PackageMembership::class, 'id', 'membership_packages_id');
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class, 'id', 'order_id');
    }
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
    public function getStatus()
    {
        switch ($this->status) {
            case '0':
                return 'Pending';
            case '1':
                return 'Complete';
            case '2':
                return 'Partially paid';
            case '3':
                return 'Rejected';
        }
    }
    public function coinpaymentTransaction()
    {
        return $this->hasOne(CoinpaymentTransaction::class);
    }

    public function marketPurchased()
    {
        return $this->hasOne(MarketPurchased::class);
    }
}
