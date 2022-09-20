@extends('layouts.app', [
    'namePage' => 'MES',
    'activePage' => 'main_dashboard',
])

@section('content')
<div class="panel-header">
  <div class="header">
    <div class="row">
      <div class="col-md-12 text-white">  
          <table style="width: 97%; margin-left: 15px;">  
            <tr>  
              <td style="width: 20%; border-right: 5px solid;">
              <h2 class="title text-center">
                <div class="pull-left" style="margin-left: 30px;">
                  <span style="display: block; font-size: 16pt;">{{ date('M-d-Y') }}</span>
                  <span style="display: block; font-size: 12pt;">{{ date('l') }}</span>
                </div>
              </h2>
            </td>
            <td style="width: 25%; border-right: 5px solid;">
              <h2 id="qwe" class="title text-center" style="margin: 2px auto;">-:--:-- --</h2>
            </td>
            <td style="width: 55%;">
              <h2 class="title" style="margin: 2px auto; padding-left: 10px;">{{ $workstation_name }}</h2>
            </td>
          </tr>
        </table>
      </div>
    </div>
    
  </div>
</div>
@include('modals.view_operators_load')
<input type="hidden" name="workstation_name" value="{{ $workstation }}" id="workstation_name">
<input type="hidden" name="workstation_id" value="{{ $workstation_id }}" id="workstation_id">
<div class="content" style="margin-top: -50px; min-height: 100px;">
  <div class="row text-center" id="header_datas">
    <div class="col-md-3 workstation-status-div" data-status="Pending" data-title="Pending" style="margin-top: -30px;">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Pending</h4>
        </div>
        <div class="card-body">
          <h1 class="pending_jt m-0 p-0">0</h1>
          <small>unit(s)</small>
        </div>
      </div>
    </div>
    <div class="col-md-3 workstation-status-div" data-status="In Progress" data-title="In Progress" style="margin-top: -30px;">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">In Progress</h4>
        </div>
        <div class="card-body">
          <h1 class="inprogress_jt m-0 p-0">0</h1>
          <small>unit(s)</small>
        </div>
      </div>
    </div>
    <div class="col-md-3 workstation-status-div" data-status="Completed" data-title="Completed" style="margin-top: -30px;">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Completed</h4>
        </div>
        <div class="card-body">
          <h1 class="completed_jt m-0 p-0">0</h1>
          <small>unit(s)</small>
        </div>
      </div>
    </div>
    <div class="col-md-3 workstation-status-div" data-status="Rejects" data-title="Rejects" style="margin-top: -30px;">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Rejects</h4>
        </div>
        <div class="card-body">
          <h1 class="rejects_jt m-0 p-0">0</h1>
          <small>unit(s)</small>
        </div>
      </div>
    </div>
    <div class="col-md-12">
        <h5 class="text-black mt-5">Scan Job Ticket</h5>
        <img src="{{ asset('img/tap.gif') }}" style="margin-top: -50px;"  width="300" height="200"/>
    </div>
     <div class="col-md-12">
      @if(isset($workstation_list))
      <table style="width: 100%; display: block; overflow-x: auto; white-space: nowrap;">
        <tr>
          @foreach($workstation_list as $row)
          <td>
            <a href="/operator/{{ $row }}" class="custom-a">
              <div class="card" style="width: 180px; height: 80px; margin: 5px; font-size: 14pt;">
                <div style="white-space: normal; margin: 10px auto;"><b>{{ $row }}</b></div>
              </div>
            </a>
          </td>
          @endforeach
        </tr>
      </table>
      @endif
    </div>
  </div>
</div>

@include('modals.search_productionorder')

<!-- Modal -->
<div class="modal fade" id="jt-workstations-modal" tabindex="-1" role="dialog">
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
          <div id="production-search-content"></div>
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
<div class="modal fade" id="scan-jt-for-qc-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-md" role="document">
     <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #f57f17;">
           <h5 class="modal-title">Quality Inspection [<b>@if(isset($workstation_name)){{ $workstation_name }}@else{{$workstation}}@endif</b>]</h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
           </button>
           <input type="hidden" id="workstation" value="@if(isset($workstation_name)){{ $workstation_name }}@else{{$workstation}}@endif">
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

<div class="modal fade" id="select-process-for-inspection-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document" style="min-width: 90%;">
     <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #f57f17;">
           <h5 class="modal-title"><b>@if(isset($workstation_name)){{ $workstation_name }}@else{{$workstation}}@endif</b> - <span class="production-order"></span></h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
           </button>
           <input type="hidden" id="workstation" value="@if(isset($workstation_name)){{ $workstation_name }}@else{{$workstation}}@endif">
        </div>
        <div class="modal-body" style="min-height: 480px;">
           <div class="row" id="tasks-for-inspection-tbl" style="margin-top: 10px;"></div>
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


