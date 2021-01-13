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
          {{ $namePage }}
        </a>
      </div>
      <div class="sidebar-wrapper" id="sidebar-wrapper">
        <ul class="nav">
          <li class="{{ $activePage == 'painting_dashboard' ? 'active' : '' }}">
            <a href="/painting_dashboard">
              <i class="now-ui-icons design_app"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="{{ $activePage == 'painting_production_orders' ? 'active' : '' }}">
            <a href="/painting_production_orders">
              <i class="now-ui-icons education_atom"></i>
              <p>Production Order(s)</p>
            </a>
          </li>
          <li class="{{ $activePage == 'production_schedule_painting' ? 'active' : '' }}">
            <a href="/production_schedule_painting">
              <i class="now-ui-icons text_caps-small"></i>
              <p>Production Schedule</p>
            </a>
          </li>
          <li class="{{ $activePage == 'operators' ? 'active' : '' }}">
            <a href="/operators">
              <i class="now-ui-icons location_map-big"></i>
              <p>Operators</p>
            </a>
          </li>
          <li class="{{ $activePage == 'bom_list' ? 'active' : '' }}">
            <a href="/bom">
              <i class="now-ui-icons design_bullet-list-67"></i>
              <p>BOM List</p>
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
            <a class="navbar-brand" href="#">{{ $namePage }}</a>
          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end" id="navigation">
            <ul class="navbar-nav">
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
                </div>
              </li>
              @endif
              
              <li class="nav-item active" {{ (in_array($activePage,['production_planning', 'machine_schedule', 'item_feedback'])  ? '' : 'hidden') }}>
                <a class="nav-link" href="/production_schedule">
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
              <h5 class="text-center" style="font-style: italic;">version: <b>7.6.1</b> <span style="font-size: 9pt;">Latest Release: 2020-01-11</span></h5>
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
                  <input type="text" class="form-control form-control-lg" placeholder="Search" id="search-information_so" style="background-color: white;">
                </div>
            </div>
          </div>
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
  {{--  <!-- Now Ui Dashboard DEMO methods, don't include it in your project! -->  --}}
  <script src="{{ asset('/js/demo.js') }}"></script>
  <script src="{{ asset('js/jquery-ui.js') }}"></script>
  @yield('script')
  <script src="{{ asset('/js/jquery.rfid.js') }}"></script>
<script>
   $(document).ready(function(){
     
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
    $('#view-item-tracking-btn').click(function(e){
      e.preventDefault();
      get_item_list();
      $('#item-tracking-modal').modal('show');
    });

    function get_item_list(page){
      $.ajax({
        url:"/get_item_status_tracking/?page="+page,
        type:"GET",
        success:function(data){
          $('#item-tracking-div').html(data);
        }
      }); 
    }

    $(document).on('click', '.tbl_item_list_pagination a', function(event){
      event.preventDefault();
      var page = $(this).attr('href').split('page=')[1];
      get_item_list(page);
    });

    $(document).on('click', '.production_order_link', function(e){
      e.preventDefault();
      var production_order = $(this).attr('data-jtno');
      console.log(production_order);
      $('#jt-workstations-modal2 .modal-title').text(production_order);
      getJtDetails2(production_order);
    });

    $(document).on('keyup', '#search-information_so', function(){
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
            $('#track-view-modal #tbl_flowchart').html(data);
            $('#track-view-modal').modal('show');
        }
      });
    });
  });
</script>
</body>

</html>

@include('modals.search_productionorder')

