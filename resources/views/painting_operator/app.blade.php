<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="perfect-scrollbar-on">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="apple-mobile-web-app-capable" content="yes">
  <title>MES</title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  {{--  <!--     Fonts and icons     -->  --}}
  <link href="{{ asset('/css/fontsgoogleapis.css') }}" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('/css/all.css') }}">
  {{--  <!-- CSS Files -->  --}}
  <link href="{{ asset('/css/bootstrap.css') }}" rel="stylesheet" />
  <link href="{{ asset('/css/bootstrap.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('/css/now-ui-dashboard.css?v=1.3.0') }}" rel="stylesheet" />
  {{--  <!-- CSS Just for demo purpose, don't include it in your project -->  --}}
  <link href="{{ asset('/css/demo.css') }}" rel="stylesheet" />
</head>

<body class="">
  <div class="wrapper">
    <div class="sidebar" data-color="orange">
      <!--
        Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red | yellow"
    -->
      <div class="logo">
        {{-- <a href="http://www.creative-tim.com" class="simple-text logo-mini">
          CT
        </a> --}}
        <a href="#" class="simple-text logo-normal text-center">
          {{ $namePage }} - Painting
        </a>
      </div>
      <div class="sidebar-wrapper" id="sidebar-wrapper">
        <ul class="nav">
          
        </ul>
      </div>
    </div>
   
    <div class="main-panel" id="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-transparent  bg-primary  navbar-absolute">
        <div class="container-fluid">
          
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end" id="navigation">
            <ul class="navbar-nav">
              @if(!in_array($activePage, ['painting_task']))
              
              <li class="nav-item active">
                <a class="nav-link text-center" href="#" id="quality-check-modal-btn" >
                  <i class="now-ui-icons ui-1_check"></i> Quality Check
                  <p hidden>
                    <span class="d-lg-none d-md-block">Quality Check</span>
                  </p>
                </a>
              </li>
              <li class="nav-item active">
                <a class="nav-link" href="#" id="jt-search-btn">
                  <i class="now-ui-icons ui-1_zoom-bold"></i> Production Order Search
                  <p>
                    <span class="d-lg-none d-md-block">Production Order Search</span>
                  </p>
                </a>
              </li>
              @endif
              <li class="nav-item active">
                <a class="nav-link" href="#" id="view-painting-schedule-btn">
                  <i class="now-ui-icons design_bullet-list-67"></i>Schedule
                  <p>
                    <span class="d-lg-none d-md-block">Schedule</span>
                  </p>
                </a>
              </li>
              @if($activePage == 'painting_task')
              <li class="nav-item active">
                <a class="nav-link text-center" href="#" style="font-size: 11pt;" id="logout_click">
                  {{--  <i class="now-ui-icons media-1_button-power"></i>  --}}
                  Logout
                  {{--  <img src="{{ asset('img/sign-out.png') }}" style="width: 70%;">  --}}
                  <p hidden>
                    <span class="d-lg-none d-md-block">Logout</span>
                  </p>
                </a>
              </li>
              @endif
              <li class="nav-item dropdown active">
                <a href="#" class="nav-link dropdown-toggle" onclick="return false;" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="now-ui-icons education_atom"></i>
                  <p>
                    <span class="d-lg-none d-md-block">Some Actions</span>
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                  <a class="dropdown-item"  id="view-chemical-records-btn" href="#">Painting Chemical Records</a>
                  <a class="dropdown-item"  id="view-water-Monitoring-btn" href="#">Water Discharged Monitoring</a>
                  <a class="dropdown-item"  id="view-powder-Monitoring-btn" href="#">Powder Coating Monitoring</a>

                </div>
              </li>
              <li class="nav-item active">
                <a class="nav-link text-center" href="#" id="refresh_page_id" onClick="document.location.reload(true)">
                  <i class="now-ui-icons arrows-1_refresh-69"></i> 
                  <p hidden>
                    <span class="d-lg-none d-md-block"></span>
                  </p>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
      @yield('content')
      <footer class="footer">
        <div class="container-fluid">
          <div class="copyright" id="copyright">
            &copy;
            <script>
              document.getElementById('copyright').appendChild(document.createTextNode(new Date().getFullYear()))
            </script>
            <a href="#" target="_blank">FUMACO IT Dev. Team</a>
          </div>
        </div>
      </footer>
    </div>
  </div>

<style type="text/css">
  .numpad{
    font-weight: bolder;
    font-size: 18pt;
  }
</style>

  <!--   Core JS Files   -->
  <script src="{{ asset('js/core/ajax.min.js') }}"></script> 
  <script src="{{ asset('js/core/jquery.min.js') }}"></script>
  <script src="{{ asset('js/core/popper.min.js') }}"></script>
  <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
  <!-- Chart JS -->
  {{-- <script src="{{ asset('/js/plugins/chartjs.min.js') }}"></script> --}}
  <!--  Notifications Plugin    -->
  <script src="{{ asset('/js/plugins/bootstrap-notify.js') }}"></script>
  <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{ asset('/js/now-ui-dashboard.min.js?v=1.3.0') }}" type="text/javascript"></script>
  <!-- Now Ui Dashboard DEMO methods, don't include it in your project! -->
  {{-- <script src="{{ asset('/js/demo.js') }}"></script> --}}
  @yield('script')
</body>
</html>
