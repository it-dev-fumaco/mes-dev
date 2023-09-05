@php
if ($export){
    header("Content-Disposition: attachment; filename=Production Orders Report.xls");
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
}
@endphp
<table class="table table-bordered" style="font-size: 9pt;" border=1>
    <tr>
        <th>Date Created (SO/MREQ)</th>
        <th>Date Approved</th>
        <th>Sales Officer</th>
        <th>Reference No.</th>
        <th>PROM#</th>
        <th>Customer</th>
        <th>Item</th>
        <th>Qty</th>
        <th>Delivery Date</th>
        <th>Rescheduled Delivery Date</th>
        <th>Feedback Date/Time</th>
        <th>Remarks</th>
        <th>Status</th>
    </tr>
    @forelse($production_orders as $po)
    @php
    $reference_details = $date_approved = $date_created = $sales_person = $customer = $delivery_date = $rescheduled_delivery_date = $status = null;
    if(isset($references[$po->reference_id][$po->parent_item_code])){
        $reference_details = $references[$po->reference_id][$po->parent_item_code][0];
        $date_approved = Carbon\Carbon::parse($reference_details->date_approved)->format('M. d, Y');
        $date_created = Carbon\Carbon::parse($reference_details->creation)->format('M. d, Y - h:i A');
        $sales_person = $reference_details->sales_person;
        $customer = $reference_details->customer;
        $delivery_date = Carbon\Carbon::parse($reference_details->delivery_date)->format('M. d, Y');
        $rescheduled_delivery_date = $reference_details->reschedule_delivery ? Carbon\Carbon::parse($reference_details->rescheduled_delivery_date)->format('M. d, Y') : '-';
        $status = $reference_details->status;
    }

    $feedback_date = isset($feedback_logs[$po->production_order]) ? Carbon\Carbon::parse($feedback_logs[$po->production_order][0]->created_at)->format('M. d, Y - h:i A') : '-';
    @endphp
    <tr>
        <td>{{ $date_created }}</td>
        <td>{{ $date_approved }}</td>
        <td>{{ $sales_person }}</td>
        <td>{{ $po->reference_id }}</td>
        <td>{{ $po->production_order }}</td>
        <td>{{ $customer }}</td>
        <td>{!! '<b>'.$po->item_code.'</b> - '.$po->description !!}</td>
        <td>{{ $po->qty_to_manufacture.' '.$po->stock_uom }}</td>
        <td>{{ $delivery_date }}</td>
        <td>{{ $rescheduled_delivery_date }}</td>
        <td>{{ $feedback_date }}</td>
        <td>{{ $po->remarks ? $po->remarks : '-' }}</td>
        <td>{{ $status }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="13" class="text-center">No result(s) found.</td>
    </tr>
    @endforelse
</table>
@if (!$export)
<div id="pagination" class="float-right mt-4">
    {!! $production_orders->appends(request()->query())->links('pagination::bootstrap-4') !!}
</div>
@endif