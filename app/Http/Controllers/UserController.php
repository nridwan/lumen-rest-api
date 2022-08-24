<?php

namespace App\Http\Controllers;

use App\Jwt\JwtApp;
use App\Jwt\JwtUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function login(Request $request, JwtUser $jwtUser) {
        $result = User::login($request->input('email'), $request->input('password') ?? "", false, false);
        if ($result['data']) {
            return buildJson(200, $result['message'], $jwtUser->generate($result['data']));
        }
        return buildErrorJson(400, $result['message']);
    }

    public function refresh(JwtUser $jwtUser) {
        return buildJson(200, 'Success', $jwtUser->refresh());
    }

    public function logout(JwtUser $jwtUser) {
        $jwtUser->logout();
        return buildJson(200, 'Success');
    }

    public function profile(JwtApp $jwtApp) {
        return buildJson(200, 'Success', $jwtApp->getApiApp());
    }
}
