<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
  <col style="width: 10%;">
  <col style="width: 70%;">
  <col style="width: 20%;">
  <thead class="text-primary text-uppercase font-weight-bold">
    <th class="p-2 text-center"><b>No.</b></th>
    <th class="p-2 text-center"><b>Reason/s for Cancellation</b></th>                                
    <th class="p-2 text-center"><b>Action(s)</b></th>
  </thead>
  <tbody style="font-size:13px;">
    @forelse($list as $index => $row)
    <tr>
      <td class="p-2 text-center">{{ $row->reason_for_cancellation_id }}</td>
      <td class="p-2 text-left">{{ $row->reason_for_cancellation }}</td>
      <td class="p-2 text-center">
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon btn_edit_reason_for_cancellation' data-id="{{ $row->reason_for_cancellation_id }}" data-reason="{{ $row->reason_for_cancellation }}" >
          <i class='now-ui-icons design-2_ruler-pencil'></i>
        </button>  
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon btn_delete_reason_for_cancellation' data-id="{{ $row->reason_for_cancellation_id }}" data-reason="{{ $row->reason_for_cancellation }}" >
          <i class='now-ui-icons ui-1_simple-remove'></i>
        </button>               
      </td>
    </tr> 
    @empty
    <tr>
      <td colspan="6" class="text-center text-uppercase text-muted">No Record(s) Found.</td>
    </tr>
    @endforelse
  </tbody>
</table>
<div id="reason_cancellation_pagination" class="col-md-12 text-center">{{ $list->links() }}</div>  