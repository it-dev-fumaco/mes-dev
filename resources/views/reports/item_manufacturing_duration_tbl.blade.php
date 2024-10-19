<table class="table table-bordered table-striped" style="font-size: 10pt;">
    <colgroup>
        <col style="width: 15%">
        <col style="width: 29%">
        <col style="width: 10%">
        <col style="width: 13%">
        <col style="width: 13%">
        <col style="width: 20%">
    </colgroup>
    <tr>
        <th class="text-center">Production Order</th>
        <th class="text-center">Item</th>
        <th class="text-center">Qty</th>
        <th class="text-center">Actual Start Date</th>
        <th class="text-center">Actual End Date</th>
        <th class="text-center">Total Duration (in Hours) per Item</th>
    </tr>
    @forelse ($list as $item)
        @php
            switch ($item['operation']) {
                case 3:
                    $operation = 'Wiring and Assembly';
                    break;
                case 2:
                    $operation = 'Painting';
                    break;
                default:
                    $operation = 'Fabrication';
                    break;
            }
        @endphp
        <tr>
            <td class="text-center">
                <b>{{ $item['production_order'] }}</b> <br>
                <span class="badge badge-primary">{{ $operation }}</span> <br>
                <small>{{ Carbon\Carbon::parse($item['production_order_creation'])->format('M. d, Y h:i A') }}</small>
            </td>
            <td class="text-center">
                <div class="row p-1">
                    <div class="col-3">
                        <img src="{{ $item['image'] }}" class="w-100">
                    </div>
                    <div class="col-9 text-justify" style="font-size: 9pt">
                        <b>{{ $item['item_code'] }}</b> - {{ strip_tags($item['description']) }}
                    </div>
                </div>
            </td>
            <td class="text-center">
                <b>{{ $item['completed_qty'] }}</b> <br>
                <small>{{ $item['stock_uom'] }}</small>
            </td>
            <td class="text-center">{{ Carbon\Carbon::parse($item['actual_start_date'])->format('M. d, Y h:i A') }}</td>
            <td class="text-center">{{ Carbon\Carbon::parse($item['actual_end_date'])->format('M. d, Y h:i A') }}</td>
            <td class="text-center">
                {{ $item['total_duration'] }} hour(s) <br>
                @php
                    $minutes = Carbon\Carbon::parse($item['actual_end_date'])->diffInMinutes($item['actual_start_date']);
                    $hours = floor($minutes / 60);
                    $remaining_minutes = $minutes % 60;
                @endphp
                <small>{{ $hours }} hour(s) and {{ $remaining_minutes }} minute(s)</small>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan=6 class="text-center">
                No record(s) found
            </td>
        </tr>
    @endforelse
</table>