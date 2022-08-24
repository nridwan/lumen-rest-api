<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class ApiApp extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'alias', 'appkey'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'appkey',
    ];

    public static function login($alias, $password, $remember, $web = true)
    {
        $user = self::where('alias', $alias)->first();
        $result = [
            'message' => null,
            'data' => null
        ];
        if ($user && Hash::check($password, $user->appkey)) {
            if (!$web) {
                $result['message'] = 'Success';
                $result['data'] = $user;
                return $result;
            }
        } else {
            $result['message'] = 'Failed';
            return $result;
        }
    }
}
