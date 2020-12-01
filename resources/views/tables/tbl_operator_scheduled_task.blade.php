<table class="table table-striped">
    <col style="width: 10%;">
    <col style="width: 18%;">
    <col style="width: 28%;">
    <col style="width: 8%;">
    <col style="width: 8%;">
    <col style="width: 8%;">
    <thead class="text-primary text-uppercase" style="font-size: 6pt;">
        <th class="text-center"><b>Prod. Order</b></th>
        <th class="text-center"><b>Customer</b></th>
        <th class="text-center"><b>Item Details</b></th>
        <th class="text-center"><b>Qty</b></th>
        <th class="text-center"><b>Completed</b></th>
        <th class="text-center"><b>Balance</b></th>
    </thead>
    @php
        $i = 1;
    @endphp
    @forelse($task as $row)
    <tbody style="font-size: 9pt;">
        <tr>
            <td class="text-center align-middle">
                <span class="pull-right badge badge-primary" style="font-size: 9pt;">{{ $i++ }}</span>
                <h6 class="view-prod-details-btn">{{ $row->production_order }}</h6>
            </td>
            <td class="text-center"><b>{{ $row->sales_order }}{{ $row->material_request }}</b><br>{{ $row->customer }}</td>
            <td class="align-top"><b>{{ $row->item_code }}</b><br>{{ $row->description }}</td>
            <td class="text-center" style="font-size: 12pt;"><b>{{ $row->qty_to_manufacture }}</b></td>
            <td class="text-center font-weight-bold" style="font-size: 11pt;">{{ $row->completed_qty }}</td>
            <td class="text-center font-weight-bold" style="font-size: 11pt;">{{ $row->qty_to_manufacture - $row->feedback_qty }}</td>
        </tr>
    </tbody>
    @empty
    <tbody>
        <tr>
            <td colspan="9" class="text-center" style="font-size: 15pt;">No scheduled task(s).</td>
        </tr>
    </tbody>
    @endforelse
</table>