{{-- <table border="1">
    <tr>
        <td>JT Id</td>
        <td>Production Order</td>
        <td>Workstation</td>
        <td>Process id</td>
        <td>BOM</td>
        <td>MES Operation ID</td>
        <td>ERP Operation ID</td>
    </tr>
    @foreach ($data as $i)
    <tr>
        <td>{{ $i['job_ticket_id'] }}</td>
        <td>{{ $i['production_order'] }}</td>
        <td>{{ $i['workstation'] }}</td>
        <td>{{ $i['process_id'] }}</td>
        <td>{{ $i['bom_no'] }}</td>
        <td>{{ $i['mes_bom_operation_id'] }}</td>
        <td>{{ $i['bom_operation_id'] }}</td>
    </tr>
    @endforeach
</table> --}}


{{-- <table border="1">
    <tr>
        <td>Production Order</td>
        <td>Item Code</td>
        <td>ERP Description</td>
        <td>MES Description</td>
        <td>Equal</td>
    </tr>
    @foreach ($data as $i)
    <tr>
        <td>{{ $i->production_order }}</td>
        <td>{{ $i->qty_to_manufacture }}</td>
        <td>{{ $i->qty_accepted }}</td>
        <td>{{ $i->workstation }}</td>
        <td>{{ $i->process_id }}</td>
        <td>{{ $i->status }}</td>
        <td>{{ $i->job_ticket_id }}</td>
       
    </tr>
    @endforeach
</table>
 --}}

{{-- <table border="1">

    @foreach ($data as $i)
    <tr>
        <td>{{ $i['job_ticket_id'] }}</td>
        <td>{{ $i['production_order'] }}</td>
        <td>{{ $i['bom_no'] }}</td>
        <td>{{ $i['workstation'] }}</td>
        <td>{{ $i['erp_process_id'] }}</td>
        <td>{{ $i['mes_process_id'] }}</td>
        <td>{{ $i['erp_bom_operation_id'] }}</td>
        <td>{{ $i['mes_bom_operation_id'] }}</td>
       
    </tr>
    @endforeach
</table> --}}