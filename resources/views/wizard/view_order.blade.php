{{-- <div class="row">
  <div class="col-md-12">
    <table style="width: 100%; font-size: 9pt;">
      <col style="width: 8%;">
      <col style="width: 22%;">
      <col style="width: 10%;">
      <col style="width: 20%;">
      <col style="width: 10%;">
      <col style="width: 30%;">
      <tr>
        <td class="align-top"><b>Customer</b></td>
        <td class="align-top">{{ $so->customer }}</td>
        <td class="align-top"><b>Transaction Date</b></td>
        <td class="align-top">{{ $so->transaction_date }}</td>
        <td class="align-top"><b>Project</b></td>
        <td class="align-top">{{ $so->project }}</td>
      </tr>
      <tr>
        <td class="align-top"><b>Sales Type</b></td>
        <td class="align-top"><u>{{ $so->sales_type }}</u></td>
        <td class="align-top"><b>Delivery Date</b></td>
        <td class="align-top">{{ $so->delivery_date }}</td>
        <td class="align-top"><b>Shipping Address</b></td>
        <td class="align-top">{!! $so->shipping_address !!}</td>
      </tr>
    </table>
  </div>
  <div class="col-md-12">
    <div class="table-responsive">
      <table class="table table-hover">
        <col style="width: 6%;">
        <col style="width: 42%;">
        <col style="width: 9%;">
        <col style="width: 14%;">
        <col style="width: 14%;">
        <col style="width: 14%;">
        <thead class="text-primary" style="font-size: 9pt;">
          <th class="text-center"><b>No.</b></th>
          <th class="text-center"><b>Item Description</b></th>
          <th class="text-center"><b>Qty</b></th>
          <th class="text-center"><b>Delivery Date</b></th>
          <th class="text-center"><b>Delivered Qty</b></th>
          <th class="text-center"><b>Stock Availability</b></th>
        </thead>
        <tbody style="font-size: 9pt;">
          @foreach($so_items as $item)
          <tr>
            <td class="text-center align-top"><b>{{ $item->idx }}</b></td>
            <td class="text-justify align-top"><b>{{ $item->item_code }}</b><br>{!! $item->description !!}<br>@if($item->item_note)Item Note: <b>{{ $item->item_note }}</b>@endif</td>
            <td class="text-center align-top">{{ number_format($item->qty) }} {{ $item->uom }}</td>
            <td class="text-center align-top">{{ $item->delivery_date }}</td>
            <td class="text-center align-top text-white" style="background-color: {{ ($item->delivered_qty > 0) ? '#27AE60' : '#95A5A6' }}; font-size: 14pt;">{{ number_format($item->delivered_qty) }}</td>
            <td class="text-center align-top text-white" style="background-color: {{ ($item->actual_qty > 0) ? '#27AE60' : '#95A5A6' }}; font-size: 14pt;">{{ number_format($item->actual_qty) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if($so->notes)
    <div class="col-md-8 offset-md-2">
      <span><b>Notes:</b><br>{!! $so->notes !!}</span>
    </div>
    @endif
  </div>
</div> --}}