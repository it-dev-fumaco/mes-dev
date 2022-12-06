<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/production_floor', 'ProductionFloorController@index');
Route::get('/get_total_output', 'ProductionFloorController@get_total_output');
Route::get('/get_workstation_dashboard_content', 'ProductionFloorController@get_workstation_dashboard_content');
Route::get('/get_ready_for_feedback', 'ProductionFloorController@get_ready_for_feedback');
Route::get('/activity_logs', 'ProductionFloorController@activity_logs');
Route::get('/get_machine_breakdown', 'ProductionFloorController@get_machine_breakdown');

Route::get('/view_conveyor_schedule/{workstation}', 'MainController@view_conveyor_schedule');

Route::get('/', 'MainController@index');

Route::get('/get_checklist/{workstation_name}/{production_order}/{process_id}', 'QualityInspectionController@get_checklist');
Route::post('/submit_quality_inspection', 'QualityInspectionController@submit_quality_inspection');

Route::get('/operator/Painting/{process_name}', 'PaintingOperatorController@painting_index');
Route::get('/operator/Painting/{process_name}/login', 'PaintingOperatorController@loading_login');
Route::get('/get_production_order_details/{production_order}/{process_id}', 'PaintingOperatorController@get_production_order_details');
Route::post('/painting/login', 'PaintingOperatorController@login_operator');
Route::post('/insert_machine_logs', 'PaintingOperatorController@insert_machine_logs');
Route::get('/get_scheduled_for_painting', 'PaintingOperatorController@get_scheduled_for_painting');
Route::get('/get_painting_backlogs', 'PaintingOperatorController@backlogs');
Route::post('/update_maintenance_task', 'MainController@update_maintenance_task');
Route::group(['middleware' => 'auth'], function(){
	Route::get('/allowed_warehouse_for_fast_issuance', 'InventoryController@getAllowedWarehouseFastIssuance');
	Route::post('/save_allowed_warehouse_for_fast_issuance', 'InventoryController@saveAllowedWarehouseFastIssuance');
	Route::post('/delete_allowed_warehouse_for_fast_issuance', 'InventoryController@deleteAllowedWarehouseFastIssuance');
	Route::get('/allowed_user_for_fast_issuance', 'InventoryController@getAllowedUserFastIssuance');
	Route::post('/save_allowed_user_for_fast_issuance', 'InventoryController@saveAllowedUserFastIssuance');
	Route::post('/delete_allowed_user_for_fast_issuance', 'InventoryController@deleteAllowedUserFastIssuance');
	Route::get('/get_items/{item_classification}', 'ManufacturingController@get_items');
	Route::get('/get_mes_warehouse', 'ManufacturingController@get_mes_warehouse');
	Route::post('/update_ste_detail', 'ManufacturingController@update_ste_detail');
	Route::post('/add_ste_items', 'ManufacturingController@add_ste_items');
	Route::get('/operator/Painting/{process_name}/{machine_code}', 'PaintingOperatorController@operator_task');
	Route::get('/get_painting_task/{process}/{operator_id}', 'PaintingOperatorController@get_painting_in_progress');
	Route::get('/get_task/{production_order}/{process_id}/{operator_id}', 'PaintingOperatorController@get_task');
	Route::post('/start_painting', 'PaintingOperatorController@start_task');
	Route::post('/end_painting', 'PaintingOperatorController@end_task');
	Route::post('/restart_painting', 'PaintingOperatorController@restart_task');
	Route::get('/painting/logout/{process_name}', 'PaintingOperatorController@logout');
	Route::post('/reject_painting', 'PaintingOperatorController@reject_task');
	Route::post('/create_feedback/{production_order}', 'PaintingOperatorController@create_stock_entry');
	Route::get('/get_feedbacked_production_order/{schedule_date}', 'PaintingController@get_feedbacked_production_order');
	Route::get('/qa_dashboard', 'QualityInspectionController@qa_dashboard');
	Route::get('/qa_staff_workload', 'QualityInspectionController@qa_staff_workload');
	Route::get('/get_reject_for_confirmation/{operation_id}', 'QualityInspectionController@get_reject_for_confirmation');
	Route::get('/get_quick_view_data', 'QualityInspectionController@get_quick_view_data');
	Route::get('/get_top_defect_count', 'QualityInspectionController@get_top_defect_count');
	Route::get('/get_reject_types/{workstation}/{process_id}', 'QualityInspectionController@get_reject_types');
	Route::post('/submit_stock_entry/{id}', 'ManufacturingController@submit_stock_entry');

	Route::get('/count_reject_for_confirmation', 'QualityInspectionController@count_reject_for_confirmation');
	Route::get('/view_rejection_report', 'QualityInspectionController@viewRejectionReport');
});

Route::get('/get_reject_confirmation_checklist/{production_order}/{workstation_name}/{process_id}/{qa_id}', 'QualityInspectionController@get_reject_confirmation_checklist');
Route::get('/operator_dashboard/{machine}/{workstation}/{production_order}', 'MainController@operatorDashboard');
Route::post('/login_user', 'MainController@loginUserId');
Route::get('/logout_operator/{id}', 'MainController@logout');
Route::post('/machine_breakdown_save', 'SecondaryController@machineBreakdownSave');

// AJAX
Route::get('/get_workstation_task/{workstation}', 'MainController@getWorkstationTask');
Route::get('/settings_module', 'SecondaryController@settings_module');
Route::get('/login', 'MainController@loginUserFrm')->name('login');
Route::get('/logout_user', 'MainController@logoutUser');
// PPC STAFF
Route::group(['middleware' => 'auth'], function(){
	Route::get('/production_fabrication_machine_board/{workstation_id}/{scheduled_date}', 'MainController@production_fabrication_machine_board');
	Route::get('/get_qa_details/{timelog}', 'SecondaryController@qa_details');
	// PAGE
	Route::get('/workstation_overview', 'MainController@workstationOverview');
	Route::get('/item_feedback', 'MainController@itemFeedback');
	Route::get('/production_planning', 'MainController@productionPlanning');
	Route::get('/main_dashboard', 'MainController@mainDashboard');
	Route::get('/production_schedule/{id}', 'MainController@production_schedule_module');
	Route::get('/machine_control', 'MainController@machineControlView');
	Route::get('/operators', 'MainController@operatorList');
	Route::get('/operator_profile/{id}', 'MainController@operatorProfile');
	Route::get('/production_schedule_report', 'MainController@productionSchedule');
	Route::get('/print_job_tickets/{scheduled_date}', 'SecondaryController@printJobTickets');
	Route::get('/single_print_job_ticket/{prod_no}', 'SecondaryController@single_printJobTickets');
	Route::post('/reorder_production/{id}', 'MainController@reorderProdOrder');
	// AJAX
	Route::get('/workstation_task_list', 'MainController@workstationTaskList');
	Route::get('/workstation_sched', 'MainController@getWorkstationSched');
	// FOR UPDATE PROCESS
	Route::get('/get_process_list/{workstation}', 'SecondaryController@get_process_list');
	Route::post('/update_process', 'MainController@update_process');
	Route::post('/mark_as_done_task', 'SecondaryController@mark_as_done_task');
	Route::post('/reset_task', 'SecondaryController@reset_task');
	Route::get('/get_AssignMachineinProcess_jquery/{process_id}/{workstation_id}', 'SecondaryController@get_AssignMachineinProcess_jquery');
	Route::post('/add_helper', 'MainController@add_helper');
	Route::post('/delete_helper', 'MainController@delete_helper');
	Route::get('/get_helpers', 'MainController@get_helpers');
	Route::get('/view_operator_task/{job_ticket}/{operator_id}', 'MainController@view_operator_task');
	Route::get('/production_schedule_per_workstation', 'MainController@production_schedule_per_workstation');
	Route::get('/operators_load_utilization', 'MainController@operators_load_utilization');
	Route::get('/get_reference_production_items/{reference}', 'MainController@get_reference_production_items');
	Route::get('/get_customer_reference_no/{customer}', 'MainController@get_customer_reference_no');
	Route::get('/get_customers', 'MainController@get_customers');
	Route::post('/update_task_schedule/{job_ticket_id}', 'MainController@update_task_schedule');
	Route::post('/update_production_task_schedules', 'MainController@update_production_task_schedules');
	Route::post('/update_production_order_schedule', 'MainController@update_production_order_schedule');
	Route::post('/save_shift_schedule', 'MainController@save_shift_schedule');
	Route::get('/operator_scheduled_task/{workstation}/{process_id}', 'MainController@operator_scheduled_task');	
});

