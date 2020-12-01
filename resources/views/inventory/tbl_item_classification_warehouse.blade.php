<div class="table-responsive" style="font-size: 11px;">
  <table class="table table-striped text-center">
      <col style="width: 5%;">
      <col style="width: 15%;">
      <col style="width: 23%;">
      <col style="width: 22%;">
      <col style="width: 22%;">
      <col style="width: 13%;">
    <thead class="text-primary">
      <th class="text-center"><b>No.</b></th>
      <th class="text-left"><b>Operation</b></th>
      <th class="text-left"><b>Item Classification</b></th>
      <th class="text-left"><b>Source Warehouse</b></th>
      <th class="text-left"><b>Target Warehouse</b></th>
      <th class="text-center"><b>Action(s)</b></th>
    </thead>
    <tbody style="font-size:13px;">
      @forelse($item_classification as $row)
      <tr>

        <td class="text-center">
          <span>{{ $row->item_classification_warehouse_id }}</span>
        </td>
        <td class="text-left">{{ $row->operation_name }}</td>
        <td class="text-left">{{ $row->item_classification }}</td>
        <td class="text-left">{{ $row->source_warehouse }}</td>
        <td class="text-left">{{ $row->target_warehouse }}</td>
        <td class="text-center">
          <button type='button' class='btn btn-default hover-icon edit-itemclassware-btn' data-itemclass="{{$row->item_classification }}" data-id="{{$row->item_classification_warehouse_id }}" data-sware="{{$row->source_warehouse }}" data-tware="{{$row->target_warehouse }}" data-operation="{{$row->operation_id}}">
            <i class='now-ui-icons design-2_ruler-pencil'></i>
          </button>
          <button type='button' class='btn btn-default hover-icon delete-itemclassware-btn'  data-itemclass="{{$row->item_classification }}" data-id="{{$row->item_classification_warehouse_id }}" data-sware="{{$row->source_warehouse }}" data-tware="{{$row->target_warehouse }}" data-operation="{{$row->operation_id}}">
            <i class='now-ui-icons ui-1_simple-remove'></i>
          </button>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6">No Item Warehouse Setup Found.</td>
      </tr>
      @endforelse 
    </tbody>
  </table>
  
  <center>
    <div class="col-md-12 text-center" id="item-classification-warehouse-pagination">
     {{ $item_classification->links() }}
    </div>
  </center>
</div>