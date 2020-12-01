<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="perfect-scrollbar-on">

<head>
  <meta charset="utf-8" />
  {{-- <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png"> --}}
  {{-- <link rel="icon" type="image/png" href="../assets/img/favicon.png"> --}}
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
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
      @yield('content')
    </div>
  </div>

  {{--  <!--   Core JS Files   -->  --}}
  <script src="{{ asset('js/core/ajax.min.js') }}"></script> 
  <script src="{{ asset('js/core/jquery.min.js') }}"></script>
  <script src="{{ asset('js/core/popper.min.js') }}"></script>
  <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
  {{--  <script src="{{ asset('js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>  --}}
  {{--  <!-- Chart JS -->  --}}
  <script src="{{ asset('/js/plugins/chartjs.min.js') }}"></script>
  {{--  <!--  Notifications Plugin    -->  --}}
  <script src="{{ asset('/js/plugins/bootstrap-notify.js') }}"></script>
  {{--  <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->  --}}
  {{--  <script src="{{ asset('/js/now-ui-dashboard.min.js?v=1.3.0') }}" type="text/javascript"></script>  --}}
  {{--  <!-- Now Ui Dashboard DEMO methods, don't include it in your project! -->  --}}
  <script src="{{ asset('/js/demo.js') }}"></script>
  <script src="{{ asset('js/jquery-ui.js') }}"></script>
  @yield('script')

</body>
</html>