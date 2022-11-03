@extends('layouts.user_app', [
    'namePage' => 'Data Export',
    'activePage' => 'machine_list_export',
    'pageHeader' => 'System Audit Report',
    'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header"></div>
<div class="row p-0" style="margin-top: -190px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
    <div class="col-12 mx-auto bg-white p-2">
        <h5 class="text-center font-weight-bold">Production Orders with Stock Withdrawals but has incorrect Material Transferred for Manufacturing</h5>
        <div class="pull-right font-weight-bold m-2">
            Total Record(s): <span class="badge badge-primary" style="font-size: 12pt;">{{ $query->total() }}</span>
        </div>
        <table class="table table-bordered">
            <thead style="font-size: 7pt;">
                <th class="text-center font-weight-bold" style="width: 10%;">Date Created</th>
                <th class="text-center font-weight-bold" style="width: 10%;">Production Order</th>
                <th class="text-center font-weight-bold" style="width: 10%;">Production Item</th>
                <th class="text-center font-weight-bold" style="width: 10%;">Qty</th>
                <th class="text-center font-weight-bold" style="width: 10%;">Material Transferred for Manufacturing</th>
                <th class="text-center font-weight-bold" style="width: 10%;">Produced Qty</th>
                <th class="text-center font-weight-bold" style="width: 10%;">Status</th>
                <th class="text-center font-weight-bold" style="width: 10%;">Stock Entry</th>
                <th class="text-center font-weight-bold" style="width: 20%;">Purpose</th>
            </thead>
            <tbody>
                @forelse ($query_grouped as $sales_order => $items)
                @foreach ($items as $row)
                <tr>
                    @if ($loop->first)
                    <td rowspan="{{ count($items) }}" class="text-center">{{ \Carbon\Carbon::parse($items[0]->creation)->format('M-d-Y h:i A') }}</td>
                    <td rowspan="{{ count($items) }}" class="text-center font-weight-bold">{{ $items[0]->production_order }}</td>
                    <td rowspan="{{ count($items) }}" class="text-center">{{ $items[0]->production_item }}</td>
                    <td rowspan="{{ count($items) }}" class="text-center">{{ $items[0]->qty }}</td>
                    <td rowspan="{{ count($items) }}" class="text-center">{{ $items[0]->material_transferred_for_manufacturing }}</td>
                    <td rowspan="{{ count($items) }}" class="text-center">{{ $items[0]->produced_qty }}</td>
                    <td rowspan="{{ count($items) }}" class="text-center">{{ $items[0]->status }}</td>
                    @endif
                    <td class="text-center font-weight-bold">{{ $row->stock_entry }}</td>
                    <td class="text-center">{{ $row->purpose }}</td>
                </tr>
                @endforeach
                @empty
                <tr>
                    <td colspan="9" class="text-center font-weight-bold">No record(s) found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="float-right mt-4">
            {!! $query->appends(request()->query())->links('pagination::bootstrap-4') !!}
        </div>
    </div>
</div>
@endsection
  
@section('script')  
<script>
    $(document).ready(function(){
        setInterval(updateClock, 1000);
        function updateClock(){
            var currentTime = new Date();
            var currentHours = currentTime.getHours();
            var currentMinutes = currentTime.getMinutes();
            var currentSeconds = currentTime.getSeconds();
            // Pad the minutes and seconds with leading zeros, if required
            currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
            currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;
            // Choose either "AM" or "PM" as appropriate
            var timeOfDay = (currentHours < 12) ? "AM" : "PM";
            // Convert the hours component to 12-hour format if needed
            currentHours = (currentHours > 12) ? currentHours - 12 : currentHours;
            // Convert an hours component of "0" to "12"
            currentHours = (currentHours === 0) ? 12 : currentHours;
            currentHours = (currentHours < 10 ? "0" : "") + currentHours;
            // Compose the string for display
            var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;
    
            $("#current-time").html(currentTimeString);
        }
    });
</script>
@endsection