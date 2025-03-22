<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Http;

class TestingEnvironmentController extends Controller
{
    //
    public function local_login(){
        Auth::loginUsingId(306);
        return redirect('/main_dashboard');
    }

    public function admin_login(){
        Auth::loginUsingId(105);
        return redirect('/main_dashboard');
    }

    public function get_unassigned(){
        $operation_id = 3;
        // $scheduled_date = '2023-06-22';

        $assigned_production = DB::connection('mysql_mes')->table('assembly_conveyor_assignment')->get();

        $not_included = ['Cancelled', 'Closed', 'Feedbacked', 'Completed'];
        // $not_included = ['Cancelled', 'Closed'];
        $unassigned_production = DB::connection('mysql_mes')->table('production_order')
            ->where('operation_id', $operation_id)->whereNotIn('status', $not_included)
            ->whereNotIn('production_order', array_column($assigned_production->toArray(), 'production_order'))
            ->whereRaw('qty_to_manufacture > feedback_qty')
            ->whereIn('production_order', ['PROM-34037',
            'PROM-34500',
            'PROM-34730',
            'PROM-34765',
            'PROM-34842',
            'PROM-35219',
            'PROM-40015',
            'PROM-47928',
            'PROM-51049',
            'PROM-51645',
            'PROM-52457',
            'PROM-52726',
            'PROM-53162',
            'PROM-54781',
            'PROM-55194',
            'PROM-58893',
            'PROM-60521',
            'PROM-61700',
            'PROM-61738',
            'PROM-61739',
            'PROM-64416',
            'PROM-64861',
            'PROM-64906',
            'PROM-65762'])
            ->whereDate('planned_start_date', '<=', Carbon::now())->orderBy('created_at', 'desc')->get();

        return view('for-testing.table', compact('unassigned_production'));

    }

    public function parent_item_in_child_bom(){
        return $report = DB::table('tabBOM as bom')
            ->join('tabBOM Item as item', 'bom.name', 'item.parent')
            ->whereRaw('bom.item = item.item_code')
            ->select('bom.name', 'bom.item', 'item.item_name', 'item.item_code')
            ->get();
    }

    public function get_mismatched_po(){
        $machine_hours = DB::connection('mysql_mes')->table('time_logs')->whereMonth('created_at', 5)->whereYear('created_at', 2023)->where('machine_code', 'M00205')->orderBy('created_at', 'desc')->get();
        return $machine_hours;
    }

    public function update_customer_name(){
        $production_orders = DB::connection('mysql_mes')->table('production_order')
            ->whereNull('customer_name')->whereDate('created_at', '>=', Carbon::parse('2024-09-01')->startOfDay()->toDateTimeString())->get();

            return $production_orders;
    }

    public function cancel_feedback(){
        return view('for_testing_only.general_testing');
    }

