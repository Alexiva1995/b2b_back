<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profitability extends Model
{
    use HasFactory;

    protected $table= 'profitability';
    protected $fillable = [
        'user_id',
        'invest_id',
        'amount',
        'status',
        'amount_retired',
        'amount_available',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function investment()
    {
        return $this->belongsTo(Invesment::class, 'invest_id');
    }
}
