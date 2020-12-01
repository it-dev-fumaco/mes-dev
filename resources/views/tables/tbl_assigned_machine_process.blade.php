
            <table class="table table-bordered" style="font-size: 12px; width: 100%;">
              <thead>
                <tr style="font-size: 10px;" class="text-center">
                  <th scope="col" style="font-weight: bold;"><b>No.</b></th>
                  <th scope="col" style="font-weight: bold;"><b>Workstation</b></th>
                  <th scope="col" style="font-weight: bold;"><b>Machine</b></th>
                  <th scope="col" style="font-weight: bold;"><b>Machine Name</b></th>
                  <th scope="col" style="font-weight: bold;"><b>Action</b></th>
                </tr>
              </thead>
              <tbody>
                @forelse($data as $index => $rows)
                     
                     <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $rows->workstation_name }}</td>
                        <td class="text-center">{{ $rows->machine_code }}</td>
                        <td class="text-center">{{ $rows->machine_name }}</td>

                        <td class="text-center">
                            <button type='button' class='btn btn-danger btn-sm remove-assigned-machine' data-id="{{ $rows->process_assignment_id }}" data-workstation="{{ $rows->workstation_name }}" data-machine="{{ $rows->machine_code }}"><i class='now-ui-icons ui-1_simple-remove' style="font-size: 8pt;font-weight: bold;"></i></button><input type="hidden" name="assigned_process_machine_id" value="{{ $rows->process_assignment_id }}">                                                       
                        </td>
                     </tr>
                     
                     @empty
                     <tr>
                        <td colspan="10" class="text-center">No record found.</td>
                     </tr>
                     @endforelse
                
              </tbody>
            </table>
<center>
  <div id="assigned_machine_process" class="col-md-12 text-center" style="text-align: center;">
   {{ $data->links() }}
  </div>
</center>
