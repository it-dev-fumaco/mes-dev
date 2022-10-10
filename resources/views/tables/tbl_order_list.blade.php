<table class="table table-bordered table-striped">
    <col style="width: 8%;">
    <col style="width: 12%;">
    <col style="width: 20%;">
    <col style="width: 20%;">
    <col style="width: 12%;">
    <col style="width: 12%;">
    <col style="width: 12%;">
    <col style="width: 4%;">
    <thead class="text-primary text-center font-weight-bold text-uppercase" style="font-size: 6pt;">
        <th class="p-2">Order No.</th>
        <th class="p-2">Type</th>
        <th class="p-2">Customer</th>
        <th class="p-2">Project</th>
        <th class="p-2">Date Approved</th>
        <th class="p-2">Delivery Date</th>
        <th class="p-2">Status</th>
        <th class="p-2">-</th>
    </thead>
    <tbody class="text-center" style="font-size: 8pt;">
        @forelse ($list as $r)
        <tr>
            <td class="p-2">{{ $r->name }}</td>
            <td class="p-2">{{ in_array($r->order_type, ['Sales DR', 'Regular Sales']) ? 'Customer Order' : $r->order_type }}</td>
            <td class="p-2">{{ $r->customer }}</td>
            <td class="p-2">{{ $r->project }}</td>
            <td class="p-2">{{ $r->delivery_date ? \Carbon\Carbon::parse($r->date_approved)->format('M. d, Y') : '-' }}</td>
            <td class="p-2">{{ $r->delivery_date ? \Carbon\Carbon::parse($r->delivery_date)->format('M. d, Y') : '-' }}</td>
            <td class="p-2">{{ $r->status }}</td>
            <td class="p-2">
                <a href="#" data-toggle="modal" data-target="#{{ strtolower($r->name) }}-modal" class="text-decoration-none">View</a>
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
                        <table class="table table-bordered table-striped">
                            <col style="width: 5%;">
                            <col style="width: 41%;">
                            <col style="width: 12%;">
                            <col style="width: 10%;">
                            <col style="width: 10%;">
                            <col style="width: 10%;">
                            <col style="width: 12%;">
                            <thead class="text-primary text-center font-weight-bold text-uppercase" style="font-size: 6pt;">
                                <th class="p-2">No.</th>
                                <th class="p-2">Item Description</th>
                                <th class="p-2">Qty</th>
                                <th class="p-2">Available Stock</th>
                                <th class="p-2">BOM No.</th>
                                <th class="p-2">Ship by</th>
                                <th class="p-2">Action</th>
                            </thead>
                            <tbody style="font-size: 8pt;">
                                @php
                                    $items = array_key_exists($s->name, $item_list) ? $item_list[$s->name] : [];
                                    $production_orders = array_key_exists($s->name, $items_production_orders) ? $items_production_orders[$s->name] : [];
                                @endphp
                                @forelse ($items as $v)
                                @php
                                    $bom = array_key_exists($v->item_code, $default_boms) ? $default_boms[$v->item_code] : [];
                                    $bom = count($bom) > 0 ? $bom[0]->name : null;

                                    $production_orders = array_key_exists($v->item_code, $production_orders) ? $production_orders[$v->item_code] : [];
                                @endphp
                                <tr>
                                    <td class="text-center p-2">{{ $v->idx }}</td>
                                    <td class="text-justify p-2">
                                        <span class="font-weight-bold">{{ $v->item_code }}</span> {!! $v->description !!}</td>
                                    <td class="text-center p-2">
                                        <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ number_format($v->qty) }}</span>
                                        <small class="d-block">{{ $v->stock_uom }}</small>
                                    </td>
                                    <td class="text-center p-2"></td>
                                    <td class="text-center p-2">
                                        <a href="#" class="text-decoration-none view-bom" data-id="{{ $bom }}">{{ $bom }}</a>
                                    </td>
                                    <td class="text-center p-2">{{ $v->delivery_date ? \Carbon\Carbon::parse($v->delivery_date)->format('M. d, Y') : '-' }}</td>
                                    <td class="text-center p-2">
                                        @forelse ($production_orders as $production_order)
                                        <a href="#" data-jtno="{{ $production_order }}" class="text-decoration-none prod-details-btn">{{ $production_order }}</a>
                                        @empty
                                            -
                                        @endforelse
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted text-uppercase">No item(s) found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @if ($s->notes)
                        <div class="d-flex flex-row pl-3 pr-3 pt-2 pb-2" style="font-size: 12px;">
                            <span class="font-weight-bold d-inline-block m-1">Notes:</span>
                            <span class="d-inline-block m-1">{!! $s->notes !!}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer pb-1 pl-2 pr-2 pt-1">
                <button type="button" class="btn btn-secondary pb-2 pt-2 pr-3 pl-3" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach 