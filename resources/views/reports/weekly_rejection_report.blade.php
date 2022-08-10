@extends('layouts.user_app', [
  'namePage' => 'Weekly Rejection Report',
  'activePage' => 'weekly_rejection_report',
])

@section('content')
<div class="panel-header" style="margin-top: -70px;">
    <div class="header text-center">
       <div class="row">
          <div class="col-md-8 text-white">
             <table style="text-align: center; width: 100%;">
                <tr>
                   <td style="width: 30%; border-right: 5px solid white;">
                      <div class="pull-right title mr-3">
                         <span class="d-block m-0 p-0" style="font-size: 12pt;">{{ date('M-d-Y') }}</span>
                         <span class="d-block m-0 p-0" style="font-size: 9pt;">{{ date('l') }}</span>
                      </div>
                   </td>
                   <td style="width: 20%; border-right: 5px solid white;">
                      <h3 class="title" style="margin: auto;"><span id="current-time">--:--:-- --</span></h3>
                   </td>
                   <td style="width: 50%">
                      <h3 class="title text-left p-0 ml-3" style="margin: auto 14pt;">{{ $operation }} Rejection Report</h3>
                   </td>
                </tr>
             </table>
          </div>
       </div>
    </div>
</div>

<div class="container-fluid bg-white">
    <div class="row" style="margin-top: -90px">
        <div class="col-12 mx-auto bg-white">
            @php
                switch($operation){
                    case 'Fabrication':
                        $link = 1;
                        break;
                    case 'Painting':
                        $link = 2;
                        break;
                    case 'Wiring and Assembly':
                        $link = 3;
                        break;
                    default:
                        $link = 0;
                        break;
                }
            @endphp
            <form action="/weekly_rejection_report/{{ $link }}">
                <div class="row">
                    <div class="col-8">
                        @php
                            $start = \Carbon\Carbon::now()->subDays(7);
                            $end = \Carbon\Carbon::now();
                            if(request()->has('date')){
                                $start = \Carbon\Carbon::parse(explode(' - ', request('date'))[0]);
                                $end = isset(explode(' - ', request('date'))[1]) ? \Carbon\Carbon::parse(explode(' - ', request('date'))[1]) : null;
                            }
                        @endphp
                        <h5 class="pt-3"><b>Period: {{ $start->startOfDay()->format('F d, Y').' - '.$end->endOfDay()->format('F d, Y') }} ({{ $end->diffInDays($start) }} day/s)</b></h5>
                    </div>
                    <div class="col-3">
                        <input type="text" class='form-control m-2' id="daterange" name='date' />
                    </div>
                    <div class="col-1">
                        <button class="btn btn-primary btn-xs p-2 w-100" type="submit">Search</button>
                    </div>
                </div>
            </form>
            <table class="table table-bordered">
                <tr>
                    <th class="text-center">Production Order</th>
                    <th class="text-center">Reference Order</th>
                    <th class="text-center">Customer</th>
                    <th class="text-center">Workstation</th>
                    <th class="text-center">Start</th>
                    <th class="text-center">End</th>
                    <th class="text-center">Good</th>
                    <th class="text-center">Reject</th>
                    <th class="text-center">Reason</th>
                    <th class="text-center">Operator</th>
                    <th class="text-center">Status</th>
                </tr>
                @forelse($reject_arr as $reject)
                    <tr>
                        <td class="reject-font-size">{{ $reject['production_order'] }}</td>
                        <td class="reject-font-size">{{ $reject['sales_order'] ? $reject['sales_order'] : $reject['material_request'] }}</td>
                        <td class="reject-font-size">{{ $reject['customer'] }}</td>
                        <td class="reject-font-size">{{ $reject['workstation'] }}</td>
                        <td class="reject-font-size">{{ $reject['from_time'] }}</td>
                        <td class="reject-font-size">{{ $reject['to_time'] }}</td>
                        <td class="reject-font-size">{{ $reject['good'] }}</td>
                        <td class="reject-font-size">{{ $reject['reject'] }}</td>
                        <td class="reject-font-size">{{ $reject['reject_reason'] }}</td>
                        <td class="reject-font-size">{{ $reject['operator_name'] }}</td>
                        <td class="reject-font-size">{{ $reject['status'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center">No result(s) found.</td>
                    </tr>
                @endforelse
            </table>
            <div class="float-right mt-4">
                {!! $rejection_logs->appends(request()->query())->links('pagination::bootstrap-4') !!}
            </div>
        </div>
    </div>
</div>
<style>
    .reject-font-size{
        font-size: 10pt;
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