    public function testing(Request $request){
		$now = Carbon::parse('2024-08-01');

        $spotwelding = DB::connection('mysql_mes')->table('production_order as p')
            ->join('job_ticket as j', 'p.production_order', 'j.production_order')
            ->join('spotwelding_qty as t', 't.job_ticket_id', 'j.job_ticket_id')
            ->whereIn('p.status', ['Feedbacked', 'Completed'])
            ->select('p.production_order', 'j.job_ticket_id', 'p.status', 'p.parent_item_code', 'p.sub_parent_item_code', 'p.item_code', 'j.workstation', 'j.actual_start_date', 'j.actual_end_date', 'j.completed_qty', 'j.good', 'j.reject', 'j.last_modified_by', 'j.created_by', 't.time_log_id', 't.from_time', 't.to_time', 't.duration', DB::raw('"spotwelding" as parent'))
            ->where('j.created_by', '!=', 'Jave Judiel M. Kulong')
            ->where('j.last_modified_by', '!=', 'Jave Judiel M. Kulong')
            ->limit(10);

        $time_logs = DB::connection('mysql_mes')->table('production_order as p')
            ->join('job_ticket as j', 'p.production_order', 'j.production_order')
            ->join('time_logs as t', 't.job_ticket_id', 'j.job_ticket_id')
            ->whereIn('p.status', ['Feedbacked', 'Completed'])
            ->where('j.created_by', '!=', 'Jave Judiel M. Kulong')
            ->where('j.last_modified_by', '!=', 'Jave Judiel M. Kulong')
            ->select('p.production_order', 'j.job_ticket_id', 'p.status', 'p.parent_item_code', 'p.sub_parent_item_code', 'p.item_code', 'j.workstation', 'j.actual_start_date', 'j.actual_end_date', 'j.completed_qty', 'j.good', 'j.reject', 'j.last_modified_by', 'j.created_by', 't.time_log_id', 't.from_time', 't.to_time', 't.duration', DB::raw('"time_logs" as parent'))
            ->limit(10)
            ->orderByDesc('p.last_modified_at')
            ->unionAll($spotwelding)
            ->get();

        $grouped = collect($time_logs)->groupBy('production_order');

        $list = [];
        foreach($grouped as $production_order => $time_logs){
            $total_duration_minutes = 0;
            $time_logs = collect($time_logs)->map(function ($time_log) use (&$total_duration_minutes) {
                $duration_minutes = Carbon::parse($time_log->to_time)->diffInMinutes($time_log->from_time);
                $total_duration_minutes += $duration_minutes;
                return $time_log;
            });
        
            $hours = floor($total_duration_minutes / 60);
            $minutes = $total_duration_minutes % 60;
        
            $total_duration = $hours . ' hour(s) and ' . $minutes . ' minute(s)';
        
            $list[$production_order] = compact('time_logs', 'total_duration');
        }

        return $list;

        return $request->ip();
        $user = Auth::user();
        $department = DB::connection('mysql_essex')->table('departments')->where('department_id', $user->department_id)->pluck('department')->first();
            $is_admin = $department && $department == 'Information Technology' ? 1 : 0;

            $role_permissions = DB::connection('mysql_mes')->table('role_permissions')
                ->select('user_group_id', 'permission')->get();

            $permissionsArray = [];

			foreach ($role_permissions as $permission) {
				$permissionsArray[$permission->permission][] = $permission->user_group_id;
			}

			$user_roles = DB::connection('mysql_mes')->table('user')
				->where('user_access_id', $user->user_id)->whereNotNull('user_group_id')
				->distinct()->pluck('user_group_id')->toArray();

                $arr = [];
			foreach ($permissionsArray as $title => $roles) {
                // Gate::define($title, function () use ($roles, $user_roles, $is_admin) {
                //     return count(array_intersect($user_roles, $roles)) > 0 || $is_admin;
                // });
                    $arr[] = [
                        'is_admin' => $is_admin,
                        'title' => $title
                    ]; 

            }

            return $arr;

        $allowed_modules = [];
        $allowed_modules[] = \Gate::any(['manage-users']) ? 'manage-users' : [];
        $allowed_modules[] = \Gate::any(['manage-user-groups']) ? 'manage-user-groups' : [];
        $allowed_modules[] = \Gate::any(['manage-email-notifications']) ? 'manage-email-notifications' : [];
        $allowed_modules[] = \Gate::any(['manage-role-permissions']) ? 'manage-role-permissions' : [];

        return $allowed_modules;
    }

