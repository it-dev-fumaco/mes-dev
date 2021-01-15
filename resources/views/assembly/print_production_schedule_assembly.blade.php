<style type="text/css">
	*{
		font-family: "Arial";
    }

    table {
        border-collapse: collapse;
    }
</style>

	<table style="width: 90%; border-collapse: collapse; font-size: 8pt; margin: 10px auto;">
		<col style="width: 18%;">
		<col style="width: 64%;">
		<col style="width: 18%;">
        <tr>
            <td rowspan="2"><img src="{{ asset('img/FUMACO Logo.png') }}" width="85"></td>
            <td style="text-align: center; font-size: 13pt;"><b>PRODUCTION SCHEDULE</b></td>
            <td rowspan="2" style="text-align: center; text-transform: uppercase;"><b>{{ $operation_details->operation_name }}</b></td>
        </tr>
        <tr>
            <td style="font-size: 10pt; text-align:center;">Scheduled Date: <b>{{ $sched_format }}</b></td>
        </tr>
    </table>
<div class="div-1"  style="width: 100%; min-height: 500px; margin-bottom: 10px;">
<table style="width: 100%;padding-bottom:20px:border: 1px solid black;" class="table table-bordered div-table">
    <col style="width: 13.33%;">
    <col style="width: 13.33%;">
    <col style="width: 20%;">
    <col style="width: 13.33%;">
    <col style="width: 13.33%;">
    <col style="width: 13.33%;">
    <col style="width: 13.33%;">
    <thead class="text-primary" style="font-size: 8pt; text-transform: uppercase;border: 1px solid black">
       <th class="text-center" style="border: 1px solid black;"><b>Production Order</b></th>
       <th class="text-center" style="border: 1px solid black;"><b>Customer</b></th>
       <th class="text-center" style="border: 1px solid black;"><b>Item Details</b></th>
       <th class="text-center" style="border: 1px solid black;"><b>Qty</b></th>
       <th class="text-center" style="border: 1px solid black;"><b>Completed</b></th>
       <th class="text-center" style="border: 1px solid black;"><b>Balance</b></th>
       <th class="text-center" style="border: 1px solid black;"><b>Tranfered Qty</b></th>
    </thead>
    @php
        $i = 1;
    @endphp
    @forelse($scheduled_arr as $row)
    <tbody style="font-size: 8pt;">
       <tr style="border: 1px solid black;">
          <td class="text-center align-middle" style="text-align:center;border: 1px solid black;">
            
             <h4 class="view-prod-details-btn">{{ $row['production_order'] }}</h4>
          </td>
          <td class="text-center" style="text-align:center;border: 1px solid black;"><b>{{ $row['reference_no'] }}</b><br>{{ $row['customer'] }}</td>
          <td class="align-top" style="border: 1px solid black;"><b>{{ $row['item_code'] }}</b><br>{{ $row['description'] }}</td>
          <td class="text-center" style="font-size: 12pt;text-align:center;border: 1px solid black;"><b>{{ $row['required_qty'] }}</b></td>
         
          <td class="text-center font-weight-bold" style="border: 1px solid black;font-size: 12pt;text-align:center;">{{ $row['completed_qty'] }}</td>
          <td class="text-center font-weight-bold" style="border: 1px solid black;font-size: 12pt;text-align:center;">{{ $row['balance_qty'] }}</td>
          <td class="text-center font-weight-bold" style="border: 1px solid black;font-size: 12pt;text-align:center;">&nbsp;</td>

       </tr>
    </tbody>
    @empty
    <tbody>
       <tr>
          <td colspan="9" style="font-size: 15pt; text-align: center; text-transform: uppercase; padding: 5px;">No scheduled production order(s) found</td>
       </tr>
    </tbody>
    @endforelse
</table>

</div>
<div style="margin-top:20px; font-size: 10pt;">
<span>Printed by: <i>{{ Auth::user()->employee_name }} - {{  now()->toDateTimeString('h:m:s') }}</i></span>

</div>




<script src="{{ asset('js/core/ajax.min.js') }}"></script> 
<script>

	$(document).ready(function(){

		window.print();
		// window.close();
	});
</script>