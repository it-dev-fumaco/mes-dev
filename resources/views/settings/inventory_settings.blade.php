@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'settings_module',
    'pageHeader' => 'Inventory Settings',
    'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header"></div>
<div class="row p-2" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
	<div class="col-12 p-0 bg-white">
		<ul class="nav workstation_navbar mr-2 ml-2 mt-3 mb-3" id="myTab" role="tablist" style="font-size: 10pt;">
			<li class="nav-item mr-2" style="border: 1px solid #f96332 !important;">
				<a class="nav-link active" href="#item_warehouse_setup" data-toggle="tab">Item Warehouse Setup</a>
			</li>
			<li class="nav-item mr-2" style="border: 1px solid #f96332 !important;">
				<a class="nav-link" href="#wip-setup-tab" data-toggle="tab">Work in Progress Warehouse Setup</a>
			</li>
			<li class="nav-item mr-2" style="border: 1px solid #f96332 !important;">
				<a class="nav-link" href="#fast-issuance-setup" data-toggle="tab">Fast Issuance Setup</a>
			</li>
			<li class="nav-item mr-2" style="border: 1px solid #f96332 !important;">
				<a class="nav-link" href="#uom-conversion-tab" data-toggle="tab">UoM Conversion</a>
			</li>
		</ul>
		<div class="tab-content text-center p-0 m-0">    
			<div class="tab-pane active" id="item_warehouse_setup">
				<div class="card">
					<div class="card-header p-0 m-0 rounded-0" style="background-color: #0277BD;">
						<div class="d-flex align-items-center pt-0 pb-0 pl-2 pr-2">
							<div class="mr-auto p-2">
								<div class="text-white font-weight-bold text-left m-0 text-uppercase" style="font-size: 16px;">Item Warehouse Setup</div>
							</div>
							<div class="p-2 col-4">
								<input type="text" class="form-control rounded bg-white p-2 w-100 m-0" placeholder="Search" id="search_item_warehouse_setup">
							</div>
							<div class="p-2">
								<button type="button" class="btn btn-primary m-0" id="add-item-warehouse-button" style="font-size: 9pt;">
									<i class="now-ui-icons ui-1_simple-add"></i> Add
								</button>
							</div>
						</div>
					</div>
					<div class="card-body p-0">
						<div class="row p-0 m-0 bg-white" style="min-height: 500px;">
							<div class="col-md-12">
								<div class="nav-tabs-navigation mt-2">
									<div class="nav-tabs-wrapper">
										<ul class="nav nav-tabs1 mb-2" data-tabs="tabs" style="font-size: 11px;">
											<li class="nav-item mr-2 rounded" style="border: 1px solid #f96332 !important;">
												<a class="nav-link active add_icw_operation pt-2 pb-2 pl-3 pr-3" href="#icw_fabrication" data-toggle="tab" data-operation="Fabrication" data-values="fab">Fabrication</a>
											</li>
											<li class="nav-item mr-2 rounded" style="border: 1px solid #f96332 !important;">
												<a class="nav-link add_icw_operation pt-2 pb-2 pl-3 pr-3" href="#icw_painting" data-toggle="tab" data-operation="Painting" data-values="pain">Painting</a>
											</li>
											<li class="nav-item mr-2 rounded" style="border: 1px solid #f96332 !important;">
												<a class="nav-link add_icw_operation pt-2 pb-2 pl-3 pr-3" href="#icw_assembly" data-toggle="tab" data-operation="Assembly" data-values="assem">Assembly</a>
											</li>
										</ul>
									</div>
								</div>
								<div class="tab-content text-center">
									<div class="tab-pane active" id="icw_fabrication">
										<div class="row p-0 m-0 w-100">
											<div class="col-md-12 p-0 m-0">
												<div id="tbl_item_warehouse_list_fabrication"></div>
											</div>
										</div>
									</div>
									<div class="tab-pane" id="icw_painting">
										<div class="row p-0 m-0 w-100">
											<div class="col-md-12 p-0 m-0">
												<div id="tbl_item_warehouse_list_painting"></div>
											</div>
										</div>
									</div>
									<div class="tab-pane" id="icw_assembly">
										<div class="row p-0 m-0 w-100">
											<div class="col-md-12 p-0 m-0">
												<div id="tbl_item_warehouse_list_assembly"></div>
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
				<div class="card">
					<div class="card-header p-0 m-0" style="background-color: #0277BD;">
						<div class="d-flex align-items-center pt-0 pb-0 pl-2 pr-2">
							<div class="mr-auto p-2">
								<div class="text-white font-weight-bold text-left m-0 text-uppercase" style="font-size: 16px;">UOM Conversion</div>
							</div>
							<div class="p-2">
								<button type="button" class="btn btn-primary m-0" id="add-uom-conversion-btn" style="font-size: 9pt;">
									<i class="now-ui-icons ui-1_simple-add"></i> Add
								</button>
							</div>
						</div>
					</div>
					<div class="card-body p-0">
						<div class="row p-0 m-0 bg-white" style="min-height: 500px;">
							<div class="col-md-12">
								<div id="uom-conversion-tbl" class="mt-3"></div>
							</div>
						</div>        
					</div>
				</div>
			</div>
			<div class="tab-pane" id="fast-issuance-setup">
				<div class="card">
					<div class="card-header p-0 m-0 rounded-0" style="background-color: #0277BD;">
						<div class="d-flex align-items-center pt-0 pb-0 pl-2 pr-2">
							<div class="mr-auto p-2">
								<div class="text-white font-weight-bold text-left m-0 text-uppercase" style="font-size: 16px;">Fast Issuance Setup</div>
							</div>
							<div class="p-2 col-4">
								<input type="text" class="form-control rounded bg-white p-2 w-100 m-0" placeholder="Search" id="search-fast-issuance-setup">
							</div>
						</div>
					</div>
					<div class="card-body p-0">
						<div class="row p-0 m-0 bg-white" style="min-height: 500px;">
							<div class="col-md-12">
								<div class="nav-tabs-navigation mt-2 mb-2">
									<div class="nav-tabs-wrapper">
										<ul class="nav" data-tabs="tabs2" style="font-size: 11px;">
											<li class="nav-item rounded mr-2" style="border: 1px solid #f96332 !important;">
												<a class="nav-link add_icw_operation active" href="#fast_issuance_warehouse" data-toggle="tab" data-operation="fiw" data-values="fiw">Allowed Warehouses for Fast Issuance</a>
											</li>
											<li class="nav-item rounded mr-2" style="border: 1px solid #f96332 !important;">
												<a class="nav-link add_icw_operation" href="#fast_issuance_user" data-toggle="tab" data-operation="fiu" data-values="fiu">Allowed Users for Fast Issuance</a>
											</li>
										</ul>
									</div>
								</div>
								<div class="tab-content text-center">
									<div class="tab-pane" id="fast_issuance_user">
										<div class="row p-0 m-0 w-100">
											<div class="col-md-12 p-0 m-0">
												<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#add-allowed-fast-issuance-user" style="margin: -40px 5px 5px 5px;">
													<i class="now-ui-icons ui-1_simple-add"></i> Add User
												</button>
												<div id="tbl-fast-issuance-user"></div>
											</div>
										</div>
									</div>
									<div class="tab-pane active" id="fast_issuance_warehouse">
										<div class="row p-0 m-0 w-100">
											<div class="col-md-12 p-0 m-0">
												<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#add-allowed-fast-issuance-warehouse" style="margin: -40px 5px 5px 5px;">
													<i class="now-ui-icons ui-1_simple-add"></i> Add Warehouse
												</button>
												<div id="tbl-fast-issuance-warehouse"></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="wip-setup-tab">
				<div class="card">
					<div class="card-header p-0 m-0 rounded-0" style="background-color: #0277BD;">
						<div class="d-flex align-items-center pt-0 pb-0 pl-2 pr-2">
							<div class="mr-auto p-2">
								<div class="text-white font-weight-bold text-left m-0 text-uppercase" style="font-size: 16px;">Work-in-Progress Warehouse Setup</div>
							</div>
							<div class="p-2 col-4">
								<input type="text" class="form-control rounded bg-white p-2 w-100 m-0" placeholder="Search" id="search-wip-setup">
							</div>
							<div class="p-2">
								<button type="button" class="btn btn-primary m-0" id="add-wip-button" style="font-size: 9pt;">
									<i class="now-ui-icons ui-1_simple-add"></i> Add
								</button>
							</div>
						</div>
					</div>
					<div class="card-body p-0">
						<div class="row p-0 m-0 bg-white" style="min-height: 500px;">
							<div class="col-md-12">
								<div id="tbl_wip_list" class="mt-3"></div>
							</div>
						</div>        
					</div>
				</div>
			</div>
		</div>
	</div>
   <div class="col-10 p-2" style="min-height: 1000px;">
		
	</div>
