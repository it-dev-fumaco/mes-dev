<table class="table table-striped table-bordered">
    <tr>
        <th class="text-center" style="width: 5%;">Prod. Order</th>
        <th style="width: 25%;">Item</th>
        <th class="text-center" style="width: 5%;">Workstation</th>
        <th class="text-center" style="width: 10%;">Process</th>
        <th class="text-center" style="width: 5%;">Good</th>
        <th class="text-center" style="width: 5%;">Reject</th>
        <th class="text-center" style="width: 10%;">Start Time</th>
        <th class="text-center" style="width: 10%;">End Time</th>
        <th style="width: 5%;" class="text-center">Duration (Seconds)</th>
        <th class="text-center" style="width: 10%">Operator Name</th>
    </tr>
    @forelse ($report as $i)
        <tr>
            <td class="text-center">
                <a href="#" data-jtno="{{ $i->production_order }}" class="prod-details-btn">{{ $i->production_order }}</a>
            </td>
            <td style="font-size: 9pt;">
                {!! '<b>'.$i->item_code.'</b> - '.$i->item_description !!}
            </td>
            <td class="text-center">{{ $i->workstation_name }}</td>
            <td class="text-center">{{ $i->process_name }}</td>
            <td class="text-center">{{ $i->good }}</td>
            <td class="text-center">{{ $i->reject }}</td>
            <td class="text-center">{{ Carbon\Carbon::parse($i->from_time)->format('M. d, Y - h:i:s A') }}</td>
            <td class="text-center">{{ Carbon\Carbon::parse($i->to_time)->format('M. d, Y - h:i:s A') }}</td>
            <td class="text-center">{{ $i->duration_in_seconds }}</td>
            <td class="text-center">{{ $i->operator_name }}</td>
        </tr>
    @empty
        <tr>
            <td colspan=10 class="text-center">
                No result(s) found.
            </td>
        </tr>
    @endforelse
</table>
<div class="mt-3" id="inaccurate-operator-feedback-report-pagination">
    {{ $report->links() }}
</div>
