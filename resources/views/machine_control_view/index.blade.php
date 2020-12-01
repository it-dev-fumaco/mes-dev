{{-- @extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'main_dashboard',
])

@section('content')
<link rel="stylesheet" href="{{ asset('css/style-machine-control.css') }}">
<script>document.getElementsByTagName("html")[0].className += " js";</script>
<div class="panel-header">
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
                     <h2 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Main Dashboard</h2>
                  </td>
               </tr>
            </table>
         </div>
      </div>
   </div>
</div>
<div class="content" style="margin-top: -80px;">
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h5 class="card-category"></h5>
               <h4 class="card-title">Operation: Fabrication</h4>
            </div>
            <div class="card-body">
              <div class="cd-schedule cd-schedule--loading margin-top-lg margin-bottom-lg js-cd-schedule">
    <div class="cd-schedule__timeline">
      <ul>
        <li><span>09:00</span></li>
        <li><span>09:30</span></li>
        <li><span>10:00</span></li>
        <li><span>10:30</span></li>
        <li><span>11:00</span></li>
        <li><span>11:30</span></li>
        <li><span>12:00</span></li>
        <li><span>12:30</span></li>
        <li><span>13:00</span></li>
        <li><span>13:30</span></li>
        <li><span>14:00</span></li>
        <li><span>14:30</span></li>
        <li><span>15:00</span></li>
        <li><span>15:30</span></li>
        <li><span>16:00</span></li>
        <li><span>16:30</span></li>
        <li><span>17:00</span></li>
        <li><span>17:30</span></li>
        <li><span>18:00</span></li>
      </ul>
    </div> <!-- .cd-schedule__timeline -->
  
    <div class="cd-schedule__events">
      <ul>
        <li class="cd-schedule__group">
          <div class="cd-schedule__top-info"><span>Monday</span></div>
  
          <ul>
            <li class="cd-schedule__event">
              <a data-start="09:30" data-end="10:00" data-content="event-abs-circuit" data-event="event-1" href="#0">
                <em class="cd-schedule__name">Abs Circuit</em>
              </a>
            </li>
  
            <li class="cd-schedule__event">
              <a data-start="11:00" data-end="12:30" data-content="event-rowing-workout" data-event="event-2" href="#0">
                <em class="cd-schedule__name">Rowing Workout</em>
              </a>
            </li>
  
            <li class="cd-schedule__event">
              <a data-start="14:00" data-end="15:15"  data-content="event-yoga-1" data-event="event-3" href="#0">
                <em class="cd-schedule__name">Yoga Level 1</em>
              </a>
            </li>
          </ul>
        </li>
  
        <li class="cd-schedule__group">
          <div class="cd-schedule__top-info"><span>Tuesday</span></div>
  
          <ul>
            <li class="cd-schedule__event">
              <a data-start="10:00" data-end="11:00"  data-content="event-rowing-workout" data-event="event-2" href="#0">
                <em class="cd-schedule__name">Rowing Workout</em>
              </a>
            </li>
  
            <li class="cd-schedule__event">
              <a data-start="11:30" data-end="13:00"  data-content="event-restorative-yoga" data-event="event-4" href="#0">
                <em class="cd-schedule__name">Restorative Yoga</em>
              </a>
            </li>
  
            <li class="cd-schedule__event">
              <a data-start="13:30" data-end="15:00" data-content="event-abs-circuit" data-event="event-1" href="#0">
                <em class="cd-schedule__name">Abs Circuit</em>
              </a>
            </li>
  
            <li class="cd-schedule__event">
              <a data-start="15:45" data-end="16:45"  data-content="event-yoga-1" data-event="event-3" href="#0">
                <em class="cd-schedule__name">Yoga Level 1</em>
              </a>
            </li>
          </ul>
        </li>
  
        <li class="cd-schedule__group">
          <div class="cd-schedule__top-info"><span>Wednesday</span></div>
  
          <ul>
            <li class="cd-schedule__event">
              <a data-start="09:00" data-end="10:15" data-content="event-restorative-yoga" data-event="event-4" href="#0">
                <em class="cd-schedule__name">Restorative Yoga</em>
              </a>
            </li>
  
            <li class="cd-schedule__event">
              <a data-start="10:45" data-end="11:45" data-content="event-yoga-1" data-event="event-3" href="#0">
                <em class="cd-schedule__name">Yoga Level 1</em>
              </a>
            </li>
  
            <li class="cd-schedule__event">
              <a data-start="12:00" data-end="13:45"  data-content="event-rowing-workout" data-event="event-2" href="#0">
                <em class="cd-schedule__name">Rowing Workout</em>
              </a>
            </li>
  
            <li class="cd-schedule__event">
              <a data-start="13:45" data-end="15:00" data-content="event-yoga-1" data-event="event-3" href="#0">
                <em class="cd-schedule__name">Yoga Level 1</em>
              </a>
            </li>
          </ul>
        </li>
  
        <li class="cd-schedule__group">
          <div class="cd-schedule__top-info"><span>Thursday</span></div>
  
          <ul>
            <li class="cd-schedule__event">
              <a data-start="09:30" data-end="10:30" data-content="event-abs-circuit" data-event="event-1" href="#0">
                <em class="cd-schedule__name">Abs Circuit</em>
              </a>
            </li>
  
            <li class="cd-schedule__event">
              <a data-start="12:00" data-end="13:45" data-content="event-restorative-yoga" data-event="event-4" href="#0">
                <em class="cd-schedule__name">Restorative Yoga</em>
              </a>
            </li>
  
            <li class="cd-schedule__event">
              <a data-start="15:30" data-end="16:30" data-content="event-abs-circuit" data-event="event-1" href="#0">
                <em class="cd-schedule__name">Abs Circuit</em>
              </a>
            </li>
  
            <li class="cd-schedule__event">
              <a data-start="17:00" data-end="18:30"  data-content="event-rowing-workout" data-event="event-2" href="#0">
                <em class="cd-schedule__name">Rowing Workout</em>
              </a>
            </li>
          </ul>
        </li>
  
        <li class="cd-schedule__group">
          <div class="cd-schedule__top-info"><span>Friday</span></div>
  
          <ul>
            <li class="cd-schedule__event">
              <a data-start="10:00" data-end="11:00"  data-content="event-rowing-workout" data-event="event-2" href="#0">
                <em class="cd-schedule__name">Rowing Workout</em>
              </a>
            </li>
  
            <li class="cd-schedule__event">
              <a data-start="12:30" data-end="14:00" data-content="event-abs-circuit" data-event="event-1" href="#0">
                <em class="cd-schedule__name">Abs Circuit</em>
              </a>
            </li>
  
            <li class="cd-schedule__event">
              <a data-start="15:45" data-end="16:45"  data-content="event-yoga-1" data-event="event-3" href="#0">
                <em class="cd-schedule__name">Yoga Level 1</em>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  
    <div class="cd-schedule-modal">
      <header class="cd-schedule-modal__header">
        <div class="cd-schedule-modal__content">
          <span class="cd-schedule-modal__date"></span>
          <h3 class="cd-schedule-modal__name"></h3>
        </div>
  
        <div class="cd-schedule-modal__header-bg"></div>
      </header>
  
      <div class="cd-schedule-modal__body">
        <div class="cd-schedule-modal__event-info"></div>
        <div class="cd-schedule-modal__body-bg"></div>
      </div>
  
      <a href="#0" class="cd-schedule-modal__close text-replace">Close</a>
    </div>
  
    <div class="cd-schedule__cover-layer"></div>
  </div> <!-- .cd-schedule -->
            </div>
         </div>
      </div>
   </div>
