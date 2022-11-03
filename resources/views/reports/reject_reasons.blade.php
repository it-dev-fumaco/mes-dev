@extends('layouts.user_app', [
  'namePage' => 'Rejection Reasons Report',
  'activePage' => 'weekly_rejection_report',
  'pageHeader' => 'Rejection Reasons Logs',
  'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header"></div>
    <div class="row p-2" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
        <div class="col-12 m-0 bg-white border">
            <form action="/reject_reasons_report">
                <div class="row p-0 m-0">
                    <div class="p-1 mt-1 mb-1 col-2">
                        @php
                            $start = \Carbon\Carbon::now()->subDays(7);
                            $end = \Carbon\Carbon::now();
                            if(request()->has('date')){
                                $start = \Carbon\Carbon::parse(explode(' - ', request('date'))[0]);
                                $end = isset(explode(' - ', request('date'))[1]) ? \Carbon\Carbon::parse(explode(' - ', request('date'))[1]) : null;
                            }
                        @endphp
                        <span class="d-block font-weight-bold text-center">
                            {{ $start->startOfDay()->format('M. d, Y').' - '.$end->endOfDay()->format('M. d, Y') }}<br/>({{ $end->diffInDays($start) }} day/s)
                        </span>
                    </div>
                    <div class="p-1 mt-1 mb-1 col-2" style="border-left: 10px solid #F40305;">
                        <small class="d-block" style="font-size: 8pt;">Total Critical Reject(s)</small>
                        <h5 class="d-block font-weight-bold m-0">{{ number_format($counts_arr['critical']) }}</h5>
                    </div>
                    <div class="p-1 mt-1 mb-1 col-2" style="border-left: 10px solid #FFC107;">
                        <small class="d-block" style="font-size: 8pt;">Total Major Reject(s)</small>
                        <h5 class="d-block font-weight-bold m-0">{{ number_format($counts_arr['major']) }}</h5>
                    </div>
                    <div class="p-1 mt-1 mb-1 col-2" style="border-left: 10px solid #27AE60;">
                        <small class="d-block" style="font-size: 8pt;">Total Minor Reject(s)</small>
                        <h5 class="d-block font-weight-bold m-0">{{ number_format($counts_arr['minor']) }}</h5>
                    </div>
                    <div class="p-1 mt-1 mb-1 col-4">
                        <div class="d-flex flex-row align-items-center">
                            <div class="col-5 pt-1 pl-2 pr-2 pb-1">
                                <select name="operation_id" class="form-control rounded">
                                    <option value="" disabled>Select Operation</option>
                                    @foreach ($operations as $operation)
                                        <option value="{{ $operation->operation_id }}" {{ $operation->operation_id == $operation_id ? 'selected' : null }}>{{ $operation->operation_name }}</option>
                                    @endforeach
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
            <div class="col-12 pt-4 mx-auto overflow-auto" style="height: 70vh;">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="text-white bg-secondary reject-font-size">
                        <tr>
                            <th class="text-left p-1" style="font-size: 10pt;">Type</th>
                            <th class="text-left p-1" style="font-size: 10pt;">Category</th>
                            <th class="text-left p-1" style="font-size: 10pt;">Reason</th>
                            <th class="text-center p-1" style="font-size: 10pt;">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reject_reasons as $reason)
                            <tr>
                                <td class="text-left p-1 reject-font-size">{{ $reason->type }}</td>
                                <td class="text-left p-1 reject-font-size">{{ $reason->reject_category_name }}</td>
                                <td class="text-left p-1 reject-font-size">{{ $reason->reject_reason }}</td>
                                <td class="text-center p-1 reject-font-size">{{ $reason->count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan=4 class="text-center">No result(s) found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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