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
                      <h3 class="title text-left p-0 ml-3" style="margin: auto 20pt;">QA Reports</h3>
                   </td>
                </tr>
             </table>
          </div>
       </div>
    </div>
 </div>
 <br>
 <div class="content">
    <div class="row" style="margin-top: -145px;">
      <div class="col-md-12">
        <ul class="nav nav-tabs" role="tablist" id="qa-dashboard-tabs">
          <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="false">Inspection Logsheet Report</a>
          </li>
        </ul>
        <div class="tab-content" style="min-height: 500px;">
          
          <div class="tab-pane active" id="tab1" role="tabpanel" aria-labelledby="tab1">
            <div class="row">
              <div class="col-md-12">
                <div class="card" style="border-radius: 0 0 3px 3px;">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="card" style="background-color: whitesmoke">
                          <div class="card-body">
                            <div class="row">
                              <div class="col-md-12" style="padding: 0;margin-top: -50px;">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                  <li class="nav-item">
                                    <a class="nav-link active" id="fabrication-tab" data-toggle="tab" href="#fabrication" role="tab" aria-controls="fabrication" aria-selected="true">Fabrication</a>
                                  </li>
                                  <li class="nav-item">
                                    <a class="nav-link" id="painting-tab" data-toggle="tab" href="#painting" role="tab" aria-controls="painting" aria-selected="false"> Painting</a>
                                  </li>
                                  <li class="nav-item">
                                    <a class="nav-link" id="assembly-tab" data-toggle="tab" href="#assembly" role="tab" aria-controls="assembly" aria-selected="false">Assembly</a>
                                  </li>
                                  
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content">
                                  <div class="tab-pane active" id="fabrication" role="tabpanel" aria-labelledby="fabrication">
                                    <div class="row" style="margin-top: 12px;">
                                      <div class="col-md-2">
                                        <div class="card" style="background-color: #0277BD;" >
                                          <div class="card-body" style="padding-bottom: 0;">
                                            <div class="row">
                                              <div class="col-md-8" style="margin-top: -10px;">
                                                <h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Filter/s</h5>
                                              </div>
                                              <div class="col-md-4" style="margin-top: -20px;">
                                                <button type="button" class="btn btn-default" id="clear-button-fab" style="margin-bottom:5px;font-size: 12px;padding: 7px 9px 6px 10px;float: right;">Clear</button>
                                              </div>
                                            </div>
                                            <div class="row" style="background-color: #ffffff; padding-top: 9px;">
                                              <div class="col-md-12" style="margin: 0;height: 780px;" id="filter_qa_inspection_fabrication">
                                                <span style="display: block; font-size: 9pt; margin-top: -8px;"></span>
                                                  <div class="form-group">
                                                    <label style="color: black;">Date Range:</label>
                                                    <input type="text" class="date form-control" name="daterange" autocomplete="off" placeholder="Select Date From" id="daterange_fabrication" value="" style="text-align:center;display:inline-block;width:100%;height:30px;" >
                                                  </div>
                                                  <div class="form-group margin-top">
                                                        <label style="color: black;">Workstation</label>
                                                        <select class="form-control text-center sel6" name="workstation" id="workstation">
                                                            <option value=""> Select Workstation</option>
                                                            @foreach($fab_workstation as $row)
                                                            <option value="{{$row->workstation_name}}">{{$row->workstation_name}}</option>
                                                            @endforeach
                                                          </select>
                                                     </div>
                                                    <div class="form-group margin-top">
                                                        <label style="color: black;">Item Code</label>
                                                        <select class="form-control text-center sel6 " name="item_code" id="item_code">
                                                            <option value=""> Select Item Code</option>
                                                            @foreach($item_code as $row)
                                                            <option value="{{$row->item_code}}">{{$row->item_code}}</option>
                                                            @endforeach
                                                          </select>
                                                     </div>
  
                                                    <div class="form-group margin-top">
                                                        <label style="color: black;">Production Order</label>
                                                         <select class="form-control text-center sel6 " name="prod" id="prod">
                                                            <option value=""> Select Production Order</option>
                                                            @foreach($production_order as $row)
                                                            <option value="{{$row->production_order}}">{{$row->production_order}}</option>
                                                            @endforeach
                                                          </select>
                                                     </div>
                                                    <div class="form-group margin-top">
                                                        <label style="color: black;">Customer</label>
                                                        <select class="form-control text-center sel6 " name="customer" id="customer">
                                                            <option value=""> Select Customer</option>
                                                            @foreach($customer as $row)
                                                            <option value="{{$row->customer}}">{{$row->customer}}</option>
                                                            @endforeach
                                                          </select>
                                                     </div>
                                                     
                                                     <div class="form-group margin-top">
                                                        <label style="color: black;">Process</label>
                                                        <select class="form-control text-center sel6 " name="process" id="process">
                                                          <option value=""> Select Process</option>
                                                           @foreach($fab_process as $row)
                                                            <option value="{{$row->process_name}}">{{$row->process_name}}</option>
                                                            @endforeach
                                                          </select>
                                                     </div>
                                                     <div class="form-group margin-top">
                                                        <label style="color: black;">QC Status</label>
                                                        <select class="form-control text-center sel6 " name="qa_status" id="qa_status">
                                                            <option value="">Select Status</option>
                                                            <option value="For Confirmation">For Confirmation</option>
                                                            <option value="QC Passed">QC Passed</option>
                                                            <option value="QC Failed">QC Failed</option>
                                                          </select>
                                                     </div>
                                                     <div class="form-group margin-top">
                                                        <label style="color: black;">QC Inspector</label>
                                                        <select class="form-control text-center sel6 " name="qa_inspector" id="qa_inspector">
                                                            <option value="">Select QC Inspector</option>
                                                            @foreach($qc_name as $row)
                                                            <option value="{{$row['user_id']}}">{{$row['name']}}</option>
                                                            @endforeach
                                                          </select>
                                                     </div>
                                                     <div class="form-group margin-top">
                                                        <label style="color: black;">Operator</label>
                                                        <select class="form-control text-center sel6 " name="operator" id="operator">
                                                            <option value="">Select Operator</option>
                                                            @foreach($operators as $row)
                                                            <option value="{{$row->user_id}}">{{$row->employee_name}}</option>
                                                            @endforeach
                                                          </select>
                                                     </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-10">
                                        <div class="card" style="">
                                          <div class="card-body" style="padding-bottom: 0;">
                                              {{--<div class="col-md-4">
                                                <img style="float:right;" src="{{ asset('img/export.png') }}" id="fabrication-btn-export" width="40" height="40" class="btn-export">
  
                                              </div>--}}
                                            <div class="col-md-12" style="margin:0px; padding:0px;">
                                              <div class="tableFixHead" id="tbl_log_fabrication" style="width: 100%;overflow: auto;max-height: 750px;min-height:750px;"></div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="tab-pane" id="painting" role="tabpanel" aria-labelledby="painting">
                                    <div class="row" style="margin-top: 12px;">
                                      <div class="col-md-2">
                                        <div class="card" style="background-color: #0277BD;" >
                                          <div class="card-body" style="padding-bottom: 0;">
                                            <div class="row">
                                              <div class="col-md-8" style="margin-top: -10px;">
                                                <h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Filter/s</h5>
                                              </div>
                                              <div class="col-md-4" style="margin-top: -20px;">
                                                <button type="button" class="btn btn-default" id="clear-button-painting" style="margin-bottom:5px;font-size: 12px;padding: 7px 9px 6px 10px;float: right;">Clear</button>
                                              </div>
                                            </div>
                                            <div class="row" style="background-color: #ffffff; padding-top: 9px;">
                                              <div class="col-md-12" style="margin: 0;height: 780px;" id="filter_qa_inspection_painting">
                                                <span style="display: block; font-size: 9pt; margin-top: -8px;"></span>
                                                <div class="form-group">
                                                    <label style="color: black;">Date Range:</label>
                                                    <input type="text" class="date form-control" name="daterange_painting" autocomplete="off" placeholder="Select Date From" id="daterange_painting" value="" style="text-align:center;display:inline-block;width:100%;height:30px;" onchange="tbl_log_fabrication()">
                                                  </div>
                                                  <div class="form-group margin-top">
                                                        <label style="color: black;">Workstation</label>
                                                        <select class="form-control text-center sel3" name="workstation_painting" id="workstation_painting" onchange="tbl_log_fabrication()">
                                                            <option value=""> Select Workstation</option>
                                                            @foreach($pain_workstation as $row)
                                                            <option value="{{$row->workstation_name}}">{{$row->workstation_name}}</option>
                                                            @endforeach
                                                          </select>
                                                     </div>
                                                    <div class="form-group margin-top">
                                                        <label style="color: black;">Item Code</label>
                                                        <select class="form-control text-center sel3 " name="item_code_painting" id="item_code_painting">
                                                            <option value=""> Select Item Code</option>
                                                            @foreach($item_code as $row)
                                                            <option value="{{$row->item_code}}">{{$row->item_code}}</option>
                                                            @endforeach
                                                          </select>
                                                     </div>
  
                                                    <div class="form-group margin-top">
                                                        <label style="color: black;">Production Order</label>
                                                         <select class="form-control text-center sel3 " name="prod_painting" id="prod_painting">
                                                            <option value=""> Select Production Order</option>
                                                            @foreach($production_order as $row)
                                                            <option value="{{$row->production_order}}">{{$row->production_order}}</option>
                                                            @endforeach
                                                          </select>
                                                     </div>
                                                    <div class="form-group margin-top">
                                                        <label style="color: black;">Customer</label>
                                                        <select class="form-control text-center sel3 " name="customer_painting" id="customer_painting">
                                                            <option value=""> Select Customer</option>
                                                            @foreach($customer as $row)
                                                            <option value="{{$row->customer}}">{{$row->customer}}</option>
                                                            @endforeach
                                                          </select>
                                                     </div>
                                                     
                                                     <div class="form-group margin-top">
                                                        <label style="color: black;">Process</label>
                                                        <select class="form-control text-center sel3 " name="process_painting" id="process_painting">
                                                          <option value=""> Select Process</option>
                                                           @foreach($process_painting as $row)
                                                            <option value="{{$row->process_name}}">{{$row->process_name}}</option>
                                                            @endforeach
                                                          </select>
                                                     </div>
                                                     <div class="form-group margin-top">
                                                        <label style="color: black;">QC Status</label>
                                                        <select class="form-control text-center sel3 " name="qa_status_painting" id="qa_status_painting">
                                                            <option value="">Select Status</option>
                                                            <option value="For Confirmation">For Confirmation</option>
                                                            <option value="QC Passed">QC Passed</option>
                                                            <option value="QC Failed">QC Failed</option>
                                                          </select>
                                                     </div>
                                                     <div class="form-group margin-top">
                                                        <label style="color: black;">QC Inspector</label>
                                                        <select class="form-control text-center sel3 " name="qa_inspector_painting" id="qa_inspector_painting">
                                                            <option value="">Select QC Inspector</option>
                                                            @foreach($qc_name as $row)
                                                            <option value="{{$row['user_id']}}">{{$row['name']}}</option>
                                                            @endforeach
                                                          </select>
                                                     </div>
                                                     <div class="form-group margin-top">
                                                        <label style="color: black;">Operator</label>
                                                        <select class="form-control text-center sel3 " name="operator_painting" id="operator_painting">
                                                            <option value="">Select Operator</option>
                                                            @foreach($operators as $row)
                                                            <option value="{{$row->user_id}}">{{$row->employee_name}}</option>
                                                            @endforeach
                                                          </select>
                                                     </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-10">
                                        <div class="card" >
                                          <div class="card-body" style="padding-bottom: 0;">
                                            <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;max-height: 750px;">
                                              
                                                  <div class="col-md-12" style="max-height: 750px;">
                                                  <div id="tbl_log_painting" style="width: 100%;overflow: auto;min-height: 750px;max-height: 750px;"></div>
                                                  </div>
                                               
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="tab-pane" id="assembly" role="tabpanel" aria-labelledby="assembly">
                                    <div class="row" style="margin-top: 12px;">
                                      <div class="col-md-2">
                                        <div class="card" style="background-color: #0277BD;" >
                                          <div class="card-body" style="padding-bottom: 0;">
                                            <div class="row">
                                              <div class="col-md-8" style="margin-top: -10px;">
                                                <h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Filter/s</h5>
                                              </div>
                                              <div class="col-md-4" style="margin-top: -20px;">
                                                <button type="button" class="btn btn-default" id="clear-button-assem" style="margin-bottom:5px;font-size: 12px;padding: 7px 9px 6px 10px;float: right;">Clear</button>
                                              </div>
                                            </div>
                                            <div class="row" style="background-color: #ffffff; padding-top: 9px;">
                                              <div class="col-md-12" style="margin: 0;height: 730px;" id="filter_qa_inspection_assembly">
                                                <span style="display: block; font-size: 9pt; margin-top: -8px;"></span>
                                                <div class="form-group">
                                                  <label style="color: black;">Date Range:</label>
                                                  <input type="text" class="date form-control" name="daterange_assem" autocomplete="off" placeholder="Select Date From" id="daterange_assem" value="" style="text-align:center;display:inline-block;width:100%;height:30px;" onchange="tbl_log_fabrication()">
                                                </div>
                                                <div class="form-group margin-top">
                                                      <label style="color: black;">Workstation</label>
                                                      <select class="form-control text-center sel4" name="workstation_assem" id="workstation_assem">
                                                          <option value=""> Select Workstation</option>
                                                          @foreach($assem_workstation as $row)
                                                          <option value="{{$row->workstation_name}}">{{$row->workstation_name}}</option>
                                                          @endforeach
                                                        </select>
                                                   </div>
                                                  <div class="form-group margin-top">
                                                      <label style="color: black;">Item Code</label>
                                                      <select class="form-control text-center sel4 " name="item_code_assem" id="item_code_assem">
                                                          <option value=""> Select Item Code</option>
                                                          @foreach($item_code as $row)
                                                          <option value="{{$row->item_code}}">{{$row->item_code}}</option>
                                                          @endforeach
                                                        </select>
                                                   </div>
  
                                                  <div class="form-group margin-top">
                                                      <label style="color: black;">Production Order</label>
                                                       <select class="form-control text-center sel4 " name="prod_assem" id="prod_assem">
                                                          <option value=""> Select Production Order</option>
                                                          @foreach($production_order as $row)
                                                          <option value="{{$row->production_order}}">{{$row->production_order}}</option>
                                                          @endforeach
                                                        </select>
                                                   </div>
                                                  <div class="form-group margin-top">
                                                      <label style="color: black;">Customer</label>
                                                      <select class="form-control text-center sel4 " name="customer_assem" id="customer_assem">
                                                          <option value=""> Select Customer</option>
                                                          @foreach($customer as $row)
                                                          <option value="{{$row->customer}}">{{$row->customer}}</option>
                                                          @endforeach
                                                        </select>
                                                   </div>
                                                   
                                                   <div class="form-group margin-top">
                                                      <label style="color: black;">Process</label>
                                                      <select class="form-control text-center sel4 " name="process_assem" id="process_assem">
                                                        <option value=""> Select Process</option>
                                                         @foreach($assem_process as $row)
                                                          <option value="{{$row->process_name}}">{{$row->process_name}}</option>
                                                          @endforeach
                                                        </select>
                                                   </div>
                                                   <div class="form-group margin-top">
                                                      <label style="color: black;">QC Status</label>
                                                      <select class="form-control text-center sel4 " name="qa_status_assem" id="qa_status_assem">
                                                          <option value="">Select Status</option>
                                                          <option value="For Confirmation">For Confirmation</option>
                                                          <option value="QC Passed">QC Passed</option>
                                                          <option value="QC Failed">QC Failed</option>
                                                        </select>
                                                   </div>
                                                   <div class="form-group margin-top">
                                                      <label style="color: black;">QC Inspector</label>
                                                      <select class="form-control text-center sel4 " name="qa_inspector_assem" id="qa_inspector_assem">
                                                          <option value="">Select QC Inspector</option>
                                                          @foreach($qc_name as $row)
                                                          <option value="{{$row['user_id']}}">{{$row['name']}}</option>
                                                          @endforeach
                                                        </select>
                                                   </div>
                                                   <div class="form-group margin-top">
                                                      <label style="color: black;">Operator</label>
                                                      <select class="form-control text-center sel4 " name="operator_assem" id="operator_assem">
                                                          <option value="">Select Operator</option>
                                                          @foreach($operators as $row)
                                                          <option value="{{$row->user_id}}">{{$row->employee_name}}</option>
                                                          @endforeach
                                                        </select>
                                                   </div>
                                                  
                       
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-10">
                                        <div class="card">
                                          <div class="card-body" style="padding-bottom: 0;">
                                            <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
                                              <div class="col-md-12" style="max-height: 750px;">
                                                <div id="tbl_log_assem" style="width: 100%;overflow: auto;min-height: 750px;max-height: 750px;" class="table-responsive"></div>
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
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane" id="tab2" role="tabpanel" aria-labelledby="tab2">
            <div class="row">
              <div class="col-md-12">
                <div class="card" style="border-radius: 0 0 3px 3px;">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="card">
                          <div class="card-header" style="background-color: #0277BD;">
                            <div class="row" style="margin-top: -15px;">
                              <div class="col-md-12" style="padding: 10px;">
                                  <h5 class="text-white font-weight-bold align-middle text-center" style="font-size:13pt; margin: 0;">Defect(s) reported by QA Inspector</h5>
                              </div>
                            </div>
                          </div>
                          <div class="card-body" style="min-height: 350px; background-color: #263238;">
                            <div class="chart-area pt-4" style="min-height: 350px;">
                              <canvas id="bigDashboardChart"></canvas>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="card">
                          <div class="card-header" style="background-color: #0277BD;">
                            <div class="row" style="margin-top: -15px;">
                              <div class="col-md-12" style="padding: 10px;">
                                  <h5 class="text-white font-weight-bold align-middle text-center" style="font-size:13pt; margin: 0;">Top 5 Defect(s)</h5>
                              </div>
                            </div>
                          </div>
                          <div class="card-body" style="min-height: 350px;">
                            <div class="chart-area pt-4" style="min-height: 350px;">
                              <canvas id="myChart" width="50" height="18"></canvas>
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
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="jt-workstations-modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document" style="min-width: 90%;">
        <div class="modal-content">
          <div class="text-white rounded-top" style="background-color: #0277BD;">
            <div class="d-flex flex-row justify-content-between p-3 align-items-center">
              <h5 class="font-weight-bold m-0 p-0">Job Ticket</h5>
              <div class="float-right">
                <h5 class="modal-title font-weight-bold p-0 mr-5 font-italic d-inline-block">Modal Title</h5>
                <button type="button" class="close d-inline-block ml-3" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            </div>
          </div>
          <div class="modal-body" style="min-height: 600px;">
            <div class="row" id="jt-details-col">
              <div class="col-md-12">
                <div class="container">
                  <div class="row">
                    <ul class="breadcrumb-c" id="process-bc"></ul>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div style="margin: 5px;">
                  <span style="font-size: 12pt; margin: auto;">Scheduled Date: </span>
                  <span class="sched-date font-weight-bold" style="font-size: 12pt; margin: auto;"></span>
                  <span class="badge badge-info task-status">--</span>
                </div>
                <table style="width: 100%; border-color: #D5D8DC;">
                  <col style="width: 18%;">
                  <col style="width: 24%;">
                  <col style="width: 23%;">
                  <col style="width: 20%;">
                  <col style="width: 15%;">
                  <tr style="font-size: 9pt;">
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>REFERENCE NO.</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>CUSTOMER</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PROJECT</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>DELIVERY DATE</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>QTY</b></td>
                  </tr>
                  <tr style="font-size: 10pt;">
                    <td class="text-center" style="border: 1px solid #ABB2B9;"><span class="ref-no"></span></td>
                    <td class="text-center" style="border: 1px solid #ABB2B9;"><span class="cust"></span></td>
                    <td class="text-center" style="border: 1px solid #ABB2B9;"><span class="proj"></span></td>
                    <td class="text-center" style="border: 1px solid #ABB2B9;"><span class="del-date"></span></td>
                    <td class="text-center" style="border: 1px solid #ABB2B9; font-size: 15pt;"><span class="qty"></span></td>
                  </tr>
                  <tr style="font-size: 10pt;">
                    <td style="border: 1px solid #ABB2B9; font-size: 9pt;" class="text-center"><b>ITEM DETAIL(S):</b></td>
                    <td style="border: 1px solid #ABB2B9;" colspan="4"><span class="item-code font-weight-bold"></span> - <span class="desc">--</span></td>
                  </tr>
                </table>
              </div>
  
              <div class="col-md-12" style="padding-top: 20px;">
                <br>
                <table style="width: 100%; border-color: #D5D8DC;" id="totals-tbl">
                    <col style="width: 25%;">
                    <col style="width: 25%;">
                    <col style="width: 25%;">
                    <col style="width: 25%;">
                    <thead style="font-size: 10pt;">
                        <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PRODUCED QTY</b></td>
                        <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>TOTAL GOOD</b></td>
                        <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>TOTAL REJECT</b></td>
                        <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>BALANCE QTY</b></td>
                    </thead>
                    <tbody style="font-size: 9pt;">
                      <tr>
                        <td class="text-center" style="border: 1px solid #ABB2B9;">
                          <span class="produced-qty" style="font-size: 15pt;"></span>
                        </td>
                        <td class="text-center" style="border: 1px solid #ABB2B9;">
                          <span class="total-good" style="font-size: 15pt;"></span>
                        </td>
                        <td class="text-center" style="border: 1px solid #ABB2B9;">
                          <span class="total-reject" style="font-size: 15pt;"></span>
                        </td>
                        <td class="text-center" style="border: 1px solid #ABB2B9;">
                          <span class="balance-qty" style="font-size: 15pt;"></span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
              </div>
    
             
              <div class="col-md-12">
                <br>
                <div class="table-respons1ive">
                  <table style="width: 100%; border-color: #D5D8DC;" id="jt-details-tbl">
                    <col style="width: 15%;">
                    <col style="width: 15%;">
                    <col style="width: 12%;">
                    <col style="width: 12%;">
                    <col style="width: 10%;">
                    <col style="width: 12%;">
                    <col style="width: 12%;">
                    <col style="width: 12%;">
                    <thead style="font-size: 10pt;">
                        <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>WORKSTATION</b></td>
                        <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PROCESS</b></td>
                        <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>GOOD</b></td>
                        <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>REJECT</b></td>
                        <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>MACHINE</b></td>
                        <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>START</b></td>
                        <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>END</b></td>
                        <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>OPERATOR</b></td>
                    </thead>
                    <tbody style="font-size: 9pt;"></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="spotwelding-modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document" style="min-width: 90%;">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
            <h5 class="modal-title font-weight-bold prod-title">Modal Title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" style="min-height: 500px;">
            <div class="row">
              <div class="col-md-12">
                <div id="spotwelding-div"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
