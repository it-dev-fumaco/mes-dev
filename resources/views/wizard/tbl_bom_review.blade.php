<div class="row" style="margin-top: 10px; font-size: 10pt;">
	<div class="col-md-12">
		<table style="width: 100%;">
			<col style="width: 12%;">
			<col style="width: 50%;">
			<col style="width: 15%;">
			<col style="width: 23%;">
			<tr>
				<td class="text-right align-top">
					<span class="d-block pr-3 font-weight-bold">RFD No.:</span>
				</td>
				<td class="align-top">{{ ($bom_details->rf_drawing_no) ? $bom_details->rf_drawing_no : '--' }}</td>
				<td class="text-right align-top">
					<span class="d-block pr-3 font-weight-bold">Item Classification:</span>
				</td>
				<td class="align-top">{{ $bom_details->item_classification }}</td>
			</tr>
			<tr>
				<td class="text-right align-top">
					<span class="d-block pr-3 font-weight-bold">Item Code:</span>
				</td>
				<td class="text-justify align-top"><b>{{ $bom_details->item }}</b> - {!! $bom_details->description !!}</td>
				<td class="text-right align-top">
					<span class="d-block pr-3 font-weight-bold">Quantity:</span>
				</td>
				<td class="text-justify align-top"><b>{{ number_format($bom_details->quantity) }}</b> {{ $bom_details->uom }}</td>
			</tr>
		</table>
	</div>
