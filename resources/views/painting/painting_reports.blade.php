@extends('layouts.user_app', [
  'namePage' => 'Painting',
  'activePage' => 'painting_production_orders',
])
@section('content')
@include('modals.item_track_modal')
<div class="panel-header">
  <div class="header text-center" style="margin-top: -60px;">
		<div class="row">
			<div class="col-md-12">
				<table style="text-align: center; width: 60%;">
					<tr>
						<td style="width: 35%; border-right: 5px solid white;">
							<h2 class="title">
								<div class="pull-right" style="margin-right: 20px;">
									<span style="display: block; font-size: 15pt;">{{ date('M-d-Y') }}</span>
									<span style="display: block; font-size: 10pt;">{{ date('l') }}</span>
								</div>
							</h2>
						</td>
						<td style="width: 15%; border-right: 5px solid white;">
							<h2 class="title" style="margin: auto; font-size: 17pt;"><span id="current-time">--:--:-- --</span></h2>
						</td>
						<td style="width: 50%">
							<h2 class="title text-left" style="margin-left: 20px; margin: auto 20pt; font-size: 19pt;">Reports</h2>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="content" style="margin-top: -170px;">
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





<style type="text/css">
  

   #chem-monitoring .form-control, #water-discharge .form-control{
    border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px;
}
  .breadcrumb-c {
    font-size: 10pt;
    font-weight: bold;
    padding: 0px;
    background: transparent;
    list-style: none;
    overflow: hidden;
    margin-top: 10px;
    margin-bottom: 10px;
    width: 100%;
    border-radius: 4px;
  }

  .breadcrumb-c>li {
    display: table-cell;
    vertical-align: top;
    width: 1%;
  }

  .breadcrumb-c>li+li:before {
    padding: 0;
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
</style>

<style type="text/css">
.modal-lg-custom {
    max-width: 80% !important;
}
#manual-production-modal .form-control {
  border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px;
}
#manual-production-modal .form-control:hover, #manual-production-modal .form-control:focus, #manual-production-modal .form-control:active {
  box-shadow: none;
}
#manual-production-modal .form-control:focus {
  border: 1px solid #34495e;
}

.select2.select2-container {
  width: 100% !important;
}

.select2.select2-container .select2-selection {
  border: 1px solid #ccc;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
  height: 34px;
  margin-bottom: 15px;
  outline: none;
  transition: all 0.15s ease-in-out;
}

.select2.select2-container .select2-selection .select2-selection__rendered {
  color: #333;
  line-height: 32px;
  padding-right: 33px;
}

