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
class PaintingOperatorController extends Controller
{	
	use GeneralTrait;
	// R E V I S E D - 06/18/2020
	public function index($process_name){
		$painting_process = DB::connection('mysql_mes')->table('process_assignment')
			->join('workstation', 'process_assignment.workstation_id', 'workstation.workstation_id')
			->join('process', 'process.process_id', 'process_assignment.process_id')->where('workstation.workstation_name', 'Painting')
			->orderBy('process.process_id', 'asc')->pluck('process.process_name');

		$process_details = DB::connection('mysql_mes')->table('process')->where('process_name', $process_name)->first();

		$machine_status = $this->get_machine_status();
		$now = Carbon::now();
		$time=$now->format('h:i:s');
		$breaktime = [];
		$shift= DB::connection('mysql_mes')->table('shift_schedule')
			->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
			->whereDate('shift_schedule.date', $now)
			->where('shift.operation_id', 2)
			->where('shift.shift_type', 'Special Shift')
			->select('shift.shift_id')->first();
			if(empty($shift)){
				$reg_shift= DB::connection('mysql_mes')
					->table('shift')
					->where('shift.operation_id',  2)
					->where('shift_type', 'Regular Shift')
					->first();
					if($reg_shift){
						$breaktime_tbl= db::connection('mysql_mes')->table('breaktime')->where('shift_id', $reg_shift->shift_id)->get();
						if(!empty($breaktime_tbl)){
							foreach($breaktime_tbl as $r){
								$breaktime[]=[
									"break_type" => $r->category,
									"time_in" => $r->time_from,
									'time_out' =>$r->time_to,
									'div_id'=> str_replace(' ', '', $r->category),
									"time_in_show" => date("h:i a", strtotime($r->time_from)),
									'time_out_show' =>date("h:i a", strtotime($r->time_to))
								];
							}
						}
					}	
			}else{
				$breaktime_tbl= db::connection('mysql_mes')->table('breaktime')->where('shift_id', $shift->shift_id)->get();
				if(!empty($breaktime_tbl)){
					foreach($breaktime_tbl as $r){
						$breaktime[]=[
							"break_type" => $r->category,
							"time_in" => $r->time_from,
							'time_out' =>$r->time_to,
							'div_id'=> str_replace(' ', '', $r->category),
							"time_in_show" => date("h:i a", strtotime($r->time_from)),
							'time_out_show' =>date("h:i a", strtotime($r->time_to))
						];
					}
				}
			}
		$o_shift_shift= DB::connection('mysql_mes')->table('shift_schedule')
			->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
			->whereDate('shift_schedule.date', $now)
			->where('shift.operation_id', 2)
			->where('shift.shift_type', 'Overtime Shift')
			->select('shift.shift_id')->first();
			if($o_shift_shift){
				$breaktime_tbll= db::connection('mysql_mes')->table('breaktime')->where('shift_id', $o_shift_shift->shift_id)->get();
				if($breaktime_tbll){
					foreach($breaktime_tbll as $r){
						$breaktime[]=[
							"break_type" => $r->category,
							"time_in" => $r->time_from,
							'time_out' =>$r->time_to,
							'div_id'=> str_replace(' ', '', $r->category),
							"time_in_show" => date("h:i a", strtotime($r->time_from)),
							'time_out_show' =>date("h:i a", strtotime($r->time_to))
						];
					}
				}
			}
		$breaktime_data= collect($breaktime);
        return view('painting_operator.index', compact('process_details', 'machine_status', 'painting_process', 'breaktime_data'));
	}

	public function get_production_order_details($production_order, $process_id){
		$now = Carbon::now();
		$task_qry = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $production_order)
			->where('process_id', $process_id)->first();
		if (!$task_qry) {
			return response()->json(['success' => 0, 'message' => 'Task not found.']);
		}

		if(!$task_qry->planned_start_date || $task_qry->planned_start_date > $now->format('Y-m-d H:i:s')){
			return response()->json(['success' => 2, 'message' => 'Task not scheduled for today.']);
		}

		if ($task_qry->status == 'Completed') {
			return response()->json(['success' => 0, 'message' => 'Task already completed.']);
		}

		$production_order_details = DB::connection('mysql_mes')->table('production_order')
			->where('production_order', $production_order)->first();

		$production_orders = DB::connection('mysql_mes')->table('production_order')
			->join('job_ticket', 'job_ticket.production_order', 'production_order.production_order')
			->where('production_order.production_order', $production_order)
			->where('workstation', 'Painting')->where('process_id', $process_id)
			->selectRaw('sales_order, material_request, qty_to_manufacture, job_ticket.completed_qty, production_order.production_order, customer, item_code, description')
			->first();

