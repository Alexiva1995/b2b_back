<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'document_id',
        'postal_code',
        'phone_number',
        'status',
        'state',
        'street',
        'department',
    ];
}
