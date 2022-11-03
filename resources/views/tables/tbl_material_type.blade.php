<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
  <col style="width: 5%;">
  <col style="width: 85%;">
  <col style="width: 10%;">
  <thead class="text-primary text-uppercase font-weight-bold">
    <th class="text-center p-2"><b>No.</b></th>
    <th class="text-center p-2"><b>Material Type</b></th>                                
    <th class="text-center p-2"><b>Action</b></th>
  </thead>
  <tbody style="font-size: 12px;">
    @forelse($list as $index => $row)
    <tr>
      <td class="text-center p-2">{{ $row->reject_material_type_id }}</td>
      <td class="text-left p-2">{{ $row->material_type }}</td>
      <td class="text-center p-2">
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon btn_edit_material_type' data-id="{{ $row->reject_material_type_id }}" data-reason="{{ $row->material_type }}" >
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
<div id="material_type_pagination" class="col-md-12 text-center">{{ $list->appends(request()->input())->links() }}</div>