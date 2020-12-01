<div class="row">
	<div class="col-md-6 pl-3">
		<h4 class=" font-weight-bold m-0">{{$production_order_details->production_order }}</h4>
		<span class="d-block">{{ date('M-d-Y h:i:A') }}</span>
		<dl class="row mt-2">          
			<dt class="col-sm-3">Reference</dt>
			<dd class="col-sm-9">{{ $production_order_details->sales_order }}{{ $production_order_details->material_request }} - {{ $production_order_details->customer }}</dd>
			
			<dt class="col-sm-3 text-truncate">Project</dt>
			<dd class="col-sm-9">{{ $production_order_details->project }}</dd>

			<dt class="col-sm-3">Item Details</dt>
			<dd class="col-sm-9 text-truncate"><b>{{ $production_order_details->item_code }}</b> {{ $production_order_details->description }}</dd>
		</dl>
		  
		<div class="card">
			<div class="card-body p-0 text-center" style="background-color: #263238;">
				<div class="row">
					<div class="col-md-4 text-white pr-0">
						<h5 class="m-0 p-1" style="background-color: #28b463; font-size: 12pt;">QUANTITY</h5>
						<span class="text-center d-block font-weight-bold" style="font-size: 25pt;">{{ number_format($production_order_details->qty_to_manufacture) }}</span>
						<span class="text-center d-block mb-2" style="font-size:8pt;">{{ $production_order_details->stock_uom }}</span>
					</div>
					<div class="col-md-4 text-white p-0">
						<h5 class="m-0 p-1" style="background-color: #28b463; font-size: 12pt;">COMPLETED</h5>
						<span class="text-center d-block font-weight-bold" style="font-size: 25pt;">{{ number_format($production_order_details->produced_qty) }}</span>
						<span class="text-center d-block mb-2" style="font-size:8pt;">{{ $production_order_details->stock_uom }}</span>
					</div>
					<div class="col-md-4 text-white pl-0">
						<h5 class="m-0 p-1" style="background-color: #28b463; font-size: 12pt;">FEEDBACKED</h5>
						<span class="text-center d-block font-weight-bold" style="font-size: 25pt;">{{ number_format($production_order_details->feedback_qty) }}</span>
						<span class="text-center d-block mb-2" style="font-size:8pt;">{{ $production_order_details->stock_uom }}</span>
					</div>
				</div>
			</div>
		</div>
    </div>
    <div class="col-md-6 pr-3 pt-4">
		<h5 class="title m-0 text-center font-weight-bold" style="font-size: 12pt;">
			{{ $production_order_details->wip_warehouse }} 
			<i class="now-ui-icons arrows-1_minimal-right"></i>
			{{ $production_order_details->fg_warehouse }}
		</h5>
		<div class="text-center">
			<span class="text-center d-block mt-3 mb-3">Current Stock: <b>{{ number_format($actual_qty) }}</b> {{ $production_order_details->stock_uom }}</span>
		</div>
		<div class="form-group text-center">
			<div class="row">
				<div class="col-md-8 offset-md-2">
					<p style="font-size: 11pt;" class="text-center m-0 font-weight-bold">Enter Qty</p>
					<input type="text" value="{{ $production_order_details->produced_qty - $production_order_details->feedback_qty }}" class="form-control form-control-lg mt-1" name="completed_qty" style="text-align: center; font-size: 20pt;">
					<small class="form-text text-muted">Maximum: <span>{{ $production_order_details->qty_to_manufacture - $production_order_details->feedback_qty }}</span></small>
					
					<button type="submit" class="btn btn-block btn-primary" id="submit-feedback-btn">Submit</button>
				</div>
			</div>
		</div>
   </div>
</div>

@if(count($list) > 0)
<h6 class="title text-center mt-4">Pending Material(s) / Component(s) for Issue</h6>
<div class="table-responsive">
    <table class="table table-striped">
        <col style="width: 9%;">
        <col style="width: 40%;">
        <col style="width: 14%;">
        <col style="width: 14%;">
        <col style="width: 8%;">
        <col style="width: 9%;">
        <col style="width: 6%;">
        <thead class="text-primary" style="font-size: 7pt;">
            <th class="text-center"><b>STE No.</b></th>
            <th class="text-center"><b>Item Code</b></th>
            <th class="text-center"><b>Source Warehouse</b></th>
            <th class="text-center"><b>Target Warehouse</b></th>
            <th class="text-center"><b>Qty</b></th>
            <th class="text-center"><b>Status</b></th>
            <th class="text-center"><b>Action</b></th>
        </thead>
        <tbody style="font-size: 8pt;">
            @forelse ($list as $row)
            <tr>
                <td class="text-center">{{ $row->name }}</td>
                <td class="text-justify">
                    <span class="d-block font-weight-bold">{{ $row->item_code }}</span>
                    <span class="d-block" style="font-size: 8pt;">{{ $row->description }}</span>
                </td>
                <td class="text-center">{{ $row->s_warehouse }}</td>
                <td class="text-center">{{ $row->t_warehouse }}</td>
                <td class="text-center">
                    <span class="d-block">{{ $row->qty * 1 }}</span>
                    <span class="d-block">{{ $row->stock_uom }}</span>
                </td>
                <td class="text-center">{{ $row->status }}</td>
                <td class="text-center">
                    <button class="btn btn-danger delete-pending-mtfm-btn" style="padding: 8px;" data-id="{{ $row->sted_id }}" data-production-order="{{ $production_order_details->production_order }}">Cancel</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="font-size: 10pt;">No Pending Materials for Issue</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

@if(count($feedbacked_log) > 0)
<h6 class="title text-center mt-2">Feedbacked Log/s</h6>
<div class="table-responsive">
	<table class="table table-striped">
		<col style="width: 25%;">
		<col style="width: 25%;">
		<col style="width: 25%;">
		<col style="width: 25%;">
		<thead class="text-primary" style="font-size: 7pt;">
			<th class="text-center"><b>STE No.</b></th>
			<th class="text-center"><b>Feedbacked Qty</b></th>
			<th class="text-center"><b>Transaction Date</b></th>
			<th class="text-center"><b>Feedbacked By</b></th>
		</thead>
		<tbody style="font-size: 8pt;">
			@forelse ($feedbacked_log as $row)
			<tr>
				<td class="text-center">{{ $row->ste_no }}</td>
				<td class="text-center">
					<span class="d-block font-weight-bold">{{ $row->feedbacked_qty }}</span>
				</td>
				<td class="text-center">

					<span class="d-block" style="font-size: 8pt;">{{ $row->created_at }}</span>
				</td>
				<td class="text-center">{{ $row->created_by }}</td>
			</tr>
			@empty
			<tr>
					<td colspan="7" class="text-center" style="font-size: 10pt;">No Record/s for Found</td>
			</tr>
			@endforelse
		</tbody>
	</table>
</div>
@endif