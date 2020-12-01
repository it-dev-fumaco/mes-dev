@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'shift',
])

@section('content')
@include('modals.add_shift_modal')
@include('modals.edit_shift_list_modal')
@include('modals.delete_shift_list_modal')
@include('modals.add_shift_schedule_modal')
@include('modals.edit_shift_schedule_modal')
@include('modals.delete_shift_schedule_modal')
<div class="panel-header">
   <div class="header text-center" style="margin-top: -60px;">
      <div class="row">
         <div class="col-md-12">
            <table style="text-align: center; width: 100%;">
               <tr>
                  <td style="width: 36%; border-right: 5px solid white;">
                     <h2 class="title">
                        <div class="pull-right" style="margin-right: 20px;">
                           <span style="display: block; font-size: 15pt;">{{ date('M-d-Y') }}</span>
                           <span style="display: block; font-size: 10pt;">{{ date('l') }}</span>
                        </div>
                     </h2>
                  </td>
                  <td style="width: 14%; border-right: 5px solid white;">
                     <h5 class="title" style="margin: auto;"><span id="current-time">--:--:-- --</span></h5>
                  </td>
                  <td style="width: 50%">
                     <h5 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Manage Shift</h5>
                  </td>
               </tr>
            </table>
         </div>
      </div>
   </div>
</div>
<div class="content">
<div class="card" style="width: 100%;margin-top: -150px;">
  

  <div class="card-body">
                      <div class="row">
                        <div class="col-md-9">
                          <div class="row">
                              <div class="col-md-6">
                                <div class="card" style="height: 1000px;">
                                  <table class="table" border="0">
                                    <col style="width: 70%;">
                                    <col style="width: 30%;">
                                    <thead class="text-white" style="background-color:#27507d;border: 0;">
                                      <th style="text-align: left;padding-left: 30px;"><b>Shift</b></th>
                                      <th style="font-size: 9pt;"><button type="button" class="btn btn-primary" id="add-shift-button" style="float: right;"><i class="now-ui-icons ui-1_simple-add"></i> Add</button></th>
                                    </thead>
                                  </table>
                                <div class="card-body">
                                  <div class="tbl_shift" id="tbl_shift"></div>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="card" style="height: 1000px;">
                                <table class="table" border="0">
                                  <col style="width: 60%;">
                                  <col style="width: 40%;">
                                  <thead class="text-white" style="background-color:#27507d;border: 0;">
                                    <th style="padding-left: 30px;"><b>Shift Schedule</b></th>
                                    <th style="font-size: 9pt;"><button type="button" class="btn btn-primary" id="add-shift-schedule-button" style="float: right;"><i class="now-ui-icons ui-1_simple-add"></i> Add</button></th>
                                  </thead>
                                </table>
                                <div class="card-body">
                                  <div class="tbl_shift_schedule_sched"></div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="card" style="height: 450px;">
                            <table class="table" border="0">
                              <col style="width: 15%;">
                                <col style="width: 75%;">
                                <col style="width: 10%;">
                                <thead class="text-white" style="background-color:#27507d;border: 0;">
                                  <th class="text-center" colspan="3"><b>Upcoming Holiday/Events</b></th>
                                </thead>
                            </table>
                            <div class="card-body" style="font-size: 13pt;overflow-y: auto;height: 360px;">            
                                @foreach($calendar as $holiday)
                                   
                                <p><b>{{ $holiday->description }}</b> — {{ \Carbon\Carbon::parse($holiday->holiday_date)->format('F d')}} </p>
                                @endforeach
                              </div>
                          </div>
                          <div class="card" style="height: 450px;">
                            <table class="table" border="0">
                              <col style="width: 15%;">
                              <col style="width: 75%;">
                              <col style="width: 10%;">
                              <thead class="text-white" style="background-color:#27507d;border: 0;">
                                <th class="text-center" colspan="3"><b>Upcoming On Leave / Absent Today</b></th>
                              </thead>
                            </table>
                          <div class="card-body" style="overflow-y: auto;height: 360px;">
                            <table class="table">
                                <tbody class="table-body">
                                  @forelse($out_today as $out_of_office)
                                  @php 
                                    $img = ($out_of_office->image) ? 'https://10.0.0.5/' . $out_of_office->image : asset('img/user.png');
                                  @endphp
                                  <tr>
                                    <td style="width: 60%;">
                                      <img src="{{ $img }}" width="65" height="45" style="float: left; padding-right: 10px;">
                                      <span class="approver-name">{{ $out_of_office->employee_name }}</span><br>
                                      <cite>{{ $out_of_office->designation }} - {{ $out_of_office->department }}</cite>
                                    </td>
                                    <td class="text-center" style="width: 40%;">{{ $out_of_office->leave_type }}<br>({{ $out_of_office->time_from }} - {{ $out_of_office->time_to }})<br>
                                      {{ \Carbon\Carbon::parse($out_of_office->date_from)->format('F d Y') }} - {{ \Carbon\Carbon::parse($out_of_office->date_to)->format('F d Y') }}</td>

                                  </tr>
                                  @empty
                                  <tr>
                                    <td class="text-center" colspan="2">No Employee(s) Found.</td>
                                  </tr>
                                  @endforelse
                                </tbody>
                              </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
  
