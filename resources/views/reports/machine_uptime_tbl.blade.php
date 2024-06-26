@php
    $operation = collect($operations)->groupBy('operation_id');
@endphp
<table class="table table-bordered table-striped" style="font-size: 9pt;">
    <colgroup>
        <col style="width: 5%">
        <col style="width: 10%">
        <col style="width: 20%">
        <col style="width: 15%">
        <col style="width: 20%">
        <col style="width: 20%">
        <col style="width: 10%">
    </colgroup>
    <tr>
        <th class="text-center p-1"></th>
        <th class="text-center p-1">Code</th>
        <th class="text-center p-1">Name</th>
        <th class="text-center p-1">Operation</th>
        <th class="text-center p-1">Type</th>
        <th class="text-center p-1">Model</th>
        <th class="text-center p-1">Total Uptime</th>
    </tr>
    @forelse ($report as $row)
        <tr>
            <td>
                <img class="img-thumbnail" src="{{ 'http://mes.fumaco.local/'.$row->image }}" alt="" class="w-100 mx-auto">
            </td>
            <td>{{ $row->machine_code }}</td>
            <td>{{ $row->machine_name }}</td>
            <td>{{ isset($operation[$row->operation_id]) ? $operation[$row->operation_id][0]->operation_name : null }}</td>
            <td>{{ $row->type }}</td>
            <td>{{ $row->model }}</td>
            <td>{{ number_format($row->total_duration, 2) }} hour(s)</td>
        </tr>
    @empty
        <tr>
            <td colspan=20 class="text-center">No result(s) found</th>
        </tr>
    @endforelse
</table>