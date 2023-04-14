@extends('layouts.user_app', [
  'namePage' => 'MES',
  'activePage' => 'inventory',
  'pageHeader' => 'Inventory',
  'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header">
</div>

<div class="row p-0" style="margin-top: -190px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body p-1">
					<div class="row">
						<div class="col-md-4 offset-md-8 pull-right" style="margin-bottom: -40px; z-index: 1;">
							<div class="form-group">
								<input type="text" class="form-control" placeholder="Search" id="inv-search-box">
							</div>
						</div>
						<div class="col-md-12">
							<div class="nav-tabs-navigation">
								<div class="nav-tabs-wrapper">
									<ul class="nav nav-tabs" data-tabs="tabs" id="operation-tab">
										<li class="nav-item" data-operation="Fabrication">
											<a class="nav-link active" href="#fabrication-tab" data-operation="Fabrication" data-toggle="tab">Fabrication</a>
										</li>
                    <li class="nav-item" data-operation="Painting">
											<a class="nav-link" href="#painting-tab" data-operation="Painting" data-toggle="tab">Painting</a>
										</li>
										<li class="nav-item" data-operation="Assembly">
											<a class="nav-link" href="#assembly-tab" data-operation="Assembly" data-toggle="tab">Assembly</a>
										</li>
									</ul>
								</div>
							</div>
							<div class="tab-content">
								<div class="tab-pane active" id="fabrication-tab">
									<div class="row m-1">
										<div class="col-md-12 pl-2 pr-2" id="fabrication-pending-inv-transaction">
											<div class="row mt-1">
												<div class="col-md-3">
													<div class="card" style="background-color: #0277BD;">
														<div class="card-body pb-0 pt-0 text-center text-white">
															<div class="row">
																<div class="col-md-12 p-1">
																	<h5 class="font-weight-bold align-middle m-1 ml-2" style="font-size: 13pt;">Material Transfer</h5>
																</div>
															</div>
															<div class="row" style="background-color: #263238;">
																<div class="col-md-12 pb-2">
																	<span class="d-block font-weight-bold" style="font-size: 32pt;">0</span>
																	<span class="d-block" style="font-size: 10pt;">0 Item(s)</span>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-3">
													<div class="card" style="background-color: #0277BD;">
														<div class="card-body pb-0 pt-0 text-center text-white">
															<div class="row">
																<div class="col-md-12 p-1">
																	<h5 class="font-weight-bold align-middle m-1 ml-2" style="font-size: 13pt;">Material Request</h5>
																</div>
															</div>
															<div class="row" style="background-color: #263238;">
																<div class="col-md-12 pb-2">
																	<span class="d-block font-weight-bold" style="font-size: 32pt;">0</span>
																	<span class="d-block" style="font-size: 10pt;">0 Item(s)</span>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-3">
													<div class="card" style="background-color: #0277BD;">
														<div class="card-body pb-0 pt-0 text-center text-white">
															<div class="row">
																<div class="col-md-12 p-1">
																	<h5 class="font-weight-bold align-middle m-1 ml-2" style="font-size: 13pt;">Pending for Issue</h5>
																</div>
															</div>
															<div class="row" style="background-color: #263238;">
																<div class="col-md-12 pb-2">
																	<span class="d-block font-weight-bold" style="font-size: 32pt;">0</span>
																	<span class="d-block" style="font-size: 10pt;">0 Item(s)</span>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-3">
													<div class="card" style="background-color: #0277BD;">
														<div class="card-body pb-0 pt-0 text-center text-white">
															<div class="row">
																<div class="col-md-12 p-1">
																	<h5 class="font-weight-bold align-middle m-1 ml-2" style="font-size: 13pt;">Pending to Receive</h5>
																</div>
															</div>
															<div class="row" style="background-color: #263238;">
																<div class="col-md-12 pb-2">
																	<span class="d-block font-weight-bold" style="font-size: 32pt;">0</span>
																	<span class="d-block" style="font-size: 10pt;">0 Item(s)</span>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12 pl-2 pr-2">
											<div class="nav-tabs-navigation mt-1">
												<div class="nav-tabs-wrapper m-0">
													<ul class="nav nav-tabs p-0" data-tabs="tabs">
														<li class="nav-item">
															<a class="nav-link active" href="#stock-list-tab" data-toggle="tab">Inventory List</a>
														</li>
														<li class="nav-item">
															<a class="nav-link" href="#rm-monitoring-tab" data-toggle="tab">Materials Monitoring</a>
														</li>
														<li class="nav-item">
															<a class="nav-link" href="#mt-tab" data-toggle="tab">Material Transfer(s)</a>
														</li>
														<li class="nav-item">
															<a class="nav-link" href="#mr-tab" data-toggle="tab">Material Request(s)</a>
														</li>
														<li class="nav-item">
															<a class="nav-link" href="#withdrawal-tab" data-toggle="tab">Withdrawal Slip(s)</a>
														</li>
													</ul>
												</div>
											</div>
											<div class="tab-content">
                        <div class="tab-pane active" id="stock-list-tab">
													<div class="row m-1">
														<div class="col-md-2">
															<div class="row">
																<div class="col-md-12 p-1" id="invetory-filters-div">
																	<div class="card" style="background-color: #0277BD;">
																		<div class="card-body pb-0">
																			<div class="row">
																				<div class="col-md-12" style="margin-top: -10px;">
																					<h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Search Filter(s)</h5>
																				</div>
																			</div>
																			<form action="#" id="inventory">
																				<div class="row" style="background-color: #ffffff; padding-top: 9px;">
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Part Category</span>
																						<div class="form-group">
																							<select class="form-control inv-filters" name="filters[]" id="inventory-part-category" data-filter-content="inventory">
																								<option value="">Select Part Category</option>
																							</select>
																						</div>
																					</div>
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Item Name</span>
																						<div class="form-group">
																							<select class="form-control inv-filters" name="filters[]" id="inventory-item-name" data-filter-content="inventory">
																								<option value="">Select Item Name</option>
																							</select>
																						</div>
																					</div>
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Material</span>
																						<div class="form-group">
																							<select class="form-control inv-filters" name="filters[]" id="inventory-material" data-filter-content="inventory">
																								<option value="">Select Material</option>
																							</select>
																						</div>
																					</div>
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Length</span>
																						<div class="form-group">
																							<select class="form-control inv-filters" name="filters[]" id="inventory-length" data-filter-content="inventory">
																								<option value="">Select Length</option>
																							</select>
																						</div>
																					</div>
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Width</span>
																						<div class="form-group">
																							<select class="form-control inv-filters" name="filters[]" id="inventory-width" data-filter-content="inventory">
																								<option value="">Select Width</option>
																							</select>
																						</div>
																					</div>
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Thickness</span>
																						<div class="form-group">
																						<select class="form-control inv-filters" name="filters[]" id="inventory-thickness" data-filter-content="inventory">
																							<option value="">Select Thickness</option>
																						</select>
																						</div>
																					</div>
																				</div>
																			</form>
																		</div>
																	</div>
																</div>
																<div class="col-md-12 p-1" id="transaction-filter-div">
																	<div class="card" style="background-color: #0277BD;" >
																		<div class="card-body pb-0">
																			<div class="row">
																				<div class="col-md-12" style="margin-top: -10px;">
																					<h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Search Filter(s)</h5>
																				</div>
																			</div>
																			<form action="#" id="transaction">
																				<div class="row" style="background-color: #ffffff; padding-top: 9px;">
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Material</span>
																						<div class="form-group">
																							<select class="form-control inv-filters" name="material" id="transaction-material" data-filter-content="transaction">
																								<option value="">Select Material</option>
																							</select>
																						</div>
																					</div>
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Length</span>
																						<div class="form-group">
																							<select class="form-control inv-filters" name="length" id="transaction-length" data-filter-content="transaction">
																								<option value="">Select Length</option>
																							</select>
																						</div>
																					</div>
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Width</span>
																						<div class="form-group">
																							<select class="form-control inv-filters" name="width" id="transaction-width" data-filter-content="transaction">
																								<option value="">Select Width</option>
																							</select>
																						</div>
																					</div>
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Thickness</span>
																						<div class="form-group">
																							<select class="form-control inv-filters" name="thickness" id="transaction-thickness" data-filter-content="transaction">
																								<option value="">Select Thickness</option>
																							</select>
																						</div>
																					</div>
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Entry Type</span>
																						<div class="form-group">
																							<select class="form-control inv-filters" name="entry_type" data-filter-content="transaction">
																								<option value="">Select Entry Type</option>
																								<option value="New Entry">New Entry</option>
																								<option value="Stock Adjustment">Stock Adjustment</option>
																							</select>
																						</div>
																					</div>
																				</div>
																			</form>
																		</div>
																	</div>
																</div>
															</div>
														</div>
														<div class="col-md-10 p-1">
															<div class="card" style="background-color: #0277BD;">
															  <div class="card-body pb-0 pt-0">
																 	<div class="row">
																		<div class="col-md-12 p-1">
																			<h5 class="text-white font-weight-bold align-middle m-1 ml-2">Fabrication Inventory</h5>
																		</div>
																 	</div>
																 	<div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
																 		<div class="col-md-12 pl-2 pr-2">
																 			<div class="nav-tabs-navigation mt-1">
																				<div class="nav-tabs-wrapper m-0">
																					<ul class="nav nav-tabs p-0" data-tabs="tabs" id="inventory-nav-tabs">
																						<li class="nav-item">
																							<a class="nav-link active" href="#inv-list-tab" data-toggle="tab">Stock List</a>
																						</li>
																						<li class="nav-item">
																							<a class="nav-link" href="#transaction-history-tab" data-toggle="tab">Transaction History</a>
																						</li>
																					</ul>
																				</div>
																			</div>
																			<div class="tab-content">
																				<div class="tab-pane active" id="inv-list-tab">
																					<div class="row m-1">
																						<div class="col-md-12 p-0" style="min-height: 600px;">
																							<div id="fab-inventory-table" style="font-size:15px;"></div>
																						</div>
																					</div>
																				</div>
																				<div class="tab-pane" id="transaction-history-tab">
																					<div class="row m-1">
																						<div class="col-md-6 offset-md-6 p-1 text-right" style="margin-top: -93px;">
																							<button class="btn btn-primary btn-stock-adjust-entry m-0">+ Stock Adjustment Entry</button>
																						</div>
																						<div class="col-md-12 p-0" style="min-height: 600px;">
																							<div id="fab-inv-transaction-table" style="font-size:15px;"></div>
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
                        
                        <div class="tab-pane" id="rm-monitoring-tab">
													<div class="row m-1">
                          	<div class="col-md-2">
															<div class="row">
																<div class="col-md-12 p-1" id="raw-filters-div">
																	<div class="card" style="background-color: #0277BD;">
																		<div class="card-body pb-0">
																			<div class="row">
																				<div class="col-md-12" style="margin-top: -10px;">
																					<h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Search Filter(s)</h5>
																				</div>
																			</div>
																			<form action="#" id="raw">
																				<div class="row" style="background-color: #ffffff; padding-top: 9px;">
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Length (mm)</span>
																						<div class="form-group">
																							<select class="form-control raw-filters" name="length" id="raw-length" data-filter-content="scrap">
																								<option value="">Select Length</option>
																							</select>
																						</div>
																					</div>
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Width (mm)</span>
																						<div class="form-group">
																							<select class="form-control raw-filters" name="width" id="raw-width" data-filter-content="scrap">
																								<option value="">Select Width</option>
																							</select>
																						</div>
																					</div>
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Thickness (mm)</span>
																						<div class="form-group">
																							<select class="form-control raw-filters" name="thickness" id="raw-thickness" data-filter-content="scrap">
																								<option value="">Select Thickness</option>
																							</select>
																						</div>
																					</div>
																				</div>
																			</form>
																		</div>
																	</div>
																</div>
																<div class="col-md-12 p-1" id="scrap-filter-div">
																	<div class="card" style="background-color: #0277BD;">
																		<div class="card-body pb-0">
																			<div class="row">
																				<div class="col-md-12" style="margin-top: -10px;">
																					<h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Search Filter(s)</h5>
																				</div>
																			</div>
																			<form action="#" id="scrap">
																				<div class="row" style="background-color: #ffffff; padding-top: 9px;">
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Material</span>
																						<div class="form-group">
																							<select class="form-control inv-filters" name="material" id="scrap-material" data-filter-content="scrap">
																								<option value="">Select Material</option>
																							</select>
																						</div>
																					</div>
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Length (mm)</span>
																						<div class="form-group">
																							<select class="form-control inv-filters" name="length" id="scrap-length" data-filter-content="scrap">
																								<option value="">Select Length</option>
																							</select>
																						</div>
																					</div>
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Width (mm)</span>
																						<div class="form-group">
																							<select class="form-control inv-filters" name="width" id="scrap-width" data-filter-content="scrap">
																								<option value="">Select Width</option>
																							</select>
																						</div>
																					</div>
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Thickness (mm)</span>
																						<div class="form-group">
																							<select class="form-control inv-filters" name="thickness" id="scrap-thickness" data-filter-content="scrap">
																								<option value="">Select Thickness</option>
																							</select>
																						</div>
																					</div>
																				</div>
																			</form>
																		</div>
																	</div>
																</div>
															</div>
														</div>
                            <div class="col-md-10 p-1">
															<div class="card" style="background-color: #0277BD;">
															  <div class="card-body pb-0 pt-0">
																 	<div class="row">
																		<div class="col-md-6 p-1">
																			<h5 class="text-white font-weight-bold align-middle m-1 ml-2">Materials Monitoring</h5>
																		</div>
																		<div class="col-md-6 p-1 text-right">
																			<button class="btn btn-primary m-0" id="add-scrap-btn">+ Add Scrap</button>
																		</div>
																	</div>
																	<div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
																		<div class="col-md-12 mt-1" style="min-height: 500px;">
																			<div class="nav-tabs-navigation" id="raw_monitoring">
																				<div class="nav-tabs-wrapper">
																					<ul class="nav nav-tabs" data-tabs="tabs" id="material-nav-tabs">
																						<li class="nav-item out_of_stock active_li" data-id="AS - Aluminum Sheets" id="alum_li">
																							<a class="nav-link active"  data-id="AS - Aluminum Sheets" href="#alum_tab" data-toggle="tab" class="out_of_stock">AS - Aluminum Sheets</a>
																						</li>
																						<li class="nav-item out_of_stock active_li" data-id="CS - Crs Steel Coil" id="crs_li">
																							<a class="nav-link" data-id="CS - Crs Steel Coil" href="#crs_tab" data-toggle="tab" class="out_of_stock">CS - Crs Steel Coil</a>
																						</li>
																						<li class="nav-item out_of_stock active_li"  data-id="DI - Diffuser" id="dif_li">
																							<a class="nav-link" data-id="DI - Diffuser" href="#diff_tab" data-toggle="tab" class="out_of_stock">DI - Diffuser</a>
																						</li>
																						<li class="nav-item active_li" data-id="scrap">
																							<a class="nav-link" href="#scrap-tab" data-toggle="tab" data-id="scrap">Scrap Inventory</a>
																						</li>
																					</ul>
																				</div>
																			</div>
																			<div class="tab-content text-center">
																				<div class="tab-pane" id="crs_tab">
																					<div class="row m-1">
																						<div class="col-md-10 p-3">
																							<h4 class="title m-0">CS - Crs Steel Coil</h4>
																							<canvas id="rawmaterial_crs_Chart" height="100"></canvas>
																						</div>
																						<div class="col-md-2 p-1" id="crs-scrap">
																							<div class="card" style="background-color: #0277BD;">
																								<div class="card-body pb-0 pt-0 text-white">
																									<div class="row">
																										<div class="col-md-12 p-1">
																											<h6 class="font-weight-bold align-middle m-1 ml-2 text-center" style="font-size: 13pt;">USABLE SCRAP</h6>
																										</div>
																									</div>
																									<div class="row" style="background-color: #263238; min-height: 150px;">
																										<div class="col-md-12 p-1">
																											<span class="d-block mt-3 font-weight-bold" style="font-size: 30pt;">0</span>
                                                      <span class="d-block" style="font-size: 12pt;">KG</span>
                                                      <span class="badge badge-info" style="font-size: 12pt;">KG</span>
                                                      
																										</div>
																									</div>
																								</div>
																							</div>
																							<div class="card" style="background-color: #0277BD;">
																								<div class="card-body pb-0 pt-0 text-white">
																									<div class="row">
																										<div class="col-md-12 p-1">
																											<h6 class="font-weight-bold align-middle m-1 ml-2 text-center" style="font-size: 13pt;">UNUSABLE SCRAP</h6>
																										</div>
																									</div>
																									<div class="row" style="background-color: #263238; min-height: 150px;">
																										<div class="col-md-12 p-1">
																											<span class="d-block mt-3 font-weight-bold" style="font-size: 30pt;">0</span>
																											<span class="d-block" style="font-size: 12pt;">KG</span>
																										</div>
																									</div>
																								</div>
																							</div>
																						</div>
																						<div class="col-md-12">
																							<div class="card" style="background-color: #0277BD;">
																								<div class="card-body pb-0 pt-0">
																									<div class="row">
																										<div class="col-md-12" style="margin-top:0px;">
																											<h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Out of Stock(s)</h5>
																										</div>
																									</div>
																									<div class="row" style="background-color: #ffffff; padding-top: 9px;min-height: 260px;max-height: 260px;overflow-y:auto;">
																										<div class="col-md-12 tbl_out_of_stock"></div>
																									</div>
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																			<div class="tab-pane active" id="alum_tab">
																					<div class="row m-1">
																						<div class="col-md-10 p-3">
																							<h4 class="title m-0">AS - Aluminum Sheets</h4>
																							<canvas id="rawmaterial_alum_Chart" height="100"></canvas>
																						</div>
																						<div class="col-md-2 p-1" id="aluminum-scrap">
																							<div class="card" style="background-color: #0277BD;">
																								<div class="card-body pb-0 pt-0 text-white">
																									<div class="row">
																										<div class="col-md-12 p-1">
																											<h6 class="font-weight-bold align-middle m-1 ml-2 text-center" style="font-size: 13pt;">USABLE SCRAP</h6>
																										</div>
																									</div>
																									<div class="row" style="background-color: #263238; min-height: 150px;">
																										<div class="col-md-12 p-1">
																											<span class="d-block mt-3 font-weight-bold" style="font-size: 30pt;">0</span>
                                                      <span class="d-block" style="font-size: 12pt;">KG</span>
                                                      <span class="badge badge-info" style="font-size: 12pt;">KG</span>
																										</div>
																									</div>
																								</div>
																							</div>
																							<div class="card" style="background-color: #0277BD;">
																								<div class="card-body pb-0 pt-0 text-white">
																									<div class="row">
																										<div class="col-md-12 p-1">
																											<h6 class="font-weight-bold align-middle m-1 ml-2 text-center" style="font-size: 13pt;">UNUSABLE SCRAP</h6>
																										</div>
																									</div>
																									<div class="row" style="background-color: #263238; min-height: 150px;">
																										<div class="col-md-12 p-1">
																											<span class="d-block mt-3 font-weight-bold" style="font-size: 30pt;">0</span>
																											<span class="d-block" style="font-size: 12pt;">KG</span>
																										</div>
																									</div>
																								</div>
																							</div>
																						</div>

																						<div class="col-md-12">
																							<div class="card" style="background-color: #0277BD;">
																								<div class="card-body pb-0 pt-0">
																									<div class="row">
																										<div class="col-md-12" style="margin-top:0px;">
																											<h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Out of Stock(s)</h5>
																										</div>
																									</div>
																									<div class="row" style="background-color: #ffffff; padding-top: 9px;min-height: 260px;max-height: 260px;overflow-y:auto;">
																										<div class="col-md-12 tbl_out_of_stock"></div>
																									</div>
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																				<div class="tab-pane" id="diff_tab">
																					<div class="row m-1">
																						<div class="col-md-10 p-3">
																							<h4 class="title m-0">DI - Diffuser</h4>
																							<canvas id="rawmaterial_diff_Chart" height="100"></canvas>
																						</div>
																						<div class="col-md-2 p-1" id="diffuser-scrap">
																							<div class="card" style="background-color: #0277BD;">
																								<div class="card-body pb-0 pt-0 text-white">
																									<div class="row">
																										<div class="col-md-12 p-1">
																											<h6 class="font-weight-bold align-middle m-1 ml-2 text-center" style="font-size: 13pt;">USABLE SCRAP</h6>
																										</div>
																									</div>
																									<div class="row" style="background-color: #263238; min-height: 150px;">
																										<div class="col-md-12 p-1">
																											<span class="d-block mt-3 font-weight-bold" style="font-size: 30pt;">0</span>
                                                      <span class="d-block" style="font-size: 12pt;">KG</span>
                                                      <span class="badge badge-info" style="font-size: 12pt;">KG</span>
																										</div>
																									</div>
																								</div>
																							</div>
																							<div class="card" style="background-color: #0277BD;">
																								<div class="card-body pb-0 pt-0 text-white">
																									<div class="row">
																										<div class="col-md-12 p-1">
																											<h6 class="font-weight-bold align-middle m-1 ml-2 text-center" style="font-size: 13pt;">UNUSABLE SCRAP</h6>
																										</div>
																									</div>
																									<div class="row" style="background-color: #263238; min-height: 150px;">
																										<div class="col-md-12 p-1">
																											<span class="d-block mt-3 font-weight-bold" style="font-size: 30pt;">0</span>
																											<span class="d-block" style="font-size: 12pt;">KG</span>
																										</div>
																									</div>
																								</div>
																							</div>
																						</div>
																						<div class="col-md-12">
																							<div class="card" style="background-color: #0277BD;">
																								<div class="card-body pb-0 pt-0">
																									<div class="row">
																										<div class="col-md-12" style="margin-top:0px;">
																											<h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Out of Stock(s)</h5>
																										</div>
																									</div>
																									<div class="row" style="background-color: #ffffff; padding-top: 9px;min-height: 260px;max-height: 260px;overflow-y:auto;">
																										<div class="col-md-12 tbl_out_of_stock"></div>
																									</div>
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																				<div class="tab-pane" id="scrap-tab">
																					<div class="row m-1">
																						<div class="col-md-12 p-1" style="min-height: 500px;">
																							<div id="fab-scrap-table" style="font-size:15px;"></div>
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
												<div class="tab-pane" id="mt-tab">
													<div class="row m-1">
														<div class="col-md-2 p-1">
															<div class="card" style="background-color: #0277BD;">
																<div class="card-body pb-0">
																	<div class="row">
																		<div class="col-md-12" style="margin-top: -10px;">
																			<h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Search Filter(s)</h5>
																		</div>
																	</div>
																	<div class="row" style="background-color: #ffffff; padding-top: 9px; height: 500px;">
																		<div class="col-md-12" style="margin: 0;height: 780px;" id="filter_material_transfer_request">
																			<span style="display: block; font-size: 9pt; margin-top: -8px;"></span>
																			<div class="form-group">
																				<label style="color: black;">Transaction Date:</label>
																				<input type="text" class="date form-control" name="daterange" autocomplete="off" placeholder="Select Date From" id="transfer_daterange" value="" style="text-align:center;isplay:inline-block;width:100%;height:30px;" onchange="tbl_log_fabrication()">
																			</div>
																			<div class="form-group" style="margin-top: -18px;">
																				<label style="color: black;">STE</label>
																				<select class="form-control text-center sel3 " name="item_code" id="transfer_stem">
																					<option value="" selected> Select STEM</option>
																					@foreach($ste_list as $row)
																					<option value="{{$row->name}}">{{$row->name}}</option>
																					@endforeach
																				</select>
																			</div>
																			<div class="form-group" style="margin-top: -5px;">
																				<label style="color: black;">Item Code</label>
																				<select class="form-control text-center sel3 " name="item_code" id="transfer_item_code">
																					<option value="" selected> Select Item Code</option>
																					@foreach($item_list as $row)
																					<option value="{{$row->name}}">{{$row->name}}</option>
																					@endforeach
																				</select>
																			</div>
																			<div class="form-group">
																				<label style="color: black;">Sales Order</label>
																				<select class="form-control text-center sel3 " name="prod" id="transfer_so">
																					<option value="" selected> Select Sales Order</option>
																					@foreach($so_list as $row)
																					<option value="{{$row->name}}">{{$row->name}}</option>
																					@endforeach
																				</select>
																			</div>
																			<div class="form-group">
																				<label style="color: black;">Customer</label>
																				<select class="form-control text-center sel3 " name="customer" id="transfer_customer">
																					<option value="" selected> Select Customer</option>
																					@foreach($customer as $row)
																					<option value="{{$row->customer_name}}">{{$row->customer_name}}</option>
																					@endforeach
																				</select>
																			</div>
																			<div class="form-group">
																				<label style="color: black;">Project</label>
																				<select class="form-control text-center sel3 " name="customer" id="transfer_project">
																					<option value="" selected> Select Project</option>
																					@foreach($project as $row)
																					<option value="{{$row->project}}">{{$row->project}}</option>
																					@endforeach
																				</select>
																			</div>
																			<div class="form-group">
																				<label style="color: black;">Status</label>
																				<select class="form-control text-center sel3 " name="qa_status" id="transfer_status">
																					<option value="" selected> Select Status</option>
																					<option value="For Checking">Pending</option>
																					<option value="Issued">Issued</option>
																				</select>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
														<div class="col-md-10 p-1">
															<div class="card" style="background-color: #0277BD;">
																<div class="card-body pb-0 pt-0">
																	<div class="row">
																		<div class="col-md-6 p-1">
																			<h5 class="text-white font-weight-bold align-middle m-1 ml-2">Material Transfer List</h5>
																		</div>
																		<div class="col-md-6 p-1 text-right">
																			<button class="btn btn-primary btn-material-transfer m-0">+ Material Transfer</button>
																		</div>
																	</div>
																	<div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
																		<div class="col-md-12" style="min-height: 930px;">
																			<div id="transfer_list" style="font-size:15px;"></div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="tab-pane" id="mr-tab">
													<div class="row m-1">
														<div class="col-md-2 p-1">
															<div class="card" style="background-color: #0277BD;" >
																<div class="card-body" style="padding-bottom: 0;">
																	<div class="row">
																		<div class="col-md-12" style="margin-top: -10px;">
																			<h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Search Filter(s)</h5>
																		</div>
																	</div>
                                  <form action="#" id="mr-request">
                                    <div class="row" id="filter_material_purchase_request" style="background-color: #ffffff; padding-top: 9px;">
                                      <div class="col-md-12" style="margin: 0;height: 780px;" id="filter_material_purchase_request">
								                        <span style="display: block; font-size: 9pt; margin-top: -8px;"></span>
								                        <div class="form-group">
								                          <label style="color: black;">Transaction Date:</label>
								                          <input type="text" class="date form-control" name="daterange" autocomplete="off" placeholder="Select Date From" id="purchase_daterange" value="" style="text-align:center;display:inline-block;width:100%;height:30px;" onchange="tbl_log_fabrication()">
								                        </div>
								                                                
								                        <div class="form-group" style="margin-top: -5px;">
								                          <label style="color: black;">Item Code</label>
								                            <select class="form-control text-center sel5 " name="item_code" id="purchase_item_code">
								                              <option value="" selected> Select Item Code</option>
								                                @foreach($item_list as $row)
								                                  <option value="{{$row->name}}">{{$row->name}}</option>
								                                @endforeach
								                            </select>
								                        </div>

								                        <div class="form-group">
								                          <label style="color: black;">Sales Order</label>
								                          <select class="form-control text-center sel5 " name="prod" id="purchase_so">
								                            <option value="" selected> Select Sales Order</option>
								                            @foreach($so_list as $row)
								                            <option value="{{$row->name}}">{{$row->name}}</option>
								                            @endforeach
								                          </select>
								                        </div>
								                        <div class="form-group">
								                          <label style="color: black;">Customer</label>
								                          <select class="form-control text-center sel5 " name="customer" id="purchase_customer">
								                            <option value="" selected> Select Customer</option>
								                            @foreach($customer as $row)
								                            <option value="{{$row->customer_name}}">{{$row->customer_name}}</option>
								                            @endforeach
								                          </select>
								                        </div>
								                        <div class="form-group">
								                          <label style="color: black;">Project</label>
								                          <select class="form-control text-center sel5 " name="customer" id="purchase_project">
								                            <option value="" selected> Select Project</option>
								                            @foreach($project as $row)
								                            <option value="{{$row->project}}">{{$row->project}}</option>
								                            @endforeach
								                          </select>
								                        </div>
								                                                   
								                                                   
								                        <div class="form-group">
								                          <label style="color: black;">Status</label>
								                          <select class="form-control text-center sel5 " name="qa_status" id="purchase_status">
								                            <option value="" selected> Select Status</option>
								                            @foreach($mreq_stat as $row)
								                            <option value="{{$row->status}}">{{$row->status}}</option>
								                            @endforeach
								                          </select>
								                        </div>
								                      </div>
																	  </div>
                                  </form>
																</div>
															</div>
														</div>
														<div class="col-md-10 p-1">
															<div class="card" style="background-color: #0277BD;">
															  	<div class="card-body pb-0 pt-0">
																 	<div class="row">
																		<div class="col-md-6 p-1">
																			<h5 class="text-white font-weight-bold align-middle m-1 ml-2">Material Request List</h5>
																		</div>
																		<div class="col-md-6 p-1 text-right">
																			<button class="btn btn-primary add-material-request-btn m-0" data-operation="Fabrication" id="add-material-request-btn">+ Material Request</button>
																		 </div>
																 	</div>
																 	<div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
																		<div class="col-md-12" style="min-height: 930px;">
																			<div id="tbl_material_request" style="font-size:15px;"></div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="tab-pane" id="withdrawal-tab">
													<div class="row m-1">
														<div class="col-md-2 p-1">
															<div class="card" style="background-color: #0277BD;" >
																<div class="card-body" style="padding-bottom: 0;">
																	<div class="row">
																		<div class="col-md-12" style="margin-top: -10px;">
																			<h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Search Filter(s)</h5>
																		</div>
																	</div>
																	<form action="#" id="withdrawal">
																		<div class="row" style="background-color: #ffffff; padding-top: 9px;">
																			<div class="col-md-12 m-0">
																				<span style="display: block; font-size: 9pt;">STEM No.</span>
																				<div class="form-group">
																					<input type="text" class="form-control inv-filters-text" name="ste_no" data-filter-content="withdrawal" style="border-radius: 0; padding: 7px; border: 1px solid #ABB2B9;">
																				</div>
																			</div>
																			<div class="col-md-12 m-0">
																				<span style="display: block; font-size: 9pt;">Production Order</span>
																				<div class="form-group">
																					<select class="form-control inv-filters" name="production_order" id="withdrawal-production-order" data-filter-content="withdrawal">
																						<option value="">Select Production Order</option>
																					</select>
																				</div>
																			</div>
																			<div class="col-md-12 m-0">
																				<span style="display: block; font-size: 9pt;">Customer</span>
																				<div class="form-group">
																					<select class="form-control inv-filters" name="customer" id="withdrawal-customer" data-filter-content="withdrawal">
																						<option value="">Select Customer</option>
																					</select>
																				</div>
																			</div>
																			<div class="col-md-12 m-0">
																				<span style="display: block; font-size: 9pt;">Source Warehouse</span>
																				<div class="form-group">
																					<select class="form-control inv-filters" name="source_warehouse" id="withdrawal-warehouse" data-filter-content="withdrawal">
																						<option value="">Select Source Warehouse</option>
																					</select>
																				</div>
																			</div>
																			<div class="col-md-12 m-0">
																				<span style="display: block; font-size: 9pt;">Status</span>
																				<div class="form-group">
																				<select class="form-control inv-filters" name="status" data-filter-content="withdrawal">
																					<option value="">Select Status</option>
																					<option value="0">Draft</option>
																					<option value="1">Submitted</option>
																					<option value="2">Cancelled</option>
																				</select>
																				</div>
																			</div>
																		</div>
																	</form>
																</div>
															</div>
														</div>
														<div class="col-md-10 p-1">
															<div class="card" style="background-color: #0277BD;">
															  	<div class="card-body pb-0 pt-0">
																 	<div class="row">
																		<div class="col-md-12 p-1">
																			<h5 class="text-white font-weight-bold align-middle m-1 ml-2">Withdrawal Slip List</h5>
																		</div>
																 	</div>
																 	<div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
																		<div class="col-md-12" style="min-height: 930px;">
																			<div id="withdrawal-slips-table" style="font-size:15px;"></div>
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
								<div class="tab-pane" id="assembly-tab">
									Assembly
								</div>
                <div class="tab-pane" id="painting-tab">
									<div class="row m-1">
										<div class="col-md-12 pl-2 pr-2">
											<div class="nav-tabs-navigation mt-1">
												<div class="nav-tabs-wrapper m-0">
													<ul class="nav nav-tabs p-0" data-tabs="tabs">
														<li class="nav-item">
															<a class="nav-link active" href="#painting-stock-list-tab" data-toggle="tab">Inventory List</a>
														</li>
                            <li class="nav-item">
															<a class="nav-link" href="#painting-material-request-tab" data-toggle="tab">Material Request</a>
														</li>
														
													</ul>
												</div>
											</div>
											<div class="tab-content">
                        <div class="tab-pane active" id="painting-stock-list-tab">
													<div class="row m-1">
														<div class="col-md-2">
															<div class="row">
                                <div class="col-md-12 p-1" id="painting-filters-div">
																	<div class="card" style="background-color: #0277BD;">
																		<div class="card-body pb-0">
																			<div class="row">
																				<div class="col-md-12" style="margin-top: -10px;">
																					<h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Search Filter(s)</h5>
																				</div>
																			</div>
																			<form action="#" id="inventory_painting">
																				<div class="row" style="background-color: #ffffff; padding-top: 9px;">
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Item Code</span>
																						<div class="form-group">
																							<select class="form-control painting_stock_filter" data-filter-content="stock-list-painting" name="filters[]" id="itemcode_painting_filter">
																								<option value="">Select Item Code</option>
																							</select>
																						</div>
																					</div>
																				</div>
																			</form>
																		</div>
																	</div>
																</div>
                                <div class="col-md-12 p-1" id="trans-painting-filters-div">
																	<div class="card" style="background-color: #0277BD;">
																		<div class="card-body pb-0">
																			<div class="row">
																				<div class="col-md-12" style="margin-top: -10px;">
																					<h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Search Filter(s)</h5>
																				</div>
																			</div>
																			<form action="#" id="trans_inventory_painting">
																				<div class="row" style="background-color: #ffffff; padding-top: 9px;">
																					<div class="col-md-12 m-0">
																						<span style="display: block; font-size: 9pt;">Entry Type</span>
																						<div class="form-group">
																							<select class="form-control trans_painting_stock_filter" data-filter-content="stock-list-transaction-painting" name="entry_type">
																								<option value="">Select Entry Type</option>
                                                <option value="New Entry">New Entry</option>
                                                <option value="Stock Adjustment">Stock Adjustment</option>
																							</select>
																						</div>
																					</div>
																				</div>
																			</form>
																		</div>
																	</div>
																</div>
                                  <div class="col-md-12 p-1" id="consumed-filter-div">
                                    <div class="card" style="background-color: #0277BD;" >
                                      <div class="card-body pb-0">
                                        <div class="row">
                                          <div class="col-md-12" style="margin-top: -10px;">
                                            <h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Search Filter(s)</h5>
                                          </div>
                                        </div>
                                        <form action="#" id="consumed_list_powder">
                                          <div class="row" style="background-color: #ffffff; padding-top: 9px;">
                                            <div class="col-md-12 m-0">
                                              <span style="display: block; font-size: 9pt;">Transaction date</span>
                                              <div class="form-group">
                                              <input type="text" class="date form-control" data-filter-content="powderconsume-list-painting" name="daterange" autocomplete="off" placeholder="Select Date From" id="consumed_daterange" value="" style="text-align:center;display:inline-block;width:100%;height:30px;">

                                              </div>
                                            </div>
                                            <div class="col-md-12 m-0">
                                              <span style="display: block; font-size: 9pt;">Item Code</span>
                                              <div class="form-group">
                                                <select class="form-control inv-filters-painting" name="filters[]" id="itemcode_consume" data-filter-content="powderconsume-list-painting">
                                                  <option value="">Select Item Code</option>
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-12 m-0">
                                              <span style="display: block; font-size: 9pt;">Operator</span>
                                              <div class="form-group">
                                                <select class="form-control inv-filters-painting" flter="operator[]" id="operator_consume" data-filter-content="powderconsume-list-painting">
                                                  <option value="">Select Operator</option>
                                                </select>
                                              </div>
                                            </div>
                                          </div>
                                        </form>
                                      </div>
                                    </div>
                                  </div>
															</div>
														</div>
														<div class="col-md-10 p-1">
															<div class="card" style="background-color: #0277BD;">
															  <div class="card-body pb-0 pt-0">
																 	<div class="row">
																		<div class="col-md-12 p-1">
																			<h5 class="text-white font-weight-bold align-middle m-1 ml-2">Painting Inventory</h5>
																		</div>
																 	</div>
																 	<div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
																 		<div class="col-md-12 pl-2 pr-2">
																 			<div class="nav-tabs-navigation mt-1">
																				<div class="nav-tabs-wrapper m-0">
																					<ul class="nav nav-tabs p-0" data-tabs="tabs" id="inventory-nav-tabs">
																						<li class="nav-item active_painting_li" data-id="stock">
																							<a class="nav-link active" href="#painting_stock_list" data-toggle="tab" data-painting="stock-list-painting">Stock List</a>
																						</li>
                                            <li class="nav-item active_painting_li" data-id="trans">
																							<a class="nav-link" href="#painting_stock_list_history" data-toggle="tab" data-painting="stock-list-transaction-painting">Transaction History</a>
																						</li>
																						<li class="nav-item active_painting_li" data-id="stock">
																							<a class="nav-link" href="#list_available_stock" data-toggle="tab" data-painting="powder-list-painting">Powder Coat Stock</a>
																						</li>
                                            <li class="nav-item active_painting_li" data-id="consume">
																							<a class="nav-link" href="#consumed_list" data-toggle="tab" data-painting="powderconsume-list-painting">Powder Coat Consumed History</a>
																						</li>
																					</ul>
																				</div>
																			</div>
																			<div class="tab-content">
                                        <div class="tab-pane active" id="painting_stock_list">
																					<div class="row m-1">
																						<div class="col-md-6 offset-md-6 p-1 text-right" style="margin-top: -93px;">
																							<button class="btn btn-primary btn-stock-adjust-entry-painting m-0">+ Stock Adjustment Entry</button>
																						</div>
																						<div class="col-md-12 p-0" style="min-height: 600px;">
                                              <div id="tbl_stock_painting"></div>
																						</div>
																					</div>
																				</div>
                                        <div class="tab-pane" id="painting_stock_list_history">
																					<div class="row m-1">
																						<div class="col-md-6 offset-md-6 p-1 text-right" style="margin-top: -93px;">
																							<button class="btn btn-primary btn-stock-adjust-entry-painting m-0">+ Stock Adjustment Entry</button>
																						</div>
																						<div class="col-md-12 p-0" style="min-height: 600px;">
                                              <div id="tbl_painting_stock_list_history"></div>
																						</div>
																					</div>
																				</div>
																				<div class="tab-pane" id="list_available_stock">
																					<div class="row m-1">
																						<div class="col-md-12 p-0 text-center" style="min-height: 600px;">
																							<div id="fab-inventory-tablee" style="font-size:15px;">
                                              <h4 class="title m-0">PA - Paint (Powder Coat)</h4>
																							<canvas id="powder_coat_Chart" height="100"></canvas></div>
																						</div>
																					</div>
																				</div>
																				<div class="tab-pane" id="consumed_list">
																					<div class="row m-1">
																						<div class="col-md-12 p-0" style="min-height: 600px;">
                                              <div id="tbl_powder_consumed"></div>
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
                        <div class="tab-pane" id="painting-material-request-tab">
													<div class="row m-1">
														<div class="col-md-2 p-1">
															<div class="card" style="background-color: #0277BD;" >
																<div class="card-body" style="padding-bottom: 0;">
																	<div class="row">
																		<div class="col-md-12" style="margin-top: -10px;">
																			<h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Search Filter(s)</h5>
																		</div>
																	</div>
																	<form action="#" id="painting-mr-request">
                                    <div class="row"  id="filter_mr_request_painting" style="background-color: #ffffff; padding-top: 9px;">
                                      <div class="col-md-12" style="margin: 0;height: 780px;">
								                        <span style="display: block; font-size: 9pt; margin-top: -8px;"></span>
								                        <div class="form-group">
								                          <label style="color: black;">Transaction Date:</label>
								                          <input type="text" class="date form-control" name="daterange" autocomplete="off" placeholder="Select Date From" id="purchase_daterange_painting" value="" style="text-align:center;display:inline-block;width:100%;height:30px;" onchange="tbl_log_fabrication()">
								                        </div>
								                                                
								                        <div class="form-group" style="margin-top: -5px;">
								                          <label style="color: black;">Item Code</label>
								                            <select class="form-control text-center mr_request_paint " name="item_code" id="purchase_item_code_painting">
								                              <option value="" selected> Select Item Code</option>
								                                @foreach($item_list as $row)
								                                  <option value="{{$row->name}}">{{$row->name}}</option>
								                                @endforeach
								                            </select>
								                        </div>

								                        <div class="form-group">
								                          <label style="color: black;">Sales Order</label>
								                          <select class="form-control text-center mr_request_paint " name="sales_order" id="purchase_so_painting">
								                            <option value="" selected> Select Sales Order</option>
								                            @foreach($so_list as $row)
								                            <option value="{{$row->name}}">{{$row->name}}</option>
								                            @endforeach
								                          </select>
								                        </div>
								                        <div class="form-group">
								                          <label style="color: black;">Customer</label>
								                          <select class="form-control text-center mr_request_paint " name="customer" id="purchase_customer_painting">
								                            <option value="" selected> Select Customer</option>
								                            @foreach($customer as $row)
								                            <option value="{{$row->customer_name}}">{{$row->customer_name}}</option>
								                            @endforeach
								                          </select>
								                        </div>
								                        <div class="form-group">
								                          <label style="color: black;">Project</label>
								                          <select class="form-control text-center mr_request_paint " name="project" id="purchase_project_painting">
								                            <option value="" selected> Select Project</option>
								                            @foreach($project as $row)
								                            <option value="{{$row->project}}">{{$row->project}}</option>
								                            @endforeach
								                          </select>
								                        </div>
								                                                   
								                                                   
								                        <div class="form-group">
								                          <label style="color: black;">Status</label>
								                          <select class="form-control text-center mr_request_paint " name="status" id="purchase_status_painting">
								                            <option value="" selected> Select Status</option>
								                            @foreach($mreq_stat as $row)
								                            <option value="{{$row->status}}">{{$row->status}}</option>
								                            @endforeach
								                          </select>
								                        </div>
								                      </div>
																	  </div>
                                  </form>
																</div>
															</div>
														</div>
														<div class="col-md-10 p-1">
															<div class="card" style="background-color: #0277BD;">
															  	<div class="card-body pb-0 pt-0">
																 	<div class="row">
																		<div class="col-md-6 p-1">
																			<h5 class="text-white font-weight-bold align-middle m-1 ml-2">Material Request List</h5>
																		</div>
																		<div class="col-md-6 p-1 text-right">
																			<button class="btn btn-primary add-material-request-btn m-0" data-operation="Painting" id="add-material-request-btn">+ Material Request</button>
																		 </div>
																 	</div>
																 	<div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
																		<div class="col-md-12" style="min-height: 930px;">
																			<div id="tbl_material_request_painting" style="font-size:15px;"></div>
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

<!-- Modal -->
<div class="modal fade" id="add-stock-entries-adjustment" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/submit/stock_entries_adjustment" method="POST" id="add-stock-entries-adjustment-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header" style="background-color: #0277BD;color:white;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Stock Adjustment Entry<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                  <label>Item Code:</label>
                    <select class="form-control select_stock_adjust" id="itemcode_line" name="item_code"  style="background-color: white;font-size: 11pt;display:inline-block;" onchange="get_balanceqty()">
                    </select>
                      <div id="item_desc_div">
                        <label style="padding-top:10px;">Item Description:</label>
                        <br>
                        <label id="item_description_label"></label>
                        <input type="hidden" name="item_description_input" id="item_description_input">
                      </div>
                  </div>
               </div>
               <div class="row" style="padding-top:10px;">
                  <div class="col-md-6">
                    <label>Balance QTY:</label>
                  </div>
                  <div class="col-md-6 text-center" id="entry_type_div">
                    <b><label id="entry_type_label" style="font-weight:bold;font-size:20px;"></label></b>
                    <input type="hidden" id="entry_type_box" name="entry_type_box">
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group" style="padding-top:10px;">
                        <input type="hidden" name="orig_balance_qty" id="orig_balance_qty">
                        <input type="text" class="form-control form-control-lg balance_qty_id" name="balance_qty" id="balance_qty_id" value="0" required>
                     </div>
                </div>
                  </div>
                
               <div class="row">
                    <div class="col-md-6 text-center" style="text-center:center;">
                        <div class="form-group" style="padding-top:10px;">
                            <label>Planned QTY:</label><b><label style="padding-left:15px; font-size:18px;" id="planned_qty_id" style="font-weight: bold;">0</label></b>
                        </div>                      
                    </div>
                    <div class="col-md-6 text-center" style="text-center:center;">
                        <div class="form-group" style="padding-top:10px;">
                            <label>In Progress QTY:</label><b><label style="padding-left:15px; font-size:18px;" id="actual_qty_id" style="font-weight: bold;">0</label></b>  
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
<div class="modal fade" id="add-stock-entries-adjustment-painting" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/submit/stock_entries_adjustment/painting" method="POST" id="add-stock-entries-adjustment-painting-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header" style="background-color: #0277BD;color:white;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Stock Adjustment Entry<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                  <label>Item Code:</label>
                    <select class="form-control select_stock_adjust_painting" id="itemcode_line_painting" name="item_code"  style="background-color: white;font-size: 11pt;display:inline-block;" onchange="get_balanceqty_painting()">
                    </select>
                      <div id="item_desc_div_painting">
                        <label style="padding-top:10px;">Item Description:</label>
                        <br>
                        <label id="item_description_label_painting"></label>
                        <input type="hidden" name="item_description_input" id="item_description_input_painting">
                      </div>
                  </div>
               </div>
               <div class="row" style="padding-top:10px;">
                  <div class="col-md-6">
                    <label>Balance QTY:</label>
                  </div>
                  <div class="col-md-6 text-center" id="entry_type_div_painting">
                    <b><label id="entry_type_label_painting" style="font-weight:bold;font-size:20px;"></label></b>
                    <input type="hidden" id="entry_type_box_painting" name="entry_type_box">
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group" style="padding-top:10px;">
                        <input type="hidden" name="orig_balance_qty" id="orig_balance_qty_painting">
                        <input type="text" class="form-control form-control-lg balance_qty_id" name="balance_qty" id="balance_qty_id_painting" value="0" required>
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
<!-- Modal Manual Create Production Prder -->
<div class="modal fade" id="add-material-transfer-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="min-width: 60%;">
    <form action="/save_material_transfer" method="post" autocomplete="off" id="add-material-transfer-frm">
      @csrf
      <input type="hidden" name="operation" value="Fabrication">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #0277BD;color:white;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Material Transfer<br>
               </h5>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Reference</label>
                    <select class="form-control 2" id="reference_transfer" name="reference_transfer" required>
                      <option value="">Select Reference</option>
                      <option value="Sales Order">Sales Order</option>
                      <option value="Material Request">Material Request</option>
                    </select>
                  </div>
                </div>
                
                <div class="col-md-12" style="display: none;" id="input-so-div-tranfer">
                  <div class="form-group">
                    <label>Sales Order</label>
                    <select class="form-control sel-reference-no-transfer sel-so sel2" id="sales_order_transfer" name="sales_order" data-type="SO" required>
                      <option value="">Select SO</option>
                      @forelse($so_list as $so)
                      <option value="{{ $so->name }}">{{ $so->name }}</option>
                      @empty
                      <option value="">No Sales Order(s) Found.</option>
                      @endforelse
                    </select>
                  </div>
                </div>
                 <div class="col-md-12" style="display: none;" id="input-mreq-div-tranfer">
                  <div class="form-group">
                    <label>MREQ</label>
                    <select class="form-control sel-reference-no-transfer sel-mreq sel2" id="material_request_transfer" name="material_request" data-type="MREQ">
                      <option value="">Select MREQ</option>
                      @forelse($mreq_list_transfer as $mreq)
                      <option value="{{ $mreq->name }}">{{ $mreq->name }}</option>
                      @empty
                      <option value="">No MREQ Found.</option>
                      @endforelse
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Source Warehouse</label>
                    <select class="form-control sel2" id="s_warehouse" name="s_warehouse" required>
                      <option value="">Select Source Warehouse</option>
                      @forelse($warehouse_list as $row)
                      <option value="{{ $row }}">{{ $row }}</option>
                      @empty
                      <option value="">No Warehouse Found.</option>
                      @endforelse
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Target Warehouse</label>
                    <select class="form-control sel2" id="t_warehouse" name="t_warehouse"  required>
                      <option value="">Select Target Warehouse</option>
                      @forelse($warehouse_list as $row)
                      <option value="{{ $row }}">{{ $row }}</option>
                      @empty
                      <option value="">No Warehouse Found.</option>
                      @endforelse
                    </select>
                  </div>
                </div>
                
            </div>
          </div>
          <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Project</label>
                    <input type="text" name="project" class="form-control readonly">
                  </div>
                </div>
                <div class="col-md-12" style="margin-top: -6px;">
                  <div class="form-group">
                    <label>Customer</label>
                    <input type="text" name="customer" class="form-control readonly" required>
                  </div>
                </div>
            </div>
          </div>
          <div class="col-md-12">
                  <a href="#" class="btn btn-primary add-row">
                    <i class="now-ui-icons ui-1_simple-add"></i>Add
                  </a>
                  <table class="table" id="material-transfer-table" style="font-size: 10px;border: 1px solid #ABB2B9;">
                     <thead style="border: 1px solid #ABB2B9;">
                        <tr style="border: 1px solid #ABB2B9;">
                           <th style="border: 1px solid #ABB2B9;width: 5%; text-align: center;font-weight: bold;">No.</th>
                           <th style="border: 1px solid #ABB2B9;width: 18%; text-align: center;font-weight: bold;">Source Warehouse</th>
                           <th style="border: 1px solid #ABB2B9;width: 18%; text-align: center;font-weight: bold;">Target Warehouse</th>
                           <th style="border: 1px solid #ABB2B9;width: 18%; text-align: center;font-weight: bold;">Item Code</th>
                           <th style="border: 1px solid #ABB2B9;width: 18%; text-align: center;font-weight: bold;">QTY</th>
                           <th style="border: 1px solid #ABB2B9;width: 18%; text-align: center;font-weight: bold;">Stock UOM</th>
                           <th style="border: 1px solid #ABB2B9;width: 5%; text-align: center;font-weight: bold;"></th>
                        </tr>
                     </thead>
                     <tbody class="table-body text-center" style="border: 1px solid #ABB2B9;">
                       
                         
                     </tbody>
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
<div class="modal fade" id="view-material-transfer-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="min-width: 60%;">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #0277BD;color:white;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i>Material Transfer Details<br>
               </h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <div class="modal-body">
          <div id="tbl_view_transfer_details" class="table-responsive"></div>
        </div>
      </div>
  </div>
</div>
<div class="modal fade" id="cancel-material-transfer-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 30%;">
      <form action="/cancel_material_transfer" method="POST" id="cancel-material-transfer-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">
                <span>Cancel Material Transfer</span>
                <span class="sampling-delete-text" style="font-weight: bolder;"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <div class="form-group" style="font-size: 12pt;">
                        <div class="tbl_view_transfer_details"> <span>Are you sure you want to cancel </span><span id="transfer-label-cancel" style="font-weight: bold; padding-left: 2px;"></span>?</div>
                        <input type="hidden" name="transfer_id" id="transfer_id">
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
<div class="modal fade" id="confirmed-material-transfer-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 30%;">
      <form action="/confirmed_material_transfer" method="POST" id="confirmed-material-transfer-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">
                <span>Submit Material Transfer</span>
                <span class="sampling-delete-text" style="font-weight: bolder;"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <div class="form-group" style="font-size: 12pt;">
                        <div class="tbl_view_transfer_details"> <span>Submit Material Transfer </span><span id="transfer-label-confirm" style="font-weight: bold; padding-left: 2px;"></span>?</div>
                        <input type="hidden" name="transfer_id_confirm" id="transfer_id_confirm">
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
<div class="modal fade" id="delete-material-transfer-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 30%;">
      <form action="/delete_material_transfer" method="POST" id="delete-material-transfer-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">
                <span>Delete Material Transfer</span>
                <span class="sampling-delete-text" style="font-weight: bolder;"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <div class="form-group" style="font-size: 12pt;">
                        <div class="tbl_view_transfer_details"> <span>Are you sure you want to delete material transfer </span><span id="transfer-label-delete" style="font-weight: bold; padding-left: 2px;"></span>?</div>
                        <input type="hidden" name="transfer_id_delete" id="transfer_id_delete">
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
<!-- Modal Manual Create Production Prder -->
<div class="modal fade" id="add-material-request-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="min-width: 60%;">
    <form action="/save_material_purchase" method="post" autocomplete="off" id="add-material-request-frm">
      @csrf
      <input type="hidden" name="operation" id="material-request-operation">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #0277BD;color:white;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Material Request for Purchase<br>
               </h5>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Purchase Request</label>
                    <select class="form-control sel4" id="purchase_type" name="purchase_type">
                      <option value="Local">Local</option>
                      <option value="Imported">Imported</option>
                    </select>
                  </div>
                </div>
                
                <div class="col-md-6" id="input-so-div">
                  <div class="form-group">
                    <label>Sales Order</label>
                    <select class="form-control sel-reference-no sel-so sel4" id="sel-so" name="sales_order" data-type="SO" required>
                      <option value="">Select SO</option>
                      @forelse($so_list as $so)
                      <option value="{{ $so->name }}">{{ $so->name }}</option>
                      @empty
                      <option value="">No Sales Order(s) Found.</option>
                      @endforelse
                    </select>
                  </div>
                </div>
                <div class="col-md-12" style="margin-top: -6px;">
                  <div class="form-group">
                    <label>Customer</label>
                    <input type="text" name="customer" class="form-control readonly">
                  </div>
                </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-12">
                  <div class="form-group">
                    <label>Required Date</label>
                    <input type="input" name="required_date_all" id="required_date_all" class="form-control date-picker" required>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <label>Project</label>
                    <input type="text" name="project" class="form-control readonly">
                  </div>
                </div>
            </div>
          </div>
          <div class="col-md-12">
                  <a href="#" class="btn btn-primary add-row">
                    <i class="now-ui-icons ui-1_simple-add"></i>Add
                  </a>
                  <table class="table" id="material-purchase-table" style="font-size: 10px;border: 1px solid #ABB2B9;">
                     <thead style="border: 1px solid #ABB2B9;">
                        <tr style="border: 1px solid #ABB2B9;">
                           <th style="border: 1px solid #ABB2B9;width: 5%; text-align: center;font-weight: bold;">No.</th>
                           <th style="border: 1px solid #ABB2B9;width: 18%; text-align: center;font-weight: bold;">Item Code</th>
                           <th style="border: 1px solid #ABB2B9;width: 18%; text-align: center;font-weight: bold;">Quantity</th>
                           <th style="border: 1px solid #ABB2B9;width: 18%; text-align: center;font-weight: bold;">Stock UOM</th>
                           <th style="border: 1px solid #ABB2B9;width: 18%; text-align: center;font-weight: bold;">For Warehouse</th>
                           <th style="border: 1px solid #ABB2B9;width: 18%; text-align: center;font-weight: bold;">Required Date</th>
                           <th style="border: 1px solid #ABB2B9;width: 5%; text-align: center;font-weight: bold;"></th>
                        </tr>
                     </thead>
                     <tbody class="table-body text-center" style="border: 1px solid #ABB2B9;">
                        <tr>
                           <td>1</td>
                           <td>
                                <select class="form-control sel4" name="new_item_code[]" required>
                                  <option value="">Select Item Code</option>
                                  @forelse($item_list as $item)
                                  <option value="{{ $item->name }}">{{ $item->name }}</option>
                                  @empty
                                  <option value="">No Item(s) Found.</option>
                                  @endforelse
                                </select>

                           </td>
                           <td>
                                <input type="text" name="qty[]" class="form-control input-multi" required>
                           </td>
                           <td>
                                <input type="text" name="uom" class="form-control input-multi readonly" id="">
                           </td>
                           
                           <td>
                              <select class="form-control sel4" name="new_warehouse[]" required>
                                @forelse($warehouse_list as $warehouse)
                                <option value="{{ $warehouse }}">{{ $warehouse }}</option>
                                @empty
                                <option value="">No Warehouse(s) Found.</option>
                                @endforelse
                              </select>
                           </td>
                           <td>
                              <input type="input" name="required_date[]" class="form-control date-picker input-multi input-date" required>

                           </td>
                           <td><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>
                        </tr>
                     </tbody>
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

<div class="modal fade" id="view-material-request-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="min-width: 60%;">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #0277BD;color:white;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i>Material Request Details<br>
               </h5>
            </div>
        <div class="modal-body">
          <div id="tbl_view_purchase_details" class="table-responsive"></div>
        </div>
      </div>
  </div>
</div>
<div class="modal fade" id="cancel-material-request-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 30%;">
      <form action="/cancel_material_purchase_request" method="POST" id="cancel-material-request-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">
                <span>Cancel Material Request</span>
                <span class="sampling-delete-text" style="font-weight: bolder;"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <div class="form-group" style="font-size: 12pt;">
                        <div class="tbl_view_purchase_details"> <span>Are you sure you want to cancel </span><span id="purchase-label-cancel" style="font-weight: bold; padding-left: 2px;"></span>?</div>
                        <input type="hidden" name="purchase_id" id="purchase_id">
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

<style>
	.select2.select2-container {
		width: 100% !important;
	}
	 .input-multi{
    height: 28px;
    font-size: 12px;
    text-align: center;
    margin-top: 10px;
  }
    #add-material-transfer-modal .form-control {
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }
  #filter_material_transfer_request .form-control {
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }
  #add-material-request-modal .form-control {
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }
  #filter_material_purchase_request .form-control {
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }
  #painting-mr-request .form-control {
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }
  #add-material-request-modal .form-control:hover, #add-material-request-modal .form-control:focus, #add-material-request-modal .form-control:active {
    box-shadow: none;
  }
  #add-material-request-modal .form-control:focus {
    border: 1px solid #34495e;
  }
