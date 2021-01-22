<div class="row">
  <div class="col-md-12" style="text-transform: uppercase;">
    <h5 class="text-center title" style="margin-top: 12px; font-style: italic;">{{ $process_details->process_name }} Process</h5>
  </div>
  <div class="col-md-12">
    <table style="width: 100%;">
      <tr>
        <td colspan="3" style="width: 75%;">
          {{--  <span style="font-size: 12pt; color: #707B7C; display: block;">Reference No:</span>  --}}
          <span style="font-size: 13pt; display: block;">{{ $production_order_details->sales_order }}{{ $production_order_details->material_request }} - {{ $production_order_details->classification }}</span>
        </td>
        <td rowspan="6" style="width: 25%;" class="text-center">
          
          <center>
            <span style="font-size: 17pt;"><b>{{ $production_order_details->production_order }}</b></span>

          @if($task_details['status'] == 'Not Started')
            <button type="button" class="btn btn-block btn-danger start-task-btn" style="width: 190px; height: 190px;" data-jobticket-id="{{ $task_details['id'] }}">
              <i class="now-ui-icons media-1_button-play" style="font-size: 30pt; padding: 3px;"></i>
              <br><br><span style="padding: 3px;">Start {{ $process_details->process_name }}</span>
            </button>
          @endif

          @if($task_details['status'] == 'In Progress')
          <button type="button" class="btn btn-block btn-warning end-task-btn" style="width: 190px; height: 190px;" data-timelog-id="{{ $task_details['id'] }}" data-balance-qty="{{ $qty_arr['pending_qty'] }}">
            <div class="waves-effect waves z-depth-4">
              <div class="spinner-grow" style="width: 4rem; height: 4rem;">
                <span class="sr-only">Loading...</span>
              </div><h4 class="text-center" style="color: #273746; font-weight:bold;">In Progress</h4>
            </div>
          </button>
          @endif

        @if($task_details['status'] == 'Completed')
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
          <span style="font-size: 12pt; color: #707B7C; display: block;">Customer</span>
          <span style="font-size: 13pt; display: block;">{{ $production_order_details->customer }}</span>
        </td>
      </tr>
      <tr>
        <td colspan="3" style="width: 75%;">
          <span style="font-size: 12pt; color: #707B7C; display: block;">Item Description</span>
          <span style="font-size: 13pt; display: block; font-weight: bold;"><b>{{ $production_order_details->item_code }}</b></span>
          <span style="font-size: 13pt; display: block;">{!! $production_order_details->description !!}</span>
        </td>
      </tr>
      @if($production_order_details->notes)
      <tr>
        <td colspan="3" style="width: 75%;">
          <span style="font-size: 12pt; color: #707B7C; display: block;">Notes</span>
          <span style="font-size: 13pt; display: block;">{{ $production_order_details->notes }}</span>
        </td>
      </tr>
      @endif
      <tr>
        <td colspan="2" style="padding: 0;" class="align-top">
          <table style="width: 100%; margin: 15px 0 0 0;">
            <tr>
              <td style="width: 34%;" class="text-center"><span style="font-size: 12pt; color: #707B7C;">QTY</span></td>
              <td style="width: 33%;" class="text-center"><span style="font-size: 12pt; color: #707B7C;">PENDING</span></td>
              <td style="width: 33%;" class="text-center"><span style="font-size: 12pt; color: #707B7C;">COMPLETED</span></td>
            </tr>
            <tr>
              <td class="text-center">
                <span style="font-size: 18pt; font-weight: bold; display: block;">{{ $qty_arr['required_qty'] }}</span>
              </td>
              <td class="text-center">
                <span style="font-size: 18pt; font-weight: bold; display: block;">{{ $qty_arr['pending_qty'] }}</span>
              </td>
              <td class="text-center">
                <span style="font-size: 18pt; font-weight: bold; display: block;">{{ $qty_arr['completed_qty'] }}</span>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </div>
</div>
<br>
{{--  <div class="row">
  <div class="col-md-4">Start Time: </div>
  <div class="col-md-4">End Time: </div>
  <div class="col-md-4">Duration: </div>
</div>  --}}

<div class="row">
  <div class="col-md-12">
    <table style="width: 100%;">
      <tr>
        @php
            $disable_restart = ($task_details['status'] != 'In Progress') ? 'disabled' : '';
        @endphp
        <td class="text-center" style="width: 20%;">
          <button type="button" class="btn btn-block btn-danger sub-btn restart-task-btn" style="background-color: #00838f;" data-timelog-id="{{ $task_details['id'] }}" {{ $disable_restart }}>
            <i class="now-ui-icons loader_refresh" style="padding: 3px;"></i>
            <br><span style="font-size: 8pt;">Restart</span>
          </button>
        </td>
        <td class="text-center" style="width: 20%;">
          <button type="button" class="btn btn-block sub-btn" style="background-color: #f57f17;">
            <i class="now-ui-icons ui-1_check" style="font-size: 13pt;"></i><br><span style="font-size: 8pt;">Quality Check</span>
          </button>
        </td>
        <td class="text-center" style="width: 20%;">
          <button type="button" class="btn btn-block sub-btn" style="background-color: #6a1b9a;" id="machine-breakdown-modal-btn">
            <i class="now-ui-icons ui-2_settings-90" style="font-size: 13pt;"></i><br><span style="font-size: 8pt;">Maintenance Request</span>
          </button>
        </td>
        @php
            $disable_enter_reject = (!$task_details['completed_task_id']) ? 'disabled' : '';
        @endphp
        <td class="text-center" style="width: 20%;">
          <button type="button" class="btn btn-block btn-danger sub-btn" id="enter-reject-btn"  data-timelog-id="{{ $task_details['completed_task_id'] }}" data-good="{{ $task_details['good'] }}" {{ $disable_enter_reject }}>
            <i class="now-ui-icons ui-1_simple-remove" style="font-size: 13pt;"></i><br><span style="font-size: 8pt;">Enter Reject</span>
          </button>
        </td>
        <td class="text-center" style="width: 20%;">
          <button type="button" class="btn btn-block sub-btn" style="background-color: {{ ($machine_status == 'Start Up') ? '#7f8c8d' : '#28b463' }}" id="machine-power-btn" data-status="{{ $machine_status }}">
            <i class="now-ui-icons media-1_button-power" style="font-size: 13pt;"></i><br><span style="font-size: 8pt;">{{ $machine_status }} Machine</span>
          </button>
        </td>
      </tr>
    </table>
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