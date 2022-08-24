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

        $production_orders = array_keys($backlogs->toArray());
    @endphp
    @forelse($production_orders as $production_order)
    @php
        if(!isset($backlogs[$production_order])){
            continue;
        }

        $production_details = collect($backlogs[$production_order])->first();
        $production_orders_grouped_by_process_name = collect($backlogs[$production_order])->groupBy('process_name');

        $unloaded_qry = isset($production_orders_grouped_by_process_name['Unloading']) ? collect($production_orders_grouped_by_process_name['Unloading'])->first() : [];
        $unloaded_qty = $unloaded_qry ? $unloaded_qry->completed_qty : 0;
    @endphp
    <tbody style="font-size: 8pt;">
       <tr>
            <td class="text-center align-middle">
                <div class="container">
                    <span class="pull-right badge badge-primary" style="font-size: 9pt;">{{ $i++ }}</span>
                    <h6 class="view-prod-details-btn">{{ $production_order }}</h6>
                </div>
                <div class="container">
                    {{ Carbon\Carbon::parse($production_details->planned_start_date)->format('M-d-Y') }}
                </div>
            </td>
            <td class="text-center">
                <b>{{ $production_details->sales_order ? $production_details->sales_order : $production_details->material_request }}</b><br>
                {{ $production_details->customer }}
                </td>
            <td class="align-top"><b>{{ $production_details->item_code }}</b><br>{{ $production_details->description }}</td>
            <td class="text-center" style="font-size: 12pt;"><b>{{ $production_details->qty_to_manufacture }}</b></td>
            @foreach($processes as $process)
                @php
                    $process_details = isset($production_orders_grouped_by_process_name[$process]) ? collect($production_orders_grouped_by_process_name[$process])->first() : [];
                @endphp
                @if ($process_details)
                    @php
                        $qty = $status_color = null;   
                        
                        switch ($process_details->process_status) {
                            case 'In Progress':
                                $status_color = '#F5B041';
                                break;
                            case 'Not Started':
                            case 'Pending':
                                $status_color = '#ABB2B9';
                                break;
                            case 'Completed':
                                $status_color = '#2ECC71';
                                $qty = '<br><span style="font-size: 10pt;">( '.$process_details->process_status.' )</span>';
                                break;
                            default:
                                $qty = null;
                                $status_color = null;
                                break;
                        }
                    @endphp 
                    <td class="text-center text-white" style="background-color: {{ $status_color }}">{{ $process_details->process_status }} {!! $qty !!}</td>
                @else
                    <td class="text-center text-white" style="background-color: #ABB2B9;">Pending</td>
                @endif
            @endforeach
            <td class="text-center font-weight-bold">
                <span style="font-size: 12pt;">
                    {{ $unloaded_qty }}
                </span> <br>
                <span style="font-size: 8pt;">
                    Balance: {{ $production_details->qty_to_manufacture - $unloaded_qty }}
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