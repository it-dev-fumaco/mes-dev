@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'settings_module',
    'pageHeader' => 'User Settings',
   'pageSpan' => Auth::user()->employee_name
])

@section('content')
@include('modals.add_email_trans')
<div class="panel-header"></div>
<div class="row p-2" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 1000px;">
  <div class="col-12 p-0 bg-white" style="min-height: 1000px;">
    <ul class="nav mr-2 ml-2 mt-3 mb-3" data-tabs="tabs">
      @canany(['manage-users'])
        <li class="nav-item mr-2" style="border: 1px solid #f96332 !important;">
          <a class="nav-link active" href="#user_list" data-toggle="tab">User List</a>
        </li>
      @endcanany
      @canany(['manage-user-groups'])
        @php
          $active = 'active';
        @endphp
        @canany(['manage-users'])
          @php
            $active = null;
          @endphp
        @endcanany
        <li class="nav-item mr-2" style="border: 1px solid #f96332 !important;">
          <a class="nav-link {{ $active }}" href="#user_group_list" data-toggle="tab">User Group</a>
        </li>
      @endcanany
      @canany(['manage-email-notifications'])
        @php
          $active = 'active';
        @endphp
        @canany(['manage-users', 'manage-user-groups'])
          @php
            $active = null;
          @endphp
        @endcanany
        <li class="nav-item mr-2" style="border: 1px solid #f96332 !important;">
          <a class="nav-link {{ $active }}" href="#email_alert_setup" data-toggle="tab">Email Notification Setup</a>
        </li>
      @endcanany
      @canany(['manage-role-permissions'])
        @php
          $active = 'active';
        @endphp
        @canany(['manage-users', 'manage-user-groups', 'manage-email-notifications'])
          @php
            $active = null;
          @endphp
        @endcanany
        <li class="nav-item mr-2" style="border: 1px solid #f96332 !important;">
          <a class="nav-link {{ $active }}" href="#role-permissions" data-toggle="tab">Role Permissions</a>
        </li>
      @endcanany
    </ul>
    <div class="tab-content text-center p-0 m-0">
      @canany(['manage-users'])
        <div class="tab-pane active" id="user_list">
          <div class="card" style="min-height: 720px;">
            <div class="card-header p-0 m-0 rounded-0" style="background-color: #0277BD;">
              <div class="d-flex align-items-center pt-0 pb-0 pl-2 pr-2">
                <div class="mr-auto p-2">
                  <div class="text-white font-weight-bold text-left m-0 text-uppercase" style="font-size: 16px;">User List</div>
                </div>
                <div class="p-2 col-4">
                  <input type="text" class="form-control rounded bg-white p-2 w-100 m-0" placeholder="Search User" id="search_user_setup">
                </div>
                <div class="p-2">
                  <button type="button" class="btn btn-primary m-0" id="add-user-btn" data-id="1" style="font-size: 9pt;">
                    <i class="now-ui-icons ui-1_simple-add"></i> Add
                  </button>
                </div>
              </div>
            </div>
            <div class="card-body p-0">
              <div id="div-user-table" class="m-3"></div>
            </div>
          </div>
        </div>
      @endcanany
      
      @canany(['manage-user-groups'])
        @php
          $active = 'active';
        @endphp
        @canany(['manage-users'])
          @php
            $active = null;
          @endphp
        @endcanany
        <div class="tab-pane {{ $active }}" id="user_group_list">
          <div class="row p-0 m-0">
            <div class="col-md-12 p-0">
              <div class="card" style="min-height: 720px;">
                <div class="card-header p-0 m-0 rounded-0" style="background-color: #0277BD;">
                  <div class="d-flex align-items-center pt-0 pb-0 pl-2 pr-2">
                    <div class="mr-auto p-2">
                      <div class="text-white font-weight-bold text-left m-0 text-uppercase" style="font-size: 16px;">User Group</div>
                    </div>
                    <div class="p-2 col-4">
                      <input type="text" class="form-control rounded bg-white p-2 w-100 m-0" placeholder="Search User Group" id="search_user_group_setup">
                    </div>
                    <div class="p-2">
                      <button type="button" class="btn btn-primary m-0" id="add-user-group" style="font-size: 9pt;">
                        <i class="now-ui-icons ui-1_simple-add"></i> Add
                      </button>
                    </div>
                  </div>
                </div>
                <div class="card-body p-0 m-0">
                  <div class="tbl_user_group m-3" id="tbl_user_group"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endcanany

      @canany(['manage-email-notifications'])
        @php
          $active = 'active';
        @endphp
        @canany(['manage-users', 'manage-user-groups'])
          @php
            $active = null;
          @endphp
        @endcanany
        <div class="tab-pane {{ $active }}" id="email_alert_setup">
          <div class="row p-0 m-0">
            <div class="col-md-12 p-0">
              <div class="card" style="min-height: 720px;">
                <div class="card-header p-0 m-0 rounded-0" style="background-color: #0277BD;">
                  <div class="d-flex align-items-center pt-0 pb-0 pl-2 pr-2">
                    <div class="mr-auto p-2">
                      <div class="text-white font-weight-bold text-left m-0 text-uppercase" style="font-size: 16px;">Email Alert Recipient</div>
                    </div>
                    <div class="p-2 col-4">
                      <input type="text" class="form-control rounded bg-white p-2 w-100 m-0" placeholder="Search Recipient" id="search_email_setup">
                    </div>
                    <div class="p-2">
                      <button type="button" class="btn btn-primary m-0" id="email_trans_btn" style="font-size: 9pt;">
                        <i class="now-ui-icons ui-1_simple-add"></i> Add
                      </button>
                    </div>
                  </div>
                </div>
                <div class="card-body p-0">
                  <div class="tbl_email_trans m-3" id="tbl_email_trans"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endcanany

      @canany(['manage-role-permissions'])
        @php
          $active = 'active';
        @endphp
        @canany(['manage-users', 'manage-user-groups', 'manage-email-notifications'])
          @php
            $active = null;
          @endphp
        @endcanany
        <div class="tab-pane {{ $active }}" id="role-permissions">
          <div class="row p-0 m-0">
            <div class="col-md-12 p-0">
              <div class="card" style="min-height: 720px;">
                <div class="card-header p-0 m-0 rounded-0" style="background-color: #0277BD;">
                  <div class="d-flex align-items-center pt-0 pb-0 pl-2 pr-2">
                    <div class="mr-auto p-2">
                      <div class="text-white font-weight-bold text-left m-0 text-uppercase" style="font-size: 16px;">Role Permissions</div>
                    </div>
                    <div class="p-2">
                      <button class="btn btn-primary m-0" type="button" id="update-permissions-btn">Update Permissions</button>
                    </div>
                  </div>
                </div>
                <div class="card-body p-0">
                  <div class="row m-0 p-0">
                    <div class="col-12 mb-2 mt-2">
                      <div class="d-flex flex-row align-items-center">
                        <div class="font-weight-bold mr-3">Select Module</div>
                        <select id="role-permission-module" class="form-control col-2 rounded-0">
                          <option value="">Select Module</option>
                          <option value="Production">Production</option>
                          <option value="Quality Assurance">Quality Assurance</option>
                          <option value="Maintenance">Maintenance</option>
                        </select>
                        <div class="font-weight-bold mr-3 ml-4">Select User Role</div>
                        <select name="user_role" id="user-role-select" class="form-control col-3 rounded-0">
                          <option value="">Select Module first</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div id="role-permissions-div"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endcanany
    </div>
  </div>
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
                       <select class="form-control sel2" name="user_access_id" id="sel-user-id-add" required>
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
                       <select class="form-control sel2" name="user_group" id="user_group" required>
                          <option value="">Select Module</option>
                          @foreach($module as $row)
                          <option value="{{ $row }}">{{ $row }}</option>
                          @endforeach
                       </select>
                    </div>
                    <div class="form-group">
                       <label>User Role:</label>
                       <select class="form-control sel2" name="user_role" id="user_role"> required
                       </select>
                    </div>
                    <div class="form-group">
                       <label>Operation:</label>
                       <select class="form-control sel2" name="operation" required>
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
                       <select class="form-control sel3" name="user_access_id" id="edit_user_id" required>
                          <option value="">Select Employee</option>
                          @foreach($employees as $row)
                          <option value="{{ $row->user_id }}" data-empname="{{ $row->employee_name }}">{{ $row->user_id }} - {{ $row->employee_name }}</option>
                          @endforeach
                       </select>
                    </div>
                    <div class="form-group">
                       <label>Employee Name:</label>
                       <input type="text" class="form-control" name="employee_name" required id="edit_employee_name" required>
                    </div>
                    <div class="form-group">
                       <label>Module:</label>
                       <select class="form-control sel3" name="user_group" id="edit_user_group" required>
                          <option value="">Select Module</option>
                          @foreach($module as $row)
                          <option value="{{ $row }}">{{ $row }}</option>
                          @endforeach
                       </select>
                    </div>
                    <div class="form-group">
                       <label>User Role:</label>
                       <select class="form-control sel3" name="user_role" id="edit_user_role" required>
                       </select>
                    </div>
                    <div class="form-group">
                       <label>Operation:</label>
                       <select class="form-control sel3" name="operation" id="edit_user_operation" required>
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
              <button type="submit" class="btn btn-primary">Confirm</button>
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
                       <select class="form-control sel11" name="add_user_group" id="add_user_group" required>
                          <option value="">Select Module</option>
                          @foreach($module as $row)
                          <option value="{{ $row }}">{{ $row }}</option>
                          @endforeach
                       </select>
                    </div>
                    <div class="form-group">
                       <label>User Role:</label>
                       {{-- <select class="form-control sel11" name="add_user_role" id="add_user_role" required>
                          <option value="">Select User Role</option>
                          <option value="Production Supervisor">Production Supervisor</option>
                          <option value="Production Manager">Production Manager</option>
                          <option value="QA Manager">QA Manager</option>
                          <option value="QA Inspector">QA Inspector</option>
                          <option value="Maintenance Manager">Maintenance Manager</option>
                          <option value="Maintenance Staff">Maintenance Staff</option>
                       </select> --}}
                       <input type="text" class="form-control" name="add_user_role" required>
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
                          <option value="{{ $row }}">{{ $row }}</option>
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


