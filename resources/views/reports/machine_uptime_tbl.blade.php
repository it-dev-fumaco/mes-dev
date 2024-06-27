@php
    $operation = collect($operations)->groupBy('operation_id');

    if ($export){
        header("Content-Disposition: attachment; filename=Machine Uptime Report.xls");
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    }
@endphp
@if ($export)
    <style>
        table, tr, td{
            border: 1px solid #000;
        }
    </style>
@endif
<table class="table table-bordered table-striped" style="font-size: 9pt;">
    <colgroup>
        @if (!$export)
            <col style="width: 5%">
        @endif
        <col style="width: 10%">
        <col style="width: 20%">
        <col style="width: 15%">
        <col style="width: 20%">
        <col style="width: 15%">
        <col style="width: 15%">
    </colgroup>
    <tr>
        @if (!$export)
            <th class="text-center p-1"></th>
        @endif
        <th class="text-center p-1">Code</th>
        <th class="text-center p-1">Name</th>
        <th class="text-center p-1">Operation</th>
        <th class="text-center p-1">Type</th>
        <th class="text-center p-1">Model</th>
        <th class="text-center p-1">Total Uptime</th>
    </tr>
    @forelse ($report as $row)
        <tr>
            @if (!$export)
                <td>
                    <img class="img-thumbnail" src="{{ asset($row->image) }}" alt="" class="w-100 mx-auto">
                </td>
            @endif
            <td>{{ $row->machine_code }}</td>
            <td>{{ $row->machine_name }}</td>
            <td>{{ isset($operation[$row->operation_id]) ? $operation[$row->operation_id][0]->operation_name : null }}</td>
            <td>{{ $row->type }}</td>
            <td>{{ $row->model }}</td>
            <td>{{ $row->total_uptime }}</td>
        </tr>
    @empty
        <tr>
            <td colspan=20 class="text-center">No result(s) found</th>
        </tr>
    @endforelse
</table>