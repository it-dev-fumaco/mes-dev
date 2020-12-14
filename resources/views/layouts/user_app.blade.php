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
    <nav class="navbar navbar-expand-lg navbar-transparent bg-primary navbar-absolute">
      <div class="container-fluid">
        <div class="navbar-wrapper">
          <div class="navbar-toggle">
            <button type="button" class="navbar-toggler">
              <span class="navbar-toggler-bar bar1"></span>
              <span class="navbar-toggler-bar bar2"></span>
              <span class="navbar-toggler-bar bar3"></span>
            </button>
          </div>
          <a class="navbar-brand" href="#">MES</a>
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-bar navbar-kebab"></span>
          <span class="navbar-toggler-bar navbar-kebab"></span>
          <span class="navbar-toggler-bar navbar-kebab"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navigation">
          <ul class="navbar-nav">
            @if (in_array($activePage,['production_schedule', 'production_schedule_painting', 'production_schedule_assembly']))
            @if(isset($mes_user_operations) && count($mes_user_operations) > 1)
            <li class="nav-item active dropdown">
              <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="now-ui-icons files_paper"></i> Scheduling
                <p>
                  <span class="d-lg-none d-md-block">Scheduling</span>
                </p>
              </a>
              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                  {{--@if ($activePage != 'production_schedule')--}}
                  @if(in_array('Fabrication', $mes_user_operations))
                  <a class="dropdown-item {{ (request()->segment(2) == '1') ? 'active' : '' }}" href="/production_schedule/1">Fabrication</a>
                  @endif
                  {{--@endif--}}
                  @if ($activePage != 'production_schedule_painting')
                  @if(in_array('Painting', $mes_user_operations))
                  <a class="dropdown-item {{ (request()->segment(2) == '0') ? 'active' : '' }}" href="/production_schedule/0">Painting</a>
                  @endif
                  @endif
                  {{--@if ($activePage != 'production_schedule_assembly')
                  @if(in_array('Wiring and Assembly', $mes_user_operations))--}}
                  <a class="dropdown-item {{ (request()->segment(2) == '3') ? 'active' : '' }}" href="/production_schedule/3">Assembly</a>
                  {{--@endif
                  @endif--}}
                </div>
            </li>
            @endif
            @endif

            @if (in_array($activePage,['operators_item_report', 'painting_production_orders']))
            @if(isset($mes_user_operations) && count($mes_user_operations) > 1)
            <li class="nav-item active dropdown">
              <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="now-ui-icons files_paper"></i> Reports
                <p>
                  <span class="d-lg-none d-md-block">Reports</span>
                </p>
              </a>
              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                @if ($activePage != 'operators_item_report')
                @if(in_array('Fabrication', $mes_user_operations))
                <a class="dropdown-item" href="/operator_item_produced_report">Fabrication</a>
                @endif
                @endif
                @if ($activePage != 'painting_production_orders')
                @if(in_array('Painting', $mes_user_operations))
                <a class="dropdown-item" href="/painting_production_orders">Painting</a>
                @endif
                @endif
              </div>
            </li>
            @endif
            @endif

            @if (in_array($activePage,['production_planning', 'production_planning_assembly']))
            @if(isset($mes_user_operations) && count($mes_user_operations) > 1)
            <li class="nav-item active dropdown">
              <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="now-ui-icons files_paper"></i> Planning
                <p>
                  <span class="d-lg-none d-md-block">Planning</span>
                </p>
              </a>
              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                @if ($activePage != 'production_planning')
                @if(in_array('Fabrication', $mes_user_operations))
                <a class="dropdown-item" href="/wizard">Fabrication</a>
                @endif
                @endif
                @if ($activePage != 'production_planning_assembly')
                @if(in_array('Wiring and Assembly', $mes_user_operations))
                <a class="dropdown-item" href="/assembly/wizard">Assembly</a>
                @endif
                @endif
              </div>
            </li>
            @endif
            @endif
            
            @if (in_array($activePage,['production_schedule', 'operators_load_utilization']))
            <li class="nav-item active dropdown">
              <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="now-ui-icons files_paper"></i> Reports
                <p>
                  <span class="d-lg-none d-md-block">Reports</span>
                </p>
              </a>
              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                <a class="dropdown-item" href="#" id="view-item-tracking-btn">Item Tracking</a>
                <a class="dropdown-item" href="/operators_load_utilization">Operator Load Utilization</a>
                <a class="dropdown-item" href="/production_schedule_report">Production Schedule Report</a>
                <a class="dropdown-item" href="/item_feedback">Production Order(s)</a>
              </div>
            </li>
            @endif
            
            <li class="nav-item active" {{ (in_array($activePage,['production_planning', 'machine_schedule', 'item_feedback'])  ? '' : 'hidden') }}>
              <a class="nav-link" href="/production_schedule/1">
                <i class="now-ui-icons files_paper"></i> Production Schedule
                <p>
                  <span class="d-lg-none d-md-block"> Production Schedule</span>
                </p>
              </a>
            </li>
            <li class="nav-item active" {{ (in_array($activePage,['production_schedule', 'item_feedback'])  ? '' : 'hidden') }}>
              <a class="nav-link" href="/wizard">
                <i class="now-ui-icons files_paper"></i> Planning Wizard
                <p>
                  <span class="d-lg-none d-md-block"> Planning Wizard</span>
                </p>
              </a>
            </li>
            @if ($activePage == 'main_dashboard')
            <li class="nav-item active">
              <a class="nav-link" href="#" id="get-notifications-btn">
                <i class="now-ui-icons ui-1_bell-53"></i> Notifications <span class="badge badge-danger" id="notification-badge" style="font-size: 8pt;">0</span>
                <p>
                  <span class="d-lg-none d-md-block"> Notifications</span>
                </p>
              </a>
            </li>
            @endif

            @if ($activePage == 'main_dashboard')
            <li class="nav-item active">
              <a class="nav-link" href="#" id="view-item-tracking-btn">
                <i class="now-ui-icons location_pin"></i> Item Tracking
                <p>
                  <span class="d-lg-none d-md-block"> Item Tracking</span>
                </p>
              </a>
            </li>
            @endif

            <li class="nav-item active" {{ $activePage == 'login' ? 'hidden' : '' }}>
              <a class="nav-link" href="#" id="jt-search-btn">
                <i class="now-ui-icons ui-1_zoom-bold"></i> Production Order Search
                <p>
                  <span class="d-lg-none d-md-block">Production Order Search</span>
                </p>
              </a>
            </li>
        

            @if(Auth::user())
            <li class="nav-item active">
              <a class="nav-link" href="/logout_user">
                <i class="now-ui-icons media-1_button-power"></i> Logout
                <p>
                  <span class="d-lg-none d-md-block">Logout</span>
                </p>
              </a>
            </li>
            <li class="nav-item active">
              <a class="nav-link" href="#" data-toggle="modal" data-target="#about-modal">
                <i class="now-ui-icons travel_info"></i>
                {{--  <p>
                  <span class="d-lg-none d-md-block">About</span>
                </p>  --}}
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

  <div class="modal fade" id="about-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title font-weight-bold"><i class="now-ui-icons travel_info"></i> About</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <h4 class="text-center title">Manufacturing Execution System</h4>
              <h5 class="text-center" style="font-style: italic;">version: <b>7.3</b> <span style="font-size: 9pt;">Latest Release: 2020-11-21</span></h5>
            </div>          
          </div>
        </div>
        <div class="modal-footer">
            <span class="text-primary">&copy; 2020 FUMACO IT Dev. Team</span>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="jt-workstations-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="min-width: 90%;">
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title font-weight-bold">Modal Title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" style="min-height: 600px;">
          <div id="production-search-content"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="jt-workstations-modal2" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="min-width: 90%;">
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title font-weight-bold">Modal Title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" style="min-height: 600px;">
          <div id="production-search-content-modal2"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Cancel Production Order -->
  <div class="modal fade" id="cancel-production-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <form action="/cancel_production_order" method="POST">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Modal Title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <input type="hidden" name="id">
                <input type="hidden" name="production_order">
                <p style="font-size: 14pt; margin: 0;" class="text-center">Cancel Production Order <b><span></span></b>?</p>
              </div>
              <div class="col-md-12" id="items-for-return-table"></div>
            </div>
          </div>
          <div class="modal-footer" style="padding: 5px 10px;">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  

  <div class="modal fade" id="item-tracking-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="min-width: 95%;">
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #117a65; padding-bottom: 5px;">
          <div class="row" style="width: 100%; margin: 0;">
            <div class="col-md-9">
                <h5 class="text-white font-weight-bold align-middle"><i class="now-ui-icons location_pin"></i> Sales Order Item Tracking</h5>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                  <input type="text" class="form-control form-control-lg item-tracking-search" placeholder="Search" id="search-information_so" style="background-color: white;">
                </div>
            </div>
          </div>
          {{--  <h5 class="modal-title font-weight-bold"><i class="now-ui-icons location_pin"></i> Item Tracking</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>  --}}
        </div>
        <div class="modal-body" style="min-height: 500px;">
          <div class="row">
            <div class="col-md-12">
              <div id="item-tracking-div"></div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-lg btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="spotwelding-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="min-width: 90%;">
      <div class="modal-content">
          <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title font-weight-bold prod-title">Modal Title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" style="min-height: 500px;">
          <div class="row">
            <div class="col-md-12">
              <div id="spotwelding-div"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="reschedule-delivery-modal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document" style="min-width:40%;">
      <form action="/update_rescheduled_delivery_date" id="reschedule_delivery_frm" method="POST">
        @csrf
        <div class="modal-content">
          <div class="modal-header text-white" style="background-color: #0277BD;">
            <h5 class="modal-title">Reschedule Delivery Date</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12" id="tbl_reschduled_deli">
                
              </div>
            </div>
          </div>
          <input type="hidden" class="tbl_reload_deli_modal" name="reload_tbl" value="reloadpage">
          <div class="modal-footer" style="padding: 5px 10px;">
            <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  @include('modals.item_track_modal')
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
    
    @include('modals.return_required_item_modal')
    @include('modals.add_required_item_modal')
	  @include('modals.edit_required_item_modal')
    @include('modals.delete_required_item_modal')
    
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
  {{--  <!-- Now Ui Dashboard DEMO methods, don't include it in your project! -->  --}}
  <script src="{{ asset('/js/demo.js') }}"></script>
  <script src="{{ asset('js/jquery-ui.js') }}"></script>

  <script src="{{ asset('/js/ekko-lightbox/ekko-lightbox.min.js') }}"></script>
  @yield('script')
  <script src="{{ asset('/js/jquery.rfid.js') }}"></script>
<script>
   $(document).ready(function(){

    function get_items_for_return(production_order){
    $.ajax({
      url:"/get_items_for_return/" + production_order,
      type:"GET",
      success:function(data){
        $('#items-for-return-table').html(data);
      },
      error : function(data) {
        console.log(data.responseText);
      }
    });
  }

  $(document).on('click', '.cancel-production-btn', function(e){
    e.preventDefault();
    var production_order = $(this).data('production-order');

    $('#cancel-production-modal input[name="production_order"]').val(production_order);
    $('#cancel-production-modal .modal-title').text('Cancel Production Order');
    $('#cancel-production-modal span').eq(1).text(production_order);
    get_items_for_return(production_order);
    $('#cancel-production-modal').modal('show');
  });

  $('#cancel-production-modal form').submit(function(e){
    e.preventDefault();
    $.ajax({
      url: '/cancel_production_order',
      type:"POST",
      data: $(this).serialize(),
      success:function(data){
        if (!data.success) {
          showNotification("danger", data.message, "now-ui-icons travel_info");
        }else{
          showNotification("success", data.message, "now-ui-icons ui-1_check");
          location.reload();
          $('#cancel-production-modal').modal('hide');
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
      }
    });
  });

  $(document).on('click', '.return-required-item-btn', function(e){
    e.preventDefault();

    var $row = $(this).closest('tr');

    $('#return-required-item-modal form input[name="production_order"]').val($(this).data('production-order'));
    
    $('#return-required-item-modal form input[name="sted_id"]').val($(this).data('sted-id'));
    $('#return-required-item-modal form input[name="target_warehouse"]').val($row.find('.target-warehouse').eq(0).text());
    $('#return-required-item-modal form input[name="source_warehouse"]').val($row.find('.source-warehouse').eq(0).text());
    $('#return-required-item-modal form input[name="item_code"]').val($row.find('.item-code').eq(0).text());
    $('#return-required-item-modal form input[name="qty"]').val($row.find('.qty').eq(0).text());
    $('#return-required-item-modal form input[name="qty_to_return"]').val($row.find('.qty').eq(0).text());
    $('#return-required-item-modal').modal('show');
  });

  $('#return-required-item-modal form').submit(function(e){
    e.preventDefault();
    var production_order = $('#return-required-item-modal form input[name="production_order"]').val();
     
    $.ajax({
      url: $(this).attr('action'),
      type:"POST",
      data: $(this).serialize(),
      success:function(response){
        console.log(response);
        if (response.status == 0) {
          showNotification("danger", response.message, "now-ui-icons travel_info");
        }else if(response.status == 2){
          showNotification("info", response.message, "now-ui-icons travel_info");
        }else{
          showNotification("success", response.message, "now-ui-icons ui-1_check");
          get_stock_entry_items(production_order);
          $('#return-required-item-modal').modal('hide');
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
      }
    });
  });

    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
      event.preventDefault();
      $(this).ekkoLightbox({
        showArrows: true,
      });
    });

    $('#add-row-required-item-btn').click(function(e){
      add_row_required_item();
    });

    function add_row_required_item(){
      var opt = '';
      $.ajax({
        url: "/get_mes_warehouse",
        type:"GET",
        datatype: "json",
        async: false,
        success: function(data){
          $.each(data, function(i, v){
            opt += '<option value="' + v + '">' + v + '</option>';
          });

          var row = '<tr>' +
            '<td class="p-1">' +
              '<div class="form-group m-0">' +
              '<input type="text" class="form-control m-0 autocomplete-item-code" name="item_code[]" placeholder="Item Code" required>' +
              '</div>' +
            '</td>' +
            '<td class="p-1">' +
              '<div class="form-group m-0">' +
              '<input type="text" class="form-control m-0" name="quantity[]" placeholder="Quantity" required>' +
              '</div>' +
            '</td>' +
            '<td class="p-1">' +
              '<div class="form-group m-0">' +
              '<select name="source_warehouse[]" class="form-control m-0" required>' + opt + '</select>' +
              '</div>' +
            '</td>' +
            '<td class="p-1 text-center">' +
              '<button type="button" class="btn btn-danger btn-icon btn-icon-mini m-0 remove-row">' +
                '<i class="now-ui-icons ui-1_simple-remove"></i>' +
              '</button>' +
            '</td>' +
          '</tr>';
          
          $('#add-required-item-tbody').append(row);

          $('.autocomplete-item-code').autocomplete({
            source:function(request,response){
              $.ajax({
                url: '/items',
                dataType: "json",
                data: {
                  term : request.term
                },
                success: function(data) {
                  response(data);
                }
              });
            },
            minLength: 1,
          });
        }
      });   
    }

    $('#add-required-item-modal').on('hidden.bs.modal', function(){
      $('#add-required-item-tbody').empty();
      var frm = $(this).find('form')[0];
      if (frm) frm.reset();
    });

    $(document).on('click', '.remove-row', function(e){
      e.preventDefault();
      $(this).parents("tr").remove();
    });

    $(document).on('click', '#add-required-item-btn', function(e){
      e.preventDefault();
      add_row_required_item();
      $('#add-required-item-modal input[name="production_order"]').val($(this).data('production-order'));
      $('#add-required-item-modal').modal('show');
    });

    $(document).on('click', '.change-required-item-btn', function(e){
      e.preventDefault();

      var $row = $(this).closest('tr');
      var sted_name = $row.find('.sted-name').eq(0).text();
      var item_code = $row.find('.item-code').eq(0).text();
      var description = $row.find('.item-description').eq(0).text();
      var required_qty = $row.find('.required-qty').eq(0).text();
      var source_warehouse = $row.find('.source-warehouse').eq(0).text();
      var stock_uom = $row.find('.stock-uom').eq(0).text();
      
      $('#change-required-item-production-order').val($(this).data('production-order'));

      var row = '';
      $('#change-required-item-modal select[name="source_warehouse"]').empty();
      $.ajax({
        url: "/get_mes_warehouse",
        type:"GET",
        datatype: "json",
        async: false,
        success: function(data){
          $.each(data, function(i, v){
            var selected = (source_warehouse == v) ? 'selected' : '';
            row += '<option value="' + v + '" '+ selected +'>' + v + '</option>';
          });
          
          $('#change-required-item-modal select[name="source_warehouse"]').append(row);
        }
      });

      $('#change-required-item-modal input[name="item_classification"]').val($(this).data('item-classification'));
      $('#change-required-item-modal input[name="sted_name"]').val(sted_name);
      $('#change-required-item-modal input[name="item_code"]').val(item_code);
      $('#change-required-item-modal input[name="stock_uom"]').val(stock_uom);
      $('#change-required-item-modal textarea[name="description"]').text(description);
      $('#change-required-item-modal input[name="quantity"]').val(required_qty);
      
      $('#change-required-item-modal').modal('show');
    });

    $('#change-required-item-modal form').submit(function(e){
      e.preventDefault();
      var production_order = $('#change-required-item-production-order').val();
     
      $.ajax({
        url:"/update_ste_detail",
        type:"POST",
        data: $(this).serialize(),
        success:function(response){
          if (response.status == 1) {
            showNotification("success", response.message, "now-ui-icons ui-1_check");
            get_stock_entry_items(production_order);
            $('#change-required-item-modal').modal('hide');
          }else if(response.status == 2){
            showNotification("info", response.message, "now-ui-icons travel_info");
          }else{
            showNotification("danger", response.message, "now-ui-icons travel_info");
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });

    $('#add-required-item-modal form').submit(function(e){
      e.preventDefault();
      var production_order = $('#add-required-item-production-order').val();
      $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: $(this).serialize(),
        success:function(response){
          if (response.status == 1) {
            showNotification("success", response.message, "now-ui-icons ui-1_check");
            get_stock_entry_items(production_order);
            $('#add-required-item-modal').modal('hide');
          }else if(response.status == 2){
            showNotification("info", response.message, "now-ui-icons travel_info");
          }else{
            showNotification("danger", response.message, "now-ui-icons travel_info");
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });

    $(document).on('click', '.delete-required-item-btn', function(e){
      e.preventDefault();
      var $row = $(this).closest('tr');
      var item_code = $row.find('.item-code').eq(0).text();
      var ste_no = $row.find('.ste-name').eq(0).text();
      var sted_name = $row.find('.sted-name').eq(0).text();

      $('#delete-required-item-modal input[name="sted_id"]').val(sted_name);
      $('#delete-required-item-modal input[name="production_order"]').val($(this).data('production-order'));
      $('#delete-required-item-modal input[name="ste_no"]').val(ste_no);
      $('#delete-required-item-modal .modal-body span').eq(0).text(item_code);
      $('#delete-required-item-modal .modal-body span').eq(1).text('('+ste_no+')');

      $('#delete-required-item-modal').modal('show');
    });

    $('#delete-required-item-modal form').submit(function(e){
      e.preventDefault();
      var production_order = $('#delete-required-item-modal input[name="production_order"]').val();
      var sted_id = $('#delete-required-item-modal input[name="sted_id"]').val();
      var ste_no = $('#delete-required-item-modal input[name="ste_no"]').val();
     
      $.ajax({
        url:"/cancel_request/" + sted_id,
        type:"POST",
        data: {ste_no: ste_no},
        success:function(response){
          if (response.error == 1) {
            showNotification("danger", response.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", response.message, "now-ui-icons ui-1_check");
            get_stock_entry_items(production_order);
            $('#delete-required-item-modal').modal('hide');
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });

    $('#change-required-item-modal input[name="item_code"]').autocomplete({
      source:function(request,response){
        $.ajax({
          url: '/get_items/' + $('#change-required-item-modal input[name="item_classification"]').val(),
          dataType: "json",
          data: {
            term : request.term
          },
          success: function(data) {
            response(data);
          }
        });
      },
      minLength: 1,
      select:function(event,ui){
        $('#change-required-item-modal textarea[name="description"]').text(ui.item.id);
        $('#change-required-item-modal input[name="item_name"]').val(ui.item.item_name);
      }
    });

    function get_stock_entry_items(id){
      $.ajax({
        url:"/get_production_order_items/"+ id,
        type:"GET",
        success:function(data){
          $('#tbl_view_transfer_details').html(data);
        },
        error : function(data) {
          console.log(data.responseText);
        }
      });
    }

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

    $(document).on('click', '.resched-deli-btn', function(){
      var prod = $(this).data('production-order');
      $.ajax({
          url: "/reschedule_prod_details/" + prod,
          type:"GET",
          success:function(data){
              $('#tbl_reschduled_deli').html(data);
              $('#reschedule-delivery-modal').modal('show');
          },
          error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
          },
        });
    });
    $(document).on('click', '.production_order_link', function(e){
      e.preventDefault();
      var production_order = $(this).attr('data-jtno');
      $('#jt-workstations-modal2 .modal-title').text(production_order);
      getJtDetails2(production_order);
    });
    $(document).on('click', '.view-production-order-details', function(e){
      e.preventDefault();
      var production_order = $(this).data('production-order');
      console.log(production_order);
      $('#jt-workstations-modal .modal-title').text(production_order);
      getJtDetails(production_order);
    });
    $(document).on('click', '.prod-details-btn', function(e){
      e.preventDefault();
      var production_order = $(this).attr('data-jtno');
      console.log(production_order);
      $('#jt-workstations-modal .modal-title').text(production_order);
      getJtDetails(production_order);
    });
     
    $('#jt-search-btn').click(function(e){
      e.preventDefault();
      $('#jt-search-modal').modal('show');
    });

    $(document).on('click', '.prod-search-numpad', function(){
      var input = '#' + $(this).data('inputid');
      var current = $(input).val();
      var new_input = current + $(this).data('val');

      new_input = format(new_input.replace(/-/g, ""), [5], "-");
        $(input).val(new_input);
    });

    function format(input, format, sep) {
      var output = "";
      var idx = 0;
      for (var i = 0; i < format.length && idx < input.length; i++) {
          output += input.substr(idx, format[i]);
          if (idx + format[i] < input.length) output += sep;
          idx += format[i];
      }

      output += input.substr(idx);

      return output;
    }

    $('#jt-search-frm').submit(function(e){
      e.preventDefault();
      var jtno = "PROM-"+$('#jt-no-search').val();
      $('#jt-workstations-modal .modal-title').text(jtno);
      getJtDetails(jtno);
    });

    $(document).on('show.bs.modal', '.modal', function (event) {
      var zIndex = 1040 + (10 * $('.modal:visible').length);
      $(this).css('z-index', zIndex);
      setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
      }, 0);
    });

    function getJtDetails(jtno){
      $('#process-bc').empty();
      $('#jt-details-tbl tbody').empty();
      $.ajax({
      url:"/get_jt_details/" + jtno,
      type:"GET",
      success:function(data){
        if (data.success < 1) {
          showNotification("danger", data.message, "now-ui-icons travel_info");
        }else{
          $('#production-search-content').html(data);
          $('#jt-workstations-modal').modal('show');
        }
      }
      });
    }
    function getJtDetails2(jtno){
      $('#process-bc').empty();
      $('#jt-details-tbl tbody').empty();
      $.ajax({
      url:"/get_jt_details/" + jtno,
      type:"GET",
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            $('#production-search-content-modal2').html(data);
            $('#jt-workstations-modal2').modal('show');
          }
          
        }
      });
    }

    $('#view-item-tracking-btn').click(function(e){
      e.preventDefault();
      get_item_list();
      $('#item-tracking-modal').modal('show');
    });

    function get_item_list(page, query){
      $.ajax({
        url:"/get_item_status_tracking/?page="+page,
        type:"GET",
        data: {search_string: query},
        success:function(data){
          $('#item-tracking-div').html(data);
        }
      }); 
    }

    $(document).on('click', '.tbl_item_list_pagination a', function(event){
      event.preventDefault();
      var query= $('.item-tracking-search').val();
      var page = $(this).attr('href').split('page=')[1];
      get_item_list(page, query);
    });

    $(document).on('keyup', '.item-tracking-search', function(){
      var query = $(this).val();
      get_search_information_details(1, query);
    });

    function get_search_information_details(page, query){
      $.ajax({
        url: "/get_search_information_details/?page=" + page,
        type:"GET",
        data: {search_string: query},
        success:function(data){
            $('#item-tracking-div').html(data);
        }
      });
    }

    $(document).on('click', '.btn_trackmodal', function(event){
      event.preventDefault();
      var guideid = $(this).attr('data-guideid');
      var itemcode = $(this).attr('data-itemcode');
      var customer = $(this).attr('data-customer');
      $.ajax({
        url: "/get_bom_tracking/" + guideid + "/" + itemcode,
        type:"GET",
        success:function(data){
          if(data.success < 1){
          showNotification("info", data.message, "now-ui-icons travel_info");
        }else{
            $('#track-view-modal #tbl_flowchart').html(data);
            $('#track-view-modal').modal('show');
            }
        }
      });
    });

    $(document).on('click', '.spotclass', function(event){
      event.preventDefault();
      var jtid = $(this).attr('data-jobticket');
      var prod = $(this).attr('data-prodno');
      $.ajax({
        url: "/spotwelding_production_order_search/" + jtid,
        type:"GET",
        success:function(data){
            $('#spotwelding-div').html(data);
            $('#spotwelding-modal .prod-title').text(prod+" - Spotwelding");
            $('#spotwelding-modal').modal('show');
        }
      });
    });
  });
</script>
</body>

</html>

@include('modals.search_productionorder')

