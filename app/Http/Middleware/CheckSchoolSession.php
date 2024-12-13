<?php

namespace App\Http\Middleware;

use App\Models\SchoolUser;
use Closure;
use Illuminate\Http\Request;

class CheckSchoolSession
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            // Obtén el registro del usuario en el modelo SchoolUser
            $authUser = SchoolUser::where('user_id', auth()->user()->id)->first();
            // Verifica que el registro exista y que el campo school_id_session esté vacío
            if ($authUser && empty($authUser->user->school_id_session)) {
                return redirect()->route('schoolSelect');
            }
        }
        return $next($request);
    }
}
