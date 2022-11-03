<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
  <col style="width: 10%;">
  <col style="width: 80%;">
  <col style="width: 10%;">
  <thead class="text-primary text-uppercase font-weight-bold">
    <th class="p-2 text-center"><b>No.</b></th>
    <th class="p-2 text-center"><b>Reschedule Delivery Reason</b></th>                                
    <th class="p-2 text-center"><b>Action(s)</b></th>
  </thead>
  <tbody style="font-size:13px;">
    @forelse($list as $index => $row)
    <tr>
      <td class="p-2 text-center">{{ $row->reschedule_reason_id }}</td>
      <td class="p-2 text-left">{{ $row->reschedule_reason }}</td>
      <td class="p-2 text-center">
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon btn_edit_late_delivery' data-id="{{ $row->reschedule_reason_id }}" data-reason="{{ $row->reschedule_reason }}" >
          <i class='now-ui-icons design-2_ruler-pencil'></i>
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
<div id="late_delivery_pagination" class="col-md-12 text-center">{{ $list->links() }}</div>