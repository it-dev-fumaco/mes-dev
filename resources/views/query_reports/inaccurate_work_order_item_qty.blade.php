


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
                      <h3 class="title text-left p-0 ml-3" style="margin: auto 20pt;">Report</h3>
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
            <h5 class="text-center font-weight-bold">Completed Production Orders with Inaccurate Required Qty, Transferred Qty and Consumed Qty</h5>
            <div class="pull-right font-weight-bold m-2">
                Total Record(s): <span class="badge badge-primary" style="font-size: 12pt;">{{ $query->total() }}</span>
            </div>
            <table class="table table-bordered" border="1">
                <thead style="font-size: 7pt;">
                    <th class="text-center font-weight-bold" style="width: 10%;">Created On</th>
                    <th class="text-center font-weight-bold" style="width: 10%;">Prod. Order</th>
                    <th class="text-center font-weight-bold" style="width: 10%;">Production Item</th>
                    <th class="text-center font-weight-bold" style="width: 10%;">Qty</th>
                    <th class="text-center font-weight-bold" style="width: 10%;">Material Transferred for Manufacturing</th>
                    <th class="text-center font-weight-bold" style="width: 10%;">Produced Qty</th>
                    <th class="text-center font-weight-bold" style="width: 10%;">Item Code</th>
                    <th class="text-center font-weight-bold" style="width: 10%;">Required Qty</th>
                    <th class="text-center font-weight-bold" style="width: 10%;">Transferred Qty</th>
                    <th class="text-center font-weight-bold" style="width: 10%;">Consumed Qty</th>
                </thead>
                <tbody>
                    @forelse ($query_grouped as $production_order => $items)
                    @foreach ($items as $row)
                    <tr>
                        @if ($loop->first)
                        <td rowspan="{{ count($items) }}" class="text-center">{{ \Carbon\Carbon::parse($items[0]->creation)->format('M-d-Y h:i A') }}</td>
                        <td rowspan="{{ count($items) }}" class="text-center font-weight-bold">{{ $items[0]->name }}</td>
                        <td rowspan="{{ count($items) }}" class="text-center">{{ $items[0]->production_item }}</td>
                        <td rowspan="{{ count($items) }}" class="text-center">{{ $items[0]->qty }}</td>
                        <td rowspan="{{ count($items) }}" class="text-center">{{ $items[0]->material_transferred_for_manufacturing }}</td>
                        <td rowspan="{{ count($items) }}" class="text-center">{{ $items[0]->produced_qty }}</td>
                        @endif
                        <td class="text-center">{{ $row->item_code }}</td>
                        <td class="text-center">{{ $row->required_qty }}</td>
                        <td class="text-center">{{ $row->transferred_qty }}</td>
                        <td class="text-center">{{ $row->consumed_qty }}</td>
                    </tr>
                    @endforeach
                    @empty
                    <tr>
                        <td colspan="10" class="text-center font-weight-bold">No record(s) found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
     
         <div class="float-right mt-4">
            {!! $query->appends(request()->query())->links('pagination::bootstrap-4') !!}
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