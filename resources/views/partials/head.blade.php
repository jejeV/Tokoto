<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="An impressive and flawless site template">
  <meta name="keywords" content="bootstrap 5, business, corporate">
  <meta name="author" content="elemis">
  <title>@yield('title', 'Shoes Baru - Team Four')</title>
  <link rel="shortcut icon" href="{{ asset('assets/home/img/favicon.png') }}">

  <!-- CSS -->
  <link rel="stylesheet" href="{{ asset('assets/home/css/plugins.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/home/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/home/css/colors/yellow.css') }}">
  <link rel="preload" href="{{ asset('assets/home/css/fonts/urbanist.css') }}" as="style" onload="this.rel='stylesheet'">

  @stack('styles')
</head>
