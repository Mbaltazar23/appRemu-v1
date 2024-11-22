<div class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar navbar-light">
            <div class="container-xl">
                <ul class="navbar-nav">
                    <li class="school-name text-center my-3 p-0 fw-bold">
                        @if (!auth()->user()->isAdmin())
                            {{ auth()->user()->name }}
                        @endif
                    </li>
                    <li class="nav-item @if (request()->routeIs('home')) active @endif">
                        <a class="nav-link" href="{{ route('home') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class='bx bx-home' style="font-size: 24px;"></i>
                            </span>
                            <span class="nav-link-title">
                                {{ __('Dashboard') }}
                            </span>
                        </a>
                    </li>
                    @if (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                        <li class="nav-item dropdown @if (request()->is('sustainers*') || request()->is('users*') || request()->is('schools*')) active @endif">
                            <a class="nav-link dropdown-toggle" href="#" id="administrationDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                                            {{ __('Schools') }}
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
                                            {{ __('Users') }}
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endif

                    @if (auth()->user()->isContador())
                        <li class="nav-item dropdown @if (request()->is('insurances*') ||
                                request()->is('workers*') ||
                                request()->is('licenses*') ||
                                request()->is('bonuses*') ||
                                request()->is('financial-indicators*') ||
                                request()->is('absences')) active @endif">
                            <a class="nav-link dropdown-toggle" href="#" id="laborDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
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
                                        <li>
                                            <a class="dropdown-item @if (request()->input('type') == $type) active @endif"
                                                href="{{ route('insurances.index', ['type' => $type]) }}">
                                                <span class="nav-link-icon">
                                                    @if ($type == 1)
                                                        <i class='bx bxs-shield-minus' style="font-size: 20px;"></i>
                                                        <!-- AFP -->
                                                    @elseif ($type == 2)
                                                        <i class='bx bx-shield-minus' style="font-size: 20px;"></i>
                                                        <!-- Salud -->
                                                    @else
                                                        <i class='bx bx-shield-plus' style="font-size: 20px;"></i>
                                                        <!-- Fonasa -->
                                                    @endif
                                                </span>
                                                {{ __('Seguro ' . $name) }}
                                            </a>
                                        </li>
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
                                            {{ __('Licencias Medicas') }}
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
                        <li class="nav-item dropdown @if (request()->is('templates*') ||
                                request()->is('liquidations*')) active @endif">
                            <a class="nav-link dropdown-toggle" href="#" id="administrationDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="bx bxs-wallet" style="font-size: 24px;"></i>
                                </span>
                                <span class="nav-link-title">
                                    {{ __('Remuneraciones') }}
                                </span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="administrationDropdown">
                                @can('viewAny', App\Models\Template::class)
                                    <li>
                                        <a class="dropdown-item @if (request()->is('templates*')) active @endif"
                                            href="{{ route('templates.index') }}">
                                            <span class="nav-link-icon">
                                                <i class='bx bx-money' style="font-size: 20px;"></i>
                                            </span>
                                            {{ __('Liquidaciones') }}
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
                        <li class="nav-item @if (request()->is('select-school')) active @endif">
                            <a class="nav-link" href="{{ route('schoolSelect') }}">
                                <span class="nav-link-icon">
                                    <i class='bx bxs-school' style="font-size: 20px;"></i>
                                </span>
                                {{ __('Cambiar Colegio') }}
                            </a>
                        </li>
                    @endif

                </ul>
            </div>
        </div>
    </div>
</div>
<style>
    @media screen and (min-width: 768px) {
        .school-name {
            display: none;
        }
    }
</style>
