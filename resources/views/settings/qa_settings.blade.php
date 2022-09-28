@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'settings_module',
    'pageHeader' => 'QA Settings',
    'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header"></div>
<div class="row p-0" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 1000px;">
	<div class="col-2 p-2">
		<div class="card" id="workstation_navbar">
      <div class="card-header p-2" style="background-color: #0277BD;">
				<h5 class="text-white text-center text-uppercase m-0 font-weight-bold" style="font-size: 13pt;">Settings</h5>
			</div>
      <div class="card-body" style="min-height: 850px;">
				<div class="row bg-white text-center">
					<div class="col-md-12">
						<ul class="nav flex-column workstation_navbar" id="myTab" role="tablist" style="font-size: 10pt;">
							<li class="nav-item">
								<a class="nav-link active" href="#qa_setup" data-toggle="tab">QA Inspection Setup</a>
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
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
  <div class="col-10 p-2" style="min-height: 1000px;">
    <div class="tab-content text-center">
      <div class="tab-pane active" id="qa_setup">
        <div class="card">
					<div class="card-header p-0 m-0" style="background-color: #0277BD;">
						<div class="row p-2 m-0">
							<div class="col-md-8 p-0 m-0">
								<h5 class="text-white font-weight-bold text-left p-1 m-0">Quality Inspection Setup</h5>
							</div>
							<div class="col-md-4 p-0 m-0">
								<div class="form-group m-0">
									<input type="text" class="form-control rounded bg-white" placeholder="Search" id="search_reject_setup">
								</div>
							</div>
						</div>
					</div>
          <div class="card-body p-0">
            <div class="row p-0 m-0 bg-white" style="min-height: 1000px;">
              <div class="col-md-12">
                <div class="nav-tabs-navigation mt-2">
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
                  <div class="tab-pane active p-0" id="inspection_checklist">
                    <div class="row p-0 m-0">
											<div class="col-md-4 pl-0 pb-1 pt-1 pr-2">
												<div class="card" style="min-height: 1000px;">
													<table class="text-white" style="width: 100%;background-color:#34495e;">
														<col style="width: 70%;">
														<col style="width: 30%;">
														<tr>
															<th class="text-left font-weight-bold" style="padding-left: 20px; font-size: 12pt;">Fabrication</th>
															<td class="text-right">
																<button type="button" class="btn pb-2 pr-3 pl-3 pt-2 btn-primary" id="add-checklist-fabrication-button" style="margin: 5px;">
																	<i class="now-ui-icons ui-1_simple-add"></i> Add
																</button>
															</td>
														</tr>
													</table>
													<div class="card-body p-1">
														<div class="tbl_check_list_fabrication" id="tbl_check_list_fabrication"></div>
													</div>
												</div>
											</div>
											<div class="col-md-4 pl-1 pb-1 pt-1 pr-1">
												<div class="card" style="min-height: 1000px;">
													<table class="text-white" style="width: 100%;background-color:#34495e;">
														<col style="width: 70%;">
														<col style="width: 30%;">
														<tr>
															<th class="text-left font-weight-bold" style="padding-left: 20px; font-size: 12pt;">Painting</th>
															<td class="text-right">
																<button type="button" class="btn pb-2 pr-3 pl-3 pt-2 btn-primary" id="add-checklist-painting-button" style="margin: 5px;">
																	<i class="now-ui-icons ui-1_simple-add"></i> Add
																</button>
															</td>
														</tr>
													</table>
													<div class="card-body p-1">
														<div class="tbl_check_list_painting" id="tbl_check_list_painting"></div>
													</div>
												</div>
											</div>
											<div class="col-md-4 pl-2 pb-1 pt-1 pr-0">
												<div class="card" style="min-height: 1000px;">
													<table class="text-white" style="width: 100%; background-color:#34495e;">
														<col style="width: 70%;">
														<col style="width: 30%;">
														<tr>
															<th class="text-left font-weight-bold" style="padding-left: 20px; font-size: 12pt;">Wiring and Assembly</th>
															<td class="text-right">
																<button type="button" class="btn pb-2 pr-3 pl-3 pt-2 btn-primary" id="add-checklist-assembly-button" style="margin: 5px;">
																	<i class="now-ui-icons ui-1_simple-add"></i> Add
																</button>
															</td>
														</tr>
													</table>
													<div class="card-body p-1">
														<div class="tbl_check_list_assembly" id="tbl_check_list_assembly"></div>
													</div>
												</div>
											</div>
                    </div>
                  </div>
                  <div class="tab-pane" id="qa_reject_list">
                    <div class="row p-0 m-0">
                      <div class="col-md-12 pl-0 pb-0 pt-1 pr-0">
                        <div class="card" style="min-height: 720px;">
                          <table class="text-white" style="width: 100%; background-color:#34495e;">
                            <col style="width: 70%;">
                            <col style="width: 30%;">
                            <tr>
                              <th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>QA Reject List</b></th>
                              <td class="text-right">
                                <button type="button" class="btn btn-primary pb-2 pr-3 pl-3 pt-2" id="add-reject-button" style="margin: 5px;">
                                  <i class="now-ui-icons ui-1_simple-add"></i> Add
                                </button>
                              </td>
                            </tr>
                          </table>
                          <div class="card-body p-0">
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
        <div class="card">
					<div class="card-header p-0 m-0" style="background-color: #0277BD;">
						<div class="row p-2 m-0">
							<div class="col-md-8 p-0 m-0">
								<h5 class="text-white font-weight-bold text-left p-1 m-0">Sampling Plan Setup</h5>
							</div>
							<div class="col-md-4 p-0 m-0">
								<div class="form-group m-0">
									<input type="text" class="form-control rounded bg-white" placeholder="Search" id="search_machine_setupss">
								</div>
							</div>
						</div>
					</div>
          <div class="card-body p-0">
            <div class="row p-0 m-0 bg-white" style="min-height: 500px;">
							<div class="col-md-4 pl-2 pb-1 pt-1 pr-2">
								<div class="card" style="min-height: 720px;">
									<table class="text-white" style="width: 100%;background-color:#34495e;">
										<col style="width: 70%;">
										<col style="width: 30%;">
										<tr>
											<th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>Visual(Minor Defects)</b></th>
											<td class="text-right">
												<button type="button" class="btn btn-primary pb-2 pr-3 pl-3 pt-2 add-smpl-plan add-smpl-plan" id="add-visual-button" data-id="1" data-spname="Visual" style="margin: 5px;">
													<i class="now-ui-icons ui-1_simple-add"></i> Add
												</button>
											</td>
										</tr>
									</table>
									<div class="card-body p-1">
										<div class="tbl_visual" id="tbl_visual"></div>
									</div>
								</div>
							</div>
							<div class="col-md-4 pl-2 pb-1 pt-1 pr-2">
								<div class="card" style="min-height: 720px;">
									<table class="text-white" style="width: 100%;background-color:#34495e;">
										<col style="width: 70%;">
										<col style="width: 30%;">
										<tr>
											<th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>Variable(Major Defects)</b></th>
											<td class="text-right">
												<button type="button" class="btn pb-2 pr-3 pl-3 pt-2 btn-primary add-smpl-plan" id="add-variable-button" style="margin: 5px;" data-id="2" data-spname="Variable">
													<i class="now-ui-icons ui-1_simple-add"></i> Add
												</button>
											</td>
										</tr>
									</table>
									<div class="card-body p-1">
										<div class="tbl_variable" id="tbl_variable"></div>
									</div>
								</div>
							</div>
							<div class="col-md-4 pl-2 pb-1 pt-1 pr-2">
								<div class="card" style="min-height: 720px;">
									<table class="text-white" style="width: 100%;background-color:#34495e;">
										<col style="width: 70%;">
										<col style="width: 30%;">
										<tr>
											<th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>Reliability(Critical Defects)</b></th>
											<td class="text-right">
												<button type="button" class="btn pb-2 pr-3 pl-3 pt-2 btn-primary add-smpl-plan" id="add-reliability-button" style="margin: 5px;" data-id="3" data-spname="Reliability">
													<i class="now-ui-icons ui-1_simple-add"></i> Add
												</button>
											</td>
										</tr>
									</table>
									<div class="card-body p-1">
										<div class="tbl_reliability" id="tbl_reliability"></div>
									</div>
								</div>
							</div>
            </div>        
          </div>
        </div>
      </div>
      <div class="tab-pane" id="operator_reject_setup">
        <div class="card">
					<div class="card-header p-0 m-0" style="background-color: #0277BD;">
						<div class="row p-2 m-0">
							<div class="col-md-8 p-0 m-0">
								<h5 class="text-white font-weight-bold text-left p-1 m-0">Operator Reject Setup</h5>
							</div>
							<div class="col-md-4 p-0 m-0">
								<div class="form-group m-0">
									<input type="text" class="form-control rounded bg-white" placeholder="Search" id="search_operator_reject_setup">
								</div>
							</div>
						</div>
					</div>
          <div class="card-body p-0">
            <div class="row p-0 m-0 bg-white" style="min-height: 700px;">
              <div class="col-md-12">
                <div class="nav-tabs-navigation mt-2">
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
                          <div class="card-body p-0">
                            <div class="tbl_reject_list" id="tbl_reject_list"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane" id="operator_reject_list">
                    <div class="row p-0 m-0">
                      <div class="col-md-12 pl-0 pb-0 pt-1 pr-0">
                        <div class="card" style="min-height: 720px;">
                          <table class="text-white" style="width: 100%;background-color:#34495e;">
                            <col style="width: 70%;">
                            <col style="width: 30%;">
                            <tr>
                              <th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>Operator Reject List</b></th>
                              <td class="text-right">
                                <button type="button" class="btn pb-2 pr-3 pl-3 pt-2 btn-primary" id="add-operator-reject-button" style="margin: 5px;">
                                  <i class="now-ui-icons ui-1_simple-add"></i> Add
                                </button>
                              </td>
                            </tr>
                          </table>
                          <div class="card-body p-0">
                            <div class="tbl_operator_reject_list" id="tbl_operator_reject_list"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane active" id="op_fabrication">
                    <div class="row p-0 m-0">
                      <div class="col-md-12 pl-0 pb-0 pt-1 pr-0">
                        <div class="card" style="min-height: 720px;">
                          <table class="text-white" style="width: 100%;background-color:#34495e;">
                            <col style="width: 70%;">
                            <col style="width: 30%;">
                            <tr>
                              <th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>Fabrication</b></th>
                              <td class="text-right">
                                <button type="button" class="btn pb-2 pr-3 pl-3 pt-2 btn-primary" id="add-opchecklist-fabrication-button" style="margin: 5px;">
                                  <i class="now-ui-icons ui-1_simple-add"></i> Add
                                </button>
                              </td>
                            </tr>
                          </table>
                          <div class="card-body p-0">
                            <div class="tbl_check_list_fabrication" id="tbl_opcheck_list_fabrication"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane" id="op_painting">
                    <div class="row p-0 m-0">
                      <div class="col-md-12 pl-0 pb-0 pt-1 pr-0">
                        <div class="card" style="min-height: 720px;">
                          <table class="text-white" style="width: 100%;background-color:#34495e;">
                            <col style="width: 70%;">
                            <col style="width: 30%;">
                            <tr>
                              <th class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>Painting</b></th>
                              <td class="text-right">
                                <button type="button" class="btn pb-2 pr-3 pl-3 pt-2 btn-primary" id="add-opchecklist-painting-button" style="margin: 5px;">
                                  <i class="now-ui-icons ui-1_simple-add"></i> Add
                                </button>
                              </td>
                            </tr>
                          </table>
                          <div class="card-body p-0">
                            <div class="tbl_check_list_painting" id="tbl_opcheck_list_painting"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane" id="op_assembly">
                    <div class="row p-0 m-0">
                      <div class="col-md-12 pl-0 pb-0 pt-1 pr-0">
                        <div class="card" style="min-height: 720px;">
                          <table class="text-white" style="width: 100%;background-color:#34495e;">
                            <tr>
                              <td class="text-left" style="padding-left: 20px; font-size: 12pt;"><b>Wiring and Assembly</b></td>
                              <td class="text-right">
                                <button type="button" class="btn pb-2 pr-3 pl-3 pt-2 btn-primary" id="add-opchecklist-painting-button" style="margin: 5px;">
                                  <i class="now-ui-icons ui-1_simple-add"></i> Add
                                </button>
                              </td>
                            </tr>
                          </table>
                          <div class="card-body p-0">
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
        <div class="card">
					<div class="card-header p-0 m-0" style="background-color: #0277BD;">
						<div class="row p-2 m-0">
							<div class="col-md-8 p-0 m-0">
								<h5 class="text-white font-weight-bold text-left p-1 m-0">Material Type Setup</h5>
							</div>
							<div class="col-md-4 p-0 m-0">
								<div class="form-group m-0">
									<input type="text" class="form-control rounded bg-white" placeholder="Search" id="search_material_type_setup">
								</div>
							</div>
						</div>
					</div>
          <div class="card-body p-0">
            <div class="row p-0 m-0 bg-white" style="min-height: 500px;">
							<div class="col-md-12">
								<button type="button" class="btn btn-primary" id="add-material-type-button" style="float: right;"><i class="now-ui-icons ui-1_simple-add"></i> Add Material Type </button>
								<div id="tbl_material_type"></div>    
							</div>
            </div>        
          </div>
        </div>
      </div>
      <div class="tab-pane" id="qa_reject_category">
        <div class="card" style="background-color: #0277BD;" >
					<div class="card-header p-0 m-0" style="background-color: #0277BD;">
						<div class="row p-2 m-0">
							<div class="col-md-8 p-0 m-0">
								<h5 class="text-white font-weight-bold text-left p-1 m-0">Reject Category Setup</h5>
							</div>
							<div class="col-md-4 p-0 m-0">
								<div class="form-group m-0">
									<input type="text" class="form-control rounded bg-white" placeholder="Search" id="search_reject_category_setup">
								</div>
							</div>
						</div>
					</div>
          <div class="card-body p-0">
            <div class="row p-0 m-0 bg-white" style="min-height: 500px;">
							<div class="col-md-12">
								<button type="button" class="btn btn-primary" id="add-reject-ctg-button" style="float: right;"><i class="now-ui-icons ui-1_simple-add"></i> Reject Category </button>
								<div class="tbl-reject-ctg" id="tbl-reject-ctg"></div>
							</div>
            </div>        
          </div>
        </div>
      </div>
  	</div>
	</div>
