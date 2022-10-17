<table class="table table-bordered table-striped">
    <col style="width: 10%;">
    <col style="width: 20%;">
    <col style="width: 16%;">
    <col style="width: 10%;">
    <col style="width: 10%;">
    <col style="width: 10%;">
    <col style="width: 10%;">
    <col style="width: 10%;">
    <col style="width: 4%;">
    <thead class="text-primary text-center font-weight-bold text-uppercase" style="font-size: 6pt;">
        <th class="p-2">Order No.</th>
        <th class="p-2">Customer</th>
        <th class="p-2">Project</th>
        <th class="p-2">Date Approved</th>
        <th class="p-2">Delivery Date</th>
        <th class="p-2">Delivery Status</th>
        <th class="p-2">Order Status</th>
        <th class="p-2">Prod. Status</th>
        <th class="p-2">-</th>
    </thead>
    <tbody class="text-center" style="font-size: 8pt;">
        @forelse ($list as $r)
        <tr class="{{ !in_array($r->name, $seen_order_logs) ? 'font-weight-bold' : ''}}">
            <td class="p-2">
                <span class="d-block">{{ $r->name }}</span>
                <small>{{ in_array($r->order_type, ['Sales DR', 'Regular Sales']) ? 'Customer Order' : $r->order_type }}</small>
            </td>
            <td class="p-2">{{ $r->customer }}</td>
            <td class="p-2">{{ $r->project }}</td>
            <td class="p-2">{{ $r->delivery_date ? \Carbon\Carbon::parse($r->date_approved)->format('M. d, Y') : '-' }}</td>
            <td class="p-2">{{ $r->delivery_date ? \Carbon\Carbon::parse($r->delivery_date)->format('M. d, Y') : '-' }}</td>
            <td class="p-2">{{ \Carbon\Carbon::now()->ne($r->delivery_date) ? 'For Reschedule' : 'To Delivery Today' }}</td>
            <td class="p-2">{{ $r->status }}</td>
            <td class="p-2">
                @if (array_key_exists($r->name, $order_production_status))
                @php
                    $progress_bar_val = $order_production_status[$r->name];
                    if ($progress_bar_val < 100) {
                        $progress_bar_color = 'bg-warning';
                    } else {
                        $progress_bar_color = 'bg-success';
                    }
                @endphp
                <div class="progress">
                    <div class="progress-bar {{ $progress_bar_color }}" role="progressbar" style="width: {{ $progress_bar_val }}%" aria-valuenow="{{ $progress_bar_val }}" aria-valuemin="0" aria-valuemax="100">
                        <small>{{ $progress_bar_val }}%</small>
                    </div>
                </div>
                @else
                    --
                @endif
            </td>
            <td class="p-1">
                <div class="btn-group dropleft">
                    <button type="button" class="btn p-2 btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Action</button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item view-order-btn" href="#" data-toggle="modal" data-target="#{{ strtolower($r->name) }}-modal" data-order="{{ $r->name }}">View Order</a>
                        <a class="dropdown-item" href="#">Reschedule Delivery Date</a>
                    </div>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center text-uppercase text-muted">No order(s) found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="m-2 order-list-pagination">
    {{ $list->appends(Request::except('page'))->links() }}
</div>

@foreach ($list as $i => $s)
<!-- Modal -->
<div class="modal fade" id="{{ strtolower($s->name) }}-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="min-width: 80%;">
        <div class="modal-content">
            <div class="modal-header pt-2 pl-3 pb-2 pr-3 text-white" style="background-color: #0277BD;">
                <h5 class="modal-title text-uppercase">{{ $s->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
              </div>
            <div class="modal-body pb-2">
                <div class="d-flex flex-row">
                    <div class="col-8">
                        <span class="d-block font-weight-bold" style="font-size: 13pt;">{{ $s->customer }}</span>
                        <span class="d-block mt-1" style="font-size: 10pt;">Project: <b>{{ $s->project }}</b></span>
                        <span class="d-block mt-1" style="font-size: 10pt;">Date Approved: <b>{{ $s->date_approved ? \Carbon\Carbon::parse($s->date_approved)->format('M. d, Y') : '-' }}</b></span>
                        <span class="d-block mt-1" style="font-size: 10pt;">Order Type: <b>{{ in_array($s->order_type, ['Sales DR', 'Regular Sales']) ? 'Customer Order' : $s->order_type }}</b></span>
                        <span class="d-block mt-1" style="font-size: 10pt;">Created by: <b>{{ $s->owner }}</b></span>
                    </div>
                    <div class="col-4">
                        <span class="d-block" style="font-size: 10pt;"><b>SHIP TO:</b> {!! $s->shipping_address !!}</span>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <form action="/assembly/wizard" class="order-items-form">
                            <input type="hidden" name="ref" value="{{ $s->name }}">
                            @php
                                $ref_type = explode("-", $s->name)[0];
                                $items = array_key_exists($s->name, $item_list) ? $item_list[$s->name] : [];
                                $production_orders = array_key_exists($s->name, $items_production_orders) ? $items_production_orders[$s->name] : [];
                            @endphp
                            <input type="hidden" name="ref_type" value="{{ $ref_type }}">
                            <table class="table table-bordered table-striped">
                                <thead class="text-primary text-center font-weight-bold text-uppercase" style="font-size: 6pt;">
                                    <th class="p-2" style="width: 5%;">-</th>
                                    <th class="p-2" style="width: 46%;">Item Description</th>
                                    <th class="p-2" style="width: 10%;">Qty</th>
                                    <th class="p-2" style="width: 17%;">BOM No.</th>
                                    <th class="p-2" style="width: 10%;">Ship by</th>
                                    @if (count($production_orders) > 0)
                                    <th class="p-2" style="width: 12%;">Prod. Order</th>
                                    @endif
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
                                            <span class="font-weight-bold">{{ $v->item_code }}</span> {!! strip_tags($v->description) !!}</td>
                                        <td class="text-center p-2">
                                            <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ number_format($v->qty) }}</span>
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
                                                    <button class="btn btn-info view-bom" type="button"><i class="now-ui-icons ui-1_zoom-bold"></i></button>
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
                                            @forelse ($production_order_item as $production_order)
                                            <a href="#" data-jtno="{{ $production_order }}" class="text-decoration-none prod-details-btn d-block">{{ $production_order }}</a>
                                            @empty
                                            -
                                            @endforelse
                                        </td>
                                        @endif
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted text-uppercase">No item(s) found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="d-flex flex-row pl-3 pr-3 pt-2 pb-2 align-items-start" style="font-size: 12px;">
                                @if ($s->notes)
                                <span class="font-weight-bold d-inline-block m-1">Notes:</span>
                                <span class="d-inline-block m-1">{!! $s->notes !!}</span>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-3">
                <button class="btn btn-primary btn-lg float-right m-0" type="submit"><i class="now-ui-icons ui-1_simple-add"></i> Production Order</button>
            </div>
        </div>
    </div>
</div>
@endforeach 