<div class="table-responsive">
    <table class="table table-striped">
        <thead class="text-primary" style="font-size: 8pt;">
            <th class="text-center"><b>No.</b></th>
            <th class="text-center"><b>Material</b></th>
            <th class="text-center"><b>Length</b>
            <th class="text-center"><b>Width</b>
            <th class="text-center"><b>Thickness</b>
            <th class="text-center"><b>Quantity</b>
            <th class="text-center"><b>UOM</b>
        </thead>
        <tbody>
            @forelse($q as $i => $row)
            <tr class="text-center">
                <td>{{ $i + 1 }}</td>
                <td>{{ $row->material }}</td>
                <td>{{ $row->length }} mm</td>
                <td>{{ $row->width }} mm</td>
                <td>{{ $row->thickness }} mm</td>
                <td>{{ $row->usable_scrap_qty }}</td>
                <td>Cubic MM</td>
            </tr>
            @empty
            <tr>
            <td colspan="8" class="text-center">No Record(s) Found.</td>
            </tr>
            @endforelse
        </tbody>  
    </table>
</div>
<center>
    <div id="tbl-scrap-pagination" class="col-md-12 text-center" style="text-align: center;">
    {{ $q->links() }}
    </div>
  </center>