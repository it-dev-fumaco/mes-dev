<table class="table custom-table-striped text-center" id="monitoring-table">
  <col style="width: 9%;">
  <col style="width: 13%;">
  <col style="width: 13%;">
  <col style="width: 7%;">
  <col style="width: 7%;">
  <col style="width: 7%;">
  <col style="width: 7%;">
  <col style="width: 7%;">
  <col style="width: 7%;">
  <col style="width: 7%;">
  <col style="width: 10%;">
  <col style="width: 6%;">
  <thead class="text-primary" style="font-size: 7pt;">
    <th class="text-center"><b>Prod. Order</b></th>
    <th class="text-center"><b>Customer</b></th>
    <th class="text-center"><b>Item Description</b></th>
    <th class="text-center"><b>Qty</b></th>
    <th class="text-center"><b>Loading</b></th>
    <th class="text-center"><b>Unloading</b></th>
    <th class="text-center"><b>Produced</b></th>
    <th class="text-center"><b>Feedbacked</b></th>
    <th class="text-center"><b>Balance</b></th>
    <th class="text-center"><b>Reject</b></th>
    <th class="text-center"><b>Notes</b></th>
    <th class="text-center"><b>Action</b></th>
  </thead>
  <tbody style="font-size: 9pt;">
    @forelse($data as $row)
    @php
        $painting_completed_qty = collect($row['job_ticket'])->min('completed_qty');
        $display_duration = ($row['qty'] == $painting_completed_qty) ? null : 'd-none';
        $rowspan = ($display_duration) ? 'rowspan="2"' : null;
    @endphp
    <tr>
      <td class="text-center" {!! $rowspan !!}>
        <span class="badge badge-info mr-2" style="font-size: 9pt;">{{ $row['sequence']}}</span>
        <a href="#" data-jtno="{{ $row['production_order'] }}" class="prod-details-btn font-weight-bold text-dark">{{ $row['production_order'] }}</a>
      </td>
      <td class="text-center" {!! $rowspan !!}>
        <span class="d-block font-weight-bold">{{ $row['reference_no'] }}</span>
        <span>{{ $row['customer'] }}</span>
      </td>
      <td class="text-center" {!! $rowspan !!}>
        <span class="font-weight-bold">{{ $row['item_code'] }}</span> - {{$row['item_description']}}
      </td>
      <td class="text-center" {!! $rowspan !!}>
        <span class="d-block font-weight-bold">{{ $row['qty'] }}</span>
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
        <span class="d-block font-weight-bold">{{ number_format($row['completed_qty']) }}</span>
        <span class="d-block">{{ $row['stock_uom'] }}</span>
      </td>
      <td class="text-center" {!! $rowspan !!}>
        <span class="d-block font-weight-bold">{{ number_format($row['feedback_qty']) }}</span>
        <span class="d-block">{{ $row['stock_uom'] }}</span>
      </td>
      <td class="text-center" {!! $rowspan !!}>
        <span class="d-block font-weight-bold">{{ number_format($row['balance_qty']) }}</span>
        <span class="d-block">{{ $row['stock_uom'] }}</span>
      </td>
      <td class="text-center" {!! $rowspan !!}>
        <span class="d-block font-weight-bold">{{ number_format($row['reject']) }}</span>
        <span class="d-block">{{ $row['stock_uom'] }}</span>
      </td>
      <td class="text-center" {!! $rowspan !!}>{{ $row['remarks']}} </td>
      <td {!! $rowspan !!}>
        <div class="btn-group">
          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
          <div class="dropdown-menu">
            <a class="dropdown-item create-feedback-btn" href="#" data-production-order="{{ $row['production_order'] }}">Create Feedback</a>
            <a class="dropdown-item addnotes" href="#" data-production-order="{{ $row['production_order'] }}" data-notes="{{ $row['remarks'] }}">Add Notes</a>
            <a class="dropdown-item editcpt_qty" href="#" id="editcpt-qty-btn" data-prod="{{$row['production_order']}}" data-qty="{{ $row['qty'] }}">Edit</a>
          </div>
        </div>
      </td>
    </tr>
    <tr class="heightcustom text-white {{ $display_duration }}" style="{{ ($row['prod_status'] == 'Completed') ? 'background-color: #2ecc71' : null }};">
      <td class="text-center" style="border: none;" colspan="2">{{ $row['duration'] }}</td>
    </tr>
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
  td.heightcustom > div {
    width: 100%;
    height: 100%;
    overflow:hidden;
}
td.heightcustom {
    height: 20px;
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
