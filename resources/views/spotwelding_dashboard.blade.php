@extends('layouts.app', [
    'namePage' => 'MES',
    'activePage' => 'operator_dashboard',
])
@section('content')
<div class="panel-header">
  <div class="header text-center" style="margin-top: -65px;">
    <div class="row">
      <div class="col-md-12 text-white">
        <table style="text-align: center; width: 87%;">
          <tr>
            <td style="width: 18%; border-right: 5px solid white;" rowspan="2">
              <h2 class="title">
                <div class="pull-left" style="margin-left: 25px;">
                  <span style="display: block; font-size: 14pt;">{{ date('M-d-Y') }}</span>
                  <span style="display: block; font-size: 10pt;">{{ date('l') }}</span>
                </div>
              </h2>
            </td>
            <td style="width: 20%; border-right: 5px solid white;" rowspan="2">
              <h2 class="title" style="margin: auto; font-size: 18pt;"><span id="current-time">--:--:-- --</span></h2>
            </td>
            <td style="width: 25%;" rowspan="2">
              <h2 class="title text-left" style="margin: auto 10px; font-size: 18pt;">{{ $workstation }}</h2>
            </td>
            <td style="width: 37%;">
              <h5 class="card-title" style="font-size: 12pt; margin: 0;">
                <span class="dot" style="background-color: {{ $machine_details->status == 'Available' ? '#28B463' : '#717D7E' }};"></span>
                <span style="font-size: 12pt;">{{ $machine_details->machine_name }} [{{ $machine_details->machine_code }}]</span>
              </h5>
            </td>
          </tr>
          <tr>
            <td>Operator: @if(Auth::user()){{ Auth::user()->employee_name }}@endif</td>
          </tr>
        </table>
      </div>
    </div>
    <input type="hidden" id="workstation_forlogout" value="{{ $workstation }}">
    <input type="hidden" id="current-production-order" value="{{ $job_ticket_details->production_order }}">
  </div>
</div>
<div class="content" style="margin-top: -184px;">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
      
        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <ul class="nav nav-tabs" role="tablist" style="font-size: 10pt;">
                <li class="nav-item">
                  <a class="nav-link active" id="current-production-order-tab" data-toggle="tab" href="#current-tab" role="tab">
                    <b>Current Job Ticket</b></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="production-queue-tab" data-toggle="tab" href="#production-queue" role="tab"><b>Pending Task(s)</b></a>
                </li>
              </ul>
              <div class="tab-content" style="min-height: 550px;">
                <div class="tab-pane active" id="current-tab" role="tabpanel">
                  <div id="current-task"></div>
                </div>
                <div class="tab-pane" id="production-queue" role="tabpanel">
                  <div class="table-responsive">  
                    <div id="assigned-tasks-table"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style type="text/css">
  #assigned-tasks-table tbody:nth-child(odd) {
    background-color: #f2f2f2;
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

  .dot {
    height: 12px;
    width: 12px;
    background-color: #bbb;
    border-radius: 50%;
    display: inline-block;
  }

  .text-blink1 {
    color: red;
    animation: blinker 1s linear infinite;
  }

  .text-blink2 {
    color: green;
    animation: blinker 1s linear infinite;
  }
  #tbl_machine_list{
    overflow-y: hidden;
  }

  @keyframes blinker {  
    50% { opacity: 0; }
  }
