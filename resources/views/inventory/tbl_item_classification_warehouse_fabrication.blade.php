<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
  <col style="width: 10%;">
  <col style="width: 20%;">
  <col style="width: 30%;">
  <col style="width: 30%;">
  <col style="width: 10%;">
  <thead class="text-primary text-uppercase font-weight-bold">
    <th class="p-2 text-center"><b>No.</b></th>
    <th class="p-2 text-center"><b>Operation</b></th>
    <th class="p-2 text-center"><b>Item Classification</b></th>
    <th class="p-2 text-center"><b>Warehouse</b></th>
    <th class="p-2 text-center"><b>Action(s)</b></th>
  </thead>
  <tbody style="font-size: 13px;">
    @forelse($item_classification as $row)
    <tr>
      <td class="p-2 text-center">
        <span>{{ $row->item_classification_warehouse_id }}</span>
      </td>
      <td class="p-2 text-left font-weight-bold">{{ $row->operation_name }}</td>
      <td class="p-2 text-left">{{ $row->item_classification }}</td>
      <td class="p-2 text-left">{{ $row->warehouse }}</td>
      <td class="p-2 text-center">
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon edit-itemclassware-btn' data-itemclass="{{$row->item_classification }}" data-id="{{$row->item_classification_warehouse_id }}" data-sware="{{$row->warehouse }}" data-operation="{{$row->operation_id}}">
          <i class='now-ui-icons design-2_ruler-pencil'></i>
        </button>
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon delete-itemclassware-btn'  data-itemclass="{{$row->item_classification }}" data-id="{{$row->item_classification_warehouse_id }}" data-sware="{{$row->warehouse }}" data-operation="{{$row->operation_id}}">
          <i class='now-ui-icons ui-1_simple-remove'></i>
        </button>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="5" class="text-center text-uppercase text-muted">No Item Warehouse Setup Found.</td>
    </tr>
    @endforelse 
  </tbody>
</table>

<div class="col-md-12 text-center" id="item-classification-warehouse-fabrication-pagination">{{ $item_classification->links() }}</div>