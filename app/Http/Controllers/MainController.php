<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Validator;
use DB;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Mail\SendMail_feedbacking;
use App\Mail\SendMail_New_DeliveryDate_Alert;
use App\Traits\GeneralTrait;
use App\LdapClasses\adLDAP;
use Exception;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MachineBreakdownImport;

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

	// /operator_dashboard/{machine}/{workstation}/{production_order}
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
			$email = strpos($request->user_id, '@fumaco.local') ? $request->user_id : $request->user_id.'@fumaco.local';
			$essex_user = DB::connection('mysql_essex')->table('users')->where('email', $email)->first();
			if(!$essex_user){
				return response()->json(['success' => 0, 'message' => '<b>Invalid login credentials!</b>']);
			}

			$email = str_replace('@fumaco.local', null, $email);

			// check if user exist in user table in MES
			$mes_user = DB::connection('mysql_mes')->table('user')
				->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
				->where('user_access_id', $essex_user->user_id)->get();

			if(count($mes_user) <= 0){
				return response()->json(['success' => 0, 'message' => '<b>User not allowed!</b>']);
			}
			
			$allowed_modules = collect($mes_user)->pluck('module')->toArray();

			$is_production_user = array_intersect($allowed_modules, ['Production']);
			$is_qa_user = array_intersect($allowed_modules, ['Quality Assurance']);
			$is_maintenance_user = array_intersect($allowed_modules, ['Maintenance']);

			$is_production_user = count($is_production_user) > 0 ? true : false;
			$is_qa_user = count($is_qa_user) > 0 ? true : false;
			$is_maintenance_user = count($is_maintenance_user) > 0 ? true : false;

			$redirect_to = null;
			if ($is_production_user && !$redirect_to) {
				$redirect_to = "/main_dashboard";
			}

			if ($is_qa_user && !$redirect_to) {
				$redirect_to = "/qa_dashboard";
			}

			if ($is_maintenance_user && !$redirect_to) {
				$redirect_to = "/maintenance_request";
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

				$adldap = new adLDAP();
                $authUser = $adldap->user()->authenticate($email, $request->password);
				if($authUser == true){
					if(Auth::loginUsingId($essex_user->id)){
						DB::connection('mysql_mes')->table('user')->where('user_access_id', $essex_user->user_id)->update(['last_login' => Carbon::now()->toDateTimeString()]);

						return response()->json(['success' => 1, 'message' => "<b>Welcome!</b> Please wait...", 'redirect_to' => $redirect_to]);
					} else {        
						// validation not successful, send back to form 
						return response()->json(['success' => 0, 'message' => '<b>Invalid credentials!</b> Please try again 1.']);
					}
				}

				return response()->json(['success' => 0, 'message' => '<b>Invalid credentials!</b> Please try again.']);
			}
		} catch (Exception $e) {
			return response()->json(['success' => 0, 'message' => '<b>No connection to authentication server.</b>']);
		}
	}

	public function loginOperatorId(Request $request){
		// validate the info, create rules for the inputs
    	$rules = array(
		    'operator_id' => 'required'
		);

		$validator = Validator::make(Input::all(), $rules);

		// if the validator fails, redirect back to the form
		if ($validator->fails()) {
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
					DB::connection('mysql_mes')->table('user')->where('user_access_id', $user->user_id)->update(['last_login' => Carbon::now()->toDateTimeString()]);

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

	// /get_jt_details/{jtno}
	public function getTimesheetDetails($jtno){
		$tab=[];
		$details = DB::connection('mysql_mes')->table('production_order')->where('production_order.production_order', $jtno)
			->leftJoin('delivery_date', function($join){
				$join->on( DB::raw('IFNULL(production_order.sales_order, production_order.material_request)'), '=', 'delivery_date.reference_no');
				$join->on('production_order.parent_item_code','=','delivery_date.parent_item_code');
			}) // get delivery date from delivery_date table
			->leftJoin('assembly_conveyor_assignment as acs', function ($q){
				return $q->on('production_order.production_order', 'acs.production_order');
			})
			->select('production_order.*', 'delivery_date.rescheduled_delivery_date', 'acs.assembly_conveyor_assignment_id as assignment_id', 'acs.machine_code')->first();

		if (!$details) {
			return response()->json(['message' => 'Production Order <b>'.$jtno.'</b> not found.', 'item_details' => [], 'details' => [], 'operations' => [], 'success' => 0]);
		}

		$description = $details->description;
		if(false !== stripos($details->item_classification, 'SA - ')){
			$description = DB::connection('mysql')->table('tabItem Variant Attribute')->where('parent', $details->item_code)->orderBy('idx', 'asc')->pluck('attribute_value')->implode(' ');
			$description = $description ? $description : $details->description;
		}

		$process = $this->getTimesheetProcess($details->production_order);

		$planned_start = Carbon::parse($details->planned_start_date);
		if ($details->actual_start_date) {
			if ($details->actual_start_date >= $planned_start->startOfDay() && $details->actual_start_date <= $planned_start->endOfDay()) {
				$task_status = 'On Time';
			}else{
				$task_status = 'Late';
			}
		} else {
			$task_status = '--';
		}

		$owner = explode('@', $details->created_by);
		$owner = ucwords(str_replace('.', ' ', $owner[0]));

		$item_details = [
			'planned_start_date' => ($details->planned_start_date == null)? NULL : Carbon::parse($details->planned_start_date)->format('M-d-Y'),
			'sales_order' => $details->sales_order,
			'material_request' => $details->material_request,
			'production_order' => $details->production_order,
			'customer' => $details->customer,
			'project' => $details->project,
			'qty_to_manufacture' => $details->qty_to_manufacture,
			'delivery_date' => ($details->rescheduled_delivery_date == null)? $details->delivery_date: $details->rescheduled_delivery_date, //link new rescchedule delivery date 
			'item_code' => $details->item_code,
			'description' => $description,
			'status' => $task_status,
			'owner' => $owner,
			'feedback_qty' => $details->feedback_qty,
			'production_order_status' => $this->production_status_with_stockentry($details->production_order, $details->status, $details->qty_to_manufacture,$details->feedback_qty, $details->produced_qty),
			'created_at' => Carbon::parse($details->created_at)->format('m-d-Y h:i A')
		];

		$process_arr = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $details->production_order)
			->select(DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process'), 'workstation', 'process_id', 'job_ticket_id', 'status', 'completed_qty', 'reject', 'remarks')
			->get();

		$operation_list = $operation_reject_logs = [];
		foreach ($process_arr as $row) {
			$operations_arr = $rejections_arr = [];
			$painting_cycle_time_in_seconds = 0;
			if($row->workstation == "Spotwelding"){
				$operations =  DB::connection('mysql_mes')->table('spotwelding_qty')
					->where('job_ticket_id',  $row->job_ticket_id)->get();

				$total_rejects = $row->reject;
				$min_count= collect($operations)->min('from_time');
				$max_count= collect($operations)->max('to_time');
				$status = collect($operations)->where('status', 'In Progress');

				$rework_qty = DB::connection('mysql_mes')->table('quality_inspection')->where('reference_type', 'Spotwelding')->whereIn('reference_id', collect($operations)->pluck('time_log_id'))->where('status', 'QC Failed')->where('qc_remarks', 'For Rework')->sum('rejected_qty');

				$total_rework = $rework_qty;
				if($row->completed_qty > 0){
					$total_rework = $rework_qty - $row->completed_qty;
					$total_rework = $total_rework > 0 ? $total_rework : 0;
				}

				$operation_reject_logs_query = DB::connection('mysql_mes')->table('quality_inspection as q')
					// ->join('spotwelding_qty as tl', 'tl.job_ticket_id', 'q.reference_id')
					->join('job_ticket as jt', 'q.reference_id', 'jt.job_ticket_id')
					->join('reject_reason as rr', 'rr.qa_id', 'q.qa_id')
					->join('reject_list as rl', 'rl.reject_list_id', 'rr.reject_list_id')
					->where('q.reference_type', 'Spotwelding')
					->whereIn('q.reference_id', collect($operations)->pluck('job_ticket_id'))
					->select('rl.reject_reason', 'rr.reject_qty', 'q.qa_inspection_type', 'q.rejected_qty', 'q.qa_inspection_date', 'q.qa_disposition', 'q.qa_staff_id', 'q.status', 'jt.workstation', 'jt.process_id', 'q.created_by', 'q.created_at')
					->get();

				foreach($operation_reject_logs_query as $l) {
					$operation_reject_logs[$l->workstation][$l->process_id][] = [
						'reject_reason' => $l->reject_reason,
						'reject_qty' => $l->rejected_qty,//$l->reject_qty,
						'qa_inspection_type' => $l->qa_inspection_type,
						'qa_inspection_date' => $l->qa_inspection_date,
						'qa_disposition' => $l->qa_disposition,
						'qa_staff_id' => $l->qa_staff_id,
						'qa_status' => $l->status,
						'reported_by' => $l->created_by,
						'reported_at' => $l->created_at
					];

					if (array_key_exists('rows', $operation_reject_logs[$l->workstation])) {
						$operation_reject_logs[$l->workstation]['rows']++;
					} else {
						$operation_reject_logs[$l->workstation]['rows'] = 1;
					}
				}

				$operations_arr[] = [
					'machine_code' => null,
					'timelog_id' => null,
					'operator_name' => null,
					'helpers' => [],
					'from_time' => $min_count,
					'to_time' => ($row->status == "In Progress") ? '' : $max_count,
					'status' => (count($status) == 0 )? 'Not started': "In Progress",
					'qa_inspection_status' => null,
					'good' => $row->completed_qty,
					'reject' => $total_rejects,
					'rework' => $total_rework,
					'remarks' => null,
					'total_duration' => null
				];
			}else{
				$operations = DB::connection('mysql_mes')->table('job_ticket AS jt')
					->join('time_logs', 'time_logs.job_ticket_id', 'jt.job_ticket_id')
					->where('time_logs.job_ticket_id', $row->job_ticket_id)->where('workstation','!=', 'Spotwelding')
					->select('jt.*', 'time_logs.*')->orderBy('idx', 'asc')->get();

				$operation_reject_logs_query = DB::connection('mysql_mes')->table('quality_inspection as q')
					->join('time_logs as tl', 'tl.time_log_id', 'q.reference_id')
					->join('job_ticket as jt', 'tl.job_ticket_id', 'jt.job_ticket_id')
					->join('reject_reason as rr', 'rr.qa_id', 'q.qa_id')
					->join('reject_list as rl', 'rl.reject_list_id', 'rr.reject_list_id')
					->where('q.reference_type', 'Time Logs')
					->whereIn('q.reference_id', collect($operations)->pluck('time_log_id'))
					->select('rl.reject_reason', 'rr.reject_qty', 'q.qa_inspection_type', 'q.qa_inspection_date', 'q.qa_disposition', 'q.qa_staff_id', 'q.status', 'jt.workstation', 'jt.process_id', 'jt.job_ticket_id', 'q.created_by', 'q.created_at', 'q.rejected_qty')
					->get();

				$qa_staff_names = DB::connection('mysql_essex')->table('users')
					->whereIn('user_id', collect($operation_reject_logs_query)->pluck('qa_staff_id')->unique())
					->pluck('employee_name', 'user_id')->toArray();

				foreach($operation_reject_logs_query as $l) {
					$operation_reject_logs[$l->workstation][$l->process_id][] = [
						'reject_reason' => $l->reject_reason,
						'reject_qty' => $l->rejected_qty,//$l->reject_qty,
						'qa_inspection_type' => $l->qa_inspection_type,
						'qa_inspection_date' => $l->qa_inspection_date,
						'qa_disposition' => $l->qa_disposition,
						'qa_staff_id' => $l->qa_staff_id,
						'qa_status' => $l->status,
						'job_ticket' => $l->job_ticket_id,
						'reported_by' => $l->created_by,
						'reported_at' => $l->created_at
					];

					if (array_key_exists('rows', $operation_reject_logs[$l->workstation])) {
						$operation_reject_logs[$l->workstation]['rows']++;
					} else {
						$operation_reject_logs[$l->workstation]['rows'] = 1;
					}
				}

				foreach ($operations as $d) {
					$reference_type = ($d->workstation == 'Spotwelding') ? 'Spotwelding' : 'Time Logs';
					$reference_id = ($d->workstation == 'Spotwelding') ? $d->job_ticket_id : $d->time_log_id;
					$qa_inspection_status = $this->get_qa_inspection_status($reference_type, $reference_id);

					$rework_qty = DB::connection('mysql_mes')->table('quality_inspection')->where('reference_type', 'Time Logs')
						->where('reference_id', $d->time_log_id)->where('status', 'QC Failed')->where('qc_remarks', 'For Rework')
						->sum('rejected_qty');

					if ($d->duration > 0) {
						if ($d->good > 0) {
							$cycle_time_in_seconds = $d->duration * 3600;
						} else {
							$cycle_time_in_seconds = 0;
						}

						$seconds = $cycle_time_in_seconds%60;
						$minutes = floor(($cycle_time_in_seconds%3600)/60);
						$hours = floor(($cycle_time_in_seconds%86400)/3600);
						$days = floor(($cycle_time_in_seconds%2592000)/86400);
						$months = floor($cycle_time_in_seconds/2592000);
						
						$dur_months = ($months > 0) ? $months .'M' : null;
						$dur_days = ($days > 0) ? $days .'d' : null;
						$dur_hours = ($hours > 0) ? $hours .'h' : null;
						$dur_minutes = ($minutes > 0) ? $minutes .'m' : null;
						$dur_seconds = ($seconds > 0) ? $seconds .'s' : null;
			
						$total_duration = $dur_months . ' '. $dur_days . ' ' .$dur_hours . ' '. $dur_minutes . ' ' . $dur_seconds;
					}else{
						$total_duration = '-';
					}					

					$helpers = DB::connection('mysql_mes')->table('helper')
						->where('time_log_id', $d->time_log_id)->orderBy('operator_name', 'asc')
						->distinct()->pluck('operator_name');

					$operations_arr[] = [
						'machine_code' => $d->machine_code,
						'timelog_id' => $d->time_log_id,
						'operator_name' => $d->operator_name,
						'helpers' => $helpers,
						'from_time' => ($d->from_time) ? Carbon::parse($d->from_time)->format('M-d-Y h:i A') : '',
						'to_time' => ($d->to_time) ? Carbon::parse($d->to_time)->format('M-d-Y h:i A') : '',
						'status' => $d->status,
						'qa_inspection_status' => $qa_inspection_status,
						'good' => $d->good,
						'reject' => $d->reject,
						'rework' => $d->rework,
						'remarks' => $d->remarks,
						'total_duration' => trim($total_duration)
					];

					$painting_cycle_time_in_seconds += $row->workstation == 'Painting' ? $d->duration : 0;
				}
			}

			if ($painting_cycle_time_in_seconds > 0) {
				$painting_cycle_time_in_seconds = $painting_cycle_time_in_seconds * 3600;

				$seconds = $painting_cycle_time_in_seconds%60;
				$minutes = floor(($painting_cycle_time_in_seconds%3600)/60);
				$hours = floor(($painting_cycle_time_in_seconds%86400)/3600);
				$days = floor(($painting_cycle_time_in_seconds%2592000)/86400);
				$months = floor($painting_cycle_time_in_seconds/2592000);
				
				$dur_months = ($months > 0) ? $months .'M' : null;
				$dur_days = ($days > 0) ? $days .'d' : null;
				$dur_hours = ($hours > 0) ? $hours .'h' : null;
				$dur_minutes = ($minutes > 0) ? $minutes .'m' : null;
				$dur_seconds = ($seconds > 0) ? $seconds .'s' : null;
	
				$painting_duration = trim($dur_months . ' '. $dur_days . ' ' .$dur_hours . ' '. $dur_minutes . ' ' . $dur_seconds);
			}else{
				$painting_duration = '-';
			}					
			
			$operation_list[] = [
				'production_order' => $jtno,
				'workstation' => $row->workstation,
				'process' => $row->process,
				'process_id' => $row->process_id,
				'job_ticket' => $row->job_ticket_id,
				'count_good' => (count($operations_arr) <= 1) ? '' : "Total: ".collect($operations_arr)->sum('good'),
				'count' => (count($operations_arr) > 0) ? count($operations_arr) : 1,
				'operations' => $operations_arr,
				'remarks' => $row->remarks,
				'completed_qty' => $row->completed_qty,
				'jt_status' => $row->status,
				'cycle_time' => $this->compute_item_cycle_time_per_process($details->item_code, $details->qty_to_manufacture, $row->workstation, $row->process_id)
			];
		}

		$processes = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $details->production_order)
			->distinct()->pluck('job_ticket_id');

		$totals = [
			'produced_qty' => $details->produced_qty,
			'total_good' => collect($process_arr)->min('completed_qty'),
			'total_reject' => collect($process_arr)->sum('reject'),
			'balance_qty' => $details->qty_to_manufacture - $details->produced_qty,
		];

		$datas = [];
		$tab_name = $details->item_classification;
		$production_order_no = $jtno;
		$po = DB::connection('mysql_mes')->table('production_order')->where('sales_order',$details->sales_order)
			->where('material_request',$details->material_request)->where('parent_item_code', $details->parent_item_code)
			->where('sub_parent_item_code', $details->item_code)->whereNotIn('production_order', [$details->production_order])
			->get();

		if(count($po) > 0){
			foreach($po as $rowss){
				$data[] = [
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
					// 'stock_uom' => $rowss->stock_uom,
					'material_status' => $this->material_status_stockentry($rowss->production_order, $rowss->status, $rowss->qty_to_manufacture,$rowss->feedback_qty, $rowss->produced_qty)
				];
			}

			$tab[] = [
				'tab' => substr($details->item_code, 0, 2).'-Parts',
				'data' => $data
			];
		}

		$reference = ($details->sales_order == null) ? $details->material_request : $details->sales_order;
		$tbl_reference = ($details->sales_order == null) ? "tabMaterial Request Item" : "tabSales Order Item";
		$get_delivery_date = DB::connection('mysql_mes')->table('delivery_date')->where('reference_no', $reference)->where('parent_item_code',  $details->item_code)->first();
		$notifications = ['match' => 'false'];
		if(!empty($get_delivery_date)) {
            $erp_sales_order = DB::connection('mysql')->table($tbl_reference)->where('name', $get_delivery_date->erp_reference_id)->select('item_code')->first();
            if(!empty($erp_sales_order)){
                if($erp_sales_order->item_code != $details->parent_item_code){
					$notifications= [
						"match"=> 'true',	
						'message' => 'Parent item code was change from <b>'.$details->parent_item_code.'</b> to <b>'.$erp_sales_order->item_code.'</b>',
					];
				}
			}
		}

		$operation_list = collect($operation_list)->groupBy('workstation');

		$job_tickets = collect($process_arr)->pluck('job_ticket_id');
		$activity_logs = DB::connection('mysql_mes')->table('activity_logs')->where('action', 'Reset Time Log')->whereIn('reference', $job_tickets)->orderBy('created_at', 'desc')->get();

		$success = 1;

		$total_planned_qty = DB::connection('mysql_mes')->table('production_order')->where('sales_order', $details->sales_order)->where('item_code', $details->item_code)->whereNotIn('status', ['Cancelled', 'Closed'])->sum('qty_to_manufacture');

        $sales_order_qty = DB::connection('mysql')->table('tabSales Order Item')->where('parent', $details->sales_order)->where('item_code', $details->parent_item_code)->pluck('qty')->first();
		$sales_order_qty = $sales_order_qty ? ($sales_order_qty * 1) : 0;

		$bom = DB::connection('mysql')->table('tabBOM')->where('item', $details->sub_parent_item_code)->where('is_default', 1)->orderBy('modified', 'desc')->first();
		
		$bom_details = [];
		$qty_to_manufacture = $sales_order_qty;

		if($bom){
			$bom_details = DB::connection('mysql')->table('tabBOM Item')->where('parent', $bom->name)->where('item_code', $details->item_code)->first();
			$qty_to_manufacture = ($bom_details ? ($bom_details->qty * 1) : 0) * $sales_order_qty;
		}

		return view('tables.production_order_search_content', compact('details', 'process', 'totals', 'item_details', 'operation_list','success', 'tab_name','tab', 'notifications', 'production_order_no', 'activity_logs', 'painting_duration', 'total_planned_qty', 'qty_to_manufacture', 'operation_reject_logs', 'qa_staff_names'));
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
				// 'stock_uom' => $rows->stock_uom,
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

	// /end_task
	public function endTask(Request $request){
		DB::connection('mysql_mes')->beginTransaction();
		DB::connection('mysql')->beginTransaction();
        try {
			if(!Auth::user()) {
				return response()->json(['status' => 0, 'message' => 'Session Expired. Please login to continue.']);
			}

			$now = Carbon::now();
			$current_task = DB::connection('mysql_mes')->table('time_logs')
				->where('time_log_id', $request->id)->first();

			if(!$current_task){
				return response()->json(['status' => 0, 'message' => 'Task not found.']);
			}

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

			$job_card_log = DB::connection('mysql')->table('tabJob Card Time Log')->where('mes_timelog_id', $request->id)->first();
			if ($job_card_log) {
				$job_card_id = $job_card_log->parent;
				$job_card_time_log = [
					'modified' => $now->toDateTimeString(),
					'modified_by' => Auth::user()->employee_name,
					'to_time' => $now->toDateTimeString(),
					'time_in_mins' => ($seconds / 60),
					'completed_qty' => $good_qty,
				];
	
				DB::connection('mysql')->table('tabJob Card Time Log')->where('mes_timelog_id', $request->id)->update($job_card_time_log);

				$this->update_job_card_status($job_card_id);
			}

			$operator_name = DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $request->id)->pluck('operator_name')->first();
			$workstation = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $current_task->job_ticket_id)->pluck('workstation')->first();

			$activity_logs = [
				'action' => 'Process Completed',
				'message' => $workstation.' process has been completed for '.$request->production_order.' by '.$operator_name,
				'created_at' => $now->toDateTimeString(),
				'created_by' => $operator_name
			];

			DB::connection('mysql_mes')->table('activity_logs')->insert($activity_logs); // insert completed processes log in activity logs

			$rework_qty = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $current_task->job_ticket_id)->pluck('rework')->first();
			$rework_qty = $rework_qty > $request->completed_qty ? $rework_qty - $request->completed_qty : 0;
			DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $current_task->job_ticket_id)->update(['rework' => $rework_qty]);

			$update_job_ticket = $this->update_job_ticket($current_task->job_ticket_id);

			if($update_job_ticket == 1){
				DB::connection('mysql')->commit();
				DB::connection('mysql_mes')->commit();

				return response()->json(['success' => 1, 'message' => 'Task has been updated.']);
			}else{
				DB::connection('mysql')->rollback();
				DB::connection('mysql_mes')->rollback();

				return response()->json(['success' => 0, 'message' => 'An error occured. Please try again.']);
			}
        } catch (Exception $e) {
			DB::connection('mysql')->rollback();
			DB::connection('mysql_mes')->rollback();

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

            DB::connection('mysql')->table('tabWork Order Operation')
				->where('parent', $prod_order)->where('workstation', $workstation)
				->where('process', $process_id)->update($data);

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

		$type = ($type == 'Reject Confirmation') ? $type : 'Random Inspection';
		$type = ($type == 'Quality Check') ? 'Quality Check' : $type;

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

		$permitted_production_operation = collect($permissions['permitted_module_operation'])->where('module', 'Production')->toArray();

		$permitted_production_operation = array_column($permitted_production_operation, 'operation_name');

		return view('main_dashboard', compact('timesheet', 'user_details', 'mes_user', 'mes_user_operations', 'permissions', 'permitted_production_operation'));
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
		$get_prod_sched_today=DB::connection('mysql_mes')->table('production_order')->whereDate('planned_start_date', '2022-02-23 16:30:05.105563')->groupBy('parent_item_code', 'sales_order', 'material_request')->select('parent_item_code', 'sales_order', 'material_request')->get();
		foreach($get_prod_sched_today as $row){
			$reference= ($row->sales_order == null)? $row->material_request: $row->sales_order;
			$tbl_reference= ($row->sales_order == null)? "tabMaterial Request Item": "tabSales Order Item";
			$get_delivery_date=DB::connection('mysql_mes')->table('delivery_date')->where('reference_no', $reference)->where('parent_item_code',  $row->parent_item_code)->first();
			if(!empty($get_delivery_date)){
				$erp_query = DB::connection('mysql')->table($tbl_reference)->where('name', $get_delivery_date->erp_reference_id)->first();
				$erp_sales_order= $erp_query ? $erp_query->item_code : null;
				if($erp_sales_order and $erp_sales_order != $row->parent_item_code){
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
			$converted= Carbon::parse($mb->date_reported)->format('M-d-Y');
			
        	$from_carbon = Carbon::parse($now);
            $to_carbon = Carbon::parse($mb->date_reported);

			$duration = $from_carbon->diffInSeconds($to_carbon);
			$converted_duration = $this->seconds2humanforduration($duration);
			
			$notifs[] = [
				'type' => 'Machine Breakdown',
				'message' => '<b>' . $mb->machine_name.'</b><br><i>Machine Request: ' . $mb->type.'<br>Date Reported: '.$converted.'<br><b>'.$converted_duration.' ago</b></i>',
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
            // ->join('tabWarehouse as w', 'item.default_warehouse', 'w.name')
            // ->where('w.disabled', 0)
            // ->where('w.is_group', 0)
            // ->where('w.company', 'FUMACO Inc.')
            // ->where('has_variants',"!=", 1)
            // ->where('w.department', 'Fabrication')
            ->whereIn('item.item_group', ['Raw Material'])
            ->whereIn('item.item_classification', ['AS - Aluminum Sheets', 'CS - Crs Steel Coil', 'DI - Diffuser'])
            ->select('item.name', 'item.item_name', 'stock_uom')
			->orderBy('item.modified', 'desc')->get();

		$activity_logs = DB::connection('mysql_mes')->table('activity_logs')->orderBy('created_at', 'desc')->get();

		foreach($activity_logs as $logs){
			if($logs->action == 'BOM Update'){
				$message = json_decode($logs->message);
			}else{
				$message = $logs->message;
				if($logs->action == 'Cancelled Process'){
					$message = explode(' at ', $logs->message)[0];
				}else if($logs->action == 'Started Production Order'){
					$message = explode(' at ', $logs->message)[0].' in '.explode(' in ', $logs->message)[1];
				}else if($logs->action == 'Feedbacked'){
					$message = explode(' at ', $logs->message)[0].' by '.explode(' by ', $logs->message)[1];
				}
			}

			$notifs[] = [
				'type' => $logs->action,
				'message' => $message,
				'created' => $logs->created_at,
				'timelog_id' => '',
				'table' => 'production_scheduling'
			];
		}

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
			$delivery_date = ($prodsched->rescheduled_delivery_date == null)? $prodsched->delivery_date:$prodsched->rescheduled_delivery_date;
			$converted_delivery_date = Carbon::parse($delivery_date)->format('M-d-Y');

			$planned_start_date = Carbon::parse($prodsched->planned_start_date)->format('M-d-Y');
			$notifs[] = [	
				'type' => 'Production Schedule',
				'message' => '<b>'.$prodsched->production_order.'</b><br><i>Delivery Date: '.$converted_delivery_date.'<br> Planned Start Date: '.$planned_start_date.'<br> Quantity: <b>' . $prodsched->qty_to_manufacture.'&nbsp;'.$prodsched->stock_uom.'</b></i>',
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
			->join('tabWork Order as po', 'po.name', 't.production_order')
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

	// /item_feedback
	public function itemFeedback(){
		$permissions = $this->get_user_permitted_operation();

		$owners = $this->get_production_order_filters()['owners'];

		$target_warehouses = $this->get_production_order_filters()['target_warehouses'];
		$target_warehouses = collect($target_warehouses)->pluck('fg_warehouse');

    	// manual create production form
    	$item_list = [];

    	$parent_code_list = [];

    	$sub_parent_code_list = [];

    	$warehouse_list = [];

        $so_list = [];

        $mreq_list = [];

		return view('reports.item_feedback', compact('item_list', 'owners', 'target_warehouses', 'warehouse_list', 'so_list', 'mreq_list', 'parent_code_list', 'sub_parent_code_list', 'permissions'));
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
				->where('docstatus', 1)
				->whereIn('custom_purpose', ['Manufacture', 'Sample Order', 'Consignment Order'])
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
			if(!Auth::user()) {
				return response()->json(['success' => 0, 'message' => 'Session Expired. Please login to continue.']);
			}

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
			$details = DB::connection('mysql')->table('tabMaterial Request')->where('docstatus', 1)
			->whereIn('custom_purpose', ['Manufacture', 'Sample Order', 'Consignment Order'])
        		->where('company', 'FUMACO Inc.')->where('per_ordered', '<', 100)->where('name', $reference_no)->first();
		}

		if (!$details) {
			return response()->json(['success' => 0, 'message' => $reference_no . ' not found.']);
		}

		return response()->json($details);
	}

	private function get_production_order_filters(){
		$owners = DB::connection('mysql_mes')->table('production_order')->distinct()->select('created_by')->orderBy('created_by', 'asc')->get();
		$owners = collect($owners)->map(function ($q){
			$owner = explode('@', $q->created_by);
			$owner = ucwords(str_replace('.', ' ', $owner[0]));

			if($owner){
				return ['email' => $q->created_by, 'name' => $owner];
			}
		})->unique()->filter();

		$target_warehouses = DB::connection('mysql_mes')->table('production_order')->distinct()->select('fg_warehouse')->orderBy('fg_warehouse', 'asc')->get();

		$filters = [
			'owners' => $owners,
			'target_warehouses' => $target_warehouses
		];

		return $filters;
	}

	// /production_order_list/{status}
	public function get_production_order_list(Request $request, $status){
		$status = count(array_filter(explode(',', $status))) == 7 ? 'All' : $status;

		$status_array = !in_array($status, ['All', 'Production Orders']) ? array_filter(explode(',', $status)) : [];
		if(in_array('Awaiting Feedback', $status_array)){
			$status_array = str_replace('Awaiting Feedback', 'Ready for Feedback', $status_array);
		}

		$user_permitted_operations = DB::connection('mysql_mes')->table('user')
			->join('operation', 'operation.operation_id', 'user.operation_id')
			->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
			->where('module', 'Production')->where('user_access_id', Auth::user()->user_id)
			->when($request->operation, function($q) use ($request){
				return $q->where('user.operation_id', $request->operation);
			})
			->select('user.operation_id', 'operation_name')->orderBy('user.operation_id', 'asc')
			->distinct()->get();

		$user_permitted_operation_id = collect($user_permitted_operations)->pluck('operation_id');
		$user_permitted_operation_names = collect($user_permitted_operations)->pluck('operation_name');
		
		$filtered_production_orders = [];
		$statuses = [];
		$inactive_production_orders = [];
		$inactive_statuses = ['Not Started', 'Cancelled', 'Closed'];
		// Not Started / Cancelled / Closed
		if(!empty(array_intersect($inactive_statuses, $status_array))){
			$inactive_status = [
				in_array('Not Started', $status_array) ? 'Not Started' : '',
				in_array('Cancelled', $status_array) ? 'Cancelled' : '',
				in_array('Closed', $status_array) ? 'Closed' : ''
			];
			$inactive_production_orders = DB::connection('mysql_mes')->table('production_order')
				->where(function($q) use ($request) {
					$q->where('production_order', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('customer', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('sales_order', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('material_request', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('item_code', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('bom_no', 'LIKE', '%'.$request->search_string.'%');
				})
				->whereIn('status', array_filter($inactive_status))
				->whereIn('operation_id', $user_permitted_operation_id)
				->when($request->owner, function ($q) use ($request){
					$q->where('created_by', $request->owner);
				})
				->when($request->target_warehouse, function ($q) use ($request){
					$q->where('fg_warehouse', $request->target_warehouse);
				})
				->select('*', DB::raw('IFNULL(sales_order, material_request) as reference_no'))
				->orderBy('created_at', 'desc');

			if(!in_array($status, ['All', 'Production Orders'])){
				$statuses = array_merge($statuses, $inactive_status);
			}
		}
		// Not Started / Cancelled / Closed

		// In Progress
		$in_progress_production_orders = [];
		if($status == 'All' or in_array('In Progress', $status_array)){
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

			$filtered_production_orders = collect($filtered_production_orders)->merge(collect($in_progress_production_orders));

			if(!in_array($status, ['All', 'Production Orders'])){
				array_push($statuses, 'In Progress');
			}
		}
		// In Progress

		// Task Queue
		$pending_production_orders = [];
		if($status == 'All' or in_array('Task Queue', $status_array)){
			$on_going_time_logs = DB::connection('mysql_mes')->table('time_logs')
				->join('job_ticket', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
				->where('job_ticket.workstation', '!=', 'Spotwelding')
				->where('time_logs.status', 'In Progress')
				->distinct()->pluck('job_ticket.production_order')->toArray();

			$on_going_spotwelding = DB::connection('mysql_mes')->table('spotwelding_qty')
				->join('job_ticket', 'spotwelding_qty.job_ticket_id', 'job_ticket.job_ticket_id')
				->where('spotwelding_qty.status', 'In Progress')
				->distinct()->pluck('job_ticket.production_order')->toArray();

			$on_going_tasks = array_merge($on_going_time_logs, $on_going_spotwelding);

			$pending_production_orders = DB::connection('mysql_mes')->table('job_ticket')
				->whereIn('status', ['In Progress', 'Pending'])
				->whereNotIn('production_order', $on_going_tasks)
				->distinct()
				->pluck('production_order');

			$filtered_production_orders = collect($filtered_production_orders)->merge(collect($pending_production_orders));

			if(!in_array($status, ['All', 'Production Orders'])){
				array_push($statuses, 'In Progress');
			}
		}
		// Task Queue

		// Awaiting Feedback
		$jt_production_orders = [];
		if(in_array('Ready for Feedback', $status_array)){
			$permitted_workstation = DB::connection('mysql_mes')->table('workstation')
				->whereIn('operation_id', $user_permitted_operation_id)->distinct()
				->pluck('workstation_name')->toArray();

			if(in_array('Painting', $user_permitted_operation_names->toArray())){
				array_push($permitted_workstation, ['Painting']);
			}
			
			$jt_production_orders = DB::connection('mysql_mes')->table('job_ticket')
				->whereIn('workstation', $permitted_workstation)
				->whereIn('status', ['In Progress', 'Completed'])->distinct()->pluck('production_order');

			$jt_production_orders = $jt_production_orders->toArray();

			$filtered_production_orders = collect($filtered_production_orders)->merge(collect($jt_production_orders));

			if(!in_array($status, ['All', 'Production Orders'])){
				$statuses = array_merge($statuses, ['In Progress', 'Completed', 'Ready for Feedback', 'Partially Feedbacked']);
			}
		}
		// Awaiting Feedback

		// Completed
		$filter_dates = $request->feedback_dates ? explode(' - ', $request->feedback_dates) : [];
		$start_date = isset($filter_dates[0]) ? Carbon::parse($filter_dates[0])->startOfDay()->toDateTimeString() : Carbon::now()->startOfDay()->toDateTimeString();
		$end_date = isset($filter_dates[1]) ? Carbon::parse($filter_dates[1])->endOfDay()->toDateTimeString() : Carbon::now()->endOfDay()->toDateTimeString();

		$mes_completed_production_orders = [];
		if(in_array('Completed', $status_array)){
			$mes_completed_production_orders = DB::connection('mysql_mes')->table('production_order')
				->where(function($q) use ($request) {
					$q->where('production_order', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('customer', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('sales_order', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('material_request', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('item_code', 'LIKE', '%'.$request->search_string.'%')
						->orWhere('bom_no', 'LIKE', '%'.$request->search_string.'%');
				})
				->whereIn('status', ['Completed', 'Feedbacked'])
				->whereRaw('feedback_qty >= qty_to_manufacture')
				->whereIn('operation_id', $user_permitted_operation_id)
				->when($filter_dates, function ($q) use ($start_date, $end_date){
					$q->whereBetween('last_modified_at', [$start_date, $end_date]);
				})
				->when($request->owner, function ($q) use ($request){
					$q->where('created_by', $request->owner);
				})
				->when($request->target_warehouse, function ($q) use ($request){
					$q->where('fg_warehouse', $request->target_warehouse);
				})
				->select('*', DB::raw('IFNULL(sales_order, material_request) as reference_no'))
				->orderBy('created_at', 'desc');

			if(!in_array($status, ['All', 'Production Orders'])){
				// array_push($statuses, 'Completed');
				$statuses = array_merge($statuses, ['Completed', 'Feedbacked']);
			}
		}
		// Completed

		$production_orders = DB::connection('mysql_mes')->table('production_order')
			->where(function($q) use ($request) {
				$q->where('production_order', 'LIKE', '%'.$request->search_string.'%')
					->orWhere('customer', 'LIKE', '%'.$request->search_string.'%')
					->orWhere('sales_order', 'LIKE', '%'.$request->search_string.'%')
					->orWhere('material_request', 'LIKE', '%'.$request->search_string.'%')
					->orWhere('item_code', 'LIKE', '%'.$request->search_string.'%')
					->orWhere('bom_no', 'LIKE', '%'.$request->search_string.'%');
			})
			->when($statuses, function ($q) use ($statuses){
				$q->whereIn('status', $statuses);
			})
			->when(count($status_array) > 0, function($q) use ($filtered_production_orders){
				$q->whereIn('production_order', $filtered_production_orders);
			})
			->when($status != 'All' and in_array('Ready for Feedback', $status_array), function($q) use ($status_array){
				$q->where('produced_qty', '>', 0)
					->where(function($q) {
						$q->whereRaw('produced_qty > feedback_qty')
							->whereRaw('qty_to_manufacture > feedback_qty');
					});
			})
			->when($status != 'All' and in_array('Task Queue', $status_array), function($q){
				$q->whereRaw('qty_to_manufacture > feedback_qty');
			})
			->when($status != 'All' and !in_array('Cancelled', $status_array), function($q){
				$q->where('status', '!=', 'Cancelled');
			})
			->whereIn('operation_id', $user_permitted_operation_id)
			->when(count($status_array) > 0 and in_array('Completed', $status_array), function($q) use ($mes_completed_production_orders){
				$q->union($mes_completed_production_orders);
			})
			->when(!empty(array_intersect($inactive_statuses, $status_array)), function($q) use ($inactive_production_orders){
				$q->union($inactive_production_orders);
			})
			->when($request->owner, function ($q) use ($request){
				$q->where('created_by', $request->owner);
			})
			->when($request->target_warehouse, function ($q) use ($request){
				$q->where('fg_warehouse', $request->target_warehouse);
			})
			->select('*', DB::raw('IFNULL(sales_order, material_request) as reference_no'))
			->orderBy('created_at', 'desc')
			->paginate(10);

		$filtered_production_orders = array_column($production_orders->items(), 'production_order');

		$filtered_parent_item_codes = array_column($production_orders->items(), 'parent_item_code');
		$filtered_reference_nos = array_column($production_orders->items(), 'reference_no');
		$delivery_dates = DB::connection('mysql_mes')->table('delivery_date')
			->whereIn('parent_item_code', $filtered_parent_item_codes)
			->where(function($q) use ($filtered_reference_nos) {
				$q->whereIn('reference_no', $filtered_reference_nos)
					->orWhereIn('reference_no', $filtered_reference_nos);
			})
			->select(DB::raw('CONCAT(reference_no, parent_item_code) as id'), 'rescheduled_delivery_date')
			->pluck('rescheduled_delivery_date', 'id')->toArray();

		$prod_details = DB::connection('mysql')->table('tabWork Order')->whereIn('name', $filtered_production_orders)->select('name', 'material_transferred_for_manufacturing', 'docstatus')->get();
		$work_order_details = collect($prod_details)->groupBy('name');

		$manufacture_entry_q = DB::connection('mysql')->table('tabStock Entry')
			->whereIn('work_order', $filtered_production_orders)->where('docstatus', 1)->where('purpose', 'Manufacture')
			->orderBy('posting_date', 'desc')->orderBy('posting_time', 'desc')->get();
		$manufacture_entry = collect($manufacture_entry_q)->groupBy('work_order');

		$manufacture_entries_q = DB::connection('mysql')->table('tabStock Entry')->where('docstatus', 1)->whereIn('work_order', $filtered_production_orders)->where('purpose', 'Manufacture')->get();
		$manufacture_entries = collect($manufacture_entries_q)->groupBy('work_order');

		$sub_assemblies = collect($production_orders->items())->map(function ($q){
			if(false !== stripos($q->item_classification, 'SA - ')){
				return $q->item_code;
			}
		})->filter()->values()->all();

		$item_details = DB::connection('mysql')->table('tabItem Variant Attribute')->whereIn('parent', $sub_assemblies)->orderBy('idx', 'asc')->get();
		$item_details = collect($item_details)->groupBy('parent');

		$production_order_list = [];
		foreach ($production_orders as $row) {
			$prod_status = 'Unknown Status';

			$description = $row->description;
			if(isset($item_details[$row->item_code])){
				$description = collect($item_details[$row->item_code])->pluck('attribute_value')->implode(' ');
				$description = $description ? $description : $row->description;
			}

			if($row->status == 'Not Started'){
				if (isset($work_order_details[$row->production_order]) and $work_order_details[$row->production_order][0]->material_transferred_for_manufacturing > 0) {
					$prod_status = 'Material Issued';
				}else{
					$prod_status = 'Material For Issue';
				}
			}

			if($row->status == 'In Progress'){
				if(count($pending_production_orders) > 0 and in_array($row->production_order, $pending_production_orders->toArray())){
					$prod_status = 'On Queue';
				}
	
				if(count($in_progress_production_orders) > 0 and in_array($row->production_order, $in_progress_production_orders)){
					$prod_status = $row->status;
				}
			}

			if (in_array($row->status, ['Completed', 'In Progress', 'Ready for Feedback', 'Partially Feedbacked', 'Feedbacked'])) {
				if($prod_status != 'In Progress') {
					if ($row->feedback_qty == 0 and $row->produced_qty == $row->qty_to_manufacture) {
						$prod_status = 'For Feedback';
					}

					if ($row->produced_qty > 0 && $row->produced_qty < $row->qty_to_manufacture) {
						$prod_status = 'For Partial Feedback';
					}

					if ($row->feedback_qty >= $row->qty_to_manufacture) {
						$prod_status = 'Feedbacked';
					}else{
						if (isset(collect($manufacture_entry)[$row->production_order]) && $prod_status != 'On Queue' && $row->feedback_qty > 0 && $row->feedback_qty < $row->qty_to_manufacture) {
							$prod_status = 'Partially Feedbacked';
						}
					}
				}
			}

			if($row->status == 'Cancelled' || $row->status == 'Closed'){
				$prod_status = $row->status;
			}

			if(isset($work_order_details[$row->production_order]) && $work_order_details[$row->production_order][0]->docstatus == 2 && $row->status != 'Cancelled'){
				$prod_status = 'Unknown Status';
			}else if(isset($work_order_details[$row->production_order]) && $work_order_details[$row->production_order][0]->docstatus == 1 && $row->status == 'Cancelled'){
				$prod_status = 'Unknown Status';
			}
		
			// get owner of production order
			$owner = explode('@', $row->created_by);
			$owner = ucwords(str_replace('.', ' ', $owner[0]));

			$reference_no = ($row->sales_order) ? $row->sales_order : $row->material_request;
			$rescheduled_delivery_date = array_key_exists($reference_no.$row->parent_item_code, $delivery_dates) ? $delivery_dates[$reference_no.$row->parent_item_code] : null;
			$production_order_list[] = [
				'production_order' => $row->production_order,
				'production_order_status' => $row->status,
				'item_code' => $row->item_code,
				'description' => $description,
				'qty' => $row->qty_to_manufacture,
				'produced_qty' => $row->produced_qty,
				'feedback_qty' => $row->feedback_qty,
				'target_warehouse' => $row->fg_warehouse,
				'operation_id' => $row->operation_id,
				'stock_uom' => $row->stock_uom,
				'reference_no' => $reference_no,
				'delivery_date' => ($rescheduled_delivery_date == null) ?  $row->delivery_date : $rescheduled_delivery_date, // new delivery from delivery table
				'customer' => $row->customer,
				'bom' => $row->bom_no,
				'status' => $prod_status,
				'sales_order_no' => $row->sales_order,
				'material_request' => $row->material_request,
				'ste_entries' => collect($manufacture_entries)->contains('work_order', $row->production_order) ? collect($manufacture_entries[$row->production_order])->pluck('name') : [],
				'count_ste_entries' => collect($manufacture_entries)->contains('work_order', $row->production_order) ? count($manufacture_entries) : null,
				'ste_manufacture' => isset($manufacture_entry[$row->production_order]) ? $manufacture_entry[$row->production_order][0]->name : null,
				'planned_start_date' => $row->planned_start_date,
				'is_scheduled' => $row->is_scheduled,
				'owner' => $owner,
				'parent_item_code'=> $row->parent_item_code,
				'sub_parent_item_code'=> $row->sub_parent_item_code,
				'created_at' =>  Carbon::parse($row->created_at)->format('m-d-Y h:i A')
			];
		}

		$total_production_orders = $production_orders->total();

		return view('reports.tbl_production_orders', compact('production_order_list', 'total_production_orders', 'production_orders'));
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
					->where('work_order', $row->production_order)->where('docstatus', 1)
					->orderBy('posting_date', 'desc')->orderBy('posting_time', 'desc')
					->where('purpose', 'Manufacture')->first();

				$manufacture_entries = DB::connection('mysql')->table('tabStock Entry')
					->where('work_order', $row->production_order)->where('docstatus', 1)
					->where('purpose', 'Manufacture')->pluck('name');

				$status = ($row->qty_to_manufacture == $row->produced_qty) ? 'For Feedback' : 'For Partial Feedback';
				
				if ($row->feedback_qty >= $row->qty_to_manufacture) {
					$status = 'Feedbacked';
				}else{
					if ($manufacture_entry) {
						$status = 'Partially Feedbacked';
					}
				}

				$is_transferred = DB::connection('mysql')->table('tabWork Order')
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

				// $from_time = collect($time_logs_qry)->min('from_time');
				$to_time = collect($time_logs_qry)->max('to_time');

				// $actual_start_date = Carbon::parse($from_time);
				// $actual_end_date = Carbon::parse($to_time);

				// $days = $actual_start_date->diffInDays($actual_end_date);
				// $hours = $actual_start_date->copy()->addDays($days)->diffInHours($actual_end_date);
				// $minutes = $actual_start_date->copy()->addDays($days)->addHours($hours)->diffInMinutes($actual_end_date);
				// $seconds = $actual_start_date->copy()->addDays($days)->addHours($hours)->addMinutes($minutes)->diffInSeconds($actual_end_date);
				// $dur_days = ($days > 0) ? $days .'d' : null;
				// $dur_hours = ($hours > 0) ? $hours .'h' : null;
				// $dur_minutes = ($minutes > 0) ? $minutes .'m' : null;
				// $dur_seconds = ($seconds > 0) ? $seconds .'s' : null;

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
					// 'actual_start_date' => ($from_time) ? Carbon::parse($from_time)->format('m-d-Y h:i A') : '--',
					// 'actual_end_date' => ($from_time) ? Carbon::parse($to_time)->format('m-d-Y h:i A') : '--',
					// 'duration' => $dur_days .' '. $dur_hours . ' '. $dur_minutes . ' '. $dur_seconds,
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
		if (Gate::denies('assign-production-order-schedule')) {
            return response()->json(["error" => 1]);
        }

		DB::connection('mysql')->beginTransaction();
		DB::connection('mysql_mes')->beginTransaction();
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

						DB::table('tabWork Order')->where('name', $name)->update($val_order_no);
						DB::connection('mysql_mes')->table('production_order')->where('production_order', $name)->update($val_order_no);

						$ongoing_spotwelding = DB::connection('mysql_mes')->table('job_ticket as jt')
							->join('spotwelding_qty as spotpart', 'spotpart.job_ticket_id','jt.job_ticket_id')
							->where('jt.production_order', $name)
							->where('spotpart.status', "In Progress")
							->exists();

						$ongoing_timelog = DB::connection('mysql_mes')->table('job_ticket as jt')
							->join('time_logs as tl', 'jt.job_ticket_id','tl.job_ticket_id')
							->where('jt.production_order', $name)
							->where('tl.status', "In Progress")
							->exists();

						if(!$ongoing_spotwelding && !$ongoing_timelog){
							DB::table('tabWork Order')->where('name', $prod)->update($val_erp);
							DB::connection('mysql_mes')->table('production_order')->where('production_order', $name)->update($val_mes);
							DB::table('tabWork Order')->where('name', $name)->update($val_order_no);
							DB::connection('mysql_mes')->table('production_order')->where('production_order', $name)->update($val_order_no);
						}

						// if(DB::connection('mysql_mes')->table('job_ticket as jt')
						// 	->join('spotwelding_qty as spotpart', 'spotpart.job_ticket_id','jt.job_ticket_id')
						// 	->where('jt.production_order', $name)
						// 	->where('spotpart.status', "In Progress")
						// 	->exists()){
						// }else{
						// 	if(DB::connection('mysql_mes')->table('job_ticket as jt')
						// 	->join('time_logs as tl', 'jt.job_ticket_id','tl.job_ticket_id')
						// 	->where('jt.production_order', $name)
						// 	->where('tl.status', "In Progress")
						// 	->exists()){
						// 	}else{
						// 		DB::table('tabWork Order')->where('name', $prod)->update($val_erp);
						// 		DB::connection('mysql_mes')->table('production_order')->where('production_order', $name)->update($val_mes);
						// 		DB::table('tabWork Order')->where('name', $name)->update($val_order_no);
						// 		DB::connection('mysql_mes')->table('production_order')->where('production_order', $name)->update($val_order_no);
						// 	}
						// }
					}
				}
			}
    		DB::connection('mysql')->commit();
			DB::connection('mysql_mes')->commit();
			return response()->json(["success" => 1]);
    	} catch (Exception $e) {
			DB::connection('mysql')->rollback();
			DB::connection('mysql_mes')->rollback();
    		return response()->json(["error" => $e->getMessage()]);
    	}	
	}

    public function update_production_task_schedules(Request $request){
		if (Gate::denies('assign-production-order-schedule')) {
            return response()->json(['success' => 0, 'message' => 'Unauthorized.', 'reload_tbl' => $request->reload_tbl]);
        }
		
		DB::connection('mysql_mes')->beginTransaction();
		try {
			if(!Auth::user()) {
				return response()->json(['success' => 0, 'message' => 'Session Expired. Please login to continue.']);
			}

			$now = Carbon::now();
			$production_order_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)->first();
			if (!$production_order_details) {
				return response()->json(['success' => 0, 'message' => 'Production Order ' . $request->production_order . ' not found.',  'reload_tbl' => $request->reload_tbl]);
			}
	
			if ($production_order_details->status != 'Completed') {
				$new_schedule = strtolower($request->planned_start_date) == 'unscheduled' ? $request->planned_start_date : Carbon::parse($request->planned_start_date);

				$values=[];
				$tasks = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->production_order)->get();
				foreach ($tasks as $row) {
					$values = [
						'last_modified_by' => Auth::user()->employee_name,
						'last_modified_at' => $now->toDateTimeString(),
						'planned_start_date' => $new_schedule,
					];

					DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $row->job_ticket_id)->update($values);
				}

				$current_scheduled_date = $request->current && $request->current != 'unscheduled' ? Carbon::parse($request->current)->format('M. d, Y') : '"Unscheduled"';
				$msg = 'Scheduled start date has been changed from '.$current_scheduled_date.' to '. $new_schedule->format('M. d, Y') .' by '.Auth::user()->employee_name;

				$activity_logs = [
					'action' => 'Change Schedule',
					'message' => $msg,
					'reference' => $production_order_details->production_order,
					'created_by' => Auth::user()->employee_name,
					'created_at' => Carbon::now()->toDateTimeString()
				];

				DB::connection('mysql_mes')->table('activity_logs')->insert($activity_logs); // insert restarted process log in activity logs
			}

			DB::connection('mysql_mes')->commit();
		} catch (Exception $th) {
			DB::connection('mysql_mes')->rollback();

			return response()->json(['success' => 0, 'message' => 'Something went wrong. Please try again.', 'reload_tbl' => $request->reload_tbl]);
		}	
	}

	public function save_shift_schedule(Request $request){
		if (Gate::denies('assign-production-order-schedule')) {
			return response()->json(['success' => 0, 'message' => 'Unauthorized.']);
        }

		DB::connection('mysql')->beginTransaction();
		DB::connection('mysql_mes')->beginTransaction();
		try {
			if(!Auth::user()) {
				return response()->json(['success' => 0, 'message' => 'Session Expired. Please login to continue.']);
			}

			$production_order_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)->whereNotIn('status', ['Cancelled', 'Closed'])->first();

			if(!$production_order_details){
				return response()->json(['success' => 0, 'message' => 'Production order not found.']);
			}

			$checker = DB::connection('mysql_mes')->table('production_order')->where('is_scheduled', 1)->whereDate('planned_start_date', $request->schedule_date)->where('operation_id', $request->operation_id)->whereNotIn('status', ['Cancelled', 'Closed'])->exists();

			if(!$checker){
				DB::connection('mysql_mes')->table('shift_schedule')->insert([
					'shift_id' => $request->selected_shift,
					'date' => $request->schedule_date,
					'scheduled_by' => Auth::user()->employee_name,
					'remarks' => $request->remarks,
					'created_at' => Carbon::now()->toDateTimeString(),
					'created_by' => Auth::user()->email,
					'last_modified_at' => Carbon::now()->toDateTimeString(),
					'last_modified_by' => Auth::user()->email
				]);
			}

			if($request->operation_id == 2){ // Painting Schedule
				DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->production_order)->where('status', 'Pending')->update([
					'planned_start_date' => $request->schedule_date,
					'last_modified_at' => Carbon::now()->toDateTimeString(),
					'last_modified_by' => Auth::user()->email
				]);
			}else{ // Fabrication and Assembly Schedule
				DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)->update([
					'is_scheduled' => 1,
					'planned_start_date' => $request->schedule_date,
					'last_modified_at' => Carbon::now()->toDateTimeString(),
					'last_modified_by' => Auth::user()->email
				]);

				DB::connection('mysql')->table('tabWork Order')->where('name', $request->production_order)->update([
					'scheduled' => 1,
					'planned_start_date' => $request->schedule_date,
					'modified' => Carbon::now()->toDateTimeString(),
					'modified_by' => Auth::user()->email
				]);
			}

			$current_date = $production_order_details->planned_start_date ? Carbon::parse($request->planned_start_date)->format('M. d, Y') : '"Unscheduled"';

			$msg = 'Scheduled start date has been changed from '.$current_date.' to '. Carbon::parse($request->schedule_date)->format('M. d, Y') .' by '.Auth::user()->employee_name;

			$activity_logs = [
				'action' => 'Change Schedule'.($request->operation_id == 2 ? ' - Painting' : null),
				'message' => $msg,
				'reference' => $production_order_details->production_order,
				'created_by' => Auth::user()->employee_name,
				'created_at' => Carbon::now()->toDateTimeString()
			];

			DB::connection('mysql_mes')->table('activity_logs')->insert($activity_logs);

			DB::connection('mysql')->commit();
			DB::connection('mysql_mes')->commit();
			return response()->json(['success' => 1, 'message' => 'Shift schedule updated.']);
		} catch (\Throwable $th) {
			DB::connection('mysql')->rollback();
			DB::connection('mysql_mes')->rollback();

			return response()->json(['success' => 0, 'message' => 'Something went wrong. Please try again later.']);
		}
	}

	public function update_production_order_schedule(Request $request){
		if (Gate::allows('assign-production-order-schedule')) {
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
	}

	// /production_schedule/{id}
	public function production_schedule_module($operation_id){
		if (!Auth::check()) {
			return redirect('/login');
		}
		
		$permissions = $this->get_user_permitted_operation();
		$primary_id=$operation_id;

		switch ($operation_id) {
			case 1:
				$operation_name_text="Fabrication";
				break;
			case 2:
				$operation_name_text="Painting";
				break;
			default:
				$operation_name_text="Assembly";
				break;
		}

		$mes_user_operations = DB::connection('mysql_mes')->table('user')
			->join('operation', 'operation.operation_id', 'user.operation_id')
			->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
			->where('module', 'Production')
			->where('user_access_id', Auth::user()->user_id)->pluck('operation_name')->toArray();

		if($operation_id == 2){
			$get_painting_schedules = $this->get_painting_schedules($primary_id);

			$unscheduled = $get_painting_schedules['unscheduled'];
			$scheduled = $get_painting_schedules['scheduled'];
			$filters = $get_painting_schedules['filters'];
		}else{
			$productionKanban = $this->productionKanban($primary_id);

			$unscheduled = $productionKanban['unscheduled'];
			$scheduled = $productionKanban['scheduled'];
			$filters = $productionKanban['filters'];
		}

		$shifts = DB::connection('mysql_mes')->table('shift')->where('operation_id', $operation_id)->get();
		$shift_schedules = DB::connection('mysql_mes')->table('shift_schedule as ss')
			->join('shift as s', 's.shift_id', 'ss.shift_id')
			->where('s.operation_id', $operation_id)
			->orderBy('date', 'desc')
			->get();
		$shift_schedule = collect($shift_schedules)->groupBy('date');

		return view('production_kanban', compact('operation_name_text','primary_id','unscheduled', 'scheduled', 'mes_user_operations', 'permissions', 'filters', 'shifts', 'shift_schedule', 'operation_id'));
	}
	public function productionKanban($operation_id){
		$unscheduled_prod = DB::connection('mysql_mes')->table('production_order')
		   ->leftJoin('delivery_date', function($join)
            {
                $join->on( DB::raw('IFNULL(production_order.sales_order, production_order.material_request)'), '=', 'delivery_date.reference_no');
                $join->on('production_order.parent_item_code','=','delivery_date.parent_item_code');
            })
			->whereNotIn('production_order.status', ['Stopped', 'Cancelled', 'Closed'])->where('production_order.feedback_qty',0)
			->where('production_order.is_scheduled', 0)->where("production_order.operation_id", $operation_id)
			->select('production_order.*', 'delivery_date.rescheduled_delivery_date')
			->orderBy('production_order.sales_order', 'desc')->orderBy('production_order.material_request', 'desc')->get();

		$unscheduled_prod = collect($unscheduled_prod)->unique('production_order');

		$unescheduled_items = collect($unscheduled_prod)->unique('item_code')->pluck('item_code');
		$unescheduled_items_cycle_time = DB::connection('mysql_mes')->table('item_cycle_time_per_process')->whereIn('item_code', $unescheduled_items)
			->selectRaw('item_code, SUM(cycle_time_in_seconds) as total_cycle_time')->groupBy('item_code')
			->pluck('total_cycle_time', 'item_code')->toArray();

    	$unscheduled = [];
    	foreach ($unscheduled_prod as $row) {
			$stripfromcomma =strtok($row->description, ",");
			$item_cycle_time = array_key_exists($row->item_code, $unescheduled_items_cycle_time) ? $unescheduled_items_cycle_time[$row->item_code] : 0;
			$item_total_cycle_time = $row->qty_to_manufacture * $item_cycle_time;

			$seconds = $item_total_cycle_time%60;
			$minutes = floor(($item_total_cycle_time%3600)/60);
			$hours = floor(($item_total_cycle_time%86400)/3600);
			$days = floor(($item_total_cycle_time%2592000)/86400);
			$months = floor($item_total_cycle_time/2592000);
			
			$dur_months = ($months > 0) ? $months .'M' : null;
			$dur_days = ($days > 0) ? $days .'d' : null;
			$dur_hours = ($hours > 0) ? $hours .'h' : null;
			$dur_minutes = ($minutes > 0) ? $minutes .'m' : null;
			$dur_seconds = ($seconds > 0) ? $seconds .'s' : null;

			$total_duration = $dur_months . ' '. $dur_days . ' ' .$dur_hours . ' '. $dur_minutes;// . ' ' . $dur_seconds;

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
				'item_code' => $row->item_code,
				'process_stat'=> $this->material_status_stockentry($row->production_order, $row->status, $row->qty_to_manufacture, $row->feedback_qty, $row->produced_qty),
				'approximate_cycle_time' => trim($total_duration),
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
			$shift_sched = $this->get_prod_shift_sched($date->format('Y-m-d'), $operation_id);
			$scheduled[] = [
				'shift'=> $shift_sched,
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
			->where('jt.planned_start_date', null)
			->whereRaw('pro.qty_to_manufacture > pro.feedback_qty')
			->whereNotIn('pro.status', ['Completed', 'Stopped', 'Cancelled', 'Closed'])
			->where('jt.workstation', 'Painting')
			->select('delivery_date.rescheduled_delivery_date','pro.production_order', 'jt.workstation', 'pro.customer', 'pro.delivery_date','pro.description', 'pro.qty_to_manufacture','pro.item_code','pro.stock_uom','pro.project','pro.classification','pro.parts_category', 'pro.sales_order', 'pro.material_request', 'pro.produced_qty', 'pro.job_ticket_print','pro.withdrawal_slip_print', 'pro.parent_item_code', 'pro.status','jt.sequence', 'pro.feedback_qty')
			->groupBy('delivery_date.rescheduled_delivery_date','pro.production_order', 'jt.workstation', 'pro.customer', 'pro.delivery_date','pro.description', 'pro.qty_to_manufacture','pro.item_code','pro.stock_uom','pro.project','pro.classification','pro.parts_category', 'pro.sales_order', 'pro.material_request',  'pro.produced_qty','pro.job_ticket_print','pro.withdrawal_slip_print', 'pro.parent_item_code', 'pro.status','jt.sequence', 'pro.feedback_qty')
			->orderBy('pro.created_at', 'desc')->get();

		$prod_items = collect($jobtickets_production)->unique('item_code')->pluck('item_code');
		$prod_items_cycle_time = DB::connection('mysql_mes')->table('item_cycle_time_per_process')->whereIn('item_code', $prod_items)
			->selectRaw('item_code, SUM(cycle_time_in_seconds) as total_cycle_time')->groupBy('item_code')
			->pluck('total_cycle_time', 'item_code')->toArray();

		$unscheduled = [];
		foreach ($jobtickets_production as $row) {
			$jt = DB::connection('mysql_mes')->table('job_ticket as jt')->where('production_order',  $row->production_order)->get();
			$prod_stat = DB::connection('mysql_mes')->table('production_order as prod')->where('production_order',  $row->production_order)->first();
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

			$item_cycle_time = array_key_exists($row->item_code, $prod_items_cycle_time) ? $prod_items_cycle_time[$row->item_code] : 0;
			$item_total_cycle_time = $row->qty_to_manufacture * $item_cycle_time;

			$seconds = $item_total_cycle_time%60;
			$minutes = floor(($item_total_cycle_time%3600)/60);
			$hours = floor(($item_total_cycle_time%86400)/3600);
			$days = floor(($item_total_cycle_time%2592000)/86400);
			$months = floor($item_total_cycle_time/2592000);
			
			$dur_months = ($months > 0) ? $months .'M' : null;
			$dur_days = ($days > 0) ? $days .'d' : null;
			$dur_hours = ($hours > 0) ? $hours .'h' : null;
			$dur_minutes = ($minutes > 0) ? $minutes .'m' : null;
			$dur_seconds = ($seconds > 0) ? $seconds .'s' : null;

			$total_duration = $dur_months . ' '. $dur_days . ' ' .$dur_hours . ' '. $dur_minutes;// . ' ' . $dur_seconds;
			
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
				'item_code' => $row->item_code,
				'approximate_cycle_time' => trim($total_duration),
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

			$shift_sched = $this->get_prod_shift_sched($date->format('Y-m-d'), $operation_id);
			$scheduled[] = [
				'shift'=> $shift_sched,
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

	public function get_scheduled_painting($schedule_date){
		$orders = DB::connection('mysql_mes')->table('production_order as pro')
			->join('job_ticket as jt', 'pro.production_order','jt.production_order')
			->leftJoin('delivery_date', function($join){
				$join->on( DB::raw('IFNULL(pro.sales_order, pro.material_request)'), '=', 'delivery_date.reference_no');
				$join->on('pro.parent_item_code','=','delivery_date.parent_item_code');
			})
			->whereRaw('pro.qty_to_manufacture > pro.feedback_qty')
			->whereNotIn('pro.status', ['Completed', 'Cancelled', 'Closed'])
			->where('jt.workstation', 'Painting')
			->whereDate('jt.planned_start_date', $schedule_date)
			->select('delivery_date.rescheduled_delivery_date','pro.production_order', 'pro.customer', 'pro.delivery_date', 'pro.item_code', 'pro.description', 'pro.qty_to_manufacture', 'pro.stock_uom', 'pro.produced_qty','pro.classification','pro.parts_category', 'pro.sales_order', 'pro.material_request', 'pro.status', 'pro.job_ticket_print','pro.withdrawal_slip_print', 'pro.parent_item_code', 'pro.status','jt.sequence','pro.feedback_qty')
			->groupBy('delivery_date.rescheduled_delivery_date','pro.production_order', 'pro.customer', 'pro.delivery_date', 'pro.item_code', 'pro.description', 'pro.qty_to_manufacture', 'pro.stock_uom', 'pro.produced_qty','pro.classification','pro.parts_category', 'pro.sales_order', 'pro.material_request','pro.status', 'pro.job_ticket_print','pro.withdrawal_slip_print', 'pro.parent_item_code', 'pro.status','jt.sequence','pro.feedback_qty')
			->orderBy('jt.sequence', 'asc')->get();

		$prod_items = collect($orders)->unique('item_code')->pluck('item_code');
		$prod_items_cycle_time = DB::connection('mysql_mes')->table('item_cycle_time_per_process')->whereIn('item_code', $prod_items)
			->selectRaw('item_code, SUM(cycle_time_in_seconds) as total_cycle_time')->groupBy('item_code')
			->pluck('total_cycle_time', 'item_code')->toArray();
	
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
			
		$item_cycle_time = array_key_exists($row->item_code, $prod_items_cycle_time) ? $prod_items_cycle_time[$row->item_code] : 0;
		$item_total_cycle_time = $row->qty_to_manufacture * $item_cycle_time;

		$seconds = $item_total_cycle_time%60;
		$minutes = floor(($item_total_cycle_time%3600)/60);
		$hours = floor(($item_total_cycle_time%86400)/3600);
		$days = floor(($item_total_cycle_time%2592000)/86400);
		$months = floor($item_total_cycle_time/2592000);
		
		$dur_months = ($months > 0) ? $months .'M' : null;
		$dur_days = ($days > 0) ? $days .'d' : null;
		$dur_hours = ($hours > 0) ? $hours .'h' : null;
		$dur_minutes = ($minutes > 0) ? $minutes .'m' : null;
		$dur_seconds = ($seconds > 0) ? $seconds .'s' : null;

		$total_duration = $dur_months . ' '. $dur_days . ' ' .$dur_hours . ' '. $dur_minutes;// . ' ' . $dur_seconds;
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
				'item_code' => $row->item_code,
				'approximate_cycle_time' => trim($total_duration),
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
		$operation_id=($operation_id == 0)? '2' : $operation_id;
		$scheduled = [];
		$special_shift_shift= DB::connection('mysql_mes')
		->table('shift_schedule')
		->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
		->where('shift_schedule.date', $date)
		->where('shift.operation_id', $operation_id)
		->where('shift.shift_type', 'Special Shift')
		->select('shift.shift_type', 'shift.time_in', 'shift.time_out')
		->get();
		
		if(count($special_shift_shift) == 0){
			$shifts= DB::connection('mysql_mes')
			->table('shift')
			->where('shift.operation_id', $operation_id)
			->where('shift_type', 'Regular Shift')
			->first();
			$scheduled[] = [
				'time_in'=> empty($shifts)? 'NO SHIFT FOUND' : $shifts->time_in,
				'time_out' =>  empty($shifts)? '' : $shifts->time_out,
				'shift_type' =>  empty($shifts)? "No Shift" : $shifts->shift_type,
			];
		}else{
			foreach($special_shift_shift as $r){
				$scheduled[] = [
					'time_in'=> $r->time_in,
					'time_out' =>  $r->time_out,
					'shift_type' =>  $r->shift_type,
				];
			}
		}
		$o_shift_shift= DB::connection('mysql_mes')
		->table('shift_schedule')
		->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
		->where('shift_schedule.date', $date)
		->where('shift.operation_id', $operation_id)
		->where('shift.shift_type', 'Overtime Shift')
		->select('shift.shift_type', 'shift.time_in', 'shift.time_out')
		->get();
		foreach($o_shift_shift as $r){
			$scheduled[] = [
				'time_in'=> $r->time_in,
				'time_out' =>  $r->time_out,
				'shift_type' =>  $r->shift_type,
			];
		}	

		$sched= collect($scheduled);
		return $scheduled;
    }

	public function get_customer_reference_no($customer){
		return DB::connection('mysql_mes')->table('production_order')->whereNotIn('status', ['Completed', 'Cancelled'])
			->where('customer', $customer)->selectRaw('IFNULL(sales_order, material_request) as reference')
			->distinct()->orderBy('reference', 'asc')->pluck('reference');
	}

	public function get_customers(){
		return DB::connection('mysql_mes')->table('production_order')->whereNotIn('status', ['Completed', 'Cancelled'])
			->whereNotNull('customer')->distinct()->orderBy('customer', 'asc')->pluck('customer');
	}

	public function get_reference_production_items(Request $request, $reference){
		if($request->item_type == 'parent'){
			return DB::connection('mysql_mes')->table('production_order')->whereNotIn('status', ['Completed', 'Cancelled'])
			->where(function($q) use ($reference) {
				$q->where('sales_order', $reference)
					->orWhere('material_request', $reference);
			})
			->distinct()->orderBy('parent_item_code', 'asc')->pluck('parent_item_code');
		}

		if($request->item_type == 'sub-parent'){
			return DB::connection('mysql_mes')->table('production_order')->whereNotIn('status', ['Completed', 'Cancelled'])
			->where(function($q) use ($reference) {
				$q->where('sales_order', $reference)
					->orWhere('material_request', $reference);
			})
			->where('parent_item_code', $request->parent_item)
			->whereNotNull('sub_parent_item_code')
			->distinct()->orderBy('sub_parent_item_code', 'asc')->pluck('sub_parent_item_code');
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
			->distinct()->orderBy('item_code', 'asc')->pluck('item_code');
	}

	public function getScheduledProdOrders($schedule_date, $operation_id){
		$orders = DB::connection('mysql_mes')->table('production_order')
			->distinct()
			->leftJoin('delivery_date', function($join)
            	{
                    $join->on( DB::raw('IFNULL(production_order.sales_order, production_order.material_request)'), '=', 'delivery_date.reference_no');
                    $join->on('production_order.parent_item_code','=','delivery_date.parent_item_code');
                })
    		->whereNotIn('production_order.status', ['Cancelled', 'Closed'])->where('production_order.is_scheduled', 1)
			->whereDate('production_order.planned_start_date', $schedule_date)
			->where("production_order.operation_id", $operation_id)
			->whereRaw('production_order.qty_to_manufacture > production_order.feedback_qty')
			->select('production_order.*', 'delivery_date.rescheduled_delivery_date')
    		->orderBy('production_order.order_no', 'asc')->orderBy('production_order.order_no', 'asc')
			->orderBy('production_order.created_at', 'desc')->get();

		$orders_items = collect($orders)->unique('item_code')->pluck('item_code');
		$orders_items_cycle_time = DB::connection('mysql_mes')->table('item_cycle_time_per_process')->whereIn('item_code', $orders_items)
			->selectRaw('item_code, SUM(cycle_time_in_seconds) as total_cycle_time')->groupBy('item_code')
			->pluck('total_cycle_time', 'item_code')->toArray();
    	$scheduled = [];
    	foreach($orders as $row){
    		$stripfromcomma =strtok($row->description, ",");
			$item_cycle_time = array_key_exists($row->item_code, $orders_items_cycle_time) ? $orders_items_cycle_time[$row->item_code] : 0;
			$item_total_cycle_time = $row->qty_to_manufacture * $item_cycle_time;

			$seconds = $item_total_cycle_time%60;
			$minutes = floor(($item_total_cycle_time%3600)/60);
			$hours = floor(($item_total_cycle_time%86400)/3600);
			$days = floor(($item_total_cycle_time%2592000)/86400);
			$months = floor($item_total_cycle_time/2592000);
			
			$dur_months = ($months > 0) ? $months .'M' : null;
			$dur_days = ($days > 0) ? $days .'d' : null;
			$dur_hours = ($hours > 0) ? $hours .'h' : null;
			$dur_minutes = ($minutes > 0) ? $minutes .'m' : null;
			$dur_seconds = ($seconds > 0) ? $seconds .'s' : null;

			$total_duration = $dur_months . ' '. $dur_days . ' ' .$dur_hours . ' '. $dur_minutes;// . ' ' . $dur_seconds;

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
				'item_code' => $row->item_code,
				'process_stat'=> $this->material_status_stockentry($row->production_order, $row->status, $row->qty_to_manufacture,$row->feedback_qty, $row->produced_qty),
				'approximate_cycle_time' => trim($total_duration),
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
    	$permissions = $this->get_user_permitted_operation();

    	return view('production_schedule', compact('permissions'));
    }

    public function getWorkstationSched(Request $request){
    	$list = DB::connection('mysql')->table('tabWork Order as po')
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
    public function operatorpage($id = null){
		if (strtolower($id) == 'spotwelding') {
			return redirect('/operator/Spotwelding');
		}else if(strtolower($id) == 'painting'){
			return redirect('/operator/Painting/Loading');
		}

		$tabWorkstation= DB::connection('mysql_mes')->table('workstation')->where('workstation_name', $id)
			->select('workstation_name', 'workstation_id', 'operation_id')->first();
			
		if(!$tabWorkstation || !$id){
			$workstations = DB::connection('mysql_mes')->table('workstation')
				->join('operation', 'operation.operation_id', 'workstation.operation_id')
				->select('operation.operation_id', 'operation.operation_name', 'workstation.workstation_id', 'workstation.workstation_name')
				->get();
			$workstations = collect($workstations)->groupBy('operation_name');

			return view('workstation_dashboard', compact('workstations'));
		}

		$workstation_list = DB::connection('mysql_mes')->table('workstation')
			->where('operation_id', $tabWorkstation->operation_id)
        	->orderBy('order_no', 'asc')->pluck('workstation_name');
        
        $now = Carbon::now();
        $workstation=$tabWorkstation->workstation_name;
        $workstation_id= $tabWorkstation->workstation_id;
        $workstation_name=$id;
        $date = $now->format('M d Y');
		$day_name= $now->format('l');
		$time=$now->format('h:i:s');
		$breaktime = [];
		$shift= DB::connection('mysql_mes')->table('shift_schedule')
			->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
			->whereDate('shift_schedule.date', $now)
			->where('shift.operation_id', $tabWorkstation->operation_id)
			->where('shift.shift_type', 'Special Shift')
			->select('shift.shift_id')->first();
			if(empty($shift)){
				$reg_shift= DB::connection('mysql_mes')
					->table('shift')
					->where('shift.operation_id',  $tabWorkstation->operation_id)
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
			->where('shift.operation_id', $tabWorkstation->operation_id)
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
		$operation_id = $tabWorkstation->operation_id;
        $operation = DB::connection('mysql_mes')->table('operation')->where('operation_id', $operation_id)->pluck('operation_name')->first();
        return view('operator_workstation_dashboard', compact('workstation','workstation_name', 'day_name', 'date', 'workstation_list', 'workstation_id', 'operation_id', 'breaktime_data', 'operation'));
    }

	public function update_maintenance_task(Request $request){
		DB::connection('mysql_mes')->beginTransaction();
		try{
			if(!$request->user_id){
				return response()->json(['success' => 0, 'message' => 'Please enter your access ID.']);
			}

			$operator = DB::connection('mysql_mes')->table('user')
				->join('user_group', 'user.user_group_id', 'user_group.user_group_id')
				->where('user_group.module', 'Maintenance')->where('user_access_id', $request->user_id)
				->get();

			$user_info = collect($operator)->first();
			if(!$operator || !$user_info){
				return response()->json(['success' => 0, 'message' => 'User not found or not allowed.']);
			}

			$operator_in_progress_task = DB::connection('mysql_mes')->table('machine_breakdown_timelogs')->where('machine_breakdown_id', '!=', $request->machine_breakdown_id)->where('operator_id', $request->user_id)->where('status', 'In Progress')->exists();

			if($operator_in_progress_task){
				return response()->json(['success' => 0, 'message' => 'User has In progress task.']);
			}

			$breakdown_details = DB::connection('mysql_mes')->table('machine_breakdown')->where('machine_breakdown_id', $request->machine_breakdown_id)->first();
			if(!$breakdown_details){
				return response()->json(['success' => 0, 'message' => 'Maintenance report not found.']);
			}

			$now = Carbon::now();
			
			if($breakdown_details->status != 'In Process'){
				$status = 'In Process';
				$timelog_status = 'In Progress';
				if($request->is_completed == 1){
					$status = $breakdown_details->hold_reason ? 'On Hold' : 'Pending';
					$timelog_status = 'Completed';
				}
			}else{
				$status = 'Pending';
				$timelog_status = 'Completed';
			}

			$update = [
				'status' => $status,
				'last_modified_by' => $user_info->employee_name,
				'last_modified_at' => $now->toDateTimeString()
			];

			if($status == 'In Process'){
				$update['work_started'] = $now->toDateTimeString();
			}

			DB::connection('mysql_mes')->table('machine_breakdown')->where('machine_breakdown_id', $request->machine_breakdown_id)->update($update);

			$in_progress_log = DB::connection('mysql_mes')->table('machine_breakdown_timelogs')->where('machine_breakdown_id', $request->machine_breakdown_id)->where('operator_id', $request->user_id)->where('status', 'In Progress')->orderBy('created_at', 'desc')->first();

			if($in_progress_log){
				$start_time = Carbon::parse($in_progress_log->start_time);
				$end_time = Carbon::now()->toDateTimeString();
				$duration = Carbon::parse($end_time)->diffInSeconds($start_time) / 3600;

				DB::connection('mysql_mes')->table('machine_breakdown_timelogs')->where('time_log_id', $in_progress_log->time_log_id)->update([
					'status' => $timelog_status,
					'end_time' => $now->toDateTimeString(),
					'duration_in_hours' => $duration,
					'last_modified_by' => $user_info->employee_name,
					'last_modified_at' => $now->toDateTimeString()
				]);
			}else{
				DB::connection('mysql_mes')->table('machine_breakdown_timelogs')->insert([
					'machine_breakdown_id' => $request->machine_breakdown_id,
					'machine_id' => $request->machine_id,
					'start_time' => $now->toDateTimeString(),
					'operator_id' => $request->user_id,
					'operator_name' => $user_info->employee_name,
					'status' => $timelog_status,
					'created_by' => $user_info->employee_name
				]);
			}

			DB::connection('mysql_mes')->commit();

            return response()->json(['success' => 1, 'message' => 'Task has been updated.']);
        } catch (Exception $e) {
			DB::connection('mysql_mes')->rollback();
            return response()->json(["error" => $e->getMessage()]);
        }
	}


    public function current_data_operator($workstation){
		$now = Carbon::now()->startOfDay()->toDateTimeString();
        $tasks = DB::connection('mysql_mes')->table('job_ticket AS jt')
			->join('production_order AS po', 'jt.production_order', 'po.production_order')
			->join('time_logs as tl', 'tl.job_ticket_id', 'jt.job_ticket_id')
			->where('jt.workstation', $workstation)->whereNotIn('po.status', ['Cancelled', 'Closed'])
			->whereDate('po.planned_start_date', $now)
			->select('po.production_order', 'po.status', 'jt.status as jt_status', 'po.qty_to_manufacture', 'tl.status as tl_status', 'po.planned_start_date')
			->get();

		$pending_tasks = DB::connection('mysql_mes')->table('job_ticket AS jt')
			->join('production_order AS po', 'jt.production_order', 'po.production_order')
			->where('jt.workstation', $workstation)->whereNotIn('po.status', ['Cancelled', 'Closed'])->where('jt.status', 'Pending')
			->whereDate('po.planned_start_date', $now)
			->select('po.production_order', 'po.status', 'jt.status as jt_status', 'po.qty_to_manufacture', 'jt.completed_qty', 'po.planned_start_date')
			->get();

		$pending_qty = collect($pending_tasks)->map(function ($q){
			$pending_qty = $q->qty_to_manufacture - $q->completed_qty;
			$pending_qty = $pending_qty > 0 ? $pending_qty : 0;

			$arr = [
				'production_order' => $q->production_order,
				'qty' => $pending_qty
			];

			return $arr;
		});

		$production_orders = array_column($tasks->toArray(), 'production_order');
		$rejects = DB::connection('mysql_mes')->table('job_ticket AS jt')
			->join('time_logs AS t', 't.job_ticket_id', 'jt.job_ticket_id')
			->where('jt.workstation', $workstation)->whereIn('jt.production_order', $production_orders)
			->sum('t.reject');
			
		$pending = $pending_qty->sum('qty');
		$inprogress = collect($tasks)->where('tl_status', 'In Progress')->sum('qty_to_manufacture');
		$completed = collect($tasks)->where('jt_status', 'Completed')->sum('qty_to_manufacture');

        $data = [
            'completed' => number_format($completed),
            'pending' => number_format($pending),
            'inprogress' => number_format($inprogress),
            'rejects' => number_format($rejects)
        ];

       	return $data;
    }

    public function operators_workstation_TaskList($workstation, $status){
        try {
			$now = Carbon::now();
        	if ($status == 'Pending') {
	    		$job_ticket_qry = DB::connection('mysql_mes')->table('job_ticket')
	    			->join('production_order', 'job_ticket.production_order', 'production_order.production_order')
	    			->where('job_ticket.workstation', $workstation)->whereNotIn('production_order.status', ['Cancelled', 'Closed'])
					->where('job_ticket.status', 'Pending')->whereDate('production_order.planned_start_date', $now)
	    			->select('production_order.customer', 'production_order.qty_to_manufacture', 'produced_qty', 'production_order.production_order', 'production_order.item_code', 'job_ticket.status', 'job_ticket.workstation', DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process'))
	                ->orderBy('production_order.order_no', 'asc')->orderBy('production_order.planned_start_date', 'asc')->get();

	            $task_list = [];
	            foreach ($job_ticket_qry as $row) {
		    		$task_list[] = [
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
				
				$task_list = collect($task_list)->groupBy('process');

		    	return view('tables.tbl_operator_workstation', compact('task_list', 'status'));
	    	}

        	$today = Carbon::now()->format('Y-m-d');
            $tasks = DB::connection('mysql_mes')->table('job_ticket AS jt')
            	->join('production_order AS po', 'jt.production_order', 'po.production_order')
            	->when($workstation == 'Spotwelding', function ($query){
    				return $query->join('spotwelding_qty AS t', 'jt.job_ticket_id', 't.job_ticket_id');
    			}, function ($query) {
    				return $query->join('time_logs AS t', 'jt.job_ticket_id', 't.job_ticket_id');;
    			})
            	->where('jt.workstation', $workstation)->whereNotIn('po.status', ['Cancelled', 'Closed'])
                ->when($status != 'Rejects', function ($query) use ($status) {
    				return $query->where('t.status', $status);
    			}, function ($query) {
    				return $query->where('t.reject', '>', 0);
				})
				->whereDate('po.planned_start_date', $now)
                ->select('jt.job_ticket_id', 'po.customer', 'po.qty_to_manufacture', 'po.production_order', 'po.item_code', 't.status', 't.operator_name','jt.workstation', 't.from_time', 't.to_time','t.machine_code', 't.time_log_id', 't.good', 't.reject', DB::raw('(SELECT process_name FROM process WHERE process_id = jt.process_id) AS process'))
                ->orderBy('po.order_no', 'asc')->orderBy('po.planned_start_date', 'asc')->paginate(100);

            $task_list = [];
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
            	$task_list[] = [
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
			
			$task_list = collect($task_list)->groupBy('process');

            return view('tables.tbl_operator_workstation', compact('task_list', 'status'));
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function submit_quality_check(Request $request){
		DB::connection('mysql_mes')->beginTransaction();
		DB::connection('mysql')->beginTransaction();
        try {
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
				}

				$process_id = ($request->qc_type == 'Random Inspection') ? $job_ticket_details->process_id : $time_log_details->process_id;

				$update_job_ticket = $this->update_job_ticket($time_log_details->job_ticket_id);

				if(!$update_job_ticket){
					DB::connection('mysql')->rollback();
					DB::connection('mysql_mes')->rollback();

					return response()->json(['success' => 0, 'message' => 'An error occured. Please try again.']);
				}

				DB::connection('mysql')->commit();
				DB::connection('mysql_mes')->commit();

				return response()->json(['success' => 1, 'message' => 'Task updated.', 'details' => ['production_order' => $production_order, 'workstation' => $workstation]]);
			}
		} catch (Exception $e) {
			DB::connection('mysql')->rollback();
			DB::connection('mysql_mes')->rollback();

            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function get_qa_inspection_status($reference_type, $reference_id){
		$query = DB::connection('mysql_mes')->table('quality_inspection')
			->where('reference_id', $reference_id)->where('reference_type', $reference_type)
			->where('status', '!=', 'For Confirmation')->orderBy('created_at', 'desc')->first();

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

	// /restart_task
    public function restart_task(Request $request){
		// insert logs
		$workstation = DB::connection('mysql_mes')->table('job_ticket as jt')
			->join('time_logs as logs', 'jt.job_ticket_id', 'logs.job_ticket_id')->where('logs.time_log_id', $request->id)->first();

		if(!$workstation){
			return response()->json(['success' => 0, 'message' => 'Task not found.']);
		}

		$reference_type = ($workstation->workstation == 'Spotwelding') ? 'Spotwelding' : 'Time Logs';
		$reference_id = ($workstation->workstation == 'Spotwelding') ? $workstation->job_ticket_id : $workstation->time_log_id;
		$qa_inspection_status = $this->get_qa_inspection_status($reference_type, $reference_id);

		if($qa_inspection_status != 'Pending'){
			return response()->json(['success' => 0, 'message' => 'Cannot restart QA Inspected tasks.']);
		}

		$activity_logs = [
			'action' => 'Restarted Process',
			'message' => $workstation->workstation.' process has been restarted by '.$workstation->operator_name,
			'created_by' => $workstation->operator_name,
			'reference' => $workstation->production_order,
			'created_at' => Carbon::now()->toDateTimeString()
		];

		DB::connection('mysql_mes')->table('activity_logs')->insert($activity_logs); // insert restarted process log in activity logs

    	DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $request->id)->delete();
		
		$jctl = DB::connection('mysql')->table('tabJob Card Time Log')->where('mes_timelog_id', $request->id);

		$job_card_time_log = $jctl->first();
		if ($job_card_time_log) {
			$job_card_id = $job_card_time_log->parent;

			$jctl->delete();

			$this->update_job_card_status($job_card_id);
		}

		$count_time_logs = DB::connection('mysql_mes')->table('job_ticket as jt')
			->join('time_logs as logs', 'jt.job_ticket_id', 'logs.job_ticket_id')
			->where('jt.workstation', '!=', 'Spotwelding')
			->where('logs.job_ticket_id', $workstation->job_ticket_id)->count();

		$count_time_logs += DB::connection('mysql_mes')->table('job_ticket as jt')
			->join('spotwelding_qty as logs', 'jt.job_ticket_id', 'logs.job_ticket_id')
			->where('jt.workstation', 'Spotwelding')->where('logs.job_ticket_id', $workstation->job_ticket_id)->count();

		if ($count_time_logs <= 0) {
			DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $workstation->job_ticket_id)->update(['status' => 'Pending']);

			$count_started_job_ticket = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $workstation->production_order)->where('status', '!=', 'Pending')->count();
			if ($count_started_job_ticket <= 0) {
				DB::connection('mysql_mes')->table('production_order')->where('production_order', $workstation->production_order)->update(['status' => 'Not Started']);
			}
		}
	
    	return response()->json(['success' => 1, 'message' => 'Task has been updated.']);
    }

    public function reset_task(Request $request){
		if(!Auth::user()) {
            return response()->json(['success' => 0, 'message' => 'Session Expired. Please login to continue.']);
        }

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
    	return DB::connection('mysql_mes')->table('process_assignment')->join('process', 'process.id', 'process_assignment.process_id')
			->where('process_assignment.workstation_id', $workstation)->select('process.id', 'process.process')->distinct()->get();
    }

    public function update_process(Request $request){
		if (Gate::denies('assign-bom-process')) {
            return response()->json(["error" => 'Unauthorized.']);
        }

    	try {
    		if ($request->id) {
    			$jt_details = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $request->id)
    				->whereNotIn('status', ['Pending'])->first();
    			if ($jt_details) {
    				return response()->json(['success' => 0, 'message' => 'Task already Completed / In Progress.']);
    			}

				DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $request->id)
					->update(['process' => $request->process, 'status' => 'Pending']);

    			return response()->json(['success' => 1, 'message' => 'Task updated.']);
    		}
    	} catch (Exception $e) {
    		return response()->json(["error" => $e->getMessage()]);
    	}
    }

    // SecondaryController
    public function update_machine_path(Request $request){
		if (Gate::denies('manage-machines')) {
            return redirect()->back()->with(['message' => 'Unauthorized.']);
        }

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

        return redirect()->back()->with(['message' => 'Machine has been successfully updated!']);
    }

    public function get_production_order_task($production_order, $workstation, Request $request){
    	$production_order_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->first();
    	if (!$production_order_details) {
    		return response()->json(['success' => 0, 'message' => 'Production Order ' . $production_order . ' not found.']);
    	}

		if (in_array($production_order_details->status, ['Cancelled', 'Closed'])) {
    		return response()->json(['success' => 0, 'message' => 'Production Order <b>' . $production_order . '</b> was <b>'.strtoupper($production_order_details->status).'</b>.']);
    	}

		$workstation_machine = null;
		if (!$request->assembly_operator) {
			$check_prod_workstation_exist = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $production_order)
				->where('workstation', $workstation)->first();
			if (!$check_prod_workstation_exist) {
				return response()->json(['success' => 0, 'message' => 'Production Order not available in this workstation.']);
			}

			$workstation_machine = $workstation;		
		}

    	$process_list = $this->get_production_workstation_process($production_order, $workstation_machine, $production_order_details->qty_to_manufacture);

    	$details = ['production_order' => $production_order_details, 'tasks' => $process_list, 'machine_code' => $workstation];

    	return response()->json(['success' => 1, 'message' => 'Task Found.', 'details' => $details]);
    }

    public function start_unassigned_task(Request $request){
		DB::connection('mysql_mes')->beginTransaction();
		DB::connection('mysql')->beginTransaction();
    	try {
    		if (!$request->operator_id) {
    			return response()->json(['success' => 0, 'message' => 'Please enter Operator ID.']);
    		}

    		$now = Carbon::now();
	    	$operator = DB::connection('mysql_essex')->table('users')->where('user_id', $request->operator_id)->first();
	    	if (!$operator) {
	    		return response()->json(['success' => 0, 'message' => 'Operator not found.']);
	    	}

			$machine_details = DB::connection('mysql_mes')->table('machine')->where('machine_code', $request->machine_code)->first();
			if($machine_details && $machine_details->operation_id < 3){
				$operator_in_progress_task = DB::connection('mysql_mes')->table('job_ticket')
					->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
					->where('time_logs.operator_id', $request->operator_id)
					->where('time_logs.status', 'In Progress')->first();

				$operator_in_progress_spotwelding = DB::connection('mysql_mes')->table('job_ticket')
					->join('spotwelding_qty', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
					->where('spotwelding_qty.operator_id', $request->operator_id)
					->where('spotwelding_qty.status', 'In Progress')->first();

				if ($operator_in_progress_task) {
					return response()->json(['success' => 0, 'message' => "Operator has in-progress task. " . $operator_in_progress_task->production_order]);
				}

				if ($operator_in_progress_spotwelding) {
					return response()->json(['success' => 0, 'message' => "Operator has in-progress task. " . $operator_in_progress_spotwelding->production_order]);
				}
			}

			$operator_existing_ongoing_backlog_task = DB::connection('mysql_mes')->table('job_ticket')
				->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
				->where('time_logs.operator_id', $request->operator_id)->whereDate('time_logs.from_time', '<', Carbon::now()->startOfDay()->format('Y-m-d'))
				->where('time_logs.status', 'In Progress')->first();

			if ($operator_existing_ongoing_backlog_task) {
				return response()->json(['success' => 0, 'message' => "Operator has on-going task from previous date. " . $operator_existing_ongoing_backlog_task->production_order]);
			}

			$operator_existing_ongoing_backlog_task_spotwelding = DB::connection('mysql_mes')->table('job_ticket')
				->join('spotwelding_qty', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
				->where('spotwelding_qty.operator_id', $request->operator_id)->whereDate('spotwelding_qty.from_time', '<', Carbon::now()->startOfDay()->format('Y-m-d'))
				->where('spotwelding_qty.status', 'In Progress')->first();

			if ($operator_existing_ongoing_backlog_task_spotwelding) {
				return response()->json(['success' => 0, 'message' => "Operator has on-going task from previous date. " . $operator_existing_ongoing_backlog_task_spotwelding->production_order]);
			}

	    	$machine_name = $machine_details->machine_name;
				
			$production_order = DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)->first();
			if ($production_order->status == 'Cancelled') {
				return response()->json(['success' => 0, 'message' => 'Production Order <b>' . $production_order->production_order . '</b> was <b>CANCELLED</b>.']);
			}

			if ($production_order->qty_to_manufacture == $production_order->feedback_qty) {
				return response()->json(['success' => 0, 'message' => 'Production Order <b>' . $production_order->production_order . '</b> was already <b>COMPLETED</b>.']);
			}

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
					->where('ste.work_order', $request->production_order)
					->where(function($q){
						$q->where('sted.status', 'Issued')
							->orWhere('ste.docstatus', 1);
					})
					->exists();

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
		
			// check for existing wip (same workstation and machine)
			$timelog_wip_existing = DB::connection('mysql_mes')->table('time_logs')
				->where('job_ticket_id', $values['job_ticket_id'])
				->where('machine_code', $values['machine_code'])
				->where('operator_id', $values['operator_id'])
				->where('status', 'In Progress')
				->first();

			if ($timelog_wip_existing) {
				return response()->json(['success' => 0, 'message' => 'Task is already on-going.']);
			}

	    	$timelog_id = DB::connection('mysql_mes')->table('time_logs')->insertGetId($values);

			$job_card = DB::connection('mysql')->table('tabJob Card')->where('work_order', $request->production_order)
				->where('workstation', $job_ticket_details->workstation)->where('docstatus', 0)->first();
			if ($job_card) {
				$employee = DB::connection('mysql')->table('tabEmployee')->where('employee_name', $operator->employee_name)->first();
				$employee_id = ($employee) ? $employee->name : null;
				$max_idx = DB::connection('mysql')->table('tabJob Card Time Log')->where('parent', $job_card->name)->max('idx');
				$job_card_time_log = [
					'name' => 'mes' . uniqid(),
					'creation' => $now->toDateTimeString(),
					'modified' => $now->toDateTimeString(),
					'modified_by' => $operator->employee_name,
					'owner' => $operator->employee_name,
					'docstatus' => 0,
					'parent' => $job_card->name,
					'parentfield' => 'time_logs',
					'parenttype' => 'Job Card',
					'idx' => $max_idx + 1,
					'employee' => $employee_id,
					'from_time' => $now->toDateTimeString(),
					'to_time' => null,
					'time_in_mins' => 0,
					'completed_qty' => 0,
					'operation' => null,
					'employee_name' => $operator->employee_name,
					'mes_timelog_id' => $timelog_id,
				];

				DB::connection('mysql')->table('tabJob Card Time Log')->insert($job_card_time_log);

				$this->update_job_card_status($job_card->name);
			}

	    	$details = [
	    		'production_order' => $request->production_order,
	    		'process_id' => $request->process_id,
	    	];
	    	
			
			if ($production_order && $production_order->status == 'Not Started') {
				DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)->update(['status' => 'In Progress']);
			}

			$update_job_ticket = $this->update_job_ticket($request->job_ticket_id);

			if(!$update_job_ticket){
				DB::connection('mysql')->rollback();
				DB::connection('mysql_mes')->rollback();

				return response()->json(['success' => 0, 'message' => 'An error occured. Please try again.']);
			}
			
	    	$operation = DB::connection('mysql_mes')->table('process')->where('process_id', $request->process_id)->first();

			$activity_logs = [
				'created_at' => $now->toDateTimeString(),
				'created_by' => $operator->employee_name,
				'reference' => $request->production_order,
			];

			$job_ticket_ids = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->production_order)->pluck('job_ticket_id');

			$checker = DB::connection('mysql_mes')->table('time_logs')->whereIn('job_ticket_id', $job_ticket_ids)->count();
			if($checker <= 1){ // check if 
				$activity_logs['message'] = 'Production has started by '.$operator->employee_name.' at '.$now->toDateTimeString().' in '.$operation->process_name.'.';
				$activity_logs['action'] = 'Started Production Order';

				DB::connection('mysql_mes')->table('activity_logs')->insert($activity_logs); // insert started production order log in activity logs
			}

			unset($activity_logs['message']);
			unset($activity_logs['action']);

			$activity_logs['message'] = $operation->process_name.' process has been started for '.$request->production_order. ' by '.$operator->employee_name;
			$activity_logs['action'] = 'Started Process';

			DB::connection('mysql_mes')->table('activity_logs')->insert($activity_logs); // insert started process log in activity logs

			DB::connection('mysql')->commit();
			DB::connection('mysql_mes')->commit();

	    	return response()->json(['success' => 1, 'message' => 'Task Updated.', 'details' => $details]);
		} catch (Exception $e) {
			DB::connection('mysql')->rollback();
			DB::connection('mysql_mes')->rollback();

            return response()->json(["error" => $e->getMessage()]);
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

	public function get_workstation_process_machine(Request $request, $workstation, $process_id){
		$machines = DB::connection('mysql_mes')->table('process_assignment')
			->join('machine', 'machine.machine_id', 'process_assignment.machine_id')
			->where('process_assignment.workstation_id', $workstation)
			->where('process_assignment.process_id', $process_id)->select('machine.*', 'process_assignment.process_id')->get();
		
		return view('tbl_workstation_process_machines', compact('machines'));
	}

	public function get_production_workstation_process($production_order, $workstation, $required_qty){
		$processes = DB::connection('mysql_mes')->table('job_ticket AS jt')
			->where('production_order', $production_order)
			->when($workstation != null, function ($a) use ($workstation) {
				$a->where('workstation', $workstation);
			})
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
		$machine_details = DB::connection('mysql_mes')->table('machine')->where('machine_code', $request->machine_code)->first();
		if($machine_details && $machine_details->operation_id < 3){
			$in_progress_operator_machine = DB::connection('mysql_mes')->table('time_logs')
				->whereNotNull('operator_id')->where('operator_id', '!=', $request->operator_id)
				->where('machine_code', $machine_details->machine_code)->where('status', 'In Progress')->exists();

			if($request->process_id != '102') {
				if ($in_progress_operator_machine) {
					return response()->json(['success' => 0, 'message' => "Machine is in use by another operator."]);
				}
			}			
		}

		$operator_in_progress_task = DB::connection('mysql_mes')->table('job_ticket')
			->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
			->where('time_logs.operator_id', $request->operator_id)
			->where('time_logs.status', 'In Progress')->first();

		$operator_in_progress_spotwelding = DB::connection('mysql_mes')->table('job_ticket')
			->join('spotwelding_qty', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
			->where('spotwelding_qty.operator_id', $request->operator_id)
			->where('spotwelding_qty.status', 'In Progress')->first();

			if ($operator_in_progress_task && $machine_details->operation_id < 3) {
			if ($operator_in_progress_task->production_order != $request->production_order) {
				return response()->json(['success' => 0, 'message' => "Operator has in-progress production order in process '" . $operator_in_progress_task->workstation . "'"]);
			}

			if ($operator_in_progress_task->machine_code != $request->machine_code) {
				return response()->json(['success' => 0, 'message' => "Operator has in-progress production order in machine <b>" . $operator_in_progress_task->machine_code . "</b><br>Production Order: <b>" . $operator_in_progress_task->production_order . "</b>"]);
			}
		}

		if ($operator_in_progress_spotwelding) {
			if ($operator_in_progress_spotwelding->production_order != $request->production_order) {
				return response()->json(['success' => 0, 'message' => "Operator has in-progress production order in process '" . $operator_in_progress_spotwelding->workstation . "'"]);
			}

			if ($operator_in_progress_spotwelding->machine_code != $request->machine_code) {
				return response()->json(['success' => 0, 'message' => "Operator has in-progress production order in machine <b>" . $operator_in_progress_spotwelding->machine_code . "</b><br>Production Order: <b>" . $operator_in_progress_spotwelding->production_order . "</b>"]);
			}
		}

		$operator_existing_ongoing_backlog_task = DB::connection('mysql_mes')->table('job_ticket')
			->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
			->where('time_logs.operator_id', $request->operator_id)->whereDate('time_logs.from_time', '<', Carbon::now()->startOfDay()->format('Y-m-d'))
			->where('time_logs.status', 'In Progress')->first();

		if ($operator_existing_ongoing_backlog_task) {
			return response()->json(['success' => 0, 'message' => "Operator has on-going task from previous date. " . $operator_existing_ongoing_backlog_task->production_order]);
		}

		$operator_existing_ongoing_backlog_task_spotwelding = DB::connection('mysql_mes')->table('job_ticket')
			->join('spotwelding_qty', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
			->where('spotwelding_qty.operator_id', $request->operator_id)->whereDate('spotwelding_qty.from_time', '<', Carbon::now()->startOfDay()->format('Y-m-d'))
			->where('spotwelding_qty.status', 'In Progress')->first();

		if ($operator_existing_ongoing_backlog_task_spotwelding) {
			return response()->json(['success' => 0, 'message' => "Operator has on-going task from previous date. " . $operator_existing_ongoing_backlog_task_spotwelding->production_order]);
		}

		$details = [
			'machine_code' => $request->machine_code,
			'operator_id' => $request->operator_id,
			'production_order' => $request->production_order,
		];

		if ($request->process_id) {
			$job_ticket = DB::connection('mysql_mes')->table('job_ticket')
				->where('production_order', $request->production_order)
				->where('process_id', $request->process_id)->first();

			if(!$job_ticket){
				return response()->json(['success' => 0, 'message' => 'An error occured. Please try again.']);
			}
	
			$details['job_ticket_id'] = $job_ticket->job_ticket_id;
			$details['workstation'] = $job_ticket->workstation;
		}

		// attempt to do the login
		$user = DB::connection('mysql_essex')->table('users')->where('user_id', $request->operator_id)->first();

        if ($user) {
            if(Auth::loginUsingId($user->id)){
				DB::connection('mysql_mes')->table('user')->where('user_access_id', $user->user_id)->update(['last_login' => Carbon::now()->toDateTimeString()]);

                return response()->json(['success' => 1, 'message' => "<b>Welcome!</b> Please wait...", 'details' => $details]);
            } 
        } else {        
            // validation not successful, send back to form 
            return response()->json(['success' => 0, 'message' => '<b>Invalid credentials!</b> Please try again.']);
        }
	}

	// /get_current_operator_task_details/{operator_id}
	public function get_current_operator_task_details(Request $request, $operator_id){
		if(!Auth::check()){
			return response()->json(['success' => 0, 'message' => 'Session Expired. Please reload the page and login to continue.']);
		}

		$job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')
			->where('job_ticket_id', $request->job_ticket_id)->first();

		if(!$job_ticket_details){
			return response()->json(['success' => 0, 'message' => 'Task not found.']);
		}

		$status = $job_ticket_details->status;
		$machine_code = $request->machine_code;

		$time_logs = DB::connection('mysql_mes')->table('time_logs')->where('job_ticket_id', $job_ticket_details->job_ticket_id)->where('operator_id', Auth::user()->user_id)->first();

		$exploded_production_order = explode('-', $request->production_order);

		if ($exploded_production_order[0] == 'SC') {
			return $this->operator_scrap_task($request->workstation, $request->machine_code, $request->production_order, $request->job_ticket_id, $operator_id);
		}

		$total_rework = 0;
		if (!$time_logs) {
			$task_list_qry = DB::connection('mysql_mes')->table('production_order AS po')
				->join('job_ticket AS jt', 'po.production_order', 'jt.production_order')
				->where('po.production_order', $request->production_order)->where('jt.workstation', $request->workstation)
				->where('jt.job_ticket_id', $request->job_ticket_id)->select('po.item_code', 'jt.job_ticket_id', DB::raw('(SELECT process_name FROM process WHERE process_id = jt.process_id) AS process_name'), 'po.production_order', 'po.description', 'po.sales_order', 'po.material_request', 'po.customer', 'po.qty_to_manufacture', 'po.stock_uom', 'po.project', 'jt.process_id', 'jt.completed_qty', 'jt.status', 'jt.rework', 'jt.reject as total_reject')
				->orderBy('jt.last_modified_at', 'desc')->get();
		}else{
			$task_list_qry = DB::connection('mysql_mes')->table('production_order AS po')
				->join('job_ticket AS jt', 'po.production_order', 'jt.production_order')
				->join('time_logs', 'time_logs.job_ticket_id', 'jt.job_ticket_id')
				->where('po.production_order', $request->production_order)->where('jt.workstation', $request->workstation)
				->where('jt.job_ticket_id', $request->job_ticket_id)->where('time_logs.operator_id', Auth::user()->user_id)
				->select('po.item_code', 'time_logs.time_log_id', 'jt.job_ticket_id', 'time_logs.operator_id', 'time_logs.machine_code', DB::raw('(SELECT process_name FROM process WHERE process_id = jt.process_id) AS process_name'), 'po.production_order', 'po.description', 'po.sales_order', 'po.material_request', 'time_logs.status', 'time_logs.from_time', 'time_logs.to_time', 'po.customer', 'po.qty_to_manufacture', DB::raw('(SELECT SUM(good) FROM time_logs WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS total_good'),  DB::raw('(SELECT SUM(reject) FROM time_logs WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS total_reject'), 'po.stock_uom', 'po.project', 'time_logs.operator_name', 'jt.process_id', 'time_logs.good', 'jt.status as jtstatus', 'jt.rework')
				->orderByRaw("FIELD(time_logs.status, 'In Progress', 'Pending', 'Completed') ASC")
				->orderBy('time_logs.last_modified_at', 'desc')->get();
		}

		$timelog_ids = DB::connection('mysql_mes')->table('time_logs')->where('job_ticket_id', $job_ticket_details->job_ticket_id)->pluck('time_log_id');
		// $total_reject = DB::connection('mysql_mes')->table('quality_inspection')->where('reference_type', 'Time Logs')->whereIn('reference_id', $timelog_ids)->whereIn('status', ['QC Failed', 'For Confirmation'])->sum('rejected_qty');

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
				
				// $count_helpers = DB::connection('mysql_mes')->table('helper')->whereIn('time_log_id', $qry)->distinct('operator_id')->count();
				$count_helpers = DB::connection('mysql_mes')->table('helper')->whereIn('time_log_id', $qry)->distinct()->count('operator_id');

				$jt_status = $row->status; // time log status
				if ($row->operator_id != Auth::user()->user_id && $row->jtstatus != 'Completed') {
					$jt_status = 'Pending';
				}
			}else{
				$qa_inspection_status = 'Pending';
				$helpers = [];
				$count_helpers = 0;

				$jt_status = $row->status; // job ticket status
				if($jt_status != 'Completed'){
					$jt_status = 'Pending';
				}
			}

			$rework_qty = $total_rework;
			if($time_logs && $row->total_good > 0){
				$rework_qty = $total_rework - $row->total_good;
				$rework_qty = $rework_qty > 0 ? $rework_qty : 0;
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
				'status' => $jt_status,
				'from_time' => ($time_logs) ? $row->from_time : null,
				'to_time' => ($time_logs) ? $row->to_time : null,
				'customer' => $row->customer,
				'qty_to_manufacture' => $row->qty_to_manufacture,
				'total_good' => ($time_logs) ? $row->total_good : $row->completed_qty,
				'total_reject' => $row->total_reject ? $row->total_reject : 0, //$total_reject,
				'total_rework' => $row->rework,
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
			->where('job_ticket.production_order', $request->production_order)
			->where('job_ticket.workstation', $request->workstation)
			->where('job_ticket.process_id', $job_ticket_details->process_id)
			->where('time_logs.operator_id', '!=', $operator_id)
			->whereNotNull('time_logs.operator_id')
			->select('time_logs.operator_id', 'time_logs.operator_nickname', DB::raw('SUM(time_logs.good + time_logs.reject) as completed_qty'))
			->groupBy('time_logs.operator_id', 'time_logs.operator_nickname')->get();
		
    	return view('tables.tbl_current_operator_task', compact('task_list', 'machine_code', 'batch_list', 'in_progress_operator', 'total_rework'));
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
				
				$count_helpers = DB::connection('mysql_mes')->table('helper')->whereIn('time_log_id', $qry)->distinct()->count('operator_id');
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
			->where('job_ticket.production_order', $production_order)
			->where('job_ticket.workstation', $workstation)
			->where('job_ticket.process_id', $job_ticket_details->process_id)
			->where('time_logs.operator_id', '!=', $operator_id)
			->whereNotNull('time_logs.operator_id')
			->select('time_logs.operator_id', 'time_logs.operator_nickname', DB::raw('SUM(time_logs.good + time_logs.reject) as completed_qty'))->groupBy('time_logs.operator_id', 'time_logs.operator_nickname')->get();

    	return view('tables.tbl_current_operator_task', compact('task_list', 'machine_code', 'batch_list', 'in_progress_operator'));
	}

	public function get_target_warehouse($operation_id){
		return DB::connection('mysql')->table('tabWarehouse')->where('disabled', 0)
            ->where('department', 'Fabrication')->where('is_group', 0)
            ->where('company', 'FUMACO Inc.')->pluck('name');
    }
	
	public function reject_task(Request $request){
		DB::connection('mysql')->beginTransaction();
		DB::connection('mysql_mes')->beginTransaction();
		try {
			if(!Auth::user()) {
				return response()->json(['success' => 0, 'message' => 'Session Expired. Please login to continue.']);
			}

			if(empty($request->reject_list)){
				return response()->json(['success' => 0, 'message' => 'Alert: Please select reject type']);

			}

			$data= $request->all();
			$reject_reason= $data['reject_list'];

			$now = Carbon::now();
			$time_log = DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $request->id)->first();
			$good_qty_after_transaction = $time_log->good - $request->rejected_qty;

			$job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $time_log->job_ticket_id)->first();

			$total_rejects = 0;
			$production_order = null;
			if ($job_ticket_details) {
				$production_order = $job_ticket_details->production_order;
				$process = DB::connection('mysql_mes')->table('process')->where('process_id', $job_ticket_details->process_id)->first();
				$process_name = $process ? $process->process_name : null;
				$total_rejects = $job_ticket_details->reject;
			}

			if ($time_log) {
				$is_feedbacked = DB::connection('mysql_mes')->table('production_order as p')
					->join('job_ticket as j', 'p.production_order', 'j.production_order')
					->where('j.job_ticket_id', $time_log->job_ticket_id)
					->whereRaw('p.feedback_qty = p.qty_to_manufacture')
					->exists();

				if ($is_feedbacked) {
					return response()->json(['success' => 0, 'message' => 'Production Order Feedbacked must be cancelled first to register item reject(s).']);
				}
			}
			
            $update = [
                'last_modified_at' => $now->toDateTimeString(),
                'last_modified_by' => Auth::user()->employee_name,
                'good' => $good_qty_after_transaction,
                'reject' => $request->rejected_qty + $total_rejects,
			];

			$reference_type = ($request->workstation == 'Spotwelding') ? 'Spotwelding' : 'Time Logs';
			$reference_id = ($request->workstation == 'Spotwelding') ? $time_log->job_ticket_id : $request->id;

			$reject_category_ids = DB::connection('mysql_mes')->table('reject_list')->whereIn('reject_list_id', $request->reject_list)->select('reject_list_id', 'reject_category_id')->get();
			$reject_category_id = collect($reject_category_ids)->groupBy('reject_list_id');

			$reason = [];
			foreach($request->reject_list as $reject_list_id){
				$category_id = isset($reject_category_id[$reject_list_id][0]->reject_category_id) ? $reject_category_id[$reject_list_id][0]->reject_category_id : 0;

				$insert = [
					'reference_type' => $reference_type,
					'reference_id' => $reference_id,
					'reject_category_id' => $category_id,
					'qa_inspection_type' => 'Reject Confirmation',
					'rejected_qty' => $request->rejected_qty,
					'total_qty' => $time_log->good,
					'status' => 'For Confirmation',
					'created_by' => Auth::user()->employee_name,
					'created_at' => $now->toDateTimeString(),
				];

				$qa_id = DB::connection('mysql_mes')->table('quality_inspection')->insertGetId($insert);

				$reason[] = [
					'job_ticket_id' => $time_log->job_ticket_id,
					'qa_id' => $qa_id,
					'reject_list_id' => $reject_list_id,
					'reject_value' => '-'
				];
			}

			DB::connection('mysql_mes')->table('reject_reason')->insert($reason);
			if($request->workstation != 'Spotwelding'){
				DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $request->id)->update($update);
			}

			$update_job_ticket = $this->update_job_ticket($time_log->job_ticket_id);

			if(!$update_job_ticket){
				DB::connection('mysql')->rollback();
				DB::connection('mysql_mes')->rollback();

				return response()->json(['success' => 0, 'message' => 'An error occured. Please try again.']);
			}

			$activity_logs = [
				'action' => 'Reject Entry',
				'message' => 'Reject quantity of ' . $request->rejected_qty . ' for '.$request->workstation.' - ' . $process_name . ' has been submitted by ' . Auth::user()->employee_name,
				'reference' => $production_order,
				'created_at' => $now->toDateTimeString(),
				'created_by' => Auth::user()->employee_name
			];

			DB::connection('mysql_mes')->table('activity_logs')->insert($activity_logs);

			DB::connection('mysql')->commit();
			DB::connection('mysql_mes')->commit();
            return response()->json(['success' => 1, 'message' => 'Task has been updated.']);
        } catch (Exception $e) {
			DB::connection('mysql')->rollback();
			DB::connection('mysql_mes')->rollback();
            return response()->json(["error" => $e->getMessage()]);
        }
	}

	public function random_inspect_task($job_ticket_id){
		$job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')
			->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
			->where('time_log_id', $job_ticket_id)->where('time_logs.status', '!=', 'Pending')
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

		if(in_array($existing_production_order->status, ['Cancelled', 'Closed'])){
			$err = $existing_production_order->status == 'Cancelled' ? 'Cancelled' : 'Closed';
			return response()->json(['success' => 0, 'message' => 'Production Order <b>' . $production_order . '</b> was <b>'.$err.'</b>.']);
		}

		$reject_category_per_workstation = DB::connection('mysql_mes')->table('qa_checklist as qc')
			->join('workstation as w','w.workstation_id', 'qc.workstation_id')
			->join('reject_list as rl','rl.reject_list_id', 'qc.reject_list_id')
			->join('reject_category as rc','rl.reject_category_id', 'rc.reject_category_id')
			->join('operation as op', 'op.operation_id', 'w.operation_id')
			->where('w.workstation_name', $workstation)
			->select('rc.reject_category_id','rc.reject_category_name')->groupBy('rc.reject_category_id','rc.reject_category_name')//->distinct('rc.reject_category_id')
			->orderBy('rc.reject_category_id', 'asc')->get();

		$operation = DB::connection('mysql_mes')->table('operation')->where('operation_id', $existing_production_order->operation_id)->pluck('operation_name')->first();

		if($workstation != 'Spotwelding'){
			$task_random_inspection = DB::connection('mysql_mes')->table('job_ticket')
				->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
				->where('production_order', $production_order)
				->where('workstation', $workstation)
				->when($request->timelogid, function ($a) use ($request) {
					$a->where('time_logs.time_log_id', $request->timelogid);
				})
				->whereIn('time_logs.status', ['In Progress', 'Completed'])
				->select(DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process'), 'time_logs.from_time', 'time_logs.to_time', DB::raw('time_logs.good + time_logs.reject AS completed_qty'), 'time_logs.good as tl_good', 'operator_name', 'time_logs.status', 'time_logs.time_log_id', 'time_logs.reject','job_ticket.workstation', 'job_ticket.process_id', 'time_logs.machine_name')
				->orderBy('idx', 'asc')->get();

			$qa_logs = DB::connection('mysql_mes')->table('quality_inspection as q')
				->join('time_logs as t', 'q.reference_id', 't.time_log_id')
				->join('job_ticket as j', 'j.job_ticket_id', 't.job_ticket_id')
				->when($request->timelogid, function ($a) use ($request) {
					$a->where('t.time_log_id', $request->timelogid);
				})
				->where('j.production_order', $production_order)->where('j.workstation', $workstation)
				->where('q.reference_type', 'Time Logs')->where('q.status', '!=', 'For Confirmation')
				->select(DB::raw('(SELECT process_name FROM process WHERE process_id = j.process_id) AS process'), 'q.qa_inspection_type', 'q.actual_qty_checked', 'q.status', 'q.qa_staff_id', 'q.qa_inspection_date', 'q.reference_id', 'q.reject_category_id', 'q.rejected_qty', 'q.qa_id')
				->orderBy('q.qa_inspection_date', 'desc')->get();

			$qa_staff_names = collect($qa_logs)->pluck('qa_staff_id')->unique();

			$qa_staff_names = DB::connection('mysql_essex')->table('users')
				->whereIn('user_id', $qa_staff_names)->pluck('employee_name', 'user_id')->toArray();

			$qa_inspection_logs = [];
			foreach ($qa_logs as $r) {
				$qa_inspection_logs[] = [
					'process' => $r->process,
					'qa_inspection_type' => $r->qa_inspection_type,
					'actual_qty_checked' => $r->actual_qty_checked,
					'reject_qty' => $r->rejected_qty,
					'status' => $r->status,
					'qa_staff' => array_key_exists($r->qa_staff_id, $qa_staff_names) ? $qa_staff_names[$r->qa_staff_id] : null,
					'qa_inspection_date' => Carbon::parse($r->qa_inspection_date)->format('M. d, Y h:i A'),
					'reject_category_id' => $r->reject_category_id
				];
			}

			$qa_inspected_qty_per_timelog = collect($qa_logs)->groupBy('reference_id');
			$qa_qty_per_timelog = [];
			foreach ($qa_inspected_qty_per_timelog as $reference_id => $values) {
				$qa_per_category = collect($values)->groupBy('reject_category_id');
				$qa_per_category_arr = [];
				foreach ($qa_per_category as $cat_id => $rows) {
					$qa_per_category_arr[$cat_id] = [
						'actual_qty_checked' => collect($rows)->sum('actual_qty_checked'),
						'rejected_qty' => collect($rows)->sum('rejected_qty'),
					];
				}
				$qa_qty_per_timelog[$reference_id] = $qa_per_category_arr;
			}
		}else{
			$task_random_inspection = DB::connection('mysql_mes')->table('job_ticket')
				->join('spotwelding_qty', 'spotwelding_qty.job_ticket_id', 'job_ticket.job_ticket_id')
				->where('production_order', $production_order)
				->where('workstation', $workstation)
				->when($request->timelogid, function ($a) use ($request) {
					$a->where('spotwelding_qty.time_log_id', $request->timelogid);
				})
				->whereIn('spotwelding_qty.status', ['In Progress', 'Completed'])
				->select(DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process'), 'spotwelding_qty.from_time', 'spotwelding_qty.to_time', DB::raw('spotwelding_qty.good + spotwelding_qty.reject AS completed_qty'), 'spotwelding_qty.good as tl_good', 'operator_name', 'spotwelding_qty.status', 'spotwelding_qty.time_log_id', 'spotwelding_qty.reject','job_ticket.workstation', 'job_ticket.process_id', 'spotwelding_qty.machine_name')
				->orderBy('idx', 'asc')->get();

			$qa_logs = DB::connection('mysql_mes')->table('quality_inspection as q')
				->join('spotwelding_qty as t', 'q.reference_id', 't.time_log_id')
				->join('job_ticket as j', 'j.job_ticket_id', 't.job_ticket_id')
				->when($request->timelogid, function ($a) use ($request) {
					$a->where('t.time_log_id', $request->timelogid);
				})
				->where('j.production_order', $production_order)->where('j.workstation', $workstation)
				->where('q.reference_type', 'Spotwelding')->where('q.status', '!=', 'For Confirmation')
				->select(DB::raw('(SELECT process_name FROM process WHERE process_id = j.process_id) AS process'), 'q.qa_inspection_type', 'q.actual_qty_checked', 'q.status', 'q.qa_staff_id', 'q.qa_inspection_date', 'q.reference_id', 'q.reject_category_id', 'q.rejected_qty', 'q.qa_id')
				->orderBy('q.qa_inspection_date', 'desc')->get();

			$qa_staff_names = collect($qa_logs)->pluck('qa_staff_id')->unique();

			$qa_staff_names = DB::connection('mysql_essex')->table('users')
				->whereIn('user_id', $qa_staff_names)->pluck('employee_name', 'user_id')->toArray();

			$qa_inspection_logs = [];
			foreach ($qa_logs as $r) {
				$qa_inspection_logs[] = [
					'process' => $r->process,
					'qa_inspection_type' => $r->qa_inspection_type,
					'actual_qty_checked' => $r->actual_qty_checked,
					'reject_qty' => $r->rejected_qty,
					'status' => $r->status,
					'qa_staff' => array_key_exists($r->qa_staff_id, $qa_staff_names) ? $qa_staff_names[$r->qa_staff_id] : null,
					'qa_inspection_date' => Carbon::parse($r->qa_inspection_date)->format('M. d, Y h:i A'),
					'reject_category_id' => $r->reject_category_id
				];
			}

			$qa_inspected_qty_per_timelog = collect($qa_logs)->groupBy('reference_id');
			$qa_qty_per_timelog = [];
			foreach ($qa_inspected_qty_per_timelog as $reference_id => $values) {
				$qa_per_category = collect($values)->groupBy('reject_category_id');
				$qa_per_category_arr = [];
				foreach ($qa_per_category as $cat_id => $rows) {
					$qa_per_category_arr[$cat_id] = [
						'actual_qty_checked' => collect($rows)->sum('actual_qty_checked'),
						'rejected_qty' => collect($rows)->sum('rejected_qty'),
					];
				}
				$qa_qty_per_timelog[$reference_id] = $qa_per_category_arr;
			}
		}
		
		// inspected logs from version 9 and below
		$inspected_logs_without_cat_id = collect($qa_logs)->where('reject_category_id', '');
		$inspected_logs_without_cat_id = collect($inspected_logs_without_cat_id)->groupBy('reference_id');

		$task_random_inspection_arr = [];
		foreach ($task_random_inspection as $r) {
			$batch_date = $r->process == 'Unloading' ? $r->to_time : $r->from_time;
			$batch_date = Carbon::parse($batch_date)->format('M-d-Y h:i A');

			$rejected_qty_without_cat_id = $inspected_qty_without_cat_id = 0;
			if(isset($inspected_logs_without_cat_id[$r->time_log_id])){
				$inspected_qty_without_cat_id = collect($inspected_logs_without_cat_id[$r->time_log_id])->sum('actual_qty_checked');
				$rejected_qty_without_cat_id = collect($inspected_logs_without_cat_id[$r->time_log_id])->sum('rejected_qty');
			}

			foreach ($reject_category_per_workstation as $rc) {
				$qtys = array_key_exists($r->time_log_id, $qa_qty_per_timelog) ? $qa_qty_per_timelog[$r->time_log_id] : [];
				$inspected_qty = array_key_exists($rc->reject_category_id, $qtys) ? $qtys[$rc->reject_category_id]['actual_qty_checked'] : 0;
				$rejected_qty = array_key_exists($rc->reject_category_id, $qtys) ? $qtys[$rc->reject_category_id]['rejected_qty'] : 0;
				$task_random_inspection_arr[$rc->reject_category_id][] = [
					'batch_date' => $batch_date,
					'process' => $r->process,
					'operator_name' => $r->operator_name,
					'completed_qty' => $r->completed_qty,
					'machine' => $r->machine_name,
					'good' => $r->tl_good,
					'inspected_qty' => $inspected_qty + $inspected_qty_without_cat_id,
					'rejected_qty' => $rejected_qty + $rejected_qty_without_cat_id,
					'status' => $r->status,
					'time_log_id' => $r->time_log_id,
					'workstation' => $r->workstation,
					'process_id' => $r->process_id,
					// 'time_log_id' => $r->time_log_id,
				];
			}
		}

		$inspected_items_per_timelogid = $item_descriptions = [];
		if ($workstation == 'Spotwelding') {
			$inspected_items = DB::connection('mysql_mes')->table('inspected_component as ic')
				->join('quality_inspection as qi', 'ic.qa_id', 'qi.qa_id')
				->whereIn('qi.qa_id', collect($qa_logs)->pluck('qa_id'))->get();

			$item_descriptions = DB::connection('mysql')->table('tabItem')
				->whereIn('name', collect($inspected_items)->pluck('item_code'))
				->pluck('description', 'name')->toArray();

			$inspected_items_per_timelogid = collect($inspected_items)->groupBy('reject_category_id')->map(function ($row) {
				return collect($row)->groupBy('reference_id');
			})->toArray();
		}

		$task_random_inspection_arr = collect($task_random_inspection_arr)->sortBy('inspected_qty')->toArray();

		return view('tables.tbl_production_process_inspection', compact('task_random_inspection_arr', 'existing_production_order', 'qa_inspection_logs', 'reject_category_per_workstation', 'workstation', 'operation', 'inspected_items_per_timelogid', 'item_descriptions'));
	}

	public function maintenance_request(Request $request){
		$permissions = $this->get_user_permitted_operation();
		$list = DB::connection('mysql_mes')->table('machine_breakdown')->join('machine', 'machine.machine_code', 'machine_breakdown.machine_id')->get();

		$fabrication = collect($list)->where('operation_id', 1)->count();
		$painting = collect($list)->where('operation_id', 2)->count();
		$wiring = collect($list)->where('operation_id', 3)->count();

		$total = DB::connection('mysql_mes')->table(DB::raw('(SELECT machine_id, COUNT(*) as total_count FROM machine_breakdown GROUP BY machine_id) AS subquery'))
			->select('machine_id', 'total_count')
			->orderBy('total_count', 'desc')
			->get();

		$total_breakdowns = collect($total)->groupBy('machine_id');

		$machines_for_maintenance = DB::connection('mysql_mes')->table(DB::raw('(SELECT machine_id, COUNT(*) as breakdown_count FROM machine_breakdown where status not in ("Done", "") GROUP BY machine_id) AS subquery'))
			->select('machine_id', 'breakdown_count')
			->orderBy('breakdown_count', 'desc')
			->get();
		
		$machines = DB::connection('mysql_mes')->table('machine')->orderby('machine_code', 'asc')->get();

		$machine_list = collect($machines)->groupBy('machine_code');

		$machine_arr = [];
		foreach($machines_for_maintenance as $mach){
			$machine_arr[] = [
				'machine_id' => $mach->machine_id,
				'machine_name' => isset($machine_list[$mach->machine_id]) ? $machine_list[$mach->machine_id][0]->machine_name : null,
				'pending_breakdowns' => $mach->breakdown_count,
				'image' => isset($machine_list[$mach->machine_id]) ? $machine_list[$mach->machine_id][0]->image : null,
				'total_breakdowns' => isset($total_breakdowns[$mach->machine_id]) ? $total_breakdowns[$mach->machine_id][0]->total_count : null
 			];
		}

		$operators = DB::connection('mysql_essex')->table('users as u')
			->join('departments as d', 'd.department_id', 'u.department_id')
			->where('u.status', 'Active')->where('u.user_type', 'Employee')
			->where(function($q) use ($request) {
				$q->where('d.department', 'LIKE', '%painting%')
					->orWhere('d.department', 'LIKE', '%assembly%')
					->orWhere('d.department', 'LIKE', '%fabrication%')
					->orWhere('d.department', 'LIKE', '%engineering%')
					->orWhere('d.department', 'LIKE', '%production%')
					->orWhere('d.department', 'LIKE', '%Plant Services%');
			})
			->select('u.user_id as operator_id', 'u.employee_name', 'd.department')
			->orderBy('u.employee_name', 'asc')->get();

		return view('maintenance_request_page', compact('fabrication', 'painting', 'wiring', 'machine_arr', 'permissions', 'machines', 'operators'));
	}

	public function maintenance_request_list(Request $request){
		$search_string = $request->search_string ? $request->search_string : null;
		$status = [];

		if($request->status != 'All'){
			$status = array_filter(explode(',', $request->status));
		}

		switch ($request->operation) {
			case 2:
				$operation = 'painting';
				break;
			case 3:
				$operation = 'wiring';
				break;
			default:
				$operation = 'fabrication';
				break;
		}

		$list = DB::connection('mysql_mes')->table('machine_breakdown')
			->join('machine', 'machine.machine_code', 'machine_breakdown.machine_id')
			->join('operation', 'operation.operation_id', 'machine.operation_id')
			->when($search_string, function ($q) use ($search_string){
				$q->where(function ($query) use ($search_string){
					return $query->where('machine.machine_name', 'LIKE', '%'.$search_string.'%')
						->orWhere('machine_breakdown.machine_id', 'LIKE', '%'.$search_string.'%')
						->orWhere('machine_breakdown.category', 'LIKE', '%'.$search_string.'%')
						->orWhere('machine_breakdown.reported_by', 'LIKE', '%'.$search_string.'%')
						->orWhere('machine_breakdown.machine_breakdown_id', 'LIKE', '%'.$search_string.'%');
				});
			})
			->when($request->status != 'All', function ($q) use ($status){
				$q->whereIn('machine_breakdown.status', $status)
					->when(!in_array('Done', $status), function ($a){
						$a->where('machine_breakdown.status', '!=' , '');
					});
			})
			->where('operation.operation_id', $request->operation)
			->select('machine_breakdown.*', 'machine.image', 'machine.machine_name', 'machine.machine_code', 'operation.operation_name', 'operation.operation_id')
			->orderByRaw("FIELD(machine_breakdown.status, 'In Process', 'Pending', 'On Hold', 'Done', 'Cancelled') asc")
			->orderBy('created_at', 'desc')
			->paginate(15);

		$breakdown_timelogs = DB::connection('mysql_mes')->table('machine_breakdown_timelogs')->whereIn('machine_breakdown_id', collect($list->items())->pluck('machine_breakdown_id'))->get();
		$tl_array = [];
		foreach($breakdown_timelogs as $tl){
			$tl_array[$tl->machine_breakdown_id][] = $tl->status;
		}

		$assigned_staffs = DB::connection('mysql_mes')->table('machine_breakdown_personnel')->whereIn('machine_breakdown_id', collect($list->items())->pluck('machine_breakdown_id'))->get();
		$assigned_staffs = collect($assigned_staffs)->groupBy('machine_breakdown_id');

		$permissions = $this->get_user_permitted_operation();

		$operators = DB::connection('mysql_essex')->table('users as u')
			->join('departments as d', 'd.department_id', 'u.department_id')
			->where('u.status', 'Active')->where('u.user_type', 'Employee')
			->where(function($q) use ($request) {
				$q->where('d.department', 'LIKE', '%painting%')
					->orWhere('d.department', 'LIKE', '%assembly%')
					->orWhere('d.department', 'LIKE', '%fabrication%')
					->orWhere('d.department', 'LIKE', '%engineering%')
					->orWhere('d.department', 'LIKE', '%production%')
					->orWhere('d.department', 'LIKE', '%Plant Services%');
			})
			->select('u.user_id as operator_id', 'u.employee_name', 'd.department')
			->orderBy('u.employee_name', 'asc')->get();

		return view('maintenance_request_tbl', compact('list', 'permissions', 'operation', 'operators', 'assigned_staffs', 'tl_array'));
	}
	
	public function update_maintenance_request($machine_breakdown_id, Request $request){
		DB::connection('mysql_mes')->beginTransaction();
        try {
			$breakdown_details = DB::connection('mysql_mes')->table('machine_breakdown')->where('machine_breakdown_id', $machine_breakdown_id)->first();
			
			if(!$breakdown_details){
				return redirect()->back()->with('error', 'Maintenance Request ID not found.');
			}

			$update = [
				'status' => $request->status_update,
				'complaints' => $request->complaints,
				'findings' => $request->findings,
				'work_started' => Carbon::parse($request->date_started)->toDateTimeString(),
				'last_modified_by' => Auth::user()->email,
				'last_modified_at' => Carbon::now()->toDateTimeString()
			];

			switch ($request->status_update) {
				case 'Done':
					$update['work_done'] = $request->work_done;
					$update['date_resolved'] = Carbon::parse($request->date_resolved)->toDateTimeString();
					break;
				case 'On Hold':
					$update['hold_reason'] = $request->hold_reason;
					break;
				default:
					break;
			}

			DB::connection('mysql_mes')->table('machine_breakdown_personnel')->where('machine_breakdown_id', $machine_breakdown_id)->delete();
			
			if($request->maintenance_staff){
				$employee_details = DB::connection('mysql_essex')->table('users')->whereIn('user_id', $request->maintenance_staff)->get();
                $employee_details = collect($employee_details)->groupBy('user_id');

				foreach(array_filter($request->maintenance_staff) as $staff){
                    DB::connection('mysql_mes')->table('machine_breakdown_personnel')->insert([
                        'machine_breakdown_id' => $machine_breakdown_id,
                        'user_id' => $staff,
                        'email' => isset($employee_details[$staff]) ? $employee_details[$staff][0]->email : null,
                        'created_by' => Auth::user()->email,
                        'last_modified_by' => Auth::user()->email
                    ]);
                }
			}

			$breakdown_timelog = DB::connection('mysql_mes')->table('machine_breakdown_timelogs')->where('machine_breakdown_id', $machine_breakdown_id)->where('status', 'In Progress')->first();

			if($request->status_update == 'In Process'){
				if(!$breakdown_timelog){
					DB::connection('mysql_mes')->table('machine_breakdown_timelogs')->insert([
						'machine_breakdown_id' => $machine_breakdown_id,
						'machine_id' => $breakdown_details->machine_id,
						'start_time' => Carbon::now()->toDateTimeString(),
						'operator_id' => Auth::user()->user_id,
						'operator_name' => Auth::user()->employee_name,
						'status' => 'In Progress',
						'created_by' => Auth::user()->email
					]);
				}
			}else{
				// update machine breakdown timelog
				if($breakdown_timelog){
					$start_time = Carbon::parse($breakdown_timelog->start_time);
					$end_time = Carbon::now();
					$duration = Carbon::parse($end_time)->diffInSeconds($start_time) / 3600;

					DB::connection('mysql_mes')->table('machine_breakdown_timelogs')->where('machine_breakdown_id', $machine_breakdown_id)->where('status', 'In Progress')->update([
						'status' => 'Completed',
						'duration_in_hours' => $duration,
						'last_modified_by' => Auth::user()->email,
						'last_modified_at' => Carbon::now()->toDateTimeString()
					]);
				}
			}

			DB::connection('mysql_mes')->table('machine_breakdown')->where('machine_breakdown_id', $machine_breakdown_id)->update($update);
			DB::connection('mysql_mes')->commit();
			return response()->json(['success' => 1, 'message' => $machine_breakdown_id.' Maintenance Request Updated']);
        } catch (Exception $e) {
            DB::connection('mysql_mes')->rollback();
			return response()->json(['success' => 0, 'message' => 'An error occured. Please try again.']);
        }
	}

	public function machineBreakdownImport(Request $request){
		DB::connection('mysql_mes')->beginTransaction();
        try {
            if($request->hasFile('file')){
				$attached_file = $request->file('file');

				$allowed_extensions = ['xlsx', 'csv', 'tsv', 'ods', 'xls', 'slk', 'xml'];

				$file_ext = pathinfo($attached_file->getClientOriginalName(), PATHINFO_EXTENSION);

				if(!in_array($file_ext, $allowed_extensions)){
					return response()->json(['status' => 0, 'message' => 'Sorry, only xlsx, csv, tsv, ods, xls, slk, and xml files are allowed.']);
				}

				try {
					$array = Excel::toArray(new MachineBreakdownImport, $attached_file);
				} catch (\Throwable $th) {
					return response()->json(['status' => 0, 'message' => 'Sorry, cannot read file. Please download and use the template provided.']);
				}

				$latest_id = DB::connection('mysql_mes')->table('machine_breakdown')->where('machine_breakdown_id', 'like', '%mr%')->orderBy('machine_breakdown_id', 'desc')->pluck('machine_breakdown_id')->first();
				$latest_id_exploded = explode("-", $latest_id);
				$new_id = (($latest_id) ? $latest_id_exploded[1] : 0) + 1;

				if (isset($array[0])) {
					foreach ($array[0] as $i => $row) {
						if(max($array[0][$i])){
							$insert[] = [
								'machine_breakdown_id' => 'MR-'.str_pad($new_id++, 5, '0', STR_PAD_LEFT),
								'machine_id' => $row['machine_id'],
								'status' => $row['status'],
								'hold_reason' => $row['hold_reason'],
								'reported_by' => $row['reported_by'],
								'date_reported' => $row['date_reported'] ? Carbon::parse($row['date_reported'])->format('Y-m-d h:i:s') : null,
								'work_started' => $row['work_started'] ? Carbon::parse($row['work_started'])->format('Y-m-d h:i:s') : null,
								'remarks' => $row['remarks'],
								'date_resolved' => $row['date_resolved'] ? Carbon::parse($row['date_resolved'])->format('Y-m-d h:i:s') : null,
								'work_done' => $row['work_done'],
								'findings' => $row['findings'],
								'assigned_maintenance_staff' => $row['assigned_maintenance_staff'],
								'type' => $row['type'],
								'corrective_reason' => $row['corrective_reason'],
								'breakdown_reason' => $row['breakdown_reason'],
								'category' => $row['category'],
								'created_by' => Auth::user()->employee_name,
								'created_at' => Carbon::now()->toDateTimeString(),
								'last_modified_by' => Auth::user()->employee_name,
								'last_modified_at' => Carbon::now()->toDateTimeString()
							];
						}
					}

					if($insert){
						try {
							DB::connection('mysql_mes')->table('machine_breakdown')->insert($insert);
						} catch (\Throwable $th) {
							return response()->json(['status' => 0, 'message' => 'Please fill-out ALL required fields.']);
						}
					}
				}
			}
			DB::connection('mysql_mes')->commit();
			return response()->json(['status' => 1, 'message' => 'Data Imported']);
		} catch (Exception $e) {
            DB::connection('mysql_mes')->rollback();
			return response()->json(['status' => 0, 'message' => 'An error occured. Please try again.']);
        }
	}

	public function attachFile(Request $request){
		DB::connection('mysql_mes')->beginTransaction();
		try {
			if($request->hasFile('file')){
				$machine_breakdown_id = $request->machine_breakdown_id;
				$breakdown_details = DB::connection('mysql_mes')->table('machine_breakdown')->where('machine_breakdown_id', $machine_breakdown_id)->first();

				if(!$breakdown_details){
					return response()->json(['success' => 0, 'message' => 'Machine Breakdown ID not found.']);
				}

				$attached_file = $request->file('file');

				$file_name = pathinfo($attached_file->getClientOriginalName(), PATHINFO_FILENAME);
				$file_ext = pathinfo($attached_file->getClientOriginalName(), PATHINFO_EXTENSION);

				$file_name = Str::slug($file_name, '-');

				$attached_file_name = $file_name.".".$file_ext;

				if(in_array($attached_file_name, explode(',', $breakdown_details->attached_files))){
					return response()->json(['success' => 0, 'message' => 'File already exists.']);
				}

				if(!Storage::disk('public')->exists('/files/'.$request->module.'/'.$machine_breakdown_id)){
					Storage::disk('public')->makeDirectory('/files/'.$request->module.'/'.$machine_breakdown_id);
				}
				
				$attached_file->move(public_path('/storage/files/'.$request->module.'/'.$machine_breakdown_id), $attached_file_name);

				$attached_files = $breakdown_details->attached_files ? $breakdown_details->attached_files.','.$attached_file_name : $attached_file_name;
				DB::connection('mysql_mes')->table('machine_breakdown')->where('machine_breakdown_id', $machine_breakdown_id)->update([
					'attached_files' => $attached_files,
					'last_modified_by' => Auth::user()->email,
					'last_modified_at' => Carbon::now()->toDateTimeString()
				]);

				DB::connection('mysql_mes')->commit();
				return response()->json(['success' => 1, 'message' => 'File Imported.', 'file' => $attached_file_name]);
			}
		} catch (\Throwable $th) {
			DB::connection('mysql_mes')->rollback();
			return response()->json(['success' => 0, 'message' => 'An error occured. Please try again later.']);
		}
	}

	public function removeFile(Request $request){
		DB::connection('mysql_mes')->beginTransaction();
        try {
			$attached_files = DB::connection('mysql_mes')->table('machine_breakdown')->where('machine_breakdown_id', $request->id)->pluck('attached_files')->first();
			$files_arr = $attached_files ? explode(',', $attached_files) : [];

			if(Storage::disk('public')->exists('/files/maintenance/'.$request->id.'/'.$request->file)){
				unlink(public_path('/storage/files/maintenance/'.$request->id.'/'.$request->file));
			}

			DB::connection('mysql_mes')->table('machine_breakdown')->where('machine_breakdown_id', $request->id)->update([
				'attached_files' => collect(array_diff($files_arr, [$request->file]))->implode(','),
				'last_modified_by' => Auth::user()->employee_name,
				'last_modified_at' => Carbon::now()->toDateTimeString()
			]);

			DB::connection('mysql_mes')->commit();
			return response()->json(['success' => 1, 'message' => $request->id.' Maintenance Request Updated.']);
		} catch (Exception $e) {
            DB::connection('mysql_mes')->rollback();
			return response()->json(['success' => 0, 'message' => 'An error occured. Please try again later.']);
        }
	}

	public function stock_entry(){
		$list = DB::connection('mysql')->table('tabStock Entry')->where('work_order', 'LIKE', '%PROM-%')->paginate(10);

		return view('stock_entry_page', compact('list'));
	}

	public function add_helper(Request $request){
		DB::connection('mysql_mes')->beginTransaction();
		try {
			if(!Auth::user()) {
				return response()->json(['success' => 0, 'message' => 'Session Expired. Please login to continue.']);
			}
	
			$now = Carbon::now();
			if (Auth::user()->user_id == $request->helper_id) {
				return response()->json(['success' => 0, 'message' => "Please enter helper ID."]);
			}
	
			if(!$request->time_log_id){
				return response()->json(['success' => 0, 'message' => 'No timelogs found. Please start operation before adding helper(s).']);
			}
	
			$helper_details = DB::connection('mysql_essex')->table('users')->where('user_id', $request->helper_id)->first();
				
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
	
			DB::connection('mysql_mes')->commit();
			return response()->json(['success' => 1, 'message' => 'Helper(s) updated.']);
		} catch (\Throwable $th) {
			DB::connection('mysql_mes')->rollback();
			return response()->json(['success' => 0, 'message' => 'An error occured. Please try again.']);
		}
	}

	public function get_helpers(Request $request){
		if ($request->display_all) {
			$qry = DB::connection('mysql_mes')->table('time_logs')
				->where('job_ticket_id', $request->job_ticket_id)
				->where('machine_code', $request->machine)
				->where('operator_id', $request->operator_id)
				->pluck('time_log_id');
			
			$helpers = DB::connection('mysql_mes')->table('helper')->whereIn('time_log_id', $qry)
				->select('helper_id', 'time_log_id', 'operator_id', 'operator_name')
				->groupBy('helper_id', 'time_log_id', 'operator_id', 'operator_name')
				->get();
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
					// ->where('production_order.status', '!=', 'Cancelled')
					->whereNotIn('production_order.status', ['Cancelled', 'Closed'])
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
		if (Gate::denies('assign-production-order-schedule')) {
            return response()->json(['success' => 0, 'message' => 'Unauthorized.']);
        }

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
		$permissions = $this->get_user_permitted_operation();

		return view('operators_load_utilization', compact('permissions'));
	}

	public function get_operators(Request $request){
		$d1 = Carbon::now()->subDays(7)->startOfDay();
		$d2 = Carbon::now()->addDays(1)->startOfDay();
		if($request->start_date && $request->end_date){
			$d1 = Carbon::parse($request->start_date)->startOfDay();
			$d2 = Carbon::parse($request->end_date)->startOfDay();
		}

		// operator spotwelding
		$query_0 = DB::connection('mysql_mes')->table('spotwelding_qty')
			->join('job_ticket', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
			->join('production_order', 'production_order.production_order', 'job_ticket.production_order')
			->whereNotNull('spotwelding_qty.operator_id')->where('production_order.operation_id', $request->operation)
			->whereBetween('spotwelding_qty.from_time', [$d1, $d2])
			->select('spotwelding_qty.operator_id', 'spotwelding_qty.operator_name', 'spotwelding_qty.time_log_id');

		$query = DB::connection('mysql_mes')->table('time_logs')
			->join('job_ticket', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
			->join('production_order', 'production_order.production_order', 'job_ticket.production_order')
			->whereNotNull('time_logs.operator_id')->where('production_order.operation_id', $request->operation)
			->whereBetween('time_logs.from_time', [$d1, $d2])
			->select('time_logs.operator_id', 'time_logs.operator_name', 'time_logs.time_log_id')
			->union($query_0)->get();

		$operators = [];
		foreach($query as $row){
			$operators[$row->operator_id] = $row->operator_name;
		}

		$time_log_ids = array_unique(array_column($query->toArray(), 'time_log_id'));

		$helpers = DB::connection('mysql_mes')->table('helper')
			->whereIn('time_log_id', $time_log_ids)->whereNotNull('operator_id')
			->distinct()->pluck('operator_name', 'operator_id');
		
		foreach($helpers as $operator_id => $operator_name){
			$operators[$operator_id] = $operator_name;
		}

		return $operators;
	}

	public function get_operator_timelogs(Request $request){
		$d1 = Carbon::now()->subDays(7)->startOfDay();
		$d2 = Carbon::now()->addDays(1)->startOfDay();
		if($request->start_date && $request->end_date){
			$d1 = Carbon::parse($request->start_date)->startOfDay();
			$d2 = Carbon::parse($request->end_date)->startOfDay();
		}
	
		// helper time logs (spotwelding workstation only)
		$query_1 = DB::connection('mysql_mes')->table('spotwelding_qty')
			->join('job_ticket', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
			->join('production_order', 'production_order.production_order', 'job_ticket.production_order')
			->join('helper', 'spotwelding_qty.time_log_id', 'helper.time_log_id')
			->whereNotNull('spotwelding_qty.operator_id')->where('spotwelding_qty.status', 'Completed')
			->whereNotNull('spotwelding_qty.from_time')->whereNotNull('spotwelding_qty.to_time')
			->where('production_order.operation_id', $request->operation)
			->whereBetween('spotwelding_qty.from_time', [$d1, $d2])
			->select('job_ticket.workstation', DB::raw('(spotwelding_qty.good + spotwelding_qty.reject) as completed_qty'), 'spotwelding_qty.from_time', 'spotwelding_qty.to_time', 'helper.operator_id', 'production_order.production_order');
		
		// helper time logs (other workstations)
		$query_2 = DB::connection('mysql_mes')->table('time_logs')
			->join('job_ticket', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
			->join('production_order', 'production_order.production_order', 'job_ticket.production_order')
			->join('helper', 'time_logs.time_log_id', 'helper.time_log_id')
			->whereNotNull('time_logs.operator_id')->where('time_logs.status', 'Completed')
			->whereNotNull('time_logs.from_time')->whereNotNull('time_logs.to_time')
			->where('production_order.operation_id', $request->operation)
			->whereBetween('time_logs.from_time', [$d1, $d2])
			->select('job_ticket.workstation', DB::raw('(time_logs.good + time_logs.reject) as completed_qty'), 'time_logs.from_time', 'time_logs.to_time', 'helper.operator_id', 'production_order.production_order');

		// operator time logs (spotwelding workstation only)
		$query_3 = DB::connection('mysql_mes')->table('spotwelding_qty')
			->join('job_ticket', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
			->join('production_order', 'production_order.production_order', 'job_ticket.production_order')
			->whereNotNull('spotwelding_qty.operator_id')->where('spotwelding_qty.status', 'Completed')
			->whereNotNull('spotwelding_qty.from_time')->whereNotNull('spotwelding_qty.to_time')
			->where('production_order.operation_id', $request->operation)
			->whereBetween('spotwelding_qty.from_time', [$d1, $d2])
			->select('job_ticket.workstation', DB::raw('(spotwelding_qty.good + spotwelding_qty.reject) as completed_qty'), 'spotwelding_qty.from_time', 'spotwelding_qty.to_time', 'spotwelding_qty.operator_id', 'production_order.production_order');
		
		// operator time logs (other workstations)
		return DB::connection('mysql_mes')->table('time_logs')
			->join('job_ticket', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
			->join('production_order', 'production_order.production_order', 'job_ticket.production_order')
			->whereNotNull('time_logs.operator_id')->where('time_logs.status', 'Completed')
			->whereNotNull('time_logs.from_time')->whereNotNull('time_logs.to_time')
			->where('production_order.operation_id', $request->operation)
			->whereBetween('time_logs.from_time', [$d1, $d2])
			->select('job_ticket.workstation', DB::raw('(time_logs.good + time_logs.reject) as completed_qty'), 'time_logs.from_time', 'time_logs.to_time', 'time_logs.operator_id', 'production_order.production_order')
			->union($query_1)->union($query_2)->union($query_3)
			->get();
	}

	// /get_tbl_notif_dashboard
	public function get_tbl_notif_dashboard(){
		$notifications = $this->getNotifications();
		$notifications = collect($notifications)->where('type', '!=', 'Machine Breakdown');
		$process_collect = DB::connection('mysql_mes')->table('process')->select('process_id', 'process_name')->get();

		return view('tables.tbl_notification_dashboard', compact('notifications', 'process_collect'));
	}

	public function get_tbl_warnings_dashboard(){
		$notifications = $this->getNotifications();

		$warnings = collect($notifications)->where('type', 'Machine Breakdown');

		return view('tables.tbl_warnings_dashboard', compact('warnings'));
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
				
				$count_helpers = DB::connection('mysql_mes')->table('helper')->whereIn('time_log_id', $qry)->distinct()->count('operator_id');
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
			->where('job_ticket.production_order', $request->production_order)
			->where('job_ticket.workstation', $request->workstation)
			->where('job_ticket.process_id', $job_ticket_details->process_id)
			->where('time_logs.operator_id', '!=', $operator_id)
			->whereNotNull('time_logs.operator_id')
			->select('time_logs.operator_id', 'time_logs.operator_nickname', DB::raw('SUM(time_logs.good + time_logs.reject) as completed_qty'))->groupBy('time_logs.operator_id', 'time_logs.operator_nickname')->get();

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
		$users = [];
		if (!Gate::denies('manage-users')) {
			$users = DB::connection('mysql_mes')->table('user')
				->join('operation as op','op.operation_id','user.operation_id')
				->join('user_group as ug', 'ug.user_group_id','user.user_group_id')
				->where(function($q) use ($request) {
					$q->where('op.operation_name', 'LIKE', '%'.$request->search_string.'%')
					->orWhere('user.user_access_id', 'LIKE', '%'.$request->search_string.'%')
					->orWhere('user.employee_name', 'LIKE', '%'.$request->search_string.'%')
					->orWhere('ug.user_role', 'LIKE', '%'.$request->search_string.'%')
					->orWhere('ug.module', 'LIKE', '%'.$request->search_string.'%');
				})
				->select('user.*','op.operation_name', "ug.module", 'ug.user_role')
				->orderBy('user.employee_name', 'asc')->get();

			$users = collect($users)->groupBy('employee_name');
		}

    	return view('tables.tbl_users', compact('users'));
    }

    public function save_user(Request $request){
		if (Gate::denies('manage-users')) {
            return response()->json(['message' => 'Unauthorized.']);
        }

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
		if (Gate::denies('manage-users')) {
            return response()->json(['message' => 'Unauthorized.']);
        }

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
		if (Gate::denies('manage-users')) {
            return response()->json(['message' => 'Unauthorized.']);
        }

		DB::connection('mysql_mes')->beginTransaction();
		try{
			DB::connection('mysql_mes')->table('user')->where('user_access_id', $request->user_id)->delete();
			DB::connection('mysql_mes')->commit();
			return response()->json(['message' => 'User has been deleted.']);
		}catch(Exception $e){
			DB::connection('mysql_mes')->rollback();
			return response()->json(['message' => 'An error occured. Please try again.']);
		}
    }

	public function remove_user_access($id){
		if (Gate::denies('manage-users')) {
            return response()->json(['message' => 'Unauthorized.']);
        }
		
		DB::connection('mysql_mes')->beginTransaction();
		try{
			DB::connection('mysql_mes')->table('user')->where('user_id', $id)->delete();
			DB::connection('mysql_mes')->commit();
			return response()->json(['message' => 'User has been deleted.']);
		}catch(Exception $e){
			DB::connection('mysql_mes')->rollback();
			return response()->json(['message' => 'An error occured. Please try again.']);
		}
	}

	// /create_stock_entry/{production_order}
    public function create_stock_entry(Request $request, $production_order){
		DB::connection('mysql')->beginTransaction();
		try {
			if(!Auth::user()) {
				return response()->json(['success' => 0, 'message' => 'Session Expired. Please login to continue.']);
			}

			// check if production order has job ticket
			$jts = DB::connection('mysql_mes')->table('job_ticket')
				->where('production_order', $production_order)->exists();
			if (!$jts) {
				return response()->json(['success' => 0, 'message' => '<center>Cannot create feedback. <br> Production order has no workstation / process.</center>']);
			}

			$existing_ste_transfer = DB::connection('mysql')->table('tabStock Entry')
				->where('work_order', $production_order)
				->where('purpose', 'Material Transfer for Manufacture')
				->where('docstatus', 1)->exists();

			if(!$existing_ste_transfer){
				return response()->json(['success' => 0, 'message' => 'Materials unavailable.']);
			}

			if ($request->fg_completed_qty <= 0) {
				return response()->json(['success' => 0, 'message' => 'Feedback qty cannot be equal to 0.']);
			}

			$production_order_details = DB::connection('mysql')->table('tabWork Order')
				->where('name', $production_order)->first();

			$produced_qty = $production_order_details->produced_qty + $request->fg_completed_qty;
			if($produced_qty >= (int)$production_order_details->qty && $production_order_details->material_transferred_for_manufacturing > 0){
				$pending_mtfm_count = DB::connection('mysql')->table('tabStock Entry as ste')
					->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
					->where('ste.work_order', $production_order)->where('purpose', 'Material Transfer for Manufacture')
					->where('ste.docstatus', 0)->first();
				
				if($pending_mtfm_count){
					return response()->json(['success' => 0, 'message' => '<center>There are pending material request for issue. <br><br> Insufficient stock for ' . $pending_mtfm_count->item_code . ' in ' . $pending_mtfm_count->t_warehouse . '.</center>']);
				}
			}

			$mes_production_order_details = DB::connection('mysql_mes')->table('production_order')
				->where('production_order', $production_order)->first();

			$operation_details = DB::connection('mysql_mes')->table('operation')->where('operation_id', $mes_production_order_details->operation_id)->first();
			if ($operation_details) {
				if (strpos(strtolower($operation_details->operation_name), 'assembly')) {
					$total_feedback_qty = $mes_production_order_details->feedback_qty + $request->fg_completed_qty;
					if ($total_feedback_qty > $mes_production_order_details->qty_to_manufacture) {
						return response()->json(['success' => 0, 'message' => '<center>Feedback Qty should not be greater than <b>' . $mes_production_order_details->qty_to_manufacture . '</b>.']);
					}
				}
			}

			$remarks_override = null;
			if($produced_qty > $mes_production_order_details->produced_qty){
				$remarks_override = 'Override';
			}

			if($mes_production_order_details->is_stock_item < 1){
				return redirect('/create_bundle_feedback/'. $production_order .'/' . $request->fg_completed_qty);
			}

			$now = Carbon::now();

			$latest_pro = DB::connection('mysql')->table('tabStock Entry')->where('name', 'like', '%step%')->max('name');
			$latest_pro_exploded = explode("-", $latest_pro);
			$new_id = (($latest_pro) ? $latest_pro_exploded[1] : 0) + 1;
			$new_id = str_pad($new_id, 6, '0', STR_PAD_LEFT);
			$new_id = 'STEP-'.$new_id;

			$id_checker = DB::connection('mysql')->table('tabStock Entry')->where('name', $new_id)->exists();
            if($id_checker){
                return response()->json(['status' => 0, 'message' => 'Stock Entry <b>'.$new_id.'</b> already exists. Please try again.']);
            }

			$production_order_items = $this->feedback_production_order_items($production_order, $mes_production_order_details->qty_to_manufacture, $request->fg_completed_qty);

			$receiving_warehouse = ['P2 - Housing Temporary - FI1'];
			$docstatus = (in_array($mes_production_order_details->fg_warehouse, $receiving_warehouse)) ? 0 : 1;

			if(count($production_order_items) < 1){
				return response()->json(['success' => 0, 'message' => 'Materials unavailable.']);
			}

			$item_codes = array_column($production_order_items, 'item_code');
			// get raw materials current qty in wip warehouse before submission of stock entry (for double checking of stocks after transaction)
			$raw_materials_current_bin = DB::connection('mysql')->table('tabBin')->whereIn('item_code', $item_codes)
				->where('warehouse', $production_order_details->wip_warehouse)->pluck('actual_qty', 'item_code')->toArray();

			// get finished good current qty in target warehouse before submission of stock entry (for double checking of stocks after transaction)
			$fg_current_bin = DB::connection('mysql')->table('tabBin')->where('item_code', $production_order_details->production_item)
				->where('warehouse', $mes_production_order_details->fg_warehouse)->pluck('actual_qty', 'item_code')->toArray();

			$stock_reservation = DB::connection('mysql')->table('tabStock Reservation')->whereIn('item_code', $item_codes)
				->where('warehouse', $production_order_details->wip_warehouse)->where('status', 'Active')
				->selectRaw('SUM(reserve_qty) as total_reserved_qty, SUM(consumed_qty) as total_consumed_qty, item_code')
				->groupBy('item_code', 'warehouse')->get();

			$stock_reservation = collect($stock_reservation)->groupBy('item_code')->toArray();
	
			$ste_total_issued = DB::table('tabStock Entry Detail')->where('docstatus', 0)->where('status', 'Issued')
				->whereIn('item_code', $item_codes)->where('s_warehouse', $production_order_details->wip_warehouse)
				->selectRaw('SUM(qty) as total_issued, item_code')->groupBy('item_code', 's_warehouse')->get();

			$ste_total_issued = collect($ste_total_issued)->groupBy('item_code')->toArray();
	
			$at_total_issued = DB::table('tabAthena Transactions as at')
				->join('tabPacking Slip as ps', 'ps.name', 'at.reference_parent')
				->join('tabPacking Slip Item as psi', 'ps.name', 'psi.parent')
				->join('tabDelivery Note as dr', 'ps.delivery_note', 'dr.name')
				->whereIn('at.reference_type', ['Packing Slip', 'Picking Slip'])
				->where('dr.docstatus', 0)->where('ps.docstatus', '<', 2)
				->where('psi.status', 'Issued')->whereIn('at.item_code', $item_codes)
				->whereIn('psi.item_code', $item_codes)->where('at.source_warehouse', $production_order_details->wip_warehouse)
				->selectRaw('SUM(at.issued_qty) as total_issued, at.item_code')
				->groupBy('at.item_code', 'at.source_warehouse')->get();
	
			$at_total_issued = collect($at_total_issued)->groupBy('item_code')->toArray();

			$stock_entry_detail = $rm_temp_bin_arr = $fg_temp_bin_arr = [];
			foreach ($production_order_items as $index => $row) {
				$qty = $row['required_qty'];
				$qty_before_transaction_temp = isset($raw_materials_current_bin[$row['item_code']]) ? $raw_materials_current_bin[$row['item_code']] : 0;
				$expected_qty_after_transaction = $qty_before_transaction_temp - $qty;
			
				$rm_temp_bin_arr[$row['item_code']]['expected_qty_after_transaction'] = number_format($expected_qty_after_transaction, 6, '.', '');

				$bom_material = DB::connection('mysql')->table('tabBOM Item')
					->where('parent', $production_order_details->bom_no)
					->where('item_code', $row['item_code'])->first();
				
				if(!$bom_material){
					$valuation_rate = DB::connection('mysql')->table('tabBin')
						->where('item_code', $row['item_code'])
						->where('warehouse', $production_order_details->wip_warehouse)
						->sum('valuation_rate');
				}

				$base_rate = ($bom_material) ? $bom_material->base_rate : $valuation_rate;

				if($qty > 0){
					$is_uom_whole_number = DB::connection('mysql')->table('tabUOM')->where('name', $row['stock_uom'])->first();
					if($is_uom_whole_number && $is_uom_whole_number->must_be_whole_number == 1){
						$qty = round($qty);
					}

					$remaining_transferred_qty = $row['transferred_qty'] - $row['consumed_qty'];

					if(number_format($remaining_transferred_qty, 5, '.', '') < number_format($qty, 5, '.', '')){
						return response()->json(['success' => 0, 'message' => 'Insufficient transferred qty for ' . $row['item_code'] . ' in ' . $production_order_details->wip_warehouse]);
					}

					if($qty <= 0){
						return response()->json(['success' => 0, 'message' => 'Qty cannot be less than or equal to 0 for ' . $row['item_code'] . ' in ' . $production_order_details->wip_warehouse]);
					}

					$actual_qty = DB::connection('mysql')->table('tabBin')->where('item_code', $row['item_code'])
						->where('warehouse', $production_order_details->wip_warehouse)->sum('actual_qty');
						
					$reserved_qty = 0;
					$consumed_qty = 0;
					if (array_key_exists($row['item_code'], $stock_reservation) || isset($stock_reservation[$row['item_code']])) {
						$reserved_qty = $stock_reservation[$row['item_code']][0]->total_reserved_qty;
						$consumed_qty = $stock_reservation[$row['item_code']][0]->total_consumed_qty;
					}
		
					$reserved_qty = $reserved_qty - $consumed_qty;
		
					$issued_qty = 0;
					if (array_key_exists($row['item_code'], $ste_total_issued)) {
						$issued_qty = $ste_total_issued[$row['item_code']][0]->total_issued;
					}
		
					if (array_key_exists($row['item_code'], $at_total_issued)) {
						$issued_qty += $at_total_issued[$row['item_code']][0]->total_issued;
					}
		
					$actual_qty = ($actual_qty - $issued_qty) - $reserved_qty;

					if($docstatus == 1){
						$production_order_details_mes = DB::connection('mysql_mes')->table('production_order')
							->where('production_order.production_order', $production_order)->first();

						$has_production_order = DB::connection('mysql_mes')->table('production_order')
							->where('item_code', $row['item_code'])->where('parent_item_code', $production_order_details_mes->parent_item_code)
							->where('sales_order', $production_order_details_mes->sales_order)
							->where('material_request', $production_order_details_mes->material_request)
							->where('sub_parent_item_code', $production_order_details_mes->item_code)->first();
						if ($has_production_order) {
							$insufficient_stock_msg = 'Insufficient stock for ' . $row['item_code'] . ' in ' . $production_order_details->wip_warehouse . '. One or more production parts are pending for feedback, please check your parts production order.';
						} else {
							$insufficient_stock_msg = 'Insufficient stock for ' . $row['item_code'] . ' in ' . $production_order_details->wip_warehouse . '. Some of the components quantity are pending for Issue.';
						}
						
						if($qty > $actual_qty){
							return response()->json(['success' => 0, 'message' => $insufficient_stock_msg]);
						}
					}

					DB::connection('mysql')->table('tabWork Order Item')->where('parent', $production_order)->where('item_code', $row['item_code'])->update(['consumed_qty' => $consumed_qty]);

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
						'item_name' => $row['item_name'],
						'image' => null,
						'additional_cost' => 0,
						'stock_uom' => $row['stock_uom'],
						'basic_amount' => $base_rate * $qty,
						'sample_quantity' => 0,
						'uom' => $row['stock_uom'],
						'basic_rate' => $base_rate,
						'description' => $row['description'],
						'barcode' => null,
						'conversion_factor' => ($bom_material) ? $bom_material->conversion_factor : 1,
						'item_code' => $row['item_code'],
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
			}

			$rm_amount = collect($stock_entry_detail)->sum('basic_amount');
			$rate = $rm_amount / $request->fg_completed_qty;

			$qty_before_transaction_temp = isset($fg_current_bin[$production_order_details->production_item]) ? $fg_current_bin[$production_order_details->production_item] : 0;
			$expected_qty_after_transaction = $qty_before_transaction_temp + $request->fg_completed_qty;
		
			$fg_temp_bin_arr[$production_order_details->production_item]['expected_qty_after_transaction'] = number_format($expected_qty_after_transaction, 6, '.', '');

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
				'total_outgoing_value' => collect($stock_entry_detail)->sum('basic_amount'),
				'supplier_name' => null,
				'remarks' => null,
				'_user_tags' => null,
				'total_additional_costs' => 0,
				'bom_no' => $production_order_details->bom_no,
				'amended_from' => null,
				'total_amount' => collect($stock_entry_detail)->sum('basic_amount'),
				'total_incoming_value' => collect($stock_entry_detail)->sum('basic_amount'),
				'project' => $production_order_details->project,
				'_assign' => null,
				'select_print_heading' => null,
				'posting_date' => $now->format('Y-m-d'),
				'target_address_display' => null,
				'work_order' => $production_order,
				'purpose' => 'Manufacture',
				'stock_entry_type' => 'Manufacture',
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
				foreach ($production_order_items as $row) {
					$consumed_qty = DB::connection('mysql')->table('tabStock Entry as ste')
						->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
						->where('ste.work_order', $production_order)->whereNull('sted.t_warehouse')
						->where('sted.item_code', $row['item_code'])->where('purpose', 'Manufacture')
						->where('ste.docstatus', 1)->sum('qty');
	
					DB::connection('mysql')->table('tabWork Order Item')->where('parent', $production_order)->where('item_code', $row['item_code'])
						->update(['consumed_qty' => $consumed_qty]);
				}

				$produced_qty = $production_order_details->produced_qty + $request->fg_completed_qty;

				// $work_order_status = $remarks_override == 'Override' ? 'In Progress' : $production_order_details->status;
				$work_order_status = $remarks_override == 'Override' ? 'In Process' : $production_order_details->status;
				$work_order_status = ($produced_qty == $production_order_details->qty) ? 'Completed' : $work_order_status;
			
				$production_data = [
					'modified' => $now->toDateTimeString(),
					'modified_by' => Auth::user()->email,
					'produced_qty' => $produced_qty,
					'status' => $work_order_status
				];

				DB::connection('mysql')->table('tabWork Order')->where('name', $production_order)->update($production_data);

				$update_bin_res = $this->update_bin($new_id);
				if ($update_bin_res['status'] == 0) {
					return response()->json(['success' => 0, 'message' => $update_bin_res['message']]);
				}
				$this->create_stock_ledger_entry($new_id);
				$this->create_gl_entry($new_id);
			}

			// get raw materials qty in wip warehouse AFTER submission of stock entry (for double checking of stocks after transaction)
			$raw_materials_current_bin = DB::connection('mysql')->table('tabBin')->whereIn('item_code', $item_codes)
				->where('warehouse', $production_order_details->wip_warehouse)->pluck('actual_qty', 'item_code')->toArray();

			// check for stock discrepancy for raw materials
			foreach ($raw_materials_current_bin as $rm_item_code => $rm_qty) {
				$expected_qty_after_transaction = isset($rm_temp_bin_arr[$rm_item_code]['expected_qty_after_transaction']) ? $rm_temp_bin_arr[$rm_item_code]['expected_qty_after_transaction'] : null;
				if ($expected_qty_after_transaction == null) {
					return response()->json(['success' => 0, 'message' => 'There was a problem creating feedback. Please reload the page and try again.']);
				}

				if (number_format($expected_qty_after_transaction, 4, '.', '') != number_format($rm_qty, 4, '.', '')) {
					return response()->json(['success' => 0, 'message' => 'There was a problem creating feedback. Please reload the page and try again.']);
				}
			}

			// get finished good qty in target warehouse AFTER submission of stock entry (for double checking of stocks after transaction)
			$fg_current_bin = DB::connection('mysql')->table('tabBin')->where('item_code', $production_order_details->production_item)
				->where('warehouse', $mes_production_order_details->fg_warehouse)->pluck('actual_qty', 'item_code')->toArray();
			
			// check for stock discrepancy for finished good
			foreach ($fg_current_bin as $fg_item_code => $fg_qty) {
				$expected_qty_after_transaction = isset($fg_temp_bin_arr[$fg_item_code]['expected_qty_after_transaction']) ? $fg_temp_bin_arr[$fg_item_code]['expected_qty_after_transaction'] : null;
				if ($expected_qty_after_transaction == null) {
					return response()->json(['success' => 0, 'message' => 'There was a problem creating feedback. Please reload the page and try again.']);
				}

				if ($expected_qty_after_transaction != $fg_qty) {
					return response()->json(['success' => 0, 'message' => 'There was a problem creating feedback. Please reload the page and try again.']);
				}
			}

			$is_feedbacked = DB::connection('mysql')->table('tabStock Entry')
				->where('name', $new_id)->where('purpose', 'Manufacture')->where('docstatus', 1)->first();

			if ($is_feedbacked) {
				DB::connection('mysql_mes')->beginTransaction();
				
				$manufactured_qty = $production_order_details->produced_qty + $request->fg_completed_qty;

				$production_data_mes = [
					'last_modified_at' => $now->toDateTimeString(),
					'last_modified_by' => Auth::user()->email,
					'feedback_qty' => $manufactured_qty,
					'status' => $manufactured_qty >= $production_order_details->qty ? 'Feedbacked' : 'Partially Feedbacked',
					'remarks' => $remarks_override
				];
				
				if($remarks_override == 'Override'){
					$production_data_mes['produced_qty'] = $manufactured_qty;

					$job_ticket_mes = [
						'completed_qty' => $manufactured_qty,
						'remarks' => $remarks_override,
						'status' => 'Completed',
						'last_modified_by' => Auth::user()->email,
					];

					DB::connection('mysql_mes')->table('job_ticket')
						->where('production_order', $production_order_details->name)
						->where('status', '!=', 'Completed')->update($job_ticket_mes);
				}

				DB::connection('mysql_mes')->table('production_order')
					->where('production_order', $production_order_details->name)->update($production_data_mes);

				$feedbacked_timelogs = [
					'production_order' => $mes_production_order_details->production_order,
					'ste_no' => $new_id,
					'item_code' => $production_order_details->production_item,
					'item_name' => $production_order_details->item_name,
					'feedbacked_qty' => $request->fg_completed_qty, 
					'from_warehouse' => $production_order_details->wip_warehouse,
					'to_warehouse' => $mes_production_order_details->fg_warehouse,
					'transaction_date' => $now->format('Y-m-d'),
					'transaction_time' => $now->format('G:i:s'),
					'created_at' => $now->toDateTimeString(),
					'created_by' => Auth::user()->email,
				];

				$feedback_id = DB::connection('mysql_mes')->table('feedbacked_logs')->insertGetId($feedbacked_timelogs);

				DB::connection('mysql_mes')->commit();
				DB::connection('mysql')->commit();

				if (!$feedback_id) {
					DB::connection('mysql_mes')->rollback();
					DB::connection('mysql')->rollback();
				}

				$this->insert_production_scrap($production_order_details->name, $request->fg_completed_qty);
			} else {
				DB::connection('mysql')->rollback();
				DB::connection('mysql_mes')->rollback();

				return response()->json(['success' => 0, 'message' => 'There was a problem create stock entry. Please try again.']);
			}
		
			return response()->json(['success' => 1, 'message' => 'Stock Entry has been created.', 'stock_entry' => $new_id]);
		} catch (Exception $e) {
			DB::connection('mysql')->rollback();
			DB::connection('mysql_mes')->rollback();
			return response()->json(['success' => 0, 'message' => 'There was a problem create stock entry']);
		}
    }

	public function split_source_warehouse(Request $request){
		DB::connection('mysql')->beginTransaction();
		DB::connection('mysql_mes')->beginTransaction();
		try {
			$mes_production_order_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)->first();

			$production_order_details = DB::connection('mysql')->table('tabWork Order as wo')
				->join('tabWork Order Item as woi', 'woi.parent', 'wo.name')
				->where('wo.name', $request->production_order)->where('woi.item_code', $request->item_code)->whereNotIn('status', ['Cancelled', 'Closed', 'Completed', 'Feedbacked'])
				->select('wo.*', 'woi.*', 'woi.name as item_id')
				->first();

			if(!$mes_production_order_details || !$production_order_details){
				return response()->json(['success' => 0, 'message' => 'Production Order <b>'.$request->production_order.'</b> not found.']);
			}

			$now = Carbon::now();
			$stock_entry_detail = DB::connection('mysql')->table('tabStock Entry as ste')
				->join('tabStock Entry Detail as sted', 'sted.parent', 'ste.name')
				->where('sted.item_code', $request->item_code)->where('ste.docstatus', 0)
				->where('ste.purpose', 'Material Transfer for Manufacture')->where('ste.work_order', $request->production_order)->where('sted.s_warehouse', $request->current_source_warehouse)
				->select('ste.name', 'sted.name as item_id', 'sted.qty', 'sted.transfer_qty', 'sted.s_warehouse as source_warehouse', 'ste.fg_completed_qty')
				->first();

			if(!$stock_entry_detail){
				return response()->json(['success' => 0, 'message' => 'Stock Entry not found. Please create withdrawal slip first.']);
			}

			if($request->requested_qty <= 0){
				return response()->json(['success' => 0, 'message' => 'New requested qty cannot be equal to or less than 0.']);
			}

			if($request->requested_qty > $stock_entry_detail->qty){
				return response()->json(['success' => 0, 'message' => 'New requested qty cannot be equal to or more than the current requested qty.']);
			}

			if($request->source_warehouse == $stock_entry_detail->source_warehouse){
				return response()->json(['success' => 0, 'message' => 'New source warehouse cannot be the same as the current source warehouse.']);
			}

			// Current Source Warehouse Stock Entry
			$bom_material = DB::connection('mysql')->table('tabBOM Item')
				->where('parent', $production_order_details->bom_no)
				->where('item_code', $request->item_code)->first();
				
			if(!$bom_material){
				$valuation_rate = DB::connection('mysql')->table('tabBin')
					->where('item_code', $request->item_code)
					->where('warehouse', $production_order_details->wip_warehouse)
					->sum('valuation_rate');
			}

			$base_rate = $bom_material ? $bom_material->base_rate : $valuation_rate;

			$qty_in_current = $stock_entry_detail->qty - $request->requested_qty;

			$values_in_current_source = [
				'qty' => $qty_in_current,
				'transfer_qty' => $qty_in_current,
				'modified' => $now->toDateTimeString(),
				'modified_by' => Auth::user()->email
			];
			
			if(in_array($stock_entry_detail->source_warehouse, ['Fabrication - FI', 'Spotwelding Warehouse - FI']) && $mes_production_order_details->operation_id == 1){
				$values_in_current_source['status'] = 'Issued';
				$values_in_current_source['validate_item_code'] = $request->item_code;
			}

			$current_source_docstatus = $this->get_actual_qty($request->item_code, $request->current_source_warehouse) >= $qty_in_current ? 1 : 0;
			$current_source_docstatus = isset($values_in_current_source['status']) && $values_in_current_source['status'] == 'Issued' ? $current_source_docstatus : 0;

			$values_in_current_source['docstatus'] = $current_source_docstatus;

			DB::connection('mysql')->table('tabStock Entry Detail')->where('name', $stock_entry_detail->item_id)->update($values_in_current_source);

			// New Source Warehouse STE
			$item_status = 'For Checking';
			if(in_array($request->source_warehouse, ['Fabrication - FI', 'Spotwelding Warehouse - FI']) && $mes_production_order_details->operation_id == 1){
				$item_status = 'Issued';
			}

			$new_source_docstatus = ($this->get_actual_qty($request->item_code, $request->source_warehouse) >= $request->requested_qty) ? 1 : 0;
			$new_source_docstatus = ($item_status == 'Issued') ? $new_source_docstatus : 0;

			$latest_pro = DB::connection('mysql')->table('tabStock Entry')->where('name', 'like', '%step%')->max('name');
			$latest_pro_exploded = explode("-", $latest_pro);
			$new_id = (($latest_pro) ? $latest_pro_exploded[1] : 0) + 1;
			$new_id = str_pad($new_id, 6, '0', STR_PAD_LEFT);
			$new_id = 'STEP-'.$new_id;

			$stock_entry_data = [
				'name' => $new_id,
				'creation' => $now->toDateTimeString(),
				'modified' => $now->toDateTimeString(),
				'modified_by' => Auth::user()->email,
				'owner' => Auth::user()->email,
				'docstatus' => $new_source_docstatus,
				'idx' => 0,
				'use_multi_level_bom' => 1,
				'naming_series' => 'STE-',
				'fg_completed_qty' => $stock_entry_detail->fg_completed_qty,
				'posting_time' => $now->format('H:i:s'),
				'to_warehouse' => $production_order_details->fg_warehouse,
				'title' => 'Material Transfer for Manufacture',
				'set_posting_time' => 0,
				'from_bom' => $production_order_details->bom_no ? 1 : 0,
				'value_difference' => 0,
				'company' => 'FUMACO Inc.',
				'total_outgoing_value' => collect($stock_entry_detail)->sum('basic_amount'),
				'total_additional_costs' => 0,
				'bom_no' => $production_order_details->bom_no,
				'total_amount' => collect($stock_entry_detail)->sum('basic_amount'),
				'total_incoming_value' => collect($stock_entry_detail)->sum('basic_amount'),
				'project' => $production_order_details->project,
				'posting_date' => $now->format('Y-m-d'),
				'work_order' => $request->production_order,
				'purpose' => 'Material Transfer for Manufacture',
				'stock_entry_type' => 'Material Transfer for Manufacture',
				'material_request' => $production_order_details->material_request,
				'item_status' => $item_status,
				'sales_order_no' => $mes_production_order_details->sales_order,
				'transfer_as' => 'Internal Transfer',
				'item_classification' => $production_order_details->item_classification,
				'qty_repack' => 0,
				'so_customer_name' => $mes_production_order_details->customer,
				'order_type' => $mes_production_order_details->classification,
			];
			
			DB::connection('mysql')->table('tabStock Entry')->insert($stock_entry_data);

			$stock_entry_detail_arr = [
				'name' =>  uniqid(),
				'creation' => $now->toDateTimeString(),
				'modified' => $now->toDateTimeString(),
				'modified_by' => Auth::user()->email,
				'owner' => Auth::user()->email,
				'docstatus' => $new_source_docstatus,
				'parent' => $new_id,
				'parentfield' => 'items',
				'parenttype' => 'Stock Entry',
				'idx' => 1,
				't_warehouse' => $mes_production_order_details->fg_warehouse,
				'transfer_qty' => $request->requested_qty,
				'expense_account' => 'Cost of Goods Sold - FI',
				'cost_center' => 'Main - FI',
				'actual_qty' => 0,
				's_warehouse' => $request->source_warehouse,
				'item_name' => $production_order_details->item_name,
				'additional_cost' => 0,
				'stock_uom' => $production_order_details->stock_uom,
				'basic_amount' => $base_rate * $request->requested_qty,
				'sample_quantity' => 0,
				'uom' => $production_order_details->stock_uom,
				'basic_rate' => $base_rate,
				'description' => $production_order_details->description,
				'conversion_factor' => 1,
				'item_code' => $request->item_code,
				'retain_sample' => 0,
				'qty' => $request->requested_qty,
				'allow_zero_valuation_rate' => 0,
				'amount' => $base_rate * $request->requested_qty,
				'valuation_rate' => $base_rate,
				'status' => $item_status,
				'session_user' => Auth::user()->email,
				'validate_item_code' => $item_status == 'Issued' ? $request->item_code : null,
				'date_modified' => $now->toDateTimeString()
			];

			DB::connection('mysql')->table('tabStock Entry Detail')->insert($stock_entry_detail_arr);

			if ($new_source_docstatus || $current_source_docstatus) {
				DB::connection('mysql')->table('tabWork Order Item')->where('name', $production_order_details->name)->update(['transferred_qty' => $qty_in_current + $request->requested_qty]);

				$values = ['material_transferred_for_manufacturing' => $mes_production_order_details->qty_to_manufacture];
				if($mes_production_order_details->status == 'Not Started'){
					$values['status'] = 'In Process';
				}
				
				DB::connection('mysql')->table('tabWork Order')
					->where('name', $mes_production_order_details->production_order)
					->update($values);

				if($current_source_docstatus){
					$update_bin_res = $this->update_bin($stock_entry_detail->name);
					if ($update_bin_res['status'] == 0) {
						return response()->json(['success' => 0, 'message' => $update_bin_res['message']]);
					}

					$this->create_stock_ledger_entry($stock_entry_detail->name);
					$this->create_gl_entry($stock_entry_detail->name);
				}

				if($new_source_docstatus){
					$update_bin_res = $this->update_bin($new_id);
					if ($update_bin_res['status'] == 0) {
						return response()->json(['success' => 0, 'message' => $update_bin_res['message']]);
					}

					$this->create_stock_ledger_entry($new_id);
					$this->create_gl_entry($new_id);
				}
			}

			$activity_logs = [
				'action' => 'Changed Withdrawal Slip Source Warehouse',
				'message' => 'Created withdrawal slip for '.$request->requested_qty.' '.$mes_production_order_details->stock_uom.' of '.$request->item_code.' from '.$stock_entry_detail->source_warehouse.' to '.$request->source_warehouse.' for '.$request->production_order.' by '.Auth::user()->employee_name.' at '.Carbon::now()->toDateTimeString(),
				'reference' => $request->production_order,
				'created_at' => Carbon::now()->toDateTimeString(),
				'created_by' => Auth::user()->email
			];
	
			DB::connection('mysql_mes')->table('activity_logs')->insert($activity_logs);

			DB::connection('mysql_mes')->commit();
			DB::connection('mysql')->commit();
			return response()->json(['success' => 1, 'message' => 'Stock Entry has been created.']);
		} catch (\Throwable $th) {
			DB::connection('mysql')->rollback();
			DB::connection('mysql_mes')->rollback();
			return response()->json(['success' => 0, 'message' => 'There was a problem create stock entry']);
		}
	}

	// create stock ledger entry for manufacture ste
    public function create_stock_ledger_entry($stock_entry){
    	$now = Carbon::now();
        $stock_entry_qry = DB::connection('mysql')->table('tabStock Entry')
        	->where('name', $stock_entry)->first();
		if ($stock_entry_qry && $stock_entry_qry->docstatus == 1) {
			$stock_entry_detail = DB::connection('mysql')->table('tabStock Entry Detail')
				->where('parent', $stock_entry)->get();

			$stock_ledger_entry = [];
			foreach ($stock_entry_detail as $row) {
				$warehouse = ($row->s_warehouse) ? $row->s_warehouse : $row->t_warehouse;

				$bin_qry = DB::connection('mysql')->table('tabBin')->where('warehouse', $warehouse)
					->where('item_code', $row->item_code)->first();

				$stock_ledger_entry[] = [
					'name' => 'mes' . uniqid(),
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
					'is_cancelled' => 0,
					'qty_after_transaction' => $bin_qry->actual_qty,
					'_user_tags' => null,
					'batch_no' => $row->batch_no,
					'stock_value_difference' => ($row->s_warehouse) ? ($row->qty * $row->valuation_rate) * -1  : $row->qty * $row->valuation_rate,
					'posting_date' => $now->format('Y-m-d'),
				];
			}

			DB::connection('mysql')->table('tabStock Ledger Entry')->insert($stock_ledger_entry);
		}
    }

	public function update_bin($stock_entry){
        try {
            $now = Carbon::now();

            $latest_id = DB::connection('mysql')->table('tabBin')->where('name', 'like', '%BINM%')->max('name');
            $latest_id = ($latest_id) ? $latest_id : 0;
            $latest_id_exploded = explode("/", $latest_id);
            $new_id = (array_key_exists(1, $latest_id_exploded)) ? $latest_id_exploded[1] + 1 : 1;

            $stock_entry_qry = DB::connection('mysql')->table('tabStock Entry')->where('name', $stock_entry)->first();
			if ($stock_entry_qry && $stock_entry_qry->docstatus == 1) {
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
							$qty_after_transaction = $bin_qry->actual_qty - $row->transfer_qty;
							if ($qty_after_transaction < 0) {
								return ['status' => 0, 'message' => 'Insufficient stock for ' . $row->item_code . ' in ' . $row->s_warehouse];
							}
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
							$qty_after_transaction = $bin_qry->actual_qty + $row->transfer_qty;
							if ($qty_after_transaction <= 0) {
								return ['status' => 0, 'message' => 'Qty cannot be less than or equal to zero for ' . $row->item_code . ' in ' . $row->t_warehouse];
							}
	
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
	
				return ['status' => 1, 'message' => 'Bin updated.'];
			}
        } catch (Exception $e) {
            return ['status' => 0, 'message' => 'Error creating transaction. Please try again.'];
        }
    }
	
	public function create_gl_entry($stock_entry){
		$now = Carbon::now();
		$stock_entry_qry = DB::connection('mysql')->table('tabStock Entry')->where('name', $stock_entry)->first();
		if ($stock_entry_qry && $stock_entry_qry->docstatus == 1) {
			$stock_entry_detail = DB::connection('mysql')->table('tabStock Entry Detail')
				->where('parent', $stock_entry)
				->select('s_warehouse', 't_warehouse', DB::raw('SUM(basic_amount) as basic_amount'), 'parent', 'cost_center', 'expense_account')
				->groupBy('s_warehouse', 't_warehouse', 'parent', 'cost_center', 'expense_account')
				->get();

			$gl_entry = [];
			foreach ($stock_entry_detail as $row) {
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
					'name' => 'mes' . uniqid(),
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
            ->whereNotIn('prod.status', ['Cancelled', 'Completed', 'Closed'])->where('tsd.workstation', 'Painting')
            ->where(function($q) use ($request) {
                $q->where('prod.production_order', 'LIKE', '%'.$request->q.'%')
                ->orWhere('prod.item_code', 'LIKE', '%'.$request->q.'%')
                ->orWhere('prod.customer', 'LIKE', '%'.$request->q.'%');
            })
			->select('prod.production_order', 'prod.qty_to_manufacture', 'prod.sales_order', 'prod.material_request', 'prod.customer', 'prod.item_code', 'prod.description', 'prod.stock_uom', 'prod.qty_to_manufacture', 'prod.produced_qty')
			->groupBy('prod.production_order', 'prod.qty_to_manufacture', 'prod.sales_order', 'prod.material_request', 'prod.customer', 'prod.item_code', 'prod.description', 'prod.stock_uom', 'prod.qty_to_manufacture', 'prod.produced_qty')
            ->orderBy('prod.last_modified_at', 'desc')->get();
        
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
            ->where('work_order', $production_order)->where('ste.purpose', 'Manufacture')
            ->whereNull('s_warehouse')->whereNotNull('t_warehouse')
            ->select('ste.name', 'sted.item_code', 'sted.description', 'sted.qty', 'ste.posting_date', 'sted.t_warehouse', 'sted.stock_uom')
            ->orderBy('ste.creation', 'desc')
            ->first();

        $transferred_qty = DB::connection('mysql')->table('tabStock Entry as ste')
            ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
            ->where('work_order', $production_order)->where('ste.purpose', 'Manufacture')
            ->whereNull('s_warehouse')->whereNotNull('t_warehouse')
            ->sum('sted.qty');

        $data[] = [
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
				->where('item_classification', 'like', '%'.$request->item_classification.'%')
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
			->groupBy('process.process_id', 'process.process_name')
			->orderBy('process_name', 'asc')->get();
	}

	// /get_pending_material_transfer_for_manufacture/{production_order}
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

		if(!$production_order_details){
			$message = 'Production Order <b>' . $production_order . '</b> parent item code mismatch. Please update parent item code.';

			$reference_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->first();
			$reference_doctype = $reference_details->sales_order ? 'tabSales Order Item' : 'tabMaterial Request Item';
			$reference_name = $reference_details->sales_order ? $reference_details->sales_order : $reference_details->material_request;

			$delivery_date_items = [];
			if ($reference_details) {
				$delivery_date_items = DB::connection('mysql_mes')->table('delivery_date')->where('reference_no', $reference_name)->pluck('parent_item_code');
			}
			
			$parent_item_codes = DB::connection('mysql')->table($reference_doctype)->where('parent', $reference_name)
				->whereIn('item_code', $delivery_date_items)->pluck('description', 'item_code');

			return view('tables.tbl_pending_material_transfer_for_manufacture', compact('message', 'parent_item_codes', 'production_order'));
		}

		$q = DB::connection('mysql')->table('tabStock Entry as ste')
			->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
			->where('ste.work_order', $production_order)->where('purpose', 'Material Transfer for Manufacture')
			->select('sted.name as sted_id', 'ste.name', 'sted.item_code', 'sted.description', 'sted.qty', 'sted.stock_uom', 'sted.s_warehouse', 'sted.t_warehouse', 'sted.status', 'sted.session_user', 'sted.date_modified')
			->where('ste.docstatus', 0)->get();

		$components = $parts = [];
		foreach ($q as $row) {
			$item_details = DB::connection('mysql')->table('tabItem')->where('name', $row->item_code)->first();
            // get item stock based on feedbacked qty for housing and other items with sub assemblies
            $has_production_order = DB::connection('mysql_mes')->table('production_order')
                ->where('item_code', $row->item_code)->where('parent_item_code', $production_order_details->parent_item_code)
                ->where('sales_order', $production_order_details->sales_order)->where('status', '!=', 'Cancelled')
                ->where('material_request', $production_order_details->material_request)
                ->where('sub_parent_item_code', $production_order_details->item_code)->first();
				
			$available_qty_at_source = DB::connection('mysql')->table('tabBin')->where('item_code', $row->item_code)
				->where('warehouse', $row->s_warehouse)->sum('actual_qty');

			$available_qty_at_wip = DB::connection('mysql')->table('tabBin')->where('item_code', $row->item_code)
				->where('warehouse', $row->t_warehouse)->sum('actual_qty');

            if($has_production_order && $production_order_details->bom_no != null){
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
                    'production_order' => $has_production_order,
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

		// for tabs
        // get production order stock entries
        $stock_entry_arr = DB::connection('mysql')->table('tabStock Entry')
            ->where('purpose', 'Material Transfer for Manufacture')
            ->where('work_order', $production_order)
            ->where('docstatus', '<', 2)->pluck('name');

        $production_order_items = DB::connection('mysql')->table('tabWork Order Item as poi')->where('parent', $production_order)->get();

		$item_codes = array_column($production_order_items->toArray(), 'item_code');

        $s_warehouses = DB::connection('mysql')->table('tabStock Entry Detail')
            ->whereIn('parent', $stock_entry_arr)->whereIn('item_code', $item_codes)
            ->where('docstatus', '<', 2)->pluck('s_warehouse');

        $stock_reservation = DB::connection('mysql')->table('tabStock Reservation')->whereIn('item_code', $item_codes)
            ->whereIn('warehouse', $s_warehouses)->where('status', 'Active')
            ->selectRaw('SUM(reserve_qty) as total_reserved_qty, SUM(consumed_qty) as total_consumed_qty, CONCAT(item_code, "-", warehouse) as item')
            ->groupBy('item_code', 'warehouse')->get();
        $stock_reservation = collect($stock_reservation)->groupBy('item')->toArray();

        $ste_total_issued = DB::table('tabStock Entry Detail')->where('docstatus', 0)->where('status', 'Issued')
            ->whereIn('item_code', $item_codes)->whereIn('s_warehouse', $s_warehouses)
            ->selectRaw('SUM(qty) as total_issued, CONCAT(item_code, "-", s_warehouse) as item')
            ->groupBy('item_code', 's_warehouse')->get();
        $ste_total_issued = collect($ste_total_issued)->groupBy('item')->toArray();

        $at_total_issued = DB::table('tabAthena Transactions as at')
            ->join('tabPacking Slip as ps', 'ps.name', 'at.reference_parent')
            ->join('tabPacking Slip Item as psi', 'ps.name', 'psi.parent')
            ->join('tabDelivery Note as dr', 'ps.delivery_note', 'dr.name')
            ->whereIn('at.reference_type', ['Packing Slip', 'Picking Slip'])
            ->where('dr.docstatus', 0)->where('ps.docstatus', '<', 2)
            ->where('psi.status', 'Issued')->whereIn('at.item_code', $item_codes)
            ->whereIn('psi.item_code', $item_codes)->whereIn('at.source_warehouse', $s_warehouses)
            ->selectRaw('SUM(at.issued_qty) as total_issued, CONCAT(at.item_code, "-", at.source_warehouse) as item')
            ->groupBy('at.item_code', 'at.source_warehouse')
            ->get();

        $at_total_issued = collect($at_total_issued)->groupBy('item')->toArray();

        $tab_components = $tab_parts = [];
        foreach ($production_order_items as $item) {
            $item_details = DB::connection('mysql')->table('tabItem')->where('name', $item->item_code)->first();
            // get item stock based on feedbacked qty for housing and other items with sub assemblies
            $has_production_order = DB::connection('mysql_mes')->table('production_order')
                ->where('item_code', $item->item_code)->where('parent_item_code', $production_order_details->parent_item_code)
                ->where('sales_order', $production_order_details->sales_order)->where('status', '!=', 'Cancelled')
                ->where('material_request', $production_order_details->material_request)
                ->where('sub_parent_item_code', $production_order_details->item_code)->first();

            $references = DB::connection('mysql')->table('tabStock Entry as ste')
                ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                ->where('ste.work_order', $production_order)->where('ste.purpose', 'Material Transfer for Manufacture')
                ->where('ste.docstatus', 1)->where('sted.item_code', $item->item_code)
                ->select('ste.name', 'sted.date_modified', 'sted.session_user', 'sted.qty')->get();

            $item_withdrawals = DB::connection('mysql')->table('tabStock Entry Detail')
                ->whereIn('parent', $stock_entry_arr)->where('item_code', $item->item_code)
                ->where('docstatus', 1)
                ->selectRaw('SUM(qty) as qty, s_warehouse, status, SUM(issued_qty) as issued_qty, GROUP_CONCAT(DISTINCT parent) as ste_names, docstatus, GROUP_CONCAT(DISTINCT remarks) as remarks')
                ->groupBy('s_warehouse', 'status', 'docstatus')
                ->get();

            $pending_item_withdrawals = DB::connection('mysql')->table('tabStock Entry Detail')
                ->whereIn('parent', $stock_entry_arr)->where('item_code', $item->item_code)
                ->where('docstatus', 0)
                ->selectRaw('qty, s_warehouse, status, issued_qty, name, docstatus, parent, remarks')
                ->get();

			// get transferred qty
			$transferred_qty = ($item->transferred_qty - $item->returned_qty);

            $withdrawals = [];
            foreach ($item_withdrawals as $i) {
				$reserved_qty = 0;
                if (array_key_exists($item->item_code . '-' . $i->s_warehouse, $stock_reservation)) {
                    $reserved_qty = $stock_reservation[$item->item_code . '-' . $i->s_warehouse][0]->total_reserved_qty;
                }
    
                $consumed_qty = 0;
                if (array_key_exists($item->item_code . '-' . $i->s_warehouse, $stock_reservation)) {
                    $consumed_qty = $stock_reservation[$item->item_code . '-' . $i->s_warehouse][0]->total_consumed_qty;
                }
    
                $reserved_qty = $reserved_qty - $consumed_qty;
    
                $issued_qty = 0;
                if (array_key_exists($item->item_code . '-' . $i->s_warehouse, $ste_total_issued)) {
                    $issued_qty = $ste_total_issued[$item->item_code . '-' . $i->s_warehouse][0]->total_issued;
                }
    
                if (array_key_exists($item->item_code . '-' . $i->s_warehouse, $at_total_issued)) {
                    $issued_qty += $at_total_issued[$item->item_code . '-' . $i->s_warehouse][0]->total_issued;
                }
    
                $actual_qty = DB::connection('mysql')->table('tabBin')->where('item_code', $item->item_code)->where('warehouse', $i->s_warehouse)->sum('actual_qty');
    
                $actual_qty = ($actual_qty - $issued_qty) - $reserved_qty;
                $actual_qty = $actual_qty < 0 ? 0 : $actual_qty;

                $withdrawals[] = [
                    'id' => null,
                    'source_warehouse' => $i->s_warehouse,
                    'actual_qty' => $actual_qty,
                    'qty' => ($i->docstatus == 1) ? ($i->qty - $item->returned_qty) : 0,
                    'issued_qty' => ($i->docstatus == 1 && $transferred_qty > 0) ? ($i->issued_qty - $item->returned_qty) : 0,
                    'status' => ($i->docstatus == 1 && $transferred_qty > 0) ? 'Issued' : 'For Checking',
                    'ste_names' => $i->ste_names,
                    'ste_docstatus' => $i->docstatus,
                    'requested_qty' => ($i->qty - $item->returned_qty),
                    'remarks' => $i->remarks
                ];
            }

            foreach ($pending_item_withdrawals as $i) {
				$reserved_qty = 0;
                if (array_key_exists($item->item_code . '-' . $i->s_warehouse, $stock_reservation)) {
                    $reserved_qty = $stock_reservation[$item->item_code . '-' . $i->s_warehouse][0]->total_reserved_qty;
                }
    
                $consumed_qty = 0;
                if (array_key_exists($item->item_code . '-' . $i->s_warehouse, $stock_reservation)) {
                    $consumed_qty = $stock_reservation[$item->item_code . '-' . $i->s_warehouse][0]->total_consumed_qty;
                }
    
                $reserved_qty = $reserved_qty - $consumed_qty;
    
                $issued_qty = 0;
                if (array_key_exists($item->item_code . '-' . $i->s_warehouse, $ste_total_issued)) {
                    $issued_qty = $ste_total_issued[$item->item_code . '-' . $i->s_warehouse][0]->total_issued;
                }
    
                if (array_key_exists($item->item_code . '-' . $i->s_warehouse, $at_total_issued)) {
                    $issued_qty += $at_total_issued[$item->item_code . '-' . $i->s_warehouse][0]->total_issued;
                }
    
                $actual_qty = DB::connection('mysql')->table('tabBin')->where('item_code', $item->item_code)->where('warehouse', $i->s_warehouse)->sum('actual_qty');
    
                $actual_qty = ($actual_qty - $issued_qty) - $reserved_qty;
                $actual_qty = $actual_qty < 0 ? 0 : $actual_qty;

                $withdrawals[] = [
                    'id' => $i->name,
                    'source_warehouse' => $i->s_warehouse,
                    'actual_qty' => $actual_qty,
                    'qty' => ($i->docstatus == 1) ? $i->qty : 0,
                    'issued_qty' => ($i->docstatus == 1 && $transferred_qty > 0) ? $i->issued_qty : 0,
                    'status' => ($i->docstatus == 1 && $transferred_qty > 0) ? 'Issued' : 'For Checking',
                    'ste_names' => $i->parent,
                    'ste_docstatus' => $i->docstatus,
                    'requested_qty' => $i->qty,
                    'remarks' => $i->remarks
                ];
            }

            $available_qty_at_wip = $this->get_actual_qty($item->item_code, $production_order_details->wip_warehouse);
            $consumed_qty = DB::connection('mysql')->table('tabStock Entry as ste')
                ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                ->where('ste.work_order', $production_order)->whereNull('sted.t_warehouse')
                ->where('sted.item_code', $item->item_code)->where('purpose', 'Manufacture')
                ->where('ste.docstatus', 1)->sum('qty');

            $remaining_available_qty_at_wip = $transferred_qty - $consumed_qty;
            if($available_qty_at_wip > $remaining_available_qty_at_wip) {
                $available_qty_at_wip = $remaining_available_qty_at_wip;
            }

            $is_alternative = ($item->item_alternative_for && $item->item_alternative_for != 'new_item') ? 1 : 0;

            if($has_production_order && $production_order_details->bom_no != null){
                $tab_parts[] = [
                    'name' => $item->name,
                    'idx' => $item->idx,
                    'item_code' => $item->item_code,
                    'item_name' => $item_details->item_name,
                    'description' => $item->description,
                    'item_image' => $item_details->item_image_path,
                    'item_classification' => $item_details->item_classification,
                    'withdrawals' => $withdrawals,
                    'source_warehouse' => $item->source_warehouse,
                    'required_qty' => $item->required_qty,
                    'stock_uom' => $item->stock_uom,
					'transferred_qty' => ($transferred_qty),
                    'actual_qty' => $this->get_actual_qty($item->item_code, $item->source_warehouse),
                    'production_order' => $has_production_order->production_order,
                    'available_qty_at_wip' => $available_qty_at_wip < 0 ? 0 : $available_qty_at_wip,
                    'status' => $has_production_order->status,
                    'references' => $references,
                    'is_alternative' => $is_alternative,
                    'item_alternative_for' => $item->item_alternative_for
                ];
            }else{
                $tab_components[] = [
                    'name' => $item->name,
                    'idx' => $item->idx,
                    'item_code' => $item->item_code,
                    'item_name' => $item_details->item_name,
                    'description' => $item->description,
                    'item_image' => $item_details->item_image_path,
                    'item_classification' => $item_details->item_classification,
                    'withdrawals' => $withdrawals,
                    'source_warehouse' => $item->source_warehouse,
                    'required_qty' => $item->required_qty,
                    'stock_uom' => $item->stock_uom,
                    'transferred_qty' => ($transferred_qty),
                    'actual_qty' => $this->get_actual_qty($item->item_code, $item->source_warehouse),
                    'production_order' => null,
                    'available_qty_at_wip' => $available_qty_at_wip < 0 ? 0 : $available_qty_at_wip,
                    'status' => null,
                    'references' => $references,
                    'is_alternative' => $is_alternative,
                    'item_alternative_for' => $item->item_alternative_for
                ];
            }
        }

        $required_items = array_merge($components, $parts);

		// get returned / for return items linked with production order (stock entry material transfer)
        $item_returns = DB::connection('mysql')->table('tabStock Entry as ste')
            ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
            ->where('ste.purpose', 'Material Transfer')->where('ste.transfer_as', 'For Return')
            ->where('ste.work_order', $production_order)
            ->where('ste.docstatus', '<', 2)->select('sted.*', 'ste.docstatus')->get();

        $items_return = [];
        foreach ($item_returns as $ret) {
            $item_details = DB::connection('mysql')->table('tabItem')->where('name', $ret->item_code)->first();
            $item_classification = $item_details->item_classification;

            $items_return[] = [
                'ste_name' => $ret->parent,
                'sted_name' => $ret->name,
                'sted_status' => $ret->status,
                'ste_docstatus' => $ret->docstatus,
                'idx' => $ret->idx,
                'item_code' => $ret->item_code,
                'description' => $ret->description,
                'item_image' => $item_details->item_image_path,
                'item_classification' => $item_classification,
                'target_warehouse' => $ret->t_warehouse,
                'requested_qty' => $ret->qty,
                'stock_uom' => $ret->stock_uom,
                'received_qty' => $ret->issued_qty,
            ];
        }

        $issued_qty = DB::connection('mysql')->table('tabStock Entry as ste')
            ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
            ->where('ste.docstatus', 0)->where('ste.work_order', $production_order)
            ->where('sted.status', 'Issued')->where('ste.purpose', 'Material Transfer for Manufacture')
            ->sum('qty');

        $feedbacked_logs = DB::connection('mysql_mes')->table('feedbacked_logs')->where('production_order', $production_order)->get();

        $time_logs_qry = DB::connection('mysql_mes')->table('job_ticket')
        	->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
        	->where('job_ticket.production_order', $production_order)->where('job_ticket.status', 'Completed')
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

        $start_date = ($from_time) ? Carbon::parse($from_time)->format('m-d-Y h:i A') : '--';
        $end_date = ($from_time) ? Carbon::parse($to_time)->format('m-d-Y h:i A') : '--';
        $duration = $dur_days .' '. $dur_hours . ' '. $dur_minutes . ' '. $dur_seconds;

		$item_to_manufacture = DB::connection('mysql')->table('tabBin')->where('item_code', $production_order_details->item_code)->where('warehouse', $production_order_details->fg_warehouse)->first();
		$current_stocks = $item_to_manufacture ? $item_to_manufacture->actual_qty : 0;

        $fast_issuance_warehouse = DB::connection('mysql_mes')->table('fast_issuance_warehouse')->pluck('warehouse')->toArray();
        $is_fast_issuance_user = DB::connection('mysql_mes')->table('fast_issuance_user')->where('user_access_id', Auth::user()->user_id)->exists();
		return view('tables.tbl_pending_material_transfer_for_manufacture', compact('components', 'parts', 'tab_components', 'tab_parts', 'list', 'url', 'production_order_details', 'actual_qty','feedbacked_logs','required_items', 'components', 'parts', 'items_return', 'issued_qty', 'feedbacked_logs', 'start_date', 'end_date', 'duration', 'fast_issuance_warehouse', 'is_fast_issuance_user', 'current_stocks'));
	}

	public function get_actual_qty($item_code, $warehouse){
        return DB::connection('mysql')->table('tabBin')->where('item_code', $item_code)
            ->where('warehouse', $warehouse)->sum('actual_qty');
    }

	public function get_available_qty($item_code, $warehouse){
        $reserved_qty = $this->get_reserved_qty($item_code, $warehouse);
        $actual_qty = $this->get_actual_qty($item_code, $warehouse);
        $issued_qty = $this->get_issued_qty($item_code, $warehouse);

        $available_qty = ($actual_qty - $issued_qty);
        $available_qty = ($available_qty - $reserved_qty);

        return ($available_qty < 0) ? 0 : $available_qty;
    }

    public function get_reserved_qty($item_code, $warehouse){
        $reserved_qty_for_website = 0;

        $stock_reservation_qty = DB::connection('mysql')->table('tabStock Reservation')->where('item_code', $item_code)
            ->where('warehouse', $warehouse)->whereIn('type', ['In-house', 'Consignment', 'Website Stocks'])->whereIn('status', ['Active', 'Partially Issued'])->sum('reserve_qty');

        $consumed_qty = DB::connection('mysql')->table('tabStock Reservation')->where('item_code', $item_code)
            ->where('warehouse', $warehouse)->whereIn('type', ['In-house', 'Consignment', 'Website Stocks'])->whereIn('status', ['Active', 'Partially Issued'])->sum('consumed_qty');

        return ($reserved_qty_for_website + $stock_reservation_qty) + $consumed_qty;
    }

    public function get_issued_qty($item_code, $warehouse){
        $total_issued = DB::connection('mysql')->table('tabStock Entry Detail')->where('docstatus', 0)->where('status', 'Issued')
            ->where('item_code', $item_code)->where('s_warehouse', $warehouse)->sum('qty');

        $total_issued += DB::connection('mysql')->table('tabAthena Transactions as at')
            ->join('tabPacking Slip as ps', 'ps.name', 'at.reference_parent')
            ->join('tabPacking Slip Item as psi', 'ps.name', 'ps.parent')
            ->join('tabDelivery Note as dr', 'ps.delivery_note', 'dr.name')
            ->whereIn('at.reference_type', ['Packing Slip', 'Picking Slip'])
            ->where('dr.docstatus', 0)->where('ps.docstatus', '<', 2)
            ->where('psi.status', 'Issued')
            ->where('at.item_code', $item_code)->where('at.source_warehouse', $warehouse)
            ->sum('at.issued_qty');

        return $total_issued;
    }

	public function delete_pending_material_transfer_for_manufacture($production_order, Request $request){
		DB::connection('mysql')->beginTransaction();
		try {
			if(!Auth::user()) {
				return response()->json(['error' => 1, 'message' => 'Session Expired. Please login to continue.']);
			}

			$production_order_details = DB::connection('mysql')->table('tabWork Order')->where('name', $production_order)->first();
			$now = Carbon::now();
			// get all pending stock entries based on item code production order
			$pending_stock_entries = DB::connection('mysql')->table('tabStock Entry as ste')
				->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
				->where('ste.docstatus', 0)->where('ste.work_order', $production_order)
				->where('sted.item_code', $request->item_code)->whereIn('ste.name', explode(',', $request->ste_names))
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

			$production_order_item = DB::connection('mysql')->table('tabWork Order Item')
				->where('parent', $production_order)->where('name', $request->production_order_item_id)
				->first();

			if ($production_order_item) {
				// delete returned item
				if ($production_order_item->transferred_qty > 0 && ($production_order_item->transferred_qty - $production_order_item->returned_qty) <= 0) {
					DB::connection('mysql')->table('tabWork Order Item')
						->where('parent', $production_order)->where('name', $production_order_item->name)
						->delete();
				}
			}

			// get all submitted stock entries based on item code production order
			$submitted_pending_stock_entries = DB::connection('mysql')->table('tabStock Entry as ste')
				->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
				->where('ste.docstatus', '<', 2)->where('ste.work_order', $production_order)
				->where('sted.item_code', $request->item_code)
				->where('ste.purpose', 'Material Transfer for Manufacture')->count();

			if($submitted_pending_stock_entries <= 0){
				// delete production order item
				if($production_order_item){
					if($production_order_item->item_alternative_for){
						$item_code_with_alternative = DB::connection('mysql')->table('tabWork Order Item')
							->where('parent', $production_order)->where('item_code', $production_order_item->item_alternative_for)
							->first();

						if($item_code_with_alternative){
							$required_qty = $item_code_with_alternative->required_qty + $production_order_item->required_qty;
							$remaining_qty = $item_code_with_alternative->required_qty - $item_code_with_alternative->transferred_qty;
							$remaining_qty = $production_order_item->required_qty + $remaining_qty;

							$st_entries = DB::connection('mysql')->table('tabStock Entry as ste')
								->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
								->where('ste.purpose', 'Material Transfer for Manufacture')->where('ste.docstatus', 0)
								->where('ste.work_order', $production_order)->where('sted.item_code', $production_order_item->item_alternative_for)
								->pluck('ste.name');

							DB::connection('mysql')->table('tabStock Entry Detail')
								->whereIn('parent', $st_entries)->where('item_code', $production_order_item->item_alternative_for)
								->update(['qty' => ($remaining_qty), 'transfer_qty' => ($remaining_qty)]);

							DB::connection('mysql')->table('tabWork Order Item')
								->where('parent', $production_order)->where('item_code', $production_order_item->item_alternative_for)
								->update(['required_qty' => $required_qty]);
						}
					}
				}

				$is_with_alternative_item = DB::connection('mysql')->table('tabWork Order Item')
					->where('parent', $production_order)->where('item_alternative_for', $production_order_item->item_code)
					->first();

				if (!$is_with_alternative_item) {
					$count_work_order_items = DB::connection('mysql')->table('tabWork Order Item')
						->where('parent', $production_order)->count();
					if ($count_work_order_items > 1) {
						DB::connection('mysql')->table('tabWork Order Item')
							->where('parent', $production_order)->where('item_code', $request->item_code)
							->delete();
					}
				}
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
			if(!Auth::user()) {
				return response()->json(['error' => 1, 'message' => 'Session Expired. Please login to continue.']);
			}

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
			->where('workstation_name', $workstation)->pluck('operation_id')->first();

		if(!$operation_id){
			return redirect()->back()->with('error', 'Workstation not found!');
		}

		$machines = DB::connection('mysql_mes')->table('machine')
			->where('operation_id', $operation_id)
			->orderBy('order_no', 'asc')->get();

        $conveyor_schedule_list = [];
        foreach($machines as $machine){

			$production_orders = $this->conveyor_assigned_production_orders($schedule_date, $machine->machine_code);

			$on_going_prod = DB::connection('mysql_mes')->table('job_ticket as j')
				->join('time_logs as t', 'j.job_ticket_id', 't.job_ticket_id')
				->where('t.status', 'In Progress')
				->whereIn('j.production_order', collect($production_orders)->pluck('production_order'))
				->pluck('j.production_order')->toArray();

            $conveyor_schedule_list[] = [
                'machine_code' => $machine->machine_code,
                'machine_name' => $machine->machine_name,
                'production_orders' => $production_orders,
				'wip_production_orders' => $on_going_prod
            ];
		}

		return view('tables.tbl_conveyor_schedule', compact('conveyor_schedule_list'));
	}

	public function conveyor_assigned_production_orders($schedule_date, $conveyor){
		// get scheduled production order against $scheduled_date
		$q = DB::connection('mysql_mes')->table('assembly_conveyor_assignment as aca')
			->join('production_order as po', 'aca.production_order', 'po.production_order')
			->whereNotIn('po.status', ['Cancelled', 'Feedbacked', 'Completed', 'Closed'])
			->whereDate('scheduled_date', $schedule_date)->where('machine_code', $conveyor)
			->select('po.production_order', 'po.sales_order', 'po.material_request', 'po.qty_to_manufacture', 'po.item_code', 'po.description', 'po.stock_uom', 'aca.order_no', 'po.customer', 'po.produced_qty', 'aca.scheduled_date', 'po.status', 'po.project', 'po.classification')
			->orderBy('aca.order_no', 'asc')->orderBy('aca.scheduled_date', 'asc');

		// get scheduled production order before $scheduled_date
		$q1 = DB::connection('mysql_mes')->table('assembly_conveyor_assignment as aca')
			->join('production_order as po', 'aca.production_order', 'po.production_order')
			->whereIn('po.status', ['In Progress', 'Not Started', 'Partially Feedbacked', 'Ready for Feedback'])
			->whereDate('scheduled_date', '<', $schedule_date)->where('machine_code', $conveyor)
			->select('po.production_order', 'po.sales_order', 'po.material_request', 'po.qty_to_manufacture', 'po.item_code', 'po.description', 'po.stock_uom', 'aca.order_no', 'po.customer', 'po.produced_qty', 'aca.scheduled_date', 'po.status', 'po.project', 'po.classification')
			->orderBy('aca.order_no', 'asc')->orderBy('aca.scheduled_date', 'asc');

		// get scheduled production order before $scheduled_date
		$q = DB::connection('mysql_mes')->table('assembly_conveyor_assignment as aca')
			->join('production_order as po', 'aca.production_order', 'po.production_order')
			->whereIn('po.status', ['Completed', 'Feedbacked'])
			->whereBetween('po.actual_end_date', [Carbon::parse($schedule_date)->startOfDay()->format('Y-m-d'), Carbon::parse($schedule_date)->endOfDay()->format('Y-m-d')])
			->whereDate('scheduled_date', '<', $schedule_date)->where('machine_code', $conveyor)
			->select('po.production_order', 'po.sales_order', 'po.material_request', 'po.qty_to_manufacture', 'po.item_code', 'po.description', 'po.stock_uom', 'aca.order_no', 'po.customer', 'po.produced_qty', 'aca.scheduled_date', 'po.status', 'po.project', 'po.classification')
			->orderBy('aca.order_no', 'asc')->orderBy('aca.scheduled_date', 'asc')
			->union($q)->union($q1)->get();

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
				'balance' => $row->qty_to_manufacture - $row->produced_qty,
				'classification' => $row->classification
			];
		}

		$wip_prods = collect($list)->filter(function ($value, $key) {
			return $value['status'] == 'In Progress';
		})->pluck('production_order')->toArray();

		// MES-1280 - Display production order in progress in multiple machines for assembly operator scheduling
		$wip_orders = DB::connection('mysql_mes')->table('assembly_conveyor_assignment as aca')
			->join('production_order as po', 'aca.production_order', 'po.production_order')
			->join('job_ticket as jt', 'jt.production_order', 'po.production_order')
			->join('time_logs as tl', 'tl.job_ticket_id', 'jt.job_ticket_id')
			->whereNotIn('po.status', ['Cancelled', 'Feedbacked', 'Completed', 'Closed'])
			->where('tl.status', 'In Progress')->where('tl.machine_code', $conveyor)
			->whereDate('scheduled_date', '<=', $schedule_date)
			->whereNotIn('po.production_order', $wip_prods)
			->select('po.production_order', 'po.sales_order', 'po.material_request', 'po.qty_to_manufacture', 'po.item_code', 'po.description', 'po.stock_uom', 'aca.order_no', 'po.customer', 'po.produced_qty', 'aca.scheduled_date', 'tl.status', 'po.project', 'po.classification')
			->groupBy('po.production_order', 'po.sales_order', 'po.material_request', 'po.qty_to_manufacture', 'po.item_code', 'po.description', 'po.stock_uom', 'aca.order_no', 'po.customer', 'po.produced_qty', 'aca.scheduled_date', 'tl.status', 'po.project', 'po.classification')
			->orderBy('aca.order_no', 'asc')->orderBy('aca.scheduled_date', 'asc')->get();

		if (count($wip_orders) > 0) {
			foreach ($wip_orders as $row) {
				$reference_no = ($row->sales_order) ? $row->sales_order : $row->material_request;
				$list[] = [
					'scheduled_date' => $row->scheduled_date,
					'production_order' => $row->production_order,
					'status' => $row->status,
					'customer' => $row->customer,
					'project' => $row->project,
					'reference_no' => $conveyor,
					'qty_to_manufacture' => $row->qty_to_manufacture,
					'item_code' => $row->item_code,
					'description' => $row->description,
					'stock_uom' => $row->stock_uom,
					'order_no' => $row->order_no,
					'good' => $row->produced_qty,
					'balance' => $row->qty_to_manufacture - $row->produced_qty,
					'classification' => $row->classification
				];
			}
		}

		return collect($list)->sortBy('order_no');
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
			return response()->json(['success' => 0, 'message' => 'Production Order ' . $request->production_order . ' not found.', 'reload_tbl' => $request->reload_tbl]);
		}
		$delivery_date =  Carbon::parse($request->delivery_date);
		$reschedule_date = Carbon::parse($request->reschedule_date);
		$planned_start_date = Carbon::parse($request->planned_start_date);
		if($reschedule_date->toDateTimeString() <= $delivery_date->toDateTimeString()){
			return response()->json(['success' => 0, 'message' => 'Rescheduled date must be greater than the current delivery date', 'reload_tbl' => $request->reload_tbl]);
		}
		if(!$production_order_details->planned_start_date){
			if($reschedule_date->toDateTimeString() < $planned_start_date->toDateTimeString()){
				return response()->json(['success' => 0, 'message' => 'Rescheduled date must be greater than the current production schedule date', 'reload_tbl' => $request->reload_tbl]);
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
					return response()->json(['success' => 3, 'message' => 'Unable to reschedule delivery date for '.$production_order_details->item_code.'. Item code doesnt exist in '.$production_order_details->sales_order.' and has been changed by Sales Personnel.', 'reload_tbl' => $request->reload_tbl]);			
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
				if(empty($get_sales_order_owner)){
					return response()->json(['success' => 3, 'message' => 'Unable to reschedule delivery date for '.$production_order_details->item_code.'. Item code doesnt exist in '.$production_order_details->sales_order.' and has been changed by Sales Personnel.', 'reload_tbl' => $request->reload_tbl]);
				}
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
				if($get_sales_order_owner->owner != "Administrator"){
					// Mail::to($get_sales_order_owner->owner)->send(new SendMail_New_DeliveryDate_Alert($email_data)); //data_to_be_inserted_in_mail_template
					// Mail::to("john.delacruz@fumaco.local")->send(new SendMail_New_DeliveryDate_Alert($email_data)); //data_to_be_inserted_in_mail_template
					// Mail::to("albert.gregorio@fumaco.local")->send(new SendMail_New_DeliveryDate_Alert($email_data)); //data_to_be_inserted_in_mail_template
				}

				DB::connection('mysql_mes')->table('delivery_date_reschedule_logs')->insert($resched_logs);// insert log in delivery schedule logs
				DB::connection('mysql_mes')->table('delivery_date')->where('parent_item_code', $production_order_details->item_code)->where('reference_no',$production_order_details->sales_order)->update($mes_data);//update the reschedule delivery date in delivery date table
			}
			//for MREQ
			if($production_order_details->material_request){
				$delivery_id=DB::connection('mysql_mes')->table('delivery_date')->where('parent_item_code', $production_order_details->item_code)->where('reference_no',$production_order_details->material_request)->first();// get the id from the delivery date table FOR MREQ refrerence
				if(empty($delivery_id)){
					return response()->json(['success' => 3, 'message' => 'Unable to reschedule delivery date for '.$production_order_details->item_code.'. Item code doesnt exist in '.$production_order_details->material_request.' and has been changed by Sales Personnel.', 'reload_tbl' => $request->reload_tbl]);
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
				if(empty($get_mreq_owner)){
					return response()->json(['success' => 3, 'message' => 'Unable to reschedule delivery date for '.$production_order_details->item_code.'. Because item code doesnt exist in '.$production_order_details->material_request.' and has been changed by Sales Personnel.', 'reload_tbl' => $request->reload_tbl]);
				}
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
				// if($get_mreq_owner->owner != "Administrator"){
				// 	Mail::to($get_mreq_owner->owner)->send(new SendMail_New_DeliveryDate_Alert($email_data)); //data_to_be_inserted_in_mail_template
				// 	Mail::to("john.delacruz@fumaco.local")->send(new SendMail_New_DeliveryDate_Alert($email_data)); //data_to_be_inserted_in_mail_template
				// 	Mail::to("albert.gregorio@fumaco.local")->send(new SendMail_New_DeliveryDate_Alert($email_data)); //data_to_be_inserted_in_mail_template
				// }

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
			DB::connection('mysql')->table('tabWork Order')
				->where('parent_item_code', $production_order_details->item_code)
				->where('sales_order_no',$production_order_details->sales_order)
				->update($production_order_data);	
		}
		if($production_order_details->material_request){
			DB::connection('mysql')->table('tabMaterial Request Item')
				->where('parent', $production_order_details->material_request)
				->where('item_code', $production_order_details->item_code)
				->update($material_request_data);
			DB::connection('mysql')->table('tabWork Order')
				->where('parent_item_code', $production_order_details->item_code)
				->where('material_request',$production_order_details->material_request)
				->update($production_order_data);
		}
		return response()->json(['success' => 1, 'message' => 'Production Order updated.', 'reload_tbl' => $request->reload_tbl]);	
	}

	public function production_schedule_monitoring_filters($operation, $schedule_date, Request $request){
		if($operation == 2){
			$production_orders = DB::connection('mysql_mes')->table('production_order as prod')
				->join('job_ticket as tsd','tsd.production_order','=','prod.production_order')
				->whereNotIn('prod.status', ['Cancelled', 'Closed'])
				->where('tsd.planned_start_date', $schedule_date)
				->where('tsd.workstation', 'Painting')
				->when($request->search_customer, function ($q) use ($request){
					return $q->where('customer', 'like', '%'.$request->search_customer.'%');
				})
				->when($request->search_reference, function ($q) use ($request){
					return $q->where(function ($x) use ($request){
						$x->where('sales_order', 'like', '%'.$request->search_reference.'%')->orWhere('material_request', 'like', '%'.$request->search_reference.'%');
					});
				})
				->when($request->search_parent, function ($q) use ($request){
					return $q->where('parent_item_code', 'like', '%'.$request->search_parent.'%');
				})
				->select('prod.sales_order', 'prod.material_request', 'prod.customer', 'prod.parent_item_code', 'tsd.sequence', 'tsd.planned_start_date as planned_start')
				->groupBy('prod.sales_order', 'prod.material_request', 'prod.customer', 'prod.parent_item_code', 'tsd.sequence', 'tsd.planned_start_date as planned_start')
				->orderBy('tsd.sequence','asc')
				->get();
		}else{
			$production_orders = DB::connection('mysql_mes')->table('production_order')
				->whereNotIn('status', ['Cancelled', 'Closed'])
				->where('operation_id', $operation)->whereRaw('feedback_qty < qty_to_manufacture')
				->when($request->search_customer, function ($q) use ($request){
					return $q->where('customer', 'like', '%'.$request->search_customer.'%');
				})
				->when($request->search_reference, function ($q) use ($request){
					return $q->where(function ($x) use ($request){
						$x->where('sales_order', 'like', '%'.$request->search_reference.'%')->orWhere('material_request', 'like', '%'.$request->search_reference.'%');
					});
				})
				->when($request->search_parent, function ($q) use ($request){
					return $q->where('parent_item_code', 'like', '%'.$request->search_parent.'%');
				})
				->get();
		}
		
		$customers = $refs = $parent = [];
		foreach ($production_orders as $row) {
			$reference_no = ($row->sales_order) ? $row->sales_order : $row->material_request;
			$customers[] = $row->customer;
			$refs[] = $reference_no;
			$parent[] = $row->parent_item_code;
		}

		$customers = collect($customers)->push('%All')->sort()->toArray();
		$refs = collect($refs)->push('%All')->sort()->toArray();
		$parent = collect($parent)->push('%All')->sort()->toArray();

		$customers = collect(array_unique(array_filter($customers)))->map(function ($q){
			return [
				'id' => $q == '%All' ? 'Select All' : $q,
				'text' => $q == '%All' ? 'Select All' : $q
			];
		})->values()->all();

		$refs = collect(array_unique(array_filter($refs)))->map(function ($q){
			return [
				'id' => $q == '%All' ? 'Select All' : $q,
				'text' => $q == '%All' ? 'Select All' : $q
			];
		})->values()->all();

		$parent = collect(array_unique(array_filter($parent)))->map(function ($q){
			return [
				'id' => $q == '%All' ? 'Select All' : $q,
				'text' => $q == '%All' ? 'Select All' : $q
			];
		})->values()->all();

		return response()->json([
			'customers' => $customers,
			'reference_nos' => $refs,
			'parent' => $parent
		]);
	}

	public function production_schedule_monitoring($operation, $schedule_date, Request $request){
		$permissions = $this->get_user_permitted_operation();

		$operation_details = DB::connection('mysql_mes')->table('operation')
			->where('operation_id', $operation)->first();

		if($operation < 1){
			$operation_details = DB::connection('mysql_mes')->table('operation')
				->where('operation_name', 'Painting')->first();
		}

		$workstation_list = DB::connection('mysql_mes')->table('workstation')
            ->where('operation_id', $operation)
            ->select('workstation_name','order_no','workstation_id')
			->orderBy('order_no','desc')->get();

		$machines = DB::connection('mysql_mes')->table('machine')->where('operation_id', $operation_details->operation_id)->orderBy('order_no', 'asc')->get();
		
		if ($request->ajax()) {
			if($request->machines){
				$request_data = $request->all();
				$production_machine_board = $this->production_assembly_machine_board($operation, $schedule_date, $request_data);
				return view('tables.tbl_production_schedule_monitoring_machines', compact('schedule_date', 'production_machine_board', 'workstation_list', 'operation_details', 'permissions'));
			}

			$start = Carbon::parse($schedule_date)->startOfDay();
			$end = Carbon::parse($schedule_date)->endOfDay();
			
			// get schedule production order against $schedule_date
			$scheduled_production = DB::connection('mysql_mes')->table('production_order')
				->whereNotIn('status', ['Cancelled', 'Feedbacked', 'Completed', 'Closed'])->whereDate('planned_start_date', $start)
				->where('operation_id', $operation)->whereRaw('feedback_qty < qty_to_manufacture')
				->when($request->customer && $request->customer != 'Select All', function ($q) use ($request){
					return $q->where('customer', $request->customer);
				})
				->when($request->reference && $request->reference != 'Select All', function ($q) use ($request){
					return $q->where(function ($x) use ($request){
						$x->where('sales_order', $request->reference)->orWhere('material_request', $request->reference);
					});
				})
				->when($request->parent && $request->parent != 'Select All', function ($q) use ($request){
					return $q->where('parent_item_code', $request->parent);
				});

			// get pending backlogs before $schedule_date
			$pending_backlogs = DB::connection('mysql_mes')->table('production_order')
				->whereIn('status', ['In Progress', 'Not Started', 'Partially Feedbacked', 'Ready for Feedback'])
				->whereDate('planned_start_date', '<', $schedule_date)
				->where('operation_id', $operation)->whereRaw('feedback_qty < qty_to_manufacture')
				->when($request->customer && $request->customer != 'Select All', function ($q) use ($request){
					return $q->where('customer', $request->customer);
				})
				->when($request->reference && $request->reference != 'Select All', function ($q) use ($request){
					return $q->where(function ($x) use ($request){
						$x->where('sales_order', $request->reference)->orWhere('material_request', $request->reference);
					});
				})
				->when($request->parent && $request->parent != 'Select All', function ($q) use ($request){
					return $q->where('parent_item_code', $request->parent);
				});

			$pending_count = Clone $pending_backlogs;
			$backlogs = $pending_count->count();

			// get completed backlogs before $schedule_date based on production order actual_end_date
			$completed_production_orders = DB::connection('mysql_mes')->table('production_order')
				->whereIn('status', ['Completed', 'Feedbacked'])->whereBetween('actual_end_date', [$start, $end])
				->whereDate('planned_start_date', '<', $schedule_date)
				->where('operation_id', $operation)->whereRaw('feedback_qty < qty_to_manufacture')
				->when($request->customer && $request->customer != 'Select All', function ($q) use ($request){
					return $q->where('customer', $request->customer);
				})
				->when($request->reference && $request->reference != 'Select All', function ($q) use ($request){
					return $q->where(function ($x) use ($request){
						$x->where('sales_order', $request->reference)->orWhere('material_request', $request->reference);
					});
				})
				->when($request->parent && $request->parent != 'Select All', function ($q) use ($request){
					return $q->where('parent_item_code', $request->parent);
				})
				->union($pending_backlogs)->union($scheduled_production)->get();

			$production_orders = [];
			foreach ($completed_production_orders as $row) {
				$reference_no = ($row->sales_order) ? $row->sales_order : $row->material_request;
				// get total rejects from all workstations
				$rejects = DB::connection('mysql_mes')->table('job_ticket as jt')
					->join('time_logs as tl', 'tl.job_ticket_id', 'jt.job_ticket_id')
					->where('jt.production_order', $row->production_order)->sum('jt.reject');

				$delivery_details = DB::connection('mysql_mes')->table('delivery_date')
					->where('reference_no', $reference_no)->where('parent_item_code', $row->parent_item_code)
					->first();

				if ($delivery_details) {
					$delivery_date = ($delivery_details->rescheduled_delivery_date) ? $delivery_details->rescheduled_delivery_date : $delivery_details->delivery_date;
				}else{
					$delivery_date = $row->delivery_date;
				}

				$is_backlog = (Carbon::parse($row->planned_start_date)->format('Y-m-d') < Carbon::now()->format('Y-m-d')) ? 1 : 0;
				$production_orders[$row->planned_start_date][] = [
					'production_order' => $row->production_order,
					'planned_start_date' => $row->planned_start_date,
					'actual_start_date' => (!in_array($row->status, ['Not Started', 'Pending'])) ? Carbon::parse($row->actual_start_date)->format('Y-m-d h:i:A') : null,
					'delivery_date' => $delivery_date,
					'parent_item_code' => $row->parent_item_code,
					'reference_no' => $reference_no,
					'customer' => $row->customer,
					'item_code' => $row->item_code,
					'description' => $row->description,
					'qty_to_manufacture' => $row->qty_to_manufacture,
					'stock_uom' => $row->stock_uom,
					'produced_qty' => $row->produced_qty,
					'feedback_qty' => $row->feedback_qty,
					'balance_qty' => ($row->qty_to_manufacture - $row->feedback_qty),
					'notes' => $row->notes,
					'rejects' => $rejects,
					'is_backlog' => $is_backlog,
					'status' => $row->status
				];
			}

			$planned_start_dates = collect(array_keys($production_orders))->sort()->reverse()->values()->all();
			return view('tables.tbl_production_schedule_monitoring', compact('production_orders', 'backlogs', 'planned_start_dates'));
		}

		// $production_machine_board = $this->production_assembly_machine_board($operation, $schedule_date);

        return view('production_schedule_monitoring', compact('schedule_date', 'workstation_list', 'operation_details', 'permissions', 'machines'));
	}

	public function save_machine_order(Request $request){
		DB::connection('mysql_mes')->beginTransaction();
		try {
			$machines = $request->machine_list;
			foreach($machines as $order => $machine){
				DB::connection('mysql_mes')->table('machine')->where('machine_id', $machine)->update([
					'order_no' => $order,
					'last_modified_at' => Carbon::now()->toDateTimeString(),
					'last_modified_by' => Auth::user()->email
				]);
			}

			DB::connection('mysql_mes')->commit();
			return response()->json(['success' => 1, 'message' => 'Machine Order Updated.']);
		} catch (\Throwable $th) {
			DB::connection('mysql_mes')->rollBack();
			return response()->json(['success' => 0, 'message' => 'An error occured. Please try again']);
		}
	}

	public function production_assembly_machine_board($operation_id, $scheduled_date, $request_data = []){
		$assigned_production = DB::connection('mysql_mes')->table('assembly_conveyor_assignment')->get();

        $unassigned_production = DB::connection('mysql_mes')->table('production_order')
            ->where('operation_id', $operation_id)->whereNotIn('status', ['Cancelled', 'Closed', 'Feedbacked', 'Completed'])
            ->whereNotIn('production_order', array_column($assigned_production->toArray(), 'production_order'))
			->when(isset($request_data['production_order']) && $request_data['production_order'], function ($q) use ($request_data){
				return $q->where('production_order', 'like', '%'.$request_data['production_order'].'%');
			})
			->whereDate('planned_start_date', '<=', $scheduled_date)->get();

        $machines = DB::connection('mysql_mes')->table('machine')
            ->where('operation_id', $operation_id)->orderBy('order_no', 'asc')->get();

        $start = Carbon::parse($scheduled_date)->startOfDay();
        $end = Carbon::parse($scheduled_date)->endOfDay();

        $assigned_production_orders = [];
        foreach($machines as $machine){
            // get scheduled production order against $scheduled_date
            $q = DB::connection('mysql_mes')->table('assembly_conveyor_assignment as aca')
                ->join('production_order as po', 'aca.production_order', 'po.production_order')
                ->whereNotIn('po.status', ['Cancelled', 'Feedbacked', 'Completed', 'Closed'])
                ->whereDate('scheduled_date', $scheduled_date)
				->where('machine_code', $machine->machine_code)
				->when(isset($request_data['production_order']) && $request_data['production_order'], function ($q) use ($request_data){
					return $q->where('po.production_order', 'like', '%'.$request_data['production_order'].'%');
				})
                ->select('aca.*', 'po.sales_order', 'po.material_request', 'po.sales_order', 'po.material_request', 'po.qty_to_manufacture', 'po.item_code', 'po.stock_uom', 'po.status', 'po.description', 'po.classification', 'po.customer')
                ->orderBy('aca.order_no', 'asc')->orderBy('aca.scheduled_date', 'asc');

            // get scheduled production order before $scheduled_date
            $q1 = DB::connection('mysql_mes')->table('assembly_conveyor_assignment as aca')
                ->join('production_order as po', 'aca.production_order', 'po.production_order')
                ->whereIn('po.status', ['In Progress', 'Not Started', 'Partially Feedbacked', 'Ready for Feedback'])
                ->whereDate('scheduled_date', '<', $scheduled_date)->where('machine_code', $machine->machine_code)
				->when(isset($request_data['production_order']) && $request_data['production_order'], function ($q) use ($request_data){
					return $q->where('po.production_order', 'like', '%'.$request_data['production_order'].'%');
				})
                ->select('aca.*', 'po.sales_order', 'po.material_request', 'po.sales_order', 'po.material_request', 'po.qty_to_manufacture', 'po.item_code', 'po.stock_uom', 'po.status', 'po.description', 'po.classification', 'po.customer')
                ->orderBy('aca.order_no', 'asc')->orderBy('aca.scheduled_date', 'asc');

            // get scheduled production order before $scheduled_date
            $assigned_production_q = DB::connection('mysql_mes')->table('assembly_conveyor_assignment as aca')
                ->join('production_order as po', 'aca.production_order', 'po.production_order')
                ->whereIn('po.status', ['Completed', 'Feedbacked'])
                ->whereBetween('po.actual_end_date', [$start, $end])
                ->whereDate('scheduled_date', '<', $scheduled_date)->where('machine_code', $machine->machine_code)
				->when(isset($request_data['production_order']) && $request_data['production_order'], function ($q) use ($request_data){
					return $q->where('po.production_order', 'like', '%'.$request_data['production_order'].'%');
				})
                ->select('aca.*', 'po.sales_order', 'po.material_request', 'po.sales_order', 'po.material_request', 'po.qty_to_manufacture', 'po.item_code', 'po.stock_uom', 'po.status', 'po.description', 'po.classification', 'po.customer')
                ->orderBy('aca.order_no', 'asc')->orderBy('aca.scheduled_date', 'asc')
                ->union($q)->union($q1)->get();

			$on_going_prod = DB::connection('mysql_mes')->table('job_ticket as j')
				->join('time_logs as t', 'j.job_ticket_id', 't.job_ticket_id')
				->where('t.status', 'In Progress')
				->whereIn('j.production_order', collect($assigned_production_q)->pluck('production_order'))
				->pluck('j.production_order')->toArray();

            $assigned_production_orders[] = [
                'machine_code' => $machine->machine_code,
                'machine_name' => $machine->machine_name,
                'production_orders' => collect($assigned_production_q)->sortBy('order_no'),
				'on_going_production_orders' => $on_going_prod
            ];
		}

		return [
			'assigned_production_orders' => $assigned_production_orders,
			'unassigned_production' => $unassigned_production
		];
	}

	public function production_fabrication_machine_board($workstation_id, $scheduled_date){
		$workstation_details = DB::connection('mysql_mes')->table('workstation')->where('workstation_id', $workstation_id)->first();
		// get workstation processes
		$workstation_process = DB::connection('mysql_mes')->table('process_assignment AS pa')
			->join('process AS p', 'p.process_id','pa.process_id')->where('pa.workstation_id', $workstation_id)
			->orderBy('p.process_id', 'asc')->groupBy('p.process_id', 'p.process_name')->pluck('p.process_name', 'p.process_id');

		$start = Carbon::now()->startOfDay()->toDateTimeString();
		$end = Carbon::now()->endOfDay()->toDateTimeString();

        $data = [];
        foreach ($workstation_process as $process_id => $process_name) {
			// get scheduled job ticket against $scheduled_date
			$scheduled_job_ticket = DB::connection('mysql_mes')->table('job_ticket as jt')
				->leftJoin('time_logs as tl', 'jt.job_ticket_id', 'tl.job_ticket_id')
				->join('production_order as po', 'jt.production_order', 'po.production_order')
				->where('jt.workstation', $workstation_details->workstation_name)
				->where('jt.process_id', $process_id)->where('po.is_scheduled', 1)
				->whereDate('jt.planned_start_date','<=', $scheduled_date)
				->whereNotIn('jt.status', ['Completed'])
				->select('po.production_order', 'po.sales_order', 'po.material_request', 'po.description', 'tl.status', 'po.qty_to_manufacture', 'po.stock_uom', 'po.customer', 'jt.planned_start_date', 'jt.completed_qty', 'po.order_no', 'po.item_code', 'jt.workstation', 'tl.machine_name', 'tl.from_time', 'tl.to_time', 'tl.machine_code', 'po.parent_item_code', 'tl.operator_name', 'tl.duration', 'tl.cycle_time_in_seconds', 'jt.remarks', 'tl.time_log_id', 'jt.job_ticket_id', 'jt.status as job_ticket_status');

			// get todays completed job ticket
			$completed_job_ticket = DB::connection('mysql_mes')->table('job_ticket as jt')
				->leftJoin('time_logs as tl', 'jt.job_ticket_id', 'tl.job_ticket_id')
				->join('production_order as po', 'jt.production_order', 'po.production_order')
				->where('jt.workstation', $workstation_details->workstation_name)
				->where('jt.process_id', $process_id)->where('po.is_scheduled', 1)
				->whereDate('jt.planned_start_date','<=', $scheduled_date)
				->whereBetween('jt.actual_end_date', [$start, $end])
				->whereIn('jt.status', ['Completed'])
				->select('po.production_order', 'po.sales_order', 'po.material_request', 'po.description', 'tl.status', 'po.qty_to_manufacture', 'po.stock_uom', 'po.customer', 'jt.planned_start_date', 'jt.completed_qty', 'po.order_no', 'po.item_code', 'jt.workstation', 'tl.machine_name', 'tl.from_time', 'tl.to_time', 'tl.machine_code', 'po.parent_item_code', 'tl.operator_name', 'tl.duration', 'tl.cycle_time_in_seconds', 'jt.remarks', 'tl.time_log_id', 'jt.job_ticket_id', 'jt.status as job_ticket_status');

			$query = $scheduled_job_ticket->unionAll($completed_job_ticket)->orderByRaw("FIELD(status, 'In Progress', 'Pending', null, 'Completed') ASC")->get();

			$tasks = [];
			foreach ($query as $task) {
				$delivery_details = DB::connection('mysql_mes')->table('delivery_date')
					->where('reference_no', ($task->sales_order) ? $task->sales_order : $task->material_request)
					->where('parent_item_code', $task->parent_item_code)->first();

				$delivery_date = ($delivery_details) ? $delivery_details->delivery_date : null;

				$duration_in_mins = number_format((float)($task->duration * 60), 2, '.', '') . ' min(s)';
				$cycle_time_in_mins = number_format((float)($task->cycle_time_in_seconds / 60), 2, '.', '') . ' min(s)';

				$tasks[] = [
					'timelog_id' => $task->time_log_id,
					'job_ticket_id' => $task->job_ticket_id,
					'job_ticket_status' => $task->job_ticket_status,
					'production_order' => $task->production_order,
					'workstation' => $task->workstation,
					'workstation_id' => $workstation_details->workstation_id,
					'planned_start_date' => Carbon::parse($task->planned_start_date)->format('M-d-Y'),
					'sales_order' => $task->sales_order,
					'material_request' => $task->material_request,
					'delivery_date' => Carbon::parse($delivery_date)->format('M-d-Y'),
					'customer' => $task->customer,
					'item_code' => $task->item_code,
					'description' => $task->description,
					'qty_to_manufacture' => $task->qty_to_manufacture,
					'completed_qty' => $task->completed_qty,
					'stock_uom' => $task->stock_uom,
					'machine_code' => $task->machine_code,
					'machine_name' => $task->machine_name,
					'from_time' => Carbon::parse($task->from_time)->format('M-d-Y h:i:s A'),
					'to_time' => Carbon::parse($task->to_time)->format('M-d-Y h:i:s A'),
					'duration_in_mins' => $duration_in_mins,
					'cycle_time_in_mins' => $cycle_time_in_mins,
					'operator_name' => $task->operator_name,
					'status' => $task->status,
					'order_no' => $task->order_no,
					'remarks' => $task->remarks
				];
			}

			$data[] = [
				'process_id' => $process_id,
				'process_name' => $process_name,
				'tasks' => $tasks,
				'task_count' => count($tasks),
              	'total_qty' => collect($tasks)->sum('qty_to_manufacture'),
			];
		}

		return view('tables.tbl_production_machine_schedules_board', compact('data'));
	}
	public function get_tbl_default_shift_sched(Request $request){
		$date= "2021-02-05";
		$operation_id= 1;
		$shift_sched = $this->get_prod_shift_sched($request->date, $request->operation);
		return view('tables.tbl_default_shift_sched', compact('shift_sched'));
	}
	// update production order rescheduled date (erp) and MES
	public function calendar_update_rescheduled_delivery_date(Request $request){
		$now = Carbon::now();
		$production_order = explode(',', $request->production_order);
		// $delivery_date =  Carbon::parse($request->delivery_date);
		$reschedule_date = Carbon::parse($request->reschedule_date);
		$planned_start_date = Carbon::parse($request->planned_start_date);
		if($planned_start_date->toDateTimeString() > $reschedule_date->toDateTimeString()){
			return response()->json(['success' => 0, 'message' => 'Rescheduled date must be greater than the current delivery date', 'reload_tbl' => $request->reload_tbl]);
		}
		foreach($production_order as $n => $pro){
			$production_order_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $pro)->first();
			// update production order & sales order rescheduled delivery date & late delivery reason
			if($planned_start_date->toDateTimeString() <= $reschedule_date->toDateTimeString()){
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
						return response()->json(['success' => 3, 'message' => 'Unable to reschedule delivery date for '.$production_order_details->item_code.'. Item code doesnt exist in '.$production_order_details->sales_order.' and has been changed by Sales Personnel.', 'reload_tbl' => $request->reload_tbl]);			
					}
					$data=explode(',',$request->reason_id);
					$datas= ">>".Carbon::parse($reschedule_date)->format('Y-m-d').'<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$data[1]."-".$request->remarks."<br>".$request->historylogs[$n];//Timeline_log for remarks(delivery Reason) in ERP
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
					if(empty($get_sales_order_owner)){
						return response()->json(['success' => 3, 'message' => 'Unable to reschedule delivery date for '.$production_order_details->item_code.'. Item code doesnt exist in '.$production_order_details->sales_order.' and has been changed by Sales Personnel.', 'reload_tbl' => $request->reload_tbl]);
					}
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
					if($get_sales_order_owner->owner != "Administrator"){
						Mail::to($get_sales_order_owner->owner)->send(new SendMail_New_DeliveryDate_Alert($email_data)); //data_to_be_inserted_in_mail_template
						Mail::to("john.delacruz@fumaco.local")->send(new SendMail_New_DeliveryDate_Alert($email_data)); //data_to_be_inserted_in_mail_template
						Mail::to("albert.gregorio@fumaco.local")->send(new SendMail_New_DeliveryDate_Alert($email_data)); //data_to_be_inserted_in_mail_template
					}				
					DB::connection('mysql_mes')->table('delivery_date_reschedule_logs')->insert($resched_logs);// insert log in delivery schedule logs
					DB::connection('mysql_mes')->table('delivery_date')->where('parent_item_code', $production_order_details->item_code)->where('reference_no',$production_order_details->sales_order)->update($mes_data);//update the reschedule delivery date in delivery date table
				}
				//for MREQ
				if($production_order_details->material_request){
					$delivery_id=DB::connection('mysql_mes')->table('delivery_date')->where('parent_item_code', $production_order_details->item_code)->where('reference_no',$production_order_details->material_request)->first();// get the id from the delivery date table FOR MREQ refrerence
					if(empty($delivery_id)){
						return response()->json(['success' => 3, 'message' => 'Unable to reschedule delivery date for '.$production_order_details->item_code.'. Item code doesnt exist in '.$production_order_details->material_request.' and has been changed by Sales Personnel.', 'reload_tbl' => $request->reload_tbl]);
					}
					$data=explode(',',$request->reason_id);
					$datas= ">>".Carbon::parse($reschedule_date)->format('Y-m-d').'<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$data[1]."-".$request->remarks."<br>".$request->historylogs[$n];//Timeline_log for remarks(delivery Reason) in ERP
					
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
					if(empty($get_mreq_owner)){
						return response()->json(['success' => 3, 'message' => 'Unable to reschedule delivery date for '.$production_order_details->item_code.'. Because item code doesnt exist in '.$production_order_details->material_request.' and has been changed by Sales Personnel.', 'reload_tbl' => $request->reload_tbl]);
					}
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
						Mail::to($get_mreq_owner->owner)->send(new SendMail_New_DeliveryDate_Alert($email_data)); //data_to_be_inserted_in_mail_template
						Mail::to("john.delacruz@fumaco.local")->send(new SendMail_New_DeliveryDate_Alert($email_data)); //data_to_be_inserted_in_mail_template
						Mail::to("albert.gregorio@fumaco.local")->send(new SendMail_New_DeliveryDate_Alert($email_data)); //data_to_be_inserted_in_mail_template
					}
					DB::connection('mysql_mes')->table('delivery_date_reschedule_logs')->insert($resched_logs);// insert log in delivery schedule logs
					DB::connection('mysql_mes')->table('delivery_date')->where('parent_item_code', $production_order_details->item_code)->where('reference_no',$production_order_details->material_request)->update($mes_data);
				}
			}
			// if schedued in less than the current delivery date (for validation)
			if($planned_start_date->toDateTimeString() > $reschedule_date->toDateTimeString()){
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
				DB::connection('mysql')->table('tabWork Order')
					->where('parent_item_code', $production_order_details->item_code)
					->where('sales_order_no',$production_order_details->sales_order)
					->update($production_order_data);	
			}
			if($production_order_details->material_request){
				DB::connection('mysql')->table('tabMaterial Request Item')
					->where('parent', $production_order_details->material_request)
					->where('item_code', $production_order_details->item_code)
					->update($material_request_data);
				DB::connection('mysql')->table('tabWork Order')
					->where('parent_item_code', $production_order_details->item_code)
					->where('material_request',$production_order_details->material_request)
					->update($production_order_data);
			}
		}
		return response()->json(['success' => 1, 'message' => 'Production Order updated.', 'reload_tbl' => $request->reload_tbl]);	
	}

	public function get_machine_status_per_operation(Request $request, $operation_id){
		$machine_list = DB::connection('mysql_mes')->table('machine')
			->where('operation_id', $operation_id)->pluck('machine_name', 'machine_code')->toArray();

		if($operation_id == 3){
			$on_queue_query = DB::connection('mysql_mes')->table('production_order as po')
				->join('job_ticket as jt', 'jt.production_order', 'po.production_order')
				->join('assembly_conveyor_assignment as aca', 'aca.production_order', 'po.production_order')
				->when($request->scheduled_date, function ($query) use ($request) {
					return $query->where('aca.scheduled_date', $request->scheduled_date);
				})
				->whereIn('aca.machine_code', array_keys($machine_list))
				->whereNotIn('po.status', ['Completed', 'Cancelled', 'Closed'])
				->where('jt.status', 'Pending')
				->select('aca.machine_code', DB::raw('(po.qty_to_manufacture - jt.completed_qty) as pending_qty'))
				->get();
		}else{
			$on_queue_query = DB::connection('mysql_mes')->table('production_order as po')
				->join('job_ticket as jt', 'jt.production_order', 'po.production_order')
				->join('process_assignment as pa', 'pa.process_id', 'jt.process_id')
				->join('machine as m', 'm.machine_id', 'pa.machine_id')
				->when($request->scheduled_date, function ($query) use ($request) {
					return $query->where('jt.planned_start_date', $request->scheduled_date);
				})
				->whereIn('m.machine_code', array_keys($machine_list))
				->whereNotIn('po.status', ['Completed', 'Cancelled', 'Closed'])
				->where('jt.status', 'Pending')
				->select('m.machine_code', DB::raw('(po.qty_to_manufacture - jt.completed_qty) as pending_qty'))
				->get();
		}

		$logs = DB::connection('mysql_mes')->table('production_order as po')
			->join('job_ticket as jt', 'jt.production_order', 'po.production_order')
			->join('time_logs as tl', 'jt.job_ticket_id', 'tl.job_ticket_id')
			->whereIn('tl.machine_code', array_keys($machine_list))
			->where('jt.status', '!=', 'Completed')->where('tl.status', 'In Progress')
			->select('tl.machine_code', DB::raw('(po.qty_to_manufacture - jt.completed_qty) as wip_qty'));

		$logs = DB::connection('mysql_mes')->table('production_order as po')
			->join('job_ticket as jt', 'jt.production_order', 'po.production_order')
			->join('spotwelding_qty as sq', 'jt.job_ticket_id', 'sq.job_ticket_id')
			->whereIn('sq.machine_code', array_keys($machine_list))
			->where('jt.status', '!=', 'Completed')->where('sq.status', 'In Progress')
			->select('sq.machine_code', DB::raw('(po.qty_to_manufacture - jt.completed_qty) as wip_qty'))
			->union($logs)->get();

		$result = [];
		foreach($machine_list as $machine_code => $machine_name){
			$time_logs = collect($logs)->filter(function ($value, $key) use ($machine_code) {
				return $value->machine_code == $machine_code;
			});

			$on_queue_arr = collect($on_queue_query)->filter(function ($value, $key) use ($machine_code) {
				return $value->machine_code == $machine_code;
			});

			$on_queue = collect($on_queue_arr)->sum('pending_qty');
			$on_going = collect($time_logs)->sum('wip_qty');

			$result[] = [
				'machine_code' => $machine_code,
				'machine_name' => $machine_name,
				'on_queue' => number_format($on_queue),
				'on_going' => number_format($on_going),
				'status' => ($on_going > 0) ? 'Active' : 'Idle'
			];
		}

		return view('tables.tbl_machine_status_per_operation', compact('result'));
	}

	public function maintenance_schedules_per_operation($operation_id){
		$unplanned = DB::connection('mysql_mes')->table('machine_breakdown as mb')
			->join('machine as m', 'm.machine_code', 'mb.machine_id')
			->where('m.operation_id', $operation_id)
			->where('mb.status', '!=', 'Completed')->select('m.machine_code', 'mb.date_reported', 'mb.type', 'm.machine_name')->get();

		$breakdown_count = collect($unplanned)->where('type', 'Breakdown')->count();
		$corrective_count = collect($unplanned)->where('type', 'Corrective')->count();
		$preventive_count = 0;

		$maintenance_count = [
			'breakdown' => $breakdown_count,
			'corrective' => $corrective_count,
			'preventive' => $preventive_count,
		];

		return view('tables.tbl_maintenance_schedule_per_operation', compact('maintenance_count', 'unplanned'));
	}

	public function sendFeedbackEmail(Request $request) {
		try {
			$production_order = $request->production_order;
			$completed_qty = $request->fg_completed_qty;
			$reference_ste = $request->stock_entry;

			$production_order_details = DB::connection('mysql_mes')->table('production_order')
				->where('production_order', $production_order)->first();
			
			if ($production_order_details) {
				$now = Carbon::now();
				$data = array(
					'posting_date' => $now->format('Y-m-d'),
					'posting_time' => $now->format('H:i:s'),
					'ste' => $reference_ste,
					'sales_order_no'=> $production_order_details->sales_order,
					'mreq' => $production_order_details->material_request,
					'item_code' => $production_order_details->item_code,
					'item_name' => $production_order_details->description,
					'customer' => $production_order_details->customer,
					'feedbacked_by' => Auth::user()->email,
					'completed_qty' => $completed_qty, 
					'uom' => $production_order_details->stock_uom
				);
				
				$recipients = DB::connection('mysql_mes')->table('email_trans_recipient')
					->where('email_trans', "Feedbacking")->where('email', 'like','%@fumaco.local%')
					->distinct()->pluck('email');

				if($production_order_details->parent_item_code == $production_order_details->sub_parent_item_code && $production_order_details->sub_parent_item_code == $production_order_details->item_code){
					if(count($recipients) > 0){
						foreach ($recipients as $recipient) {
							Mail::to($recipient)->send(new SendMail_feedbacking($data));
						}
					}
				}
			}

			return response()->json(['status' => 1, 'message' => 'Feedback email notification has been sent.']);
		} catch (Exception $e) {
			return response()->json(['status' => 0, 'message' => 'Warning! Feedback email notification sending failed.']);
		}
	}

	public function checkWorkOrderItemQty() {
		$query = DB::connection('mysql')->table('tabWork Order as wo')
			->join('tabWork Order Item as woi', 'wo.name', 'woi.parent')
			->where('wo.status', 'Completed')
			->where(function($q) {
				$q->whereRaw('woi.required_qty > woi.transferred_qty')
					->orWhereRaw('woi.required_qty > woi.consumed_qty');
			})
			->select('woi.required_qty', 'woi.transferred_qty', 'woi.consumed_qty', 'wo.name', 'wo.production_item', 'wo.qty', 'wo.material_transferred_for_manufacturing', 'wo.produced_qty', 'wo.creation', 'wo.status', 'woi.item_code')
			->orderBy('wo.creation', 'desc')
			->paginate(100);

		$query_grouped = collect($query->items())->groupBy('name');

		$permissions = $this->get_user_permitted_operation();

		return view('reports.inaccurate_work_order_item_qty', compact('query', 'query_grouped', 'permissions'));
    }

	public function completedSoWithPendingProduction() {
		$query = DB::connection('mysql')->table('tabWork Order as wo')
			->join('tabSales Order as so', 'so.name', 'wo.sales_order_no')->where('wo.docstatus', 1)->where('so.docstatus', 1)
			->where('so.per_delivered', 100)->whereNotIn('wo.status', ['Completed', 'Stopped'])
			->whereRaw('wo.produced_qty < wo.qty')->where('wo.name', 'like', '%prom%')
			->select('so.name', 'so.customer', 'wo.name as production_order', 'so.status as so_status', 'wo.production_item', 'wo.qty', 'wo.status as wo_status', 'wo.creation')
			->orderBy('wo.creation', 'desc')->paginate(100);

		$query_grouped = collect($query->items())->groupBy('name');

		$permissions = $this->get_user_permitted_operation();

		return view('reports.completed_so_with_pending_po', compact('query', 'query_grouped', 'permissions'));
	}

	public function completedMreqWithPendingProduction() {
		$query = DB::connection('mysql')->table('tabWork Order as wo')
			->join('tabMaterial Request as mreq', 'mreq.name', 'wo.material_request')->where('wo.docstatus', 1)->where('mreq.docstatus', 1)
			->where('mreq.per_ordered', 100)->whereNotIn('wo.status', ['Completed', 'Stopped'])
			->select('mreq.name', 'mreq.customer', 'wo.name as production_order', 'mreq.status as mreq_status', 'wo.production_item', 'wo.qty', 'wo.status as wo_status', 'wo.creation')
			->orderBy('wo.creation', 'desc')->paginate(100);

		$query_grouped = collect($query->items())->groupBy('name');

		$permissions = $this->get_user_permitted_operation();

		return view('reports.completed_mreq_with_pending_po', compact('query', 'query_grouped', 'permissions'));
	}

	public function inaccurateProductionTransferredQtyWithWithdrawals() {
		$query = DB::connection('mysql')->table('tabWork Order as wo')->join('tabStock Entry as ste', 'wo.name', 'ste.work_order')->where('wo.docstatus', 1)
			->where('ste.docstatus', 1)->where('ste.purpose', ['Material Transfer for Manufacture', 'Material Transfer'])
			->whereRaw('wo.material_transferred_for_manufacturing != wo.qty')->whereNotIn('wo.status', ['Not Started'])
			->select('wo.creation', 'wo.name as production_order', 'wo.production_item', 'wo.qty', 'wo.material_transferred_for_manufacturing', 'wo.produced_qty', 'wo.status', 'ste.name as stock_entry', 'ste.purpose')
			->orderBy('wo.creation', 'desc')->paginate(100);

		$query_grouped = collect($query->items())->groupBy('production_order');

		$permissions = $this->get_user_permitted_operation();

		return view('reports.production_inaccurate_material_transferred', compact('query', 'query_grouped', 'permissions'));
	}

	public function timelogOutputVsProducedQty() {
		$production_query = DB::connection('mysql_mes')->table('production_order')
			->whereIn('status', ['In Process', 'Completed'])->whereRaw('qty_to_manufacture > produced_qty')
			->select('production_order', 'item_code', 'qty_to_manufacture', 'produced_qty', 'feedback_qty', 'status', 'created_at')
			->orderBy('created_at', 'desc')
			->paginate(100);

		$production_orders = array_column($production_query->items(), 'production_order');

		$job_ticket_query = DB::connection('mysql_mes')->table('job_ticket')->whereIn('production_order', $production_orders)
			->select('production_order', 'workstation', 'process_id', 'status', 'good', 'completed_qty', 'job_ticket_id', 'remarks')->get();

		$job_tickets = array_column($job_ticket_query->toArray(), 'job_ticket_id');

		$job_ticket_query_grouped = collect($job_ticket_query)->groupBy('production_order')->toArray();

		$time_logs_query = DB::connection('mysql_mes')->table('time_logs')->whereIn('job_ticket_id', $job_tickets)
			->select('job_ticket_id', 'good', 'machine_code')->get();

		$spotwelding_time_logs_query = DB::connection('mysql_mes')->table('spotwelding_qty')->whereIn('job_ticket_id', $job_tickets)
			->select('job_ticket_id', 'good', 'machine_code')->get();

		$spotwelding_time_logs_data = [];
		foreach ($spotwelding_time_logs_query as $row) {
			$spotwelding_time_logs_data[$row->job_ticket_id][] = [
				'good' => $row->good,
				'machine_code' => $row->machine_code,
			];
		}

		$time_logs_data = [];
		foreach ($time_logs_query as $row) {
			$time_logs_data[$row->job_ticket_id][] = [
				'good' => $row->good,
				'machine_code' => $row->machine_code,
			];
		}

		$job_ticket_data = [];
		foreach ($job_ticket_query as $row) {
			if($row->workstation == 'Spotwelding') {
				$time_logs = array_key_exists($row->job_ticket_id, $spotwelding_time_logs_data) ? $spotwelding_time_logs_data[$row->job_ticket_id] : [];
			} else {
				$time_logs = array_key_exists($row->job_ticket_id, $time_logs_data) ? $time_logs_data[$row->job_ticket_id] : [];
			}
			
			$job_ticket_data[$row->production_order][] = [
				'workstation' => $row->workstation,
				'process_id' => $row->process_id,
				'good' => $row->good,
				'completed_qty' => $row->completed_qty,
				'status' => $row->status,
				'remarks' => $row->remarks,
				'time_logs' => $time_logs
			];
		}

		$data = [];
		foreach ($production_query as $row) {
			$data[] = [
				'created_at' => $row->created_at,
				'production_order' => $row->production_order,
				'item_code' => $row->item_code,
				'qty_to_manufacture' => $row->qty_to_manufacture,
				'produced_qty' => $row->produced_qty,
				'feedback_qty' => $row->feedback_qty,
				'status' => $row->status,
				'job_ticket' => array_key_exists($row->production_order, $job_ticket_data) ? $job_ticket_data[$row->production_order] : []
			];
		}

		$permissions = $this->get_user_permitted_operation();

		return view('reports.timelog_output_vs_produced_qty', compact('production_query', 'data', 'permissions'));
	}

	public function jobTicketCompletedQtyVsTimelogsCompletedQty() {
		$job_ticket_query = DB::connection('mysql_mes')->table('job_ticket as jt')->join('production_order as po', 'po.production_order', 'jt.production_order')
			->whereIn('jt.status', ['In Progress', 'Pending'])->whereNotIn('po.status', ['Cancelled'])
			->select('jt.job_ticket_id', 'jt.production_order', 'jt.workstation', 'jt.good', 'jt.completed_qty', 'jt.status', 'jt.remarks', 'jt.created_at')
			->orderBy('jt.created_at', 'desc')->get();

		$job_tickets = array_column($job_ticket_query->toArray(), 'job_ticket_id');

		$time_logs_query = DB::connection('mysql_mes')->table('time_logs')->whereIn('job_ticket_id', $job_tickets)
			->select('job_ticket_id', 'good', 'machine_code')->get();

		$spotwelding_time_logs_query = DB::connection('mysql_mes')->table('spotwelding_qty')->whereIn('job_ticket_id', $job_tickets)
			->selectRaw('SUM(good) as total_good, job_ticket_id')->groupBy('spotwelding_part_id', 'job_ticket_id')->get();
	
		$spotwelding_time_logs_data = [];
		foreach ($spotwelding_time_logs_query as $row) {
			$spotwelding_time_logs_data[$row->job_ticket_id][] = [
				'good' => $row->total_good,
				'machine_code' => null,
			];
		}

		$time_logs_data = [];
		foreach ($time_logs_query as $row) {
			$time_logs_data[$row->job_ticket_id][] = [
				'good' => $row->good,
				'machine_code' => $row->machine_code,
			];
		}

		$job_ticket_data = [];
		foreach ($job_ticket_query as $row) {
			if($row->workstation == 'Spotwelding') {
				$time_logs = array_key_exists($row->job_ticket_id, $spotwelding_time_logs_data) ? $spotwelding_time_logs_data[$row->job_ticket_id] : [];
				$timelogs_completed_qty = collect($time_logs)->min('good');
			} else {
				$time_logs = array_key_exists($row->job_ticket_id, $time_logs_data) ? $time_logs_data[$row->job_ticket_id] : [];

				$timelogs_completed_qty = collect($time_logs)->sum('good');
			}

			if ($timelogs_completed_qty != $row->completed_qty) {
				$job_ticket_data[] = [
					'production_order' => $row->production_order,
					'created_at' => $row->created_at,
					'workstation' => $row->workstation,
					'good' => $row->good,
					'completed_qty' => $row->completed_qty,
					'status' => $row->status,
					'remarks' => $row->remarks,
					'time_logs' => $time_logs,
					'timelogs_completed_qty' => $timelogs_completed_qty
				];
			}
		}

		$permissions = $this->get_user_permitted_operation();

		return view('reports.job_ticket_vs_time_logs_completed_qty', compact('job_ticket_data', 'job_ticket_query', 'permissions'));
	}
	
	// /reset_operator_time_log
	public function reset_operator_time_log(Request $request) {
		$job_ticket_id = $request->job_ticket_id;
		$timelog_id = $request->timelog_id;

		DB::connection('mysql_mes')->beginTransaction();
		DB::connection('mysql')->beginTransaction();
		try {
			$job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)->first();
			if (!$job_ticket_details) {
				return response()->json(['status' => 0, 'message' => 'Job ticket not found.']);
			}

			$timelog_table = ($job_ticket_details->workstation != 'Spotwelding') ? 'time_logs' : 'spotwelding_qty';

			$production_order_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $job_ticket_details->production_order)->first();
			if (!$production_order_details) {
				return response()->json(['status' => 0, 'message' => 'Production Order not found.']);
			}

			$wip_timelog = DB::connection('mysql_mes')->table($timelog_table)->where('job_ticket_id', $job_ticket_id)
				->where('time_log_id', $timelog_id)->where('status', 'In Progress')->exists();

			if (!$wip_timelog) {
				if ($production_order_details->feedback_qty > 0) {
					return response()->json(['status' => 0, 'message' => 'Cannot reset time logs. Production Order has been partially / fully feedbacked.']);
				}
			}

			if ($request->is_operator) {
				$is_authorized = DB::connection('mysql_mes')->table('user')->where('user_access_id', $request->authorized_staff)->first();
				if (!$is_authorized) {
					return response()->json(['status' => 0, 'message' => 'User not authorized.']);
				}
				$authorized_user = $is_authorized->employee_name;
			} else {
				$authorized_user = Auth::user()->employee_name;
			}

			// insert activity logs
			DB::connection('mysql_mes')->table('activity_logs')->insert([
				'action' => 'Timelog Reset',
				'message' => $job_ticket_details->workstation . ' timelogs for ' . $job_ticket_details->production_order . ' has been reset by ' . $authorized_user,
				'created_at' => Carbon::now()->toDateTimeString(),
				'created_by' => $authorized_user
			]);

			$process = DB::connection('mysql_mes')->table('process')->where('process_id', $job_ticket_details->process_id)->first();

			if($job_ticket_details->workstation == 'Painting' && $process && $process->process_name == 'Unloading'){
				$unloading_detail = DB::connection('mysql_mes')->table('time_logs')->where('job_ticket_id', $job_ticket_id)->where('time_log_id', $timelog_id)->first();

				if($unloading_detail){ // update loading time log after resetting unloading
					DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $unloading_detail->reference_time_log)->update([
						'last_modified_at' => Carbon::now()->toDateTimeString(),
						'last_modified_by' => $authorized_user,
						'status' => 'In Progress'
					]);
				}
			}

			DB::connection('mysql_mes')->table($timelog_table)->where('job_ticket_id', $job_ticket_id)->where('time_log_id', $timelog_id)->delete();

			$update_job_ticket = $this->update_job_ticket($job_ticket_id, $authorized_user);

			if(!$update_job_ticket){
				DB::connection('mysql')->rollback();
				DB::connection('mysql_mes')->rollback();

				return response()->json(['success' => 0, 'message' => 'An error occured. Please try again.']);
			}

			$activity_logs = [
				'action' => 'Reset Time Log',
				'message' => 'Reset time logs for '.$process->process_name.' of '.$job_ticket_details->production_order.' by '.$authorized_user.' at '.Carbon::now()->toDateTimeString(),
				'reference' => $job_ticket_details->production_order,
				'created_at' => Carbon::now()->toDateTimeString(),
				'created_by' => $authorized_user
			];
	
			DB::connection('mysql_mes')->table('activity_logs')->insert($activity_logs);

			DB::connection('mysql_mes')->commit();
			DB::connection('mysql')->commit();

			return response()->json(['status' => 1, 'message' => 'Time logs has been reset.', 'id' => $job_ticket_details->production_order]);
		} catch (Exception $e) {
			DB::connection('mysql_mes')->rollback();
			DB::connection('mysql')->rollback();

			return response()->json(['status' => 0, 'message' => 'An error occured. Please contact your system administrator.']);
		}
	}

	public function edit_operator_time_log(Request $request) {
		$job_ticket_id = $request->job_ticket_id;
		$timelog_id = $request->timelog_id;
		$qty = (float)$request->qty;

		DB::connection('mysql_mes')->beginTransaction();
		DB::connection('mysql')->beginTransaction();
		try {
			if ($qty <= 0) {
				return response()->json(['status' => 0, 'message' => 'Qty cannot be less than 0.']);
			}

			$job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)->first();
			if (!$job_ticket_details) {
				return response()->json(['status' => 0, 'message' => 'Job ticket not found.']);
			}
		
			$production_order_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $job_ticket_details->production_order)->first();
			if (!$production_order_details) {
				return response()->json(['status' => 0, 'message' => 'Production Order not found.']);
			}

			if ($production_order_details->feedback_qty >= $production_order_details->qty_to_manufacture) {
				return response()->json(['status' => 0, 'message' => 'Cannot reset time logs. Production Order has been fully feedbacked.']);
			}

			if ($request->is_operator) {
				$is_authorized = DB::connection('mysql_mes')->table('user')->where('user_access_id', $request->authorized_staff)->first();
				if (!$is_authorized) {
					return response()->json(['status' => 0, 'message' => 'User not authorized.']);
				}
				$authorized_user = $is_authorized->employee_name;
			} else {
				$authorized_user = Auth::user()->employee_name;
			}

			$timelog_table = ($job_ticket_details->workstation != 'Spotwelding') ? 'time_logs' : 'spotwelding_qty';

			$time_log_details = DB::connection('mysql_mes')->table($timelog_table)->where('job_ticket_id', $job_ticket_id)->where('time_log_id', $timelog_id)->first();

			$timelogs_good = DB::connection('mysql_mes')->table($timelog_table)->where('job_ticket_id', $job_ticket_id)->where('time_log_id', '!=', $timelog_id)->sum('good');
			$remaining_to_be_completed = $production_order_details->qty_to_manufacture - $timelogs_good;
			if ($time_log_details) {
				if ($qty > $remaining_to_be_completed) {
					return response()->json(['status' => 0, 'message' => 'Qty cannot be greater than ' . $remaining_to_be_completed]);
				}
			}

			// insert activity logs
			DB::connection('mysql_mes')->table('activity_logs')->insert([
				'action' => 'Timelog Good Qty Update',
				'message' => 'Good qty for ' . $job_ticket_details->workstation . ' timelogs for ' . $job_ticket_details->production_order . ' has been updated from ' . $time_log_details->good . ' to ' . $qty . ' by ' . $authorized_user,
				'created_at' => Carbon::now()->toDateTimeString(),
				'created_by' => $authorized_user
			]);

			DB::connection('mysql_mes')->table($timelog_table)->where('job_ticket_id', $job_ticket_id)->where('time_log_id', $timelog_id)->update(['good' => $qty]);

			$update_job_ticket = $this->update_job_ticket($job_ticket_id);

			if(!$update_job_ticket){
				DB::connection('mysql')->rollback();
				DB::connection('mysql_mes')->rollback();

				return response()->json(['success' => 0, 'message' => 'An error occured. Please try again.']);
			}

			DB::connection('mysql_mes')->commit();
			DB::connection('mysql')->commit();

			return response()->json(['status' => 1, 'message' => 'Time logs has been updated.', 'id' => $job_ticket_details->production_order]);
		} catch (Exception $e) {
			DB::connection('mysql_mes')->rollback();
			DB::connection('mysql')->rollback();

			return response()->json(['status' => 0, 'message' => 'An error occured. Please contact your system administrator.']);
		}
	}

	public function viewOverrideForm($production_order) {
		$production_order_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)
			->leftJoin('delivery_date', function($join){
				$join->on( DB::raw('IFNULL(production_order.sales_order, production_order.material_request)'), '=', 'delivery_date.reference_no');
				$join->on('production_order.parent_item_code','=','delivery_date.parent_item_code');
			}) // get delivery date from delivery_date table
			->select('production_order.*', 'delivery_date.rescheduled_delivery_date')->first();

		if (!$production_order_details) {
			return response()->json(['message' => 'Production Order <b>' . $production_order . '</b> not found.', 'status' => 1]);
		}

		$production_order_operations = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $production_order)
			->select(DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process'), 'workstation', 'process_id', 'job_ticket_id', 'status', 'completed_qty', 'reject', 'remarks')
			->get();

		$operator_logs = DB::connection('mysql_mes')->table('job_ticket as jt')
			->join('time_logs as tl', 'tl.job_ticket_id', 'jt.job_ticket_id')
			->where('jt.workstation', '!=', 'Spotwelding')
			->where('jt.production_order', $production_order)->get();

		$operator_logs = collect($operator_logs)->groupBy('job_ticket_id')->toArray();

		$spotwelding_operator_logs = DB::connection('mysql_mes')->table('job_ticket as jt')
			->join('spotwelding_qty as tl', 'tl.job_ticket_id', 'jt.job_ticket_id')
			->where('jt.workstation', 'Spotwelding')
			->where('jt.production_order', $production_order)->get();

		$spotwelding_operator_logs = collect($spotwelding_operator_logs)->groupBy('job_ticket_id')->toArray();

		$workstation_names = collect($production_order_operations)->pluck('workstation');
		$workstation_ids = DB::connection('mysql_mes')->table('workstation')->whereIn('workstation_name', $workstation_names)->pluck('workstation_id');
		$process_ids = collect($production_order_operations)->pluck('process_id');

		$machine_list = DB::connection('mysql_mes')->table('process_assignment')
				->join('machine', 'machine.machine_id', 'process_assignment.machine_id')
				->whereIn('process_assignment.workstation_id', $workstation_ids)
				->whereIn('process_assignment.process_id', $process_ids)
				->select('machine.*', 'process_assignment.process_id')->get();

		$machine_per_process = collect($machine_list)->groupBy('process_id')->toArray();

		$operation_name = DB::connection('mysql_mes')->table('operation')->where('operation_id', $production_order_details->operation_id)->first();
		$operation_name = $operation_name ? $operation_name->operation_name : null;

		$operation_name = strpos(strtolower($operation_name), "fabrication") !== false ? $operation_name . ' Painting' : $operation_name;

		$operation_name = explode(" ", $operation_name);

		$operators = DB::connection('mysql_essex')->table('users as u')
			->join('departments as d', 'd.department_id', 'u.department_id')
			->whereIn('d.department', ['Production', 'Fabrication', 'Assembly', 'Painting'])
			->where('u.user_type', 'Employee')->where('u.status', 'Active')
			->orderBy('employee_name', 'asc')->pluck('u.employee_name', 'u.user_id');
		
		return view('override_production_form', compact('production_order_operations', 'production_order_details', 'machine_per_process', 'operators', 'operator_logs', 'spotwelding_operator_logs'));
	}

	public function updateOverrideProduction(Request $request) {
		$data = $request->all();
		
		DB::connection('mysql_mes')->beginTransaction();
		DB::connection('mysql')->beginTransaction();
		try {
			$now = Carbon::now();
			$production_order_details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $data['production_order'])->first();
			if (!$production_order_details) {
				return response()->json(['status' => 0, 'message' => 'Production order <b>' . $data['production_order'] . '</b> not found.']);
			}
			// get workstations
			$job_ticket_ids = array_keys($data['job_ticket']);
			$workstations = DB::connection('mysql_mes')->table('job_ticket')->whereIn('job_ticket_id', $job_ticket_ids)->pluck('workstation', 'job_ticket_id')->toArray();

			// timelogs data
			$timelogs = $spotwelding_logs = [];
			foreach ($data['job_ticket'] as $job_ticket_id => $value) {
				$workstation = array_key_exists($job_ticket_id, $workstations) ? $workstations[$job_ticket_id] : null;
				if ($workstation) {
					if (isset($value['has_time_logs']) && $value['has_time_logs']) {
						unset($value['has_time_logs']);
						// get employee names
						$employee_ids = array_column($value, 'operator');
						$employee_names = DB::connection('mysql_essex')->table('users')->whereIn('user_id', $employee_ids)->pluck('employee_name', 'user_id')->toArray();
						// get machine names
						$machine_codes = array_column($value, 'machine');
						$machine_names = DB::connection('mysql_mes')->table('machine')->whereIn('machine_code', $machine_codes)->pluck('machine_name', 'machine_code')->toArray();

						foreach ($value as $time_log_id => $tl) {
							if (Carbon::parse($tl['start_time'])->gt(Carbon::parse($tl['end_time']))) {
								return response()->json(['status' => 0, 'message' => 'Start time cannot be greater than End time for <b>' . $workstation . '</b>.']);
							}

							$machine_name = array_key_exists($tl['machine'], $machine_names) ? $machine_names[$tl['machine']] : null;
							$employee_name = array_key_exists($tl['operator'], $employee_names) ? $employee_names[$tl['operator']] : null;
							$seconds = Carbon::parse($tl['end_time'])->diffInSeconds(Carbon::parse($tl['start_time']));
							$cycle_time_in_seconds = $seconds > 0 ? $seconds / $tl['good'] : 0;
							$duration = $seconds / 3600;
							
							$table_update = ($workstation != 'Spotwelding') ? 'time_logs' : 'spotwelding_qty';
								
							DB::connection('mysql_mes')->table($table_update)->where('time_log_id', $time_log_id)->update([
								'from_time' => Carbon::parse($tl['start_time'])->toDateTimeString(),
								'to_time' => Carbon::parse($tl['end_time'])->toDateTimeString(),
								'duration' => $duration,
								'good' => $tl['good'],
								'reject' => $tl['reject'],
								'machine_code' => $tl['machine'],
								'machine_name' => $machine_name,
								'operator_id' => $tl['operator'],
								'operator_name' => $employee_name,
								'status' => 'Completed',
								'cycle_time_in_seconds' => $cycle_time_in_seconds,
								'created_by' => Auth::user()->employee_name,
								'created_at' => $now->toDateTimeString(),
							]);
						}
					} else {
						if ($production_order_details->qty_to_manufacture < $value['good']) {
							return response()->json(['status' => 0, 'message' => 'Good qty for <b>' . $workstation . '</b> cannot be greater than <b>' . $production_order_details->qty_to_manufacture . '</b>']);
						}

						if (Carbon::parse($value['start_time'])->gt(Carbon::parse($value['end_time']))) {
							return response()->json(['status' => 0, 'message' => 'Start time cannot be greater than End time for <b>' . $workstation . '</b>.']);
						}
						// get employee names
						$employee_ids = array_column($data['job_ticket'], 'operator');
						$employee_names = DB::connection('mysql_essex')->table('users')->whereIn('user_id', $employee_ids)->pluck('employee_name', 'user_id')->toArray();
						// get machine names
						$machine_codes = array_column($data['job_ticket'], 'machine');
						$machine_names = DB::connection('mysql_mes')->table('machine')->whereIn('machine_code', $machine_codes)->pluck('machine_name', 'machine_code')->toArray();

						$employee_name = array_key_exists($value['operator'], $employee_names) ? $employee_names[$value['operator']] : null;
						$machine_name = array_key_exists($value['machine'], $machine_names) ? $machine_names[$value['machine']] : null;
						$seconds = Carbon::parse($value['end_time'])->diffInSeconds(Carbon::parse($value['start_time']));
						$cycle_time_in_seconds = $seconds > 0 ? $seconds / $value['good'] : 0;
						$duration = $seconds / 3600;
						
						if ($workstation != 'Spotwelding') {
							$timelogs[] = [
								'job_ticket_id' => $job_ticket_id,
								'from_time' => Carbon::parse($value['start_time'])->toDateTimeString(),
								'to_time' => Carbon::parse($value['end_time'])->toDateTimeString(),
								'duration' => $duration,
								'good' => $value['good'],
								'reject' => $value['reject'],
								'machine_code' => $value['machine'],
								'machine_name' => $machine_name,
								'operator_id' => $value['operator'],
								'operator_name' => $employee_name,
								'status' => 'Completed',
								'cycle_time_in_seconds' => $cycle_time_in_seconds,
								'created_by' => Auth::user()->employee_name,
								'created_at' => $now->toDateTimeString(),
							];
						} else {
							$spotwelding_part_id = uniqid();
							$spotwelding_logs[] = [
								'job_ticket_id' => $job_ticket_id,
								'from_time' => Carbon::parse($value['start_time'])->toDateTimeString(),
								'to_time' => Carbon::parse($value['end_time'])->toDateTimeString(),
								'spotwelding_part_id' => $spotwelding_part_id,
								'duration' => $duration,
								'good' => $value['good'],
								'reject' => $value['reject'],
								'machine_code' => $value['machine'],
								'machine_name' => $machine_name,
								'operator_id' => $value['operator'],
								'operator_name' => $employee_name,
								'status' => 'Completed',
								'cycle_time_in_seconds' => $cycle_time_in_seconds,
								'created_by' => Auth::user()->employee_name,
								'created_at' => $now->toDateTimeString(),
							];

							$bom_operation_id = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)->pluck('bom_operation_id');
							$bom_items = DB::connection('mysql')->table('tabBOM Operation as op')
								->join('tabBOM Item as item', 'op.parent', 'item.parent')
								->join('tabBOM as bom', 'bom.name', 'item.parent')
								->where('op.name', $bom_operation_id)
								->select('item.*', 'bom.item as housing_code')->get();
								
							$spotwelding_part_arr = [];
							foreach ($bom_items as $item) {
								$spotwelding_part_arr[] = [
									'housing_production_order' => $request->production_order,
									'spotwelding_part_id' => $spotwelding_part_id,
									'housing_code' => $item->housing_code,
									'reference_no' => $production_order_details->sales_order ? $production_order_details->sales_order : $production_order_details->material_request,
									'part_category' => isset(explode(' - ', $item->item_classification)[1]) ? explode(' - ', $item->item_classification)[1] : null,
									'part_code' => $item->item_code,
									'created_by' => Auth::user()->email,
									'created_at' => Carbon::now()->toDateTimeString()
								];
							}

							DB::connection('mysql_mes')->table('spotwelding_part')->insert($spotwelding_part_arr);
						}
					}
				}
			}

			if (count($timelogs) > 0) {
				DB::connection('mysql_mes')->table('time_logs')->insert($timelogs);
			}
			
			if (count($spotwelding_logs) > 0) {
				DB::connection('mysql_mes')->table('spotwelding_qty')->insert($spotwelding_logs);
			}

			foreach($job_ticket_ids as $jtid) {
				$update_job_ticket = $this->update_job_ticket($jtid);

				if(!$update_job_ticket){
					DB::connection('mysql')->rollback();
					DB::connection('mysql_mes')->rollback();

					return response()->json(['success' => 0, 'message' => 'An error occured. Please try again.']);
				}
			}

			$jt_exceeding_production = DB::connection('mysql_mes')->table('job_ticket as jt')
				->join('process as p', 'p.process_id', 'jt.process_id')
				->where('production_order', $production_order_details->production_order)
				->where('jt.completed_qty', '>', $production_order_details->qty_to_manufacture)->first();

			if ($jt_exceeding_production) {
				return response()->json(['status' => 0, 'message' => 'Good qty for <b>' . $jt_exceeding_production->workstation . '['. $jt_exceeding_production->process_name.']</b> cannot be greater than <b>' . $production_order_details->qty_to_manufacture . '</b>']);
			}
			
			DB::connection('mysql_mes')->commit();
			DB::connection('mysql')->commit();

			return response()->json(['status' => 1, 'message' => 'Production order <b>' . $production_order_details->production_order .'</b> has been overriden.', 'production_order' => $production_order_details->production_order]);
		} catch (Exception $e) {
			DB::connection('mysql_mes')->rollback();
			DB::connection('mysql')->rollback();

			return response()->json(['status' => 0, 'message' => 'An error occured. Please contact your system administrator.']);
		}
	}

	public function idleMachines(Request $request) {
		$operation_id = $request->operation;

		$in_progress_machines = DB::connection('mysql_mes')->table('time_logs')->where('status', 'In Progress')->distinct()->pluck('machine_code');

		$spotwelding_in_progress_machines = DB::connection('mysql_mes')->table('spotwelding_qty')->where('status', 'In Progress')->distinct()->pluck('machine_code');

		$machines_in_use = collect($in_progress_machines)->merge($spotwelding_in_progress_machines);

		$list = DB::connection('mysql_mes')->table('machine')
			->when($operation_id, function ($query) use ($operation_id) {
				return $query->where('operation_id', $operation_id);
			})
			->whereNotIn('machine_code', $machines_in_use)
			->select('machine_code', 'machine_name', 'status', 'image')
			->get();

		$machines_idle_time_spotwelding = DB::connection('mysql_mes')->table('time_logs')->where('status', '!=', 'In Progress')
			->whereNotNull('machine_code')->select('machine_code', DB::raw('MAX(to_time) as last_transaction'))->groupBy('machine_code')
			->orderBy('to_time', 'desc');

		$machines_idle_time = DB::connection('mysql_mes')->table('time_logs')->where('status', '!=', 'In Progress')
			->whereNotNull('machine_code')->select('machine_code', DB::raw('MAX(to_time) as last_transaction'))->groupBy('machine_code')
			->orderBy('to_time', 'desc')->unionAll($machines_idle_time_spotwelding)->pluck('last_transaction', 'machine_code')->toArray();
		
		$data = [];
		foreach ($list as $r) {
			$last_transaction = array_key_exists($r->machine_code, $machines_idle_time) ? $machines_idle_time[$r->machine_code] : '2022-01-01';

			$cycle_time_in_seconds = Carbon::now()->diffInSeconds(Carbon::parse($last_transaction));

			$seconds = $cycle_time_in_seconds%60;
			$minutes = floor(($cycle_time_in_seconds%3600)/60);
			$hours = floor(($cycle_time_in_seconds%86400)/3600);
			$days = floor(($cycle_time_in_seconds%2592000)/86400);
			$months = floor($cycle_time_in_seconds/2592000);
			
			$dur_months = ($months > 0) ? $months .'M' : null;
			$dur_days = ($days > 0) ? $days .'d' : null;
			$dur_hours = ($hours > 0) ? $hours .'h' : null;
			$dur_minutes = ($minutes > 0) ? $minutes .'m' : null;
			$dur_seconds = ($seconds > 0) ? $seconds .'s' : null;

			$total_duration = $dur_months . ' '. $dur_days . ' ' .$dur_hours . ' '. $dur_minutes . ' ' . $dur_seconds;

			$data[] = [
				'image' => $r->image,
				'machine_code' => $r->machine_code,
				'machine_name' => $r->machine_name,
				'idle_time' => $total_duration,
				'last_transaction' => $last_transaction,
			];
		}

		$data = collect($data)->sortBy('last_transaction')->reverse();

		return view('dashboard_idle_machines', compact('data'));
	}

	public function idleOperators(Request $request) {
		$operation_id = $request->operation;

		$operators = DB::connection('mysql_essex')->table('users as u')
			->join('departments as d', 'd.department_id', 'u.department_id')
			->where('u.status', 'Active')->where('u.user_type', 'Employee')
			->where(function($q) use ($request) {
				$q->where('d.department', 'LIKE', '%painting%')
					->orWhere('d.department', 'LIKE', '%assembly%')
					->orWhere('d.department', 'LIKE', '%fabrication%');
			})
			->select('u.user_id as operator_id', 'u.employee_name', 'd.department')
			->orderBy('u.employee_name', 'asc')->get();

		$shift_schedules = DB::connection('mysql_mes')->table('shift as s')
			->join('shift_schedule as ss', 'ss.shift_id', 's.shift_id')
			->whereDate('ss.date', Carbon::now()->format('Y-m-d'))
			->select('s.time_in', 's.time_out', 's.operation_id', 'ss.date')->get();

		$fabrication_schedule = $painting_schedule = $assembly_schedule = [];
		foreach ($shift_schedules as $r) {
			if ($r->operation_id == 1) {
				$fabrication_schedule[] = [
					'time_in' => Carbon::parse($r->time_in)->format('H:i:s'),
					'time_out' => Carbon::parse($r->time_out)->format('H:i:s'),
				];
			}

			if ($r->operation_id == 2) {
				$painting_schedule[] = [
					'time_in' => Carbon::parse($r->time_in)->format('H:i:s'),
					'time_out' => Carbon::parse($r->time_out)->format('H:i:s'),
				];
			}

			if ($r->operation_id == 3) {
				$assembly_schedule[] = [
					'time_in' => Carbon::parse($r->time_in)->format('H:i:s'),
					'time_out' => Carbon::parse($r->time_out)->format('H:i:s'),
				];
			}
		}

		$fabrication_in = collect($fabrication_schedule)->min('time_in');
		$fabrication_out = collect($fabrication_schedule)->max('time_out');
		$painting_in = collect($painting_schedule)->min('time_in');
		$painting_out = collect($painting_schedule)->max('time_out');
		$assembly_in = collect($assembly_schedule)->min('time_in');
		$assembly_out = collect($assembly_schedule)->max('time_out');

		$wip_operators = DB::connection('mysql_mes')->table('time_logs')
			->where('status', 'In Progress')->whereNotNull('operator_id')
			->select('operator_id', 'time_log_id')->get();
		
		$wip_time_logs = collect($wip_operators)->pluck('time_log_id');
		$wip_operators = collect($wip_operators)->pluck('operator_id');
		$wip_helpers = DB::connection('mysql_mes')->table('helper')->whereIn('time_log_id', $wip_time_logs)->distinct()->pluck('operator_id');

		$spowtwelding_wip_operators = DB::connection('mysql_mes')->table('spotwelding_qty')
			->whereNotNull('operator_id')->where('status', 'In Progress')
			->whereNotNull('operator_id')->select('operator_id', 'time_log_id')->get();

		$spotwelding_wip_time_logs = collect($spowtwelding_wip_operators)->pluck('time_log_id');
		$spowtwelding_wip_operators = collect($spowtwelding_wip_operators)->pluck('operator_id');
		$spotwelding_wip_helpers = DB::connection('mysql_mes')->table('helper')->whereIn('time_log_id', $spotwelding_wip_time_logs)->distinct()->pluck('operator_id');

		$wip_operators = collect($wip_operators)->merge($spowtwelding_wip_operators)->merge($wip_helpers)->merge($spotwelding_wip_helpers)->toArray();

		$operator_images = DB::connection('mysql_essex')->table('users')
			->whereNotNull('image')->whereIn('user_id', collect($operators)->pluck('operator_id'))
			->pluck('image', 'user_id')->toArray();

		$helpers_idle_time = DB::connection('mysql_mes')->table('time_logs as t')->join('helper as h', 't.time_log_id', 'h.time_log_id')
			->where('t.status', '!=', 'In Progress')->whereBetween('h.created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])
			->whereNotNull('h.operator_id')->select('h.operator_id', DB::raw('MAX(t.to_time) as last_transaction'))
			->groupBy('h.operator_id')->orderBy('t.to_time', 'desc');

		$helpers_idle_time_spotwelding = DB::connection('mysql_mes')->table('spotwelding_qty as t')->join('helper as h', 't.time_log_id', 'h.time_log_id')
			->where('t.status', '!=', 'In Progress')->whereBetween('h.created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])
			->whereNotNull('h.operator_id')->select('h.operator_id', DB::raw('MAX(t.to_time) as last_transaction'))->groupBy('h.operator_id')
			->orderBy('t.to_time', 'desc')->orderBy('to_time', 'desc')->unionAll($helpers_idle_time)->get();

		$helpers_last_transaction = collect($helpers_idle_time_spotwelding)->groupBy('operator_id')->toArray();

		$operators_idle_time_spotwelding = DB::connection('mysql_mes')->table('spotwelding_qty')->where('status', '!=', 'In Progress')
			->whereNotNull('operator_id')->whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])
			->select('operator_id', DB::raw('MAX(to_time) as last_transaction'))->groupBy('operator_id')
			->orderBy('to_time', 'desc');

		$operators_idle_time = DB::connection('mysql_mes')->table('time_logs')->where('status', '!=', 'In Progress')
			->whereNotNull('operator_id')->whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])
			->select('operator_id', DB::raw('MAX(to_time) as last_transaction'))->groupBy('operator_id')
			->orderBy('to_time', 'desc')->unionAll($operators_idle_time_spotwelding)->get();

		$operators_last_transaction = collect($operators_idle_time)->groupBy('operator_id')->toArray();

		$operators_list = $temp = [];
		foreach ($operators as $row) {		
			$last_transaction = null;	
			if (!in_array($row->operator_id, $temp)) {
				$image = array_key_exists($row->operator_id, $operator_images) ? $operator_images[$row->operator_id] : null;
				$image = $image ? 'https://essex.fumaco.local/' . $image : null;
				if (!in_array($row->operator_id, $wip_operators)) {
					// last transaction as operator
					$last_transaction_as_operator = array_key_exists($row->operator_id, $operators_last_transaction) ? Carbon::parse($operators_last_transaction[$row->operator_id][0]->last_transaction) : null;
					// last transaction as helper
					$last_transaction_as_helper = array_key_exists($row->operator_id, $helpers_last_transaction) ? Carbon::parse($helpers_last_transaction[$row->operator_id][0]->last_transaction) : null;
					// get max last transaction based on operator id
					if ($last_transaction_as_helper && $last_transaction_as_operator) {
						$last_transaction = Carbon::parse($last_transaction_as_helper)->gt(Carbon::parse($last_transaction_as_operator)) ? $last_transaction_as_helper : $last_transaction_as_operator;
					} elseif ($last_transaction_as_operator) {
						$last_transaction = $last_transaction_as_operator;
					} else {
						$last_transaction = $last_transaction_as_helper;
					}

					$last_transaction = $last_transaction ? Carbon::parse($last_transaction) : null;
					if ($last_transaction) {
						if (Carbon::now()->format('Y-m-d') !== Carbon::parse($last_transaction)->format('Y-m-d')){
							if (strpos(strtolower($row->department), 'fabrication') > -1) {
								$last_transaction = $fabrication_in ? Carbon::parse($fabrication_in) : null;
							}
							if (strpos(strtolower($row->department), 'assembly') > -1) {
								$last_transaction = $assembly_in ? Carbon::parse($assembly_in) : null;
							}
							if (strpos(strtolower($row->department), 'painting') > -1) {
								$last_transaction = $painting_in ? Carbon::parse($painting_in) : null;
							}
						}
					}

					if (!$last_transaction) {
						if (strpos(strtolower($row->department), 'fabrication') > -1) {
							$last_transaction = $fabrication_in ? Carbon::parse($fabrication_in) : null;
						}
						if (strpos(strtolower($row->department), 'assembly') > -1) {
							$last_transaction = $assembly_in ? Carbon::parse($assembly_in) : null;
						}
						if (strpos(strtolower($row->department), 'painting') > -1) {
							$last_transaction = $painting_in ? Carbon::parse($painting_in) : null;
						}
					}

					$out = null;
					if (strpos(strtolower($row->department), 'fabrication') > -1) {
						$out = $fabrication_out ? Carbon::parse($fabrication_out) : null;
					}
					if (strpos(strtolower($row->department), 'assembly') > -1) {
						$out = $assembly_out ? Carbon::parse($assembly_out) : null;
					}
					if (strpos(strtolower($row->department), 'painting') > -1) {
						$out = $painting_out ? Carbon::parse($painting_out) : null;
					}
          
					$cycle_time_in_seconds = Carbon::now()->diffInSeconds($last_transaction);

					$seconds = $cycle_time_in_seconds%60;
					$minutes = floor(($cycle_time_in_seconds%3600)/60);
					$hours = floor(($cycle_time_in_seconds%86400)/3600);
					$days = floor(($cycle_time_in_seconds%2592000)/86400);
					$months = floor($cycle_time_in_seconds/2592000);
					
					$dur_months = ($months > 0) ? $months .'M' : null;
					$dur_days = ($days > 0) ? $days .'d' : null;
					$dur_hours = ($hours > 0) ? $hours .'h' : null;
					$dur_minutes = ($minutes > 0) ? $minutes .'m' : null;
					$dur_seconds = ($seconds > 0) ? $seconds .'s' : null;

					$total_duration = $dur_months . ' '. $dur_days . ' ' .$dur_hours . ' '. $dur_minutes . ' ' . $dur_seconds;
					$total_duration = trim($total_duration) ? trim($total_duration) : null;

					if (!$out || !Carbon::now()->gt(Carbon::parse($out))) {
						if ($total_duration) {
							$operators_list[] = [
								'id' => $row->operator_id,
								'name' => $row->employee_name,
								'image' => $image,
								'idle_time' => $total_duration,
								'last_transaction' => $last_transaction,
								'cycle_time_in_seconds' => $cycle_time_in_seconds,
							];
						}
					}
					
					$temp[] = $row->operator_id;
				}
			}
		}

		$list = collect($operators_list)->sortBy('cycle_time_in_seconds')->toArray();

		return view('dashboard_idle_operators', compact('list'));
	}

	public function dashboardNumbers(Request $request) {
		$now = Carbon::now();
		$scheduled_orders = DB::connection('mysql_mes')->table('production_order')
			->whereBetween('planned_start_date', [$now->startOfDay()->format('Y-m-d'), $now->endOfDay()->format('Y-m-d')])
			->whereNotIn('status', ['Cancelled', 'Closed'])->select('sales_order', 'material_request', 'classification')
			->groupBy('sales_order', 'material_request', 'classification')->get();

		$sales_orders = $consignment_orders = $sample_orders = $other_orders = 0;
		foreach	($scheduled_orders as $o) {
			$sales_orders += $o->sales_order ? 1 : 0;
			$consignment_orders += in_array($o->classification, ['Consignment', 'Consignment Order']) ? 1 : 0;
			$sample_orders += in_array($o->classification, ['Sample Order', 'Sample']) ? 1 : 0;
			$other_orders += !in_array($o->classification, ['Consignment', 'Consignment Order', 'Customer Order', 'Sample', 'Sample Order']) ? 1 : 0;
		}

		$quality_inspection_created = DB::connection('mysql_mes')->table('quality_inspection')
			->whereDate('qa_inspection_date', '>=', $now->startOfDay())
			->whereDate('qa_inspection_date', '<=', $now->endOfDay())->count();

		$for_feedback = DB::connection('mysql_mes')->table('production_order')->whereNotIn('status', ['Cancelled', 'Closed'])->where('produced_qty', '>', 0)
			->whereRaw('produced_qty > feedback_qty')->whereRaw('qty_to_manufacture > feedback_qty')->count();

		return [
			'sales_orders' => number_format($sales_orders),
			'consignment_orders' => number_format($consignment_orders),
			'sample_orders' => number_format($sample_orders),
			'other_orders' => number_format($other_orders),
			'quality_inspections' => number_format($quality_inspection_created),
			'for_feedback' => number_format($for_feedback)
		];
	}

	public function orderTypes() {
		$material_requests = DB::connection('mysql')->table('tabMaterial Request')->where('docstatus', 1)
			->whereIn('custom_purpose', ['Manufacture', 'Sample Order', 'Consignment Order'])
			->where('per_ordered', '<', 100)->where('status', '!=', 'Stopped')
			->select('name', 'creation', 'custom_purpose as order_type', 'status')->get();

		$consignment_orders = $sample_orders = $other_orders = 0;
		foreach ($material_requests as $r) {
			if ($r->order_type == 'Consignment Order') {
				$consignment_orders++;
			}

			if ($r->order_type == 'Sample Order') {
				$sample_orders++;
			}

			if (!in_array($r->order_type, ['Consignment Order', 'Sample Order', 'Consignment', 'Sample'])) {
				$other_orders++;
			}
		}

		$customer_order = DB::connection('mysql')->table('tabSales Order')->where('docstatus', 1)
			->whereIn('sales_type', ['Regular Sales', 'Sales DR'])
			->where('per_delivered', '<', 100)->where('status', '!=', 'Closed')
			->select('name', 'creation', 'sales_type as order_type', 'status')
			->orderBy('creation', 'desc')->count();

		return [
			'customer_order' => number_format($customer_order),
			'consignment_order' => number_format($consignment_orders),
			'sample_order' => number_format($sample_orders),
			'other_order' => number_format($other_orders)
		];
	}
	
	public function viewOrderList(Request $request) {
		$permissions = $this->get_user_permitted_operation();

		$order_types = [
			[
				'id' => 'Customer Order',
				'type' => 'Sales Order'
			],
			[
				'id' => 'Consignment Order',
				'type' => 'Consignment Order'
			],
			[
				'id' => 'Sample Order',
				'type' => 'Sample Order'
			],
			[
				'id' => 'Manufacture',
				'type' => 'Others'
			]
		];

		return view('view_order_list', compact('permissions', 'order_types'));
	}

	public function viewOrderDetails($id) {
		$ref_type = explode("-", $id)[0];

		if ($ref_type == 'MREQ') {
			$details = DB::connection('mysql')->table('tabMaterial Request as mr')->where('name', $id)
				->select('mr.name', 'mr.creation', 'mr.customer', 'mr.project', 'mr.delivery_date', 'mr.custom_purpose as order_type', 'mr.status', 'mr.modified as date_approved', DB::raw('CONCAT(mr.address_line, " ", mr.address_line2, " ", mr.city_town)  as shipping_address'), 'mr.owner', 'mr.notes00 as notes', 'mr.sales_person', 'mr.delivery_date as reschedule_delivery_date', DB::raw('IFNULL(mr.delivery_date, 0) as reschedule_delivery'), 'mr.company', 'mr.modified', 'mr.per_ordered as delivery_percentage')->first();

			$item_list = DB::connection('mysql')->table('tabMaterial Request Item')->where('parent', $id)
				->select('item_code', 'description', 'qty', 'stock_uom', 'idx', 'parent', 'schedule_date as delivery_date', 'name', 'ordered_qty as delivered_qty', 'item_code as item_note', 'warehouse')
				->orderBy('idx', 'asc')->get();

			$item_codes = collect($item_list)->pluck('item_code')->unique();

			$actual_delivery_date_per_item = DB::connection('mysql')->table('tabStock Entry as ste')->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
				->join('tabMaterial Request Item as mri', 'mri.item_code', 'sted.item_code')
				->where('mri.parent', $id)->where('ste.material_request', $id)->where('ste.docstatus', 1)->whereIn('sted.item_code', $item_codes)
				->where('ste.stock_entry_type', 'Material Transfer')
				->select('ste.name', 'sted.item_code', DB::raw('SUM(sted.qty) as delivered_qty'), 'ste.delivery_date as actual_delivery_date', 'ste.reference_no as dr_ref_no', 'mri.qty as ordered_qty', 'mri.stock_uom', 'sted.date_modified', 'ste.posting_date', 'ste.owner', 'sted.session_user')
				->groupBy('ste.name', 'sted.item_code', 'ste.delivery_date', 'ste.reference_no', 'mri.qty', 'mri.stock_uom', 'sted.date_modified', 'ste.posting_date', 'ste.owner', 'sted.session_user')->get();

			$picking_slip_arr = [];
			foreach ($actual_delivery_date_per_item as $ps_row) {
				$picking_slip_arr[$ps_row->name][$ps_row->item_code]['date_picked'] = Carbon::parse($ps_row->date_modified ? $ps_row->date_modified : $ps_row->posting_date)->format('M. d, Y');
				$picking_slip_arr[$ps_row->name][$ps_row->item_code]['user'] = $ps_row->session_user;
			}
		} else {
			$details = DB::connection('mysql')->table('tabSales Order as so')->where('name', $id)
				->select('so.name', 'so.creation', 'so.customer', 'so.project', 'so.delivery_date', 'so.sales_type as order_type', 'so.status', 'so.date_approved', 'so.shipping_address', 'so.owner', 'so.notes', 'so.sales_person', 'so.reschedule_delivery_date', 'so.reschedule_delivery', 'so.company', 'so.modified', 'so.per_delivered as delivery_percentage')
				->first();
			
			$item_list = DB::connection('mysql')->table('tabSales Order Item')->where('parent', $id)
				->select('item_code', 'description', 'qty', 'stock_uom', 'idx', 'parent', 'delivery_date', 'name', 'delivered_qty', 'item_note', 'warehouse')
				->orderBy('idx', 'asc')->get();

			$item_codes = collect($item_list)->pluck('item_code')->unique();

			$actual_delivery_date_per_item = DB::connection('mysql')->table('tabDelivery Note as dr')
				->join('tabDelivery Note Item as dri', 'dr.name', 'dri.parent')->join('tabSales Order Item as soi', 'soi.item_code', 'dri.item_code')
				->where('dr.reference', $id)->where('soi.parent', $id)->where('dr.docstatus', 1)->whereIn('dri.item_code', $item_codes)
				->select('dr.name', 'dri.item_code', DB::raw('SUM(dri.qty) as delivered_qty'), 'dr.delivery_date as actual_delivery_date', 'dr.dr_ref_no', 'soi.qty as ordered_qty', 'dri.stock_uom', 'dr.owner')
				->groupBy('dr.name', 'dri.item_code', 'dr.delivery_date', 'dr.dr_ref_no', 'soi.qty', 'dri.stock_uom', 'dr.owner')->get();

			$picking_slips = DB::connection('mysql')->table('tabPacking Slip as ps')->join('tabPacking Slip Item as psi', 'ps.name', 'psi.parent')
				->whereIn('ps.delivery_note', collect($actual_delivery_date_per_item)->pluck('name'))
				->whereIn('psi.item_code', $item_codes)->select('ps.delivery_note', 'psi.item_code', 'psi.date_modified', 'ps.modified', 'psi.session_user')->get();

			$picking_slip_arr = [];
			foreach ($picking_slips as $ps_row) {
				$picking_slip_arr[$ps_row->delivery_note][$ps_row->item_code]['date_picked'] = Carbon::parse($ps_row->date_modified ? $ps_row->date_modified : $ps_row->modified)->format('M. d, Y');
				$picking_slip_arr[$ps_row->delivery_note][$ps_row->item_code]['user'] = $ps_row->session_user;
			}
		}

		$current_inventory = DB::connection('mysql')->table('tabBin')->whereIn('item_code', collect($item_list)->pluck('item_code'))->whereIn('warehouse', collect($item_list)->pluck('warehouse'))->get();
		$current_inventory_arr = [];
		foreach($current_inventory as $ci){
			$current_inventory_arr[$ci->item_code][$ci->warehouse] = $ci->actual_qty;
		}

		$actual_delivery_date_per_item = collect($actual_delivery_date_per_item)->groupBy('item_code')->toArray();

		$item_images = DB::connection('mysql')->table('tabItem Images')->whereIn('parent', $item_codes)->pluck('image_path', 'parent')->toArray();

		$default_boms = DB::connection('mysql')->table('tabBOM')
			->whereIn('item', $item_codes)->where('docstatus', 1)->where('is_active', 1)
			->select('item', 'is_default', 'name')->orderBy('is_default', 'desc')
			->orderBy('creation', 'desc')->get();

		$default_boms = collect($default_boms)->groupBy('item')->toArray();

		$item_list = collect($item_list)->groupBy('parent')->toArray();

		$production_orders = DB::connection('mysql_mes')->table('production_order')
			->whereIn('item_code', $item_codes)->where(DB::raw('IFNULL(sales_order, material_request)'), $id)
			->where('status', '!=', 'Cancelled')
			->select('production_order', 'item_code', DB::raw('IFNULL(sales_order, material_request) as reference'), 'qty_to_manufacture', 'feedback_qty', 'status', 'produced_qty', 'created_at', 'created_by')
			->orderBy('created_at', 'desc')->get();

		$items_production_orders = [];
		foreach ($production_orders as $r) {
			$p_status = $r->produced_qty > $r->feedback_qty ? 'Ready for Feedback' : $r->status;
			$p_status = $r->qty_to_manufacture == $r->feedback_qty ? 'Feedbacked' : $p_status;
			$items_production_orders[$r->reference][$r->item_code][] = [
				'production_order' => $r->production_order,
				'status' => $p_status,
				'produced_qty' => $r->produced_qty,
				'qty_to_manufacture' => $r->qty_to_manufacture,
				'created_at' => $r->created_at,
				'created_by' => $r->created_by
			];
		}

		$seen_order_logs = DB::connection('mysql_mes')->table('activity_logs')
			->where('reference', $id)->where('action', 'View Order')->orderBy('created_at', 'desc')->get();
		$seen_logs_per_order = collect($seen_order_logs)->groupBy('reference')->toArray();
		$seen_order_logs = collect($seen_order_logs)->pluck('reference')->toArray();

		$comments = DB::connection('mysql')->table('tabComment')->where('reference_name', $id)
			->where('comment_type', 'Comment')->select('creation', 'comment_by', 'content')
			->orderBy('creation', 'desc')->get();

		$files = DB::connection('mysql')->table('tabFile')->where('attached_to_doctype', $ref_type == 'SO' ? 'Sales Order' : 'Material Request')->where('attached_to_name', $id)->get();

		return view('modals.view_order_modal_content', compact('details', 'ref_type', 'items_production_orders', 'item_list', 'default_boms', 'item_images', 'seen_logs_per_order', 'comments', 'actual_delivery_date_per_item', 'picking_slip_arr', 'files', 'current_inventory_arr'));
	}
	
	// /get_order_list
	public function getOrderList(Request $request) {
		$date_approved = $request->date_approved ? explode(' - ', $request->date_approved) : [];

		$start_date = $end_date = null;
		try {
			$start_date = Carbon::parse($date_approved[0])->startOfDay();
			$end_date = Carbon::parse($date_approved[1])->endOfDay();
		} catch (\Throwable $th) {
			$start_date = $end_date = null;
			$date_approved = [];
		}

		$sort_by = $request->sort_by ? $request->sort_by : 'date_approved';
		$order_by = $request->order_by ? $request->order_by : 'desc';

		$erp_db = ENV('DB_DATABASE_ERP');
		$mes_db = ENV('DB_DATABASE_MES');

		$material_requests = DB::table($erp_db.'.tabMaterial Request as mr')
			->when(isset($request->reschedule), function ($q) use ($mes_db){
				return $q->join($mes_db.'.delivery_date as dd', 'dd.reference_no', 'mr.name');
			})
			->where('mr.docstatus', 1)
			->whereIn('mr.custom_purpose', ['Manufacture', 'Sample Order', 'Consignment Order'])
			->where('mr.status', '!=', 'Stopped')
			->when(!$request->q, function ($query) use ($request) {
				return $query->where('mr.per_ordered', '<', 100);
            })
			->when($request->q, function ($query) use ($request) {
				return $query->where(function($q) use ($request) {
					$q->where('mr.name', 'LIKE', '%'.$request->q.'%')
					->orWhere('mr.customer', 'LIKE', '%'.$request->q.'%')
					->orWhere('mr.project', 'LIKE', '%'.$request->q.'%')
					->orWhere('mr.custom_purpose', 'LIKE', '%'.$request->q.'%');
				});
            })
			->when($request->status, function ($query) use ($request) {
				return $query->where('mr.status', $request->status);
            })
			->when($request->order_types, function ($query) use ($request) {
				return $query->whereIn('mr.custom_purpose', $request->order_types);
            })
			->when($date_approved, function ($q) use ($start_date, $end_date){
				return $q->whereDate('mr.modified', '>=', $start_date)->whereDate('mr.modified', '<=', $end_date);
			})
			->when(isset($request->reschedule), function ($q){
				return $q->whereDate(DB::raw('IFNULL(dd.rescheduled_delivery_date, mr.delivery_date)'), '<', Carbon::now()->startOfDay())
					->select('mr.name', 'mr.creation', 'mr.customer', 'mr.project', 'mr.delivery_date', 'mr.custom_purpose as order_type', 'mr.status', 'mr.modified as date_approved', DB::raw('CONCAT(mr.address_line, " ", mr.address_line2, " ", mr.city_town)  as shipping_address'), 'mr.owner', 'mr.notes00 as notes', 'mr.sales_person', 'dd.rescheduled_delivery_date as reschedule_delivery_date', DB::raw('IFNULL(dd.rescheduled_delivery_date, 0) as reschedule_delivery'), 'mr.company', 'mr.modified');
			})
			->when(!isset($request->reschedule), function ($q){
				return $q->select('mr.name', 'mr.creation', 'mr.customer', 'mr.project', 'mr.delivery_date', 'mr.custom_purpose as order_type', 'mr.status', 'mr.modified as date_approved', DB::raw('CONCAT(mr.address_line, " ", mr.address_line2, " ", mr.city_town)  as shipping_address'), 'mr.owner', 'mr.notes00 as notes', 'mr.sales_person', 'mr.delivery_date as reschedule_delivery_date', DB::raw('IFNULL(mr.delivery_date, 0) as reschedule_delivery'), 'mr.company', 'mr.modified');
			});
			
		$list = DB::connection('mysql')->table('tabSales Order as so')
			->where('so.docstatus', 1)
			->whereIn('so.sales_type', ['Regular Sales', 'Sales DR'])
			->where('so.status', '!=', 'Closed')
			->when(!$request->q, function ($query) use ($request) {
				return $query->where('so.per_delivered', '<', 100);
            })
			->when($request->q, function ($query) use ($request) {
				return $query->where(function($q) use ($request) {
					$q->where('so.name', 'LIKE', '%'.$request->q.'%')
						->orWhere('so.customer', 'LIKE', '%'.$request->q.'%')
						->orWhere('so.project', 'LIKE', '%'.$request->q.'%')
						->orWhere('so.sales_type', 'LIKE', '%'.$request->q.'%');
				});
            })
			->when($request->status, function ($query) use ($request) {
				return $query->where('so.status', $request->status);
            })
			->when($request->order_types && !in_array('Customer Order', $request->order_types), function ($query) use ($request) {
				return $query->whereIn('so.sales_type', $request->order_types);
            })
			->when(isset($request->reschedule), function ($q){
				return $q->whereDate(DB::raw('CASE WHEN so.reschedule_delivery = 1 THEN so.reschedule_delivery_date ELSE so.delivery_date END'), '<', Carbon::now()->startOfDay());
			})
			->when($date_approved, function ($q) use ($start_date, $end_date){
				return $q->whereDate('so.date_approved', '>=', $start_date)->whereDate('so.date_approved', '<=', $end_date);
			})
			->select('so.name', 'so.creation', 'so.customer', 'so.project', 'so.delivery_date', 'so.sales_type as order_type', 'so.status', 'so.date_approved', 'so.shipping_address', 'so.owner', 'so.notes', 'so.sales_person', 'so.reschedule_delivery_date', 'so.reschedule_delivery', 'so.company', 'so.modified')
			->unionAll($material_requests);

		if(!$request->sort_by){
			$list = $list->orderBy('modified', 'desc');
		}
			
		$list = $list->orderBy($sort_by, $order_by)->paginate(15);

		$order_list = collect($list->items());
		if(!isset($request->reschedule)){
			$mreq_arr = collect($list->items())->map(function ($q){
				if(explode('-', $q->name)[0] == 'MREQ'){
					return $q->name;
				}
			})->filter()->values()->all();
	
			$rescheduled_mreq = DB::connection('mysql_mes')->table('delivery_date')->whereIn('reference_no', $mreq_arr)->distinct()->select('reference_no', 'delivery_date', 'rescheduled_delivery_date')->get();
			$rescheduled_mreq = collect($rescheduled_mreq)->groupBy('reference_no');
	
			$order_list = collect($list->items())->map(function ($q) use ($rescheduled_mreq){
				if(isset($rescheduled_mreq[$q->name]) && $rescheduled_mreq[$q->name][0]->rescheduled_delivery_date){
					$q->reschedule_delivery_date = $rescheduled_mreq[$q->name][0]->rescheduled_delivery_date;
					$q->reschedule_delivery = 1;
				}
	
				return $q;
			});
		}

		// get items
		$references = collect($order_list)->pluck('name');
		$material_request_items = DB::connection('mysql')->table('tabMaterial Request Item')->whereIn('parent', $references)
			->select('item_code', 'description', 'qty', 'stock_uom', 'idx', 'parent', 'schedule_date as delivery_date', 'name', 'ordered_qty as delivered_qty', 'item_code as item_note');

		$item_list = DB::connection('mysql')->table('tabSales Order Item')->whereIn('parent', $references)
			->select('item_code', 'description', 'qty', 'stock_uom', 'idx', 'parent', 'delivery_date', 'name', 'delivered_qty', 'item_note')
			->unionAll($material_request_items)->orderBy('idx', 'asc')->get();

		$item_codes = collect($item_list)->pluck('item_code')->unique();

		$item_images = DB::connection('mysql')->table('tabItem Images')->whereIn('parent', $item_codes)->pluck('image_path', 'parent')->toArray();

		$default_boms = DB::connection('mysql')->table('tabBOM')
			->whereIn('item', $item_codes)->where('docstatus', 1)->where('is_active', 1)
			->select('item', 'is_default', 'name')->orderBy('is_default', 'desc')
			->orderBy('creation', 'desc')->get();

		$default_boms = collect($default_boms)->groupBy('item')->toArray();

		$item_list = collect($item_list)->groupBy('parent')->toArray();

		$production_orders = DB::connection('mysql_mes')->table('production_order')
			->whereIn('item_code', $item_codes)->whereIn(DB::raw('IFNULL(sales_order, material_request)'), $references)
			->select('production_order', 'item_code', DB::raw('IFNULL(sales_order, material_request) as reference'), 'qty_to_manufacture', 'feedback_qty', 'status', 'produced_qty')
			->get();

		$items_production_orders = [];
		foreach ($production_orders as $r) {
			$p_status = $r->produced_qty > $r->feedback_qty ? 'Ready for Feedback' : $r->status;
			$p_status = $r->qty_to_manufacture == $r->feedback_qty ? 'Feedbacked' : $p_status;
			$items_production_orders[$r->reference][$r->item_code][] = [
				'production_order' => $r->production_order,
				'status' => $p_status,
				'produced_qty' => $r->produced_qty
			];
		}

		$prod_statuses = collect($production_orders)->groupBy('reference')->toArray();
		$order_production_status = [];
		foreach ($prod_statuses as $i => $r) {
			$total_qty =  collect($r)->sum('qty_to_manufacture');
			$total_feedback_qty =  collect($r)->sum('feedback_qty');
			$percentage = ($total_feedback_qty/$total_qty) * 100;
			$has_in_progress = array_filter($r, function ($var) {
				return ($var->produced_qty > 0);
			});
			$has_in_progress = collect($r)->where('status', 'In Progress')->count();
			$has_in_progress += collect($has_in_progress)->count();
			$order_production_status[$i]['percentage'] = number_format($percentage);
			$order_production_status[$i]['has_in_progress'] = $has_in_progress;
		}

		$seen_order_logs = DB::connection('mysql_mes')->table('activity_logs')
			->where('created_by', Auth::user()->email)->whereIn('reference', $references)->where('action', 'View Order')->orderBy('created_at', 'desc')->get();
		$seen_logs_per_order = collect($seen_order_logs)->groupBy('reference')->toArray();
		$seen_order_logs = collect($seen_order_logs)->pluck('reference')->toArray();

		$reschedule_reason = DB::connection('mysql_mes')->table('delivery_reschedule_reason')->select('reschedule_reason_id as id', 'reschedule_reason as reason')->get();

		return view('tables.tbl_order_list', compact('list', 'item_list', 'default_boms', 'items_production_orders', 'order_production_status', 'seen_logs_per_order', 'seen_order_logs', 'reschedule_reason', 'item_images', 'order_list'));
	}

	public function reschedule_delivery(Request $request, $id){
		DB::connection('mysql')->beginTransaction();
		DB::connection('mysql_mes')->beginTransaction();
		try {
			$table = explode('-', $id)[0] == 'SO' ? 'tabSales Order' : 'tabMaterial Request';
			$so_details = DB::connection('mysql')->table($table)->where('name', $id)->first();
			$delivery_date_details = DB::connection('mysql_mes')->table('delivery_date')->where('reference_no', $id)->first();

			if(!$so_details){
				return redirect()->back()->with('error', 'Sales Order not found.');
			}

			$delivery_date_column = $table == 'tabSales Order' ? 'delivery_date' : 'schedule_date';
			$so_items = DB::connection('mysql')->table($table.' Item')->where('parent', $id)->select('name', 'item_code', 'description', 'qty', 'stock_uom', $delivery_date_column)->get();

			$reason = DB::connection('mysql_mes')->table('delivery_reschedule_reason')->where('reschedule_reason_id', $request->reason)->pluck('reschedule_reason')->first();

			if($table == 'tabSales Order'){
				DB::connection('mysql')->table('tabSales Order')->where('name', $id)->update([
					'reschedule_delivery' => 1,
					'reschedule_delivery_date' => $request->rescheduled_date,
					'modified' => Carbon::now()->toDateTimeString(),
					'modified_by' => Auth::user()->email
				]);
			}

			DB::connection('mysql')->table($table.' Item')->where('parent', $id)->update([
				'reschedule_delivery' => 1,
				'rescheduled_delivery_date' => $request->rescheduled_date,
				'modified' => Carbon::now()->toDateTimeString(),
				'modified_by' => Auth::user()->email
			]);

			if(!$delivery_date_details){
				foreach ($so_items as $so_item) {
					DB::connection('mysql_mes')->table('delivery_date')->insert([
						'erp_reference_id' => $so_item->name,
						'reference_no' => $id,
						'parent_item_code' => $so_item->item_code,
						'delivery_date' => $so_item->$delivery_date_column,
						'rescheduled_delivery_date' => $request->rescheduled_date,
						'created_by' => Auth::user()->email,
						'created_at' => Carbon::now()->toDateTimeString(),
						'last_modified_at' => Carbon::now()->toDateTimeString(),
						'last_modified_by' => Auth::user()->email
					]);
				}

				$delivery_date_details = DB::connection('mysql_mes')->table('delivery_date')->where('reference_no', $id)->first();
			}else{
				DB::connection('mysql_mes')->table('delivery_date')->where('reference_no', $id)->update([
					'rescheduled_delivery_date' => $request->rescheduled_date,
					'last_modified_at' => Carbon::now()->toDateTimeString(),
					'last_modified_by' => Auth::user()->email
				]);
			}

			$previous_delivery_date = $request->previous_date ? $request->previous_date : $delivery_date_details->delivery_date;

			DB::connection('mysql_mes')->table('delivery_date_reschedule_logs')->insert([
				'delivery_date_id' => $delivery_date_details->delivery_date_id,
				'previous_delivery_date' => $previous_delivery_date,
				'reschedule_reason_id' => $request->reason,
				'rescheduled_by' => Auth::user()->employee_name,
				'remarks' => $request->remarks,
				'created_at' => Carbon::now()->toDateTimeString(),
				'created_by' => Auth::user()->email,
				'last_modified_at' => Carbon::now()->toDateTimeString(),
				'last_modified_by' => Auth::user()->email
			]);

			DB::connection('mysql_mes')->table('activity_logs')->insert([
				'action' => 'Reschedule Delivery Date',
				'reference' => $id,
				'message' => 'Delivery date has been changed from '.Carbon::parse($previous_delivery_date)->format('M. d, Y').' to '.Carbon::parse($request->rescheduled_date)->format('M. d, Y'). ' by '.Auth::user()->employee_name,
				'created_at' => Carbon::now()->toDateTimeString(),
				'created_by' => Auth::user()->email,
				'last_modified_at' => Carbon::now()->toDateTimeString(),
				'last_modified_by' => Auth::user()->email
			]);

			$email_data = array( 
				'orig_delivery_date'	=> Carbon::parse($previous_delivery_date)->format('Y-m-d'),
				'resched_date'			=> Carbon::parse($request->rescheduled_date)->format('Y-m-d'),
				'items_arr'				=> $so_items,
				'reference'				=> $id,
				'resched_by'			=> Auth::user()->employee_name,
				'resched_reason'		=> $reason.' - '.$request->remarks,
				'customer'				=> $so_details->customer
			); 

			if($so_details->owner != "Administrator"){
				try {
					Mail::to($so_details->owner)->send(new SendMail_New_DeliveryDate_Alert($email_data)); //data_to_be_inserted_in_mail_template
					Mail::to("albert.gregorio@fumaco.local")->send(new SendMail_New_DeliveryDate_Alert($email_data)); //data_to_be_inserted_in_mail_template
					Mail::to("jave.kulong@fumaco.local")->send(new SendMail_New_DeliveryDate_Alert($email_data)); //data_to_be_inserted_in_mail_template
				} catch (\Throwable $th) {}
			}

			DB::connection('mysql')->commit();
			DB::connection('mysql_mes')->commit();

			return redirect()->back()->with('success', 'Delivery date rescheduled.');
		} catch (\Throwable $th) {
			DB::connection('mysql')->rollback();
			DB::connection('mysql_mes')->rollback();

			return redirect()->back()->with('error', 'Something went wrong. Please try again later');
		}
	}

	// /production_settings
	public function productionSettings(){
        $permissions = $this->get_user_permitted_operation();

        $operation_list=DB::connection('mysql_mes')->table('operation')->get();
		$workstation_list = DB::connection('mysql_mes')->table('workstation')
			// ->where('operation_id', $tabWorkstation->operation_id)
			->orderBy('order_no', 'asc')->get();

        return view('settings.production_settings', compact('permissions', 'operation_list', 'workstation_list'));
    }

	public function inventorySettings(){
        $permissions = $this->get_user_permitted_operation();

        $item_classification = DB::connection('mysql')->table('tabItem Classification as item_class')->get();

        $warehouse = DB::connection('mysql')->table('tabWarehouse')
			->where('disabled', 0)->where('is_group', 0)->where('company', 'FUMACO Inc.')->get();

        $warehouse_wip = DB::connection('mysql')->table('tabWarehouse')->where('company', 'FUMACO Inc.')->where('disabled', 0)->where('is_group', 0)->get();

        $operation_list = DB::connection('mysql_mes')->table('operation')->get();
    
        $mes_users = DB::connection('mysql_mes')->table('user')->pluck('employee_name', 'user_access_id');

		$uom_list = DB::connection('mysql_mes')->table('uom')->get();

        $material_types = DB::connection('mysql')->table('tabItem Attribute Value')
            ->where('parent', 'like', '%materials%')->distinct()->pluck('attribute_value');

        return view('settings.inventory_settings', compact('permissions', 'warehouse_wip', 'item_classification', 'warehouse', 'operation_list', 'mes_users', 'uom_list', 'material_types'));
    }

	public function qaSettings(){
        $permissions = $this->get_user_permitted_operation();

        $reject_category = DB::connection('mysql_mes')->table('reject_category')->get();
		
        return view('settings.qa_settings', compact('permissions', 'reject_category'));
    }

	public function userSettings(){
        $permissions = $this->get_user_permitted_operation();
                
        $employees = DB::connection('mysql_essex')->table('users')->where('user_type', 'Employee')
            ->where('status', 'Active')->get();
        $user_group_arr = DB::connection('mysql_mes')->table('user_group')->select('module', 'user_role')->get();
		
		$module = collect($user_group_arr)->pluck('module')->unique();
		$user_group = collect($user_group_arr)->groupBy('module');

        $operations = DB::connection('mysql_mes')->table('operation')->get();

        return view('settings.user_settings', compact('permissions', 'module', 'employees', 'operations', 'user_group'));
    }

	public function updateParentCode($production_order, Request $request) {
		DB::connection('mysql_mes')->beginTransaction();
		try {
			$production_det = DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->first();
			if (!$production_det) {
				return response()->json(['status' => 0, 'message' => 'Production order' . $production_order . ' not found.', 'production_order' => $production_order]);
			}

			DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->update([
				'parent_item_code' => $request->parent_item_code,
				'last_modified_at' => Carbon::now()->toDateTimeString(),
				'last_modified_by' => Auth::user()->employee_name
			]);

			DB::connection('mysql_mes')->commit();

			return response()->json(['status' => 1, 'message' => 'Parent code for ' . $production_order . ' has been updated.', 'production_order' => $production_order]);
		} catch (Exception $th) {
			DB::connection('mysql_mes')->rollback();

			return response()->json(['status' => 0, 'message' => 'Something went wrong. Please reload the page and try again.', 'production_order' => $production_order]);
		}
	}

	public function rejectionListToday() {
		$now = Carbon::now();
		$list = DB::connection('mysql_mes')->table('quality_inspection as q')
			->join('reject_reason as rr', 'q.qa_id', 'rr.qa_id')
			->join('reject_list as rl', 'rr.reject_list_id', 'rl.reject_list_id')
			->join('job_ticket as j', 'j.job_ticket_id', 'rr.job_ticket_id')
			->where('q.qa_inspection_type', 'Reject Confirmation')
			->whereBetween('q.created_at', [$now->startOfDay()->format('Y-m-d'), $now->endOfDay()->format('Y-m-d')])
			->select('q.qa_id', 'rl.reject_reason', 'q.rejected_qty', 'j.workstation', 'q.created_by')
			->orderBy('q.qa_inspection_date', 'desc')->get();

		return view('dashboard_rejection', compact('list'));
	}

	public function createViewOrderLog(Request $request) {
		$existing = DB::connection('mysql_mes')->table('activity_logs')->where('action', 'View Order')->where('reference', $request->order_no)->where('created_by', Auth::user()->email)->exists();
		if (!$existing) {
			DB::connection('mysql_mes')->table('activity_logs')->insert([
				'action' => 'View Order',
				'reference' => $request->order_no,
				'message' => $request->order_no . ' has been viewed by ' . Auth::user()->employee_name . ' on ' . Carbon::now()->toDateTimeString(),
				'created_at' => Carbon::now()->toDateTimeString(),
				'created_by' => Auth::user()->email
			]);
		}
	}

	public function dashboardOperatorOutput() {
		$now = Carbon::now();
		$q = DB::connection('mysql_mes')->table('time_logs as t')->join('job_ticket as j', 'j.job_ticket_id', 't.job_ticket_id')
			->join('production_order as p', 'j.production_order', 'p.production_order')->where('t.status', 'Completed')
			->whereNotNull('t.operator_id')->whereDate('t.from_time', '>=', $now->startOfDay())->whereDate('t.to_time', '<=', $now->endOfDay())
			->selectRaw('t.operator_id, t.operator_name, t.good, j.workstation, p.operation_id, t.reject')
			->orderBy('t.from_time', 'desc')->get();

		$fabrication_op_output = $painting_op_output = $assembly_op_output = [];
		foreach ($q as $e) {
			if ($e->operation_id == 1 && $e->workstation != 'Painting') {
				if (array_key_exists($e->operator_id, $fabrication_op_output)) {
					$fabrication_op_output[$e->operator_id]['output'] += $e->good;
					$fabrication_op_output[$e->operator_id]['reject'] += $e->reject;
				} else {
					$fabrication_op_output[$e->operator_id] = ['operator_name' => $e->operator_name, 'output' => $e->good, 'reject' => $e->reject];
				}
			}

			if ($e->workstation == 'Painting') {
				if (array_key_exists($e->operator_id, $painting_op_output)) {
					$painting_op_output[$e->operator_id]['output'] += $e->good;
					$painting_op_output[$e->operator_id]['reject'] += $e->reject;
				} else {
					$painting_op_output[$e->operator_id] = ['operator_name' => $e->operator_name, 'output' => $e->good];
					$painting_op_output[$e->operator_id] = ['operator_name' => $e->operator_name, 'output' => $e->good, 'reject' => $e->reject];
				}
			}

			if ($e->operation_id == 3) {
				if (array_key_exists($e->operator_id, $assembly_op_output)) {
					$assembly_op_output[$e->operator_id]['output'] += $e->good;
					$assembly_op_output[$e->operator_id]['reject'] += $e->reject;
				} else {
					$assembly_op_output[$e->operator_id] = ['operator_name' => $e->operator_name, 'output' => $e->good];
					$assembly_op_output[$e->operator_id] = ['operator_name' => $e->operator_name, 'output' => $e->good, 'reject' => $e->reject];
				}
			}
		}

		$fabrication_data = $painting_data = $assembly_data = [];
		$fabrication_max_output = collect($fabrication_op_output)->max('output');
		foreach (collect($fabrication_op_output)->sortBy('output')->reverse() as $v) {
			$output_percentage = ($v['output']/$fabrication_max_output) * 100;
			$reject_percentage = ($v['reject']/$fabrication_max_output) * 100;
			$fabrication_data[] = [
				'operator_name' => $this->splitName($v['operator_name']),
				'percentage' => $output_percentage,
				'reject_percentage' => $reject_percentage,
				'output' => number_format($v['output']),
				'reject' => number_format($v['reject']),
			];
		}

		$painting_max_output = collect($painting_op_output)->max('output');
		foreach (collect($painting_op_output)->sortBy('output')->reverse() as $v) {
			$output_percentage = ($v['output']/$painting_max_output) * 100;
			$reject_percentage = ($v['reject']/$painting_max_output) * 100;
			$painting_data[] = [
				'operator_name' => $this->splitName($v['operator_name']),
				'percentage' => $output_percentage,
				'reject_percentage' => $reject_percentage,
				'output' => number_format($v['output']),
				'reject' => number_format($v['reject']),
			];
		}

		$assembly_max_output = collect($assembly_op_output)->max('output');
		foreach (collect($assembly_op_output)->sortBy('output')->reverse() as $v) {
			$output_percentage = ($v['output']/$assembly_max_output) * 100;
			$reject_percentage = ($v['reject']/$assembly_max_output) * 100;
			$assembly_data[] = [
				'operator_name' => $this->splitName($v['operator_name']),
				'percentage' => $output_percentage,
				'reject_percentage' => $reject_percentage,
				'output' => number_format($v['output']),
				'reject' => number_format($v['reject']),
			];
		}

		$data = [
			'fabrication' => $fabrication_data,
			'painting' => $painting_data,
			'assembly' => $assembly_data,
		];

		return view('dashboard_operator_output', compact('data', 'q'));
	}

	private function splitName($name){
		$names = explode(' ', $name);
		$firstname = substr($name, 0, 1);
		$lastname = $names[count($names) - 1];
		$lastname = $this->clean($lastname);
		if (in_array(strtolower($lastname), ['jr', 'sr'])) {
			$lastname = $names[count($names) - 2] . ' ' . $this->clean($names[count($names) - 1]) . '.';
		}

		return $firstname . '. ' . $lastname;
	}

	private function clean($string) {
		$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
		$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	 
		return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
	}

	public function printOrder($order_id) {
		$ref_type = explode("-", $order_id)[0];

		if ($ref_type == 'SO') {
			$details = DB::connection('mysql')->table('tabSales Order')->where('name', $order_id)
				->select('name', 'transaction_date', 'customer', 'project', 'delivery_date', 'status', 'sales_type', 'shipping_address', 'notes')->first();

			$items = DB::connection('mysql')->table('tabSales Order Item')->where('parent', $order_id)->orderBy('idx', 'asc')->get();
		} else {
			$details = DB::connection('mysql')->table('tabMaterial Request')->where('name', $order_id)
				->select('name', 'customer', 'project', 'custom_purpose as sales_type', 'transaction_date', 'delivery_date', 'status', DB::raw('CONCAT(address_line, " ", address_line2, " ", city_town)  as shipping_address'), 'notes00 as notes')->first();

			$items = DB::connection('mysql')->table('tabMaterial Request Item')->where('parent', $order_id)
				->select('item_code', 'description', 'idx', 'qty', 'stock_uom', 'schedule_date as delivery_date')->orderBy('idx', 'asc')->get();
		}

		return view('print_order', compact('items', 'details', 'ref_type'));
	}

	public function checkNewOrders() {
		$start = Carbon::now()->subMinutes(5);
		$end = Carbon::now();

		$latest_material_requests = DB::connection('mysql')->table('tabMaterial Request')
			->where('docstatus', 1)->whereIn('custom_purpose', ['Manufacture', 'Sample Order', 'Consignment Order'])
			->where('status', '!=', 'Stopped')->select('name', 'modified')->orderBy('modified', 'desc')->first();

		$has_production_order = DB::connection('mysql_mes')->table('production_order')->where('material_request', $latest_material_requests->name)->exists();
		if (!$has_production_order) {
			$check = Carbon::parse($latest_material_requests->modified)->between($start, $end);
			if ($check) {
				return response()->json(true);
			}
		}

		$latest_sales_orders = DB::connection('mysql')->table('tabSales Order')
			->where('docstatus', 1)->whereIn('sales_type', ['Regular Sales', 'Sales DR'])
			->where('status', '!=', 'Closed')->select('name', 'modified')->orderBy('modified', 'desc')->first();

		$has_production_order = DB::connection('mysql_mes')->table('production_order')->where('sales_order', $latest_sales_orders->name)->exists();
		if (!$has_production_order) {
			$check = Carbon::parse($latest_sales_orders->modified)->between($start, $end);
			if ($check) {
				return response()->json(true);
			}
	
			return response()->json(false);
		}
	}

	// id = production order no
	public function syncJobTicket($id) {
		DB::connection('mysql')->beginTransaction();
		DB::connection('mysql_mes')->beginTransaction();
		try {
			$production_order = DB::connection('mysql_mes')->table('production_order')->where('production_order', $id)->first();
			if (in_array($production_order->status, ['Feedbacked', 'Partially Feedbacked'])) {
				return response()->json(['status' => 0, 'message' => 'Production order has been already feedbacked.']);
			}

			if ($production_order->feedback_qty > 0) {
				return response()->json(['status' => 0, 'message' => 'Production order has been already feedbacked.']);
			}

			$job_ticket = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $id)->get();
			$job_ticket_ids = collect($job_ticket)->pluck('job_ticket_id');

			$time_logs = DB::connection('mysql_mes')->table('time_logs')->whereIn('job_ticket_id', $job_ticket_ids)->get();
			$time_logs_by_job_ticket = collect($time_logs)->groupBy('job_ticket_id')->toArray();

			$spotwelding_time_logs = DB::connection('mysql_mes')->table('spotwelding_qty')->whereIn('job_ticket_id', $job_ticket_ids)->get();
			$spotwelding_time_logs_by_job_ticket = collect($spotwelding_time_logs)->groupBy('job_ticket_id')->toArray();

			foreach ($job_ticket as $key => $value) {
				$logs = [];
				$total_reject = $total_good = 0;
				$job_ticket_id = $value->job_ticket_id;
				// get total good, total reject, actual start and end date
				if ($value->workstation == 'Spotwelding') {
					if (array_key_exists($job_ticket_id, $spotwelding_time_logs_by_job_ticket)) {
						$logs = collect($spotwelding_time_logs_by_job_ticket[$job_ticket_id]);
						$bom_parts = $this->get_production_order_bom_parts($value->production_order);
			
						$spotwelding_parts = DB::connection('mysql_mes')->table('spotwelding_part')->where('housing_production_order', $value->production_order)->get();
						$spotwelding_part_codes = collect($spotwelding_parts)->groupBy('part_code')->toArray();
						$spotwelding_parts = collect($spotwelding_parts)->groupBy('spotwelding_part_id')->toArray();
			
						if (count(array_diff(array_column($bom_parts, 'item_code'), array_keys($spotwelding_part_codes))) <= 0) {
							$total_good_spotwelding = DB::connection('mysql_mes')->table('spotwelding_qty')->whereIn('spotwelding_part_id', array_keys($spotwelding_parts))
								->where('job_ticket_id', $job_ticket_id)->selectRaw('spotwelding_part_id, SUM(good) as total_good, SUM(reject) as total_reject')->groupBy('spotwelding_part_id')
								->where('status', 'Completed')->get();
								
							$total_good_spotwelding = collect($total_good_spotwelding)->map(function ($q){
								return $q->total_good;
							})->min();
						} else {
							$total_good_spotwelding = 0;
						}
							
						$total_reject = $value->reject;
						$total_good = $total_good_spotwelding;
					}
				} else {
					if (array_key_exists($job_ticket_id, $time_logs_by_job_ticket)) {
						$logs = collect($time_logs_by_job_ticket[$job_ticket_id]);
						
						$total_good = collect($logs)->where('status', 'Completed')->sum('good');
						$total_reject = collect($logs)->where('status', 'Completed')->sum('reject');
					}
				}

				$job_ticket_actual_start_date = $job_ticket_actual_end_date = null;
				if (collect($logs)->count() > 0) {       
					$job_ticket_actual_start_date = collect($logs)->min('from_time');
					$job_ticket_actual_end_date = collect($logs)->max('to_time');
				}

				// update job ticket details
				$job_ticket_values = [
					'actual_start_date' => $job_ticket_actual_start_date,
					'actual_end_date' => $job_ticket_actual_end_date,
					'last_modified_by' => Auth::check() ? Auth::user()->employee_name : null,
					'last_modified_at' => Carbon::now()->toDateTimeString(),
				];

				if ($total_good > 0) {
					if ($total_good != $value->good || $total_good != $value->completed_qty) {
						$job_ticket_values['good'] = $total_good;
						$job_ticket_values['completed_qty'] = $total_good;
					}
				}

				if ($total_reject > 0) {
					if ($total_reject != $value->reject) {
						$job_ticket_values['reject'] = $total_good;
					}
				}

				$job_ticket_values['status'] = 'Pending';
				if (count($logs) > 0) {
					$job_ticket_values['status'] = 'In Progress';
					if ($total_good >= $production_order->qty_to_manufacture) {
						$job_ticket_values['status'] = 'Completed';
					}
				}

				DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)
					->update($job_ticket_values);
			}

			$production_order_values = [
				'actual_start_date' => $job_ticket_actual_start_date,
				'actual_end_date' => $job_ticket_actual_end_date,
				'last_modified_by' => Auth::check() ? Auth::user()->employee_name : null,
				'last_modified_at' => Carbon::now()->toDateTimeString(),
				'status' => $production_order->status
			];

			$job_ticket = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $id)->get();

			$produced_qty = collect($job_ticket)->min('completed_qty');

			$production_order_values['produced_qty'] = $produced_qty;
			$production_order_values['actual_start_date'] = collect($job_ticket)->min('actual_start_date');
			$production_order_values['actual_end_date'] = collect($job_ticket)->max('actual_end_date');

			$completed_job_ticket = collect($job_ticket)->where('status', 'Completed')->count();
			if (count($job_ticket) == $completed_job_ticket) {
				$production_order_values['status'] = 'Completed';
			}

			$wip_job_ticket = collect($job_ticket)->where('status', 'In Progress')->count();
			if ($wip_job_ticket) {
				$production_order_values['status'] = 'In Progress';
			}

			$pending_job_ticket = collect($job_ticket)->where('status', 'Pending')->count();
			if (count($job_ticket) == $pending_job_ticket) {
				$production_order_values['status'] = 'Not Started';
			}

			DB::connection('mysql_mes')->table('production_order')->where('production_order', $id)
				->update($production_order_values);

			// for erp
			$values = [
				'actual_start_date' => $production_order_values['actual_start_date'],
				'actual_end_date' => $production_order_values['actual_end_date']
			];

			$values['status'] = ($production_order_values['status'] == 'In Progress') ? 'In Process' : $production_order_values['status'];

			DB::connection('mysql')->table('tabWork Order')->where('name', $id)
				->update($values);

			DB::connection('mysql')->commit();
			DB::connection('mysql_mes')->commit();

			return response()->json(['status' => 1, 'message' => 'Production order has been updated.']);
		} catch (Exception $e) {
			DB::connection('mysql')->rollback();
			DB::connection('mysql_mes')->rollback();

			return response()->json(['status' => 0, 'message' => 'Something went wrong. Please try again.']);
		}
	}

	public function viewDeliveryList(Request $request, $date) {
		$permissions = $this->get_user_permitted_operation();

		$start_date = Carbon::parse($date)->startOfWeek();
		$end_date = Carbon::parse($date)->endOfWeek();

		$erp_db = env('DB_DATABASE_ERP');
		$mes_db = env('DB_DATABASE_MES');

		$material_requests = DB::table($erp_db.'.tabMaterial Request as mr')
			->join($erp_db.'.tabMaterial Request Item as mri', 'mr.name', 'mri.parent')
			->join($mes_db.'.production_order as po', 'po.material_request', 'mr.name')
			->whereRaw('po.item_code = mri.item_code')
			->whereNotIn('po.status', ['Stopped', 'Cancelled', 'Closed'])
			->where('mr.docstatus', 1)->whereIn('mr.custom_purpose', ['Manufacture', 'Sample Order', 'Consignment Order'])->where('mr.status', '!=', 'Stopped')->where('mr.per_ordered', '<', 100)
			->when($request->search_string, function ($query) use ($request){
				$search_str = explode(' ', $request->search_string);
				return $query->where(function($q) use ($search_str, $request) {
                    foreach ($search_str as $str) {
                        $q->where('mri.description', 'LIKE', "%".$str."%");
                    }
                })->orWhere('mri.item_code', 'LIKE', '%'.$request->search_string.'%');
			})
			->when($request->project, function ($q) use ($request){
				return $q->where('mr.project', $request->project);
			})
			->when($request->reference, function ($q) use ($request){
				return $q->where('mr.name', $request->reference);
			})
			->when($request->customer, function ($q) use ($request){
				return $q->where('mr.customer', $request->customer);
			})
			->whereRaw('IF(mri.reschedule_delivery, mri.rescheduled_delivery_date, mr.delivery_date) BETWEEN "'.$start_date.'" AND "'.$end_date.'"')
			->select('mr.name', 'mr.customer', 'mr.project', 'mri.item_code', 'mri.description', 'mri.qty', 'mri.uom', 'mr.delivery_date', 'mri.rescheduled_delivery_date', 'mri.reschedule_delivery', 'mr.owner', 'po.production_order', 'po.feedback_qty', 'po.status')
			->groupBy('mr.name', 'mr.customer', 'mr.project', 'mri.item_code', 'mri.description', 'mri.qty', 'mri.uom', 'mr.delivery_date', 'mri.rescheduled_delivery_date', 'mri.reschedule_delivery', 'mr.owner', 'po.production_order', 'po.feedback_qty', 'po.status');

		$query = DB::table($erp_db.'.tabSales Order as so')
			->join($erp_db.'.tabSales Order Item as soi', 'so.name', 'soi.parent')
			->join($mes_db.'.production_order as po', 'po.sales_order', 'so.name')
			->whereRaw('po.item_code = soi.item_code')
			->whereNotIn('po.status', ['Stopped', 'Cancelled', 'Closed'])->whereNotIn('so.status', ['Stopped', 'Cancelled', 'Closed', 'Completed'])->where('so.per_delivered', '<', 100)->where('so.docstatus', 1)->whereRaw('soi.delivered_qty < soi.qty')
			->when($request->search_string, function ($query) use ($request){
				$search_str = explode(' ', $request->search_string);
				return $query->where(function($q) use ($search_str, $request) {
                    foreach ($search_str as $str) {
                        $q->where('soi.description', 'LIKE', "%".$str."%");
                    }
                })->orWhere('soi.item_code', 'LIKE', '%'.$request->search_string.'%');
			})
			->when($request->project, function ($q) use ($request){
				return $q->where('so.project', $request->project);
			})
			->when($request->reference, function ($q) use ($request){
				return $q->where('so.name', $request->reference);
			})
			->when($request->customer, function ($q) use ($request){
				return $q->where('so.customer', $request->customer);
			})
			->whereRaw('IF(soi.reschedule_delivery, soi.rescheduled_delivery_date, soi.delivery_date) BETWEEN "'.$start_date.'" AND "'.$end_date.'"')
			->select('so.name', 'so.customer', 'so.project', 'soi.item_code', 'soi.description', 'soi.qty', 'soi.uom', 'soi.delivery_date', 'soi.rescheduled_delivery_date', 'soi.reschedule_delivery', 'so.owner', 'po.production_order', 'po.feedback_qty', 'po.status')
			->groupBy('so.name', 'so.customer', 'so.project', 'soi.item_code', 'soi.description', 'soi.qty', 'soi.uom', 'soi.delivery_date', 'soi.rescheduled_delivery_date', 'soi.reschedule_delivery', 'so.owner', 'po.production_order', 'po.feedback_qty', 'po.status')
			->union($material_requests)->orderByRaw('IF(reschedule_delivery, rescheduled_delivery_date, delivery_date) desc')->get();

		$customers = collect($query)->pluck('customer')->unique()->filter()->sort()->values()->all();
		$projects = collect($query)->pluck('project')->unique()->filter()->sort()->values()->all();
		$reference_arr = collect($query)->pluck('name')->unique()->filter()->sort()->values()->all();

		if($request->ajax()){
			$item_images = DB::connection('mysql')->table('tabItem Images')->whereIn('parent', collect($query)->pluck('item_code'))->get();
			$item_image = collect($item_images)->groupBy('parent');

			$resched_logs = DB::connection('mysql_mes')->table('delivery_date as d')
				->join('delivery_date_reschedule_logs as rd', 'd.delivery_date_id', 'rd.delivery_date_id')
				->whereIn('d.reference_no', collect($query)->pluck('name'))->whereIn('d.parent_item_code', collect($query)->pluck('item_code'))->get();

			$sched_log = [];
			foreach ($resched_logs as $sched) {
				$sched_log[$sched->reference_no][$sched->parent_item_code][] = collect($sched);
			}

			$resched_logs = collect($resched_logs)->groupBy('delivery_date_id');

			$arr = [];
			foreach($query as $q){
				$delivery_date = $q->delivery_date;

				$previous_delivery_dates = [];
				if($q->reschedule_delivery){
					$delivery_date = $q->rescheduled_delivery_date;
					$previous_delivery_dates = isset($sched_log[$q->name][$q->item_code]) ? collect($sched_log[$q->name][$q->item_code])->sortBy('previous_delivery_date') : [];
				}

				$image_path = isset($item_image[$q->item_code]) ? 'img/'.$item_image[$q->item_code][0]->image_path : 'icon/no_img.png';

				$arr[$delivery_date][] = [
					'reference' => $q->name,
					'production_order' => $q->production_order,
					'item_code' => $q->item_code,
					'image' => 'http://athenaerp.fumaco.local/storage/'.$image_path,
					'description' => $q->description,
					'feedback_qty' => $q->feedback_qty,
					'status' => $q->status,
					'qty_to_manufacture' => $q->qty,
					'uom' => $q->uom,
					'customer' => $q->customer,
					'project' => $q->project,
					'rescheduled' => $q->reschedule_delivery,
					'previous_delivery_dates' => $previous_delivery_dates
				];
			}

			return view('reports.delivery_schedule_tbl', compact('arr', 'query', 'date'));
		}

		return view('reports.delivery_schedule_list', compact('permissions', 'projects', 'reference_arr', 'customers', 'date'));
	}

	public function viewRolePermissionsForm($user_group) {
		$settings_actions = [
			'manage-workstations' => 'Add, Edit and Delete Workstations',
			'manage-machines' => 'Add, Edit and Delete Machines',
			'manage-rescheduled-delivery-reason' => 'Add or Remove Rescheduled Delivery Reason',
			'manage-production-order-cancellation' => 'Add or Remove Reason for Production Order Cancellation',
			'manage-shifts' => 'Add, Edit and Delete Shifts / Production Working Hours Schedule',
			'manage-item-classification-source' => 'Add and configure Source Warehouse based on Item Classification',
			'manage-fast-issuance-permission' => 'Add new user that has fast issuance permission',
			'manage-wip-warehouse' => 'Assign physical warehouse as Work-in-Progress per operation',
			'manage-users' => 'Add, Edit and Delete Users',
			'manage-user-groups' => 'Add, Edit and Delete User Groups',
			'manage-email-notifications' => 'Add and Delete Email Notifications',
			'manage-role-permissions' => 'Assign Roles and Permissions',
			'reports' => 'Production Reports',
		];
		$production_planning_actions = [
			'view-incoming-orders' => 'View Incoming Orders',
			'create-production-order' => 'Create Production Order',
			'cancel-production-order' => 'Cancel Production Order',
			'close-production-order' => 'Close Production Order',
			'override-production-order' => 'Override Production Order',
			'reopen-production-order' => 'Re-open Production Order',
			'create-production-order-feedback' => 'Create Production Order Feedback',
			'cancel-production-order-feedback' => 'Cancel Production Order Feedback',
			'reschedule-delivery-date-order' => 'Reschedule Delivery Date per Order',
		];
		$material_planning_actions = [
			'create-withdrawal-slip' => 'Create Withdrawal Slip Request',
			'create-withdrawal-slip-for-production-orders-wo-bom' => 'Create Withdrawal Slip Request for Production Orders without BOM',
			'print-withdrawal-slip' => 'Print Withdrawal Slips',
			'change-production-order-items' => 'Change Production Order Items (Components/Raw Materials)',
			'fast-issue-items' => 'Initiate Fast Issue Items',
			'return-items-to-warehouse' => 'Return Items to Warehouse',
			'add-production-order-items' => 'Add new item / alternative item in Production Order Required Materials',
			'create-material-request' => 'Create Material Requests (Raw Materials Request)',
		];
		$scheduling_actions = [
			'assign-shift-schedule' => 'Assign Shift working / OT Schedule per Operation (optional)',
			'reschedule-delivery-date-production-order' => 'Reschedule Delivery Date per Production Order',
			'assign-production-order-schedule' => 'Assign Production Order Schedule / Planned Start Date',
			'assign-production-order-to-machines' => 'Assign Production Order to Conveyor / Machines',
		];
		$execution_actions = [
			'assign-bom-process' => 'Assign BOM Process / Workstations ',
			'print-job-ticket' => 'Print Job Ticket',
			'edit-operator-timelog' => 'Edit Operator Timelog',
			'reset-operator-timelog' => 'Reset Operator Timelog',
			'override-operator-timelog' => 'Override Operator Timelogs',
			'update-wip-production-order-process' => 'Update Production Order Process (during the production order process)',
		];
		$actions = [
			'Production Planning' => $production_planning_actions,
			'Materials Planning' => $material_planning_actions,
			'Scheduling' => $scheduling_actions,
			'Production Control' => $execution_actions,
			'Settings' => $settings_actions,
		];
		$existing_permissions = DB::connection('mysql_mes')->table('role_permissions')->where('user_group_id', $user_group)->pluck('permission')->toArray();
		return view('role_permissions', compact('actions', 'user_group', 'existing_permissions'));
	}
	public function saveRolePermissions($user_group, Request $request) {
		if (Gate::denies('manage-role-permissions')) {
            return response()->json(['status' => 0, 'message' => 'Unauthorized.']);
        }

		DB::connection('mysql_mes')->beginTransaction();
		try {
			$current_timestamp = Carbon::now()->toDateTimeString();
			$user = Auth::user()->email;
			$requested_permissions = $request->permission;
			if ($requested_permissions) {
				$requested_permissions = Arr::where($requested_permissions, function ($value, $key) {
					return $value == 1;
				});
	
				// delete permission not included in the $requested_permissions
				DB::connection('mysql_mes')->table('role_permissions')
					->where('user_group_id', $user_group)
					->whereNotIn('permission', array_keys($requested_permissions))
					->delete();
	
				// existing permissions
				$existing_permissions = DB::connection('mysql_mes')->table('role_permissions')
					->where('user_group_id', $user_group)->pluck('permission')->toArray();
		
				$permissions = [];
				foreach($requested_permissions as $permission => $value) {
					if (!in_array($permission, $existing_permissions)) {
						$permissions[] = [
							'user_group_id' => $user_group,
							'permission' => $permission,
							'modified_by' => $user,
							'last_modified_at' => $current_timestamp
						];
					}
				}
				// save permissions
				DB::connection('mysql_mes')->table('role_permissions')->insert($permissions);			
	
			}else{
				DB::connection('mysql_mes')->table('role_permissions')
					->where('user_group_id', $user_group)
					->delete();
			}

			DB::connection('mysql_mes')->commit();
			return response()->json(['status' => 1, 'message' => 'Role Permission has been saved.']);
		} catch (\Throwable $th) {
			DB::connection('mysql_mes')->rollback();
			
			return response()->json(['status' => 0, 'message' => 'An error occured. Please contact your system administrator.']);
		}
	}

	public function viewRoleUsers(Request $request) {
		$users = [];
		if ($request->role) {
			$users = DB::connection('mysql_mes')->table('user as u')
				->join('user_group as ug', 'ug.user_group_id', 'u.user_group_id')
				->join('operation as o', 'o.operation_id', 'u.operation_id')
				->where('ug.module', $request->module)->where('ug.user_group_id', $request->role)
				->select('u.employee_name', DB::raw('GROUP_CONCAT(o.operation_name SEPARATOR ", ") as operations'), 'ug.module')
				->groupBy('u.employee_name', 'ug.module')->orderBy('o.operation_name', 'u.employee_name')->get();
		}
		
		return view('view_role_users', compact('users'));
	}
}