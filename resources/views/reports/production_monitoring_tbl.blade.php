@php
if ($export){
    header("Content-Disposition: attachment; filename=Production Orders Report.xls");
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
}
@endphp
<table class="table table-bordered" style="font-size: 8pt;" border=1>
    <tr>
        <th class="text-center" style="width: 6%">Date Created</th>
        <th class="text-center" style="width: 6%">Date Approved</th>
        <th class="text-center">Created By</th>
        <th class="text-center" style="width: 6%">Reference No.</th>
        <th class="text-center" style="width: 6%">PROM#</th>
        <th class="text-center" style="width: 13%">Customer</th>
        <th style="width: 20%">Item</th>
        <th class="text-center">Qty</th>
        <th class="text-center" style="width: 6%">Delivery Date</th>
        <th class="text-center" style="width: 6%">Rescheduled Delivery Date</th>
        <th class="text-center" style="width: 6%">Feedback Date/Time</th>
        <th class="text-center" style="width: 6%">Remarks</th>
        <th class="text-center">Status</th>
    </tr>
    @forelse($production_orders as $po)
    @php
    $reference_details = $date_approved = $date_created = $sales_person = $customer = $delivery_date = $rescheduled_delivery_date = $status = null;
    if(isset($references[$po->reference_id][$po->parent_item_code])){
        $reference_details = $references[$po->reference_id][$po->parent_item_code][0];
        $date_approved = Carbon\Carbon::parse($reference_details->date_approved)->format('M. d, Y');
        $date_created = Carbon\Carbon::parse($reference_details->creation)->format('M. d, Y - h:i A');
        $exploded_name = explode('@', $reference_details->owner)[0];
        $sales_person = str_replace('.', ' ', $exploded_name);
        $customer = $reference_details->customer;
        $delivery_date = Carbon\Carbon::parse($reference_details->delivery_date)->format('M. d, Y');
        $rescheduled_delivery_date = $delivery_date != Carbon\Carbon::parse($reference_details->rescheduled_delivery_date)->format('M. d, Y') ? Carbon\Carbon::parse($reference_details->rescheduled_delivery_date)->format('M. d, Y') : '-';
        $status = $reference_details->status;
    }

    $feedback_date = isset($feedback_logs[$po->production_order]) ? Carbon\Carbon::parse($feedback_logs[$po->production_order][0]->created_at)->format('M. d, Y - h:i A') : '-';
    @endphp
    <tr>
        <td class="text-center">{{ $date_created }}</td>
        <td class="text-center">{{ $date_approved }}</td>
        <td class="text-center">
            <span style="text-transform: capitalize">{{ $sales_person }}</span>
        </td>
        <td class="text-center">{{ $po->reference_id }}</td>
        <td class="text-center">{{ $po->production_order }}</td>
        <td class="text-center">{{ $customer }}</td>
        <td class="text-justify">
            <b>{{ $po->item_code }}</b> - {{ strip_tags($po->description) }}
        </td>
        <td class="text-center">{{ $po->qty_to_manufacture.' '.$po->stock_uom }}</td>
        <td class="text-center">{{ $delivery_date }}</td>
        <td class="text-center">{{ $rescheduled_delivery_date }}</td>
        <td class="text-center">{{ $feedback_date }}</td>
        <td class="text-center">{{ $po->remarks ? $po->remarks : '-' }}</td>
        <td class="text-center">{{ $status }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="13" class="text-center">No result(s) found.</td>
    </tr>
    @endforelse
</table>

<style>
    th{
        background-color: #FFFF00 !important;
        border: 1px solid #DEE2E6 !important;
    }
</style>

@if (!$export)
<div id="pagination" class="float-right mt-4">
    {!! $production_orders->appends(request()->query())->links('pagination::bootstrap-4') !!}
</div>
@endif