</div>

<!-- Reject Setup -->
<div class="modal fade" id="add-reject-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document" style="min-width: 60%;">
		<form action="/save_reject_list" method="POST" id="save-reject-frm">
			@csrf
			<div class="modal-content">
				<div class="modal-header text-white" style="background-color: #0277BD;">
					<h5 class="modal-title" id="modal-title "> Add Reject Checklist</h5>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div style="margin: -5px;">
								<a href="#" class="btn btn-primary add-row pull-right">
									<i class="now-ui-icons ui-1_simple-add"></i> Add
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
                 <span class="operation-text" style="font-weight: bolder;"></span>
              </h5>
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
                 <span class="sampling-text" style="font-weight: bolder;"></span>
              </h5>
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
                 <span class="sampling-delete-text" style="font-weight: bolder;"></span>
              </h5>
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
                 <span class="operation-text" style="font-weight: bolder;"></span>
              </h5>
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
              <h5 class="modal-title" id="modal-title "> Add Material Type<br></h5>
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
                          <tr style=""></tr>
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
              <h5 class="modal-title" id="modal-title "> Edit Material Type<br></h5>
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
            <h5 class="modal-title" id="modal-title"></h5>
          </div>
          <div class="modal-body">
            <input type="hidden" name="owner_checklist" id="painting_owner_checklist">
            <div class="col-sm-12">
              <div class="form-group">
                <label><b>Workstation:</b></label>
                <select class="form-controls sel4" name="workstation_id" id="painting_r_workstation_id" class="r_workstation_id" required></select>
              </div>
              <a href="#" class="btn btn-primary add-row"><i class="now-ui-icons ui-1_simple-add"></i>Add</a>
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

