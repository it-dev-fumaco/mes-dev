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


use App\Traits\GeneralTrait;

class TrackingController extends Controller
{
    use GeneralTrait;
    
    
    public function item_status_tracking_page(){

        return view('item_status_tracking');
    }
    public function get_item_status_tracking(Request $request){

        $production_orders = DB::connection('mysql_mes')->table('production_order AS po')
            ->whereNotIn('status', ['Cancelled'])
            ->where('parent_item_code', '!=', null)
            ->where(function($q) use ($request) {
                $q->Where('customer', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('sales_order', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('material_request', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('parent_item_code', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('project', 'LIKE', '%'.$request->search_string.'%');
            })
            ->select('sales_order', 'material_request', 'parent_item_code')
            ->groupBy('sales_order','material_request', 'parent_item_code')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if($request->get_total){
            return ['div' => '#item-tracking-total', 'total' => number_format($production_orders->total())];
        }

        $so_item_list = [];
        foreach ($production_orders as $row) {
            $guide_id = ($row->sales_order == null) ? $row->material_request : $row->sales_order;
            $so_item_list[] = [
                'guide_id' => $guide_id,
                'item' => $this->get_so_item_list($row->sales_order,$row->material_request, $row->parent_item_code)
            ];
        }
        // // $array = collect($production_order_list)->sortBy('item')->reverse()->toArray();
        // // dd($production_order_list);
        // // Get current page form url e.x. &page=1
        // $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // // Create a new Laravel collection from the array data
        // $itemCollection = collect($production_order_list);
        // // Define how many items we want to be visible in each page
        // $perPage = 10;
        // // Slice the collection to get the items to display in current page
        // $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        // // Create our paginator and pass it to the view
        // $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
        // // set url path for generted links
        // $paginatedItems->setPath($request->url());

        // $so_item_list = $paginatedItems;

        return view('tables.tbl_item_list_for_tracking', compact('so_item_list', 'production_orders'));
    }
    public function get_so_item_list($sales_order, $material_request){
        if ($sales_order != null) {
            $erp_item = DB::connection('mysql')->table('tabSales Order Item as sotbl')
                    ->join('tabSales Order as so', 'so.name', 'sotbl.parent')
                    ->join('tabItem as item', 'item.name', 'sotbl.item_code')
                    ->where('sotbl.parent', $sales_order)
                    ->where('item.item_group', '!=', 'Raw Material')
                    ->select('sotbl.parent as sales_order', 'sotbl.item_code as item_code', 'sotbl.description as description', 'so.customer as customer', 'so.delivery_date as delivery_date', 'so.project', 'sotbl.qty', 'so.creation', 'sotbl.rescheduled_delivery_date', 'sotbl.name')
                    ->distinct('sotbl.parent')
                    ->get();
        }else{
            $erp_item = DB::connection('mysql')->table('tabMaterial Request Item as sotbl')
                    ->join('tabMaterial Request as so', 'so.name', 'sotbl.parent')
                    ->join('tabItem as item', 'item.name', 'sotbl.item_code')
                    ->where('sotbl.parent', $material_request)
                    ->where('item.item_group', '!=', 'Raw Material')
                    ->select('sotbl.parent as sales_order', 'sotbl.item_code as item_code', 'sotbl.description as description', 'so.customer as customer', 'so.delivery_date as delivery_date', 'so.project', 'sotbl.qty', 'so.creation', 'sotbl.rescheduled_delivery_date', 'sotbl.name')
                    ->distinct('sotbl.parent')
                    ->get();
        }
        
                    $production_order_list = [];
                    foreach ($erp_item as $row) {
                       $production_order_list[] = [
                        'sales_order' => $row->sales_order,
                        'item_code' => $row->item_code,
                        'description' => $row->description,
                        'customer' => $row->customer,
                        'delivery_date' => ($row->rescheduled_delivery_date == null)?  $row->delivery_date :$row->rescheduled_delivery_date,
                        'qty' => $row->qty,
                        'project' => $row->project,
                        'creation' => $row->creation,
                        'erp_reference_no' => $row->name
                    ];
            }
        // dd($production_order_list);
            return $production_order_list;
                    

    }
    public function get_search_information_details(Request $request){
        try {
            $production_orders = DB::connection('mysql_mes')->table('production_order AS po')
                ->whereNotIn('status', ['Cancelled'])
                ->where('parent_item_code', '!=', null)
                ->where(function($q) use ($request) {
                    $q->Where('customer', 'LIKE', '%'.$request->search_string.'%')
                        ->orWhere('sales_order', 'LIKE', '%'.$request->search_string.'%')
                        ->orWhere('material_request', 'LIKE', '%'.$request->search_string.'%')
                        ->orWhere('parent_item_code', 'LIKE', '%'.$request->search_string.'%')
                        ->orWhere('project', 'LIKE', '%'.$request->search_string.'%');
                })
                ->select('sales_order', 'material_request', 'customer')
                ->groupBy('sales_order','material_request', 'customer')
                ->paginate(10);

            $so_item_list = [];
            foreach ($production_orders as $row) {
                $guide_id = ($row->sales_order == null) ? $row->material_request : $row->sales_order; 
                $function_function= $this->get_so_item_list($row->sales_order,$row->material_request);

                    $so_item_list[] = [
                        'guide_id' => $guide_id,
                        'item' => $this->get_so_item_list($row->sales_order,$row->material_request)
                    ];
                
            }
            // // dd($production_order_list);
            // $array = collect($production_order_list)->sortBy('created_at')->reverse()->toArray();
            // // Get current page form url e.x. &page=1
            // $currentPage = LengthAwarePaginator::resolveCurrentPage();
            // // Create a new Laravel collection from the array data
            // $itemCollection = collect($production_order_list);
            // // Define how many items we want to be visible in each page
            // $perPage = 10;
            // // Slice the collection to get the items to display in current page
            // $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
            // // Create our paginator and pass it to the view
            // $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
            // // set url path for generted links
            // $paginatedItems->setPath($request->url());
            // $so_item_list = $paginatedItems;
            return view('tables.tbl_item_list_for_tracking', compact('so_item_list', 'production_orders'));
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }
    public function get_bom_tracking(Request $request){
        $guide_id= $request->guideid;
        $itemcode= $request->itemcode;
        $delivery_date_tbl= DB::connection('mysql_mes')->table('delivery_date')->where('erp_reference_id', $request->erp_reference_id)->first();
        $change_code=["match" => "" ];
        if($delivery_date_tbl){
            if($delivery_date_tbl->parent_item_code == $request->itemcode){
                $change_code=[
                    "match" => "true"
                ];
            }else{
                $change_code=[ 
                    "match" => "false",
                    "original_item" => $delivery_date_tbl->parent_item_code,
                    'new_item' => $itemcode
                ];
                $itemcode= $delivery_date_tbl->parent_item_code;
            }
        }
        $boms = DB::connection('mysql')->table('tabBOM')->where('item', $itemcode)->where('docstatus', '<', 2)
            ->select('name', 'is_default', 'rf_drawing_no', 'item as item_code', 'description')
            ->orderBy('modified', 'desc')->get();

        $bom_first = DB::connection('mysql')->table('tabBOM')->where('item', $itemcode)->where('docstatus', '<', 2)
            ->select('name', 'is_default', 'rf_drawing_no', 'item as item_code', 'description', 'docstatus')
            ->orderBy('modified', 'desc')->first();

        $default_bom = collect($boms)->where('is_default', 1)->first();
        $draft_BOM= collect($boms)->where('is_default', 1)->where('docstatus', 0)->first();
        if (!$bom_first) {
			return response()->json(['success' => 0, 'message' => 'BOM not found.']);
		}
        if($bom_first->is_default == 1 && $bom_first->docstatus == 0){
            $bom_get= $bom_first->name;
            $bom=$bom_first;
        }else{
            $bom_get= $default_bom->name;
            $bom=$default_bom;
        }
        $production= DB::connection('mysql_mes')->table('production_order AS po')
            ->leftJoin('delivery_date', function($join){
                $join->on( DB::raw('IFNULL(po.sales_order, po.material_request)'), '=', 'delivery_date.reference_no');
                $join->on('po.parent_item_code','=','delivery_date.parent_item_code');
            }) // get delivery date from delivery_date table
            ->whereNotIn('po.status', ['Cancelled'])
            ->where(function($q) use ($guide_id) {
                    $q->Where('po.sales_order', $guide_id)
                        ->orWhere('po.material_request', $guide_id);
            })
            ->select('po.sales_order', 'po.material_request', 'po.customer', 'delivery_date.delivery_date', 'po.project','po.parent_item_code', 'delivery_date.rescheduled_delivery_date')
            ->groupBy('po.sales_order', 'po.material_request', 'po.customer', 'delivery_date.delivery_date', 'po.project','po.parent_item_code', 'delivery_date.rescheduled_delivery_date')
            ->first();
        $parent_productions= DB::connection('mysql_mes')->table('production_order AS po')
            ->whereNotIn('po.status', ['Cancelled'])
            ->where(function($q) use ($guide_id) {
                $q->Where('po.sales_order', $guide_id)
                    ->orWhere('po.material_request', $guide_id);
                })
            ->where('item_code', $itemcode)
            ->first(); 
            $materials= [];            
            if (!empty($parent_productions)) {
                $data1=[];
                $data2=[];
                $jt_details1 =  DB::connection('mysql_mes')->table('job_ticket')
                    ->where('production_order', $parent_productions->production_order)
                    ->select('job_ticket_id', 'workstation')->get();

                        if(count($jt_details1) == 0){
                            $production_order_no= null;
                            $planned_start_date=null;
                            $end_date= null;
                            $start_date= null;
                            $duration=null;
                            $status=null;
                            $qty_to_manufacture=null;
                            $produced_qty=null;
                            $jobtickets_details=[];
                            $parts_category=null;
                            $done=null;
                        }else{
                            
                            $start_date = $parent_productions->actual_start_date;
                            $end_date = $parent_productions->actual_end_date;
                            $from_carbon = Carbon::parse($start_date);
                            $to_carbon = Carbon::parse($end_date);

                            $duration = $from_carbon->diffInSeconds($to_carbon);

                            $jobtickets_details=DB::connection('mysql_mes')->table('job_ticket as jt')
                                    ->join('process as p','p.process_id', 'jt.process_id')
                                    ->where('jt.production_order', $parent_productions->production_order)
                                    ->where('jt.status', 'In Progress')
                                    ->select('p.process_name', 'jt.workstation')
                                    ->distinct('jt.workstation')
                                    ->get();
                         
                            $stat= $parent_productions->status;
                            $done=$parent_productions->produced_qty;
                        
                            $production_order_no= $parent_productions->production_order;
                            $planned_start_date= $parent_productions->planned_start_date;


                            $status=$stat;
                            $qty_to_manufacture=$parent_productions->qty_to_manufacture;
                            $produced_qty=$parent_productions->produced_qty;
                            $parts_category = $parent_productions->parts_category; 
                            

                        }
                    
                        
                    
                    }else{
                        $production_order_no= null;
                        $planned_start_date=null;
                        $end_date= null;
                        $start_date= null;
                        $duration=null;
                        $status=null;
                        $qty_to_manufacture=null;
                        $produced_qty=null;
                        $jobtickets_details=[];
                        $parts_category=null;
                        $done=null;
                    }
            $item_details = DB::connection('mysql')->table('tabItem')->where('name', $itemcode)->first();
            $item_description = ($item_details) ? $item_details->description : '';
            $available_stock = DB::connection('mysql_mes')->table('fabrication_inventory')->where('item_code', $itemcode)->sum('balance_qty');


            if (!empty($parent_productions)) {
                $materials = [
                        'item_code' => $parent_productions->item_code,
                        'description' => $item_description,
                        'item_name' => $item_details->item_name,
                        'qty' => $parent_productions->qty_to_manufacture,
                        'bom_no' => $parent_productions->bom_no,
                        'uom' => $parent_productions->stock_uom,
                        'parts_category' => $parts_category,
                        'production_order' => ($production_order_no == null) ? '': $production_order_no,
                        'planned_start_date' => ($planned_start_date == null) ? '': $planned_start_date,
                        'end_date' => ($end_date == null) ? '': Carbon::parse($end_date)->format('F d, Y h:ia'),
                        'start_date' => ($start_date == null) ? '': Carbon::parse($start_date)->format('F d, Y h:ia'),
                        'duration' => ($duration == null) ? '': $this->seconds2human($duration),
                        'status' => ($status == null) ? '': $status,
                        'qty_to_manufacture' => ($status == null) ? '': $qty_to_manufacture,
                        'produced_qty' => $done,
                        'bom_no'=> (empty($default_bom->name))? '':$default_bom->name,
                        'current_load' => $jobtickets_details,
                        'available_stock' => $available_stock,
                        'operation_id' => $parent_productions->operation_id,
                        'feedback_qty' => $parent_productions->feedback_qty,
                        'process' =>  $this->getTimesheetProcess(($production_order_no == null) ? '': $production_order_no)
                ];
            }else{
                $materials = [
                        'item_code' => $itemcode,
                        'description' => $item_description,
                        'item_name' => $item_details->item_name,
                        'qty' => "",
                        'bom_no' => "",
                        'uom' => "",
                        'parts_category' => $parts_category,
                        'production_order' => ($production_order_no == null) ? '': $production_order_no,
                        'planned_start_date' => ($planned_start_date == null) ? '': $planned_start_date,
                        'end_date' => ($end_date == null) ? '': Carbon::parse($end_date)->format('F d, Y h:ia'),
                        'start_date' => ($start_date == null) ? '': Carbon::parse($start_date)->format('F d, Y h:ia'),
                        'duration' => ($duration == null) ? '': $this->seconds2human($duration),
                        'status' => ($status == null) ? '': $status,
                        'qty_to_manufacture' => ($status == null) ? '': $qty_to_manufacture,
                        'produced_qty' => $done,
                        'bom_no'=> (empty($default_bom->name))? '':$default_bom->name,
                        'current_load' => $jobtickets_details,
                        'available_stock' => $available_stock,
                        'operation_id' => 1,
                        'feedback_qty' => 0,
                        'process'=> [],
                ];
            }
            $spotlogss=DB::connection('mysql_mes')->table('job_ticket as jt')
                ->join('production_order as po', 'po.production_order', 'jt.production_order')
                ->leftJoin('spotwelding_qty as spotpart', 'spotpart.job_ticket_id','jt.job_ticket_id')
                ->join('process as p','p.process_id','jt.process_id')
                ->where(function($q) use ($guide_id) {
                    $q->Where('po.sales_order', $guide_id)
                        ->orWhere('po.material_request', $guide_id);
                    })
                ->where('po.parent_item_code', $itemcode)
                ->orderBy('spotpart.last_modified_at', 'desc')
                ->select(DB::raw('(SELECT MIN(from_time) FROM spotwelding_qty WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS from_time'),DB::raw('(SELECT MAX(to_time) FROM spotwelding_qty WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS to_time'),'p.process_name','jt.production_order','jt.job_ticket_id','jt.workstation', 'po.operation_id');  

            $timelogss=DB::connection('mysql_mes')->table('job_ticket as jt')
                ->join('production_order as po', 'po.production_order', 'jt.production_order')
                ->leftJoin('time_logs as tl', 'jt.job_ticket_id','tl.job_ticket_id')
                ->join('process as p','p.process_id','jt.process_id')
                ->where(function($q) use ($guide_id) {
                    $q->Where('po.sales_order', $guide_id)
                        ->orWhere('po.material_request', $guide_id);
                    })
                ->where('po.parent_item_code', $itemcode)
                ->select(DB::raw('(SELECT MIN(from_time) FROM time_logs WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS from_time'),DB::raw('(SELECT MAX(to_time) FROM time_logs WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS to_time'),'p.process_name','jt.production_order','jt.job_ticket_id','jt.workstation', 'po.operation_id')
                ->unionAll($spotlogss)
                ->get();     
                                                             
            
            $status= db::connection('mysql_mes')->table('production_order')
            ->where('parent_item_code', $itemcode)
            ->where(function($q) use ($guide_id) {
                $q->Where('sales_order', $guide_id)
                    ->orWhere('material_request', $guide_id);
                })->select('status', 'operation_id','production_order')->get();             
            $plucked = collect($status)->pluck('production_order');
            $job_ticket_per_workstation= db::connection('mysql_mes')->table('job_ticket')->join('production_order as pro', 'pro.production_order', 'job_ticket.production_order')->where('pro.status', '!=', "Cancelled")->whereIn('job_ticket.production_order', $plucked)->select('pro.operation_id', 'job_ticket.*')->get();
            $fabrication= collect($job_ticket_per_workstation)->where('operation_id', '1')->where('workstation', '!=', 'Painting')->count();
            $fabrication_completed= collect($job_ticket_per_workstation)->where('operation_id', '1')->where('workstation', '!=', 'Painting')->where('status', "Completed")->count();
            $painting= collect($job_ticket_per_workstation)->where('operation_id', '1')->where('workstation', 'Painting')->count();
            $painting_completed=  collect($job_ticket_per_workstation)->where('operation_id', '1')->where('workstation', 'Painting')->where('status', "Completed")->count();
            $assembly= collect($job_ticket_per_workstation)->where('operation_id', '3')->count();
            $assembly_completed= collect($job_ticket_per_workstation)->where('operation_id', '3')->where('status', "Completed")->count();
            
            $min_fab=collect($timelogss)->where('operation_id', '1')->where('workstation', '!=', 'Painting')->min('from_time');
            $max_fab=collect($timelogss)->where('operation_id', '1')->where('workstation', '!=', 'Painting')->max('to_time');
            
            $min_pain=collect($timelogss)->where('operation_id', '1')->where('workstation', 'Painting')->min('from_time');
            $max_pain=collect($timelogss)->where('operation_id', '1')->where('workstation', 'Painting')->max('to_time');

            $min_assem=collect($timelogss)->where('operation_id', '3')->min('from_time');
            $max_assem=collect($timelogss)->where('operation_id', '3')->max('to_time');

            if((collect($job_ticket_per_workstation)->where('operation_id', '1')->where('workstation', '!=', 'Painting')->where('status', "Pending")->count()) == $fabrication){                
                $fab_timeline_stat = "not_started";
                $fab_duration="-";
            }elseif ($fabrication == $fabrication_completed ){ 
                $fab_timeline_stat = "Completed";
                $from = Carbon::parse($min_fab);
                $to = Carbon::parse($max_fab);

                $duration = $from->diffInSeconds($to);
                $fab_duration= ($duration == null) ? '': $this->seconds2human($duration);
            }else{
                 $fab_timeline_stat = "In Progress";
                 $fab_duration="- On Going";
            }
            if((collect($job_ticket_per_workstation)->where('operation_id', '3')->where('status', "Pending")->count()) == $assembly ){
                $assem_timeline_stat = "not_started";
                $assem_duration=" - ";

            }elseif ($assembly == $assembly_completed){
                $assem_timeline_stat = "Completed";
                $from = Carbon::parse($min_assem);
                $to = Carbon::parse($max_assem);

                $duration = $from->diffInSeconds($to);
                $assem_duration= ($duration == null) ? '': $this->seconds2human($duration);
            }else{
                 $assem_timeline_stat = "In Progress";
                 $assem_duration=" - On Going";

            }

            if((collect($job_ticket_per_workstation)->where('operation_id', '1')->where('workstation', 'Painting')->where('status', "Pending")->count()) == $painting){
                $pain_timeline_stat = "not_started";
                $pain_duration=" - ";

            }elseif ($painting == $painting_completed){
                $pain_timeline_stat = "Completed";
                $from = Carbon::parse($min_pain);
                $to = Carbon::parse($max_pain);

                $duration = $from->diffInSeconds($to);
                $pain_duration= ($duration == null) ? '': $this->seconds2human($duration);
            }else{
                 $pain_timeline_stat = "In Progress";
                 $pain_duration=" - On Going ";

            }
            if($assem_timeline_stat == "Completed" && $fab_timeline_stat == "Completed" && $pain_timeline_stat="Completed"){
                $from_carbon = Carbon::parse($min_fab);
                $to_carbon = Carbon::parse($max_assem);

                $duration = $from_carbon->diffInSeconds($to_carbon);
                $formated_duration= ($duration == null) ? '': $this->seconds2human($duration);
            }else{
                $formated_duration=" - ";
                
            }
            $total_qty_fab=0;
            $timeline=[
                'fab_min' => ($min_fab == null) ? '-': Carbon::parse($min_fab)->format('F d, Y h:ia'),
                'fab_max' => ($max_fab == null) ? '-': Carbon::parse($max_fab)->format('F d, Y h:ia'),
                'pain_min' => ($min_pain == null) ? '-': Carbon::parse($min_pain)->format('F d, Y h:ia'),
                'pain_max' => ($max_pain == null) ? '-': Carbon::parse($max_pain)->format('F d, Y h:ia'),
                'assem_min'=> ($min_assem == null) ? '-': Carbon::parse($min_assem)->format('F d, Y h:ia'),
                'assem_max' => ($max_assem == null) ? '-': Carbon::parse($max_assem)->format('F d, Y h:ia'),
                'fab_stat' => $fab_timeline_stat,
                'assem_stat' => $assem_timeline_stat,
                'pain_stat' => $pain_timeline_stat,
                'duration' => $formated_duration,
                'fab_duration' => $fab_duration,
                'pain_duration' => $pain_duration,
                'assem_duration' => $assem_duration,
                'fab_required' => empty($total_qty_fab) ? '0' : $total_qty_fab->qty_to_manufacture,
                'fab_produced' =>  empty($total_qty_fab) ? '0' : $total_qty_fab->produced_qty,
                'uom' =>  empty($total_qty_fab) ? '0' : $total_qty_fab->stock_uom

            ];
        if ($bom != null) {
            $boms = $this->get_bom($bom_get, $guide_id, $itemcode, $itemcode);
            $item_codes = $itemcode;
        }else{
            $boms = [];
            $item_codes="";
        }
        return view('tracking_flowchart', compact('boms', 'item_codes','guide_id', 'production','bom', 'materials', 'timeline', 'change_code'));
    }
    public function get_bom($bom, $guide_id, $item_code, $parent_item_code){
        try {
            $bom1 = DB::connection('mysql')->table('tabBOM Item as bom')
            ->join('tabItem as item', 'item.name', 'bom.item_code')
            ->whereNotIn('item.item_group', ['Raw Material', 'Factory Supplies'])
            // ->whereIn('item.item_classificati', ['RA - REFLECTOR ASSEMBLY', 'SA - Sub Assembly', 'HO - Housing', 'DI - Diffuser','FA - FRAME ASSEMBLY','FR - FRAME','FPA - FRONT PLATE ASSEMBLY','WA - WIREGUARD ASSEMBLY'])
            ->whereNotIn('item.item_classification', ['BP - Battery Pack', 'WW - Wall Washer Luminaire', 'WL - Wall Lights'])
            ->where('bom.docstatus', '<', 2)
            ->where('bom.parent', $bom)
            ->select('bom.*', 'item.parts_category')
            ->orderBy('bom.idx', 'asc')->get();
            // dd($item_code);
            $materials = [];
            foreach ($bom1 as $item) {
                $default_bom = DB::connection('mysql')->table('tabBOM')
                ->where('docstatus', '<', 2)
                ->where('is_default', 1)
                // ->orderBy('modified', 'desc')
                ->where('item', $item->item_code)
                ->first();
                
                $item_details = DB::connection('mysql')->table('tabItem')->where('name', $item->item_code)->first();
                $item_description = ($item_details) ? $item_details->description : '';
                $child_bom = ($default_bom) ? $default_bom->name : $item->bom_no;

                $production= DB::connection('mysql_mes')->table('production_order AS po')
                    ->whereNotIn('po.status', ['Cancelled'])
                    ->where(function($q) use ($guide_id) {
                            $q->Where('po.sales_order', $guide_id)
                                ->orWhere('po.material_request', $guide_id);
                        })
                    ->where('item_code', $item->item_code)
                    ->where('sub_parent_item_code', $item_code)
                    ->where('parent_item_code', $parent_item_code)
                    ->first(); 

                if (!empty($production)) {
                    $data1=[];
                    $data2=[];
                    $jt_details1 =  DB::connection('mysql_mes')->table('job_ticket')
                    ->where('production_order', $production->production_order)
                    ->select('job_ticket_id', 'workstation')
                    ->get();

                    if(count($jt_details1) == 0){
                        $production_order_no= null;
                        $planned_start_date=null;
                        $end_date= null;
                        $start_date= null;
                        $duration=null;
                        $status=null;
                        $qty_to_manufacture=null;
                        $produced_qty=null;
                        $jobtickets_details=[];
                        $parts_category=null;
                        $done=null;
                    }else{
                        
                        $start_date = $production->actual_start_date;
                        $end_date = $production->actual_end_date;
                        $from_carbon = Carbon::parse($start_date);
                        $to_carbon = Carbon::parse($end_date);

                        $duration = $from_carbon->diffInSeconds($to_carbon);

                        $jobtickets_details=DB::connection('mysql_mes')->table('job_ticket as jt')
                                ->join('process as p','p.process_id', 'jt.process_id')
                                ->where('jt.production_order', $production->production_order)
                                ->where('jt.status', 'In Progress')
                                ->select('p.process_name', 'jt.workstation')
                                ->distinct('jt.workstation')
                                ->get();
                        
                        $stat= $production->status;
                        $done=$production->produced_qty;
                    
                        $production_order_no= $production->production_order;
                        $planned_start_date= $production->planned_start_date;

    
                        $status=$stat;
                        $qty_to_manufacture=$production->qty_to_manufacture;
                        $produced_qty=$production->produced_qty;
                        $parts_category = $production->parts_category; 
                        

                    }
                   
                    
                   
                }else{
                    $production_order_no= null;
                    $planned_start_date=null;
                    $end_date= null;
                    $start_date= null;
                    $duration=null;
                    $status=null;
                    $qty_to_manufacture=null;
                    $produced_qty=null;
                    $jobtickets_details=[];
                    $parts_category=null;
                    $done=null;
                }

                $available_stock = DB::connection('mysql_mes')->table('fabrication_inventory')->where('item_code', $item->item_code)->sum('balance_qty');

                $materials[] = [
                    'item_code' => $item->item_code,
                    'description' => $item_description,
                    'qty' => $item->qty,
                    'bom_no' => $item->bom_no,
                    'uom' => $item->uom,
                    'parts_category' => $parts_category,
                    'production_order' => ($production_order_no == null) ? '': $production_order_no,
                    'planned_start_date' => ($planned_start_date == null) ? '': $planned_start_date,
                    'end_date' => ($end_date == null) ? '': Carbon::parse($end_date)->format('F d, Y h:ia'),
                    'start_date' => ($start_date == null) ? '': Carbon::parse($start_date)->format('F d, Y h:ia'),
                    'duration' => ($duration == null) ? '': $this->seconds2human($duration),
                    'status' => ($status == null) ? '': $status,
                    'qty_to_manufacture' => ($status == null) ? '': $qty_to_manufacture,
                    'produced_qty' => $done,
                    'bom_no'=> (empty($default_bom->name))? '':$default_bom->name,
                    'current_load' => $jobtickets_details,
                    'child_nodes' => $this->get_bom($child_bom, $guide_id, $item->item_code, $parent_item_code),
                    'available_stock' => $available_stock
                ];
            }
            // dd($materials);
            return $materials;
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }


    public function getTimesheetProcess($prod_no){
		$req = DB::connection('mysql_mes')->table('production_order')->where('production_order', $prod_no)
			->first()->qty_to_manufacture;

        $workstations = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $prod_no)
            ->join('process as p', 'p.process_id', 'job_ticket.process_id')
			->selectRaw('job_ticket.process_id, GROUP_CONCAT(status) as status, p.process_name, job_ticket.workstation')
			->orderBy('idx', 'asc')->groupBy('job_ticket.process_id','p.process_name','job_ticket.workstation')->get();

		$data = [];
		foreach($workstations as $row){
            $completed = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $prod_no)->where('process_id',  $row->process_id)->sum('completed_qty');
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
                'process_name' => $row->process_name,
                'status' => $status,
                'completed_qty' => $completed,
                'required' => $req
			];
		}

		return $data;
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
    public function productionKanban(){
		$permissions = $this->get_user_permitted_operation();

		$mes_user_operations = DB::connection('mysql_mes')->table('user')
			->join('operation', 'operation.operation_id', 'user.operation_id')
			->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
			->where('module', 'Production')
			->where('user_access_id', Auth::user()->user_id)->pluck('operation_name')->toArray();

	   	$unscheduled_prod = DB::connection('mysql_mes')->table('production_order')
			->whereNotIn('status', ['Stopped', 'Cancelled'])
			->where('feedback_qty',0)
			->where('is_scheduled', 0)
			->where("operation_id", '1')
			->orderBy('sales_order', 'desc')
			->orderBy('material_request', 'desc')->get();

    	$unscheduled = [];
    	$max = [];
    	foreach ($unscheduled_prod as $row) {
	    $stripfromcomma =strtok($row->description, ",");

                                   
                           
            $spotlogs_inprogress=DB::connection('mysql_mes')->table('job_ticket as jt')
            ->leftJoin('spotwelding_qty as spotpart', 'spotpart.job_ticket_id','jt.job_ticket_id')
            ->join('process as p','p.process_id','jt.process_id')
			->where('jt.production_order', $row->production_order)
			->where('spotpart.status', "In Progress")
            ->select('spotpart.status as stat');  

            $timelogs_inprogress=DB::connection('mysql_mes')->table('job_ticket as jt')
            ->leftJoin('time_logs as tl', 'jt.job_ticket_id','tl.job_ticket_id')
            ->join('process as p','p.process_id','jt.process_id')
			->where('jt.production_order', $row->production_order)
			->where('tl.status', "In Progress")
            ->select('tl.status as stat')
			->union($spotlogs_inprogress);

			$groupby_logs = DB::connection('mysql_mes')->query()->fromSub($timelogs_inprogress,'logss')
			  ->select('stat')->first();

			$drag = empty($groupby_logs->stat)? "move":"not_move";
			
			if($drag == "not_move"){
				$prod_status="In Progress";
			}else{
				if($row->status == "Completed"){
					$prod_status="Completed";

				}else{
					if($row->status == "Not Started"){
						$prod_status="Not Started";

					}else{
						$prod_status="In Progress- On Que";
					}

				}
			}
			
			  
            


			$unscheduled[] = [
				'id' => $row->production_order,
				'status' => $prod_status,
				'name' => $row->production_order,
				'order_no' => $row->order_no,
				'customer' => $row->customer,
				'delivery_date' => $row->delivery_date,
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
				'sales_order' =>($row->sales_order == null) ? $row->material_request: $row->sales_order,
				'batch' => null,
				'process_stat'=> $this->material_status_stockentry($row->production_order, $row->status),
				'drag' => $drag,
				'cycle_time' => $this->compute_item_cycle_time($row->item_code, $row->qty_to_manufacture),
				'cycle_in_seconds' =>$this->compute_item_cycle_time_seconds_format($row->item_code, $row->qty_to_manufacture)			];
		}

		$period = CarbonPeriod::create(now()->subDays(1), now()->addDays(6));

		// Iterate over the period->subDays(1)
		$scheduled = [];
		foreach ($period as $date) {
			$orders = $this->getScheduledProdOrders($date->format('Y-m-d'));
			$shift_sched = $this->get_prod_shift_sched($date->format('Y-m-d'));
			$total_seconds= collect($orders)->sum('cycle_in_seconds');
			$scheduled[] = [
				'shift'=> $shift_sched,
				'schedule' => $date->format('Y-m-d'),
				'orders' => $orders,
				'estimates' => $this->format_for_estimates($total_seconds),
				'estimates_in_seconds' => $total_seconds
			];
		}
		// return $scheduled;
    	return view('tbl_reload_production_kanban', compact('unscheduled', 'scheduled', 'mes_user_operations', 'permissions'));
	}
	public function material_status_stockentry($production_order, $stat){
			
			//feedbacked
			// $is_feedbacked = DB::connection('mysql')->table('tabStock Entry')
			// 	->where('purpose', 'Manufacture')
			// 	->where('production_order', $production_order)
			// 	->where('docstatus', 1)->first();

			// if ($is_feedbacked) {
			// 	$status = 'Completed';
			// }

			
			$is_transferred = DB::connection('mysql')->table('tabStock Entry')
				->where('purpose', 'Material Transfer for Manufacture')
				->where('production_order', $production_order)
				->where('docstatus', 1)->first();

			if ($is_transferred) {
				$status = 'Material Issued';
			}else{
				$status = 'Material For Issue';
			}

			$spotlogs=DB::connection('mysql_mes')->table('job_ticket as jt')
            ->leftJoin('spotwelding_qty as spotpart', 'spotpart.job_ticket_id','jt.job_ticket_id')
            ->join('process as p','p.process_id','jt.process_id')
            ->where('jt.production_order', $production_order)
            ->orderBy('spotpart.last_modified_at', 'desc')
            ->select(DB::raw('(SELECT MAX(last_modified_at) FROM spotwelding_qty WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS last_modified_at'),'p.process_name','jt.production_order','jt.job_ticket_id','jt.workstation');  

            $timelogs=DB::connection('mysql_mes')->table('job_ticket as jt')
            ->leftJoin('time_logs as tl', 'jt.job_ticket_id','tl.job_ticket_id')
            ->join('process as p','p.process_id','jt.process_id')
            ->where('jt.production_order', $production_order)
            ->select('tl.last_modified_at','p.process_name','jt.production_order','jt.job_ticket_id','jt.workstation')
			->union($spotlogs); 
			$groupby_log = DB::connection('mysql_mes')->query()->fromSub($timelogs,'logs')
              ->select('last_modified_at', 'process_name', 'job_ticket_id','workstation')
			  ->orderBy('last_modified_at', 'DESC')->first();


			if(!empty($groupby_log)){
				if($groupby_log->last_modified_at != null){
					$status = $groupby_log->workstation;
				}
			}

			if ($stat == "Completed") {
				$status = 'Ready For Feedback';
			}

			return $status;
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

	public function get_prod_shift_sched($date){
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

    public function getScheduledProdOrders($schedule_date){
    	$orders = DB::connection('mysql_mes')->table('production_order')
    		->whereNotIn('status', ['Cancelled'])->where('is_scheduled', 1)
			->whereDate('planned_start_date', $schedule_date)
			->where("operation_id", '1')
			->where('feedback_qty',0)
    		->orderBy('order_no', 'asc')->orderBy('order_no', 'asc')->orderBy('created_at', 'desc')
    		->get();

    	$scheduled = [];
    	foreach($orders as $row){
    		$stripfromcomma =strtok($row->description, ",");
			
			$spotlogs_inprogress=DB::connection('mysql_mes')->table('job_ticket as jt')
            ->leftJoin('spotwelding_qty as spotpart', 'spotpart.job_ticket_id','jt.job_ticket_id')
            ->join('process as p','p.process_id','jt.process_id')
			->where('jt.production_order', $row->production_order)
			->where('spotpart.status', "In Progress")
            ->select('spotpart.status as stat');  

            $timelogs_inprogress=DB::connection('mysql_mes')->table('job_ticket as jt')
            ->leftJoin('time_logs as tl', 'jt.job_ticket_id','tl.job_ticket_id')
            ->join('process as p','p.process_id','jt.process_id')
			->where('jt.production_order', $row->production_order)
			->where('tl.status', "In Progress")
            ->select('tl.status as stat')
			->union($spotlogs_inprogress);
			
			$groupby_logs = DB::connection('mysql_mes')->query()->fromSub($timelogs_inprogress,'logss')
			  ->select('stat')->first();

			$drag = empty($groupby_logs->stat)? "move":"not_move";
			if($drag == "not_move"){
				$prod_status="In Progress";
			}else{
				if($row->status == "Completed"){
					$prod_status="Completed";

				}else{
					if($row->status == "Not Started"){
						$prod_status="Not Started";

					}else{
						$prod_status="In Progress- On Que";
					}

				}
			}
    		$scheduled[] = [
    			'id' => $row->production_order,
	    		'name' => $row->production_order,
				'status' => $prod_status,
	    		'order_no' => $row->order_no,
	    		'customer' => $row->customer,
	    		'delivery_date' => $row->delivery_date,
	    		'production_item' => $row->item_code,
	    		'description' => $row->description,
	    		'parts_category' => $row->parts_category,
	    		'parent_item_code' => $row->parent_item_code,
	    		'strip' => $stripfromcomma,
	    		'qty' => $row->qty_to_manufacture,
	    		'stock_uom' => $row->stock_uom,
	    		'produced_qty' => $row->produced_qty,
	    		'classification' => $row->classification,
				'production_order' => $row->production_order,
				'sales_order' =>($row->sales_order == null) ? $row->material_request:$row->sales_order,
				'batch' => null,
				'process_stat' => $this->material_status_stockentry($row->production_order, $row->status),
				'drag' => $drag,
				'cycle_time' => $this->compute_item_cycle_time($row->item_code, $row->qty_to_manufacture),
				'cycle_in_seconds' =>$this->compute_item_cycle_time_seconds_format($row->item_code, $row->qty_to_manufacture)

	    	];
    	}
		
    	return $scheduled;
	}

}