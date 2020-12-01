@extends('production_floor.app', [
    'namePage' => 'MES',
    'activePage' => 'main_dashboard',
])

@section('content')
<div class="panel-header" style="height: 400px;">
  <div class="header" style="margin-top: -100px;">
    <div class="row">
      <div class="col-md-6 text-white">
        <h1 class="title m-4" style="text-transform: uppercase; letter-spacing: 5px; font-size: 40pt;">Production Live Status</h1>
        <h5 class="ml-4 text-white" style="font-size: 15pt; letter-spacing: 6px; margin-top: -20px;">Manufacturing Execution System</h5>
      </div>
      <div class="col-md-6">
        <div class="row mt-4">
          <div class="col-md-6 text-center">
            <div id="date" style="padding-left: 100px;"></div>
          </div>
          <div class="col-md-6 pl-5" id="clock">--:--:-- --</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="content" style="margin-top: -280px; padding-bottom: 0;">
  <div class="row">
    <div class="col-md-8">
      <div id="workstation-div"></div>
    </div>
    <div class="col-md-4">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header ready text-center" style="height: 80px;" id="ready-feedback-bg">
              <h5 class="title text-center" style="font-size: 25pt;">Ready for Feedback</h5>
            </div>
            <div class="card-body p-0 pb-3 text-center" style="min-height: 330px; background-color: #263238;">
              <div class="row">
                <div class="col-md-12 pt-1">
                  <span class="title text-white" style="font-size: 100pt;" id="production-for-feedback-count">0</span>
                  <span class="title text-white" style="font-size: 25pt; display: block;" id="total-qty-for-feedback">Qty: 0</span>
                  <span class="title text-white mt-2" style="font-size: 20pt; display: block;">Production Order(s)</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12">
          <div class="card">
            <div class="card-header text-center" style="height: 80px;">
              <h5 class="title" style="font-size: 22pt; margin-top: -5px;">Weekly Output Chart</h5>
              <div style="margin-top: -18px;"><span style="font-size: 14pt;">Past 7 Days</span></div>
            </div>
            <div class="card-body p-0 pb-3" style="min-height: 330px; background-color: #263238;">
              <div class="chart-area pt-4" style="min-height: 300px;">
                <canvas id="bigDashboardChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12" style="background-color: #fff; margin: 0; padding: 0;">
      <div class="row">
        <div class="col-md-8" style="padding-left: 15px;">

<div class="font-weight-bold" id="activity-logs-div"><p class="marquee">
  
</p></div>

        </div>
        <div class="col-md-4">
          <div style="height: 65px; margin: 10px; background-color:#ccd1d1;">
            <div class="carousel">
              <div class="change_outer">
                <div class="change_inner text-center font-weight-bold" id="breakdown-div"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.marquee {
  width: 100%;
  margin: 10px auto;
  overflow: hidden;
  white-space: nowrap;
  padding: 0;
}

.marquee span {
  display: inline-block;
  font-size: 25pt;
  position: relative;
  /*margin-top: -30px;*/
  margin-bottom: 10px;
  left: 100%;
  animation: marquee 40s linear infinite;
}

.marquee:hover span {
  animation-play-state: paused;
}

.marquee span:nth-child(1) {
  animation-delay: 0s;
}

