<div class="table-responsive">
                    <table class="table table-striped text-center">
                      <thead class="text-primary">
                        <th class="text-center"><b>No.</b></th>
                        <th class="text-center"><b>Machine Code</b></th>
                        <th class="text-center"><b>Machine Name</b></th>
                        <th class="text-center"><b>Status</b></th>
                        <th class="text-center"><b>Action(s)</b></th>
                      </thead>
                      <tbody>
                         @foreach($machine_list as $row)
                           <tr>
                           <td>{{ $row->id }}</td>
                           <td>{{ $row->machine_code }}</td>
                           <td>
                              {{ $row->machine_name }}
                           </td>
                           <td>{{ $row->status }}</td>
                           <td>
                              <a href="#" class="hover-icon"  data-toggle="modal" data-target="#edit-machinelist-{{ $row->id }}-modal">
                                <button type='button' class='btn btn-success'><i class='now-ui-icons design-2_ruler-pencil'></i></button>
                              </a>
                              <a href="/goto_machine_profile/{{ $row->id }}" class="hover-icon"  data-toggle="modal"  style="padding-left: 5px;">
                                <button type='button' class='btn btn-info'><i class='now-ui-icons ui-1_zoom-bold'></i></button>
                              </a>
                              <a href="#" class="hover-icon"  data-toggle="modal" data-target="#delete-machinelist-{{ $row->id }}-modal" style="padding-left: 5px;">
                                <button type='button' class='btn btn-danger'><i class='now-ui-icons ui-1_simple-remove'></i></button>
                              </a>
                              
                              
                           </td>
                              @include('modals.edit_machineList_modal')
                              @include('modals.delete_machineList_modal')
                           @endforeach

                        </tr>
                      </tbody>
                    </table>
                      {{ $machine_list->links() }}
                  </div>