.select-input{
    height: 33px;
    font-size: 12px;
}

.timepicker { 
  font-size:9pt !important;
  min-width:200px !important;
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

</style>
@endsection

@section('script')
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
   
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 

    check_list_fabrication();
    check_list_painting();
    check_list_assembly();
    qa_reject_list();
    reject_category_list();
    tbl_sampling_plan_visual();
    tbl_sampling_plan_variable();
    tbl_sampling_plan_reliability();

    $('.sel4').select2({
      dropdownParent: $("#add-checklist-modal"),
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
    
    $('.operator-checklist-sel').select2({
      dropdownParent: $("#add-operator-checklist-modal"),
      dropdownAutoWidth: false,
      width: '100%',
      cache: false
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

    $('.modal').on('hidden.bs.modal', function(){
      var frm = $(this).find('form')[0];
      if (frm) frm.reset();
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
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });

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
          var len2 = new_id;
          var id_unique="count"+len2;
              
          var tblrow = '<tr>' +
            '<td>'+len2+'</td>' +
            '<td><select name="new_checklist_r_type[]" class="form-control onchange-selection count-row sel16"  data-idcolumn='+id_unique+' required>'+row+'</select></td>' +
            '<td><select name="new_checklist_r_desc[]" class="form-control sel16" id='+id_unique+' required></select></td>' +
            '<td><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>' +
            '</tr>';

          $("#add-checklist-modal #reject-table").append(tblrow);
          $('.sel16').select2({
            dropdownParent: $("#add-checklist-modal "),
            dropdownAutoWidth: false,
            width: '100%',
            cache: false
          });
        },
        error: function(response) {
          console.log('Error fetching Designation!');
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
    
    $(document).on('click', '#add-reject-ctg-button ', function(){       
      $('#add-reject-category-modal').modal('show');
    });

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

    $(document).on('click', '.edit-reject-category-btn', function(){
      var ctg_id = $(this).attr('data-id');
      var ctg_type = $(this).attr('data-type');
      var ctg_name = $(this).attr('data-category');
      var ctg_desc = $(this).attr('data-categorydesc');
      $('#edit_type option:contains(' + ctg_type + ')').prop({selected: true});
      $('#edit_type').val(ctg_type);
      $('#orig_reject_ctgtype').val(ctg_type);
      $('#orig_category').val(ctg_name);
      $('#edit_category').val(ctg_name);
      $('#edit_reject_ctgdesc').val(ctg_desc);
      $('#orig_reject_ctgdesc').val(ctg_desc);
      $('#ctg_id').val(ctg_id);
      $('#edit-reject-category-modal').modal('show');
    });

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

    $(document).on('click', '.btn-delete-reject-category', function(){
      var ctg_id = $(this).attr('data-id');
      var ctg_type = $(this).attr('data-type');
      var ctg_name = $(this).attr('data-category');
      var ctg_desc = $(this).attr('data-categorydesc');
      $('#delete_reject_category_label').text(ctg_name);
      $('#delete_reject_category_id').val(ctg_id);
      $('#delete-reject-category-modal').modal('show');
    });

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

    function tbl_sampling_plan_visual(page){
      $.ajax({
        url:"/get_tbl_qa_visual?page=" + page,
        type:"GET",
        success:function(data){
          $('#tbl_visual').html(data);
        }
      });
    }

    function tbl_sampling_plan_variable(page){
      $.ajax({
        url:"/get_tbl_qa_variable?page=" + page,
        type:"GET",
        success:function(data){
          $('#tbl_variable').html(data);
        }
      });
    }

    function tbl_sampling_plan_reliability(page){
      $.ajax({
        url:"/get_tbl_qa_reliability?page=" + page,
        type:"GET",
        success:function(data){
          $('#tbl_reliability').html(data);
        }
      });
    }
    
    $(document).on('click', '.add-smpl-plan', function(){
      var id = $(this).attr('data-id');
      var spname = $(this).attr('data-spname');
      $.ajax({
        url:"/get_reject_category_for_add_reject_modal",
        type:"GET",
        success:function(data){
          $('#sp_category').html(data.category);
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

    $(document).on('click', '.btn-delete-sampling_plan', function(){
      var id = $(this).attr('data-id');
      var category = $(this).attr('data-category');
      $('#delete_sampling_plan_id').val(id);
      $('#delete_sampling_plan_category').val(category);
      $('#delete-sampling-plan-modal').modal('show');
    });

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
          } else {
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
          console.log('Error!');
        }
      });
    });

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
          } else {
            var new_id = parseInt(id) + 1;
          }

          var len2 = new_id;
          var id_unique="operator-count"+len2;
              
          var tblrow = '<tr>' +
            '<td>'+len2+'</td>' +
            '<td><select name="operator_new_checklist_r_type[]" class="form-control operator-onchange-selection count-row operator-checklist-sel"   data-idcolumn='+id_unique+' required>'+row+'</select></td>' +
            '<td><select name="operator_new_checklist_r_process[]" class="form-control count-row operator-checklist-sel">'+row2+'</select></td>' +
            '<td><select name="operator_new_checklist_r_desc[]" class="form-control operator-checklist-sel" id='+id_unique+' required></select></td>' +
            '<td><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>' +
            '</tr>';

          $("#add-operator-checklist-modal #operator-checklist-table").append(tblrow);
        
          $('.operator-checklist-sel').select2({
            dropdownParent: $("#add-operator-checklist-modal"),
            dropdownAutoWidth: false,
            width: '100%',
            cache: false
          });
        },
        error: function(response) {
          console.log('Connection Lost, pls try again');
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
          } else {
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
          $('.sel17').select2({
            dropdownParent: $("#add-checklist-painting-modal"),
            dropdownAutoWidth: false,
            width: '100%',
            cache: false
          });
        },
        error: function(response) {
          console.log('Error fetching Designation!');
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
</script>
@endsection