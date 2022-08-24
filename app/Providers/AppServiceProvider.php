<?php

namespace App\Providers;

use App\Jwt\JwtApp;
use App\Jwt\JwtUser;
use Hidehalo\Nanoid\Client;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function () {
            return new Client();
        });
        $this->app->singleton(JwtApp::class, function (Application $app) {
            return new JwtApp($app->make(Client::class));
        });
        $this->app->singleton(JwtUser::class, function (Application $app) {
            return new JwtUser($app->make(Client::class), $app->make(JwtApp::class));
        });
    }
}
