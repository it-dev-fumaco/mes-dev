<div class="row">
  <div class="col-md-6 offset-md-1" style="margin-top: -52px;">
    <table class="w-100 mt-2 p-0" id="filter-form">
      <col style="width: 40%;">
      <col style="width: 25%;">
      <col style="width: 25%;">
      <col style="width: 10%;">
      <tr>
        <td>
          <div class="form-group mb-0 mr-1">
            <select class="form-control select-custom" id="customer-filter">
              <option value="all">Select Customer</option>
              @foreach ($filters['customers'] as $i => $customer)
              <option value="{{ $customer }}">{{ $customer }}</option>
              @endforeach
            </select>
          </div>
        </td>
        <td>
          <div class="form-group mb-0 mr-1">
            <select class="form-control select-custom" id="reference-filter">
              <option value="all">Select Reference No.</option>
              @foreach ($filters['reference_nos'] as $i => $reference)
              <option value="{{ $reference }}">{{ $reference }}</option>
              @endforeach
            </select>
          </div>
        </td>
        <td>
          <div class="form-group mb-0 mr-1">
            <select class="form-control select-custom rounded-0" id="parent-item-filter">
              <option value="all">Select Parent Item</option>
              @foreach ($filters['parent_item_codes'] as $i => $parent_item)
              <option value="{{ $parent_item }}">{{ $parent_item }}</option>
              @endforeach
            </select>
          </div>
        </td>
        <td class="pl-2">
          <button class="btn btn-secondary btn-mini p-2 btn-block m-0" id="clear-kanban-filters">Clear</button>
        </td>
      </tr>
    </table>
  </div>
</div>
<table class="table table-striped text-center" id="monitoring-table">
    <col style="width: 7%;">
    <col style="width: 7%;">
    <col style="width: 7%;">
    <col style="width: 16%;">
    <col style="width: 26%;">
    <col style="width: 6%;">
    <col style="width: 6%;">
    <col style="width: 6%;">
    <col style="width: 6%;">
    <col style="width: 6%;">
    <col style="width: 9%;">
    <thead class="text-primary" style="font-size: 7pt;">
      <th class="text-center font-weight-bold">Prod. Order</th>
      <th class="text-center font-weight-bold">Planned Start</th>
      <th class="text-center font-weight-bold">Delivery Date</th>
      <th class="text-center font-weight-bold">Customer</th>
      <th class="text-center font-weight-bold">Item Description</th>
      <th class="text-center font-weight-bold">Qty</th>
      <th class="text-center font-weight-bold">Produced</th>
      <th class="text-center font-weight-bold">Feedbacked</th>
      <th class="text-center font-weight-bold">Balance</th>
      <th class="text-center font-weight-bold">Reject</th>
      <th class="text-center font-weight-bold">Action</th>
    </thead>
    <tbody style="font-size: 9pt;">
      @forelse($production_orders as $row)
      <tr class="tbl-row" data-customer="{{ $row['customer'] }}" data-reference-no="{{ $row['reference_no'] }}" data-parent-item="{{ $row['parent_item_code'] }}">
        <td class="text-center p-0">
          <a href="#" data-jtno="{{ $row['production_order'] }}" class="prod-details-btn font-weight-bold text-dark d-block">{{ $row['production_order'] }}</a>
          <span class="badge {{ ($row['is_backlog']) ? 'badge-danger' : 'd-none' }}" style="font-size: 9pt;">Backlog</span>
        </td>
        <td class="text-center p-0">
          <span class="d-block font-weight-bold">{{ date('M-d-Y', strtotime($row['planned_start_date'])) }}</span>
          <span class="d-block font-italic" style="font-size: 8pt;">{{ $row['actual_start_date'] }}</span>
        </td>
        <td class="text-center p-0 font-weight-bold">{{ date('M-d-Y', strtotime($row['delivery_date'])) }}</td>
        <td class="text-center">
          <span class="d-block font-weight-bold">{{ $row['reference_no'] }}</span>
          <span class="d-block">{{ $row['customer'] }}</span>
        </td>
        <td class="text-justify">
          <span class="font-weight-bold">{{ $row['item_code'] }}</span> - {{$row['description'] }}
          @if ($row['notes'])
          <span class="d-block"><b>Notes:</b> {{ $row['notes'] }}</span>
          @endif
        </td>
        <td class="text-center">
          <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ $row['qty_to_manufacture'] }}</span>
          <span class="d-block">{{ $row['stock_uom'] }}</span>
        </td>
        <td class="text-center">
          <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ $row['produced_qty'] }}</span>
          <span class="d-block">{{ $row['stock_uom'] }}</span>
        </td>
        <td class="text-center">
          <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ $row['feedback_qty'] }}</span>
          <span class="d-block">{{ $row['stock_uom'] }}</span>
        </td>
        <td class="text-center">
          <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ $row['balance_qty'] }}</span>
          <span class="d-block">{{ $row['stock_uom'] }}</span>
        </td>
        <td class="text-center">
          <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ $row['rejects'] }}</span>
          <span class="d-block">{{ $row['stock_uom'] }}</span>
        </td>
        <td class="text-center p-0">
          <div class="btn-group m-0">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action </button>
            <div class="dropdown-menu">
              @if ($row['parent_item_code'] == $row['item_code'])
              <a class="dropdown-item resched-deli-btn" href="#" data-production-order="{{ $row['production_order'] }}">Reschedule Delivery Date</a>
              @endif
              <a class="dropdown-item create-feedback-btn" href="#" data-production-order="{{ $row['production_order'] }}">Create Feedback</a>
              <a class="dropdown-item addnotes" href="#" data-production-order="{{ $row['production_order'] }}" data-notes="{{ $row['notes'] }}">Add Notes</a>
              <a class="dropdown-item view-process-qty-btn" href="#" data-production-order="{{ $row['production_order'] }}">View Process</a>
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