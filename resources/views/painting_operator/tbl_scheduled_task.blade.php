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
    @endphp
    @forelse($scheduled_arr as $row)
    <tbody style="font-size: 8pt;">
       <tr>
          <td class="text-center align-middle">
             <span class="pull-right badge badge-primary" style="font-size: 9pt;">{{ $i++ }}</span>
             <h6 class="view-prod-details-btn">{{ $row['production_order'] }}</h6>
          </td>
          <td class="text-center"><b>{{ $row['reference_no'] }}</b><br>{{ $row['customer'] }}</td>
          <td class="align-top"><b>{{ $row['item_code'] }}</b><br>{{ $row['description'] }}</td>
          <td class="text-center" style="font-size: 12pt;"><b>{{ $row['required_qty'] }}</b></td>
          @foreach($row['processes'] as $process)
          @php
            $status_color = '';
            $qty = '';
            if($process->status == 'In Progress'){
                $status_color = '#F5B041';
            }

            if($process->status == 'Pending'){
                $status_color = '#ABB2B9';
            }

            if($process->status == 'Completed'){
                $status_color = '#2ECC71';
                $qty = '<br><span style="font-size: 10pt;">( '.$process->completed_qty.' )</span>';
            }
            
            @endphp 
          <td class="text-center text-white" style="background-color: {{ $status_color }};">{{ $process->status }} {!! $qty !!}</td>
          @endforeach
          <td class="text-center font-weight-bold">
            <span style="font-size: 12pt;">
                {{ $row['completed_qty'] }}
            </span> <br>
            <span style="font-size: 8pt;">
                Balance: {{ $row['balance_qty'] }}
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