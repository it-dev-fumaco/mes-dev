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
          @if(!in_array($activePage, ['operator_dashboard', 'operator_workstation_dashboard']))
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
                <a class="nav-link text-center" href="#" id="pending-for-maintenance-trigger">
                  <i class="now-ui-icons ui-2_settings-90"></i> Maintenance Request
                  <p hidden>
                    <span class="d-lg-none d-md-block">Maintenance Request</span>
                  </p>
                </a>
              </li>
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

  @if (!in_array($activePage, ['operator_workstation_dashboard']))
    @include('modals.qc_rejects')
    @include('modals.qc_machine_setup_random')
  @endif
  
<!-- Modal -->
<div class="modal fade" id="reset-log-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form action="/reset_operator_time_log" method="POST" autocomplete="off" id="reset-time-log-form">
      @csrf
      <div class="modal-content">
        <div class="modal-header p-3">
          <h5 class="modal-title">Reset Time Log</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-2">
          <div class="row m-0">
            <div class="col-md-6 pt-3">
              <input type="hidden" id="job-ticket-id-reset-log" name="job_ticket_id" required>
              <input type="hidden" id="time-log-id-reset-log" name="timelog_id" required>
              <dl class="row mt-5">
                <dt class="col-sm-4">Start Time</dt>
                <dd class="col-sm-8" id="start-time-reset-log">-</dd>
                <dt class="col-sm-4">End Time</dt>
                <dd class="col-sm-8" id="end-time-reset-log">-</dd>
                <dt class="col-sm-4">Operator</dt>
                <dd class="col-sm-8" id="operator-reset-log">-</dd>
              </dl>
              <p class="text-center font-weight-bold mt-5 pt-1">
                Operator time log will reset for <br><span id="workstation-reset-log"></span> (<span id="process-reset-log"></span>) in <span id="production-order-reset-log"></span>
              </p>
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