</div>
</div>
                     
<style type="text/css">
  .scrolling table {
    table-layout: fixed;
    width: 100%;
}
.scrolling .td, .th {
  padding: 10px;
  width: auto;
}
.parent-td{
  padding: 10px;
  width: 4px;
  float: left;
}
.scrolling .th {
  position: relative;
  left: 0;
  width: auto;
}
.outer {
  position: relative
}
.inner {
  overflow-x: auto;

}
.nav-item .active{
  background-color: #f96332;
  font-weight: bold;
  color:#ffffff;
}



  .user-image {
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 5px;
}

.imgPreview {
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 5px;
}

.upload-btn{
   padding: 6px 12px;
}

.fileUpload {
   position: relative;
   overflow: hidden;
   font-size: 9pt;
}

.fileUpload input.upload {
   position: absolute;
   top: 0;
   right: 0;
   margin: 0;
   padding: 0;
   cursor: pointer;
   opacity: 0;
   filter: alpha(opacity=0);
}
.imgPreview1 {
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 5px;
}

.upload-btn1{
   padding: 6px 12px;
}

.fileUpload1 {
   position: relative;
   overflow: hidden;
   font-size: 9pt;
}

.fileUpload1 input.upload1 {
   position: absolute;
   top: 0;
   right: 0;
   margin: 0;
   padding: 0;
   cursor: pointer;
   opacity: 0;
   filter: alpha(opacity=0);
}

.boldwrap {
  font-weight: bold;
}



</style>

@endsection
@section('script')
<script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />
<script type="text/javascript" src="{{ asset('css/datepicker/jquery.timepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/jquery.timepicker.css') }}" />
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
  $('.time').timepicker({
        'showDuration': true,
        'timeFormat': 'g:i a'
    });
  $('.date').datepicker({
        'format': 'yyyy-mm-dd',
        'autoclose': true
    });

</script>
<script>
  $(document).ready(function(){
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 
    
    $(document).on('click', '#add-shift-button', function(){
      $('#add-shift-modal').modal('show');
    });
    

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
  });
</script>
<script type="text/javascript">
$(document).ready(function(){
  tbl_shift_list();
  tbl_shift_schedule_sched();
  get_shift_details();
  $('.schedule-date').datepicker({
    'format': 'yyyy-mm-dd',
    'autoclose': true
  });


});
</script>
<script type="text/javascript">
  function tbl_shift_list(page){
        $.ajax({
          url:"/get_tbl_shift_list/?page="+page,
          type:"GET",
          success:function(data){
            $('#tbl_shift').html(data);
          }
        }); 
  }
</script>
<script type="text/javascript">

    $('#add-shift-frm').submit(function(e){
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
            $('#add-shift-modal').modal('hide');
            tbl_shift_list();
          }
        }
      });
    });
</script>
<script type="text/javascript">

    $('#edit-shift-frm').submit(function(e){
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
            $('#edit-shift-modal').modal('hide');
            tbl_shift_list();
            tbl_shift_schedule_sched();
          }
        }
      });
    });
</script>
<script type="text/javascript">
    $('#delete-shift-frm').submit(function(e){
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
            $('#delete-shift-modal').modal('hide');
            tbl_shift_list();
          }
        }
      });
    });
</script>
<script type="text/javascript">
    $(document).on('click', '.edit-shift-list', function(){
      var shift_id = $(this).attr('data-shiftid');
      var time_in = $(this).attr('data-timein');
      var time_out = $(this).attr('data-timeout');
      var shift_type = $(this).attr('data-shifttype');
      var remarks = $(this).attr('data-remarks');
      var break_inmin = $(this).attr('data-breakinmin');
      var qty_capacity = $(this).attr('data-qtycapacity');
      var operation_id = $(this).attr('data-operation');

      $('#edit-shift-frm .operation').val(operation_id).prop('selected', true);
      $('#edit-shift-frm .time_in').val(time_in);
      $('#edit-shift-frm .time_out').val(time_out);
      $('#edit-shift-frm .shift_type').val(shift_type);
      $('#edit-shift-frm .remarks').val(remarks);
      $('#edit-shift-frm .breaktime_in_min').val(break_inmin);
      $('#edit-shift-frm .qty_capacity').val(qty_capacity);
      $('#edit-shift-frm .shift_id').val(shift_id);

    $('#edit-shift-modal').modal('show');
    });
