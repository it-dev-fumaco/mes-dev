<div class="row">
   @foreach($schedule as $sched)
            <div class="table-responsive">
            	<div class="col-md-2" style="float: left;font-size: 10pt;">
			         <div class="card">
			            <div class="card-body">
			            <b>Production:</b> {{ $sched['name'] }}<br>
                  		<b>Customer:</b> {{ $sched['customer'] }}<br>
                  		<b>Job Ticket:</b> {{ $sched['jt'] }}<br>
                  		<b>Delivery Date:</b> {{ $sched['delivery_date'] }}
			            <div class="col-md-12" id="report_table"></div>
			            </div>
			         </div>
			      </div>
			    
            	<div class="col-md-10" style="float: right;">
	               <table class="table table-bordered">
	               	  <thead class="text-primary" style="font-size: 10pt !important; font-weight: bold;background-color: #27517f;">
<!-- 	               	  	 <th class="text-center" rowspan="5"></th>
 -->	                 <th class="text-center"><b>Process</b></th>
	                     <th class="text-center"><b>QTY</b></th>
	                     <th class="text-center"><b>Start Time</b></th>
	                     <th class="text-center"><b>End Time</b></th>
	                     <th class="text-center"><b>Duration</b></th>
	                     <th class="text-center"><b>Good</b></th>
	                     <th class="text-center"><b>Reject</b></th>
	                     <th class="text-center"><b>Rework</b></th>
	                     <th class="text-center"><b>Status</b></th>
	                     <th class="text-center"><b>Quality Check</b></th>
	                  </thead>
	                  <tbody style="font-size: 9pt;">  
	                   @foreach($sched['jt_details'] as $row) 
	             		
<!-- 	                   <td><b>{{ $row['workstation'] }}</b></td>           	
 -->	                  	<td><b>Production:</b> {{ $sched['name'] }}<br>
                  		<b>Customer:</b> {{ $sched['customer'] }}<br>
                  		<b>Job Ticket:</b> {{ $sched['jt'] }}<br>
                  		<b>Delivery Date:</b> {{ $sched['delivery_date'] }}</td>
 							@forelse($row['details'] as $rows)
	                  		
		                     <tr>
				                     @php
				                     if ($rows->status == 'Unassigned') {
				                        $badge_color = 'danger';
				                     }elseif ($rows->status == 'Accepted') {
				                        $badge_color = 'primary';
				                     }elseif ($rows->status == 'In Progress') {
				                        $badge_color = 'warning';
				                     }elseif ($rows->status == 'Completed') {
				                        $badge_color = 'success';
				                     }else{
				                        $badge_color = 'default';
				                     }
				                     @endphp
		                     	<td>@if($rows->machine != "") Machine: {{ $rows->machine }}  @endif <br>@if($rows->operator_name != "") Operator: {{ $rows->operator_name }} @endif</td>
		                        <td class="text-center">{{ number_format($sched['qty'], 0, '.', ',') }}</td>
		                        <td class="text-center">
		                        	@if($rows->status == "Unassigned" || $rows->status == "Accepted")
		                        	@else {{ date('M-d-Y ,h:i A', strtotime($rows->from_time)) }}
		                        	@endif</td>
		                        <td class="text-center">
		                        	@if($rows->status == "Unassigned" || $rows->status == "Accepted" || $rows->status == "In Progress")
		                        	@else {{ date('M-d-Y ,h:i A', strtotime($rows->to_time )) }}
		                        	@endif</td>

		                        <td class="text-center">
		                        	@php
		                        		$time = $rows->hours * 3600;
		                        		$s = $time%60;
									    $m = floor(($time%3600)/60);
									    $h = floor(($time%86400)/3600);
									    $d = floor(($time%2592000)/86400);
									    $ss = $s > 1 ? "secs":'sec';
									    $mm = $m > 1 ? "mins":'min';
									    $dd = $d > 1 ? "days":'day';
									    $hh = $h > 1 ? "hrs":'hr';
									    
									    if($d == 0 and $h == 0 and $m == 0 and $s == 0) {
									       $format= "$s $ss";
									    }elseif($d == 0 and $h == 0 and $m == 0) {
									       $format= "$s $ss";
									    }elseif($d == 0 and $h == 0) {
									       $format= "$m $mm,$s $ss";
									    }elseif($d == 0) {
									       $format= "$h $hh, $m $mm,$s $ss";
									    }else{
									        $format="$d $dd,$h $hh, $m $mm,$s $ss";
									    }
									    
									   
				                     @endphp
		                        	@if($rows->status == "Unassigned" || $rows->status == "Accepted" || $rows->status == "In Progress")
		                        	@else {{ $format }}
		                        	@endif</td>
		                        <td class="text-center">{{ number_format($rows->good, 0, '.', ',') }}</td>
		                        <td class="text-center">{{ number_format($rows->reject, 0, '.', ',') }}<br>@if($rows->rejection_type != "") Rejection Type: {{ $rows->rejection_type  }} @endif</td>
		                        <td class="text-center">{{ number_format($rows->rework, 0, '.', ',') }}</td>
		                        <td class="text-center"><span class="badge badge-{{$badge_color}}" style="font-size: 9pt; color: #fff;">{{ $rows->status }}</span></td>
		                        <td class="text-center">Quality Inspection Status : {{ $rows->qa_inspection_status}}<br>@if($rows->quality_inspected_by != "") Quality Inspection By: {{ $rows->quality_inspected_by  }} @endif</td>
		                     </tr>
		                     @empty
		                     <tr>
		                        <td colspan="5" class="text-center">No unassigned task(s) found for this process.</td>
		                     </tr>
		                     @endforelse
		                  @endforeach
	                  </tbody>
	               </table>
	           </div>
            </div>
   @endforeach
</div>