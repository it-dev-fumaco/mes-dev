@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'settings_module',
])

@section('content')
@include('modals.add_workstation_modal')
@include('modals.add_machine_modal')
@include('modals.add_process_modal')
@include('modals.process_assignment_modal')
@include('modals.process_profile')
@include('modals.delete_assigned_machine_to_process_modal')
@include('modals.edit_process_list_modal')
@include('modals.delete_process_setup_list_modal')
@include('modals.add_shift_modal')
@include('modals.edit_shift_list_modal')
@include('modals.delete_shift_list_modal')
@include('modals.add_operation_modal')
@include('modals.edit_operation_modal')
@include('modals.add_shift_schedule_modal')
@include('modals.edit_shift_schedule_modal')
@include('modals.delete_shift_schedule_modal')
@include('modals.edit_workstation_modal')
@include('modals.delete_workstation_modal')
@include('modals.edit_machineList_modal')
@include('modals.delete_machineList_modal')
@include('modals.add_email_trans')
@include('modals.uom_conversion_modal')
@include('modals.delete_uom_conversion_modal')
<div class="panel-header">
   <div class="header text-center" style="margin-top: -60px;">
      <div class="row">
         <div class="col-md-12">
            <table style="text-align: center; width: 100%;">
               <tr>
                  <td style="width: 36%; border-right: 5px solid white;">
                     <h2 class="title">
                        <div class="pull-right" style="margin-right: 20px;">
                           <span style="display: block; font-size: 15pt;">{{ date('M-d-Y') }}</span>
                           <span style="display: block; font-size: 10pt;">{{ date('l') }}</span>
                        </div>
                     </h2>
                  </td>
                  <td style="width: 14%; border-right: 5px solid white;">
                     <h5 class="title" style="margin: auto;"><span id="current-time">--:--:-- --</span></h5>
                  </td>
                  <td style="width: 50%">
                     <h5 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Settings</h5>
                  </td>
               </tr>
            </table>
         </div>
      </div>
   </div>
</div>
<div class="content">
    <div class="row justify-content-center">
<!--         <div class="col-1"></div>
 -->        <div class="col-2" style="margin-top: -160px; height: 500px; padding-right: 0px;">
            <div class="card" style="background-color: #0277BD;" id="workstation_navbar">
              <div class="card-body" style="padding-bottom: 0;">
                <div class="row">
                  <div class="col-md-12" style="margin-top: -10px;">
                    <h5 class="text-white text-center" style="font-size: 13pt; margin-bottom: 5px;">Settings</h5>
                  </div>
                </div>
                <div class="row" style="background-color: #ffffff;" class="text-center">
                  <div class="col-md-12 text-center">
                        <ul class="nav flex-column workstation_navbar" id="myTab" role="tablist" style="font-size: 10pt;">   <h6 class="text-center" style="padding-top: 10px;">Production</h6>
                            <li class="nav-item">
                                <a class="nav-link active"  href="#process_setup" data-toggle="tab" onclick="tbl_process_setup_list()">Production Process Setup</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#operation_setup" data-toggle="tab">Operation Setup</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " href="#workstation_setup" data-toggle="tab">Workstation Setup</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#machine_setup" data-toggle="tab">Machine Setup</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" href="#machine_setup" data-toggle="tab">Machine Setup</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" href="#late_delivery_setup" data-toggle="tab">Reschedule Delivery Reason Setup</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" href="#cancel_po_setup" data-toggle="tab">Reason/s for Cancellation Setup</a>
                            </li>
                            <h6 class="text-center" style="padding-top: 10px;">User</h6>
                            <li class="nav-item">
                                <a class="nav-link" href="#users_setup" data-toggle="tab">Users Setup</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#email_alert_setup" data-toggle="tab">Email Alert Setup</a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" href="#shift" data-toggle="tab">Shift Setup</a>
                            </li>
                            <h6 class="text-center" style="padding-top: 10px;">Quality Assurance</h6>
                            <li class="nav-item">
                                <a class="nav-link" href="#qa_setup" data-toggle="tab">QA Inspection Setup</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" href="#operator_reject_setup" data-toggle="tab">Operator Reject Setup</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#qa_lot_size" data-toggle="tab">Sampling Plan Setup</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" href="#qa_reject_category" data-toggle="tab">Reject Category Setup</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" href="#material_type_setup" data-toggle="tab">Material Type Setup</a>
                            </li>
                            <h6 class="text-center" style="padding-top: 10px;">Inventory</h6>
                            <li class="nav-item">
                                <a class="nav-link" href="#item_warehouse_setup" data-toggle="tab">Item Warehouse Setup</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#uom-conversion-tab" data-toggle="tab">UoM Conversion</a>
                            </li>
                        </ul>
                      </div>
                </div>
              </div>
          </div>
        </div>
        <div class="col-10" style="margin-top: -160px; min-height:1000px; ">
            <div class="tab-content text-center">
                <div class="tab-pane active" id="process_setup">
                    <div class="card" style="background-color: #0277BD;" >
                        <div class="card-body" style="padding-bottom: 0;">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="text-white font-weight-bold text-left">Production Process Setup</h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control" placeholder="Search" id="search-production-process">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
                                <div class="card card-nav-tabs card-plain">
                                    <div class="col-md-12">
                                      <button type="button" class="btn btn-primary" id="add-process-button" style="float: right;"><i class="now-ui-icons ui-1_simple-add"></i> Add Process</button>
                                        <div class="tbl_process_setup_list_div" id="tbl_process_setup_list_div" style="font-size:11px;"></div>
                                    </div>
                                    
                                </div>
                            </div>        
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="workstation_setup">
                    <div class="card" style="background-color: #0277BD;" >
                        <div class="card-body" style="padding-bottom: 0;">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="text-white font-weight-bold text-left">Workstation Setup</h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control" placeholder="Search" id="search_workstation_setup">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
                                <div class="card card-nav-tabs card-plain">
                                    <div class="col-md-12">
                                      <button type="button" class="btn btn-primary" id="add-workstation-button" style="float: right;"><i class="now-ui-icons ui-1_simple-add"></i> Add Workstation</button>
                                        <div class="table-responsive" id="tbl_workstation_list"></div>
                                    </div>
                                </div>
                            </div>        
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="operation_setup">
                    <div class="card" style="background-color: #0277BD;" >
                        <div class="card-body" style="padding-bottom: 0;">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="text-white font-weight-bold text-left">Operation Setup</h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control" placeholder="Search" id="search_operation_setup">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
                                <div class="card card-nav-tabs card-plain">
                                    <div class="col-md-12">
                                      <button type="button" class="btn btn-primary" id="add-operation-button" style="float: right;"><i class="now-ui-icons ui-1_simple-add"></i> Add Operation</button>
                                        <div class="tbl_operation" id="tbl_operation"></div>
                                    </div>
                                </div>
                            </div>        
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="cancel_po_setup">
                  <div class="card" style="background-color: #0277BD;" >
                      <div class="card-body" style="padding-bottom: 0;">
                          <div class="row">
                              <div class="col-md-8">
                                  <h5 class="text-white font-weight-bold text-left">Reason/s for Cancellation Setup</h5>
                              </div>
                              <div class="col-md-4">
                                  <div class="form-group">
                                      <input type="text" class="form-control form-control" placeholder="Search" id="search_reason_cancelled_po">
                                  </div>
                              </div>
                          </div>
                          <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
                              <div class="card card-nav-tabs card-plain">
                                  <div class="col-md-8 offset-md-2">
                                    <button type="button" class="btn btn-primary" id="add-cancelled-reason-button" style="float: right;"><i class="now-ui-icons ui-1_simple-add"></i> Add Reason</button>
                                      <div class="tbl_reason_for_cancellation_po" id="tbl_reason_for_cancellation_po"></div>
                                  </div>
                              </div>
                          </div>        
                      </div>
                  </div>
              </div>
                <div class="tab-pane" id="users_setup">
                    <div class="card" style="background-color: #0277BD;" >
                        <div class="card-body" style="padding-bottom: 0;">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="text-white font-weight-bold text-left">User Setup</h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control" placeholder="Search" id="search_user_setup">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="background-color: #ffffff;height: auto; min-height: 700px;">
                                <div class="col-md-12">
                                    <div class="nav-tabs-navigation">
                                            <div class="nav-tabs-wrapper">
                                                <ul class="nav nav-tabs" data-tabs="tabs">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" href="#user_list" data-toggle="tab">User List</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="#user_group_list" data-toggle="tab">User Group</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    <div class="tab-content text-center">
                                        <div class="tab-pane active" id="user_list">
                                            <div class="card card-nav-tabs card-plain">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="card" style="min-height: 720px;">
                                                              <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                                <col style="width: 70%;">
                                                                <col style="width: 30%;">
                                                                <tr>
                                                                  <th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>User List</b></th>
                                                                  <td class="text-right">
                                                                    <button type="button" class="btn btn-primary" id="add-user-btn" data-id="1" style="margin: 5px;">
                                                                      <i class="now-ui-icons ui-1_simple-add"></i> Add
                                                                    </button>
                                                                  </td>
                                                                </tr>
                                                              </table>
                                                                <div class="card-body">
                                                                <div id="div-user-table"></div>  
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> 
                                        </div>
                                        <div class="tab-pane" id="user_group_list">
                                            <div class="card card-nav-tabs card-plain">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="card" style="min-height: 720px;">
                                                              <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                                <col style="width: 70%;">
                                                                <col style="width: 30%;">
                                                                <tr>
                                                                  <th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>User Group</b></th>
                                                                  <td class="text-right">
                                                                    <button type="button" class="btn btn-primary" id="add-user-group" style="margin: 5px;">
                                                                      <i class="now-ui-icons ui-1_simple-add"></i> Add
                                                                    </button>
                                                                  </td>
                                                                </tr>
                                                              </table>
                                                                <div class="card-body">
                                                                    <div class="tbl_user_group" id="tbl_user_group"></div>
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
                <div class="tab-pane" id="late_delivery_setup">
                  <div class="card" style="background-color: #0277BD;" >
                      <div class="card-body" style="padding-bottom: 0;">
                          <div class="row">
                              <div class="col-md-8">
                                  <h5 class="text-white font-weight-bold text-left">Reschedule Delivery Reason Setup</h5>
                              </div>
                              <div class="col-md-4">
                                  <div class="form-group">
                                      <input type="text" class="form-control form-control" placeholder="Search" id="search_late_delivery_setup">
                                  </div>
                              </div>
                          </div>
                          <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
                              <div class="card card-nav-tabs card-plain">
                                  <div class="col-md-8 offset-md-2">
                                    <button type="button" class="btn btn-primary" id="add-late-deli-button" style="float: right;"><i class="now-ui-icons ui-1_simple-add"></i> Add Reschedule Delivery Reason </button>
                                      <div id="tbl_late_delivery_list"></div>    
                                  </div>
        
                              </div>
                          </div>        
                      </div>
                  </div>
                </div>
                <div class="tab-pane" id="email_alert_setup">
                    <div class="card" style="background-color: #0277BD;" >
                        <div class="card-body" style="padding-bottom: 0;">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="text-white font-weight-bold text-left">Email Alert Setup</h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control" placeholder="Search" id="search_email_setup">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="background-color: #ffffff;height: auto; min-height: 700px;">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary pull-right" id="email_trans_btn" style="margin: 5px;">
                                      <i class="now-ui-icons ui-1_simple-add"></i> Add Email Alert Recipient
                                    </button>
                                      <div class="tbl_email_trans" id="tbl_email_trans"></div>
                                </div>
                            </div>      
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="machine_setup">
                    <div class="card" style="background-color: #0277BD;" >
                        <div class="card-body" style="padding-bottom: 0;">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="text-white font-weight-bold text-left">Machine Setup</h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control" placeholder="Search" id="search_machine_setup">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
                                
                                <div class="card card-nav-tabs card-plain">
                                    <div class="col-md-12">
                                      <button type="button" class="btn btn-primary" id="add-machine-button" style="float: right;"><i class="now-ui-icons ui-1_simple-add"></i> Add Machine</button>
                                        <div id="tbl_setting_machine_list"></div>    
                                    </div>
          
                                </div>
                            </div>        
                        </div>
                    </div>
                </div>
                
                <div class="tab-pane" id="qa_setup">
                    <div class="card" style="background-color: #0277BD;" >
                        <div class="card-body" style="padding-bottom: 0;">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="text-white font-weight-bold text-left">Quality Inspection Setup</h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control" placeholder="Search" id="search_reject_setup">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="background-color: #ffffff;height: auto; min-height: 700px;">
                                <div class="col-md-12">
                                    <div class="nav-tabs-navigation">
                                        <div class="nav-tabs-wrapper" id="qa_tab">
                                            <ul class="nav nav-tabs" data-tabs="tabs">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-qatab="checklist" href="#inspection_checklist" data-toggle="tab">Inspection Checklist</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-qatab="reject" href="#qa_reject_list" data-toggle="tab">QA Inspection List</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="tab-content text-center">
                                        <div class="tab-pane active" id="inspection_checklist">
                                            <div class="card card-nav-tabs card-plain">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="card" style="min-height: 720px;">
                                                              <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                                <col style="width: 70%;">
                                                                <col style="width: 30%;">
                                                                <tr>
                                                                  <th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>Fabrication</b></th>
                                                                  <td class="text-right">
                                                                    <button type="button" class="btn btn-primary" id="add-checklist-fabrication-button" style="margin: 5px;">
                                                                      <i class="now-ui-icons ui-1_simple-add"></i> Add
                                                                    </button>
                                                                  </td>
                                                                </tr>
                                                              </table>
                                                                <div class="card-body">
                                                                    <div class="tbl_check_list_fabrication" id="tbl_check_list_fabrication"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="card" style="min-height: 720px;">
                                                              <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                                <col style="width: 70%;">
                                                                <col style="width: 30%;">
                                                                <tr>
                                                                  <th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>Painting</b></th>
                                                                  <td class="text-right">
                                                                    <button type="button" class="btn btn-primary" id="add-checklist-painting-button" style="margin: 5px;">
                                                                      <i class="now-ui-icons ui-1_simple-add"></i> Add
                                                                    </button>
                                                                  </td>
                                                                </tr>
                                                              </table>
                                                                <div class="card-body">
                                                                    <div class="tbl_check_list_painting" id="tbl_check_list_painting"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="card" style="min-height: 720px;">
                                                              <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                                <col style="width: 70%;">
                                                                <col style="width: 30%;">
                                                                <tr>
                                                                  <th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>Wiring and Assembly</b></th>
                                                                  <td class="text-right">
                                                                    <button type="button" class="btn btn-primary" id="add-checklist-assembly-button" style="margin: 5px;">
                                                                      <i class="now-ui-icons ui-1_simple-add"></i> Add
                                                                    </button>
                                                                  </td>
                                                                </tr>
                                                              </table>
                                                                <div class="card-body">
                                                                    <div class="tbl_check_list_assembly" id="tbl_check_list_assembly"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div> 
                                        </div>
                                        <div class="tab-pane" id="qa_reject_list">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="card" style="min-height: 720px;">
                                                        <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                        <col style="width: 70%;">
                                                        <col style="width: 30%;">
                                                        <tr>
                                                            <th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>QA Reject List</b></th>
                                                            <td class="text-right">
                                                            <button type="button" class="btn btn-primary" id="add-reject-button" style="margin: 5px;">
                                                                <i class="now-ui-icons ui-1_simple-add"></i> Add
                                                            </button>
                                                            </td>
                                                        </tr>
                                                        </table>
                                                        <div class="card-body">
                                                            <div class="tbl_reject_list" id="tbl_reject_list"></div>
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
                <div class="tab-pane" id="qa_lot_size">
                    <div class="card" style="background-color: #0277BD;" >
                        <div class="card-body" style="padding-bottom: 0;">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="text-white font-weight-bold text-left">Sampling Plan Setup</h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control" placeholder="Search" id="search_machine_setupss">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
                                <div class="card card-nav-tabs card-plain">
                                    <div class="col-md-12">
                                    <div class="row" style="padding-top:10px;">
                                                        <div class="col-md-4">
                                                            <div class="card" style="min-height: 720px;">
                                                              <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                                <col style="width: 70%;">
                                                                <col style="width: 30%;">
                                                                <tr>
                                                                  <th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>Visual(Minor Defects)</b></th>
                                                                  <td class="text-right">
                                                                    <button type="button" class="btn btn-primary add-smpl-plan add-smpl-plan" id="add-visual-button" data-id="1" data-spname="Visual" style="margin: 5px;">
                                                                      <i class="now-ui-icons ui-1_simple-add"></i> Add
                                                                    </button>
                                                                  </td>
                                                                </tr>
                                                              </table>
                                                                <div class="card-body">
                                                                    <div class="tbl_visual" id="tbl_visual"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="card" style="min-height: 720px;">
                                                              <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                                <col style="width: 70%;">
                                                                <col style="width: 30%;">
                                                                <tr>
                                                                  <th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>Variable(Major Defects)</b></th>
                                                                  <td class="text-right">
                                                                    <button type="button" class="btn btn-primary add-smpl-plan" id="add-variable-button" style="margin: 5px;" data-id="2" data-spname="Variable">
                                                                      <i class="now-ui-icons ui-1_simple-add"></i> Add
                                                                    </button>
                                                                  </td>
                                                                </tr>
                                                              </table>
                                                                <div class="card-body">
                                                                    <div class="tbl_variable" id="tbl_variable"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="card" style="min-height: 720px;">
                                                              <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                                <col style="width: 70%;">
                                                                <col style="width: 30%;">
                                                                <tr>
                                                                  <th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>Reliability(Critical Defects)</b></th>
                                                                  <td class="text-right">
                                                                    <button type="button" class="btn btn-primary add-smpl-plan" id="add-reliability-button" style="margin: 5px;" data-id="3" data-spname="Reliability">
                                                                      <i class="now-ui-icons ui-1_simple-add"></i> Add
                                                                    </button>
                                                                  </td>
                                                                </tr>
                                                              </table>
                                                                <div class="card-body">
                                                                    <div class="tbl_reliability" id="tbl_reliability"></div>
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
                <div class="tab-pane" id="operator_reject_setup">
                  <div class="card" style="background-color: #0277BD;" >
                      <div class="card-body" style="padding-bottom: 0;">
                          <div class="row">
                              <div class="col-md-8">
                                  <h5 class="text-white font-weight-bold text-left">Operator Reject Setup</h5>
                              </div>
                              <div class="col-md-4">
                                  <div class="form-group">
                                      <input type="text" class="form-control form-control" placeholder="Search" id="search_operator_reject_setup">
                                  </div>
                              </div>
                          </div>
                          <div class="row" style="background-color: #ffffff;height: auto; min-height: 700px;">
                              <div class="col-md-12">
                                  <div class="nav-tabs-navigation">
                                      <div class="nav-tabs-wrapper" id="operator_tab">
                                          <ul class="nav nav-tabs" data-tabs="tabs">
                                              <li class="nav-item">
                                                  <a class="nav-link active" data-qatab="op_fabrication" href="#op_fabrication" data-toggle="tab">Fabrication</a>
                                              </li>
                                              <li class="nav-item">
                                                  <a class="nav-link" data-qatab="op_painting" href="#op_painting" data-toggle="tab">Painting</a>
                                              </li>
                                              <li class="nav-item">
                                                  <a class="nav-link" data-qatab="op_assembly" href="#op_assembly" data-toggle="tab">Wiring and Assembly</a>
                                              </li>
                                              <li class="nav-item">
                                                <a class="nav-link" data-qatab="op_reject" href="#operator_reject_list" data-toggle="tab">Operator Reject list</a>
                                              </li>
                                          </ul>
                                      </div>
                                  </div>
                                  <div class="tab-content text-center">
                                      <div class="tab-pane" id="qa_reject_list">
                                          <div class="row">
                                              <div class="col-md-12">
                                                  <div class="card" style="min-height: 720px;">
                                                      <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                      <col style="width: 70%;">
                                                      <col style="width: 30%;">
                                                      <tr>
                                                          <th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>QA Reject List</b></th>
                                                          <td class="text-right">
                                                          <button type="button" class="btn btn-primary" id="add-reject-button" style="margin: 5px;">
                                                              <i class="now-ui-icons ui-1_simple-add"></i> Add
                                                          </button>
                                                          </td>
                                                      </tr>
                                                      </table>
                                                      <div class="card-body">
                                                          <div class="tbl_reject_list" id="tbl_reject_list"></div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="tab-pane" id="operator_reject_list">
                                          <div class="row">
                                              <div class="col-md-12">
                                                  <div class="card" style="min-height: 720px;">
                                                      <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                      <col style="width: 70%;">
                                                      <col style="width: 30%;">
                                                      <tr>
                                                          <th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>Operator Reject List</b></th>
                                                          <td class="text-right">
                                                          <button type="button" class="btn btn-primary" id="add-operator-reject-button" style="margin: 5px;">
                                                              <i class="now-ui-icons ui-1_simple-add"></i> Add
                                                          </button>
                                                          </td>
                                                      </tr>
                                                      </table>
                                                      <div class="card-body">
                                                          <div class="tbl_operator_reject_list" id="tbl_operator_reject_list"></div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="tab-pane active" id="op_fabrication">
                                          <div class="row">
                                              <div class="col-md-12">
                                                  <div class="card" style="min-height: 720px;">
                                                    <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                      <col style="width: 70%;">
                                                      <col style="width: 30%;">
                                                      <tr>
                                                        <th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>Fabrication</b></th>
                                                        <td class="text-right">
                                                          <button type="button" class="btn btn-primary" id="add-opchecklist-fabrication-button" style="margin: 5px;">
                                                            <i class="now-ui-icons ui-1_simple-add"></i> Add
                                                          </button>
                                                        </td>
                                                      </tr>
                                                    </table>
                                                      <div class="card-body">
                                                          <div class="tbl_check_list_fabrication" id="tbl_opcheck_list_fabrication"></div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="tab-pane" id="op_painting">
                                          <div class="row">
                                              <div class="col-md-12">
                                                  <div class="card" style="min-height: 720px;">
                                                      <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                        <col style="width: 70%;">
                                                        <col style="width: 30%;">
                                                        <tr>
                                                          <th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>Painting</b></th>
                                                          <td class="text-right">
                                                            <button type="button" class="btn btn-primary" id="add-opchecklist-painting-button" style="margin: 5px;">
                                                              <i class="now-ui-icons ui-1_simple-add"></i> Add
                                                            </button>
                                                          </td>
                                                        </tr>
                                                      </table>
                                                      <div class="card-body">
                                                        <div class="tbl_check_list_painting" id="tbl_opcheck_list_painting"></div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="tab-pane" id="op_assembly">
                                          <div class="row">
                                              <div class="col-md-12">
                                                  <div class="card" style="min-height: 720px;">
                                                      <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                        <tr>
                                                          <td class="text-left" style="padding:13px; font-size: 12pt; margin: 5px;"><b>Wiring and Assembly</b></td>
                                                        </tr>
                                                      </table>
                                                      <div class="card-body">
                                                          <div class="tbl_check_list_assembly" id="tbl_opcheck_list_assembly"></div>
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
                <div class="tab-pane" id="material_type_setup">
                    <div class="card" style="background-color: #0277BD;" >
                        <div class="card-body" style="padding-bottom: 0;">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="text-white font-weight-bold text-left">Material Type Setup</h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control" placeholder="Search" id="search_material_type_setup">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
                                <div class="card card-nav-tabs card-plain">
                                    <div class="col-md-8 offset-md-2">
                                      <button type="button" class="btn btn-primary" id="add-material-type-button" style="float: right;"><i class="now-ui-icons ui-1_simple-add"></i> Add Material Type </button>
                                        <div id="tbl_material_type"></div>    
                                    </div>
                                </div>
                            </div>        
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="qa_reject_category">
                    <div class="card" style="background-color: #0277BD;" >
                        <div class="card-body" style="padding-bottom: 0;">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="text-white font-weight-bold text-left">Reject Category Setup</h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control" placeholder="Search" id="search_reject_category_setup">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
                                <div class="card card-nav-tabs card-plain">
                                    <div class="col-md-12">
                                      <button type="button" class="btn btn-primary" id="add-reject-ctg-button" style="float: right;"><i class="now-ui-icons ui-1_simple-add"></i> Reject Category </button>
                                      <div class="tbl-reject-ctg" id="tbl-reject-ctg"></div>
                                    </div>
                                </div>
                            </div>        
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="item_warehouse_setup">
                    <div class="card" style="background-color: #0277BD;" >
                        <div class="card-body" style="padding-bottom: 0;">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="text-white font-weight-bold text-left">Item Warehouse Setup </h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control" placeholder="Search" id="search_item_warehouse_setup">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
                                <div class="card card-nav-tabs card-plain">
                                    <div class="col-md-12">
                                        <div class="nav-tabs-navigation" style="margin-top: 8px;">
                                            <div class="nav-tabs-wrapper">
                                                <ul class="nav nav-tabs" data-tabs="tabs">
                                                    <li class="nav-item">
                                                        <a class="nav-link active add_icw_operation" href="#icw_fabrication" data-toggle="tab" data-operation="Fabrication" data-values="fab">Fabrication</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link add_icw_operation" href="#icw_painting" data-toggle="tab" data-operation="Painting" data-values="pain">Painting</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link add_icw_operation" href="#icw_assembly" data-toggle="tab" data-operation="Assembly" data-values="assem">Assembly</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link add_icw_operation" href="#icw_workinprogress" data-toggle="tab" data-operation="hide_me" data-values="assem">Work In Progress</a>
                                                    </li>

                                                    
                                                </ul>
                                            </div>
                                        </div>
                                         <button type="button" class="btn btn-primary pull-right" id="add-item-warehouse-button" style="margin: 5px;margin-top: -40px;">
                                            <i class="now-ui-icons ui-1_simple-add"></i> Add Item Warehouse
                                        </button>
                                    <div class="tab-content text-center">
                                        <div class="tab-pane active" id="icw_fabrication">
                                            <div class="card card-nav-tabs card-plain">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="card" style="min-height: 720px;">
                                                              <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                                <col style="width: 70%;">
                                                                <col style="width: 30%;">
                                                                <tr>
                                                                  <th class="text-left" style="padding-left: 20px;padding-top: 10px;padding-bottom:10px; font-size: 12pt;"><b>Fabrication</b></th>
                                                                </tr>
                                                              </table>
                                                                <div class="card-body">
                                                                    <div id="tbl_item_warehouse_list_fabrication"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> 
                                        </div>
                                        <div class="tab-pane" id="icw_painting">
                                            <div class="card card-nav-tabs card-plain">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="card" style="min-height: 720px;">
                                                              <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                                <col style="width: 70%;">
                                                                <col style="width: 30%;">
                                                                <tr>
                                                                  <th class="text-left" style="padding-left: 20px;padding-top: 10px;padding-bottom:10px; font-size: 12pt;"><b>Painting</b></th>
                                                                </tr>
                                                              </table>
                                                                <div class="card-body">
                                                                    <div id="tbl_item_warehouse_list_painting"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div> 
                                        </div>
                                        <div class="tab-pane" id="icw_assembly">
                                            <div class="card card-nav-tabs card-plain">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="card" style="min-height: 720px;">
                                                              <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                                <col style="width: 70%;">
                                                                <col style="width: 30%;">
                                                                <tr>
                                                                  <th class="text-left" style="padding-left: 20px;padding-top: 10px;padding-bottom:10px; font-size: 12pt;"><b>Assembly</b></th>
                                                                </tr>
                                                              </table>
                                                                <div class="card-body">
                                                                    <div id="tbl_item_warehouse_list_assembly"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div> 
                                        </div>
                                        <div class="tab-pane" id="icw_workinprogress">
                                            <div class="card card-nav-tabs card-plain">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="card" style="min-height: 720px;">
                                                              <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                                <col style="width: 70%;">
                                                                <col style="width: 30%;">
                                                                <tr>
                                                                  <th class="text-left" style="padding-left: 20px;padding-top: 10px;padding-bottom:10px; font-size: 12pt;"><b>Work In Progress</b></th>
                                                                </tr>
                                                              </table>
                                                                <div class="card-body">
                                                                    <button type="button" class="btn btn-primary pull-right" id="add-wip-button" style="margin: 5px;">
                                                                        <i class="now-ui-icons ui-1_simple-add"></i> Add WIP
                                                                    </button>
                                                                    <div id="tbl_wip_list"></div>
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
                <div class="tab-pane" id="uom-conversion-tab">
                    <div class="card" style="background-color: #0277BD;" >
                        <div class="card-body" style="padding-bottom: 0;">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="text-white font-weight-bold text-left">UOM Conversion</h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control" placeholder="Search">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
                                <div class="card card-nav-tabs card-plain">
                                    <div class="col-md-8 offset-md-2">
                                        <button type="button" class="btn btn-primary" id="add-uom-conversion-btn" style="float: right;">
                                            <i class="now-ui-icons ui-1_simple-add"></i> Add UoM Conversion
                                        </button>
                                        <div class="table-responsive" id="uom-conversion-tbl"></div>
                                    </div>
                                </div>
                            </div>        
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="shift">
                    <div class="card" style="background-color: #0277BD;" >
                        <div class="card-body" style="padding-bottom: 0;">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="text-white font-weight-bold text-left" style="font-size:20px;">Shift Setup</h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control" placeholder="Search" id="search-cancelled-prod">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
                                <div class="card card-nav-tabs card-plain">
                                    <div class="card-body">
                                        <div class="row">
                                          <div class="col-md-7">
                                            <div class="card" style="min-height: 400px;">
                                              <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                <col style="width: 70%;">
                                                <col style="width: 30%;">
                                                <tr>
                                                  <th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>Shift</b></th>
                                                  <td class="text-right">
                                                    <button type="button" class="btn btn-primary" id="add-shift-button" style="margin: 5px;">
                                                      <i class="now-ui-icons ui-1_simple-add"></i> Add
                                                    </button>
                                                  </td>
                                                </tr>
                                              </table>
                                                <!-- <table class="table">
                                                    <col style="width: 70%;">
                                                    <col style="width: 30%;">
                                                    <thead class="text-white" style="background-color:#34495e  ;border: 0;">
                                                        <th style="text-align: left;padding-left: 20px;"><b>Shift</b></th>
                                                        <th style="font-size: 9pt;">
                                                          <button type="button" class="btn btn-primary" id="add-shift-button" style="float: right;">
                                                            <i class="now-ui-icons ui-1_simple-add"></i> Add
                                                          </button>
                                                        </th>
                                                    </thead>
                                                </table> -->
                                                <div class="card-body">
                                                    <div class="tbl_shift" id="tbl_shift"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                                        <div class="card" style="min-height: 400px;">
                                                          <table class="text-white" style="width: 100%;background-color:#34495e;">
                                                <col style="width: 70%;">
                                                <col style="width: 30%;">
                                                <tr>
                                                  <th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>Shift Schedule</b></th>
                                                  <td class="text-right">
                                                    <button type="button" class="btn btn-primary" id="add-shift-schedule-button" style="margin: 5px;">
                                                      <i class="now-ui-icons ui-1_simple-add"></i> Add
                                                    </button>
                                                  </td>
                                                </tr>
                                              </table>
                                                          <!--   <table class="table" border="0">
                                                                <col style="width: 60%;">
                                                                <col style="width: %;">
                                                                <thead class="text-white" style="background-color:#34495e;">
                                                                    <th style="text-align: left;padding-left: 20px;"><b>Shift Schedule</b></th>
                                                                    <th style="font-size: 9pt;"><button type="button" class="btn btn-primary" id="add-shift-schedule-button" style="float: right;"><i class="now-ui-icons ui-1_simple-add"></i> Add</button></th>
                                                                </thead>
                                                            </table> -->
                                                            <div class="card-body">
                                                                <div class="tbl_shift_schedule_sched"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                     <div class="col-md-6">
                                                <div class="card" style="min-height: 300px;">
                                                  <table class="text-white" style="width: 100%; background-color:#34495e;">
                                                <tr style="line-height: 39px;">
                                                  <th class="text-center" style="font-size: 12pt;"><b>Upcoming Holiday/s</b></th>
                                                </tr>
                                              </table>
                                                  
                                                    <div class="card-body">
                                                    </div>
                                                </div>
                                                </div>
                                                     <div class="col-md-6">
                                                <div class="card" style="min-height: 300px;">
                                                   <table class="text-white" style="width: 100%; background-color:#34495e;">
                                                <tr style="line-height: 39px;">
                                                  <th class="text-center" style="font-size: 12pt;"><b>On Leave / Absent Today</b></th>
                                                </tr>
                                              </table>
                                                   
                                                    <div class="card-body">
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
<!--     <div class="col-1"></div>
 --></div>
