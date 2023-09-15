@php
  $result = [];
  $qty_to_manufacture = $feedback_qty = 0;
  $badge = 'secondary';
  if (in_array($item['item_code'], array_keys($production_per_item))) {
    $result = $production_per_item[$item['item_code']];
    $qty_to_manufacture = collect($result)->sum('qty_to_manufacture');
    $feedback_qty = collect($result)->sum('feedback_qty');
    $produced_qty = collect($result)->sum('produced_qty');

    $status = array_key_exists($item['item_code'], $per_item_idle_production_orders) ? 'Idle' : $result[0]['status'];
    if ($produced_qty > $feedback_qty && $status != 'In Progress') {
      $status = 'Ready for Feedback';
    }

    if ($produced_qty <= $feedback_qty && $feedback_qty > 0 && $qty_to_manufacture <= $feedback_qty) {
      $status = 'Feedbacked';
    }

    switch($status){
    case 'In Progress':
      $badge = 'warning';
      break;
    case 'Feedbacked':
    case 'Partially Feedbacked':
      $badge = 'success';
      break;
    case 'Completed':
    case 'Ready for Feedback':
    case 'For Partial Feedback':
      $badge = 'info';
      break;
    case 'Cancelled':
    case 'Closed':
      $badge = 'danger';
      break;
    default:
      $badge = 'secondary';
      break;
    }
  }
@endphp 

<span class="hvrlink">
  <a class="aclass mb-4 border border-secondary bg-light text-decoration-none" href="#">
    <span style="font-size: 13px;" class="border-bottom d-block font-weight-bold">{{ $item['item_code'] }} {{ $item['parts_category'] ? '['.$item['parts_category'].']' : null }}</span>
    <p class="border-bottom pb-1 pt-1 m-0" style="font-size: 13px;">
      @if ($result)
      <span class="badge badge-{{ $badge }}">{{ $status }}</span>
      @else
      <small class="d-block text-center text-uppercase text-muted">No Production Order</small>
      @endif
    </p>
    <table class="w-100 m-0" style="font-size: 10px;">
      <tr>
        <td>
          <span class="d-block font-weight-bold" style="font-size: 12px;">{{ $qty_to_manufacture }}</span>
          <span class="d-block">Qty to Produce</span>
        </td>
        <td>
          <span class="d-block font-weight-bold" style="font-size: 12px;">{{ $feedback_qty }}</span>
          <span class="d-block">Feedbacked Qty</span>
        </td>
      </tr>
    </table>
  </a>
</span>

