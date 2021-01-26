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
<table class="table custom-table-striped text-center" id="monitoring-table">
  <col style="width: 8%;">
  <col style="width: 7%;">
  <col style="width: 7%;">
  <col style="width: 12%;">
  <col style="width: 12%;">
  <col style="width: 7%;">
  <col style="width: 7%;">
  <col style="width: 7%;">
  <col style="width: 7%;">
  <col style="width: 7%;">
  <col style="width: 7%;">
  <col style="width: 7%;">
  <col style="width: 6%;">
  <thead class="text-primary" style="font-size: 7pt;">
    <th class="text-center font-weight-bold">Prod. Order</th>
    <th class="text-center font-weight-bold">Planned Start</th>
    <th class="text-center font-weight-bold">Delivery Date</th>
    <th class="text-center font-weight-bold">Customer</th>
    <th class="text-center font-weight-bold">Item Description</th>
    <th class="text-center font-weight-bold">Qty</th>
    <th class="text-center font-weight-bold">Loading</th>
    <th class="text-center font-weight-bold">Unloading</th>
    <th class="text-center font-weight-bold">Produced</th>
    <th class="text-center font-weight-bold">Feedbacked</th>
    <th class="text-center font-weight-bold">Balance</th>
    <th class="text-center font-weight-bold">Reject</th>
    <th class="text-center font-weight-bold">Action</th>
  </thead>
  <tbody style="font-size: 9pt;">
    @forelse($data as $row)
    @php
      $painting_completed_qty = collect($row['job_ticket'])->min('completed_qty');
      $display_duration = ($painting_completed_qty > 0 && $row['qty'] == $painting_completed_qty) ? null : 'd-none';
      $rowspan = ($display_duration != 'd-none') ? 'rowspan="2"' : null;
    @endphp
    <tr class="tbl-row" data-customer="{{ $row['customer'] }}" data-reference-no="{{ $row['reference_no'] }}" data-parent-item="{{ $row['parent_item_code'] }}">
      <td class="text-center" {!! $rowspan !!}>
        <span class="badge badge-info mr-2" style="font-size: 9pt;">{{ $row['sequence']}}</span>
        <a href="#" data-jtno="{{ $row['production_order'] }}" class="prod-details-btn font-weight-bold text-dark d-block">{{ $row['production_order'] }}</a>
        <span class="badge {{ ($row['is_backlog']) ? 'badge-danger' : 'd-none' }}" style="font-size: 9pt;">Backlog</span>
      </td>
      <td class="text-center p-0 font-weight-bold" {!! $rowspan !!}>
        <span class="d-block">{{ date('M-d-Y', strtotime($row['planned_start_date'])) }}</span>
        <span class="d-block font-italic" style="font-size: 8pt;">{{ $row['actual_start_date'] }}</span>
      </td>
      <td class="text-center p-0 font-weight-bold" {!! $rowspan !!}>{{ date('M-d-Y', strtotime($row['delivery_date'])) }}</td>
      <td class="text-center" {!! $rowspan !!}>
        <span class="d-block font-weight-bold">{{ $row['reference_no'] }}</span>
        <span>{{ $row['customer'] }}</span>
      </td>
      <td class="text-center" {!! $rowspan !!}>
        <span class="font-weight-bold">{{ $row['item_code'] }}</span> - {{$row['item_description']}}
        @if($row['remarks'])
        <span class="d-block"><b>Notes:</b> {{ $row['remarks'] }}</span>
        @endif
      </td>
      <td class="text-center" {!! $rowspan !!}>
        <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ $row['qty'] }}</span>
        <span class="d-block">{{ $row['stock_uom'] }}</span>
      </td>
      @foreach($row['job_ticket'] as $rows)
        @php
        if($rows->status == "Completed"){
          $status="#2ecc71";
        }else if($rows->status == "In Progress"){
          $status="#f4d03f";
        }else{
          $status="#b2babb";
        }
        @endphp
        <td class="text-center text-white" style="background-color: {{$status}};">
          <span style="font-size: 9pt;">{{ $rows->status }}</span>
          <span class="d-block" style="font-size: 9pt;">( {{ $rows->completed_qty }} )</span>
        </td>
      @endforeach
      <td class="text-center" {!! $rowspan !!}>
        <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ $row['completed_qty'] }}</span>
        <span class="d-block">{{ $row['stock_uom'] }}</span>
      </td>
      <td class="text-center" {!! $rowspan !!}>
        <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ $row['feedback_qty'] }}</span>
        <span class="d-block">{{ $row['stock_uom'] }}</span>
      </td>
      <td class="text-center" {!! $rowspan !!}>
        <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ $row['balance_qty'] }}</span>
        <span class="d-block">{{ $row['stock_uom'] }}</span>
      </td>
      <td class="text-center" {!! $rowspan !!}>
        <span class="d-block font-weight-bold" style="font-size: 12pt;">{{ $row['reject'] }}</span>
        <span class="d-block">{{ $row['stock_uom'] }}</span>
      </td>
      <td {!! $rowspan !!}>
        <div class="btn-group">
          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
          <div class="dropdown-menu">
            @if ($row['parent_item_code'] == $row['item_code'])
            <a class="dropdown-item resched-deli-btn" href="#" data-production-order="{{ $row['production_order'] }}">Reschedule Delivery Date</a>
            @endif
            <a class="dropdown-item create-feedback-btn" href="#" data-production-order="{{ $row['production_order'] }}">Create Feedback</a>
            <a class="dropdown-item addnotes" href="#" data-production-order="{{ $row['production_order'] }}" data-notes="{{ $row['remarks'] }}">Add Notes</a>
            <a class="dropdown-item editcpt_qty" href="#" id="editcpt-qty-btn" data-prod="{{$row['production_order']}}" data-qty="{{ $row['qty'] }}">Edit</a>
          </div>
        </div>
      </td>
    </tr>
    @if($display_duration != 'd-none')
    <tr class="text-white" style="{{ ($row['prod_status'] == 'Completed') ? 'background-color: #2ecc71' : null }};">
      <td class="text-center p-1" style="border: none;" colspan="2">{{ $row['duration'] }}</td>
    </tr>
    @endif
    @empty
    <tr>
      <td colspan="12" class="text-center">No task(s) found</td>
    </tr>
    @endforelse
  </tbody>
</table>

<style type="text/css">
.custom-table-striped > tbody > tr:nth-child(4n+1) > td, .custom-table-striped > tbody > tr:nth-child(4n+1) > th {
   background-color: whitesmoke;
}

th.sticky-header {
  position: sticky;
  top: 0;
  z-index: 10;
  background-color: white;
}

.truncate {
  white-space: nowrap;
  /*overflow: hidden;*/
  text-overflow: ellipsis;
}
</style>
