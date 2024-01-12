<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MassMessage extends Model
{
    use HasFactory;

    protected $fillable = ['message', 'title'];

    public function userRead()
    {
        return $this->belongsToMany(User::class, 'message_read_user')->withPivot('is_read');
    }
    public function userUnread()
    {
        return $this->belongsToMany(User::class, 'message_read_user')->wherePivot('is_read', 1);
    }
}