@keyframes marquee {
  0%   { left: 100%; }
  100% { left: -100%; }
}


  
  .carousel {
    position: relative;
    width: 100%;
    text-align: center;
    font-size: 30px;
    line-height: 65px;
    height: 65px;
  }

  .carousel .change_outer {
    text-align: left;
    height: 65px;
    overflow: hidden;
  }
  
  .carousel .change_outer .change_inner {
    position: relative;
    -webkit-animation: rotate 15s ease-in-out infinite;
            animation: rotate 15s ease-in-out infinite;
  }
  
  .carousel .change_outer .element {
    display: block;
  }
  
  @-webkit-keyframes rotate {
    10%, 15%, 100% {
      -webkit-transform: translateY(0);
              transform: translateY(0);
    }
    25%,36% {
      -webkit-transform: translateY(-65px);
              transform: translateY(-65px);
    }
    46%,57% {
      -webkit-transform: translateY(-130px);
              transform: translateY(-130px);
    }
    67%,78% {
      -webkit-transform: translateY(-195px);
              transform: translateY(-195px);
    }
    88%, 99% {
      -webkit-transform: translateY(-260px);
              transform: translateY(-260px);
    }
  }
  
  @keyframes rotate {
    10%, 15%, 100% {
      -webkit-transform: translateY(0);
              transform: translateY(0);
    }
    25%,36% {
      -webkit-transform: translateY(-65px);
              transform: translateY(-65px);
    }
    46%,57% {
      -webkit-transform: translateY(-130px);
              transform: translateY(-130px);
    }
    67%,78% {
      -webkit-transform: translateY(-195px);
              transform: translateY(-195px);
    }
    88%, 99% {
      -webkit-transform: translateY(-260px);
              transform: translateY(-260px);
    }
  }

</style>

