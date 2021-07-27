<div class="table-responsive" style="font-size:13px;">
    <table class="table table-striped">
              <col style="width: 20%;">
              <col style="width: 20%;">
              <col style="width: 20%;">
              <col style="width: 10%;">
              <col style="width: 20%;">
              <col style="width: 10%;">
            <thead class="text-primary" style="font-size: 10px;font-weight: bold;">
              <th class="text-center"><b>Workstation</b></th>
              <th class="text-center"><b>Process</b></th>                                
              <th class="text-center"><b>Completed Qty</b></th>
              <th class="text-center"><b>Status</b></th>
              <th class="text-center"><b>Remarks</b></th>
              <th class="text-center"><b>Action</b></th>
            </thead>
            <tbody>
              @forelse($list as $index => $row)
                <tr>
                <td class="text-center"><b>{{ $row->workstation }}</b></td>
                <td class="text-left">{{ $row->process_name }}</td>
                <td class="text-center">{{ $row->completed_qty }}</td>
                <td class="text-center">{{ $row->status }}</td>
                <td class="text-center">{{ $row->remarks }}</td>
                <td>
                  <button type='button' class='btn btn-default hover-icon btn_reset_workstation' data-id="{{ $row->job_ticket_id }}" data-prod="{{ $row->production_order }}" data-workstation="{{ $row->workstation }}" data-process="{{ $row->process_name }}">
                    Reset
                  </button>              
                </td>
                @empty
                <tr>
                  <td colspan="6" class="text-center">No Record(s) Found.</td>
                </tr>
                @endforelse
              </tr> 
            </tbody>
          </table>
    </div>
    