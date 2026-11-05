<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('languages.supported', []));
        $locale = session('locale', config('languages.default', 'en'));

        if (!in_array($locale, $supported, true)) {
            $locale = config('languages.default', 'en');
        }

        App::setLocale($locale);

        return $next($request);
    }
}

