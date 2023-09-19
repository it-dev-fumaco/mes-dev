<table class="table table-hover table-bordered" id="req-items-tbl">
  <col style="width: 8%;">
  <col style="width: 35%;">
  <col style="width: 13%;">
  <col style="width: 8%;">
  <col style="width: 7%;">
  <col style="width: 8%;">
  <col style="width: 8%;">
  <tbody style="font-size: 9pt;">
    @php
    $mr_allowed = ['SA - Sub Assembly', 'HO - Housing'];
    @endphp
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
  
      $arr_ind = $idx - 1;
      $arr_ind = ($arr_ind < 0) ? 0 : $arr_ind;
    @endphp

    @if($loop->first || $req_items[$arr_ind]['production_order'] != $item['production_order'])
    <tr class="text-white bg-secondary">
      <th class="text-center" colspan="7" style="font-size: 12pt;"><b>{{ $item['production_order'] }} - {{ $item['production_item'] }} {{ $item['production_item_name'] }}</b></th>
    </tr>
    <tr class="text-primary">
      <th class="text-center" colspan="2"><b>Item Description</b></th>
      <th class="text-center"><b>Source Warehouse</b></th>
      <th class="text-center"><b>Required Qty</b></th>
      <th class="text-center"><b>Current Stock</b></th>
      <th class="text-center"><b>Balance</b></th>
      <th class="text-center"><b>Actions</b></th>
    </tr>
    @endif
    <tr class="tr-{{ $item['production_order'] }} {{ $item_row }}">
      <td class="text-center">
        <span class="item-id" style="display: none;">{{ $item['id'] }}</span>
        <span class="item-name" style="display: none;">{{ $item['item_name'] }}</span>
        <span class="production-order d-none">{{ $item['production_order'] }}</span>
        @php
        $img = ($item['item_image']) ? "/img/" . explode('.', $item['item_image'])[0] . '.webp' : "/icon/no_img.png";
        @endphp
        <span class="reference-no" style="display: none;">{{ $item['sales_order'] }}{{ $item['material_request'] }}</span>
        <a href="http://athenaerp.fumaco.local/storage/{{ $img }}" data-toggle="lightbox">
          <img src="http://athenaerp.fumaco.local/storage/{{ $img }}" class="img-thumbnail" width="100">
        </a>
      </td>
      <td class="text-justify pl-3">
        <span class="item-code font-weight-bold"><b>{{ $item['item_code'] }}</b></span> - <span class="item-classification">{{ $item['item_classification'] }}</span><br>
        <span class="item-description">{{ $item['description'] }}</span>
        
      </td>
      <td class="text-center">
        <div class="form-group" style="margin: 0;">
          <select class="form-control form-control-lg source-wh-select font-weight-bold">
            @foreach($item['s_warehouses'] as $wh)
            <option value="{{ $wh }}" {{ ($wh == $item['source_warehouse']) ? 'selected' : '' }} data-item-group="{{ $item['item_group'] }}">{{ $wh }}</option>
            @endforeach
          </select>
        </div>
      </td>
      {{-- <td class="text-center"> --}}
        <span class="wip-warehouse d-none">{{ $item['wip_warehouse'] }}</span>
      {{-- </td> --}}
      <td class="text-center">
        <b>
          <span class="req-qty" style="font-size: 11pt; display: none;">{{ $item['required_qty'] }}</span>
          <span style="font-size: 10pt; display: block;">{{ $required_qty }}</span>
          <span class="stock-uom">{{ $item['stock_uom'] }}</span>
          <br>
        </b>
        @if($item['no_of_sheets'] != 'N/A')
        <span class="badge badge-info no-of-sheets" style="font-size: 8pt;">{{ $item['no_of_sheets'] }}</span>
        @endif
      </td>
      <td class="text-center" style="{{$lblcolorstock}}">
        <b>
          <span class="stock-qty" style="font-size: 11pt;">{{ ($qty_source_warehouse > 0) ? $qty_source_warehouse : number_format($item['qty_source_warehouse']) }}</span>
          <br><span class="uom-stock">{{ $item['stock_uom'] }}</span>
        </b>
      </td>
      <td class="text-center" style="{{$lblcolorbal}}">
        <b><span class="balance-qty" style="font-size: 11pt;">{{ $item['balance_qty'] }}</span>
          <br><span class="uom-bal">{{ $item['stock_uom'] }}</span></b>
      </td>
      <td class="td-actions text-center">
        @php
          $arr_ind = $idx - 1;
          $arr_ind = ($arr_ind < 0) ? 0 : $arr_ind;
        @endphp

        @if($loop->first || $req_items[$arr_ind]['production_order'] != $item['production_order'])
        @if($item['ste'] && Gate::any(['create-withdrawal-slip-for-production-orders-wo-bom', 'create-withdrawal-slip']))
        <button type="button" rel="tooltip" class="btn btn-success ste-btn">
          <i class="now-ui-icons ui-1_check"></i> {{ $item['ste'] }}
        </button>
        
        @else
        <button type="button" rel="tooltip" class="btn btn-primary create-ste-btn ste-btn">
          <i class="now-ui-icons ui-1_simple-add"></i> STE
        </button>
        @endif
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