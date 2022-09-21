<div class="row" style="margin: 8px;">
  <div class="col-md-12">
    <ul class="nav nav-tabs" role="tablist" style="display: none;">
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
        <h6 class="text-center"><span class="badge badge-primary" style="font-size: 12pt;">QA Inspected</span></h6>
        @endif
        <table style="width: 100%;">
          <tr>
            <td colspan="3" style="width: 75%;">
              <span style="font-size: 12pt; color: #707B7C; display: block;">Process</span>
              <span class="process-name" style="font-size: 13pt; display: block; font-weight: bold;">
                <b>{{ $row['process_name'] }}</b>
              </span>
            </td>
            <td rowspan="3" style="width: 25%;" class="text-center">
              <span style="font-size: 17pt;"><b>{{ $row['production_order'] }}</b></span>
              <center>

              @if($row['status'] == 'Pending')
                <button type="button" class="btn btn-block btn-danger start-task-btn" style="width: 190px; height: 190px;" data-jobticket-id="{{ $row['job_ticket_id'] }}" data-process-id="{{ $row['process_id'] }}">
                  <i class="now-ui-icons media-1_button-play" style="font-size: 30pt; padding: 3px;"></i>
                  <br><span style="padding: 3px;">Start Work</span>
                </button>
              @endif

              @php
                $conversion_factor = 0;
                if(isset($row['conversion_factor'])){
                  $conversion_factor = $row['conversion_factor'];
                }
              @endphp
              @if($row['status'] == 'In Progress')
              <button type="button" class="btn btn-block btn-warning end-task-btn" data-timelog-id="{{ $row['time_log_id'] }}" data-process-name="{{ $row['process_name'] }}" data-balance-qty="{{ $row['qty_to_manufacture'] - $row['total_good'] }}" data-description="{{ $row['description'] }}" data-qty-mfg="{{ $row['qty_to_manufacture'] }}" data-density="{{ $conversion_factor }}" style="width: 190px; height: 190px;">
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
            </td>
          </tr>
          <tr>
            <td colspan="3" style="width: 75%;">
              <span style="font-size: 12pt; color: #707B7C; display: block;">Item Description</span>
              <span style="font-size: 13pt; display: block; font-weight: bold;"><b>{{ $row['item_code'] }}</b></span>
              <span style="font-size: 13pt; display: block;">{!! $row['description'] !!}</span>
            </td>
          </tr>
          <tr>
            <td style="width: 25%;">
              <span style="font-size: 12pt; color: #707B7C; display: block;">Reference No.</span>
              <span style="font-size: 13pt; display: block;">{{ $row['sales_order'] }}{{ $row['material_request'] }}</span>
            </td>
            <td style="width: 25%;">
              <span style="font-size: 12pt; color: #707B7C; display: block;">Start Time</span>
              <span style="font-size: 13pt; display: block;">{{ (in_array($row['status'], ['In Progress', 'Completed']) && Auth::user()->user_id == $row['operator_id'] && $row['machine_code'] == $machine_code) ? date('M-d-Y h:i A', strtotime($row['from_time'])) : '-' }}</span>
            </td>
            <td style="width: 25%;">
              <span style="font-size: 12pt; color: #707B7C; display: block;">End Time</span>
              <span style="font-size: 13pt; display: block;">{{ ($row['status'] == 'Completed' && Auth::user()->user_id == $row['operator_id'] && $row['machine_code'] == $machine_code) ? date('M-d-Y h:i A', strtotime($row['to_time'])) : '-' }}</span>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <span style="font-size: 12pt; color: #707B7C; display: block;">Customer</span>
              <span style="font-size: 13pt; display: block;">{{ $row['customer'] }}</span>
            </td>
            <td colspan="2" rowspan="2" style="padding: 0;" class="align-top">
              <table style="width: 100%; margin: 15px 0 0 0;">
                <tr>
                  <td style="width: 34%;" class="text-center"><span style="font-size: 12pt; color: #707B7C;">QTY</span></td>
                  <td style="width: 33%;" class="text-center"><span style="font-size: 12pt; color: #707B7C;">GOOD</span></td>
                  <td style="width: 33%;" class="text-center"><span style="font-size: 12pt; color: #707B7C;">REJECT</span></td>
                </tr>
                <tr>
                  <td class="text-center">
                    <span style="font-size: 18pt; font-weight: bold; display: block;">{{ $row['qty_to_manufacture'] }}</span>
                    <span style="font-size: 9pt;"><b>{{ $row['stock_uom'] }}</b></span>
                  </td>
                  <td class="text-center">
                    <span style="font-size: 18pt; font-weight: bold; display: block;">@if(!isset($row['conversion_factor'])){{ $row['total_good'] }}@else - @endif</span>
                    <span style="font-size: 9pt;"><b>{{ $row['stock_uom'] }}</b></span>
                  </td>
                  <td class="text-center">
                    <span style="font-size: 18pt; font-weight: bold; display: block;">@if(!isset($row['conversion_factor'])){{ $row['total_reject'] }}@else - @endif</span>
                    <span style="font-size: 9pt;"><b>{{ $row['stock_uom'] }}</b></span>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <span style="font-size: 12pt; color: #707B7C; display: block;">Project</span>
              <span style="font-size: 13pt; display: block;">{{ $row['project'] }}</span>
            </td>
          </tr>
        </table>
        <div class="row">
          <div class="col-md-12">
            <table style="width: 100%;">
              @php
                $disable_continue = ($row['status'] != 'Completed') ? 'disabled' : '';
                $disable_continue = ($row['operator_id'] && $row['operator_id'] != Auth::user()->user_id) ? 'disabled' : $disable_continue;
                $disable_continue = ($row['qty_to_manufacture'] <= $row['total_good']) ? 'disabled' : $disable_continue;
              @endphp
              <tr>
                <td class="text-center" style="width: 20%;">
                  <button type="button" class="btn btn-block start-task-btn" style="height: 100px; background-color: #117A65; border-radius: 0;" data-jobticket-id="{{ $row['job_ticket_id'] }}" data-process-id="{{ $row['process_id'] }}" {{ $disable_continue }}>
                    <i class="now-ui-icons ui-1_simple-add" style="font-size: 30pt;"></i>
                    <br><span style="font-size: 10pt;">Continue Task</span>
                  </button>
                </td>
                <td class="text-center" style="width: 20%;">
                  @php
                      $disabled_qc = ($row['status'] == 'Pending') ? 'disabled' : '';
                  @endphp
                  <button type="button" class="btn btn-block quality-inspection-btn" data-timelog-id="{{ $row['time_log_id'] }}" data-production-order="{{ $row['production_order'] }}" data-processid="{{ $row['process_id'] }}" data-inspection-type="Random Inspection" style="height: 100px; background-color: #f57f17; border-radius: 0;" {{ $disabled_qc }}>
                    <i class="now-ui-icons ui-1_check" style="font-size: 30pt;"></i><br><span style="font-size: 10pt;">Quality Check</span>
                  </button>
                </td>
                <td class="text-center" style="width: 20%;">
                  @php
                      $disabled_restart = ($row['qa_inspection_status'] != 'Pending' || in_array($row['status'], ['Pending', 'Completed'])) ? 'disabled' : '';
                  @endphp
                  <button type="button" class="btn btn-block restart-task-btn" data-timelog-id="{{ $row['time_log_id'] }}" style="height: 100px; background-color: #00838f; border-radius: 0;" {{ $disabled_restart }}>
                    <i class="now-ui-icons loader_refresh" style="font-size: 30pt;"></i><br><span style="font-size: 10pt;">Restart</span>
                  </button>
                </td>
                <td class="text-center" style="width: 20%;">
                  <button type="button" class="btn btn-block machine-breakdown-modal-btn" style="height: 100px; background-color: #6a1b9a; border-radius: 0;">
                    <i class="now-ui-icons ui-2_settings-90" style="font-size: 30pt;"></i><br><span style="font-size: 10pt;">Maintenance Request</span>
                  </button>
                </td>
                <td class="text-center" style="width: 20%;">
                  @php
                      $disabled_enter_reject = ($row['qa_inspection_status'] != 'Pending' || $row['status'] == 'In Progress') ? 'disabled' : '';
                  @endphp
                  <button type="button" class="btn btn-block enter-reject-btn" data-timelog-id="{{ $row['time_log_id'] }}"  data-process-name="{{ $row['process_name'] }}" data-good-qty="{{ $row['good'] }}" style="height: 100px; background-color: #C62828; border-radius: 0;">
                    <i class="now-ui-icons ui-1_simple-remove" style="font-size: 30pt;"></i><br><span style="font-size: 10pt;">Enter Reject</span>
                  </button>
                </td>
               <!--  <td class="text-center" style="width: 16%;">
                  <button type="button" class="btn btn-block enter-scrap-btn" data-item-code="{{ $row['item_code'] }}" data-production-order="{{ $row['production_order'] }}" data-process-name="{{ $row['process_name'] }}" data-jobticket-id="{{ $row['job_ticket_id'] }}" style="height: 100px; background-color: #95A5A6; border-radius: 0;">
                    <i class="now-ui-icons shopping_basket" style="font-size: 30pt;"></i><br><span style="font-size: 10pt;">Enter Scrap</span>
                  </button>
                </td> -->
              </tr>
            </table>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>

<input id="count-batch" value="{{ count($batch_list) }}" style="display: none;">
<select id="sel-batch" style="display: none;">
  <option value="">Select Batch Date</option>
  @foreach ($batch_list as $row)
      <option value="{{ $row->time_log_id }}" data-good="{{ $row->good }}" data-process="{{ $row->process_name }}">
        {{ date('M-d-Y h:i A', strtotime($row->from_time)) }} - {{ date('M-d-Y h:i A', strtotime($row->to_time)) }}
      </option>
  @endforeach
</select>



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
</style>