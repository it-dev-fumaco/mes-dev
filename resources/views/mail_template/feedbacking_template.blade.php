<div class="col-md-12">
    <div class="row">
        <div class="col-md-12" style="margin-left:10%;margin-right:10%;">
        <h3><b>Production Feedback Alert </b></h3>
        <br>
        <p style="display:block;line-height:8px;">Date: <b> {{ $data['posting_date'] }} {{ $data['posting_time'] }}</b></p>
        <p style="display:block;line-height:8px;">Ref. No.: <b> {{ $data['ste'] }}</b></p>
        <p style="display:block;line-height:8px;">{{ ($data['sales_order_no'] == '')? 'MREQ No.:' : 'Sales Order No.:'}} <b> {{ ($data['sales_order_no'] == '')? $data['mreq'] : $data['sales_order_no']  }} </b></p>
        <p style="display:block;line-height:8px;">Customer: <b> {{ $data['customer'] }} </b></p>

        <table style="border: 1px solid black;border-collapse: collapse;">
        <thead>
            <colgroup>
                    <col style="width: 70%;">
                    <col style="width: 30%;">
            </colgroup>
            <tr style="text-align:center">
                <th style="border: 1px solid black;">Item Name</th>
                <th style="border: 1px solid black;">Completed Qty</th>
            </tr>
            
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid black;text-align:justfy;"><b>{{ $data['item_code'] }}</b> <br> {{ $data['item_name'] }}</td>
                <td style="border: 1px solid black;text-align:center;">{{ $data['completed_qty'] }} <span style="padding-left:5px;">{{ $data['uom'] }} </span></td>
            </tr>
        </tbody>
        </table>
        
        
        <p style="display:block;line-height:8px;">For more details please log in to http://10.0.0.83:8000</p>
        <p style="display:block;line-height:8px;">Feedbacked By: <i> {{ $data['feedbacked_by']  }} </i></p>

        <br>
        <hr>
        <b>Fumaco Inc / MES-Feedbacking </b><br></br><small>Auto Generated E-mail from MES - NO REPLY </small>
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