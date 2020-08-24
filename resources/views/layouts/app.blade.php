<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Title -->
    <title>Better Spotify</title>

    <!-- Required Meta Tags Always Come First -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800,900&display=swap" rel="stylesheet">

    <!-- Theme CSS -->
    <link rel="stylesheet" href="{{ url('/fonts/font-awesome5.css') }}">
    <link rel="stylesheet" href="{{ url('/css/app.css') }}">


    <!-- CSS Customization -->
    <!--<link rel="stylesheet" href="/assets/css/custom.css">-->

</head>

<body>
    <main id="app">
        <div class="nav-main">
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <img id="navbar-spotify-icon" src="{{ url('/icons/Spotify_Icon_White.png') }}">
                    <a class="navbar-brand font-weight-bold" href="/">Better Spotify</a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <svg id="hamburger-menu-icon">
                            <use xlink:href="{{ url('/icons/svg-sprites.svg#menu') }}"></use>
                        </svg>
                    </button>
                    <div class="collapse navbar-collapse mt-3" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link px-2" href="/">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-2" href="/better_release_radar">Better Release Radar</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link px-2" href="/about">About</a>
                            </li>
                        </ul>

                        <ul class="navbar-nav ml-auto">

                            
                            <li class="nav-item">
                                <a class="nav-link px-2" href="/contact">Contact</a>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!--end:Nav -->
            </div>
        </div>
        <!--end:Navigation -->
        <!-- Header -->
        <header>
            
        </header>
        <!-- End Header -->

        <!-- View Contents -->
        @yield('content')
        <!-- End View Contents -->

        <!-- Footer -->
        <footer class="position-relative">
            <div class="container">
                <div class="row">
                    <div class="col-lg-auto ml-lg-auto mt-4">
                        <ul class="list-unstyled footer-link">
                            <!-- Please note.You are not allowed to remove credit link.Please respect that.-->
                            <li><a href="https://sharebootstrap.com">dev by sharebootstrap</a></li>
                            <li>
                                <div>Hamburger menu icon made by <a href="https://www.flaticon.com/authors/hirschwolf" title="hirschwolf">hirschwolf</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!--/.row-->
            </div>
            <!--/.container-->
        </footer>
        <!-- End Footer -->

    </main>  

</body>

<script src="{{ url('/js/app.js') }}"></script>

</html>