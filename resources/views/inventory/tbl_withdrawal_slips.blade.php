<div class="table-responsive">
    <table class="table table-striped">
        <thead class="text-primary" style="font-size: 8pt;">
            <th class="text-center"><b>Series</b></th>
            <th class="text-center"><b>Production Order</b></th>
            <th class="text-center"><b>Reference No.</b></th>
            <th class="text-center"><b>BOM</b></th>
            <th class="text-center"><b>Issued By</b></th>
            <th class="text-center"><b>Warehouse</b></th>
            <th class="text-center"><b>Status</b></th>
            {{-- <th class="text-center"><b>Actions</b></th> --}}
        </thead>
        <tbody>
            @foreach ($list as $row)
            <tr>
                <td class="text-center">{{ $row['name'] }}</td>
                <td class="text-center">{{ $row['production_order'] }}</td>
                <td class="text-center"><b>{{ $row['sales_order_no'] }}</b><br>{{ $row['so_customer_name'] }} </td>
                <td class="text-center">{{ $row['bom_no'] }}</td>
                <td class="text-center">{{ $row['issued_by'] }}</td>
                <td class="text-center">{{ $row['warehouse'] }}</td>
                <td class="text-center">{{ $row['item_status'] }}</td>
                {{-- <td class="text-center">
                    <img src="{{ asset('img/search.png') }}" width="37">
                    <img src="{{ asset('img/print.png') }}" width="30">
                </td> --}}
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<center>
    <div id="tbl-withdrawal-pagination" class="col-md-12 text-center" style="text-align: center;">
    {{ $list->links() }}
    </div>
  </center>