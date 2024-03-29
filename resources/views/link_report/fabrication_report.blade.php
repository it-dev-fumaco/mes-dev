@extends('layouts.user_app', [
  'namePage' => 'Fabrication',
  'activePage' => 'operation_report',
  'pageHeader' => 'Fabrication Reports',
  'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header"></div>
 
   <div class="row p-0" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
     <div class="col-md-12 p-2 m-0">
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
            <div class="row m-0 p-0 bg-white">
               <div class="col-md-12">
                  <div class="d-flex flex-row align-items-center p-2">
                     <div class="col-3">
                        <h5 class="font-weight-bold p-0 m-0">Daily Fabrication Output</h5>
                     </div>
                     <div class="col-4">
                        <div class="form-group m-0">
                           <label for="parts_filter" style="font-size: 10pt; display: inline-block; margin-right: 1%;">Item Classification:</label>
                           <select class="form-control form-control-lg m-0" name="parts_filter" id="parts_filter" style="display: inline-block; width: 60%;">
                              <option value="All">Select Item Classification</option>
                              @foreach($item_classification as $rows)
                              <option value="{{$rows->item_classification}}">{{$rows->item_classification}}</option>
                              @endforeach
                           </select>
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="form-group m-0">
                           <label for="daterange_report" style="font-size: 10pt; display: inline-block; margin-right: 1%;">Date Range:</label>
                           <input type="text" class="date form-control form-control-lg m-0" name="daterange_report" autocomplete="off" placeholder="Select Date From and To" id="daterange_report" value="" style="display: inline-block; width: 60%;">
                        </div>
                     </div>
                     <div class="col-1">
                        <div style="float: right;" id="printthisasap"><img src="{{ asset('img/print.png') }}" width="35" class="printbtnprint" data-print=""></div>
                     </div>
                  </div>
                 
               </div>
               <div class="col-md-12" >
                  <canvas id="fabrication_daily_report_chart" height="50"></canvas>
               </div>
               <div class="col-md-12">
                  <ul class="nav nav-tabs nav-hide mt-4" id="mytab" role="tablist">
                     <li class="nav-item nav-hide">
                        <a class="nav-link active" id="tab01-tab" data-toggle="tab" href="#tabclass01" role="tab" aria-controls="tab01" aria-selected="true">Item Classification</a>
                     </li>
                     <li class="nav-item nav-hide">
                        <a class="nav-link" id="tab02-tab" data-toggle="tab" href="#tabcateg02" role="tab" aria-controls="tab02" aria-selected="false"> Parts Category</a>
                     </li>
                  </ul>
                  <!-- Tab panes -->
                  <div class="tab-content" id="printtbl">
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
        
         <div class="tab-pane {{ (request()->segment(2) == '2') ? 'active' : '' }}" id="tab1" role="tabpanel" aria-labelledby="tab1">
            <div class="card" style="background-color: #0277BD;">
               <div class="card-body" style="padding-bottom: 0;">
                  <div class="row">
                     <div id="datepairExample" class="col-md-12" style="font-size:9pt;">
                        <table class="w-100" style="text-align:center;margin-bottom:10px;margin-top:-8px;" id="table-selection">
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
                                 <h6 class="d-inline text-center text-white">From Date</h6>
                                 <input type="text" class="form-control date rounded attendanceFilter bg-white d-inline-block" autocomplete="off" placeholder="Select Date From" id="from_Filter_date" value="" style="text-align: center; width:85%; height:30px;">
                              </td>
                              <td>
                                 <h6 class="d-inline text-center text-white">To Date</h6>
                                 <input type="text" class="form-control date rounded attendanceFilter bg-white d-inline-block" autocomplete="off"  placeholder="Select Date To" id="to_Filter_date" value="" style="width: 85%;height:30px; text-align: center;" >
                              </td>
                              <td>
                              <h6 class="d-inline text-center text-white">Workstation</h6>
                                 <select class="form-control rounded d-inline-block bg-white" id="workstation_line" name="production_line" style="font-size: 9pt; width:85%;height:30px; text-align:center;" onchange="getprocess()">
                                    <option value="All">All</option>
                                    @foreach($workstation as $row)
                                    <option value="{{ $row->workstation_name }}" style="font-size: 9pt;">{{ $row->workstation_name }}</option>
                                    @endforeach
                                 </select>
                              </td>
                              <td>
                                 <h6 class="d-inline text-center text-white">Process</h6>
                                 <select class="form-control rounded process_line d-inline-block bg-white" id="process_line" name="production_line" style="font-size: 9pt; width: 85%; height: 30px; text-align:center;">
                                    <option value="All">All</option>
                                 </select>
                              </td>
                              <td>
                                 <h6 class="d-inline text-center text-white">Item Classification</h6>
                                 <select class="form-control rounded d-inline-block bg-white" id="parts_line" name="production_line" style="font-size: 9pt; width: 85%; height: 30px; text-align:center;">
                                    <option value="All">Select Item Classification</option>
                                    @foreach($parts as $row)
                                    <option value="{{ $row->parts_category }}" style="font-size: 9pt;">{{ $row->parts_category }}</option>
                                    @endforeach
                                 </select>
                              </td>
                              <td>
                                 <h6 class="d-inline text-center text-white">Item Code</h6>
                                 <select class="form-control rounded sel2 bg-white d-inline-block" id="itemcode_line" name="production_line" style="font-size: 9pt; width: 85%; height: 30px; text-align: center;">
                                    <option value="All">All</option>
                                    @foreach($sacode as $row)
                                    <option value="{{ $row->item_code }}" style="font-size: 9pt;">{{ $row->item_code }}</option>
                                    @endforeach
                                 </select>
                              </td>
                              <td class="text-center">
                                 <button type="button" class="btn btn-primary text-center" onclick="productioon_report()">Search</button>
                              </td>
                              <td>
                                 <img style="float:right;" src="{{ asset('img/download.png') }}" width="40" height="40" class="btn-export">
                              </td>
                           </tr>
                        </table>
                     </div>
                  </div>
                  <div class="row bg-white" style="height: auto; min-height: 600px;">
                     <div class="col-md-12">
                        <div class="table-responsive" id="report_table"></div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="tab-pane {{ (request()->segment(2) == '3') ? 'active' : '' }}" id="tab2" role="tabpanel" aria-labelledby="tab2">
            <div class="card">
               <div class="card-body" style="min-height: 450px;">
                  <div class="row">
                     <div class="col-md-4 offset-md-8">
                        <div class="form-group row m-0 text-right">
                           <label for="fltr-date-oploadutil" class="col-sm-4 col-form-label text-dark mt-1 mb-1" style="font-size: 11pt;">Date Range:</label>
                           <div class="col-sm-8 p-0">
                              <input type="text" class="date form-control form-control-lg" name="daterange_report" autocomplete="off" placeholder="Select Date From and To" id="fltr-date-oploadutil" style="font-weight: bolder; text-align: center;">
                           </div>
                        </div>
                     </div>
                  </div>
                  <div id="sked2"></div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
 <style type="text/css">
   .sked-tape__event {
      background-color: #5ba044 !important;
      border: 1px solid #5ba044 !important;
   }
   .sked-tape__event--low-gap {
      background-color: #EC6A5E !important;
      border-color: #e32c1b !important;
   }
</style>
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
<link rel="stylesheet" href="{{ asset('css/jquery.skedTape.css') }}">
<script src="{{ asset('js/jquery.skedTape.js') }}"></script>
<script>
$(document).ready(function(){
   $('#fltr-date-oploadutil').daterangepicker({
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
      operator_load_utilization(1);
   });

   operator_load_utilization(1);
   function operator_load_utilization(operation){
      var start = $('#fltr-date-oploadutil').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var end = $('#fltr-date-oploadutil').data('daterangepicker').endDate.format('YYYY-MM-DD');

      var data = {
         operation:operation,
         start: start,
         end: end
      }

      $("#sked2").empty();
      var operator_list = function () {
         var tmp = null;

         $.ajax({
            async: false,
            url:"/get_operators",
            data: data,
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

         $.ajax({
            async: false,
            url:"/get_operator_timelogs",
            data: data,
            type:"GET",
            success:function(data){
               var operator_logs = [];
               $.each(data, function (i, value) {
                  operator_logs.push({
                     name: value.production_order + ' - ' + value.workstation + ' - ' + value.completed_qty + ' pcs',
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

      var start_date = new Date(start);
      start_date.setDate(start_date.getDate());
      start_date.setHours(6,0,0,0);

      var end_date = new Date(end);
      end_date.setDate(end_date.getDate());
      end_date.setHours(6,0,0,0);
      var sked2Config = {
         caption: 'Operator Name',
         start: start_date,
         end: end_date,
         showEventTime: true,
         showEventDuration: true,
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

   $(document).on('click', '#olu_click', function(){
      operator_load_utilization(1);
   });
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
            console.log('Error fetching data!');
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
             console.log(data);
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
          console.log(response);
          }
        });
      }
   }
</script>
<script>

   $(document).on('change', '#parts_filter', function(event){
      tbl_log_report();
      tbl_chart(); 
   }); 
   $(function() {
  $('nav a[href^="/' + location.pathname.split("/")[1] + '"]').addClass('active');
});
$('#printthisasap').on("click", function () {
    var dataUrl = document.getElementById('fabrication_daily_report_chart').toDataURL(); //attempt to save base64 string to server using this var  
    var tbldata=   document.getElementById('printtbl').innerHTML;
    var div2 = document.createElement('div');
    var labelrange= $('#daterange_report').text();
    var date = $('#daterange_report').val();
     var windowContent = '<!DOCTYPE html>';
     windowContent += '<html>'
      windowContent += '<head><title>Print</title>';
         windowContent += '<style> *{ -webkit-print-color-adjust: exact !important; /*Chrome, Safari */color-adjust: exact !important;  /*Firefox*/} @page { size: landscape; }</style>';

      windowContent += '</head>';
     windowContent += '<body style="font-size:12px;"><div class="row"><div class="col-md-12"><h2 style="float:left;">Daily Fabrication Output Report</h2><h3 style="float:right;">'+ date +'</h3></div></div>'
     windowContent += '<img style="display: block; width: 100%; height: 100%;" src="' + dataUrl + '">';
     windowContent += '<div style="width: 100%; height: 100%;font-size:30pt;">'+ tbldata +'</div>';
     windowContent += '</body>';
     windowContent += '<style> #tbl_id_report{min-height:200px !important;font-size:12px;}</style>';

     windowContent += '</html>';
     var printWin = window.open('','','width=1100,height=800');
     printWin.document.open();
     printWin.document.write(windowContent);
     printWin.document.close();
     printWin.focus();
     printWin.print();
     printWin.close();
 });
 </script>
@endsection