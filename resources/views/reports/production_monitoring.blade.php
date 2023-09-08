@extends('layouts.user_app', [
  'namePage' => 'Data Export',
  'activePage' => 'reports',
  'pageHeader' => 'Production Monitoring Report',
  'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header"></div>
<div class="row p-0" style="margin-top: -190px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
      <div class="col-12 mx-auto bg-white">
         <form action="/export/job_ticket" method="get">
            <div class="row p-2">
               <div class="col-1 offset-1 pt-2 pr-0 text-right">
                  <span class="m-0" style="font-size: 12pt;">Date Range</span>
               </div>
               <div class="col-3 input-group">
                  <input type="text" class="form-control rounded border" name="daterange" placeholder="Select Date Range">
               </div>
               <div class="col-3 input-group">
                  <select name="operation" class="custom-select">
                     <option selected disabled>Select an Operation</option>
                     <option value="">All</option>
                     @foreach ($operations as $id => $operation)
                        <option value="{{ $id }}">{{ $operation }}</option>
                     @endforeach
                  </select>
               </div>
               <div class="col-3 input-group">
                  <select name="module" class="custom-select">
                     <option selected disabled>Select a Reference Type</option>
                     <option value="">All</option>
                     <option value="sales_order">Sales Order (SO)</option>
                     <option value="material_request">Material Request (MREQ)</option>
                  </select>
               </div>
               <div class="col-1 p-0">
                  <button class="btn btn-primary m-0 w-100" id="export-btn" tabindex="0" aria-controls="export-table" type="button">
                     Export&nbsp;
                     <div class="d-none spinner-border spinner-border-sm text-primary" id="spinner" role="status" style="color: #fff !important; font-size: 10px !important">
                        <span class="sr-only">Loading...</span>
                     </div>
                  </button>
               </div>
            </div>
         </form>
         <div id="tbl-container"></div>
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
   load_tbl(1)
   setInterval(updateClock, 1000);

   $(document).on('change', 'select', (e) => {
      e.preventDefault()
      load_tbl(1)
   })

   $(document).on('click', '#export-btn', (e) => {
      e.preventDefault()
      $('#spinner').removeClass('d-none');

      var data = '?export=1'
      data += '&operation=' + $('select[name="operation"]').val() ? $('select[name="operation"]').val() : ''
      data += '&module=' + $('select[name="module"]').val() ? $('select[name="module"]').val() : ''
      data += '&daterange=' + $('input[name="daterange"]').val() ? $('input[name="daterange"]').val() : ''

      window.location.href = '{{ url()->current() }}' + data
      setTimeout(() => {
         $('#spinner').addClass('d-none')
      }, 3000);
   })

   $('input[name="daterange"]').daterangepicker({
      opens: 'left',
      placeholder: 'Select Date Range',
      startDate: moment().subtract(29, 'days'),
      endDate: moment(),
      locale: {
         format: 'MMM. D, YYYY'
      }
   });

   $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('MMM. D, YYYY') + ' - ' + picker.endDate.format('MMM. D, YYYY'));
      load_tbl(1)
   })

   $(document).on('click', '#pagination a', function(e){
      e.preventDefault();
      const page = $(this).attr('href').split('page=')[1];
      load_tbl(page);
   });

   function load_tbl(page){
      $.ajax({
			type: 'GET',
			url: '{{ url()->current() }}',
         data: {
            operation: $('select[name="operation"]').val(),
            module: $('select[name="module"]').val(),
            daterange: $('input[name="daterange"]').val(),
            page
         },
			success: (result) => {
				$('#tbl-container').html(result);
			},
         error: (xhr) => {
				$('#tbl-container').html('An error occured. Please try again.')
            $('#spinner').addClass('d-none')
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