</div>


<!-- Modal -->
<div class="modal fade" id="add-user-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/save_user" method="POST" id="add-user-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Add User
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                     <div class="form-group">
                        <label>User ID:</label>
                        <select class="form-control sel2" name="user_access_id" id="sel-user-id-add" >
                          <option value="">Select Employee</option>
                          @foreach($employees as $row)
                          <option value="{{ $row->user_id }}" data-empname="{{ $row->employee_name }}">{{ $row->user_id }} - {{ $row->employee_name }}</option>
                          @endforeach
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Employee Name:</label>
                        <input type="text" class="form-control" name="employee_name" required id="emp-name-input-add">
                     </div>
                     <div class="form-group">
                        <label>Module:</label>
                         <select class="form-control sel2" name="user_group" id="user_group" onchange="change_userrole_add()">
                              <option value="">Select Module</option>
                               @foreach($module as $row)
                              <option value="{{ $row->module }}">{{ $row->module }}</option>
                          @endforeach
                         </select>
                     </div>
                     <div class="form-group">
                        <label>User Role:</label>
                         <select class="form-control sel2" name="user_role" id="user_role">
                              
                         </select>
                     </div>
                     <div class="form-group">
                        <label>Operation:</label>
                        <select class="form-control sel2" name="operation">
                            <option value="">Select Operation</option>
                            @foreach($operations as $row)
                            <option value="{{ $row->operation_id }}">{{ $row->operation_name }}</option>
                            @endforeach
                         </select>
                     </div>
                  </div>

               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>

<!-- Modal -->
<div class="modal fade" id="edit-user-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/update_user" method="POST" id="edit-user-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Edit User<br>
               </h5>
            </div>
            <div class="modal-body">
              <input type="hidden" name="user_id" id="edit-user-id-input">
               <div class="row">
                  <div class="col-md-12">
                     <div class="form-group">
                        <label>User ID:</label>
                        <select class="form-control sel3" name="user_access_id" id="edit_user_id">
                          <option value="">Select Employee</option>
                          @foreach($employees as $row)
                          <option value="{{ $row->user_id }}" data-empname="{{ $row->employee_name }}">{{ $row->user_id }} - {{ $row->employee_name }}</option>
                          @endforeach
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Employee Name:</label>
                        <input type="text" class="form-control" name="employee_name" required id="edit_employee_name">
                     </div>
                     <div class="form-group">
                        <label>Module:</label>
                         <select class="form-control sel3" name="user_group" id="edit_user_group" onchange="change_userrole_edit()">
                              <option value="">Select Module</option>
                               @foreach($module as $row)
                              <option value="{{ $row->module }}">{{ $row->module }}</option>
                          @endforeach
                         </select>
                     </div>
                     <div class="form-group">
                        <label>User Role:</label>
                         <select class="form-control sel3" name="user_role" id="edit_user_role">
                              
                         </select>
                     </div>
                     <div class="form-group">
                        <label>Operation:</label>
                        <select class="form-control sel3" name="operation" id="edit_user_operation">
                            <option value="">Select Operation</option>
                            @foreach($operations as $row)
                            <option value="{{ $row->operation_id }}">{{ $row->operation_name }}</option>
                            @endforeach
                         </select>
                     </div>
                  </div>

               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>

<!-- Modal -->
<div class="modal fade" id="delete-user-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/delete_user" method="POST" id="delete-user-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  Remove User
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                        <input type="hidden" name="user_id">
                        <div class="row">
                           <div class="col-sm-12"style="font-size: 12pt;">
                              Remove <b><span></span></b>?
                           </div>               
                        </div>
                  </div>

               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
<!-- Modal -->
<div class="modal fade" id="add-user-group-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/save_user_group" method="POST" id="add-user-group-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Add User Group
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                     <div class="form-group">
                        <label>Module:</label>
                         <select class="form-control sel11" name="add_user_group" id="add_user_group">
                              <option value="">Select Module</option>
                               @foreach($module as $row)
                              <option value="{{ $row->module }}">{{ $row->module }}</option>
                             @endforeach
                         </select>
                     </div>
                     <div class="form-group">
                        <label>User Role:</label>
                        <select class="form-control sel11" name="add_user_role" id="add_user_role">
                             <option value="Production Supervisor">Production Supervisor</option>
                             <option value="Production Manager">Production Manager</option>
                             <option value="QA Manager">QA Manager</option>
                             <option value="QA Inspector">QA Inspector</option>
                             <option value="Maintenance Manager">Maintenance Manager</option>
                             <option value="Maintenance Staff">Maintenance Staff</option>
                         </select>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
<div class="modal fade" id="edit-user-group-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/update_user_group" method="POST" id="edit-user-group-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Edit User Group
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                <input type="hidden" name="edit_user_group_regis_id" id="edit_user_group_regis_id">
                <input type="hidden" name="edit_orig_module" id="edit_orig_module">
                <input type="hidden" name="edit_orig_role" id="edit_orig_role">
                  <div class="col-md-12">
                     <div class="form-group">
                        <label>Module:</label>
                         <select class="form-control sel12" name="edit_user_group_regis" id="edit_user_group_regis">
                              <option value="">Select Module</option>
                               @foreach($module as $row)
                              <option value="{{ $row->module }}">{{ $row->module }}</option>
                             @endforeach
                         </select>
                     </div>
                     <div class="form-group">
                        <label>User Role:</label>
                        <select class="form-control sel12" name="edit_user_role_regis" id="edit_user_role_regis">
                             <option value="Production Supervisor">Production Supervisor</option>
                             <option value="Production Manager">Production Manager</option>
                             <option value="QA Manager">QA Manager</option>
                             <option value="QA Inspector">QA Inspector</option>
                             <option value="Maintenance Manager">Maintenance Manager</option>
                             <option value="Maintenance Staff">Maintenance Staff</option>
                         </select>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
<div class="modal fade" id="delete-user-group-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/delete_user_group" method="POST" id="delete-user-group-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  Remove User Group
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                        <input type="hidden" name="delete_user_group_id" id="delete_user_group_id">
                        <div class="row">
                           <div class="col-sm-12"style="font-size: 12pt;">
                              <label> Remove User Group <span id="delete_label_user_group" style="font-weight: bold;"></span></label>?
                           </div>               
                        </div>
                  </div>

               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>


<!-- Reject Setup -->

<div class="modal fade" id="add-reject-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document" style="min-width: 60%;">
      <form action="/save_reject_list" method="POST" id="save-reject-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title "> Add Reject Checklist
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <div  style="margin:-5px; ">
                      <a href="#" class="btn btn-primary add-row pull-right" >
                        <i class="now-ui-icons ui-1_simple-add"></i>Add
                      </a>
                    </div>
                    <div class="row">
                      <div class="col-md-4" style="display:none;" id="div_op_operation">
                        <div class="form-group">
                            <label>Operation:</label>
                            <select class="form-controls sel6" name="op_operation" id="op_operation" class="op_operation">
                            </select>
                        </div>
                      </div>
                        <div class="col-md-4">
                          <div class="form-group">
                              <label>Category:</label>
                              <select class="form-controls sel6" name="reject_category" id="reject_category" class="reject_category" required="">
                              </select>
                          </div>
                        </div>
                    </div>
                    
                     <input type="hidden" id="reject_owner" name="reject_owner">
                     
                  <table class="table" id="addreject-table" style="font-size: 10px;">
                     <thead>
                        <tr>
                           <th style=" text-align: center;font-weight: bold;">No.</th>
                           <th style=" text-align: center;font-weight: bold;" id="checklist_th" >Reject Checklist:</th>
                           <th style=" text-align: center;font-weight: bold;">Reject Reason</th>
                           <th style=" text-align: center;font-weight: bold;">Material Type</th>
                           <th style=" text-align: center;font-weight: bold;">Responsible</th>
                           <th style=" text-align: center;font-weight: bold;" >Recommended Action</th>
                           <th style=" text-align: center;font-weight: bold;"></th>
                        </tr>
                     </thead>
                     <tbody class="table-body text-center">
                        <tr>
                           
                        </tr>
                     </tbody>
                  </table>

                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
<div class="modal fade" id="edit-reject-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/update_reject_list" method="POST" id="update-reject-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Edit Reject list
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                      <div class="form-group" id="div_operation_edit">
                          <label>Operation:</label>
                          <select class="form-control sel7" name="edit_reject_operation" id="edit_reject_operation">
                          </select>
                          <input type="hidden" class="form-control" name="orig_reject_operation" id="orig_reject_operation">
                      </div>
                      <div class="form-group">
                          <input type="hidden" class="form-control" name="edit_id_reject" required id="edit_id_reject">
                          <label>Category:</label>
                          <select class="form-control sel7" name="edit_reject_category" id="edit_reject_category" required>
                          </select>
                          <input type="hidden" class="form-control" name="orig_reject_category" required id="orig_reject_category">
                      </div>
                      <input type="hidden" name="reloadtbl_edit" id="reloadtbl_edit">
                      <input type="hidden"  name="edit_reject_owner" id="edit_reject_owner" class="edit_reject_owner">
                      <div class="form-group" id="edit_reject_checklist_div">
                          <label>Reject Checklist:</label>
                          <input type="text" class="form-control" name="edit_reject_checklist" id="edit_reject_checklist">
                          <input type="hidden" class="form-control" name="orig_reject_checklist" id="orig_reject_checklist">
                      </div>
                      <div class="form-group">
                          <label>Reject Reason:</label>
                          <input type="text" class="form-control" name="edit_reject_reason" required id="edit_reject_reason">
                          <input type="hidden" class="form-control" name="orig_reject_reason" required id="orig_reject_reason">
                      </div>
                      <div class="form-group" id="edit_reject_responsible_div" style="">
                          <label>Responsible:</label>
                          <select class="form-controls sel7" name="edit_reject_responsible" id="edit_reject_responsible" class="edit_reject_responsible" required>
                              <option value="" selected>Select Responsible</option>
                              <option value="Engineering">Engineering</option>
                              <option value="Operator">Operator</option>
                              <option value="Assembler">Assembler</option>
                              <option value="Supplier">Supplier</option>
                              <option value="Machine/Equipment">Machine/Equipment</option>
                              <option value="Others">Others</option>
                          </select>
                          <input type="hidden" class="form-control" name="orig_reject_responsible"  id="orig_reject_responsible">
                      </div>
                      <div class="form-group" style="" id="edit_r_action_div">
                          <label>Recommended Action:</label>
                          <select class="form-controls sel7" name="edit_r_action" id="edit_r_action" class="edit_r_action" required>
                            <option value="" selected>Select Recommended Action</option>
                            <option value="Undefined">Undefined</option>
                            <option value="Rework">Rework</option>
                            <option value="Replace">Replace</option>
                            <option value="Scrap">Scrap</option>
                            <option value="Use as is">Use as is</option>
                          </select>
                          <input type="hidden" class="form-control" name="orig_r_action"  id="orig_r_action">
                      </div>
                      <div class="form-group">
                          <label>Material type:</label>
                          <select class="form-controls sel7" name="edit_material_type" id="edit_material_type" class="edit_material_type">
                          </select>
                          <input type="hidden" class="form-control" name="orig_material_type" id="orig_material_type">
                      </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
