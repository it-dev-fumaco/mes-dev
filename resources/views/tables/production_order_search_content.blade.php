@if($notifications['match'] == "true")
<div class="alert alert-warning text-center" role="alert">
	<span class="d-none"></span>
	<div class="container">
		<div class="alert-icon" style="color:black;">
		<i class="now-ui-icons travel_info" style="padding-right:5px;"></i><span style="font-size:13pt;"><b>Notification Change Code :</b></span> 
			<span style="font-size:11pt;">{!! $notifications['message'] !!} </span>
		</div>
	</div>
</div>
@endif
<div class="row tabs-banner" style="margin:2px;">
	<div class="col-md-12">
		<ul class="nav nav-tabs" id="myTabsearchpo" role="tablistsearch">
			<li class="nav-item protab">
				<a class="nav-link active" id="prodserach-tab{{ $production_order_no }}" data-toggle="tab" href="#prod_search_tab{{ $production_order_no }}" role="tab" aria-controls="search_tab" aria-selected="true">{{ $tab_name }}</a>
			</li>
			@foreach($tab as $index => $row)
			<li class="nav-item protab">
				<a class="nav-link" id="tab{{$index}}{{$row['tab']}}" onclick="return false;" data-toggle="tab" href="#tab_{{$index}}{{$row['tab']}}" role="tab" aria-controls="search_tab" aria-selected="true">{{$row['tab']}}</a>
			</li>
			@endforeach                  
		</ul>
		<!-- Tab panes -->
		<div class="tab-content">
         	<div class="tab-pane active" id="prod_search_tab{{ $production_order_no }}" role="tabpanel" aria-labelledby="search_tab">
				<div class="row" style="margin-top: 12px;">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-12">
								@php
									switch ($item_details['production_order_status']) {
										case "Cancelled":
										case "Material For Issue":
											$badge_color ="danger";
											break;
										case "Material Issued":
											$badge_color ="primary";
											break;
										case "Ready for Feedback":
											$badge_color ="info";
											break;
										case "Partially Feedbacked":
										case "Feedbacked":
											$badge_color ="success";
											break;
										case "Closed":
											$badge_color = 'secondary';
											break;
										default:
											$badge_color ="warning";
											break;
									}
								@endphp

								<div class="pull-right p-0" style="margin-top:-60px;">
									<span class="badge badge-{{$badge_color}} m-2 text-center text-white" style="font-size:16px;">{{ $item_details['production_order_status'] }}</span>
									@if (Auth::check())
									@if (!in_array($item_details['production_order_status'], ['Cancelled', 'Partially Feedbacked', 'Feedbacked', 'Closed']))
									<i class="now-ui-icons arrows-1_refresh-69" id="sync-job-ticket-btn" style="font-size:25px;font-weight:bolder;color:black; vertical-align: middle; cursor: pointer;" data-production-order="{{ $production_order_no }}"></i>
									@endif
									@endif
								</div>
								<div class="container">
									<div class="row">
										<ul class="breadcrumb-c">
											@foreach($process as $a)
											<li class="{{ $a['status'] }}">
												<a href="#">{{ $a['workstation'] }}</a>
											</li>
											@endforeach
										</ul>
									</div>
								</div>
							</div>
							<div class="col-md-12">
								@php 
								@endphp
								<div class="row">
									@if ($item_details['planned_start_date'])
										<div class="col-6 p-1">
											<span style="font-size: 12pt; margin: auto;">Scheduled Date: </span>
											<span class="font-weight-bold" style="font-size: 12pt; margin: auto;">{{ $item_details['planned_start_date'] }}</span>
											<span class="badge badge-{{ ($item_details['status'] == 'Late') ? 'danger' : 'info' }}">{{ $item_details['status'] }}</span>
										</div>
									@else
										<div class="col-6 p-1">
											<span style="font-size: 12pt; margin: auto;color:#dc3545;font-weight:bolder;">Unscheduled</span>
										</div>
									@endif
									@if ($item_details['qty_to_manufacture'] != $qty_to_manufacture)
										<div class="col-6">
											<div class="row">
												<div class="col-6 p-1">
													Planned Qty: <span class="font-weight-bold" style="font-size: 12pt;">{{ $total_planned_qty }}</span>
												</div>
												<div class="col-6 p-1">
													Unplanned Qty: <span class="font-weight-bold" style="font-size: 12pt;">{{ $qty_to_manufacture > $total_planned_qty ? $qty_to_manufacture - $total_planned_qty : 0 }}</span>
												</div>
											</div>
										</div>
									@endif
								</div>
								
								<table style="width: 100%; border-color: #D5D8DC;">
									<col style="width: 18%;">
									<col style="width: 24%;">
									<col style="width: 23%;">
									<col style="width: 20%;">
									<col style="width: 15%;">
									<tr style="font-size: 9pt;">
										<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>REFERENCE NO.</b></td>
										<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>CUSTOMER</b></td>
										<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PROJECT</b></td>
										<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>DELIVERY DATE</b></td>
										<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>QTY</b></td>
									</tr>
									<tr style="font-size: 10pt;">
										<td class="text-center" style="border: 1px solid #ABB2B9;">{{ $item_details['sales_order'] }} {{ $item_details['material_request'] }}</td>
										<td class="text-center" style="border: 1px solid #ABB2B9;">{{ $item_details['customer'] }}</td>
										<td class="text-center" style="border: 1px solid #ABB2B9;">{{ $item_details['project'] }}</td>
										<td class="text-center" style="border: 1px solid #ABB2B9;">{{ $item_details['delivery_date'] }}</td>
										<td class="text-center" style="border: 1px solid #ABB2B9; font-size: 15pt;">{{ $item_details['qty_to_manufacture'] }}</td>
									</tr>
									<tr style="font-size: 10pt;">
										<td style="border: 1px solid #ABB2B9; font-size: 9pt;" class="text-center"><b>ITEM DETAIL(S):</b></td>
										<td style="border: 1px solid #ABB2B9;" colspan="4"><span class="font-weight-bold">{{ $item_details['item_code'] }}</span> - <span>{{ $item_details['description'] }}</span></td>
									</tr>
								</table>
							</div>
							<div class="col-md-12">
								<br>
								<table style="width: 100%; border-color: #D5D8DC;">
									<col style="width: 25%;">
									<col style="width: 25%;">
									<col style="width: 25%;">
									<thead style="font-size: 10pt;">
										<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PRODUCED QTY</b></td>
										<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>TOTAL GOOD</b></td>
										<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>BALANCE QTY</b></td>
									</thead>
									<tbody style="font-size: 9pt;">
										<tr>
											<td class="text-center" style="border: 1px solid #ABB2B9;">
												<span style="font-size: 15pt;">{{ $totals['produced_qty'] }}</span>
											</td>
											<td class="text-center" style="border: 1px solid #ABB2B9;">
												<span style="font-size: 15pt;">{{ $totals['total_good'] }}</span>
											</td>
											<td class="text-center" style="border: 1px solid #ABB2B9;">
												<span style="font-size: 15pt;">{{ $totals['balance_qty'] }}</span>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="col-md-12">
								<ul class="nav nav-tabs mt-2 font-weight-bold" role="tablist" id="s-tab-1">
									<li class="nav-item">
										<a class="custom-nav-link show active" data-toggle="tab" href="#jt-tab-1" role="tab" aria-controls="home" aria-selected="true">Operator Log(s)</a>
									</li>
									@if (count($operation_reject_logs) > 0)
									<li class="nav-item">
										<a class="custom-nav-link show" data-toggle="tab" href="#jt-tab-2" role="tab" aria-controls="messages" aria-selected="false">Reject(s) <span class="badge badge-info mr-2">{{ collect($operation_reject_logs)->sum('rows') }}</span>
										</a>
									</li>
									@endif
								</ul>
								<div class="tab-content mt-1">
									<div class="tab-pane show active mt-2" id="jt-tab-1" role="tabpanel" aria-labelledby="w1-tab">
										<div class="table-responsive pt-1">
											<table style="width: 100%; border-color: #D5D8DC; margin: 0;">
												<col style="width: 11%;"><!-- WORKSTATION -->
												<col style="width: 14%;"><!-- PROCESS -->
												<col style="width: 7%;"><!-- GOOD -->
												<col style="width: 7%;"><!-- REWORK -->
												<col style="width: 10%;"><!-- MACHINE -->
												<col style="width: 10%;"><!-- START -->
												<col style="width: 10%;"><!-- END -->
												<col style="width: 10%;"><!-- DURATION -->
												<col style="width: 8%;"><!-- OPERATOR -->
												@if ($item_details['feedback_qty'] <= 0)
												<col style="width: 6%;"><!-- ACTION -->
												@endif
												<thead style="font-size: 10pt;">
													<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>WORKSTATION</b></td>
													<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PROCESS</b></td>
													<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>GOOD</b></td>
													<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>FOR REWORK</b></td>
													<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>MACHINE</b></td>
													<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>START</b></td>
													<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>END</b></td>
													<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>DURATION</b></td>
													<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>OPERATOR</b></td>
													@if ($item_details['feedback_qty'] < $item_details['qty_to_manufacture'])
													<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>ACTION</b></td>
													@endif
												</thead>
												<tbody style="font-size: 9pt;">
													@foreach ($operation_list as $workstation => $processes)
													@php
														$rowspan = count($processes) < collect($operation_list[$workstation])->sum('count') ? collect($operation_list[$workstation])->sum('count') : count($processes);
														if($workstation == "Spotwelding"){
															$spotclass = "spotclass";
															$icon = '<span style="font-size:15px;">&nbsp; >></span>';
															$jt = $operation_list['Spotwelding'][0]['job_ticket'];
														}else{
															$spotclass = $icon = $jt = "";
														}
		
														$is_painting = false;
													@endphp
													<tr>
														<td class="text-center" style="border: 1px solid #ABB2B9;" rowspan="{{ ($rowspan) }}">
															<span class="{{ $spotclass }} font-weight-bold" data-jobticket="{{ $jt }}" data-prodno="{{ $production_order_no }}">{{ $workstation }} {!! $icon !!}</span>
														</td>
														@foreach ($processes as $process)
														<td class="text-center" style="border: 1px solid #ABB2B9;" rowspan="{{ $process['count'] }}">
															<span class="{{ $spotclass }} font-weight-bold" data-jobticket="{{ $process['job_ticket'] }}" data-prodno="{{ $process['production_order'] }}">
																{{ $process['process'] }}
															</span>
															<span class="d-block font-weight-bold font-italic" style="font-size: 11px;">{{ $process['count_good'] }}</span>
															@if ($process['cycle_time'] && $process['cycle_time'] != '-')
															<span class="d-block font-italic" style="font-size: 9px;">{{ $process['cycle_time'] }}</span>
															@endif
														</td>
														@if (count($process['operations']) > 0 )
														@foreach($process['operations'] as $a)
														@php
															$machine = ($a['machine_code']) ? $a['machine_code'] : '-';
															$operator_name = ($a['operator_name']) ? $a['operator_name'] : '-';
															$from_time = ($a['from_time']) ? Carbon\Carbon::parse($a['from_time'])->format('M-d-Y h:i A') : '-';
															$to_time = ($a['to_time']) ? Carbon\Carbon::parse($a['to_time'])->format('M-d-Y h:i A') : '-';
															$inprogress_class = ($a['status'] == 'In Progress') ? 'active-process' : '';
															$qc_status = null;
															
															if($process['process'] != "Housing and Frame Welding"){
																$qc_status = ($a['qa_inspection_status'] == 'QC Passed') ? "qc_passed" : "qc_failed";
																$qc_status = ($a['qa_inspection_status'] == 'Pending') ? '' : $qc_status;
															}
														@endphp
														<td class="text-center {{ $inprogress_class }} {{ $qc_status }}" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>{{ number_format($a['good']) }}</b></td>
														<td class="text-center {{ $inprogress_class }}" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>{{ $loop->last ? number_format($a['rework']) : 0 }}</b></td>
														<td class="text-center {{ $inprogress_class }}" style="border: 1px solid #ABB2B9;">{{ $machine }}</td>
														<td class="text-center {{ $inprogress_class }} {{ $process['process'] == 'Unloading' ? 'd-none' : null }}" style="border: 1px solid #ABB2B9;" colspan={{ $process['workstation'] == 'Painting' ? 2 : 1 }}>{{ $from_time }}</td>
														<td class="text-center {{ $inprogress_class }} {{ $process['process'] == 'Loading' ? 'd-none' : null }}" style="border: 1px solid #ABB2B9;" colspan={{ $process['workstation'] == 'Painting' ? 2 : 1 }}>{{ $to_time }}</td>
		
														@if ($workstation == 'Painting' && !$is_painting)
														<td class="text-center {{ $inprogress_class }}" style="border: 1px solid #ABB2B9;" rowspan="{{ ($rowspan) }}">{{ $painting_duration }}</td>
														@php
															$is_painting = true;
														@endphp
														@else
														<td class="text-center {{ $inprogress_class }} {{ $workstation == 'Painting' ? 'd-none' : '' }}" style="border: 1px solid #ABB2B9;">{{ isset($a['total_duration']) ? $a['total_duration'] : '-' }}</td>
														@endif
														<td class="text-center {{ $inprogress_class }}" style="border: 1px solid #ABB2B9;">
															<span class="hvrlink-plan">{{ $operator_name }}</span>
															@if($process['workstation'] != "Spotwelding")
															<div class="hover-box text-center">
																@if (count($a['helpers']) > 0)
																<label class="font-weight-bold mb-1">HELPER(S)</label>
																@foreach ($a['helpers'] as $helper)
																<span class="d-block">{{ $helper }}</span>
																@endforeach
																@else
																<label class="font-weight-bold m-0">NO HELPER(S)</label>
																@endif
															</div>
															@endif
														</td>
														@if ($item_details['feedback_qty'] < $item_details['qty_to_manufacture'])
														<td class="text-center {{ $inprogress_class }}" style="border: 1px solid #ABB2B9;">
															@if ($process['workstation'] != 'Spotwelding')
															<span class="d-none">{{ $process['production_order'] }}</span>
															<span class="d-none">{{ $process['workstation'] }}</span>
															<span class="d-none">{{ $process['process'] }}</span>
															<span class="d-none">{{ $from_time }}</span>
															<span class="d-none">{{ $to_time }}</span>
															<span class="d-none">{{ $operator_name }}</span>
															<span class="d-none">{{ $a['good'] }}</span>
															@if (in_array($details->status, ['Cancelled', 'Closed']))
																<img src="{{ asset('/img/edit-new-icon.png') }}" class="d-inline-block m-1" width="20" style="cursor: not-allowed;">
																<img src="{{ asset('/img/reset.png') }}" class="d-inline-block m-1" width="20" style="cursor: not-allowed;">
															@else
																<img src="{{ asset('/img/edit-new-icon.png') }}" class="edit-time-log-btn d-inline-block m-1" width="20" style="cursor: pointer;" data-jobticket="{{ $process['job_ticket'] }}" data-timelog="{{ $a['timelog_id'] }}">
																<img src="{{ asset('/img/reset.png') }}" class="reset-time-log-btn d-inline-block m-1" width="20" style="cursor: pointer;" data-jobticket="{{ $process['job_ticket'] }}" data-timelog="{{ $a['timelog_id'] }}">
															@endif
															@else
															<img src="{{ asset('/img/edit-new-icon.png') }}" width="20" style="cursor: not-allowed;">
															@endif
														</td>
														@endif
													</tr>
													@endforeach
													@else
														<td class="text-center" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>0</b></td>
														<td class="text-center" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>0</b></td>
														@if ($process['workstation'] != 'Painting')
														<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>
														@endif
														<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>
														<td class="text-center" style="border: 1px solid #ABB2B9;" colspan={{ $process['workstation'] == 'Painting' ? 2 : 1 }}>-</td>
														@if ($process['workstation'] != 'Painting')
														<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>
														@endif
														<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>
														@if ($item_details['feedback_qty'] <= 0)
														<td class="text-center" style="border: 1px solid #ABB2B9;">
															<img src="{{ asset('/img/edit-new-icon.png') }}" width="20" style="cursor: not-allowed;">
														</td>
														@endif
													</tr>
													@endif
													@endforeach
													@endforeach
												</tbody>
											</table>
										</div>
									</div>
									<div class="tab-pane" id="jt-tab-2" role="tabpanel" aria-labelledby="w2-tab">
										<div class="table-responsive pt-1">
											<table style="width: 100%; border-color: #D5D8DC; margin: 0;">
												<col style="width: 11%;"><!-- WORKSTATION -->
												<col style="width: 14%;"><!-- PROCESS -->
												<col style="width: 7%;"><!-- REJECT -->
												<col style="width: 10%;"><!-- MACHINE -->
												<col style="width: 10%;"><!-- START -->
												<col style="width: 10%;"><!-- END -->
												<col style="width: 10%;"><!-- DURATION -->
												<thead style="font-size: 10pt;">
													<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>WORKSTATION</b></td>
													<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PROCESS</b></td>
													<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>REJECT</b></td>
													<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>REJECT REASON</b></td>
													<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>QC INSPECTION TYPE</b></td>
													<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>QC STATUS</b></td>
													<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>INSPECTED BY</b></td>
												</thead>
												<tbody style="font-size: 9pt;">
													@foreach ($operation_list as $workstation => $processes)
													@php
														$reject_logs = array_key_exists($workstation, $operation_reject_logs) ? $operation_reject_logs[$workstation] : [];
														$rowspan = isset($reject_logs['rows']) ? $reject_logs['rows'] : '';	
														if($workstation == "Spotwelding"){
															$spotclass = "spotclass";
															$icon = '<span style="font-size:15px;">&nbsp; >></span>';
															$jt = $operation_list['Spotwelding'][0]['job_ticket'];
														}else{
															$spotclass = $icon = $jt = "";
														}
		
														$is_painting = false;
													@endphp
													<tr>
														@if ($rowspan > 0)
														<td class="text-center" style="border: 1px solid #ABB2B9;" rowspan="{{ $rowspan }}">
															<span class="{{ $spotclass }} font-weight-bold" data-jobticket="{{ $jt }}" data-prodno="{{ $production_order_no }}">{{ $workstation }} {!! $icon !!}</span>
														</td>
														@endif
														@foreach ($processes as $process)
														@php
															$reject_logs_arr = array_key_exists($process['process_id'], $reject_logs) ? $reject_logs[$process['process_id']] : [];
														@endphp
														@if (count($reject_logs_arr) > 0)
														<td class="text-center" style="border: 1px solid #ABB2B9;" rowspan="{{ count($reject_logs_arr) > 0 ? count($reject_logs_arr) : '' }}">
															<span class="{{ $spotclass }} font-weight-bold" data-jobticket="{{ $process['job_ticket'] }}" data-prodno="{{ $process['production_order'] }}">{{ $process['process'] }}</span>
														</td>
														@foreach($reject_logs_arr as $a)
														<td class="text-center" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>{{ number_format($a['reject_qty']) }}</b></td>
														<td class="text-center" style="border: 1px solid #ABB2B9;"><b>{{ $a['reject_reason'] }}</b></td>
														<td class="text-center" style="border: 1px solid #ABB2B9;"><b>{{ $a['qa_inspection_type'] }}</b></td>
														<td class="text-center" style="border: 1px solid #ABB2B9;">
															@if ($a['qa_status'] == 'For Confirmation')
															<span class="badge badge-warning font-weight-bold" style="font-size: 11px;">{{ $a['qa_status'] }}</span>
															@elseif ($a['qa_status'] == 'QC Passed')
															<span class="badge badge-success font-weight-bold" style="font-size: 11px;">{{ $a['qa_status'] }}</span>
															@else
															<span class="badge badge-danger font-weight-bold" style="font-size: 11px;">{{ $a['qa_status'] }}</span>
															@endif
														</td>
														<td class="text-center" style="border: 1px solid #ABB2B9;"><b>{{ array_key_exists($a['qa_staff_id'], $qa_staff_names) ? $qa_staff_names[$a['qa_staff_id']] : '-' }}</b></td>
													</tr>
													@endforeach
													@endif
													@endforeach
													@endforeach
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<style>
									#s-tab-1 .custom-nav-link {
										padding: 10px 20px;
										color: #2c3e50;
										text-decoration: none;
									}
									#s-tab-1 {
										border-bottom: 3px solid #ebedef;
										padding: 10px 0 10px 0;
									}
									#s-tab-1 .nav-item .active {
										color: #f96332;
										font-weight: bolder;
										border-bottom: 3px solid #f96332;
									}
								</style>
								<br>
								<div class="pull-right font-italic" style="font-size: 9pt;"><b>Created by:</b> {{ $item_details['owner'] }} - {{ $item_details['created_at'] }}</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<br>
			@foreach($tab as $index => $row)            
			<div class="tab-pane" id="tab_{{$index}}{{$row['tab']}}" role="tabpanel" aria-labelledby="search_tab">
				<div class="row">
					<div class="col-md-12">
						<div class="row" style="margin: 0 8px;">
							<div class="col-md-12">
								<br>
								<div class="table-responsive">
									<table style="width: 100%; border-color: #D5D8DC;">
										<col style="width: 15%;">
										<col style="width: 12%;">
										<col style="width: 12%;">
										<col style="width: 12%;">
										<col style="width: 12%;">
										<col style="width: 12%;">
										<col style="width: 11%;">
										<col style="width: 11%;">
										<thead style="font-size: 10pt;">
											<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PRODUCTION ORDER</b></td>
											<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>ITEM</b></td>
											<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PARTS CATEGORY</b></td>
											<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>ORDERED QTY</b></td>
											<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>COMPLETED QTY</b></td>
											<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>STATUS</b></td>
											<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>START DATE</b></td>
											<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>END DATE</b></td>
										</thead>
										<tbody class="text-center" style="font-size: 9.5pt;">
											@forelse($row['data'] as $rows)
											<tr>
												<td style="border: 1px solid #ABB2B9;padding:5px;" data-jtno="{{ $rows['production_order']}}" class="production_order_link"><b>{{ $rows['production_order'] }}</b></td>
												<td style="border: 1px solid #ABB2B9;padding:5px;">
													<span class="hvrlinks" style="margin-bottom: 30px;color:black;">
														<a href="#"><span style="font-size: 9pt;color:black;">{{  $rows['item_code']  }}</a>
													</span>
													<div class="details-panes">
														<h5 class="title">{{ $rows['item_code'] }}</h5>
														<p class="desc" style="padding-top: 5px;"><b>Description:</b> {!! $rows['description'] !!}</p>
													</div>											
												</td>
												<td style="border: 1px solid #ABB2B9;padding:5px;">{{ $rows['item_classification']}} </td>
												<td style="border: 1px solid #ABB2B9;padding:5px;">{{ $rows['qty_to_manufacture'] }}</td>
												<td style="border: 1px solid #ABB2B9;padding:5px;">{{ $rows['produced_qty'] }}</td>
												@php
													switch ($rows['material_status']) {
														case "Cancelled":
														case "Material For Issue":
															$stat_badge ="danger";
															break;
														case "Material Issued":
															$stat_badge ="primary";
															break;
														case "Ready For Feedback":
															$stat_badge ="info";
															break;
														case "Partially Feedbacked":
														case "Feedbacked":
															$stat_badge ="success";
															break;
														default:
															$stat_badge ="warning";
															break;
													}
												@endphp
												<td style="border: 1px solid #ABB2B9;padding:5px;">
													<span class="badge badge-{{$stat_badge}} badge-style" style="text-align: center;font-size:13px;color:white;">
														<b>{{ $rows['material_status'] }}</b>
													</span>
												</td>
												<td style="border: 1px solid #ABB2B9;padding:5px;font-style:{{ ($rows['status'] == 'Not Started')? 'italic':'normal'}};">{{ ($rows['planned_start_date'] == "-")? '-' : ($rows['status'] == "Not Started")? $rows['planned_start_date']: $rows['actual_start_date'] }}</td>
												<td style="border: 1px solid #ABB2B9;padding:5px;">{{ ($rows['status'] == "In Progress")? "-": $rows['actual_end_date'] }}</td>
											</tr>
											@empty
											<tr>
												<td colspan="6">No Record/s Found</td>
											</tr>
											@endforelse
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			@endforeach
      	</div>
	</div>
	@if (count($activity_logs) > 0)
		<div class="col-12 p-0">
			<p>Activity Logs</p>
			<ul>
				@foreach ($activity_logs as $log)
					<li>
						<span style="font-size: 9pt;" class="text-muted">{{ Carbon\Carbon::parse($log->created_at)->format('M d, Y h:i:s a') }}</span><br>
						<span style="font-size: 9pt;"><b>{{ $log->action }}</b></span><br>
						<span style="font-size: 9pt;">{{ $log->message ? explode(' at ', $log->message)[0] : null }}</span><br><br>
					</li>
				@endforeach
			</ul>
		</div>
	@endif
