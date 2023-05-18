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

	$disabled = in_array($details->status, ['Cancelled', 'Closed']) ? 'disabled' : null;
	$disabled = $details->feedback_qty >= $details->qty_to_manufacture ? 'disabled' : null;

	$no_bom = ($details->bom_no == null) ? 'disabled1' : '';
@endphp
<span id="has-no-bom" class="d-none">{{ $details->bom_no }}</span>
<table style="width: 100%; border-collapse: collapse;" class="custom-table-1-1 exclude-table">
	<col style="width: 10%;"><!-- Reference No. -->
	<col style="width: 15%;"><!-- Customer -->
	<col style="width: 15%;"><!-- Project -->
	<col style="width: 10%;"><!-- Prod. Order -->
	<col style="width: 12%;"><!-- Actual Start Date -->
	<col style="width: 10%;"><!-- Delivery Date -->
	<col style="width: 10%;"><!-- For Manufacture -->
	<col style="width: 10%;"><!-- Produced -->
	<col style="width: 10%;"><!-- Feedbacked -->
	<tr class="text-center">
		<th>Reference No.</th>
		<th>Customer</th>
		<th>Project</th>
		<th>Delivery Date</th>
		<th>Planned Start</th>
		<th>Start & End Date</th>
		<td style="background-color: #28b463;" class="text-white">For Manufacture</td>
		<td style="background-color: #28b463;" class="text-white">Produced</td>
		<td style="background-color: #28b463;" class="text-white">Feedbacked</td>
	</tr>
	<tr class="text-center" style="font-size: 10pt;">
		
		<td><b>{{ $details->sales_order }}{{ $details->material_request }}</b></td>
		<td>{{ $details->customer }}</td>
		<td>{{ $details->project }}</td>
		<td>{{ ($details->rescheduled_delivery_date == null)? $details->delivery_date: $details->rescheduled_delivery_date   }}</td>
		<td>
			@if ($details->planned_start_date)
			<span class="d-block">{{ \Carbon\Carbon::parse($details->planned_start_date)->format('M. d, Y') }}</span>
			@else
			<span class="d-block text-danger">Unscheduled</span>
			@endif
		</td>
		<td>
			<span class="d-block">{{ $start_date . ' - ' . $end_date }}</span>
			<span class="d-block">{{ str_replace(' ', '', $duration) ? $duration : '' }}</span>
		</td>
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
		<td colspan="7">
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
			@if ($checker == 0)
			<button class="btn btn-primary m-1 sync-production-order-items-btn p-3" data-production-order="{{ $details->production_order }}" {{ $disabled }}>Sync Items</button>
			@else
			@if($issued_qty > 0)
			<button class="btn btn-primary m-1 submit-ste-btn p-3" data-production-order="{{ $details->production_order }}" {{ $disabled }}>Submit Withdrawal Slip</button>
			@elseif($ste_transferred_qty > 0 && $all_items_has_transferred_qty == 1 && $checker == 1)
			<button class="btn btn-success m-1 p-3">Withdrawal Slip Submitted</button>
			@elseif($ste_transferred_qty > 0 && $all_items_has_transferred_qty == 1 && $checker == 0)
			<button class="btn btn-primary m-1 sync-production-order-items-btn p-3" data-production-order="{{ $details->production_order }}" {{ $disabled }}>Sync Items</button>
			@else
			<button class="btn btn-primary m-1 generate-ste-btn p-3" data-production-order="{{ $details->production_order }}" {{ $disabled }}>Create Withdrawal Slip</button>
			@endif
			@endif
		</td>
	</tr>
</table>

@if(count($required_items) <= 0)
   <h5 class="text-center m-4">No withdrawal slip(s) created.</h5>
