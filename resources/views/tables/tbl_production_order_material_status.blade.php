<div class="table-responsive" style="overflow:hidden;">
    <table class="table table-striped">
        <col style="width: 8%;">
        <col style="width: 13%;">
        <col style="width: 11%;">
        <col style="width: 20%;">
        <col style="width: 8%;">
        <col style="width: 8%;">
        <col style="width: 8%;">
        <col style="width: 8%;">
        <col style="width: 8%;">
        <col style="width: 8%;">
        <thead class="text-primary" style="font-size: 7pt;">
            <th class="text-center"><b>Prod. Order</b></th>
            <th class="text-center"><b>Customer</b></th>
            <th class="text-center"><b>Project</b></th>
            <th class="text-center"><b>Item Description</b></th>
            <th class="text-center"><b>Stock UOM</b></th>
            <th class="text-center"><b>Required Qty</b></th>
            <th class="text-center"><b>Transferred Qty</b></th>
            <th class="text-center"><b>Pending Qty</b></th>
            <th class="text-center"><b>Status</b></th>
            <th class="text-center"><b>Stock Entries</b></th>
        </thead>
        <tbody style="font-size: 9pt;">
            @forelse ($erp_production_order_required_items as $row)
            <tr>
                <td class="text-center p-2">
                    <a href="#" data-jtno="{{ $row->name }}" class="prod-details-btn"><i class="now-ui-icons ui-1_zoom-bold"></i> {{ $row->name }}</a>
                </td>
                <td class="text-left p-2">
                    <span class="d-block font-weight-bold">
                    @if ($row->material_request)
                    {{ $row->material_request }}
                    @else
                    {{ $row->sales_order ? $row->sales_order : $row->sales_order_no }}
                    @endif
                    </span>
                    <span class="d-block">{{ $row->customer }}</span>
                </td>
                <td class="text-center p-2">{{ $row->project }}
                </td>
                <td class="text-left p-2">
                    <span class="d-block font-weight-bold">{{ $row->item_code }}</span>
                    {!! $row->description !!}
                </td>
                <td class="text-center p-2">{{ $row->stock_uom }}</td>
                <td class="text-center p-2" style="font-size: 11pt;">
                    <span class="d-block">{{ number_format($row->required_qty, 2, '.', ',') }}</span>
                </td>
                <td class="text-center p-2" style="font-size: 11pt;">
                    <span class="d-block">{{ number_format($row->transferred_qty, 2, '.', ',') }}</span>
                </td>
                <td class="text-center p-2" style="font-size: 11pt;">
                    <span class="d-block">{{ number_format(($row->required_qty - $row->transferred_qty), 2, '.', ',') }}</span>
                </td>
                <td class="text-center p-2">
                    @if (($row->required_qty - $row->transferred_qty) > 0)
                    <span class="badge badge-warning" style="font-size: 9pt;">Pending</span>
                    @else
                    <span class="badge badge-success" style="font-size: 9pt;">Issued</span>
                    @endif
                </td>
                <td class="text-center p-2">
                    @php
                        $key = $row->name . $row->item_code;
                    @endphp
                    {{ array_key_exists($key, $stock_entries) ? $stock_entries[$key] : '--' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<center>
    <div class="col-md-12 text-center" id="tbl-production-order-material-status-pagination">
        {{ $erp_production_order_required_items->links() }}
    </div>
</center>