<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(){
        Schema::defaultStringLength(191);

        // Configura el formato de fecha global para toda la aplicación
        DB::connection()->getPdo()->exec("SET LANGUAGE us_english");
    }

    public function boot(){
        if(env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
    }
}
