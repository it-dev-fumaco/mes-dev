<div class="table-responsive">
  <table class="table table-striped">
    <col style="width: 10%;">
    <col style="width: 45%;">
    <col style="width: 15%;">
    <col style="width: 15%;">
    <col style="width: 15%;">
    <thead class="text-primary" style="font-size: 8pt;">
      <th class="text-center"><b>Item Code</b></th>
      <th class="text-center"><b>Description</b></th>
      <th class="text-center"><b>UOM</b>
      <th class="text-center"><b>Item Classification</b>
      <th class="text-center"><b>Balance Qty</b>
    </thead>
    <tbody>
      @forelse($data as $row)
      <tr class="text-center">
        <td><b>{{ $row->item_code}}</b></td>
        <td class="text-justify" style="font-size: 9pt;">{{ $row->description}}</td>
        <td>{{ $row->uom}}</td>
        <td>{{ $row->item_classification}}</td>
        <td><span style="display:block;"><b>{{ $row->balance_qty}}</b><span><span style="display:block;font-size:10px;">{{ $row->uom}}<span></td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="text-center">No Record(s) Found.</td>
      </tr>
      @endforelse
    </tbody>  
  </table>
</div>
<center>
  <div id="tbl_painting_stock_pagination" class="col-md-12 text-center" style="text-align: center;">
  {{ $data->links() }}
  </div>
</center>