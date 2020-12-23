<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Validator;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Mail\SendMail_feedbacking;
use App\Mail\SendMail_New_DeliveryDate_Alert;
use App\Traits\GeneralTrait;

class MainController extends Controller
{
	use GeneralTrait;

	public function index(){
		return redirect('/login');
	}

	public function loginUserFrm(){
		if (Auth::user()) {
			return redirect('/main_dashboard');
		}

		return view('login');
	}

	public function operatorDashboard($machine, $workstation, $job_ticket_id){
		if(!Auth::user()){
			return redirect('/operator/' . $workstation);
		}

		$workstation_details = DB::connection('mysql_mes')->table('workstation')->where('workstation_name', $workstation)
			->select('workstation_name', 'workstation_id')->first();
		
		$workstation = $workstation_details->workstation_name;
		$workstation_id = $workstation_details->workstation_id;
			
		$job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)->first();

		$machine_workstations = DB::connection('mysql_mes')->table('machine as m')
			->join('workstation_machine AS wm', 'm.machine_code', 'wm.machine_code')
			->where('m.machine_id', $machine)->orWhere('m.machine_code', $machine)->get();

		$machine_details = DB::connection('mysql_mes')->table('machine')->where('machine_id', $machine)
			->orWhere('machine_code', $machine)->first();

