<table class="table table-bordered" style="font-size: 9pt;">
    <colgroup>
        <col style="width: 5%">{{-- Ref. No --}}
        <col style="width: 5%">{{-- Prod. Order --}}
        <col style="width: 35%">{{-- Item --}}
        <col style="width: 5%">{{-- Qty to Manufacture --}}
        <col style="width: 5%">{{-- Produced Qty --}}
        <col style="width: 5%">{{-- Feedbacked Qt --}}
        <col style="width: 10%">{{-- Creation --}}
        <col style="width: 10%">{{-- Target Warehouse --}}
        <col style="width: 10%">{{-- Delivery Date --}}
        <col style="width: 10%">{{-- Feedback Date --}}
    </colgroup>
    <tr>
        <th class="text-center p-1">Ref. No</th>
        <th class="text-center p-1">Prod. Order</th>
        <th class="text-center">Item</th>
        <th class="text-center p-1">Qty to Manufacture</th>
        <th class="text-center p-1">Produced Qty</th>
        <th class="text-center p-1">Feedbacked Qty</th>
        <th class="text-center p-1">Creation</th>
        <th class="text-center p-1">Target Warehouse</th>
        <th class="text-center p-1">Delivery Date</th>
        <th class="text-center p-1">Feedback Date</th>
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

            $created_by = explode('@', $po->created_by)[0];
            $created_by = ucwords(str_replace('.', ' ', $created_by));
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
            <td class="text-center p-1">
                {{ $created_by }} <br>
                <small>{{ Carbon\Carbon::parse($po->created_at)->format('M. d, Y - h:i A') }}</small>
            </td>
            <td class="text-center p-1">{{ $po->fg_warehouse }}</td>
            <td class="text-center p-1">
                {{ Carbon\Carbon::parse($po->delivery_date)->format('M. d, Y') }}
            </td>
            <td class="text-center p-1">
                @if (isset($feedback_logs[$po->production_order]))
                    @php
                        $feedback_log = $feedback_logs[$po->production_order][0];
                    @endphp
                    <small>
                        {{ Carbon\Carbon::parse($feedback_log->transaction_date.' '.$feedback_log->transaction_time)->format('M. d, Y - h:i A') }}
                    </small>
                    <br>
                    @if (Carbon\Carbon::parse($po->delivery_date)->lt(Carbon\Carbon::parse($feedback_log->transaction_date)))
                        <span class="badge badge-danger">Delayed</span>
                    @else
                        <span class="badge badge-success">On Time</span>
                    @endif
                @else
                    -
                @endif
            </td>
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
