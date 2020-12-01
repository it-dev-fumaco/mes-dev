@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'production_planning',
])

@section('content')
<script src="{{ asset('js/charts/Chart.min.js') }}"></script>
<script src="{{ asset('js/charts/utils.js') }}"></script>
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
                     <h2 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Production Planning</h2>
                  </td>
               </tr>
            </table>
         </div>
      </div>
   </div>
</div>
<div class="content" style="margin-top: -80px;">
   <div class="row">
      <div class="col-md-8">
         <div class="card">
            <div class="card-header">
               <h5 class="card-category"></h5>
               <h4 class="card-title">Operation: Fabrication<br>{{ Auth::user()->employee_name }} [{{ $user_details->designation }}]</h4>
            </div>
            <div class="card-body">
              <h5 class="card-title text-center">Production Planned Hour(s) - 8 Hour Shift</h5>
              <div style="width:100%;">
                <canvas id="canvas" height="100"></canvas>
              </div>
            </div>
         </div>
      </div>
      <div class="col-md-4">
      <div class="row">
      <div class="col-md-12">
      <div class="card" style="min-height: 250px;">
      <div class="card-header">
      <h4 class="card-title">Current Production Task</h4>
      </div>
      <div class="card-body">
      <div class="table-full-width table-responsive">
      <table class="table">
      <thead class="text-white" style="background-color: #34495e;">
      <th class="text-center"></th>
      <th class="text-center"><b>No. of Task(s)</b></th>
      <th class="text-center"><b>Total Qty</b></th>
      </thead>
      <tbody>
      <tr>
      <td class="text-left">Total Unassigned</td>
      <td class="td-actions text-center">
     
      </td>
      <td class="td-actions text-center">
     
      </td>
      </tr>
      <tr>
      <td class="text-left">Total Not Started</td>
      <td class="td-actions text-center">
  
      </td>
      <td class="td-actions text-center">
     
      </td>
      </tr>
      <tr>
      <td class="text-left">Total Work-In-Progress</td>
      <td class="td-actions text-center">
      
      </td>
      <td class="td-actions text-center">
     
      </td>
      </tr>
      <tr>
      <td class="text-left">Total Completed</td>
      <td class="td-actions text-center">
     
      </td>
      <td class="td-actions text-center">
   
      </td>
      </tr>
      </tbody>
      </table>
      </div>
      </div>
      </div>
      </div>
   </div>
</div>
<style>
  canvas {
    -moz-user-select: none;
    -webkit-user-select: none;
    -ms-user-select: none;
  }
  </style>
@endsection

@section('script')
<script>
    var lineChartData = {
      labels: ['7:00AM','7:15AM','7:30AM','7:45AM','8:00AM','8:15AM','8:30AM','8:45AM','9:00AM','9:15AM','9:30AM','9:45AM','10:00AM','10:15AM','10:30AM','10:45AM','11:00AM','11:15AM','11:30AM','11:45AM','12:00NN','12:15PM','12:30PM','12:45PM','1:00PM','1:15PM','1:30PM','1:45PM', '2:00PM','2:15PM','2:30PM','2:45PM','3:00PM','3:15PM','3:30PM','3:45PM','4:00PM'],
      datasets: [{
        label: 'Shearing',
        borderColor: '#2471A3',
        backgroundColor: '#2471A3',
        fill: false,
        data: [
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor()
        ],
        yAxisID: 'y-axis-2'
      },
      {
        label: 'Punching',
        borderColor: '#1E8449',
        backgroundColor: '#1E8449',
        fill: false,
        data: [
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor()
        ],
        yAxisID: 'y-axis-2'
      },
      {
        label: 'Spotwelding',
        borderColor: '#B7950B',
        backgroundColor: '#B7950B',
        fill: false,
        data: [
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor()
        ],
        yAxisID: 'y-axis-2'
      },
      {
        label: 'Bending',
        borderColor: '#BA4A00',
        backgroundColor: '#BA4A00',
        fill: false,
        data: [
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor()
        ],
        yAxisID: 'y-axis-2'
      },
      {
        label: 'Cutting',
        borderColor: '#8E44AD',
        backgroundColor: '#8E44AD',
        fill: false,
        data: [
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor()
        ],
        yAxisID: 'y-axis-2'
      },
      {
        label: 'Painting',
        borderColor: '#76D7C4',
        backgroundColor: '#76D7C4',
        fill: false,
        data: [
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor(),
          randomScalingFactor()
        ],
        yAxisID: 'y-axis-2'
      }]
    };

    window.onload = function() {
      var ctx = document.getElementById('canvas').getContext('2d');
      window.myLine = Chart.Line(ctx, {
        data: lineChartData,
        options: {
          responsive: true,
          hoverMode: 'index',
          stacked: false,
          title: {
            display: false,
            text: 'Chart.js Line Chart - Multi Axis'
          },
          scales: {
            yAxes: [{
              type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
              display: true,
              position: 'left',
              id: 'y-axis-1',
            }, {
              type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
              display: false,
              position: 'right',
              id: 'y-axis-2',

              // grid line settings
              gridLines: {
                drawOnChartArea: false, // only want the grid lines for one axis to show up
              },
            }],
          }
        }
      });
    };

  </script>

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

