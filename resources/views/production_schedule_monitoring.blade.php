@extends('layouts.user_app', [
  'namePage' => 'MES',
  'activePage' => 'production_schedule_monitoring',
  'pageHeader' => 'Schedule - ' . $operation_details->operation_name,
  'pageSpan' => 'Schedule Date : ' . date('F d, Y', strtotime($schedule_date))
])

@section('content')
<div class="panel-header"></div>

@include('modals.machine_kanban_modal')
<input type="hidden" id="schedule-date-val" value="{{ $schedule_date }}">
<input type="hidden" id="operation-name" value="{{ $operation_details->operation_name }}">
<input type="hidden" id="operation-id" value="{{ $operation_details->operation_id }}">

<div class="row p-0" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
    <div class="col-md-12 p-2 m-0">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body pb-0">
              @php
                $date_format = date('M-d-Y', strtotime($schedule_date));
                if ($schedule_date == date('Y-m-d')) {
                  $header = 'Schedule for Today';
                }elseif($schedule_date > date('Y-m-d')){
                  $header = 'Upcoming Schedule: ' . $date_format;
                }else{
                  $header = 'Schedule Date: ' . $date_format;
                }
              @endphp
              <div class="row">
                <div class="col-6">
                  <ul class="nav nav-tabs font-weight-bold" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="tab1" data-toggle="tab" href="#tab1-1" role="tab" aria-controls="tab1" aria-selected="true">{{ $header }}</a>
                    </li>
                    @if ($operation_details->operation_name != 'Painting')
                    <li class="nav-item">
                      <a class="nav-link" id="tab2" data-toggle="tab" href="#tab2-2" role="tab" aria-controls="tab2" aria-selected="false">
                        <span class="load-machine-schedule">
                          Machine Schedule
                        </span>
                        &nbsp; 
                        <i class="now-ui-icons loader_gear" data-toggle="modal" data-target="#re-order-machines"></i>
                      </a>
                    </li>
                    @endif
                  </ul>
                </div>
                <div class="col-6">
                  <div class="row">
                    <div class="col-12" style="margin-top: -12px;">
                      <div class="row pt-1 pb-2">
                        <div class="col-4 p-1 text-center" style="border-left: solid 10px #27AE60 !important">
                          <span class="font-weight-bold" id="backlogged-production-order-count" style="font-size: 18pt;">0</span>
                          <span class="d-block" style="font-size: 8pt;">Backlogged Production Order(s)</span>
                          <span class="d-none" id="production-order-count"></span>
                        </div>
                        <div class="col-4 p-1 text-center" style="border-left: solid 10px #007BFF !important">
                          <span class="font-weight-bold" id="pending_count" style="font-size: 18pt;">0</span>
                          <span class="d-block" style="font-size: 8pt;">Scheduled Production Order(s)</span>
                        </div>
                        <div class="col-4 p-1 text-center" style="border-left: solid 10px #F96332 !important">
                          <span class="d-block font-weight-bold" id="qty-to-manufacture-count" style="font-size: 18pt;">0</span>
                            <span class="d-block" style="font-size: 8pt;">Total Quantity to Manufacture</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-content">
                <div class="tab-pane active" id="tab1-1" role="tabpanel" aria-labelledby="tab1">
                  <div class="row mt-2">
                    <div class="col-md-12">
                      <div class="card p-0" style="background-color: #0277BD;">
                        <div class="card-body pb-0">
                          <div class="row" style="margin-top: -15px;">
                            <div class="col-md-1" style="padding: 5px 5px 5px 12px;">
                              <h5 class="text-white font-weight-bold text-left m-2 pl-3" style="font-size: 13pt;">Filter</h5>
                            </div>
                            <div class="col-md-7">
                              <table class="w-100 mt-2 p-0" id="filter-form">
                                <col style="width: 40%;">
                                <col style="width: 25%;">
                                <col style="width: 25%;">
                                <col style="width: 10%;">
                                <tr>
                                  <td>
                                    <div class="form-group mb-0 mr-1">
                                      <select class="form-control" id="customer-filter">
                                      </select>
                                    </div>
                                  </td>
                                  <td>
                                    <div class="form-group mb-0 mr-1">
                                      <select class="form-control" id="reference-filter">
                                      </select>
                                    </div>
                                  </td>
                                  <td>
                                    <div class="form-group mb-0 mr-1">
                                      <select class="form-control rounded-0" id="parent-item-filter">
                                      </select>
                                    </div>
                                  </td>
                                  <td class="pl-2">
                                    <button class="btn btn-secondary btn-mini p-2 btn-block m-0" id="clear-kanban-filters">Clear</button>
                                  </td>
                                </tr>
                              </table>
                            </div>
                            <div class="col-md-4 m-0" style="padding: 5px;">
                              <table style="width: 100%;">
                                <tr>
                                  <td style="width: 50%;">
                                    <div class="form-group m-0">
                                      <input type="text" class="form-control bg-white rounded-0" placeholder="Search" id="search_production_schedule">
                                    </div>
                                  </td>
                                  <td style="width: 7%;">
                                    <img style="float:right; margin-right:10px;" src="{{ asset('img/print_btn.png') }}" width="40" height="40" id="btn-print-schedule">
                                  </td>
                                </tr>
                              </table>
                            </div>
                          </div>
                          <div class="row bg-white" style="height: auto; min-height: 400px;">
                            <div class="card card-nav-tabs card-plain">
                              <div class="col-md-12 p-0">
                                <div id="scheduled-production-div" style="min-height: 400px;"></div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane" id="tab2-2" role="tabpanel" aria-labelledby="tab2">
                  <div class="container-fluid" style="background-color: #0277BD;">
                    <div class="row">
                      <div class="col-3 offset-8 p-2">
                        <input type="text" name="search_machine" class="form-control rounded bg-white" placeholder="Search Production Order...">
                      </div>
                      <div class="col-1 p-2">
                        <button type="button" class="btn btn-primary load-machine-schedule btn-sm m-0 w-100 h-100" style="font-size: 10pt;">Search</button>
                      </div>
                    </div>
                  </div>
                  <div id="machine-list" class="container-fluid p-0">
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
       