</style>

  <!-- Modal -->
  <div class="modal fade" id="view-operator-task-modal" tabindex="-1" role="dialog">
     <div class="modal-dialog modal-lg" role="document" style="min-width: 85%;">
        <div class="modal-content">
           <div class="modal-header text-white" style="background-color: #0277BD;">
              <h5 class="modal-title">Operator Task List</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
              </button>
           </div>
           <div class="modal-body" style="min-height: 500px;">
              <div class="row">
                <div class="col-md-12">
                  <table class="table table-striped" style="width: 100%;" id="view-operator-task-table">
                    <thead class="text-primary text-center" style="font-size: 7pt;">
                      <tr>
                        <th class="font-weight-bold">Operator</th>
                        <th class="font-weight-bold">Process</th>
                        <th class="font-weight-bold">Machine</th>
                        <th class="font-weight-bold">Start Time</th>
                        <th class="font-weight-bold">End Time</th>
                        <th class="font-weight-bold">Completed Qty</th>
                        <th class="font-weight-bold">Status</th>
                      </tr>
                    </thead>
                    <tbody style="font-size: 9pt;"></tbody>
                  </table>
                </div>
              </div>
           </div>
        </div>
     </div>
  </div>
  
    <div class="modal fade" id="view-helpers-modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title">Helpers</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body" style="min-height: 500px;">
               <div class="row">
                 <div class="col-md-12">
                   <table class="table table-striped" style="width: 100%;" id="view-helpers-table">
                    <col style="width: 15%;">
                    <col style="width: 55%;">
                    <col style="width: 25%;">
                     <thead class="text-primary text-center" style="font-size: 7pt;">
                       <tr>
                        <th class="font-weight-bold">No.</th>
                         <th class="font-weight-bold">Helper Name</th>
                         <th class="font-weight-bold">Action</th>
                       </tr>
                     </thead>
                     <tbody style="font-size: 9pt;"></tbody>
                   </table>
                 </div>
               </div>
            </div>
         </div>
      </div>
    </div>
    
    <div class="modal fade" id="add-helper-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-white" style="background-color: #0277BD;">
            <h5 class="modal-title">Add Helper</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" style="min-height: 480px;">
            <form>
              @csrf
              <div class="row">
                <div class="col-md-12">
                  <div class="row">
                    <div class="col-md-10 offset-md-1">
                      <h6 class="text-center">Scan Helper ID</h6>
                      <div class="row">
                        <div class="col-md-10">
                          <div class="form-group">
                            <input type="hidden" name="time_log_id">
                            <input type="text" class="form-control" id="helper-id" name="helper_id" style="font-size: 15pt;" required>
                          </div>
                        </div>
                        <div class="col-md-2" style="padding: 0; margin-top: -15px;">
                          <center>
                            <img src="{{ asset('img/tap.gif') }}" width="260" height="60" id="toggle-helper-numpad">
                          </center>
                        </div>
                      </div>
                      <div id="helper-numpad" style="display: none;">
                        <div class="text-center">
                          <div class="row1">
                            <span class="numpad num">1</span>
                            <span class="numpad num">2</span>
                            <span class="numpad num">3</span>
                          </div>
                          <div class="row1">
                            <span class="numpad num">4</span>
                            <span class="numpad num">5</span>
                            <span class="numpad num">6</span>
                          </div>
                          <div class="row1">
                            <span class="numpad num">7</span>
                            <span class="numpad num">8</span>
                            <span class="numpad num">9</span>
                          </div>
                          <div class="row1">
                            <span class="numpad del"><</span>
                            <span class="numpad num">0</span>
                            <span class="numpad clear">Clear</span>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-10 offset-md-1">
                            <button type="submit" class="btn btn-block btn-primary btn-lg">SUBMIT</button>
                          </div>
                        </div>
                      </div>
                      <div id="helper-scan-img">
                        <center>
                          <img src="{{ asset('img/operator-id.png') }}" width="280" height="200" style="margin: 40px 10px 10px 10px;">
                        </center>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal fade" id="delete-helper-modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <form>
          @csrf
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Remove Helper</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12">
                  <input type="hidden" name="job_ticket_id">
                  <input type="hidden" name="helper_id">
                  <input type="hidden" name="helper_name">
                  <div class="row">
                    <div class="col-md-12 text-center">
                      Remove <b><span class="helper-name"></span></b> as helper?
                    </div>
                  </div>
                </div>
              </div>
                </div>
                <div class="modal-footer">
                   <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal" style="padding-top: -100px;">Cancel</button>
                   &nbsp;
                   <button type="submit" class="btn btn-danger btn-lg">Remove</button>
                </div>
             </div>
          </form>
       </div>
    </div>
  
  
    <div class="modal fade" id="view-helpers-modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title">Helpers</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body" style="min-height: 500px;">
               <div class="row">
                 <div class="col-md-12">
                   <table class="table table-striped" style="width: 100%;" id="view-helpers-table">
                    <col style="width: 15%;">
                    <col style="width: 55%;">
                    <col style="width: 25%;">
                     <thead class="text-primary text-center" style="font-size: 7pt;">
                       <tr>
                        <th class="font-weight-bold">No.</th>
                         <th class="font-weight-bold">Helper Name</th>
                         <th class="font-weight-bold">Action</th>
                       </tr>
                     </thead>
                     <tbody style="font-size: 9pt;"></tbody>
                   </table>
                 </div>
               </div>
            </div>
         </div>
      </div>
    </div>
    
    <div class="modal fade" id="add-helper-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-white" style="background-color: #0277BD;">
            <h5 class="modal-title">Add Helper</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" style="min-height: 480px;">
            <form>
              @csrf
              <div class="row">
                <div class="col-md-12">
                  <div class="row">
                    <div class="col-md-10 offset-md-1">
                      <h6 class="text-center">Scan Helper ID</h6>
                      <div class="row">
                        <div class="col-md-10">
                          <div class="form-group">
                            <input type="hidden" name="job_ticket_id">
                            <input type="text" class="form-control" id="helper-id" name="helper_id" style="font-size: 15pt;" required>
                          </div>
                        </div>
                        <div class="col-md-2" style="padding: 0; margin-top: -15px;">
                          <center>
                            <img src="{{ asset('img/tap.gif') }}" width="260" height="60" id="toggle-helper-numpad">
                          </center>
                        </div>
                      </div>
                      <div id="helper-numpad" style="display: none;">
                        <div class="text-center">
                          <div class="row1">
                            <span class="numpad num">1</span>
                            <span class="numpad num">2</span>
                            <span class="numpad num">3</span>
                          </div>
                          <div class="row1">
                            <span class="numpad num">4</span>
                            <span class="numpad num">5</span>
                            <span class="numpad num">6</span>
                          </div>
                          <div class="row1">
                            <span class="numpad num">7</span>
                            <span class="numpad num">8</span>
                            <span class="numpad num">9</span>
                          </div>
                          <div class="row1">
                            <span class="numpad del"><</span>
                            <span class="numpad num">0</span>
                            <span class="numpad clear">Clear</span>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-10 offset-md-1">
                            <button type="submit" class="btn btn-block btn-primary btn-lg">SUBMIT</button>
                          </div>
                        </div>
                      </div>
                      <div id="helper-scan-img">
                        <center>
                          <img src="{{ asset('img/operator-id.png') }}" width="280" height="200" style="margin: 40px 10px 10px 10px;">
                        </center>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal fade" id="delete-helper-modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <form>
          @csrf
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Remove Helper</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12">
                  <input type="hidden" name="job_ticket_id">
                  <input type="hidden" name="helper_id">
                  <input type="hidden" name="helper_name">
                  <div class="row">
                    <div class="col-md-12 text-center">
                      Remove <b><span class="helper-name"></span></b> as helper?
                    </div>
                  </div>
                </div>
              </div>
                </div>
                <div class="modal-footer">
                   <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal" style="padding-top: -100px;">Cancel</button>
                   &nbsp;
                   <button type="submit" class="btn btn-danger btn-lg">Remove</button>
                </div>
             </div>
          </form>
       </div>
    </div>

