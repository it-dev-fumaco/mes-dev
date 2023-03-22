<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use App\Mail\SendMail_machinebreakdown;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use Image;
use Validator;
use DB;
use App\Machine;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exports\ExportDataexcel;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportDataQaInspectionLog; 
use App\Traits\GeneralTrait;

class LinkReportController extends Controller
{
    use GeneralTrait;
    // /report_index
    public function index(){
        $permissions = $this->get_user_permitted_operation();
        $user_groups = DB::connection('mysql_mes')->table('user')
            ->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
            ->where('user.user_access_id', Auth::user()->user_id)
            ->pluck('user_group.user_role');
        
        return view('reports.report_index', compact('permissions', 'user_groups'));
    }

    public function painting_report_page(){
        $permissions = $this->get_user_permitted_operation();

        $item_classification= DB::connection('mysql_mes')->table('production_order')->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')->where('jt.workstation', 'Painting')->where('production_order.operation_id',1 )->whereNotNull('production_order.item_classification')->groupBy('production_order.item_classification')->select('production_order.item_classification')->get();       
        return view('link_report.painting_report', compact('item_classification', 'permissions'));
    }

    public function export_job_ticket(Request $request){
        $permissions = $this->get_user_permitted_operation();
        $start_date = $request->date ? explode(' - ', $request->date)[0] : Carbon::now()->subDays(30);
        $end_date = $request->date ? explode(' - ', $request->date)[1] : Carbon::now();
        $status = $request->status;
        $operation = $request->operation;

        $min_export_date = null;
        $max_export_date = null;

        $processes = DB::connection('mysql_mes')->table('process')->get();
        $process = collect($processes)->groupBy('process_id');
        
        $operations_q = DB::connection('mysql_mes')->table('operation')->get();
        $operations = collect($operations_q)->groupBy('operation_id');

        $time_logs_production_orders = DB::connection('mysql_mes')->table('production_order as po')
            ->join('job_ticket as jt', 'po.production_order', 'jt.production_order')
            ->join('time_logs as logs', 'jt.job_ticket_id', 'logs.job_ticket_id')
            ->whereBetween('po.created_at', [Carbon::parse($start_date)->startOfDay()->toDateTimeString(), Carbon::parse($end_date)->endOfDay()->toDateTimeString()])
            ->when($request->status and $request->status != 'All', function ($q) use ($status){
                $q->where('po.status', $status);
            })
            ->when($request->operation and $request->operation != 'All', function($q) use ($operation){
                $q->where('po.operation_id', $operation);
            })
            ->where('po.status', '!=', 'Cancelled')
            ->where('jt.workstation', '!=', 'Spotwelding')
            ->select('po.created_at', 'po.item_code', 'po.description', 'po.production_order', 'po.status', 'po.sales_order', 'po.material_request', 'po.customer', 'po.operation_id', 'jt.job_ticket_id', 'jt.workstation', 'jt.process_id', 'logs.from_time', 'logs.to_time', 'logs.good', 'logs.reject', 'logs.operator_name');

        if($request->ajax()){ // for export
            $production_orders = DB::connection('mysql_mes')->table('production_order as po')
                ->join('job_ticket as jt', 'po.production_order', 'jt.production_order')
                ->join('spotwelding_qty as logs', 'jt.job_ticket_id', 'logs.job_ticket_id')
                ->whereBetween('po.created_at', [Carbon::parse($start_date)->startOfDay()->toDateTimeString(), Carbon::parse($end_date)->endOfDay()->toDateTimeString()])
                ->when($request->status and $request->status != 'All', function ($q) use ($status){
                    $q->where('po.status', $status);
                })
                ->when($request->operation and $request->operation != 'All', function($q) use ($operation){
                    $q->where('po.operation_id', $operation);
                })
                ->where('po.status', '!=', 'Cancelled')
                ->where('jt.workstation', 'Spotwelding')
                ->select('po.created_at', 'po.item_code', 'po.description', 'po.production_order', 'po.status', 'po.sales_order', 'po.material_request', 'po.customer', 'po.operation_id', 'jt.job_ticket_id', 'jt.workstation', 'jt.process_id', 'logs.from_time', 'logs.to_time', 'logs.good', 'logs.reject', 'logs.operator_name')->union($time_logs_production_orders)
                ->orderBy('created_at', 'desc')
                ->limit(999)->get();

            $min_export_date = Carbon::parse(collect($production_orders)->min('created_at'))->format('M d, Y');
            $max_export_date = Carbon::parse(collect($production_orders)->max('created_at'))->format('M d, Y');
        }else{ // for UI
            $production_orders = DB::connection('mysql_mes')->table('production_order as po')
                ->join('job_ticket as jt', 'po.production_order', 'jt.production_order')
                ->join('spotwelding_qty as logs', 'jt.job_ticket_id', 'logs.job_ticket_id')
                ->whereBetween('po.created_at', [Carbon::parse($start_date)->startOfDay()->toDateTimeString(), Carbon::parse($end_date)->endOfDay()->toDateTimeString()])
                ->when($request->status and $request->status != 'All', function ($q) use ($status){
                    $q->where('po.status', $status);
                })
                ->when($request->operation and $request->operation != 'All', function($q) use ($operation){
                    $q->where('po.operation_id', $operation);
                })
                ->where('po.status', '!=', 'Cancelled')
                ->where('jt.workstation', 'Spotwelding')
                ->select('po.created_at', 'po.item_code', 'po.description', 'po.production_order', 'po.status', 'po.sales_order', 'po.material_request', 'po.customer', 'po.operation_id', 'jt.job_ticket_id', 'jt.workstation', 'jt.process_id', 'logs.from_time', 'logs.to_time', 'logs.good', 'logs.reject', 'logs.operator_name')->union($time_logs_production_orders)
                ->orderBy('created_at', 'desc')->paginate(10);
        }

        $export_arr = [];
        foreach($production_orders as $po){
            $process_name = isset($process[$po->process_id]) ? $process[$po->process_id][0]->process_name : null;
            $operation = isset($operations[$po->operation_id]) ? $operations[$po->operation_id][0]->operation_name : null;

            $export_arr[] = [
                'created_at' => $po->created_at,
                'operation' => $operation,
                'item_code' => $po->item_code,
                'item_description' => $po->description,
                'production_order' => $po->production_order,
                'status' => $po->status,
                'sales_order' => $po->sales_order,
                'material_request' => $po->material_request,
                'customer' => $po->customer,
                'job_ticket_id' => $po->job_ticket_id,
                'workstation' => $po->workstation,
                'process_name' => $process_name,
                'from' => $po->from_time,
                'to' => $po->to_time,
                'good' => $po->good,
                'reject' => $po->reject,
                'operator' => $po->operator_name
            ];
        }

        if($request->ajax()){
            return view('reports.export_job_ticket_file', compact('export_arr', 'min_export_date', 'max_export_date'));
        }

        $statuses = DB::connection('mysql_mes')->table('production_order')->where('status', '!=', 'Cancelled')->select('status')->distinct('status')->get();
        $operations_filter = DB::connection('mysql_mes')->table('operation')->get();

        return view('reports.export_job_ticket', compact('export_arr', 'production_orders', 'statuses', 'operations_filter', 'permissions'));
    }

    // /weekly_rejection_report/{operation_id}
    public function weekly_rejection_report(Request $request){
        $permissions = $this->get_user_permitted_operation();
        $operation_id = $request->operation_id;

        $start_date = ($request->date ? Carbon::parse(explode(' - ', $request->date)[0]) : Carbon::now()->subDays(7))->startOfDay();
        $end_date = ($request->date ? Carbon::parse(explode(' - ', $request->date)[1]) : Carbon::now())->endOfDay();

        $rejection_logs = DB::connection('mysql_mes')->table('time_logs as t')
            ->join('job_ticket as j', 't.job_ticket_id', 'j.job_ticket_id')
            ->join('production_order as p', 'p.production_order', 'j.production_order')
            ->whereBetween('t.from_time', [$start_date, $end_date])->where('j.workstation', '!=', 'Spotwelding')
            ->where('t.reject', '>', 0)
            ->when($operation_id != 2, function($q) use ($operation_id){
                $q->where('p.operation_id', $operation_id)->where('j.workstation', '!=', 'Painting');
            })
            ->when($operation_id == 2, function($q){
                $q->where('j.workstation', 'Painting');
            })
            ->select('p.production_order', 'p.sales_order', 'p.customer', 'p.material_request', 'p.status as po_status', 'p.operation_id', 'j.workstation', DB::raw('MIN(t.from_time) as from_time'), DB::raw('MAX(t.to_time) as to_time'), DB::raw('SUM(t.good) as good'), DB::raw('SUM(t.reject) as reject'), 't.status', DB::raw('GROUP_CONCAT(DISTINCT j.job_ticket_id SEPARATOR ",") as job_ticket_ids'))
            ->groupBy('p.production_order', 'p.sales_order', 'p.customer', 'p.material_request', 'p.status', 'p.operation_id', 'j.workstation', 't.status')
            ->orderBy('t.from_time', 'asc')->get();

        $job_ticket_ids = [];
        foreach ($rejection_logs as $j) {
            $jt_ids = explode(',', $j->job_ticket_ids);
            foreach ($jt_ids as $jt_id) {
                $job_ticket_ids[] = $jt_id;
            }
        }

        $reject_reasons = DB::connection('mysql_mes')->table('reject_reason as rr')->join('reject_list as rl', 'rl.reject_list_id', 'rr.reject_list_id')
            ->whereIn('rr.job_ticket_id', $job_ticket_ids)->select('rl.reject_reason', 'rl.reject_list_id', 'rr.job_ticket_id')->get();
        $reject_reasons = collect($reject_reasons)->groupBy('job_ticket_id')->toArray();

        $total_good = collect($rejection_logs)->sum('good');
        $total_reject = collect($rejection_logs)->sum('reject');

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
		// Create a new Laravel collection from the array data
		$itemCollection = collect($rejection_logs);
		// Define how many items we want to be visible in each page
		$perPage = 20;
		// Slice the collection to get the items to display in current page
		$currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
		// Create our paginator and pass it to the view
		$paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
		// set url path for generted links
		$paginatedItems->setPath($request->url());
		$rejection_logs = $paginatedItems;

        $reject_arr = [];
        foreach($rejection_logs as $reject){
            $reasons = [];
            $jts = explode(',', $reject->job_ticket_ids);
            foreach ($jts as $jt) {
                if (array_key_exists($jt, $reject_reasons)) {
                    foreach ($reject_reasons[$jt] as $reason) {
                        $reasons[] = $reason;
                    }
                }
            }

            $reject_arr[] = [
                'production_order' => $reject->production_order,
                'sales_order' => $reject->sales_order,
                'customer' => $reject->customer,
                'material_request' => $reject->material_request,
                'workstation' => $reject->workstation,
                'from_time' => Carbon::parse($reject->from_time)->format('M. d, Y h:i A'),
                'to_time' => Carbon::parse($reject->to_time)->format('M. d, Y h:i A'),
                'good' => $reject->good,
                'reject' => $reject->reject,
                'status' => $reject->status,
                'reject_reason' => implode('<br>', array_unique(array_column($reasons, 'reject_reason')))
            ];
        }

        return view('reports.weekly_rejection_report', compact('rejection_logs', 'reject_arr', 'permissions', 'total_good', 'total_reject'));
    }

