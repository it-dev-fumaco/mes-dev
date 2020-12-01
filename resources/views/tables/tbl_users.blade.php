<div class="table-responsive" style="font-size: 11px;">
  <table class="table table-striped text-center">
      <col style="width: 5%;">
      <col style="width: 15.75%;">
      <col style="width: 15.75%;">
      <col style="width: 15.75%;">
      <col style="width: 15.75%;">
      <col style="width: 10%;">
    <thead class="text-primary">
      <th class="text-center"><b>No.</b></th>
      <th class="text-left"><b>Module</b></th>
      <th class="text-left"><b>Operation</b></th>
      <th class="text-left"><b>User Role</b></th>
      <th class="text-left"><b>User Name</b></th>
      <th class="text-center"><b>Action(s)</b></th>
    </thead>
    <tbody style="font-size:13px;">
      @forelse($users as $row)
      <tr>
        <td class="text-center">
          <span>{{ $row->user_id }}</span><span style="display: none;">{{ $row->user_access_id }}</span>
        </td>
        <td class="text-left">{{ $row->module }}</td>
        <td class="text-left"><span style="display: none;">{{ $row->operation_id }}</span>{{ $row->operation_name }}</td>
        <td class="text-left">{{ $row->user_role }}</td>
        <td class="text-left">{{ $row->employee_name }}</td>
        <td class="text-center">
          <button type='button' class='btn btn-default hover-icon edit-user-btn' data-module="{{ $row->module }}" data-operation="{{ $row->operation_id }}" data-userid="{{ $row->user_access_id }}" data-usergroup="{{ $row->user_group_id }}" data-user="{{ $row->employee_name }}" data-id="{{ $row->user_id }}">
            <i class='now-ui-icons design-2_ruler-pencil'></i>
          </button>
          <button type='button' class='btn btn-default hover-icon delete-user-btn'>
            <i class='now-ui-icons ui-1_simple-remove'></i>
          </button>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="5">No User(s) Found.</td>
      </tr>
      @endforelse 
    </tbody>
  </table>
  
  <center>
    <div class="col-md-12 text-center" id="user-pagination">
     {{ $users->links() }}
    </div>
  </center>
</div>