@include('modals.end_task')
@include('modals.restart_task')
@include('modals.machine_breakdown_form')
@include('modals.enter_reject_spotwelding')
@include('modals.logout_confirmation_modal')
@include('quality_inspection.modal_inspection')

<div class="modal fade" id="confirm-sample-size-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #f57f17;;">
        <h5 class="modal-title" id="modal-title ">Sample Size</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <input type="hidden" id="sample-size-tab-id">
            <h5 class="text-center" style="margin: 0;">Recommended Sample Size: <span class="sample-size font-weight-bold">0</span></h5>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-lg" id="confirm-sample-size-btn">OK</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="select-process-for-inspection-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document" style="min-width: 95%;">
    <div class="modal-content">
      <div class="text-white rounded-top" style="background-color: #f57f17;">
        <div class="d-flex flex-row justify-content-between pt-2 pb-2 pr-3 pl-3 align-items-center">
          <h5 class="font-weight-bold m-0 p-0" style="font-size: 14pt;">In Process - Quality Inspection</h5>
          <div class="float-right">
            <h5 class="modal-title font-weight-bold p-0 mr-3 font-italic d-inline-block" style="font-size: 14pt;">{{ $workstation }} - <span class="production-order"></span></h5>
            <button type="button" class="close d-inline-block ml-3" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        </div>
      </div>
      <div class="modal-body p-2" style="min-height: 480px;">
        <div id="tasks-for-inspection-tbl"></div>
      </div>
    </div>
  </div>
</div>



