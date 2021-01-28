@if ($details->feedback_qty > 0)
<div class="alert alert-warning text-center" role="alert">
	<div class="container">
		<strong>Note!</strong> Production Order has been partially feedbacked.
	</div>
</div>
@else

	@if(count($items) > 0)
	<h5 class="text-center mt-3 mb-1">Item(s) for Return</h5>
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
			<th>Source</th>
			<th>Target</th>
			<th>Quantity</th>
			<th>Action</th>
		</tr>
		@foreach ($items as $i => $row)
		<tr>
			<td class="text-center">{{ $i + 1 }}</td>
			<td class="text-center">
				@php
					$img = ($row['item_image']) ? "/img/" . $row['item_image'] : "/icon/no_img.png";
				@endphp
				<a href="http://athenaerp.fumaco.local/storage/{{ $img }}" data-toggle="lightbox">
					<img src="http://athenaerp.fumaco.local/storage/{{ $img }}" class="img-thumbnail" width="100">
				</a>
			</td>
			<td class="text-justify">
				<span class="ste-names d-none">{{ $row['ste_names'] }}</span>
				<span class="d-block font-weight-bold item-code">{{ $row['item_code'] }}</span>
				<span class="d-block item-description" style="font-size: 8pt;">{!! $row['description'] !!}</span>
			</td>
			<td class="text-center source-warehouse" style="font-size: 9pt;">{{ $row['source_warehouse'] }}</td>
			<td class="text-center target-warehouse" style="font-size: 9pt;">{{ $row['target_warehouse'] }}</td>
			<td class="text-center">
				<span class="d-block font-weight-bold qty">{{ $row['qty'] * 1 }}</span>
				<span class="d-block" style="font-size: 8pt;">{{ $row['stock_uom'] }}</span>
			</td>
			<td class="text-center">
				<button type="button" class="btn btn-secondary btn-icon btn-icon-mini return-required-item-btn" data-production-order="{{ $details->production_order }}">
					<i class="now-ui-icons loader_refresh"></i>
				</button>
			</td>
		</tr>
		@endforeach
	</table>

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
	@endif
@endif