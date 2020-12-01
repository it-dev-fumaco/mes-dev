<table>
    <tr>
        <td colspan="6" class="text-center">
            <span style="display: block; font-size: 12pt; font-weight:bold;">DAILY PRODUCTION SCHEDULE</span>
            <span style="display: block; font-size: 10pt; margin-top: 3px;"><i>Date of Schedule: {{ date('d-F-Y', strtotime($scheduled_date)) }}</i></span>
        </td>
    </tr>
    <tr>
        <td colspan="6" class="text-center">
            <span style="display: block; font-size: 12pt; margin: 8px 0 5px 0; font-weight: bold;">{{ $machine_details->machine_name }}</span>
        </td>
    </tr>
</table>
<table border="1">
    <tr>
        <td class="text-center" style="width: 5%; font-weight:bold; padding: 3px;">NO.</td>
        <td class="text-center" style="width: 10%; font-weight:bold;">PROD. ORDER</td>
        <td class="text-center" style="width: 35%; font-weight:bold;">ITEM</td>
        <td class="text-center" style="width: 30%; font-weight:bold;">REFERENCE</td>
        <td class="text-center" style="width: 10%; font-weight:bold;">DEL. DATE</td>
        <td class="text-center" style="width: 10%; font-weight:bold;">QTY</td>
    </tr>
    @forelse ($scheduled_production as $i => $row)
    <tr>
        <td class="text-center">{{ $i + 1 }}</td>
        <td class="text-center">{{ $row->production_order }}</td>
        <td style="text-align: justify; padding: 2px;">
            <span style="display: block; font-weight: bold;">{{ $row->item_code }}</span>
            <span style="display: block;">{{ $row->description }}</span>
        </td>
        <td style="padding: 2px;">
            <span style="display: block; font-weight: bold;">{{ $row->sales_order }}{{ $row->material_request }}</span>
            <span style="display: block;">{{ $row->customer }}</span>
            <span style="display: block; margin-top: 5px;">Project: <i>{{ $row->project }}</i></span>
        </td>
        <td class="text-center">{{ $row->delivery_date }}</td>
        <td class="text-center">
            <span style="display: block; font-size: 9pt;">{{ $row->qty_to_manufacture }}</span>
            <span style="display: block;">{{ $row->stock_uom }}</span>
        </td>
    </tr>
    @empty
    <tr>
        <td class="text-center" colspan="6" style="font-size: 11pt; font-weight: bold;">No Production Order(s)</td>
    </tr>
        
    @endforelse
</table>
<table>
    <tr>
        <td colspan="6" style="text-align: right; padding-top: 5px;"><span>Printed by: <i>{{ Auth::user()->employee_name }} - {{  now()->toDateTimeString('h:m:s') }}</i></span></td>
    </tr>
</table>

<style>
    table{
        font-family: sans-serif;
        font-size: 7pt;
        width: 100%;
        border-collapse: collapse;
    }

    .text-center{
        text-align: center;
    }
</style>

<script>
    window.print();
</script>