    public function get_unreturned_samples(){
        $order_list = DB::connection('live_db')->table('tabMaterial Request as mr')
            ->join('tabMaterial Request Item as mri', 'mr.name', 'mri.parent')
            ->where('mr.purpose', 'Sample Order')->where('mr.company', 'FUMACO Inc.')->where('mr.docstatus', 1)
            ->select('mr.name as order_id', 'mr.customer as customer_name', 'mr.*', 'mri.*')
            ->orderBy('mr.creation', 'desc')
            ->get()
            // ->groupBy('sales_order_id')
            ;

            // return collect($order_list)->pluck('order_id')->implode('","');

            $mr_ref = collect($order_list)->groupBy('order_id');

        $rr_list = DB::connection('live_db')->table('tabStock Entry as se')
            ->join('tabStock Entry Detail as sed', 'sed.parent', 'se.name')
            ->where('se.docstatus', 1)
            ->whereIn('se.transfer_as', ['For Return', 'Sample Item'])
            ->whereRaw('se.reference_no IN ("'.collect($order_list)->pluck('order_id')->implode('","').'")')
            ->select('se.creation', 'sed.name as ste_details', 'sed.item_code', 'sed.description', 'sed.qty', 'se.name as ste_name', 'se.reference_no', 'se.transfer_as', 'se.docstatus', 'se.delivery_date', 'sed.stock_uom')
            ->orderBy('se.creation', 'desc')
            ->get()
            ->groupBy(['reference_no', 'item_code'])
            ;

            // return $rr_list;

        $arr = [];
        foreach($rr_list as $mreq => $list){
            // return $list;
            // $arr[$mreq][] = [];
            // $arr[$mreq] = collect($list)->filter()
            foreach ($list as $item_code => $value) {
            // $arr[$mreq] = collect($list)->filter()
                // return $value;

                // return collect($value)->pluck('transfer_as');
                if(!in_array('For Return', collect($value)->pluck('transfer_as')->toArray())){
                    foreach ($value as $item) {
                        $arr[] = [
                            'mreq' => $mreq,
                            'ste' => $item->ste_name,
                            'customer' => isset($mr_ref[$mreq]) ? $mr_ref[$mreq][0]->customer : null,
                            'sales_person' => isset($mr_ref[$mreq]) ? $mr_ref[$mreq][0]->sales_person : null,
                            'item_code' => $item->item_code,
                            'description' => $item->description,
                            'delivery_date' => $item->delivery_date,
                            'qty' => $item->qty,
                            'stock_uom' => $item->stock_uom,
                        ];
                    }
                    
                }
            }
        }

        $arr = collect($arr)->sortByDesc('delivery_date');

        return view('for_testing_only.get_unreturned_samples', compact('arr'));
    }

    public function activity_logs_testing(Request $request){
        return $request->route()->getName();
    }

    public function get_completed_so_with_pending_production_orders(){
        // return 1;
        $erp = ENV('DB_DATABASE_ERP');
        $mes = ENV('DB_DATABASE_MES');
        // $report = DB::connection('live_db')->table($erp.'.tabSales Order as so')
        //     ->join($mes.'.production_order as po', 'po.sales_order', 'so.name')
        //     ->where('so.company', 'FUMACO Inc.')
        //     ->where('so.status', 'Completed')->whereNotIn('po.status', ['Closed', 'Cancelled', 'Completed'])->whereRaw('po.qty_to_manufacture > po.feedback_qty')
        //     ->select('so.creation as date_created', 'so.owner as created_by', 'so.status as so_status', 'po.status as po_status', 'po.production_order', 'so.*', 'po.*')
        //     ->orderBy('so.creation', 'desc')->get();


        $report = DB::table($erp.'.tabMaterial Request as mreq')
            ->join($mes.'.production_order as po', 'po.material_request', 'mreq.name')
            ->where('mreq.company', 'FUMACO Inc.')
            ->where('mreq.status', 'Transferred')->whereNotIn('po.status', ['Closed', 'Cancelled', 'Completed'])->whereRaw('po.qty_to_manufacture > po.feedback_qty')
            ->select('mreq.creation as date_created', 'mreq.owner as created_by', 'mreq.status as mreq_status', 'po.status as po_status', 'po.production_order', 'mreq.*', 'po.*')
            ->limit(20)
            ->orderBy('mreq.creation', 'desc')->get();

        // $unable_to_close = [];
        // foreach ($report as $val) {
        //     $close_production_order = $this->close_production_order($val->production_order);

        //     if(!$close_production_order->original['success']){
        //         $unable_to_close[] = $val->production_order;
        //     }
        // }

        // return $unable_to_close;
        // return $report;
        return view('for_testing_only.table', compact('report'));
    }

