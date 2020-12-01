@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'stock_entry_page',
])

@section('content')
<div class="panel-header" style="margin-top: -20px;">
   <div class="header text-center">
    <div class="row">
         <div class="col-md-12">
            <table style="text-align: center; width: 100%;">
               <tr>
                  <td style="width: 36%; border-right: 5px solid white;">
                     <h2 class="title">
                        <div class="pull-right" style="margin-right: 20px;">
                           <span style="display: block; font-size: 20pt;">{{ date('M-d-Y') }}</span>
                           <span style="display: block; font-size: 12pt;">{{ date('l') }}</span>
                        </div>
                     </h2>
                  </td>
                  <td style="width: 14%; border-right: 5px solid white;">
                     <h2 class="title" style="margin: auto;"><span id="current-time">--:--:-- --</span></h2>
                  </td>
                  <td style="width: 50%">
                     <h2 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Stock Entry</h2>
                  </td>
               </tr>
            </table>
         </div>
      </div>
   </div>
</div>
<div class="content" style="margin-top: -110px;">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card" style="background-color: #0277BD;">
                <div class="card-body" style="padding-bottom: 0;">
                    <div class="row">
                        <div class="col-md-9">
                            <h5 class="text-white font-weight-bold align-middle">Stock Entry List</h5>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Search">
                            </div>
                        </div>
                    </div>
                    <div class="row" style="background-color: #ffffff;">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="text-primary">
                                        <th class="text-center font-weight-bold">Series</th>
                                        <th class="text-center font-weight-bold">Production Order</th>
                                        <th class="text-center font-weight-bold">Reference No.</th>
                                        <th class="text-center font-weight-bold">BOM</th>
                                        <th class="text-center font-weight-bold">Status</th>
                                        <th class="text-center font-weight-bold">Actions</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($list as $row)
                                        <tr>
                                            <td class="text-center">{{ $row->name }}</td>
                                            <td class="text-center">{{ $row->production_order }}</td>
                                            <td class="text-center"><b>{{ $row->sales_order_no }}</b> <br> {{ $row->so_customer_name }} </td>
                                            <td class="text-center">{{ $row->bom_no }}</td>
                                            <td class="text-center">{{ $row->item_status }}</td>
                                            <td class="text-center">
                                                <img src="{{ asset('img/search.png') }}" width="37">
                                                <img src="{{ asset('img/print.png') }}" width="30">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ asset('/js/jquery.rfid.js') }}"></script>
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