<!-- Modal -->
<div class="modal fade" id="edit-log-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form action="/edit_operator_time_log" method="POST" autocomplete="off" id="edit-time-log-form">
      @csrf
      <div class="modal-content">
        <div class="modal-header p-3">
          <h5 class="modal-title">Edit Time Log</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-2">
          <input type="hidden" id="job-ticket-id-edit-log" name="job_ticket_id" required>
          <input type="hidden" id="time-log-id-edit-log" name="timelog_id" required>
          <input type="hidden" class="form-control" name="is_operator" value="1" required>
          <div class="row m-0">
            <div class="col-md-12 p-1">
              <ul class="nav nav-tabs d-none" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active custom-tabs-1" id="edit-log-tab1" data-toggle="tab" href="#edit-log-tab1-content" role="tab">Tab 1</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link custom-tabs-1" id="edit-log-tab2" data-toggle="tab" href="#edit-log-tab2-content" role="tab">Tab 2</a>
                </li>
              </ul>
              <div class="tab-content">
                <div class="tab-pane active" id="edit-log-tab1-content" role="tabpanel">
                  <div class="row mt-2">
                    <div class="col-md-6">
                      <dl class="row mt-5 ml-3">
                        <dt class="col-sm-4">Production Order</dt>
                        <dd class="col-sm-8" id="production-order-edit-log">-</dd>
                        <dt class="col-sm-4">Workstation</dt>
                        <dd class="col-sm-8" id="workstation-edit-log">-</dd>
                        <dt class="col-sm-4">Process</dt>
                        <dd class="col-sm-8" id="process-edit-log">-</dd>
                        <dt class="col-sm-4">Good Qty</dt>
                        <dd class="col-sm-8" id="good-qty-edit-log">-</dd>
                        <dt class="col-sm-4">Operator</dt>
                        <dd class="col-sm-8" id="operator-edit-log">-</dd>
                      </dl>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group text-center">
                        <label for="new-good-qty-edit-log" class="font-weight-bold">New Good Qty</label>
                        <input type="text" class="form-control" id="new-good-qty-edit-log" name="qty" value="0" style="text-align: center; font-size: 16pt;" readonly>
                      </div>
        
                      <div class="text-center numpad-div">
                        <div class="row1">
                          <span class="numpad new-good-qty-edit-log-numpad">1</span>
                          <span class="numpad new-good-qty-edit-log-numpad">2</span>
                          <span class="numpad new-good-qty-edit-log-numpad">3</span>
                        </div>
                        <div class="row1">
                          <span class="numpad new-good-qty-edit-log-numpad">4</span>
                          <span class="numpad new-good-qty-edit-log-numpad">5</span>
                          <span class="numpad new-good-qty-edit-log-numpad">6</span>
                        </div>
                        <div class="row1">
                          <span class="numpad new-good-qty-edit-log-numpad">7</span>
                          <span class="numpad new-good-qty-edit-log-numpad">8</span>
                          <span class="numpad new-good-qty-edit-log-numpad">9</span>
                        </div>
                        <div class="row1">
                          <span class="numpad" onclick="document.getElementById('new-good-qty-edit-log').value=document.getElementById('new-good-qty-edit-log').value.slice(0, -1);"><</span>
                          <span class="numpad new-good-qty-edit-log-numpad">0</span>
                          <span class="numpad" onclick="document.getElementById('new-good-qty-edit-log').value='';">Clear</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <button type="button" class="btn btn-secondary btn-block btn-lg" data-dismiss="modal">Cancel</button>
                    </div>
                    <div class="col-md-6">
                      <button type="button" class="btn btn-primary btn-block btn-lg" id="edit-log-tab1-content-next-btn">Next</button>
                    </div>
                  </div>
                </div>
                <div class="tab-pane" id="edit-log-tab2-content" role="tabpanel">
                  <div class="row mt-2">
                    <div class="col-md-6 offset-md-3 text-center">
                      <div class="form-group">
                        <label for="edit-log-authorized-user-input" class="font-weight-bold">Enter Authorized Staff ID</label>
                        <input type="password" class="form-control" name="authorized_staff" style="text-align: center; font-size: 16pt;" id="edit-log-authorized-user-input" readonly required>
                      </div>
                      <div class="text-center numpad-div">
                        <div class="row1">
                          <span class="numpad edit-log-authorized-user-numpad">1</span>
                          <span class="numpad edit-log-authorized-user-numpad">2</span>
                          <span class="numpad edit-log-authorized-user-numpad">3</span>
                        </div>
                        <div class="row1">
                          <span class="numpad edit-log-authorized-user-numpad">4</span>
                          <span class="numpad edit-log-authorized-user-numpad">5</span>
                          <span class="numpad edit-log-authorized-user-numpad">6</span>
                        </div>
                        <div class="row1">
                          <span class="numpad edit-log-authorized-user-numpad">7</span>
                          <span class="numpad edit-log-authorized-user-numpad">8</span>
                          <span class="numpad edit-log-authorized-user-numpad">9</span>
                        </div>
                        <div class="row1">
                          <span class="numpad" onclick="document.getElementById('edit-log-authorized-user-input').value=document.getElementById('edit-log-authorized-user-input').value.slice(0, -1);"><</span>
                          <span class="numpad edit-log-authorized-user-numpad">0</span>
                          <span class="numpad" onclick="document.getElementById('edit-log-authorized-user-input').value='';">Clear</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <button type="button" class="btn btn-secondary btn-block btn-lg" id="edit-log-tab1-content-prev-btn">Previous</button>
                    </div>
                    <div class="col-md-6">
                      <button type="submit" class="btn btn-primary btn-block btn-lg">Submit</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="qa-login-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #F57F17; color: #fff;">
        <h5 class="modal-title" id="exampleModalLabel">QA Reject Confirmation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="/verify_qa_staff" id="qa-login-form" method="post">
          <div class="form-group text-center">
            <label class="font-weight-bold">Enter Authorized Staff ID</label>
            <div class="form-group">
              @csrf
              <input type="password" class="form-control" id="qa-login-id" name="id" style="font-size: 18pt; text-align: center;" required>
            </div>
          </div>
        
          <div class="text-center numpad-div">
            <div class="row1">
              <span class="numpad num-key" data-input="#qa-login-id">1</span>
              <span class="numpad num-key" data-input="#qa-login-id">2</span>
              <span class="numpad num-key" data-input="#qa-login-id">3</span>
            </div>
            <div class="row1">
              <span class="numpad num-key" data-input="#qa-login-id">4</span>
              <span class="numpad num-key" data-input="#qa-login-id">5</span>
              <span class="numpad num-key" data-input="#qa-login-id">6</span>
            </div>
            <div class="row1">
              <span class="numpad num-key" data-input="#qa-login-id">7</span>
              <span class="numpad num-key" data-input="#qa-login-id">8</span>
              <span class="numpad num-key" data-input="#qa-login-id">9</span>
            </div>
            <div class="row1">
              <span class="numpad num-key" data-input="#qa-login-id"><</span>
              <span class="numpad num-key" data-input="#qa-login-id">0</span>
              <span class="numpad num-key" data-input="#qa-login-id">Clear</span>
            </div>
          </div>
          <div class="row">
            <div class="col-md-8 offset-md-2">
              <button type="submit" class="btn btn-lg btn-block btn-primary" id="qa-login">CONFIRM</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="general-keypad" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #F57F17; color: #fff;">
        <h5 class="modal-title" id="exampleModalLabel">QA Reject Confirmation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group text-center">
          <span id="reject-type-text" class="font-weight-bold mb-2 d-block" style="font-size: 12pt"></span>
          <label class="font-weight-bold" id="modal-label">Enter Qty</label>
          <div class="form-group">
            <input type="text" class="form-control" id="general-keypad-input" style="font-size: 18pt; text-align: center;" required>
          </div>
        </div>

        <div class="text-center numpad-div">
          <div class="row1">
            <span class="numpad num-key" data-input="#general-keypad-input">1</span>
            <span class="numpad num-key" data-input="#general-keypad-input">2</span>
            <span class="numpad num-key" data-input="#general-keypad-input">3</span>
          </div>
          <div class="row1">
            <span class="numpad num-key" data-input="#general-keypad-input">4</span>
            <span class="numpad num-key" data-input="#general-keypad-input">5</span>
            <span class="numpad num-key" data-input="#general-keypad-input">6</span>
          </div>
          <div class="row1">
            <span class="numpad num-key" data-input="#general-keypad-input">7</span>
            <span class="numpad num-key" data-input="#general-keypad-input">8</span>
            <span class="numpad num-key" data-input="#general-keypad-input">9</span>
          </div>
          <div class="row1">
            <span class="numpad num-key" data-input="#general-keypad-input"><</span>
            <span class="numpad num-key" data-input="#general-keypad-input">0</span>
            <span class="numpad num-key" data-input="#general-keypad-input">Clear</span>
          </div>
        </div>

        <div class="row">
          <div class="col-md-8 offset-md-2">
            <button type="submit" class="btn btn-lg btn-block btn-primary" id="enter-key">CONFIRM</button>
          </div>
        </div>
      </div>
    </div>
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
      function close_modal(modal){
        $(modal).modal('hide');
      }

      $(document).on('click', '.num-key', function (e){
        var input = $(this).data('input');
        var val = $(input).val();
        switch ($(this).text()) {
          case 'Clear':
            val = '';
            break;
          case '<':
            val = val.slice(0, -1);
            break;
          default:
            val = val + $(this).text();
            break;
        }

        $(input).val(val);
      });

      var reject_qty = reject_type = '';
      $(document).on('click', '#reject-confirmation-form input', function (e){
        e.preventDefault();
        reject_qty = $(this);
        reject_type = '';
        if($(this).parent().prev().find('select').find(":selected").val()){
          $(this).parent().prev().find('select').removeClass('border').removeClass('border-danger');
          reject_type = $(this).parent().prev().find('select').find(":selected").text();

          if($(this).data('type') == 'Reject Type'){
            $('#reject-type-text').removeClass('d-none');
            $('#reject-type-text').text('Reject Type: ' + reject_type);
            $('#modal-label').text('Enter Qty');
          }else{
            $('#modal-label').text('Enter Confirmed Reject Qty');
          }

          $("#general-keypad-input").val($(this).val());
          $('#general-keypad').modal('show');

          $(document).on('click', '#enter-key', function(event){
            event.preventDefault();
            reject_qty.val($("#general-keypad-input").val());
            $('#general-keypad').modal('hide');
          });
        }else{
          $(this).parent().prev().find('select').addClass('border').addClass('border-danger');
          showNotification("danger", 'Please select reject type.', "now-ui-icons travel_info");
        }
      });

      $('#general-keypad').on('hidden.bs.modal', function (e) {
        $('#reject-type-text').addClass('d-none');
      });

      $(document).on('click', '.remove-workstation-row', function(e) {
        e.preventDefault();
        $(this).closest("tr").remove();
      });

      $(document).on('click', '#fourth-tab', function (e){
        e.preventDefault();
        get_reject_for_confirmation($(this).data('production-order'), $(this).data('operation'));
      });

      function get_reject_for_confirmation(production_order, operation){
        $.ajax({
          url:"/getProductionOrderRejectForConfirmation/" + production_order,
          type:"GET",
          data: {
            operation: operation,
          },
          success:function(response){
            $('#tab' + production_order + '-4').html(response);
            $('#header-table').addClass('d-none');
          }
        });
      }

      $(document).on('submit', '#reject-confirmation-form', function(e) {
        e.preventDefault();
        $('#qa-login-modal').modal('show');
      });

      $(document).on('submit', '#qa-login-form', function (e){
        e.preventDefault();
        var id = $('#qa-login-id').val();
        
        $.ajax({
          url: $(this).attr('action'),
          type:"POST",
          data: $(this).serialize(),
          success:function(response){
            if(response.success){
              $('#qa-login-modal').modal('hide');

              $.ajax({
                url: $('#reject-confirmation-form').attr('action'),
                type:"POST",
                data: $('#reject-confirmation-form').serialize(),
                success:function(data){
                  if (data.success) {
                    showNotification("success", data.message, "now-ui-icons ui-1_check");
                    $('#reject-confirmation-modal').modal('hide');
                    $('#fourth-tab').trigger('click');
                  }else{
                    showNotification("danger", data.message, "now-ui-icons travel_info");
                  }
                },
                error: function(response) {
                  showNotification("danger", 'An error occured. Please try again.', "now-ui-icons travel_info");
                }
              });
            }else{
              showNotification("danger", response.message, "now-ui-icons travel_info");
            }
          },
          error: function(response) {
            showNotification("danger", 'An error occured. Please try again.', "now-ui-icons travel_info");
          }
        });
      });
      
      $(document).on('click', '.edit-time-log-btn', function(e) {
        e.preventDefault();

        var $row = $(this).parent();

        $('#production-order-edit-log').text($row.find('span').eq(0).text());
        $('#workstation-edit-log').text($row.find('span').eq(1).text());
        $('#process-edit-log').text($row.find('span').eq(2).text());
        $('#start-time-edit-log').text($row.find('span').eq(3).text());
        $('#end-time-edit-log').text($row.find('span').eq(4).text());
        $('#operator-edit-log').text($row.find('span').eq(5).text());

        var qty = $row.find('span').eq(6).text();
        $('#good-qty-edit-log').text(qty);
        $('#new-good-qty-edit-log').val(qty);

        $('#job-ticket-id-edit-log').val($(this).data('jobticket'));
        $('#time-log-id-edit-log').val($(this).data('timelog'));
          
        $('#edit-log-modal').modal('show');

        $('#edit-log-tab1').tab('show');
      });

      $(document).on('submit', '#edit-time-log-form', function(e) {
        e.preventDefault();

        if (!$('#edit-log-authorized-user-input').val()) {
          showNotification("danger", 'Please enter Authorized Staff ID.', "now-ui-icons travel_info");
          return false;
        }

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

              $('#edit-log-modal').modal('hide');
            }else{
              showNotification("danger", data.message, "now-ui-icons travel_info");
            }
          }
        });
      });

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

      $(document).on('click', '.new-good-qty-edit-log-numpad', function(e){
        e.preventDefault();
        var num = $(this).text();
        var current = $('#new-good-qty-edit-log').val();
        var new_input = current + num;
          
        $('#new-good-qty-edit-log').val(new_input);
      });

      $(document).on('click', '.edit-log-authorized-user-numpad', function(e){
        e.preventDefault();
        var num = $(this).text();
        var current = $('#edit-log-authorized-user-input').val();
        var new_input = current + num;
          
        $('#edit-log-authorized-user-input').val(new_input);
      });

      $(document).on('click', '#edit-log-tab1-content-next-btn', function(e){
        e.preventDefault();

        if (!$('#new-good-qty-edit-log').val()) {
          showNotification("danger", 'Please enter qty.', "now-ui-icons travel_info");
          return false;
        }

        if ($('#new-good-qty-edit-log').val() <= 0) {
          showNotification("danger", 'Qty cannot be less than or equal to 0.', "now-ui-icons travel_info");
          return false;
        }

        $('#edit-log-modal .nav-tabs .nav-item > .active').parent().next().find('.custom-tabs-1').tab('show');
      });

      $(document).on('click', '#edit-log-tab1-content-prev-btn', function(e){
        e.preventDefault();
        $('#edit-log-modal .nav-tabs .nav-item > .active').parent().prev().find('.custom-tabs-1').tab('show');
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

      $(document).on('click', '#quality-inspection-frm .next-tab', function(e){
        e.preventDefault();
        var tab_id = $(this).data('tab-id');
        var tab_qty_reject = parseInt($('#' + tab_id + '-qty-reject').val());
        var tab_qty_checked = parseInt($('#' + tab_id + '-qty-checked').val());
        var tab_qty = parseInt($('#' + tab_id + '-qty').val());
        var tab_reject_level = parseInt($('#' + tab_id + ' .reject-level').text());

        if(tab_qty_checked <= 0){
          showNotification("danger", 'Please enter quantity checked.', "now-ui-icons travel_info");
          return false;
        }

        var checklist_unchecked = $('#' + tab_id + ' .chk-list input:checkbox:not(:checked)').length;
        if(checklist_unchecked > 0){
          if(tab_qty_reject <= 0){
            showNotification("danger", 'Please enter quantity reject.', "now-ui-icons travel_info");
            return false;
          }

          if(tab_qty_reject > tab_qty_checked){
            showNotification("danger", 'Reject quantity cannot be greater than quantity checked.', "now-ui-icons travel_info");
            return false;
          }
        }else{
          $('#' + tab_id + '-qty-reject').val(0);
        }

        if(tab_qty_checked > tab_qty){
          showNotification("danger", 'Quantity checked cannot be greater than '+ tab_qty +'.', "now-ui-icons travel_info");
          return false;
        }

        var sample_size = $('#' + tab_id + ' .sample-size').text();
        if(sample_size != $('#' + tab_id + '-qty-checked').val()){
          if($('#' + tab_id + '-validated-sample-size').val() == 0){
            $('#confirm-sample-size-modal .sample-size').text(sample_size);
            $('#sample-size-tab-id').val(tab_id);
            $('#confirm-sample-size-modal').modal('show');
            return false;
          }
        }
        
        var active_tab_qa = $('#quality-inspection-modal .nav-tabs li > .active').attr('id');
        var invalid = false;
        var declared_reject_qty = $('#quality-inspection-modal').find('input[name="qty_reject"]').eq(0).val();
        if (active_tab_qa == 'tab-occurence') {
          $('#occurence-div input').each(function(){
            if ($(this).val() <= 0) {
              invalid = true;
              showNotification("danger", 'Please specify qty of items with ' + $(this).data('reason'), "now-ui-icons travel_info");
              return false;
            }

            if ($(this).val() > declared_reject_qty) {
              invalid = true;
              showNotification("danger", 'Qty for ' + $(this).data('reason') + ' cannot be greater than rejected qty (' + declared_reject_qty + ').', "now-ui-icons travel_info");
              return false;
            }
          });
        }

        if (invalid) {
          return false;
        }
        
        var next_tab_id = $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').attr('id');
        if(next_tab_id != 'tablast'){
          if(declared_reject_qty <= 0){
            $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').removeClass('custom-tabs-1').addClass('active');
          }else{
            $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').addClass('custom-tabs-1').removeClass('active');
          }
        }

        if (next_tab_id == 'tab-occurence') {
          $('#occurence-div').empty();
        }  
        
        var no_rej = '';
        var table = '<table style="width: 100%; font-size: 10pt;" border="1">' + 
          '<col style="width:30%;"><col style="width:20%;"><col style="width:50%;">' +
          '<tr><th class="text-center" style="border: 1px solid #ABB2B9; padding: 2px 0;">Inspection</th><th class="text-center" style="border: 1px solid #ABB2B9; padding: 2px 0;">Reject(s)</th><th class="text-center" style="border: 1px solid #ABB2B9; padding: 2px 0;">Reject Reason</th></tr>';
        
        var reject_id = '';
        var reject_values = '';
        var qty_checked = 0;
        var qty_reject = 0;
        $('#quality-inspection-modal .custom-tabs-1').each(function(){
          var tab_pane_id = $('#' + $(this).attr('id') + '-inspection');
          var q = tab_pane_id.find('input[name="qty_checked"]').eq(0).val();
          var r = tab_pane_id.find('input[name="qty_reject"]').eq(0).val();
          if(q){
            qty_checked = qty_checked + parseInt(q);
            qty_reject = qty_reject + parseInt(r);
          }

          var reject_type_occurence = '';
          $('#' + $(this).attr('id') + '-inspection input:checkbox:not(:checked)').each(function(i){
            if($.isNumeric($(this).val())){
              reject_id += $(this).val() + ',';
              reject_values += $('#' + $(this).attr('id') + '-input').val() + ',';
              
              reject_type_occurence += '<div class="col-6 p-1"><div class="d-flex flex-row align-items-center border rounded"><div class="col-9 pt-1 pb-1 pl-2 pr-2">'+$(this).data('reject-reason')+'</div>' +
              '<div class="col-3 p-1"><input type="text" class="form-control rounded qty-input" data-edit="1" id="rto'+i+'" value="0" name="occ['+$(this).val()+']" data-reason="'+$(this).data('reject-reason')+'" readonly required></div></div></div>';
            }
          });

          if (next_tab_id == 'tab-occurence') {
            $('#occurence-div').append(reject_type_occurence);
          }

          var checklist_category = tab_pane_id.find('.checklist-category').eq(0).text();
          var reject_qty = tab_pane_id.find('input[name="qty_reject"]').eq(0).val();
          var reason = '';
          $('#' + $(this).attr('id') + '-inspection input:checkbox:not(:checked)').each(function(){
            if($.isNumeric($(this).val())){
              reason += $(this).data('reject-reason') + ', ';
            }
          });

          if(checklist_category){
            if(parseInt(tab_pane_id.find('input[name="qty_checked"]').eq(0).val()) > 0){
              if(reject_qty <= 0){
                reason = 'No Reject';
                no_rej += '<br>' + tab_pane_id.find('.chklist-cat').text();
              }else{
                table += '<tr>' + 
                  '<td class="text-center" style="border: 1px solid #ABB2B9; padding: 2px;"><div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100px;">' + checklist_category + '</div></td>' +
                  '<td class="text-center" style="border: 1px solid #ABB2B9; padding: 2px;">' + reject_qty + '</td>' +
                  '<td style="border: 1px solid #ABB2B9; padding: 2px;">' + 
                  '<div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 180px;">' + reason + '</div></td>' +
                  '</tr>';
              }
            }
          }

          $('#qa-result-div-1').html(no_rej);
        });

        table += '</table>';

        $('#rejection-types-input').val(reject_id);
        $('#rejection-values-input').val(reject_values);
        $('#final-qty-checked').text(qty_checked);

        $('#total-rejects-input').val(qty_reject);
        $('#total-checked-input').val(qty_checked);

        if(qty_reject > 0){
          $('#quality-inspection-frm .reject-details-tr').removeAttr('hidden');
          $('#qc-status').addClass('text-danger').removeClass('text-success').text('QC Failed');
          $('#qa-result-div').html(table);
        }else{
          $('#quality-inspection-frm .reject-details-tr').attr('hidden', true);
          $('#qc-status').addClass('text-success').removeClass('text-danger').text('QC Passed');
          $('#qa-result-div').empty();
        }

        active_input = null;
        
        $('#quality-inspection-modal .nav-tabs .nav-item > .active').parent().next().find('.custom-tabs-1').tab('show');
        $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').removeAttr('active');
      });

      $(document).on('click', '#quality-inspection-frm .prev-tab', function() {
        active_input = null;

        var prev_tab_id = $('#quality-inspection-modal .nav-tabs li > .active').parent().prev().find('a[data-toggle="tab"]').attr('id');
        if (prev_tab_id == 'tabitem') {
          $('#quality-inspection-modal .nav-tabs .nav-item > .active').parent().prev().find('.custom-tabs-1').tab('show');
        } else {
          var next_tab_id = $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').attr('id');
          if(next_tab_id != 'tablast'){
            $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').removeClass('custom-tabs-1').addClass('active');
          }else{
            $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').addClass('custom-tabs-1').removeClass('active');
          }
          $('#quality-inspection-modal .nav-tabs .nav-item > .active').parent().prev().find('.custom-tabs-1').tab('show');
        }
      });

      $(document).on('click', '#quality-inspection-modal .select-item-for-inspection-btn', function(e) {
        e.preventDefault();
        var img = $(this).data('img');
        var item_code = $(this).data('item-code');
        var item_description = $(this).data('item-desc');
        var required_qty = $(this).data('required-qty');

        $('#quality-inspection-modal .selected-item-image').attr('src', img).attr('alt', item_code);
        $('#quality-inspection-modal .selected-item-code').text(item_code);
        $('#quality-inspection-modal .selected-item-description').text(item_description);
        $('#quality-inspection-modal .selected-item-required-qty').val(required_qty);
        $('#quality-inspection-modal .selected-item-tbl-cell').removeClass('d-none');
        $('#quality-inspection-modal .selected-item-code-val').val(item_code);

        $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').tab('show');
      });
    });
  </script>
</body>
</html>