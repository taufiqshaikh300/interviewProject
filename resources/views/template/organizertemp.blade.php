<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>@stack('title')</title>
  </head>
  <style>
    /* Gradient Navbar */
.navbar {
    background: linear-gradient(135deg, #00aaff, #4b6cb7);
}

/* Navbar Links Hover Effect */
.navbar-nav .nav-link:hover {
    color: #fff !important;
    text-decoration: underline;
}

/* Active Link Highlight */
.navbar-nav .nav-link.active {
    color: #ffcc00 !important;
    font-weight: bold;
}

/* Toggler Icon (Hamburger) */
.navbar-toggler-icon {
    background-color: #fff;
}

  </style>
  <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
      <div class="container-fluid">
          <!-- Brand and Toggle Button -->
          <a class="navbar-brand text-primary font-weight-bold text-light" href="{{route('organizer.dashboard')}}">
            DASHBOARD
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
          </button>
  
          <!-- Navbar Links -->
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                  <li class="nav-item">
                      <a class="nav-link" href="{{route('organizer.allPublicEvents')}}">All Events</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="{{route('organizer.allEvents')}}">My Events</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="{{route('organizer.addEvent')}}">Add Events</a>
                  </li>
              </ul>
  
              <!-- Logout Button -->
              <form action="{{ route('logout') }}" method="POST" class="d-flex ms-3">
                  @csrf
                  <button class="btn btn-outline-danger btn-sm rounded-pill" type="submit">
                      <i class="bi bi-box-arrow-right"></i> Logout
                  </button>
              </form>
          </div>
      </div>
  </nav>
  
        @yield('content')
 

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    @yield('script')
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>