    private function close_production_order($production_order)
    {
        DB::connection('mysql')->beginTransaction();
        DB::connection('mysql_mes')->beginTransaction();
        try {
            $now = Carbon::now();

            $reason = 'Production Order status discrepancy with Sales Order status';

            // check for task in progress
            $task_in_progress = DB::connection('mysql_mes')->table('job_ticket')
                ->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
                ->where('production_order', $production_order)
                ->where('time_logs.status', 'In Progress')->count();

            if ($task_in_progress > 0) {
                return response()->json(['success' => 0, 'message' => 'Cannot close production order with on-going task by operator. ' . $production_order]);
            }

            // $stock_entries = DB::connection('mysql')->table('tabStock Entry')->where('work_order', $production_order)->where('docstatus', 0)->orWhere('item_status', 'For Checking')->where('work_order', $production_order)->get();

            // if ($stock_entries) {
            //     $draft_stes = collect($stock_entries)->pluck('name');

            //     DB::connection('mysql')->table('tabStock Entry')->whereIn('name', $draft_stes)->delete();
            //     DB::connection('mysql')->table('tabStock Entry Detail')->whereIn('parent', $draft_stes)->delete();
            // }

            DB::connection('mysql')->table('tabWork Order')
                ->where('name', $production_order)->where('docstatus', 1)->where('status', '!=', 'Completed')
                ->update([
                    'status' => 'Stopped',
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->email
                ]);

            DB::connection('mysql_mes')->table('production_order')
                ->where('production_order', $production_order)
                ->update([
                    'status' => 'Closed',
                    'last_modified_at' => $now->toDateTimeString(),
                    'last_modified_by' => Auth::user()->email,
                    'remarks' => $reason
                ]);

            DB::connection('mysql_mes')->table('activity_logs')->insert([
                'action' => 'Production Order Closed',
                'message' => 'Production Order ' . $production_order . ' has been closed by ' . Auth::user()->employee_name . ' at ' . Carbon::now()->toDateTimeString() . '<br>Reason: ' . $reason,
                'reference' => $production_order,
                'created_at' => Carbon::now()->toDateTimeString(),
                'created_by' => Auth::user()->email
            ]);

            // DB::connection('mysql')->commit();
            // DB::connection('mysql_mes')->commit();
            return response()->json(['success' => 1, 'message' => 'Production Order <b>' . $production_order . '</b> has been closed.']);
            // return 1;
        } catch (\Exception $e) {
            DB::connection('mysql')->rollback();
            DB::connection('mysql_mes')->rollback();
            return response()->json(['success' => 0, 'message' => 'There was a problem creating transaction.']);
            // return 0;
        }
    }

