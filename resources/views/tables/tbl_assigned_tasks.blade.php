<table class="table" id="assigned-tasks-table">
   <col style="width: 12%;">
   <col style="width: 18%;">
   <col style="width: 40%;">
   <col style="width: 20%;">
   <col style="width: 10%;">
   <thead class="text-primary" style="font-size: 8pt;">
      <th class="text-center"><b>Production Order</b></th>
      <th class="text-center"><b>Customer</b></th>
      <th class="text-center"><b>Item Details</b></th>
      <th class="text-center"><b>Process</b></th>
      <th class="text-center"><b>Balance Qty</b></th>
   </thead>
   @forelse($assigned_tasks as $index => $row)
   <tbody style="font-size: 10pt;">
      <tr>
         <td class="text-center align-middle">
            <span class="pull-right badge badge-primary" style="font-size: 9pt;">{{ $index + 1 }}</span>
            <h6>{{ $row['production_order'] }}</h6>
         </td>
         <td class="text-center"><b>{{ $row['sales_order'] }}{{ $row['material_request'] }}</b><br>{{ $row['customer']}}</td>
         <td class="align-top"><b>{{ $row['production_item'] }}</b><br>{{ $row['description'] }}</td>
         <td class="text-center"><b>{{ $row['process'] }}</b></td>
         <td class="text-center" style="font-size: 14pt; padding: 0;"><b>{{ number_format($row['balance_qty']) }}</b></td>
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