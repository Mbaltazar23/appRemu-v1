<div class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar navbar-light">
            <div class="container-xl">
                <ul class="navbar-nav">
                    <li class="school-name text-center my-3 p-0 fw-bold">
                        {{ auth()->user()->name }}
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
                    <x-nav-bar />

                    @can('viewSchools', App\Models\SchoolUser::class)
                        <li class="nav-item @if (request()->is('select-school')) active @endif">
                            <a class="nav-link" href="{{ route('schoolSelect') }}">
                                <span class="nav-link-icon">
                                    <i class='bx bxs-school' style="font-size: 20px;"></i>
                                </span>
                                {{ __('Cambiar Colegio') }}
                            </a>
                        </li>
                    @endcan
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
