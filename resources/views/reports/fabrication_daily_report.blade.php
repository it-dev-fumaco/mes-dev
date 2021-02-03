@extends('layouts.user_app', [
  'namePage' => 'Fabrication',
  'activePage' => 'production_schedule',
])

@section('content')
<div class="panel-header" style="margin-top: -70px;">
    <div class="header text-center">
       <div class="row">
          <div class="col-md-8 text-white">
             <table style="text-align: center; width: 100%;">
                <tr>
                   <td style="width: 30%; border-right: 5px solid white;">
                      <div class="pull-right title mr-3">
                         <span class="d-block m-0 p-0" style="font-size: 14pt;">{{ date('M-d-Y') }}</span>
                         <span class="d-block m-0 p-0" style="font-size: 10pt;">{{ date('l') }}</span>
                      </div>
                   </td>
                   <td style="width: 20%; border-right: 5px solid white;">
                      <h3 class="title" style="margin: auto;"><span id="current-time">--:--:-- --</span></h3>
                   </td>
                   <td style="width: 50%">
                      <h3 class="title text-left p-0 ml-3" style="margin: auto 20pt;">Fabrication Report</h3>
                   </td>
                </tr>
             </table>
          </div>
       </div>
    </div>
 </div>
 <br>
 <div class="content" style="margin-top: -145px;">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                   <div class="row ">
                      <div class="col-md-12">
                         <div class="row text-black" style=" padding-top:50px auto;">
                           <div class="col-md-8">
                              <div class="form-group">
                                  <h5><b>Fabrication Daily Report</b></h5>
                              </div>
                           </div>
                           <div class="col-md-4 text-center">
                              <div class="form-group">
                                    <label for="daterange_report" style="font-size: 12pt; color: black; display: inline-block; margin-right: 1%;"><b>Date Range:</b></label>
                                    <input type="text" class="date form-control form-control-lg " name="daterange_report" autocomplete="off" placeholder="Select Date From" id="daterange_report" value="" style="display: inline-block; width: 40%; font-weight: bolder;">
                              </div>
                           </div>
                         </div>
                      </div>
                   </div>
                  
                    <div class="card">
                        <div class="card-body">
                           <div class="col-md-12">
                              <canvas id="fabrication_daily_report_chart" height="50"></canvas>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-12" style="">
                           <div id="tbl_log_report" style="width: 100%;overflow: auto;"></div>
                        </div>
                     </div>
                   
                </div>
            </div>
        </div>
    </div>
 </div>
<div id="active-tab"></div>

@endsection

@section('script')
<script type="text/javascript" src="{{ asset('js/daterange/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/daterange/daterangepicker.css') }}" />
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />
<script>
$(document).ready(function(){
    $('#daterange_report').daterangepicker({
    "showDropdowns": true,
    ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    "linkedCalendars": false,
    "autoUpdateInput": false,
    "alwaysShowCalendars": true,
  }, function(start, end, label) {
    console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    tbl_log_report();
    rfdTimeliness();
    
  });

   $('#daterange_report').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
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
    function tbl_log_report(){
      var date = $('#daterange_report').val();
      var startDate = $('#daterange_report').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var endDate = $('#daterange_report').data('daterangepicker').endDate.format('YYYY-MM-DD');
      var data = {
            start_date: startDate,
            end_date:endDate
          }
      $.ajax({
              url:"/fabrication_daily_report",
              type:"GET",
              data: data,
              success:function(data){
                $('#tbl_log_report').html(data);
              }
            });
      };
      function rfdTimeliness(){
         var date = $('#daterange_report').val();
         var startDate = $('#daterange_report').data('daterangepicker').startDate.format('YYYY-MM-DD');
         var endDate = $('#daterange_report').data('daterangepicker').endDate.format('YYYY-MM-DD');
         var data = {
            start_date: startDate,
            end_date:endDate
          }
      $.ajax({
         url: "/fabrication_daily_chart",
         method: "GET",
         data: data,
         success: function(data) {
            var days = [];
            var target = [100, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100];
            var percentage = [];
            var planned =[];
            var produced =[];

            for(var i in data.per_day) {
               days.push(data.per_day[i].date);
            }
            for(var i in data.planned) {
               planned.push(data.planned[i].value);
            }
            for(var i in data.produced) {
               produced.push(data.produced[i].value);
            }
            console.log(days);
            console.log(planned);

            var chartdata = {
               labels: days,
               datasets : [{
                     data: produced,
                     backgroundColor: '#3cba9f',
                     borderColor: "#3cba9f",
                     label: "TOTAL OUTPUT",
                     fill: false
                  },
                  {
                     data: planned,
                     backgroundColor: '#3e95cd',
                     borderColor: "#3e95cd",
                     label: "PLANNED QUANTITY",
                     fill: false
                  }]
            };

            var ctx = $("#fabrication_daily_report_chart");

            if (window.rfdTimelinessCtx != undefined) {
               window.rfdTimelinessCtx.destroy();
            }

            window.rfdTimelinessCtx = new Chart(ctx, {
               type: 'line',
               data: chartdata,
               options: {
                  responsive: true,
                  legend: {
                     position: 'top',
                     labels:{
                        boxWidth: 11
                     }
                  },
                  elements: {
                     line: {
                         tension: 0 // disables bezier curves
                     }
                  }
               }
            });
         },
         error: function(data) {
            alert('Error fetching data!');
         }
      });
   }

    </script>
@endsection