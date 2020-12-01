<div class="table-responsive" style="font-size:11px;">
                    <table class="table table-striped text-center" >
                        <col style="width: 5%;">
                        <col style="width: 40%;">
                        <col style="width: 36%;">
                        <col style="width: 19%;">
                      <thead class="text-primary">
                        <th class="text-center"><b>No</b></th>
                        <th class="text-left"><b>Process</b></th>
                        <th class="text-left"><b>Remarks</b></th>
<!--                         <th class="text-center"><b>Production Line</b></th>
 -->                     <th class="text-center"><b>Action(s)</b></th>
                      </thead>
                      <tbody style="font-size:13px;">
                         @forelse($data as $index => $row)
                           <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                           <td class="text-left">{{ $row->process_name }}</td>
                          
                           <td class="text-left">{{ $row->remarks }}</td>
                           <td class="text-center">
                            
                              <a href="#" class="hover-icon edit-modal-process-profile" data-toggle="modal" data-processid="{{ $row->process_id }}" data-process="{{ $row->process_name }}" data-remarks="{{ $row->remarks }}" style="padding-left: 5px;" data-color="{{ $row->color_legend }}">
                                <button type='button' class='btn btn-default ui-1_zoom-bold'><i class='now-ui-icons design-2_ruler-pencil'></i></button>
                              </a>

                              <a href="#" class="hover-icon btn-modal-process-profile" data-toggle="modal" data-processid="{{ $row->process_id }}" data-process="{{ $row->process_name }}" style="padding-left: 5px;">
                                <button type='button' class='btn btn-default ui-1_zoom-bold'><i class='now-ui-icons ui-1_zoom-bold'></i></button>
                              </a>
                              <a href="#" class="hover-icon btn-delete-process-setup-list"  data-toggle="modal" data-processid="{{ $row->process_id }}" data-process="{{ $row->process_name }}" style="padding-left: 5px;">
                                <button type='button' class='btn btn-default'><i class='now-ui-icons ui-1_simple-remove'></i></button>
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
                  </div>
                  <center>
  <div id="tbl_process_setup_list_pgination" class="col-md-12 text-center" style="text-align: center;">
   {{ $data->links() }}
  </div>
</center>