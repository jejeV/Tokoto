@extends('layouts.app') {{-- Menggunakan layout utama --}}

@section('content')

    <body>
        <div class="content-wrapper">
            <header class="wrapper bg-light">
                <nav class="navbar navbar-expand-lg center-nav navbar-light navbar-bg-light">
                    <div class="container flex-lg-row flex-nowrap align-items-center">
                        <div class="navbar-brand w-100">
                            <a href="./index.html">
                                <img src="./assets/img/logo.png" srcset="./assets/img/logo@2x.png 2x" alt="" />
                            </a>
                        </div>
                        <div class="navbar-collapse offcanvas offcanvas-nav offcanvas-start">
                            <div class="offcanvas-header d-lg-none">
                                <h3 class="text-white fs-30 mb-0">Sandbox</h3>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                                    aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body ms-lg-auto d-flex flex-column h-100">
                                <ul class="navbar-nav">
                                    <li class="nav-item dropdown dropdown-mega">
                                        <a class="nav-link dropdown-toggle" href="#"
                                            data-bs-toggle="dropdown">Demos</a>
                                        <ul class="dropdown-menu mega-menu mega-menu-dark mega-menu-img">
                                            <li class="mega-menu-content mega-menu-scroll">
                                                <ul class="row row-cols-1 row-cols-lg-6 gx-0 gx-lg-4 gy-lg-2 list-unstyled">
                                                    <li class="col"><a class="dropdown-item" href="./demo1.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi1.jpg"
                                                                    srcset="./assets/img/demos/mi1@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo I</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo2.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi2.jpg"
                                                                    srcset="./assets/img/demos/mi2@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo II</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo3.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi3.jpg"
                                                                    srcset="./assets/img/demos/mi3@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo III</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo4.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi4.jpg"
                                                                    srcset="./assets/img/demos/mi4@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo IV</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo5.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi5.jpg"
                                                                    srcset="./assets/img/demos/mi5@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo V</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo6.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi6.jpg"
                                                                    srcset="./assets/img/demos/mi6@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo VI</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo7.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi7.jpg"
                                                                    srcset="./assets/img/demos/mi7@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo VII</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo8.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi8.jpg"
                                                                    srcset="./assets/img/demos/mi8@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo VIII</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo9.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi9.jpg"
                                                                    srcset="./assets/img/demos/mi9@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo IX</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo10.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi10.jpg"
                                                                    srcset="./assets/img/demos/mi10@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo X</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo11.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi11.jpg"
                                                                    srcset="./assets/img/demos/mi11@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo XI</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo12.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi12.jpg"
                                                                    srcset="./assets/img/demos/mi12@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo XII</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo13.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi13.jpg"
                                                                    srcset="./assets/img/demos/mi13@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo XIII</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo14.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi14.jpg"
                                                                    srcset="./assets/img/demos/mi14@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo XIV</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo15.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi15.jpg"
                                                                    srcset="./assets/img/demos/mi15@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo XV</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo16.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi16.jpg"
                                                                    srcset="./assets/img/demos/mi16@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo XVI</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo17.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi17.jpg"
                                                                    srcset="./assets/img/demos/mi17@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo XVII</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo18.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi18.jpg"
                                                                    srcset="./assets/img/demos/mi18@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo XVIII</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo19.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi19.jpg"
                                                                    srcset="./assets/img/demos/mi19@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo XIX</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo20.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi20.jpg"
                                                                    srcset="./assets/img/demos/mi20@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo XX</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo21.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi21.jpg"
                                                                    srcset="./assets/img/demos/mi21@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo XXI</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo22.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi22.jpg"
                                                                    srcset="./assets/img/demos/mi22@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo XXII</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo23.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi23.jpg"
                                                                    srcset="./assets/img/demos/mi23@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo XXIII</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo24.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi24.jpg"
                                                                    srcset="./assets/img/demos/mi24@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo XXIV</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo25.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi25.jpg"
                                                                    srcset="./assets/img/demos/mi25@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo XXV</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo26.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi26.jpg"
                                                                    srcset="./assets/img/demos/mi26@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo XXVI</span>
                                                        </a></li>
                                                    <li class="col"><a class="dropdown-item" href="./demo27.html">
                                                            <figure class="rounded lift d-none d-lg-block"><img
                                                                    src="./assets/img/demos/mi27.jpg"
                                                                    srcset="./assets/img/demos/mi27@2x.jpg 2x"
                                                                    alt=""></figure>
                                                            <span class="d-lg-none">Demo XXVII</span>
                                                        </a></li>
                                                </ul>
                                                <!--/.row -->
                                                <span class="d-none d-lg-flex"><i
                                                        class="uil uil-direction"></i><strong>Scroll to view
                                                        more</strong></span>
                                            </li>
                                            <!--/.mega-menu-content-->
                                        </ul>
                                        <!--/.dropdown-menu -->
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#"
                                            data-bs-toggle="dropdown">Pages</a>
                                        <ul class="dropdown-menu">
                                            <li class="dropdown dropdown-submenu dropend"><a
                                                    class="dropdown-item dropdown-toggle" href="#"
                                                    data-bs-toggle="dropdown">Services</a>
                                                <ul class="dropdown-menu">
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./services.html">Services I</a></li>
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./services2.html">Services II</a></li>
                                                </ul>
                                            </li>
                                            <li class="dropdown dropdown-submenu dropend"><a
                                                    class="dropdown-item dropdown-toggle" href="#"
                                                    data-bs-toggle="dropdown">About</a>
                                                <ul class="dropdown-menu">
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./about.html">About I</a></li>
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./about2.html">About II</a></li>
                                                </ul>
                                            </li>
                                            <li class="dropdown dropdown-submenu dropend"><a
                                                    class="dropdown-item dropdown-toggle" href="#"
                                                    data-bs-toggle="dropdown">Shop</a>
                                                <ul class="dropdown-menu">
                                                    <li class="nav-item"><a class="dropdown-item" href="./shop.html">Shop
                                                            I</a></li>
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./shop2.html">Shop II</a></li>
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./shop-product.html">Product Page</a></li>
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./shop-cart.html">Shopping Cart</a></li>
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./shop-checkout.html">Checkout</a></li>
                                                </ul>
                                            </li>
                                            <li class="dropdown dropdown-submenu dropend"><a
                                                    class="dropdown-item dropdown-toggle" href="#"
                                                    data-bs-toggle="dropdown">Contact</a>
                                                <ul class="dropdown-menu">
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./contact.html">Contact I</a></li>
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./contact2.html">Contact II</a></li>
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./contact3.html">Contact III</a></li>
                                                </ul>
                                            </li>
                                            <li class="dropdown dropdown-submenu dropend"><a
                                                    class="dropdown-item dropdown-toggle" href="#"
                                                    data-bs-toggle="dropdown">Career</a>
                                                <ul class="dropdown-menu">
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./career.html">Job Listing I</a></li>
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./career2.html">Job Listing II</a></li>
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./career-job.html">Job Description</a></li>
                                                </ul>
                                            </li>
                                            <li class="dropdown dropdown-submenu dropend"><a
                                                    class="dropdown-item dropdown-toggle" href="#"
                                                    data-bs-toggle="dropdown">Utility</a>
                                                <ul class="dropdown-menu">
                                                    <li class="nav-item"><a class="dropdown-item" href="./404.html">404
                                                            Not Found</a></li>
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./page-loader.html">Page Loader</a></li>
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./signin.html">Sign In I</a></li>
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./signin2.html">Sign In II</a></li>
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./signup.html">Sign Up I</a></li>
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./signup2.html">Sign Up II</a></li>
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./terms.html">Terms</a></li>
                                                </ul>
                                            </li>
                                            <li class="nav-item"><a class="dropdown-item"
                                                    href="./pricing.html">Pricing</a></li>
                                            <li class="nav-item"><a class="dropdown-item" href="./onepage.html">One
                                                    Page</a></li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#"
                                            data-bs-toggle="dropdown">Projects</a>
                                        <div class="dropdown-menu dropdown-lg">
                                            <div class="dropdown-lg-content">
                                                <div>
                                                    <h6 class="dropdown-header">Project Pages</h6>
                                                    <ul class="list-unstyled">
                                                        <li><a class="dropdown-item" href="./projects.html">Projects I</a>
                                                        </li>
                                                        <li><a class="dropdown-item" href="./projects2.html">Projects
                                                                II</a></li>
                                                        <li><a class="dropdown-item" href="./projects3.html">Projects
                                                                III</a></li>
                                                        <li><a class="dropdown-item" href="./projects4.html">Projects
                                                                IV</a></li>
                                                    </ul>
                                                </div>
                                                <!-- /.column -->
                                                <div>
                                                    <h6 class="dropdown-header">Single Projects</h6>
                                                    <ul class="list-unstyled">
                                                        <li><a class="dropdown-item" href="./single-project.html">Single
                                                                Project I</a></li>
                                                        <li><a class="dropdown-item" href="./single-project2.html">Single
                                                                Project II</a></li>
                                                        <li><a class="dropdown-item" href="./single-project3.html">Single
                                                                Project III</a></li>
                                                        <li><a class="dropdown-item" href="./single-project4.html">Single
                                                                Project IV</a></li>
                                                    </ul>
                                                </div>
                                                <!-- /.column -->
                                            </div>
                                            <!-- /auto-column -->
                                        </div>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#"
                                            data-bs-toggle="dropdown">Blog</a>
                                        <ul class="dropdown-menu">
                                            <li class="nav-item"><a class="dropdown-item" href="./blog.html">Blog without
                                                    Sidebar</a></li>
                                            <li class="nav-item"><a class="dropdown-item" href="./blog2.html">Blog with
                                                    Sidebar</a></li>
                                            <li class="nav-item"><a class="dropdown-item" href="./blog3.html">Blog with
                                                    Left Sidebar</a></li>
                                            <li class="dropdown dropdown-submenu dropend"><a
                                                    class="dropdown-item dropdown-toggle" href="#"
                                                    data-bs-toggle="dropdown">Blog Posts</a>
                                                <ul class="dropdown-menu">
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./blog-post.html">Post without Sidebar</a></li>
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./blog-post2.html">Post with Sidebar</a></li>
                                                    <li class="nav-item"><a class="dropdown-item"
                                                            href="./blog-post3.html">Post with Left Sidebar</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown dropdown-mega">
                                        <a class="nav-link dropdown-toggle" href="#"
                                            data-bs-toggle="dropdown">Blocks</a>
                                        <ul class="dropdown-menu mega-menu mega-menu-dark mega-menu-img">
                                            <li class="mega-menu-content">
                                                <ul
                                                    class="row row-cols-1 row-cols-lg-6 gx-0 gx-lg-6 gy-lg-4 list-unstyled">
                                                    <li class="col"><a class="dropdown-item"
                                                            href="./docs/blocks/about.html">
                                                            <div class="rounded img-svg d-none d-lg-block p-4 mb-lg-2"><img
                                                                    class="rounded-0" src="./assets/img/demos/block1.svg"
                                                                    alt=""></div>
                                                            <span>About</span>
                                                        </a>
                                                    </li>
                                                    <li class="col"><a class="dropdown-item"
                                                            href="./docs/blocks/blog.html">
                                                            <div class="rounded img-svg d-none d-lg-block p-4 mb-lg-2"><img
                                                                    class="rounded-0" src="./assets/img/demos/block2.svg"
                                                                    alt=""></div>
                                                            <span>Blog</span>
                                                        </a>
                                                    </li>
                                                    <li class="col"><a class="dropdown-item"
                                                            href="./docs/blocks/call-to-action.html">
                                                            <div class="rounded img-svg d-none d-lg-block p-4 mb-lg-2"><img
                                                                    class="rounded-0" src="./assets/img/demos/block3.svg"
                                                                    alt=""></div>
                                                            <span>Call to Action</span>
                                                        </a>
                                                    </li>
                                                    <li class="col"><a class="dropdown-item"
                                                            href="./docs/blocks/clients.html">
                                                            <div class="rounded img-svg d-none d-lg-block p-4 mb-lg-2"><img
                                                                    class="rounded-0" src="./assets/img/demos/block4.svg"
                                                                    alt=""></div>
                                                            <span>Clients</span>
                                                        </a>
                                                    </li>
                                                    <li class="col"><a class="dropdown-item"
                                                            href="./docs/blocks/contact.html">
                                                            <div class="rounded img-svg d-none d-lg-block p-4 mb-lg-2"><img
                                                                    class="rounded-0" src="./assets/img/demos/block5.svg"
                                                                    alt=""></div>
                                                            <span>Contact</span>
                                                        </a>
                                                    </li>
                                                    <li class="col"><a class="dropdown-item"
                                                            href="./docs/blocks/facts.html">
                                                            <div class="rounded img-svg d-none d-lg-block p-4 mb-lg-2"><img
                                                                    class="rounded-0" src="./assets/img/demos/block6.svg"
                                                                    alt=""></div>
                                                            <span>Facts</span>
                                                        </a>
                                                    </li>
                                                    <li class="col"><a class="dropdown-item"
                                                            href="./docs/blocks/faq.html">
                                                            <div class="rounded img-svg d-none d-lg-block p-4 mb-lg-2"><img
                                                                    class="rounded-0" src="./assets/img/demos/block7.svg"
                                                                    alt=""></div>
                                                            <span>FAQ</span>
                                                        </a>
                                                    </li>
                                                    <li class="col"><a class="dropdown-item"
                                                            href="./docs/blocks/features.html">
                                                            <div class="rounded img-svg d-none d-lg-block p-4 mb-lg-2"><img
                                                                    class="rounded-0" src="./assets/img/demos/block8.svg"
                                                                    alt=""></div>
                                                            <span>Features</span>
                                                        </a>
                                                    </li>
                                                    <li class="col"><a class="dropdown-item"
                                                            href="./docs/blocks/footer.html">
                                                            <div class="rounded img-svg d-none d-lg-block p-4 mb-lg-2"><img
                                                                    class="rounded-0" src="./assets/img/demos/block9.svg"
                                                                    alt=""></div>
                                                            <span>Footer</span>
                                                        </a>
                                                    </li>
                                                    <li class="col"><a class="dropdown-item"
                                                            href="./docs/blocks/hero.html">
                                                            <div class="rounded img-svg d-none d-lg-block p-4 mb-lg-2"><img
                                                                    class="rounded-0" src="./assets/img/demos/block10.svg"
                                                                    alt=""></div>
                                                            <span>Hero</span>
                                                        </a>
                                                    </li>
                                                    <li class="col"><a class="dropdown-item"
                                                            href="./docs/blocks/misc.html">
                                                            <div class="rounded img-svg d-none d-lg-block p-4 mb-lg-2"><img
                                                                    class="rounded-0" src="./assets/img/demos/block17.svg"
                                                                    alt=""></div>
                                                            <span>Misc</span>
                                                        </a>
                                                    </li>
                                                    <li class="col"><a class="dropdown-item"
                                                            href="./docs/blocks/navbar.html">
                                                            <div class="rounded img-svg d-none d-lg-block p-4 mb-lg-2"><img
                                                                    class="rounded-0" src="./assets/img/demos/block11.svg"
                                                                    alt=""></div>
                                                            <span>Navbar</span>
                                                        </a>
                                                    </li>
                                                    <li class="col"><a class="dropdown-item"
                                                            href="./docs/blocks/portfolio.html">
                                                            <div class="rounded img-svg d-none d-lg-block p-4 mb-lg-2"><img
                                                                    class="rounded-0" src="./assets/img/demos/block12.svg"
                                                                    alt=""></div>
                                                            <span>Portfolio</span>
                                                        </a>
                                                    </li>
                                                    <li class="col"><a class="dropdown-item"
                                                            href="./docs/blocks/pricing.html">
                                                            <div class="rounded img-svg d-none d-lg-block p-4 mb-lg-2"><img
                                                                    class="rounded-0" src="./assets/img/demos/block13.svg"
                                                                    alt=""></div>
                                                            <span>Pricing</span>
                                                        </a>
                                                    </li>
                                                    <li class="col"><a class="dropdown-item"
                                                            href="./docs/blocks/process.html">
                                                            <div class="rounded img-svg d-none d-lg-block p-4 mb-lg-2"><img
                                                                    class="rounded-0" src="./assets/img/demos/block14.svg"
                                                                    alt=""></div>
                                                            <span>Process</span>
                                                        </a>
                                                    </li>
                                                    <li class="col"><a class="dropdown-item"
                                                            href="./docs/blocks/team.html">
                                                            <div class="rounded img-svg d-none d-lg-block p-4 mb-lg-2"><img
                                                                    class="rounded-0" src="./assets/img/demos/block15.svg"
                                                                    alt=""></div>
                                                            <span>Team</span>
                                                        </a>
                                                    </li>
                                                    <li class="col"><a class="dropdown-item"
                                                            href="./docs/blocks/testimonials.html">
                                                            <div class="rounded img-svg d-none d-lg-block p-4 mb-lg-2"><img
                                                                    class="rounded-0" src="./assets/img/demos/block16.svg"
                                                                    alt=""></div>
                                                            <span>Testimonials</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <!--/.row -->
                                            </li>
                                            <!--/.mega-menu-content-->
                                        </ul>
                                        <!--/.dropdown-menu -->
                                    </li>
                                    <li class="nav-item dropdown dropdown-mega">
                                        <a class="nav-link dropdown-toggle" href="#"
                                            data-bs-toggle="dropdown">Documentation</a>
                                        <ul class="dropdown-menu mega-menu">
                                            <li class="mega-menu-content">
                                                <div class="row gx-0 gx-lg-3">
                                                    <div class="col-lg-4">
                                                        <h6 class="dropdown-header">Usage</h6>
                                                        <ul class="list-unstyled cc-2 pb-lg-1">
                                                            <li><a class="dropdown-item" href="./docs/index.html">Get
                                                                    Started</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/forms.html">Forms</a></li>
                                                            <li><a class="dropdown-item" href="./docs/faq.html">FAQ</a>
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/changelog.html">Changelog</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/credits.html">Credits</a></li>
                                                        </ul>
                                                        <h6 class="dropdown-header mt-lg-6">Styleguide</h6>
                                                        <ul class="list-unstyled cc-2">
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/styleguide/colors.html">Colors</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/styleguide/fonts.html">Fonts</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/styleguide/icons-svg.html">SVG Icons</a>
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/styleguide/icons-font.html">Font Icons</a>
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/styleguide/illustrations.html">Illustrations</a>
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/styleguide/backgrounds.html">Backgrounds</a>
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/styleguide/misc.html">Misc</a></li>
                                                        </ul>
                                                    </div>
                                                    <!--/column -->
                                                    <div class="col-lg-8">
                                                        <h6 class="dropdown-header">Elements</h6>
                                                        <ul class="list-unstyled cc-3">
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/accordion.html">Accordion</a>
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/alerts.html">Alerts</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/animations.html">Animations</a>
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/avatars.html">Avatars</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/background.html">Background</a>
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/badges.html">Badges</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/buttons.html">Buttons</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/card.html">Card</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/carousel.html">Carousel</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/dividers.html">Dividers</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/form-elements.html">Form
                                                                    Elements</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/image-hover.html">Image Hover</a>
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/image-mask.html">Image Mask</a>
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/lightbox.html">Lightbox</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/player.html">Media Player</a>
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/modal.html">Modal</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/pagination.html">Pagination</a>
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/progressbar.html">Progressbar</a>
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/shadows.html">Shadows</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/shapes.html">Shapes</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/tables.html">Tables</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/tabs.html">Tabs</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/text-animations.html">Text
                                                                    Animations</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/text-highlight.html">Text
                                                                    Highlight</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/tiles.html">Tiles</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/tooltips-popovers.html">Tooltips
                                                                    & Popovers</a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="./docs/elements/typography.html">Typography</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <!--/column -->
                                                </div>
                                                <!--/.row -->
                                            </li>
                                            <!--/.mega-menu-content-->
                                        </ul>
                                        <!--/.dropdown-menu -->
                                    </li>
                                </ul>
                                <!-- /.navbar-nav -->
                                <div class="offcanvas-footer d-lg-none">
                                    <div>
                                        <a href="mailto:first.last@email.com" class="link-inverse">info@email.com</a>
                                        <br /> 00 (123) 456 78 90 <br />
                                        <nav class="nav social social-white mt-4">
                                            <a href="#"><i class="uil uil-twitter"></i></a>
                                            <a href="#"><i class="uil uil-facebook-f"></i></a>
                                            <a href="#"><i class="uil uil-dribbble"></i></a>
                                            <a href="#"><i class="uil uil-instagram"></i></a>
                                            <a href="#"><i class="uil uil-youtube"></i></a>
                                        </nav>
                                        <!-- /.social -->
                                    </div>
                                </div>
                                <!-- /.offcanvas-footer -->
                            </div>
                            <!-- /.offcanvas-body -->
                        </div>
                        <!-- /.navbar-collapse -->
                        <div class="navbar-other w-100 d-flex ms-auto">
                            <ul class="navbar-nav flex-row align-items-center ms-auto">
                                <li class="nav-item"><a class="nav-link" data-bs-toggle="offcanvas"
                                        data-bs-target="#offcanvas-search"><i class="uil uil-search"></i></a></li>
                                <li class="nav-item">
                                    <a class="nav-link position-relative d-flex flex-row align-items-center"
                                        data-bs-toggle="offcanvas" data-bs-target="#offcanvas-cart">
                                        <i class="uil uil-shopping-cart"></i>
                                        <span class="badge badge-cart bg-primary">3</span>
                                    </a>
                                </li>
                                <li class="nav-item d-lg-none">
                                    <button class="hamburger offcanvas-nav-btn"><span></span></button>
                                </li>
                            </ul>
                            <!-- /.navbar-nav -->
                        </div>
                        <!-- /.navbar-other -->
                    </div>
                    <!-- /.container -->
                </nav>
                <!-- /.navbar -->
                <div class="offcanvas offcanvas-end bg-light" id="offcanvas-cart" data-bs-scroll="true">
                    <div class="offcanvas-header">
                        <h3 class="mb-0">Your Cart</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body d-flex flex-column">
                        <div class="shopping-cart">
                            <div class="shopping-cart-item d-flex justify-content-between mb-4">
                                <div class="d-flex flex-row">
                                    <figure class="rounded w-17"><a href="./shop-product.html"><img
                                                src="./assets/img/photos/sth1.jpg"
                                                srcset="./assets/img/photos/sth1@2x.jpg 2x" alt="" /></a></figure>
                                    <div class="w-100 ms-4">
                                        <h3 class="post-title fs-16 lh-xs mb-1"><a href="./shop-product.html"
                                                class="link-dark">Nike Air Sneakers</a></h3>
                                        <p class="price fs-sm"><del><span class="amount">$55.00</span></del> <ins><span
                                                    class="amount">$45.99</span></ins></p>
                                        <div class="form-select-wrapper">
                                            <select class="form-select form-select-sm">
                                                <option value="1" selected>1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>
                                        </div>
                                        <!--/.form-select-wrapper -->
                                    </div>
                                </div>
                                <div class="ms-2"><a href="#" class="link-dark"><i
                                            class="uil uil-trash-alt"></i></a></div>
                            </div>
                            <!--/.shopping-cart-item -->
                            <div class="shopping-cart-item d-flex justify-content-between mb-4">
                                <div class="d-flex flex-row">
                                    <figure class="rounded w-17"><a href="./shop-product.html"><img
                                                src="./assets/img/photos/sth2.jpg"
                                                srcset="./assets/img/photos/sth2@2x.jpg 2x" alt="" /></a></figure>
                                    <div class="w-100 ms-4">
                                        <h3 class="post-title fs-16 lh-xs mb-1"><a href="./shop-product.html"
                                                class="link-dark">Colorful Sneakers</a></h3>
                                        <p class="price fs-sm"><span class="amount">$45.00</span></p>
                                        <div class="form-select-wrapper">
                                            <select class="form-select form-select-sm">
                                                <option value="1" selected>1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>
                                        </div>
                                        <!--/.form-select-wrapper -->
                                    </div>
                                </div>
                                <div class="ms-2"><a href="#" class="link-dark"><i
                                            class="uil uil-trash-alt"></i></a></div>
                            </div>
                            <!--/.shopping-cart-item -->
                            <div class="shopping-cart-item d-flex justify-content-between mb-4">
                                <div class="d-flex flex-row">
                                    <figure class="rounded w-17"><a href="./shop-product.html"><img
                                                src="./assets/img/photos/sth3.jpg"
                                                srcset="./assets/img/photos/sth3@2x.jpg 2x" alt="" /></a></figure>
                                    <div class="w-100 ms-4">
                                        <h3 class="post-title fs-16 lh-xs mb-1"><a href="./shop-product.html"
                                                class="link-dark">Polaroid Camera</a></h3>
                                        <p class="price fs-sm"><span class="amount">$45.00</span></p>
                                        <div class="form-select-wrapper">
                                            <select class="form-select form-select-sm">
                                                <option value="1" selected>1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>
                                        </div>
                                        <!--/.form-select-wrapper -->
                                    </div>
                                </div>
                                <div class="ms-2"><a href="#" class="link-dark"><i
                                            class="uil uil-trash-alt"></i></a></div>
                            </div>
                            <!--/.shopping-cart-item -->
                        </div>
                        <!-- /.shopping-cart-->
                        <div class="offcanvas-footer flex-column text-center">
                            <div class="d-flex w-100 justify-content-between mb-4">
                                <span>Subtotal:</span>
                                <span class="h6 mb-0">$135.99</span>
                            </div>
                            <a href="#" class="btn btn-primary btn-icon btn-icon-start rounded w-100 mb-4"><i
                                    class="uil uil-credit-card fs-18"></i> Checkout</a>
                            <p class="fs-14 mb-0">Free shipping on all orders over $50</p>
                        </div>
                        <!-- /.offcanvas-footer-->
                    </div>
                    <!-- /.offcanvas-body -->
                </div>
                <!-- /.offcanvas -->
                <div class="offcanvas offcanvas-top bg-light" id="offcanvas-search" data-bs-scroll="true">
                    <div class="container d-flex flex-row py-6">
                        <form class="search-form w-100">
                            <input id="search-form" type="text" class="form-control"
                                placeholder="Type keyword and hit enter">
                        </form>
                        <!-- /.search-form -->
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <!-- /.container -->
                </div>
                <!-- /.offcanvas -->
            </header>
            <!-- /header -->
            <section class="wrapper bg-gray">
                <div class="container py-3 py-md-5">
                    <nav class="d-inline-block" aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="#">Shop</a></li>
                            <li class="breadcrumb-item"><a href="#">Cart</a></li>
                            <li class="breadcrumb-item active text-muted" aria-current="page">Checkout</li>
                        </ol>
                    </nav>
                    <!-- /nav -->
                </div>
                <!-- /.container -->
            </section>
            <!-- /section -->
            <section class="wrapper bg-light">
                <div class="container pt-12 pt-md-14 pb-14 pb-md-16">
                    <div class="row gx-md-8 gx-xl-12 gy-12">
                        <div class="col-lg-8">
                            <div class="alert alert-blue alert-icon mb-6" role="alert">
                                <i class="uil uil-exclamation-circle"></i> Already have an account? <a href="#"
                                    data-bs-target="#modal-signin" data-bs-toggle="modal" data-bs-dismiss="modal"
                                    class="alert-link hover">Sign in</a> for faster checkout experience.
                            </div>
                            <h3 class="mb-4">Billing address</h3>
                            <form class="needs-validation" novalidate>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="firstName"
                                                placeholder="First name" value="" required>
                                            <label for="firstName" class="form-label">First name</label>
                                            <div class="invalid-feedback"> Valid first name is required. </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="lastName"
                                                placeholder="Last name" value="" required>
                                            <label for="lastName" class="form-label">Last name</label>
                                            <div class="invalid-feedback"> Valid last name is required. </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="email"
                                                placeholder="you@example.com" required>
                                            <label for="email" class="form-label">Email</label>
                                            <div class="invalid-feedback"> Please enter a valid email address for shipping
                                                updates. </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="address"
                                                placeholder="1234 Main St" required>
                                            <label for="address" class="form-label">Address</label>
                                            <div class="invalid-feedback"> Please enter your shipping address. </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="address2"
                                                placeholder="Apartment or suite">
                                            <label for="address2" class="form-label">Address 2 <span
                                                    class="text-muted">(Optional)</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-select-wrapper">
                                            <select class="form-select" id="country" required>
                                                <option value="">Country</option>
                                                <option>United States</option>
                                            </select>
                                            <div class="invalid-feedback"> Please select a valid country. </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-select-wrapper">
                                            <select class="form-select" id="state" required>
                                                <option value="">State</option>
                                                <option>California</option>
                                            </select>
                                            <div class="invalid-feedback"> Please provide a valid state. </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="zip"
                                                placeholder="Zip Code" required>
                                            <label for="zip" class="form-label">Zip Code</label>
                                            <div class="invalid-feedback"> Zip code required. </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="mt-7 mb-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="same-address">
                                    <label class="form-check-label" for="same-address">Shipping address is the same as my
                                        billing address</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="save-info">
                                    <label class="form-check-label" for="save-info">Save this information for next
                                        time</label>
                                </div>
                                <hr class="mt-7 mb-6">
                                <h3 class="mb-4">Payment</h3>
                                <div class="mt-3 mb-6">
                                    <div class="form-check">
                                        <input id="credit" name="paymentMethod" type="radio"
                                            class="form-check-input" checked required>
                                        <label class="form-check-label" for="credit">Credit card</label>
                                    </div>
                                    <div class="form-check">
                                        <input id="debit" name="paymentMethod" type="radio"
                                            class="form-check-input" required>
                                        <label class="form-check-label" for="debit">Debit card</label>
                                    </div>
                                    <div class="form-check">
                                        <input id="paypal" name="paymentMethod" type="radio"
                                            class="form-check-input" required>
                                        <label class="form-check-label" for="paypal">PayPal</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-8">
                                        <div class="row gy-3 gx-3">
                                            <div class="col-md-12">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="cc-number"
                                                        placeholder="Credit card number" required>
                                                    <label for="cc-number" class="form-label">Credit card number</label>
                                                    <div class="invalid-feedback"> Credit card number is required </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="cc-name"
                                                        placeholder="Name on card" required>
                                                    <label for="cc-name" class="form-label">Name on card</label>
                                                    <div class="invalid-feedback"> Name on card is required </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="cc-expiration"
                                                        placeholder="Expiration" required>
                                                    <label for="cc-expiration" class="form-label">Expiration</label>
                                                    <div class="invalid-feedback"> Expiration date required </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="cc-cvv"
                                                        placeholder="CVV" required>
                                                    <label for="cc-cvv" class="form-label">CVV</label>
                                                    <div class="invalid-feedback"> Security code required </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /column -->
                        <div class="col-lg-4">
                            <h3 class="mb-4">Order Summary</h3>
                            <div class="shopping-cart mb-7">
                                <div class="shopping-cart-item d-flex justify-content-between mb-4">
                                    <div class="d-flex flex-row d-flex align-items-center">
                                        <figure class="rounded w-17"><a href="./shop-product.html"><img
                                                    src="./assets/img/photos/sth1.jpg"
                                                    srcset="./assets/img/photos/sth1@2x.jpg 2x" alt="" /></a>
                                        </figure>
                                        <div class="w-100 ms-4">
                                            <h3 class="post-title h6 lh-xs mb-1"><a href="./shop-product.html"
                                                    class="link-dark">Nike Air Sneakers</a></h3>
                                            <div class="small">Color: Black</div>
                                            <div class="small">Size: 43</div>
                                        </div>
                                    </div>
                                    <div class="ms-2 d-flex align-items-center">
                                        <p class="price fs-sm"><span class="amount">$45.99</span></p>
                                    </div>
                                </div>
                                <!--/.shopping-cart-item -->
                                <div class="shopping-cart-item d-flex justify-content-between mb-4">
                                    <div class="d-flex flex-row d-flex align-items-center">
                                        <figure class="rounded w-17"><a href="./shop-product.html"><img
                                                    src="./assets/img/photos/sth2.jpg"
                                                    srcset="./assets/img/photos/sth2@2x.jpg 2x" alt="" /></a>
                                        </figure>
                                        <div class="w-100 ms-4">
                                            <h3 class="post-title h6 lh-xs mb-1"><a href="./shop-product.html"
                                                    class="link-dark">Colorful Sneakers</a></h3>
                                            <div class="small">Color: Misc</div>
                                            <div class="small">Size: 43</div>
                                        </div>
                                    </div>
                                    <div class="ms-2 d-flex align-items-center">
                                        <p class="price fs-sm"><span class="amount">$45.00</span></p>
                                    </div>
                                </div>
                                <!--/.shopping-cart-item -->
                                <div class="shopping-cart-item d-flex justify-content-between mb-4">
                                    <div class="d-flex flex-row d-flex align-items-center">
                                        <figure class="rounded w-17"><a href="./shop-product.html"><img
                                                    src="./assets/img/photos/sth3.jpg"
                                                    srcset="./assets/img/photos/sth3@2x.jpg 2x" alt="" /></a>
                                        </figure>
                                        <div class="w-100 ms-4">
                                            <h3 class="post-title h6 lh-xs mb-1"><a href="./shop-product.html"
                                                    class="link-dark">Polaroid Camera</a></h3>
                                            <div class="small">Color: Black</div>
                                        </div>
                                    </div>
                                    <div class="ms-2 d-flex align-items-center">
                                        <p class="price fs-sm"><span class="amount">$45.00</span></p>
                                    </div>
                                </div>
                                <!--/.shopping-cart-item -->
                            </div>
                            <!-- /.shopping-cart-->
                            <hr class="my-4">
                            <h3 class="mb-2">Shipping</h3>
                            <div class="mb-5">
                                <div class="form-check mb-2">
                                    <input id="standart" name="shippingMethod" type="radio" class="form-check-input"
                                        required>
                                    <label class="form-check-label" for="standart">Free - Standart delivery</label>
                                    <small class="text-muted d-block">Shipment may take 5-6 business days</small>
                                </div>
                                <div class="form-check">
                                    <input id="express" name="shippingMethod" type="radio" class="form-check-input"
                                        checked required>
                                    <label class="form-check-label" for="express">$10 - Express delivery</label>
                                    <small class="text-muted d-block">Shipment may take 2-3 business days</small>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-order">
                                    <tbody>
                                        <tr>
                                            <td class="ps-0"><strong class="text-dark">Subtotal</strong></td>
                                            <td class="pe-0 text-end">
                                                <p class="price">$135.99</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0"><strong class="text-dark">Discount (5%)</strong></td>
                                            <td class="pe-0 text-end">
                                                <p class="price text-red">-$6.8</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0"><strong class="text-dark">Shipping</strong></td>
                                            <td class="pe-0 text-end">
                                                <p class="price">$10</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0"><strong class="text-dark">Grand Total</strong></td>
                                            <td class="pe-0 text-end">
                                                <p class="price text-dark fw-bold">$152.79</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <a href="#" class="btn btn-primary rounded w-100 mt-4">Place Order</a>
                        </div>
                        <!-- /column -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container -->
            </section>
            <!-- /section -->
            <div class="modal fade" id="modal-signin" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content text-center">
                        <div class="modal-body">
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                            <h2 class="mb-3 text-start">Welcome Back</h2>
                            <p class="lead mb-6 text-start">Fill your email and password to sign in.</p>
                            <form class="text-start mb-3">
                                <div class="form-floating mb-4">
                                    <input type="email" class="form-control" placeholder="Email" id="loginEmail">
                                    <label for="loginEmail">Email</label>
                                </div>
                                <div class="form-floating password-field mb-4">
                                    <input type="password" class="form-control" placeholder="Password"
                                        id="loginPassword">
                                    <span class="password-toggle"><i class="uil uil-eye"></i></span>
                                    <label for="loginPassword">Password</label>
                                </div>
                                <a class="btn btn-primary rounded-pill btn-login w-100 mb-2">Sign In</a>
                            </form>
                            <!-- /form -->
                            <p class="mb-1"><a href="#" class="hover">Forgot Password?</a></p>
                            <p class="mb-0">Don't have an account? <a href="#" data-bs-target="#modal-signup"
                                    data-bs-toggle="modal" data-bs-dismiss="modal" class="hover">Sign up</a></p>
                            <div class="divider-icon my-4">or</div>
                            <nav class="nav social justify-content-center text-center">
                                <a href="#" class="btn btn-circle btn-sm btn-google"><i
                                        class="uil uil-google"></i></a>
                                <a href="#" class="btn btn-circle btn-sm btn-facebook-f"><i
                                        class="uil uil-facebook-f"></i></a>
                                <a href="#" class="btn btn-circle btn-sm btn-twitter"><i
                                        class="uil uil-twitter"></i></a>
                            </nav>
                            <!--/.social -->
                        </div>
                        <!--/.modal-content -->
                    </div>
                    <!--/.modal-body -->
                </div>
                <!--/.modal-dialog -->
            </div>
            <!--/.modal -->
            <div class="modal fade" id="modal-signup" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content text-center">
                        <div class="modal-body">
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                            <h2 class="mb-3 text-start">Sign up to Sandbox</h2>
                            <p class="lead mb-6 text-start">Registration takes less than a minute.</p>
                            <form class="text-start mb-3">
                                <div class="form-floating mb-4">
                                    <input type="text" class="form-control" placeholder="Name" id="loginName">
                                    <label for="loginName">Name</label>
                                </div>
                                <div class="form-floating mb-4">
                                    <input type="email" class="form-control" placeholder="Email" id="loginEmail">
                                    <label for="loginEmail">Email</label>
                                </div>
                                <div class="form-floating password-field mb-4">
                                    <input type="password" class="form-control" placeholder="Password"
                                        id="loginPassword">
                                    <span class="password-toggle"><i class="uil uil-eye"></i></span>
                                    <label for="loginPassword">Password</label>
                                </div>
                                <div class="form-floating password-field mb-4">
                                    <input type="password" class="form-control" placeholder="Confirm Password"
                                        id="loginPasswordConfirm">
                                    <span class="password-toggle"><i class="uil uil-eye"></i></span>
                                    <label for="loginPasswordConfirm">Confirm Password</label>
                                </div>
                                <a class="btn btn-primary rounded-pill btn-login w-100 mb-2">Sign Up</a>
                            </form>
                            <!-- /form -->
                            <p class="mb-0">Already have an account? <a href="#"
                                    data-bs-target="#modal-signin" data-bs-toggle="modal" data-bs-dismiss="modal"
                                    class="hover">Sign in</a></p>
                            <div class="divider-icon my-4">or</div>
                            <nav class="nav social justify-content-center text-center">
                                <a href="#" class="btn btn-circle btn-sm btn-google"><i
                                        class="uil uil-google"></i></a>
                                <a href="#" class="btn btn-circle btn-sm btn-facebook-f"><i
                                        class="uil uil-facebook-f"></i></a>
                                <a href="#" class="btn btn-circle btn-sm btn-twitter"><i
                                        class="uil uil-twitter"></i></a>
                            </nav>
                            <!--/.social -->
                        </div>
                        <!--/.modal-content -->
                    </div>
                    <!--/.modal-body -->
                </div>
                <!--/.modal-dialog -->
            </div>
            <!--/.modal -->
            <section class="wrapper bg-gray">
                <div class="container py-12 py-md-14">
                    <div class="row gx-lg-8 gx-xl-12 gy-8">
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex flex-row">
                                <div>
                                    <img src="./assets/img/icon/shipment.svg"
                                        class="svg-inject icon-svg icon-svg-sm solid-mono text-navy me-4"
                                        alt="" />
                                </div>
                                <div>
                                    <h4 class="mb-1">Free Shipping</h4>
                                    <p class="mb-0">Duis mollis gravida commodo id luctus erat porttitor ligula, eget
                                        lacinia odio sem.</p>
                                </div>
                            </div>
                        </div>
                        <!--/column -->
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex flex-row">
                                <div>
                                    <img src="./assets/img/icons/solid/push-cart.svg"
                                        class="svg-inject icon-svg icon-svg-sm solid-mono text-navy me-4"
                                        alt="" />
                                </div>
                                <div>
                                    <h4 class="mb-1">30 Days Return</h4>
                                    <p class="mb-0">Duis mollis gravida commodo id luctus erat porttitor ligula, eget
                                        lacinia odio sem.</p>
                                </div>
                            </div>
                        </div>
                        <!--/column -->
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex flex-row">
                                <div>
                                    <img src="./assets/img/icons/solid/verify.svg"
                                        class="svg-inject icon-svg icon-svg-sm solid-mono text-navy me-4"
                                        alt="" />
                                </div>
                                <div>
                                    <h4 class="mb-1">2-Years Warranty</h4>
                                    <p class="mb-0">Duis mollis gravida commodo id luctus erat porttitor ligula, eget
                                        lacinia odio sem.</p>
                                </div>
                            </div>
                        </div>
                        <!--/column -->
                    </div>
                    <!--/.row -->
                </div>
                <!-- /.container -->
            </section>
            <!-- /section -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="bg-dark text-inverse">
            <div class="container py-13 py-md-15">
                <div class="row gy-6 gy-lg-0">
                    <div class="col-md-4 col-lg-3">
                        <div class="widget">
                            <img class="mb-4" src="./assets/img/logo-light.png"
                                srcset="./assets/img/logo-light@2x.png 2x" alt="" />
                            <p class="mb-4">© 2022 Sandbox. <br class="d-none d-lg-block" />All rights reserved.</p>
                            <nav class="nav social social-white">
                                <a href="#"><i class="uil uil-twitter"></i></a>
                                <a href="#"><i class="uil uil-facebook-f"></i></a>
                                <a href="#"><i class="uil uil-dribbble"></i></a>
                                <a href="#"><i class="uil uil-instagram"></i></a>
                                <a href="#"><i class="uil uil-youtube"></i></a>
                            </nav>
                            <!-- /.social -->
                        </div>
                        <!-- /.widget -->
                    </div>
                    <!-- /column -->
                    <div class="col-md-4 col-lg-3">
                        <div class="widget">
                            <h4 class="widget-title text-white mb-3">Get in Touch</h4>
                            <address class="pe-xl-15 pe-xxl-17">Moonshine St. 14/05 Light City, London, United Kingdom
                            </address>
                            <a href="mailto:#">info@email.com</a><br /> 00 (123) 456 78 90
                        </div>
                        <!-- /.widget -->
                    </div>
                    <!-- /column -->
                    <div class="col-md-4 col-lg-3">
                        <div class="widget">
                            <h4 class="widget-title text-white mb-3">Learn More</h4>
                            <ul class="list-unstyled  mb-0">
                                <li><a href="#">About Us</a></li>
                                <li><a href="#">Our Story</a></li>
                                <li><a href="#">Projects</a></li>
                                <li><a href="#">Terms of Use</a></li>
                                <li><a href="#">Privacy Policy</a></li>
                            </ul>
                        </div>
                        <!-- /.widget -->
                    </div>
                    <!-- /column -->
                    <div class="col-md-12 col-lg-3">
                        <div class="widget">
                            <h4 class="widget-title text-white mb-3">Our Newsletter</h4>
                            <p class="mb-5">Subscribe to our newsletter to get our news & deals delivered to you.</p>
                            <div class="newsletter-wrapper">
                                <!-- Begin Mailchimp Signup Form -->
                                <div id="mc_embed_signup2">
                                    <form
                                        action="https://elemisfreebies.us20.list-manage.com/subscribe/post?u=aa4947f70a475ce162057838d&amp;id=b49ef47a9a"
                                        method="post" id="mc-embedded-subscribe-form2"
                                        name="mc-embedded-subscribe-form" class="validate dark-fields" target="_blank"
                                        novalidate>
                                        <div id="mc_embed_signup_scroll2">
                                            <div class="mc-field-group input-group form-floating">
                                                <input type="email" value="" name="EMAIL"
                                                    class="required email form-control" placeholder="Email Address"
                                                    id="mce-EMAIL2">
                                                <label for="mce-EMAIL2">Email Address</label>
                                                <input type="submit" value="Join" name="subscribe"
                                                    id="mc-embedded-subscribe2" class="btn btn-primary ">
                                            </div>
                                            <div id="mce-responses2" class="clear">
                                                <div class="response" id="mce-error-response2" style="display:none">
                                                </div>
                                                <div class="response" id="mce-success-response2" style="display:none">
                                                </div>
                                            </div>
                                            <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                                            <div style="position: absolute; left: -5000px;" aria-hidden="true"><input
                                                    type="text" name="b_ddc180777a163e0f9f66ee014_4b1bcfa0bc"
                                                    tabindex="-1" value=""></div>
                                            <div class="clear"></div>
                                        </div>
                                    </form>
                                </div>
                                <!--End mc_embed_signup-->
                            </div>
                            <!-- /.newsletter-wrapper -->
                        </div>
                        <!-- /.widget -->
                    </div>
                    <!-- /column -->
                </div>
                <!--/.row -->
            </div>
            <!-- /.container -->
        </footer>
        <div class="progress-wrap">
            <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
                <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
            </svg>
        </div>
        <script src="./assets/js/plugins.js"></script>
        <script src="./assets/js/theme.js"></script>
    </body>

    </html>
@endsection