</style>


<div class="modal fade" id="add-scrap-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
	   <form action="/add_scrap/fabrication" method="POST" autocomplete="off">
		  @csrf
		  <div class="modal-content">
			 <div class="modal-header text-white" style="background-color: #0277BD;">
				<h5 class="modal-title" id="modal-title ">
				   <i class="now-ui-icons"></i> Add Scrap
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			 </div>
			 <div class="modal-body">
				 <div class="row">
					 <div class="col-md-12">
            <div class="form-group">
							<label for="add-scrap-type">Scrap Type</label>
							<select name="scrap_type" class="form-control form-control-lg" id="scrap-type-sel" required>
								<option value="Usable">Usable</option>
								<option value="Unusable">Unusable</option>
							</select>
						</div>
						 <div class="form-group">
							 <label for="add-scrap-material">Material</label>
							 <select name="material" class="form-control form-control-lg" required>
                <option value=""></option>
                <option value="CRS">CRS</option>
                <option value="HRS">HRS</option>
                <option value="Aluminum">Aluminum</option>
              </select>
						 </div>
						 <div class="form-group">
							<label for="add-scrap-length">Length</label>
							<input type="text" name="length" class="form-control form-control-lg" required>
						</div>
						<div class="form-group">
							<label for="add-scrap-width">Width</label>
							<input type="text" name="width" class="form-control form-control-lg" required>
						</div>
						<div class="form-group">
							<label for="add-scrap-thickness">Thickness</label>
							<input type="text" name="thickness" class="form-control form-control-lg" required>
						</div>
						<div class="form-group">
							<label for="add-scrap-qty">Quantity (in Cubic MM)</label>
							<input type="text" name="qty" class="form-control form-control-lg" required>
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
@endsection

