@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'maintenance_request_page',
])

@section('content')
<div class="panel-header" style="margin-top: -50px;">
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
                     <h2 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Maintenance Request(s)</h2>
                  </td>
               </tr>
            </table>
         </div>
      </div>
   </div>
</div>
<div class="content" style="margin-top: -110px;">
    <div class="row p-0">
        <div class="col-9 p-0">
            <div class="col-12 p-0" style="min-height:440px;">
                <div class="panel panel-default p-0">
                  <div class="panel-body p-0 panel-body">
                    <div class="col-sm-12 ticket-status-widget pt-" role="tabpanel" aria-expanded="true" aria-hidden="false">
                      <div class="ui-tab-container ui-tab-default">
                        <div justified="true" class="ui-tab">
                            @php
                                $status_arr = array('Pending', 'On Hold', 'In Process', 'Done');;
                            @endphp
                            <ul class="nav nav-tabs nav-justified">
                                <li class="tab custom-nav-link col-2" heading="Justified" style="background-color: #808495 !important">
                                    <a data-toggle="tab" onclick="changeTab('fabrication', 1)" href="#tab-fabrication">
                                        <span class="tab-number" id="fabrication-count">{{ $fabrication }}</span> 
                                        <span class="tab-title">&nbsp;Fabrication&nbsp;</span> 
                                    </a>
                                </li>
                                
                                <li class="tab custom-nav-link col-2" heading="Justified" style="background-color: #2196E3 !important">
                                    <a data-toggle="tab" onclick="changeTab('painting', 2)" href="#tab-painting">
                                        <span class="tab-number" id="painting-count">{{ $painting }}</span> 
                                        <span class="tab-title">&nbsp;Painting&nbsp;</span> 
                                    </a>
                                </li>

                                <li class="tab custom-nav-link col-2" heading="Justified" style="background-color: #8BC753 !important">
                                    <a data-toggle="tab" onclick="changeTab('wiring', 3)" href="#tab-wiring">
                                        <span class="tab-number" id="wiring-count">{{ $wiring }}</span> 
                                        <span class="tab-title">&nbsp;Wiring and Assembly&nbsp;</span> 
                                    </a>
                                </li>
                            </ul>
                
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab-fabrication">
                                    <div class="tab-heading tab-heading--gray">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-8">
                                                    <input class="d-none" type="text" value="All" id="fabrication-current-status">
                                                    <div class="row">
                                                        @foreach ($status_arr as $status)
                                                        <label class="PillList-item">
                                                            <input type="checkbox" class="fabrication-checkbox" value="{{ $status }}">
                                                            <span class="PillList-label">{{ $status }}
                                                            </span>
                                                        </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group mr-2">
                                                        <input type="text" data-div="fabrication" data-op="1" class="form-control bg-white fabrication-search-filter maintenance-search" placeholder="Search">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12" id="fabrication-div" style="min-height:500px; border-top: 1px solid #D3D7DA;"></div>
                                    </div>
                                </div>
                                
                                <div class="tab-pane" id="tab-painting">
                                    <div class="tab-heading tab-heading--blue">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-8">
                                                    <input class="d-none" type="text" value="All" id="painting-current-status">
                                                    <div class="row">
                                                        @foreach ($status_arr as $status)
                                                        <label class="PillList-item">
                                                            <input type="checkbox" class="painting-checkbox" value="{{ $status }}">
                                                            <span class="PillList-label">{{ $status }}
                                                            </span>
                                                        </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group mr-2">
                                                        <input type="text" data-div="painting" data-op="2" class="form-control bg-white painting-search-filter maintenance-search" placeholder="Search">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12" id="painting-div" style="min-height:500px; border-top: 1px solid #D3D7DA;"></div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="tab-wiring">
                                    <div class="tab-heading tab-heading--green">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-8">
                                                    <input class="d-none" type="text" value="All" id="wiring-current-status">
                                                    <div class="row">
                                                        @foreach ($status_arr as $status)
                                                            <label class="PillList-item">
                                                                <input type="checkbox" class="wiring-checkbox" value="{{ $status }}">
                                                                <span class="PillList-label">{{ $status }}
                                                                </span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group mr-2">
                                                        <input type="text" data-div="wiring" data-op="3" class="form-control bg-white wiring-search-filter maintenance-search" placeholder="Search">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12" id="wiring-div" style="min-height:500px; border-top: 1px solid #D3D7DA;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
        <div class="col-3 p-0" style="background-color: #F7F7F9; margin-top: 56px">
          <div class="container p-3">
            <h5>Machines for Maintenance</h5>
          </div>
          <div class="container" style="height: 700px; overflow-y: scroll; overflow-x: hidden">
            @foreach ($machine_arr as $machine)
              <div class="card m-1">
                <div class="card-body row">
                  <div class="col-2 p-1">
                    <center>
                      <img src="{{ asset($machine['image']) }}" alt="" class="w-100">
                    </center>
                  </div>
                  <div class="col-10">
                    <span class="card-title" style="font-weight: bold">
                      {{ $machine['machine_id'] }} <span class="badge badge-danger">{{ $machine['pending_breakdowns'] }}</span><br/>
                      {{ $machine['machine_name'] }}
                    </span>
                    <p class="card-text">{{ $machine['total_breakdowns'] }} total breakdowns</p>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
    </div>
