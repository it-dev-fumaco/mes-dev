
<table class="text-center table table-striped" style="width: 100%;" id="ceo">
                <col style="width: 13.33%;">
                <col style="width: 13.33%;">
                <col style="width: 13.33%;">
                <col style="width: 13.33%;">
                <col style="width: 13.33%;">
                <col style="width: 13.33%;">
                <col style="width: 20%;">

              <thead style="font-weight:bold; color:#e67e22;">
                
                <tr style="font-size:10px;">
                  <th style="font-weight:bold;" class="classme" data-sort="name">Date</th>
                  <th style="font-weight:bold;" class="classme" data-sort="name">Shift</th>
                  <th style="font-weight:bold;" class="classme" data-sort="name">Item Code</th>
                  <th style="font-weight:bold;" class="classme" data-sort="name">Current Qty</th>
                  <th style="font-weight:bold;" class="classme" data-sort="name">Consumed Qty</th>
                  <th style="font-weight:bold;" class="classme" data-sort="name">Balance QTY</th>
                  <th style="font-weight:bold;" class="classme" data-sort="name">Operator</th>
            </thead>
            <tbody style="font-size:14px;">
           @forelse($data as $rows)
            
            <tr class="item">
                <td>{{$rows['date']}} </td>
                <td>{{$rows['operating_hrs']}}</td>
                <td>{!! $rows['item_code'] !!} </td>
                <td><b>{!! $rows['current_qty'] !!}</b> <span style="display:block; font-size:10px;">{{$rows['uom']}}</span></td>
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
  {{ $data->links() }}
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


