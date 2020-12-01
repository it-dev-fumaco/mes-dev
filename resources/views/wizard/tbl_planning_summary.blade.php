@php
	$has_scheduled = collect($production_order_list)->sum('is_scheduled');
@endphp

<table class="table table-hover table-bordered" style="font-size: 8pt;" id="summary-tbl">
	<col style="width: 12%;">
	<col style="width: 12%;">
	<col style="width: 42%;">
	<col style="width: 12%;">
	<col style="width: 12%;">
	<thead class="text-white bg-secondary">
		<th class="text-center"><b>Parent Code</b></th>
		<th class="text-center"><b>Production Order</b></th>
		<th class="text-center"><b>Item Code</b></th>
		<th class="text-center"><b>Planned Qty</b></th>
		@if($has_scheduled > 0)
		<th class="text-center"><b>Planned Start Date</b></th>
		@endif
	</thead>
	<tbody style="font-size: 9pt;">
		@foreach($production_order_list as $prod)
		<tr>
			<td class="text-center font-weight-bold">{{ $prod['parent_code'] }}</td>
			<td class="text-center font-weight-bold">{{ $prod['production_order'] }}</td>
			<td class="text-justify"><b>{{ $prod['item_code'] }}</b><br>{!! $prod['description'] !!}</td>
			<td class="text-center">
				<span class="d-block font-weight-bold" style="font-size: 11pt;">{{ number_format($prod['qty']) }}</span>
				<span class="d-block">{{ $prod['stock_uom'] }}</span>
			</td>
			@if($has_scheduled > 0)
			<td class="text-center font-weight-bold">{{ ($prod['is_scheduled'] == 1) ? date('Y-m-d', strtotime($prod['planned_start_date'])) : 'Unscheduled' }}</td>
			@endif
		</tr>
		@endforeach
	</tbody>
</table>