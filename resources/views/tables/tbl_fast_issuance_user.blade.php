<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
  <thead class="text-primary text-uppercase font-weight-bold">
    <th class="p-2 text-center"><b>ID</b></th>
    <th class="p-2 text-center"><b>User</b></th>
    <th class="p-2 text-center"><b>Created by</b></th>
    <th class="p-2 text-center"><b>Created at</b></th>
    <th class="p-2 text-center"><b>Action</b></th>
  </thead>
  <tbody style="font-size:13px;">
    @forelse($list as $row)
      @if (isset($employee_names[$row->user_access_id]))
        <tr>
          <td class="p-2 text-center">{{ $row->fast_issuance_user_id }}</td>
          <td class="p-2 text-center">{{ $employee_names[$row->user_access_id] }}</td>
          <td class="p-2 text-center">{{ $row->created_by }}</td>
          <td class="p-2 text-center">{{ date('Y-m-d h:i a', strtotime($row->created_at)) }}</td>
          <td class="p-2 text-center">
            <button type="button"
              class="btn pb-2 pt-2 pr-3 pl-3 btn-danger hover-icon delete-allowed-fast-issuance-user-btn"
              data-id="{{ $row->fast_issuance_user_id }}" data-username="{{ $employee_names[$row->user_access_id] }}">
              <i class="now-ui-icons ui-1_simple-remove"></i>
            </button>
          </td>
        </tr>
      @endif
    @empty
    <tr>
      <td colspan="5" class="text-center text-uppercase text-muted">No Record(s) Found.</td>
    </tr>
    @endforelse
  </tbody>
</table>

<div id="tbl-fast-issuance-user-pagination" class="col-md-12 text-center">{{ $list->links() }}</div>