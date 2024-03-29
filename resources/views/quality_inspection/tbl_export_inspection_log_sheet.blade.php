

<table cellspacing="0" cellpadding="0" class="text-center table table-bordered" style="" id="ceo" >


<thead style="font-weight:bold; color:#e67e22;">

<tr style="font-size:10px;">
  
  <th style="color:#d35400; padding: 7px 5px 6px 12px;background-color:#fef5e7;border: 1px solid black;font-weight: bold;" class="classme" data-sort="name" colspan="11">Item Details</th>
  
  
  <th style="color:#d35400; padding: 7px 5px 6px 12px;background-color:#fef5e7;border: 1px solid black;background-color:#f6ddcc; font-weight: bold;" class="classme" data-sort="name" colspan="{{ $quality_check }}">Quality Check</th>
  <th style="color:#d35400; padding: 7px 5px 6px 12px;background-color:#fef5e7;border: 1px solid black;font-weight: bold;" class="classme" data-sort="name" rowspan="3">Status </th>
  <th style="color:#d35400; padding: 7px 5px 6px 12px;background-color:#fef5e7;border: 1px solid black;font-weight: bold;" class="classme" data-sort="name" rowspan="3">Remarks </th>
  <th style="color:#d35400; padding: 7px 5px 6px 12px; background-color:#fef5e7;border: 1px solid black;font-weight: bold;" class="classme" data-sort="name" rowspan="3">Operator </th>
    <th style="color:#d35400; padding: 7px 5px 6px 12px; background-color:#fef5e7;border: 1px solid black;font-weight: bold;" class="classme" data-sort="name" rowspan="3">QC Staff </th>

  <!-- <th style=";color:black;border: 1px solid black;background-color:#fef5e7;" class="classme" data-sort="name" rowspan="3">Reference Document</th>
  <th style=";color:black;border: 1px solid black;background-color:#fef5e7;" class="classme" data-sort="name" rowspan="3">Remarks</th> -->
</tr>
<tr>
  <th style="color:#d35400; padding: 7px 5px 6px 12px;background-color:#fef5e7;font-weight: bold;font-size: 13px;border: 2px solid black;" class="classme" data-sort="name" rowspan="2">Production Order</th>
  <th style="color:#d35400; padding: 7px 5px 6px 12px;background-color:#fef5e7;font-weight: bold;font-size: 13px;border: 2px solid black;" class="classme" data-sort="name" rowspan="2">Ref No</th>
  <th style="color:#d35400; padding: 7px 5px 6px 12px; background-color:#fef5e7;font-weight: bold;font-size: 13px;border: 2px solid black;" class="classme" data-sort="name" rowspan="2">Description</th>
  <th style="color:#d35400; padding: 7px 5px 6px 12px;background-color:#fef5e7;font-weight: bold;font-size: 13px;border: 2px solid black;" class="classme" data-sort="name" rowspan="2">Workstation</th>
  <th style="color:#d35400; padding: 7px 5px 6px 12px; background-color:#fef5e7;font-weight: bold;font-size: 13px;border: 2px solid black;" class="classme" data-sort="name" rowspan="2">Process</th>
  <th style="color:#d35400; padding: 7px 5px 6px 12px;background-color:#fef5e7;font-weight: bold;font-size: 13px;border: 2px solid black;" class="classme" data-sort="name" rowspan="2">Machine</th>
  <th style="color:#d35400; padding: 7px 5px 6px 12px; background-color:#fef5e7;font-weight: bold;font-size: 13px;border: 2px solid black;" class="classme" data-sort="name" rowspan="2">Date</th>
  <th style="color:#d35400; padding: 7px 5px 6px 12px;background-color:#fef5e7;font-weight: bold;font-size: 13px;border: 2px solid black;" class="classme" data-sort="name" colspan="4">Quantity</th>
  <th style=";color: #d35400;padding: 7px 5px 6px 12px ;font-weight: bold;border: 1px solid black;background-color:#f6ddcc;font-size: 11px;" class="classme" data-sort="name" colspan="{{$count_header_variable}}">Variable Checked</th>
  <th style=";color: #d35400;padding: 7px 5px 6px 12px ;font-weight: bold;border: 1px solid black;background-color:#f6ddcc;font-size: 11px;" class="classme" data-sort="name" colspan="{{$count_header_visual}}"> Visual Checked</th>
