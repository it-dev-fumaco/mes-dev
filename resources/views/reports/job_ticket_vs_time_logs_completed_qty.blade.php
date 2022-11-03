@extends('layouts.user_app', [
    'namePage' => 'Data Export',
   'activePage' => 'machine_list_export',
   'pageHeader' => 'System Audit Report',
   'pageSpan' => Auth::user()->employee_name
])
  
  @section('content')
<div class="panel-header"></div>
<div class="row p-0" style="margin-top: -190px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
   <div class="col-12 mx-auto bg-white p-2">
      <h5 class="text-center font-weight-bold">Job Ticket Completed Qty vs Timelogs Completed Qty for In Progress / Not Started Job Tickets</h5>
      <div class="pull-right font-weight-bold m-2">
         Total Record(s): <span class="badge badge-primary" style="font-size: 12pt;">{{ collect($job_ticket_data)->count() }}</span>
      </div>
      <table class="table table-bordered">
         <thead style="font-size: 7pt;">
            <th class="text-center font-weight-bold">Created At</th>
            <th class="text-center font-weight-bold">Production Order</th>
            <th class="text-center font-weight-bold">Workstation</th>
            <th class="text-center font-weight-bold">Completed Qty</th>
            <th class="text-center font-weight-bold">Status</th>
            <th class="text-center font-weight-bold">Remarks</th>
            <th class="text-center font-weight-bold">Timelogs Qty</th>
         </thead>
         <tbody>
            @forelse ($job_ticket_data as $production_order => $row)
            <tr>
               <td class="text-center p-1">{{ $row['created_at'] }}</td>
               <td class="text-center font-weight-bold p-1">{{ $row['production_order'] }}</td>
               <td class="text-center p-1">{{ $row['workstation'] }}</td>
               <td class="text-center p-1">{{ $row['completed_qty'] }}</td>
               <td class="text-center p-1">{{ $row['status'] }}</td>
               <td class="text-center p-1">{{ $row['remarks'] }}</td>
               <td class="text-center p-1">{{ $row['timelogs_completed_qty'] }}</td>
            </tr> 
            @empty
            <tr>
               <td colspan="6" class="text-center font-weight-bold">No record(s) found.</td>
            </tr>
            @endforelse
         </tbody>
      </table>
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
  });
  </script>
  @endsection