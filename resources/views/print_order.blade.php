<table style="width: 100%; font-family: Arial, Helvetica, sans-serif;">
    <tr>
        <td style="width: 60%;">
            <span style="font-size: 19px;"><b>{{ $ref_type == 'SO' ? 'SALES ORDER' : 'MATERIAL REQUEST' }}</b></span>
        </td>
        <td style="width: 40%;">
            <small>
                <span style="display: block; margin: 2px;">Printed by: {{ Auth::user()->employee_name }}</span>
                <span style="display: block; margin: 2px;">Time: {{ \Carbon\Carbon::now()->format('M. d, Y h:i A') }}</span>
            </small>
        </td>
    </tr>
</table>

<hr>
<table style="width: 100%; font-family: Arial, Helvetica, sans-serif;">
    <tr>
        <td style="width: 18%; vertical-align: top; padding: 3px;">Reference No.:</td>
        <td style="width: 47%; vertical-align: top; padding: 3px;"><b>{{ $details->name }}</b></td>
        <td style="width: 15%; vertical-align: top; padding: 3px;">Date:</td>
        <td style="width: 20%; vertical-align: top; padding: 3px;"><b>{{ \Carbon\Carbon::parse($details->transaction_date)->format('M. d, Y') }}</b></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Customer:</td>
        <td style="vertical-align: top;"><b>{{ $details->customer }}</b></td>
        <td style="vertical-align: top;">Delivery Date:</td>
        <td style="vertical-align: top;"><b>{{ \Carbon\Carbon::parse($details->delivery_date)->format('M. d, Y') }}</b></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Project:</td>
        <td style="vertical-align: top;"><b>{{ $details->project }}</b></td>
        <td style="vertical-align: top;">Status:</td>
        <td style="vertical-align: top;"><b>{{ $details->status }}</b></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Order Type:</td>
        <td style="vertical-align: top;" colspan="3"><b>{{ $details->sales_type }}</b></td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Shipping Address:</td>
        <td style="vertical-align: top;" colspan="3"><b>{!! $details->shipping_address !!}</b></td>
    </tr>
</table>
<br>

<table style="width: 100%; font-family: Arial, Helvetica, sans-serif;">
    <thead>
        <th style="text-align: center; width:6%; border-top: 1px solid; border-bottom: 1px solid;">NO</th>
        <th style="text-align: center; width:55%; border-top: 1px solid; border-bottom: 1px solid;">PRODUCT DESCRIPTION</th>
        <th style="text-align: center; width:10%; border-top: 1px solid; border-bottom: 1px solid;">QTY</th>
        <th style="text-align: center; width:15%; border-top: 1px solid; border-bottom: 1px solid;">SHIP BY</th>
    </thead>
    <tbody>
        @foreach ($items as $r)
        <tr>
            <td style="text-align: center; vertical-align: top;">{{ $r->idx }}</td>
            <td style="text-align: justify; padding-bottom: 8px;">
                <b>{{ $r->item_code }}</b><br>{!! strip_tags($r->description) !!}
                @if (isset($r->item_note) && $ref_type == 'SO')
                <p style="font-style: italic;">Item Note: <b>{{ $r->item_note }}</b></p>
                @endif
                @if (isset($r->notes) && $ref_type == 'SO')
                <p style="font-style: italic;">Notes: {{ $r->notes }}</p>
                @endif
                @if (isset($r->request_for_drawing) && $ref_type == 'SO')
                <p style="font-style: italic;">{{ $r->request_for_drawing }}</p>
                @endif            
            </td>
            <td style="text-align: center;">
                <span style="font-size: 12pt; display: block;"><b>{{ number_format($r->qty) }}</b></span>
                <span style="font-size: 10pt;">{{ $r->stock_uom }}</span>
            </td>
            <td style="text-align: center;">{{ \Carbon\Carbon::parse($r->delivery_date)->format('M. d, Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<br>
@if ($details->notes)
<div style="font-family: Arial;">
    <b>Notes:</b> {!! $details->notes !!}
</div>
@endif

<script>
    window.print();
</script>