</div>

@endsection

@section('script')


  <script src="{{ asset('js/js-machine-control/util.js') }}"></script> 
  <!-- util functions included in the CodyHouse framework -->
  <script src="{{ asset('js/js-machine-control/main.js') }}"></script>
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
@endsection --}}


<div class='centered'>
  <div class='info'>
    <p>Try adding your own activity. Open the console and paste this command:</p>
    <div class='code'>schedule.activities.add(4,"Physics", "8.00-10.30", 102, "A", "B.Konon","pink");</div>
  </div>
  <div id='schedule'>
    <div class='s-legend'>

      <div class='s-cell s-head-info'>
        <div class='s-name'>TT</div>
      </div>
      @foreach($machines as $machine)
      <div class='s-week-day s-cell'>
        <div class='s-day'>{{ $machine->machine_name }}</div>
      </div>
      @endforeach
    </div>
    <div class='s-container s-block'>
      <div class='s-head-info'>
        <div class='s-head-hour'>
          <div class='s-number'>0</div>
          <div class='s-hourly-interval'>7.10-7.55</div>
        </div>
        <div class='s-head-hour'>
          <div class='s-number'>1</div>
          <div class='s-hourly-interval'>8.00 - 8.45</div>
        </div>
        <div class='s-head-hour'>
          <div class='s-number'>2</div>
          <div class='s-hourly-interval'>8.50 - 9.35</div>
        </div>
        <div class='s-head-hour'>
          <div class='s-number'>3</div>
          <div class='s-hourly-interval'>9.45 - 10.30</div>
        </div>
        <div class='s-head-hour'>
          <div class='s-number'>4</div>
          <div class='s-hourly-interval'>10.50 - 11.35</div>
        </div>
        <div class='s-head-hour'>
          <div class='s-number'>5</div>
          <div class='s-hourly-interval'>11.45 - 12.30</div>
        </div>
        <div class='s-head-hour'>
          <div class='s-number'>6</div>
          <div class='s-hourly-interval'>12.50 - 13.35</div>
        </div>
        <div class='s-head-hour'>
          <div class='s-number'>7</div>
          <div class='s-hourly-interval'>13.45 - 14.30</div>
        </div>
        <div class='s-head-hour'>
          <div class='s-number'>8</div>
          <div class='s-hourly-interval'>14.35 - 15.20</div>
        </div>
        <div class='s-head-hour'>
          <div class='s-number'>9</div>
          <div class='s-hourly-interval'>15.25 - 16.10</div>
        </div>
      </div>
      <div class='s-rows-container'>
        <div class='s-activities'>
          <div class='s-act-row'>
            @foreach($machines as $i => $machine)
            <div class='s-act-tab green' data-hours='7.45-8.45'>
              <div class='s-act-name'>{{ $machine->machine_name }}</div>
              <div class='s-wrapper'>
                <div class='s-act-teacher'>A. Rygulska</div>
                <div class='s-act-room'>105</div>
                <div class='s-act-group'>G1</div>
              </div>
            </div>
            @endforeach
          </div>
         
          
        </div>
        @for ($i = 0; $i < 10; $i++)
          <div class='s-row s-hour-row'>
          @foreach($machines as $machine)
            <div class='s-hour-wrapper s-cell' style="border: 1px solid;">
              <div class='s-half-hour'></div>
              <div class='s-half-hour'></div>
            </div>
          @endforeach
          </div>
        @endfor
          
