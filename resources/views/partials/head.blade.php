<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="An impressive and flawless e-commerce">
  <meta name="keywords" content="bootstrap 5, business, corporate">
  <meta name="author" content="elemis">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Shoebaru - Ecommerce')</title>
  <link rel="shortcut icon" href="{{ asset('assets/home/img/nike/favicon.png') }}">

  <!-- CSS -->
  <link rel="stylesheet" href="{{ asset('assets/home/css/plugins.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/home/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/home/css/colors/yellow.css') }}">
  <link rel="preload" href="{{ asset('assets/home/css/fonts/urbanist.css') }}" as="style" onload="this.rel='stylesheet'">

  @stack('styles')
</head>
