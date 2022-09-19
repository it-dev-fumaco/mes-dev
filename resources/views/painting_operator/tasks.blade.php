@extends('painting_operator.app', [
    'namePage' => 'Painting',
    'activePage' => 'painting_task',
    'process' => $process_name
])
@section('content')
<div class="panel-header">
  <div class="header text-center" style="margin-top: -65px;">
    <div class="row">
      <div class="col-md-10 text-white">
        <table style="text-align: center; width: 85%;">
          <tr>
            <td style="width: 25%; border-right: 5px solid white;" rowspan="2">
              <h2 class="title">
                <div class="pull-left" style="margin-left: 30px;">
                  <span style="display: block; font-size: 14pt;">{{ date('M-d-Y') }}</span>
                  <span style="display: block; font-size: 10pt;">{{ date('l') }}</span>
                </div>
              </h2>
            </td>
            <td style="width: 32%; border-right: 5px solid white;" rowspan="2">
              <h2 class="title" style="margin: auto; font-size: 22pt;"><span id="current-time">--:--:-- --</span></h2>
            </td>
            <td style="width: 43%;">
              <h5 class="card-title" style="font-size: 12pt; margin: 0;">
                <span class="dot" style="background-color: {{ $machine_details->status == 'Available' ? '#28B463' : '#717D7E' }};"></span>
                <span style="font-size: 12pt;"><b>{{ $process_name }}</b> [{{ $machine_details->machine_code }}]</span>
              </h5>
            </td>
          </tr>
          <tr>
            <td>
              <span style="font-size: 8pt;">Operator: @if(Auth::user()){{ Auth::user()->employee_name }}@endif</span>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="content" style="margin-top: -184px;">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <div class="row">
                <div class="col-6">
                  <h5 class="pt-3">Current Job Ticket(s)</h5>
                </div>
                <div class="col-6">
                  <div class="row pt-0">
                    @if ($process_name == 'Loading')
                      <div class="offset-6 col-6">
                        <button class="btn btn-danger btn-block start-new-task" style="display: flex; justify-content: center; align-items: center;">
                          <i class="now-ui-icons media-1_button-play" style="font-size: 11pt;"></i>&nbsp;<span style="font-size: 8pt;">Start New</span>
                        </button>
                      </div>
                    @endif
                </div>
                </div>
              </div>
              
              <div class="tab-content" style="min-height: 480px;">
                <div class="tab-pane active" id="current-tab" role="tabpanel">
                  <div id="row-tbl"></div>
                </div>
                @if ($process_name == 'Loading')
                  <div class="tab-pane" id="production-queue" role="tabpanel">
                    <div class="table-responsive">  
                      <div id="assigned-tasks-table"></div>
                    </div>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div> 

<!-- Scan JT Modal -->
<div class="modal fade" id="search-jt-Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color:#011F3D;">
                <h5 class="modal-title" id="exampleModalLabel">Scan / Enter Job Ticket</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-md-10 offset-md-1" id="jt-numpad">
                    <div class="form-group">
                      <div class="input-group jt-grp">
                        <div class="input-group-prepend">
                            <div class="input-group-text">PROM-</div>
                        </div>
                        <input type="text" class="form-control" id="job-ticket-id" style="font-size: 15pt;" required>
                      </div>
                      <small class="font-italic font-weight-bold empty-job-ticket-warning d-none" style="color: red">Please enter job ticket ID</small>
                    </div>
                    <div id="job-ticket-numpad">
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
                                <span class="numpad" onclick="document.getElementById('job-ticket-id').value=document.getElementById('job-ticket-id').value.slice(0, -1);"><</span>
                                <span class="numpad num">0</span>
                                <span class="numpad" onclick="document.getElementById('job-ticket-id').value='';">Clear</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-10 offset-md-1">
                                <button type="submit" class="btn btn-block btn-primary btn-lg" id="submit-job-ticket-btn">SUBMIT</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Scan JT Modal -->

<!-- Start Task Modal -->
<div class="modal fade" id="selected-jt-Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #011F3D;">
                <h5 class="modal-title text-placeholder font-weight-bold" id="production-order"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid" id="production-order-container">
                    <div class="row d-flex flex-row justify-content-start align-items-center">
                       
                        <div class="col-11 mx-auto text-left">
                            <span class="font-weight-bold d-block text-center" style="font-size: 11pt; display: block;"><span class="text-placeholder" id="reference"></span> - <span class="text-placeholder" id="classification"></span></span>
                            <span style="font-size: 10pt; color: #707B7C; display: block;">Customer</span>
                            <span style="font-size: 11pt; display: block;"><span class="text-placeholder font-weight-bold" id="customer-name"></span></span>
                            <span style="font-size: 10pt; color: #707B7C; display: block; margin-top: 8px;">Item Description</span>
                            <span class="text-justify" style="font-size: 10pt; display: block;">
                                <b><span class="text-placeholder" id="item-code"></span></b> - <span class="text-placeholder" id="item-description"></span>
                            </span>
                            <span class="notes d-none" style="font-size: 10pt; color: #707B7C; display: block;">Notes</span>
                            <span class="notes d-none" style="font-size: 11pt; display: block;"><span class="text-placeholder" id="notes"></span></span>
                            <div class="d-none" id="hidden-values">
                                <span class="text-placeholder" id="jt-id"></span>
                                <span class="text-placeholder" id="process-id"></span>
                                <span id="machine-code">{{ $machine_details->machine_code }}</span>
                            </div>
                        </div>
                        
                        <div class="col-8 mx-auto">
                          <div class="form-group" style="margin-top: 20px;">
                            <div class="input-group">
                                <input type="text" class="form-control input-placeholder qty-grp rounded" id="qty" style="font-size: 16pt; border: 1px solid #6C757D; text-align: center; font-weight: bold;" value="" placeholder='Enter Qty' required>
                            </div>
                            <span class="font-weight-bold d-none">Max: <span id="max"></span></span>
                            <small class="font-italic font-weight-bold empty-qty-warning d-none" style="color: red">Please enter qty</small>
                          </div>
                        </div>
                        <div class="col-12 mt-2 mx-auto">
                            <div id="qty-numpad">
                                <div class="text-center">
                                    <div class="row1">
                                        <span class="numpad qty num">1</span>
                                        <span class="numpad qty num">2</span>
                                        <span class="numpad qty num">3</span>
                                    </div>
                                    <div class="row1">
                                        <span class="numpad qty num">4</span>
                                        <span class="numpad qty num">5</span>
                                        <span class="numpad qty num">6</span>
                                    </div>
                                    <div class="row1">
                                        <span class="numpad qty num">7</span>
                                        <span class="numpad qty num">8</span>
                                        <span class="numpad qty num">9</span>
                                    </div>
                                    <div class="row1">
                                        <span class="numpad qty" onclick="document.getElementById('qty').value=document.getElementById('qty').value.slice(0, -1);"><</span>
                                        <span class="numpad qty num">0</span>
                                        <span class="numpad qty" onclick="document.getElementById('qty').value='';">Clear</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-10 offset-md-1">
                                        <button type="submit" class="btn btn-block btn-danger btn-lg text-uppercase" id="start-task-btn">
                                            <i class="now-ui-icons media-1_button-play"></i>&nbsp;Start Now
                                        </button>
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
<!-- Start Task Modal -->

