@extends('painting_operator.app', [
    'namePage' => 'MES',
    'activePage' => 'painting_dashboard',
])

@section('content')
<div class="panel-header" style="border: 1px solid; min-height: 350px;">
  <div class="header">
    <div class="row" style="margin-top: -70px; margin-left: -160px;">
      <div class="col-md-8 text-white">  
          <table style="width: 85%; margin-left: 15px;">  
            <tr>  
              <td style="width: 50%; border-right: 5px solid;">
              <h2 class="title text-center" style="font-size:10px;">
                <div class="pull-right" style="margin-right: 30px;">
                  <span style="display: block; font-size: 17pt;">{{ date('M-d-Y') }}</span>
                  <span style="display: block; font-size: 13pt;">{{ date('l') }}</span>
                </div>
              </h2>
            </td>
            <td style="width: 50%;" style="pull-left">
              <h4 id="qwe" class="title text-center" style="margin: 1px auto; font-size: 25pt;margin-left:-100px;">-:--:-- --</h4>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
@include('painting_operator.modal_enter_operator_id')
@include('modals.search_productionorder')
<div class="content" style="margin-top: -100px; min-height: 10px;">

<div class="row" style="margin-top: -265px;">
  @foreach($breaktime_data as $r => $row)
  <div class="col-md-12" id="{{$r}}{{$row['div_id']}}" style="display:none; margin-top:-30px;margin-bottom:10px;">
    <div class="alert alert-primary text-center" role="alert">
      <span class="d-none"></span>
      <div class="container">
        <div class="alert-icon" style="color:black;">
          <i class="now-ui-icons ui-2_time-alarm" style="padding-right:5px;font-size:30px;"></i><span style="font-size:18pt;"> <b>{{$row['break_type']}} :</b></span> 
                <span class="ml-1 font-weight-bold" style="font-size:25px;">{{$row['time_in_show']}} -  {{$row['time_out_show']}} </span>
        </div>
      </div>
    </div>
  </div>
  @endforeach
  <div class="col-md-6" style="margin-top: 20px;">
    <div style="margin-top:-25px; padding-bottom:25px;">
      <h3 class="text-center font-weight-bold text-white" style="text-transform: uppercase; margin: 100px 0 0 0; font-size: 20pt; letter-spacing: 8px;">Painting</h3>
      <h2 class="text-center font-weight-bold text-white" style="font-style: italic; text-transform: uppercase; margin: 20px 8px 8px 8px; font-size: 30pt;">{{ $process_details->process_name }} Area</h2>
      <h5 class="card-title text-center" style="font-size: 15pt; margin: 100px 10px 10px 10px;">
        <span style="font-size: 17pt;"><b>Machine Status</b></span>
      </h5>
      <center>
      <button type="button" class="btn btn-block btn-danger" id="machine-power-btn" style="height: 70px; width: 330px; font-size: 20pt; background-color: {{ $machine_status == 'Start Up' ? '#717D7E' : '#28B463' }};">
        <i class="now-ui-icons media-1_button-power"></i>
        <span style="padding: 3px;">{{ ($machine_status == 'Start Up') ? 'Unavailable' : 'Available' }}</span>
      </button></center>
    </div>
    
    
  </div>
  <div class="col-md-6">
    <div class="card" style="min-height: 500px;">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <h5 class="text-black mt-1 text-center text-uppercase">
              Enter Biometric ID
            </h5>
            <div id="jt-scan-img">
              <center>
                <img src="{{ asset('img/tap.gif') }}" width="330" height="250" id="toggle-jt-numpad" style="margin: -15px 10px 50px 10px;">
              </center>
            </div>
          </div>
          <div class="col-md-10 offset-md-1" style="display: none;" id="jt-numpad">
            <div class="form-group">
              <div class="input-group">
                 <input type="text" class="form-control" id="bio-id" style="font-size: 15pt;" required>
              </div>
            </div>
            <div id="user-id-numpad">
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
                  <span class="numpad" onclick="document.getElementById('bio-id').value=document.getElementById('bio-id').value.slice(0, -1);"><</span>
                  <span class="numpad num">0</span>
                  <span class="numpad" onclick="document.getElementById('bio-id').value='';">Clear</span>
                </div>
              </div>
              <div class="row">
                <div class="col-md-10 offset-md-1">
                  <button type="submit" class="btn btn-block btn-primary btn-lg" id="submit-user-id-btn">SUBMIT</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@foreach($breaktime_data as $r => $row)
  <input type="hidden" class="breaktime_input" value="{{$row['break_type']}}" data-timein="{{$row['time_in']}}" data-timeout="{{$row['time_out']}}" data-type="{{$row['break_type']}}" data-divid="{{$r}}{{$row['div_id']}}">
