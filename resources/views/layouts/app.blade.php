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
    <div class="main-panel" id="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-transparent  bg-primary  navbar-absolute">
        <div class="container-fluid">
          @if($activePage != 'operator_dashboard')
          <div class="navbar-wrapper">
            <div class="navbar-toggle">
              <button type="button" class="navbar-toggler" disabled>
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </button>
            </div>
            <a class="navbar-brand" href="#">{{ $namePage }}</a>
          </div>
          @endif
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end" id="navigation">
            <ul class="navbar-nav">
              @if($activePage == 'main_dashboard')
              <li class="nav-item active">
                <a class="nav-link" href="#" id="view-schedule-btn">
                  <i class="now-ui-icons design_bullet-list-67"></i> Schedule
                  <p>
                    <span class="d-lg-none d-md-block">Schedule</span>
                  </p>
                </a>
              </li>
              <li class="nav-item active">
                <a class="nav-link text-center" href="#" id="select-scrap-to-process-btn" >
                  <i class="now-ui-icons ui-2_settings-90"></i> Process Scrap
                  <p hidden>
                    <span class="d-lg-none d-md-block">Process Scrap</span>
                  </p>
                </a>
              </li>
              <li class="nav-item active">
                <a class="nav-link text-center" href="#" id="scan-production-order-btn" >
                  <i class="now-ui-icons shopping_basket"></i> Scan Job Ticket
                  <p hidden>
                    <span class="d-lg-none d-md-block">Scan Job Ticket</span>
                  </p>
                </a>
              </li>
              @endif
              <li class="nav-item active" {{ !in_array($activePage, ['main_dashboard', 'painting_dashboard', 'painting_task']) ? 'hidden' : '' }}>
                <a class="nav-link" href="#" id="jt-search-btn">
                  <i class="now-ui-icons ui-1_zoom-bold"></i> Production Order Search
                  <p>
                    <span class="d-lg-none d-md-block">Production Order Search</span>
                  </p>
                </a>
              </li>
              @if($activePage == 'main_dashboard')
              <li class="nav-item active">
                <a class="nav-link text-center" href="#" id="quality-check-modal-btn" >
                  <i class="now-ui-icons ui-1_check"></i> Quality Check
                  <p hidden>
                    <span class="d-lg-none d-md-block">Quality Check</span>
                  </p>
                </a>
              </li>
              @endif
              @if(Auth::user())
              <li class="nav-item active">
                <a class="nav-link text-center" href="#" id="logout_click" style="width: 90px; height: 50px; font-size: 12pt;">
                  <i class="now-ui-icons media-1_button-power"></i>{{-- Logout --}}
                  <p hidden>
                    <span class="d-lg-none d-md-block">Logout</span>
                  </p>
                </a>
              </li>
              @endif
               @if($activePage == 'painting_dashboard')
              <li class="nav-item active">
                <a class="nav-link text-center" href="#" style="width: 90px; height: 50px; font-size: 12pt;" id="machine-power-btn">
                  <i class="now-ui-icons media-1_button-power"></i>
                  <p hidden>
                    <span class="d-lg-none d-md-block">Logout</span>
                  </p>
                </a>
              </li>
              @endif
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

  @include('modals.qc_rejects')
  @include('modals.qc_machine_setup_random')

  <style type="text/css">
    .numpad{
      font-weight: bolder;
      font-size: 18pt;
    }
  </style>

  <!--   Core JS Files   -->
  <script src="{{ asset('js/core/ajax.min.js') }}"></script> 
  <script src="{{ asset('js/core/jquery.min.js') }}"></script>
  {{-- <script src="{{ asset('js/core/popper.min.js') }}"></script> --}}
  <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
  <!-- Chart JS -->
  {{-- <script src="{{ asset('/js/plugins/chartjs.min.js') }}"></script> --}}
  <!--  Notifications Plugin    -->
  <script src="{{ asset('/js/plugins/bootstrap-notify.js') }}"></script>
  <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
  {{-- <script src="{{ asset('/js/now-ui-dashboard.min.js?v=1.3.0') }}" type="text/javascript"></script> --}}
  <!-- Now Ui Dashboard DEMO methods, don't include it in your project! -->
  {{-- <script src="{{ asset('/js/demo.js') }}"></script> --}}
  @yield('script')

</body>
</html>