//machine overview
Route::get('/machine_overview', 'SecondaryController@machineOverview');
Route::get('/machine_task_list', 'SecondaryController@machineTaskList');
Route::post('/update_machine_img', 'MainController@update_machine_path');
Route::get('/machine_overview/details_overview', 'SecondaryController@machine_details_tbl');
Route::get('/machine_overview/machine_details_chart/breakdown/{id}', 'SecondaryController@machine_breakdown');
Route::get('/machine_overview/machine_details_chart/corrective/{id}', 'SecondaryController@machine_corrective');
Route::get('/operator/pending_for_maintenance/{id}', 'SecondaryController@get_machines_pending_for_maintenance');
//operator dashboard
Route::get('/operator/Spotwelding', 'SpotweldingController@operator_spotwelding_dashboard');
Route::get('/operator/{id}', 'MainController@operatorpage');
Route::get('/operator/header_total_data/{id}', 'MainController@current_data_operator');
Route::get('/operator/header_table_data/{id}/{name}', 'MainController@operators_workstation_TaskList');
Route::get('/operator/available_machine/{id}', 'SecondaryController@available_machine');
Route::get('/operator/unavailable_machine/{id}', 'SecondaryController@unavailable_machine');
Route::get('/machine_kanban/{workstation}/{schedule_date}', 'SecondaryController@machineKanban');
Route::get('/machine_kanban_tbl/{workstation}/{schedule_date}', 'SecondaryController@machineKanban_tbl');
Route::post('/reorder_productions', 'SecondaryController@reorderProdOrder');
Route::get('/machine_kanban/workstation/{id}/{name}', 'SecondaryController@machine_kanban_workstation');
Route::get('/get_count_unassignedTask/machineKanban/{workstation}/{date}', 'SecondaryController@countUnassignedTasksForOperator');

