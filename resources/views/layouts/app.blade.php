<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs"></script>
    </body>
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite('resources/sass/app.scss')

    <!-- Custom styles for this Page-->
    @stack('custom_styles')
</head>

<body class="theme-light">
    <div class="page">
        <div class="sticky-top">
            <header class="navbar navbar-expand-md navbar-light sticky-top d-print-none">
                <div class="container-xl">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbar-menu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                        <a href="{{ route('home') }}">
                            @if (auth()->check() && auth()->user()->isContador())
                                @php
                                    $school = App\Models\School::find(auth()->user()->school_id_session);
                                @endphp
                                @if ($school)
                                    {{ auth()->user()->getRoleNameAttribute() .' - '.$school->name }}
                                @else
                                    {{ config('app.name') }}
                                @endif
                            @else
                                {{ config('app.name') }}
                            @endif
                        </a>

                    </h1>
                    <div class="navbar-nav flex-row order-md-last">

                        @auth
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                                    aria-label="Open user menu">
                                    <span class="avatar avatar-sm"
                                        style="background-image: url(https://eu.ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }})"></span>
                                    <div class="d-none d-xl-block ps-2">
                                        {{ auth()->user()->name ?? null }}
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <a href="{{ route('profile.show') }}" class="dropdown-item">{{ __('Profile') }}</a>
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a href="{{ route('logout') }}" class="dropdown-item"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </a>
                                    </form>
                                </div>
                            </div>
                        @endauth

                    </div>
                </div>
            </header>

            @include('layouts.navigation')

        </div>
        <div class="page-wrapper">


            @if (session('success'))
                <div class="container-xl mt-2">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="container-xl mt-2">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @yield('content')

        </div>
    </div>
    </div>

    <!-- Core plugin JavaScript-->
    @vite('resources/js/app.js')

    <!-- Page level custom scripts -->
    @stack('custom_scripts')

    <script src="https://cdn.jsdelivr.net/npm/alpinejs"></script>

</html>
<style>
    @media screen and (max-width: 767px) {
        .name-school {
            display: none;
        }
    }
</style>
