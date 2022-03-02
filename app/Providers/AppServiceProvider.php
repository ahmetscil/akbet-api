<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use App\Helpers\Hermes;
use App\Helpers\Pariette;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        RateLimiter::for('publicApi', function (Request $request) {
            return Limit::perMinute(10000)->response(function() {
                Hermes::discord('OHA! THROTTLE', 'dakikada 10.000 üzeri api isteği algılandı.', 'istek atan ip:' . Pariette::getIp() );
                return new Response('Beep! Beep! Too many attempts');
            });
        });
    }
}
