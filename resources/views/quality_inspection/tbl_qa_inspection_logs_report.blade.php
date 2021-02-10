<table cellspacing="0" cellpadding="0" class="text-center table table-bordered tableFixHead" style="width: 2600px; border-collapse: separate;overflow:;" id="ceo" >


<thead class="classheader">

<tr style="border-bottom:1px solid white;">
  
  <th style="color:#ffffff; padding: 7px 5px 6px 12px;border:0.8px solid white;background-color:#0277BD;font-weight: bold;font-size: 13px;width:100px; border: 1px solid white; border-right: 1px solid white;z-index:2;"  class="classme" data-sort="name" colspan="7">Item Details</th>
  
  <th style="color:#ffffff; padding: 7px 5px 6px 12px;border:0.8px solid white;background-color:#0277BD;font-weight: bold;font-size: 13px;width:100px; border: 1px solid white; border-right: 1px solid white;z-index:2;"  class="classme" data-sort="name" colspan="4" rowspan="2">Quantity</th>
  <th style="color:#ffffff; padding: 7px 5px 6px 12px;border:0.8px solid white;background-color:#0277BD;font-weight: bold;font-size: 13px;width:100px; border: 1px solid white; border-right: 1px solid white;z-index:2;"  class="classme" data-sort="name" colspan="4" rowspan="2">Quality Check</th>
  <th style="color:#ffffff; padding: 7px 5px 6px 12px;border:0.8px solid white;background-color:#0277BD;font-weight: bold;font-size: 13px;width:100px; border: 1px solid white; border-right: 1px solid white;z-index:2;"  class="classme" data-sort="name" rowspan="3">Status </th>
  <th style="color:#ffffff; padding: 7px 5px 6px 12px;border:0.8px solid white;background-color:#0277BD;font-weight: bold;font-size: 13px;width:100px; border: 1px solid white; border-right: 1px solid white;z-index:2;"  class="classme" data-sort="name" rowspan="3">Remarks </th>
  <th style="color:#ffffff; padding: 7px 5px 6px 12px;border:0.8px solid white;background-color:#0277BD;font-weight: bold;font-size: 13px;width:100px; border: 1px solid white; border-right: 1px solid white;z-index:2;"  class="classme" data-sort="name" rowspan="3">Operator </th>
  <th style="color:#ffffff; padding: 7px 5px 6px 12px;border:0.8px solid white;background-color:#0277BD;font-weight: bold;font-size: 13px;width:100px; border: 1px solid white; border-right: 1px solid white;z-index:2;"  class="classme" data-sort="name" rowspan="3">QC Staff </th>

</tr>
<tr style="border-bottom:1px solid white;">
  <th style="color:#ffffff;z-index:2; padding: 7px 5px 6px 12px;border:0.8px solid white;background-color:#0277BD;font-weight: bold;font-size: 13px;width:100px;" class="classme" data-sort="name" rowspan="2">Production Order</th>
  <th style="color:#ffffff;z-index:2; padding: 7px 5px 6px 12px;border:0.8px solid white;background-color:#0277BD;font-weight: bold;font-size: 13px;width:60px;" class="classme" data-sort="name" rowspan="2">Ref No</th>
  <th style="color:#ffffff;z-index:2; padding: 7px 5px 6px 12px;border:0.8px solid white; background-color:#0277BD;font-weight: bold;font-size: 13px;width:360px;" class="classme" data-sort="name" rowspan="2">Description</th>
  <th style="color:#ffffff;z-index:2; padding: 7px 5px 6px 12px;border:0.8px solid white;background-color:#0277BD;font-weight: bold;font-size: 13px;width:100px;" class="classme" data-sort="name" rowspan="2">Workstation</th>
  <th style="color:#ffffff;z-index:2; padding: 7px 5px 6px 12px;border:0.8px solid white; background-color:#0277BD;font-weight: bold;font-size: 13px;width:100px;" class="classme" data-sort="name" rowspan="2">Process</th>
  <th style="color:#ffffff;z-index:2; padding: 7px 5px 6px 12px;border:0.8px solid white;background-color:#0277BD;font-weight: bold;font-size: 13px;width:50px;" class="classme" data-sort="name" rowspan="2">Machine</th>
  <th style="color:#ffffff;z-index:2; padding: 7px 5px 6px 12px;border:0.8px solid white; background-color:#0277BD;font-weight: bold;font-size: 13px;width:100px;" class="classme" data-sort="name" rowspan="2">Date</th>
</tr>
<tr style="border-bottom:1px solid white;">
  <th style="color:#ffffff;z-index:2; padding: 7px 5px 6px 12px;border:0.8px solid white;width:20px;background-color:#0277BD;width:8px;font-weight: bold;font-size: 13px;" class="classme" data-sort="name">Batch</th>
  <th style="color:#ffffff;z-index:2; padding: 7px 5px 6px 12px;border:0.8px solid white;width:20px;background-color:#0277BD;width:8px;font-weight: bold;font-size: 13px;" class="classme" data-sort="name">Samples</th>
 <th style="color:#ffffff;z-index:2; padding: 7px 5px 6px 12px;border:0.8px solid white;width:20px;background-color:#0277BD;width:8px;font-weight: bold;font-size: 12px;" class="classme" data-sort="name">Actual</th>
