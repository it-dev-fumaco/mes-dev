<div class="row" style="margin-top: 10px; font-size: 9pt;">
	<div class="col-md-12">
		<table style="width: 100%;">
			<tr>
				<td style="width: 12%;" class="text-right align-top"><span style="padding-right: 10px;"><b>Item Code:</b></span></td>
				<td style="width: 50%;" class="text-justify"><b>{{ $bom_details->item }}</b> - {!! $bom_details->description !!}</td>
				<td style="width: 10%;" class="text-center align-top"><b>RFD No.:</b></td>
				<td style="width: 28%;" class="align-top">{{ $bom_details->rf_drawing_no }}</td>
			</tr>
			<tr>
				<td style="width: 12%;" class="text-right align-top"><span style="padding-right: 10px;"><b>Qty:</b></span></td>
				<td style="width: 50%;" class="text-justify"><b>{{ number_format($bom_details->quantity) }}</b></td>
			</tr>
		</table>
	</div>
</div>
<div class="row" style="margin-top: 10px;">
	<div class="col-md-6">
		<table class="table table-striped table-bordered" style="font-size: 9pt;">
			<thead class="text-primary">
				<th class="text-center"><b>No.</b></th>
				<th class="text-center"><b>Raw Material</b></th>
				<th class="text-center"><b>Qty</b></th>
			</thead>
			<tbody>
				@foreach($bom_materials as $rm)
				<tr>
					<td class="text-center">{{ $rm->idx }}</td>
					<td class="text-justify"><b>{{ $rm->item_code }}</b><br>{!! $rm->description !!}</td>
					<td class="text-center">{{ $rm->qty }} {{ $rm->uom }}</td>
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
			<tbody>
				<input type="hidden" name="bom_id" value="{{ $bom_details->name }}">
				<input type="hidden" name="username" value="{{ Auth::user()->employee_name }}">
				@foreach($bom_operations as $ops)
				<tr>
					<td class="text-center">{{ $ops->idx }}<span style="display: none;">{{ $ops->name }}</span></td>
					<td>{{ $ops->workstation }}</td>
					<td>
						<div class="form-group" style="margin: 0;">
							<select class="form-control form-control-lg">
								<option value="">Select Process</option>
								@foreach($workstation_process as $wprocess)
								@if($wprocess->workstation_name == $ops->workstation)
								<option value="{{ $wprocess->process_id }}" {{ ($wprocess->process_id == $ops->process) ? 'selected' : '' }} {{ ($wprocess->process_id == $ops->process) ? 'selected' : '' }}>{{ $wprocess->process_name }}</option>
								@endif
								@endforeach
							</select>
						</div>
					</td>
					<td class="td-actions text-center">
						<button type="button" rel="tooltip" class="btn btn-danger delete-row">
          				<i class="now-ui-icons ui-1_simple-remove"></i>
        				</button>
					</td>
				</tr>
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
<br>
<div class="row">
	<div class="col-md-4 offset-md-4">
		<button class="btn btn-block btn-primary btn-lg" id="submit-bom-review-btn">Update</button>
	</div>
</div>