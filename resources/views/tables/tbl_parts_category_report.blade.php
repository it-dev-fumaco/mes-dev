

<table cellspacing="0" cellpadding="0" class="text-center table table-bordered" id="ceo" >
    <thead class="tableclass">
      <tr class="tableclass" style="font-size:10px;border:1px solid white;background-color:  #2874a6;color:white;font-weight:bold;padding:0; ">
        <th  class="tableclass" data-sort="name" rowspan="2" style="border:1px solid white;background-color:  #2874a6;color:white;font-weight:bold;padding:0;  ">Indeces</th>
        <th  class="tableclass" data-sort="name" colspan="{{$colspan_date}}" style="border:1px solid white;background-color:  #2874a6;color:white;font-weight:bold;padding:0;  ">Output(Sets)</th>
        <th  class="tableclass" data-sort="name" rowspan="2" style="border:1px solid white;background-color:  #2874a6;color:white;font-weight:bold;padding:0;  ">Total</th>
        <th  class="tableclass" data-sort="name" rowspan="2" style="border:1px solid white;background-color:  #2874a6;color:white;font-weight:bold;padding:0;  ">AVG per Day</th>

    </tr>
      <tr class="tableclass" style="font-size:10px;border:1px solid white;background-color:  #2874a6;color:white;font-weight:bold; ">
        @foreach($date_column as $values)
          <th class="tableclass" data-sort="name" style="border:1px solid white;background-color: {{($values['stat_sunday'] != null ) ? '#b03a2e':'#2874a6'}};color:white;font-weight:bold;padding:0;  ">{{$values['date']}} <br><span style="display:{{($values['stat'] != null ) ? '':'none'}};">OT</span></th>
        @endforeach
      </tr>
    </thead>
    <tbody style=" border:1px solid black;" class="tableclass">
      
   @foreach($data as $rows => $row)
      <tr class="item tableclass text-center" style="font-size: 10.5px; ">
        <td class="text-center tableclass" style=""><b>{{$row['parts']}}</b></td>
        
        @foreach($row['data'] as $r)
        @php
        if ($r['stat'] == "Sunday") {
            if($r['sum'] > 0){
                $value_show= $r['sum'];
            }else{
                $value_show= $r['stat'];

            }
            $background_clr='#b03a2e';
            $color="white";

        }else{
          $background_clr="";
          $color="black";
          $value_show= $r['sum'];
        }
        @endphp
          <td class="text-center tableclass" style="background-color: {{$background_clr}};color:{{$color}}" rowspan=""> {{$value_show}}</td>

        @endforeach
        <td class="text-center tableclass" style="">{{$row['total']}}</td>
        <td class="text-center tableclass" style="">{{$row['t_day']}}</td> 

      </tr>
    @endforeach
    </tbody>
    </table>
    <style>
    .sortable th,.sortable td {
    padding: 10px 30px;
    /* height:100%; */
    }
    .tableclass{
      border:1px solid black;
      text-align: center;
    }
    </style>
    <style type="text/css">
            table { width: 100%;
               border-collapse: collapse;

              }
            
    </style>
    <style type="text/css">
    
    
    </style>
    