<table style="width: 100%; border-collapse: collapse;" class="custom-table-1-1">
    <col style="width: 13%;">
    <col style="width: 17%;">
    <col style="width: 25%;">
    <col style="width: 15%;">
    <col style="width: 15%;">
    <col style="width: 15%;">
    <tr class="text-center">
        <th>Prod. Order</th>
        <th>Reference No.</th>
        <th>Customer</th>
        <th>Project</th>
        <th>Delivery Date</th>
        <th>Qty to Manufacture</th>
    </tr>
    <tr class="text-center" style="font-size: 10pt;">
        <td>
            <a href="#" class="font-weight-bold view-production-order-details" data-production-order="{{ $details->production_order }}" style="color: black;">{{ $details->production_order }}</a>
        </td>
        <td><b>{{ $details->sales_order }}{{ $details->material_request }}</b></td>
        <td>{{ $details->customer }}</td>
        <td>{{ $details->project }}</td>
        <td>{{ $details->delivery_date }}</td>
        <td>
            <span class="d-block font-weight-bold" style="font-size: 11pt;">{{ $details->qty_to_manufacture }}</span>
            <span class="d-block" style="font-size: 8pt;">{{ $details->stock_uom }}</span>
        </td>
    </tr>
    <tr style="font-size: 10pt;">
        <td class="text-center font-weight-bold">ITEM DETAIL(S)</td>
        <td colspan="4"><span class="font-weight-bold">{{ $details->item_code }}</span> - {{ $details->description }}</td>
        <td class="text-center">
            @php
                $ref_ste = array_column($required_items, 'ste_docstatus');
            @endphp
            @if(count($required_items) > 0)
            @if(collect($ref_ste)->min() == 0)
            <button class="btn btn-primary m-1 submit-ste-btn p-3" data-production-order="{{ $details->production_order }}">Submit Withdrawal Slip</button>
            @else
            <button class="btn btn-success m-1 p-3">Withdrawal Slip Submitted</button>
            @endif
            @else
            <button class="btn btn-primary m-1 generate-ste-btn p-3" data-production-order="{{ $details->production_order }}">Create Withdrawal Slip</button>
            @endif
        </td>
    </tr>
</table>

@if(count($required_items) <= 0)
    <h5 class="text-center m-4">No withdrawal slip(s) created.</h5>
@else
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;" class="custom-table-1-1">
        <col style="width: 5%;">
        @if($has_parts_with_production)
        <col style="width: 10%;">
        <col style="width: 8%;">
        <col style="width: 22%;">
        @else
        <col style="width: 8%;">
        <col style="width: 32%;">
        @endif
        <col style="width: 15%;">
        <col style="width: 10%;">
        <col style="width: 10%;">
        <col style="width: 10%;">
        <col style="width: 10%;">
        <tr class="text-center">
            <th>No.</th>
            @if($has_parts_with_production)
            <th>Prod. Order</th>
            @endif
            <th>Image</th>
            <th>Item Code</th>
            <th>Source Warehouse</th>
            <th>Available</th>
            <th>Required</th>
            <th>Transferred / Issued</th>
            <th>Action</th>
        </tr>
        @foreach ($required_items as $i => $item)
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            @if($has_parts_with_production)
            <td class="text-center">
                @if ($item['production_order'])
                <span class="font-weight-bold view-production-order-details" data-production-order="{{ $item['production_order'] }}" style="color: black; cursor: pointer;">{{ $item['production_order'] }}</span>
                @else
                --
                @endif
            </td>
            @endif
            <td class="text-center">
                @php
        $img = ($item['item_image']) ? "/img/" . $item['item_image'] : "/icon/no_img.png";
        @endphp
                <a href="http://athenaerp.fumaco.local/storage/{{ $img }}" data-toggle="lightbox">
                    <img src="http://athenaerp.fumaco.local/storage/{{ $img }}" class="img-thumbnail" width="100">
                  </a>
            </td>
            <td class="text-justify">
                <span class="ste-name d-none">{{ $item['ste_name'] }}</span>
                <span class="sted-name d-none">{{ $item['sted_name'] }}</span>
                <span class="d-block font-weight-bold item-code">{{ $item['item_code'] }}</span>
                <span class="d-block item-description" style="font-size: 8pt;">{!! $item['description'] !!}</span>
            </td>
            <td class="text-center source-warehouse" style="font-size: 9pt;">{{ $item['source_warehouse'] }}</td>
            <td class="text-center">
                <span class="d-block font-weight-bold">{{ $item['actual_qty'] * 1 }}</span>
                <span class="d-block stock-uom" style="font-size: 8pt;">{{ $item['stock_uom'] }}</span>
            </td>
            <td class="text-center">
                <span class="d-block font-weight-bold required-qty">{{ ($item['required_qty'] > 0) ? $item['required_qty'] * 1 : $item['requested_qty'] * 1 }}</span>
                <span class="d-block" style="font-size: 8pt;">{{ $item['stock_uom'] }}</span>
            </td>
            <td class="text-center">
                <span class="d-block font-weight-bold">{{ $item['transferred_qty'] * 1 }}</span>
                <span class="d-block" style="font-size: 8pt;">{{ $item['stock_uom'] }}</span>
                <span class="d-block font-italic" style="font-size: 7pt;">{{ $details->wip_warehouse }}</span>
            </td>
            <td class="text-center">
                @php
                    $issued = ($item['issued_qty'] > 0) ? 'disabled' : null;
                @endphp
                <button type="button" class="btn btn-info btn-icon btn-icon-mini change-required-item-btn" data-production-order="{{ $details->production_order }}" data-item-classification="{{ $item['item_classification'] }}" {{ $issued }}> 
                    <i class="now-ui-icons ui-2_settings-90"></i>
                </button>
                <button type="button" class="btn btn-danger btn-icon btn-icon-mini delete-required-item-btn" data-production-order="{{ $details->production_order }}" {{ $issued }}>
                    <i class="now-ui-icons ui-1_simple-remove"></i>
                </button>
            </td>
        </tr>    
        @endforeach
    </table>

    @if(count($reference_stock_entry) > 0)
    <div class="pull-right font-italic m-2" style="font-size: 9pt;">
        <b>Reference: </b>{{ implode(', ', $reference_stock_entry) }}
    </div>
    @endif
    <div class="pull-left m-1">
        <button class="btn btn-primary btn-sm" id="add-required-item-btn" data-production-order="{{ $details->production_order }}">
            <i class="now-ui-icons ui-1_simple-add"></i> Add Item(s)
        </button>
    </div>
@endif

<style>
    .custom-table-1-1{
        border: 1px solid #ABB2B9;
    }

    .custom-table-1-1 th{
        background-color: #D5D8DC;
        text-transform: uppercase;
        font-size: 9pt;
    }

    .custom-table-1-1 th, .custom-table-1-1 td{
        padding: 3px;
        border: 1px solid #ABB2B9;
    }
</style>