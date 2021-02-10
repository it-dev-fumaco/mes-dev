@extends('link_report.app', [
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
                      <h3 class="title text-left p-0 ml-3" style="margin: auto 20pt;">Assembly Reports</h3>
                   </td>
                </tr>
             </table>
          </div>
       </div>
    </div>
 </div>
 <br>
 <div class="content">
   <div class="row" style="margin-top: -145px;">
     <div class="col-md-12">
       <ul class="nav nav-tabs" role="tablist" id="qa-dashboard-tabs">
         <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#tab0" role="tab" aria-controls="tab0" aria-selected="true">Daily Assembly Output</a>
         </li>
         <li class="nav-item">
           <a class="nav-link" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false" id="olu_click">Operator Load Utilization</a>
         </li>
       </ul>
       <div class="tab-content" style="min-height: 500px;">
         <div class="tab-pane active" id="tab0" role="tabpanel" aria-labelledby="tab0">
           <div class="row">
             <div class="col-md-12">
               <div class="card" style="border-radius: 0 0 3px 3px;">
                 <div class="card-body">
                   <div class="row m-0">
                     <div class="col-md-12">
                        <div class="row text-black" style=" padding-top:50px auto;">
                          <div class="col-md-8">
                             <div class="form-group">
                                 <h5><b>Daily Assembly Output</b></h5>
                             </div>
                          </div>
                          <div class="col-md-4 text-center">
                             <div class="form-group">
                                   <label for="daterange_report" style="font-size: 12pt; color: black; display: inline-block; margin-right: 1%;"><b>Date Range:</b></label>
                                   <input type="text" class="date form-control form-control-lg " name="daterange_report" autocomplete="off" placeholder="Select Date From and To" id="daterange_report" value="" style="display: inline-block; width: 60%; font-weight: bolder;">
                             </div>
                          </div>
                        </div>
                     </div>
                     <div class="col-md-12">
                       <div class="card">
                          <div class="card-body">
                             <div class="col-md-12">
                                <canvas id="assembly_daily_report_chart" height="50"></canvas>
                             </div>
                          </div>
                       </div>
                     </div>
                     <div class="col-md-12">
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
           </div>
         </div>
         <div class="tab-pane" id="tab2" role="tabpanel" aria-labelledby="tab2">
            <div class="row">
               <div class="col-md-12">
                  <div class="card">
                      <div class="card-body" style="min-height: 450px;">
                          <div id="sked2"></div>
                      </div>
                  </div>
              </div>
           </div>
         </div>
       </div>
     </div>
   </div>
 </div>
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('js/daterange/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/daterange/daterangepicker.css') }}" />
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />

<link rel="stylesheet" href="{{ asset('css/jquery.skedTape.css') }}">
<script src="{{ asset('js/jquery.skedTape.js') }}"></script>
<script>
$(document).ready(function(){
    $('#daterange_report').daterangepicker({
    "showDropdowns": true,
    "startDate": moment().startOf('month'),
    "endDate": moment().endOf('month'),
    "locale": {format: 'MMMM D, YYYY'},
    ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    "linkedCalendars": false,
    "autoUpdateInput": true,
    "alwaysShowCalendars": true,
  }, function(start, end, label) {
    console.log('New date range selected: ' + start.format('MMMM D, YYYY') + ' to ' + end.format('MMMM D, YYYY') + ' (predefined range: ' + label + ')');
    tbl_log_report();
    rfdTimeliness();
    
  });
  tbl_log_report();
    rfdTimeliness();
   $('#daterange_report').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
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
      var operation= 3;
      var data = {
            start_date: startDate,
            end_date:endDate,
            operation:operation
          }
      $.ajax({
              url:"/daily_output_report",
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
         var operation = 3;
         var data = {
            start_date: startDate,
            end_date:endDate,
            operation: operation
          }
      $.ajax({
         url: "/daily_output_chart",
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

            var ctx = $("#assembly_daily_report_chart");

            if (window.rfdTimelinessCtx != undefined) {
               window.rfdTimelinessCtx.destroy();
            }

            window.rfdTimelinessCtx = new Chart(ctx, {
               type: 'line',
               data: chartdata,
               options: {
                  tooltips: {
                     mode: 'index'
                  },
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
<script>

   $(document).on('click', '#olu_click', function(){
      $("#sked2").empty();
      var operator_list = function () {
       var tmp = null;
       var operation = 3;

       $.ajax({
           async: false,
           url:"/get_operators",
           data:{operation:operation},
           type:"GET",
           success:function(data){
               var operator_arr = [];
               $.each(data, function (i, value) {
                   operator_arr.push({id: i, name: value});
               }); 
 
               tmp = operator_arr;
           }
       }); 
 
       return tmp;
     }();
 
     var timelogs = function () {
       var tmp = null;
       var operation = 3;
       $.ajax({
           async: false,
           url:"/get_operator_timelogs",
           data:{operation:operation},
           type:"GET",
           success:function(data){
               var operator_logs = [];
               $.each(data, function (i, value) {
                   operator_logs.push({
                       name: value.workstation + ' - ' + value.completed_qty + ' pcs',
                       location: value.operator_id,
                       start: new Date(value.from_time),
                       end: new Date(value.to_time),
                   });
               }); 
 
               tmp = operator_logs;
           }
       }); 
 
       return tmp;
     }();
 
     console.log(timelogs);
 
     // -------------------------- Helpers ------------------------------
     function today(hours, minutes) {
         var date = new Date();
         date.setHours(hours, minutes, 0, 0);
         return date;
     }
     function yesterday(hours, minutes) {
         var date = today(hours, minutes);
         date.setTime(date.getTime() - 24 * 60 * 60 * 1000);
         return date;
     }
     function tomorrow(hours, minutes) {
         var date = today(hours, minutes);
         date.setTime(date.getTime() + 24 * 60 * 60 * 1000);
         return date;
     }
     var start_date = new Date();
     start_date.setDate(start_date.getDate() - 7);
     start_date.setHours(6,0,0,0);
     var sked2Config = {
         caption: 'Operator Name',
         start: start_date,
         end: tomorrow(0, 0),
         showEventTime: true,
         showEventDuration: true,
         //locations: locations.map(function(location) {
           // var newLocation = $.extend({}, location);
             //delete newLocation.tzOffset;
           // return newLocation;
         //}),
         formatters: {
             date: function (date) {
                 return $.fn.skedTape.format.date(date, 'm', '/');
             },
         },
         locations: operator_list.slice(),
         events: timelogs.slice(),
         tzOffset: 0,
         sorting: true,
         orderBy: 'name',
         showIntermission: true
     };
     var $sked2 = $.skedTape(sked2Config);
     $sked2.appendTo('#sked2').skedTape('render');
     //$sked2.skedTape('destroy');
     $sked2.skedTape(sked2Config);
   
   });
       

 </script>
@endsection