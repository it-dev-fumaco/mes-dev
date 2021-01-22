@extends('painting_operator.app', [
    'namePage' => 'Painting',
    'activePage' => 'painting_task',
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
  </div>
</div>
<div class="content" style="margin-top: -184px;">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <ul class="nav nav-tabs" role="tablist" style="font-size: 10pt; font-weight: bold;">
                <li class="nav-item">
                  <a class="nav-link active" id="current-production-order-tab" data-toggle="tab" href="#current-tab" role="tab">Current Job Ticket</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="production-queue-tab" data-toggle="tab" href="#production-queue" role="tab">Painting Schedule</a>
                </li>
              </ul>
              <div class="tab-content" style="min-height: 480px;">
                <div class="tab-pane active" id="current-tab" role="tabpanel">
                  <div id="row-tbl"></div>
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


@include('painting_operator.modal_view_schedule')
@include('painting_operator.modal_view_production_order_details')
@include('painting_operator.modal_end_task')
@include('painting_operator.modal_enter_reject')
@include('modals.machine_breakdown_form')
@include('painting_operator.modal_restart_task')

<style type="text/css">
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

    get_task();
    function get_task(){
      var production_order = '{{ $production_order }}';
      var process_id = {{ $process_details->process_id }};
      $.ajax({
        url:"/get_task/" + production_order + "/" + process_id + '/{{ Auth::user()->user_id }}',
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

    $(document).on('click', '.start-task-btn', function(e){
      e.preventDefault();

      var btn = $(this);

      btn.attr('disabled', true);

      var data = {  
        operator_id: '{{ Auth::user()->user_id }}', 
        production_order: '{{ $production_order }}', 
        process_id: {{ $process_details->process_id }}, 
        machine_code: '{{ $machine_details->machine_code }}', 
        _token: '{{ csrf_token() }}', 
        job_ticket_id: $(this).data('jobticket-id') 
      }

      $.ajax({
        url: "/start_painting",
        type:"POST",
        data: data,
        success:function(response){
          btn.removeAttr('disabled');
          if (response.success > 0) {
            get_task();
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

    $(document).on('click', '.end-task-btn', function(e){
      e.preventDefault();
      var max_qty = $(this).data('balance-qty');
      var production_order = '{{ $production_order }}';
      $('#end-task-modal .timelog-id').val($(this).data('timelog-id'));
      $('#end-task-modal .process-name').text('{{ $process_details->process_name }}');
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
            window.location.href="/painting/logout/{{ $process_details->process_name }}";
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
      window.location.href = "/painting/logout/{{ $process_details->process_name }}";
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

    get_scheduled_for_painting();
    setInterval(get_scheduled_for_painting, 5000);
    function get_scheduled_for_painting(){
      $.ajax({
        url:"/get_scheduled_for_painting",
        type:"GET",
        success:function(data){
          $('#assigned-tasks-table').html(data);
        }
      });  
    }

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

    $(document).on('click', '.view-prod-details-btn', function(e){
      e.preventDefault();
      $('#jt-workstations-modal .modal-title').text($(this).text() + " [Painting]");
      getJtDetails($(this).text());
    });

    $(document).on('click', '#enter-reject-btn', function(e){
      e.preventDefault();
      console.log($(this).data('timelog-id'));
      var max_qty = $(this).data('good');
      var production_order = '{{ $production_order }}';
      
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
        $('#enter-reject-modal .max-qty').text($(this).data('good-qty'));
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
@endsection