{{-- <table class="table table-hover" id="item-list" style="font-size: 9pt;">
  <col style="width: 10%;">
  <col style="width: 30%;">
  <col style="width: 5%;">
  <col style="width: 10%;">
  <col style="width: 10%;">
  <col style="width: 10%;">
  <col style="width: 10%;">
  <col style="width: 5%;">
  <thead class="text-primary">
    <th class="text-center"><b>SO No.1</b></th>
    <th class="text-center"><b>Item Description</b></th>
    <th class="text-center"><b>UoM</b></th>
    <th class="text-center"><b>Ordered Qty</b></th>
    <th class="text-center"><b>Stock Availability</b></th>
    <th class="text-center"><b>BOM No.</b></th>
    <th class="text-center"><b>RFD No.</b></th>
    <th class="text-center"><b>Action</b></th>
  </thead>
  <tbody>
    @forelse($item_list as $idx => $item)
    <tr>
      <td class="text-center"><b>{{ $item['sales_order'] }}</b></td>
      <td class="text-justify">
        <span style="display: none;">{{ $item['idx'] }}</span>
        <b>{{ $item['item_code'] }}</b><br>{!! $item['description'] !!}
        <br><br><b>{{ $item['item_classification'] }}</b>
      </td>
      <td class="text-center">{{ $item['uom'] }}</td>
      <td class="text-center" style="font-size: 12pt; font-weight: bolder;"><span>{{ number_format($item['qty']) }}</span></td>
      <td class="text-center" style="font-size: 12pt; font-weight: bolder;">{{ number_format($item['stock']) }}</td>
      <td class="text-center">
        <a href="#" class="{{ ($item['bom'] != '-- No BOM --') ? 'view-bom' : '' }}" data-id="{{ $item['bom'] }}">
          <b>{{ $item['bom'] }}</b>
        </a>
      </td>
      <td class="text-center">{{ $item['rfd_no'] }}</td>
      <td class="td-actions text-center">
        <button type="button" rel="tooltip" class="btn btn-danger delete-row">
          <i class="now-ui-icons ui-1_simple-remove"></i>
        </button>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="9" class="text-center">No Sales Order(s) Found.</td>
    </tr>
    @endforelse
  </tbody>
</table> --}}