// WIZARD
Route::group(['middleware' => 'auth'], function(){
	route::get('/machine_list','SecondaryController@goto_machine_list');
	route::post('/save_machine','SecondaryController@insert_machine');
	route::post('/edit_machine','SecondaryController@update_machine');
	route::post('/delete_machine','SecondaryController@delete_machine');
	route::get('/get_machine_list','SecondaryController@get_machine_list_data');
	route::get('/goto_machine_profile/{id}','SecondaryController@get_machine_profile');
	route::get('/workstation_list','SecondaryController@get_workstation_list');
	route::get('/get_tbl_machine_profile','SecondaryController@machine_profile_tbl');
	route::get('/maintenance_machine_list','SecondaryController@maintenanceMachineList');
	route::get('/workstation_profile/{id}','SecondaryController@workstation_profile');
	route::post('/save_workstation_machine','SecondaryController@insert_machineToworkstation');
	route::post('/delete_workstation_machine','SecondaryController@delete_machineToworkstation');
	route::get('/get_machine_to_select','SecondaryController@get_machine_to_select');
	route::post('/save_workstation','SecondaryController@save_workstation');
	route::post('/edit_workstation','SecondaryController@edit_workstation');
	route::post('/delete_workstation','SecondaryController@delete_workstation');
	route::post('/save_process_workstation','SecondaryController@save_process_workstation');
	route::get('/get_tbl_workstation_process','SecondaryController@get_tbl_workstation_process');
	route::get('/get_tbl_workstation_machine','SecondaryController@get_tbl_workstation_machine');
	route::post('/delete_process_workstation','SecondaryController@delete_process_workstation');
	route::post('/save_machine_process','SecondaryController@save_machine_process');
	route::post('/delete_machine_process','SecondaryController@delete_machine_process');
	route::get('/get_workstation_process_jquery/{id}','SecondaryController@get_workstation_process_jquery');
	route::post('/save_process','SecondaryController@save_process');
	route::get('/process_profile/{id}','SecondaryController@process_profile');
	route::get('/get_tbl_assigned_workstation_process','SecondaryController@get_tbl_assigned_workstation_process');
	route::get('/get_tbl_assigned_machine_process','SecondaryController@get_tbl_assigned_machine_process');
	route::get('/get_machine_assignment_jquery/{id}','SecondaryController@get_machine_assignment_jquery');
	route::get('/get_AssignProcessinMachine_jquery/{id}/{workstation}','SecondaryController@get_AssignProcessinMachine_jquery');
	route::post('/process_assignment','SecondaryController@process_assignment');
	route::get('/get_tbl_assigned_machine_process/{id}','SecondaryController@tbl_assigned_machine_process');
	route::get('/get_tbl_process_setup_list','SecondaryController@tbl_process_setup_list');
	route::get('/get_tbl_machine_setup_list','SecondaryController@tbl_machine_setup_list');
	route::post('/update_process_setup_list','SecondaryController@Update_process_setup_list');
	route::post('/delete_process_setup_list','SecondaryController@Delete_process_setup_list');
	Route::get('/get_production_order_details/{id}/{workstation}/{machine}', 'MainController@get_production_order_details');
	Route::get('/machineKanban_view_machineList/{id}/{workstation}/{machine}', 'SecondaryController@machineKanban_view_machineList');
	Route::get('/view_override_form/{production_order}', 'MainController@viewOverrideForm');
	Route::post('/update_override_production_form', 'MainController@updateOverrideProduction');
	Route::get('/send_feedback_email', 'MainController@sendFeedbackEmail');
	Route::get('/planning_wizard/no_bom', 'ManufacturingController@wizardNoBom');
	Route::post('/create_production_order_without_bom', 'ManufacturingController@create_production_order_without_bom');
	Route::get('/view_operations_wizard', 'ManufacturingController@viewAddOperationsWizard');
	Route::get('/get_production_order_material_status', 'InventoryController@getProductionOrderMaterialStatus');
	Route::get('/view_bundle/{item_code}', 'ManufacturingController@view_bundle_components');
	Route::post('/create_material_transfer_for_return', 'ManufacturingController@create_material_transfer_for_return');
	Route::get('/get_items_for_return/{production_order}', 'ManufacturingController@get_items_for_return');
	// assembly wizard
	Route::get('/assembly/wizard', 'AssemblyController@wizard');
	Route::get('/assembly/get_reference_details/{reference_type}/{id}', 'AssemblyController@get_reference_details');
	Route::get('/assembly/get_parts', 'AssemblyController@get_parts');
	Route::get('/assembly/get_production_req_items', 'AssemblyController@get_production_req_items');
	Route::post('/assembly/create_stock_entry', 'AssemblyController@create_draft_stock_entry');
	Route::post('/assembly/submit_change_raw_material', 'AssemblyController@submit_change_raw_material');
	Route::get('/assembly/get_raw_materials_item/{item_classification}', 'AssemblyController@get_raw_materials_item');
	Route::get('/get_reference_list/{reference_type}', 'MainController@get_reference_list');
	Route::get('/get_parent_code/{reference_type}/{reference_no}', 'MainController@get_parent_code');
	Route::get('/get_sub_parent_code/{parent_code}', 'MainController@get_sub_parent_code');
	// PAGE
	Route::get('/wizard', 'ManufacturingController@wizard');
	Route::get('/get_target_warehouse/{operation_id}', 'MainController@get_target_warehouse');
	// AJAX
	Route::get('/get_material_request_details/{id}', 'ManufacturingController@get_material_request_details');
	Route::get('/get_sales_order_items', 'ManufacturingController@get_sales_order_items');	
	Route::get('/get_sales_order_details/{id}', 'ManufacturingController@get_sales_order_details');
	Route::get('/view_bom/{bom}', 'ManufacturingController@view_bom');
	Route::get('/view_bom_for_review/{bom}', 'ManufacturingController@view_bom_for_review');
	Route::get('/get_parts', 'ManufacturingController@get_parts');
	Route::get('/get_workstation_process/{workstation}', 'ManufacturingController@get_workstation_process');
	Route::get('/get_warehouses/{operation}/{item_classification}', 'ManufacturingController@get_warehouses');
	Route::get('/get_production_req_items', 'ManufacturingController@get_production_req_items');
	Route::get('/get_actual_qty/{item_code}/{warehouse}', 'ManufacturingController@get_actual_qty');
	Route::get('/production_planning_summary', 'ManufacturingController@production_planning_summary');
	Route::get('/print_withdrawals', 'ManufacturingController@print_withdrawals');
	Route::get('/get_scheduled_production', 'ManufacturingController@get_scheduled_production');
	Route::post('/submit_bom_review/{bom}', 'ManufacturingController@submit_bom_review');
	Route::post('/create_production_order', 'ManufacturingController@create_production_order');
	Route::post('/create_material_request', 'ManufacturingController@create_material_request');
	Route::post('/create_stock_entry', 'ManufacturingController@create_stock_entry');
	Route::post('/cancel_production_order', 'ManufacturingController@cancel_production_order');
	Route::post('/close_production_order', 'ManufacturingController@close_production_order');
	Route::post('/reopen_production_order', 'ManufacturingController@reopen_production_order');
	Route::get('/get_reason_for_cancellation', 'ManufacturingController@get_reason_for_cancellation');
	Route::post('/manual_create_production_order', 'ManufacturingController@manual_create_production_order');
	Route::get('/get_item_details/{id}', 'MainController@get_item_details');
	Route::get('/get_item_bom/{id}', 'MainController@get_item_bom');
	Route::get('/get_reference_details/{reference_type}/{reference_no}', 'MainController@get_reference_details');
	Route::get('/get_machine_status_per_operation/{operation_id}', 'MainController@get_machine_status_per_operation');
	// BOM CRUD
	Route::get('/bom', 'ManufacturingController@view_bom_list');
	Route::get('/get_bom_list', 'ManufacturingController@get_bom_list');
	Route::get('/get_bom_details/{bom}', 'ManufacturingController@get_bom_details');
	Route::get('/create_bom', 'ManufacturingController@create_bom');
	Route::get('/get_for_feedback_production', 'MainController@get_for_feedback_production');
	Route::get('/production_order_list/{status}', 'MainController@get_production_order_list');
	// Maintenance Request
	Route::get('/maintenance_request', 'MainController@maintenance_request');
	Route::get('/maintenance_request_list', 'MainController@maintenance_request_list');
	Route::post('/save_maintenance_request', 'SecondaryController@saveMaintenanceRequest');
	Route::get('/dashboard_operator_output', 'MainController@dashboardOperatorOutput');
	Route::post('/update_maintenance_request/{machine_breakdown_id}', 'MainController@update_maintenance_request');
	// Stock Entry
	Route::get('/stock_entry', 'MainController@stock_entry');
	Route::get('/get_users', 'MainController@get_users');
	Route::post('/save_user', 'MainController@save_user');
	Route::post('/update_user', 'MainController@update_user');
	Route::post('/delete_user', 'MainController@delete_user');
	Route::post('/start_spotwelding', 'SpotweldingController@start_task');
	Route::post('/end_spotwelding', 'SpotweldingController@end_task');
	Route::post('/restart_spotwelding', 'SpotweldingController@restart_task');
	Route::post('/continue_log_task/{timelog_id}', 'SpotweldingController@continue_log_task');
	Route::post('/create_stock_entry/{production_order}', 'MainController@create_stock_entry');
	Route::get('/create_bundle_feedback/{production_order}/{fg_completed_qty}', 'ManufacturingController@create_production_feedback_for_item_bundle');
	Route::get('/create_gl_entry/{stock_entry}', 'MainController@create_gl_entry');
	Route::get('/get_tbl_notif_dashboard', 'MainController@get_tbl_notif_dashboard');
	Route::get('/get_tbl_warnings_dashboard', 'MainController@get_tbl_warnings_dashboard');
	Route::get('/get_fabrication_inventory_data', 'SecondaryController@get_fabrication_inventory_data');
	Route::get('/stock_adjustment_entries_page', 'SecondaryController@stock_adjustment_entries_page');
	Route::get('/get_item_code_stock_adjustment_entries', 'SecondaryController@get_item_code_stock_adjustment_entries');
	Route::get('/get_balanceqty_stock_adjustment_entries/{id}', 'SecondaryController@get_balanceqty_stock_adjustment_entries');
	Route::post('/submit/stock_entries_adjustment', 'SecondaryController@submit_stock_entries_adjustment');
	Route::get('/get_tbl_stock_adjustment_entry', 'SecondaryController@get_tbl_stock_adjustment_entry');
	Route::get('/get_fabrication_inventory_history_list', 'SecondaryController@get_fabrication_inventory_history_list');
	route::get('/get_tbl_workstation_list','SecondaryController@get_tbl_workstation_list');
	Route::get('/get_tbl_setting_machine_list', 'SecondaryController@get_tbl_setting_machine_list');
	Route::post('/create_stock_entry/{production_order}', 'MainController@create_stock_entry');
	Route::get('/selected_print_job_tickets/{scheduled_date}', 'SecondaryController@selected_printJobTickets');
	Route::get('/production_schedule_painting', 'SecondaryController@get_production_painting');
	Route::post('/reorder_production_painting', 'SecondaryController@reorder_production_painting');
	Route::get('/production_schedule_calendar_painting', 'PaintingController@production_schedule_calendar_painting');
	Route::get('/get_production_schedule_calendar_painting', 'PaintingController@get_production_schedule_calendar_painting');
	Route::post('/hide_reject', 'SecondaryController@hidereject_notif_dash');
	Route::get('/operator_item_produced_report', 'SecondaryController@operator_item_produced_report');
	Route::get('/export/view/{date1}/{date2}/{workstation}/{process}/{parts}/{item_code}', 'SecondaryController@export_view');

	Route::get('/getProductionOrderRejectForConfirmation/{production_order}', 'QualityInspectionController@getProductionOrderRejectForConfirmation');
	Route::post('/submitRejectConfirmation/{production_order}', 'QualityInspectionController@submitRejectConfirmation');
	
});
//painting_calendar
Route::group(['middleware' => 'auth'], function(){
	Route::post('/calendar_painting/update_planned_start_date', 'PaintingController@update_planned_start_date_painting');
    Route::post('/calendar_painting/update_planned_start_date_by_click', 
        ['uses' => 'PaintingController@update_planned_start_date_by_click_painting', 'as' => 'painting.ajax_update']);
});
// Quality Check
Route::get('/get_production_quality_check/{production_order}/{workstation}/{machine}/{type}', 'MainController@get_production_quality_check');
Route::post('/submit_quality_check', 'MainController@submit_quality_check');
Route::get('/validate_jt_for_qa/{id}/{workstation}', 'MainController@validateJtQa');
Route::get('/get_items_for_qa/{id}/{workstation}/{type}', 'MainController@getItemsForQc');
// GET TASK OPERATOR DASHBOARD
Route::get('/validate_workstation_machine/{machine}/{workstation}', 'MainController@validate_workstation_machine');
Route::get('/get_production_order_task/{production_order}/{workstation}', 'MainController@get_production_order_task');
Route::post('/start_unassigned_task', 'MainController@start_unassigned_task');
Route::get('/get_workstation_process_machine/{workstation}/{process_id}', 'MainController@get_workstation_process_machine');
Route::post('/login_operator_via_jt', 'MainController@login_operator');
Route::get('/get_current_operator_task_details/{operator_id}', 'MainController@get_current_operator_task_details');
Route::get('/operator_dashboard/{machine}/{workstation}/{job_ticket_id}', 'MainController@operatorDashboard');
Route::get('/get_assigned_tasks/{workstation}/{machine_code}', 'MainController@assignedTasks');
Route::post('/end_task', 'MainController@endTask');
Route::post('/restart_task', 'MainController@restart_task');
Route::post('/reject_task', 'MainController@reject_task');
Route::get('/get_workstation_process_batch', 'MainController@get_workstation_process_batch');
Route::get('/get_jt_details/{jtno}', 'MainController@getTimesheetDetails');
// Route::post('/status_reset', 'ManufacturingController@time_log_reset');//!!
Route::post('/log_delete', 'ManufacturingController@time_log_delete');//!!
Route::get('/random_inspect_task/{job_ticket_id}', 'MainController@random_inspect_task');
Route::get('/reject_confirmation_task/{job_ticket_id}', 'MainController@reject_confirmation_task');
Route::get('/get_tasks_for_inspection/{workstation}/{production_order}', 'MainController@get_tasks_for_inspection');
//production_scheduling_PATRICK
Route::get('/production_scheduling_tbl', 'SecondaryController@tbl_production_scheduling');
Route::post('/reset_operator_time_log', 'MainController@reset_operator_time_log');
Route::post('/edit_operator_time_log', 'MainController@edit_operator_time_log');
//fabrication_calendar
Route::group(['middleware' => 'auth'], function(){
	Route::post('/update_parent_code/{production_order}', 'MainController@updateParentCode');
	Route::get('/qa_monitoring_summary/{schedule_date}', 'SecondaryController@qa_monitoring_summary');
	Route::get('/production_schedule_monitoring/{operation}/{schedule_date}', 'MainController@production_schedule_monitoring');
	Route::get('/production_schedule_monitoring_filters/{operation}/{schedule_date}', 'MainController@production_schedule_monitoring_filters');
	Route::post('/calendar/update_planned_start_date', 'SecondaryController@update_planned_start_date');
    Route::post('/add_shift_schedule', 
        ['uses' => 'SecondaryController@add_shift_schedule', 'as' => 'fabrication.ajax_update']);
});
Route::post('/add_shift_schedule_prod', 'SecondaryController@add_shift_schedule');
Route::get('/maintenance_schedules_per_operation/{operation_id}', 'MainController@maintenance_schedules_per_operation');
///revise MainDashboard Patrick 
Route::get('/get_production_order_list/{date}', 'SecondaryController@get_production_order_list');
Route::get('/count_current_production_order/{date}', 'SecondaryController@count_current_production_order');
Route::post('/add_shift', 'SecondaryController@add_shift');
Route::post('/edit_shift', 'SecondaryController@edit_shift');
Route::post('/delete_shift', 'SecondaryController@delete_shift');
Route::get('/get_tbl_shift_list', 'SecondaryController@tbl_shift_list');
Route::post('/add_operation', 'SecondaryController@add_operation');
Route::post('/edit_operation', 'SecondaryController@edit_operation');
Route::get('/get_tbl_operation_list', 'SecondaryController@tbl_operation_list');
Route::post('/edit_shift_schedule', 'SecondaryController@edit_shift_schedule');
Route::post('/delete_shift_schedule', 'SecondaryController@delete_shift_sched');
Route::get('/get_tbl_shiftsched_list', 'SecondaryController@get_tbl_shiftsched_list');
Route::get('/get_shift_details/{id}', 'SecondaryController@get_shift_details');
Route::get('/shift', 'SecondaryController@shift_page');
Route::get('/get_shift_list_option', 'SecondaryController@get_shift_list_option');
Route::get('/item_status_tracking', 'TrackingController@item_status_tracking_page');
Route::get('/get_item_status_tracking', 'TrackingController@get_item_status_tracking');
Route::get('/get_search_information_details', 'TrackingController@get_search_information_details');
Route::get('/get_bom_tracking', 'TrackingController@get_bom_tracking');
Route::get('/checkNewOrders', 'MainController@checkNewOrders');
Route::get('/production_schedule_calendar/{id}', 'SecondaryController@production_schedule_calendar');
Route::get('/get_production_schedule_calendar/{id}', 'SecondaryController@get_production_schedule_calendar');
Route::get('/get_production_details_for_edit/{prod}', 'SecondaryController@get_production_details_for_edit');
//Reject Checklist
Route::get('/get_operation_checklist_jquery', 'SecondaryController@get_operation_checklist_jquery');
Route::get('/get_workstation_checklist_jquery/{id}', 'SecondaryController@get_workstation_checklist_jquery');
Route::get('/get_process_checklist_jquery/{id}', 'SecondaryController@get_process_checklist_jquery');
Route::post('/save_checklist', 'SecondaryController@save_checklist');
Route::get('/get_tbl_checklist_list', 'SecondaryController@get_tbl_checklist_list');
Route::post('/update_checklist', 'SecondaryController@update_checklist');
Route::post('/delete_checklist', 'SecondaryController@delete_checklist');
//Production_Scheduling_Monitoritng_Assembly
Route::get('/production_schedule_monitoring_assembly/{id}', 'AssemblyController@production_schedule_monitoring_assembly');
Route::get('/get_feedbacked_production_order_assembly/{schedule_date}', 'AssemblyController@get_feedbacked_production_order_assembly');
Route::get('/get_reject_assembly_production_order/{schedule_date}', 'AssemblyController@get_reject_assembly_production_order');
Route::get('/count_current_assembly_production_schedule_monitoring/{date}', 'AssemblyController@count_current_assembly_production_schedule_monitoring');
Route::post('/move_today_task_assembly', 'AssemblyController@move_today_task');
Route::get('/get_scheduled_production_order/{operation_id}/{scheduled_date}', 'AssemblyController@get_scheduled_production_order');
Route::get('/get_production_sched_assembly/{date}', 'AssemblyController@get_production_schedule_monitoring_list_assembly');
Route::get('/get_production_sched_assembly_backlog/{date}', 'AssemblyController@get_production_schedule_monitoring_list_backlogs');
Route::get('/get_production_sched_assembly_view_process/{id}', 'AssemblyController@get_production_sched_assembly_view_process');
Route::post('/edit_cpt_status_qty', 'SecondaryController@edit_cpt_status_qty');
//painting_material_request
Route::get('/tbl_painting_material_request_list/{id}', 'InventoryController@tbl_material_request_list');
//production_schedule_monitoring
Route::get('/production_schedule_monitoring/{date}', 'SecondaryController@production_schedule_monitoring');
Route::get('/get_production_schedule_monitoring_list/{date}', 'SecondaryController@get_production_schedule_monitoring_list');
Route::get('/get_production_schedule_monitoring_list_backlogs/{date}', 'SecondaryController@get_production_schedule_monitoring_list_backlogs');
Route::get('/count_current_painting_production_schedule_monitoring/{date}', 'SecondaryController@count_current_painting_production_schedule_monitoring');
Route::post('/move_today_task', 'SecondaryController@move_today_task');
Route::post('/addnotes_task', 'SecondaryController@add_notes_task');
Route::get('/print_production_sched/{date}', 'SecondaryController@get_scheduled_for_painting');
Route::get('/get_production_details_for_edit/{prod}', 'SecondaryController@get_production_details_for_edit');
Route::post('/edit_cpt_status_qty', 'SecondaryController@edit_cpt_status_qty');
//spotwelding_production_order_search
Route::get('/spotwelding_production_order_search/{id}', 'SecondaryController@spotwelding_exploded_production_order_search');
Route::get('/view_operator_task/{job_ticket}/{operator_id}', 'MainController@view_operator_task');
Route::get('/spotwelding_dashboard/{machine}/{production_order}', 'SpotweldingController@spotwelding_dashboard');
Route::get('/get_spotwelding_current_operator_task_details/{operator_id}', 'SpotweldingController@get_spotwelding_current_operator_task_details');
Route::get('/get_production_order_bom_parts/{production_order}', 'SpotweldingController@get_production_order_bom_parts');
Route::get('/logout_spotwelding', 'SpotweldingController@logout_spotwelding');
Route::get('/get_spotwelding_part_remaining_qty', 'SpotweldingController@get_spotwelding_part_remaining_qty');
Route::get('/view_spotwelding_operator_task/{job_ticket}/{operator_id}', 'SpotweldingController@view_operator_task');
Route::post('/reject_task_spotwelding', 'SpotweldingController@update_task_reject');
Route::get('/get_scrap_to_process/{workstation}', 'InventoryController@get_scrap_to_process');
Route::get('/get_process/{workstation}', 'InventoryController@get_process');
Route::post('/insert_scrap_job_ticket', 'InventoryController@insert_scrap_job_ticket');
//water_discharged_monitoring
Route::get('/get_water_discharged_modal_details', 'SecondaryController@get_water_discharged_modal_details');
Route::post('/submit_water_discharge_monitoring', 'SecondaryController@submit_water_discharge_monitoring');
//painting_chemical_records
Route::get('/get_chemical_records_modal_details', 'SecondaryController@get_chemical_records_modal_details');
Route::post('/submit_painting_chemical_records', 'SecondaryController@submit_painting_chemical_records');
// qa_checklist
Route::get('/get_reject_type_desc', 'SecondaryController@get_reject_type_desc');
Route::get('/get_reject_desc/{reject_type}/{id}/{operation}', 'SecondaryController@get_reject_desc');
Route::post('/save_checklist', 'SecondaryController@save_checklist');
Route::get('/get_tbl_checklist_list_fabrication', 'SecondaryController@get_tbl_checklist_list_fabrication');
Route::get('/get_tbl_checklist_list_painting', 'SecondaryController@get_tbl_checklist_list_painting');
Route::get('/get_tbl_checklist_list_assembly', 'SecondaryController@get_tbl_checklist_list_assembly');
Route::get('/get_workstation_list_from_checklist/{id}', 'SecondaryController@get_workstation_list_from_checklist');
Route::get('/get_reject_category_for_add_reject_modal', 'SecondaryController@get_reject_category_for_add_reject_modal');
Route::post('/delete_checklist', 'SecondaryController@delete_checklist');
//reject_list
Route::post('/save_reject_list', 'SecondaryController@save_reject_list');
Route::get('/get_tbl_qa_reject_list', 'SecondaryController@get_tbl_qa_reject_list');
Route::post('/update_reject_list', 'SecondaryController@update_reject_list');
Route::post('/delete_rejectlist', 'SecondaryController@delete_rejectlist');
Route::get('/get_tbl_op_reject_list', 'SecondaryController@get_tbl_op_reject_list');
//reject_category
Route::post('/save_reject_category', 'SecondaryController@save_reject_category');
Route::post('/update_reject_category', 'SecondaryController@update_reject_category');
Route::get('/get_tbl_reject_category', 'SecondaryController@get_tbl_reject_category');
Route::post('/delete_reject_category', 'SecondaryController@delete_reject_category');
//sampling_plan_setup
Route::get('/get_tbl_qa_visual', 'SecondaryController@get_tbl_qa_visual');
Route::get('/get_tbl_qa_variable', 'SecondaryController@get_tbl_qa_variable');
Route::get('/get_tbl_qa_reliability', 'SecondaryController@get_tbl_qa_reliability');
Route::post('/save_sampling_plan', 'SecondaryController@save_sampling_plan');
Route::post('/delete_sampling_plan', 'SecondaryController@delete_sampling_plan');
Route::get('/get_max_for_min_sampling_plan/{id}', 'SecondaryController@get_max_for_min_sampling_plan');
//user-group
Route::get('/get_user_role_by_module/{id}', 'SecondaryController@get_user_role_by_module');
Route::get('/get_users_group', 'SecondaryController@get_users_group');
Route::post('/save_user_group', 'SecondaryController@save_user_group');
Route::post('/update_user_group', 'SecondaryController@update_user_group');
Route::post('/delete_user_group', 'SecondaryController@delete_user_group');
//QA_inspection_log_report
Route::get('/tbl_qa_inspection_log_report', 'QualityInspectionController@tbl_qa_inspection_log_report');
Route::get('/qa_logs_filters', 'QualityInspectionController@qa_logs_filters');
Route::get('/get_tbl_qa_inspection_log_export/{start}/{end}/{workstation}/{customer}/{prod}/{item_code}/{status}/{processs}/{qa_inspector}/{operator}', 'QualityInspectionController@get_tbl_qa_inspection_log_export');
//item_classification_warehouse_setup
Route::post('/save_item_classification_warehouse', 'SecondaryController@insert_item_classification_warehouse');
Route::post('/edit_item_classification_warehouse', 'SecondaryController@update_item_classification_warehouse');
Route::post('/delete_item_classification_warehouse', 'SecondaryController@delete_item_classification_warehouse');
Route::get('/get_selection_box_in_item_class_warehouse', 'SecondaryController@get_selection_box_in_item_class_warehouse');
Route::get('/item_classification_warehouse_tbl_fabrication', 'SecondaryController@item_classification_warehouse_tbl_fabrication');
Route::get('/item_classification_warehouse_tbl_painting', 'SecondaryController@item_classification_warehouse_tbl_painting');
Route::get('/item_classification_warehouse_tbl_assembly', 'SecondaryController@item_classification_warehouse_tbl_assembly');
//item_group_item_warehouse_setup
Route::get('/get_item_class_based_on_item_group/{id}', 'SecondaryController@get_item_class_based_on_item_group');
//wip setup
Route::post('/save_wip', 'InventoryController@save_wip');
Route::get('/tbl_wip_list', 'InventoryController@tbl_wip_list');
Route::post('/edit_wip', 'InventoryController@edit_wip');
Route::post('/delete_wip', 'InventoryController@delete_wip');
//painting_reports
Route::get('/painting_reports', 'PaintingController@painting_reports');
Route::get('/get_tbl_report_painting_chemical', 'PaintingController@get_tbl_report_painting_chemical');
Route::get('/get_tbl_report_painting_chemical_filter/{fromdate}/{todate}/{free}/{replen}/{acce}', 'PaintingController@get_tbl_report_painting_chemical_filter');
Route::get('/get_tbl_report_painting_chemical_export/{fromdate}/{todate}/{free}/{replen}/{acce}', 'PaintingController@get_tbl_report_painting_chemical_export');
Route::get('/get_tbl_water_discharged', 'PaintingController@get_tbl_water_discharged');
Route::get('/get_tbl_report_painting_water_discharge_filter/{fromdate}/{todate}/{hrs}', 'PaintingController@get_tbl_report_painting_water_discharge_filter');
Route::get('/get_tbl_report_painting_water_discharge_export/{fromdate}/{todate}/{hrs}', 'PaintingController@get_tbl_report_painting_water_discharge_export');
//powder_coating_monitoring
Route::get('/get_powder_records_modal_details', 'PaintingOperatorController@get_powder_modal_details');
Route::get('/get_pwder_coat_desc/{id}', 'PaintingOperatorController@get_pwder_coat_desc');
Route::post('/submit_powder_record_monitoring', 'PaintingOperatorController@submit_powder_record_monitoring');
Route::get('/get_powder_coat_chart', 'SecondaryController@get_powder_coat_chart');
Route::get('/tbl_poweder_coat_consumed_list', 'SecondaryController@tbl_poweder_coat_consumed_list');
Route::get('/tbl_painting_stock', 'SecondaryController@tbl_painting_stock');
Route::get('/tbl_comsumed_filter_box', 'SecondaryController@tbl_comsumed_filter_box');
Route::get('/get_item_code_stock_adjustment_entries_painting', 'SecondaryController@get_item_code_stock_adjustment_entries_painting');
Route::get('/get_balanceqty_stock_adjustment_entries_painting/{id}', 'SecondaryController@get_balanceqty_stock_adjustment_entries_painting');
Route::post('/submit/stock_entries_adjustment/painting', 'SecondaryController@submit_stock_entries_adjustment_painting');
Route::get('/get_stock_painting_filters', 'SecondaryController@get_stock_painting_filters');
Route::get('/tbl_filter_stock_inventory_box', 'SecondaryController@tbl_filter_stock_inventory_box');
Route::get('/get_consume_painting_filters', 'SecondaryController@get_consume_painting_filters');
Route::get('/get_powder_coat_item', 'SecondaryController@get_powder_coat_item');
Route::get('/get_powder_coat_desc/{id}', 'SecondaryController@get_powder_coat_desc');
Route::get('/get_inventory_transaction_history_painting', 'InventoryController@get_inventory_transaction_history_painting');
//shift_break_time
Route::get('/show_edit_shift_break_time/{id}', 'SecondaryController@show_edit_shift_break_time');
//raw_monitoring
Route::get('/raw_material_monitoring_data_diff', 'InventoryController@raw_material_monitoring_data_diff');
Route::get('/raw_material_monitoring_data_crs', 'InventoryController@raw_material_monitoring_data_crs');
Route::get('/raw_material_monitoring_data_alum', 'InventoryController@raw_material_monitoring_data_alum');
Route::get('/out_of_stock', 'InventoryController@alum_out_of_stock');
Route::get('/tbl_filter_out_of_stock', 'InventoryController@tbl_filter_out_of_stock');
Route::get('/get_raw_filters/{operation}', 'InventoryController@get_scrap_filters');
//material request
Route::get('/material_request', 'InventoryController@material_request');
Route::post('/save_material_purchase', 'InventoryController@save_material_purchase');
Route::get('/get_tbl_material_request', 'InventoryController@list_material_purchase');
Route::get('/get_selection_box_in_item_code_warehouse/{id}', 'InventoryController@get_selection_box_in_item_code_warehouse');
Route::post('/cancel_material_purchase_request', 'InventoryController@cancel_material_purchase_request');
Route::get('/get_material_request_for_purchase/{id}', 'InventoryController@get_material_request_for_purchase');
Route::get('/tbl_filter_material_purchase_request/{from}/{to}', 'InventoryController@tbl_filter_material_purchase_request');
Route::get('/get_uom_item_selected_in_purchase/{id}', 'InventoryController@get_uom_item_selected_in_purchase');
//material transfer
Route::post('/save_material_transfer', 'InventoryController@save_material_transfer');
Route::get('/get_tbl_material_transfer', 'InventoryController@list_material_transfer');
Route::get('/get_material_transfer_details/{id}', 'InventoryController@get_material_tranfer');
Route::post('/cancel_material_transfer', 'InventoryController@cancel_material_transfer');
Route::get('/tbl_filter_material_transfer/{from}/{to}', 'InventoryController@tbl_filter_material_transfer');
Route::post('/confirmed_material_transfer', 'InventoryController@confirmed_material_transfer');
Route::post('/delete_material_transfer', 'InventoryController@delete_material_transfer');
//production_order with stock_entry
Route::get('/get_stock_entry_details/{id}', 'SecondaryController@get_stock_entry_details');
Route::get('/get_stock_entry_exist/{id}', 'SecondaryController@get_stock_entry_exist');
Route::get('/production_notif_fab_count/{id}', 'SecondaryController@count_production_notification');
Route::get('/count_production_notification_notstarted', 'SecondaryController@count_production_notification_notstarted');
Route::get('/get_all_prod_notif/{id}', 'SecondaryController@get_all_prod_notif');
Route::post('/reschedule_production_from_notif', 'SecondaryController@reschedule_production_from_notif');
Route::get('/reload_pro_scheduled', 'TrackingController@productionKanban');
Route::get('/get_notif_filters/{id}', 'SecondaryController@get_notif_filters');
Route::post('/cancel_production_from_notif', 'SecondaryController@cancel_production_from_notif');
Route::get('/get_all_prod_notif_inprogress/{id}', 'SecondaryController@get_all_prod_notif_inprogress');
Route::post('/complete_production_from_notif', 'SecondaryController@complete_production_from_notif');
Route::post('/unschedule_production_from_notif', 'SecondaryController@unschedule_production_from_notif');
Route::get('/get_production_order_items/{production_order}', 'ManufacturingController@get_production_order_items');
Route::post('/generate_stock_entry/{production_order}', 'ManufacturingController@generate_stock_entry');
Route::get('/selected_print_fg_transfer_slip/{production_order}', 'MainController@selected_print_fg_transfer_slip');
//email_trans_setup
Route::get('/get_employee_email', 'SecondaryController@get_employee_email');
Route::post('/save_add_email_trans', 'SecondaryController@save_add_email_trans');
Route::get('/get_tbl_email_trans_list', 'SecondaryController@get_tbl_email_trans_list');
Route::post('/delete_email_recipient', 'SecondaryController@delete_email_recipient');
Route::get('/drag_n_drop/{production_order}', 'MainController@drag_n_drop');
Route::get('/get_feedback_logs/{prod}', 'SecondaryController@get_feedbacked_log'); // N

