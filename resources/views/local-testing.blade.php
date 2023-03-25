<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        table, tr, td, th{
            border: 1px solid;
        }
    </style>
</head>
<body>
    <span>Total: {{ count($report) }}</span><br>
    <table style="width: 100%;">
        <tr>
            <th>Production Order</th>
            <th>Customer</th>
            <th>Project</th>
            <th>Reference</th>
            <th>Qty to Manufacture</th>
            <th>Date Created</th>
            <th>Created By</th>
        </tr>
        @foreach ($report as $res)
            <tr>
                <td>{{ $res->production_order }}</td>
                <td>{{ $res->customer }}</td>
                <td>{{ $res->project }}</td>
                <td>{{ $res->sales_order ? $res->sales_order : $res->material_request }}</td>
                <td>{{ $res->qty_to_manufacture }}</td>
                <td>{{ Carbon\Carbon::parse($res->created_at)->format('M. d, Y') }}</td>
                <td>{{ $res->created_by }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>