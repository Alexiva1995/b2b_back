<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Learning extends Model
{
    use HasFactory;
    const DOCUMENT = 0;
    const VIDEO = 1;
    const LINK = 2;
    protected $fillable = [
        'title',
        'description',
        'file_name',
        'path',
        'type'
    ];

    public function category()
    {
        return $this->belongsTo(CategoryLearning::class);
    }
}

