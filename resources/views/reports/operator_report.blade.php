@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'operators_item_report',
])
@section('content')
<div class="panel-header">
   <div class="header text-center" style="margin-top: -60px;">
      <div class="row">
         <div class="col-md-12">
            <table style="text-align: center; width: 100%;">
               <tr>
                  <td style="width: 36%; border-right: 5px solid white;">
                     <h2 class="title">
                        <div class="pull-right" style="margin-right: 20px;">
                           <span style="display: inline-block; font-size: 15pt;">{{ date('M-d-Y') }}</span>
                           <span style="display: inline-block; font-size: 9pt;">{{ date('l') }}</span>
                        </div>
                     </h2>
                  </td>
                  <td style="width: 14%; border-right: 5px solid white;">
                     <h5 class="title" style="margin: auto;"><span id="current-time">--:--:-- --</span></h5>
                  </td>
                  <td style="width: 50%">
                     <h5 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Operator List </h5>
                  </td>
               </tr>
            </table>
         </div>
      </div>
   </div>
</div>
<div class="content" style="margin-top: -140px;">
    <div class="row">
        <div class="col-md-12" style="padding-top:10px;">
            <div class="card" style="background-color: #0277BD;"  >
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
                        <input type="text" class="date attendanceFilter" autocomplete="off" placeholder="Select Date From" id="from_Filter_date" value="" style="text-align:center;display:inline-block;width:85%;height:30px;" >
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
                        <h6 style="display:inline;padding:5px;color:white;margin-left:0px;">Parts Category:</h6>
                        <select class="form-control" id="parts_line" name="production_line"  style="background-color: white;font-size: 9pt; width:85%;height:30px;display:inline-block;text-align:center;">
                           <option value="All">All</option>
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


@endsection

@section('script')
<script type="text/javascript" src="{{ asset('css/datepicker/jquery.timepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/jquery.timepicker.css') }}" />
<script type="text/javascript" src="{{ asset('css/datepicker/datepair.js') }}"></script>
<script type="text/javascript" src="{{ asset('css/datepicker/jquery.datepair.js') }}"></script>
<script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<script>
   $(document).ready(function(){
      getprocess();

      $('.sel2').select2({
      dropdownParent: $("#table-selection"),
      dropdownAutoWidth: false,
      width: '100%',
      cache: false
    });
      setInterval(updateClock, 1000);
      // productioon_report();


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

@endsection