<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use App\Exports\ExportDataQaInspectionLog; 
use Maatwebsite\Excel\Facades\Excel;

use DB;

class QualityInspectionController extends Controller
{
	public function get_checklist(Request $request, $workstation_name, $production_order, $process_id){
        $workstation_details = DB::connection('mysql_mes')->table('workstation')->where('workstation_name', $workstation_name)->first();
        if(!$workstation_details){
            return response()->json(['success' => 0, 'message' => 'No QA Checklist found for workstation ' . $workstation_name]);
        }

        $process_details = DB::connection('mysql_mes')->table('process')->where('process_id', $process_id)->first();
        if(!$process_details){
            return response()->json(['success' => 0, 'message' => 'Process not found.']);
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
            ->orderByRaw("FIELD(type, 'Minor Reject(s)','Major Reject(s)','Critical Reject(s)') DESC")
            // ->orderBy('reject_list.reject_category_id', 'desc')
            // ->orderBy('reject_category.reject_category_name', 'asc')
            ->get();
            
        }else{
            $q = DB::connection('mysql_mes')->table('qa_checklist')
            ->join('reject_list', 'qa_checklist.reject_list_id', 'reject_list.reject_list_id')
            ->join('reject_category', 'reject_category.reject_category_id', 'reject_list.reject_category_id')
            ->where('qa_checklist.workstation_id', $workstation_details->workstation_id)
            ->orderByRaw("FIELD(type, 'Minor Reject(s)','Major Reject(s)','Critical Reject(s)') DESC")
            // ->orderBy('reject_list.reject_category_id', 'desc')
            // ->orderBy('reject_category.reject_category_name', 'asc')
            ->get();
        }
        

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

        if(!$timelog_details){
            $timelog_details = [];
        }

        $inspection_type = $request->inspection_type;

        return view('quality_inspection.tbl_inspection_tabs', compact('checklist', 'production_order_details', 'workstation_details', 'process_details', 'sample_sizes', 'timelog_details', 'inspection_type', 'reject_levels'));
    }
    
    public function submit_quality_inspection(Request $request){
        $now = Carbon::now(); 	
    	if (!$request->inspected_by) {
    		return response()->json(['success' => 0, 'message' => 'Please tap Authorized QC Employee ID.']);
		}

    	$qa_user = DB::connection('mysql_essex')->table('users')
    		->where('user_id', $request->inspected_by)->first();

    	if (!$qa_user) {
    		return response()->json(['success' => 0, 'message' => 'Authorized QA Employee ID not found.']);
        }
        
        if($request->total_rejects > 0){
            if (!$request->qc_remarks) {
                return response()->json(['success' => 0, 'message' => 'Please select QC Remarks.']);
            }
        }

    	$qa_staff_name = $qa_user->employee_name;
    	if ($request->time_log_id) {
    		if ($request->inspection_type == 'Random Inspection') {
    			
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

                if ($request->workstation == 'Spotwelding') {
                    $insert = [
                        'reference_type' => 'Spotwelding',
                        'reference_id' => $job_ticket_details->job_ticket_id,
                        'qa_inspection_type' => $request->inspection_type,
                        'qa_inspection_date' => $now->toDateTimeString(),
                        'qa_staff_id' => $request->inspected_by,
                        'reject_level' => $request->reject_level,
                        'sample_size' => $request->sample_size,
                        'actual_qty_checked' => $request->total_checked,
                        'rejected_qty' => $request->total_rejects,
                        'for_rework_qty' => 0,
                        'status' => ($request->total_rejects > 0) ? 'QC Failed' : 'QC Passed',
                        'qc_remarks' => $request->qc_remarks,
                        // 'rejection_id' => $request->rejection_types,
                        'created_by' => $qa_staff_name,
                        'created_at' => $now->toDateTimeString()
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
                        'rejected_qty' => $request->total_rejects,
                        'for_rework_qty' => 0,
                        'status' => ($request->total_rejects > 0) ? 'QC Failed' : 'QC Passed',
                        'qc_remarks' => $request->qc_remarks,
                        // 'rejection_id' => $request->rejection_types,
                        'created_by' => $qa_staff_name,
                        'created_at' => $now->toDateTimeString()
                    ];
                }
            
	    		$good_qty_after_transaction = $job_ticket_details->good - $request->total_rejects;
	
				if($good_qty_after_transaction < 0){
					$good_qty_after_transaction = 0;
				}

				$update = [
					'last_modified_at' => $now->toDateTimeString(),
					'last_modified_by' => $qa_staff_name,
					'good' => $good_qty_after_transaction,
					'reject' => $request->total_rejects,
				];
                
                if ($request->workstation == 'Spotwelding') {
                    // comment
                    // DB::connection('mysql_mes')->table('spotwelding_qty')->where('time_log_id', $request->time_log_id)->update($update);
                }else{
                    DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $request->time_log_id)->update($update);
                }
				
                $qa_id = DB::connection('mysql_mes')->table('quality_inspection')->insertGetId($insert);

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
                
	    		$this->update_completed_qty_per_workstation($job_ticket_details->job_ticket_id);
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

                $prod_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->first();
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
                    // 'created_by' => $qa_staff_name,
                    // 'created_at' => $now->toDateTimeString()
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
                
                if ($request->workstation == 'Spotwelding') {
                    // commetn
                    // DB::connection('mysql_mes')->table('spotwelding_qty')->where('time_log_id', $request->time_log_id)->update($update);
                }else{
                    DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $request->time_log_id)->update($update);
                }

                $this->update_completed_qty_per_workstation($job_ticket_details->job_ticket_id);

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

			$this->updateProdOrderOps($production_order, $workstation);
			$this->update_produced_qty($production_order);

			return response()->json(['success' => 1, 'message' => 'Task updated.', 'details' => ['production_order' => $production_order, 'workstation' => $workstation]]);
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

            DB::connection('mysql')->table('tabProduction Order Operation')
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
        $user_details = DB::connection('mysql_essex')->table('users')
            ->join('designation', 'users.designation_id', '=', 'designation.des_id')
            ->join('departments', 'users.department_id', '=', 'departments.department_id')
            ->where('user_id', Auth::user()->user_id)->first();

        $item_code=  DB::connection('mysql_mes')->table('production_order as po')->select('item_code')->groupBy('item_code')->get();
        $customer=  DB::connection('mysql_mes')->table('production_order as po')->select('customer')->groupBy('customer')->get();
        $production_order=  DB::connection('mysql_mes')->table('production_order as po')->select('production_order')->get();
        $workstation=  DB::connection('mysql_mes')->table('workstation')->where('workstation_name','!=','Painting')->select('workstation_name','workstation_id')->get();
        $process=  DB::connection('mysql_mes')->table('process')->whereNotIn('process_name',['Loading','Unloading'])->get();

        $qc_staff= DB::connection('mysql_mes')->table('quality_inspection as qa')
        ->groupBy('qa.qa_staff_id')->select('qa.qa_staff_id')->get();
        foreach ($qc_staff as $row) {
           $emp_name= DB::connection('mysql_essex')
                        ->table('users')
                        ->where('users.user_id', $row->qa_staff_id)
                        ->select('users.employee_name')
                        ->first();
            if($row->qa_staff_id != null){
                 $qc_name[]=[
                "name" => $emp_name->employee_name,
                "user_id" =>  $row->qa_staff_id
                ];
            }
            
        }
        $operators = DB::connection('mysql_essex')->table('users')
            ->where('status', 'Active')->where('user_type', 'Employee')
            ->whereIn('designation_id', [46, 47, 53])->orderBy('employee_name', 'asc')
            ->select('user_id', 'employee_name')
            ->get();
        // dd($qc_name);
        $workstation_painting=  DB::connection('mysql_mes')->table('workstation as w')->join('operation as op', 'op.operation_id', 'w.operation_id')->where('op.operation_name','Fabrication')->where('w.workstation_name','Painting')->select('w.workstation_name','w.workstation_id')->get();
        $process_painting=  DB::connection('mysql_mes')->table('process')->whereIn('process_name',['Loading','Unloading'])->get();

        return view('quality_inspection.qa_dashboard', compact('user_details', 'workstation_painting','process_painting','item_code','customer','production_order','workstation','process', 'qc_name', 'operators'));
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

        $inspected_production_orders = collect($quality_inspection_query)->unique('production_order')->count();

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

        if($count_production_order > 0){
            $qa_efficiency = ($inspected_production_orders / $count_production_order) * 100;
        }else{
            $qa_efficiency = 0;
        }

        if($sample_size > 0){
            $performance = ($actual_qty_checked / $sample_size) * 100;
        }else{
            $performance = 0;
        }
        
        return $data = [
            'produced_qty' => number_format($produced_qty),
            'inspected_qty' => number_format($inspected_qty),
            'rejected_qty' => number_format($rejected_qty),
            'production_order' => number_format($count_production_order),
            'completed_wip_production_orders' => number_format($completed_wip_production_orders),
            'qa_efficiency' => number_format($qa_efficiency),
            'performance' => number_format($performance),
        ];
    }

