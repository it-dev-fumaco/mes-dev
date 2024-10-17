<table class="table table-bordered table-striped" style="font-size: 10pt;">
    <tr>
        <th>Operator</th>
        <th>Operation</th>
        <th>Completed Qty</th>
        <th>Completed Qty as Helper</th>
        <th>Total Qty</th>
    </tr>
    @forelse ($list as $item)
        <tr>
            <td>{{ $item->operator_name }}</td>
            <td>{{ $item->operation_name }}</td>
            <td>{{ number_format($item->good_qty) }}</td>
            <td>{{ number_format($item->helper_qty) }}</td>
            <td>{{ number_format($item->total_qty) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan=5 class="text-center">
                No record(s) found
            </td>
        </tr>
    @endforelse
</table>