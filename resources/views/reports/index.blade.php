@extends('layouts.user_app', [
    'namePage' => 'MES',
    'activePage' => 'reports',
])
@section('content')
<div class="panel-header">
    <div class="header text-center"> 
       <div class="row">
         <div class="col-md-12 text-white" style="margin-top: -70px;">
            <table style="width: 100%;">
                <tr>
                <td style="width: 25%; border-right: 5px solid white;">
                <h5 class="title">
                    <div class="pull-right" style="margin-right: 20px;">
                    <span style="display: block; font-size: 18pt;">{{ date('M-d-Y') }}</span>
                    <span style="display: block; font-size: 11pt;">{{ date('l') }}</span>
                    </div>
                </h5>
                </td>
                <td style="width: 14%; border-right: 5px solid white;">
                <h5 class="title" style="margin: auto; font-size: 25pt;"><span id="current-time">--:--:-- --</span></h5>
                </td>
                <td style="width: 50%">
                <h4 class="title text-left" style="margin-left: 20px; margin: auto 20pt;  font-size: 25pt;">Reports</h4>
                </td>
            </tr>
            </table>
         </div>
       </div>
    </div>
</div>  
<div class="content" style="margin-top: -200px;">
	<div class="row m-0">
		<div class="col-md-12 m-0 p-0">
			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" data-toggle="tab" href="#tab-fabrication" role="tab" aria-controls="tab-fabrication" aria-selected="true">Fabrication</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#tab-painting" role="tab" aria-controls="tab-painting" aria-selected="true">Painting</a>
             </li>
         </ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab-fabrication" role="tabpanel" aria-labelledby="tab-fabrication">
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
				<div class="tab-pane" id="tab-painting" role="tabpanel" aria-labelledby="tab-painting">
					<div class="row">
						<div class="col-md-12">
						  <div class="card" style="background-color: whitesmoke">
							 <div class="card-body">
								<div class="row">
								  <div class="col-md-12">
									 <ul class="nav nav-tabs" id="myTab" role="tablist">
										<li class="nav-item">
										  <a class="nav-link active" id="chem-monitoring-tab" data-toggle="tab" href="#chem-monitoring" role="tab" aria-controls="chem-monitoring" aria-selected="true">Painting Chemical Records</a>
										</li>
										<li class="nav-item">
										  <a class="nav-link" id="water-discharge-tab" data-toggle="tab" href="#water-discharge" role="tab" aria-controls="water-discharge" aria-selected="false">Water Discharged Monitoring</a>
										</li>
									 </ul>
									 <!-- Tab panes -->
									 <div class="tab-content">
										<div class="tab-pane active" id="chem-monitoring" role="tabpanel" aria-labelledby="chem-monitoring">
										  <div class="row" style="margin-top: 12px;">
											 <div class="col-md-2">
												<div class="card" style="background-color: #0277BD;" >
												  <div class="card-body" style="padding-bottom: 0;">
													 <div class="row">
														<div class="col-md-8" style="margin-top: -10px;">
														  <h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Filter/s</h5>
														</div>
														<div class="col-md-4" style="margin-top: -20px;">
														  <button type="button" class="btn btn-default" id="clear-button" style="margin-bottom:5px;font-size: 12px;padding: 7px 9px 6px 10px;float: right;">Clear</button>
														</div>
													 </div>
													 <div class="row" style="background-color: #ffffff; padding-top: 9px;">
														<div class="col-md-12" style="margin: 0;height: 400px;" id="filter_chem_monitoring">
														  <span style="display: block; font-size: 9pt; margin-top: -8px;"></span>
															 <div class="form-group">
																<label style="color: black;">Date Range:</label>
																<input type="text" class="date attendanceFilter form-control" name="daterange" autocomplete="off" placeholder="Select Date From" id="daterange" value="" style="text-align:center;display:inline-block;width:100%;height:30px;">
															 </div>
															 
																<div class="form-group" style="margin-top: -14px;">
																	 <label style="color: black;">Free AKALI(6.5-7.5)</label>
																	 <select class="form-control text-center" name="free_akali" id="free_akali">
																		  <option value="All">Select Range</option>
																		  <option value="<">< 6.5</option>
																		  <option value="range">6.5 - 7.5</option>
																		  <option value=">">> 7.5</option>
																		</select>
																 </div>
				  
																<div class="form-group">
																	 <label style="color: black;">Replenishing(16-20)</label>
																	  <select class="form-control text-center" name="replenishing" id="replenishing">
																		  <option value="All">Select Range</option>
																		  <option value="<">< 16</option>
																		  <option value="range">16 - 20</option>
																		  <option value=">">> 20</option>
																		</select>
																 </div>
																<div class="form-group">
																	 <label style="color: black;">Accelerator(6.0-9.0)</label>
																	 <select class="form-control text-center" name="accelerator" id="accelerator">
																		  <option value="All">Select Range</option>
																		  <option value="<">< 6</option>
																		  <option value="range">6.0 - 9.0</option>
																		  <option value=">">> 9.0</option>
																		</select>
																 </div>
				  
																 <div class="form-group text-center">
																	 <button type="button" class="btn btn-primary" id="submit-button" onclick="filterbutton()" style="margin: 5px;">
																		Submit
																	 </button>
																 </div>
														  
														</div>
													 </div>
												  </div>
												</div>
											 </div>
											 <div class="col-md-10">
												<div class="card" style="background-color: #0277BD;" >
												  <div class="card-body" style="padding-bottom: 0;">
													 <div class="row">
														<div class="col-md-8">
														  
														  <h5 class="text-white font-weight-bold align-middle">Painting Chemical Record</h5>
														</div>
														<div class="col-md-4">
														  <img style="float:right;" src="{{ asset('img/export.png') }}" width="40" height="40" class="btn-export">
				  
														</div>
													 </div>
													 <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
														<div class="card card-nav-tabs card-plain">
														  <div class="card-body">
															 <div class="col-md-12">
																<div id="tbl_chemical" style="width: 100%;"class="table-responsive"></div>
															 </div>
														  </div>
														</div>
													 </div>
												  </div>
												</div>
											 </div>
										  </div>
										</div>
										<div class="tab-pane" id="water-discharge" role="tabpanel" aria-labelledby="water-discharge">
										  <div class="row" style="margin-top: 12px;">
											 <div class="col-md-2">
												<div class="card" style="background-color: #0277BD;" >
												  <div class="card-body" style="padding-bottom: 0;">
													 <div class="row">
														<div class="col-md-8" style="margin-top: -10px;">
														  <h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Filter/s</h5>
														</div>
														<div class="col-md-4" style="margin-top: -20px;">
														  <button type="button" class="btn btn-default" id="water-clear-button" style="margin-bottom:5px;font-size: 12px;padding: 7px 9px 6px 10px;float: right;">Clear</button>
														</div>
													 </div>
													 <div class="row" style="background-color: #ffffff; padding-top: 9px;">
														<div class="col-md-12" style="margin: 0;height: 400px;" id="filter_chem_monitoring">
														  <span style="display: block; font-size: 9pt; margin-top: -8px;"></span>
														  
																 <div class="form-group">
																<label style="color: black;">Date Range:</label>
																<input type="text" class="date form-control" name="daterange" autocomplete="off" placeholder="Select Date From" id="daterange_water" value="" style="text-align:center;display:inline-block;width:100%;height:30px;">
															 </div>
															 <div class="form-group" style="margin-top: -14px;">
																	 <label style="color: black;">Operating Hrs</label>
																	 <select class="form-control text-center" name="operating_hrs" id="operating_hrs">
																		  <option value="All">Select Range</option>
																		  <option value="<">< 8</option>
																		  <option value="range">8</option>
																		  <option value=">">> 8</option>
																		</select>
																 </div>
															 
															 
																 <div class="form-group text-center">
																	 <button type="button" class="btn btn-primary" id="submit-button" onclick="filterbutton_water()" style="margin: 5px;">
																		Submit
																	 </button>
																 </div>
														  
														</div>
													 </div>
												  </div>
												</div>
											 </div>
											 <div class="col-md-10">
												<div class="card" style="background-color: #0277BD;" >
												  <div class="card-body" style="padding-bottom: 0;">
													 <div class="row">
														<div class="col-md-8">
														  <h5 class="text-white font-weight-bold align-middle">Water Discharged Monitoring</h5>
														</div>
														<div class="col-md-4">
														  <img style="float:right;" src="{{ asset('img/export.png') }}" width="40" height="40" class="btn-export-water">
				  
														</div>
													 </div>
													 <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
														<div class="card card-nav-tabs card-plain">
														  <div class="card-body">
															 <div class="col-md-8 offset-md-2">
																<div id="tbl_water" style="width: 100%;"class="table-responsive"></div>
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

