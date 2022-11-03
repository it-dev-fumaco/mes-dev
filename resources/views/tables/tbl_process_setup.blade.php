<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
  <col style="width: 5%;">
  <col style="width: 40%;">
  <col style="width: 36%;">
  <col style="width: 19%;">
  <thead class="text-primary text-uppercase font-weight-bold">
    <th class="p-2 text-center"><b>No</b></th>
    <th class="p-2 text-center"><b>Process</b></th>
    <th class="p-2 text-center"><b>Remarks</b></th>
    <th class="p-2 text-center"><b>Action(s)</b></th>
  </thead>
  <tbody style="font-size: 13px;">
    @forelse($process_list as $index => $row)
    <tr>
      <td class="p-2 text-center">{{ $index + 1 }}</td>
      <td class="p-2 text-left">{{ $row->process_name }}</td>
      <td class="p-2 text-left">{{ $row->remarks }}</td>
      <td class="p-2 text-center">
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default ui-1_zoom-bold hover-icon edit-modal-process-profile' data-toggle="modal" data-processid="{{ $row->process_id }}" data-process="{{ $row->process_name }}" data-remarks="{{ $row->remarks }}" data-color="{{ $row->color_legend }}"><i class='now-ui-icons design-2_ruler-pencil'></i></button>
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default ui-1_zoom-bold hover-icon btn-modal-process-profile' data-toggle="modal" data-processid="{{ $row->process_id }}" data-process="{{ $row->process_name }}"><i class='now-ui-icons ui-1_zoom-bold'></i></button>
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon btn-delete-process-setup-list' data-toggle="modal" data-processid="{{ $row->process_id }}" data-process="{{ $row->process_name }}" ><i class='now-ui-icons ui-1_simple-remove'></i></button>
      </td>
    </tr>
    @empty
    <tr>
        <td colspan="4" class="text-center text-uppercase text-muted">No Record(s) Found.</td>
    </tr>
    @endforelse
  </tbody>
</table>
<div id="tbl_process_setup_list_pgination" class="col-md-12 text-center">{{ $process_list->links() }}</div>