{{--           <div class='s-row s-hour-row'>
            <div class='s-hour-wrapper s-cell' style="border: 1px solid;">
              <div class='s-half-hour'></div>
              <div class='s-half-hour'></div>
            </div>
          </div>
          <div class='s-row s-hour-row'>
            <div class='s-hour-wrapper s-cell' style="border: 1px solid;">
              <div class='s-half-hour'></div>
              <div class='s-half-hour'></div>
            </div>
          </div>
          <div class='s-row s-hour-row'>
            <div class='s-hour-wrapper s-cell' style="border: 1px solid;">
              <div class='s-half-hour'></div>
              <div class='s-half-hour'></div>
            </div>
          </div>
          <div class='s-row s-hour-row'>
            <div class='s-hour-wrapper s-cell' style="border: 1px solid;">
              <div class='s-half-hour'></div>
              <div class='s-half-hour'></div>
            </div>
          </div>
          <div class='s-row s-hour-row'>
            <div class='s-hour-wrapper s-cell' style="border: 1px solid;">
              <div class='s-half-hour'></div>
              <div class='s-half-hour'></div>
            </div>
          </div>
          <div class='s-row s-hour-row'>
            <div class='s-hour-wrapper s-cell' style="border: 1px solid;">
              <div class='s-half-hour'></div>
              <div class='s-half-hour'></div>
            </div>
          </div>
          <div class='s-row s-hour-row'>
            <div class='s-hour-wrapper s-cell' style="border: 1px solid;">
              <div class='s-half-hour'></div>
              <div class='s-half-hour'></div>
            </div>
          </div>
          <div class='s-row s-hour-row'>
            <div class='s-hour-wrapper s-cell' style="border: 1px solid;">
              <div class='s-half-hour'></div>
              <div class='s-half-hour'></div>
            </div>
          </div>
          <div class='s-row s-hour-row'>
            <div class='s-hour-wrapper s-cell' style="border: 1px solid;">
              <div class='s-half-hour'></div>
              <div class='s-half-hour'></div>
            </div>
          </div> --}}
      </div>
    </div>
  </div>