<div class="modal fade" id="delete-reject-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/delete_rejectlist" method="POST" id="delete-reject-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                 Delete Reject list
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12" style="font-size:13pt;">
                     <div class="form-group">
                        <label style="padding-left: 10px;display:inline;">Are you sure to delete reject checklist -</label><label style="padding-left: 10px;display:inline;font-weight:bold;" id="delete_rejectlist_label"></label>?
                        <input type="hidden" name="delete_rejectlist_id" id="delete_rejectlist_id">
                     </div>
                  </div>
                  <input type="hidden" name="delete_reloadtbl" id="delete_reloadtbl">
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
<div class="modal fade" id="add-reject-category-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <form action="/save_reject_category" method="POST" id="add-reject-category-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Add Reject Category
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                        <label>Type:</label>
                        <select class="form-controls sel8" name="reject_ctgtype" id="reject_ctgtype" required="">
                            <option value="Minor Reject(s)">Minor Reject(s)</option>
                            <option value="Major Reject(s)">Major Reject(s)</option>
                            <option value="Critical Reject(s)">Critical Reject(s)</option>
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Reject Category:</label>
                        <input type="text" class="form-control" name="reject_category" required id="reject_category">
                     </div>
                    <div class="form-group">
                        <label>Description:</label>
                        <textarea class="form-control" name="reject_ctgdesc" required id="reject_ctgdesc" cols="30" rows="5" style="width:100%;max-width:100%;min-width:100%;"></textarea>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
<div class="modal fade" id="edit-reject-category-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <form action="/update_reject_category" method="POST" id="edit-reject-category-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Edit Reject Category
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                        <input type="hidden" name="ctg_id" id="ctg_id">
                        <label>Type:</label>
                        <select class="form-control" name="edit_type" id="edit_type" required>
                            <option value="Minor Reject(s)">Minor Reject(s)</option>
                            <option value="Major Reject(s)">Major Reject(s)</option>
                            <option value="Critical Reject(s)">Critical Reject(s)</option>
                            <option value="Operator Reject(s)">Operator Reject(s)</option>
                        </select>
                        <input type="hidden" name="orig_reject_ctgtype" id="orig_reject_ctgtype">
                     </div>
                     <div class="form-group">
                        <label>Reject Category:</label>
                        <input type="text" class="form-control" name="edit_category" required id="edit_category">
                        <input type="hidden" name="orig_reject_category" id="orig_category">
                     </div>
                    <div class="form-group">
                        <label>Description:</label>
                        <textarea class="form-control" name="edit_reject_ctgdesc" required id="edit_reject_ctgdesc" cols="30" rows="5" style="width:100%;max-width:100%;min-width:100%;"></textarea>
                        <input type="hidden" name="orig_reject_ctgdesc" id="orig_reject_ctgdesc">
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
<div class="modal fade" id="delete-reject-category-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/delete_reject_category" method="POST" id="delete-reject-category-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                 Delete Reject Category
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                     <div class="form-group">
                        <label style="padding-left: 10px;display:inline;">Are you sure to delete reject category -</label><label style="padding-left: 10px;display:inline;font-weight:bold;" id="delete_reject_category_label"></label>?
                        <input type="hidden" name="delete_reject_category_id" id="delete_reject_category_id">
                     </div>
                  </div>

               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
<div class="modal fade" id="add-checklist-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document" style="min-width: 40%;">
      <form action="/save_checklist" method="POST" id="save-checklist-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                 
               </h5>
            </div>
            <div class="modal-body"> 
                <input type="hidden" name="owner_checklist" id="owner_checklist">
                <div class="col-sm-12">
                  <div class="form-group">
                        <label><b>Workstation:</b></label>
                        <select class="form-controls sel4" name="workstation_id" id="r_workstation_id" class="r_workstation_id" required>
                          
                        </select>
                  </div>
                  <a href="#" class="btn btn-primary add-row">
                    <i class="now-ui-icons ui-1_simple-add"></i>Add
                  </a>
                  <table class="table" id="reject-table" style="font-size: 10px;">
                     <thead>
                        <tr>
                           <th style="width: 5%; text-align: center;font-weight: bold;">No.</th>
                           <th style="width: 45%; text-align: center;font-weight: bold;">Type</th>
                           <th style="width: 45%; text-align: center;font-weight: bold;">Description</th>
                           <th style="width: 5%; text-align: center;font-weight: bold;"></th>
                        </tr>
                     </thead>
                     <tbody class="table-body text-center">
                        <tr>
                           <td>1</td>
                           <td>
                              <select name="new_checklist_r_type[]" class="form-control onchange-selection count-row" id="first-selection" data-idcolumn=''>
                                @foreach($reject_category as $row)
                                    <option value="{{ $row->reject_category_id }}">{{ $row->reject_category_name }}</option>
                                @endforeach
                                 
                              </select>
                           </td>
                           <td>
                              <select name="new_checklist_r_desc[]" class="form-control second-selection-only" id="">
                                 <option value="">--Description--</option>
                                 
                              </select>
                           </td>
                           <td><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>
                        </tr>
                     </tbody>
                  </table>
               </div>  
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
<div class="modal fade" id="delete-checklist-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 30%;">
      <form action="/delete_checklist" method="POST" id="delete-checklist-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">
                <span>Delete</span>
                <span class="operation-text" style="font-weight: bolder;"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <div class="form-group"  style="font-size:13pt;">
                        <label style="padding-left: 10px;display:inline;">Delete</label><label style="padding-left: 10px;display:inline;font-weight:bold;" id="delete_checklist_label"></label><label style="padding-left: 10px;display:inline;">from</label><label style="padding-left: 10px;display:inline;font-weight:bold;" id="delete_workstation_label"></label><label style="padding-left: 10px;display:inline;">?</label>
                        <input type="hidden" name="check_list_id" id="delete_checklist_id">
                     </div>
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
<div class="modal fade" id="add-sampling-plan-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <form action="/save_sampling_plan" method="POST" id="add-sampling-plan-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">
                <span>Add Sampling Plan</span>
                <span class="sampling-text" style="font-weight: bolder;"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Category:</label>
                            <select class="form-control sel9" name="sp_category" id="sp_category" required="">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Lot Size(Min):</label>
                            <input type="text" class="form-control" name="lot_min" required id="lot_min">
                         </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Lot Size(Max):</label>
                            <input type="text" class="form-control" name="lot_max" required id="lot_max">
                         </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Sample Size:</label>
                            <input type="text" class="form-control" name="spl_size" required id="spl_size">
                         </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Acceptance Level:</label>
                            <input type="text" class="form-control" name="accpt_lvl" required id="accpt_lvl">
                         </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Reject Level:</label>
                            <input type="text" class="form-control" name="rjt_lvl" required id="rjt_lvl">
                         </div>
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
<div class="modal fade" id="delete-sampling-plan-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 30%;">
      <form action="/delete_sampling_plan" method="POST" id="delete-sampling-plan-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">
                <span>Delete Sampling Plan</span>
                <span class="sampling-delete-text" style="font-weight: bolder;"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                        <label style="padding-left: 10px;display:inline;">Are you sure to delete sampling plan?</label>
                        <input type="hidden" name="delete_sampling_plan_id" id="delete_sampling_plan_id">
                        <input type="hidden" name="delete_sampling_plan_category" id="delete_sampling_plan_category">
                     </div>
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
<div class="modal fade bd-example-modal-lg" id="add-item-warehouse-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document">
      <form action="/save_item_classification_warehouse" method="POST" id="save-item-classification-warehouse-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Add Item Warehouse
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                        <label>Operation:</label>
                        <select class="form-controls sel5" name="operation" id="icw_operation" required>
                          @foreach($operation_list as $row)
                            <option value="{{ $row->operation_id }}">{{ $row->operation_name }}</option>
                          @endforeach
                        </select>
                     </div>
                     <a href="#" class="btn btn-primary add-row">
                    <i class="now-ui-icons ui-1_simple-add"></i>Add
                  </a>
                  <table class="table" id="icw-table" style="font-size: 10px;">
                     <thead>
                        <tr>
                           <th style="width: 5%; text-align: center;font-weight: bold;">No.</th>
                           {{-- <th style="width: 22.5%; text-align: center;font-weight: bold;">Item Group</th> --}}
                           <th style="width: 22.5%; text-align: center;font-weight: bold;">Item Classification</th>
                           <th style="width: 22.5%; text-align: center;font-weight: bold;">Warehouse</th>
                           {{-- <th style="width: 22.5%; text-align: center;font-weight: bold;">Target Warehouse</th> --}}
                           <th style="width: 5%; text-align: center;font-weight: bold;"></th>
                        </tr>
                     </thead>
                     <tbody class="table-body text-center">
                        <tr>
                           <td>1</td>
                           <td>
                              <select name="new_item_class[]" class="form-control sel5" id="icw_item_classification" data-idcolumn=''>
                                <option value="none" selected>Select Item Classification</option>
                                @foreach($item_classification as $ic)
                                    <option value="{{ $ic->name }}">{{ $ic->name }}</option>
                                @endforeach
                              </select>
                           </td>
                           <td>
                              <select name="new_source_warehouse[]" class="form-control second-selection sel5" id="icw_s_warehouse">
                                <option value="none" selected>Select Source Warehouse</option>
                                @foreach($warehouse as $w)
                                    <option value="{{ $w->name }}">{{ $w->name }}</option>
                                @endforeach
                              </select>
                           </td>
                           <td>
                              <select name="new_target_warehouse[]" class="form-control third-selection sel5" id="icw_t_warehouse">
                                <option value="none" selected>Select Target Warehouse</option>
                                @foreach($warehouse as $w)
                                  <option value="{{ $w->name }}">{{ $w->name }}</option>
                                @endforeach
                              </select>
                           </td>
                           <td><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>
                        </tr>
                     </tbody>
                  </table>
                     
                  </div>
                

               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
<div class="modal fade" id="edit-item-warehouse-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/edit_item_classification_warehouse" method="POST" id="edit-item-classification-warehouse-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Edit Item Warehouse
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <input type="hidden" name="orig_item_class" id="orig_item_class">
                    <input type="hidden" name="orig_source_w" id="orig_source_w">
                    <input type="hidden" name="orig_item_group" id="orig_item_group">
                    <input type="hidden" name="orig_target_w" id="orig_target_w">
                    <input type="hidden" name="orig_operation" id="orig_operation">
                    <input type="hidden" name="icw_id" id="icw_id">
                    <div class="form-group">
                        <label>Operation:</label>
                        <select class="form-controls sel10" name="edit_operation" id="edit_icw_operation" required>
                          @foreach($operation_list as $row)
                            <option value="{{ $row->operation_id }}">{{ $row->operation_name }}</option>
                          @endforeach
                        </select>
                     </div>
                     {{-- <div class="form-group">
                        <label>Item Group:</label>
                        <select class="form-controls sel10 edit-onchange-selections-item-group" data-itemgroup='edit_icw_item_classification' data-dateslct="" name="edit_item_group" id="edit_icw_item_group" required>
                          @foreach($item_group as $ig)
                            <option value="{{ $ig->name }}">{{ $ig->name }}</option>
                          @endforeach
                        </select>
                     </div> --}}
                    <div class="form-group">
                        <label>Item Classification:</label>
                        <select class="form-controls sel10" name="edit_item_classification" id="edit_icw_item_classification" >
                          <option value=""></option>
                          @foreach($item_classification as $ic)
                            <option value="{{ $ic->name }}">{{ $ic->name }}</option>
                          @endforeach
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Warehouse:</label>
                        <select class="form-controls sel10" name="edit_s_warehouse" id="edit_icw_s_warehouse" required>
                          @foreach($warehouse as $w)
                            <option value="{{ $w->name }}">{{ $w->name }}</option>
                          @endforeach
                        </select>
                     </div>
                     {{-- <div class="form-group">
                        <label>Target Warehouse:</label>
                        <select class="form-controls sel10" name="edit_t_warehouse" id="edit_icw_t_warehouse" required>
                        @foreach($warehouse as $w)
                          <option value="{{ $w->name }}">{{ $w->name }}</option>
                        @endforeach
                        </select>
                     </div> --}}
                     
                  </div>
                

               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
<div class="modal fade" id="delete-item-warehouse-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 30%;">
      <form action="/delete_item_classification_warehouse" method="POST" id="delete-item-classification-warehouse-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">
                <span>Delete Item Warehouse</span>
                <span class="sampling-delete-text" style="font-weight: bolder;"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                        <label style="padding-left: 10px;display:inline;">Delete Item Classification Warehouse under </label><span id="icw_itemclass_label" style="font-weight: bold;"></span>?
                        <input type="hidden" name="delete_icw_id" id="delete_icw_id">
                     </div>
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
<div class="modal fade" id="add-wip-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <form action="/save_wip" method="POST" id="add-wip-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Add Work In Progress
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                        <label>Type:</label>
                        <select class="form-controls sel14" name="icw_wip_operation" id="icw_wip_operation" required="">
                            @foreach($operation_list as $row)
                              <option value="{{ $row->operation_id }}"> {{$row->operation_name}}</option>
                            @endforeach
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Work In Progress:</label>
                        <select class="form-controls sel14" name="icw_workinprogress" id="ict_workinprogress" required>
                        @foreach($warehouse_wip as $w)
                          <option value="{{ $w->name }}">{{ $w->name }}</option>
                        @endforeach
                        </select>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
