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
              @if(isset($operation_id) && $operation_id == 3)
              <li class="nav-item active">
                <a class="nav-link" href="#" id="view-schedule-btn">
                  <i class="now-ui-icons design_bullet-list-67"></i> Schedule
                  <p>
                    <span class="d-lg-none d-md-block">Schedule</span>
                  </p>
                </a>
              </li>
              @endif
              @if(isset($operation_id) && $operation_id == 1)
              <li class="nav-item active">
                <a class="nav-link text-center" href="#" id="select-scrap-to-process-btn" >
                  <i class="now-ui-icons ui-2_settings-90"></i> Process Scrap
                  <p hidden>
                    <span class="d-lg-none d-md-block">Process Scrap</span>
                  </p>
                </a>
              </li>
              @endif
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

  
<!-- Modal -->
<div class="modal fade" id="reset-log-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form action="/reset_operator_time_log" method="POST" autocomplete="off" id="reset-time-log-form">
      @csrf
      <div class="modal-content">
        <div class="modal-header p-3">
          <h5 class="modal-title">Reset Log</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-2">
          <div class="row m-0">
            <div class="col-md-6">
              <input type="hidden" id="job-ticket-id-reset-log" name="job_ticket_id" required>
              <input type="hidden" id="time-log-id-reset-log" name="timelog_id" required>
              <p class="text-center mt-5 font-weight-bold">Reset operator time log?</p>
              <dl class="row m-0">
                <dt class="col-sm-4">Production Order</dt>
                <dd class="col-sm-8" id="production-order-reset-log">-</dd>
                <dt class="col-sm-4">Workstation</dt>
                <dd class="col-sm-8" id="workstation-reset-log">-</dd>
                <dt class="col-sm-4">Process</dt>
                <dd class="col-sm-8" id="process-reset-log">-</dd>
                <dt class="col-sm-4">Start Time</dt>
                <dd class="col-sm-8" id="start-time-reset-log">-</dd>
                <dt class="col-sm-4">End Time</dt>
                <dd class="col-sm-8" id="end-time-reset-log">-</dd>
                <dt class="col-sm-4">Operator</dt>
                <dd class="col-sm-8" id="operator-reset-log">-</dd>
              </dl>
            </div>
            <div class="col-md-6">
              <div class="form-group text-center">
                <label for="reset-log-authorized-user-input" class="font-weight-bold">Enter Authorized Staff ID</label>
                <div class="form-group">
                  <input type="hidden" class="form-control" name="is_operator" value="1" required>
                  <input type="password" class="form-control" id="reset-log-authorized-user-input" name="authorized_staff" style="font-size: 18pt; text-align: center;" required>
                </div>
              </div>
            
              <div class="text-center numpad-div">
                <div class="row1">
                  <span class="numpad reset-log-authorized-user-numpad">1</span>
                  <span class="numpad reset-log-authorized-user-numpad">2</span>
                  <span class="numpad reset-log-authorized-user-numpad">3</span>
                </div>
                <div class="row1">
                  <span class="numpad reset-log-authorized-user-numpad">4</span>
                  <span class="numpad reset-log-authorized-user-numpad">5</span>
                  <span class="numpad reset-log-authorized-user-numpad">6</span>
                </div>
                <div class="row1">
                  <span class="numpad reset-log-authorized-user-numpad">7</span>
                  <span class="numpad reset-log-authorized-user-numpad">8</span>
                  <span class="numpad reset-log-authorized-user-numpad">9</span>
                </div>
                <div class="row1">
                  <span class="numpad" onclick="document.getElementById('reset-log-authorized-user-input').value=document.getElementById('reset-log-authorized-user-input').value.slice(0, -1);"><</span>
                  <span class="numpad reset-log-authorized-user-numpad">0</span>
                  <span class="numpad" onclick="document.getElementById('reset-log-authorized-user-input').value='';">Clear</span>
                </div>
              </div>
              <div class="row">
                <div class="col-md-8 offset-md-2">
                  <button type="submit" class="btn btn-lg btn-block btn-primary">CONFIRM</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
    
  <style type="text/css">
    .numpad{
      font-weight: bolder;
      font-size: 18pt;
    }

    [data-notify] { z-index: 9999 !important; }
  </style>

  <!--   Core JS Files   -->
  <script src="{{ asset('js/core/ajax.min.js') }}"></script> 
  <script src="{{ asset('js/core/jquery.min.js') }}"></script>
  <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
  <script src="{{ asset('/js/plugins/bootstrap-notify.js') }}"></script>

  @yield('script')

  <script>
    $(document).ready(function() {
      $(document).on('click', '.reset-time-log-btn', function(e) {
        e.preventDefault();

        var $row = $(this).parent();

        $('#production-order-reset-log').text($row.find('span').eq(0).text());
        $('#workstation-reset-log').text($row.find('span').eq(1).text());
        $('#process-reset-log').text($row.find('span').eq(2).text());
        $('#start-time-reset-log').text($row.find('span').eq(3).text());
        $('#end-time-reset-log').text($row.find('span').eq(4).text());
        $('#operator-reset-log').text($row.find('span').eq(5).text());

        $('#job-ticket-id-reset-log').val($(this).data('jobticket'));
        $('#time-log-id-reset-log').val($(this).data('timelog'));
          
        $('#reset-log-modal').modal('show');
      });

      $(document).on('submit', '#reset-time-log-form', function(e) {
        e.preventDefault();
        $.ajax({
          url: $(this).attr('action'),
          type:"POST",
          data: $(this).serialize(),
          success:function(data){
            if(data.status){
              showNotification("success", data.message, "now-ui-icons ui-1_check");

              $('#process-bc').empty();
              $('#jt-details-tbl tbody').empty();
              $.ajax({
                url:"/get_jt_details/" + data.id,
                type:"GET",
                success:function(response){
                  if (response.success < 1) {
                    showNotification("danger", response.message, "now-ui-icons travel_info");
                  }else{
                    $('#production-search-content').html(response);
                  }
                }
              });

              $('#reset-log-modal').modal('hide');
            }else{
              showNotification("danger", data.message, "now-ui-icons travel_info");
            }
          }
        });
      });

      // numpad for production order search
      $(document).on('click', '.reset-log-authorized-user-numpad', function(e){
        e.preventDefault();
        var num = $(this).text();
        var current = $('#reset-log-authorized-user-input').val();
        var new_input = current + num;
          
        $('#reset-log-authorized-user-input').val(new_input);
      });

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