<th style="color:#ffffff;z-index:2; padding: 7px 5px 6px 12px;border:0.8px solid white;width:20px;background-color:#0277BD;width:8px;font-weight: bold;font-size: 12px;" class="classme" data-sort="name">Reject</th>
  @foreach($header as $values)
 <th style="z-index:2;color: #ffffff;width:10px;background-color:#0277BD;padding: 7px 5px 6px 12px ;font-size: 11px;font-weight: bold;border:0.8px solid white;border-bottom: 50px;" class="classme" data-sort="name">{{$values->reject_category_name}}</th>
  @endforeach

  
</tr>
</thead>
<tbody style="font-size:10.5px;">

@forelse($data as $rows)
  
  <tr class="item" style="font-size: 11.5px;">
    <td style="padding:0px; margin:0px;"><a class="prod-details-btn" data-jtno="{{$rows['production_order']}}"><b><span>{{$rows['production_order']}}</span></b></a><span style="display: block;font-size: 10px;">{{$rows['customer']}}</span></td>
    <td style="">{{$rows['reference_id']}} </td>
    <td style="padding:0px; margin:0px;text-align:justify;"><b>{{$rows['item_code']}}</b> - {{$rows['decsription']}}</td>
    <td style="padding:0px; margin:0px;">{{$rows['workstation']}} </td>
    <td style="padding:0px; margin:0px;">{{$rows['process']}}</td>
    <td style="padding:0px; margin:0px;">{{$rows['machine']}}</td>
    <td style="padding:0px; margin:0px;">{{$rows['inspection_date']}}<span style="display: block;">{{$rows['time']}}</span> </td>
    <td style="padding:0px; margin:0px;"><span style="display: block;">{{$rows['batch_qty']}}</span> </td>
    <td style="padding:0px; margin:0px;"><span style="display: block;">{{$rows['samples']}}</span> </td>
    <td style="padding:0px; margin:0px;"><span style="display: block;">{{$rows['actual_qty']}}</span></td>
    <td style="padding:0px; margin:0px;"><span style="display: block;">{{$rows['reject']}}</span></td>
         
              @foreach($rows['checklist'] as $value)
                @php
                    if ($value['stat'] =="QC Passed") {
                      $div_color="#52be80";
                    }elseif ($value['stat'] =="QC Failed") {
                      $div_color="#ec7063";
                    }else{
                      $div_color="#fae5d3";
                    }
                @endphp
                    <td style="padding:0px; margin:0px;vertical-align:baseline;" class="align-middle">
                      <table style="padding:0px; margin:0px;vertical-align:baseline;border:0.8px solid white;">
                        @if($value['count'] == 0)
                          <tr style="padding:0px; margin:0px;vertical-align:baseline;border:0.8px solid white;"  class="align-middle">
                            <td  class="align-middle" style="padding:0px; margin:0px;vertical-align:baseline;border:0.8px solid white;background-color:{{$div_color}};" colspan="{{$value['colspan']}}">{{$value['stat']}}</td>
                          </tr>
                        @endif
                        @if($value['count'] == 1)
                          @foreach($value['value'] as $row)
                          <tr style="padding:0px; margin:0px;vertical-align:baseline; ">
                            <td style="padding:0px; margin:0px;vertical-align:baseline; background-color:{{$div_color}};"  class="align-middle">{{$row->reject_reason}}</td>
                            <td style="padding: 7px 5px 6px 12px; margin:1px;vertical-align:baseline;background-color:{{$div_color}}"  class="align-middle">{{($row->reject_value == "undefined") ? "Failed":$row->reject_value}}</td>
                          </tr>
                          @endforeach
                        @endif
                      </table>
                    </td>
              @endforeach
              <td style="padding:0px; margin:0px;"><span style="display: block;">{{$rows['status']}}</span> </td>
              <td style="padding:0px; margin:0px;"><span style="display: block;">{{$rows['remarks']}}</span> </td>
              <td style="padding:0px; margin:0px;"><span style="display: block;">{{$rows['operator']}}</span></td>
              <td style="padding:0px; margin:0px;"><span style="display: block;">{{$rows['qc_staff']}}</span></td>
</tr>
   


@empty
 <tr>
    <td colspan="21" class="text-center" style="font-size: 11pt;">No Record Found</td>
 </tr>
@endforelse
</tbody>
</table>
<style>
.sortable th,.sortable td {
padding: 10px 30px;
height:100%;
}
</style>
<style type="text/css" media="screen">
        table {height: 100%; width: 100%;
           border-collapse: collapse;
          }
        
</style>
<style type="text/css">


</style>
