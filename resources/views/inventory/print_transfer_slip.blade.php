<div class="divid" style="width: 95%; float: left; margin: 0 0.5% 1% 0.5%;min-height: 100px; border: 1px solid; padding: 1%; page-break-inside: avoid;">
<table style="width: 100%; font-size: 10.5pt; border-collapse: collapse;">
	<tr style="border-bottom: 1px solid;">
		<td style="width: 20%; text-align: center;">
			<img src="{{ asset('img/FUMACO Logo.png') }}" width="90">
		</td>
		<th style="width: 65%; text-align: center;">
			<span style="font-size: 13pt;">TRANSFER SLIP</span>
		</th>
		<td style="width: 15%; text-align: center;">
		</td>
	</tr>
</table>

<table style="width: 100%; font-size: 11.2pt; border-collapse: collapse;"> 
	<col style="width: 15%;">
	<col style="width: 25%;">
	<col style="width: 10%;">
	<col style="width: 10%;">
	<col style="width: 10%;">
	<col style="width: 10%;">
	<col style="width: 10%;">
	<tr style="border-top:1px solid; border-bottom:1px solid;">
		<th style="width: 10%;text-align: center">Production Order</th>
		<th style="width: 30%;text-align: justify">Item Code</th>
		<th style="width: 20%;text-align: center">Warehouse</th>
		<th style="width: 10%;text-align: center">Qty</th>
        <th style="width: 12%;text-align: center">For Transfer Qty</th>
        <th style="width: 12%;text-align: center">Actual Qty Received</th>
		<th style="width: 12%;text-align: center">Verified By</th>
	</tr>
	@foreach($data as $datas) 
	<tr style="border-bottom:1px solid;">
		<td style="text-align: center;"><b>{{ $datas['production_order'] }}</b></td>

		<td style="text-align: justify;"><b>{{ $datas['item_code'] }}</b><br><span style="font-size:10pt;">{!! $datas['description'] !!}</span></td>		
		<td style="text-align: center;">{{ $datas['t_warehouse'] }}</td>
		<td style="text-align: center;">{{ $datas['req_qty'] }} <br><span style="font-size:9.5pt;">{{ $datas['stock_uom'] }}</span></td>
		<td style="text-align: center;">{{ $datas['transferred_qty'] }}</td>
		<td style="text-align: center;">____________</td>
		<td style="text-align: center;">____________</td>
	</tr>
	@endforeach
</table>
<div style="font-size: 8pt; font-style: italic; float: right; margin-top: 10px;">Printed by: {{ Auth::user()->employee_name }} - Time: {{date('Y-m-d h:i A') }}</div>
</div>

<style type="text/css">
 	.divid{
		font-family: "Arial";
	}
	@page  
	{ 
		size: auto;   /* auto is the initial value */ 
		/* this affects the margin in the printer settings */ 
		margin: 13mm 5mm 5mm 5mm;  
	} 
</style>
<script>
	window.print();
	$(document).ready(function(){
        new QRCode('qr-code', {
            text: $('#qr-code').data('id'),
            width: 40,
            height: 40,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
	
	});
</script>
