@extends('layouts.user_app', [
  'namePage' => 'Production Orders Report',
  'activePage' => 'production_orders_report',
  'pageHeader' => 'Production Orders',
  'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header"></div>
    <div class="row p-2" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
        <div class="col-12 m-0 bg-white border">
            <div class="row mt-2 p-2">
                <div class="col-2 offset-3 d-flex justify-content-end align-items-center">
                    <label class="pt-1"><b>Select Date Range</b></label>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" id="daterange">
                </div>
                <div class="col-3">
                    <select name="status" class="form-control" id="status-selection"></select>
                </div>
            </div>
            <div id="tbl" class="col-12 pt-2 mx-auto overflow-auto"></div>
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

    var start_date = "{{ request('date') ? date('m/d/Y', strtotime(explode(' - ', request('date'))[0])) : null }}";
    var end_date = "{{ isset(explode(' - ', request('date'))[1]) ? date('m/d/Y', strtotime(explode(' - ', request('date'))[1])) : null }}";

    $('#daterange').daterangepicker({
        opens: 'left',
        placeholder: 'Select Date Range',
        startDate: start_date ? start_date : moment().subtract(7, 'days'),
        endDate: end_date ? end_date : moment(),
    });

    const load = (page = 1) => {
        const daterange = $('#daterange').val()
        const status = $('select[name="status"]').val()
        $.ajax({
            url: '/production_orders_report/{{ $operation }}',
            type:"GET",
            data: {
                page, daterange, status
            },
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

    load()

    $('#status-selection').select2({
        theme: 'bootstrap',
        containerCssClass: 'form-control h-100',
        dropdownCssClass: 'form-control',
        placeholder: 'Select a Status',
        allowClear: true,
        ajax: {
            url: '/production_orders_report/1',
            method: 'GET',
            dataType: 'json',
            data: function (data) {
                return {
                    q: data.term,
                    get_status: 1
                };
            },
            processResults: function (response) {
                return {
                    results: response.statuses
                };
            },
            cache: true
        }
    });

    $(document).on('select2:select', '#status-selection', function(e){
        load();
    }).on('select2:clear', function (event) {
        load();
    })

    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
		$(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));

		load();
	});

	$('#daterange').on('cancel.daterangepicker', function(ev, picker) {
		$(this).val('');

		load();
	});

    $(document).on('click', '.table-paginate a', function(event){
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        load(page);
    });
});
</script>
@endsection