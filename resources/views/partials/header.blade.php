<header class="wrapper bg-gray">
    <nav class="navbar navbar-expand-lg extended extended-alt navbar-light navbar-bg-light">
        <div class="container flex-lg-column">
            <div class="topbar d-flex flex-row justify-content-lg-center align-items-center">
                <div class="navbar-brand">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('assets/home/img/nike/logo-dark.png') }}"
                            srcset="{{ asset('assets/home/img/nike/logo-dark@2x.png') }} 2x" alt="" />
                    </a>
                </div>
            </div>
            <div class="navbar-collapse-wrapper bg-white d-flex flex-row align-items-center justify-content-between">
                <div class="navbar-other w-100 d-none d-lg-block">
                    <nav class="nav social social-muted">
                        <a href="#"><i class="uil uil-twitter"></i></a>
                        <a href="#"><i class="uil uil-facebook-f"></i></a>
                        <a href="#"><i class="uil uil-instagram"></i></a>
                    </nav>
                </div>
                <div class="navbar-collapse offcanvas offcanvas-nav offcanvas-start">
                    <div class="offcanvas-header d-lg-none">
                        <h3 class="text-white fs-30 mb-0">Shoebaru</h3>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body d-flex flex-column h-100">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link scroll" href="#services">Shop</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('collections') ? 'active' : '' }}" href="{{ route('collections') }}">Collections</a>
                            {{-- </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
                            </li> --}}
                        </ul>
                    </div>
                </div>
                <div class="navbar-other w-100 d-flex">
                    <ul class="navbar-nav flex-row align-items-center ms-auto">
                        @guest
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}" title="Login">
                                <i class="uil uil-signin"></i>
                            </a>
                        </li>
                        @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdownUser" class="nav-link dropdown-toggle {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="uil uil-user-circle"></i> <span
                                    class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                                <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                    Dashboard
                                </a>
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                document.getElementById('logout-form-other').submit();">
                                    Logout
                                </a>

                                <form id="logout-form-other" action="{{ route('logout') }}" method="POST"
                                    class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                        @endguest
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('cart.index') ? 'active' : '' }}" href="{{ route('cart.index') }}">
                                <i class="uil uil-shopping-cart"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>
