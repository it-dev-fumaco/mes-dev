@extends('layouts.user_app', [
  'namePage' => 'Weekly Rejection Report',
  'activePage' => 'weekly_rejection_report',
  'pageHeader' => 'Rejection Logs',
  'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header"></div>
    <div class="row p-2" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
        <div class="col-12 m-0 bg-white border">
            @php
                $start = \Carbon\Carbon::now()->subDays(7);
                $end = \Carbon\Carbon::now();
                if(request()->has('date')){
                    $start = \Carbon\Carbon::parse(explode(' - ', request('date'))[0]);
                    $end = isset(explode(' - ', request('date'))[1]) ? \Carbon\Carbon::parse(explode(' - ', request('date'))[1]) : null;
                }
            @endphp
            <form action="/weekly_rejection_report">
                <div class="row p-0 m-0">
                    <div class="p-1 mt-1 mb-1 col-3">
                        <small class="d-block">Period:</small>
                        <span class="d-block font-weight-bold text-center">{{ $start->startOfDay()->format('M. d, Y').' - '.$end->endOfDay()->format('M. d, Y') }} ({{ $end->diffInDays($start) }} day/s)</span>
                    </div>
                    <div class="p-1 mt-1 mb-1 col-2" style="border-left: 10px solid #27AE60;">
                        <small class="d-block" style="font-size: 8pt;">Total Good</small>
                        <h5 class="d-block font-weight-bold m-0">{{ number_format($total_good) }}</h5>
                    </div>
                    <div class="p-1 mt-1 mb-1 col-2" style="border-left: 10px solid #F40305;">
                        <small class="d-block" style="font-size: 8pt;">Total Reject</small>
                        <h5 class="d-block font-weight-bold m-0">{{ number_format($total_reject) }}</h5>
                    </div>

                    <div class="p-1 mt-1 mb-1 col-5">
                        <div class="d-flex flex-row align-items-center">
                            <div class="col-5 pt-1 pl-2 pr-2 pb-1">
                                <select name="operation_id" class="form-control rounded">
                                    <option value="">Select Operation</option>
                                    <option value="1" {{ request('operation_id') == 1 ? 'selected' : '' }}>Fabrication</option>
                                    <option value="2" {{ request('operation_id') == 2 ? 'selected' : '' }}>Painting</option>
                                    <option value="3" {{ request('operation_id') == 3 ? 'selected' : '' }}>wiring and Assembly</option>
                                </select>
                            </div>
                            <div class="col-5 pt-1 pl-2 pr-2 pb-1">
                                <input type="text" class='form-control rounded' id="daterange" name='date' />
                            </div>
                            <div class="col-2 p-1">
                                <button class="btn btn-primary btn-xs p-2 m-0 w-100" type="submit">Search</button>
                            </div>
                        </div>
         
                    </div>
                </div>
            </form>
            <table class="table table-bordered table-striped table-hover">
                <thead class="text-white bg-secondary reject-font-size">
                    <tr>
                        <th class="text-center p-1" style="font-size: 10pt;">Production Order</th>
                        <th class="text-center p-1" style="font-size: 10pt;">Reference Order</th>
                        <th class="text-center p-1" style="font-size: 10pt;">Customer</th>
                        <th class="text-center p-1" style="font-size: 10pt;">Workstation</th>
                        <th class="text-center p-1" style="font-size: 10pt;">Start</th>
                        <th class="text-center p-1" style="font-size: 10pt;">End</th>
                        <th class="text-center p-1" style="font-size: 10pt;">Good</th>
                        <th class="text-center p-1" style="font-size: 10pt;">Reject</th>
                        <th class="text-center p-1" style="font-size: 10pt;">Reason</th>
                        <th class="text-center p-1" style="font-size: 10pt;">Status</th>
                        <th class="text-center p-1" style="font-size: 10pt;">Rejection Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reject_arr as $reject)
                    <tr>
                        <td class="p-2 reject-font-size text-center">{{ $reject['production_order'] }}</td>
                        <td class="p-2 reject-font-size text-center">{{ $reject['sales_order'] ? $reject['sales_order'] : $reject['material_request'] }}</td>
                        <td class="p-2 reject-font-size text-center">{{ $reject['customer'] }}</td>
                        <td class="p-2 reject-font-size text-center">{{ $reject['workstation'] }}</td>
                        <td class="p-2 reject-font-size text-center">{{ $reject['from_time'] }}</td>
                        <td class="p-2 reject-font-size text-center">{{ $reject['to_time'] }}</td>
                        <td class="p-2 reject-font-size text-center">{{ number_format($reject['good']) }}</td>
                        <td class="p-2 reject-font-size text-center">{{ number_format($reject['reject']) }}</td>
                        <td class="p-2 reject-font-size text-center">{!! $reject['reject_reason'] !!}</td>
                        <td class="p-2 reject-font-size text-center">{{ $reject['status'] }}</td>
                        <td class="p-2 reject-font-size text-center">
                            @php
                                $rejection_rate = (($reject['reject']) > 0) && ($reject['good']) > 0 ? (($reject['reject']) / ($reject['good'])) * 100 : 0;
                            @endphp
                            {{ number_format($rejection_rate, 2) }}%
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center text-uppercase text-muted">No result(s) found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="float-right mt-4">
                {!! $rejection_logs->appends(request()->query())->links('pagination::bootstrap-4') !!}
            </div>
        </div>
</div>
<style>
    .reject-font-size{
        font-size: 9pt !important;
    }
</style>
@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/daterange/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/daterange/daterangepicker.css') }}" />
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />

<script src="{{ asset('js/excel-export/src/jquery.table2excel.js') }}"></script>
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

    var start_date = "{{ request('date') ? date('m/d/Y', strtotime(explode(' - ', request('date'))[0])) : null }}";
    var end_date = "{{ isset(explode(' - ', request('date'))[1]) ? date('m/d/Y', strtotime(explode(' - ', request('date'))[1])) : null }}";

    $('#daterange').daterangepicker({
        opens: 'left',
        placeholder: 'Select Date Range',
        startDate: start_date ? start_date : moment().subtract(7, 'days'),
        endDate: end_date ? end_date : moment(),
    });
});
</script>
@endsection