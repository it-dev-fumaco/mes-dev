<table style="width: 100%;">
    <col style="width: 15%;">
    <col style="width: 15%;">
    <col style="width: 20%;">
    <col style="width: 10%;">
    <col style="width: 12%;">
    <col style="width: 12%;">
    <col style="width: 16%;">
    <thead class="text-primary" style="font-size: 8pt; text-transform: uppercase;">
       <th class="text-center"><b>Production Order</b></th>
       <th class="text-center"><b>Customer</b></th>
       <th class="text-center"><b>Item Details</b></th>
       <th class="text-center"><b>Qty</b></th>
       <th class="text-center"><b>Loading</b></th>
       <th class="text-center"><b>Unloading</b></th>
       <th class="text-center"><b>Completed</b></th>
    </thead>
    @php
        $i = 1;
        $processes = ['Loading', 'Unloading'];
    @endphp
    @forelse($previously_scheduled_production_orders as $row)
    @php
        $planned_start_date = isset($backlogs_arr[$row->production_order]['Loading']) ? Carbon\Carbon::parse($backlogs_arr[$row->production_order]['Loading']['planned_date'])->format('M-d-Y') : null;
        $unloaded_qty = isset($backlogs_arr[$row->production_order]['Unloading']) ? $backlogs_arr[$row->production_order]['Unloading']['completed_qty'] : 0;
    @endphp
    <tbody style="font-size: 8pt;">
       <tr>
            <td class="text-center align-middle">
                <div class="container">
                    <span class="pull-right badge badge-primary" style="font-size: 9pt;">{{ $i++ }}</span>
                    <h6 class="view-prod-details-btn">{{ $row->production_order }}</h6>
                </div>
                <div class="container">
                    {{ $planned_start_date }}
                </div>
            </td>
            <td class="text-center">
                <b>{{ $row->sales_order ? $row->sales_order : $row->material_request }}</b><br>
                {{ $row->customer }}
                </td>
            <td class="align-top"><b>{{ $row->item_code }}</b><br>{{ $row->description }}</td>
            <td class="text-center" style="font-size: 12pt;"><b>{{ $row->qty_to_manufacture }}</b></td>
            @foreach($processes as $process)
                @if (isset($backlogs_arr[$row->production_order][$process]))
                    @php
                        $process_arr = $backlogs_arr[$row->production_order][$process];

                        $status = $process_arr['status'];
                        $qty = $status_color = null;   
                        
                        switch ($process_arr['status']) {
                            case 'In Progress':
                                $status_color = '#F5B041';
                                break;
                            case 'Pending':
                                $status_color = '#ABB2B9';
                                break;
                            case 'Completed':
                                $status_color = '#2ECC71';
                                $qty = '<br><span style="font-size: 10pt;">( '.$process_arr['completed_qty'].' )</span>';
                                break;
                            default:
                                $qty = '';
                                $status_color = '';
                                break;
                        }
                    @endphp 
                    <td class="text-center text-white" style="background-color: {{ $status_color }}">{{ $status }} {!! $qty !!}</td>
                @else
                    <td class="text-center text-white" style="background-color: #ABB2B9;">Pending</td>
                @endif
            @endforeach
            <td class="text-center font-weight-bold">
                <span style="font-size: 12pt;">
                    {{ $unloaded_qty }}
                </span> <br>
                <span style="font-size: 8pt;">
                    Balance: {{ $row->qty_to_manufacture - $unloaded_qty }}
                </span>
            </td>
        </tr>
    </tbody>
    @empty
    <tbody>
        <tr>
            <td colspan="9" class="text-center" style="font-size: 15pt;">No assigned task(s).</td>
        </tr>
    </tbody>
    @endforelse
</table>