<div class="modal fade" id="edit-wip-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <form action="/edit_wip" method="POST" id="edit-wip-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Edit Work In Progress
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                        <label>Type:</label>
                        <select class="form-controls sel15" name="edit_icw_wip_operation" id="edit_icw_wip_operation" required="">
                            @foreach($operation_list as $row)
                              <option value="{{ $row->operation_id }}"> {{$row->operation_name}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="orig_icw_wip_operation" id="orig_icw_wip_operation">
                     </div>
                     <div class="form-group">
                        <input type="hidden" name="wip_id" id="wip_id">
                        <label>Work In Progress:</label>
                        <select class="form-controls sel15" name="edit_icw_workinprogress" id="edit_icw_workinprogress" required>
                        @foreach($warehouse_wip as $w)
                          <option value="{{ $w->name }}">{{ $w->name }}</option>
                        @endforeach
                        </select>
                        <input type="hidden" name="orig_icw_workinprogress" id="orig_icw_workinprogress">
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
<div class="modal fade" id="delete-wip-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 30%;">
      <form action="/delete_wip" method="POST" id="delete-wip-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">
                <span>Delete Work In Progress</span>
                <span class="sampling-delete-text" style="font-weight: bolder;"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                        <label style="padding-left: 10px;display:inline;">Delete Work in Progress under </label><span id="wip_label" style="font-weight: bold;"></span>?
                        <input type="hidden" name="delete_wip_id" id="delete_wip_id">
                     </div>
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
<div class="modal fade" id="delete-email-trans-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 30%;">
      <form action="/delete_email_recipient" method="POST" id="delete-email-trans-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD; padding: 5px 8px;">
               <h5 class="modal-title" id="modal-title">
                <span>Delete Email Recipient</span>
                <span class="operation-text" style="font-weight: bolder;"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                        <label style="display:inline;">Delete</label><label style="padding-left: 10px;display:inline;font-weight:bold;" id="delete_email_label"></label><label style="padding-left: 10px;display:inline;">from</label><label style="padding-left: 10px;display:inline;font-weight:bold;" id="delete_etrans_label"></label><label style="padding-left: 10px;display:inline;">?</label>
                        <input type="hidden" name="email_id" id="delete_email_id">
                     </div>
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
<div class="modal fade" id="add-late-delivery-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
     <form action="/save_late_delivery_reason" method="POST" id="add-late-delivery-frm">
        @csrf
        <div class="modal-content">
           <div class="modal-header text-white" style="background-color: #0277BD;">
              <h5 class="modal-title" id="modal-title "> Add Reschedule Delivery Reason<br>
              </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">×</span>
              </button>
           </div>
           <div class="modal-body">
              <div class="form-row">
                 <div class="form-group col-md-12">
                    <button type="button" class="btn btn-primary pull-right" id="add-late-delivery-button" style="margin: 5px;">
                       <i class="now-ui-icons ui-1_simple-add"></i> Add
                    </button>
                 </div>
                 <hr>
                 <div class="col-md-12">
                 <table class="table" id="latedelivery-table" style="font-size: 10px; ">
                   <col style="width: 5%;">
                   <col style="width: 90%;">
                   <col style="width: 5%;">
                    <thead>
                       <tr style="">
                          <th style="width: 5%; text-align: center;font-weight: bold;">No.</th>
                          <th style="width: 40%; text-align: center;font-weight: bold; ">Late Delivery Reason:</th>
                          <th style="width: 5%; text-align: center;font-weight: bold; "></th>
                       </tr>
                    </thead>
                    <tbody class="table-body text-center" style="">
                       <tr style="">
                       </tr>
                    </tbody>
                 </table>
                 </div> 
              </div>
              
           </div>
           <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Submit</button>
           </div>
        </div>
     </form>
  </div>
</div>
<div class="modal fade" id="edit-late-delivery-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
     <form action="/edit_late_delivery_reason" method="POST" id="edit-late-delivery-frm">
        @csrf
        <div class="modal-content">
           <div class="modal-header text-white" style="background-color: #0277BD;">
              <h5 class="modal-title" id="modal-title "> Edit Reschedule Delivery Reason<br>
              </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">×</span>
              </button>
           </div>
           <div class="modal-body">
               <div class="row">
                   <div class="col-md-12">
                     <div class="form-group">
                         <label>Late Delivery Reason:</label>
                         <input type="text" name="edit_late_deli_reason" id="edit_late_deli_reason" class="form-control">
                         <input type="hidden" name="orig_late_deli_reason" id="orig_late_deli_reason" class="form-control">
                         <input type="hidden" name="transid" id="transid" class="form-control">

                     </div>
                     
                   </div>
               </div>
           </div>
           <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Submit</button>
           </div>
        </div>
     </form>
  </div>
</div>
<div class="modal fade" id="add-operator-checklist-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document" style="min-width:40%;">
     <form action="/save_operator_checklist" method="POST" id="save-operator-checklist-frm">
        @csrf
        <div class="modal-content">
           <div class="modal-header text-white" style="background-color: #0277BD;">
              <h5 class="modal-title" id="modal-title ">
                
              </h5>
           </div>
           <div class="modal-body"> 
               <input type="hidden" name="operator_owner_checklist" id="operator_owner_checklist">
               <input type="hidden" name="reload_operator_checklist" id="reload_operator_checklist">
               <div class="col-sm-12">
                 <div class="form-group">
                       <label><b>Workstation:</b></label>
                       <select class="form-controls operator-checklist-sel" name="workstation_id" id="opchecklist_workstation_id" class="opchecklist_workstation_id" required>
                         
                       </select>
                 </div>
                 <a href="#" class="btn btn-primary add-row">
                   <i class="now-ui-icons ui-1_simple-add"></i>Add
                 </a>
                 <table class="table" id="operator-checklist-table" style="font-size: 10px;">
                    <thead>
                       <tr>
                          <th style="width: 5%; text-align: center;font-weight: bold;">No.</th>
                          <th style="width: 30%; text-align: center;font-weight: bold;">Type</th>
                          <th style="width: 30%; text-align: center;font-weight: bold;">Process</th>
                          <th style="width: 30%; text-align: center;font-weight: bold;">Description</th>
                          <th style="width: 5%; text-align: center;font-weight: bold;"></th>
                       </tr>
                    </thead>
                    <tbody class="table-body text-center">
                       <tr>
                          <td>1</td>
                          <td>
                             <select name="operator_new_checklist_r_type[]" class="form-control onchange-selection count-row" id="operator-first-selection" data-idcolumn=''>
                               @foreach($reject_category as $row)
                                   <option value="{{ $row->reject_category_id }}">{{ $row->reject_category_name }}</option>
                               @endforeach
                             </select>
                          </td>
                          <td>
                            <select name="operator_new_checklist_r_process[]" class="form-control">
                               <option value="">--Process--</option>
                            </select>
                         </td>
                          <td>
                             <select name="operator_new_checklist_r_desc[]" class="form-control operator-second-selection-only" id="">
                                <option value="">--Description--</option>
                             </select>
                          </td>
                          <td><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>
                       </tr>
                    </tbody>
                 </table>
              </div>  
           </div>
           <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Submit</button>
           </div>
        </div>
     </form>
  </div>
</div>
<div class="modal fade" id="delete-operator-checklist-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="width: 40%;">
     <form action="/delete_operator_checklist" method="POST" id="delete-operator-checklist-frm">
        @csrf
        <div class="modal-content">
           <div class="modal-header text-white" style="background-color: #0277BD;">
              <h5 class="modal-title" id="modal-title">
               <span>Delete</span>
               <span class="operation-text" style="font-weight: bolder;"></span></h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
               </button>
           </div>
           <div class="modal-body">
              <div class="row">
                 <div class="col-md-12">
                   <div class="form-group" style="font-size:13pt;">
                       <label style="padding-left: 10px;display:inline;">Delete</label><label style="padding-left: 10px;display:inline;font-weight:bold;" id="delete_opchecklist_label"></label><label style="padding-left: 10px;display:inline;">from</label><label style="padding-left: 10px;display:inline;font-weight:bold;" id="delete_opworkstation_label"></label><label style="padding-left: 10px;display:inline;">?</label>
                       <input type="hidden" name="check_list_id" id="delete_opchecklist_id">
                    </div>
                 </div>
                 <input type="hidden" name="delete_op_reloadtbl" id="delete_op_reloadtbl">
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
<div class="modal fade" id="add-material-type-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
     <form action="/save_material_type" method="POST" id="add-material-type-frm">
        @csrf
        <div class="modal-content">
           <div class="modal-header text-white" style="background-color: #0277BD;">
              <h5 class="modal-title" id="modal-title "> Add Material Type<br>
              </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">×</span>
              </button>
           </div>
           <div class="modal-body">
              <div class="form-row">
                 <div class="form-group col-md-12">
                    <button type="button" class="btn btn-primary pull-right" id="add-material-type-row" style="margin: 5px;">
                       <i class="now-ui-icons ui-1_simple-add"></i> Add
                    </button>
                 </div>
                 <hr>
                 <div class="col-md-12">
                 <table class="table" id="material-type-table" style="font-size: 10px; ">
                   <col style="width: 5%;">
                   <col style="width: 90%;">
                   <col style="width: 5%;">
                    <thead>
                       <tr style="">
                          <th style="width: 5%; text-align: center;font-weight: bold;">No.</th>
                          <th style="width: 40%; text-align: center;font-weight: bold; ">Material Type</th>
                          <th style="width: 5%; text-align: center;font-weight: bold; "></th>
                       </tr>
                    </thead>
                    <tbody class="table-body text-center" style="">
                       <tr style="">
                       </tr>
                    </tbody>
                 </table>
                 </div> 
              </div>
           </div>
           <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Submit</button>
           </div>
        </div>
     </form>
  </div>
</div>
<div class="modal fade" id="edit-material-type-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
     <form action="/edit_material_type" method="POST" id="edit-material-type-frm">
        @csrf
        <div class="modal-content">
           <div class="modal-header text-white" style="background-color: #0277BD;">
              <h5 class="modal-title" id="modal-title "> Edit Material Type<br>
              </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">×</span>
              </button>
           </div>
           <div class="modal-body">
               <div class="row">
                   <div class="col-md-12">
                     <div class="form-group">
                         <label>Material Type:</label>
                         <input type="text" name="edit_material_type" id="edit_material_type_setup" class="form-control">
                         <input type="hidden" name="orig_material_type" id="orig_material_type_setup" class="form-control">
                         <input type="hidden" name="mtypeid" id="mtypeid" class="form-control">

                     </div>
                     
                   </div>
               </div>
           </div>
           <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Submit</button>
           </div>
        </div>
     </form>
  </div>
</div>
<div class="modal fade" id="add-checklist-painting-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document" style="min-width: 40%;">
    <form action="/save_checklist" method="POST" id="save-painting-checklist-frm">
       @csrf
       <div class="modal-content">
          <div class="modal-header text-white" style="background-color: #0277BD;">
             <h5 class="modal-title" id="modal-title ">
               
             </h5>
          </div>
          <div class="modal-body"> 
              <input type="hidden" name="owner_checklist" id="painting_owner_checklist">
              <div class="col-sm-12">
                <div class="form-group">
                      <label><b>Workstation:</b></label>
                      <select class="form-controls sel4" name="workstation_id" id="painting_r_workstation_id" class="r_workstation_id" required>
                        
                      </select>
                </div>
                <a href="#" class="btn btn-primary add-row">
                  <i class="now-ui-icons ui-1_simple-add"></i>Add
                </a>
                <table class="table" id="painting-reject-table" style="font-size: 10px;">
                   <thead>
                      <tr>
                         <th style="width: 5%; text-align: center;font-weight: bold;">No.</th>
                         <th style="width: 30%; text-align: center;font-weight: bold;">Type</th>
                         <th style="width: 30%; text-align: center;font-weight: bold;">Process</th>
                         <th style="width: 30%; text-align: center;font-weight: bold;">Description</th>
                         <th style="width: 5%; text-align: center;font-weight: bold;"></th>
                      </tr>
                   </thead>
                   <tbody class="table-body text-center">
                      <tr>
                         <td>1</td>
                         <td>
                            <select name="new_checklist_r_type[]" class="form-control onchange-selection count-row" id="first-selection" data-idcolumn=''>
                              @foreach($reject_category as $row)
                                  <option value="{{ $row->reject_category_id }}">{{ $row->reject_category_name }}</option>
                              @endforeach
                               
                            </select>
                         </td>
                         <td>
                            <select name="new_checklist_r_desc[]" class="form-control second-selection-only" id="">
                               <option value="">--Description--</option>
                               
                            </select>
                         </td>
                         <td><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>
                      </tr>
                   </tbody>
                </table>
             </div>  
          </div>
          <div class="modal-footer">
             <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
             <button type="submit" class="btn btn-primary">Submit</button>
          </div>
       </div>
    </form>
 </div>
</div>
<div class="modal fade" id="add-cancelled-reason-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
     <form action="/save_cancelled_reason" method="POST" id="add-cancelled-reason-frm">
        @csrf
        <div class="modal-content">
           <div class="modal-header text-white" style="background-color: #0277BD;">
              <h5 class="modal-title" id="modal-title "> Add Reason for Cancellation<br>
              </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">×</span>
              </button>
           </div>
           <div class="modal-body">
              <div class="form-row">
                 <div class="form-group col-md-12">
                    <button type="button" class="btn btn-primary pull-right" id="add-reason-of-cancel-row" style="margin: 5px;">
                       <i class="now-ui-icons ui-1_simple-add"></i> Add
                    </button>
                 </div>
                 <hr>
                 <div class="col-md-12">
                 <table class="table" id="cancelled-reason-table" style="font-size: 10px; ">
                   <col style="width: 5%;">
                   <col style="width: 90%;">
                   <col style="width: 5%;">
                    <thead>
                       <tr style="">
                          <th style="width: 5%; text-align: center;font-weight: bold;">No.</th>
                          <th style="width: 40%; text-align: center;font-weight: bold; ">Reason for Cancellation</th>
                          <th style="width: 5%; text-align: center;font-weight: bold; "></th>
                       </tr>
                    </thead>
                    <tbody class="table-body text-center" style="">
                       <tr style="">
                       </tr>
                    </tbody>
                 </table>
                 </div> 
              </div>
           </div>
           <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Submit</button>
           </div>
        </div>
     </form>
  </div>
</div>
<div class="modal fade" id="edit-cancelled-reason-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
     <form action="/edit_cancelled_reason" method="POST" id="edit-cancelled-reason-frm">
        @csrf
        <div class="modal-content">
           <div class="modal-header text-white" style="background-color: #0277BD;">
              <h5 class="modal-title" class="modal-title"> Edit Reason for Cancellation<br>
              </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">×</span>
              </button>
           </div>
           <div class="modal-body">
               <div class="row">
                   <div class="col-md-12">
                     <div class="form-group">
                         <label>Reason for Cancellation:</label>
                         <input type="text" name="edit_reason_for_cancellation" id="edit_reason_for_cancellation_setup" class="form-control">
                         <input type="hidden" name="orig_reason_for_cancellation" id="orig_reason_for_cancellation_setup" class="form-control">
                         <input type="hidden" name="edit_reason_for_cancellation_id" id="edit_reason_for_cancellation_id" class="form-control">
                     </div>
                   </div>
               </div>
           </div>
           <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Submit</button>
           </div>
        </div>
     </form>
  </div>
</div>
<div class="modal fade" id="delete-cancelled-reason-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
     <form action="/delete_cancelled_reason" method="POST" id="delete-cancelled-reason-frm">
        @csrf
        <div class="modal-content">
           <div class="modal-header text-white" style="background-color: #0277BD;">
              <h5 class="modal-title" class="modal-title"> Remove Reason for Cancellation<br>
              </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">×</span>
              </button>
           </div>
           <div class="modal-body">
               <div class="row">
                   <div class="col-md-12">
                      <input type="hidden" name="delete_reason_cancellation_id" id="delete_reason_cancellation_id">
                      <input type="hidden" name="delete_reason_cancellation" id="delete_reason_cancellation">
                      <div class="row">
                        <div class="col-sm-12"style="font-size: 12pt;">
                            <label> Remove reason for cancellation -  <span id="delete_label_reason_cancellation_id" style="font-weight: bold;"></span></label>?
                        </div>               
                      </div>
                   </div>
               </div>
           </div>
           <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Submit</button>
           </div>
        </div>
     </form>
  </div>
</div>
<style type="text/css">
  .scrolling table {
    table-layout: fixed;
    width: 100%;
}
.scrolling .td, .th {
  padding: 10px;
  width: auto;
}
.parent-td{
  padding: 10px;
  width: 4px;
  float: left;
}
.scrolling .th {
  position: relative;
  left: 0;
  width: auto;
}
.outer {
  position: relative
}
.inner {
  overflow-x: auto;

}
.nav-item .active{
  background-color: #f96332;
  font-weight: bold;
  color:#ffffff;
}



  .user-image {
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 5px;
}

.imgPreview {
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 5px;
}

.upload-btn{
   padding: 6px 12px;
}

.fileUpload {
   position: relative;
   overflow: hidden;
   font-size: 9pt;
}

.fileUpload input.upload {
   position: absolute;
   top: 0;
   right: 0;
   margin: 0;
   padding: 0;
   cursor: pointer;
   opacity: 0;
   filter: alpha(opacity=0);
}
.imgPreview1 {
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 5px;
}

.upload-btn1{
   padding: 6px 12px;
}

.fileUpload1 {
   position: relative;
   overflow: hidden;
   font-size: 9pt;
}

.fileUpload1 input.upload1 {
   position: absolute;
   top: 0;
   right: 0;
   margin: 0;
   padding: 0;
   cursor: pointer;
   opacity: 0;
   filter: alpha(opacity=0);
}

.boldwrap {
  font-weight: bold;
}
  
#add-user-modal .form-control, #edit-user-modal .form-control {
  border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px;
}
#edit-user-modal .form-control:hover, #edit-user-modal .form-control:focus, #edit-user-modal .form-control:active {
  box-shadow: none;
}
#edit-user-modal .form-control:focus {
  border: 1px solid #34495e;
}

#add-user-modal .form-control:hover, #add-user-modal .form-control:focus, #add-user-modal .form-control:active {
  box-shadow: none;
}
#add-user-modal .form-control:focus {
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
#edit-checklist-modal .form-control, #add-checklist-modal .form-control{
  border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px;
}
#add-checklist-modal .form-control{
  border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px;
  font-size: 12px;
}
#add-reject-modal .form-control, #edit-reject-modal .form-control, #add-reject-category-modal .form-control{
  border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px;
}
 #add-reject-category-modal .form-control, #edit-reject-category-modal .form-control{
    border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px;
}
 #add-sampling-plan-modal .form-control{
    border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px;
}
 #add-wip-modal .form-control{
    border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px;
}
.select-input{
    height: 33px;
    font-size: 12px;
}

.timepicker { 
  font-size:9pt !important;
  min-width:200px !important;
}
#add-shift-modal .form-control {
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }
  #edit-shift-modal .form-control {
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }
  #add-email-trans-modal .form-control{
    border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px; 
}
#add-late-delivery-modal .form-control{
    border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px;
}
#edit-late-delivery-modal .form-control{
    border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px;
}
#add-material-type-modal .form-control{
    border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px;
}
#edit-material-type-modal .form-control{
    border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px;
}
#add-cancelled-reason-modal .form-control{
  border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px;
}
#edit-cancelled-reason-modal .form-control{
  border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px;
}
</style>

@endsection

@section('script')
<script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />
<script type="text/javascript" src="{{ asset('css/datepicker/jquery.timepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/jquery.timepicker.css') }}" />
<script type="text/javascript">
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
</script>
<script type="text/javascript">
  $('.time').timepicker({
        'showDuration': true,
        'timeFormat': 'g:i a'
    });
  $('.date').datepicker({
        'format': 'yyyy-mm-dd',
        'autoclose': true
    });

</script>
<script>
  $(document).ready(function(){
    uom_conversion_list();
    $('#add-uom-conversion-btn').click(function(e){
        e.preventDefault();
        $('#uom-conversion-frm .modal-title').text('Add UoM Conversion');
        $('#uom-conversion-frm input[name="conversion_id"]').val(0);
        $('#uom-conversion-modal').modal('show');
    });

    $('.uom-sel').select2({
        dropdownParent: $("#uom-conversion-modal"),
        dropdownAutoWidth: false,
        width: '100%',
        cache: false
    });

    $('#uom-conversion-frm').submit(function(e){
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
                $('#uom-conversion-modal').modal('hide');
                uom_conversion_list();
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
        });
    });

    $(document).on('click', '#uom-conversion-pagination a', function(event){
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        uom_conversion_list(page);
    });

    function uom_conversion_list(page){
        $.ajax({
            url: '/get_uom_conversion_list/?page=' + page,
            type:"GET",
            success:function(data){
                $('#uom-conversion-tbl').html(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
        });
    }

    $(document).on('click', '.edit-uom-conversion-btn', function(e){
        e.preventDefault();

        var $row = $(this).closest('tr');
        var id_1 = $row.find('.id').eq(0).text();
        var id_2 = $row.find('.id').eq(1).text();

        var uom_id_1 = $row.find('.uom-id').eq(0).text();
        var uom_id_2 = $row.find('.uom-id').eq(1).text();

        var conversion_1 = $row.find('.conversion_factor').eq(0).text();
        var conversion_2 = $row.find('.conversion_factor').eq(1).text();

        var material_type = $row.find('.uom-material-type').eq(0).text();

        $('#uom-conversion-frm select[name="material_type"]').eq(0).val(material_type);

        $('#uom-conversion-frm .id_1').val(id_1);
        $('#uom-conversion-frm .id_2').val(id_2);

        $('#uom-conversion-frm .uom-sel').eq(0).val(material_type);
        $('#uom-conversion-frm .uom-sel').eq(1).val(uom_id_1);
        $('#uom-conversion-frm .uom-sel').eq(2).val(uom_id_2);

        $('#uom-conversion-frm input[name="conversion_factor[]"]').eq(0).val(conversion_1);
        $('#uom-conversion-frm input[name="conversion_factor[]"]').eq(1).val(conversion_2);

        $('#uom-conversion-frm .modal-title').text('Edit UoM Conversion');
        $('#uom-conversion-frm input[name="conversion_id"]').val(1);
        $('#uom-conversion-modal').modal('show');
    });

    $(document).on('click', '.delete-uom-conversion-btn', function(e){
        e.preventDefault();
        var $row = $(this).closest('tr');
        var uom_conversion_id = $row.find('.uom-conversion-id').eq(0).text();

        var uom_name_1 = $row.find('.uom-name').eq(0).text();
        var uom_name_2 = $row.find('.uom-name').eq(1).text();

        var conversion_1 = $row.find('.conversion_factor').eq(0).text();
        var conversion_2 = $row.find('.conversion_factor').eq(1).text();

        var description = conversion_1 + ' ' + uom_name_1 + ' >> ' + conversion_2 + ' ' + uom_name_2; 

        $('#delete-uom-conversion-modal .uom-description').text(description);

        $('#delete-uom-conversion-modal input[name="uom_conversion_id"]').val(uom_conversion_id);
        
        $('#delete-uom-conversion-modal').modal('show');
    });

     $('#delete-uom-conversion-modal form').submit(function(e){
        e.preventDefault();

        $.ajax({
            url: $(this).attr("action"),
            type:"POST",
            data: $(this).serialize(),
            success:function(data){
              if (data.success < 1) {
                showNotification("danger", 'There was a problem deleting UoM Conversion.', "now-ui-icons travel_info");
              }else{
                showNotification("success", data.message, "now-ui-icons ui-1_check");
                $('#delete-uom-conversion-modal').modal('hide');
                uom_conversion_list();
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
        });
    });

   
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 
    $('#icw_operation option:contains(' + "Fabrication" + ')').prop({selected: true});
    $('#icw_operation').trigger('change');
    
    $('.add_icw_operation').click(function(){
		var is_active = $(this).attr('data-operation');
    if(is_active == "hide_me"){
      $('#add-item-warehouse-button').hide();
      
      
    }else{
      $('#add-item-warehouse-button').show();
      $('#add-item-warehouse-modal .modal-title').text(is_active);
      $('#icw_operation option:contains(' + is_active + ')').prop({selected: true});
      $('#icw_operation').trigger('change');
    }
   
      // if(!is_active){
      //   $('#transaction-filter-div').toggle();
      //   $('#invetory-filters-div').toggle();
      // }
	  });
    $(document).on('click', '#add-machine-to-workstation-button', function(){
      $('#add-machine_to_workstation-processprofile-modal').modal('show');
    });
    $(document).on('click', '#add-wip-button', function(){
      $('#add-wip-modal').modal('show');
    });
    $(document).on('click', '.edit-wip-button', function(){
        var op = $(this).data('operation');
        var ware = $(this).data('ware');
        var id = $(this).data('id');
              $('#wip_id').val(id);
              $('#orig_icw_wip_operation').val(op);
              $('#orig_icw_workinprogress').val(ware);
              $('#edit_icw_wip_operation').val(op);
              $('#edit_icw_workinprogress').val(ware);
              $('#edit_icw_wip_operation').trigger('change');
              $('#edit_icw_workinprogress').trigger('change');

      $('#edit-wip-modal').modal('show');
    });
    $(document).on('click', '.delete-wip-button', function(){
        var op = $(this).data('operation');
        var op_name = $(this).data('op');
        var id = $(this).data('id');
        $('#delete_wip_id').val(id);
        $('#wip_label').text(op_name);
        $('#delete-wip-modal').modal('show');
    });

    $(document).on('click', '#add-machine-to-workstation-button', function(){
      $('#process-profile-modal').modal('show');
    });
    $(document).on('click', '#add-shift-button', function(){
      $('#add-shift-modal').modal('show');
      $('#add-reject-modal tbody').empty();
    });
    $(document).on('click', '.edit-modal-process-profile', function(){
      var process_id = $(this).data('processid');
      var process_name = $(this).data('process');
      var remarks = $(this).data('remarks');
      $('#edit_process_id').val(process_id);
      $('#edit_process_name').val(process_name);
      $('#edit_color_legend').val($(this).data('color'));
      $('#edit_remarks').val(remarks);
      $('#edit-process-setup-list-modal').modal('show');
    });
    $(document).on('click', '.btn-delete-process-setup-list', function(){
      var process_id = $(this).data('processid');
      var process_name = $(this).data('process');
      // alert(process_name);
      $('#delete_process_id').val(process_id);
      $('#delete_process_name_input').text(process_name);
      $('#delete-process-setup-list-modal').modal('show');
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
  get_machine_assign();
  tbl_process_setup_list();
  tbl_shift_list();
  tbl_operation_list();
  tbl_shift_schedule_sched();
  get_users();
  workstation_list();
  setting_machine_list();
  check_list_fabrication();
  check_list_painting();
  check_list_assembly();
  qa_reject_list();
  reject_category_list();
  tbl_sampling_plan_visual();
  tbl_sampling_plan_variable();
  tbl_sampling_plan_reliability();
  item_classification_warehouse_tbl_fabrication();
  item_classification_warehouse_tbl_painting();
  item_classification_warehouse_tbl_assembly();
  get_user_group();
  tbl_wip_list();
  tbl_email_trans();

  $('.schedule-date').datepicker({
    'format': 'yyyy-mm-dd',
    'autoclose': true
  });
  $(document).on('click', '#add-workstation-button', function(){
    $('#add-workstation-modal').modal('show');
    });
  $(document).on('click', '.btn-modal-process-profile', function(){
    var process_id = $(this).data('processid');
    var process_name = $(this).data('process');
    $('#process_id_to_assign').val(process_id);
    $('#process-profile-modal .modal-title').text(process_name);
    
    // var status = $(this).data('status');
    // alert(process_id);
    tbl_process_assigned_process();
    $('#process-profile-modal').modal('show');
    });
  $(document).on('click', '.remove-assigned-machine', function(){
    var id = $(this).data('id');
    var workstation = $(this).data('workstation');
    var machine = $(this).data('machine');
    $('#delete_id').val(id);
    $('#delete_workstation').text(workstation);
    $('#delete_machine').text(machine);


    // var status = $(this).data('status');
    // alert(id);
    // tbl_process_assigned_process();
    $('#delete-assigned').modal('show');
    });
  $(document).on('click', '.add-process-assignment', function(){
    var process_id = $('#process_id_to_assign').val();
    $('#assign_process_id').val(process_id);

    $('#process-assignment-modal').modal('show');
    });
  $(document).on('click', '#add-machine-button', function(){
    $('#add-machine-modal').modal('show');
    $('#add-worktation-frm').trigger("reset");
    });
  $(document).on('click', '#add-process-button', function(){
    $('#add-process-modal').modal('show');
    });
    $('#add-worktation-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
                $('#add-workstation-modal').modal('hide');
                $('#add-worktation-frm').trigger("reset");
                // location.reload(true);
                workstation_list();

          }
        }
      });
    });
    $('#edit-workstation-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
                $('#edit-workstation-modal').modal('hide');
                $('#edit-worktation-frm').trigger("reset");
                workstation_list();

          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });

      function getMachine_list(){
      $.ajax({
        url:"/get_machine_list",
        type:"GET",
        success:function(data){
          $('#tbl_machine_list').html(data);
        }
      });  
    }

     $('.sel2').select2({
    dropdownParent: $("#add-user-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });

     $('.sel3').select2({
    dropdownParent: $("#edit-user-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });

    $('.sel4').select2({
    dropdownParent: $("#add-checklist-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });

  $('.sel5').select2({
    dropdownParent: $("#add-item-warehouse-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
    $('.sel6').select2({
    dropdownParent: $("#add-reject-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
$('.sel7').select2({
    dropdownParent: $("#edit-reject-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
$('.sel8').select2({
    dropdownParent: $("#add-reject-category-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
$('.sel9').select2({
    dropdownParent: $("#add-sampling-plan-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
  $('.sel10').select2({
    dropdownParent: $("#edit-item-warehouse-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
  $('.sel11').select2({
    dropdownParent: $("#add-user-group-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
  $('.sel12').select2({
    dropdownParent: $("#edit-user-group-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
    $('.sel14').select2({
    dropdownParent: $("#add-wip-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
        $('.sel15').select2({
    dropdownParent: $("#edit-wip-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
  $('.operator-checklist-sel').select2({
    dropdownParent: $("#add-operator-checklist-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });


    

    $('#add-user-btn').click(function(e){
      e.preventDefault();
      $('#add-user-modal').modal('show'); 
    });
    $('#add-user-group').click(function(e){
      e.preventDefault();
      $('#add-user-group-modal').modal('show'); 
    });

    $('#add-user-frm').submit(function(e){
      e.preventDefault();
      $.ajax({
        url: $(this).attr('action'),
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          get_users();
          $('#add-user-modal').modal('hide');
          showNotification("success", data.message, "now-ui-icons ui-1_check");
        }
      });  
    });

    $('#sel-user-id-add').change(function(e){
      $('#emp-name-input-add').val($(this).find(':selected').data('empname'));
    });

    $('#edit_user_id').change(function(e){
      $('#edit_employee_name').val($(this).find(':selected').data('empname'));
    });

    $(document).on('click', '.edit-user-group-btn', function(){
      var user_m = $(this).attr('data-module');
      var role = $(this).attr('data-role');
      var id = $(this).attr('data-id');

              $('#edit_user_group_regis').val(user_m).prop('selected', true);
              $('#edit_user_role_regis').val(role);
              $('#edit_user_group_regis_id').val(id);
              $('#edit_orig_module').val(user_m);
              $('#edit_orig_role').val(role);
              $('#edit_user_group_regis').trigger('change');
              $('#edit_user_role_regis').trigger('change');
              $('#edit-user-group-modal').modal('show');
            
    });
    $(document).on('click', '.delete-user-group-btn', function(){
      var user_m = $(this).attr('data-module');
      var role = $(this).attr('data-role');
      var id = $(this).attr('data-id');
       var label = user_m+"-"+role;

              $('#delete_user_group_id').val(id);
              $('#delete_label_user_group').text(label);
              
              $('#delete-user-group-modal').modal('show');
            
    });
    $(document).on('click', '.edit-user-btn', function(){
      var user_m = $(this).attr('data-module');
      var user_name = $(this).attr('data-user');
      var user_id = $(this).attr('data-userid');
      var user_group = $(this).attr('data-usergroup');
      var operation = $(this).attr('data-operation');
      var id = $(this).attr('data-id');
      $('#edit_user_group').val(user_m);
       $('#edit_user_group').trigger('change');
      $.ajax({
            url:"/get_user_role_by_module/"+user_m,
            type:"GET",
            success:function(data){
              $("#edit_user_role").html(data);
              $('#edit_user_operation').val(operation).prop('selected', true);
              $('#edit_employee_name').val(user_name);
              $('#edit_user_id').val(user_id).prop('selected', true);
              $('#edit_user_group').val(user_m);
              $('#edit-user-id-input').val(id);
              $('#edit_user_operation').trigger('change');
              $('#edit_user_id').trigger('change');
              $('#edit_user_role').trigger('change');
              $('#edit-user-modal').modal('show');
              $("#edit_user_role").val(user_group);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
        });
    });

    $('#edit-user-frm').submit(function(e){
      e.preventDefault();
      $.ajax({
        url: $(this).attr('action'),
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          get_users();
          $('#edit-user-modal').modal('hide');
          showNotification("success", data.message, "now-ui-icons ui-1_check");
        }
      });  
    });

    $(document).on('click', '.delete-user-btn', function(e){
      e.preventDefault();
      var row = $(this).closest('tr');
      $('#delete-user-frm input[name="user_id"]').val(row.find('span').eq(0).text());
      $('#delete-user-frm span').text(row.find('td').eq(1).text());
      $('#delete-user-modal').modal('show');
    });


     $('#delete-user-frm').submit(function(e){
      e.preventDefault();
      $.ajax({
        url: $(this).attr('action'),
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          get_users();
          $('#delete-user-modal').modal('hide');
          showNotification("success", data.message, "now-ui-icons ui-1_check");
        }
      });  
    });

     $(document).on('click', '#user-pagination a', function(event){
         event.preventDefault();
         var page = $(this).attr('href').split('page=')[1];
         get_users(page);
      });

     $('.modal').on('hidden.bs.modal', function(){
      var frm = $(this).find('form')[0];
      if (frm) frm.reset();
    });

});
</script>
<script type="text/javascript">
       $("#add-machine-frm .upload").change(function () {
         if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#add-machine-frm .imgPreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
         }
      });
</script>
<script type="text/javascript">
       $("#machine_test .upload").change(function () {
         if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#machine_test .imgPreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
         }
      });  
</script>
<script type="text/javascript">
       $("#add-machine-frm .upload").change(function () {
         if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#add-machine-frm .imgPreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
         }
      });
</script>
<script type="text/javascript">
       $("#machine_test .upload1").change(function () {
         if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#machine_test .imgPreview1').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
         }
      });  
</script>
<script>

  function markMatch(text, term) {

    var startString = '<span class="boldwrap">';
    var endString = text.replace(startString, '');

    var match = endString.toUpperCase().indexOf(term.toUpperCase());
    var $result = $('<span></span>');

    if (match < 0) {
      return $result.text(text);
    }
    var elementToReplace = endString.substr(match, term.length);
    var $match = '<span class="select2-rendered__match">' + endString.substring(match, match + term.length) + '</span>';
    text = startString + endString.replace(elementToReplace, $match);

    // console.log(text);
    $result.append(text);
    return $result;
  }

  $('#machine_assignment').select2({
    dropdownParent: $("#process-assignment-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    templateResult: function(item) {
      if (item.loading) {
        return item.text;
      }
      var term = query.term || '';
      var $result = markMatch('<span class="boldwrap">' + item.text.substring(0, item.text.indexOf("-")) + '</span>' + item.text.substring(item.text.indexOf("-")), term);
      return $result;

    },

    language: {
      searching: function(params) {
        // Intercept the query as it is happening
        query = params;
        // Change this to be appropriate for your application
        return 'Searching...';
      }
    },
    cache: true
  });
</script>
<script type="text/javascript">
  function get_machine_assign(){
    var workstation = $('#workstation_id_assign').val();
        $.ajax({
          url:"/get_machine_assignment_jquery/"+ workstation,
          type:"GET",
          success:function(data){
            $('#machine_assignment').html(data);
          }
        }); 
  }
</script>
<script type="text/javascript">
  function tbl_process_assigned_process(page){
    var id = $('#process_id_to_assign').val();
        $.ajax({
          url:"/get_tbl_assigned_machine_process/"+ id + "/?page="+page,
          type:"GET",
          success:function(data){
            $('#tbl_assigned_process_div').html(data);
          }
        }); 
  }
</script>
<script type="text/javascript">
  function tbl_process_setup_list(page, query){
        $.ajax({
          url:"/get_tbl_process_setup_list/?page="+page,
          type:"GET",
          data: {search_string: query},
          success:function(data){
            $('#tbl_process_setup_list_div').html(data);
          }
        }); 
  }
</script>
<script type="text/javascript">

    $('#add-assign-machine-workstation-to-process-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#process-assignment-modal').modal('hide');
            tbl_process_assigned_process();
            // getAssignedTasks();
          }
        }
      });
    });
</script>
<script type="text/javascript">

    $(document).on('click', '#assigned_machine_process a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    tbl_process_assigned_process(page);

  });
    $(document).on('click', '#tbl_process_setup_list_pgination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    tbl_process_setup_list(page);

  });
    $(document).on('click', '#tbl_shift_list_pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    tbl_shift_list(page);

  });
    $(document).on('click', '#tbl_operation_list_pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    tbl_operation_list(page);

  });
    $(document).on('click', '#tbl_shift_sched_list_pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    tbl_shift_schedule_sched(page);

  });
    $(document).on('click', '#checklist_list_pagination_fabrication a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
   check_list_fabrication(page);

  });
    $(document).on('click', '#checklist_list_pagination_painting a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
   check_list_painting(page);

  });
    $(document).on('click', '#checklist_list_pagination_assembly a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
   check_list_assembly(page);

  });
    $(document).on('click', '#checklist_list_pagination_operator a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
   check_list_operator(page);

  });
  $(document).on('click', '#reject_check_list_pagination a', function(event){
    event.preventDefault();
    var query = $('#search_reject_setup').val();
    var page = $(this).attr('href').split('page=')[1];
    qa_reject_list(page, query);

  });
  $(document).on('click', '#op_reject_check_list_pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    var query = $('#search_operator_reject_setup').val();
    op_reject_list(page, query);

  });
    $(document).on('click', '#reject_category_pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    reject_category_list(page);

  });
    

    
</script>
<script type="text/javascript">

    $('#delete-assigned-machine-workstation-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#delete-assigned').modal('hide');
            tbl_process_assigned_process();
            // getAssignedTasks();
          }
        }
      });
    });
</script>
<script type="text/javascript">

    $('#edit-process-setup-list-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#edit-process-setup-list-modal').modal('hide');
            tbl_process_setup_list();
            // getAssignedTasks();
          }
        }
      });
    });
</script>
<script type="text/javascript">

    $('#delete-process-setup-list-modal-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#delete-process-setup-list-modal').modal('hide');
            tbl_process_setup_list();
            // getAssignedTasks();
          }
        }
      });
    });
