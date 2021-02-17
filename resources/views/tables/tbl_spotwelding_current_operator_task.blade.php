<div class="row" style="margin: 8px;">
  <div class="col-md-12">
    <ul class="nav nav-tabs" id="op-tab" role="tablist" style="display: none;">
      @foreach($task_list as $index => $row)
      <li class="nav-item">
        <a class="nav-link {{ ($loop->first) ? 'active' : '' }}" id="tab{{ $index }}" data-toggle="tab" href="#task{{ $index }}" role="tab">Task #{{ $index }}</a>
      </li>
      @endforeach
    </ul>
    <div class="tab-content">
      @foreach($task_list as $index => $row)
      <div class="tab-pane {{ ($loop->first) ? 'active' : '' }}" id="task{{ $index }}" role="tabpanel">
      <div class="row" style="margin-right: -45px;">
        <div class="col-md-4 offset-md-4 text-center" style="margin-top: -77px;">
            @foreach($in_progress_operator as $r)
            <span class="operator-badge view-in-progress-operator" data-operator-id="{{ $r->operator_id }}" data-jobticket-id="{{ $row['job_ticket_id'] }}">
              <span class="notify-badge">{{ $r->completed_qty }}</span>
              <img src="{{ asset('img/user.png') }}" width="40" height="40">
              <span style="font-size: 7pt; display: block;">{{ $r->operator_nickname }}</span>
            </span>
            @endforeach
        </div>
        <div class="col-md-4" style="margin-top: -77px;">
          @if($row['status'] != 'Completed')
          <div class="pull-right">
            <table>
              <tr>
                @foreach($row['helpers'] as $helper)
                <td style="padding: 0; width: 50px;" class="text-center">
                  <img src="{{ asset('img/user.png') }}" width="40" height="40" style="padding: 0; margin: 0 auto;" class="view-helpers-btn" data-timelog-id="{{ $row['time_log_id'] }}" data-display-all="0">
                  <span style="font-size: 7pt; display: block;">{{ $helper->operator_nickname }}</span>
                </td>
                @endforeach
                <td class="align-top" style=" width: 50px;">
                  <img src="{{ asset('img/add-user.png') }}" width="80" height="55" style="margin: 8px auto;" class="add-helper-btn" data-timelog-id="{{ $row['time_log_id'] }}" >
                  <span style="font-size: 7pt; display: block;">&nbsp;</span>
                </td>
              </tr>
            </table>
          </div>
          @endif
          @if($row['status'] == 'Completed')
          <div class="pull-right" style="margin-top: 8px;">
            <button class="btn btn-secondary view-helpers-btn" data-display-all="1" data-timelog-id="{{ $row['time_log_id'] }}" data-jobticket-id="{{ $row['job_ticket_id'] }}" data-machine="{{$row['machine_code']}}" data-operator="{{ $row['operator_id'] }}">
              View Helpers ({{ $row['count_helpers'] }})
            </button>
          </div>
          @endif
        </div>
      </div>
      @if($row['qa_inspection_status'] != 'Pending')
      <div class="row">
        <div class="col-md-12">
          <h6 class="text-center"><span class="badge badge-primary" style="font-size: 12pt;">QA Inspected</span></h6>
        </div>  
      </div>
      @endif
      <div class="row">
        <table style="width: 50%; float: left;">
          <tr>
            <td style="width: 100%; text-align: justify; padding: 5px;">
              <span style="font-size: 9pt; color: #707B7C; display: block;">ITEM DETAILS</span>
              <span style="font-size: 10pt; display: block; font-weight: bold;"><b>{{ $row['item_code'] }}</b></span>
              <span style="font-size: 9pt; display: block;">{!! $row['description'] !!}</span>
            </td>
          </tr>
        </table>
        <table style="width: 50%; float: left;">
          <tr>
            <td style="width: 10%; background-color: #D5D8DC; border: 1px solid #ABB2B9;" class="align-top text-center">
              <span style="font-size: 9pt; color: #707B7C; display: block;">QTY</span>
              <span style="font-size: 17pt; display: block;"><b>{{ $row['qty_to_manufacture'] }}</b></span>
              <span style="font-size: 9pt; display: block;">{{ $row['stock_uom'] }}</span>
            </td>
            <td style="width: 10%; background-color: #D5D8DC; border: 1px solid #ABB2B9;" class="align-top text-center">
              <span style="font-size: 9pt; color: #707B7C; display: block;">IN PROCESS</span>
              <span style="font-size: 17pt; display: block;"><b>{{ ($row['qty_to_manufacture'] - $row['completed_qty'])}}</b></span>
              <span style="font-size: 9pt; display: block;">{{ $row['stock_uom'] }}</span>
            </td>
            <td style="width: 10%; background-color: #D5D8DC; border: 1px solid #ABB2B9;" class="align-top text-center">
              <span style="font-size: 9pt; color: #707B7C; display: block;">REJECT</span>
              <span style="font-size: 17pt; display: block;"><b>{{ $total_rejects }}</b></span>
              <span style="font-size: 9pt; display: block;">{{ $row['stock_uom'] }}</span>
            </td>
            <td style="width: 10%; background-color: #D5D8DC; border: 1px solid #ABB2B9;" class="align-top text-center">
              <span style="font-size: 9pt; color: #707B7C; display: block;">COMPLETED</span>
              <span style="font-size: 17pt; display: block;"><b>{{ $row['completed_qty'] }}</b></span>
              <span style="font-size: 9pt; display: block;">{{ $row['stock_uom'] }}</span>
            </td>
          </tr>
        </table>
      </div>
        <div class="row" style="padding: 0;">
        <span class="process-name" style="display: none;">{{ $row['process_name'] }}</span>
        <span class="process-id" style="display: none;">{{ $row['process_id'] }}</span>

								if($part['status'] == 'Completed'){
									$selected_status = 'completed';
									$selected = 'selected-box';
								}
              			@endphp
							<div class="custom-box part-items {{ $selected }} {{ $selected_status }}" data-tabid="task{{ $index }}">
								<div class="d-block">
									<span class="font-weight-bold part-item-code">{{ $part['item_code'] }}</span> - <span style="font-size: 10pt;">Qty:[{{ $part['qty'] }}]</span>
								</div>
								<span class="d-none part-category">{{ $part['parts_category'] }}</span>
								<span class="d-block" style="font-size: 10pt;">{{ $part['item_name'] }}</span>
								<span class="d-block" style="font-size: 9pt;">{{ $status }}</span>
								<span class="d-none part-production-order">{{ $part['production_order'] }}</span>
								<span class="d-block" style="font-size: 9pt;">Available Qty: {{ $part['available_stock'] }}</span>
							</div>
							@endforeach
							<p class="clear"></p>
						</div>
					</div>
					<div class="col-md-4">
						<center>
							<h5 class="title" style="font-size: 13pt;">{{ $row['production_order'] }}</h5>
							<button type="button" class="btn btn-block btn-danger start-task-btn {{ ($row['status'] == 'Pending') ? '' : 'd-none' }}" style="width: 190px; height: 190px;" data-jobticket-id="{{ $row['job_ticket_id'] }}" data-process-id="{{ $row['process_id'] }}" data-ho-code="{{ $row['item_code'] }}" data-refno="{{ $row['sales_order'] }}{{ $row['material_request'] }}" data-reqqty="{{ $row['qty_to_manufacture'] }}">
								<i class="now-ui-icons media-1_button-play" style="font-size: 30pt; padding: 3px;"></i>
								<span class="d-block p-2">Start Work</span>
							</button>
							<div id="jt-status" class="d-none">{{ $row['status'] }}</div>
							<button type="button" class="btn btn-block btn-warning end-task-btn {{ ($row['status'] == 'In Progress') ? '' : 'd-none' }}" data-timelog-id="{{ $row['time_log_id'] }}" data-process-name="{{ $row['process_name'] }}" data-jobticket="{{ $row['job_ticket_id'] }}" data-reqqty="{{ $row['qty_to_manufacture'] }}" data-spotwelding-part-id="{{ $row['spotwelding_part_id'] }}" style="width: 190px; height: 190px;">
								<div class="waves-effect waves z-depth-4">
									<div class="spinner-grow" style="width: 4rem; height: 4rem;">
										<span class="sr-only">Loading...</span>
									</div>
									<h4 class="text-center blinking font-weight-bold text-dark">In Progress</h4>
								</div>
							</button>
							<button type="button" class="btn btn-block btn-success completed-btn {{ ($row['status'] == 'Completed') ? '' : 'd-none' }}" style="width: 190px; height: 190px;">
								<i class="now-ui-icons ui-1_check" style="font-size: 30pt; padding: 3px;"></i>
								<span class="d-block p-2">Completed</span>
							</button>
						</center>
					</div>
					<div class="col-md-12 p-0 mt-2" style="font-size: 8pt;">
						<table class="table table-bordered">
							<col style="width: 34%;">
							<col style="width: 10%;">
							<col style="width: 8%;">
							<col style="width: 8%;">
							<col style="width: 20%;">
							<col style="width: 10%;">
							<col style="width: 10%;">
							<tbody>
								<tr>
									<th class="text-center">PROCESS</th>
									<th class="text-center">MACHINE</th>
									<th class="text-center">GOOD</th>
									<th class="text-center">REJECT</th>
									<th class="text-center">OPERATOR</th>
									<th class="text-center" colspan="2">ACTIONS</th>
								</tr>
								@forelse($logs as $log)
								<tr style="font-size: 10pt;" class="{{ ($log['status'] == 'In Progress') ? 'blink-bg' : '' }}">
									<td class="text-center">
										@php
											$continue_btn = ($log['total_completed_qty'] >= $row['qty_to_manufacture']) ? 'disabled' : '';
											$process_description = (count($bom_parts) == $log['count_parts']) ? 'ALL PARTS' : $log['process_description'];
										@endphp
										<span class="d-block">{{ $process_description }}</span>
										<span style="font-size: 10pt;" class="badge badge-{{ ($log['status'] == 'Completed') ? 'success' : 'warning' }} text-white">{{ $log['status']}}</span>
									</td>
									<td class="text-center">
										<span class="d-block">{{ $log['machine'] }}</span>
										<span class="d-block font-italic" style="font-size: 8pt;">{{ ($log['status'] == 'Completed') ? $log['duration'] : '-' }}</span>
									</td>
									<td class="text-center">{{ $log['completed_qty'] }}</td>
									<td class="text-center">{{ $log['reject'] }}</td>
									<td class="text-center">{{ $log['operator_name'] }}</td>
									<td class="text-center p-1">
										<button type="button" class="btn btn-block continue-log-btn rounded-0" data-timelog-id="{{ $log['time_log_id'] }}" style="height: 60px; background-color: #117A65;" {{ ($row['status'] == 'In Progress') ? 'disabled' : '' }} {{ $continue_btn }}>
											<i class="now-ui-icons media-1_button-play" style="font-size: 13pt;"></i>
											<span class="d-block" style="font-size: 8pt;">Continue</span>
										</button>
									</td>
									<td class="text-center p-1">
										@php
											$disabled_enter_reject = ($row['qa_inspection_status'] != 'Pending' || $row['status'] == 'In Progress') ? 'disabled' : '';
										@endphp
										<button type="button" class="btn btn-block enter-reject-btn rounded-0" data-id="{{ $row['time_log_id'] }}" data-processid={{$row['process_id']}}  data-process-name="{{ $row['process_name'] }}" data-good-qty="{{ $row['completed_qty'] }}" data-row="1" style="height: 60px; background-color: #C62828;">
											<i class="now-ui-icons ui-1_simple-remove" style="font-size: 13pt;"></i>
											<span class="d-block" style="font-size: 8pt;">Reject</span>
										</button>
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
								<td class="text-center w-25">
									@php
										$disabled_restart = ($row['operator_id'] != Auth::user()->user_id) ? 'disabled' : '';
									@endphp
									<button type="button" class="btn btn-block restart-task-btn rounded-0" data-timelog-id="{{ $row['time_log_id'] }}" style="height: 70px; background-color: #00838f;" {{ $disabled_restart }} {{ ($row['status'] == 'In Progress') ? '' : 'disabled' }}>
										<i class="now-ui-icons loader_refresh" style="font-size: 15pt;"></i>
										<span class="d-block" style="font-size: 10pt;">Restart</span>
									</button>
								</td>
								<td class="text-center w-25">
									@php
										$disabled_qc = ($row['qa_inspection_status'] != 'Pending' || $row['status'] == 'Pending') ? 'disabled' : '';
									@endphp
									<button type="button" class="btn btn-block quality-inspection-btn rounded-0" data-timelog-id="{{ $row['time_log_id'] }}" data-production-order="{{ $row['production_order'] }}" data-processid="{{ $row['process_id'] }}" data-inspection-type="Random Inspection" style="height: 70px; background-color: #f57f17;" {{ $disabled_qc }}>
										<i class="now-ui-icons ui-1_check" style="font-size: 15pt;"></i>
										<span class="d-block" style="font-size: 10pt;">Quality Check</span>
									</button>
								</td>
								<td class="text-center w-25">
									<button type="button" class="btn btn-block machine-breakdown-modal-btn rounded-0" style="height: 70px; background-color: #6a1b9a;">
										<i class="now-ui-icons ui-2_settings-90" style="font-size: 15pt;"></i>
										<span class="d-block" style="font-size: 10pt;">Maintenance Request</span>
									</button>
								</td>
								<td class="text-center w-25">
									@php
										$disabled_enter_reject = ($row['qa_inspection_status'] != 'Pending' || $row['status'] == 'In Progress') ? 'disabled' : '';
									@endphp
									<button type="button" class="btn btn-block enter-reject-btn rounded-0" data-id="{{ $row['job_ticket_id'] }}"  data-processid={{$row['process_id']}} data-process-name="{{ $row['process_name'] }}" data-good-qty="{{ $row['completed_qty'] }}" data-row="0" style="height: 70px; background-color: #C62828;">
										<i class="now-ui-icons ui-1_simple-remove" style="font-size: 15pt;"></i>
										<span class="d-block" style="font-size: 10pt;">Enter Reject</span>
									</button>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			@endforeach
    	</div>
  	</div>
