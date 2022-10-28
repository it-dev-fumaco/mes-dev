<table class="table table-bordered table-hover table-striped m-0">
  <col style="width: 5%;">
  <col style="width: 20%;">
  <col style="width: 10%;">
  <col style="width: 45%;">
  <col style="width: 12%;">
  <col style="width: 8%;">
  <thead class="text-center text-white bg-secondary" style="font-size: 6.5pt;">
    <th class="p-1">No.</th>
    <th class="p-1">Customer</th>
    <th class="p-1">Prod. Order</th>
    <th class="p-1">Item Details</th>
    <th class="p-1">Qty to Manufacture</th>
    <th class="p-1">Action</th>
  </thead>
  <tbody style="font-size: 8pt;">
    @forelse($list as $i => $row)
    <tr>
      <td class="text-center p-1 font-weight-bold">{{ $i + 1 }}</td>
      <td class="text-center p-1 font-weight-bold">
        <span class="d-block">{{ $row->sales_order .''. $row->material_request }}</span>
        <small class="d-block">{{ $row->customer }}</small>
      </td>
      <td class="text-center p-1 font-weight-bold">{{ $row->production_order }}</td>
      <td class="text-justify p-1"><b>{{ $row->item_code }}</b> {{ strip_tags($row->description) }}</td>
      <td class="text-center p-1">
        <span class="d-block font-weight-bold">{{ number_format($row->qty_to_manufacture) }}</span>
        <small class="d-block">{{ $row->stock_uom }}</small>
      </td>
      <td class="text-center p-1">
        <button type="button" class="btn btn-primary pb-2 pt-2 pr-3 pl-3 reject-for-confirmation-action-btn" data-production-order="{{ $row->production_order }}">Action</button>
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