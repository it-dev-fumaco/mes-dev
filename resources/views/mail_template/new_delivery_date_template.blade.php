<div class="col-md-12">
    <div class="row">
        <div class="col-md-12" style="margin-left:10%;margin-right:10%;">
        <h3><b>Reschedule Delivery Date Alert </b></h3>
        <br>
        <p style="display:block;line-height:8px;">Reference No: <b>{{ $data['reference'] }} </b></p>
        <p style="display:block;line-height:8px;">Customer: <b>{{ $data['customer'] }}</b></p>
        
        <table style="border: 2px solid white; width:100%;">
            <col style="width: 40%;">
            <col style="width: 20%;">
            <col style="width: 40%;">
            <tr style="font-size: 10pt;text-align:center;border: 2px solid white;">
                <td style="font-size: 14pt;text-align:center;border: 2px solid white;" >Delivery Date: <b>{{ $data['orig_delivery_date'] }}</b></td>
                <td style="font-size: 25pt;text-align:center;border: 2px solid white;font-weight:bolder;">>>></td>
                <td style="text-align:center;border: 2px solid white;"><span style="font-size: 14pt;">Reschedule Date: <b>{{ $data['resched_date'] }}  </span> </b></td>
               
            </tr>
        </table>

        <table style="border: 1px solid black;width: 100%; border-collapse: collapse;">
            <col style="width: 50;">
            <col style="width: 20%;">
            <col style="width: 30%;">

            <tr class="text-center">
                <th>ITEM DETAILS</th>
                <th>QTY</th>
                <th>REASON</th>
            </tr>
            <tr style="font-size: 10pt;">
                <td style="font-size: 10pt;text-align:justify;"><span class="font-weight-bold">
                     <b>{{ $data['item_code'] }}</b> <br> {{ $data['description'] }}
                </td>
                <td style="font-size: 10pt;text-align:center;" >{{ $data['qty'] }} &nbsp;{{ $data['uom'] }} </td>
                <td style="font-size: 10pt;text-align:center;" > <b> {{ $data['resched_reason'] }}</b></td>

        
            </tr>
            
        </table>

        <p style="display:block;line-height:8px;">For more details please log in to http://10.0.0.83:8000</p>
        <p style="display:block;line-height:8px;"><b>Reschedule By: </b><i> {{ $data['resched_by']  }} </i></p>

        <br>
        <hr>
        <b>Fumaco Inc / MES-Reschedule Delivery </b><br></br><small>Auto Generated E-mail from MES - NO REPLY </small>
        </div>
    
    </div>
</div>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
th, td {
  padding: 10px;
}
</style>