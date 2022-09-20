@extends('layouts.user_app', [
    'namePage' => 'MES',
    'activePage' => 'qa_dashboard',
    'pageHeader' => 'Quality Assurance Dashboard',
  'pageSpan' => Auth::user()->employee_name . ' - ' . $user_details->designation_name
])

@section('content')
<div class="panel-header"></div>

<div class="row p-0" style="margin-top: -190px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
    <div class="col-md-12">
      <ul class="nav nav-tabs" role="tablist" id="qa-dashboard-tabs">
        <li class="nav-item">
           <a class="nav-link active" data-toggle="tab" href="#tab0" role="tab" aria-controls="tab0" aria-selected="true">Quick View</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="false">Inspection Logsheet</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Rejection Report</a>
        </li>
      </ul>
      <div class="tab-content" style="min-height: 500px;">
        <div class="tab-pane active" id="tab0" role="tabpanel" aria-labelledby="tab0">
          <div class="row">
            <div class="col-md-12">
              <div class="card" style="border-radius: 0 0 3px 3px;">
                <div class="card-body">
                  <div class="row m-0">
                    <div class="col-md-4">
                      <div class="card" id="fabrication-card">
                        <div class="card-body">
                          <div class="row text-center">
                            <div class="col-md-12">
                              <h5 class="text-center text-uppercase font-weight-bold">Fabrication</h5>
                            </div>
                            <div class="col-md-4">
                              <span class="span-title">Produced</span>
                              <span class="span-qty produced-qty">0</span>
                              <span class="span-uom">Piece(s)</span>
                            </div>
                            <div class="col-md-4">
                              <span class="span-title">Inspected</span>
                              <span class="span-qty inspected-qty">0</span>
                              <span class="span-uom">Piece(s)</span>
                            </div>
                            <div class="col-md-4">
                              <span class="span-title">Reject</span>
                              <span class="span-qty rejected-qty">0</span>
                              <span class="span-uom">Piece(s)</span>
                            </div>
                          </div>
                          <div class="row text-center mt-4">
                            <div class="col-md-12">
                              <table style="width: 100%; border: 1px solid #99A3A4;">
                                <tr style="background-color: #2E86C1;" class="text-white">
                                  <td style="width: 50%;"><span class="span-title">Production Order</span></td>
                                  <td style="width: 50%;"><span class="span-title">Completed / WIP</span></td>
                                </tr>
                                <tr>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty production-order-count">0</span>
                                  </td>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty completed-wip-count">0</span>
                                  </td>
                                </tr>
                                <tr style="background-color: #2E86C1;" class="text-white">
                                  <td><span class="span-title">Performance</span></td>
                                  <td><span class="span-title">Efficiency</span></td>
                                </tr>
                                <tr>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty performance">0%</span>
                                  </td>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty qa-efficiency">0%</span>
                                  </td>
                                </tr>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="card" id="painting-card">
                        <div class="card-body">
                          <div class="row text-center">
                            <div class="col-md-12">
                              <h5 class="text-center text-uppercase font-weight-bold">Painting</h5>
                            </div>
                            <div class="col-md-4">
                              <span class="span-title">Produced</span>
                              <span class="span-qty produced-qty">0</span>
                              <span class="span-uom">Piece(s)</span>
                            </div>
                            <div class="col-md-4">
                              <span class="span-title">Inspected</span>
                              <span class="span-qty inspected-qty">0</span>
                              <span class="span-uom">Piece(s)</span>
                            </div>
                            <div class="col-md-4">
                              <span class="span-title">Reject</span>
                              <span class="span-qty rejected-qty">0</span>
                              <span class="span-uom">Piece(s)</span>
                            </div>
                          </div>
                          <div class="row text-center mt-4">
                            <div class="col-md-12">
                              <table style="width: 100%; border: 1px solid #99A3A4;">
                                <tr style="background-color: #2E86C1;" class="text-white">
                                  <td style="width: 50%;"><span class="span-title">Production Order</span></td>
                                  <td style="width: 50%;"><span class="span-title">Completed / WIP</span></td>
                                </tr>
                                <tr>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty production-order-count">0</span>
                                  </td>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty completed-wip-count">0</span>
                                  </td>
                                </tr>
                                <tr style="background-color: #2E86C1;" class="text-white">
                                  <td><span class="span-title">Performance</span></td>
                                  <td><span class="span-title">Efficiency</span></td>
                                </tr>
                                <tr>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty performance">0%</span>
                                  </td>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty qa-efficiency">0%</span>
                                  </td>
                                </tr>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="card" id="assembly-card">
                        <div class="card-body">
                          <div class="row text-center">
                            <div class="col-md-12">
                              <h5 class="text-center text-uppercase font-weight-bold">Assembly</h5>
                            </div>
                            <div class="col-md-4">
                              <span class="span-title">Produced</span>
                              <span class="span-qty produced-qty">0</span>
                              <span class="span-uom">Piece(s)</span>
                            </div>
                            <div class="col-md-4">
                              <span class="span-title">Inspected</span>
                              <span class="span-qty inspected-qty">0</span>
                              <span class="span-uom">Piece(s)</span>
                            </div>
                            <div class="col-md-4">
                              <span class="span-title">Reject</span>
                              <span class="span-qty rejected-qty">0</span>
                              <span class="span-uom">Piece(s)</span>
                            </div>
                          </div>
                          <div class="row text-center mt-4">
                            <div class="col-md-12">
                              <table style="width: 100%; border: 1px solid #99A3A4;">
                                <tr style="background-color:#2E86C1;" class="text-white">
                                  <td style="width: 50%;"><span class="span-title">Production Order</span></td>
                                  <td style="width: 50%;"><span class="span-title">Completed / WIP</span></td>
                                </tr>
                                <tr>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty production-order-count">0</span>
                                  </td>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty completed-wip-count">0</span>
                                  </td>
                                </tr>
                                <tr style="background-color: #2E86C1;" class="text-white">
                                  <td><span class="span-title">Performance</span></td>
                                  <td><span class="span-title">Efficiency</span></td>
                                </tr>
                                <tr>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty performance">0%</span>
                                  </td>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty qa-efficiency">0%</span>
                                  </td>
                                </tr>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="card">
                        <div class="card-header" style="background-color: #0277BD;">
                          <div class="row" style="margin-top: -15px;">
                            <div class="col-md-8" style="padding: 10px;">
                                <h5 class="text-white font-weight-bold align-middle" style="font-size:13pt; margin: 0;">Reject Confirmation</h5>
                            </div>
                            <div class="col-md-4" style="padding: 8px;">
                                <div class="form-group" style="margin: 0;">
                                    <input type="text" id="search-reject-confirmation" class="form-control" placeholder="Search" style="background-color: white; padding: 6px 8px;" autocomplete="off">
                                </div>
                            </div>
                          </div>
                        </div>
                        <div class="card-body">
                          <div id="reject-confirmation-tbl-div"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
       
        <div class="tab-pane" id="tab1" role="tabpanel" aria-labelledby="tab1">
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
                                                  {{-- <div class="form-group margin-top">
                                                      <label style="color: black;">Item Code</label>
                                                      <select class="form-control text-center sel6 " name="item_code" id="item_code">
                                                          <option value=""> Select Item Code</option>
                                                          @foreach($item_code as $item)
                                                          <option value="{{$item }}">{{$item }}</option>
                                                          @endforeach
                                                        </select>
                                                   </div>

                                                  <div class="form-group margin-top">
                                                      <label style="color: black;">Production Order</label>
                                                       <select class="form-control text-center sel6 " name="prod" id="prod">
                                                          <option value=""> Select Production Order</option>
                                                          @foreach($production_order as $production)
                                                          <option value="{{$production}}">{{$production}}</option>
                                                          @endforeach
                                                        </select>
                                                   </div>
                                                  <div class="form-group margin-top">
                                                      <label style="color: black;">Customer</label>
                                                      <select class="form-control text-center sel6 " name="customer" id="customer">
                                                          <option value=""> Select Customer</option>
                                                          @foreach($customer as $cust)
                                                          <option value="{{$cust}}">{{$cust}}</option>
                                                          @endforeach
                                                        </select>
                                                   </div> --}}
                                                   
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
                                                  {{-- <div class="form-group margin-top">
                                                      <label style="color: black;">Item Code</label>
                                                      <select class="form-control text-center sel3 " name="item_code_painting" id="item_code_painting">
                                                          <option value=""> Select Item Code</option>
                                                          @foreach($item_code as $item)
                                                          <option value="{{$item}}">{{$item}}</option>
                                                          @endforeach
                                                        </select>
                                                   </div>

                                                  <div class="form-group margin-top">
                                                      <label style="color: black;">Production Order</label>
                                                       <select class="form-control text-center sel3 " name="prod_painting" id="prod_painting">
                                                          <option value=""> Select Production Order</option>
                                                          @foreach($production_order as $production)
                                                          <option value="{{$production}}">{{$production}}</option>
                                                          @endforeach
                                                        </select>
                                                   </div>
                                                  <div class="form-group margin-top">
                                                      <label style="color: black;">Customer</label>
                                                      <select class="form-control text-center sel3 " name="customer_painting" id="customer_painting">
                                                          <option value=""> Select Customer</option>
                                                          @foreach($customer as $cust)
                                                          <option value="{{$cust}}">{{$cust}}</option>
                                                          @endforeach
                                                        </select>
                                                   </div>
                                                    --}}
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
                                                {{-- <div class="form-group margin-top">
                                                    <label style="color: black;">Item Code</label>
                                                    <select class="form-control text-center sel4 " name="item_code_assem" id="item_code_assem">
                                                        <option value=""> Select Item Code</option>
                                                        @foreach($item_code as $item)
                                                        <option value="{{$item}}">{{$item}}</option>
                                                        @endforeach
                                                      </select>
                                                 </div>

                                                <div class="form-group margin-top">
                                                    <label style="color: black;">Production Order</label>
                                                     <select class="form-control text-center sel4 " name="prod_assem" id="prod_assem">
                                                        <option value=""> Select Production Order</option>
                                                        @foreach($production_order as $production)
                                                        <option value="{{$production}}">{{$production}}</option>
                                                        @endforeach
                                                      </select>
                                                 </div>
                                                <div class="form-group margin-top">
                                                    <label style="color: black;">Customer</label>
                                                    <select class="form-control text-center sel4 " name="customer_assem" id="customer_assem">
                                                        <option value=""> Select Customer</option>
                                                        @foreach($customer as $cust)
                                                        <option value="{{$cust}}">{{$cust}}</option>
                                                        @endforeach
                                                      </select>
                                                 </div>
                                                  --}}
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
                <div class="card-body p-1">
                  <div class="row m-0">
                    <div class="col-md-12 m-0">
                      <div class="row">
                        <div class="col-md-12 m-0 p-0" style="margin-top: -50px;">
                          <ul class="nav nav-tabs" role="tablist" id="reject-dashboard-tabs">
                            <li class="nav-item">
                              <a class="nav-link active" id="fab_tab_reject" data-toggle="tab" href="#fab0" role="tab" aria-controls="fab0" aria-selected="true">Fabrication</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link " id="pain_tab_reject" data-toggle="tab" href="#pan1" role="tab" aria-controls="pan1" aria-selected="false">Painting</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link " id="assem_tab_reject" data-toggle="tab" href="#assem2" role="tab" aria-controls="assem2" aria-selected="false" id="olu_click">Wiring and Assembly</a>
                            </li>
                          </ul>
                          <div class="tab-content" style="min-height: 500px;">
                            {{-- Fabrication Rejection Report --}}
                            <div class="tab-pane active" id="fab0" role="tabpanel" aria-labelledby="fab0">
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="card mb-0" style="border-radius: 0 0 3px 3px;">
                                    <div class="card-body">
                                      <div class="row">
                                        <div class="col-md-6 offset-md-6">
                                          <div class="row">
                                            <div class="col-md-4 p-0">
                                              <div class="form-group text-right">
                                                <label for="fab_reject_filter" class="text-dark font-weight-bold m-2 p-0" style="font-size: 11pt;">Reject Category:</label>
                                              </div>
                                            </div>
                                            <div class="col-md-4 p-0">
                                              <div class="form-group">
                                                <select class="form-control form-control-lg text-center class-dynamic m-0" name="fab_reject_filter" id="fab_reject_filter">
                                                  @foreach($reject_category as $rows)
                                                    <option value="{{$rows->reject_category_id}}">{{$rows->reject_category_name}}</option>
                                                  @endforeach
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-1 p-0">
                                              <div class="form-group text-right">
                                                <label for="fab_yearpicker" class="text-dark font-weight-bold m-2 p-0" style="font-size: 11pt;">Year:</label>
                                              </div>
                                            </div>
                                            <div class="col-md-2 p-0">
                                              <div class="form-group">
                                                <select id="fab_yearpicker" style="font-weight: bolder;" name="fab_yearpicker" class="form-control form-control-lg">
                                                  <option value="2017" {{ date('Y') == 2017 ? 'selected' : '' }}>2017</option>
                                                  <option value="2018" {{ date('Y') == 2018 ? 'selected' : '' }}>2018</option>
                                                  <option value="2019" {{ date('Y') == 2019 ? 'selected' : '' }}>2019</option>
                                                  <option value="2020" {{ date('Y') == 2020 ? 'selected' : '' }}>2020</option>
                                                  <option value="2021" {{ date('Y') == 2021 ? 'selected' : '' }}>2021</option>
                                                  <option value="2022" {{ date('Y') == 2022 ? 'selected' : '' }}>2022</option>
                                                  <option value="2023" {{ date('Y') == 2023 ? 'selected' : '' }}>2023</option>
                                                  <option value="2024" {{ date('Y') == 2024 ? 'selected' : '' }}>2024</option>
                                                  <option value="2025" {{ date('Y') == 2025 ? 'selected' : '' }}>2025</option>
                                                  <option value="2026" {{ date('Y') == 2026 ? 'selected' : '' }}>2026</option>
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-1 p-0">
                                              <img src="{{ asset('img/print.png') }}" width="35" class="ml-3 mt-1 print-rejection-report-btn" data-operation-id="1">
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-12 m-0">
                                        <div class="row">
                                          <div class="col-md-6">
                                            <div class="card">
                                              <div class="card-body">
                                                <div class="col-md-12">
                                                  <h5 id="label_fab_chart_reject" class="text-center font-weight-bold"></h5>
                                                    <canvas id="tbl_fab_reject_report_chart" height="120"></canvas>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="card">
                                              <div class="card-body">
                                                <div class="col-md-12">
                                                  <h5 id="label_fab_rate_chart_reject" class="text-center font-weight-bold"></h5>
                                                  <canvas id="tbl_fab_rate_report_chart" height="120"></canvas>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-md-12">
                                            <div class="row">
                                              <div class="col-md-12" style="">
                                                <div id="tbl_fab_log_reject_report" style="width: 100%;overflow: auto;"></div>
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
                            {{-- Painting Rejection Report --}}
                            <div class="tab-pane" id="pan1" role="tabpanel" aria-labelledby="pan1">
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="card mb-0" style="border-radius: 0 0 3px 3px;">
                                    <div class="card-body">
                                      <div class="row">
                                        <div class="col-md-6 offset-md-6">
                                          <div class="row">
                                            <div class="col-md-4 p-0">
                                              <div class="form-group text-right">
                                                <label for="pain_reject_filter" class="text-dark font-weight-bold m-2 p-0" style="font-size: 11pt;">Reject Category:</label>
                                              </div>
                                            </div>
                                            <div class="col-md-4 p-0">
                                              <div class="form-group">
                                                <select class="form-control form-control-lg text-center class-dynamic m-0" name="pain_reject_filter" id="pain_reject_filter">
                                                  @foreach($reject_category as $rows)
                                                    <option value="{{$rows->reject_category_id}}">{{$rows->reject_category_name}}</option>
                                                  @endforeach
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-1 p-0">
                                              <div class="form-group text-right">
                                                <label for="pain_yearpicker" class="text-dark font-weight-bold m-2 p-0" style="font-size: 11pt;">Year:</label>
                                              </div>
                                            </div>
                                            <div class="col-md-2 p-0">
                                              <div class="form-group">
                                                <select id="pain_yearpicker" style="font-weight: bolder;" name="pain_yearpicker" class="form-control form-control-lg">
                                                  <option value="2017" {{ date('Y') == 2017 ? 'selected' : '' }}>2017</option>
                                                  <option value="2018" {{ date('Y') == 2018 ? 'selected' : '' }}>2018</option>
                                                  <option value="2019" {{ date('Y') == 2019 ? 'selected' : '' }}>2019</option>
                                                  <option value="2020" {{ date('Y') == 2020 ? 'selected' : '' }}>2020</option>
                                                  <option value="2021" {{ date('Y') == 2021 ? 'selected' : '' }}>2021</option>
                                                  <option value="2022" {{ date('Y') == 2022 ? 'selected' : '' }}>2022</option>
                                                  <option value="2023" {{ date('Y') == 2023 ? 'selected' : '' }}>2023</option>
                                                  <option value="2024" {{ date('Y') == 2024 ? 'selected' : '' }}>2024</option>
                                                  <option value="2025" {{ date('Y') == 2025 ? 'selected' : '' }}>2025</option>
                                                  <option value="2026" {{ date('Y') == 2026 ? 'selected' : '' }}>2026</option>
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-1 p-0">
                                              <img src="{{ asset('img/print.png') }}" width="35" class="ml-3 mt-1 print-rejection-report-btn" data-operation-id="2">
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-12 m-0">
                                        <div class="row">
                                          <div class="col-md-6">
                                            <div class="card">
                                              <div class="card-body">
                                                <div class="col-md-12">
                                                  <h5 id="label_pain_chart_reject" class="text-center font-weight-bold"></h5>
                                                    <canvas id="tbl_pain_reject_report_chart" height="120"></canvas>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="card">
                                              <div class="card-body">
                                                <div class="col-md-12">
                                                  <h5 id="label_pain_rate_chart_reject" class="text-center font-weight-bold"></h5>
                                                  <canvas id="tbl_pain_rate_report_chart" height="120"></canvas>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-md-12">
                                            <div class="row">
                                              <div class="col-md-12" style="">
                                                <div id="tbl_pain_log_reject_report" style="width: 100%;overflow: auto;"></div>
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
                            {{-- Assembly Rejection Report --}}
                            <div class="tab-pane" id="assem2" role="tabpanel" aria-labelledby="assem2">
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="card mb-0" style="border-radius: 0 0 3px 3px;">
                                    <div class="card-body">
                                      <div class="row">
                                        <div class="col-md-6 offset-md-6">
                                          <div class="row">
                                            <div class="col-md-4 p-0">
                                              <div class="form-group text-right">
                                                <label for="assem_reject_filter" class="text-dark font-weight-bold m-2 p-0" style="font-size: 11pt;">Reject Category:</label>
                                              </div>
                                            </div>
                                            <div class="col-md-4 p-0">
                                              <div class="form-group">
                                                <select class="form-control form-control-lg text-center class-dynamic m-0" name="assem_reject_filter" id="assem_reject_filter">
                                                  @foreach($reject_category as $rows)
                                                    <option value="{{$rows->reject_category_id}}">{{$rows->reject_category_name}}</option>
                                                  @endforeach
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-1 p-0">
                                              <div class="form-group text-right">
                                                <label for="assem_yearpicker" class="text-dark font-weight-bold m-2 p-0" style="font-size: 11pt;">Year:</label>
                                              </div>
                                            </div>
                                            <div class="col-md-2 p-0">
                                              <div class="form-group">
                                                <select id="assem_yearpicker" style="font-weight: bolder;" name="assem_yearpicker" class="form-control form-control-lg">
                                                  <option value="2017" {{ date('Y') == 2017 ? 'selected' : '' }}>2017</option>
                                                  <option value="2018" {{ date('Y') == 2018 ? 'selected' : '' }}>2018</option>
                                                  <option value="2019" {{ date('Y') == 2019 ? 'selected' : '' }}>2019</option>
                                                  <option value="2020" {{ date('Y') == 2020 ? 'selected' : '' }}>2020</option>
                                                  <option value="2021" {{ date('Y') == 2021 ? 'selected' : '' }}>2021</option>
                                                  <option value="2022" {{ date('Y') == 2022 ? 'selected' : '' }}>2022</option>
                                                  <option value="2023" {{ date('Y') == 2023 ? 'selected' : '' }}>2023</option>
                                                  <option value="2024" {{ date('Y') == 2024 ? 'selected' : '' }}>2024</option>
                                                  <option value="2025" {{ date('Y') == 2025 ? 'selected' : '' }}>2025</option>
                                                  <option value="2026" {{ date('Y') == 2026 ? 'selected' : '' }}>2026</option>
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-1 p-0">
                                              <img src="{{ asset('img/print.png') }}" width="35" class="ml-3 mt-1 print-rejection-report-btn" data-operation-id="3">
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-12 m-0">
                                        <div class="row">
                                          <div class="col-md-6">
                                            <div class="card">
                                              <div class="card-body">
                                                <div class="col-md-12">
                                                  <h5 id="label_assem_chart_reject" class="text-center font-weight-bold"></h5>
                                                    <canvas id="tbl_assem_reject_report_chart" height="120"></canvas>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="card">
                                              <div class="card-body">
                                                <div class="col-md-12">
                                                  <h5 id="label_assem_rate_chart_reject" class="text-center font-weight-bold"></h5>
                                                  <canvas id="tbl_assem_rate_report_chart" height="120"></canvas>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-md-12">
                                            <div class="row">
                                              <div class="col-md-12">
                                                <div id="tbl_assem_log_reject_report" style="width: 100%;overflow: auto;"></div>
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
</style>
@include('quality_inspection.modal_inspection')
<div class="modal fade" id="print-report-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="min-width: 78%;">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #0277BD;">
        <h5 class="modal-title font-weight-bold">Print Report</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="min-height: 500px;">
        <div class="row">
          <div class="col-md-12" style="overflow: hidden;">
            <iframe src="#" id="print-report-iframe" class="d-no1ne zoom-frame" height="100%" width="100%" style="min-height: 800px;"></iframe>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="print-report-btn">Print</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
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

    $(document).on('click', '.print-rejection-report-btn', function(e){
      var a = $(this).parent().parent();
      var reject_category = a.find('select').eq(0).val();
      var reject_cat_name = a.find('select option:selected').eq(0).text();      
      var operation = $(this).data('operation-id');
      var year = a.find('select').eq(1).val();
      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }

      $('#print-report-iframe').attr('src', "/print_qa_rejection_report?" + $.param( data ));

      $('#print-report-modal').modal('show');
    });

    $('#print-report-modal').on('hidden.bs.modal', function (e) {
      $('#print-report-iframe').attr('src', "");
    });

    $('#print-report-btn').click(function(e){
      $("#print-report-iframe").get(0).contentWindow.print();
    });

    $(document).on('change', '#fab_reject_filter', function(event){
      tbl_fab_log_reject_report('#tbl_fab_log_reject_report');
      fab_optyStats();
      tbl_fab_reject_rate_chart();
    });
  
    $(document).on('change', '#fab_yearpicker', function(event){
      tbl_fab_log_reject_report('#tbl_fab_log_reject_report');
      fab_optyStats();
      tbl_fab_reject_rate_chart();
    });

    $(document).on('change', '#assem_reject_filter', function(event){
      tbl_assem_log_reject_report();
      assem_optyStats();
      tbl_assem_reject_rate_chart();
    }); 
  
    $(document).on('change', '#assem_yearpicker', function(event){
      tbl_assem_log_reject_report();
      assem_optyStats();
      tbl_assem_reject_rate_chart();
    });

    $(document).on('change', '#pain_reject_filter', function(event){
      tbl_pain_log_reject_report();
      pain_optyStats();
      tbl_pain_reject_rate_chart();
    });

    $(document).on('change', '#pain_yearpicker', function(event){
      tbl_pain_log_reject_report();
      pain_optyStats();
      tbl_pain_reject_rate_chart();
    });

    

    function tbl_fab_log_reject_report(div_id){
      var reject_category = $('#fab_reject_filter').val();  
      var reject_cat_name = $('#fab_reject_filter option:selected').text();      
      var operation = 1;
      var year = $('#fab_yearpicker').val();
      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }

      $.ajax({
        url:"/rejection_report",
        type:"GET",
        data: data,
        success:function(data){
          $(div_id).html(data);
        }
      });
    }

    function tbl_pain_log_reject_report(){
      var reject_category = $('#pain_reject_filter').val();  
      var reject_cat_name = $('#pain_reject_filter option:selected').text();      
      var operation = 2;
      var year = $('#pain_yearpicker').val();
      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }

      $.ajax({
        url:"/rejection_report",
        type:"GET",
        data: data,
        success:function(data){
          $('#tbl_pain_log_reject_report').html(data);
        }
      });
    }

    function tbl_assem_log_reject_report(){
      var reject_category = $('#assem_reject_filter').val();  
      var reject_cat_name = $('#assem_reject_filter option:selected').text();      
      var operation = 3;
      var year = $('#assem_yearpicker').val();
      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }
      $.ajax({
        url:"/rejection_report",
        type:"GET",
        data: data,
        success:function(data){
          $('#tbl_assem_log_reject_report').html(data);
        }
      });
    }

    function fab_optyStats(){
      var reject_category = $('#fab_reject_filter').val();  
      var reject_cat_name = $('#fab_reject_filter option:selected').text();      
      var operation = 1;
      var year = $('#fab_yearpicker').val();
      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }

      $('#label_fab_chart_reject').text(reject_cat_name + ' Reject ('+ year +')');

      $.ajax({
        url: "/rejection_report_chart",
        method: "GET",
        data: data,
        success: function(data) {
          var reject = [];
          var val = [];
          var series=[];

          for(var i in data.year) {
            reject.push(data.year[i].reject);
            series.push(data.year[i].series);
            val.push(data.year[i].per_month);
          }

          var chartdata = {
            labels: series,
            data1:reject,
            datasets : [{
              data: val,
              backgroundColor: '#2874a6',
              borderColor: "#2874a6",
              borderWidth: 3,
              label: "Total Reject/s",
            }]
          };

          var ctx = $("#tbl_fab_reject_report_chart");
          if (window.fab_optyCtx != undefined) {
              window.fab_optyCtx.destroy();
          }

          window.fab_optyCtx = new Chart(ctx, {
            type: 'bar',
            data: chartdata,
            options: {
            tooltips: {
              callbacks: {
                title: function (t, d) {
                  return d['data1'][t[0]['index']];
                },
              },
            },
              responsive: true,
              legend: {
                position: 'top',
                labels:{
                  boxWidth: 11
                }
              },
            }
          });
        },
        error: function(data) {
          alert('Error fetching data!');
        }
      });
    }

    function pain_optyStats(){
      var reject_category = $('#pain_reject_filter').val();
      var reject_cat_name = $('#pain_reject_filter option:selected').text();      
      var operation = 2;
      var year = $('#pain_yearpicker').val();
      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }
      $('#label_pain_chart_reject').text(reject_cat_name + ' Reject ('+ year +')');
      $.ajax({
        url: "/rejection_report_chart",
        method: "GET",
        data: data,
        success: function(data) {
          var reject = [];
          var val = [];
          var series=[];
          
          for(var i in data.year) {
            reject.push(data.year[i].reject);
            series.push(data.year[i].series);
            val.push(data.year[i].per_month);
          }
          
          var chartdata = {
            labels: series,
            data1:reject,
            datasets : [{
              data: val,
              backgroundColor: '#2874a6',
              borderColor: "#2874a6",
              borderWidth: 3,
              label: "Total Reject/s",
            }]
          };
          
          var ctx = $("#tbl_pain_reject_report_chart");
          if (window.pain_optyCtx != undefined) {
            window.pain_optyCtx.destroy();
          }
          
          window.pain_optyCtx = new Chart(ctx, {
            type: 'bar',
            data: chartdata,
            options: {
              tooltips: {
                callbacks: {
                  title: function (t, d) {
                    return d['data1'][t[0]['index']];
                  },
                },
              },
              responsive: true,
              legend: {
                position: 'top',
                labels:{
                  boxWidth: 11
                }
              },
            }
          });
        },
        error: function(data) {
          alert('Error fetching data!');
        }
      });
    }

    function assem_optyStats(){
      var reject_category = $('#assem_reject_filter').val();  
      var reject_cat_name = $('#assem_reject_filter option:selected').text();      
      var operation = 3;
      var year = $('#assem_yearpicker').val();

      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }

      $('#label_assem_chart_reject').text(reject_cat_name + ' Reject ('+ year +')');

      $.ajax({
        url: "/rejection_report_chart",
        method: "GET",
        data: data,
        success: function(data) {
          var reject = [];
          var val = [];
          var series=[];
          
          for(var i in data.year) {
            reject.push(data.year[i].reject);
            series.push(data.year[i].series);
            val.push(data.year[i].per_month);
          }
          
          var chartdata = {
            labels: series,
            data1:reject,
            datasets : [{
              data: val,
              backgroundColor: '#2874a6',
              borderColor: "#2874a6",
              borderWidth: 3,
              label: "Total Reject/s",
            }]
          };
          
          var ctx = $("#tbl_assem_reject_report_chart");
          if (window.assem_optyCtx != undefined) {
            window.assem_optyCtx.destroy();
          }
          
          window.assem_optyCtx = new Chart(ctx, {
            type: 'bar',
            data: chartdata,
            options: {
              tooltips: {
                callbacks: {
                  title: function (t, d) {
                    return d['data1'][t[0]['index']];
                  },
                },
              },
              responsive: true,
              legend: {
                position: 'top',
                labels:{
                  boxWidth: 11
                }
              },
            }
          });
        },
        error: function(data) {
          alert('Error fetching data!');
        }
      });
      
    }

    function tbl_fab_reject_rate_chart(){
      var reject_category = $('#fab_reject_filter').val();  
      var reject_cat_name = $('#fab_reject_filter option:selected').text();      
      var operation = 1
      var year = $('#fab_yearpicker').val();
      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }

      $('#label_fab_rate_chart_reject').text('Reject Rate ('+ year +')');

      $.ajax({
        url: "/rejection_report_chart",
        method: "GET",
        data: data,
        success: function(data) {
          var numberWithCommas = function(x) {
            return x.substring(0,10) + '...';
          };

          var reject = [];
          var target = [];
          var planned =[];
          var rate =[];
          var series =[];

          for(var i in data.year) {
            rate.push(data.year[i].per_rate);
            planned.push(data.year[i].target);
            reject.push(data.year[i].reject);
            series.push(data.year[i].series);
          }
          
          var chartdata = {
            data1:reject,
            labels: series,
            datasets : [{
              data: rate,
              backgroundColor: '#3cba9f',
              borderColor: "#3cba9f",
              label: "Reject Rate",
              fill: false
            },
            {
              data: planned,
              backgroundColor: '#3e95cd',
              borderColor: "#3e95cd",
              label: "Target",
              fill: false
            }]
          };
          
          var ctx = $("#tbl_fab_rate_report_chart");

          if (window.tbl_chartCtx != undefined) {
              window.tbl_chartCtx.destroy();
          }

          window.tbl_chartCtx = new Chart(ctx, {
            type: 'line',
            data: chartdata,
            options: {
              tooltips: {
                callbacks: {
                  title: function (t, d) {
                    return d['data1'][t[0]['index']];
                  },
                },
              },
              responsive: true,
              legend: {
                position: 'top',
                labels:{
                  boxWidth: 11
                }
              },
              elements: {
                line: {
                  tension: 0 // disables bezier curves
                }
              },
              scales: {         
                xAxes: [
                  { 
                    ticks: {
                      maxRotation: 90,
                      callbacks: {
                        title: function (tooltipItems, data) {
                          return data.labels[tooltipItems[0].index]
                        }
                      },
                    }, 
                  }
                ]
              }
            }
          });
        },
        error: function(data) {
          alert('Error fetching data!');
        }
      });
    }

    function tbl_pain_reject_rate_chart(){
      var reject_category = $('#pain_reject_filter').val();  
      var reject_cat_name = $('#pain_reject_filter option:selected').text();      
      var operation = 2;
      var year = $('#pain_yearpicker').val();
      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }

      $('#label_pain_rate_chart_reject').text('Reject Rate ('+ year +')');
      
      $.ajax({
        url: "/rejection_report_chart",
        method: "GET",
        data: data,
        success: function(data) {
          var numberWithCommas = function(x) {
            return x.substring(0,10) + '...';
          };

          var reject = [];
          var target = [];
          var planned =[];
          var rate =[];
          var series =[];

          for(var i in data.year) {
            rate.push(data.year[i].per_rate);
            planned.push(data.year[i].target);
            reject.push(data.year[i].reject);
            series.push(data.year[i].series);
          }

          var chartdata = {
            data1:reject,
            labels: series,
            datasets : [{
              data: rate,
              backgroundColor: '#3cba9f',
              borderColor: "#3cba9f",
              label: "Reject Rate",
              fill: false
            },
            {
              data: planned,
              backgroundColor: '#3e95cd',
              borderColor: "#3e95cd",
              label: "Target",
              fill: false
            }]
          };
          
          var ctx = $("#tbl_pain_rate_report_chart");
          
          if (window.tbl_pain_chartCtx != undefined) {
            window.tbl_pain_chartCtx.destroy();
          }
          
          window.tbl_pain_chartCtx = new Chart(ctx, {
            type: 'line',
            data: chartdata,
            options: {
              tooltips: {
                callbacks: {
                  title: function (t, d) {
                    return d['data1'][t[0]['index']];
                  },
                },
              },
              responsive: true,
              legend: {
                position: 'top',
                labels:{
                  boxWidth: 11
                }
              },
              elements: {
                line: {
                  tension: 0 // disables bezier curves
                }
              },
              scales: {         
                xAxes: [
                  { 
                    ticks: {
                      maxRotation: 90,
                      callbacks: {
                        title: function (tooltipItems, data) {
                          return data.labels[tooltipItems[0].index]
                        }
                      },
                    }, 
                  }
                ]
              }
            }
          });
        },
        error: function(data) {
          alert('Error fetching data!');
        }
      });
    }

    function tbl_assem_reject_rate_chart(){
      var reject_category = $('#assem_reject_filter').val();  
      var reject_cat_name = $('#assem_reject_filter option:selected').text();      
      var operation = 3;
      var year = $('#assem_yearpicker').val();

      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }

      $('#label_assem_rate_chart_reject').text('Reject Rate ('+ year +')');
      
      $.ajax({
        url: "/rejection_report_chart",
        method: "GET",
        data: data,
        success: function(data) {
          var numberWithCommas = function(x) {
            return x.substring(0,10) + '...';
          };

          var reject = [];
          var target = [];
          var planned =[];
          var rate =[];
          var series =[];

          for(var i in data.year) {
            rate.push(data.year[i].per_rate);
            planned.push(data.year[i].target);
            reject.push(data.year[i].reject);
            series.push(data.year[i].series);
          }
      
          var chartdata = {
            data1:reject,
            labels: series,
            datasets : [{
              data: rate,
              backgroundColor: '#3cba9f',
              borderColor: "#3cba9f",
              label: "Reject Rate",
              fill: false
            },
            {
              data: planned,
              backgroundColor: '#3e95cd',
              borderColor: "#3e95cd",
              label: "Target",
              fill: false
            }]
          };
          
          var ctx = $("#tbl_assem_rate_report_chart");
          
          if (window.tbl_assem_chartCtx != undefined) {
            window.tbl_assem_chartCtx.destroy();
          }
          
          window.tbl_assem_chartCtx = new Chart(ctx, {
            type: 'line',
            data: chartdata,
            options: {
              tooltips: {
                callbacks: {
                  title: function (t, d) {
                    return d['data1'][t[0]['index']];
                  },
                },
              },
              responsive: true,
              legend: {
                position: 'top',
                labels:{
                  boxWidth: 11
                }
              },
              elements: {
                line: {
                  tension: 0 // disables bezier curves
                }
              },
              scales: {         
                xAxes: [
                  { 
                    ticks: {
                      maxRotation: 90,
                      callbacks: {
                        title: function (tooltipItems, data) {
                          return data.labels[tooltipItems[0].index]
                        }
                      },
                    }, 
                  }
                ]
              }
            }
            
          });
        },
        error: function(data) {
          alert('Error fetching data!');
        }
      });
    }

    $(document).on('click', '#pain_tab_reject', function(event){
      tbl_pain_log_reject_report();
      pain_optyStats();
      tbl_pain_reject_rate_chart();
    });
    $(document).on('click', '#assem_tab_reject', function(event){
      tbl_assem_log_reject_report();
      assem_optyStats();
      tbl_assem_reject_rate_chart();
    });

    $(document).on('click', '#fab_tab_reject', function(event){
      tbl_fab_log_reject_report('#tbl_fab_log_reject_report');
      fab_optyStats();
      tbl_fab_reject_rate_chart();
    });

    var active_tab= $('#reject-dashboard-tabs .active').text();
    if(active_tab == "Fabrication"){
      $('#fab_tab_reject').trigger('click');
    }


    get_quick_view_data();
    get_reject_for_confirmation();
  
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