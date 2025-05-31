<header class="wrapper bg-gray">
  <nav class="navbar navbar-expand-lg extended extended-alt navbar-light navbar-bg-light">
    <div class="container flex-lg-column">
      <div class="topbar d-flex flex-row justify-content-lg-center align-items-center">
        <div class="navbar-brand"><a href="{{ url('/') }}"><img src="{{ asset('assets/home/img/logo-dark.png') }}" srcset="{{ asset('assets/home/img/logo-dark@2x.png') }} 2x" alt="" /></a></div>
      </div>
      <!-- /.d-flex -->
      <div class="navbar-collapse-wrapper bg-white d-flex flex-row align-items-center justify-content-between">
        <div class="navbar-other w-100 d-none d-lg-block">
          <nav class="nav social social-muted">
            <a href="#"><i class="uil uil-twitter"></i></a>
            <a href="#"><i class="uil uil-facebook-f"></i></a>
            <a href="#"><i class="uil uil-instagram"></i></a>
          </nav>
          <!-- /.social -->
        </div>
        <!-- /.navbar-other -->
        <div class="navbar-collapse offcanvas offcanvas-nav offcanvas-start">
          <div class="offcanvas-header d-lg-none">
            <h3 class="text-white fs-30 mb-0">Sandbox</h3>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body d-flex flex-column h-100">
            <ul class="navbar-nav">
              <li class="nav-item"><a class="nav-link scroll active" href="#home">Home</a></li>
              <li class="nav-item"><a class="nav-link scroll" href="#services">Shop</a></li>
              <li class="nav-item"><a class="nav-link scroll" href="">Collections</a></li>
              <li class="nav-item"><a class="nav-link scroll" href="#about">About</a></li>
              <li class="nav-item"><a class="nav-link scroll" href="#pricing">My Account</a></li>
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
        </div>
        <!-- /.navbar-collapse -->
        <div class="navbar-other w-100 d-flex">
          <ul class="navbar-nav flex-row align-items-center ms-auto">
            <li class="nav-item"><a class="nav-link" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-info"><i class="uil uil-info-circle"></i></a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-search"><i class="uil uil-search"></i></a></li>
            <li class="nav-item d-lg-none">
              <button class="hamburger offcanvas-nav-btn"><span></span></button>
            </li>
          </ul>
          <!-- /.navbar-nav -->
        </div>
        <!-- /.navbar-other -->
      </div>
      <!-- /.navbar-collapse-wrapper -->
    </div>
    <!-- /.container -->
  </nav>
  <!-- /.navbar -->
</header>
<!-- /header -->
