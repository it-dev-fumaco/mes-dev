<table class="table table-bordered table-hover">
    <col style="width: 10%;">
    <col style="width: 20%;">
    <col style="width: 10%;">
    <col style="width: 16%;">
    <col style="width: 10%;">
    <col style="width: 10%;">
    <col style="width: 10%;">
    <col style="width: 10%;">
    <col style="width: 4%;">
    <thead class="text-white text-center font-weight-bold text-uppercase bg-secondary" style="font-size: 6pt;">
        <th class="p-2">Order No.</th>
        <th class="p-2">Customer</th>
        <th class="p-2">Order Type</th>
        <th class="p-2">Project</th>
        <th class="p-2">Date Approved</th>
        <th class="p-2">Delivery Date</th>
        <th class="p-2">Delivery Status</th>
        <th class="p-2">Prod. Status</th>
        <th class="p-2">-</th>
    </thead>
    <tbody class="text-center" style="font-size: 8pt;">
        @forelse ($list as $r)
        @php
            $delivery_date = $r->reschedule_delivery == 1 ? $r->reschedule_delivery_date : $r->delivery_date;
            $start = \Carbon\Carbon::now()->subMinutes(5);
		    $end = \Carbon\Carbon::now();
            $is_modified = false;
            $check = \Carbon\Carbon::parse($r->modified)->between($start, $end);
            if ($check && (!array_key_exists($r->name, $order_production_status))) {
                $is_modified = true;
            }
        @endphp
        <tr class="{{ !in_array($r->name, $seen_order_logs) ? 'font-weight-bold' : ''}} {{ $is_modified && !in_array($r->name, $seen_order_logs) ? 'is-new-order' : '' }}">
            <td class="p-2">{{ $r->name }}</td>
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
                <span class="badge badge-{{ Carbon\Carbon::now()->startOfDay() > Carbon\Carbon::parse($delivery_date)->endOfDay() ? 'danger' : 'info' }}" style="font-size: 7pt;">
                    {{ \Carbon\Carbon::parse($delivery_date)->format('M. d, Y') }}
                </span>
                @else
                -
                @endif
            </td>
            <td class="p-2">{{ $r->status }}</td>
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
                    <span class="badge badge-warning" style="font-size: 7pt;">In Progress</span>
                @elseif ($has_in_progress <= 0 && $progress_bar_val <= 0 && count($production_orders) <= 0)
                    <span class="badge badge-danger" style="font-size: 7pt;">Not Started</span>
                @else
                <div class="progress">
                    <div class="progress-bar {{ $progress_bar_color }}" role="progressbar" style="width: {{ $progress_bar_val }}%" aria-valuenow="{{ $progress_bar_val }}" aria-valuemin="0" aria-valuemax="100">
                        <small>{{ $progress_bar_val }}%</small>
                    </div>
                </div>
                @endif
                @else
                <span style="font-size: 8.5pt;">{!! in_array($r->name, $seen_order_logs) ? '<img src="'. asset('/img/view-icon.png') . '" width="18"> Viewed' : '-' !!}</span>
                @endif
            </td>
            <td class="p-1">
                <div class="btn-group dropleft">
                    <button type="button" class="btn p-2 btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Action</button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item view-order-details-btn" href="#" data-id="{{ $r->name }}">View Order</a>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#reschedule-{{ strtolower($r->name) }}-Modal">Reschedule Delivery Date</a>
                    </div>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="text-center text-uppercase text-muted">No order(s) found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="m-2 order-list-pagination">{{ $list->appends(Request::except('page'))->links() }}</div>

@foreach ($list as $i => $s)
    @php
        $delivery_date = $s->reschedule_delivery == 1 ? $s->reschedule_delivery_date : $s->delivery_date;
    @endphp
<!-- Modal -->
<div class="modal fade" id="reschedule-{{ strtolower($s->name) }}-Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
                <h5 class="modal-title" id="exampleModalLabel">Reschedule Delivery Date - <b>{{ $s->name }}</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/reschedule_delivery/{{ $s->name }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="container-fluid">
                        <p>Delivery Date: {{ Carbon\Carbon::parse($delivery_date)->format('M d, Y') }}</p>
                        <input type="text" name="previous_date" class="d-none" value="{{ $delivery_date }}">
                        <label for="rescheduled_date">New Delivery Date</label>
                        <input type="date" name="rescheduled_date" class="form-control rounded" min="{{ Carbon\Carbon::now()->format('Y-m-d') }}" required>
                        <br>
                        <select name="reason" class="form-control rounded" required>
                            <option value="" selected>Select Reason</option>
                            @foreach ($reschedule_reason as $reason)
                            <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                            @endforeach
                        </select>
                        <br>
                        <textarea name="remarks" class="form-control rounded border p-2" placeholder="Remarks..." rows="8"></textarea>
                    </div>
                </div>
                <div class="modal-footer pt-2 pb-2">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach 

<style>
    .is-new-order {
        background-color: #0aa934;
        color: #000000;
        animation: blinkingBackgroundNewOrder 2.5s linear infinite;
    }

    @keyframes blinkingBackgroundNewOrder{
        0%    { background-color: #ffffff;}
        25%   { background-color: #0aa934;}
        50%   { background-color: #ffffff;}
        75%   { background-color: #0aa934;}
        100%  { background-color: #ffffff;}
    }
</style>