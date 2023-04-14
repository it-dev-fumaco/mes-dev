<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use Illuminate\Http\Request;

class ProductionFloorController extends Controller
{
	public function index(){
		return view('production_floor.index');
	}

	public function get_workstation_dashboard_content(){
		$now = Carbon::now()->format('Y-m-d');
		$open_production_orders = DB::connection('mysql_mes')->table('production_order')->whereIn('status', ['In Progress', 'Not Started'])->pluck('production_order');

		$workstation_arr = ['Shearing', 'Punching', 'Bending'];
		$workstations = DB::connection('mysql_mes')->table('workstation')->where('operation_id', 1)
			->whereIn('workstation_name', $workstation_arr)->orderBy('order_no', 'asc')->get();

		$list = [];
		foreach ($workstations as $row) {
			$qry = DB::connection('mysql_mes')->table('production_order')
				->join('job_ticket', 'job_ticket.production_order', 'production_order.production_order')
				->whereIn('production_order.production_order', $open_production_orders)
				->where('job_ticket.workstation', $row->workstation_name)
				->whereDate('job_ticket.planned_start_date', $now)
				->select('production_order.qty_to_manufacture', 'job_ticket.completed_qty', 'job_ticket.planned_start_date', 'job_ticket.job_ticket_id', 'job_ticket.production_order')
				->get();

			$job_tickets = collect($qry)->pluck('job_ticket_id');

			$total_production_for_qc = collect($qry)->unique('production_order')->count();
			$inspected_prod = DB::connection('mysql_mes')->table('job_ticket')
				->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
				->join('quality_inspection', 'quality_inspection.reference_id', 'time_logs.time_log_id')
				->where('quality_inspection.reference_type', 'Time Logs')
				->whereIn('job_ticket.job_ticket_id', $job_tickets)->distinct()->count('job_ticket.production_order');

			if($total_production_for_qc > 0){
				$qa_efficiency = ($inspected_prod / $total_production_for_qc) * 100;
			}else{
				$qa_efficiency = 0;
			}

			$total_rejects = DB::connection('mysql_mes')->table('time_logs')->whereIn('job_ticket_id', $job_tickets)->sum('reject');

			$has_in_progress = DB::connection('mysql_mes')->table('production_order')
				->join('job_ticket', 'job_ticket.production_order', 'production_order.production_order')
				->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
				->whereIn('production_order.production_order', $open_production_orders)
				->where('job_ticket.workstation', $row->workstation_name)->whereDate('job_ticket.planned_start_date', $now)
				->where('time_logs.status', 'In Progress')->exists();

			$target = collect($qry)->sum('qty_to_manufacture');
			$actual = collect($qry)->sum('completed_qty');

			$workstation_machine_count = DB::connection('mysql_mes')->table('workstation_machine')
				->join('workstation', 'workstation.workstation_id', 'workstation_machine.workstation_id')
				->where('workstation.workstation_name', $row->workstation_name)->count();

			$in_progress_workstation_machine_count = DB::connection('mysql_mes')->table('job_ticket')
				->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
				->where('job_ticket.workstation', $row->workstation_name)->where('time_logs.status', 'In Progress')
				->distinct()->count('machine_code');

			if($workstation_machine_count > 0){
				$machine_utilization = ($in_progress_workstation_machine_count / $workstation_machine_count) * 100;
			}else{
				$machine_utilization = 0;
			}

			$status = 'idle';
			if(($target > 0) && ($actual <= 0)){
				$status = 'idle';
			}

			if($has_in_progress){
				$status = 'in-process';
			}

			if($target == $actual){
				$status = 'completed';
			}

			if($target <= 0){
				$status = 'idle';
			}

			$quality = 0;
			if($actual > 0){
				$quality = ($actual / ($actual + $total_rejects)) * 100;
			}
			
			$list[] = [
				'workstation_name' => $row->workstation_name,
				'target' => $target,
				'actual' => $actual,
				'machine_utilization' => $machine_utilization,
				'status' => $status,
				'quality' => $quality,
				'rejects' => $total_rejects,
				'qa_efficiency' => $qa_efficiency
			];
		}

		$workstation_arr = ['Spotwelding', 'Dimension Checking', 'Grinding', 'Drilling'];
		$qry = DB::connection('mysql_mes')->table('production_order')
				->join('job_ticket', 'job_ticket.production_order', 'production_order.production_order')
				->whereIn('production_order.production_order', $open_production_orders)
				->whereIn('job_ticket.workstation', $workstation_arr)
				->whereDate('job_ticket.planned_start_date', $now)
				->select('production_order.qty_to_manufacture', 'job_ticket.completed_qty', 'job_ticket.planned_start_date', 'job_ticket.job_ticket_id')
				->get();


		$total_production_for_qc = collect($qry)->unique('production_order')->count();
		$inspected_prod = DB::connection('mysql_mes')->table('job_ticket')
			->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
			->join('quality_inspection', 'quality_inspection.reference_id', 'time_logs.time_log_id')
			->where('quality_inspection.reference_type', 'Time Logs')
			->whereIn('job_ticket.job_ticket_id', $job_tickets)->distinct()->count('job_ticket.production_order');

		if($total_production_for_qc > 0){
			$qa_efficiency = ($inspected_prod / $total_production_for_qc) * 100;
		}else{
			$qa_efficiency = 0;
		}

		$job_tickets = collect($qry)->pluck('job_ticket_id');
		$total_rejects = DB::connection('mysql_mes')->table('time_logs')->whereIn('job_ticket_id', $job_tickets)->sum('reject');

		$has_in_progress = DB::connection('mysql_mes')->table('production_order')
			->join('job_ticket', 'job_ticket.production_order', 'production_order.production_order')
			->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
			->whereIn('production_order.production_order', $open_production_orders)
			->whereIn('job_ticket.workstation', $workstation_arr)
			->whereDate('job_ticket.planned_start_date', $now)
			->where('time_logs.status', 'In Progress')->exists();

		$target = collect($qry)->sum('qty_to_manufacture');
		$actual = collect($qry)->sum('completed_qty');

		$workstation_machine_count = DB::connection('mysql_mes')->table('workstation_machine')
			->join('workstation', 'workstation.workstation_id', 'workstation_machine.workstation_id')
			->whereIn('workstation.workstation_name', $workstation_arr)->count();

		$in_progress_workstation_machine_count = DB::connection('mysql_mes')->table('job_ticket')
			->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
			->whereIn('job_ticket.workstation', $workstation_arr)
			->where('time_logs.status', 'In Progress')
			->distinct()->count('machine_code');

		if($workstation_machine_count > 0){
			$machine_utilization = ($in_progress_workstation_machine_count / $workstation_machine_count) * 100;
		}else{
			$machine_utilization = 0;
		}

		$status = 'idle';

		if(($target > 0) && ($actual <= 0)){
			$status = 'not-started';
		}

		if($has_in_progress){
			$status = 'in-process';
		}

		if($target == $actual){
			$status = 'completed';
		}

		if($target <= 0){
			$status = 'idle';
		}

		$quality = 0;
		if($actual > 0){
			$quality = ($actual / ($actual + $total_rejects)) * 100;
		}
		
		$list[] = [
			'workstation_name' => $workstation_arr[0],
			'target' => $target,
			'actual' => $actual,
			'machine_utilization' => $machine_utilization,
			'status' => $status,
			'quality' => $quality,
			'rejects' => $total_rejects,
			'qa_efficiency' => $qa_efficiency
		];

		return view('production_floor.workstation_dashboard_content', compact('list'));
	}

