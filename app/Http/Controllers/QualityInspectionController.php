<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use App\Exports\ExportDataQaInspectionLog; 
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\GeneralTrait;

use DB;

class QualityInspectionController extends Controller
{
    use GeneralTrait;

	public function get_checklist(Request $request, $workstation_name, $production_order, $process_id){
        $workstation_details = DB::connection('mysql_mes')->table('workstation')->where('workstation_name', $workstation_name)->first();
        if(!$workstation_details){
            return response()->json(['success' => 0, 'message' => 'No QA Checklist found for workstation ' . $workstation_name]);
        }

        $operation_id = $workstation_details->operation_id;
        $process_details = DB::connection('mysql_mes')->table('process')->where('process_id', $process_id)->first();
        if(!$process_details){
            return response()->json(['success' => 0, 'message' => 'Process not found.']);
        }

        $production_order_items = $item_images = $inspected_component_qtys = [];
        if ($operation_id == 3) {
            $production_order_items = DB::connection('mysql')->table('tabWork Order Item')->where('parent', $production_order)
                ->select('item_code', 'description', 'required_qty')->get();

            $item_images = DB::connection('mysql')->table('tabItem Images')->whereIn('parent', collect($production_order_items)->pluck('item_code'))
                ->pluck('image_path', 'parent')->toArray();

            $inspected_component_qtys = DB::connection('mysql_mes')->table('inspected_component')
                ->whereIn('item_code', collect($production_order_items)->pluck('item_code'))
                ->where('production_order', $production_order)
                ->selectRaw('item_code, SUM(inspected_qty) as inspected_qty, SUM(rejected_qty) as rejected_qty')
                ->groupBy('item_code')->get();

            $inspected_component_qtys = collect($inspected_component_qtys)->groupBy('item_code')->toArray();
        }

        if($workstation_name == "Painting"){
            $q = DB::connection('mysql_mes')->table('qa_checklist')
                ->join('reject_list', 'qa_checklist.reject_list_id', 'reject_list.reject_list_id')
                ->join('reject_category', 'reject_category.reject_category_id', 'reject_list.reject_category_id')
                ->where('qa_checklist.workstation_id', $workstation_details->workstation_id)
                ->where(function($q) use ($process_id) {
                    $q->where('qa_checklist.process_id', $process_id)
                        ->orWhere('qa_checklist.process_id', null);
                })
                ->where('reject_category.reject_category_id', $request->reject_category)
                ->orderByRaw("FIELD(type, 'Minor Reject(s)','Major Reject(s)','Critical Reject(s)') DESC")
                ->get();
        }else{
            $q = DB::connection('mysql_mes')->table('qa_checklist')
                ->join('reject_list', 'qa_checklist.reject_list_id', 'reject_list.reject_list_id')
                ->join('reject_category', 'reject_category.reject_category_id', 'reject_list.reject_category_id')
                ->where('qa_checklist.workstation_id', $workstation_details->workstation_id)
                ->where('reject_category.reject_category_id', $request->reject_category)
                ->orderByRaw("FIELD(type, 'Minor Reject(s)','Major Reject(s)','Critical Reject(s)') DESC")
                ->get();
        }

        $reject_category = 'In Process - ';
        $reject_category .= strtoupper(count($q) > 0 ? $q[0]->reject_category_name : 'Quality Inspection');

        $checklist = collect($q)->groupBy(['type', 'reject_category_name']);

        $production_order_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->first();
        if(!$production_order_details){
            return response()->json(['success' => 0, 'message' => 'Production Order ' . $production_order . ' not found.']);
        }

        $categories = collect($q)->unique('type');
        $categories = array_column($categories->toArray(), 'type');

        $sample_sizes = [];
        $reject_levels = [];
        foreach($categories as $category){
            $sample_size_qry = DB::connection('mysql_mes')->table('qa_sampling_plan')
                ->where('type', $category)
                ->where('lot_size_min', '<=', $production_order_details->qty_to_manufacture)
                ->where('lot_size_max', '>=', $production_order_details->qty_to_manufacture)
                ->first();

            $sample_size = ($sample_size_qry) ? $sample_size_qry->sample_size : 0;
            $reject_level = ($sample_size_qry) ? $sample_size_qry->reject_level : 0;

            array_push($sample_sizes, $sample_size);
            array_push($reject_levels, $reject_level);
        }

        if ($workstation_name == 'Spotwelding') {
            $timelog_details = DB::connection('mysql_mes')->table('spotwelding_qty')->where('time_log_id', $request->time_log_id)->first();
        }else{
            $timelog_details = DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $request->time_log_id)->first();
        }

        $inspection_type = $request->inspection_type;
       
