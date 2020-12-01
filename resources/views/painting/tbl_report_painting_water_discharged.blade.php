
<table class="text-center table table-striped" style="width: 100%;" id="ceo">
                <col style="width: 15%;">
                <col style="width: 15%;">
                <col style="width: 15%;">
                <col style="width: 15%;">
                <col style="width: 15%;">
                <col style="width: 20%;">

              <thead style="font-weight:bold; color:#e67e22;">
                
                <tr style="font-size:10px;">
                  <th style="font-weight:bold;" class="classme" data-sort="name">Date</th>
                  <th style="font-weight:bold;" class="classme" data-sort="name">HRS/ DAY</th>
                  <th style="font-weight:bold;" class="classme" data-sort="name">PREVIOUS</th>
                  <th style="font-weight:bold;" class="classme" data-sort="name">PRESENT</th>
                  <th style="font-weight:bold;" class="classme" data-sort="name">DISCHARGED</th>
                  <th style="font-weight:bold;" class="classme" data-sort="name">Operator</th>
            </thead>
            <tbody style="font-size:14px;">
           @forelse($data as $rows)
            
            <tr class="item">
                <td>{{$rows['water_date']}} </td>
                <td>{{$rows['operating_hrs']}}</td>
                <td>{!! $rows['previous'] !!}</td>
                <td>{!! $rows['present'] !!}</td>
                <td><b>{!! $rows['incoming_water_discharged'] !!}</b></td>
                <td>{{$rows['operator_name']}}</td>
                
            </tr>  
              
            @empty
                 <tr>
                    <td colspan="12" class="text-center" style="font-size: 11pt;">No Record Found</td>
                 </tr>
              @endforelse
            </tbody>
        </table>
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


