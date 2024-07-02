@extends('layouts.user_app', [
  'namePage' => 'Assembly Floating Stocks',
  'activePage' => 'assembly_floating_stocks',
  'pageHeader' => 'Assembly Floating Stocks',
  'pageSpan' => Auth::user()->employee_name
])

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('js/daterange/daterangepicker.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />
@endsection

@section('content')
<div class="panel-header"></div>
    <div class="row p-2" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
        <div class="col-12 m-0 bg-white border">
            {{-- <div class="row mt-2 p-2">
                <div class="col-3 offset-3 d-flex justify-content-end align-items-center">
                    <label class="pt-1"><b>Select Date</b></label>
                </div>
                <div class="col-3">
                    <input type="text" class="form-control" id="date">
                </div>
            </div> --}}
            <div id="tbl" class="col-12 pt-2 mx-auto overflow-auto" style="max-height: 90vh;">
                <div class="container p-5 d-flex justify-content-center align-items-center">
                    <div class="spinner-border"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/daterange/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
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

    const showNotification = (color, message, icon) =>{
        $.notify({
            icon: icon,
            message: message
        },{
            type: color,
            timer: 5000,
            placement: {
                from: 'top',
                align: 'center'
            }
        });
    }

    const load = (page = 1) => {
        const date = $('#date').val()
        const operation = $('#operation').val();

        $.ajax({
            url: '/report/assembly_floating_stocks',
            type:"GET",
            success: (data) => {
                $('#tbl').html(data)
            },
            error: (jqXHR, textStatus, errorThrown) => {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
              showNotification('danger', 'An error occured. Please try again.', 'now-ui-icons travel_info')
            }
        });
    }

    load(1)
});
</script>
@endsection