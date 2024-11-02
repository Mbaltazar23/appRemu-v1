<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckSchoolSession
{
    public function handle(Request $request, Closure $next)
    {
        $authUser = User::find(auth()->user()->id);

        if (auth()->check() && $authUser->isContador() && !auth()->user()->school_id_session) {
            return redirect()->route('schoolSelect');
        }

        return $next($request);
    }
}
