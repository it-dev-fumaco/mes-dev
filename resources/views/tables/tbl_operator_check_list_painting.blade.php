<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
  <col style="width: 5%;">
  <col style="width: 17.5%;">
  <col style="width: 22.5%;">
  <col style="width: 22.5%;">
  <col style="width: 23.5%;">
  <col style="width: 10%;">
  <thead class="text-primary text-uppercase font-weight-bold">
    <th class="text-center p-2"><b>No.</b></th>
    <th class="text-center p-2"><b>Workstation</b></th>
    <th class="text-center p-2"><b>Category</b></th>
    <th class="text-center p-2"><b>Process</b></th>
    <th class="text-center p-2"><b>Reason</b></th>
    <th class="text-center p-2"><b>Action(s)</b></th>
  </thead>
  <tbody style="font-size: 12px;">
    @forelse($check_list as $index => $row)
    <tr>
      <td class="text-center p-2">{{ $row->operator_reject_list_setup_id }}</td>
      <td class="text-left p-2">{{ $row->workstation_name }}</td>
      <td class="text-left p-2"> {{ $row->reject_category_name }}</td>
      <td class="text-left p-2"> {{ $row->process_name }}</td>
      <td class="text-left p-2"> {{ $row->reject_reason }}</td>
      <td class="text-center p-2">
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default btn-delete-opchecklist hover-icon' data-toggle="modal" data-id="{{$row->operator_reject_list_setup_id}}" data-workstation="{{$row->workstation_name}}" data-rejectchecklist="{{$row->reject_reason}}" data-operation="{{$row->operation_name}}" data-reloadtbl="Painting"><i class='now-ui-icons ui-1_simple-remove'></i></button>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="6" class="text-center text-uppercase text-muted">No Record(s) Found.</td>
    </tr>
    @endforelse
  </tbody>
</table>
<div id="operator_checklist_list_pagination_painting" class="col-md-12 text-center">{{ $check_list->links() }}</div>