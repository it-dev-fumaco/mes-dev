

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
                <td style="text-align: center; font-size: 18pt;"><b>Maintenance Request</b></td>
                <td style="text-align: center; font-size: 10pt;"><b></b></td>
            </tr>
            <tr style="line-height:16px;">
                <td>Date Reported:</td>
                <td style="font-size: 10pt;"><b>{{ \Carbon\Carbon::parse($main['date_reported'] )->format('M d, Y h:m A') }}</b></td>
                <td style="text-align: center; font-size: 14pt;"><b>{{ $main['machine_breakdown_id'] }}</b></td>
            </tr>
            <tr style="line-height:16px;">
                <td>Reported By: </td>
                <td style="font-size: 10pt;"><b>{{ $main['reported_by'] }} </b></td>
                <td rowspan="3">
                    <center>
                        <div class="qrcode" id="qr{{$main['machine_breakdown_id']}}" data-id="{{ $main['machine_breakdown_id'] }}"></div>
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
                <td style="font-size:15px;"><b>{{ $main['machine_id'] }} [ {{ $main['machine_name'] }} ] </b></td>
                <td  style="text-align: center; font-size: 13pt;"></td>
            </tr>
            <tr style="line-height:16px;">
                <td>Status:</td>
                <td  style="text-align: left; font-size: 13pt;"><i>{{ $main['status'] }}</i></td>
                <td  style="text-align: center; font-size: 13pt;"></td>
            </tr>

        </table>
        <table style="width: 85%; font-size: 10pt; border-collapse: collapse; margin: 10px auto;" border="1">
            <col style="width: 20%;">
            <col style="width: 25%;">
            <col style="width: 27.5%;">
            <col style="width: 27.5%;">
            <tr>
                <th style="text-align: center;">Resolved Date</th>
                <th style="text-align: center;">Maintenance Type</th>
                <th style="text-align: center;">Complaints/Problems: </th>
                <th style="text-align: center;">Assigned Maintenance Staff:</th>
            </tr>
            <tr>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($main['date_resolved'] )->format('M d, Y h:m A') }}</td>
                <td style="text-align: center;">{{ $main['type'] }}</td>
                <td style="text-align: center;">{{ ($main['type'] == "Breakdown")? $main['breakdown_reason']: $main['corrective_reason'] }}</td>
                <td style="text-align: center;line-height:10px;">
                    @forelse($main['main_staff'] as $r)
                    <label style="font-size:12px;">{{ $r->employee_name}}</label><br>
                    @empty
                    <label style="font-size:12px;">-</label>
                    @endforelse
                  
              </td>
            </tr>
        </table>
        <br>

        <table style="width: 98%; border-collapse: collapse; margin: -5.5px auto; " border="1">
			<col style="width: 50%; line-height: 30%;">
			<col style="width: 50%;">
			<tr style="font-size: 9.2pt;">
				<th style="text-align: center;font-size: 9.2pt;">Finding/s</th>
				<th style="text-align: center;font-size: 9.2pt;">Work Done</th>
			</tr>

            <tr style="line-height: 18px;">
				<td style="font-size: 12px;height:100px; text-align:center;">{{ $main['findings'] }} </td>
                <td style="font-size: 12px;height:100px; text-align:center;">{{ $main['work_done'] }}</td>

			</tr>

			
	    </table>
        <br>
        <table style="width: 98%; border-collapse: collapse; margin: -5.5px auto; " border="1">
			<col style="width: 30%;">
			<col style="width: 30%;">
            <col style="width: 30%;">

			<tr>
				<th style="text-align: center;font-size: 10pt;line-height:18px;" colspan="3"> LABOR USED</th>
			</tr>
            <tr>
                <th style="text-align: center;font-size: 9.2pt;">Craft</th>
				<th style="text-align: center;font-size: 9.2pt;">Men</th>
                <th style="text-align: center;font-size: 9.2pt;">Hours</th>
            </tr>


            <tr style="line-height: 8px;">
				<td style="font-size: 15pt;height:20px;"></td>
				<td style="text-align: center; font-size: 11.2pt;height:20px;"></td>
                <td style="font-size: 15pt;height:20px;"></td>
			</tr>
            <tr style="line-height: 8px;">
				<td style="font-size: 15pt;height:20px;"></td>
				<td style="text-align: center; font-size: 11.2pt;height:20px;"></td>
                <td style="font-size: 15pt;height:20px;"></td>
			</tr>
            <tr style="line-height: 8px;">
				<td style="font-size: 15pt;height:20px;"></td>
				<td style="text-align: center; font-size: 11.2pt;height:20px;"></td>
                <td style="font-size: 15pt;height:20px;"></td>
			</tr>
            <tr style="line-height: 8px;">
				<td style="font-size: 15pt;height:20px;"></td>
				<td style="text-align: center; font-size: 11.2pt;height:20px;"></td>
                <td style="font-size: 15pt;height:20px;"></td>
			</tr>
            

			
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