<style type="text/css">
  .active-process {
    background-color: #e69e0e;
    color: #000000;
    animation: blinkingBackground 2.5s linear infinite;
  }
  @keyframes blinkingBackground{
    0%    { background-color: #e69e0e;}
    25%   { background-color: #FFC107;}
    50%   { background-color: #e69e0e;}
    75%   { background-color: #FFC107;}
    100%  { background-color: #e69e0e;}
  }
  #workstation-tabs .active{
    background-color: #f96332;
    font-weight: bold;
    color:#ffffff;
  }

  @-webkit-keyframes blinker {
    from {
      background-color: #CD6155;
    }
    to {
      background-color: inherit;
    }
  }
  
  @-moz-keyframes blinker {
    from {
      background-color: #CD6155;
    }
    to {
      background-color: inherit;
    }
  }
  
  @-o-keyframes blinker {
    from {
      background-color: #CD6155;
    }
    
    to {
      background-color: inherit;
    }
  }
  
  #editcpt-modal .form-control {
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }

  .blink {
    text-decoration: blink;
    -webkit-animation-name: blinker;
    -webkit-animation-duration: 1s;
    -webkit-animation-iteration-count: infinite;
    -webkit-animation-timing-function: ease-in-out;
    -webkit-animation-direction: alternate;
  }

  .dot {
    height: 15px;
    width: 15px;
    border-radius: 50%;
    display: inline-block;
  }

  #loader-wrapper {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 99999;
    padding: 2px;
  }
  #loader {
    display: block;
    position: relative;
    left: 50%;
    top: 50%;
    width: 150px;
    height: 150px;
    margin: -75px 0 0 -75px;
    border-radius: 50%;
    border: 3px solid transparent;
    border-top-color: #3498db;
    -webkit-animation: spin 2s linear infinite; /* Chrome, Opera 15+, Safari 5+ */
    animation: spin 2s linear infinite; /* Chrome, Firefox 16+, IE 10+, Opera */
    z-index: 999999;
  }

  #loader:before {
    content: "";
    position: absolute;
    top: 5px;
    left: 5px;
    right: 5px;
    bottom: 5px;
    border-radius: 50%;
    border: 3px solid transparent;
    border-top-color: #e74c3c;
    -webkit-animation: spin 3s linear infinite; /* Chrome, Opera 15+, Safari 5+ */
    animation: spin 3s linear infinite; /* Chrome, Firefox 16+, IE 10+, Opera */
  }

  #loader:after {
    content: "";
    position: absolute;
    top: 15px;
    left: 15px;
    right: 15px;
    bottom: 15px;
    border-radius: 50%;
    border: 3px solid transparent;
    border-top-color: #f9c922;
    -webkit-animation: spin 1.5s linear infinite; /* Chrome, Opera 15+, Safari 5+ */
    animation: spin 1.5s linear infinite; /* Chrome, Firefox 16+, IE 10+, Opera */
  }

  @-webkit-keyframes spin {
    0%   { 
      -webkit-transform: rotate(0deg);  /* Chrome, Opera 15+, Safari 3.1+ */
      -ms-transform: rotate(0deg);  /* IE 9 */
      transform: rotate(0deg);  /* Firefox 16+, IE 10+, Opera */
    }
    100% {
      -webkit-transform: rotate(360deg);  /* Chrome, Opera 15+, Safari 3.1+ */
      -ms-transform: rotate(360deg);  /* IE 9 */
      transform: rotate(360deg);  /* Firefox 16+, IE 10+, Opera */
    }
  }
  @keyframes spin {
    0%   { 
      -webkit-transform: rotate(0deg);  /* Chrome, Opera 15+, Safari 3.1+ */
      -ms-transform: rotate(0deg);  /* IE 9 */
      transform: rotate(0deg);  /* Firefox 16+, IE 10+, Opera */
    }
    100% {
      -webkit-transform: rotate(360deg);  /* Chrome, Opera 15+, Safari 3.1+ */
      -ms-transform: rotate(360deg);  /* IE 9 */
      transform: rotate(360deg);  /* Firefox 16+, IE 10+, Opera */
    }
  }

  #loader-wrapper .loader-section {
    position: absolute;
    top: 0;
    width: 100%;
    height: 100%;
    background-color:  #e5e7e9 ;
    z-index: 1000;
    opacity: 50%;
    -webkit-transform: translateX(0);  /* Chrome, Opera 15+, Safari 3.1+ */
    -ms-transform: translateX(0);  /* IE 9 */
    transform: translateX(0);  /* Firefox 16+, IE 10+, Opera */
  }

  .loaded #loader {
    opacity: 0;
    -webkit-transition: all 0.3s ease-out;  
    transition: all 0.3s ease-out;
  }
  .loaded #loader-wrapper {
    visibility: hidden;
    -webkit-transform: translateY(-100%);  /* Chrome, Opera 15+, Safari 3.1+ */
    -ms-transform: translateY(-100%);  /* IE 9 */
    transform: translateY(-100%);  /* Firefox 16+, IE 10+, Opera */
    -webkit-transition: all 0.3s 1s ease-out;  
    transition: all 0.3s 1s ease-out;
  }

  #s-tab-1 .nav-item .active{
		background-color: #fff !important;
		font-weight: bold !important;
    border-bottom: solid 5px #f96332;
		color: #f96332 !important;
	}
