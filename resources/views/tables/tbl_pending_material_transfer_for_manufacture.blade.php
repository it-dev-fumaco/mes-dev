@if(isset($message))
<div class="row">
	<div class="col-md-12">
		<div class="alert alert-primary text-center" role="alert">
			{!! $message !!}
		  </div>
	</div>
</div>
@else
<div class="row">
	<div class="col-md-12">
		<table style="width: 100%; border-collapse: collapse;" class="custom-table-1-2">
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
					<a href="#" class="font-weight-bold view-production-order-details" data-production-order="{{ $production_order_details->production_order }}" style="color: black;">{{ $production_order_details->production_order }}</a>
				</td>
				<td><b>{{ $production_order_details->sales_order }}{{ $production_order_details->material_request }}</b></td>
				<td>{{ $production_order_details->customer }}</td>
				<td>{{ $production_order_details->project }}</td>
				<td>{{ ($production_order_details->rescheduled_delivery_date == null)? $production_order_details->delivery_date: $production_order_details->rescheduled_delivery_date   }}</td>
				<td style="background-color: #263238;" class="text-white">
					<span class="d-block font-weight-bold" style="font-size: 12pt;">{{ $production_order_details->qty_to_manufacture }}</span>
					<span class="d-block" style="font-size: 8pt;">{{ $production_order_details->stock_uom }}</span>
				</td>
				<td style="background-color: #263238;" class="text-white">
					<span class="d-block font-weight-bold" style="font-size: 12pt;">{{ $production_order_details->produced_qty }}</span>
					<span class="d-block" style="font-size: 8pt;">{{ $production_order_details->stock_uom }}</span>
				</td>
				<td style="background-color: #263238;" class="text-white">
					<span class="d-block font-weight-bold" style="font-size: 12pt;">{{ $production_order_details->feedback_qty }}</span>
					<span class="d-block" style="font-size: 8pt;">{{ $production_order_details->stock_uom }}</span>
				</td>
			</tr>
			<tr style="font-size: 10pt;">
				<td colspan="8" class="text-center p-2"><span class="font-weight-bold">{{ $production_order_details->item_code }}</span> - {{ $production_order_details->description }}</td>
			</tr>
		</table>
	</div>
	
