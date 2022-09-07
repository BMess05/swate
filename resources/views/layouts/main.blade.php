<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Swate Admin Panel"> 
  <title>Swate</title>
  <!-- Favicon -->
  <link rel="icon" href="{{asset('assets/img/Swate-Fav-Icon.png')}}" type="image/png">
  <!-- Fonts -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
  <!-- Icons -->
  
  <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">

  <link rel="stylesheet" href="{{asset('assets/vendor/nucleo/css/nucleo.css')}}" type="text/css">
  <link rel="stylesheet" href="{{asset('assets/vendor/@fortawesome/fontawesome-free/css/all.min.css')}}" type="text/css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <!-- Page plugins -->
  <!-- Argon CSS -->
  <link rel="stylesheet" href="{{asset('assets/css/argon.css?v=1.2.0')}}" type="text/css">
  <link rel="stylesheet" href="{{asset('assets/vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" type="text/css">

  <link rel="stylesheet" href="{{asset('assets/css/bootstrap-timepicker.min.css')}}" type="text/css">
</head>

<body>
  <!-- Sidenav -->
<nav class="bg-green-grediant sidenav navbar navbar-vertical  fixed-left  navbar-expand-xs navbar-light bg-white" id="sidenav-main">
    <div class="scrollbar-inner">
        <!-- Brand -->
        <div class="sidenav-header  align-items-center">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
              <img src="{{asset('assets/img/logo_white.png')}}" class="navbar-brand-img" alt="...">
            </a>
        </div>
        <div class="navbar-inner">
            <!-- Collapse -->
            <div class="collapse navbar-collapse" id="sidenav-collapse-main">
                <!-- Nav items -->
                <ul class="navbar-nav"> 
                    <li class="nav-item">
                    <a class="nav-link" href="{{url('/')}}">
                        <i class="ni ni-tv-2 text-white"></i>
                        <span class="nav-link-text text-white">Dashboard</span>
                    </a>
                    </li> 
                    <li class="nav-item">
                    <a class="nav-link" href="{{url('users')}}">
                        <i class="ni ni-single-02 text-white"></i>
                        <span class="nav-link-text text-white">Users</span>
                    </a>
                    </li> 

                     <li class="nav-item">
                    <a class="nav-link" href="{{ route('listCategories') }}">
                         <img src="{{asset('assets/img/icons/ingredients-categories.svg')}}" class="main-images">
                        <span class="main-span nav-link-text text-white">Ingredient Categories</span>
                    </a>
                    </li>
                    
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('listItems') }}">
                          <img src="{{asset('assets/img/icons/ingredients.svg')}}" class="main-images">
                          <span class="main-span nav-link-text text-white">Ingredients</span>
                      </a>
                    </li>

                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('listDietCategories') }}">
                          <img src="{{asset('assets/img/icons/diet.svg')}}" class="main-images">
                          <span class="main-span nav-link-text text-white">Diet Categories</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('listCuisineTypes') }}">
                          <img src="{{asset('assets/img/icons/cusine.svg')}}" class="main-images">
                          <span class="main-span nav-link-text text-white">Cuisine Types</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('listDishTypes') }}">
                          <img src="{{asset('assets/img/icons/dish.svg')}}" class="main-images">
                          <span class="main-span nav-link-text text-white">Dish Types</span>
                      </a>
                    </li>

                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('listReceipes') }}">
                         <img src="{{asset('assets/img/icons/recipie.svg')}}" class="main-images">
                          <span class="main-span nav-link-text text-white">Recipe</span>
                      </a>
                    </li> 
                    
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('listTimeFilters') }}">
                          <img src="{{asset('assets/img/icons/time-filter.svg')}}" class="main-images">
                          <span class="main-span nav-link-text text-white">Time Filters</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('listTags') }}">
                          <img src="{{asset('assets/img/icons/tags.png')}}" class="main-images">
                          <span class="main-span nav-link-text text-white">Tags</span>
                      </a>
                    </li>

                    <!--  <li class="nav-item">
                      <a class="nav-link" href="{{ route('listUnits') }}">
                          <i class="ni ni-bullet-list-67 text-white"></i>
                          <span class="nav-link-text text-white">Units</span>
                      </a>
                    </li> -->

                     <li class="nav-item">
                      <a class="nav-link" href="{{ route('listFaqs') }}">
                          <i class="fas fa-question-circle text-white"></i>
                          <span class="nav-link-text text-white">FAQs<span>
                      </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<!-- Main content -->
<div class="main-content" id="panel">
  <!-- Topnav -->
  @include('layouts.top_nav')
  <!-- Header -->
  <!-- Header -->
  @yield('content')
</div>
  <!-- Argon Scripts -->
  <!-- Core -->
  <script src="{{asset('assets/vendor/jquery/dist/jquery.min.js')}}"></script>
  <script src="{{asset('assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('assets/vendor/js-cookie/js.cookie.js')}}"></script>
  <script src="{{asset('assets/vendor/jquery.scrollbar/jquery.scrollbar.min.js')}}"></script>
  <script src="{{asset('assets/vendor/jquery-scroll-lock/dist/jquery-scrollLock.min.js')}}"></script>
  <!-- Optional JS -->
  <script src="{{asset('assets/vendor/chart.js/dist/Chart.min.js')}}"></script>
  <script src="{{asset('assets/vendor/chart.js/dist/Chart.extension.js')}}"></script>
  <!-- Argon JS -->
  <script src="{{asset('assets/js/argon.js?v=1.2.0')}}"></script>
  <script src="{{asset('assets/js/bootstrap-timepicker.min.js')}}"></script>

  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <!-- Page level plugins -->
  <script src="{{ asset('assets/vendor/datatables.net/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
  
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script src="{{asset('assets/js/custom.js')}}"></script>
  <script>
    $(document).ready(function() {

    //   $(document).ready(function() {
    //       $('#dataTable').DataTable();
    //   });
      setTimeout(function() {
        $('.alert').remove();
      }, 3000);
    });
  </script>
  <script src="https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js"></script>
  @yield('script')
</body>

</html>