</div>

          <div class="col-md-8" style="padding: 0;" id="select-part-div">
            <h5 class="title text-center" style="margin: 0 0 8px 0; font-size: 12pt;">Select Parts</h5>
            <div class="custom-container">
              @foreach($bom_parts as $part)
              @php
              $selected = '';
              if($row['operator_id'] == Auth::user()->user_id){
                $status = $part['status'];
                if($part['status'] == 'In Progress'){
                  $selected = 'in-progress';
                }

                if($part['status'] == 'Completed'){
                  $selected = 'completed';
                }
              }else{
                $status = 'Not Started';
              } 
              @endphp
              <div class="custom-box part-items {{ $selected }}" data-tabid="task{{ $index }}">
                <div style="display: block;">
                  <span style="font-weight: bold;">{{ $part['item_code'] }}</span> - Qty:[{{ $part['qty'] }}]
                </div>                     
                
                <span hidden>{{ $part['parts_category'] }}</span>
                <span style="display: block;">{{ $part['item_name'] }}</span>
                <span style="font-size: 9pt;">{{ $status }}</span>
                
                <span style="display: none;">{{ $part['production_order'] }}</span>
                <span style="display: block;">Available Qty: {{ $part['available_stock'] }}</span>
              </div>
              @endforeach
              <p class="clear"></p>
            </div>
          </div>

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
          </style>

          <div class="col-md-4">
            <center>
            <h5 class="title" style="font-size: 13pt;">{{ $row['production_order'] }}</h5>
            @if($row['status'] == 'Pending')
            <button type="button" class="btn btn-block btn-danger start-task-btn" style="width: 190px; height: 190px;" data-jobticket-id="{{ $row['job_ticket_id'] }}" data-process-id="{{ $row['process_id'] }}" data-ho-code="{{ $row['item_code'] }}" data-refno="{{ $row['sales_order'] }}{{ $row['material_request'] }}" data-reqqty="{{ $row['qty_to_manufacture'] }}">
                <i class="now-ui-icons media-1_button-play" style="font-size: 30pt; padding: 3px;"></i>
                <br><span style="padding: 3px;">Start Work</span>
              </button>
            @endif
            <div id="jt-status" hidden>{{ $row['status'] }}</div>
            @if($row['status'] == 'In Progress')
            <button type="button" class="btn btn-block btn-warning end-task-btn" data-timelog-id="{{ $row['time_log_id'] }}" data-process-name="{{ $row['process_name'] }}" data-jobticket="{{ $row['job_ticket_id'] }}" data-reqqty="{{ $row['qty_to_manufacture'] }}" data-spotwelding-part-id="{{ $row['spotwelding_part_id'] }}" style="width: 190px; height: 190px;">
              <div class="waves-effect waves z-depth-4">
                <div class="spinner-grow" style="width: 4rem; height: 4rem;">
                  <span class="sr-only">Loading...</span>
                </div><h4 class="text-center blinking" style="color: #273746; font-weight:bold;">In Progress</h4>
              </div>
            </button>
            @endif

            @if($row['status'] == 'Completed')
              <button type="button" class="btn btn-block btn-success" style="width: 190px; height: 190px;">
                <i class="now-ui-icons ui-1_check" style="font-size: 30pt; padding: 3px;"></i>
                <br><span style="padding: 3px;">Completed</span>
              </button>
              @endif
            </center>
          </div>
           <div class="col-md-12" style="padding: 0; margin-top: 10px; font-size: 8pt;">
           <table class="table table-bordered" border="1">
             <tbody>
              <tr>
                 <th class="text-center">PROCESS</th>
                 <th class="text-center">MACHINE</th>
                 <th class="text-center">DURATION</th>
                 <th class="text-center">GOOD</th>
                 <th class="text-center">REJECT</th>
                 <th class="text-center">OPERATOR</th>
                 <th class="text-center" colspan="2">ACTIONS</th>
               </tr>
               @forelse($logs as $log)
               <tr style="font-size: 10pt;" class="{{ ($log['status'] == 'In Progress') ? 'blink-bg' : '' }}">
                 <td class="text-center">{{ $log['process_description'] }}<br>
                  <span style="font-size: 10pt;" class="badge badge-{{ ($log['status'] == 'Completed') ? 'success' : 'warning' }} text-white">{{ $log['status']}}</span>
                  </td>
                 <td class="text-center">{{ $log['machine'] }}</td>
                 <td class="text-center">{{ ($log['status'] == 'Completed') ? $log['duration'] : '-' }}</td>
                 <td class="text-center">{{ $log['completed_qty'] }}</td>
                 <td class="text-center">{{ $log['reject'] }}</td>
                 <td class="text-center">{{ $log['operator_name'] }}</td>
                 <td class="text-center">
                  @php
                    $disabled_restart = ($log['operator_id'] != Auth::user()->user_id) ? 'disabled' : '';
                  @endphp
                  <button type="button" class="btn restart-task-btn" data-timelog-id="{{ $log['time_log_id'] }}" style="width: 90px; padding: 4px; height: 50px; background-color: #00838f; border-radius: 0;" {{$disabled_restart}}>
                    <i class="now-ui-icons loader_refresh" style="font-size: 13pt;"></i><br><span style="font-size: 8pt;">Restart</span>
                  </button>
                </td>
                <td class="text-center">
                  @php
                    $disabled_enter_reject = ($row['qa_inspection_status'] != 'Pending' || $row['status'] == 'In Progress') ? 'disabled' : '';
                  @endphp
                  <button type="button" class="btn btn-block enter-reject-btn" data-id="{{ $row['time_log_id'] }}" data-processid={{$row['process_id']}}  data-process-name="{{ $row['process_name'] }}" data-good-qty="{{ $row['completed_qty'] }}" data-row="1" style="height: 70px; background-color: #C62828; border-radius: 0;">
                      <i class="now-ui-icons ui-1_simple-remove" style="font-size: 13pt;"></i><br><span style="font-size: 8pt;">Enter Reject</span>
                    </button>
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
            <table style="width: 100%;">
              @php
                $disable_continue = ($row['status'] != 'Completed') ? 'disabled' : '';
              @endphp
              <tr>
                <td class="text-center" style="width: 20%;">
                  <button type="button" class="btn btn-block start-task-btn" style="height: 70px; background-color: #117A65; border-radius: 0;" data-jobticket-id="{{ $row['job_ticket_id'] }}" data-process-id="{{ $row['process_id'] }}"  data-ho-code="{{ $row['item_code'] }}" data-refno="{{ $row['sales_order'] }}{{ $row['material_request'] }}" data-reqqty="{{ $row['qty_to_manufacture'] }}" {{ $disable_continue }}>
                    <i class="now-ui-icons ui-1_simple-add" style="font-size: 13pt;"></i>
                    <br><span style="font-size: 8pt;">Continue Task</span>
                  </button>
                </td>
                <td class="text-center" style="width: 20%;">
                  @php
                      $disabled_qc = ($row['qa_inspection_status'] != 'Pending' || $row['status'] == 'Pending') ? 'disabled' : '';
                  @endphp
                   <button type="button" class="btn btn-block quality-inspection-btn" data-timelog-id="{{ $row['time_log_id'] }}" data-production-order="{{ $row['production_order'] }}" data-processid="{{ $row['process_id'] }}" data-inspection-type="Random Inspection" style="height: 70px; background-color: #f57f17; border-radius: 0;" {{ $disabled_qc }}>
                    <i class="now-ui-icons ui-1_check" style="font-size: 13pt;"></i><br><span style="font-size: 8pt;">Quality Check</span>
                  </button>
                </td>
                <td class="text-center" style="width: 20%;">
                  <button type="button" class="btn btn-block machine-breakdown-modal-btn" style="height: 70px; background-color: #6a1b9a; border-radius: 0;">
                    <i class="now-ui-icons ui-2_settings-90" style="font-size: 13pt;"></i><br><span style="font-size: 8pt;">Maintenance Request</span>
                  </button>
                </td>
                <td class="text-center" style="width: 20%;">
                  @php
                      $disabled_enter_reject = ($row['qa_inspection_status'] != 'Pending' || $row['status'] == 'In Progress') ? 'disabled' : '';
                  @endphp
                  <button type="button" class="btn btn-block enter-reject-btn" data-id="{{ $row['job_ticket_id'] }}"  data-processid={{$row['process_id']}} data-process-name="{{ $row['process_name'] }}" data-good-qty="{{ $row['completed_qty'] }}" data-row="0" style="height: 70px; background-color: #C62828; border-radius: 0;">
                    <i class="now-ui-icons ui-1_simple-remove" style="font-size: 13pt;"></i><br><span style="font-size: 8pt;">Enter Reject</span>
                  </button>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>

<style>
  .blinking{
    animation:blinkingText 1.2s infinite;
  }

  @keyframes blinkingText{
    0%{     color: #273746;    }
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

<script>
  $( function() {
    $('.part-items').click(function(){
      if ($('#jt-status').text() != 'In Progress') {
      if ($(this).hasClass('completed')) {
        $('#'+ $(this).data('tabid') + ' #select-part-div .completed').each(function(){
          $(this).toggleClass('selected-part');
        });
      }else{
          $(this).toggleClass('selected-part');
        }
      }
    });
  });
</script>