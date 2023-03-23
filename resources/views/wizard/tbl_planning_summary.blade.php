@php
	$has_scheduled = collect($production_order_list)->sum('is_scheduled');
	$has_unplanned = collect($production_order_list)->pluck('unplanned_qty')->max();
@endphp

<table class="table table-hover table-bordered" style="font-size: 8pt;" id="summary-tbl">
	<col style="width: 12%;">
	<col style="width: 12%;">
	<col style="width: 42%;">
	<col style="width: 12%;">
	@if($has_scheduled > 0)
	<col style="width: 12%;">
	@endif
	<thead class="text-white bg-secondary">
		<th class="text-center" style="font-size: 9pt;"><b>Parent Code</b></th>
		<th class="text-center" style="font-size: 9pt;"><b>Production Order</b></th>
		<th class="text-center" style="font-size: 9pt;"><b>Item Code</b></th>
		<th class="text-center" style="font-size: 9pt;"><b>Planned Qty</b></th>
		@if ($has_unplanned > 0)
		<th class="text-center" style="font-size: 9pt;"><b>Unplanned Qty</b></th>
		@endif
		<th class="text-center" style="font-size: 9pt;"><b>Planned Prod. Orders</b></th>
		@if($has_scheduled > 0)
		<th class="text-center" style="font-size: 9pt;"><b>Planned Start Date</b></th>
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
			@if ($has_unplanned > 0)
			<td class="text-center">
				<span class="d-block font-weight-bold" style="font-size: 11pt;">{{ $prod['unplanned_qty'] > 0 ? number_format($prod['unplanned_qty']) : 0 }}</span>
				<span class="d-block">{{ $prod['stock_uom'] }}</span>
			</td>
			@endif
			<td class="text-center">
				@forelse ($prod['planned_production_orders'] as $production_order)
				@php
					switch ($production_order->status) {
						case "Material For Issue":
						case "Not Started":
							$badge_color ="danger";
							break;
						case "Material Issued":
							$badge_color ="primary";
							break;
						case "Ready For Feedback":
							$badge_color ="info";
							break;
						case "Partially Feedbacked":
						case "Feedbacked":
							$badge_color ="success";
							break;
						default:
							$badge_color ="warning";
							break;
					}
               	@endphp
				<p class="m-1 badge badge-{{ $badge_color }}" style="font-size: 8pt;">
					<span class="prod-view-btn">{{ $production_order->production_order }}</span>
					<span>({{ $production_order->qty_to_manufacture }})</span>
				</p>
				@empty
					<center>
						<span class="d-block">-</span>
					</center>
				@endforelse
			</td>
			@if($has_scheduled > 0)
			<td class="text-center font-weight-bold">{{ ($prod['is_scheduled'] == 1) ? date('Y-m-d', strtotime($prod['planned_start_date'])) : 'Unscheduled' }}</td>
			@endif
		</tr>
		@endforeach
	</tbody>
</table>