		return view('operator_dashboard', compact('machine_details', 'machine_workstations', 'job_ticket_details', 'workstation_id', 'workstation'));
	}

	public function assignedTasks($workstation, $machine_code){
		$assigned = DB::connection('mysql_mes')->table('job_ticket AS tsd')
			->join('production_order AS pro', 'pro.production_order', 'tsd.production_order')
			->join('time_logs', 'time_logs.job_ticket_id', 'tsd.job_ticket_id')
			->where('tsd.workstation', $workstation)
			->where('time_logs.status', 'Pending')
			->select('tsd.process_id', 'pro.description', 'pro.production_order', 'pro.customer', 'pro.project', 'pro.sales_order', 'pro.item_code', 'pro.qty_to_manufacture', 'time_logs.operator_id', 'pro.material_request')
			->orderBy('pro.order_no', 'asc')->orderBy('pro.planned_start_date', 'asc')->get();

		$assigned_tasks = [];
		foreach ($assigned as $a) {
			$process = DB::connection('mysql_mes')->table('process')->where('process_id', $a->process_id)->first();
			$process_name = ($process) ? $process->process_name : null;

			$completed_qty = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $a->production_order)
			->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
				->where('process_id', $a->process_id)->sum('good');

			$balance_qty = $a->qty_to_manufacture - $completed_qty;

			$assigned_tasks[] = [
				'production_item' => $a->item_code,
				'description' => $a->description,
				'balance_qty' => $balance_qty,
				'production_order' => $a->production_order,
				'customer' => $a->customer,
				'sales_order' => $a->sales_order,
				'material_request' => $a->material_request,
				'process' => $process_name,
			];
		}

		return view('tables.tbl_assigned_tasks', compact('assigned_tasks'));
	}

	public function operator_scheduled_task($workstation, $process_id){
		$start = Carbon::now()->startOfDay()->toDateTimeString();
		$end = Carbon::now()->endOfDay()->toDateTimeString();

		$task = DB::connection('mysql_mes')->table('production_order as pro')
			->join('job_ticket as jt', 'pro.production_order', 'jt.production_order')
			->where('jt.workstation', $workstation)->where('jt.process_id', $process_id)
			->whereBetween('pro.planned_start_date', [$start, $end])
			->where('pro.status', '!=', 'Cancelled')->orderBy('pro.order_no', 'asc')->get();

		return view('tables.tbl_operator_scheduled_task', compact('task'));
	}
	
	public function seconds2human($ss) {
	    $s = $ss%60;
	    $m = floor(($ss%3600)/60);
	    $h = floor(($ss%86400)/3600);
	    $d = floor(($ss%2592000)/86400);
	    $ss = $s > 1 ? "secs":'sec';
	    $mm = $m > 1 ? "mins":'min';
	    $dd = $d > 1 ? "days":'day';
	    $hh = $h > 1 ? "hrs":'hr';
	    
	    if($d == 0 and $h == 0 and $m == 0 and $s == 0) {
	       $format= "$s $ss";
	    }elseif($d == 0 and $h == 0 and $m == 0) {
	       $format= "$s $ss";
	    }elseif($d == 0 and $h == 0) {
	       $format= "$m $mm,$s $ss";
	    }elseif($d == 0) {
	       $format= "$h $hh, $m $mm,$s $ss";
	    }else{
	        $format="$d $dd,$h $hh, $m $mm,$s $ss";
	    }
	    return $format;
	}

	public function seconds2humanforduration($ss) {
	    $s = $ss%60;
		$m = floor(($ss%3600)/60);
		$h = floor(($ss%86400)/3600);
		$d = floor(($ss%2592000)/86400);
		$M = floor($ss/2592000);
	    $ss = $s > 1 ? "secs":'sec';
	    $mm = $m > 1 ? "mins":'min';
	    $dd = $d > 1 ? "days":'day';
		$hh = $h > 1 ? "hrs":'hr';
		$MM = $M > 1 ? "Months":'Month';
	    
	    if($M == 0 and $d == 0 and $h == 0 and $m == 0 and $s == 0) {
	       $format= "$s $ss";
	    }elseif($M == 0 and $d == 0 and $h == 0 and $m == 0) {
	       $format= "$s $ss";
	    }elseif($M == 0 and $d == 0 and $h == 0) {
	       $format= "$m $mm";
	    }elseif($M == 0 and $d == 0) {
		   $format= "$h $hh";
		}elseif($M == 0) {
			$format= "$d $dd";
	    }else{
	        $format="$M $MM";
	    }
	    return $format;
	}

	public function loginUserId(Request $request){
		try {
			$essex_connection = $this->check_essex_connection();
			if($essex_connection['response'] <= 0){
				return response()->json(['success' => 0, 'message' => $essex_connection['message']]);
			}

			// check if user exist in user table in MES
			$mes_user = DB::connection('mysql_mes')->table('user')
				->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
				->where('user_access_id', $request->user_id)->where('module', $request->login_as)->first();

			if(!$mes_user){
				return response()->json(['success' => 0, 'message' => '<b>User not allowed!</b>']);
			}

			$redirect_to = "/main_dashboard";
			if($request->login_as == 'Quality Assurance'){
				$redirect_to = '/qa_dashboard';
			}

			// validate the info, create rules for the inputs
			$rules = array(
				'user_id' => 'required',
				'password' => 'required',
			);

			$validator = Validator::make(Input::all(), $rules);

			// if the validator fails, redirect back to the form
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)
					->withInput(Input::except('user_id', 'password'));
			}else{
				// create our user data for the authentication
				$user_data = array(
					'user_id'  => Input::get('user_id'),
					'password'  => Input::get('password')
				);

				// attempt to do the login
				if(Auth::attempt($user_data)){
					return response()->json(['success' => 1, 'message' => "<b>Welcome!</b> Please wait...", 'redirect_to' => $redirect_to]);
				} else {        
					// validation not successful, send back to form 
					return response()->json(['success' => 0, 'message' => '<b>Invalid credentials!</b> Please try again.']);
				}
			}
		} catch (\Exception $e) {
			return response()->json(['success' => 0, 'message' => '<b>There was a problem connecting to ESSEX.</b>']);
		}
	}

	public function loginOperatorId(Request $request){
		$essex_connection = $this->check_essex_connection();
		if($essex_connection['response'] <= 0){
			return response()->json(['success' => 0, 'message' => $essex_connection['message']]);
		}
		
		// validate the info, create rules for the inputs
    	$rules = array(
		    'operator_id' => 'required'
		);

		$validator = Validator::make(Input::all(), $rules);

		// if the validator fails, redirect back to the form
		if ($validator->fails()) {
			// return redirect()->back()->withErrors($validator)
		 //        ->withInput(Input::except('operator_id'));
		    return response()->json(['success' => 0, 'message' => 'Please tap your Operator ID or enter your Biometric ID.']);
		}else{
			// create our user data for the authentication
		    $user_data = array(
		        'id_security_key'  => Input::get('operator_id')
		    );

		    // attempt to do the login
            $user = DB::connection('mysql_essex')->table('users')
            	->where('id_security_key', $request->operator_id)
            	->orWhere('user_id', $request->operator_id)->first();

            if ($user) {
                if(Auth::loginUsingId($user->id)){
                    return response()->json(['success' => 1, 'message' => "<b>Welcome!</b> Please wait..."]);
                } 
            } else {        
                // validation not successful, send back to form 
                return response()->json(['success' => 0, 'message' => '<b>Invalid credentials!</b> Please try again.']);
            }
		}
	}

	public function logoutUser(){
        Auth::guard('web')->logout();
        return redirect('/');
    }

    public function logout($id){
        Auth::guard('web')->logout();
        $route = '/operator/'.$id;
        return redirect($route);
    }

	public function validateMachine($machine_id){
		try {
			$details = DB::connection('mysql_mes')->table('workstation_machine')
				->join('machine', 'machine.id', 'workstation_machine.machine_id')
				->where('machine.machine_code', $machine_id)
				->select('machine.*')->first();

			if (!$details) {
				return response()->json(["success" => 0, "message" => 'Machine not found. Try again.', 'details' => []]);
			}

			return response()->json(["success" => 1, "message" => 'Machine found. Please wait.', 'details' => $details]);
		} catch (Exception $e) {
			return response()->json(["success" => 0, "message" => $e->getMessage()]);
		}
	}

	public function getJtWorkstation($jtno, $workstation){
		try {
			$jt = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $jtno)->first();
			if (!$jt) {
				return response()->json(["success" => 0, "message" => "Job Ticket for <b>".$jtno."</b> not found."]);
			}

			$assigned_tasks = DB::connection('mysql_mes')->table('job_ticket AS tsd')
				->join('production_order AS pro', 'pro.production_order', 'tsd.production_order')
				->where('tsd.production_order', $jt->production_order)
				->where('tsd.workstation', $workstation)
				->whereIn('tsd.status', ['Accepted', 'In Progress'])
				->select('tsd.id AS tsdname', 'pro.item_code', 'pro.description', 'pro.production_order', 'tsd.completed_qty', 'tsd.status', 'tsd.good', 'tsd.reject', 'tsd.rework', 'pro.order_no', 'tsd.qty_accepted','pro.customer', 'pro.project', 'pro.sales_order', 'tsd.to_time','tsd.from_time', 'tsd.hours', 'pro.order_no', 'pro.planned_start_date','tsd.item_feedback','tsd.qa_inspection_status', 'pro.order_no', 'tsd.remarks', 'pro.priority', 'pro.qty_to_manufacture')
				->orderBy('pro.created_at', 'asc')->get();

			$data = [];
			foreach ($assigned_tasks as $a) {
				$prev = $this->checkPreviousWorkstation($a->production_order);
				$data[] = [
					'tsdname' => $a->tsdname,
					'qty_to_manufacture' => $a->qty_to_manufacture,
					'production_item' => $a->item_code,
					'description' => $a->description,
					'status' => $a->status,
					'qty_accepted' => $a->qty_accepted,
					'production_order' => $a->production_order,
					'completed_qty' => $a->completed_qty,
					'good' => $a->good,
					'order_no' => $a->order_no,
					'reject' => $a->reject,
					'rework' => $a->rework,
					'priority' => $a->priority,
					'customer' => $a->customer,
					'project' => $a->project,
					'sales_order' => $a->sales_order,
					'from_time' => $a->from_time,
					'to_time' => $a->to_time,
					'remarks' => $a->remarks,
					'hours' => $this->seconds2human($a->hours * 3600),
					'prev_good_qty' => $prev['previous_good_qty'],
					'prev_workstation' => $prev['previous_workstation'],
					'prev_operator' => $prev['previous_operator'],
				];
			}

			return view('tables.tbl_jtworkstation', compact('data'));
		} catch (Exception $e) {
			return response()->json(["success" => 0, "message" => $e->getMessage()]);
		}
	}

	public function getTimesheetProcess($prod_no){
		$req = DB::connection('mysql_mes')->table('production_order')->where('production_order', $prod_no)
			->first()->qty_to_manufacture;

		$workstations = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $prod_no)
			->selectRaw('workstation, GROUP_CONCAT(status) as status')
			->orderBy('idx', 'asc')->groupBy('workstation')->get();

		$data = [];
		foreach($workstations as $row){
			if ((strpos($row->status, 'Pending') > -1) && (strpos($row->status, 'Complet') > -1)) {
				$status = 'active';
			}elseif (strpos($row->status, 'Progress') > -1) {
				$status = 'active';
			}elseif (strpos($row->status, 'Pending') > -1) {
				$status = '';
			}else{
				$status = 'completed';
			}
			
			$data[] = [
				'workstation' => $row->workstation,
				'status' => $status,
			];
		}

		return $data;
	}

	public function getTimesheetDetails($jtno){
		$tab=[];
		$details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $jtno)
			->leftJoin('delivery_date', function($join){
				$join->on( DB::raw('IFNULL(production_order.sales_order, production_order.material_request)'), '=', 'delivery_date.reference_no');
				$join->on('production_order.parent_item_code','=','delivery_date.parent_item_code');
			}) // get delivery date from delivery_date table
			->select('production_order.*', 'delivery_date.rescheduled_delivery_date')->first();
		if (!$details) {
			return response()->json(['message' => 'Production Order <b>'.$jtno.'</b> not found.', 'item_details' => [], 'details' => [], 'operations' => [], 'success' => 0]);
		}

		$process = $this->getTimesheetProcess($details->production_order);

		$planned_start = Carbon::parse($details->planned_start_date);
		if ($details->actual_start_date) {
			if ($details->actual_start_date >= $planned_start->startOfDay() && $details->actual_start_date <= $planned_start->endOfDay()) {
				$task_status = 'On Time';
			}else{
				$task_status = 'Late';
			}
		}else{
			$task_status = '--';
		}

		$owner = explode('@', $details->created_by);
		$owner = ucwords(str_replace('.', ' ', $owner[0]));

		$item_details = [
			'planned_start_date' => Carbon::parse($details->planned_start_date)->format('M-d-Y'),
			'sales_order' => $details->sales_order,
			'material_request' => $details->material_request,
			'production_order' => $details->production_order,
			'customer' => $details->customer,
			'project' => $details->project,
			'qty_to_manufacture' => $details->qty_to_manufacture,
			'delivery_date' => ($details->rescheduled_delivery_date == null)? $details->delivery_date: $details->rescheduled_delivery_date, //link new rescchedule delivery date 
			'item_code' => $details->item_code,
			'description' => $details->description,
			'status' => $task_status,
			'owner' => $owner,
			'production_order_status' => $this->production_status_with_stockentry($details->production_order, $details->status, $details->qty_to_manufacture,$details->feedback_qty, $details->produced_qty),
			'created_at' =>  Carbon::parse($details->created_at)->format('m-d-Y h:i A')
		];

		$process_arr = DB::connection('mysql_mes')->table('job_ticket')
			->where('production_order', $details->production_order)
			->select(DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process'), 'workstation', 'process_id', 'job_ticket_id', 'status', 'completed_qty')
			->get();

		$operation_list = [];
		foreach ($process_arr as $row) {
			$operations_arr = [];
			if($row->workstation == "Spotwelding"){
				  $operations =  DB::connection('mysql_mes')->table('spotwelding_qty as qpart')
                  ->where('qpart.job_ticket_id',  $row->job_ticket_id)->get();
				  $total_rejects =collect($operations)->sum('reject');
				  $min_count= collect($operations)->min('from_time');
				  $max_count=collect($operations)->max('to_time');
				  $status = collect($operations)->where('status', 'In Progress');
				  $operations_arr[] = [
					'machine_code' => "",
					'operator_name' => "",
					'from_time' => $min_count,
					'to_time' => ($row->status == "In Progress") ? '' : $max_count,
					'status' => (count($status) == 0 )? 'Not started': "In Progress",
					'qa_inspection_status' => "",
					'good' => $row->completed_qty,
					'reject' => $total_rejects,
					'remarks' => "",
				];
			}else{
				$operations = DB::connection('mysql_mes')->table('job_ticket AS jt')
				->join('time_logs', 'time_logs.job_ticket_id', 'jt.job_ticket_id')
				->where('time_logs.job_ticket_id', $row->job_ticket_id)
				->where('workstation','!=', 'Spotwelding')
				->select('jt.*', 'time_logs.*')
				->orderBy('idx', 'asc')->get();

				foreach ($operations as $d) {
					$reference_type = ($d->workstation == 'Spotwelding') ? 'Spotwelding' : 'Time Logs';
					$reference_id = ($d->workstation == 'Spotwelding') ? $d->job_ticket_id : $d->time_log_id;
					$qa_inspection_status = $this->get_qa_inspection_status($reference_type, $reference_id);

					if ($d->cycle_time_in_seconds > 0) {
						$cycle_time_in_seconds = $d->cycle_time_in_seconds / $d->good;

						$dur_hours = floor($cycle_time_in_seconds / 3600);
						$dur_minutes = floor(($cycle_time_in_seconds / 60) % 60);
						$dur_seconds = $cycle_time_in_seconds % 60;
			
						$dur_hours = ($dur_hours > 0) ? $dur_hours .'h' : null;
						$dur_minutes = ($dur_minutes > 0) ? $dur_minutes .'m' : null;
						$dur_seconds = ($dur_seconds > 0) ? $dur_seconds .'s' : null;
			
						$cycle_time_per_log = $dur_hours . ' '. $dur_minutes . ' ' . $dur_seconds;
					}else{
						$cycle_time_per_log = '-';
					}

					$operations_arr[] = [
						'machine_code' => $d->machine_code,
						'operator_name' => $d->operator_name,
						'from_time' => ($d->from_time) ? Carbon::parse($d->from_time)->format('M-d-Y h:i A') : '',
						'to_time' => ($d->to_time) ? Carbon::parse($d->to_time)->format('M-d-Y h:i A') : '',
						'status' => $d->status,
						'qa_inspection_status' => $qa_inspection_status,
						'good' => $d->good,
						'reject' => $d->reject,
						'remarks' => $d->remarks,
						'cycle_time_per_log' => $cycle_time_per_log
					];
				}
			}
			$operation_list[] = [
				'production_order' => $jtno,
				'workstation' => $row->workstation,
				'process' => $row->process,
				'job_ticket' => $row->job_ticket_id,
				'count_good' => (count($operations_arr) <= 1) ? '' : "Total: ".collect($operations_arr)->sum('good'),
				'count' => (count($operations_arr) > 0) ? count($operations_arr) : 1,
				'operations' => $operations_arr,
				'cycle_time' => $this->compute_item_cycle_time_per_process($details->item_code, $details->qty_to_manufacture, $row->workstation, $row->process_id)
			];
		}
		$processes = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $details->production_order)
			->distinct()->pluck('job_ticket_id');
		$process_list = [];
		foreach ($processes as $row) {
			$query = DB::connection('mysql_mes')->table('time_logs')
				->where('job_ticket_id', $row)->where('status', 'Completed')->get();
			$process_list[] = [
				'process_id' => $row,
				'total_good' => collect($query)->sum('good'),
				'total_reject' => collect($query)->sum('reject'),
			];
		}
		$totals = [
			'produced_qty' => $details->produced_qty,
			'total_good' => collect(array_column($process_list, 'total_good'))->min(),
			'total_reject' => collect(array_column($process_list, 'total_reject'))->max(),
			'balance_qty' => $details->qty_to_manufacture - $details->produced_qty,
		];
		$datas=[];
		$tab_name=$details->item_classification;
		$po=DB::connection('mysql_mes')->table('production_order')->where('sales_order',$details->sales_order)->where('material_request',$details->material_request)->where('parent_item_code', $details->parent_item_code)->where('sub_parent_item_code', $details->item_code)->whereNotIn('production_order', [$details->production_order])->get();
		if(count($po) > 0){
			foreach($po as $rowss){
				$data[]=[
					'production_order' => $rowss->production_order,
					'item_classification' => $rowss->item_classification,
					'item_code' => $rowss->item_code,
					'description' =>$rowss->description,
					'parts_category' => $rowss->parts_category,
					'qty_to_manufacture' =>$rowss->qty_to_manufacture,
					'produced_qty' => $rowss->produced_qty,
					'feedback_qty' => $rowss->feedback_qty,
					'stock_uom' => $rowss->stock_uom,
					'planned_start_date' => ($rowss->planned_start_date == null)?'-':Carbon::parse($rowss->planned_start_date)->format('M-d-Y'),
					'status' => $rowss->status,
					'actual_start_date' => Carbon::parse($rowss->actual_start_date)->format('M-d-Y h:i A'),
					'actual_end_date' => ($rowss->status == "Not Started") ? '-' : Carbon::parse($rowss->actual_end_date)->format('M-d-Y h:i A'),
					'created_at' => $rowss->created_at,
					'created_by' => $rowss->created_by,
					'stock_uom' => $rowss->stock_uom,
					'material_status' => $this->material_status_stockentry($rowss->production_order, $rowss->status, $rowss->qty_to_manufacture,$rowss->feedback_qty, $rowss->produced_qty)
				];
			}
			$tab[]=[
				'tab' => substr($details->item_code, 0, 2).'-Parts',
				'data' => $data

			];
		}

		$reference= ($details->sales_order == null)? $details->material_request: $details->sales_order;
		$tbl_reference= ($details->sales_order == null)? "tabMaterial Request Item": "tabSales Order Item";
		$get_delivery_date=DB::connection('mysql_mes')->table('delivery_date')->where('reference_no', $reference)->where('parent_item_code',  $details->item_code)->first();
		$notifications=['match' => 'false'];
		if(!empty($get_delivery_date)){
            $erp_sales_order=DB::connection('mysql')->table($tbl_reference)->where('name', $get_delivery_date->erp_reference_id)->select('item_code')->first();
            if(!empty($erp_sales_order)){
                if($erp_sales_order->item_code != $details->parent_item_code){
					$notifications= [
						"match"=> 'true',	
						'message' => 'Parent item code was change from <b>'.$details->parent_item_code.'</b> to <b>'.$erp_sales_order->item_code.'</b>',
					];
                    }
                }
			}
		$success=1;
		return view('tables.production_order_search_content', compact('process', 'totals', 'item_details', 'operation_list','success', 'tab_name','tab', 'notifications'));
	}

	public function sub_track_tab($sales_order, $parent_item_code, $sub_parent_item_code, $item_code, $material_request){
		$po=DB::connection('mysql_mes')->table('production_order')->where('sales_order',$sales_order)->where('material_request',$material_request)->where('parent_item_code', $parent_item_code)->where('sub_parent_item_code', $item_code)->get();

		foreach($po as $rows){
						
			$processes = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $rows->production_order)->distinct()->pluck('job_ticket_id');
			$process_list = [];
				foreach ($processes as $row) {
					$query = DB::connection('mysql_mes')->table('time_logs')
						->where('job_ticket_id', $row)->where('status', 'Completed')->get();
					
						$process_list[] = [
							'process_id' => $row,
							'max' => collect($query)->max('to_time'),
							'min_time' => collect($query)->min('from_time'),
							
						];
				}
			
			$data[]=[
				'production_order' => $rows->production_order,
				'item_classification' => $rows->item_classification,
				'item_code' => $rows->item_code,
				'description' =>$rows->description,
				'parts_category' => $rows->parts_category,
				'qty_to_manufacture' =>$rows->qty_to_manufacture,
				'produced_qty' => $rows->produced_qty,
				'feedback_qty' => $rows->feedback_qty,
				'stock_uom' => $rows->stock_uom,
				'planned_start_date' => ($rows->planned_start_date == null)?'-':Carbon::parse($rows->planned_start_date)->format('M-d-Y'),
				'status' => $rows->status,
				'start_date' => Carbon::parse(collect(array_column($process_list, 'min_time'))->min())->format('M-d-Y h:i A'),
				'end_date' => ($rows->status == "Not Started") ? '-' : Carbon::parse(collect(array_column($process_list, 'max'))->max())->format('M-d-Y h:i A'),
				'created_at' => $rows->created_at,
				'created_by' => $rows->created_by,
				'stock_uom' => $rows->stock_uom,
				// 'stock_uom' => $this->get_child($prod_details->bom_no,$prod_details->sales_order, $prod_details->item_code, $prod_details->parent_item_code),
			];

		}
		return $data;
	}

	public function checkPreviousWorkstation($jtno){
		$current_idx = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $jtno)->first()->idx;

		if ($current_idx > 1) {
			$previous = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $jtno)->where('idx', $current_idx - 1);

			$previous_good_qty = $previous->sum('good');
			$previous_workstation = $previous->first()->workstation;
			$previous_operator = $previous->first()->operator_name;

			return [
				'previous_good_qty' => $previous_good_qty,
				'previous_workstation' => $previous_workstation,
				'previous_operator' => $previous_operator,
			];
		}

		return [
			'previous_good_qty' => -1,
			'previous_workstation' => null,
			'previous_operator' => '--',
		];
	}

	public function getTimesheetRow($id){
		$details = DB::connection('mysql_mes')->table('job_ticket AS tsd')->join('production_order AS pro', 'pro.production_order','tsd.production_order')->where('tsd.job_ticket_id', $id)->select('tsd.*','pro.qty_to_manufacture')->first();

		$details2 = DB::connection('mysql_mes')->table('job_ticket AS tsd')->join('production_order AS pro', 'pro.production_order','tsd.production_order')->where('tsd.workstation', $details->workstation)->where('tsd.production_order', $details->production_order)->get();
		$data=[];
		$sum_bal =collect($details2)->sum('qty_accepted');
		$diff = $details->qty_to_manufacture - $sum_bal;
		$data[] =[
				'qty_diff' => $diff,
				'qty_accepted' => $details->qty_accepted,
				'completed_qty' => $details->completed_qty
				];
		return response()->json($data);
	}

	public function endTask(Request $request){
        try {
			$now = Carbon::now();
			$current_task = DB::connection('mysql_mes')->table('time_logs')
				->where('time_log_id', $request->id)->first();

			$seconds = $now->diffInSeconds(Carbon::parse($current_task->from_time));
			$duration= $seconds / 3600;

			$cycle_time_in_seconds = $seconds / $request->completed_qty;

			$good_qty = $request->completed_qty - $current_task->reject;

			// get in progress time logs
			$in_progress_time_logs = DB::connection('mysql_mes')->table('job_ticket')
				->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
				->where('time_logs.job_ticket_id', $current_task->job_ticket_id)
				->where('time_logs.status', 'In Progress')->get();

			if(count($in_progress_time_logs) > 1){
				$in_progress_operators = array_column($in_progress_time_logs->toArray(), 'operator_name');
				if (($key = array_search(Auth::user()->employee_name, $in_progress_operators)) !== false) {
					unset($in_progress_operators[$key]);
				}

				// get qty_to_manufacture
				$qty_to_manufacture = DB::connection('mysql_mes')->table('production_order')
					->where('production_order', $request->production_order)->sum('qty_to_manufacture');
				
				$job_ticket_completed_qty = DB::connection('mysql_mes')->table('job_ticket')
					->where('job_ticket_id', $current_task->job_ticket_id)->sum('completed_qty');
					
				if(($job_ticket_completed_qty + $good_qty) >= $qty_to_manufacture){
					return response()->json(['success' => 0, 'message' => 'Cannot complete all quantity.<br>In-progress task by ' . implode(', ', $in_progress_operators)]);
				}
			}

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
			
			DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $request->id)->update($update);

			$process_id = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $current_task->job_ticket_id)->first()->process_id;
			
			$this->updateProdOrderOps($request->production_order, $request->workstation, $process_id);
			$this->update_completed_qty_per_workstation($current_task->job_ticket_id);
			$this->update_produced_qty($request->production_order);
			$this->update_production_actual_start_end($request->production_order);

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
    	$job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)->first();

    	$qty_to_manufacture = DB::connection('mysql_mes')->table('production_order')
	    		->where('production_order', $job_ticket_details->production_order)->sum('qty_to_manufacture');
    	
    	$logs = DB::connection('mysql_mes')->table('time_logs')->where('job_ticket_id', $job_ticket_id)->exists();

    	// get total good qty from timelogs
    	$total_good = DB::connection('mysql_mes')->table('time_logs')->where('job_ticket_id', $job_ticket_id)->where('status', 'Completed')->sum('good');

    	if ($logs && $total_good >= 0) {
    		DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)
    			->update(['status' => 'In Progress']);
    	}

    	if ($qty_to_manufacture == $total_good) {
    		DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)
    			->update(['status' => 'Completed', 'completed_qty' => $total_good]);
    	}else{
    		DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)
    			->update(['completed_qty' => $total_good]);
    	}
    }

	public function updateProdOrderOps($prod_order, $workstation, $process_id){
        try {
            $prod_qty = DB::connection('mysql_mes')->table('production_order')
                ->where('production_order', $prod_order)->sum('qty_to_manufacture');
            
            // get total completed
            $tsd = DB::connection('mysql_mes')->table('job_ticket')
            	->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
				->where('workstation', $workstation)->where('production_order', $prod_order)
				->where('process_id', $process_id)
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
				->where('parent', $prod_order)->where('workstation', $workstation)
				->where('process', $process_id)->update($data);

        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
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

    public function machineBreakdownSave(Request $request){
        try {
            $now = Carbon::now();
            $values = [
                'name' => 'mb'.uniqid(),
                'creation' => $now->toDateTimeString(),
                'modified' => $now->toDateTimeString(),
                'modified_by' => $request->reported_by,
                'owner' => $request->reported_by,
                'docstatus' => 0,
                'parent' => $request->id,
                'parentfield' => 'breakdown_history',
                'parenttype' => 'Machine',
                'idx' => 1,
                'status' => 'Pending',
                'category' => $request->category,
                'work_done' => null,
                'reported_by' => $request->reported_by,
                'maintenance_staff' => null,
                'date_resolved' => null,
                'date_reported' => $now->toDateTimeString(),
                'corrective_reason' => $request->corrective_reason,
                'breakdown_reason' => $request->breakdown_reason,
                'type' => $request->category
            ];

            DB::connection('mysql')->table('tabMachine Breakdown')->insert($values);

            DB::connection('mysql')->table('tabMachine')->where('name', $request->id)->update(['status' => 'Unavailable']);
            
            return response()->json(['success' => 1, 'message' => 'Machine Breakdown successfully submitted.']);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function getWorkstationMachines($workstation){
    	return DB::connection('mysql_mes')->table('workstation_machine as wm')->join('machine as m', 'wm.machine_code', 'm.machine_code')
    		->where('wm.workstation', $workstation)
    		->get();
    }

    public function get_production_quality_check($production_order, $workstation, $machine, $type){
		$row = DB::connection('mysql_mes')->table('job_ticket AS jt')
			->join('production_order AS pro', 'pro.production_order', 'jt.production_order')
			->where('jt.production_order', $production_order)->where('jt.workstation', $workstation)
			->where('jt.machine', $machine)->where('jt.qa_inspection_status', 'Pending')
			->whereNotIn('pro.status', ['Stopped', 'Cancelled'])->where('jt.status', 'Completed')
            ->when($type == 'Quality Check', function ($query) {
				return $query->where('jt.item_feedback', 'Quality Check');
            })
			->select('jt.id', 'pro.description', 'jt.production_order', 'jt.completed_qty', 'jt.status', 'jt.good', 'jt.rework', 'jt.qty_accepted', 'jt.qa_inspection_status', 'jt.remarks', 'jt.sampling_qty','pro.cutting_size','pro.item_code', 'jt.reject', 'jt.rejection_type','jt.operator_name')
			->orderBy('pro.order_no', 'asc')
			->orderBy('pro.planned_start_date', 'asc')
			->first();

		if ($row->cutting_size) {
			$cutting_size = strtoupper($row->cutting_size);
			$cutting_size = str_replace(' ', '', preg_replace('/\s+/', '', $cutting_size));
			$cutting_size = explode("X", $cutting_size);
			$length = $cutting_size[0];
			$width = $cutting_size[1];
			$thickness = $cutting_size[2];
		}

		$data = [
			'tsdname' => $row->id,
			'production_order' => $row->production_order,
			'production_item' => $row->item_code,
			'description' => $row->description,
			'sampling_qty' => $row->sampling_qty,
			'qty_accepted' => $row->qty_accepted,
			'length' => ($row->cutting_size) ? $length : null,
			'width' => ($row->cutting_size) ? $width : null,
			'thickness' => ($row->cutting_size) ? $thickness : null,
			'good' => $row->good,
			'completed_qty' => $row->completed_qty,
			'reject' => $row->reject,
			'rejection_type' => $row->rejection_type,
			'operator_name' => $row->operator_name,
		];

		$type = ($type == 'Quality Check') ? $type : ($type == 'Reject Confirmation') ? $type : 'Random Inspection';

		return view('tables.tbl_quality_check', compact('data', 'type'));
	}

	// BEGIN PPC Staff
	public function mainDashboard(){
		$mes_user = DB::connection('mysql_mes')->table('user')->where('user_access_id', Auth::user()->user_id)->first();
		$mes_user_operations = DB::connection('mysql_mes')->table('user')
			->join('operation', 'operation.operation_id', 'user.operation_id')
			->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
			->where('module', 'Production')
			->where('user_access_id', Auth::user()->user_id)
			->pluck('operation_name')->toArray();

		$permissions = $this->get_user_permitted_operation();

		$user_details = DB::connection('mysql_essex')->table('users')
                    ->join('designation', 'users.designation_id', '=', 'designation.des_id')
                    ->join('departments', 'users.department_id', '=', 'departments.department_id')
                    ->where('user_id', Auth::user()->user_id)->first();

		$timesheet = DB::connection('mysql_mes')->table('job_ticket')->select('job_ticket_id')->get();

		return view('main_dashboard', compact('timesheet', 'user_details', 'mes_user', 'mes_user_operations', 'permissions'));
	}

	public function productionPlanning(){
		$user_details = DB::connection('mysql_essex')->table('users')
            ->join('designation', 'users.designation_id', '=', 'designation.des_id')
            ->join('departments', 'users.department_id', '=', 'departments.department_id')
            ->where('user_id', Auth::user()->user_id)
            ->first();

		return view('production_planning', compact('user_details'));
	}

	public function getNotifications(){
		$now = Carbon::now();
		$notifs = [];
		$get_prod_sched_today=DB::connection('mysql_mes')->table('production_order')->whereDate('planned_start_date', $now)->groupBy('parent_item_code', 'sales_order', 'material_request')->select('parent_item_code', 'sales_order', 'material_request')->get();
		foreach($get_prod_sched_today as $row){
			$reference= ($row->sales_order == null)? $row->material_request: $row->sales_order;
			$tbl_reference= ($row->sales_order == null)? "tabMaterial Request Item": "tabSales Order Item";
			$get_delivery_date=DB::connection('mysql_mes')->table('delivery_date')->where('reference_no', $reference)->where('parent_item_code',  $row->parent_item_code)->first();
			if(!empty($get_delivery_date)){
				$erp_sales_order=DB::connection('mysql')->table($tbl_reference)->where('name', $get_delivery_date->erp_reference_id)->first()->item_code;
				if($erp_sales_order != $row->parent_item_code){
					$notifs[] = [	
						'type' => 'Change Code',
						'message' => 'Parent item code was change <br> from <b>'.$row->parent_item_code.'</b> to <b>'.$erp_sales_order.'</b> <br> Reference no: <b> '.$reference.'</b>',
						'created' => $now->toDateTimeString(),
						'timelog_id' =>	"",
						'table' => 'ERP'
					];
				}
			}

		}
		$user_permitted_operations = DB::connection('mysql_mes')->table('user')
			->join('operation', 'operation.operation_id', 'user.operation_id')
            ->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
			->where('user_access_id', Auth::user()->user_id)
			->where('module', 'Production')
			->select('user.operation_id', 'operation_name')
			->distinct()->get();

		$user_permitted_operation_id = collect($user_permitted_operations)->pluck('operation_id');
		$user_permitted_operation_names = collect($user_permitted_operations)->pluck('operation_name');
		
		$permitted_workstation = DB::connection('mysql_mes')->table('workstation')
			->whereIn('operation_id', $user_permitted_operation_id)->distinct()
			->pluck('workstation_name')->toArray();

		if(in_array('Painting', $user_permitted_operation_names->toArray())){
			array_push($permitted_workstation, ['Painting']);
		}

		$w_machines = DB::connection('mysql_mes')->table('workstation_machine')->whereIn('workstation', $permitted_workstation)->distinct()->pluck('machine_code');
		
		$machine_breakdown = DB::connection('mysql_mes')->table('machine as m')
			->join('machine_breakdown as mb', 'm.machine_code', 'mb.machine_id')
			->whereIn('m.status', ['Unavailable', 'On-going Maintenance'])
			->whereIn('m.machine_code', $w_machines)
			->select('m.*', 'mb.category', 'mb.date_reported','mb.type')
			->get();

		foreach ($machine_breakdown as $mb) {
			$converted= Carbon::parse($mb->date_reported)->format('Y-m-d');
			
        	$from_carbon = Carbon::parse($now);
            $to_carbon = Carbon::parse($mb->date_reported);

			$duration = $from_carbon->diffInSeconds($to_carbon);
			$converted_duration = $this->seconds2humanforduration($duration);
			
			$notifs[] = [
				'type' => 'Machine Breakdown',
				'message' => $mb->machine_name.'<br>Machine Request: ' . $mb->type.'<br> Date Reported:'.$converted.'<br><b><i>'.$converted_duration.'</i> ago</b>',
				'created' => $mb->date_reported,
				'timelog_id' =>	'',
				'table' => 'machine'
			];
		}
		$prod_late_delivery= DB::connection('mysql_mes')
		->table('production_order')
		->leftJoin('delivery_date', function($join){
			$join->on( DB::raw('IFNULL(production_order.sales_order, production_order.material_request)'), '=', 'delivery_date.reference_no');
			$join->on('production_order.parent_item_code','=','delivery_date.parent_item_code');
		})
		->whereRaw('production_order.planned_start_date > production_order.delivery_date')
		->where('production_order.status', '!=', 'Completed')
		->whereDate('production_order.created_at', '>', '2020-07-01')
		->select( 'delivery_date.rescheduled_delivery_date','production_order.production_order','production_order.delivery_date', 'production_order.planned_start_date', 'production_order.stock_uom','production_order.qty_to_manufacture', 'production_order.created_at')
		->where('production_order.operation_id', '3')->get();
		// dd($prod_late_delivery);
		// $unassigned = Carbon::now()->subHour(8)->toDateTimeString();
		// $accepted = Carbon::now()->subHour(2)->toDateTimeString();
		
		// $os_unassigned = DB::connection('mysql_mes')->table('job_ticket as td')
		// 	->join('production_order as t', 't.production_order', 'td.production_order')
		// 	->where('td.status', 'Unassigned')->whereDate('t.created_at', '<', $unassigned);

		// $os_accepted = DB::connection('mysql_mes')->table('job_ticket as td')
		// 	->join('production_order as t', 't.production_order', 'td.production_order')
		// 	->where('td.status', 'Accepted')->whereDate('t.created_at', '<', $accepted)
		// 	->union($os_unassigned)->get();
		
		$dateMinusOneWeek = Carbon::now()->subWeek()->toDateTimeString();

		$get_timelog_reject= DB::connection('mysql_mes')->table('time_logs as tl')
			->join('job_ticket as jt', 'jt.job_ticket_id', 'tl.job_ticket_id')
			->join('process as p','p.process_id','jt.process_id')
			->whereIn('jt.workstation', $permitted_workstation)
			->where('tl.reject','>', 0)
			->where('tl.is_hide','=',0)
			->where('tl.created_at','>=', $dateMinusOneWeek)
			->select('tl.*', 'jt.workstation', 'p.process_name','jt.production_order')
			->get();

		$get_spotwelding_reject= DB::connection('mysql_mes')->table('spotwelding_qty as spotqty')
			->join('job_ticket as jt', 'jt.job_ticket_id', 'spotqty.job_ticket_id')
			->join('process as p','p.process_id','jt.process_id')
			->whereIn('jt.workstation', $permitted_workstation)
			->where('spotqty.reject','>', 0)
			->where('spotqty.is_hide','=',0)
			->select('spotqty.*', 'jt.workstation', 'p.process_name', 'jt.production_order')
			->whereDate('spotqty.created_at','>=', $dateMinusOneWeek)
			->get();
		
		$item_list = DB::connection('mysql')->table('tabItem as item')
            ->join('tabWarehouse as w', 'item.default_warehouse', 'w.name')
            ->where('w.disabled', 0)
            ->where('w.is_group', 0)
            ->where('w.company', 'FUMACO Inc.')
            ->where('has_variants',"!=", 1)
            ->where('w.department', 'Fabrication')
            ->whereIn('item.item_group', ['Raw Material'])
            ->whereIn('item.item_classification', ['AS - Aluminum Sheets', 'CS - Crs Steel Coil', 'DI - Diffuser'])
            ->select('item.name', 'item.item_name', 'stock_uom')
			->orderBy('item.modified', 'desc')->get();

		foreach ($item_list as $row) {
                $min_level= DB::connection('mysql')->table('tabItem Reorder')->where('parent', $row->name)->where('material_request_type', 'Transfer')->select('warehouse_reorder_qty','warehouse_reorder_level')->first();
				$actual= DB::connection('mysql')->table('tabBin')->where('item_code', $row->name)->where('warehouse', 'like',  '%Fabrication - FI%')->select('actual_qty')->first();
				if(!empty($min_level)){
					$minimum= empty($min_level->warehouse_reorder_level)? 0:$min_level->warehouse_reorder_level;
					$actual= empty($actual->actual_qty)? 0 : $actual->actual_qty;

					if($minimum >  $actual){
						$notifs[] = [	
							'type' => 'Inventory',
							'message' => 'The Current Stock of <b>'.$row->name.'</b> is now below minimum stock level <br> Current Stock:<b>  '.$actual.' '.$row->stock_uom.'</b> <br> Minimum Stock Level: <b> '.$minimum.' '.$row->stock_uom.'</b>',
							'created' => $now->toDateTimeString(),
							'timelog_id' =>	"",
							'table' => 'production_scheduling'
						];
					}
				}
		}
		foreach ($prod_late_delivery as $prodsched) {
			$delivery_date=($prodsched->rescheduled_delivery_date == null)? $prodsched->delivery_date:$prodsched->rescheduled_delivery_date;
			$notifs[] = [	
				'type' => 'Production Schedule',
				'message' => '<b>'.$prodsched->production_order.'</b><br> Delivery Date:'.$delivery_date.'<br> Planned Start Date:'.$prodsched->planned_start_date.'<br> QTY: <b>' . $prodsched->qty_to_manufacture.'&nbsp;'.$prodsched->stock_uom.'</b>',
				'created' => $prodsched->created_at,
				'timelog_id' =>	"",
				'table' => 'production_scheduling'
			];
		}

		foreach ($get_timelog_reject as $tmlogs) {
			$notifs[] = [	
				'type' => 'Reject',
				'message' => $tmlogs->machine_name.' ['.$tmlogs->machine_code.']'.'<br>'.$tmlogs->workstation.'-'.$tmlogs->process_name.'<br><b>'.$tmlogs->production_order.'</b> | QTY: <b>' . $tmlogs->reject.'</b>',
				'created' => $tmlogs->created_at,
				'timelog_id' =>	$tmlogs->time_log_id,
				'table' => 'fabrication'
			];
		}



		foreach ($get_spotwelding_reject as $os) {
			$notifs[] = [
				'type' => 'Reject',
				'message' => $os->machine_name.' ['.$os->machine_code.']'.'<br>'.$os->workstation.'<br><b>'.$os->production_order.'</b> | QTY: <b>' . $os->reject.'</b>',
				'created' => $os->created_at,
				'timelog_id' =>	$os->time_log_id,
				'table' => 'spotwelding'
			];
		}
		$dif= collect($notifs)->SortByDesc('created');

		return $dif;
	}

	public function workstationOverview(){
		return view('workstation_overview.index');
	}

	public function getWorkstationTask($workstation){
		$tasks = DB::connection('mysql_mes')->table('production_order AS prod')
			->join('job_ticket AS jt', 'jt.production_order', 'prod.production_order')
			->where('jt.workstation', $workstation)
			->select('prod.production_order', 'prod.item_code', 'prod.description', 'prod.qty_to_manufacture', 'jt.completed_qty', 'jt.status', 'jt.operator_name', 'jt.machine_code', DB::raw('prod.qty_to_manufacture - (SELECT SUM(completed_qty) FROM job_ticket WHERE workstation = jt.workstation AND production_order = jt.production_order) AS bal'))
			->where('prod.status', '!=', 'Cancelled')
			->orderByRaw("FIELD(jt.status, 'In Progress', 'Pending', 'Completed') ASC")
			->orderBy('prod.created_at', 'asc')->get();

		return $tasks;
	}

	public function workstationTaskList(){
		try {
			$workstations = DB::connection('mysql_mes')
			->table('workstation as w')
			->join('operation as op', 'op.operation_id', 'w.operation_id')
			->where('op.operation_name', 'Fabrication')
			->orderBy('order_no', 'asc')
			->get();
			$dashboard = [];
			foreach ($workstations as $key => $value) {
				$tasks = DB::connection('mysql_mes')->table('production_order AS prod')
					->join('job_ticket AS jt', 'prod.production_order', 'jt.production_order')
					->where('workstation', $value->workstation_name)
					->select('prod.production_order', 'prod.item_code', 'prod.description', 'prod.qty_to_manufacture', 'jt.completed_qty', 'jt.status', 'jt.operator_name', 'jt.machine_code', DB::raw('(SELECT process_name FROM process WHERE process_id = jt.process_id) AS process'), DB::raw('prod.qty_to_manufacture - (SELECT SUM(completed_qty) FROM job_ticket WHERE workstation = jt.workstation AND production_order = jt.production_order) AS bal'))
					->orderByRaw("FIELD(jt.status, 'In Progress', 'Pending', 'Completed') ASC")
					->orderBy('prod.created_at', 'asc')->get();

				$wip = collect($tasks)->where('status', 'In Progress')->count();
				$ctd = collect($tasks)->where('status', 'Completed')->count();
				$ua = collect($tasks)->where('status', 'Pending')->count();
				$total = $wip + (int)$ua;

				$total_production = collect($tasks)->count();

				$percentage = (($total) > 0) ? (($total) / ($total_production)) * 100 : 0;

				$dashboard[] = [
					'workstation' => $value->workstation_name,
					'wip' => $wip,
					'ctd' => $ctd,
					'ua' => $ua,
					'percentage' => $percentage,
					'tasks' => $tasks,
				];
			}

			return view('workstation_overview.content', compact('dashboard'));
		} catch (Exception $e) {
			return response()->json(["error" => $e->getMessage()]);
		}
	}

	public function operatorList(){
		$operators = DB::connection('mysql_essex')->table('users')
			->where('status', 'Active')->where('user_type', 'Employee')
			->whereIn('designation_id', [46, 47, 53])->orderBy('employee_name', 'asc')
			->get();

		$out_today = DB::connection('mysql_essex')->table('notice_slip')
			->join('users', 'users.user_id', '=', 'notice_slip.user_id')
			->join('designation', 'users.designation_id', '=', 'designation.des_id')
			->join('departments', 'departments.department_id', '=', 'users.department_id')
			->join('leave_types', 'leave_types.leave_type_id', '=', 'notice_slip.leave_type_id')
			->whereDate('notice_slip.date_from', '<=', date("Y-m-d"))
			->whereDate('notice_slip.date_to', '>=', date("Y-m-d"))
			->where('notice_slip.status', 'Approved')
			->whereIn('users.designation_id', [46, 47, 53])
			->select('users.employee_name', 'leave_types.leave_type', 'designation.designation', 'notice_slip.date_from', 'notice_slip.date_to', 'notice_slip.time_from', 'notice_slip.time_to', 'departments.department', 'users.image')->get();

		return view('operator_list', compact('operators', 'out_today'));
	}

	public function operatorProfile($id){
		$operator_details = DB::connection('mysql_essex')->table('users')
			->join('departments', 'departments.department_id', 'users.department_id')
			->where('status', 'Active')->where('user_type', 'Employee')
			->where('user_id', $id)->first();

		$task_history = DB::connection('mysql')->table('tabTimesheet Detail as td')
			->join('tabTimesheet as t', 't.name', 'td.parent')
			->join('tabProduction Order as po', 'po.name', 't.production_order')
			->whereIn('td.status', ['Completed'])
			->where('po.company', 'FUMACO Inc.')
			->where('td.operator_id', $operator_details->id_security_key)
			->select('td.*', 'po.production_item')
			->orderBy('td.from_time', 'desc')
			->get();

		$task_histories = [];
		foreach ($task_history as $row) {
			$from = Carbon::parse($row->from_time);
			$to = Carbon::parse($row->to_time);

			$days = $from->diffInDays($to);
			$hours = $from->copy()->addDays($days)->diffInHours($to);
			$minutes = $from->copy()->addDays($days)->addHours($hours)->diffInMinutes($to);
			$dur_days = ($days > 0) ? $days .'d' : null;
			$dur_hours = ($hours > 0) ? $hours .'h' : null;
			$dur_minutes = ($minutes > 0) ? $minutes .'m' : null;

			$task_histories[] = [
				'item_code' => $row->production_item,
				'workstation' => $row->workstation,
				'completed_qty' => $row->completed_qty,
				'good' => $row->good,
				'reject' => $row->reject,
				'rework' => $row->rework,
				'duration' => $dur_days .' '. $dur_hours . ' '. $dur_minutes,
				'from_time' => Carbon::parse($row->from_time)->format('Y-m-d h:i A'),
				'to_time' => Carbon::parse($row->to_time)->format('Y-m-d h:i A'),
			];
		}

		$machine_assignment = DB::connection('mysql')->table('tabTimesheet Detail')
			->where('machine', '!=', null)->where('operator_id', $operator_details->id_security_key)
			->orderBy('from_time', 'desc')->get();

		$machine_assignments = [];
		foreach ($machine_assignment as $row) {
			$machine_assignments[] = [
				'workstation' => $row->workstation,
				'machine' => $row->machine,
				'machine_name' => $row->machine_name,
				'from_time' => Carbon::parse($row->from_time)->format('Y-m-d h:i A'),
				'to_time' => Carbon::parse($row->to_time)->format('Y-m-d h:i A'),
			];
		}

		$td = DB::connection('mysql')->table('tabTimesheet Detail')
			->whereIn('status', ['Accepted', 'In Progress', 'Completed'])
			->where('operator_id', $operator_details->id_security_key)
			->get();

		$totals = [
			'assigned' => collect($td)->count(),
			'completed' => collect($td)->where('status', 'Completed')->count(),
			'qty' => collect($td)->where('status', 'Completed')->sum('qty_done'),
		];

		$data = [
			'operator_details' => $operator_details,
			'task_histories' => $task_histories,
			'machine_assignment' => $machine_assignments,
			'totals' => $totals,
		];

		return response()->json($data);
	}

	public function itemFeedback(){
		$permissions = $this->get_user_permitted_operation();

    	// manual create production form
    	$item_list = [];

    	$parent_code_list = [];

    	$sub_parent_code_list = [];

    	$warehouse_list = [];

        $so_list = [];

        $mreq_list = [];

		return view('reports.item_feedback', compact('item_list', 'warehouse_list', 'so_list', 'mreq_list', 'parent_code_list', 'sub_parent_code_list', 'permissions'));
	}

	public function get_parent_code($reference_type, $reference_no, Request $request){
		if($reference_type == 'SO'){
			$table = 'tabSales Order Item';
		}else{
			$table = 'tabMaterial Request Item';
		}

		return DB::connection('mysql')->table($table)
			->where('parent', $reference_no)
			->where('item_code', 'like', '%'.$request->term.'%')
			->select('item_code as value', 'item_code as id')
			->orderBy('modified', 'desc')->limit(5)->get();
	}

	public function get_sub_parent_code($parent_code, Request $request){
		return DB::connection('mysql')->table('tabBOM')
			->join('tabBOM Item', 'tabBOM.name', 'tabBOM Item.parent')
			->join('tabItem', 'tabBOM Item.item_code', 'tabItem.name')
			->where('tabBOM Item.item_code', 'like', '%'.$request->term.'%')
			->where('tabBOM.item', $parent_code)->whereIn('tabItem.item_classification', ['SA - Sub Assembly', 'HO - Housing', 'FG - Finished Goods'])
			->orderBy('tabItem.modified', 'desc')->distinct()->pluck('tabItem.name');
	}

	public function get_reference_list($reference_type, Request $request){
		if ($reference_type == 'Sales Order') {
			return DB::connection('mysql')->table('tabSales Order')
				->where('name', 'like', '%'.$request->term.'%')
				->where('docstatus', 1)->where('per_delivered', '<', 100)
				->where('company', 'FUMACO Inc.')->select('name as value', 'name as id')
				->orderBy('modified', 'desc')->limit(5)->get();
		}
		
		if ($reference_type == 'Material Request') {
			return DB::connection('mysql')->table('tabMaterial Request')
				->where('name', 'like', '%'.$request->term.'%')
				->where('docstatus', 1)->where('material_request_type', 'Manufacture')
				->where('company', 'FUMACO Inc.')->where('per_ordered', '<', 100)
				->select('name as value', 'name as id')
				->orderBy('modified', 'desc')->limit(5)->get();
		}

		if ($reference_type == 'Item') {
			DB::statement(DB::raw('set @is_stock_item = 0'));
			$bundled_items = DB::connection('mysql')->table('tabProduct Bundle')
				->where('name', 'like', '%'.$request->term.'%')
				->select('name as value', 'name as id', DB::raw('@is_stock_item'));

			return DB::connection('mysql')->table('tabItem')->where('is_stock_item', 1)->where('disabled', 0)
				->where('has_variants', 0)->where('name', 'like', '%'.$request->term.'%')
				->union($bundled_items)
				->select('name as value', 'name as id', 'is_stock_item')->limit(5)->get();
		}
	}

	public function end_scrap_task(Request $request){
        try {
        	if (number_format($request->completed_qty_kg, 12) > number_format($request->balance_qty, 12)) {
				return response()->json(['success' => 0, 'message' => number_format($request->completed_qty_kg, 12) . 'Completed qty cannot be greater than ' . number_format($request->balance_qty, 12)]);
			}

			if ($request->length <= 0) {
				return response()->json(['success' => 0, 'message' => 'Length cannot be equal to 0 mm']);
			}

			if ($request->width <= 0) {
				return response()->json(['success' => 0, 'message' => 'Width cannot be equal to 0 mm']);
			}

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

			$exploded_production_order = explode('-', $request->production_order);
			$scrap_id = $exploded_production_order[1];

			$scrap_qty_after_deduct = $request->balance_qty - $request->completed_qty_kg;

			DB::connection('mysql_mes')->table('scrap')->where('scrap_id', $scrap_id)
				->update([
					'scrap_qty' => $scrap_qty_after_deduct,
					'last_modified_at' => $now->toDateTimeString(),
					'last_modified_by' => Auth::user()->employee_name
				]);


	        $existing_usable_scrap = DB::connection('mysql_mes')->table('usable_scrap')
	        	->where('scrap_id', $scrap_id)->where('length', (float)$request->length)
	        	->where('width', (float)$request->width)->first();

	        $usable_scrap_qty = $request->length * $request->width * $request->thickness * $request->completed_qty;

	        if($existing_usable_scrap){
	        	$data = [
		            'usable_scrap_qty' => $existing_usable_scrap->usable_scrap_qty + $usable_scrap_qty,
		            'last_modified_by' => Auth::user()->employee_name,
		        ];

	        	DB::connection('mysql_mes')->table('usable_scrap')
	        		->where('usable_scrap_id', $existing_usable_scrap->usable_scrap_id)->update($data);
	        }else{
	            $data = [
		            'uom_conversion_id' => 0,
		            'scrap_id' => $scrap_id,
		            'length' => $request->length,
		            'width' => $request->width,
		            'usable_scrap_qty' => $usable_scrap_qty,
		            'created_by' => Auth::user()->employee_name,
		        ];

		        DB::connection('mysql_mes')->table('usable_scrap')->insert($data);
	        }

	        DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $current_task->job_ticket_id)
    			->update(['status' => 'Completed', 'completed_qty' => $request->completed_qty]);

            return response()->json(['success' => 1, 'message' => 'Task updated.']);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
	}
	
	public function get_item_details($id){
		$details = DB::connection('mysql')->table('tabItem')->where('name', $id)->first();
		if (!$details) {
			return response()->json(['success' => 0, 'message' => 'Item ' . $id . ' not found.']);
		}

		return response()->json($details);
	}

	public function get_item_bom($id){
		$details = DB::connection('mysql')->table('tabBOM')->where('item', $id)->orderBy('modified', 'desc')->get();
		if (!$details) {
			return response()->json(['success' => 0, 'message' => 'BOM for ' . $id . ' not found.']);
		}

		return $details;
	}

	public function get_reference_details($reference_type, $reference_no){
		if ($reference_type == 'SO') {
			$details = DB::connection('mysql')->table('tabSales Order')->where('docstatus', 1)->where('per_delivered', '<', 100)
        		->where('company', 'FUMACO Inc.')->where('name', $reference_no)->first();
		}

		if ($reference_type == 'MREQ') {
			$details = DB::connection('mysql')->table('tabMaterial Request')->where('docstatus', 1)->where('material_request_type', 'Manufacture')
        		->where('company', 'FUMACO Inc.')->where('per_ordered', '<', 100)->where('name', $reference_no)->first();
		}

		if (!$details) {
			return response()->json(['success' => 0, 'message' => $id . ' not found.']);
		}

		return response()->json($details);
	}

	public function get_production_order_list(Request $request, $status){
		$user_permitted_operations = DB::connection('mysql_mes')->table('user')
			->join('operation', 'operation.operation_id', 'user.operation_id')
			->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
			->where('module', 'Production')->where('user_access_id', Auth::user()->user_id)
			->select('user.operation_id', 'operation_name')->orderBy('user.operation_id', 'asc')
			->distinct()->pluck('operation_id');

		if ($status == 'Not Started') {
			$q = DB::connection('mysql_mes')->table('production_order')
				->leftJoin('delivery_date', function($join)
				{
					$join->on( DB::raw('IFNULL(production_order.sales_order, production_order.material_request)'), '=', 'delivery_date.reference_no');
					$join->on('production_order.parent_item_code','=','delivery_date.parent_item_code');
				})
				->where(function($q) use ($request) {
					$q->where('production_order.production_order', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.customer', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.sales_order', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.material_request', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.item_code', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.bom_no', 'LIKE', '%'.$request->search_string.'%');
				})
				->whereIn('production_order.operation_id', $user_permitted_operations)
				->where('production_order.status', 'Not Started')
				->select('production_order.*', 'delivery_date.rescheduled_delivery_date')
				->orderBy('production_order.created_at', 'desc')->paginate(10);

			if($request->get_total){
				return ['div' => '#not-started-total', 'total' => number_format($q->total())];
			}

			$production_orders = [];
			foreach ($q as $row) {
				$is_transferred = DB::connection('mysql')->table('tabProduction Order')
					->where('material_transferred_for_manufacturing', '>', 0)
					->where('name', $row->production_order)->where('docstatus', 1)->first();

				if ($is_transferred) {
					$status = 'Material Issued';
				}else{
					$status = 'Material For Issue';
				}

				// get owner of production order
				$owner = explode('@', $row->created_by);
				$owner = ucwords(str_replace('.', ' ', $owner[0]));

				$reference_no = ($row->sales_order) ? $row->sales_order : $row->material_request;
				$production_orders[] = [
					'production_order' => $row->production_order,
					'item_code' => $row->item_code,
					'description' => $row->description,
					'qty_to_manufacture' => $row->qty_to_manufacture,
					'produced_qty' => $row->produced_qty,
					'feedback_qty' => $row->feedback_qty,
					'target_warehouse' => $row->fg_warehouse,
					'operation_id' => $row->operation_id,
					'stock_uom' => $row->stock_uom,
					'reference_no' => $reference_no,
					'delivery_date' => ($row->rescheduled_delivery_date == null)?  $row->delivery_date :$row->rescheduled_delivery_date, // new delivery from delivery table
					'customer' => $row->customer,
					'bom_no' => $row->bom_no,
					'status' => $status,
					'planned_start_date' => $row->planned_start_date,
					'is_scheduled' => $row->is_scheduled,
					'owner' => $owner,
					'operation_id'=>$row->operation_id,
					'parent_item_code'=> $row->parent_item_code,
					'sub_parent_item_code'=> $row->sub_parent_item_code,
					'created_at' =>  Carbon::parse($row->created_at)->format('m-d-Y h:i A')
				];
			}

			return view('reports.tbl_not_started_production', compact('production_orders', 'q'));
		}

		if ($status == 'In Progress') {
			$in_progress_time_logs = DB::connection('mysql_mes')->table('time_logs')
				->join('job_ticket', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
				->where('job_ticket.workstation', '!=', 'Spotwelding')
				->where('time_logs.status', 'In Progress')
				->distinct()->pluck('job_ticket.production_order')->toArray();

			$in_progress_spotwelding_logs = DB::connection('mysql_mes')->table('spotwelding_qty')
				->join('job_ticket', 'spotwelding_qty.job_ticket_id', 'job_ticket.job_ticket_id')
				->where('spotwelding_qty.status', 'In Progress')
				->distinct()->pluck('job_ticket.production_order')->toArray();

			$in_progress_production_orders = array_merge($in_progress_time_logs, $in_progress_spotwelding_logs);

			$q = DB::connection('mysql_mes')->table('production_order')
				->leftJoin('delivery_date', function($join)
				{
					$join->on( DB::raw('IFNULL(production_order.sales_order, production_order.material_request)'), '=', 'delivery_date.reference_no');
					$join->on('production_order.parent_item_code','=','delivery_date.parent_item_code');
				})
				->whereIn('production_order', $in_progress_production_orders)
				->where(function($q) use ($request) {
					$q->where('production_order.production_order', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.customer', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.sales_order', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.material_request', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.item_code', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.bom_no', 'LIKE', '%'.$request->search_string.'%');
				})
				->whereIn('production_order.operation_id', $user_permitted_operations)
				->where('production_order.status', 'In Progress')
				->select('production_order.*', 'delivery_date.rescheduled_delivery_date')
				->orderBy('production_order.created_at', 'desc')->paginate(10);

			if($request->get_total){
				return ['div' => '#in-progress-total', 'total' => number_format($q->total())];
			}

			$production_orders = [];
			foreach ($q as $row) {
				// $actual_start_date = DB::connection('mysql_mes')->table('job_ticket')
				// 	->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
				// 	->where('job_ticket.production_order', $row->production_order)
				// 	->min('time_logs.from_time');

				// get owner of production order
				$owner = explode('@', $row->created_by);
				$owner = ucwords(str_replace('.', ' ', $owner[0]));
				
				$reference_no = ($row->sales_order) ? $row->sales_order : $row->material_request;
				$production_orders[] = [
					'production_order' => $row->production_order,
					'item_code' => $row->item_code,
					'description' => $row->description,
					'qty_to_manufacture' => $row->qty_to_manufacture,
					'produced_qty' => $row->produced_qty,
					'feedback_qty' => $row->feedback_qty,
					'target_warehouse' => $row->fg_warehouse,
					'operation_id' => $row->operation_id,
					'stock_uom' => $row->stock_uom,
					'reference_no' => $reference_no,
					'delivery_date' => ($row->rescheduled_delivery_date == null)?  $row->delivery_date :$row->rescheduled_delivery_date, // new delivery from delivery table
					'customer' => $row->customer,
					'bom_no' => $row->bom_no,
					'status' => $row->status,
					'actual_start_date' => $row->actual_start_date,
					'planned_start_date' => $row->planned_start_date,
					'is_scheduled' => $row->is_scheduled,
					'owner' => $owner,
					'operation_id'=>$row->operation_id,
					'parent_item_code'=> $row->parent_item_code,
					'sub_parent_item_code'=> $row->sub_parent_item_code,
					'created_at' =>  Carbon::parse($row->created_at)->format('m-d-Y h:i A')
				];
			}
				
			return view('reports.tbl_in_progress_production', compact('production_orders', 'q'));
		}

		if ($status == 'Task Queue') {
			$on_going_time_logs = DB::connection('mysql_mes')->table('time_logs')
				->where('status', 'In Progress')->distinct()->pluck('job_ticket_id')->toArray();

			$on_going_spotwelding = DB::connection('mysql_mes')->table('spotwelding_qty')
				->where('status', 'In Progress')->distinct()->pluck('job_ticket_id')->toArray();

			$on_going_tasks = array_merge($on_going_time_logs, $on_going_spotwelding);

			$pending_production_orders = DB::connection('mysql_mes')->table('job_ticket')
				->whereIn('status', ['In Progress', 'Pending'])
				->whereNotIn('job_ticket_id', $on_going_tasks)
				->distinct()
				->pluck('production_order');

			$q = DB::connection('mysql_mes')->table('production_order')
				->leftJoin('delivery_date', function($join)
				{
					$join->on( DB::raw('IFNULL(production_order.sales_order, production_order.material_request)'), '=', 'delivery_date.reference_no');
					$join->on('production_order.parent_item_code','=','delivery_date.parent_item_code');
				})
				->whereIn('production_order', $pending_production_orders)
				->where(function($q) use ($request) {
					$q->where('production_order.production_order', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.customer', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.sales_order', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.material_request', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.item_code', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.bom_no', 'LIKE', '%'.$request->search_string.'%');
				})
				->whereIn('production_order.operation_id', $user_permitted_operations)
				->whereRaw('production_order.qty_to_manufacture > feedback_qty')
				->where('production_order.status', 'In Progress')
				->select('production_order.*', 'delivery_date.rescheduled_delivery_date')
				->orderBy('production_order.created_at', 'desc')->paginate(10);
			
			if($request->get_total){
				return ['div' => '#task-queue-total', 'total' => $q->total()];
			}

			$production_orders = [];
			foreach ($q as $row) {
				// $actual_start_date = DB::connection('mysql_mes')->table('job_ticket')
				// 	->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
				// 	->where('job_ticket.production_order', $row->production_order)
				// 	->min('time_logs.from_time');

				// get owner of production order
				$owner = explode('@', $row->created_by);
				$owner = ucwords(str_replace('.', ' ', $owner[0]));
				
				$reference_no = ($row->sales_order) ? $row->sales_order : $row->material_request;
				$production_orders[] = [
					'production_order' => $row->production_order,
					'item_code' => $row->item_code,
					'description' => $row->description,
					'qty_to_manufacture' => $row->qty_to_manufacture,
					'produced_qty' => $row->produced_qty,
					'feedback_qty' => $row->feedback_qty,
					'target_warehouse' => $row->fg_warehouse,
					'operation_id' => $row->operation_id,
					'stock_uom' => $row->stock_uom,
					'reference_no' => $reference_no,
					'delivery_date' => ($row->rescheduled_delivery_date == null)?  $row->delivery_date :$row->rescheduled_delivery_date, // new delivery from delivery table
					'customer' => $row->customer,
					'bom_no' => $row->bom_no,
					'status' => 'On Queue',
					'actual_start_date' => $row->actual_start_date,
					'planned_start_date' => $row->planned_start_date,
					'is_scheduled' => $row->is_scheduled,
					'owner' => $owner,
					'operation_id'=>$row->operation_id,
					'parent_item_code'=> $row->parent_item_code,
					'sub_parent_item_code'=> $row->sub_parent_item_code,
					'created_at' =>  Carbon::parse($row->created_at)->format('m-d-Y h:i A')
				];
			}

			return view('reports.tbl_task_queue_production', compact('production_orders', 'q'));
		}

		if ($status == 'Cancelled') {
			$q = DB::connection('mysql_mes')->table('production_order')->where('status', 'Cancelled')
				->leftJoin('delivery_date', function($join)
				{
					$join->on( DB::raw('IFNULL(production_order.sales_order, production_order.material_request)'), '=', 'delivery_date.reference_no');
					$join->on('production_order.parent_item_code','=','delivery_date.parent_item_code');
				})
				->whereIn('production_order.operation_id', $user_permitted_operations)
				->where(function($q) use ($request) {
					$q->where('production_order.production_order', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.customer', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.sales_order', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.material_request', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.item_code', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.bom_no', 'LIKE', '%'.$request->search_string.'%');
				})
				->select('production_order.*', 'delivery_date.rescheduled_delivery_date')
				->orderBy('production_order.created_at', 'desc')->paginate(10);


			if($request->get_total){
				return ['div' => '#cancelled-total', 'total' => number_format($q->total())];
			}

			$production_orders = [];
			foreach ($q as $row) {
				$reference_no = ($row->sales_order) ? $row->sales_order : $row->material_request;
				// get owner of production order
				$owner = explode('@', $row->created_by);
				$owner = ucwords(str_replace('.', ' ', $owner[0]));

				$production_orders[] = [
					'production_order' => $row->production_order,
					'item_code' => $row->item_code,
					'description' => $row->description,
					'qty_to_manufacture' => $row->qty_to_manufacture,
					'stock_uom' => $row->stock_uom,
					'reference_no' => $reference_no,
					'delivery_date' => ($row->rescheduled_delivery_date == null)?  $row->delivery_date :$row->rescheduled_delivery_date, // new delivery from delivery table
					'customer' => $row->customer,
					'bom_no' => $row->bom_no,
					'status' => $row->status,
					'planned_start_date' => $row->planned_start_date,
					'is_scheduled' => $row->is_scheduled,
					'owner' => $owner,
					'created_at' =>  Carbon::parse($row->created_at)->format('m-d-Y h:i A')
				];
			}

			return view('reports.tbl_cancelled_production', compact('production_orders', 'q'));
		}

		if($status == 'Awaiting Feedback'){
			$user_permitted_operations = DB::connection('mysql_mes')->table('user')
				->join('operation', 'operation.operation_id', 'user.operation_id')
				->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
				->where('module', 'Production')->where('user_access_id', Auth::user()->user_id)
				->select('user.operation_id', 'operation_name')->orderBy('user.operation_id', 'asc')
				->distinct()->get();

			$user_permitted_operation_id = collect($user_permitted_operations)->pluck('operation_id');
			$user_permitted_operation_names = collect($user_permitted_operations)->pluck('operation_name');
			
			$permitted_workstation = DB::connection('mysql_mes')->table('workstation')
				->whereIn('operation_id', $user_permitted_operation_id)->distinct()
				->pluck('workstation_name')->toArray();

			if(in_array('Painting', $user_permitted_operation_names->toArray())){
				array_push($permitted_workstation, ['Painting']);
			}
			
			$jt_production_orders = DB::connection('mysql_mes')->table('job_ticket')
				->whereIn('workstation', $permitted_workstation)
				->whereIn('status', ['In Progress', 'Completed'])->distinct()->pluck('production_order');

			$q = DB::connection('mysql_mes')->table('production_order AS po')
				->leftJoin('delivery_date', function($join)
				{
					$join->on( DB::raw('IFNULL(po.sales_order, po.material_request)'), '=', 'delivery_date.reference_no');
					$join->on('po.parent_item_code','=','delivery_date.parent_item_code');
				})
				->whereIn('po.production_order', $jt_production_orders)
				->whereNotIn('po.status', ['Cancelled'])
				->where(function($q) use ($request) {
			       	$q->where('po.production_order', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('po.customer', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('po.sales_order', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('po.material_request', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('po.item_code', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('po.bom_no', 'LIKE', '%'.$request->search_string.'%');
				})
				->where('po.produced_qty', '>', 0)
				->whereRaw('po.produced_qty > feedback_qty')
				->select('po.*', 'delivery_date.rescheduled_delivery_date')
				->paginate(10);

			if($request->get_total){
				return ['div' => '#awaiting-feedback-total', 'total' => number_format($q->total())];
			}

			$production_orders = [];
			foreach ($q as $row) {
				$manufacture_entry = DB::connection('mysql')->table('tabStock Entry')
					->where('production_order', $row->production_order)->where('docstatus', 1)
					->orderBy('posting_date', 'desc')->orderBy('posting_time', 'desc')
					->where('purpose', 'Manufacture')->first();

				$manufacture_entries = DB::connection('mysql')->table('tabStock Entry')
					->where('production_order', $row->production_order)->where('docstatus', 1)
					->where('purpose', 'Manufacture')->pluck('name');

				$status = ($row->qty_to_manufacture == $row->produced_qty) ? 'For Feedback' : 'For Partial Feedback';
				
				if ($row->feedback_qty >= $row->qty_to_manufacture) {
					$status = 'Feedbacked';
				}else{
					if ($manufacture_entry) {
						$status = 'Partially Feedbacked';
					}
				}

				$is_transferred = DB::connection('mysql')->table('tabProduction Order')
					->where('material_transferred_for_manufacturing', '>', 0)
					->where('name', $row->production_order)->where('docstatus', 1)->first();

				if ($is_transferred) {
					$status = 'Material Issued';
				}else{
					$status = 'Material For Issue';
				}

				$time_logs_qry = DB::connection('mysql_mes')->table('job_ticket')
					->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
					->where('job_ticket.production_order', $row->production_order)->whereIn('job_ticket.status', ['Completed', 'In Progress'])
					->get();

				$from_time = collect($time_logs_qry)->min('from_time');
				$to_time = collect($time_logs_qry)->max('to_time');

				$actual_start_date = Carbon::parse($from_time);
				$actual_end_date = Carbon::parse($to_time);

				$days = $actual_start_date->diffInDays($actual_end_date);
				$hours = $actual_start_date->copy()->addDays($days)->diffInHours($actual_end_date);
				$minutes = $actual_start_date->copy()->addDays($days)->addHours($hours)->diffInMinutes($actual_end_date);
				$seconds = $actual_start_date->copy()->addDays($days)->addHours($hours)->addMinutes($minutes)->diffInSeconds($actual_end_date);
				$dur_days = ($days > 0) ? $days .'d' : null;
				$dur_hours = ($hours > 0) ? $hours .'h' : null;
				$dur_minutes = ($minutes > 0) ? $minutes .'m' : null;
				$dur_seconds = ($seconds > 0) ? $seconds .'s' : null;

				// get owner of production order
				$owner = explode('@', $row->created_by);
				$owner = ucwords(str_replace('.', ' ', $owner[0]));
				
				$production_orders[] = [
					'name' => $row->production_order,
					'operation_id' => $row->operation_id,
					'sales_order_no' => $row->sales_order,
					'material_request' => $row->material_request,
					'bom' => $row->bom_no,
					'customer' => $row->customer,
					'item_code' => $row->item_code,
					'description' => $row->description,
					'qty' => $row->qty_to_manufacture,
					'produced_qty' => $row->produced_qty,
					'feedback_qty' => $row->feedback_qty,
					'stock_uom' => $row->stock_uom,
					'delivery_date' => ($row->rescheduled_delivery_date == null)?  $row->delivery_date :$row->rescheduled_delivery_date, // new delivery from delivery table
					'completed_qty' => 0,
					'status' => $status,
					'bom_no' => $row->bom_no,
					'ste_manufacture' => ($manufacture_entry) ? $manufacture_entry->name : '',
					'target_warehouse' => $row->fg_warehouse,
					'ste_entries' => $manufacture_entries,
					'actual_start_date' => ($from_time) ? Carbon::parse($from_time)->format('m-d-Y h:i A') : '--',
					'actual_end_date' => ($from_time) ? Carbon::parse($to_time)->format('m-d-Y h:i A') : '--',
					'duration' => $dur_days .' '. $dur_hours . ' '. $dur_minutes . ' '. $dur_seconds,
					'actual_end_date_1' => $to_time,
					'planned_start_date' => Carbon::parse($row->planned_start_date)->format('m-d-Y'),
					'owner' => $owner,
					'operation_id'=>$row->operation_id,
					'parent_item_code'=> $row->parent_item_code,
					'sub_parent_item_code'=> $row->sub_parent_item_code,
					'production_order' => $row->production_order,
					'created_at' =>  Carbon::parse($row->created_at)->format('m-d-Y h:i A')
				];
			}

			$production_order_list = collect($production_orders)->sortByDesc('actual_end_date_1')->toArray();

  			return view('reports.tbl_feedback_ready_production_order', compact('production_order_list', 'q'));
		}

		if ($status == 'Completed') {
			$mes_production_orders = DB::connection('mysql_mes')->table('production_order')
				->whereIn('status', ['In Progress', 'Completed'])->pluck('production_order');
			
			$erp_completed_production_orders = DB::connection('mysql')->table('tabProduction Order')
				->where('status', 'Completed')->whereIn('name', $mes_production_orders)->pluck('name')->toArray();

				$q = DB::connection('mysql_mes')->table('production_order')
				->leftJoin('delivery_date', function($join)
            	{
                    $join->on( DB::raw('IFNULL(production_order.sales_order, production_order.material_request)'), '=', 'delivery_date.reference_no');
                    $join->on('production_order.parent_item_code','=','delivery_date.parent_item_code');
                })
				->whereIn('production_order.production_order', $erp_completed_production_orders)
				->where(function($q) use ($request) {
					$q->where('production_order.production_order', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.customer', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.sales_order', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.material_request', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.item_code', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('production_order.bom_no', 'LIKE', '%'.$request->search_string.'%');
				})
				->whereIn('production_order.operation_id', $user_permitted_operations)
				->select('production_order.*', 'delivery_date.rescheduled_delivery_date')
				->orderBy('production_order.created_at', 'desc')->paginate(10);

			if($request->get_total){
				return ['div' => '#completed-total', 'total' => number_format($q->total())];
			}

			$production_orders = [];
			foreach ($q as $row) {
				$manufacture_entry = DB::connection('mysql')->table('tabStock Entry')
					->where('production_order', $row->production_order)->where('docstatus', 1)
					->orderBy('posting_date', 'desc')->orderBy('posting_time', 'desc')
					->where('purpose', 'Manufacture')->first();

				$manufacture_entries = DB::connection('mysql')->table('tabStock Entry')
					->where('production_order', $row->production_order)->where('docstatus', 1)
					->where('purpose', 'Manufacture')->pluck('name');

				$status = ($row->qty_to_manufacture == $row->produced_qty) ? 'For Feedback' : 'For Partial Feedback';
				
				if ($row->feedback_qty >= $row->qty_to_manufacture) {
					$status = 'Feedbacked';
				}else{
					if ($manufacture_entry) {
						$status = 'Partially Feedbacked';
					}
				}

				$time_logs_qry = DB::connection('mysql_mes')->table('job_ticket')
					->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
					->where('job_ticket.production_order', $row->production_order)->where('job_ticket.status', 'Completed')
					->get();

				$from_time = collect($time_logs_qry)->min('from_time');
				$to_time = collect($time_logs_qry)->max('to_time');

				$actual_start_date = Carbon::parse($from_time);
				$actual_end_date = Carbon::parse($to_time);

				$days = $actual_start_date->diffInDays($actual_end_date);
				$hours = $actual_start_date->copy()->addDays($days)->diffInHours($actual_end_date);
				$minutes = $actual_start_date->copy()->addDays($days)->addHours($hours)->diffInMinutes($actual_end_date);
				$seconds = $actual_start_date->copy()->addDays($days)->addHours($hours)->addMinutes($minutes)->diffInSeconds($actual_end_date);
				$dur_days = ($days > 0) ? $days .'d' : null;
				$dur_hours = ($hours > 0) ? $hours .'h' : null;
				$dur_minutes = ($minutes > 0) ? $minutes .'m' : null;
				$dur_seconds = ($seconds > 0) ? $seconds .'s' : null;

				// get owner of production order
				$owner = explode('@', $row->created_by);
				$owner = ucwords(str_replace('.', ' ', $owner[0]));
				
				$production_orders[] = [
					'name' => $row->production_order,
					'operation_id' => $row->operation_id,
					'sales_order_no' => $row->sales_order,
					'material_request' => $row->material_request,
					'bom' => $row->bom_no,
					'customer' => $row->customer,
					'item_code' => $row->item_code,
					'description' => $row->description,
					'qty' => $row->qty_to_manufacture,
					'produced_qty' => $row->produced_qty,
					'feedback_qty' => $row->feedback_qty,
					'stock_uom' => $row->stock_uom,
					'delivery_date' => ($row->rescheduled_delivery_date == null)?  $row->delivery_date :$row->rescheduled_delivery_date, // new delivery from delivery table
					'completed_qty' => 0,
					'status' => $status,
					'ste_manufacture' => ($manufacture_entry) ? $manufacture_entry->name : '',
					'count_ste_entries'=> count($manufacture_entries),
					'target_warehouse' => $row->fg_warehouse,
					'ste_entries' => $manufacture_entries,
					'actual_start_date' => ($from_time) ? Carbon::parse($from_time)->format('m-d-Y h:i A') : '--',
					'actual_end_date' => ($from_time) ? Carbon::parse($to_time)->format('m-d-Y h:i A') : '--',
					'duration' => $dur_days .' '. $dur_hours . ' '. $dur_minutes . ' '. $dur_seconds,
					'actual_end_date_1' => $to_time,
					'owner' => $owner,
					'operation_id'=>$row->operation_id,
					'parent_item_code'=> $row->parent_item_code,
					'sub_parent_item_code'=> $row->sub_parent_item_code,
					'production_order' => $row->production_order,
					'created_at' =>  Carbon::parse($row->created_at)->format('m-d-Y h:i A')
				];
			}

			$production_order_list = collect($production_orders)->sortByDesc('actual_end_date_1')->toArray();

			return view('reports.tbl_completed_production', compact('production_order_list', 'q'));
		}
	}

	public function get_for_feedback_production(Request $request){
		try {
			$user_permitted_operations = DB::connection('mysql_mes')->table('user')
				->join('operation', 'operation.operation_id', 'user.operation_id')
				->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
				->where('module', 'Production')->where('user_access_id', Auth::user()->user_id)
				->select('user.operation_id', 'operation_name')->orderBy('user.operation_id', 'asc')
				->distinct()->get();

			$user_permitted_operation_id = collect($user_permitted_operations)->pluck('operation_id');
			$user_permitted_operation_names = collect($user_permitted_operations)->pluck('operation_name');
			
			$permitted_workstation = DB::connection('mysql_mes')->table('workstation')
				->whereIn('operation_id', $user_permitted_operation_id)->distinct()
				->pluck('workstation_name')->toArray();

			if(in_array('Painting', $user_permitted_operation_names->toArray())){
				array_push($permitted_workstation, ['Painting']);
			}
			
			$jt_production_orders = DB::connection('mysql_mes')->table('job_ticket')
				->whereIn('workstation', $permitted_workstation)
				->where('status', '=', 'Completed')->distinct()->pluck('production_order');

			$q = DB::connection('mysql_mes')->table('production_order AS po')
				->whereIn('production_order', $jt_production_orders)
				->whereNotIn('status', ['Cancelled'])
				->where(function($q) use ($request) {
			       	$q->where('production_order', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('customer', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('sales_order', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('material_request', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('item_code', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('bom_no', 'LIKE', '%'.$request->search_string.'%');
				})
				->where('produced_qty', '>', 0)
				->whereRaw('produced_qty > feedback_qty')
				->paginate(10);

			if($request->get_total){
				return ['div' => '#awaiting-feedback-total', 'total' => $q->total()];
			}

			$production_orders = [];
			foreach ($q as $row) {
				$manufacture_entry = DB::connection('mysql')->table('tabStock Entry')
					->where('production_order', $row->production_order)->where('docstatus', 1)
					->orderBy('posting_date', 'desc')->orderBy('posting_time', 'desc')
					->where('purpose', 'Manufacture')->first();

				$manufacture_entries = DB::connection('mysql')->table('tabStock Entry')
					->where('production_order', $row->production_order)->where('docstatus', 1)
					->where('purpose', 'Manufacture')->pluck('name');

				$status = ($row->qty_to_manufacture == $row->produced_qty) ? 'For Feedback' : 'For Partial Feedback';
				
				if ($row->feedback_qty >= $row->qty_to_manufacture) {
					$status = 'Feedbacked';
				}else{
					if ($manufacture_entry) {
						$status = 'Partially Feedbacked';
					}
				}

				$is_transferred = DB::connection('mysql')->table('tabProduction Order')
					->where('material_transferred_for_manufacturing', '>', 0)
					->where('name', $row->production_order)->where('docstatus', 1)->first();

				if ($is_transferred) {
					$status = 'Material Issued';
				}else{
					$status = 'Material For Issue';
				}

				$time_logs_qry = DB::connection('mysql_mes')->table('job_ticket')
					->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
					->where('job_ticket.production_order', $row->production_order)->where('job_ticket.status', 'Completed')
					->get();

				$from_time = collect($time_logs_qry)->min('from_time');
				$to_time = collect($time_logs_qry)->max('to_time');

				$actual_start_date = Carbon::parse($from_time);
				$actual_end_date = Carbon::parse($to_time);

				$days = $actual_start_date->diffInDays($actual_end_date);
				$hours = $actual_start_date->copy()->addDays($days)->diffInHours($actual_end_date);
				$minutes = $actual_start_date->copy()->addDays($days)->addHours($hours)->diffInMinutes($actual_end_date);
				$seconds = $actual_start_date->copy()->addDays($days)->addHours($hours)->addMinutes($minutes)->diffInSeconds($actual_end_date);
				$dur_days = ($days > 0) ? $days .'d' : null;
				$dur_hours = ($hours > 0) ? $hours .'h' : null;
				$dur_minutes = ($minutes > 0) ? $minutes .'m' : null;
				$dur_seconds = ($seconds > 0) ? $seconds .'s' : null;

				// get owner of production order
				$owner = explode('@', $row->created_by);
				$owner = ucwords(str_replace('.', ' ', $owner[0]));
				
				$production_orders[] = [
					'name' => $row->production_order,
					'operation_id' => $row->operation_id,
					'sales_order_no' => $row->sales_order,
					'material_request' => $row->material_request,
					'bom' => $row->bom_no,
					'customer' => $row->customer,
					'item_code' => $row->item_code,
					'description' => $row->description,
					'qty' => $row->qty_to_manufacture,
					'produced_qty' => $row->produced_qty,
					'feedback_qty' => $row->feedback_qty,
					'stock_uom' => $row->stock_uom,
					'delivery_date' => $row->delivery_date,
					'completed_qty' => 0,
					'status' => $status,
					'bom_no' => $row->bom_no,
					'ste_manufacture' => ($manufacture_entry) ? $manufacture_entry->name : '',
					'target_warehouse' => $row->fg_warehouse,
					'ste_entries' => $manufacture_entries,
					'actual_start_date' => ($from_time) ? Carbon::parse($from_time)->format('m-d-Y h:i A') : '--',
					'actual_end_date' => ($from_time) ? Carbon::parse($to_time)->format('m-d-Y h:i A') : '--',
					'duration' => $dur_days .' '. $dur_hours . ' '. $dur_minutes . ' '. $dur_seconds,
					'actual_end_date_1' => $to_time,
					'planned_start_date' => Carbon::parse($row->planned_start_date)->format('m-d-Y'),
					'owner' => $owner,
					'created_at' =>  Carbon::parse($row->created_at)->format('m-d-Y h:i A')
				];
			}

			$production_order_list = collect($production_orders)->sortByDesc('actual_end_date_1')->toArray();

  			return view('reports.tbl_feedback_ready_production_order', compact('production_order_list', 'q'));
		} catch (Exception $e) {
			return response()->json(["error" => $e->getMessage()]);
		}
	}

	public function reorderProdOrder(Request $request, $id){
    	try {
			$val = [];
			if ($request->positions) {
				if($id == "0"){
					foreach ($request->positions as $value) {
						$name = $value[0];
						$position = $value[1];
						$schedule = $value[2];
						$prod = $value[3];
						$val_sched = [
							'planned_start_date' => ($schedule == 'unscheduled') ? null : $schedule,
						];
						$val_order_no=[
							'sequence' => $position,
						];
							DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $name)->where('workstation','Painting')->update($val_order_no);

							if(DB::connection('mysql_mes')->table('job_ticket as jt')
							->join('time_logs as tl', 'jt.job_ticket_id','tl.job_ticket_id')
							->where('jt.production_order', $name)
							->where('tl.status', "In Progress")
							->select('tl.status as stat')
							->exists()){

							}else{
								DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $name)->where('workstation','Painting')->update($val_sched);

							}

					}
				}else{
				
					foreach ($request->positions as $value) {
						$name = $value[0];
						$position = $value[1];
						$schedule = $value[2];
						$prod = $value[3];
						$val_mes = [
							'planned_start_date' => ($schedule == 'unscheduled') ? null : $schedule,
							'is_scheduled' => ($schedule == 'unscheduled') ? 0 : 1
						];
		
						$val_erp = [
							'planned_start_date' => ($schedule == 'unscheduled') ? null : $schedule,
							'scheduled' => ($schedule == 'unscheduled') ? 0 : 1
						];
						$val_order_no=[
							'order_no' => $position 
						];
						DB::table('tabProduction Order')->where('name', $name)->update($val_order_no);
						DB::connection('mysql_mes')->table('production_order')->where('production_order', $name)->update($val_order_no);

						if(DB::connection('mysql_mes')->table('job_ticket as jt')
							->join('spotwelding_qty as spotpart', 'spotpart.job_ticket_id','jt.job_ticket_id')
							->where('jt.production_order', $name)
							->where('spotpart.status', "In Progress")
							->exists()){
								
								return response()->json(['success' => 0, 'message' => 'error.']);


						}else{
							if(DB::connection('mysql_mes')->table('job_ticket as jt')
							->join('time_logs as tl', 'jt.job_ticket_id','tl.job_ticket_id')
							->where('jt.production_order', $name)
							->where('tl.status', "In Progress")
							->exists()){
								return response()->json(['success' => 0, 'message' => 'error.']);

							}else{
								DB::table('tabProduction Order')->where('name', $prod)->update($val_erp);
								DB::connection('mysql_mes')->table('production_order')->where('production_order', $name)->update($val_mes);
								DB::table('tabProduction Order')->where('name', $name)->update($val_order_no);
								DB::connection('mysql_mes')->table('production_order')->where('production_order', $name)->update($val_order_no);
							}
						}
						
					}
				}
			}
    		
    	} catch (Exception $e) {
    		return response()->json(["error" => $e->getMessage()]);
    	}	
	}

    public function update_production_task_schedules(Request $request){
		$now = Carbon::now();
		$production_order_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)->first();
		if (!$production_order_details) {
			return response()->json(['success' => 0, 'message' => 'Production Order ' . $request->production_order . ' not found.']);
		}

		if ($production_order_details->status != 'Completed') {
			
			$current_schedule =  Carbon::parse($production_order_details->planned_start_date);
			$new_schedule = Carbon::parse($request->planned_start_date);
			$diff_in_days = $current_schedule->diffInDays($new_schedule);
			
			$val_mes = [
				'planned_start_date' => $new_schedule,
			];

			DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)->update($val_mes);
			
			$tasks = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->production_order)->get();
			foreach ($tasks as $row) {
				if ($row->workstation != 'Painting') {
					$current_planned_start_date = ($row->planned_start_date) ? $row->planned_start_date : $production_order_details->planned_start_date;

					if($new_schedule->toDateTimeString() > $current_schedule->toDateTimeString()){
						$new_planned_start_date = Carbon::parse($current_planned_start_date)->addDays($diff_in_days);
					}
					
					if($new_schedule->toDateTimeString() < $current_schedule->toDateTimeString()){
						$new_planned_start_date = Carbon::parse($current_planned_start_date)->subDays($diff_in_days);
					}

					$values = [
						'last_modified_by' => Auth::user()->employee_name,
						'last_modified_at' => $now->toDateTimeString(),
						'planned_start_date' => $new_planned_start_date->toDateTimeString(),
					];

					DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $row->job_ticket_id)->update($values);
				}
			}
		}
	}

	public function update_production_order_schedule(Request $request){
		$now = Carbon::now();
		$production_order_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)->first();
		if (!$production_order_details) {
			return response()->json(['success' => 0, 'message' => 'Production Order ' . $request->production_order . ' not found.']);
		}

		$query = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->production_order)->get();

		$planned_start_date = collect($query)->min('planned_start_date');
		$planned_end_date = collect($query)->max('planned_start_date');

		$values = [
			'last_modified_by' => Auth::user()->employee_name,
			'last_modified_at' => $now->toDateTimeString(),
			'planned_start_date' => $planned_start_date,
			'planned_end_date' => $planned_end_date,
		];

		DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)->update($values);
	}

	public function production_schedule_module($operation_id){
		$permissions = $this->get_user_permitted_operation();

		$primary_id=$operation_id;
		if($primary_id == "1"){
			$operation_name_text="Fabrication";
		}else{
			$operation_name_text="Assembly";
		}

		$operation_name_text = ($operation_id < 1) ? 'Painting' : $operation_name_text;

		$mes_user_operations = DB::connection('mysql_mes')->table('user')
			->join('operation', 'operation.operation_id', 'user.operation_id')
			->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
			->where('module', 'Production')
			->where('user_access_id', Auth::user()->user_id)->pluck('operation_name')->toArray();

		if($operation_id < 1){
			$unscheduled = $this->get_painting_schedules($primary_id)['unscheduled'];
			$scheduled = $this->get_painting_schedules($primary_id)['scheduled'];
			$filters = $this->get_painting_schedules($primary_id)['filters'];
		}else{
			$unscheduled = $this->productionKanban($primary_id)['unscheduled'];
			$scheduled = $this->productionKanban($primary_id)['scheduled'];
			$filters = $this->productionKanban($primary_id)['filters'];
		}

		return view('production_kanban', compact('operation_name_text','primary_id','unscheduled', 'scheduled', 'mes_user_operations', 'permissions', 'filters'));

	}
	public function productionKanban($operation_id){
		   $unscheduled_prod = DB::connection('mysql_mes')->table('production_order')
		   ->leftJoin('delivery_date', function($join)
            {
                $join->on( DB::raw('IFNULL(production_order.sales_order, production_order.material_request)'), '=', 'delivery_date.reference_no');
                $join->on('production_order.parent_item_code','=','delivery_date.parent_item_code');
            })
			->whereNotIn('production_order.status', ['Stopped', 'Cancelled'])
			->where('production_order.feedback_qty',0)
			->where('production_order.is_scheduled', 0)
			->where("production_order.operation_id", $operation_id)
			->select('production_order.*', 'delivery_date.rescheduled_delivery_date')
			->orderBy('production_order.sales_order', 'desc')
			->orderBy('production_order.material_request', 'desc')->get();

    	$unscheduled = [];
    	$max = [];
    	foreach ($unscheduled_prod as $row) {
			$stripfromcomma =strtok($row->description, ",");
			$unscheduled[] = [
				'id' => $row->production_order,
				'status' => $row->status,
				'name' => $row->production_order,
				'order_no' => $row->order_no,
				'customer' => $row->customer,
				'delivery_date' => ($row->rescheduled_delivery_date == null)? $row->delivery_date: $row->rescheduled_delivery_date, //show reschedule delivery date 
				'production_item' => $row->item_code,
				'production_order' => $row->production_order,
				'description' => $row->description,
	    		'parts_category' => $row->parts_category,
	    		'parent_item_code' => $row->parent_item_code,
	    		'strip' => $stripfromcomma,
				'qty' => $row->qty_to_manufacture,
				'stock_uom' => $row->stock_uom,
				'produced_qty' => $row->produced_qty,
				'classification' => $row->classification,
				'withdrawal_slip_print' => $row->withdrawal_slip_print,
				'job_ticket_print' => $row->job_ticket_print,
				'sales_order' =>($row->sales_order == null) ? $row->material_request: $row->sales_order,
				'batch' => null,
				'process_stat'=> $this->material_status_stockentry($row->production_order, $row->status, $row->qty_to_manufacture,$row->feedback_qty, $row->produced_qty),
			];
		}

		$period = CarbonPeriod::create(now()->subDays(1), now()->addDays(6));

		$customers = array_column($unscheduled, 'customer');
		$reference_nos = array_column($unscheduled, 'sales_order');
		$parent_items = array_column($unscheduled, 'parent_item_code');

		// Iterate over the period->subDays(1)
		$scheduled = [];
		foreach ($period as $date) {
			$orders = $this->getScheduledProdOrders($date->format('Y-m-d'), $operation_id);

			$customers = array_merge($customers, array_column($orders, 'customer'));
			$reference_nos = array_merge($reference_nos, array_column($orders, 'sales_order'));
			$parent_items = array_merge($parent_items, array_column($orders, 'parent_item_code'));

			$scheduled[] = [
				'shift'=> [],
				'schedule' => $date->format('Y-m-d'),
				'duplicate_item_code' => 0,
				'orders' => $orders,
			];
		}

		$filters = [
			'customers' => array_unique($customers),
			'reference_nos' => array_unique($reference_nos),
			'parent_items' => array_unique($parent_items),
		];

		return [
			'unscheduled' => $unscheduled,
			'scheduled' => $scheduled,
			'filters' => $filters,
		];
		
	}

	public function get_painting_schedules($operation_id){
		$jobtickets_production=DB::connection('mysql_mes')->table('job_ticket as jt')
			->join('production_order as pro','pro.production_order', 'jt.production_order')
			->leftJoin('delivery_date', function($join){
				$join->on( DB::raw('IFNULL(pro.sales_order, pro.material_request)'), '=', 'delivery_date.reference_no');
				$join->on('pro.parent_item_code','=','delivery_date.parent_item_code');
			})
			->where('jt.planned_start_date', null)->where('pro.status', '!=', 'Cancelled')
			->whereRaw('pro.qty_to_manufacture > pro.feedback_qty')
			->where('jt.workstation', 'Painting')
			->select('delivery_date.rescheduled_delivery_date','pro.production_order', 'jt.workstation', 'pro.customer', 'pro.delivery_date','pro.description', 'pro.qty_to_manufacture','pro.item_code','pro.stock_uom','pro.project','pro.classification','pro.parts_category', 'pro.sales_order', 'pro.material_request', 'pro.produced_qty', 'pro.job_ticket_print','pro.withdrawal_slip_print', 'pro.parent_item_code', 'pro.status','jt.sequence', 'pro.feedback_qty')
			->distinct('delivery_date.rescheduled_delivery_date','pro.production_order','pro.customer', 'pro.delivery_date','pro.description', 'pro.qty_to_manufacture','pro.item_code','pro.stock_uom','pro.project','pro.classification','pro.parts_category', 'pro.sales_order', 'pro.material_request',  'pro.produced_qty','pro.job_ticket_print','pro.withdrawal_slip_print', 'pro.parent_item_code', 'pro.status','jt.sequence', 'pro.feedback_qty')
			->whereNotIn('pro.status', ['Completed', 'Stopped', 'Cancelled'])
			->orderBy('pro.created_at', 'desc')->get();

		$unscheduled = [];
		foreach ($jobtickets_production as $row) {
			$jt = DB::connection('mysql_mes')->table('job_ticket as jt')
				->where('production_order',  $row->production_order)->get();
			$prod_stat = DB::connection('mysql_mes')->table('production_order as prod')
			->where('production_order',  $row->production_order)->first();
			$total_process = collect($jt)->where('workstation','Painting')->count();
			$total_pending = collect($jt)->where('workstation','Painting')->where('status', 'Pending')->count();
			$total_inprogress = collect($jt)->where('workstation','Painting')->where('status', '!=', 'Completed')->count();

			if ($total_process == $total_pending) {
				$status= 'Not Started';
			}else{
				if ($total_inprogress > 0) {
					$status= 'In Progress';
				}else{
					$status= 'Completed';
				}
			}
							
			$stripfromcomma =strtok($row->description, ",");
			
			$unscheduled[] = [
				'id' => $row->production_order,
				'status' => $status,
				'name' => $row->production_order,
				'customer' => $row->customer,
				'delivery_date' => ($row->rescheduled_delivery_date == null)? $row->delivery_date: $row->rescheduled_delivery_date, // show reschedule delivery date/ existing delivery date based on the validation 
				'production_item' => $row->item_code,
				'production_order' => $row->production_order,
				'description' => $row->description,
				'strip' => $stripfromcomma,
				'parts_category' => $row->parts_category,
				'qty' => $row->qty_to_manufacture,
				'parent_item_code' => $row->parent_item_code,
				'stock_uom' => $row->stock_uom,
				'produced_qty'=> $row->produced_qty,
				'sales_order' =>($row->sales_order == null) ? $row->material_request:$row->sales_order,
				'classification' => $row->classification,
				'withdrawal_slip_print' => $row->withdrawal_slip_print,
				'job_ticket_print' => $row->job_ticket_print,
				'prod_status' => $row->status,
				'process_stat'=> $this->material_status_stockentry($row->production_order, $prod_stat->status,  $row->qty_to_manufacture,$row->feedback_qty, $row->produced_qty),
				'order_no' =>$row->sequence,
			];
		}

		$period = CarbonPeriod::create(now()->subDays(1), now()->addDays(6));

		$customers = array_column($unscheduled, 'customer');
		$reference_nos = array_column($unscheduled, 'sales_order');
		$parent_items = array_column($unscheduled, 'parent_item_code');

		$scheduled = [];
		foreach ($period as $date) {
			$orders = $this->get_scheduled_painting($date->format('Y-m-d'));

			$customers = array_merge($customers, array_column($orders, 'customer'));
			$reference_nos = array_merge($reference_nos, array_column($orders, 'sales_order'));
			$parent_items = array_merge($parent_items, array_column($orders, 'parent_item_code'));

			// $shift_sched = $this->get_prod_shift_sched($date->format('Y-m-d'), $operation_id);
			// $total_seconds= collect($orders)->sum('cycle_in_seconds');
			$scheduled[] = [
				'shift'=> [],
				'schedule' => $date->format('Y-m-d'),
				// 'estimates' => $this->format_for_estimates($total_seconds),
				// 'estimates_in_seconds' => $total_seconds,
				'duplicate_item_code' => 0,
				'orders' => $orders,
			];
		}

		$filters = [
			'customers' => array_unique($customers),
			'reference_nos' => array_unique($reference_nos),
			'parent_items' => array_unique($parent_items),
		];

		return [
			'unscheduled' => $unscheduled,
			'scheduled' => $scheduled,
			'filters' => $filters,
		];

		// return view('production_schedule_painting', compact('unscheduled', 'scheduled'));
	}

	public function get_scheduled_painting($schedule_date){
		$orders = DB::connection('mysql_mes')->table('production_order as pro')
		->join('job_ticket as jt', 'pro.production_order','jt.production_order')
		->leftJoin('delivery_date', function($join){
            $join->on( DB::raw('IFNULL(pro.sales_order, pro.material_request)'), '=', 'delivery_date.reference_no');
            $join->on('pro.parent_item_code','=','delivery_date.parent_item_code');
		})
		->whereRaw('pro.qty_to_manufacture > pro.feedback_qty')
		->whereNotIn('pro.status', ['Completed', 'Cancelled'])
		->where('jt.workstation', 'Painting')
		->whereDate('jt.planned_start_date', $schedule_date)
		->distinct('delivery_date.rescheduled_delivery_date','pro.production_order', 'pro.customer', 'pro.delivery_date', 'pro.item_code', 'pro.description', 'pro.qty_to_manufacture', 'pro.stock_uom', 'pro.produced_qty','pro.classification','pro.parts_category', 'pro.sales_order', 'pro.material_request','pro.status', 'pro.job_ticket_print','pro.withdrawal_slip_print', 'pro.parent_item_code', 'pro.status','jt.sequence','pro.feedback_qty')
		->select('delivery_date.rescheduled_delivery_date','pro.production_order', 'pro.customer', 'pro.delivery_date', 'pro.item_code', 'pro.description', 'pro.qty_to_manufacture', 'pro.stock_uom', 'pro.produced_qty','pro.classification','pro.parts_category', 'pro.sales_order', 'pro.material_request', 'pro.status', 'pro.job_ticket_print','pro.withdrawal_slip_print', 'pro.parent_item_code', 'pro.status','jt.sequence','pro.feedback_qty')
		->orderBy('jt.sequence', 'asc')
		->get();
	
	
	
			$scheduled = [];
			foreach($orders as $row){
							$jt = DB::connection('mysql_mes')->table('job_ticket as jt')
								->where('production_order',  $row->production_order)->get();
							$prod_stat = DB::connection('mysql_mes')->table('production_order as prod')
								->where('production_order',  $row->production_order)->first();
							$total_process = collect($jt)->where('workstation','Painting')->count();
							$total_pending = collect($jt)->where('workstation','Painting')->where('status', 'Pending')->count();
							$total_inprogress = collect($jt)->where('workstation','Painting')->where('status', '!=', 'Completed')->count();
		
							if ($total_process == $total_pending) {
								$status= 'Not Started';
							}else{
								if ($total_inprogress > 0) {
									$status= 'In Progress';
								}else{
									$status= 'Completed';
								}
		
							}
							

				$stripfromcomma =strtok($row->description, ",");
				$scheduled[] = [
					'id' => $row->production_order,
					'name' => $row->production_order,
					'status' => $status,
					'customer' => $row->customer,
					'delivery_date' => ($row->rescheduled_delivery_date == null)? $row->delivery_date: $row->rescheduled_delivery_date, // show reschedule delivery date/ existing delivery date based on the validation 
					'production_item' => $row->item_code,
					'description' => $row->description,
					'strip' => $stripfromcomma,
					'parts_category' => $row->parts_category,
					'parent_item_code' => $row->parent_item_code,
					'qty' => $row->qty_to_manufacture,
					'stock_uom' => $row->stock_uom,
					'produced_qty' => $row->produced_qty,
					'classification' => $row->classification,
					'withdrawal_slip_print' => $row->withdrawal_slip_print,
					'job_ticket_print' => $row->job_ticket_print,
					'production_order' => $row->production_order,
					'sales_order' =>($row->sales_order == null) ? $row->material_request:$row->sales_order,
					'prod_status' => $row->status,
					'process_stat'=> $this->material_status_stockentry($row->production_order, $prod_stat->status,  $row->qty_to_manufacture,$row->feedback_qty, $row->produced_qty),
					'order_no' => $row->sequence,
				];
			}
	
			return $scheduled;
	}

	public function format_for_estimates($cycle_time_in_seconds){
		$dur_hours = floor($cycle_time_in_seconds / 3600);
		$dur_minutes = floor(($cycle_time_in_seconds / 60) % 60);
		$dur_seconds = $cycle_time_in_seconds % 60;

		$dur_hours = ($dur_hours > 0) ? $dur_hours .'h' : null;
		$dur_minutes = ($dur_minutes > 0) ? $dur_minutes .'m' : null;
		$dur_seconds = ($dur_seconds > 0) ? $dur_seconds .'s' : null;

		return $dur_hours . ' '. $dur_minutes . ' ' . $dur_seconds;
	}

	public function get_prod_shift_sched($date, $operation_id){
		$scheduled = [];
		if (DB::connection('mysql_mes')
        ->table('shift_schedule')
		->where('date', $date)
        ->exists()){

			$shift_sched = DB::connection('mysql_mes')
			->table('shift_schedule')
			->where('date', $date)->get();
			foreach($shift_sched as $r){
				$shift_sched = DB::connection('mysql_mes')
				->table('shift')
                ->where('shift_id', $r->shift_id)
                ->where('operation_id', $operation_id)
                ->operat
				->first();
				$scheduled1[] = [
					'time_in'=> $shift_sched->time_in,
					'time_out' =>  $shift_sched->time_out,
					'shift_type' =>  $shift_sched->shift_type,
				];
			}
			
		}else{
			$scheduled1 = [];
		}
		return $scheduled1;
    }

	public function get_customer_reference_no($customer){
		return DB::connection('mysql_mes')->table('production_order')->whereNotIn('status', ['Completed', 'Cancelled'])
			->where('customer', $customer)->selectRaw('IFNULL(sales_order, material_request) as reference')
			->distinct('reference')->orderBy('reference', 'asc')->pluck('reference');
	}

	public function get_customers(){
		return DB::connection('mysql_mes')->table('production_order')->whereNotIn('status', ['Completed', 'Cancelled'])
			->whereNotNull('customer')->distinct('customer')->orderBy('customer', 'asc')->pluck('customer');
	}

	public function get_reference_production_items(Request $request, $reference){
		if($request->item_type == 'parent'){
			return DB::connection('mysql_mes')->table('production_order')->whereNotIn('status', ['Completed', 'Cancelled'])
			->where(function($q) use ($reference) {
				$q->where('sales_order', $reference)
					->orWhere('material_request', $reference);
			})
			->distinct('parent_item_code')->orderBy('parent_item_code', 'asc')->pluck('parent_item_code');
		}

		if($request->item_type == 'sub-parent'){
			return DB::connection('mysql_mes')->table('production_order')->whereNotIn('status', ['Completed', 'Cancelled'])
			->where(function($q) use ($reference) {
				$q->where('sales_order', $reference)
					->orWhere('material_request', $reference);
			})
			->where('parent_item_code', $request->parent_item)
			->whereNotNull('sub_parent_item_code')
			->distinct('sub_parent_item_code')->orderBy('sub_parent_item_code', 'asc')->pluck('sub_parent_item_code');
		}

		return DB::connection('mysql_mes')->table('production_order')->whereNotIn('status', ['Completed', 'Cancelled'])
			->where(function($q) use ($reference) {
				$q->where('sales_order', $reference)
					->orWhere('material_request', $reference);
			})
			->where('parent_item_code', $request->parent_item)
			->when($request->sub_parent_item, function($q) use ($request){
				return $q->where('sub_parent_item_code', $request->sub_parent_item);
			})
			->distinct('item_code')->orderBy('item_code', 'asc')->pluck('item_code');
	}

	public function getScheduledProdOrders($schedule_date, $operation_id){
		$orders = DB::connection('mysql_mes')->table('production_order')
			->distinct()
			->leftJoin('delivery_date', function($join)
            	{
                    $join->on( DB::raw('IFNULL(production_order.sales_order, production_order.material_request)'), '=', 'delivery_date.reference_no');
                    $join->on('production_order.parent_item_code','=','delivery_date.parent_item_code');
                })
    		->whereNotIn('production_order.status', ['Cancelled'])->where('production_order.is_scheduled', 1)
			->whereDate('production_order.planned_start_date', $schedule_date)
			->where("production_order.operation_id", $operation_id)
			->whereRaw('production_order.qty_to_manufacture > production_order.feedback_qty')
			->select('production_order.*', 'delivery_date.rescheduled_delivery_date')
    		->orderBy('production_order.order_no', 'asc')->orderBy('production_order.order_no', 'asc')->orderBy('production_order.created_at', 'desc')
    		->get();
    	$scheduled = [];
    	foreach($orders as $row){
    		$stripfromcomma =strtok($row->description, ",");
			$scheduled[] = [
				'id' => $row->production_order,
				'status' => $row->status,
				'name' => $row->production_order,
				'order_no' => $row->order_no,
				'customer' => $row->customer,
				'delivery_date' => ($row->rescheduled_delivery_date == null)? $row->delivery_date: $row->rescheduled_delivery_date,// show reschedule delivery date/ existing delivery date based on the validation 
				'production_item' => $row->item_code,
				'production_order' => $row->production_order,
				'description' => $row->description,
	    		'parts_category' => $row->parts_category,
	    		'parent_item_code' => $row->parent_item_code,
	    		'strip' => $stripfromcomma,
				'qty' => $row->qty_to_manufacture,
				'stock_uom' => $row->stock_uom,
				'produced_qty' => $row->produced_qty,
				'classification' => $row->classification,
				'withdrawal_slip_print' => $row->withdrawal_slip_print,
				'job_ticket_print' => $row->job_ticket_print,
				'sales_order' =>($row->sales_order == null) ? $row->material_request: $row->sales_order,
				'batch' => null,
				'process_stat'=> $this->material_status_stockentry($row->production_order, $row->status, $row->qty_to_manufacture,$row->feedback_qty, $row->produced_qty),
			];
    	}
		
    	return $scheduled;
	}

	public function view_operator_task($job_ticket, $operator_id){
    	$task_details = DB::connection('mysql_mes')->table('time_logs')->where('job_ticket_id', $job_ticket)->get();

    	$task_list = [];
    	foreach ($task_details as $row) {
    		$task_list[] = [
    			'operator_name' => $row->operator_name,
    			'machine_code' => $row->machine_code,
    			'from_time' => Carbon::parse($row->from_time)->format('M-d-Y h:i A'),
    			'to_time' => ($row->to_time) ? Carbon::parse($row->to_time)->format('M-d-Y h:i A') : '-',
    			'completed_qty' => $row->good + $row->reject,
    			'status' => $row->status,
    		];
    	}

    	return $task_list;
    }

    public function productionSchedule(){
    	return view('production_schedule');
    }

    public function getWorkstationSched(Request $request){
    	$list = DB::connection('mysql')->table('tabProduction Order as po')
    		->join('tabTimesheet as t', 'po.name', 't.production_order')
    		->join('tabTimesheet Detail as td', 't.name', 'td.parent')
    		->where('po.scheduled', 1)->where('po.docstatus', 1)
    		->where('po.company', 'FUMACO Inc.')
    		->where('po.planned_start_date', $request->schedule_date)
    		->select('td.workstation', 't.production_order', 'po.sales_order_no', 'po.customer', 'po.delivery_date', 'po.production_item', 'po.description', 'po.qty', 'td.good', 'td.reject', 'td.rework', 'td.operator_name', 'td.machine_name', 'td.from_time', 'td.to_time', 'td.status')
    		->get();

    	$prodorders = [];
    	foreach ($list as $row) {
    		$from = Carbon::parse($row->from_time);
			$to = Carbon::parse($row->to_time);

			$days = $from->diffInDays($to);
			$hours = $from->copy()->addDays($days)->diffInHours($to);
			$minutes = $from->copy()->addDays($days)->addHours($hours)->diffInMinutes($to);
			$dur_days = ($days > 0) ? $days .'d' : null;
			$dur_hours = ($hours > 0) ? $hours .'h' : null;
			$dur_minutes = ($minutes > 0) ? $minutes .'m' : null;

    		$prodorders[] = [
    			'workstation' => $row->workstation,
    			'production_order' => $row->production_order,
    			'sales_order_no' => $row->sales_order_no,
    			'customer' => $row->customer,
    			'delivery_date' => $row->delivery_date,
    			'production_item' => $row->production_item,
    			'description' => $row->description,
    			'qty' => $row->qty,
    			'good' => $row->good,
    			'reject' => $row->reject,
    			'rework' => $row->rework,
    			'balance' => $row->qty - $row->good,
    			'operator_name' => $row->operator_name,
    			'machine_name' => $row->machine_name,
    			'from_time' => Carbon::parse($row->from_time)->format('Y-m-d h:i A'),
    			'to_time' => Carbon::parse($row->to_time)->format('Y-m-d h:i A'),
    			'status' => $row->status,
    			'duration' => $dur_days .' '. $dur_hours . ' '. $dur_minutes,
    		];
    	}

    	$is_not_painting = in_array($request->type, ['Fabrication', 'Assembly']);
    	$workstations = DB::connection('mysql')->table('tabWorkstation')
    		->when($is_not_painting, function ($query) use ($request) {
    			return $query->where('operation', $request->type);
    		}, function ($query) {
    			return $query->where('name', 'Painting');
    		})
    		->orderBy('order_no', 'asc')->get();

    	$output = $this->productionOutput($workstations, $request->schedule_date);

    	if ($is_not_painting) {
    		return view('prod_schedule_content_fabrication', compact('workstations', 'prodorders', 'output'));
    	}

    	return view('prod_schedule_content_painting', compact('workstations', 'prodorders', 'output'));
    }

    public function productionOutput($workstations, $schedule_date){
    	$list = DB::connection('mysql')->table('tabTimesheet Detail')->where('status', 'Completed')
	    		->where('operator_id', '!=', null)->where('machine_name', '!=', null)
	    		->whereDate('to_time', $schedule_date)->whereDate('from_time', $schedule_date)
	    		->selectRaw('workstation, machine_name, operator_name, SUM(completed_qty) as qty, MIN(from_time) as from_time, MAX(to_time) as to_time')
	    		->groupBy('workstation', 'machine_name', 'operator_name')
	    		->get();

	   	$production_output = [];
    	foreach ($workstations as $w) {
    		$output_list = collect($list)->where('workstation', $w->name);
	    	$output_arr = [];
	    	foreach ($output_list as $v) {
	    		$from = Carbon::parse($v->from_time);
				$to = Carbon::parse($v->to_time);

				$dur_minutes = $from->diffInMinutes($to);

	    		$output_arr[] = [
	    			'workstation' => $v->workstation,
	    			'machine_name' => $v->machine_name,
	    			"operator_name" => $v->operator_name,
	    			"qty" => $v->qty,
	    			"from_time" => $v->from_time,
	    			"to_time" => $v->to_time,
	    			"duration_in_mins" => $dur_minutes
	    		];
	    	}

	    	$total_dur_in_minutes = collect($output_arr)->sum('duration_in_mins');

	    	$hours = floor($total_dur_in_minutes / 60);
   			$minutes = ($total_dur_in_minutes % 60);

   			$dur_hours = ($hours > 0) ? $hours .'h' : null;
			$dur_minutes = ($minutes > 0) ? $minutes .'m' : null;

			$total_runtime = $dur_hours . ' '. $dur_minutes;

			$production_output[] = [
				'workstation' => $w->name,
				'output' => $output_arr,
				'total_runtime' => $total_runtime
			];
    	}

    	return $production_output;
    }

    public function machineControlView(){
    	$machines = DB::connection('mysql')->table('tabWorkstation Machine')->where('parent', 'Bending 1')->get();

    	return view('machine_control_view.index', compact('machines'));
    }
	// END PPC STAFF
    // Operator
    public function operatorpage($id){
        $tabWorkstation= DB::connection('mysql_mes')->table('workstation')->where('workstation_name', $id)
			->select('workstation_name', 'workstation_id', 'operation_id')->first();

		$workstation_list = DB::connection('mysql_mes')->table('workstation')
			->where('operation_id', $tabWorkstation->operation_id)
        	->orderBy('order_no', 'asc')->pluck('workstation_name');
        
        $now = Carbon::now();
        $workstation=$tabWorkstation->workstation_name;
        $workstation_id= $tabWorkstation->workstation_id;
        $workstation_name=$id;
        $date = $now->format('M d Y');
        $day_name= $now->format('l');

        return view('operator_workstation_dashboard', compact('workstation','workstation_name', 'day_name', 'date', 'workstation_list', 'workstation_id'));
    }

    public function current_data_operator($workstation){
        $tasks = DB::connection('mysql_mes')->table('job_ticket AS jt')
        	->join('production_order AS po', 'jt.production_order', 'po.production_order')
        	->join('time_logs AS t', 't.job_ticket_id', 'jt.job_ticket_id')
        	->where('jt.workstation', $workstation)->whereNotIn('po.status', ['Cancelled'])
            ->select('po.production_order', 't.status', 't.reject')
            ->get();

        if ($workstation == 'Spotwelding') {
	        $tasks = DB::connection('mysql_mes')->table('job_ticket AS jt')
	        	->join('production_order AS po', 'jt.production_order', 'po.production_order')
	        	->join('spotwelding_qty AS t', 't.job_ticket_id', 'jt.job_ticket_id')
	        	->where('jt.workstation', $workstation)->whereNotIn('po.status', ['Cancelled'])
	            ->select('po.production_order', 't.status', 't.reject')
	            ->get();
        }

        $pending = DB::connection('mysql_mes')->table('job_ticket')->where('status', 'Pending')
        	->where('workstation', $workstation)->count();
        $inprogress = collect($tasks)->where('status', 'In Progress')->count();
        $rejects = collect($tasks)->where('reject', '>', 0)->count();
        $completed = collect($tasks)->where('status', 'Completed')->count();

        $data = [
            'completed' => $completed,
            'pending' => $pending,
            'inprogress' => $inprogress,
            'rejects' => $rejects
        ];

       	return $data;
    }

    public function operators_workstation_TaskList($workstation, $status){
        try {
        	if ($status == 'Pending') {
	    		$job_ticket_qry = DB::connection('mysql_mes')->table('job_ticket')
	    			->join('production_order', 'job_ticket.production_order', 'production_order.production_order')
	    			->where('job_ticket.workstation', $workstation)->whereNotIn('production_order.status', ['Cancelled'])
	    			->where('job_ticket.status', 'Pending')
	    			->select('production_order.customer', 'production_order.qty_to_manufacture', 'produced_qty', 'production_order.production_order', 'production_order.item_code', 'job_ticket.status', 'job_ticket.workstation', DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process'))
	                ->orderBy('production_order.order_no', 'asc')->orderBy('production_order.planned_start_date', 'asc')->get();

	            $tasks_list = [];
	            foreach ($job_ticket_qry as $row) {
		    		$tasks_list[] = [
	            		'production_order' => $row->production_order,
	            		'workstation' => $row->workstation,
	            		'item_code' => $row->item_code,
	            		'customer' => $row->customer,
	            		'qty' => $row->qty_to_manufacture - $row->produced_qty,
	            		'completed_qty' => 0,
	            		'good_qty' => 0,
	            		'reject_qty' => 0,
	            		'status' => $row->status,
	            		'process' => $row->process,
	            		'duration' => null,
	            		'from_time' => null,
	            		'to_time' => null,
	            		'operator_name' => null,
	            		'qa_inspection_status' => null,
	            		'machine' => null,
	            	];
		    	}

		    	return view('tables.tbl_operator_workstation', compact('tasks_list', 'status'));
	    	}

        	$today = Carbon::now()->format('Y-m-d');
            $tasks = DB::connection('mysql_mes')->table('job_ticket AS jt')
            	->join('production_order AS po', 'jt.production_order', 'po.production_order')
            	->when($workstation == 'Spotwelding', function ($query){
    				return $query->join('spotwelding_qty AS t', 'jt.job_ticket_id', 't.job_ticket_id');
    			}, function ($query) {
    				return $query->join('time_logs AS t', 'jt.job_ticket_id', 't.job_ticket_id');;
    			})
            	->where('jt.workstation', $workstation)->whereNotIn('po.status', ['Cancelled'])
                ->when($status != 'Rejects', function ($query) use ($status) {
    				return $query->where('t.status', $status);
    			}, function ($query) {
    				return $query->where('t.reject', '>', 0);
    			})
                ->select('jt.job_ticket_id', 'po.customer', 'po.qty_to_manufacture', 'po.production_order', 'po.item_code', 't.status', 't.operator_name','jt.workstation', 't.from_time', 't.to_time','t.machine_code', 't.time_log_id', 't.good', 't.reject', DB::raw('(SELECT process_name FROM process WHERE process_id = jt.process_id) AS process'))
                ->orderBy('po.order_no', 'asc')->orderBy('po.planned_start_date', 'asc')->get();

            $tasks_list = [];
            foreach ($tasks as $row) {
            	$from = Carbon::parse($row->from_time);
				$to = Carbon::parse($row->to_time);

				$days = $from->diffInDays($to);
				$hours = $from->copy()->addDays($days)->diffInHours($to);
				$minutes = $from->copy()->addDays($days)->addHours($hours)->diffInMinutes($to);
				$seconds = $from->copy()->addDays($days)->addHours($hours)->addMinutes($minutes)->diffInSeconds($to);
				$dur_days = ($days > 0) ? $days .'d' : null;
				$dur_hours = ($hours > 0) ? $hours .'h' : null;
				$dur_minutes = ($minutes > 0) ? $minutes .'m' : null;
				$dur_seconds = ($seconds > 0) ? $seconds .'s' : null;

				$reference_type = ($workstation == 'Spotwelding') ? 'Spotwelding' : 'Time Logs';
				$reference_id = ($workstation == 'Spotwelding') ? $row->job_ticket_id : $row->time_log_id;
				$qa_inspection_status = $this->get_qa_inspection_status($reference_type, $reference_id);
            	$tasks_list[] = [
            		'production_order' => $row->production_order,
            		'workstation' => $row->workstation,
            		'item_code' => $row->item_code,
            		'customer' => $row->customer,
            		'qty' => $row->qty_to_manufacture,
            		'completed_qty' => $row->good + $row->reject,
            		'good_qty' => $row->good,
            		'reject_qty' => $row->reject,
            		'status' => $row->status,
            		'process' => $row->process,
            		'duration' => $dur_days .' '. $dur_hours . ' '. $dur_minutes . ' '. $dur_seconds,
            		'from_time' => Carbon::parse($row->from_time)->format('Y-m-d h:i A'),
            		'to_time' => Carbon::parse($row->to_time)->format('Y-m-d h:i A'),
            		'operator_name' => $row->operator_name,
            		'qa_inspection_status' => $qa_inspection_status,
            		'machine' => $row->machine_code,
            	];
            }

            return view('tables.tbl_operator_workstation', compact('tasks_list', 'status'));
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function submit_quality_check(Request $request){
		$now = Carbon::now(); 	
    	if (!$request->inspected_by) {
    		return response()->json(['success' => 0, 'message' => 'Please tap Authorized QC Employee ID.']);
		}
		
		if ($request->qc_inspection_status == 'QC Failed') {
			if (!$request->rejection_type) {
				return response()->json(['success' => 0, 'message' => 'Please select Rejection Type']);
			}
		}

    	$qa_user = DB::connection('mysql_essex')->table('users')
    		->where('user_id', $request->inspected_by)->first();

    	if (!$qa_user) {
    		return response()->json(['success' => 0, 'message' => 'Authorized QA Employee ID not found.']);
    	}

    	$qa_staff_name = $qa_user->employee_name;
    	if ($request->id) {
    		$qc_remarks = ($request->rework_qty && $request->rework_qty > 0) ? 'For Rework' : 'Scrap';
	    	$qc_remarks = ($request->qc_inspection_status == 'QC Failed') ? $qc_remarks : null;
    		if ($request->qc_type == 'Random Inspection') {
    			// request id = time log id
    			$job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')
	    			->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
	    			->where('time_logs.time_log_id', $request->id)->first();

	    		$production_order = $job_ticket_details->production_order;
	    		$workstation = $job_ticket_details->workstation;
	    		$insert = [
	    			'time_log_id' => $request->id,
	    			'qa_inspection_type' => $request->qc_type,
	    			'qa_inspection_date' => $now->toDateTimeString(),
	    			'qa_staff_id' => $request->inspected_by,
	    			'sampling_qty' => $request->sampling_qty,
	    			'rejected_qty' => $request->reject_qty,
	    			'for_rework_qty' => ($request->rework_qty) ? $request->rework_qty : 0,
	    			'status' => $request->qc_inspection_status,
	    			'qc_remarks' => $qc_remarks,
	    			'rejection_id' => $request->rejection_type,
	    			'remarks' => $request->rejection_type,
	    			'created_by' => $qa_staff_name,
	    			'created_at' => $now->toDateTimeString()
	    		];

	    		$good_qty_after_transaction = $job_ticket_details->good - $request->reject_qty;
	
				if($good_qty_after_transaction < 0){
					$good_qty_after_transaction = 0;
				}

				$update = [
					'last_modified_at' => $now->toDateTimeString(),
					'last_modified_by' => $qa_staff_name,
					'good' => $good_qty_after_transaction,
					'reject' => $request->reject_qty,
				];
				
				DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $request->id)->update($update);
	    		DB::connection('mysql_mes')->table('quality_inspection')->insert($insert);

	    		$this->update_completed_qty_per_workstation($job_ticket_details->job_ticket_id);
    		}

			if ($request->qc_type == 'Reject Confirmation') {
				// request id = qa id
				$time_log_details = DB::connection('mysql_mes')->table('time_logs')
						->join('job_ticket', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
		    			->join('quality_inspection', 'quality_inspection.time_log_id', 'time_logs.time_log_id')
		    			->where('qa_id', $request->qa_id)->first();

		    	$production_order = $time_log_details->production_order;
	    		$workstation = $time_log_details->workstation;

				$update_rejection_type = [
					'last_modified_at' => $now->toDateTimeString(),
					'last_modified_by' => $qa_staff_name,
	    			'qa_staff_id' => $request->inspected_by,
	    			'sampling_qty' => $request->sampling_qty,
	    			'rejected_qty' => $request->reject_qty,
	    			'qa_inspection_date' => $now->toDateTimeString(),
	    			'for_rework_qty' => ($request->rework_qty) ? $request->rework_qty : 0,
	    			'status' => $request->qc_inspection_status,
	    			'qc_remarks' => $qc_remarks,
	    			'rejection_id' => $request->rejection_type,
	    			'remarks' => $request->rejection_type,
				];

				if ($request->qc_inspection_status == 'QC Passed') {
					$good_qty_after_transaction = $time_log_details->good + $time_log_details->reject;

					$update = [
						'last_modified_at' => $now->toDateTimeString(),
						'last_modified_by' => $qa_staff_name,
						'good' => $good_qty_after_transaction,
						'reject' => $request->reject_qty,
					];
					
					DB::connection('mysql_mes')->table('time_logs')
						->where('time_log_id', $time_log_details->time_log_id)->update($update);
				}

				DB::connection('mysql_mes')->table('quality_inspection')->where('qa_id', $request->qa_id)
					->update($update_rejection_type);

				$this->update_completed_qty_per_workstation($time_log_details->job_ticket_id);
			}

			$process_id = ($request->qc_type == 'Random Inspection') ? $job_ticket_details->process_id : $time_log_details->process_id;

			$this->updateProdOrderOps($production_order, $workstation, $process_id);
			$this->update_produced_qty($production_order);

			return response()->json(['success' => 1, 'message' => 'Task updated.', 'details' => ['production_order' => $production_order, 'workstation' => $workstation]]);
		}
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

    public function get_production_order_details($production_order, $workstation, $machine){
    	$prod = DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->first();
    	if (!$prod) {
    		return response()->json(['not_found' => 1, 'message' => 'Production Order not found.']);
    	}

    	$details = DB::connection('mysql_mes')->table('production_order AS po')
    		->join('job_ticket AS jt', 'po.production_order', 'jt.production_order')
    		->when($machine != 'null', function ($query) use ($machine) {
				return $query->where('jt.machine', $machine);
            })
    		->where('po.production_order', $production_order)->where('jt.workstation', $workstation);

    	$workstations = $details->get();

    	$in_process = collect($workstations)->where('status', 'In Progress')->count();
    	$assigned = collect($workstations)->where('status', 'Pending')->count();
    	$completed = collect($workstations)->where('status', 'Completed')->count();
    	$quality_check = collect($workstations)->where('jt.item_feedback', 'Quality Check')
    		->where('jt.status', 'Completed')->where('jt.qa_inspection_status', 'Pending')->count();
    	$reject_confirmation = collect($workstations)->where('reject', '>', 0)->where('qa_inspection_status', 'Pending')
    		->where('jt.status', 'Completed')->count();
    	
    	if ($reject_confirmation > 0) {
    		$details = $details->where('reject', '>', 0)->where('qa_inspection_status', 'Pending')
    			->where('jt.status', 'Completed')->first();
    	}elseif ($quality_check > 0) {
    		$details = $details->where('jt.item_feedback', 'Quality Check')->where('jt.status', 'Completed')
    			->where('jt.qa_inspection_status', 'Pending')->first();
    	}elseif ($in_process > 0) {
    		$details = $details->where('jt.status', 'In Progress')->first();
    	}elseif ($assigned > 0) {
    		$details = $details->where('jt.status', 'Pending')->first();
    	}elseif ($completed > 0) {
    		$details = $details->where('jt.status', 'Completed')->first();
    	}else{
    		$details = [];
    	}
    	
    	return view('tables.production_actions_content', compact('details'));
    }

    public function restart_task(Request $request){
    	DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $request->id)->delete();
 
    	return response()->json(['success' => 1, 'message' => 'Task has been updated.']);
    }

    public function reset_task(Request $request){
    	$now = Carbon::now();
    	$update = [
    		'from_time' => null,
    		'to_time' => null,
    		'hours' => null,
    		'status' => 'Accepted',
    		'operator_id' => null,
    		'operator_name' => null,
    		'last_modified_at' => $now->toDateTimeString(),
			'last_modified_by' => Auth::user()->employee_name,
    	];

    	DB::connection('mysql_mes')->table('job_ticket')->where('id', $request->id)->update($update);

    	return response()->json(['success' => 1, 'message' => 'Task has been reset.']);
    }

    public function get_process_list($workstation){
    	return DB::connection('mysql_mes')->table('process_assignment')->join('process', 'process.id', 'process_assignment.process_id')->where('process_assignment.workstation_id', $workstation)->select('process.id', 'process.process')->distinct()->get();
    }

    public function update_process(Request $request){
    	try {
    		if ($request->id) {
    			$jt_details = DB::connection('mysql_mes')->table('job_ticket')->where('id', $request->id)
    				->whereNotIn('status', ['Unassigned', 'Accepted'])->first();
    			if ($jt_details) {
    				return response()->json(['success' => 0, 'message' => 'Task already Completed / In Progress.']);
    			}

    			DB::connection('mysql_mes')->table('job_ticket')->where('id', $request->id)->update(['process' => $request->process, 'machine' => null, 'machine_name' => null, 'status' => 'Unassigned']);

    			return response()->json(['success' => 1, 'message' => 'Task updated.']);
    		}
    	} catch (Exception $e) {
    		return response()->json(["error" => $e->getMessage()]);
    	}
    }

    public function mark_as_done_task(Request $request){
    	try {
            if ($request->id) {
                $jt_details = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $request->id)
                    ->where('status','=', 'Completed')->first();
                if ($jt_details) {
                    return response()->json(['success' => 0, 'message' => 'Task already Completed']);
                }
                $jt_details = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $request->id)->first();

                $machine_details= DB::connection('mysql_mes')->table('machine')->where('machine_id', $request->machine_selected_id)->select('machine_code','machine_name')->first();
                $now = Carbon::now();
                $values = [
                    'to_time' => $now->toDateTimeString(),
                    'status' => 'Completed',
                    'remarks' => 'Override',
                    'operator_id' => Auth::user()->user_id,
                    'operator_name' => Auth::user()->employee_name,
                    'last_modified_by' => Auth::user()->employee_name,
                    'last_modified_at' => $now->toDateTimeString(),
                    'good' => $request->qty_accepted,
                    'completed_qty' => $request->qty_accepted,
                    'machine_code' => $machine_details->machine_code,
                    'machine_name' => $machine_details->machine_name
                ];

                DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $request->id)->update($values);

                $this->update_produced_qty($jt_details->production_order);

                return response()->json(['success' => 1, 'message' => 'Task Overridden.']);
            }
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }
    // SecondaryController
    public function update_machine_path(Request $request){

        $image_path = $request->user_image;
        if($request->hasFile('empImage')){
            $file = $request->file('empImage');

            //get filename with extension
            $filenamewithextension = $file->getClientOriginalName();
            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
            //get file extension
            $extension = $file->getClientOriginalExtension();
            //filename to store
            $filenametostore = $request->machine_code.''.uniqid().'.'.$extension;
            // Storage::put('public/employees/'. $filenametostore, fopen($file, 'r+'));
            Storage::put('public/machine/'. $filenametostore, fopen($file, 'r+'));
            //Resize image here
            $thumbnailpath = public_path('storage/machine/'.$filenametostore);
            $img = Image::make($thumbnailpath)->resize(500, 350, function($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($thumbnailpath);

            $image_path = '/storage/machine/'.$filenametostore;
        }
            $update = DB::connection('mysql_mes')->table('machine')
                ->where('machine_code', $request->test5)
                ->update(['image' => $image_path ]);

        return redirect()->back()->with(['message' => 'Employee has been successfully updated!']);
    }

    public function get_production_order_task($production_order, $workstation){
    	$production_order_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->first();
    	if (!$production_order_details) {
    		return response()->json(['success' => 0, 'message' => 'Production Order ' . $production_order . ' not found.']);
    	}

    	$check_prod_workstation_exist = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $production_order)
    		->where('workstation', $workstation)->first();
    	if (!$check_prod_workstation_exist) {
    		return response()->json(['success' => 0, 'message' => 'Production Order not available in this workstation.']);
    	}

    	$process_list = $this->get_production_workstation_process($production_order, $workstation, $production_order_details->qty_to_manufacture);

    	$details = ['production_order' => $production_order_details, 'tasks' => $process_list];

    	return response()->json(['success' => 1, 'message' => 'Task Found.', 'details' => $details]);
    }

    public function start_unassigned_task(Request $request){
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
				
			$production_order = DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)->first();
			$job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $request->job_ticket_id)->first();

			$exploded_production_order = explode('-', $request->production_order);

			if ($exploded_production_order[0] != 'SC') {

				if(!$production_order->planned_start_date){
					return response()->json(['success' => 0, 'message' => 'Production Order not scheduled today.']);
				}

				if(!$production_order->planned_start_date){
					return response()->json(['success' => 0, 'message' => 'Task not scheduled today.']);
				}

				if($production_order->planned_start_date > $now->format('Y-m-d H:i:s')){
					return response()->json(['success' => 0, 'message' => 'Task not scheduled today.']);
				}
			}

			if($production_order->item_classification != 'HO - Housing'){
				$is_transferred = DB::connection('mysql')->table('tabStock Entry as ste')
					->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
					->where('ste.production_order', $request->production_order)
					->where(function($q){
						$q->where('sted.status', 'Issued')
							->orWhere('ste.docstatus', 1);
					})
					->where('ste.docstatus', '<', 2)->exists();
					
				if(!$is_transferred){
					return response()->json(['success' => 0, 'message' => 'No available materials.']);
				}
			}

	    	$values = [
	    		'job_ticket_id' => $request->job_ticket_id,
				'from_time' => $now->toDateTimeString(),
				'machine_code' => $request->machine_code,
				'machine_name' => $machine_name,
				'operator_id' => $request->operator_id,
				'operator_name' => $operator->employee_name,
				'operator_nickname' => $operator->nick_name,
				'status' => 'In Progress',
				'created_by' => $operator->employee_name,
				'created_at' => $now->toDateTimeString(),
				'process_description' => $request->process_description
	    	];

	    	DB::connection('mysql_mes')->table('time_logs')->insert($values);

	    	$details = [
	    		'production_order' => $request->production_order,
	    		'process_id' => $request->process_id,
	    	];
	    	
			
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

    public function validate_workstation_machine($machine_id, $workstation){
		try {
			$details = DB::connection('mysql_mes')->table('workstation_machine')
				->join('machine', 'machine.machine_id', 'workstation_machine.machine_id')
				->join('workstation', 'workstation.workstation_id', 'workstation_machine.workstation_id')
				->where('workstation.workstation_name', $workstation)->where('machine.machine_code', $machine_id)
				->select('machine.*')->first();

			if (!$details) {
				return response()->json(["success" => 0, "message" => 'Machine not found. Try again.', 'details' => []]);
			}

			return response()->json(["success" => 1, "message" => 'Machine found. Please wait.', 'details' => $details]);
		} catch (Exception $e) {
			return response()->json(["success" => 0, "message" => $e->getMessage()]);
		}
	}

	public function get_workstation_process_machine($workstation, $process_id){
		return DB::connection('mysql_mes')->table('process_assignment')
				->join('machine', 'machine.machine_id', 'process_assignment.machine_id')
				->where('process_assignment.workstation_id', $workstation)
				->where('process_assignment.process_id', $process_id)->select('machine.*', 'process_assignment.process_id')->get();
	}

	public function get_production_workstation_process($production_order, $workstation, $required_qty){
		$processes = DB::connection('mysql_mes')->table('job_ticket AS jt')
			->where('production_order', $production_order)->where('workstation', $workstation)
			->select('process_id', DB::raw('(SELECT process_name FROM process WHERE process_id = jt.process_id) AS process_name'), 'completed_qty', 'status')
			->orderBy('idx', 'asc')
			->get();

		$process_list = [];
		foreach ($processes as $row) {
			$process_list[] = [
				'process_id' => $row->process_id,
				'process_name' => $row->process_name,
				'remaining_qty' => $required_qty - $row->completed_qty,
				'completed_qty' => $row->completed_qty,
				'has_in_progress' => ($row->status == 'In Progress') ? true : false,
			];
		}
		
		return $process_list;
	}

	public function login_operator(Request $request){
		if($request->operator_id < 3){
			$in_progress_operator_machine = DB::connection('mysql_mes')->table('time_logs')
			->whereNotNull('operator_id')
			->where('operator_id', '!=', $request->operator_id)
			->where('machine_code', $request->machine_code)
			->where('status', 'In Progress')->exists();
		
			if ($in_progress_operator_machine) {
				return response()->json(['success' => 0, 'message' => "Machine is in use by another operator."]);
			}
		}

		$operator_in_progress_task = DB::connection('mysql_mes')->table('job_ticket')
			->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
			->where('job_ticket.production_order', '!=', $request->production_order)
			->where('time_logs.operator_id', $request->operator_id)
			->where('time_logs.status', 'In Progress')->first();

		$operator_in_progress_spotwelding = DB::connection('mysql_mes')->table('job_ticket')
			->join('spotwelding_qty', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
			->where('job_ticket.production_order', '!=', $request->production_order)
			->where('spotwelding_qty.operator_id', $request->operator_id)
			->where('spotwelding_qty.status', 'In Progress')->first();

		if ($operator_in_progress_task) {
			return response()->json(['success' => 0, 'message' => "Operator has in-progress task. " . $operator_in_progress_task->production_order]);
		}

		if ($operator_in_progress_spotwelding) {
			return response()->json(['success' => 0, 'message' => "Operator has in-progress task. " . $operator_in_progress_spotwelding->production_order]);
		}

		$job_ticket = DB::connection('mysql_mes')->table('job_ticket')
			->where('production_order', $request->production_order)
			->where('process_id', $request->process_id)->first();
	
    	$details = [
			'job_ticket_id' => $job_ticket->job_ticket_id,
			'machine_code' => $request->machine_code,
			'operator_id' => $request->operator_id,
    		'workstation' => $job_ticket->workstation,
		];

		// attempt to do the login
		$user = DB::connection('mysql_essex')->table('users')->where('user_id', $request->operator_id)->first();

        if ($user) {
            if(Auth::loginUsingId($user->id)){
                return response()->json(['success' => 1, 'message' => "<b>Welcome!</b> Please wait...", 'details' => $details]);
            } 
        } else {        
            // validation not successful, send back to form 
            return response()->json(['success' => 0, 'message' => '<b>Invalid credentials!</b> Please try again.']);
        }
	}

	public function get_current_operator_task_details(Request $request, $operator_id){
		$job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')
			->where('job_ticket_id', $request->job_ticket_id)->first();

		if(!$job_ticket_details){
			return response()->json(['success' => 0, 'message' => 'Task not found.']);
		}

		$status = $job_ticket_details->status;
		$machine_code = $request->machine_code;

		$time_logs = DB::connection('mysql_mes')->table('time_logs')->where('job_ticket_id', $request->job_ticket_id)
			->where('operator_id', $operator_id)->first();

		$exploded_production_order = explode('-', $request->production_order);

		if ($exploded_production_order[0] == 'SC') {
			return $this->operator_scrap_task($request->workstation, $request->machine_code, $request->production_order, $request->job_ticket_id, $operator_id);
		}

		if (!$time_logs) {
			$task_list_qry = DB::connection('mysql_mes')->table('production_order AS po')
			->join('job_ticket AS jt', 'po.production_order', 'jt.production_order')
			->where('po.production_order', $request->production_order)
			->where('jt.workstation', $request->workstation)
			->where('jt.job_ticket_id', $request->job_ticket_id)
			->select('po.item_code', 'jt.job_ticket_id', DB::raw('(SELECT process_name FROM process WHERE process_id = jt.process_id) AS process_name'), 'po.production_order', 'po.description', 'po.sales_order', 'po.material_request', 'po.customer', 'po.qty_to_manufacture', 'po.stock_uom', 'po.project', 'jt.process_id', 'jt.completed_qty', 'jt.status')
			->orderBy('jt.last_modified_at', 'desc')->get();
		}else{
			$task_list_qry = DB::connection('mysql_mes')->table('production_order AS po')
				->join('job_ticket AS jt', 'po.production_order', 'jt.production_order')
				->join('time_logs', 'time_logs.job_ticket_id', 'jt.job_ticket_id')
				->where('po.production_order', $request->production_order)
				->where('jt.workstation', $request->workstation)
				->where('jt.job_ticket_id', $request->job_ticket_id)
				->where('time_logs.operator_id', Auth::user()->user_id)
				->select('po.item_code', 'time_logs.time_log_id', 'jt.job_ticket_id', 'time_logs.operator_id', 'time_logs.machine_code', DB::raw('(SELECT process_name FROM process WHERE process_id = jt.process_id) AS process_name'), 'po.production_order', 'po.description', 'po.sales_order', 'po.material_request', 'time_logs.status', 'time_logs.from_time', 'time_logs.to_time', 'po.customer', 'po.qty_to_manufacture', DB::raw('(SELECT SUM(good) FROM time_logs WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS total_good'),  DB::raw('(SELECT SUM(reject) FROM time_logs WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS total_reject'), 'po.stock_uom', 'po.project', 'time_logs.operator_name', 'jt.process_id', 'time_logs.good')
				->orderByRaw("FIELD(time_logs.status, 'In Progress', 'Pending', 'Completed') ASC")
				->orderBy('time_logs.last_modified_at', 'desc')->get();
		}

		$task_list = [];
		foreach ($task_list_qry as $row) {
			if ($time_logs) {
				$reference_type = ($request->workstation == 'Spotwelding') ? 'Spotwelding' : 'Time Logs';
				$reference_id = ($request->workstation == 'Spotwelding') ? $row->job_ticket_id : $row->time_log_id;
				$qa_inspection_status = $this->get_qa_inspection_status($reference_type, $reference_id);
				$helpers = DB::connection('mysql_mes')->table('helper')
					->where('time_log_id', $row->time_log_id)->get();

				$qry = DB::connection('mysql_mes')->table('time_logs')
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

			if(!$time_logs && $row->status != 'Completed'){
				$jt_status = 'Pending';
			}else{
				$jt_status = $row->status;
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
				'status' => ($time_logs) ? $row->status : $jt_status,
				'from_time' => ($time_logs) ? $row->from_time : null,
				'to_time' => ($time_logs) ? $row->to_time : null,
				'customer' => $row->customer,
				'qty_to_manufacture' => $row->qty_to_manufacture,
				'total_good' => ($time_logs) ? $row->total_good : $row->completed_qty,
				'total_reject' => ($time_logs) ? $row->total_reject : 0,
				'stock_uom' => $row->stock_uom,
				'project' => $row->project,
				'operator_name' => ($time_logs) ? $row->operator_name : null,
				'process_id' => $row->process_id,
				'good' => ($time_logs) ? $row->good : 0,
				'time_log_id' => ($time_logs) ? $row->time_log_id : null,
				'helpers' => $helpers,
				'count_helpers' => $count_helpers
			];
		}

		$batch_list = DB::connection('mysql_mes')->table('job_ticket')
			->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
			->where('time_logs.job_ticket_id', $request->job_ticket_id)
			->where('operator_id', $operator_id)
			->select('*', DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process_name'))
			->where('time_logs.status', 'Completed')->get();

		$in_progress_operator = DB::connection('mysql_mes')->table('job_ticket')
			->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
			->where('production_order', $request->production_order)
			->where('workstation', $request->workstation)
			->where('process_id', $job_ticket_details->process_id)
			->where('operator_id', '!=', $operator_id)
			->whereNotNull('operator_id')
			->select('operator_id', 'operator_nickname', DB::raw('SUM(good + reject) as completed_qty'))->groupBy('operator_id', 'operator_nickname')->get();

    	return view('tables.tbl_current_operator_task', compact('task_list', 'machine_code', 'batch_list', 'in_progress_operator'));
	}

	public function operator_scrap_task($workstation, $machine_code, $production_order, $job_ticket_id, $operator_id){
		$job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')
			->where('job_ticket_id', $job_ticket_id)->first();

		if(!$job_ticket_details){
			return response()->json(['success' => 0, 'message' => 'Task not found.']);
		}

		$status = $job_ticket_details->status;

		$time_logs = DB::connection('mysql_mes')->table('time_logs')->where('job_ticket_id', $job_ticket_id)
			->where('operator_id', $operator_id)->first();

		if (!$time_logs) {
			$task_list_qry = DB::connection('mysql_mes')->table('scrap')
				->join('uom', 'scrap.uom_id', 'uom.uom_id')
				->join('job_ticket AS jt', DB::raw('CONCAT("SC-", scrap.scrap_id)'), 'jt.production_order')
				->where('jt.production_order', $production_order)
				->where('jt.workstation', $workstation)
				->where('jt.job_ticket_id', $job_ticket_id)
				->select('jt.job_ticket_id', DB::raw('(SELECT process_name FROM process WHERE process_id = jt.process_id) AS process_name'), 'jt.production_order', 'jt.process_id', 'scrap.*', 'uom.uom_name')
				->orderBy('jt.last_modified_at', 'desc')->get();

			// $task_list_qry = DB::connection('mysql_mes')->table('job_ticket AS jt')
			// 	->where('jt.production_order', $production_order)
			// 	->where('jt.workstation', $workstation)
			// 	->where('jt.job_ticket_id', $job_ticket_id)
			// 	->select('jt.job_ticket_id', DB::raw('(SELECT process_name FROM process WHERE process_id = jt.process_id) AS process_name'), 'jt.production_order', 'jt.process_id')
			// 	->orderBy('jt.last_modified_at', 'desc')->get();

		}else{
			$task_list_qry = DB::connection('mysql_mes')->table('scrap')
				->join('uom', 'scrap.uom_id', 'uom.uom_id')
				->join('job_ticket AS jt', DB::raw('CONCAT("SC-", scrap.scrap_id)'), 'jt.production_order')
				->join('time_logs', 'time_logs.job_ticket_id', 'jt.job_ticket_id')
				->where('jt.production_order', $production_order)
				->where('jt.workstation', $workstation)
				->where('jt.job_ticket_id', $job_ticket_id)
				->where('time_logs.operator_id', Auth::user()->user_id)
				->select('time_logs.time_log_id', 'jt.job_ticket_id', 'time_logs.operator_id', 'time_logs.machine_code', DB::raw('(SELECT process_name FROM process WHERE process_id = jt.process_id) AS process_name'), 'jt.production_order', 'time_logs.status', 'time_logs.from_time', 'time_logs.to_time', DB::raw('(SELECT SUM(good) FROM time_logs WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS total_good'),  DB::raw('(SELECT SUM(reject) FROM time_logs WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS total_reject'), 'time_logs.operator_name', 'jt.process_id', 'time_logs.good', 'scrap.*', 'uom.uom_name')
				->orderByRaw("FIELD(time_logs.status, 'In Progress', 'Pending', 'Completed') ASC")
				->orderBy('time_logs.last_modified_at', 'desc')->get();

			// $task_list_qry = DB::connection('mysql_mes')->table('job_ticket AS jt')
			// 	->join('time_logs', 'time_logs.job_ticket_id', 'jt.job_ticket_id')
			// 	->where('jt.production_order', $production_order)
			// 	->where('jt.workstation', $workstation)
			// 	->where('jt.job_ticket_id', $job_ticket_id)
			// 	->where('time_logs.operator_id', Auth::user()->user_id)
			// 	->select('time_logs.time_log_id', 'jt.job_ticket_id', 'time_logs.operator_id', 'time_logs.machine_code', DB::raw('(SELECT process_name FROM process WHERE process_id = jt.process_id) AS process_name'), 'jt.production_order', 'time_logs.status', 'time_logs.from_time', 'time_logs.to_time', DB::raw('(SELECT SUM(good) FROM time_logs WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS total_good'),  DB::raw('(SELECT SUM(reject) FROM time_logs WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS total_reject'), 'time_logs.operator_name', 'jt.process_id', 'time_logs.good')
			// 	->orderByRaw("FIELD(time_logs.status, 'In Progress', 'Pending', 'Completed') ASC")
			// 	->orderBy('time_logs.last_modified_at', 'desc')->get();
		}

		$task_list = [];
		foreach ($task_list_qry as $row) {
			if ($time_logs) {
				$reference_type = ($workstation == 'Spotwelding') ? 'Spotwelding' : 'Time Logs';
				$reference_id = ($workstation == 'Spotwelding') ? $row->job_ticket_id : $row->time_log_id;
				$qa_inspection_status = $this->get_qa_inspection_status($reference_type, $reference_id);
				$helpers = DB::connection('mysql_mes')->table('helper')
					->where('time_log_id', $row->time_log_id)->get();

				$qry = DB::connection('mysql_mes')->table('time_logs')
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

            $uom_1_conversion_factor = DB::connection('mysql_mes')->table('uom_conversion')
				->join('uom', 'uom.uom_id', 'uom_conversion.uom_id')
				->where('uom.uom_name', 'not like', '%kilogram%')
                ->where('uom_conversion_id', $row->uom_conversion_id)
                ->sum('conversion_factor');

            $uom_2_conversion_factor = DB::connection('mysql_mes')->table('uom_conversion')
				->join('uom', 'uom.uom_id', 'uom_conversion.uom_id')
				->where('uom.uom_name', 'like', '%kilogram%')
                ->where('uom_conversion_id', $row->uom_conversion_id)
                ->sum('conversion_factor');

            $conversion_factor = $uom_2_conversion_factor / $uom_1_conversion_factor;

			$task_list[] = [
				'item_code' => '-',
				'job_ticket_id' => $row->job_ticket_id,
				'operator_id' => ($time_logs) ? $row->operator_id : null,
				'qa_inspection_status' => $qa_inspection_status,
				'machine_code' => ($time_logs) ? $row->machine_code : null,
				'process_name' => $row->process_name,
				'production_order' => $row->production_order,
				'description' => strtoupper($row->material) . ", " . $row->thickness . " mm",
				'sales_order' => '-',
				'material_request' => '-',
				'status' => ($time_logs) ? $row->status : 'Pending',
				'from_time' => ($time_logs) ? $row->from_time : null,
				'to_time' => ($time_logs) ? $row->to_time : null,
				'customer' => '-',
				'qty_to_manufacture' => $row->scrap_qty, 8,
				'total_good' => ($time_logs) ? $row->total_good : 0,
				'total_reject' => ($time_logs) ? $row->total_reject : 0,
				'stock_uom' => $row->uom_name,
				'project' => '-',
				'operator_name' => ($time_logs) ? $row->operator_name : null,
				'process_id' => $row->process_id,
				'good' => ($time_logs) ? $row->good : 0,
				'time_log_id' => ($time_logs) ? $row->time_log_id : null,
				'helpers' => $helpers,
				'count_helpers' => $count_helpers,
				'conversion_factor' => $conversion_factor, 8
			];
		}

		$batch_list = DB::connection('mysql_mes')->table('job_ticket')
			->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
			->where('time_logs.job_ticket_id', $job_ticket_id)
			->where('operator_id', $operator_id)
			->select('*', DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process_name'))
			->where('time_logs.status', 'Completed')->get();

		$in_progress_operator = DB::connection('mysql_mes')->table('job_ticket')
			->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
			->where('production_order', $production_order)
			->where('workstation', $workstation)
			->where('process_id', $job_ticket_details->process_id)
			->where('operator_id', '!=', $operator_id)
			->whereNotNull('operator_id')
			->select('operator_id', 'operator_nickname', DB::raw('SUM(good + reject) as completed_qty'))->groupBy('operator_id', 'operator_nickname')->get();

    	return view('tables.tbl_current_operator_task', compact('task_list', 'machine_code', 'batch_list', 'in_progress_operator'));
	}

	public function get_target_warehouse($operation_id){
        // return DB::connection('mysql_mes')->table('item_classification_warehouse')
		// 	->where('operation_id', $operation_id)->distinct()->pluck('target_warehouse');
			
			return DB::connection('mysql')->table('tabWarehouse')->where('disabled', 0)
            ->where('department', 'Fabrication')->where('is_group', 0)
            ->where('company', 'FUMACO Inc.')->pluck('name');
    }
	
	public function reject_task(Request $request){
		try {
			if(empty($request->reject_list)){
				return response()->json(['success' => 0, 'message' => 'Alert: Please select reject type']);

			}

			$data= $request->all();
			$reject_reason= $data['reject_list'];


			$now = Carbon::now();
			$time_log = DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $request->id)->first();
			$good_qty_after_transaction = $time_log->good - $request->rejected_qty;
			
            $update = [
                'last_modified_at' => $now->toDateTimeString(),
                'last_modified_by' => Auth::user()->employee_name,
                'good' => $good_qty_after_transaction,
                'reject' => $request->rejected_qty,
			];

			$reference_type = ($request->workstation == 'Spotwelding') ? 'Spotwelding' : 'Time Logs';
			$reference_id = ($request->workstation == 'Spotwelding') ? $time_log->job_ticket_id : $request->id;

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
			if($request->workstation != 'Spotwelding'){
				DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $request->id)->update($update);
			}

			$process_id = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $time_log->job_ticket_id)->first()->process_id;
			
			$this->updateProdOrderOps($request->production_order, $request->workstation, $process_id);
			$this->update_completed_qty_per_workstation($time_log->job_ticket_id);
			$this->update_produced_qty($request->production_order);

            return response()->json(['success' => 1, 'message' => 'Task has been updated.']);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
	}

	public function random_inspect_task($job_ticket_id){
		$job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')
			->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
			->where('time_log_id', $job_ticket_id)
			// ->where('qa_inspection_status', 'Psending')
			->where('time_logs.status', '!=', 'Pending')
			->select(DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process_name'), 'time_logs.*', 'job_ticket.production_order')
			->first();
		
		if (!$job_ticket_details) {
			return response()->json(['success' => 0, 'message' => 'Task already inspected.']);
		}

		$production_order_details = DB::connection('mysql_mes')->table('production_order')
			->where('production_order', $job_ticket_details->production_order)->first();

		return view('tables.tbl_qc_random_inspection', compact('job_ticket_details', 'production_order_details'));
	}

	public function reject_confirmation_task($job_ticket_id){
		$job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')
			->where('job_ticket_id', $job_ticket_id)
			->where('qa_inspection_status', 'Pending')
			->where('status', '!=', 'Pending')
			->select(DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process_name'), 'job_ticket.*')
			->first();
		
		if (!$job_ticket_details) {
			return response()->json(['success' => 0, 'message' => 'Task already inspected.']);
		}

		$production_order_details = DB::connection('mysql_mes')->table('production_order')
			->where('production_order', $job_ticket_details->production_order)->first();

		return view('tables.tbl_qc_reject_confirmation', compact('job_ticket_details', 'production_order_details'));
	}

	public function get_tasks_for_inspection(Request $request, $workstation, $production_order){
		$existing_production_order = DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->first();
		if(!$existing_production_order){
			return response()->json(['success' => 0, 'message' => 'Production Order <b>' . $production_order . '</b> not found.']);
		}

		if($workstation != 'Spotwelding'){
			$task_reject_confirmation = DB::connection('mysql_mes')->table('job_ticket')
				->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
				->join('quality_inspection', 'time_logs.time_log_id', 'quality_inspection.reference_id')
				->where('quality_inspection.reference_type', 'Time Logs')->where('production_order', $production_order)
				->where('workstation', $workstation)->whereIn('time_logs.status', ['In Progress', 'Completed'])
				->where('quality_inspection.status', 'For Confirmation')
				->select(DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process'), 'time_logs.from_time', DB::raw('time_logs.good + time_logs.reject AS completed_qty'), 'operator_name', 'time_logs.status', 'time_logs.time_log_id', 'time_logs.reject', 'qa_id','job_ticket.workstation','job_ticket.process_id', 'job_ticket.production_order')
				->orderBy('idx', 'asc')->get();

			$inspected = DB::connection('mysql_mes')->table('job_ticket')
				->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
				->join('quality_inspection', 'time_logs.time_log_id', 'quality_inspection.reference_id')
				->where('production_order', $production_order)
				->where('workstation', $workstation)
				->whereIn('time_logs.status', ['In Progress', 'Completed'])
				->where('quality_inspection.reference_type', 'Time Logs')
				->pluck('time_logs.time_log_id');

			$task_random_inspection = DB::connection('mysql_mes')->table('job_ticket')
				->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
				->where('production_order', $production_order)
				->where('workstation', $workstation)
				->whereIn('time_logs.status', ['In Progress', 'Completed'])
				->whereNotIn('time_logs.time_log_id', $inspected)
				->select(DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process'), 'time_logs.from_time', DB::raw('time_logs.good + time_logs.reject AS completed_qty'), 'operator_name', 'time_logs.status', 'time_logs.time_log_id', 'time_logs.reject','job_ticket.workstation', 'job_ticket.process_id')
				->orderBy('idx', 'asc')->get();
		}else{
			$task_reject_confirmation = DB::connection('mysql_mes')->table('job_ticket')
				->join('spotwelding_qty', 'spotwelding_qty.job_ticket_id', 'job_ticket.job_ticket_id')
				->join('quality_inspection', 'spotwelding_qty.time_log_id', 'quality_inspection.reference_id')
				->where('quality_inspection.reference_type', 'Time Logs')->where('production_order', $production_order)
				->where('workstation', $workstation)
				->whereIn('spotwelding_qty.status', ['In Progress', 'Completed'])
				->where('quality_inspection.status', 'For Confirmation')
				->select(DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process'), 'spotwelding_qty.from_time', DB::raw('spotwelding_qty.good + spotwelding_qty.reject AS completed_qty'), 'operator_name', 'spotwelding_qty.status', 'spotwelding_qty.time_log_id', 'spotwelding_qty.reject', 'qa_id','job_ticket.workstation','job_ticket.process_id', 'job_ticket.production_order')
				->orderBy('idx', 'asc')->get();

			$inspected = DB::connection('mysql_mes')->table('job_ticket')
				->join('spotwelding_qty', 'spotwelding_qty.job_ticket_id', 'job_ticket.job_ticket_id')
				->join('quality_inspection', 'spotwelding_qty.job_ticket_id', 'quality_inspection.reference_id')
				->where('production_order', $production_order)
				->where('workstation', $workstation)
				->whereIn('spotwelding_qty.status', ['In Progress', 'Completed'])
				->where('quality_inspection.reference_type', 'Spotwelding')
				->pluck('job_ticket.job_ticket_id');

			$task_random_inspection = DB::connection('mysql_mes')->table('job_ticket')
				->join('spotwelding_qty', 'spotwelding_qty.job_ticket_id', 'job_ticket.job_ticket_id')
				->where('production_order', $production_order)
				->where('workstation', $workstation)
				->whereIn('spotwelding_qty.status', ['In Progress', 'Completed'])
				->whereNotIn('job_ticket.job_ticket_id', $inspected)
				->select(DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process'), 'spotwelding_qty.from_time', DB::raw('spotwelding_qty.good + spotwelding_qty.reject AS completed_qty'), 'operator_name', 'spotwelding_qty.status', 'spotwelding_qty.time_log_id', 'spotwelding_qty.reject','job_ticket.workstation', 'job_ticket.process_id')
				->orderBy('idx', 'asc')->get();
		}

		return view('tables.tbl_production_process_inspection', compact('task_reject_confirmation', 'task_random_inspection', 'existing_production_order'));
	}

	public function maintenance_request(){
		$list = DB::connection('mysql_mes')->table('machine_breakdown')->orderBy('created_at', 'desc')->get();

		return view('maintenance_request_page', compact('list'));
	}

	public function stock_entry(){
		$list = DB::connection('mysql')->table('tabStock Entry')->where('production_order', 'LIKE', '%PROM-%')->paginate(10);

		return view('stock_entry_page', compact('list'));
	}

	public function add_helper(Request $request){
		$now = Carbon::now();
		if (Auth::user()->user_id == $request->helper_id) {
			return response()->json(['success' => 0, 'message' => "Please enter helper ID."]);
		}

		$helper_details = DB::connection('mysql_essex')->table('users')
			->where('user_id', $request->helper_id)->first();
			
		if(!$helper_details){
			return response()->json(['success' => 0, 'message' => "User not found."]);
		}

		$existing_helper = DB::connection('mysql_mes')->table('helper')->where('time_log_id', $request->time_log_id)
			->where('operator_id', $request->helper_id)->exists();
		
		if ($existing_helper) {
			return response()->json(['success' => 0, 'message' => 'Helper already exists.']);
		}else{
			$details = [
				'created_by' => Auth::user()->employee_name,
				'created_at' => $now->toDateTimeString(),
				'time_log_id' => $request->time_log_id,
				'operator_id' => $request->helper_id,
				'operator_name' => $helper_details->employee_name,
				'operator_nickname' => $helper_details->nick_name,
			];

			DB::connection('mysql_mes')->table('helper')->insert($details);
		}

		return response()->json(['success' => 1, 'message' => 'Helper(s) updated.']);	
	}

	public function get_helpers(Request $request){
		if ($request->display_all) {
			$qry = DB::connection('mysql_mes')->table('time_logs')
				->where('job_ticket_id', $request->job_ticket_id)
				->where('machine_code', $request->machine)
				->where('operator_id', $request->operator_id)
				->pluck('time_log_id');
			
			$helpers = DB::connection('mysql_mes')->table('helper')->whereIn('time_log_id', $qry)->distinct('operator_id')->get();
		}else{
			$helpers = DB::connection('mysql_mes')->table('helper')->where('time_log_id', $request->time_log_id)->get();
		}
		
		$helper_list = [];
		foreach ($helpers as $helper) {
			$helper_list[] = [
				'helper_id' => $helper->helper_id,
				'time_log_id' => $helper->time_log_id,
				'operator_id' => $helper->operator_id,
				'helper_name' => $helper->operator_name,
			];
		}

		return $helper_list;
	}

	public function delete_helper(Request $request){
		DB::connection('mysql_mes')->table('helper')->where('helper_id', $request->helper_id)->delete();

		return response()->json(['success' => 1, 'message' => 'Helper <b>' . $request->helper_name . '</b> has been removed.', 'job_ticket_id' => $request->job_ticket_id]);
	}

	public function production_schedule_per_workstation(Request $request){
		$date_range = CarbonPeriod::create(now(), now()->addDays(6));
		$date_period = [];
		foreach ($date_range as $period) {
			$regular_shift = DB::connection('mysql_mes')->table('shift')->where('shift_type', 'Regular Shift')->first();

			$shift_time_in = ($regular_shift) ? Carbon::parse($regular_shift->time_in) : 'No Regular Shift Found';
			$shift_time_out = ($regular_shift) ? Carbon::parse($regular_shift->time_out) : 'No Regular Shift Found';

			$special_shift = DB::connection('mysql_mes')->table('shift_schedule')->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
				->where('shift_schedule.date', $period->format('Y-m-d'))->where('shift.shift_type', 'Special Shift')->first();
			
			if($special_shift){
				$shift_time_in = Carbon::parse($special_shift->time_in);
				$shift_time_out = Carbon::parse($special_shift->time_out);
			}

			$overtime = DB::connection('mysql_mes')->table('shift_schedule')->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
				->where('shift_schedule.date', $period->format('Y-m-d'))->where('shift.shift_type', 'Overtime Shift')->first();

			if($overtime){
				$shift_time_out = Carbon::parse($overtime->time_out);
			}

			$date_period[] = [
				'date' => $period,
				'shift_time_in' => $shift_time_in->format('g:i A'),
				'shift_time_out' => $shift_time_out->format('g:i A'),
			];
		}

		$workstations = DB::connection('mysql_mes')->table('workstation')->orderBy('order_no', 'asc')->pluck('workstation_name', 'workstation_id');

		$task_list = [];
		foreach ($workstations as $index => $workstation) {
			$task_per_day = [];
			foreach ($date_range as $date) {
				$production_order_task = DB::connection('mysql_mes')->table('production_order')
					->join('job_ticket', 'production_order.production_order', 'job_ticket.production_order')
					->where('production_order.status', '!=', 'Cancelled')
					->where('job_ticket.workstation', $workstation)
					->whereDate('job_ticket.planned_start_date', $date)
					->select('production_order.sub_parent_item_code', 'production_order.parent_item_code', 'production_order.customer', 'production_order.sales_order', 'production_order.material_request', 'job_ticket.job_ticket_id', 'production_order.production_order', 'workstation', 'job_ticket.status', 'production_order.qty_to_manufacture', 'production_order.item_code', 'production_order.description', 'stock_uom', 'parts_category', DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) as process_name'), DB::raw('(SELECT color_legend FROM process WHERE process_id = job_ticket.process_id) as color_legend'), DB::raw('IFNULL(sales_order, material_request) as reference'))->get();

				$task_per_day[] = [
					'schedule_date' => $date->format('Y-m-d'),
					'task_list' =>  $production_order_task,
				];
			}

			$task_list[] = [
				'workstation_id' => $index,
				'workstation_name' => $workstation,
				'task_per_day' => $task_per_day,
			];
		}

		$filters = [
			'reference_no' => $request->select_reference,
			'customer' => $request->select_customer,
			'parent_item' => $request->select_parent_item,
			'sub_parent_item' => $request->select_sub_parent_item,
			'item' => $request->select_item,
		];

		return view('production_schedule_per_workstation', compact('task_list', 'date_period', 'filters'));
	}

	public function update_task_schedule(Request $request, $job_ticket_id){
		$now = Carbon::now();
		$values = [
			'planned_start_date' => $request->planned_start_date,
			'last_modified_by' => Auth::user()->employee_name,
			'last_modified_at' => $now->toDateTimeString()
		];
		
		DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)
			->update($values);

		return response()->json(['success' => 0, 'message' => 'Task updated.']);
	}

	public function operators_load_utilization(){
		return view('operators_load_utilization');
	}

	public function get_operators(){
		$d1 = Carbon::now()->subDays(7)->startOfDay();
		$d2 = Carbon::now()->addDays(1)->startOfDay();

		return DB::connection('mysql_mes')->table('time_logs')->whereNotNull('operator_id')
			->whereBetween('from_time', [$d1, $d2])
			// ->where(function($q) {
			// 	$q->whereNull('remarks')
			// 		->orWhere('remarks', '!=', 'Override');
			// })
			->distinct()->pluck('operator_name', 'operator_id');
	}

	public function get_operator_timelogs(){
		$d1 = Carbon::now()->subDays(7)->startOfDay();
		$d2 = Carbon::now()->addDays(1)->startOfDay();
		
		return DB::connection('mysql_mes')->table('time_logs')
			->join('job_ticket', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
			->whereNotNull('operator_id')->where('time_logs.status', 'Completed')
			->whereNotNull('from_time')
			->whereNotNull('to_time')
			->whereBetween('from_time', [$d1, $d2])
			->select('time_logs.*', 'job_ticket.workstation', 'job_ticket.production_order', 'job_ticket.planned_start_date', 'job_ticket.planned_end_date', DB::raw('(good + reject) as completed_qty'))
			// ->where(function($q) {
			// 	$q->whereNull('remarks')
			// 		->orWhere('remarks', '!=', 'Override');
			// })
			->get();
	}

	public function get_tbl_notif_dashboard(){
		$notifications = $this->getNotifications();

    	return view('tables.tbl_notification_dashboard', compact('notifications'));
	}

    public function operator_spotwelding_dashboard(){
		 $tabWorkstation= DB::connection('mysql_mes')->table('workstation')->where('workstation_name', 'Spotwelding')
        	->select('workstation_name', 'workstation_id')->first();

        $workstation_list = DB::connection('mysql_mes')
        ->table('workstation as w')
        ->join('operation as op', 'op.operation_id', 'w.operation_id')
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

	public function get_spotwelding_current_operator_task_details(Request $request, $operator_id){
		$job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')
			->where('job_ticket_id', $request->job_ticket_id)->first();
		if(!$job_ticket_details){
			return response()->json(['success' => 0, 'message' => 'Task not found.']);
		}

		$status = $job_ticket_details->status;
		$machine_code = $request->machine_code;

		$time_logs = DB::connection('mysql_mes')->table('time_logs')->where('job_ticket_id', $request->job_ticket_id)
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
				->join('time_logs', 'time_logs.job_ticket_id', 'jt.job_ticket_id')
				->where('po.production_order', $request->production_order)
				->where('jt.workstation', $request->workstation)
				->where('jt.job_ticket_id', $request->job_ticket_id)
				->where('time_logs.operator_id', Auth::user()->user_id)
				->select('po.item_code', 'time_logs.time_log_id', 'jt.job_ticket_id', 'time_logs.operator_id', 'time_logs.machine_code', DB::raw('(SELECT process_name FROM process WHERE process_id = jt.process_id) AS process_name'), 'po.production_order', 'po.description', 'po.sales_order', 'po.material_request', 'time_logs.status', 'time_logs.from_time', 'time_logs.to_time', 'po.customer', 'po.qty_to_manufacture', DB::raw('(SELECT SUM(good) FROM time_logs WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS total_good'),  DB::raw('(SELECT SUM(reject) FROM time_logs WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS total_reject'), 'po.stock_uom', 'po.project', 'time_logs.operator_name', 'jt.process_id', 'time_logs.good', 'time_logs.process_description')
				->orderByRaw("FIELD(time_logs.status, 'In Progress', 'Pending', 'Completed') ASC")
				->orderBy('time_logs.last_modified_at', 'desc')->get();
		}

		$task_list = [];
		foreach ($task_list_qry as $row) {
			if ($time_logs) {
				$reference_type = ($request->workstation == 'Spotwelding') ? 'Spotwelding' : 'Time Logs';
				$reference_id = ($request->workstation == 'Spotwelding') ? $row->job_ticket_id : $row->time_log_id;
				$qa_inspection_status = $this->get_qa_inspection_status($reference_type, $reference_id);
				$helpers = DB::connection('mysql_mes')->table('helper')
					->where('time_log_id', $row->time_log_id)->get();

				$qry = DB::connection('mysql_mes')->table('time_logs')
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
				'process_description' => ($time_logs) ? $row->process_description : null
			];
		}

		$batch_list = DB::connection('mysql_mes')->table('job_ticket')
			->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
			->where('production_order', $request->production_order)
			->where('workstation', $request->workstation)
			->where('process_id', $job_ticket_details->process_id)
			->where('operator_id', $operator_id)
			->select('*', DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process_name'))
			->where('time_logs.status', 'Completed')->get();

		$in_progress_operator = DB::connection('mysql_mes')->table('job_ticket')
			->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
			->where('production_order', $request->production_order)
			->where('workstation', $request->workstation)
			->where('process_id', $job_ticket_details->process_id)
			->where('operator_id', '!=', $operator_id)
			->whereNotNull('operator_id')
			->select('operator_id', 'operator_nickname', DB::raw('SUM(good + reject) as completed_qty'))->groupBy('operator_id', 'operator_nickname')->get();

		$bom_parts = $this->get_production_order_bom_parts($request->production_order);

		$timelogs = DB::connection('mysql_mes')->table('time_logs')
			->join('job_ticket', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
			->where('job_ticket.job_ticket_id', $request->job_ticket_id)
			->select('job_ticket.process_id', 'time_logs.*', DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process_name'))->get();

		$logs = [];
		foreach ($timelogs as $log) {
			$from = Carbon::parse($log->from_time);
			$to = Carbon::parse($log->to_time);

			$days = $from->diffInDays($to);
			$hours = $from->copy()->addDays($days)->diffInHours($to);
			$minutes = $from->copy()->addDays($days)->addHours($hours)->diffInMinutes($to);
			$dur_days = ($days > 0) ? $days .'d' : null;
			$dur_hours = ($hours > 0) ? $hours .'h' : null;
			$dur_minutes = ($minutes > 0) ? $minutes .'m' : null;

			$logs[] = [
				'time_log_id' => $log->time_log_id,
				'process_name' => $log->process_name,
				'good' => $log->good,
				'process_description' => $log->process_description,
				'from_time' => ($log->from_time) ? Carbon::parse($log->from_time)->format('M-d-Y h:i A') : '--',
				'to_time' => ($log->to_time) ? Carbon::parse($log->to_time)->format('M-d-Y h:i A') : '--',
				'completed_qty' => $log->good + $log->reject,
				'duration' => $dur_days .' '. $dur_hours . ' '. $dur_minutes,
				'status' => $log->status,
				'machine' => $log->machine_code,
				'operator_name' => $log->operator_name
			];
		}

    	return view('tables.tbl_spotwelding_current_operator_task', compact('task_list', 'machine_code', 'batch_list', 'in_progress_operator', 'helpers', 'count_helpers', 'bom_parts', 'logs'));
	}

	public function get_production_order_bom_parts($production_order){
		// temporary
		$production_order_details = DB::connection('mysql_mes')->table('production_order')
			->where('production_order', $production_order)->first();

		$bom_parts = DB::connection('mysql_mes')->table('production_order')
			->where('parent_item_code', $production_order_details->parent_item_code)
			->where('sub_parent_item_code', $production_order_details->item_code)
			->where('sales_order', $production_order_details->sales_order)
			->where('material_request', $production_order_details->material_request)
			// ->select('production_order', 'parent_item_code', 'sub_parent_item_code', 'item_code')
			->get();

		$bom_parts_arr = [];
		foreach($bom_parts as $part){
			$time_log = DB::connection('mysql_mes')->table('job_ticket')
				->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
				->where('job_ticket.production_order', $production_order)
				->where('time_logs.process_description', 'LIKE', "%".$part->item_code."%")
				->orderByRaw("FIELD(time_logs.status, 'In Progress', 'Completed') ASC")
				->first();

			$status = 'Not Started';
			if ($time_log) {
				$status = $time_log->status;
			}

			$bom_parts_arr[] = [
				'item_code' => $part->item_code,
				'production_order' => $part->production_order,
				'parts_category' => $part->parts_category,
				'status' => $status,
			];
		}

		return $bom_parts_arr;
	}

	public function logout_spotwelding(){
        Auth::guard('web')->logout();
        $route = '/operator/Spotwelding';
        return redirect($route);
    }

    public function get_users(Request $request){
		$list = DB::connection('mysql_mes')->table('user')
			->join('operation as op','op.operation_id','user.operation_id')
			->join('user_group as ug', 'ug.user_group_id','user.user_group_id')
			->select('user.*','op.operation_name', "ug.module", 'ug.user_role')
			->where(function($q) use ($request) {
					$q->where('op.operation_name', 'LIKE', '%'.$request->search_string.'%')
					->orWhere('user.user_access_id', 'LIKE', '%'.$request->search_string.'%')
					->orWhere('user.employee_name', 'LIKE', '%'.$request->search_string.'%')
					->orWhere('ug.user_role', 'LIKE', '%'.$request->search_string.'%')
					->orWhere('ug.module', 'LIKE', '%'.$request->search_string.'%');
		    })
            ->orderBy('user.user_id', 'desc')->get();
        
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

        $users = $paginatedItems;
    	
    	return view('tables.tbl_users', compact('users'));
    }

    public function save_user(Request $request){
        $now = Carbon::now();
        $data = [
            'user_access_id' => $request->user_access_id,
            'employee_name' => $request->employee_name,
            'user_group_id' => $request->user_role,
            'operation_id' => $request->operation,
            'created_by' => Auth::user()->employee_name,
            'created_at' => $now->toDateTimeString()
        ];
        
        DB::connection('mysql_mes')->table('user')->insert($data);

        return response()->json(['message' => 'User has been saved.']);
    }

    public function update_user(Request $request){
    	$now = Carbon::now();

    	$data = [
    		'user_access_id' => $request->user_access_id,
            'employee_name' => $request->employee_name,
            'user_group_id' => $request->user_role,
            'operation_id' => $request->operation,
    		'last_modified_by' => Auth::user()->employee_name,
    		'last_modified_at' => $now->toDateTimeString()
    	];

    	DB::connection('mysql_mes')->table('user')->where('user_id', $request->user_id)->update($data);

    	return response()->json(['message' => 'User has been updated.']);
    }

    public function delete_user(Request $request){
    	DB::connection('mysql_mes')->table('user')->where('user_id', $request->user_id)->delete();

    	return response()->json(['message' => 'User has been deleted.']);
    }

    public function create_stock_entry(Request $request, $production_order){
		DB::connection('mysql')->beginTransaction();
		try {
			$existing_ste_transfer = DB::connection('mysql')->table('tabStock Entry')
				->where('production_order', $production_order)
				->where('purpose', 'Material Transfer for Manufacture')
				->where('docstatus', 1)->exists();

			if(!$existing_ste_transfer){
				return response()->json(['success' => 0, 'message' => 'Materials unavailable.']);
			}

			$production_order_details = DB::connection('mysql')->table('tabProduction Order')
				->where('name', $production_order)->first();

			$produced_qty = $production_order_details->produced_qty + $request->fg_completed_qty;
			if($produced_qty >= (int)$production_order_details->qty && $production_order_details->material_transferred_for_manufacturing > 0){
				$pending_mtfm_count = DB::connection('mysql')->table('tabStock Entry as ste')
					->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
					->where('ste.production_order', $production_order)->where('purpose', 'Material Transfer for Manufacture')
					->where('ste.docstatus', 0)->count();
				
				if($pending_mtfm_count > 0){
					return response()->json(['success' => 0, 'message' => 'There are pending material request for issue.']);
				}
			}

			$mes_production_order_details = DB::connection('mysql_mes')->table('production_order')
				->where('production_order', $production_order)->first();

			if($mes_production_order_details->is_stock_item < 1){
				return redirect('/create_bundle_feedback/'. $production_order .'/' . $request->fg_completed_qty);
			}

			$now = Carbon::now();

			$latest_pro = DB::connection('mysql')->table('tabStock Entry')->max('name');
			$latest_pro_exploded = explode("-", $latest_pro);
			$new_id = $latest_pro_exploded[1] + 1;
			$new_id = str_pad($new_id, 5, '0', STR_PAD_LEFT);
			$new_id = 'STEM-'.$new_id;

			$production_order_items = DB::connection('mysql')->table('tabProduction Order Item')
				->where('parent', $production_order)->orderBy('idx', 'asc')
				// ->where('transferred_qty', '>', 0)
				->get();

			$receiving_warehouse = ['P2 - Housing Temporary - FI1'];
			$docstatus = (in_array($mes_production_order_details->fg_warehouse, $receiving_warehouse)) ? 0 : 1;

			if(count($production_order_items) < 1){
				return response()->json(['success' => 0, 'message' => 'Materials unavailable.']);
			}

			$stock_entry_detail = [];
			foreach ($production_order_items as $index => $row) {
				$bom_material = DB::connection('mysql')->table('tabBOM Item')
					->where('parent', $production_order_details->bom_no)
					->where('item_code', $row->item_code)->first();
				
				if(!$bom_material){
					$valuation_rate = DB::connection('mysql')->table('tabBin')
						->where('item_code', $row->item_code)
						->where('warehouse', $production_order_details->wip_warehouse)
						->sum('valuation_rate');
				}

				$base_rate = ($bom_material) ? $bom_material->base_rate : $valuation_rate;

				$qty_per_item = $row->required_qty / $mes_production_order_details->qty_to_manufacture;
				
				$qty = $qty_per_item * $request->fg_completed_qty;

				$is_uom_whole_number = DB::connection('mysql')->table('tabUOM')->where('name', $row->stock_uom)->first();
				if($is_uom_whole_number && $is_uom_whole_number->must_be_whole_number == 1){
					$qty = round($qty);
				}

				$consumed_qty = DB::connection('mysql')->table('tabStock Entry as ste')
					->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
					->where('ste.production_order', $production_order)
					->where('sted.item_code', $row->item_code)->where('purpose', 'Manufacture')
					->where('ste.docstatus', 1)->sum('qty');

				$remaining_transferred_qty = $row->transferred_qty - $consumed_qty;

				if($remaining_transferred_qty < $qty){
					return response()->json(['success' => 0, 'message' => 'Insufficient transferred qty for ' . $row->item_code . ' in ' . $production_order_details->wip_warehouse]);
				}

				if($qty <= 0){
					return response()->json(['success' => 0, 'message' => 'Qty cannot be less than or equal to 0 for ' . $row->item_code . ' in ' . $production_order_details->wip_warehouse]);
				}

				$actual_qty = DB::connection('mysql')->table('tabBin')->where('item_code', $row->item_code)
					->where('warehouse', $production_order_details->wip_warehouse)->sum('actual_qty');

				if($docstatus == 1){
					if($qty > $actual_qty){
						return response()->json(['success' => 0, 'message' => 'Insufficient stock for ' . $row->item_code . ' in ' . $production_order_details->wip_warehouse]);
					}
				}

				$stock_entry_detail[] = [
					'name' =>  uniqid(),
					'creation' => $now->toDateTimeString(),
					'modified' => $now->toDateTimeString(),
					'modified_by' => Auth::user()->email,
					'owner' => Auth::user()->email,
					'docstatus' => $docstatus,
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
					'basic_amount' => $base_rate * $qty,
					'sample_quantity' => 0,
					'uom' => $row->stock_uom,
					'basic_rate' => $base_rate,
					'description' => $row->description,
					'barcode' => null,
					'conversion_factor' => ($bom_material) ? $bom_material->conversion_factor : 1,
					'item_code' => $row->item_code,
					'retain_sample' => 0,
					'qty' => $qty,
					'bom_no' => null,
					'allow_zero_valuation_rate' => 0,
					'material_request_item' => null,
					'amount' => $base_rate * $qty,
					'batch_no' => null,
					'valuation_rate' => $base_rate,
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
				'docstatus' => $docstatus,
				'parent' => $new_id,
				'parentfield' => 'items',
				'parenttype' => 'Stock Entry',
				'idx' => count($stock_entry_detail) + 1,
				't_warehouse' => $mes_production_order_details->fg_warehouse,
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
				'bom_no' => null,
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
				'docstatus' => $docstatus,
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
				'from_warehouse' => null,
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

			if($docstatus == 1){

				$produced_qty = $production_order_details->produced_qty + $request->fg_completed_qty;
			
				$production_data = [
					'modified' => $now->toDateTimeString(),
					'modified_by' => Auth::user()->email,
					'produced_qty' => $produced_qty,
					'status' => ($produced_qty == $production_order_details->qty) ? 'Completed' : $production_order_details->status
				];

				DB::connection('mysql')->table('tabProduction Order')->where('name', $production_order)->update($production_data);

				$this->update_bin($new_id);
				$this->create_stock_ledger_entry($new_id);
				$this->create_gl_entry($new_id);
				
				DB::connection('mysql_mes')->transaction(function() use ($now, $request, $production_order_details, $mes_production_order_details){
					$manufactured_qty = $production_order_details->produced_qty + $request->fg_completed_qty;
					$status = ($manufactured_qty == $production_order_details->qty) ? 'Completed' : $mes_production_order_details->status;

					if($status == 'Completed'){
						$production_data_mes = [
							'last_modified_at' => $now->toDateTimeString(),
							'last_modified_by' => Auth::user()->email,
							'feedback_qty' => $manufactured_qty,
							'produced_qty' => $manufactured_qty,
							'status' => $status
						];
					}else{
						$production_data_mes = [
							'last_modified_at' => $now->toDateTimeString(),
							'last_modified_by' => Auth::user()->email,
							'feedback_qty' => $manufactured_qty,
						];
					}

					DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order_details->name)->update($production_data_mes);
					$this->insert_production_scrap($production_order_details->name, $request->fg_completed_qty);
				});
			}
			$data = array(
                'posting_date'  => $now->format('Y-m-d'),
                'posting_time'  => $now->format('H:i:s'),
                'ste'           => $new_id,
				'sales_order_no'=> $mes_production_order_details->sales_order,
				'mreq'			=> $production_order_details->material_request,
                'item_code'     => $production_order_details->production_item,
				'item_name'     => $production_order_details->item_name,
				'customer'		=> $mes_production_order_details->customer,
				'feedbacked_by' => Auth::user()->email,
				'completed_qty' => $request->fg_completed_qty, 
				'uom'			=> $production_order_details->stock_uom
			);
			$recipient= DB::connection('mysql_mes')
                ->table('email_trans_recipient')
				->where('email_trans', "Feedbacking")
				->where('email', 'like','%@fumaco.local%')
                ->select('email')
                ->get();
			if(count($recipient) > 0){
				if($mes_production_order_details->parent_item_code == $mes_production_order_details->sub_parent_item_code && $mes_production_order_details->sub_parent_item_code == $mes_production_order_details->item_code){
					foreach ($recipient as $row) {
						Mail::to($row->email)->send(new SendMail_feedbacking($data));
					}	
				}
			}
			$feedbacked_timelogs = [
                'production_order'  => $mes_production_order_details->production_order,
                'ste_no'           => $new_id,
                'item_code'     => $production_order_details->production_item,
				'item_name'     => $production_order_details->item_name,
				'feedbacked_qty' => $request->fg_completed_qty, 
				'from_warehouse'=> $production_order_details->wip_warehouse,
				'to_warehouse' => $mes_production_order_details->fg_warehouse,
				'transaction_date'=>$now->format('Y-m-d'),
				'transaction_time' =>$now->format('G:i:s'),
				'created_at'  => $now->toDateTimeString(),
				'created_by'  =>  Auth::user()->email,
			];
			DB::connection('mysql_mes')->table('feedbacked_logs')->insert($feedbacked_timelogs);
			DB::connection('mysql')->commit();

			return response()->json(['success' => 1, 'message' => 'Stock Entry has been created.']);
		} catch (Exception $e) {
			DB::connection('mysql')->rollback();
			return response()->json(['success' => 0, 'message' => 'There was a problem create stock entry']);
		}
    }

	// create stock ledger entry for manufacture ste
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
        try {
            $now = Carbon::now();

            $stock_entry_detail = DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $stock_entry)->get();

            $latest_id = DB::connection('mysql')->table('tabBin')->max('name');
            $latest_id_exploded = explode("/", $latest_id);
            $new_id = $latest_id_exploded[1] + 1;

            $stock_entry_qry = DB::connection('mysql')->table('tabStock Entry')->where('name', $stock_entry)->first();

            $stock_entry_detail = DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $stock_entry)->get();
            
            $s_data_insert = [];
            $d_data = [];
            foreach($stock_entry_detail as $row){
               
                    if($row->s_warehouse){
                        $bin_qry = DB::connection('mysql')->table('tabBin')->where('warehouse', $row->s_warehouse)
                        ->where('item_code', $row->item_code)->first();
                    if (!$bin_qry) {
                               
                        $new_id = $new_id + 1;
                        $new_id = str_pad($new_id, 7, '0', STR_PAD_LEFT);
                        $id = 'BINM/'.$new_id;

                        $bin = [
                            'name' => $id,
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
                            'warehouse' => $row->s_warehouse,
                            'stock_value' => $row->valuation_rate * $row->transfer_qty,
                            '_user_tags' => null,
                            'valuation_rate' => $row->valuation_rate,
                        ];

                        DB::connection('mysql')->table('tabBin')->insert($bin);
                    }else{
                        $bin = [
                            'modified' => $now->toDateTimeString(),
                            'modified_by' => Auth::user()->email,
                            'actual_qty' => $bin_qry->actual_qty - $row->transfer_qty,
                            'stock_value' => $bin_qry->valuation_rate * $row->transfer_qty,
                            'valuation_rate' => $bin_qry->valuation_rate,
                        ];
        
                        DB::connection('mysql')->table('tabBin')->where('name', $bin_qry->name)->update($bin);
                    }
                    
                }

                if($row->t_warehouse){
                    $bin_qry = DB::connection('mysql')->table('tabBin')->where('warehouse', $row->t_warehouse)
                        ->where('item_code', $row->item_code)->first();
                    if (!$bin_qry) {
                        
                        $new_id = $new_id + 1;
                        $new_id = str_pad($new_id, 7, '0', STR_PAD_LEFT);
                        $id = 'BINM/'.$new_id;

                        $bin = [
                            'name' => $id,
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
                            'warehouse' => $row->t_warehouse,
                            'stock_value' => $row->valuation_rate * $row->transfer_qty,
                            '_user_tags' => null,
                            'valuation_rate' => $row->valuation_rate,
                        ];

                        DB::connection('mysql')->table('tabBin')->insert($bin);
                    }else{
                        $bin = [
                            'modified' => $now->toDateTimeString(),
                            'modified_by' => Auth::user()->email,
                            'actual_qty' => $bin_qry->actual_qty + $row->transfer_qty,
                            'stock_value' => $bin_qry->valuation_rate * $row->transfer_qty,
                            'valuation_rate' => $bin_qry->valuation_rate,
                        ];
        
                        DB::connection('mysql')->table('tabBin')->where('name', $bin_qry->name)->update($bin);
                    }
                }
            }
            
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage(), 'id' => $stock_entry]);
        }
    }
	
	public function create_gl_entry($stock_entry){
		$now = Carbon::now();
		$stock_entry_qry = DB::connection('mysql')->table('tabStock Entry')->where('name', $stock_entry)->first();
		$stock_entry_detail = DB::connection('mysql')->table('tabStock Entry Detail')
			->where('parent', $stock_entry)
			->select('s_warehouse', 't_warehouse', DB::raw('SUM(basic_amount) as basic_amount'), 'parent', 'cost_center', 'expense_account')
			->groupBy('s_warehouse', 't_warehouse', 'parent', 'cost_center', 'expense_account')
			->get();

		$latest_name = DB::connection('mysql')->table('tabGL Entry')->max('name');
		$latest_name_exploded = explode("L", $latest_name);
		$new_id = $latest_name_exploded[1] + 1;

		$gl_entry = [];
		foreach ($stock_entry_detail as $row) {
			$id = str_pad($new_id, 7, '0', STR_PAD_LEFT);
			$id = 'MGL'.$id;

			$new_id = $new_id + 1;

			if($row->s_warehouse){
				$credit = $row->basic_amount;
				$debit = 0;
				$account = $row->s_warehouse;
			}else{
				$credit = 0;
				$debit = $row->basic_amount;
				$account = $row->t_warehouse;
			}

			$gl_entry[] = [
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
		}	
		
		DB::connection('mysql')->table('tabGL Entry')->insert($gl_entry);
	}

    public function updatejt(){
    	$jt = DB::connection('mysql_mes')->table('job_ticket')
    		->where('workstation', 'Painting')->get();

    	$data = [];
    	foreach ($jt as $key => $value) {
    		$data[] = [
				// "job_ticket_id" => 86
				"production_order" => $value->production_order,
				"workstation" => $value->workstation,
				"process_id" => 121,
				"idx" => $value->idx,
				"planned_start_date" => $value->planned_start_date,
				"planned_end_date" => $value->planned_end_date,
				"completed_qty" => $value->completed_qty,
				"remarks" => $value->remarks,
				"item_feedback" => $value->item_feedback,
				"status" => $value->status,
				"created_by" => $value->created_by,
				"created_at" => $value->created_at,
				"last_modified_by" => $value->last_modified_by,
				"last_modified_at" => $value->last_modified_at,
				"bom_operation_id" => $value->bom_operation_id,
    		];
    		$data[] = [
				// "job_ticket_id" => 86
				"production_order" => $value->production_order,
				"workstation" => $value->workstation,
				"process_id" => 122,
				"idx" => $value->idx,
				"planned_start_date" => $value->planned_start_date,
				"planned_end_date" => $value->planned_end_date,
				"completed_qty" => $value->completed_qty,
				"remarks" => $value->remarks,
				"item_feedback" => $value->item_feedback,
				"status" => $value->status,
				"created_by" => $value->created_by,
				"created_at" => $value->created_at,
				"last_modified_by" => $value->last_modified_by,
				"last_modified_at" => $value->last_modified_at,
				"bom_operation_id" => $value->bom_operation_id,
    		];
    		
    		# code...
    	}

    	DB::connection('mysql_mes')->table('job_ticket')
    		->where('workstation', 'Painting')->delete();

    	DB::connection('mysql_mes')->table('job_ticket')->insert($data);

    	return $data;
	}
	
    public function report_index(){
		$workstation= DB::connection('mysql_mes')->table('workstation AS w')
                    ->join("operation as op","op.operation_id", "w.operation_id")
                    ->select("w.workstation_name", "w.workstation_id")
                    ->where('op.operation_name','Fabrication')
                    ->orderBy('w.workstation_name', 'asc')
                    ->get();

        $process= DB::connection('mysql_mes')->table('process AS p')
                    ->select("p.process_name", "p.process_id")
                    ->orderBy('p.process_name', 'asc')
                    ->get();
        
        $parts= DB::connection('mysql_mes')->table('production_order AS po')
                    ->select("po.parts_category")
                    ->where('po.parts_category', '!=', null)
                    ->groupBy('po.parts_category')
                    ->orderBy('po.parts_category', 'asc')
                    ->get();
        
        $sacode= DB::connection('mysql_mes')->table('production_order AS po')
                    ->select("po.item_code")
                    ->where('po.item_code', '!=', null)
                    ->groupBy('po.item_code')
                    ->orderBy('po.item_code', 'asc')
                    ->get();

        $mes_user_operations = DB::connection('mysql_mes')->table('user')
					->join('operation', 'operation.operation_id', 'user.operation_id')
					->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
                    ->where('module', 'Production')
                    ->where('user_access_id', Auth::user()->user_id)->pluck('operation_name')->toArray();

		return view('reports.index', compact('workstation', 'process', 'parts','sacode', 'mes_user_operations'));
	}

	public function painting_ready_list(Request $request){
		$orders = DB::connection('mysql_mes')->table('production_order as prod')
			->join('job_ticket as tsd', 'tsd.production_order', 'prod.production_order')
            ->whereNotIn('prod.status', ['Cancelled', 'Completed'])->where('tsd.workstation', 'Painting')
            ->where(function($q) use ($request) {
                $q->where('prod.production_order', 'LIKE', '%'.$request->q.'%')
                ->orWhere('prod.item_code', 'LIKE', '%'.$request->q.'%')
                ->orWhere('prod.customer', 'LIKE', '%'.$request->q.'%');
            })
            ->distinct('prod.production_order')->select('prod.*')
            ->orderBy('last_modified_at', 'desc')->get();
        
        $data = [];
        foreach($orders as $row){
			$qty_ready_for_painting = DB::connection('mysql_mes')->table('job_ticket')
				->where('production_order', $row->production_order)
				->where('workstation', '!=', 'Painting')
				->min('completed_qty');
			
			if($qty_ready_for_painting > 0){
				$processes = DB::connection('mysql_mes')->table('job_ticket as tsd')
					->join('process as p', 'p.process_id', 'tsd.process_id')
					->where('tsd.production_order', $row->production_order)
					->where('tsd.workstation','Painting')
					->join('workstation as work','work.workstation_name','tsd.workstation')
					->select('p.process_name','tsd.status','tsd.completed_qty', 'tsd.planned_start_date')
					->get();

				$painting_completed_qty = collect($processes)->min('completed_qty');
				$painting_planned_start_date = collect($processes)->min('planned_start_date');
				
				$status = 'Ready for Painting';
				if($painting_completed_qty > 0){
					$status = 'Painting In Progress';
				}

				if($painting_completed_qty == $row->qty_to_manufacture){
					$status = 'Painting Completed';
				}

				if($painting_planned_start_date){
					$planned_start_date = Carbon::parse($painting_planned_start_date)->format('M-d-Y');
				}else{
					$planned_start_date = 'Unscheduled';
				}
           
				$data[]=[
					'sales_order' => $row->sales_order,
					'material_request' => $row->material_request,
					'customer' => $row->customer,
					'item_code' => $row->item_code,
					'item_description'=> $row->description,
					'stock_uom' => $row->stock_uom,
					'balance_qty' => ($row->qty_to_manufacture - $row->produced_qty),
					'completed_qty'=> $row->produced_qty,
					'qty'=> $row->qty_to_manufacture, 
					'production_order' => $row->production_order,
					'job_ticket'=> $processes,
					'status'=> $status,
					'qty_ready_for_painting' => $qty_ready_for_painting,
					'planned_start_date' => $planned_start_date
				];
			}	
		}

		return view('tables.tbl_ready_for_painting', compact('data'));		
	}

	public function print_fg_transfer_slip($production_order){
        $production_order_details = DB::connection('mysql_mes')->table('production_order')
            ->where('production_order', $production_order)->first();

        $ste_details = DB::connection('mysql')->table('tabStock Entry as ste')
            ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
            ->where('production_order', $production_order)->where('ste.purpose', 'Manufacture')
            ->whereNull('s_warehouse')->whereNotNull('t_warehouse')
            ->select('ste.name', 'sted.item_code', 'sted.description', 'sted.qty', 'ste.posting_date', 'sted.t_warehouse', 'sted.stock_uom')
            ->orderBy('ste.creation', 'desc')
            ->first();

        $transferred_qty = DB::connection('mysql')->table('tabStock Entry as ste')
            ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
            ->where('production_order', $production_order)->where('ste.purpose', 'Manufacture')
            ->whereNull('s_warehouse')->whereNotNull('t_warehouse')
            ->sum('sted.qty');

        $data = [
            'production_order' => $production_order_details->production_order,
            'sales_order' => $production_order_details->sales_order,
            'material_request' => $production_order_details->material_request,
            'transaction_date' => $ste_details->posting_date,
            'customer' => $production_order_details->customer,
            'project' => $production_order_details->project,
            'ste_no' => $ste_details->name,
            'item_code' => $ste_details->item_code,
            'description' => $ste_details->description,
            't_warehouse' => $ste_details->t_warehouse,
            'req_qty' => $production_order_details->qty_to_manufacture,
            'transferred_qty' => number_format($transferred_qty),
            'balance_qty' => $production_order_details->qty_to_manufacture - $transferred_qty,
            'stock_uom' => $ste_details->stock_uom,
        ];

        return view('inventory.print_transfer_slip', compact('data'));
	}
	
	public function item_query(Request $request){
		return DB::connection('mysql')->table('tabItem')->where('is_stock_item', 1)->where('disabled', 0)
				->where('has_variants', 0)
				->where('name', 'like', '%'.$request->term.'%')
				->select('name as value', 'name as id')->orderBy('modified', 'desc')->limit(5)->get();
	}

	public function warehouse_query(Request $request){
		return DB::connection('mysql')->table('tabWarehouse')->where('disabled', 0)->where('is_group', 0)
			->where('company', 'FUMACO Inc.')
			->where('name', 'like', '%'.$request->term.'%')
			->select('name as value', 'name as id')->orderBy('modified', 'desc')->limit(5)->get();
	}

	public function operation_query(){
		return DB::connection('mysql_mes')->table('operation')
			->orderBy('last_modified_at', 'desc')->get();
	}

	public function workstation_query($operation_id){
		return DB::connection('mysql_mes')->table('workstation')->where('operation_id', $operation_id)
			->orderBy('workstation_name', 'asc')->get();
	}

	public function process_query($workstation_id){
		return DB::connection('mysql_mes')->table('process_assignment')
			->join('process', 'process_assignment.process_id', 'process.process_id')
			->where('workstation_id', $workstation_id)
			->select('process.process_id', 'process.process_name')
			->distinct('process.process_id', 'process.process_name')
			->orderBy('process_name', 'asc')->get();
	}

	public function get_pending_material_transfer_for_manufacture($production_order, Request $request){
		$production_order_details = DB::connection('mysql_mes')->table('production_order')
            ->join('delivery_date', function ($join) {
                $join->on('delivery_date.parent_item_code', '=', 'production_order.parent_item_code')
                    ->on('delivery_date.reference_no', '=', 'production_order.sales_order')
                    ->orOn('delivery_date.reference_no', '=', 'production_order.material_request');  //Inner join new table for Delivery Date
            })
            ->where('production_order.production_order', $production_order)
            ->select('production_order.*', 'delivery_date.rescheduled_delivery_date')
            ->first();
				
		$q = DB::connection('mysql')->table('tabStock Entry as ste')
			->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
			->where('ste.production_order', $production_order)->where('purpose', 'Material Transfer for Manufacture')
			->select('sted.name as sted_id', 'ste.name', 'sted.item_code', 'sted.description', 'sted.qty', 'sted.stock_uom', 'sted.s_warehouse', 'sted.t_warehouse', 'sted.status', 'sted.session_user', 'sted.date_modified')
			->where('ste.docstatus', 0)->get();

		$components = $parts = [];
		foreach ($q as $row) {
			$item_details = DB::connection('mysql')->table('tabItem')->where('name', $row->item_code)->first();
            // get item stock based on feedbacked qty for housing and other items with sub assemblies
            $has_production_order = DB::connection('mysql_mes')->table('production_order')
                ->where('item_code', $row->item_code)->where('parent_item_code', $production_order_details->parent_item_code)
                ->where('sales_order', $production_order_details->sales_order)
                ->where('material_request', $production_order_details->material_request)
                ->where('sub_parent_item_code', $production_order_details->item_code)->first();
				
			$available_qty_at_source = DB::connection('mysql')->table('tabBin')->where('item_code', $row->item_code)
				->where('warehouse', $row->s_warehouse)->sum('actual_qty');

			$available_qty_at_wip = DB::connection('mysql')->table('tabBin')->where('item_code', $row->item_code)
				->where('warehouse', $row->t_warehouse)->sum('actual_qty');

            if($has_production_order){
                $parts[] = [
					'name' => $row->name,
					'item_code' => $row->item_code,
					'item_image' => $item_details->item_image_path,
					'description' => $row->description,
					's_warehouse' => $row->s_warehouse,
					't_warehouse' => $row->t_warehouse,
					'qty' => $row->qty,
					'stock_uom' => $row->stock_uom,
					'status' => $row->status,
					'available_qty_at_source' => $available_qty_at_source * 1,
					'available_qty_at_wip' => $available_qty_at_wip * 1,
                    'production_order' => $has_production_order->production_order,
                ];
            }else{
                $components[] = [
					'name' => $row->name,
					'item_code' => $row->item_code,
					'item_image' => $item_details->item_image_path,
					'description' => $row->description,
					's_warehouse' => $row->s_warehouse,
					't_warehouse' => $row->t_warehouse,
					'qty' => $row->qty,
					'stock_uom' => $row->stock_uom,
					'status' => $row->status,
					'available_qty_at_source' => $available_qty_at_source * 1,
					'available_qty_at_wip' => $available_qty_at_wip * 1,
                    'production_order' => null,
                ];
			}
		}

		$list = array_merge($components, $parts);

		$actual_qty = DB::connection('mysql')->table('tabBin')->where('item_code', $production_order_details->item_code)
			->where('warehouse', $production_order_details->fg_warehouse)->sum('actual_qty');

		$url = $request->fullUrl();
		$feedbacked_logs = DB::connection('mysql_mes')->table('feedbacked_logs')->where('production_order', $production_order)->get();

		return view('tables.tbl_pending_material_transfer_for_manufacture', compact('components', 'parts', 'list', 'url', 'production_order_details', 'actual_qty','feedbacked_logs'));
	}

	public function delete_pending_material_transfer_for_manufacture($production_order, Request $request){
		DB::connection('mysql')->beginTransaction();
		try {
			$production_order_details = DB::connection('mysql')->table('tabProduction Order')->where('name', $production_order)->first();
			$now = Carbon::now();
			// get all pending stock entries based on item code production order
			$pending_stock_entries = DB::connection('mysql')->table('tabStock Entry as ste')
				->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
				->where('ste.docstatus', 0)->where('ste.production_order', $production_order)
				->where('sted.item_code', $request->item_code)
				// ->where('sted.s_warehouse', $request->source_warehouse)
				->where('ste.purpose', 'Material Transfer for Manufacture')
				->select('sted.name as sted_name', 'ste.name as ste_name')
				->get()->toArray();
			
			if(count($pending_stock_entries) > 0){
				// delete stock entry item
				$sted_names = array_column($pending_stock_entries, 'sted_name');
				DB::connection('mysql')->table('tabStock Entry Detail')->whereIn('name', $sted_names)->delete();

				$ste_names = array_column($pending_stock_entries, 'ste_name');
				foreach ($ste_names as $ste_name) {
					// check if ste item is not empty
					$ste_detail = DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $ste_name)->get();
					if(count($ste_detail) > 0){
						// set status
						$for_checking = collect($ste_detail)->where('status', '!=', 'Issued')->count();
						$item_status = ($for_checking > 0) ? 'For Checking' : 'Issued';
						// recalculate ste
						$stock_entry_data = [
							'modified' => $now->toDateTimeString(),
							'modified_by' => Auth::user()->email,
							'posting_time' => $now->format('H:i:s'),
							'total_outgoing_value' => collect($ste_detail)->sum('basic_amount'),
							'total_amount' => collect($ste_detail)->sum('basic_amount'),
							'total_incoming_value' => collect($ste_detail)->sum('basic_amount'),
							'posting_date' => $now->format('Y-m-d'),
							'item_status' => $item_status,
						];

						DB::connection('mysql')->table('tabStock Entry')->where('name', $ste_name)->where('docstatus', 0)->update($stock_entry_data);
					}else{
						// delete parent stock entry 
						DB::connection('mysql')->table('tabStock Entry')->where('name', $ste_name)->where('docstatus', 0)->delete();
					}
				}
			}

			// get all submitted stock entries based on item code warehouse production order
			$submitted_stock_entries = DB::connection('mysql')->table('tabStock Entry as ste')
				->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
				->where('ste.docstatus', 1)->where('ste.production_order', $production_order)
				->where('sted.item_code', $request->item_code)
				->where('ste.purpose', 'Material Transfer for Manufacture')->count();

			if($submitted_stock_entries <= 0){
				// delete production order item
				DB::connection('mysql')->table('tabProduction Order Item')
					->where('parent', $production_order)->where('item_code', $request->item_code)
					->delete();
			}
			
			DB::connection('mysql')->commit();

			return response()->json(['error' => 0, 'message'=> 'Stock entry request has been cancelled.']);
		} catch (Exception $e) {
			DB::connection('mysql')->rollback();

			return response()->json(['error' => 1, 'message' => 'There was a problem creating transaction.']);
		}
	}

	public function delete_pending_material_transfer_for_return($sted_id, Request $request){
		DB::connection('mysql')->beginTransaction();
		try {
			$now = Carbon::now();
			$sted_detail = DB::connection('mysql')->table('tabStock Entry Detail')->where('name', $sted_id)->first();
			
			// delete ste detail
			DB::connection('mysql')->table('tabStock Entry Detail')->where('name', $sted_id)->where('docstatus', 0)->delete();

			// check if ste item is not empty
			$ste_detail = DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $request->ste_no)->get();
			if(count($ste_detail) > 0){
				// set status
				$for_checking = collect($ste_detail)->where('status', '!=', 'Issued')->count();
				$item_status = ($for_checking > 0) ? 'For Checking' : 'Issued';
				// recalculate ste
				$stock_entry_data = [
					'modified' => $now->toDateTimeString(),
					'modified_by' => Auth::user()->email,
					'posting_time' => $now->format('H:i:s'),
					'total_outgoing_value' => collect($ste_detail)->sum('basic_amount'),
					'total_amount' => collect($ste_detail)->sum('basic_amount'),
					'total_incoming_value' => collect($ste_detail)->sum('basic_amount'),
					'posting_date' => $now->format('Y-m-d'),
					'item_status' => $item_status,
				];

				DB::connection('mysql')->table('tabStock Entry')->where('name', $request->ste_no)->where('docstatus', 0)->update($stock_entry_data);
			}else{
				// delete parent ste
				DB::connection('mysql')->table('tabStock Entry')->where('name', $request->ste_no)->where('docstatus', 0)->delete();
			}
			
			DB::connection('mysql')->commit();

			return response()->json(['error' => 0, 'message'=> 'Stock entry request has been cancelled.']);
		} catch (Exception $e) {
			DB::connection('mysql')->rollback();

			return response()->json(['error' => 1, 'message' => 'There was a problem creating transaction.']);
		}
	}

	public function selected_print_fg_transfer_slip($production_order){
		$myArray = explode(',', $production_order);
        $prod_orders = DB::connection('mysql_mes')->table('production_order')
            ->whereIn('production_order', $myArray)
            ->get();
		foreach($prod_orders as $row){
			$ste_details = DB::connection('mysql')->table('tabStock Entry as ste')
            ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
            ->where('production_order', $row->production_order)->where('ste.purpose', 'Manufacture')
            ->whereNull('s_warehouse')->whereNotNull('t_warehouse')
            ->select('ste.name', 'sted.item_code', 'sted.description', 'sted.qty', 'ste.posting_date', 'sted.t_warehouse', 'sted.stock_uom')
            ->orderBy('ste.creation', 'desc')
            ->first();

			$transferred_qty = DB::connection('mysql')->table('tabStock Entry as ste')
				->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
				->where('production_order', $row->production_order)->where('ste.purpose', 'Manufacture')
				->whereNull('s_warehouse')->whereNotNull('t_warehouse')
				->sum('sted.qty');

			$data[] = [
				'production_order' => $row->production_order,
				'sales_order' => $row->sales_order,
				'material_request' => $row->material_request,
				'transaction_date' => $ste_details->posting_date,
				'customer' => $row->customer,
				'project' => $row->project,
				'ste_no' => $ste_details->name,
				'item_code' => $ste_details->item_code,
				'description' => $ste_details->description,
				't_warehouse' => $ste_details->t_warehouse,
				'req_qty' => $row->qty_to_manufacture,
				'transferred_qty' => number_format($transferred_qty),
				'balance_qty' => $row->qty_to_manufacture - $transferred_qty,
				'stock_uom' => $ste_details->stock_uom,
			];
		}
        // dd($data);

        return view('inventory.print_transfer_slip', compact('data'));
	}

	public function view_conveyor_schedule($workstation){
		$schedule_date = Carbon::now()->format('Y-m-d');

		$operation_id = DB::connection('mysql_mes')->table('workstation')
			->where('workstation_name', $workstation)->first()->operation_id;

		$machines = DB::connection('mysql_mes')->table('machine')
			->where('operation_id', $operation_id)
			->where(function($q){
				$q->where('machine_name', 'LIKE', '%conveyor%')
					->orWhere('machine_name', 'LIKE', '%lane%')
					->orWhere('machine_name', 'LIKE', '%special%');
			})
			->orderBy('order_no', 'asc')->get();

        $conveyor_schedule_list = [];
        foreach($machines as $machine){
            $conveyor_schedule_list[] = [
                'machine_code' => $machine->machine_code,
                'machine_name' => $machine->machine_name,
                'production_orders' => $this->conveyor_assigned_production_orders($schedule_date, $machine->machine_code)
            ];
		}

		return view('tables.tbl_conveyor_schedule', compact('conveyor_schedule_list'));
	}

	public function conveyor_assigned_production_orders($schedule_date, $conveyor){
		// get scheduled production order against $scheduled_date
		$q = DB::connection('mysql_mes')->table('assembly_conveyor_assignment as aca')
			->join('production_order as po', 'aca.production_order', 'po.production_order')
			->whereNotIn('po.status', ['Cancelled'])
			->whereDate('scheduled_date', $schedule_date)->where('machine_code', $conveyor)
			->select('po.production_order', 'po.sales_order', 'po.material_request', 'po.qty_to_manufacture', 'po.item_code', 'po.description', 'po.stock_uom', 'aca.order_no', 'po.customer', 'po.produced_qty', 'aca.scheduled_date', 'po.status', 'po.project')
			->orderBy('aca.order_no', 'asc')->orderBy('aca.scheduled_date', 'asc');

		// get scheduled production order before $scheduled_date
		$q = DB::connection('mysql_mes')->table('assembly_conveyor_assignment as aca')
			->join('production_order as po', 'aca.production_order', 'po.production_order')
			->whereNotIn('po.status', ['Cancelled', 'Completed'])
			->whereDate('scheduled_date', '<', $schedule_date)->where('machine_code', $conveyor)
			->select('po.production_order', 'po.sales_order', 'po.material_request', 'po.qty_to_manufacture', 'po.item_code', 'po.description', 'po.stock_uom', 'aca.order_no', 'po.customer', 'po.produced_qty', 'aca.scheduled_date', 'po.status', 'po.project')
			->orderBy('aca.order_no', 'asc')->orderBy('aca.scheduled_date', 'asc')
			->union($q)->get();

		$list = [];
		foreach ($q as $row) {
			$reference_no = ($row->sales_order) ? $row->sales_order : $row->material_request;
			$list[] = [
				'scheduled_date' => $row->scheduled_date,
				'production_order' => $row->production_order,
				'status' => $row->status,
				'customer' => $row->customer,
				'project' => $row->project,
				'reference_no' => $reference_no,
				'qty_to_manufacture' => $row->qty_to_manufacture,
				'item_code' => $row->item_code,
				'description' => $row->description,
				'stock_uom' => $row->stock_uom,
				'order_no' => $row->order_no,
				'good' => $row->produced_qty,
				'balance' => $row->qty_to_manufacture - $row->produced_qty
			];
		}

		return $list;
	}
	public function drag_n_drop($name){
		if(DB::connection('mysql_mes')->table('job_ticket as jt')
							->join('spotwelding_qty as spotpart', 'spotpart.job_ticket_id','jt.job_ticket_id')
							->where('jt.production_order', $name)
							->where('spotpart.status', "In Progress")
							->select('spotpart.status as stat')
							->exists()){
								return response()->json(['success' => 0, 'message' => 'error.']);

						}else{
							if(DB::connection('mysql_mes')->table('job_ticket as jt')
							->join('time_logs as tl', 'jt.job_ticket_id','tl.job_ticket_id')
							->where('jt.production_order', $name)
							->where('tl.status', "In Progress")
							->select('tl.status as stat')
							->exists()){
								return response()->json(['success' => 0, 'message' => 'error.']);

							}else{
								return response()->json(['success' => 1, 'message' => 'Task updated.']);

							}
						}
	}
	// update production order rescheduled date (erp) and MES
	public function update_rescheduled_delivery_date(Request $request){
		$now = Carbon::now();
		$production_order_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)->first();
		if (!$production_order_details) {
			return response()->json(['success' => 0, 'message' => 'Production Order ' . $request->production_order . ' not found.']);
		}
		
		$delivery_date =  Carbon::parse($request->delivery_date);
		$reschedule_date = Carbon::parse($request->reschedule_date);
		$planned_start_date = Carbon::parse($request->planned_start_date);

		

		if($reschedule_date->toDateTimeString() <= $delivery_date->toDateTimeString()){
			return response()->json(['success' => 0, 'message' => 'Rescheduled date must be greater than the current delivery date']);
		}
		if(!$production_order_details->planned_start_date){
			if($reschedule_date->toDateTimeString() <= $planned_start_date->toDateTimeString()){
				return response()->json(['success' => 0, 'message' => 'Rescheduled date must be greater than the current production schedule date']);
			}
		}
		// update production order & sales order rescheduled delivery date & late delivery reason
		if($reschedule_date->toDateTimeString() > $delivery_date->toDateTimeString()){
			$production_order_data = [
				'reschedule_delivery' => 1,
				'reschedule_delivery_date' => $reschedule_date->toDateTimeString()
			];
			$mes_data=[
				'rescheduled_delivery_date' =>  $reschedule_date->toDateTimeString(),
				'last_modified_by' => Auth::user()->employee_name,
				'last_modified_at' => $now->toDateTimeString(),
			];
			//for sales order
			if ($production_order_details->sales_order) {
				$delivery_id=DB::connection('mysql_mes')->table('delivery_date')->where('parent_item_code', $production_order_details->item_code)->where('reference_no',$production_order_details->sales_order)->first();// get the id from the delivery date table FOR SO refrerence
				if(empty($delivery_id)){
					return response()->json(['success' => 0, 'message' => 'Unable to process transaction.Parent item code has been changed by Sales Personnel', 'reload_tbl' => $request->reload_tbl]);

				}
				$data=explode(',',$request->reason_id);
				$datas= ">>".Carbon::parse($reschedule_date)->format('Y-m-d').'<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$data[1]."-".$request->remarks."<br>".$request->logs;//Timeline_log for remarks(delivery Reason) in ERP
				$sales_order_data = [
					'reschedule_delivery' => 1,
					'rescheduled_delivery_date' => $reschedule_date->toDateTimeString(),
					'remarks' => $datas
				];
				$resched_logs=[
					'delivery_date_id' => $delivery_id->delivery_date_id,
					'previous_delivery_date' => ($delivery_id->rescheduled_delivery_date == null)?$delivery_id->delivery_date:$delivery_id->rescheduled_delivery_date,
					'reschedule_reason_id' => $data[0],
					'rescheduled_by' => Auth::user()->employee_name,
					'remarks' => $request->remarks,
					'created_by' => Auth::user()->employee_name,
					'created_at' => $now->toDateTimeString(),
	
				];
				//email alert
				$get_sales_order_owner=db::connection('mysql')->table('tabSales Order Item')
				->where('parent', $production_order_details->sales_order)
				->where('item_code', $production_order_details->item_code)->select('owner')->first();//get so owner from erp

				$email_data = array( 
					'orig_delivery_date'  => ($delivery_id->rescheduled_delivery_date == null)? Carbon::parse($delivery_id->delivery_date)->format('Y-m-d'): Carbon::parse($delivery_id->rescheduled_delivery_date)->format('Y-m-d'),
					'resched_date'  	  => Carbon::parse($reschedule_date)->format('Y-m-d'),
					'item_code'           => $production_order_details->item_code,
					'description'		  => $production_order_details->description,
					'reference'			  => $production_order_details->sales_order,
					'resched_by'     	  => Auth::user()->employee_name,
					'resched_reason'      => $data[1]."-".$request->remarks,
					'customer'			  => $production_order_details->customer,
					'qty'			 	  => $production_order_details->qty_to_manufacture,
					'uom'			      => $production_order_details->stock_uom,


				); 
				if($get_sales_order_owner->owner =! "Administrator"){
					Mail::to($get_sales_order_owner->owner, "john.delacruz@fumaco.local")->send(new SendMail_New_DeliveryDate_Alert($email_data)); //data_to_be_inserted_in_mail_template
				}				
				DB::connection('mysql_mes')->table('delivery_date_reschedule_logs')->insert($resched_logs);// insert log in delivery schedule logs
				DB::connection('mysql_mes')->table('delivery_date')->where('parent_item_code', $production_order_details->item_code)->where('reference_no',$production_order_details->sales_order)->update($mes_data);//update the reschedule delivery date in delivery date table
				
			}
			//for MREQ
			if($production_order_details->material_request){
				$delivery_id=DB::connection('mysql_mes')->table('delivery_date')->where('parent_item_code', $production_order_details->item_code)->where('reference_no',$production_order_details->material_request)->first();// get the id from the delivery date table FOR MREQ refrerence
				if(empty($delivery_id)){
					return response()->json(['success' => 0, 'message' => 'Unable to process transaction.Parent item code has been changed by Sales Personnel', 'reload_tbl' => $request->reload_tbl]);

				}
				$data=explode(',',$request->reason_id);
				$datas= ">>".Carbon::parse($reschedule_date)->format('Y-m-d').'<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$data[1]."-".$request->remarks."<br>".$request->logs;//Timeline_log for remarks(delivery Reason) in ERP
				
				$material_request_data = [
					'reschedule_delivery' => 1,
					'rescheduled_delivery_date' => $reschedule_date->toDateTimeString(),
					'late_delivery_reason' => $datas
				];
				$resched_logs=[
					'delivery_date_id' => $delivery_id->delivery_date_id,
					'previous_delivery_date' => ($delivery_id->rescheduled_delivery_date == null)?$delivery_id->delivery_date:$delivery_id->rescheduled_delivery_date,
					'reschedule_reason_id' => $data[0],
					'rescheduled_by' => Auth::user()->employee_name,
					'remarks' => $request->remarks,
					'created_by' => Auth::user()->employee_name,
					'created_at' => $now->toDateTimeString(),
				];

				//email alert
				$get_mreq_owner=db::connection('mysql')->table('tabMaterial Request Item')
				->where('parent', $production_order_details->material_request)
				->where('item_code', $production_order_details->item_code)->select('owner')->first();//get mreq owner from erp

				$email_data = array( 
					'orig_delivery_date'  => ($delivery_id->rescheduled_delivery_date == null)? Carbon::parse($delivery_id->delivery_date)->format('M-d-Y'): Carbon::parse($delivery_id->rescheduled_delivery_date)->format('M-d-Y'),
					'resched_date'  	  => Carbon::parse($reschedule_date)->format('M-d-Y'),
					'item_code'           => $production_order_details->item_code,
					'description'		  => $production_order_details->description,
					'reference'			  => $production_order_details->material_request,
					'resched_by'     	  => Auth::user()->employee_name,
					'resched_reason'      => $data[1]."-".$request->remarks,
					'customer'			  => $production_order_details->customer,
					'qty'			 	  => $production_order_details->qty_to_manufacture,
					'uom'			      => $production_order_details->stock_uom,


				); 
				if($get_mreq_owner->owner != "Administrator"){
					Mail::to($get_mreq_owner->owner, "john.delacruz@fumaco.local")->send(new SendMail_New_DeliveryDate_Alert($email_data)); //data_to_be_inserted_in_mail_template
				}
				DB::connection('mysql_mes')->table('delivery_date_reschedule_logs')->insert($resched_logs);// insert log in delivery schedule logs
				DB::connection('mysql_mes')->table('delivery_date')->where('parent_item_code', $production_order_details->item_code)->where('reference_no',$production_order_details->material_request)->update($mes_data);

			}
			
		}
		// if schedued in less than the current delivery date (for validation)
		if($reschedule_date->toDateTimeString() <= $delivery_date->toDateTimeString()){
			$production_order_data = [
				'reschedule_delivery' => 0,
				'reschedule_delivery_date' => null
			];

			if ($production_order_details->sales_order) {
				$sales_order_data = [
					'reschedule_delivery' => 0,
					'rescheduled_delivery_date' => null,
					'remarks' => null
				];
			}

			if($production_order_details->material_request){
				$material_request_data = [
					'reschedule_delivery' => 0,
					'rescheduled_delivery_date' => null,
					'late_delivery_reason' => null
				];
			}
		}


		//QUERY TO UPDATE DATA IN ERP
		if($production_order_details->sales_order){
			DB::connection('mysql')->table('tabSales Order Item')
				->where('parent', $production_order_details->sales_order)
				->where('item_code', $production_order_details->item_code)
				->update($sales_order_data);

			DB::connection('mysql')->table('tabProduction Order')
				->where('parent_item_code', $production_order_details->item_code)
				->where('sales_order_no',$production_order_details->sales_order)
				->update($production_order_data);

			
			
		}

		if($production_order_details->material_request){
			DB::connection('mysql')->table('tabMaterial Request Item')
				->where('parent', $production_order_details->material_request)
				->where('item_code', $production_order_details->item_code)
				->update($material_request_data);
				
			DB::connection('mysql')->table('tabProduction Order')
				->where('parent_item_code', $production_order_details->item_code)
				->where('material_request',$production_order_details->material_request)
				->update($production_order_data);

			

		}
		return response()->json(['success' => 1, 'message' => 'Production Order updated.']);
	}
}