@extends('layouts.user_app', [
  'namePage' => 'Machine Breakdown History',
  'activePage' => 'machine_breakdown_history',
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
                      <h3 class="title text-left p-0 ml-3" style="margin: auto 20pt;">Machine Breakdown History</h3>
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
            <table class="table table-bordered">
                <tr>
                    <th>machine_code</th>
                    <th>machine_name</th>
                    <th>model</th>
                    <th>status</th>
                    <th>operation</th>
                    <th>breakdown_history</th>
                </tr>
                @forelse($machine_arr as $i => $machine)
                    <tr>
                        <td>{{ $machine['machine_code'] }}</td>
                        <td>{{ $machine['machine_name'] }}</td>
                        <td>{{ $machine['model'] }}</td>
                        <td>{{ $machine['status']}}</td>
                        <td>{{ $machine['operation'] }}</td>
                        <td class='text-center'>
                            @if ($machine['breakdown_history'])
                                <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#history-{{ $i }}-Modal">
                                    View Machine Breakdown History
                                </button>
                            
                                <div class="modal fade" id="history-{{ $i }}-Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">{{ $machine['machine_code'] }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="container" style="overflow: auto;">
                                                    @php
                                                        $breakdown_history = collect($machine['breakdown_history'])->chunk(10);
                                                    @endphp     
                                                    <!-- Tab panes -->
                                                    <div class="tab-content">
                                                        @for($a = 0; $a < count($breakdown_history); $a++)
                                                            <div id="breakdown-tab-{{ $i.'-'.$a }}" class="container tab-pane{{ $a == 0 ? " active" : null }}" style="padding: 8px 0 0 0;">
                                                                <table class="table table-striped">
                                                                    <tr>
                                                                        <th>date_reproted</th>
                                                                        <th>breakdown_reason</th>
                                                                        <th>status</th>
                                                                        <th>category</th>
                                                                        <th>findings</th>
                                                                        <th>work_started</th>
                                                                        <th>work_done</th>
                                                                        <th>hold_reason</th>
                                                                        <th>staff</th>
                                                                        <th>type</th>
                                                                        <th>remarks</th>
                                                                        <th>date_resolved</th>
                                                                    </tr>
                                                                    @foreach ($breakdown_history[$a] as $breakdown)
                                                                        <tr>
                                                                            <td>{{ $breakdown['date_reproted'] ? Carbon\Carbon::parse($breakdown['date_reproted'])->format('M d, Y') : null }}</td>
                                                                            <td>{{ $breakdown['breakdown_reason'] }}</td>
                                                                            <td>
                                                                                @php
                                                                                    switch ($breakdown['status']) {
                                                                                        case 'Done':
                                                                                            $badge = 'success';
                                                                                            break;
                                                                                        case 'On Hold':
                                                                                            $badge = 'warning';
                                                                                            break;
                                                                                        case null:
                                                                                        case 'Pending':
                                                                                            $badge = 'secondary';
                                                                                            break;
                                                                                        default:
                                                                                            $badge = 'primary';
                                                                                            break;
                                                                                    }
                                                                                @endphp
                                                                                <span class="badge badge-{{ $badge }}">{{ $breakdown['status'] ? $breakdown['status'] : 'Unkown Status' }}</span>
                                                                            </td>
                                                                            <td>{{ $breakdown['category'] }}</td>
                                                                            <td>{{ $breakdown['findings'] }}</td>
                                                                            <td>{{ $breakdown['work_started'] ? Carbon\Carbon::parse($breakdown['work_started'])->format('M d, Y') : null }}</td>
                                                                            <td>{{ $breakdown['work_done'] }}</td>
                                                                            <td>{{ $breakdown['hold_reason'] }}</td>
                                                                            <td style="white-space: nowrap">{{ $breakdown['staff'] }}</td>
                                                                            <td>{{ $breakdown['type'] }}</td>
                                                                            <td>{{ $breakdown['remarks'] }}</td>
                                                                            <td>{{ $breakdown['date_resolved'] ? Carbon\Carbon::parse($breakdown['date_resolved'])->format('M d, Y') : null }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </table>
                                                            </div>
                                                        @endfor
                                                    </div>
                                                </div>
                                                @if (count($breakdown_history) > 1)
                                                    <ul class="nav nav-tabs pt-2 justify-content-end" role="tablist">
                                                        @for($b = 0; $b < count($breakdown_history); $b++)
                                                            <li class="nav-item">
                                                                <a class="nav-link {{ $b == 0 ? 'active' : null }}" data-toggle="tab" href="#breakdown-tab-{{ $i.'-'.$b }}">{{ $b + 1 }}</a>
                                                            </li>
                                                        @endfor
                                                    </ul>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               
                            @else
                                <span class="text-muted">No Actions Available</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No result(s) found.</td>
                    </tr>
                @endforelse
            </table>
            <div class="float-right mt-4">
                {!! $machines->appends(request()->query())->links('pagination::bootstrap-4') !!}
            </div>
        </div>
    </div>
</div>
<style>
    .nav-tabs{
        border-top: 1px solid #DEE2E6 !important;
        border-bottom: none !important;
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
});
</script>
@endsection