</style>

<div class="modal fade" id="re-order-machines" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #0277BD">
        <h5 class="modal-title text-white" id="exampleModalLabel">Re-order Machines</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="/save_machine_order" id="save-machine-order-form" method="post">
          @csrf
          <div id="sortable" class="container">
            @foreach ($machines as $machine)
              <div class="col-12 p-2 mb-1 border">
                <span>
                  <i class="now-ui-icons gestures_tap-01"></i> &nbsp;<b>{{ $machine->machine_code }}</b> - {{ $machine->machine_name }}
                  <input type="text" class="d-none" name="machine_list[]" value="{{ $machine->machine_id }}">
                </span>
              </div>
            @endforeach
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary save-machine-order">Save changes</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="reset-task-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="width: 30%;">
    <form action="/restart_task" method="POST" id="reset-task-frm">
      @csrf
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title">
            <span>Reset Task</span>
            <span class="workstation-text" style="font-weight: bolder;"></span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row p-0">
            <div class="col-md-12 p-2">
              <h5 class="text-center">Reset start and end time?</h5>
              <input type="hidden" name="id" required>
            </div>
          </div>
        </div>
        <div class="modal-footer p-2">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Confirm</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="change-process-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="width: 30%;">
    <form action="/update_process" method="POST" id="change-process-frm">
      @csrf
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title">
            <span>Change Process</span>
            <span class="workstation-text"></span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-8 offset-md-2">
              <div class="form-group" style="font-size: 16pt;">
                <label for="qty-accepted">Select Process</label>
                <input type="hidden" name="id" required>
                <select id="sel-process" class="form-control form-control-lg" name="process" style="font-size: 14pt;"></select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer p-2">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="view-machine-task-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document" style="min-width: 60%;">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #0277BD;">
        <h5 class="modal-title">Modal Title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div id="tbl-view-machine-task-modal"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addnotes-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <form action="/addnotes_task" method="POST" id="addnotes-frm">
      @csrf
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title">
            <span>Add Notes to Operator</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <h5 class="text-left" style="font-size: 13pt;">Notes:</h5>
              <input type="hidden" name="prod_no" id="prod_no">
            </div>
            <div class="col-md-12">
              <textarea name="remarks_field" id="remarks_field" cols="30" rows="5" style="width:100%;max-width:100%;min-width:100%;"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer p-2">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Confirm</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="process-view-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="min-width: 50%;">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #0277BD;">
        <h5 class="modal-title">
          <span>Process</span>
          <span class="sampling-delete-text font-weight-bold"></span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12" id="process_tbl_modal"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editcpt-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <form action="/edit_cpt_status_qty" method="POST" id="edit-cpt-status-qty-frm">
      @csrf
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title"><span>Edit</span>
            <span class="prod-text font-weight-bolder"></span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="col-md-12">
            <input type="hidden" name="prod_no" id="prod_no">
            <input type="hidden" name="qty_validation" id="qty_validation">
            <div class="form-group">
              <label><b>Loading Completed Qty:</b></label>
              <input type="text" class="form-control" name="loading_cpt" placeholder="Loading Completed Qty" required id="loading_cpt">
            </div>
            <div class="form-group">
              <label><b>Loading Status:</b></label>
              <select class="form-control sel4" name="loading_status" id="loading_status" required>
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
              </select>
            </div>
            <div class="form-group">
              <label><b>Unloading Completed Qty:</b></label>
              <input type="text" class="form-control" name="unloading_cpt" placeholder="Unloading Completed Qty" required id="unloading_cpt">
            </div>
            <div class="form-group">
              <label><b>Unloading Status:</b></label>
              <select class="form-control sel4" name="unloading_status" id="unloading_status" required>
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
              </select>
            </div>
            <div class="form-group">
              <label><b>Completed Qty:</b></label>
              <input type="text" class="form-control" name="cpt_overall" placeholder="Completed Qty" required id="cpt_overall">
            </div>
            <div class="form-group">
              <label><b>Status:</b></label>
              <select class="form-control sel4" name="status_overall" id="status_overall" required>
                <option value="Not Started">Not Started</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer p-2">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Confirm</button>
        </div>
      </div>
    </form>
  </div>
