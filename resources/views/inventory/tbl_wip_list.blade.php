<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
  <col style="width: 5%;">
  <col style="width: 15%;">
  <col style="width: 23%;">
  <col style="width: 13%;">
  <thead class="text-primary text-uppercase font-weight-bold">
    <th class="p-2 text-center"><b>No.</b></th>
    <th class="p-2 text-center"><b>Operation</b></th>
    <th class="p-2 text-center"><b>Work In Progress</b></th>
    <th class="p-2 text-center"><b>Action(s)</b></th>
  </thead>
  <tbody style="font-size: 13px;">
    @forelse($wip_list as $row)
    <tr>
      <td class="p-2 text-center">{{ $row->wip_id}}</td>
      <td class="p-2 text-center">
        <span>{{ $row->operation_name }}</span>
      </td>
      <td class="p-2 text-center">{{ $row->warehouse }}</td>
      <td class="p-2 text-center">
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon edit-wip-button' data-operation="{{$row->operation_id}}" data-ware="{{$row->warehouse}}" data-id="{{$row->wip_id}}">
          <i class='now-ui-icons design-2_ruler-pencil'></i>
        </button>
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon delete-wip-button' data-operation="{{$row->operation_id}}" data-ware="{{$row->warehouse}}" data-id="{{$row->wip_id}}" data-op="{{$row->operation_name}}">
          <i class='now-ui-icons ui-1_simple-remove'></i>
        </button>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="4" class="text-center text-uppercase text-muted">No Record Found.</td>
    </tr>
    @endforelse 
  </tbody>
</table>
<div class="col-md-12 text-center" id="wip_list_pagination">{{ $wip_list->links() }}</div>