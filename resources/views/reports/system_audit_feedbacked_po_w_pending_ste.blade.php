@extends('layouts.user_app', [
  'namePage' => 'Feedbacked Production Orders with Pending STE',
  'activePage' => 'system_audit_feedbacked_po_w_pending_ste',
  'pageHeader' => 'Feedbacked Production Orders with Pending STE',
  'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header">
</div>

<div class="row p-0" style="margin-top: -190px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
        <div class="col-12 mx-auto bg-white">
            <div class="row">
                <div class="col-10 mx-auto">
                    <div class="container-fluid">
                        <h6 class="pt-2">Total: <span class="badge badge-primary" style="font-size: 11pt;">{{ $ste->total() }}</span> </h6>
                    </div>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Date Created</th>
                                <th class="text-center">Production Order</th>
                                <th class="text-center">Purpose</th>
                                <th class="text-center">Stock Entry</th>
                                <th class="text-center">Stock Entry Status</th>
                                <th class="text-center">Owner</th>
                            </tr>
                        </thead>
                        @forelse ($ste as $po)
                            <tr>
                                <td class="text-center">{{ \Carbon\Carbon::parse($po->creation)->format('M d, Y') }}</td>
                                <td class="text-center">{{ $po->work_order }}</td>
                                <td class="text-center">{{ $po->purpose }}</td>
                                <td class="text-center">{{ $po->name }}</td>
                                <td class="text-center">{{ $po->docstatus == 0 ? 'Draft' : $po->docstatus }}</td>
                                <td class="text-center">{{ $po->owner }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan=6 class="text-center">No feedbacked production orders with pending STE</td>
                            </tr>
                        @endforelse
                    </table>
                </div>
            </div>
            <div class="float-right mt-4">
                {!! $ste->appends(request()->query())->links('pagination::bootstrap-4') !!}
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