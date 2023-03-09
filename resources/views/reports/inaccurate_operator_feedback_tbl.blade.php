<table class="table table-striped table-bordered">
    <thead class="text-center text-primary text-uppercase" style="font-size: 7pt;">
        <th>Operator</th>
        <th>Process Count</th>
        <th>Completed Qty</th>
    </thead>
    <tbody>
        @foreach ($report as $r)
        <tr>
            <td class="p-2">{{ $r['operator_name'] }}</td>
            <td class="text-center p-2">{{ number_format($r['process_count']) }}</td>
            <td class="text-center p-2">{{ number_format($r['completed_qty']) }}</td>
        </tr>
        @endforeach
    </tbody>

</table>