<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'user_id', 'hash', 'expired_at'
    ];
}
