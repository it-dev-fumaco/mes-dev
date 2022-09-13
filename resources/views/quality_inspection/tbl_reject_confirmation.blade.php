<table class="table table-bordered table-hover">
    <col style="width: 5%;">
    <col style="width: 10%;">
    <col style="width: 10%;">
    <col style="width: 25%;">
    <col style="width: 12%;">
    <col style="width: 8%;">
    <col style="width: 11%;">
    <col style="width: 11%;">
    <col style="width: 8%;">
    <thead class="text-center font-weight-bold text-primary text-uppercase" style="font-size: 7pt;">
      <th>No.</th>
      <th>Workstation</th>
      <th>Production Order</th>
      <th>Item Code</th>
      <th>Reject Type</th>
      <th>Reject</th>
      <th>Operator</th>
      <th>Date Reported</th>
      <th>Actions</th>
    </thead>
    <tbody>
      @forelse($list as $i => $row)
      <tr style="font-size: 10pt;">
        <td class="text-center">{{ $i + 1 }}</td>
        <td class="text-center font-weight-bold">{{ $row->workstation }}</td>
        <td class="text-center font-weight-bold">{{ $row->production_order }}</td>
        <td class="text-center"><b>{{ $row->item_code }}</b><br>{{ strtok($row->description, ',') }}</td>
        <td class="text-center">{{ $row->reject_reason }}</td>
        <td class="text-center">
          <span style="display: block; font-size: 16pt;">{{ number_format($row->rejected_qty) }}</span>
          <span style="display: block; font-size: 9pt;">{{ $row->stock_uom }}</span>
        </td>
        <td class="text-center">{{ $row->created_by }}</td>
        <td class="text-center">{{ date('M-d-Y h:i A', strtotime($row->created_at)) }}</td>
        <td class="text-center">
          <button type="button" class="btn btn-primary reject-confirmation-btn" data-inspection-type="Reject Confirmation" data-workstation="{{ $row->workstation }}" data-production-order="{{ $row->production_order }}" data-process-id="{{ $row->process_id }}" data-qaid="{{ $row->qa_id }}">Actions</button>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="8"><span style="font-size: 16pt;">No record(s) found.</span></td>
      </tr>
      @endforelse
    </tbody>
  </table>

  <center>
    <div id="paginate-reject-confirmation" class="text-center" style="text-align: center;">
     {{ $list->links() }}
    </div>
  </center>