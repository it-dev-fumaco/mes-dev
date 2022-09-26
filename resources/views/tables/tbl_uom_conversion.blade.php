<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
  <col style="width: 10%;">
  <col style="width: 10%;">
  <col style="width: 25%;">
  <col style="width: 10%;">
  <col style="width: 25%;">
  <col style="width: 20%;">
  <thead class="text-primary text-uppercase font-weight-bold">
    <th class="p-2 text-center"><b>No.</b></th>
    <th class="p-2 text-center"><b>Material</b></th>
    <th class="p-2 text-center" colspan="3"><b>UoM Conversion</b></th>
    <th class="p-2 text-center"><b>Action(s)</b></th>
  </thead>
  <tbody style="font-size: 13px;">
    @forelse($list as $row)
    <tr>
      <td class="p-2 text-center">
        <span class="uom-conversion-id">{{ $row['uom_conversion_id'] }}</span>
      </td>
      @foreach($row['uom_list'] as $uom)
      @if($loop->first)
      <td class="p-2 text-center font-weight-bold">
        <span class="uom-material-type">{{ $uom->material }}</span>
      </td>
      @endif
      <td class="p-2 text-center font-weight-bold">
        <span class="id" style="display: none;">{{ $uom->id }}</span>
        <span class="uom-id" style="display: none;">{{ $uom->uom_id }}</span>
        <span class="conversion_factor">{{ number_format($uom->conversion_factor, 8) }}</span>
        <span class="uom-name">{{ $uom->uom_name }}</span></b></td>
      @if($loop->first)
      <td class="p-2 text-center">
        <i class="now-ui-icons arrows-1_minimal-right"></i>
        <i class="now-ui-icons arrows-1_minimal-right"></i>
        <i class="now-ui-icons arrows-1_minimal-right"></i>
      </td>
      @endif
      @endforeach
      <td class="p-2 text-center">
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default edit-uom-conversion-btn'>
          <i class='now-ui-icons design-2_ruler-pencil'></i>
        </button>
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default delete-uom-conversion-btn'>
          <i class='now-ui-icons ui-1_simple-remove'></i>
        </button>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="5" class="text-center text-uppercase text-muted">No record(s) Found.</td>
    </tr>
    @endforelse 
  </tbody>
</table>

<div class="col-md-12 text-center" id="uom-conversion-pagination">{{ $list->links() }}</div>