		if (!$production_order_details) {
            return response()->json(['success' => 0, 'message' => 'Production Order not found.']);
        } else {        
			return response()->json(['success' => 1, 'message' => "Production Order found.", 'details' => $production_orders]);
		}
	}

	public function login_operator(Request $request){
		$user = DB::connection('mysql_essex')->table('users')->where('user_id', $request->operator_id)->first();

        if (!$user) {
            return response()->json(['success' => 0, 'message' => '<b>Invalid credentials!</b> Please try again.']);
		}

		$painting_machine = DB::connection('mysql_mes')->table('workstation_machine')->where('workstation', 'Painting')->first();
		if (!$painting_machine) {
			return response()->json(['success' => 0, 'message' => 'Machine for Painting not found.']);
		}
		
		$url = '/operator/Painting/' . $request->process_name . '/' . $painting_machine->machine_code . '/' . $request->production_order;

		// attempt to do the login
        if ($user) {
            if(Auth::loginUsingId($user->id)){
                return response()->json(['success' => 1, 'message' => "<b>Welcome!</b> Please wait...", 'url' => $url]);
            } 
        } else {        
            // validation not successful, send back to form 
            return response()->json(['success' => 0, 'message' => '<b>Invalid credentials!</b> Please try again.']);
        }
	}

	public function operator_task($process_name, $machine_code, $production_order){
		$machine_details = DB::connection('mysql_mes')->table('machine')->where('machine_code', $machine_code)->first();

		$process_details = DB::connection('mysql_mes')->table('process')->where('process_name', $process_name)->first();

		$machine_status = $this->get_machine_status();

		return view('painting_operator.task', compact('machine_details', 'process_details', 'machine_status', 'production_order'));
	}

	public function reject_task(Request $request){
		try {
			if(empty($request->reject_list)){
				return response()->json(['success' => 0, 'message' => 'Alert: Please select reject type']);
			}
			$now = Carbon::now();
			$data= $request->all();
			$reject_reason= $data['reject_list'];
			$time_log = DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $request->id)->first();
			$good_qty_after_transaction = $time_log->good - $request->rejected_qty;
			
            $update = [
                'last_modified_at' => $now->toDateTimeString(),
                'last_modified_by' => Auth::user()->employee_name,
                'good' => $good_qty_after_transaction,
                'reject' => $request->rejected_qty,
			];

			$reference_type = 'Time Logs';
			$reference_id =  $request->id;

			$insert = [
				'reference_type' => $reference_type,
				'reference_id' => $reference_id,
				'qa_inspection_type' => 'Reject Confirmation',
				'rejected_qty' => $request->rejected_qty,
				'total_qty' => $time_log->good,
				'status' => 'For Confirmation',
				'created_by' => Auth::user()->employee_name,
				'created_at' => $now->toDateTimeString(),
			];

			$qa_id = DB::connection('mysql_mes')->table('quality_inspection')->insertGetId($insert);

			foreach($reject_reason as $i => $row){
				$reason[] = [
					'job_ticket_id' => $time_log->job_ticket_id,
					'qa_id' => $qa_id,
					'reject_list_id' => $row,
					'reject_value' => '-'
				];
			}
			

			DB::connection('mysql_mes')->table('reject_reason')->insert($reason);
			DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $request->id)->update($update);
			
			$this->updateProdOrderOps($request->production_order, 'Painting');
			$this->update_completed_qty_per_workstation($time_log->job_ticket_id);
			$this->update_produced_qty($request->production_order);
			$this->update_job_ticket_good($time_log->job_ticket_id);
			$this->update_job_ticket_reject($time_log->job_ticket_id);
            return response()->json(['success' => 1, 'message' => 'Task has been updated.']);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
	}

	public function get_task($production_order, $process_id, $operator_id){
		$process_details = DB::connection('mysql_mes')->table('process')->where('process_id', $process_id)->first();
		$jt_details = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $production_order)->where('process_id',$process_id)->select('job_ticket_id')->first();
		$production_order_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->first();

		$machine_status = $this->get_machine_status();
		
		$status = 'In Progress';
		$in_progress_task = DB::connection('mysql_mes')->table('job_ticket')
			->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
			->where('job_ticket.workstation', 'Painting')
			->where('job_ticket.production_order', $production_order)
			->where('job_ticket.process_id', $process_id)->where('time_logs.status', 'In Progress')->first();

		if (!$in_progress_task) {
			$status = 'Not Started';
			$not_started_task = DB::connection('mysql_mes')->table('job_ticket')
				->where('job_ticket.workstation', 'Painting')
				->where('job_ticket.production_order', $production_order)
				->where('job_ticket.process_id', $process_id)->first();
		}

		$id = (!$in_progress_task) ? $not_started_task->job_ticket_id : $in_progress_task->time_log_id;
		$production_task = (!$in_progress_task) ? $not_started_task : $in_progress_task;
		$pending_qty = $production_order_details->qty_to_manufacture - $production_task->completed_qty;

		if ($pending_qty <= 0) {
			$status = 'Completed';
		}

		$completed_task = DB::connection('mysql_mes')->table('job_ticket')
			->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
			->where('job_ticket.workstation', 'Painting')
			->where('job_ticket.production_order', $production_order)
			->where('job_ticket.process_id', $process_id)
			->where('time_logs.status', 'Completed')
			->first();

		$task_details = [
			'status' => $status,
			'id' => $id,
			'completed_task_id' => ($completed_task) ? $completed_task->time_log_id : null,
			'good' => ($completed_task) ? $completed_task->good : 0,
		];

		if($process_details->process_name == 'Unloading'){
			$loading_process_detail = DB::connection('mysql_mes')->table('process')->where('process_name', 'Loading')->first();
			$qty_loaded = DB::connection('mysql_mes')->table('job_ticket')
				->where('workstation', 'Painting')->where('production_order', $production_order)
				->where('process_id', $loading_process_detail->process_id)->sum('completed_qty');

			$pending_qty = $qty_loaded - $production_task->completed_qty;
		}

		$batch_list = DB::connection('mysql_mes')->table('job_ticket')
			->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
			->where('time_logs.job_ticket_id', $jt_details->job_ticket_id)
			->where('operator_id', $operator_id)
			->select('*', DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process_name'))
			->where('time_logs.status', 'Completed')->get();
		$qty_arr = [
			'required_qty' => $production_order_details->qty_to_manufacture,
			'completed_qty' => $production_task->completed_qty,
			'pending_qty' => $pending_qty
		];

		return view('painting_operator.tbl_task', compact('production_order_details', 'process_details', 'task_details', 'machine_status', 'qty_arr', 'batch_list'));
	}

	public function start_task(Request $request){
		$now = Carbon::now();
	    $operator = DB::connection('mysql_essex')->table('users')->where('user_id', Auth::user()->user_id)->first();
    	if (!$operator) {
    		return response()->json(['success' => 0, 'message' => 'Operator not found.']);
    	}

    	$in_progress_time_log = DB::connection('mysql_mes')->table('time_logs')
    		->where('job_ticket_id', $request->job_ticket_id)
    		->where('operator_id', Auth::user()->user_id)
    		->where('status', 'In Progress')->first();

    	if (!$in_progress_time_log) {
    		$machine_name = DB::connection('mysql_mes')->table('machine')
    		->where('machine_code', $request->machine_code)->first()->machine_name;

			$values = [
				'job_ticket_id' => $request->job_ticket_id,
				'from_time' => $now->toDateTimeString(),
				'machine_code' => $request->machine_code,
				'machine_name' => $machine_name,
				'operator_id' => $operator->user_id,
				'operator_name' => $operator->employee_name,
				'operator_nickname' => $operator->nick_name,
				'status' => 'In Progress',
				'created_by' => $operator->employee_name,
				'created_at' => $now->toDateTimeString(),
			];

			$production_order = DB::connection('mysql_mes')->table('production_order')
				->where('production_order', $request->production_order)->first();
			if ($production_order && $production_order->status == 'Not Started') {
				DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)->update(['status' => 'In Progress']);
			}

			DB::connection('mysql_mes')->table('time_logs')->insert($values);

			$this->update_completed_qty_per_workstation($request->job_ticket_id);
			$this->update_jobticket_actual_start_end($request->job_ticket_id);
			$this->update_job_ticket_good($request->job_ticket_id);
			$this->update_job_ticket_reject($request->job_ticket_id);
    	}

		return response()->json(['success' => 1, 'message' => 'Task updated.']);
	}

	public function end_task(Request $request){
        try {
			$now = Carbon::now();
			$current_task = DB::connection('mysql_mes')->table('time_logs')
				->where('time_log_id', $request->id)->first();

			$seconds = $now->diffInSeconds(Carbon::parse($current_task->from_time));
			$duration= $seconds / 3600;

			$good_qty = $request->completed_qty - $current_task->reject;
			
			$update = [
				'last_modified_at' => $now->toDateTimeString(),
				'last_modified_by' => Auth::user()->employee_name,
				'to_time' => $now->toDateTimeString(),
				'good' => $good_qty,
				'status' => 'Completed',
				'duration' => $duration,
			];
			
			DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $request->id)->update($update);
			
			$this->updateProdOrderOps($request->production_order, $request->workstation);
			$this->update_produced_qty($request->production_order);

			// get completed qty in painting workstation
			$painting_completed_qty = DB::connection('mysql_mes')->table('job_ticket')
				->where('production_order', $request->production_order)
				->where('workstation', 'Painting')->min('completed_qty');

			// get production order qty_to_manufacture
			$qty_to_manufacture = DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)->sum('qty_to_manufacture');

			$this->updateProdOrderOps($request->production_order, $request->workstation);
			$this->update_completed_qty_per_workstation($current_task->job_ticket_id);
			$this->update_job_ticket_good($current_task->job_ticket_id);
			$this->update_job_ticket_reject($current_task->job_ticket_id);
			$this->update_produced_qty($request->production_order);
			$this->update_jobticket_actual_start_end($current_task->job_ticket_id);
			if($qty_to_manufacture == $painting_completed_qty){
				// update spotwelding status and completed qty
				$values = [
					'completed_qty' => $painting_completed_qty,
					'status' => 'Completed',
					'remarks' => 'Override'
				];

				DB::connection('mysql_mes')->table('job_ticket')
					->where('production_order', $request->production_order)
					->where('workstation', 'Spotwelding')
					->whereIn('status', ['In Progress', 'Pending'])
					->update($values);

				$this->updateProdOrderOps($request->production_order, $request->workstation);
				$this->update_produced_qty($request->production_order);
			}
			
			return response()->json(['success' => 1, 'message' => 'Task has been updated.']);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
	}

	public function get_machine_status(){
		$start = Carbon::now()->startOfDay()->toDateTimeString();
		$end = Carbon::now()->endOfDay()->toDateTimeString();

		$machine_log = DB::connection('mysql_mes')->table('painting_operation_logs')
			->whereBetween('operation_date', [$start, $end])->orderBy('created_at', 'desc')->first();

		if ($machine_log) {
			if ($machine_log->category == 'Start Up') {
				return 'Shutdown';
			}
		}

		return 'Start Up';
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

		$this->update_inventory($item_code, $produced_qty, 0);
	}

	public function update_inventory($item_code, $qty, $is_withdrawn){
        $existing_inv = DB::connection('mysql_mes')->table('fabrication_inventory')
            ->where('item_code', $item_code)->first();
		if($qty > 0){
			if ($existing_inv) {
				$current_balance_qty = DB::connection('mysql_mes')->table('fabrication_inventory')
					->where('item_code', $item_code)->sum('balance_qty');
	
				if ($is_withdrawn) {
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
				$insert = [
					'item_code' => $item_code,
					'balance_qty' => $qty,
					'created_by' => Auth::user()->employee_name
				];
	
				DB::connection('mysql_mes')->table('fabrication_inventory')->insert($insert);
			}
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
		}else{
			DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)
    			->update(['status' => 'Pending']);
		}
		
    	if ($job_ticket_details->qty_to_manufacture == $total_good) {
    		DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)
    			->update(['status' => 'Completed', 'completed_qty' => $total_good]);
    	}else{
    		DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)
    			->update(['completed_qty' => $total_good]);
    	}
	}

	public function insert_machine_logs(Request $request){
    	$now = Carbon::now();
    	$operator = DB::connection('mysql_essex')->table('users')->where('user_id', $request->operator_id)->first();
    	if ($operator) {
    		$data = [
	    		'operation_date' => $now->toDateTimeString(),
	    		'operator_id' => $request->operator_id,
	    		'category' => $request->category,
	    		'created_by' => $operator->employee_name,
	    		'created_at' => $now->toDateTimeString()
	    	];

	    	DB::connection('mysql_mes')->table('painting_operation_logs')->insert($data);

	    	return response()->json(['success' => 1, 'message' => 'Log created.']);
    	}else{
    		return response()->json(['success' => 1, 'message' => 'Operator not found.']);
    	}
    }
	
	public function logout($process_name){
        Auth::guard('web')->logout();
        $route = '/operator/Painting/' . $process_name;
        return redirect($route);
	}

	public function get_scheduled_for_painting(){
		$start = Carbon::now()->startOfDay()->toDateTimeString();
		$end = Carbon::now()->endOfDay()->toDateTimeString();

		$scheduled_painting_production_orders = DB::connection('mysql_mes')->table('job_ticket')
			->where('workstation', 'Painting')->whereBetween('job_ticket.planned_start_date', [$start, $end])
			// ->where('status', 'Pending')
			->distinct()->pluck('production_order');

		$scheduled = DB::connection('mysql_mes')->table('production_order')
			->whereIn('production_order', $scheduled_painting_production_orders)
			->where('status', '!=', 'Cancelled')->select('production_order.*')->get();

		$scheduled_arr = [];
		foreach ($scheduled as $i => $row) {
			$reference_no = ($row->sales_order) ? $row->sales_order : $row->material_request;

			$process = DB::connection('mysql_mes')->table('job_ticket')
				->join('process', 'job_ticket.process_id', 'process.process_id')
				->where('job_ticket.production_order', $row->production_order)
				->where('job_ticket.workstation', 'Painting')
				->select('job_ticket.status', 'job_ticket.completed_qty', 'job_ticket.sequence')
				->get();

			$completed_qty = collect($process)->min('completed_qty');

			$sequence = collect($process)->min('sequence');
			
			$scheduled_arr[] = [
				'production_order' => $row->production_order,
				'reference_no' => $reference_no,
				'customer' => $row->customer,
				'item_code' => $row->item_code,
				'description' => $row->description,
				'required_qty' => $row->qty_to_manufacture,
				'processes' => $process,
				'sequence' => $sequence,
				'completed_qty' => $completed_qty,
				'balance_qty' => $row->qty_to_manufacture - $completed_qty
			];
		}

		$scheduled_arr = collect($scheduled_arr)->sortBy('sequence')->toArray();

		return view('painting_operator.tbl_scheduled_task', compact('scheduled_arr'));
	}

	public function restart_task(Request $request){
		$qry = DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $request->id)->first();

		DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $request->id)->delete();
		
		if($qry){
			$this->update_completed_qty_per_workstation($qry->job_ticket_id);
		}
 
    	return response()->json(['success' => 1, 'message' => 'Task has been updated.']);
    }

    public function create_stock_entry(Request $request, $production_order){
    	$production_order_details = DB::connection('mysql')->table('tabProduction Order')
    		->where('name', $production_order)->first();

    	$mes_production_order_details = DB::connection('mysql_mes')->table('production_order')
    		->where('production_order', $production_order)->first();

    	$now = Carbon::now();

    	$latest_pro = DB::connection('mysql')->table('tabStock Entry')->max('name');
        $latest_pro_exploded = explode("-", $latest_pro);
        $new_id = $latest_pro_exploded[1] + 1;
        $new_id = str_pad($new_id, 5, '0', STR_PAD_LEFT);
        $new_id = 'STEM-'.$new_id;

    	$production_order_items = DB::connection('mysql')->table('tabProduction Order Item')
    		->where('parent', $production_order)->get();

    	$stock_entry_detail = [];
    	foreach ($production_order_items as $index => $row) {
    		$bom_material = DB::connection('mysql')->table('tabBOM Item')
    			->where('parent', $production_order_details->bom_no)
    			->where('item_code', $row->item_code)->first();

    		$qty = $row->required_qty / $request->fg_completed_qty;

    		$stock_entry_detail[] = [
	    		'name' =>  uniqid(),
		    	'creation' => $now->toDateTimeString(),
				'modified' => $now->toDateTimeString(),
				'modified_by' => Auth::user()->email,
				'owner' => Auth::user()->email,
				'docstatus' => 1,
				'parent' => $new_id,
				'parentfield' => 'items',
				'parenttype' => 'Stock Entry',
				'idx' => $index + 1,
				't_warehouse' => null,
				'transfer_qty' => $qty,
				'serial_no' => null,
				'expense_account' => 'Cost of Goods Sold - FI',
				'cost_center' => 'Main - FI',
				'actual_qty' => 0,
				's_warehouse' => $production_order_details->wip_warehouse,
				'item_name' => $row->item_name,
				'image' => null,
				'additional_cost' => 0,
				'stock_uom' => $row->stock_uom,
				'basic_amount' => $bom_material->base_rate * $qty,
				'sample_quantity' => 0,
				'uom' => $row->stock_uom,
				'basic_rate' => $bom_material->base_rate,
				'description' => $row->description,
				'barcode' => null,
				'conversion_factor' => $bom_material->conversion_factor,
				'item_code' => $row->item_code,
				'retain_sample' => 0,
				'qty' => $qty,
				'bom_no' => $bom_material->bom_no,
				'allow_zero_valuation_rate' => 0,
				'material_request_item' => null,
				'amount' => $bom_material->base_rate * $qty,
				'batch_no' => null,
				'valuation_rate' => $bom_material->base_rate,
				'material_request' => null,
				't_warehouse_personnel' => null,
				's_warehouse_personnel' => null,
				'target_warehouse_location' => null,
				'source_warehouse_location' => null,
	    	];
		}
		
		$rm_amount = collect($stock_entry_detail)->sum('basic_amount');
		$rate = $rm_amount / $request->fg_completed_qty;

    	$stock_entry_detail[] = [
			'name' =>  uniqid(),
			'creation' => $now->toDateTimeString(),
			'modified' => $now->toDateTimeString(),
			'modified_by' => Auth::user()->email,
			'owner' => Auth::user()->email,
			'docstatus' => 1,
			'parent' => $new_id,
			'parentfield' => 'items',
			'parenttype' => 'Stock Entry',
			'idx' => count($stock_entry_detail) + 1,
			't_warehouse' => 'P2 - Housing Temporary - FI',
			'transfer_qty' => $request->fg_completed_qty,
			'serial_no' => null,
			'expense_account' => 'Cost of Goods Sold - FI',
			'cost_center' => 'Main - FI',
			'actual_qty' => 0,
			's_warehouse' => null,
			'item_name' => $production_order_details->item_name,
			'image' => null,
			'additional_cost' => 0,
			'stock_uom' => $production_order_details->stock_uom,
			'basic_amount' => $rm_amount,
			'sample_quantity' => 0,
			'uom' => $production_order_details->stock_uom,
			'basic_rate' => $rate,
			'description' => $production_order_details->description,
			'barcode' => null,
			'conversion_factor' => 1,
			'item_code' => $production_order_details->production_item,
			'retain_sample' => 0,
			'qty' => $request->fg_completed_qty,
			'bom_no' => $bom_material->bom_no,
			'allow_zero_valuation_rate' => 0,
			'material_request_item' => null,
			'amount' => $rm_amount,
			'batch_no' => null,
			'valuation_rate' => $rate,
			'material_request' => null,
			't_warehouse_personnel' => null,
			's_warehouse_personnel' => null,
			'target_warehouse_location' => null,
			'source_warehouse_location' => null,
		];

    	DB::connection('mysql')->table('tabStock Entry Detail')->insert($stock_entry_detail);

    	$stock_entry_data = [
    		'name' => $new_id,
    	 	'creation' => $now->toDateTimeString(),
    	 	'modified' => $now->toDateTimeString(),
    	 	'modified_by' => Auth::user()->email,
    	 	'owner' => Auth::user()->email,
    	 	'docstatus' => 1,
    	 	'parent' => null,
    	 	'parentfield' => null,
    	 	'parenttype' => null,
    	 	'idx' => 0,
    	 	'use_multi_level_bom' => 1,
    	 	'delivery_note_no' => null,
    	 	'naming_series' => 'STE-',
    	 	'fg_completed_qty' => $request->fg_completed_qty,
    	 	'letter_head' => null,
    	 	'_liked_by' => null,
    	 	'purchase_receipt_no' => null,
    	 	'posting_time' => $now->format('H:i:s'),
    	 	'customer_name' => null,
    	 	'to_warehouse' => $production_order_details->fg_warehouse,
    	 	'title' => 'Manufacture',
    	 	'_comments' => null,
    	 	'from_warehouse' => $production_order_details->wip_warehouse,
    	 	'set_posting_time' => 0,
    	 	'purchase_order' => null,
    	 	'from_bom' => 1,
    	 	'supplier_address' => null,
    	 	'supplier' => null,
    	 	'source_address_display' => null,
    	 	'address_display' => null,
    	 	'source_warehouse_address' => null,
    	 	'value_difference' => 0,
    	 	'credit_note' => null,
    	 	'sales_invoice_no' => null,
    	 	'company' => 'FUMACO Inc.',
    	 	'target_warehouse_address' => null,
    	 	'customer_address' => null,
    	 	'total_outgoing_value' => collect($stock_entry_detail)->sum('basic_amount'),
    	 	'supplier_name' => null,
    	 	'remarks' => null,
    	 	'_user_tags' => null,
    	 	'total_additional_costs' => 0,
    	 	'customer' => null,
    	 	'bom_no' => $production_order_details->bom_no,
    	 	'amended_from' => null,
    	 	'total_amount' => collect($stock_entry_detail)->sum('basic_amount'),
    	 	'total_incoming_value' => collect($stock_entry_detail)->sum('basic_amount'),
    	 	'project' => $production_order_details->project,
    	 	'_assign' => null,
    	 	'select_print_heading' => null,
    	 	'posting_date' => $now->format('Y-m-d'),
    	 	'target_address_display' => null,
    	 	'production_order' => $production_order,
    	 	'purpose' => 'Manufacture',
    	 	'shipping_address_contact_person' => null,
    	 	'customer_1' => null,
			'material_request' => $production_order_details->material_request,
			'reference_no' => null,
			'delivery_date' => null,
			'delivery_address' => null,
			'city' => null,
			'address_line_2' => null,
			'address_line_1' => null,
			'item_status' => 'Issued',
			'sales_order_no' => $mes_production_order_details->sales_order,
			'transfer_as' => 'Internal Transfer',
			'workflow_state' => null,
			'item_classification' => $production_order_details->item_classification,
			'bom_repack' => null,
			'qty_repack' => 0,
			'issue_as' => null,
			'receive_as' => null,
			'so_customer_name' => $mes_production_order_details->customer,
			'order_type' => $mes_production_order_details->classification,
    	];

		DB::connection('mysql')->table('tabStock Entry')->insert($stock_entry_data);
		
		$produced_qty = $production_order_details->produced_qty + $request->fg_completed_qty;
		
		$production_data = [
			'modified' => $now->toDateTimeString(),
    	 	'modified_by' => Auth::user()->email,
			'produced_qty' => $produced_qty,
			'status' => ($produced_qty == $production_order_details->qty) ? 'Completed' : $production_order_details->status
		];

		DB::connection('mysql')->table('tabProduction Order')->where('name', $production_order)->update($production_data);

		$production_data_mes = [
			'last_modified_at' => $now->toDateTimeString(),
    	 	'last_modified_by' => Auth::user()->email,
			'feedback_qty' => $production_order_details->produced_qty + $request->fg_completed_qty,
		];

		DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->update($production_data_mes);

		$this->update_bin($new_id);
		$this->create_stock_ledger_entry($new_id);
		$this->create_gl_entry($new_id);

    	return response()->json(['message' => 'Stock Entry has been created.']);
    }

    public function create_stock_ledger_entry($stock_entry){
    	$now = Carbon::now();
    	$latest_pro = DB::connection('mysql')->table('tabStock Ledger Entry')->max('name');
        $latest_pro_exploded = explode("/", $latest_pro);
        $new_id = $latest_pro_exploded[1] + 1;

        $stock_entry_qry = DB::connection('mysql')->table('tabStock Entry')
        	->where('name', $stock_entry)->first();

        $stock_entry_detail = DB::connection('mysql')->table('tabStock Entry Detail')
        	->where('parent', $stock_entry)->get();

        $stock_ledger_entry = [];
        foreach ($stock_entry_detail as $row) {
        	$warehouse = ($row->s_warehouse) ? $row->s_warehouse : $row->t_warehouse;

        	$bin_qry = DB::connection('mysql')->table('tabBin')->where('warehouse', $warehouse)
				->where('item_code', $row->item_code)->first();
			
        	$new_id = $new_id + 1;
        	$new_id = str_pad($new_id, 8, '0', STR_PAD_LEFT);
        	$id = 'SLEM/'.$new_id;

        	$stock_ledger_entry[] = [
	    		'name' => $id,
			    'creation' => $now->toDateTimeString(),
				'modified' => $now->toDateTimeString(),
				'modified_by' => Auth::user()->email,
				'owner' => Auth::user()->email,
				'docstatus' => 1,
				'parent' => null,
				'parentfield' => null,
				'parenttype' => null,
				'idx' => 0,
				'serial_no' => $row->serial_no,
				'fiscal_year' => $now->format('Y'),
				'voucher_type' => 'Stock Entry',
				'posting_time' => $now->format('H:i:s'),
				'actual_qty' => ($row->s_warehouse) ? ($row->qty * -1) : $row->qty,
				'stock_value' => $bin_qry->actual_qty * $bin_qry->valuation_rate,
				'_comments' => null,
				'incoming_rate' => ($row->t_warehouse) ? ($row->basic_rate) : 0,
				'voucher_detail_no' => $row->name,
				'stock_uom' => $row->stock_uom,
				'warehouse' => $warehouse,
				'_liked_by' => null,
				'company' => 'FUMACO Inc.',
				'_assign' => null,
				'item_code' => $row->item_code,
				// 'stock_queue' => ,
				'valuation_rate' => $bin_qry->valuation_rate,
				'project' => $stock_entry_qry->project,
				'voucher_no' => $row->parent,
				'outgoing_rate' => 0,
				'is_cancelled' => 'No',
				'qty_after_transaction' => $bin_qry->actual_qty,
				'_user_tags' => null,
				'batch_no' => $row->batch_no,
				'stock_value_difference' => ($row->s_warehouse) ? ($row->qty * $row->valuation_rate) * -1  : $row->qty * $row->valuation_rate,
				'posting_date' => $now->format('Y-m-d'),
	    	];
        }

        DB::connection('mysql')->table('tabStock Ledger Entry')->insert($stock_ledger_entry);
    }

    public function update_bin($stock_entry){
		$now = Carbon::now();

		$stock_entry_detail = DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $stock_entry)->get();
		foreach($stock_entry_detail as $row){
			$warehouse = ($row->s_warehouse) ? $row->s_warehouse : $row->t_warehouse;
			$bin_qry = DB::connection('mysql')->table('tabBin')->where('warehouse', $warehouse)
				->where('item_code', $row->item_code)->first();

			if(!$bin_qry){
				$latest_name = DB::connection('mysql')->table('tabBin')->max('name');
				$latest_name_exploded = explode("/", $latest_name);
				$new_id = $latest_name_exploded[1] + 1;
				$new_id = str_pad($new_id, 7, '0', STR_PAD_LEFT);
				$new_id = 'BINM/'.$new_id;

				$bin = [
					'name' => $new_id,
					'creation' => $now->toDateTimeString(),
					'modified' => $now->toDateTimeString(),
					'modified_by' => Auth::user()->email,
					'owner' => Auth::user()->email,
					'docstatus' => 0,
					'parent' => null,
					'parentfield' => null,
					'parenttype' => null,
					'idx' => 0,
					'reserved_qty_for_production' => 0,
					'_liked_by' => null,
					'fcfs_rate' => 0,
					'reserved_qty' => 0,
					'_assign' => null,
					'planned_qty' => 0,
					'item_code' => $row->item_code,
					'actual_qty' => $row->transfer_qty,
					'projected_qty' => $row->transfer_qty,
					'ma_rate' => 0,
					'stock_uom' => $row->stock_uom,
					'_comments' => null,
					'ordered_qty' => 0,
					'reserved_qty_for_sub_contract' => 0,
					'indented_qty' => 0,
					'warehouse' => $warehouse,
					'stock_value' => $row->valuation_rate * $row->transfer_qty,
					'_user_tags' => null,
					'valuation_rate' => $row->valuation_rate,
				];

				DB::connection('mysql')->table('tabBin')->insert($bin);
			}else{
				$bin = [
					// 'name' => $new_id,
					// 'creation' => $now->toDateTimeString(),
					'modified' => $now->toDateTimeString(),
					'modified_by' => Auth::user()->email,
					// 'owner' => Auth::user()->email,
					// 'docstatus' => 0,
					// 'parent' => null,
					// 'parentfield' => null,
					// 'parenttype' => null,
					// 'idx' => 0,
					// 'reserved_qty_for_production' => 0,
					// '_liked_by' => null,
					// 'fcfs_rate' => 0,
					// 'reserved_qty' => 0,
					// '_assign' => null,
					// 'planned_qty' => 0,
					// 'item_code' => $row->item_code,
					'actual_qty' => ($row->s_warehouse) ? $bin_qry->actual_qty - abs($row->transfer_qty) : $bin_qry->actual_qty + $row->transfer_qty,
					// 'projected_qty' => ($row->s_warehouse) ? $bin_qry->projected_qty - abs($row->transfer_qty) : $bin_qry->projected_qty + $row->transfer_qty,
					// 'ma_rate' => 0,
					// 'stock_uom' => $row->stock_uom,
					// '_comments' => null,
					// 'ordered_qty' => 0,
					// 'reserved_qty_for_sub_contract' => 0,
					// 'indented_qty' => 0,
					// 'warehouse' => $warehouse,
					'stock_value' => $bin_qry->valuation_rate * $row->transfer_qty,
					// '_user_tags' => null,
					'valuation_rate' => $bin_qry->valuation_rate,
				];

				DB::connection('mysql')->table('tabBin')->where('name', $bin_qry->name)->update($bin);
			}
		}
	}
	
	public function create_gl_entry($stock_entry){
		$now = Carbon::now();
		$stock_entry_qry = DB::connection('mysql')->table('tabStock Entry')->where('name', $stock_entry)->first();
		$stock_entry_detail = DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $stock_entry)->get();

		foreach ($stock_entry_detail as $row) {
			$latest_name = DB::connection('mysql')->table('tabGL Entry')->max('name');
			$latest_name_exploded = explode("L", $latest_name);
			$new_id = $latest_name_exploded[1] + 1;
			$new_id = str_pad($new_id, 7, '0', STR_PAD_LEFT);
			$new_id = 'MGL'.$new_id;

			if($row->s_warehouse){
				$credit = $row->basic_amount;
				$debit = 0;
				$account = $row->s_warehouse;
			}else{
				$credit = 0;
				$debit = $row->basic_amount;
				$account = $row->t_warehouse;
			}

			$gl_entry = [
				'name' => $new_id,
				'creation' => $now->toDateTimeString(),
				'modified' => $now->toDateTimeString(),
				'modified_by' => Auth::user()->email,
				'owner' => Auth::user()->email,
				'docstatus' => 1,
				'parent' => null,
				'parentfield' => null,
				'parenttype' => null,
				'idx' => 0,
				'fiscal_year' => $now->format('Y'),
				'voucher_no' => $row->parent,
				'cost_center' => $row->cost_center,
				'credit' => $credit,
				'party_type' => null,
				'transaction_date' => null,
				'debit' => $debit,
				'party' => null,
				'_liked_by' => null,
				'company' => 'FUMACO Inc.',
				'_assign' => null,
				'voucher_type' => 'Stock Entry',
				'_comments' => null,
				'is_advance' => 'No',
				'remarks' => 'Accounting Entry for Stock',
				'account_currency' => 'PHP',
				'debit_in_account_currency' => $debit,
				'_user_tags' => null,
				'account' => $account,
				'against_voucher_type' => null,
				'against' => $row->expense_account,
				'project' => $stock_entry_qry->project,
				'against_voucher' => null,
				'is_opening' => 'No',
				'posting_date' => $stock_entry_qry->posting_date,
				'credit_in_account_currency' => $credit,
				'total_allocated_amount' => 0,
				'reference_no' => null,
				'mode_of_payment' => null,
				'order_type' => null,
				'po_no' => null,
				'reference_date' => null,
				'cr_ref_no' => null,
				'or_ref_no' => null,
				'dr_ref_no' => null,
				'pr_ref_no' => null,
			];

			DB::connection('mysql')->table('tabGL Entry')->insert($gl_entry);
		}		
	}
	public function get_powder_modal_details(){
        $now = Carbon::now();
        $get_date_today = $now->format('Y-m-d');
        // $previous_date= date('Y-m-d',strtotime("-1 days"));

        $operating_hrs= DB::connection('mysql_mes')
            ->table('painting_operation_logs')
            ->whereDate('operation_date', $get_date_today)
            ->get();
        $previous= DB::connection('mysql_mes')
            ->table('water_discharged_monitoring')
            ->orderBy('created_by', 'desc')
            ->first();

        $min=collect($operating_hrs)->min('operation_date');
        $max=collect($operating_hrs)->max('operation_date');
        // dd($min);
        $start = Carbon::parse($min);
        $end = Carbon::parse($max);
        $totalDuration = $end->diffInHours($start);
        $op_hrs= $totalDuration;
		// $previous_wd = ($previous->previous == null)? 0: $previous->previous;
		$shift=DB::connection('mysql_mes')
		->table('shift')
		->join('operation as op','op.operation_id','shift.operation_id')
		->where('op.operation_name','Painting')
		->select('shift.*')
		->get();
		// dd($shift);

        $date_today=  $now->format('l,  F d Y');
		$item_list = DB::table('tabItem as item')
		->whereIn('item.item_group', ['Raw Material'])
		->where('item.item_classification','like', '%'."PA - Paints".'%')
		->where('item.item_name','like', '%'.'powder'.'%')
        ->select('item.name', 'item.item_name')
		->orderBy('item.modified', 'desc')->get();
		// dd($item_list);
        return view('painting_operator.tbl_powder_record_tab', compact('shift','item_list', 'date_today', 'op_hrs'));
                        
	}
	public function get_pwder_coat_desc($item){
        
		$item_list = DB::table('tabItem as item')
		->whereIn('item.item_group', ['Raw Material'])
		->where('item.name', $item)
        ->select('item.name', 'item.item_name')
		->first();

		return response()->json(['item' => $item_list->name,'item_desc' => $item_list->item_name]);

                        
	}
	public function submit_powder_record_monitoring(Request $request){
        $now = Carbon::now();
		$data = $request->all();
		$item_code = $data['item_code'];
		$current = $data['current'];
		$consum = $data['consum'];
		$bal = $data['bal'];
		$email= DB::connection('mysql_essex')
                ->table('users')
                ->where('users.user_id', $request->inspected_by)
                ->select('users.email', 'users.employee_name')
				->first();
				
				$arr = $request->item_code;

				$ar=array_unique( array_diff_assoc( $arr, array_unique( $arr ) ) );
				if(!empty($ar)){
					foreach($ar as $i => $r){
						
						$row= $i +1;
		
						return response()->json(['success' => 0, 'message' => 'Please check DUPLICATE '.$r.' at ROW '.$row ]);
		
					}
					
				}else{
				if ($request->item_code) { 
				   foreach($item_code as $i => $row){
						if (DB::connection('mysql_mes')
							->table('powder_coating')
							->whereDate('date', $now->format('Y-m-d'))
							->where('item_code',$row)
							->exists()){
								return response()->json(['success' => 0, 'message' => 'Powder Coat  Variant '.$row.' for today already exist.']);
						}elseif($row == 'none'){
								return response()->json(['success' => 0, 'message' => 'Please Select Item Code']);
	
					
						}elseif($consum[$i] == 'none'){
								return response()->json(['success' => 0, 'message' => 'Please Indicate consumed qty']);
	
						}else{
								$data = [
								'operating_hrs' => $request->operating_hrs,
								'current_qty' =>$current[$i],
								'consumed_qty' => $consum[$i],
								'balance_qty' => $bal[$i],
								'item_code' => $row,
								'operator_id' => $request->inspected_by,
								'operator' => $email->employee_name,
								'date' =>  $now->toDateTimeString(),
								'last_modified_by' =>$email->email,
								'created_by' => $email->email,
								'created_at' => $now->toDateTimeString()
								];
								DB::connection('mysql_mes')->table('powder_coating')->insert($data);
								DB::connection('mysql_mes')->table('fabrication_inventory')->where('item_code', $row)->update(['balance_qty' => $bal[$i], 'last_modified_at' => $now->toDateTimeString(), 'last_modified_by' => $email->employee_name]);						
						} 
					}
				}
	
			return response()->json(['success'=>1, 'message' => 'Record Successfuly inserted.']);
		}
	}
}