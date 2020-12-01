                                <div class="table-responsive" style="font-size: 11px;">
                                <table class="table table-striped text-center">
                                                      <col style="width: 5%;">
                                                      <col style="width: 40%;">
                                                      <col style="width: 36%;">
                                                      <col style="width: 19%;">
                                                      <thead class="text-primary">
                                                        <th class="text-center"><b>No</b></th>
                                                        <th class="text-left"><b>Operation</b></th>
                                                        <th class="text-left"><b>Workstation</b></th>
                                                        <th class="text-center"><b>Action(s)</b></th>
                                                      </thead>
                                                      <tbody style="font-size:13px;">
                                                        @forelse($data as $index => $row)
                                                          <tr>
                                                            <td class="text-center">{{ $index + 1 }}</td>
                                                          <td class="text-left">{{ $row->operation }}</td>
                                                          <td class="text-left">
                                                              {{ $row->workstation_name }}
                                                          </td>
                                                          <td>
                                                            <a href="#" class="hover-icon"  data-toggle="modal" style="padding-left: 5px;" >
                                                                <button type='button' class='btn btn-default btn-edit-workstation-modal' data-workstationid="{{ $row->workstation_id }}" data-workstationname="{{ $row->workstation_name }}" data-orderno="{{$row->order_no}}" data-operation="{{$row->operation_id}}"><i class='now-ui-icons design-2_ruler-pencil'></i></button>
                                                              </a>
                                                              <a href="/workstation_profile/{{ $row->workstation_id }}" class="hover-icon"  data-toggle="modal"  style="padding-left: 5px;">
                                                                <button type='button' class='btn btn-default ui-1_zoom-bold'><i class='now-ui-icons ui-1_zoom-bold'></i></button>
                                                              </a>
                                                              <a href="#" class="hover-icon"  data-toggle="modal"  style="padding-left: 5px;">
                                                                <button type='button' class='btn btn-default btn-delete-workstation' data-workstationid="{{ $row->workstation_id }}" data-workstationname="{{ $row->workstation_name }}" data-orderno="{{$row->order_no}}" data-operation="{{$row->operation}}"><i class='now-ui-icons ui-1_simple-remove'></i></button>
                                                              </a>
                                                          </td>
                                                          @empty
                                                            <tr>
                                                              <td colspan="4" class="text-center">No Record(s) Found.</td>
                                                            </tr>
                                                            @endforelse
                                                        </tr>
                                                      </tbody>
                                                    </table>
                  <center>
  <div id="workstation_list_pagination" class="col-md-12 text-center" style="text-align: center;">
   {{ $data->links() }}
  </div>
</center>

                  </div>