@endforeach
<!-- Modal -->
<div class="modal fade" id="machine-enter-operator-id-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title text-center">Modal Title</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                     <h6 class="text-center">Enter Biometric ID</h6>
                     <form action="#" method="POST" autocomplete="off">
                        @csrf
                        <div class="row">
                           <div class="col-md-10 offset-md-1">
                              <div class="form-group">
                                <input type="hidden" id="machine-status" name="category">
                                 <input type="text" class="form-control" name="operator_id" id="machine-user-id" style="font-size: 15pt; text-align: center;" required>
                              </div>
                           </div>
                        </div>
                        <div class="text-center numpad-div">
                           <div class="row1">
                              <span class="numpad" onclick="document.getElementById('machine-user-id').value=document.getElementById('machine-user-id').value + '1';">1</span>
                              <span class="numpad" onclick="document.getElementById('machine-user-id').value=document.getElementById('machine-user-id').value + '2';">2</span>
                              <span class="numpad" onclick="document.getElementById('machine-user-id').value=document.getElementById('machine-user-id').value + '3';">3</span>
                           </div>
                           <div class="row1">
                              <span class="numpad" onclick="document.getElementById('machine-user-id').value=document.getElementById('machine-user-id').value + '4';">4</span>
                              <span class="numpad" onclick="document.getElementById('machine-user-id').value=document.getElementById('machine-user-id').value + '5';">5</span>
                              <span class="numpad" onclick="document.getElementById('machine-user-id').value=document.getElementById('machine-user-id').value + '6';">6</span>
                           </div>
                           <div class="row1">
                              <span class="numpad" onclick="document.getElementById('machine-user-id').value=document.getElementById('machine-user-id').value + '7';">7</span>
                              <span class="numpad" onclick="document.getElementById('machine-user-id').value=document.getElementById('machine-user-id').value + '8';">8</span>
                              <span class="numpad" onclick="document.getElementById('machine-user-id').value=document.getElementById('machine-user-id').value + '9';">9</span>
                           </div>
                           <div class="row1">
                              <span class="numpad" onclick="document.getElementById('machine-user-id').value=document.getElementById('machine-user-id').value.slice(0, -1);"><</span>
                              <span class="numpad" onclick="document.getElementById('machine-user-id').value=document.getElementById('machine-user-id').value + '0';">0</span>
                              <span class="numpad" onclick="document.getElementById('machine-user-id').value='';">Clear</span>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-10 offset-md-1">
                              <button type="submit" class="btn btn-block btn-primary btn-lg">LOGIN</button>
                           </div>
                        </div>
                     </form>
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
        <div class="text-white rounded-top" style="background-color: #0277BD;">
          <div class="d-flex flex-row justify-content-between p-3 align-items-center">
            <h5 class="font-weight-bold m-0 p-0">Job Ticket</h5>
            <div class="float-right">
              <h5 class="modal-title font-weight-bold p-0 mr-5 font-italic d-inline-block">Modal Title</h5>
              <button type="button" class="close d-inline-block ml-3" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          </div>
        </div>
        <div class="modal-body" style="min-height: 600px;">
          <div id="production-search-content-modal2"></div>
        </div>
      </div>
    </div>
  </div>

