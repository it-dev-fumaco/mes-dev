@extends('layouts.user_app', [
  'namePage' => 'Items in thier own BOM',
  'activePage' => 'items_in_their_own_bom',
  'pageHeader' => 'Items in thier own BOM',
  'pageSpan' => Auth::user() ? Auth::user()->employee_name : null
])

@section('content')
<div class="panel-header"></div>

<div class="row p-0" style="margin-top: -190px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
        <div class="col-12 mx-auto bg-white">
            <div class="row">
                <div class="col-12 mx-auto">
                    <div class="container-fluid">
                        <h6 class="pt-2">Total: <span class="badge badge-primary" style="font-size: 11pt;">{{ $report->total() }}</span> </h6>
                    </div>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">BOM</th>
                                <th class="text-center">Item</th>
                            </tr>
                        </thead>
                        @forelse ($report as $item)
                            <tr>
                                <td class="text-center" style="white-space: nowrap">{{ $item->name }}</td>
                                <td class="text-justify"><b>{{ $item->item }}</b> - {{ strip_tags($item->description) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan=6 class="text-center">No mismatched feedback status(es)</td>
                            </tr>
                        @endforelse
                    </table>
                </div>
            </div>
            <div class="float-right mt-4">
                {!! $report->appends(request()->query())->links('pagination::bootstrap-4') !!}
            </div>
        </div>
    </div>
<div id="active-tab"></div>

@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/daterange/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/daterange/daterangepicker.css') }}" />
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />
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