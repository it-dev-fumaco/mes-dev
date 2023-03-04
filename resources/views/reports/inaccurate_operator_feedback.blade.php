@extends('layouts.user_app', [
  'namePage' => 'Inaccurate Operator Feedback',
  'activePage' => 'inaccurate_operator_feedback',
  'pageHeader' => 'Inaccurate Operator Feedback',
  'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header"></div>

<div class="row p-0" style="margin-top: -190px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
        <div class="col-12 mx-auto bg-white">
            <div class="row">
                <div class="col-12 pt-3 mx-auto">
                    <div class="row">
                        <div class="col-3 offset-6">
                            <div class="row">
                                <div class="col-3 p-0" style="display: flex; justify-content: center; align-items: center;">
                                    <b>Date Filter</b>
                                </div>
                                <div class="col-9 p-0">
                                    <input type="text" class="form-control tbl-filter daterange p-2 rounded" id="date-range">
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="row">
                                <div class="col-3 p-0" style="display: flex; justify-content: center; align-items: center;">
                                    <b>Operation</b>
                                </div>
                                <div class="col-9 p-0">
                                    <select class="form-control tbl-filter rounded" id="operation">
                                        @foreach ($operations as $operation)
                                            <option value="{{ $operation->operation_id }}">{{ $operation->operation_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 mx-auto pt-3" id="tbl-container"></div>
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

    $(document).on('change', '.tbl-filter', function(e){
        e.preventDefault();
        load_tbl();
    });

    $(document).on('click', '#inaccurate-operator-feedback-report-pagination a', function(event){
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        load_tbl(page);
    });

    load_tbl();
    function load_tbl(page){
        $.ajax({
            type: 'GET',
            url: '/inaccurate_operator_feedback',
            data: {
                date_range: $('#date-range').val(),
                operation: $('#operation').val(),
                page: page
            },
            success: function(response){
                $('#tbl-container').html(response);
            }
        });
    }

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