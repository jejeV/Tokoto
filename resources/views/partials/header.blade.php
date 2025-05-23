<header class="wrapper bg-gray">
    <nav class="navbar navbar-expand-lg extended extended-alt navbar-light navbar-bg-light">
        <div class="container flex-lg-column">
            <div class="topbar d-flex flex-row justify-content-lg-center align-items-center"></div>
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
                        <h3 class="text-white fs-30 mb-0"></h3>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body d-flex flex-column h-100">
                        <ul class="navbar-nav">
                            <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">Product</a></li>
                            <li class="nav-item"><a class="nav-link" href="/category">Category</a></li>
                            <li class="nav-item"><a class="nav-link" href="/shop">Shop</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('checkout.index') }}">Checkout</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}">Cart</a></li>
                        </ul>
                        <div class="offcanvas-footer d-lg-none">
                            <div>
                                <a href="mailto:first.last@email.com" class="link-inverse">info@email.com</a>
                                <br /> 00 (123) 456 78 90 <br />
                                <nav class="nav social social-white mt-4">
                                    <a href="#"><i class="uil uil-facebook-f"></i></a>
                                    <a href="#"><i class="uil uil-instagram"></i></a>
                                    <a href="#"><i class="uil uil-youtube"></i></a>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="navbar-other w-100 d-flex">
                    <ul class="navbar-nav flex-row align-items-center ms-auto">
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvas-info"><i class="uil uil-info-circle"></i></a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvas-search"><i class="uil uil-search"></i></a></li>
                        <li class="nav-item d-lg-none">
                            <button class="hamburger offcanvas-nav-btn"><span></span></button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    @include('partials.offcanvas')
</header>
