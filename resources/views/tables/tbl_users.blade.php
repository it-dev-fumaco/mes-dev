<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
  <thead class="text-primary text-uppercase font-weight-bold">
    <th class="text-center">Employee Name</th>
    <th class="text-center">Last Login</th>
    <th class="text-center">Last Active</th>
    <th class="text-center">Action</th>
  </thead>
  <tbody style="font-size: 12px;">
    @forelse($users as $name => $access_arr)
      @php
        $last_login = collect($access_arr)->max('last_login');
        $last_seen = collect($access_arr)->max('last_seen');
      @endphp
      <tr>
        <td class="text-center">{{ $name }}</td>
        <td class="text-center">{{ $last_login ? Carbon\Carbon::parse($last_login)->format('M. d, Y h:i A') : '-' }}</td>
        <td class="text-center">{{ $last_seen ? Carbon\Carbon::parse($last_seen)->format('M. d, Y h:i A') : '-' }}</td>
        <td class="text-center">
          <div class="btn-group" role="group" aria-label="Basic example">
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#{{ str_slug($name) }}-modal" style="font-size: 8pt;"><i class="now-ui-icons design-2_ruler-pencil"></i></button>
            <button type="button" class="btn btn-sm delete-user-btn" data-name="{{ $name }}" data-user="{{ $access_arr[0]->user_access_id }}" style="font-size: 8pt;"><i class="now-ui-icons ui-1_simple-remove"></i></button>
          </div>
          
          <div class="modal fade" id="{{ str_slug($name) }}-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">
                <div class="modal-header" style="background-color: #0277BD; color: #fff">
                  <h5 class="modal-title" id="exampleModalLabel">{{ $name }}</h5>
                  <button type="button" class="close modal-control" data-modal="#{{ str_slug($name) }}-modal" data-action="hide">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="row text-left pb-2" style="font-size: 10pt;">
                    <div class="col-6">
                      <span class="d-block"><b>Employee Name: </b>{{ $name }}</span>
                      <span class="d-block"><b>User Access ID: </b>{{ $access_arr[0]->user_access_id }}</span>
                    </div>
                    <div class="col-6">
                      <span class="d-block"><b>Last Login: </b>{{ $last_login ? Carbon\Carbon::parse($last_login)->format('M. d, Y h:i A') : '-' }}</span>
                      <span class="d-block"><b>Last Seen: </b>{{ $last_seen ? Carbon\Carbon::parse($last_seen)->format('M. d, Y h:i A') : '-' }}</span>
                    </div>
                  </div>
                  <table class="table table-bordered">
                    <thead class="text-primary text-uppercase font-weight-bold">
                      <th class="text-center p-2"><b>No.</b></th>
                      <th class="text-center p-2"><b>Module</b></th>
                      <th class="text-center p-2"><b>Operation</b></th>
                      <th class="text-center p-2"><b>User Role</b></th>
                      <th class="text-center p-2"><b>Action(s)</b></th>
                    </thead>
                    <tbody>
                      @foreach ($access_arr as $row)
                        <tr>
                          <td class="text-center p-2">
                            <span>{{ $row->user_id }}</span><span style="display: none;">{{ $row->user_access_id }}</span>
                          </td>
                          <td class="text-center p-2">{{ $row->module }}</td>
                          <td class="text-center p-2"><span style="display: none;">{{ $row->operation_id }}</span>{{ $row->operation_name }}</td>
                          <td class="text-center p-2">{{ $row->user_role }}</td>
                          <td class="text-center p-2">
                            <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon edit-user-btn' data-module="{{ $row->module }}" data-operation="{{ $row->operation_id }}" data-userid="{{ $row->user_access_id }}" data-usergroup="{{ $row->user_group_id }}" data-user="{{ $row->employee_name }}" data-id="{{ $row->user_id }}">
                              <i class='now-ui-icons design-2_ruler-pencil'></i>
                            </button>
                            <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon' data-toggle="modal" data-target="#delete-user-access-{{ str_slug($name) }}">
                              <i class='now-ui-icons ui-1_simple-remove'></i>
                            </button>

                            <div class="modal fade" id="delete-user-access-{{ str_slug($name) }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Remove Access</h5>
                                    <button type="button" class="close modal-control" data-modal="#delete-user-access-{{ str_slug($name) }}" data-action='hide'>
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                    Remove <b>{{ $row->module.' - '.$row->user_role }}</b> access of <b>{{ $name }}?</b>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary mr-2 modal-control" data-modal="#delete-user-access-{{ str_slug($name) }}" data-action="hide">Close</button>
                                    <button type="button" class="btn btn-primary remove-user-access-btn" data-user="{{ $row->user_id }}" data-name="#delete-user-access-{{ str_slug($name) }}">Confirm</button>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="5" class="text-center text-uppercase text-muted">No User(s) Found.</td>
      </tr>
    @endforelse 
  </tbody>
</table>

<style>
  .modal{
    background-color: rgba(0,0,0,.4);
  }
</style>