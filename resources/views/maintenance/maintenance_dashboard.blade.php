@extends('layouts.user_app', [
    'namePage' => 'Maintenance',
    'activePage' => 'maintenance_dashboard',
])

@section('content')
<div class="panel-header">
  <div class="header text-center"> 
    <div class="row">
      <div class="col-md-8 offset-md-1" style="margin-top: -71px;">
        <table style="text-align: center; width: 100%;font-size: 11px;">
          <tr>
            <td style="width: 30%; border-right: 5px solid white;">
              <h5 class="title" >
                <div class="pull-right" style="margin-right: 20px;">
                  <span style="display: block; font-size: 14pt;">{{ date('M-d-Y') }}</span>
                  <span style="display: block; font-size: 8pt;">{{ date('l') }}</span>
                </div>
              </h5>
            </td>
            <td style="width: 20%; border-right: 5px solid white;">
              <h5 class="title" style="margin: auto; font-size: 15pt;"><span id="current-time">--:--:-- --</span></h5>
            </td>
            <td style="width: 50%">
              <h4 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Maintenance</h4>
              <span class="title text-left" style="margin-left: 20px; margin: auto 20pt;float:left;">
                <i>{{ Auth::user()->employee_name }} - {{ $user_details->designation_name }}</i>
              </span>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="content" id="form-style-box">
  <div class="row" style="margin-top: -185px;">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row" style="margin-top: -15px;">
                    <div class="col-md-12" style="padding: 10px;">
                        <ul class="nav nav-tabs" role="tablist" id="maintenance-dashboard-tabs">
                            <li class="nav-item">
                              <a class="nav-link active" data-toggle="tab" data-qatab="main_pending" href="#tab1" role="tab" aria-controls="tab1" aria-selected="false"><b>Pending Request</b></a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" data-toggle="tab" data-qatab="main_done" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false"><b>Done</b></a>
                            </li>
                          </ul>
                          <input type="text" id="search-maintenance-request" class="form-control pull-right" placeholder="Search" style="background-color: white; padding: 6px 8px; width:400px; margin-top:-40px;" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="card-body">
                  <div class="tab-content" style="min-height: 500px;">
                    <div class="tab-pane active" id="tab1" role="tabpanel" aria-labelledby="tab1">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="card" style="border-radius: 0 0 3px 3px;margin-top: -25px;">
                            <div class="card-body">
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="card" style="background-color: whitesmoke;">
                                    <div class="card-body">
                                      <div class="row">
                                        <div class="col-md-12" style="padding: 0;">
                                          <div id="tbl_pending_request"></div>
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
                            <div class="card" style="border-radius: 0 0 3px 3px;margin-top: -25px;">
                              <div class="card-body">
                                <div class="row">
                                  <div class="col-md-12">
                                    <div class="card" style="background-color: whitesmoke;">
                                      <div class="card-body">
                                        <div class="row">
                                          <div class="col-md-12" style="padding: 0;">
                                            <div id="tbl_done_request"></div>
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
<div class="modal fade" id="jt-workstations-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="min-width: 90%;">
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title font-weight-bold">Modal Title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
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
  <div class="modal fade" id="assign-maintenance-staff-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document" style="min-width: 30%;">
       <form action="/set_assigned_maintenance_staff" method="POST" id="assign-maintenance-staff-frm">
          @csrf
          <div class="modal-content">
             <div class="modal-header text-white" style="background-color: #0277BD;">
                <h5 class="modal-title" id="modal-title ">Assign Maintenance Staff<br>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                   <span aria-hidden="true">×</span>
                </button>
             </div>
             <div class="modal-body">
                 <div class="row">
                  <div class="col-md-12">
                    <div class="card">
                      
                      <input type="hidden" class="selected_prod_order" id="selected_prod_order">
                      <div class="card-body">
                        <input type="hidden" id='tbl_opration_id' name="operation_id">
                       <input type="hidden" id="m_request_id" name="maintenance_request_id">
                       <input type="hidden" id="date_reload_tbl" name="date_reload_tbl">
                       <input type="hidden" name="pagename" value="prod_sched" id="pagename">
                       <div id="default_shift_sched" style="margin-top: -10px;"></div>
                       <div id="old_ids"></div>
                         <table class="table table-bordered" style="margin-top: 5px;">
                           <col style="width: 90%;">
                           <col style="width: 10%;">
                           <tr>
                             <th class="text-center">Name</th>
                             <th></th>
                           </tr>
                           <tbody id="addassignstaff-table">
                           </tbody>
                         </table>
                       <div class="pull-left">
                         <button type="button" class="btn btn-info btn-sm" id="add-row-shift-btn">
                           <i class="now-ui-icons ui-1_simple-add"></i> Add Row
                         </button>
                       </div>
                      </div>
                    </div>
                   </div>
                     {{-- <div class="col-md-12">
                       <div class="form-group">
                        <input type="hidden" id="breakdown_id" name="breakdown_id">
                        <label for="set_assign_maintenance_staff">Maintenance Staff:</label>
                        <select id="set_assign_maintenance_staff" class="form-control" name="set_assign_maintenance_staff" required>
                            @foreach($maintence_staff as $row)
                                <option value="{{ $row->user_access_id }}">{{ $row->employee_name }}</option>
                            @endforeach
                        </select>
                      </div>
                     </div> --}}
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
  <div class="modal fade" id="confirm-task-for-breakdown-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document" style="min-width:45%;">
       <form action="/save_maintenance_request" method="POST" id="confirm-task-for-breakdown-frm">
          @csrf
          <div class="modal-content">
             <div class="modal-header text-white" style="background-color: #0277BD;">
                <h5 class="modal-title" id="modal-title ">Maintenance Request<br>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                   <span aria-hidden="true">×</span>
                </button>
             </div>
             <div class="modal-body">
                 <div class="row" id="tbl_maintenance_dtls">
                 </div>
             </div>
             <div class="modal-footer" id="submit_id_form">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" >Submit</button>
             </div>
          </div>
       </form>
    </div>
  </div>
  <div class="modal fade bd-example-modal-lg" id="print_modal_js_ws" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="min-width:60%; width:60%;">
        <div class="modal-content">
          <div class="modal-header" style="background-color: #ffffff;">
            <h5 class="modal-title">
            <img src="{{ asset('img/preview.png') }}" width="40">
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="printdiv" style="height:600px;overflow-y:auto;" >
            <div id="printmodalbody" style='page-break-after:always;'></div>
          </div>
          <div class="modal-footer" style="padding: 5px 10px;">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            <button id="btnPrint" type="button" class="btn btn-primary">Print</button>
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
#form-style-box .form-control, #assign-maintenance-staff-modal .form-control, #confirm-task-for-breakdown-modal .form-control{
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
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />

<script type="text/javascript" src="{{  asset('js/printThis.js') }}"></script>

<script>
  $(document).ready(function(){
    load_pending_request();
    load_completed_request();
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

    $('.select1').select2({
    dropdownParent: $("#create-task-for-breakdown-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
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
    function load_pending_request(page, query){
        $.ajax({
            url:"/get_pending_maintenance_request?page=" + page,
            type:"GET",
            data: {search_string: query},
            success:function(data){
                $('#tbl_pending_request').html(data);
            }
        });
    }
    function load_completed_request(page, query){
        $.ajax({
            url:"/get_completed_maintenance_request?page=" + page,
            type:"GET",
            data: {search_string: query},
            success:function(data){
                $('#tbl_done_request').html(data);
            }
        });
    }
    $(document).on('keyup', '#search-maintenance-request', function(){
        var query = $(this).val();
            var parent_tab = $("#maintenance-dashboard-tabs li a.active").attr('data-qatab');
        if(parent_tab == "main_pending"){
            load_pending_request(1, query);
        }else if(parent_tab == "main_done"){
            load_completed_request(1, query);
        }else{
        }
    });
    $(document).on('click', '#paginate-maintenance-request-pending a', function(event){
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        var query = $("#search-maintenance-request").val();
        load_pending_request(page, query);
    });
    $(document).on('click', '#paginate-maintenance-request-done a', function(event){
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        var query = $("#search-maintenance-request").val();
        load_pending_request(page, query);
    });
    $(document).on('click', '.assign-main-staff', function(event){
        event.preventDefault();
        var id = $(this).data('id');
        var staff = $(this).data('staff');
        tbl_assigned_maintenance_staff(id);
        $('#breakdown_id').val(id);
        $('#m_request_id').val(id);
        $('#assign-maintenance-staff-modal').modal('show');
        $('#set_assign_maintenance_staff').val(staff);        
    });
    $('#assign-maintenance-staff-frm').submit(function(e){
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
                $('#assign-maintenance-staff-modal').modal('hide');
                var query = $("#search-maintenance-request").val();
                load_pending_request(1, query);
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
        });
    });
    $(document).on('click', '.complete-task', function(event){
        event.preventDefault();
        var id = $(this).data('id');
        $.ajax({
            url:"/get_maintenance_request_details",
            type:"GET",
            data: {id: id},
            success:function(data){
                $('#confirm-task-for-breakdown-modal .modal-title').text('Maintenance Request');
                $('#tbl_maintenance_dtls').html(data);
                $('#tbl_maintenance_dtls').html(data);
                $("#maintenance_status" ).prop("disabled", false);
                $("#date_resolve_picker" ).prop("disabled", false);
                $("#maintennace_type" ).prop("disabled", false);
                $("#findings" ).prop("disabled", false);
                $("#work_done" ).prop("disabled", false);
                $("#t_duration" ).prop("disabled", false);
                $("#submit_id_form" ).show();
            }
        });
        $('#confirm-task-for-breakdown-modal').modal('show');
        // $('#set_assign_maintenance_staff').val(staff);

        
        // var page = $(this).attr('href').split('page=')[1];
        
    });

    $(document).on('click', '#create-maintenance-btn', function(event){
        event.preventDefault();
        // $('#create-task-for-breakdown-modal').modal('show');
    });
    $(document).on('change', '#date_resolve_picker', function(event){
        event.preventDefault();
        var date_resolve = $(this).val();
        var date_reported = $('#date_reported').val();
        console.log(date_resolve);
        console.log(date_reported);
        console.log(timeDiffCalc(new Date(date_reported), new Date(date_resolve)));
        console.log(diffinhrs(new Date(date_reported), new Date(date_resolve)));
        $('#t_duration').val(timeDiffCalc(new Date(date_reported), new Date(date_resolve)));

    });
    $('#confirm-task-for-breakdown-frm').submit(function(e){
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
                $('#confirm-task-for-breakdown-modal').modal('hide');
                var query = $("#search-maintenance-request").val();
                load_pending_request(1, query);
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
        });
    });
});
function add_row(){
  $.ajax({
    url:"/get_maintenance_staff",
    type:"GET",
    success: function(data){
      var row1 = '<option value=""></option>';
      $.each(data.maintenance, function(i, d){
        row1 += '<option value="' + d.user_access_id + '">' + d.employee_name + '</option>';
      });
      var thizz = document.getElementById('addassignstaff-table');
      var id = $(thizz).closest('table').find('tr:last td:first').text();
      var validation = isNaN(parseFloat(id));
      if(validation){
        var new_id = 1;
      }else{
        var new_id = parseInt(id) + 1;
      }
      var len2 = new_id;
      var tblrow = '<tr>' +
        '<td style="display:none;">'+len2+'</td>' +
        '<td class="p-1"><div class="form-group m-0"><input type="hidden"><select name="newstaff[]" class="form-control m-0 count-row" required>'+row1+'</select></div></td>' +
        '<td class="p-1 text-center"><button type="button" class="btn btn-danger btn-icon btn-icon-mini m-0 remove-row"><i class="now-ui-icons ui-1_simple-remove"></i></button></td>' +
        '</tr>';
      
      $("#assign-maintenance-staff-modal #addassignstaff-table").append(tblrow);
    } 
  });
}
$('#add-row-shift-btn').click(function(e){
      add_row();
});
function tbl_assigned_maintenance_staff(id){
  $("#addassignstaff-table").empty();
  $.ajax({
    url:"/get_current_assigned_maintenance",
    type:"GET",
    data: {id: id},
    success: function(data){
      var old_staff = '';
      $.each(data.old_staff, function(i, d){
	      var sel_id = d.user_access_id;
	      var break_id = d.assigned_maintenance_staff_id;
	      old_staff += '<input type="hidden" name="old_staff_main[]" value="'+d.assigned_maintenance_staff_id+'">';
	      console.log(d.assigned_maintenance_staff_id);
        // var s_type= d.shift_type;
	      var row1 = '';
	      $.each(data.maintenance_staff, function(i, d){
	        selected = (d.user_access_id == sel_id) ? 'selected' : null;
	        row1 += '<option value="' + d.user_access_id  + '" '+selected+'>' + d.employee_name + '</option>';
	      });
	      var thizz = document.getElementById('addassignstaff-table');
	      var id = $(thizz).closest('table').find('tr:last td:first').text();
	      var validation = isNaN(parseFloat(id));
	      if(validation){
	        var new_id = 1;
	      }else{
	        var new_id = parseInt(id) + 1;
	      }
	      var len2 = new_id;
        // var id_stype= "#" + id_unique2;
	      var tblrow = '<tr>' +
          '<td style="display:none;">'+len2+'</td>' +
	        '<td class="p-1"><div class="form-group m-0"><input type="hidden" name="oldstaff_main_id[]"  value="'+break_id+'"><select name="oldstaff[]"  class="form-control m-0 count-row onchange-shift-select" required>'+row1+'</select></div></td>' +
	        '<td class="p-1 text-center"><button type="button" class="btn btn-danger btn-icon btn-icon-mini m-0 remove-row"><i class="now-ui-icons ui-1_simple-remove"></i></button></td>' +
	      '</tr>';
	      $("#assign-maintenance-staff-modal #addassignstaff-table").append(tblrow);
	      $("#assign-maintenance-staff-modal #old_ids").html(old_staff);
        // $(id_stype).val(s_type);
	    });
    }
 }); 
}
function timeDiffCalc(dateFuture, dateNow) {
  // console.log(dateFuture);
  // console.log(dateNow);

    let diffInMilliSeconds = Math.abs(dateFuture - dateNow) / 1000;

    // calculate days
    const days = Math.floor(diffInMilliSeconds / 86400);
    diffInMilliSeconds -= days * 86400;
    console.log('calculated days', days);

    // calculate hours
    const hours = Math.floor(diffInMilliSeconds / 3600) % 24;
    diffInMilliSeconds -= hours * 3600;
    console.log('calculated hours', hours);

    // calculate minutes
    const minutes = Math.floor(diffInMilliSeconds / 60) % 60;
    diffInMilliSeconds -= minutes * 60;
    console.log('minutes', minutes);

    let difference = '';
    if (days > 0) {
      difference += (days === 1) ? `${days} day, ` : `${days} days, `;
    }

    difference += (hours === 0 || hours === 1) ? `${hours} hour, ` : `${hours} hours, `;

    difference += (minutes === 0 || hours === 1) ? `${minutes} minutes` : `${minutes} minutes`; 

    return difference;
  }
  function diffinhrs(dateFuture, dateNow){
    let diffInMilliSeconds = Math.abs(dateFuture - dateNow) / 1000;
    const hours = Math.floor(diffInMilliSeconds);
    return hours;

  }
  $(document).on('click', '.printbtnprint', function(){
  var divname = $(this).data('id');
    $.ajax({
    url: "/print_maintenance_form/",
    data: {id:divname},
    type:"GET",
    success:function(data){
      if (data.success < 1) {
      showNotification("danger", data.message, "now-ui-icons travel_info");
      }else{
      $('#printmodalbody').html(data);
      $('#print_modal_js_ws').modal('show');
      }
    },
    error: function(jqXHR, textStatus, errorThrown) {
    console.log(jqXHR);
    console.log(textStatus);
    console.log(errorThrown);
    }
    });
  });
  $('#btnPrint').on("click", function () {
  
  $('#printmodalbody').printThis({
  });
});
</script>