@section('script')

<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<script src="{{ asset('js/charts/Chart.min.js') }}"></script>
<script src="{{ asset('js/charts/utils.js') }}"></script>
<script src="{{ asset('js/charts/chartjs-plugin-datalabels.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('js/daterange/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/daterange/daterangepicker.css') }}" />

<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
<script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>
<script>
$(document).ready(function(){
  $('#scrap-type-sel').change(function(e){
    e.preventDefault();
    if($(this).val() == 'Usable'){
      $('#add-scrap-modal .form-group').eq(2).show();
      $('#add-scrap-modal .form-group').eq(3).show();
      $('#add-scrap-modal .form-control').eq(2).attr('required', true);
      $('#add-scrap-modal .form-control').eq(3).attr('required', true);
      $('#add-scrap-modal label').eq(5).text('Quantity (in Cubic MM)');
    }else{
      $('#add-scrap-modal .form-group').eq(2).hide();
      $('#add-scrap-modal .form-group').eq(3).hide();
      $('#add-scrap-modal .form-control').eq(2).removeAttr('required');
      $('#add-scrap-modal .form-control').eq(3).removeAttr('required');
      $('#add-scrap-modal label').eq(5).text('Quantity (in KG)');
    }
  });


	$('#transaction-filter-div').hide();
	$('#inventory-nav-tabs .nav-link').click(function(){
		var is_active = $(this).hasClass('active');
		if(!is_active){
			$('#transaction-filter-div').toggle();
			$('#invetory-filters-div').toggle();
		}
	});

	setInterval(get_scrap_per_material('crs'), 10000);
	setInterval(get_scrap_per_material('aluminum'), 10000);
	setInterval(get_scrap_per_material('diffuser'), 10000);
	function get_scrap_per_material(material_type){
		$.ajax({
			url:"/get_scrap_per_material/" + material_type,
			type:"GET",
			success:function(data){
        $('#' + material_type + "-scrap span").eq(0).text(data.usable_scrap_in_kg);
        $('#' + material_type + "-scrap span").eq(2).text(data.usable_scrap_in_cubic_mm + ' Cubic MM');
				$('#' + material_type + "-scrap span").eq(3).text(data.unusable_scrap);
			}
		}); 
	}

	setInterval(get_pending_inventory_transactions, 10000);
    function get_pending_inventory_transactions(){
      $.ajax({
        url:"/get_pending_inventory_transactions",
        type:"GET",
        success:function(data){
        	$('#fabrication-pending-inv-transaction span').eq(0).text(data.material_transfer);
        	$('#fabrication-pending-inv-transaction span').eq(1).text(data.material_transfer_items + ' Item(s)');
        	$('#fabrication-pending-inv-transaction span').eq(2).text(data.material_request);
        	$('#fabrication-pending-inv-transaction span').eq(3).text(data.material_request_items + ' Item(s)');
        	$('#fabrication-pending-inv-transaction span').eq(4).text(data.pending_issue);
        	$('#fabrication-pending-inv-transaction span').eq(5).text(data.pending_issue_items + ' Item(s)');
        	$('#fabrication-pending-inv-transaction span').eq(6).text(data.pending_receive);
        	$('#fabrication-pending-inv-transaction span').eq(7).text(data.pending_receive_items + ' Item(s)');
        }
      }); 
    }


  $('#scrap-filter-div').hide();
	$('.active_li').click(function(){
		// var is_active = $(this).hasClass('active');
    var id = $(this).data('id');

    // alert(id);
		if(id != "scrap"){
      $('#raw-filters-div').show();
      $('#scrap-filter-div').hide();
		}else{
      $('#raw-filters-div').hide();
      $('#scrap-filter-div').show();

    }
	});

  $('#trans-painting-filters-div').hide();
  $('#consumed-filter-div').hide();
	$('.active_painting_li').click(function(){
		// var is_active = $(this).hasClass('active');
    var id = $(this).data('id');

    // alert(id);
		if(id == "stock"){
      $('#painting-filters-div').show();
      $('#consumed-filter-div').hide();
      $('#trans-painting-filters-div').hide();
    }else if(id == "trans"){
      $('#trans-painting-filters-div').show();
      $('#painting-filters-div').hide();
      $('#consumed-filter-div').hide();
    
		}else{
      $('#painting-filters-div').hide();
      $('#trans-painting-filters-div').hide();
      $('#consumed-filter-div').show();

    }
	});
	$('#add-scrap-btn').click(function(e){
		e.preventDefault();
		$('#add-scrap-modal').modal('show');
	});

	$('#add-scrap-modal form').submit(function(e){
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
			  $('#add-scrap-modal').modal('hide');
			  get_scrap('Fabrication');
			}
		  }
		});
	  });

		$(document).on('click', '#tbl-scrap-pagination a', function(event){
			event.preventDefault();
			var page = $(this).attr('href').split('page=')[1];
			get_scrap('Fabrication', page);
			 });
			 
		$(document).on('click', '#tbl_stockadjustment_entry_pagination a', function(event){
			event.preventDefault();
			var page = $(this).attr('href').split('page=')[1];
			get_inventory_list('Fabrication', page);
		});
	
		$(document).on('click', '#tbl_fabrication_inventory_history_pagination a', function(event){
			event.preventDefault();
			var page = $(this).attr('href').split('page=')[1];
			get_transaction_list('Fabrication', page);
		});
	
		$(document).on('click', '#tbl-withdrawal-pagination a', function(event){
			event.preventDefault();
			var page = $(this).attr('href').split('page=')[1];
			get_withdrawal_slips('Fabrication', page);
		});
	rawmaterial_diff_Chart();
  rawmaterial_crs_Chart();
  rawmaterial_alum_Chart();
  out_of_stock();
	get_inventory_list('Fabrication');
  tbl_painting_material_request_list("Painting");

	function get_inventory_list(operation, page, filters){
		$.ajax({
			url:"/get_inventory_list/" + operation + "?page=" + page,
			type:"GET",
			data: filters,
			success:function(data){
				$('#fab-inventory-table').html(data);
			}
		});
	}
	
	get_transaction_list('Fabrication');
	function get_transaction_list(operation, page, filters){
		$.ajax({
			url:"/get_transaction_history/" + operation + "?page=" + page,
			type:"GET",
			data: filters,
			success:function(data){
				$('#fab-inv-transaction-table').html(data);
			}
		});
	}

	get_scrap('Fabrication');
	function get_scrap(operation, filters, page){
		$.ajax({
			url:"/get_scrap_inventory/" + operation + "?page=" + page,
			type:"GET",
			data: filters,
			success:function(data){
				$('#fab-scrap-table').html(data);
			}
		});
	}

	get_withdrawal_slips('Fabrication');
	function get_withdrawal_slips(operation, page, filters){
		$.ajax({
			url:"/get_withdrawal_slips/" + operation + "?page=" + page,
			type:"GET",
			data: filters,
			success:function(data){
				$('#withdrawal-slips-table').html(data);
			}
		});
	}

	get_scrap_filters('Fabrication');
	function get_scrap_filters(operation){
		$.ajax({
			url:"/get_scrap_filters/" + operation,
			type:"GET",
			success:function(data){
				var m = '';
				$.each(data.material, function(i, d){
					m += '<option value="' + d + '">' + d + '</option>';
				});

				var l = '';
				$.each(data.length, function(i, d){
					l += '<option value="' + d + '">' + d + '</option>';
				});

				var w = '';
				$.each(data.width, function(i, d){
					w += '<option value="' + d + '">' + d + '</option>';
				});

				var t = '';
				$.each(data.thickness, function(i, d){
					t += '<option value="' + d + '">' + d + '</option>';
				});

				$('#scrap-material').append(m);
				$('#scrap-length').append(l);
				$('#scrap-width').append(w);
				$('#scrap-thickness').append(t);
			}
		});
	}

	get_inventory_filters('Fabrication');
	function get_inventory_filters(operation){
		$.ajax({
			url:"/get_inventory_filters/" + operation,
			type:"GET",
			success:function(data){
				var m = '';
				$.each(data.material, function(i, d){
					m += '<option value="' + d + '">' + d + '</option>';
				});

				var l = '';
				$.each(data.length, function(i, d){
					l += '<option value="' + d + '">' + d + '</option>';
				});

				var w = '';
				$.each(data.width, function(i, d){
					w += '<option value="' + d + '">' + d + '</option>';
				});

				var t = '';
				$.each(data.thickness, function(i, d){
					t += '<option value="' + d + '">' + d + '</option>';
				});

				var item_name = '';
				$.each(data.item_names, function(i, d){
					item_name += '<option value="' + d + '">' + d + '</option>';
				});

				var part_category = '';
				$.each(data.part_categories, function(i, d){
					part_category += '<option value="' + d + '">' + d + '</option>';
				});

				$('#inventory-material').append(m);
				$('#inventory-length').append(l);
				$('#inventory-width').append(w);
				$('#inventory-thickness').append(t);
				$('#inventory-item-name').append(item_name);
				$('#inventory-part-category').append(part_category);
			}
		});
	}

	get_transaction_filters('Fabrication');
	function get_transaction_filters(operation){
		$.ajax({
			url:"/get_transaction_filters/" + operation,
			type:"GET",
			success:function(data){
				var m = '';
				$.each(data.material, function(i, d){
					m += '<option value="' + d + '">' + d + '</option>';
				});

				var l = '';
				$.each(data.length, function(i, d){
					l += '<option value="' + d + '">' + d + '</option>';
				});

				var w = '';
				$.each(data.width, function(i, d){
					w += '<option value="' + d + '">' + d + '</option>';
				});

				var t = '';
				$.each(data.thickness, function(i, d){
					t += '<option value="' + d + '">' + d + '</option>';
				});

				$('#transaction-material').append(m);
				$('#transaction-length').append(l);
				$('#transaction-width').append(w);
				$('#transaction-thickness').append(t);
			}
		});
	}

	get_withdrawal_slip_filters('Fabrication');
	function get_withdrawal_slip_filters(operation){
		$.ajax({
			url:"/get_withdrawal_slip_filters/" + operation,
			type:"GET",
			success:function(data){
				var l = '';
				$.each(data.production_orders, function(i, d){
					l += '<option value="' + d + '">' + d + '</option>';
				});

				var w = '';
				$.each(data.customer, function(i, d){
					w += '<option value="' + d + '">' + d + '</option>';
				});

				var t = '';
				$.each(data.warehouse, function(i, d){
					t += '<option value="' + d + '">' + d + '</option>';
				});

				$('#withdrawal-production-order').append(l);
				$('#withdrawal-customer').append(w);
				$('#withdrawal-warehouse').append(t);
			}
		});
	}

	$('.inv-filters').select2();

	$('.inv-filters-text').keyup(function(e){
		e.preventDefault();
		var filter_content = $(this).data('filter-content');
		var query = $('#inv-search-box').val();
		var page = 1;
		var filters = 'q=' + query + '&' + $('#' + filter_content).serialize();

		get_filters(filter_content, filters, page, 'Fabrication');
	});

	$('.inv-filters').change(function(e){
		e.preventDefault();
		var filter_content = $(this).data('filter-content');
		var query = $('#inv-search-box').val();
		var page = 1;
		var filters = 'q=' + query + '&' + $('#' + filter_content).serialize();

		get_filters(filter_content, filters, page, 'Fabrication');
	});
  $(document).on('change', '.trans_painting_stock_filter', function(event){
    event.preventDefault();
		var filter_contents = "trans_inventory_painting";
    var filter_content = $(this).data('filter-content');
		var query = $('#inv-search-box').val();
		var page = 1;
		var filters = 'q=' + query + '&' + $('#' + filter_contents).serialize();
 
    get_filters(filter_content, filters, page);
  
  });
