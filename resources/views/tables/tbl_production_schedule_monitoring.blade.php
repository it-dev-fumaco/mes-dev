<table class="table table-striped text-center" id="monitoring-table">
    <col style="width: 7%;">
    <col style="width: 15%;">
    <col style="width: 27%;">
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
      <th class="text-center"><b>Produced</b></th>
      <th class="text-center"><b>Feedbacked</b></th>
      <th class="text-center"><b>Balance</b></th>
      <th class="text-center"><b>Reject</b></th>
      <th class="text-center"><b>Notes</b></th>
      <th class="text-center"><b>Action</b></th>
    </thead>
    <tbody style="font-size: 9pt;">
      @forelse($production_orders as $row)
      <tr>
        <td class="text-center p-0">
          <a href="#" data-jtno="{{ $row['production_order'] }}" class="prod-details-btn font-weight-bold text-dark">{{ $row['production_order'] }}</a>
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