<div class="table-responsive min-vh-100">
  <table class="table table-striped">
    <col style="width: 9%;">
    <col style="width: 38%;">
    <col style="width: 8%;">
    <col style="width: 19%;">
    <col style="width: 9%;">
    <col style="width: 10%;">
    <col style="width: 8%;">
    <thead class="text-primary" style="font-size: 7pt;">
      <th class="text-center"><b>Prod. No.</b></th>
      <th class="text-center"><b>Item Code</b></th>
      <th class="text-center"><b>Qty</b></th>
      <th class="text-center"><b>Customer</b></th>
      <th class="text-center"><b>Delivery Date</b></th>
      <th class="text-center"><b>Status</b></th>
      <th class="text-center"><b>Actions</b></th>
    </thead>
    <tbody style="font-size: 9pt;">
      @forelse($production_orders as $r)
      <tr>
        <td class="text-center">
          <a href="#" data-jtno="{{ $r['production_order'] }}" class="prod-details-btn"><i class="now-ui-icons ui-1_zoom-bold"></i> {{ $r['production_order'] }}</a>
        </td>
        <td class="text-left"><b>{{ $r['item_code'] }}</b><br>{!! $r['description'] !!} <span class="d-block mt-2 font-italic" style="font-size: 8pt;"><b>Created by:</b> {{ $r['owner'] }} - {{ $r['created_at'] }}</span></td>
        <td class="text-center">
          <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ number_format($r['qty_to_manufacture']) }}</span>
          <span class="d-block">{{ $r['stock_uom'] }}</span>
        </td>
        <td class="text-center">{{ $r['reference_no'] }}<br>{{ $r['customer'] }}</td>
        <td class="text-center">@if($r['delivery_date']){{ date('M-d-Y', strtotime($r['delivery_date'])) }}@endif</td>
        <td class="text-center">
          @php
              if($r['status'] == 'Material For Issue'){
                $status_badge = 'danger';
              }elseif ($r['status'] == 'Unknown Status') {
                $status_badge = 'secondary';
              }else{
                $status_badge = 'success';
              }
          @endphp
          <span class="badge badge-{{ $status_badge }}" style="font-size: 10pt;">{{ $r['status'] }}</span>
          @if($r['is_scheduled'] && $r['planned_start_date'])<span class="d-block" style="font-size: 10pt;">{{ date('M-d-Y', strtotime($r['planned_start_date'])) }}</span>@endif
        </td>
        <td class="text-center">
          <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Action
            </button>
              @php
                if($r['parent_item_code'] == $r['sub_parent_item_code'] and $r['item_code'] == $r['sub_parent_item_code']){
                  $resched_btn="";
                }else{
                  $resched_btn="none";
                }
              @endphp
            <div class="dropdown-menu">
              <a class="dropdown-item resched-deli-btn" href="#" data-production-order="{{ $r['production_order'] }}" style="display:{{ $resched_btn }};">Reschedule Delivery Date</a>
              <a class="dropdown-item create-ste-btn" href="#" data-production-order="{{ $r['production_order'] }}">View Materials</a>
              <a class="dropdown-item create-feedback-btn" href="#" data-production-order="{{ $r['production_order'] }}">Feedback</a>
              <a class="dropdown-item  view-bom-details-btn" href="#" data-bom="{{ $r['bom_no'] }}" data-production-order="{{ $r['production_order'] }}" data-operationid="{{ $r['operation_id'] }}">Update Process</a>
              <a class="dropdown-item cancel-production-btn" href="#"data-production-order="{{ $r['production_order'] }}">Cancel Production</a>
            </div>
          </div>
        </td>
      </tr>
      @empty
      <tr>
        <td class="text-center" colspan="7">No Record(s) found.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
  </div>
  
  <center>
    <div class="col-md-12 text-center custom-production-pagination" data-status="Not Started" data-div="#not-started-div">
     {{ $q->links() }}
    </div>
  </center>