</script>
<script type="text/javascript">

    $('#add-process-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#add-process-modal').modal('hide');
            tbl_process_setup_list();
            // getAssignedTasks();
          }
        }
      });
    });
</script>
<script type="text/javascript">
  function tbl_shift_list(page){
        $.ajax({
          url:"/get_tbl_shift_list/?page="+page,
          type:"GET",
          success:function(data){
            $('#tbl_shift').html(data);
          }
        }); 
  }
</script>
<script type="text/javascript">

    $('#add-shift-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#add-shift-modal').modal('hide');
            tbl_shift_list();
          }
        }
      });
    });
</script>
<script type="text/javascript">

    $('#edit-shift-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#edit-shift-modal').modal('hide');
            tbl_shift_list();
            tbl_shift_schedule_sched();
          }
        }
      });
    });
</script>
<script type="text/javascript">
    $('#delete-shift-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#delete-shift-modal').modal('hide');
            tbl_shift_list();
          }
        }
      });
    });
</script>

<script type="text/javascript">
    $(document).on('click', '.delete-shift-list', function(){
      var shift_id = $(this).attr('data-shiftid');
      $('#delete-shift-frm .delete_shift_id').val(shift_id);

    $('#delete-shift-modal').modal('show');
    });
</script>
<script type="text/javascript">
    $(document).on('click', '#add-operation-button', function(){
    $('#add-operation-modal').modal('show');
    });
</script>
<script type="text/javascript">
  function tbl_operation_list(page, query){
        $.ajax({
          url:"/get_tbl_operation_list/?page="+page,
          type:"GET",
          data: {search_string: query},
          success:function(data){
            $('#tbl_operation').html(data);
          }
        }); 
  }
</script>
<script type="text/javascript">
    $('#add-operation-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#add-operation-modal').modal('hide');
            tbl_operation_list();
          }
        }
      });
    });
</script>
<script type="text/javascript">
    $('#edit-operation-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#edit-operation-modal').modal('hide');
            tbl_operation_list();

          }
        }
      });
    });
</script>
<script type="text/javascript">
      $('.modal').on('hidden.bs.modal', function(){
      var frm = $(this).find('form')[0];
      if (frm) frm.reset();
    });
</script>
<script type="text/javascript">
    $(document).on('click', '.edit-operation-list', function(){
      var operation_id = $(this).attr('data-operationid');
      var operation_name = $(this).attr('data-operationname');
      var operation_desc = $(this).attr('data-operationdesc');

      $('#edit-operation-frm .operation_id').val(operation_id);
      $('#edit-operation-frm .old_operation').val(operation_name);
      $('#edit-operation-frm .operation_name').val(operation_name);
      $('#edit-operation-frm .operation_desc').val(operation_desc);

    $('#edit-operation-modal').modal('show');
    });
</script>

<script type="text/javascript">
  function tbl_shift_schedule_sched(page){
        $.ajax({
          url:"/get_tbl_shiftsched_list/?page="+page,
          type:"GET",
          success:function(data){
            $('.tbl_shift_schedule_sched').html(data);
          }
        }); 
  }
</script>
<script type="text/javascript">
    $('#add-shift-schedule-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#add-shift-schedule-modal').modal('hide');
            tbl_shift_schedule_sched();
          }
        }
      });
    });
</script>
<script type="text/javascript">
    $('#edit-shift-schedule-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#edit-shift-schedule-modal').modal('hide');
            tbl_shift_schedule_sched();
          }
        }
      });
    });
</script>
<script type="text/javascript">
    $(document).on('click', '.edit-shift-sched-list', function(){
      var shift_id = $(this).attr('data-shiftid');
      var shift_sched_id = $(this).attr('data-shiftschedid');
      var date = $(this).attr('data-date');
      var remarks = $(this).attr('data-remarks');

      $('#edit-shift-schedule-frm .shift_id').val(shift_id).prop('selected', true);
      $('#edit-shift-schedule-frm .shift_sched_id').val(shift_sched_id);
      $('#edit-shift-schedule-frm .sched_date').val(date);
      $('#edit-shift-schedule-frm .remarks').val(remarks);

    $('#edit-shift-schedule-modal').modal('show');
    });
</script>
<script type="text/javascript">
    $(document).on('click', '.delete-shift-sched-list', function(){
      var shift_sched_id = $(this).attr('data-shiftschedid');
      $('#delete-shift-sched-frm .delete_shift_sched_id').val(shift_sched_id);

    $('#delete-shift-sched-modal').modal('show');
    });
</script>
<script type="text/javascript">
    $('#delete-shift-sched-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#delete-shift-sched-modal').modal('hide');
            tbl_shift_schedule_sched();
          }
        }
      });
    });
</script>
<script type="text/javascript">
  function get_shift_details(){
    var shift_sched_id = $('#add-shift-schedule-frm #shift_id').val();
    $.ajax({
          url:"/get_shift_details/"+ shift_sched_id,
          type:"GET",
          success:function(data){
            $('#add-shift-schedule-frm .time_in').text("Time-in:  " + data.time_in + "  " + " " + "  ");
            $('#add-shift-schedule-frm .time_out').text("         Time-out:  " + data.time_out);
          }
        }); 
  }
</script>
<script type="text/javascript">
    $(document).on('click', '#add-shift-schedule-button', function(){
      $('#add-shift-schedule-frm .time_in').text("");
            $('#add-shift-schedule-frm .time_out').text("");
    $.ajax({
          url:"/get_shift_list_option",
          type:"GET",
          success:function(data){
            
            $('#add-shift-schedule-frm .shift_id').html(data);
            $('#add-shift-schedule-modal').modal('show');

          }
        });
    });
</script>

<!-- Additional function -->
<script>
function workstation_list(page, query){
    $.ajax({
          url:"/get_tbl_workstation_list?page=" + page,
          type:"GET",
          data: {search_string: query},
          success:function(data){
            
            $('#tbl_workstation_list').html(data);
          }
        });

}
</script>
<script>
    $(document).on('click', '.btn-edit-workstation-modal', function(){
      var workstation_id = $(this).data('workstationid');
      var workstation_name = $(this).data('workstationname');
      var order_no = $(this).data('orderno');
      var operation = $(this).data('operation');
      // alert(operation);
      $('#orig_workstation_name').val(workstation_name);
      $('#orig_workstation_id').val(workstation_id);
      $('#edit_workstation_name').val(workstation_name);
      $('#edit_order_no').val(order_no);
      $('#edit_workstation_operation').val(operation);
      $('#orig_operation').val(operation);
      $('#edit-workstation-modal').modal('show');
    });
    $(document).on('click', '.btn-delete-workstation', function(){
      var workstation_id = $(this).data('workstationid');
      var workstation_name = $(this).data('workstationname');
      var order_no = $(this).data('orderno');
      var operation = $(this).data('operation');
      // alert(operation);
      $('#delete-workstation-id').val(workstation_id);
      $('#delete-workstation-name').val(workstation_name);
      $('#workstation_name_label').text(workstation_name);
      $('#operation_label').text(operation);
      $('#delete-workstation-modal').modal('show');
    });
    $(document).on('click', '#workstation_list_pagination a', function(event){
         event.preventDefault();
         var page = $(this).attr('href').split('page=')[1];
         workstation_list(page);
    });
    $('#delete-workstation-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#delete-workstation-modal').modal('hide');
            workstation_list();
          }
        }
      });
    });
 
</script>
<script type="text/javascript">
    $('#add-machine-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      var form1 = $(this).get(0); 
      $.ajax({
        url: url,
        type:"POST",
        data: new FormData(form1),
        processData: false,
        contentType: false,
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#add-machine-modal').modal('hide');
            $('#add-machine-frm').trigger("reset");

            setting_machine_list();
          }
        }
      });
    });
</script>
<!-- Machine_list -->
<script>
function setting_machine_list(page, query){
    $.ajax({
          url:"/get_tbl_setting_machine_list?page=" + page,
          data: {search_string: query},
          type:"GET",
          success:function(data){
            
            $('#tbl_setting_machine_list').html(data);
          }
        });

}
$(document).on('click', '#setting_machine_list_pagination a', function(event){
         event.preventDefault();
         var page = $(this).attr('href').split('page=')[1];
         setting_machine_list(page);
    });
    $(document).on('click', '.btn-edit-machine', function(){
      var machine_id = $(this).data('machineid');
      var machine_name = $(this).data('machinename');
      var reference_key = $(this).data('referencekey');
      var machine_code = $(this).data('machinecode');
      var status = $(this).data('status');
      var type = $(this).data('type');
      var model = $(this).data('model');
      var image = $(this).data('image');
      var imagevar = image;
      // alert(imagevar);
      $("#machine_image").attr("src","");
      $("#machine_image").attr("src",imagevar);
      $('#edit_orig_image').val(image);
      $('#editt_machineid').val(reference_key);
      $('#edit_origmachine_code').val(machine_code);

      $('#edit_machineid').val(machine_id);
      $('#edit_machinecode').val(machine_code);
      $('#edit_machine_name').val(machine_name);
      $('#edit_machine_type').val(type);
      $('#edit_machine_model').val(model);
      $('#edit_machine_status').val(status);
      // $('#machine_image_forupload').val(image);
      
      $('#edit-machine-modal').modal('show');
    });

    $('#edit-machine-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      var form1 = $(this).get(0); 
      $.ajax({
        url: url,
        type:"POST",
        data: new FormData(form1),
        processData: false,
        contentType: false,
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#edit-machine-modal').modal('hide');
            $('#edit-machine-frm').trigger("reset");
            $('#edit-machine-modal').trigger("reset");
            $("#edit-machine-frm .imgPreview").attr("src","");
            setting_machine_list();
            // localStorage.clear(); 

            // tbl_shift_schedule_sched();
 
                // $("#edit-machine-modal .modal-content").empty();
      
          }
        }
      });
    });
  
    $(document).on('click', '.btn-delete-machine', function(){
      var machine_id = $(this).data('machineid');
      var machine_name = $(this).data('machinename');
      var reference_key = $(this).data('referencekey');
      var machine_code = $(this).data('machinecode');
      var status = $(this).data('status');
      var type = $(this).data('type');
      var model = $(this).data('model');
      var image = $(this).data('image');
      $('#machine_image').attr("src",$(this).data('image'));
      
      // alert(operation);
      $('#edit_orig_image').val(image);
      $('#editt_machineid').val(machine_id);

      $('#edit_machineid').val(reference_key);
      $('#edit_machinecode').val(machine_code);
      $('#edit_machine_name').val(machine_name);
      $('#edit_machine_type').val(type);
      $('#edit_machine_model').val(model);
      $('#edit_machine_status').val(status);
      // $('#machine_image_forupload').val(image);
      
      $('#delete-machine-modal').modal('show');
    });
    $(document).on('click', '.btn-delete-machine', function(){
      var machine_id = $(this).data('machineid');
      var machine_name = $(this).data('machinename');
      var reference_key = $(this).data('referencekey');
      var machine_code = $(this).data('machinecode');
      var status = $(this).data('status');
      var type = $(this).data('type');
      var model = $(this).data('model');
      var image = $(this).data('image');
      
      // alert(operation);
      $('#delete-machine-id').val(machine_id);
      // alert(machine_id);
      $('#delete-machine-code').val(machine_code);
      $('#machine_code_label').text(machine_code);
      
      $('#delete-machinelist-modal').modal('show');
    });
    $('#delete-machine-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#delete-machinelist-modal').modal('hide');
            setting_machine_list();
          }
        }
      });
    });
</script>
<script>
  $(document).on('keyup', '#search-production-process', function(){
    var query = $(this).val();
    tbl_process_setup_list(1, query);
  });
  $(document).on('keyup', '#search_workstation_setup', function(){
    var query = $(this).val();
    workstation_list(1, query);
  });
  $(document).on('keyup', '#search_user_setup', function(){
    var query = $(this).val();
    get_users(1, query);
  });
  $(document).on('keyup', '#search_machine_setup', function(){
    var query = $(this).val();
    setting_machine_list(1, query);
  });
  $(document).on('keyup', '#search_operation_setup', function(){
    var query = $(this).val();
    tbl_operation_list(1, query);
  });
  $(document).on('keyup', '#search_reject_setup', function(){
    var query = $(this).val();
		var parent_tab = $("#qa_tab li a.active").attr('data-qatab');
    if(parent_tab == "checklist"){
      check_list_assembly(1, query);
    }else if(parent_tab == "user"){
      reje
    }else if(parent_tab == "category"){

    }else if(parent_tab == "reject"){
      qa_reject_list(1, query);
    }else{

    }
  });
  $(document).on('keyup', '#search_operator_reject_setup', function(){
    var query = $(this).val();
		var parent_tab = $("#operator_tab li a.active").attr('data-qatab');
    if(parent_tab == "op_fabrication"){
      operator_check_list_fabrication(1, query);
    }else if(parent_tab == "op_painting"){
      operator_check_list_painting(1, query);
    }else if(parent_tab == "op_assembly"){
      operator_check_list_assembly(1, query);
    }else if(parent_tab == "op_reject"){
      op_reject_list(1, query);
    }else{
    }
  });

  $(document).on('keyup', '#search_item_warehouse_setup', function(){
    var query = $(this).val();
    val = $('#item_warehouse_setup').find('a.active').attr('data-values');
    if (val =="fab") {
        item_classification_warehouse_tbl_fabrication(1, query);
    }else if(val == "pain"){
        item_classification_warehouse_tbl_painting(1, query);
    }else{
        item_classification_warehouse_tbl_assembly(1, query);

    }
    
  });

