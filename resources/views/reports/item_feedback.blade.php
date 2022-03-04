@extends('layouts.user_app', [
  'namePage' => 'MES',
  'activePage' => 'item_feedback',
])
@section('content')
@include('modals.item_track_modal')
<div class="panel-header">
  <div class="header text-center" style="margin-top: -70px;">
		<div class="row">
			<div class="col-md-12">
				<table style="text-align: center; width: 60%;">
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
							<h2 class="title" style="margin: auto; font-size: 17pt;"><span id="current-time">--:--:-- --</span></h2>
						</td>
						<td style="width: 50%">
							<h2 class="title text-left" style="margin-left: 20px; margin: auto 20pt; font-size: 19pt;">Production Order(s)</h2>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="content" style="margin-top: -200px;">
  <div class="row">
    <div class="col-md-4 offset-md-8" style="margin-bottom: -50px; z-index: 1;">
      <div class="pull-right">
        <button type="button" class="btn btn-secondary" id="reload-list">
          <i class="now-ui-icons loader_refresh"></i> Refresh List
        </button>
        <button type="button" class="btn btn-primary" id="manual-production-create-btn">
          <i class="now-ui-icons ui-1_simple-add"></i> Create Production Order
        </button>
      </div>
    </div>
    {{--  start  --}}
    <div class="col-md-12" style="min-height:440px;">
      <div class="panel panel-default">
        <div class="panel-body panel-body">
          <div class="col-sm-12 ticket-status-widget pt-" role="tabpanel" aria-expanded="true" aria-hidden="false">
            <div class="ui-tab-container ui-tab-default">
              <div justified="true" class="ui-tab">
                <ul class="nav nav-tabs nav-justified">
                  <li class="tab production-orders-tab custom-nav-link" heading="Justified" style="background-color: #808495 !important">
                    <a data-toggle="tab" href="#tab-production-orders">
                      <span class="tab-number" id="production-orders-total">0</span> 
                      <span class="tab-title">Production Order(s)</span> 
                    </a>
                  </li>
                  
                  <li class="tab search-tab custom-nav-link" heading="Justified">
                    <a data-toggle="tab" href="#tab-search">
                      <span class="tab-number" id="item-tracking-total">0</span> 
                      <span class="tab-title">Order Tracking</span> 
                    </a>
                  </li>
                </ul>
  
                <div class="tab-content">
                  <div class="tab-pane active" id="tab-production-orders">
                    {{-- All Production Orders --}}
                    <div class="tab-heading tab-heading--gray">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-8">
                            <input class='d-none' type="text" value="" id="current-status">
                            @php
                              $status_arr = ['Not Started','In Progress','Task Queue','Cancelled','Ready for Feedback','Completed'];
                            @endphp
                            <div class="row">
                              @foreach ($status_arr as $status)
                                <label class="PillList-item">
                                  <input type="checkbox" class="production-orders-checkbox" value="{{ $status }}">
                                  <span class="PillList-label">{{ $status }}
                                  </span>
                                </label>
                              @endforeach
                            </div>
                          </div>
                          <div class="col-4">
                            <div class="form-group mr-2">
                              <input type="text" id="production-orders-search" class="form-control bg-white search-filter" placeholder="Search" data-status="Production Orders" data-div="#production-orders-div">
                            </div>
                          </div>
                        </div>
                        
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12" id="production-orders-div" style="min-height:500px; border-top: 1px solid #D3D7DA;"></div>
                    </div>
                  </div>
                  
                  <div class="tab-pane" id="tab-search">
                    {{--  item tracking  --}}
                    <div class="tab-heading tab-heading--ltgray">
                      <h4>Item Tracking</h4>
                    </div>
                    <div class="row">
                      <div class="col-md-4 offset-md-8" style="margin-top: -50px;">
                        <div class="form-group mr-2">
                          <input type="text" class="form-control bg-white item-tracking-search" placeholder="Search">
                        </div>
                      </div>
                      <div class="col-md-12" id="item-tracking-div" style="min-height:500px;"></div>
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
<div class="modal fade bd-example-modal-lg" id="print_modal_js_ws" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document" style="min-width:60%; width:60%;">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #ffffff;">
        <img src="{{ asset('img/preview.png') }}" width="40">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="printdiv" style="height:600px;overflow-y:auto;" >
          <div id="printmodalbody"></div>
        </div>
        <div class="modal-footer" style="padding: 5px 10px;">
          <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
          <button id="btnPrint" type="button" class="btn btn-primary">Print</button>
        </div>
      </div>
  </div>
