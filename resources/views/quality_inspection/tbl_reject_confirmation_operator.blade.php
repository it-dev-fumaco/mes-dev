<form action="/submit_quality_inspection" method="post" autocomplete="off" id="reject-confirmation-frm">
	@csrf
	<div class="row">
		<input type="hidden" name="inspection_type" value="Reject Confirmation">
		<input type="hidden" name="time_log_id" value="{{ $qa_details->reference_id }}">
		<input type="hidden" name="workstation" value="{{ $workstation_details->workstation_name }}">
		<input type="hidden" name="qa_id" value="{{ $qa_details->qa_id }}">
		<input type="hidden" name="old_reject_qty" value="{{ $qa_details->rejected_qty }}">
		<div class="col-md-12">
			<ul class="nav nav-tabs" id="qi-tabs-1" role="tablist" style="display: none;">
				<li class="nav-item">
					<a class="nav-link active" data-toggle="tab" href="#tab1-random-inspection" role="tab" aria-controls="tab1" aria-selected="true">QC</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#tab2-random-inspection" role="tab" aria-controls="tab2" aria-selected="false">Confirm</a>
				</li>
			</ul>
			<div class="tab-content" style="min-height: 500px;" id="inspection-tabs">
				<div class="tab-pane active" id="tab1-random-inspection" role="tabpanel" aria-labelledby="first-tab">
					<div class="row" style="min-height: 420px;">
						<div class="col-md-6">
							<table style="width: 100%;">
								<tr>
									<td style="font-size: 12pt;"><b>{{ $production_order_details->production_order }}</b></td>
								</tr>
								<tr>
									<td style="font-size: 12pt;"><b>{{ $process_details->process_name }}</b></td>
								</tr>
								<tr>
									<td colspan="3"><b>{{ $production_order_details->item_code }}</b> - {{ $production_order_details->description }}</td>
								</tr>
								<tr>
									<td colspan="3"><br>Qty to Manufacture: <b>{{ $production_order_details->qty_to_manufacture }} {{ $production_order_details->stock_uom }}</b></td>
								</tr>
							</table>
							<br><br>
							<div class="form-group">
								<label class="font-weight-bold">Reject Type</label>
								<select class="form-control form-control-lg" name="reject_list_id">
									@forelse($checklist as $r)
									<option value="{{ $r->reject_list_id }}" {{ ($reject_details->reject_list_id == $r->reject_list_id) ? 'selected' : '' }}>{{ $r->reject_reason }}</option>
									@empty
									<option value="">No Reject Type(s)</option>
									@endforelse
								</select>
							</div>
							<div class="form-group">
								<label class="font-weight-bold">Disposition</label>
								<select class="form-control form-control-lg" name="qa_disposition">
									<option value="Use As Is">Use As Is</option>
									<option value="Replace">Replace</option>
									<option value="Rework">Rework</option>
									<option value="Scrap">Scrap</option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group text-center">
										<label for="reject-qty">Reject Qty</label>
										<input type="text" class="form-control form-control-lg" name="rejected_qty" id="rejected-qty" value="{{ $qa_details->rejected_qty }}" readonly required style="font-size: 20pt; text-align: center; font-weight: bolder;">
									</div>
								</div>
							</div>
							<div class="text-center numpad-div" id="rc-numpad">
								<div class="row1">
									<span class="numpad num">1</span>
									<span class="numpad num">2</span>
									<span class="numpad num">3</span>
								</div>
								<div class="row1">
									<span class="numpad num">4</span>
									<span class="numpad num">5</span>
									<span class="numpad num">6</span>
								</div>
								<div class="row1">
									<span class="numpad num">7</span>
									<span class="numpad num">8</span>
									<span class="numpad num">9</span>
								</div>
								<div class="row1">
									<span class="numpad del"><</span>
									<span class="numpad num">0</span>
									<span class="numpad clear">Clear</span>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<button type="button" class="btn btn-secondary btn-block btn-lg" data-dismiss="modal">Cancel</button>
						</div>
						<div class="col-md-6">
							<button type="button" class="btn btn-primary btn-block btn-lg next-tab">Next</button>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="tab2-random-inspection" role="tabpanel" aria-labelledby="second-tab">
					<div class="row" style="min-height: 420px;">
						<div class="col-md-6 offset-md-3 text-center" id="qc-enter-operator">
							<h5 class="text-center">Scan Authorized QC Employee ID</h5>
							<div class="form-group">
								<input type="password" class="form-control form-control-lg" name="inspected_by" readonly style="text-align: center; font-size: 30pt; font-weight: bolder;" id="inspected-by">
							</div>
							<button type="button" class="toggle-manual-input" style="margin-bottom: 10px;">Tap here for Manual Entry</button>
							<div class="text-center numpad-div manual" style="display: none;">
								<div class="row1">
									<span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '1';">1</span>
									<span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '2';">2</span>
									<span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '3';">3</span>
								</div>
								<div class="row1">
									<span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '4';">4</span>
									<span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '5';">5</span>
									<span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '6';">6</span>
								</div>
								<div class="row1">
									<span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '7';">7</span>
									<span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '8';">8</span>
									<span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '9';">9</span>
								</div>
								<div class="row1">
									<span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value.slice(0, -1);"><</span>
									<span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '0';">0</span>
									<span class="numpad" onclick="document.getElementById('inspected-by').value='';">Clear</span>
								</div>
							</div>
							<img src="{{ asset('img/tap.gif') }}" style="margin-top: -20px; width: 60%;" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<button type="button" class="btn btn-secondary btn-block btn-lg prev-tab">Previous</button>
						</div>
						<div class="col-md-6">
							<button type="submit" class="btn btn-info btn-block btn-lg" id="second-tab-btn2">Submit</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>