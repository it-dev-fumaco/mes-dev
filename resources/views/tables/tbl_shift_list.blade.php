<div class="table-responsive">
                    <table class="table table-striped text-center">
                      <thead class="text-primary" style="font-size: 8pt;">
                        <th class="text-center"><b>ID</b></th>
                        <th class="text-center"><b>Time in</b></th>
                        <th class="text-center"><b>Time out</b></th>
                        <th class="text-center"><b>Shift type</b></th>
                        <th class="text-center"><b>Operation</b></th>
                        <th class="text-center"><b>Action/s</b></th>
                      </thead>
                      <tbody style="font-size: 9pt;">
                        @forelse($data as $row)
                           <tr>
                            <td>{{ $row->shift_id }}</td>
                           <td>{{ $row->time_in }}</td>
                          <td>{{ $row->time_out }}</td>
                          <td>{{ $row->shift_type }}</td>
                          <td>{{ $row->operation_name }}</td>
                          <td>
                            
                              <a href="#" class="hover-icon edit-shift-list" data-toggle="modal" data-timein="{{ $row->time_in }}" data-timeout="{{ $row->time_out }}" data-shifttype="{{ $row->shift_type }}" data-qtycapacity="{{ $row->qty_capacity }}" data-remarks="{{ $row->remarks }}" data-shiftid="{{ $row->shift_id }}" data-breakinmin="{{ $row->breaktime_in_mins}}" data-operation="{{ $row->operation_id }}" style="padding-left: 5px;">
                                <button type='button' class='btn btn-default ui-1_zoom-bold'><i class='now-ui-icons design-2_ruler-pencil'></i></button>
                              </a>

                              {{--<a href="#" class="hover-icon btn-modal-process-profile" data-toggle="modal" data-processid="{{ $row->process_id }}" data-process="{{ $row->process_name }}" style="padding-left: 5px;">
                                <button type='button' class='btn btn-default ui-1_zoom-bold'><i class='now-ui-icons ui-1_zoom-bold'></i></button>
                              </a>--}}

                              <a href="#" class="hover-icon delete-shift-list"  data-toggle="modal" data-shiftid="{{ $row->shift_id }}" style="padding-left: 5px;">
                                <button type='button' class='btn btn-default'><i class='now-ui-icons ui-1_simple-remove'></i></button>
                              </a>
                                                           

                           </td>
                        </tr>
                        @empty
                        <tr>
                           <td colspan="6" class="text-center">No Record(s) Found.</td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                  <center>
  <div id="tbl_shift_list_pagination" class="col-md-12 text-center" style="text-align: center;">
   {{ $data->links() }}
  </div>
</center>


