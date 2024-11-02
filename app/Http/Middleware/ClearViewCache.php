<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Artisan;

class ClearViewCache
{

    public function handle($request, Closure $next)
    {
        // Clear the view cache
        Artisan::call('view:clear');
        return $next($request);
    }

}
