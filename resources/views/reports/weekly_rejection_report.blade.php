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
                         <span class="d-block m-0 p-0" style="font-size: 14pt;">{{ date('M-d-Y') }}</span>
                         <span class="d-block m-0 p-0" style="font-size: 10pt;">{{ date('l') }}</span>
                      </div>
                   </td>
                   <td style="width: 20%; border-right: 5px solid white;">
                      <h3 class="title" style="margin: auto;"><span id="current-time">--:--:-- --</span></h3>
                   </td>
                   <td style="width: 50%">
                      <h3 class="title text-left p-0 ml-3" style="margin: auto 20pt;">Weekly Rejection Report - {{ $operation }}</h3>
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
            <h5 class="p-2">{{ Carbon\Carbon::now()->subDays(7)->format('F d, Y').' to '.Carbon\Carbon::now()->format('F d, Y') }}</h5>
            <table class="table table-bordered">
                <tr>
                    <th>production_order</th>
                    <th>sales_order</th>
                    <th>customer</th>
                    <th>material_request</th>
                    <th>workstation</th>
                    <th>from_time</th>
                    <th>to_time</th>
                    <th>good</th>
                    <th>reject</th>
                    <th>operator_name</th>
                    <th>status</th>
                    <th>reject_reason</th>
                </tr>
                @forelse($reject_arr as $reject)
                    <tr>
                        <td>{{ $reject['production_order'] }}</td>
                        <td>{{ $reject['sales_order'] }}</td>
                        <td>{{ $reject['customer'] }}</td>
                        <td>{{ $reject['material_request']}}</td>
                        <td>{{ $reject['workstation'] }}</td>
                        <td>{{ $reject['from_time'] }}</td>
                        <td>{{ $reject['to_time'] }}</td>
                        <td>{{ $reject['good'] }}</td>
                        <td>{{ $reject['reject'] }}</td>
                        <td>{{ $reject['operator_name'] }}</td>
                        <td>{{ $reject['status'] }}</td>
                        <td>{{ $reject['reject_reason'] }}</td>
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
});
</script>
@endsection