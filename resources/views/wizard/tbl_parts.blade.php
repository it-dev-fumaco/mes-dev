<table class="table table-hover table-bordered" id="parts-list" style="font-size: 8pt;">
  <col style="width: 10%;">
  <col style="width: 10%;">
  <col style="width: 40%;">
  <col style="width: 10%;">
  <col style="width: 10%;">
  <col style="width: 10%;">
  <col style="width: 10%;">
  <thead class="text-white bg-secondary" style="font-size: 8pt;">
    <th class="text-center"><b>Parent Code</b></th>
    <th class="text-center"><b>Part Code</b></th>
    <th class="text-center"><b>Item Description</b></th>
    <th class="text-center"><b>Ordered Qty</b></th>
    <th class="text-center"><b>Cycle Time</b></th>
    <th class="text-center"><b>Review BOM</b></th>
    <th class="text-center"><b>Action</b></th>
  </thead>
  <tbody style="font-size: 9pt;">
    @forelse($parts as $idx => $item)
    @php 
    $qty = $item['planned_qty'];
    $qty = ($qty < 0) ? 0 : $qty;
    $production_balance_qty = $qty - $item['production_order_qty'];
    $production_balance_qty = ($production_balance_qty < 0) ? 0 : $production_balance_qty;
    @endphp
    <tr>
      <td class="text-center">
        <span class="actual-qty" style="display: none">{{ $item['available_stock'] }}</span>
        <span class="sub-parent-item" style="display: none;">{{ $item['sub_parent_item'] }}</span>
        <span class="qty" style="display: none;">{{ $qty }}</span>
        @isset($item['available_qty'])
          <span class="available-qty" style="display: none;">{{ $item['available_qty'] }}</span>
        @endisset
        <span class="planned-start-date" style="display: none;">{{ $item['planned_start_date'] }}</span>
        <span class="wip-warehouse" style="display: none;">{{ $item['wip_warehouse'] }}</span>
        <span class="fg-warehouse" style="display: none;">{{ $item['fg_warehouse'] }}</span>
        <span class="production-order" style="display: none;">{{ $item['production_order'] }}</span>
        <span class="production-balance-qty" style="display: none;">{{ $production_balance_qty }}</span>
        <span class="parent-code font-weight-bold">{{ $item['parent_item'] }}</span><b></b><span style="display: none;" class="reference-no">{{ $item['reference_no'] }}</span>
        <span class="item-classification" style="display: none;">{{ $item['item_classification'] }}</span>
        <span class="s-warehouse" style="display: none;">{{ $item['s_warehouse'] }}</span>
        <span class="item-reference-id" style="display: none;">{{ $item['item_reference_id'] }}</span>
        <span class="delivery-date" style="display: none;">{{ $item['delivery_date'] }}</span>
        <span class="production-order-reference" style="display: none;">{{ $item['production_references'] }}</span>
        <span class="po-ref-qty" style="display: none;">@foreach ($item['po_ref_qty'] as $s => $r){{ $s . '=' . ($r * 1) }},@endforeach</span>
      </td>
      <td class="text-center"><b>{{ $item['item_code'] }}</b></td>
      <td class="text-justify">{!! $item['description'] !!}</td>
      <td class="text-center" style="font-size: 12pt; font-weight: bolder;">{{ number_format($item['planned_qty']) }}</td>
      <td class="text-center" style="font-size: 12pt; font-weight: bolder;">{{ $item['cycle_time'] }}</td>
      <td class="text-center">
        @php
        $icon = ($item['bom_reviewed'] == 1) ? 'now-ui-icons ui-1_check text-success' : 'unchecked';
        $style = ($item['bom_reviewed'] == 1) ? 'style="font-size: 12pt;"' : 'style="display: none;"';
        @endphp
        {{--  <i class="{{ $icon }}" {{ $style }} id="bom{{ $item['bom'] }}"></i>  --}}
        <i class="unchecked" id="{{ $idx }}bom{{ $item['bom'] }}"></i>
        @if($item['bom'])
        <a href="#" class="review-bom-row" data-bom="{{ $item['bom'] }}" data-idx="{{ $idx }}">{{ $item['bom'] }}</a>
        @else
        <a href="#" >-- No BOM --</a>
        @endif
      </td>
      <td class="text-center">
        {{-- <button type="button" class="btn btn-primary create-production-btn" data-id="{{ $item['item_code'] }}{{ $idx }}" data-so="{{ $item['sales_order'] }}" data-bom="{{ $item['bom'] }}" data-item="{{ $item['item_code'] }}" data-planned-qty="{{ number_format($item['planned_qty']) }}">
          <i class="now-ui-icons ui-1_simple-add"></i> Create Production Order
        </button> --}}
        <button type="button" rel="tooltip" class="btn btn-danger delete-row">
          <i class="now-ui-icons ui-1_simple-remove"></i>
        </button>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="9" class="text-center font-weight-bold text-danger" style="font-size: 15pt;">
          No Item(s) Found.<br> Please specify operations in BOM for this item; check BOM in ERP.
      </td>
    </tr>
    @endforelse
  </tbody>
</table>