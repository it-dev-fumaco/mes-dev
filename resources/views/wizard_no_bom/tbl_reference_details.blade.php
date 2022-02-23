<div id="so-details-tbl">
   <div class="text-center m-2">
      <span class="text-center text-uppercase" style="font-size: 12pt;"><b>Order Details - {{ $reference_details['name'] }}</b></span>
   </div>
   <hr style="margin: 0;">
   <table style="width: 90%; margin: 8px auto;">
      <col style="width: 8%;">
      <col style="width: 30%;">
      <col style="width: 15%;">
      <col style="width: 13%;">
      <col style="width: 6%;">
      <col style="width: 28%;">
      <tr>
         <td class="align-top"><b>Customer</b></td>
         <td class="align-top">{{ $reference_details['customer'] }}</td>
         <td class="align-top"><b>Transaction Date</b></td>
         <td class="align-top">{{ date('M-d-Y', strtotime($reference_details['transaction_date'])) }}</td>
         <td class="align-top"><b>Status</b></td>
         <td class="align-top"><b>{{ $reference_details['status'] }}</b></td>     
      </tr>
      <tr>
         <td class="align-top"><b>Sales Type</b></td>
         <td class="align-top">{{ $reference_details['sales_type'] }}</td>
         <td class="align-top"><b>Delivery Date</b></td>
         <td class="align-top">{{ date('M-d-Y', strtotime($reference_details['delivery_date'])) }}</td>
         <td class="align-top"><b>Project</b></td>
         <td class="align-top">{{ $reference_details['project'] }}</td>
      </tr>
   </table>
</div>
<table class="table table-hover table-bordered" id="item-list" style="font-size: 9pt;">
   <col style="width: 5%;">
   <col style="width: 45%;">
   <col style="width: 10%;">
   <col style="width: 13%;">
   <col style="width: 12%;">
   <col style="width: 15%;">
   <thead class="text-white bg-secondary" style="font-size: 7pt;">
      <th class="text-center"><b>No.</b></th>
      <th class="text-center"><b>Item Description</b></th>
      <th class="text-center"><b>Qty</b></th>
      <th class="text-center"><b>Planned Start Date</b></th>
      <th class="text-center"><b>Target Warehouse</b></th>
      <th class="text-center"><b>Create Production Order</b></th>
   </thead>
   <tbody>
      @forelse($item_list as $idx => $item)
      @php
         $div_color = ( $item['match'] == "false") ? "change_code_class":""; 
         $div_hide = ( $item['match'] == "false") ? "" : "none";
         $div_parent = ( $item['match'] == "false") ?  $item['new_code'] : "";
         $div_orig = ( $item['match'] == "false") ?  $item['origl_code'] : "";
      @endphp
      <tr class="{{$div_color}}">
         <td class="text-center"><b>{{ $item['idx'] }}</b></td>
         <td class="text-justify">
            <span style="display: none;">{{ $item['idx'] }}</span>
            <span style="display: none;">{{ $item['reference'] }}</span>
            <span style="display:{{$div_hide}};" style="text-align:center;">
               <i class="now-ui-icons travel_info text-center" style="padding-right:5px; font-size:20px;"></i>
               <span style="font-size:13pt;" class="text-center d-block">
                  <b>Notification: Change Code</b>
               </span>
               <span style="font-size:11pt;">Parent Item code was change from </span>
               <span class="ml-1 font-weight-bold">{{$div_orig}}</span> to <span class="ml-1 font-weight-bold">{{$div_parent}}</span>
               <br><br>
            </span>
            <span class="d-block item-code font-weight-bold">{{ $item['item_code'] }}</span>
            <span class="d-block item-description">{!! $item['description'] !!}</span>
            <br><br><b>{{ $item['item_classification'] }}</b>
         </td>
         <td class="text-center" style="font-size: 11pt;">
            <span class="qty" style="display: none;">{{ $item['qty'] }}</span>
            <b><span>{{ number_format($item['qty']) }}</span><br>{{ $item['uom'] }}</b>
         </td>
         <td class="text-center">
            <div class="form-group" style="margin: 0;">
               <input type="text" class="form-control form-control-lg date-picker planned-date" style="text-align: center;" placeholder="Planned Start Date" value="{{ $item['planned_start_date'] }}" {{ ($item['production_order']) ? 'disabled' : '' }}>
            </div>
         </td>
         <td class="text-center">
            <div class="form-group" style="margin: 0;">
               <select class="form-control form-control-lg target" {{ ($item['production_order']) ? 'disabled' : '' }}>
                  @forelse ($item_warehouses as $target_warehouse)
                  <option value="{{ $target_warehouse }}" {{ ($item['fg_warehouse']) ? ($item['fg_warehouse'] == $target_warehouse ? 'selected' : '') : ('Finished Goods - FI' == $target_warehouse ? 'selected' : '') }}>{{ $target_warehouse }}</option>
                  @empty
                  <option value="">No warehouse found.</option>
                  @endforelse
               </select>
            </div>
         </td>
         <td class="td-actions text-center">
            <span class="item-reference-id d-none">{{ $item['id'] }}</span>
            @if ($item['production_order'])
            <button type="button" class="btn btn-success production-order-no" disabled><i class="now-ui-icons ui-1_check"></i> {{ $item['production_order'] }}</button>
            <button type="button" rel="tooltip" class="btn btn-danger" disabled>
               <i class="now-ui-icons ui-1_simple-remove"></i>
            </button>
            @else
            <button type="button" class="btn btn-primary create-production-btn"><i class="now-ui-icons ui-1_simple-add"></i> Production Order</button>
            <button type="button" rel="tooltip" class="btn btn-danger delete-row">
               <i class="now-ui-icons ui-1_simple-remove"></i>
            </button>
            @endif
         </td>
      </tr>
      @empty
      <tr>
         <td colspan="9" class="text-center">No Record(s) Found.</td>
      </tr>
      @endforelse
   </tbody>
</table>
@if($reference_details['notes'])
   <span><b>Notes / Instructions:</b><br>{!! $reference_details['notes'] !!}</span>
@endif
<style>
   .change_code_class {
      background-color: #ffbc50;
      color: #000000;
      animation: blinkingBackground_change_code 2.5s linear infinite;
   }
   @keyframes blinkingBackground_change_code{
      0%    { background-color: #ffffff;}
      25%   { background-color: #ffbc50;}
      50%   { background-color: #ffffff;}
      75%   { background-color: #ffbc50;}
      100%  { background-color: #ffffff;}
   }
</style>