$(document).on('change', '.painting_stock_filter', function(event){
 event.preventDefault();
		var filter_contents = "inventory_painting";
    var filter_content = $(this).data('filter-content');
		var query = $('#inv-search-box').val();
		var page = 1;
		var filters = 'q=' + query + '&' + $('#' + filter_contents).serialize();
 
    get_filters(filter_content, filters, page);
  
  });
  $(document).on('change', '.inv-filters-painting', function(event){
 event.preventDefault();
		var filter_contents = "powderconsume-list-painting";
    var filter_content = $(this).data('filter-content');
		var query = $('#inv-search-box').val();
		var page = 1;
		var filters = 'q=' + query + '&' + $('#' + filter_contents).serialize();
 
    get_filters(filter_content, filters, page);
  
  });
  
	function get_filters(filter_content, filters, page, operation){
		if(filter_content == 'scrap'){
			get_scrap(operation, page, filters);

			return false;
		}

		if(filter_content == 'inventory'){
			get_inventory_list(operation, page, filters);

			return false;
		}

		if(filter_content == 'transaction'){
			get_transaction_list(operation, page, filters);

			return false;
		}

		if(filter_content == 'withdrawal'){
			get_withdrawal_slips(operation, page, filters);
			return false;
		}

    if(filter_content == "stock-list-painting"){
      tbl_painting_stock_list(page, filters);
			return false;
    }
    if(filter_content == "stock-list-transaction-painting"){
    var filter_contents = "trans_inventory_painting";
		var query = $('#inv-search-box').val();
		var page = 1;
		var filterss = 'q=' + query + '&' + $('#' + filter_contents).serialize();
      get_transaction_list_painting(page, filterss);
			return false;
    }
    if(filter_content == "painting-mr-request"){
      tbl_painting_material_request_list(operation,page, filters);
			return false;
    }
    if(filter_content == "powderconsume-list-painting"){
      var filter_contents = "consumed_list_powder";
      var query = $('#inv-search-box').val();
      var page = 1;
      var filterss = 'q=' + query + '&' + $('#' + filter_contents).serialize();
      tbl_powder_consumed_list(page, filterss);
			return false;
    }
	}

	$('#inv-search-box').keyup(function(){
		var parent_tab = $("#operation-tab li a.active").attr('href');
    var operation = $("#operation-tab li a.active").attr('data-operation');
		var child_tab = $(parent_tab + ' li a.active').attr('href');
		var filter_content = $(child_tab + ' form').attr('id');
		var query = $('#inv-search-box').val();
		var page = 1;
		var filters = 'q=' + query + '&' + $('#' + filter_content).serialize();
    var grand_child_tab = $(child_tab + ' li a.active').attr('data-painting');
    if(operation == "Painting"){
      if(grand_child_tab == null){
        get_filters(filter_content, filters, page, operation);
      }else{
        get_filters(grand_child_tab, filters, page, operation);
      }

    }else{
      get_filters(filter_content, filters, page, operation);
      // alert(filter_content);
    }
		
  });
 
	$('#transfer_daterange').daterangepicker({
    "showDropdowns": true,
    startDate: moment().subtract(62, 'day'),
    ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    "linkedCalendars": false,
    "autoUpdateInput": false,
    "alwaysShowCalendars": true,
    locale: {
          cancelLabel: 'Clear'
      }
  }, function(start, end, label) {
    console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    filter_from_transfer_date_range();
  });


   $('#transfer_daterange').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
      filter_from_transfer_date_range();
  });
  $('#transfer_daterange').on('cancel.daterangepicker', function(ev, picker)  {
      $(this).val('');
  });


    $('#balance_qty_id').val("");
    $('#item_description_input').val("");
    $('#actual_qty_id').text(0);
    $('#planned_qty_id').text(0);
    // stock_adjustment_list();
    // inventory_history_list();
    $('#item_desc_div').hide();
    $('#entry_type_div').hide();
    $('.schedule-date').datepicker({
        'format': 'yyyy-mm-dd',
        'autoclose': true
    });
    $(document).on('click', '.btn-stock-adjust-entry', function(){
        get_itemcode();
        $('#balance_qty_id').val("");
        $('#item_description_input').val("");
        $('#actual_qty_id').text(0);
        $('#planned_qty_id').text(0);
        $('#add-stock-entries-adjustment').modal('show');
        $('#item_desc_div').hide();
        $('#entry_type_div').hide();
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
<script>
    function get_itemcode(){
        $.ajax({
          url: "/get_item_code_stock_adjustment_entries",
          method: "GET",
          success: function(data) {
          $('#itemcode_line').html(data);
            
          },
          error: function(data) {
          alert(data);
          }
        });
    }

    function get_balanceqty(){
        var item_code = $('#itemcode_line').val();
        $.ajax({
          url: "/get_balanceqty_stock_adjustment_entries/"+ item_code,
          method: "GET",
          success: function(data) {
          $('#balance_qty_id').val(data.qty.balance);
          $('#orig_balance_qty').val(data.qty.balance);
          $('#actual_qty_id').text(data.qty.actual);
          $('#planned_qty_id').text(data.qty.planned);
          $('#item_description_label').text(data.qty.description);
          $('#item_description_input').val(data.qty.description);
          $('#entry_type_label').text(data.qty.entry_type);
          $('#entry_type_box').val(data.qty.entry_type);
          $('#item_desc_div').show();
          $('#entry_type_div').show();
          },
          error: function(data) {
          alert(data);
          }
        });
    }
    $('#add-stock-entries-adjustment-frm').submit(function(e){
      e.preventDefault();
      var item_code = $('#itemcode_line').val();
      
      if(item_code == "default"){
        showNotification("danger", "Pls Select Item code", "now-ui-icons travel_info");
      }else{
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
            $('#add-stock-entries-adjustment-frm').trigger("reset");
            $('#balance_qty_id').val("");
            $('#item_description_input').val("");
            $('#actual_qty_id').text('0');
            $('#planned_qty_id').text('0');
            $('#add-stock-entries-adjustment').modal('hide');
            $('#item_desc_div').hide();
            $('#entry_type_div').hide();
            stock_adjustment_list();
            inventory_history_list();

                // $('#edit-worktation-frm').trigger("reset");
                // workstation_list();

          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      }); 
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

  $('.select_stock_adjust').select2({
    dropdownParent: $("#add-stock-entries-adjustment"),
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
//   $('.sel2').select2({
//       dropdownParent: $("#add-stock-entries-adjustment"),
//       dropdownAutoWidth: false,
//       width: '100%',
//       cache: false
//     });
</script>
{{-- <script>
function stock_adjustment_list(page, filters, query){
    $.ajax({
          url:"/get_tbl_stock_adjustment_entry?page=" + page,
          type:"GET",
          data: {filters: filters, q: query},
          success:function(data){
            $('#adjuststock-list-tbl').html(data);
          }
        });

    }
function inventory_history_list(page, query){
    $.ajax({
          url:"/get_fabrication_inventory_history_list?page=" + page,
          type:"GET",
          data: {search_string: query},
          success:function(data){
            
            $('#inventory-list-tbl').html(data);
          }
        });

    }
</script> --}}
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
  
function rawmaterial_diff_Chart(){
      // var year = $('#rfd-success-rate-year').val();
      $.ajax({
         url: "/raw_material_monitoring_data_diff",
         method: "GET",
          success: function(data) {
          
          var item_code = [];
            var actual_qty = [];
            var actual_bar = [];
            var minimum = [];
            var planned = [];
            var items = "\n";
            var minimum_label = [];
            var decsription = [];
            var c_actual = [];
            var c_planned = [];
            var c_minimum = [];
            for(var i in data.chart_data) {
               item_code.push(data.chart_data[i].item_code);
               minimum.push(data.chart_data[i].minimum);
               actual_bar.push(data.chart_data[i].actual_bar);
               actual_qty.push(data.chart_data[i].actual_qty);
               minimum_label.push(data.chart_data[i].minimum_label);
               planned.push(data.chart_data[i].planned);
               decsription.push(data.chart_data[i].decsription);
               c_actual.push(data.chart_data[i].c_actual);
               c_planned.push(data.chart_data[i].c_planned);
               c_minimum.push(data.chart_data[i].c_minimum);
            }
            var chartdata = {
               labels: item_code,
               data1:decsription,
               data2:actual_qty,
               datasets : [{
                  backgroundColor:c_minimum ,
                  data: minimum,
                  lay:minimum_label,
                  item:decsription,
                  label: "Minimum"
               },{
                  backgroundColor:c_actual,
                  data: actual_bar,
                  lay:actual_qty,
                  label: "Actual Qty"
               },
               {
                  backgroundColor: c_planned,
                  data: planned,
                  lay:planned,
                  label: "Planned"
               }]
               
            };

            var ctx = $("#rawmaterial_diff_Chart");

            if (window.diffchart != undefined) {
               window.diffchart.destroy();
            }
            const totalizer = {
			  id: 'totalizer',

			  beforeUpdate: chart => {
			    let totals = {}
			    let utmost = 2

			    chart.data.datasets.forEach((dataset, datasetIndex) => {
			      
			      totals[datasetIndex] =  actual_qty[datasetIndex]
			    })

			    chart.$totalizer = {
			      totals: totals,
			      utmost: utmost
			    }
			  }
			}
            window.diffchart = new Chart(ctx, {
               type: 'bar',
               data: chartdata,
               options: {
                  responsive: true,
                  legend: {
                     position: 'top',
                     labels:{
                        boxWidth: 11
                     }
                  },
                  scales: {
                     xAxes: [{ stacked: true,
                     ticks: {
                    // Include a dollar sign in the ticks
                    callback: function(value, index, values) {
                        return value;
                    }
                }}],
                     yAxes: [{
                      stacked: true,
                         ticks: {
		                    // Include a dollar sign in the ticks
		                    callback: function(value, index, values) {
		                        return value + "\n pcs";
		                    }
		                }
                      }]
                  },
                  tooltips: {
                     mode: 'label',
                     callbacks: {
                      title: function (t, d) {
                         return d['data1'][t[0]['index']];
                          },
                        label: function(t, d) {
                           var dstLabel = d.datasets[t.datasetIndex].label;
                           var yLabel = d['datasets'][t.datasetIndex]['lay'][t['index']];
                           var item = d['data1'][1][t['index']];
                           var btn = document.createElement("div");
                           var prod =  dstLabel + ':\n'+ yLabel +"\n m3";
                           var fortooltip= "\ decription: "+item +"\ this " +prod+'\n'+ item;
                            return [prod];
                        }
                     },
                      backgroundColor: '#FFF',
                      titleFontSize: 16,
                      titleFontColor: '#0066ff',
                      bodyFontColor: '#000',
                      bodyFontSize: 14,
                      displayColors: false
                  },
                  animation: {
            		duration: 0 // general animation time
       			  },
                  hover: {
                  animationDuration: 0 // duration of animations when hovering an item
                  },
                  responsiveAnimationDuration: 0 ,// animation duration after a resize
                  plugins: {
			      datalabels: {
			        formatter: (value, ctx) => {
			          const total = ctx.chart.$totalizer.totals[ctx.dataIndex]
			          return ctx.chart.data.data2[ctx.dataIndex];
			        },
			        align: 'end',
			        anchor: 'end',
			        display: function(ctx) {
			          return ctx.datasetIndex === ctx.chart.$totalizer.utmost
			        }
			      }
			    }
               }, plugins: [totalizer]
            });
         },
         error: function(data) {
            alert('Error fetching data!');
         }
      });
   }
</script>
<script type="text/javascript">
  
function rawmaterial_crs_Chart(){
      // var year = $('#rfd-success-rate-year').val();
      $.ajax({
         url: "/raw_material_monitoring_data_crs",
         method: "GET",
         success: function(data) {
          
          var item_code = [];
            var actual_qty = [];
            var actual_bar = [];
            var minimum = [];
            var planned = [];
            var items = "\n";
            var minimum_label = [];
            var decsription = [];
            var c_actual = [];
            var c_planned = [];
            var c_minimum = [];
            for(var i in data.chart_data) {
               item_code.push(data.chart_data[i].item_code);
               minimum.push(data.chart_data[i].minimum);
               actual_bar.push(data.chart_data[i].actual_bar);
               actual_qty.push(data.chart_data[i].actual_qty);
               minimum_label.push(data.chart_data[i].minimum_label);
               planned.push(data.chart_data[i].planned);
               decsription.push(data.chart_data[i].decsription);
               c_actual.push(data.chart_data[i].c_actual);
               c_planned.push(data.chart_data[i].c_planned);
               c_minimum.push(data.chart_data[i].c_minimum);
            }
            var chartdata = {
               labels: item_code,
               data1:decsription,
               data2:actual_qty,
               datasets : [{
                  backgroundColor:c_minimum ,
                  data: minimum,
                  lay:minimum_label,
                  item:decsription,
                  label: "Minimum"
               },{
                  backgroundColor:c_actual,
                  data: actual_bar,
                  lay:actual_qty,
                  label: "Actual Qty"
               },
               {
                  backgroundColor: c_planned,
                  data: planned,
                  lay:planned,
                  label: "Planned"
               }]
               
            };

            var ctx = $("#rawmaterial_crs_Chart");

            if (window.crschart != undefined) {
               window.crschart.destroy();
            }
            const totalizer = {
			  id: 'totalizer',

			  beforeUpdate: chart => {
			    let totals = {}
			    let utmost = 2

			    chart.data.datasets.forEach((dataset, datasetIndex) => {
			      
			      totals[datasetIndex] =  actual_qty[datasetIndex]
			    })

			    chart.$totalizer = {
			      totals: totals,
			      utmost: utmost
			    }
			  }
			}
            window.crschart = new Chart(ctx, {
               type: 'bar',
               data: chartdata,
               options: {
                  responsive: true,
                  legend: {
                     position: 'top',
                     labels:{
                        boxWidth: 11
                     }
                  },
                  scales: {
                     xAxes: [{ stacked: true,
                     ticks: {
                    // Include a dollar sign in the ticks
                    callback: function(value, index, values) {
                        return value;
                    }
                }}],
                     yAxes: [{
                      stacked: true,
                         ticks: {
                            stepSize: 10000,
                            callback: function  (value) {
                               var ranges = [
                                  { divider: 1e6, suffix: 'M Sheet/s' },
                                  { divider: 1e3, suffix: 'K \nm3' }
                               ];
                               function formatNumber(n) {
                                  for (var i = 0; i < ranges.length; i++) {
                                     if (n >= ranges[i].divider) {
                                        return (n / ranges[i].divider).toString() + ranges[i].suffix;
                                     }
                                  }
                                  return n;
                               }
                               return formatNumber(value);
                            }
                         }
                      }]
                  },
                  tooltips: {
                     mode: 'label',
                     callbacks: {
                      title: function (t, d) {
                         return d['data1'][t[0]['index']];
                          },
                        label: function(t, d) {
                           var dstLabel = d.datasets[t.datasetIndex].label;
                           var yLabel = d['datasets'][t.datasetIndex]['lay'][t['index']];
                           var item = d['data1'][1][t['index']];
                           var btn = document.createElement("div");
                           var prod =  dstLabel + ':\n'+ yLabel +"\n m3";
                           var fortooltip= "\ decription: "+item +"\ this " +prod+'\n'+ item;
                            return [prod];
                        }
                     },
                      backgroundColor: '#FFF',
                      titleFontSize: 16,
                      titleFontColor: '#0066ff',
                      bodyFontColor: '#000',
                      bodyFontSize: 14,
                      displayColors: false
                  },
                  animation: {
            		duration: 0 // general animation time
       			  },
                  hover: {
                  animationDuration: 0 // duration of animations when hovering an item
                  },
                  responsiveAnimationDuration: 0, // animation duration after a resize
                  plugins: {
			      datalabels: {
			        formatter: (value, ctx) => {
			          const total = ctx.chart.$totalizer.totals[ctx.dataIndex]
			          return ctx.chart.data.data2[ctx.dataIndex];

			        },
			        align: 'end',
			        anchor: 'end',
			        display: function(ctx) {
			          return ctx.datasetIndex === ctx.chart.$totalizer.utmost
			        }
			      }
			    }
               }, plugins: [totalizer]
            });
         },
         error: function(data) {
            alert('Error fetching data!');
         }
      });
   }
</script>
<script type="text/javascript">
  
function rawmaterial_alum_Chart(){
      // var year = $('#rfd-success-rate-year').val();
      $.ajax({
         url: "/raw_material_monitoring_data_alum",
         method: "GET",
          success: function(data) {
          
          var item_code = [];
            var actual_qty = [];
            var actual_bar = [];
            var minimum = [];
            var planned = [];
            var items = "\n";
            var minimum_label = [];
            var decsription = [];
            var c_actual = [];
            var c_planned = [];
            var c_minimum = [];
            for(var i in data.chart_data) {
               item_code.push(data.chart_data[i].item_code);
               minimum.push(data.chart_data[i].minimum);
               actual_bar.push(data.chart_data[i].actual_bar);
               actual_qty.push(data.chart_data[i].actual_qty);
               minimum_label.push(data.chart_data[i].minimum_label);
               planned.push(data.chart_data[i].planned);
               decsription.push(data.chart_data[i].decsription);
               c_actual.push(data.chart_data[i].c_actual);
               c_planned.push(data.chart_data[i].c_planned);
               c_minimum.push(data.chart_data[i].c_minimum);
            }
            var chartdata = {
               labels: item_code,
               data1:decsription,
               data2:actual_qty,
               datasets : [{
                  backgroundColor:c_minimum ,
                  data: minimum,
                  lay:minimum_label,
                  item:decsription,
                  label: "Minimum"
               },{
                  backgroundColor:c_actual,
                  data: actual_bar,
                  lay:actual_qty,
                  label: "Actual Qty"
               },
               {
                  backgroundColor: c_planned,
                  data: planned,
                  lay:planned,
                  label: "Planned"
               }]
               
            };

            var ctx = $("#rawmaterial_alum_Chart");

            if (window.alumchart != undefined) {
               window.alumchart.destroy();
            }
            const totalizer = {
			  id: 'totalizer',

			  beforeUpdate: chart => {
			    let totals = {}
			    let utmost = 2

			    chart.data.datasets.forEach((dataset, datasetIndex) => {
			      
			      totals[datasetIndex] =  actual_qty[datasetIndex]
			    })

			    chart.$totalizer = {
			      totals: totals,
			      utmost: utmost
			    }
			  }
			}
            window.alumchart = new Chart(ctx, {
               type: 'bar',
               data: chartdata,
               options: {
                  responsive: true,
                  legend: {
                     position: 'top',
                     labels:{
                        boxWidth: 11
                     }
                  },
                  scales: {
                     xAxes: [{ stacked: true,
                     ticks: {
                    // Include a dollar sign in the ticks
                    callback: function(value, index, values) {
                        return value;
                    }
                }}],
                     yAxes: [{
                      stacked: true,
                         ticks: {
		                    // Include a dollar sign in the ticks
		                    callback: function(value, index, values) {
		                        return value + "\n mm3";
		                    }
		                }
                      }]
                  },
                  tooltips: {
                     mode: 'label',
                     callbacks: {
                      title: function (t, d) {
                         return d['data1'][t[0]['index']];
                          },
                        label: function(t, d) {
                           var dstLabel = d.datasets[t.datasetIndex].label;
                           var yLabel = d['datasets'][t.datasetIndex]['lay'][t['index']];
                           var item = d['data1'][1][t['index']];
                           var btn = document.createElement("div");
                           var prod =  dstLabel + ':\n'+ yLabel +"\n m3";
                           var fortooltip= "\ decription: "+item +"\ this " +prod+'\n'+ item;
                            return [prod];
                        }
                     },
                      backgroundColor: '#FFF',
                      titleFontSize: 16,
                      titleFontColor: '#0066ff',
                      bodyFontColor: '#000',
                      bodyFontSize: 14,
                      displayColors: false
                  },
                  animation: {
            		duration: 0 // general animation time
       			  },
                  hover: {
                  animationDuration: 0 // duration of animations when hovering an item
                  },
                  responsiveAnimationDuration: 0, // animation duration after a resize
                  plugins: {
			      datalabels: {
			        formatter: (value, ctx) => {
			          const total = ctx.chart.$totalizer.totals[ctx.dataIndex]
			            return ctx.chart.data.data2[ctx.dataIndex];
			        },
			        align: 'end',
			        anchor: 'end',
			        display: function(ctx) {
			          return ctx.datasetIndex === ctx.chart.$totalizer.utmost
			        }
			      }
			    }
               }, plugins: [totalizer]
            });
         },
         error: function(data) {
            alert('Error fetching data!');
         }
      });
   }
</script>
<script type="text/javascript">
  $(document).on('click', '.out_of_stock', function(){
      var id = $(this).attr("data-id");
      $.ajax({
          url:"/out_of_stock/",
          type:"GET",
      data: {id: id},
          success:function(data){
            $('.tbl_out_of_stock').html(data);
          }
      });

    });
  // $(document).on('click', '#alum_li', function(){
  //   rawmaterial_alum_Chart();
  //   });
  // $(document).on('click', '#crs_li', function(){
  //     rawmaterial_crs_Chart();
  // });
  // $(document).on('click', '#dif_li', function(){
  //   rawmaterial_diff_Chart();

  // });
  $(document).on('click', '.btn-material-transfer', function(){
    $('#add-material-transfer-modal').modal('show');
    $
    $("#material-transfer-table tbody").empty();

  });
    $('.sel2').select2({
    dropdownParent: $("#add-material-transfer-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
    $('.sel3').select2({
    dropdownParent: $("#filter_material_transfer_request"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
  $('.raw-filters').select2({
    dropdownParent: $("#raw_monitoring"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
  $('.painting_stock_filter').select2({
    dropdownParent: $("#inventory_painting"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
  $('.inv-filters-painting').select2({
    dropdownParent: $("#consumed_list_powder"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });

    $('.trans_painting_stock_filter').select2({
    dropdownParent: $("#trans_inventory_painting"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
  $('.select_stock_adjust_painting').select2({
    dropdownParent: $("#add-stock-entries-adjustment-painting"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
  
    
</script>
<script type="text/javascript">
	function out_of_stock(){
    var id = $('#raw_monitoring').find('a.active').data('id');
    $.ajax({
        url:"/out_of_stock/",
        type:"GET",
        data: {id: id},
        success:function(data){
        $('.tbl_out_of_stock').html(data);
        }
      });
  }
</script>
<script type="text/javascript">
  $('#add-material-transfer-modal .add-row').click(function(e){
         e.preventDefault();
         var col1 = '';
         var col3 = '';
         var col4 = '';
         var s = ($('#s_warehouse').val() == '')? "": $('#s_warehouse').val();
         var t = ($('#t_warehouse').val() == '')? "": $('#t_warehouse').val();

         $.ajax({
            url: "/get_selection_box_in_item_code_warehouse",
            type:"get",
            cache: false,
            success: function(response) {
               col1 += '<option value="none">Select Item Code</option>';
               col3 += '<option value="none">Select Warehouse</option>';
               $.each(response.item_list, function(i, d){
                  col1 += '<option value="' + d.name + '">' + d.name + '</option>';
               });
               $.each(response.warehouse, function(i, d){
                  col3 += '<option value="' + d + '">' + d + '</option>';
               });
               $.each(response.warehouse, function(i, d){
                  col4 += '<option value="' + d + '">' + d + '</option>';
               });
               var thizz = document.getElementById('material-transfer-table');
               var id = $(thizz).closest('table').find('tr:last td:first').text();
               var validation = isNaN(parseFloat(id));
               if(validation){
                var new_id = 1;
               }else{
                var new_id = parseInt(id) + 1;
               }
               var len2 = new_id;
               var id_unique="transfer"+len2;
               var tblrow = '<tr class="inline-style">' +
                  '<td class="inline-style">'+len2+'</td>' +
                  '<td class="inline-style"><select class="form-control sel2 sware" name="new_s_warehouse[]" value='+ s +' required>'+col3+'</select></td>' +
                  '<td class="inline-style"><select class="form-control sel2 tware" name="new_t_warehouse[]"value='+ t +' required>'+col4+'</select></td>' +
                  '<td class="inline-style"><select class="form-control sel2 onchange-selection" name="new_item_code[]" required data-idcolumn='+id_unique+'>'+col1+'</select></td>' +
                  '<td class="inline-style"><input type="text" name="qty[]" class="form-control input-multi" required></td>' +
                  '<td class="inline-style"><input type="text" name="uom" class="form-control input-multi readonly-input e" style="text-center:center;" id='+ id_unique +'></td>' +
                  '<td class="inline-style"><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>' +
                  '</tr>';

               $("#add-material-transfer-modal #material-transfer-table").append(tblrow);
               $('.sel2').select2({
                    dropdownParent: $("#add-material-transfer-modal"),
                    dropdownAutoWidth: false,
                    width: '100%',
                    cache: false
                  });
               $('.date-picker').datepicker({
                  'format': 'yyyy-mm-dd',
                  'autoclose': true
                });
               $('.readonly-input').attr('readonly', true);
               
               autoRowNumberAddKPI();
               var s = ($('#s_warehouse').val() == '')? "": $('#s_warehouse').val();
         		var t = ($('#t_warehouse').val() == '')? "": $('#t_warehouse').val();
         		$('.sware').val(s);
         		$('.tware').val(t);
         		$('.sware').trigger('change');
         		$('.tware').trigger('change');
            },
            error: function(response) {
               alert('Error fetching Designation!');
            }
         });
      });

    $(document).on('change', '.onchange-selection', function(){
           var first_selection_data = $(this).val();
           var id_for_second_selection = $(this).attr('data-idcolumn');
           var format_id_for_second_selection = "#"+id_for_second_selection;
            $.ajax({
            url:"/get_uom_item_selected_in_purchase/"+first_selection_data,
            type:"GET",
            success:function(data){
              $(format_id_for_second_selection).val(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
      });

    $(document).on('change', '#required_date_all', function(){
           var datee = $("#required_date_all").val();
          $('.input-date').val(datee);
      });
    $(document).on('change', '#s_warehouse', function(){
           var s = $("#s_warehouse").val();
          $('.sware').val(s);
                    $('.sware').trigger('change');

      });
    $(document).on('change', '#t_warehouse', function(){
           var t = $("#t_warehouse").val();
          $('.tware').val(t);
         		$('.tware').trigger('change');
      });


      $('.readonly').each(function(){
	    $(this).attr('readonly','readonly');
	  });

	  $('.sel-reference-no-transfer').change(function(){
	    var ref_type = $(this).data('type');
	    var ref_no = $(this).val();
	    $.ajax({
	      url:"/get_reference_details/" + ref_type + "/" + ref_no,
	      type:"GET",
	      success:function(response){
	        if (response.success == 0) {
	          showNotification("danger", 'No BOM found for Item ' + item_code, "now-ui-icons travel_info");
	        }else{
	          $('#add-material-transfer-modal input[name="customer"]').val(response.customer);
	          $('#add-material-transfer-modal input[name="project"]').val(response.project);
	        }
	      }
	    });
  });
	  $('#add-material-transfer-frm').submit(function(e){
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
            $('#add-material-transfer-modal').modal('hide');
            $('#add-material-transfer-frm').trigger("reset");
            $('#sales_order_transfer').trigger("change");
            $('#material_request_transfer').trigger("change");
            $('#s_warehouse').trigger("change");
            $('#t_warehouse').trigger("change");
              
            material_transfer_tbl();

          }
        }
      });
    });
	  $('#cancel-material-transfer-frm').submit(function(e){
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
            $('#cancel-material-transfer-modal').modal('hide');
            $('#cancel-material-transfer-frm').trigger("reset");
            material_transfer_tbl();

          }
        }
      });
    });
	   $(document).on('click', '#transfer_list_pagination a', function(event){
         event.preventDefault();
         var page = $(this).attr('href').split('page=')[1];
         material_transfer_tbl(page);
    });
	   $(document).on('click', '.view_transfer_detail_btn', function(){
	   	 var id = $(this).data('id');
	   	$.ajax({
	        url:"/get_material_transfer_details/"+id,
	        type:"GET",
	        success:function(data){
	        $('#tbl_view_transfer_details').html(data);
	        }
	      });
           $('#view-material-transfer-modal').modal('show');
      });
	   $(document).on('click', '.cancel-transfer-btn', function(){
		  var id = $(this).attr("data-id");
	      $('#transfer_id').val(id);
	      $('#transfer-label-cancel').text(id);
          $('#cancel-material-transfer-modal').modal('show');
      });


		$(document).on('change', '.sel3', function(event){
		  var date = $('#transfer_daterange').val();
		  var startDate = $('#transfer_daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
		  var endDate = $('#transfer_daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
		  var data = {
		        item_code: $('#transfer_item_code').val(),
		        so:$('#transfer_so').val(),
		        customer: $('#transfer_customer').val(),
		        status: $('#transfer_status').val(),
		        project: $('#transfer_project').val(),
		        ste: $('#transfer_stem').val(),
            filter: true
		      }
		 
		    $.ajax({
          url:"/get_tbl_material_transfer",
		          type:"GET",
		          data: data,
		          success:function(data){
		            
		            $('#transfer_list').html(data);
		          }
		        });
		  
		  });
		
	$(document).on('click', '.confirmed-transfer-btn', function(){
		  var id = $(this).attr("data-id");
	      $('#transfer_id_confirm').val(id);
	      $('#transfer-label-confirm').text(id);
          $('#confirmed-material-transfer-modal').modal('show');
     });
  $(document).on('click', '.delete_transfer_btn', function(){
		  var id = $(this).attr("data-id");
	      $('#transfer_id_delete').val(id);
	      $('#transfer-label-delete').text(id);
        $('#delete-material-transfer-modal').modal('show');
     });
	$('#confirmed-material-transfer-frm').submit(function(e){
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
            $('#confirmed-material-transfer-modal').modal('hide');
            $('#confirmed-material-transfer-frm').trigger("reset");
            material_transfer_tbl();

          }
        }
      });
    });
    $('#delete-material-transfer-frm').submit(function(e){
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
            $('#delete-material-transfer-modal').modal('hide');
            $('#delete-material-transfer-frm').trigger("reset");
            material_transfer_tbl();

          }
        }
      });
    });
	   
</script>
<script type="text/javascript">
    function autoRowNumberAddKPI(){
         $('#add-material-transfer-modal #material-transfer-table tbody tr').each(function (idx) {
            $(this).children("td:eq(0)").html(idx + 1);
         });
      }
</script>

<script>
function filter_from_transfer_date_range(){
  var date = $('#transfer_daterange').val();
		  var startDate = $('#transfer_daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
		  var endDate = $('#transfer_daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
		  var data = {
		        item_code: $('#transfer_item_code').val(),
		        so:$('#transfer_so').val(),
		        customer: $('#transfer_customer').val(),
		        status: $('#transfer_status').val(),
		        project: $('#transfer_project').val(),
            ste: $('#transfer_stem').val()
            filter: true
		      }
		 
    	$.ajax({
        url:"/get_tbl_material_transfer/",
          type:"GET",
          data: data,
          success:function(data){
            
            $('#transfer_list').html(data);
          }
        });
  
  };
</script>
<script type="text/javascript">
	function material_transfer_tbl(page, query){
		  var data = {
            from: $('#transfer_daterange').data('daterangepicker').startDate.format('YYYY-MM-DD'),
            end:$('#transfer_daterange').data('daterangepicker').endDate.format('YYYY-MM-DD'),
		        item_code: $('#transfer_item_code').val(),
		        so:$('#transfer_so').val(),
		        customer: $('#transfer_customer').val(),
		        status: $('#transfer_status').val(),
		        project: $('#transfer_project').val(),
		         ste: $('#transfer_stem').val()
             filter: false
		      }
    $.ajax({
        url:"/get_tbl_material_transfer/?page="+page,
        data: data,
        type:"GET",
        success:function(data){
        $('#transfer_list').html(data);
        }
      });
  }
</script>

<script type="text/javascript">
$(document).ready(function(){
  

  $(document).on('click', '.add-material-request-btn', function(){
    var operation = $(this).attr("data-operation");
    $('#add-material-request-modal').modal('show');
    $('#material-request-operation').val(operation);
   

    // $('#target-wh option:contains(A Warehouse P2 - FI)').prop({selected: true});
    // $('#target-wh').trigger('change');
    $("#material-purchase-table tbody").empty();
      

  });
  $('.sel4').select2({
    dropdownParent: $("#add-material-request-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false,
    placeholder: "",
    allowClear: true
  });
  $('.sel5').select2({
    dropdownParent: $("#filter_material_purchase_request"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
  $('.mr_request_paint').select2({
    dropdownParent: $("#painting-material-request-tab"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });


  $('#purchase_daterange').daterangepicker({
    "showDropdowns": true,
    startDate: moment().subtract(62, 'day'),
    ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    "linkedCalendars": false,
    "autoUpdateInput": false,
    "alwaysShowCalendars": true,
  }, function(start, end, label) {
    console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    filter_from_purchase_date_range();
  });

   $('#purchase_daterange').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
      filter_from_purchase_date_range();
  });
  $('#purchase_daterange_painting').daterangepicker({
    "showDropdowns": true,
    startDate: moment().subtract(62, 'day'),
    ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    "linkedCalendars": false,
    "autoUpdateInput": false,
    "alwaysShowCalendars": true,
  }, function(start, end, label) {
    console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    var operation ="Painting";
      var filter_contents = "painting-mr-request";
      var query = $('#inv-search-box').val();
      var page = 1;
      var filterss = 'q=' + query + '&' + $('#' + filter_contents).serialize();
      tbl_painting_material_request_list(operation,page, filterss);
  });

   $('#purchase_daterange_painting').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
      var operation ="Painting";
      var filter_contents = "painting-mr-request";
      var query = $('#inv-search-box').val();
      var page = 1;
      var filterss = 'q=' + query + '&' + $('#' + filter_contents).serialize();
      tbl_painting_material_request_list(operation,page, filterss);
  });
  $('#consumed_daterange').daterangepicker({
    "showDropdowns": true,
    startDate: moment().subtract(62, 'day'),
    ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    "linkedCalendars": false,
    "autoUpdateInput": false,
    "alwaysShowCalendars": true,
  }, function(start, end, label) {
    console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    
         var page = $(this).attr('href').split('page=')[1];
         var filter_contents = "consumed_list_powder";
         var filter_content = "powderconsume-list-painting";
		     var query = $('#inv-search-box').val();
	    	 var filters = 'q=' + query + '&' + $('#' + filter_contents).serialize();
         tbl_powder_consumed_list(page, filters);
  });

   $('#consumed_daterange').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
      
         var page = $(this).attr('href').split('page=')[1];
         var filter_contents = "consumed_list_powder";
         var filter_content = "powderconsume-list-painting";
		     var query = $('#inv-search-box').val();
	    	 var filters = 'q=' + query + '&' + $('#' + filter_contents).serialize();
         tbl_powder_consumed_list(page, filters);
  });

  $('.readonly').each(function(){
    $(this).attr('readonly','readonly');
  });
  $('.sel-reference-no').change(function(){
    var ref_type = $(this).data('type');
    var ref_no = $(this).val();
    $.ajax({
      url:"/get_reference_details/" + ref_type + "/" + ref_no,
      type:"GET",
      success:function(response){
        if (response.success == 0) {
          showNotification("danger", 'No BOM found for Item ' + item_code, "now-ui-icons travel_info");
        }else{
          $('#add-material-request-modal input[name="customer"]').val(response.customer);
          $('#add-material-request-modal input[name="project"]').val(response.project);
          var classification = (response.purpose) ? response.purpose : response.sales_type;
          classification = (classification != 'Sample') ? 'Customer Order' : 'Sample';
          $('#add-material-request-modal input[name="classification"]').val(classification);
        }
      }
    });
  });
  $('#sel-item').change(function(e){
    e.preventDefault();
    if ($(this).val()) {
      $.ajax({
        url:"/get_item_details/" + $(this).val(),
        type:"GET",
        success:function(response){
          if (response.success == 0) {
            showNotification("danger", response.message, "now-ui-icons travel_info");
          }else{
            $('#add-material-request-modal textarea[name="description"]').text(response.description);
            $('#add-material-request-modal input[name="stock_uom"]').val(response.stock_uom);
            $('#add-material-request-modal input[name="item_classification"]').val(response.item_classification); 
          }
        }
      });
    }
  });
  $('.date-picker').datepicker({
    'format': 'yyyy-mm-dd',
    'autoclose': true
  });
  $(document).on('click', '#purchase_list_pagination a', function(event){
         event.preventDefault();
         var page = $(this).attr('href').split('page=')[1];
         purchase_list(page);
  });
  $('#add-material-request-frm').submit(function(e){
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
            $('#add-material-request-modal').modal('hide');
            $('#add-material-request-frm').trigger("reset");
            purchase_list();
            tbl_painting_material_request_list("Painting");

          }
        }
      });
    });
  $('#cancel-material-request-frm').submit(function(e){
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
            $('#cancel-material-request-modal').modal('hide');
            $('#cancel-material-request-frm').trigger("reset");
            purchase_list();
            tbl_painting_material_request_list("Painting");


          }
        }
      });
    });
  $(document).on('click', '.cancel-purchase-btn', function(e){
     e.preventDefault();
      var id = $(this).attr("data-id");
      $('#purchase_id').val(id);
      $('#purchase-label-cancel').text(id);
      $('#cancel-material-request-modal').modal('show');

  });
  $(document).on('click', '.view_purchase_detail_btn', function(){
    var id = $(this).attr("data-id");
    $.ajax({
        url:"/get_material_request_for_purchase/"+id,
        type:"GET",
        success:function(data){
          $('#tbl_view_purchase_details').html(data);
        }
      });
    $('#view-material-request-modal').modal('show');
    });

  $(document).on('keyup', '#material-request-purchase-filter', function(){
    var query = $(this).val();
    purchase_list(1, query);
  });
  purchase_list();
  material_transfer_tbl();
  get_raw_filters('Fabrication');
  powder_coat_Chart();
  tbl_powder_consumed_list();
  tbl_painting_stock_list()
  
});



</script>

<script type="text/javascript">
  $(document).on("click", ".delete", function(){
        $(this).parents("tr").remove();
      });
</script>
<script type="text/javascript">
  $('#add-material-request-modal .add-row').click(function(e){
         e.preventDefault();
         var col1 = '';
         var col3 = '';
         var datee = ($('#required_date_all').val() == '')? "": $('#required_date_all').val();
         var operation = $('#material-request-operation').val();
         $.ajax({
            url: "/get_selection_box_in_item_code_warehouse/"+operation,
            type:"get",
            cache: false,
            success: function(response) {
               col1 += '<option value="none">Select Item Code</option>';
               col3 += '<option value="none">Select Warehouse</option>';
               $.each(response.item_list, function(i, d){
                  col1 += '<option value="' + d.name + '">' + d.name + '</option>';
               });
               $.each(response.warehouse, function(i, d){
                  col3 += '<option value="' + d + '">' + d + '</option>';
               });
               var thizz = document.getElementById('material-purchase-table');
               var id = $(thizz).closest('table').find('tr:last td:first').text();
               var validation = isNaN(parseFloat(id));
               if(validation){
                var new_id = 1;
               }else{
                var new_id = parseInt(id) + 1;
               }
               
               var len2 = new_id;
               var id_unique="purchase"+len2;
               var id_unique_warehouse="purchase-warehouse"+len2;
               var id_warehouse="#purchase-warehouse"+len2;
               var id_unique_warehousee="#purchase-warehouse"+len2 + " "+ "option:contains(Painting Warehouse - FI)";
               var tblrow = '<tr class="inline-style">' +
                  '<td class="inline-style">'+len2+'</td>' +
                  '<td class="inline-style"><select class="form-control sel4 onchange-selection" name="new_item_code[]" required data-idcolumn='+id_unique+'>'+col1+'</select></td>' +
                  '<td class="inline-style"><input type="text" name="qty[]" class="form-control input-multi" required></td>' +
                  '<td class="inline-style"><input type="text" name="uom" class="form-control input-multi readonly-input e" style="text-center:center;" id='+ id_unique +'></td>' +
                  '<td class="inline-style"><select id='+ id_unique_warehouse +' class="form-control sel4" name="new_warehouse[]" required>'+col3+'</select></td>' +
                  '<td class="inline-style"><input type="input" name="required_date[]" class="form-control  date-picker input-multi input-date" required value='+datee+' ></td>' +
                  '<td class="inline-style"><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>' +
                  '</tr>';

               $("#add-material-request-modal #material-purchase-table").append(tblrow);
               $('.sel4').select2({
                    dropdownParent: $("#add-material-request-modal"),
                    dropdownAutoWidth: false,
                    width: '100%',
                    cache: false
                  });
               $('.date-picker').datepicker({
                  'format': 'yyyy-mm-dd',
                  'autoclose': true
                });
               $('.readonly-input').attr('readonly', true);
               if(operation == "Painting"){
                $(id_unique_warehousee).prop({selected: true});
                $(id_warehouse).trigger('change');
               }
               
               autoRowNumberAddKPI_purchase();
            },
            error: function(response) {
               alert('Error fetching Designation!');
            }
         });
      });
    $(document).on('change', '.onchange-selection', function(){
           var first_selection_data = $(this).val();
           var id_for_second_selection = $(this).attr('data-idcolumn');
           var format_id_for_second_selection = "#"+id_for_second_selection;
            $.ajax({
            url:"/get_uom_item_selected_in_purchase/"+first_selection_data,
            type:"GET",
            success:function(data){
              $(format_id_for_second_selection).val(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
      });
    $(document).on('change', '#required_date_all', function(){
           var datee = $("#required_date_all").val();
          $('.input-date').val(datee);
      });
</script>
<script type="text/javascript">
    function autoRowNumberAddKPI_purchase(){
         $('#add-material-request-modal #material-purchase-table tbody tr').each(function (idx) {
            $(this).children("td:eq(0)").html(idx + 1);
         });
      }
</script>
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
<script>

$(document).on('change', '.raw-filters', function(event){
  var id = $('#raw_monitoring').find('a.active').data('id');
  var data = {
    length: $('#raw-length').val(),
        width:$('#raw-width').val(),
        thickness: $('#raw-thickness').val(),
        material : id
      }
 
    $.ajax({
          url:"/tbl_filter_out_of_stock",
          type:"GET",
          data: data,
          success:function(data){
            
            $('.tbl_out_of_stock').html(data);
          }
        });
  
  });
</script> 
<script>
  $(document).on('change', '.sel5', function(event){
    var date = $('#purchase_daterange').val();
    var startDate = $('#purchase_daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var endDate = $('#purchase_daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
    var activeme = $("#operation-tab a.active").attr('data-operation');
    var data = {
      from: startDate,
      end: endDate,
      item_code: $('#purchase_item_code').val(),  
      so:$('#purchase_so').val(),
      customer: $('#purchase_customer').val(),
      status: $('#purchase_status').val(),
      project: $('#purchase_project').val()
      filter: true
    }
    alert(activeme);
    $.ajax({
      url:"/get_tbl_material_request",
      type:"GET",
      data: data,
      success:function(data){
        
        $('#tbl_material_request').html(data);
      }
    });
  });
</script> 
<script>
function filter_from_purchase_date_range(){
  var date = $('#purchase_daterange').val();
  var startDate = $('#purchase_daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
  var endDate = $('#purchase_daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
  var data = {
        item_code: $('#purchase_item_code').val(),
        so:$('#purchase_so').val(),
        customer: $('#purchase_customer').val(),
        status: $('#purchase_status').val(),
        project: $('#purchase_project').val()
        filter: true
      }

    $.ajax({
          url:"/get_tbl_material_request",
          type:"GET",
          data: data,
          success:function(data){
            
            $('#tbl_material_request').html(data);
          }
        });
  
  };
</script>
<script>
function consumed_powder_coating_filter(){
  var date = $('#consumed_daterange').val();
  var startDate = $('#consumed_daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
  var endDate = $('#consumed_daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
  var data = {
        item_code: $('#itemcode_consume').val(),
        operator:$('#operator_consume').val(),
        start:startDate,
        end:endDate
      }

    $.ajax({
          url:"/tbl_comsumed_filter_box",
          type:"GET",
          data: data,
          success:function(data){
            
            $('#tbl_powder_consumed').html(data);
          }
        });
  
  };
</script>
<script type="text/javascript">
	 $(document).on('change', '#reference_transfer', function(){
           var reference_transfer = $("#reference_transfer").val();
           if (reference_transfer == "Sales Order") {
           	$('#input-so-div-tranfer').show();
           	$('#material_request_transfer').trigger("change");
           	$('#material_request_transfer option:contains(' + 'Select MREQ' + ')').prop({selected: true});
           	$('#input-mreq-div-tranfer').hide();
           	$('#add-material-transfer-modal input[name="customer"]').val("");
	        $('#add-material-transfer-modal input[name="project"]').val("");

           }else if(reference_transfer == "Material Request"){
           	$('#input-mreq-div-tranfer').show();
           	$('#input-so-div-tranfer').hide();
           	$('#sales_order_transfer option:contains(' + 'Select SO' + ')').prop({selected: true});
           	$('#sales_order_transfer').trigger("change");
           	$('#add-material-transfer-modal input[name="customer"]').val("");
	        $('#add-material-transfer-modal input[name="project"]').val("");

           }else{
           	$('#input-mreq-div-tranfer').hide();
           	$('#input-so-div-tranfer').hide();
           	$('#sales_order_transfer option:contains(' + 'Select SO' + ')').prop({selected: true});
           	$('#material_request_transfer option:contains(' + 'Select MREQ' + ')').prop({selected: true});
           	$('#sales_order_transfer').trigger("change");
           	$('#material_request_transfer').trigger("change");
           	$('#add-material-transfer-modal input[name="customer"]').val("");
	        $('#add-material-transfer-modal input[name="project"]').val("");
           }
      });
	
</script>
<script type="text/javascript">
  function purchase_list(page, query){
    var data = {
            from: $('#purchase_daterange').data('daterangepicker').startDate.format('YYYY-MM-DD'),
            end:$('#purchase_daterange').data('daterangepicker').endDate.format('YYYY-MM-DD'),
		        item_code: $('#purchase_item_code').val(),
            so:$('#purchase_so').val(),
            customer: $('#purchase_customer').val(),
            status: $('#purchase_status').val(),
            project: $('#purchase_project').val(),
            filter: false
		      }

        $.ajax({
            url:"/get_tbl_material_request/?page="+page,
            type:"GET",
            data: data,
            success:function(data){
              $('#tbl_material_request').html(data);
            }
          });
  }
</script>
<script>

	function get_raw_filters(operation){
		$.ajax({
			url:"/get_scrap_filters/" + operation,
			type:"GET",
			success:function(data){
				var m = '';
				$.each(data.material, function(i, d){
					m += '<option value="' + d + '">' + d + '</option>';
				});

				var l = '';
				$.each(data.length, function(i, d){
					l += '<option value="' + d + '">' + d + '</option>';
				});

				var w = '';
				$.each(data.width, function(i, d){
					w += '<option value="' + d + '">' + d + '</option>';
				});

				var t = '';
				$.each(data.thickness, function(i, d){
					t += '<option value="' + d + '">' + d + '</option>';
				});

				$('#raw-length').append(l);
				$('#raw-width').append(w);
				$('#raw-thickness').append(t);
			}
		});
	}
  </script>
  <script type="text/javascript">
  
  function powder_coat_Chart(){
        // var year = $('#rfd-success-rate-year').val();
        $.ajax({
           url: "/get_powder_coat_chart",
           method: "GET",
           success: function(data) {
            
            var item_code = [];
              var actual_qty = [];
              var actual_bar = [];
              var minimum = [];
              var planned = [];
              var items = "\n";
              var minimum_label = [];
              var decsription = [];
              var c_actual = [];
              var c_planned = [];
              var c_minimum = [];
              for(var i in data.chart_data) {
                 item_code.push(data.chart_data[i].item_code);
                 minimum.push(data.chart_data[i].minimum);
                 actual_bar.push(data.chart_data[i].actual_bar);
                 actual_qty.push(data.chart_data[i].actual_qty);
                 minimum_label.push(data.chart_data[i].minimum_label);
                 decsription.push(data.chart_data[i].decsription);
                 c_actual.push(data.chart_data[i].c_actual);
                 c_planned.push(data.chart_data[i].c_planned);
                 c_minimum.push(data.chart_data[i].c_minimum);
              }
              var chartdata = {
                 labels: item_code,
                 data1:decsription,
                 data2:actual_qty,
                 datasets : [{
                    backgroundColor:c_minimum ,
                    data: minimum,
                    lay:minimum_label,
                    item:decsription,
                    label: "Minimum"
                 },{
                    backgroundColor:c_actual,
                    data: actual_bar,
                    lay:actual_qty,
                    label: "Actual Qty"
                 }]
                 
              };
  
              var ctx = $("#powder_coat_Chart");
  
              if (window.powderchart != undefined) {
                 window.powderchart.destroy();
              }
              const totalizer = {
          id: 'totalizer',
  
          beforeUpdate: chart => {
            let totals = {}
            let utmost = 1
  
            chart.data.datasets.forEach((dataset, datasetIndex) => {
              
              totals[datasetIndex] =  actual_qty[datasetIndex]
            })
  
            chart.$totalizer = {
              totals: totals,
              utmost: utmost
            }
          }
        }
              window.powderchart = new Chart(ctx, {
                 type: 'bar',
                 data: chartdata,
                 options: {
                    responsive: true,
                    legend: {
                       position: 'top',
                       labels:{
                          boxWidth: 11
                       }
                    },
                    scales: {
                       xAxes: [{ stacked: true,
                       ticks: {
                      // Include a dollar sign in the ticks
                      callback: function(value, index, values) {
                          return value;
                      }
                  }}],
                  yAxes: [{ stacked: true,
                       ticks: {
                      // Include a dollar sign in the ticks
                      callback: function(value, index, values) {
                          return value + "Kg";
                      }
                  }}]
                    },
                    tooltips: {
                       mode: 'label',
                       callbacks: {
                        title: function (t, d) {
                           return d['data1'][t[0]['index']];
                            },
                          label: function(t, d) {
                             var dstLabel = d.datasets[t.datasetIndex].label;
                             var yLabel = d['datasets'][t.datasetIndex]['lay'][t['index']];
                             var item = d['data1'][1][t['index']];
                             var btn = document.createElement("div");
                             var prod =  dstLabel + ':\n'+ yLabel +"\n KG";
                             var fortooltip= "\ decription: "+item +"\ this " +prod+'\n'+ item;
                              return [prod];
                          }
                       },
                        backgroundColor: '#FFF',
                        titleFontSize: 16,
                        titleFontColor: '#0066ff',
                        bodyFontColor: '#000',
                        bodyFontSize: 14,
                        displayColors: false
                    },
                    animation: {
                  duration: 0 // general animation time
                 },
                    hover: {
                    animationDuration: 0 // duration of animations when hovering an item
                    },
                    responsiveAnimationDuration: 0, // animation duration after a resize
                    plugins: {
              datalabels: {
                formatter: (value, ctx) => {
                  const total = ctx.chart.$totalizer.totals[ctx.dataIndex]
                  return ctx.chart.data.data2[ctx.dataIndex];
  
                },
                align: 'end',
                anchor: 'end',
                display: function(ctx) {
                  return ctx.datasetIndex === ctx.chart.$totalizer.utmost
                }
              }
            }
                 }, plugins: [totalizer]
              });
           },
           error: function(data) {
              alert('Error fetching data!');
           }
        });
     }
  </script>
  <script>
    function tbl_powder_consumed_list(page, query){

        $.ajax({
            url:"/tbl_poweder_coat_consumed_list/?page="+page,
            type:"GET",
            data: query,
            success:function(data){
              $('#tbl_powder_consumed').html(data);
            }
          });
  }
</script>
<script>
  function tbl_painting_stock_list(page, query){

    $.ajax({
        url:"/tbl_painting_stock/?page="+page,
        type:"GET",
        data: query,
        success:function(data){
          $('#tbl_stock_painting').html(data);
        }
      });
  }
</script>
<script>

  $(document).on('click', '.btn-stock-adjust-entry-painting', function(){
        get_itemcode_painting();
        $('#balance_qty_id_painting').val("");
        $('#item_description_input_painting').val("");
        $('#actual_qty_id_painting').text(0);
        $('#planned_qty_id_painting').text(0);
        $('#add-stock-entries-adjustment-painting').modal('show');
        $('#item_desc_div_painting').hide();
        $('#entry_type_div_painting').hide();
    });
    $('#add-stock-entries-adjustment-painting-frm').submit(function(e){
      e.preventDefault();
      var item_code = $('#itemcode_line').val();
      
      if(item_code == "default"){
        showNotification("danger", "Pls Select Item code", "now-ui-icons travel_info");
      }else{
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
            $('#add-stock-entries-adjustment-painting-frm').trigger("reset");
            $('#balance_qty_id_painting').val("");
            $('#item_description_input_painting').val("");
            $('#actual_qty_id_painting').text('0');
            $('#planned_qty_id_painting').text('0');
            $('#add-stock-entries-adjustment-painting').modal('hide');
            $('#item_desc_div_painting').hide();
            $('#entry_type_div_painting').hide();
            tbl_painting_stock_list();
            powder_coat_Chart();
            // inventory_history_list();

                // $('#edit-worktation-frm').trigger("reset");
                // workstation_list();

          } 
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      }); 
      }
    });
    $(document).on('click', '#tbl_painting_stock_pagination a', function(event){
         event.preventDefault();
         var page = $(this).attr('href').split('page=')[1];
         var filter_contents = "inventory_painting";
         var filter_content = "stock-list-painting";
		     var query = $('#inv-search-box').val();
	    	 var filters = 'q=' + query + '&' + $('#' + filter_contents).serialize();
         tbl_painting_stock_list(page, filters);
    });
    $(document).on('click', '#tbl_painting_consumed_pagination a', function(event){
         event.preventDefault();
         var page = $(this).attr('href').split('page=')[1];
         var filter_contents = "consumed_list_powder";
         var filter_content = "powderconsume-list-painting";
		     var query = $('#inv-search-box').val();
	    	 var filters = 'q=' + query + '&' + $('#' + filter_contents).serialize();
         tbl_powder_consumed_list(page, filters);
    });

</script>
<script>
function get_balanceqty_painting(){
        var item_code = $('#itemcode_line_painting').val();
        $.ajax({
          url: "/get_balanceqty_stock_adjustment_entries_painting/"+ item_code,
          method: "GET",
          success: function(data) {
          $('#balance_qty_id_painting').val(data.qty.balance);
          $('#orig_balance_qty_painting').val(data.qty.balance);
          $('#actual_qty_id_painting').text(data.qty.actual);
          $('#planned_qty_id_painting').text(data.qty.planned);
          $('#item_description_label_painting').text(data.qty.description);
          $('#item_description_input_painting').val(data.qty.description);
          $('#entry_type_label_painting').text(data.qty.entry_type);
          $('#entry_type_box_painting').val(data.qty.entry_type);
          $('#item_desc_div_painting').show();
          $('#entry_type_div_painting').show();
          },
          error: function(data) {
          alert(data);
          }
        });
    }
</script>
<script>
function get_itemcode_painting(){
        $.ajax({
          url: "/get_item_code_stock_adjustment_entries_painting",
          method: "GET",
          success: function(data) {
          $('#itemcode_line_painting').html(data);
            
          },
          error: function(data) {
          alert(data);
          }
        });
    }
</script>
<script>
get_stock_painting_filters();
	function get_stock_painting_filters(){
		$.ajax({
			url:"/get_stock_painting_filters",
			type:"GET",
			success:function(data){
				var m = '';
				$.each(data, function(i, d){
					m += '<option value="' + d.item_code + '">' + d.item_code +"-" + d.desc + '</option>';
				});

			

				$('#itemcode_painting_filter').append(m);
        $('#itemcode_consume').append(m);
			}
		});
	}
</script>
<script>
get_consume_painting_filters();
	function get_consume_painting_filters(){
		$.ajax({
			url:"/get_consume_painting_filters",
			type:"GET",
			success:function(data){
				var m = '';
				$.each(data, function(i, d){
					m += '<option value="' + d.operator + '">'  + d.operator + '</option>';
				});

			

				$('#operator_consume').append(m);
			}
		});
	}
</script>
<script>

get_transaction_list_painting();
	function get_transaction_list_painting(page, filters){
		$.ajax({
			url:"/get_inventory_transaction_history_painting/?page=" + page,
			type:"GET",
			data: filters,
			success:function(data){
				$('#tbl_painting_stock_list_history').html(data);
			}
		});
	}
  $(document).on('click', '#tbl_inventory_transactions_painting_pagination a', function(event){
			event.preventDefault();
			var page = $(this).attr('href').split('page=')[1];
			get_transaction_list_painting(page);
	});

  
 
  </script>
<script>
$(document).on('change', '.mr_request_paint', function(event){
      var operation ="Painting";
      var filter_contents = "painting-mr-request";
      var query = $('#inv-search-box').val();
      var page = 1;
      var filterss = 'q=' + query + '&' + $('#' + filter_contents).serialize();
      tbl_painting_material_request_list(operation,page, filterss);

  });
</script> 
<script>
    function tbl_painting_material_request_list(operation,page, query){
      var operation = "Painting";
        $.ajax({
            url:"/tbl_painting_material_request_list/"+operation + "/?page="+page,
            type:"GET",
            data: query,
            success:function(data){
              $('#tbl_material_request_painting').html(data);
            }
          });
  }
</script>

@endsection