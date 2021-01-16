<div class="table-responsive vh-100">
    <table class="table table-striped">
        <col style="width: 9%;">
        <col style="width: 22%;">
        <col style="width: 5%;">
        <col style="width: 5%;">
        <col style="width: 5%;">
        <col style="width: 8%;">
        <col style="width: 8%;">
        <col style="width: 14%;">
        <col style="width: 8%;">
        <col style="width: 8%;">
        <col style="width: 8%;">
       <thead class="text-primary" style="font-size: 7pt;">
        <th class="text-center"><b>Prod. No.</b></th>
        <th class="text-center"><b>Item Code</b></th>
        <th class="text-center"><b>Qty</b></th>
        <th class="text-center"><b>Completed</b></th>
        <th class="text-center"><b>Feedbacked</b></th>
        <th class="text-center"><b>Planned Start</b></th>
        <th class="text-center"><b>Actual Start</b></th>
        <th class="text-center"><b>Customer</b></th>
        <th class="text-center"><b>Delivery Date</b></th>
        <th class="text-center"><b>Status</b></th>
        <th class="text-center"><b>Actions</b></th>
      </thead>
      <tbody style="font-size: 9pt;">
        @forelse($production_orders as $r)
              @php
                if($r['parent_item_code'] == $r['sub_parent_item_code'] and $r['item_code'] == $r['sub_parent_item_code']){
                  $resched_btn="";
                }else{
                $resched_btn="none";
                }
              @endphp
        <tr>
          <td class="text-center">
            <a href="#" data-jtno="{{ $r['production_order'] }}" class="prod-details-btn"><i class="now-ui-icons ui-1_zoom-bold"></i> {{ $r['production_order'] }}</a>
          </td>
          <td class="text-left"><b>{{ $r['item_code'] }}</b><br>{!! $r['description'] !!}<span class="d-block mt-2 font-italic" style="font-size: 8pt;"><b>Created by:</b> {{ $r['owner'] }} - {{ $r['created_at'] }}</span></td>
          <td class="text-center">
            <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ number_format($r['qty_to_manufacture']) }}</span>
            <span class="d-block">{{ $r['stock_uom'] }}</span>
          </td>
          <td class="text-center">
            <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ number_format($r['produced_qty']) }}</span>
            <span class="d-block">{{ $r['stock_uom'] }}</span>
          </td>
          <td class="text-center">
            <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ number_format($r['feedback_qty']) }}</span>
            <span class="d-block">{{ $r['stock_uom'] }}</span>
          </td>
          <td class="text-center">{{ $r['planned_start_date'] }}</td>
          <td class="text-center">{{ $r['actual_start_date'] }}</td>
          <td class="text-center">{{ $r['reference_no'] }}<br>{{ $r['customer'] }}</td>
          <td class="text-center">@if($r['delivery_date']){{ date('M-d-Y', strtotime($r['delivery_date'])) }}@endif</td>
          <td class="text-center">
              <span class="badge tab-heading--{{ ($r['status'] == 'Material For Issue') ? 'reddish' : 'orange' }}" style="font-size: 10pt;">{{ $r['status'] }}</span>
        </td>
          <td class="text-center">
            <div class="btn-group">
              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Action
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item resched-deli-btn" href="#" data-production-order="{{ $r['production_order'] }}" style="display:{{ $resched_btn }};">Reschedule Delivery Date</a>
                <a class="dropdown-item create-feedback-btn" href="#" data-production-order="{{ $r['production_order'] }}" data-completed-qty="{{ ($r['produced_qty']) - ($r['feedback_qty']) }}" data-target-warehouse="{{ $r['target_warehouse'] }}" data-operation="{{ $r['operation_id'] }}" data-max="{{ $r['qty_to_manufacture'] - $r['feedback_qty'] }}">Create Feedback</a>
                <a class="dropdown-item  view-bom-details-btn" href="#" data-bom="{{ $r['bom_no'] }}" data-production-order="{{ $r['production_order'] }}">Update Process</a>
                <a class="dropdown-item cancel-production-btn" href="#"data-production-order="{{ $r['production_order'] }}">Cancel Production</a>
                <a class="dropdown-item create-ste-btn" href="#" data-production-order="{{ $r['production_order'] }}">View Materials</a>
                <a class="dropdown-item prod-reset-btn" href="#" data-production-order="{{ $r['production_order'] }}">Reset</a>
              </div>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td class="text-center" colspan="11">No Record(s) found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
    </div>
    
    <center>
      <div class="col-md-12 text-center custom-production-pagination" data-status="In Progress" data-div="in-progress-div">
       {{ $q->links() }}
      </div>
    </center>