@else
	<style>
		#s-tab-1 .custom-nav-link {
			padding: 10px 20px;
			color: #2c3e50;
			text-decoration: none;
		}
		#s-tab-1 {
			border-bottom: 3px solid #ebedef;
			padding: 10px 0 10px 0;
		}
		#s-tab-1 .nav-item .active {
			color: #f96332;
			font-weight: bolder;
			border-bottom: 3px solid #f96332;
		}
	</style>
	<ul class="nav nav-tabs mt-2 font-weight-bold" role="tablist" id="s-tab-1">
		@if(count($components) > 0)
		<li class="nav-item">
			<a class="custom-nav-link {{ $tab1 }}" data-toggle="tab" href="#wtpoi1" role="tab" aria-controls="home" aria-selected="true">
				<span class="badge badge-info mr-2">{{ count($components) }}</span> Component(s) 
			</a>
		</li>
		@endif
		@if(count($parts) > 0)
		<li class="nav-item">
			<a class="custom-nav-link {{ $tab2 }}"  data-toggle="tab" href="#wtpoi2" role="tab" aria-controls="profile" aria-selected="false">
				<span class="badge badge-info mr-2">{{ count($parts) }}</span>	Part(s) 
			</a>
		</li>
		@endif
		@if(count($items_return) > 0)
		<li class="nav-item">
			<a class="custom-nav-link {{ $tab3 }}" data-toggle="tab" href="#wtpoi3" role="tab" aria-controls="messages" aria-selected="false">
				<span class="badge badge-info mr-2">{{ count($items_return) }}</span> Item Return(s)
			</a> 
		</li>
		@endif
		@if(count($feedbacked_logs) > 0)
		<li class="nav-item">
			<a class="custom-nav-link" data-toggle="tab" href="#wtpoi4" role="tab" aria-controls="messages" aria-selected="false">
				<span class="badge badge-info mr-2">{{ count($feedbacked_logs) }}</span> Feedbacked Log(s)
			</a> 
		</li>
		@endif
		@if (count($activity_logs) > 0)
		<li class="nav-item create-feedback-tab">
			<a class="custom-nav-link" data-toggle="tab" href="#wtpoi5" role="tab" aria-controls="messages" aria-selected="false">
				<span class="badge badge-info mr-2">{{ count($activity_logs) }}</span> Activity Log(s)
			</a> 
		</li>
		@endif
		@if (count($qa_array) > 0)
		<li class="nav-item create-feedback-tab">
			<a class="custom-nav-link" data-toggle="tab" href="#wtpoi6" role="tab" aria-controls="messages" aria-selected="false">
				<span class="badge badge-info mr-2">{{ count($qa_array) }}</span> Quality Inspection Log(s)
			</a> 
		</li>
		@endif
	</ul>

	<div class="tab-content bg-light mt-1" style="border: 1px solid #f2f3f4;">
		<div class="tab-pane {{ $tab1 }}" id="wtpoi1" role="tabpanel" aria-labelledby="w1-tab">
			@if(count($components) > 0)
			@php
				$transferred_qty_discrepancy_arr = collect($components)->map(function ($q){
					if(number_format($q['transferred_qty']) > number_format($q['required_qty'])){
						return $q['item_code'];
					}
				})->unique()->filter()->implode(', ');
			@endphp
			@if ($transferred_qty_discrepancy_arr)
				<div class="alert alert-warning text-center p-2 mt-2 w-100" style="color: #000">
					Transferred qty exceeds required qty of item(s) <b>{{ $transferred_qty_discrepancy_arr }}</b>
				</div>
			@endif
			<table style="width: 100%; border-collapse: collapse; margin-top: 10px;" class="custom-table-1-1" border="1">
				<col style="width: 3%;">	<!-- No. -->
				<col style="width: 7%;">	<!-- Image -->
				<col style="width: 25%;">	<!-- Item Code -->
				<col style="width: 8%;">	<!-- Required Qty -->
				<col style="width: 8%;">	<!-- Requested Qty -->
				<col style="width: 16%;">	<!-- Source Warehouse -->
				<col style="width: 10%;">	<!-- Transferred Qty -->
				<col style="width: 8%;">	<!-- Status -->
				<col style="width: 15%;">	<!-- Action -->
				<tr class="text-center">
					<th>No.</th>
					<th colspan="2">Item Code</th>
					<th>Required Qty</th>
					<th>Requested Qty</th>
					<th>Source Warehouse</th>
					<th>Transferred Qty</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
				@foreach ($components as $i => $component)
				@php
					$rowspan = (count($component['withdrawals']) > 1) ? 'rowspan="' . (count($component['withdrawals']) + 1) .'"' : 'rowspan="2"';
					$img = ($component['item_image']) ? "/img/" . explode('.', $component['item_image'])[0].'.webp' : "/icon/no_img.webp";
					$balance = $component['required_qty'] - $component['transferred_qty'];
					$wwhb = ($component['available_qty_at_wip'] < $component['transferred_qty'] || $component['available_qty_at_wip'] <= 0) ? "badge badge-danger" : "badge badge-success";
				@endphp
				<tr class="{{ (float)$component['transferred_qty'] > (float)$component['required_qty'] ? 'row-blink' : null }}">
					<td class="text-center" {!! $rowspan !!}>{{ $i + 1 }}</td>
					<td class="text-center" {!! $rowspan !!}>
						<a href="http://athenaerp.fumaco.local/storage{{ $img }}" data-toggle="lightbox">
							<img src="http://athenaerp.fumaco.local/storage{{ $img }}" class="img-thumbnail" width="100">
						</a>
					</td>
					@php
						$for_add_item = '';
						if($details->item_code != $component['item_code']) {
							$for_add_item = 'for-add-item';
						}
					@endphp
					<td class="text-justify {{ (!$component['is_alternative']) ? $for_add_item : null }}" {!! $rowspan !!}>
						<span class="item-name d-none">{{ $component['item_name'] }}</span>
						<div class="d-block">
							<span class="font-weight-bold item-code">{{ $component['item_code'] }}</span> 
							@if($component['is_alternative'])
							<small class="font-italic badge badge-info">Alternative for {{ $component['item_alternative_for'] }}</small> 
							@endif
						</div>
						
						<span class="d-none item-classification">{{ $component['item_classification'] }}</span>
						<span class="d-block item-description" style="font-size: 8pt;">{!! $component['description'] !!}</span>

						<span class="mt-2 {{ $wwhb }}" style="font-size: 9pt;">Balanced Qty: {{ floor($component['available_qty_at_wip']) != $component['available_qty_at_wip'] ? number_format($component['available_qty_at_wip'], 4) : $component['available_qty_at_wip'] * 1 }}</span>
					</td>
					<td class="text-center" {!! $rowspan !!}>
						<span class="d-block font-weight-bold required-qty" style="font-size: 10pt;">{{ $component['required_qty'] * 1 }}</span>
						<span class="d-block" style="font-size: 8pt;">{{ $component['stock_uom'] }}</span>
					</td>
					<td class="text-center p-0 border-bottom-0" colspan="4"></td>
				</tr>
				@forelse ($component['withdrawals'] as $a)
				@php
					$twhb = ($a['status'] == 'Issued') ? "badge badge-success" : "badge badge-danger";

					$swhb = ($a['actual_qty'] < $balance) ? "badge badge-danger" : "badge badge-success";
					$item_status_badge = ($a['status'] == 'For Checking') ? 'badge-warning' : 'badge-success';
					
					$transferred_issued_qty = ($a['status'] != 'For Checking') ? $a['qty'] : $a['issued_qty'];

					$ste_qty = ($a['status'] == 'For Checking') ? $balance : $component['required_qty'];
				@endphp
				<tr class="{{ (float)$component['transferred_qty'] > (float)$component['required_qty'] ? 'row-blink' : null }}">
					<td class="border-top-0 text-center">
						<span class="d-block font-weight-bold requested-qty" style="font-size: 10pt;">{{ $a['requested_qty'] * 1 }}</span>
						<span class="d-block" style="font-size: 8pt;">{{ $component['stock_uom'] }}</span>
					</td>
					<td class="border-top-0 text-center">
						<span class="d-none production-order-item-id">{{ $component['name'] }}</span>
						<span class="d-none ste-names">{{ $a['ste_names'] }}</span>
						<span class="d-none item-code">{{ $component['item_code'] }}</span>
						<span class="d-none item-name">{{ $component['item_name'] }}</span>
						<span class="d-none item-description">{!! $component['description'] !!}</span>
						<span class="d-none required-qty">{{ $component['required_qty'] * 1 }}</span>
						<span class="d-block source-warehouse" style="font-size: 9pt;">{{ $a['source_warehouse'] }}</span>
						<span class="d-none target-warehouse">{{ $details->wip_warehouse }}</span>
						<span class="font-weight-bold {{ $swhb }}" style="font-size: 9pt;">Current Qty: {{ (floor($a['actual_qty']) != $a['actual_qty'] * 1) ? number_format($a['actual_qty'], 4) : $a['actual_qty'] * 1 }}</span>
					</td>
					<td class="border-top-0 text-center">
						<span class="font-weight-bold qty {{ $twhb }}" style="font-size: 10pt;">{{ $transferred_issued_qty * 1 }}</span>
						<span class="d-block stock-uom" style="font-size: 8pt;">{{ $component['stock_uom'] }}</span>
					</td>
					<td class="border-top-0 text-center">
						<span class="badge {{ $item_status_badge }} hvrlink" style="font-size: 9pt;">{{ ($a['remarks'] == 'Fast Issued' && $a['status'] != 'For Checking') ? $a['remarks'] : $a['status'] }}</span>
						@if (in_array($a['source_warehouse'], $fast_issuance_warehouse) && ($balance * 1) <= ($a['actual_qty'] * 1) && $is_fast_issuance_user && !$a['ste_docstatus'])
						<button type="button" class="btn btn-primary btn-sm issue-now-btn" data-id="{{ $a['id'] }}" data-production-order="{{ $details->production_order }}"><span style="font-size: 7pt;">Issue Now</span></button>
						@endif
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
					<td class="border-top-0 text-center">
						@php
							$change_cancel_btn = ($a['status'] == 'Issued') ? 'disabled' : null;
							$return_btn = ($a['status'] == 'Issued') ? '' : 'disabled';
						@endphp
						<button type="button" class="btn btn-info btn-sm p-1 change-required-item-btn" data-production-order="{{ $details->production_order }}" data-item-classification="{{ $component['item_classification'] }}" data-production-order-item-id="{{ $component['name'] }}" {{ $disabled }} data-ws-status="{{ $a['status'] }}" style="min-width: 45px;"> 
							<i class="now-ui-icons ui-2_settings-90 d-block" style="font-size: 9pt;"></i><span style="font-size: 6pt;">Change</span>
						</button>
						<button type="button" class="btn btn-sm p-1 split-source-warehouse"
							data-production-order="{{ $details->production_order }}"
							data-item-id="{{ $component['name'] }}"
							data-item-classification="{{ $component['item_classification'] }}"
							data-item-code="{{ $component['item_code'] }}"
							data-item-name="{{ $component['item_name'] }}"
							data-source-warehouse="{{ $a['source_warehouse'] }}"
							data-transferred-qty="{{ $a['requested_qty'] * 1 }}"
							style="min-width: 45px; background-color: #22D3CC" {{ $change_cancel_btn }} {{ $disabled }}> 
							<i class="now-ui-icons business_chart-pie-36 d-block" style="font-size: 9pt;"></i><span style="font-size: 6pt;">Split</span>
						</button>
						<button type="button" class="btn btn-secondary btn-sm p-1 return-required-item-btn" data-production-order="{{ $details->production_order }}" data-production-order-item-id="{{ $component['name'] }}" {{ $return_btn }} {{ $no_bom }} {{ $disabled }} style="min-width: 45px;">
							<i class="now-ui-icons loader_refresh d-block" style="font-size: 9pt;"></i><span style="font-size: 6pt;">Return</span>
						</button>
						<button type="button" class="btn btn-danger  btn-sm p-1 delete-required-item-btn" data-production-order="{{ $details->production_order }}" {{ $change_cancel_btn }} {{ $no_bom }} {{ $disabled }} style="min-width: 45px;">
							<i class="now-ui-icons ui-1_simple-remove d-block" style="font-size: 9pt;"></i><span style="font-size: 6pt;">Cancel</span>
						</button>
						{{-- <div class="ws dropdown">
							<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							  	Actions
							</button>
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
								<button class="dropdown-item btn-sm change-required-item-btn" data-production-order="{{ $details->production_order }}" data-item-classification="{{ $component['item_classification'] }}" data-production-order-item-id="{{ $component['name'] }}" data-ws-status="{{ $a['status'] }}" {{ $disabled }}>
									<i class="now-ui-icons ui-2_settings-90"></i> Change
								</button>
								<button class="dropdown-item btn-sm split-source-warehouse"
									data-production-order="{{ $details->production_order }}"
									data-item-id="{{ $component['name'] }}"
									data-item-classification="{{ $component['item_classification'] }}"
									data-item-code="{{ $component['item_code'] }}"
									data-item-name="{{ $component['item_name'] }}"
									data-source-warehouse="{{ $a['source_warehouse'] }}"
									data-transferred-qty="{{ $a['requested_qty'] * 1 }}"
									{{ $change_cancel_btn }} {{ $disabled }}>
									<i class="now-ui-icons business_chart-pie-36"></i> Split Source Warehouse
								</button>
								<button class="dropdown-item btn-sm return-required-item-btn" data-production-order="{{ $details->production_order }}" data-production-order-item-id="{{ $component['name'] }}" {{ $return_btn }} {{ $no_bom }} {{ $disabled }}>
									<i class="now-ui-icons loader_refresh"></i> Return
								</button>
								<button class="dropdown-item btn-sm delete-required-item-btn" data-production-order="{{ $details->production_order }}" {{ $change_cancel_btn }} {{ $no_bom }} {{ $disabled }}>
									<i class="now-ui-icons ui-1_simple-remove"></i> Cancel
								</button>
							</div>
						</div> --}}
					</td>
				</tr>
				@empty
				@php
					$swhb_1 = ($component['actual_qty'] < $balance) ? "badge badge-danger" : "badge badge-success";
				@endphp
				<tr>
					<td class="border-top-0 text-center">
						<span class="d-block font-weight-bold" style="font-size: 10pt;">0</span>
						<span class="d-block stock-uom" style="font-size: 8pt;">{{ $component['stock_uom'] }}</span>
					</td>
					<td class="border-top-0 text-center">
						<span class="d-block" style="font-size: 9pt;">{{ $component['source_warehouse'] }}</span>
						<span class="font-weight-bold {{ $swhb_1 }}" style="font-size: 9pt;">Current Qty: {{ (floor($component['actual_qty']) != $component['actual_qty'] * 1) ? number_format($component['actual_qty'], 4) : $component['actual_qty'] * 1 }}</span>
					</td>
					<td class="border-top-0 text-center">
						<span class="font-weight-bold badge badge-danger" style="font-size: 10pt;">{{ $component['transferred_qty'] * 1 }}</span>
						<span class="d-block" style="font-size: 8pt;">{{ $component['stock_uom'] }}</span>
					</td>
					<td class="border-top-0 text-center">
						<span class="badge badge-warning hvrlink" style="font-size: 9pt;">For Checking</span>
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
					<td class="border-top-0 text-center">
						<div class="d-none">
							<span class="production-order-item-id">{{ $component['name'] }}</span>
							<span class="item-code">{{ $component['item_code'] }}</span>
							<span class="item-description">{!! $component['description'] !!}</span>
							<span class="item-name">{{ $component['item_name'] }}</span>
							<span class="required-qty">{{ $component['required_qty'] * 1 }}</span>
							<span class="requested-qty">{{ $component['required_qty'] * 1 }}</span>
							<span class="stock-uom">{{ $component['stock_uom'] }}</span>
							<span class="source-warehouse">{{ $component['source_warehouse'] }}</span>
						</div>
						<button type="button" class="btn btn-info btn-sm p-1 change-required-item-btn" data-production-order="{{ $details->production_order }}" data-item-classification="{{ $component['item_classification'] }}" data-production-order-item-id="{{ $component['name'] }}" {{ $disabled }} style="min-width: 45px;"> 
							<i class="now-ui-icons ui-2_settings-90 d-block" style="font-size: 9pt;"></i><span style="font-size: 6pt;">Change</span>
						</button>
						<button type="button" class="btn btn-sm p-1"style="min-width: 45px; background-color: #22D3CC" disabled> 
							<i class="now-ui-icons business_chart-pie-36 d-block" style="font-size: 9pt;"></i><span style="font-size: 6pt;">Split</span>
						</button>
						<button type="button" class="btn btn-secondary btn-sm p-1" disabled {{ $disabled }} style="min-width: 45px;">
							<i class="now-ui-icons loader_refresh d-block" style="font-size: 9pt;"></i><span style="font-size: 6pt;">Return</span>
						</button>
						<button type="button" class="btn btn-danger  btn-sm p-1 delete-required-item-btn" data-production-order="{{ $details->production_order }}" {{ $no_bom }} {{ $disabled }} style="min-width: 45px;">
							<i class="now-ui-icons ui-1_simple-remove d-block" style="font-size: 9pt;"></i><span style="font-size: 6pt;">Cancel</span>
						</button>
						{{-- <div class="ws dropdown">
							<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							  	Actions
							</button>
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
								<button class="dropdown-item btn-sm change-required-item-btn" data-production-order="{{ $details->production_order }}" data-item-classification="{{ $component['item_classification'] }}" data-production-order-item-id="{{ $component['name'] }}" {{ $disabled }}>
									<i class="now-ui-icons ui-2_settings-90"></i> Change
								</button>
								<button class="dropdown-item btn-sm split-source-warehouse" disabled>
									<i class="now-ui-icons business_chart-pie-36"></i> Split Source Warehouse
								</button>
								<button class="dropdown-item btn-sm return-required-item-btn" disabled>
									<i class="now-ui-icons loader_refresh"></i> Return
								</button>
								<button class="dropdown-item btn-sm delete-required-item-btn" disabled>
									<i class="now-ui-icons ui-1_simple-remove"></i> Cancel
								</button>
							</div>
						</div> --}}
					</td>
				</tr>
				@endforelse
				@endforeach
			</table>
			@else
			<h5 class="text-center m-4">No withdrawal slip(s) created for item component(s).</h5>
			@endif
		</div>
		<div class="tab-pane {{ $tab2 }}" id="wtpoi2" role="tabpanel" aria-labelledby="w2-tab">
			@if(count($parts) > 0)
			@php
				$transferred_qty_discrepancy_arr = collect($parts)->map(function ($q){
					if(number_format($q['transferred_qty']) > number_format($q['required_qty'])){
						return $q['item_code'];
					}
				})->unique()->filter()->implode(', ');
			@endphp
			@if ($transferred_qty_discrepancy_arr)
				<div class="alert alert-warning text-center p-2 mt-2 w-100" style="color: #000">
					Transferred qty exceeds required qty of item(s) <b>{{ $transferred_qty_discrepancy_arr }}</b>
				</div>
			@endif
			<table style="width: 100%; border-collapse: collapse; margin-top: 10px;" class="custom-table-1-1">
				<col style="width: 3%;"><!-- No. -->
				<col style="width: 9%;"><!-- Prod. Order -->
				<col style="width: 24%;"><!-- Item Description -->
				<col style="width: 9%;"><!-- Required Qty -->
				<col style="width: 9%;"><!-- Requested Qty -->
				<col style="width: 14%;"><!-- Source Warehouse -->
				<col style="width: 9%;"><!-- Transferred Qty -->
				<col style="width: 8%;"><!-- Status -->
				<col style="width: 15%;"><!-- Action -->
				<tr class="text-center">
					<th>No.</th>
					<th>Prod. Order</th>
					<th>Item Description</th>
					<th>Required Qty</th>
					<th>Requested Qty</th>
					<th>Source Warehouse</th>
					<th>Transferred Qty</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
				@foreach ($parts as $i => $part)
				@php
					$rowspan = (count($part['withdrawals']) > 1) ? 'rowspan="' . (count($part['withdrawals']) + 1) .'"' : 'rowspan="2"';
					$balance = $part['required_qty'] - $part['transferred_qty'];
					$wwhb = ($part['available_qty_at_wip'] < $part['transferred_qty'] || $part['available_qty_at_wip'] <= 0) ? "badge badge-danger" : "badge badge-success";
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
				@endphp
				<tr class="{{ (float)$part['transferred_qty'] > (float)$part['required_qty'] ? 'row-blink' : null }}">
					<td class="text-center" {!! $rowspan !!}>{{ $i + 1 }}</td>
					<td class="text-center" {!! $rowspan !!}>
						@if ($part['production_order'])
						<span class="d-block font-weight-bold view-production-order-details" data-production-order="{{ $part['production_order'] }}" style="color: black; cursor: pointer;">{{ $part['production_order'] }}</span>
						@else
						--
						@endif
						<span class="{{ $stat_badge }}" style="font-size: 9pt;">{{ $part['status'] }}</span>
						@if ($part['planned_start_date'])
						<small class="d-block mt-1">{{ $part['planned_start_date'] }}</small>
						@else
						<small class="d-block mt-1 text-danger">Unscheduled</small>
						@endif
					</td>
					@php
					$for_add_item_part = '';
					if($details->item_code != $part['item_code']) {
						$for_add_item_part = 'for-add-item';
					}
				@endphp
					<td class="text-justify {{ (!$part['is_alternative']) ? $for_add_item_part : null }}" {!! $rowspan !!}>
						<span class="item-name d-none">{{ $part['item_name'] }}</span>
						<div class="d-block">
							<span class="font-weight-bold item-code">{{ $part['item_code'] }}</span> 
							@if($part['is_alternative'])
							<small class="font-italic badge badge-info">Alternative for {{ $part['item_alternative_for'] }}</small> 
							@endif
						</div>
						<span class="d-none item-classification">{{ $part['item_classification'] }}</span>
						<span class="d-block item-description" style="font-size: 8pt;">{!! $part['description'] !!}</span>
						<span class="mt-2 {{ $wwhb }}" style="font-size: 9pt;">Balanced Qty: {{ floor($part['available_qty_at_wip']) != $part['available_qty_at_wip'] ? number_format($part['available_qty_at_wip'], 4) : $part['available_qty_at_wip'] * 1 }}</span>
					</td>
					<td class="text-center" {!! $rowspan !!}>
						<span class="d-block font-weight-bold required-qty" style="font-size: 10pt;">{{ $part['required_qty'] * 1 }}</span>
						<span class="d-block stock-uom" style="font-size: 8pt;">{{ $part['stock_uom'] }}</span>
					</td>
					<td class="text-center p-0 border-bottom-0" colspan="4"></td>
				</tr>
				@forelse ($part['withdrawals'] as $a)	
				@php
					$twhb = ($a['status'] == 'Issued') ? "badge badge-success" : "badge badge-danger";

					$swhb = ($a['actual_qty'] < $balance) ? "badge badge-danger" : "badge badge-success";
					$item_status_badge = ($a['status'] == 'For Checking') ? 'badge-warning' : 'badge-success';
					
					$transferred_issued_qty = ($a['status'] != 'For Checking') ? $a['qty'] : $a['issued_qty'];

					$ste_qty = ($a['status'] == 'For Checking') ? $balance : $part['required_qty'];
				@endphp
				<tr class="{{ (float)$part['transferred_qty'] > (float)$part['required_qty'] ? 'row-blink' : null }}">
					<td class="border-top-0 text-center">
						<span class="d-block font-weight-bold requested-qty" style="font-size: 10pt;">{{ $a['requested_qty'] * 1 }}</span>
						<span class="d-block" style="font-size: 8pt;">{{ $part['stock_uom'] }}</span>
					</td>
					<td class="border-top-0 text-center">
						<span class="d-none production-order-item-id">{{ $part['name'] }}</span>
						<span class="d-none ste-names">{{ $a['ste_names'] }}</span>
						<span class="d-none item-code">{{ $part['item_code'] }}</span>
						<span class="d-none item-name">{{ $part['item_name'] }}</span>
						<span class="d-none item-description">{!! $part['description'] !!}</span>
						<span class="d-none required-qty">{{ $part['required_qty'] * 1 }}</span>
						<span class="d-block source-warehouse" style="font-size: 9pt;">{{ $a['source_warehouse'] }}</span>
						<span class="d-none target-warehouse">{{ $details->wip_warehouse }}</span>
						<span class="font-weight-bold {{ $swhb }}" style="font-size: 9pt;">Current Qty: {{ (floor($a['actual_qty']) != $a['actual_qty'] * 1) ? number_format($a['actual_qty'], 4) : $a['actual_qty'] * 1 }}</span>
					</td>
					<td class="border-top-0 text-center">
						<span class="font-weight-bold qty {{ $twhb }}" style="font-size: 10pt;">{{ $transferred_issued_qty * 1 }}</span>
						<span class="d-block stock-uom" style="font-size: 8pt;">{{ $part['stock_uom'] }}</span>
					</td>
					<td class="border-top-0 text-center">
						<span class="badge {{ $item_status_badge }} hvrlink" style="font-size: 9pt;">{{ ($a['remarks'] == 'Fast Issued' && $a['status'] != 'For Checking') ? $a['remarks'] : $a['status'] }}</span>
						@if (in_array($a['source_warehouse'], $fast_issuance_warehouse) && ($part['required_qty'] * 1) <= ($a['actual_qty'] * 1) && $is_fast_issuance_user && !$a['ste_docstatus'])
						<button type="button" class="btn btn-primary btn-sm issue-now-btn" data-id="{{ $a['id'] }}" data-production-order="{{ $details->production_order }}"><span style="font-size: 7pt;">Issue Now</span></button>
						@endif
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
					<td class="border-top-0 text-center">
						@php
							$change_cancel_btn = ($a['status'] == 'Issued') ? 'disabled' : null;
							$return_btn = ($a['status'] == 'Issued') ? '' : 'disabled';
						@endphp
						<button type="button" class="btn btn-info btn-sm p-1 change-required-item-btn" data-production-order="{{ $details->production_order }}" data-item-classification="{{ $part['item_classification'] }}" data-production-order-item-id="{{ $part['name'] }}" data-ws-status="{{ $a['status'] }}" {{ $disabled }} style="min-width: 45px;"> 
								<i class="now-ui-icons ui-2_settings-90 d-block" style="font-size: 9pt;"></i><span style="font-size: 6pt;">Change</span>
						</button>
						<button type="button" class="btn btn-sm p-1 split-source-warehouse"
							data-production-order="{{ $details->production_order }}"
							data-item-id="{{ $part['name'] }}"
							data-item-classification="{{ $part['item_classification'] }}"
							data-item-code="{{ $part['item_code'] }}"
							data-item-name="{{ $part['item_name'] }}"
							data-source-warehouse="{{ $a['source_warehouse'] }}"
							data-transferred-qty="{{ $a['requested_qty'] * 1 }}"
							style="min-width: 45px; background-color: #22D3CC" {{ $change_cancel_btn }} {{ $disabled }}> 
							<i class="now-ui-icons business_chart-pie-36 d-block" style="font-size: 9pt;"></i><span style="font-size: 6pt;">Split</span>
						</button>
						<button type="button" class="btn btn-secondary btn-sm p-1 return-required-item-btn" data-production-order="{{ $details->production_order }}" data-production-order-item-id="{{ $part['name'] }}" {{ $return_btn }} {{ $no_bom }} {{ $disabled }} style="min-width: 45px;">
							<i class="now-ui-icons loader_refresh d-block" style="font-size: 9pt;"></i><span style="font-size: 6pt;">Return</span>
						</button>
						<button type="button" class="btn btn-danger btn-sm p-1 delete-required-item-btn" data-production-order="{{ $details->production_order }}" {{ $change_cancel_btn }} {{ $no_bom }} {{ $disabled }} style="min-width: 45px;">
							<i class="now-ui-icons ui-1_simple-remove d-block" style="font-size: 9pt;"></i><span style="font-size: 6pt;">Cancel</span>
						</button>
						{{-- <div class="ws dropdown">
							<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							  	Actions
							</button>
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
								<button class="dropdown-item btn-sm change-required-item-btn" data-production-order="{{ $details->production_order }}" data-item-classification="{{ $part['item_classification'] }}" data-production-order-item-id="{{ $part['name'] }}" data-ws-status="{{ $a['status'] }}" {{ $disabled }}>
									<i class="now-ui-icons ui-2_settings-90"></i> Change
								</button>
								<button class="dropdown-item btn-sm split-source-warehouse"
									data-production-order="{{ $details->production_order }}"
									data-item-id="{{ $part['name'] }}"
									data-item-classification="{{ $part['item_classification'] }}"
									data-item-code="{{ $part['item_code'] }}"
									data-item-name="{{ $part['item_name'] }}"
									data-source-warehouse="{{ $a['source_warehouse'] }}"
									data-transferred-qty="{{ $a['requested_qty'] * 1 }}"
									{{ $change_cancel_btn }} {{ $disabled }}>
									<i class="now-ui-icons business_chart-pie-36"></i> Split Source Warehouse
								</button>
								<button class="dropdown-item btn-sm return-required-item-btn" data-production-order="{{ $details->production_order }}" data-production-order-item-id="{{ $part['name'] }}" {{ $return_btn }} {{ $no_bom }} {{ $disabled }}>
									<i class="now-ui-icons loader_refresh"></i> Return
								</button>
								<button class="dropdown-item btn-sm delete-required-item-btn" data-production-order="{{ $details->production_order }}" {{ $change_cancel_btn }} {{ $no_bom }} {{ $disabled }}>
									<i class="now-ui-icons ui-1_simple-remove"></i> Cancel
								</button>
							</div>
						</div> --}}
					</td>
				</tr>
				@empty
				@php
					$swhb_1 = ($part['actual_qty'] < $balance) ? "badge badge-danger" : "badge badge-success";
				@endphp
				<tr>
					<td class="border-top-0 text-center">
						<span class="d-block font-weight-bold" style="font-size: 10pt;">0</span>
						<span class="d-block stock-uom" style="font-size: 8pt;">{{ $part['stock_uom'] }}</span>
					</td>
					<td class="border-top-0 text-center">
						<span class="d-block" style="font-size: 9pt;">{{ $part['source_warehouse'] }}</span>
						<span class="font-weight-bold {{ $swhb_1 }}" style="font-size: 9pt;">Current Qty: {{ (floor($part['actual_qty']) != $part['actual_qty'] * 1) ? number_format($part['actual_qty'], 4) : $part['actual_qty'] * 1 }}</span>
					</td>
					<td class="border-top-0 text-center">
						<span class="font-weight-bold badge badge-danger" style="font-size: 10pt;">{{ $part['transferred_qty'] * 1 }}</span>
						<span class="d-block" style="font-size: 8pt;">{{ $part['stock_uom'] }}</span>
					</td>
					<td class="border-top-0 text-center">
						<span class="badge badge-warning hvrlink" style="font-size: 9pt;">For Checking</span>
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
					<td class="border-top-0 text-center">
						<span class="d-none production-order-item-id">{{ $part['name'] }}</span>
						<span class="d-none item-code">{{ $part['item_code'] }}</span>
						<span class="d-none item-description">{!! $part['description'] !!}</span>
						<span class="d-none item-name">{{ $part['item_name'] }}</span>
						<span class="d-none required-qty">{{ $part['required_qty'] * 1 }}</span>
						<span class="d-none requested-qty">{{ $part['required_qty'] * 1 }}</span>
						<span class="d-none stock-uom">{{ $part['stock_uom'] }}</span>
						<span class="d-none source-warehouse">{{ $part['source_warehouse'] }}</span>
						<button type="button" class="btn btn-info  btn-sm p-1 change-required-item-btn" data-production-order="{{ $details->production_order }}" data-item-classification="{{ $part['item_classification'] }}" data-production-order-item-id="{{ $part['name'] }}" {{ $disabled }} style="width: 45px;"> 
							<i class="now-ui-icons ui-2_settings-90 d-block" style="font-size: 9pt;"></i><span style="font-size: 6pt;">Change</span>
						</button>
						<button type="button" class="btn btn-sm p-1" style="min-width: 45px; background-color: #22D3CC" disabled style="width: 45px;"> 
							<i class="now-ui-icons business_chart-pie-36 d-block" style="font-size: 9pt;"></i><span style="font-size: 6pt;">Split</span>
						</button>
						<button type="button" class="btn btn-secondary btn-sm p-1" disabled {{ $disabled }} style="width: 45px;">
							<i class="now-ui-icons loader_refresh d-block" style="font-size: 9pt;"></i><span style="font-size: 6pt;">Return</span>
						</button>
						<button type="button" class="btn btn-danger  btn-sm p-1 delete-required-item-btn" data-production-order="{{ $details->production_order }}" {{ $no_bom }} {{ $disabled }} style="width: 45px;">
							<i class="now-ui-icons ui-1_simple-remove d-block" style="font-size: 9pt;"></i><span style="font-size: 6pt;">Cancel</span>
						</button>
					</td>
				</tr>
				@endforelse
				@endforeach
			</table>
			@else
			<h5 class="text-center m-4">No withdrawal slip(s) created for item component(s).</h5>
			@endif
		</div>
		<div class="tab-pane {{ $tab3 }}" id="wtpoi3" role="tabpanel" aria-labelledby="w3-tab">
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
							$img = ($return['item_image']) ? "/img/" . explode('.', $return['item_image'])[0].'.webp' : "/icon/no_img.webp";
						@endphp
						<a href="http://athenaerp.fumaco.local/storage{{ $img }}" data-toggle="lightbox">
							<img src="http://athenaerp.fumaco.local/storage{{ $img }}" class="img-thumbnail" width="100">
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
		<div class="tab-pane" id="wtpoi4" role="tabpanel" aria-labelledby="w4-tab">
			@if(count($feedbacked_logs) > 0)
			<div class="row m-0">
				<div class="col-md-10 offset-md-1">
					<table class="custom-table-1-1 w-100 mt-3">
						<col style="width: 8%;">
						<col style="width: 15%;">
						<col style="width: 14.5%;">
						<col style="width: 13%;">
						<col style="width: 12%;">
						<col style="width: 17.5%;">
						<col style="width: 10%;">
						<col style="width: 10%;">
						<tr class="text-center">
							<th>No.</th>
							<th >STE No.</th>
							<th>Feedbacked Qty</th>
							<th>Date</th>
							<th>Time</th>
							<th>Created by</th>
							<th>Status</th>
							<th>Action</th>
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
								<span class="font-weight-bold qty">{{ $log->feedbacked_qty }}</span> <span class="uom">{{ $details->stock_uom }}</span>
							</td>
							<td class="text-center p-2">{{ Carbon\Carbon::parse($log->transaction_date)->format('M-d-Y') }}</td>
							<td class="text-center p-2">{{ Carbon\Carbon::parse($log->transaction_time)->format('h:i:A') }}</td>
							<td class="text-center p-2">{{ $created_by }}</td>
							<td class="text-center p-2">
								<span class="badge {{ ($log->status == 'Cancelled') ? 'badge-danger' : 'badge-success' }}" style="font-size: 10pt;">{{ $log->status }}</span>
							</td>
							<td class="text-center p-2">
								<span class="d-none production-order">{{ $log->production_order }}</span>
								<button class="btn btn-danger m-0 cancel-production-order-feedback-btn" data-stock-entry="{{ $log->ste_no }}" {{ ($log->status == 'Cancelled') || in_array($details->status, ['Cancelled', 'Closed']) ? 'disabled' : '' }}>Cancel</button>
							</td>
						</tr>
						@endforeach
					</table>
				</div>
			</div>
			@else
			<h5 class="text-center m-4">No record(s) found.</h5>
			@endif
		</div>

		<!-- Activity Logs -->
		<div class="tab-pane" id="wtpoi5" role="tabpanel" aria-labelledby="w5-tab">
			<div class="col-12 p-2" style="max-height: 500px; overflow:auto;">
				<ul>
					@foreach ($activity_logs as $log)
						<li>
							<span style="font-size: 8pt;" class="text-muted">{{ Carbon\Carbon::parse($log->created_at)->format('M d, Y h:i:s a') }}</span><br>
							<span style="font-size: 8pt;"><b>{{ $log->action }}</b></span><br>
							<span style="font-size: 8pt;">{!! $log->message !!}</span><br><br>
						</li>
					@endforeach
				</ul>
			</div>
		</div>
		<!-- Activity Logs -->

		<!-- QA Logs -->
		<div class="tab-pane" id="wtpoi6" role="tabpanel" aria-labelledby="w5-tab">
			<div class="col-12 p-2" style="max-height: 500px; overflow:auto;">
				@if(count($qa_array) > 0)
					<table class="custom-table-1-1 w-100 mt-3" style="font-size: 9pt;">
						<tr class="text-center">
							<th>Workstation</th>
							<th>Status</th>
							<th>Qty Checked</th>
							<th>Rejects</th>
							<th>Inspection Type</th>
							<th>Inspection Date</th>
							<th>Inspected By</th>
						</tr>
						@foreach ($qa_array as $i => $qa)
							@php
								switch($qa->qa_status){
									case 'QC Passed':
										$badge = 'success';
										break;
									case 'QC Failed':
										$badge = 'secondary';
										break;
									case 'For Confirmation':
										$badge = 'warning';
										break;
									default:
										$badge = null;
										break;
								}
							@endphp
							<tr>
								<td class="text-center p-1">{{ $qa->workstation }}</td>
								<td class="text-center p-1">
									<span class="badge badge-{{ $badge }}" style="font-size: 8pt;">{{ $qa->qa_status }}</span>
								</td>
								<td class="text-center p-1">{{ $qa->actual_qty_checked }}</td>
								<td class="text-center p-1">{{ $qa->rejected_qty }}</td>
								<td class="text-center p-1">
									{{ $qa->qa_inspection_type }}
									@if ($qa->qa_inspection_type == 'Reject Confirmation')
										@isset($reject_reason[$qa->job_ticket_id])
											<div class="text-left p-1 m-0">
												<ul class="m-0">
													@foreach ($reject_reason[$qa->job_ticket_id] as $reject)
														<li class="font-italic">{{ $reject->reject_reason }}</li>
													@endforeach
												</ul>
											</div>
										@endisset
									@endif
								</td>
								<td class="text-center p-1">{{ Carbon\Carbon::parse($qa->qa_inspection_date)->format('M-d-Y h:i:A') }}</td>
								<td class="text-center p-1">{{ $qa->qa_owner }}</td>
							</tr>
						@endforeach
					</table>
				@else
					<h5 class="text-center m-4">No record(s) found.</h5>
				@endif
			</div>
		</div>
		<!-- QA Logs -->
	</div>
		
	@if($details->feedback_qty < $details->qty_to_manufacture)
    <div class="pull-left m-1">
        <button class="btn btn-primary btn-sm" id="add-required-item-btn" data-production-order="{{ $details->production_order }}" {{ $no_bom }} {{ $disabled }}>
            <i class="now-ui-icons ui-1_simple-add"></i> Add Item(s)
        </button>
	</div>
	@endif
@endif

<style>
    .custom-table-1-1{
		border: 1px solid #ABB2B9;
		border-collapse: collapse;
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

	.row-blink {
		background-color: #FFC107;
		/* color: #000000; */
		animation: blinkingBackground 2.5s linear infinite;
	}

	.ws .dropdown-item:hover{
		background-color: rgba(0,0,0,0.2) !important;
	}

	@keyframes blinkingBackground{
		0%    { background-color: #ffffff;}
		25%   { background-color: #FFC107;}
		50%   { background-color: #ffffff;}
		75%   { background-color: #FFC107;}
		100%  { background-color: #ffffff;}
	}
</style>