    // /rejection_reasons_report/{id}
    public function reject_reasons_report(Request $request){
        $permissions = $this->get_user_permitted_operation();
        $operations = DB::connection('mysql_mes')->table('operation')->select('operation_id', 'operation_name')->get();

        $operation_id = $request->operation_id ? $request->operation_id : 1;

        if(!$operations){
            return redirect()->back()->with('error', 'Operation not found.');
        }

        $start = Carbon::now()->startOfDay()->subDays(7);
        $end = Carbon::now()->endOfDay();
        if($request->date){
            $start = Carbon::parse(explode(' - ', $request->date)[0])->startOfDay();
            $end = isset(explode(' - ', $request->date)[1]) ? Carbon::parse(explode(' - ', $request->date)[1])->endOfDay() : Carbon::now()->endOfDay();
        }

        $operation_name = collect($operations)->groupBy('operation_id');

        $reject_reasons = DB::connection('mysql_mes')->table('reject_reason as rr')
            ->join('job_ticket as jt', 'jt.job_ticket_id', 'rr.job_ticket_id')
            ->join('reject_list as rl', 'rl.reject_list_id', 'rr.reject_list_id')
            ->join('production_order as po', 'po.production_order', 'jt.production_order')
            ->join('reject_category as rc', 'rc.reject_category_id', 'rl.reject_category_id')
            ->where('po.operation_id', $operation_id)->whereBetween('rr.created_at', [$start, $end])
            ->selectRaw('rr.reject_list_id, rl.reject_reason, rc.type, rc.reject_category_name, count(rr.reject_list_id) as count')->groupBy('rr.reject_list_id', 'rl.reject_reason', 'rc.type', 'rc.reject_category_name')
            ->orderBy('count', 'desc')
            ->get();

        $reject_types = collect($reject_reasons)->groupBy('type');

        $counts_arr = [
            'critical' => isset($reject_types['Critical Reject(s)']) ? count($reject_types['Critical Reject(s)']) : 0,
            'major' => isset($reject_types['Major Reject(s)']) ? count($reject_types['Major Reject(s)']) : 0,
            'minor' => isset($reject_types['Minor Reject(s)']) ? count($reject_types['Minor Reject(s)']) : 0
        ];

        return view('reports.reject_reasons', compact('operation_id', 'reject_reasons', 'operations', 'counts_arr', 'permissions'));
    }

    public function export_rejection_logs(Request $request){
        $permissions = $this->get_user_permitted_operation();
        $start_date = $request->date ? explode(' - ', $request->date)[0] : Carbon::now()->subDays(30);
        $end_date = $request->date ? explode(' - ', $request->date)[1] : Carbon::now();
        $status = $request->status;
        $operation = $request->operation;

        $min_export_date = null;
        $max_export_date = null;

        $processes = DB::connection('mysql_mes')->table('process')->get();
        $process = collect($processes)->groupBy('process_id');

        $operations_q = DB::connection('mysql_mes')->table('operation')->get();
        $operations = collect($operations_q)->groupBy('operation_id');

        $time_logs = DB::connection('mysql_mes')->table('production_order as po')
            ->join('job_ticket as jt', 'po.production_order', 'jt.production_order')
            ->join('time_logs as logs', 'jt.job_ticket_id', 'logs.job_ticket_id')
            ->whereBetween('po.created_at', [Carbon::parse($start_date)->startOfDay()->toDateTimeString(), Carbon::parse($end_date)->endOfDay()->toDateTimeString()])
            ->when($request->status and $request->status != 'All', function ($q) use ($status){
                $q->where('po.status', $status);
            })
            ->when($request->operation and $request->operation != 'All', function($q) use ($operation){
                $q->where('po.operation_id', $operation);
            })
            ->where('po.status', '!=', 'Cancelled')
            ->where('logs.reject', '>', 0)
            ->where('jt.workstation', '!=', 'Spotwelding')
            ->select('po.created_at', 'po.item_code', 'po.description', 'po.production_order', 'po.status', 'po.sales_order', 'po.material_request', 'po.customer', 'po.operation_id', 'jt.job_ticket_id', 'jt.workstation', 'jt.process_id', 'logs.from_time', 'logs.to_time', 'logs.reject', 'logs.operator_name');

        if($request->ajax()){
            $rejection_logs = DB::connection('mysql_mes')->table('production_order as po')
                ->join('job_ticket as jt', 'po.production_order', 'jt.production_order')
                ->join('spotwelding_qty as logs', 'jt.job_ticket_id', 'logs.job_ticket_id')
                ->whereBetween('po.created_at', [Carbon::parse($start_date)->startOfDay()->toDateTimeString(), Carbon::parse($end_date)->endOfDay()->toDateTimeString()])
                ->when($request->status and $request->status != 'All', function ($q) use ($status){
                    $q->where('po.status', $status);
                })
                ->when($request->operation and $request->operation != 'All', function($q) use ($operation){
                    $q->where('po.operation_id', $operation);
                })
                ->where('po.status', '!=', 'Cancelled')
                ->where('logs.reject', '>', 0)
                ->where('jt.workstation', 'Spotwelding')
                ->select('po.created_at', 'po.item_code', 'po.description', 'po.production_order', 'po.status', 'po.sales_order', 'po.material_request', 'po.customer', 'po.operation_id', 'jt.job_ticket_id', 'jt.workstation', 'jt.process_id', 'logs.from_time', 'logs.to_time', 'logs.reject', 'logs.operator_name')->union($time_logs)
                ->orderBy('created_at', 'desc')
                ->limit(999)->get();

            $min_export_date = Carbon::parse(collect($rejection_logs)->min('created_at'))->format('M d, Y');
            $max_export_date = Carbon::parse(collect($rejection_logs)->max('created_at'))->format('M d, Y');
        }else{
            $rejection_logs = DB::connection('mysql_mes')->table('production_order as po')
                ->join('job_ticket as jt', 'po.production_order', 'jt.production_order')
                ->join('spotwelding_qty as logs', 'jt.job_ticket_id', 'logs.job_ticket_id')
                ->whereBetween('po.created_at', [Carbon::parse($start_date)->startOfDay()->toDateTimeString(), Carbon::parse($end_date)->endOfDay()->toDateTimeString()])
                ->when($request->status and $request->status != 'All', function ($q) use ($status){
                    $q->where('po.status', $status);
                })
                ->when($request->operation and $request->operation != 'All', function($q) use ($operation){
                    $q->where('po.operation_id', $operation);
                })
                ->where('po.status', '!=', 'Cancelled')
                ->where('logs.reject', '>', 0)
                ->where('jt.workstation', 'Spotwelding')
                ->select('po.created_at', 'po.item_code', 'po.description', 'po.production_order', 'po.status', 'po.sales_order', 'po.material_request', 'po.customer', 'po.operation_id', 'jt.job_ticket_id', 'jt.workstation', 'jt.process_id', 'logs.from_time', 'logs.to_time', 'logs.reject', 'logs.operator_name')->union($time_logs)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }
        
        $export_arr = [];
        foreach($rejection_logs as $reject){
            $process_name = isset($process[$reject->process_id]) ? $process[$reject->process_id][0]->process_name : null;
            $operation = isset($operations[$reject->operation_id]) ? $operations[$reject->operation_id][0]->operation_name : null;

            $export_arr[] = [
                'created_at' => $reject->created_at,
                'operation' => $operation,
                'item_code' => $reject->item_code,
                'item_description' => $reject->description,
                'production_order' => $reject->production_order,
                'status' => $reject->status,
                'sales_order' => $reject->sales_order,
                'material_request' => $reject->material_request,
                'customer' => $reject->customer,
                'job_ticket_id' => $reject->job_ticket_id,
                'workstation' => $reject->workstation,
                'process_name' => $process_name,
                'from' => $reject->from_time,
                'to' => $reject->to_time,
                'reject' => $reject->reject,
                'operator' => $reject->operator_name
            ];
        }

        if($request->ajax()){
            return view('reports.export_rejection_logs_file', compact('export_arr', 'min_export_date', 'max_export_date'));
        }

        $statuses = DB::connection('mysql_mes')->table('production_order')->where('status', '!=', 'Cancelled')->select('status')->distinct('status')->get();
        $operations_filter = DB::connection('mysql_mes')->table('operation')->get();

        return view('reports.export_rejection_logs', compact('export_arr', 'rejection_logs', 'statuses', 'operations_filter', 'permissions'));
    }
    
    public function export_machine_list(Request $request){
        $status = $request->status;
        $operation = $request->operation;
        $permissions = $this->get_user_permitted_operation();

        if($request->ajax()){
            $machine_list = DB::connection('mysql_mes')->table('machine')
                ->join('operation', 'machine.operation_id', 'operation.operation_id')
                ->when($request->status and $request->status != 'All', function ($q) use ($status){
                    $q->where('machine.status', $status);
                })
                ->when($request->operation and $request->operation != "All", function ($q) use ($operation){
                    $q->where('machine.operation_id', $operation);
                })
                ->select('machine.machine_code', 'machine.machine_name', 'machine.model', 'machine.operation_id', 'machine.status', 'operation.operation_name')
                ->limit(999)
                ->get();

            return view('reports.export_machine_list_file', compact('machine_list'));
        }else{
            $machine_list = DB::connection('mysql_mes')->table('machine')
                ->join('operation', 'machine.operation_id', 'operation.operation_id')
                ->when($request->status and $request->status != 'All', function ($q) use ($status){
                    $q->where('machine.status', $status);
                })
                ->when($request->operation and $request->operation != "All", function ($q) use ($operation){
                    $q->where('machine.operation_id', $operation);
                })
                ->select('machine.machine_code', 'machine.machine_name', 'machine.model', 'machine.operation_id', 'machine.status', 'operation.operation_name')
                ->paginate(10);
        }
        
        $operations_filter = DB::connection('mysql_mes')->table('operation')->get();
        $statuses = DB::connection('mysql_mes')->table('machine')->select('status')->distinct('status')->get();

        return view('reports.export_machine_list', compact('machine_list', 'operations_filter', 'statuses', 'permissions'));
    }