	public function get_ready_for_feedback(){
		$count_ready_feedback = DB::connection('mysql_mes')->table('production_order')->whereNotIn('status', ['Cancelled'])
			->where('produced_qty', '>', 0)->whereRaw('feedback_qty < produced_qty')->count();

		$total_qty_feedback = DB::connection('mysql_mes')->table('production_order')->whereNotIn('status', ['Cancelled'])
		->where('produced_qty', '>', 0)->whereRaw('feedback_qty < produced_qty')->get();

		$total_produced = collect($total_qty_feedback)->sum('produced_qty');
		$total_feedback = collect($total_qty_feedback)->sum('feedback_qty');

		$total_qty = $total_produced - $total_feedback;

		$data = [
			'production_orders_count' => number_format($count_ready_feedback),
			'total_qty' => number_format($total_qty),
			// 'total_feedback' => number_format($total_feedback)
		];

		return response()->json($data);
	}

	public function get_total_output(){
		$start = Carbon::now()->subDays(6)->format('Y-m-d');
		$end = Carbon::now()->format('Y-m-d');

		$period = CarbonPeriod::create($start, $end);

		$arr = [];

		// Iterate over the period
		foreach ($period as $date) {
			$start = Carbon::parse($date)->startOfDay()->toDateTimeString();
			$end = Carbon::parse($date)->endOfDay()->toDateTimeString();
			$total_output = DB::connection('mysql_mes')->table('time_logs')
				->whereBetween('from_time', [$start, $end])
				->whereBetween('to_time', [$start, $end])
				->sum('good');
			
			$arr[] = [
				'transaction_date' => $date->format('M-d'),
				'output' => $total_output
			];
		}

		return $arr;
	}

