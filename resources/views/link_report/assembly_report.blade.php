@extends('layouts.user_app', [
  'namePage' => 'Assembly',
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
            <a class="nav-link {{ (request()->segment(2) == '1') ? 'active' : '' }}" data-toggle="tab" href="#tab0" role="tab" aria-controls="tab0" aria-selected="true">Daily Assembly Output</a>
         </li>
         <li class="nav-item">
           <a class="nav-link {{ (request()->segment(2) == '2') ? 'active' : '' }}" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false" id="olu_click">Operator Load Utilization</a>
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
                                 <h5><b>Daily Assembly Output</b></h5>
                              </div>
                           </div>
                           <div class="col-md-3 text-center">
                              <div class="form-group row">
                                 <label for="parts_filter" style="font-size: 12pt; color: black; margin-right: 1%;display:inline-block; margin-top:5px;"><b>Item Classification:</b></label>
											<div class="col-sm-7">
                                    <select class="form-control form-control-lg text-center" style="display:inline;" name="parts_filter" id="parts_filter">
                                       <option value="All">Select Item Classification</option>
                                       @foreach($item_classification as $rows)
                                          <option value="{{$rows->item_classification}}">{{$rows->item_classification}}</option>

                                       @endforeach
                                    </select>
                                  </div>
                              </div>
                           </div>
                           <div class="col-md-4 pull-right">
                              <div class="row">
                                 <div class="col-md-10">
                                    <div class="form-group">
                                       <label for="daterange_report" style="font-size: 12pt; color: black; display: inline-block; margin-right: 1%;"><b>Date Range:</b></label>
                                       <input type="text" class="date form-control form-control-lg " name="daterange_report" autocomplete="off" placeholder="Select Date From and To" id="daterange_report" value="" style="display: inline-block; width: 60%; font-weight: bolder;">
                                    </div>
                                 </div>
                                 <div class="col-md-2">
                                    <div style="float: right;" id="printthisasap"><img src="{{ asset('img/print.png') }}" width="35" class="printbtnprint" data-print=""  ></div>
                                 </div>
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
                              <div class="tab-content"  id="printtbl">
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
         <div class="tab-pane {{ (request()->segment(2) == '2') ? 'active' : '' }}" id="tab2" role="tabpanel" aria-labelledby="tab2">
            <div class="row">
               <div class="col-md-12">
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
      tbl_log_report();
      tbl_chart();
   });

   tbl_log_report();
   tbl_chart();

   $('#daterange_report').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
   });

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
      operator_load_utilization(3);
   });

   operator_load_utilization(3);
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
      operator_load_utilization(3);
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
      var parts_category = $('#parts_filter').val();
      var startDate = $('#daterange_report').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var endDate = $('#daterange_report').data('daterangepicker').endDate.format('YYYY-MM-DD');
      var operation= 3;
      var data = {
            start_date: startDate,
            end_date:endDate,
            operation:operation,
            parts_category : parts_category
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
         var parts_category = $('#parts_filter').val();
         var startDate = $('#daterange_report').data('daterangepicker').startDate.format('YYYY-MM-DD');
         var endDate = $('#daterange_report').data('daterangepicker').endDate.format('YYYY-MM-DD');
         var operation = 3;
         var data = {
            start_date: startDate,
            end_date:endDate,
            operation: operation,
            parts_category : parts_category
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

            var ctx = $("#assembly_daily_report_chart");

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
      var parts_category = $('#parts_filter').val();
      var startDate = $('#daterange_report').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var endDate = $('#daterange_report').data('daterangepicker').endDate.format('YYYY-MM-DD');
      var operation= 3;
      var data = {
            start_date: startDate,
            end_date:endDate,
            operation:operation,
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

 
   $(document).on('change', '#parts_filter', function(event){
      tbl_log_report();
      tbl_chart(); 
   }); 
   $('#printthisasap').on("click", function () {
    var dataUrl = document.getElementById('assembly_daily_report_chart').toDataURL(); //attempt to save base64 string to server using this var  
    var tbldata=   document.getElementById('printtbl').innerHTML;
    var div2 = document.createElement('div');
    var labelrange= $('#daterange_report').text();
    var date = $('#daterange_report').val();
     var windowContent = '<!DOCTYPE html>';
     windowContent += '<html>'
      windowContent += '<head><title>Print</title>';
         windowContent += '<style> *{ -webkit-print-color-adjust: exact !important; /*Chrome, Safari */color-adjust: exact !important;  /*Firefox*/} @page { size: landscape; }</style>';

      windowContent += '</head>';
     windowContent += '<body style="font-size:12px;"><div class="row"><div class="col-md-12"><h2 style="float:left;">Daily Assembly Output Report</h2><h3 style="float:right;">'+ date +'</h3></div></div>'
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