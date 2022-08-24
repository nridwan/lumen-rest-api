<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public static function login($email, $password, $remember, $web = true)
    {
        $user = self::where('email', $email)->first();
        $result = [
            'message' => null,
            'data' => null
        ];
        if ($user && Hash::check($password, $user->getAuthPassword())) {
            if (!$web) {
                $result['message'] = 'Login Success';
                $result['data'] = $user;
                return $result;
            }
        } else {
            $result['message'] = 'Wrong email/password';
            return $result;
        }
    }
}