</tr>
<tr style="">
  <th style="color:#d35400; padding: 7px 5px 6px 12px;border: 1px solid black;background-color:#fef5e7;border: 1px solid black;font-weight: bold;font-size: 13px;" class="classme" data-sort="name">Batch</th>
  <th style="color:#d35400; padding: 7px 5px 6px 12px;border: 1px solid black;background-color:#fef5e7;border: 1px solid black;font-weight: bold;font-size: 13px;" class="classme" data-sort="name">Samples</th>
 <th style="color:#d35400; padding: 7px 5px 6px 12px;border: 1px solid black;background-color:#fef5e7;border: 1px solid black;font-weight: bold;font-size: 12px;" class="classme" data-sort="name">Actual</th>
<th style="color:#d35400; padding: 7px 5px 6px 12px;border: 1px solid black;background-color:#fef5e7;border: 1px solid black;font-weight: bold;font-size: 12px;" class="classme" data-sort="name">Reject</th>
   @foreach($header as $values)

 <th style=";color: #d35400;border: 1px solid black;background-color:#fef5e7;padding: 7px 5px 6px 12px ;font-size: 11px;font-weight: bold;" class="classme" data-sort="name">{{$values['reject_checklist']}}</th>
  @endforeach

  
</tr>
</thead>
<tbody style="font-size:12px;">
  <tr>
    
  </tr>
  <tr>
    
  </tr>
@forelse($data as $rows)
  
  <tr class="item" style="font-size: 14px;">
    <td style="padding:0px; margin:0px;"><b>{{$rows['production_order']}}</b><span style="display: block;font-size: 10px;">{{$rows['customer']}}</span></td>
    <td style="">{{$rows['reference_id']}} </td>
    <td style="padding:0px; margin:0px;"><b>{{$rows['item_code']}}</b> - {{$rows['decsription']}}</td>
    <td style="padding:0px; margin:0px;">{{$rows['workstation']}} </td>
    <td style="padding:0px; margin:0px;">{{$rows['process']}}</td>
    <td style="padding:0px; margin:0px;">{{$rows['machine']}}</td>
    <td style="padding:0px; margin:0px;">{{$rows['inspection_date']}}<span style="display: block;">{{$rows['time']}}</span> </td>
    <td style="padding:0px; margin:0px;"><span style="display: block;">{{$rows['batch_qty']}}</span> </td>
    <td style="padding:0px; margin:0px;"><span style="display: block;">{{$rows['samples']}}</span> </td>
    <td style="padding:0px; margin:0px;"><span style="display: block;">{{$rows['actual_qty']}}</span></td>
    <td style="padding:0px; margin:0px;"><span style="display: block;">{{$rows['reject']}}</span></td>
    <td colspan="{{ $quality_check }}" style="margin:0px;padding:0px;">
      <table style="width:100%;text-align:center;border:0px;">
          @foreach($width as $value)
           <col style="width: {{$value['value']}}%;">
          @endforeach
          <tr></tr>
          @foreach($rows['checklist'] as $value)
           <tr style="word-break: break-word;padding: 0;margin: 0;border:0;">
                  
              @foreach($value['checklist'] as $s)
                @php
                if($s['stat'] == 'good'){
                  $bcolor="#2ecc71";
                  $fontw="bold";
                  }else{
                  $bcolor="#ec7063";
                  $fontw="bolder";
                }
                @endphp 
                <td class="classme" style="color:white; text-align:center;font-size: 11px;font-weight:{{$fontw}}; background-color: {{$bcolor}};vertical-align: middle;padding: 0;margin: 0;" data-sort="name">{{$s['value']}}</td>
              @endforeach
            </tr>
          @endforeach
      </table>
    </td>
  <td style="padding: 7px 5px 6px 12px;">{{$rows['status']}} </td>
  <td style="padding: 7px 5px 6px 12px;">{{$rows['remarks']}} </td>
  <td style="padding: 7px 5px 6px 12px;">{{$rows['operator']}} </td>
  <td style="padding: 7px 5px 6px 12px;">{{$rows['qc_staff']}} </td>
      
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
        table {height: 100%; width: 100%;}
        .col {
  word-wrap: break-word;
  width: 75px;
  min-height: 50px;
  border: 1px solid black;
}
table{ 
    </style>

