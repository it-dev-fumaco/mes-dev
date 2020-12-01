@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'production_schedule_report',
])

@section('content')
<div class="panel-header">
   <div class="header text-center">
      <div class="row">
         <div class="col-md-12">
            <table style="text-align: center; width: 100%;">
               <tr>
                  <td style="width: 36%; border-right: 5px solid white;">
                     <h2 class="title">
                        <div class="pull-right" style="margin-right: 20px;">
                           <span style="display: block; font-size: 20pt;">{{ date('M-d-Y') }}</span>
                           <span style="display: block; font-size: 12pt;">{{ date('l') }}</span>
                        </div>
                     </h2>
                  </td>
                  <td style="width: 14%; border-right: 5px solid white;">
                     <h2 class="title" style="margin: auto;"><span id="current-time">--:--:-- --</span></h2>
                  </td>
                  <td style="width: 50%">
                     <h2 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Production Schedule Report</h2>
                  </td>
               </tr>
            </table>
         </div>
      </div>
   </div>
</div>
<div class="content" style="margin-top: -80px;">
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-body">
               <div id="datepairExample">
                              <h6>Production Schedule Date:</h6>
                              <input type="text" class="date attendanceFilter" autocomplete="off" id="Filter_date" value="{{ Carbon\Carbon::parse('this week -7 days')->format('Y-m-d') }}" style="display: inline-block;" onchange="productioon_report()">
                             
               </div>
            <div class="col-md-12" id="report_table" style="padding-top: 30px;"></div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('css/datepicker/jquery.timepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/jquery.timepicker.css') }}" />
<script type="text/javascript" src="{{ asset('css/datepicker/datepair.js') }}"></script>
<script type="text/javascript" src="{{ asset('css/datepicker/jquery.datepair.js') }}"></script>
<script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
<script>
   $(document).ready(function(){
      setInterval(updateClock, 1000);
      productioon_report();


         // initialize input widgets first
    $('.time').timepicker({
        'timeFormat': 'g:i A'
    });

    $('#datepairExample .date').datepicker({
        'format': 'yyyy-mm-dd',
        'autoclose': true
    });

    // initialize datepair
    $('#datepairExample').datepair();

    $.ajaxSetup({
      headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
   });
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
   function productioon_report(){
      var date = $('#Filter_date').val();
      $.ajax({
      url: "/report/table_production_schedule_report",
      method: "GET",
      data:{date:date},
      success: function(response) {
         $('#report_table').html(response);
      },
      error: function(response) {
        alert(response);
      }
    });
   }
</script>
@endsection