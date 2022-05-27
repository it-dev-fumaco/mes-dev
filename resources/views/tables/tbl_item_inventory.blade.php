<h6>Available Stock(s)</h6>
<table class="table table-bordered" style="font-size: 8pt;">
    <col style="width: 70%;">
    <col style="width: 30%;">
    <tr>
        <th class="text-center p-1">Warehouse</th>
        <th class="text-center p-1">Actual Qty</th>
    </tr>
    @forelse($stocks as $row)
    <tr>
        <td class="text-center p-1">{{ $row['warehouse'] }}</td>
        <td class="text-center p-1">{{ $row['available_qty'] }}</td>
    </tr>
    @empty
    <tr>
        <td class="text-center font-weight-bold text-uppercase" colspan="2">No available stocks</td>
    </tr>
    @endforelse
</table>