<!-- Modal -->
<div class="modal fade" id="machine-power-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/insert_machine_logs" method="POST" id="machine-power-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title">- Machine</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                        <div class="row" style="margin-top: -1%;">
                           <div class="col-sm-12 text-center">
                              <input type="hidden" name="operator_id" value="{{ Auth::user()->user_id }}">
                              <input type="hidden" name="category" id="category-machine">
                              <span style="font-size: 12pt;">Confirm machine -.</span>
                           </div>               
                        </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal" style="padding-top: -100px;">Cancel</button>
               &nbsp;
               <button type="submit" class="btn btn-primary">Confirm</button>
            </div>
         </div>
      </form>
   </div>
</div>
<div class="modal fade" id="scan-jt-for-qc-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-md" role="document">
     <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #f57f17;">
           <h5 class="modal-title">Quality Inspection [<b>Painting</b>]</h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
           </button>
           <input type="hidden" id="workstation" value="Painting">
        </div>
        <div class="modal-body" style="min-height: 480px;">
           <div class="row">
              <div class="col-md-12">
                 <div class="row" id="enter-production-order">
                    <div class="col-md-10 offset-md-1">
                       <h6 class="text-center">Scan your Job Ticket</h6>
                       <div class="row">
                          <div class="col-md-10">
                             <div class="form-group">
                                <div class="input-group">
                                   <div class="input-group-prepend">
                                      <div class="input-group-text">PROM-</div>
                                   </div>
                                   <input type="text" class="form-control" id="production-order-qc" style="font-size: 15pt;" required>
                                </div>
                             </div>
                          </div>
                          <div class="col-md-2" style="padding: 0; margin-top: -15px;">
                             <center>
                                <img src="{{ asset('img/tap.gif') }}" width="260" height="60" id="toggle-jt-numpad-qc">
                             </center>
                          </div>
                       </div>
                      
                       <div id="jt-numpad-qc" style="display: none;">
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
                             <span class="numpad" onclick="document.getElementById('production-order-qc').value=document.getElementById('production-order-qc').value.slice(0, -1);"><</span>
                             <span class="numpad num">0</span>
                             <span class="numpad" onclick="document.getElementById('production-order-qc').value='';">Clear</span>
                          </div>
                       </div>
                       <div class="row">
                          <div class="col-md-10 offset-md-1">
                             <button type="button" class="btn btn-block btn-primary btn-lg" id="submit-enter-production-order-qc">SUBMIT</button>
                          </div>
                       </div>
                       </div>
                       <div id="jt-scan-img-qc">
                          <center>
                             <img src="{{ asset('img/scan-barcode.png') }}" width="220" height="240" style="margin: 40px 10px 10px 10px;">
                          </center>
                       </div>
                    </div>
                 </div>
              </div>
           </div>
        </div>
     </div>
  </div>
</div>
<div class="modal fade" id="chemical-records-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
     <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
           <h5 class="modal-title">&nbsp;
             Painting Chemical Records
           </h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
           </button>
        </div>
        <div class="modal-body">
           <div id="chemical-records-div"></div>
        </div>
     </div>
  </div>
</div>
<div class="modal fade" id="water-discharged-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
     <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
           <h5 class="modal-title">&nbsp;
             Water Discharge Monitoring
           </h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
           </button>
        </div>
        <div class="modal-body">
           <div id="water_discharged_div"></div>
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
 <div class="modal fade" id="select-process-for-inspection-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document" style="min-width: 90%;">
     <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #f57f17;">
           <h5 class="modal-title"><b>Painting</b> - <span class="production-order"></span></h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
           </button>
           <input type="hidden" id="workstation" value="Painting">
        </div>
        <div class="modal-body" style="min-height: 480px;">
           <div class="row" id="tasks-for-inspection-tbl" style="margin-top: 10px;"></div>
        </div>
     </div>
  </div>
</div>
<div class="modal fade" id="powder-record-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" style="min-width: 98%;" role="document">
      <div class="modal-content">
         <div class="modal-header text-white" style="background-color: #0277BD;">
            <h5 class="modal-title">&nbsp;
              POWDER COAT - INVENTORY UPDATE
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div id="powder_record_div"></div>
         </div>
      </div>
   </div>
</div>

@include('painting_operator.modal_view_schedule')
@include('painting_operator.modal_view_production_order_details')
@include('painting_operator.modal_end_task')
@include('painting_operator.modal_enter_reject')
@include('modals.machine_breakdown_form')
@include('painting_operator.modal_restart_task')
@include('quality_inspection.modal_inspection')

