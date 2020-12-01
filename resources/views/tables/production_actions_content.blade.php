{{-- @if($details)
  @php
  if ($details->status == 'Accepted') {
    $action = '/start_task';
  }else{
    $action = '#';
  }

  if ($details->operator_id == ''|| $details->operator_name == '') {
    $no_operator = 'disabled';
  }else{
    $no_operator = '';
  }
  @endphp

  <form id="production-actions-frm" action="{{ $action }}" method="POST">
    @csrf
<div class="row">
    <div class="col-md-12">
      {{$no_operator}}
      <table style="width: 100%;">
        <input type="hidden" name="id" value="{{ $details->id }}">
        <input type="hidden" name="production_order" value="{{ $details->production_order }}">
        <input type="hidden" name="operator_name" value="@if(Auth::user()){{ Auth::user()->employee_name }}@endif">
        <input type="hidden" name="operator_id" value="@if(Auth::user()){{ Auth::user()->user_id }}@endif">
        <tr>
          <td colspan="2">
            <span style="font-size: 12pt; color: #707B7C; display: block;">Item Description</span>
            <span style="font-size: 13pt; display: block; font-weight: bold;"><b>{{ $details->item_code }}</b></span>
            <span style="font-size: 13pt; display: block;">{!! $details->description !!}</span>
          </td>
          <td rowspan="3" class="text-center">
            @if($details->qa_inspection_status != 'Pending')
            <center>
            <span class="badge badge-{{ ($details->qa_inspection_status == "QC Failed") ? 'danger' : 'success' }}" style="font-size: 14pt;">{{ $details->qa_inspection_status}}</span>
            </center>
            @endif

            @if($details->item_feedback == 'Quality Check' && $details->qa_inspection_status == 'Pending')
            <center>
            <span class="badge badge-danger" style="font-size: 14pt;">For {{ $details->item_feedback}}</span></center>
            @endif
            @if($details->is_rework == 1 && $details->status != 'Completed')
            <center>
            <span class="badge badge-danger" style="font-size: 14pt;">For Rework</span></center>
            @elseif($details->is_rework == 1 && $details->status == 'Completed')
            <center>
            <span class="badge badge-danger" style="font-size: 14pt;">Rework</span></center>
            @endif
            <center>
            @if($details->status == 'In Progress')
            <button type="button" class="btn btn-block btn-warning" id="production-actions-end-btn" data-name="{{ $details->id }}" style="width: 190px; height: 190px;" {{ $no_operator }}>
              <div class="spinner-grow" style="width: 4rem; height: 4rem;">
                <span class="sr-only">Loading...</span>
              </div><br><span style="margin: 3px;">In Progress</span>
            </button>
            @elseif($details->status == 'Pending')
            <button type="submit" class="btn btn-block btn-danger" style="width: 190px; height: 190px;" {{ $no_operator }}>
              <i class="now-ui-icons media-1_button-play" style="font-size: 30pt; padding: 3px;"></i>
              <br><span style="padding: 3px;">Start Work</span>
            </button>
            @elseif($details->status == 'Completed')
            <button type="button" class="btn btn-block btn-success" style="width: 190px; height: 190px;" {{ $no_operator }}>
              <i class="now-ui-icons ui-1_check" style="font-size: 30pt; padding: 3px;"></i>
              <br><span style="padding: 3px;">Completed</span>
            </button>
            @endif
            </center>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <span style="font-size: 12pt; color: #707B7C; display: block;">Qty</span>
            <span style="font-size: 13pt; display: block;"><b>{{ number_format($details->qty_accepted) }} {{ $details->stock_uom }}</b></span>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <span style="font-size: 12pt; color: #707B7C; display: block;">Sales Order</span>
            <span style="font-size: 13pt; display: block;">{{ $details->sales_order }}</span>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <span style="font-size: 12pt; color: #707B7C; display: block;">Customer</span>
            <span style="font-size: 13pt; display: block;">{{ $details->customer }}</span>
          </td>
          <td>
            <span style="font-size: 12pt; color: #707B7C; display: block;">Start Time</span>
            <span style="font-size: 13pt; display: block;">{{ (in_array($details->status, ['In Progress', 'Completed'])) ? $details->from_time : '-' }}</span>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <span style="font-size: 12pt; color: #707B7C; display: block;">Project</span>
            <span style="font-size: 13pt; display: block;">{{ $details->project }}</span>
          </td>
          <td>
            <span style="font-size: 12pt; color: #707B7C; display: block;">End Time</span>
            <span style="font-size: 13pt; display: block;">{{ ($details->status == 'Completed') ? $details->to_time : '-' }}</span>
          </td>
        </tr>
         <tr>
          <td style="width: 33%;">
            <span style="font-size: 12pt; color: #707B7C; display: block;" class="text-center">Good</span>
            <span style="font-size: 19pt; font-weight: bold; display: block;" class="text-center">{{ number_format($details->good) }}</span>
          </td>
          <td style="width: 34%;">
            <span style="font-size: 12pt; color: #707B7C; display: block;" class="text-center">Reject</span>
            <span style="font-size: 19pt; font-weight: bold; display: block;" class="text-center">{{ number_format($details->reject) }}</span>
          </td>
          <td style="width: 33%;">
            <span style="font-size: 12pt; color: #707B7C; display: block;" class="text-center">Completed</span>
            <span style="font-size: 19pt; font-weight: bold; display: block;" class="text-center">{{ number_format($details->completed_qty) }}</span>
          </td>
        </tr>
      </table>
    </div>
</div>
  </form>

<div class="row">
  <div class="col-md-12">
    <table style="width: 100%;">
      <tr>
        <td class="text-center">
          @php
          $type = 'Random Inspection';
          if ($details->item_feedback == 'Quality Check') {
            $type = 'Quality Check';
          }

          if ($details->reject > 0 && $details->qa_inspection_status == 'Pending') {
            $type = 'Reject Confirmation';
          }
          @endphp
          <button type="button" class="btn btn-info production-actions-qc-btn" style="width: 140px; height: 100px;" {{ (in_array($details->status, ['In Progress', 'Accepted'])) ? 'disabled' : '' }} data-id="{{ $details->production_order }}" data-workstation="{{ $details->workstation }}" data-machine="{{ $details->machine }}" data-type="{{ $type }}" {{ $no_operator }} {{ ($details->qa_inspection_status != 'Pending') ? 'disabled' : '' }}>
            <i class="now-ui-icons ui-1_check" style="font-size: 30pt;"></i><br><span style="font-size: 10pt;">Quality Check</span>
          </button>
        </td>
        <td class="text-center">
          <button type="button" class="btn btn-secondary" id="production-actions-restart-btn" style="width: 140px; height: 100px;" {{ (in_array($details->status, ['Accepted', 'Completed'])) ? 'disabled' : '' }} data-name="{{ $details->id }}" data-prodno="{{ $details->production_order }}" {{ $no_operator }} {{ ($details->qa_inspection_status != 'Pending') ? 'disabled' : '' }}>
            <i class="now-ui-icons loader_refresh" style="font-size: 30pt;"></i><br><span style="font-size: 10pt;">Restart</span>
          </button>
        </td>
                <td class="text-center">
                  <button type="button" class="btn btn-primary" id="production-actions-edit-btn" style="width: 140px; height: 100px;" data-name="{{ $details->id }}" data-prodno="{{ $details->production_order }}" {{ $no_operator }} {{ ($details->qa_inspection_status != 'Pending') ? 'disabled' : '' }}>
                    <i class="now-ui-icons design-2_ruler-pencil" style="font-size: 30pt;"></i><br><span style="font-size: 10pt;">Edit</span>
                  </button>
                </td>
                @php
                $production_item = $details->item_code .': '.$details->description;
                @endphp
                <td class="text-center">
                  <button type="button" class="btn btn-danger" style="width: 140px; height: 100px;" {{ (in_array($details->status, ['In Progress', 'Completed'])) ? 'disabled' : '' }} data-name="{{ $details->id }}" data-qty="{{ number_format($details->qty_to_manufacture) }}" data-item="{{ $production_item }}" id="production-actions-remove-btn" {{ $no_operator }} {{ ($details->qa_inspection_status != 'Pending') ? 'disabled' : '' }}>
                    <i class="now-ui-icons ui-1_simple-remove" style="font-size: 30pt;"></i><br><span style="font-size: 10pt;">Remove</span>
                  </button>
                </td>
              </tr>
            </table>
          </div>
        
        </div>

  @else
  <center>
  <i class="now-ui-icons travel_info" style="font-size: 40pt; margin-bottom: 10px;"></i></center>
  <p class="text-center">Production Order is <b>UNASSIGNED</b>.</p>
  <p class="text-center">Please accept the task first in <u>Production Queue.</u></p>
  <center>
  <button type="button" class="btn btn-secondary" data-dismiss="modal">OK</button></center>
  @endif --}}