</div>

@endsection
@section('style')
<style>
    .ui-autocomplete {
      position: absolute;
      z-index: 2150000000 !important;
      cursor: default;
      border: 2px solid #ccc;
      padding: 5px 0;
      border-radius: 2px;
    }
  
    .custom-nav-link{
      padding: 5px;
      /* width: 9%; */
    }
  
    .custom-nav-link a{
      text-decoration: none;
    }
    
    .ui-tab-container.ui-tab-default .nav-tabs {
      border: 0;
    }
    
    .tab-heading {
      width: 100%;
      padding: 1em .5em;
    }
    .tab-heading h4 {
      margin: 0;
      padding: 0;
    }
    .tab-heading--blue {
      background-color: #2196E3;
      color: #FFF;
    }
    .tab-heading--orange {
      background-color: #EA9034;
      color: #FFF;
    }
    .tab-heading--reddish {
      background-color: #E86B46;
      color: #FFF;
    }
    .tab-heading--teal {
      background-color: #22D3CC;
      color: #FFF;
    }
    .tab-heading--green {
      background-color: #8BC753;
      color: #FFF;
    }
    .tab-heading--gray {
      background-color: #808495;
      color: #FFF;
    }
    .tab-heading--ltgray {
      background-color: #F3F3F3;
      color: #242424;
    }
    
    .ui-tab-container .nav-tabs > li.active > a,
    .ui-tab-container .nav-tabs > li > a {
      background: transparent;
      border: 0;
      border-width: 0;
      outline: 0;
    }
    .ui-tab-container .nav-tabs > li.active > a:hover, .ui-tab-container .nav-tabs > li.active > a:focus,
    .ui-tab-container .nav-tabs > li > a:hover,
    .ui-tab-container .nav-tabs > li > a:focus {
      background-color: transparent;
      border: 0;
      border-width: 0;
      outline: 0;
    }
        
    li.tab .tab-number {
      color: #FFF;
      font-weight: 800;
      font-size: 1.2em;
      display: block;
      text-align: center;
      margin-bottom: .25em;
    }
    li.tab .tab-title {
      color: #FFF;
      font-size: .8em;
      display: block;
      text-align: center;
      text-transform: uppercase;
    }
    li.tab.in-progress-tab {
      background-color: #EA9034;
    }
    li.tab.task-queue-tab {
      background-color: #2196E3;
    }
    li.tab.bug-queue-tab {
      background-color: #E86B46;
      color: #FFF;
    }
    li.tab.awaiting-feedback-tab {
      background-color: #22D3CC;
      color: #FFF;
    }
    li.tab.completed-tab {
      background-color: #8BC753;
      color: #FFF;
    }
    li.tab.next-deploy-tab {
      background-color: #808495;
      color: #FFF;
    }
    li.tab.search-tab {
      background-color: #F3F3F3;
    }
    li.tab.search-tab .tab-number {
      color: #242424;
    }
    li.tab.search-tab .tab-title {
      color: #242424;
    }
    
    .ticket-status-widget .tab-content {
      background: #FFF;
      padding: 0;
    }
    .PillList-item {
  cursor: pointer;
  display: inline-block;
  float: left;
  font-size: 14px;
  font-weight: normal;
  line-height: 20px;
  margin: 0 12px 12px 0;
  text-transform: capitalize;
}

.PillList-item input[type="checkbox"] {
  display: none;
}
.PillList-item input[type="checkbox"]:checked + .PillList-label {
  background-color: #F96332;
  border: 1px solid #F96332;
  color: #fff;
  padding-right: 16px;
  padding-left: 16px;
}
.PillList-label {
  border: 1px solid #FFF;
  border-radius: 20px;
  color: #FFF;
  display: block;
  padding: 7px 28px;
  text-decoration: none;
}
.PillList-item
  input[type="checkbox"]:checked
  + .PillList-label
  .Icon--checkLight {
  display: none;
}
.PillList-item input[type="checkbox"]:checked + .PillList-label .Icon--addLight,
.PillList-label .Icon--checkLight,
.PillList-children {
  display: none;
}
.PillList-label .Icon {
  width: 12px;
  height: 12px;
  margin: 0 0 0 12px;
}
.Icon--smallest {
  font-size: 12px;
  line-height: 12px;
}
.Icon {
  background: transparent;
  display: inline-block;
  font-style: normal;
  vertical-align: baseline;
  position: relative;
}
  </style>
