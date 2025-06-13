<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="An impressive and flawless site template that includes various UI elements and countless features, attractive ready-made blocks and rich pages, basically everything you need to create a unique and professional website.">
  <meta name="keywords" content="bootstrap 5, business, corporate, creative, gulp, marketing, minimal, modern, multipurpose, one page, responsive, saas, sass, seo, startup, html5 template, site template">
  <meta name="author" content="elemis">
  <title>Shoebaru | E-commerce</title>
  <link rel="shortcut icon" href="{{ asset('assets/home/img/nike/favicon.png') }}">
  <link rel="stylesheet" href="{{ asset('assets/home/css/plugins.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/home/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/home/css/colors/yellow.css') }}">
  <link rel="preload" href="{{ asset('assets/home/css/fonts/urbanist.css') }}" as="style" onload="this.rel='stylesheet'">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @yield('styles')
</head>

<body class="onepage">
  <div class="content-wrapper">
    @include('partials.header')

    @yield('content')
  </div>
  @include('partials.footer')

  <div class="progress-wrap">
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
      <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
    </svg>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="{{ asset('assets/home/js/plugins.js') }}"></script>
  <script src="{{ asset('assets/home/js/theme.js') }}"></script>
  @stack('scripts')
</body>

</html>
