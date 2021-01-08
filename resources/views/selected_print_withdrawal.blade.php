@foreach($stock_entries as $idx => $ste)
<div style="width: 95%; float: left; margin: 0 0.5% 1% 0.5%;min-height: 100px; border: 1px solid; padding: 1%; page-break-inside: avoid;" id="divwidraw_id">
<table style="width: 100%; font-size: 11.2pt; border-collapse: collapse;">
	<tr style="border-bottom: 1px solid;">
		<td style="width: 20%; text-align: center;">
			<img src="{{ asset('img/FUMACO Logo.png') }}" width="90">
		</td>
		<th style="width: 65%; text-align: center;">
			<span style="font-size: 13pt;">WITHDRAWAL SLIP</span>
		</th>
		<td style="width: 15%; text-align: center;">
        <center>
				<div class="qrcode" id="qr{{ $idx }}" data-id="{{ $ste['production_order'] }}" style="padding-bottom: 5px;"></div>
                <label><b>{{ $ste['production_order'] }}<b></label>
                <br>
		</center>
		</td>
	</tr>
</table>
<table style="width: 100%; font-size: 11.2pt; border-collapse: collapse;">
	<tr>
		<td style="width: 12%;">Reference No:</td>
		<td style="width: 63%;"><b>{{ $ste['sales_order'] }}{{ $ste['material_request'] }}</b></td>
		<td style="width: 12%;">Date:</td>
		<td style="width: 13%;"><b>{{ date('M-d-Y', strtotime($ste['posting_date'])) }}</b></td>
	</tr>
	<tr>
		<td style="width: 12%;">Customer:</td>
		<td style="width: 63%;"><b>{{ $ste['customer'] }}</b></td>
		<td style="width: 12%;">Reference:</td>
		<td style="width: 13%;"><b>{{ $ste['production_order'] }}</b></td>
	</tr>
	<tr>
		<td style="width: 12%;">Project:</td>
		<td style="width: 63%;"><b>{{ $ste['project'] }}</td>
		{{--<td style="width: 12%;">STE No.:</td>
		<td style="width: 13%;"><b>{{ $ste['name'] }}</b></td>--}}
	</tr>
</table>
<table style="width: 100%; font-size: 11.2pt; border-collapse: collapse;">
	<tr style="border-top:1px solid; border-bottom:1px solid;">
		<th style="width: 40%;text-align: justify;">Item Code</th>
		<th style="width: 20%;text-align: center;">Warehouse</th>
		<th style="width: 10%;text-align: center;">Qty</th>
		<th style="width: 12%;text-align: center;">Received Qty</th>
		<th style="width: 12%;text-align: center;">Verified By</th>
		<th style="width: 11%;text-align: center;">Status</th>
	</tr>
	@foreach($ste['items'] as $item)
	<tr style="border-bottom:1px solid;">
		<td style="text-align: justify;"><b>{{ $item->item_code }}</b><br>{!! $item->description !!}</td>
		<td style="text-align: center;">{{ $item->s_warehouse }}</td>
		<td style="text-align: center;">{{ number_format((float)$item->qty, 4, '.', '') }} {{ $item->stock_uom }}</td>
		<td style="text-align: center;">____________</td>
		<td style="text-align: center;">____________</td>
		<td style="text-align: center;">{{ $item->status }}</td>
	</tr>
	@endforeach
</table>
<div style="font-size: 8pt; font-style: italic; float: right; margin-top: 10px;">Printed by: {{ Auth::user()->employee_name }} - Time: {{date('Y-m-d h:i A') }}</div>
</div>
@endforeach

<style type="text/css">
 	
	#divwidraw_id{
		font-family: "Arial";
	}
	@page  
	{ 
		size: auto;   /* auto is the initial value */ 

		/* this affects the margin in the printer settings */ 
		margin: 13mm 5mm 5mm 5mm;  
	} 

</style>

<!-- <script src="{{ asset('js/core/ajax.min.js') }}"></script>  -->
<script type="text/javascript" src="{{ asset('js/qrcode.js') }}"></script>
<script>

	$(document).ready(function(){
		$('.qrcode').each(function(){
			new QRCode($(this).attr('id'), {
			    text: $(this).data('id'),
			    width: 40,
			    height: 40,
			    colorDark : "#000000",
			    colorLight : "#ffffff",
			    correctLevel : QRCode.CorrectLevel.H
			});
		});
		// window.print();
		// window.close();
	});
</script>

