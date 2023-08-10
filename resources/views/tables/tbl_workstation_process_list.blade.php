<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
  <col style="width: 5%;">
  <col style="width: 46%;">
  <col style="width: 30%;">
  <col style="width: 19%;">
  <thead class="text-primary text-uppercase font-weight-bold">
    <th class="p-2 text-center"><b>No</b></th>
    <th class="p-2 text-center"><b>Workstation</b></th>
    <th class="p-2 text-center"><b>Last Modified By</b></th>
    <th class="p-2 text-center"><b>Action(s)</b></th>
  </thead>
  <tbody style="font-size: 13px;">
    @forelse($list as $index => $row)
    <tr>
      <td class="p-2 text-center">{{ $index + 1 }}</td>
      <td class="p-2 text-left">
        {{ $row->workstation_name }} <br>
        {{-- <small class="text-muted">Last modified by: {{ $row->last_modified_by }}</small> --}}
      </td>
      <td class="p-2 text-center">
        {{ $row->last_modified_by }}
      </td>
      <td class="p-2 text-center">
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default btn-edit-workstation-modal hover-icon' data-workstationid="{{ $row->workstation_id }}" data-workstationname="{{ $row->workstation_name }}" data-orderno="{{$row->order_no}}" data-operation="{{$row->operation_id}}" data-toggle="modal"><i class='now-ui-icons design-2_ruler-pencil'></i></button>
        <a href="/workstation_profile/{{ $row->workstation_id }}" class="hover-icon btn pb-2 pt-2 pr-3 pl-3 btn-default ui-1_zoom-bold" data-toggle="modal"><i class='now-ui-icons ui-1_zoom-bold'></i></a>
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default btn-delete-workstation hover-icon' data-toggle="modal" data-workstationid="{{ $row->workstation_id }}" data-workstationname="{{ $row->workstation_name }}" data-orderno="{{$row->order_no}}" data-operation="{{$row->operation}}"><i class='now-ui-icons ui-1_simple-remove'></i></button>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="4" class="text-center text-uppercase text-muted">No Record(s) Found.</td>
    </tr>
    @endforelse
  </tbody>
</table>
<div id="workstation_list_pagination" class="col-md-12 text-center">{{ $list->links() }}</div>