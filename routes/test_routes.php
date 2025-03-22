<?php

use Illuminate\Support\Facades\Route;

Route::get('/local', 'TestingEnvironmentController@local_login');
Route::get('/admin', 'TestingEnvironmentController@admin_login');
Route::get('/get_unassigned', 'TestingEnvironmentController@get_unassigned');
Route::get('/parent_item_in_child_bom', 'Testing EnvironmentController@parent_item_in_child_bom');
Route::get('/get_machine_utilization', 'TestingEnvironmentController@get_machine_utilization');
Route::get('/testing', 'TestingEnvironmentController@testing');
Route::get('/get_unreturned_samples', 'TestingEnvironmentController@get_unreturned_samples');
Route::get('/activity_logs_testing', 'TestingEnvironmentController@activity_logs_testing')->name('Activity Logs Test 2');
Route::get('/get_completed_so_with_pending_production_orders', 'TestingEnvironmentController@get_completed_so_with_pending_production_orders');
Route::get('/completed_po_with_pending_jt', 'TestingEnvironmentController@completed_po_with_pending_jt');
Route::get('/get_so_received', 'TestingEnvironmentController@get_so_received');
Route::get('/close_production_orders', 'TestingEnvironmentController@close_production_orders');
Route::get('/get_machine_uptime', 'TestingEnvironmentController@get_machine_uptime');
Route::get('/notification', 'TestingEnvironmentController@notification');
Route::post('/send-notification', 'TestingEnvironmentController@send_notification');
Route::get('/get_assembly_floating_stocks', 'TestingEnvironmentController@get_assembly_floating_stocks');
Route::get('/testing/webhook', 'TestingEnvironmentController@so_webhook');
Route::get('/stock_ledger_report', 'TestingEnvironmentController@stock_ledger_report');
Route::post('/create_stock_recon', 'TestingEnvironmentController@create_stock_recon');
Route::get('/cancel_feedback', 'TestingEnvironmentController@cancel_feedback');
