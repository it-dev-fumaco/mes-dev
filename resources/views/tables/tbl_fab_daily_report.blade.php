

<table cellspacing="0" cellpadding="0" class="text-center table table-bordered" id="ceo" >
    <thead class="tableclass">
      <tr class="tableclass" style="font-size:10px;border:1px solid white;background-color:  #2874a6;color:white;font-weight:bold;padding:0; ">
        <th  class="tableclass" data-sort="name" rowspan="2" style="border:1px solid white;background-color:  #2874a6;color:white;font-weight:bold;padding:0;  ">Indeces</th>
        <th  class="tableclass" data-sort="name" colspan="{{$colspan_date}}" style="border:1px solid white;background-color:  #2874a6;color:white;font-weight:bold;padding:0;  ">Output(Sets)</th>
        <th  class="tableclass" data-sort="name" rowspan="2" style="border:1px solid white;background-color:  #2874a6;color:white;font-weight:bold;padding:0;  ">Total</th>
        <th  class="tableclass" data-sort="name" rowspan="2" style="border:1px solid white;background-color:  #2874a6;color:white;font-weight:bold;padding:0;  ">AVE./ PER DAY</th>

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
        <td class="text-center tableclass" style=""><b>{{$row['row_name']}}</b></td>
        
        @foreach($row['data'] as $r)
        @php
        if($row['row_name'] == "REALIZATION RATE" || $row['row_name'] == "AVERAGE MAN-HR UTILIZATION"){
          $background_clr="#82e0aa";
          $color="black";
          $value= $r['value_display'];


        }elseif ($r['stat'] == "Sunday") {
          if($r['stat_ot'] != 0 ){
            $background_clr='#b03a2e';
            $color="white";
            $value= $r['value_display'];
          }else{
            $background_clr='#b03a2e';
            $color="white";
            $value= $r['stat'];

          }
        }else{
          $background_clr="";
          $color="black";
          $value= $r['value_display'];

        }
        @endphp
          <td class="text-center tableclass" style="background-color:{{$background_clr}};color:{{$color}}" rowspan="">{{$value}}</td>

        @endforeach
        <td class="text-center tableclass" style="">{{$row['total']}}</td>
        <td class="text-center tableclass" style="">{{$row['avg']}}</td>

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
    