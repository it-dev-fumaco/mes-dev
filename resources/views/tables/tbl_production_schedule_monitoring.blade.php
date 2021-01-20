<div class="row">
  <div class="col-md-6 offset-md-2 pl-5" style="margin-top: -52px;">
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
    <col style="width: 13%;">
    <col style="width: 24%;">
    <col style="width: 7%;">
    <col style="width: 7%;">
    <col style="width: 7%;">
    <col style="width: 7%;">
    <col style="width: 7%;">
    <col style="width: 10%;">
    <col style="width: 6%;">
    <thead class="text-primary" style="font-size: 7pt;">
      <th class="text-center font-weight-bold"><b>Prod. Order</b></th>
      <th class="text-center font-weight-bold"><b>Planned Start</b></th>
      <th class="text-center font-weight-bold"><b>Customer</b></th>
      <th class="text-center font-weight-bold"><b>Item Description</b></th>
      <th class="text-center font-weight-bold"><b>Qty</b></th>
      <th class="text-center font-weight-bold"><b>Produced</b></th>
      <th class="text-center font-weight-bold"><b>Feedbacked</b></th>
      <th class="text-center font-weight-bold"><b>Balance</b></th>
      <th class="text-center font-weight-bold"><b>Reject</b></th>
      <th class="text-center font-weight-bold"><b>Notes</b></th>
      <th class="text-center font-weight-bold"><b>Action</b></th>
    </thead>
    <tbody style="font-size: 9pt;">
      @forelse($production_orders as $row)
      <tr class="tbl-row" data-customer="{{ $row['customer'] }}" data-reference-no="{{ $row['reference_no'] }}" data-parent-item="{{ $row['parent_item_code'] }}">
        <td class="text-center p-0">
          <a href="#" data-jtno="{{ $row['production_order'] }}" class="prod-details-btn font-weight-bold text-dark">{{ $row['production_order'] }}</a>
          <span class="badge {{ ($row['is_backlog']) ? 'badge-danger' : 'd-none' }}" style="font-size: 9pt;">Backlog</span>
        </td>
        <td class="text-center p-0 font-weight-bold">{{ date('M-d-Y', strtotime($row['planned_start_date'])) }}
        </td>
        <td class="text-center">
          <span class="d-block font-weight-bold">{{ $row['reference_no'] }}</span>
          <span class="d-block">{{ $row['customer'] }}</span>
        </td>
        <td class="text-justify">
          <span class="font-weight-bold">{{ $row['item_code'] }}</span> - {{$row['description'] }}
        </td>
        <td class="text-center">
          <span class="d-block font-weight-bold">{{ $row['qty_to_manufacture'] }}</span>
          <span class="d-block">{{ $row['stock_uom'] }}</span>
        </td>
        <td class="text-center">
          <span class="d-block font-weight-bold">{{ $row['produced_qty'] }}</span>
          <span class="d-block">{{ $row['stock_uom'] }}</span>
        </td>
        <td class="text-center">
          <span class="d-block font-weight-bold">{{ $row['feedback_qty'] }}</span>
          <span class="d-block">{{ $row['stock_uom'] }}</span>
        </td>
        <td class="text-center">
          <span class="d-block font-weight-bold">{{ $row['balance_qty'] }}</span>
          <span class="d-block">{{ $row['stock_uom'] }}</span>
        </td>
        <td class="text-center">
          <span class="d-block font-weight-bold">{{ $row['rejects'] }}</span>
          <span class="d-block">{{ $row['stock_uom'] }}</span>
        </td>
        <td class="text-center">{{ $row['notes'] }}</td>
        <td class="text-center p-0">
          <div class="btn-group m-0">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action </button>
            <div class="dropdown-menu">
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