<style type="text/css">
  .jt-grp, .qty-grp{
    border-radius: 25px;
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
    text-overflow: ellipsis;
  }

  .blinking{
    animation:blinkingText 1.2s infinite;
  }

  @keyframes blinkingText{
    0%{     color: #f39c12;    }
    49%{    color: #f39c12; }
    60%{    color: transparent; }
    99%{    color:transparent;  }
    100%{   color: #f39c12;    }
  }

  .blinking-completed{
    animation:blinkingText-completed 1.2s infinite;
  }

  @keyframes blinkingText-completed{
    0%{     color: #28b463;    }
    49%{    color: #28b463; }
    60%{    color: transparent; }
    99%{    color:transparent;  }
    100%{   color: #28b463;    }
  }

  .sub-btn{
    height: 70px;
    border-radius: 0;
  }

  .card-css{
    background-color: #99a3a4; margin-bottom: 5px;
  }

  .card-body-css{
    padding: 5px;
  }

  .remove-btn{
    font-size: 15pt; margin: 2px;
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

  .numpad > .qty{
    display: inline-block;
    border: 1px solid #333;
    border-radius: 5px;
    text-align: center;
    width: 20%;
    height: 20%;
    line-height: 60px;
    margin: 3px;
    font-size: 10pt !important;
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

@endsection
@section('script')
<script src="{{ asset('/js/jquery.rfid.js') }}"></script>
<script>
  $(document).ready(function(){
    $(document).on('click', '.custom-a', function(e){
      e.preventDefault();
      location.assign($(this).attr('href'));
    });

    function close_modal(modal){
      $(modal).modal('hide');
    }

    $(document).on('click', '.maintnenance-access-id-modal-trigger', function(e){
      e.preventDefault();
      var machine_id = $(this).data('machine-id');
      var machine_breakdown_id = $(this).data('machine-breakdown-id');
      $('#machine-id').val(machine_id);
      $('#machine-breakdown-id').val(machine_breakdown_id);
      if($(this).data('maintenance-status') == 'In Process'){
        $('#is-completed').prop('checked', true);
      }else{
        $('#is-completed').prop('checked', false);
      }
      $('#maintenance-access-id-modal').modal('show');
      $('#access-id').val('');
    });
    
    $(document).on('click', '#access-id-numpad .num', function(e){
        e.preventDefault();
        var num = $(this).text();
        var current = $('#access-id').val();
        var new_input = current + num;
            
        $('#access-id').val(new_input);
    });

    $(document).on('click', '#submit-access-id', function (e){
      $.ajax({
        url: '/update_maintenance_task',
        type:"POST",
        data: {
          _token: '{{ csrf_token() }}',
          user_id: $('#access-id').val(),
          machine_id: $('#machine-id').val(),
          machine_breakdown_id: $('#machine-breakdown-id').val(),
          is_completed: $('#is-completed').is(":checked") ? 1 : 0
        },
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#maintenance-access-id-modal').modal('hide');
            get_pending_for_maintenance();
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });

    $(document).on('click', '#pending-for-maintenance-trigger', function (){
      $('#pending-for-maintenance-modal').modal('show');
      get_pending_for_maintenance();
    });
    
    function get_pending_for_maintenance(){
      $.ajax({
        url:"/operator/pending_for_maintenance/{{ $operation_id }}",
        type:"GET",
        success:function(data){
          $('#pending-for-maintenance-tbl').html(data);
          $('#operation').text($('#operation-placeholder').text());
        }
      });
    }

    $(document).on('click', '.get-task', function(e){
      e.preventDefault();
      get_task();
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

    function clear_placeholders(){
        $('.text-placeholder').text('');
        $('.input-placeholder').val('');
    }

    $('.start-new-task').click(function(e){
        e.preventDefault();
        $('#search-jt-Modal').modal('show');
    });

    $(document).on('click', '#submit-job-ticket-btn', function (){
        if($('#job-ticket-id').val() == ''){
          $('.jt-grp').css('border', '1px solid red');
          $('.empty-job-ticket-warning').removeClass('d-none');
          return false;
        }else{
          $('.jt-grp').css('border', '1px solid #fff');
          $('.empty-job-ticket-warning').addClass('d-none');
        }
        var production_order = 'PROM-' + $('#job-ticket-id').val();
        $.ajax({
            url:"/get_production_order_details/" + production_order + "/{{ $process_details->process_id }}",
            type:"GET",
            success:function(data){
                if(data.success == 0){
                    // $('#row-tbl').html(data);
                    showNotification("danger", data.message, "now-ui-icons travel_info");
                }else{
                    var reference = data.details.sales_order ? data.details.sales_order : data.details.material_request;
                    $('#reference').text(reference);
                    $('#production-order').text(data.details.production_order);
                    $('#classification').text(data.details.classification);
                    $('#customer-name').text(data.details.customer);
                    $('#item-code').text(data.details.item_code);
                    $('#item-description').text(data.details.description);
                    $('#max').text(data.details.qty_to_manufacture);
                    $('#jt-id').text(data.details.job_ticket_id);
                    $('#process-id').text(data.details.process_id);
                    $('#qty').val(data.qty);

                    if(data.details.notes){
                        $('.notes').removeClass('d-none');
                        $('#notes').text(data.details.notes);
                    }else{
                        $('.notes').addClass('d-none');
                    }

                    $('#selected-jt-Modal').modal('show');
                }
            }, 
            error: function(jqXHR, textStatus, errorThrown) {
                // $('#row-tbl').html(data);
                showNotification("danger", data.message, "now-ui-icons travel_info");
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    });

    $(document).on('click', '#start-task-btn', function(e){
        e.preventDefault();

        var btn = $(this);

        btn.attr('disabled', true);

        var data = {  
            operator_id: '{{ Auth::user()->user_id }}', 
            production_order: $('#production-order').text(), 
            process_id: $('#process-id').text(), 
            machine_code: '{{ $machine_details->machine_code }}',
            _token: '{{ csrf_token() }}', 
            job_ticket_id: $('#jt-id').text(),
            qty: $('#qty').val()
        }

        if($('#qty').val() == ''){
          $('.qty-grp').css('border', '1px solid red');
          $('.empty-qty-warning').removeClass('d-none');
          
          btn.attr('disabled', false);
          console.log('test');
        }else{
          $('.qty-grp').css('border', '1px solid #6C757D');
          $('.empty-qty-warning').addClass('d-none');

          $.ajax({
            url: "/start_painting",
            type:"POST",
            data: data,
            success:function(response){
              btn.attr('disabled', false);
              if (response.success > 0) {
                clear_placeholders();
                $('#search-jt-Modal').modal('hide');
                $('#selected-jt-Modal').modal('hide');

                get_task();
                showNotification("success", response.message, "now-ui-icons ui-1_check");
              }else{
                showNotification("danger", response.message, "now-ui-icons travel_info");
              }
            }, 
            error: function(jqXHR, textStatus, errorThrown) {
              btn.attr('disabled', false);
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
        }
    });

    $(document).on('click', '#job-ticket-numpad .num', function(e){
        e.preventDefault();
        var num = $(this).text();
        var current = $('#job-ticket-id').val();
        var new_input = current + num;
        new_input = format(new_input.replace(/-/g, ""), [5], "-");
            
        $('#job-ticket-id').val(new_input);
    });

    $(document).on('click', '#qty-numpad .num', function(e){
        e.preventDefault();
        var num = $(this).text();
        var current = $('#qty').val();
        var new_input = current + num;
            
        $('#qty').val(new_input);
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

    get_task();
    setInterval(get_task, 5000);
    function get_task(){
      $.ajax({
        url:"/operator/Painting/{{ $process_name }}/{{ $machine_code }}",
        type:"GET",
        success:function(data){
          $('#row-tbl').html(data);
        }, 
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    }

    $(document).on('click', '.end-task-btn', function(e){
      e.preventDefault();
      var max_qty = $(this).data('balance-qty');
      var production_order = $(this).data('production-order');
      $('#end-task-modal .timelog-id').val($(this).data('timelog-id'));
      $('#end-task-modal .process-name').text($(this).data('process-name'));
      $('#end-task-modal .max-qty').text(max_qty);
      $('#end-task-modal .production-order').text(production_order);
      $('#end-task-modal .production-order-input').val(production_order);
      $('#end-task-modal .workstation-input').val('Painting');
      $('#end-task-modal .balance-qty').val(max_qty);
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

    // Quality Inspection
    var active_input = null;
    var end_scrap_task_active_input = null;
      
    $(document).on('focus', '#quality-inspection-frm input[type=text]', function() {
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
  
        var next_tab_id = $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').attr('id');
        if(next_tab_id != 'tablast'){
          if(tab_qty_reject > tab_reject_level){
            $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').removeClass('custom-tabs-1').addClass('active');
          }else{
            $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').addClass('custom-tabs-1').removeClass('active');
          }
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
  
          $('#' + $(this).attr('id') + '-inspection input:checkbox:not(:checked)').each(function(){
            if($.isNumeric($(this).val())){
              reject_id += $(this).val() + ',';
              reject_values += $('#' + $(this).attr('id') + '-input').val() + ',';
            }
          });

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

      $(document).on('change', '.select-all-checklist-per-tab', function(e){
        e.preventDefault();
        var selector = '.' + $(this).attr('id');
        $(selector).not(this).prop('checked', this.checked);
      });
  
      $(document).on('click', '#quality-inspection-modal .toggle-manual-input', function(e){
        $('#quality-inspection-modal img').slideToggle();
        $('#quality-inspection-modal .manual').slideToggle();
      });
  
      $(document).on('click', '#quality-inspection-frm .prev-tab', function() {
        active_input = null;
  
        var next_tab_id = $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').attr('id');
        if(next_tab_id != 'tablast'){
          $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').removeClass('custom-tabs-1').addClass('active');
        }else{
          $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').addClass('custom-tabs-1').removeClass('active');
        }
  
        $('#quality-inspection-modal .nav-tabs .nav-item > .active').parent().prev().find('.custom-tabs-1').tab('show');
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
        new_input = format(new_input.replace(/-/g, ""), [5], "-");
           
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
  
      function get_tasks_for_inspection(workstation, production_order){
        $.ajax({
          url:"/get_tasks_for_inspection/" + workstation +"/" + production_order,
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
  
      $(document).on('click', '.quality-inspection-btn', function(e){
        e.preventDefault();
  
        $('#quality-inspection-frm button[type="submit"]').removeAttr('disabled');
  
        var production_order = $(this).data('production-order');
        var process_id = $(this).data('processid');
        var workstation = 'Painting';
        var inspection_type = $(this).data('inspection-type');
  
        var data = {
          time_log_id: $(this).data('timelog-id'),
          inspection_type: inspection_type
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
              $('#quality-inspection-modal').modal('hide');
              get_tasks_for_inspection(data.details.workstation, data.details.production_order)
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
    // Quality Inspection

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
        url: $(this).attr('action'),
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            get_task();
            $('#end-task-modal').modal('hide');
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });

    $(document).on('click', '#machine-power-btn', function(e){
      e.preventDefault();
      var status = $(this).data('status');
      $('#machine-power-modal .modal-title').text(status + ' Machine');
      $('#machine-power-modal .modal-body span').text('Confirm machine ' + status);
      $('#machine-power-modal #category-machine').val(status);
      $('#machine-power-modal').modal('show');
    });

    $('#machine-power-frm').submit(function(e){
      e.preventDefault();

      var category = $('#category-machine').val();

      $.ajax({
        url:"/insert_machine_logs",
        type:"post",
        data: $(this).serialize(),
        success:function(data){
          if (category == 'Start Up') {
            location.reload();
          }else{
            window.location.href="/painting/logout/{{ $process_name }}";
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
      $('#restart-task-modal .workstation').html('[<b>Painting</b>]');
      $('#restart-task-modal .operator-name').val($($(this).data('tabid') + ' .operator-name').text());
      $('#restart-task-modal').modal('show');
    });

    $('#logout_click').click(function(e){
      e.preventDefault();
      window.location.href = "/painting/logout/{{ $process_name }}";
    });

    $('#restart-task-frm').submit(function(e){
      e.preventDefault();
      $.ajax({
        url: "/restart_painting",
        type:"POST",
        data: $(this).serialize(),
        success:function(response){
          if (response.success < 1) {
            showNotification("danger", response.message, "now-ui-icons travel_info");
          }else{
            get_task();
            showNotification("success", response.message, "now-ui-icons ui-1_check");
            $('#restart-task-modal').modal('hide');
          }
        }
      });
    });

    $(document).on('click', '#machine-breakdown-modal-btn', function(e){
      e.preventDefault();
      $('#report-machine-breakdown-modal').modal('show');
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

    @if ($process_name == 'Loading')
      function get_scheduled_for_painting(){
        $.ajax({
          url:"/get_scheduled_for_painting",
          type:"GET",
          success:function(data){
            $('#assigned-tasks-table').html(data);
          }
        });  
      }
    @endif

    $('#view-painting-schedule-btn').click(function(e){
      e.preventDefault();
      var scheduled;
      var backlog;
      $.when(
        $.ajax({
          url:"/get_scheduled_for_painting",
          type:"GET",
          success:function(data){
            scheduled = data;
          }
        }),
        $.ajax({
          url:"/get_painting_backlogs",
          type:"GET",
          success:function(data){
            backlog = data;
          }
        })
      ).then(function(){
        $('#view-scheduled-task-tbl').html(scheduled);
        $('#backlogs-tbl').html(backlog);
        $('#view-scheduled-task-modal').modal('show');
      });
    });

    $(document).on('click', '.view-prod-details-btn', function(e){
      e.preventDefault();
      $('#jt-workstations-modal .modal-title').text($(this).text() + " [Painting]");
      getJtDetails($(this).text());
    });

    $(document).on('click', '#enter-reject-btn', function(e){
      e.preventDefault();
    //   console.log($(this).data('timelog-id'));
      var max_qty = $(this).data('good');
      var production_order = $(this).data('production-order');// '{{-- $production_order --}}';
      
      $('#reject-sel-batch').empty();
      $('#reject-sel-batch').append($('#sel-batch').html());
      var has_batch = $('#count-batch').val();
      $('#enter-reject-modal .production-order').text(production_order);
      $('#enter-reject-modal .production-order-input').val(production_order);
      $('#enter-reject-modal .workstation-input').val('Painting');
      
      if(has_batch > 1){
        $('#enter-reject-modal .max-qty').text(0);
        $('#enter-reject-modal .timelog-id').val('');
        $('#enter-reject-modal .process-name').text('--');
        $('#enter-reject-modal #sel-batch-div').removeAttr('hidden');
      }else{
        var max_qty = $($(this).data('tabid') + ' .good-qty').text();
        $('#enter-reject-modal .max-qty').text(max_qty);
        $('#enter-reject-modal #sel-batch-div').attr('hidden', true);
        $('#enter-reject-modal .timelog-id').val($(this).data('timelog-id'));
        $('#enter-reject-modal .max-qty').text($(this).data('good'));
        $('#enter-reject-modal .process-name').text($(this).data('process-name'));
      }

      
      $.ajax({
        url: "/get_reject_types/" + "Painting/"+{{ $process_details->process_id }},
        type:"GET",
        success:function(data){
          $('.op_reject_checklist').html(data);
        }, 
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
  
      $('#enter-reject-modal .process-id-input').val('{{ $process_details->process_id }}');
      $('#enter-reject-modal .process-name-input').val('{{ $process_name }}');
      $('#enter-reject-modal .status-input').val($(this).data('status'));
      $('#enter-reject-modal').modal('show');
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

    $('#reject-task-frm').submit(function(e){
      e.preventDefault();
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
          console.log(response);
          if (response.success < 1) {
            showNotification("danger", response.message, "now-ui-icons travel_info");
          }else{
            get_task();
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

    $('.modal').on('hidden.bs.modal', function(){
      var frm = $(this).find('form')[0];
      if (frm) frm.reset();
    });

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });


    // ------------------------------------------------------------------------------------------------
 


    

    

    var rfidParser = function (rawData) {
      // if (rawData.length < 12) return null;
      return rawData;
   };

   // Called on a good scan (company card recognized)
   var goodScan = function (cardData) {
    if($('#manual-enter-jt-modal').is(':visible') == false){
      get_production_order_details(cardData, '{{ $process_details->process_id }}');
          
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
  $(document).on('change', '#reject-sel-batch', function(e){
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

</script>

  <script type="text/javascript">
  $(document).on('click', '#view-chemical-records-btn', function(event){
      $.ajax({
        url:"/get_chemical_records_modal_details",
        type:"GET",
        success:function(response){
          $('#chemical-records-div').html(response);
          
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        },
      }); 
       $('#chemical-records-modal').modal('show');
       
    });

</script>
<script type="text/javascript">
  $('#view-water-Monitoring-btn').click(function(e){
      e.preventDefault();
      $.ajax({
        url:"/get_water_discharged_modal_details",
        type:"GET",
        success:function(response){
          // $('#operating_hrs').val(data.operating_hrs);
          // $('#w_previous').val(data.previous_date);
          $('#water_discharged_div').html(response);
          
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        },
      }); 
       $('#water-discharged-modal').modal('show');
       // $('#sidebar-wrapper').modal('hide');
       // $(this).find('[autofocus]').focus();
    });


</script>
<script type="text/javascript">
  $(document).on('click', '#view-chemical-records-btn', function(event){
      $.ajax({
        url:"/get_chemical_records_modal_details",
        type:"GET",
        success:function(response){
          $('#chemical-records-div').html(response);
          
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        },
      }); 
       $('#chemical-records-modal').modal('show');
       
    });

  $(document).on('focus', '#water_discharge_monitoring_frm input[type=text]', function() {
      if($(this).data('edit') > 0){
        active_input = $(this).attr('id');
      }else{
        active_input = null;
      }
    });

    $(document).on('click', '#water_discharge_monitoring_frm .num', function() {
      $("#" + active_input).focus();
      var input = $('#water_discharge_monitoring_frm #' + active_input);
      var x = input.val();
      var y = $(this).text();
  
      if (x == 0) {
        x = '';
      }
      
      input.val(x + y);
    });

    $(document).on('click', '#water_discharge_monitoring_frm .clear', function() {
      $("#" + active_input).focus();
      var input = $('#water_discharge_monitoring_frm #' + active_input);
      input.val(0);
    });

    $(document).on('click', '#water_discharge_monitoring_frm .del', function() {
      $("#" + active_input).focus();
      var input = $('#water_discharge_monitoring_frm #' + active_input);
      var x = input.val();
 
      input.val(x.substring(0, x.length - 1));
  
      if (input.val().length == 0) {
        input.val(0);
      }
    });


</script>
<script type="text/javascript">
  $(document).on('click', '#water-discharged-modal .toggle-manual-input', function(e){
      $('#water-discharged-modal img').slideToggle();
      $('#water-discharged-modal .manual').slideToggle();
    });
</script>
<script type="text/javascript">
     $(document).on('click', '#water_discharge_monitoring_frm .numm', function() {

        var valpre = $('#present_input').val();
        var valprev = $('#previous_input').val();
        if (valpre == 0 || valpre =="") {
          $("#incoming_water_discharged").val(0);
        }
        var diff = valpre - valprev;
         $("#incoming_water_discharged").val(diff);
    });

</script>

<script type="text/javascript">
  $(document).on('submit', '#water_discharge_monitoring_frm', function(e){
      e.preventDefault();
     
      $.ajax({
        url: $(this).attr('action'),
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success) {
            showNotification("success", data.message, "now-ui-icons ui-1_check");
           $('#water-discharged-modal').modal('hide');
           $('#water_discharge_monitoring_frm').trigger("reset");
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
</script>
<script type="text/javascript">
      function showNotification(color, message, icon){
      $.notify({
        icon: icon,
        message: message
      },{
        type: color,
        timer: 3000,
        placement: {
          from: 'top',
          align: 'center'
        }
      });
    }
</script>
<script type="text/javascript">
  $(document).on('focus', '#painting_chemical_records_frm input[type=text]', function() {
      if($(this).data('edit') > 0){
        active_input = $(this).attr('id');
      }else{
        active_input = null;
      }
    });

    $(document).on('click', '#painting_chemical_records_frm .num', function() {
      $("#" + active_input).focus();
      var input = $('#painting_chemical_records_frm #' + active_input);
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
      var c_point = input.val();

      var chemtype = $("#" + active_input).attr('data-chemtype');
      if (chemtype == "freealkali") {
        if (c_point > 7.5) {
            if ($('#degreasing_type').val() > 0) {
              $('#stat_degreasing').text('Good');
              $("#degreasing_add").text("Add");
              $("#degreasing_type_label").text(" Water (L)");
            }else{
              $("#degreasing_add").text("Add");
              $("#degreasing_type_label").text(" Water (L)");
              $("#degreasing_type_input").val("Water (L)");
              $('#stat_degreasing').text('');
              document.getElementById('degreasing_type').disabled = false;
            }
        }else if(c_point < 6.5){
          if ($('#degreasing_type').val() > 0) {
              $('#stat_degreasing').text('Good');
              $("#degreasing_add").text("Add");
              $("#degreasing_type_label").text(" FC-43490 (KG)");
          }else{
            $("#degreasing_add").text("Add");
            $("#degreasing_type_label").text(" FC-43490 (KG)");
            $("#degreasing_type_input").val("FC-43490 (KG)");
            $('#stat_degreasing').text('');
            document.getElementById('degreasing_type').disabled = false; 
          }
        }else{
            $("#degreasing_add").text("Increase/Decrease");
            $("#degreasing_type_label").text(" Point");
            $('#stat_degreasing').text('Good');
            $("#degreasing_type").val("0");
            $("#degreasing_type_input").val("");
            document.getElementById('degreasing_type').disabled = true;
        }
      }else if(chemtype == "PB3100R"){
        if (c_point > 20) {
          if ($('#replenshing_type').val() > 0) {
            $('#stat_replenshing').text('Good');
            $("#replenshing_add").text("Add");
            $("#replenshing_type_label").text(" Water (L)");
          }else{
            $("#replenshing_add").text("Add");
            $("#replenshing_type_label").text(" Water (L)");
            $("#replenshing_type_input").val("Water (L)");
            $('#stat_replenshing').text('');
            document.getElementById('replenshing_type').disabled = false; 
          }

        }else if( c_point < 16 ){
          if ($('#replenshing_type').val() > 0) {
            $('#stat_replenshing').text('Good');
            $("#replenshing_add").text("Add");
            $("#replenshing_type_label").text(" PB-3100R (KG)");
          }else{
            $("#replenshing_add").text("Add");
            $("#replenshing_type_label").text(" PB-3100R (KG)");
            $("#replenshing_type_input").val("PB-3100R (KG)");
            $('#stat_replenshing').text('');
             document.getElementById('replenshing_type').disabled = false; 
          }
        }else{
          $("#replenshing_add").text("Increase/Decrease");
          $("#replenshing_type_label").text(" Point");
          $('#stat_replenshing').text('Good');
          document.getElementById('replenshing_type').disabled = true;
          $("#replenshing_type_input").val("");
          $("#replenshing_type").val("0");
        }

      }else if(chemtype == "AC-131"){
        if (c_point < 6) {
          if ($('#accelerator_type').val() > 0) {
            $('#stat_accelerator').text('Good');
            $("#accelerator_add").text("Add");
            $("#accelerator_type_label").text(" AC-131 (KG)");
          }else{
            $("#accelerator_add").text("Add");
            $("#accelerator_type_label").text(" AC-131 (KG)");
            $("#accelerator_type_input").val("AC-131 (KG)");
            $('#stat_accelerator').text('');
            document.getElementById('accelerator_type').disabled = false; 
          }

        }else if( c_point > 9.0 ){
          if ($('#accelerator_type').val() > 0) {
            $('#stat_accelerator').text('Good');
            $("#accelerator_add").text("Add");
            $("#accelerator_type_label").text(" Water (L)");
          }else{
            $("#accelerator_add").text("Add");
            $("#accelerator_type_label").text(" Water (L)");
            $("#accelerator_type_input").val("Water (L)");
            $('#stat_accelerator').text('');
             document.getElementById('accelerator_type').disabled = false; 
          }
          
        }else{
          $("#accelerator_add").text("Increase/Decrease");
          $("#accelerator_type_label").text(" Point");
          $('#stat_accelerator').text('Good');
          document.getElementById('accelerator_type').disabled = true;
          $("#accelerator_type_input").val("");
          $("#accelerator_type").val("0");
        }
      }else{
        if (chemtype == "degreasing_type_val") {
          if (c_point > 0) {
            $('#stat_degreasing').text('Good');
          }else{
            $('#stat_degreasing').text('');
          }
        }else if (chemtype == "replenshing_type_val") {
          if (c_point > 0) {
            $('#stat_replenshing').text('Good');
          }else{
            $('#stat_replenshing').text('');
          }
        }else if (chemtype == "accelerator_type_val") {
          if (c_point > 0) {
            $('#stat_accelerator').text('Good');
          }else{
            $('#stat_accelerator').text('');
          }
        }
      }
      console.log(chemtype);
      console.log('#' + active_input);
      console.log(c_point);
      
    });

    $(document).on('click', '#painting_chemical_records_frm .decimal', function() {
      $("#" + active_input).focus();
      var input = $('#painting_chemical_records_frm #' + active_input);
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
      var c_point = input.val();

      var chemtype = $("#" + active_input).attr('data-chemtype');
      if (chemtype == "freealkali") {
        if (c_point > 7.5) {
            if ($('#degreasing_type').val() > 0) {
              $('#stat_degreasing').text('Good');
              $("#degreasing_add").text("Add");
              $("#degreasing_type_label").text(" Water (L)");
            }else{
              $("#degreasing_add").text("Add");
              $("#degreasing_type_label").text(" Water (L)");
              $("#degreasing_type_input").val("Water (L)");
              $('#stat_degreasing').text('');
              document.getElementById('degreasing_type').disabled = false;
            }
        }else if(c_point < 6.5){
          if ($('#degreasing_type').val() > 0) {
              $('#stat_degreasing').text('Good');
              $("#degreasing_add").text("Add");
              $("#degreasing_type_label").text(" FC-43490 (KG)");
          }else{
            $("#degreasing_add").text("Add");
            $("#degreasing_type_label").text(" FC-43490 (KG)");
            $("#degreasing_type_input").val("FC-43490 (KG)");
            $('#stat_degreasing').text('');
            document.getElementById('degreasing_type').disabled = false; 
          }
        }else{
            $("#degreasing_add").text("Increase/Decrease");
            $("#degreasing_type_label").text(" Point");
            $('#stat_degreasing').text('Good');
            $("#degreasing_type").val("0");
            $("#degreasing_type_input").val("");
            document.getElementById('degreasing_type').disabled = true;
        }
      }else if(chemtype == "PB3100R"){
        if (c_point > 20) {
          if ($('#replenshing_type').val() > 0) {
            $('#stat_replenshing').text('Good');
            $("#replenshing_add").text("Add");
            $("#replenshing_type_label").text(" Water (L)");
          }else{
            $("#replenshing_add").text("Add");
            $("#replenshing_type_label").text(" Water (L)");
            $("#replenshing_type_input").val("Water (L)");
            $('#stat_replenshing').text('');
            document.getElementById('replenshing_type').disabled = false; 
          }

        }else if( c_point < 16 ){
          if ($('#replenshing_type').val() > 0) {
            $('#stat_replenshing').text('Good');
            $("#replenshing_add").text("Add");
            $("#replenshing_type_label").text(" PB-3100R (KG)");
          }else{
            $("#replenshing_add").text("Add");
            $("#replenshing_type_label").text(" PB-3100R (KG)");
            $("#replenshing_type_input").val("PB-3100R (KG)");
            $('#stat_replenshing').text('');
             document.getElementById('replenshing_type').disabled = false; 
          }
        }else{
          $("#replenshing_add").text("Increase/Decrease");
          $("#replenshing_type_label").text(" Point");
          $('#stat_replenshing').text('Good');
          document.getElementById('replenshing_type').disabled = true;
          $("#replenshing_type_input").val("");
          $("#replenshing_type").val("0");
        }

      }else if(chemtype == "AC-131"){
        if (c_point < 6) {
          if ($('#accelerator_type').val() > 0) {
            $('#stat_accelerator').text('Good');
            $("#accelerator_add").text("Add");
            $("#accelerator_type_label").text(" AC-131 (KG)");
          }else{
            $("#accelerator_add").text("Add");
            $("#accelerator_type_label").text(" AC-131 (KG)");
            $("#accelerator_type_input").val("AC-131 (KG)");
            $('#stat_accelerator').text('');
            document.getElementById('accelerator_type').disabled = false; 
          }

        }else if( c_point > 9.0 ){
          if ($('#accelerator_type').val() > 0) {
            $('#stat_accelerator').text('Good');
            $("#accelerator_add").text("Add");
            $("#accelerator_type_label").text(" Water (L)");
          }else{
            $("#accelerator_add").text("Add");
            $("#accelerator_type_label").text(" Water (L)");
            $("#accelerator_type_input").val("Water (L)");
            $('#stat_accelerator').text('');
             document.getElementById('accelerator_type').disabled = false; 
          }
          
        }else{
          $("#accelerator_add").text("Increase/Decrease");
          $("#accelerator_type_label").text(" Point");
          $('#stat_accelerator').text('Good');
          document.getElementById('accelerator_type').disabled = true;
          $("#accelerator_type_input").val("");
          $("#accelerator_type").val("0");
        }
      }else{
        if (chemtype == "degreasing_type_val") {
          if (c_point > 0) {
            $('#stat_degreasing').text('Good');
          }else{
            $('#stat_degreasing').text('');
          }
        }else if (chemtype == "replenshing_type_val") {
          if (c_point > 0) {
            $('#stat_replenshing').text('Good');
          }else{
            $('#stat_replenshing').text('');
          }
        }else if (chemtype == "accelerator_type_val") {
          if (c_point > 0) {
            $('#stat_accelerator').text('Good');
          }else{
            $('#stat_accelerator').text('');
          }
        }
      }

    });

    $(document).on('click', '#painting_chemical_records_frm .del', function() {
      $("#" + active_input).focus();
      var input = $('#painting_chemical_records_frm #' + active_input);
      var x = input.val();
 
      input.val(x.substring(0, x.length - 1));
  
      if (input.val().length == 0) {
        input.val(0);
      }

      var c_point = input.val();

      var chemtype = $("#" + active_input).attr('data-chemtype');
      if (chemtype == "freealkali") {
        if (c_point > 7.5) {
            if ($('#degreasing_type').val() > 0) {
              $('#stat_degreasing').text('Good');
              $("#degreasing_add").text("Add");
              $("#degreasing_type_label").text(" Water (L)");
            }else{
              $("#degreasing_add").text("Add");
              $("#degreasing_type_label").text(" Water (L)");
              $("#degreasing_type_input").val("Water (L)");
              $('#stat_degreasing').text('');
              document.getElementById('degreasing_type').disabled = false;
            }
        }else if(c_point < 6.5){
          if ($('#degreasing_type').val() > 0) {
              $('#stat_degreasing').text('Good');
              $("#degreasing_add").text("Add");
              $("#degreasing_type_label").text(" FC-43490 (KG)");
          }else{
            $("#degreasing_add").text("Add");
            $("#degreasing_type_label").text(" FC-43490 (KG)");
            $("#degreasing_type_input").val("FC-43490 (KG)");
            $('#stat_degreasing').text('');
            document.getElementById('degreasing_type').disabled = false; 
          }
        }else{
            $("#degreasing_add").text("Increase/Decrease");
            $("#degreasing_type_label").text(" Point");
            $('#stat_degreasing').text('Good');
            $("#degreasing_type").val("0");
            $("#degreasing_type_input").val("");
            document.getElementById('degreasing_type').disabled = true;
        }
      }else if(chemtype == "PB3100R"){
        if (c_point > 20) {
          if ($('#replenshing_type').val() > 0) {
            $('#stat_replenshing').text('Good');
            $("#replenshing_add").text("Add");
            $("#replenshing_type_label").text(" Water (L)");
          }else{
            $("#replenshing_add").text("Add");
            $("#replenshing_type_label").text(" Water (L)");
            $("#replenshing_type_input").val("Water (L)");
            $('#stat_replenshing').text('');
            document.getElementById('replenshing_type').disabled = false; 
          }

        }else if( c_point < 16 ){
          if ($('#replenshing_type').val() > 0) {
            $('#stat_replenshing').text('Good');
            $("#replenshing_add").text("Add");
            $("#replenshing_type_label").text(" PB-3100R (KG)");
          }else{
            $("#replenshing_add").text("Add");
            $("#replenshing_type_label").text(" PB-3100R (KG)");
            $("#replenshing_type_input").val("PB-3100R (KG)");
            $('#stat_replenshing').text('');
             document.getElementById('replenshing_type').disabled = false; 
          }
        }else{
          $("#replenshing_add").text("Increase/Decrease");
          $("#replenshing_type_label").text(" Point");
          $('#stat_replenshing').text('Good');
          document.getElementById('replenshing_type').disabled = true;
          $("#replenshing_type_input").val("");
          $("#replenshing_type").val("0");
        }

      }else if(chemtype == "AC-131"){
        if (c_point < 6) {
          if ($('#accelerator_type').val() > 0) {
            $('#stat_accelerator').text('Good');
            $("#accelerator_add").text("Add");
            $("#accelerator_type_label").text(" AC-131 (KG)");
          }else{
            $("#accelerator_add").text("Add");
            $("#accelerator_type_label").text(" AC-131 (KG)");
            $("#accelerator_type_input").val("AC-131 (KG)");
            $('#stat_accelerator').text('');
            document.getElementById('accelerator_type').disabled = false; 
          }

        }else if( c_point > 9.0 ){
          if ($('#accelerator_type').val() > 0) {
            $('#stat_accelerator').text('Good');
            $("#accelerator_add").text("Add");
            $("#accelerator_type_label").text(" Water (L)");
          }else{
            $("#accelerator_add").text("Add");
            $("#accelerator_type_label").text(" Water (L)");
            $("#accelerator_type_input").val("Water (L)");
            $('#stat_accelerator').text('');
             document.getElementById('accelerator_type').disabled = false; 
          }
          
        }else{
          $("#accelerator_add").text("Increase/Decrease");
          $("#accelerator_type_label").text(" Point");
          $('#stat_accelerator').text('Good');
          document.getElementById('accelerator_type').disabled = true;
          $("#accelerator_type_input").val("");
          $("#accelerator_type").val("0");
        }
      }else{
        if (chemtype == "degreasing_type_val") {
          if (c_point > 0) {
            $('#stat_degreasing').text('Good');
          }else{
            $('#stat_degreasing').text('');
          }
        }else if (chemtype == "replenshing_type_val") {
          if (c_point > 0) {
            $('#stat_replenshing').text('Good');
          }else{
            $('#stat_replenshing').text('');
          }
        }else if (chemtype == "accelerator_type_val") {
          if (c_point > 0) {
            $('#stat_accelerator').text('Good');
          }else{
            $('#stat_accelerator').text('');
          }
        }
      }

    });
    $(document).on('change','#present_input', function(){
      // var valpre = $(this).val();
      // var valprev = $('#previous_input').val();
      // var diff = valpre - valprev;
      // alert(diff);
      //  $("#incoming_water_discharged").val(diff);
      console.log('hi');
    });
    $(document).on('click', '#painting_chemical_records_frm .next-tab', function(e){
      e.preventDefault();

      var stat = $('#stat_accelerator').text();
      if( stat != 'Good'){
        showNotification("danger", 'Before to proceed in submission of form please checked if all status is Good.', "now-ui-icons travel_info");
        return false;
      }

    });

</script>
<script type="text/javascript">
  $(document).on('click', '#chemical-records-modal .toggle-manual-input', function(e){
      $('#chemical-records-modal img').slideToggle();
      $('#chemical-records-modal .manual').slideToggle();
    });
</script>
<script type="text/javascript">
  $(document).on('submit', '#painting_chemical_records_frm', function(e){
      e.preventDefault();
     
      $.ajax({
        url: $(this).attr('action'),
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success) {
            showNotification("success", data.message, "now-ui-icons ui-1_check");
           $('#chemical-records-modal').modal('hide');
           $('#painting_chemical_records_frm').trigger("reset");
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
</script>
<script type="text/javascript">
  $(document).on('click', '#view-powder-Monitoring-btn', function(event){
      $.ajax({
        url:"/get_powder_records_modal_details",
        type:"GET",
        success:function(response){
          $('#powder_record_div').html(response);
          
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        },
      }); 
      $('#item_desc_div').hide();
       $('#powder-record-modal').modal('show');
       
    });

</script>

<script type="text/javascript">
  $(document).on('focus', '#powder_coating_monitoring_frm input[type=text]', function() {
      if($(this).data('edit') > 0){
        active_input = $(this).attr('id');
      }else{
        active_input = null;
      }
    });
    $(document).on('click', '#powder_coating_monitoring_frm .decimal', function() {
      $("#" + active_input).focus();
      var input = $('#powder_coating_monitoring_frm #' + active_input);
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
      var cons= input.attr('data-vali');
      if(cons =="consumption"){
        var cons= input.attr('data-id');
        var balance = $('#powder_coating_monitoring_frm #current' + cons).val();
        var consume= x + y;
        var consump = balance - consume;
        
        if(consump < 0){
          showNotification("danger", 'Consumed Qty must be less than Current Qty', "now-ui-icons travel_info");
        }else{
          $('#powder_coating_monitoring_frm #bal' + cons).val(consump);
        }
      }

      
      input.val(x + y);
    });
    $(document).on('click', '#powder_coating_monitoring_frm .numm', function() {
      $("#" + active_input).focus();
      var input = $('#powder_coating_monitoring_frm #' + active_input);
      var x = input.val();
      var y = $(this).text();
      // if (x == 0) {
      //   x = '';
      // }
      if (x == '0' && y != '.') {
        x = '';
      }

      if((x.indexOf(".") > -1) && y == "."){
        return false;
      }

      if (x == '0' && y == '.') {
        x = '0';
      }
      var cons= input.attr('data-vali');
      if(cons =="consumption"){
        var cons= input.attr('data-id');
        var balance = $('#powder_coating_monitoring_frm #current' + cons).val();
        var consume= x + y;
        var consump = balance - consume;
        
        if(consump < 0){
          $('#powder_coating_monitoring_frm #bal' + cons).val("0");
          showNotification("danger", 'Consumed Qty must be less than Current Qty', "now-ui-icons travel_info");
        }else{
          $('#powder_coating_monitoring_frm #bal' + cons).val(consump);
        }
      }
      input.val(x + y);

    });

    $(document).on('click', '#powder_coating_monitoring_frm .clear', function() {
      $("#" + active_input).focus();
      var input = $('#powder_coating_monitoring_frm #' + active_input);
      var x = input.val();
      var cons= input.attr('data-vali');
      console.log(x);
      if(cons =="consumption"){
        var cons= input.attr('data-id');
        var balance = $('#powder_coating_monitoring_frm #current' + cons).val();
        var consume= x;
        var consump = balance - consume;
        
        if(consump < 0){
          $('#powder_coating_monitoring_frm #bal' + cons).val("0");
          showNotification("danger", 'Consumed Qty must be less than Current Qty', "now-ui-icons travel_info");
        }else{
          $('#powder_coating_monitoring_frm #bal' + cons).val(consump);
        }
      }
      input.val(0);
    });

    $(document).on('click', '#powder_coating_monitoring_frm .del', function() {
      $("#" + active_input).focus();
      var input = $('#powder_coating_monitoring_frm #' + active_input);
      var x = input.val();
 
      input.val(x.substring(0, x.length - 1));
  
      if (input.val().length == 0) {
        input.val(0);
      }
      
      var cons= input.attr('data-vali');
      console.log(x);
      if(cons =="consumption"){
        var cons= input.attr('data-id');
        var balance = $('#powder_coating_monitoring_frm #current' + cons).val();
        var consume= x.substring(0, x.length - 1);
        var consump = balance - consume;
        
        if(consump < 0){
          $('#powder_coating_monitoring_frm #bal' + cons).val("0");
          showNotification("danger", 'Consumed Qty must be less than Current Qty', "now-ui-icons travel_info");
        }else{
          $('#powder_coating_monitoring_frm #bal' + cons).val(consump);
        }
      }
    });
    
</script>
<script type="text/javascript">
  $(document).on('click', '#powder-record-modal .toggle-manual-input', function(e){
      $('#powder-record-modal img').slideToggle();
      $('#powder-record-modal .manual').slideToggle();
    });
</script>
<script type="text/javascript">
     $(document).on('click', '#powder_coating_monitoring_frm .numm', function() {

        var valpre = $('#present_input_qty').val();
        // var valprev = $('#previous_input').val();
        // if (valpre == 0 || valpre =="") {
        //   $("#incoming_powder").val(0);
        // }
        // var diff = valpre - valprev;
        //  $("#incoming_powder").val(diff);
        $("#incoming_powder").val(valpre);

    });

</script>

<script type="text/javascript">
 
    $(document).on('change','.item_code_selection', function(){
      var valpre = $(this).val();
    if(valpre == "none"){

    }else{
        $.ajax({
          url:"/get_pwder_coat_desc/"+ valpre,
          type:"GET",
          success:function(data){
            
            $('#item_desc_label').html(data.item_desc);
            $('#item_label').html(data.item);
            $('#item_desc_div').show();
          }
        });
    }
     
    });
</script>
<script>
 $(document).on('submit', '#powder_coating_monitoring_frm', function(e){
      e.preventDefault();
     
      $.ajax({
        url: $(this).attr('action'),
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#powder-record-modal').modal('hide');
           $('#powder_coating_monitoring_frm').trigger("reset");

          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
      });
    });

    function get_itemcode_painting(){
        $.ajax({
          url: "/get_item_code_stock_adjustment_entries_painting",
          method: "GET",
          success: function(data) {
          $('#itemcode_line_painting').html(data);
            
          },
          error: function(data) {
          alert(data);
          }
        });
    }
    $(document).on('click', '.btn-stock-adjust-entry-painting', function(){
        get_itemcode_painting();
        $('#balance_qty_id_painting').val("");
        $('#item_description_input_painting').val("");
        $('#actual_qty_id_painting').text(0);
        $('#planned_qty_id_painting').text(0);
        $('#add-stock-entries-adjustment-painting').modal('show');
        $('#item_desc_div_painting').hide();
        $('#entry_type_div_painting').hide();
    });
    $('#add-stock-entries-adjustment-painting-frm').submit(function(e){
      e.preventDefault();
      var item_code = $('#itemcode_line').val();
      
      if(item_code == "default"){
        showNotification("danger", "Pls Select Item code", "now-ui-icons travel_info");
      }else{
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
            $('#add-stock-entries-adjustment-painting-frm').trigger("reset");
            $('#balance_qty_id_painting').val("");
            $('#item_description_input_painting').val("");
            $('#actual_qty_id_painting').text('0');
            $('#planned_qty_id_painting').text('0');
            $('#add-stock-entries-adjustment-painting').modal('hide');
            $('#item_desc_div_painting').hide();
            $('#entry_type_div_painting').hide();
            tbl_painting_stock_list();
            powder_coat_Chart();
            // inventory_history_list();

                // $('#edit-worktation-frm').trigger("reset");
                // workstation_list();

          } 
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      }); 
      }
    });
    $(document).on('click', '#tbl_painting_stock_pagination a', function(event){
         event.preventDefault();
         var page = $(this).attr('href').split('page=')[1];
         var filter_contents = "inventory_painting";
         var filter_content = "stock-list-painting";
		     var query = $('#inv-search-box').val();
	    	 var filters = 'q=' + query + '&' + $('#' + filter_contents).serialize();
         tbl_painting_stock_list(page, filters);
    });
    $(document).on('click', '#tbl_painting_consumed_pagination a', function(event){
         event.preventDefault();
         var page = $(this).attr('href').split('page=')[1];
         var filter_contents = "consumed_list_powder";
         var filter_content = "powderconsume-list-painting";
		     var query = $('#inv-search-box').val();
	    	 var filters = 'q=' + query + '&' + $('#' + filter_contents).serialize();
         tbl_powder_consumed_list(page, filters);
    });
</script>
@endsection