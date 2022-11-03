<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
  <col style="width: 5%;">
  <col style="width: 43%;">
  <col style="width: 42%;">
  <col style="width: 10%;">
  <thead class="text-primary text-uppercase font-weight-bold">
    <th class="p-2 text-center"><b>ID</b></th>
    <th class="p-2 text-center"><b>Operation</b></th>
    <th class="p-2 text-center"><b>Description</b></th>
    <th class="p-2 text-center"><b>Action</b></th>
  </thead>
  <tbody style="font-size:13px;">
    @forelse($shift_list as $row)
    <tr>
      <td class="p-2 text-center">{{ $row->operation_id }}</td>
      <td class="p-2 text-left">{{ $row->operation_name }}</td>
      <td class="p-2 text-left">{{ $row->description }}</td>
      <td class="p-2 text-center">
          <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default ui-1_zoom-bold hover-icon edit-operation-list' data-toggle="modal" data-operationid="{{ $row->operation_id }}" data-operationname="{{ $row->operation_name }}" data-operationdesc="{{ $row->description }}"><i class='now-ui-icons design-2_ruler-pencil'></i></button>
      </td>
    </tr>
    @empty
    <tr>
        <td colspan="4" class="text-center text-uppercase text-muted">No Record(s) Found.</td>
    </tr>
    @endforelse
  </tbody>
</table>        
<div id="tbl_operation_list_pagination" class="col-md-12 text-center">{{ $shift_list->links() }}</div>