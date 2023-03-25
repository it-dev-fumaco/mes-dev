<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        table{
            width: 100%;
        }

        table, tr, th, td{
            border: 1px solid #000;
        }
    </style>
</head>
<body>
    <span>Total: <b>{{ count($report) }}</b></span>
    <table>
        <tr>
            <td>Machine Code</td>
            <td>Machine Name</td>
            <td>Machine Status</td>
            <td>Machine Type</td>
            <td>Machine Model</td>
            <td>Operation</td>
            <td>Machine Breakdown ID</td>
            <td>Complain</td>
            <td>Breakdown Status</td>
            <td>Category</td>
            <td>Reported By</td>
            <td>Date Reported</td>
            <td>Maintenance Staff</td>
            <td>Date Resolved</td>
        </tr>
        @foreach ($report as $i)
            <tr>
                <td>{{ $i->machine_code }}</td>
                <td>{{ $i->machine_name }}</td>
                <td>{{ $i->status }}</td>
                <td>{{ $i->type }}</td>
                <td>{{ $i->model }}</td>
                <td>{{ $i->operation_name }}</td>
                <td>{{ $i->machine_breakdown_id }}</td>
                <td>{{ $i->complaints }}</td>
                <td>{{ $i->breakdown_status }}</td>
                <td>{{ $i->category }}</td>
                <td>{{ $i->reported_by }}</td>
                <td>{{ $i->date_reported ? Carbon\Carbon::parse($i->date_reported)->format('M. d, Y h:i A') : '-' }}</td>
                <td>{{ $i->assigned_maintenance_staff ? $i->assigned_maintenance_staff : $i->last_modified_by }}</td>
                <td>{{ $i->date_resolved ? Carbon\Carbon::parse($i->date_resolved)->format('M. d, Y h:i A') : '-' }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>