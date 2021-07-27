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
    <div class="sidebar" data-color="orange">
     <!--
       Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red | yellow"
   -->
     <div class="logo">
       {{-- <a href="http://www.creative-tim.com" class="simple-text logo-mini">
         CT
       </a> --}}
       <a href="#" class="simple-text logo-normal text-center">
         {{--  {{ $namePage }}  --}}
         MES
       </a>
     </div>
     <div class="sidebar-wrapper" id="sidebar-wrapper">
       <ul class="nav">
         @isset($permissions)
           @php
           $a = array_intersect($permissions['permitted_modules'], ['Production']);
           $b = array_intersect($permissions['permitted_modules'], ['Quality Assurance']);
           @endphp
           @if (count($b) > 0)
           <li class="{{ $activePage == 'qa_dashboard' ? 'active' : '' }}">
             <a href="/main_dashboard">
               <i class="now-ui-icons business_chart-bar-32"></i>
               <p>QA Dashboard</p>
             </a>
           </li>
           @endif
           @if (count($a) > 0)
           <li class="{{ $activePage == 'main_dashboard' ? 'active' : '' }}">
             <a href="/main_dashboard">
               <i class="now-ui-icons business_chart-bar-32"></i>
               <p>Dashboard</p>
             </a>
           </li>
           <li class="{{ $activePage == 'item_feedback' ? 'active' : '' }}">
             <a href="/item_feedback">
               <i class="now-ui-icons education_atom"></i>
               <p>Production Order(s)</p>
             </a>
           </li>
           <li class="{{ $activePage == 'inventory' ? 'active' : '' }}">
             <a href="/inventory">
               <i class="now-ui-icons files_box"></i>
               <p>Inventory</p>
             </a>
           </li>
           <li class="{{ $activePage == 'production_planning' ? 'active' : '' }}">
             <a href="/wizard">
               <i class="now-ui-icons files_paper"></i>
               <p>Production Planning</p>
             </a>
           </li>
           <li class="{{ $activePage == 'production_planning_assembly' ? 'active' : '' }}">
             <a href="/assembly/wizard">
               <i class="now-ui-icons files_paper"></i>
               <p>Assembly Planning Wizard</p>
             </a>
           </li>
           <li class="{{ $activePage == 'production_schedule_assembly' ? 'active' : '' }}">
             <a href="/production_schedule/3">
               <i class="now-ui-icons files_paper"></i>
               <p>Production Schedule Assembly</p>
             </a>
           </li>
           <li class="{{ $activePage == 'production_schedule' ? 'active' : '' }}">
             <a href="/production_schedule/1">
               <i class="now-ui-icons ui-1_calendar-60"></i>
               <p>Production Schedule</p>
             </a>
           </li>
           @if(in_array('Painting', $permissions['permitted_operations']))
           <li class="{{ $activePage == 'production_schedule_painting' ? 'active' : '' }}">
             <a href="/production_schedule/0">
               <i class="now-ui-icons ui-1_calendar-60"></i>
               <p>Painting Schedule</p>
             </a>
           </li>
           @endif
           <li>
             <a href="#">
               <i class="now-ui-icons users_single-02"></i>
               <p>Resources</p>
             </a>
           </li>
           <li>
             <a href="#">
               <i class="now-ui-icons ui-2_settings-90"></i>
               <p>Maintenance Requests</p>
             </a>
           </li>
           <li>
             <a href="#">
               <i class="now-ui-icons location_map-big"></i>
               <p>Quality Inspection Logs</p>
             </a>
           </li>
           @endif
         @endisset
         <li>
           <a href="/report/production_schedule_report">
             <i class="now-ui-icons files_single-copy-04"></i>
             <p>Reports</p>
           </a>
         </li>
         <li class="{{ $activePage == 'settings_module' ? 'active' : '' }}">
           <a href="/settings_module">
             <i class="now-ui-icons ui-1_settings-gear-63"></i>
             <p>Settings</p>
           </a>
         </li>
       </ul>

   </div>
 </div>

  <div class="main-panel" id="main-panel">
    <!-- Navbar -->
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
    .qc_passed{
      background-image: url("{{ asset('img/chk.png') }}");
      background-size: 28%;
      background-repeat: no-repeat;
      background-position: center; 
    }
  
    .qc_failed{
      background-image: url("{{ asset('img/x.png') }}");
      background-size: 20%;
      background-repeat: no-repeat;
      background-position: center; 
    }

    .active-process {
      background-color: #FFC107;
      color: #000000;
      animation: blinkingBackground 2.5s linear infinite;
    }

  @keyframes blinkingBackground{
    0%    { background-color: #ffffff;}
    25%   { background-color: #FFC107;}
    50%   { background-color: #ffffff;}
    75%   { background-color: #FFC107;}
    100%  { background-color: #ffffff;}
  }
  
  .breadcrumb-c {
    font-size: 8pt;
    font-weight: bold;
    padding: 0;
    background: transparent;
    list-style: none;
    overflow: hidden;
    margin-top: 3px;
    margin-bottom: 3px;
    width: 100%;
    border-radius: 4px;
  }

  .breadcrumb-c>li {
    display: table-cell;
    vertical-align: top;
    width: 0.8%;
  }

  .breadcrumb-c>li+li:before {
    padding: 0;
  }

  .breadcrumb-c li a {
    color: white;
    text-decoration: none;
    padding: 10px 0 10px 5px;
    position: relative;
    display: inline-block;
    width: calc( 100% - 10px );
    background-color: hsla(0, 0%, 83%, 1);
    text-align: center;
    text-transform: capitalize;
  }

  .breadcrumb-c li.completed a {
    background: brown;
    background: hsla(153, 57%, 51%, 1);
  }

  .breadcrumb-c li.completed a:after {
    border-left: 30px solid hsla(153, 57%, 51%, 1);
  }

  .breadcrumb-c li.active a {
    background: #ffc107;
  }

  .breadcrumb-c li.active a:after {
    border-left: 30px solid #ffc107;
  }

  .breadcrumb-c li:first-child a {
    padding-left: 1px;
  }

  .breadcrumb-c li:last-of-type a {
    width: calc( 100% - 38px );
  }

  .breadcrumb-c li a:before {
    content: " ";
    display: block;
    width: 0;
    height: 0;
    border-top: 50px solid transparent;
    border-bottom: 50px solid transparent;
    border-left: 30px solid white;
    position: absolute;
    top: 50%;
    margin-top: -50px;
    margin-left: 1px;
    left: 100%;
    z-index: 1;
  }

  .breadcrumb-c li a:after {
    content: " ";
    display: block;
    width: 0;
    height: 0;
    border-top: 50px solid transparent;
    border-bottom: 50px solid transparent;
    border-left: 30px solid hsla(0, 0%, 83%, 1);
    position: absolute;
    top: 50%;
    margin-top: -50px;
    left: 100%;
    z-index: 2;
  }

  .truncate {
    white-space: nowrap;
    /*overflow: hidden;*/
    text-overflow: ellipsis;
  }

  .scrolltbody tbody {
    display:block;
    height:300px;
    overflow:auto;
  }
  .scrolltbody thead, .scrolltbody tbody tr {
      display:table;
      width:100%;
      table-layout:fixed;
  }
  .scrolltbody thead {
      width: calc(100%)
  }
    .numpad-div .row1{
    -webkit-user-select: none; /* Chrome/Safari */        
    -moz-user-select: none; /* Firefox */
    -ms-user-select: none; /* IE10+ */
    /* Not implemented yet */
    -o-user-select: none;
    user-select: none;   
  }

  .numpad{
    display: inline-block;
    border: 1px solid #333;
    border-radius: 5px;
    text-align: center;
    width: 27%;
    height: 27%;
    line-height: 60px;
    margin: 3px;
    font-size: 15pt;
    color: inherit;
    background: rgba(255, 255, 255, 0.7);
    transition: all 0.3s ease-in-out;
  }

  .numpad:active,
  .numpad:hover {
    cursor: pointer ;
    box-shadow: inset 0 0 2px #000000;
  }

  .modal-ste .form-control {
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }
  .modal-ste .form-control:hover, .modal-ste .form-control:focus, .modal-ste .form-control:active {
    box-shadow: none;
  }
  .modal-ste .form-control:focus {
    border: 1px solid #34495e;
  }

  .modal { overflow: auto !important; }
  #reschedule-delivery-modal .form-control {
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }
  </style>


    
  {{--  <!--   Core JS Files   -->  --}}
  <script src="{{ asset('js/core/ajax.min.js') }}"></script> 
  <script src="{{ asset('js/core/jquery.min.js') }}"></script>
  <script src="{{ asset('js/core/popper.min.js') }}"></script>
  <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
  {{--  <!-- Chart JS -->  --}}
  <script src="{{ asset('/js/plugins/chartjs.min.js') }}"></script>
  {{--  <!--  Notifications Plugin    -->  --}}
  <script src="{{ asset('/js/plugins/bootstrap-notify.js') }}"></script>
  {{--  <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->  --}}
  <script src="{{ asset('/js/now-ui-dashboard.min.js?v=1.3.0') }}" type="text/javascript"></script>
  {{--  <!-- Now Ui Dashboard DEMO methods, dont include it in your project! -->  --}}
  <script src="{{ asset('/js/demo.js') }}"></script>
  <script src="{{ asset('js/jquery-ui.js') }}"></script>

  <script src="{{ asset('/js/ekko-lightbox/ekko-lightbox.min.js') }}"></script>
  @yield('script')
  <script src="{{ asset('/js/jquery.rfid.js') }}"></script>
<script>
   $(document).ready(function(){

    function showNotification(color, message, icon){
      $.notify({
        icon: icon,
        message: message
      },{
        type: color,
        timer: 500,
        placement: {
          from: 'top',
          align: 'center'
        }
      });
    }

  });

</script>
</body>

</html>

