<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SetSchoolIdSessionNullOnHomeLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si la ruta es home o login
        if ($request->is('home') || $request->is('login')) {
            // Verificar si ya se ejecutó la actualización en estas rutas
            if (!Cache::has('school_id_session_null_set')) {
                // Si no se ha ejecutado, actualiza todos los usuarios
                DB::table('users')->update(['school_id_session' => null]);

                // Marcar en el cache que la acción ya se ejecutó
                Cache::put('school_id_session_null_set', true, now()->addDay()); // Marca por 1 día
            }
        }
        return $next($request);
    }
}
