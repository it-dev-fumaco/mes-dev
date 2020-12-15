<<<<<<< HEAD
@php
if($prod_details->rescheduled_delivery_date == null){
   $deli=  $prod_details->delivery_date;
}else{
	$deli= $prod_details->rescheduled_delivery_date;
}
@endphp
=======
>>>>>>> parent of b84899c... Merge pull request #10 from it-dev-fumaco/MES-717
<div class="row">
	<div class="col-md-12">
		<h5 class=" font-weight-bold m-0">{{ $prod_details->sales_order }}{{ $prod_details->material_request }}</h5>
		<span class="d-block" style="font-size:14px;"><b>{{ $prod_details->customer}}</b> </span>

		<span class="d-block" style="font-size:13px;"><i>{{ $prod_details->production_order }} - {{ $prod_details->item_code }}</i> {{ $prod_details->description }}</span>
		<span class="d-block" style="font-size:12px;">{{ $prod_details->qty_to_manufacture }}</b> {{ $prod_details->stock_uom }}</span>


   </div>
</div>
<div class="row">
   @php
	 if($prod_details->rescheduled_delivery_date == null){
		$deli=  $prod_details->deli;
	 }else{
		 $deli= $prod_details->rescheduled_delivery_date;

	 }
   @endphp
   <div class="col-md-6 ">
   		<div class="form-group text-center">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
                        <label><b>Reschedule Delivery Reason:</b></label>
                        <select class="form-control" name="reason_id" id="reched_reason" required>
                            @foreach($reason as $row)
                              <option value="{{ $row->reschedule_reason_id }},{{$row->reschedule_reason}}"> {{$row->reschedule_reason}}</option>
                            @endforeach
                        </select>
                     </div>
				</div>
				<div class="col-md-12">
					<p style="font-size: 11pt;" class="text-center"><b>Remarks:</b></p>
						<textarea class="form-control" name="remarks" rows="3"></textarea>
						<textarea type="text" style="text-align:justify; display:none;" name="logs">
							@foreach($data as $row)
                              <span>>> {{$row['delivery_date'] }}</span><br>
							  <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$row['delivery_reason'] }} - {{$row['remarks'] }}</span><br>
                            @endforeach
						</textarea>
				</div>
			</div>
		</div>
   </div>
   <div class="col-md-6">
		<div class="text-center">
			<span class="text-center d-block mt-3 mb-3">Current Delivery Date: </b> {{ $deli }}</span>
			<input type="hidden" value="{{ $deli }}" name="delivery_date">
			<input type="hidden" value="{{ $prod_details->production_order }}" name="production_order">
			<input type="hidden" value="{{ $prod_details->planned_start_date }}" name="planned_start_date">

		</div>
		
		<div class="form-group text-center">
			<div class="row">
				<div class="col-md-8 offset-md-2">
					<p style="font-size: 11pt;" class="text-center"><b>Reschedule Date</b></p>
					<input type="date"  class="form-control" name="reschedule_date" style="text-align: center; font-size: 15t;color:black;" required>					
				</div>
			</div>
		</div>

		
   </div>
</div>