@endsection
@section('script')
<script src="{{ asset('/js/jquery.rfid.js') }}"></script>
<script src="https://kit.fontawesome.com/ec0415ab92.js"></script>
<script>
    $(document).on('click', '.machine-details', function(e){
      status_check($(this).data('breakdown'));
    });

    // tabs
    function changeTab(operation, op){
      get_maintenance_request_list(op, $('#'+operation+'-current-status').val(), $('.'+operation+'-search-filter').val(), '#'+operation+'-div', 1);
    }

    function status_check(machine_breakdown_id){
      if($('#'+machine_breakdown_id+'-status').val() == 'On Hold'){
          $('#'+machine_breakdown_id+'-hold-container').slideDown();
          $('#'+machine_breakdown_id+'-done-container').slideUp();
          $('#'+machine_breakdown_id+'-hold-reason').prop('required', true);
          $('#'+machine_breakdown_id+'-work-done').prop('required', false);
          $('#'+machine_breakdown_id+'-findings').prop('required', false);
      }else if($('#'+machine_breakdown_id+'-status').val() == 'Done'){
          $('#'+machine_breakdown_id+'-done-container').slideDown();
          $('#'+machine_breakdown_id+'-hold-container').slideUp();
          $('#'+machine_breakdown_id+'-hold-reason').prop('required', false);
          $('#'+machine_breakdown_id+'-work-done').prop('required', true);
          $('#'+machine_breakdown_id+'-findings').prop('required', true);
      }else{
          $('#'+machine_breakdown_id+'-done-container').slideUp();
          $('#'+machine_breakdown_id+'-hold-container').slideUp();
          $('#'+machine_breakdown_id+'-hold-reason').prop('required', false);
          $('#'+machine_breakdown_id+'-work-done').prop('required', false);
          $('#'+machine_breakdown_id+'-findings').prop('required', false);
      }
    }

    // search
    $(".maintenance-search").keyup(function(){
        var operation = $(this).data('div');
        var op = parseInt($(this).data('op'));

        var status = $('#'+operation+'-current-status').val();
        var query = $('.'+operation+'-search-filter').val();
        var div = '#'+operation+'-div';
        get_maintenance_request_list(op, status, query, div, 1);
    });

    // pill tabs
    $(".fabrication-checkbox").click(function(){
        if($(this).prop('checked') == true){
            status += $(this).val() + ',';
        }else if($(this).prop('checked') == false){
            status = status.replace($(this).val() + ',', '');
        }

        if(status == ''){
            $('#fabrication-current-status').val('All');
        }else{
            $('#fabrication-current-status').val(status);
        }

        query = $('.fabrication-search-filter').val();
        get_maintenance_request_list(1, $('#fabrication-current-status').val(), query, '#fabrication-div', 1);
    });

    $(".painting-checkbox").click(function(){
        if($(this).prop('checked') == true){
            status += $(this).val() + ',';
        }else if($(this).prop('checked') == false){
            status = status.replace($(this).val() + ',', '');
        }

        if(status == ''){
            $('#painting-current-status').val('All');
        }else{
            $('#painting-current-status').val(status);
        }

        query = $('.painting-search-filter').val();
        get_maintenance_request_list(2, $('#painting-current-status').val(), query, '#painting-div', 1);
    });

    $(".wiring-checkbox").click(function(){
        if($(this).prop('checked') == true){
            status += $(this).val() + ',';
        }else if($(this).prop('checked') == false){
            status = status.replace($(this).val() + ',', '');
        }

        if(status == ''){
            $('#wiring-current-status').val('All');
        }else{
            $('#wiring-current-status').val(status);
        }

        query = $('.wiring-search-filter').val();
        get_maintenance_request_list(3, $('#wiring-current-status').val(), query,'#wiring-div', 1);
    });

    // pagination links
    $(document).on('click', '.custom-fabrication-pagination a', function(event){
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        var query = $('.fabrication-search-filter').val();
        var status = $('#fabrication-current-status').val() ? $('#fabrication-current-status').val() : 'All';
        get_maintenance_request_list(1, status, query,'#fabrication-div', page);
    });

    $(document).on('click', '.custom-painting-pagination a', function(event){
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        var query = $('.painting-search-filter').val();
        var status = $('#painting-current-status').val() ? $('#painting-current-status').val() : 'All';
        get_maintenance_request_list(1, status, query,'#painting-div', page);
    });

    $(document).on('click', '.custom-wiring-pagination a', function(event){
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        var query = $('.wiring-search-filter').val();
        var status = $('#wiring-current-status').val() ? $('#wiring-current-status').val() : 'All';
        get_maintenance_request_list(1, status, query,'#wiring-div', page);
    });

    // main function
    function get_maintenance_request_list(operation, status, query, div, page){
        if(parseInt(operation) == 1){
            var op = '#fabrication';
        }else if(parseInt(operation) == 2){
            var op = '#painting';
        }else if(parseInt(operation) == 3){
            var op = '#wiring';
        }
        $.ajax({
            url: "/maintenance_request_list/?page="+page,
            type:"GET",
            data: {
                search_string: query,
                operation: operation,
                status: status
            },
            success:function(data){
                $(div).html(data);
                $(op+'-count').text($(op+'-total').val());
            }
        });
    }

    $(document).ready(function(){
        get_maintenance_request_list(1, 'All', $('.fabrication-search-filter').val(), '#fabrication-div', 1);

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