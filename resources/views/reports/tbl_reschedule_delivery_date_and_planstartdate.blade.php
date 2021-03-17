<div class="row">
	<div class="col-md-12">
        <table class="table table-bordered">
            <thead style="font-size:11px;font-weight:bold;">
                <th><b>Reference </b></th>
                <th class="text-center"><b>Item</b></th>
                <th class="text-center"><b>Qty</b></th>
                <th class="text-center"><b>Delivery Date</b></th>
            </thead>
            <tbody>
                @foreach($prod_orders as $row)
                <tr class="m-0">
                    <td><b>{{($row->sales_order == null) ? $row->material_request : $row->sales_order}} </b><br><i> {{$row->customer}}</i></td>
                    <td class="text-center">{{$row->item_code}}</td>
                    <td class="text-center">{{$row->qty_to_manufacture}}</td>
                    <td class="text-center">{{$row->rescheduled_delivery_date}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
   </div>
</div>
<div class="row">
	<input type="hidden" value="{{ $prod_implode }}" name="production_order">
    <input type="hidden" value="{{ $planned_start_date }}" name="planned_start_date">

   	<div class="col-md-12" style="margin-top:15px;">
   		<div class="form-group text-center">
			<div class="row">
                <div class="col-md-6">
					<div class="form-group">
                        <label><b>Reschedule Date:</b></label>
						<input type="date"  class="form-control" name="reschedule_date" style="text-align: center; font-size: 10pt;color:black;" required>					
                    </div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
                        <label><b>Reason:</b></label>
                        <select class="form-control" name="reason_id" id="reched_reason" required>
                            @foreach($reason as $row)
                              <option value="{{ $row->reschedule_reason_id }},{{$row->reschedule_reason}}"> {{$row->reschedule_reason}}</option>
                            @endforeach
                        </select>
                     </div>
				</div>
				<div class="col-md-12">
					<p style="font-size: 11pt;" class="text-center"><b>Remarks:</b></p>
					<textarea class="form-control" name="remarks" rows="3" style="border:1px solid #c0c0c0;">

                    </textarea>
                    @foreach($trans_history as $r)
                        <textarea type="text" style="text-align:justify;display:none;" name="historylogs[]">
                            @foreach($r['data'] as $row)
                                <span>>> {{$row['delivery_date'] }}</span><br>
                                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$row['delivery_reason'] }} - {{$row['remarks'] }}</span><br>
                            @endforeach
                        </textarea>
                    @endforeach
					
				</div>
			</div>
		</div>
   </div>
</div>