<div class="table-responsive">
    <table class="table table-striped">
      <col style="width: 10%;">
      <col style="width: 35%;">
      <col style="width: 13.75%;">
      <col style="width: 13.75%;">
      <col style="width: 13.75%;">
      <col style="width: 13.75%;">
      <thead class="text-primary" style="font-size: 8pt;">
        <th class="text-center"><b>Item Code</b></th>
        <th class="text-center"><b>Description</b></th>
        <th class="text-center"><b>Cycle Time</b></th>
        <th class="text-center"><b>Planned Qty</b>
        <th class="text-center"><b>In Process Qty</b>
        <th class="text-center"><b>Balance Qty</b>
      </thead>
      <tbody>
        @forelse($data as $row)
        <tr class="text-center">
          <td><b>{{ $row['item_code'] }}</b></td>
          <td class="text-justify" style="font-size: 9pt;">{{ $row['description'] }}</td>
          <td class="text-center" style="font-size: 9pt;">{{ $row['cycle_time'] }}</td>

          <td>
            <span class="d-block font-weight-bold" style="font-size: 14pt;">{{ number_format($row['planned_qty']) }}</span>
            <span class="d-block" style="font-size: 8pt;">Piece(s)</span>
          </td>
          <td>
            <span class="d-block font-weight-bold" style="font-size: 14pt;">{{ number_format($row['in_process_qty']) }}</span>
            <span class="d-block" style="font-size: 8pt;">Piece(s)</span>
          </td>
          <td>
            <span class="d-block font-weight-bold" style="font-size: 14pt;">{{ number_format($row['balance_qty']) }}</span>
            <span class="d-block" style="font-size: 8pt;">Piece(s)</span>
          </td>
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
    {{ $inv_qry->links() }}
    </div>
  </center>