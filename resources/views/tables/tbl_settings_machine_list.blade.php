      <div class="table-responsive" style="font-size:11px;">

                        <table class="table table-striped">
                                  <col style="width: 5%;">
                                  <col style="width: 26%;">
                                  <col style="width: 25%;">
                                  <col style="width:  25%;">
                                  <col style="width: 19%;">
                                <thead class="text-primary">
                                  <th class="text-center"><b>No.</b></th>
                                  <th class="text-left"><b>Machine Code</b></th>
                                  <th class="text-left"><b>Machine Name</b></th>
                                  <th class="text-left"><b>Status</b></th>
                                  <th class="text-center"><b>Action(s)</b></th>
                                </thead>
                                <tbody style="font-size:13px;">
                                  @forelse($data as $index => $row)
                                    <tr>
                                    <td>{{ $row->machine_id }}</td>
                                    <td class="text-left">{{ $row->machine_code }}</td>
                                    <td class="text-left">
                                        {{ $row->machine_name }}
                                    </td>
                                    <td class="text-left">{{ $row->status }}</td>
                                    <td>
                                        <a href="#" class="hover-icon"  data-toggle="modal">
                                          <button type='button' class='btn btn-default btn-edit-machine' data-machineid="{{ $row->machine_id }}" data-machinecode="{{ $row->machine_code }}" data-machinename="{{ $row->machine_name }}" data-status="{{ $row->status }}" data-referencekey="{{ $row->reference_key }}" data-type="{{ $row->type}}" data-model="{{$row->model}}" data-image="{{ $row->image }}"><i class='now-ui-icons design-2_ruler-pencil'></i></button>
                                        </a>
                                        <a href="/goto_machine_profile/{{ $row->machine_id }}" class="hover-icon"  data-toggle="modal"  style="padding-left: 5px;">
                                          <button type='button' class='btn btn-default'><i class='now-ui-icons ui-1_zoom-bold'></i></button>
                                        </a>
                                        <a href="#" class="hover-icon"  data-toggle="modal" style="padding-left: 5px;">
                                          <button type='button' class='btn btn-default btn-delete-machine' data-machineid="{{ $row->machine_id }}" data-machinecode="{{ $row->machine_code }}" data-machinename="{{ $row->machine_name }}" data-status="{{ $row->status }}" data-referencekey="{{ $row->reference_key }}" data-type="{{ $row->type}}" data-model="{{$row->model}}" data-image="{{ $row->image }}"><i class='now-ui-icons ui-1_simple-remove'></i></button>
                                        </a>
                                        
                                        
                                    </td>
                                    @empty
                                    <tr>
                                      <td colspan="5" class="text-center">No Record(s) Found.</td>
                                    </tr>
                                    @endforelse

                                  </tr>
                                </tbody>
                              </table>
                              <center>
  <div id="setting_machine_list_pagination" class="col-md-12 text-center" style="text-align: center;">
   {{ $data->links() }}
  </div>
</center>
</div>
