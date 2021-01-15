@php
	$tab1 = $tab2 = $tab3 = null;
	if(count($components) > 0){
		$tab1 = 'active';
	}

	if($tab1 != 'active'){
		if(count($parts) > 0){
			$tab2 = 'active';
		}
	}

	if($tab1 != 'active' && $tab2 != 'active'){
		if(count($items_return) > 0){
			$tab3 = 'active';
		}
	}
@endphp
<table style="width: 100%; border-collapse: collapse;" class="custom-table-1-1">
	<col style="width: 10%;">
	<col style="width: 10%;">
	<col style="width: 20%;">
	<col style="width: 20%;">
	<col style="width: 10%;">
	<col style="width: 10%;">
	<col style="width: 10%;">
	<col style="width: 10%;">
	<tr class="text-center">
		<th>Prod. Order</th>
		<th>Reference No.</th>
		<th>Customer</th>
		<th>Project</th>
		<th>Delivery Date</th>
		<td style="background-color: #28b463;" class="text-white">For Manufacture</td>
		<td style="background-color: #28b463;" class="text-white">Produced</td>
		<td style="background-color: #28b463;" class="text-white">Feedbacked</td>
	</tr>
	<tr class="text-center" style="font-size: 10pt;">
		<td>
			<a href="#" class="font-weight-bold view-production-order-details" data-production-order="{{ $details->production_order }}" style="color: black;">{{ $details->production_order }}</a>
		</td>
		<td><b>{{ $details->sales_order }}{{ $details->material_request }}</b></td>
		<td>{{ $details->customer }}</td>
		<td>{{ $details->project }}</td>
		<td>{{ ($details->rescheduled_delivery_date == null)? $details->delivery_date: $details->rescheduled_delivery_date   }}</td>
		<td style="background-color: #263238;" class="text-white">
			<span class="d-block font-weight-bold" style="font-size: 12pt;">{{ $details->qty_to_manufacture }}</span>
			<span class="d-block" style="font-size: 8pt;">{{ $details->stock_uom }}</span>
		</td>
		<td style="background-color: #263238;" class="text-white">
			<span class="d-block font-weight-bold" style="font-size: 12pt;">{{ $details->produced_qty }}</span>
			<span class="d-block" style="font-size: 8pt;">{{ $details->stock_uom }}</span>
		</td>
		<td style="background-color: #263238;" class="text-white">
			<span class="d-block font-weight-bold" style="font-size: 12pt;">{{ $details->feedback_qty }}</span>
			<span class="d-block" style="font-size: 8pt;">{{ $details->stock_uom }}</span>
		</td>
	</tr>
	<tr style="font-size: 10pt;">
		<td colspan="6">
			<div class="d-flex flex-row">
				@if ($details->parent_item_code != $details->item_code)
					<div class="p-2 align-self-center text-center" style="width: 15%;font-size: 12pt;">
						<span class="font-weight-bold">{{ $details->parent_item_code }}</span>
						
						<span class="d-block font-italic" style="font-size: 8pt;">Parent Item Code</span>
					</div>
				@endif
				<div class="p-2">
					<span class="font-weight-bold">{{ $details->item_code }}</span> - {{ $details->description }}
				</div>
			</div>
		</td>
		<td class="text-center" colspan="2">
			@if($issued_qty > 0)
			<button class="btn btn-primary m-1 submit-ste-btn p-3" data-production-order="{{ $details->production_order }}">Submit Withdrawal Slip</button>
			@elseif(collect($required_items)->sum('required_qty') <= collect($required_items)->sum('transferred_qty') && collect($required_items)->sum('transferred_qty') > 0)
			<button class="btn btn-success m-1 p-3">Withdrawal Slip Submitted</button>
			@else
			<button class="btn btn-primary m-1 generate-ste-btn p-3" data-production-order="{{ $details->production_order }}">Create Withdrawal Slip</button>
			@endif
		</td>
	</tr>
</table>

@if(count($required_items) <= 0)
   <h5 class="text-center m-4">No withdrawal slip(s) created.</h5>
