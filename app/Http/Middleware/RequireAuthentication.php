<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAuthentication
{
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->session()->get('user_id');

        $user = $userId ? User::find($userId) : null;

        if (!$user) {
            $request->session()->flush();

            return redirect('/login');
        }

        view()->share('currentUser', $user);

        return $next($request);
    }
}
