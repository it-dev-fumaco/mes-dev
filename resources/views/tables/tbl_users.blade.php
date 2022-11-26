<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
  <col style="width: 5%;">
  <col style="width: 10%;">
  <col style="width: 10%;">
  <col style="width: 12%;">
  <col style="width: 15%;">
  <col style="width: 10%;">
  <col style="width: 10%;">
  <col style="width: 8%;">
  <thead class="text-primary text-uppercase font-weight-bold">
    <th class="text-center p-2"><b>No.</b></th>
    <th class="text-center p-2"><b>Module</b></th>
    <th class="text-center p-2"><b>Operation</b></th>
    <th class="text-center p-2"><b>User Role</b></th>
    <th class="text-center p-2"><b>Name</b></th>
    <th class="text-center p-2"><b>Last Login</b></th>
    <th class="text-center p-2"><b>Last Active</b></th>
    <th class="text-center p-2"><b>Action(s)</b></th>
  </thead>
  <tbody style="font-size: 12px;">
    @forelse($users as $row)
    <tr>
      <td class="text-center p-2">
        <span>{{ $row->user_id }}</span><span style="display: none;">{{ $row->user_access_id }}</span>
      </td>
      <td class="text-center p-2">{{ $row->module }}</td>
      <td class="text-center p-2"><span style="display: none;">{{ $row->operation_id }}</span>{{ $row->operation_name }}</td>
      <td class="text-center p-2">{{ $row->user_role }}</td>
      <td class="text-center p-2">{{ $row->employee_name }}</td>
      <td class="text-center p-2">{{ $row->last_login ? \Carbon\Carbon::parse($row->last_login)->format('M. d, Y h:i A') : '-' }}</td>
      <td class="text-center p-2">{{ $row->last_seen ? \Carbon\Carbon::parse($row->last_seen)->format('M. d, Y h:i A') : '-' }}</td>
      <td class="text-center p-2">
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon edit-user-btn' data-module="{{ $row->module }}" data-operation="{{ $row->operation_id }}" data-userid="{{ $row->user_access_id }}" data-usergroup="{{ $row->user_group_id }}" data-user="{{ $row->employee_name }}" data-id="{{ $row->user_id }}">
          <i class='now-ui-icons design-2_ruler-pencil'></i>
        </button>
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon delete-user-btn'>
          <i class='now-ui-icons ui-1_simple-remove'></i>
        </button>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="5" class="text-center text-uppercase text-muted">No User(s) Found.</td>
    </tr>
    @endforelse 
  </tbody>
</table>
<div class="col-md-12 text-center" id="user-pagination">{{ $users->links() }}</div>