        return view('quality_inspection.tbl_inspection_tabs', compact('checklist', 'production_order_details', 'workstation_details', 'process_details', 'sample_sizes', 'timelog_details', 'inspection_type', 'reject_levels', 'production_order_items', 'reject_category', 'item_images', 'inspected_component_qtys'));
    }
    
    // /submit_quality_inspection
    public function submit_quality_inspection(Request $request){
        DB::connection('mysql_mes')->beginTransaction();
        DB::connection('mysql')->beginTransaction();
        try {
            $now = Carbon::now(); 	
            if (!$request->inspected_by) {
                return response()->json(['success' => 0, 'message' => 'Please tap Authorized QC Employee ID.']);
            }

            $qa_user = DB::table('essex.users as essex')
                ->join('mes.user as mes', 'essex.user_id', 'mes.user_access_id')
                ->join('mes.user_group as group', 'group.user_group_id', 'mes.user_group_id')
                ->where('essex.status', 'Active')->where('essex.user_id', $request->inspected_by)->where('group.module', 'Quality Assurance')
                ->select('essex.*')->first();

            if (!$qa_user) {
                return response()->json(['success' => 0, 'message' => 'Authorized QA Employee ID not found.']);
            }
            
            $total_rejects = $request->total_rejects;
            if($total_rejects > 0){
                if (!$request->qc_remarks) {
                    return response()->json(['success' => 0, 'message' => 'Please select QC Remarks.']);
                }
            }

            $qa_staff_name = $qa_user->employee_name;
            if ($request->time_log_id) {
                if ($request->workstation == 'Spotwelding') {
                    $job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')
                        ->join('spotwelding_qty', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
                        ->where('spotwelding_qty.time_log_id', $request->time_log_id)->first();
                }else{
                    $job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')
                        ->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
                        ->where('time_logs.time_log_id', $request->time_log_id)->first();
                }
                $production_order = $job_ticket_details->production_order;
                $workstation = $job_ticket_details->workstation;
                $prod_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->first();

                $operation_id = $prod_details->operation_id;
                $operation_id = $workstation == 'Painting' ? 2 : $operation_id;

                if ($request->inspection_type == 'Random Inspection') {
                    if ($request->item_code && $total_rejects > 0) {
                        $item_required_qty = DB::connection('mysql')->table('tabWork Order Item')->where('parent', $production_order)
                            ->where('item_code', $request->item_code)->sum('required_qty');

                        $required_qty_per_piece = $item_required_qty / $prod_details->qty_to_manufacture;
                        $total_rejects = $total_rejects / $required_qty_per_piece;
                        $total_rejects = $total_rejects < 1 ? 1 : round($total_rejects);
                    }

                    if ($request->workstation == 'Spotwelding') {
                        $insert = [
                            'reference_type' => 'Spotwelding',
                            'reference_id' => $request->time_log_id,
                            'qa_inspection_type' => $request->inspection_type,
                            'qa_inspection_date' => $now->toDateTimeString(),
                            'qa_staff_id' => $request->inspected_by,
                            'reject_level' => $request->reject_level,
                            'sample_size' => $request->sample_size,
                            'actual_qty_checked' => $request->total_checked,
                            'rejected_qty' => $total_rejects,
                            'for_rework_qty' => 0,
                            'status' => ($total_rejects > 0) ? 'QC Failed' : 'QC Passed',
                            'qc_remarks' => $request->qc_remarks,
                            'created_by' => $qa_staff_name,
                            'created_at' => $now->toDateTimeString(),
                            'reject_category_id' => $request->reject_category_id
                        ];
                    }else{
                        $insert = [
                            'reference_type' => 'Time Logs',
                            'reference_id' => $request->time_log_id,
                            'qa_inspection_type' => $request->inspection_type,
                            'qa_inspection_date' => $now->toDateTimeString(),
                            'qa_staff_id' => $request->inspected_by,
                            'reject_level' => $request->reject_level,
                            'sample_size' => $request->sample_size,
                            'actual_qty_checked' => $request->total_checked,
                            'rejected_qty' => $total_rejects,
                            'for_rework_qty' => 0,
                            'status' => ($total_rejects > 0) ? 'QC Failed' : 'QC Passed',
                            'qc_remarks' => $request->qc_remarks,
                            'created_by' => $qa_staff_name,
                            'created_at' => $now->toDateTimeString(),
                            'reject_category_id' => $request->reject_category_id
                        ];
                    }
                
                    $good_qty_after_transaction = $job_ticket_details->good - $total_rejects;
        
                    if($good_qty_after_transaction < 0){
                        $good_qty_after_transaction = 0;
                    }

                    $rework_qty = $job_ticket_details->rework;
                    if($request->qc_remarks == 'For Rework'){
                        $rework_qty = $job_ticket_details->rework + $request->qty_reject;
                    }

                    DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_details->job_ticket_id)->update(['rework' => $rework_qty]);

                    $update = [
                        'last_modified_at' => $now->toDateTimeString(),
                        'last_modified_by' => $qa_staff_name,
                        'good' => $good_qty_after_transaction,
                        'reject' => $total_rejects + $job_ticket_details->reject,
                    ];
                    
                    $logs_table = $request->workstation == 'Spotwelding' ? 'spotwelding_qty' : 'time_logs';
                    DB::connection('mysql_mes')->table($logs_table)->where('time_log_id', $request->time_log_id)->update($update);
                    
                    $qa_id = DB::connection('mysql_mes')->table('quality_inspection')->insertGetId($insert);

                    if ($request->item_code) {
                        $inspected_component = [
                            'qa_id' => $qa_id,
                            'item_code' => $request->item_code,
                            'inspected_qty' => $request->total_checked,
                            'rejected_qty' => $request->total_rejects,
                            'production_order' => $production_order,
                            'created_by' => $qa_staff_name,
                            'created_at' => $now->toDateTimeString(),
                        ];
    
                        DB::connection('mysql_mes')->table('inspected_component')->insert($inspected_component);
                    }

                    if($request->rejection_values){
                        $rejection_values = rtrim($request->rejection_values, ',');
                        $rejection_values = explode(",", $rejection_values);

                        $rejection_types = rtrim($request->rejection_types, ',');
                        $rejection_types = explode(",", $rejection_types);

                        $reject_values = [];
                        foreach ($rejection_values as $i => $value) {
                            $reject_values[] = [
                                'job_ticket_id' => $job_ticket_details->job_ticket_id,
                                'qa_id' => $qa_id,
                                'reject_list_id' => $rejection_types[$i],
                                'reject_value' => $value
                            ];
                        }

                        DB::connection('mysql_mes')->table('reject_reason')->insert($reject_values);
                    }
                    
                    $this->update_job_ticket($job_ticket_details->job_ticket_id);

                    $process_name = DB::connection('mysql_mes')->table('process')->where('process_id', $job_ticket_details->process_id)->pluck('process_name')->first();

                    if($total_rejects > 0){
                        $message = 'Reject quantity of ' . $total_rejects . ' for '.$request->workstation.' - ' . $process_name . ' has been submitted by ' . $qa_user->employee_name;
                    }else{
                        $message = 'QC Pass for '.$request->workstation.' - ' . $process_name . ' has been submitted by ' . $qa_user->employee_name;
                    }

                    $activity_logs = [
                        'action' => $total_rejects > 0 ? 'QC Failed' : 'QC Passed',
                        'message' => $message,
                        'reference' => $production_order,
                        'created_at' => $now->toDateTimeString(),
                        'created_by' => $qa_user->employee_name
                    ];
        
                    DB::connection('mysql_mes')->table('activity_logs')->insert($activity_logs);
                }else{
                    if ($request->workstation == 'Spotwelding') {
                        $job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')
                            ->join('spotwelding_qty', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
                            ->where('spotwelding_qty.job_ticket_id', $request->time_log_id)->first();
                    }else{
                        $job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')
                            ->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
                            ->where('time_logs.time_log_id', $request->time_log_id)->first();
                    }

                    $production_order = $job_ticket_details->production_order;
                    $workstation = $job_ticket_details->workstation;

                    if ($request->rejected_qty > $prod_details->qty_to_manufacture) {
                        return response()->json(['success' => 0, 'message' => 'Rejected qty cannot be greater than ' . $prod_details->qty_to_manufacture]);
                    }

                    $reject_reason_id = 0;
                    $reject_reason = DB::connection('mysql_mes')->table('reject_reason')->where('qa_id', $request->qa_id)->first();
                    if($reject_reason){
                        $reject_reason_id = $reject_reason->reject_reason_id;
                    }

                    DB::connection('mysql_mes')->table('reject_reason')->where('reject_reason_id', $reject_reason_id)->update(['reject_list_id' => $request->reject_list_id]);

                    $update = [
                        'qa_inspection_date' => $now->toDateTimeString(),
                        'qa_staff_id' => $request->inspected_by,
                        'rejected_qty' => $request->rejected_qty,
                        'qa_disposition' => $request->qa_disposition,
                        'remarks' => $request->remarks,
                        'status' => ($request->rejected_qty > 0) ? 'QC Failed' : 'QC Passed',
                    ];

                    DB::connection('mysql_mes')->table('quality_inspection')->where('qa_id', $request->qa_id)->update($update);

                    $reject_qty = $request->old_reject_qty - $request->rejected_qty;

                    $good_qty_after_transaction = $job_ticket_details->good + $reject_qty;
        
                    if($good_qty_after_transaction < 0){
                        $good_qty_after_transaction = 0;
                    }

                    $update = [
                        'last_modified_at' => $now->toDateTimeString(),
                        'last_modified_by' => $qa_staff_name,
                        'good' => $good_qty_after_transaction,
                        'reject' => $request->rejected_qty,
                    ];
                    
                    $logs_table = $request->workstation == 'Spotwelding' ? 'spotwelding_qty' : 'time_logs';
                    DB::connection('mysql_mes')->table($logs_table)->where('time_log_id', $request->time_log_id)->update($update);

                    $this->update_job_ticket($job_ticket_details->job_ticket_id);

                    // 
                    if ($request->qa_disposition == 'Scrap') {
                        if ($prod_details->item_classification == 'SA - Sub Assembly') {
                            $uom_details = DB::connection('mysql_mes')->table('uom')->where('uom_name', 'like', '%kilogram%')->first();
                            if ($uom_details) {
                            
                                $cutting_size = DB::connection('mysql')->table('tabItem Variant Attribute')
                                    ->where('parent', $prod_details->item_code)->where('attribute', 'like', '%cutting size%')->first();

    
                                if ($cutting_size) {
                                    $cutting_size = strtoupper($cutting_size->attribute_value);
                                    $cutting_size = str_replace(' ', '', preg_replace('/\s+/', '', $cutting_size));
                                    $cutting_size = explode("X", $cutting_size);

                                    $length = $cutting_size[0];
                                    $width = $cutting_size[1];
                                    $thickness = preg_replace("/[^0-9,.]/", '', $cutting_size[2]);
                                }

                                $material = DB::connection('mysql')->table('tabItem Variant Attribute')
                                    ->where('parent', $prod_details->item_code)->where('attribute', 'like', '%material%')->first();

                                if ($material) {
                                    $material = strtoupper($material->attribute_value);
                                }

                                $qty_in_cubic_mm = ($length * $width * $thickness) * $request->rejected_qty;

                                if($material == 'CRS'){
                                    $uom_arr_1 = DB::connection('mysql_mes')->table('uom_conversion')
                                        ->join('uom', 'uom.uom_id', 'uom_conversion.uom_id')
                                        ->where('uom.uom_name', 'like', '%cubic%')->pluck('uom_conversion_id')->toArray();

                                    $uom_arr_2 = DB::connection('mysql_mes')->table('uom_conversion')
                                        ->where('uom_id', $uom_details->uom_id)->pluck('uom_conversion_id')->toArray();

                                    $uom_conversion_id = array_intersect($uom_arr_1, $uom_arr_2);

                                    $uom_1_conversion_factor = DB::connection('mysql_mes')->table('uom_conversion')
                                        ->where('uom_conversion_id', $uom_conversion_id[0])
                                        ->where('uom_id', '!=', $uom_details->uom_id)->sum('conversion_factor');

                                    $uom_2_conversion_factor = DB::connection('mysql_mes')->table('uom_conversion')
                                        ->where('uom_conversion_id', $uom_conversion_id[0])
                                        ->where('uom_id', $uom_details->uom_id)->sum('conversion_factor');

                                    $conversion_factor = $uom_2_conversion_factor / $uom_1_conversion_factor;

                                    $scrap_qty = $qty_in_cubic_mm * $conversion_factor;

                                    $existing_scrap = DB::connection('mysql_mes')->table('scrap')
                                        ->where('material', $material)->where('uom_id', $uom_details->uom_id)
                                        ->where('thickness', $thickness)->first();

                                    if ($existing_scrap) {
                                        $scrap_qty = $scrap_qty + $existing_scrap->scrap_qty;
                                        $values = [
                                            'scrap_qty' => $scrap_qty,
                                            'last_modified_by' => $qa_staff_name,
                                        ];

                                        DB::connection('mysql_mes')->table('scrap')->where('scrap_id', $existing_scrap->scrap_id)->update($values);
                                    }else{
                                        $values = [
                                            'uom_conversion_id' => $uom_conversion_id[0],
                                            'uom_id' => $uom_details->uom_id,
                                            'material' => $material,
                                            'thickness' => $thickness,
                                            'scrap_qty' => $scrap_qty,
                                            'created_by' => $qa_staff_name,
                                        ];
        
                                        DB::connection('mysql_mes')->table('scrap')->insert($values);
                                    }
                                }
                            }
                        }
                    }
                }

                DB::connection('mysql')->commit();
                DB::connection('mysql_mes')->commit();

                return response()->json(['success' => 1, 'message' => 'QA Inspection created.', 'details' => ['production_order' => $production_order, 'workstation' => $workstation, 'checklist_url' => $request->checklist_url, 'timelogid' => $request->time_log_id, 'operation_id' => $operation_id]]);
            }
        } catch (Exception $e) {
            DB::connection('mysql_mes')->rollback();
            DB::connection('mysql')->rollback();

			return response()->json(['success' => 0, 'message' => 'An error occured. Please contact your system administrator.']);
        }
    }

    public function update_completed_qty_per_workstation($job_ticket_id){
    	$job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')
    		->join('production_order', 'job_ticket.production_order', 'production_order.production_order')
    		->where('job_ticket_id', $job_ticket_id)
    		->select('job_ticket.status', 'production_order.qty_to_manufacture')->first();

    	$logs = DB::connection('mysql_mes')->table('time_logs')->where('job_ticket_id', $job_ticket_id)->exists();

    	// get total good qty from timelogs
    	$total_good = DB::connection('mysql_mes')->table('time_logs')->where('job_ticket_id', $job_ticket_id)->where('status', 'Completed')->sum('good');

    	if ($logs && $total_good >= 0) {
    		DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)
    			->update(['status' => 'In Progress']);
    	}

    	if ($job_ticket_details->qty_to_manufacture == $total_good) {
    		DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)
    			->update(['status' => 'Completed', 'completed_qty' => $total_good]);
    	}else{
    		DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)
    			->update(['completed_qty' => $total_good]);
    	}
    }

    public function updateProdOrderOps($prod_order, $workstation){
        try {
            $prod_qty = DB::connection('mysql_mes')->table('production_order')
                ->where('production_order', $prod_order)->sum('qty_to_manufacture');
            
            // get total completed
            $tsd = DB::connection('mysql_mes')->table('job_ticket')
            	->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
                ->where('workstation', $workstation)->where('production_order', $prod_order)
                ->where('time_logs.status', 'Completed')
                ->get();

            $completed = collect($tsd)->sum('good');
            $actual_start = collect($tsd)->min('from_time');
            $actual_end = collect($tsd)->max('to_time');

            $status = ($prod_qty == $completed) ? "Completed" : "Pending";

            $actual_start = Carbon::parse($actual_start);
            $actual_end = Carbon::parse($actual_end);
            $operation_time = $actual_end->diffInSeconds($actual_start);
            $operation_time = $operation_time / 60;

            $data = [
                'status' => $status,
                'completed_qty' => $completed,
                'actual_start_time' => $actual_start,
                'actual_end_time' => $actual_end,
                'actual_operation_time' => $operation_time,
            ];

            DB::connection('mysql')->table('tabWork Order Operation')
				->where('parent', $prod_order)->where('workstation', $workstation)->update($data);

        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }
    
    public function update_produced_qty($production_order){
		$produced_qty = DB::connection('mysql_mes')->table('job_ticket')
            ->where('production_order', $production_order)->min('completed_qty');
            
		if ($produced_qty > 0) {
			DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->update(['produced_qty' => $produced_qty]);
		}

		$required_qty = DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->sum('qty_to_manufacture');
		if ($produced_qty >= $required_qty) {
			DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->update(['status' => 'Completed']);
		}

		$item_code = DB::connection('mysql_mes')->table('production_order')
			->where('production_order', $production_order)->first()->item_code;

    }
    
    public function qa_dashboard(){
        $permissions = $this->get_user_permitted_operation();

        if(!in_array('Quality Assurance', $permissions['permitted_modules'])){
            return redirect('/main_dashboard');
        }
        
        $user_details = DB::connection('mysql_essex')->table('users')
            ->join('designation', 'users.designation_id', '=', 'designation.des_id')
            ->join('departments', 'users.department_id', '=', 'departments.department_id')
            ->where('user_id', Auth::user()->user_id)->first();

        $now = Carbon::now();
        $qa_logs = DB::connection('mysql_mes')->table('quality_inspection as qa')
            ->join('time_logs', 'time_logs.time_log_id', 'qa.reference_id')
            ->join('job_ticket', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
            ->join('production_order', 'production_order.production_order', 'job_ticket.production_order')
            ->whereDate('qa.qa_inspection_date', '>=', $now->startOfDay())
            ->where('qa.qa_inspection_date', '<=',  $now->endOfDay())
            ->where('qa.reference_type', 'Time Logs')->whereNotnull('qa.qa_inspection_date')
            ->whereIn('qa.status', ['QC Passed', 'QC Failed'])
            ->select('qa.qa_staff_id', 'job_ticket.workstation', 'qa.qa_inspection_date', 'qa.actual_qty_checked', 'qa.rejected_qty', 'production_order.operation_id');

        $qa_logs = DB::connection('mysql_mes')->table('quality_inspection as qa')
            ->join('spotwelding_qty', 'spotwelding_qty.time_log_id', 'qa.reference_id')
            ->join('job_ticket', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
            ->join('production_order', 'production_order.production_order', 'job_ticket.production_order')
            ->whereDate('qa.qa_inspection_date', '>=', $now->startOfDay())
            ->where('qa.qa_inspection_date', '<=',  $now->endOfDay())
            ->where('qa.reference_type', 'Time Logs')->whereNotnull('qa.qa_inspection_date')
            ->whereIn('qa.status', ['QC Passed', 'QC Failed'])
            ->select('qa.qa_staff_id', 'job_ticket.workstation', 'qa.qa_inspection_date', 'qa.actual_qty_checked', 'qa.rejected_qty', 'production_order.operation_id')
            ->unionAll($qa_logs)->limit(500)->orderBy('qa_inspection_date', 'desc')->get();

        $qa_logs_fabrication = $qa_logs_painting = $qa_logs_assembly = 0;
        $qa_logs_fabrication_qty_checked = $qa_logs_painting_qty_checked = $qa_logs_assembly_qty_checked = 0;
        $qa_logs_fabrication_rejected_qty = $qa_logs_painting_rejected_qty = $qa_logs_assembly_rejected_qty = 0;
        $qa_inspector_fabrication = $qa_inspector_painting = $qa_inspector_assembly = [];
        foreach ($qa_logs as $r) {
            if ($r->operation_id == 1 && $r->workstation != 'Painting') {
                $qa_logs_fabrication++;
                $qa_inspector_fabrication[] = $r->qa_staff_id;
                $qa_logs_fabrication_qty_checked += $r->actual_qty_checked;
                $qa_logs_fabrication_rejected_qty += $r->rejected_qty;
            }

            if ($r->operation_id == 1 && $r->workstation == 'Painting') {
                $qa_logs_painting++;
                $qa_inspector_painting[] = $r->qa_staff_id;
                $qa_logs_painting_qty_checked += $r->actual_qty_checked;
                $qa_logs_painting_rejected_qty += $r->rejected_qty;
            }

            if ($r->operation_id == 3) {
                $qa_logs_assembly++;
                $qa_inspector_assembly[] = $r->qa_staff_id;
                $qa_logs_assembly_qty_checked += $r->actual_qty_checked;
                $qa_logs_assembly_rejected_qty += $r->rejected_qty;
            }
        }

        $summary['fabrication'] = [
            'total_logs' => number_format($qa_logs_fabrication),
            'inspectors' => array_unique($qa_inspector_fabrication),
            'qty_checked' => number_format($qa_logs_fabrication_qty_checked),
            'qty_rejects' => number_format($qa_logs_fabrication_rejected_qty),
        ];

        $summary['painting'] = [
            'total_logs' => number_format($qa_logs_painting),
            'inspectors' => array_unique($qa_inspector_painting),
            'qty_checked' => number_format($qa_logs_painting_qty_checked),
            'qty_rejects' => number_format($qa_logs_painting_rejected_qty),
        ];

        $summary['assembly'] = [
            'total_logs' => number_format($qa_logs_assembly),
            'inspectors' => array_unique($qa_inspector_assembly),
            'qty_checked' => number_format($qa_logs_assembly_qty_checked),
            'qty_rejects' => number_format($qa_logs_assembly_rejected_qty),
        ];

        $qa_staffs = array_merge( array_unique($qa_inspector_fabrication),  array_unique($qa_inspector_painting),  array_unique($qa_inspector_assembly));
        $qa_staffs = DB::connection('mysql_essex')->table('users')
            ->whereIn('user_id', $qa_staffs)->pluck('employee_name', 'user_id')->toArray();

        return view('quality_inspection.qa_dashboard', compact('user_details', 'permissions', 'qa_staffs', 'summary'));
    }

    public function viewRejectionReport(){
        $permissions = $this->get_user_permitted_operation();

        if(!in_array('Quality Assurance', $permissions['permitted_modules'])){
            return redirect('/main_dashboard');
        }

        $reject_category= DB::connection('mysql_mes')->table('reject_category')->get();

        return view('quality_inspection.view_rejection_report', compact('permissions', 'reject_category'));
    }

    public function get_quick_view_data(){
        return $data = [
            'fabrication' => $this->get_quick_view_per_operation('Fabrication'),
            'painting' => $this->get_quick_view_per_operation('Painting'),
            'assembly' => $this->get_quick_view_per_operation('Assembly'),
        ];
    }

    public function get_quick_view_per_operation($operation){
        $start = Carbon::now()->startOfDay()->toDateTimeString();
        $end = Carbon::now()->endOfDay()->toDateTimeString();

        $operation_details = DB::connection('mysql_mes')->table('operation')->where('operation_name', $operation)->first();

        $operation_id = ($operation_details) ? $operation_details->operation_id : 0;

        $workstations = DB::connection('mysql_mes')->table('workstation')->where('operation_id', $operation_id)->pluck('workstation_name');
        $production_order_query = DB::connection('mysql_mes')->table('production_order')
            ->join('job_ticket', 'job_ticket.production_order', 'production_order.production_order')
            ->when($operation == 'Fabrication', function ($query) {
                return $query->where('job_ticket.workstation', '!=', 'Painting');
            })
            ->when($operation != 'Painting', function ($query) use ($workstations) {
                return $query->whereIn('job_ticket.workstation', $workstations);
            })
            ->when($operation == 'Painting', function ($query) {
                return $query->where('job_ticket.workstation', 'Painting');
            })
            ->whereBetween('job_ticket.planned_start_date', [$start, $end])->get();

        $count_production_order = collect($production_order_query)->unique('production_order')->count();

        $time_logs = DB::connection('mysql_mes')->table('job_ticket')
            ->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
            ->when($operation == 'Fabrication', function ($query) {
                return $query->where('job_ticket.workstation', '!=', 'Painting');
            })
            ->whereBetween('time_logs.from_time', [$start, $end])
            ->whereBetween('time_logs.to_time', [$start, $end])
            ->when($operation != 'Painting', function ($query) use ($workstations) {
                return $query->whereIn('job_ticket.workstation', $workstations);
            })
            ->when($operation == 'Painting', function ($query) {
                return $query->where('job_ticket.workstation', 'Painting');
            })
            ->where('time_logs.status', 'Completed')->get();
    
        $produced_qty = collect($time_logs)->sum('good');

        $quality_inspection_query = DB::connection('mysql_mes')->table('quality_inspection')
            ->join('time_logs', 'time_logs.time_log_id', 'quality_inspection.reference_id')
            ->join('job_ticket', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
            ->when($operation != 'Painting', function ($query) use ($workstations) {
                return $query->whereIn('job_ticket.workstation', $workstations);
            })
            ->when($operation == 'Painting', function ($query) {
                return $query->where('job_ticket.workstation', 'Painting');
            })
            ->where('quality_inspection.reference_type', 'Time Logs')
            ->whereBetween('time_logs.from_time', [$start, $end])
            ->whereIn('quality_inspection.status', ['QC Passed', 'QC Failed'])
            ->select('actual_qty_checked', 'rejected_qty', 'sample_size');

        if($operation == 'Fabrication'){
            $quality_inspection_query = DB::connection('mysql_mes')->table('quality_inspection')
                ->join('spotwelding_qty', 'spotwelding_qty.time_log_id', 'quality_inspection.reference_id')
                ->join('job_ticket', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
                ->when($operation != 'Painting', function ($query) use ($workstations) {
                    return $query->whereIn('job_ticket.workstation', $workstations);
                })
                ->when($operation == 'Painting', function ($query) {
                    return $query->where('job_ticket.workstation', 'Painting');
                })
                ->where('quality_inspection.reference_type', 'Spotwelding')
                ->whereBetween('spotwelding_qty.from_time', [$start, $end])
                ->whereIn('quality_inspection.status', ['QC Passed', 'QC Failed'])
                ->select('actual_qty_checked', 'rejected_qty', 'sample_size')
                ->union($quality_inspection_query);
        }

        $quality_inspection_query = $quality_inspection_query->get();

        $inspected_qty = collect($quality_inspection_query)->sum('actual_qty_checked');
        $rejected_qty = collect($quality_inspection_query)->sum('rejected_qty');
        $sample_size = collect($quality_inspection_query)->sum('sample_size');
        $actual_qty_checked = collect($quality_inspection_query)->sum('actual_qty_checked');

        $completed_wip_production_orders = DB::connection('mysql_mes')->table('production_order')
            ->join('job_ticket', 'job_ticket.production_order', 'production_order.production_order')
            ->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
            ->when($operation == 'Fabrication', function ($query) {
                return $query->where('job_ticket.workstation', '!=', 'Painting');
            })
            ->when($operation != 'Painting', function ($query) use ($workstations) {
                return $query->whereIn('job_ticket.workstation', $workstations);
            })
            ->when($operation == 'Painting', function ($query) {
                return $query->where('job_ticket.workstation', 'Painting');
            })
            ->whereBetween('time_logs.to_time', [$start, $end])
            ->where('time_logs.status', 'Completed')
            ->whereIn('production_order.status', ['Completed', 'In Progress'])
            ->distinct('production_order.production_order')
            ->count();
        
        return $data = [
            'produced_qty' => number_format($produced_qty),
            'inspected_qty' => number_format($inspected_qty),
            'rejected_qty' => number_format($rejected_qty),
            'production_order' => number_format($count_production_order),
            'completed_wip_production_orders' => number_format($completed_wip_production_orders),
        ];
    }

    public function get_reject_for_confirmation($operation_id, Request $request){
        $q = DB::connection('mysql_mes')->table('quality_inspection as q')
            ->join('time_logs as t', 't.time_log_id', 'q.reference_id')
            ->join('job_ticket as j', 'j.job_ticket_id', 't.job_ticket_id')
            ->join('production_order as p', 'p.production_order', 'j.production_order')
            ->join('reject_reason as rr', 'rr.qa_id', 'q.qa_id')
            ->join('reject_list as rl', 'rl.reject_list_id', 'rr.reject_list_id')
            ->where(function($q) use ($request) {
                $q->where('p.production_order', 'LIKE', '%'.$request->q.'%')
                ->orWhere('p.item_code', 'LIKE', '%'.$request->q.'%')
                ->orWhere('p.description', 'LIKE', '%'.$request->q.'%')
                ->orWhere('rl.reject_reason', 'LIKE', '%'.$request->q.'%')
                ->orWhere('j.workstation', 'LIKE', '%'.$request->q.'%')
                ->orWhere('p.item_code', 'LIKE', '%'.$request->q.'%');
            })
            ->when($operation_id == 1, function ($query) {
                return $query->where('p.operation_id', 1)->where('j.workstation', '!=', 'Painting');
            })
            ->when($operation_id == 3, function ($query) {
                return $query->where('p.operation_id', 3);
            })
            ->when($operation_id == 2, function ($query) {
                return $query->where('j.workstation', 'Painting');
            })
            ->whereRaw('p.feedback_qty < p.qty_to_manufacture')
            ->where('q.reference_type', 'Time Logs')
            ->where('q.qa_inspection_type', 'Reject Confirmation')
            ->whereNotIn('q.status', ['QC Passed', 'QC Failed'])
            ->select('p.production_order', 'p.item_code', 'p.stock_uom', 'p.description', DB::raw('MAX(q.created_at) as created_at'), 'p.qty_to_manufacture', 'p.customer', 'p.project', 'p.sales_order', 'p.material_request')
            ->groupBy('p.production_order', 'p.item_code', 'p.stock_uom', 'p.description', 'p.qty_to_manufacture', 'p.customer', 'p.project', 'p.sales_order', 'p.material_request')
            ->orderBy('q.created_at', 'desc');

        $list = DB::connection('mysql_mes')->table('quality_inspection as q')
            ->join('spotwelding_qty as t', 't.job_ticket_id', 'q.reference_id')
            ->join('job_ticket as j', 'j.job_ticket_id', 't.job_ticket_id')
            ->join('production_order as p', 'p.production_order', 'j.production_order')
            ->join('reject_reason as rr', 'rr.qa_id', 'q.qa_id')
            ->join('reject_list as rl', 'rl.reject_list_id', 'rr.reject_list_id')
            ->where(function($q) use ($request) {
                $q->where('p.production_order', 'LIKE', '%'.$request->q.'%')
                ->orWhere('p.item_code', 'LIKE', '%'.$request->q.'%')
                ->orWhere('p.description', 'LIKE', '%'.$request->q.'%')
                ->orWhere('rl.reject_reason', 'LIKE', '%'.$request->q.'%')
                ->orWhere('j.workstation', 'LIKE', '%'.$request->q.'%')
                ->orWhere('p.item_code', 'LIKE', '%'.$request->q.'%');
            })
            ->whereRaw('p.feedback_qty < p.qty_to_manufacture')
            ->when($operation_id == 1, function ($query) {
                return $query->where('p.operation_id', 1)->where('j.workstation', '!=', 'Painting');
            })
            ->when($operation_id == 3, function ($query) {
                return $query->where('p.operation_id', 3);
            })
            ->when(!in_array($operation_id, [1, 3]), function ($query) {
                return $query->where('j.workstation', 'Painting');
            })
            ->where('q.reference_type', 'Spotwelding')
            ->where('q.qa_inspection_type', 'Reject Confirmation')
            ->whereNotIn('q.status', ['QC Passed', 'QC Failed'])
            ->select('p.production_order', 'p.item_code', 'p.stock_uom', 'p.description', DB::raw('MAX(q.created_at) as created_at'), 'p.qty_to_manufacture', 'p.customer', 'p.project', 'p.sales_order', 'p.material_request')
            ->groupBy('p.production_order', 'p.item_code', 'p.stock_uom', 'p.description', 'p.qty_to_manufacture', 'p.customer', 'p.project', 'p.sales_order', 'p.material_request')
            ->unionAll($q)->orderBy('created_at', 'desc')->get();

        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // Create a new Laravel collection from the array data
        $itemCollection = collect($list);
        // Define how many items we want to be visible in each page
        $perPage = 15;
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
        // set url path for generted links
        $paginatedItems->setPath($request->url());

        $list = $paginatedItems;

        return view('quality_inspection.tbl_reject_confirmation', compact('list', 'operation_id'));
    }

    public function getProductionOrderRejectForConfirmation(Request $request, $production_order) {
        $production_order_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->first();
        $operation_id = $request->operation;

        $q = DB::connection('mysql_mes')->table('quality_inspection as q')
            ->join('time_logs as t', 't.time_log_id', 'q.reference_id')
            ->join('job_ticket as j', 'j.job_ticket_id', 't.job_ticket_id')
            ->join('process as p', 'j.process_id', 'p.process_id')
            ->join('reject_reason as rr', 'rr.qa_id', 'q.qa_id')
            ->join('reject_list as rl', 'rl.reject_list_id', 'rr.reject_list_id')
            ->where('q.reference_type', 'Time Logs')
            ->where('q.qa_inspection_type', 'Reject Confirmation')
            ->when($operation_id == 2, function ($q){
                return $q->where('j.workstation', 'Painting');
            })
            ->whereNotIn('q.status', ['QC Passed', 'QC Failed'])
            ->where('j.production_order', $production_order)
            ->select('j.process_id', 'j.workstation', 'rl.reject_reason', 'q.rejected_qty', 'q.created_at', 't.operator_name', 'rl.reject_list_id', 'time_log_id', 'q.qa_id', 'p.process_name', 'rr.reject_reason_id');

        $reject_confirmation_list = DB::connection('mysql_mes')->table('quality_inspection as q')
            ->join('spotwelding_qty as t', 't.job_ticket_id', 'q.reference_id')
            ->join('job_ticket as j', 'j.job_ticket_id', 't.job_ticket_id')
            ->join('process as p', 'j.process_id', 'p.process_id')
            ->join('reject_reason as rr', 'rr.qa_id', 'q.qa_id')
            ->join('reject_list as rl', 'rl.reject_list_id', 'rr.reject_list_id')
            ->where('q.reference_type', 'Spotwelding')
            ->where('q.qa_inspection_type', 'Reject Confirmation')
            ->when($operation_id == 2, function ($q){
                return $q->where('j.workstation', 'Painting');
            })
            ->whereNotIn('q.status', ['QC Passed', 'QC Failed'])
            ->where('j.production_order', $production_order)
            ->select('j.process_id', 'j.workstation', 'rl.reject_reason', 'q.rejected_qty', 'q.created_at', 't.operator_name', 'rl.reject_list_id', 'time_log_id', 'q.qa_id', 'p.process_name', 'rr.reject_reason_id')
            ->union($q)->get();

        if ($production_order_details->operation_id == 3) {
            $checklist = DB::connection('mysql_mes')->table('reject_list')
                ->join('reject_material_type', 'reject_material_type.reject_material_type_id', 'reject_list.reject_material_type_id')
                ->join('operation', 'operation.operation_id', 'reject_list.operation_id')
                ->where('operation.operation_name', 'like', '%Assembly%')->orderBy('reject_list.reject_reason', 'asc')->get();
        } else {
            $workstations = collect($reject_confirmation_list)->pluck('workstation')->unique();
            $checklist = DB::connection('mysql_mes')->table('qa_checklist')
                ->join('reject_list', 'qa_checklist.reject_list_id', 'reject_list.reject_list_id')
                ->join('workstation', 'workstation.workstation_id', 'qa_checklist.workstation_id')
                ->whereIn('workstation_name', $workstations)
                ->orderBy('reject_list.reject_reason', 'asc')->get();

            $checklist = collect($checklist)->groupby('workstation_name')->toArray();
        }

        $timelogs = collect($reject_confirmation_list)->pluck('time_log_id')->unique();

        return view('quality_inspection.tbl_production_order_reject_for_confirmation', compact('production_order_details', 'reject_confirmation_list', 'checklist', 'timelogs'));
    }

    public function count_reject_for_confirmation(){
        $q = DB::connection('mysql_mes')->table('quality_inspection as q')
            ->join('time_logs as t', 't.time_log_id', 'q.reference_id')
            ->join('job_ticket as j', 'j.job_ticket_id', 't.job_ticket_id')
            ->join('production_order as p', 'p.production_order', 'j.production_order')
            ->join('reject_reason as rr', 'rr.qa_id', 'q.qa_id')
            ->join('reject_list as rl', 'rl.reject_list_id', 'rr.reject_list_id')
            ->whereRaw('p.feedback_qty < p.qty_to_manufacture')
            ->where('q.reference_type', 'Time Logs')
            ->where('q.qa_inspection_type', 'Reject Confirmation')
            ->whereNotIn('q.status', ['QC Passed', 'QC Failed'])
            ->select('j.workstation', 'p.production_order', 'p.operation_id')
            ->groupBy('j.workstation', 'p.production_order', 'p.operation_id')
            ->orderBy('q.created_at', 'desc');

        $list = DB::connection('mysql_mes')->table('quality_inspection as q')
            ->join('spotwelding_qty as t', 't.job_ticket_id', 'q.reference_id')
            ->join('job_ticket as j', 'j.job_ticket_id', 't.job_ticket_id')
            ->join('production_order as p', 'p.production_order', 'j.production_order')
            ->join('reject_reason as rr', 'rr.qa_id', 'q.qa_id')
            ->join('reject_list as rl', 'rl.reject_list_id', 'rr.reject_list_id')
            ->whereRaw('p.feedback_qty < p.qty_to_manufacture')
            ->where('q.reference_type', 'Spotwelding')
            ->where('q.qa_inspection_type', 'Reject Confirmation')
            ->whereNotIn('q.status', ['QC Passed', 'QC Failed'])
            ->select('j.workstation', 'p.production_order', 'p.operation_id')
            ->groupBy('j.workstation', 'p.production_order', 'p.operation_id')
            ->unionAll($q)->get();

        $assembly = collect($list)->where('operation_id', 3)->where('workstation', '!=', 'Painting')->count();
        $painting = collect($list)->where('workstation', 'Painting')->count();
        $fabrication = collect($list)->where('operation_id', 1)->where('workstation', '!=', 'Painting')->count();

        return response()->json([
            'fabrication' => $fabrication,
            'painting' => $painting,
            'assembly' => $assembly,
            'overall' => $assembly + $painting + $fabrication
        ]);
    }

    public function qa_staff_workload(){
        $start = Carbon::now()->startOfDay()->toDateTimeString();
        $end = Carbon::now()->endOfDay()->toDateTimeString();

        $qa_staffs_workload = DB::connection('mysql_mes')->table('quality_inspection')->whereNotNull('qa_staff_id')
            ->whereBetween('qa_inspection_date', [$start, $end])
            ->select('qa_staff_id', DB::raw('SUM(actual_qty_checked) as qty_checked'))->groupBy('qa_staff_id')
            ->orderBy('qty_checked', 'desc')->get();

        $workload = [];
        foreach ($qa_staffs_workload as $row) {
            $qa_staff_details = DB::connection('mysql_essex')->table('users')->where('user_id', $row->qa_staff_id)->first();
            if($qa_staff_details){
                $workload[] = [
                    'qa_staff' => $qa_staff_details->employee_name,
                    'qty_checked' => (int)$row->qty_checked
                ];
            }
        }

        return response()->json($workload);
    }

    public function get_top_defect_count(){
        $start = Carbon::now()->startOfDay()->toDateTimeString();
        $end = Carbon::now()->endOfDay()->toDateTimeString();

        return $q = DB::connection('mysql_mes')->table('reject_reason')
            ->join('reject_list', 'reject_reason.reject_list_id', 'reject_list.reject_list_id')
            ->whereBetween('reject_reason.created_at', [$start, $end])
            ->select(DB::raw('COUNT(reject_reason.reject_list_id) as reject_count'), 'reject_list.reject_checklist')
            ->orderBy('reject_count', 'desc')
            ->groupBy('reject_reason.reject_list_id', 'reject_list.reject_checklist')
            ->limit(5)
            ->get();
    }

    public function get_reject_confirmation_checklist(Request $request, $production_order, $workstation_name, $process_id, $qa_id){
        $workstation_details = DB::connection('mysql_mes')->table('workstation')->where('workstation_name', $workstation_name)->first();
        if(!$workstation_details){
            return response()->json(['success' => 0, 'message' => 'No QA Checklist found for workstation ' . $workstation_name]);
        }

        $process_details = DB::connection('mysql_mes')->table('process')->where('process_id', $process_id)->first();
        if(!$process_details){
            return response()->json(['success' => 0, 'message' => 'Process not found.']);
        }

        $production_order_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->first();
        if(!$production_order_details){
            return response()->json(['success' => 0, 'message' => 'Production Order ' . $production_order . ' not found.']);
        }

        $qa_details = DB::connection('mysql_mes')->table('quality_inspection')->where('qa_id', $qa_id)->first();
        if(!$qa_details){
            return response()->json(['success' => 0, 'message' => 'Inspection not found.']);
        }

        $reject_details = DB::connection('mysql_mes')->table('reject_reason')->where('qa_id', $qa_id)->first();
        if(!$reject_details){
            return response()->json(['success' => 0, 'message' => 'Reject Reason not found.']);
        }

        if ($workstation_details->operation_id == 3) {
            $checklist = DB::connection('mysql_mes')->table('reject_list')
                ->join('reject_material_type', 'reject_material_type.reject_material_type_id', 'reject_list.reject_material_type_id')
                ->join('operation', 'operation.operation_id', 'reject_list.operation_id')
                ->where('operation.operation_name', 'like', '%Assembly%')->orderBy('reject_list.reject_reason', 'asc')->get();
        } else {
            $checklist = DB::connection('mysql_mes')->table('qa_checklist')
                ->join('reject_list', 'qa_checklist.reject_list_id', 'reject_list.reject_list_id')
                ->where('qa_checklist.workstation_id', $workstation_details->workstation_id)
                ->orderBy('reject_list.reject_reason', 'asc')->get();
        }

        $view = ($request->page != 'operator') ? 'quality_inspection.tbl_reject_confirmation_checklist' : 'quality_inspection.tbl_reject_confirmation_operator';

        return view($view, compact('production_order_details', 'workstation_details', 'process_details', 'checklist', 'qa_details', 'reject_details'));
    }

    public function get_reject_types($workstation, $process_id){
        $workstation_id = DB::connection('mysql_mes')->table('workstation')->where('workstation_name', $workstation)->first();
        if(empty($workstation_id)){
            $tab= DB::connection('mysql_mes')->table('operator_reject_list_setup as opset')
            ->join('reject_list', 'opset.reject_list_id', 'reject_list.reject_list_id')
            ->join('reject_category', 'reject_category.reject_category_id', 'reject_list.reject_category_id')
            ->where('opset.process_id', $workstation_id)
            ->get();
            $validation_tab="no_tab";
        }else{
            if($workstation_id->operation_id < 3){
                // $tab=[];
                $tab= DB::connection('mysql_mes')->table('operator_reject_list_setup as opset')
                ->join('reject_list', 'opset.reject_list_id', 'reject_list.reject_list_id')
                ->join('reject_category', 'reject_category.reject_category_id', 'reject_list.reject_category_id')
                ->where('opset.workstation_id', $workstation_id->workstation_id)
                ->where(function($q) use ($process_id) {
					$q->where('opset.process_id', $process_id)
						->orWhere('opset.process_id', null);
				})
                ->get();
                
                $validation_tab="no_tab";
            }else{
                $data= DB::connection('mysql_mes')->table('reject_list')
                ->join('reject_material_type', 'reject_material_type.reject_material_type_id', 'reject_list.reject_material_type_id')
                ->join('operation', 'operation.operation_id', 'reject_list.operation_id')
                ->where('operation.operation_name', 'like', '%Assembly%') 
                ->where('owner', 'Operator')
                ->get();
                $tab = $data->groupBy('material_type');
                $tab->all();
                $validation_tab="with_tab";
            }
        }

        return view('tables.tbl_reject_reason', compact('tab', 'validation_tab'));
    }

    // /qa_inspection_logs
    public function qaInspectionLogs() {
        $permissions = $this->get_user_permitted_operation();

        return view('quality_inspection.qa_inspection_logs', compact('permissions'));
    }

    public function qa_logs_filters(Request $request) {
        // workstation
        if ($request->type == 'workstation') {
            return DB::connection('mysql_mes')->table('workstation')
                ->when($request->operation == 1, function ($query) {
                    return $query->where('operation_id', 1);
                })
                ->when($request->operation == 3, function ($query) {
                    return $query->where('operation_id', 3);
                })
                ->when($request->operation == 0, function ($query) {
                    return $query->where('workstation_name', 'Painting');
                })
                ->select('workstation_id as id', 'workstation_name as text')
                ->orderBy('workstation_name', 'asc')->get();
        }
        // process
        if ($request->type == 'process') {
            return DB::connection('mysql_mes')->table('process_assignment')
                ->join('process', 'process.process_id', 'process_assignment.process_id')
                ->join('workstation', 'workstation.workstation_id', 'process_assignment.workstation_id')
                ->when($request->operation == 1, function ($query) {
                    return $query->where('workstation.operation_id', 1);
                })
                ->when($request->operation == 3, function ($query) {
                    return $query->where('workstation.operation_id', 3);
                })
                ->when($request->operation == 0, function ($query) {
                    return $query->where('workstation.workstation_name', 'Painting');
                })
                ->select('process_assignment.process_id as id', 'process.process_name as text')
                ->groupBy('process_assignment.process_id', 'process.process_name')->get();
        }
        // qc_inspector
        if ($request->type == 'qc_inspector') {
            return DB::connection('mysql_essex')->table('users as u')->join('departments as d', 'd.department_id', 'u.department_id')
                ->where('d.department', 'like', '%quality%')->where('u.status', 'Active')->where('u.user_type', 'Employee')
                ->select('u.user_id as id', 'u.employee_name as text')->orderBy('u.employee_name', 'asc')->get();
        }
        // operator
        if ($request->type == 'operator') {
            return DB::connection('mysql_essex')->table('users as u')->join('departments as d', 'd.department_id', 'u.department_id')
                ->when($request->operation == 1, function ($query) {
                    return $query->where('d.department', 'like', '%fabrication%');
                })
                ->when($request->operation == 3, function ($query) {
                    return $query->where('d.department', 'like', '%assembly%');
                })
                ->when($request->operation == 0, function ($query) {
                    return $query->where('d.department', 'like', '%painting%');
                })
                ->where('u.status', 'Active')->where('u.user_type', 'Employee')
                ->select('u.user_id as id', 'u.employee_name as text')
                ->orderBy('u.employee_name', 'asc')->get();
        }
    }

    public function tbl_qa_inspection_log_report(Request $request){
        $date_range = explode(" - ", $request->date_range);
        $start = Carbon::parse($date_range[0])->format('Y-m-d');
        $end = Carbon::parse($date_range[1])->format('Y-m-d');
        $operation_id = $request->operation;
        $qa_logs = DB::connection('mysql_mes')->table('quality_inspection as q')
            ->join('time_logs as t', 't.time_log_id', 'q.reference_id')
            ->join('job_ticket as j', 't.job_ticket_id','j.job_ticket_id')
            ->join('production_order as p','j.production_order','p.production_order')
            ->join('process as pr', 'pr.process_id','j.process_id')
            ->whereDate('q.qa_inspection_date', '>=', $start)
            ->whereDate('q.qa_inspection_date', '<=', $end)
            ->when($operation_id == 1, function ($query) {
                return $query->where('j.workstation', '!=', "Painting")->where('p.operation_id', 1);
            })
            ->when($operation_id == 0, function ($query) {
                return $query->where('j.workstation', "Painting");
            })
            ->when($operation_id == 3, function ($query) {
                return $query->where('p.operation_id', 3);
            })
            ->when($request->workstation, function ($query) use ($request) {
                return $query->where('j.workstation', $request->workstation);
            })
            ->when($request->process, function ($query) use ($request) {
                return $query->where('j.process_id', $request->process);
            })
            ->when($request->qc_status, function ($query) use ($request) {
                return $query->where('q.status', $request->qc_status);
            })
            ->when($request->qc_inspector, function ($query) use ($request) {
                return $query->where('q.qa_staff_id', $request->qc_inspector);
            })
            ->when($request->operator, function ($query) use ($request) {
                return $query->where('t.operator_id', $request->operator);
            })
            ->where('q.reference_type', 'Time Logs')
            ->select('p.production_order','p.sales_order', 'p.material_request', 'p.item_code', 'p.description', 'j.workstation', 'pr.process_name', 't.machine_code', 'q.qa_inspection_date', 't.good as batch_qty', 'q.reject_category_id', 'q.rejected_qty', 'q.actual_qty_checked', 'q.reference_id', 'q.sample_size', 'q.status', 't.operator_name', 'q.qa_staff_id', 'q.qa_id', 'p.customer');

        $qa_logs = DB::connection('mysql_mes')->table('quality_inspection as q')
            ->join('spotwelding_qty as t', 't.time_log_id', 'q.reference_id')
            ->join('job_ticket as j', 't.job_ticket_id','j.job_ticket_id')
            ->join('production_order as p','j.production_order','p.production_order')
            ->join('process as pr', 'pr.process_id','j.process_id')
            ->whereDate('q.qa_inspection_date', '>=', $start)
            ->whereDate('q.qa_inspection_date', '<=', $end)
            ->when($operation_id == 1, function ($query) {
                return $query->where('j.workstation', '!=', "Painting")->where('p.operation_id', 1);
            })
            ->when($operation_id == 0, function ($query) {
                return $query->where('j.workstation', "Painting");
            })
            ->when($operation_id == 3, function ($query) {
                return $query->where('p.operation_id', 3);
            })

            ->when($request->workstation, function ($query) use ($request) {
                return $query->where('j.workstation', $request->workstation);
            })
            ->when($request->process, function ($query) use ($request) {
                return $query->where('j.process_id', $request->process);
            })
            ->when($request->qc_status, function ($query) use ($request) {
                return $query->where('q.status', $request->qc_status);
            })
            ->when($request->qc_inspector, function ($query) use ($request) {
                return $query->where('q.qa_staff_id', $request->qc_inspector);
            })
            ->when($request->operator, function ($query) use ($request) {
                return $query->where('t.operator_id', $request->operator);
            })
            ->where('q.reference_type', 'Time Logs')
            ->select('p.production_order','p.sales_order', 'p.material_request', 'p.item_code', 'p.description', 'j.workstation', 'pr.process_name', 't.machine_code', 'q.qa_inspection_date', 't.good as batch_qty', 'q.reject_category_id', 'q.rejected_qty', 'q.actual_qty_checked', 'q.reference_id', 'q.sample_size', 'q.status', 't.operator_name', 'q.qa_staff_id', 'q.qa_id', 'p.customer')
            ->unionAll($qa_logs)->orderBy('qa_inspection_date', 'desc')->get();

        $reject_category = DB::connection('mysql_mes')->table('reject_category')
            ->select('reject_category_id', 'type', 'reject_category_name', 'category_description')
            ->orderBy('reject_category_id', 'ASC')->get();

        $qa_results = [];
        foreach ($qa_logs as $r) {
            $qa_results[$r->reject_category_id][$r->reference_id][$r->qa_id] = [
                "sample_size" => $r->sample_size,
                "actual_qty_checked" => $r->actual_qty_checked,
                "rejected_qty" => $r->rejected_qty,
            ];
        }

        $qa_staffs = collect($qa_logs)->pluck('qa_staff_id')->unique();

        $qa_staff_names = DB::connection('mysql_essex')->table('users')
            ->whereIn('user_id', $qa_staffs)->pluck('employee_name', 'user_id')->toArray();

        // $qa_logs = collect($qa_logs)->groupBy('reference_id')->toArray();

        $data = [];
        foreach ($qa_logs as $reference_id => $rows) {
            $reject_reasons = DB::connection('mysql_mes')->table('reject_reason')
                ->join('reject_list','reject_list.reject_list_id','reject_reason.reject_list_id')
                ->where('reject_reason.qa_id', $rows->qa_id)->orderBy('reject_reason.reject_list_id')
                ->distinct()->pluck('reject_list.reject_reason')->toArray();
            
            if (count($reject_reasons) > 0) {
                $reject_reasons = implode(", ", $reject_reasons);
            } else {
                $reject_reasons = 'GOOD';
            }

            $data[] = [
                "production_order" => $rows->production_order,
                "customer" => $rows->customer,
                "reference" => $rows->sales_order ? $rows->sales_order : $rows->material_request,
                "item_code" => $rows->item_code,
                "description" => strip_tags($rows->description),
                "workstation" => $rows->workstation,
                "process_name" => $rows->process_name,
                "machine_code" => $rows->machine_code,
                "qa_inspection_date" => Carbon::parse($rows->qa_inspection_date)->format('M. d, Y h:i A'),
                "batch_qty" => $rows->batch_qty,
                "reference_id" => $rows->reference_id,
                "status" => $rows->status,
                "remarks" => $reject_reasons,
                "operator" => $rows->operator_name,
                "qc_staff" => array_key_exists($rows->qa_staff_id, $qa_staff_names) ? $qa_staff_names[$rows->qa_staff_id] : null,
                'qa_id' => $rows->qa_id
            ];
        }

        return view('quality_inspection.tbl_qa_inspection_logs_report', compact('data', 'reject_category', 'qa_results'));
	}

    public function get_qa_checklist($status, $qa_id, $workstation, $header){
        $data_array = array_pluck( $header, 'reject_category_id');
        foreach($data_array as $row){
            $data1= DB::connection('mysql_mes')
            ->table('qa_checklist')
            ->leftjoin('reject_list', 'reject_list.reject_list_id', 'qa_checklist.reject_list_id')
            ->where('reject_list.reject_category_id', $row)
            ->where('qa_checklist.workstation_id', $workstation)->groupBy('reject_list.reject_category_id')->select('reject_list.reject_category_id')->first();
            
            if (!empty($data1)) {
                $reject_reasons=DB::connection('mysql_mes')->table('reject_list')
                    ->leftJoin('reject_reason','reject_list.reject_list_id','reject_reason.reject_list_id')
                    ->leftJoin('reject_category','reject_list.reject_category_id','reject_category.reject_category_id')
                    ->where('reject_category.reject_category_id', $row)
                    ->where('reject_reason.qa_id', $qa_id)
                    ->select('reject_reason.reject_value', 'reject_list.reject_reason')
                    ->get();
                    if(count($reject_reasons) > 0){
                        $data[]=[
                            'category' => $row,
                            'value' =>  $reject_reasons,
                            'stat' =>  "QC Failed",
                            'count' => 1,
                        ];
                    }else{
                        $data[]=[
                            'category' =>  $row,
                            'value' =>  [],
                            'stat' =>   "QC Passed",
                            'count' => 0,
                            'colspan' => 2

                        ]; 
                    }
                    
            }else{
                $data[]=[
                    'category' => 'n/a',
                    'value' =>  [],
                    'stat' =>  'n/a',
                    'count' => 0,
                    'colspan' => 2

                ]; 
            }
        }
        return $data;
    }
    public function get_tbl_qa_inspection_log_export($start, $end, $workstation,$customer, $prod, $item_code, $status,$processs, $qa_inspector, $operator){
        if($customer ==  'none'){
            $customer= "";
        }elseif($prod == 'none'){
            $prod= "";
        }elseif($item_code == 'none'){
            $item_code= "";
        }elseif($status == 'none'){
            $status= "";
        }elseif($processs == 'none'){
            $processs= "";
        }elseif($qa_inspector == 'none'){
            $qa_inspector= "";
        }elseif($operator == 'none'){
            $operator= "";
        }
        
        return Excel::download(new ExportDataQaInspectionLog($colspan_variable,$colspan_visual, $header_variable,$count_header_variable, $header_visual, $count_header_visual, $quality_check, $data, $width, $header), "QaInspectionLogs.xlsx");
    }

    public function submitRejectConfirmation($production_order, Request $request){
        DB::connection('mysql_mes')->beginTransaction();
        DB::connection('mysql')->beginTransaction();
        try {
            $now = Carbon::now(); 	
            if (!Auth::check()) {
                return response()->json(['success' => 0, 'message' => 'Session expired. Please reload the page.']);
            }

            $qa_user = DB::connection('mysql_essex')->table('users')
                ->where('user_id', Auth::user()->user_id)->first();

            if (!$qa_user) {
                return response()->json(['success' => 0, 'message' => 'Authorized QA Employee ID not found.']);
            }

            $qa_staff_name = $qa_user->employee_name;
            foreach($request->workstation as $time_log_id => $workstation) {
                if ($time_log_id) {
                    $request_rejected_qty = $request->confirmed_reject[$time_log_id];
                    $request_qa_id = $request->qa_id[$time_log_id];

                    if ($workstation == 'Spotwelding') {
                        $job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')
                            ->join('spotwelding_qty', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
                            ->where('spotwelding_qty.time_log_id', $time_log_id)->first();
                    }else{
                        $job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')
                            ->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
                            ->where('time_logs.time_log_id', $time_log_id)->first();
                    }
                    $production_order = $job_ticket_details->production_order;
                    $workstation = $job_ticket_details->workstation;
                    $prod_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->first();
    
                    $operation_id = $prod_details->operation_id;
                    $operation_id = $workstation == 'Painting' ? 2 : $operation_id;
    
                    if ($workstation == 'Spotwelding') {
                        $job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')
                            ->join('spotwelding_qty', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
                            ->where('spotwelding_qty.time_log_id', $time_log_id)->first();
                    }else{
                        $job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')
                            ->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
                            ->where('time_logs.time_log_id', $time_log_id)->first();
                    }

                    if ($request_rejected_qty > $prod_details->qty_to_manufacture) {
                        return response()->json(['success' => 0, 'message' => 'Rejected qty cannot be greater than ' . $prod_details->qty_to_manufacture]);
                    }

                    $reject_reasons = isset($request->reject_type[$time_log_id]) ? $request->reject_type[$time_log_id] : [];
                    foreach($reject_reasons as $reason_id => $list_id){
                        DB::connection('mysql_mes')->table('reject_reason')->where('reject_reason_id', $reason_id)->update(['reject_list_id' => $list_id]);
                    }

                    $update = [
                        'qa_inspection_date' => $now->toDateTimeString(),
                        'qa_staff_id' => Auth::user()->user_id,
                        'rejected_qty' => $request_rejected_qty,
                        'qa_disposition' => $request->disposition[$time_log_id],
                        'status' => ($request_rejected_qty > 0) ? 'QC Failed' : 'QC Passed',
                    ];

                    DB::connection('mysql_mes')->table('quality_inspection')->where('qa_id', $request_qa_id)->update($update);

                    $reject_qty = $request->old_reject_qty[$time_log_id] - $request_rejected_qty;

                    $good_qty_after_transaction = $job_ticket_details->good + $reject_qty;
        
                    if($good_qty_after_transaction < 0){
                        $good_qty_after_transaction = 0;
                    }

                    $rework_qty = $job_ticket_details->rework;
                    if($request->disposition[$time_log_id] == 'Rework'){
                        $rework_qty = $job_ticket_details->rework + $request_rejected_qty;
                    }

                    DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_details->job_ticket_id)->update(['rework' => $rework_qty]);

                    $update = [
                        'last_modified_at' => $now->toDateTimeString(),
                        'last_modified_by' => $qa_staff_name,
                        'good' => $good_qty_after_transaction,
                        'reject' => $request_rejected_qty
                    ];
                    
                    $logs_table = $workstation == 'Spotwelding' ? 'spotwelding_qty' : 'time_logs';
                    DB::connection('mysql_mes')->table($logs_table)->where('time_log_id', $time_log_id)->update($update);

                    $this->update_job_ticket($job_ticket_details->job_ticket_id);

                    if ($request->disposition[$time_log_id] == 'Scrap') {
                        if ($prod_details->item_classification == 'SA - Sub Assembly') {
                            $uom_details = DB::connection('mysql_mes')->table('uom')->where('uom_name', 'like', '%kilogram%')->first();
                            if ($uom_details) {
                            
                                $cutting_size = DB::connection('mysql')->table('tabItem Variant Attribute')
                                    ->where('parent', $prod_details->item_code)->where('attribute', 'like', '%cutting size%')->first();

    
                                if ($cutting_size) {
                                    $cutting_size = strtoupper($cutting_size->attribute_value);
                                    $cutting_size = str_replace(' ', '', preg_replace('/\s+/', '', $cutting_size));
                                    $cutting_size = explode("X", $cutting_size);

                                    $length = $cutting_size[0];
                                    $width = $cutting_size[1];
                                    $thickness = preg_replace("/[^0-9,.]/", '', $cutting_size[2]);
                                }

                                $material = DB::connection('mysql')->table('tabItem Variant Attribute')
                                    ->where('parent', $prod_details->item_code)->where('attribute', 'like', '%material%')->first();

                                if ($material) {
                                    $material = strtoupper($material->attribute_value);
                                }

                                $qty_in_cubic_mm = ($length * $width * $thickness) * $request_rejected_qty;

                                if($material == 'CRS'){
                                    $uom_arr_1 = DB::connection('mysql_mes')->table('uom_conversion')
                                        ->join('uom', 'uom.uom_id', 'uom_conversion.uom_id')
                                        ->where('uom.uom_name', 'like', '%cubic%')->pluck('uom_conversion_id')->toArray();

                                    $uom_arr_2 = DB::connection('mysql_mes')->table('uom_conversion')
                                        ->where('uom_id', $uom_details->uom_id)->pluck('uom_conversion_id')->toArray();

                                    $uom_conversion_id = array_intersect($uom_arr_1, $uom_arr_2);

                                    $uom_1_conversion_factor = DB::connection('mysql_mes')->table('uom_conversion')
                                        ->where('uom_conversion_id', $uom_conversion_id[0])
                                        ->where('uom_id', '!=', $uom_details->uom_id)->sum('conversion_factor');

                                    $uom_2_conversion_factor = DB::connection('mysql_mes')->table('uom_conversion')
                                        ->where('uom_conversion_id', $uom_conversion_id[0])
                                        ->where('uom_id', $uom_details->uom_id)->sum('conversion_factor');

                                    $conversion_factor = $uom_2_conversion_factor / $uom_1_conversion_factor;

                                    $scrap_qty = $qty_in_cubic_mm * $conversion_factor;

                                    $existing_scrap = DB::connection('mysql_mes')->table('scrap')
                                        ->where('material', $material)->where('uom_id', $uom_details->uom_id)
                                        ->where('thickness', $thickness)->first();

                                    if ($existing_scrap) {
                                        $scrap_qty = $scrap_qty + $existing_scrap->scrap_qty;
                                        $values = [
                                            'scrap_qty' => $scrap_qty,
                                            'last_modified_by' => $qa_staff_name,
                                        ];

                                        DB::connection('mysql_mes')->table('scrap')->where('scrap_id', $existing_scrap->scrap_id)->update($values);
                                    }else{
                                        $values = [
                                            'uom_conversion_id' => $uom_conversion_id[0],
                                            'uom_id' => $uom_details->uom_id,
                                            'material' => $material,
                                            'thickness' => $thickness,
                                            'scrap_qty' => $scrap_qty,
                                            'created_by' => $qa_staff_name,
                                        ];
        
                                        DB::connection('mysql_mes')->table('scrap')->insert($values);
                                    }
                                }
                            }
                        }
                    }
                }

                DB::connection('mysql')->commit();
                DB::connection('mysql_mes')->commit();

                return response()->json(['success' => 1, 'message' => 'QA Reject Confirmation has been updated.', 'operation' => $request->operation_id]);
            }
        } catch (Exception $e) {
            DB::connection('mysql_mes')->rollback();
            DB::connection('mysql')->rollback();

			return response()->json(['success' => 0, 'message' => 'An error occured. Please contact your system administrator.']);
        }
    }
}