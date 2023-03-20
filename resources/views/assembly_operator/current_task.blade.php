@php
	$has_wip = count($time_logs) > 0 ? true : false;
@endphp
<div class="row m-2">
   <div class="col-md-12">
      <div class="row" style="margin-right: -45px;">
			<div class="col-md-4 offset-md-4 text-center" style="margin-top: -77px;">
				@foreach($wip_operators as $r)
				<span class="operator-badge view-in-progress-operator" data-operator-id="{{ $r->operator_id }}">
					<span class="notify-badge">{{ $r->completed_qty }}</span>
					<img src="{{ asset('img/user.png') }}" width="40" height="40">
					<span class="d-block" style="font-size: 7pt;">{{ ($r->operator_nickname) ? $r->operator_nickname : '-' }}</span>
				</span>
				@endforeach
			</div>
         <div class="col-md-4" style="margin-top: -77px;">
            @if($production_order_details->status != 'Completed')
            <div class="pull-right">
              	<table>
						<tr>
							@foreach($helpers as $helper)
							<td style="width: 50px;" class="text-center p-0">
								<img src="{{ asset('img/user.png') }}" width="40" height="40" style="margin: 0 auto;" class="view-helpers-btn p-0" data-timelog-id="{{ $helper->time_log_id }}" data-display-all="0">
								<span class="d-block" style="font-size: 7pt;">{{ $helper->operator_nickname }}</span>
							</td>
							@endforeach
							<td class="align-top" style=" width: 50px;">
								<img src="{{ asset('img/add-user.png') }}" width="80" height="55" style="margin: 8px auto;" class="add-helper-btn">
								<span class="d-block" style="font-size: 7pt;">&nbsp;</span>
							</td>
						</tr>
					</table>
            </div>
            @endif
				@if($production_order_details->status == 'Completed')
				<div class="pull-right" style="margin-top: 8px;">
					<button class="btn btn-secondary view-helpers-btn">View Helpers ({{ count($helpers) }})</button>
				</div>
				@endif
         </div>
      </div>
      {{-- @if($task_arr['qa_inspection_status'] != 'Pending')
      <div class="row">
          <div class="col-md-12">
              <h6 class="text-center"><span class="badge badge-primary" style="font-size: 12pt;">QA Inspected</span></h6>
          </div>
      </div>
      @endif --}}
      <div class="row">
			<table class="w-50 pull-left">
				<tr>
					<td class="text-justify w-100 p-2">
						<span class="d-block" style="font-size: 9pt; color:#707B7C;">ITEM DETAILS</span>
						<span class="d-block font-weight-bold" style="font-size: 10pt;">{{ $production_order_details->item_code }}</span>
						<span class="d-block" style="font-size: 9pt;">{!! $production_order_details->description !!}</span>
					</td>
				</tr>
			</table>
			<table class="w-50 pull-left">
				<tr>
					<td style="width: 10%; border: 1px solid #ABB2B9;" class="align-middle text-center">
						<span class="d-block" style="font-size: 9pt; color: #707B7C;">QTY</span>
						<span class="d-block font-weight-bold" style="font-size: 17pt;">{{ number_format($production_order_details->qty_to_manufacture) }}</span>
						<span class="d-block" style="font-size: 9pt;">{{ $production_order_details->stock_uom }}</span>
					</td>
					<td style="width: 10%; border: 1px solid #ABB2B9;" class="align-middle text-center">
						<span class="d-block" style="font-size: 9pt; color: #707B7C;">COMPLETED QTY</span>
						<span class="d-block font-weight-bold" style="font-size: 17pt;">{{ number_format($production_order_details->produced_qty) }}</span>
						<span class="d-block" style="font-size: 9pt;">{{ $production_order_details->stock_uom }}</span>
					</td>
					<td style="width: 10%; border: 1px solid #ABB2B9;" class="align-middle text-center">
						<span class="d-block" style="font-size: 9pt; color: #707B7C;">FEEDBACKED QTY</span>
						<span class="d-block font-weight-bold" style="font-size: 17pt;">{{ number_format($production_order_details->feedback_qty) }}</span>
						<span class="d-block" style="font-size: 9pt;">{{ $production_order_details->stock_uom }}</span>
					</td>
				</tr>
			</table>
      </div>
      <div class="row p-0">
         <div class="col-md-8 p-0" id="select-part-div">
            <h5 class="title text-center" style="margin: 0 0 8px 0; font-size: 12pt;">Select Process</h5>
				<div class="custom-container">
					@foreach($job_ticket as $jt)
					@php
						$status = $jt->status;
						if($status == 'Completed'){
							$selected_status = 'completed';
							$selected = 'selected-box';
						}

						$selected = $selected_status = '';
						if (in_array($jt->job_ticket_id, $wip_jobtickets)) {
							$selected_status = 'in-progress';
							$selected = 'selected-box';
						}
					@endphp
					<div class="custom-box part-items {{ $selected }} {{ $selected_status }}" data-jobticketid="{{ $jt->job_ticket_id }}">
						<div class="d-block">
							<span class="d-block font-weight-bold part-item-code">{{ $jt->workstation }}</span>
							<span style="font-size: 10pt;">[{{ $jt->process_name }}]</span>
						</div>
						<small class="d-block">Completed Qty: {{ number_format($jt->good) }}</small>
					</div>
					@endforeach
					<p class="clear"></p>
				</div>
         </div>
          <div class="col-md-4">
              <center>
                  <button type="button" class="btn btn-block btn-danger start-task-btn {{ $has_wip ? 'd-none' : '' }}" style="width: 190px; height: 190px;">
                      <i class="now-ui-icons media-1_button-play" style="font-size: 30pt; padding: 3px;"></i>
                      <span class="d-block p-2">Start Work</span>
                  </button>
                  <button type="button" class="btn btn-block btn-warning end-task-btn {{ !$has_wip ? 'd-none' : '' }}" style="width: 190px; height: 190px;">
                      <div class="waves-effect waves z-depth-4">
                          <div class="spinner-grow" style="width: 4rem; height: 4rem;">
                              <span class="sr-only">Loading...</span>
                          </div>
                          <h4 class="text-center blinking font-weight-bold text-dark">In Progress</h4>
                      </div>
                  </button>
                  <button type="button" class="btn btn-block btn-success completed-btn {{ !in_array($production_order_details->status, ['Completed']) ? 'd-none' : '' }}" style="width: 190px; height: 190px;">
                      <i class="now-ui-icons ui-1_check" style="font-size: 30pt; padding: 3px;"></i>
                      <span class="d-block p-2">Completed</span>
                  </button>
              </center>
          </div>
          <div class="col-md-12 p-0 mt-2" style="font-size: 8pt;">
              <table class="table table-bordered">
                  {{-- <col style="width: 30%;">
                  <col style="width: 15%;">
                  <col style="width: 15%;">
                  <col style="width: 30%;">
                  <col style="width: 10%;"> --}}
                  <tbody>
                      <tr>
								 <th class="text-center">PROCESS</th>
								 <th class="text-center">START</th>
								 <th class="text-center">END</th>
                          <th class="text-center">GOOD</th>
								  <th class="text-center">REJECT</th>
								  <th class="text-center">MACHINE</th>
                          <th class="text-center">OPERATOR</th>
                          <th class="text-center">ACTIONS</th>
                      </tr>
                      @forelse($job_ticket_logs as $log)
                      <tr style="font-size: 10pt;" class="{{ ($log->status == 'In Progress') ? 'blink-bg' : '' }}">
								<td class="text-center">{{ $log->workstation }}</td>
                          <td class="text-center">{{ $log->from_time }}</td>
								  <td class="text-center">{{ $log->to_time }}</td>
                          <td class="text-center">{{ $log->good }}</td>
								  <td class="text-center">{{ $log->reject }}</td>
								  <td class="text-center">{{ $log->machine_code }}</td>
								  <td class="text-center">{{ $log->operator_name }}</td>
                          {{-- <td class="text-center">{{ $log['completed_qty'] }}</td>
                          <td class="text-center">{{ $log['operator_name'] }}</td> --}}
                          <td class="text-center p-1">
                              {{-- <button type="button" class="btn btn-block continue-log-btn rounded-0" data-timelog-id="{{ $log['time_log_id'] }}" style="height: 60px; background-color: #117A65;" {{ ($row['status'] == 'In Progress' || $log['status'] == 'In Progress') ? 'disabled' : '' }} {{ $continue_btn }}>
                                  <i class="now-ui-icons media-1_button-play" style="font-size: 13pt;"></i>
                                  <span class="d-block" style="font-size: 8pt;">Continue</span>
                              </button> --}}
                          </td>
                      </tr>
                      @empty
                      <tr>
                          <td class="text-center" colspan="7">No Record(s) Found.</td>
                      </tr>
                      @endforelse
                  </tbody>
              </table>
          </div>
      </div>
      <div class="row">
          <div class="col-md-12">
              <table class="w-100">
                  <tr>
                      <td class="text-center" style="width: 20%;">
                          {{-- @php
                              $disabled_restart = ($task_arr['operator_id'] != Auth::user()->user_id) ? 'disabled' : '';
                          @endphp --}}
                          <button type="button" class="btn btn-block restart-task-btn rounded-0" style="height: 70px; background-color: #00838f;">
                              <i class="now-ui-icons loader_refresh" style="font-size: 15pt;"></i>
                              <span class="d-block" style="font-size: 10pt;">Reset</span>
                          </button>
                      </td>
                      <td class="text-center" style="width: 20%;">
                          {{-- @php
                              $disabled_qc = ($task_arr['qa_inspection_status'] != 'Pending' || $task_status == 'Pending') ? 'disabled' : '';
                          @endphp --}}
                          <button type="button" class="btn btn-block quality-inspection-btn-op rounded-0" style="height: 70px; background-color: #f57f17;">
                              <i class="now-ui-icons ui-1_check" style="font-size: 15pt;"></i>
                              <span class="d-block" style="font-size: 10pt;">Quality Check</span>
                          </button>
                      </td>
                      <td class="text-center" style="width: 20%;">
                          <button type="button" class="btn btn-block machine-breakdown-modal-btn rounded-0 p-0" style="height: 70px; background-color: #6a1b9a;">
                              <i class="now-ui-icons ui-2_settings-90" style="font-size: 15pt;"></i>
                              <span class="d-block" style="font-size: 9pt;">Maintenance Request</span>
                          </button>
                      </td>
                      <td class="text-center" style="width: 20%;">
                          {{-- @php
                              $disabled_enter_reject = ($task_arr['qa_inspection_status'] != 'Pending' || $task_status == 'In Progress') ? 'disabled' : '';
                          @endphp --}}
                          <button type="button" class="btn btn-block enter-reject-btn rounded-0" style="height: 70px; background-color: #C62828;">
                              <i class="now-ui-icons ui-1_simple-remove" style="font-size: 15pt;"></i>
                              <span class="d-block" style="font-size: 10pt;">Enter Reject</span>
                          </button>
                      </td>
                      <td class="text-center" style="width: 20%;">
                          <button type="button" class="btn btn-block rounded-0 reload-btn" style="height: 70px; background-color:#566573;">
                              <i class="now-ui-icons loader_refresh" style="font-size: 15pt;"></i>
                              <span class="d-block" style="font-size: 10pt;">Refresh</span>
                          </button>
                      </td>
                  </tr>
              </table>
          </div>
      </div>
    </div>