.select2.select2-container .select2-selection .select2-selection__arrow {
  background: #f8f8f8;
  border-left: 1px solid #ccc;
  -webkit-border-radius: 0 3px 3px 0;
  -moz-border-radius: 0 3px 3px 0;
  border-radius: 0 3px 3px 0;
  height: 32px;
  width: 33px;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--single {
  background: #f8f8f8;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--single .select2-selection__arrow {
  -webkit-border-radius: 0 3px 0 0;
  -moz-border-radius: 0 3px 0 0;
  border-radius: 0 3px 0 0;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--multiple {
  border: 1px solid #34495e;
}

.select2.select2-container.select2-container--focus .select2-selection {
  border: 1px solid #34495e;
}

.select2.select2-container .select2-selection--multiple {
  height: auto;
  min-height: 34px;
}

.select2.select2-container .select2-selection--multiple .select2-search--inline .select2-search__field {
  margin-top: 0;
  height: 32px;
}

.select2.select2-container .select2-selection--multiple .select2-selection__rendered {
  display: block;
  padding: 0 4px;
  line-height: 29px;
}

.select2.select2-container .select2-selection--multiple .select2-selection__choice {
  background-color: #f8f8f8;
  border: 1px solid #ccc;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
  margin: 4px 4px 0 0;
  padding: 0 6px 0 22px;
  height: 24px;
  line-height: 24px;
  font-size: 12px;
  position: relative;
}

.select2.select2-container .select2-selection--multiple .select2-selection__choice .select2-selection__choice__remove {
  position: absolute;
  top: 0;
  left: 0;
  height: 22px;
  width: 22px;
  margin: 0;
  text-align: center;
  color: #e74c3c;
  font-weight: bold;
  font-size: 16px;
}

.select2-container .select2-dropdown {
  background: transparent;
  border: none;
  margin-top: -5px;
}

.select2-container .select2-dropdown .select2-search {
  padding: 0;
}

.select2-container .select2-dropdown .select2-search input {
  outline: none;
  border: 1px solid #34495e;
  border-bottom: none;
  padding: 4px 6px;
}

.select2-container .select2-dropdown .select2-results {
  padding: 0;
}

.select2-container .select2-dropdown .select2-results ul {
  background: #fff;
  border: 1px solid #34495e;
}

.select2-container .select2-dropdown .select2-results ul .select2-results__option--highlighted[aria-selected] {
  background-color: #3498db;
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

<script>

$(document).ready(function(){
  tbl_chem_records();
  water_discharge_tbl();
  $('#daterange').val('');
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
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

  $(document).on('click', '.prod-details-btn', function(e){
    e.preventDefault();
    var jtno = $(this).data('jtno');
    $('#jt-workstations-modal .modal-title').text(jtno);
    if(jtno){
      getJtDetails($(this).data('jtno'));
    }else{
      showNotification("danger", 'No Job Ticket found.', "now-ui-icons travel_info");
    }
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
  
  
  function getJtDetails(jtno){
      $('#process-bc').empty();
      $('#jt-details-tbl tbody').empty();
      $.ajax({
        url:"/get_jt_details/" + jtno,
        type:"GET",
        success:function(data){
          $('#jt-details-col .produced-qty').text(data.totals.produced_qty);
          $('#jt-details-col .total-good').text(data.totals.total_good);
          $('#jt-details-col .total-reject').text(data.totals.total_reject);
          $('#jt-details-col .balance-qty').text(data.totals.balance_qty);

          if (data.item_details.sales_order) {
            $('#jt-details-col .ref-type').text('SO No.');
            $('#jt-details-col .ref-no').text(data.item_details.sales_order);
          }
  
          if (data.item_details.material_request) {
            $('#jt-details-col .ref-type').text('MREQ No.');
            $('#jt-details-col .ref-no').text(data.item_details.material_request);
          }
  
          $('#jt-details-col .prod-no').text(data.item_details.production_order);
          $('#jt-details-col .cust').text(data.item_details.customer);
          $('#jt-details-col .proj').text(data.item_details.project);
          $('#jt-details-col .qty').text(data.item_details.qty_to_manufacture);
          $('#jt-details-col .del-date').text(data.item_details.delivery_date);
          $('#jt-details-col .item-code').text(data.item_details.item_code);
          $('#jt-details-col .desc').text(data.item_details.description);
          $('#jt-details-col .sched-date').text(data.item_details.planned_start_date);
          $('#jt-details-col .task-status').text(data.item_details.status);
          if (data.item_details.status == 'Late') {
            $('#jt-details-col .task-status').removeClass('badge-info').addClass('badge-danger');
          }else{
            $('#jt-details-col .task-status').removeClass('badge-danger').addClass('badge-info');
          } 
     
          var r = '';
          $.each(data.operations, function(i, v){
            r += '<tr>' +
              '<td class="text-center" style="border: 1px solid #ABB2B9;" rowspan="' + v.count + '"><b>' + v.workstation + '</b></td>';
            if (v.operations.length > 0) {
              $.each(v.operations, function(i, d){
                machine = (d.machine_code) ? d.machine_code : '-';
                operator_name = (d.operator_name) ? d.operator_name : '-';
                from_time = (d.from_time) ? d.from_time : '-';
                to_time = (d.to_time) ? d.to_time : '-';
                var inprogress_class = (d.status == 'In Progress') ? 'active-process' : '';
                var qc_status = (d.qa_inspection_status == 'QC Passed') ? "qc_passed" : "qc_failed";
                qc_status = (d.qa_inspection_status == 'Pending') ? '' : qc_status;
                r += '<td class="text-center '+inprogress_class+' '+qc_status+'" style="border: 1px solid #ABB2B9;"><b>' + v.process + '</b></td>' +
                    '<td class="text-center '+inprogress_class+'" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>' + Number(d.good) + '</b></td>' +
                    '<td class="text-center '+inprogress_class+'" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>' + Number(d.reject) + '</b></td>' +
                    '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + machine + '</td>' +
                    '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + from_time + '</td>' +
                    '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + to_time + '</td>' +
                    '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + operator_name + '</td>' +
                    '</tr>';
              });
            }else{
              r += '<td class="text-center" style="border: 1px solid #ABB2B9;"><b>' + v.process + '</b></td>' +
                    '<td class="text-center" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>0</b></td>' +
                    '<td class="text-center" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>0</b></td>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>' +
                    '</tr>';
            }
          });

          var p = '';
          $.each(data.process, function(i, d){
            p += '<li class="'+ d.status +'">'+
                '<a href="javascript:void(0);">' + d.workstation + '</a>' +
                '</li>';
            });
  
          $('#process-bc').append(p);
          $('#jt-details-tbl tbody').append(r);
          $('#jt-workstations-modal').modal('show');
        }
      });
    }

    $(document).on('click', '.create-feedback-btn', function(e){
      e.preventDefault();
      $('#submit-feedback-btn').removeAttr('disabled');
      var production_order = $(this).data('production-order');
      var completed_qty = $(this).data('completed-qty');
      var target_warehouse = $(this).data('target-warehouse');

      $('#confirm-feedback-production-modal input[name="production_order"]').val(production_order);
      $('#confirm-feedback-production-modal .completed-qty').text(completed_qty);
      $('#confirm-feedback-production-modal .target-warehouse').text(target_warehouse);
      $('#confirm-feedback-production-modal').modal('show');
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


  $(document).on('click', '.cancelled-production-pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    get_cancelled_production(page);
  });

  $(document).on('keyup', '#search-cancelled-prod', function(){
    var query = $(this).val();
    get_cancelled_production(1, query);
  });
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
<script type="text/javascript">
  function showNotification(color, message, icon){
        $.notify({
          icon: icon,
          message: message
        },{
          type: color,
          timer: 500,
          placement: {
            from: 'top',
            align: 'center'
          }
        });
  }
</script>
@endsection