<div class="modal fade" id="delete-email-trans-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="width: 30%;">
     <form action="/delete_email_recipient" method="POST" id="delete-email-trans-frm">
        @csrf
        <div class="modal-content">
           <div class="modal-header text-white" style="background-color: #0277BD; padding: 5px 8px;">
              <h5 class="modal-title" id="modal-title">
                 <span>Delete Email Recipient</span>
                 <span class="operation-text" style="font-weight: bolder;"></span>
              </h5>
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
	.select-input{
		height: 33px;
		font-size: 12px;
	}
	#add-email-trans-modal .form-control{
		border: 1px solid #ccc;
		border-radius: 3px;
		box-shadow: none;
		margin-bottom: 15px; 
	}
</style>

@endsection

@section('script')
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />

<script>
  $(document).ready(function(){
    $('#update-permissions-btn').attr('disabled', true);
    $(document).on('change', '#role-permission-module', function (e) {
      e.preventDefault();
      $.ajax({
        url:"/get_user_role_by_module/" + $(this).val(),
        type:"GET",
        success:function(data){
          $("#user-role-select").html(data);
          $('#role-permissions-div').empty();
          $('#update-permissions-btn').attr('disabled', true);
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR, textStatus, errorThrown);
        }
      });
    });
    $(document).on('change', '#user-role-select', function (e) {
      e.preventDefault();
      $('#update-permissions-btn').removeAttr('disabled');
      view_role_permissions_form($(this).val());
    });
    function view_role_permissions_form(query){
      $.ajax({
        url:"/view_role_permissions_form/" + query,
        type:"GET",
        success:function(data){
          $('#role-permissions-div').html(data);
        }
      });  
    }
    $('#update-permissions-btn').click(function (e) {
      e.preventDefault();
      $('#role-permission-form').submit();
    });
    $(document).on('submit', '#role-permission-form', function(e) {
      e.preventDefault();
      $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.status) {
            showNotification("success", data.message, "now-ui-icons ui-1_check");
          } else {
            showNotification("warning", data.message, "now-ui-icons travel_info");
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR, textStatus, errorThrown);
          showNotification("danger", 'Server Error. Please contact your system administrator.', "now-ui-icons travel_info");
        }
      }); 
    });
    $(document).on('click', '.check-all-permission', function (e){
      e.preventDefault();
      $("." + $(this).data('type')).prop('checked', true);
    });
    $(document).on('click', '.uncheck-all-permission', function (e){
      e.preventDefault();
      $("." + $(this).data('type')).prop('checked', false);
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
   
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 

    
  get_users();

get_user_group();
tbl_email_trans();




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
      var form = $('#add-user-frm');
      var reportValidity = form[0].reportValidity();

      if(reportValidity){
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
      }
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
      var form = $('#edit-user-frm');
      var reportValidity = form[0].reportValidity();

      if(reportValidity){
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
      }
    });

    $(document).on('click', '.modal-control', function (e){
      e.preventDefault();
      modal_control($(this).data('modal'), $(this).data('action'));
    });

    function modal_control(modal, action){
      $(modal).modal(action);
    }

    $(document).on('click', '.remove-user-access-btn', function (e){
      var id = $(this).data('user');
      var modal = $(this).data('name');
      $.ajax({
        url: '/remove_user_access/' + id,
        type: "get",
        success:function(data){
          $('.modal').modal('hide');
          get_users();
          showNotification("success", data.message, "now-ui-icons ui-1_check");
        }
      });
    });

    $(document).on('click', '.delete-user-btn', function(e){
      e.preventDefault();
      $('#delete-user-frm input[name="user_id"]').val($(this).data('user'));
      $('#delete-user-frm span').text($(this).data('name'));
      $('#delete-user-modal').modal('show');
    });

     $('#delete-user-frm').submit(function(e){
      e.preventDefault();
      $.ajax({
        url: $(this).attr('action'),
        type:"post",
        data: $(this).serialize(),
        success:function(data){
          get_users();
          $('.modal').modal('hide');
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

    $(document).on('keyup', '#search_user_setup', function(){
    var query = $(this).val();
    get_users(1, query);
  });

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

  $(document).on('change', '#user_group', function (){
    change_userrole_add();
  });

  function change_userrole_add(){
    var modules = $("#user_group").val();
    if (modules != "") {
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

  $(document).on('change', '#edit_user_group', function (){
    change_userrole_edit();
  });

 function change_userrole_edit(){
    var modules = $("#edit_user_group").val();
    // alert(modules);
    if (modules != "") {
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

 $(document).on('keyup', '#search_user_group_setup', function(){
    var query = $(this).val();
    get_user_group(1, query);
  });

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

    $('#add-user-group-frm').submit(function(e){
      e.preventDefault();
      var form = $('#add-user-group-frm');
      var reportValidity = form[0].reportValidity();

      if(reportValidity){
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
      }
    });

    $('#edit-user-group-frm').submit(function(e){
      e.preventDefault();
      var form = $('#edit-user-group-frm');
      var reportValidity = form[0].reportValidity();

      if(reportValidity){
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
      }
    });

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

   
  });
</script>
@endsection