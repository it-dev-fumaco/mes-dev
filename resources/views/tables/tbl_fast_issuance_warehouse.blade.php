<div class="table-responsive" style="font-size:15px;">
    <table class="table table-striped text-center" style="font-size:8pt;">
      <thead class="text-primary">
        <th class="text-center"><b>ID</b></th>
        <th class="text-center"><b>Warehouse</b>
        <th class="text-center"><b>Created by</b>
        <th class="text-center"><b>Created at</b>
        <th class="text-center"><b>Action</b>
      </thead>
      <tbody>
        @forelse($list as $row)
          <tr>
            <td>{{ $row->fast_issuance_warehouse_id }}</td>
            <td>{{ $row->warehouse }}</td>
            <td>{{ $row->created_by }}</td>
            <td>{{ date('Y-m-d h:i a', strtotime($row->created_at)) }}</td>
            <td>
                <button type="button" class="btn btn-danger hover-icon delete-allowed-fast-issuance-warehouse-btn" data-id="{{ $row->fast_issuance_warehouse_id }}" data-warehouse="{{ $row->warehouse }}">
                    <i class="now-ui-icons ui-1_simple-remove"></i>
                </button>
            </td>
          </tr>
        @empty
        <tr>
           <td colspan="5" class="text-center">No Record(s) Found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <center>
    <div id="tbl-fast-issuance-warehouse-pagination" class="col-md-12 text-center" style="text-align: center;">
    {{ $list->links() }}
    </div>
</center>