</div>
<div class="row" style="margin-top: 10px;">
	@if (count($items_with_different_uom) > 0)
	<style>
		.text-blink-alert {color: #161616d3;
			animation: blinker-alert 2.1s linear infinite;
		}
	
		@keyframes blinker-alert {  
			0%    { background-color: #ffffff;}
			25%   { background-color: #FFC107;}
			50%   { background-color: #ffffff;}
			75%   { background-color: #FFC107;}
			100%  { background-color: #ffffff;}
		}
	</style>
	<div class="col-md-12">
		<div class="alert alert-warning text-center mt-1 mb-2 text-blink-alert" id="manual-prod-note" role="alert">
			<div class="container">
				<strong>Warning:</strong> Please update <span class="font-weight-bold">{{ implode(', ', $items_with_different_uom) }}</span> stock uom and quantity for this BOM in ERP.
			</div>
		</div>
	</div>
	@endif
	@if ($duplicates)
		<div class="col-md-12">
			<div class="alert alert-warning text-center mt-1 mb-2 text-blink-alert" id="manual-prod-note" role="alert">
				<div class="container" style="color: #000;">
					<strong>Warning:</strong> Duplicate item codes found. Please update BOM in ERP.
				</div>
			</div>
		</div>
	@endif
	<div class="col-md-6">
		<table class="table table-striped table-bordered" style="font-size: 9pt;">
			<thead class="text-primary">
				<th class="text-center"><b>No.</b></th>
				<th class="text-center"><b>Raw Material</b></th>
				<th class="text-center"><b>Qty</b></th>
			</thead>
			<tbody>
				@foreach($bom_materials as $rm)
				<tr class="{{ in_array($rm['item_code'], $duplicates) ? 'bg-warning' : null }}">
					<td class="text-center">{{ $rm['idx'] }}</td>
					<td class="text-justify"><b>{{ $rm['item_code'] }}</b><br>{!! $rm['description'] !!}</td>
					<td class="text-center">
						<span class="d-block font-weight-bold">{{ $rm['qty'] * 1 }}</span>
						<span>{{ $rm['uom'] }}</span>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	<div class="col-md-6">
		<form id="bom-review-frm">
		@csrf
		<table class="table table-striped table-bordered" id="bom-workstations-tbl" style=" font-size: 9pt;">
			<thead class="text-primary">
				<th class="text-center" style="width: 10%;"><b>No.</b></th>
				<th class="text-center" style="width: 40%;"><b>Workstation</b></th>
				<th class="text-center" style="width: 40%;"><b>Process</b></th>
				<th class="text-center" style="width: 10%;"><b>Action</b></th>
			</thead>
			<input type="hidden" name="bom_id" value="{{ $bom_details->name }}">
			<input type="hidden" name="username" value="{{ Auth::user()->employee_name }}">

			<tbody class="sortable-operation-list">
				@foreach($bom_operations as $index => $ops)
				@if($ops->workstation != 'Painting')
				<tr>
					<td class="text-center">{{ $ops->idx }}</td>
					<td>{{ $ops->workstation }}</td>
					<td>
						<div class="form-group" style="margin: 0;">
							<select class="form-control form-control-lg">
								<option value="">Select Process</option>
								@foreach($workstation_process as $wprocess)
								@if($wprocess->workstation_name == $ops->workstation)
								<option value="{{ $wprocess->process_id }}" {{ ($wprocess->process_id == $ops->process) ? 'selected' : '' }}>{{ $wprocess->process_name }}</option>
								@endif
								@endforeach
							</select>
						</div>
					</td>
					<td class="td-actions text-center">
						<span style="display: none;">{{ $ops->name }}</span>
						<button type="button" rel="tooltip" class="btn btn-danger delete-row">
          				<i class="now-ui-icons ui-1_simple-remove"></i>
        				</button>
					</td>
				</tr>
				@else
				<tr>
					<td class="text-center">{{ $ops->idx }}</td>
					<td>{{ $ops->workstation }}</td>
					<td>
						<div class="form-group" style="margin: 0;">
							<select class="form-control form-control-lg">
								<!-- <option value="">Select Process</option> -->
								<option value="0" selected>Painting</option>
							</select>
						</div>
					</td>
					<td class="td-actions text-center">
						<span style="display: none;">{{ $ops->name }}</span>
						<button type="button" rel="tooltip" class="btn btn-danger delete-row">
          				<i class="now-ui-icons ui-1_simple-remove"></i>
        				</button>
					</td>
				</tr>
				@endif
				@endforeach
			</tbody>
		</table>
		</form>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group" style="margin: 0;">
					<select class="form-control form-control-lg" id="sel-workstation">
						<option value="">Select Workstation</option>
						@foreach($workstations as $station)
						<option value="{{ $station->workstation_id }}">{{ $station->workstation_name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="col-md-4" style="display: none;">
				<div class="form-group" style="margin: 0;">
					<select class="form-control form-control-lg" id="sel-process">
						<option value="">Select Process</option>
					</select>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group" style="margin: 0;">
					<button class="btn btn-block" id="add-operation-btn" style="margin: 0;">Add Operation</button>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row mt-3">
	<div class="col-md-4 offset-md-4">
		@php
			$disabled_btn = (count($items_with_different_uom) > 0) ? 'disabled' : null;
			$disabled_btn = $duplicates ? 'disabled' : $disabled_btn;
		@endphp
		<button class="btn btn-block btn-primary btn-lg" id="submit-bom-review-btn" data-id="bom{{ $bom_details->name }}" {{ $disabled_btn }}>Update</button>
	</div>
</div>

<div class="container deleteTable">
	<div class="accordion mb-0" id="accordionExample">
		<div class="card">
			<div class="" id="headingOne">
				<button class="btn btn-sm btn-link btn-block text-left c-btn" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
					<b>Operator Logs</b>
				</button>
			</div>
		
			<div id="collapseOne" class="collapse mb-0" aria-labelledby="headingOne" data-parent="#accordionExample">
				<div>
					<div class="table-responsive p-1">
						<table style="width: 100%; border-color: #D5D8DC;">
							<col style="width: 15%;">
							<col style="width: 15%;">
							<col style="width: 7%;">
							<col style="width: 7%;">
							<col style="width: 10%;">
							<col style="width: 12%;">
							<col style="width: 12%;">
							<col style="width: 12%;">
							<col style="width: 10%;">
							<thead style="font-size: 10pt;">
								<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>WORKSTATION</b></td>
								<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PROCESS</b></td>
								<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>GOOD</b></td>
								<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>REJECT</b></td>
								<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>MACHINE</b></td>
								<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>START</b></td>
								<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>END</b></td>
								<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>OPERATOR</b></td>
								<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>ACTION</b></td>
							</thead>
							<tbody style="font-size: 9pt;">
								@foreach ($operation_list as $b)
								@php
								if($b['workstation'] == "Spotwelding"){
									$spotclass= "spotclass";
									$icon = '<span style="font-size:15px;">&nbsp; >></span>';
								}else{
									$spotclass= "";
									$icon="";
								}
								@endphp
								
								<tr>
									<td class="text-center" style="border: 1px solid #ABB2B9;" rowspan="{{ $b['count'] }}">
										<span class="{{ $spotclass }}" data-jobticket="{{ $b['job_ticket'] }}" data-prodno="{{ $b['production_order'] }}">
											<b>{{ $b['workstation'] }} {!! $icon !!}</b>
										</span>
										<br><span style="font-size:9px;"><i>{{ $b['cycle_time'] }}</i></span>
									</td>
									@if (count($b['operations']) > 0)
											<td class="text-center" style="border: 1px solid #ABB2B9;" rowspan="{{ $b['count'] }}">
												<span class="{{ $spotclass }}" data-jobticket="{{ $b['job_ticket'] }}" data-prodno="{{ $b['production_order'] }}">
													<b>{{ $b['process'] }}</b>
												</span>
												<br><span style="font-size:11px;"><b><i>{{ $b['count_good'] }}</i><b></span>
											</td>
										@foreach($b['operations'] as $c)
											@php
												$machine = ($c['machine_code']) ? $c['machine_code'] : '-';
												$operator_name = ($c['operator_name']) ? $c['operator_name'] : '-';
												$from_time = ($c['from_time']) ? $c['from_time'] : '-';
												$to_time = ($c['to_time']) ? $c['to_time'] : '-';
												$inprogress_class = ($c['status'] == 'In Progress') ? 'active-process' : '';
												// if($b['process'] == "Housing and Frame Welding"){
												// 	$qc_status = '';
												// }else{
												// 	$qc_status = ($c['qa_inspection_status'] == 'QC Passed') ? "qc_passed" : "qc_failed";
												// 	$qc_status = ($c['qa_inspection_status'] == 'Pending') ? '' : $qc_status;
												// }
											@endphp
											{{-- <td class="text-center {{ $inprogress_class }} {{ $qc_status }}" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>{{ number_format($c['good']) }}</b></td> --}}
											<td class="text-center {{ $inprogress_class }}" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>{{ number_format($c['good']) }}</b></td>
											<td class="text-center {{ $inprogress_class }}" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>{{ number_format($c['reject']) }}</b></td>
											<td class="text-center {{ $inprogress_class }}" style="border: 1px solid #ABB2B9;">{{ $machine }}</td>
											<td class="text-center {{ $inprogress_class }}" style="border: 1px solid #ABB2B9;">{{ $from_time }}</td>
											<td class="text-center {{ $inprogress_class }}" style="border: 1px solid #ABB2B9;">{{ $to_time }}</td>
											<td class="text-center {{ $inprogress_class }}" style="border: 1px solid #ABB2B9;">
												<span class="hvrlink-plan">{{ $operator_name }}</span>
												@if($b['workstation'] != "Spotwelding")
												<div class="hover-box text-center">
													@if (count($c['helpers']) > 0)
													<label class="font-weight-bold mb-1">HELPER(S)</label>
													@foreach ($c['helpers'] as $helper)
													<span class="d-block">{{ $helper }}</span>
													@endforeach
													@else
													{{-- <label class="font-weight-bold m-0">NO HELPER(S)</label> --}}
													@endif
												</div>
												@endif
											</td>
											<td class="text-center {{ $inprogress_class }}" style="border: 1px solid #ABB2B9;">
												<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal-{{ $b['production_order'] }}">
													Delete
												</button>
												
												<div class="modal fade deleteModal" id="deleteModal-{{ $b['production_order'] }}" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
													<div class="modal-dialog">
													<div class="modal-content">
														<div class="modal-header">
														<h5 class="modal-title" id="deleteModalLabel">Delete <b>{{ $b['workstation'] }}</b> Task for <b>{{ $operator_name }}</b>?</h5>
														<button type="button" class="close" data-dismiss="modal" aria-label="Close">
															<span aria-hidden="true">&times;</span>
														</button>
														</div>
														<div class="modal-body">
															<form action="/log_delete" method="POST">
																@csrf
																<div class="d-none">
																	<input class="prod_no" type="text" name="prod_order" value="{{ $b['production_order'] }}" readonly required/>
																	<input type="text" name="jtid" value="{{ $b['job_ticket'] }}" readonly required/>
																	<input type="text" name="workstation" value="{{ $b['workstation'] }}" readonly required/>
																	<input type="text" name="tbl_good" value="{{ $c['good'] }}" readonly required/>
																	<input type="text" name="tbl_reject" value="{{ $c['reject'] }}" readonly required/>
																	<input type="text" name="machine" value="{{ $machine }}" readonly required/>
																	<input type="text" name="from_time" value="{{ $from_time }}" readonly required/>
																	<input type="text" name="to_time" value="{{ $to_time }}" readonly required/>
																	<input type="text" name="process" value="{{ $b['process'] }}" readonly required/>
																	<input type="text" name="operator" value="{{ $operator_name }}" readonly required/>
																</div>
																<button type="submit" class="btn btn-danger">Delete</button>
																<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
															</form>
														</div>
													</div>
													</div>
												</div>
											</td>
										</tr>
										@endforeach
									@else
										<td class="text-center" style="border: 1px solid #ABB2B9;"><b>{{ $b['process'] }}</b></td>
										<td class="text-center" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>0</b></td>
										<td class="text-center" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>0</b></td>
										<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>
										<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>
										<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>
										<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>
										<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>
									</tr>
									@endif
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	
</div>
@if (\Session::has('success'))	
	<span>Task Updated</span>
@endif
<style>
	.deleteModal{
		background-color: rgba(0,0,0,.8);
	}

	.c-btn:hover{
		box-shadow: none !important;
	}
</style>
<script>
	$( function() {
		$( ".sortable-operation-list" ).sortable().disableSelection();
	  } );

	$(document).ready(function() {
		$(".deleteTable").{{ $tbl_display }}();
	});

</script>