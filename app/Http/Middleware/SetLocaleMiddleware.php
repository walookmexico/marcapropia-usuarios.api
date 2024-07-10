<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocaleMiddleware{
    public function handle($request, Closure $next){
        if ($locale = $request->header('X-Locale')) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}