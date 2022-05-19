@extends('layouts.user_app', [
  'namePage' => 'Check if Production Order Items Transferred Qty is Matched with Required Qty',
  'activePage' => 'transferred_required_qty_mismatch',
])

@section('content')
<div class="panel-header" style="margin-top: -70px;">
    <div class="header text-center">
       <div class="row">
          <div class="col-md-9 text-white">
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
                      <h3 class="title text-left p-0 ml-3" style="margin: auto 20pt;">Mismatched Production Order Item Required Qty and Transferred Qty</h3>
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
            <div class="row">
                <div class="col-10 mx-auto">
                    <div class="container-fluid">
                        <h6 class="pt-2">Total: <span class="badge badge-primary" style="font-size: 11pt;">{{ $transferred_required_qty_mismatch->total() }}</span> </h6>
                    </div>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Date Created</th>
                                <th class="text-center">Production Order</th>
                                <th class="text-center">Item Code</th>
                                <th class="text-center">Required Qty</th>
                                <th class="text-center">Transferred Qty</th>
                                <th class="text-center">Owner</th>
                            </tr>
                        </thead>
                        @forelse ($transferred_required_qty_mismatch as $po)
                            <tr>
                                <td class="text-center">{{ \Carbon\Carbon::parse($po->creation)->format('M d, Y') }}</td>
                                <td class="text-center">{{ $po->name }}</td>
                                <td class="text-center">{{ $po->item_code }}</td>
                                <td class="text-center">{{ $po->required_qty }}</td>
                                <td class="text-center">{{ $po->transferred_qty }}</td>
                                <td class="text-center">{{ $po->owner }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan=6 class="text-center">No mismatched required qty and transferred qty</td>
                            </tr>
                        @endforelse
                    </table>
                </div>
            </div>
            <div class="float-right mt-4">
                {!! $transferred_required_qty_mismatch->appends(request()->query())->links('pagination::bootstrap-4') !!}
            </div>
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