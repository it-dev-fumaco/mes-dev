<table class="table table-striped text-center">
    <col style="width: 10%;">
    <col style="width: 8%;">
    <col style="width: 12%;">
    <col style="width: 20%;">
    <col style="width: 8%;">
    <col style="width: 8%;">
    <col style="width: 8%;">
    <col style="width: 8%;">
    <col style="width: 8%;">
    <col style="width: 10%;">
    <thead class="text-primary" style="font-size: 7pt;">
        <th class="text-center font-weight-bold">Planned Start Date</th>
        <th class="text-center font-weight-bold">Prod. Order</th>
        <th class="text-center font-weight-bold">Reference</th>
        <th class="text-center font-weight-bold">Description</th>
        <th class="text-center font-weight-bold">Ordered Qty</th>
        <th class="text-center font-weight-bold">Ready for Painting</th>
        <th class="text-center font-weight-bold">Loaded Qty</th>
        <th class="text-center font-weight-bold">Unloaded Qty</th>
        <th class="text-center font-weight-bold">Completed</th>
        <th class="text-center font-weight-bold">Status</th>
    </thead>
    <tbody style="font-size: 8pt;">
        @forelse($data as $row)
        <tr>
            <td class="text-center font-weight-bold"><span style="font-size: 11pt;">{{ $row['planned_start_date'] }}</span></td>
            <td class="text-center">
                <span class="font-weight-bold prod_order_link_to_search" data-prod="{{ $row['production_order'] }}">{{ $row['production_order'] }}</span>
            </td>
            <td class="text-center">
                <span class="d-block font-weight-bold">{{ $row['sales_order'] }}{{ $row['material_request'] }}</span>
                <span class="d-block">{{ $row['customer'] }}</span>
            </td>
            <td class="text-left">
                <span class="d-block font-weight-bold">{{ $row['item_code'] }}</span>
                <span class="d-block">{{$row['item_description']}}</span>
            </td>
            <td class="text-center">
                <span class="d-block font-weight-bold" style="font-size: 10pt;">{{ $row['qty'] }}</span>
                <span class="d-block">{{ $row['stock_uom'] }}</span>
            </td>
            <td class="text-center">
                <span class="d-block font-weight-bold" style="font-size: 10pt;">{{ $row['qty_ready_for_painting'] }}</span>
                <span class="d-block">{{ $row['stock_uom'] }}</span>
            </td>
            @foreach($row['job_ticket'] as $rows)
            @php
                if($rows->status == "Completed"){
                    $status="#2ecc71";
                }else if($rows->status == "In Progress"){
                    $status="#f4d03f";
                }else{
                    $status="#b2babb";
                }
            @endphp
            <td class="text-center text-white" style="background-color: {{$status}};">
                <span class="d-block" style="font-size: 11pt;">{{ $rows->status }}</span>
                <span class="d-block" style="font-size: 9pt;">( {{ $rows->completed_qty }} )</span>
            </td>
            @endforeach
            @if (count($row['job_ticket']) < 2)
            <td class="text-center text-white" style="background-color: #b2babb;">
                <span class="d-block" style="font-size: 11pt;">Pending</span>
                <span class="d-block" style="font-size: 9pt;">( 0 )</span>
            </td>
            @endif
            <td class="text-center">
                <span class="d-block font-weight-bold" style="font-size: 10pt;">{{ number_format($row['completed_qty']) }}</span>
                <span class="d-block" style="font-size: 9pt;">{{ $row['stock_uom'] }}</span>
            </td>
           
            
            @php
                if($row['status'] == "Ready for Painting"){
                    $col = 'info';
                }else if($row['status'] == "Painting In Progress"){
                    $col = 'warning';
                }else{
                    $col = 'success';
                }
            @endphp
            <td class="text-center">
                <span class="badge badge-{{ $col }} p-2" style="font-size: 10pt;">{{ $row['status'] }}</span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="12" class="text-center">No task(s) found</td>
        </tr>
        @endforelse
   </tbody>
</table>
