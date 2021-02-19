@extends('layouts.user_app', [
  'namePage' => 'Fabrication',
  'activePage' => 'operation_report',
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
                      <h3 class="title text-left p-0 ml-3" style="margin: auto 20pt;">Fabrication Reports</h3>
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
            <a class="nav-link {{ (request()->segment(2) == '1') ? 'active' : '' }}" data-toggle="tab" href="#tab0" role="tab" aria-controls="tab0" aria-selected="true">Daily Fabrication Output</a>
         </li>
         <li class="nav-item">
           <a class="nav-link {{ (request()->segment(2) == '2') ? 'active' : '' }}" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="false">Operator Log Report</a>
         </li>
         <li class="nav-item">
           <a class="nav-link {{ (request()->segment(2) == '3') ? 'active' : '' }}" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false" id="olu_click">Operator Load Utilization</a>
         </li>
       </ul>
       <div class="tab-content" style="min-height: 500px;">
         <div class="tab-pane {{ (request()->segment(2) == '1') ? 'active' : '' }}" id="tab0" role="tabpanel" aria-labelledby="tab0">
            <div class="row">
               <div class="col-md-12">
                  <div class="card" style="border-radius: 0 0 3px 3px;">
                     <div class="card-body">
                        <div class="row m-0">
                           <div class="col-md-12">
                              <div class="row text-black" style=" padding-top:50px auto;">
                                 <div class="col-md-5">
                                    <div class="form-group">
                                       <h5><b>Daily Fabrication Output</b></h5>
                                    </div>
                                 </div>
                                 <div class="col-md-3 text-center align-middle">
                                    <div class="form-group row align-middle">
                                       <label for="parts_filter" class="align-middle" style="font-size: 12pt; color: black; margin-right: 1%;display:inline-block;margin-top:5px;"><b>Parts Category:</b></label>
											      <div class="col-sm-7 align-middle">
                                          <select class="form-control form-control-lg text-center" style="display:inline;" name="parts_filter" id="parts_filter">
                                             <option value="All">Select Parts Category</option>
                                                @foreach($item_classification as $rows)
                                                   <option value="{{$rows->item_classification}}">{{$rows->item_classification}}</option>
                                                @endforeach
                                          </select>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-md-4 pull-right">
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
                                       <canvas id="fabrication_daily_report_chart" height="50"></canvas>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-12">
                              <div class="row m-0">
                                 <div class="col-md-12" style="padding: 0;">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item">
                                       <a class="nav-link active" id="tab01-tab" data-toggle="tab" href="#tabclass01" role="tab" aria-controls="tab01" aria-selected="true">Item Classification</a>
                                    </li>
                                    <li class="nav-item">
                                       <a class="nav-link" id="tab02-tab" data-toggle="tab" href="#tabcateg02" role="tab" aria-controls="tab02" aria-selected="false"> Parts Category</a>
                                    </li>
                                    
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                       <div class="tab-pane active" id="tabclass01" role="tabpanel" aria-labelledby="tabclass01">
                                          <div class="row" style="margin-top: 12px;">
                                             <div class="col-md-12">
                                                <div id="tbl_log_report" style="width: 100%;overflow: auto;"></div>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="tab-pane" id="tabcateg02" role="tabpanel" aria-labelledby="tabcateg02">
                                          <div class="row" style="margin-top: 12px;">
                                             <div class="col-md-12">
                                                <div id="tbl_log_partscateg_report" style="width: 100%;overflow: auto;"></div>
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
               </div>
            </div>
         </div>
        
         <div class="tab-pane {{ (request()->segment(2) == '2') ? 'active' : '' }}" id="tab1" role="tabpanel" aria-labelledby="tab1">
            <div class="row">
               <div class="col-md-12">
                   <div class="card" style="background-color: #0277BD;">
                       <div class="card-body" style="padding-bottom: 0;">
                           <div class="row">
                           <div id="datepairExample" class="col-md-12" style="font-size:9pt;">
                           <table class="col-md-12" style="text-align:center;margin-bottom:10px;margin-top:-8px;" id="table-selection">
                           <col style="width: 13%;">
                            <col style="width: 13%;">
                            <col style="width: 13%;">
                            <col style="width: 13%;">
                            <col style="width: 12.5%;">
                            <col style="width: 12.5%;">
                            <col style="width: 12.5%;">
                            <col style="width: 12.5%;">
                            
                           <tr>
                           <td>
                            <h6 style="display:inline;color:white;" class="text-center;">From Date:</h6>
                               <input type="text" class="date attendanceFilter" autocomplete="off" placeholder="Select Date From" id="from_Filter_date" value="" style="text-align:center;display:inline-block;width:85%;height:30px;">
                            </td>
                            <td>
                               <h6 style="display:inline; padding:5px;color:white;">To Date:</h6>
                               <input type="text" class="date attendanceFilter" autocomplete="off"  placeholder="Select Date To" id="to_Filter_date" value="" style="display: inline-block;width:85%;height:30px;text-align:center;" >
                            </td>
                            <td>
                            <h6 style="display:inline; padding:5px;color:white;"> Workstation:</h6>
                                <select class="form-control" id="workstation_line" name="production_line"  style="background-color: white;font-size: 9pt; width:85%;height:30px;display:inline-block;text-align:center;" onchange="getprocess()">
                                  <option value="All">All</option>
                                     @foreach($workstation as $row)
                                      <option value="{{ $row->workstation_name }}" style="font-size: 9pt;">{{ $row->workstation_name }}</option>
                                     @endforeach
                               </select>
                            </td>
                            <td>
                               <h6 style="display:inline;padding:5px;color:white;margin-left:0px;">Process:</h6>
                               <select class="form-control process_line" id="process_line" name="production_line"  style="background-color: white;font-size: 9pt; width:85%;height:30px;display:inline-block;text-align:center;">
                                  <option value="All">All</option>
                               </select>
                            </td>
                            <td>
                               <h6 style="display:inline;padding:5px;color:white;margin-left:0px;">Item Classification:</h6>
                               <select class="form-control" id="parts_line" name="production_line"  style="background-color: white;font-size: 9pt; width:85%;height:30px;display:inline-block;text-align:center;">
                                  <option value="All">Select Item Classification</option>
                                     @foreach($parts as $row)
                                        <option value="{{ $row->parts_category }}" style="font-size: 9pt;">{{ $row->parts_category }}</option>
                                     @endforeach
                               </select>
                            </td>
                            <td>
                               <h6 style="display:inline;padding:5px;color:white;margin-left:0px; text-align:center;">Item Code:</h6>
                               <select class="form-control sel2" id="itemcode_line" name="production_line"  style="background-color: white;font-size: 9pt; width:85%;height:30px;display:inline-block;text-lign:left;">
                                  <option value="All">All</option>
                                     @foreach($sacode as $row)
                                        <option value="{{ $row->item_code }}" style="font-size: 9pt;">{{ $row->item_code }}</option>
                                     @endforeach
                               </select>
                            </td>
                            <td style="text-align:center;">
                            <button type="button" class="btn btn-primary text-center" onclick="productioon_report()">Search</button>
                            </td>
                            <td>
                            
                            <img style="float:right;" src="{{ asset('img/download.png') }}" width="40" height="40" class="btn-export">
                            </td>
                           </tr>
                           </table>
                            </div>
                           </div>
                           <div class="row" style="background-color: #ffffff;height: auto; min-height: 600px;">
                               <div class="col-md-12">
                               <!-- <div class="col-md-2 text-center" style="float:right;border;margin-right:-30px;">
                                  <button class="btn btn-default btn-export" style="display: inline;"><b>EXPORT</b></button><br>
                                  <span style="font-size:7pt;display:block; margin-top:-5px;"> Export Data to Excel </span>
                               </div> -->
       
                                   <div class="table-responsive" id="report_table">
                                       
                                   </div>
       
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
         </div>
         <div class="tab-pane {{ (request()->segment(2) == '3') ? 'active' : '' }}" id="tab2" role="tabpanel" aria-labelledby="tab2">
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


<script type="text/javascript" src="{{ asset('css/datepicker/jquery.timepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/jquery.timepicker.css') }}" />
<script type="text/javascript" src="{{ asset('css/datepicker/datepair.js') }}"></script>
<script type="text/javascript" src="{{ asset('css/datepicker/jquery.datepair.js') }}"></script>
<script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />

<link rel="stylesheet" href="{{ asset('css/jquery.skedTape.css') }}">
<script src="{{ asset('js/jquery.skedTape.js') }}"></script>
<script>
$(document).ready(function(){
   if ( $( "#olu_click" ).is( ".active" ) ) {
      $("#sked2").empty();
      var operator_list = function () {
       var tmp = null;
       var operation = 1;

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
       var operation = 1;
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
      }
   getprocess();
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
    tbl_chart();    
  });
   tbl_log_report();
   tbl_chart();
   $('#daterange_report').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
  });
  $('.time').timepicker({
        'timeFormat': 'g:i A'
    });

    $('#datepairExample .date').datepicker({
        'format': 'yyyy-mm-dd',
        'autoclose': true
    });

    // initialize datepair
    $('#datepairExample').datepair();

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
      var item_classification = $('#parts_filter').val();
      var startDate = $('#daterange_report').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var endDate = $('#daterange_report').data('daterangepicker').endDate.format('YYYY-MM-DD');
      var operation = 1;
      var data = {
            start_date: startDate,
            end_date:endDate,
            operation: operation,
            item_classification : item_classification

          }
      $.ajax({
              url:"/link_daily_output_report",
              type:"GET",
              data: data,
              success:function(data){
                $('#tbl_log_report').html(data);
                tbl_log_partcateg_report();
              }
            });
      };
      function tbl_chart(){
         var date = $('#daterange_report').val();
         var item_classification = $('#parts_filter').val();
         var startDate = $('#daterange_report').data('daterangepicker').startDate.format('YYYY-MM-DD');
         var endDate = $('#daterange_report').data('daterangepicker').endDate.format('YYYY-MM-DD');
         var operation = 1;
         var data = {
            start_date: startDate,
            end_date:endDate,
            operation:operation,
            item_classification : item_classification

          }
      $.ajax({
         url: "/link_daily_output_chart",
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

            var ctx = $("#fabrication_daily_report_chart");

            if (window.tbl_chartCtx != undefined) {
               window.tbl_chartCtx.destroy();
            }

            window.tbl_chartCtx = new Chart(ctx, {
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
   function tbl_log_partcateg_report(){
      var date = $('#daterange_report').val();
      var item_classification = $('#parts_filter').val();
      var startDate = $('#daterange_report').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var endDate = $('#daterange_report').data('daterangepicker').endDate.format('YYYY-MM-DD');
      var operation = 1;
      var data = {
            start_date: startDate,
            end_date:endDate,
            operation: operation
          }
      $.ajax({
              url:"/link_parts_category_daily_output",
              type:"GET",
              data: data,
              success:function(data){
                $('#tbl_log_partscateg_report').html(data);
              }
            });
      };

</script>
<script>
   function getprocess(){
      var workstation = $('#workstation_line').val();
      $.ajax({
             url: "/getprocess_query/"+ workstation,
             method: "GET",
             success: function(data) {
             $('#process_line').html(data);
               
             },
             error: function(data) {
             alert(data);
             }
           });
   
   }
   </script>
   <script type="text/javascript">
       $(document).on('click', '.btn-export', function(){
         var from_date = $('#from_Filter_date').val();
         var to_date = $('#to_Filter_date').val();
         var workstation = $('#workstation_line').val();
         var process = $('#process_line').val();
         var parts = $('#parts_line').val();
         var item_code = $('#itemcode_line').val();
         if(from_date == "" || to_date==""){
   
         }else{
   
            location.href="/export/view/"+ from_date +"/"+ to_date + "/" + workstation + "/" + process + "/" + parts + "/" + item_code;
         }
   
       });
</script>
<script type="text/javascript">
   function productioon_report(){
      var from_date = $('#from_Filter_date').val();
      var to_date = $('#to_Filter_date').val();
      var workstation = $('#workstation_line').val();
      var process = $('#process_line').val();
      var parts = $('#parts_line').val();
      var item_code = $('#itemcode_line').val();
      if(from_date == "" || to_date==""){

      }else{
        
          $.ajax({
          url: "/tbl_operator_item_produced_report/"+ from_date + '/'+ to_date + '/' + workstation + '/' + process + '/' + parts + '/' + item_code,
          method: "GET",
          success: function(response) {
          $('#report_table').html(response);

          },
          error: function(response) {
          alert(response);
          }
        });
      }
   }
</script>
<script>

   $(document).on('click', '#olu_click', function(){
      $("#sked2").empty();
      var operator_list = function () {
       var tmp = null;
       var operation = 1;

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
       var operation = 1;
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
   $(document).on('change', '#parts_filter', function(event){
      tbl_log_report();
      tbl_chart(); 
   }); 
   $(function() {
  $('nav a[href^="/' + location.pathname.split("/")[1] + '"]').addClass('active');
});
 </script>
@endsection