</script>
<script>
function get_users(page, query){
      $.ajax({
        url:"/get_users/?page=" + page,
        type:"GET",
        data: {search_string: query},
        success:function(data){
          $('#div-user-table').html(data);
        }
      });  
    }
</script>
<script type="text/javascript">
  
  $(document).on('click', '#add-reject-button', function(){
    $.ajax({
        url:"/get_reject_category_for_add_reject_modal",
        type:"GET",
        success:function(data){
          $('#reject_category').html(data.category);
        }
    });  
    $("#op_operation").prop('required',false);
    $('#checklist_th').show();
    $('#add-reject-modal .modal-title').text('Add QA Reject list');
    $('#div_op_operation').hide();
    $('#reject_owner').val('Quality Assurance');
    $('#add-reject-modal tbody').empty();
    $('#add-reject-modal').modal('show');
});
$(document).on('click', '#add-operator-reject-button', function(){
    $.ajax({
        url:"/get_reject_category_for_add_reject_modal",
        type:"GET",
        success:function(data){
          $('#reject_category').html(data.category);
          $('#op_operation').html(data.operation);
        }
    }); 
    $("#op_operation").prop('required',true);
    $('#div_op_operation').show();
    $('#checklist_th').hide();
    $('#reject_owner').val('Operator');
    $('#add-reject-modal .modal-title').text('Add Operator Reject list');
    $('#add-reject-modal tbody').empty();
    $('#add-reject-modal').modal('show');
});
// $(document).on('change', '#reject_owner', function(){
    // var category_val = $('#reject_owner').val();
    // $('#add-reject-modal tbody').empty();
    // if (category_val == "Operator") {
    //     $("#reject_checklist_div").hide();
    // }else{
    //    $("#reject_checklist_div").show();

 
    // }
// });
// $(document).on('change', '#edit_reject_owner', function(){
//     var category_val = $('#edit_reject_owner').val();
//     if (category_val == 'Operator') {
//                $("#edit_reject_checklist_div").hide();

        
//     }else{
//                       $("#edit_reject_checklist_div").show();


 
//     }
// });

$(document).on('click', '.btn-edit-checklist', function(){

      var workstation = $(this).data('workstationid');
      var operation = $(this).data('operationid');
      var classifi = $(this).data('classifi');
      var checklist = $(this).data('checklist');
      var check_list_id = $(this).data('id');

      $('#edit_r_workstation_id').text(workstation);
      $('#edit_r_operation_id').text(operation);
      $('#edit_classification').text(classifi);
      $('#edit_c_checklist').val(checklist);
      $('orig_checklist').val(checklist);
      $('#checklist_id').val(check_list_id);

      $('#edit-checklist-modal').modal('show');
    });

$(document).on('click', '.btn-delete-checklist', function(){

      var id = $(this).data('id');
      var workstation = $(this).data('workstation');
      var rejectlist = $(this).data('rejectchecklist');
      var operation = $(this).data('operation');
      $('#delete_checklist_label').text(rejectlist);
      $('#delete_workstation_label').text(workstation);
      $('.operation-text').text("["+operation+"]");
      $('#delete_checklist_id').val(id);
      $('#delete-checklist-modal').modal('show');
    });
</script>
<script>
function check_list_fabrication(page, query){
    $.ajax({
          url:"/get_tbl_checklist_list_fabrication?page=" + page,
          type:"GET",
          data: {search_string: query},
          success:function(data){
            
            $('#tbl_check_list_fabrication').html(data);
          }
        });

}
</script>
<script>
function check_list_painting(page, query){
    $.ajax({
          url:"/get_tbl_checklist_list_painting?page=" + page,
          type:"GET",
          data: {search_string: query},
          success:function(data){
            
            $('#tbl_check_list_painting').html(data);
          }
        });

}
</script>
<script>
function check_list_assembly(page, query){
    $.ajax({
          url:"/get_tbl_checklist_list_assembly?page=" + page,
          type:"GET",
          data: {search_string: query},
          success:function(data){
            
            $('#tbl_check_list_assembly').html(data);
          }
        });

}
</script>

<script type="text/javascript">

    $('#save-checklist-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#add-checklist-modal').modal('hide');
            check_list_fabrication();
            check_list_painting();
            check_list_assembly();
            
            $('#save-checklist-frm').trigger("reset");
            // getAssignedTasks();
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });
</script>
<script type="text/javascript">

    $('#delete-checklist-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#delete-checklist-modal').modal('hide');
            check_list_fabrication();
            check_list_painting();
            check_list_assembly();
            
            $('#delete-checklist-frm').trigger("reset");
          }
        }
      });
    });
</script>

<script>

    $(document).on('click', '#add-checklist-fabrication-button', function(){
       $('#add-checklist-modal .modal-title').text('Fabrication');
       $('#owner_checklist').val('Quality Assurance');

        $.ajax({
            url:"/get_workstation_list_from_checklist/"+ "Fabrication",
            type:"GET",
            success:function(data){
              $("#r_workstation_id").html(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
            $('#reject-table tbody').empty();
            $('#add-checklist-modal').modal('show');

    });
    $(document).on('click', '#add-checklist-painting-button', function(){
       $('#add-checklist-painting-modal .modal-title').text('Painting');
       $('#painting_owner_checklist').val('Quality Assurance');
       
       $.ajax({
            url:"/get_workstation_list_from_checklist/"+ "Painting",
            type:"GET",
            success:function(data){
              $("#painting_r_workstation_id").html(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
        $('#painting-reject-table tbody').empty();
        $('#add-checklist-painting-modal').modal('show');
    });
    $(document).on('click', '#add-checklist-assembly-button', function(){
       $('#add-checklist-modal .modal-title').text('Wiring and Assembly');
       $('#owner_checklist').val('Quality Assurance');
       $.ajax({
            url:"/get_workstation_list_from_checklist/"+ "Wiring and Assembly",
            type:"GET",
            success:function(data){
              $("#r_workstation_id").html(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
        $('#reject-table tbody').empty();
        $('#add-checklist-modal').modal('show');
    });
    $(document).on('click', '#add-checklist-operator-button', function(){
        $('#owner_checklist').val('Operator');
       $('#add-checklist-modal .modal-title').text('Operator Rejects Types');
        $.ajax({
            url:"/get_workstation_list_from_checklist/"+ "Operator",
            type:"GET",
            success:function(data){
              $("#r_workstation_id").html(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
            $('#reject-table tbody').empty();
            $('#add-checklist-modal').modal('show');

    });
    $('#add-checklist-modal .add-row').click(function(e){
         e.preventDefault();
         var row = '';
         $.ajax({
            url: "/get_reject_type_desc",
            type:"get",
            cache: false,
            success: function(response) {
               row += '<option value="none">--Type--</option>';
               $.each(response, function(i, d){
                  row += '<option value="' + d.reject_category_id + '">' + d.reject_category_name + '</option>';
               });
               var thizz = document.getElementById('reject-table');
              var id = $(thizz).closest('table').find('tr:last td:first').text();
               var validation = isNaN(parseFloat(id));
               if(validation){
                var new_id = 1;
               }else{
                var new_id = parseInt(id) + 1;
               }
              //  alert(new_id);
               var len2 = new_id;
               var id_unique="count"+len2;
               // alert(id_unique);
               var tblrow = '<tr>' +
                  '<td>'+len2+'</td>' +
                  '<td><select name="new_checklist_r_type[]" class="form-control onchange-selection count-row sel16"  data-idcolumn='+id_unique+' required>'+row+'</select></td>' +
                  '<td><select name="new_checklist_r_desc[]" class="form-control sel16" id='+id_unique+' required></select></td>' +
                  '<td><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>' +
                  '</tr>';

               $("#add-checklist-modal #reject-table").append(tblrow);
               // autoRowNumberAddKPI();
               $('.sel16').select2({
                  dropdownParent: $("#add-checklist-modal "),
                  dropdownAutoWidth: false,
                  width: '100%',
                  cache: false
                });
            },
            error: function(response) {
               alert('Error fetching Designation!');
            }
         });
      });
     
       $(document).on('change', '.onchange-selection', function(){
           var owner = $('#owner_checklist').val();
           var first_selection_data = $(this).val();
           var id_for_second_selection = $(this).attr('data-idcolumn');
           var operation = $('#add-checklist-modal .modal-title').text();
           var format_id_for_second_selection = "#"+id_for_second_selection;
            $.ajax({
            url:"/get_reject_desc/"+first_selection_data+'/'+owner + '/'+ operation,
            type:"GET",
            success:function(data){
              $(format_id_for_second_selection).html(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
      });
       $(document).on("click", ".delete", function(){
        $(this).parents("tr").remove();
      });
</script>
<script type="text/javascript">

$('#save-reject-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            if(data.reloadtbl == "Operator"){
              showNotification("success", data.message, "now-ui-icons ui-1_check");
              $('#add-reject-modal').modal('hide');
              $('#save-reject-frm').trigger("reset");
              op_reject_list();
            }else{
              showNotification("success", data.message, "now-ui-icons ui-1_check");
              $('#add-reject-modal').modal('hide');
              $('#save-reject-frm').trigger("reset");
              qa_reject_list();
            }

          }
        }
      });
    });
</script>
<script>
function qa_reject_list(page, query){
    $.ajax({
          url:"/get_tbl_qa_reject_list?page=" + page,
          data: {search_string: query},
          type:"GET",
          success:function(data){
            $('#tbl_reject_list').html(data);
          }
        });

}
</script>
<script>
op_reject_list();
function op_reject_list(page, query){
    $.ajax({
          url:"/get_tbl_op_reject_list?page=" + page,
          data: {search_string: query},
          type:"GET",
          success:function(data){
            $('#tbl_operator_reject_list').html(data);
          }
        });

}
</script>
<script type="text/javascript">
    $(document).on('click', '.edit-rejectlist-btn', function(){
        var rjt_id = $(this).attr('data-id');
        var rjt_list = $(this).attr('data-rjtlist');
        var rjt_reason = $(this).attr('data-rjtreason');
        var rjt_ctg = $(this).attr('data-ctgID');
        var rjt_res = $(this).attr('data-responsible');
        var rjt_mtype = $(this).attr('data-mtype');
        var reloadtbl = $(this).attr('data-reloadtbl');
        var rjt_action = $(this).attr('data-action');
        var owner = $(this).attr('data-owner');
        var opoperation = $(this).attr('data-opoperation');

        if(reloadtbl =="Operator"){
          $('#edit_reject_checklist_div').hide();
          $('#div_operation_edit').show();

        }else{
          $('#edit_reject_checklist_div').show();
          $('#div_operation_edit').hide();


        }
        $('#orig_reject_operation').val(opoperation);
        $('#edit_reject_owner').val(owner);
        $('#reloadtbl_edit').val(reloadtbl);
        $('#orig_reject_category').val(rjt_ctg);
        $('#edit_reject_checklist').val(rjt_list);
        $('#orig_reject_checklist').val(rjt_list);
        $('#edit_reject_reason').val(rjt_reason);
        $('#orig_reject_reason').val(rjt_reason);
        $('#edit_id_reject').val(rjt_id);
        $('#edit_reject_responsible').val(rjt_res);
        $('#orig_reject_responsible').val(rjt_res);
  
        $('#edit_r_action').val(rjt_action);
        $('#orig_r_action').val(rjt_action);
        $('#orig_material_type').val(rjt_mtype);
        // $('#edit_reject_owner').trigger('change');
        $('#edit_r_action').trigger('change');

        $.ajax({
        url:"/get_reject_category_for_add_reject_modal",
        type:"GET",
          success:function(data){
            $('#edit_reject_category').html(data.category);
            $('#edit_reject_operation').html(data.operation);
            $('#edit_material_type').html(data.material_type);
            $('#edit_reject_category').val(rjt_ctg);
            $('#edit_reject_operation').val(opoperation);
            $('#edit_material_type').val(rjt_mtype);

            $('#edit_material_type').trigger('change');
            $('#edit_reject_responsible').trigger('change');
            $('#edit_reject_category').trigger('change');
            $('#edit_reject_operation').trigger('change');

          }
        }); 
        $('#edit-reject-modal').modal('show');

    });
</script>
<script type="text/javascript">

    $('#update-reject-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            if(data.reloadtbl == "Operator"){
              showNotification("success", data.message, "now-ui-icons ui-1_check");
              $('#edit-reject-modal').modal('hide');
              op_reject_list();
              $('#edit-reject-frm').trigger("reset");
            }else{
              showNotification("success", data.message, "now-ui-icons ui-1_check");
              $('#edit-reject-modal').modal('hide');
              qa_reject_list();
              $('#edit-reject-frm').trigger("reset");
            }
          }
        }
      });
    });
</script>
<script type="text/javascript">
    $(document).on('click', '.btn-delete-rejectlist', function(){
        var rjt_id = $(this).attr('data-id');
        var rjt_list = $(this).attr('data-rjtlist');
        var rjt_reason = $(this).attr('data-rjtreason');
        var rjt_ctg = $(this).attr('data-ctgID');
        var reloadtbl = $(this).attr('data-reloadtbl');
        if(rjt_list== ''){
          $('#delete_rejectlist_label').text(rjt_reason);
        }else{
          $('#delete_rejectlist_label').text(rjt_list);
        }
        $('#delete_rejectlist_id').val(rjt_id);
        $('#delete_reloadtbl').val(reloadtbl);

        

        $('#delete-reject-modal').modal('show');

    });
</script>
<script type="text/javascript">

    $('#delete-reject-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            if(data.reloadtbl == "Operator"){
              showNotification("success", data.message, "now-ui-icons ui-1_check");
                $('#delete-reject-modal').modal('hide');
                op_reject_list();
                $('#delete-reject-frm').trigger("reset");
            }else{
              showNotification("success", data.message, "now-ui-icons ui-1_check");
                $('#delete-reject-modal').modal('hide');
                qa_reject_list();
                $('#delete-reject-frm').trigger("reset");
            }
          }
        }
      });
    });
</script>           
<script type="text/javascript">
    $(document).on('click', '#add-reject-ctg-button ', function(){       
        $('#add-reject-category-modal').modal('show');

    });
</script>             
<script>
function reject_category_list(page, query){
    $.ajax({
          url:"/get_tbl_reject_category?page=" + page,
          data: {search_string: query},
          type:"GET",
          success:function(data){
            
            $('#tbl-reject-ctg').html(data);
          }
        });

}
</script> 
<script type="text/javascript">
    $(document).on('click', '.edit-reject-category-btn', function(){
        var ctg_id = $(this).attr('data-id');
        var ctg_type = $(this).attr('data-type');
        var ctg_name = $(this).attr('data-category');
        var ctg_desc = $(this).attr('data-categorydesc');
        // alert(ctg_type);
        $('#edit_type option:contains(' + ctg_type + ')').prop({selected: true});
        // $('#edit_type').val($(this).find(':selected').data('ctg_type'));
        $('#edit_type').val(ctg_type);
        $('#orig_reject_ctgtype').val(ctg_type);
        $('#orig_category').val(ctg_name);
        $('#edit_category').val(ctg_name);
        $('#edit_reject_ctgdesc').val(ctg_desc);
        $('#orig_reject_ctgdesc').val(ctg_desc);
        $('#ctg_id').val(ctg_id);
        $('#edit-reject-category-modal').modal('show');

    });
</script> 
<script type="text/javascript">

    $('#add-reject-category-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#add-reject-category-modal').modal('hide');
            reject_category_list();
            $('#add-reject-category-frm').trigger("reset");
          }
        }
      });
    });
</script>  
<script type="text/javascript">

    $('#edit-reject-category-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#edit-reject-category-modal').modal('hide');
            reject_category_list();
            $('#edit-reject-category-frm').trigger("reset");
          }
        }
      });
    });
</script>  
<script type="text/javascript">
    $(document).on('click', '.btn-delete-reject-category', function(){
        var ctg_id = $(this).attr('data-id');
        var ctg_type = $(this).attr('data-type');
        var ctg_name = $(this).attr('data-category');
        var ctg_desc = $(this).attr('data-categorydesc');
        // alert(ctg_type);
        $('#delete_reject_category_label').text(ctg_name);
        $('#delete_reject_category_id').val(ctg_id);
        $('#delete-reject-category-modal').modal('show');

    });
</script>   
<script type="text/javascript">

    $('#delete-reject-category-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#delete-reject-category-modal').modal('hide');
            reject_category_list();
            $('#delete-reject-category-frm').trigger("reset");
          }
        }
      });
    });
</script>
<script>
function tbl_sampling_plan_visual(page){
    $.ajax({
          url:"/get_tbl_qa_visual?page=" + page,
          type:"GET",
          success:function(data){
            
            $('#tbl_visual').html(data);
          }
        });

}
</script> 
<script>
function tbl_sampling_plan_variable(page){
    $.ajax({
          url:"/get_tbl_qa_variable?page=" + page,
          type:"GET",
          success:function(data){
            
            $('#tbl_variable').html(data);
          }
        });

}
</script> 
<script>
function tbl_sampling_plan_reliability(page){
    $.ajax({
          url:"/get_tbl_qa_reliability?page=" + page,
          type:"GET",
          success:function(data){
            
            $('#tbl_reliability').html(data);
          }
        });

}
</script> 
<script type="text/javascript">
    $(document).on('click', '.add-smpl-plan', function(){
        var id = $(this).attr('data-id');
        var spname = $(this).attr('data-spname');
        $.ajax({
        url:"/get_reject_category_for_add_reject_modal",
        type:"GET",
        success:function(data){
          $('#sp_category').html(data);
          $('#sp_category').val(id);
        }
        }); 
        $.ajax({
        url:"/get_max_for_min_sampling_plan/"+ id,
        type:"GET",
        success:function(data){
          $('#lot_min').val(data);
        }
        });
        $('.sampling-text').text('['+ spname+']' );

        $('#add-sampling-plan-modal').modal('show');

    });
</script>
<script type="text/javascript">

    $('#add-sampling-plan-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
           if (data.val == 1) {
            tbl_sampling_plan_visual();
            $('#add-sampling-plan-modal').modal('hide');
            $('#add-sampling-plan-frm').trigger("reset");
           }else if(data.val == 2){
            tbl_sampling_plan_variable();
            $('#add-sampling-plan-modal').modal('hide');
            $('#add-sampling-plan-frm').trigger("reset");
           }else{
            tbl_sampling_plan_reliability();
            $('#add-sampling-plan-modal').modal('hide');
            $('#add-sampling-plan-frm').trigger("reset");
           }
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
      });
    });
</script>
<script type="text/javascript">

$(document).on('click', '#sampling_visual_pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    tbl_sampling_plan_visual(page);

  });
$(document).on('click', '#sampling_variable_pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    tbl_sampling_plan_variable(page);

  });
$(document).on('click', '#sampling_reliability_pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    tbl_sampling_plan_reliability(page);

  });
</script>
<script type="text/javascript">
    $(document).on('click', '.btn-delete-sampling_plan', function(){
        var id = $(this).attr('data-id');
        var category = $(this).attr('data-category');
        $('#delete_sampling_plan_id').val(id);
        $('#delete_sampling_plan_category').val(category);
        $('#delete-sampling-plan-modal').modal('show');

    });
</script>
<script type="text/javascript">

    $('#delete-sampling-plan-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
           
            if (data.category == 1) {
            tbl_sampling_plan_visual();
            $('#delete-sampling-plan-modal').modal('hide');
             $('#delete-sampling-plan-frm').trigger("reset");
           }else if(data.category == 2){
            tbl_sampling_plan_variable();
            $('#delete-sampling-plan-modal').modal('hide');
             $('#delete-sampling-plan-frm').trigger("reset");
           }else{
            tbl_sampling_plan_reliability();
            $('#delete-sampling-plan-modal').modal('hide');
             $('#delete-sampling-plan-frm').trigger("reset");
           }
          }
        }
      });
    });
