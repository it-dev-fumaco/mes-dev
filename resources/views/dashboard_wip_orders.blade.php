<div class="table-responsive m-0 p-0" style="max-height: 540px;">
    <table class="table table-striped table-bordered text-center m-0">
        <col style="width: 10%;">
        <col style="width: 15%;">
        <col style="width: 10%;">
        <col style="width: 45%;">
        <col style="width: 10%;">
        <col style="width: 10%;">
        <thead class="text-white bg-secondary" style="font-size: 6.5pt;">
          <th class="text-center p-1">Order No.</th>
          <th class="text-center p-1">Customer</th>
          <th class="text-center p-1">Order Type</th>
          <th class="text-center p-1">Item Details</th>
          <th class="text-center p-1">Qty</th>
          <th class="text-center p-1">Delivery Date</th>
        </thead>
        <tbody style="font-size: 7pt;">
            @forelse($list as $r)
            <tr>
                <td class="text-center p-1 font-weight-bold">{{ $r->sales_order .''. $r->material_request }}</td>
                <td class="text-center p-1">{{ $r->customer }}</td>
                <td class="text-center p-1">{{ $r->classification }}</td>
                <td class="text-justify p-1">
                    <span class="font-weight-bold">{{ $r->item_code }}</span>
                    <span>{!! strip_tags($r->description) !!}</span>
                </td>
                <td class="text-center p-1 font-weight-bold">{{ number_format($r->qty_to_manufacture) }}</td>
                <td class="text-center p-1">{{ \Carbon\Carbon::parse($r->delivery_date)->format('M. d, Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center font-weight-bold text-muted text-uppercase">No In Process Order(s)</td>
            </tr>
            @endforelse
        </tbody>
    </table>  
</div>