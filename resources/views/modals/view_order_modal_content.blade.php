@php
    $items = array_key_exists($details->name, $item_list) ? $item_list[$details->name] : [];
    $production_orders = array_key_exists($details->name, $items_production_orders) ? $items_production_orders[$details->name] : [];
@endphp

<form action="/assembly/wizard" class="order-items-form">
    <div class="modal-content">
        <div class="modal-header pt-2 pl-3 pb-2 pr-3 text-white" style="background-color: #0277BD;">
            <h5 class="modal-title">{{ $ref_type == 'SO' ? 'Sales Order' : 'Material Request - ' . $details->order_type }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body pb-2">
            <table class="w-100 border-0">
                <tr>
                    <td class="align-middle text-right p-1" style="width: 12%;">Order No.:</td>
                    <td class="align-middle p-1 font-weight-bold" style="width: 26%; font-size: 13pt;">{{ $details->name }}</td>
                    <td class="align-middle text-right p-1" style="width: 12%;">Delivery Date:</td>
                    <td class="align-middle p-1 font-weight-bold" style="width: 20%;">{{ \Carbon\Carbon::parse($details->delivery_date)->format('M. d, Y') }}</td>
                    <td class="align-top p-1" rowspan="5" style="width: 20%; font-size: 10pt;">
                        <b>SHIP TO:</b> {!! $details->shipping_address !!}
                    </td>
                    <td class="align-top text-right p-1" rowspan="5" style="width: 10%;">
                        <a href="/print_order/{{ $details->name }}" class="btn btn-info pt-2 pb-2 pl-3 pr-3 m-0 print-order-btn"><img src="{{ asset('/img/print_btn.png') }}" alt="Print" width="20" class="m-0"> Print</a>
                    </td>
                </tr>
                <tr>
                    <td class="align-middle text-right p-1">Customer:</td>
                    <td class="align-middle p-1 font-weight-bold" style="font-size: 11pt;">{{ $details->customer }}</td>
                    <td class="align-middle text-right p-1">Date Approved:</td>
                    <td class="align-middle p-1 font-weight-bold">{{ $details->date_approved ? \Carbon\Carbon::parse($details->date_approved)->format('M. d, Y') : '-' }}</td>
                </tr>
                <tr>
                    <td class="align-middle text-right p-1">Project:</td>
                    <td class="align-middle p-1 font-weight-bold" style="font-size: 10pt;">{{ $details->project }}</td>
                    <td class="align-middle text-right p-1">Company:</td>
                    <td class="align-middle p-1 font-weight-bold">{{ $details->company }}</td>
                </tr>
                <tr>
                    <td class="align-middle text-right p-1">Sales Person:</td>
                    <td class="align-middle p-1 font-weight-bold" colspan="3" style="font-size: 10pt;">{{ $details->sales_person }}</td>
                </tr>
                <tr>
                    <td class="align-middle text-right p-1">Order Type:</td>
                    <td class="align-middle p-1 font-weight-bold" colspan="3" style="font-size: 10pt;">{{ in_array($details->order_type, ['Sales DR', 'Regular Sales']) ? 'Customer Order' : $details->order_type }}</td>
                </tr>
            </table>
            <div class="row mt-2">
                <div class="col-12">
                    <input type="hidden" name="ref" value="{{ $details->name }}">
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

                                $img = 'http://athenaerp.fumaco.local/storage';
                                $img .= array_key_exists($v->item_code, $item_images) ? "/img/" . $item_images[$v->item_code] : "/icon/no_img.png";

                                $production_order_item = array_key_exists($v->item_code, $production_orders) ? $production_orders[$v->item_code] : [];
                            @endphp
                            <tr>
                                <td class="text-center p-2">
                                    <input type="checkbox" class="form-control" value="{{ $v->item_code }}" name="item[]">
                                </td>
                                <td class="text-justify p-1">
                                    <div class="d-flex flex-row">
                                        <img src="{{ $img }}" alt="{{ $v->item_code }}" class="m-1" style="width: 50px; height: 50px;">
                                        <div class="m-1">
                                            <span class="font-weight-bold">{{ $v->item_code }}</span> {!! strip_tags($v->description) !!}
                                            @if ($ref_type == 'SO')
                                            <span class="d-block mt-1"><b>Note:</b> {!! $v->item_note !!}</span>
                                            @endif
                                        </div>
                                    </div>
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
                                    @elseif (in_array($po['status'], ['Ready for Feedback', 'For Partial Feedback']))
                                    <span class="badge badge-info" style="font-size: 7pt;">{{ $po['status'] }}</span>
                                    @elseif (in_array($po['status'], ['Cancelled']))
                                    <span class="badge badge-danger" style="font-size: 7pt;">{{ $po['status'] }}</span>
                                    @else
                                    <span class="badge badge-secondary" style="font-size: 7pt;">{{ $po['status'] }}</span>
                                    @endif
                                    @empty
                                    -
                                    @endforelse
                                </td>
                                @endif
                                <td class="text-center p-2">
                                    <button class="btn btn-info btn-icon btn_trackmodal" data-itemcode="{{ $v->item_code }}" data-guideid="{{ $details->name }}" data-erpreferenceno="{{ $v->name }}" data-customer="{{ $details->customer }}">
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
                        $notes = strip_tags($details->notes);
                    @endphp
                    @if ($notes)
                    <div class="d-flex flex-row pl-3 pr-3 pt-2 pb-2 align-items-start" style="font-size: 12px;">
                        <span class="font-weight-bold d-inline-block m-1">Notes:</span>
                        <span class="d-inline-block m-1">{!! $details->notes !!}</span>
                    </div>
                    @endif
                    <span class="d-block mt-1 ml-2 font-italic" style="font-size: 8pt;">Created by: <b>{{ $details->owner }}</b></span>
                </div>
                @php
                    $seen_logs = array_key_exists($details->name, $seen_logs_per_order) ? $seen_logs_per_order[$details->name] : [];
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