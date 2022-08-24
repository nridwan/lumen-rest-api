<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiAppToken extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'app_id', 'hash', 'expired_at'
    ];
}
