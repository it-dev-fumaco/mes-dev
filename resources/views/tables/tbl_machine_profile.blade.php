            <table class="table table-bordered" style="font-size: 12px; width: 100%;">
              <thead>
                <tr style="font-size: 10px;" class="text-center">
                  <th scope="col" style="font-weight: bold;"><b>#</b></th>
                  <th scope="col" style="font-weight: bold;"><b>MR No.</b></th>
                  <th scope="col" style="font-weight: bold;"><b>Reason</b></th>
                  <th scope="col" style="font-weight: bold;"><b>Status</b></th>
                  <th scope="col" style="font-weight: bold;"><b>Category</b></th>
                  <th scope="col" style="font-weight: bold;"><b>Date Reported</b></th>
                  <th scope="col" style="font-weight: bold;"><b>Duration</b></th>
                  
                </tr>
              </thead>
              <tbody>
                @forelse($data as $index => $rows)
                     
                     <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center"><a href="#" class="hover-icon"  data-toggle="modal" data-target="#machine_breakdownhistory-{{ $rows['id'] }}">{{ $rows['series'] }}</a> @include('modals.machine_breakdownhistory_details_modal')</td>
                        <td class="text-center">{{ $rows['reason'] }}</td>
                        <td class="text-center">{{ $rows['status'] }}</td>
                        <td class="text-center">{{ $rows['category'] }}</td>
                        <td class="text-center">{{ $rows['date_reported'] }}</td>
                        <td class="text-center">{{ $rows['duration'] }}</td>
                       
                     </tr>
                     
                     @empty
                     <tr>
                        <td colspan="10" class="text-center">No record found.</td>
                     </tr>
                     @endforelse
                
              </tbody>
            </table>
<center>
  <div id="machine_pagination" class="col-md-12 text-center" style="text-align: center;">
   {{ $data->links() }}
  </div>
</center>
