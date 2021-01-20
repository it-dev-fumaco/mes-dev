<div class="table-responsive" style="font-size:13px;">

    <table class="table table-striped">
              <col style="width: 10%;">
              <col style="width: 70%;">
              <col style="width: 20%;">
            <thead class="text-primary" style="font-size: 10px;font-weight: bold;">
              <th class="text-center"><b>No.</b></th>
              <th class="text-center"><b>Reason/s for Cancellation</b></th>                                
              <th class="text-center"><b>Action(s)</b></th>
            </thead>
            <tbody>
              @forelse($list as $index => $row)
                <tr>
                <td>{{ $row->reason_for_cancellation_id }}</td>
                <td class="text-left">{{ $row->reason_for_cancellation }}</td>
                <td>
                  <button type='button' class='btn btn-default hover-icon btn_edit_reason_for_cancellation' data-id="{{ $row->reason_for_cancellation_id }}" data-reason="{{ $row->reason_for_cancellation }}" >
                    <i class='now-ui-icons design-2_ruler-pencil'></i>
                  </button>  
                  <button type='button' class='btn btn-default hover-icon btn_delete_reason_for_cancellation' data-id="{{ $row->reason_for_cancellation_id }}" data-reason="{{ $row->reason_for_cancellation }}" >
                    <i class='now-ui-icons ui-1_simple-remove'></i>
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
          <center>
    <div id="reason_cancellation_pagination" class="col-md-12 text-center" style="text-align: center;">
    {{ $list->links() }}
    </div>
    </center>
    </div>
    