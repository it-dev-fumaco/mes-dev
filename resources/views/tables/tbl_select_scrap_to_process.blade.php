<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered text-center" border="1">
            <col style="width: 25%;">
            <col style="width: 25%;">
            <col style="width: 25%;">
            <col style="width: 25%;">
            <thead class="text-primary font-weight-bold" style="font-size: 8pt;">
                <th class="font-weight-bold">Material</th>
                <th class="font-weight-bold">Thickness</th>
                <th class="font-weight-bold">Qty</th>
                <th class="font-weight-bold">Action</th>
            </thead>
            <tbody>
                @foreach ($q as $row)
                <tr>
                    <td>{{ strtoupper($row->material) }}</td>
                    <td>{{ $row->thickness }} mm</td>
                    <td><span class="scrap-qty">{{ number_format($row->scrap_qty, 8) }} Kg(s)</span></td>
                    <td><button class="btn btn-default selected-scrap-btn" data-id="{{ $row->scrap_id }}">Select</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>