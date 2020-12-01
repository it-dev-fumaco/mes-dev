<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<div id="table-scroll" class="table-scroll">
					<div class="table-wrap">
					  	<table class="main-table">
							<thead class="text-primary" style="font-size: 10pt; background-color: #D5D8DC;">
						  		<tr>
									<th class="fixed-side text-center" style="min-width: 180px; background-color: #D5D8DC;" scope="col">Workstation</th>
									@foreach ($date_period as $date)
									@php
										$is_today = (date('Y-m-d') == date('Y-m-d', strtotime($date['date']))) ? '[Today]' : '';
									@endphp
									<th class="text-center align-middle" style="background-color: #D5D8DC;" scope="col">
										{{ date('F-d-Y', strtotime($date['date'])) }} {{ $is_today }} [{{ $date['shift_time_in'] }} - {{ $date['shift_time_out'] }}]
										<div class="pull-right"><a href="#">
											<img src="{{ asset('img/print.png') }}" width="25">
										  </a></div>
									</th>
									@endforeach
								</tr>
							</thead>
							<tbody style="font-size: 9pt;">
								@foreach ($task_list as $workstation)
								<tr>
									<td class="fixed-side text-center" style="background: #eee;">{{ $workstation['workstation_name'] }}</td>
									@foreach ($workstation['task_per_day'] as $task)
									<td>
									<ul class="sortable-list" style="width: 800px; height: 50px; margin: 0;" data-schedule-date="{{ $task['schedule_date'] }}" data-workstation="{{ $workstation['workstation_name'] }}">
											@foreach ($task['task_list'] as $row)
											@php
												$css = 'background-color: '.$row->color_legend . ';';
												if($filters['customer'] && $filters['customer'] != $row->customer){
													$css = 'background-color: #ABB2B9; opacity: 0.5';
												}

												if($filters['reference_no'] && $filters['reference_no'] != $row->reference){
													$css = 'background-color: #ABB2B9; opacity: 0.5';
												}

												if($filters['parent_item'] && $filters['parent_item'] != $row->parent_item_code){
													$css = 'background-color: #ABB2B9; opacity: 0.5';
												}

												if($filters['item'] && $filters['item'] != $row->item_code){
													$css = 'background-color: #ABB2B9; opacity: 0.5';
												}
											@endphp
											<li class="sortable-item text-center" id="{{ $row->job_ticket_id }}" data-workstation="{{ $row->workstation }}" data-production-order="{{ $row->production_order }}">
												<div class="hvrlink text-white" style=" {{ $css }}">
													<span class="text-white" style="font-size: 8pt;">{{ $row->item_code }} ({{ $row->qty_to_manufacture }})</span>
													<span style="font-size: 7pt; display: block;">{{ $row->parts_category }}&nbsp;</span>
												</div>
												
												<div class="details-pane">
													<table style="width: 100%; font-size: 9pt;">
														<col style="width: 40%;">
														<col style="width: 60%;">
														<tr>
															<td>{{ $row->production_order }} <b>[{{ $row->status }}]</b></td>
															<td class="text-center"><b>{{ $row->process_name }}</b></td>
														</tr>
														<tr>
															<td colspan="2" style="white-space: normal;"><b>{{ $row->item_code }}</b> - {{ $row->description }}</td>
														</tr>
														<tr>
															<td colspan="2" style="white-space: normal;">Qty to Manufacture: <b>{{ $row->qty_to_manufacture }} {{ $row->stock_uom }}</b></td>
														</tr>
														<tr>
															<td colspan="2" style="white-space: normal;">Customer: <b>{{ $row->customer }} - {{ $row->reference }}</b></td>
														</tr>
														{{-- <tr>
															<td colspan="2" style="white-space: normal;">Status: <b>{{ $row->status }}</b></td>
														</tr> --}}
													</table>
												</div>
											</li>
											@endforeach
										</ul>
									</td>
									@endforeach
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	.table-scroll {
		position:relative;
		width:100%;
		margin:auto;
		overflow:hidden;
	}

	.table-wrap {
		width:100%;
		overflow:auto;
	}
	
	.table-scroll table {
		width:100%;
		margin:auto;
		border-collapse:collapse;
		border-spacing:0;
	}

	.table-scroll th, .table-scroll td {
		padding:5px;
		border:1px solid #ABB2B9;
		/* background:#fff; */
		/* white-space:nowrap; */
		vertical-align:middle;
	}

	.clone {
		position:absolute;
		top:0;
		left:0;
		pointer-events:none;
	}
	.clone th, .clone td {
		visibility:hidden
	}
	.clone td, .clone th {
		border-color:transparent
	}
	.clone tbody th {
		visibility:visible;
		color:red;
	}
	.clone .fixed-side {
		border:1px solid #ABB2B9;
		background:#eee;
		visibility:visible;
	}
	.clone thead, .clone tfoot{background:transparent;}

	.sortable-list {
		list-style-type: none;
		display: block;
		padding: 0;
		height: 100%;
	}
	  
  	.sortable-item {
		float: left;
		position: relative;
		width: 120px;
		margin: 1px;
		padding: 2px 4px;
	}

	.details-pane {
		display: none;
		color: #414141;
		background: #EBEDEF;
		border: 1px solid #a9a9a9;
		position: absolute;
		top: 20px;
		left: 0;
		z-index: 1;
		width: 500px;
		padding: 6px 8px;
		text-align: left;
		-webkit-box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
		-moz-box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
		box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
		white-space: normal;
	}

	div.hvrlink:hover + .details-pane {
		display: block;
	}

	.details-pane:hover {
		display: block;
	}

	div.hvrlink{
		cursor: pointer;
	}
</style>
{{-- https://codepen.io/paulobrien/pen/gWoVzN --}}
<script>
	$( function() {
		$( ".sortable-list" ).sortable({
			connectWith: ".sortable-list",
			receive:function(event, ui) {
				var schedule_date = $(this).data('schedule-date');
				var workstation1 = $(this).data('workstation');
				
				var workstation2 = ui.item.data('workstation');
				var id = ui.item.attr('id');
				var production_order = ui.item.data('production-order');
				
				if (workstation1 != workstation2) {
					ui.sender.sortable("cancel");
					return false;
				}

				$('.main-table').closest('.clone').remove();

				$.ajax({
					url:"/update_task_schedule/" + id,
					type:"POST",
					dataType: "text",
					data: {
						planned_start_date: schedule_date
					},
					success:function(data){
						console.log(data);
						$('.main-table').clone(true).appendTo('#table-scroll').addClass('clone');

						$.ajax({
							url:"/update_production_order_schedule",
							type:"POST",
							dataType: "text",
							data: {
								production_order: production_order
							},
							success:function(data){
								console.log(data);
								$('.main-table').clone(true).appendTo('#table-scroll').addClass('clone');
							},
							error: function(jqXHR, textStatus, errorThrown) {
								console.log(jqXHR);
								console.log(textStatus);
								console.log(errorThrown);
							}
						});	
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log(jqXHR);
						console.log(textStatus);
						console.log(errorThrown);
					}
				});	
				
				
			},
		}).disableSelection();

		jQuery(document).ready(function() {
			jQuery(".main-table").clone(true).appendTo('#table-scroll').addClass('clone');
		});
	});
</script>