</div>


<style type="text/css">
  @import url("https://fonts.googleapis.com/css?family=Roboto");
* {
  box-sizing: border-box;
  font-size: 13px;
}

body {
  background: #f7f7f7;
  margin: 0;
}

.centered {
  font-family: "Roboto", sans-serif;
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  width: 90%;
  border: 1px solid;
}

.info {
  width: 100%;
  max-width: 895px;
  margin: 50px auto;
}

.code {
  background: #dfdfdf;
  padding: 10px;
  color: #9e9e9e;
  font-size: 12px;
  border-radius: 3px;
}

#schedule {
  width: 100%;
  margin: 0 auto;
  max-width: 895px;
  background: #fff;
  font-family: "Roboto", sans-serif;
  display: flex;
  position: asbolute;
}

.s-legend {
  display: flex;
  position: relative;
  flex-direction: column;
  width: 150px;
}
.s-legend .s-head-info {
  height: 100%;
}
.s-legend .s-name {
  margin: 20px auto;
}
.s-legend .s-week-day {
  height: 100%;
  position: relative;
  box-shadow: inset 0 1px 0 0.5px #f5f5f5;
}
.s-legend .s-week-day .s-day {
  width: 100%;
  margin: 20px auto;
}

.s-head-info {
  font-weight: 900;
  max-height: 50px;
  position: relative;
  width: 100%;
}

.s-wrapper {
  display: flex;
}

.s-block {
  display: block;
  text-align: center;
}

.s-week-day {
  min-width: 50px;
  max-width: 150px;
  width: 100%;
}

.s-cell {
  text-align: center;
}

