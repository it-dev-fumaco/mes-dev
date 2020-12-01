<input type="hidden" name="per_item_cubic_mm" value="{{ $item_cm }}">
<input type="hidden" name="production_order" value="{{ $production_order_details->production_order }}">
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered text-center" border="1">
            <col style="width: 27%;">
            <col style="width: 15%;">
            <col style="width: 15%;">
            <col style="width: 15%;">
            <col style="width: 13%;">
            <col style="width: 15%;">
            <thead class="text-primary font-weight-bold" style="font-size: 8pt;">
                <th class="font-weight-bold">Material</th>
                <th class="font-weight-bold">Length</th>
                <th class="font-weight-bold">Width</th>
                <th class="font-weight-bold">Thickness</th>
                <th class="font-weight-bold">Qty</th>
                <th></th>
            </thead>
            <tbody>
                @foreach ($available_scrap as $row)
                <tr>
                    <td>{{ $row['material'] }}</td>
                    <td>{{ $row['length'] }} mm</td>
                    <td>{{ $row['width'] }} mm</td>
                    <td>{{ $row['thickness'] }} mm</td>
                    <td>
                        <span class="d-block">{{ $row['qty'] }}</span>
                        <span class="badge badge-info scrap-qty">{{ $row['usable_scrap_qty'] }} Cubic MM</span>
                    </td>
                    <td>
                        <div class="form-group m-0">
                            <input type="hidden" name="usable_scrap_id[]" value="{{ $row['usable_scrap_id'] }}">
                            <input type="hidden" name="usable_scrap_qty[]" value="{{ $row['usable_scrap_qty'] }}">
                            <input type="hidden" name="available_qty[]" value="{{ $row['qty'] }}">
                            <input type="hidden" name="per_qty_in_cubic_mm[]" value="{{ $row['per_qty_in_cubic_mm'] }}">
                            <input type="number" name="qty_scrap[]" class="form-control form-control-lg scrap-qty-to-use" value="0">
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>