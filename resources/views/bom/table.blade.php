<div class="table-responsive">
  <table class="table table-striped">
    <col style="width: 5%;">
    <col style="width: 20%;">
    <col style="width: 40%;">
    <col style="width: 15%;">
    <col style="width: 10%;">
    <col style="width: 10%;">
    <thead class="text-primary">
      <th class="text-center font-weight-bold"><b>No.</b></th>
      <th class="text-center font-weight-bold"><b>BOM</b></th>
      <th class="text-center font-weight-bold"><b>Item</b></th>
      <th class="text-center font-weight-bold"><b>Status</b></th>
      <th class="text-center font-weight-bold"><b>Is Active</b></th>
      <th class="text-center font-weight-bold"><b>Actions</b></th>
    </thead>
    <tbody style="font-size: 10pt;">
      @forelse($bom_list as $i => $bom)
      <tr>
        <td class="text-center">{{ $i + $bom_list->firstItem() }}</td>
        <td class="text-center"><b>{{ $bom->name }}</b></td>
        <td class="text-left"><b>{{ $bom->item }}</b><br>{!! $bom->description !!}</td>
        <td class="text-center" style="font-size: 13pt;">
          @if($bom->docstatus == 1)
            @if($bom->is_default)
            <span class="badge badge-success">Default</span>
            @elseif($bom->is_active)
            <span class="badge badge-info">Active</span>
            @else
            <span class="badge badge-default">Inactive</span>
            @endif
          @endif
          @if($bom->docstatus == 2)
          <span class="badge badge-danger">Cancelled</span>
          @endif
          @if($bom->docstatus == 0)
          <span class="badge badge-danger">Draft</span>
          @endif
        </td>
        <td class="text-center" style="font-size: 15pt;">
          @if($bom->is_active)
          <i class="now-ui-icons ui-1_check text-success font-weight-bold"></i>
          @else
          <i class="now-ui-icons ui-1_simple-remove text-danger font-weight-bold"></i>
          @endif
        </td>
        <td class="text-center">
          <button type="button" class="btn btn-success view-bom-details-btn" data-bom="{{ $bom->name }}">
            <i class="now-ui-icons design-2_ruler-pencil"></i>
          </button>
        </td>
      </tr>
      @empty
      <tr>
        <td class="text-center" colspan="6">No BOM found.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

<center>
  <div class="col-md-12 text-center" id="bom-pagination">
   {{ $bom_list->links() }}
  </div>
</center>