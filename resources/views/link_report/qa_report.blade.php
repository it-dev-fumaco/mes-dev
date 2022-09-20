@extends('layouts.user_app', [
	'namePage' => 'MES',
	'activePage' => 'operation_report',
  'pageHeader' => 'QA Reports',
  'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header"></div>

<div class="row p-0" style="margin-top: -190px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
		<div class="col-md-12">
      <ul class="nav nav-tabs" role="tablist" id="qa-dashboard-tabs">
				<li class="nav-item">
					<a class="nav-link {{ (request()->segment(2) == '1') ? 'active' : '' }}" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="false">Inspection Logsheet Report</a>
				</li>
				<li class="nav-item">
					<a class="nav-link {{ (request()->segment(2) == '2') ? 'active' : '' }}" data-toggle="tab" href="#tab02" role="tab" aria-controls="tab02" aria-selected="false">Rejection Report</a>
				</li>
			</ul>
			<div class="tab-content" style="min-height: 500px;">
				<div class="tab-pane {{ (request()->segment(2) == '1') ? 'active' : '' }}" id="tab1" role="tabpanel" aria-labelledby="tab1">
					<div class="row">
						<div class="col-md-12">
							<div class="card" style="border-radius: 0 0 3px 3px;">
								<div class="card-body">
									<div class="row">
										<div class="col-md-12">
											<div class="card" style="background-color: whitesmoke">
												<div class="card-body">
													<div class="row">
														<div class="col-md-12 p-0" style="margin-top: -50px;">
															<ul class="nav nav-tabs" id="myTab" role="tablist">
																<li class="nav-item">
																	<a class="nav-link active" id="fabrication-tab" data-toggle="tab" href="#fabrication" role="tab" aria-controls="fabrication" aria-selected="true">Fabrication</a>
																</li>
																<li class="nav-item">
																	<a class="nav-link" id="painting-tab" data-toggle="tab" href="#painting" role="tab" aria-controls="painting" aria-selected="false">Painting</a>
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
																			<div class="card" style="background-color: #0277BD;">
																				<div class="card-body pb-0">
																					<div class="row">
																						<div class="col-md-8" style="margin-top: -10px;">
																							<h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Filter/s</h5>
																						</div>
																						<div class="col-md-4" style="margin-top: -20px;">
																							<button type="button" class="btn btn-default" id="clear-button-fab" style="margin-bottom:5px;font-size: 12px;padding: 7px 9px 6px 10px;float: right;">Clear</button>
																						</div>
																					</div>
																					<div class="row" style="background-color: #ffffff; padding-top: 9px;">
																						<div class="col-md-12 m-0" style="height: 780px;" id="filter_qa_inspection_fabrication">
                                              <div class="form-group">
																								<label class="text-dark">Date Range:</label>
																								<input type="text" class="date form-control" name="daterange" autocomplete="off" placeholder="Select Date From" id="daterange_fabrication" value="" style="text-align:center; display:inline-block; width:100%; height:30px;">
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">Workstation</label>
																								<select class="form-control text-center sel6" name="workstation" id="workstation">
																									<option value=""> Select Workstation</option>
																									@foreach($fab_workstation as $row)
																									<option value="{{ $row->workstation_name }}">{{ $row->workstation_name }}</option>
																									@endforeach
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">Item Code</label>
																								<select class="form-control text-center sel6" name="item_code" id="item_code">
																									<option value="">Select Item Code</option>
																									@foreach($item_code as $row)
																									<option value="{{ $row->item_code }}">{{ $row->item_code }}</option>
																									@endforeach
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">Production Order</label>
																								<select class="form-control text-center sel6" name="prod" id="prod">
																									<option value="">Select Production Order</option>
																									@foreach($production_order as $row)
																									<option value="{{ $row->production_order }}">{{ $row->production_order }}</option>
																									@endforeach
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">Customer</label>                                                       <select class="form-control text-center sel6" name="customer" id="customer">
																									<option value="">Select Customer</option>
																									@foreach($customer as $row)
																									<option value="{{ $row->customer }}">{{ $row->customer }}</option>
																									@endforeach
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">Process</label>
																								<select class="form-control text-center sel6" name="process" id="process">
																									<option value="">Select Process</option>
																									@foreach($fab_process as $row)
																									<option value="{{ $row->process_name }}">{{ $row->process_name }}</option>
																									@endforeach
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">QC Status</label>
																								<select class="form-control text-center sel6" name="qa_status" id="qa_status">
																									<option value="">Select Status</option>
																									<option value="For Confirmation">For Confirmation</option>
																									<option value="QC Passed">QC Passed</option>
																									<option value="QC Failed">QC Failed</option>
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">QC Inspector</label>
																								<select class="form-control text-center sel6" name="qa_inspector" id="qa_inspector">
																									<option value="">Select QC Inspector</option>
																									@foreach($qc_name as $row)
																									<option value="{{$row['user_id']}}">{{$row['name']}}</option>
																									@endforeach
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">Operator</label>
																								<select class="form-control text-center sel6" name="operator" id="operator">
																									<option value="">Select Operator</option>
																									@foreach($operators as $row)
																									<option value="{{ $row->user_id }}">{{ $row->employee_name }}</option>
																									@endforeach
																								</select>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																		<div class="col-md-10">
																			<div class="card">
																				<div class="card-body pb-0">
																					<div class="col-md-12 m-0 p-0">
																						<div class="tableFixHead w-100" id="tbl_log_fabrication" style="overflow: auto; max-height: 750px;"></div>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
																<div class="tab-pane" id="painting" role="tabpanel" aria-labelledby="painting">
																	<div class="row" style="margin-top: 12px;">
																		<div class="col-md-2">
																			<div class="card" style="background-color: #0277BD;">
																				<div class="card-body pb-0">
																					<div class="row">
																						<div class="col-md-8" style="margin-top: -10px;">
																							<h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Filter/s</h5>
																						</div>
																						<div class="col-md-4" style="margin-top: -20px;">
																							<button type="button" class="btn btn-default" id="clear-button-painting" style="margin-bottom:5px;font-size: 12px;padding: 7px 9px 6px 10px;float: right;">Clear</button>
																						</div>
																					</div>
																					<div class="row" style="background-color: #ffffff; padding-top: 9px;">
																						<div class="col-md-12 m-0" style="height: 780px;" id="filter_qa_inspection_painting">
																							<div class="form-group">
																								<label class="text-dark">Date Range:</label>
																								<input type="text" class="date form-control" name="daterange_painting" autocomplete="off" placeholder="Select Date From" id="daterange_painting" value="" style="text-align:center;display:inline-block;width:100%;height:30px;">
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">Workstation</label>
																								<select class="form-control text-center sel3" name="workstation_painting" id="workstation_painting">
																									<option value="">Select Workstation</option>
																									@foreach($pain_workstation as $row)
																									<option value="{{ $row->workstation_name }}">{{ $row->workstation_name }}</option>
																									@endforeach
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">Item Code</label>
																								<select class="form-control text-center sel3" name="item_code_painting" id="item_code_painting">
																									<option value="">Select Item Code</option>
																									@foreach($item_code as $row)
																									<option value="{{ $row->item_code }}">{{ $row->item_code }}</option>
																									@endforeach
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">Production Order</label>
																								<select class="form-control text-center sel3" name="prod_painting" id="prod_painting">
																									<option value="">Select Production Order</option>
																									@foreach($production_order as $row)
																									<option value="{{ $row->production_order }}">{{ $row->production_order }}</option>
																									@endforeach
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">Customer</label>
																								<select class="form-control text-center sel3" name="customer_painting" id="customer_painting">
																									<option value="">Select Customer</option>
																									@foreach($customer as $row)
																									<option value="{{ $row->customer }}">{{ $row->customer }}</option>
																									@endforeach
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">Process</label>
																								<select class="form-control text-center sel3" name="process_painting" id="process_painting">
																									<option value="">Select Process</option>
																									@foreach($process_painting as $row)
																									<option value="{{ $row->process_name }}">{{ $row->process_name }}</option>
																									@endforeach
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">QC Status</label>
																								<select class="form-control text-center sel3" name="qa_status_painting" id="qa_status_painting">
																									<option value="">Select Status</option>
																									<option value="For Confirmation">For Confirmation</option>
																									<option value="QC Passed">QC Passed</option>
																									<option value="QC Failed">QC Failed</option>
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">QC Inspector</label>
																								<select class="form-control text-center sel3" name="qa_inspector_painting" id="qa_inspector_painting">
																									<option value="">Select QC Inspector</option>
																									@foreach($qc_name as $row)
																									<option value="{{ $row['user_id'] }}">{{ $row['name'] }}</option>
																									@endforeach
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">Operator</label>
																								<select class="form-control text-center sel3" name="operator_painting" id="operator_painting">
																									<option value="">Select Operator</option>
																									@foreach($operators as $row)
																									<option value="{{ $row->user_id }}">{{ $row->employee_name }}</option>
																									@endforeach
																								</select>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																		<div class="col-md-10">
																			<div class="card">
																				<div class="card-body pb-0">
																					<div class="row" style="background-color: #ffffff;height: auto; min-height: 500px; max-height: 750px;">
																						<div class="col-md-12" style="max-height: 750px;">
																							<div id="tbl_log_painting" style="width: 100%;overflow: auto;min-height: 750px;max-height: 750px;"></div>
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
																				<div class="card-body pb-0">
																					<div class="row">
																						<div class="col-md-8" style="margin-top: -10px;">
																							<h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Filter/s</h5>
																						</div>
																						<div class="col-md-4" style="margin-top: -20px;">
																							<button type="button" class="btn btn-default" id="clear-button-assem" style="margin-bottom:5px;font-size: 12px;padding: 7px 9px 6px 10px;float: right;">Clear</button>
																						</div>
																					</div>
																					<div class="row" style="background-color: #ffffff; padding-top: 9px;">
																						<div class="col-md-12 m-0" style="height: 730px;" id="filter_qa_inspection_assembly">
																							<div class="form-group">
																								<label class="text-dark">Date Range:</label>
																								<input type="text" class="date form-control" name="daterange_assem" autocomplete="off" placeholder="Select Date From" id="daterange_assem" value="" style="text-align:center;display:inline-block;width:100%;height:30px;">
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">Workstation</label>
																								<select class="form-control text-center sel4" name="workstation_assem" id="workstation_assem">
																									<option value="">Select Workstation</option>
																									@foreach($assem_workstation as $row)
																									<option value="{{ $row->workstation_name }}">{{ $row->workstation_name }}</option>
																									@endforeach
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">Item Code</label>
																								<select class="form-control text-center sel4" name="item_code_assem" id="item_code_assem">
																									<option value=""> Select Item Code</option>
																									@foreach($item_code as $row)
																									<option value="{{$row->item_code}}">{{$row->item_code}}</option>
																									@endforeach
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">Production Order</label>
																								<select class="form-control text-center sel4" name="prod_assem" id="prod_assem">
																									<option value="">Select Production Order</option>
																									@foreach($production_order as $row)
																									<option value="{{$row->production_order}}">{{$row->production_order}}</option>
																									@endforeach
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">Customer</label>
																								<select class="form-control text-center sel4 " name="customer_assem" id="customer_assem">
																									<option value=""> Select Customer</option>
																									@foreach($customer as $row)
																									<option value="{{$row->customer}}">{{$row->customer}}</option>
																									@endforeach
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">Process</label>
																								<select class="form-control text-center sel4 " name="process_assem" id="process_assem">
																									<option value=""> Select Process</option>
																									@foreach($assem_process as $row)
																									<option value="{{$row->process_name}}">{{$row->process_name}}</option>
																									@endforeach
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">QC Status</label>
																								<select class="form-control text-center sel4" name="qa_status_assem" id="qa_status_assem">
																									<option value="">Select Status</option>
																									<option value="For Confirmation">For Confirmation</option>
																									<option value="QC Passed">QC Passed</option>
																									<option value="QC Failed">QC Failed</option>
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">QC Inspector</label>
																								<select class="form-control text-center sel4" name="qa_inspector_assem" id="qa_inspector_assem">
																									<option value="">Select QC Inspector</option>
																									@foreach($qc_name as $row)
																									<option value="{{ $row['user_id'] }}">{{ $row['name'] }}</option>
																									@endforeach
																								</select>
																							</div>
																							<div class="form-group margin-top">
																								<label class="text-dark">Operator</label>
																								<select class="form-control text-center sel4" name="operator_assem" id="operator_assem">
																									<option value="">Select Operator</option>
																									@foreach($operators as $row)
																									<option value="{{ $row->user_id }}">{{ $row->employee_name }}</option>
																									@endforeach
																								</select>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																		<div class="col-md-10">
																			<div class="card">
																				<div class="card-body pb-0">
																					<div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
																						<div class="col-md-12" style="max-height: 750px;">
																							<div id="tbl_log_assem" style="width: 100%;overflow: auto;min-height: 750px;max-height: 750px;" class="table-responsive"></div>
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
        <div class="tab-pane {{ (request()->segment(2) == '2') ? 'active' : '' }}" id="tab02" role="tabpanel" aria-labelledby="tab2">
          <div class="row">
            <div class="col-md-12">
              <div class="card" style="border-radius: 0 0 3px 3px;">
                <div class="card-body p-1">
                  <div class="row m-0">
                    <div class="col-md-12 m-0">
                      <div class="row">
                        <div class="col-md-12 m-0 p-0" style="margin-top: -50px;">
                          <ul class="nav nav-tabs" role="tablist" id="reject-dashboard-tabs">
                            <li class="nav-item">
                              <a class="nav-link active" id="fab_tab_reject" data-toggle="tab" href="#fab0" role="tab" aria-controls="fab0" aria-selected="true">Fabrication</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link " id="pain_tab_reject" data-toggle="tab" href="#pan1" role="tab" aria-controls="pan1" aria-selected="false">Painting</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link " id="assem_tab_reject" data-toggle="tab" href="#assem2" role="tab" aria-controls="assem2" aria-selected="false" id="olu_click">Wiring and Assembly</a>
                            </li>
                          </ul>
                          <div class="tab-content" style="min-height: 500px;">
                            {{-- Fabrication Rejection Report --}}
                            <div class="tab-pane active" id="fab0" role="tabpanel" aria-labelledby="fab0">
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="card mb-0" style="border-radius: 0 0 3px 3px;">
                                    <div class="card-body">
                                      <div class="row">
                                        <div class="col-md-6 offset-md-6">
                                          <div class="row">
                                            <div class="col-md-4 p-0">
                                              <div class="form-group text-right">
                                                <label for="fab_reject_filter" class="text-dark font-weight-bold m-2 p-0" style="font-size: 11pt;">Reject Category:</label>
                                              </div>
                                            </div>
                                            <div class="col-md-4 p-0">
                                              <div class="form-group">
                                                <select class="form-control form-control-lg text-center class-dynamic m-0" name="fab_reject_filter" id="fab_reject_filter">
                                                  @foreach($reject_category as $rows)
                                                    <option value="{{$rows->reject_category_id}}">{{$rows->reject_category_name}}</option>
                                                  @endforeach
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-1 p-0">
                                              <div class="form-group text-right">
                                                <label for="fab_yearpicker" class="text-dark font-weight-bold m-2 p-0" style="font-size: 11pt;">Year:</label>
                                              </div>
                                            </div>
                                            <div class="col-md-2 p-0">
                                              <div class="form-group">
                                                <select id="fab_yearpicker" style="font-weight: bolder;" name="fab_yearpicker" class="form-control form-control-lg">
                                                  <option value="2017" {{ date('Y') == 2017 ? 'selected' : '' }}>2017</option>
                                                  <option value="2018" {{ date('Y') == 2018 ? 'selected' : '' }}>2018</option>
                                                  <option value="2019" {{ date('Y') == 2019 ? 'selected' : '' }}>2019</option>
                                                  <option value="2020" {{ date('Y') == 2020 ? 'selected' : '' }}>2020</option>
                                                  <option value="2021" {{ date('Y') == 2021 ? 'selected' : '' }}>2021</option>
                                                  <option value="2022" {{ date('Y') == 2022 ? 'selected' : '' }}>2022</option>
                                                  <option value="2023" {{ date('Y') == 2023 ? 'selected' : '' }}>2023</option>
                                                  <option value="2024" {{ date('Y') == 2024 ? 'selected' : '' }}>2024</option>
                                                  <option value="2025" {{ date('Y') == 2025 ? 'selected' : '' }}>2025</option>
                                                  <option value="2026" {{ date('Y') == 2026 ? 'selected' : '' }}>2026</option>
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-1 p-0">
                                              <img src="{{ asset('img/print.png') }}" width="35" class="ml-3 mt-1 print-rejection-report-btn" data-operation-id="1">
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-12 m-0">
                                        <div class="row">
                                          <div class="col-md-6">
                                            <div class="card">
                                              <div class="card-body">
                                                <div class="col-md-12">
                                                  <h5 id="label_fab_chart_reject" class="text-center font-weight-bold"></h5>
                                                    <canvas id="tbl_fab_reject_report_chart" height="120"></canvas>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="card">
                                              <div class="card-body">
                                                <div class="col-md-12">
                                                  <h5 id="label_fab_rate_chart_reject" class="text-center font-weight-bold"></h5>
                                                  <canvas id="tbl_fab_rate_report_chart" height="120"></canvas>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-md-12">
                                            <div class="row">
                                              <div class="col-md-12" style="">
                                                <div id="tbl_fab_log_reject_report" style="width: 100%;overflow: auto;"></div>
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
                            {{-- Painting Rejection Report --}}
                            <div class="tab-pane" id="pan1" role="tabpanel" aria-labelledby="pan1">
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="card mb-0" style="border-radius: 0 0 3px 3px;">
                                    <div class="card-body">
                                      <div class="row">
                                        <div class="col-md-6 offset-md-6">
                                          <div class="row">
                                            <div class="col-md-4 p-0">
                                              <div class="form-group text-right">
                                                <label for="pain_reject_filter" class="text-dark font-weight-bold m-2 p-0" style="font-size: 11pt;">Reject Category:</label>
                                              </div>
                                            </div>
                                            <div class="col-md-4 p-0">
                                              <div class="form-group">
                                                <select class="form-control form-control-lg text-center class-dynamic m-0" name="pain_reject_filter" id="pain_reject_filter">
                                                  @foreach($reject_category as $rows)
                                                    <option value="{{$rows->reject_category_id}}">{{$rows->reject_category_name}}</option>
                                                  @endforeach
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-1 p-0">
                                              <div class="form-group text-right">
                                                <label for="pain_yearpicker" class="text-dark font-weight-bold m-2 p-0" style="font-size: 11pt;">Year:</label>
                                              </div>
                                            </div>
                                            <div class="col-md-2 p-0">
                                              <div class="form-group">
                                                <select id="pain_yearpicker" style="font-weight: bolder;" name="pain_yearpicker" class="form-control form-control-lg">
                                                  <option value="2017" {{ date('Y') == 2017 ? 'selected' : '' }}>2017</option>
                                                  <option value="2018" {{ date('Y') == 2018 ? 'selected' : '' }}>2018</option>
                                                  <option value="2019" {{ date('Y') == 2019 ? 'selected' : '' }}>2019</option>
                                                  <option value="2020" {{ date('Y') == 2020 ? 'selected' : '' }}>2020</option>
                                                  <option value="2021" {{ date('Y') == 2021 ? 'selected' : '' }}>2021</option>
                                                  <option value="2022" {{ date('Y') == 2022 ? 'selected' : '' }}>2022</option>
                                                  <option value="2023" {{ date('Y') == 2023 ? 'selected' : '' }}>2023</option>
                                                  <option value="2024" {{ date('Y') == 2024 ? 'selected' : '' }}>2024</option>
                                                  <option value="2025" {{ date('Y') == 2025 ? 'selected' : '' }}>2025</option>
                                                  <option value="2026" {{ date('Y') == 2026 ? 'selected' : '' }}>2026</option>
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-1 p-0">
                                              <img src="{{ asset('img/print.png') }}" width="35" class="ml-3 mt-1 print-rejection-report-btn" data-operation-id="2">
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-12 m-0">
                                        <div class="row">
                                          <div class="col-md-6">
                                            <div class="card">
                                              <div class="card-body">
                                                <div class="col-md-12">
                                                  <h5 id="label_pain_chart_reject" class="text-center font-weight-bold"></h5>
                                                    <canvas id="tbl_pain_reject_report_chart" height="120"></canvas>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="card">
                                              <div class="card-body">
                                                <div class="col-md-12">
                                                  <h5 id="label_pain_rate_chart_reject" class="text-center font-weight-bold"></h5>
                                                  <canvas id="tbl_pain_rate_report_chart" height="120"></canvas>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-md-12">
                                            <div class="row">
                                              <div class="col-md-12" style="">
                                                <div id="tbl_pain_log_reject_report" style="width: 100%;overflow: auto;"></div>
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
                            {{-- Assembly Rejection Report --}}
                            <div class="tab-pane" id="assem2" role="tabpanel" aria-labelledby="assem2">
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="card mb-0" style="border-radius: 0 0 3px 3px;">
                                    <div class="card-body">
                                      <div class="row">
                                        <div class="col-md-6 offset-md-6">
                                          <div class="row">
                                            <div class="col-md-4 p-0">
                                              <div class="form-group text-right">
                                                <label for="assem_reject_filter" class="text-dark font-weight-bold m-2 p-0" style="font-size: 11pt;">Reject Category:</label>
                                              </div>
                                            </div>
                                            <div class="col-md-4 p-0">
                                              <div class="form-group">
                                                <select class="form-control form-control-lg text-center class-dynamic m-0" name="assem_reject_filter" id="assem_reject_filter">
                                                  @foreach($reject_category as $rows)
                                                    <option value="{{$rows->reject_category_id}}">{{$rows->reject_category_name}}</option>
                                                  @endforeach
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-1 p-0">
                                              <div class="form-group text-right">
                                                <label for="assem_yearpicker" class="text-dark font-weight-bold m-2 p-0" style="font-size: 11pt;">Year:</label>
                                              </div>
                                            </div>
                                            <div class="col-md-2 p-0">
                                              <div class="form-group">
                                                <select id="assem_yearpicker" style="font-weight: bolder;" name="assem_yearpicker" class="form-control form-control-lg">
                                                  <option value="2017" {{ date('Y') == 2017 ? 'selected' : '' }}>2017</option>
                                                  <option value="2018" {{ date('Y') == 2018 ? 'selected' : '' }}>2018</option>
                                                  <option value="2019" {{ date('Y') == 2019 ? 'selected' : '' }}>2019</option>
                                                  <option value="2020" {{ date('Y') == 2020 ? 'selected' : '' }}>2020</option>
                                                  <option value="2021" {{ date('Y') == 2021 ? 'selected' : '' }}>2021</option>
                                                  <option value="2022" {{ date('Y') == 2022 ? 'selected' : '' }}>2022</option>
                                                  <option value="2023" {{ date('Y') == 2023 ? 'selected' : '' }}>2023</option>
                                                  <option value="2024" {{ date('Y') == 2024 ? 'selected' : '' }}>2024</option>
                                                  <option value="2025" {{ date('Y') == 2025 ? 'selected' : '' }}>2025</option>
                                                  <option value="2026" {{ date('Y') == 2026 ? 'selected' : '' }}>2026</option>
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-1 p-0">
                                              <img src="{{ asset('img/print.png') }}" width="35" class="ml-3 mt-1 print-rejection-report-btn" data-operation-id="3">
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-12 m-0">
                                        <div class="row">
                                          <div class="col-md-6">
                                            <div class="card">
                                              <div class="card-body">
                                                <div class="col-md-12">
                                                  <h5 id="label_assem_chart_reject" class="text-center font-weight-bold"></h5>
                                                    <canvas id="tbl_assem_reject_report_chart" height="120"></canvas>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="card">
                                              <div class="card-body">
                                                <div class="col-md-12">
                                                  <h5 id="label_assem_rate_chart_reject" class="text-center font-weight-bold"></h5>
                                                  <canvas id="tbl_assem_rate_report_chart" height="120"></canvas>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-md-12">
                                            <div class="row">
                                              <div class="col-md-12">
                                                <div id="tbl_assem_log_reject_report" style="width: 100%;overflow: auto;"></div>
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
    </div>
  

    <div class="modal fade" id="jt-workstations-modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document" style="min-width: 90%;">
        <div class="modal-content">
          <div class="text-white rounded-top" style="background-color: #0277BD;">
            <div class="d-flex flex-row justify-content-between p-3 align-items-center">
              <h5 class="font-weight-bold m-0 p-0">Job Ticket</h5>
              <div class="float-right">
                <h5 class="modal-title font-weight-bold p-0 mr-5 font-italic d-inline-block">Modal Title</h5>
                <button type="button" class="close d-inline-block ml-3" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            </div>
          </div>
          <div class="modal-body" style="min-height: 600px;">
            <div class="row" id="jt-details-col">
              <div class="col-md-12">
                <div class="container">
                  <div class="row">
                    <ul class="breadcrumb-c" id="process-bc"></ul>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div style="margin: 5px;">
                  <span style="font-size: 12pt; margin: auto;">Scheduled Date: </span>
                  <span class="sched-date font-weight-bold" style="font-size: 12pt; margin: auto;"></span>
                  <span class="badge badge-info task-status">--</span>
                </div>
                <table style="width: 100%; border-color: #D5D8DC;">
                  <col style="width: 18%;">
                  <col style="width: 24%;">
                  <col style="width: 23%;">
                  <col style="width: 20%;">
                  <col style="width: 15%;">
                  <tr style="font-size: 9pt;">
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>REFERENCE NO.</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>CUSTOMER</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PROJECT</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>DELIVERY DATE</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>QTY</b></td>
                  </tr>
                  <tr style="font-size: 10pt;">
                    <td class="text-center" style="border: 1px solid #ABB2B9;"><span class="ref-no"></span></td>
                    <td class="text-center" style="border: 1px solid #ABB2B9;"><span class="cust"></span></td>
                    <td class="text-center" style="border: 1px solid #ABB2B9;"><span class="proj"></span></td>
                    <td class="text-center" style="border: 1px solid #ABB2B9;"><span class="del-date"></span></td>
                    <td class="text-center" style="border: 1px solid #ABB2B9; font-size: 15pt;"><span class="qty"></span></td>
                  </tr>
                  <tr style="font-size: 10pt;">
                    <td style="border: 1px solid #ABB2B9; font-size: 9pt;" class="text-center"><b>ITEM DETAIL(S):</b></td>
                    <td style="border: 1px solid #ABB2B9;" colspan="4">
                      <span class="item-code font-weight-bold"></span> - <span class="desc">--</span>
                    </td>
                  </tr>
                </table>
              </div>				
              <div class="col-md-12" style="padding-top: 20px;">
                <br>
                <table style="width: 100%; border-color: #D5D8DC;" id="totals-tbl">
                  <col style="width: 25%;">
                  <col style="width: 25%;">
                  <col style="width: 25%;">
                  <col style="width: 25%;">
                  <thead style="font-size: 10pt;">
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PRODUCED QTY</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>TOTAL GOOD</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>TOTAL REJECT</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>BALANCE QTY</b></td>
                  </thead>
                  <tbody style="font-size: 9pt;">
                    <tr>
                      <td class="text-center" style="border: 1px solid #ABB2B9;">
                        <span class="produced-qty" style="font-size: 15pt;"></span>
                      </td>
                      <td class="text-center" style="border: 1px solid #ABB2B9;">
                        <span class="total-good" style="font-size: 15pt;"></span>
                      </td>
                      <td class="text-center" style="border: 1px solid #ABB2B9;">
                        <span class="total-reject" style="font-size: 15pt;"></span>
                      </td>
                      <td class="text-center" style="border: 1px solid #ABB2B9;">
                        <span class="balance-qty" style="font-size: 15pt;"></span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="col-md-12">
                <br>
                <div class="table-respons1ive">
                  <table style="width: 100%; border-color: #D5D8DC;" id="jt-details-tbl">
                    <col style="width: 15%;">
                    <col style="width: 15%;">
                    <col style="width: 12%;">
                    <col style="width: 12%;">
                    <col style="width: 10%;">
                    <col style="width: 12%;">
                    <col style="width: 12%;">
                    <col style="width: 12%;">
                    <thead style="font-size: 10pt;">
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>WORKSTATION</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PROCESS</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>GOOD</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>REJECT</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>MACHINE</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>START</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>END</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>OPERATOR</b></td>
                    </thead>
                    <tbody style="font-size: 9pt;"></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="spotwelding-modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document" style="min-width: 90%;">
        <div class="modal-content">
          <div class="modal-header text-white" style="background-color: #0277BD;">
            <h5 class="modal-title font-weight-bold prod-title">Modal Title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" style="min-height: 500px;">
            <div class="row">
              <div class="col-md-12">
                <div id="spotwelding-div"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="print-report-modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document" style="min-width: 78%;">
        <div class="modal-content">
          <div class="modal-header text-white" style="background-color: #0277BD;">
            <h5 class="modal-title font-weight-bold">Print Report</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" style="min-height: 500px;">
            <div class="row">
              <div class="col-md-12" style="overflow: hidden;">
                <iframe src="#" id="print-report-iframe" class="d-no1ne zoom-frame" height="100%" width="100%" style="min-height: 800px;"></iframe>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="print-report-btn">Print</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

  <style>
    .classheader{
      position: sticky; top: 0; 
      z-index:2;
      position: -webkit-sticky;
      border: 0.8px solid white;
    }

    .margin-top{
      margin-top: -18px;
    }

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

    .span-title{
      display: block;
      font-weight: bold;
      font-size: 11pt;
    }
  
    .span-qty{
      display: block;
      font-weight: bold;
      font-size: 22pt;
    }
  
    .span-uom{
      display: block;
      font-size:8pt;
    }

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

   .breadcrumb-c {
      font-size: 8pt;
      font-weight: bold;
      padding: 0;
      background: transparent;
      list-style: none;
      overflow: hidden;
      margin-top: 3px;
      margin-bottom: 3px;
      width: 100%;
      border-radius: 4px;
    }
  
    .breadcrumb-c>li {
      display: table-cell;
      vertical-align: top;
      width: 0.8%;
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
  
    .truncate {
      white-space: nowrap;
      /*overflow: hidden;*/
      text-overflow: ellipsis;
    }
  
    .scrolltbody tbody {
      display:block;
      height:300px;
      overflow:auto;
    }
    .scrolltbody thead, .scrolltbody tbody tr {
        display:table;
        width:100%;
        table-layout:fixed;
    }
    .scrolltbody thead {
        width: calc(100%)
    }
      .numpad-div .row1{
      -webkit-user-select: none; /* Chrome/Safari */        
      -moz-user-select: none; /* Firefox */
      -ms-user-select: none; /* IE10+ */
      /* Not implemented yet */
      -o-user-select: none;
      user-select: none;   
    }
  
    .numpad{
      display: inline-block;
      border: 1px solid #333;
      border-radius: 5px;
      text-align: center;
      width: 27%;
      height: 27%;
      line-height: 60px;
      margin: 3px;
      font-size: 15pt;
      color: inherit;
      background: rgba(255, 255, 255, 0.7);
      transition: all 0.3s ease-in-out;
    }
  
    .numpad:active,
    .numpad:hover {
      cursor: pointer ;
      box-shadow: inset 0 0 2px #000000;
    }
  </style>

  @include('quality_inspection.modal_inspection')
  @endsection
  
  @section('script')
  <link rel="stylesheet" type="text/css" href="{{ asset('js/daterange/daterangepicker.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/jquery.timepicker.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
  <script type="text/javascript" src="{{ asset('js/daterange/moment.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
  <script type="text/javascript" src="{{ asset('css/datepicker/jquery.timepicker.js') }}"></script>
  <script type="text/javascript" src="{{ asset('css/datepicker/datepair.js') }}"></script>
  <script type="text/javascript" src="{{ asset('css/datepicker/jquery.datepair.js') }}"></script>
  <script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>

  <script>
    $(document).ready(function(){
      $(document).on('click', '.print-rejection-report-btn', function(e){
        var a = $(this).parent().parent();
        var reject_category = a.find('select').eq(0).val();
        var reject_cat_name = a.find('select option:selected').eq(0).text();      
        var operation = $(this).data('operation-id');
        var year = a.find('select').eq(1).val();
        var data = {
          operation: operation,
          reject_category : reject_category,
          reject_name: reject_cat_name,
          year: year
        }

        $('#print-report-iframe').attr('src', "/print_qa_rejection_report?" + $.param( data ));

        $('#print-report-modal').modal('show');
      });

      $('#print-report-modal').on('hidden.bs.modal', function (e) {
        $('#print-report-iframe').attr('src', "");
      });

      $('#print-report-btn').click(function(e){
        $("#print-report-iframe").get(0).contentWindow.print();
      });

      $(document).on('click', '.reject-confirmation-btn', function(e){
        e.preventDefault();
        
        var inspection_type = $(this).data('inspection-type');
        var workstation = $(this).data('workstation');
        var production_order = $(this).data('production-order');
        var process_id = $(this).data('process-id');
        var qa_id = $(this).data('qaid');
  
        $.ajax({
          url:"/get_reject_confirmation_checklist/" + production_order + "/" + workstation + "/" + process_id + "/" + qa_id,
          type:"GET",
          success:function(data){
            $('#quality-inspection-div').html(data);
            $('#quality-inspection-modal .qc-type').text(inspection_type);
            $('#quality-inspection-modal .qc-workstation').text('[' + workstation + ']');
            $('#quality-inspection-modal').modal('show');
          }
        });
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

			$('.sel3').select2({
				dropdownParent: $("#filter_qa_inspection_painting"),
				dropdownAutoWidth: false,
				width: '100%',
				cache: false,
				placeholder: "",
				allowClear: true
			});

			$('.sel4').select2({
				dropdownParent: $("#filter_qa_inspection_assembly"),
				dropdownAutoWidth: false,
				width: '100%',
				cache: false,
				placeholder: "",
				allowClear: true
			});
			
			$('.sel6').select2({
				dropdownParent: $("#filter_qa_inspection_fabrication"),
				dropdownAutoWidth: false,
				width: '100%',
				cache: false,
				placeholder: "",
				allowClear: true
			});

      $(document).on('change', '.sel3', function(e){
        tbl_log_painting();
      });

      $(document).on('change', '.sel4', function(e){
        tbl_log_assem();
      });

      $(document).on('change', '.sel6', function(e){
        tbl_log_fabrication();
      });

			$('#daterange_fabrication').daterangepicker({
				"showDropdowns": true,
				"startDate": moment().startOf('month'),
				"endDate": moment().endOf('month'),
				"ranges": {
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
				tbl_log_fabrication();
			});

      $('#daterange_painting').daterangepicker({
        "showDropdowns": true,
        "startDate": moment().startOf('month'),
        "endDate": moment().endOf('month'),
        "ranges": {
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
        tbl_log_painting();
      });

      $('#daterange_assem').daterangepicker({
        "showDropdowns": true,
        "startDate": moment().startOf('month'),
        "endDate": moment().endOf('month'),
        "ranges": {
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
        tbl_log_assem();
      });
      
      $('#daterange_assem').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        tbl_log_assem();
      });

			$('#daterange_fabrication').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        tbl_log_fabrication();
			});

      $('#daterange_painting').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        tbl_log_painting();
      });

      tbl_log_fabrication();
      tbl_log_painting();
      tbl_log_assem();

      function tbl_log_fabrication(){
        var date = $('#daterange_fabrication').val();
        var workstation = $('#workstation').val();
        var startDate = $('#daterange_fabrication').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var endDate = $('#daterange_fabrication').data('daterangepicker').endDate.format('YYYY-MM-DD');
        var operation_id = 1;
        var data = {
          workstation : workstation,
          customer: $('#customer').val(),
          prod: $('#prod').val(),
          item_code: $('#item_code').val(),
          status: $('#qa_status').val(),
          process: $('#process').val()
        }
  
        $.ajax({
          url:"/tbl_qa_inspection_log_report/"+ startDate +"/" +endDate+'/'+ operation_id,
          type:"GET",
          data: data,
          success:function(data){
            $('#tbl_log_fabrication').html(data);
          }
        });
      }

      function tbl_log_painting(){
        var date = $('#daterange_painting').val();
        var workstation = $('#workstation_painting').val();
        var startDate = $('#daterange_painting').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var endDate = $('#daterange_painting').data('daterangepicker').endDate.format('YYYY-MM-DD');
        var operation_id = 1;
        var data = {
          workstation: 'Painting',
          customer: $('#customer_painting').val(),
          prod:$('#prod_painting').val(),
          item_code: $('#item_code_painting').val(),
          status: $('#qa_status_painting').val(),
          process: $('#process_painting').val()
        }

        $.ajax({
          url:"/tbl_qa_inspection_log_report/"+ startDate +"/" +endDate+"/"+operation_id,
          type:"GET",
          data: data,
          success:function(data){
            $('#tbl_log_painting').html(data);
          }
        });
      }

      function tbl_log_assem(){
        var date = $('#daterange_assem').val();
        var workstation = $('#workstation_assem').val();
        var startDate = $('#daterange_assem').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var endDate = $('#daterange_assem').data('daterangepicker').endDate.format('YYYY-MM-DD');
        var operation_id= 3;
        var data = {
          workstation: workstation,
          customer: $('#customer_assem').val(),
          prod:$('#prod_assem').val(),
          item_code: $('#item_code_assem').val(),
          status: $('#qa_status_assem').val(),
          process: $('#process_assem').val()
        }

        $.ajax({
          url:"/tbl_qa_inspection_log_report/"+ startDate +"/" +endDate+"/"+operation_id,
          type:"GET",
          data: data,
          success:function(data){
            $('#tbl_log_assem').html(data);
          }
        });
      }

      $(document).on('change', '#fab_reject_filter', function(event){
        tbl_fab_log_reject_report('#tbl_fab_log_reject_report');
        fab_optyStats();
        tbl_fab_reject_rate_chart();
      });
    
      $(document).on('change', '#fab_yearpicker', function(event){
        tbl_fab_log_reject_report('#tbl_fab_log_reject_report');
        fab_optyStats();
        tbl_fab_reject_rate_chart();
      });

      $(document).on('change', '#assem_reject_filter', function(event){
        tbl_assem_log_reject_report();
        assem_optyStats();
        tbl_assem_reject_rate_chart();
      });

      $(document).on('change', '#pain_reject_filter', function(event){
        tbl_pain_log_reject_report();
        pain_optyStats();
        tbl_pain_reject_rate_chart();
      });

      $(document).on('change', '#pain_yearpicker', function(event){
        tbl_pain_log_reject_report();
        pain_optyStats();
        tbl_pain_reject_rate_chart();
      });

      $(document).on('change', '#assem_yearpicker', function(event){
        tbl_assem_log_reject_report();
        assem_optyStats();
        tbl_assem_reject_rate_chart();
      }); 

      $(document).on('click', '#fabrication-btn-export', function(){
        var date = $('#daterange').val();
        var workstation = $('#workstation').val();
        var startDate = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var endDate = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
        var customer = $('#customer').val();
        var prod = $('#prod').val();
        var item_code = $('#item_code').val();
        var status = $('#qa_status').val();
        var processs = $('#process').val();
        var qa_inspector = $('#qa_inspector').val();
        var operator = $('#operator').val();
  
        customer = (customer == "") ? "none" : customer;
        prod = (prod == "") ? "none" : prod;
        item_code = (item_code == "") ? "none" : item_code;
        processs = (processs == "") ? "none" : processs;
        qa_inspector = (qa_inspector == "") ? "none" : qa_inspector;
        status = (status == "") ? "none" : status;
        operator = (operator == "") ? "none" : operator;
        
        if(workstation != "none" && date != ""){
          location.href= "/get_tbl_qa_inspection_log_export/"+ startDate +"/" +endDate+"/"+workstation+"/"+ customer +"/" + prod + "/" + item_code + "/" + status + "/" + processs + "/" + qa_inspector + "/" +  operator;
        }
      });
  
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
  
        if(workstation != "none" && date != ""){
          location.href= "/get_tbl_qa_inspection_log_export_painting/"+ startDate +"/" +endDate+"/"+workstation+"/"+ customer +"/" + prod + "/" + item_code + "/" + status + "/" + processs + "/" + qa_inspector + "/" +  operator;
        }
      });

      $("#clear-button-fab").click(function(){
        $("#workstation").select2('val', 'All');
        $("#customer").select2('val', 'All');
        $("#prod").select2('val', 'All');
        $("#status").select2('val', 'All');
        $("#process").select2('val', 'All');
        $("#qa_inspector").select2('val', 'All');
        $("#operator").select2('val', 'All');
        $("#item_code").select2('val', 'All');
        $("#qa_status").select2('val', 'All');
      });

      $("#clear-button-painting").click(function(){
        $("#workstation_painting").select2('val', 'All');
        $("#customer_painting").select2('val', 'All');
        $("#prod_painting").select2('val', 'All');
        $("#status_painting").select2('val', 'All');
        $("#process_painting").select2('val', 'All');
        $("#qa_inspector_painting").select2('val', 'All');
        $("#operator_painting").select2('val', 'All');
        $("#item_code_painting").select2('val', 'All');
        $("#qa_status_painting").select2('val', 'All');
      });
      
      var currentTime = new Date()
      var year = currentTime.getFullYear();

      $("#fab_yearpicker").val(year);

      function tbl_fab_log_reject_report(div_id){
        var reject_category = $('#fab_reject_filter').val();  
        var reject_cat_name = $('#fab_reject_filter option:selected').text();      
        var operation = 1;
        var year = $('#fab_yearpicker').val();
        var data = {
          operation: operation,
          reject_category : reject_category,
          reject_name: reject_cat_name,
          year: year
        }

        $.ajax({
          url:"/rejection_report",
          type:"GET",
          data: data,
          success:function(data){
            $(div_id).html(data);
          }
        });
      }

      function tbl_pain_log_reject_report(){
        var reject_category = $('#pain_reject_filter').val();  
        var reject_cat_name = $('#pain_reject_filter option:selected').text();      
        var operation = 2;
        var year = $('#pain_yearpicker').val();
        var data = {
          operation: operation,
          reject_category : reject_category,
          reject_name: reject_cat_name,
          year: year
        }

        $.ajax({
          url:"/rejection_report",
          type:"GET",
          data: data,
          success:function(data){
            $('#tbl_pain_log_reject_report').html(data);
          }
        });
      }

      function tbl_assem_log_reject_report(){
        var reject_category = $('#assem_reject_filter').val();  
        var reject_cat_name = $('#assem_reject_filter option:selected').text();      
        var operation = 3;
        var year = $('#assem_yearpicker').val();
        var data = {
          operation: operation,
          reject_category : reject_category,
          reject_name: reject_cat_name,
          year: year
        }
        $.ajax({
          url:"/rejection_report",
          type:"GET",
          data: data,
          success:function(data){
            $('#tbl_assem_log_reject_report').html(data);
          }
        });
      }
      
      function fab_optyStats(){
        var reject_category = $('#fab_reject_filter').val();  
        var reject_cat_name = $('#fab_reject_filter option:selected').text();      
        var operation = 1;
        var year = $('#fab_yearpicker').val();
        var data = {
          operation: operation,
          reject_category : reject_category,
          reject_name: reject_cat_name,
          year: year
        }

        $('#label_fab_chart_reject').text(reject_cat_name + ' Reject ('+ year +')');

        $.ajax({
          url: "/rejection_report_chart",
          method: "GET",
          data: data,
          success: function(data) {
            var reject = [];
            var val = [];
            var series=[];

            for(var i in data.year) {
              reject.push(data.year[i].reject);
              series.push(data.year[i].series);
              val.push(data.year[i].per_month);
            }

            var chartdata = {
              labels: series,
              data1:reject,
              datasets : [{
                data: val,
                backgroundColor: '#2874a6',
                borderColor: "#2874a6",
                borderWidth: 3,
                label: "Total Reject/s",
              }]
            };

            var ctx = $("#tbl_fab_reject_report_chart");
            if (window.fab_optyCtx != undefined) {
                window.fab_optyCtx.destroy();
            }

            window.fab_optyCtx = new Chart(ctx, {
              type: 'bar',
              data: chartdata,
              options: {
              tooltips: {
                callbacks: {
                  title: function (t, d) {
                    return d['data1'][t[0]['index']];
                  },
                },
              },
                responsive: true,
                legend: {
                  position: 'top',
                  labels:{
                    boxWidth: 11
                  }
                },
              }
            });
          },
          error: function(data) {
            alert('Error fetching data!');
          }
        });
      }

      function pain_optyStats(){
        var reject_category = $('#pain_reject_filter').val();
        var reject_cat_name = $('#pain_reject_filter option:selected').text();      
        var operation = 2;
        var year = $('#pain_yearpicker').val();
        var data = {
          operation: operation,
          reject_category : reject_category,
          reject_name: reject_cat_name,
          year: year
        }
        $('#label_pain_chart_reject').text(reject_cat_name + ' Reject ('+ year +')');
        $.ajax({
          url: "/rejection_report_chart",
          method: "GET",
          data: data,
          success: function(data) {
            var reject = [];
            var val = [];
            var series=[];
            
            for(var i in data.year) {
              reject.push(data.year[i].reject);
              series.push(data.year[i].series);
              val.push(data.year[i].per_month);
            }
            
            var chartdata = {
              labels: series,
              data1:reject,
              datasets : [{
                data: val,
                backgroundColor: '#2874a6',
                borderColor: "#2874a6",
                borderWidth: 3,
                label: "Total Reject/s",
              }]
            };
            
            var ctx = $("#tbl_pain_reject_report_chart");
            if (window.pain_optyCtx != undefined) {
              window.pain_optyCtx.destroy();
            }
            
            window.pain_optyCtx = new Chart(ctx, {
              type: 'bar',
              data: chartdata,
              options: {
                tooltips: {
                  callbacks: {
                    title: function (t, d) {
                      return d['data1'][t[0]['index']];
                    },
                  },
                },
                responsive: true,
                legend: {
                  position: 'top',
                  labels:{
                    boxWidth: 11
                  }
                },
              }
            });
          },
          error: function(data) {
            alert('Error fetching data!');
          }
        });
      }

      function assem_optyStats(){
        var reject_category = $('#assem_reject_filter').val();  
        var reject_cat_name = $('#assem_reject_filter option:selected').text();      
        var operation = 3;
        var year = $('#assem_yearpicker').val();

        var data = {
          operation: operation,
          reject_category : reject_category,
          reject_name: reject_cat_name,
          year: year
        }

        $('#label_assem_chart_reject').text(reject_cat_name + ' Reject ('+ year +')');

        $.ajax({
          url: "/rejection_report_chart",
          method: "GET",
          data: data,
          success: function(data) {
            var reject = [];
            var val = [];
            var series=[];
            
            for(var i in data.year) {
              reject.push(data.year[i].reject);
              series.push(data.year[i].series);
              val.push(data.year[i].per_month);
            }
            
            var chartdata = {
              labels: series,
              data1:reject,
              datasets : [{
                data: val,
                backgroundColor: '#2874a6',
                borderColor: "#2874a6",
                borderWidth: 3,
                label: "Total Reject/s",
              }]
            };
            
            var ctx = $("#tbl_assem_reject_report_chart");
            if (window.assem_optyCtx != undefined) {
              window.assem_optyCtx.destroy();
            }
            
            window.assem_optyCtx = new Chart(ctx, {
              type: 'bar',
              data: chartdata,
              options: {
                tooltips: {
                  callbacks: {
                    title: function (t, d) {
                      return d['data1'][t[0]['index']];
                    },
                  },
                },
                responsive: true,
                legend: {
                  position: 'top',
                  labels:{
                    boxWidth: 11
                  }
                },
              }
            });
          },
          error: function(data) {
            alert('Error fetching data!');
          }
        });
        
      }

      function tbl_fab_reject_rate_chart(){
        var reject_category = $('#fab_reject_filter').val();  
        var reject_cat_name = $('#fab_reject_filter option:selected').text();      
        var operation = 1
        var year = $('#fab_yearpicker').val();
        var data = {
          operation: operation,
          reject_category : reject_category,
          reject_name: reject_cat_name,
          year: year
        }

        $('#label_fab_rate_chart_reject').text('Reject Rate ('+ year +')');

        $.ajax({
          url: "/rejection_report_chart",
          method: "GET",
          data: data,
          success: function(data) {
            var numberWithCommas = function(x) {
              return x.substring(0,10) + '...';
            };

            var reject = [];
            var target = [];
            var planned =[];
            var rate =[];
            var series =[];

            for(var i in data.year) {
              rate.push(data.year[i].per_rate);
              planned.push(data.year[i].target);
              reject.push(data.year[i].reject);
              series.push(data.year[i].series);
            }
            
            var chartdata = {
              data1:reject,
              labels: series,
              datasets : [{
                data: rate,
                backgroundColor: '#3cba9f',
                borderColor: "#3cba9f",
                label: "Reject Rate",
                fill: false
              },
              {
                data: planned,
                backgroundColor: '#3e95cd',
                borderColor: "#3e95cd",
                label: "Target",
                fill: false
              }]
            };
            
            var ctx = $("#tbl_fab_rate_report_chart");

            if (window.tbl_chartCtx != undefined) {
                window.tbl_chartCtx.destroy();
            }

            window.tbl_chartCtx = new Chart(ctx, {
              type: 'line',
              data: chartdata,
              options: {
                tooltips: {
                  callbacks: {
                    title: function (t, d) {
                      return d['data1'][t[0]['index']];
                    },
                  },
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
                },
                scales: {         
                  xAxes: [
                    { 
                      ticks: {
                        maxRotation: 90,
                        callbacks: {
                          title: function (tooltipItems, data) {
                            return data.labels[tooltipItems[0].index]
                          }
                        },
                      }, 
                    }
                  ]
                }
              }
            });
          },
          error: function(data) {
            alert('Error fetching data!');
          }
        });
      }

      function tbl_pain_reject_rate_chart(){
        var reject_category = $('#pain_reject_filter').val();  
        var reject_cat_name = $('#pain_reject_filter option:selected').text();      
        var operation = 2;
        var year = $('#pain_yearpicker').val();
        var data = {
          operation: operation,
          reject_category : reject_category,
          reject_name: reject_cat_name,
          year: year
        }

        $('#label_pain_rate_chart_reject').text('Reject Rate ('+ year +')');
        
        $.ajax({
          url: "/rejection_report_chart",
          method: "GET",
          data: data,
          success: function(data) {
            var numberWithCommas = function(x) {
              return x.substring(0,10) + '...';
            };

            var reject = [];
            var target = [];
            var planned =[];
            var rate =[];
            var series =[];

            for(var i in data.year) {
              rate.push(data.year[i].per_rate);
              planned.push(data.year[i].target);
              reject.push(data.year[i].reject);
              series.push(data.year[i].series);
            }

            var chartdata = {
              data1:reject,
              labels: series,
              datasets : [{
                data: rate,
                backgroundColor: '#3cba9f',
                borderColor: "#3cba9f",
                label: "Reject Rate",
                fill: false
              },
              {
                data: planned,
                backgroundColor: '#3e95cd',
                borderColor: "#3e95cd",
                label: "Target",
                fill: false
              }]
            };
            
            var ctx = $("#tbl_pain_rate_report_chart");
            
            if (window.tbl_pain_chartCtx != undefined) {
              window.tbl_pain_chartCtx.destroy();
            }
            
            window.tbl_pain_chartCtx = new Chart(ctx, {
              type: 'line',
              data: chartdata,
              options: {
                tooltips: {
                  callbacks: {
                    title: function (t, d) {
                      return d['data1'][t[0]['index']];
                    },
                  },
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
                },
                scales: {         
                  xAxes: [
                    { 
                      ticks: {
                        maxRotation: 90,
                        callbacks: {
                          title: function (tooltipItems, data) {
                            return data.labels[tooltipItems[0].index]
                          }
                        },
                      }, 
                    }
                  ]
                }
              }
            });
          },
          error: function(data) {
            alert('Error fetching data!');
          }
        });
      }

      function tbl_assem_reject_rate_chart(){
        var reject_category = $('#assem_reject_filter').val();  
        var reject_cat_name = $('#assem_reject_filter option:selected').text();      
        var operation = 3;
        var year = $('#assem_yearpicker').val();

        var data = {
          operation: operation,
          reject_category : reject_category,
          reject_name: reject_cat_name,
          year: year
        }

        $('#label_assem_rate_chart_reject').text('Reject Rate ('+ year +')');
        
        $.ajax({
          url: "/rejection_report_chart",
          method: "GET",
          data: data,
          success: function(data) {
            var numberWithCommas = function(x) {
              return x.substring(0,10) + '...';
            };

            var reject = [];
            var target = [];
            var planned =[];
            var rate =[];
            var series =[];

            for(var i in data.year) {
              rate.push(data.year[i].per_rate);
              planned.push(data.year[i].target);
              reject.push(data.year[i].reject);
              series.push(data.year[i].series);
            }
        
            var chartdata = {
              data1:reject,
              labels: series,
              datasets : [{
                data: rate,
                backgroundColor: '#3cba9f',
                borderColor: "#3cba9f",
                label: "Reject Rate",
                fill: false
              },
              {
                data: planned,
                backgroundColor: '#3e95cd',
                borderColor: "#3e95cd",
                label: "Target",
                fill: false
              }]
            };
            
            var ctx = $("#tbl_assem_rate_report_chart");
            
            if (window.tbl_assem_chartCtx != undefined) {
              window.tbl_assem_chartCtx.destroy();
            }
            
            window.tbl_assem_chartCtx = new Chart(ctx, {
              type: 'line',
              data: chartdata,
              options: {
                tooltips: {
                  callbacks: {
                    title: function (t, d) {
                      return d['data1'][t[0]['index']];
                    },
                  },
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
                },
                scales: {         
                  xAxes: [
                    { 
                      ticks: {
                        maxRotation: 90,
                        callbacks: {
                          title: function (tooltipItems, data) {
                            return data.labels[tooltipItems[0].index]
                          }
                        },
                      }, 
                    }
                  ]
                }
              }
              
            });
          },
          error: function(data) {
            alert('Error fetching data!');
          }
        });
      }

      $(document).on('click', '#pain_tab_reject', function(event){
        tbl_pain_log_reject_report();
        pain_optyStats();
        tbl_pain_reject_rate_chart();
      });
      $(document).on('click', '#assem_tab_reject', function(event){
        tbl_assem_log_reject_report();
        assem_optyStats();
        tbl_assem_reject_rate_chart();
      });

      $(document).on('click', '#fab_tab_reject', function(event){
        tbl_fab_log_reject_report('#tbl_fab_log_reject_report');
        fab_optyStats();
        tbl_fab_reject_rate_chart();
      });

      var active_tab= $('#reject-dashboard-tabs .active').text();
      if(active_tab == "Fabrication"){
        $('#fab_tab_reject').trigger('click');
      }
    });
  </script>
  @endsection