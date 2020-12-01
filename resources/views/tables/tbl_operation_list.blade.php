<div class="table-responsive" style="font-size:11px;">
                    <table class="table table-striped">
                      <col style="width: 5%;">
                      <col style="width: 43%;">
                      <col style="width: 42%;">
                      <col style="width: 10%;">
                      
                      <thead class="text-primary">
                        <th class="text-center"><b>ID</b></th>
                        <th class="text-left"><b>Operation</b></th>
                        <th class="text-left"><b>Description</b></th>
                        <th class="text-center"><b>Action/s</b></th>
                      </thead>
                      <tbody style="font-size:13px;">
                        @forelse($data as $row)
                           <tr>
                            <td class="text-center">{{ $row->operation_id }}</td>
                            <td class="text-left">{{ $row->operation_name }}</td>
                            <td class="text-left">{{ $row->description }}</td>
                            <td>
                          
                              <a href="#" class="hover-icon edit-operation-list" data-toggle="modal" data-operationid="{{ $row->operation_id }}" data-operationname="{{ $row->operation_name }}" data-operationdesc="{{ $row->description }}" style="padding-left: 5px;">
                                <button type='button' class='btn btn-default ui-1_zoom-bold'><i class='now-ui-icons design-2_ruler-pencil'></i></button>
                              </a>
                                {{--
                              <a href="#" class="hover-icon btn-modal-process-profile" data-toggle="modal" data-processid="{{ $row->process_id }}" data-process="{{ $row->process_name }}" style="padding-left: 5px;">
                                <button type='button' class='btn btn-info ui-1_zoom-bold'><i class='now-ui-icons ui-1_zoom-bold'></i></button>
                              </a>

                              <a href="#" class="hover-icon delete-shift-list"  data-toggle="modal" data-shiftid="{{ $row->shift_id }}" style="padding-left: 5px;">
                                <button type='button' class='btn btn-danger'><i class='now-ui-icons ui-1_simple-remove'></i></button>
                              </a>
                                  --}}                         

                           </td>
                        </tr>
                        @empty
                        <tr>
                           <td colspan="4" class="text-center">No Record(s) Found.</td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                  <center>
  <div id="tbl_operation_list_pagination" class="col-md-12 text-center" style="text-align: center;">
   {{ $data->links() }}
  </div>
</center>


