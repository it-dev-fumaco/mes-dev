<div class="table-responsive">
                    <table class="table table-striped text-center" style="font-size: 11px;">
                      <thead class="text-primary">
                        <th class="text-center" style="font-size:12px;"><b>Transaction No</b></th>
                        {{--  <th class="text-center" style="font-size:12px;"><b>Operation</b>  --}}
                        <th class="text-center" style="font-size:12px;"><b>Item Code</b>
                        <th class="text-center" style="font-size:12px;"><b>Adjusted Qty</b>
                        <th class="text-center" style="font-size:12px;"><b>Previous Qty</b>
                        <th class="text-center" style="font-size:12px;"><b>Entry Type</b>
                        <th class="text-center" style="font-size:12px;"><b>Created By</b>
                        <th class="text-center" style="font-size:12px;"><b>Created At</b>
                      </thead>
                      <tbody>
                        @forelse($data as $row)
                          <tr>
                            <td>{{ $row->transaction_no }}</td>
                            {{--  <td>{{ $row->operation_name }}</td>  --}}
                            <td>{{ $row->item_code }}</td>
                            <td><b>{{ $row->adjusted_qty }}</b></td>
                            <td>{{ $row->previous_qty }}</td>
                            <td>{{ $row->entry_type }}</td>
                            <td>{{ $row->created_by }}</td>
                            <td>{{ date('Y-m-d h:i a', strtotime($row->created_at)) }}</td>
                          </tr>
                        @empty
                        <tr>
                           <td colspan="8" class="text-center">No Record(s) Found.</td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                  <center>
  <div id="tbl_inventory_transactions_painting_pagination" class="col-md-12 text-center" style="text-align: center;">
   {{ $data->links() }}
  </div>
</center>


