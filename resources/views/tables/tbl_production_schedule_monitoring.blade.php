<div class="container-fluid">
  @php
      $qty_to_manufacture = [];
  @endphp
  @forelse ($planned_start_dates as $date)
    @php
        $production_order = isset($production_orders[$date]) ? $production_orders[$date] : [];
        $qty_to_manufacture[] = collect($production_order)->sum('qty_to_manufacture');
    @endphp
    <div class="card card-primary">
      <div class="card-body">
        <div class="row p-0">
          <div class="col-2 text-center p-3">
            <div class="col-10 text-left">
              <span style="font-size: 17pt;">{{ Carbon\Carbon::parse($date)->format('M d, Y') }}</span> <br>
              <span>{{ Carbon\Carbon::parse($date)->format('l') }}</span> <br>
              @if (Carbon\Carbon::parse($date)->endOfDay() < Carbon\Carbon::now()->startOfDay())
                <span style="color: #E42223">BACKLOG</span>
              @else
                <span style="color: #1F8C04">TODAY</span>
              @endif
            </div>
          </div>
          <div class="col-10 p-0">
            <table class="table table-striped text-center" id="monitoring-table">
              <col style="width: 12%"><!-- Actual Start Date -->
              <col style="width: 20%"><!-- Prod. Order -->
              <col style="width: 30%"><!-- Item Description -->
              <col style="width: 12%"><!-- Delivery Date -->
              <col style="width: 8%"><!-- Qty -->
              <col style="width: 8%"><!-- Action -->
              <tbody style="font-size: 9pt;">
                @forelse($production_order as $row)
                @php
                  if($row['actual_start_date']){
                    switch ($row['status']) {
                      case "Cancelled":
                      case "Material For Issue":
                        $badge_color ="danger";
                        break;
                      case "Material Issued":
                        $badge_color ="primary";
                        break;
                      case "Ready For Feedback":
                        $badge_color ="info";
                        break;
                      case "Partially Feedbacked":
                      case "Feedbacked":
                        $badge_color ="success";
                        break;
                      case "Closed":
                        $badge_color = 'secondary';
                        break;
                      default:
                        $badge_color ="warning";
                        break;
                    }
                    $status = $row['status'];
                  }else{
                    $badge_color ="danger";
                    $status = 'Not Started';
                  }
                @endphp
                <tr class="tbl-row" data-customer="{{ $row['customer'] }}" data-reference-no="{{ $row['reference_no'] }}" data-parent-item="{{ $row['parent_item_code'] }}">
                  <td class="p-0">
                    <div class="container-fluid row text-center p-1">
                      <div class="col-12 d-flex flex-row justify-content-center align-items-center">
                        <span class="badge badge-{{ $badge_color }}" style="font-size: 8pt;">{{ $status }}</span>
                      </div>
                    </div>
                  </td>
                  <td class="text-center p-0">
                    <a href="#" data-jtno="{{ $row['production_order'] }}" class="prod-details-btn font-weight-bold text-dark d-block">{{ $row['production_order'] }}</a>
                    <span class="d-block">{{ $row['customer'] }}</span>
                  </td>
                  <td class="text-justify">
                    <span class="font-weight-bold">{{ $row['item_code'] }}</span> - {{$row['description'] }}
                    @if ($row['notes'])
                    <span class="d-block"><b>Notes:</b> {{ $row['notes'] }}</span>
                    @endif
                  </td>
                  <td class="text-center p-0 font-weight-bold">SHIP BY: {{ date('m/d/Y', strtotime($row['delivery_date'])) }}</td>
                  <td class="text-center">
                    <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ $row['qty_to_manufacture'] }}</span>
                    <span class="d-block">{{ $row['stock_uom'] }}</span>
                  </td>
                  <td class="text-center p-0">
                    <div class="btn-group m-0">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item create-ste-btn" href="#" data-production-order="{{ $row['production_order'] }}" data-item-code="{{ $row['item_code'] }}">View Materials</a>
                        @if ($row['parent_item_code'] == $row['item_code'])
                        <a class="dropdown-item resched-deli-btn" href="#" data-production-order="{{ $row['production_order'] }}">Reschedule Delivery Date</a>
                        @endif
                        <a class="dropdown-item create-feedback-btn" href="#" data-production-order="{{ $row['production_order'] }}">Create Feedback</a>
                        <a class="dropdown-item addnotes" href="#" data-production-order="{{ $row['production_order'] }}" data-notes="{{ $row['notes'] }}">Add Notes</a>
                        <a class="dropdown-item view-process-qty-btn" href="#" data-production-order="{{ $row['production_order'] }}">View Process</a>
                        @if (!in_array($row['status'], ['Cancelled', 'Feedbacked']))
                          @if ($row['status'] == 'Closed')
                            <a class="dropdown-item re-open-production-btn" href="#"data-production-order="{{ $row['production_order'] }}">Re-open Production</a>
                          @else
                            <a class="dropdown-item close-production-btn" href="#"data-production-order="{{ $row['production_order'] }}">Close Production</a>
                          @endif
                        @endif
                      </div>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="12" class="text-center">No Scheduled Production Order(s) found</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  @empty
      
  @endforelse
</div>

  <style>
    .sticky-header th{
      position: sticky;
      top: 0;
      background-color: #fff !important;
      z-index: 10;
    }
  </style>

<script>
  $(document).ready(function (){
    $('#production-order-count').text('{{ count($production_orders) }}');
    $('#qty-to-manufacture-count').text('{{ number_format(collect($qty_to_manufacture)->sum()) }}');
    $('#backlogged-production-order-count').text('{{ $backlogs }}');
  });
</script>