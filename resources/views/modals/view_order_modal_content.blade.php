@php
    $items = array_key_exists($details->name, $item_list) ? $item_list[$details->name] : [];
    $production_orders = array_key_exists($details->name, $items_production_orders) ? $items_production_orders[$details->name] : [];
@endphp
<style>
    #orders-tab-1 .custom-nav-link {
        padding: 10px 20px;
        color: #2c3e50;
    }
    #orders-tab-1 {
        border-bottom: 3px solid #ebedef;
        padding: 10px 0 10px 0;
    }
    #orders-tab-1 .nav-item .active {
        color: #f96332;
        font-weight: bolder;
        border-bottom: 3px solid #f96332;
    }
</style>
<form action="/assembly/wizard" class="order-items-form">
    <div class="modal-content">
        <div class="pt-2 pl-3 pb-2 pr-3 text-white" style="background-color: #0277BD;">
            <div class="row m-0 p-0">
                <div class="col-8 m-0 p-0">
                    <h5 class="modal-title m-0">{{ $ref_type == 'SO' ? 'Sales Order' : 'Material Request - ' . $details->order_type }}</h5>
                </div>
                <div class="col-4 m-0 p-0 text-right">
                    <h5 class="d-inline-block mb-0 mr-5 font-italic">{{ $details->name }}</h5>
                    <button type="button" class="close d-inline-block" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="modal-body pb-2">
            <table class="w-100 border-0" style="font-size: 9pt;">
                <tr>
                    <td class="align-middle text-right p-1">Customer:</td>
                    <td class="align-middle p-1 font-weight-bold">{{ $details->customer }}</td>
                    <td class="align-middle text-right p-1" style="width: 12%;">Delivery Date:</td>
                    <td class="align-middle p-1 font-weight-bold" style="width: 20%;">{{ \Carbon\Carbon::parse($details->delivery_date)->format('M. d, Y') }}</td>
                    <td class="align-top p-1" rowspan="4" style="width: 20%;">
                        <b>SHIP TO:</b> {!! $details->shipping_address !!}
                    </td>
                    <td class="align-top text-right p-1" rowspan="4" style="width: 10%;">
                        <a href="/print_order/{{ $details->name }}" class="btn btn-info pt-2 pb-2 pl-3 pr-3 m-0 print-order-btn"><img src="{{ asset('/img/print_btn.png') }}" alt="Print" width="20" class="m-0"> Print</a>
                    </td>
                </tr>
                <tr>
                    <td class="align-middle text-right p-1">Project:</td>
                    <td class="align-middle p-1 font-weight-bold">{{ $details->project }}</td>
                    <td class="align-middle text-right p-1">Date Approved:</td>
                    <td class="align-middle p-1 font-weight-bold">{{ $details->date_approved ? \Carbon\Carbon::parse($details->date_approved)->format('M. d, Y') : '-' }}</td>
                </tr>
                <tr>
                    <td class="align-middle text-right p-1">Sales Person:</td>
                    <td class="align-middle p-1 font-weight-bold">{{ $details->sales_person }}</td>
                    <td class="align-middle text-right p-1">Company:</td>
                    <td class="align-middle p-1 font-weight-bold">{{ $details->company }}</td>
                </tr>
                <tr>
                    <td class="align-middle text-right p-1">Order Type:</td>
                    <td class="align-middle p-1 font-weight-bold" colspan="3">{{ in_array($details->order_type, ['Sales DR', 'Regular Sales']) ? 'Customer Order' : $details->order_type }}</td>
                </tr>
            </table>
            <div class="row mt-2">
                <div class="col-12">
                    <input type="hidden" name="ref" value="{{ $details->name }}">
                    <input type="hidden" name="ref_type" value="{{ $ref_type }}">

                    <div class="nav-tabs-navigation mt-2">
                        <div class="nav-tabs-wrapper">
                            <ul class="nav nav-tabs" data-tabs="tabs" id="orders-tab-1">
                                <li class="nav-item">
                                    <a class="custom-nav-link active show text-decoration-none" href="#icw_fabrication" data-toggle="tab">Order Item(s)</a>
                                </li>
                                @if(count($actual_delivery_date_per_item) > 0)
                                <li class="nav-item">
                                    <a class="custom-nav-link show text-decoration-none" href="#icw_painting" data-toggle="tab">Delivered Item(s)</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <div class="tab-content text-center mt-1">
                        <div class="tab-pane active show" id="icw_fabrication">
                            <div class="row p-0 m-0 w-100">
                                <div class="col-md-12 p-0 m-0">
                                

                                    <table class="table table-bordered">
                                        <thead class="text-white bg-secondary text-center font-weight-bold text-uppercase" style="font-size: 6pt;">
                                            <th class="p-2" style="width: 3%;">-</th>
                                            <th class="p-2" style="width: 35%;">Item Description</th>
                                            <th class="p-2" style="width: 5%;">Ordered</th>
                                            <th class="p-2" style="width: 7%;">Ship by</th>
                                            <th class="p-2" style="width: 10%;">BOM No.</th>
                                            <th class="p-2" style="width: 10%;">Prod. Order</th>
                                            <th class="p-2" style="width: 5%;">Status</th>
                                            <th class="p-2" style="width: 10%;">Qty to Manufacture</th>
                                            <th class="p-2" style="width: 5%;">Produced Qty</th>
                                            <th class="p-1" style="width: 5%;">Action</th>
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
                                                <td class="text-center p-2" rowspan="{{ count($production_order_item) > 0 ? count($production_order_item) : '' }}">
                                                    <input type="checkbox" class="form-control" value="{{ $v->item_code }}" name="item[]">
                                                </td>
                                                <td class="text-justify p-1" rowspan="{{ count($production_order_item) > 0 ? count($production_order_item) : '' }}">
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
                                                <td class="text-center p-2" rowspan="{{ count($production_order_item) > 0 ? count($production_order_item) : '' }}">
                                                    <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ number_format($v->qty) }}</span>
                                                    <small class="d-block">{{ $v->stock_uom }}</small>
                                                </td>
                                                <td class="text-center p-2" rowspan="{{ count($production_order_item) > 0 ? count($production_order_item) : '' }}">
                                                    {{ $v->delivery_date ? \Carbon\Carbon::parse($v->delivery_date)->format('M. d, Y') : '-' }}
                                                </td>
                                                <td class="text-center p-2" rowspan="{{ count($production_order_item) > 0 ? count($production_order_item) : '' }}">
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
                                                @forelse ($production_order_item as $po)
                                                <td class="text-center p-2">
                                                    <a href="#" data-jtno="{{ $po['production_order'] }}" class="text-decoration-none prod-details-btn d-block">{{ $po['production_order'] }}</a>
                                                    @php
                                                        switch($po['status']){
                                                            case 'In Progress':
                                                                $badge = 'warning';
                                                                break;
                                                            case 'Completed':
                                                            case 'Feedbacked':
                                                                $badge = 'success';
                                                                break;
                                                            case 'Ready for Feedback':
                                                            case 'For Partial Feedback':
                                                                $badge = 'info';
                                                                break;
                                                            case 'Cancelled':
                                                                $badge = 'danger';
                                                                break;
                                                            default:
                                                                $badge = 'secondary';
                                                                break;
                                                        }
                                                    @endphp
                                                    <span style="font-size: 7pt;">{{ $po['created_by'] }}</span><br>
                                                    <span style="font-size: 7pt;">{{ Carbon\Carbon::parse($po['created_at'])->format('M. d, Y h:i a') }}</span>
                                                </td>
                                                <td class="text-center p-2">
                                                    <span class="badge badge-{{ $badge }} mt-1" style="font-size: 7pt;">{{ $po['status'] }}</span>
                                                </td>
                                                <td class="text-center p-2">
                                                    <span class="d-block font-weight-bold">{{ $po['qty_to_manufacture'] }}</span>
                                                </td>
                                                <td class="text-center p-2">
                                                    <span class="d-block font-weight-bold">{{ $po['produced_qty'] }}</span>
                                                </td>
                                                <td class="text-center p-1">
                                                    @if(count($bom) > 0)
                                                        <button class="btn btn-sm btn-info btn-icon btn_trackmodal" style="padding: 7px 8px;" data-itemcode="{{ $v->item_code }}" data-guideid="{{ $details->name }}" data-erpreferenceno="{{ $v->name }}" data-customer="{{ $details->customer }}"><i class="now-ui-icons ui-1_zoom-bold"></i></button>
                                                    @endif
                                                    <a class="btn btn-sm btn-primary btn-icon create-ste-btn" style="padding: 7px 8px;" href="#" data-production-order="{{ $po['production_order'] }}" data-item-code="{{ $v->item_code }}" data-qty="{{ number_format($v->qty) }}" data-uom="{{ $v->stock_uom }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @empty
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                            @endforelse
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
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane show" id="icw_painting">
                            <div class="row p-0 m-0 w-100">
                                <div class="col-12 p-0">
                                    <table class="table table-bordered">
                                        <thead class="text-white bg-secondary text-center font-weight-bold text-uppercase" style="font-size: 6pt;">
                                            <th class="p-2">Item Code</th>
                                            <th class="p-2">Ordered Qty</th>
                                            <th class="p-2">DR No.</th>
                                            <th class="p-1">Delivered Qty</th>
                                            <th class="p-2">Date Picked</th>
                                            <th class="p-2">Date Delivered</th>
                                            <th class="p-2">Reference No.</th>
                                        </thead>
                                        <tbody style="font-size: 8pt;">
                                            @forelse ($actual_delivery_date_per_item as $item_idx => $del)
                                            <tr>
                                                <td class="text-center p-2" rowspan="{{ count($del) > 0 ? count($del) : '' }}">
                                                    <span class="d-block font-weight-bold">{{ $item_idx }}</span>
                                                </td>
                                                <td class="text-center p-2" rowspan="{{ count($del) > 0 ? count($del) : '' }}">
                                                    <span class="d-block font-weight-bold" style="font-size: 11pt;">{{ number_format($del[0]->ordered_qty) }}</span>
                                                </td>
                                                @forelse ($del as $del_row)
                                                <td class="text-center p-2">
                                                    @php
                                                        $owner = ucwords(str_replace('.', ' ', explode('@', $del_row->owner)[0]));
                                                    @endphp
                                                    <span class="d-block">{{ $del_row->name }}</span>
                                                    <small class="d-block">{{ $owner }}</small>
                                                </td>
                                                <td class="text-center p-2">
                                                    <span class="d-block font-weight-bold" style="font-size: 11pt;">{{ number_format($del_row->delivered_qty) }}</span>
                                                </td>
                                                <td class="text-center p-2">
                                                    @php
                                                        $issued_by = isset($picking_slip_arr[$del_row->name][$item_idx]['user']) ? $picking_slip_arr[$del_row->name][$item_idx]['user'] : null;
                                                        $issued_by = ucwords(str_replace('.', ' ', explode('@', $issued_by)[0]));
                                                    @endphp
                                                    <span class="d-block">{{ isset($picking_slip_arr[$del_row->name][$item_idx]['date_picked']) ? $picking_slip_arr[$del_row->name][$item_idx]['date_picked'] : null }}</span>
                                                    <small class="d-block">{{ $issued_by }}</small>
                                                </td>
                                                <td class="text-center p-2">
                                                    {{ \Carbon\Carbon::parse($del_row->actual_delivery_date)->format('M. d, Y') }}
                                                </td>
                                                <td class="text-center p-2"> {{ ($del_row->dr_ref_no) ? $del_row->dr_ref_no : '-' }}</td>
                                            </tr>
                                            @empty
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                            @endforelse
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted text-uppercase">No record(s) found</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    
                    @if (count($comments) > 0)
                    <span class="d-block mt-1 mb-1 ml-2 text-uppercase" style="font-size: 9pt;">Comment(s)</span>
                    @foreach ($comments as $c)
                    <div class="border-bottom p-1 ml-2 mr-2 rounded">
                        <p class="font-weight-bold m-0">{{ $c->comment_by }} <small class="font-italic">{{ \Carbon\Carbon::parse($c->creation)->format('M. d, Y h:i A') }}</small></p>
                        <div class="d-block m-0 p-0">{!! $c->content !!}</div>
                    </div>
                    @endforeach
                    @endif
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