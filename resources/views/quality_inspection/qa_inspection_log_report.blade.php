@extends('layouts.painting_app', [
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
                  <a class="nav-link active" id="fabrication-tab" data-toggle="tab" href="#fabrication" role="tab" aria-controls="fabrication" aria-selected="true">Fabrication</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="painting-tab" data-toggle="tab" href="#painting" role="tab" aria-controls="painting" aria-selected="false"> Painting</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="assembly-tab" data-toggle="tab" href="#assembly" role="tab" aria-controls="assembly" aria-selected="false">Assembly</a>
                </li>
                
              </ul>
              <!-- Tab panes -->
              <div class="tab-content">
                <div class="tab-pane active" id="fabrication" role="tabpanel" aria-labelledby="fabrication">
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
                            <div class="col-md-12" style="margin: 0;height: 780px;" id="filter_qa_inspection_fabrication">
                              <span style="display: block; font-size: 9pt; margin-top: -8px;"></span>
                                <div class="form-group">
                                  <label style="color: black;">Date Range:</label>
                                  <input type="text" class="date form-control" name="daterange" autocomplete="off" placeholder="Select Date From" id="daterange" value="" style="text-align:center;display:inline-block;width:100%;height:30px;" onchange="tbl_log_fabrication()">
                                </div>
                                <div class="form-group">
                                      <label style="color: black;">Workstation</label>
                                      <select class="form-control text-center sel6" name="workstation" id="workstation" onchange="tbl_log_fabrication()">
                                          <option value="none"> Select Workstation</option>
                                          @foreach($workstation as $row)
                                          <option value="{{$row->workstation_id}}">{{$row->workstation_name}}</option>
                                          @endforeach
                                        </select>
                                   </div>
                                  <div class="form-group" style="margin-top: -14px;">
                                      <label style="color: black;">Item Code</label>
                                      <select class="form-control text-center sel6 " name="item_code" id="item_code">
                                          <option value=""> Select Item Code</option>
                                          @foreach($item_code as $row)
                                          <option value="{{$row->item_code}}">{{$row->item_code}}</option>
                                          @endforeach
                                        </select>
                                   </div>

                                  <div class="form-group">
                                      <label style="color: black;">Production Order</label>
                                       <select class="form-control text-center sel6 " name="prod" id="prod">
                                          <option value=""> Select Production Order</option>
                                          @foreach($production_order as $row)
                                          <option value="{{$row->production_order}}">{{$row->production_order}}</option>
                                          @endforeach
                                        </select>
                                   </div>
                                  <div class="form-group">
                                      <label style="color: black;">Customer</label>
                                      <select class="form-control text-center sel6 " name="customer" id="customer">
                                          <option value=""> Select Customer</option>
                                          @foreach($customer as $row)
                                          <option value="{{$row->customer}}">{{$row->customer}}</option>
                                          @endforeach
                                        </select>
                                   </div>
                                   
                                   <div class="form-group">
                                      <label style="color: black;">Process</label>
                                      <select class="form-control text-center sel6 " name="process" id="process">
                                        <option value=""> Select Process</option>
                                         @foreach($process as $row)
                                          <option value="{{$row->process_name}}">{{$row->process_name}}</option>
                                          @endforeach
                                        </select>
                                   </div>
                                   <div class="form-group">
                                      <label style="color: black;">QC Status</label>
                                      <select class="form-control text-center sel6 " name="qa_status" id="qa_status">
                                          <option value="">Select Status</option>
                                          <option value="For Confirmation">For Confirmation</option>
                                          <option value="QC Passed">QC Passed</option>
                                          <option value="QC Failed">QC Failed</option>
                                        </select>
                                   </div>
                                   <div class="form-group">
                                      <label style="color: black;">QC Inspector</label>
                                      <select class="form-control text-center sel6 " name="qa_inspector" id="qa_inspector">
                                          <option value="">Select QC Inspector</option>
                                          @foreach($qc_name as $row)
                                          <option value="{{$row['user_id']}}">{{$row['name']}}</option>
                                          @endforeach
                                        </select>
                                   </div>
                                   <div class="form-group">
                                      <label style="color: black;">Operator</label>
                                      <select class="form-control text-center sel6 " name="operator" id="operator">
                                          <option value="">Select Operator</option>
                                          @foreach($operators as $row)
                                          <option value="{{$row->user_id}}">{{$row->employee_name}}</option>
                                          @endforeach
                                        </select>
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
                              
                              <h5 class="text-white font-weight-bold align-middle">Fabrication Random Inspection Logsheet</h5>
                            </div>
                            <div class="col-md-4">
                              <img style="float:right;" src="{{ asset('img/export.png') }}" id="fabrication-btn-export" width="40" height="40" class="btn-export">

                            </div>
                          </div>
                          <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
                            <div class="card card-nav-tabs card-plain">
                              <div class="card-body">
                                <div class="col-md-12">
                                  <div id="tbl_log_fabrication" style="width: 100%;overflow: auto;min-height: 750px;"></div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane" id="painting" role="tabpanel" aria-labelledby="painting">
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
                            <div class="col-md-12" style="margin: 0;height: 780px;" id="filter_qa_inspection_painting">
                              <span style="display: block; font-size: 9pt; margin-top: -8px;"></span>
                              <div class="form-group">
                                  <label style="color: black;">Date Range:</label>
                                  <input type="text" class="date form-control" name="daterange_painting" autocomplete="off" placeholder="Select Date From" id="daterange_painting" value="" style="text-align:center;display:inline-block;width:100%;height:30px;" onchange="tbl_log_fabrication()">
                                </div>
                                <div class="form-group">
                                      <label style="color: black;">Workstation</label>
                                      <select class="form-control text-center sel3" name="workstation_painting" id="workstation_painting" onchange="tbl_log_fabrication()">
                                          <option value="none"> Select Workstation</option>
                                          @foreach($workstation_painting as $row)
                                          <option value="{{$row->workstation_id}}">{{$row->workstation_name}}</option>
                                          @endforeach
                                        </select>
                                   </div>
                                  <div class="form-group" style="margin-top: -14px;">
                                      <label style="color: black;">Item Code</label>
                                      <select class="form-control text-center sel3 " name="item_code_painting" id="item_code_painting">
                                          <option value=""> Select Item Code</option>
                                          @foreach($item_code as $row)
                                          <option value="{{$row->item_code}}">{{$row->item_code}}</option>
                                          @endforeach
                                        </select>
                                   </div>

                                  <div class="form-group">
                                      <label style="color: black;">Production Order</label>
                                       <select class="form-control text-center sel3 " name="prod_painting" id="prod_painting">
                                          <option value=""> Select Production Order</option>
                                          @foreach($production_order as $row)
                                          <option value="{{$row->production_order}}">{{$row->production_order}}</option>
                                          @endforeach
                                        </select>
                                   </div>
                                  <div class="form-group">
                                      <label style="color: black;">Customer</label>
                                      <select class="form-control text-center sel3 " name="customer_painting" id="customer_painting">
                                          <option value=""> Select Customer</option>
                                          @foreach($customer as $row)
                                          <option value="{{$row->customer}}">{{$row->customer}}</option>
                                          @endforeach
                                        </select>
                                   </div>
                                   
                                   <div class="form-group">
                                      <label style="color: black;">Process</label>
                                      <select class="form-control text-center sel3 " name="process_painting" id="process_painting">
                                        <option value=""> Select Process</option>
                                         @foreach($process_painting as $row)
                                          <option value="{{$row->process_name}}">{{$row->process_name}}</option>
                                          @endforeach
                                        </select>
                                   </div>
                                   <div class="form-group">
                                      <label style="color: black;">QC Status</label>
                                      <select class="form-control text-center sel3 " name="qa_status_painting" id="qa_status_painting">
                                          <option value="">Select Status</option>
                                          <option value="For Confirmation">For Confirmation</option>
                                          <option value="QC Passed">QC Passed</option>
                                          <option value="QC Failed">QC Failed</option>
                                        </select>
                                   </div>
                                   <div class="form-group">
                                      <label style="color: black;">QC Inspector</label>
                                      <select class="form-control text-center sel3 " name="qa_inspector_painting" id="qa_inspector_painting">
                                          <option value="">Select QC Inspector</option>
                                          @foreach($qc_name as $row)
                                          <option value="{{$row['user_id']}}">{{$row['name']}}</option>
                                          @endforeach
                                        </select>
                                   </div>
                                   <div class="form-group">
                                      <label style="color: black;">Operator</label>
                                      <select class="form-control text-center sel3 " name="operator_painting" id="operator_painting">
                                          <option value="">Select Operator</option>
                                          @foreach($operators as $row)
                                          <option value="{{$row->user_id}}">{{$row->employee_name}}</option>
                                          @endforeach
                                        </select>
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
                              <h5 class="text-white font-weight-bold align-middle">Painting Random Inspection Logsheet</h5>
                            </div>
                            <div class="col-md-4">
                              <img style="float:right;" src="{{ asset('img/export.png') }}" width="40" height="40" class="painting-btn-export" id="painting-btn-export">

                            </div>
                          </div>
                          <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
                            <div class="card card-nav-tabs card-plain">
                              <div class="card-body">
                                <div class="col-md-8 offset-md-2">
                                <div id="tbl_log_painting" style="width: 100%;overflow: auto;min-height: 750px;"></div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane" id="assembly" role="tabpanel" aria-labelledby="assembly">
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
                            <div class="col-md-12" style="margin: 0;height: 400px;" id="filter_qa_inspection_assembly">
                              <span style="display: block; font-size: 9pt; margin-top: -8px;"></span>

                                
     
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
                              
                              <h5 class="text-white font-weight-bold align-middle">Assembly Random Inspection Logsheet</h5>
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
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>





<style type="text/css">
  

   #fabrication .form-control, #painting .form-control{
    border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px;
}
#assembly .form-control{
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
  // tbl_log_fabrication();
  $('#daterange').val('');
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  
  
  $('.sel3').select2({
    dropdownParent: $("#filter_qa_inspection_painting"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
  $('.sel4').select2({
    dropdownParent: $("#filter_qa_inspection_assembly"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
  $('.sel6').select2({
    dropdownParent: $("#filter_qa_inspection_fabrication"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });


  $('#daterange').daterangepicker({
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
    tbl_log_fabrication();
  });

   $('#daterange').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
  });
  
  $('#daterange_painting').daterangepicker({
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
    tbl_log_painting();
  });

   $('#daterange_painting').on('apply.daterangepicker', function(ev, picker) {
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
<script type="text/javascript">
function tbl_log_fabrication(){
  var date = $('#daterange').val();
  var workstation = $('#workstation').val();
  var startDate = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
  var endDate = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
  var data = {
        customer: $('#customer').val(),
        prod:$('#prod').val(),
        item_code: $('#item_code').val(),
        status: $('#qa_status').val(),
        process: $('#process').val()
      }
  $.ajax({
          url:"/tbl_qa_inspection_log_report_fabrication/"+ startDate +"/" +endDate+"/"+workstation,
          type:"GET",
          data: data,
          success:function(data){
            
            $('#tbl_log_fabrication').html(data);
          }
        });
  };

</script>
<script>
$(document).on('change', '.sel6', function(event){
  var date = $('#daterange').val();
  var workstation = $('#workstation').val();
  var startDate = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
  var endDate = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
  var data = {
        customer: $('#customer').val(),
        prod:$('#prod').val(),
        item_code: $('#item_code').val(),
        status: $('#qa_status').val(),
        process: $('#process').val(),
        qa_inspector: $('#qa_inspector').val(),
        operator: $('#operator').val(),
      }
  if(workstation == 'none'){
    showNotification("danger", 'Please Select Workstation', "now-ui-icons travel_info");
  }else if(date == ""){
    showNotification("danger", 'Please Select Date Range', "now-ui-icons travel_info");
  }else{
    $.ajax({
          url:"/tbl_qa_inspection_log_report_fabrication/"+ startDate +"/" +endDate+"/"+workstation,
          type:"GET",
          data: data,
          success:function(data){
            
            $('#tbl_log_fabrication').html(data);
          }
        });
  }
  
  });
</script> 
<script type="text/javascript">
    $(document).on('click', '#fabrication-btn-export', function(){
      var date = $('#daterange').val();
      var workstation = $('#workstation').val();
      var startDate = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var endDate = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
     
      var customer= $('#customer').val();
      var prod=$('#prod').val();
      var item_code= $('#item_code').val();
      var status= $('#qa_status').val();
      var processs= $('#process').val();
      var qa_inspector= $('#qa_inspector').val();
      var operator= $('#operator').val();
      if(customer ==""){
        var customer="none";
      }
      if(prod ==""){
        var prod="none";
      }
      if(item_code ==""){
        var item_code="none";
      }
      if(status ==""){
        var status="none";
      }
      if(processs ==""){
        var processs="none";
      }
      if(qa_inspector ==""){
        var qa_inspector="none";
      }
      if(operator ==""){
        var operator="none";
      }
      if(workstation == 'none'){
        showNotification("danger", 'Please Select Workstation', "now-ui-icons travel_info");
      }else if(date == ""){
        showNotification("danger", 'Please Select Date Range', "now-ui-icons travel_info");
      }else{
          location.href= "/get_tbl_qa_inspection_log_export/"+ startDate +"/" +endDate+"/"+workstation+"/"+ customer +"/" + prod + "/" + item_code + "/" + status + "/" + processs + "/" + qa_inspector + "/" +  operator;
      }

    });
</script>
<script type="text/javascript">
function tbl_log_painting(){
  var date = $('#daterange_painting').val();
  var workstation = $('#workstation_painting').val();
  var startDate = $('#daterange_painting').data('daterangepicker').startDate.format('YYYY-MM-DD');
  var endDate = $('#daterange_painting').data('daterangepicker').endDate.format('YYYY-MM-DD');
  var data = {
        customer: $('#customer_painting').val(),
        prod:$('#prod_painting').val(),
        item_code: $('#item_code_painting').val(),
        status: $('#qa_status_painting').val(),
        process: $('#process_painting').val()
      }
  $.ajax({
          url:"/tbl_qa_inspection_log_report_painting/"+ startDate +"/" +endDate+"/"+workstation,
          type:"GET",
          data: data,
          success:function(data){
            
            $('#tbl_log_painting').html(data);
          }
        });
  };

</script>
<script>
$(document).on('change', '.sel3', function(event){
  var date = $('#daterange_painting').val();
  var workstation = $('#workstation_painting').val();
  var startDate = $('#daterange_painting').data('daterangepicker').startDate.format('YYYY-MM-DD');
  var endDate = $('#daterange_painting').data('daterangepicker').endDate.format('YYYY-MM-DD');
  var data = {
        customer: $('#customer_painting').val(),
        prod:$('#prod_painting').val(),
        item_code: $('#item_code_painting').val(),
        status: $('#qa_status_painting').val(),
        process: $('#process_painting').val(),
        qa_inspector: $('#qa_inspector_painting').val(),
        operator: $('#operator_painting').val(),
      }
  if(workstation == 'none'){
    showNotification("danger", 'Please Select Workstation', "now-ui-icons travel_info");
  }else if(date == ""){
    showNotification("danger", 'Please Select Date Range', "now-ui-icons travel_info");
  }else{
    $.ajax({
          url:"/tbl_qa_inspection_log_report_painting/"+ startDate +"/" +endDate+"/"+workstation,
          type:"GET",
          data: data,
          success:function(data){
            
            $('#tbl_log_painting').html(data);
          }
        });
  }
  
  });
</script> 
<script type="text/javascript">
    $(document).on('click', '#painting-btn-export', function(){
      var date = $('#daterange_painting').val();
      var workstation = $('#workstation_painting').val();
      var startDate = $('#daterange_painting').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var endDate = $('#daterange_painting').data('daterangepicker').endDate.format('YYYY-MM-DD');
     
      var customer= $('#customer_painting').val();
      var prod=$('#prod_painting').val();
      var item_code= $('#item_code_painting').val();
      var status= $('#qa_status_painting').val();
      var processs= $('#process_painting').val();
      var qa_inspector= $('#qa_inspector_painting').val();
      var operator= $('#operator_painting').val();

      if(workstation == 'none'){
        showNotification("danger", 'Please Select Workstation', "now-ui-icons travel_info");
      }else if(date == ""){
        showNotification("danger", 'Please Select Date Range', "now-ui-icons travel_info");
      }else{
          location.href= "/get_tbl_qa_inspection_log_export_painting/"+ startDate +"/" +endDate+"/"+workstation+"/"+ customer +"/" + prod + "/" + item_code + "/" + status + "/" + processs + "/" + qa_inspector + "/" +  operator;
      }

    });
</script>

@endsection