Route::group(['middleware' => 'auth'], function(){
	Route::get('view_order_detail/{id}', 'MainController@viewOrderDetails');
	Route::get('/order_list', 'MainController@viewOrderList');
	Route::get('/get_order_list', 'MainController@getOrderList');
	Route::post('/submit_withdrawal_slip', 'ManufacturingController@submit_withdrawal_slip');
	Route::post('/reschedule_delivery/{id}', 'MainController@reschedule_delivery');
	Route::get('/get_available_warehouse_qty/{item_code}', 'ManufacturingController@get_available_warehouse_qty');
	Route::get('/get_pending_material_transfer_for_manufacture/{production_order}', 'MainController@get_pending_material_transfer_for_manufacture');
	Route::post('/cancel_request/{production_order}', 'MainController@delete_pending_material_transfer_for_manufacture');
	Route::post('/cancel_return/{sted_id}', 'MainController@delete_pending_material_transfer_for_return');
	Route::post('/update_production_order_item_required_qty', 'ManufacturingController@update_production_order_item_required_qty');
	Route::post('/submit_stock_entries/{production_order}', 'ManufacturingController@submit_stock_entries');
	Route::post('/generate_material_request', 'InventoryController@generate_material_request');
	Route::get('/painting_dashboard', 'PaintingController@mainDashboard');
	Route::get('/get_painting_production_order_list/{date}', 'PaintingController@get_production_order_list');
	Route::get('/count_current_painting_production_order/{date}', 'PaintingController@count_current_production_order');
	Route::get('/get_painting_notif_dashboard', 'PaintingController@get_tbl_notif_dashboard');
	Route::get('/painting_production_orders', 'PaintingController@itemFeedback');
	Route::get('/get_painting_open_production_orders/{reference_type}', 'PaintingController@get_open_production_orders');
	Route::get('/get_painting_for_feedback_production', 'PaintingController@get_for_feedback_production');
	Route::get('/get_painting_cancelled_production_orders', 'PaintingController@get_cancelled_production_orders');
	Route::post('/sync_production_order_items/{production_order}', 'ManufacturingController@sync_production_order_items');
	Route::get('/idle_machines', 'MainController@idleMachines');
	Route::get('/idle_operators', 'MainController@idleOperators');
	Route::get('/dashboard_numbers', 'MainController@dashboardNumbers');
	Route::get('/dashboard_rejections', 'MainController@rejectionListToday');
	Route::get('/production_settings', 'MainController@productionSettings');
	Route::get('/inventory_settings', 'MainController@inventorySettings');
	Route::get('/qa_settings', 'MainController@qaSettings');
	Route::get('/user_settings', 'MainController@userSettings');
	Route::get('/orderTypes', 'MainController@orderTypes');
	Route::get('/dashboard_operator_output', 'MainController@dashboardOperatorOutput');
	Route::get('/print_order/{order_id}', 'MainController@printOrder');
});
//operator_checklist
Route::post('/save_operator_checklist', 'SecondaryController@save_operator_checklist');
Route::post('/delete_operator_checklist', 'SecondaryController@delete_operator_checklist');
Route::get('/get_tbl_opchecklist_list_fabrication', 'SecondaryController@get_tbl_opchecklist_list_fabrication');
Route::get('/get_tbl_opchecklist_list_painting', 'SecondaryController@get_tbl_opchecklist_list_painting');
Route::get('/get_tbl_opchecklist_list_assembly', 'SecondaryController@get_tbl_opchecklist_list_assembly');
//reschedule delivery date
Route::post('/update_rescheduled_delivery_date', 'MainController@update_rescheduled_delivery_date');
Route::post('/edit_late_delivery_reason', 'SecondaryController@update_late_delivery');
Route::post('/delete_late_delivery_reason', 'SecondaryController@delete_late_delivery_reason');
Route::get('/get_late_delivery', 'SecondaryController@get_tbl_late_delivery');
Route::post('/save_late_delivery_reason', 'SecondaryController@save_late_delivery_reason');
Route::get('/reschedule_prod_details/{production_order}', 'SecondaryController@reschedule_prod_details');
//change code alert
Route::get('/get_reload_tbl_change_code', 'SecondaryController@get_reload_tbl_change_code');
//revise operator reject list setup
Route::get('/get_material_type', 'SecondaryController@get_material_type');
Route::post('/edit_material_type', 'SecondaryController@update_material_type');
Route::get('/get_material_type_tbl', 'SecondaryController@get_material_type_tbl');
Route::post('/save_material_type', 'SecondaryController@save_material_type');
//calendar
Route::get('/schedule_prod_calendar_details', 'SecondaryController@schedule_prod_calendar_details');
Route::get('/get_assembly_prod_calendar', 'SecondaryController@get_assembly_prod_calendar');
Route::post('/calendar_update_rescheduled_delivery_date', 'MainController@calendar_update_rescheduled_delivery_date');
//reason for cancellation(PO)
Route::post('/save_cancelled_reason', 'SecondaryController@save_reason_for_cancellation');
Route::get('/tbl_reason_for_cancellation_po', 'SecondaryController@tbl_reason_for_cancellation_po');
Route::post('/edit_cancelled_reason', 'SecondaryController@update_reason_for_cancellation');
Route::post('/delete_cancelled_reason', 'SecondaryController@delete_reason_for_cancellation');
Route::get('/tbl_reset_workstation/{id}', 'SecondaryController@get_tbl_reset_workstation');
Route::post('/reset_workstation_data', 'SecondaryController@reverse_mark_as_done_task');
Route::get('/get_reject_categ_and_process', 'SecondaryController@get_reject_categ_and_process');
//warning notif for custom shift sched
Route::get('/get_warning_notif_for_custom_shift/{id}', 'SecondaryController@get_warning_notif_for_custom_shift');
//Additional shift sched
Route::get('/shift_sched_details', 'SecondaryController@shift_sched_details');
Route::get('/get_tbl_default_shift_sched', 'MainController@get_tbl_default_shift_sched');
//Daily Report
Route::get('/daily_output_report', 'ReportsController@daily_output_report');
Route::get('/fabrication_report', 'ReportsController@fabrication_daily_report_page');
Route::get('/daily_output_chart', 'ReportsController@daily_output_chart');
Route::get('/assembly_daily_report', 'ReportsController@daily_output_report');
Route::get('/assembly_report', 'ReportsController@assembly_report_page');
Route::get('/painting_report', 'ReportsController@painting_report_page');
Route::get('/qa_report', 'ReportsController@qa_report');

