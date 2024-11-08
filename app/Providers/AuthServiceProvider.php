<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Absence;
use App\Models\FinancialIndicator;
use App\Models\Insurance;
use App\Models\School;
use App\Models\Sustainer;
use App\Models\Template;
use App\Models\User;
use App\Models\Worker;
use App\Policies\AbsencePolicy;
use App\Policies\FinancialIndicatorPolicy;
use App\Policies\InsurancePolicy;
use App\Policies\SchoolPolicy;
use App\Policies\SustainerPolicy;
use App\Policies\TemplatePolicy;
use App\Policies\UserPolicy;
use App\Policies\WorkerPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        School::class => SchoolPolicy::class,
        Sustainer::class => SustainerPolicy::class,
        Insurance::class => InsurancePolicy::class,
        Worker::class => WorkerPolicy::class,
        FinancialIndicator::class => FinancialIndicatorPolicy::class,
        Absence::class => AbsencePolicy::class,
        Template::class => TemplatePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        \Illuminate\Pagination\Paginator::useBootstrap(); // Cambia según tu configuración
    }
}
