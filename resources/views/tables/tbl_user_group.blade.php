<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
      <col style="width: 5%;">
      <col style="width: 35%;">
      <col style="width: 35%;">
      <col style="width: 10%;">
      <thead class="text-primary text-uppercase font-weight-bold">
      <th class="text-center"><b>No.</b></th>
      <th class="text-left"><b>Module</b></th>
      <th class="text-left"><b>User Role</b></th>
      <th class="text-center"><b>Action(s)</b></th>
    </thead>
    <tbody style="font-size: 12px;">
      @forelse($list as $row)
      <tr>
        <td class="text-center">
          {{ $row->user_group_id }}
        </td>
        <td class="text-left">{{ $row->module }}</td>
        <td class="text-left">{{ $row->user_role }}</td>
        <td class="text-center">
          <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon edit-user-group-btn' data-module="{{ $row->module }}"   data-id="{{ $row->user_group_id }}" data-role="{{ $row->user_role }}">
            <i class='now-ui-icons design-2_ruler-pencil'></i>
          </button>
          <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon delete-user-group-btn'  data-module="{{ $row->module }}"   data-id="{{ $row->user_group_id }}" data-role="{{ $row->user_role }}">
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
<div class="col-md-12 text-center" id="user-pagination">{{ $list->links() }}</div>