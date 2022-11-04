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
            $start = \Carbon\Carbon::now()->subMinutes(2);
		    $end = \Carbon\Carbon::now();
            $is_modified = false;
            $check = \Carbon\Carbon::parse($r->modified)->between($start, $end);
            if ($check) {
                $is_modified = true;
            }
        @endphp
        <tr class="{{ !in_array($r->name, $seen_order_logs) ? 'font-weight-bold' : ''}} {{ $is_modified && !in_array($r->name, $seen_order_logs) ? 'is-new-order' : '' }}">
            <td class="p-2">{{ $r->name }}</td>
            <td class="p-2">{{ $r->customer }}</td>
            <td class="p-2">{{ $r->order_type }}</td>
            <td class="p-2">{{ $r->project }}</td>
            <td class="p-2">{{ $r->delivery_date ? \Carbon\Carbon::parse($r->date_approved)->format('M. d, Y') : '-' }}</td>
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
                        <a class="dropdown-item view-order-btn" href="#" data-toggle="modal" data-target="#{{ strtolower($r->name) }}-modal" data-order="{{ $r->name }}">View Order</a>
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

@php
    $ref_type = explode("-", $s->name)[0];
    $items = array_key_exists($s->name, $item_list) ? $item_list[$s->name] : [];
    $production_orders = array_key_exists($s->name, $items_production_orders) ? $items_production_orders[$s->name] : [];
@endphp

