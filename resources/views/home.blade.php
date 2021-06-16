<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="description" content="Your ultimate free stock trading journal for PSE market. Hero journals helps you find your mathematical trading edge or advantage and make you a stronger trader.">
    <meta name="keywords" content="stock,trading,journal">
    <meta name="author" content="Hero Journals">
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <title>{{ config('app.name', 'Laravel') }}</title> 
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,700" rel="stylesheet"> 
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/ico.png')}}">
</head>
<body> 
<div id="overlay"></div>
<video autoplay muted loop id="home-background-video" style="">
  <source src="{{ asset('videos/stock_market.mp4') }}" type="video/mp4">
</video>
<div id="content-wrapper" class="cover-container d-flex h-100 mx-auto flex-column">

      <header class="masthead mb-auto">
        <div class="inner container">
          <h3 class="masthead-brand"> 
            <img src="{{ asset('images/logo.png')}}"  width="180" style="position:relative"/> 
          </h3>
          <nav class="nav nav-masthead justify-content-center">
            <a class="nav-link active" href="#">Home</a> 
            <a class="nav-link" href="{{ url('about') }}">About</a>
            <a class="nav-link" href="{{ url('donate') }}">Donate</a>
            <a class="nav-link" href="{{ url('contact') }}">Contact</a>
            <a href="{{ url('login') }}" class="btn btn-default" id="login-btn" href="#">Login</a>
          </nav>
        </div>
      </header>
    
      <main role="main" class="inner cover container text-center">
        <h1 class="cover-heading">Track and Manage Your Trades</h1>
        <p class="lead">Hero journals is a free powerful tool that will boost your trading strategy and will aid you to become the best trader version of yourself.</p>
        <p class="lead">
          <a href="{{ url('/signup') }}" class="btn btn-lg btn-primary">Sign Up Now</a>
        </p>
      </main>

      <footer class="mastfoot mt-auto">
        <div class="inner text-center container">
          <p>&copy; 2021 Hero Journals, Developed by <a href="https://algermakiputin.dev">Alger Makiputin</a>.</p>
        </div>
      </footer>
    </div>
</body>
</html>

 