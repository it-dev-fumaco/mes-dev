@extends('layouts.user_app', [
  'namePage' => 'Fabrication',
  'activePage' => 'production_schedule',
  'pageHeader' => 'Daily Production Schedule Report',
    'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header"></div>
<div class="row p-0" style="margin-top: -190px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          
          <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="production-sched-tab" data-toggle="tab" href="#production-sched" role="tab" aria-controls="production-sched" aria-selected="true">Fabrication</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="painting-tab" data-toggle="tab" href="#painting" role="tab" aria-controls="painting" aria-selected="false">Painting</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="assembly-tab" data-toggle="tab" href="#assembly" role="tab" aria-controls="assembly" aria-selected="false">Assembly</a>
            </li>
          </ul>
          <!-- Tab panes -->
          <div class="tab-content" style="min-height: 620px;">
            <div class="tab-pane active" id="production-sched" role="tabpanel" aria-labelledby="production-sched-tab">
              <div class="row" style="margin-top: 10px;">
                <div class="col-md-4 offset-md-4 text-center">
                  <div class="form-group" style="">
                    <label for="schedule-date-fabrication" style="font-size: 12pt; color: black; display: inline-block; margin-right: 1%;"><b>Scheduled Date:</b></label>
                    <input type="text" class="form-control form-control-lg schedule-date" value="{{ date('Y-m-d') }}" id="schedule-date-fabrication" style="display: inline-block; width: 40%; font-weight: bolder;">
                  </div>
                </div>
                <div class="col-md-12">
                  <div id="fabrication-sched-content"></div>
                </div>
              </div>              
            </div>
            <div class="tab-pane" id="painting" role="tabpanel" aria-labelledby="painting-tab">
              <div class="row" style="margin-top: 10px;">
                <div class="col-md-4 offset-md-4 text-center">
                  <div class="form-group" style="">
                    <label for="schedule-date-painting" style="font-size: 12pt; color: black; display: inline-block; margin-right: 1%;"><b>Scheduled Date:</b></label>
                    <input type="text" class="form-control form-control-lg schedule-date" value="{{ date('Y-m-d') }}" id="schedule-date-painting" style="display: inline-block; width: 40%; font-weight: bolder;">
                  </div>
                </div>
                <div class="col-md-12">
                  <div id="painting-sched-content"></div>
                </div>
              </div>  
            </div>
            <div class="tab-pane" id="assembly" role="tabpanel" aria-labelledby="assembly-tab">
              <div class="row" style="margin-top: 12px;">
                <div class="col-md-12">
                  <h5 class="title text-center">Assembly Section</h5>
                </div>
                <div class="col-md-12">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<div id="active-tab"></div>
<style type="text/css">
  #active-tab{
    display: none;
  }
  .scrolltbody tbody {
      display:block;
      height:620px;
      overflow-y:scroll;
  }
  .scrolltbody thead, .scrolltbody tbody tr {
      display:table;
      width:100%;
      table-layout:fixed;
  }
  .scrolltbody thead {
      width: calc( 100% - 1em )
  }
</style>
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
<script>
$(document).ready(function(){
  loadWorkstationFabrication();
  loadWorkstationPainting();
  $('.schedule-date').datepicker({
    'format': 'yyyy-mm-dd',
    'autoclose': true
  });

  $('#schedule-date-fabrication').change(function(){
    loadWorkstationFabrication();
  });

  $('#schedule-date-painting').change(function(){
    loadWorkstationPainting();
  });

  $(document).on('click', '#workstation-tab-list .nav-link', function(){
    $('#active-tab').text($(this).attr('id'));
  });

  function loadWorkstationFabrication(){
    var schedule_date = $('#schedule-date-fabrication').val();
    var type = 'Fabrication';
    $.ajax({
      url:"/workstation_sched",
      type:"GET",
      data: {schedule_date: schedule_date, type:type},
      success:function(data){
        $('#fabrication-sched-content').html(data);
        var active = $('#active-tab').text();
        if (active) {
          $('#' + active).tab('show');
        }
      }
    });
  }

  function loadWorkstationPainting(){
    var schedule_date = $('#schedule-date-painting').val();
    var type = 'Painting';
    $.ajax({
      url:"/workstation_sched",
      type:"GET",
      data: {schedule_date: schedule_date, type:type},
      success:function(data){
        $('#painting-sched-content').html(data);
        var active = $('#active-tab').text();
        if (active) {
          $('#' + active).tab('show');
        }
      }
    });
  }

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