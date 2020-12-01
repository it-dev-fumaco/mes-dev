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
      <th class="text-center"><b>Planned Qty</b>
      <th class="text-center"><b>In Process Qty</b>
      <th class="text-center"><b>Balance Qty</b>
    </thead>
    <tbody>
      @forelse($data as $row)
      <tr class="text-center">
        <td><b>{{ $row['item_code'] }}</b></td>
        <td class="text-justify" style="font-size: 9pt;">{{ $row['description'] }}</td>
        <td>{{ $row['planned_qty'] }}</td>
        <td>{{ $row['in_process_qty'] }}</td>
        <td>{{ $row['balance_qty'] }}</td>
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
  <div id="tbl_stockadjustment_entry_pagination" class="col-md-12 text-center" style="text-align: center;">
  {{ $data->links() }}
  </div>
</center>