    public function get_reject_for_confirmation(Request $request){
        $q = DB::connection('mysql_mes')->table('quality_inspection')
            ->join('time_logs', 'time_logs.time_log_id', 'quality_inspection.reference_id')
            ->join('job_ticket', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
            ->join('production_order', 'production_order.production_order', 'job_ticket.production_order')
            ->join('reject_reason', 'reject_reason.qa_id', 'quality_inspection.qa_id')
            ->join('reject_list', 'reject_list.reject_list_id', 'reject_reason.reject_list_id')
            ->where(function($q) use ($request) {
                $q->where('production_order.production_order', 'LIKE', '%'.$request->q.'%')
                ->orWhere('production_order.item_code', 'LIKE', '%'.$request->q.'%')
                ->orWhere('production_order.customer', 'LIKE', '%'.$request->q.'%');
            })
            ->where('quality_inspection.reference_type', 'Time Logs')
            ->where('quality_inspection.qa_inspection_type', 'Reject Confirmation')
            ->whereNotIn('quality_inspection.status', ['QC Passed', 'QC Failed'])
            ->select('job_ticket.process_id', 'job_ticket.workstation', 'production_order.production_order', 'production_order.item_code', 'production_order.stock_uom', 'quality_inspection.*', 'production_order.description', 'reject_list.reject_reason')
            ->orderBy('quality_inspection.created_at', 'desc');

        $list = DB::connection('mysql_mes')->table('quality_inspection')
            ->join('spotwelding_qty', 'spotwelding_qty.job_ticket_id', 'quality_inspection.reference_id')
            ->join('job_ticket', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
            ->join('production_order', 'production_order.production_order', 'job_ticket.production_order')
            ->join('reject_reason', 'reject_reason.qa_id', 'quality_inspection.qa_id')
            ->join('reject_list', 'reject_list.reject_list_id', 'reject_reason.reject_list_id')
            ->where(function($q) use ($request) {
                $q->where('production_order.production_order', 'LIKE', '%'.$request->q.'%')
                ->orWhere('production_order.item_code', 'LIKE', '%'.$request->q.'%')
                ->orWhere('production_order.customer', 'LIKE', '%'.$request->q.'%');
            })
            ->where('quality_inspection.reference_type', 'Spotwelding')
            ->where('quality_inspection.qa_inspection_type', 'Reject Confirmation')
            ->whereNotIn('quality_inspection.status', ['QC Passed', 'QC Failed'])
            ->select('job_ticket.process_id', 'job_ticket.workstation', 'production_order.production_order', 'production_order.item_code', 'production_order.stock_uom', 'quality_inspection.*', 'production_order.description', 'reject_list.reject_reason')
            ->orderBy('quality_inspection.created_at', 'desc')->union($q)->get();

        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // Create a new Laravel collection from the array data
        $itemCollection = collect($list);
        // Define how many items we want to be visible in each page
        $perPage = 10;
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
        // set url path for generted links
        $paginatedItems->setPath($request->url());

        $list = $paginatedItems;

        return view('quality_inspection.tbl_reject_confirmation', compact('list'));
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

        $checklist = DB::connection('mysql_mes')->table('qa_checklist')
            ->join('reject_list', 'qa_checklist.reject_list_id', 'reject_list.reject_list_id')
            // ->join('reject_category', 'reject_category.reject_category_id', 'reject_list.reject_category_id')
            ->where('qa_checklist.workstation_id', $workstation_details->workstation_id)
            // ->orderBy('reject_list.reject_category_id', 'desc')
            ->get();

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
        // dd($tab);
            
        return view('tables.tbl_reject_reason', compact('tab', 'validation_tab'));
    }

    // public function submit_reject_confirmation(Request $request){
    //     $qa_details = DB::connection('mysql_mes')->table('quality_inspection')->where('qa_id', $request->qa_id)->first();
    //     if(!$qa_details){
    //         return response()->json(['success' => 0, 'message' => 'Inspection not found.']);
    //     }

    //     if($qa_details->reference_type == 'Spotwelding'){
    //         $jt_details = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $qa_details->reference_id)->first();
    //         $rejected_qty = $request->old_reject_qty - $request->rejected_qty;
    //         $completed_qty_after_transaction = $jt_details->completed_qty - $rejected_qty;

    //         DB::connection('mysql_mes')->table('job_ticket')
    //             ->where('job_ticket_id', $jt_details->job_ticket_id)
    //             ->update(['completed_qty' => $completed_qty_after_transaction]);
    //     }else{
    //         $time_log_details = DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $qa_details->reference_id)->first();
    //         $jt_details = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $qa_details->reference_id)->first();
    //         $rejected_qty = $request->old_reject_qty - $request->rejected_qty;
    //         $completed_qty_after_transaction = $jt_details->completed_qty - $rejected_qty;

    //         DB::connection('mysql_mes')->table('time_logs')
    //             ->where('time_log_id', $qa_details->reference_id)
    //             ->update(['good' => $completed_qty_after_transaction, 'reject' => $request->rejected_qty]);
    //     }

    //     $this->update_completed_qty_per_workstation($jt_details->job_ticket_id);
    //     $this->updateProdOrderOps($jt_details->production_order, $jt_details->workstation);
    //     $this->update_produced_qty($jt_details->production_order);

    //     $update = [
    //         'qa_staff_id' => $request->qa_staff_id,
    //         'rejected_qty' => $request->rejected_qty,
    //         'status' => ($request->rejected_qty > 0) ? 'QC Failed' : 'QC Passed',
    //         'last_modified_by' => $request->qa_staff_name,
    //     ];

    //     DB::connection('mysql_mes')->table('quality_inspection')->where('qa_id', $request->qa_id)->update($update);

    //     DB::connection('mysql_mes')->table('reject_reason')
    //         ->where('reject_list_id', $request->old_reject_list_id)->where('qa_id', $request->qa_id)
    //         ->update(['reject_list_id' =>$request->reject_list_id]);
    // }

    public function tbl_qa_inspection_log_report(Request $request, $start, $end, $operation_id){
        $data=[];
        $qa_table = DB::connection('mysql_mes')
        ->table('quality_inspection as qa')
        ->whereBetween(DB::raw('DATE_FORMAT(qa_inspection_date, "%Y-%m-%d")'),[$start,$end])
        ->select('qa_id','reference_type')
        ->get();

        $header=db::connection('mysql_mes')->table('reject_category')->orderBy('reject_category_id', 'ASC')->get();
        foreach ($qa_table as $row) {
            
            if ($row->reference_type == "Spotwelding") {
                $order=DB::connection('mysql_mes')
                ->table('quality_inspection as qa')
                ->leftJoin('reject_reason as rjr','rjr.qa_id', 'qa.qa_id')
                ->leftJoin('reject_list as rl','rl.reject_list_id','rjr.reject_list_id')
                ->leftJoin('spotwelding_qty as spot_qty', 'qa.reference_id','spot_qty.job_ticket_id')
                ->leftJoin('job_ticket as jt', 'spot_qty.job_ticket_id','jt.job_ticket_id')
                ->leftjoin('process as process','process.process_id','jt.process_id')
                ->join('production_order as po','jt.production_order','po.production_order')
                ->where('qa.qa_id', $row->qa_id)
                ->where('po.operation_id', $operation_id)
                ->where('jt.workstation', 'LIKE', '%'.$request->workstation.'%')
                ->where('po.production_order', 'LIKE', '%'.$request->prod.'%')
                ->Where('po.customer', 'LIKE', '%'.$request->customer.'%')
                ->Where('po.item_code', 'LIKE', '%'.$request->item_code.'%')
                ->Where('process.process_name', 'LIKE', '%'.$request->process.'%')
                ->Where('qa.status', 'LIKE', '%'.$request->status.'%')
                ->Where('qa.qa_staff_id', 'LIKE', '%'.$request->qa_inspector.'%')
                ->Where('spot_qty.operator_id', 'LIKE', '%'.$request->operator.'%')
                ->select('qa.*','po.production_order','po.customer','po.project','po.item_code','po.description','jt.workstation','spot_qty.machine_code','po.sales_order','spot_qty.good','rjr.reject_reason_id','process.process_name', 'spot_qty.operator_name','po.cutting_size','po.material_request')
                ->first();

                if(!empty($order)){
                        $emp_name= DB::connection('mysql_essex')
                            ->table('users')
                            ->where('users.user_id', $order->qa_staff_id)
                            ->select('users.employee_name')
                            ->first();
                        $workstation_id= DB::connection('mysql_mes')->table('workstation')->where('workstation_name', $order->workstation)->first()->workstation_id;
                        $count= ($order->actual_qty_checked);
                        $goods= ($order->actual_qty_checked - $order->rejected_qty);
                        $reject = $order->rejected_qty;
                        $checklist_status=DB::connection('mysql_mes')->table('reject_reason')
                        ->leftJoin('reject_list','reject_list.reject_list_id','reject_reason.reject_list_id')
                        ->leftJoin('reject_category','reject_list.reject_category_id','reject_category.reject_category_id')
                        ->selectRaw('qa_id,GROUP_CONCAT(DISTINCT reject_list.reject_reason) as reject_reason')
                        ->where('reject_reason.qa_id',$row->qa_id)
                        ->orderBy('reject_reason.reject_list_id')
                        ->groupBy('reject_reason.qa_id')
                        ->first();

                        $data[]=[
                       "inspection_date" => date('F d, Y', strtotime($order->qa_inspection_date)),
                       "time" => date('h:ia', strtotime($order->qa_inspection_date)),
                       'production_order' => $order->production_order,
                       'reference_id' => ($order->sales_order == null)?$order->material_request: $order->sales_order,
                       'customer' => $order->customer,
                       'project' => $order->project,
                       'item_code'=> $order->item_code,
                       'decsription'=>strtok($order->description, ","),
                       'batch_qty'=> $order->good,
                       'workstation' => $order->workstation,
                       'process' => $order->process_name,
                       'machine' => $order->machine_code,
                       'samples' => $order->sample_size,
                       'reject' =>$reject,
                       'good'=> $goods,
                       'count' => $row->qa_id,
                       'actual_qty' =>$order->actual_qty_checked,
                       'checklist' => $this->get_qa_checklist($order->status, $row->qa_id, $workstation_id, $header),
                       'reference_document' =>'',
                       'status'=> $order->status,
                       'operator' => $order->operator_name,
                        'qc_staff' => $emp_name->employee_name,
                        'remarks' => empty($checklist_status)? "Good" : $checklist_status->reject_reason
               ];
                }

            }else{
                $validate= $request->workstation;
                $order=DB::connection('mysql_mes')
                ->table('quality_inspection as qa')
                ->leftJoin('reject_reason as rjr','rjr.qa_id', 'qa.qa_id')
                ->leftJoin('reject_list as rl','rl.reject_list_id','rjr.reject_list_id')
                ->leftJoin('time_logs as tl', 'qa.reference_id','tl.time_log_id')
                ->leftJoin('job_ticket as jt', 'tl.job_ticket_id','jt.job_ticket_id')
                ->leftJoin('process as process', 'process.process_id','jt.process_id')
                ->join('production_order as po','jt.production_order','po.production_order')
                ->where('qa.qa_id', $row->qa_id)
                ->where('qa.actual_qty_checked',"!=", 0)
                ->where('po.operation_id', $operation_id)
                ->when($validate, function ($query, $validate) {
                    return $query->where('jt.workstation', 'LIKE', $validate);
                })
                ->when($validate == null, function ($query, $validate) {
                    return $query->where('jt.workstation', '!=', "Painting");
                })
                ->where('po.production_order', 'LIKE', '%'.$request->prod.'%')
                ->Where('po.customer', 'LIKE', '%'.$request->customer.'%')
                ->Where('po.item_code', 'LIKE', '%'.$request->item_code.'%')
                ->Where('process.process_name', 'LIKE', '%'.$request->process.'%')
                ->Where('qa.status', 'LIKE', '%'.$request->status.'%')
                ->Where('qa.qa_staff_id', 'LIKE', '%'.$request->qa_inspector.'%')
                ->Where('tl.operator_id', 'LIKE', '%'.$request->operator.'%')
                ->select('qa.*','po.production_order','po.customer','po.project','po.item_code','po.description','jt.workstation','tl.machine_code','po.sales_order','tl.good','rjr.reject_reason_id','process.process_name','tl.operator_name','po.cutting_size', 'po.material_request')
                ->first();

                if (!empty($order)) {
                        $emp_name= DB::connection('mysql_essex')
                        ->table('users')
                        ->where('users.user_id', $order->qa_staff_id)
                        ->select('users.employee_name')
                        ->first();
                        $workstation_id= DB::connection('mysql_mes')->table('workstation')->where('workstation_name', $order->workstation)->first()->workstation_id;
                        $count= ($order->actual_qty_checked);
                        $goods= ($order->actual_qty_checked - $order->rejected_qty);
                        $reject = $order->rejected_qty;
                        $checklist_status=DB::connection('mysql_mes')->table('reject_reason')
                        ->leftJoin('reject_list','reject_list.reject_list_id','reject_reason.reject_list_id')
                        ->leftJoin('reject_category','reject_list.reject_category_id','reject_category.reject_category_id')
                        ->selectRaw('qa_id,GROUP_CONCAT(DISTINCT reject_list.reject_reason) as reject_reason')
                        ->where('reject_reason.qa_id',$row->qa_id)
                        ->orderBy('reject_reason.reject_list_id')
                        ->groupBy('reject_reason.qa_id')
                        ->first();
                        $data[]=[
                       "inspection_date" => date('F d, Y', strtotime($order->qa_inspection_date)),
                       "time" => date('h:ia', strtotime($order->qa_inspection_date)),
                       'production_order' => $order->production_order,
                       'reference_id' => ($order->sales_order == null)?$order->material_request: $order->sales_order,
                       'customer' => $order->customer,
                       'project' => $order->project,
                       'item_code'=> $order->item_code,
                       'decsription'=>$order->description,
                       'batch_qty'=> $order->good,
                       'workstation' => $order->workstation,
                       'process' => $order->process_name,
                       'machine' => $order->machine_code,
                       'samples' => $order->sample_size,
                       'reject' =>$reject,
                       'good'=> $goods,
                       'count' => ($count == 0 )? 0 : $count+1,
                       'actual_qty' =>$order->actual_qty_checked,
                       'checklist' => $this->get_qa_checklist($order->status, $row->qa_id, $workstation_id, $header),
                       'reference_document' =>'',
                       'status'=> $order->status,
                       'operator' => $order->operator_name,
                        'qc_staff' => $emp_name->employee_name,
                        'remarks' => empty($checklist_status)? "Good" : $checklist_status->reject_reason
                   ];

                }
                    
            } 
        }
        return view('quality_inspection.tbl_qa_inspection_logs_report', compact('header','data'));
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
    public function tbl_th_workstation_header($workstation, $category_type){
        
        $header=DB::connection('mysql_mes')->table('qa_checklist as ql')
        ->leftJoin('reject_list as rl','rl.reject_list_id', 'ql.reject_list_id')
        ->leftJoin('reject_category','rl.reject_category_id','reject_category.reject_category_id')
        ->where('reject_category.type','like', '%'.$category_type.'%')
        ->where('workstation_id', $workstation)
        ->selectRaw('ql.workstation_id,GROUP_CONCAT(rl.reject_checklist ORDER BY ql.reject_list_id SEPARATOR ",") as reject_checklist')
        ->groupBy('ql.workstation_id')
        ->orderBy('rl.reject_list_id', 'asc')
        ->first();
        if (!empty($header)) {
            $data= $header->reject_checklist;
        }else{
            $data="";
        }

        return $data;
    }
    public function qa_log_checklist_value($qa_id, $good, $reject, $actual, $id, $cutting_size){
        $data_array = explode(',',$id);
            //  dd($id);
            foreach ($data_array as $row) {
                $reject_reasons=DB::connection('mysql_mes')->table('reject_list')
                    ->leftJoin('reject_reason','reject_list.reject_list_id','reject_reason.reject_list_id')
                    ->leftJoin('reject_category','reject_list.reject_category_id','reject_category.reject_category_id')
                    ->where('reject_reason.reject_list_id', $row)
                    ->where('reject_reason.qa_id', $qa_id)
                    ->select('reject_reason.reject_value', 'reject_list.reject_checklist')
                    ->first();

                    if ($cutting_size == null){
                              $data[]=[
                                'size' => "none",
                                'value' =>  empty($reject_reasons->reject_value)? 'Ok':$reject_reasons->reject_value,
                                'stat' =>  empty($reject_reasons->reject_value)? 'good':'reject'
                                ]; 
                    }else{
                        if (!empty($reject_reasons)) {
                            $dimension = strtolower(str_replace(' ', '', $cutting_size));
                            $dimension_arr = explode("x", $dimension);
                                if (strtoupper("Length") == strtoupper($reject_reasons->reject_checklist)) {
                                     $size = preg_replace("/[^0-9,.]/", "", ($dimension_arr[0]));
            
                                }elseif (strtoupper("Width") == strtoupper($reject_reasons->reject_checklist)) {
                                    $size = preg_replace("/[^0-9,.]/", "", ($dimension_arr[1]));
            
                                }elseif (strtoupper("Thickness") == strtoupper($reject_reasons->reject_checklist)) {
                                    $size = preg_replace("/[^0-9,.]/", "", ($dimension_arr[2]));
                                }else{
                                     $size="none";
                                }
                                $data[]=[
                                    'size' => $size,
                                    'value' =>  empty($reject_reasons->reject_value)? 'Ok':$reject_reasons->reject_value,
                                    'stat' =>  empty($reject_reasons->reject_value)? 'good':'reject'
                                    ]; 
                          
                        }else{
                            $cutting_list=DB::connection('mysql_mes')->table('reject_list')
                            ->leftJoin('qa_checklist','reject_list.reject_list_id','qa_checklist.reject_list_id')
                            ->leftJoin('reject_category','reject_list.reject_category_id','reject_category.reject_category_id')
                            // ->where('reject_category.type','like', '%'.'Major Reject(s)'.'%')
                            ->where('reject_list.reject_list_id', $row)
                            ->select('reject_list.reject_checklist')
                            ->first();
                            $dimension = strtolower(str_replace(' ', '', $cutting_size));
                            $dimension_arr = explode("x", $dimension);
                                if (strtoupper("Length") == strtoupper($cutting_list->reject_checklist)) {
                                     $size = preg_replace("/[^0-9,.]/", "", ($dimension_arr[0]));
            
                                }elseif (strtoupper("Width") == strtoupper($cutting_list->reject_checklist)) {
                                    $size = preg_replace("/[^0-9,.]/", "", ($dimension_arr[1]));
            
                                }elseif (strtoupper("Thickness") == strtoupper($cutting_list->reject_checklist)) {
                                    $size = preg_replace("/[^0-9,.]/", "", ($dimension_arr[2]));
                                }else{
                                     $size="none";
                                }
                          
                            $data[]=[
                                "cutting_size" =>$cutting_size,
                                'size' => $size,
                                'value' =>  'Ok',
                                'stat' =>  'good'
                                ]; 
                        }
                        
                        
                    }

            }
            // dd(collect($data));
        return collect($data);

    }
    public function qa_log_checklist_good_value($qa_id, $good, $reject, $actual, $id, $cutting_size){
        $data_array = explode(',',$id);
        // dd($id);

            foreach ($data_array as $row) {
                $reject_reasons=DB::connection('mysql_mes')->table('reject_list')
                    ->leftJoin('qa_checklist','reject_list.reject_list_id','qa_checklist.reject_list_id')
                    ->leftJoin('reject_category','reject_list.reject_category_id','reject_category.reject_category_id')
                    ->where('reject_category.type','like', '%'.'Major Reject(s)'.'%')
                    ->where('reject_list.reject_list_id', $row)
                    ->select('reject_list.reject_checklist')
                    ->first();

                if ($cutting_size == null) {
                      $data[]=[
                        'size' => "none",
                        'value' => 'Ok',
                        'stat' => 'good',
                        ]; 
                    }else{
                        if (!empty($reject_reasons->reject_checklist)) {
                            $dimension = strtolower(str_replace(' ', '', $cutting_size));
                            $dimension_arr = explode("x", $dimension);
                                if (strtoupper("Length") == strtoupper($reject_reasons->reject_checklist)) {
                                     $size = preg_replace("/[^0-9,.]/", "", ($dimension_arr[0]));
            
                                }elseif (strtoupper("Width") == strtoupper($reject_reasons->reject_checklist)) {
                                    $size = preg_replace("/[^0-9,.]/", "", ($dimension_arr[1]));
            
                                }elseif (strtoupper("Thickness") == strtoupper($reject_reasons->reject_checklist)) {
                                    $size = empty(preg_replace("/[^0-9,.]/", "", ($dimension_arr[1])))? 'OK':preg_replace("/[^0-9,.]/", "", ($dimension_arr[1]));
                                }else{
                                     $size="none";
                                }
                          
                        }else{
                            $cutting="";
                            $size="none";
                        }
                        
                      $data[]=[
                        'size' => $size,
                        'value' =>  'Ok',
                        'stat' => 'good',
                        "cutting_size" => $cutting_size
                        ];   
                    }

            }
    // dd(collect($data));

        return collect($data);

    }
    public function qa_log_checklist($qa_id, $good, $reject, $actual, $id, $cutting_size){

        if ($reject != 0) {
            $checklist[]=[
                'checklist'=> $this->qa_log_checklist_value($qa_id, $good, $reject, $actual,$id, $cutting_size)
            ];
        }else{
            $checklist[]=[
                'checklist'=> $this->qa_log_checklist_good_value($qa_id, $good, $reject, $actual,$id, $cutting_size)
            ];
        } 
            // dd($qa_id);
                 
        // dd($checklist);
        return collect($checklist);

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
        
        $qa_table = DB::connection('mysql_mes')
        ->table('quality_inspection as qa')
        ->whereBetween(DB::raw('DATE_FORMAT(qa_inspection_date, "%Y-%m-%d")'),[$start,$end])
        ->select('qa_id','reference_type')
        ->get();
        $type_variable="Major Reject(s)";
        $type_visual="Minor Reject(s)";
        $t_variable="Remarks";
        $t_visual="Remarks";
        $status_index_for_variable=0;
        $status_index_for_visual=0.1;

        
        $header_variable=$this->tbl_th_workstation_header($workstation, $type_variable);
        $data_array_variable = explode(',',$header_variable);
        foreach ($data_array_variable as $row) {
            $header[]=[
                'reject_checklist' => $row
            ];
        }


        $header_visual=$this->tbl_th_workstation_header($workstation, $type_visual).'';
        $data_array_visual = explode(',',$header_visual);
        foreach ($data_array_visual as $row) {
            $header[]=[
                'reject_checklist' => $row
            ];
        }
        $count_header_visual= count(collect($data_array_visual));
        $count_header_variable= count(collect($data_array_variable));

        
        $quality_check= ($count_header_variable + $count_header_visual);
        $colspan_variable = $count_header_variable +1;
        $colspan_visual = $count_header_visual +1;

        $width_size=round(100/ $quality_check, 2);

       for($i=1;$i<=$width_size;$i++){
            $width[]=[
                'value'=> $width_size
            ];
        }


        $workstation_name= DB::connection('mysql_mes')->table('workstation')->where('workstation_id', $workstation)->first();
        foreach ($qa_table as $row) {

            $checklist_id_varible=DB::connection('mysql_mes')->table('qa_checklist as ql')
                ->leftJoin('reject_list as rl','rl.reject_list_id', 'ql.reject_list_id')
                ->leftJoin('reject_category','rl.reject_category_id','reject_category.reject_category_id')
                ->where('reject_category.type','like', '%'.$type_variable.'%')
                ->where('workstation_id', $workstation)
                ->selectRaw('ql.workstation_id,GROUP_CONCAT(ql.reject_list_id ORDER BY ql.reject_list_id SEPARATOR ",") as reject_id')
                ->groupBy('ql.workstation_id')
                ->orderBy('ql.reject_list_id', 'asc')
                ->first();

            $checklist_id_visual=DB::connection('mysql_mes')->table('qa_checklist as ql')
                ->leftJoin('reject_list as rl','rl.reject_list_id', 'ql.reject_list_id')
                ->leftJoin('reject_category','rl.reject_category_id','reject_category.reject_category_id')
                ->where('reject_category.type','like', '%'.$type_visual.'%')
                ->where('workstation_id', $workstation)
                ->selectRaw('ql.workstation_id,GROUP_CONCAT(ql.reject_list_id ORDER BY ql.reject_list_id SEPARATOR ",") as reject_id')
                ->groupBy('ql.workstation_id')
                ->orderBy('ql.reject_list_id', 'asc')
                ->first();

            if ($row->reference_type == "Spotwelding") {
                $order=DB::connection('mysql_mes')
                ->table('quality_inspection as qa')
                ->leftJoin('reject_reason as rjr','rjr.qa_id', 'qa.qa_id')
                ->leftJoin('reject_list as rl','rl.reject_list_id','rjr.reject_list_id')
                ->leftJoin('spotwelding_qty as spot_qty', 'qa.reference_id','spot_qty.job_ticket_id')
                ->leftJoin('job_ticket as jt', 'spot_qty.job_ticket_id','jt.job_ticket_id')
                ->leftjoin('process as process','process.process_id','jt.process_id')
                ->join('production_order as po','jt.production_order','po.production_order')
                ->where('qa.qa_id', $row->qa_id)
                ->where('jt.workstation', $workstation_name->workstation_name)
                ->when($prod != "none", function ($query1) use($prod){
                    return $query1->where('po.production_order', 'LIKE', '%'.$prod.'%');
                    })
                ->when($customer !="none" , function ($query2) use($customer){
                    return $query2->where('po.customer', 'LIKE', '%'.$customer.'%');
                    })
                ->when($item_code !="none" , function ($query3) use($item_code){
                    return $query3->where('po.item_code', 'LIKE', '%'.$item_code.'%');
                    })
                ->when($processs !="none" , function ($query4) use($processs){
                    return $query4->where('process.process_name', 'LIKE', '%'.$processs.'%');
                    })
                ->when($status !="none" , function ($query5) use($item_code){
                    return $query5->where('qa.status', 'LIKE', '%'.$status.'%');
                    })
                ->when($qa_inspector !="none" , function ($query6) use($qa_inspector){
                    return $query6->where('qa.qa_staff_id', 'LIKE', '%'.$qa_inspector.'%');
                    })
                ->when($operator !="none" , function ($query7) use($operator){
                    return $query7->where('spot_qty.operator_id', 'LIKE', '%'.$operator.'%');
                    })
                ->select('qa.*','po.production_order','po.customer','po.project','po.item_code','po.description','jt.workstation','spot_qty.machine_code','po.sales_order','spot_qty.good','rjr.reject_reason_id','process.process_name', 'spot_qty.operator_name', 'po.cutting_size' )
                ->first();

                if (!empty($order)) {
                        
                    $emp_name= DB::connection('mysql_essex')
                    ->table('users')
                    ->where('users.user_id', $order->qa_staff_id)
                    ->select('users.employee_name')
                    ->first();
                    $status_index_for_variable=0;
                    $status_index_for_visual=0.1;
                    $count= ($order->actual_qty_checked);
                    $goods= ($order->actual_qty_checked - $order->rejected_qty);
                    $reject = $order->rejected_qty;
                    $string = $checklist_id_varible->reject_id.",". $checklist_id_visual->reject_id;
                    // dd($string);
                    $data[]=[
                   "inspection_date" => date('F d, Y', strtotime($order->qa_inspection_date)),
                   "time" => date('h:ia', strtotime($order->qa_inspection_date)),
                   'production_order' => $order->production_order,
                   'reference_id' => $order->sales_order,
                   'customer' => $order->customer,
                   'project' => $order->project,
                   'item_code'=> $order->item_code,
                   'decsription'=>strtok($order->description, ","),
                   'batch_qty'=> $order->good,
                   'workstation' => $order->workstation,
                   'process' => $order->process_name,
                   'machine' => $order->machine_code,
                   'samples' => $order->sample_size,
                   'reject' =>$reject,
                   'good'=> $goods,
                   'count' => ($count == 0 )? 0 : $count+1,
                   'actual_qty' =>$order->actual_qty_checked,
                   'checklist' =>$this->qa_log_checklist($order->qa_id, $goods, $reject, $count,$string, $order->cutting_size),
                   'reference_document' =>'',
                   'status'=> $order->status,
                   'operator' => $order->operator_name,
                    'qc_staff' => $emp_name->employee_name,
                    'remarks' => $order->remarks,
               ];
                }
                 
                    


            }else{
                $order=DB::connection('mysql_mes')
                ->table('quality_inspection as qa')
                ->leftJoin('reject_reason as rjr','rjr.qa_id', 'qa.qa_id')
                ->leftJoin('reject_list as rl','rl.reject_list_id','rjr.reject_list_id')
                ->leftJoin('time_logs as tl', 'qa.reference_id','tl.time_log_id')
                ->leftJoin('job_ticket as jt', 'tl.job_ticket_id','jt.job_ticket_id')
                ->leftJoin('process as process', 'process.process_id','jt.process_id')
                ->join('production_order as po','jt.production_order','po.production_order')
                ->where('qa.qa_id', $row->qa_id)
                ->where('jt.workstation', $workstation_name->workstation_name)
                ->where('qa.actual_qty_checked',"!=", 0)
                ->when($prod != "none", function ($query1) use($prod){
                    return $query1->where('po.production_order', 'LIKE', '%'.$prod.'%');
                    })
                ->when($customer !="none" , function ($query2) use($customer){
                    return $query2->where('po.customer', 'LIKE', '%'.$customer.'%');
                    })
                ->when($item_code !="none" , function ($query3) use($item_code){
                    return $query3->where('po.item_code', 'LIKE', '%'.$item_code.'%');
                    })
                ->when($processs !="none" , function ($query4) use($processs){
                    return $query4->where('process.process_name', 'LIKE', '%'.$processs.'%');
                    })
                ->when($status !="none" , function ($query5) use($item_code){
                    return $query5->where('qa.status', 'LIKE', '%'.$status.'%');
                    })
                ->when($qa_inspector !="none" , function ($query6) use($qa_inspector){
                    return $query6->where('qa.qa_staff_id', 'LIKE', '%'.$qa_inspector.'%');
                    })
                ->when($operator !="none" , function ($query7) use($operator){
                    return $query7->where('tl.operator_id', 'LIKE', '%'.$operator.'%');
                    })
                ->select('qa.*','po.production_order','po.customer','po.project','po.item_code','po.description','jt.workstation','tl.machine_code','po.sales_order','tl.good','rjr.reject_reason_id','process.process_name','tl.operator_name', 'po.cutting_size')
                ->first();

                if (!empty($order)) {
                        
                    $emp_name= DB::connection('mysql_essex')
                    ->table('users')
                    ->where('users.user_id', $order->qa_staff_id)
                    ->select('users.employee_name')
                    ->first();
                    $status_index_for_variable=0;
                    $status_index_for_visual=0.1;
                    $count= ($order->actual_qty_checked);
                    $goods= ($order->actual_qty_checked - $order->rejected_qty);
                    $reject = $order->rejected_qty;
                    $string = $checklist_id_varible->reject_id.",". $checklist_id_visual->reject_id;
                    // dd($string);
                    $data[]=[
                   "inspection_date" => date('F d, Y', strtotime($order->qa_inspection_date)),
                   "time" => date('h:ia', strtotime($order->qa_inspection_date)),
                   'production_order' => $order->production_order,
                   'reference_id' => $order->sales_order,
                   'customer' => $order->customer,
                   'project' => $order->project,
                   'item_code'=> $order->item_code,
                   'decsription'=>strtok($order->description, ","),
                   'batch_qty'=> $order->good,
                   'workstation' => $order->workstation,
                   'process' => $order->process_name,
                   'machine' => $order->machine_code,
                   'samples' => $order->sample_size,
                   'reject' =>$reject,
                   'good'=> $goods,
                   'count' => ($count == 0 )? 0 : $count+1,
                   'actual_qty' =>$order->actual_qty_checked,
                   'checklist' =>$this->qa_log_checklist($order->qa_id, $goods, $reject, $count,$string, $order->cutting_size),
                   'reference_document' =>'',
                   'status'=> $order->status,
                   'operator' => $order->operator_name,
                    'qc_staff' => $emp_name->employee_name,
                    'remarks' => $order->remarks,
                   ];

                }
                    
            }

           
        }
        // dd($data);
        // return view('quality_inspection.tbl_qa_inspection_logs_report', compact('colspan_variable','colspan_visual','header_variable','count_header_variable','header_visual','count_header_visual', 'quality_check','data','width','header'));

        return Excel::download(new ExportDataQaInspectionLog($colspan_variable,$colspan_visual, $header_variable,$count_header_variable, $header_visual, $count_header_visual, $quality_check, $data, $width, $header), "QaInspectionLogs.xlsx");
           
    }
    public function tbl_qa_inspection_log_report_painting(Request $request, $start, $end, $workstation){
        $data=[];
        $qa_table = DB::connection('mysql_mes')
        ->table('quality_inspection as qa')
        ->whereBetween(DB::raw('DATE_FORMAT(qa_inspection_date, "%Y-%m-%d")'),[$start,$end])
        ->select('qa_id','reference_type')
        ->get();
        $type_variable="Major Reject(s)";
        $type_visual="Minor Reject(s)";
        $t_variable="Remarks";
        $t_visual="Remarks";
        $status_index_for_variable=0;
        $status_index_for_visual=0.1;

        
        $header_variable=$this->tbl_th_workstation_header($workstation, $type_variable);
        $data_array_variable = explode(',',$header_variable);
        foreach ($data_array_variable as $row) {
            $header[]=[
                'reject_checklist' => $row
            ];
        }


        $header_visual=$this->tbl_th_workstation_header($workstation, $type_visual).'';
        $data_array_visual = explode(',',$header_visual);
        foreach ($data_array_visual as $row) {
            $header[]=[
                'reject_checklist' => $row
            ];
        }
        $count_header_visual= count(collect($data_array_visual));
        $count_header_variable= count(collect($data_array_variable));

        
        $quality_check= ($count_header_variable + $count_header_visual);
        $colspan_variable = $count_header_variable +1;
        $colspan_visual = $count_header_visual +1;

        $width_size=round(100/ $quality_check, 2);

       for($i=1;$i<=$width_size;$i++){
            $width[]=[
                'value'=> $width_size
            ];
        }


        $workstation_name= DB::connection('mysql_mes')->table('workstation')->where('workstation_id', $workstation)->first();
        foreach ($qa_table as $row) {

            $checklist_id_varible=DB::connection('mysql_mes')->table('qa_checklist as ql')
                ->leftJoin('reject_list as rl','rl.reject_list_id', 'ql.reject_list_id')
                ->leftJoin('reject_category','rl.reject_category_id','reject_category.reject_category_id')
                ->where('reject_category.type','like', '%'.$type_variable.'%')
                ->where('workstation_id', $workstation)
                ->selectRaw('ql.workstation_id,GROUP_CONCAT(ql.reject_list_id ORDER BY ql.reject_list_id SEPARATOR ",") as reject_id')
                ->groupBy('ql.workstation_id')
                ->orderBy('ql.reject_list_id', 'asc')
                ->first();

            $checklist_id_visual=DB::connection('mysql_mes')->table('qa_checklist as ql')
                ->leftJoin('reject_list as rl','rl.reject_list_id', 'ql.reject_list_id')
                ->leftJoin('reject_category','rl.reject_category_id','reject_category.reject_category_id')
                ->where('reject_category.type','like', '%'.$type_visual.'%')
                ->where('workstation_id', $workstation)
                ->selectRaw('ql.workstation_id,GROUP_CONCAT(ql.reject_list_id ORDER BY ql.reject_list_id SEPARATOR ",") as reject_id')
                ->groupBy('ql.workstation_id')
                ->orderBy('ql.reject_list_id', 'asc')
                ->first();

 
                $order=DB::connection('mysql_mes')
                ->table('quality_inspection as qa')
                ->leftJoin('reject_reason as rjr','rjr.qa_id', 'qa.qa_id')
                ->leftJoin('reject_list as rl','rl.reject_list_id','rjr.reject_list_id')
                ->leftJoin('time_logs as tl', 'qa.reference_id','tl.time_log_id')
                ->leftJoin('job_ticket as jt', 'tl.job_ticket_id','jt.job_ticket_id')
                ->leftJoin('process as process', 'process.process_id','jt.process_id')
                ->join('production_order as po','jt.production_order','po.production_order')
                ->where('qa.qa_id', $row->qa_id)
                ->where('jt.workstation', $workstation_name->workstation_name)
                ->where('qa.actual_qty_checked',"!=", 0)
                ->where('po.production_order', 'LIKE', '%'.$request->prod.'%')
                ->Where('po.customer', 'LIKE', '%'.$request->customer.'%')
                ->Where('po.item_code', 'LIKE', '%'.$request->item_code.'%')
                ->Where('process.process_name', 'LIKE', '%'.$request->process.'%')
                ->Where('qa.status', 'LIKE', '%'.$request->status.'%')
                ->Where('qa.qa_staff_id', 'LIKE', '%'.$request->qa_inspector.'%')
                ->Where('tl.operator_id', 'LIKE', '%'.$request->operator.'%')
                ->select('qa.*','po.production_order','po.customer','po.project','po.item_code','po.description','jt.workstation','tl.machine_code','po.sales_order','tl.good','rjr.reject_reason_id','process.process_name','tl.operator_name', 'po.cutting_size')
                ->first();

                if (!empty($order)) {
                        
                        $emp_name= DB::connection('mysql_essex')
                        ->table('users')
                        ->where('users.user_id', $order->qa_staff_id)
                        ->select('users.employee_name')
                        ->first();
                        $status_index_for_variable=0;
                        $status_index_for_visual=0.1;
                        $count= ($order->actual_qty_checked);
                        $goods= ($order->actual_qty_checked - $order->rejected_qty);
                        $reject = $order->rejected_qty;
                        $string = $checklist_id_varible->reject_id.",". $checklist_id_visual->reject_id;

                        $checklist_status=DB::connection('mysql_mes')->table('reject_reason')
                        ->leftJoin('reject_list','reject_list.reject_list_id','reject_reason.reject_list_id')
                        ->leftJoin('reject_category','reject_list.reject_category_id','reject_category.reject_category_id')
                        ->selectRaw('qa_id,GROUP_CONCAT(DISTINCT reject_list.reject_reason) as reject_reason')
                        ->where('reject_reason.qa_id',$row->qa_id)
                        // ->where('reject_category.type','like', '%'.'Major Reject(s)'.'%')
                        ->orderBy('reject_reason.reject_list_id')
                        ->groupBy('reject_reason.qa_id')
                        ->first();
                        // dd($string);
                        $data[]=[
                       "inspection_date" => date('F d, Y', strtotime($order->qa_inspection_date)),
                       "time" => date('h:ia', strtotime($order->qa_inspection_date)),
                       'production_order' => $order->production_order,
                       'reference_id' => $order->sales_order,
                       'customer' => $order->customer,
                       'project' => $order->project,
                       'item_code'=> $order->item_code,
                       'decsription'=>strtok($order->description, ","),
                       'batch_qty'=> $order->good,
                       'workstation' => $order->workstation,
                       'process' => $order->process_name,
                       'machine' => $order->machine_code,
                       'samples' => $order->sample_size,
                       'reject' =>$reject,
                       'good'=> $goods,
                       'count' => ($count == 0 )? 0 : $count+1,
                       'actual_qty' =>$order->actual_qty_checked,
                       'checklist' =>$this->qa_log_checklist($order->qa_id, $goods, $reject, $count,$string, $order->cutting_size),
                       'reference_document' =>'',
                       'status'=> $order->status,
                       'operator' => $order->operator_name,
                        'qc_staff' => $emp_name->employee_name,
                        'remarks' => empty($checklist_status)? "Good" : $checklist_status->reject_reason
                   ];

                }


           
        }
           

        
        // return $data;
         return view('quality_inspection.tbl_qa_inspection_logs_report', compact('colspan_variable','colspan_visual','header_variable','count_header_variable','header_visual','count_header_visual', 'quality_check','data','width','header'));
    }
    public function get_tbl_qa_inspection_log_export_painting($start, $end, $workstation,$customer, $prod, $item_code, $status,$processs, $qa_inspector, $operator){
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
        
        $qa_table = DB::connection('mysql_mes')
        ->table('quality_inspection as qa')
        ->whereBetween(DB::raw('DATE_FORMAT(qa_inspection_date, "%Y-%m-%d")'),[$start,$end])
        ->select('qa_id','reference_type')
        ->get();
        $type_variable="Major Reject(s)";
        $type_visual="Minor Reject(s)";
        $t_variable="Remarks";
        $t_visual="Remarks";
        $status_index_for_variable=0;
        $status_index_for_visual=0.1;

        
        $header_variable=$this->tbl_th_workstation_header($workstation, $type_variable);
        $data_array_variable = explode(',',$header_variable);
        foreach ($data_array_variable as $row) {
            $header[]=[
                'reject_checklist' => $row
            ];
        }


        $header_visual=$this->tbl_th_workstation_header($workstation, $type_visual).'';
        $data_array_visual = explode(',',$header_visual);
        foreach ($data_array_visual as $row) {
            $header[]=[
                'reject_checklist' => $row
            ];
        }
        $count_header_visual= count(collect($data_array_visual));
        $count_header_variable= count(collect($data_array_variable));

        
        $quality_check= ($count_header_variable + $count_header_visual);
        $colspan_variable = $count_header_variable +1;
        $colspan_visual = $count_header_visual +1;

        $width_size=round(100/ $quality_check, 2);

       for($i=1;$i<=$width_size;$i++){
            $width[]=[
                'value'=> $width_size
            ];
        }


        $workstation_name= DB::connection('mysql_mes')->table('workstation')->where('workstation_id', $workstation)->first();
        foreach ($qa_table as $row) {

            $checklist_id_varible=DB::connection('mysql_mes')->table('qa_checklist as ql')
                ->leftJoin('reject_list as rl','rl.reject_list_id', 'ql.reject_list_id')
                ->leftJoin('reject_category','rl.reject_category_id','reject_category.reject_category_id')
                ->where('reject_category.type','like', '%'.$type_variable.'%')
                ->where('workstation_id', $workstation)
                ->selectRaw('ql.workstation_id,GROUP_CONCAT(ql.reject_list_id ORDER BY ql.reject_list_id SEPARATOR ",") as reject_id')
                ->groupBy('ql.workstation_id')
                ->orderBy('ql.reject_list_id', 'asc')
                ->first();

            $checklist_id_visual=DB::connection('mysql_mes')->table('qa_checklist as ql')
                ->leftJoin('reject_list as rl','rl.reject_list_id', 'ql.reject_list_id')
                ->leftJoin('reject_category','rl.reject_category_id','reject_category.reject_category_id')
                ->where('reject_category.type','like', '%'.$type_visual.'%')
                ->where('workstation_id', $workstation)
                ->selectRaw('ql.workstation_id,GROUP_CONCAT(ql.reject_list_id ORDER BY ql.reject_list_id SEPARATOR ",") as reject_id')
                ->groupBy('ql.workstation_id')
                ->orderBy('ql.reject_list_id', 'asc')
                ->first();

            
            $order=DB::connection('mysql_mes')
                ->table('quality_inspection as qa')
                ->leftJoin('reject_reason as rjr','rjr.qa_id', 'qa.qa_id')
                ->leftJoin('reject_list as rl','rl.reject_list_id','rjr.reject_list_id')
                ->leftJoin('time_logs as tl', 'qa.reference_id','tl.time_log_id')
                ->leftJoin('job_ticket as jt', 'tl.job_ticket_id','jt.job_ticket_id')
                ->leftJoin('process as process', 'process.process_id','jt.process_id')
                ->join('production_order as po','jt.production_order','po.production_order')
                ->where('qa.qa_id', $row->qa_id)
                ->where('jt.workstation', $workstation_name->workstation_name)
                ->where('qa.actual_qty_checked',"!=", 0)
                ->when($prod != "none", function ($query1) use($prod){
                    return $query1->where('po.production_order', 'LIKE', '%'.$prod.'%');
                    })
                ->when($customer !="none" , function ($query2) use($customer){
                    return $query2->where('po.customer', 'LIKE', '%'.$customer.'%');
                    })
                ->when($item_code !="none" , function ($query3) use($item_code){
                    return $query3->where('po.item_code', 'LIKE', '%'.$item_code.'%');
                    })
                ->when($processs !="none" , function ($query4) use($processs){
                    return $query4->where('process.process_name', 'LIKE', '%'.$processs.'%');
                    })
                ->when($status !="none" , function ($query5) use($item_code){
                    return $query5->where('qa.status', 'LIKE', '%'.$status.'%');
                    })
                ->when($qa_inspector !="none" , function ($query6) use($qa_inspector){
                    return $query6->where('qa.qa_staff_id', 'LIKE', '%'.$qa_inspector.'%');
                    })
                ->when($operator !="none" , function ($query7) use($operator){
                    return $query7->where('tl.operator_id', 'LIKE', '%'.$operator.'%');
                    })
                ->select('qa.*','po.production_order','po.customer','po.project','po.item_code','po.description','jt.workstation','tl.machine_code','po.sales_order','tl.good','rjr.reject_reason_id','process.process_name','tl.operator_name', 'po.cutting_size')
                ->first();

                if (!empty($order)) {
                        
                    $emp_name= DB::connection('mysql_essex')
                    ->table('users')
                    ->where('users.user_id', $order->qa_staff_id)
                    ->select('users.employee_name')
                    ->first();
                    $status_index_for_variable=0;
                    $status_index_for_visual=0.1;
                    $count= ($order->actual_qty_checked);
                    $goods= ($order->actual_qty_checked - $order->rejected_qty);
                    $reject = $order->rejected_qty;
                    $string = $checklist_id_varible->reject_id.",". $checklist_id_visual->reject_id;
                    // dd($string);
                    $data[]=[
                   "inspection_date" => date('F d, Y', strtotime($order->qa_inspection_date)),
                   "time" => date('h:ia', strtotime($order->qa_inspection_date)),
                   'production_order' => $order->production_order,
                   'reference_id' => $order->sales_order,
                   'customer' => $order->customer,
                   'project' => $order->project,
                   'item_code'=> $order->item_code,
                   'decsription'=>strtok($order->description, ","),
                   'batch_qty'=> $order->good,
                   'workstation' => $order->workstation,
                   'process' => $order->process_name,
                   'machine' => $order->machine_code,
                   'samples' => $order->sample_size,
                   'reject' =>$reject,
                   'good'=> $goods,
                   'count' => ($count == 0 )? 0 : $count+1,
                   'actual_qty' =>$order->actual_qty_checked,
                   'checklist' =>$this->qa_log_checklist($order->qa_id, $goods, $reject, $count,$string, $order->cutting_size),
                   'reference_document' =>'',
                   'status'=> $order->status,
                   'operator' => $order->operator_name,
                    'qc_staff' => $emp_name->employee_name,
                    'remarks' => $order->remarks,
                   ];

                }
                    
            

           
        }
        // dd($data);
        // return view('quality_inspection.tbl_qa_inspection_logs_report', compact('colspan_variable','colspan_visual','header_variable','count_header_variable','header_visual','count_header_visual', 'quality_check','data','width','header'));

        return Excel::download(new ExportDataQaInspectionLog($colspan_variable,$colspan_visual, $header_variable,$count_header_variable, $header_visual, $count_header_visual, $quality_check, $data, $width, $header), "QaInspectionLogs.xlsx");
           
    }
}