</div>

<iframe id="frame-print" class="d-none"></iframe>
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<script>
  $(document).ready(function () {
    $( "#sortable" ).sortable();
    var scheduled_date = $('#schedule-date-val').val();
    var operation_name = $('#operation-name').val();
    var operation_id = $('#operation-id').val();
    
    const load_machines_schedules = () => {
      $('#machine-list').html('<div class="w-100 d-flex justify-content-center align-items-center p-3">' +
        '<div class="spinner-border" role="status">' +
          '<span class="sr-only">Loading...</span>' +
        '</div>' +
      '</div>');

      $.ajax({
        url: "/production_schedule_monitoring/{{ $operation_details->operation_id }}/{{ $schedule_date }}",
        type: "GET",
        data: {
          machines: true,
          production_order: $('input[name="search_machine"]').val()
        },
        success: function (data) {
          $('#machine-list').html(data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
          showNotification("danger", 'An error occured. Unable to load machine schedules', "now-ui-icons travel_info");
        }
      });
    }

    $(document).on('click', '.load-machine-schedule', (e) => {
      load_machines_schedules()
    })

    $(document).on('click', '.save-machine-order', (e) => {
      e.preventDefault()
      $.ajax({
        url: $('#save-machine-order-form').attr('action'),
        type: "POST",
        data: $('#save-machine-order-form').serialize(),
        success: (response) => {
          if(response.success){
            showNotification("success", response.message, "now-ui-icons travel_info")
            $('#re-order-machines').modal('hide')
            load_machines_schedules()
          }else{
            showNotification("danger", response.message, "now-ui-icons travel_info")
          }
        },
        error: (jqXHR, textStatus, errorThrown) => {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
          showNotification("danger", 'An error occured. Unable to load machine schedules', "now-ui-icons travel_info");
        }
      });
    })

    $(document).on('change', '#filter-form select', function() {
      // filter_monitoring_table($('#customer-filter').val(), $('#reference-filter').val(), $('#parent-item-filter').val());
      filter_schedule_monitoring_table($('#customer-filter').val(), $('#reference-filter').val(), $('#parent-item-filter').val());
    });

    $(document).on('click', '#clear-kanban-filters', function(e){
      e.preventDefault();
      $('#customer-filter').val('all').trigger('change');
      $('#reference-filter').val('all').trigger('change');
      $('#parent-item-filter').val('all').trigger('change');
    
      filter_monitoring_table($('#customer-filter').val(), $('#reference-filter').val(), $('#parent-item-filter').val());
    });

    function filter_schedule_monitoring_table(fltr1, fltr2, fltr3){
      var url = '{{ $operation_details->operation_name == "Painting" ? "/get_production_schedule_monitoring_list" : "/production_schedule_monitoring/".$operation_details->operation_id }}';
      $.ajax({
        url: url + "/{{ $schedule_date }}",
        type: "GET",
        data: {
          customer: fltr1,
          reference: fltr2,
          parent: fltr3
        },
        success: function (response) {
          $('#scheduled-production-div').html(response);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    }

    $('#customer-filter').select2({
      placeholder: 'Select a Customer',
      ajax: {
        url: '/production_schedule_monitoring_filters/{{ $operation_details->operation_id }}/{{ $schedule_date }}',
        method: 'GET',
        dataType: 'json',
        data: function (data) {
          return {
            search_customer: data.term // search term
          };
        },
        processResults: function (response) {
          return {
            results: response.customers
          };
        },
        cache: true
      }
    });

    $('#reference-filter').select2({
      placeholder: 'Select Reference Number',
      ajax: {
        url: '/production_schedule_monitoring_filters/{{ $operation_details->operation_id }}/{{ $schedule_date }}',
        method: 'GET',
        dataType: 'json',
        data: function (data) {
          return {
            search_reference: data.term // search term
          };
        },
        processResults: function (response) {
          return {
            results: response.reference_nos
          };
        },
        cache: true
      }
    });

    $('#parent-item-filter').select2({
      placeholder: 'Select Parent Item Code',
      ajax: {
        url: '/production_schedule_monitoring_filters/{{ $operation_details->operation_id }}/{{ $schedule_date }}',
        method: 'GET',
        dataType: 'json',
        data: function (data) {
          return {
            search_parent: data.term // search term
          };
        },
        processResults: function (response) {
          return {
            results: response.parent
          };
        },
        cache: true
      }
    });

    // start by showing all items
    $('#monitoring-table .tbl-row').removeClass('d-none');
    function filter_monitoring_table(fltr1, fltr2, fltr3) {
      // reset results list
      $('#monitoring-table .tbl-row').addClass('d-none');
      
      // the filtering in action for all criteria
      var selector = "#monitoring-table .tbl-row";
      if (fltr1 !== 'all') {
           selector = selector + '[data-customer="' + fltr1 + '"]';
      }

      if (fltr2 !== 'all') {
        selector =  selector + '[data-reference-no="' + fltr2 + '"]';
      }

      if (fltr3 !== 'all') {
        selector =  selector + '[data-parent-item="' + fltr3 + '"]';
      }

      // count rows without d-none

      // show all results
      $(selector).removeClass('d-none');
    }

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $(document).on('click', '.view-process-qty-btn', function (e) {
      e.preventDefault();
      var prod = $(this).data('production-order');
      $.ajax({
        url: "/get_production_sched_assembly_view_process/" + prod,
        type: "GET",
        success: function (data) {
          $('#process_tbl_modal').html(data);
          $('#process-view-modal').modal('show');
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });

    $(document).on('click', '.addnotes', function () {
      $('#addnotes-modal #prod_no').val($(this).data('production-order'));
      $('#addnotes-modal #remarks_field').val($(this).data('notes'));
      $('#addnotes-modal').modal('show');
    });

    $('#addnotes-frm').submit(function (e) {
      e.preventDefault();
      $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: $(this).serialize(),
        success: function (data) {
          if (data.success) {
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#addnotes-modal').modal('hide');
            load_scheduled_production_order();
          } else {
            showNotification("danger", data.message, "now-ui-icons travel_info");
            return false;
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        },
      });
    });

    if(operation_name == 'Painting'){
      load_scheduled_painting();

      $(document).on('keyup', '#search_production_schedule', function(){
        var query = $(this).val();
        load_scheduled_painting(1, query);
      });
    }else{
      load_scheduled_production_order();

      $("#search_production_schedule").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#monitoring-table tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
      });
    }
    
    function load_scheduled_production_order(){
      $.ajax({
        url:"/{{ Request::path() }}",
        type:"GET",
        success:function(response){
          $('#scheduled-production-div').html(response);

          $('.select-custom').select2({
            dropdownParent: $("#filter-form"),
            dropdownAutoWidth: false,
            width: '100%',
            cache: false
          });
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    }

    function load_scheduled_painting(page, query){
      $.ajax({
        url:"/get_production_schedule_monitoring_list/" + scheduled_date +"/?page="+page,
        type:"GET",
        data: {search_string: query},
        success:function(data){
          $('#scheduled-production-div').html(data);

          $('.select-custom').select2({
            dropdownParent: $("#filter-form"),
            dropdownAutoWidth: false,
            width: '100%',
            cache: false
          });
        }
      }); 
    }

    $(document).on('click', '#btn-print-schedule', function () {
      window.open("/get_scheduled_production_order/" + operation_id + "/" + scheduled_date, '_blank');
    });
  
    count_current_production();
    function count_current_production() {
      $.ajax({
        url: "/count_current_production_order/" + scheduled_date,
        type: "GET",
        success: function (data) {
          $('#pending_count').text(data.pending);
          $('#inprogress_count').text(data.inProgress);
          $('#completed_count').text(data.completed);
          $('#reject_count').text(data.reject);
          $('#pending_qty').text(data.pending_qty);
          $('#inprogress_qty').text(data.inProgress_qty);
          $('#totransfer_qty').text(data.completed_qty);
        }
      });
    }

    $('#confirm-feedback-production-modal').on('hide.bs.modal', function (e) {
      if(operation_name == 'Painting'){
        load_scheduled_painting();
      }else{
        load_scheduled_production_order();
      }
    });

    if(operation_name.indexOf('Fabrication') > -1){
      var workstation_id = $('#workstation-tabs li a.active').data('workstation-id');
      load_workstation_schedules(workstation_id);

      function load_workstation_schedules(workstation_id){
        $('#machine-schedule-div-content').empty();
        $('#loader-wrapper').removeAttr('hidden');
        $.ajax({
          url:"/production_fabrication_machine_board/" + workstation_id + "/" + scheduled_date,
          type:"GET",
          success:function(data){
            $('#loader-wrapper').attr('hidden', true);
            $('#machine-schedule-div-content').html(data);
          }
        }); 
      }
  
      $(document).on('click', '.view-details', function(e){
        var timelog_id = $(this).find('span.timelog-id').eq(0).text();
        var timelog_status = $(this).find('span.timelog-status').eq(0).text();
        var remarks = $(this).find('span.remarks').eq(0).text();
        var production_order = $(this).find('span.production-order').eq(0).text();
        var workstation = $(this).find('span.workstation').eq(0).text();
        var workstation_id = $(this).find('span.workstation-id').eq(0).text();
        var job_ticket_id = $(this).find('span.jobticket-id').eq(0).text();
        var planned_start_date = $(this).find('span.planned-start-date').eq(0).text();
        var delivery_date = $(this).find('span.delivery-date').eq(0).text();
        var process_name = $(this).find('span.process-name').eq(0).text();
        var reference_no = $(this).find('span.reference-no').eq(0).text();
        var customer = $(this).find('span.customer').eq(0).text();
        var item_code = $(this).find('span.item-code').eq(0).text();
        var item_description = $(this).find('span.item-description').eq(0).text();
        var qty_to_manufacture = $(this).find('span.qty-to-manufacture').eq(0).text();
        var completed_qty = $(this).find('span.completed-qty').eq(0).text();
        var stock_uom = $(this).find('span.stock-uom').eq(0).text();
        var machine_name = $(this).find('span.machine-name').eq(0).text();
        var machine_code = $(this).find('span.machine-code').eq(0).text();
        var start_time = $(this).find('span.start-time').eq(0).text();
        var end_time = $(this).find('span.end-time').eq(0).text();
        var duration_in_mins = $(this).find('span.duration-in-mins').eq(0).text();
        var cycle_time_in_mins = $(this).find('span.cycle-time-in-mins').eq(0).text();
        var operator_name = $(this).find('span.operator-name').eq(0).text();
  
        $('#jtname-modal .prod-view-btn').eq(0).removeData('production-order').data('production-order', production_order).text(production_order);
  
        $('#jtname-modal .workstation-name').eq(0).text(workstation);
        $('#jtname-modal .planned-start-date').eq(0).text(planned_start_date);
        $('#jtname-modal .delivery-date').eq(0).text(delivery_date);
        $('#jtname-modal .process-name').eq(0).text(process_name);
        $('#jtname-modal .reference-no').eq(0).text(reference_no);
        $('#jtname-modal .customer').eq(0).text(customer);
        $('#jtname-modal .item-code').eq(0).text(item_code);
        $('#jtname-modal .item-description').eq(0).text(item_description);
        $('#jtname-modal .qty-to-manufacture').eq(0).text(qty_to_manufacture);
        $('#jtname-modal .completed-qty').eq(0).text(completed_qty);
        $('#jtname-modal .stock-uom').text(stock_uom);
        $('#jtname-modal .machine-code').text('[' + machine_code +']');
        $('#jtname-modal .machine-name').text(machine_name);
        $('#jtname-modal .start-time').text(start_time);
        $('#jtname-modal .end-time').text(end_time);
        $('#jtname-modal .duration-in-mins').text(duration_in_mins);
        $('#jtname-modal .cycle-time-in-mins').text(cycle_time_in_mins);
        $('#jtname-modal .operator-name').text(operator_name);
  
        $('#jtname-modal .time-log-id').val(timelog_id);
        $('#jtname-modal .time-log-status').val(timelog_status);
        $('#jtname-modal .workstation-id').val(workstation_id);
        $('#jtname-modal .job-ticket-id').val(job_ticket_id);
  
        $('#div_machine_details').hide();
        var a = ['Completed', 'In Progress'];
        if(a.indexOf(timelog_status) > -1){
          if(timelog_status == 'In Progress'){
            $('#jtname-modal .end-time').text('-');
            $('#jtname-modal .duration-in-mins').text('-');
            $('#jtname-modal .cycle-time-in-mins').text('-');
          }
          
          $('#div_machine_details').show();
        }
  
        $('#div_machine_details h5').eq(0).remove();
  
        if(remarks == 'Override'){
          $('#div_machine_details').show();
          $('#div_machine_details .row').eq(0).hide();
          $('#div_machine_details').append('<h5 class="text-uppercase text-center font-weight-bold">OVERRIDEN</h5>');
        }
  
        $('#jt-details-tbl tbody').empty();
        $('#div_quality_check').hide();
        if(timelog_id){
          $.ajax({
            url: "/get_qa_details/" + timelog_id,
            type:"GET",
            success:function(data){
              if(data.qa_tables.length > 0){
                $('#div_quality_check').show();
                var r = '';
                $.each(data.qa_tables, function(i, v){
                  var badge = 'badge-warning';
                  if (v.status == "QC Passed") {
                    badge = 'badge-success';
                  }else if(v.status == "QC Failed"){
                    badge = 'badge-danger';
                  }else{
                    badge = 'badge-warning';      
                  }
                  
                  r += '<tr>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;"><b>' + v.qa_inspection_type + '</b></td>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;"><b>' + v.qa_inspection_date + '</b></td>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;"><b>' + v.qa_staff_id + '</b></td>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;">' + v.sample_size + '</td>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;">' + v.rejected_qty + '</td>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;">' + v.for_rework_qty + '</td>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;"><span class="badge ' + badge + '">' + v.status + '</span></td>' +
                    '</tr>';
                });
                
                $('#jt-details-tbl tbody').append(r);
              }
            }
          });
        }
        
        $('#jtname-modal').modal("show");
      });
  
      $(document).on('click', '#reset-time-btn', function(){
        var workstation = $('#machine_kanban_details .workstation-name').text();
        var timelog_id = $('#jtname-modal .time-log-id').eq(0).val();
        var timelog_status = $('#jtname-modal .time-log-status').eq(0).val();
  
        if(!timelog_id){
          showNotification("danger", 'Unable to reset task. There are no on-going time logs for this task.', "now-ui-icons travel_info");
          return false;
        }
  
        if(timelog_status == 'Complete1d'){
          showNotification("info", 'Task already completed.', "now-ui-icons travel_info");
          return false;
        }
      
        $('#reset-task-modal input[name="id"]').val(timelog_id);
        $('#reset-task-modal .workstation-text').text('[' + workstation + ']');
        $('#reset-task-modal').modal('show');
      });
  
      $(document).on('click', '#change-process-btn', function(){
        var timelog_id = $('#jtname-modal .time-log-id').eq(0).val();
        var timelog_status = $('#jtname-modal .time-log-status').eq(0).val();
  
        var disable_change = ['Completed', 'In Progress']
        if (disable_change.indexOf(timelog_status) > -1) {
          showNotification("danger", 'Unable to change Process.', "now-ui-icons travel_info");
          return false;
        }
  
        $('#change-process-modal #sel-process').empty();
        
        var workstation_id = $('#jtname-modal .workstation-id').val();
        var current_process = $('#jtname-modal .process-name').eq(0).text();
        var workstation = $('#machine_kanban_details .workstation-name').text();
        var jtid = $('#jtname-modal .job-ticket-id').val();
  
        $('#change-process-modal input[name="id"]').val(jtid);
  
        $('#change-process-modal .workstation-text').text('[' + workstation + ']');
  
        $.ajax({
          url: "/get_process_list/" + workstation_id,
          type:"GET",
          success:function(data){
            var row = '';
            $.each(data, function(i, v){
              var selected = (current_process == v.process_name) ? 'selected' : '';
              row += '<option value="' + v.process_id + '" '+ selected +'>' + v.process_name + '</option>';
            });
  
            $('#change-process-modal #sel-process').append(row);
            $('#change-process-modal').modal('show');
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
          },
        });
      });
  
      $('#change-process-frm').submit(function(e){
        e.preventDefault();
        var current_process = $('#machine_kanban_details .process-name').text();
        var new_process = $('#change-process-modal #sel-process option:selected').text();
        if (current_process == new_process) {
          showNotification("info", 'No changes made.', "now-ui-icons travel_info");
          return false;
        }else{
          $.ajax({
            url: $(this).attr('action'),
            type:"POST",
            data: $(this).serialize(),
            success:function(data){
              if (data.success) {
                showNotification("success", data.message, "now-ui-icons ui-1_check");
                $('#change-process-modal').modal('hide');
                $('#jtname-modal').modal("hide");
                $('#view-machine-task-modal').modal("hide");
                var workstation_id = $('#workstation-tabs li a.active').data('workstation-id');
                load_workstation_schedules(workstation_id);
              }else{
                showNotification("danger", data.message, "now-ui-icons travel_info");
                return false;
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            },
          });
        }      
      });
  
      $('#reset-task-frm').submit(function(e){
        e.preventDefault();
        $.ajax({
          url: $(this).attr('action'),
          type:"POST",
          data: $(this).serialize(),
          success:function(data){
            if (data.success) {
              showNotification("success", data.message, "now-ui-icons ui-1_check");
              $('#reset-task-modal').modal('hide');
              $('#jtname-modal').modal("hide");
              $('#view-machine-task-modal').modal("hide");
              var workstation_id = $('#workstation-tabs li a.active').data('workstation-id');
              load_workstation_schedules(workstation_id);
            }else{
              showNotification("danger", data.message, "now-ui-icons travel_info");
              return false;
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
          },
        }); 
      });
  
      $(document).on('click', '#mark-done-btn', function(){
        var workstation = $('#machine_kanban_details .workstation-name').text();
        var jtid = $('#jtname-modal .job-ticket-id').val();
        $('#mark-done-modal input[name="id"]').val(jtid);
        $('#mark-done-modal #jt-index').val(jtid);
        $('#mark-done-modal #workstation-override').val(workstation);
        $('#mark-done-modal .workstation-text').text('[' + workstation + ']');
        $('#mark-done-modal').modal('show');
      });
  
      $('#mark-done-frm').submit(function(e){
        e.preventDefault();
        $.ajax({
          url: $(this).attr('action'),
          type:"POST",
          data: $(this).serialize(),
          success:function(data){
            if (data.success) {
              showNotification("success", data.message, "now-ui-icons ui-1_check");
              $('#mark-done-modal').modal('hide');
              $('#jtname-modal').modal("hide");
              $('#view-machine-task-modal').modal("hide");
              var workstation_id = $('#workstation-tabs li a.active').data('workstation-id');
              load_workstation_schedules(workstation_id);
            }else{
              showNotification("danger", data.message, "now-ui-icons travel_info");
              return false;
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
          },
        }); 
      });
  
      $(document).on('click', '.view-btn-modal', function(e){
        var process_id = $(this).attr('data-process-id');
        var process_name = $(this).attr('data-process-name');
        var current_workstation = $('#workstation-tabs li a.active').data('workstation-id');
  
        $.ajax({
          url:"/machineKanban_view_machineList/" + process_id + "/" + current_workstation + "/" + scheduled_date,
          type:"GET",
          success:function(response){
            $('#tbl-view-machine-task-modal').html(response);
            $('#view-machine-task-modal').modal('show');
            $('#view-machine-task-modal .modal-title').text(process_name);
          }
        });
      });
    }

    $('#workstation-tabs .nav-link').click(function(e){
      e.preventDefault();
      load_workstation_schedules($(this).data('workstation-id'));
    });

		$(document).on('click', '.print-schedule', function(e){
			e.preventDefault();
			$('#frame-print').attr('src', '/assembly/print_machine_schedule/' + scheduled_date + '/' + $(this).data('machine'));
		});

    $(document).on('click', '.editcpt_qty', function (e) {
      e.preventDefault();
      var prod = $(this).data('prod');
      var qty = $(this).data('qty');

      $.ajax({
        url: "/get_production_details_for_edit/" + prod,
        type: "GET",
        success: function (data) {
          $('#editcpt-modal #prod_no').val(prod);
          $('#editcpt-modal .prod-text').text('[' + prod + ']');
          $('#loading_cpt').val(data.loading_cpt);
          $('#loading_status').val(data.loading_status);
          $('#unloading_cpt').val(data.unloading_cpt);
          $('#unloading_status').val(data.unloading_status);
          $('#cpt_overall').val(data.completed);
          $('#status_overall').val(data.status);
          $('#qty_validation').val(qty);
          $('#prod_no').val(prod);
          $('#editcpt-modal').modal('show');
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        },
      });
    });

    $('#edit-cpt-status-qty-frm').submit(function (e) {
      e.preventDefault();
      var qty_to_manufacture = $('#qty_validation').val();
      var loading = $('#loading_cpt').val();
      var unloading = $('#unloading_cpt').val();
      var overall_cpt = $('#cpt_overall').val();

      if (qty_to_manufacture < loading) {
        showNotification("danger", "Loading qty must not be greater than " + qty_to_manufacture + '', "now-ui-icons travel_info");
        return false;
      } else if (qty_to_manufacture < unloading) {
        showNotification("danger", "Unloading qty must not be greater than " + qty_to_manufacture + '', "now-ui-icons travel_info");
        return false;
      } else if (qty_to_manufacture < overall_cpt) {
        showNotification("danger", "Completed qty must not be greater than " + qty_to_manufacture + '', "now-ui-icons travel_info");
        return false;
      } else {
        $.ajax({
          url: $(this).attr('action'),
          type: "POST",
          data: $(this).serialize(),
          success: function (data) {
            if (data.success) {
              showNotification("success", data.message, "now-ui-icons ui-1_check");
              $('#editcpt-modal').modal('hide');
              load_scheduled_painting();
              count_current_production();
            } else {
              showNotification("danger", data.message, "now-ui-icons travel_info");
              return false;
            }
          },
          error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
          },
        });
      }
    });
    
    function showNotification(color, message, icon){
      $.notify({
        icon: icon,
        message: message
      },{
        type: color,
        timer: 3000,
        placement: {
          from: 'top',
          align: 'center'
        }
      });
    }

    $('#reschedule_delivery_frm').submit(function(e){
      e.preventDefault();
      $.ajax({
        url: $(this).attr("action"),
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#reschedule-delivery-modal').modal('hide');
            if(operation_name == 'Painting'){
              load_scheduled_painting();
            }else{
              load_scheduled_production_order();
            }
          }
        }
      });
    });
  });
</script>
@endsection