<!-- Modal -->
<div class="modal fade order-modal" id="{{ strtolower($s->name) }}-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="min-width: 85%;">
        <form action="/assembly/wizard" class="order-items-form">
            <div class="modal-content">
                <div class="modal-header pt-2 pl-3 pb-2 pr-3 text-white" style="background-color: #0277BD;">
                    <h5 class="modal-title">{{ $ref_type == 'SO' ? 'Sales Order' : 'Material Request - ' . $s->order_type }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pb-2">
                    <table class="w-100 border-0">
                        <tr>
                            <td class="align-middle text-right p-1" style="width: 12%;">Order No.:</td>
                            <td class="align-middle p-1 font-weight-bold" style="width: 26%; font-size: 13pt;">{{ $s->name }}</td>
                            <td class="align-middle text-right p-1" style="width: 12%;">Delivery Date:</td>
                            <td class="align-middle p-1 font-weight-bold" style="width: 20%;">{{ \Carbon\Carbon::parse($r->delivery_date)->format('M. d, Y') }}</td>
                            <td class="align-top p-1" rowspan="5" style="width: 20%; font-size: 10pt;">
                                <b>SHIP TO:</b> {!! $s->shipping_address !!}
                            </td>
                            <td class="align-top text-right p-1" rowspan="5" style="width: 10%;">
                                <a href="/print_order/{{ $s->name }}" class="btn btn-info pt-2 pb-2 pl-3 pr-3 m-0 print-order-btn"><img src="{{ asset('/img/print_btn.png') }}" alt="Print" width="20" class="m-0"> Print</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle text-right p-1">Customer:</td>
                            <td class="align-middle p-1 font-weight-bold" style="font-size: 11pt;">{{ $s->customer }}</td>
                            <td class="align-middle text-right p-1">Date Approved:</td>
                            <td class="align-middle p-1 font-weight-bold">{{ $s->date_approved ? \Carbon\Carbon::parse($s->date_approved)->format('M. d, Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="align-middle text-right p-1">Project:</td>
                            <td class="align-middle p-1 font-weight-bold" style="font-size: 10pt;">{{ $s->project }}</td>
                            <td class="align-middle text-right p-1">Company:</td>
                            <td class="align-middle p-1 font-weight-bold">{{ $s->company }}</td>
                        </tr>
                        <tr>
                            <td class="align-middle text-right p-1">Sales Person:</td>
                            <td class="align-middle p-1 font-weight-bold" colspan="3" style="font-size: 10pt;">{{ $s->sales_person }}</td>
                        </tr>
                        <tr>
                            <td class="align-middle text-right p-1">Order Type:</td>
                            <td class="align-middle p-1 font-weight-bold" colspan="3" style="font-size: 10pt;">{{ in_array($s->order_type, ['Sales DR', 'Regular Sales']) ? 'Customer Order' : $s->order_type }}</td>
                        </tr>
                    </table>
                    <div class="row mt-2">
                        <div class="col-12">
                            <input type="hidden" name="ref" value="{{ $s->name }}">
                            <input type="hidden" name="ref_type" value="{{ $ref_type }}">
                            <table class="table table-bordered table-striped">
                                <thead class="text-white bg-secondary text-center font-weight-bold text-uppercase" style="font-size: 6pt;">
                                    <th class="p-2" style="width: 3%;">-</th>
                                    <th class="p-2" style="width: 40%;">Item Description</th>
                                    <th class="p-2" style="width: 5%;">Ordered</th>
                                    @if (count($production_orders) > 0)
                                    <th class="p-2" style="width: 5%;">Manufactured</th>
                                    @endif
                                    <th class="p-2" style="width: 5%;">Delivered</th>
                                    <th class="p-2" style="width: 15%;">BOM No.</th>
                                    <th class="p-2" style="width: 8%;">Ship by</th>
                                    @if (count($production_orders) > 0)
                                    <th class="p-2" style="width: 8%;">Prod. Order</th>
                                    @endif
                                    <th class="p-1" style="width: 7%;">Track Order</th>
                                </thead>
                                <tbody style="font-size: 8pt;">
                                    @forelse ($items as $v)
                                    @php
                                        $bom = array_key_exists($v->item_code, $default_boms) ? $default_boms[$v->item_code] : [];
                                        $defaultbom = count($bom) > 0 ? $bom[0]->name : null;

                                        $production_order_item = array_key_exists($v->item_code, $production_orders) ? $production_orders[$v->item_code] : [];
                                    @endphp
                                    <tr>
                                        <td class="text-center p-2">
                                            <input type="checkbox" class="form-control" value="{{ $v->item_code }}" name="item[]">
                                        </td>
                                        <td class="text-justify p-2">
                                            <span class="font-weight-bold">{{ $v->item_code }}</span> {!! strip_tags($v->description) !!}
                                            @if ($ref_type == 'SO')
                                            <span class="d-block mt-1"><b>Note:</b> {!! $v->item_note !!}</span>
                                            @endif
                                        </td>
                                        <td class="text-center p-2">
                                            <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ number_format($v->qty) }}</span>
                                            <small class="d-block">{{ $v->stock_uom }}</small>
                                        </td>
                                        @if (count($production_orders) > 0)
                                        <td class="text-center p-2 font-weight-bold" style="font-size: 9pt;">
                                            <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ collect($production_order_item)->sum('produced_qty') }}</span>
                                            <small class="d-block">{{ $v->stock_uom }}</small>
                                        </td>
                                        @endif
                                        <td class="text-center p-2 font-weight-bold" style="font-size: 9pt;">
                                            <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ number_format($v->delivered_qty) }}</span>
                                            <small class="d-block">{{ $v->stock_uom }}</small>
                                        </td>
                                        <td class="text-center p-2">
                                            @if(count($bom) > 0)
                                            <div class="input-group m-0">
                                                <select class="custom-select p-2" name="bom[{{ $v->item_code }}]">
                                                    @foreach($bom as $b)
                                                    <option value="{{ $b->name }}"><b>{{ $b->name }}</b></option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                    <button class="btn btn-secondary view-bom pb-2 pt-2 pr-3 pl-3" type="button"><i class="now-ui-icons ui-1_zoom-bold"></i></button>
                                                </div>
                                            </div>
                                            @else
                                            <input type="hidden" name="bom[{{ $v->item_code }}]">
                                            <span>-- No BOM --</span>
                                            @endif
                                        </td>
                                        <td class="text-center p-2">{{ $v->delivery_date ? \Carbon\Carbon::parse($v->delivery_date)->format('M. d, Y') : '-' }}</td>
                                        @if (count($production_orders) > 0)
                                        <td class="text-center p-2">
                                            @forelse ($production_order_item as $po)
                                            <a href="#" data-jtno="{{ $po['production_order'] }}" class="text-decoration-none prod-details-btn d-block">{{ $po['production_order'] }}</a>
                                            @if ($po['status'] == 'In Progress')
                                            <span class="badge badge-warning" style="font-size: 7pt;">{{ $po['status'] }}</span>
                                            @elseif (in_array($po['status'], ['Completed', 'Feedbacked']))
                                            <span class="badge badge-success" style="font-size: 7pt;">{{ $po['status'] }}</span>
                                            @else
                                            <span class="badge badge-secondary" style="font-size: 7pt;">{{ $po['status'] }}</span>
                                            @endif
                                            @empty
                                            -
                                            @endforelse
                                        </td>
                                        @endif
                                        <td class="text-center p-2">
                                            <button class="btn btn-info btn-icon btn_trackmodal" data-itemcode="{{ $v->item_code }}" data-guideid="{{ $s->name }}" data-erpreferenceno="{{ $v->name }}" data-customer="{{ $s->customer }}">
                                                <i class="now-ui-icons ui-1_zoom-bold"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted text-uppercase">No item(s) found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @php
                                $notes = strip_tags($s->notes);
                            @endphp
                            @if ($notes)
                            <div class="d-flex flex-row pl-3 pr-3 pt-2 pb-2 align-items-start" style="font-size: 12px;">
                                <span class="font-weight-bold d-inline-block m-1">Notes:</span>
                                <span class="d-inline-block m-1">{!! $s->notes !!}</span>
                            </div>
                            @endif
                            <span class="d-block mt-1 ml-2 font-italic" style="font-size: 8pt;">Created by: <b>{{ $s->owner }}</b></span>
                        </div>
                        @php
                            $seen_logs = array_key_exists($s->name, $seen_logs_per_order) ? $seen_logs_per_order[$s->name] : [];
                        @endphp
                        <div class="col-12 mt-3" style="font-size: 8pt;">
                            <ul>
                                @foreach ($seen_logs as $e)
                                <li>
                                    <span class="d-block font-italic text-muted">{{ 'Viewed by ' . ucwords(str_replace(".", " ", explode("@", $e->created_by)[0])) . ' on ' . \Carbon\Carbon::parse($e->created_at)->format('M. d, Y h:i A') }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="border-top pt-2 pb-2 pl-3 pr-3">
                    <div class="d-flex justify-content-end align-items-center">
                        <button class="btn btn-secondary btn-lg float-ri1ght m1-0 disabled" type="submit"><i class="now-ui-icons ui-1_simple-add"></i> Production Order</button>
                    </div>
                </div>
            </div>
        </form>
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