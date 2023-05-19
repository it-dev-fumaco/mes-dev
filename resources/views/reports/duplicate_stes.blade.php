@extends('layouts.user_app', [
  'namePage' => 'Production Orders with Duplicated Withdrawal Slips',
  'activePage' => 'duplicated_withdrawal_slips',
  'pageHeader' => 'Production Orders with Duplicated Withdrawal Slips',
  'pageSpan' => Auth::check() ? Auth::user()->employee_name : null
])

@section('content')
<div class="panel-header"></div>

<div class="row p-0" style="margin-top: -190px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 85vh;">
    <div class="col-12" style="background-color: #fff !important">
        <div class="col-10 mx-auto" style="background-color: #fff !important">
            <table class="table table-bordered table-striped">
                <tr>
                    <th class="text-center p-1" rowspan=2>Production Order</th>
                    <th class="text-center p-1" rowspan=2>Item</th>
                    <th class="text-center p-1" rowspan=2>Required Qty</th>
                    <th class="text-center p-1" colspan=3 style="background-color: rgba(47, 157, 88, .3)">Work Order</th>
                    <th class="text-center p-1" colspan=3 style="background-color: rgba(15, 111, 183, .3)">Stock Entry</th>
                    <th class="text-center p-1" rowspan=2>Stock Entries(Withdrawals)</th>
                </tr>
                <tr>
                    <th class="text-center p-1">Returned</th>
                    <th class="text-center p-1">Withdrawn</th>
                    <th class="text-center p-1">Transfered</th>
                    
                    <th class="text-center p-1">Returned</th>
                    <th class="text-center p-1">Withdrawn</th>
                    <th class="text-center p-1">Transfered</th>
                </tr>
                @foreach ($stock_entry_arr as $ste)
                <tr>
                    <td class="text-center p-1">
                        {{ $ste->name }}<br/>
                        <small class="text-muted">{{ Carbon\Carbon::parse($ste->creation)->format('M. d, Y h:i A') }}</small>
                    </td>
                    <td class="text-center p-1">{{ $ste->item_code }}</td>
                    <td class="text-center p-1">{{ $ste->required_qty }}</td>
                    
                    <td class="text-center p-1">{{ $ste->returned_qty }}</td>
                        <td class="text-center p-1">{{ $ste->transferred_qty }}</td>
                        <td class="text-center p-1">{{ $ste->transferred_qty - $ste->returned_qty }}</td>

                        <td class="text-center p-1">{{ $ste->returned_qty_on_stock_entry }}</td>
                        <td class="text-center p-1">{{ $ste->transfered_qty_on_stock_entry }}</td>
                        <td class="text-center p-1">
                            <b>{{ $ste->total_transfered_on_stock_entry }}</b>
                        </td>
                        
                        <td class="text-center p-1">
                            @foreach ($ste->stock_entries as $ref => $qty)
                            <p>{{ $ref }} ({{ $qty }})</p>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </table>
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
    $(".daterange").daterangepicker({
        placeholder: 'Select Date Range',
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: {
            format: 'YYYY-MMM-DD',
            separator: " to "
        },
        startDate: moment().subtract(7, 'days'), endDate: moment(),
    });

    // $(document).on('change', '.tbl-filter', function(e){
    //     e.preventDefault();
    //     $('.tbl-container').each(function( index ) {
    //         load_tbl($(this).data('operation'), $(this));
    //     });
    // });

    // $('.tbl-container').each(function( index ) {
    //     load_tbl($(this).data('operation'), $(this));
    // });

    // function load_tbl(operation, el){
    //     $.ajax({
    //         type: 'GET',
    //         url: '/inaccurate_operator_feedback',
    //         data: {
    //             date_range: $('#date-range').val(),
    //             operation: operation,
    //         },
    //         success: function(response){
    //             el.html(response);
    //         }
    //     });
    // }

    // $(document).on('click', '.export-btn', function (e) {
    //     e.preventDefault();

    //     var op = $(this).data('operation');
    //     var op_name = $(this).data('operation-name');

    //     $.ajax({
    //         type: 'GET',
    //         url: '/inaccurate_operator_feedback',
    //         data: {
    //             date_range: $('#date-range').val(),
    //             operation: op,
    //         },
    //         success: function(response){
    //             $('#for-export').html(response);
    //             $('#for-export').find('table').table2excel({
    //                 name: "Worksheet Name",
    //                 filename: op_name + '.xls'
    //             }); 
    //         }
    //     });
    // });

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