    public function daily_output_report(Request $request){
        $operation= 1;
        $now = Carbon::now();
        $to=$request->end_date;
        $from=$request->start_date;
        $operation= $request->operation;
        $period = CarbonPeriod::create($from, $to);
        $day=[];
        foreach ($period as $date) {
            $overtime_hour=0;
            $over_time_shift= DB::connection('mysql_mes')->table('shift_schedule')
            ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
            ->whereDate('shift_schedule.date', $date)
            ->where('shift.operation_id', $operation)
            ->where('shift.shift_type', "Overtime Shift")
            ->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();

            $special_shift= DB::connection('mysql_mes')->table('shift_schedule')
            ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
            ->whereDate('shift_schedule.date', $date)->where('shift.operation_id', $operation)->where('shift.shift_type', "Special Shift")->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();


            if($special_shift){
                $reg_hours = $special_shift->hrs_of_work; 
            }else{
                $reg_shift= DB::connection('mysql_mes')->table('shift')->where('shift.operation_id', $operation)->where('shift.shift_type', "Regular Shift")->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();
                $reg_hours = $reg_shift->hrs_of_work;
            }

            $overtime_hour = ($over_time_shift != null) ?  $over_time_shift->hrs_of_work: 0;
            if($request->item_classification == 'All'){
                if($operation == 3){
                    $production_order_planned=DB::connection('mysql_mes')->table('production_order')->whereDate('planned_start_date', $date)->where('operation_id', $operation)->whereRaw('parent_item_code = item_code')->get();
                    $production_order_output=DB::connection('mysql_mes')->table('production_order')->whereDate('actual_end_date', $date)->where('operation_id', $operation)->whereRaw('parent_item_code = item_code')->get();
                    $produced_qty= collect($production_order_output)->sum('feedback_qty');
                    $output_qty = (collect($production_order_planned)->sum('qty_to_manufacture') == 0)?  1 :collect($production_order_planned)->sum('qty_to_manufacture');            
                    $per_day_planned= collect($production_order_planned)->sum('qty_to_manufacture');
                    $per_day_produced= collect($production_order_output)->sum('feedback_qty');

                }else{
                    $production_order_planned=DB::connection('mysql_mes')->table('production_order')->whereDate('planned_start_date', $date)->where('parts_category', 'SA - Housing')->where('item_classification', 'HO - Housing')->where('operation_id', $operation)->get();
                    $production_order_ouput=DB::connection('mysql_mes')->table('production_order')->whereDate('actual_end_date', $date)->where('parts_category', 'SA - Housing')->where('item_classification', 'HO - Housing')->where('operation_id', $operation)->get();
                    $produced_qty=  collect($production_order_ouput)->sum('produced_qty');
                    $output_qty = (collect($production_order_planned)->sum('qty_to_manufacture') == 0)?  1 :collect($production_order_planned)->sum('qty_to_manufacture');
                    $per_day_planned= collect($production_order_planned)->sum('qty_to_manufacture');
                    $per_day_produced= collect($production_order_ouput)->sum('produced_qty');

                }
            }else{
                $production_order_planned=DB::connection('mysql_mes')->table('production_order')->whereDate('planned_start_date', $date)->where('operation_id', $operation)->where('item_classification', 'like','%'.$request->item_classification.'%')->get();
                $production_order_ouput=DB::connection('mysql_mes')->table('production_order')->whereDate('actual_end_date', $date)->where('operation_id', $operation)->where('item_classification', 'like','%'.$request->item_classification.'%')->get();
                $produced_qty= ($operation == 3)? collect($production_order_ouput)->sum('feedback_qty'):collect($production_order_ouput)->sum('produced_qty');
                $output_qty = (collect($production_order_planned)->sum('qty_to_manufacture') == 0) ? 1 : collect($production_order_planned)->sum('qty_to_manufacture');
                $per_day_planned=  collect($production_order_planned)->sum('qty_to_manufacture');
                $per_day_produced= ($operation == 3)? collect($production_order_ouput)->sum('feedback_qty'):collect($production_order_ouput)->sum('produced_qty');

            }

            $pluck_pro= collect($production_order_planned)->pluck('production_order');
            $job_ticket1=DB::connection('mysql_mes')->table('job_ticket as jt')->whereIn('production_order', $pluck_pro)
            ->join('time_logs as tl', 'tl.job_ticket_id', 'jt.job_ticket_id')
            ->whereDate('tl.from_time', $date)
            ->where('jt.workstation','!=', 'Painting')
            ->groupBy('tl.operator_name')
            ->select('tl.operator_name as op');
            
            $spot1=DB::connection('mysql_mes')->table('job_ticket as jt')->whereIn('production_order', $pluck_pro)
            ->join('spotwelding_qty as spot', 'spot.job_ticket_id', 'jt.job_ticket_id')
            ->whereDate('spot.from_time', $date)
            ->groupBy('spot.operator_name')
            ->select('spot.operator_name as op');
            
            $spot2=DB::connection('mysql_mes')->table('job_ticket as jt')->whereIn('production_order', $pluck_pro)
            ->join('spotwelding_qty as spot', 'spot.job_ticket_id', 'jt.job_ticket_id')
            ->join('helper', 'spot.time_log_id', 'helper.time_log_id')
            ->whereDate('spot.from_time', $date)
            ->select('helper.operator_name as op')
            ->distinct('op')
            ->groupBy('op');

            $job_ticket=DB::connection('mysql_mes')->table('job_ticket as jt')->whereIn('production_order', $pluck_pro)
            ->join('time_logs as tl', 'tl.job_ticket_id', 'jt.job_ticket_id')
            ->join('helper', 'tl.time_log_id', 'helper.time_log_id')
            ->whereDate('tl.from_time', $date)
            ->where('jt.workstation','!=', 'Painting')
            ->select('helper.operator_name as op')
            ->unionAll($job_ticket1)
            ->unionAll($spot2)
            ->unionAll($spot1)
            ->distinct('op')
            ->groupBy('op')
            ->get();
            if($this->overallStatus($date) == "Sunday" || $this->overallStatus($date) == "Holiday"){
                if($overtime_hour == 0){
                    $per_day_planned=0;
                    $per_day_produced=0;
                    $per_reg_shift=0;
                    $per_man_hr=0;
                    $per_realization_rate =0;
                    $per_day_count=null;

                }else{
                    $per_reg_shift=$reg_hours;
                    $per_man_hr=count($job_ticket);
                    $per_realization_rate = round(($produced_qty / $output_qty)*100, 2);
                    $per_day_count='count';

                }
            }else{
                $per_reg_shift=$reg_hours;
                $per_man_hr=count($job_ticket);
                $per_realization_rate = round(($produced_qty / $output_qty)*100, 2);
                $per_day_count='count';

            }
            $day[]=[
                'date' =>  date('d', strtotime($date)),
                'stat' => ($this->overallStatus($date) != null) ? $overtime_hour: null,
                'stat_sunday' => $this->overallStatus($date),
                'count'=>$per_day_count
            ];

            $target_output = ceil((($per_man_hr * $per_reg_shift) / 0.44) + ($overtime_hour * count($this->get_operator_with_ot($date, $pluck_pro, $operation)) / 0.44));
            $per_day_planned = ($operation == 3) ? $target_output : $per_day_planned;

            $planned_qty[]=[
                'value'=>  $per_day_planned,
                'value_display'=> $per_day_planned,
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
                'count'=>$per_day_count

            ];
            $produced[]=[
                'value'=> $per_day_produced,
                'value_display'=> $per_day_produced,
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
                'count'=>$per_day_count

            ];
            $shift_overtimeshif[]=[
                'value'=> $overtime_hour,
                'value_display'=> $overtime_hour,
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
                'count'=>$per_day_count

            ];

            $shift_regshif[]=[
                'value'=> $per_reg_shift,
                'value_display'=> $per_reg_shift,
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
                'count'=>$per_day_count

            ];
            $total_man_power[]=[
                'value'=>  $per_man_hr,
                'value_display'=> $per_man_hr,
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
                'count'=>$per_day_count

            ];
            
            $total_realization_rate[]=[
                'value'=>  $per_realization_rate,
                'value_display'=>  $per_realization_rate.'%',
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
                'count'=>$per_day_count


            ];
            $avg_man_power[]=[
                'value'=>  ($produced_qty == 0) ? 0 : round((($overtime_hour + $per_reg_shift) *count($job_ticket))/(($produced_qty == 0)? 1 : $produced_qty), 2),
                'value_display'=>  ($produced_qty == 0) ? 0 : round((($overtime_hour + $per_reg_shift) *count($job_ticket))/(($produced_qty == 0)? 1 : $produced_qty), 2),
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
                'count'=>$per_day_count


            ];
            
            $man_hr_with_overtime[]=[
                'value'=>  count($this->get_operator_with_ot($date, $pluck_pro, $operation)),
                'value_display'=>  count($this->get_operator_with_ot($date, $pluck_pro, $operation)),
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
                'count'=>$per_day_count


            ];
        }
        // return $day;
        $data2=[
            'data' =>$planned_qty,
            'row_name' =>"PLANNED QUANTITY",
            'total' => collect($planned_qty)->sum('value'),
            'avg' => round( collect($planned_qty)->sum('value') / collect($planned_qty)->where('count', "count")->count('count'), 0)
        ];
        $data3=[
            'data' =>$produced,
            'row_name' =>"TOTAL OUTPUT",
            'total' => collect($produced)->sum('value'),
            'avg' => round(collect($produced)->sum('value') / collect($planned_qty)->where('count', "count")->count('count'), 0)

        ];
        $data4=[
            'data' =>$shift_overtimeshif,
            'row_name' =>"TOTAL HRS. OVERTIME",
            'total' => collect($shift_overtimeshif)->sum('value'),
            'avg' => round(collect($shift_overtimeshif)->sum('value') / collect($planned_qty)->where('count', "count")->count('count'), 0)

        ];
        $data5=[
            'data' =>$shift_regshif,
            'row_name' =>"TOTAL HRS. REGULAR TIME",
            'total' => collect($shift_regshif)->sum('value'),
            'avg' => round(collect($shift_regshif)->sum('value') / collect($planned_qty)->where('count', "count")->count('count'), 0)

        ];
        $data5=[
            'data' =>$shift_regshif,
            'row_name' =>"TOTAL HRS. REGULAR TIME",
            'total' => collect($shift_regshif)->sum('value'),
            'avg' => round(collect($shift_regshif)->sum('value') / collect($planned_qty)->where('count', "count")->count('count'), 0)

        ];
        $data6=[
            'data' =>$total_man_power,
            'row_name' =>"TOTAL MAN HR",
            'total' => collect($total_man_power)->sum('value'),
            'avg' => round(collect($total_man_power)->sum('value') / collect($planned_qty)->where('count', "count")->count('count'), 0)

        ];
        $data7=[
            'data' =>$total_realization_rate,
            'row_name' =>"REALIZATION RATE",
            'total' => collect($total_realization_rate)->whereNotIn('stat',['Sunday', 'Holiday'])->sum('value'),
            'avg' => round(collect($total_realization_rate)->sum('value') / collect($planned_qty)->where('count', "count")->count('count'), 0)

        ];
        $data8=[
            'data' =>$avg_man_power,
            'row_name' =>"AVERAGE MAN-HR UTILIZATION",
            'total' => collect($avg_man_power)->whereNotIn('stat',['Sunday', 'Holiday'])->sum('value'),
            'avg' => round(collect($avg_man_power)->sum('value') / collect($planned_qty)->where('count', "count")->count('count'), 2)

        ];
        $data9=[
            'data' =>$man_hr_with_overtime,
            'row_name' =>"TOTAL MANPOWER W/ OVERTIME",
            'total' => collect($man_hr_with_overtime)->whereNotIn('stat',['Sunday', 'Holiday'])->sum('value'),
            'avg' => round(collect($man_hr_with_overtime)->sum('value') / collect($planned_qty)->where('count', "count")->count('count'), 0)

        ];
        $data=[
            'planned' => $data2,
            'produced' => $data3,
            'total_overtimehrs' => $data4,
            'ma_hr_with_overtime' => $data9,
            'total_reg' => $data5,
            'total_man_hr' => $data6,
            'realization_rate' => $data7,
            'avg_man_power' => $data8
        ];
        $date_column= $day;
        $colspan_date = count($day);
        if($operation == 1){
            return view('tables.tbl_fab_daily_report', compact('data', "date_column", 'colspan_date'));
        }else{
            return view('tables.tbl_fab_daily_report', compact('data', "date_column", 'colspan_date'));
        }
    }

