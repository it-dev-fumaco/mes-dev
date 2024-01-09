<table class="table table-bordered" style="font-size: 9pt;">
    <tr>
        <th class="text-center p-1">Ref. No</th>
        <th class="text-center p-1">Prod. Order</th>
        <th class="text-center">Item</th>
        <th class="text-center p-1">Qty to Manufacture</th>
        <th class="text-center p-1">Produced Qty</th>
        <th class="text-center p-1">Feedbacked Qty</th>
        <th class="text-center p-1">Created By</th>
        <th class="text-center p-1">Created At</th>
        <th class="text-center p-1">Target Warehouse</th>
    </tr>
    @forelse ($production_orders as $po)
        @php
            switch($po->status){
                case 'In Progress':
                    $badge = 'warning';
                    break;
                case 'Feedbacked':
                case 'Partially Feedbacked':
                    $badge = 'success';
                    break;
                case 'Completed':
                case 'Ready for Feedback':
                case 'For Partial Feedback':
                    $badge = 'info';
                    break;
                case 'Cancelled':
                case 'Closed':
                    $badge = 'danger';
                    break;
                default:
                    $badge = 'secondary';
                    break;
                }
        @endphp
        <tr>
            <td class="text-center p-1">{{ $po->sales_order ? $po->sales_order : $po->material_request }}</td>
            <td class="text-center p-1">
                {{ $po->production_order }} <br>
                <span class="badge badge-{{ $badge }}">{{ $po->status }}</span>
            </td>
            <td class="text-justify p-1" style="font-size: 9pt;"><b>{{ $po->item_code }}</b> - {{ strip_tags($po->description) }}</td>
            <td class="text-center p-1"><b>{{ number_format($po->qty_to_manufacture) }}</b><br/>{{ $po->stock_uom }}</td>
            <td class="text-center p-1"><b>{{ number_format($po->produced_qty) }}</b><br/>{{ $po->stock_uom }}</td>
            <td class="text-center p-1"><b>{{ number_format($po->feedback_qty) }}</b><br/>{{ $po->stock_uom }}</td>
            <td class="text-center p-1">{{ $po->created_by }}</td>
            <td class="text-center p-1">{{ Carbon\Carbon::parse($po->created_at)->format('M. d, Y - h:i A') }}</td>
            <td class="text-center p-1">{{ $po->fg_warehouse }}</td>
        </tr>
    @empty
        <tr>
            <td colspan=9 class="text-center">No result(s) found</td>
        </tr>
    @endforelse
</table>

<div class="row">
    <div class="col-6">
        <h5><b>Total: {{ $production_orders->total() }}</b></h5>
    </div>
    <div class="col-6">
        <div class="text-center mt-2 table-paginate pull-right">{{ $production_orders->links() }}</div>
    </div>
</div>
