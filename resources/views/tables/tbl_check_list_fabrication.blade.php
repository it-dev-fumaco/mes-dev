<div class="table-responsive">
  <table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
    <col style="width: 5%;">
    <col style="width: 20%;">
    <col style="width: 20%;">
    <col style="width: 20%;">
    <col style="width: 20%;">
    <col style="width: 15%;">
    <thead class="text-primary text-uppercase font-weight-bold" style="font-size: 5pt;">
      <th class="text-center p-2"><b>No.</b></th>
      <th class="text-center p-2"><b>Workstation</b></th>
      <th class="text-center p-2"><b>Category</b></th>
      <th class="text-center p-2"><b>Checklist</b></th>
      <th class="text-center p-2"><b>Reason</b></th>
      <th class="text-center p-2"><b>Action(s)</b></th>
    </thead>
    <tbody>
      @forelse($check_list as $index => $row)
      <tr>
        <td class="text-center p-2">{{ $row->qa_checklist_id }}</td>
        <td class="text-center p-2">{{ $row->workstation_name }}</td>
        <td class="text-center p-2"> {{ $row->reject_category_name }}</td>
        <td class="text-center p-2"> {{ $row->reject_checklist }}</td>
        <td class="text-center p-2"> {{ $row->reject_reason }}</td>
        <td class="text-center p-2">
          <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default btn-delete-checklist hover-icon' data-toggle="modal" data-id="{{$row->qa_checklist_id}}" data-workstation="{{$row->workstation_name}}" data-rejectchecklist="{{$row->reject_checklist}}" data-operation="{{$row->operation_name}}"><i class='now-ui-icons ui-1_simple-remove'></i></button>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="text-center text-uppercase text-muted">No Record(s) Found.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
  <div id="checklist_list_pagination_fabrication" class="col-md-12 text-center">{{ $check_list->links() }}</div>
</div>