</div>

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
    width: 9%;
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
  
</style>
{{--  end  --}}

<div id="loader-wrapper" hidden>
  <div id="loader"></div>
  <div class="loader-section section-left"></div>
  <div class="loader-section section-right"></div>
</div>

<style type="text/css">
  #loader-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 99999;
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
    position: fixed;
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

<!-- Modal Manual Create Production Prder -->
<div class="modal fade" id="manual-production-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="min-width: 80%;">
    <form action="/manual_create_production_order" method="post" autocomplete="off">
      @csrf
      <input type="hidden" value="0" name="is_stock_item">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Create Production Order</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Reference Type</label>
                    <select class="form-control" id="sel-reference-type" name="reference_type">
                      <option value="SO">Sales Order</option>
                      <option value="MREQ">Material Request</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6" id="input-so-div">
                  <div class="form-group">
                    <label>Sales Order</label>
                    <input class="form-control sel-reference-no sel-so" id="sel-so" name="sales_order" data-type="SO" required>
                  </div>
                </div>
                <div class="col-md-6" id="input-mreq-div" hidden>
                  <div class="form-group">
                    <label>Material Request</label>
                    <input class="form-control sel-reference-no sel-mreq" id="sel-mreq" name="material_request" data-type="MREQ" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Item to Manufacture</label>
                    <input type="text" class="form-control" id="sel-item" name="item_code" maxlength="7" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Item Classification</label>
                    <input type="text" name="item_classification" class="form-control readonly">
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Description</label>
                    <textarea style="min-height: 110px;" name="description" class="form-control readonly"></textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Qty to Manufacture</label>
                    <input type="text" name="qty" class="form-control" value="0" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Stock UoM</label>
                    <input type="text" name="stock_uom" class="form-control readonly">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Customer</label>
                    <input type="text" name="customer" class="form-control readonly">
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Project</label>
                    <input type="text" name="project" class="form-control readonly">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Delivery Date</label>
                    <input type="text" name="delivery_date" class="form-control readonly">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Classification</label>
                    <input type="text" name="classification" class="form-control readonly">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Parent Item Code</label>
                    <input type="text" class="form-control" name="parent_code" id="sel-parent-code" maxlength="7">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Sub Parent Item Code</label>
                    <input type="text" class="form-control" name="sub_parent_code" id="sel-sub-parent-code" maxlength="7">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Planned Start Date</label>
                    <input type="text" name="planned_date" class="form-control date-picker">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="form-check-input" type="checkbox" id="has-no-bom-checkbox" name="custom_bom">
                      <span class="form-check-sign"></span> Item has no BOM
                    </label>
                  </div>
                </div>
                <div class="col-md-4">
                  <input type="hidden" name="is_reviewed" value="0">
                  <label>BOM No</label>
                  <div class="input-group">
                    <select class="form-control" id="sel-bom" name="bom" required></select>
                    <div class="input-group-append p-0" style="height: 100%;">
                      <button class="btn btn-info mt-0 mb-0" type="button" id="view-bom-btn" style="padding: 12px;"><i class="now-ui-icons ui-1_zoom-bold"></i></button>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Target Warehouse</label>
                    <select class="form-control" name="target" required id="target-wh">
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row" id="manual-material-operation-row">
            <!-- <div class="col-md-6">
              <h6 class="title m-2">Material(s)</h6>
              <table class="table table-bordered" border="1" style="margin-top: 17px;">
                <tr>
                  <th style="width: 30%;" class="text-center">Item Code</th>
                  <th style="width: 30%;" class="text-center">Source Warehouse</th>
                  <th style="width: 30%;" class="text-center">Required Qty</th>
                  <th style="width: 10%;" class="text-center">Action</th>
                </tr>
                <tbody id="material-table-tbody"></tbody>
                <tfoot>
                  <td colspan="4">
                    <button class="btn btn-primary" type="button" style="padding: 5px 8px;" id="material-add-row-btn">
                      <i class="now-ui-icons ui-1_simple-add"></i> Add Row
                    </button>
                  </td>
                </tfoot>
              </table>
            </div> -->

            <div class="col-md-6 offset-md-3">
              <div class="alert alert-warning text-center" id="manual-prod-note" role="alert">
                <div class="container">
                 
                  <strong>Note:</strong> The selected item is a product bundle. <span style="font-size: 9pt;" id="view-bundle-components-btn"><a href="#"><i class="now-ui-icons ui-1_zoom-bold" style="font-size: 7pt;"></i> View Components</span></a>
                 
                </div>
              </div>
              <div class="row">
                <div class="col-md-8 offset-md-2">
                  <table style="width: 100%;" class="mb-2">
                    <tr>
                      <td style="width: 45%;"><h6 class="title text-center m-0">Operation(s)</h6></td>
                      <td style="width: 55%;"><select name="operation_id" class="form-control m-0" name="operation_id"></select></td>
                    </tr>
                  </table>
                </div>
              </div>
              
              
              <table class="table table-bordered" border="1">
                <tr>
                  <th style="width: 50%;" class="text-center">Workstation</th>
                  <th style="width: 40%;" class="text-center">Process</th>
                  <th style="width: 10%;" class="text-center">Action</th>
                </tr>
                <tbody id="operations-table-tbody">
                  
                </tbody>
                <tfoot>
                  <td colspan="4">
                    <button class="btn btn-primary" type="button" style="padding: 5px 8px;" id="operation-add-row-btn">
                      <i class="now-ui-icons ui-1_simple-add"></i> Add Row
                    </button>
                  </td>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-info">Submit</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Delete Pending Material Transfer for Manufacture Modal -->
