<form action="/submit_quality_inspection" method="post" autocomplete="off" id="reject-confirmation-frm">
	@csrf
	<input type="hidden" name="inspection_type" value="Reject Confirmation">
	<input type="hidden" name="time_log_id" value="{{ $qa_details->reference_id }}">
	<input type="hidden" name="workstation" value="{{ $workstation_details->workstation_name }}">
	<input type="hidden" name="qa_id" value="{{ $qa_details->qa_id }}">
	<input type="hidden" name="inspected_by" value="{{ (Auth::user()) ? Auth::user()->user_id : '' }}">
	<input type="hidden" name="old_reject_qty" value="{{ $qa_details->rejected_qty }}">
	<div class="row">
		<div class="col-md-12">
			<table style="width: 100%;">
				<col style="width: 55%;">
				<col style="width: 20%;">
				<col style="width: 25%;">
				<tr>
					<td style="font-size: 12pt;"><b>{{ $production_order_details->production_order }}</b></td>
					<td class="text-right" style="font-size: 10pt;">Date Reported:</td>
					<td style="font-size: 10pt;"><b>{{ date('M-d-Y h:i A', strtotime($qa_details->created_at)) }}</b></td>
				</tr>
				<tr>
					<td style="font-size: 12pt;"><b>{{ $process_details->process_name }}</b></td>
					<td class="text-right" style="font-size: 10pt;">Operator Name:</td>
					<td style="font-size: 10pt;"><b>{{ $qa_details->created_by }}</b></td>
				</tr>
				<tr>
					<td colspan="3"><b>{{ $production_order_details->item_code }}</b> - {{ $production_order_details->description }}</td>
				</tr>
				<tr>
					<td colspan="3">Qty to Manufacture: <b>{{ $production_order_details->qty_to_manufacture }} {{ $production_order_details->stock_uom }}</b></td>
				</tr>
				<tr>
					<td colspan="3">Declared Reject: <b>{{ $qa_details->rejected_qty }} {{ $production_order_details->stock_uom }}</b></td>
				</tr>
			</table>
		</div>
		<div class="col-md-8 offset-md-2">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="font-weight-bold">Reject Type</label>
						<select class="form-control rounded" name="reject_list_id">
							@forelse($checklist as $r)
							<option value="{{ $r->reject_list_id }}" {{ ($reject_details->reject_list_id == $r->reject_list_id) ? 'selected' : '' }}>{{ $r->reject_reason }}</option>
							@empty
							<option value="">No Reject Type(s)</option>
							@endforelse
						</select>
					</div>
					<div class="form-group">
						<label class="font-weight-bold">Disposition</label>
						<select class="form-control rounded" name="qa_disposition">
							<option value="Use As Is">"Use As Is"</option>
							<option value="Replace">Replace</option>
							<option value="Rework">Rework</option>
							<option value="Scrap">Scrap</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="font-weight-bold">Confirmed Reject Qty</label>
						<input type="text" name="rejected_qty" class="form-control rounded" value="{{ $qa_details->rejected_qty }}">
					</div>
					<div class="form-group">
						<label class="font-weight-bold">Remarks</label>
						<textarea name="remarks" class="form-control rounded" style="border: 1px solid #dddddd;"></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12 text-right">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			<button type="submit" class="btn btn-primary">Confirm</button>
		</div>
	</div>
	</div>
</form>