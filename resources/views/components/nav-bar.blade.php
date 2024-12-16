<!-- Dropdown Administración -->
@php
    $hasAdminPermission =
        auth()
            ->user()
            ->can('viewAny', App\Models\Sustainer::class) ||
        auth()
            ->user()
            ->can('viewAny', App\Models\School::class) ||
        auth()
            ->user()
            ->can('viewAny', App\Models\User::class) ||
        auth()
            ->user()
            ->can('viewAny', App\Models\Role::class) ||
        auth()
            ->user()
            ->can('viewAny', App\Models\CostCenter::class) ||
        auth()
            ->user()
            ->can('viewAny', App\Models\History::class);
@endphp
@if ($hasAdminPermission)
    <li class="nav-item dropdown @if (request()->is('sustainers*') ||
            request()->is('users*') ||
            request()->is('roles*') ||
            request()->is('schools*') ||
            request()->is('costcenters*') ||
            request()->is('historys*')) active @endif">
        <a class="nav-link dropdown-toggle" href="#" id="administrationDropdown" role="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <i class='bx bx-cog' style="font-size: 24px;"></i>
            </span>
            <span class="nav-link-title">
                {{ __('Administración') }}
            </span>
        </a>
        <ul class="dropdown-menu" aria-labelledby="administrationDropdown">
            @can('viewAny', App\Models\Sustainer::class)
                <li>
                    <a class="dropdown-item @if (request()->is('sustainers*')) active @endif"
                        href="{{ route('sustainers.index') }}">
                        <span class="nav-link-icon">
                            <i class='bx bxs-building-house' style="font-size: 20px;"></i>
                        </span>
                        {{ __('Sostenedores') }}
                    </a>
                </li>
            @endcan
            @can('viewAny', App\Models\School::class)
                <li>
                    <a class="dropdown-item @if (request()->is('schools*')) active @endif"
                        href="{{ route('schools.index') }}">
                        <span class="nav-link-icon">
                            <i class='bx bxs-school' style="font-size: 20px;"></i>
                        </span>
                        {{ __('Escuelas') }}
                    </a>
                </li>
            @endcan
            @can('viewAny', App\Models\User::class)
                <li>
                    <a class="dropdown-item @if (request()->is('users*')) active @endif"
                        href="{{ route('users.index') }}">
                        <span class="nav-link-icon">
                            <i class='bx bx-user' style="font-size: 20px;"></i>
                        </span>
                        {{ __('Usuarios') }}
                    </a>
                </li>
            @endcan
            @can('viewAny', App\Models\Role::class)
                <li>
                    <a class="dropdown-item @if (request()->is('roles*')) active @endif"
                        href="{{ route('roles.index') }}">
                        <span class="nav-link-icon">
                            <i class='bx bxs-user-detail' style="font-size: 20px;"></i>
                        </span>
                        {{ __('Perfiles') }}
                    </a>
                </li>
            @endcan
            @can('viewAny', App\Models\CostCenter::class)
                <li>
                    <a class="dropdown-item @if (request()->is('costcenters*')) active @endif"
                        href="{{ route('costcenters.index') }}">
                        <span class="nav-link-icon">
                            <i class='bx bx-calculator' style="font-size: 20px;"></i>
                        </span>
                        {{ __('C.Costos') }}
                    </a>
                </li>
            @endcan
            @can('viewAny', App\Models\History::class)
                <li>
                    <a class="dropdown-item @if (request()->is('historys')) active @endif"
                        onclick="window.open('{{ route('historys.index') }}', 'Historial', 'width=800,height=600');">
                        <span class="nav-link-icon">
                            <i class='bx bx-history' style="font-size: 20px;"></i>
                        </span>
                        {{ __('Historial') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endif

<!-- Dropdown Mantenedor -->
@php
    $hasLaborPermission =
        (auth()
            ->user()
            ->can('viewAny', App\Models\Worker::class) ||
            auth()
                ->user()
                ->can('viewAny', App\Models\Insurance::class) ||
            auth()
                ->user()
                ->can('viewAny', App\Models\FinancialIndicator::class) ||
            auth()
                ->user()
                ->can('viewAny', App\Models\Bonus::class) ||
            auth()
                ->user()
                ->can('viewAny', App\Models\License::class) ||
            auth()
                ->user()
                ->can('viewAny', App\Models\Absence::class)) &&
        App\Models\SchoolUser::where('user_id', auth()->user()->id)->exists();
@endphp
@if ($hasLaborPermission)
    <li class="nav-item dropdown @if (request()->is('insurances*') ||
            request()->is('workers*') ||
            request()->is('licenses*') ||
            request()->is('bonuses*') ||
            request()->is('financial-indicators*') ||
            request()->is('absences*')) active @endif">
        <a class="nav-link dropdown-toggle" href="#" id="laborDropdown" role="button" data-bs-toggle="dropdown"
            aria-expanded="false">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <i class='bx bxs-briefcase-alt-2' style="font-size: 20px;"></i>
            </span>
            <span class="nav-link-title">
                {{ __('Mantenedor') }}
            </span>
        </a>
        <ul class="dropdown-menu" aria-labelledby="laborDropdown">
            @can('viewAny', App\Models\Worker::class)
                <li>
                    <a class="dropdown-item @if (request()->is('workers*')) active @endif"
                        href="{{ route('workers.index') }}">
                        <span class="nav-link-icon">
                            <i class='bx bx-hard-hat' style="font-size: 20px;"></i>
                        </span>
                        {{ __('Trabajadores') }}
                    </a>
                </li>
            @endcan
            @can('viewAny', App\Models\Insurance::class)
                @foreach (App\Models\Insurance::TYPES as $type => $name)
                    @if ($type == 1)
                        <!-- Li para el tipo 1 (Afp) -->
                        @can('viewAnyAfp', App\Models\Insurance::class)
                            <li>
                                <a class="dropdown-item @if (request()->input('type') == $type) active @endif"
                                    href="{{ route('insurances.index', ['type' => $type]) }}">
                                    <span class="nav-link-icon">
                                        <i class='bx bxs-shield-minus' style="font-size: 20px;"></i>
                                    </span>
                                    {{ __('Seguro ' . $name) }}
                                </a>
                            </li>
                        @endcan
                    @elseif ($type == 2)
                        <!-- Li para el tipo 2 (Isapre) -->
                        @can('viewAnyIsapre', App\Models\Insurance::class)
                            <li>
                                <a class="dropdown-item @if (request()->input('type') == $type) active @endif"
                                    href="{{ route('insurances.index', ['type' => $type]) }}">
                                    <span class="nav-link-icon">
                                        <i class='bx bx-shield-minus' style="font-size: 20px;"></i>
                                    </span>
                                    {{ __('Seguro ' . $name) }}
                                </a>
                            </li>
                        @endcan
                    @else
                        <!-- Li para otros tipos -->
                        <li>
                            <a class="dropdown-item @if (request()->input('type') == $type) active @endif"
                                href="{{ route('insurances.index', ['type' => $type]) }}">
                                <span class="nav-link-icon">
                                    <i class='bx bx-shield-plus' style="font-size: 20px;"></i>
                                </span>
                                {{ __('Seguro ' . $name) }}
                            </a>
                        </li>
                    @endif
                @endforeach
            @endcan
            @can('viewAny', App\Models\FinancialIndicator::class)
                <li>
                    <a class="dropdown-item @if (request()->is('financial-indicators*')) active @endif"
                        href="{{ route('financial-indicators.index') }}">
                        <span class="nav-link-icon">
                            <i class='bx bx-chart' style="font-size: 20px;"></i>
                        </span>
                        {{ __('Indicadores Financieros') }}
                    </a>
                </li>
            @endcan
            @can('viewAny', App\Models\Bonus::class)
                <li>
                    <a class="dropdown-item @if (request()->is('bonuses*')) active @endif"
                        href="{{ route('bonuses.index') }}">
                        <span class="nav-link-icon">
                            <i class='bx bxs-tag' style="font-size: 20px;"></i>
                        </span>
                        {{ __('Bonos y Descuentos') }}
                    </a>
                </li>
            @endcan
            @can('viewAny', App\Models\License::class)
                <li>
                    <a class="dropdown-item @if (request()->is('licenses*')) active @endif"
                        href="{{ route('licenses.index') }}">
                        <span class="nav-link-icon">
                            <i class='bx bxs-heart' style="font-size: 20px;"></i>
                        </span>
                        {{ __('Licencias Médicas') }}
                    </a>
                </li>
            @endcan
            @can('viewAny', App\Models\Absence::class)
                <li>
                    <a class="dropdown-item @if (request()->is('absences*')) active @endif"
                        href="{{ route('absences.index') }}">
                        <span class="nav-link-icon">
                            <i class="bx bx-minus-circle" style="font-size: 20px;"></i>
                        </span>
                        {{ __('Inasistencias') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endif

<!-- Dropdown Remuneraciones -->
@php
    $hasRemuneracionesPermission =
        (auth()
            ->user()
            ->can('viewAny', App\Models\Template::class) ||
            auth()
                ->user()
                ->can('viewAny', App\Models\Liquidation::class)) &&
        App\Models\SchoolUser::where('user_id', auth()->user()->id)->exists();
@endphp
@if ($hasRemuneracionesPermission)
    <li class="nav-item dropdown @if (request()->is('templates*') || request()->is('liquidations*')) active @endif">
        <a class="nav-link dropdown-toggle" href="#" id="remuneracionesDropdown" role="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <i class="bx bxs-wallet" style="font-size: 24px;"></i>
            </span>
            <span class="nav-link-title">
                {{ __('Remuneraciones') }}
            </span>
        </a>
        <ul class="dropdown-menu" aria-labelledby="remuneracionesDropdown">
            @can('viewAny', App\Models\Template::class)
                <li>
                    <a class="dropdown-item @if (request()->is('templates*')) active @endif"
                        href="{{ route('templates.index') }}">
                        <span class="nav-link-icon">
                            <i class='bx bx-money' style="font-size: 20px;"></i>
                        </span>
                        {{ __("Item's Liquidaciones") }}
                    </a>
                </li>
            @endcan
            @can('viewAny', App\Models\Liquidation::class)
                <li>
                    <a class="dropdown-item @if (request()->is('liquidations*')) active @endif"
                        href="{{ route('liquidations.index') }}">
                        <span class="nav-link-icon">
                            <i class='bx bx-money-withdraw' style="font-size: 20px;"></i>
                        </span>
                        {{ __('Emitir Liquidaciones') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endif
<!-- Dropdown Consultas -->
@php
    $hasConsultasPermission =
        (auth()
            ->user()
            ->can('viewAny', App\Models\Report::class) ||
            auth()
                ->user()
                ->can('viewAny', App\Models\Payroll::class) ||
            auth()
                ->user()
                ->can('viewAny', App\Models\Certificate::class)) &&
        App\Models\SchoolUser::where('user_id', auth()->user()->id)->exists();
@endphp
@if ($hasConsultasPermission)
    <li class="nav-item dropdown @if (request()->is('reports*') || request()->is('payrolls*') || request()->is('certificates*')) active @endif">
        <a class="nav-link dropdown-toggle" href="#" id="consultasDropdown" role="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <i class="bx bx-file" style="font-size: 24px;"></i>
            </span>
            <span class="nav-link-title">
                {{ __('Consultas') }}
            </span>
        </a>
        <ul class="dropdown-menu" aria-labelledby="consultasDropdown">
            @can('viewAny', App\Models\Report::class)
                <li>
                    <a class="dropdown-item @if (request()->is('reports*')) active @endif"
                        href="{{ route('reports.index') }}">
                        <span class="nav-link-icon">
                            <i class='bx bx-folder' style="font-size: 20px;"></i>
                        </span>
                        {{ __('Informes previsionales') }}
                    </a>
                </li>
            @endcan
            @can('viewAny', App\Models\Payroll::class)
                <li>
                    <a class="dropdown-item @if (request()->is('payrolls*')) active @endif"
                        href="{{ route('payrolls.index') }}">
                        <span class="nav-link-icon">
                            <i class='bx bx-file' style="font-size: 20px;"></i>
                        </span>
                        {{ __('Planilla de Remuneraciones') }}
                    </a>
                </li>
            @endcan
            @can('viewAny', App\Models\Certificate::class)
                <li>
                    <a class="dropdown-item @if (request()->is('certificates*')) active @endif"
                        href="{{ route('certificates.index') }}">
                        <span class="nav-link-icon">
                            <i class='bx bxs-certification' style="font-size: 20px;"></i>
                        </span>
                        {{ __('Certificado de Remuneraciones') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endif
