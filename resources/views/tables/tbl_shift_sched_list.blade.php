<div class="table-responsive">
                    <table class="table table-striped text-center">
                      <thead class="text-primary" style="font-size: 8pt;">
                        <th class="text-center"><b>ID</b></th>
                        <th class="text-center"><b>Shift</b></th>
                        <th class="text-center"><b>Date</b></th>
                        <th class="text-center"><b>Operation</b></th>
                        <th class="text-center"><b>Action/s</b></th>
                      </thead>
                      <tbody style="font-size: 9pt;">
                        @forelse($data as $row)
                           <tr>
                            <td>{{ $row->shift_schedule_id }}</td>
                           <td>{{ $row->shift_type }}</td>
                          <td>{{ $row->date }}</td>
                          <td>{{ $row->operation_name }}</td>
                          <td>
                          
                               {{--<a href="#" class="hover-icon edit-shift-sched-list" data-toggle="modal"  data-remarks="{{ $row->remarks }}" data-date="{{ $row->date }}" data-shiftid="{{ $row->shift_id }}" data-shiftschedid="{{ $row->shift_schedule_id }}" style="padding-left: 5px;">
                                <button type='button' class='btn btn-success ui-1_zoom-bold'><i class='now-ui-icons design-2_ruler-pencil'></i></button>
                              </a>
                               
                              <a href="#" class="hover-icon btn-modal-process-profile" data-toggle="modal" data-processid="{{ $row->process_id }}" data-process="{{ $row->process_name }}" style="padding-left: 5px;">
                                <button type='button' class='btn btn-info ui-1_zoom-bold'><i class='now-ui-icons ui-1_zoom-bold'></i></button>
                              </a>--}}

                              <a href="#" class="hover-icon delete-shift-sched-list"  data-toggle="modal" data-shiftschedid="{{ $row->shift_schedule_id }}" style="padding-left: 5px;">
                                <button type='button' class='btn btn-danger'><i class='now-ui-icons ui-1_simple-remove'></i></button>
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
  <div id="tbl_shift_sched_list_pagination" class="col-md-12 text-center" style="text-align: center;">
   {{ $data->links() }}
  </div>
</center>