.s-container {
  width: 100%;
  overflow: auto;
}
.s-container .s-head-info {
  align-items: center;
  display: flex;
}
.s-container .s-head-info .s-head-hour {
  width: 100%;
  height: 55px;
  max-height: 55px;
  padding: 12px 0;
  min-width: 76.5px;
  border-left: 1.2px solid #e9e9e9;
}
.s-container .s-head-info .s-head-hour .s-number {
  font-size: 15px;
}
.s-container .s-head-info .s-head-hour .s-hourly-interval {
  color: #cccccc;
  font-size: 8px;
}
.s-container .s-rows-container {
  display: flex;
  position: relative;
}
.s-container .s-rows-container .s-activities {
  position: absolute;
  width: 100%;
  height: 100%;
}
.s-container .s-rows-container .s-activities .s-act-row {
  position: relative;
  height: 55px;
}
.s-container .s-rows-container .s-activities .s-act-row .green {
  background: linear-gradient(to right, #00b09b, #96c93d);
}
.s-container .s-rows-container .s-activities .s-act-row .orange {
  background: linear-gradient(to right, #F2994A, #F2C94C);
}
.s-container .s-rows-container .s-activities .s-act-row .red {
  background: linear-gradient(to right, #CB356B, #BD3F32);
}
.s-container .s-rows-container .s-activities .s-act-row .yellow {
  background: linear-gradient(to right, #fffc00, #fffc00);
}
.s-container .s-rows-container .s-activities .s-act-row .blue {
  background: linear-gradient(to right, #36D1DC, #5B86E5);
}
.s-container .s-rows-container .s-activities .s-act-row .pink {
  background: linear-gradient(to right, #834d9b, #d04ed6);
}
.s-container .s-rows-container .s-activities .s-act-row .black {
  background: linear-gradient(to right, #000428, #004e92);
}
.s-container .s-rows-container .s-activities .s-act-row .s-act-tab {
  height: 45px;
  padding: 15px;
  border-radius: 3px;
  position: absolute;
  top: 5px;
}
.s-container .s-rows-container .s-activities .s-act-row .s-act-tab .s-wrapper {
  display: block;
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
}
.s-container .s-rows-container .s-activities .s-act-row .s-act-tab .s-act-teacher, .s-container .s-rows-container .s-activities .s-act-row .s-act-tab .s-act-room, .s-container .s-rows-container .s-activities .s-act-row .s-act-tab .s-act-group {
  font-size: 7px;
  color: white;
  position: absolute;
  margin: 5px;
}
.s-container .s-rows-container .s-activities .s-act-row .s-act-tab .s-act-group {
  bottom: 0;
  left: 0;
}
.s-container .s-rows-container .s-activities .s-act-row .s-act-tab .s-act-name {
  color: white;
}
.s-container .s-rows-container .s-activities .s-act-row .s-act-tab .s-act-teacher {
  left: 0;
  top: 0;
}
.s-container .s-rows-container .s-activities .s-act-row .s-act-tab .s-act-room {
  top: 0;
  right: 0;
}

.s-hour-row {
  height: 100%;
  width: 100%;
  min-width: 76.5px;
}
.s-hour-row .s-hour-wrapper {
  display: flex;
}
.s-hour-row .s-hour-wrapper .s-half-hour:nth-child(2) {
  border-left: 0.5px solid #e9e9e9;
}
.s-hour-row .s-hour-wrapper .s-half-hour {
  width: 50%;
  height: 55px;
  border-left: 1.2px solid #e9e9e9;
}

@media only screen and (max-width: 480px) {
  .s-legend {
    width: 100px;
  }
}

</style>


  <script src="{{ asset('js/core/ajax.min.js') }}"></script> 
  <script src="{{ asset('js/core/jquery.min.js') }}"></script>
<script type="text/javascript">
  var schedule = {
  initialize: function(){
    schedule.activities.set();
    
  }, 
  options: {
    schedule: '#schedule', 
    breaks: [5,5,10,20,10,20,10,5,5], // breaks duration
    s_breaks: [475,525, 575, 630, 695, 750, 815, 870, 920], // the time after which the break begins
    lesson_time: 45, // lesson duration (minutes)
    lessons: 9, // number of lessons per week
    start: function(){ // start at 7.10 
      return schedule.general.toMin(7,10)
    }, 
    end: function(){ // start at 16.10 
      return schedule.general.toMin(16,10)
    },
    h_width: $('.s-hour-row').width(), // get a width of hour div
    minToPx: function(){ // divide the box width by the duration of one lesson
      return schedule.options.h_width / schedule.options.lesson_time;
    },
  },
  general: {
    hoursRegEx: function(hours){
      var regex = /([0-9]{1,2}).([0-9]{1,2})-([0-9]{1,2}).([0-9]{1,2})/;
      if(regex.test(hours)){
        return true;
      }else{
        return false;
      }
      
    },
    toMin: function(hours, minutes, string){ 
      // change time format (10,45) to minutes (645)
      if(!string){
        return (hours * 60) + minutes;
      }
      
      if(string.length>0){
        // "7.10"
        var h = parseInt(string.split('.')[0]),
            m = parseInt(string.split('.')[1]);
        
        return schedule.general.toMin(h, m);
      }
    },
    getPosition: function(start, duration, end){
      var translateX = (start - schedule.options.start()) * schedule.options.minToPx(),
          width = duration * schedule.options.minToPx(),
          breaks = schedule.options.breaks,
          s_breaks = schedule.options.s_breaks;
      
      $.each(breaks, function(index, item) { 
        if( start < s_breaks[index] && duration > item && end > (s_breaks[index]+item) ){
          width -= item * schedule.options.minToPx();
        }
        if( start > s_breaks[index] && duration > item && end > (s_breaks[index]+item) ){
          translateX -= item * schedule.options.minToPx();
        }
      }); 
      
      return [translateX, width];
    }
  },
  activities: {
    find: function(week, hours, id){
      
    },
    delete: function(week, hours){
      /* week: 0-4 << remove all activities from a day 
         hours: "7.10-16.10" << remove all activities from a choosed hours
      */
      function finalize(message){
        if(confirm(message)){
          return true;
        }
      }
      
      if(week && !hours){
        if(finalize("Do you want to delete all activities on the selected day?")){
          $('.s-activities .s-act-row:eq('+ week +')').empty();
        }
      }
      
      if(!week && !hours){
        console.log('Error. You have to add variables like a week (0-4) or hours ("9.10-10.45")!')
      }
      // if day is not defined and hours has got a correct form
      if(!week && schedule.general.hoursRegEx(hours)){
        
          console.log('Week not defined and hours are defined!');
        
          $(schedule.options.schedule + ' .s-act-tab').each(function(i,v){
              var t = $(this), // get current tab
                  name = t.children('.s-act-name').text(), // get tab name
                  h = t.attr('data-hours').split('-'), // get tab hours
                  s = schedule.general.toMin(0,0, h[0]), // get tab start time (min)
                  e = schedule.general.toMin(0,0, h[1]), // get tab end time (min)
                  uh = hours.split('-'), // user choosed time
                  us = schedule.general.toMin(0,0, uh[0]), // user choosed start time (min)
                  ue = schedule.general.toMin(0,0, uh[1]); // user choosed end time (min)

              if(us<=s && ue>=e){
                 $(this).remove();
              }

            })
        
      }
    
      if(week && hours){
        // if week and hours is defined 
        console.log('Week is defined and hours are defined too!');
        
        $('#schedule .s-act-row:eq('+ week +') .s-act-tab').each(function(i,v){
              var t = $(this), // get current tab
                  name = t.children('.s-act-name').text(), // get tab name
                  h = t.attr('data-hours').split('-'), // get tab hours
                  s = schedule.general.toMin(0,0, h[0]), // get tab start time (min)
                  e = schedule.general.toMin(0,0, h[1]), // get tab end time (min)
                  uh = hours.split('-'), // user choosed time
                  us = schedule.general.toMin(0,0, uh[0]), // user choosed start time (min)
                  ue = schedule.general.toMin(0,0, uh[1]); // user choosed end time (min)

              if(us<=s && ue>=e){
                 $(this).remove();
              }

          })
        
        
      };
      
    },
    add: function(week, lesson, hours, classroom, group, teacher, color){
      /* EXAMPLES --> week: 0-4, lesson: "Math", hours: "9.45-12.50", 
      classroom: 145, group: "A", teacher: "A. Badurski", color: "orange" */
      var tab = "<div class='s-act-tab "+ color +"' data-hours='"+ hours +"'>\
            <div class='s-act-name'>"+ lesson +"</div>\
            <div class='s-wrapper'>\
              <div class='s-act-teacher'>"+ teacher +"</div>\
              <div class='s-act-room'>"+ classroom +"</div>\
              <div class='s-act-group'>"+ group +"</div>\
            </div>\
          </div>";
      $('.s-activities .s-act-row:eq('+ week +')').append(tab);
      schedule.activities.set();
    },
    set: function(){
      $(schedule.options.schedule + ' .s-act-tab').each(function(i){
        var hours = $(this).attr('data-hours').split("-"),
            start = /* HOURS */ parseInt(hours[0].split(".")[0]*60) 
            + /* MINUTES */ parseInt(hours[0].split(".")[1]),
            end = /* HOURS */ parseInt(hours[1].split(".")[0]*60) 
            + /* MINUTES */ parseInt(hours[1].split(".")[1]),
            duration = end - start,
            translateX = schedule.general.getPosition(start,duration,end)[0],
            width = schedule.general.getPosition(start,duration,end)[1];

        $(this)
          .attr({"data-start": start, "data-end": end})
          .css({"transform": "translateX("+translateX+"px)", "width": width+"px"});
      });
    }
  }
  
}

schedule.initialize();



</script>