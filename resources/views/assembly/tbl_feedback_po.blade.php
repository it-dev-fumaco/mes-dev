<table class="table table-striped text-center">
   <thead class="text-primary" style="font-size: 7pt;">
      <th class="text-center sticky-header"><b>P.O</b></th>
      <th class="text-center sticky-header"><b>Customer</b></th>
      <th class="text-center sticky-header"><b>Item</b></th>
      <th class="text-center sticky-header"><b>QTY</b></th>
   </thead>
   <tbody style="font-size: 9pt;">
      @forelse($data as $row)
      <tr>
         <td class="text-center"><a href="#" data-jtno="{{ $row['production_order'] }}" class="prod-details-btn" style="color: black;">{{ $row['production_order'] }}</a></td>
         <td class="text-center" >{{ $row['customer'] }}</td>
         <td class="text-center" >{{ $row['item_code'] }}- {{$row['item_description']}}</td>
         <td class="text-center">{{ $row['qty'] }} {{ $row['stock_uom'] }}<br>
            <span class="badge badge-{{ ($row['status'] == 'Full Feedback') ? 'success' : 'warning' }}" style="font-size: 8pt;">{{ $row['status'] }}</span>
         </td>
      </tr>
      @empty
      <tr>
         <td colspan="4" class="text-center">No record(s) found</td>
      </tr>
      @endforelse
   </tbody>
</table>