<style>
  #clock{
      font-family: sans-serif;
      font-size:65px;
      display: inline-block;
      text-shadow:0px 0px 1px #fff;
      color:#fff;
  }

  #clock span {
      color: #f2f3f4 ;
      text-shadow:0px 0px 1px #333;
      font-size:45px;
      position:relative;
      top:-5px;
      left:10px;
  }

  #date span {
    color: #f2f3f4 ;
    text-shadow:0px 0px 1px #333;
    font-size:20pt;
    display:block;
    margin-top:-10px;
    left:10px;
  }

  #date {
    display: inline-block;
      letter-spacing:3px;
      font-size: 30pt;
      font-family:arial,sans-serif;
      color:#fff;
  }

  .completed{
    background-color: #4caf50 !important;
  }

  .in-process{
    background-color: #ffb300 !important;
  }

  .not-started{
    background-color:  #ff7043 !important;
  }

  .idle{
    background-color:   #707b7c  !important;
  }

  .ready {
    background-color: #4caf50;
    color: #000000;
    animation: blinkingBackground 2.5s linear infinite;
  }

  @keyframes blinkingBackground{
    0%    { background-color: #ffffff;}
    25%   { background-color: #4caf50;}
    50%   { background-color: #ffffff;}
    75%   { background-color: #4caf50;}
    100%  { background-color: #ffffff;}
  }
</style>
@endsection

@section('script')
<script>
  $(document).ready(function(){
    get_workstations();
    get_ready_for_feedback();
    load_chart();
    get_machine_breakdown();
    activity_logs();
    setInterval(load_chart, 10000);
    setInterval(get_workstations, 10000);
    setInterval(get_ready_for_feedback, 5000);
    setInterval(activity_logs, 320000);
    setInterval(get_machine_breakdown, 15000);
    function get_workstations(){
      $.ajax({
        url:"/get_workstation_dashboard_content",
        type:"GET",
        success:function(data){
          $('#workstation-div').html(data);
        }
      });  
    }

    function activity_logs(){
      $.ajax({
        url:"/activity_logs",
        type:"GET",
        success:function(data){
          $('#activity-logs-div .marquee').html(data);
        }
      });  
    }

    function get_machine_breakdown(){
      $.ajax({
        url:"/get_machine_breakdown",
        type:"GET",
        success:function(data){
          var l = '';
          if(data.length > 0){
            $.each(data, function(i ,d){
              l += '<div class="element" style="color: #a93226;">' + d.machine_code + ' ' + d.category + '</div>';
            });
          }else{
            l += '<div class="element" style="color: #27ae60;"><b>No Downtime Reported</b></div>';
          }
          
          $('#breakdown-div').append(l);
        }
      });  
    }

    function get_ready_for_feedback(){
      $.ajax({
        url:"/get_ready_for_feedback",
        type:"GET",
        success:function(data){
          if(parseInt(data.production_orders_count) > 0){
            $('#ready-feedback-bg').addClass('ready');
          }else{
            $('#ready-feedback-bg').removeClass('ready');
          }
          console.log(data.total_feedback);
          $('#production-for-feedback-count').text(data.production_orders_count);
          $('#total-qty-for-feedback').text('Qty: ' + data.total_qty);
        }
      });  
    }
    
    function load_chart(){
      chartColor = "#FFFFFF";
      var ctx = document.getElementById('bigDashboardChart').getContext("2d");

      var gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
      gradientStroke.addColorStop(0, '#80b6f4');
      gradientStroke.addColorStop(1, chartColor);

      var gradientFill = ctx.createLinearGradient(0, 200, 0, 50);
      gradientFill.addColorStop(0, "rgba(128, 182, 244, 0)");
      gradientFill.addColorStop(1, "rgba(255, 255, 255, 0.24)");

      $.ajax({
        url:"/get_total_output",
        type:"GET",
        success:function(data){
          var labels = [];
          var outputs = [];
          for(var i in data) {
            labels.push(data[i].transaction_date);
            outputs.push(data[i].output);
          }

          var myChart = new Chart(ctx, {
            type: 'line',
            data: {
              labels: labels,
              datasets: [{
                label: "Data",
                borderColor: chartColor,
                pointBorderColor: chartColor,
                pointBackgroundColor: "#1e3d60",
                pointHoverBackgroundColor: "#1e3d60",
                pointHoverBorderColor: chartColor,
                pointBorderWidth: 1,
                pointHoverRadius: 7,
                pointHoverBorderWidth: 2,
                pointRadius: 5,
                fill: true,
                backgroundColor: gradientFill,
                borderWidth: 2,
                data: outputs,
                lineTension: 0
              }]
            },
            options: {
            	animation: false,
              layout: {
                padding: {
                  left: 20,
                  right: 20,
                  top: 0,
                  bottom: 0
                }
              },
              maintainAspectRatio: false,
              tooltips: {
                backgroundColor: '#fff',
                titleFontColor: '#333',
                bodyFontColor: '#666',
                bodySpacing: 4,
                xPadding: 12,
                mode: "nearest",
                intersect: 0,
                position: "nearest"
              },
              legend: {
                position: "bottom",
                fillStyle: "#FFF",
                display: false,
              },
              scales: {
                yAxes: [{
                  ticks: {
                    fontColor: "rgba(255,255,255,0.4)",
                    fontStyle: "bold",
                    beginAtZero: true,
                    maxTicksLimit: 5,
                    padding: 10,
                    fontSize: 20
                  },
                  gridLines: {
                    drawTicks: true,
                    drawBorder: false,
                    display: true,
                    color: "rgba(255,255,255,0.1)",
                    zeroLineColor: "transparent"
                  }
                }],
                xAxes: [{
                  gridLines: {
                    zeroLineColor: "transparent",
                    display: false,
                  },
                  ticks: {
                    padding: 10,
                    fontColor: "rgba(255,255,255,0.4)",
                    fontStyle: "bold",
                    fontSize: 18
                  }
                }]
              }
            }
          });
        }
      });  
    }
   
    startTime();
    function startTime() {
      var today = new Date();
      var hr = today.getHours();
      var min = today.getMinutes();
      var sec = today.getSeconds();
      ap = (hr < 12) ? "<span>AM</span>" : "<span>PM</span>";
      hr = (hr == 0) ? 12 : hr;
      hr = (hr > 12) ? hr - 12 : hr;
      //Add a zero in front of numbers<10
      hr = checkTime(hr);
      min = checkTime(min);
      sec = checkTime(sec);
      document.getElementById("clock").innerHTML = hr + ":" + min + ":" + sec + " " + ap;
      
      var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
      var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
      var curWeekDay = days[today.getDay()];
      var curDay = today.getDate();
      var curMonth = months[today.getMonth()];
      var curYear = today.getFullYear();
      curWeekDay = "<span>" + curWeekDay + "</span>";
      var date = curDay+" "+curMonth+" "+curYear + "" + curWeekDay;
      document.getElementById("date").innerHTML = date;
      
      var time = setTimeout(function(){ startTime() }, 500);
  }
  function checkTime(i) {
      if (i < 10) {
          i = "0" + i;
      }
      return i;
  }
  });
</script>
@endsection