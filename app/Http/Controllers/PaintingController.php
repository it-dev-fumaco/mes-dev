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
use App\Exports\ExportDataChemicalMonitoringExcel;
use App\Exports\ExportDataWaterDischargeExcel;
use Maatwebsite\Excel\Facades\Excel;

class PaintingController extends Controller
{
	public function mainDashboard(){
		$user_details = DB::connection('mysql_essex')->table('users')
                    ->join('designation', 'users.designation_id', '=', 'designation.des_id')
                    ->join('departments', 'users.department_id', '=', 'departments.department_id')
                    ->where('user_id', Auth::user()->user_id)->first();

		$timesheet = DB::connection('mysql_mes')->table('job_ticket')->select('job_ticket_id')->get();


		return view('painting.painting_dashboard', compact('timesheet', 'user_details'));
	}

    public function itemFeedback(){
        // manual create production form
        $item_list = DB::table('tabItem')->where('is_stock_item', 1)->where('disabled', 0)
            ->where('has_variants', 0)->whereIn('item_classification', ['SA - Sub Assembly', 'HO - Housing'])
            ->select('name', 'description')->orderBy('modified', 'desc')->get();

        $parent_code_list = DB::table('tabItem')->where('is_stock_item', 1)->where('disabled', 0)
            ->where('has_variants', 0)->whereNotIn('item_classification', ['SA - Sub Assembly', 'HO - Housing'])
            ->select('name', 'description')->orderBy('modified', 'desc')->get();

        $warehouse_list = DB::table('tabWarehouse')->where('disabled', 0)->where('is_group', 0)
            ->where('company', 'FUMACO Inc.')->where('department', 'Fabrication')->pluck('name');

        $so_list = DB::table('tabSales Order')->where('docstatus', 1)->where('per_delivered', '<', 100)
                ->where('company', 'FUMACO Inc.')->orderBy('modified', 'desc')->get();

        $mreq_list = DB::table('tabMaterial Request')->where('docstatus', 1)->where('material_request_type', 'Manufacture')
                ->where('company', 'FUMACO Inc.')->where('per_ordered', '<', 100)->orderBy('modified', 'desc')->get();

        return view('painting.painting_production_orders', compact('item_list', 'warehouse_list', 'so_list', 'mreq_list', 'parent_code_list'));
    }

