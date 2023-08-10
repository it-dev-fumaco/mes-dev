<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        table, tr, th, td{
            border: 1px solid #000;
        }
    </style>
</head>
<body>
    Total: {{ count($unassigned_production) }}
    <table>
        <tr>
            <th>Production Order</th>
            <th>Item</th>
            <th>Description</th>
            <th>Reference No.</th>
            <th>Feedbacked Qty</th>
            <th>Status</th>
            <th>Customer</th>
            <th>Classification</th>
        </tr>
        @foreach ($unassigned_production as $item)
            <tr>
                <td style="text-align: left">{{ $item->production_order }}</td>
                <td style="text-align: left">{{ $item->parent_item_code }}</td>
                <td style="text-align: left">{{ strip_tags($item->description) }}</td>
                <td style="text-align: left">{{ $item->sales_order ? $item->sales_order : $item->material_request }}</td>
                <td style="text-align: left">{{ $item->feedback_qty }}</td>
                <td style="text-align: left">{{ $item->status }}</td>
                <td style="text-align: left">{{ $item->customer }}</td>
                <td style="text-align: left">{{ $item->classification }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>