<div class="details-pane">
  <h6 class="title m-1">{{ $item['item_code'] }} {{ $item['parts_category'] ? '['.$item['parts_category'].']' : null }}</h6>
  <p class="desc m-1 pb-2" style="font-size: 11px;">{{ strip_tags($item['description']) }}</p>
  @forelse ($result as $res)
  @php
    $workstations = [];
    $badge = 'secondary';
    $qty_to_manufacture = $feedback_qty = 0;
    if (in_array($res['production_order'], array_keys($production_order_workstations))) {
      $workstations = $production_order_workstations[$res['production_order']];
      $qty_to_manufacture = collect($result)->sum('qty_to_manufacture');
      $feedback_qty = collect($result)->sum('feedback_qty');
      $produced_qty = collect($result)->sum('produced_qty');

      $status = array_key_exists($res['production_order'], $idle_production_orders) ? 'Idle' : $result[0]['status'];
      if ($produced_qty > $feedback_qty && $status != 'In Progress') {
        $status = 'Ready for Feedback';
      }

      if ($produced_qty <= $feedback_qty && $feedback_qty > 0 && $qty_to_manufacture <= $feedback_qty) {
        $status = 'Feedbacked';
      }

      switch($status){
      case 'In Progress':
        $badge = 'warning';
        break;
      case 'Feedbacked':
      case 'Partially Feedbacked':
        $badge = 'success';
        break;
      case 'Completed':
      case 'Ready for Feedback':
      case 'For Partial Feedback':
        $badge = 'info';
        break;
      case 'Cancelled':
      case 'Closed':
        $badge = 'danger';
        break;
      default:
        $badge = 'secondary';
        break;
      }
    }
  @endphp 
  <div class="row border bg-light rounded m-1 p-0">
    <div class="col">
      <p class="border-bottom pb-1 pt-1 text-center mb-2">
        <span class="d-block font-weight-bold">{{ $res['production_order'] }}</span>
      </p>
      <ul class="list-group list-group-flush" style="font-size: 11px; list-style-type: none;">
        @foreach ($workstations as $workstation => $statuses)
        @php
            $workstation_status = 'Pending';
            if (in_array('Completed', $statuses)) {
              $workstation_status = 'Completed';
            }

            if (in_array('In Progress', $statuses)) {
              $workstation_status = 'In Progress';
            }
        @endphp
        <li class="pb-1 text-uppercase">
          @if ($workstation_status == 'Completed')
          <span class="d-block"><i class="now-ui-icons ui-1_check text-success d-inline-block mr-2"></i> {{ $workstation }}</span>
          @elseif ($workstation_status == 'Pending')
          <span class="d-block text-muted"><i class="now-ui-icons ui-1_simple-delete d-inline-block mr-2"></i> {{ $workstation }}</span>
          @else
          <span class="d-block font-weight-bold"><i class="now-ui-icons media-1_button-play text-warning d-inline-block mr-2"></i> {{ $workstation }}</span>
          @endif
        </li>
        @endforeach
      </ul>
    </div>
    <div class="col">
      <div class="d-block font-weight-bold text-center border-bottom pt-1 pb-1">
        <span class="badge badge-{{ $badge }}">{{ $status }}</span>
      </div>
      <dl class="row mt-2 mb-1">
        <dt class="col-9 pb-1 m-0" style="font-size: 10px;">Qty to Manufacture</dt>
        <dd class="col-3 pb-1 m-0 text-center" style="font-size: 12px;">{{ $res['qty_to_manufacture'] }}</dd>
        <dt class="col-9 pb-1 m-0" style="font-size: 10px;">Produced Qty</dt>
        <dd class="col-3 pb-1 m-0 text-center" style="font-size: 12px;">{{ $res['produced_qty'] }}</dd>
        <dt class="col-9 pb-1 m-0" style="font-size: 10px;">Feedbacked Qty</dt>
        <dd class="col-3 pb-1 m-0 text-center" style="font-size: 12px;">{{ $res['feedback_qty'] }}</dd>
      </dl>
    </div>
    <div class="col-12 border-top">
      <dl class="row mt-1 mb-1">
        <dt class="col-6 pb-1 m-0" style="font-size: 10px;">BOM No.:</dt>
        <dd class="col-6 pb-1 m-0" style="font-size: 10px;">{{ $res['bom_no'] }}</dd>
        <dt class="col-6 pb-1 m-0" style="font-size: 10px;">Planned Start Date:</dt>
        <dd class="col-6 pb-1 m-0" style="font-size: 10px;">{{ $res['planned_start_date'] }}</dd>
        @if ($res['actual_start_date'])
        <dt class="col-6 pb-1 m-0" style="font-size: 10px;">Actual Start Date:</dt>
        <dd class="col-6 pb-1 m-0" style="font-size: 10px;">{{ $res['actual_start_date'] }}</dd>
        @endif
        @if ($res['actual_end_date'])
        <dt class="col-6 pb-1 m-0" style="font-size: 10px;">Actual End Date:</dt>
        <dd class="col-6 pb-1 m-0" style="font-size: 10px;">{{ $res['actual_end_date'] }}</dd>
        @endif
        @if ($res['actual_end_date'] && $res['actual_start_date'])
        <dt class="col-6 pb-1 m-0" style="font-size: 10px;">Duration:</dt>
        <dd class="col-6 pb-1 m-0" style="font-size: 10px;">{{ $res['duration'] }}</dd>
        @endif
      </dl>
    </div>
  </div>
  @empty
  <span class="d-block text-uppercase text-center text-muted mb-2 mt-1">No Production Order</span>
  @endforelse
</div>