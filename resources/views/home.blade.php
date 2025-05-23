@extends('layouts.app')

@section('title', 'Homepage')

@section('content')
    <section id="home">
        <div class="wrapper image-wrapper bg-image bg-overlay text-white"
            data-image-src="{{ asset('assets/img/shoes/back.jpeg') }}">
            <div class="container py-16 py-md-19 text-center">
                <h2 class="display-1 text-white mb-0">I take photographs with <br class="d-none d-md-block"> creativity,
                    concept & passion</h2>
            </div>
            <!-- /.container -->
        </div>
        <!-- /.overflow-hidden -->
    </section>
    <!-- /section -->
    <section id="services">
        <div class="wrapper bg-gray">
            <div class="container py-15 py-md-17">
                <div class="row gx-lg-0 gy-10 align-items-center">
                    <div class="col-lg-6">
                        <div class="row g-6 text-center">
                            <div class="col-md-6">
                                <div class="card shadow-lg mb-6">
                                    <figure class="card-img-top overlay overlay-1">
                                        <a href="#"><img class="img-fluid"
                                                src="{{ asset('assets/home/img/nike/wp2.jpg') }}" alt="" /></a>
                                        <figcaption>
                                            <h5 class="from-top mb-0">View Gallery</h5>
                                        </figcaption>
                                    </figure>
                                    <div class="card-body p-4">
                                        <h3 class="h4 mb-0">Products</h3>
                                    </div>
                                    <!--/.card-body -->
                                </div>
                                <!-- /.card -->
                                <div class="card shadow-lg">
                                    <figure class="card-img-top overlay overlay-1">
                                        <a href="#"><img class="img-fluid"
                                                src="{{ asset('assets/home/img/photos/fs6.jpg') }}" alt="" /></a>
                                        <figcaption>
                                            <h5 class="from-top mb-0">View Gallery</h5>
                                        </figcaption>
                                    </figure>
                                    <div class="card-body p-4">
                                        <h3 class="h4 mb-0">Recipes</h3>
                                    </div>
                                    <!--/.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!-- /column -->
                            <div class="col-md-6">
                                <div class="card shadow-lg mt-md-6 mb-6">
                                    <figure class="card-img-top overlay overlay-1">
                                        <a href="#"><img class="img-fluid"
                                                src="{{ asset('assets/home/img/nike/wp3.jpg') }}" alt="" /></a>
                                        <figcaption>
                                            <h5 class="from-top mb-0">View Gallery</h5>
                                        </figcaption>
                                    </figure>
                                    <div class="card-body p-4">
                                        <h3 class="h4 mb-0">Restaurants</h3>
                                    </div>
                                    <!--/.card-body -->
                                </div>
                                <!-- /.card -->
                                <div class="card shadow-lg">
                                    <figure class="card-img-top overlay overlay-1">
                                        <a href="#"><img class="img-fluid"
                                                src="{{ asset('assets/home/img/photos/fs7.jpg') }}" alt="" /></a>
                                        <figcaption>
                                            <h5 class="from-top mb-0">View Gallery</h5>
                                        </figcaption>
                                    </figure>
                                    <div class="card-body p-4">
                                        <h3 class="h4 mb-0">Still Life</h3>
                                    </div>
                                    <!--/.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!-- /column -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /column -->
                    <div class="col-lg-5 offset-lg-1">
                        <h2 class="display-5 mb-3">My Services</h2>
                        <p class="lead fs-lg">I would like to give you a unique photography experience, capture your
                            products with excellent composition and lighting skills.</p>
                        <p>Donec ullamcorper nulla non metus auctor fringilla. Lorem ipsum dolor sit amet, consectetur
                            adipiscing elit. Nullam quis risus eget urna mollis ornare vel eu leo. Nullam quis risus eget
                            urna mollis ornare vel eu leo. Maecenas faucibus mollis elit interdum. Duis mollis, est non
                            commodo luctus, nisi erat ligula mollis metus auctor fringilla.</p>
                        <a href="#" class="btn btn-primary rounded-pill mt-2">More Details</a>
                    </div>
                    <!-- /column -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container -->
        </div>
        <!-- /.wrapper -->
    </section>
    <!-- /.wrapper -->
    <section id="portfolio">
        <div class="wrapper bg-gray">
            <div class="container py-15 py-md-17 text-center">
                <div class="row">
                    <div class="col-lg-10 col-xl-8 col-xxl-7 mx-auto mb-8">
                        <h2 class="display-5 mb-3">My Selected Shots</h2>
                        <p class="lead fs-lg">Photography is my passion and I love to turn ideas into beautiful things.</p>
                    </div>
                    <!-- /column -->
                </div>
                <!-- /.row -->
                <div class="grid grid-view projects-masonry">
                    <div class="isotope-filter filter mb-10">
                        <ul>
                            <li><a class="filter-item active" data-filter="*">All</a></li>
                            <li><a class="filter-item" data-filter=".foods">Men</a></li>
                            <li><a class="filter-item" data-filter=".drinks">Woman</a></li>
                            <li><a class="filter-item" data-filter=".events">Child</a></li>
                        </ul>
                    </div>
                    <div class="row gx-md-6 gy-6 isotope">
                        <div class="project item col-md-6 col-xl-4 drinks events">
                            <figure class="overlay overlay-1 rounded"><a
                                    href="{{ asset('assets/home/img/photos/pf1-full.jpg') }}" data-glightbox
                                    data-gallery="shots-group"> <img src="{{ asset('assets/home/img/photos/pf1.jpg') }}"
                                        alt="" /></a>
                                <figcaption>
                                    <h5 class="from-top mb-0">Fringilla Nullam</h5>
                                </figcaption>
                            </figure>
                        </div>
                        <!-- /.project -->
                        <div class="project item col-md-6 col-xl-4 events">
                            <figure class="overlay overlay-1 rounded"><a
                                    href="{{ asset('assets/home/img/photos/pf2-full.jpg') }}" data-glightbox
                                    data-gallery="shots-group"> <img src="{{ asset('assets/home/img/photos/pf2.jpg') }}"
                                        alt="" /></a>
                                <figcaption>
                                    <h5 class="from-top mb-0">Ridiculus Parturient</h5>
                                </figcaption>
                            </figure>
                        </div>
                        <!-- /.project -->
                        <div class="project item col-md-6 col-xl-4 drinks foods">
                            <figure class="overlay overlay-1 rounded"><a
                                    href="{{ asset('assets/home/img/photos/pf3-full.jpg') }}" data-glightbox
                                    data-gallery="shots-group"> <img src="{{ asset('assets/home/img/photos/pf3.jpg') }}"
                                        alt="" /></a>
                                <figcaption>
                                    <h5 class="from-top mb-0">Ornare Ipsum</h5>
                                </figcaption>
                            </figure>
                        </div>
                        <!-- /.project -->
                        <div class="project item col-md-6 col-xl-4 events">
                            <figure class="overlay overlay-1 rounded"><a
                                    href="{{ asset('assets/home/img/photos/pf4-full.jpg') }}" data-glightbox
                                    data-gallery="shots-group"> <img src="{{ asset('assets/home/img/photos/pf4.jpg') }}"
                                        alt="" /></a>
                                <figcaption>
                                    <h5 class="from-top mb-0">Nullam Mollis</h5>
                                </figcaption>
                            </figure>
                        </div>
                        <!-- /.project -->
                        <div class="project item col-md-6 col-xl-4 pastries events">
                            <figure class="overlay overlay-1 rounded"><a
                                    href="{{ asset('assets/home/img/photos/pf5-full.jpg') }}" data-glightbox
                                    data-gallery="shots-group"> <img src="{{ asset('assets/home/img/photos/pf5.jpg') }}"
                                        alt="" /></a>
                                <figcaption>
                                    <h5 class="from-top mb-0">Euismod Risus</h5>
                                </figcaption>
                            </figure>
                        </div>
                        <!-- /.project -->
                        <div class="project item col-md-6 col-xl-4 foods">
                            <figure class="overlay overlay-1 rounded"><a
                                    href="{{ asset('assets/home/img/photos/pf6-full.jpg') }}" data-glightbox
                                    data-gallery="shots-group"> <img src="{{ asset('assets/home/img/photos/pf6.jpg') }}"
                                        alt="" /></a>
                                <figcaption>
                                    <h5 class="from-top mb-0">Ridiculus Tristique</h5>
                                </figcaption>
                            </figure>
                        </div>
                        <!-- /.project -->
                        <div class="project item col-md-6 col-xl-4 foods drinks">
                            <figure class="overlay overlay-1 rounded"><a
                                    href="{{ asset('assets/home/img/photos/pf7-full.jpg') }}" data-glightbox
                                    data-gallery="shots-group"> <img src="{{ asset('assets/home/img/photos/pf7.jpg') }}"
                                        alt="" /></a>
                                <figcaption>
                                    <h5 class="from-top mb-0">Sollicitudin Pharetra</h5>
                                </figcaption>
                            </figure>
                        </div>
                        <!-- /.project -->
                        <div class="project item col-md-6 col-xl-4 pastries">
                            <figure class="overlay overlay-1 rounded"><a
                                    href="{{ asset('assets/home/img/photos/pf8-full.jpg') }}" data-glightbox
                                    data-gallery="shots-group"> <img src="{{ asset('assets/home/img/photos/pf8.jpg') }}"
                                        alt="" /></a>
                                <figcaption>
                                    <h5 class="from-top mb-0">Tristique Venenatis</h5>
                                </figcaption>
                            </figure>
                        </div>
                        <!-- /.project -->
                        <div class="project item col-md-6 col-xl-4 events">
                            <figure class="overlay overlay-1 rounded"><a
                                    href="{{ asset('assets/home/img/photos/pf9-full.jpg') }}" data-glightbox
                                    data-gallery="shots-group"> <img src="{{ asset('assets/home/img/photos/pf9.jpg') }}"
                                        alt="" /></a>
                                <figcaption>
                                    <h5 class="from-top mb-0">Cursus Fusce</h5>
                                </figcaption>
                            </figure>
                        </div>
                        <!-- /.project -->
                        <div class="project item col-md-6 col-xl-4 foods">
                            <figure class="overlay overlay-1 rounded"><a
                                    href="{{ asset('assets/home/img/photos/pf10-full.jpg') }}" data-glightbox
                                    data-gallery="shots-group"> <img src="{{ asset('assets/home/img/photos/pf10.jpg') }}"
                                        alt="" /></a>
                                <figcaption>
                                    <h5 class="from-top mb-0">Consectetur Malesuada</h5>
                                </figcaption>
                            </figure>
                        </div>
                        <!-- /.project -->
                        <div class="project item col-md-6 col-xl-4 drinks">
                            <figure class="overlay overlay-1 rounded"><a
                                    href="{{ asset('assets/home/img/photos/pf11-full.jpg') }}" data-glightbox
                                    data-gallery="shots-group"> <img src="{{ asset('assets/home/img/photos/pf11.jpg') }}"
                                        alt="" /></a>
                                <figcaption>
                                    <h5 class="from-top mb-0">Ultricies Aenean</h5>
                                </figcaption>
                            </figure>
                        </div>
                        <!-- /.project -->
                        <div class="project item col-md-6 col-xl-4 foods">
                            <figure class="overlay overlay-1 rounded"><a
                                    href="{{ asset('assets/home/img/photos/pf12-full.jpg') }}" data-glightbox
                                    data-gallery="shots-group"> <img src="{{ asset('assets/home/img/photos/pf12.jpg') }}"
                                        alt="" /></a>
                                <figcaption>
                                    <h5 class="from-top mb-0">Pellentesque Commodo</h5>
                                </figcaption>
                            </figure>
                        </div>
                        <!-- /.project -->
                        <div class="project item col-md-6 col-xl-4 drinks">
                            <figure class="overlay overlay-1 rounded"><a
                                    href="{{ asset('assets/home/img/photos/pf13-full.jpg') }}" data-glightbox
                                    data-gallery="shots-group"> <img src="{{ asset('assets/home/img/photos/pf13.jpg') }}"
                                        alt="" /></a>
                                <figcaption>
                                    <h5 class="from-top mb-0">Ultricies Aenean</h5>
                                </figcaption>
                            </figure>
                        </div>
                        <!-- /.project -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.grid -->
            </div>
            <!-- /.container -->
        </div>
        <!-- /.wrapper -->
    </section>
    <!-- /section -->
    <section id="testimonials">
        <div class="wrapper image-wrapper bg-image bg-overlay"
            data-image-src="{{ asset('assets/img/shoes/back.jpeg') }}">
            <div class="container py-15 py-md-17">
                <h2 class="display-5 mb-4 text-center text-white">Happy Customers</h2>
                <div class="swiper-container dots-closer dots-light dots-light-75" data-margin="0" data-dots="true"
                    data-items-xl="3" data-items-md="2" data-items-xs="1">
                    <div class="swiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="item-inner">
                                    <div class="card border-0 bg-white-900">
                                        <div class="card-body">
                                            <span class="ratings five mb-3"></span>
                                            <blockquote class="border-0 mb-0">
                                                <p>“Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.
                                                    Vestibulum id ligula porta felis euismod semper. Cras justo odio dapibus
                                                    facilisis sociis natoque penatibus.”</p>
                                                <div class="blockquote-details">
                                                    <div class="info p-0">
                                                        <h5 class="mb-0">Coriss Ambady</h5>
                                                        <p class="mb-0">Financial Analyst</p>
                                                    </div>
                                                </div>
                                            </blockquote>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!-- /.item-inner -->
                            </div>
                            <!--/.swiper-slide -->
                            <div class="swiper-slide">
                                <div class="item-inner">
                                    <div class="card border-0 bg-white-900">
                                        <div class="card-body">
                                            <span class="ratings five mb-3"></span>
                                            <blockquote class="border-0 mb-0">
                                                <p>“Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.
                                                    Vestibulum id ligula porta felis euismod semper. Cras justo odio dapibus
                                                    facilisis sociis natoque penatibus.”</p>
                                                <div class="blockquote-details">
                                                    <div class="info p-0">
                                                        <h5 class="mb-0">Cory Zamora</h5>
                                                        <p class="mb-0">Marketing Specialist</p>
                                                    </div>
                                                </div>
                                            </blockquote>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!-- /.item-inner -->
                            </div>
                            <!--/.swiper-slide -->
                            <div class="swiper-slide">
                                <div class="item-inner">
                                    <div class="card border-0 bg-white-900">
                                        <div class="card-body">
                                            <span class="ratings five mb-3"></span>
                                            <blockquote class="border-0 mb-0">
                                                <p>“Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.
                                                    Vestibulum id ligula porta felis euismod semper. Cras justo odio dapibus
                                                    facilisis sociis natoque penatibus.”</p>
                                                <div class="blockquote-details">
                                                    <div class="info p-0">
                                                        <h5 class="mb-0">Nikolas Brooten</h5>
                                                        <p class="mb-0">Sales Manager</p>
                                                    </div>
                                                </div>
                                            </blockquote>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!-- /.item-inner -->
                            </div>
                            <!--/.swiper-slide -->
                            <div class="swiper-slide">
                                <div class="item-inner">
                                    <div class="card border-0 bg-white-900">
                                        <div class="card-body">
                                            <span class="ratings five mb-3"></span>
                                            <blockquote class="border-0 mb-0">
                                                <p>“Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.
                                                    Vestibulum id ligula porta felis euismod semper. Cras justo odio dapibus
                                                    facilisis sociis natoque penatibus.”</p>
                                                <div class="blockquote-details">
                                                    <div class="info p-0">
                                                        <h5 class="mb-0">Coriss Ambady</h5>
                                                        <p class="mb-0">Financial Analyst</p>
                                                    </div>
                                                </div>
                                            </blockquote>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!-- /.item-inner -->
                            </div>
                            <!--/.swiper-slide -->
                            <div class="swiper-slide">
                                <div class="item-inner">
                                    <div class="card border-0 bg-white-900">
                                        <div class="card-body">
                                            <span class="ratings five mb-3"></span>
                                            <blockquote class="border-0 mb-0">
                                                <p>“Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.
                                                    Vestibulum id ligula porta felis euismod semper. Cras justo odio dapibus
                                                    facilisis sociis natoque penatibus.”</p>
                                                <div class="blockquote-details">
                                                    <div class="info p-0">
                                                        <h5 class="mb-0">Jackie Sanders</h5>
                                                        <p class="mb-0">Investment Planner</p>
                                                    </div>
                                                </div>
                                            </blockquote>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!-- /.item-inner -->
                            </div>
                            <!--/.swiper-slide -->
                            <div class="swiper-slide">
                                <div class="item-inner">
                                    <div class="card border-0 bg-white-900">
                                        <div class="card-body">
                                            <span class="ratings five mb-3"></span>
                                            <blockquote class="border-0 mb-0">
                                                <p>“Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.
                                                    Vestibulum id ligula porta felis euismod semper. Cras justo odio dapibus
                                                    facilisis sociis natoque penatibus.”</p>
                                                <div class="blockquote-details">
                                                    <div class="info p-0">
                                                        <h5 class="mb-0">Coriss Ambady</h5>
                                                        <p class="mb-0">Financial Analyst</p>
                                                    </div>
                                                </div>
                                            </blockquote>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!-- /.item-inner -->
                            </div>
                            <!--/.swiper-slide -->
                            <div class="swiper-slide">
                                <div class="item-inner">
                                    <div class="card border-0 bg-white-900">
                                        <div class="card-body">
                                            <span class="ratings five mb-3"></span>
                                            <blockquote class="border-0 mb-0">
                                                <p>“Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.
                                                    Vestibulum id ligula porta felis euismod semper. Cras justo odio dapibus
                                                    facilisis sociis natoque penatibus.”</p>
                                                <div class="blockquote-details">
                                                    <div class="info p-0">
                                                        <h5 class="mb-0">Jackie Sanders</h5>
                                                        <p class="mb-0">Investment Planner</p>
                                                    </div>
                                                </div>
                                            </blockquote>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!-- /.item-inner -->
                            </div>
                            <!--/.swiper-slide -->
                            <div class="swiper-slide">
                                <div class="item-inner">
                                    <div class="card border-0 bg-white-900">
                                        <div class="card-body">
                                            <span class="ratings five mb-3"></span>
                                            <blockquote class="border-0 mb-0">
                                                <p>“Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.
                                                    Vestibulum id ligula porta felis euismod semper. Cras justo odio dapibus
                                                    facilisis sociis natoque penatibus.”</p>
                                                <div class="blockquote-details">
                                                    <div class="info p-0">
                                                        <h5 class="mb-0">Laura Widerski</h5>
                                                        <p class="mb-0">Sales Specialist</p>
                                                    </div>
                                                </div>
                                            </blockquote>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!-- /.item-inner -->
                            </div>
                            <!--/.swiper-slide -->
                        </div>
                        <!--/.swiper-wrapper -->
                    </div>
                    <!-- /.swiper -->
                </div>
                <!-- /.swiper-container -->
            </div>
            <!-- /.container -->
        </div>
        <!-- /.wrapper -->
    </section>
    <!-- /section -->
    </div>
@endsection
