<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Validator;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Image;

use App\Traits\GeneralTrait;

class SpotweldingController extends Controller
{
	use GeneralTrait;
	
	public function operator_spotwelding_dashboard(){
		 $tabWorkstation= DB::connection('mysql_mes')->table('workstation')->where('workstation_name', 'Spotwelding')
        	->select('workstation_name', 'workstation_id')->first();

		$workstation_list = DB::connection('mysql_mes')
			->table('workstation as w')
			->join('operation as op','op.operation_id', "w.operation_id")
			->where('op.operation_name', 'Fabrication')
			->orderBy('w.order_no', 'desc')->pluck('w.workstation_name');
        
        $now = Carbon::now();
        $workstation = $tabWorkstation->workstation_name;
        $workstation_id = $tabWorkstation->workstation_id;
        $workstation_name = 'Spotwelding';
        $date = $now->format('M d Y');
        $day_name= $now->format('l');

        return view('operator_spotwelding_dashboard', compact('workstation','workstation_name', 'day_name', 'date', 'workstation_list', 'workstation_id'));
	}

	public function spotwelding_dashboard($machine, $job_ticket_id){
		if(!Auth::user()){
			return redirect('/operator/Spotwelding');
		}

		$workstation = 'Spotwelding';

		$job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)->first();

		$machine_workstations = DB::connection('mysql_mes')->table('machine as m')
			->join('workstation_machine AS wm', 'm.machine_code', 'wm.machine_code')
			->where('m.machine_id', $machine)->orWhere('m.machine_code', $machine)->get();

		$machine_details = DB::connection('mysql_mes')->table('machine')->where('machine_id', $machine)
			->orWhere('machine_code', $machine)->first();