    public function get_open_production_orders(Request $request, $reference_type){
        $production_orders = DB::connection('mysql_mes')->table('production_order')
            ->join('job_ticket', 'job_ticket.production_order', 'production_order.production_order')
            ->where('job_ticket.workstation', 'Painting')
            ->where('production_order.status', '!=', 'Completed')
            ->when($reference_type == 'SO', function ($query) use ($reference_type) {
                return $query->whereNotNull('sales_order');
            })
            ->when($reference_type == 'MREQ', function ($query) use ($reference_type) {
                return $query->whereNotNull('material_request');
            })
            ->where(function($q) use ($request) {
                $q->where('production_order.production_order', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('customer', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('item_code', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('bom_no', 'LIKE', '%'.$request->search_string.'%');
            })
            ->orderBy('production_order.production_order', 'desc')->get();

        $production_order_list = [];
        foreach ($production_orders as $row) {
            $production_order_list[] = [
                'id' => $row->production_order,
                'name' => $row->production_order,
                'is_scheduled' => $row->is_scheduled,
                'planned_start_date' => $row->planned_start_date,
                'sales_order_no' => $row->sales_order,
                'material_request' => $row->material_request,
                'production_item' => $row->item_code,
                'description' => $row->description,
                'bom' => $row->bom_no,
                'qty' => $row->qty_to_manufacture,
                'customer' => $row->customer,
                'delivery_date' => $row->delivery_date,
                'stock_uom' => $row->stock_uom,
                'status' => $row->status
            ];
        }

        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // Create a new Laravel collection from the array data
        $itemCollection = collect($production_order_list);
        // Define how many items we want to be visible in each page
        $perPage = 10;
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
        // set url path for generted links
        $paginatedItems->setPath($request->url());

        $production_order_list = $paginatedItems;

        return view('reports.tbl_open_production_order', compact('production_order_list', 'reference_type'));
    }

    public function get_for_feedback_production(Request $request){
        try {
            $production_orders = DB::connection('mysql_mes')->table('production_order AS po')
                ->join('job_ticket', 'job_ticket.production_order', 'po.production_order')
                ->where('job_ticket.workstation', 'Painting')
                ->whereNotIn('po.status', ['Cancelled'])
                ->where(function($q) use ($request) {
                    $q->where('po.production_order', 'LIKE', '%'.$request->search_string.'%')
                        ->orWhere('customer', 'LIKE', '%'.$request->search_string.'%')
                        ->orWhere('item_code', 'LIKE', '%'.$request->search_string.'%')
                        ->orWhere('bom_no', 'LIKE', '%'.$request->search_string.'%');
                })
                ->where('po.produced_qty', '>', 0)->get();

            $production_order_list = [];
            foreach ($production_orders as $row) {
                $feedbacked_prod = DB::connection('mysql')->table('tabProduction Order')->where('name', $row->production_order)->first();

                $manufacture_entry = DB::connection('mysql')->table('tabStock Entry')
                    ->where('production_order', $row->production_order)->where('purpose', 'Manufacture')->first();

                if ($feedbacked_prod && $feedbacked_prod->status != 'Completed') {
                    $status = ($row->qty_to_manufacture == $row->produced_qty) ? 'For Feedback' : 'For Partial Feedback';
                    if ($manufacture_entry) {
                        $status = 'Feedbacked';
                    }
                    
                    $production_order_list[] = [
                        'name' => $row->production_order,
                        'sales_order_no' => $row->sales_order,
                        'material_request' => $row->material_request,
                        'customer' => $row->customer,
                        'item_code' => $row->item_code,
                        'description' => $row->description,
                        'qty' => $row->qty_to_manufacture,
                        'produced_qty' => $row->produced_qty,
                        'stock_uom' => $row->stock_uom,
                        'delivery_date' => $row->delivery_date,
                        'completed_qty' => 0,
                        'status' => $status,
                        'ste_manufacture' => ($manufacture_entry) ? $manufacture_entry->name : '',
                        'target_warehouse' => $row->fg_warehouse
                    ];
                }
            }
            // Get current page form url e.x. &page=1
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            // Create a new Laravel collection from the array data
            $itemCollection = collect($production_order_list);
            // Define how many items we want to be visible in each page
            $perPage = 10;
            // Slice the collection to get the items to display in current page
            $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
            // Create our paginator and pass it to the view
            $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
            // set url path for generted links
            $paginatedItems->setPath($request->url());

            $production_order_list = $paginatedItems;

            return view('reports.tbl_feedback_ready_production_order', compact('production_order_list'));
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function get_cancelled_production_orders(Request $request){
        $production_order_list = DB::connection('mysql_mes')->table('production_order')
            ->join('job_ticket', 'job_ticket.production_order', 'production_order.production_order')
            ->where('job_ticket.workstation', 'Painting')
            ->where('production_order.status', 'Cancelled')
            ->where(function($q) use ($request) {
               $q->where('production_order.production_order', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('customer', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('item_code', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('bom_no', 'LIKE', '%'.$request->search_string.'%');
            })
            ->orderBy('production_order.production_order', 'desc')->paginate(10);

        return view('reports.tbl_cancelled_production_order', compact('production_order_list'));
    }

	public function get_production_order_list($schedule_date){
        $orders = DB::connection('mysql_mes')->table('production_order as prod')
            ->join('job_ticket as tsd','tsd.production_order','=','prod.production_order')
            ->join('time_logs', 'time_logs.job_ticket_id', 'tsd.job_ticket_id')
            // ->join('quality_inspection as qa', 'qa.time_log_id', 'time_logs.time_log_id')
            ->join('process as p', 'p.process_id', 'tsd.process_id')
            ->whereNotIn('prod.status', ['Cancelled'])
            ->join('workstation as work','work.workstation_name','tsd.workstation')
            ->where('tsd.status', 'In Progress')->where('tsd.workstation', 'Painting')
            ->select('prod.production_order','prod.qty_to_manufacture','tsd.workstation as workstation_plot','time_logs.machine_code as machine','time_logs.job_ticket_id as jtname', 'p.process_name', "tsd.status as stat", 'tsd.item_feedback as item_feed', 'time_logs.operator_name', 'time_logs.from_time', 'time_logs.to_time', 'time_logs.machine_code', 'work.workstation_id', 'time_logs.time_log_id', 'tsd.job_ticket_id')
            ->get();
        
        $data = [];
        foreach($orders as $row){
            $qa_table = DB::connection('mysql_mes')->table('quality_inspection')
            ->where('time_log_id', $row->time_log_id)
            ->first();
            $data[]=[
                'workstation_plot'=> $row->workstation_plot,
                'machine' => $row->machine,
                'jtname' => $row->jtname,
                'process_name' => $row->process_name,
                'stat' => $row->stat,
                'item_feed' => $row->item_feed,
                'operator_name' => $row->operator_name,
                'from_time' => $row->from_time,
                'to_time' => $row->to_time,
                'time_log_id'=>$row->time_log_id,
                'qa_inspection_status' => ($qa_table == null) ? 'Pending': $qa_table->status,
                'qa_inspected_by' =>  ($qa_table == null) ? 'Pending': $qa_table->qa_staff_id,
                'qa_inspection_date' => ($qa_table == null) ? 'Pending': $qa_table->qa_inspection_date,
                'production_order' => $row->production_order,
                'job_ticket_id' => $row->job_ticket_id,
                'timelogs_id' => $row->time_log_id,
                'qty_accepted' => $row->qty_to_manufacture,
                'workstation_id' =>  $row->workstation_id
            ];
        }
        $current_date= $schedule_date;
        return view('tables.tbl_production_order_list_maindashboard', compact('data','current_date'));
 
    }

    public function count_current_production_order($schedule_date){
        $orders_rejects = DB::connection('mysql_mes')->table('production_order as prod')
            ->join('job_ticket as tsd','tsd.production_order','=','prod.production_order')
            ->join('time_logs as tl', 'tl.job_ticket_id', 'tsd.job_ticket_id')
            ->join('process as p', 'p.process_id', 'tsd.process_id')
            ->where('tsd.workstation', 'Painting')
            ->whereDate('tl.last_modified_at',$schedule_date)
            ->where('prod.status', 'Not Started')
            ->where('prod.is_scheduled' , 1)
            ->select("tsd.status","tl.reject")->get();

            $orders_inprogress = DB::connection('mysql_mes')->table('production_order as prod')
            ->join('job_ticket as tsd','tsd.production_order','=','prod.production_order')
            ->join('time_logs as tl', 'tl.job_ticket_id', 'tsd.job_ticket_id')
            ->join('process as p', 'p.process_id', 'tsd.process_id')
            ->whereNotIn('prod.status', ['Cancelled'])
            ->where('tsd.workstation', 'Painting')
            ->where('prod.is_scheduled' , 1)
            ->select("tsd.status","tl.reject")->get();


            $orders_completed = DB::connection('mysql_mes')->table('production_order as prod')
            ->whereDate('prod.last_modified_at',$schedule_date)
            ->where('prod.is_scheduled' , 1)->get();

            $orders_pending_po = DB::connection('mysql_mes')->table('production_order as prod')
            ->where('prod.is_scheduled' , 1)->get();

        $scheduled = [];
 
        $count_pending = collect($orders_pending_po)->where('status', 'Not Started')->count();
        $count_inprogress = collect($orders_inprogress)->where('status', 'In Progress')->count();
        $count_completed = collect($orders_completed)->where('status', 'Completed')->count();
        $count_reject = collect($orders_rejects)->where('reject','!=', '0')->sum('reject');
        $scheduled = [
                'pending' => $count_pending ,
                'inProgress' => $count_inprogress,
                'completed' => $count_completed,
                'reject' =>  $count_reject
            ];
        return $scheduled;

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

    public function get_tbl_notif_dashboard(){
		$now = Carbon::now();

		$machine_breakdown = DB::connection('mysql_mes')->table('machine as m')
			->join('machine_breakdown as mb', 'm.machine_code', 'mb.machine_id')
			->whereIn('m.status', ['Unavailable', 'On-going Maintenance'])
			->select('m.*', 'mb.category', 'mb.date_reported','mb.type')
			->get();
		

		$notifs = [];
		foreach ($machine_breakdown as $mb) {
			$converted= Carbon::parse($mb->date_reported)->format('Y-m-d');
			
        	$from_carbon = Carbon::parse($now);
            $to_carbon = Carbon::parse($mb->date_reported);

			$duration = $from_carbon->diffInSeconds($to_carbon);
			$converted_duration = $this->seconds2human($duration);
			
			$notifs[] = [
				'type' => 'Machine Breakdown',
				'message' => $mb->machine_name.'<br>Machine Request: ' . $mb->type.'<br> Date Reported:'.$converted.'<br>Duration: '.$converted_duration,
				'created' => $mb->date_reported,
				'timelog_id' =>	'',
				'table' => 'machine'
			];
		}

		$unassigned = Carbon::now()->subHour(8)->toDateTimeString();
		$accepted = Carbon::now()->subHour(2)->toDateTimeString();
		
		$dateMinusOneWeek = Carbon::now()->subWeek()->toDateTimeString();

		$get_timelog_reject= DB::connection('mysql_mes')->table('time_logs as tl')
		->join('job_ticket as jt', 'jt.job_ticket_id', 'tl.job_ticket_id')
		->join('process as p','p.process_id','jt.process_id')
		->where('tl.reject','>', 0)
		->where('jt.workstation', 'Painting')
		->where('tl.is_hide','=',0)
		->where('tl.created_at','>=', $dateMinusOneWeek)
		->select('tl.*', 'jt.workstation', 'p.process_name','jt.production_order')
		->get();

		

		foreach ($get_timelog_reject as $tmlogs) {
			$notifs[] = [	
				'type' => 'Reject',
				'message' => $tmlogs->machine_name.' ['.$tmlogs->machine_code.']'.'<br>'.$tmlogs->workstation.'-'.$tmlogs->process_name.'<br><b>'.$tmlogs->production_order.'</b> | QTY: <b>' . $tmlogs->reject.'</b>',
				'created' => $tmlogs->created_at,
				'timelog_id' =>	$tmlogs->time_log_id,
				'table' => 'fabrication'
			];
		}


		$notifications = collect($notifs)->SortByDesc('created');

    	return view('tables.tbl_notification_dashboard', compact('notifications'));
    }
    public function production_schedule_calendar_painting(){

        return view('production_schedule_calendar_painting');
    }
    public function get_production_schedule_calendar_painting(){
        
        $prod = DB::connection('mysql_mes')->table('job_ticket as jt')
        ->join('production_order as pro','pro.production_order','jt.production_order')
        ->leftJoin('delivery_date', function($join){
            $join->on( DB::raw('IFNULL(pro.sales_order, pro.material_request)'), '=', 'delivery_date.reference_no');
            $join->on('pro.parent_item_code','=','delivery_date.parent_item_code');
        })
        ->where('pro.status','!=', 'Cancelled')
        ->where('jt.workstation', 'Painting')
        ->where('jt.planned_start_date','!=', null)
        ->distinct( 'delivery_date.rescheduled_delivery_date','pro.customer', 'pro.sales_order', 'pro.material_request','pro.delivery_date', 'pro.production_order','pro.status','pro.item_code','pro.qty_to_manufacture','pro.description','pro.stock_uom')
        ->select( 'delivery_date.rescheduled_delivery_date','pro.customer', 'pro.sales_order', 'pro.material_request','pro.delivery_date', 'pro.production_order','pro.status','pro.item_code','pro.qty_to_manufacture','pro.description','pro.stock_uom')
        ->get();

        $data = array();
        foreach ($prod as $rows) {

            $guide_id = ($rows->sales_order == null) ? $rows->material_request : $rows->sales_order;
            $planned_start = DB::connection('mysql_mes')->table('job_ticket as jt')
            ->where('jt.production_order', $rows->production_order)
            ->where('workstation','Painting')
            ->select('jt.planned_start_date')
            ->first();
            $title = $rows->production_order . ' - ' . $rows->customer;
                    if ($rows->status == "Not Started" ) {
                        $stat= 'Not Started';
                        $color = '#b2babb';
                    }elseif($rows->status == "In Progress"){
                        $stat= 'In Progress';
                        $color = '#EB984E';
                    }else{
                        $stat= 'Completed';
                        $color = '#58d68d';
                    }

            $date = date('Y-m-d', strtotime($planned_start->planned_start_date));

            $data[] = array(
                'id'   => $rows->production_order,
                'title'   => $title,
                'start'   => $date,
                'end'   =>  $date,
                'color' => $color,
                'description' =>$rows->description,
                'status' => $rows->status,
                'uom' => $rows->stock_uom,
                'item_code' => $rows->item_code,
                'qty' => $rows->qty_to_manufacture,
                'customer' => $rows->customer,
                'sales_order'=> $guide_id,
                'delivery_date' => ($rows->rescheduled_delivery_date == null)? $rows->delivery_date: $rows->rescheduled_delivery_date, //show reschedule delivery date or the existing delivery date based on validation
                'production_order' => $rows->production_order
            );
        }
        // dd($data);
        return $data;
    }
    public function update_planned_start_date_painting(Request $request)
        {
            $scheduledTime = $request->input('scheduledtime');
            $prodid = $request->input('prodid');
            $val = [];
                $val = [
                    'planned_start_date' => $scheduledTime,
                    'last_modified_by' => Auth::user()->email
                ];
                     
                DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $prodid)->where('workstation','Painting')->update($val);  
        return response()->json(['success' => 1, 'message' => 'Stock Entry Successfully Inserted!']);

        }
    public function update_planned_start_date_by_click_painting(Request $request)
        {   
            $val = [];
                $val = [
                    'planned_start_date' => $request->start_time,
                    'last_modified_by' => Auth::user()->email
                ];

            DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->prod_id)->where('workstation','Painting')->update($val); 

            $get_data=$this->get_production_schedule_calendar_painting();
            return $get_data;
        }



        // J
        public function get_feedbacked_production_order(Request $request, $schedule_date){
            $orders = DB::connection('mysql_mes')->table('production_order as prod')
                ->join('job_ticket as tsd','tsd.production_order','=','prod.production_order')
                ->whereNotIn('prod.status', ['Cancelled'])
                ->join('workstation as work','work.workstation_name','tsd.workstation')
                ->where('tsd.planned_start_date', $schedule_date)
                ->where('tsd.workstation', 'Painting')
                ->where(function($q) use ($request) {
                    $q->where('prod.production_order', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('prod.item_code', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('prod.customer', 'LIKE', '%'.$request->search_string.'%');
                })
                ->where('prod.feedback_qty', '>', 0)
                ->distinct('prod.production_order', 'tsd.sequence')
                ->select('prod.*','tsd.sequence')
                ->orderBy('tsd.sequence','asc')
                ->get();
            
            $data = [];
            foreach($orders as $row){
               
    
                $data[]=[
                    'customer' => $row->customer,
                    'item_code' => $row->item_code,
                    'item_description'=> strtok($row->description, ","),
                    'stock_uom' => $row->stock_uom,
                    'qty'=> $row->qty_to_manufacture, 
                    'production_order' => $row->production_order,
                ];
            }
            
            return view('painting.tbl_feedbacked_po', compact('data'));

    }
    public function get_tbl_report_painting_chemical(){
        $chem_data= DB::connection('mysql_mes')->table('chemical_monitoring as chem')->orderBy('chem_monitoring_id','desc')->get();
        $data=[];
       foreach ($chem_data as $row) {
            $user = DB::connection('mysql_essex')->table('users')
            ->where('email', $row->created_by)->select('employee_name')
            ->first();

            $data[]=[
            'chem_date' => date('F d, Y', strtotime($row->date)),
            'degreasing_freealkali' => $row->degreasing_freealkali,
            'degre_add_status' => ($row->degreasing_increase_type == '')? $row->degreasing_status: "Add ".$row->degreasing_increase_type,
            'degrasing_point' => ($row->degrasing_point == null)? '-':$row->degrasing_point,
            'phospating_acid' => $row->phospating_acid,
            'phospating_increase_type' => ($row->phospating_increase_type == '')? $row->phospating_acid_status: "Add ".$row->phospating_increase_type,
            'phospating_acid_point' => ($row->phospating_acid_point == null)? '-':$row->phospating_acid_point,
            'phospating_accelerator' => $row->phospating_accelerator,
            'accelerator_increase_type' => ($row->accelerator_increase_type == '')? $row->phospating_accelerator_status: "Add ".$row->accelerator_increase_type,
            'accelerator_increase_point' => ($row->accelerator_increase_point == null)? '-':$row->accelerator_increase_point,
            'operator_name' => ($user == null)? "": $user->employee_name

            ];
        }
        // dd($data);
        
         return view('painting.tbl_report_chemical_records', compact('data'));
    }
    public function get_tbl_report_painting_chemical_filter($fromdate,$todate, $free,$replen,$acce){
        // $data= "07/01/2020 - 07/03/2020";

        // $to_date = substr($daterange, strpos($daterange, "-") + 9);
        // $from_date = strtok($daterange, "- ");
        // $arr = explode('-',trim($daterange));
        // dd($arr[1]);

        // $start = date('Y-m-d', strtotime($from_date));
        // $end = date('Y-m-d', strtotime($to_date));

        $get_all_operator=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
        ->whereBetween('date', [$fromdate, $todate])
        ->orderBy('chem_monitoring_id','desc')
        ->get();
        // dd($get_all_operator);

        if($free=="All" && $replen =="All" && $acce =="All"){
            $query= collect($get_all_operator);

        }elseif($free !="All" && $replen=="All" && $acce =="All"){
            if ($free == "range") {
                $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                ->whereBetween('date', [$fromdate, $todate])->whereBetween('degreasing_freealkali', ["6.5","7.5"])
                ->orderBy('chem_monitoring_id','desc')
                ->get();
            }else{
                if ($free =="<") {
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "6.5");
                }else{
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "7.5");
                }
            }

        }elseif($free =="All" && $replen !="All" && $acce =="All"){
            if ($replen == "range") {
                $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                ->whereBetween('date', [$fromdate, $todate])->whereBetween('phospating_acid', ["16","20"])
                ->orderBy('chem_monitoring_id','desc')
                ->get();
            }else{
                if ($replen =="<") {
                    $query= collect($get_all_operator)->where('phospating_acid', $replen, "16");
                }else{
                    $query= collect($get_all_operator)->where('phospating_acid', $replen, "20");
                }
            }

        }elseif($free =="All" && $replen=="All" && $acce !="All"){
            if ($acce == "range") {
                $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                ->whereBetween('date', [$fromdate, $todate])->whereBetween('phospating_accelerator', ["6","9"])
                ->orderBy('chem_monitoring_id','desc')
                ->get();
            }else{
                if ($acce =="<") {
                    $query= collect($get_all_operator)->where('phospating_accelerator', $acce, "6");
                }else{
                    $query= collect($get_all_operator)->where('phospating_accelerator', $acce, "9");
                }
            }

        }elseif($free =="All" && $replen !="All" && $acce !="All"){
            if ($acce == "range" && $replen == "range") {
                $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                ->whereBetween('date', [$fromdate, $todate])->whereBetween('phospating_accelerator', ["6","9"])->whereBetween('phospating_acid', ["16","20"])
                ->orderBy('chem_monitoring_id','desc')
                ->get();
            }else{
                if ($replen =="<" && $acce =="<") {
                    $query= collect($get_all_operator)->where('phospating_accelerator', $acce, "6")->where('phospating_acid', $replen, "16");
                }elseif($replen ==">" && $acce ="range"){
                    $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                    ->whereBetween('date', [$fromdate, $todate])->where('phospating_acid', $replen, "16")->whereBetween('phospating_accelerator', ["6","9"])
                    ->orderBy('chem_monitoring_id','desc')
                    ->get();
                }elseif($replen =="<" && $acce ="range"){
                    $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                    ->whereBetween('date', [$fromdate, $todate])->where('phospating_acid', $replen, "20")->whereBetween('phospating_accelerator', ["16","20"])
                    ->orderBy('chem_monitoring_id','desc')
                    ->get();
                }elseif($replen =="range" && $acce ="<"){
                    $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                    ->whereBetween('date', [$fromdate, $todate])->where('phospating_accelerator', $acce, "6")->whereBetween('phospating_acid', ["16","20"])
                    ->orderBy('chem_monitoring_id','desc')
                    ->get();
                }elseif($replen =="range" && $acce =">"){
                    $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                    ->whereBetween('date', [$fromdate, $todate])->where('phospating_accelerator', $acce, "9")->whereBetween('phospating_acid', ["16","20"])
                    ->orderBy('chem_monitoring_id','desc')
                    ->get();
                }elseif($replen ==">" && $acce ==">"){
                    $query= collect($get_all_operator)->where('phospating_accelerator', $acce, "9")->where('phospating_acid', $replen, "20");
                }elseif($replen =="<" && $acce ==">"){
                    
                    $query= collect($get_all_operator)->where('phospating_accelerator', $acce, "6")->where('phospating_acid', $replen, "20");
                }else{
                    $query= collect($get_all_operator)->where('phospating_accelerator', $acce, "9")->where('phospating_acid', $replen, "16");
                }
            }

        }elseif($free !="All" && $replen !="All" && $acce =="All"){
            if ($free == "range" && $replen == "range") {
                $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                ->whereBetween('date', [$fromdate, $todate])->whereBetween('degreasing_freealkali', ["6.5","7.5"])->whereBetween('phospating_acid', ["16","20"])
                ->orderBy('chem_monitoring_id','desc')
                ->get();
            }else{
                if ($free =="<" && $replen =="<") {
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "6.5")->where('phospating_acid', $replen, "16");
                }elseif($free ==">" && $replen ="range"){
                    $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                    ->whereBetween('date', [$fromdate, $todate])->where('degreasing_freealkali', $free, "7.5")->whereBetween('phospating_acid', ["16","20"])
                    ->orderBy('chem_monitoring_id','desc')
                    ->get();
                }elseif($free =="<" && $replen ="range"){
                    $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                    ->whereBetween('date', [$fromdate, $todate])->where('degreasing_freealkali', $free, "7.5")->whereBetween('phospating_acid', ["16","20"])
                    ->orderBy('chem_monitoring_id','desc')
                    ->get();
                }elseif($free =="range" && $replen ="<"){
                    $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                    ->whereBetween('date', [$fromdate, $todate])->where('phospating_acid', $replen, "16")->whereBetween('degreasing_freealkali', ["6.5","7.5"])
                    ->orderBy('chem_monitoring_id','desc')
                    ->get();
                }elseif($free =="range" && $replen =">"){
                    $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                    ->whereBetween('date', [$fromdate, $todate])->where('phospating_acid', $replen, "20")->whereBetween('degreasing_freealkali', ["6.5","7.5"])
                    ->orderBy('chem_monitoring_id','desc')
                    ->get();
                }elseif($free ==">" && $replen ==">"){
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "7.5")->where('phospating_acid', $replen, "20");
                }elseif($free =="<" && $replen ==">"){
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "6.5")->where('phospating_acid', $replen, "20");
                }else{
                     $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "7.5")->where('phospating_acid', $replen, "16");
                }
            }

        }elseif($free !="All" && $replen=="All" && $acce !="All"){
            if ($acce == "range" && $acce == "range") {
                $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                ->whereBetween('date', [$fromdate, $todate])->whereBetween('degreasing_freealkali', ["6.5","7.5"])->whereBetween('phospating_accelerator', ["6","9"])
                ->orderBy('chem_monitoring_id','desc')
                ->get();
            }else{
                if ($free =="<" && $acce =="<") {
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "6.5")->where('phospating_accelerator', $acce, "6");
                
                }elseif($free ==">" && $acce ="range"){
                    $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                    ->whereBetween('date', [$fromdate, $todate])->where('degreasing_freealkali', $free, "7.5")->whereBetween('phospating_accelerator', ["6","9"])
                    ->orderBy('chem_monitoring_id','desc')
                    ->get();
                }elseif($free =="<" && $acce ="range"){
                    $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                    ->whereBetween('date', [$fromdate, $todate])->where('degreasing_freealkali', $free, "7.5")->whereBetween('phospating_accelerator', ["6","9"])
                    ->orderBy('chem_monitoring_id','desc')
                    ->get();
                }elseif($free =="range" && $acce ="<"){
                    $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                    ->whereBetween('date', [$fromdate, $todate])->where('phospating_accelerator', $acce, "6")->whereBetween('degreasing_freealkali', ["6.5","7.5"])
                    ->orderBy('chem_monitoring_id','desc')
                    ->get();
                }elseif($free =="range" && $acce =">"){
                    $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                    ->whereBetween('date', [$fromdate, $todate])->where('phospating_accelerator', $acce, "9")->whereBetween('degreasing_freealkali', ["6.5","7.5"])
                    ->orderBy('chem_monitoring_id','desc')
                    ->get();
                }elseif($free ==">" && $acce ==">"){
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "7.5")->where('phospating_accelerator', $acce, "9");
                }elseif($free =="<" && $acce ==">"){
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "6.5")->where('phospating_accelerator', $acce, "9");
                }else{
                     $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "7.5")->where('phospating_accelerator', $acce, "6");
                }
            }
        }else{
            if ($free =="range" && $acce == "range" && $replen == "range") {
                $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                ->whereBetween('date', [$fromdate, $todate])->whereBetween('degreasing_freealkali', ["6.5","7.5"])
                ->whereBetween('phospating_acid', ["16","20"])
                ->whereBetween('phospating_accelerator', ["6","9"])
                ->orderBy('chem_monitoring_id','desc')
                ->get();
               
            }else{
                if ($free =="<" && $replen =="<" && $acce =="<") {
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "6.5")
                    ->where('phospating_acid', $replen, "16")
                    ->where('phospating_accelerator', $acce, "6");
                
                }elseif($free ==">" && $replen ==">" && $acce ==">"){
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "7.5")
                    ->where('phospating_acid', $replen, "20")
                    ->where('phospating_accelerator', $acce, "9");
                }elseif($free =="<" && $replen ==">" && $acce ==">"){
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "6.5")
                    ->where('phospating_acid', $replen, "20")
                    ->where('phospating_accelerator', $acce, "9");
                }elseif($free ==">" && $replen =="<" && $acce =="<"){
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "7.5")
                    ->where('phospating_acid', $replen, "16")
                    ->where('phospating_accelerator', $acce, "9");
                }else{
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "7.5")
                    ->where('phospating_acid', $replen, "20")
                    ->where('phospating_accelerator', $acce, "6");
                }
            }
        }
        // dd($query);

        $data=[];
        foreach ($query as $row) {
            $user = DB::connection('mysql_essex')->table('users')
            ->where('email', $row->created_by)->select('employee_name')
            ->first();

            $data[]=[
            'chem_date' => date('F d, Y', strtotime($row->date)),
            'degreasing_freealkali' => $row->degreasing_freealkali,
            'degre_add_status' => ($row->degreasing_increase_type == '')? $row->degreasing_status: "Add ".$row->degreasing_increase_type,
            'degrasing_point' => ($row->degrasing_point == null)? '-':$row->degrasing_point,
            'phospating_acid' => $row->phospating_acid,
            'phospating_increase_type' => ($row->phospating_increase_type == '')? $row->phospating_acid_status: "Add ".$row->phospating_increase_type,
            'phospating_acid_point' => ($row->phospating_acid_point == null)? '-':$row->phospating_acid_point,
            'phospating_accelerator' => $row->phospating_accelerator,
            'accelerator_increase_type' => ($row->accelerator_increase_type == '')? $row->phospating_accelerator_status: "Add ".$row->accelerator_increase_type,
            'accelerator_increase_point' => ($row->accelerator_increase_point == null)? '-':$row->accelerator_increase_point,
            'operator_name' => ($user == null)? "": $user->employee_name

            ];
        }
         return view('painting.tbl_report_chemical_records', compact('data'));
    }
    public function get_tbl_report_painting_chemical_export($fromdate,$todate, $free,$replen,$acce){
        $get_all_operator=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
        ->whereBetween('date', [$fromdate, $todate])
        ->orderBy('chem_monitoring_id','desc')
        ->get();

        if($free=="All" && $replen =="All" && $acce =="All"){
            $query= collect($get_all_operator);

        }elseif($free !="All" && $replen=="All" && $acce =="All"){
            if ($free == "range") {
                $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                ->whereBetween('date', [$fromdate, $todate])->whereBetween('degreasing_freealkali', ["6.5","7.5"])
                ->orderBy('chem_monitoring_id','desc')
                ->get();
            }else{
                if ($free =="<") {
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "6.5");
                }else{
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "7.5");
                }
            }

        }elseif($free =="All" && $replen !="All" && $acce =="All"){
            if ($replen == "range") {
                $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                ->whereBetween('date', [$fromdate, $todate])->whereBetween('phospating_acid', ["16","20"])
                ->orderBy('chem_monitoring_id','desc')
                ->get();
            }else{
                if ($replen =="<") {
                    $query= collect($get_all_operator)->where('phospating_acid', $replen, "16");
                }else{
                    $query= collect($get_all_operator)->where('phospating_acid', $replen, "20");
                }
            }

        }elseif($free =="All" && $replen=="All" && $acce !="All"){
            if ($acce == "range") {
                $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                ->whereBetween('date', [$fromdate, $todate])->whereBetween('phospating_accelerator', ["6","9"])
                ->orderBy('chem_monitoring_id','desc')
                ->get();
            }else{
                if ($acce =="<") {
                    $query= collect($get_all_operator)->where('phospating_accelerator', $acce, "6");
                }else{
                    $query= collect($get_all_operator)->where('phospating_accelerator', $acce, "9");
                }
            }

        }elseif($free =="All" && $replen !="All" && $acce !="All"){
            if ($acce == "range" && $replen == "range") {
                $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                ->whereBetween('date', [$fromdate, $todate])->whereBetween('phospating_accelerator', ["6","9"])->whereBetween('phospating_acid', ["16","20"])
                ->orderBy('chem_monitoring_id','desc')
                ->get();
            }else{
                if ($replen =="<" && $acce =="<") {
                    $query= collect($get_all_operator)->where('phospating_accelerator', $acce, "6")->where('phospating_acid', $replen, "16");
                }elseif($replen ==">" && $acce ==">"){
                    $query= collect($get_all_operator)->where('phospating_accelerator', $acce, "9")->where('phospating_acid', $replen, "20");
                }elseif($replen =="<" && $acce ==">"){
                    
                    $query= collect($get_all_operator)->where('phospating_accelerator', $acce, "6")->where('phospating_acid', $replen, "20");
                }else{
                    $query= collect($get_all_operator)->where('phospating_accelerator', $acce, "9")->where('phospating_acid', $replen, "16");
                }
            }

        }elseif($free !="All" && $replen !="All" && $acce =="All"){
            if ($free == "range" && $replen == "range") {
                $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                ->whereBetween('date', [$fromdate, $todate])->whereBetween('degreasing_freealkali', ["6.5","7.5"])->whereBetween('phospating_acid', ["16","20"])
                ->orderBy('chem_monitoring_id','desc')
                ->get();
            }elseif($free == ">" && $replen == "range") {
                $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                ->whereBetween('date', [$fromdate, $todate])->where('degreasing_freealkali', $free, "7.5")->whereBetween('phospating_acid', ["16","20"])
                ->orderBy('chem_monitoring_id','desc')
                ->get();
            }else{
                if ($free =="<" && $replen =="<") {
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "6.5")->where('phospating_acid', $replen, "16");
                }elseif($free ==">" && $replen ==">"){
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "7.5")->where('phospating_acid', $replen, "20");
                }elseif($free =="<" && $replen ==">"){
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "6.5")->where('phospating_acid', $replen, "20");
                }elseif($free ==">" && $replen =="<"){
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "6.5")->where('phospating_acid', $replen, "20");
                }else{
                     $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "7.5")->where('phospating_acid', $replen, "16");
                }
            }

        }elseif($free !="All" && $replen=="All" && $acce !="All"){
            if ($acce == "range" && $acce == "range") {
                $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                ->whereBetween('date', [$fromdate, $todate])->whereBetween('degreasing_freealkali', ["6.5","7.5"])->whereBetween('phospating_accelerator', ["6","9"])
                ->orderBy('chem_monitoring_id','desc')
                ->get();
            }else{
                if ($free =="<" && $acce =="<") {
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "6.5")->where('phospating_accelerator', $acce, "6");
                }elseif($free ==">" && $acce ==">"){
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "7.5")->where('phospating_accelerator', $acce, "9");
                }elseif($free =="<" && $acce ==">"){
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "6.5")->where('phospating_accelerator', $acce, "9");
                }else{
                     $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "7.5")->where('phospating_accelerator', $acce, "6");
                }
            }
        }else{
            if ($free =="range" && $acce == "range" && $replen == "range") {
                $query=  DB::connection('mysql_mes')->table('chemical_monitoring as chem')
                ->whereBetween('date', [$fromdate, $todate])->whereBetween('degreasing_freealkali', ["6.5","7.5"])
                ->whereBetween('phospating_acid', ["16","20"])
                ->whereBetween('phospating_accelerator', ["6","9"])
                ->orderBy('chem_monitoring_id','desc')
                ->get();
               
            }else{
                if ($free =="<" && $replen =="<" && $acce =="<") {
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "6.5")
                    ->where('phospating_acid', $replen, "16")
                    ->where('phospating_accelerator', $acce, "6");
                }elseif($free ==">" && $replen ==">" && $acce ==">"){
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "7.5")
                    ->where('phospating_acid', $replen, "20")
                    ->where('phospating_accelerator', $acce, "9");
                }elseif($free =="<" && $replen ==">" && $acce ==">"){
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "6.5")
                    ->where('phospating_acid', $replen, "20")
                    ->where('phospating_accelerator', $acce, "9");
                }elseif($free ==">" && $replen =="<" && $acce =="<"){
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "7.5")
                    ->where('phospating_acid', $replen, "16")
                    ->where('phospating_accelerator', $acce, "9");
                }else{
                    $query= collect($get_all_operator)->where('degreasing_freealkali', $free, "7.5")
                    ->where('phospating_acid', $replen, "20")
                    ->where('phospating_accelerator', $acce, "6");
                }
            }
        }
        // dd($query);

        $data=[];
        foreach ($query as $row) {
            $user = DB::connection('mysql_essex')->table('users')
            ->where('email', $row->created_by)->select('employee_name')
            ->first();

            $data[]=[
            'chem_date' => date('F d, Y', strtotime($row->date)),
            'degreasing_freealkali' => $row->degreasing_freealkali,
            'degre_add_status' => ($row->degreasing_increase_type == '')? $row->degreasing_status: "Add ".$row->degreasing_increase_type,
            'degrasing_point' => ($row->degrasing_point == null)? '-':$row->degrasing_point,
            'phospating_acid' => $row->phospating_acid,
            'phospating_increase_type' => ($row->phospating_increase_type == '')? $row->phospating_acid_status: "Add ".$row->phospating_increase_type,
            'phospating_acid_point' => ($row->phospating_acid_point == null)? '-':$row->phospating_acid_point,
            'phospating_accelerator' => $row->phospating_accelerator,
            'accelerator_increase_type' => ($row->accelerator_increase_type == '')? $row->phospating_accelerator_status: "Add ".$row->accelerator_increase_type,
            'accelerator_increase_point' => ($row->accelerator_increase_point == null)? '-':$row->accelerator_increase_point,
            'operator_name' => ($user == null)? "": $user->employee_name

            ];
        }
         return Excel::download(new ExportDataChemicalMonitoringExcel($data), "ChemicalMonitoringList.xlsx");
    }
    public function get_tbl_water_discharged(){
        
        $data=[];
        // $year = 2020;
        // $month = 7;
        // $date_1 = Carbon::create($year, $month)->startOfMonth()->format('Y-m-d'); //returns 2020-03-01
        // $date_2 = Carbon::create($year, $month)->lastOfMonth()->format('Y-m-d'); //returns 2020-03-31
        // $period = CarbonPeriod::create($date_1, $date_2);
        // foreach ($period as $date ){
        //     $day= $date->format('Y-m-d');
            $water_data= DB::connection('mysql_mes')->table('water_discharged_monitoring as water')->orderBy('water_discharged_motoring_id','desc')->get();
             // $water_data= DB::connection('mysql_mes')->table('water_discharged_monitoring as water')->whereDate("date", $day)->first();

             foreach ($water_data as $row) {
              
                $user = DB::connection('mysql_essex')->table('users')
                    ->where('user_id', $row->operator_id)->select('employee_name')
                    ->first();

                    $data[]=[
                    'water_date' =>  date('F d, Y', strtotime($row->date)),
                    'operating_hrs' => $row->operating_hrs,
                    'previous' => ($row->previous == '')? "0":$row->previous."&nbsp;<label style='font-size: 10px;color:black;'>cm<sup>3</sup></label>",
                    'present' => ($row->present == null)? '0':$row->present."&nbsp;<label style='font-size: 10px;color:black;'>cm<sup>3</sup></label>",
                    'incoming_water_discharged' => $row->incoming_water_discharged."&nbsp;<label style='font-size: 10px;color:black;'><b>cm<sup>3</sup></b></label>",
                    'operator_name' => ($user == null)? "": $user->employee_name

                    ];

            }
                    
                
        // }  
        return view('painting.tbl_report_painting_water_discharged', compact('data'));
    }
    public function get_tbl_report_painting_water_discharge_filter($date_from, $date_to, $hrs){
        
        $data=[];
        // $period = CarbonPeriod::create($date_from, $date_to);
        // foreach ($period as $date ){
        //     $day= $date->format('Y-m-d');
            if ($hrs == "All") {
               $water_data= DB::connection('mysql_mes')->table('water_discharged_monitoring')->whereBetween("date", [$date_from, $date_to])->get();
            }elseif ($hrs == ">") {
                $water_data= DB::connection('mysql_mes')->table('water_discharged_monitoring')->whereBetween("date", [$date_from, $date_to])->where('operating_hrs', '>', '8')->get();
            }elseif ($hrs == "<") {
                $water_data= DB::connection('mysql_mes')->table('water_discharged_monitoring')->whereBetween("date", [$date_from, $date_to])->where('operating_hrs', '<', '8')->get();
            }else{
                $water_data= DB::connection('mysql_mes')->table('water_discharged_monitoring')->whereBetween("date", [$date_from, $date_to])->where('operating_hrs', '8')->get();
            }
            

            foreach ($water_data as $row) {
              
                $user = DB::connection('mysql_essex')->table('users')
                    ->where('user_id', $row->operator_id)->select('employee_name')
                    ->first();

                    $data[]=[
                    'water_date' =>  date('F d, Y', strtotime($row->date)),
                    'operating_hrs' => $row->operating_hrs,
                    'previous' => ($row->previous == '')? "0":$row->previous,
                    'present' => ($row->present == null)? '0':$row->present,
                    'incoming_water_discharged' => $row->incoming_water_discharged,
                    'operator_name' => ($user == null)? "": $user->employee_name

                    ];

            }
            
                
        return view('painting.tbl_report_painting_water_discharged', compact('data'));
    }
    public function get_tbl_report_painting_water_discharge_export($date_from, $date_to, $hrs){
        
        $data=[];
        // $period = CarbonPeriod::create($date_from, $date_to);
        // foreach ($period as $date ){
        //     $day= $date->format('Y-m-d');
            if ($hrs == "All") {
               $water_data= DB::connection('mysql_mes')->table('water_discharged_monitoring')->whereBetween("date", [$date_from, $date_to])->get();
            }elseif ($hrs == ">") {
                $water_data= DB::connection('mysql_mes')->table('water_discharged_monitoring')->whereBetween("date", [$date_from, $date_to])->where('operating_hrs', '>', '8')->get();
            }elseif ($hrs == "<") {
                $water_data= DB::connection('mysql_mes')->table('water_discharged_monitoring')->whereBetween("date", [$date_from, $date_to])->where('operating_hrs', '<', '8')->get();
            }else{
                $water_data= DB::connection('mysql_mes')->table('water_discharged_monitoring')->whereBetween("date", [$date_from, $date_to])->where('operating_hrs', '8')->get();
            }
            

            foreach ($water_data as $row) {
              
                $user = DB::connection('mysql_essex')->table('users')
                    ->where('user_id', $row->operator_id)->select('employee_name')
                    ->first();

                    $data[]=[
                    'water_date' =>  date('F d, Y', strtotime($row->date)),
                    'operating_hrs' => $row->operating_hrs,
                    'previous' => ($row->previous == '')? "0":$row->previous,
                    'present' => ($row->present == null)? '0':$row->present,
                    'incoming_water_discharged' => $row->incoming_water_discharged,
                    'operator_name' => ($user == null)? "": $user->employee_name

                    ];

            }
            
                
         return Excel::download(new ExportDataWaterDischargeExcel($data), "WaterDischarged.xlsx");
    }
}