</script>
<script type="text/javascript">

  $('#save-item-classification-warehouse-frm').submit(function(e){
    e.preventDefault();
    var url = $(this).attr("action");
    $.ajax({
      url: url,
      type:"POST",
      data: $(this).serialize(),
      success:function(data){
        if (data.success < 1) {
          showNotification("danger", data.message, "now-ui-icons travel_info");
        }else{
          showNotification("success", data.message, "now-ui-icons ui-1_check");
          $('#add-item-warehouse-modal').modal('hide');
          $('#save-item-classification-warehouse-frm').trigger("reset");
          item_classification_warehouse_tbl_fabrication();
          item_classification_warehouse_tbl_painting();
          item_classification_warehouse_tbl_assembly();
         
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
      }
    });
  });
</script>
<script type="text/javascript">
    function item_classification_warehouse_tbl_fabrication(page, query){
        $.ajax({
         url:"/item_classification_warehouse_tbl_fabrication?page=" + page,
        type:"GET",
        data: {search_string: query},
        success:function(data){
        
            $('#tbl_item_warehouse_list_fabrication').html(data);

        }
      });
    }

    function item_classification_warehouse_tbl_painting(page, query){
        $.ajax({
         url:"/item_classification_warehouse_tbl_painting?page=" + page,
        type:"GET",
        data: {search_string: query},
        success:function(data){
        
            $('#tbl_item_warehouse_list_painting').html(data);query

        }
      });
    }

    function item_classification_warehouse_tbl_assembly(page, query){
        $.ajax({
         url:"/item_classification_warehouse_tbl_assembly?page=" + page,
        type:"GET",
        data: {search_string: query},
        success:function(data){
        
            $('#tbl_item_warehouse_list_assembly').html(data);

        }
      });
    }

</script>
<script type="text/javascript">
    $(document).on('click', '.edit-itemclassware-btn', function(){
        var icw_id = $(this).attr('data-id');
        var icw_class = $(this).attr('data-itemclass');
        var icw_source = $(this).attr('data-sware');
        var icw_target = $(this).attr('data-tware');
        var ic_op = $(this).attr('data-operation');
        var ic_ig = $(this).attr('data-itemgroup');
        $('#edit_icw_operation').val(ic_op); 
        $('#edit_icw_item_classification').val(icw_class); 
        $('#edit_icw_item_group').val(ic_ig); 
        $('#edit_icw_s_warehouse ').val(icw_source);
        $('#edit_icw_t_warehouse ').val(icw_target);
        $('#orig_item_class ').val(icw_class);
        $('#orig_source_w ').val(icw_source);
        $('#orig_target_w ').val(icw_target);
        $('#orig_operation ').val(ic_op);
        $('#orig_item_group ').val(ic_ig);
        $('#icw_id').val(icw_id);
        $("#edit_icw_item_group").attr("data-dateslct", icw_class);
      
        
        $('#edit_icw_item_classification').trigger('change');
        $('#edit_icw_operation').trigger('change');
        $('#edit_icw_item_group').trigger('change');
        $('#edit_icw_s_warehouse').trigger('change');
        $('#edit_icw_t_warehouse').trigger('change');
        $('#edit-item-warehouse-modal').modal('show');
        // $('#edit_type').val(ctg_type);
        // $('#orig_reject_ctgtype').val(ctg_type);
        // $('#orig_category').val(ctg_name);
        // $('#edit_category').val(ctg_name);
        // $('#edit_reject_ctgdesc').val(ctg_desc);
        // $('#orig_reject_ctgdesc').val(ctg_desc);
        // $('#ctg_id').val(ctg_id);
        

    });
</script> 
<script type="text/javascript">
  $('#edit-item-classification-warehouse-frm').submit(function(e){
    e.preventDefault();
    var url = $(this).attr("action");
    $.ajax({
      url: url,
      type:"POST",
      data: $(this).serialize(),
      success:function(data){
        if (data.success < 1) {
          showNotification("danger", data.message, "now-ui-icons travel_info");
        }else{
          showNotification("success", data.message, "now-ui-icons ui-1_check");
          $('#edit-item-warehouse-modal').modal('hide');
           $('#edit-item-classification-warehouse-frm').trigger("reset");
          item_classification_warehouse_tbl_fabrication();
          item_classification_warehouse_tbl_painting();
          item_classification_warehouse_tbl_assembly();
         
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
      }
    });
  });
</script>
<script type="text/javascript">
    $(document).on('click', '.delete-itemclassware-btn', function(){
        var icw_id = $(this).attr('data-id');
        var icw_class = $(this).attr('data-itemclass');
        var icw_source = $(this).attr('data-sware');
        var icw_target = $(this).attr('data-tware');
        var ic_op = $(this).attr('data-operation');

        $('#icw_itemclass_label').text(icw_class);
        $('#delete_icw_id ').val(icw_id);

        $('#delete-item-warehouse-modal').modal('show');

    });
    $(document).on('click', '#item-classification-warehouse-fabrication-pagination a', function(event){
        event.preventDefault();
        var query = $('#search_item_warehouse_setup').val();
        var page = $(this).attr('href').split('page=')[1];
        item_classification_warehouse_tbl_fabrication(page, query);

    });
    $(document).on('click', '#item-classification-warehouse-painting-pagination a', function(event){
        event.preventDefault();
        var query = $('#search_item_warehouse_setup').val();
        var page = $(this).attr('href').split('page=')[1];
        item_classification_warehouse_tbl_painting(page, query);

    });
    $(document).on('click', '#item-classification-warehouse-assembly-pagination a', function(event){
        event.preventDefault();
        var query = $('#search_item_warehouse_setup').val();
        var page = $(this).attr('href').split('page=')[1];
        item_classification_warehouse_tbl_assembly(page, query);

    });
</script> 
<script type="text/javascript">

    $('#delete-item-classification-warehouse-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#delete-item-warehouse-modal').modal('hide');
             $('#edit-item-classification-warehouse-frm').trigger("reset");
            item_classification_warehouse_tbl_fabrication();
            item_classification_warehouse_tbl_painting();
            item_classification_warehouse_tbl_assembly();
           
          }
        }
      });
    });
</script>
<script type="text/javascript">
    $(document).on('click', '#add-item-warehouse-button', function(){
        $('#icw_s_warehouse').trigger('change');
        $('#icw_t_warehouse').trigger('change');
        $('#icw_operation').trigger('change');
        $('#save-item-classification-warehouse-frm').trigger("reset");
        $('#add-item-warehouse-modal').modal('show');
        $("#icw-table .table-body").empty();

    });

    $('#add-item-warehouse-modal .add-row').click(function(e){
      e.preventDefault();
      var col1 = '';
      var col2 = '';
      var col3 = '';
      var col4 = '';
      $.ajax({
         url: "/get_selection_box_in_item_class_warehouse",
         type:"get",
         cache: false,
         success: function(response) {
            col1 += '<option value="none">Select Item Classification</option>';
            col2 += '<option value="none">Select Source Warehouse</option>';
            col3 += '<option value="none">Select Target Warehouse</option>';
            col4 += '<option value="none">Select Item Group</option>';
            $.each(response.item_class, function(i, d){
               col1 += '<option value="' + d.name + '">' + d.name + '</option>';
            });
            $.each(response.warehouse, function(i, d){
               col2 += '<option value="' + d.name + '">' + d.name + '</option>';
            });
            //$.each(response.warehouse, function(i, d){
              // col3 += '<option value="' + d.name + '">' + d.name + '</option>';
           // });
            $.each(response.item_group, function(i, d){
               col4 += '<option value="' + d.name + '">' + d.name + '</option>';
            });
           var thizz = document.getElementById('icw-table');
           var id = $(thizz).closest('table').find('tr:last td:first').text();
           var validation = isNaN(parseFloat(id));
            if(validation){
             var new_id = 1;
            }else{
             var new_id = parseInt(id) + 1;
            }

            var len2 = new_id;
            var id_unique="group"+len2;
            var uniq_item_class ="itemgroulclass"+len2;
           //  alert(id_unique);
            var tblrow = '<tr>' +
               '<td></td>' +
               '<td><select name="new_item_class[]" class="form-control count-row sel5" id='+uniq_item_class+'>'+col1+'</select></td>' +
               '<td><select name="new_source_warehouse[]" class="form-control count-row sel5" required>'+col2+'</select></td>' +
               //'<td><select name="new_target_warehouse[]" class="form-control count-row sel5" required>'+col3+'</select></td>' +
               '<td><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>' +
               '</tr>';

            $("#add-item-warehouse-modal #icw-table").append(tblrow);
            $('.sel5').select2({
                 dropdownParent: $("#add-item-warehouse-modal"),
                 dropdownAutoWidth: false,
                 width: '100%',
                 cache: false
               });
            autoRowNumberAddKPI();
         },
         error: function(response) {
            alert('Error fetching Designation!');
         }
      });
   });
   
 


</script>
<script type="text/javascript">
    function autoRowNumberAddKPI(){
         $('#add-item-warehouse-modal #icw-table tbody tr').each(function (idx) {
            $(this).children("td:eq(0)").html(idx + 1);
         });
      }
</script>
<script type="text/javascript">
 function change_userrole_add(){
    var modules = $("#user_group").val();
    if (modules =="") {

    }else{
       $.ajax({
            url:"/get_user_role_by_module/"+modules,
            type:"GET",
            success:function(data){
              $("#user_role").html(data);
              console.log("add_role");
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          }); 
    }
            
 }
           

</script>
<script type="text/javascript">
     function change_userrole_edit(){
    var modules = $("#edit_user_group").val();
    // alert(modules);
    if (modules =="") {

    }else{
            $.ajax({
            url:"/get_user_role_by_module/"+modules,
            type:"GET",
            success:function(data){
              $("#edit_user_role").html(data);
              console.log("edit_role");
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
    }
            
 }

</script>
<script>
function get_user_group(page, query){
      $.ajax({
        url:"/get_users_group/?page=" + page,
        type:"GET",
        data: {search_string: query},
        success:function(data){
          $('#tbl_user_group').html(data);
        }
      });  
    }
</script>

<script type="text/javascript">
    $('#add-user-group-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#add-user-group-modal').modal('hide');
            $('#add-user-group-frm').trigger('resset');
            get_user_group();
          }
        }
      });
    });
</script>
<script type="text/javascript">

    $('#edit-user-group-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#edit-user-group-modal').modal('hide');
            get_user_group();
          }
        }
      });
    });
</script>
<script type="text/javascript">

    $('#delete-user-group-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#delete-user-group-modal').modal('hide');
            get_user_group();
          }
        }
      });
    });
</script>
<script type="text/javascript">
    $('#add-reject-modal .add-row').click(function(e){
         e.preventDefault();
         var selectValues1 = {
          "": "Select Responsible",
          "Engineering": "Engineering",
          "Operator": "Operator",
          'Assembler': "Assembler",
          'Supplier': "Supplier",                             
          'Machine/Equipment': "Machine/Equipment", 
          "Others" : 'Others'
        };
        
        var selectValues2 = {
          "": "Select Recommended Action",
          "Undefined": "Undefined",
          "Rework": "Rework",
          "Replace": "Replace",
          "Scrap": "Scrap",
          "Use as is": "Use as is"
        };

        var selectValues4 = {
          "": "Select Operation",
          "Fabrication": "",
          "Operator": "Operator",
          'Assembler': "Assembler",
          'Supplier': "Supplier",
          "Others" : 'Others'
        };
        var row1 = '';
        var row2 = '';
        var col1 = '';


        $.each(selectValues1, function(i, d){
            row1 += '<option value="' + i + '">' + d + '</option>';
        });
        $.each(selectValues2, function(i, d){
            row2 += '<option value="' + i + '">' + d + '</option>';
        });
        var owner = $('#reject_owner').val();

        var thizz = document.getElementById('addreject-table');
        var id = $(thizz).closest('table').find('tr:last td:first').text();
        var validation = isNaN(parseFloat(id));

        if(validation){
            var new_id = 1;
        }else{
            var new_id = parseInt(id) + 1;
        }
        var len2 = new_id;
        var id_unique="count"+len2;
        $.ajax({
          url: "/get_material_type",
          type:"get",
          cache: false,
          success: function(response) {
            col1 += '<option value="">Select Material Type</option>';
              $.each(response.material_type, function(i, d){
                col1 += '<option value="' + d.reject_material_type_id + '">' + d.material_type + '</option>';
              });
              
              if (owner == "Operator") {

                var tblrow = '<tr>' +
                  '<td>'+len2+'</td>' +
                  '<td><input type="text" class="form-control select-input" name="reject_reason[]" required id="reject_reason"></td>' +
                  '<td><select name="m_type[]" class="form-control sel6">'+col1+'</select></td>' +
                  '<td><select name="responsible[]" class="form-control count-row sel6" required>'+row1+'</select></td>' +
                  '<td><select name="r_action[]" class="form-control sel6" required>'+row2+'</select></td>' +
                  '<td><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>' +
                  '</tr>';
              }else{
                var tblrow = '<tr>' +
                  '<td>'+len2+'</td>' +
                  '<td><input type="text" class="form-control select-input" name="reject_checklist[]" required id="reject_reason"></td>' +
                  '<td><input type="text" class="form-control select-input" name="reject_reason[]" required id="reject_reason"></td>' +
                  '<td><select name="m_type[]" class="form-control sel6">'+col1+'</select></td>' +
                  '<td><select name="responsible[]" class="form-control count-row sel6" required>'+row1+'</select></td>' +
                  '<td><select name="r_action[]" class="form-control sel6" required>'+row2+'</select></td>' +
                  '<td><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>' +
                  '</tr>';
              }
              $("#add-reject-modal #addreject-table").append(tblrow);
              $('.sel6').select2({
                  dropdownParent: $("#add-reject-modal"),
                  dropdownAutoWidth: false,
                  width: '100%',
                  cache: false
              });

            },
          error: function(response) {
            alert('Error!');
        }

      });

    });
</script>
<script>
function tbl_wip_list(page, query){
      $.ajax({
        url:"/tbl_wip_list/?page=" + page,
        type:"GET",
        data: {search_string: query},
        success:function(data){
          $('#tbl_wip_list').html(data);
        }
      });  
    }
</script>
<script type="text/javascript">
    $('#add-wip-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
                $('#add-wip-modal').modal('hide');
                $('#add-wip-frm').trigger("reset");
                tbl_wip_list();

          }
        }
      });
    });
</script>
<script type="text/javascript">
    $('#edit-wip-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
                $('#edit-wip-modal').modal('hide');
                $('#edit-wip-frm').trigger("reset");
                tbl_wip_list();

          }
        }
      });
    });
</script>
<script type="text/javascript">
    $('#delete-wip-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
                $('#delete-wip-modal').modal('hide');
                $('#delete-wip-frm').trigger("reset");
                tbl_wip_list();

          }
        }
      });
    });
</script>
<script>
	
	$('#add-break-button').click(function(e){
	         e.preventDefault();
	         var selectValues1 = {
	          "": "Select Category",
	          "Morning Break": "Morning Break",
	          "Afternoon Break": "Afternoon Break",
	          "Lunch Break": "Lunch Break",
	          "Evening Break": "Evening Break"
	        };
	
	        var row1 = '';
	        $.each(selectValues1, function(i, d){
	            row1 += '<option value="' + i + '">' + d + '</option>';
	        });
	
	        var thizz = document.getElementById('addbreak-table');
	        var id = $(thizz).closest('table').find('tr:last td:first').text();
	        var validation = isNaN(parseFloat(id));
	
	        if(validation){
	            var new_id = 1;
	        }else{
	            var new_id = parseInt(id) + 1;
	        }
	              //  alert(new_id);
	        var len2 = new_id;
	        var id_unique="count"+len2;
	
	        var tblrow = '<tr>' +
	          '<td>'+len2+'</td>' +
	          '<td><select name="shiftcategory[]" style="font-size:12px;" class="form-control count-row shiftbreak" required>'+row1+'</select></td>' +
	          '<td><input type="text" autocomplete="off" placeholder="From Time"class="form-control select-input timepicker" name="timein[]" required></td>' +
	          '<td><input type="text" autocomplete="off" placeholder="To Time" class="form-control select-input timepicker" name="timeout[]" required></td>' +
	          '<td><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>' +
	          '</tr>';
	
	         $("#add-shift-modal #addbreak-table").append(tblrow);
	
	          
	        $('.timepicker').timepicker({
	          // 'showDuration': true,
	                'timeFormat': 'g:i a',
	                'step': 5,
	                "minTime": '6',
	            "maxTime": '9:00pm'
	        });
	
	
	
	
	      });
	      $(document).on('click', '#edit-break-button', function(e){
	         e.preventDefault();
	         
	      });
	
	</script>
	<script type="text/javascript">
	    $(document).on('click', '.edit-shift-list', function(){
	      var shift_id = $(this).attr('data-shiftid');
	      var time_in = $(this).attr('data-timein');
	      var time_out = $(this).attr('data-timeout');
	      var shift_type = $(this).attr('data-shifttype');
	      var remarks = $(this).attr('data-remarks');
	      var break_inmin = $(this).attr('data-breakinmin');
	      var qty_capacity = $(this).attr('data-qtycapacity');
	      var operation_id = $(this).attr('data-operation');
	
	      $('#edit-shift-frm .operation').val(operation_id).prop('selected', true);
	      $('#edit-shift-frm .time_in').val(time_in);
	      $('#edit-shift-frm .time_out').val(time_out);
	      $('#edit-shift-frm .shift_type').val(shift_type);
	      $('#edit-shift-frm .remarks').val(remarks);
	      $('#edit-shift-frm .breaktime_in_min').val(break_inmin);
	      $('#edit-shift-frm .qty_capacity').val(qty_capacity);
	      $('#edit-shift-frm .shift_id').val(shift_id);
	      $('#edit-shift-modal #editbreak-table tbody').empty();
	
	         $.ajax({
	            url: "/show_edit_shift_break_time/"+shift_id,
	            success: function(data) {
	               var old_break = '';
	               if(data.success < 1){
	                $("#edit-shift-modal #old_ids").empty();
	                $('#edit-shift-modal #old_ids').find('input:text').val('');
	               }else{
	                  $.each(data.shift_break, function(i, d){
	                    var sel_id = d.category;
	                    var break_id = d.id;
	                    old_break += '<input type="hidden" name="old_break[]" value="'+d.id+'">';
	                    console.log(d.id);
	                    var selectValues1 = {
	                        "": "Select Category",
	                        "Morning Break": "Morning Break",
	                        "Afternoon Break": "Afternoon Break",
	                        "Lunch Break": "Lunch Break",
	                        "Evening Break": "Evening Break"
	                      };
	
	                      var row1 = '';
	                      $.each(selectValues1, function(i, d){
	                        selected = (i == sel_id) ? 'selected' : null;
	                          row1 += '<option value="' + i + '" '+selected+'>' + d + '</option>';
	                      });
	
	                      var thizz = document.getElementById('editbreak-table');
	                      var id = $(thizz).closest('table').find('tr:last td:first').text();
	                      var validation = isNaN(parseFloat(id));
	
	                      if(validation){
	                          var new_id = 1;
	                      }else{
	                          var new_id = parseInt(id) + 1;
	                      }
	                            //  alert(new_id);
	                      var len2 = new_id;
	                      var id_unique="count"+len2;
	
	                      var tblrow = '<tr>' +
	                        '<td>'+len2+'</td>' +
	                        '<td><input type="hidden" name="oldshiftbreakid[]"  value="'+break_id+'"><select name="oldshiftcategory[]" style="font-size:12px;" class="form-control count-row editshiftbreak" required>'+row1+'</select></td>' +
	                        '<td><input type="text" autocomplete="off" placeholder="From Time" value="'+ d.time_from +'" class="form-control select-input timepicker" name="oldtimein[]" required></td>' +
	                        '<td><input type="text" autocomplete="off" placeholder="To Time" value="'+ d.time_to +'" class="form-control select-input timepicker" name="oldtimeout[]" required></td>' +
	                        '<td><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>' +
	                        '</tr>';
	
	                      $("#edit-shift-modal #editbreak-table").append(tblrow);
	                      $("#edit-shift-modal #old_ids").html(old_break);
	                      $('.timepicker').timepicker({
	                        // 'showDuration': true,
	                              'timeFormat': 'g:i a',
	                              'step': 5,
	                              "minTime": '6',
	                          "maxTime": '9:00pm'
	                      });
	
	                  });
	               }
	               
	            },
	            error: function(data) {
	               alert('Error fetching data!');
	            }
	         });
	         
	    $('#edit-shift-modal').modal('show');
	    
	    });
	    
	$('#edit-break-button').click(function(e){
	         e.preventDefault();
	         var selectValues1 = {
	          "": "Select Category",
	          "Morning Break": "Morning Break",
	          "Afternoon Break": "Afternoon Break",
	          "Lunch Break": "Lunch Break",
	          "Evening Break": "Evening Break"
	        };
	
	        var row1 = '';
	        $.each(selectValues1, function(i, d){
	            row1 += '<option value="' + i + '">' + d + '</option>';
	        });
	
	        var thizz = document.getElementById('editbreak-table');
	        var id = $(thizz).closest('table').find('tr:last td:first').text();
	        var validation = isNaN(parseFloat(id));
	
	        if(validation){
	            var new_id = 1;
	        }else{
	            var new_id = parseInt(id) + 1;
	        }
	              //  alert(new_id);
	        var len2 = new_id;
	        var id_unique="count"+len2;
	
	        var tblrow = '<tr>' +
	          '<td>'+len2+'</td>' +
	          '<td><select name="newshiftcategory[]" class="form-control count-row editshiftbreak" style="font-size:12px;" required>'+row1+'</select></td>' +
	          '<td><input type="text" autocomplete="off" placeholder="From Time"class="form-control select-input timepicker" name="newtimein[]" required></td>' +
	          '<td><input type="text" autocomplete="off" placeholder="To Time" class="form-control select-input timepicker" name="newtimeout[]" required></td>' +
	          '<td><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>' +
	          '</tr>';
	
	         $("#edit-shift-modal #editbreak-table").append(tblrow);
	         $('.timepicker').timepicker({
	                      // 'showDuration': true,
	                            'timeFormat': 'g:i a',
	                            'step': 5,
	                            "minTime": '6',
	                        "maxTime": '9:00pm'
	                    });
	
	
	      });
        
	</script>
  
