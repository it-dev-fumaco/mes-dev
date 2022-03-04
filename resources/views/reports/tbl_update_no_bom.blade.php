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
				<td class="align-top">--</td>
				<td class="text-right align-top">
					<span class="d-block pr-3 font-weight-bold">Item Classification:</span>
				</td>
				<td class="align-top">{{ $details->item_classification }}</td>
			</tr>
			<tr>
				<td class="text-right align-top">
					<span class="d-block pr-3 font-weight-bold">Item Code:</span>
				</td>
				<td class="text-justify align-top"><b>{{ $details->item_code }}</b> - {!! $details->description !!}</td>
				<td class="text-right align-top">
					<span class="d-block pr-3 font-weight-bold">Quantity:</span>
				</td>
				<td class="text-justify align-top"><b>{{ number_format($details->qty_to_manufacture) }}</b> {{ $details->stock_uom }}</td>
			</tr>
		</table>
	</div>
</div>
<div class="row" style="margin-top: 10px;">
	<div class="col-md-6 offset-md-3">
		<form id="bom-review-frm">
		@csrf
		<table class="table table-striped table-bordered" id="bom-workstations-tbl" style="font-size: 9pt;">
			<thead class="text-primary">
				<th class="text-center" style="width: 10%;"><b>No.</b></th>
				<th class="text-center" style="width: 40%;"><b>Workstation</b></th>
				<th class="text-center" style="width: 40%;"><b>Process</b></th>
				<th class="text-center" style="width: 10%;"><b>Action</b></th>
			</thead>
			<input type="hidden" name="bom_id" value="no_bom">
			<input type="hidden" name="username" value="{{ Auth::user()->employee_name }}">
			<input type="hidden" name="operation" value="{{ $details->operation_id }}">
			<tbody class="sortable-operation-list">
				@foreach($existing_workstation as $index => $ops)
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
								<option value="{{ $wprocess->process_id }}" {{ ($wprocess->process_id == $ops->process_id) ? 'selected' : '' }}>{{ $wprocess->process_name }}</option>
								@endif
								@endforeach
							</select>
						</div>
					</td>
					<td class="td-actions text-center">
						<span style="display: none;">{{ $ops->bom_operation_id }}</span>
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
						<span style="display: none;">{{ $ops->bom_operation_id }}</span>
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
<br><br>
<div class="row">
	<div class="col-md-4 offset-md-4">
		<button class="btn btn-block btn-primary btn-lg" id="submit-bom-review-btn" data-id="{{ $details->production_order}}">Update</button>
	</div>
</div>

<script>
	$( function() {
		$( ".sortable-operation-list" ).sortable().disableSelection();
	  } );
</script>