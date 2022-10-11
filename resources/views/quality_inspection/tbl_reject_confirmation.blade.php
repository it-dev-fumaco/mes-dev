<table class="table table-bordered table-hover table-striped m-0">
  <col style="width: 5%;">
  <col style="width: 10%;">
  <col style="width: 10%;">
  <col style="width: 25%;">
  <col style="width: 12%;">
  <col style="width: 8%;">
  <col style="width: 11%;">
  <col style="width: 11%;">
  <col style="width: 8%;">
  <thead class="text-center text-white bg-secondary" style="font-size: 6.5pt;">
    <th class="p-1">No.</th>
    <th class="p-1">Workstation</th>
    <th class="p-1">Prod. Order</th>
    <th class="p-1">Item Code</th>
    <th class="p-1">Reject Type</th>
    <th class="p-1">Reject</th>
    <th class="p-1">Operator</th>
    <th class="p-1">Date Reported</th>
    <th class="p-1">Action</th>
  </thead>
  <tbody style="font-size: 8pt;">
    @forelse($list as $i => $row)
    <tr>
      <td class="text-center p-1">{{ $i + 1 }}</td>
      <td class="text-center p-1 font-weight-bold">{{ $row->workstation }}</td>
      <td class="text-center p-1 font-weight-bold">{{ $row->production_order }}</td>
      <td class="text-center p-1"><b>{{ $row->item_code }}</b><br>{{ strtok($row->description, ',') }}</td>
      <td class="text-center p-1">{{ $row->reject_reason }}</td>
      <td class="text-center p-1">
        <span class="d-block font-weight-bold" style="font-size: 10pt;">{{ number_format($row->rejected_qty) }}</span>
        <small class="d-block">{{ $row->stock_uom }}</small>
      </td>
      <td class="text-center p-1">{{ $row->created_by }}</td>
      <td class="text-center p-1">{{ date('M-d-Y h:i A', strtotime($row->created_at)) }}</td>
      <td class="text-center p-1">
        <button type="button" class="btn btn-primary pb-2 pt-2 pr-3 pl-3 reject-confirmation-btn" data-inspection-type="Reject Confirmation" data-workstation="{{ $row->workstation }}" data-production-order="{{ $row->production_order }}" data-process-id="{{ $row->process_id }}" data-qaid="{{ $row->qa_id }}">Action</button>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="9" class="text-center text-uppercase text-muted">No record(s) found</td>
    </tr>
    @endforelse
  </tbody>
</table>
<div class="text-center mt-2 paginate-reject-confirmation">{{ $list->links() }}</div>