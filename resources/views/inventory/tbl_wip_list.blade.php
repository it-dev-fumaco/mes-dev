<div class="table-responsive" style="font-size: 11px;">
  <table class="table table-striped text-center">
      <col style="width: 5%;">
      <col style="width: 15%;">
      <col style="width: 23%;">
      <col style="width: 13%;">
    <thead class="text-primary">
      <th class="text-center"><b>No.</b></th>
      <th class="text-center"><b>Operation</b></th>
      <th class="text-center"><b>Work In Progress</b></th>
      <th class="text-center"><b>Action(s)</b></th>
    </thead>
    <tbody style="font-size:13px;">
      @forelse($wip_list as $row)
      <tr>
        <td>{{ $row->wip_id}}</td>
        <td class="text-center">
          <span>{{ $row->operation_name }}</span>
        </td>
        <td class="text-center">{{ $row->warehouse }}</td>
        <td class="text-center">
          <button type='button' class='btn btn-default hover-icon edit-wip-button' data-operation="{{$row->operation_id}}" data-ware="{{$row->warehouse}}" data-id="{{$row->wip_id}}">
            <i class='now-ui-icons design-2_ruler-pencil'></i>
          </button>
          <button type='button' class='btn btn-default hover-icon delete-wip-button' data-operation="{{$row->operation_id}}" data-ware="{{$row->warehouse}}" data-id="{{$row->wip_id}}" data-op="{{$row->operation_name}}">
            <i class='now-ui-icons ui-1_simple-remove'></i>
          </button>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="4">No Record Found.</td>
      </tr>
      @endforelse 
    </tbody>
  </table>
  
  <center>
    <div class="col-md-12 text-center" id="wip_list_pagination">
     {{ $wip_list->links() }}
    </div>
  </center>
</div>