</div>

<div class="modal fade bd-example-modal-lg" id="add-item-warehouse-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<form action="/save_item_classification_warehouse" method="POST" id="save-item-classification-warehouse-frm">
			@csrf
			<div class="modal-content">
				<div class="modal-header text-white" style="background-color: #0277BD;">
					<h5 class="modal-title"><i class="now-ui-icons"></i> Add Item Warehouse</h5>
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
							<a href="#" class="btn btn-primary add-row"><i class="now-ui-icons ui-1_simple-add"></i> Add</a>
							<table class="table" id="icw-table" style="font-size: 10px;">
								<thead>
									<tr>
										<th style="width: 5%; text-align: center;font-weight: bold;">No.</th>
										<th style="width: 22.5%; text-align: center;font-weight: bold;">Item Classification</th>
										<th style="width: 22.5%; text-align: center;font-weight: bold;">Warehouse</th>
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
					<h5 class="modal-title"><i class="now-ui-icons"></i> Edit Item Warehouse</h5>
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
					<h5 class="modal-title">
						<span>Delete Item Warehouse</span>
						<span class="sampling-delete-text font-weight-bolder"></span>
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label style="padding-left: 10px; display:inline;">Delete Item Classification Warehouse under </label><span id="icw_itemclass_label" class="font-weight-bold"></span>?
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
					<h5 class="modal-title"><i class="now-ui-icons"></i> Add Work In Progress</h5>
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
					<h5 class="modal-title"><i class="now-ui-icons"></i> Edit Work In Progress</h5>
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
					<h5 class="modal-title">
						<span>Delete Work In Progress</span>
						<span class="sampling-delete-text font-weight-bold"></span>
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label style="padding-left: 10px;display:inline;">Delete Work in Progress under </label><span id="wip_label" class="font-weight-bold"></span>?
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
<div class="modal fade" id="add-allowed-fast-issuance-warehouse" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<form action="/save_allowed_warehouse_for_fast_issuance" method="POST">
			@csrf
			<div class="modal-content">
				<div class="modal-header text-white" style="background-color: #0277BD;">
					<h5 class="modal-title" style="font-size: 11pt;"><i class="now-ui-icons"></i> Add Allowed Warehouse for Fast Issuance</h5>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-10 offset-md-1">
							<div class="form-group">
								<label>Warehouse</label>
								<select class="form-control" name="warehouse" id="fast-issuance-sel" required>
									@foreach($warehouse_wip as $w)
									<option value="{{ $w->name }}">{{ $w->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer pt-1 pb-1 pr-2">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="delete-allowed-fast-issuance-warehouse" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document" style="width: 30%;">
		<form action="/delete_allowed_warehouse_for_fast_issuance" method="POST">
			@csrf
			<div class="modal-content">
				<div class="modal-header text-white" style="background-color: #0277BD;">
					<h5 class="modal-title" style="font-size: 11pt;">Delete Allowed Warehouse for Fast Issuance</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<p>Delete <span id="allowed-fast-issuance-warehouse-name" class="font-weight-bold"></span> in allowed warehouses for fast issuance?</p>
								<input type="hidden" name="id" id="allowed-fast-issuance-warehouse-id">
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
<div class="modal fade" id="add-allowed-fast-issuance-user" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<form action="/save_allowed_user_for_fast_issuance" method="POST">
			@csrf
			<div class="modal-content">
				<div class="modal-header text-white" style="background-color: #0277BD;">
					<h5 class="modal-title" style="font-size: 11pt;"><i class="now-ui-icons"></i> Add Allowed User for Fast Issuance</h5>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-10 offset-md-1">
							<div class="form-group">
								<label>User</label>
								<select class="form-control" name="user_access_id" id="fast-issuance-sel2" required>
									@foreach($mes_users as $user_access_id => $employee_name)
									<option value="{{ $user_access_id }}">{{ $employee_name }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer pt-1 pb-1 pr-2">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="delete-allowed-fast-issuance-user" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document" style="width: 30%;">
		<form action="/delete_allowed_user_for_fast_issuance" method="POST">
			@csrf
			<div class="modal-content">
				<div class="modal-header text-white" style="background-color: #0277BD;">
					<h5 class="modal-title" style="font-size: 11pt;">Delete Allowed User for Fast Issuance</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<p>Delete <span id="allowed-fast-issuance-user-name" style="font-weight: bold;"></span> in allowed users for fast issuance?</p>
								<input type="hidden" name="id" id="allowed-fast-issuance-user-id">
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
@include('modals.uom_conversion_modal')
@include('modals.delete_uom_conversion_modal')
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
</style>

@endsection

@section('script')
<script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />

<script>
  $(document).ready(function(){
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

   	// Item Warehouse Setup form submits
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
	// Item Warehouse Setup form submits

	// Fast Issuance Setup form submits
	$('#add-allowed-fast-issuance-warehouse form').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.status) {
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#add-allowed-fast-issuance-warehouse').modal('hide');
            fast_issuance_warehouse();
          }else{
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }
        }
      });
    });
	$('#delete-allowed-fast-issuance-warehouse form').submit(function(e){
		e.preventDefault();

		$.ajax({
			url: $(this).attr("action"),
			type:"POST",
			data: $(this).serialize(),
			success:function(data){
				if (data.status) {
					showNotification("success", data.message, "now-ui-icons ui-1_check");
					$('#delete-allowed-fast-issuance-warehouse').modal('hide');
					fast_issuance_warehouse();
				}else{
					showNotification("danger", 'There was a problem deleting warehouse.', "now-ui-icons travel_info");
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(jqXHR);
				console.log(textStatus);
				console.log(errorThrown);
			}
		});
	});
	$('#add-allowed-fast-issuance-user form').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.status) {
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#add-allowed-fast-issuance-user').modal('hide');
            fast_issuance_user();
          }else{
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }
        }
      });
    });
	$('#delete-allowed-fast-issuance-user form').submit(function(e){
		e.preventDefault();

		$.ajax({
			url: $(this).attr("action"),
			type:"POST",
			data: $(this).serialize(),
			success:function(data){
				if (data.status) {
					showNotification("success", data.message, "now-ui-icons ui-1_check");
					$('#delete-allowed-fast-issuance-user').modal('hide');
					fast_issuance_user();
				}else{
					showNotification("danger", 'There was a problem deleting user.', "now-ui-icons travel_info");
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(jqXHR);
				console.log(textStatus);
				console.log(errorThrown);
			}
		});
	});
	// Fast Issuance Setup form submits

	// WIP warehouse form submits
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
	// WIP warehouse form submits

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

	$(document).on('click', '.delete-allowed-fast-issuance-warehouse-btn', function(e){
		e.preventDefault();
		var warehouse = $(this).data('warehouse');
		var id = $(this).data('id');

		$('#allowed-fast-issuance-warehouse-id').val(id);
		$('#allowed-fast-issuance-warehouse-name').text(warehouse);

		$('#delete-allowed-fast-issuance-warehouse').modal('show');
	});

	$(document).on('click', '.delete-allowed-fast-issuance-user-btn', function(e){
		e.preventDefault();
		var username = $(this).data('username');
		var id = $(this).data('id');

		$('#allowed-fast-issuance-user-id').val(id);
		$('#allowed-fast-issuance-user-name').text(username);

		$('#delete-allowed-fast-issuance-user').modal('show');
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
    if(is_active == "hide_me" || is_active == "fiw" || is_active == "fiu"){
      $('#add-item-warehouse-button').hide();
      
      
    }else{
      $('#add-item-warehouse-button').show();
      $('#add-item-warehouse-modal .modal-title').text(is_active);
      $('#icw_operation option:contains(' + is_active + ')').prop({selected: true});
      $('#icw_operation').trigger('change');
    }
   
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


  });
</script>
<script type="text/javascript">
$(document).ready(function(){
  item_classification_warehouse_tbl_fabrication();
  item_classification_warehouse_tbl_painting();
  item_classification_warehouse_tbl_assembly();
  tbl_wip_list();

  $('.schedule-date').datepicker({
    'format': 'yyyy-mm-dd',
    'autoclose': true
  });

  
   

  $('.sel5').select2({
    dropdownParent: $("#add-item-warehouse-modal"),
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


     $('.modal').on('hidden.bs.modal', function(){
      var frm = $(this).find('form')[0];
      if (frm) frm.reset();
    });

});

      $('.modal').on('hidden.bs.modal', function(){
      var frm = $(this).find('form')[0];
      if (frm) frm.reset();
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

$('#fast-issuance-sel2').select2({
                  dropdownParent: $("#add-allowed-fast-issuance-user"),
                  dropdownAutoWidth: false,
                  width: '100%',
                  cache: false
                });
$('#fast-issuance-sel').select2({
                  dropdownParent: $("#add-allowed-fast-issuance-warehouse"),
                  dropdownAutoWidth: false,
                  width: '100%',
                  cache: false
                });

  
     
     
       $(document).on("click", ".delete", function(){
        $(this).parents("tr").remove();
      });

$(document).on('click', '#tbl-fast-issuance-user-pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    fast_issuance_user(page);
  });
$(document).on('click', '#tbl-fast-issuance-warehouse-pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    fast_issuance_warehouse(page);
  });


</script>
<script type="text/javascript">
fast_issuance_warehouse();
fast_issuance_user();
function fast_issuance_user(page, query){
        $.ajax({
         url:"/allowed_user_for_fast_issuance?page=" + page,
        type:"GET",
        data: {search_string: query},
        success:function(data){
        
            $('#tbl-fast-issuance-user').html(data);

        }
      });
    }
function fast_issuance_warehouse(page, query){
        $.ajax({
         url:"/allowed_warehouse_for_fast_issuance?page=" + page,
        type:"GET",
        data: {search_string: query},
        success:function(data){
        
            $('#tbl-fast-issuance-warehouse').html(data);

        }
      });
    }

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
     
	$(document).on("click", ".delete", function(){
        $(this).parents("tr").remove();
	});

</script>

@endsection