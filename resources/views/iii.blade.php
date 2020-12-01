{{-- <table border="1">
	<tr>
		<td style="background-color: red;">MES Status: Not Started</td>
		<td style="background-color: orange;">MES Status: In Progress</td>
		<td style="background-color: green;">MES Status: Completed</td>
	</tr>
	<tr>
		<td style="vertical-align: top;">
			<table border="1">
				<tr>
					<td>ERP Production Order</td>
					<td>ERP Status</td>
					<td>MES Production Order</td>
					<td>MES Status</td>


<<<<<<< HEAD
					Albert Gregorio Dela Cruz 12345555555
=======
					Albert Gregorio IT Dept
>>>>>>> 07088f95d07c68187be7c449a0ea9e464c7704d1
					
				</tr>
				@foreach($not_started as $i)
				<tr>
					<td>{{ $i['erp_production_order'] }}</td>
					<td>{{ $i['erp_status'] }}</td>
					<td>{{ $i['mes_production_order'] }}</td>
					<td>{{ $i['mes_status'] }}</td>
				</tr>
				@endforeach
			</table>
		</td>
		<td style="vertical-align: top;">
			<table border="1">
				<tr>
					<td>ERP Production Order</td>
					<td>ERP Status</td>
					<td>MES Production Order</td>
					<td>MES Status</td>
				</tr>
				@foreach($in_progress as $i)
				<tr>
					<td>{{ $i['erp_production_order'] }}</td>
					<td>{{ $i['erp_status'] }}</td>
					<td>{{ $i['mes_production_order'] }}</td>
					<td>{{ $i['mes_status'] }}</td>
				</tr>
				@endforeach
			</table>
		</td>
		<td style="vertical-align: top;">
			<table border="1">
				<tr>
					<td>ERP Production Order</td>
					<td>ERP Status</td>
					<td>MES Production Order</td>
					<td>MES Status</td>
				</tr>
				@foreach($completed as $i)
				<tr>
					<td>{{ $i['erp_production_order'] }}</td>
					<td>{{ $i['erp_status'] }}</td>
					<td>{{ $i['mes_production_order'] }}</td>
					<td>{{ $i['mes_status'] }}</td>
				</tr>
				@endforeach
			</table>
		</td>
	</tr>
</table>
 --}}

 <table border="1">
 	<tr>
 		<td>MES Production Order</td>
 		<td>MES Status</td>
 		<td>MES Qty</td>
 		<td>MES Produced Qty</td>
 		<td>MES Feedback Qty</td>
 		<td>ERP Production Order</td>
 		<td>ERP Status</td>
 		<td>ERP Qty</td>
 		<td>ERP Feedback Qty</td>
 	</tr>
 	@foreach($r as $m)
 	<tr>
 		<td>{{ $m['mes_po'] }}</td>
 		<td>{{ $m['mes_status'] }}</td>
 		<td>{{ $m['mes_qty_to_manufacture'] }}</td>
 		<td>{{ $m['mes_produced_qty'] }}</td>
 		<td>{{ $m['mes_feedback_qty'] }}</td>
 		<td>{{ $m['erp_po'] }}</td>
 		<td>{{ $m['erp_status'] }}</td>
 		<td>{{ $m['erp_qty_to_manufacture'] }}</td>
 		<td>{{ $m['erp_produced_qty'] }}</td>
 	</tr>
 	@endforeach
 </table>