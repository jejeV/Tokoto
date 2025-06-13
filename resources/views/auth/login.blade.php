<!DOCTYPE html>
<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('assets/admin/') }}/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Login | Shoebaru</title>

    <meta name="description" content="Halaman login admin e-commerce" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/home/img/nike/favicon.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/fonts/fontawesome.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/admin/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('assets/admin/vendor/js/helpers.js') }}"></script>

    <!-- Config -->
    <script src="{{ asset('assets/admin/js/config.js') }}"></script>
  </head>

  <body>
    <!-- Content -->
    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Login Card -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center">
                <a href="index.html" class="app-brand-link gap-2">
                  <!-- <span class="app-brand-text demo text-body fw-bolder">Shoebaru</span> -->
                  <img src="{{ asset('assets/home/img/nike/logo-dark.png') }}" srcset="{{ asset('assets/home/img/nike/logo-dark@2x.png') }} 2x" alt="" />
                </a>
              </div>
              <!-- /Logo -->

              <h4 class="mb-2">Selamat Datang! 👋</h4>
              <p class="mb-4">Silakan login untuk mengakses dashboard</p>

              @if($errors->any())
                <div class="alert alert-danger">
                  <ul class="mb-0">
                    @foreach($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input
                    type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    id="email"
                    name="email"
                    placeholder="Masukkan email Anda"
                    value="{{ old('email') }}"
                    autofocus
                    required
                  />
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3 form-password-toggle">
                  <div class="d-flex justify-content-between">
                    <label class="form-label" for="password">Password</label>
                    {{-- <a href="{{ route('password.request') }}"> --}}
                      <small>Lupa Password?</small>
                    {{-- </a> --}}
                  </div>
                  <div class="input-group input-group-merge">
                    <input
                      type="password"
                      id="password"
                      class="form-control @error('password') is-invalid @enderror"
                      name="password"
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                      aria-describedby="password"
                      required
                    />
                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    @error('password')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}/>
                    <label class="form-check-label" for="remember"> Ingat Saya </label>
                  </div>
                </div>

                <div class="mb-3">
                  <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
                </div>
                <div class="mb-3">
                    <a href="{{ route('login.google') }}" class="btn btn-outline-primary w-100">
                      Masuk dengan Google
                    </a>
                  </div>
              </form>

              <p class="text-center">
                <span>Belum punya akun?</span>
                <a href="{{ route('register') }}">
                  <span>Daftar disini</span>
                </a>
              </p>
            </div>
          </div>
          <!-- /Login Card -->
        </div>
      </div>
    </div>
    <!-- / Content -->

    <!-- Core JS -->
    <script src="{{ asset('assets/admin/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/admin/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/admin/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/admin/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/admin/vendor/js/menu.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/admin/js/main.js') }}"></script>

    <script>
      // Toggle password visibility
      document.querySelectorAll('.form-password-toggle i').forEach(icon => {
        icon.addEventListener('click', function() {
          const input = this.parentElement.querySelector('input');
          const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
          input.setAttribute('type', type);
          this.classList.toggle('bx-show');
          this.classList.toggle('bx-hide');
        });
      });
    </script>
  </body>
</html>