		return view('spotwelding_dashboard', compact('machine_details', 'workstation', 'machine_workstations', 'job_ticket_details'));
	}

	public function start_task(Request $request){
    	try {
    		if (!$request->operator_id) {
    			return response()->json(['success' => 0, 'message' => 'Please enter Operator ID.']);
    		}

    		$now = Carbon::now();
	    	$operator = DB::connection('mysql_essex')->table('users')->where('user_id', $request->operator_id)->first();
	    	if (!$operator) {
	    		return response()->json(['success' => 0, 'message' => 'Operator not found.']);
	    	}

	    	$machine_name = DB::connection('mysql_mes')->table('machine')
	    		->where('machine_code', $request->machine_code)->first()->machine_name;

	    	$spotwelding_parts = DB::connection('mysql_mes')->table('spotwelding_qty')
	    		->where('job_ticket_id', $request->job_ticket_id)
	    		->distinct()->pluck('parts', 'spotwelding_part_id');

	    	$spotwelding_part_id = uniqid();
	    	foreach ($spotwelding_parts as $id => $parts) {
	    		$parts_array = array_filter(explode(',', $parts));
	    		$select_parts_arr = array_column($request->parts, 'part_code');
	    		// Sort the array elements 
				sort($parts_array); 
				sort($select_parts_arr); 
				// Check for equality 
				if ($parts_array == $select_parts_arr) 
				    $spotwelding_part_id = $id; 
	    	}

	    	$total_good = DB::connection('mysql_mes')->table('spotwelding_qty')
	    		->where('spotwelding_part_id', $spotwelding_part_id)->sum('good');

	    	$balance_qty = $request->qty_to_manufacture - $total_good;
	    	if ($balance_qty <= 0) {
	    		return response()->json(['success' => 0, 'message' => 'Task already completed.', 'details' => []]);
	    	}

	    	$log = [
				'job_ticket_id' => $request->job_ticket_id,
				'spotwelding_part_id' => $spotwelding_part_id,
				'from_time' => $now->toDateTimeString(),
				'machine_code' => $request->machine_code,
				'machine_name' => $machine_name,
				'operator_id' => $request->operator_id,
				'operator_name' => $operator->employee_name,
				'operator_nickname' => $operator->nick_name,
				'status' => 'In Progress',
				'created_by' => $operator->employee_name,
				'created_at' => $now->toDateTimeString(),
				'parts' => $request->process_description
	    	];

	    	$parts = [];
	    	foreach ($request->parts as $part) {
	    		$existing_spotwelding_part = DB::connection('mysql_mes')->table('spotwelding_part')
					->where('housing_production_order', $request->production_order)
					->where('part_code', $part['part_code'])->exists();

    			if (!$existing_spotwelding_part) {
					$stocks = DB::connection('mysql_mes')->table('fabrication_inventory')->where('item_code', $part['part_code'])->where('balance_qty', '>', 0)->exists();
					// if(!$stocks){
					// 	return response()->json(["success" => 0, "message" => "No available quantity for item " . $part['part_code']]);
					// }
					
    				$parts[] = [
			    		'housing_production_order' => $request->production_order,
						'spotwelding_part_id' => $spotwelding_part_id,
						'housing_code' => $request->ho_code,
						'reference_no' => $request->reference_no,
						'part_production_order' => $part['production_order'],
						'part_category' => $part['category'],
						'part_code' => $part['part_code'],
						'created_by' => $operator->employee_name,
						'created_at' => $now->toDateTimeString(),
			    	];
    			}	
	    	}

	    	DB::connection('mysql_mes')->table('spotwelding_part')->insert($parts);
	    	DB::connection('mysql_mes')->table('spotwelding_qty')->insert($log);

	    	$details = [	
	    		'production_order' => $request->production_order,
	    		'process_id' => $request->process_id,
	    	];
	    	
			$production_order = DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)->first();
			if ($production_order && $production_order->status == 'Not Started') {
				DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)->update(['status' => 'In Progress']);
			}

			$this->update_completed_qty_per_workstation($request->job_ticket_id);
			$this->update_production_actual_start_end($request->production_order);
			
	    	return response()->json(['success' => 1, 'message' => 'Task Updated.', 'details' => $details]);
    	} catch (Exception $e) {
    		return response()->json(["success" => 0, "message" => $e->getMessage()]);
    	}
	}

	public function end_task(Request $request){
        try {
			$now = Carbon::now();
			$current_task = DB::connection('mysql_mes')->table('spotwelding_qty')
				->where('time_log_id', $request->id)->first();

			$seconds = $now->diffInSeconds(Carbon::parse($current_task->from_time));
			$duration= $seconds / 3600;

			$cycle_time_in_seconds = $seconds / $request->completed_qty;

			$good_qty = $request->completed_qty - $current_task->reject;

			$operation_id = DB::connection('mysql_mes')->table('production_order')
				->where('production_order', $request->production_order)->first()->operation_id;
			
            $breaktime = $this->get_breaktime($current_task->from_time, $now, $operation_id);
			
            $update = [
                'last_modified_at' => $now->toDateTimeString(),
                'last_modified_by' => Auth::user()->employee_name,
                'to_time' => $now->toDateTimeString(),
                'good' => $good_qty,
                'status' => 'Completed',
				'duration' => $duration,
				'cycle_time_in_seconds' => $cycle_time_in_seconds,
				'breaktime_in_mins' => $breaktime
			];
			
			DB::connection('mysql_mes')->table('spotwelding_qty')->where('time_log_id', $request->id)->update($update);

			$process_id = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $current_task->job_ticket_id)->first()->process_id;
			
			$this->updateProdOrderOps($request->production_order, $request->workstation, $process_id);
			$this->update_completed_qty_per_workstation($current_task->job_ticket_id);
			$this->update_produced_qty($request->production_order);
			$this->update_production_actual_start_end($request->production_order);

			$ho_bom = DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)->first()->bom_no;

			$parts = DB::connection('mysql_mes')->table('spotwelding_part')->where('spotwelding_part_id', $current_task->spotwelding_part_id)->get();
			foreach ($parts as $part) {
				$bom_qty = DB::connection('mysql')->table('tabBOM Item')->where('parent', $ho_bom)->where('item_code', $part->part_code)->sum('qty');
				$this->update_inventory($part->part_code, $bom_qty * $good_qty, 1);
			}

            return response()->json(['success' => 1, 'message' => 'Task has been updated.']);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
	}
	
	public function update_inventory($item_code, $qty, $is_withdrawn){
        $existing_inv = DB::connection('mysql_mes')->table('fabrication_inventory')
            ->where('item_code', $item_code)->first();
		if($qty > 0){
			if ($existing_inv) {
				$current_balance_qty = DB::connection('mysql_mes')->table('fabrication_inventory')
					->where('item_code', $item_code)->sum('balance_qty');
	
				if ($is_withdrawn == 1) {
					// deduct
					$balance_qty = $current_balance_qty - $qty;
					$balance_qty = ($balance_qty <= 0) ? 0 : $balance_qty;
				}else{
					// add
					$balance_qty = $current_balance_qty + $qty;
				}
	
				$data = [
					'balance_qty' => $balance_qty,
					'last_modified_by' => Auth::user()->employee_name
				];
	
				DB::connection('mysql_mes')->table('fabrication_inventory')
						->where('item_code', $item_code)->update($data);
			}else{
				$description = DB::connection('mysql_mes')->table('production_order')
					->where('item_code', $item_code)->first()->description;

				$insert = [
					'item_code' => $item_code,
					'description' => $description,
					'balance_qty' => $qty,
					'created_by' => Auth::user()->employee_name
				];
	
				DB::connection('mysql_mes')->table('fabrication_inventory')->insert($insert);
			}
		}
	}
	
	public function restart_task(Request $request){
    	$spotwelding_qty_det = DB::connection('mysql_mes')->table('spotwelding_qty')->where('time_log_id', $request->id)->first();

    	DB::connection('mysql_mes')->table('spotwelding_qty')->where('time_log_id', $request->id)->delete();

    	$existing = DB::connection('mysql_mes')->table('spotwelding_qty')
    		->where('spotwelding_part_id', $spotwelding_qty_det->spotwelding_part_id)->exists();

    	if (!$existing) {
    		DB::connection('mysql_mes')->table('spotwelding_part')
    			->where('spotwelding_part_id', $spotwelding_qty_det->spotwelding_part_id)->delete();
    	}

    	return response()->json(['success' => 1, 'message' => 'Task has been updated.']);
    }

   	public function update_task_reject(Request $request){
   		// return $request->all();
		try {
			$now = Carbon::now();
			$time_log = DB::connection('mysql_mes')->table('spotwelding_qty')->where('time_log_id', $request->id)->first();
			if ($request->per_row_reject == 1) {
				$good_qty_after_transaction = $time_log->good - $request->rejected_qty;
				
	            $update = [
	                'last_modified_at' => $now->toDateTimeString(),
	                'last_modified_by' => Auth::user()->employee_name,
	                'good' => $good_qty_after_transaction,
	                'reject' => $request->rejected_qty,
				];

				DB::connection('mysql_mes')->table('spotwelding_qty')->where('time_log_id', $request->id)->update($update);
				
				$this->update_completed_qty_per_workstation($time_log->job_ticket_id); 
			}else{				
	            $insert = [
					'job_ticket_id' => $request->id,
					'qa_inspection_type' => 'Reject Confirmation',
					'rejected_qty' => $request->rejected_qty,
					'total_qty' => $request->good,
					'status' => 'For Confirmation',
					'created_by' => Auth::user()->employee_name,
					'created_at' => $now->toDateTimeString(),
				];

				DB::connection('mysql_mes')->table('spotwelding_reject')->insert($insert);
				
				$this->update_completed_qty_per_workstation($time_log->job_ticket_id);
			}

			$process_id = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $time_log->job_ticket_id)->first()->process_id;

			$this->updateProdOrderOps($request->production_order, $request->workstation, $process_id);
			$this->update_produced_qty($request->production_order);

			return response()->json(['success' => 1, 'message' => 'Task has been updated.']);
        } catch (Exception $e) {

            return response()->json(["error" => $e->getMessage()]);
        }
	}

	public function update_completed_qty_per_workstation($job_ticket_id){
    	$job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')
    		->join('production_order', 'job_ticket.production_order', 'production_order.production_order')
    		->where('job_ticket_id', $job_ticket_id)
    		->select('job_ticket.production_order', 'production_order.qty_to_manufacture')->first();

    	$logs = DB::connection('mysql_mes')->table('spotwelding_qty')->where('job_ticket_id', $job_ticket_id)->exists();

    	$total_good = DB::connection('mysql_mes')->table('spotwelding_qty')->where('job_ticket_id', $job_ticket_id)->selectRaw('SUM(good) as total_good')->groupBy('spotwelding_part_id')->get();
    	$total_good = collect($total_good)->min('total_good');

    	$bom_parts = $this->get_production_order_bom_parts($job_ticket_details->production_order);
    	$bom_parts = array_column($bom_parts, 'item_code');
    	$parts_done = DB::connection('mysql_mes')->table('spotwelding_part')
    		->where('housing_production_order', $job_ticket_details->production_order)
    		->distinct()->pluck('part_code');

    	$parts_done = $parts_done->toArray();

    	sort($bom_parts); 
		sort($parts_done);

		if ($logs && $total_good >= 0) {
    		DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)
    			->update(['status' => 'In Progress']);
	    }

		if ($bom_parts == $parts_done) {
	    	if ($job_ticket_details->qty_to_manufacture == $total_good) {
	    		DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)
	    			->update(['status' => 'Completed', 'completed_qty' => $total_good]);
	    	}else{
	    		DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)
	    			->update(['completed_qty' => $total_good]);
	    	}
		}
    }

    public function update_produced_qty($production_order){
		$processes = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $production_order)->distinct()->pluck('process_id');
		$process_list = [];
		foreach ($processes as $process) {
			$total_completed = DB::connection('mysql_mes')->table('job_ticket')
				->where('production_order', $production_order)
				->where('process_id', $process)->sum('completed_qty');

			$process_list[] = [
				'process_id' => $process,
				'total_completed' => $total_completed
			];
		}

		$produced_qty = collect(array_column($process_list, 'total_completed'))->min();
		if ($produced_qty > 0) {
			DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->update(['produced_qty' => $produced_qty]);
		}

		$required_qty = DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->sum('qty_to_manufacture');
		if ($produced_qty >= $required_qty) {
			DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->update(['status' => 'Completed']);
		}

		$item_code = DB::connection('mysql_mes')->table('production_order')
			->where('production_order', $production_order)->first()->item_code;

		$this->update_inventory($item_code, $produced_qty, 0);
	}

	public function updateProdOrderOps($prod_order, $workstation, $process_id){
        try {
            $qty_to_manufacture = DB::connection('mysql_mes')->table('production_order')
                ->where('production_order', $prod_order)->sum('qty_to_manufacture');

            $time_logs = DB::connection('mysql_mes')->table('job_ticket')
            	->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
				->where('workstation', $workstation)->where('production_order', $prod_order)
				->where('process_id', $process_id)
                ->where('time_logs.status', 'Completed')
                ->select('time_logs.good', 'time_logs.from_time', 'time_logs.to_time');
            
            // get total completed
            $logs = DB::connection('mysql_mes')->table('job_ticket')
            	->join('spotwelding_qty', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
				->where('workstation', $workstation)->where('production_order', $prod_order)
				->where('process_id', $process_id)
                ->where('spotwelding_qty.status', 'Completed')
                ->select('spotwelding_qty.good', 'spotwelding_qty.from_time', 'spotwelding_qty.to_time')
                ->union($time_logs)->get();

            $completed = collect($logs)->sum('good');
            $actual_start = collect($logs)->min('from_time');
            $actual_end = collect($logs)->max('to_time');

            $status = ($qty_to_manufacture == $completed) ? "Completed" : "Pending";

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
				->where('parent', $prod_order)->where('workstation', $workstation)
				->where('process', $process_id)->update($data);

        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
	}

	public function get_spotwelding_current_operator_task_details(Request $request, $operator_id){
		$job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')
			->where('job_ticket_id', $request->job_ticket_id)->first();
		if(!$job_ticket_details){
			return response()->json(['success' => 0, 'message' => 'Task not found.']);
		}

		$status = $job_ticket_details->status;
		$machine_code = $request->machine_code;

		$time_logs = DB::connection('mysql_mes')->table('spotwelding_qty')->where('job_ticket_id', $request->job_ticket_id)
			->where('operator_id', $operator_id)->first();

		if (!$time_logs) {
			$task_list_qry = DB::connection('mysql_mes')->table('production_order AS po')
				->join('job_ticket AS jt', 'po.production_order', 'jt.production_order')
				->where('po.production_order', $request->production_order)
				->where('jt.workstation', $request->workstation)
				->where('jt.job_ticket_id', $request->job_ticket_id)
				->select('po.item_code', 'jt.job_ticket_id', DB::raw('(SELECT process_name FROM process WHERE process_id = jt.process_id) AS process_name'), 'po.production_order', 'po.description', 'po.sales_order', 'po.material_request', 'po.customer', 'po.qty_to_manufacture', 'po.stock_uom', 'po.project', 'jt.process_id')
				->orderBy('jt.last_modified_at', 'desc')->get();
		}else{
			$task_list_qry = DB::connection('mysql_mes')->table('production_order AS po')
				->join('job_ticket AS jt', 'po.production_order', 'jt.production_order')
				->join('spotwelding_qty', 'spotwelding_qty.job_ticket_id', 'jt.job_ticket_id')
				->where('po.production_order', $request->production_order)
				->where('jt.workstation', $request->workstation)
				->where('jt.job_ticket_id', $request->job_ticket_id)
				->where('spotwelding_qty.operator_id', Auth::user()->user_id)
				->select('po.item_code', 'spotwelding_qty.time_log_id', 'jt.job_ticket_id', 'spotwelding_qty.operator_id', 'spotwelding_qty.machine_code', DB::raw('(SELECT process_name FROM process WHERE process_id = jt.process_id) AS process_name'), 'po.production_order', 'po.description', 'po.sales_order', 'po.material_request', 'spotwelding_qty.status', 'spotwelding_qty.from_time', 'spotwelding_qty.to_time', 'po.customer', 'po.qty_to_manufacture', DB::raw('(SELECT SUM(good) FROM spotwelding_qty WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS total_good'),  DB::raw('(SELECT SUM(reject) FROM spotwelding_qty WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS total_reject'), 'po.stock_uom', 'po.project', 'spotwelding_qty.operator_name', 'jt.process_id', 'spotwelding_qty.good', 'spotwelding_qty.spotwelding_part_id', 'jt.completed_qty')
				->orderByRaw("FIELD(spotwelding_qty.status, 'In Progress', 'Pending', 'Completed') ASC")
				->orderBy('spotwelding_qty.last_modified_at', 'desc')->get();
		}

		$task_list = [];
		foreach ($task_list_qry as $row) {
			if ($time_logs) {
				$reference_type = ($request->workstation == 'Spotwelding') ? 'Spotwelding' : 'Time Logs';
				$reference_id = ($request->workstation == 'Spotwelding') ? $row->job_ticket_id : $row->time_log_id;
				$qa_inspection_status = $this->get_qa_inspection_status($reference_type, $reference_id);
				$helpers = DB::connection('mysql_mes')->table('helper')
					->where('time_log_id', $row->time_log_id)->get();

				$qry = DB::connection('mysql_mes')->table('spotwelding_qty')
					->where('job_ticket_id', $row->job_ticket_id)
					->where('machine_code', $row->machine_code)
					->where('operator_id', $row->operator_id)
					->pluck('time_log_id');
				
				$count_helpers = DB::connection('mysql_mes')->table('helper')->whereIn('time_log_id', $qry)->distinct('operator_id')->count();
			}else{
				$qa_inspection_status = 'Pending';
				$helpers = [];
				$count_helpers = 0;
			}

			$task_list[] = [
				'item_code' => $row->item_code,
				'job_ticket_id' => $row->job_ticket_id,
				'operator_id' => ($time_logs) ? $row->operator_id : null,
				'qa_inspection_status' => $qa_inspection_status,
				'machine_code' => ($time_logs) ? $row->machine_code : null,
				'process_name' => $row->process_name,
				'production_order' => $row->production_order,
				'description' => $row->description,
				'sales_order' => $row->sales_order,
				'material_request' => $row->material_request,
				'status' => ($time_logs) ? $row->status : 'Pending',
				'from_time' => ($time_logs) ? $row->from_time : null,
				'to_time' => ($time_logs) ? $row->to_time : null,
				'customer' => $row->customer,
				'qty_to_manufacture' => $row->qty_to_manufacture,
				'completed_qty' => ($time_logs) ? $row->completed_qty : 0,
				'total_good' => ($time_logs) ? $row->total_good : 0,
				'total_reject' => ($time_logs) ? $row->total_reject : 0,
				'stock_uom' => $row->stock_uom,
				'project' => $row->project,
				'operator_name' => ($time_logs) ? $row->operator_name : null,
				'process_id' => $row->process_id,
				'good' => ($time_logs) ? $row->good : 0,
				'time_log_id' => ($time_logs) ? $row->time_log_id : null,
				'helpers' => $helpers,
				'count_helpers' => $count_helpers,
				'spotwelding_part_id' => ($time_logs) ? $row->spotwelding_part_id : null,
			];
		}

		$batch_list = DB::connection('mysql_mes')->table('job_ticket')
			->join('spotwelding_qty', 'spotwelding_qty.job_ticket_id', 'job_ticket.job_ticket_id')
			->where('production_order', $request->production_order)
			->where('workstation', $request->workstation)
			->where('process_id', $job_ticket_details->process_id)
			->where('operator_id', $operator_id)
			->select('*', DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process_name'))
			->where('spotwelding_qty.status', 'Completed')->get();

		$in_progress_operator = DB::connection('mysql_mes')->table('job_ticket')
			->join('spotwelding_qty', 'spotwelding_qty.job_ticket_id', 'job_ticket.job_ticket_id')
			->where('production_order', $request->production_order)
			->where('workstation', $request->workstation)
			->where('process_id', $job_ticket_details->process_id)
			->where('operator_id', '!=', $operator_id)
			->whereNotNull('operator_id')
			->select('operator_id', 'operator_nickname', DB::raw('SUM(good + reject) as completed_qty'))->groupBy('operator_id', 'operator_nickname')->get();

		$bom_parts = $this->get_production_order_bom_parts($request->production_order);

		$bom_parts = collect($bom_parts)->sortBy('status');

		$timelogs = DB::connection('mysql_mes')->table('spotwelding_qty')
			->join('job_ticket', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
			->where('job_ticket.job_ticket_id', $request->job_ticket_id)
			->select('job_ticket.process_id', 'spotwelding_qty.*', DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process_name'))->get();

		$logs = [];
		foreach ($timelogs as $log) {
			$parts = DB::connection('mysql_mes')->table('spotwelding_part')->where('spotwelding_part_id', $log->spotwelding_part_id)->get();
			$process_description = '';
			foreach ($parts as $part) {
				$process_description .= $part->part_code . ' (' . $part->part_category . ') >>> ';
			}

			$process_description = rtrim($process_description, ' >>> ');

			$from = Carbon::parse($log->from_time);
			$to = Carbon::parse($log->to_time);

			// modified start
			$days = $from->diffInDays($to);
			$hours = $from->copy()->addDays($days)->diffInHours($to);
			$minutes = $from->copy()->addDays($days)->addHours($hours)->diffInMinutes($to);
			$seconds = $from->copy()->addDays($days)->addHours($hours)->addMinutes($minutes)->diffInSeconds($to);
			$dur_days = ($days > 0) ? $days .'d' : null;
			$dur_hours = ($hours > 0) ? $hours .'h' : null;
			$dur_minutes = ($minutes > 0) ? $minutes .'m' : null;
			$dur_seconds = ($seconds > 0) ? $seconds .'s' : null;

			$logs[] = [
				'time_log_id' => $log->time_log_id,
				'process_name' => $log->process_name,
				'good' => $log->good,
				'process_description' => $process_description,
				'from_time' => ($log->from_time) ? Carbon::parse($log->from_time)->format('M-d-Y h:i A') : '--',
				'to_time' => ($log->to_time) ? Carbon::parse($log->to_time)->format('M-d-Y h:i A') : '--',
				'completed_qty' => $log->good,
				'reject' => $log->reject,
				'duration' => $dur_days .' '. $dur_hours . ' '. $dur_minutes .' '. $dur_seconds,
				'status' => $log->status,
				'machine' => $log->machine_code,
				'operator_id' => $log->operator_id,
				'operator_name' => $log->operator_name
			];
			// modified end
		}

		$total_rejects = DB::connection('mysql_mes')->table('spotwelding_reject')
			->where('job_ticket_id', $request->job_ticket_id)->sum('rejected_qty');

    	return view('tables.tbl_spotwelding_current_operator_task', compact('task_list', 'machine_code', 'batch_list', 'in_progress_operator', 'helpers', 'count_helpers', 'bom_parts', 'logs', 'total_rejects'));
	}

	public function view_operator_task($job_ticket, $operator_id){
    	$task_details = DB::connection('mysql_mes')->table('spotwelding_qty')->where('job_ticket_id', $job_ticket)->get();

    	$task_list = [];
    	foreach ($task_details as $row) {
    		$parts = DB::connection('mysql_mes')->table('spotwelding_part')->where('spotwelding_part_id', $row->spotwelding_part_id)->get();
			$process_description = '';
			foreach ($parts as $part) {
				$process_description .= $part->part_code . ' (' . $part->part_category . ') >>> ';
			}

			$process_description = rtrim($process_description, ' >>> ');

    		$task_list[] = [
    			'operator_name' => $row->operator_name,
    			'machine_code' => $row->machine_code,
    			'process_description' => $process_description,
    			'from_time' => Carbon::parse($row->from_time)->format('M-d-Y h:i A'),
    			'to_time' => ($row->to_time) ? Carbon::parse($row->to_time)->format('M-d-Y h:i A') : '-',
    			'completed_qty' => $row->good + $row->reject,
    			'status' => $row->status,
    		];
    	}

    	return $task_list;
    }

	public function get_production_order_bom_parts($production_order){
		$production_order_details = DB::connection('mysql_mes')->table('production_order')
			->where('production_order', $production_order)->first();

		$bom_parts = DB::connection('mysql')->table('tabBOM Item')->where('parent', $production_order_details->bom_no)->get();

		$bom_parts_arr = [];
		foreach($bom_parts as $part){
			$time_log = DB::connection('mysql_mes')->table('job_ticket')
				->join('spotwelding_qty', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
				->where('job_ticket.production_order', $production_order)
				->where('spotwelding_qty.parts', 'LIKE', "%".$part->item_code."%")
				->orderByRaw("FIELD(spotwelding_qty.status, 'In Progress', 'Completed') ASC")
				->first();

			$status = 'Not Started';
			if ($time_log) {
				$status = $time_log->status;
			}

			$available_stock = DB::connection('mysql_mes')->table('fabrication_inventory')
				->where('item_code', $part->item_code)->sum('balance_qty');
				
			$item_name = explode(',', $part->description);

			$part_production_order = DB::connection('mysql_mes')->table('production_order')
				->where('item_code', $part->item_code)
				->where('parent_item_code', $production_order_details->parent_item_code)
				->where('sub_parent_item_code', $production_order_details->item_code)
				->where('sales_order', $production_order_details->sales_order)
				->where('material_request', $production_order_details->material_request)
				->first();

			if($part_production_order){
				$prod_order = $part_production_order->production_order;
				$part_qty = $part_production_order->qty_to_manufacture;
				$part_category = $part_production_order->parts_category;
			}else{
				$prod_order = null;
				$part_qty = $part->qty * $production_order_details->qty_to_manufacture;
				$part_category = null;
			}

			$bom_parts_arr[] = [
				'item_code' => $part->item_code,
				'item_name' => $item_name[0],
				'production_order' => $prod_order,
				'parts_category' => $part_category,
				'qty' => $part_qty,
				'status' => $status,
				'available_stock' => $available_stock
			];
		}

		return $bom_parts_arr;
	}

	public function logout_spotwelding(){
        Auth::guard('web')->logout();
        $route = '/operator/Spotwelding';
        return redirect($route);
    }

    public function get_qa_inspection_status($reference_type, $reference_id){
		$query = DB::connection('mysql_mes')->table('quality_inspection')
			->where('reference_id', $reference_id)->where('reference_type', $reference_type)
			->where('status', '!=', 'For Confirmation')->first();

    	if ($query) {
    		return $query->status;
    	}

    	return 'Pending';
    }

    public function get_spotwelding_part_remaining_qty(Request $request){
    	$completed_qty = DB::connection('mysql_mes')->table('spotwelding_qty')
    		->where('job_ticket_id', $request->job_ticket_id)
    		->where('spotwelding_part_id', $request->spotwelding_part_id)
    		->where('status', 'Completed')->sum('good');

    	return $request->qty_to_manufacture - $completed_qty;
    }

    public function update_production_order_operation($production_order){
    	$production_order_details = DB::connection('mysql')->table('tabProduction Order')->where('name', $production_order)->first();
    	if ($production_order_details) {
    		$values = [
	    		// 'name'
			    // 'creation'
				// 'modified'
				// 'modified_by'
				// 'owner'
				// 'docstatus'
				// 'parent'
				// 'parentfield'
				// 'parenttype'
				// 'idx'
				'status' => 'Completed',
				// 'actual_start_time'
				// 'workstation'
				'completed_qty' => $production_order_details->qty
				// 'planned_operating_cost'
				// 'description'
				// 'actual_end_time'
				// 'actual_operating_cost'
				// 'hour_rate'
				// 'planned_start_time'
				// 'bom'
				// 'actual_operation_time'
				// 'operation'
				// 'planned_end_time'
				// 'time_in_mins'
				// 'process'
	    	];

	    	DB::connection('mysql')->table('tabProduction Order Operation')
	    		->where('parent', $production_order)->update($values);

	    	return $values;

    	}

    	

    
    }
}