@extends('layouts.user_app', [
  'namePage' => 'Data Export',
  'activePage' => 'machine_list_export',
  'pageHeader' => 'Machine List Data Export',
  'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header">
</div>

<div class="row p-0" style="margin-top: -190px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
      <div class="col-12 mx-auto bg-white">
         <div class="row">
            <div class="col-10 mx-auto">
               <form action="/export/machine_list" method="get">
                    <div class="row p-3">
                        <div class="col-2"></div>
                        <div class="col-3 input-group">
                            <select class="custom-select" name="status">
                            <option {{ request('status') ? null : 'selected' }} disabled>Select a Status</option>
                            <option value="All" {{ request('status') == 'All' ? 'selected' : null }}>All</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->status }}" {{ request('status') == $status->status ? 'selected' : null }}>{{ $status->status }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="col-3 input-group">
                            <select name="operation" class="custom-select">
                            <option {{ request('operation') ? null : 'selected' }} disabled>Select an Operation</option>
                            <option value="All" {{ request('operation') == 'All' ? 'selected' : null }}>All</option>
                            @foreach ($operations_filter as $item)
                                <option value="{{ $item->operation_id }}" {{ request('operation') == $item->operation_id ? 'selected' : null }}>{{ $item->operation_name }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="col-1">
                            <button type="submit" class="btn btn-primary m-0">
                            Search
                            </button>
                        </div>
                        <div class="col-2">
                            <button class="btn btn-primary m-0 p-2 w-75" id="export-btn" tabindex="0" aria-controls="export-table" type="button" style="font-size: 14pt;">
                            Export&nbsp;
                            <div class="d-none spinner-border spinner-border-sm text-primary" id="spinner" role="status" style="color: #fff !important; font-size: 10px !important">
                                <span class="sr-only">Loading...</span>
                            </div>
                            </button>
                        </div>
                    </div>
               </form>
            </div>
         </div>
         <table class="table table-bordered">
            <tr>
               <th>machine_code</th>
               <th>machine_name</th>
               <th>model</th>
               <th>status</th>
               <th>operation</th>
            </tr>
            @forelse($machine_list as $export)
               <tr>
                  <td>{{ $export->machine_code }}</td>
                  <td>{{ $export->machine_name }}</td>
                  <td>{{ $export->model }}</td>
                  <td>{{ $export->status }}</td>
                  <td>{{ $export->operation_name }}</td>
               </tr>
            @empty
               <tr>
                  <td colspan="5" class="text-center">No result(s) found.</td>
               </tr>
            @endforelse
         </table>
         <div class="float-right mt-4">
            {!! $machine_list->appends(request()->query())->links('pagination::bootstrap-4') !!}
         </div>
      </div>
   </div>
<div id="active-tab"></div>
<div id="for-export" class="d-none"></div> <!-- file to be exported -->

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

    $("#export-btn").click(function(){
        var data = {
            status: "{{ request('status') }}",
            operation: "{{ request('operation') }}"
        };

        $('#spinner').removeClass('d-none');
        $.ajax({
            type: 'GET',
            url: '{{ url()->current() }}',
            data: data,
            success: function (result){
                $('#for-export').html(result);
                $("#export-table").table2excel({
                    name: "Worksheet Name",
                    filename: 'Machine-List.xls'
                }); 

                $('#spinner').addClass('d-none');
            }
        });
    });
});
</script>
@endsection