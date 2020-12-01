<div class="col-md-12">
    <div class="row">
        <div class="col-md-12" style="margin-left:10%;margin-right:10%;">
        <h3><b>Material Request Alert </b></h3>
        <br>
        <p style="display:block;line-height:8px;">Material Request No.: <b> {{ $data['mreq'] }}</b></p>
        <p style="display:block;line-height:8px;">Purchase Type: <b>{{ $data['purchase_request'] }} </b></p>
        <p style="display:block;line-height:8px;">SO: <b> {{ $data['sales_order_no']  }}</b></p>
        <p style="display:block;line-height:8px;">Customer: <b> {{ $data['customer'] }}</b></p>
        <p style="display:block;line-height:8px;">Project: <b> {{ $data['project'] }}</b></p>

        <table style="border: 1px solid black;border-collapse: collapse;">
        <thead>
            <colgroup>
                    <col style="width: 50%;">
                    <col style="width: 30%;">
                    <col style="width: 20%;">
            </colgroup>
            <tr style="text-align:center">
                <th style="border: 1px solid black;">Item Name</th>
                <th style="border: 1px solid black;">Required Date</th>
                <th style="border: 1px solid black;"> Qty</th>
            </tr>
            
        </thead>
        <tbody>
            @foreach( $data['items'] as $rows)
            <tr>
                <td style="border: 1px solid black;text-align:justfy;"><b>{{ $rows['item_code'] }}</b> <br> {{ $rows['item_name'] }}</td>
                <td style="border: 1px solid black;text-align:center;">{{ $rows['schedule_date'] }}</span></td>
                <td style="border: 1px solid black;text-align:center;">{{ $rows['qty'] }} <span style="padding-left:5px;">{{ $rows['stock_uom'] }} </span></td>
            
            </tr>
            @endforeach
        </tbody>
        </table>
        
        
        <p style="display:block;line-height:8px;">For more details please log in to http://10.0.0.83:8000</p>
        <p style="display:block;line-height:8px;">Created By: <i> {{ $data['created_by']  }} </i></p>

        <br>
        <hr>
        <b>Fumaco Inc / MES-Material Request </b><br></br><small>Auto Generated E-mail from MES - NO REPLY </small>
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