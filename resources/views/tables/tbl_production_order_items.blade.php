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
	<col style="width: 27%;">
	<col style="width: 27%;">
	<col style="width: 11%;">
	<col style="width: 15%;">
	<tr class="text-center">
		<th>Prod. Order</th>
		<th>Reference No.</th>
		<th>Customer</th>
		<th>Project</th>
		<th>Delivery Date</th>
		<th>Qty to Manufacture</th>
	</tr>
	<tr class="text-center" style="font-size: 10pt;">
		<td>
			<a href="#" class="font-weight-bold view-production-order-details" data-production-order="{{ $details->production_order }}" style="color: black;">{{ $details->production_order }}</a>
		</td>
		<td><b>{{ $details->sales_order }}{{ $details->material_request }}</b></td>
		<td>{{ $details->customer }}</td>
		<td>{{ $details->project }}</td>
		<td>{{ ($details->rescheduled_delivery_date == null)? $details->delivery_date: $details->rescheduled_delivery_date   }}</td>
		<td>
			<span class="d-block font-weight-bold" style="font-size: 11pt;">{{ $details->qty_to_manufacture }}</span>
			<span class="d-block" style="font-size: 8pt;">{{ $details->stock_uom }}</span>
		</td>
	</tr>
	<tr style="font-size: 10pt;">
		<td class="text-center font-weight-bold">ITEM DETAIL(S)</td>
		<td colspan="4"><span class="font-weight-bold">{{ $details->item_code }}</span> - {{ $details->description }}</td>
		<td class="text-center">
			@php
				$ref_ste = array_column($required_items, 'ste_docstatus');
			@endphp
			@if(count($required_items) > 0)
			@if(collect($ref_ste)->min() == 0)
			<button class="btn btn-primary m-1 submit-ste-btn p-3" data-production-order="{{ $details->production_order }}">Submit Withdrawal Slip</button>
			@else
			<button class="btn btn-success m-1 p-3">Withdrawal Slip Submitted</button>
			@endif
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
	</ul>

	<div class="tab-content bg-light" style="border: 1px solid #f2f3f4;">
		<div class="tab-pane {{ $tab1 }}" id="w1" role="tabpanel" aria-labelledby="w1-tab">
			@if(count($components) > 0)
			<table style="width: 100%; border-collapse: collapse; margin-top: 10px;" class="custom-table-1-1">
				<col style="width: 5%;">
				<col style="width: 8%;">
				<col style="width: 32%;">
				<col style="width: 15%;">
				<col style="width: 10%;">
				<col style="width: 10%;">
				<col style="width: 10%;">
				<col style="width: 10%;">
				<tr class="text-center">
					<th>No.</th>
					<th colspan="2">Item Code</th>
					<th>Source Warehouse</th>
					<th>Available</th>
					<th>Required</th>
					<th>Transferred / Issued</th>
					<th>Action</th>
				</tr>
				@foreach ($components as $i => $component)
				<tr>
					<td class="text-center">{{ $i + 1 }}</td>
					<td class="text-center">
						@php
							$img = ($component['item_image']) ? "/img/" . $component['item_image'] : "/icon/no_img.png";
						@endphp
						<a href="http://athenaerp.fumaco.local/storage/{{ $img }}" data-toggle="lightbox">
							<img src="http://athenaerp.fumaco.local/storage/{{ $img }}" class="img-thumbnail" width="100">
						</a>
					</td>
					<td class="text-justify">
						<span class="ste-name d-none">{{ $component['ste_name'] }}</span>
						<span class="sted-name d-none">{{ $component['sted_name'] }}</span>
						<span class="item-name d-none">{{ $component['item_name'] }}</span>
						<span class="d-block font-weight-bold item-code">{{ $component['item_code'] }}</span>
						<span class="d-block item-description" style="font-size: 8pt;">{!! $component['description'] !!}</span>
					</td>
					<td class="text-center source-warehouse" style="font-size: 9pt;">{{ $component['source_warehouse'] }}</td>
					<td class="text-center">
						<span class="d-block font-weight-bold">{{ $component['actual_qty'] * 1 }}</span>
						<span class="d-block stock-uom" style="font-size: 8pt;">{{ $component['stock_uom'] }}</span>
					</td>
					<td class="text-center">
						<span class="d-block font-weight-bold required-qty">{{ $component['requested_qty'] * 1 }}</span>
						<span class="d-block" style="font-size: 8pt;">{{ $component['stock_uom'] }}</span>
					</td>
					<td class="text-center">
						@php
							$transferred_qty = $component['transferred_qty'] * 1;
							$status_color = '';
							if ($transferred_qty <= 0){
								$status_color = 'badge badge-danger';
							}elseif($transferred_qty >= ($component['requested_qty'] * 1)){
								$status_color = 'badge badge-success';
							}else{
								$status_color = '';
							}
						@endphp
						<span class="font-weight-bold qty {{ $status_color }}" style="font-size: 9pt;">{{ $component['transferred_qty'] * 1 }}</span>
						<span class="d-block" style="font-size: 8pt;">{{ $component['stock_uom'] }}</span>
					</td>
					<td class="text-center">
						@php
							$change_cancel_btn = ($component['ste_docstatus'] == 1) ? 'disabled' : null;
							$return_btn = ($component['ste_docstatus'] == 1) ? '' : 'disabled';
						@endphp
						<button type="button" class="btn btn-info  btn-sm p-1 change-required-item-btn" data-production-order="{{ $details->production_order }}" data-item-classification="{{ $component['item_classification'] }}" {{ $change_cancel_btn }}> 
								<i class="now-ui-icons ui-2_settings-90 d-block"></i><span style="font-size: 7pt;">Change</span>
						</button>
						<button type="button" class="btn btn-secondary  btn-sm p-1 return-required-item-btn" data-sted-id="{{ $component['sted_name'] }}" data-production-order="{{ $details->production_order }}" {{ $return_btn }}>
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
				<col style="width: 4%;">
				<col style="width: 10%;">
				<col style="width: 8%;">
				<col style="width: 22%;">
				<col style="width: 15%;">
				<col style="width: 10%;">
				<col style="width: 10%;">
				<col style="width: 10%;">
				<col style="width: 11%;">
				<tr class="text-center">
					<th>No.</th>
					<th>Prod. Order</th>
					<th colspan="2">Item Description</th>
					<th>Source Warehouse</th>
					<th>Available</th>
					<th>Required</th>
					<th>Transferred / Issued</th>
					<th>Action</th>
				</tr>
				@foreach ($parts as $i => $part)
				<tr>
					<td class="text-center">{{ $i + 1 }}</td>
					<td class="text-center">
						@if ($part['production_order'])
						<span class="font-weight-bold view-production-order-details" data-production-order="{{ $part['production_order'] }}" style="color: black; cursor: pointer;">{{ $part['production_order'] }}</span>
						@else
						--
						@endif
					</td>
					<td class="text-center">
						@php
							$img = ($part['item_image']) ? "/img/" . $part['item_image'] : "/icon/no_img.png";
						@endphp
						<a href="http://athenaerp.fumaco.local/storage/{{ $img }}" data-toggle="lightbox">
							<img src="http://athenaerp.fumaco.local/storage/{{ $img }}" class="img-thumbnail" width="100">
						</a>
					</td>
					<td class="text-justify">
						<span class="ste-name d-none">{{ $part['ste_name'] }}</span>
						<span class="sted-name d-none">{{ $part['sted_name'] }}</span>
						<span class="item-name d-none">{{ $part['item_name'] }}</span>
						<span class="d-block font-weight-bold item-code">{{ $part['item_code'] }}</span>
						<span class="d-block item-description" style="font-size: 8pt;">{!! $part['description'] !!}</span>
					</td>
					<td class="text-center source-warehouse" style="font-size: 9pt;">{{ $part['source_warehouse'] }}</td>
					<td class="text-center">
						<span class="d-block font-weight-bold">{{ $part['actual_qty'] * 1 }}</span>
						<span class="d-block stock-uom" style="font-size: 8pt;">{{ $part['stock_uom'] }}</span>
					</td>
					<td class="text-center">
						<span class="d-block font-weight-bold required-qty">{{ $part['requested_qty'] * 1 }}</span>
						<span class="d-block" style="font-size: 8pt;">{{ $part['stock_uom'] }}</span>
					</td>
					<td class="text-center">
						@php
							$transferred_qty = $part['transferred_qty'] * 1;
							$status_color = '';
							if ($transferred_qty <= 0){
								$status_color = 'badge badge-danger';
							}elseif($transferred_qty >= ($part['requested_qty'] * 1)){
								$status_color = 'badge badge-success';
							}else{
								$status_color = '';
							}
						@endphp
						<span class="font-weight-bold qty {{ $status_color }}" style="font-size: 9pt;">{{ $part['transferred_qty'] * 1 }}</span>
						<span class="d-block" style="font-size: 8pt;">{{ $part['stock_uom'] }}</span>
					</td>
					<td class="text-center p-0">
						@php
							$change_cancel_btn = ($part['ste_docstatus'] == 1) ? 'disabled' : null;
							$return_btn = ($part['ste_docstatus'] == 1) ? '' : 'disabled';
						@endphp
						<button type="button" class="btn btn-info btn-sm p-1 change-required-item-btn" data-production-order="{{ $details->production_order }}" data-item-classification="{{ $part['item_classification'] }}" {{ $change_cancel_btn }}> 
							<i class="now-ui-icons ui-2_settings-90 d-block"></i><span style="font-size: 7pt;">Change</span>
						</button>
						<button type="button" class="btn btn-secondary p-1 btn-sm return-required-item-btn" data-sted-id="{{ $part['sted_name'] }}" data-production-order="{{ $details->production_order }}" {{ $return_btn }}>
							<i class="now-ui-icons loader_refresh d-block"></i><span style="font-size: 7pt;">Return</span>
						</button>
						<button type="button" class="btn btn-danger p-1 btn-sm delete-required-item-btn" data-production-order="{{ $details->production_order }}" {{ $change_cancel_btn }}>
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
						<span class="d-block" style="font-size: 8pt;">{{ $return['stock_uom'] }}</span>
					</td>
					<td class="text-center">
						@php
							$issued = ($return['received_qty'] > 0) ? 'disabled' : null;
						@endphp
						<button type="button" class="btn btn-danger btn-icon btn-icon-mini delete-required-item-btn" data-production-order="{{ $details->production_order }}" {{ $issued }}>
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
	</div>
	

    @if(count($reference_stock_entry) > 0)
    <div class="pull-right font-italic m-2" style="font-size: 9pt;">
        <b>Reference: </b>{{ implode(', ', $reference_stock_entry) }}
    </div>
    @endif
    <div class="pull-left m-1">
        <button class="btn btn-primary btn-sm" id="add-required-item-btn" data-production-order="{{ $details->production_order }}">
            <i class="now-ui-icons ui-1_simple-add"></i> Add Item(s)
        </button>
    </div>
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
</style>