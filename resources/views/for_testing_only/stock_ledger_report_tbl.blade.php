<table class="table table-bordered table-striped" style="font-size: 9pt">
    <thead>
        <tr>
            <th>Posting Date</th>
            <th>Created By</th>
            <th>Modified By</th>
            <th>Item Code</th>
            <th>Warehouse</th>
            <th>Reference Type</th>
            <th>Reference No</th>
            <th>Transaction Qty</th>
            <th>Balance Qty</th>
            <th>Expected qty after Transaction</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($report as $item_code => $stock_ledger_data)
            <tr>
                <td class="font-weight-bold bg-success text-white" colspan="11">{{ $item_code }}</td>
            </tr>
            @foreach ($stock_ledger_data as $i => $data)
                @php
                    $transaction_qty = number_format($data->actual_qty);
                    $expected_qty = $i > 0 ? (int) $data->expected_qty : '-'
                @endphp
                <tr>
                    <td>{{ Carbon\Carbon::parse($data->posting_datetime)->format('M. d, Y - h:i A') }}</td>
                    <td>{{ $data->owner }}</td>
                    <td>{{ $data->modified_by }}</td>
                    <td>{{ $data->item_code }}</td>
                    <td>{{ $data->warehouse }}</td>
                    <td>{{ $data->voucher_type }}</td>
                    <td>{{ $data->voucher_no }}</td>
                    <td>{{ $transaction_qty }}</td>
                    <td class="{{ $expected_qty != (int) $data->qty_after_transaction && $i > 0 ? 'bg-danger text-white' : null }}">{{ number_format($data->qty_after_transaction) }}</td>
                    <td class="{{ $expected_qty != (int) $data->qty_after_transaction && $i > 0 ? 'bg-danger text-white' : null }}">{{ $expected_qty }}</td>
                </tr>
                @if ($loop->last)
                    <tr>
                        <td class="font-weight-bold text-right" colspan="11">
                            <button class="btn-primary btn-sm create-stock-recon"
                                data-quantity="{{ $expected_qty }}"
                                data-warehouse="{{ $data->warehouse }}"
                                data-item_code="{{ $item_code }}"
                                data-company="{{ $data->company }}"
                            >Create Stock Recon</button>
                        </td>
                    </tr>
                @endif
            @endforeach
        @endforeach
    </tbody>
</table>