    public function completed_po_with_pending_jt(){
        DB::beginTransaction();
        try {
            $production_orders = DB::connection('live_mes')->table('production_order as po')
                ->join('job_ticket as jt', 'jt.production_order', 'po.production_order')
                ->whereNotIn('po.status', ['Not Started', 'Closed', 'Cancelled'])
                ->whereRaw('po.qty_to_manufacture = po.feedback_qty')
                ->whereIn('jt.status', ['In Progress', 'Pending'])
                ->whereYear('po.created_at', '<=', 2022)
                ->select('po.status as po_status', 'jt.status as jt_status', 'jt.job_ticket_id', 'po.*', 'jt.*')
                ->orderByRaw("FIELD(po.status, 'In Progress', 'Completed', 'Feedbacked') ASC")
                ->get();

            $now = Carbon::now();

            foreach ($production_orders as $val) {
                DB::connection('live_mes')->table('production_order')->where('production_order', $val->production_order)->update([
                    'status' => 'Feedbacked',
                    'last_modified_at' => $now->toDateTimeString(),
                    'last_modified_by' => Auth::user()->email
                ]);
            }

            // DB::commit();

            return view('for_testing_only.completed_po_with_pending_jt', compact('production_orders'));
            return $production_orders;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function get_so_received(){
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $monthlyOrders = DB::connection('live_erp')->table('tabSales Order')->select(
                DB::raw('MONTH(creation) as month'),
                DB::raw('COUNT(*) as total_orders')
            )
            ->whereYear('creation', now()->year)
            ->groupBy(DB::raw('MONTH(creation)'))
            ->orderBy(DB::raw('MONTH(creation)'))
            ->get();

        return $monthlyOrders = collect($monthlyOrders)->map(function ($q) use ($months){
            $key = $q->month - 1;
            $q->month = isset($months[$key]) ? $months[$key] : null;

            return $q;
        });
    }

    public function close_production_orders(){
        $erp_conn = 'mysql';
        $mes_conn = 'mysql_mes';
        DB::connection($erp_conn)->beginTransaction();
        DB::connection($mes_conn)->beginTransaction();
        try {
            $now = Carbon::now();

            $production_orders = [];
            $unable_to_close = [];
            $reason = 'Sales Order is Already Completed.';

            foreach ($production_orders as $production_order) {
                // check for task in progress
                $task_in_progress = DB::connection($mes_conn)->table('job_ticket')
                    ->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
                    ->where('production_order', $production_order)
                    ->where('time_logs.status', 'In Progress')->count();

                if ($task_in_progress > 0) {
                    // return response()->json(['success' => 0, 'message' => 'Cannot close production order with on-going task by operator. ' . $production_order]);
                    $unable_to_close[] = [
                        'production_order' => $production_order,
                        'reason' => 'Cannot close production order with on-going task by operator.'
                    ];
                }

                $stock_entries = DB::connection($erp_conn)->table('tabStock Entry')->where('work_order', $production_order)->where('docstatus', 0)->orWhere('item_status', 'For Checking')->where('work_order', $production_order)->get();

                if ($stock_entries) {
                    $draft_stes = collect($stock_entries)->pluck('name');

                    DB::connection($erp_conn)->table('tabStock Entry')->whereIn('name', $draft_stes)->delete();
                    DB::connection($erp_conn)->table('tabStock Entry Detail')->whereIn('parent', $draft_stes)->delete();
                }

                DB::connection($mes_conn)->table('production_order')
                    ->where('production_order', $production_order)
                    ->update([
                        'status' => 'Closed',
                        'last_modified_at' => $now->toDateTimeString(),
                        'last_modified_by' => Auth::user()->email,
                        'remarks' => $reason
                    ]);

                DB::connection($mes_conn)->table('activity_logs')->insert([
                    'action' => 'Production Order Closed',
                    'message' => 'Production Order ' . $production_order . ' has been closed by ' . Auth::user()->employee_name . ' at ' . Carbon::now()->toDateTimeString() . '<br>Reason: ' . $reason,
                    'reference' => $production_order,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'created_by' => Auth::user()->email
                ]);
            }

            DB::connection($erp_conn)->commit();
            DB::connection($mes_conn)->commit();
            if($unable_to_close){
                return $unable_to_close;
            }
            return response()->json(['success' => 1, 'message' => 'Production Orders has been closed.']);
        } catch (\Exception $e) {
            DB::connection($erp_conn)->rollback();
            DB::connection($mes_conn)->rollback();
            return response()->json(['success' => 0, 'message' => 'There was a problem creating transaction.']);
        }
    }

    public function notification(Request $request){
        return view('for_testing_only.notification_testing');
    }

    public function send_notification(Request $request){
        $client = new Client();
        $response = $client->post('http://10.0.49.27:3001/trigger-event', [
            'json' => [
                'message' => $request->message,
            ],
        ]);

        return response()->json(['status' => 'Event sent']);
    }

    public function so_webhook(Request $request){
        $client = new Client();
        $response = $client->post('http://10.0.49.27:3001/so-webhook', [
            'json' => [
                'name' => $request->name,
            ],
        ]);

        return response()->json(['status' => 'Event sent']);
    }

    public function get_assembly_floating_stocks(){
        $erp_db = 'live_erp';

        $assembly_warehouse = 'Assembly Warehouse - FI';
        $assembly_stocks = DB::connection($erp_db)->table('tabBin as p')
            ->join('tabItem as c', 'c.name', 'p.item_code')
            ->where('p.warehouse', $assembly_warehouse)
            ->select('p.item_code', 'p.description', 'p.item_image_path', 'p.stock_uom', 'p.actual_qty')
            // ->limit(100)
            ->get();

        $item_codes = collect($assembly_stocks)->pluck('item_code');

        $issued_from_assembly = DB::connection($erp_db)->table('tabStock Entry as p')
            ->join('tabStock Entry Detail as c', 'c.parent', 'p.name')
            ->where('p.from_warehouse', $assembly_warehouse)->where('item_status', 'Issued')
            ->whereIn('c.item_code', $item_codes)
            ->select('c.item_code', 'c.uom', DB::raw('SUM(c.qty) as total_qty'))
            ->groupBy('item_code', 'uom')
            ->get()->groupBy('item_code');

        $report = collect($assembly_stocks)->map(function ($q) use ($issued_from_assembly){
            $issued = isset($issued_from_assembly[$q->item_code]) ? $issued_from_assembly[$q->item_code][0] : [];
            if($issued){
                $floating_stocks = $q->actual_qty - $issued->total_qty;
                $floating_stocks = $floating_stocks > 0 ? $floating_stocks : 0;
                if($floating_stocks){
                    $q->withdrawn = $issued->total_qty;
                    $q->floating_stocks = $floating_stocks;
                    return $q;
                }
            }
            return null;
        })->sortByDesc('floating_stocks')->filter()->values()->all();
    }
}
