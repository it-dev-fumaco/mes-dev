@extends('layouts.user_app', [
  'namePage' => 'MES',
  'activePage' => 'production_schedule_monitoring',
])

@section('content')
<div class="panel-header">
  <div class="header text-center">
    <div class="row" style="margin-top:-70px;margin-left:140px;">
      <div class="col-md-12">
        <table class="text-center" style="width: 100%;">
          <tr>
            <td style="width: 25%; border-right: 5px solid white; color:white;">
              <h4 class="title text-center" style="margin-left: 20px; margin: auto 20pt;">Schedule Date : {{ date('F d, Y', strtotime($schedule_date)) }}</h4>
            </td>
            <td style="width: 50%">
              <h4 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Production Schedule Monitoring - {{ $operation_details->operation_name }}</h4>
              <span class="title text-left d-block" style="font-size: 11pt;margin-left: 30px;">{{ Auth::user()->employee_name }}</span>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>

@include('modals.machine_kanban_modal')
<input type="hidden" id="schedule-date-val" value="{{ $schedule_date }}">
<input type="hidden" id="operation-name" value="{{ $operation_details->operation_name }}">
<input type="hidden" id="operation-id" value="{{ $operation_details->operation_id }}">

<div class="content">
  <div class="row" style="margin-top: -70px;">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-12" style="margin-top:-110px;">
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
              <ul class="nav nav-tabs font-weight-bold" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="tab1" data-toggle="tab" href="#tab1-1" role="tab" aria-controls="tab1" aria-selected="true">{{ $header }}</a>
                </li>
                @if ($operation_details->operation_name != 'Painting')
                <li class="nav-item">
                  <a class="nav-link" id="tab2" data-toggle="tab" href="#tab2-2" role="tab" aria-controls="tab2" aria-selected="false">Machine Schedule</a>
                </li>
                @endif
              </ul>
              <div class="tab-content">
                <div class="tab-pane active" id="tab1-1" role="tabpanel" aria-labelledby="tab1">
                  <div class="row mt-2">
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-4 offset-md-8" style="margin-top: -62px;">
                          <table class="text-center m-0" style="width: 100%;" id="totals-table">
                            <tr>
                              <td class="align-top p-0" style="width: 25%;">
                                <span class="font-weight-bold" id="pending_count" style="font-size: 18pt;">0</span>
                                <span class="d-block" style="font-size: 8pt;">Production Order</span>
                                {{--  <span class="font-weight-bold" style="font-size: 8pt;" id="pending_qty">0</span>
                                <span style="font-size: 8pt;">unit(s)</span>  --}}
                              </td>
                              <td class="align-top p-0" style="width: 25%;">
                                <span class="d-block font-weight-bold p-0" id="inprogress_count" style="font-size: 18pt;">0</span>
                                <span class="d-block" style="font-size: 8pt;">In Progress</span>
                                {{--  <span class="font-weight-bold" style="font-size: 8pt;" id="inprogress_qty">0</span>
                                <span style="font-size: 8pt;">unit(s)</span>  --}}
                              </td>
                              <td class="align-top p-0" style="width: 25%;">
                                <span class="d-block font-weight-bold" id="reject_count" style="font-size: 18pt;">0</span>
                                <span class="d-block" style="font-size: 8pt;">Rejects</span>
                              </td>
                              <td class="align-top p-0" style="width: 25%;">
                                <span class="d-block font-weight-bold" id="completed_count" style="font-size: 18pt;">0</span>
                                <span class="d-block" style="font-size: 8pt;">Ready For Transfer</span>
                                {{--  <span class="font-weight-bold" style="font-size: 8pt;" id="totransfer_qty">0</span>
                                <span style="font-size: 8pt;">unit(s)</span>  --}}
                              </td>
                            </tr>
                          </table>
                        </div>
                      </div>
                      <div class="card p-0" style="background-color: #0277BD;">
                        <div class="card-body pb-0">
                          <div class="row" style="margin-top: -15px;">
                            <div class="col-md-8" style="padding: 5px 5px 5px 12px;">
                              <h5 class="text-white font-weight-bold text-left m-1">{{ $header }}</h5>
                            </div>
                            <div class="col-md-4 m-0" style="padding: 5px;">
                              <table style="width: 100%;">
                                <tr>
                                  <td style="width: 50%;">
                                    <div class="form-group m-0">
                                      <input type="text" class="form-control bg-white" placeholder="Search" id="search_production_schedule">
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
                  @if ($operation_details->operation_name == 'Fabrication')
                  <div class="row mt-2 mb-0 mr-0 ml-0 p-0">
                    <div class="col-md-6 offset-md-6 p-0" style="margin-top: -50px;">
                      <div class="d-flex flex-row justify-content-end p-0">
                        <div class="p-2">
                          <span class="dot bg-secondary" style="margin-left: 12px;"></span>
                          <span style="font-size: 12pt; padding-right: 13px;padding-left: 3px;">Not Started</span>
                        </div>
                        <div class="p-2">
                          <span class="dot bg-warning" style="margin-left: 12px;"></span>
                          <span style="font-size: 12pt; padding-right: 13px;padding-left: 3px;">In Progress</span>
                        </div>
                        <div class="p-2">
                          <span class="dot bg-success" style="margin-left: 12px;"></span>
                          <span style="font-size: 12pt; padding-right: 13px;padding-left: 3px;">Completed</span>
                        </div>
                        <div class="p-2">
                          <span class="dot bg-danger" style="margin-left: 12px;"></span>
                          <span style="font-size: 12pt; padding-right: 13px;padding-left: 3px;">Late</span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-2 p-2">
                      <div class="card m-0" style="background-color:#D5D8DC; min-height: 780px;">
                        <div class="card-header p-2">
                          <h6 class="text-center m-0 p-0 font-weight-bolder">Workstation</h6>
                        </div>
                        <div class="card-body overflow-auto pt-0" style="height: 740px;">
                          <ul class="nav flex-column font-weight-bolder" role="tablist" style="font-size: 10pt;" id="workstation-tabs">
                            @foreach($workstation_list as $row)
                            <li class="nav-item text-center" style="cursor: pointer;">
                              <a class="nav-link {{ ($loop->first) ? 'active' : '' }}" data-workstation-id="{{ $row->workstation_id }}" data-toggle="tab">{{ $row->workstation_name }}</a>
                            </li>
                            @endforeach 
                          </ul>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-10 m-0 p-0">
                      <div id="loader-wrapper" hidden>
                        <div id="loader"></div>
                        <div class="loader-section section-left" style="border: 8px solid white;"></div>
                        <div class="loader-section section-right" style="border: 8px solid white;"></div>
                      </div>
                      <div class="scrolling-wrapper row flex-row flex-nowrap m-0 p-0" style="overflow-x: auto;" id="machine-schedule-div-content"></div>
                    </div>
                  </div>
                  @endif

                  @if (strpos($operation_details->operation_name, 'Assembly'))
                  <div class="col-md-6 offset-md-6 p-0" style="margin-top: -50px;">
                    <div class="d-flex flex-row justify-content-end p-0">
                      <div class="p-2">
                        <span class="dot bg-secondary" style="background-color:#717D7E; margin-left: 12px;"></span>
                        <span style="font-size: 12pt; padding-right: 13px;padding-left: 3px;">Not Started</span>
                      </div>
                      <div class="p-2">
                        <span class="dot bg-warning" style="background-color:#EB984E; margin-left: 12px;"></span>
                      <span style="font-size: 12pt; padding-right: 13px;padding-left: 3px;">In Progress</span>
                      </div>
                      <div class="p-2">
                        <span class="dot bg-success" style="background-color:#58d68d; margin-left: 12px;"></span>
                      <span style="font-size: 12pt; padding-right: 13px;padding-left: 3px;">Completed</span>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-2 mb-0 mr-0 ml-0 p-0">
                    <div class="col-md-2 p-2">
                      <div class="card m-0" style="background-color:#D5D8DC; min-height: 800px;">
                        <div class="card-header" style="margin-top: -15px;">
                          <h5 class="card-title text-center font-weight-bold" style="font-size: 15px;">Unassigned Prod. Order(s)</h5>
                        </div>
                        <div class="card-body custom-sortable custom-sortable-connected overflow-auto" id="unassigned" style="height: 740px;">
                          @foreach ($production_machine_board['unassigned_production'] as $i => $row)
                          @php
                            if($row->status == 'Not Started'){
                              $b = 'secondary text-white';
                            }elseif($row->status == 'In Progress'){
                              $b = 'warning';
                            }else{
                              $b = 'success text-white';
                            }
                          @endphp
                          <div class="card bg-{{ $b }} view-production-order-details" data-production-order="{{ $row->production_order }}" data-position="{{ $i + 1 }}" data-card="unassigned">
                            <div class="card-body">
                              <div class="pull-right">
                                <span class="badge badge-primary badge-number" style="font-size: 9pt;"></span>
                              </div>
                              <span class="d-block font-weight-bold" style="font-size: 10pt;">{{ $row->production_order }} [{{ $row->sales_order }}{{ $row->material_request }}]</span>
                              <span class="d-block mt-1">{{ $row->item_code }} [{{ $row->qty_to_manufacture }} {{ $row->stock_uom }}]</span>
                              <span class="d-block" style="font-size: 9pt;">{{ strtok($row->description, ',') }}</span>
                            </div>
                          </div>
                          @endforeach
                        </div>
                      </div>
                    </div>
                    <div class="col-md-10 m-0 p-0">
                      <div class="scrolling-wrapper row flex-row flex-nowrap m-0 p-0" style="overflow-x: auto;">
                        @foreach ($production_machine_board['assigned_production_orders'] as $machine)
                        <div class="col-md-2 p-2" style="min-width: 350px !important;">
                          <div class="card m-0" style="background-color:#D5D8DC; min-height: 800px;">
                            <div class="card-header" style="margin-top: -15px;">
                              <h5 class="card-title text-center font-weight-bold" style="font-size: 15px;">{{ $machine['machine_name'] }}</h5>
                              <div class="pull-right p-1" style="margin-top: -40px;">
                                <img src="{{ asset('img/print.png') }}" width="25" class="print-schedule" data-machine="{{ $machine['machine_code'] }}">
                              </div>
                            </div>
                            <div class="card-body custom-sortable custom-sortable-connected overflow-auto" id="{{ $machine['machine_code'] }}" style="height: 740px;">
                              @foreach ($machine['production_orders'] as $j => $row)
                              @php
                                if($row->status == 'Not Started'){
                                  $b = 'secondary text-white';
                                }elseif($row->status == 'In Progress'){
                                  $b = 'warning';
                                }else{
                                  $b = 'success text-white';
                                }
                              @endphp
                              <div class="card bg-{{ $b }} view-production-order-details" data-production-order="{{ $row->production_order }}" data-position="{{ $j + 1 }}" data-card="{{ $row->machine_code }}">
                                <div class="card-body">
                                  <div class="pull-right">
                                    <span class="badge badge-primary badge-number" style="font-size: 9pt;">{{ $row->order_no }}</span>
                                  </div>
                                  <span class="d-block font-weight-bold" style="font-size: 10pt;">{{ $row->production_order }} [{{ $row->sales_order }}{{ $row->material_request }}]</span>
                                  <span class="d-block mt-1">{{ $row->item_code }} [{{ $row->qty_to_manufacture }} {{ $row->stock_uom }}]</span>
                                  <span class="d-block" style="font-size: 9pt;">{{ strtok($row->description, ',') }}</span>
                                </div>
                              </div>
                              @endforeach
                            </div>
                          </div>
                        </div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                  @endif
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
</style>

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