Route::group(['middleware' => 'auth'], function(){
	Route::get('/display_available_scrap/{production_order}', 'ManufacturingController@display_available_scrap');
	Route::post('/update_production_projected_scrap/{production_order}/{projected_scrap}', 'ManufacturingController@update_production_projected_scrap');
	Route::post('/submit_scrap', 'ManufacturingController@submit_scrap');
	Route::post('/update_scrap', 'ManufacturingController@update_scrap');
	Route::post('/insert_scrap_used', 'ManufacturingController@insert_scrap_used');
	Route::get('/inventory', 'InventoryController@inventory_index');
	Route::get('/get_inventory_list/{operation}', 'InventoryController@get_inventory_list');
	Route::get('/get_transaction_history/{operation}', 'InventoryController@get_transaction_history');
	Route::get('/get_scrap_inventory/{operation}', 'InventoryController@get_scrap_inventory');
	Route::get('/get_withdrawal_slips/{operation}', 'InventoryController@get_withdrawal_slips');
	Route::get('/get_scrap_per_material/{material_type}', 'InventoryController@get_scrap_per_material');
	Route::get('/get_scrap_filters/{operation}', 'InventoryController@get_scrap_filters');
	Route::get('/get_withdrawal_slip_filters/{operation}', 'InventoryController@get_withdrawal_slip_filters');
	Route::get('/get_inventory_filters/{operation}', 'InventoryController@get_inventory_filters');
	Route::get('/get_transaction_filters/{operation}', 'InventoryController@get_transaction_filters');
	Route::post('/add_scrap/fabrication', 'InventoryController@add_scrap');
	Route::get('/get_pending_inventory_transactions', 'InventoryController@get_pending_inventory_transactions');
	Route::get('/get_inspection_logs', 'InventoryController@get_inspection_logs');
	Route::get('/count_notifications', 'MainController@count_notifications');
	Route::post('/end_scrap_task', 'MainController@end_scrap_task');
	Route::post('/submit_uom_conversion', 'InventoryController@submit_uom_conversion');
	Route::post('/delete_uom_conversion', 'InventoryController@delete_uom_conversion');
	Route::get('/get_uom_conversion_list', 'InventoryController@get_uom_conversion_list');
	Route::get('/painting_ready_list', 'MainController@painting_ready_list');
	Route::get('/print_fg_transfer_slip/{production_order}', 'MainController@print_fg_transfer_slip');
	Route::get('/assembly/print_machine_schedule/{scheduled_date}/{machine_code}', 'AssemblyController@print_assembly_machine_schedule');
	Route::post('/update_conveyor_assignment', 'AssemblyController@update_conveyor_assignment');
	Route::post('/cancel_production_order_feedback/{stock_entry}', 'ManufacturingController@cancel_production_order_feedback');
	Route::get('/checkWorkOrderItemQty', 'MainController@checkWorkOrderItemQty');
	Route::get('/completed_so_with_pending_production_order', 'MainController@completedSoWithPendingProduction');
	Route::get('/completed_mreq_with_pending_production_order', 'MainController@completedMreqWithPendingProduction');
	Route::get('/production_inaccurate_material_transferred', 'MainController@inaccurateProductionTransferredQtyWithWithdrawals');
	Route::get('/timelogOutputVsProducedQty', 'MainController@timelogOutputVsProducedQty');
	Route::get('/jobTicketCompletedQtyVsTimelogsCompletedQty', 'MainController@jobTicketCompletedQtyVsTimelogsCompletedQty');
	Route::get('/audit_report/mismatched_po_status', 'LinkReportController@mismatched_po_status');
	Route::get('/audit_report/feedbacked_po_with_pending_ste', 'LinkReportController@feedbacked_po_with_pending_ste');
	Route::get('/audit_report/transferred_required_qty_mismatch', 'LinkReportController@transferred_required_qty_mismatch');
	Route::get('/audit_report/overridden_production_orders', 'LinkReportController@overridden_production_orders');
	Route::get('/audit_report/stocks_transferred_but_none_in_wip', 'LinkReportController@stocks_transferred_but_none_in_wip');
	Route::get('/qa_inspection_logs', 'QualityInspectionController@qaInspectionLogs');
	Route::post('/createViewOrderLog', 'MainController@createViewOrderLog');
	Route::get('/weekly_rejection_report', 'LinkReportController@weekly_rejection_report');
	Route::get('/reject_reasons_report', 'LinkReportController@reject_reasons_report');
});

