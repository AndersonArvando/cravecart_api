<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Kantin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $auth_key = $request->auth_key;
        $user = User::where('type', 2)->where('auth_key', $auth_key)->first();
        if($user) {
            return $next($request);
        } else {
            return response()->json(['error' => 'Authenticated is required!'], 403);
        }
    }
}