<style>
    .classheader{
      position: sticky; top: 0; 
    z-index:2;
    position: -webkit-sticky;
    border: 0.8px solid white;
    }
    .margin-top{
      margin-top: -18px;
    }
  #fabrication .form-control, #painting .form-control{
      border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }
  #assembly .form-control{
      border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }
    .span-title{
      display: block;
      font-weight: bold;
      font-size: 11pt;
    }
  
    .span-qty{
      display: block;
      font-weight: bold;
      font-size: 22pt;
    }
  
    .span-uom{
      display: block;
      font-size:8pt;
    }
  </style>
  <style type="text/css">
  .modal-lg-custom {
      max-width: 80% !important;
  }
  #manual-production-modal .form-control {
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }
  #manual-production-modal .form-control:hover, #manual-production-modal .form-control:focus, #manual-production-modal .form-control:active {
    box-shadow: none;
  }
  #manual-production-modal .form-control:focus {
    border: 1px solid #34495e;
  }
  
  .select2.select2-container {
    width: 100% !important;
  }
  
  .select2.select2-container .select2-selection {
    border: 1px solid #ccc;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    height: 34px;
    margin-bottom: 15px;
    outline: none;
    transition: all 0.15s ease-in-out;
  }
  
  .select2.select2-container .select2-selection .select2-selection__rendered {
    color: #333;
    line-height: 32px;
    padding-right: 33px;
  }
  
  .select2.select2-container .select2-selection .select2-selection__arrow {
    background: #f8f8f8;
    border-left: 1px solid #ccc;
    -webkit-border-radius: 0 3px 3px 0;
    -moz-border-radius: 0 3px 3px 0;
    border-radius: 0 3px 3px 0;
    height: 32px;
    width: 33px;
  }
  
  .select2.select2-container.select2-container--open .select2-selection.select2-selection--single {
    background: #f8f8f8;
  }
  
  .select2.select2-container.select2-container--open .select2-selection.select2-selection--single .select2-selection__arrow {
    -webkit-border-radius: 0 3px 0 0;
    -moz-border-radius: 0 3px 0 0;
    border-radius: 0 3px 0 0;
  }
  
  .select2.select2-container.select2-container--open .select2-selection.select2-selection--multiple {
    border: 1px solid #34495e;
  }
  
  .select2.select2-container.select2-container--focus .select2-selection {
    border: 1px solid #34495e;
  }
  
  .select2.select2-container .select2-selection--multiple {
    height: auto;
    min-height: 34px;
  }
  
  .select2.select2-container .select2-selection--multiple .select2-search--inline .select2-search__field {
    margin-top: 0;
    height: 32px;
  }
  
  .select2.select2-container .select2-selection--multiple .select2-selection__rendered {
    display: block;
    padding: 0 4px;
    line-height: 29px;
  }
  
  .select2.select2-container .select2-selection--multiple .select2-selection__choice {
    background-color: #f8f8f8;
    border: 1px solid #ccc;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    margin: 4px 4px 0 0;
    padding: 0 6px 0 22px;
    height: 24px;
    line-height: 24px;
    font-size: 12px;
    position: relative;
  }
  
  .select2.select2-container .select2-selection--multiple .select2-selection__choice .select2-selection__choice__remove {
    position: absolute;
    top: 0;
    left: 0;
    height: 22px;
    width: 22px;
    margin: 0;
    text-align: center;
    color: #e74c3c;
    font-weight: bold;
    font-size: 16px;
  }
  
  .select2-container .select2-dropdown {
    background: transparent;
    border: none;
    margin-top: -5px;
  }
  
  .select2-container .select2-dropdown .select2-search {
    padding: 0;
  }
  
  .select2-container .select2-dropdown .select2-search input {
    outline: none;
    border: 1px solid #34495e;
    border-bottom: none;
    padding: 4px 6px;
  }
  
  .select2-container .select2-dropdown .select2-results {
    padding: 0;
  }
  
  .select2-container .select2-dropdown .select2-results ul {
    background: #fff;
    border: 1px solid #34495e;
  }
  
  .select2-container .select2-dropdown .select2-results ul .select2-results__option--highlighted[aria-selected] {
    background-color: #3498db;
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
  
  </style>
  @include('quality_inspection.modal_inspection')
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
      load_chart();
      get_quick_view_data();
      get_reject_for_confirmation();
      setInterval(load_chart, 5000);
      setInterval(get_quick_view_data, 5000);
      load_chart_2();
  
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
          url:"/qa_staff_workload",
          type:"GET",
          success:function(data){
            var labels = [];
            var outputs = [];
            for(var i in data) {
              labels.push(data[i].qa_staff);
              outputs.push(data[i].qty_checked);
            }
  
            var myChart = new Chart(ctx, {
              type: 'horizontalBar',
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
                    }
                  }]
                }
              }
            });
          }
        });  
      }
  
      function load_chart_2(){
        var ctx = document.getElementById('myChart').getContext("2d");
        $.ajax({
          url:"/get_top_defect_count",
          type:"GET",
          success:function(data){
            var labels = [];
            var outputs = [];
            for(var i in data) {
              labels.push(data[i].reject_checklist);
              outputs.push(data[i].reject_count);
            }
  
            var myChart = new Chart(ctx, {
              type: 'radar',
              data: {
                labels: labels,
                datasets: [{
                  label: "Total Count",
                  backgroundColor: "rgba(41, 128, 185, 0.2)",
                  borderColor: "rgba(41, 128, 185, 1)",
                  pointBackgroundColor: "rgba(41, 128, 185, 1)",
                  pointBorderColor: "#fff",
                  pointHoverBackgroundColor: "#fff",
                  pointHoverBorderColor: "rgba(41, 128, 185, 1)",
                  data: outputs,
                }]
              },
              options: {
                responsive: true,
                scale: {
                  pointLabels: {
                    fontSize: 15
                  },
                  ticks: {
                      stepSize: 1
                  },
                  yAxes: [{
                    ticks: {
                        beginAtZero:true,
                    }
                  }]
                },
                legend: {
                  display: false,
                  
                },
              }
            });
          }
        });  
      }
    
      function get_reject_for_confirmation(page, query){
        $.ajax({
          url:"/get_reject_for_confirmation?page=" + page,
          type:"GET",
          data: {q: query},
          success:function(data){
            $('#reject-confirmation-tbl-div').html(data);
          }
        }); 
      }
  
      function get_quick_view_data(){
        $.ajax({
          url:"/get_quick_view_data",
          type:"GET",
          success:function(data){
            $('#fabrication-card .produced-qty').text(data.fabrication.produced_qty);
            $('#painting-card .produced-qty').text(data.painting.produced_qty);
            $('#assembly-card .produced-qty').text(data.assembly.produced_qty);
  
            $('#fabrication-card .inspected-qty').text(data.fabrication.inspected_qty);
            $('#painting-card .inspected-qty').text(data.painting.inspected_qty);
            $('#assembly-card .inspected-qty').text(data.assembly.inspected_qty);
  
            $('#fabrication-card .rejected-qty').text(data.fabrication.rejected_qty);
            $('#painting-card .rejected-qty').text(data.painting.rejected_qty);
            $('#assembly-card .rejected-qty').text(data.assembly.rejected_qty);
  
            $('#fabrication-card .production-order-count').text(data.fabrication.production_order);
            $('#painting-card .production-order-count').text(data.painting.production_order);
            $('#assembly-card .production-order-count').text(data.assembly.production_order);
  
            $('#fabrication-card .completed-wip-count').text(data.fabrication.completed_wip_production_orders);
            $('#painting-card .completed-wip-count').text(data.painting.completed_wip_production_orders);
            $('#assembly-card .completed-wip-count').text(data.assembly.completed_wip_production_orders);
  
            $('#fabrication-card .performance').text(data.fabrication.performance + '%');
            $('#painting-card .performance').text(data.painting.performance + '%');
            $('#assembly-card .performance').text(data.assembly.performance + '%');
  
            $('#fabrication-card .qa-efficiency').text(data.fabrication.qa_efficiency + '%');
            $('#painting-card .qa-efficiency').text(data.painting.qa_efficiency + '%');
            $('#assembly-card .qa-efficiency').text(data.assembly.qa_efficiency + '%');
          }
        });
      }
  
      $(document).on('submit', '#reject-confirmation-frm', function(e){
        e.preventDefault();
        $.ajax({
          url: $(this).attr('action'),
          type:"POST",
          data: $(this).serialize(),
          success:function(data){
            console.log(data);
            if (data.success) {
              showNotification("success", data.message, "now-ui-icons ui-1_check");
              $('#quality-inspection-modal').modal('hide');
              get_reject_for_confirmation();
            }else{
              showNotification("danger", data.message, "now-ui-icons travel_info");
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
              }
        });
      });
  
      function showNotification(color, message, icon){
        $.notify({
          icon: icon,
          message: message
        },{
          type: color,
          timer: 1000,
          placement: {
            from: 'top',
            align: 'center'
          }
        });
      }
  
      $(document).on('keyup', '#search-reject-confirmation', function(){
        var query = $(this).val();
        get_reject_for_confirmation(1, query);
      });
  
      $(document).on('click', '#paginate-reject-confirmation a', function(event){
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        get_reject_for_confirmation(page);
      });
  
      $(document).on('click', '.reject-confirmation-btn', function(e){
        e.preventDefault();
        
        var inspection_type = $(this).data('inspection-type');
        var workstation = $(this).data('workstation');
        var production_order = $(this).data('production-order');
        var process_id = $(this).data('process-id');
        var qa_id = $(this).data('qaid');
  
        $.ajax({
          url:"/get_reject_confirmation_checklist/" + production_order + "/" + workstation + "/" + process_id + "/" + qa_id,
          type:"GET",
          success:function(data){
            $('#quality-inspection-div').html(data);
            $('#quality-inspection-modal .qc-type').text(inspection_type);
            $('#quality-inspection-modal .qc-workstation').text('[' + workstation + ']');
            $('#quality-inspection-modal').modal('show');
          }
        });
      });
  
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
  <script type="text/javascript">
    $(document).ready(function(){
      // tbl_log_fabrication();
    $('.sel3').select2({
      dropdownParent: $("#filter_qa_inspection_painting"),
      dropdownAutoWidth: false,
      width: '100%',
      cache: false,
      placeholder: "",
      allowClear: true
    });
    $('.sel4').select2({
      dropdownParent: $("#filter_qa_inspection_assembly"),
      dropdownAutoWidth: false,
      width: '100%',
      cache: false,
      placeholder: "",
      allowClear: true
    });
    $('.sel6').select2({
      dropdownParent: $("#filter_qa_inspection_fabrication"),
      dropdownAutoWidth: false,
      width: '100%',
      cache: false,
      placeholder: "",
      allowClear: true
    });
  
  
    $('#daterange_fabrication').daterangepicker({
      "showDropdowns": true,
      "startDate": moment().startOf('month'),
      "endDate": moment().endOf('month'),
      "ranges": {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      "linkedCalendars": false,
      "autoUpdateInput": true,
      "alwaysShowCalendars": true,
    }, function(start, end, label) {
      console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
      tbl_log_fabrication();
    });
    tbl_log_fabrication();
  
     $('#daterange_fabrication').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        tbl_log_fabrication();
  
    });
    
    $('#daterange_painting').daterangepicker({
      "showDropdowns": true,
      "startDate": moment().startOf('month'),
      "endDate": moment().endOf('month'),
      "ranges": {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      "linkedCalendars": false,
      "autoUpdateInput": true,
      "alwaysShowCalendars": true,
    }, function(start, end, label) {
      console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
      tbl_log_painting();
    });
    tbl_log_painting();
  
     $('#daterange_painting').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        tbl_log_painting();
  
    });
    $('#daterange_assem').daterangepicker({
      "showDropdowns": true,
      "startDate": moment().startOf('month'),
      "endDate": moment().endOf('month'),
      "ranges": {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      "linkedCalendars": false,
      "autoUpdateInput": true,
      "alwaysShowCalendars": true,
    }, function(start, end, label) {
      console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
      tbl_log_assem();
    });
    tbl_log_assem();
  
     $('#daterange_assem').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        tbl_log_assem();
  
    });
   });
  </script>
  <script type="text/javascript">
    function showNotification(color, message, icon){
          $.notify({
            icon: icon,
            message: message
          },{
            type: color,
            timer: 500,
            placement: {
              from: 'top',
              align: 'center'
            }
          });
    }
  </script>
  <script type="text/javascript">
  function tbl_log_fabrication(){
    var date = $('#daterange_fabrication').val();
    var workstation = $('#workstation').val();
    var startDate = $('#daterange_fabrication').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var endDate = $('#daterange_fabrication').data('daterangepicker').endDate.format('YYYY-MM-DD');
    var operation_id=1;
    var data = {
          workstation : workstation,
          customer: $('#customer').val(),
          prod:$('#prod').val(),
          item_code: $('#item_code').val(),
          status: $('#qa_status').val(),
          process: $('#process').val()
        }
    $.ajax({
            url:"/tbl_qa_inspection_log_report/"+ startDate +"/" +endDate+'/'+ operation_id,
            type:"GET",
            data: data,
            success:function(data){
              
              $('#tbl_log_fabrication').html(data);
            }
          });
    };
  
  </script>
  <script>
  $(document).on('change', '.sel6', function(event){
    var date = $('#daterange_fabrication').val();
    var workstation = $('#workstation').val();
    var startDate = $('#daterange_fabrication').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var endDate = $('#daterange_fabrication').data('daterangepicker').endDate.format('YYYY-MM-DD');
    var operation_id=1;
    var data = {
          workstation: workstation,
          customer: $('#customer').val(),
          prod:$('#prod').val(),
          item_code: $('#item_code').val(),
          status: $('#qa_status').val(),
          process: $('#process').val(),
          qa_inspector: $('#qa_inspector').val(),
          operator: $('#operator').val(),
        }
    if(workstation == 'none'){
      
    }else if(date == ""){
      
    }else{
      $.ajax({
            url:"/tbl_qa_inspection_log_report/"+ startDate +"/" +endDate + "/"+ operation_id,
            type:"GET",
            data: data,
            success:function(data){
              
              $('#tbl_log_fabrication').html(data);
            }
          });
    }
    
    });
  </script> 
  <script type="text/javascript">
      $(document).on('click', '#fabrication-btn-export', function(){
        var date = $('#daterange').val();
        var workstation = $('#workstation').val();
        var startDate = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var endDate = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
       
        var customer= $('#customer').val();
        var prod=$('#prod').val();
        var item_code= $('#item_code').val();
        var status= $('#qa_status').val();
        var processs= $('#process').val();
        var qa_inspector= $('#qa_inspector').val();
        var operator= $('#operator').val();
        if(customer ==""){
          var customer="none";
        }
        if(prod ==""){
          var prod="none";
        }
        if(item_code ==""){
          var item_code="none";
        }
        if(status ==""){
          var status="none";
        }
        if(processs ==""){
          var processs="none";
        }
        if(qa_inspector ==""){
          var qa_inspector="none";
        }
        if(operator ==""){
          var operator="none";
        }
        if(workstation == 'none'){
          
        }else if(date == ""){
          
        }else{
            location.href= "/get_tbl_qa_inspection_log_export/"+ startDate +"/" +endDate+"/"+workstation+"/"+ customer +"/" + prod + "/" + item_code + "/" + status + "/" + processs + "/" + qa_inspector + "/" +  operator;
        }
  
      });
  </script>
  <script type="text/javascript">
  function tbl_log_painting(){
    var date = $('#daterange_painting').val();
    var workstation = $('#workstation_painting').val();
    var startDate = $('#daterange_painting').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var endDate = $('#daterange_painting').data('daterangepicker').endDate.format('YYYY-MM-DD');
    var operation_id= 1;
    var data = {
          workstation: 'Painting',
          customer: $('#customer_painting').val(),
          prod:$('#prod_painting').val(),
          item_code: $('#item_code_painting').val(),
          status: $('#qa_status_painting').val(),
          process: $('#process_painting').val()
        }
    $.ajax({
            url:"/tbl_qa_inspection_log_report/"+ startDate +"/" +endDate+"/"+operation_id,
            type:"GET",
            data: data,
            success:function(data){
              
              $('#tbl_log_painting').html(data);
            }
          });
    };
  
  </script>
  <script>
  $(document).on('change', '.sel3', function(event){
    var date = $('#daterange_painting').val();
    var workstation = $('#workstation_painting').val();
    var startDate = $('#daterange_painting').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var endDate = $('#daterange_painting').data('daterangepicker').endDate.format('YYYY-MM-DD');
    var operation_id = 1;
    var data = {
          workstation: "Painting",
          customer: $('#customer_painting').val(),
          prod:$('#prod_painting').val(),
          item_code: $('#item_code_painting').val(),
          status: $('#qa_status_painting').val(),
          process: $('#process_painting').val(),
          qa_inspector: $('#qa_inspector_painting').val(),
          operator: $('#operator_painting').val(),
        }
    if(workstation == 'none'){
      
    }else if(date == ""){
      
    }else{
      $.ajax({
            url:"/tbl_qa_inspection_log_report/"+ startDate +"/" +endDate+"/"+operation_id,
            type:"GET",
            data: data,
            success:function(data){
              
              $('#tbl_log_painting').html(data);
            }
          });
    }
    
    });
  </script> 
  <script type="text/javascript">
      $(document).on('click', '#painting-btn-export', function(){
        var date = $('#daterange_painting').val();
        var workstation = $('#workstation_painting').val();
        var startDate = $('#daterange_painting').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var endDate = $('#daterange_painting').data('daterangepicker').endDate.format('YYYY-MM-DD');
       
        var customer= $('#customer_painting').val();
        var prod=$('#prod_painting').val();
        var item_code= $('#item_code_painting').val();
        var status= $('#qa_status_painting').val();
        var processs= $('#process_painting').val();
        var qa_inspector= $('#qa_inspector_painting').val();
        var operator= $('#operator_painting').val();
  
        if(workstation == 'none'){
          
        }else if(date == ""){
          
        }else{
            location.href= "/get_tbl_qa_inspection_log_export_painting/"+ startDate +"/" +endDate+"/"+workstation+"/"+ customer +"/" + prod + "/" + item_code + "/" + status + "/" + processs + "/" + qa_inspector + "/" +  operator;
        }
  
      });
  </script>
  <script type="text/javascript">
    
    $(function(){
      $("#clear-button-fab").click(function(){
          $("#workstation").select2('val', 'All');
          $("#customer").select2('val', 'All');
          $("#prod").select2('val', 'All');
          $("#status").select2('val', 'All');
          $("#process").select2('val', 'All');
          $("#qa_inspector").select2('val', 'All');
          $("#operator").select2('val', 'All');
          $("#item_code").select2('val', 'All');
          $("#qa_status").select2('val', 'All');
      });
  });
    $(function(){
      $("#clear-button-painting").click(function(){
          $("#workstation_painting").select2('val', 'All');
          $("#customer_painting").select2('val', 'All');
          $("#prod_painting").select2('val', 'All');
          $("#status_painting").select2('val', 'All');
          $("#process_painting").select2('val', 'All');
          $("#qa_inspector_painting").select2('val', 'All');
          $("#operator_painting").select2('val', 'All');
          $("#item_code_painting").select2('val', 'All');
          $("#qa_status_painting").select2('val', 'All');
      });
  });
  $(function(){
      $("#clear-button-assem").click(function(){
          $("#workstation_assem").select2('val', 'All');
          $("#customer_assem").select2('val', 'All');
          $("#prod_assem").select2('val', 'All');
          $("#status_assem").select2('val', 'All');
          $("#process_assem").select2('val', 'All');
          $("#qa_inspector_assem").select2('val', 'All');
          $("#operator_assem").select2('val', 'All');
          $("#item_code_assem").select2('val', 'All');
          $("#qa_status_assem").select2('val', 'All');
      });
  });
  </script>
  <script type="text/javascript">
      $(document).on('click', '.prod-details-btn', function(e){
      e.preventDefault();
      var jtno = $(this).data('jtno');
      $('#jt-workstations-modal .modal-title').text(jtno);
      if(jtno){
        getJtDetails($(this).data('jtno'));
      }else{
        showNotification("danger", 'No Job Ticket found.', "now-ui-icons travel_info");
      }
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
            $('#jt-details-col .produced-qty').text(data.totals.produced_qty);
            $('#jt-details-col .total-good').text(data.totals.total_good);
            $('#jt-details-col .total-reject').text(data.totals.total_reject);
            $('#jt-details-col .balance-qty').text(data.totals.balance_qty);
  
            if (data.item_details.sales_order) {
              $('#jt-details-col .ref-type').text('SO No.');
              $('#jt-details-col .ref-no').text(data.item_details.sales_order);
            }
    
            if (data.item_details.material_request) {
              $('#jt-details-col .ref-type').text('MREQ No.');
              $('#jt-details-col .ref-no').text(data.item_details.material_request);
            }
    
            $('#jt-details-col .prod-no').text(data.item_details.production_order);
            $('#jt-details-col .cust').text(data.item_details.customer);
            $('#jt-details-col .proj').text(data.item_details.project);
            $('#jt-details-col .qty').text(data.item_details.qty_to_manufacture);
            $('#jt-details-col .del-date').text(data.item_details.delivery_date);
            $('#jt-details-col .item-code').text(data.item_details.item_code);
            $('#jt-details-col .desc').text(data.item_details.description);
            $('#jt-details-col .sched-date').text(data.item_details.planned_start_date);
            $('#jt-details-col .task-status').text(data.item_details.status);
            if (data.item_details.status == 'Late') {
              $('#jt-details-col .task-status').removeClass('badge-info').addClass('badge-danger');
            }else{
              $('#jt-details-col .task-status').removeClass('badge-danger').addClass('badge-info');
            } 
       
            var r = '';
            $.each(data.operations, function(i, v){
              if(v.workstation == "Spotwelding"){
                var spotclass= "spotclass";
                var icon = '<span style="font-size:15px;">&nbsp; >></span>';
              }else{
                var spotclass= "";
                var icon="";
              }
              r += '<tr>' +
                '<td class="text-center" style="border: 1px solid #ABB2B9;" rowspan="' + v.count + '"><span class="'+ spotclass +'" data-jobticket="'+v.job_ticket +'" data-prodno="'+ v.production_order+'"><b>' + v.workstation + icon+'</b></span></td>';
              if (v.operations.length > 0) {
                $.each(v.operations, function(i, d){
                  machine = (d.machine_code) ? d.machine_code : '-';
                  operator_name = (d.operator_name) ? d.operator_name : '-';
                  from_time = (d.from_time) ? d.from_time : '-';
                  to_time = (d.to_time) ? d.to_time : '-';
                  var inprogress_class = (d.status == 'In Progress') ? 'active-process' : '';
                 
                  if(v.process == "Housing and Frame Welding"){
                    qc_status = '';
                  }else{
                     var qc_status = (d.qa_inspection_status == 'QC Passed') ? "qc_passed" : "qc_failed";
                      qc_status = (d.qa_inspection_status == 'Pending') ? '' : qc_status;
                  }
                  r += '<td class="text-center '+inprogress_class+' '+qc_status+'" style="border: 1px solid #ABB2B9;"><b>' + v.process + '</b></td>' +
                      '<td class="text-center '+inprogress_class+'" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>' + Number(d.good) + '</b></td>' +
                      '<td class="text-center '+inprogress_class+'" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>' + Number(d.reject) + '</b></td>' +
                      '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + machine + '</td>' +
                      '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + from_time + '</td>' +
                      '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + to_time + '</td>' +
                      '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + operator_name + '</td>' +
                      '</tr>';
                });
              }else{
                r += '<td class="text-center" style="border: 1px solid #ABB2B9;"><b>' + v.process + '</b></td>' +
                      '<td class="text-center" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>0</b></td>' +
                      '<td class="text-center" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>0</b></td>' +
                      '<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>' +
                      '<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>' +
                      '<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>' +
                      '<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>' +
                      '</tr>';
              }
            });
  
            var p = '';
            $.each(data.process, function(i, d){
              p += '<li class="'+ d.status +'">'+
                  '<a href="javascript:void(0);">' + d.workstation + '</a>' +
                  '</li>';
              });
    
            $('#process-bc').append(p);
            $('#jt-details-tbl tbody').append(r);
            $('#jt-workstations-modal').modal('show');
          }
        });
      }
  </script>
  <script type="text/javascript">
    $(document).on('click', '.spotclass', function(event){
        event.preventDefault();
        var jtid = $(this).attr('data-jobticket');
        var prod = $(this).attr('data-prodno');
        $.ajax({
          url: "/spotwelding_production_order_search/" + jtid,
          type:"GET",
          success:function(data){
              $('#spotwelding-div').html(data);
              $('#spotwelding-modal .prod-title').text(prod+" - Spotwelding");
              $('#spotwelding-modal').modal('show');
          }
        });
      });
  </script>
  <script type="text/javascript">
    function tbl_log_assem(){
      var date = $('#daterange_assem').val();
      var workstation = $('#workstation_assem').val();
      var startDate = $('#daterange_assem').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var endDate = $('#daterange_assem').data('daterangepicker').endDate.format('YYYY-MM-DD');
      var operation_id= 3;
      var data = {
            workstation: workstation,
            customer: $('#customer_assem').val(),
            prod:$('#prod_assem').val(),
            item_code: $('#item_code_assem').val(),
            status: $('#qa_status_assem').val(),
            process: $('#process_assem').val()
          }
      $.ajax({
              url:"/tbl_qa_inspection_log_report/"+ startDate +"/" +endDate+"/"+operation_id,
              type:"GET",
              data: data,
              success:function(data){
                $('#tbl_log_assem').html(data);
              }
            });
      };
    
    </script>
    <script>
    $(document).on('change', '.sel4', function(event){
      var date = $('#daterange_assem').val();
      var workstation = $('#workstation_assem').val();
      var startDate = $('#daterange_assem').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var endDate = $('#daterange_assem').data('daterangepicker').endDate.format('YYYY-MM-DD');
      var operation_id = 3;
      var data = {
            workstation:workstation,
            customer: $('#customer_assem').val(),
            prod:$('#prod_assem').val(),
            item_code: $('#item_code_assem').val(),
            status: $('#qa_status_assem').val(),
            process: $('#process_assem').val(),
            qa_inspector: $('#qa_inspector_assem').val(),
            operator: $('#operator_assem').val(),
          }
      if(workstation == 'none'){
        
      }else if(date == ""){
        
      }else{
        $.ajax({
              url:"/tbl_qa_inspection_log_report/"+ startDate +"/" +endDate+"/"+operation_id,
              type:"GET",
              data: data,
              success:function(data){
                
                $('#tbl_log_assem').html(data);
              }
            });
      }
      
      });
    </script> 
  @endsection