</script>
<script type="text/javascript">
    $(document).on('click', '.delete-shift-list', function(){
      var shift_id = $(this).attr('data-shiftid');
      $('#delete-shift-frm .delete_shift_id').val(shift_id);

    $('#delete-shift-modal').modal('show');
    });
</script>

<script type="text/javascript">
      $('.modal').on('hidden.bs.modal', function(){
      var frm = $(this).find('form')[0];
      if (frm) frm.reset();
    });
</script>
<script type="text/javascript">
    $(document).on('click', '.edit-operation-list', function(){
      var operation_id = $(this).attr('data-operationid');
      var operation_name = $(this).attr('data-operationname');
      var operation_desc = $(this).attr('data-operationdesc');

      $('#edit-operation-frm .operation_id').val(operation_id);
      $('#edit-operation-frm .old_operation').val(operation_name);
      $('#edit-operation-frm .operation_name').val(operation_name);
      $('#edit-operation-frm .operation_desc').val(operation_desc);

    $('#edit-operation-modal').modal('show');
    });
</script>
<script type="text/javascript">
    $(document).on('click', '#add-shift-schedule-button', function(){
    $.ajax({
          url:"/get_shift_list_option",
          type:"GET",
          success:function(data){
            $('#add-shift-schedule-frm .shift_id').html(data);
            $('#add-shift-schedule-modal').modal('show');

          }
        });
    });
</script>
<script type="text/javascript">
  function tbl_shift_schedule_sched(page){
        $.ajax({
          url:"/get_tbl_shiftsched_list/?page="+page,
          type:"GET",
          success:function(data){
            $('.tbl_shift_schedule_sched').html(data);
          }
        }); 
  }
</script>
<script type="text/javascript">
    $('#add-shift-schedule-frm').submit(function(e){
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
            $('#add-shift-schedule-modal').modal('hide');
            tbl_shift_schedule_sched();
          }
        }
      });
    });
</script>
<script type="text/javascript">
    $('#edit-shift-schedule-frm').submit(function(e){
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
            $('#edit-shift-schedule-modal').modal('hide');
            tbl_shift_schedule_sched();
          }
        }
      });
    });
</script>
<script type="text/javascript">
    $(document).on('click', '.edit-shift-sched-list', function(){
      var shift_id = $(this).attr('data-shiftid');
      var shift_sched_id = $(this).attr('data-shiftschedid');
      var date = $(this).attr('data-date');
      var remarks = $(this).attr('data-remarks');

      $('#edit-shift-schedule-frm .shift_id').val(shift_id).prop('selected', true);
      $('#edit-shift-schedule-frm .shift_sched_id').val(shift_sched_id);
      $('#edit-shift-schedule-frm .sched_date').val(date);
      $('#edit-shift-schedule-frm .remarks').val(remarks);

    $('#edit-shift-schedule-modal').modal('show');
    });
</script>
<script type="text/javascript">
    $(document).on('click', '.delete-shift-sched-list', function(){
      var shift_sched_id = $(this).attr('data-shiftschedid');
      $('#delete-shift-sched-frm .delete_shift_sched_id').val(shift_sched_id);

    $('#delete-shift-sched-modal').modal('show');
    });
</script>
<script type="text/javascript">
    $('#delete-shift-sched-frm').submit(function(e){
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
            $('#delete-shift-sched-modal').modal('hide');
            tbl_shift_schedule_sched();
          }
        }
      });
    });
</script>
<script type="text/javascript">
  function get_shift_details(){
    var shift_sched_id = $('#add-shift-schedule-frm #shift_id').val();
    $.ajax({
          url:"/get_shift_details/"+ shift_sched_id,
          type:"GET",
          success:function(data){
            $('#add-shift-schedule-frm .time_in').text("Time-in:  " + data.time_in + "  " + " " + "  ");
            $('#add-shift-schedule-frm .time_out').text("         Time-out:  " + data.time_out);
          }
        }); 
  }
</script>
<script type="text/javascript">

   
    $(document).on('click', '#tbl_shift_list_pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    tbl_shift_list(page);

  });

    $(document).on('click', '#tbl_shift_sched_list_pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    tbl_shift_schedule_sched(page);

  });

    
</script>
@endsection