Route::get('/get_item_attributes', 'SecondaryController@get_item_attributes');
Route::get('/update_produced_qty/{prod}', 'MainController@update_produced_qty');
// ajax list (json)
Route::get('/items', 'MainController@item_query');
Route::get('/warehouses', 'MainController@warehouse_query');
Route::get('/operations', 'MainController@operation_query');
Route::get('/workstations/{operation_id}', 'MainController@workstation_query');
Route::get('/processes/{workstation_id}', 'MainController@process_query');
//exclude from authentication
Route::get('/getprocess_query/{workstation}', 'SecondaryController@getprocess_query');
Route::get('/get_operators', 'MainController@get_operators');
Route::get('/get_operator_timelogs', 'MainController@get_operator_timelogs');
Route::get('/tbl_operator_item_produced_report/{date1}/{date2}/{workstation}/{process}/{parts}/{item_code}', 'SecondaryController@tbl_operator_item_produced_report');
//Daily Report
Route::get('/link_fabrication_report', 'LinkReportController@fabrication_daily_report_page');
Route::get('/link_assembly_report', 'LinkReportController@assembly_report_page');
Route::get('/link_painting_report', 'LinkReportController@painting_report_page');
// Route::get('/link_qa_report', 'LinkReportController@qa_report');
Route::get('/link_daily_output_report', 'LinkReportController@daily_output_report');
Route::get('/link_daily_output_chart', 'LinkReportController@daily_output_chart');
Route::get('/get_filter_per_parts_category', 'LinkReportController@get_filter_per_parts_category');
Route::get('/report_index', 'LinkReportController@index');
Route::get('/link_painting_report/{id}', 'LinkReportController@painting_report_page');
Route::get('/link_fabrication_report/{id}', 'LinkReportController@fabrication_daily_report_page');
Route::get('/link_assembly_report/{id}', 'LinkReportController@assembly_report_page');
Route::get('/link_qa_report/{id}', 'LinkReportController@qa_report');
Route::get('/export/job_ticket', 'LinkReportController@export_job_ticket');
Route::get('/export/rejection_logs', 'LinkReportController@export_rejection_logs');
Route::get('/export/machine_list', 'LinkReportController@export_machine_list');

Route::get('/link_painting_daily_output_report', 'LinkReportController@painting_output_report');
Route::get('/link_painting_daily_output_chart', 'LinkReportController@painting_daily_output_chart');
Route::get('/rejection_report', 'LinkReportController@rejection_report');
Route::get('/rejection_report_chart', 'LinkReportController@rejection_report_chart');
Route::get('/link_parts_category_daily_output', 'LinkReportController@parts_output_report');
Route::get('/link_painting_parts_category_daily_output', 'LinkReportController@painting_parts_output_report');
Route::get('/powder_coating_usage_report', 'LinkReportController@powder_coating_usage_report');
Route::get('/powder_coat_usage_history', 'LinkReportController@powder_coat_usage_history');
Route::get('/print_qa_rejection_report', 'LinkReportController@print_qa_rejection_report');