<div class="modal fade" id="mark-done-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="width: 30%;">
    <form action="/mark_as_done_task" method="POST" id="mark-done-frm">
      @csrf
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title">
            <span>Mark as Done</span>
            <span class="workstation-text font-weight-bold"></span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <h5 class="text-center">Do you want to override task?</h5>
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
<script>
  $(document).ready(function () {
    var scheduled_date = $('#schedule-date-val').val();
    var operation_name = $('#operation-name').val();
    var operation_id = $('#operation-id').val();

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
        }
      }); 
    }

    $(document).on('click', '#btn-print-schedule', function () {
      window.location.href = "/get_scheduled_production_order/" + operation_id + "/" + scheduled_date;
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
        var workstation_id = $('#jtname-modal .workstation-id').val();
        var jtid = $('#jtname-modal .job-ticket-id').val();
  
        $('#mark-done-modal input[name="id"]').val(jtid);
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

    $( ".custom-sortable" ).sortable({
			connectWith: ".custom-sortable-connected",
			appendTo: 'body',
			helper: 'clone',
			update:function(event, ui) {
				var card_id = this.id;
				$(this).children().each(function(index){
					if ($(this).attr('data-position') != (index + 1) || $(this).attr('data-card') != card_id) {
						$(this).attr('data-position', (index + 1)).attr('data-card', card_id).addClass('updated');
					}
				});
			
				var pos = [];
				$('.updated').each(function(){
					var production_order = $(this).attr('data-production-order');
					var order_no = $(this).attr('data-position');
					pos.push([production_order, order_no, card_id]);
					$(this).removeClass('updated');
				});

				if (pos) {
					$.ajax({
						url:"/update_conveyor_assignment",
						type:"POST",
						dataType: "text",
						data: {
							list: pos,
							scheduled_date: scheduled_date
						},
						success:function(data){
							console.log(data);
						},
						error: function(jqXHR, textStatus, errorThrown) {
							console.log(jqXHR);
							console.log(textStatus);
							console.log(errorThrown);
						}
					});
				}
			},
			receive: function(ev, ui) {
				update_badge_number('#' + this.id);
			}
		}).disableSelection();

		$(document).on('click', '.print-schedule', function(e){
			e.preventDefault();
			$('#frame-print').attr('src', '/assembly/print_machine_schedule/' + scheduled_date + '/' + $(this).data('machine'));
		});

		function update_badge_number(id){
			$(id).children().each(function(index){
				$(this).find('.badge-number').text( (index + 1));
			});
    }

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
  });
</script>
@endsection