</div>
<style type="text/css">
	.details-panes {
		display: none;
		color: #414141;
		background: #f1f1f1;
		border: 1px solid #a9a9a9;
		z-index: 1;
		width: 300px;
		padding: 6px 8px;
		text-align: left;
		-webkit-box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
		-moz-box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
		box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
		white-space: normal;
		position: absolute;
		margin: auto;
	}
	.details-panes h5 {
		font-size: 1.5em;
		line-height: 1.1em;
		margin-bottom: 4px;
		line-height: 8px;
	}
	.details-panes h5 span {
		font-size: 0.75em;
		font-style: italic;
		color: #555;
		padding-left: 15px;
		line-height: 8px;
	}
	.details-panes .desc {
		font-size: 1.0em;
		margin-bottom: 6px;
		line-height: 16px;
	}
  	/** hover styles **/
	span.hvrlinks:hover + .details-panes {
		display: block;
	}
	.details-panes:hover {
		display: block;
	}
	.hover-box {
		display: none;
		color: #414141;
		background: #f1f1f1;
		border: 1px solid #a9a9a9;
		position: absolute;
		right: 5px;
		z-index: 9999999;
		width: 220px;
		padding: 5px;
		-webkit-box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
		-moz-box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
		box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
		white-space: normal;
	}
	.hover-box span{
		padding: 0.2%;
	}
	/** hover styles **/
	span.hvrlink-plan:hover + .hover-box {
		display: block;
	}
	.hover-box:hover {
		display: block;
	}
</style>