<script type="text/javascript" src="{{ asset('js/daterange/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/daterange/daterangepicker.css') }}" />

<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />

<script>
   $(document).ready(function(){
		getprocess();
		tbl_chem_records();
  water_discharge_tbl();
  $('#daterange').val('');

  $('input[name="daterange"]').daterangepicker({
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
 });
 $(document).on('click', '#clear-button', function(){
	$('#free_akali').prop('selectedIndex',0);
	$('#replenishing').prop('selectedIndex',0);
	$('#accelerator').prop('selectedIndex',0);
	$('#daterange').val("").daterangepicker("update");
 });
 $(document).on('click', '#water-clear-button', function(){
	$('#operating_hrs').prop('selectedIndex',0);
	$('#daterange_water').val("").daterangepicker("update");
 });

 $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
 $('#daterange').val('');

});


 // initialize input widgets first
 $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
	$(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
});

$('.sel2').select2({
 dropdownParent: $("#filter_chem_monitoring"),
 dropdownAutoWidth: false,
 width: '100%',
 cache: false
});

$('#daterange_water').daterangepicker({
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
 });

  $('#daterange_water').on('apply.daterangepicker', function(ev, picker) {
	  $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
 });
 

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

<script type="text/javascript">
	function tbl_chem_records(){
	  
		 $.ajax({
		 url: "/get_tbl_report_painting_chemical",
		 type:"GET",
		 success:function(data){
			  $('#tbl_chemical').html(data);
		 },
		 error: function(jqXHR, textStatus, errorThrown) {
			console.log(jqXHR);
			console.log(textStatus);
			console.log(errorThrown);
		 }
		 
	  });
	}
 </script>
 <script type="text/javascript">
	function filterbutton(){
		 var date = $('#daterange').val();
		 var free = $('#free_akali').val();
		 var replen = $('#replenishing').val();
		 var acce = $('#accelerator').val();
		 var startDate = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
		 var endDate = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
		 if(daterange == ""){
 
		 }else{
			
			  $.ajax({
			  url: "/get_tbl_report_painting_chemical_filter/"+ startDate +'/'+ endDate + '/'+ free + '/' + replen + '/' + acce,
			  method: "GET",
			  success: function(data) {
			  $('#tbl_chemical').html(data);
 
			  },
			  error: function(jqXHR, textStatus, errorThrown) {
			  console.log(jqXHR);
			  console.log(textStatus);
			  console.log(errorThrown);
			}
			});
		 }
	}
		 
 </script>
 <script type="text/javascript">
	  $(document).on('click', '.btn-export', function(){
		 var date = $('#daterange').val();
		 var free = $('#free_akali').val();
		 var replen = $('#replenishing').val();
		 var acce = $('#accelerator').val();
		 var startDate = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
		 var endDate = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
		 
		 if(date == ""){
					  showNotification("danger", 'Please Select Date Range', "now-ui-icons travel_info");
 
		 }else{
			  location.href= "/get_tbl_report_painting_chemical_export/"+ startDate +'/'+ endDate + '/'+ free + '/' + replen + '/' + acce;
		 }
 
 
	  });
 </script>
 <script type="text/javascript">
	function water_discharge_tbl(){
		 
			  $.ajax({
			  url: "/get_tbl_water_discharged/",
			  method: "GET",
			  success: function(data) {
			  $('#tbl_water').html(data);
 
			  },
			  error: function(jqXHR, textStatus, errorThrown) {
			  console.log(jqXHR);
			  console.log(textStatus);
			  console.log(errorThrown);
			}
			});
	}
		 
 </script>
 <script type="text/javascript">
	function filterbutton_water(){
		 var date = $('#daterange_water').val();
		 var operating_hrs = $('#operating_hrs').val();
		 var startDate = $('#daterange_water').data('daterangepicker').startDate.format('YYYY-MM-DD');
		 var endDate = $('#daterange_water').data('daterangepicker').endDate.format('YYYY-MM-DD');
		 if(date == ""){
 
		 }else{
			
			  $.ajax({
			  url: "/get_tbl_report_painting_water_discharge_filter/"+ startDate +'/'+ endDate +'/' + operating_hrs,
			  method: "GET",
			  success: function(data) {
			  $('#tbl_water').html(data);
 
			  },
			  error: function(jqXHR, textStatus, errorThrown) {
			  console.log(jqXHR);
			  console.log(textStatus);
			  console.log(errorThrown);
			}
			});
		 }
	}
		 
 </script>
 <script type="text/javascript">
	  $(document).on('click', '.btn-export-water', function(){
		 var date = $('#daterange_water').val();
		 var operating_hrs = $('#operating_hrs').val();
		 var startDate = $('#daterange_water').data('daterangepicker').startDate.format('YYYY-MM-DD');
		 var endDate = $('#daterange_water').data('daterangepicker').endDate.format('YYYY-MM-DD');
		 
		 if(date == ""){
			 showNotification("danger", 'Please Select Date Range', "now-ui-icons travel_info");
		 }else{
			  location.href= "/get_tbl_report_painting_water_discharge_export/"+ startDate +'/'+ endDate +'/' + operating_hrs;
		 }
 
 
	  });
 </script>

@endsection