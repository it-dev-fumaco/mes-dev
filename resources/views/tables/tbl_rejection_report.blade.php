

<table cellspacing="0" cellpadding="0" class="text-center table table-bordered" id="ceo" >
    <thead class="tableclass">
      <tr class="tableclass" style="font-size:10.5px;background-color:  #2874a6;color:white;font-weight:bold; ">
        <th  class="tableclass" data-sort="name" style="background-color:  #2874a6;color:white;font-weight:bold;padding:0;">{{$reject_category_name}}</th>
        @foreach($month_column as $values)
          <th class="tableclass" data-sort="name" style="color:white;font-weight:bold;padding:0;">{{$values}}</th>
        @endforeach
        <th class="tableclass" data-sort="name" style="color:white;font-weight:bold;padding:0;">Total</th>
        <th class="tableclass" data-sort="name" style="color:white;font-weight:bold;padding:0;background-color:#2ecc71;" rowspan="2">Reject Rate</th>
        <th class="tableclass" data-sort="name" style="color:white;font-weight:bold;padding:0;background-color:#2ecc71;" rowspan="2">Target</th>

      </tr>
      <tr class="item tableclass text-center" style="font-size: 10.5px;background-color:#82e0aa;">
        <td  class="text-center tableclass"><b>Total Output</b></td>
        @foreach($total_output_per_month as $topm)
          <th class="tableclass" data-sort="name" style="color:black;font-weight:bold;padding:0;font-size:10.5px;">{{$topm['sum']}}</th>
        @endforeach
        <th class="tableclass" data-sort="name" style="color:black;font-weight:bold;padding:0;font-size:10.5px;">{{$total_output}}</th>

      </tr>
    </thead>
    <tbody style=" border:1px solid black;" class="tableclass">
      
      @foreach($data as $rows => $row)
      <tr class="item tableclass text-left" style="font-size: 10.5px; ">
        <td class="text-left tableclass" style=""><span style="padding-left:15px;"><b>{{$row['series']}}.</b>&nbsp;<i>{{$row['reject']}}</i></span></td>
        @foreach($row['data'] as $r)
          <td class="text-center tableclass" style="color:{{($r['sum'] == 0) ? '#196f3d' :'#922b21'}};font-weight:{{($r['sum'] == 0) ? '' :'bold'}};"> {{$r['sum']}}</td>

        @endforeach
        <td class="text-center tableclass" style="color:{{($row['per_month'] == 0) ? '#196f3d' :'#922b21'}};font-weight:{{($row['per_month'] == 0) ? '' :'bold'}}; " > {{$row['per_month']}}</td>
        <td class="text-center tableclass" style="background-color:#2ecc71;"><b> {{$row['per_rate']}}%</b></td>
        <td class="text-center tableclass"  style="background-color:#2ecc71;"><b> 0.5%</b></td>

      </tr>
    @endforeach 
    <tr class="item tableclass text-center" style="font-size: 10.5px;">
      <td  class="text-center tableclass" style="font-size: 10.5px;background-color: #e74c3c; "><b>Total Reject</b></td>
      @foreach($total_reject_per_month as $trpm)
        <td class="text-center tableclass" style="font-size: 10.5px;background-color: #e74c3c; "> <b>{{$trpm['sum']}}</b></td>
      @endforeach
      <td class="text-center tableclass" style="font-size: 10.5px;background-color: #e74c3c; "> <b>{{$total_reject}}</b></td>
      <td class="text-center tableclass" rowspan="2" style="font-size: 10.5px;background-color: #f7dc6f; "><b> {{$reject_rate_for_total_reject}}%</b></td>
      <td class="text-center tableclass" rowspan="2" style="font-size: 10.5px;background-color: #f7dc6f; "><b> 0.5%</b></td>

    </tr>
    <tr class="item tableclass text-center" style="font-size: 10.5px;background-color: #e74c3c; ">
      <td  class="text-center tableclass"><b>Reject Rate</b></td>
      @foreach($reject_rate as $rr)
        <td class="text-center tableclass" ><b> {{$rr['sum']}}%</b></td>
      @endforeach
      <td class="text-center tableclass" > <b>{{$total_reject_rate}}%</b></td>

    </tr>
    </tbody>
    </table>
    <style>
    .sortable th,.sortable td {
    padding: 10.5px 30px;
    /* height:100%; */
    }
    .tableclass{
      border:1px solid black;
      text-align: center;
    }
    </style>
    <style type="text/css">
            table { width: 100%;

              }
            
    </style>
    <style type="text/css">
    
    
    </style>
    