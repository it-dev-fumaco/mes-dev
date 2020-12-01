<div class="table-responsive" style="font-size:15px;">
                    <table class="table table-striped text-center" style="font-size:8pt;">
                      <thead class="text-primary">
                        <th class="text-center"><b>Transaction No</b></th>
                        <th class="text-center"><b>Operation</b>
                        <th class="text-center"><b>Item Code</b>
                        <th class="text-center"><b>Adjusted Qty</b>
                        <th class="text-center"><b>Previous Qty</b>
                        <th class="text-center"><b>Entry Type</b>
                        <th class="text-center"><b>Created By</b>
                        <th class="text-center"><b>Created At</b>
                      </thead>
                      <tbody>
                        @forelse($data as $row)
                          <tr>
                            <td>{{ $row->transaction_no }}</td>
                            <td>{{ $row->operation_name }}</td>
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
  <div id="tbl_fabrication_inventory_history_pagination" class="col-md-12 text-center" style="text-align: center;">
   {{ $data->links() }}
  </div>
</center>


