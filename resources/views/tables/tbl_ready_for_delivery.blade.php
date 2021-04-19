<table>
    <tr>
        <td>Reference No</td>
        <td>Item Description</td>
        <td>Qty</td>
    </tr>
    @forelse ($list as $row)
    <tr>
        <td>
            <span class="d-block">{{ ($row->sales_order) ? $row->sales_order : $row->material_request }}</span>
            <span>{{ $row->customer }}</span>
        </td>
        <td>{{ $row->item_code }} {{ $row->description }}</td>
        <td>{{ $row->feedback_qty }} {{ $row->stock_uom }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="3">No records found.</td>
    </tr>
    @endforelse
</table>