@include('modals.scan_production_order_modal')
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
    height: 12px;
    width: 12px;
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
</style>
@endsection

@section('script')
<script src="{{ asset('/js/jquery.rfid.js') }}"></script>
<link rel="stylesheet" href="{{ asset('/css/datepicker/landscape.css') }}" type="text/css" media="print" />
<script>
  $(document).ready(function(){
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

    var workstation = "{{ $workstation_name }}";

    var active_input = null;

      $(document).on('change', '.select-all-checklist-per-tab', function(e){
        e.preventDefault();
        var selector = '.' + $(this).attr('id');
        $(selector).not(this).prop('checked', this.checked);
      });
      
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
        var workstation = $(this).data('workstation');
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

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $(document).on('click', 'a', function(e){
      e.preventDefault();
      location.assign($(this).attr('href'));
    });

    $('#scan-production-order-btn').click(function(e){
      e.preventDefault();
      $('#scan-production-order-modal').modal('show');
    });

    // numpad for production order
    $(document).on('click', '#scan-production-order-modal #enter-production-order .num', function(e){
      e.preventDefault();
      var num = $(this).text();
      var current = $('#scan-production-order-modal #enter-production-order input[type="text"]').val();
      var new_input = current + num;
      new_input = format(new_input.replace(/-/g, ""), [5], "-");
         
      $('#scan-production-order-modal #enter-production-order input[type="text"]').val(new_input);
    });

    // numpad for operator id - step 3
    $(document).on('click', '#scan-production-order-step3-modal #enter-operator-id .num', function(e){
      e.preventDefault();
      var num = $(this).text();
      var current = $('#scan-production-order-step3-modal #enter-operator-id input[type="text"]').val();
      var new_input = current + num;
      new_input = format(new_input.replace(/-/g, ""), [5], "-");
         
      $('#scan-production-order-step3-modal #enter-operator-id input[type="text"]').val(new_input);
    });

    // submit production order
    $(document).on('click', '#scan-production-order-modal .submit-enter-production-order', function(e) {
      e.preventDefault();
      var production_order = $('#scan-production-order-modal #production-order').val();
      var workstation = $('#scan-production-order-modal #workstation').val();
      if (!production_order) {
        showNotification("danger", 'Please enter Production Order No.', "now-ui-icons travel_info");
        return false;
      }
      
      get_production_order_task(production_order, workstation);
    });

    // select process
    $(document).on('click', '#scan-production-order-step1-modal .selected-process', function(e) {
      e.preventDefault();
      var process_id = $(this).data('process');
      
      get_workstation_process_machines(process_id);
      $('#scan-production-order-step3-modal input[name="process_id"]').val(process_id);
      $('#scan-production-order-step2-modal .production-order').text($('#scan-production-order-step1-modal .production-order').text());
      $('#scan-production-order-step2-modal #step2-row').html($('#scan-production-order-step1-modal #step1-row').html());
      $('#scan-production-order-step2-modal').modal('show');
    });

    // select machine
    $(document).on('click', '#scan-production-order-step2-modal .selected-machine', function(e) {
      e.preventDefault();
      var machine_code = $(this).data('machine-code');
      var process_id = $(this).data('process-id');
      var production_order = $('#scan-production-order-step2-modal .production-order').text();
      var machine_status = $(this).data('status').toLowerCase();

      // if (machine_status == 'unavailable') {
      //   showNotification("danger", 'Selected machine is currently unavailable. Please select another machine.', "now-ui-icons travel_info");
      //   return false;
      // }

      $('#scan-production-order-step3-modal input[name="machine_code"]').val(machine_code);
      $('#scan-production-order-step3-modal input[name="production_order"]').val(production_order);
      $('#scan-production-order-step3-modal .production-order').text(production_order);
      $('#scan-production-order-step3-modal input[name="process_id"]').val(process_id);
      $('#scan-production-order-step3-modal #step3-row').html($('#scan-production-order-step2-modal #step2-row').html());
      $('#scan-production-order-step3-modal').modal('show');
    });

    // submit operator id / submit form
    $(document).on('click', '#scan-production-order-step3-modal .submit-enter-operator-id', function(e) {
      e.preventDefault();
      if (!$('#scan-production-order-step3-modal #operator-id').val()) {
        showNotification("danger", 'Please enter Operator ID.', "now-ui-icons travel_info");
        return false;
      }

      $('#scan-production-order-step3-modal form').submit();
    });

    $(document).on('submit', '#scan-production-order-step3-modal form', function(e){
      e.preventDefault();
      var data = {
        production_order: $('#scan-production-order-step3-modal form input[name="production_order"').val(),
        process_id: $('#scan-production-order-step3-modal form input[name="process_id"').val(),
        machine_code: $('#scan-production-order-step3-modal form input[name="machine_code"').val(),
        operator_id: $('#scan-production-order-step3-modal form input[name="operator_id"').val(),
        _token: '{{ csrf_token() }}'
      }

      login_operator(data);
    });

    function get_production_order_task(production_order, workstation){
      $.ajax({
        url: '/get_production_order_task/PROM-' + production_order + '/' + workstation,
        type:"GET",
        success:function(response){
          if (response.success > 0) {
            var ref_no = (response.details.production_order.sales_order) ? response.details.production_order.sales_order : response.details.production_order.material_request;
            $('#scan-production-order-step1-modal .production-order').text(response.details.production_order.production_order);
            $('#scan-production-order-step1-modal .item-code').text(response.details.production_order.item_code);
            $('#scan-production-order-step1-modal .description').text(response.details.production_order.description);
            $('#scan-production-order-step1-modal .qty').text(response.details.production_order.qty_to_manufacture);
            $('#scan-production-order-step1-modal .stock-uom').text(response.details.production_order.stock_uom);
            $('#scan-production-order-step1-modal .ref-no').text(ref_no);
            $('#scan-production-order-step1-modal .customer').text(response.details.production_order.customer);

            get_workstation_process(response.details.tasks);
            $('#scan-production-order-step1-modal').modal('show');
          }else{
            showNotification("danger", response.message, "now-ui-icons travel_info");
          }
        }, error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        },
      });
    }

    function get_workstation_process(arr){
      $('#scan-production-order-step1-modal #process-list-div').empty();
      var col = '';
      var stat = 0;
      var status = 'Pending';
      $.each(arr, function(i, v){
        if (v.has_in_progress) {
          color = 'in_progress';
          stat = 1;
          status = 'In Progress';
        }else{
          if (v.remaining_qty <= 0) {
            color = 'completed';
            stat = 2
            status = 'Completed';
          }else{
            color = 'pending';
            stat = 0
            status = 'Pending';
          }
        }
 
        col += '<li class="text-white selected-process ' + color + '" data-process="'+v.process_id+'" data-remaining-qty="' + v.remaining_qty +'" data-status="'+stat+'">' +
          '<table style="width: 100%;">'+
          '<tr><td class="text-center"><b>' + v.process_name + '</b></td></tr>' +
          '<tr><td><div class="pull-left">Bal. Qty: <span style="font-size: 14pt;"><b>' + v.remaining_qty + '</b></span></div>' +
            '<div class="pull-right">Ctd Qty: <span style="font-size: 14pt;"><b>'+v.completed_qty+'</b></span></div></td></tr>' +
          '<tr><td class="text-center"><b>' + status + '</b></td></tr>' +
          '</table>' +
          '</li>';
      });

      $('#scan-production-order-step1-modal #process-list-div').append(col);
    }

    function get_workstation_process_machines(process_id){
      var workstation_id = $('#workstation_id').val();
      $('#scan-production-order-step2-modal #machine-list-div').empty();
      $.ajax({
        url: "/get_workstation_process_machine/" + workstation_id + "/" + process_id ,
        method: "GET",
        success: function(response) {
          var col = '';
          $.each(response, function(i, v){
            if (v.status == 'Available') {
              color = '#28B463';
            }else if (v.status == 'On-going Maintenance'){
              color = '#D68910';
            }else{
              color = '#C0392B';
            }
            
            col += '<div class="col-md-4 selected-machine" data-machine-code="'+v.machine_code+'" data-process-id="' + v.process_id+'" data-status="' + v.status + '">' +
              '<div class="card" style="background-color: #1B4F72;">' +
              '<div class="card-body" style="padding-top: 0; padding-bottom: 0;">' +
              '<div class="row" style="border: 0px solid; ">' +
              '<div class="col-md-4" style="padding: 0;">' +
              '<img src="'+ v.image +'" style="width: 100px; height: 100px;">' +
              '</div>'+
              '<div class="col-md-8">' +
              '<h5 class="card-category text-white" style="padding: 0; margin: 0">' + v.machine_name + ' ['+v.machine_code+']</h5>' +
              '<p class="text-white"">' +
              '<span class="dot" style="background-color: '+ color + ';"></span> ' + v.status + ' </p></div>' +
              '</div></div></div></div>';
          });

          $('#scan-production-order-step2-modal #machine-list-div').append(col);
        },
        error: function(response) {
          console.log(response);
        }
      });
    }

    function login_operator(data){
      $.ajax({
        url:"/login_operator_via_jt",
        type:"post",
        data: data,
        success:function(data){
          console.log(data);
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", 'Logging in. Please wait..', "now-ui-icons ui-1_check");
            window.location.href="/spotwelding_dashboard/"+data.details.machine_code+"/"+data.details.job_ticket_id;
          }
        }, 
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });  
    }

    $('#toggle-jt-numpad').click(function(e){
      e.preventDefault();
      $('#scan-production-order-modal #jt-numpad').slideToggle();
      $('#scan-production-order-modal #jt-scan-img').slideToggle();
    });

    $('#toggle-operator-numpad').click(function(e){
      e.preventDefault();
      $('#scan-production-order-step3-modal #operator-numpad').slideToggle();
      $('#scan-production-order-step3-modal #operator-scan-img').slideToggle();
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
      new_input = format(new_input.replace(/-/g, ""), [5], "-");
         
      $('#jt-no-search').val(new_input);
    });

    $('#jt-search-frm').submit(function(e){
      e.preventDefault();
      var jtno = "PROM-"+$('#jt-no-search').val();
      $('#jt-workstations-modal .modal-title').text(jtno + " [" + workstation + "]");
      getJtDetails(jtno);
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
    $(document).on('click', '.production_order_link', function(e){
      e.preventDefault();
      var production_order = $(this).attr('data-jtno');
      console.log(production_order);
      $('#jt-workstations-modal2 .modal-title').text(production_order);
      getJtDetails2(production_order);
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
    $(document).on('click', '.view-prod-details-btn', function(e){
      e.preventDefault();
      $('#jt-workstations-modal .modal-title').text($(this).text() + " [" + workstation + "]");
      getJtDetails($(this).text());
    });

    startInterval();
    setInterval(' header_data();', 3000);
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
  
      if ($('#scan-production-order-step3-modal').is(':visible') == true) {
        $('#scan-production-order-step3-modal #operator-id').val(cardData);
        $('#scan-production-order-step3-modal form').submit();
        return false;
      }

      if ($('#scan-production-order-step2-modal').is(':visible') == true) {
        var machine_code = cardData;
        var production_order = $('#scan-production-order-step2-modal .production-order').text();

        $.ajax({
          url:'/validate_workstation_machine/' + machine_code + '/' + workstation,
          type:"GET",
          success:function(data){
            if (data.success) {
              $('#scan-production-order-step3-modal input[name="machine_code"]').val(machine_code);
              $('#scan-production-order-step3-modal input[name="production_order"]').val(production_order);
              $('#scan-production-order-step3-modal .production-order').text(production_order);
              $('#scan-production-order-step3-modal #step3-row').html($('#scan-production-order-step2-modal #step2-row').html());
              $('#scan-production-order-step3-modal').modal('show');
              
            }else{
              showNotification("danger", "Machine not found. Try again.", "now-ui-icons travel_info");
            }
          }
        });

        return false;
      }

      if ($('#scan-production-order-modal').is(':visible') == true) {
        $('#scan-production-order-modal #production-order').val(cardData.substring(5));
        get_production_order_task(cardData.substring(5), workstation);
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

      if ($('#scan-jt-for-qc-modal').is(':visible') == true) {
        $('#scan-jt-for-qc-modal #production-order-qc').val(cardData.substring(5));
        get_tasks_for_inspection(workstation, cardData);
        return false;
      }

      if($('#jt-search-modal').is(':visible') == true){
        $('#jt-search-modal #jt-no-search').val(cardData.substring(5));
        $('#jt-workstations-modal .modal-title').text(cardData + " [" + workstation + "]");
        getJtDetails(cardData);

        return false;
      }

      get_production_order_task(cardData.substring(5), workstation);
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

    $('.workstation-status-div').click(function(){
      var status = $(this).data('status');
      var workstation = $('#workstation_name').val(); 
      var combine = $(this).data('title')+ " " + "[<b>" + workstation + "</b>]";
      $.ajax({
        url: "/operator/header_table_data/" + workstation +"/"+ status,
        method: "GET",
        success: function(response) {
          $('#workstation_name_title').html(combine);
          $('#data_table_entry').html(response);
          $('#view_operators_load_modal').modal('show');
        },
        error: function(response) {
          console.log(response);
        }
      });
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
  }
</script>
<script type="text/javascript">
  function header_data(){
    var workstation = $('#workstation_name').val();
    $.ajax({
      url: "/operator/header_total_data/" + workstation,
      method: "GET",
      success: function(response) {
        $('#header_datas .rejects_jt').text(response.rejects);
        $('#header_datas .completed_jt').text(response.completed);
        $('#header_datas .pending_jt').text(response.pending);
        $('#header_datas .inprogress_jt').text(response.inprogress);
      }
    });  
  }

</script>
@endsection