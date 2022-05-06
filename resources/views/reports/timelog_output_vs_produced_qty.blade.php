@extends('layouts.user_app', [
    'namePage' => 'Data Export',
    'activePage' => 'machine_list_export',
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
                        <h3 class="title text-left p-0 ml-3" style="margin: auto 20pt;">System Audit Report
                        </h3>
                     </td>
                  </tr>
               </table>
            </div>
         </div>
      </div>
  </div>
  
  <div class="container-fluid bg-white">
      <div class="row" style="margin-top: -90px">
          <div class="col-12 mx-auto bg-white p-2">
              <h5 class="text-center font-weight-bold">Timelogs Output vs Produced Qty for In Progress / Completed Production Orders</h5>
              <div class="pull-right font-weight-bold m-2">
                  Total Record(s): <span class="badge badge-primary" style="font-size: 12pt;">{{ $production_query->total() }}</span>
              </div>
              <table class="table table-bordered">
                  <thead style="font-size: 7pt;">
                     <th class="text-center font-weight-bold">Created At</th>
                        <th class="text-center font-weight-bold">Production Order</th>
                        <th class="text-center font-weight-bold">Item Code</th>
                        <th class="text-center font-weight-bold">Qty to Manufacture</th>
                        <th class="text-center font-weight-bold">Produced Qty</th>
                        <th class="text-center font-weight-bold">Feedback Qty</th>
                        <th class="text-center font-weight-bold">Status</th>
                        <th class="text-center font-weight-bold">Job Ticket / Timelogs</th>
                  </thead>
                  <tbody>
                      @forelse ($data as $row)
                      <tr>
                        <td class="text-center p-1">{{ $row['created_at'] }}</td>
                        <td class="text-center font-weight-bold p-1">{{ $row['production_order'] }}</td>
                        <td class="text-center p-1">{{ $row['item_code'] }}</td>
                        <td class="text-center p-1">{{ $row['qty_to_manufacture'] }}</td>
                        <td class="text-center p-1">{{ $row['produced_qty'] }}</td>
                        <td class="text-center p-1">{{ $row['feedback_qty'] }}</td>
                        <td class="text-center p-1">{{ $row['status'] }}</td>
                        <td class="text-center p-1">
                           <table class="table table-bordered table-sm m-0">
                              <thead>
                                 <td class="p-0 font-weight-bold" style="width: 35%;">Workstation</td>
                                 <td class="p-0 font-weight-bold" style="width: 15%;">Completed Qty</td>
                                 <td class="p-0 font-weight-bold" style="width: 20%;">Status</td>
                                 <td class="p-0 font-weight-bold" style="width: 20%;">Remarks</td>
                                 <td class="p-0 font-weight-bold" style="width: 10%;">Timelogs Qty</td>
                              </thead>
                              @foreach ($row['job_ticket'] as $row_1)
                              <tr>
                                 <td class="p-0" style="width: 35%;">{{ $row_1['workstation'] }}</td>
                                 <td class="p-0" style="width: 15%;">{{ $row_1['completed_qty'] }}</td>
                                 <td class="p-0" style="width: 20%;">{{ $row_1['status'] }}</td>
                                 <td class="p-0" style="width: 20%;">{{ $row_1['remarks'] }}</td>
                                 <td class="p-0" style="width: 10%;">
                                    <table class="table table-bordered table-sm m-0">
                                       @foreach ($row_1['time_logs'] as $row_2)
                                       <tr>
                                          <td class="p-0">{{ $row_2['good'] }}</td>
                                       </tr>
                                       @endforeach
                                    </table>
                                 </td>
                              </tr>
                              @endforeach
                           </table>
                        </td>
                    </tr> 
                      @empty
                          
                      @endforelse
                
                  </tbody>
              </table>
       
           <div class="float-right mt-4">
              {!! $production_query->appends(request()->query())->links('pagination::bootstrap-4') !!}
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
  });
  </script>
  @endsection