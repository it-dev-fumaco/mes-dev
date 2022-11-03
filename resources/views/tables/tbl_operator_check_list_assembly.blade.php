<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
  <col style="width: 5%;">
  <col style="width: 23.75%;">
  <col style="width: 23.75%;">
  <col style="width: 23.75%;">
  <col style="width: 23.75%;">
  <thead class="text-primary text-uppercase font-weight-bold">
    <th class="text-center p-2"><b>No.</b></th>
    <th class="text-center p-2"><b>Category</b></th>
    <th class="text-center p-2"><b>Reason</b></th>
    <th class="text-center p-2"><b>Material Type</b></th>
    <th class="text-center p-2"><b>Recommended Action</b></th>
  </thead>
  <tbody style="font-size: 12px;">
    @forelse($check_list as $index => $row)
    <tr>
      <td class="text-center p-2">{{ $row->reject_list_id }}</td>
      <td class="text-left p-2">{{ $row->reject_category_name }}</td>
      <td class="text-left p-2"> {{ $row->reject_reason }}</td>
      <td class="text-left p-2"> {{ $row->material_type }}</td>
      <td class="text-left p-2"> {{ $row->recommended_action }}</td>
    </tr>
      @empty
      <tr>
        <td colspan="6" class="text-center text-uppercase text-muted">No Record(s) Found.</td>
      </tr>
      @endforelse
  </tbody>
</table>
<div id="operator_checklist_list_pagination_assembly" class="col-md-12 text-center">{{ $check_list->links() }}</div>