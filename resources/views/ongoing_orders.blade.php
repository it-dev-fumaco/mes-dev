<table class="table table-bordered table-hover table-striped">
    <col style="width: 8%;">
    <col style="width: 20%;">
    <col style="width: 10%;">
    <col style="width: 16%;">
    <col style="width: 10%;">
    <col style="width: 10%;">
    <col style="width: 10%;">
    <col style="width: 10%;">
    <col style="width: 6%;">
    <thead class="text-white text-center font-weight-bold text-uppercase bg-secondary" style="font-size: 11px;">
        <th class="p-2">Order No.</th>
        <th class="p-2">Customer</th>
        <th class="p-2">Order Type</th>
        <th class="p-2">Project</th>
        <th class="p-2">Date Approved</th>
        <th class="p-2">Delivery Date</th>
        <th class="p-2">Delivery Status</th>
        <th class="p-2">Prod. Status</th>
        <th class="p-2">Action</th>
    </thead>
    <tbody class="text-center" style="font-size: 12px;">
        @forelse ($order_list as $i => $r)
        @php
            $delivery_date = $r->reschedule_delivery == 1 ? $r->reschedule_delivery_date : $r->delivery_date;
        @endphp
        <tr>
            <td class="p-2 font-weight-bold">{{ $r->name }}</td>
            <td class="p-2">{{ $r->customer }}</td>
            <td class="p-2">{{ $r->order_type }}</td>
            <td class="p-2">{{ $r->project }}</td>
            <td class="p-2">
                <span class="d-block">{{ $r->date_approved ? \Carbon\Carbon::parse($r->date_approved)->format('M. d, Y') : '-' }}</span>
                @if ($r->date_approved)
                <small class="d-block">{{ \Carbon\Carbon::parse($r->date_approved)->format('Y-m-d') == \Carbon\Carbon::now()->format('Y-m-d') ? 'TODAY' : '' }}</small>
                @endif
            </td>
            <td class="p-2">
                @if ($delivery_date)
                @php
                    $delivery_status = str_replace(" and Bill", "", $r->status);
                    $delivery_status = str_replace("To Bill", "Delivered", $delivery_status);

                    $delivery_date_status = 'info';
                    if (Carbon\Carbon::now()->startOfDay() > Carbon\Carbon::parse($delivery_date)->endOfDay() && $delivery_status != 'Completed') {
                        $delivery_date_status = 'danger';
                    }
                @endphp
                <span class="d-block font-weight-bold">
                    {{ \Carbon\Carbon::parse($delivery_date)->format('M. d, Y') }}
                </span>
                @if ($delivery_date_status == 'danger')
                <span class="badge badge-{{ $delivery_date_status }}" style="font-size: 11px;">Delayed</span>
                @endif
                @else
                -
                @endif
            </td>
            <td class="p-2">
                @if ($r->status == 'Partially Ordered')
                Partially Delivered
                @else
                {{ $delivery_status }}
                @endif
            </td>
            <td class="p-2">
                @if (array_key_exists($r->name, $order_production_status))
                @php
                    $progress_bar_val = $order_production_status[$r->name]['percentage'];
                    $has_in_progress = $order_production_status[$r->name]['has_in_progress'];
                    if ($progress_bar_val < 100) {
                        $progress_bar_color = 'bg-warning';
                    } else {
                        $progress_bar_color = 'bg-success';
                    }

                    $production_orders = array_key_exists($r->name, $items_production_orders) ? $items_production_orders[$r->name] : [];
                @endphp
                @if ($has_in_progress && $progress_bar_val <= 0)
                    <span class="badge badge-warning" style="font-size: 11px;">In Progress</span>
                @elseif ($has_in_progress <= 0 && $progress_bar_val <= 0 && count($production_orders) <= 0)
                    <span class="badge badge-danger" style="font-size: 11px;">Not Started</span>
                @else
                <div class="progress">
                    <div class="progress-bar {{ $progress_bar_color }}" role="progressbar" style="width: {{ $progress_bar_val }}%" aria-valuenow="{{ $progress_bar_val }}" aria-valuemin="0" aria-valuemax="100">
                        <small>{{ $progress_bar_val }}%</small>
                    </div>
                </div>
                @endif
                @else
                -
                @endif
            </td>
            <td class="p-2">
                <button type="button" class="btn p-2 btn-info view-order-details-btn" data-id="{{ $r->name }}">Track Order</button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="text-center text-uppercase text-muted">No order(s) found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="m-2 ongoing-list-pagination">{{ $references_query->appends(Request::except('page'))->links() }}</div>
