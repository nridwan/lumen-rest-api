<?php

namespace App\Http\Controllers;

use App\Jwt\JwtApp;
use App\Jwt\JwtUser;
use App\Models\ApiApp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAppController extends Controller
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

    public function auth(Request $request, JwtApp $jwtApp) {
        $result = ApiApp::login($request->input('app'), $request->input('key') ?? "", false, false);
        if ($result['data']) {
            return buildJson(200, $result['message'], $jwtApp->generate($result['data']));
        }
        return buildErrorJson(400, $result['message']);
    }

    public function refresh(JwtApp $jwtApp) {
        return buildJson(200, 'Success', $jwtApp->refresh());
    }

    public function logout(JwtApp $jwtApp) {
        $jwtApp->logout();
        return buildJson(200, 'Success');
    }
}