@else
	<ul class="nav nav-tabs mt-2 font-weight-bold" role="tablist">
		@if(count($components) > 0)
		<li class="nav-item">
			<a class="nav-link {{ $tab1 }}" data-toggle="tab" href="#w1" role="tab" aria-controls="home" aria-selected="true">
				<span class="badge badge-info mr-2">{{ count($components) }}</span> Component(s) 
			</a>
		</li>
		@endif
		@if(count($parts) > 0)
		<li class="nav-item">
			<a class="nav-link {{ $tab2 }}"  data-toggle="tab" href="#w2" role="tab" aria-controls="profile" aria-selected="false">
				<span class="badge badge-info mr-2">{{ count($parts) }}</span>	Part(s) 
			</a>
		</li>
		@endif
		@if(count($items_return) > 0)
		<li class="nav-item">
			<a class="nav-link {{ $tab3 }}" data-toggle="tab" href="#w3" role="tab" aria-controls="messages" aria-selected="false">
				<span class="badge badge-info mr-2">{{ count($items_return) }}</span> Item Return(s)
			</a> 
		</li>
		@endif
		@if(count($feedbacked_logs) > 0)
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#w4" role="tab" aria-controls="messages" aria-selected="false">
				<span class="badge badge-info mr-2">{{ count($feedbacked_logs) }}</span> Feedbacked Log(s)
			</a> 
		</li>
		@endif
	</ul>

	<div class="tab-content bg-light" style="border: 1px solid #f2f3f4;">
		<div class="tab-pane {{ $tab1 }}" id="w1" role="tabpanel" aria-labelledby="w1-tab">
			@if(count($components) > 0)
			<table style="width: 100%; border-collapse: collapse; margin-top: 10px;" class="custom-table-1-1" border="1">
				<col style="width: 3%;">
				<col style="width: 8%;">
				<col style="width: 26%;">
				<col style="width: 13%;">
				<col style="width: 13%;">
				<col style="width: 9%;">
				<col style="width: 10%;">
				<col style="width: 8%;">
				<col style="width: 10%;">
				<tr class="text-center">
					<th>No.</th>
					<th colspan="2">Item Code</th>
					<th>Source Warehouse</th>
					<th>WIP Warehouse</th>
					<th>Required</th>
					<th>Transferred / Issued</th>
					<th>Item Status</th>
					<th>Action</th>
				</tr>
				@foreach ($components as $i => $component)
				<tr>
					<td class="text-center">{{ $i + 1 }}</td>
					<td class="text-center">
						@php
							$img = ($component['item_image']) ? "/img/" . $component['item_image'] : "/icon/no_img.png";

							$balance = $component['required_qty'] - $component['transferred_qty'];
							$swhb = ($component['actual_qty'] < $balance) ? "badge badge-danger" : "badge badge-success";
							$wwhb = ($component['available_qty_at_wip'] < $component['transferred_qty'] || $component['available_qty_at_wip'] <= 0) ? "badge badge-danger" : "badge badge-success";

							if($component['transferred_qty'] == $component['required_qty']){
								$twhb = "badge badge-success";
							}elseif($component['transferred_qty'] <= 0){
								$twhb = "badge badge-danger";
							}else{
								$twhb = "badge badge-warning";
							}

							$item_status_badge = ($component['item_status'] == 'For Checking') ? 'badge-warning' : 'badge-success';
						@endphp
						<a href="http://athenaerp.fumaco.local/storage/{{ $img }}" data-toggle="lightbox">
							<img src="http://athenaerp.fumaco.local/storage/{{ $img }}" class="img-thumbnail" width="100">
						</a>
					</td>
					<td class="text-justify">
						<span class="item-name d-none">{{ $component['item_name'] }}</span>
						<span class="d-block font-weight-bold item-code">{{ $component['item_code'] }}</span>
						<span class="d-block item-description" style="font-size: 8pt;">{!! $component['description'] !!}</span>
					</td>
					<td class="text-center">
						<span class="d-block source-warehouse" style="font-size: 9pt;">{{ $component['source_warehouse'] }}</span>
						<span class="font-weight-bold {{ $swhb }}" style="font-size: 9pt;">Current Qty: {{ $component['actual_qty'] * 1 }}</span>
					</td>
					<td class="text-center" style="font-size: 9pt;">
						<span class="d-block target-warehouse" style="font-size: 9pt;">{{ $details->wip_warehouse }}</span>
						<span class="font-weight-bold {{ $wwhb }}" style="font-size: 9pt;">Current Qty: {{ $component['available_qty_at_wip'] * 1 }}</span>
					</td>
					<td class="text-center">
						<span class="d-block font-weight-bold required-qty" style="font-size: 10pt;">{{ $component['required_qty'] * 1 }}</span>
						<span class="d-block" style="font-size: 8pt;">{{ $component['stock_uom'] }}</span>
					</td>
					<td class="text-center">
						<span class="font-weight-bold qty {{ $twhb }}" style="font-size: 10pt;">{{ $component['transferred_qty'] * 1 }}</span>
						<span class="d-block stock-uom" style="font-size: 8pt;">{{ $component['stock_uom'] }}</span>
					</td>
					<td class="text-center">
						<span class="badge {{ $item_status_badge }} hvrlink" style="font-size: 9pt;">{{ $component['item_status'] }}</span>
						<div class="details-pane" style="font-size:8pt;">
							<table border="1" style="width: 100%;">
								<tr>
									<th class="text-center">Ref. No.</th>
									<th class="text-center">Date Issued</th>
									<th class="text-center">Issued by</th>
								</tr>
								@forelse ($component['references'] as $ref)
								<tr>
									<td class="text-center">{{ $ref->name }} ({{ $ref->qty * 1 }})</td>
									<td class="text-center">{{ Carbon\Carbon::parse($ref->date_modified)->format('M-d-Y H:i:A') }}</td>
									<td class="text-center">{{ $ref->session_user }}</td>
								</tr>
								@empty
								<tr>
									<td colspan="3" class="text-center font-weight-bold">No reference stock entry</td>
								</tr>
								@endforelse
							</table>
						</div>
					</td>
					<td class="text-center">
						@php
							$change_cancel_btn = (!$component['has_pending_ste_for_issue']) ? 'disabled' : null;
							$return_btn = ($component['transferred_qty'] > 0) ? '' : 'disabled';
						@endphp
						<button type="button" class="btn btn-info  btn-sm p-1 change-required-item-btn" data-production-order="{{ $details->production_order }}" data-item-classification="{{ $component['item_classification'] }}" data-production-order-item-id="{{ $component['name'] }}" {{ $change_cancel_btn }}> 
								<i class="now-ui-icons ui-2_settings-90 d-block"></i><span style="font-size: 7pt;">Change</span>
						</button>
						<button type="button" class="btn btn-secondary btn-sm p-1 return-required-item-btn" data-production-order="{{ $details->production_order }}" data-production-order-item-id="{{ $component['name'] }}" {{ $return_btn }}>
							<i class="now-ui-icons loader_refresh d-block"></i><span style="font-size: 7pt;">Return</span>
						</button>
						<button type="button" class="btn btn-danger  btn-sm p-1 delete-required-item-btn" data-production-order="{{ $details->production_order }}" {{ $change_cancel_btn }}>
							<i class="now-ui-icons ui-1_simple-remove d-block"></i><span style="font-size: 7pt;">Cancel</span>
						</button>
					</td>
				</tr>
				@endforeach
			</table>
			@else
			<h5 class="text-center m-4">No withdrawal slip(s) created for item component(s).</h5>
			@endif
		</div>
		<div class="tab-pane {{ $tab2 }}" id="w2" role="tabpanel" aria-labelledby="w2-tab">
			@if(count($parts) > 0)
			<table style="width: 100%; border-collapse: collapse; margin-top: 10px;" class="custom-table-1-1">
				<col style="width: 3%;">
				<col style="width: 8%;">
				<col style="width: 27%;">
				<col style="width: 13%;">
				<col style="width: 13%;">
				<col style="width: 9%;">
				<col style="width: 9%;">
				<col style="width: 8%;">
				<col style="width: 10%;">
				<tr class="text-center">
					<th>No.</th>
					<th>Prod. Order</th>
					<th>Item Description</th>
					<th>Source Warehouse</th>
					<th>WIP Warehouse</th>
					<th>Required</th>
					<th>Transferred / Issued</th>
					<th>Item Status</th>
					<th>Action</th>
				</tr>
				@foreach ($parts as $i => $part)
				@php
					$balance = $part['required_qty'] - $part['transferred_qty'];
					$swhb = ($part['actual_qty'] < $balance) ? "badge badge-danger" : "badge badge-success";
					$wwhb = ($part['available_qty_at_wip'] < $part['transferred_qty'] || $part['available_qty_at_wip'] <= 0) ? "badge badge-danger" : "badge badge-success";

					if($part['transferred_qty'] == $part['required_qty']){
						$twhb = "badge badge-success";
					}elseif($part['transferred_qty'] <= 0){
						$twhb = "badge badge-danger";
					}else{
						$twhb = "badge badge-warning";
					}

					$stat_badge = 'badge badge-secondary';
					if($part['status'] == 'Completed'){
						$stat_badge = 'badge badge-success';
					}elseif($part['status'] == 'In Progress'){
						$stat_badge = 'badge badge-warning';
					}elseif($part['status'] == 'Cancelled'){
						$stat_badge = 'badge badge-danger';
					}else{
						$stat_badge = 'badge badge-secondary';
					}

					$item_status_badge = ($part['item_status'] == 'For Checking') ? 'badge-warning' : 'badge-success';
				@endphp
				<tr>
					<td class="text-center">{{ $i + 1 }}</td>
					<td class="text-center">
						@if ($part['production_order'])
						<span class="d-block font-weight-bold view-production-order-details" data-production-order="{{ $part['production_order'] }}" style="color: black; cursor: pointer;">{{ $part['production_order'] }}</span>
						@else
						--
						@endif
						<span class="{{ $stat_badge }}" style="font-size: 9pt;">{{ $part['status'] }}</span>
					</td>
					<td class="text-justify">
						<span class="item-name d-none">{{ $part['item_name'] }}</span>
						<span class="d-block font-weight-bold item-code">{{ $part['item_code'] }}</span>
						<span class="d-block item-description" style="font-size: 8pt;">{!! $part['description'] !!}</span>
					</td>
					<td class="text-center">
						<span class="d-block source-warehouse" style="font-size: 9pt;">{{ $part['source_warehouse'] }}</span>
						<span class="font-weight-bold {{ $swhb }}" style="font-size: 9pt;">Current Qty: {{ $part['actual_qty'] * 1 }}</span>
					</td>
					<td class="text-center" style="font-size: 9pt;">
						<span class="d-block target-warehouse" style="font-size: 9pt;">{{ $details->wip_warehouse }}</span>
						<span class="font-weight-bold {{ $wwhb }}" style="font-size: 9pt;">Current Qty: {{ $part['available_qty_at_wip'] * 1 }}</span>
					</td>
					<td class="text-center">
						<span class="d-block font-weight-bold required-qty" style="font-size: 10pt;">{{ $part['required_qty'] * 1 }}</span>
						<span class="d-block stock-uom" style="font-size: 8pt;">{{ $part['stock_uom'] }}</span>
					</td>
					<td class="text-center">
						<span class="font-weight-bold qty {{ $twhb }}" style="font-size: 10pt;">{{ $part['transferred_qty'] * 1 }}</span>
						<span class="d-block" style="font-size: 8pt;">{{ $part['stock_uom'] }}</span>
					</td>
					<td class="text-center">
						<span class="badge {{ $item_status_badge }} hvrlink" style="font-size: 9pt;">{{ $part['item_status'] }}</span>
						<div class="details-pane" style="font-size:8pt;">
							<table border="1" style="width: 100%;">
								<tr>
									<th class="text-center">Ref. No.</th>
									<th class="text-center">Date Issued</th>
									<th class="text-center">Issued by</th>
								</tr>
								@forelse ($part['references'] as $ref)
								<tr>
									<td class="text-center">{{ $ref->name }} ({{ $ref->qty * 1 }})</td>
									<td class="text-center">{{ Carbon\Carbon::parse($ref->date_modified)->format('M-d-Y H:i:A') }}</td>
									<td class="text-center">{{ $ref->session_user }}</td>
								</tr>
								@empty
								<tr>
									<td colspan="3" class="text-center font-weight-bold">No reference stock entry</td>
								</tr>
								@endforelse
							</table>
						</div>
					</td>
					<td class="text-center">
						@php
							$change_cancel_btn = (!$part['has_pending_ste_for_issue']) ? 'disabled' : null;
							$return_btn = ($part['transferred_qty'] > 0) ? '' : 'disabled';
						@endphp
						<button type="button" class="btn btn-info  btn-sm p-1 change-required-item-btn" data-production-order="{{ $details->production_order }}" data-item-classification="{{ $part['item_classification'] }}" data-production-order-item-id="{{ $part['name'] }}" {{ $change_cancel_btn }}> 
							<i class="now-ui-icons ui-2_settings-90 d-block"></i><span style="font-size: 7pt;">Change</span>
						</button>
						<button type="button" class="btn btn-secondary btn-sm p-1 return-required-item-btn" data-production-order="{{ $details->production_order }}" data-production-order-item-id="{{ $part['name'] }}" {{ $return_btn }}>
							<i class="now-ui-icons loader_refresh d-block"></i><span style="font-size: 7pt;">Return</span>
						</button>
						<button type="button" class="btn btn-danger  btn-sm p-1 delete-required-item-btn" data-production-order="{{ $details->production_order }}" {{ $change_cancel_btn }}>
							<i class="now-ui-icons ui-1_simple-remove d-block"></i><span style="font-size: 7pt;">Cancel</span>
						</button>
					</td>
				</tr>
				@endforeach
			</table>
			@else
			<h5 class="text-center m-4">No withdrawal slip(s) created for item component(s).</h5>
			@endif
		</div>
		<div class="tab-pane {{ $tab3 }}" id="w3" role="tabpanel" aria-labelledby="w3-tab">
			@if(count($items_return) > 0)
			<table style="width: 100%; border-collapse: collapse; margin-top: 10px;" class="custom-table-1-1">
				<col style="width: 5%;">
				<col style="width: 8%;">
				<col style="width: 40%;">
				<col style="width: 20%;">
				<col style="width: 10%;">
				<col style="width: 10%;">
				<col style="width: 12%;">
				<tr class="text-center">
					<th>No.</th>
					<th colspan="2">Item Description</th>
					<th>Target Warehouse</th>
					<th>Quantity</th>
					<th>Transferred / Returned</th>
					<th>Action</th>
				</tr>
				@foreach ($items_return as $i => $return)
				<tr>
					<td class="text-center">{{ $i + 1 }}</td>
					<td class="text-center">
						@php
							$img = ($return['item_image']) ? "/img/" . $return['item_image'] : "/icon/no_img.png";
						@endphp
						<a href="http://athenaerp.fumaco.local/storage/{{ $img }}" data-toggle="lightbox">
							<img src="http://athenaerp.fumaco.local/storage/{{ $img }}" class="img-thumbnail" width="100">
						</a>
					</td>
					<td class="text-justify">
						<span class="ste-name d-none">{{ $return['ste_name'] }}</span>
						<span class="sted-name d-none">{{ $return['sted_name'] }}</span>
						<span class="d-block font-weight-bold item-code">{{ $return['item_code'] }}</span>
						<span class="d-block item-description" style="font-size: 8pt;">{!! $return['description'] !!}</span>
					</td>
					<td class="text-center source-warehouse" style="font-size: 9pt;">{{ $return['target_warehouse'] }}</td>
					<td class="text-center">
						<span class="d-block font-weight-bold required-qty">{{ $return['requested_qty'] * 1 }}</span>
						<span class="d-block" style="font-size: 8pt;">{{ $return['stock_uom'] }}</span>
					</td>
					<td class="text-center">
						<span class="d-block font-weight-bold">{{ $return['received_qty'] * 1 }}</span>
						<span class="d-block stock-uom" style="font-size: 8pt;">{{ $return['stock_uom'] }}</span>
					</td>
					<td class="text-center">
						@php
							$issued = ($return['received_qty'] > 0) ? 'disabled' : null;
						@endphp
						<button type="button" class="btn btn-danger btn-icon btn-icon-mini cancel-return-item-btn" data-production-order="{{ $details->production_order }}" {{ $issued }}>
							<i class="now-ui-icons ui-1_simple-remove"></i>
						</button>
					</td>
				</tr>
				@endforeach
			</table>
			@else
			<h5 class="text-center m-4">No record(s) found.</h5>
			@endif
		</div>
		<div class="tab-pane" id="w4" role="tabpanel" aria-labelledby="w4-tab">
			@if(count($feedbacked_logs) > 0)
			<div class="row">
				<div class="col-md-8 offset-md-2">
					<table style="width: 100%; border-collapse: collapse; margin-top: 10px;" class="custom-table-1-1" border="1">
						<col style="width: 10%;">
						<col style="width: 20%;">
						<col style="width: 20%;">
						<col style="width: 17%;">
						<col style="width: 17%;">
						<col style="width: 16%;">
						<tr class="text-center">
							<th>No.</th>
							<th >STE No.</th>
							<th>Feedbacked Qty</th>
							<th>Transaction Date</th>
							<th>Transaction Time</th>
							<th>Created by</th>
						</tr>
						@foreach ($feedbacked_logs as $i => $log)
						@php
							$created_by = str_replace('@fumaco.local', '', $log->created_by);
							$created_by = ucwords(str_replace('.', ' ', $created_by));
						@endphp
						<tr>
							<td class="text-center p-2">{{ $i + 1 }}</td>
							<td class="text-center p-2">{{ $log->ste_no }}</td>
							<td class="text-center p-2">
								<span class="font-weight-bold">{{ $log->feedbacked_qty }}</span> {{ $details->stock_uom }}</td>
							<td class="text-center p-2">{{ Carbon\Carbon::parse($log->transaction_date)->format('M-d-Y') }}</td>
							<td class="text-center p-2">{{ Carbon\Carbon::parse($log->transaction_time)->format('h:i:A') }}</td>
							<td class="text-center p-2">{{ $created_by }}</td>
						</tr>
						@endforeach
					</table>
				</div>
			</div>
			@else
			<h5 class="text-center m-4">No record(s) found.</h5>
			@endif
		</div>
	</div>
		
	@if($details->feedback_qty < $details->qty_to_manufacture)
    <div class="pull-left m-1">
        <button class="btn btn-primary btn-sm" id="add-required-item-btn" data-production-order="{{ $details->production_order }}">
            <i class="now-ui-icons ui-1_simple-add"></i> Add Item(s)
        </button>
	</div>
	@endif
@endif

<style>
    .custom-table-1-1{
        border: 1px solid #ABB2B9;
    }

    .custom-table-1-1 th{
        background-color: #D5D8DC;
        text-transform: uppercase;
        font-size: 9pt;
    }

    .custom-table-1-1 th, .custom-table-1-1 td{
        padding: 3px;
        border: 1px solid #ABB2B9;
	}
	
	.details-pane {
		display: none;
		color: #414141;
		background: #EBEDEF;
		border: 1px solid #a9a9a9;
		position: absolute;
		right: 70px;
		z-index: 1;
		width: 400px;
		padding: 6px 8px;
		text-align: left;
		-webkit-box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
		-moz-box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
		box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
		white-space: normal;
	}

	span.hvrlink:hover + .details-pane {
		display: block;
	}

	.details-pane:hover {
		display: block;
	}

	span.hvrlink{
		cursor: pointer;
	}
</style>