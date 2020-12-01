<div class="table-responsive" style="font-size: 9pt;">
                    
                    <table class="table table-striped text-center">
                      <thead class="text-primary">
                        <th class="text-center"><b>No.</b></th>
                        <th class="text-center"><b>Machine Code</b></th>
                        <th class="text-center"><b>Machine Name</b></th>
                        <th class="text-center"><b>Action/s</b></th>
                      </thead>
                      <tbody>
                        @foreach($data as $row)
                          <tr>
                            <td>{{ $row->idx }}</td>
                            <td>{{ $row->machine_code }}</td>
                            <td>{{ $row->machine_name }}</td>
                            <td class="text-center"><a href="#" class="hover-icon"  data-toggle="modal" data-target="#delete-machineworkstation-{{ $row->workstation_machine_id }}-modal">
                                 <button type='button' class='btn btn-danger btn-sm'><i class='now-ui-icons ui-1_simple-remove' style="font-size: 8pt;font-weight: bold;"></i></button>
                              </a>                                                       @include('modals.delete_machineWorkstation_modal')
                              </td>

                              <td class='text-center'></td>

                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  <center>
  <div id="machineWorkstation_pagination" class="col-md-12 text-center" style="text-align: center;">
   {{ $data->links() }}
  </div>
</center>

                  </div>