</div>
<div class="row mt-2">
	<div class="container-fluid">
		<!-- Tab navigation -->
		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item create-feedback-tab">
				<a class="nav-link active" data-toggle="tab" href="#create-feedback">Create Feedback</a>
			</li>
			<li class="nav-item create-feedback-tab">
				<a class="nav-link" data-toggle="tab" href="#view-materials" id="view-materials-tab-btn" data-production-order="{{ $production_order_details->production_order }}" data-item-code="{{ $production_order_details->item_code }}" data-qty="{{ number_format($production_order_details->qty_to_manufacture) }}" data-uom="{{ $production_order_details->stock_uom }}">View Issued Materials</a>
			</li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content"> 
			<div id="create-feedback" class="container-fluid tab-pane active">
				<div class="row">
					<div class="col-md-12 pr-5 pl-5 pt-2 pb-1 m-0">
						@if(count($list) > 0)
						<div class="alert alert-warning text-center mb-0 mt-1" role="alert">
							<div class="container">
								<strong>Warning:</strong> There are pending materials for issue in this production order.
							</div>
						</div>
						@endif
					</div>
					<div class="col-md-5 offset-md-2 p-0">
						<table style="width: 100%;" class="mb-0 mr-0 ml-0 mt-3">
							<tr>
								<td>
									<span class="d-block text-center font-italic" style="font-size: 10pt;">Source Warehouse</span>
								</td>
								<td></td>
								<td>
									<span class="d-block text-center font-italic" style="font-size: 10pt;">Target Warehouse</span>
								</td>
							</tr>
							<tr>
								<td>
									<h5 class="title m-0 text-center font-weight-bold" style="font-size: 12pt;">{{ $production_order_details->wip_warehouse }}</h5>
								</td>
								<td>
									<h5 class="title m-0 text-center font-weight-bold" style="font-size: 12pt;"><i class="now-ui-icons arrows-1_minimal-right"></i></h5>
								</td>
								<td>
									<h5 class="title m-0 text-center font-weight-bold" style="font-size: 12pt;">{{ $production_order_details->fg_warehouse }}</h5>
								</td>
							</tr>
							<tr>
								<td colspan="3">
									<div class="text-center">
										<span class="text-center d-block mt-3 mb-3">Current Stock: <b>{{ number_format($actual_qty) }}</b> {{ $production_order_details->stock_uom }}</span>
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div class="col-md-3">
						<div class="form-group text-center m-2">
							<div class="row">
								<div class="col-md-12">
									<input type="text" value="{{ $production_order_details->produced_qty - $production_order_details->feedback_qty }}" class="form-control form-control-lg mt-1" name="completed_qty" style="text-align: center; font-size: 20pt;">
									<small class="form-text text-muted">Maximum: <span>{{ $production_order_details->qty_to_manufacture - $production_order_details->feedback_qty }}</span></small>
									
									<button type="submit" class="btn btn-block btn-primary" id="submit-feedback-btn">Submit</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="container-fluid">
					@if(count($list) > 0 || count($feedbacked_logs) > 0)

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
								if(count($feedbacked_logs) > 0){
									$tab3 = 'active';
								}
							}
						@endphp

						<ul class="nav nav-tabs font-weight-bold" role="tablist">
							@if(count($components) > 0)
							<li class="nav-item">
								<a class="nav-link {{ $tab1 }}" data-toggle="tab" href="#w11" role="tab" aria-controls="home" aria-selected="true">
									<span class="badge badge-info mr-2">{{ count($components) }}</span> Component(s) 
								</a>
							</li>
							@endif
							@if(count($parts) > 0)
							<li class="nav-item">
								<a class="nav-link {{ $tab2 }}"  data-toggle="tab" href="#w22" role="tab" aria-controls="profile" aria-selected="false">
									<span class="badge badge-info mr-2">{{ count($parts) }}</span>	Part(s) 
								</a>
							</li>
							@endif
							@if(count($feedbacked_logs) > 0)
							<li class="nav-item">
								<a class="nav-link {{ $tab3 }}" data-toggle="tab" href="#w33" role="tab" aria-controls="messages" aria-selected="false">
									<span class="badge badge-info mr-2">{{ count($feedbacked_logs) }}</span> Feedbacked Log(s)
								</a> 
							</li>
							@endif
						</ul>

						<div class="tab-content bg-light" style="border: 1px solid #f2f3f4;">
							<div class="tab-pane {{ $tab1 }}" id="w11" role="tabpanel" aria-labelledby="w1-tab">
								@if(count($components) > 0)
								<table style="width: 100%; border-collapse: collapse;" class="custom-table-1-2">
									<col style="width: 9%;">
									<col style="width: 33%;">
									<col style="width: 17%;">
									<col style="width: 17%;">
									<col style="width: 8%;">
									<col style="width: 8%;">
									<col style="width: 6%;">
									<thead style="font-size: 7pt;">
										<th class="text-center"><b>STE No.</b></th>
										<th class="text-center"><b>Item Code</b></th>
										<th class="text-center"><b>Source Warehouse</b></th>
										<th class="text-center"><b>Target Warehouse</b></th>
										<th class="text-center"><b>Qty</b></th>
										<th class="text-center"><b>Status</b></th>
										<th class="text-center"><b>Action</b></th>
									</thead>
									<tbody style="font-size: 9pt;">
										@foreach ($components as $i => $component)
										<tr>
											<td class="text-center">{{ $component['name'] }}</td>
											<td class="text-justify">
												<span class="d-block font-weight-bold item-code">{{ $component['item_code'] }}</span>
												<span class="d-block" style="font-size: 8pt;">{{ $component['description'] }}</span>
											</td>
											<td class="text-center">
												@php
													$b1 = ($component['available_qty_at_source'] > 0) ? 'badge badge-success' : 'badge badge-danger';
													$b2 = ($component['available_qty_at_wip'] > 0) ? 'badge badge-success' : 'badge badge-danger';
												@endphp
												<span class="d-block">{{ $component['s_warehouse'] }}</span>
												<span class="{{ $b1 }}" style="font-size: 8pt;">Current Qty: {{ $component['available_qty_at_source'] }}</span>
											</td>
											<td class="text-center">
												<span class="d-block">{{ $component['t_warehouse'] }}</span>
												<span class="{{ $b2 }}" style="font-size: 8pt;">Current Qty: {{ $component['available_qty_at_wip'] }}</span>
											</td>
											<td class="text-center">
												<span class="d-block font-weight-bold">{{ $component['qty'] * 1 }}</span>
												<span class="d-block">{{ $component['stock_uom'] }}</span>
											</td>
											<td class="text-center">{{ $component['status'] }}</td>
											<td class="text-center">
												<button class="btn btn-danger delete-required-item-btn" style="padding: 8px;" data-production-order="{{ $production_order_details->production_order }}">Cancel</button>
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
								@else
								<h5 class="text-center m-4">No withdrawal slip(s) created for item component(s).</h5>
								@endif
							</div>
							<div class="tab-pane {{ $tab2 }}" id="w22" role="tabpanel" aria-labelledby="w2-tab">
								@if(count($parts) > 0)
								<table style="width: 100%; border-collapse: collapse;" class="custom-table-1-2">
									<col style="width: 9%;">
									<col style="width: 33%;">
									<col style="width: 17%;">
									<col style="width: 17%;">
									<col style="width: 8%;">
									<col style="width: 8%;">
									<col style="width: 6%;">
									<thead style="font-size: 7pt;">
										<th class="text-center"><b>Prod. Order</b></th>
										<th class="text-center"><b>Item Code</b></th>
										<th class="text-center"><b>Source Warehouse</b></th>
										<th class="text-center"><b>Target Warehouse</b></th>
										<th class="text-center"><b>Qty</b></th>
										<th class="text-center"><b>Status</b></th>
										<th class="text-center"><b>Action</b></th>
									</thead>
									<tbody style="font-size: 9pt;">
									@foreach ($parts as $i => $part)
									<tr>
										<td class="text-center">
											<span class="view-production-order-details font-weight-bold" data-production-order="{{ $part['production_order'] }}">{{ $part['production_order'] }}</span>
										</td>
										<td class="text-justify">
											<span class="d-block font-weight-bold item-code">{{ $part['item_code'] }}</span>
											<span class="d-block" style="font-size: 8pt;">{{ $part['description'] }}</span>
										</td>
										<td class="text-center">
											@php
												$b1 = ($part['available_qty_at_source'] > 0) ? 'badge badge-success' : 'badge badge-danger';
												$b2 = ($part['available_qty_at_wip'] > 0) ? 'badge badge-success' : 'badge badge-danger';
											@endphp
											<span class="d-block">{{ $part['s_warehouse'] }}</span>
											<span class="{{ $b1 }}" style="font-size: 8pt;">Current Qty: {{ $part['available_qty_at_source'] }}</span>
										</td>
										<td class="text-center">
											<span class="d-block">{{ $part['t_warehouse'] }}</span>
											<span class="{{ $b2 }}" style="font-size: 8pt;">Current Qty: {{ $part['available_qty_at_wip'] }}</span>
										</td>
										<td class="text-center">
											<span class="d-block font-weight-bold">{{ $part['qty'] * 1 }}</span>
											<span class="d-block">{{ $part['stock_uom'] }}</span>
										</td>
										<td class="text-center">{{ $part['status'] }}</td>
										<td class="text-center">
											<button class="btn btn-danger delete-required-item-btn" style="padding: 8px;" data-production-order="{{ $production_order_details->production_order }}">Cancel</button>
										</td>
									</tr>
									@endforeach
									</tbody>
								</table>
								@else
								<h5 class="text-center m-4">No withdrawal slip(s) created for item component(s).</h5>
								@endif
							</div>
							<div class="tab-pane {{ $tab3 }}" id="w33" role="tabpanel" aria-labelledby="w4-tab">
								@if(count($feedbacked_logs) > 0)
								<div class="row">
									<div class="col-md-8 offset-md-2">
										<table style="width: 100%; border-collapse: collapse; margin-top: 10px;" class="custom-table-1-2">
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
													<span class="font-weight-bold">{{ $log->feedbacked_qty }}</span> {{ $production_order_details->stock_uom }}</td>
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
						@endif


						<style>
							.custom-table-1-2{
								border: 1px solid #ABB2B9;
							}

							.custom-table-1-2 th{
								background-color: #D5D8DC;
								text-transform: uppercase;
								font-size: 9pt;
							}

							.custom-table-1-2 th, .custom-table-1-2 td{
								padding: 3px;
								border: 1px solid #ABB2B9;
							}
						</style>

					@endif
				</div>
			</div>
			<div id="view-materials" class="container-fluid tab-pane"><div>
		</div>
	</div>
</div>
<style>
	.create-feedback-tab .active{
		background-color: #F96332 !important;
		color: #fff !important;
	}
</style>
<script>
	$(document).ready(function(){
		$('#view-materials-tab-btn').click(function(){
			production_order = $(this).data('production-order');

			$.ajax({
				type:'GET',
 				url:'/get_production_order_items/' + production_order, 
				success: function (response) {
					$('#view-materials').html(response);
					$('.exclude-table').addClass('d-none');
				}
			});
		});
	});
</script>