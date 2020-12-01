<ul class="nav nav-tabs">
	@foreach($conveyor_schedule_list as $i => $row)
	<li class="nav-item">
		<a class="nav-link {{ ($loop->first) ? 'active' : '' }}" data-toggle="tab" href="#tab_{{ $i }}" role="tab">{{ $row['machine_name'] }} <span class="badge badge-info">{{ count($row['production_orders']) }}</span></a>
	</li>
	@endforeach                  
</ul>

<div class="tab-content">
	@foreach($conveyor_schedule_list as $i => $row)
	<div class="tab-pane {{ ($loop->first) ? 'active' : '' }}" id="tab_{{ $i }}" role="tabpanel" aria-labelledby="search_tab">
		<div class="row">
			<div class="col-md-12 p-1">
				<div class="table-responsive pr-1 pl-1" style="max-height: 580px;">
					<table class="table table-bordered table-condensed">
						<col style="width: 15%;">
						<col style="width: 25%;">
						<col style="width: 30%;">
						<col style="width: 10%;">
						<col style="width: 10%;">
						<col style="width: 10%;">
						<thead class="text-uppercase font-weight-bold" style="font-size: 9pt;">
							<tr>
							<td class="text-center">Prod. Order</td>
							<td class="text-center">Customer</td>
							<td class="text-center">Item Details</td>
							<td class="text-center">Qty</td>
							<td class="text-center">Good</td>
							<td class="text-center">Bal.</td>
						</tr>
						</thead>
						<tbody style="font-size: 8pt;">
							@forelse ($row['production_orders'] as $r)
							<tr>
								<td class="text-center font-weight-bold">
									@php
										if($r['status'] == 'Not Started'){
											$b = 'secondary';
										}elseif($r['status'] == 'In Progress'){
											$b = 'warning';
										}else{
											$b = 'success';
										}
									@endphp
									<span class="badge badge-{{ $b }} view-prod-details-btn" style="font-size: 9pt;">{{ $r['production_order'] }}</span>
								</td>
								<td class="text-center">
									<span class="d-block font-weight-bold">{{ $r['reference_no'] }}</span>
									<span class="d-block">{{ $r['customer'] }}</span>
									<span class="d-block">Project: {{ $r['project'] }}</span>
								</td>
								<td class="text-justify">
									<span class="font-weight-bold">{{ $r['item_code'] }}</span> - 
									<span>{{ $r['description'] }}</span>
								</td>
								<td class="text-center">
									<span class="d-block font-weight-bold" style="font-size: 10pt;">{{ $r['qty_to_manufacture'] }}</span>
									<span class="d-block">{{ $r['stock_uom'] }}</span>
								</td>
								<td class="text-center">
									<span class="d-block font-weight-bold" style="font-size: 10pt;">{{ $r['good'] }}</span>
									<span class="d-block">{{ $r['stock_uom'] }}</span>
								</td>
								<td class="text-center">
									<span class="d-block font-weight-bold" style="font-size: 10pt;">{{ $r['balance'] }}</span>
									<span class="d-block">{{ $r['stock_uom'] }}</span>
								</td>
							</tr>
							@empty
							<tr>
								<td colspan="7" class="text-center font-weight-bold" style="font-size: 10pt;">No production order(s) found</td>
							</tr>
							@endforelse
						</tbody>
				  </table>
				</div>
			</div>
		</div>
	</div>
	@endforeach
</div>
