<table class="table table-bordered" style="font-size: 9pt;">
    <tr>
        <th rowspan=2 class="text-center p-1">Referece</th>
        <th rowspan=2 class="text-center p-1">DR No.</th>
        <th class="text-center p-1">Scheduled</th>
        <th class="text-center p-1">Rescheduled</th>
        <th class="text-center p-1">Actual</th>
        <th rowspan=2 class="text-center p-1">Created By</th>
        <th rowspan=2 class="text-center p-1">Created At</th>
        <th rowspan=2 class="text-center p-1">Customer</th>
        <th rowspan=2 class="text-center p-1">Status</th>
    </tr>
    <tr>
        <th colspan=3 class="text-center p-1">Delivery Date</th>
    </tr>
    @forelse ($sales_orders as $so)
        @php
            switch ($so->status) {
                case 'Completed':
                    $badge = 'success';
                    break;
                case 'Draft':
                    $badge = 'danger';
                    break;
                case 'To Bill':
                case 'To Deliver':
                case 'To Deliver and Bill':
                    $badge = 'warning';
                    break;
                default:
                    $badge = 'secondary';
                    break;
            }
        @endphp
        <tr>
            <td class="text-center p-1">{{ $so->reference }}</th>
            <td class="text-center p-1">
                {{ $so->dr_reference }}
                @if ($so->per_delivered == 100)
                    <span class="badge badge-success">Delivered</span>
                @endif
            </th>
            <td class="text-center p-1">{{ Carbon\Carbon::parse($so->scheduled_delivery_date)->format('M. d, Y') }}</th>
            <td class="text-center p-1">{{ $so->reschedule_delivery ? Carbon\Carbon::parse($so->reschedule_delivery_date)->format('M. d, Y') : '-' }}</th>
            <td class="text-center p-1">{{ $so->per_delivered == 100 ? Carbon\Carbon::parse($so->actual_delivery_date)->format('M. d, Y') : '-' }}</th>
            <td class="text-center p-1">{{ $so->owner }}</th>
            <td class="text-center p-1">{{ Carbon\Carbon::parse($so->creation)->format('M. d, Y') }}</th>
            <td class="text-center p-1">{{ $so->customer }}</th>
            <td class="text-center p-1">
                <span class="badge badge-{{ $badge }}">
                    {{ $so->status }}
                </span>
            </th>
        </tr>
    @empty
        <tr>
            <td colspan=20 class="text-center">No result(s) found</th>
        </tr>
    @endforelse
</table>

<div class="row">
    <div class="col-6">
        <h5><b>Total: {{ $sales_orders->total() }}</b></h5>
    </div>
    <div class="col-6">
        <div class="text-center mt-2 table-paginate pull-right">{{ $sales_orders->links() }}</div>
    </div>
</div>