@endsection
@section('script')
<script src="{{ asset('/js/jquery.rfid.js') }}"></script>
<script>
  $(document).ready(function(){
    var active_input = null;
    
    $(document).on('focus', '#quality-inspection-frm input[type=text]', function() {
      $('.custom-selected-active-input').removeClass('custom-bg-selected-active-input text-white font-weight-bold');
      if ($(this).data('qa') !== undefined) {
        $(this).closest('.d-flex').addClass('custom-selected-active-input custom-bg-selected-active-input text-white font-weight-bold');
      }
      
      if($(this).data('edit') > 0){
        active_input = $(this).attr('id');
      }else{
        active_input = null;
      }
    });

    $(document).on('click', '#quality-inspection-frm .num', function() {
      $("#" + active_input).focus();
      var input = $('#quality-inspection-frm #' + active_input);
      var x = input.val();
      var y = $(this).text();
  
      if (x == '0' && y != '.') {
        x = '';
      }

      if((x.indexOf(".") > -1) && y == "."){
        return false;
      }

      if (x == '0' && y == '.') {
        x = '0';
      }
      
      input.val(x + y);
    });

    $(document).on('click', '#quality-inspection-frm .clear', function() {
      $("#" + active_input).focus();
      var input = $('#quality-inspection-frm #' + active_input);
      input.val(0);
    });

    $(document).on('click', '#quality-inspection-frm .del', function() {
      $("#" + active_input).focus();
      var input = $('#quality-inspection-frm #' + active_input);
      var x = input.val();
 
      input.val(x.substring(0, x.length - 1));
  
      if (input.val().length == 0) {
        input.val(0);
      }
    });

    $('#confirm-sample-size-btn').click(function(e){
      e.preventDefault();
      var tab_id = $('#sample-size-tab-id').val();
      $('#' + tab_id + '-validated-sample-size').val(1);
      $('#' + tab_id + '-next-btn').trigger('click');
      $('#confirm-sample-size-modal').modal('hide');
    });

    $(document).on('click', '#quality-inspection-modal .toggle-manual-input', function(e){
      $('#quality-inspection-modal img').slideToggle();
      $('#quality-inspection-modal .manual').slideToggle();
    });

    $('#quality-check-modal-btn').click(function(e){
      e.preventDefault();
      $('#scan-jt-for-qc-modal').modal('show');
    });

    $(document).on('click', '#scan-jt-for-qc-modal .num', function(e){
      e.preventDefault();
      var num = $(this).text();
      var current = $('#scan-jt-for-qc-modal input[type="text"]').val();
      var new_input = current + num;
         
      $('#scan-jt-for-qc-modal input[type="text"]').val(new_input);
    });

    $('#toggle-jt-numpad-qc').click(function(e){
      e.preventDefault();
      $('#scan-jt-for-qc-modal #jt-numpad-qc').slideToggle();
      $('#scan-jt-for-qc-modal #jt-scan-img-qc').slideToggle();
    });

    $('#submit-enter-production-order-qc').click(function(e){
      e.preventDefault();
      var production_order = 'PROM-' + $('#production-order-qc').val();
      get_tasks_for_inspection(workstation, production_order);
    });

    function get_tasks_for_inspection(workstation, production_order, timelogid = null){
      $.ajax({
        url:"/get_tasks_for_inspection/" + workstation +"/" + production_order + "?timelogid=" + timelogid,
        type:"GET",
        success:function(data){
          if(data.success == 0){
            showNotification("danger", data.message, "now-ui-icons travel_info");
            return false;
          }

          $('#select-process-for-inspection-modal').modal('show');
          $('#select-process-for-inspection-modal .production-order').text(production_order);
          $('#tasks-for-inspection-tbl').html(data);
        }
      });
    }

    $(document).on('click', '.quality-inspection-btn-op', function(e){
      e.preventDefault();
      var production_order = $(this).data('production-order');
      var workstation = '{{ $workstation }}';
      var time_log_id = $(this).data('timelog-id');
      get_tasks_for_inspection(workstation, production_order, time_log_id);
    });

    $(document).on('submit', '#quality-inspection-frm', function(e){
      e.preventDefault();

      $('#quality-inspection-frm button[type="submit"]').attr('disabled', true);
     
      $.ajax({
        url: $(this).attr('action'),
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success) {
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            get_tasks_for_inspection(data.details.workstation, data.details.production_order, data.details.timelogid);
              if (data.details.checklist_url) {
                $.ajax({
                  url: data.details.checklist_url,
                  type:"GET",
                  success:function(response){
                    active_input = null;
                    $('#quality-inspection-div').html(response);
                  }, error: function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                  },
                });
              } else {
                $('#quality-inspection-modal').modal('hide');
              }
          }else{
            showNotification("danger", data.message, "now-ui-icons travel_info");
            $('#quality-inspection-frm button[type="submit"]').removeAttr('disabled');
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
      });
    });

    $(document).on('click', '.quality-inspection-btn', function(e){
        e.preventDefault();

        $('#quality-inspection-frm button[type="submit"]').removeAttr('disabled');

        var production_order = $(this).data('production-order');
        var process_id = $(this).data('processid');
        var workstation = $(this).data('workstation');
        var inspection_type = $(this).data('inspection-type');
        var reject_category = $(this).data('reject-cat');

        var data = {
          time_log_id: $(this).data('timelog-id'),
          inspection_type,
          reject_category,
        }

        $.ajax({
          url: '/get_checklist/' + workstation + '/' + production_order + '/' + process_id,
          type:"GET",
          data: data,
          success:function(response){
            active_input = null;
            $('#quality-inspection-div').html(response);
            $('#quality-inspection-modal .qc-type').text(inspection_type);
            $('#quality-inspection-modal .qc-workstation').text('[' + workstation + ']');
            $('#quality-inspection-modal').modal('show');
          }, error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
          },
        });
      });
  

    $(document).on('click', '.custom-a', function(e){
      e.preventDefault();
      location.assign($(this).attr('href'));
    });

    $(document).on('change', '.select-all-checklist-per-tab', function(e){
      e.preventDefault();
      var selector = '.' + $(this).attr('id');
      $(selector).not(this).prop('checked', this.checked);
    });

    $(document).on('click', '.add-helper-btn', function(e){
          e.preventDefault();
          var time_log_id = $(this).data('timelog-id');
          var operator_id = $(this).data('operator-id');
          $('#add-helper-modal input[name="time_log_id"]').val(time_log_id);
    
          $('#add-helper-modal').modal('show');
        });
    
        $('#toggle-helper-numpad').click(function(e){
          e.preventDefault();
          $('#helper-numpad').slideToggle();
          $('#helper-scan-img').slideToggle();
        });
    
        $(document).on('click', '#helper-numpad .num', function(e){
          e.preventDefault();
          var num = $(this).text();
          var current = $('#helper-id').val();
          var new_input = current + num;
             
          $('#helper-id').val(new_input);
        });
    
        $(document).on('click', '#helper-numpad .clear', function() {
          $('#helper-id').val('');
        });
    
        $(document).on('click', '#helper-numpad .del', function() {
          var input = $('#helper-id');
          var x = input.val();
     
          input.val(x.substring(0, x.length - 1));
      
          if (input.val().length == 0) {
            input.val('');
          }
        });
    
        $(document).on('submit', '#add-helper-modal form', function(e){
          e.preventDefault();
          $.ajax({
            url:"/add_helper",
            type:"POST",
            data: $(this).serialize(),
            success:function(data){
              if (data.success) {
                showNotification("success", data.message, "now-ui-icons ui-1_check");
                get_current_job_ticket_tasks();
                $('#add-helper-modal').modal('hide');     
              }else{
                showNotification("danger", data.message, "now-ui-icons travel_info");
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
        });
    
       $(document).on('click', '.view-helpers-btn', function(e){
        e.preventDefault();
        var data = {
          'time_log_id' : $(this).data('timelog-id'),
          'job_ticket_id' : $(this).data('jobticket-id'),
          'machine' : $(this).data('machine'),
          'display_all' : $(this).data('display-all'),
          'operator_id' : $(this).data('operator')
        }
  
        get_helpers(data);
      });
  
      function get_helpers(arr){
        $('#view-helpers-table tbody').empty();
        $.ajax({
          url:"/get_helpers",
          type:"GET",
          data: arr,
          success:function(data){
            var row = '';
            if(data.length > 0){
              $.each(data, function(i, d){
                var n = i + 1;
                row += '<tr><td class="text-center">' + n + '</td>'+
                  '<td class="text-center">' + d.helper_name + '</td>'+
                  '<td class="text-center"><button type="button" class="btn btn-danger delete-helper-btn" data-id="' + d.helper_id + '" data-jobticket-id="' + d.job_ticket_id + '">' +
                    '<i class="now-ui-icons ui-1_simple-remove"></i> Remove' +
                  '</button></td></tr>';
              });
            }else{
              row = '<tr><td class="text-center" colspan=3>No helper(s) found.</td></td></tr>';
            }
            
            $('#view-helpers-table tbody').append(row);
            $('#view-helpers-modal').modal('show');
          }
        });
      }
    
        $(document).on('click', '.delete-helper-btn', function(e){
          e.preventDefault();
          var helper_name = $(this).closest('tr').find('td').eq(1).text();
          var helper_id = $(this).data('id');
          $('#delete-helper-modal input[name="job_ticket_id"]').val($(this).data('jobticket-id'));
          $('#delete-helper-modal input[name="helper_id"]').val(helper_id);
          $('#delete-helper-modal input[name="helper_name"]').val(helper_name);
          $('#delete-helper-modal .helper-name').text(helper_name);
          $('#delete-helper-modal').modal('show');
        });
    
        $(document).on('submit', '#delete-helper-modal form', function(e){
          e.preventDefault();
          $.ajax({
            url:"/delete_helper",
            type:"POST",
            data: $(this).serialize(),
            success:function(data){
              if (data.success) {
                showNotification("success", data.message, "now-ui-icons ui-1_check");
                get_helpers(data.job_ticket_id);
                get_current_job_ticket_tasks();
                $('#delete-helper-modal').modal('hide');     
              }else{
                showNotification("warning", data.message, "now-ui-icons travel_info");
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
        });

        $(document).on('click', '.reload-btn', function(e){
          e.preventDefault();
          get_current_job_ticket_tasks();
        });
  
      $(document).on('click', '.view-in-progress-operator', function(e){
        e.preventDefault();
        var job_ticket_id = $(this).data('jobticket-id');
        var operator_id = $(this).data('operator-id');
  
        $('#view-operator-task-table tbody').empty();
        $.ajax({
          url:"/view_spotwelding_operator_task/"+job_ticket_id+"/"+operator_id,
          type:"GET",
          success:function(data){
            var row = '';
            $.each(data, function(i, d){
            var completed_qty = (d.completed_qty > 0) ? d.completed_qty : '-';
            var status_class = (d.status == 'Completed') ? 'badge-success' : 'badge-warning';
              row += '<tr><td class="text-center">' + d.operator_name + '</td>'+
                '<td class="text-center">' + d.process_description + '</td>'+ 
                '<td class="text-center">' + d.machine_code + '</td>'+ 
                '<td class="text-center">' + d.from_time + '</td>'+
                '<td class="text-center">' + d.to_time + '</td>' +
                '<td class="text-center">' + completed_qty + '</td>'+
                '<td class="text-center" style="font-size: 12pt;"><span class="badge ' + status_class + ' text-white">' + d.status + '</span></td></tr>';
            });
  
            $('#view-operator-task-table tbody').append(row);
            $('#view-operator-task-modal').modal('show');
          }
        });
      });

    get_current_job_ticket_tasks();
    // setInterval(getAssignedTasks, 5000);

    $('.modal').on('hidden.bs.modal', function(){
      var frm = $(this).find('form')[0];
      if (frm) frm.reset();
    });

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $(document).on('click', '#production-queue-tab', function (e){
      getAssignedTasks();
    });

    function getAssignedTasks(){
      $.ajax({
        url:"/get_assigned_tasks/{{ $workstation }}/{{ $machine_details->machine_code }}",
        type:"GET",
        success:function(data){
          $('#assigned-tasks-table').html(data);
        }
      });  
    }
    
    function get_current_job_ticket_tasks(){
      var data = {
        workstation: '{{ $workstation }}',
        machine_code: '{{ $machine_details->machine_code }}',
        production_order: '{{ $job_ticket_details->production_order }}',
        job_ticket_id: '{{ $job_ticket_details->job_ticket_id }}'
      }

      $.ajax({
        url:"/get_spotwelding_current_operator_task_details/{{ Auth::user()->user_id }}",
        type:"GET",
        data: data,
        success:function(data){
          $('#current-task').html(data);
        }
      });
    }

    $(document).on('click', '.end-task-btn', function(e){
      e.preventDefault();

      var data = {
        'time_log_id': $(this).data('timelog-id'),
        _token: '{{ csrf_token() }}', 
        'job_ticket_id': $(this).data('jobticket'),
        'qty_to_manufacture': $(this).data('reqqty'),
        'spotwelding_part_id': $(this).data('spotwelding-part-id')
      }

      $.ajax({
        url: '/get_spotwelding_part_remaining_qty',
        type:"GET",
        data: data,
        success:function(data){
          $('#end-task-modal .balance-qty').val(data);
          $('#end-task-modal .max-qty').text(data);
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });

      // var max_qty = $(this).data('balance-qty');
      var production_order = $('#current-production-order').val();
      $('#end-task-modal .timelog-id').val($(this).data('timelog-id'));
      $('#end-task-modal .process-name').text($(this).data('process-name'));
      // $('#end-task-modal .max-qty').text(max_qty);
      $('#end-task-modal .production-order').text(production_order);
      $('#end-task-modal .production-order-input').val(production_order);
      $('#end-task-modal .workstation-input').val('{{ $workstation }}');
      // $('#end-task-modal .balance-qty').val(max_qty);
      $('#end-task-modal').modal('show');
    });

    $('#end-task-modal .num').on('click', function() {
      var input = $('#end-task-modal #completed-qty');
      var x = input.val();
      var y = $(this).text();
  
      if (x == 0) {
        x = '';
      }
      
      input.val(x + y);
    });

    $('#end-task-modal .clear').on('click', function() {
      $('#end-task-modal #completed-qty').val(0);
    });

    $('#end-task-modal .del').on('click', function() {
      var input = $('#end-task-modal #completed-qty');
      var x = input.val();
 
      input.val(x.substring(0, x.length - 1));
  
      if (input.val().length == 0) {
        input.val(0);
      }
    });

    $('#end-task-frm').submit(function(e){
      e.preventDefault();
      var balance_qty = $('#end-task-frm input[name="balance_qty"]').val();
      var completed_qty = $('#end-task-frm input[name="completed_qty"]').val();
      
      if(parseInt(completed_qty) <= 0){
        showNotification("danger", "Completed Qty cannot be less than or equal to <b>0</b>", "now-ui-icons travel_info");
        return false;
      }

      if(parseInt(completed_qty) > parseInt(balance_qty)){
        showNotification("danger", "Completed Qty cannot be greater than <b>" + balance_qty + "</b>", "now-ui-icons travel_info");
        return false;
      }

      $.ajax({
        url: '/end_spotwelding',
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            get_current_job_ticket_tasks();
            $('#end-task-modal').modal('hide');
            //var workstation_name = $('#workstation_forlogout').val();
            //window.location.href = "/logout_operator/" + workstation_name;
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });

    $(document).on('click', '.restart-task-btn', function(e){
      e.preventDefault();
      $('#restart-task-modal .timelog-id').val($(this).data('timelog-id'));
      $('#restart-task-modal .workstation').html('[<b>{{ $workstation }}</b>]');
      $('#restart-task-modal .operator-name').val($($(this).data('tabid') + ' .operator-name').text());
      $('#restart-task-modal').modal('show');
    });

    $(document).on('click', '.machine-breakdown-modal-btn', function(e){
      e.preventDefault();
      $('#report-machine-breakdown-modal').modal('show');
    });

     $(document).on('click', '.machine-breakdown-modal-btn', function(e){
      $("#machine-breakdown-frm").trigger("reset");
      loadbreakdown_validation();

    });

    $('#restart-task-frm').submit(function(e){
      e.preventDefault();
      $.ajax({
        url: '/restart_spotwelding',
        type:"POST",
        data: $(this).serialize(),
        success:function(response){
          if (response.success < 1) {
            showNotification("danger", response.message, "now-ui-icons travel_info");
          }else{
            get_current_job_ticket_tasks();
            showNotification("success", response.message, "now-ui-icons ui-1_check");
            $('#restart-task-modal').modal('hide');
          }
        }
      });
    });

    $(document).on('click', '.continue-log-btn', function(e){
      e.preventDefault();

      var data = {  
        machine_code: '{{ $machine_details->machine_code }}', 
        _token: '{{ csrf_token() }}', 
      }

      if(typeof($(this).data('is-rework')) != "undefined" && $(this).data('is-rework') !== null) {
        data.is_rework = 1;
      }

      $.ajax({
        url: "/continue_log_task/" + $(this).data('timelog-id'),
        type:"POST",
        data: data,
        success:function(response){
          if (response.success > 0) {
            get_current_job_ticket_tasks();
            showNotification("success", response.message, "now-ui-icons ui-1_check");
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

    $(document).on('click', '.start-task-btn', function(e){
      e.preventDefault();
      $(this).prop('disabled', true);
      var count_selected_parts = $('#select-part-div .selected-part').length;
      if (count_selected_parts <= 1) {
        $(this).prop('disabled', false);
        $(this).removeProp('disabled');
        showNotification("danger", 'Please select parts.', "now-ui-icons travel_info");
        return false;
      }

      var btn = $(this);

      var process_description = '';
      var parts = [];
      $('#select-part-div .selected-part').each(function(){
        process_description += $(this).find('span').eq(0).text() + ',';
        parts.push({
          part_code: $(this).find('.part-item-code').eq(0).text(),
          category: $(this).find('.part-category').eq(0).text(),
          production_order: $(this).find('.part-production-order').eq(0).text(),
        });
      });

      var data = {  
        operator_id: '{{ Auth::user()->user_id }}', 
        production_order: $('#current-production-order').val(), 
        process_id: $(this).data('process-id'), 
        machine_code: '{{ $machine_details->machine_code }}', 
        _token: '{{ csrf_token() }}', 
        job_ticket_id: $(this).data('jobticket-id'),
        process_description: process_description,
        parts: parts,
        ho_code: $(this).data('ho-code'),
        reference_no: $(this).data('refno'),
        qty_to_manufacture: $(this).data('reqqty')
      }

      $.ajax({
        url: "/start_spotwelding",
        type:"POST",
        data: data,
        success:function(response){
          $(this).prop('disabled', false);
          $(this).removeProp('disabled');
          if (response.success > 0) {
            get_current_job_ticket_tasks();
            showNotification("success", response.message, "now-ui-icons ui-1_check");
          }else{
            showNotification("danger", response.message, "now-ui-icons travel_info");
          }
          btn.prop('disabled', false);
          btn.removeProp('disabled');
        }, 
        error: function(jqXHR, textStatus, errorThrown) {
          btn.prop('disabled', false);
          btn.removeProp('disabled');
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });

    $(document).on('click', '.enter-reject-btn', function(e){
      e.preventDefault();
      var production_order = $('#current-production-order').val();
      $('#enter-reject-modal .production-order').text(production_order);
      $('#enter-reject-modal .production-order-input').val(production_order);
      $('#enter-reject-modal .workstation-input').val('{{ $workstation }}');
      
      $('#enter-reject-modal .max-qty').text($(this).data('good-qty'));
      $('#enter-reject-modal #sel-batch-div').attr('hidden', true);
      $('#enter-reject-modal .timelog-id').val($(this).data('id'));
      $('#enter-reject-modal .max-qty').text($(this).data('good-qty'));
      $('#enter-reject-modal .process-name').text($(this).data('process-name'));
      $('#enter-reject-modal .per-row-reject').val($(this).data('row'));
      $('#enter-reject-modal .good-qty-input').val($(this).data('good-qty'));
      var process_id = $(this).data('processid');
      get_reject_types(process_id);
      $('#enter-reject-modal').modal('show');
    });

    function get_reject_types(process_id){
      $('#rejected-type-sel').empty();
      $.ajax({
        url: "/get_reject_types/Spotwelding/"+ process_id,
        type:"GET",
        success:function(data){
          $(".spotwelding_reject_list").html(data);
        }, 
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    }

    $('#reject-task-frm').submit(function(e){
      e.preventDefault();
      if(!$('#enter-reject-modal .timelog-id').val()){
        showNotification("danger", 'Please select batch.', "now-ui-icons travel_info");
        return false;
      }

      var rejected_qty = $('#enter-reject-modal #rejected-qty').val();
      var good_qty = $('#enter-reject-modal .max-qty').text();
      if(parseInt(rejected_qty) <= 0){
        showNotification("danger", "Rejected Qty cannot be less than or equal to <b>0</b>", "now-ui-icons travel_info");
        return false;
      }

      if(parseInt(rejected_qty) > parseInt(good_qty)){
        showNotification("danger", "Rejected Qty cannot be greater than <b>" + good_qty + "</b>", "now-ui-icons travel_info");
        return false;
      }

      $.ajax({
        url: $(this).attr("action"),
        type:"POST",
        data: $(this).serialize(),
        success:function(response){
          if (response.success < 1) {
            showNotification("danger", response.message, "now-ui-icons travel_info");
          }else{
            get_current_job_ticket_tasks();
            showNotification("success", response.message, "now-ui-icons ui-1_check");
            $('#enter-reject-modal').modal('hide');
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });

   $('#reject-sel-batch').change(function(e){
      e.preventDefault();
      if($(this).val()){
        var good = $(this).find(':selected').data('good');
        var process_name = $(this).find(':selected').data('process');
        $('#enter-reject-modal .max-qty').text(good);
        $('#enter-reject-modal .timelog-id').val($(this).val());
        $('#enter-reject-modal .process-name').text(process_name);
      }else{
        $('#enter-reject-modal .max-qty').text('0');
        $('#enter-reject-modal .timelog-id').val('');
        $('#enter-reject-modal .process-name').text('--');
      }
    });

    $('#enter-reject-modal .num').on('click', function() {
      var input = $('#enter-reject-modal #rejected-qty');
      var x = input.val();
      var y = $(this).text();
  
      if (x == 0) {
        x = '';
      }
      
      input.val(x + y);
    });

    $('#enter-reject-modal .clear').on('click', function() {
      $('#enter-reject-modal #rejected-qty').val(0);
    });

    $('#enter-reject-modal .del').on('click', function() {
      var input = $('#enter-reject-modal #rejected-qty');
      var x = input.val();
 
      input.val(x.substring(0, x.length - 1));
  
      if (input.val().length == 0) {
        input.val(0);
      }
    });

    var rfidParser = function (rawData) {
      // if (rawData.length < 12) return null;
      return rawData;
   };

   // Called on a good scan (company card recognized)
   var goodScan = function (cardData) {
    if($('#add-helper-modal').is(':visible') == true){
          $('#helper-id').val(cardData);
          $('#add-helper-modal form').submit();
    
          return false;
         }
         
   if ($('#quality-inspection-modal').is(':visible') == true) {
          var active_tab = $("#quality-inspection-modal ul.nav-tabs li a.active").attr('id');
          if(active_tab == 'tablast'){
            $('#quality-inspection-modal #inspected-by').val(cardData);
            $('#quality-inspection-frm').submit();
          }
  
          return false;
        }
   };

    // Called on a bad scan (company card not recognized)
    var badScan = function() {
      console.log("Bad Scan.");
    };

    $.rfidscan({
      parser: rfidParser,
      success: goodScan,
      error: badScan
    });
   


 
    $('#machine-breakdown-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#report-machine-breakdown-modal').modal('hide');
          }
        }
      });
    });


    $('.modal').on('shown.bs.modal', function() {
      $(this).find('[autofocus]').focus();
    });

    $(document).on('show.bs.modal', '.modal', function (event) {
      var zIndex = 1040 + (10 * $('.modal:visible').length);
      $(this).css('z-index', zIndex);
      setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
      }, 0);
    });

    $('#logout_click').click(function(e){
      e.preventDefault();
      window.location.href = "/logout_spotwelding";
    });


    function showNotification(color, message, icon){
      $.notify({
        icon: icon,
        message: message
      },{
        type: color,
        timer: 1000,
        placement: {
          from: 'top',
          align: 'center'
        }
      });
    }
    
    setInterval(updateClock, 1000);
    function updateClock(){
         var currentTime = new Date();
         var currentHours = currentTime.getHours();
         var currentMinutes = currentTime.getMinutes();
         var currentSeconds = currentTime.getSeconds();
         // Pad the minutes and seconds with leading zeros, if required
         currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
         currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;
         // Choose either "AM" or "PM" as appropriate
         var timeOfDay = (currentHours < 12) ? "AM" : "PM";
         // Convert the hours component to 12-hour format if needed
         currentHours = (currentHours > 12) ? currentHours - 12 : currentHours;
         // Convert an hours component of "0" to "12"
         currentHours = (currentHours === 0) ? 12 : currentHours;
         currentHours = (currentHours < 10 ? "0" : "") + currentHours;
         // Compose the string for display
         var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;

         $("#current-time").html(currentTimeString);
    }

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
  });
</script>
<script type="text/javascript">

    $('#category').change(function() {
      if( this.value == 'Breakdown')
      {
        $("#breakdown_reason_div").show();
        $("#corrective_reason_div").attr("style", "display:none");
         $("#warning_div").show();
        $("#corrective_reason").prop('required',false);

      }
      else if(this.value == 'Corrective')
      {
        $("#corrective_reason_div").show();
        $("#breakdown_reason_div").attr("style", "display:none");
        $("#warning_div").attr("style", "display:none");
        $("#corrective_reason").prop('required',true);
      }
      else{
        $("#breakdown_reason_div").attr("style", "display:none");
        $("#corrective_reason_div").attr("style", "display:none");
        $("#warning_div").attr("style", "display:none");
        $("#corrective_reason").prop('required',false);

      }
      
    });

</script> 
<script type="text/javascript">
  function loadbreakdown_validation(){
    var category = $('#category').val();
      if( category == 'Breakdown')
      {
        $("#breakdown_reason_div").show();
        $("#corrective_reason_div").attr("style", "display:none");
         $("#warning_div").show();
      }
      else if(category == 'Corrective')
      {
        $("#corrective_reason_div").show();
        $("#breakdown_reason_div").attr("style", "display:none");
        $("#warning_div").attr("style", "display:none");
      }
      else{
        $("#breakdown_reason_div").attr("style", "display:none");
        $("#corrective_reason_div").attr("style", "display:none");
        $("#warning_div").attr("style", "display:none");
      }
  }
</script>
<script type="text/javascript">
var idleTime = 0;
$(document).ready(function () {
    //Increment the idle time counter every minute.
    var idleInterval = setInterval(timerIncrement, 30000); // 1 minute

    //Zero the idle timer on mouse movement.
    $(this).mousemove(function (e) {
        idleTime = 0;
    });
    $(this).keypress(function (e) {
        idleTime = 0;
    });
    $(this).mousedown(function (e) {
        idleTime = 0;
    });
    $(this).click(function (e) {
        idleTime = 0;
    });
    $(this).scroll(function (e) { 
        idleTime = 0;
    });
    $(this).scrollTop(function (e) {
        idleTime = 0;
    });

});
  

function timerIncrement() {
    idleTime = idleTime + 1;
    if (idleTime > 2) { // 20 minutes
        window.location.reload();
         $('#logout_confirmation').modal('show');
         timercountdown();
        idleTime = 0;
    }
}
var downloadTimer;
function timercountdown(){
  var timeleft = 10;
  downloadTimer = setInterval(function(){
  document.getElementById("timer").innerHTML = timeleft + " seconds remaining";

  timeleft -= 1;
  if(timeleft <= 0){
    var workstation_name = $('#workstation_forlogout').val();
  window.location.href = "/logout_operator/" + workstation_name;
    clearInterval(downloadTimer);
  }
}, 1000);
}
function stoptimer(){
  clearInterval(downloadTimer);
  document.getElementById("timer").innerHTML = "";
}
function logout_user(){
  var workstation_name = $('#workstation_forlogout').val();
  window.location.href = "/logout_operator/" + workstation_name;
}

</script>

@endsection