


{{-- @media print and (orientation:landscape) {
	    @page {size: landscape; margin: margin: 25mm 25mm 25mm 25mm;}
	    header nav, footer {display: none;}
	    input {border: 0px;}
	} --}}
<style type="text/css">
	
	/*.div-1{    -ms-zoom: 1.665;
	transform: rotate(270deg) translate(-150mm, 0);
    transform-origin: 0 0;   }*/
	*{
		font-family: "Arial";
	}
	.div-1 span {
		font-size: 8pt;
   position: absolute;
   bottom: 10px;
   left: 10px;
}
.div-1 {
    position: relative;
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
		<td><img src="{{ asset('img/FUMACO Logo.png') }}" width="80"></td>
		<td style="text-align: center; font-size: 13pt;"><b>PRODUCTION SCHEDULE</b></td>
		<td style="text-align: center;"><b>ASSEMBLY</b></td>
	</tr>
	<tr>
		<td>SCHEDULED DATE:</td>
		<td style="font-size: 10pt;"><b>{{ $sched_format }}</b></td>
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
             <span class="pull-right badge badge-primary" style="font-size: 9pt;border: 1px solid black;">{{ $i++ }}</span>
             <h4 class="view-prod-details-btn">{{ $row['production_order'] }}</h4>
          </td>
          <td class="text-center" style="text-align:center;border: 1px solid black;"><b>{{ $row['reference_no'] }}</b><br>{{ $row['customer'] }}</td>
          <td class="align-top" style="border: 1px solid black;"><b>{{ $row['item_code'] }}</b><br>{{ $row['description'] }}</td>
          <td class="text-center" style="font-size: 12pt;text-align:center;border: 1px solid black;"><b>{{ $row['required_qty'] }}</b></td>
          {{--@foreach($row['processes'] as $process)
          @php
            $status_color = '';
            $qty = '';
            if($process->status == 'In Progress'){
                $status_color = '#F5B041';
            }

            if($process->status == 'Pending'){
                $status_color = '#ABB2B9';
            }

            if($process->status == 'Completed'){
                $status_color = '#2ECC71';
                $qty = '<br><label style="font-size: 10pt;">( '.$process->completed_qty.' )</label>';
            }
            
            @endphp 
          <td class="text-center text-white" style="border: 1px solid black;background-color: {{ $status_color }};text-align:center;">{{ $process->status }} {!! $qty !!}</td>
          @endforeach--}}
          <td class="text-center font-weight-bold" style="border: 1px solid black;font-size: 12pt;text-align:center;">{{ $row['completed_qty'] }}</td>
          <td class="text-center font-weight-bold" style="border: 1px solid black;font-size: 12pt;text-align:center;">{{ $row['balance_qty'] }}</td>
          <td class="text-center font-weight-bold" style="border: 1px solid black;font-size: 12pt;text-align:center;">&nbsp;</td>

       </tr>
    </tbody>
    @empty
    <tbody>
       <tr>
          <td colspan="9" class="text-center" style="font-size: 15pt;">No assigned task(s).</td>
       </tr>
    </tbody>
    @endforelse
</table>

</div>
<div style="margin-top:30px;">
<span>Printed by: <i>{{ Auth::user()->employee_name }} - {{  now()->toDateTimeString('h:m:s') }}</i></span>

</div>




<script src="{{ asset('js/core/ajax.min.js') }}"></script> 
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
		window.print();
		// window.close();
	});
</script>