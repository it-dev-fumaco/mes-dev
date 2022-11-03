@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'operators_load_utilization',
    'pageHeader' => 'Operator Load Utilization',
    'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header"></div>
<div class="row p-0" style="margin-top: -190px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div id="sked2"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<link rel="stylesheet" href="{{ asset('css/jquery.skedTape.css') }}">
<script src="{{ asset('js/jquery.skedTape.js') }}"></script>
<script>
  $(document).ready(function(){    
    var operator_list = function () {
      var tmp = null;
     
      $.ajax({
          async: false,
          url:"/get_operators",
          type:"GET",
          success:function(data){
              var operator_arr = [];
              $.each(data, function (i, value) {
                  operator_arr.push({id: i, name: value});
              }); 

              tmp = operator_arr;
          }
      }); 

      return tmp;
    }();

    var timelogs = function () {
      var tmp = null;
     
      $.ajax({
          async: false,
          url:"/get_operator_timelogs",
          type:"GET",
          success:function(data){
              var operator_logs = [];
              $.each(data, function (i, value) {
                  operator_logs.push({
                      name: value.workstation + ' - ' + value.completed_qty + ' pcs',
                      location: value.operator_id,
                      start: new Date(value.from_time),
                      end: new Date(value.to_time),
                  });
              }); 

              tmp = operator_logs;
          }
      }); 

      return tmp;
    }();

    console.log(timelogs);

    // -------------------------- Helpers ------------------------------
    function today(hours, minutes) {
        var date = new Date();
        date.setHours(hours, minutes, 0, 0);
        return date;
    }
    function yesterday(hours, minutes) {
        var date = today(hours, minutes);
        date.setTime(date.getTime() - 24 * 60 * 60 * 1000);
        return date;
    }
    function tomorrow(hours, minutes) {
        var date = today(hours, minutes);
        date.setTime(date.getTime() + 24 * 60 * 60 * 1000);
        return date;
    }
    var start_date = new Date();
    start_date.setDate(start_date.getDate() - 7);
    start_date.setHours(6,0,0,0);
    var sked2Config = {
        caption: 'Operator Name',
        start: start_date,
        end: tomorrow(0, 0),
        showEventTime: true,
        showEventDuration: true,
        //locations: locations.map(function(location) {
          // var newLocation = $.extend({}, location);
            //delete newLocation.tzOffset;
          // return newLocation;
        //}),
        formatters: {
            date: function (date) {
                return $.fn.skedTape.format.date(date, 'm', '/');
            },
        },
        locations: operator_list.slice(),
        events: timelogs.slice(),
        tzOffset: 0,
        sorting: true,
        orderBy: 'name',
        showIntermission: true
    };
    var $sked2 = $.skedTape(sked2Config);
    $sked2.appendTo('#sked2').skedTape('render');
    //$sked2.skedTape('destroy');
    $sked2.skedTape(sked2Config);

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