@include('painting_operator.modal_view_production_order_details')
@include('painting_operator.modal_view_schedule')

<style type="text/css">
    @-webkit-keyframes blinker_break {
      from { background-color: #fa764b; }
      to { background-color: inherit; }
    }
    @-moz-keyframes blinker_break {
      from { background-color: #fa764b; }
      to { background-color: inherit; }
    }
    @-o-keyframes blinker_break {
      from { background-color: #fa764b; }
      to { background-color: inherit; }
    }
    @keyframes blinker_break {
      from { background-color: #fa764b; }
      to { background-color: inherit; }
    }
    
    .blink_break{
      text-decoration: blink;
      -webkit-animation-name: blinker;
      -webkit-animation-duration: 3s;
      -webkit-animation-iteration-count:infinite;
      -webkit-animation-timing-function:ease-in-out;
      -webkit-animation-direction: alternate;
    }
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
  
  .tap_here {
    animation: bounce 1s linear infinite;
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

  .dot {
    height: 20px;
    width: 20px;
    background-color: #bbb;
    border-radius: 50%;
    display: inline-block;
  }

  .text-blink {color: orange;
    animation: blinker 1s linear infinite;
  }

  @keyframes blinker {  
    50% { opacity: 0; }
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
  #powder-record-modal .form-control {
  border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px;
  }
</style>
@include('quality_inspection.modal_inspection')

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
                            <span class="numpad num">-</span>
                            <span class="numpad num">0</span>
                            <span class="numpad" onclick="document.getElementById('production-order-qc').value=document.getElementById('production-order-qc').value.slice(0, -1);"><</span>
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

<div class="modal fade" id="select-process-for-inspection-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document" style="min-width: 95%;">
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

@endsection

@section('script')
<script src="{{ asset('/js/jquery.rfid.js') }}"></script>
<script>
  $(document).ready(function(){
    var workstation = "Painting";
    var active_input = null;
    
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $(document).on('click', '.reject-confirmation-btn', function(e){
      e.preventDefault();
      
      var inspection_type = $(this).data('inspection-type');
      var workstation = $(this).data('workstation');
      var production_order = $(this).data('production-order');
      var process_id = $(this).data('process-id');
      var qa_id = $(this).data('qaid');

      $.ajax({
        url:"/get_reject_confirmation_checklist/" + production_order + "/" + workstation + "/" + process_id + "/" + qa_id + "?page=operator",
        type:"GET",
        success:function(data){
          $('#quality-inspection-div').html(data);
          $('#quality-inspection-modal .qc-type').text(inspection_type);
          $('#quality-inspection-modal .qc-workstation').text('[' + workstation + ']');
          $('#quality-inspection-modal').modal('show');
        }
      });
    });

    $(document).on('click', '#reject-confirmation-frm .next-tab', function() {
      $('#reject-confirmation-frm .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').tab('show');
  });

  $(document).on('click', '#reject-confirmation-frm .prev-tab', function() {
    $('#reject-confirmation-frm .nav-tabs li > .active').parent().prev().find('a[data-toggle="tab"]').tab('show');
  });

  $(document).on('submit', '#reject-confirmation-frm', function(e){
      e.preventDefault();
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
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
      });
    });

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
      var workstation = $(this).data('workstation');
      var inspection_type = $(this).data('inspection-type');
      var reject_category = $(this).data('reject-cat');

      var data = {
        time_log_id: $(this).data('timelog-id'),
        inspection_type,
        reject_category
      }
      $.ajax({
        url: '/get_checklist/' + workstation + '/' + production_order + '/' + process_id,
        type:"GET",
        data: data,
        success:function(response){
          active_input = null;
          $('#quality-inspection-div').html(response);
          $('#quality-inspection-modal .qc-type').text(inspection_type);
          $('#quality-inspection-modal').modal('show');
        }, error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        },
      });
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

    $(document).on('click', '#quality-inspection-modal .toggle-manual-input', function(e){
      $('#quality-inspection-modal img').slideToggle();
      $('#quality-inspection-modal .manual').slideToggle();
    });

    $('#confirm-sample-size-btn').click(function(e){
      e.preventDefault();
      var tab_id = $('#sample-size-tab-id').val();
      $('#' + tab_id + '-validated-sample-size').val(1);
      $('#' + tab_id + '-next-btn').trigger('click');
      $('#confirm-sample-size-modal').modal('hide');
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


    $('#submit-user-id-btn').click(function(e){
      e.preventDefault();

      var production_order = $('#bio-id').val();
      var process_id = {{ $process_details->process_id }};
    });

    function get_production_order_details(production_order, process_id){
      $.ajax({
        url:"/get_production_order_details/" + production_order + "/" + process_id,
        type:"GET",
        success:function(data){
          if(data.success == 2){
            showNotification("info", data.message, "now-ui-icons travel_info");
          }else if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            var reference_no = (data.details.sales_order) ? data.details.sales_order : data.details.material_request;
            var pending_qty = parseInt(data.details.qty_to_manufacture) - parseInt(data.details.completed_qty);
            $('#enter-operator-id-modal .production-order').text(data.details.production_order);
            $('#enter-operator-id-modal .customer-name').text(data.details.customer);
            $('#enter-operator-id-modal .reference-no').text(reference_no);
            $('#enter-operator-id-modal .item-code').text(data.details.item_code);
            $('#enter-operator-id-modal .item-description').text(data.details.description);
            $('#enter-operator-id-modal .required-qty').text(data.details.qty_to_manufacture);
            $('#enter-operator-id-modal .pending-qty').text(pending_qty);
            $('#enter-operator-id-modal .completed-qty').text(data.details.completed_qty);

            $('#enter-operator-id-modal .modal-title').text('{{ $process_details->process_name }} Process');
            $('#enter-operator-id-modal').modal('show');
          }
        }, 
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    }

    $('#enter-operator-id-modal form').submit(function(e){
      e.preventDefault();
      var process_name = '{{ $process_details->process_name }}';
      var operator_id = $('#user-id').val();
      var production_order = $('#enter-operator-id-modal .production-order').text();

      var data = {  
        operator_id: operator_id, 
        process_name: process_name, 
        _token: '{{ csrf_token() }}',
        production_order: production_order
      }

      $.ajax({
        url:"/painting/login",
        type:"post",
        data: data,
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", 'Logging in. Please wait..', "now-ui-icons ui-1_check");
            window.location.href = data.url;
          }
        }, 
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });

    $('#machine-power-btn').click(function(e){
      e.preventDefault();
      $('#machine-enter-operator-id-modal .modal-title').text('{{ $machine_status }} Machine');
      $('#machine-status').val('{{ $machine_status }}');
      $('#machine-enter-operator-id-modal').modal('show');
    });

    $('#machine-enter-operator-id-modal form').submit(function(e){
      e.preventDefault();
      console.log($(this).serialize());

      $.ajax({
        url:"/insert_machine_logs",
        type:"post",
        data: $(this).serialize(),
        success:function(data){
          $('#machine-enter-operator-id-modal').modal('hide');
          location.reload();
        }, 
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });

    $('#view-painting-schedule-btn').click(function(e){
      e.preventDefault();
      $.ajax({
        url:"/get_scheduled_for_painting",
        type:"GET",
        success:function(data){
          $('#view-scheduled-task-tbl').html(data);
          $('#view-scheduled-task-modal').modal('show');
        }
      });  
    });
    
    $(document).on('click', 'a', function(e){
      e.preventDefault();
      location.assign($(this).attr('href'));
    });

    $('#toggle-jt-numpad').click(function(e){
      e.preventDefault();
      $('#jt-numpad').slideToggle();
      $('#jt-scan-img').slideToggle();
    });

    $(document).on('click', '#job-ticket-numpad .num', function(e){
      e.preventDefault();
      var num = $(this).text();
      var current = $('#job-ticket-id').val();
      var new_input = current + num;
         
      $('#job-ticket-id').val(new_input);
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

    $('#jt-search-btn').click(function(e){
      e.preventDefault();
      $('#jt-search-modal').modal('show');
    });

    // numpad for production order search
    $(document).on('click', '.prod-search-numpad', function(e){
      e.preventDefault();
      var num = $(this).text();
      var current = $('#jt-no-search').val();
      var new_input = current + num;
         
      $('#jt-no-search').val(new_input);
    });

    $('#jt-search-frm').submit(function(e){
      e.preventDefault();
      var jtno = "PROM-"+$('#jt-no-search').val();
      $('#jt-workstations-modal .modal-title').text(jtno + " [" + workstation + "]");
      getJtDetails(jtno);
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
    $(document).on('click', '.production_order_link', function(e){
      e.preventDefault();
      var production_order = $(this).attr('data-jtno');
      console.log(production_order);
      $('#jt-workstations-modal2 .modal-title').text(production_order);
      getJtDetails2(production_order);
    });

    $(document).on('click', '.view-prod-details-btn', function(e){
      e.preventDefault();
      $('#jt-workstations-modal .modal-title').text($(this).text() + " [" + workstation + "]");
      getJtDetails($(this).text());
    });

    startInterval();
    function showNotification(color, message, icon){
      $.notify({
        icon: icon,
        message: message
      },{
        type: color,
        timer: 300,
        placement: {
          from: 'top',
          align: 'center'
        }
      });
    }

    $(document).on('show.bs.modal', '.modal', function (event) {
      var zIndex = 1040 + (10 * $('.modal:visible').length);
      $(this).css('z-index', zIndex);
      setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
      }, 0);
    });

    $('.modal').on('shown.bs.modal', function() {
      $(this).find('[autofocus]').focus();
    });

    // Parses raw scan into name and ID number
    var rfidParser = function (rawData) {
      // console.log(rawData, rawData.length);
      if (rawData.length < 4) return null;
      else return rawData;
    };

    // Called on a good scan (company card recognized)
    var goodScan = function (cardData) {
      if($('#jt-search-modal').is(':visible') == true){
        $('#jt-search-modal #jt-no-search').val(cardData.substring(5));
        $('#jt-workstations-modal .modal-title').text(cardData + " [" + workstation + "]");
        getJtDetails(cardData);

        return false;
      }else{
        var process_id = {{ $process_details->process_id }};
  
        get_production_order_details(cardData, process_id);
      }
    };

    // Called on a bad scan (company card not recognized)
    var badScan = function() {
      console.log("Bad Scan.");
    };

    // Initialize the plugin.
    $.rfidscan({
      parser: rfidParser,
      success: goodScan,
      error: badScan
    });

    $('.modal').on('hidden.bs.modal', function(){
      var frm = $(this).find('form')[0];
      if (frm) frm.reset();
    });


  });
</script>
<script>
  var now = new Date(<?php echo time() * 1000 ?>);
  function startInterval(){  
    setInterval('showTime();', 1000);
  }

  function showTime() {
    manilaTime = new Date();
    var clock = document.getElementById('qwe');
    if(clock){
      clock.innerHTML = manilaTime.toLocaleTimeString();//adjust to suit
    }
    var timeformat = manilaTime.toTimeString();
    $('.breaktime_input').each(function() {
      var div_id= "#" + $(this).data('divid');
      if($(this).data('timein') <= timeformat && $(this).data('timeout') >= timeformat ){
        $(div_id).show();
        $(div_id).addClass("blink_break");
      }else{
        $(div_id).hide();
        $("#div_id").removeClass("blink_break");

      }
    });
  }
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
    
</script>
@endsection