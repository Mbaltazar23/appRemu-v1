<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSchoolUserAssociation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            // Lista de modelos con los que el usuario puede tener acceso
            $models = [
                \App\Models\Worker::class,
                \App\Models\Insurance::class,
                \App\Models\FinancialIndicator::class,
                \App\Models\Bonus::class,
                \App\Models\License::class,
                \App\Models\Absence::class,
                \App\Models\Template::class,
                \App\Models\Liquidation::class,
                \App\Models\Report::class,
                \App\Models\Payroll::class,
                \App\Models\Certificate::class,
            ];
            // Verificar si el usuario tiene acceso a alguno de los modelos
            $hasAccess = collect($models)->contains(function ($model) {
                return auth()->user()->can('viewAny', $model);
            });
            // Verificar si el usuario no está asociado a un SchoolUser
            $isNotAssociated = ! \App\Models\SchoolUser::where('user_id', auth()->user()->id)->exists();
            // Si tiene acceso y no está asociado, establece un mensaje de advertencia
            if ($hasAccess && $isNotAssociated) {
                session()->flash('warning', 'Al no tener asociado ningun Colegio no tiene acceso a los modulos a los que tiene permiso !!');
            }
        }

        return $next($request);
    }
}