</div>


<script>
    $( function() {
      $('.part-items').click(function(){
        if ($('#jt-status').text() != 'In Progress') {
              if ($(this).hasClass('selected-box')) {
              $('#'+ $(this).data('tabid') + ' #select-part-div .selected-box').each(function(){
                  $(this).toggleClass('selected-part');
              });
              }else{
                  $(this).toggleClass('selected-part');
              }
          }

          if ($('#jt-status').text() == 'Completed') {
              var count_selected_part = $('#spotwelding-task-list-row .selected-part').length;
              if(count_selected_part > 1){
                  $('#spotwelding-task-list-row').find('.start-task-btn').eq(0).removeClass('d-none');
                  $('#spotwelding-task-list-row').find('.completed-btn').eq(0).addClass('d-none');
              }else{
                  $('#spotwelding-task-list-row').find('.start-task-btn').eq(0).addClass('d-none');
                  $('#spotwelding-task-list-row').find('.completed-btn').eq(0).removeClass('d-none');
              }
          }
      });
    });
</script>

<style type="text/css">
	.custom-container {
		width: 100%;
		float: left;
	}
	.custom-container:nth-of-type(2),
	.custom-container:nth-of-type(5) {
		margin: 1em 2%;
	}
	.custom-container .custom-box {
		display: block;
		width: 24%;
		margin: 0.5%;
		padding: 0.5em 0;
		text-align: center;
		float: left;
		border: 2px solid  #566573 ;
		height: 100px;
	}
	.custom-container .clear {
		clear: left;
	}
	.blinking{
		animation:blinkingText 1.2s infinite;
	}
	@keyframes blinkingText{
		0%{ color: #273746;}
		49%{    color: #273746; }
		60%{    color: transparent; }
		99%{    color:transparent;  }
		100%{   color: #273746;    }
	}
	.operator-badge {
		position:relative;
		padding-top:15px;
		padding-bottom: 5px;
		display:inline-block;
	}
	.notify-badge{
		position: absolute;
		right:-5px;
		top:12px;
		background: #f57f17;
		text-align: center;
		border-radius: 30px;
		color:white;
		padding:5px 8px;
		font-size:8px;
	}
	.part-items{
		cursor: pointer;
	}
	.selected-part{
		background-color:  #85929e  !important;
	}
	.completed{
		background-color: #2ecc71;
	}
	.in-progress{
		background-color: #f39c12;
	}
	@-webkit-keyframes blinker-bg {
		from { background-color: #f5b041; }
		to { background-color: inherit; }
	}
	@-moz-keyframes blinker-bg {
		from { background-color: #f5b041; }
		to { background-color: inherit; }
	}
	@-o-keyframes blinker-bg {
		from { background-color: #f5b041; }
		to { background-color: inherit; }
	}
	@keyframes blinker-bg {
		from { background-color: #f5b041; }
		to { background-color: inherit; }
	}
	.blink-bg{
		text-decoration: blink;
		-webkit-animation-name: blinker-bg;
		-webkit-animation-duration: 1s;
		-webkit-animation-iteration-count:infinite;
		-webkit-animation-timing-function:ease-in-out;
		-webkit-animation-direction: alternate;
	}
</style>