<script>
  $('#email_check').click(function(){
        if ($(this).is(":checked")) {
          $('#textboxlabel').text('Non User Recipient');
          $('#add-email-trans-modal tbody').empty();

            } else {
          $('#textboxlabel').text('User Recipient');
          $('#add-email-trans-modal tbody').empty();



            }
          
         

  });
  $('#email_trans_btn').click(function(e){
         e.preventDefault();
          $('#add-email-trans-modal').modal('show');
          $('#add-email-trans-modal tbody').empty();


  });
      
  $(document).on('click', '.delete-email-trans-list', function(){
    var type = $(this).data('etrans');
    var email = $(this).data('eemail');
    var id = $(this).data('emailtransid');

    $('#delete_etrans_label').text(type);
    $('#delete_email_label').text(email);
    $('#delete_email_id').val(id);
    $('#delete-email-trans-modal').modal('show');
  });
  $('#add-emailtrans-button').click(function(e){
         e.preventDefault();
         var col1 ='';
         var selectValues1 = {
          "": "Select Transaction Type",
          "Material Request-Local": "Material Request-Local",
          "Material Request-Imported": "Material Request-Imported",
          "Maintenance Request": "Maintenance Request",
          "FeedBacking": "FeedBacking"
        };
        var row1 = '';
        $.each(selectValues1, function(i, d){
            row1 += '<option value="' + i + '">' + d + '</option>';
        });

         $.ajax({
            url: "/get_employee_email",
            type:"get",
            cache: false,
            success: function(response) {
               col1 += '<option value="none">Select Email</option>';
               $.each(response.email, function(i, d){
                  col1 += '<option value="' + d.email + '">' + d.email + '</option>';
               });
               
                var thizz = document.getElementById('addemail-table');
                var id = $(thizz).closest('table').find('tr:last td:first').text();
                var validation = isNaN(parseFloat(id));
               $('#thead_id_email').show();
                if(validation){
                    var new_id = 1;
                }else{
                    var new_id = parseInt(id) + 1;
                }
                      //  alert(new_id);
                var len2 = new_id;
                var id_unique="count"+len2;

                if ($('#email_check').is(":checked")) {
                  var tblrow = '<tr>' +
                  '<td>'+len2+'</td>' +
                  '<td><select name="etranstype[]" class="form-control count-row emailtras_class" style="font-size:12px;width: 40%;" required>'+row1+'</select></td>' +
                  '<td><input name="emailtrans[]" type="email" style="font-size:12px;" class="form-control count-row" required></td>' +
                  '<td><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>' +
                  '</tr>';
                }else{
                  var tblrow = '<tr>' +
                  '<td>'+len2+'</td>' +
                  '<td><select name="etranstype[]" class="form-control count-row emailtras_class" style="font-size:12px;width: 40%;" required>'+row1+'</select></td>' +
                  '<td><select name="emailtrans[]" style="font-size:12px;width: 50%;" class="form-control count-row emailtras_class" required>'+col1+'</select></td>' +
                  '<td><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>' +
                  '</tr>';
                }
                

                $("#add-email-trans-modal #addemail-table").append(tblrow);
                $('.emailtras_class').select2({
                  dropdownParent: $("#add-email-trans-modal"),
                  dropdownAutoWidth: false,
                  width: '100%',
                  cache: false
                });
            },
            error: function(response) {
               alert('Error fetching Designation!');
            }

         });
         
      });

      $('#add-email-trans-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
                $('#add-email-trans-modal').modal('hide');
                $('#add-email-trans-frm').trigger("reset");
                // location.reload(true);
                tbl_email_trans();

          }
        }
      });
    });
    $('#delete-email-trans-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
                $('#delete-email-trans-modal').modal('hide');
                $('#delete-email-trans-frm').trigger("reset");
                // location.reload(true);
                tbl_email_trans();

          }
        }
      });
    });
    $(document).on('keyup', '#search_email_setup', function(){
      var query = $(this).val();
      var parent_tab = $("#qa_tab li a.active").attr('data-qatab');
      tbl_email_trans(1, query);  
    });
    $(document).on('click', '#tbl_email_trans_list_pagination a', function(event){
      event.preventDefault();
      var query = $("#search_email_setup").val();
      var page = $(this).attr('href').split('page=')[1];
      tbl_email_trans(page, query);

    });
</script>
<script type="text/javascript">
  function tbl_email_trans(page, query){
        $.ajax({
          url:"/get_tbl_email_trans_list/?page="+page,
          data: {search_string: query},
          type:"GET",
          success:function(data){
            $('#tbl_email_trans').html(data);
          }
        }); 
  }
</script>

<script>
  $('#add-late-deli-button').click(function(e){
         e.preventDefault();
          $('#add-late-delivery-modal').modal('show');
  });

  $(document).on('click', '.btn_edit_late_delivery', function(){
      var edit_late_reason = $(this).data('reason');
      var id = $(this).data('id');

      $('#orig_late_deli_reason').val(edit_late_reason);
      $('#edit_late_deli_reason').val(edit_late_reason);
      $('#transid').val(id);
  
       $('#edit-late-delivery-modal').modal('show');

    });
  
  
  $('#add-late-delivery-button').click(function(e){
         e.preventDefault();

                var thizz = document.getElementById('latedelivery-table');
                var id = $(thizz).closest('table').find('tr:last td:first').text();
                var validation = isNaN(parseFloat(id));
                if(validation){
                    var new_id = 1;
                }else{
                    var new_id = parseInt(id) + 1;
                }
                var len2 = new_id;
                var id_unique="count"+len2;

                  var tblrow = '<tr>' +
                  '<td>'+len2+'</td>' +
                  '<td><input name="late_delivery[]" text="text" style="font-size:12px;" class="form-control count-row" required></td>' +
                  '<td> <a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>' +
                  '</tr>';
                $("#add-late-delivery-modal #latedelivery-table").append(tblrow);
         
      });
</script>

<script type="text/javascript">
tbl_late_delivery_date();
  function tbl_late_delivery_date(page, query){
        $.ajax({
          url:"/get_late_delivery?page="+page,
          data: {search_string: query},
          type:"GET",
          success:function(data){
            $('#tbl_late_delivery_list').html(data);
          }
        }); 
  }
</script>
<script>
$(document).on('click', '#late_delivery_pagination a', function(event){
      event.preventDefault();
      var query = $("#search_late_delivery_setup").val();
      var page = $(this).attr('href').split('page=')[1];
      tbl_late_delivery_date(page, query);

    });
    $(document).on('keyup', '#search_late_delivery_setup', function(){
    var query = $(this).val();
    tbl_late_delivery_date(1, query);
  });
  $('#add-late-delivery-frm').submit(function(e){
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
                $('#add-late-delivery-modal').modal('hide');
                tbl_late_delivery_date();
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
        });
    });
    $('#edit-late-delivery-frm').submit(function(e){
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
                $('#edit-late-delivery-modal').modal('hide');
                tbl_late_delivery_date();
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
        });
    });
</script>
<script>

    $(document).on('click', '#add-opchecklist-fabrication-button', function(){
       $('#add-operator-checklist-modal .modal-title').text('Fabrication');
       $('#operator_owner_checklist').val('Operator');
       $('#reload_operator_checklist').val('Fabrication');
       
        $.ajax({
            url:"/get_workstation_list_from_checklist/"+ "Fabrication",
            type:"GET",
            success:function(data){
              $("#opchecklist_workstation_id").html(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
            $('#operator-checklist-table tbody').empty();
            $('#add-operator-checklist-modal').modal('show');

    });
    $(document).on('click', '#add-opchecklist-painting-button', function(){
       $('#add-operator-checklist-modal .modal-title').text('Painting');
       $('#operator_owner_checklist').val('Operator');
       $('#reload_operator_checklist').val('Painting');

       $.ajax({
            url:"/get_workstation_list_from_checklist/"+ "Painting",
            type:"GET",
            success:function(data){
              $("#opchecklist_workstation_id").html(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
        $('#operator-checklist-table tbody').empty();
        $('#add-operator-checklist-modal').modal('show');
    });
    $(document).on('click', '#add-opchecklist-assembly-button', function(){
       $('#add-operator-checklist-modal .modal-title').text('Wiring and Assembly');
       $('#operator_owner_checklist').val('Operator');
       $('#reload_operator_checklist').val('Asembly');

       $.ajax({
            url:"/get_workstation_list_from_checklist/"+ "Wiring and Assembly",
            type:"GET",
            success:function(data){
              $("#opchecklist_workstation_id").html(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
        $('#operator-checklist-table tbody').empty();
        $('#add-operator-checklist-modal').modal('show');
    });
    $('#add-operator-checklist-modal .add-row').click(function(e){
         e.preventDefault();
         var row = '';
         var row2 = '';
         var workstation = $("#opchecklist_workstation_id").val();
         $.ajax({
            url: "/get_reject_categ_and_process",
            type:"get",
            data:{workstation:workstation},
            success: function(response) {
               row += '<option value="none">--Type--</option>';
               $.each(response.category, function(i, d){
                  row += '<option value="' + d.reject_category_id + '">' + d.reject_category_name + '</option>';
               });
               row2 += '<option value="">--Process--</option>';
               $.each(response.process, function(i, d){
                  row2 += '<option value="' + d.process_id + '">' + d.process_name + '</option>';
               });

               var thizz = document.getElementById('operator-checklist-table');
               var id = $(thizz).closest('table').find('tr:last td:first').text();
               var validation = isNaN(parseFloat(id));
               if(validation){
                var new_id = 1;
               }else{
                var new_id = parseInt(id) + 1;
               }
              //  alert(new_id);
               var len2 = new_id;
               var id_unique="operator-count"+len2;
               // alert(id_unique);
               var tblrow = '<tr>' +
                  '<td>'+len2+'</td>' +
                  '<td><select name="operator_new_checklist_r_type[]" class="form-control operator-onchange-selection count-row operator-checklist-sel"   data-idcolumn='+id_unique+' required>'+row+'</select></td>' +
                  '<td><select name="operator_new_checklist_r_process[]" class="form-control count-row operator-checklist-sel">'+row2+'</select></td>' +
                  '<td><select name="operator_new_checklist_r_desc[]" class="form-control operator-checklist-sel" id='+id_unique+' required></select></td>' +
                  '<td><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>' +
                  '</tr>';

               $("#add-operator-checklist-modal #operator-checklist-table").append(tblrow);
               // autoRowNumberAddKPI();
               $('.operator-checklist-sel').select2({
                  dropdownParent: $("#add-operator-checklist-modal"),
                  dropdownAutoWidth: false,
                  width: '100%',
                  cache: false
                });
            },
            error: function(response) {
               alert('Connection Lost, pls try again');
            }
         });
      });
     
       $(document).on('change', '.operator-onchange-selection', function(){
           var owner = $('#operator_owner_checklist').val();
           var first_selection_data = $(this).val();
           var id_for_second_selection = $(this).attr('data-idcolumn');
           var format_id_for_second_selection = "#"+id_for_second_selection;
           var operation = $('#add-operator-checklist-modal .modal-title').text();
            $.ajax({
            url:"/get_reject_desc/"+first_selection_data+'/'+owner + '/'+ operation,
            type:"GET",
            success:function(data){
              $(format_id_for_second_selection).html(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
      });
       $(document).on("click", ".delete", function(){
        $(this).parents("tr").remove();
      });
</script>
<script type="text/javascript">
    $('#save-operator-checklist-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            if(data.reloadtbl == "Fabrication"){
              showNotification("success", data.message, "now-ui-icons ui-1_check");
              $('#add-operator-checklist-modal').modal('hide');
              operator_check_list_fabrication();
              $('#save-operator-checklist-frm').trigger("reset");
            }else if(data.reloadtbl == "Painting"){
              showNotification("success", data.message, "now-ui-icons ui-1_check");
              $('#add-operator-checklist-modal').modal('hide');
              operator_check_list_painting();
              $('#save-operator-checklist-frm').trigger("reset");
            }else{
              showNotification("success", data.message, "now-ui-icons ui-1_check");
              $('#add-operator-checklist-modal').modal('hide');
              operator_check_list_assembly();
              $('#save-operator-checklist-frm').trigger("reset");
            }
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });
</script>
<script>
operator_check_list_fabrication();
 function operator_check_list_fabrication(page, query){
    $.ajax({
          url:"/get_tbl_opchecklist_list_fabrication?page=" + page,
          type:"GET",
          data: {search_string: query},
          success:function(data){
            $('#tbl_opcheck_list_fabrication').html(data);
          }
        });
}
</script>
<script>
operator_check_list_assembly();
 function operator_check_list_assembly(page, query){
    $.ajax({
          url:"/get_tbl_opchecklist_list_assembly?page=" + page,
          type:"GET",
          data: {search_string: query},
          success:function(data){
            $('#tbl_opcheck_list_assembly').html(data);
          }
        });
}
</script>
<script>
operator_check_list_painting();
 function operator_check_list_painting(page, query){
    $.ajax({
          url:"/get_tbl_opchecklist_list_painting?page=" + page,
          type:"GET",
          data: {search_string: query},
          success:function(data){
            $('#tbl_opcheck_list_painting').html(data);
          }
        });
}
</script>
<script>  
   $(document).on('click', '#operator_checklist_list_pagination_fabrication a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    var query = $("#search_operator_reject_setup").val();
    operator_check_list_fabrication(page, query);
  });
  $(document).on('click', '#operator_checklist_list_pagination_assembly a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    var query = $("#search_operator_reject_setup").val();
    operator_check_list_assembly(page, query);

  });
  $(document).on('click', '#operator_checklist_list_pagination_painting a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    var query = $("#search_operator_reject_setup").val();
    operator_check_list_painting(page, query);

  });
  $(document).on('click', '.btn-delete-opchecklist', function(){
    var id = $(this).data('id');
    var workstation = $(this).data('workstation');
    var rejectlist = $(this).data('rejectchecklist');
    var operation = $(this).data('operation');
    var reloadtbl = $(this).data('reloadtbl');
    $('#delete_opchecklist_label').text(rejectlist);
    $('#delete_opworkstation_label').text(workstation);
    $('.operation-text').text("["+operation+"]");
    $('#delete_opchecklist_id').val(id);
    $('#delete_op_reloadtbl').val(reloadtbl);
    
    $('#delete-operator-checklist-modal').modal('show');
  });
  $('#delete-operator-checklist-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            if(data.reloadtbl == "Fabrication"){
              showNotification("success", data.message, "now-ui-icons ui-1_check");
              $('#delete-operator-checklist-modal').modal('hide');
              operator_check_list_fabrication();
              $('#delete-operator-checklist-frm').trigger("reset");
            }else if(data.reloadtbl == "Painting"){
              showNotification("success", data.message, "now-ui-icons ui-1_check");
              $('#delete-operator-checklist-modal').modal('hide');
              operator_check_list_painting();
              $('#delete-operator-checklist-frm').trigger("reset");
            }else{
              showNotification("success", data.message, "now-ui-icons ui-1_check");
              $('#delete-operator-checklist-modal').modal('hide');
              operator_check_list_assembly();
              $('#delete-operator-checklist-frm').trigger("reset");
            }
          }
        }
      });
    });
</script>
<script>
  tbl_material_type();
  function tbl_material_type(page, query){
        $.ajax({
          url:"/get_material_type_tbl?page="+page,
          data: {search_string: query},
          type:"GET",
          success:function(data){
            $('#tbl_material_type').html(data);
          }
        }); 
  }
  $(document).on('click', '#add-material-type-button', function(){
    $('#add-material-type-modal').modal('show');
  });
  $('#add-material-type-row').click(function(e){
    e.preventDefault();
      var thizz = document.getElementById('material-type-table');
      var id = $(thizz).closest('table').find('tr:last td:first').text();
      var validation = isNaN(parseFloat(id));
      if(validation){
        var new_id = 1;
      }else{
        var new_id = parseInt(id) + 1;
      }
      var len2 = new_id;
      var id_unique="count"+len2;
      var tblrow = '<tr>' +
        '<td>'+len2+'</td>' +
        '<td><input name="material_type[]" text="text" style="font-size:12px;" class="form-control count-row" required></td>' +
        '<td> <a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>' +
        '</tr>';
      $("#add-material-type-modal #material-type-table").append(tblrow);
         
  });
  $(document).on('click', '#material_type_pagination a', function(event){
      event.preventDefault();
      var query = $("#search_material_type_setup").val();
      var page = $(this).attr('href').split('page=')[1];
      tbl_material_type(page, query);

    });
    $(document).on('keyup', '#search_material_type_setup', function(){
    var query = $(this).val();
    tbl_material_type(1, query);
  });
  $('#add-material-type-frm').submit(function(e){
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
                $('#add-material-type-modal').modal('hide');
                tbl_material_type();
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
        });
    });
    $('#edit-material-type-frm').submit(function(e){
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
                $('#edit-material-type-modal').modal('hide');
                tbl_material_type();
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
        });
    });
    $(document).on('click', '.btn_edit_material_type', function(){
      var edit_material_type = $(this).data('reason');
      var id = $(this).data('id');

      $('#orig_material_type_setup').val(edit_material_type);
      $('#edit_material_type_setup').val(edit_material_type);
      $('#mtypeid').val(id);
  
       $('#edit-material-type-modal').modal('show');

    });

    $(document).on('click', '#add-cancelled-reason-button', function(){
    $('#add-cancelled-reason-modal').modal('show');
    $('#add-cancelled-reason-modal tbody').empty();

    });
    $('#add-reason-of-cancel-row').click(function(e){
    e.preventDefault();
      var thizz = document.getElementById('cancelled-reason-table');
      var id = $(thizz).closest('table').find('tr:last td:first').text();
      var validation = isNaN(parseFloat(id));
      if(validation){
        var new_id = 1;
      }else{
        var new_id = parseInt(id) + 1;
      }
      var len2 = new_id;
      var id_unique="count"+len2;
      var tblrow = '<tr>' +
        '<td>'+len2+'</td>' +
        '<td><input name="reasonofcancel[]" text="text" style="font-size:12px;" class="form-control count-row" required></td>' +
        '<td> <a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>' +
        '</tr>';
      $("#add-cancelled-reason-modal #cancelled-reason-table").append(tblrow);
    });
    $('#add-cancelled-reason-frm').submit(function(e){
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
                $('#add-cancelled-reason-modal').modal('hide');
                tbl_reason_for_cancellation_po();
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
        });
    });
    tbl_reason_for_cancellation_po();
  function tbl_reason_for_cancellation_po(page, query){
    $.ajax({
      url:"/tbl_reason_for_cancellation_po?page="+page,
      data: {search_string: query},
      type:"GET",
      success:function(data){
        $('#tbl_reason_for_cancellation_po').html(data);
      }
    }); 
  }
  $(document).on('click', '.btn_edit_reason_for_cancellation', function(){
      var edit_reason_for_cancellation = $(this).data('reason');
      var id = $(this).data('id');
      $('#orig_reason_for_cancellation_setup').val(edit_reason_for_cancellation);
      $('#edit_reason_for_cancellation_setup').val(edit_reason_for_cancellation);
      $('#edit_reason_for_cancellation_id').val(id);
      $('#edit-cancelled-reason-modal').modal('show');

    });
    $('#edit-cancelled-reason-frm').submit(function(e){
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
                $('#edit-cancelled-reason-modal').modal('hide');
                tbl_reason_for_cancellation_po();
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
        });
    });
    $(document).on('click', '.btn_delete_reason_for_cancellation', function(){
      var delete_reason_for_cancellation = $(this).data('reason');
      var id = $(this).data('id');
      $('#delete_label_reason_cancellation_id').text(delete_reason_for_cancellation);
      $('#delete_reason_cancellation_id').val(id);
      $('#delete_reason_cancellation').val(delete_reason_for_cancellation);
      $('#delete-cancelled-reason-modal').modal('show');
    });
    $('#delete-cancelled-reason-frm').submit(function(e){
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
                $('#delete-cancelled-reason-modal').modal('hide');
                tbl_reason_for_cancellation_po();
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
        });
    });
    $(document).on('click', '#reason_cancellation_pagination a', function(event){
      event.preventDefault();
      var query = $('#search_reason_cancelled_po').val();
      var page = $(this).attr('href').split('page=')[1];
      tbl_reason_for_cancellation_po(page, query);
    })
    $(document).on('keyup', '#search_reason_cancelled_po', function(){
      var query = $(this).val();
      tbl_reason_for_cancellation_po(1, query);
    });

    $(document).on('change', '#opchecklist_workstation_id', function(){
      $('#operator-checklist-table tbody').empty();
    });
    $('#add-checklist-painting-modal .add-row').click(function(e){
         e.preventDefault();
         var row = '';
         var row2 = '';
         var workstation = $("#painting_r_workstation_id").val();
         $.ajax({
            url: "/get_reject_categ_and_process",
            type:"get",
            cache: false,
            data:{workstation:workstation},
            success: function(response) {
               row += '<option value="none">--Type--</option>';
               $.each(response.category, function(i, d){
                  row += '<option value="' + d.reject_category_id + '">' + d.reject_category_name + '</option>';
               });
               row2 += '<option value="none">--Process--</option>';
               $.each(response.process, function(i, d){
                  row2 += '<option value="' + d.process_id + '">' + d.process_name + '</option>';
               });

               var thizz = document.getElementById('painting-reject-table');
               var id = $(thizz).closest('table').find('tr:last td:first').text();
               var validation = isNaN(parseFloat(id));
               if(validation){
                var new_id = 1;
               }else{
                var new_id = parseInt(id) + 1;
               }
               var len2 = new_id;
               var id_unique="paintcount"+len2;
               var tblrow = '<tr>' +
                  '<td>'+len2+'</td>' +
                  '<td><select name="new_checklist_r_type[]" class="form-control painting-onchange-selection count-row sel17"  data-idcolumn='+id_unique+' required>'+row+'</select></td>' +
                  '<td><select name="new_checklist_r_process[]" class="form-control count-row sel17">'+row2+'</select></td>' +
                  '<td><select name="new_checklist_r_desc[]" class="form-control sel17" id='+id_unique+' required></select></td>' +
                  '<td><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>' +
                  '</tr>';
               $("#add-checklist-painting-modal #painting-reject-table").append(tblrow);
               // autoRowNumberAddKPI();
               $('.sel17').select2({
                  dropdownParent: $("#add-checklist-painting-modal"),
                  dropdownAutoWidth: false,
                  width: '100%',
                  cache: false
                });
            },
            error: function(response) {
               alert('Error fetching Designation!');
            }
         });
      });
      $(document).on('change', '.painting-onchange-selection', function(){
           var owner = $('#painting_owner_checklist').val();
           var first_selection_data = $(this).val();
           var id_for_second_selection = $(this).attr('data-idcolumn');
           var format_id_for_second_selection = "#"+id_for_second_selection;
           var operation = $('#add-checklist-painting-modal .modal-title').text();
           
          $.ajax({
            url:"/get_reject_desc/"+first_selection_data+'/'+owner + '/' + operation,
            type:"GET",
            success:function(data){
              $(format_id_for_second_selection).html(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
      });
      $('#save-painting-checklist-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#add-checklist-painting-modal').modal('hide');
            check_list_fabrication();
            check_list_painting();
            check_list_assembly();
            
            $('#save-painting-checklist-frm').trigger("reset");
            // getAssignedTasks();
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });
</script>
@endsection
