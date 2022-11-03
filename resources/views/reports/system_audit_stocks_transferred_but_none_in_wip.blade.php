@extends('layouts.user_app', [
  'namePage' => 'Stocks Transferred but None in Assembly/Work in Progress',
  'activePage' => 'stocks_transferred_but_none_in_wip',
  'pageHeader' => 'Stocks Transferred but No Available in Assembly/Work in Progress',
  'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header">
</div>

<div class="row p-0" style="margin-top: -190px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
        <div class="col-12 mx-auto bg-white">
            <div class="row">
                <div class="col-12 mx-auto">
                    <div class="container-fluid">
                        <form action="/audit_report/stocks_transferred_but_none_in_wip" method="get">
                            <div class="row">
                                <div class="col-2 p-2">
                                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') ? request('search') : null }}"/>
                                </div>
                                <div class="col-2 p-2">
                                    <select name="warehouse" class="form-control">
                                        <option value="" {{ !request('warehouse') ? 'selected' : null }}>Select a Warehouse</option>
                                        @foreach ($filter_warehouses as $warehouse)
                                            <option value="{{ $warehouse }}" {{ request('warehouse') == $warehouse ? 'selected' : null }}>{{ $warehouse }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-1">
                                    <button type="submit" class="btn btn-sm btn-primary">Search</button>
                                </div>
                                <div class="offset-6 col-1">
                                    <h6 class="pt-2">Total: <span class="badge badge-primary" style="font-size: 11pt;">{{ $bin_arr->total() }}</span></h6>
                                </div>
                            </div>
                        </form>
                    </div>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Date Created</th>
                                <th class="text-center">Item Code</th>
                                <th class="text-center">Warehouse</th>
                                <th class="text-center">Bin Actual Qty</th>
                                <th class="text-center">Transferred Qty</th>
                                <th class="text-center">Production Orders</th>
                                <th class="text-center">Owner</th>
                            </tr>
                        </thead>
                        @forelse ($bin_arr as $po)
                            <tr>
                                <td class="text-center">{{ $po['creation'] }}</td>
                                <td class="text-center">{{ $po['item_code'] }}</td>
                                <td class="text-center">{{ $po['warehouse'] }}</td>
                                <td class="text-center">{{ ($po['actual_qty'] * 1).' '.$po['uom'] }}</td>
                                <td class="text-center">{{ ($po['transferred_qty'] * 1).' '.$po['uom'] }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#item-code-{{ $po['item_code'] }}-Modal">
                                        View Production Orders
                                    </button>
                                    
                                    <div class="modal fade" id="item-code-{{ $po['item_code'] }}-Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-xl" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">{{ $po['item_code'].' - '.$po['warehouse'] }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table table-bordered table-striped">
                                                        <tr>
                                                            <th>Production Order</th>
                                                            {{-- <th>Warehouse</th> --}}
                                                            <th>Status</th>
                                                            <th>Transferred Qty</th>
                                                            <th>Required Qty</th>
                                                        </tr>
                                                        @foreach ($po['production_orders'] as $item)
                                                            <tr>
                                                                <td>{{ $item->name }}</td>
                                                                {{-- <td>{{ $item->wip_warehouse }}</td> --}}
                                                                <td>{{ $item->status }}</td>
                                                                <td>{{ $item->transferred_qty * 1 }}</td>
                                                                <td>{{ $item->required_qty * 1 }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">{{ $po['owner'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan=6 class="text-center">No item(s)</td>
                            </tr>
                        @endforelse
                    </table>
                    <div class="float-right mt-4">
                        {!! $bin_arr->appends(request()->query())->links('pagination::bootstrap-4') !!}
                    </div>
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