<script type="text/javascript">
  $(document).on('click', '.complete-task-view', function(event){
    event.preventDefault();
    var id = $(this).data('id');
    $.ajax({
        url:"/get_maintenance_request_details",
        type:"GET",
        data: {id: id},
        success:function(data){
            $('#tbl_maintenance_dtls').html(data);
            $('#confirm-task-for-breakdown-modal .modal-title').text('Maintenance Request Details');
            $("#maintenance_status" ).prop("disabled", true);
            $("#date_resolve_picker" ).prop("disabled", true);
            $("#maintennace_type" ).prop("disabled", true);
            $("#findings" ).prop("disabled", true);
            $("#work_done" ).prop("disabled", true);
            $("#t_duration" ).prop("disabled", true);
            $("#maintenance_status" ).css("background-color", 'white');
            $("#date_resolve_picker" ).css("background-color", 'white');
            $("#maintennace_type" ).css("background-color", 'white');
            $("#findings" ).css("background-color", 'white');
            $("#work_done" ).css("background-color", 'white');
            $("#t_duration" ).css("background-color", 'white');
            $("#maintenance_status" ).css("color", 'black');
            $("#date_resolve_picker" ).css("color", 'black');
            $("#maintennace_type" ).css("color", 'black');
            $("#findings" ).css("color", 'black');
            $("#work_done" ).css("color", 'black');
            $("#t_duration" ).css("color", 'black');
            // $("#submit_id_form" ).hide();

            
            $('#confirm-task-for-breakdown-modal').modal('show');
        }
    });
    
    // $('#set_assign_maintenance_staff').val(staff);

    
    // var page = $(this).attr('href').split('page=')[1];
    
});
</script>
@endsection