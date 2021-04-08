

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
    
    {{-- @foreach($jobtickets as $i => $pro) --}}
    <div class="div-1"  style="width: 100%; min-height: 460px; border: 1px solid; margin-bottom: 10px;">
        <table style="width: 90%; border-collapse: collapse; font-size: 10pt; margin: 10px auto;">
            <col style="width: 18%; ">
            <col style="width: 64%;">
            <col style="width: 18%;">
            <tr>
                <td><img src="{{ asset('img/FUMACO Logo.png') }}" width="80"></td>
                <td style="text-align: center; font-size: 18pt;"><b>Preventive Maintenance</b></td>
                <td style="text-align: center; font-size: 10pt;"><b></b></td>
            </tr>
            <tr style="line-height:16px;">
                <td>Date Reported:</td>
                <td style="font-size: 10pt;"><b>{{ \Carbon\Carbon::parse($main['created_at'] )->format('M d, Y h:m A') }}</b></td>
                <td style="text-align: center; font-size: 14pt;"><b>{{ $main['preventive_maintenance_id'] }}</b></td>
            </tr>
            <tr style="line-height:16px;">
                <td>Reported By: </td>
                <td style="font-size: 10pt;"><b>{{ $main['created_by'] }} </b></td>
                <td rowspan="3">
                    <center>
                        <div class="qrcode" id="qr{{$main['preventive_maintenance_id']}}" data-id="{{ $main['preventive_maintenance_id'] }}"></div>
                    </center>
                </td>
            </tr>
            <tr style="line-height:16px;">
                <td>Operation:</td>
                <td style="font-size:15px;"><b>{{ $main['operation'] }}</b></td>
                <td  style="text-align: center; font-size: 13pt;"></td>
            </tr>
            <tr style="line-height:16px;">
                <td>Machine Code:</td>
                <td style="font-size:15px;"><b>{{ $main['machine_code'] }} [ {{ $main['machine_name'] }} ] </b></td>
                <td  style="text-align: center; font-size: 13pt;"></td>
            </tr>
            <tr style="line-height:16px;">
                <td>Status:</td>
                <td  style="text-align: left; font-size: 13pt;"><i>{{-- $main['status'] --}}</i></td>
                <td  style="text-align: center; font-size: 13pt;"></td>
            </tr>

        </table>
        
        <br>

        <table style="width: 98%; border-collapse: collapse; margin: -5.5px auto; " border="1">
			<col style="width: 30%;">
			<col style="width: 30%;">
            <col style="width: 40%;">
            <tr>
                <th style="text-align: center;font-size: 9.2pt;">Task </th>
				<th style="text-align: center;font-size: 9.2pt;">Task Description</th>
                <th style="text-align: center;font-size: 9.2pt;">Remarks</th>
            </tr>


            @forelse($main['task'] as $r)
                <tr>
                    <th style="text-align: center;font-size: 9.2pt;">{{$r->preventive_maintenance_task}} </th>
                    <th style="text-align: center;font-size: 9.2pt;">{{$r->preventive_maintenance_desc}} </th>
                    <th style="text-align: center;font-size: 9.2pt;"></th>
                </tr>
            @empty
                <label style="font-size:12px;">-</label>
            @endforelse
            

			
	    </table>
    
        <div style="width:100%;height:100%; padding-top:20px;">
            <table style="position:relative;bottom: 10px; width:100%;">
                <tr>
                    <td style="font-size: 10.5pt; width:80%;">&nbsp;&nbsp;&nbsp;Printed by: <i>{{ Auth::user()->employee_name }} - {{  now()->toDateTimeString('h:m:s') }}</i></td>
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
    {{-- @endforeach --}}
    
    <script type="text/javascript" src="{{ asset('js/qrcode.js') }}"></script>

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
	});
</script>
