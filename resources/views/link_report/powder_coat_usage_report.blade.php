<table class="text-center table table-striped">
   <col style="width: 13.33%;">
   <col style="width: 13.33%;">
   <col style="width: 13.33%;">
   <col style="width: 13.33%;">
   <col style="width: 13.33%;">
   <col style="width: 13.33%;">
   <col style="width: 20%;">
   <thead class="text-primary font-weight-bold" style="font-size: 6pt;">
      <th>Date</th>
      <th>Shift</th>
      <th>Item Code</th>
      <th>Current Qty</th>
      <th>Consumed Qty</th>
      <th>Balance Qty</th>
      <th>Operator</th>
   </thead>
   <tbody style="font-size: 8pt;">
      @forelse($data as $rows)
      <tr class="item">
         <td>{{ $rows['date'] }}</td>
         <td>{!! $rows['operating_hrs'] !!}</td>
         <td>{!! $rows['item_code'] !!}</td>
         <td>
            <span class="font-weight-bold">{!! $rows['current_qty'] !!}</span>
            <span style="display:block; font-size:10px;">{{$rows['uom']}}</span></td>
         <td><b>{!! $rows['consumed_qty'] !!}</b> <span style="display:block; font-size:10px;">{{$rows['uom']}}</span></td>
         <td><b>{!! $rows['balance_qty'] !!}</b> <span style="display:block; font-size:10px;">{{$rows['uom']}}</span></td>
         <td>{{$rows['operator_name']}}</td>
      </tr>  
   
   @empty
      <tr>
         <td colspan="12" class="text-center" style="font-size: 11pt;">No Record Found</td>
      </tr>
   @endforelse
</tbody>
</table>
<div class="text-right" style="margin-right: 15%;margin-top: 20px;"> <h6 style="display: inline;">Total Consumed Qty =</h6><span style="padding-left: 10px;font-size: 20px; display: inline-block;"> <b>{{ $count}} Kg</b></span></div>
<center>
<div id="tbl_painting_consumed_pagination" class="col-md-12 text-center" style="text-align: center;">
{{ $powder_data->links() }}
</div>
</center>
<style>
.sortable th,.sortable td {
padding: 10px 30px;
}


.sortable th.asc:after {
display: inline;
content: '↓';
color: black;
font-size: 20px;
}
.sortable th.desc:after {
display: inline;
content: '↑';
color: black;
font-size: 20px;
}


</style>