    public function overallStatus($transaction_date){
        $status=null;
        $isHoliday = DB::connection('mysql_essex')->table('holidays')->whereDate('holiday_date', $transaction_date)->first();
        if ($isHoliday) {
            $status = 'Holiday';
        }elseif (Carbon::parse($transaction_date)->format('N') == 7) {
            $status = 'Sunday';
        }
        return $status;
    }
    public function get_operator_with_ot($date, $prod, $operation){
        $shift_sched= DB::connection('mysql_mes')->table('shift_schedule')
        ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
        ->whereDate('shift_schedule.date', $date)
        ->where('shift.operation_id', $operation)
        ->max('time_out');
        if(empty($shift_sched)){
            $shift_sched= DB::connection('mysql_mes')->table('shift')->where('operation_id',$operation)->where('shift_type', 'Regular Shift')->max('time_out');
        }
        $shift_sched= date('H:i:s', strtotime($shift_sched));
        $timelogs=DB::connection('mysql_mes')->table('time_logs')
            ->join('job_ticket as jt','jt.job_ticket_id', 'time_logs.job_ticket_id')
            ->join('process', 'process.process_id', 'jt.process_id')
            ->where('jt.workstation', '!=', 'Painting')
            ->whereIn('jt.production_order', $prod)
            ->whereDate('time_logs.to_time',$date )
            ->whereTime('time_logs.to_time', '>', $shift_sched )
            ->selectRaw('time_logs.operator_name')
            ->groupBy('time_logs.operator_name')
            ->get();
        
        return $timelogs;
    }
    public function fabrication_daily_report_page(Request $request){
        $permissions = $this->get_user_permitted_operation();

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

        $parts_category= DB::connection('mysql_mes')->table('production_order')->where('operation_id', 1)->whereNotNull('parts_category')->groupBy('parts_category')->select('parts_category')->get();       
        $reject_category= DB::connection('mysql_mes')->table('reject_category')->get();  
        
        $item_classification= DB::connection('mysql_mes')->table('production_order')->where('operation_id', 1)->whereNotNull('item_classification')->groupBy('item_classification')->select('item_classification')->get();      

        return view('link_report.fabrication_report',  compact('workstation', 'process', 'parts','sacode', 'parts_category', 'reject_category', 'permissions', 'item_classification'));
    }
    public function daily_output_chart(Request $request){
        $now = Carbon::now();
        $to=$request->end_date;
        $from=$request->start_date;
        $operation= $request->operation;
        $period = CarbonPeriod::create($from, $to);
        $day=[];
        $hours=0;
        foreach ($period as $date) {
            $over_time_shift= DB::connection('mysql_mes')->table('shift_schedule')
            ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
            ->whereDate('shift_schedule.date', $date)
            ->where('shift.operation_id', $operation)
            ->where('shift.shift_type', "Overtime Shift")
            ->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();

            $special_shift= DB::connection('mysql_mes')->table('shift_schedule')
            ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
            ->whereDate('shift_schedule.date', $date)->where('shift.operation_id', $operation)->where('shift.shift_type', "Special Shift")->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();
           
            if($special_shift){
                $reg_hours = $special_shift->hrs_of_work; 
            }else{
                $reg_shift= DB::connection('mysql_mes')->table('shift')->where('shift.operation_id', $operation)->where('shift.shift_type', "Regular Shift")->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();
                $reg_hours = $reg_shift->hrs_of_work;
            }
            if($over_time_shift){
                $hours = $over_time_shift->hrs_of_work; 
            }
            $overtime_hour = ($over_time_shift != null) ?  $over_time_shift->hrs_of_work: 0;
            if($special_shift){
                $reg_hours = $special_shift->hrs_of_work; 
            }else{
                $reg_shift= DB::connection('mysql_mes')->table('shift')->where('shift.operation_id', $operation)->where('shift.shift_type', "Regular Shift")->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();
                $reg_hours = $reg_shift->hrs_of_work;
            }

            $overtime_hour = ($over_time_shift != null) ?  $over_time_shift->hrs_of_work: 0;

            if($request->item_classification == 'All'){
                if($operation == 3){
                    $production_order_planned=DB::connection('mysql_mes')->table('production_order')->whereDate('planned_start_date', $date)->where('operation_id', $operation)->whereRaw('parent_item_code = item_code')->get();
                    $production_order_output=DB::connection('mysql_mes')->table('production_order')->whereDate('actual_end_date', $date)->where('operation_id', $operation)->whereRaw('parent_item_code = item_code')->get();
                    $per_day_planned= collect($production_order_planned)->sum('qty_to_manufacture');
                    $per_day_produced= collect($production_order_output)->sum('feedback_qty');

                }else{
                    $production_order_planned=(DB::connection('mysql_mes')->table('production_order')->whereDate('planned_start_date', $date)->where('operation_id', $operation)->where('parts_category', 'SA - Housing')->where('item_classification', 'HO - Housing')->select('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->get());
                    $production_order_output=(DB::connection('mysql_mes')->table('production_order')->whereDate('actual_end_date', $date)->where('operation_id', $operation)->where('parts_category', 'SA - Housing')->where('item_classification', 'HO - Housing')->select('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->get());
                    $per_day_planned= collect($production_order_planned)->sum('qty_to_manufacture');
                    $per_day_produced= collect($production_order_output)->sum('produced_qty');

                }
            }else{
                $production_order_planned=(DB::connection('mysql_mes')->table('production_order')->whereDate('planned_start_date', $date)->where('operation_id', $operation)->where('item_classification', 'like','%'.$request->item_classification.'%')->select('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->get());
                $production_order_output=(DB::connection('mysql_mes')->table('production_order')->whereDate('actual_end_date', $date)->where('operation_id', $operation)->where('item_classification', 'like','%'.$request->item_classification.'%')->select('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->get());
                $per_day_planned=  collect($production_order_planned)->sum('qty_to_manufacture');
                $per_day_produced= ($operation == 3)? collect($production_order_output)->sum('feedback_qty'):collect($production_order_output)->sum('produced_qty');
            }

            if($this->overallStatus($date) == "Sunday" || $this->overallStatus($date) == "Holiday"){
                if($overtime_hour == 0){
                    $per_day_planned=0;
                    $per_day_produced=0;
                }
            }
            $planned_qty[]=[
                'value'=>  $per_day_planned,
                'value_display'=> $per_day_planned,
            ];
            $produced[]=[
                'value'=> $per_day_produced,
                'value_display'=> $per_day_produced,
            ];
            
            $day[]=[
                'date' =>  date('d', strtotime($date)),
            ];
        }
        $planned_data= $planned_qty;
        $produce_data= $produced;
        $date_column= $day;
        $colspan_date = count($day);
        return response()->json(['per_day' => $date_column, 'planned' => $planned_data, 'produced' => $produce_data]);
    }
    public function assembly_report_page(){
        $permissions = $this->get_user_permitted_operation();

        $item_classification= DB::connection('mysql_mes')->table('production_order')->where('operation_id', 3)->whereNotNull('item_classification')->groupBy('item_classification')->select('item_classification')->get();       
        return view('link_report.assembly_report', compact('item_classification', 'permissions'));
    }
    
    public function qa_report(){
        $permissions = $this->get_user_permitted_operation();

        $item_code=  DB::connection('mysql_mes')->table('production_order as po')->select('item_code')->groupBy('item_code')->get();
        $customer=  DB::connection('mysql_mes')->table('production_order as po')->select('customer')->groupBy('customer')->get();
        $production_order=  DB::connection('mysql_mes')->table('production_order as po')->select('production_order')->get();
        $fab_workstation=  DB::connection('mysql_mes')->table('workstation')->where('operation_id', 1)->where('workstation_name', '!=', "Painting")->select('workstation_name','workstation_id')->get();
        $pain_workstation=  DB::connection('mysql_mes')->table('workstation')->where('operation_id', 1)->where('workstation_name', "Painting")->select('workstation_name','workstation_id')->get();
        $assem_workstation=  DB::connection('mysql_mes')->table('workstation')->where('operation_id', 3)->select('workstation_name','workstation_id')->get();

        $fab_process=  DB::connection('mysql_mes')
        ->table('process_assignment')
        ->join('process', 'process.process_id', 'process_assignment.process_id')
        ->join('workstation', 'workstation.workstation_id', 'process_assignment.workstation_id')
        ->whereNotIn('process.process_name',['Loading','Unloading'])
        ->where('workstation.operation_id', 1)
        ->groupBy('process_assignment.process_id', 'process.process_name')->select('process_assignment.process_id', 'process.process_name')->get();
        $assem_process=  DB::connection('mysql_mes')
        ->table('process_assignment')
        ->join('process', 'process.process_id', 'process_assignment.process_id')
        ->join('workstation', 'workstation.workstation_id', 'process_assignment.workstation_id')
        ->where('workstation.operation_id', 3)
        ->groupBy('process_assignment.process_id', 'process.process_name')->select('process_assignment.process_id', 'process.process_name')->get();

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
                "name" => ($emp_name) ? $emp_name->employee_name : null,
                "user_id" =>  $row->qa_staff_id
                ];
            }
        }

        $operators = DB::connection('mysql_essex')->table('users')
            ->where('status', 'Active')->where('user_type', 'Employee')
            ->whereIn('designation_id', [46, 47, 53])->orderBy('employee_name', 'asc')
            ->select('user_id', 'employee_name')
            ->get();
        $process_painting=  DB::connection('mysql_mes')->table('process')->whereIn('process_name',['Loading','Unloading'])->get();
        $reject_category= DB::connection('mysql_mes')->table('reject_category')->get();       
        
        return view('link_report.qa_report', compact('permissions', 'reject_category','process_painting','item_code','customer','production_order', 'qc_name', 'operators','fab_workstation','assem_workstation','pain_workstation','fab_process', "assem_process"));
    } 
    
    public function painting_output_report(Request $request){
        $now = Carbon::now();
        $to=$request->end_date;
        $from=$request->start_date;
        $operation= 1;
        $period = CarbonPeriod::create($from, $to);
        $day=[];
        foreach ($period as $date) {
            $overtime_hour=0;
            $production_order_planned=($request->item_classification != "All")? (DB::connection('mysql_mes')->table('production_order')->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')->where('jt.workstation', 'Painting')->whereDate('jt.planned_start_date', $date)->where('production_order.operation_id', $operation)->where('production_order.item_classification', 'like','%'.$request->item_classification.'%')->groupBy('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->select('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->get()):(DB::connection('mysql_mes')->table('production_order')->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')->where('jt.workstation', 'Painting')->whereDate('jt.planned_start_date', $date)->where('production_order.operation_id', $operation)->groupBy('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->select('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->get());
            $production_order_output=($request->item_classification != "All")? (DB::connection('mysql_mes')->table('production_order')->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')->where('jt.workstation', 'Painting')->whereDate('jt.actual_end_date', $date)->where('production_order.operation_id', $operation)->where('production_order.item_classification', 'like','%'.$request->item_classification.'%')->groupBy('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->select('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->get()):(DB::connection('mysql_mes')->table('production_order')->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')->where('jt.workstation', 'Painting')->whereDate('jt.actual_end_date', $date)->where('production_order.operation_id', $operation)->groupBy('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->select('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->get());
            $over_time_shift= DB::connection('mysql_mes')->table('shift_schedule')
            ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
            ->whereDate('shift_schedule.date', $date)
            ->where('shift.operation_id', $operation)
            ->where('shift.shift_type', "Overtime Shift")
            ->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();
            $special_shift= DB::connection('mysql_mes')->table('shift_schedule')
            ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
            ->whereDate('shift_schedule.date', $date)->where('shift.operation_id', $operation)->where('shift.shift_type', "Special Shift")->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();
           
            $pluck_pro= collect($production_order_planned)->pluck('production_order');

            $job_ticket1=DB::connection('mysql_mes')->table('job_ticket as jt')->whereIn('production_order', $pluck_pro)
            ->join('time_logs as tl', 'tl.job_ticket_id', 'jt.job_ticket_id')
            ->whereDate('tl.from_time', $date)
            ->where('jt.workstation', 'Painting')
            ->groupBy('tl.operator_name')
            ->select('tl.operator_name as op');
            $job_ticket=DB::connection('mysql_mes')->table('job_ticket as jt')->whereIn('production_order', $pluck_pro)
            ->join('time_logs as tl', 'tl.job_ticket_id', 'jt.job_ticket_id')
            ->join('helper', 'tl.time_log_id', 'helper.time_log_id')
            ->whereDate('tl.from_time', $date)
            ->where('jt.workstation', 'Painting')
            ->select('helper.operator_name as op')
            ->unionAll($job_ticket1)
            ->distinct('op')
            ->groupBy('op')
            ->get();
            
            if($special_shift){
                $reg_hours = $special_shift->hrs_of_work; 
            }else{
                $reg_shift= DB::connection('mysql_mes')->table('shift')->where('shift.operation_id', $operation)->where('shift.shift_type', "Regular Shift")->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();
                $reg_hours = $reg_shift->hrs_of_work;
            }

            $overtime_hour = ($over_time_shift != null) ?  $over_time_shift->hrs_of_work: 0;
            $produced_qty= collect($production_order_output)->sum('produced_qty');
            $output_qty = (collect($production_order_planned)->sum('qty_to_manufacture') == 0) ? 1 : collect($production_order_planned)->sum('qty_to_manufacture');
            $per_day_planned=  collect($production_order_planned)->sum('qty_to_manufacture');
            $per_day_produced= collect($production_order_output)->sum('produced_qty');


            if($this->overallStatus($date) == "Sunday" || $this->overallStatus($date) == "Holiday"){
                if($overtime_hour == 0){
                    $per_day_planned=0;
                    $per_day_produced=0;
                    $per_reg_shift=0;
                    $per_man_hr=0;
                    $per_realization_rate =0;
                    $per_day_count=null;

                }else{
                    $per_reg_shift=$reg_hours;
                    $per_man_hr=count($job_ticket);
                    $per_realization_rate = round(($produced_qty / $output_qty)*100, 2);
                    $per_day_count='count';

                }
            }else{
                $per_reg_shift=$reg_hours;
                $per_man_hr=count($job_ticket);
                $per_realization_rate = round(($produced_qty / $output_qty)*100, 2);
                $per_day_count='count';

            }
            $day[]=[
                'date' =>  date('d', strtotime($date)),
                'stat' => ($this->overallStatus($date) != null) ? $overtime_hour: null,
                'stat_sunday' => $this->overallStatus($date),
                'count'=>$per_day_count
            ];
            $planned_qty[]=[
                'value'=>  $per_day_planned,
                'value_display'=> $per_day_planned,
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
                'count'=>$per_day_count

            ];
            $produced[]=[
                'value'=> $per_day_produced,
                'value_display'=> $per_day_produced,
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
                'count'=>$per_day_count

            ];
            $shift_overtimeshif[]=[
                'value'=> $overtime_hour,
                'value_display'=> $overtime_hour,
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
                'count'=>$per_day_count

            ];

            $shift_regshif[]=[
                'value'=> $per_reg_shift,
                'value_display'=> $per_reg_shift,
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
                'count'=>$per_day_count

            ];
            $total_man_power[]=[
                'value'=>  $per_man_hr,
                'value_display'=> $per_man_hr,
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
                'count'=>$per_day_count

            ];
            
            $total_realization_rate[]=[
                'value'=>  $per_realization_rate,
                'value_display'=>  $per_realization_rate.'%',
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
                'count'=>$per_day_count


            ];
            $avg_man_power[]=[
                'value'=>  ($produced_qty == 0) ? 0 : round((($overtime_hour + $per_reg_shift) *count($job_ticket))/(($produced_qty == 0)? 1 : $produced_qty), 2),
                'value_display'=>  ($produced_qty == 0) ? 0 : round((($overtime_hour + $per_reg_shift) *count($job_ticket))/(($produced_qty == 0)? 1 : $produced_qty), 2),
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
                'count'=>$per_day_count


            ];
            
            $man_hr_with_overtime[]=[
                'value'=>  count($this->get_operator_with_ot($date, $pluck_pro, $operation)),
                'value_display'=>  count($this->get_operator_with_ot($date, $pluck_pro, $operation)),
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
                'count'=>$per_day_count
            ];
        }
        $data2=[
            'data' =>$planned_qty,
            'row_name' =>"PLANNED QUANTITY",
            'total' => collect($planned_qty)->sum('value'),
            'avg' => round( collect($planned_qty)->sum('value') / collect($planned_qty)->where('count', "count")->count('count'), 0)
        ];
        $data3=[
            'data' =>$produced,
            'row_name' =>"TOTAL OUTPUT",
            'total' => collect($produced)->sum('value'),
            'avg' => round(collect($produced)->sum('value') / collect($planned_qty)->where('count', "count")->count('count'), 0)

        ];
        $data4=[
            'data' =>$shift_overtimeshif,
            'row_name' =>"TOTAL HRS. OVERTIME",
            'total' => collect($shift_overtimeshif)->sum('value'),
            'avg' => round(collect($shift_overtimeshif)->sum('value') / collect($planned_qty)->where('count', "count")->count('count'), 0)

        ];
        $data5=[
            'data' =>$shift_regshif,
            'row_name' =>"TOTAL HRS. REGULAR TIME",
            'total' => collect($shift_regshif)->sum('value'),
            'avg' => round(collect($shift_regshif)->sum('value') / collect($planned_qty)->where('count', "count")->count('count'), 0)

        ];
        $data5=[
            'data' =>$shift_regshif,
            'row_name' =>"TOTAL HRS. REGULAR TIME",
            'total' => collect($shift_regshif)->sum('value'),
            'avg' => round(collect($shift_regshif)->sum('value') / collect($planned_qty)->where('count', "count")->count('count'), 0)

        ];
        $data6=[
            'data' =>$total_man_power,
            'row_name' =>"TOTAL MAN HR",
            'total' => collect($total_man_power)->sum('value'),
            'avg' => round(collect($total_man_power)->sum('value') / collect($planned_qty)->where('count', "count")->count('count'), 0)

        ];
        $data7=[
            'data' =>$total_realization_rate,
            'row_name' =>"REALIZATION RATE",
            'total' => collect($total_realization_rate)->whereNotIn('stat',['Sunday', 'Holiday'])->sum('value'),
            'avg' => round(collect($total_realization_rate)->sum('value') / collect($planned_qty)->where('count', "count")->count('count'), 0)

        ];
        $data8=[
            'data' =>$avg_man_power,
            'row_name' =>"AVERAGE MAN-HR UTILIZATION",
            'total' => collect($avg_man_power)->whereNotIn('stat',['Sunday', 'Holiday'])->sum('value'),
            'avg' => round(collect($avg_man_power)->sum('value') / collect($planned_qty)->where('count', "count")->count('count'), 2)

        ];
        $data9=[
            'data' =>$man_hr_with_overtime,
            'row_name' =>"TOTAL MANPOWER W/ OVERTIME",
            'total' => collect($man_hr_with_overtime)->whereNotIn('stat',['Sunday', 'Holiday'])->sum('value'),
            'avg' => round(collect($man_hr_with_overtime)->sum('value') / collect($planned_qty)->where('count', "count")->count('count'), 0)

        ];
        $data=[
            'planned' => $data2,
            'produced' => $data3,
            'total_overtimehrs' => $data4,
            'ma_hr_with_overtime' => $data9,
            'total_reg' => $data5,
            'total_man_hr' => $data6,
            'realization_rate' => $data7,
            'avg_man_power' => $data8
        ];
        $date_column= $day;
        $colspan_date = count($day);
        if($operation == 1){
            return view('tables.tbl_fab_daily_report', compact('data', "date_column", 'colspan_date'));
        }else{
            return view('tables.tbl_fab_daily_report', compact('data', "date_column", 'colspan_date'));
        }
    }
    public function painting_daily_output_chart(Request $request){
        $now = Carbon::now();
        $to=$request->end_date;
        $from=$request->start_date;
        $operation= 1;
        $period = CarbonPeriod::create($from, $to);
        $day=[];
        $hours=0;
        foreach ($period as $date) {
            $production_order_planned=($request->item_classification != "All")? (DB::connection('mysql_mes')->table('production_order')->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')->where('jt.workstation', 'Painting')->whereDate('jt.planned_start_date', $date)->where('production_order.operation_id', $operation)->where('production_order.item_classification', 'like','%'.$request->item_classification.'%')->groupBy('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->select('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->get()):(DB::connection('mysql_mes')->table('production_order')->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')->where('jt.workstation', 'Painting')->whereDate('jt.planned_start_date', $date)->where('production_order.operation_id', $operation)->groupBy('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->select('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->get());
            $production_order_output=($request->item_classification != "All")? (DB::connection('mysql_mes')->table('production_order')->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')->where('jt.workstation', 'Painting')->whereDate('jt.actual_end_date', $date)->where('production_order.operation_id', $operation)->where('production_order.item_classification', 'like','%'.$request->item_classification.'%')->groupBy('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->select('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->get()):(DB::connection('mysql_mes')->table('production_order')->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')->where('jt.workstation', 'Painting')->whereDate('jt.actual_end_date', $date)->where('production_order.operation_id', $operation)->groupBy('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->select('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->get());
            $over_time_shift= DB::connection('mysql_mes')->table('shift_schedule')
            ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
            ->whereDate('shift_schedule.date', $date)
            ->where('shift.operation_id', $operation)
            ->where('shift.shift_type', "Overtime Shift")
            ->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();
            $special_shift= DB::connection('mysql_mes')->table('shift_schedule')
            ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
            ->whereDate('shift_schedule.date', $date)->where('shift.operation_id', $operation)->where('shift.shift_type', "Special Shift")->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();

            if($special_shift){
                $reg_hours = $special_shift->hrs_of_work; 
            }else{
                $reg_shift= DB::connection('mysql_mes')->table('shift')->where('shift.operation_id', $operation)->where('shift.shift_type', "Regular Shift")->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();
                $reg_hours = $reg_shift->hrs_of_work;
            }
            
            $overtime_hour = ($over_time_shift != null) ?  $over_time_shift->hrs_of_work: 0;
            $produced_qty= collect($production_order_planned)->sum('produced_qty');
            $output_qty = (collect($production_order_output)->sum('qty_to_manufacture') == 0) ? 1 : collect($production_order_output)->sum('qty_to_manufacture');
            $per_day_planned=  collect($production_order_planned)->sum('qty_to_manufacture');
            $per_day_produced= collect($production_order_output)->sum('produced_qty');

            if($this->overallStatus($date) == "Sunday" || $this->overallStatus($date) == "Holiday"){
                if($overtime_hour == 0){
                    $per_day_planned=0;
                    $per_day_produced=0;
                }
            }
            $planned_qty[]=[
                'value'=>  $per_day_planned,
                'value_display'=> $per_day_planned,
            ];
            $produced[]=[
                'value'=> $per_day_produced,
                'value_display'=> $per_day_produced,
            ];
            
            $day[]=[
                'date' =>  date('d', strtotime($date)),
            ];
        }
        $planned_data= $planned_qty;
        $produce_data= $produced;
        $date_column= $day;
        $colspan_date = count($day);
        return response()->json(['per_day' => $date_column, 'planned' => $planned_data, 'produced' => $produce_data]);
    }

    public function rejection_report(Request $request){
        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul","Aug", "Sep", "Oct", "Nov", "Dec"];        
        $current= date('n');
        $year= $request->year;
        $reject_category= $request->reject_category;
        $data = [];
        $operation= $request->operation;
        $reject_category_name=$request->reject_name." Reject";
        if($operation == 2){
            $reject_list=DB::connection('mysql_mes')->table('reject_list as rl')
                        ->LeftJoin('reject_reason as rr', 'rl.reject_list_id', 'rr.reject_list_id')
                        ->Leftjoin('job_ticket as jt', 'jt.job_ticket_id', 'rr.job_ticket_id')
                        ->Leftjoin('production_order as pro', 'pro.production_order', 'jt.production_order')
                        ->Leftjoin('quality_inspection as qi', 'qi.qa_id', 'rr.qa_id')
                        ->where('jt.workstation', 'Painting')
                        ->where('pro.operation_id', 1)
                        ->where('rl.reject_category_id', $reject_category)
                        ->select('rl.reject_checklist', 'rl.reject_reason', 'rl.reject_list_id', DB::raw('MONTH(rr.created_at) as month'), DB::raw('YEAR(rr.created_at) as year'), 'jt.job_ticket_id', 'qi.rejected_qty', 'rr.reject_reason_id')
                        ->get();
        }else{
            $reject_list=DB::connection('mysql_mes')->table('reject_list as rl')
                        ->LeftJoin('reject_reason as rr', 'rl.reject_list_id', 'rr.reject_list_id')
                        ->Leftjoin('job_ticket as jt', 'jt.job_ticket_id', 'rr.job_ticket_id')
                        ->Leftjoin('production_order as pro', 'pro.production_order', 'jt.production_order')
                        ->Leftjoin('quality_inspection as qi', 'qi.qa_id', 'rr.qa_id')
                        ->whereNotIn('jt.workstation', ['Painting'])
                        ->where('pro.operation_id', $operation)
                        ->where('rl.reject_category_id', $reject_category)
                        ->select('rl.reject_checklist', 'rl.reject_reason', 'rl.reject_list_id', DB::raw('MONTH(rr.created_at) as month'), DB::raw('YEAR(rr.created_at) as year'), 'jt.job_ticket_id', 'qi.rejected_qty', 'rr.reject_reason_id')
                        ->get();
        }   
        $uniq_rej= collect($reject_list)->uniqueStrict('reject_list_id')->all();
        if($operation == 3){
            $total_output= DB::connection('mysql_mes')->table('production_order')->whereYear('actual_end_date', $year)->whereRaw('parent_item_code = item_code')->where('production_order.operation_id', $operation)->sum('produced_qty');
        }elseif($operation == 2){
            $total_output= DB::connection('mysql_mes')
                ->table('production_order')
                ->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')
                ->where('jt.workstation', "Painting")
                ->whereYear('jt.actual_end_date',$year)
                ->where('production_order.operation_id', 1)
                ->where('jt.process_id', 122)
                ->sum('produced_qty');
        }else{
            $total_output= DB::connection('mysql_mes')->table('production_order')->whereYear('actual_end_date', $year)->where('production_order.operation_id', $operation)->sum('produced_qty');
        }
        $ir= 1;
        foreach($uniq_rej as $row){
            $node=[];
            $days=[];
            $total_output_rate_categ=[];
            $var1= 0;
            foreach ($months as $i => $month) {
                $m= $i +1;
                $var1 += collect($reject_list)->where('month', $m)->where('year', $year)->sum('rejected_qty');
                $node[]=[ 
                    'month' =>  $m,
                    'mon' =>  $month,
                    'sum' => collect($reject_list)->where('reject_reason', $row->reject_reason)->where('reject_checklist',$row->reject_checklist)->where('month', $m)->where('year', $year)->sum('rejected_qty'),
                    'test' =>$var1
                ];
            }
            $toupperspec= strtoupper('Out of Specification/Wrong Dimension');
            $toupperval= strtoupper('Out of Specification');
            $data[]=[
                'reject'=> (strtoupper($row->reject_reason) == $toupperspec || strtoupper($row->reject_reason) == $toupperval) ? $row->reject_reason.'('.$row->reject_checklist.')': $row->reject_reason,
                'id'=> $row->reject_list_id,
                'per_month' => collect($node)->sum('sum'),
                'per_rate' => ($total_output == 0)? 0 :round(collect($node)->sum('sum') /(($total_output) == 0? 1 : $total_output), 4),
                'test'=> $var1,
                'data'=> $node,
                'series' => 'A'.$ir++
            ];
        }

        $month_column=$months;
        $colspan_month=12;
        foreach ($months as $i => $month) {
            $m= $i +1;
            $total_reject_per_month[]=[ 
                'month' =>  $m,
                'sum' => collect($reject_list)->where('month', $m)->where('year', $year)->sum('rejected_qty'),
            ];
            if($operation == 3){
               $total_q= DB::connection('mysql_mes')->table('production_order')->whereMonth('actual_end_date', $m)->whereYear('actual_end_date', $year)->whereRaw('parent_item_code = item_code')->where('production_order.operation_id', $operation)->select('production_order.produced_qty')->sum('produced_qty');
            }elseif($operation == 2){
                $total_q= DB::connection('mysql_mes')
                    ->table('production_order')
                    ->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')
                    ->where('jt.workstation', "Painting")
                    ->whereYear('jt.actual_end_date',$year)
                    ->whereMonth('jt.actual_end_date', $m)
                    ->where('production_order.operation_id', 1)
                    ->where('jt.process_id', 122)
                    ->select('production_order.produced_qty')->sum('produced_qty');
            }else{
                $total_q= DB::connection('mysql_mes')->table('production_order')->whereMonth('actual_end_date', $m)->whereYear('actual_end_date', $year)->where('production_order.operation_id', $operation)->select('production_order.produced_qty')->sum('produced_qty');

            }
            $total_output_per_month[]=[
                'month' => $m,
                'sum' => $total_q
            ];
            $reject_rate[]=[
                'month' => $m,
                'sum' => ($total_q == 0)? 0 : round(collect($reject_list)->where('month', $m)->where('year', $year)->sum('rejected_qty')/(($total_q == 0) ? 1 : $total_q), 4)
            ];

        }
        $total_reject= ($total_output == 0)? 0 :collect($total_reject_per_month)->sum('sum');
        $total_reject_rate= ($total_output == 0)? 0 :round($total_reject/ (($total_output == 0) ? 1: $total_output), 4);
        $reject_rate_for_total_reject= ($total_output == 0)? 0 :round($total_reject/ (($total_output == 0) ? 1: $total_output), 4);
        return view('tables.tbl_rejection_report', compact('data', "month_column", 'colspan_month', 'total_reject_per_month', 'reject_category_name', 'total_output_per_month', 'reject_rate', 'total_output', 'total_reject', 'total_reject_rate', 'reject_rate_for_total_reject'));
    }
    public function rejection_report_chart(Request $request){
        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul","Aug", "Sep", "Oct", "Nov", "Dec"];        
        $current= date('n');
        $year= $request->year;
        $reject_category= $request->reject_category;
        $data = [];
        $operation= $request->operation;
        $reject_category_name=$request->reject_name." Reject";
        if($operation == 2){
            $reject_list=DB::connection('mysql_mes')->table('reject_list as rl')
                        ->LeftJoin('reject_reason as rr', 'rl.reject_list_id', 'rr.reject_list_id')
                        ->Leftjoin('job_ticket as jt', 'jt.job_ticket_id', 'rr.job_ticket_id')
                        ->Leftjoin('production_order as pro', 'pro.production_order', 'jt.production_order')
                        ->Leftjoin('quality_inspection as qi', 'qi.qa_id', 'rr.qa_id')
                        ->where('jt.workstation', 'Painting')
                        ->where('pro.operation_id', 1)
                        ->where('rl.reject_category_id', $reject_category)
                        ->select('rl.reject_checklist', 'rl.reject_reason', 'rl.reject_list_id', DB::raw('MONTH(rr.created_at) as month'), DB::raw('YEAR(rr.created_at) as year'), 'jt.job_ticket_id', 'qi.rejected_qty', 'rr.reject_reason_id')
                        ->get();
        }else{
            $reject_list=DB::connection('mysql_mes')->table('reject_list as rl')
                        ->LeftJoin('reject_reason as rr', 'rl.reject_list_id', 'rr.reject_list_id')
                        ->Leftjoin('job_ticket as jt', 'jt.job_ticket_id', 'rr.job_ticket_id')
                        ->Leftjoin('production_order as pro', 'pro.production_order', 'jt.production_order')
                        ->Leftjoin('quality_inspection as qi', 'qi.qa_id', 'rr.qa_id')
                        ->whereNotIn('jt.workstation', ['Painting'])
                        ->where('pro.operation_id', $operation)
                        ->where('rl.reject_category_id', $reject_category)
                        ->select('rl.reject_checklist', 'rl.reject_reason', 'rl.reject_list_id', DB::raw('MONTH(rr.created_at) as month'), DB::raw('YEAR(rr.created_at) as year'), 'jt.job_ticket_id', 'qi.rejected_qty', 'rr.reject_reason_id')
                        ->get();
        }   
        $uniq_rej= collect($reject_list)->uniqueStrict('reject_list_id')->all();
        if($operation == 3){
            $total_output= DB::connection('mysql_mes')->table('production_order')->whereYear('actual_end_date', $year)->whereRaw('parent_item_code = item_code')->where('production_order.operation_id', $operation)->sum('produced_qty');
        }elseif($operation == 2){
            $total_output= DB::connection('mysql_mes')
                ->table('production_order')
                ->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')
                ->where('jt.workstation', "Painting")
                ->whereYear('jt.actual_end_date',$year)
                ->where('production_order.operation_id', 1)
                ->where('jt.process_id', 122)
                ->sum('produced_qty');
        }else{
            $total_output= DB::connection('mysql_mes')->table('production_order')->whereYear('actual_end_date', $year)->where('production_order.operation_id', $operation)->sum('produced_qty');
        }
        $ir= 1;
        foreach($uniq_rej as $row){
            $node=[];
            $days=[];
            $total_output_rate_categ=[];
            $var1= 0;
            foreach ($months as $i => $month) {
                $m= $i +1;
                $var1 += collect($reject_list)->where('month', $m)->where('year', $year)->sum('rejected_qty');
                $node[]=[ 
                    'month' =>  $m,
                    'mon' =>  $month,
                    'sum' => collect($reject_list)->where('reject_reason', $row->reject_reason)->where('reject_checklist',$row->reject_checklist)->where('month', $m)->where('year', $year)->sum('rejected_qty'),
                    'test' =>$var1
                ];
            }
            $toupperspec= strtoupper('Out of Specification/Wrong Dimension');
            $toupperval= strtoupper('Out of Specification');
            $data[]=[
                'reject'=> (strtoupper($row->reject_reason) == $toupperspec || strtoupper($row->reject_reason) == $toupperval) ? $row->reject_reason.'('.$row->reject_checklist.')': $row->reject_reason,
                'id'=> $row->reject_list_id,
                'per_month' => collect($node)->sum('sum'),
                'per_rate' => ($total_output == 0)? 0 :round(collect($node)->sum('sum') /(($total_output) == 0? 1 : $total_output), 4),
                'target'=> "0.5",
                'series' => 'A'.$ir++
            ];
        }
        return response()->json(['year'=> $data]);
    }
  
    public function parts_output_report(Request $request){
        $now = Carbon::now();
        $to=$request->end_date;
        $from=$request->start_date;
        $operation= $request->operation;
        $period = CarbonPeriod::create($from, $to);
        $day=[];
        if($operation == 2){
            $parts_category=DB::connection('mysql_mes')
                ->table('production_order')
                ->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')
                ->where('jt.workstation', "Painting")
                ->whereBetween('jt.actual_end_date',[$from, $to])
                ->whereNotNull('production_order.parts_category')
                ->where('production_order.operation_id', 1)
                ->where('jt.process_id', 122)
                ->selectRaw('production_order.parts_category, SUM(production_order.produced_qty) as qty, DAY(jt.actual_end_date) as day, MONTH(jt.actual_end_date) as month, YEAR(jt.actual_end_date) as year')
                ->groupBy('production_order.parts_category','day', 'month', 'year')
                ->get();
        }else{
            $parts_category=DB::connection('mysql_mes')
                ->table('production_order')
                ->whereBetween('actual_end_date',[$from, $to])
                ->whereNotNull('parts_category')
                ->where('operation_id', $operation)
                ->selectRaw('parts_category, SUM(produced_qty) as qty, DAY(actual_end_date) as day, MONTH(actual_end_date) as month, YEAR(actual_end_date) as year')
                ->groupBy('parts_category','day', 'month', 'year')->get();
        }
        
        $uniq_parts= collect($parts_category)->unique('parts_category')->all();
        $data=[];
        foreach($uniq_parts as $row){
            $node=[];
            $days=[];

            foreach ($period as $date) {
                $day= date('d', strtotime($date));
                $month= date('m', strtotime($date));
                $year= date('Y', strtotime($date));
                $node[]=[ 
                    'date' =>  date('d', strtotime($date)),
                    'stat' => $this->overallStatus($date),
                    'sum' => collect($parts_category)->where('parts_category', $row->parts_category)->where('day', $day)->where('month', $month)->where('year', $year)->sum('qty')
                ];
                $days[]=[
                    'date' =>  date('d', strtotime($date)),
                    'stat' => ($this->overallStatus($date) != null) ? 0: null,
                    'stat_sunday' => $this->overallStatus($date),
                ];
            }
            $total_day_wtih_qty_fall_in_sundayHoli= collect($node)->whereIn('stat', ['Sunday', 'Holiday'])->where('sum', '!=', 0)->count();
            $total_day_with_qty_wtihout_sundayHoli= collect($node)->whereNotIn('stat', ['Sunday', 'Holiday'])->count();
            $grad_total=$total_day_wtih_qty_fall_in_sundayHoli + $total_day_with_qty_wtihout_sundayHoli;

            $data[]=[
                'parts'=> $row->parts_category,
                'data' => $node,
                'total' => collect($node)->sum('sum'),
                't_day' =>  round( (collect($node)->sum('sum')) / $grad_total, 0)
            ];
        }
        if(!empty($uniq_parts)){
            $date_column=collect($days)->unique('date')->all();
            $colspan_date = count($days);
        }else{
            $date_column=[];
            $colspan_date =0;
        }
        return view('tables.tbl_parts_category_report', compact('data', "date_column", 'colspan_date'));
    }

    public function powder_coating_usage_report(Request $request){
        $year = Carbon::createFromDate($request->year);

        $start_date = $year->copy()->startOfYear()->format('Y-m-d');
        $end_date = $year->copy()->endOfYear()->format('Y-m-d');

        $period = CarbonPeriod::create($start_date, '1 month', $end_date);

        $data = DB::connection('mysql_mes')->table('powder_coating')
            ->whereBetween('date', [$start_date, $end_date])
            ->selectRaw('SUM(consumed_qty) as total_consumed_qty, item_code, MONTH(date) as month, YEAR(date) as year')
            ->whereRaw('YEAR(date) = ' . $request->year)
            ->groupBy('item_code', 'month', 'year')->get();

        $item_codes = array_unique(array_column($data->toArray(), 'item_code'));

        $item_code_inv = DB::connection('mysql_mes')->table('fabrication_inventory')
            ->whereIn('item_code', $item_codes)->select('item_code', 'color_code', 'description')
            ->distinct('item_code')->orderBy('item_code', 'desc')->get();

        $arr_list = [];
        foreach($period as $i => $date){
            $month = Carbon::parse($date)->format('m');
            $month_name = Carbon::parse($date)->format('M');
            $arr_list[$i]['month'] = $month_name;
            foreach($item_code_inv as $e => $item){
                $total = collect($data)->where('item_code', $item->item_code)->where('month', $month)->sum('total_consumed_qty');

                $arr_list[$i]['item_' . $e] = $total;
            }
        }

        $result = [
            'item_codes' => $item_code_inv,
            'data' => $arr_list
        ];

        return $result;
    }

    public function powder_coat_usage_history(Request $request){
        $data=[];
        $powder_data= DB::connection('mysql_mes')->table('powder_coating')
            ->join('shift', 'shift.shift_id','powder_coating.operating_hrs')
            ->whereRaw('YEAR(powder_coating.date) = ' . $request->year)
            ->orderBy('powder_coating_id','desc')->paginate(10);

        $count = DB::connection('mysql_mes')->table('powder_coating')
            ->join('shift', 'shift.shift_id','powder_coating.operating_hrs')
            ->whereRaw('YEAR(powder_coating.date) = ' . $request->year)
            ->sum('consumed_qty');

        foreach ($powder_data as $row) {
            $shift=DB::connection('mysql_mes')->table('shift')
                ->join('operation as op','op.operation_id','shift.operation_id')
                ->where('op.operation_name','Painting')
                ->where('shift.shift_id',  $row->operating_hrs)
                ->select('shift.*')->first();

            $item_details = DB::connection('mysql_mes')->table('fabrication_inventory')
                ->where('item_code', $row->item_code)->select('uom', 'description')->first();

            $data[]=[
                'date' =>  date('F d, Y', strtotime($row->date)),
                'shift_type' => $shift->shift_type,
                'operating_hrs' => $shift->time_in.' - '.$shift->time_out,
                'current_qty' => ($row->current_qty == null)? '0':$row->current_qty,
                'consumed_qty' => ($row->consumed_qty == null)? '0':$row->consumed_qty,
                'balance_qty' => $row->balance_qty,
                'item_code' => $row->item_code,
                'operator_name' => $row->operator,
                'uom'=> empty($item_details->uom) ? "" : $item_details->uom,
                'description'=> ($item_details) ? $item_details->description : null,
            ];
        }
        
        return view('link_report.powder_coat_usage_report', compact('data', 'count', 'powder_data'));
    }

    public function print_qa_rejection_report(Request $request){
        $requests = $request->all();
        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul","Aug", "Sep", "Oct", "Nov", "Dec"];        
        $current= date('n');
        $year= $request->year;
        $reject_category= $request->reject_category;
        $data = [];
        $operation= $request->operation;
        $reject_category_name=$request->reject_name." Reject";
        if($operation == 2){
            $reject_list=DB::connection('mysql_mes')->table('reject_list as rl')
                        ->LeftJoin('reject_reason as rr', 'rl.reject_list_id', 'rr.reject_list_id')
                        ->Leftjoin('job_ticket as jt', 'jt.job_ticket_id', 'rr.job_ticket_id')
                        ->Leftjoin('production_order as pro', 'pro.production_order', 'jt.production_order')
                        ->Leftjoin('quality_inspection as qi', 'qi.qa_id', 'rr.qa_id')
                        ->where('jt.workstation', 'Painting')
                        ->where('pro.operation_id', 1)
                        ->where('rl.reject_category_id', $reject_category)
                        ->select('rl.reject_checklist', 'rl.reject_reason', 'rl.reject_list_id', DB::raw('MONTH(rr.created_at) as month'), DB::raw('YEAR(rr.created_at) as year'), 'jt.job_ticket_id', 'qi.rejected_qty', 'rr.reject_reason_id')
                        ->get();
        }else{
            $reject_list=DB::connection('mysql_mes')->table('reject_list as rl')
                        ->LeftJoin('reject_reason as rr', 'rl.reject_list_id', 'rr.reject_list_id')
                        ->Leftjoin('job_ticket as jt', 'jt.job_ticket_id', 'rr.job_ticket_id')
                        ->Leftjoin('production_order as pro', 'pro.production_order', 'jt.production_order')
                        ->Leftjoin('quality_inspection as qi', 'qi.qa_id', 'rr.qa_id')
                        ->whereNotIn('jt.workstation', ['Painting'])
                        ->where('pro.operation_id', $operation)
                        ->where('rl.reject_category_id', $reject_category)
                        ->select('rl.reject_checklist', 'rl.reject_reason', 'rl.reject_list_id', DB::raw('MONTH(rr.created_at) as month'), DB::raw('YEAR(rr.created_at) as year'), 'jt.job_ticket_id', 'qi.rejected_qty', 'rr.reject_reason_id')
                        ->get();
        }   
        $uniq_rej= collect($reject_list)->uniqueStrict('reject_list_id')->all();
        if($operation == 3){
            $total_output= DB::connection('mysql_mes')->table('production_order')->whereYear('actual_end_date', $year)->whereRaw('parent_item_code = item_code')->where('production_order.operation_id', $operation)->sum('produced_qty');
        }elseif($operation == 2){
            $total_output= DB::connection('mysql_mes')
                ->table('production_order')
                ->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')
                ->where('jt.workstation', "Painting")
                ->whereYear('jt.actual_end_date',$year)
                ->where('production_order.operation_id', 1)
                ->where('jt.process_id', 122)
                ->sum('produced_qty');
        }else{
            $total_output= DB::connection('mysql_mes')->table('production_order')->whereYear('actual_end_date', $year)->where('production_order.operation_id', $operation)->sum('produced_qty');
        }
        $ir= 1;
        foreach($uniq_rej as $row){
            $node=[];
            $days=[];
            $total_output_rate_categ=[];
            $var1= 0;
            foreach ($months as $i => $month) {
                $m= $i +1;
                $var1 += collect($reject_list)->where('month', $m)->where('year', $year)->sum('rejected_qty');
                $node[]=[ 
                    'month' =>  $m,
                    'mon' =>  $month,
                    'sum' => collect($reject_list)->where('reject_reason', $row->reject_reason)->where('reject_checklist',$row->reject_checklist)->where('month', $m)->where('year', $year)->sum('rejected_qty'),
                    'test' =>$var1
                ];
            }
            $toupperspec= strtoupper('Out of Specification/Wrong Dimension');
            $toupperval= strtoupper('Out of Specification');
            $data[]=[
                'reject'=> (strtoupper($row->reject_reason) == $toupperspec || strtoupper($row->reject_reason) == $toupperval) ? $row->reject_reason.'('.$row->reject_checklist.')': $row->reject_reason,
                'id'=> $row->reject_list_id,
                'per_month' => collect($node)->sum('sum'),
                'per_rate' => ($total_output == 0)? 0 :round(collect($node)->sum('sum') /(($total_output) == 0? 1 : $total_output), 4),
                'test'=> $var1,
                'data'=> $node,
                'series' => 'A'.$ir++
            ];
        }

        $month_column=$months;
        $colspan_month=12;
        foreach ($months as $i => $month) {
            $m= $i +1;
            $total_reject_per_month[]=[ 
                'month' =>  $m,
                'sum' => collect($reject_list)->where('month', $m)->where('year', $year)->sum('rejected_qty'),
            ];
            if($operation == 3){
               $total_q= DB::connection('mysql_mes')->table('production_order')->whereMonth('actual_end_date', $m)->whereYear('actual_end_date', $year)->whereRaw('parent_item_code = item_code')->where('production_order.operation_id', $operation)->select('production_order.produced_qty')->sum('produced_qty');
            }elseif($operation == 2){
                $total_q= DB::connection('mysql_mes')
                    ->table('production_order')
                    ->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')
                    ->where('jt.workstation', "Painting")
                    ->whereYear('jt.actual_end_date',$year)
                    ->whereMonth('jt.actual_end_date', $m)
                    ->where('production_order.operation_id', 1)
                    ->where('jt.process_id', 122)
                    ->select('production_order.produced_qty')->sum('produced_qty');
            }else{
                $total_q= DB::connection('mysql_mes')->table('production_order')->whereMonth('actual_end_date', $m)->whereYear('actual_end_date', $year)->where('production_order.operation_id', $operation)->select('production_order.produced_qty')->sum('produced_qty');

            }
            $total_output_per_month[]=[
                'month' => $m,
                'sum' => $total_q
            ];
            $reject_rate[]=[
                'month' => $m,
                'sum' => ($total_q == 0)? 0 : round(collect($reject_list)->where('month', $m)->where('year', $year)->sum('rejected_qty')/(($total_q == 0) ? 1 : $total_q), 4)
            ];

        }
        $total_reject= ($total_output == 0)? 0 :collect($total_reject_per_month)->sum('sum');
        $total_reject_rate= ($total_output == 0)? 0 :round($total_reject/ (($total_output == 0) ? 1: $total_output), 4);
        $reject_rate_for_total_reject= ($total_output == 0)? 0 :round($total_reject/ (($total_output == 0) ? 1: $total_output), 4);
        return view('link_report.print_qa_rejection_report', compact('data', "month_column", 'colspan_month', 'total_reject_per_month', 'reject_category_name', 'total_output_per_month', 'reject_rate', 'total_output', 'total_reject', 'total_reject_rate', 'reject_rate_for_total_reject', 'requests'));
    }

    public function mismatched_po_status(Request $request){
        $permissions = $this->get_user_permitted_operation();
        $mes_statuses = DB::connection('mysql_mes')->table('production_order')->select('status')->distinct('status')->pluck('status');

        $erp_po = DB::connection('mysql')->table('tabWork Order')->whereIn('status', $mes_statuses)->select('name', 'status', 'produced_qty')->get();
        $erp_production_orders = collect($erp_po)->map(function ($q){
            return $q->name;
        });

        $erp_po = collect($erp_po)->groupBy('name');

        $mes_po = DB::connection('mysql_mes')->table('production_order')->whereIn('production_order', $erp_production_orders)->where('status', 'Completed')->select('created_at', 'created_by',  'production_order', 'status', 'feedback_qty')->orderBy('created_at', 'desc')->get();
        
        $mismatched_production_orders = [];
        foreach($mes_po as $po){
            if(isset($erp_po[$po->production_order])){
                $erp_status = $erp_po[$po->production_order][0]->status == 'In Process' ? 'In Progress' : $erp_po[$po->production_order][0]->status;
                $erp_produced_qty = $erp_po[$po->production_order][0]->produced_qty * 1;
                if($po->status != $erp_status){
                    $mismatched_production_orders[] = [
                        'created_at' => $po->created_at,
                        'owner' => $po->created_by,
                        'production_order' => $po->production_order,
                        'mes_status' => $po->status,
                        'mes_feedback_qty' => $po->feedback_qty,
                        'erp_status' => $erp_status,
                        'erp_produced_qty' => $erp_produced_qty 
                    ];
                }else{
                    continue;
                }
            }
        }

        $total = count($mismatched_production_orders);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // Create a new Laravel collection from the array data3
        $itemCollection = collect($mismatched_production_orders);
        // Define how many items we want to be visible in each page
        $perPage = 20;
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
        // set url path for generted links
        $paginatedItems->setPath($request->url());
        $mismatched_production_orders = $paginatedItems;

        return view('reports.system_audit_mismatched_po_status', compact('mismatched_production_orders', 'total', 'permissions'));
    }

    public function feedbacked_po_with_pending_ste(){
        $permissions = $this->get_user_permitted_operation();
        $erp_po = DB::connection('mysql')->table('tabWork Order')->where('status', 'Completed')->orderBy('creation', 'desc')->pluck('name');
        
        $ste = DB::connection('mysql')->table('tabStock Entry')->whereIn('work_order', $erp_po)->whereIn('purpose', ['Material Transfer for Manufacture', 'Material Transfer'])->where('docstatus', 0)->select('creation', 'owner', 'work_order', 'name', 'purpose', 'docstatus')->orderBy('creation', 'desc')->paginate(20);

        return view('reports.system_audit_feedbacked_po_w_pending_ste', compact('ste', 'permissions'));
    }

	public function transferred_required_qty_mismatch(){
        $permissions = $this->get_user_permitted_operation();
		$transferred_required_qty_mismatch = DB::connection('mysql')->table('tabWork Order as wo')
			->join('tabWork Order Item as woi', 'wo.name', 'woi.parent')
            ->where('wo.status', 'Completed')->whereRaw('woi.transferred_qty < woi.required_qty')
            ->select('wo.name', 'wo.status', 'woi.item_code', 'woi.required_qty', 'woi.transferred_qty', 'wo.creation', 'wo.owner')
            ->orderBy('wo.creation', 'desc')
            ->paginate(20);

        return view('reports.system_audit_transfer_required_mismatch', compact('transferred_required_qty_mismatch', 'permissions'));
	}

    public function overridden_production_orders(){
        $permissions = $this->get_user_permitted_operation();
        $overridden_job_tickets = DB::connection('mysql_mes')->table('production_order as po')
            ->join('job_ticket as jt', 'jt.production_order', 'po.production_order')
            ->where('po.status', 'Completed')->where('jt.status', '!=', 'Completed')->where('jt.remarks', 'Override')
            ->select('po.production_order', 'jt.job_ticket_id', 'jt.status', 'jt.created_by', 'jt.created_at')->orderBy('jt.created_at', 'desc')->paginate(20);

        return view('reports.system_audit_overridden_po', compact('overridden_job_tickets', 'permissions'));
    }

    public function stocks_transferred_but_none_in_wip(Request $request){
        $permissions = $this->get_user_permitted_operation();
        $warehouses = ['Work In Progress - FI', 'Assembly Warehouse - FI'];
        $filter_warehouses = ['Work In Progress - FI', 'Assembly Warehouse - FI'];
        if($request->warehouse){
            $warehouses = [$request->warehouse];
        }

        $erp_po = DB::connection('mysql')->table('tabWork Order as wo')
            ->join('tabWork Order Item as woi', 'woi.parent', 'wo.name')
            ->when($request->search, function ($q) use ($request){
                return $q->where('woi.item_code', 'LIKE', '%'.$request->search.'%');
            })
            ->where('wo.docstatus', 1)->whereNotIn('wo.status', ['Cancelled', 'Stopped', 'Completed'])
            ->whereIn('wo.wip_warehouse', $warehouses)->where('woi.transferred_qty', '>', 0)
			->select('wo.name', 'wo.status', 'wo.wip_warehouse', 'woi.item_code', 'woi.transferred_qty', 'woi.required_qty', 'woi.consumed_qty', 'woi.stock_uom', 'wo.creation', 'wo.owner')
            ->orderBy('creation', 'desc')->get();

        $item_codes = collect($erp_po)->pluck('item_code')->unique();

        $po_collection = collect($erp_po)->groupBy('wip_warehouse')->transform(function($item, $k) {
            return $item->groupBy('item_code');
        });

        $bin = DB::connection('mysql')->table('tabBin')->whereIn('warehouse', $warehouses)->whereIn('item_code', $item_codes)
            ->select('warehouse', 'item_code', 'actual_qty', 'stock_uom', 'creation', 'owner')->orderBy('item_code', 'asc')->get();

        $bin_arr = [];
        foreach($bin as $b){
            if(isset($po_collection[$b->warehouse][$b->item_code])){
				$required_qty = (float)$po_collection[$b->warehouse][$b->item_code]->sum('required_qty');
				$transferred_qty = (float)$po_collection[$b->warehouse][$b->item_code]->sum('transferred_qty');
				$consumed_qty = (float)$po_collection[$b->warehouse][$b->item_code]->sum('consumed_qty');
				$remaining = (float)($transferred_qty - $consumed_qty);
				
				if((float)$b->actual_qty < $remaining){
					$bin_arr[] = [
						'item_code' => $b->item_code,
						'warehouse' => $b->warehouse,
						'actual_qty' => (float)$b->actual_qty,
						'transferred_qty' => $transferred_qty,
						'remaining' => $remaining,
						'uom' => $b->stock_uom,
						'production_orders' => $po_collection[$b->warehouse][$b->item_code],
					];
				}
			}
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // Create a new Laravel collection from the array data3
        $itemCollection = collect($bin_arr);
        // Define how many items we want to be visible in each page
        $perPage = 25;
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
        // set url path for generted links
        $paginatedItems->setPath($request->url());
        $bin_arr = $paginatedItems;

        return view('reports.system_audit_stocks_transferred_but_none_in_wip', compact('bin_arr', 'filter_warehouses', 'permissions'));
    }

    public function inaccurate_operator_feedback(Request $request){
        if($request->ajax()){
            $exploded_date = explode(' to ', $request->date_range);
            $start_date = isset($exploded_date[0]) ? Carbon::parse($exploded_date[0]) : null;
            $end_date = isset($exploded_date[1]) ? Carbon::parse($exploded_date[1]) : null;

            $operation_id = $request->operation;

            $spotwelding_query = [];
            if($operation_id == 1){
                $spotwelding_query = DB::connection('mysql_mes')->table('job_ticket as jt')
                ->join('production_order as po', 'po.production_order', 'jt.production_order')
                    ->join('process as p', 'p.process_id', 'jt.process_id')
                    ->join('workstation as w', 'jt.workstation', 'w.workstation_name')
                    ->join('spotwelding_qty as tl', 'tl.job_ticket_id', 'jt.job_ticket_id')
                    ->when($request->date_range, function ($q) use ($start_date, $end_date){
                        return $q->whereBetween('tl.created_at', [$start_date, $end_date]);
                    })
                    ->whereRaw('TIMESTAMPDIFF(second, tl.from_time, tl.to_time) <= 60')->where('tl.good', '>=', 5)->where('jt.workstation', 'Spotwelding')->where('w.operation_id', 1)
                    ->selectRaw('jt.production_order, p.process_name, w.workstation_name, tl.good, tl.reject, tl.from_time, tl.to_time, TIMESTAMPDIFF(second, tl.from_time, tl.to_time) as duration_in_seconds, tl.operator_name, tl.created_at, po.item_code, po.description as item_description')->orderBy('tl.created_at', 'desc');
            }

            $query = DB::connection('mysql_mes')->table('operation as op')
                ->join('workstation as w', 'w.operation_id', 'op.operation_id')
                ->join('job_ticket as jt', 'jt.workstation', 'w.workstation_name')
                ->join('production_order as po', 'po.production_order', 'jt.production_order')
                ->join('process as p', 'p.process_id', 'jt.process_id')
                ->join('time_logs as tl', 'tl.job_ticket_id', 'jt.job_ticket_id')
                ->when($request->date_range, function ($q) use ($start_date, $end_date){
                    return $q->whereBetween('tl.created_at', [$start_date, $end_date]);
                })
                ->when($operation_id, function ($q) use ($operation_id){
                    return $q->where('op.operation_id', $operation_id);
                })
                ->where('w.workstation_name', $operation_id == 2 ? '=' : '!=', 'Painting')
                ->whereRaw('TIMESTAMPDIFF(second, tl.from_time, tl.to_time) <= 60')->where('tl.good', '>=', 5)
                ->selectRaw('jt.production_order, p.process_name, w.workstation_name, tl.good, tl.reject, tl.from_time, tl.to_time, TIMESTAMPDIFF(second, tl.from_time, tl.to_time) as duration_in_seconds, tl.operator_name, tl.created_at, po.item_code, po.description as item_description')->orderBy('tl.created_at', 'desc')
                ->when($operation_id == 1, function ($q) use ($spotwelding_query){
                    return $q->union($spotwelding_query);
                })
                ->get();

            $query_result = collect($query)->groupBy('operator_name');

            $report = [];
            foreach ($query_result as $operator => $rows) {
                $report[] = [
                    'operator_name' => $operator,
                    'process_count' => collect($rows)->count(),
                    'completed_qty' => collect($rows)->sum('good'),
                ];

            }

            $report = collect($report)->sortBy('process_count')->reverse()->toArray();

            return view('reports.inaccurate_operator_feedback_tbl', compact('report'));
        }

		$permissions = $this->get_user_permitted_operation();

        $operations = DB::connection('mysql_mes')->table('operation')->get();

        return view('reports.inaccurate_operator_feedback', compact('operations', 'permissions'));
    }
}