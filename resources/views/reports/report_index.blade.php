@extends('layouts.user_app', [
  'namePage' => 'Fabrication',
  'activePage' => 'production_schedule',
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
 <br>
 <div class="content" style="margin-top: -100px;">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-body" style="min-height:750px;">
                   <div class="row" style="margin-top: 15px;">
                      
                      <div class="col-md-6"  style="margin-top:30px;">
                        <table style= "width:100%;">
                            <thead  style="">
                                <tr style="">
                                    <th><span style="font-size:13.5pt;padding-left:30px;">Fabrication</span> </th>
                                </tr>
                            </thead>
                            <tbody style=" padding-left:30px; text-align:left;">
                                <tr>
                                    <td><a href="/fabrication_report" class="hover-class"><span style="padding-left:30px;line-height:25px;"> Daily Fabrication Output Report </span></a></td>
                                </tr>
                                <tr>
                                    <td><a href="/fabrication_report#!tab1" class="hover-class"><span style="padding-left:30px;line-height:25px;"> Fabrication Operator Log Report  </span></a></td>
                                </tr>
                                <tr>
                                    <td><a href="/fabrication_report" class="hover-class"><span style="padding-left:30px;line-height:25px;">  Operator Load Utilization  </span></a></td>
                                </tr>
                               {{-- <tr>
                                    <td><a href="/production_schedule_report" class="hover-class"><span style="padding-left:30px;line-height:25px;">  Production Schedule Report  </span></a></td>
                                </tr> --}}
                            </tbody>
                        </table>
                      </div>
                      <div class="col-md-6"  style="margin-top:30px;">
                        <table style= "width:100%;">
                            <thead  style="">
                                <tr style="">
                                    <th><span style="font-size:13.5pt;padding-left:30px;">Painting</span> </th>
                                </tr>
                            </thead>
                            <tbody style=" padding-left:30px; text-align:left;">
                                {{--<tr>
                                    <td><a href="/#" class="hover-class"><span style="padding-left:30px;line-height:25px;"> Daily Painting Output Report </span></a></td>
                                </tr>--}}
                                <tr>
                                    <td><a href="/painting_report" class="hover-class"><span style="padding-left:30px;line-height:25px;"> Painting Chemical Record  </span></a></td>
                                </tr>
                                <tr>
                                    <td><a href="/painting_report" class="hover-class"><span style="padding-left:30px;line-height:25px;"> Water Discharged Monitoring </span></a></td>

                                </tr>
                                
                            </tbody>
                        </table>
                      </div>
                      <div class="col-md-6"  style="margin-top:70px;">
                        <table style= "width:100%;">
                            <thead  style="">
                                <tr style="">
                                    <th><span style="font-size:13.5pt;padding-left:30px;">Wiring and Assembly</span> </th>
                                </tr>
                            </thead>
                            <tbody style=" padding-left:30px; text-align:left;">
                                <tr>
                                    <td><a href="/assembly_report" class="hover-class"><span style="padding-left:30px;line-height:25px;"> Daily Assembly Output Report </span></a></td>
                                </tr>
                                <tr>
                                    <td><a href="/assembly_report" class="hover-class"><span style="padding-left:30px;line-height:25px;">  Operator Load Utilization  </span></a></td>
                                </tr>
                                
                            </tbody>
                        </table>
                      </div>
                      <div class="col-md-6" style="margin-top:70px;">
                        <table style= "width:100%;">
                            <thead  style="">
                                <tr style="">
                                    <th><span style="font-size:13.5pt;padding-left:30px;">Quality Assurance</span> </th>
                                </tr>
                            </thead>
                            <tbody style=" padding-left:30px; text-align:left;">
                                <tr>
                                    <td><a href="/qa_report" class="hover-class"><span style="padding-left:30px;line-height:25px;"> Inspection log Report </span></a></td>
                                </tr>
                            </tbody>
                        </table>
                      </div>
                   </div>
                </div>
            </div>
        </div>
    </div>
 </div>

<div id="active-tab"></div>

@endsection

@section('script')
<script type="text/javascript" src="{{ asset('js/daterange/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/daterange/daterangepicker.css') }}" />
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />
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