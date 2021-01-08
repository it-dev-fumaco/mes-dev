

{{-- @media print and (orientation:landscape) {
	    @page {size: landscape; margin: margin: 25mm 25mm 25mm 25mm;}
	    header nav, footer {display: none;}
	    input {border: 0px;}
	} --}}
	
<style type="text/css">
	@page  
{ 
    size: auto;   /* auto is the initial value */ 

    /* this affects the margin in the printer settings */ 
    margin: 15mm 5mm 5mm 5mm;  
} 

	/*.div-1{    -ms-zoom: 1.665;
	transform: rotate(270deg) translate(-150mm, 0);
    transform-origin: 0 0;   }*/

	.div-1 span {
		font-size: 10.5pt;
   position: absolute;
   bottom: 10px;
   left: 10px;
   
}
.div-1 {
    position: relative;
    page-break-inside: avoid;
	font-family: "Arial";

}
</style>

@foreach($jobtickets as $i => $pro)
<div class="div-1"  style="width: 100%; min-height: 460px; border: 1px solid; margin-bottom: 10px;">
	<table style="width: 90%; border-collapse: collapse; font-size: 10pt; margin: 10px auto;">
		<col style="width: 18%;">
		<col style="width: 64%;">
		<col style="width: 18%;">
		<tr>
			<td><img src="{{ asset('img/FUMACO Logo.png') }}" width="80"></td>
			<td style="text-align: center; font-size: 18pt;"><b>JOB TICKET</b></td>
			<td style="text-align: center; font-size: 10pt;"><b>{{$pro['operation']}}</b></td>
		</tr>
		<tr>
			<td>SCHEDULED DATE:</td>
			<td style="font-size: 10pt;"><b>{{ date('M-d-Y', strtotime($pro['sched_date'])) }}</b></td>
			<td style="text-align: center; font-size: 14pt;"><b>{{ $pro['production_order'] }}</b></td>
		</tr>
		<tr>
			<td>MODEL:</td>
			<td><b>{{ $pro['model'] }}</b></td>
			<td rowspan="3">
				<center>
					<div class="qrcode" id="qr{{$i}}" data-id="{{ $pro['production_order'] }}"></div>
				</center>
			</td>
		</tr>
		<tr>
			<td>ITEM CODE:</td>
			<td><b>{{ $pro['item_code'] }}</b> - {!! $pro['description'] !!}</td>
		</tr>
		<tr>
			<td>CUTTING SIZE:</td>
			<td></td>
		</tr>
	</table>
	<table style="width: 85%; font-size: 10pt; border-collapse: collapse; margin: 10px auto;" border="1">
		<col style="width: 15%;">
		<col style="width: 30%;">
		<col style="width: 30%;">
		<col style="width: 13%;">
		<col style="width: 12%;">
		<tr>
			<th style="text-align: center;">SO / MREQ</th>
			<th style="text-align: center;">CUSTOMER</th>
			<th style="text-align: center;">PROJECT</th>
			<th style="text-align: center;">SALES TYPE</th>
			<th style="text-align: center;">QTY</th>
		</tr>
		<tr>
			<td style="text-align: center;">{{ $pro['sales_order'] }}{{ $pro['material_request'] }}</b></td>
			<td style="text-align: center;">{{ $pro['customer'] }}</b></td>
			<td style="text-align: center;">{{ $pro['project'] }}</b></td>
			<td style="text-align: center;">{{ $pro['sales_type'] }}</b></td>
			<td style="text-align: center;font-size: 11.5pt">{{ number_format($pro['qty']) }}</b></td>
		</tr>
	</table>
	<br>
	<table style="width: 98%; border-collapse: collapse; margin: -5.5px auto; " border="1">
			<col style="width: 14%; line-height: 30%;">
			<col style="width: 16%;">
			<col style="width: 6%;">
			<col style="width: 6%;">
			<col style="width: 6%;">
			<col style="width: 6%;">
			<col style="width: 14%;">
			<col style="width: 10%;">
			<col style="width: 12%;">
			<col style="width: 10%;">
			<tr style="font-size: 9.2pt;">
				<th style="text-align: center;font-size: 9.2pt;">WORKSTATION</th>
				<th style="text-align: center;font-size: 9.2pt;">PROCESS</th>
				<th style="text-align: center;font-size: 9.2pt;">QTY</th>
				<th style="text-align: center;font-size: 9.2pt;">GOOD</th>
				<th style="text-align: center;font-size: 9.2pt;">REJECT</th>
				<th style="text-align: center;font-size: 9.2pt;">BAL</th>
				<th style="text-align: center;font-size: 9.2pt;">OPERATOR</th>
				<th style="text-align: center;font-size: 9.2pt;">MACHINE</th>
				<th style="text-align: center;font-size: 9.2pt;">QC</th>
				<th style="text-align: center;font-size: 9.2pt;">Remarks</th>
			</tr>
			@foreach($pro['workstation'] as $jt)
			
			<tr style="line-height: 18px;">
				<td style="text-align: center; font-size: 11.2pt;" rowspan="{{ $jt['count'] }}">{{ $jt['workstation'] }}</td>
				@foreach($jt['jobticket_details'] as $rows)
				<td style="text-align: center; font-size: 11.2pt;">{{ $rows['process'] }}</td>
				<td style="text-align: center; font-size: 11.7pt;">{{ number_format($rows['qty'])}}</td>
				<td style="text-align: center; font-size: 11.2pt;">@if($rows['good'] == "") @else {{ number_format($rows['good'])}} @endif </td>
				<td style="text-align: center; font-size: 11.2pt;">@if($rows['good'] == "") @else{{ number_format($rows['reject'])}} @endif </td>
				<td style="text-align: center; font-size: 11.2pt;">@if($rows['good'] == "") @else{{ number_format($rows['bal'])}} @endif</td>
				<td style="text-align: center;line-height: 10px;">{{ $rows['operator'] }}</td>
				<td style="text-align: center; font-size: 10px;">@if($rows['machine']){{ $rows['machine'] }}@endif</td>
				<td style="text-align: center;">{{ $rows['status'] }}</td>
				<td style="text-align: center;">{{ $rows['remark'] }}</td>
				
			</tr>
			@endforeach
			@endforeach
	</table>

	<div style="width:100%;height:100%; padding-top:20px;">
		<table style="position:relative;bottom: 10px; width:100%;">
			<tr>
				<td style="font-size: 9pt; width:80%;">&nbsp;&nbsp;&nbsp;Printed by: <i>{{ Auth::user()->employee_name }} - {{  now()->toDateTimeString('h:m:s') }}</i></td>
				<td style="width:30%;">	Checked By:
				</td>
			</tr>
			<tr>
			<td style="width:80%;"></td>
				<td style="font-size: 10.5pt;width:30%;">______________________</td>
			</tr>
		</table>
	</div>
</div>
@endforeach

<script type="text/javascript" src="{{ asset('js/qrcode.js') }}"></script>
<script>

	$(document).ready(function(){
		$('.qrcode').each(function(){
			new QRCode($(this).attr('id'), {
			    text: $(this).data('id'),
			    width: 50,
			    height: 50,
			    colorDark : "#000000",
			    colorLight : "#ffffff",
			    correctLevel : QRCode.CorrectLevel.H
			});
		});
		// window.print();
		// window.close();
	});
</script>