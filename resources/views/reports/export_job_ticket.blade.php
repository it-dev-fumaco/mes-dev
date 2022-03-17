@extends('layouts.user_app', [
  'namePage' => 'Data Export',
  'activePage' => 'job_ticket_export',
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
                      <h3 class="title text-left p-0 ml-3" style="margin: auto 20pt;">Job Ticket Timelogs Data Export</h3>
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
               <form action="/export/job_ticket" method="get">
                  <div class="row p-3">
                     <div class="col-3 input-group">
                        <input class="form-control" id="daterange" type="text" name="date"/>
                     </div>
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
                        <button class="btn btn-primary m-0 p-2 w-75" id="export-btn" tabindex="0" aria-controls="export-table" type="button" style="font-size: 14pt">
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
               <th>created_at</th>
               <th>production_order</th>
               <th>item_code</th>
               <th>description</th>
               <th>operation</th>
               <th>reference_no</th>
               <th>customer</th>
               <th>status</th>
               <th>workstation</th>
               <th>process</th>
               <th>good</th>
               <th>reject</th>
            </tr>
            @forelse($export_arr as $export)
               @php
                  $reference_number = $export['sales_order'] ? $export['sales_order'] : $export['material_request'];
               @endphp
               <tr>
                  <td>{{ $export['created_at'] }}</td>
                  <td>{{ $export['production_order'] }}</td>
                  <td>{{ $export['item_code'] }}</td>
                  <td>{{ $export['item_description'] }}</td>
                  <td>{{ $export['operation'] }}</td>
                  <td>{{ $reference_number }}</td>
                  <td>{{ $export['customer'] }}</td>
                  <td>{{ $export['status'] }}</td>
                  <td>{{ $export['workstation'] }}</td>
                  <td>{{ $export['process_name'] }}</td>
                  <td>{{ $export['good'] }}</td>
                  <td>{{ $export['reject'] }}</td>
               </tr>
            @empty
               <tr>
                  <td colspan="12" class="text-center">No result(s) found.</td>
               </tr>
            @endforelse
         </table>
         <div class="float-right mt-4">
            {!! $production_orders->appends(request()->query())->links('pagination::bootstrap-4') !!}
         </div>
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

   var start_date = "{{ request('date') ? explode(' - ', request('date'))[0] : null }}";
   var end_date = "{{ request('date') ? explode(' - ', request('date'))[1] : null }}";

   $('#daterange').daterangepicker({
      opens: 'left',
      placeholder: 'Select Date Range',
      startDate: start_date ? start_date : moment().subtract(29, 'days'),
      endDate: end_date ? end_date : moment()
   }, function(start, end, label) {
      console.log('OK');
   });

   $("#export-btn").click(function(){  
      var data = {
         status: "{{ request('status') }}",
         operation: "{{ request('operation') }}",
         date: "{{ request('date') }}"
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
               filename: $('#file_name').val(),
               fileext: ".xls"
            }); 

            $('#spinner').addClass('d-none');
			}
		});
   });
});
</script>
@endsection