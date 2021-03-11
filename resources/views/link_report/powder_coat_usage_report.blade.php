<table class="table table-striped border">
   <col style="width: 10%;">
   <col style="width: 20%;">
   <col style="width: 28%;">
   <col style="width: 10%;">
   <col style="width: 10%;">
   <col style="width: 10%;">
   <col style="width: 12%;">
   <thead class="text-primary text-center" style="font-size: 7pt;">
      <th class="font-weight-bold">Date</th>
      <th class="font-weight-bold">Shift</th>
      <th class="font-weight-bold">Item Code</th>
      <th class="font-weight-bold">Current Qty</th>
      <th class="font-weight-bold">Consumed Qty</th>
      <th class="font-weight-bold">Balance Qty</th>
      <th class="font-weight-bold">Operator</th>
   </thead>
   <tbody style="font-size: 9pt;">
      @forelse($data as $rows)
      <tr class="item">
         <td class="text-center">{{ $rows['date'] }}</td>
         <td class="text-center">
            <span class="d-block">{{ $rows['shift_type'] }} ({{  $rows['operating_hrs']  }})</span>
         </td>
         <td class="text-justify">
            <span class="d-block font-weight-bold">{!! $rows['item_code'] !!}</span>
            <span class="d-block">{!! $rows['description'] !!}</span>
         </td>
         <td class="text-center">
            <span class="d-block font-weight-bold" style="font-size: 11pt;">{{ number_format($rows['current_qty']) }}</span>
            <span class="d-block">{{ $rows['uom'] }}</span>
         </td>
         <td class="text-center">
            <span class="d-block font-weight-bold" style="font-size: 11pt;">{{ number_format($rows['consumed_qty']) }}</span>
            <span class="d-block">{{ $rows['uom'] }}</span>
         </td>
         <td class="text-center">
            <span class="d-block font-weight-bold" style="font-size: 11pt;">{{ number_format($rows['balance_qty']) }}</span>
            <span class="d-block">{{ $rows['uom'] }}</span>
         </td>
         <td class="text-center">{{ $rows['operator_name'] }}</td>
      </tr>
      @if ($loop->last)
      <tr>
         <td colspan="7" class="text-right font-weight-bold text-uppercase p-1">
            <span style="font-size: 11pt;">Total Consumed Qty: </span><span class="mr-2" style="font-size: 14pt;">{{ number_format($count) }} Kg</span>
         </td>
      </tr>
      @endif
      @empty
      <tr>
         <td colspan="6"></td>
         <td class="text-center" style="font-size: 11pt;">No Record Found</td>
      </tr>
      @endforelse
   </tbody>
</table>
<div class="pull-right">
   <div id="tbl_painting_consumed_pagination" class="col-md-12 text-center">{{ $powder_data->links() }}</div>
</div>