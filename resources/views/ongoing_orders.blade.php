<table class="table table-bordered table-hover table-striped">
    <col style="width: 6%;">
    <col style="width: 45%;">
    <col style="width: 8%;">
    <col style="width: 8%;">
    <col style="width: 10%;">
    <col style="width: 10%;">
    <col style="width: 8%;">
    <col style="width: 5%;">
    <thead class="text-white text-center font-weight-bold text-uppercase bg-secondary" style="font-size: 9px;">
        <th class="p-2">Order No.</th>
        <th class="p-2">Item Description</th>
        <th class="p-2">Ordered Qty</th>
        <th class="p-2">Delivered Qty</th>
        <th class="p-2">Delivery Status</th>
        <th class="p-2">Prod. Status</th>
        <th class="p-2">Feedbacked Qty</th>
        <th class="p-2">Action</th>
    </thead>
    <tbody class="text-center" style="font-size: 12px;">
        @forelse ($order_list as $e)
        @php
            $delivery_date = $e->reschedule_delivery ? $e->rescheduled_delivery_date : $e->delivery_date;
        @endphp
        <tr>
            <td class="p-2 font-weight-bold">
                <a href="#" class="view-order-details-btn" data-id="{{ $e->name }}">{{ $e->name }}</a>
            </td>
            <td class="p-2 text-justify">
                <span class="font-weight-bold">{{ $e->item_code }}</span> {!! strip_tags($e->description) !!}
            </td>
            <td class="p-2">
                <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ number_format($e->qty) }}</span>
                <small class="d-block">{{ $e->stock_uom }}</small>
            </td>
            <td class="p-2">
                <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ number_format($e->delivered_qty) }}</span>
                <small class="d-block">{{ $e->stock_uom }}</small>
            </td>
            @if ($delivery_date)
            @php
                $delivery_date_status = 'bg-info';
                if (\Carbon\Carbon::now()->startOfDay() > \Carbon\Carbon::parse($delivery_date)->endOfDay() && $e->delivered_qty < $e->qty) {
                    $delivery_date_status = 'bg-danger';
                }
            @endphp
            <td class="p-2 {{ $delivery_date_status }} text-white">
                @if ($delivery_date_status == 'bg-danger')
                <span class="text-uppercase font-weight-bold" style="font-size: 11px;">Delayed</span>
                @endif
                <small class="d-block font-weight-bold">
                    {{ \Carbon\Carbon::parse($delivery_date)->format('M. d, Y') }}
                </small>
            </td>
            @else
            <td class="p-2">--</td>
            @endif
            <td class="p-2">
                @php
                    $production_details = isset($items_production_orders[$e->name][$e->item_code]) ? $items_production_orders[$e->name][$e->item_code] : [];
                    $production_status = $production_details ? $production_details['status'] : '--';
                    $feedbacked_qty = $production_details ? $production_details['feedbacked_qty'] : 0;
                    $no_of_process = $production_details ? $production_details['no_of_process'] : 0;
                    $no_of_completed_process = $production_details ? $production_details['no_of_completed_process'] : 0;
                @endphp
                @if ($production_status == 'Not Started')
                <span class="badge badge-danger" style="font-size: 11px;">{{ $production_status }}</span>
                @elseif ($production_status == 'Ready for Feedback')
                <span class="badge badge-info" style="font-size: 11px;">{{ $production_status }}</span>
                @elseif ($production_status == 'Feedbacked')
                <span class="badge badge-success" style="font-size: 11px;">{{ $production_status }}</span>
                @elseif ($production_status == 'Idle' || $production_status == '--')
                <span class="badge badge-secondary" style="font-size: 11px;">{{ $production_status }}</span>
                @else
                <span class="badge badge-warning" style="font-size: 11px;">{{ $production_status }}</span>
                @endif
                @if (!in_array($production_status, ['Not Started', 'Feedbacked', '--']))
                <small class="d-block mt-2 font-weight-bolder">{{ $no_of_completed_process }} out of {{ $no_of_process }}</small>
                <span class="d-block text-uppercase" style="font-size: 9.5px;">Process Completed</span>
                @endif
            </td>
            <td class="p-2">
                <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ number_format($feedbacked_qty) }}</span>
                <small class="d-block">{{ $e->stock_uom }}</small>
            </td>
            <td class="p-2">
                <button class="btn btn-sm btn-info btn-icon btn_trackmodal" data-itemcode="{{ $e->item_code }}" data-guideid="{{ $e->name }}" data-erpreferenceno="{{ $e->child }}">
                    <i class="now-ui-icons ui-1_zoom-bold"></i>
                </button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center text-uppercase text-muted">No order(s) found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="m-2 ongoing-list-pagination">{{ $references_query->appends(Request::except('page'))->links() }}</div>