	public function activity_logs(){
		$start = Carbon::now()->startOfDay()->toDateTimeString();
		$end = Carbon::now()->endOfDay()->toDateTimeString();

		$q = DB::connection('mysql_mes')->table('production_order')
			->join('job_ticket', 'job_ticket.production_order', 'production_order.production_order')
			->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
			->whereBetween('time_logs.created_at', [$start, $end])
			->orWhereBetween('time_logs.last_modified_at', [$start, $end])
			->get();

		$logs = [];
		$message = '';
		foreach ($q as $r) {
			$stat = ($r->status == 'In Progress') ? ' started task for' : ' completed';
			$qty = ($r->status == 'Completed') ? '('. $r->good .' piece(s))' : '';
			$logs[] = [
				'message' => $r->operator_name . ' ' . $stat . ' ' . strtok($r->description, ",") . ' ' . $qty
			];
			$message .= '<span>' . $r->operator_name . ' ' . $stat . ' ' . strtok($r->description, ",") . ' ' . $qty . '</span> | ';
		}

		if(count($q) <= 0){
			$message = '<span>-- No Activity --</span>';
		}

		return $message;
	}

	public function get_machine_breakdown(){
		$q = DB::connection('mysql_mes')->table('machine_breakdown')->where('status', 'Pending')->get();

		$end = Carbon::now();
		$breakdowns = [];
		foreach ($q as $r) {
			$start = Carbon::parse($r->date_reported);

			$days = $start->diffInDays($end);
			$hours = $start->copy()->addDays($days)->diffInHours($end);
			$minutes = $start->copy()->addDays($days)->addHours($hours)->diffInMinutes($end);
			$seconds = $start->copy()->addDays($days)->addHours($hours)->addMinutes($minutes)->diffInSeconds($end);
			$dur_days = ($days > 0) ? $days .'d' : null;
			$dur_hours = ($hours > 0) ? $hours .'h' : null;
			$dur_minutes = ($minutes > 0) ? $minutes .'m' : null;
			$dur_seconds = ($seconds > 0) ? $seconds .'s' : null;

			$duration = $dur_days .' '. $dur_hours . ' '. $dur_minutes . ' '. $dur_seconds . ' ago';

			$breakdowns[] = [
				'machine_code' => $r->machine_id,
				'category' => $r->category,
				'duration' => $duration
			];
		}

		return $breakdowns;
	}
}