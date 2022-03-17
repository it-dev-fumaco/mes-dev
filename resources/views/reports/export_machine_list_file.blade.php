<table id="export-table" class="table table-bordered">
    <tr>
        <th>machine_code</th>
        <th>machine_name</th>
        <th>model</th>
        <th>status</th>
        <th>operation</th>
    </tr>
    @forelse($machine_list as $export)
        <tr>
            <td>{{ $export->machine_code }}</td>
            <td>{{ $export->machine_name }}</td>
            <td>{{ $export->model }}</td>
            <td>{{ $export->status }}</td>
            <td>{{ $export->operation_name }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center">No result(s) found.</td>
        </tr>
    @endforelse
</table>
<script>
    $(document).ready(function(){
        var export_count = "{{ count($machine_list) }}";
        if(parseInt(export_count) == 999){
            alert('Data export is limited to 1000 records per export.');
        }
    });
</script>