<div class="modal fade" id="delete-pending-mtfm-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form action="#" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title" id="modal-title">Cancel Request</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row p-0">
            <div class="col-md-12 p-1">
              <input type="hidden" name="sted_id">
              <input type="hidden" name="ste_no">
              <input type="hidden" name="production_order">
              <p class="text-center m-0">Cancel request for <span class="font-weight-bold"></span> <span class="font-weight-bold"></span></p>
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


<!-- Modal Create STE Production Order -->
<div class="modal fade" id="create-ste-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form action="#" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Create Stock Entry</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-8 offset-md-2 p-0">
              <div class="form-group text-center">
                <input type="hidden" name="production_order">
                <input type="hidden" name="item_code">
                <input type="hidden" name="manual" value="1">
                <p style="font-size: 12pt;" class="text-center m-0">Production Order</p>
                <p style="font-size: 12pt;" class="text-center m-0">Item Code</p>
                <p style="font-size: 12pt;" class="text-center m-0">
                  <span>Qty to Manufacture = </span><span class="font-weight-bold">0</span> <span class="font-weight-bold">UoM</span></p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="padding: 5px 10px;">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="modal fade" id="feedbacked-log-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="min-width:40%;">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Feedbacked Log/s</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12" id="tbl_feedbacked_logs">
              
            </div>
          </div>
        </div>
        <div class="modal-footer" style="padding: 5px 10px;">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
  </div>
</div>
<div class="modal fade" id="reset-workstation-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="min-width:40%;">
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title" id="modal-title">Reset Workstation</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="col-md-12" id="tbl_workstation_reset">
            
          </div>
        </div>
      </div>
  </div>
</div>
<div class="modal fade" id="prod-list-confirm-reset-workstation-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form action="/reset_workstation_data" method="POST" id="prod-list-reset-works-frm">
      @csrf
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title">Confirmation</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
              <div class="col-md-12">
                 <input type="hidden" name="reset_job_ticket_id"  class="reset_job_ticket_id">
                 <input type="hidden" name="reset_prod"  class="reset_prod">
                 <input type="hidden" name="reload_tbl"  class="reset_reload_tbl">

                 <div class="row">
                   <div class="col-sm-12"style="font-size: 12pt;">
                       <label> Are you sure you want to reset <span class="reset_job_ticket_workstation" style="font-weight: bold;"></span> ?</label>
                   </div>               
                 </div>
              </div>
          </div>
        </div>
        <div class="modal-footer" style="padding: 5px 10px;">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </div>
    </form>
  </div>
</div>

