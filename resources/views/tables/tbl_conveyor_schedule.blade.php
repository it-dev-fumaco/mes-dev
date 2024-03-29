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
						<col style="width: 5%;">
						<col style="width: 15%;">
						<col style="width: 25%;">
						<col style="width: 25%;">
						<col style="width: 10%;">
						<col style="width: 10%;">
						<col style="width: 10%;">
						<thead class="text-uppercase font-weight-bold" style="font-size: 9pt;">
							<tr>
								<td class="text-center">No.</td>
								<td class="text-center">Prod. Order</td>
								<td class="text-center">Customer</td>
								<td class="text-center">Item Details</td>
								<td class="text-center">Qty</td>
								<td class="text-center">Good</td>
								<td class="text-center">Bal.</td>
							</tr>
						</thead>
						<tbody style="font-size: 8pt;">
							@php
								$idx = 1;
							@endphp
							@forelse ($row['production_orders'] as $r)
							@php
								$c = '';
								if (in_array($r['production_order'], $row['wip_production_orders'])) {
									$c = 'active-process';
								}
								if($r['status'] == 'Not Started'){
									$b = 'secondary';
								}elseif($r['status'] == 'In Progress'){
									$b = 'warning';
								}else{
									$b = 'success';
								}
							@endphp
							<tr class="{{ $c }}">
								<td class="text-center align-middle">
									<h4 class="m-0 font-weight-bold">{{ $idx++ }}</h4>
								</td>
								<td class="text-center font-weight-bold">
									<span class="badge badge-{{ $b }} view-prod-details-btn" style="font-size: 9pt;">{{ $r['production_order'] }}</span>
								</td>
								<td class="text-center">
									<span class="d-block font-weight-bold">{{ $r['reference_no'] }}</span>
									<span class="d-block">{{ $r['customer'] }}</span>
									<span class="d-block">Project: {{ $r['project'] }}</span>
									<span class="d-block mt-1 font-weight-bold text-primary">{{ $r['classification'] }}</span>
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

<style>
	.active-process {
		background-color: #e69e0e;
		color: #000000;
		animation: blinkingBackground 2.5s linear infinite;
	}
	@keyframes blinkingBackground{
		0%    { background-color: #ffff;}
		25%   { background-color: #FFC107;}
		50%   { background-color: #ffff;}
		75%   { background-color: #FFC107;}
		100%  { background-color: #ffff;}
	}
</style>