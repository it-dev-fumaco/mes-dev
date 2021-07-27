@if($notifications['match'] == "true")
<div class="alert alert-warning text-center" role="alert">
  <span class="d-none"></span>
  <div class="container">
     <div class="alert-icon" style="color:black;">
      <i class="now-ui-icons travel_info" style="padding-right:5px;"></i><span style="font-size:13pt;"> <b>Notification Change Code :</b></span> 
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
								if($item_details['production_order_status'] == "Material For Issue"){
									$badge_color ="danger";
								}else if($item_details['production_order_status'] == "Material Issued"){
									$badge_color ="primary";
								}else if($item_details['production_order_status'] == "Cancelled"){
									$badge_color ="danger";
								}else if($item_details['production_order_status'] == "Ready For Feedback"){
									$badge_color ="info";
								}else if($item_details['production_order_status'] == "Partial Feedbacked"){
									$badge_color ="success";
								}else if($item_details['production_order_status'] == "Feedbacked"){
									$badge_color ="success";
								}else{
									$badge_color ="warning";
								}
							@endphp
							<span class="badge badge-{{$badge_color}}  pull-right" style="margin-top:-50px;text-align: center;font-size:13px;color:white; font-size:18px;">{{ $item_details['production_order_status'] }}</span>
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
									<div style="margin: 5px; display:{{($item_details['planned_start_date'] == null )? 'none':''}};">
										<span style="font-size: 12pt; margin: auto;">Scheduled Date: </span>
										<span class="font-weight-bold" style="font-size: 12pt; margin: auto;">{{ $item_details['planned_start_date'] }}</span>
										<span class="badge badge-{{ ($item_details['status'] == 'Late') ? 'danger' : 'info' }}">{{ $item_details['status'] }}</span>
									</div>
									<div style="margin: 5px; display:{{($item_details['planned_start_date'] == null )? '':'none'}};">
										<span style="font-size: 12pt; margin: auto;color:#dc3545;font-weight:bolder;">Unscheduled</span>
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
										<col style="width: 25%;">
										<thead style="font-size: 10pt;">
											<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PRODUCED QTY</b></td>
											<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>TOTAL GOOD</b></td>
											<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>TOTAL REJECT</b></td>
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
													<span style="font-size: 15pt;">{{ $totals['total_reject'] }}</span>
												</td>
												<td class="text-center" style="border: 1px solid #ABB2B9;">
													<span style="font-size: 15pt;">{{ $totals['balance_qty'] }}</span>
												</td>
											</tr>
										</tbody>
									</table>
								</div>


								<div class="col-md-12">
									<br>
									<div class="table-responsive">
										<table style="width: 100%; border-color: #D5D8DC;">
											<col style="width: 15%;">
											<col style="width: 15%;">
											<col style="width: 12%;">
											<col style="width: 12%;">
											<col style="width: 10%;">
											<col style="width: 12%;">
											<col style="width: 12%;">
											<col style="width: 12%;">
											<thead style="font-size: 10pt;">
												<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>WORKSTATION</b></td>
												<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PROCESS</b></td>
												<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>GOOD</b></td>
												<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>REJECT</b></td>
												<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>MACHINE</b></td>
												<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>START</b></td>
												<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>END</b></td>
												<td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>OPERATOR</b></td>
											</thead>
											<tbody style="font-size: 9pt;">
												@foreach ($operation_list as $b)
												@php
												if($b['workstation'] == "Spotwelding"){
													$spotclass= "spotclass";
													$icon = '<span style="font-size:15px;">&nbsp; >></span>';
												}else{
													$spotclass= "";
													$icon="";
												}
												@endphp
												<tr>
													<td class="text-center" style="border: 1px solid #ABB2B9;" rowspan="{{ $b['count'] }}">
														<span class="{{ $spotclass }}" data-jobticket="{{ $b['job_ticket'] }}" data-prodno="{{ $b['production_order'] }}">
															<b>{{ $b['workstation'] }} {!! $icon !!}</b>
														</span>
														<br><span style="font-size:9px;"><i>{{ $b['cycle_time'] }}</i></span>
													</td>
													@if (count($b['operations']) > 0)
															<td class="text-center" style="border: 1px solid #ABB2B9;" rowspan="{{ $b['count'] }}">
																<span class="{{ $spotclass }}" data-jobticket="{{ $b['job_ticket'] }}" data-prodno="{{ $b['production_order'] }}">
																	<b>{{ $b['process'] }}</b>
																</span>
																<br><span style="font-size:11px;"><b><i>{{ $b['count_good'] }}</i><b></span>
															</td>
														@foreach($b['operations'] as $c)
															@php
																$machine = ($c['machine_code']) ? $c['machine_code'] : '-';
																$operator_name = ($c['operator_name']) ? $c['operator_name'] : '-';
																$from_time = ($c['from_time']) ? $c['from_time'] : '-';
																$to_time = ($c['to_time']) ? $c['to_time'] : '-';
																$inprogress_class = ($c['status'] == 'In Progress') ? 'active-process' : '';
																if($b['process'] == "Housing and Frame Welding"){
																	$qc_status = '';
																}else{
																	$qc_status = ($c['qa_inspection_status'] == 'QC Passed') ? "qc_passed" : "qc_failed";
																	$qc_status = ($c['qa_inspection_status'] == 'Pending') ? '' : $qc_status;
																}
															@endphp
															<td class="text-center {{ $inprogress_class }} {{ $qc_status }}" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>{{ number_format($c['good']) }}</b></td>
															<td class="text-center {{ $inprogress_class }}" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>{{ number_format($c['reject']) }}</b></td>
															<td class="text-center {{ $inprogress_class }}" style="border: 1px solid #ABB2B9;">{{ $machine }}</td>
															<td class="text-center {{ $inprogress_class }}" style="border: 1px solid #ABB2B9;">{{ $from_time }}</td>
															<td class="text-center {{ $inprogress_class }}" style="border: 1px solid #ABB2B9;">{{ $to_time }}</td>
															<td class="text-center {{ $inprogress_class }}" style="border: 1px solid #ABB2B9;">
																<span class="hvrlink-plan">{{ $operator_name }}</span>
																@if($b['workstation'] != "Spotwelding")
																<div class="hover-box text-center">
																	@if (count($c['helpers']) > 0)
																	<label class="font-weight-bold mb-1">HELPER(S)</label>
																	@foreach ($c['helpers'] as $helper)
																	<span class="d-block">{{ $helper }}</span>
																	@endforeach
																	@else
																	<label class="font-weight-bold m-0">NO HELPER(S)</label>
																	@endif
																</div>
																@endif
															</td>
														</tr>
														@endforeach
													@else
														<td class="text-center" style="border: 1px solid #ABB2B9;"><b>{{ $b['process'] }}</b></td>
														<td class="text-center" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>0</b></td>
														<td class="text-center" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>0</b></td>
														<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>
														<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>
														<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>
														<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>
													</tr>
													@endif
												</tr>
											@endforeach
											</tbody>
										</table>
									</div>
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
																<p class="desc" style="padding-top: 5px;">
																<b>Description:</b> {!! $rows['description'] !!}         
																</p>
														</div>
													
													
													</td>
													<td style="border: 1px solid #ABB2B9;padding:5px;">{{ $rows['item_classification']}} </td>
													<td style="border: 1px solid #ABB2B9;padding:5px;">{{ $rows['qty_to_manufacture'] }}</td>
													<td style="border: 1px solid #ABB2B9;padding:5px;">{{ $rows['produced_qty'] }}</td>
													@php
														
														if($rows['material_status'] == "Material For Issue"){
														$stat_badge ="danger";
														}else if($rows['material_status'] == "Material Issued"){
														$stat_badge ="primary";
														}else if($rows['material_status'] == "Cancelled"){
														$stat_badge ="danger";
														}else if($rows['material_status'] == "Ready For Feedback"){
														$stat_badge ="info";
														}else if($rows['material_status'] == "Partial Feedbacked"){
														$stat_badge ="success";
														}else if($rows['material_status'] == "Feedbacked"){
														$stat_badge ="success";
														}else{
														$stat_badge ="warning";
														}
														@endphp
                               
													<td style="border: 1px solid #ABB2B9;padding:5px;"><span class="badge badge-{{$stat_badge}} badge-style" style="text-align: center;font-size:13px;color:white;">
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
</div>
<style type="text/css">
 /** detail panel **/
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






