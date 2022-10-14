<div class="table-responsive border" style="max-height: 600px;">
    <table class="table table-bordered table-striped">
        <col style="width: 25%;">
        <col style="width: 25%;">
        <col style="width: 25%;">
        <col style="width: 25%;">
        <thead class="text-primary text-center font-weight-bold text-uppercase" style="font-size: 6pt;">
            <th class="p-2">Order No.</th>
            <th class="p-2">Item</th>
            <th class="p-2">Qty</th>
            <th class="p-2">Status</th>
        </thead>
        <tbody class="text-center" style="font-size: 8pt;">
            @forelse ($data as $r)
            <tr>
                <td class="p-2">{{ $r['reference_no'] }}</td>
                <td class="p-2">{{ $r['item_code'] }}</td>
                <td class="p-2">{{ $r['qty'] . ' ' . $r['stock_uom'] }}</td>
                <td class="p-2">{{ $r['status'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-uppercase text-muted">No order(s) found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>