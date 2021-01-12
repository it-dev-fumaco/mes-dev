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
              <h4 class="title text-center" style="margin-left: 20px; margin: auto 20pt;">Schedule Date : {{ $date_format }}</h4>
            </td>
            <td style="width: 50%">
              <h4 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Production Schedule Monitoring - Assembly</h4>
              <span class="title text-left d-block" style="font-size: 11pt;margin-left: 30px;">{{ Auth::user()->employee_name }}</span>
            </td>
          </tr>
        </table>
        <input type="hidden" name="date_today" id="date_today" value="{{ $date }}">
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="scheduled-date" value="{{ $scheduled_date }}">
<iframe id="frame-print" style="display: none;"></iframe>
<div class="content">
  <div class="row" style="margin-top: -70px;">
    <div class="col-md-12">
      <div style="height: 150px;">
        <div class="row">
          <div class="col-md-12" style="margin-top:-110px;">
            <div class="card">
              <div class="card-body pb-0">
                @php
                if ($date == date('Y-m-d')) {
                  $header = 'Schedule for Today';
                }elseif($date > date('Y-m-d')){
                  $header = 'Upcoming Schedule: ' . $date_format;
                }else{
                  $header = 'Schedule Date: ' . $date_format;
                }
                @endphp
                <ul class="nav nav-tabs" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="tab1" data-toggle="tab" href="#tab1-1" role="tab" aria-controls="tab1" aria-selected="true">{{ $header }}</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="tab2" data-toggle="tab" href="#tab2-2" role="tab" aria-controls="tab2" aria-selected="false">Machine Schedule</a>
                  </li>
                  {{--  <li class="nav-item">
                    <a class="nav-link" id="tab3" data-toggle="tab" href="#tab3-3" role="tab" aria-controls="tab3" aria-selected="false">Production Output</a>
                  </li>  --}}
                </ul>
                <div class="tab-content">
                  <div class="tab-pane active" id="tab1-1" role="tabpanel" aria-labelledby="tab1">
                    <div class="row" style="margin-top: 12px;">
                      <div class="col-md-8">
                        <div class="card p-0" style="background-color: #0277BD;">
                          <div class="card-body pb-0">
                            <div class="row" style="margin-top: -15px;">
                              <div class="col-md-6" style="padding: 5px 5px 5px 12px;">
                                <h5 class="text-white font-weight-bold text-left m-1">{{ $header }}</h5>
                              </div>
                              <div class="col-md-6 m-0" style="padding: 5px;">
                                <table style="width: 100%;">
                                  <tr>
                                    <td style="width: 50%;">
                                      <div class="form-group m-0">
                                        <input type="text" class="form-control" placeholder="Search" id="search_production_schedule">
                                      </div>
                                    </td>
                                    {{--  <td style="width: 40%;" class="text-center">
                                      <a href="/assembly/conveyor_assignment/{{ $scheduled_date }}" class="btn btn-primary">View Machine Schedule</a>
                                    </td>  --}}
                                    <td style="width: 7%;"><img style="float:right; margin-right:10px;" src="{{ asset('img/print_btn.png') }}" width="40" height="40" class="btn-print"></td>
                                  </tr>
                                </table>
                              </div>
                            </div>
                            <div class="row" style="background-color: #ffffff;height: auto; min-height: 400px;">
                              <div class="card card-nav-tabs card-plain">
                                <div class="col-md-12">
                                  <div id="table_orders" style="max-height: 400px; overflow:auto; min-height:400px;"></div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="card" style="background-color: #0277BD;">
                          <div class="card-body" style="padding-bottom: 0;">
                            <div class="row" style="margin-top: -15px;">
                              <div class="col-md-8" style="padding: 5px 5px 5px 12px;">
                                <h5 class="text-white font-weight-bold text-left" style="margin: 0; font-size: 12pt;">BACKLOG/S</h5>
                              </div>
                            </div>
                            <div class="row" style="background-color: #ffffff;height: auto; min-height: 265px;">
                              <div class="card card-nav-tabs card-plain">
                                <div class="col-md-12">
                                  <div id="backlogs_orders" style="max-height: 210px;overflow:auto;min-height:210px;"></div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="card" style="background-color: #0277BD;">
                          <div class="card-body pb-0">
                            <div class="row" style="margin-top: -15px;">
                              <div class="col-md-12" style="padding: 5px 5px 5px 12px; height: 45px;">
                                <h5 class="text-white font-weight-bold text-center m-0">Production Order </h5>
                              </div>
                            </div>
                            <div class="row" style="background-color: #ffffff;height: auto; min-height: 150px;">
                              <table style="width: 100%; margin-top:15px;" id="totals-table">
                                <tr>
                                  <td class="text-center align-top">
                                    <span class="span-value text-center" id="pending_count" style="display:block;font-size:35pt;font-weight:bold;">0</span>
                                    <span class="span-title text-center" style="display:block;font-size:10pt;">Production Order</span>
                                    <span class="span-title text-center" style="font-size:8pt;text-align:center; font-weight:bold;" id="pending_qty">0</span>
                                    <span class="span-title text-center" style="font-size:8pt;text-align:center;">PCS QTY</span>
                                  </td>
                                  <td class="text-center align-top">
                                    <span class="span-value text-center" id="inprogress_count" style="display:block;font-size:35pt;font-weight:bold;">0</span>
                                    <span class="span-title text-center" style="display:block;font-size:10pt;">In Progress</span>
                                    <span class="span-title text-center" style="font-size:8pt;text-align:center;font-weight:bold;" id="inprogress_qty">0</span>
                                    <span class="span-title text-center" style="font-size:8pt;text-align:center;">PCS QTY</span>
                                  </td>
                                  <td class="align-top">
                                    <span class="span-value text-center" id="reject_count" style="display:block;font-size:35pt;font-weight:bold;">0</span>
                                    <span class="span-title text-center" style="display:block;font-size:10pt;">Rejects</span>
                                  </td>
                                  <td class="text-center align-top">
                                    <span class="span-value text-center" id="completed_count" style="display:block;font-size:35pt;font-weight:bold;">0</span>
                                    <span class="span-title text-center" style="display:block;font-size:10pt;">Ready For Transfer</span>
                                    <span class="span-title text-center" style="font-size:8pt;text-align:center;font-weight:bold;" id="totransfer_qty">0</span>
                                    <span class="span-title text-center" style="font-size:8pt;text-align:center;">PCS QTY</span>
                                  </td>
                                </tr>
                              </table>
                            </div>
                          </div>
                        </div>
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                          <li class="nav-item">
                            <a class="nav-link active" id="feedback-tab" data-toggle="tab" href="#feedbacked" role="tab" aria-controls="open" aria-selected="true">Feedbacked PO</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" id="reject-tab" data-toggle="tab" href="#reject" role="tab" aria-controls="open-mreq" aria-selected="false">Reject</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" id="done-tab" data-toggle="tab" href="#done" role="tab" aria-controls="done" aria-selected="false">Activity Logs</a>
                          </li>
                        </ul>
                        <div class="tab-content">
                          <div class="tab-pane active" id="feedbacked" role="tabpanel" aria-labelledby="feedbacked-tab">
                            <div class="row" style="margin-top: 12px;">
                              <div class="col-md-12">
                                <div class="card">
                                  <div class="card-header" style="background-color: #0277BD;">
                                    <div class="row" style="margin-top: -15px;">
                                      <div class="col-md-6" style="padding: 7px 5px 6px 12px;">
                                        <h5 class="text-white font-weight-bold align-middle" style="font-size:12pt; margin: 0;">Feedbacked PO</h5>
                                      </div>
                                      <div class="col-md-6" style="padding: 3px;">
                                        <div class="form-group" style="margin: 0;">
                                          <input type="text" class="form-control" placeholder="Search" id="search-cancelled-prod" style="background-color: white; padding: 6px 8px;">
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="card-body" style="min-height:505px; max-height:505px;">
                                    <div id="tbl-feedbacked-po"></div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="tab-pane" id="reject" role="tabpanel" aria-labelledby="open-tab">
                            <div class="row" style="margin-top: 12px;">
                              <div class="col-md-12">
                                <div class="card">
                                  <div class="card-header" style="background-color: #C62828;">
                                    <div class="row" style="margin-top: -15px;">
                                      <div class="col-md-6" style="padding: 7px 5px 6px 12px;">
                                        <h5 class="text-white font-weight-bold align-middle" style="font-size:12pt; margin: 0;">Reject(s)</h5>
                                      </div>
                                      <div class="col-md-6" style="padding: 3px;">
                                        <div class="form-group" style="margin: 0;">
                                          <input type="text" class="form-control search-open-prod" data-type="SO" placeholder="Search" style="background-color: white; padding: 6px 8px;">
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="card-body" style="min-height:505px;max-height:505px;">
                                    <div id="tbl-reject-po"></div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="tab-pane" id="done" role="tabpanel" aria-labelledby="done-tab">
                            <div class="row" style="margin-top: 12px;">
                              <div class="col-md-12">
                                <div class="card">
                                  <div class="card-header" style="background-color: #229954;">
                                    <div class="row" style="margin-top: -15px;">
                                      <div class="col-md-6" style="padding: 7px 5px 6px 12px;">
                                        <h5 class="text-white font-weight-bold align-middle" style="font-size:12pt; margin: 0;">Activity Logs</h5>
                                      </div>
                                      <div class="col-md-6" style="padding: 3px;">
                                        <div class="form-group" style="margin: 0;">
                                          <input type="text" class="form-control search-feedback-prod" placeholder="Search" style="background-color: white; padding: 6px 8px;">
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="card-body" style="min-height:505px;max-height:505px;">
                                    <div id="for-feedback-production-div"></div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane" id="tab2-2" role="tabpanel" aria-labelledby="tab2">
                    <style>
                      .dot {
                      height: 15px;
                      width: 15px;
                      border-radius: 50%;
                      display: inline-block;
                    }</style>
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
                    <div class="row m-0 p-0">
                      <div class="col-md-2 p-2">
                       <div class="card m-0" style="background-color:#D5D8DC; min-height: 800px;">
                         <div class="card-header" style="margin-top: -15px;">
                           <h5 class="card-title text-center" style="font-size: 15px;"><b>Unassigned Prod. Order(s)</b></h5>
                         </div>
                         <div class="card-body custom-sortable custom-sortable-connected overflow-auto" id="unassigned" style="height: 740px;">
                           @foreach ($unassigned_production as $i => $row)
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
                         @foreach ($assigned_production_orders as $machine)
                         <div class="col-md-2 p-2" style="min-width: 350px !important;">
                           <div class="card m-0" style="background-color:#D5D8DC; min-height: 800px;">
                             <div class="card-header" style="margin-top: -15px;">
                               <h5 class="card-title text-center" style="font-size: 15px;"><b>{{ $machine['machine_name'] }}</b></h5>
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
                  
                  </div>
                  <div class="tab-pane" id="tab3-3" role="tabpanel" aria-labelledby="tab3">
                    jlsbhafakjsf
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
       
  <div class="modal fade" id="addnotes-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
      <form action="/addnotes_task" method="POST" id="addnotes-frm">
        @csrf
        <div class="modal-content">
          <div class="modal-header text-white" style="background-color: #0277BD; padding: 5px 8px;">
            <h5 class="modal-title" id="modal-title">
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
          <div class="modal-footer" style="padding: 5px 8px;">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Confirm</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="mark-done-modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document" style="width: 30%;">
          <form action="/mark_as_done_task_assembly" method="POST" id="mark-done-frm">
              @csrf
              <div class="modal-content">
                  <div class="modal-header text-white"
                      style="background-color: #0277BD; padding: 5px 8px;">
                      <h5 class="modal-title" id="modal-title">
                          <span>Mark as Done</span>
                          <span class="workstation-text" style="font-weight: bolder;"></span></h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <div class="row">
                          <div class="col-md-12">
                              <h5 class="text-center" style="font-size: 12pt; margin: 8px;">Do you want to
                                  override task?</h5>
                              <input type="hidden" name="prod" required id="prod">

                          </div>
                      </div>
                  </div>
                  <div class="modal-footer" style="padding: 5px 8px;">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-primary">Confirm</button>
                  </div>
              </div>
          </form>
      </div>
  </div>

  <div class="modal fade" id="editcpt-modal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-md" role="document">
          <form action="/edit_cpt_status_qty" method="POST" id="edit-cpt-status-qty-frm">
              @csrf
              <div class="modal-content">
                  <div class="modal-header text-white"
                      style="background-color: #0277BD; padding: 5px 8px;">
                      <h5 class="modal-title" id="modal-title">
                          <span>Edit</span>
                          <span class="prod-text" style="font-weight: bolder;"></span></h5>
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
                              <input type="text" class="form-control" name="loading_cpt"
                                  placeholder="Loading Completed Qty" required id="loading_cpt">
                          </div>
                          <div class="form-group">
                              <label><b>Loading Status:</b></label>
                              <select class="form-control sel4" name="loading_status" id="loading_status"
                                  required>
                                  <option value="Pending">Pending</option>
                                  <option value="In Progress">In Progress</option>
                                  <option value="Completed">Completed</option>
                              </select>
                          </div>
                          <div class="form-group">
                              <label><b>Unloading Completed Qty:</b></label>
                              <input type="text" class="form-control" name="unloading_cpt"
                                  placeholder="Unloading Completed Qty" required id="unloading_cpt">
                          </div>
                          <div class="form-group">
                              <label><b>Unloading Status:</b></label>
                              <select class="form-control sel4" name="unloading_status"
                                  id="unloading_status" required>
                                  <option value="Pending">Pending</option>
                                  <option value="In Progress">In Progress</option>
                                  <option value="Completed">Completed</option>
                              </select>
                          </div>
                          <div class="form-group">
                              <label><b>Completed Qty:</b></label>
                              <input type="text" class="form-control" name="cpt_overall"
                                  placeholder="Completed Qty" required id="cpt_overall">
                          </div>
                          <div class="form-group">
                              <label><b>Status:</b></label>
                              <select class="form-control sel4" name="status_overall" id="status_overall"
                                  required>
                                  <option value="Not Started">Not Started</option>
                                  <option value="In Progress">In Progress</option>
                                  <option value="Completed">Completed</option>
                              </select>
                          </div>

                      </div>
                  </div>
                  <div class="modal-footer" style="padding: 5px 8px;">
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
          <h5 class="modal-title" id="modal-title">
            <span>Process</span>
            <span class="sampling-delete-text" style="font-weight: bolder;"></span>
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
  
  <!-- Modal Confirm Feedback Production Order -->
  <div class="modal fade" id="confirm-feedback-production-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="min-width: 55%;">
      <form action="#" method="POST">
        @csrf
        <div class="modal-content">
          <div class="modal-header text-white" style="background-color: #0277BD;">
            <h5 class="modal-title">Production Order Feedback</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="production_order">
            <div class="row">
              <div class="col-md-12">
                <div id="feedback-production-items"></div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="moved-today-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width: 30%;">
      <form action="/move_today_task_assembly" method="POST" id="move-today-frm">
          @csrf
          <div class="modal-content">
          <div class="modal-header text-white" style="background-color: #0277BD; padding: 5px 8px;">
            <h5 class="modal-title" id="modal-title">
              <span>Confirmation</span>
              <span class="workstation-text" style="font-weight: bolder;"></span>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                  <h5 class="text-center" style="font-size: 12pt;">Are you sure you want to move today the task?</h5>
                  <input type="hidden" name="prod_moved_today" id="prod_moved_today">
                  <input type="hidden" name="prod_date_today" id="prod_date_today">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Confirm</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <style type="text/css">
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

    <blade keyframes|%20blinker%20%7B%0D>from {
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
  </style>
@endsection

                @section('script')
            

                <script>
                    $(document).ready(function () {

                      var scheduled_date = $('#scheduled-date').val();
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




                        get_feedbacked_po();

                        function get_feedbacked_po(page, query) {
                            var date_sched = $("#date_today").val();
                            $.ajax({
                                url: "/get_feedbacked_production_order_assembly/" + date_sched +
                                    "/?page=" + page,
                                type: "GET",
                                data: {
                                    search_string: query
                                },
                                success: function (data) {
                                    $('#tbl-feedbacked-po').html(data);
                                }
                            });
                        }

                        get_reject_po();

                        function get_reject_po(page, query) {
                            var date_sched = $("#date_today").val();
                            $.ajax({
                                url: "/get_reject_assembly_production_order/" + date_sched + "/?page=" +
                                    page,
                                type: "GET",
                                data: {
                                    search_string: query
                                },
                                success: function (data) {
                                    $('#tbl-reject-po').html(data);
                                }
                            });
                        }

                        $(document).on('click', '.create-feedback-btn', function(e){
                          e.preventDefault();
                      
                          $('#submit-feedback-btn').removeAttr('disabled');
                          var production_order = $(this).data('production-order');
                          $('#confirm-feedback-production-modal input[name="production_order"]').val(production_order);
                          get_pending_material_transfer_for_manufacture(production_order);
                      
                          $('#confirm-feedback-production-modal').modal('show');
                        });

                        function get_pending_material_transfer_for_manufacture(production_order){
                          $.ajax({
                            url:"/get_pending_material_transfer_for_manufacture/" + production_order,
                            type:"GET",
                            success:function(response){
                              $('#feedback-production-items').html(response);
                              
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                              console.log(jqXHR);
                              console.log(textStatus);
                              console.log(errorThrown);
                            }
                          });
                        }
                    
                         $('#confirm-feedback-production-modal form').submit(function(e){
                        e.preventDefault();
                        $('#submit-feedback-btn').attr('disabled', true);
                        $('#loader-wrapper').removeAttr('hidden');
                        var production_order = $('#confirm-feedback-production-modal input[name="production_order"]').val();
                        var target_warehouse = $('#confirm-feedback-production-modal input[name="target_warehouse"]').val();
                        var completed_qty = $('#confirm-feedback-production-modal input[name="completed_qty"]').val();
                    
                        $.ajax({
                          url:"/create_stock_entry/" + production_order,
                          type:"POST",
                          data: {fg_completed_qty: completed_qty, target_warehouse: target_warehouse},
                          success:function(response){
                            $('#loader-wrapper').attr('hidden', true);
                            if (response.success == 0) {
                              showNotification("danger", response.message, "now-ui-icons travel_info");
                            }else{
                              showNotification("success", response.message, "now-ui-icons travel_info");
                              $('#confirm-feedback-production-modal').modal('hide');
                            }
                          },
                          error: function(jqXHR, textStatus, errorThrown) {
                            console.log(jqXHR);
                            console.log(textStatus);
                            console.log(errorThrown);
                          }
                        });
                      });
                        table_po_orders();
                        table_backlogs_po_orders();
                        count_current_production();
                        // setInterval(notif_dashboard, 7000);
                        notif_dashboard();

                        var speed = {
                            'trigger': '#speed-compare',
                            'target': '.modal_content .speedometer'
                        };

                        $(function () {
                            $(speed.trigger).on('click', function () {
                                setTimeout(function () {
                                    $(speed.target).each(function () {
                                        $(this).addClass('play');
                                    });
                                }, 1000);
                            });
                        });

                        setInterval(updateClock, 1000);

                        function updateClock() {
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
                            var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds +
                                " " + timeOfDay;

                            $("#current-time").html(currentTimeString);
                        }
                    });

                </script>
                <script>
                    function defer(method) {
                        if (window.jQuery)
                            method();
                        else
                            setTimeout(function () {
                                defer(method)
                            }, 50);
                    }
                    defer(function () {
                        (function ($) {

                            function doneResizing() {
                                var totalScroll = $('.resource-slider-frame').scrollLeft();
                                var itemWidth = $('.resource-slider-item').width();
                                var difference = totalScroll % itemWidth;
                                if (difference !== 0) {
                                    $('.resource-slider-frame').animate({
                                        scrollLeft: '-=' + difference
                                    }, 500, function () {
                                        // check arrows
                                        checkArrows();
                                    });
                                }
                            }

                            function checkArrows() {
                                var totalWidth = $('#resource-slider .resource-slider-item').length * $(
                                    '.resource-slider-item').width();
                                var frameWidth = $('.resource-slider-frame').width();
                                var itemWidth = $('.resource-slider-item').width();
                                var totalScroll = $('.resource-slider-frame').scrollLeft();

                                if (((totalWidth - frameWidth) - totalScroll) < itemWidth) {
                                    $(".next").css("visibility", "hidden");
                                } else {
                                    $(".next").css("visibility", "visible");
                                }
                                if (totalScroll < itemWidth) {
                                    $(".prev").css("visibility", "hidden");
                                } else {
                                    $(".prev").css("visibility", "visible");
                                }
                            }

                            $('.arrow').on('click', function () {
                                var $this = $(this),
                                    width = $('.resource-slider-item').width(),
                                    speed = 500;
                                if ($this.hasClass('prev')) {
                                    $('.resource-slider-frame').animate({
                                        scrollLeft: '-=' + width
                                    }, speed, function () {
                                        // check arrows
                                        checkArrows();
                                    });
                                } else if ($this.hasClass('next')) {
                                    $('.resource-slider-frame').animate({
                                        scrollLeft: '+=' + width
                                    }, speed, function () {
                                        // check arrows
                                        checkArrows();
                                    });
                                }
                            }); // end on arrow click

                            $(window).on("load resize", function () {
                                checkArrows();
                                $('#resource-slider .resource-slider-item').each(function (i) {
                                    var $this = $(this),
                                        left = $this.width() * i;
                                    $this.css({
                                        left: left
                                    })
                                }); // end each
                            }); // end window resize/load

                            var resizeId;
                            $(window).resize(function () {
                                clearTimeout(resizeId);
                                resizeId = setTimeout(doneResizing, 500);
                            });

                        })(jQuery); // end function
                    });

                </script>

                <script type="text/javascript">
                    function table_po_orders(page, query) {
                        var date_sched = $("#date_today").val();
                        $.ajax({
                            url: "/get_production_sched_assembly/" + date_sched + "/?page=" + page,
                            type: "GET",
                            data: {
                                search_string: query
                            },
                            success: function (data) {
                                $('#table_orders').html(data);
                            }
                        });
                    }

                    function table_backlogs_po_orders() {
                        var date_sched = $("#date_today").val();
                        $.ajax({
                            url: "/get_production_sched_assembly_backlog/" + date_sched,
                            type: "GET",
                            success: function (data) {
                                $('#backlogs_orders').html(data);
                            }
                        });
                    }

                </script>
                <script type="text/javascript">
                    function count_current_production() {
                        var date_today = $("#date_today").val();
                        $.ajax({
                            url: "/count_current_assembly_production_schedule_monitoring/" + date_today,
                            type: "GET",
                            success: function (data) {
                                $('#pending_count').text(data.pending);
                                $('#inprogress_count').text(data.inProgress);
                                $('#completed_count').text(data.completed);
                                $('#reject_count').text(data.reject);
                                $('#pending_qty').text(data.qty_pending);
                                $('#inprogress_qty').text(data.qty_inprogress);
                                $('#totransfer_qty').text(data.qty_completed);
                                console.log(data);
                            }
                        });
                    }

                </script>
                <script type="text/javascript">
                    function notif_dashboard() {
                        $.ajax({
                            url: "/get_painting_notif_dashboard",
                            type: "GET",
                            success: function (data) {
                                $('#tbl_notif_dash').html(data);
                            }
                        });
                    }

                </script>
                <script>
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $(document).on('click', '.btn-hide', function (e) {

                        e.preventDefault();
                        var from_table = $(this).attr('data-frmtable')
                        var data = {
                            timelog_id: $(this).attr('data-timelogid'),
                            frm_table: $(this).attr('data-frmtable')
                        }
                        if (from_table == "machine") {

                        } else {
                            $.ajax({
                                url: "/hide_reject",
                                type: "post",
                                data: data,
                                success: function (response) {
                                    if (response.success > 0) {
                                        notif_dashboard();
                                        showNotification("success", response.message,
                                            "now-ui-icons ui-1_check");
                                    } else {
                                        showNotification("danger", response.message,
                                            "now-ui-icons travel_info");
                                    }
                                    // alert(response.message);
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    console.log(jqXHR);
                                    console.log(textStatus);
                                    console.log(errorThrown);
                                }
                            });
                        }

                    });
                    // $('#table_id tr').click(function(){
                    //     $(this).remove();
                    //     return false;
                    // });
                    // $("#table_id").delegate(".btn-hide", "click", function() {
                    //   $(this).closest("tr").hide();
                    // });

                </script>
                <script type="text/javascript">
                    $(document).on('click', '.btn-movedtoday', function () {
                        var id = $(this).attr('data-prod');
                        var date = $('#date_today').val();
                        $('#moved-today-modal #prod_moved_today').val(id);
                        $('#moved-today-modal #prod_date_today').val(date);
                        $('#moved-today-modal').modal('show');

                    });

                </script>
                <script type="text/javascript">
                    $(document).on('click', '.addnotes', function () {
                        var id = $(this).attr('data-prod');
                        var remarks = $(this).attr('data-remarks');
                        $('#addnotes-modal #prod_no').val(id);
                        $('#addnotes-modal #remarks_field').val(remarks);
                        $('#addnotes-modal').modal('show');

                    });

                </script>
                <script type="text/javascript">
                    $('#move-today-frm').submit(function (e) {
                        e.preventDefault();
                        var url = $(this).attr('action');
                        $.ajax({
                            url: url,
                            type: "POST",
                            data: $(this).serialize(),
                            success: function (data) {
                                if (data.success) {
                                    showNotification("success", data.message,
                                        "now-ui-icons ui-1_check");
                                    $('#moved-today-modal').modal('hide');
                                    table_po_orders();
                                    table_backlogs_po_orders();
                                } else {
                                    showNotification("danger", data.message,
                                        "now-ui-icons travel_info");
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
                    $(document).on('keyup', '#search_production_schedule', function () {
                        var query = $(this).val();
                        table_po_orders(1, query);
                    });

                </script>
                <script type="text/javascript">
                    $('#addnotes-frm').submit(function (e) {
                        e.preventDefault();
                        var url = $(this).attr('action');
                        $.ajax({
                            url: url,
                            type: "POST",
                            data: $(this).serialize(),
                            success: function (data) {
                                if (data.success) {
                                    showNotification("success", data.message,
                                        "now-ui-icons ui-1_check");
                                    $('#addnotes-modal').modal('hide');
                                    table_po_orders();
                                } else {
                                    showNotification("danger", data.message,
                                        "now-ui-icons travel_info");
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

                </script>
                <script type="text/javascript">
                    $('#mark-done-frm').submit(function (e) {
                        e.preventDefault();
                        var url = $(this).attr('action');
                        $.ajax({
                            url: url,
                            type: "POST",
                            data: $(this).serialize(),
                            success: function (data) {
                                if (data.success) {
                                    showNotification("success", data.message,
                                        "now-ui-icons ui-1_check");
                                    $('#mark-done-modal').modal('hide');
                                    $('#jtname-modal').modal("hide");
                                    $('#view-machine-task-modal').modal("hide");
                                    table_po_orders();
                                    count_current_production();
                                } else {
                                    showNotification("danger", data.message,
                                        "now-ui-icons travel_info");
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

                </script>
                <script type="text/javascript">
                    $(document).on('click', '.mark-done-btn', function () {

                        var prod = $(this).attr('data-prod');

                        $('#mark-done-modal #prod').val(prod);
                        $('#mark-done-modal .workstation-text').text('[' + prod + ']');
                        $('#mark-done-modal').modal('show');

                    });

                </script>
                <script>
                    function getJtDetails(jtno){
                      $('#process-bc').empty();
                      $('#jt-details-tbl tbody').empty();
                      $.ajax({
                      url:"/get_jt_details/" + jtno,
                      type:"GET",
                      success:function(data){
                        if (data.success < 1) {
                          showNotification("danger", data.message, "now-ui-icons travel_info");
                        }else{
                          $('#production-search-content').html(data);
                          $('#jt-workstations-modal').modal('show');
                        }
                      }
                      });
                    }

                </script>
                <script>
                    $(document).on('click', '.btn-print', function () {
                        // var tryval = $('#tryme').val();
                        var date = $("#date_today").val();
                        window.location.href = "/print_production_sched_assembly/" + date;
                    });

                </script>
                <script type="text/javascript">
                    $(document).on('click', '.editcpt_qty', function () {
                        var prod = $(this).attr('data-prod');
                        var qty = $(this).attr('data-qty');


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

                </script>
                <script type="text/javascript">
                    $('#edit-cpt-status-qty-frm').submit(function (e) {
                        e.preventDefault();
                        var url = $(this).attr('action');
                        var qty_to_manufacture = $('#qty_validation').val();
                        var loading = $('#loading_cpt').val();
                        var unloading = $('#unloading_cpt').val();
                        var overall_cpt = $('#cpt_overall').val();

                        if (qty_to_manufacture < loading) {
                            showNotification("danger", "Loading qty must not be greater than " +
                                qty_to_manufacture + '', "now-ui-icons travel_info");
                            return false;
                        } else if (qty_to_manufacture < unloading) {
                            showNotification("danger", "Unloading qty must not be greater than " +
                                qty_to_manufacture + '', "now-ui-icons travel_info");
                            return false;
                        } else if (qty_to_manufacture < overall_cpt) {
                            showNotification("danger", "Completed qty must not be greater than " +
                                qty_to_manufacture + '', "now-ui-icons travel_info");
                            return false;
                        } else {
                            $.ajax({
                                url: url,
                                type: "POST",
                                data: $(this).serialize(),
                                success: function (data) {
                                    if (data.success) {
                                        showNotification("success", data.message,
                                            "now-ui-icons ui-1_check");
                                        $('#editcpt-modal').modal('hide');
                                        table_po_orders();
                                        count_current_production();
                                    } else {
                                        showNotification("danger", data.message,
                                            "now-ui-icons travel_info");
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

                </script>
                <script>
                    $(document).on('click', '.view-process-qty-btn', function () {
                        var prod = $(this).attr('data-prod');

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
                            },
                        });

                    });

                </script>
                @endsection