<style type="text/css">
  .ui-autocomplete {
    position: absolute;
    z-index: 2150000000 !important;
    cursor: default;
    border: 2px solid #ccc;
    padding: 5px 0;
    border-radius: 2px;
  }

  .breadcrumb-c {
    font-size: 10pt;
    font-weight: bold;
    padding: 0px;
    background: transparent;
    list-style: none;
    overflow: hidden;
    margin-top: 10px;
    margin-bottom: 10px;
    width: 100%;
    border-radius: 4px;
  }

  .breadcrumb-c>li {
    display: table-cell;
    vertical-align: top;
    width: 1%;
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

</style>

<iframe src="#" id="iframe-print" style="display: none;"></iframe>

@include('modals.stock_withdrawal_modal')

@endsection

@section('script')
<link rel="stylesheet" href="{{ asset('css/jquery-ui-1-12.css') }}">
<script src="{{ asset('js/jquery-ui-1.12.js') }}"></script>

 <link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
<script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>
{{--  
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />   --}}
<script type="text/javascript" src="{{  asset('js/printThis.js') }}"></script>

<script>
$(document).ready(function(){
  $(document).on('click', '#add-operation-btn', function(){
    var workstation = $('#sel-workstation option:selected').text();
    var wprocess = $('#sel-process').val();
    if (!$('#sel-workstation').val()) {
      showNotification("info", 'Please select Workstation', "now-ui-icons travel_info");
      return false;
    }
    var rowno = $('#bom-workstations-tbl tr').length;
    var sel = '<div class="form-group" style="margin: 0;"><select class="form-control form-control-lg">' + $('#sel-process').html() + '</select></div>';
    if (workstation) {
      var markup = "<tr><td class='text-center'>" + rowno + "</td><td>" + workstation + "</td><td>" + sel + "</td><td class='td-actions text-center'><button type='button' class='btn btn-danger delete-row'><i class='now-ui-icons ui-1_simple-remove'></i></button></td></tr>";
      $("#bom-workstations-tbl tbody").append(markup);
    }
  });
  $(document).on('click', '#submit-bom-review-btn', function(){
    var production_order = $('#production-order-val-bom').val();
    var operation_id = $('#operation_id_update_bom').val();
    var id = [];
    var workstation = [];
    var wprocess = [];
    var workstation_process = [];
    var bom = $('#bom-workstations-tbl input[name=bom_id]').val();
    var user = $('#bom-workstations-tbl input[name=username]').val();
    // var operation = $('#bom-workstations-tbl input[name=operation]').val();
    $("#bom-workstations-tbl > tbody > tr").each(function () {
      id.push($(this).find('span').eq(0).text());
      workstation.push($(this).find('td').eq(1).text());
      wprocess.push($(this).find('select').eq(0).val());
      workstation_process.push($(this).find('select option:selected').eq(0).text());
    });
    var filtered_process = wprocess.filter(function (el) {
      return el != null && el != "";
    });
    if (workstation.length != filtered_process.length) {
      showNotification("danger", 'Please select process', "now-ui-icons travel_info");
      return false;
    }
    var processArr = workstation_process.sort();
    var processDup = [];
    for (var i = 0; i < processArr.length - 1; i++) {
        if (processArr[i + 1] == processArr[i]) {
            processDup.push(processArr[i]);
            showNotification("danger", 'Process <b>' + processArr[i] + '</b> already exist.', "now-ui-icons travel_info");
            return false;
        }
    }
    $.ajax({
      url: '/submit_bom_review/' + bom,
      type:"POST",
      data: {user: user, id: id, workstation: workstation, wprocess: wprocess, production_order: production_order, operation:operation_id},
      success:function(data){
        if(data.status) {
          $('#review-bom-modal').modal('hide');
          $('#manual-production-modal input[name="is_reviewed"]').val(1);
          showNotification("success", data.message, "now-ui-icons ui-1_check");
        } else {
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
  
  $('#manual-material-operation-row').hide();
  $('#has-no-bom-checkbox').click(function(){
    $('#manual-material-operation-row').toggle();
    if($(this).is(':checked')){
      $('#sel-bom').attr('disabled', true);
    }else{
      $('#sel-bom').removeAttr('disabled');
    }
  });

  get_operations();
  $('#material-add-row-btn').click(function(e){
    e.preventDefault();
    row = '<tr>' +
      '<td><input type="text" class="form-control m-0 material-item-code" name="raw_item_code[]"></td>' +
      '<td><input type="text" class="form-control m-0 material-s-wh" name="source_warehouse[]"></td>' +
      '<td><input type="text" class="form-control m-0" name="raw_required_qty[]"></td>' +
      '<td><button class="btn btn-danger remove-row-btn" type="button" style="padding: 5px 8px;">Remove</button></td>' +
    '</tr>';

    $('#material-table-tbody').append(row);
    $('.material-item-code').autocomplete({
      source:function(request,response){
        $.ajax({
          url: '/items',
          dataType: "json",
          data: {
              term : request.term
          },
          success: function(data) {
            response(data);
          }
      });
      },
      minLength: 1,
    });

    $('.material-s-wh').autocomplete({
      source:function(request,response){
        $.ajax({
          url: '/warehouses',
          dataType: "json",
          data: {
              term : request.term
          },
          success: function(data) {
            response(data);
          }
      });
      },
      minLength: 1
    });
  });

  $(document).on('click', '.remove-row-btn', function(e){
    e.preventDefault();
    
    $(this).closest("tr").remove();
  });

  $('#operation-add-row-btn').click(function(e){
    e.preventDefault();
    
    var operation_id = $('#manual-production-modal select[name="operation_id"]').val();
    if(!operation_id){
      showNotification("info", 'Select Operation first', "now-ui-icons travel_info");
    }
    if(operation_id){
      $.ajax({
        url: "/workstations/" + operation_id,
        type:"GET",
        datatype: "json",
        async: false,
        success: function(data){
          opt = '<option value="">Select Workstation</option>';
           $.each(data, function(i, v){
             opt += '<option value="' + v.workstation_id + '">' + v.workstation_name + '</option>';
           });
        }
      });
  
      var $row = '<tr>' +
        '<td><select name="workstation_id[]" class="form-control m-0">' + opt + '</select></td>' +
        '<td><select name="process_id[]" class="form-control m-0"></select></td>' +
        '<td><button class="btn btn-danger remove-row-btn" type="button" style="padding: 5px 8px;">Remove</button></td>' +
      '</tr>';
  
      $('#operations-table-tbody').append($row);
    }
  });

  function get_operations(){
    $.ajax({
      url: "/operations",
      type:"GET",
      datatype: "json",
      async: false,
      success: function(data){
        opt = '<option value="">Select Operation</option>';
         $.each(data, function(i, v){
           opt += '<option value="' + v.operation_id + '">' + v.operation_name + '</option>';
         });
      }
    });

    $('#manual-production-modal select[name="operation_id"]').append(opt);
  }

  $(document).on('change', '#manual-production-modal select[name="operation_id"]', function(e){
    $('#operations-table-tbody').empty();
  });

  $(document).on('click', '#operations-table-tbody select[name="workstation_id[]"]', function(e){
    e.preventDefault();

    var $row = $(this).closest('tr');

    var workstation_id = $(this).val();

    $row.find('select[name="process_id[]"]').eq(0).empty();

    var opt_1 = '<option value="">Select Process</option>';

    if(workstation_id){
      $.ajax({
        url: "/processes/" + workstation_id,
        type:"GET",
        datatype: "json",
        async: false,
        success: function(data){
          $.each(data, function(i, v){
            opt_1 += '<option value="' + v.process_id + '">' + v.process_name + '</option>';
          });
        }
      });
    }

    $row.find('select[name="process_id[]"]').eq(0).append(opt_1);
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

  $('#sel-mreq').autocomplete({
    source:function(request,response){
      $.ajax({
        url: '/get_reference_list/Material Request',
        dataType: "json",
        data: {
            term : request.term
        },
        success: function(data) {
          response(data);
        }
    });
    },
    minLength: 1,
    select:function(event,ui){
      get_reference_details('MREQ', ui.item.value);
    }
  });

  $('#sel-so').autocomplete({
      source:function(request,response){
        $.ajax({
          url: '/get_reference_list/Sales Order',
          dataType: "json",
          data: {
              term : request.term
          },
          success: function(data) {
            response(data);
          }
      });
      },
      minLength: 1,
      select:function(event,ui){
        get_reference_details('SO', ui.item.value);
      }
  });

  $('#manual-prod-note').hide();
  $('#sel-item').autocomplete({
    source:function(request,response){
      $.ajax({
        url: '/get_reference_list/Item',
        dataType: "json",
        data: {
            term : request.term
        },
        success: function(data) {
          response(data);
        }
    });
    },
    minLength: 1,
    select:function(event,ui){
      var is_stock_item = ui.item.is_stock_item;
      $('#manual-material-operation-row').hide();
      $.ajax({
        url:"/get_item_details/" + ui.item.value,
        type:"GET",
        success:function(response){
          if (response.success == 0) {
            showNotification("danger", response.message, "now-ui-icons travel_info");
          }else{
            $('#manual-production-modal textarea[name="description"]').text(response.description);
            $('#manual-production-modal input[name="stock_uom"]').val(response.stock_uom);
            $('#manual-production-modal input[name="item_classification"]').val(response.item_classification);
            $('#manual-production-modal input[name="is_stock_item"]').val(is_stock_item);
            $('#target-wh').append(get_warehouse(response.item_classification, 'target'));
          }

          get_bom(response.name);

          if(is_stock_item == 0){
            $('#has-no-bom-checkbox').prop( "checked", true );
            $('#sel-bom').attr('disabled', true);
            $('#manual-prod-note').show();
            $('#manual-material-operation-row').show();
          }else{
            $('#has-no-bom-checkbox').prop( "checked", false );
            $('#sel-bom').removeAttr('disabled');
            $('#manual-prod-note').hide();
            $('#manual-material-operation-row').hide();
          }

        }
      });
    }
  });

  $('#sel-parent-code').autocomplete({
    source:function(request,response){
      var ref_type = $('#sel-reference-type').val();
      var ref_no = (ref_type == 'SO') ? $('#sel-so').val() : $('#sel-mreq').val();
      $.ajax({
        url: "/get_parent_code/" + ref_type + "/" + ref_no,
        dataType: "json",
        data: {
            term : request.term
        },
        success: function(data) {
          response(data);
        }
    });
    },
    minLength: 1,
  });

  $('#sel-sub-parent-code').autocomplete({
    source:function(request,response){
      $.ajax({
        url:"/get_sub_parent_code/" + $('#sel-parent-code').val(),
        dataType: "json",
        data: {
            term : request.term
        },
        success: function(data) {
          response(data);
        }
    });
    },
    minLength: 1,
  });

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $('#reload-list').click(function(e){
    e.preventDefault();
    load_list();
  });

  function load_list(){
    var status = $('#current-status').val() ? $('#current-status').val() : 'All';

    item_tracking(0);
    get_production_order_list(status, '#production-orders-div');

    item_tracking(1);
    get_production_order_list(status, '#production-orders-div', 1, 1, $('.search-filter').val());
  }

  load_list();
  const status_array = [];
  var status = '';

  $(document).on('keyup', '.search-filter', function(){
    var status = $('#current-status').val();
    var div = $(this).data('div');

    get_production_order_list(status ? status : 'All', div, 0, 1, $(this).val());
  });

  $(".production-orders-checkbox").click(function(){
    if($(this).prop('checked') == true){
      status += $(this).val() + ',';
    }else if($(this).prop('checked') == false){
      status = status.replace($(this).val() + ',', '');
    }

    if(status == ''){
      $('#current-status').val('All');
    }else{
      $('#current-status').val(status);
    }

    query = $('.search-filter').val();

    get_production_order_list(status ? status : 'All', '#production-orders-div', 0, 1, query);
  });
  
  function get_production_order_list(status, div, get_total, page, query){
    $('#loader-wrapper').removeAttr('hidden');

    status = status ? status : 'All';
    $.ajax({
      url: "/production_order_list/" + status + "?page=" + page,
      type:"GET",
      data: {search_string: query, get_total: get_total},
      success:function(data){
        $('#loader-wrapper').attr('hidden', true);
        $(div).html(data);
      }
    });
  }

  $(document).on('click', '.custom-production-pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    var query = $('#production-orders-search').val();
    var status = $('#current-status').val() ? $('#current-status').val() : 'All';
    get_production_order_list(status, '#production-orders-div', 0, page, query);
  });

  $(document).on('click', '.print-transfer-slip-btn', function(e){
    e.preventDefault();

    $.ajax({
       url: "/print_fg_transfer_slip/" + $(this).data('production-order'),
       type:"GET",
       success:function(data){
         // window.open(this.url);
          $("#iframe-print").attr("src", this.url);
       },
       error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
       },
    });
  });

  function get_warehouse(item_classification, type){
    var row = '';
    $.ajax({
       url: "/get_warehouses/Fabrication/" + item_classification,
       type:"GET",
       datatype: "json",
       async: false,
       success: function(data){
          $.each(data, function(i, v){
            row += '<option value="' + v.warehouse + '">' + v.warehouse + '</option>';
          });
       }
    });

    return row;
  }

  $('.date-picker').datepicker({
    'format': 'yyyy-mm-dd',
    'autoclose': true
  });

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

  $('#reschedule_delivery_frm').submit(function(e){
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
            $('#reschedule-delivery-modal').modal('hide');
            load_list();
          }
        }
      });
    });
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

// Manual Create Production Order
  $('#manual-production-create-btn').click(function(e){
    e.preventDefault();
    $('#operations-table-tbody').empty();
    $('#material-table-tbody').empty();
    $('#manual-material-operation-row').hide();
    $('#manual-production-modal').modal('show');
  });

  $('#sel-reference-type').change(function(){
    set_attr_for_reference_no($(this).val());
  });

  set_attr_for_reference_no($('#sel-reference-type').val());
  function set_attr_for_reference_no(reference_type){
    if (reference_type == 'SO') {
      $('#input-so-div').removeAttr('hidden');
      $('#input-mreq-div').attr('hidden', true);
      $('#manual-production-modal .sel-so').attr('required', true);
      $('#manual-production-modal .sel-mreq').removeAttr('required');
      $('#manual-production-modal .sel-mreq').val('');
    }else{
      $('#input-mreq-div').removeAttr('hidden');
      $('#input-so-div').attr('hidden', true);
      $('#manual-production-modal .sel-mreq').attr('required', true);
      $('#manual-production-modal .sel-so').removeAttr('required');
      $('#manual-production-modal .sel-so').val('');
    }
  }

  function get_bom(item_code){
    $('#sel-bom').empty();
    $.ajax({
      url:"/get_item_bom/" + item_code,
      type:"GET",
      success:function(response){
        if (response.length > 0) {
          var opt = '';
          $.each(response, function(i, d){
            opt += '<option value="' + d.name + '" data-reviewed="' + d.is_reviewed + '">' + d.name + '</option>';
          });

          $('#sel-bom').append(opt);
        }else{
          showNotification("danger", 'No BOM found for Item ' + item_code, "now-ui-icons travel_info");
        }

        get_bom_status($('#sel-bom').find(':selected').data('reviewed'));
      }
    });
  }

  $('#sel-bom').change(function(){
    get_bom_status($(this).find(':selected').data('reviewed'));
  });

  function get_bom_status(reviewed){
    if (reviewed == 0) {
      showNotification("info", 'Please review and update BOM', "now-ui-icons travel_info");
    }else{
      $('#manual-production-modal input[name="is_reviewed"]').val(1);
    }
  }

  function get_reference_details(ref_type, ref_no){
    $.ajax({
      url:"/get_reference_details/" + ref_type + "/" + ref_no,
      type:"GET",
      success:function(response){
        if (response.success == 0) {
          showNotification("danger", 'No BOM found for Item ' + item_code, "now-ui-icons travel_info");
        }else{
          $('#manual-production-modal input[name="customer"]').val(response.customer);
          $('#manual-production-modal input[name="project"]').val(response.project);
          $('#manual-production-modal input[name="delivery_date"]').val(response.delivery_date);
          var classification = (response.purpose) ? response.purpose : response.sales_type;
          classification = (classification != 'Sample') ? 'Customer Order' : 'Sample';
          $('#manual-production-modal input[name="classification"]').val(classification);
        }
      }
    });
  }

  $('.readonly').each(function(){
    $(this).attr('readonly','readonly');
  });

  $('#manual-production-modal form').submit(function(e){
    e.preventDefault();
    var qty = $('#manual-production-modal input[name="qty"]').val();
    if (qty <= 0) {
      showNotification("danger", 'Qty cannot be less than or equal to 0.', "now-ui-icons travel_info");
      return false;
    }else{
      $.ajax({
        url: $(this).attr('action'),
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success == 0) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", 'Production Order <b>' + data.message + '</b> has been created.', "now-ui-icons ui-1_check");
            $('#manual-production-modal').modal('hide');
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

  $('#view-bom-btn').click(function(e){
    e.preventDefault();
    $('#production-order-val').val('');
    var bom = $('#sel-bom').val();
    $.ajax({
      url: "/view_bom_for_review/" + bom,
      type:"GET",
      success:function(data){
        $('#review-bom-details-div').html(data);
      }
    });

    $('#review-bom-modal .modal-title').html('Review & Finalize BOM [' + bom + ']');
    $('#review-bom-modal').modal('show');
  });

  $(document).on('change', '#sel-workstation', function(){
    var workstation = $(this).val();
    $('#sel-process').empty();
    if (workstation) {
      $.ajax({
        url: '/get_workstation_process/' + workstation,
        type:"GET",
        success:function(data){
          if (data.length > 0) {
            var opt = '<option value="">Select Process</option>';
            $.each(data, function(i, v){
              opt += '<option value="' + v.process_id + '">' + v.process_name + '</option>';
            });

            $('#sel-process').append(opt);
          }
        }
      });
    }
  });

  $(document).on("click", ".delete-row", function(e){
    e.preventDefault();
    $(this).parents("tr").remove();

    $('#bom-workstations-tbl tbody tr').each(function (idx) {
      $(this).children("td:eq(0)").html(idx + 1);
    });
  });

  $('.modal').on('hidden.bs.modal', function(){
    var frm = $(this).find('form')[0];
    if (frm) frm.reset();
  });

  $(document).on('show.bs.modal', '.modal', function (event) {
    var zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
      $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
  });
});
</script>

<script type="text/javascript">

  function item_tracking(get_total, page, query){
    $.ajax({
          url:"/get_item_status_tracking/?page="+page,
          type:"GET",
          data: {search_string: query, get_total: get_total},
          success:function(data){
            if(get_total){
              $(data.div).text(data.total);
            }else{
              $('#item-tracking-div').html(data);
            }
            
          }
        }); 
  }
</script>
<script>
$('#btnPrint').on("click", function () {
      $('#printmodalbody').printThis({
      });
    });
</script>
<script>
$(document).on('click', '.printtransfer', function(){
  var tryval = $('#prod_list_print').val();

  if(tryval == ''){
    showNotification("danger", "No selected Production Order", "now-ui-icons travel_info");
  }else{
    $.ajax({
       url: "/selected_print_fg_transfer_slip/" + tryval,
       type:"GET",
       success:function(data){
          $('#printmodalbody').html(data);
          $('#print_modal_js_ws').modal('show');

       },
       error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
       },
    });
  }
})
$(document).on('click', '.feedbacked_log_btn', function(){
  var prod = $(this).data('production-order');
  $.ajax({
       url: "/get_feedback_logs/" + prod,
       type:"GET",
       success:function(data){
          $('#tbl_feedbacked_logs').html(data);
          $('#feedbacked-log-modal').modal('show');
          // $('#reschedule-delivery-modal .modal-title').text('Reschedule Delivery Date'+"["+ prod +"]");


       },
       error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
       },
    });
});
$(document).on('click', '.prod-reset-btn', function(){
  var prod = $(this).data('production-order');
    $.ajax({
       url: "/tbl_reset_workstation/" + prod,
       type:"GET",
       success:function(data){
          $('#tbl_workstation_reset').html(data);
          $('#reset-workstation-modal').modal('show');
       },
       error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
       },
    });
});
$(document).on('click', '.btn_reset_workstation', function(){
  var jt_id = $(this).data('id');
  var prod = $(this).data('prod');
  var work = $(this).data('workstation');
  var process = $(this).data('process');
  $('#prod-list-confirm-reset-workstation-modal .reset_job_ticket_workstation').text(work+"-"+ process);
  $('#prod-list-confirm-reset-workstation-modal .reset_job_ticket_id').val(jt_id);
  $('#prod-list-confirm-reset-workstation-modal .reset_prod').val(prod);
  $('#prod-list-confirm-reset-workstation-modal').modal('show');
  $('#prod-list-confirm-reset-workstation-modal .reset_reload_tbl').val("prod_list");
});



</script>
@endsection