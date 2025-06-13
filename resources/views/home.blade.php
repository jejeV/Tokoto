@extends('layouts.app')

@section('title', 'Homepage')

@section('content')
    <section id="home">
      <div class="wrapper bg-gray overflow-hidden">
        <div class="container-fluid px-xl-0 pt-6 pb-10">
          <div class="swiper-container swiper-auto" data-margin="30" data-dots="true" data-nav="true" data-centered="true" data-loop="true" data-items-auto="true">
            <div class="swiper overflow-visible">
              <div class="swiper-wrapper">
                <div class="swiper-slide">
                  <figure class="rounded"><img src="{{ asset('assets/home/img/nike/cf1.png') }}" alt="" /><a class="item-link" href="{{ asset('assets/home/img/nike/AIR+JORDAN+1+RETRO+HIGH+OG SIDE.jpg') }}" data-glightbox data-gallery="gallery-group"><i class="uil uil-focus-add"></i></a></figure>
                </div>
                <!--/.swiper-slide -->
                <div class="swiper-slide">
                  <figure class="rounded"><img src="{{ asset('assets/home/img/nike/cf2.png') }}" alt="" /><a class="item-link" href="{{ asset('assets/home/img/photos/cf2.jpg') }}" data-glightbox data-gallery="gallery-group"><i class="uil uil-focus-add"></i></a></figure>
                </div>
                <!--/.swiper-slide -->
                <div class="swiper-slide">
                  <figure class="rounded"><img src="{{ asset('assets/home/img/nike/cf3.png') }}" alt="" /><a class="item-link" href="{{ asset('assets/home/img/photos/cf3.jpg') }}" data-glightbox data-gallery="gallery-group"><i class="uil uil-focus-add"></i></a></figure>
                </div>
                <!--/.swiper-slide -->
                <div class="swiper-slide">
                  <figure class="rounded"><img src="{{ asset('assets/home/img/nike/cf4.png') }}" alt="" /><a class="item-link" href="{{ asset('assets/home/img/photos/cf4.jpg') }}" data-glightbox data-gallery="gallery-group"><i class="uil uil-focus-add"></i></a></figure>
                </div>
                <!--/.swiper-slide -->
                <div class="swiper-slide">
                  <figure class="rounded"><img src="{{ asset('assets/home/img/nike/cf6.png') }}" alt="" /><a class="item-link" href="{{ asset('assets/home/img/photos/cf5.jpg') }}" data-glightbox data-gallery="gallery-group"><i class="uil uil-focus-add"></i></a></figure>
                </div>
                <!--/.swiper-slide -->
                <div class="swiper-slide">
                  <figure class="rounded"><img src="{{ asset('assets/home/img/nike/cf7.png') }}" alt="" /><a class="item-link" href="{{ asset('assets/home/img/photos/cf6.jpg') }}" data-glightbox data-gallery="gallery-group"><i class="uil uil-focus-add"></i></a></figure>
                </div>
                <!--/.swiper-slide -->
              </div>
              <!--/.swiper-wrapper -->
            </div>
            <!-- /.swiper -->
          </div>
          <!-- /.swiper-container -->
        </div>
        <!-- /.cotnainer -->
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
                      <a href="#"><img class="img-fluid" src="{{ asset('assets/home/img/nike/fs4-airmax.jpg') }}" alt="" /></a>
                      <figcaption>
                        <h5 class="from-top mb-0">View Gallery</h5>
                      </figcaption>
                    </figure>
                    <div class="card-body p-4">
                      <h3 class="h4 mb-0">Air Max</h3>
                    </div>
                    <!--/.card-body -->
                  </div>
                  <!-- /.card -->
                  <div class="card shadow-lg">
                    <figure class="card-img-top overlay overlay-1">
                      <a href="#"><img class="img-fluid" src="{{ asset('assets/home/img/nike/fs2-force.jpg') }}" alt="" /></a>
                      <figcaption>
                        <h5 class="from-top mb-0">View Gallery</h5>
                      </figcaption>
                    </figure>
                    <div class="card-body p-4">
                      <h3 class="h4 mb-0">Air Force</h3>
                    </div>
                    <!--/.card-body -->
                  </div>
                  <!-- /.card -->
                </div>
                <!-- /column -->
                <div class="col-md-6">
                  <div class="card shadow-lg mt-md-6 mb-6">
                    <figure class="card-img-top overlay overlay-1">
                      <a href="#"><img class="img-fluid" src="{{ asset('assets/home/img/nike/fs3-dunk.jpg') }}" alt="" /></a>
                      <figcaption>
                        <h5 class="from-top mb-0">View Gallery</h5>
                      </figcaption>
                    </figure>
                    <div class="card-body p-4">
                      <h3 class="h4 mb-0">Dunk</h3>
                    </div>
                    <!--/.card-body -->
                  </div>
                  <!-- /.card -->
                  <div class="card shadow-lg">
                    <figure class="card-img-top overlay overlay-1">
                      <a href="#"><img class="img-fluid" src="{{ asset('assets/home/img/nike/fs1-jordan.jpg') }}" alt="" /></a>
                      <figcaption>
                        <h5 class="from-top mb-0">View Gallery</h5>
                      </figcaption>
                    </figure>
                    <div class="card-body p-4">
                      <h3 class="h4 mb-0">Air Jordan</h3>
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
              <h2 class="display-5 mb-3">Our Collections</h2>
              <p class="lead fs-lg">Koleksi sepatu eksklusif untuk kamu yang ingin tampil lebih stylish dan percaya diri.</p>
              <p>Mulai dari Air Max, Air Force, hingga berbagai model terbatas lainnya, kami hadirkan pilihan sepatu terbaik dengan desain ikonik dan material berkualitas. Lengkapi gaya harianmu dengan koleksi terbaru yang hanya tersedia di sini.</p>
              <a href="{{ route('collections') }}" class="btn btn-primary rounded-pill mt-2">Shop Now</a>
            </div>
            <!-- /column -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container -->
      </div>
      <!-- /.wrapper -->
    </section>
    <!-- /section -->
    <div class="wrapper image-wrapper bg-image bg-overlay text-white" data-image-src="{{ asset('assets/home/img/nike/wp1.jpg') }}">
      <div class="container py-16 py-md-19 text-center">
        <h2 class="display-1 text-white mb-0">Discover Our Exclusive <br class="d-none d-md-block"> Sneaker Collection</h2>
      </div>
      <!-- /.container -->
    </div>
    <!-- /.wrapper -->
    <section id="portfolio">
      <div class="wrapper bg-gray">
        <div class="container py-15 py-md-17 text-center">
          <div class="row">
            <div class="col-lg-10 col-xl-8 col-xxl-7 mx-auto mb-8">
              <h2 class="display-5 mb-3">Sneaker Lookbook</h2>
              <p class="lead fs-lg">Dapatkan inspirasi gaya lewat deretan foto sneakers favorit. Dari klasik hingga edisi terbatas!</p>
            </div>
            <!-- /column -->
          </div>
          <!-- /.row -->
          <div class="grid grid-view projects-masonry">
            <div class="isotope-filter filter mb-10">
              <ul>
                <li><a class="filter-item active" data-filter="*">All</a></li>
                <li><a class="filter-item" data-filter=".foods">Men</a></li>
                <li><a class="filter-item" data-filter=".drinks">Women</a></li>
                <li><a class="filter-item" data-filter=".events">Best Seller</a></li>
                <li><a class="filter-item" data-filter=".pastries">New Drop</a></li>
              </ul>
            </div>
            <div class="row gx-md-6 gy-6 isotope">
              <div class="project item col-md-6 col-xl-4 drinks events">
                <figure class="overlay overlay-1 rounded"><a href="{{ asset('assets/home/img/nike/gallery-AMD8.jpg') }}" data-glightbox data-gallery="shots-group"> <img src="{{ asset('assets/home/img/nike/gallery-AMD8.jpg') }}" alt="" /></a>
                  <figcaption>
                    <h5 class="from-top mb-0">Air Max Dn8</h5>
                  </figcaption>
                </figure>
              </div>
              <!-- /.project -->
              <div class="project item col-md-6 col-xl-4 events">
                <figure class="overlay overlay-1 rounded"><a href="{{ asset('assets/home/img/nike/pf2-full.jpg') }}" data-glightbox data-gallery="shots-group"> <img src="{{ asset('assets/home/img/nike/pf2.jpg') }}" alt="" /></a>
                  <figcaption>
                    <h5 class="from-top mb-0">Dunk Low Next Nature</h5>
                  </figcaption>
                </figure>
              </div>
              <!-- /.project -->
              <div class="project item col-md-6 col-xl-4 drinks foods">
                <figure class="overlay overlay-1 rounded"><a href="{{ asset('assets/home/img/nike/pf3-full.jpg') }}" data-glightbox data-gallery="shots-group"> <img src="{{ asset('assets/home/img/nike/pf3.jpg') }}" alt="" /></a>
                  <figcaption>
                    <h5 class="from-top mb-0">Air Jordan 1 Low OG</h5>
                  </figcaption>
                </figure>
              </div>
              <!-- /.project -->
              <div class="project item col-md-6 col-xl-4 events">
                <figure class="overlay overlay-1 rounded"><a href="{{ asset('assets/home/img/nike/pf4-full.jpg') }}" data-glightbox data-gallery="shots-group"> <img src="{{ asset('assets/home/img/nike/pf4.jpg') }}" alt="" /></a>
                  <figcaption>
                    <h5 class="from-top mb-0">Air Force 1 '07 EasyOn</h5>
                  </figcaption>
                </figure>
              </div>
              <!-- /.project -->
              <div class="project item col-md-6 col-xl-4 pastries events">
                <figure class="overlay overlay-1 rounded"><a href="{{ asset('assets/home/img/nike/gallery-AM1.jpg') }}" data-glightbox data-gallery="shots-group"> <img src="{{ asset('assets/home/img/nike/gallery-AM1-p.jpg') }}" alt="" /></a>
                  <figcaption>
                    <h5 class="from-top mb-0">Air Max 1</h5>
                  </figcaption>
                </figure>
              </div>
              <!-- /.project -->
              <div class="project item col-md-6 col-xl-4 foods">
                <figure class="overlay overlay-1 rounded"><a href="{{ asset('assets/home/img/nike/pf6-full.jpg') }}" data-glightbox data-gallery="shots-group"> <img src="{{ asset('assets/home/img/nike/pf6.jpg') }}" alt="" /></a>
                  <figcaption>
                    <h5 class="from-top mb-0">Dunk Low Retro 'Berlin'</h5>
                  </figcaption>
                </figure>
              </div>
              <!-- /.project -->
              <div class="project item col-md-6 col-xl-4 foods drinks">
                <figure class="overlay overlay-1 rounded"><a href="{{ asset('assets/home/img/nike/pf7-full.jpg') }}" data-glightbox data-gallery="shots-group"> <img src="{{ asset('assets/home/img/nike/pf7.jpg') }}" alt="" /></a>
                  <figcaption>
                    <h5 class="from-top mb-0">Dunk Low</h5>
                  </figcaption>
                </figure>
              </div>
              <!-- /.project -->
              <div class="project item col-md-6 col-xl-4 pastries">
                <figure class="overlay overlay-1 rounded"><a href="{{ asset('assets/home/img/nike/pf8-full.jpg') }}" data-glightbox data-gallery="shots-group"> <img src="{{ asset('assets/home/img/nike/pf8.jpg') }}" alt="" /></a>
                  <figcaption>
                    <h5 class="from-top mb-0">Air Jordan 1 Low SE</h5>
                  </figcaption>
                </figure>
              </div>
              <!-- /.project -->
              <div class="project item col-md-6 col-xl-4 events">
                <figure class="overlay overlay-1 rounded"><a href="{{ asset('assets/home/img/nike/pf9-full.jpg') }}" data-glightbox data-gallery="shots-group"> <img src="{{ asset('assets/home/img/nike/pf9.jpg') }}" alt="" /></a>
                  <figcaption>
                    <h5 class="from-top mb-0">Dunk Low Next Nature</h5>
                  </figcaption>
                </figure>
              </div>
              <!-- /.project -->
              <div class="project item col-md-6 col-xl-4 foods">
                <figure class="overlay overlay-1 rounded"><a href="{{ asset('assets/home/img/nike/gallery-AMdawn.jpg') }}" data-glightbox data-gallery="shots-group"> <img src="{{ asset('assets/home/img/nike/gallery-AMdawn-p.jpg') }}" alt="" /></a>
                  <figcaption>
                    <h5 class="from-top mb-0">Air Max Dawn</h5>
                  </figcaption>
                </figure>
              </div>
              <!-- /.project -->
              <div class="project item col-md-6 col-xl-4 drinks">
                <figure class="overlay overlay-1 rounded"><a href="{{ asset('assets/home/img/nike/pf11-full.jpg') }}" data-glightbox data-gallery="shots-group"> <img src="{{ asset('assets/home/img/nike/pf11.jpg') }}" alt="" /></a>
                  <figcaption>
                    <h5 class="from-top mb-0">Air Force 1 LE</h5>
                  </figcaption>
                </figure>
              </div>
              <!-- /.project -->
              <div class="project item col-md-6 col-xl-4 foods">
                <figure class="overlay overlay-1 rounded"><a href="{{ asset('assets/home/img/nike/pf12-full.jpg') }}" data-glightbox data-gallery="shots-group"> <img src="{{ asset('assets/home/img/nike/pf12.jpg') }}" alt="" /></a>
                  <figcaption>
                    <h5 class="from-top mb-0">Jordan 1 Low Alt</h5>
                  </figcaption>
                </figure>
              </div>
              <!-- /.project -->
              <div class="project item col-md-6 col-xl-4 drinks">
                <figure class="overlay overlay-1 rounded"><a href="{{ asset('assets/home/img/nike/gallery-AM90.jpg') }}" data-glightbox data-gallery="shots-group"> <img src="{{ asset('assets/home/img/nike/gallery-AM90-p.jpg') }}" alt="" /></a>
                  <figcaption>
                    <h5 class="from-top mb-0">Nike Air Max 90 G</h5>
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
  </div>
  @endsection
