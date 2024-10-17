@extends('layouts.user_app', [
  'namePage' => 'Data Export',
   'activePage' => 'machine_list_export',
   'pageHeader' => 'Operator Efficiency Report',
   'pageSpan' => Auth::user()->employee_name
])

@section('content')
@php
    $operations = ['Fabrication', 'Painting', 'Wiring and Assembly'];
@endphp
<div class="panel-header"></div>
<div class="row p-0" style="margin-top: -190px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
   <div class="col-12 mx-auto bg-white p-2">
      <h5 class="text-center font-weight-bold">Operator Efficiency Report</h5>

      <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <input type="text" class="form-control" id="daterange">
            </div>
            <div class="col-3">
                <select name="operation" class="form-control filter">
                    <option value="" disabled selected>Select an Operation</option>
                    <option value="">Select All</option>
                    @foreach ($operations as $i => $operation)
                        @php
                            $i++;
                        @endphp
                        <option value="{{ $i }}">{{ $operation }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-3">
                <select name="operator" class="form-control filter">
                    <option value="" disabled selected>Select an Operator</option>
                    <option value="">Select All</option>
                    @foreach ($operators as $id => $operator)
                        <option value="{{ $id }}">{{ $operator }}</option>
                    @endforeach
                </select>
            </div>
            <div id="tbl" class="col-12 my-2" style="max-height: 80vh; overflow-y: auto">
                <div class="text-center p-3">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
      </div>
   </div>
</div>
@endsection
  
@section('script')
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

        const load = () => {
            $('#tbl').html(`
                <div class="text-center p-3">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>`)
            const date = $('#daterange').val()
            const operation = $('select[name="operation"]').val()
            const operator = $('select[name="operator"]').val()
            $.ajax({
                url: '/report/operator_efficiency',
                type:"GET",
                data: {
                    date, operation, operator
                },
                success: (data) => {
                    $('#tbl').html(data)
                },
                error: (jqXHR, textStatus, errorThrown) => {
                    showNotification('danger', 'An error occured. Please try again.', 'now-ui-icons travel_info')
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        }

        load()

        $('#daterange').daterangepicker({
            opens: 'left',
            placeholder: 'Select Date Range',
            startDate: moment().subtract(7, 'days'),
            endDate: moment(),
            locale: {
                format: 'YYYY-MMM-DD',
                separator: " to "
            },
        });

        $("#daterange").on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MMM-DD') + ' to ' + picker.endDate.format('YYYY-MMM-DD'));
            load();
        });

        $("#daterange").on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
            load();
        });

        $(document).on('change', '.filter', function (e){
            e.preventDefault()
            load()
        })
    });
</script>
@endsection