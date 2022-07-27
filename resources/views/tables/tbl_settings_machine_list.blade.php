      <div class="table-responsive" style="font-size:11px;">

                        <table class="table table-striped">
                                  <col style="width: 5%;"><!-- No. -->
                                  <col style="width: 20%;"><!-- Machine Code -->
                                  <col style="width: 25%;"><!-- Machine Name -->
                                  <col style="width: 15%;"><!-- Status -->
                                  <col style="width: 35%;"><!-- Action(s) -->
                                <thead class="text-primary">
                                  <th class="text-center"><b>No.</b></th>
                                  <th class="text-left"><b>Machine Code</b></th>
                                  <th class="text-center"><b>Machine Name</b></th>
                                  <th class="text-center"><b>Status</b></th>
                                  <th class="text-center"><b>Action(s)</b></th>
                                </thead>
                                <tbody style="font-size:13px;">
                                  @forelse($data as $index => $row)
                                    <tr>
                                    <td class="text-center">{{ $row->machine_id }}</td>
                                    <td class="text-left">
                                      <div class="row">
                                        <div class="col-4">
                                          <img src="{{ asset(($row->image ? $row->image : '/storage/no_img.png')) }}" class="w-100 img-thumbnail" alt="">
                                        </div>
                                        <div class="col-5" style="display: flex; justify-content: center; align-items: center;">
                                          {{ $row->machine_code }}
                                        </div>
                                      </div>
                                    </td>
                                    <td class="text-left">
                                      {{ $row->machine_name }}
                                    </td>
                                    <td class="text-center">
                                      @php
                                          switch($row->status){
                                            case 'Available':
                                              $badge = 'success';
                                              break;
                                            case 'Unavailable':
                                              $badge = 'secondary';
                                              break;
                                            default:
                                              $badge = 'primary';
                                              break;
                                          }
                                      @endphp
                                      <span class="badge badge-{{ $badge }}">{{ $row->status }}</span>
                                    </td>
                                    <td class="text-center">
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
