@extends('layouts.app', [
    'namePage' => 'MES',
    'activePage' => 'operator_workstation_dashboard',
])

@section('content')
<div class="panel-header" style="padding-top: 30px; margin: 0; height: 200px;">
  <div class="header">
    <div class="row">
      <div class="col-md-12 text-white">
        <div class="col-10 mx-auto">
          <div class="row">
            <div class="col-4" style="display: flex; justify-content: center; align-items: center;">
                <div class="container text-center">
                    <span style="display: block; font-size: 16pt;">{{ date('M-d-Y') }}</span>
                    <span style="display: block; font-size: 12pt;">{{ date('l') }}</span>
                </div>
            </div>
            <div class="col-4" style="display: flex; justify-content: center; align-items: center;">
                <h2 id="current-time" class="title text-center" style=" margin: 2px auto;">--:--:-- --</h2>
            </div>
            <div class="col-4" style="display: flex; justify-content: center; align-items: center;">
                <h3 class="title" style="margin: 2px auto;">MES - Dashboard</h3>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="content" style="margin-top: -50px; min-height: 100px;">
  <div class="row text-center" id="header_datas">
    <div class="container-fluid workstation-status-div" data-status="Pending" data-title="Pending" style="margin-top: -30px;">
        @foreach ($workstations as $operation => $ws)
            <div class="card p-0">
                <div class="card-header" style="padding: 0; margin: 0 !important">
                <h4><b>{{ $operation }}</b></h4>
                </div>
                <div class="card-body" style="background-color: rgba(0,0,0,0); padding: 0 10px 10px 10px !important">
                    <div class="row">
                        @foreach ($ws as $w)
                            <div class="col-3 mt-2">
                                <a href="/operator/{{ $w->workstation_name }}">
                                    <div class="container p-3 border border-outline-secondary" style="box-shadow: 1px 1px 8px #888; color: #F96332; height: 78px;display: flex; justify-content: center; align-items: center; font-size: 18px;">
                                        <strong>
                                            {{ $w->workstation_name }}
                                        </strong>   
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
  </div>
</div>
<style type="text/css">
    @-webkit-keyframes blinker_break {
      from { background-color: #fa764b; }
      to { background-color: inherit; }
    }
    @-moz-keyframes blinker_break {
      from { background-color: #fa764b; }
      to { background-color: inherit; }
    }
    @-o-keyframes blinker_break {
      from { background-color: #fa764b; }
      to { background-color: inherit; }
    }
    @keyframes blinker_break {
      from { background-color: #fa764b; }
      to { background-color: inherit; }
    }
    
    .blink_break{
      text-decoration: blink;
      -webkit-animation-name: blinker;
      -webkit-animation-duration: 3s;
      -webkit-animation-iteration-count:infinite;
      -webkit-animation-timing-function:ease-in-out;
      -webkit-animation-direction: alternate;
    }

  .qc_passed{
    background-image: url("{{ asset('img/chk.png') }}");
    background-size: 28%;
    background-repeat: no-repeat;
    background-position: center; 
  }

  .qc_failed{
    background-image: url("{{ asset('img/x.png') }}");
    background-size: 20%;
    background-repeat: no-repeat;
    background-position: center; 
  }
  
  .tap_here {
    animation: bounce 1s linear infinite;
  }

  .active-process {
    background-color: #FFC107;
    color: #000000;
    animation: blinkingBackground 2.5s linear infinite;
  }

  @keyframes blinkingBackground{
    0%    { background-color: #ffffff;}
    25%   { background-color: #FFC107;}
    50%   { background-color: #ffffff;}
    75%   { background-color: #FFC107;}
    100%  { background-color: #ffffff;}
  }

  .dot {
    height: 12px;
    width: 12px;
    background-color: #bbb;
    border-radius: 50%;
    display: inline-block;
  }

  .text-blink {color: orange;
    animation: blinker 1s linear infinite;
  }

  @keyframes blinker {  
    50% { opacity: 0; }
  }

  .numpad-div .row1{
    -webkit-user-select: none; /* Chrome/Safari */        
    -moz-user-select: none; /* Firefox */
    -ms-user-select: none; /* IE10+ */
    /* Not implemented yet */
    -o-user-select: none;
    user-select: none;   
  }

  .numpad{
    display: inline-block;
    border: 1px solid #333;
    border-radius: 5px;
    text-align: center;
    width: 27%;
    height: 27%;
    line-height: 60px;
    margin: 3px;
    font-size: 15pt;
    color: inherit;
    background: rgba(255, 255, 255, 0.7);
    transition: all 0.3s ease-in-out;
  }

  .numpad:active,
  .numpad:hover {
    cursor: pointer ;
    box-shadow: inset 0 0 2px #000000;
  }
  .breadcrumb-c {
    font-size: 8pt;
    font-weight: bold;
    padding: 0;
    background: transparent;
    list-style: none;
    overflow: hidden;
    margin-top: 3px;
    margin-bottom: 3px;
    width: 100%;
    border-radius: 4px;
  }

  .breadcrumb-c>li {
    display: table-cell;
    vertical-align: top;
    width: 0.8%;
  }

  .breadcrumb-c>li+li:before {
    padding: 0;
  }

  .breadcrumb-c li a {
    color: white;
    text-decoration: none;
    padding: 10px 0 10px 5px;
    position: relative;
    display: inline-block;
    width: calc( 100% - 10px );
    background-color: hsla(0, 0%, 83%, 1);
    text-align: center;
    text-transform: capitalize;
  }

  .breadcrumb-c li.completed a {
    background: brown;
    background: hsla(153, 57%, 51%, 1);
  }

  .breadcrumb-c li.completed a:after {
    border-left: 30px solid hsla(153, 57%, 51%, 1);
  }

  .breadcrumb-c li.active a {
    background: #ffc107;
  }

  .breadcrumb-c li.active a:after {
    border-left: 30px solid #ffc107;
  }

  .breadcrumb-c li:first-child a {
    padding-left: 1px;
  }

  .breadcrumb-c li:last-of-type a {
    width: calc( 100% - 38px );
  }

  .breadcrumb-c li a:before {
    content: " ";
    display: block;
    width: 0;
    height: 0;
    border-top: 50px solid transparent;
    border-bottom: 50px solid transparent;
    border-left: 30px solid white;
    position: absolute;
    top: 50%;
    margin-top: -50px;
    margin-left: 1px;
    left: 100%;
    z-index: 1;
  }

  .breadcrumb-c li a:after {
    content: " ";
    display: block;
    width: 0;
    height: 0;
    border-top: 50px solid transparent;
    border-bottom: 50px solid transparent;
    border-left: 30px solid hsla(0, 0%, 83%, 1);
    position: absolute;
    top: 50%;
    margin-top: -50px;
    left: 100%;
    z-index: 2;
  }

  .truncate {
    white-space: nowrap;
    /*overflow: hidden;*/
    text-overflow: ellipsis;
  }

  .scrolltbody tbody {
    display:block;
    height:300px;
    overflow:auto;
  }
  .scrolltbody thead, .scrolltbody tbody tr {
    display:table;
    width:100%;
    table-layout:fixed;
  }
  .scrolltbody thead {
    width: calc(100%)
  }
</style>
@endsection

@section('script')
<link rel="stylesheet" href="{{ asset('/css/datepicker/landscape.css') }}" type="text/css" media="print" />
<script>
    $(document).ready(function (){
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