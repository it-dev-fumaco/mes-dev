<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
  <thead class="text-primary text-uppercase font-weight-bold">
    <th class="p-2 text-center"><b>ID</b></th>
    <th class="p-2 text-center"><b>Time in</b></th>
    <th class="p-2 text-center"><b>Time out</b></th>
    <th class="p-2 text-center"><b>Shift type</b></th>
    <th class="p-2 text-center"><b>Operation</b></th>
    <th class="p-2 text-center"><b>Action/s</b></th>
  </thead>
  <tbody style="font-size: 13px;">
    @forelse($shift_list as $row)
    <tr>
      <td class="p-2 text-center">{{ $row->shift_id }}</td>
      <td class="p-2 text-center">{{ $row->time_in }}</td>
      <td class="p-2 text-center">{{ $row->time_out }}</td>
      <td class="p-2 text-center">{{ $row->shift_type }}</td>
      <td class="p-2 text-center">{{ $row->operation_name }}</td>
      <td class="p-2 text-center">        
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 hover-icon  edit-shift-list btn-default ui-1_zoom-bold' data-toggle="modal" data-timein="{{ $row->time_in }}" data-timeout="{{ $row->time_out }}" data-shifttype="{{ $row->shift_type }}" data-qtycapacity="{{ $row->qty_capacity }}" data-remarks="{{ $row->remarks }}" data-shiftid="{{ $row->shift_id }}" data-breakinmin="{{ $row->breaktime_in_mins}}" data-operation="{{ $row->operation_id }}"><i class='now-ui-icons design-2_ruler-pencil'></i></button>
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 hover-icon delete-shift-list btn-default' data-toggle="modal" data-shiftid="{{ $row->shift_id }}"><i class='now-ui-icons ui-1_simple-remove'></i></button>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="6" class="text-center text-uppercase text-muted">No Record(s) Found.</td>
    </tr>
    @endforelse
  </tbody>
</table>
<div id="tbl_shift_list_pagination" class="col-md-12 text-center">{{ $shift_list->links() }}</div>