<div class="table-responsive vh-100" style="font-size: 9pt;">
  <table class="table table-striped">
    <col style="width: 7%;">
    <col style="width: 18%;">
    <col style="width: 5%;">
    <col style="width: 5%;">
    <col style="width: 5%;">
    <col style="width: 12%;">
    <col style="width: 9%;">
    <col style="width: 9%;">
    <col style="width: 7%;">
    <col style="width: 7%;">
    <col style="width: 10%;">
    <col style="width: 16%;">
    <thead class="text-primary" style="font-size: 7pt;">
      <th class="text-center"><b>Prod. No.</b></th>
      <th class="text-center"><b>Item Code</b></th>
      <th class="text-center"><b>Qty</b></th>
      <th class="text-center"><b>Completed</b></th>
      <th class="text-center"><b>Feedbacked</b></th>
      <th class="text-center"><b>Customer</b></th>
      <th class="text-center"><b>Actual Start Date</b></th>
      <th class="text-center"><b>Actual End Date</b></th>
      <th class="text-center"><b>Duration</b></th>
      <th class="text-center"><b>Delivery Date</b></th>
      <th class="text-center"><b>Target Warehouse</b></th>
      <th class="text-center"><b>Actions</b></th>
    </thead>
    <tbody>
      @forelse($production_order_list as $r)
        @php
          if($r['parent_item_code'] == $r['sub_parent_item_code'] and $r['item_code'] == $r['sub_parent_item_code']){
            $resched_btn="";
          }else{
          $resched_btn="none";
          }
        @endphp
      <tr>
        <td class="text-center" style="padding: 3px;">
          <a href="#" data-jtno="{{ $r['name'] }}" class="prod-details-btn"><i class="now-ui-icons ui-1_zoom-bold"></i> {{ $r['name'] }}</a>
        </td>
        <td class="text-left" style="padding: 3px;"><b>{{ $r['item_code'] }}</b><br>{!! $r['description'] !!}<span class="d-block mt-2 font-italic" style="font-size: 8pt;"><b>Created by:</b> {{ $r['owner'] }} - {{ $r['created_at'] }}</span></td>
        <td class="text-center" style="padding: 3px;">
          <span style="font-size: 12pt; display: block; font-weight: bold;">{{ number_format($r['qty']) }}</span>
          <span>{{ $r['stock_uom'] }}</span>
        </td>
        <td class="text-center" style="padding: 3px;">
          <span style="font-size: 12pt; display: block; font-weight: bold;">{{ number_format($r['produced_qty']) }}</span>
          <span>{{ $r['stock_uom'] }}</span>
        </td>
        <td class="text-center" style="padding: 3px;">
          <span style="font-size: 12pt; display: block; font-weight: bold;">{{ number_format($r['feedback_qty']) }}</span>
          <span>{{ $r['stock_uom'] }}</span>
        </td>
        <td class="text-center" style="padding: 3px;">{{ $r['sales_order_no']}}{{ $r['material_request']}}<br>{{ $r['customer'] }}</td>
        <td class="text-center" style="padding: 3px;">
          <span class="d-block">{{ $r['actual_start_date'] }}</span>
          <span class="d-block font-italic" style="font-size: 7pt;">{{ $r['planned_start_date'] }}</span>
        </td>
        <td class="text-center" style="padding: 3px;">{{ $r['actual_end_date'] }}</td>
        <td class="text-center" style="padding: 3px;">{{ $r['duration'] }}</td>
        <td class="text-center" style="padding: 3px;">@if($r['delivery_date']){{date('M-d-Y', strtotime($r['delivery_date']))}}@endif</td>
        <td class="text-center font-weight-bold" style="padding: 3px;">{{ $r['target_warehouse'] }}<br>
          <span class="badge tab-heading--{{ ($r['status'] == 'Material For Issue') ? 'reddish' : 'teal' }} text-white" style="font-size: 9pt;">{{ $r['status'] }}</span>
        </td>
        <td class="text-center" style="padding: 3px;">
          <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Action
            </button>
            <div class="dropdown-menu">
              <a class="dropdown-item resched-deli-btn" href="#" data-production-order="{{ $r['production_order'] }}" style="display:{{ $resched_btn }};">Reschedule Delivery Date</a>
              @if($r['status'] == 'Material For Issue')
              @if($r['status'] == 'For Feedback')
              <a class="dropdown-item view-bom-details-btn" href="#" data-bom="{{ $r['bom'] }}" data-production-order="{{ $r['name'] }}">Update Process</a>
              @endif
              @if($r['status'] == 'Feedbacked')
              <a class="dropdown-item" href="#"><i class="now-ui-icons ui-1_check"></i> {{$r['ste_manufacture']}}</a>
              <a class="dropdown-item print-transfer-slip-btn" data-production-order="{{ $r['name'] }}" href="#">Print Transfer Slip</a>
              @else
              @endif
              @if($r['status'] == 'Partially Feedbacked')
              <a class="dropdown-item print-transfer-slip-btn" data-production-order="{{ $r['name'] }}" href="#">Print Transfer Slip</a>
              @endif
              <a class="dropdown-item create-ste-btn" href="#" data-production-order="{{ $r['name'] }}" data-item-code="{{ $r['item_code'] }}" data-qty="{{ number_format($r['qty']) }}" data-uom="{{ $r['stock_uom'] }}">Create Stock Entry</a>
              @else
              <a class="dropdown-item create-feedback-btn" href="#" data-production-order="{{ $r['name'] }}" data-completed-qty="{{ ($r['produced_qty']) - ($r['feedback_qty']) }}" data-target-warehouse="{{ $r['target_warehouse'] }}" data-operation="{{ $r['operation_id'] }}" data-max="{{ $r['qty'] - $r['feedback_qty'] }}">Create Feedback</a>
              @endif
              <a class="dropdown-item  view-bom-details-btn" href="#" data-bom="{{ $r['bom_no'] }}" data-production-order="{{ $r['name'] }}">Update Process</a>
              <a class="dropdown-item create-ste-btn" href="#" data-production-order="{{ $r['name'] }}">View Materials</a>
              <a class="dropdown-item prod-reset-btn" href="#" data-production-order="{{ $r['production_order'] }}">Reset</a>

            </div>
          </div>
        </td>
      </tr>
      @empty
      <tr>
        <td class="text-center" colspan="13">No Record(s) found.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

<center>
  <div class="col-md-12 text-center for-feedback-production-pagination custom-production-pagination" data-status="Awaiting Feedback" data-div="#awaiting-feedback-div">
    {{ $q->links() }}
  </div>
</center>