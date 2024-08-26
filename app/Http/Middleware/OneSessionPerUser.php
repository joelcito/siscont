<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class OneSessionPerUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check()) {
            $currentSessionId = Session::getId();
            $user = Auth::user();
            $lastSessionId = $user->session_id;

            if ($lastSessionId && $lastSessionId !== $currentSessionId) {
                // Invalida la sesión anterior
                \DB::table('sessions')->where('id', $lastSessionId)->delete();
            }

            // Actualiza el session_id del usuario
            $user->session_id = $currentSessionId;
            $user->save();
        }

        return $next($request);

        // return $next($request);
    }
}
