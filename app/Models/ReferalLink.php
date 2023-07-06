<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferalLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'link_code',
        'cyborg_id',
        'right',
        'left',
    ];

    public function cyborg()
    {
        return $this->belongsTo(Market::class, 'cyborg_id');
    }

    
}
