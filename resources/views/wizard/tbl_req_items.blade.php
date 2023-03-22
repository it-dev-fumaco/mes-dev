<table class="table table-hover table-bordered" id="req-items-tbl" border="1">
  <col style="width: 8%;">
  <col style="width: 5%;">
  <col style="width: 30%;">
  <col style="width: 12%;">
  <col style="width: 8%;">
  <col style="width: 7%;">
  <col style="width: 7%;">
  <col style="width: 7%;">
  <col style="width: 7%;">
  <tbody style="font-size: 9pt;">
    <tr class="text-white bg-secondary">
      <th class="text-center" rowspan="2"><b>Prod. Order</b></th>
      <th class="text-center" rowspan="2" colspan="2"><b>Item Description</b></th>
      <th class="text-center" rowspan="2"><b>Material Warehouse</b></th>
      <th class="text-center" rowspan="2"><b>Required Qty</b></th>
      <th class="text-center" rowspan="2"><b>Available Scrap</b></th>
      <th class="text-center" rowspan="2"><b>Current Stock</b></th>
      <th class="text-center" colspan="2"><b>Projected Qty</b></th>
      <th class="text-center" rowspan="2"><b>Actions</b></th>
    </tr>
    <tr class="text-white bg-secondary">
      <th class="text-center"><b>Balance</b></th>
      <th class="text-center"><b>Scrap</b></th>
    </tr>
    @forelse($req_items as $idx => $item)
    @php
      if (filter_var((float)$item['required_qty'], FILTER_VALIDATE_INT)) {
          $required_qty = number_format($item['required_qty']);
      } else {
          $required_qty = number_format($item['required_qty'], 4);
      }

      if (filter_var((float)$item['qty_source_warehouse'], FILTER_VALIDATE_INT)) {
          $qty_source_warehouse = number_format($item['qty_source_warehouse']);
      } else {
          $qty_source_warehouse = number_format($item['qty_source_warehouse'], 4);
      }

      $lblcolorstock = ($item['qty_source_warehouse'] < $item['required_qty']) ? 'color: red;' : 'color:green;';
      $lblcolorbal = ($item['balance_qty'] <= 0) ? 'color: red;' : 'color:green;';
      
      $item_classes = ['Raw Material'];
      $item_row = (in_array($item['item_group'], $item_classes) && $item['qty_source_warehouse'] < $item['required_qty']) ? 'item-row' : '';
    @endphp
    <tr class="tr-{{ $item['production_order'] }} {{ $item_row }}">
      <td class="text-center">
        <span class="production-order">{{ $item['production_order'] }}</span>
        @php
        $img = ($item['item_image']) ? "/img/" . $item['item_image'] : "/icon/no_img.png";
        @endphp
        <span class="reference-no" style="display: none;">{{ $item['sales_order'] }}{{ $item['material_request'] }}</span>
      </td>
      <td class="text-center">
        <a href="http://athenaerp.fumaco.local/storage/{{ $img }}" data-toggle="lightbox">
          <img src="http://athenaerp.fumaco.local/storage/{{ $img }}" class="img-thumbnail" width="100">
        </a>
      </td>
      <td class="text-justify pl-3">
        <span class="d-block font-weight-bold">{{ $item['production_item'] }}</span>
        <span class="d-block">{{ strtok($item['production_item_description'], ',') }} {{ $item['attr'] }}</span>
        <br>
        <span class="item-code"><b>{{ $item['item_code'] }}</b></span> - {{ $item['item_classification'] }}<br>
        <span class="item-description">{{ $item['description'] }}</span>
      </td>
      <td class="text-center">
        <div class="form-group" style="margin: 0;">
          <select class="form-control form-control-lg source-wh-select">
            @foreach($item['s_warehouses'] as $wh)
            <option value="{{ $wh }}" {{ ($wh == $item['source_warehouse']) ? 'selected' : '' }}>{{ $wh }}</option>
            @endforeach
          </select>
        </div>
      </td>
      {{--  <td class="text-center">  --}}
        <span class="wip-warehouse d-none">{{ $item['wip_warehouse'] }}</span>
      {{--  </td>  --}}
      <td class="text-center">
        <b>
          <span class="req-qty" style="font-size: 11pt; display: none;">{{ $item['required_qty'] }}</span>
          <span style="font-size: 10pt; display: block;">{{ $required_qty }}</span>
          {{ $item['stock_uom'] }}<br>
        </b>
        @if($item['no_of_sheets'] != 'N/A')
        <span class="badge badge-info no-of-sheets" style="font-size: 8pt;">{{ $item['no_of_sheets'] }}</span>
        @endif
      </td>
      <td class="text-center">
        <span style="font-size: 11pt;">{{ $item['available_scrap_count'] }}</span>
        <span class="d-block">Cubic MM</span>
      </td>
      <td class="text-center" style="{{$lblcolorstock}}">
        <b>
          <span class="stock-qty" style="font-size: 11pt;">{{ ($qty_source_warehouse > 0) ? $qty_source_warehouse : number_format($item['qty_source_warehouse']) }}</span>
          <br><span class="uom-stock">{{ $item['stock_uom'] }}</span>
        </b><br>
        @if($item['current_stock_in_sheets'] != 'N/A')
        <span class="badge badge-info" style="font-size: 8pt;">{{ $item['current_stock_in_sheets'] }}</span>
        @endif
      </td>
      <td class="text-center" style="{{$lblcolorbal}}">
        <b><span class="balance-qty" style="font-size: 11pt;">{{ $item['balance_qty'] }}</span><br><span class="stock-uom uom-bal">{{ $item['stock_uom'] }}</span></b>
        @if($item['balance_in_sheets'] != 'N/A')
        <span class="badge badge-info" style="font-size: 8pt;">{{ $item['balance_in_sheets'] }}</span>
        @endif
      </td>
      <td class="text-center">
        @if($item['balance_in_sheets'] != 'N/A')
        <span class="d-block" style="font-size: 11pt;">{{ ($item['projected_scrap_in_kg'] && $item['projected_scrap_in_kg'] > 0) ? $item['projected_scrap_in_kg'] : 0 }}</span>
        <span class="projected-scrap-in-cubic-mm" style="display: none;">{{ ($item['projected_scrap'] && $item['projected_scrap'] > 0) ? $item['projected_scrap'] : 0 }}</span>
        <span class="badge badge-info" style="font-size: 8pt;">{{ ($item['projected_scrap'] && $item['projected_scrap'] > 0) ? $item['projected_scrap'] : 0 }} Cubic Meter</span>
        @endif
      </td>
      <td class="td-actions text-center">
        @if($item['ste'])
        <button type="button" rel="tooltip" class="btn btn-success ste-btn">
          <i class="now-ui-icons ui-1_check"></i> {{ $item['ste'] }}
        </button>
        @elseif($item['available_scrap_count'] > 0)
        <button type="button" rel="tooltip" class="btn btn-primary get-scrap-btn ste-btn" data-production-order="{{ $item['production_order'] }}" data-req-url="{{ $url }}">
          <i class="now-ui-icons ui-1_simple-add"></i> Withdrawal Slip
        </button>
        @else
        <button type="button" rel="tooltip" class="btn btn-primary create-ste-btn ste-btn">
          <i class="now-ui-icons ui-1_simple-add"></i> Withdrawal Slip
        </button>
        @endif
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="9" class="text-center">No Item(s) Found.</td>
    </tr>
    @endforelse
  </tbody>
</table>