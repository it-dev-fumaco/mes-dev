<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
  <col style="width: 5%;">
  <col style="width: 14%;">
  <col style="width: 14%;">
  <col style="width: 14%;">
  <col style="width: 12%;">
  <col style="width: 14%;">
  <col style="width: 14%;">
  <col style="width: 13%;">
  <thead class="text-primary text-uppercase font-weight-bold">
    <th class="text-center p-2"><b>No.</b></th>
    <th class="text-center p-2"><b>Operation</b></th>
    <th class="text-center p-2"><b>Reject Category</b></th>
    <th class="text-center p-2"><b>Reject Reason</b></th> 
    <th class="text-center p-2"><b>Responsible</b></th> 
    <th class="text-center p-2"><b>Material Type</b></th> 
    <th class="text-center p-2"><b>Recommended Action</b></th>                                  
    <th class="text-center p-2"><b>Action(s)</b></th>
  </thead>
  <tbody style="font-size: 12px;">
    @forelse($reject_list as $index => $row)
    <tr>
      <td class="text-center p-2">{{ $row->reject_list_id }}</td>
      <td class="text-center p-2">{{ $row->operation_name }}</td>
      <td class="text-center p-2">{{ $row->reject_category_name }}</td>
      <td class="text-center p-2">{{ $row->reject_reason }}</td>
      <td class="text-center p-2">{{ $row->responsible }}</td>
      <td class="text-center p-2">{{ $row->material_type }}</td>
      <td class="text-center p-2">{{ $row->recommended_action }}</td>
      <td class="text-center p-2">
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon edit-rejectlist-btn' data-id="{{ $row->reject_list_id }}" data-ctgID="{{ $row->reject_category_id }}" data-rjtlist="{{ $row->reject_checklist }}" data-rjtreason="{{ $row->reject_reason }}" data-responsible="{{$row->responsible}}" data-owner="{{ $row->owner }}" data-action="{{ $row->recommended_action }}" data-mtype="{{$row->reject_material_type_id}}" data-opoperation="{{$row->operation_id}}" data-reloadtbl="Operator">
          <i class='now-ui-icons design-2_ruler-pencil'></i>
        </button>
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon btn-delete-rejectlist' data-id="{{ $row->reject_list_id }}" data-ctgID="{{ $row->reject_category_id }}" data-rjtlist="{{ $row->reject_checklist }}" data-rjtreason="{{ $row->reject_reason }}"  data-reloadtbl="Operator"><i class='now-ui-icons ui-1_simple-remove'></i></button>
      </td>
    </tr> 
    @empty
    <tr>
      <td colspan="8" class="text-center text-uppercase text-muted">No Record(s) Found.</td>
    </tr>
    @endforelse
  </tbody>
</table>
<div id="op_reject_check_list_pagination" class="col-md-12 text-center">{{ $reject_list->appends(request()->input())->links() }}</div>