<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Absence;
use App\Models\Certificate;
use App\Models\CostCenter;
use App\Models\FinancialIndicator;
use App\Models\History;
use App\Models\Insurance;
use App\Models\Liquidation;
use App\Models\Payroll;
use App\Models\Report;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolUser;
use App\Models\Sustainer;
use App\Models\Template;
use App\Models\User;
use App\Models\Worker;
use App\Policies\AbsencePolicy;
use App\Policies\CertificatePolicy;
use App\Policies\CostCenterPolicy;
use App\Policies\FinancialIndicatorPolicy;
use App\Policies\HistoryPolicy;
use App\Policies\InsurancePolicy;
use App\Policies\LiquidationPolicy;
use App\Policies\PayrollPolicy;
use App\Policies\ReportPolicy;
use App\Policies\RolePolicy;
use App\Policies\SchoolPolicy;
use App\Policies\SustainerPolicy;
use App\Policies\TemplatePolicy;
use App\Policies\UserPolicy;
use App\Policies\WorkerPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Pagination\Paginator;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        School::class => SchoolPolicy::class,
        SchoolUser::class => SchoolPolicy::class,
        Sustainer::class => SustainerPolicy::class,
        Insurance::class => InsurancePolicy::class,
        Worker::class => WorkerPolicy::class,
        FinancialIndicator::class => FinancialIndicatorPolicy::class,
        Absence::class => AbsencePolicy::class,
        Template::class => TemplatePolicy::class,
        Liquidation::class => LiquidationPolicy::class,
        Report::class => ReportPolicy::class,
        Payroll::class => PayrollPolicy::class,
        Certificate::class => CertificatePolicy::class,
        CostCenter::class => CostCenterPolicy::class,
        History::class => HistoryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Paginator::useBootstrap(); // Cambia según tu configuración
    }
}
