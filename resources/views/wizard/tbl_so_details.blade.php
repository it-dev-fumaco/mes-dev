<div id="so-details-tbl">
   <div class="text-center m-2">
      <span class="text-center text-uppercase" style="font-size: 12pt;"><b>Order Details - {{ $so->name }}</b></span>
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
         <td class="align-top">{{ $so->customer }}</td>
         <td class="align-top"><b>Transaction Date</b></td>
         <td class="align-top">{{ date('M-d-Y', strtotime($so->transaction_date)) }}</td>
         <td class="align-top"><b>Status</b></td>
         <td class="align-top"><b>{{ $so->status }}</b></td>     
      </tr>
      <tr>
         <td class="align-top"><b>Sales Type</b></td>
         <td class="align-top">{{ $so->sales_type }}</td>
         <td class="align-top"><b>Delivery Date</b></td>
         <td class="align-top">{{ date('M-d-Y', strtotime($so->delivery_date)) }}</td>
         <td class="align-top"><b>Project</b></td>
         <td class="align-top">{{ $so->project }}</td>
      </tr>
   </table>
</div>
<table class="table table-hover table-bordered" id="item-list" style="font-size: 9pt;">
   <col style="width: 5%;">
   <col style="width: 37%;">
   <col style="width: 12%;">
   <col style="width: 12%;">
   <col style="width: 12%;">
   <col style="width: 17%;">
   <col style="width: 5%;">
   <thead class="text-white bg-secondary" style="font-size: 8pt;">
      <th class="text-center"><b>No.</b></th>
      <th class="text-center"><b>Item Description</b></th>
      <th class="text-center"><b>Qty</b></th>
      <th class="text-center"><b>Delivered Qty</b></th>
      <th class="text-center"><b>Stock Availability</b></th>
      <th class="text-center"><b>BOM No.</b></th>
      <th class="text-center"><b>Action</b></th>
   </thead>
   <tbody>
      @forelse($item_list as $idx => $item)
         @foreach($item['change_code'] as $rows)
            @php
               $div_color = ($rows['match'] == "false") ? "change_code_class":""; 
               $div_hide = ($rows['match'] == "false") ? "" : "none";
               $div_parent = ($rows['match'] == "false") ? $rows['new_item'] : "";
               $div_orig = ($rows['match'] == "false") ? $rows['original_item'] : "";

            @endphp
         @endforeach
      <tr class="{{$div_color}}">
         <td class="text-center"><b>{{ $item['idx'] }}</b></td>
         <td class="text-justify">
            <div class="container text-center"  style="display:{{$div_hide}}; ">
               <div class="alert-icon">
                <i class="now-ui-icons travel_info" style="padding-right:5px; font-size:20px;"></i><span style="font-size:13pt;"> <b>Notification: Change Code </b></span> 
                      <br>
                      <span style="font-size:11pt;">Parent Item code was change from <span class="ml-1 font-weight-bold">{{$div_orig}} </span> to <span class="ml-1 font-weight-bold">{{$div_parent}}  </span></span>
               </div>
               <br>
            </div>
            <span style="display: none;">{{ $item['idx'] }}</span>
            <span style="display: none;">{{ $item['sales_order'] }}</span>
            <b>{{ $item['item_code'] }}</b><br>{!! $item['description'] !!}
            <br><br><b>{{ $item['item_classification'] }}</b>
         </td>
         <td class="text-center" style="font-size: 14pt;">
            <span class="qty" style="display: none;">{{ $item['qty'] }}</span>
            <b><span>{{ number_format($item['qty']) }}</span><br>{{ $item['uom'] }}</b>
         </td>
         <td class="text-center text-white" style="font-size: 14pt; background-color: {{ ($item['delivered_qty'] > 0) ? '#27AE60' : '#A6ACAF' }};"><b>{{ number_format($item['delivered_qty']) }}</b></td>
         <td class="text-center text-white" style="background-color: {{ ($item['stock'] > 0) ? '#27AE60' : '#A6ACAF' }};">
            @if(number_format($item['stock']) > 0)
            <span style="font-size: 14pt;"><b>{{ number_format($item['stock']) > 0 }}</b></span>
            @else
            <span style="font-size: 10pt;"><b>Not Available</b></span>
            @endif
         <td class="text-center">
            @if($item['bom'] != '-- No BOM --' || count($item['bom_list']) > 0)
            <div class="input-group" style="margin: 0;">
               <select class="custom-select" id="{{ $item['id'] }}">
                  @foreach($item['bom_list'] as $bom)
                  <option value="{{ $bom->name }}" {{ ($bom->name == $item['bom']) ? 'selected' : '' }}><b>{{ $bom->name }}</b></option>
                  @endforeach
               </select>
               <div class="input-group-append">
                  <button class="btn btn-info view-bom" type="button" data-id="{{ $item['id'] }}"><i class="now-ui-icons ui-1_zoom-bold"></i></button>
               </div>
            </div>
            @else
            <span>-- No BOM --</span>
            @endif
         </td>
         <td class="td-actions text-center">
            <span class="item-reference-id d-none">{{ $item['id'] }}</span>
            <span class="delivery-date d-none">{{ $item['delivery_date'] }}</span>
            <button type="button" rel="tooltip" class="btn btn-danger delete-row">
               <i class="now-ui-icons ui-1_simple-remove"></i>
            </button>
         </td>
      </tr>
      @empty
      <tr>
         <td colspan="9" class="text-center">No Sales Order Item(s) Found.</td>
      </tr>
      @endforelse
   </tbody>
</table>
@if($so->notes)
   <span><b>Notes / Instructions:</b><br>{!! $so->notes !!}</span>
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