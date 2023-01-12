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


class SecondaryController extends Controller
{
    use GeneralTrait;

    public function get_stock_entry_details($prod){
        $ste = DB::connection('mysql')->table('tabStock Entry')
            ->where('purpose', 'Material Transfer for Manufacture')
            ->where('production_order', $prod)->first();
        
        if(empty($ste)){
            $ste_item = [];
        }else{
            $ste_item =DB::connection('mysql')->table('tabStock Entry Detail')
                ->where('parent', $ste->name)->get();
        }
        
        return view('tables.tbl_prod_stock_details', compact('ste','ste_item'));
    }

    public function get_stock_entry_exist($prod){
        $ste = DB::connection('mysql')->table('tabStock Entry')
            ->where('purpose', 'Material Transfer for Manufacture')
            ->where('production_order', $prod)->first();
        
        if(empty($ste)){
            return response()->json(['success' => 0, 'message' => 'No Existing Stock Entry']);

        }else{
            return response()->json(['success' => 1, 'message' => 'Stock Entry Exist']);

        }
    }

    public function acceptTask(Request $request){
        try {
            $tsd = DB::connection('mysql')->table('tabTimesheet Detail')->where('name', $request->id)->first();
            $now = Carbon::now();
            if ($request->status == "Accepted") {
                if ((int)$tsd->completed_qty != (int)$request->qty_accepted) {
                    // insert new row for balance qty
                    $this->insertRowBal($request->id, $request->qty_accepted);
                }

                $update = [
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => $request->operator_name,
                    'completed_qty' => $request->qty_accepted,
                    'machine' => $request->machine,
                    'machine_name' => $request->machine_name,
                    'operator' => $request->operator_name,
                    'qty_accepted' => $request->qty_accepted,
                    'status' => $request->status,
                    'operator_id' => $request->operator_id,
                    'operator_name' => $request->operator_name,
                ];

                DB::connection('mysql')->table('tabTimesheet Detail')->where('name', $tsd->name)->update($update);
            }

            return redirect()->back();
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }
    
    public function operatorDashboard($machine){
        $machine_details = DB::connection('mysql')->table('tabMachine AS m')->join('tabWorkstation Machine AS wm', 'm.name', 'wm.machine_code')
            ->where('m.machine_id', $machine)->selectRaw('wm.parent as workstation, m.*')->first();

        $unassigned = DB::connection('mysql')->table('tabTimesheet Detail AS tsd')
            ->join('tabTimesheet AS ts', 'ts.name', 'tsd.parent')
            ->join('tabWork Order AS pro', 'pro.name', 'ts.production_order')
            ->where('tsd.workstation', $machine_details->workstation)
            ->where('ts.docstatus', 0)->where('tsd.status', 'Unassigned')
            ->whereDate('ts.creation', '>', '2019-10-11')
            ->select('tsd.name AS tsdname', 'ts.name', 'pro.production_item', 'pro.item_name', 'tsd.completed_qty', 'tsd.status', 'pro.priority')
            ->orderByRaw("FIELD(pro.priority, 'High', 'Normal', 'Low') ASC")
            ->orderBy('ts.creation', 'asc')->get();

        $unassigned_tasks = [];
        foreach ($unassigned as $row) {
            $workstations = $this->getTimehsheetProcess($row->name);
            $unassigned_tasks[] = [
                'tsdname' => $row->tsdname,
                'name' => $row->name,
                'production_item' => $row->production_item,
                'item_name' => $row->item_name,
                'completed_qty' => number_format($row->completed_qty),
                'priority' => $row->priority,
                'workstations' => $workstations,
            ];
        }

        $operator_id = (Auth::check()) ? Auth::user()->id_security_key : null;
        $assigned = DB::connection('mysql')->table('tabTimesheet Detail AS tsd')
            ->join('tabTimesheet AS ts', 'ts.name', 'tsd.parent')
            ->join('tabWork Order AS pro', 'pro.name', 'ts.production_order')
            ->where('tsd.workstation', $machine_details->workstation)
            ->where('operator_id', $operator_id)
            ->where('ts.docstatus', 0)->where('tsd.status', '!=', 'Unassigned')
            ->whereDate('ts.creation', '>', '2019-10-11')
            ->select('tsd.name AS tsdname', 'ts.name', 'pro.production_item', 'pro.item_name', 'tsd.completed_qty', 'tsd.status', 'tsd.good', 'tsd.reject', 'tsd.rework', 'pro.priority', 'tsd.qty_accepted','tsd.to_time','tsd.from_time','ts.production_order','tsd.hours')
            ->orderBy('ts.creation', 'asc')->get();

        $assigned_tasks = [];
        foreach ($assigned as $row) {
            $assigned_tasks[] = [
                'duration' => $this->convertTime($row->hours),
            ];
        }  
   
        return view('operator_dashboard', compact('assigned', 'unassigned_tasks', 'machine_details','assigned_tasks'));
    }

    public function getTimehsheetProcess($jtno){
        $req = DB::connection('mysql')->table('tabTimesheet')->where('name', $jtno)->sum('no_of_units');
        $workstations = DB::connection('mysql')->table('tabTimesheet Detail')->where('parent', $jtno)
            ->selectRaw('workstation, GROUP_CONCAT(status) as status')
            ->groupBy('workstation')->orderBy('idx', 'asc')->get();

        $data = [];
        foreach($workstations as $row){
            $status = '';
            if (strpos($row->status, 'Progress')) {
                $status = 'active';
            }elseif (in_array($row->status, ['Unassigned', 'Accepted'])) {
                $status = '';
            }elseif ($row->status == 'Completed'){
                $status = 'completed';
            }
            $data[] = [
                
                'workstation' => $row->workstation,
                'status' => $status
            ];
        }

        return $data;
    }

    public function machineOverview(){
        $workstation= DB::connection('mysql_mes')->table('workstation AS w')
            ->join('operation as op', 'op.operation_id', 'w.operation_id')
            ->select("w.workstation_name", "w.workstation_id")
            ->where('op.operation_name','Fabrication')
            ->orderBy('w.order_no', 'asc')->get();

        return view('machine_overview.index', compact('workstation'));
    }

    public function machine_tasklist_table($workstation, $machine_code){
        $data=[];
        $data[]=[
            'wip' =>$wip,
            'ctd' =>$ctd,
            '$total' => $total
        ];
        return $data;
    }

    public function Queingtime($a, $b){
        $tasks =  DB::connection('mysql_mes')->table('job_ticket AS tsd')
            ->join('production_order AS pro', 'pro.production_order', 'tsd.production_order')
            ->where('tsd.workstation', $a)->where('tsd.machine_code', $b)
            ->whereIn('tsd.status', ['Completed','In Progress'])
            ->whereDate('tsd.created_at', '>', '2019-10-11')
            ->select('tsd.job_ticket_id AS tsdname', 'tsd.production_order', 'pro.item_code', 'tsd.completed_qty', 'tsd.status', 'pro.priority', 'tsd.operator_name','tsd.created_at', 'tsd.from_time')
            ->orderByRaw("FIELD(pro.priority, 'High', 'Normal', 'Low') ASC")
            ->orderBy('tsd.created_at', 'asc')->get();
        $data=[];
        foreach ($tasks as $rows) {
            $start = Carbon::parse($rows->created_at);
            $end = Carbon::parse($rows->from_time);
            $totalDuration = $end->diffInSeconds($start);
            $data[]=[
                "total" => $totalDuration
            ];
        }

        return $data;   
    }

    function seconds2human($ss) {
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

    public function machine_details($machine_name){
        $machine_workstations = DB::connection('mysql_mes')->table('machine as m')
            ->join('workstation_machine AS wm', 'm.machine_code', 'wm.machine_code')
            ->join('workstation as w', 'wm.workstation_id', 'w.workstation_id')
            ->where('wm.workstation_id', $machine_name)->orderBy('wm.machine_code', 'ASC')
            ->select('m.*', 'w.workstation_name as workstationname')->get();
        
        $data=[];
        foreach ($machine_workstations as $row) {
            $tasks = DB::connection('mysql_mes')->table('job_ticket AS tsd')
                ->join('production_order AS pro', 'pro.production_order', 'tsd.production_order')
                ->where('tsd.workstation', $row->workstationname)->where('tsd.machine_code', $row->machine_code)
                ->whereDate('tsd.created_at', '>', '2019-10-11')
                ->select('tsd.job_ticket_id AS tsdname', 'tsd.production_order', 'pro.item_code', 'tsd.completed_qty', 'tsd.status', 'pro.priority', 'tsd.operator_name', 'tsd.from_time')
                ->get();

            $totals = collect($tasks)->whereIn('status',['Completed','In Progress'])->count();
            $completed = collect($tasks)->whereIn('status','Completed')->sum('completed_qty');
            $accepted = collect($tasks)->whereIn('status',['Accepted','In Progress','Completed'])->sum('completed_qty');
            $totalss = collect($tasks)->whereIn('status',['Completed','In Progress','Accepted'])->count();
            $totalduration= $this->Queingtime($row->workstationname,$row->machine_code);
            $counttotalSec = collect($totalduration)->sum('total');

            if ($totals == 0) {
                $var1=1;
            }else{
                $var1=$totals;
            }
            if ($accepted == 0) {
                $var=1;
            }else{
                $var=$accepted;
            }
                    

            $percentage=($completed/ $var)*100;
            $avr_hrs_submission=($counttotalSec/ $var1);
            $converted = $this->seconds2human($avr_hrs_submission);

            $data[]=[
                "image_file" => $row->image,
                "machine_code" => $row->machine_code,
                "machine_name" => $row->machine_name,
                "status" => $row->status,
                'timesheet'=> $totals,
                'tasks'=>$tasks,
                'duration' => $counttotalSec,
                'avg' => $converted,
                'completed_qty'=> $completed,
                'accepted_qty'=> $accepted,
                'percentage' => round($percentage, 2),
                'workstation' => $row->workstationname
            ];
        }
    
        return $data;
    }
  
    public function machineTaskList(Request $request){
        $datas=[];
        if ($request->prod_line == "All") {
            $workstatione= DB::connection('mysql_mes')->table('workstation as w')
            ->join('operation as op', 'op.operation_id', 'w.operation_id')
            ->where('op.operation_name', "Fabrication")->orderBy('w.order_no', 'asc')
            ->select("w.workstation_name", "w.workstation_id")->get();

            foreach ($workstatione as $row) {
                $datas[]=[
                    "production_line" => $row->workstation_name,
                    "w_to_m" => $this->machine_details($row->workstation_id)
                ];
            }
        }else{
            $workstatione= DB::connection('mysql_mes')->table('workstation')
                ->where('workstation_id', $request->prod_line)
                ->select("workstation_name", "workstation_id")
                ->first();
            $datas[]=[
                "production_line" => $workstatione->workstation_name,
                "w_to_m" => $this->machine_details($workstatione->workstation_id)
            ];
        }

        return view('machine_overview.content', compact('datas'));
    }

    public function machine_details_tbl(Request $request){
        $machine_workstations = DB::connection('mysql_mes')->table('machine')
            ->where('machine_code', $request->machine_code)->first();

        $workstation = $request->workstation;
        $quetime = $request->quetime;
        $percetage = $request->percetage;
        $completedqty = $request->completedqty;
        $acceptedqty = $request->acceptedqty;

        return view('tables.tbl_machine_details', compact('machine_workstations','workstation', 'quetime','percetage','completedqty','acceptedqty' ));
    }

    public function operators_workstation_TaskList($workstation,$W_name){
        try {
            $tasks = DB::connection('mysql')->table('tabTimesheet Detail AS tsd')
                ->join('tabTimesheet AS ts', 'ts.name', 'tsd.parent')
                ->join('tabWork Order AS pro', 'pro.name', 'ts.production_order')
                ->join('tabWorkstation AS wt', 'wt.workstation_name', 'tsd.workstation')
                ->where('wt.workstation', $workstation)
                ->where('tsd.operation', 'Fabrication')
                ->where('ts.docstatus', 0)
                ->where('tsd.status', $W_name)
                ->whereDate('ts.creation', '>', '2019-10-11')
                ->select('tsd.name AS tsdname', 'ts.production_order', 'pro.production_item', 'pro.item_name', 'tsd.completed_qty', 'tsd.status', 'pro.priority', 'tsd.operator_name')
                ->orderByRaw("FIELD(pro.priority, 'High', 'Normal', 'Low') ASC")
                ->orderBy('ts.creation', 'asc')
                ->get();

            $wip = collect($tasks)->where('status', 'In Progress')->sum('completed_qty');
            $ctd = collect($tasks)->where('status', 'Completed')->sum('completed_qty');
            $total = collect($tasks)->sum('completed_qty');

            $dashboard[] = [
                'tasks' => $tasks
            ];

            return view('tables.tbl_operator_workstation', compact('dashboard'));
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function decimalHours($time) {
        $hms = explode(":", $time);
        return ($hms[0] + ($hms[1]/60) + ($hms[2]/3600));
    }

    public function testing(){
        $hms = "2:12:12";
        $decimalHours = $this->decimalHours($hms);
        $hrs =$this->convertTime($decimalHours);
        return $hrs;
    }

    public function convertTime($dec) {
        // start by converting to seconds
        $seconds = ($dec * 3600);
        // we're given hours, so let's get those the easy way
        $hours = floor($dec);
        // since we've "calculated" hours, let's remove them from the seconds variable
        $seconds -= $hours * 3600;
        // calculate minutes left
        $minutes = floor($seconds / 60);
        // remove those from seconds as well
        $seconds -= $minutes * 60;
        // return the time formatted HH:MM:SS
        return $this->lz($hours).":".$this->lz($minutes).":".round($seconds, 2);
    }

    public function lz($num){
        return (strlen($num) < 2) ? "0{$num}" : $num;
    }

    public function workstation_name(){
        $machine_details = DB::connection('mysql')->table('tabMachine AS m')->select('m.workstation')->groupBy('m.workstation')->get();
        dd($machine_details);
        // return view('testing', compact('machine_details'));
    }

    public function operatorpage($id){
        $tabWorkstation= DB::connection('mysql')->table('tabWorkstation')
                        ->where('name', $id)
                        ->select('production_line')
                        ->first();
        $now = Carbon::now();
        $workstation=$tabWorkstation->production_line;
        $workstation_name=$id;
        $date = $now->format('M d Y');
        $day_name= $now->format('l');
        return view('operator_workstation_dashboard', compact('workstation','workstation_name', 'day_name', 'date'));
    }

    public function operatorDashboards($machine){
        return view('testing');
    }

    public function logout($id){
        Auth::guard('web')->logout();
        $route= '/operator/'.$id;
        return redirect($route);
    }
    
    public function current_data_operator($workstation){
        $datas=[];
        $workstations = DB::connection('mysql')->table('tabTimesheet Detail as tsd')
        ->join('tabTimesheet AS ts', 'ts.name', 'tsd.parent')
        ->join('tabWorkstation AS wt', 'wt.workstation_name', 'tsd.workstation')
        ->where('tsd.operation', 'Fabrication') 
        ->where('wt.production_line', $workstation)
        ->whereDate('tsd.creation', '>', '2019-10-11')
        ->where('ts.docstatus', 0)
        ->select('tsd.*')
        ->get();
        $pending = collect($workstations)->where('status', 'Pending')->count();
        $inprogress = collect($workstations)->where('status', 'In Progress')->count();
        $unassigned = collect($workstations)->where('status', 'Unassigned')->count();
        $completed = collect($workstations)->where('status', 'Completed')->count();
        $datas[]=[
            'completed' => $completed,
            'pending' => $pending,
            'inprogress' => $inprogress,
            'unassigned' => $unassigned
        ];

       return response()->json($datas);
    }

    public function available_machine($machine){
        $machine_available= db::connection('mysql')->table('tabMachine AS tm')
                            ->join('tabWorkstation Machine AS twm', 'twm.machine_code','tm.machine_code')
                            ->join('tabWorkstation AS tw', 'tw.workstation_name','twm.parent')
                            ->where('tw.production_line',$machine)
                            ->select('tm.*','twm.parent')
                            ->get();
        $status="Machine";

        // dd($machine_available);
        return view('tables.tbl_operator_machine_available', compact('machine_available','status'));
    }

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
            $filenametostore = $request->machine_code.'.'.$extension;
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

    public function machine_breakdown(Request $request,$machine_code){
            $machine=DB::connection('mysql_mes')->table('machine_breakdown as tmb')
                    ->where('tmb.machine_id', $machine_code)
                    ->whereYear('date_reported', $request->year)
                    ->whereMonth('date_reported', $request->month)
                    ->where('type', 'Breakdown')
                    ->get();
            $machine1=DB::connection('mysql_mes')->table('machine_breakdown as tmb')
                    ->select('tmb.category')
                    ->whereYear('date_reported', $request->year)
                    ->whereMonth('date_reported', $request->month)
                    ->where('type', 'Breakdown')
                    ->where('tmb.machine_id',$machine_code)
                    ->groupBy('tmb.category')
                    ->get();
            $data=[];
            foreach ($machine1 as $row) {
            $table1 = collect($machine)->where('category', $row->category)->where('type', 'Breakdown')->count();
            $duration = collect($machine)->where('category', $row->category)->where('type', 'Breakdown')->sum("duration");
            $data[]=[
                    'occurence' => $table1,
                    'reason' => $row->category,
                    'duration' => round($duration, 2),
                ];

            }

        return $data;
            
    }

    public function machine_corrective(Request $request, $machine_code){
            $machine=DB::connection('mysql_mes')->table('machine_breakdown as tmb')
                    ->where('tmb.machine_id', $machine_code)
                    ->whereYear('date_reported', $request->year)
                    ->whereMonth('date_reported', $request->month)
                    ->where('type', 'Corrective')
                    ->get();
            $machine1=DB::connection('mysql_mes')->table('machine_breakdown as tmb')
                    ->select('tmb.category')
                    ->whereYear('date_reported', $request->year)
                    ->whereMonth('date_reported', $request->month)
                    ->where('type', 'Corrective')
                    ->where('tmb.machine_id',$machine_code)
                    ->groupBy('tmb.category')
                    ->get();
            $data=[];
            foreach ($machine1 as $row) {
            $table1 = collect($machine)->where('category', $row->category)->where('type', 'Corrective')->count();
            $duration = collect($machine)->where('category', $row->category)->where('type', 'Corrective')->sum("duration");
            $data[]=[
                    'occurence' => $table1,
                    'reason' => $row->category,
                    'duration' => round($duration, 2),
                ];

            }

        return $data;
            
    }

    public function getNextOrderNumber(){
        // Get the last created order
        $lastOrder = DB::connection('mysql_mes')->table('machine_breakdown')->orderBy('machine_breakdown_id', 'desc')->select('machine_breakdown_id')->first();

        if (empty($lastOrder->machine_breakdown_id)){
            // We get here if there is no order at all
            // If there is no number set it to 0, which will be 1 at the end.

            $number = 0;
        }else{
            $number1 = $lastOrder->machine_breakdown_id;
            $number = preg_replace("/[^0-9]{1,4}/", '', $number1); // return 1234
        }

        // If we have ORD000001 in the database then we only want the number
        // So the substr returns this 000001

        // Add the string in front and higher up the number.
        // the %05d part makes sure that there are always 6 numbers in the string.
        // so it adds the missing zero's when needed.
        
        return 'MR-' . sprintf('%05d', intval($number) + 1);
    }

    public function maintenanceMachineList(){
        $permissions = $this->get_user_permitted_operation();
        return view('maintenance_machine_list', compact('permissions'));
    }

    public function machineBreakdownSave(Request $request){
        try {
            $now = Carbon::now();
            $values = [
                'machine_id' => $request->id,
                'machine_breakdown_id' => $this->getNextOrderNumber(),
                'status' => 'Pending',
                'reported_by' => $request->reported_by,
                'date_reported' => $now->toDateTimeString(),
                'remarks' => $request->remarks,
                'date_resolved' => null,
                'work_done' => null,
                'assigned_maintenance_staff' => null,
                'category' => $request->category,
                'type' => $request->category,
                'corrective_reason' => ($request->category == "Corrective") ?  $request->corrective_reason : "" ,
                'breakdown_reason' => ($request->category == "Corrective") ?  "" : $request->breakdown_reason ,
                'created_by' => $request->reported_by,
                'last_modified_by' => $request->reported_by,
            ];
            DB::connection('mysql_mes')
                ->table('machine_breakdown')->insert($values);

            $gatepass_id= $request->id;
            if ($request->category == "Breakdown") {
                $type="Breakdown";
                $reason = $request->breakdown_reason;
                DB::connection('mysql_mes')->table('machine')->where('machine_code', $request->id)->update(['status' => 'Unavailable']);

            }else{
                $type="Corrective";
                $reason = $request->corrective_reason;
            }
            $data = array(
                'employee_name'      => Auth::user()->employee_name,
                'year'               => now()->format('Y'),
                'slip_id'            => $gatepass_id,
                'type'               => $type,
                'reason'             => $reason
            );
       
            Mail::to("maintenance@fumaco.local")->send(new SendMail_machinebreakdown($data));
             
            return response()->json(['success' => 1, 'message' => 'Machine Breakdown successfully submitted.']);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function saveMaintenanceRequest(Request $request){
        DB::connection('mysql_mes')->beginTransaction();
        try {
            $status = $request->status;
            $new_id = $this->getNextOrderNumber();

            DB::connection('mysql_mes')->table('machine_breakdown')->insert([
                'machine_id' => $request->machine_id,
                'machine_breakdown_id' => $new_id,
                'status' => $status,
                'reported_by' => $request->reported_by,
                'date_reported' => $request->date_reported,
                'remarks' => $request->remarks,
                'date_resolved' => $status == 'Done' ? $request->date_resolved : null,
                'work_done' => $status == 'Done' ? $request->work_done : null,
                'assigned_maintenance_staff' => $request->assigned_maintenance_staff,
                'hold_reason' => $request->hold_reason,
                'findings' => $request->findings,
                'complaints' => $request->complaints,
                'building' => $request->building,
                'category' => $request->category,
                'type' => $request->type,
                'corrective_reason' => ($request->category == "Corrective") ? $request->corrective_reason : null,
                'breakdown_reason' => ($request->category == "Breakdown") ? $request->breakdown_reason : null,
                'created_by' => Auth::user()->email,
                'last_modified_by' => Auth::user()->email
            ]);

            if ($request->category == "Breakdown") {
                DB::connection('mysql_mes')->table('machine')->where('machine_code', $request->machine_id)->update(['status' => 'Unavailable']);
            }

            if($request->maintenance_staff){
                $employee_details = DB::connection('mysql_essex')->table('users')->whereIn('user_id', $request->maintenance_staff)->get();
                $employee_details = collect($employee_details)->groupBy('user_id');

                foreach(array_filter($request->maintenance_staff) as $staff){
                    DB::connection('mysql_mes')->table('machine_breakdown_personnel')->insert([
                        'machine_breakdown_id' => $new_id,
                        'user_id' => $staff,
                        'email' => isset($employee_details[$staff]) ? $employee_details[$staff][0]->email : null,
                        'created_by' => Auth::user()->email,
                        'last_modified_by' => Auth::user()->email
                    ]);
                }
            }

            DB::connection('mysql_mes')->commit();
             
            return response()->json(['success' => 1, 'message' => 'Maintenance Request has been successfully created.']);
        } catch (Exception $e) {
            DB::connection('mysql_mes')->rollback();

            return response()->json(['success' => 0, "message" => 'An error occured. Please try again.']);
        }
    }

    public function timesheet_details_report($id){
        $tasks = DB::connection('mysql')->table('tabTimesheet Detail AS tsd')
                    ->join('tabTimesheet AS ts', 'ts.name', 'tsd.parent')
                    ->join('tabWork Order AS pro', 'pro.name', 'ts.production_order')
                    ->where('tsd.parent', $id)
                    ->where('tsd.operation', 'Fabrication')
                    ->where('ts.docstatus', 0)
                    ->whereDate('ts.creation', '>', '2019-10-11')
                    ->select('tsd.workstation')
                    ->groupBy('tsd.workstation')
                    ->orderBy('tsd.idx', 'asc')
                    ->get();
        $details=[];
        foreach ($tasks as $row) {
             $jt_details = DB::connection('mysql')->table('tabTimesheet Detail AS tsd')
                    ->join('tabTimesheet AS ts', 'ts.name', 'tsd.parent')
                    ->join('tabWork Order AS pro', 'pro.name', 'ts.production_order')
                    ->where('tsd.parent', $id)
                    ->where('tsd.workstation', $row->workstation)
                    ->where('tsd.operation', 'Fabrication')
                    ->where('ts.docstatus', 0)
                    ->select('tsd.name AS tsdname', 'ts.production_order', 'pro.production_item', 'pro.item_name', 'tsd.completed_qty', 'tsd.status', 'pro.priority', 'tsd.operator_name', 'tsd.reject', 'tsd.good', 'tsd.rework','tsd.qty_accepted','tsd.machine','tsd.qa_inspection_status','tsd.quality_inspected_by','tsd.from_time','tsd.to_time', 'tsd.hours','tsd.rejection_type')
                    ->whereDate('ts.creation', '>', '2019-10-11')
                    ->orderBy('ts.creation', 'asc')
                    ->get();

            $details[]=[
                "workstation" => $row->workstation,
                "details" => $jt_details,
            ];
        }
        return $details;
    }

    public function tbl_productionSchedule_report(Request $request){
        try {
            $production_orders = DB::connection('mysql')->table('tabWork Order as tpo')
            ->where('tpo.docstatus', 1)
            ->where('tpo.planned_start_date', $request->date)
            ->get();

            $schedule = [];
            foreach ($production_orders as $row) {
                $timesheet = DB::connection('mysql')->table('tabTimesheet')
                    ->where('production_order', $row->name)->first();
                $batch=DB::connection('mysql')->table('tabWork Batch Table as tpbt')
                ->where('tpbt.production_order', $row->name)->select('parent','idx')->first();
                // $last_process =DB::connection('mysql')->table('tabWork Order Operation')
                //     ->where('parent', $row->name)->orderBy('idx', 'desc')->first();

                // $completed_qty = ($last_process) ? $last_process->completed_qty : 0;

                // $status = ($completed_qty > $row->produced_qty) ? 'Completed' : 'Partialy completed';
                    if ($this->timesheet_details_report(($timesheet) ? $timesheet->name :null) != null) {
                      $schedule[] = [
                            'name' => $row->name,
                            'customer' => ($timesheet) ? (($timesheet->customer) ? $timesheet->customer : 'NONE') : 'NONE',
                            'item_code' => $row->production_item,
                            // 'description' => $row->description,
                            'qty' => $row->qty,
                            // 'produced_qty' => $row->produced_qty,
                            // 'stock_uom' => $row->stock_uom,
                            'planned_end_date' => $row->planned_end_date,
                            'delivery_date' => ($timesheet) ? $timesheet->delivery_date :null,
                            // 'completed_qty' => $completed_qty,
                            // 'duration' => ($timesheet) ? $timesheet->total_hours : 0,
                            // 'status' => $status,
                            // 'last_process' => $last_process,
                            'jt' =>($timesheet) ? $timesheet->name :null,
                            'jt_details' => $this->timesheet_details_report(($timesheet) ? $timesheet->name :null),
                            // 'batchno' => $batch


                        ];
                    }       
            }
            // dd($schedule);
            return view('reports.tbl_production_schedule_report', compact('schedule'));
            // return $schedule;
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function productioncShedule_report(){
        return view('reports.production_schedule_report');
    }

    public function line_workstation($workstation, $schedule, $workstation_id){
        $line= DB::connection('mysql_mes')->table('job_ticket as p')->where('p.workstation', $workstation)->where('p.process_id',"!=", null)->groupBy('p.process_id')->select('p.process_id')->get();
        $work=[];
        foreach ($line as $row) {
            $process_name = $this->process_name_function($row->process_id);
            $process_load = $this->process_load($workstation, $schedule, $row->process_id, $process_name, $workstation_id);
            $process_name1 = DB::connection('mysql_mes')->table('process')->where('process_id', $row->process_id)->first()->process_name;

            $work[]=[
                'process_id'=> $row->process_id,
                'process'=> $process_name1,
                'load'=> $process_load,
                'order_no'=> $row->process_id,
            ];
        }
        return $work;
    }

    public function process_name_function($id){
        $process_name= DB::connection('mysql_mes')->table('process as p')->where('p.process_id', $id)->select('process_id')->first();
        return $process_name->process_id;
    }

    public function machineKanban($workstation, $schedule_date){
        $current_workstation_details = DB::connection('mysql_mes')->table('workstation')
            ->where('workstation_id', $workstation)->first();

        $workstation_list = DB::connection('mysql_mes')
            ->table('workstation as w')
            ->join('operation as op', 'op.operation_id', 'w.operation_id')
            ->where('op.operation_name','Fabrication')
            ->select('workstation_name','order_no','workstation_id')
            ->orderBy('order_no','asc')->get();

        $shift_sched = $this->get_prod_shift_sched($schedule_date);

        return view('machine_kanban', compact('schedule_date', 'current_workstation_details', 'workstation_list', 'shift_sched'));    
    }

    public function get_prod_shift_sched($date){
            $scheduled = [];
            if (DB::connection('mysql_mes')
            ->table('shift_schedule')
            ->where('date', $date)
            ->exists()){
                // $scheduled1="hi";
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

    public function getScheduledProdOrders($workstation, $schedule_date, $process){
        $now = Carbon::now();
        $current_date = $now->toDateString();
        if ($schedule_date <= $current_date) {
           $orders = DB::connection('mysql_mes')->table('production_order as prod')
            ->join('job_ticket as tsd','tsd.production_order','=','prod.production_order')
            ->where('tsd.process', $process)
            ->where('tsd.workstation', $workstation)
            ->where('prod.status', 'Not Started')
            ->where('prod.is_scheduled' , 1)
            ->whereNull('tsd.machine')
            ->whereDate('prod.planned_start_date',"<=", $schedule_date)
            ->select('tsd.status', 'prod.customer','prod.production_order','prod.item_code','prod.description','prod.qty_to_manufacture','prod.stock_uom','prod.produced_qty', 'prod.classification', 'tsd.workstation as workstation_plot','tsd.machine','tsd.id as jtname','prod.sales_order','tsd.good as good_qty','prod.order_no', 'tsd.process','prod.delivery_date','prod.planned_start_date','tsd.operator_name', 'tsd.from_time', 'tsd.to_time', 'tsd.hours', 'tsd.qa_inspection_status', 'tsd.qa_inspection_date', 'tsd.qa_staff_name', 'tsd.item_feedback', 'tsd.completed_qty', 'tsd.machine_name', 'tsd.qc_type', 'prod.material_request')
            ->orderBy('prod.order_no','asc')
            ->get(); 
        }else{
            $orders = DB::connection('mysql_mes')->table('production_order as prod')
            ->join('job_ticket as tsd','tsd.production_order','=','prod.production_order')
            ->where('tsd.process', $process)
            ->where('tsd.workstation', $workstation)
            ->where('prod.status', 'Not Started')
            ->where('prod.is_scheduled' , 1)
            ->whereNull('tsd.machine')
            ->whereDate('prod.planned_start_date', $schedule_date)
            ->select('tsd.status', 'prod.customer','prod.production_order','prod.item_code','prod.description','prod.qty_to_manufacture','prod.stock_uom','prod.produced_qty', 'prod.classification', 'tsd.workstation as workstation_plot','tsd.machine','tsd.id as jtname','prod.sales_order','tsd.good as good_qty','prod.order_no', 'tsd.process','prod.delivery_date','prod.planned_start_date','tsd.operator_name', 'tsd.from_time', 'tsd.to_time', 'tsd.hours', 'tsd.qa_inspection_status', 'tsd.qa_inspection_date', 'tsd.qa_staff_name', 'tsd.item_feedback', 'tsd.completed_qty', 'tsd.machine_name', 'tsd.qc_type', 'prod.material_request')
            ->orderBy('prod.order_no','asc')
            ->get();
        }

        $scheduled = [];
        foreach($orders as $row){
            $status = $this->prodJtStatus($row->production_order);
            $process_name = DB::connection('mysql_mes')->table('process')->where('id', $row->process)->first()->process;
            $scheduled[] = [
                'name' => $row->production_order,
                'status' => $row->status,
                'order_no' => $row->order_no,
                'customer' => $row->customer,
                'production_item' => $row->item_code,
                'description' => $row->description,
                'qty' => $row->qty_to_manufacture,
                'stock_uom' => $row->stock_uom,
                'produced_qty' => $row->qty_to_manufacture,
                'classification' => $row->classification,
                'workstation_plot' => $row->workstation_plot,
                'machine' => $row->machine,
                'jtname' => $row->jtname,
                'sales_order'=>($row->sales_order == null) ? $row->material_request : $row->sales_order,
                'process' => $process_name,
                'delivery_date' => $row->delivery_date,
                'planned_start_date' => $row->planned_start_date,
                'completed_qty' => $row->good_qty,
                'operator_name' => $row->operator_name,
                'to_time' => $row->from_time,
                'from_time' => $row->to_time,
                'hours' => $row->hours,
                'qa_inspection_status' => $row->qa_inspection_status,
                'qa_inspection_date' => $row->qa_inspection_date,
                'qa_inspected_by' => $row->qa_staff_name,
                'item_feedback' =>$row->item_feedback,
                'machine_name' => $row->machine_name,
                'qc_type' => $row->qc_type


            ];
        }

        return $scheduled;
    }

    public function process_load($workstation, $schedule_date, $id, $process, $workstation_id){
        $now = Carbon::now();
        $current_date = $now->toDateString();
        $machine= DB::connection('mysql_mes')->table('process_assignment AS pm')->join('machine AS m', 'm.machine_id','pm.machine_id')
        ->where('pm.process_id', $id)->where('pm.workstation_id', $workstation_id)->select('pm.*','m.machine_name','m.machine_code')->orderBy('m.machine_code', 'asc')->get();
        $process_load=[];
        foreach ($machine as $rows) {

      if ($schedule_date <= $current_date){

          $late_completed_query= DB::connection('mysql_mes')->table('job_ticket as jt')
          ->leftJoin('time_logs as tl', 'jt.job_ticket_id', 'tl.job_ticket_id')
          ->join('production_order as prod', 'prod.production_order', 'jt.production_order')
          ->join('process as p', 'p.process_id', 'jt.process_id')
          ->whereDate('jt.last_modified_at', $current_date)
          ->where('jt.status', 'Completed')
          ->where('jt.workstation', $workstation)
          ->where('jt.process_id', $id)
          ->select('prod.*', 'jt.workstation as workstation_plot','tl.machine_code as machine','jt.job_ticket_id as jtname', 'p.process_name',"jt.status as stat",'tl.good as good_qty', 'tl.operator_name', 'tl.from_time', 'tl.to_time', 'tl.duration', 'tl.time_log_id', 'tl.machine_name', 'jt.remarks');

          $completed_query= DB::connection('mysql_mes')->table('job_ticket as jt')
          ->leftJoin('time_logs as tl', 'jt.job_ticket_id', 'tl.job_ticket_id')
          ->join('production_order as prod', 'prod.production_order', 'jt.production_order')
          ->join('process as p', 'p.process_id', 'jt.process_id')
          ->whereDate('jt.planned_start_date', $schedule_date)
        //   ->where('prod.status', '!=', 'Cancelled')
          ->whereNotIn('prod.status', ['Cancelled', 'Closed'])
          ->where('jt.status', 'Completed')
          ->where('prod.is_scheduled' , 1)
          ->where('jt.workstation', $workstation)
          ->where('jt.process_id', $id)
          ->select('prod.*', 'jt.workstation as workstation_plot','tl.machine_code as machine','jt.job_ticket_id as jtname', 'p.process_name',"tl.status as stat",'tl.good as good_qty', 'tl.operator_name', 'tl.from_time', 'tl.to_time', 'tl.duration', 'tl.time_log_id', 'tl.machine_name', 'jt.remarks');

          $orders= DB::connection('mysql_mes')->table('job_ticket as jt')
          ->leftJoin('time_logs as tl', 'jt.job_ticket_id', 'tl.job_ticket_id')
                ->join('production_order as prod', 'prod.production_order', 'jt.production_order')
                ->join('process as p', 'p.process_id', 'jt.process_id')
                ->whereDate('jt.planned_start_date','<=', $schedule_date)
                // ->where('jt.status', 'Pending')
                ->whereNotIn('jt.status', ['', 'Completed'])
                ->where('prod.is_scheduled' , 1)
                ->where('jt.workstation', $workstation)
                ->where('jt.process_id', $id)
                ->union($completed_query)
                ->union($late_completed_query)
                ->select('prod.*', 'jt.workstation as workstation_plot','tl.machine_code as machine','jt.job_ticket_id as jtname', 'p.process_name',"jt.status as stat",'tl.good as good_qty', 'tl.operator_name', 'tl.from_time', 'tl.to_time', 'tl.duration', 'tl.time_log_id','tl.machine_name', 'jt.remarks')
                ->get();
        //   dd($orders);
          
          }else{
              $orders= DB::connection('mysql_mes')->table('job_ticket as jt')
              ->join('production_order as prod', 'prod.production_order', 'jt.production_order')
              ->join('process as p', 'p.process_id', 'jt.process_id')
              ->whereDate('jt.planned_start_date', $schedule_date)
              ->whereIn('jt.status', ['Pending', 'In Progress'])
            //   ->where('prod.status', '!=', 'Cancelled')
              ->whereNotIn('prod.status', ['Cancelled', 'Closed'])
              ->where('prod.is_scheduled' , 1)
              ->where('jt.workstation', $workstation)
              ->where('jt.process_id', $id)
              ->select('prod.*', 'jt.workstation as workstation_plot', DB::raw('(SELECT machine_code FROM time_logs WHERE job_ticket_id = jt.job_ticket_id) AS machine'), DB::raw('(SELECT job_ticket_id FROM time_logs WHERE job_ticket_id = jt.job_ticket_id) AS jtname'),'p.process_name', "jt.status as stat", DB::raw('(SELECT good FROM time_logs WHERE job_ticket_id = jt.job_ticket_id) AS good_qty'), DB::raw('(SELECT operator_name FROM time_logs WHERE job_ticket_id = jt.job_ticket_id) AS operator_name'), DB::raw('(SELECT from_time FROM time_logs WHERE job_ticket_id = jt.job_ticket_id) AS from_time'),DB::raw('(SELECT to_time FROM time_logs WHERE job_ticket_id = jt.job_ticket_id) AS to_time'), DB::raw('(SELECT duration FROM time_logs WHERE job_ticket_id = jt.job_ticket_id) AS duration'),  DB::raw('(SELECT time_log_id FROM time_logs WHERE job_ticket_id = jt.job_ticket_id) AS time_log_id'), DB::raw('(SELECT machine_name FROM time_logs WHERE job_ticket_id = jt.job_ticket_id) AS machine_name'), 'jt.remarks')
              ->get();
          }
        }

        $process_load[]=[
              'load_count' => count($orders),
              'total_qty' => collect($orders)->sum('qty_to_manufacture'),
              'load' => $orders
              
          ];
      return $process_load;
    }

    public function qa_details($timelog_id){
        $details = DB::connection('mysql_mes')->table('job_ticket')
            ->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
            ->where('time_logs.time_log_id', $timelog_id)->first();

        $reference_type = ($details->workstation != 'Spotwelding') ? 'Time Logs' : 'Spotwelding';
        $reference_id = ($reference_type == 'Spotwelding') ? $details->job_ticket_id : $timelog_id;

        $qa_table= DB::connection('mysql_mes')->table('quality_inspection')
            ->where('reference_type', $reference_type)->where('reference_id', $reference_id)
            ->orderBy('last_modified_at', 'desc')->get();

      return response()->json([
            'qa_tables' => $qa_table
          ]);
    }

    public function prodJtStatus($prodno){
        $req = DB::connection('mysql_mes')->table('production_order')->where('production_order', $prodno)->first()->qty_to_manufacture;
        $prod = DB::connection('mysql_mes')->table('job_ticket as t')->where('t.production_order', $prodno)->get();

        $qty_done = collect($prod)->sum('completed_qty');

        foreach ($prod as $row) {
            if (in_array($row->status, ['Accepted', 'In Progress'])) {
                return 'In Process';
            }
        }

        if ($qty_done > 0 && $qty_done < $req) {
            return 'In Process';
        }

        if ($qty_done == $req) {
            return 'Completed';
        }

        return 'Not Started';
    }

    public function machine_kanban_workstation($workstation, $schedule){

        $unscheduled = DB::connection('mysql')->table('tabWork Order as prod')
            ->join('tabTimesheet as ts','ts.production_order','=','prod.name')
            ->join('tabTimesheet Detail as tsd','tsd.parent', '=','ts.name')
            ->where('tsd.workstation', $workstation)
            ->where('prod.docstatus', 1)
            ->whereNotIn('prod.status', ['Completed', 'Stopped'])
            ->where('prod.scheduled' , 0)
            ->whereDate('prod.creation', '>', '2019-10-11')
            ->select('prod.*', 'tsd.workstation as workstation_plot')
            ->orderBy('prod.name', 'asc')
            ->get();

        
        $orders = $this->getScheduledProdOrders($workstation, $schedule);
        // dd($orders);
        $production_line= DB::connection('mysql')->table('tabWorkstation')->where('workstation_name', $workstation)->get();
        // dd($machine_load);
        $work=[];
        foreach ($production_line as $row) {
            $machine= DB::connection('mysql')->table('tabWorkstation Machine')->where('parent', $row->workstation_name)->orderBy('machine_code', 'asc')->get();
            // $machine_load = $this->machine_load($row->workstation_name, $schedule);
            $work[]=[
                'workstation'=> $row->workstation_name,
                'machine' => $this->machine_load($row->workstation_name, $schedule)
            ];
        }
        // dd($work);
        return view('tables.tbl_machine_kanban_workstation', compact('unscheduled','production_line','work', 'machine','orders'));
        
    }

    public function workstation_line($workstation_name){
         $machine= DB::connection('mysql')->table('tabWorkstation Machine')->where('parent', $workstation_name)->orderBy('machine_code', 'asc')->get();
         return $machine;
    }

    public function machinename($schedule){
        $machine_namee=DB::connection('mysql')->table('tabMachine')->where("name",$schedule)->select('machine_name')->first();
        return $machine_namee;
    }

    public function reorderProdOrder(Request $request){
        try {
            $val = [];
            if ($request->positions) {
                foreach ($request->positions as $value) {
                    $jt_no = $value[0];
                    $process_id = $value[1];
                    

                        $val = [
                        'process_id' => $process_id
                        
                        ];
                     
                    DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $jt_no)->update($val); 
                }
            }
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }   
    }

    public function continue_process($prod_name, $date){
            $prod_qty=DB::connection('mysql')->table('tabWork Order')->where('name', $prod_name)->select('qty','planned_start_date')->first();
            $workstations = DB::connection('mysql')->table('tabTimesheet as t')->join('tabTimesheet Detail as td', 't.name', 'td.parent')->where('t.production_order', $prod_name)->get();

            $qtys = collect($workstations)->where('workstation',$row->workstation)->max("completed_qty");
            $reject = collect($workstations)->where('workstation',$row->workstation)->sum("reject");
            $good = collect($workstations)->where('workstation',$row->workstation)->sum("good");
            $rework = collect($workstations)->where('workstation',$row->workstation)->sum("rework");
            $operator = DB::connection('mysql')->table('tabTimesheet as t')->join('tabTimesheet Detail as td', 't.name', 'td.parent')->where('t.production_order', $prod_name)->where('td.workstation',$row->workstation)->orderBy('td.idx','DESC')->first();

    }

    public function goto_machine_list(){
        $machine_list= DB::connection('mysql_mes')
                ->table('machine')
                ->paginate(10);
        $machine_process= DB::connection('mysql_mes')
                ->table('machine')
                ->paginate(10);
        return view('machine_list', compact('machine_list'));
    }

    // /save_machine
    public function insert_machine(Request $request){
        if (DB::connection('mysql_mes')
        ->table('machine')
        ->where('machine_code', $request->machine_code)
        ->exists()){

        }else{

            $requestko = $request->all();

            if($request->hasFile('machineImage')){
                $file = $request->file('machineImage');

                //get filename with extension
                $filenamewithextension = $file->getClientOriginalName();
                //get filename without extension
                $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                //get file extension
                $extension = $file->getClientOriginalExtension();
                //filename to store
                $filenametostore = $request->machine_code.'.'.$extension;
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
            else{
                $image_path = null;
            }
            // dd($requestko);

            $machine = new Machine;
            $machine->reference_key = $request->machine_id;
            $machine->machine_code = $request->machine_code;
            $machine->machine_name  = $request->machine_name;
            $machine->status = $request->status;
            $machine->type = $request->type;
            $machine->model = $request->model;
            $machine->image = $image_path;
            $machine->created_by = Auth::user()->employee_name;
            $machine->created_at = null;
            $machine->last_modified_by = Auth::user()->employee_name;
            $machine->last_modified_at = null;
            $machine->save();
        }

        if($request->ajax()){
            return response()->json(['success' => 1, 'message' => 'Machine Successfully Added']);
        }else{
            return redirect()->back()->with('success', 'Machine Successfully Added');
        }

    }
    public function update_machine(Request $request){
        // $requestko = $request->all();
        // dd($requestko);
        if (DB::connection('mysql_mes')
        ->table('machine')
        ->where('machine_code', $request->machine_code)
        ->exists()){
            if($request->origmachine_code == $request->machine_code){
                if($request->hasFile('machineImage')){
                    $file = $request->file('machineImage');
                    // dd($file);
                    //get filename with extension
                    $filenamewithextension = $file->getClientOriginalName();
                    //get filename without extension
                    $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
                    //get file extension
                    $extension = $file->getClientOriginalExtension();
                    //filename to store
                    $filenametostore = $request->machine_code.'.'.$extension;
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
                else{
                    $image_path = $request->origimage;
                    
                }
                $machine = Machine::find($request->machineID);
                    $machine->reference_key = $request->machine_id;
                    $machine->machine_code = $request->machine_code;
                    $machine->machine_name  = $request->machine_name;
                    $machine->status = $request->status;
                    $machine->type = $request->type;
                    $machine->model = $request->model;
                    $machine->image = $image_path;
                    $machine->created_by = Auth::user()->employee_name;
                    $machine->last_modified_by = Auth::user()->employee_name;
                    $machine->process = $request->process;
                    $machine->save();
                    return response()->json(['success' => 1, 'message' => 'Machine Successfully Updated-hasfile']);
        
            }else{
                return response()->json(['success' => 0, 'message' => 'Machine already exist!']);

            }

        }else{
        if($request->hasFile('machineImage')){
            $file = $request->file('machineImage');
            // dd($file);
            //get filename with extension
            $filenamewithextension = $file->getClientOriginalName();
            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
            //get file extension
            $extension = $file->getClientOriginalExtension();
            //filename to store
            $filenametostore = $request->machine_code.'.'.$extension;
            // Storage::put('public/employees/'. $filenametostore, fopen($file, 'r+'));
            Storage::put('public/machine/'. $filenametostore, fopen($file, 'r+'));
            //Resize image here
            $thumbnailpath = public_path('storage/machine/'.$filenametostore);
            $img = Image::make($thumbnailpath)->resize(500, 350, function($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($thumbnailpath);

            $image_path = '/storage/machine/'.$filenametostore;
            $machine = Machine::find($request->machineID);
            $machine->reference_key = $request->machine_id;
            $machine->machine_code = $request->machine_code;
            $machine->machine_name  = $request->machine_name;
            $machine->status = $request->status;
            $machine->type = $request->type;
            $machine->model = $request->model;
            $machine->image = $image_path;
            $machine->created_by = Auth::user()->employee_name;
            $machine->last_modified_by = Auth::user()->employee_name;
            $machine->process = $request->process;
            $machine->save();
            return response()->json(['success' => 1, 'message' => 'Machine Successfully Updated-hasfile']);

        }
        else{
            $image_path = $request->origimage;
            $machine = Machine::find($request->machineID);
        $machine->reference_key = $request->machine_id;
        $machine->machine_code = $request->machine_code;
        $machine->machine_name  = $request->machine_name;
        $machine->status = $request->status;
        $machine->type = $request->type;
        $machine->model = $request->model;
        $machine->image = $image_path;
        $machine->created_by = Auth::user()->employee_name;
        $machine->last_modified_by = Auth::user()->employee_name;
        $machine->process = $request->process;
        $machine->save();
        return response()->json(['success' => 1, 'message' => 'Machine Successfully Updated-default']);

        }

        
    }

    }
    public function get_machine_list_data(Request $request){
        $machine_list= DB::connection('mysql_mes')->table('machine')
                ->where('machine_id', $request->id)->first();

        return view('tables.tbl_machine_list', compact('machine_list'));
    }

    public function get_machine_profile($id){
        $machine_list = DB::connection('mysql_mes')->table('machine')->where('machine_id', $id)->first();

        $process_list = DB::connection('mysql_mes')->table('process_assignment AS pm')
            ->join('process AS p', 'p.process_id', 'pm.process_id')->where('pm.machine_id', $machine_list->machine_id)
            ->select('pm.*', 'p.process_name')->get();

        $permissions = $this->get_user_permitted_operation();

        return view('machine_profile', compact('machine_list', 'process_list', 'permissions'));
    }
    public function delete_machine(Request $request){
        $itemissued = Machine::find($request->machine_id);
        $itemissued->delete();
        
        return response()->json(['success' => 1, 'message' => 'Machine successfully deleted']);
        // return redirect()->back()->with(['message' => 'Item code - <b>' . $itemissued->item_code . '</b>  has been successfully deleted!']);
    }
    public function get_workstation_list(){
        $list=  DB::connection('mysql_mes')->table('workstation')
                ->paginate(10);

        $operation = DB::connection('mysql')->table('tabOperation as top')
        ->get();
        return view('workstation_list', compact('list','operation'));

    }
    public function machine_profile_tbl(Request $request){
        $breakdown= DB::connection('mysql_mes')
                ->table('machine_breakdown')
                ->where('machine_id', $request->id)
                ->get(); 
        $data=[];
        foreach ($breakdown as $row) {
            if (empty($row->date_resolved)) {
                $duration="";
            }else{
                $start = Carbon::parse($row->date_reported);
                $end = Carbon::parse($row->date_resolved);
                $seconds = $end->diffInSeconds($start);
                $duration =$this->seconds2human($seconds);

            }
            
            $data[]=[
                'machine_code' => $row->machine_id,
                'assigned_maintenance_staff' => $row->assigned_maintenance_staff,
                'reported_by' => $row->reported_by,
                'type' => $row->type,
                'breakdown_reason' => $row->breakdown_reason,
                'corrective_reason' => $row->corrective_reason,
                'remarks' => $row->remarks,
                'work_done' => $row->work_done,
                'id' => $row->machine_breakdown_id,
                'series' => $row->machine_breakdown_id,
                'reason' => ($row->category == "Breakdown") ? $row->breakdown_reason : $row->corrective_reason,
                'status' => $row->status,
                'category' => $row->category,
                'date_reported' => $row->date_reported,
                'duration' => $duration

            
            ];
        }
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
     
        // Create a new Laravel collection from the array data
        $itemCollection = collect($data);
     
        // Define how many items we want to be visible in each page
        $perPage = 10;
     
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
     
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
     
        // set url path for generted links
        $paginatedItems->setPath($request->url());

        $data = $paginatedItems;
        return view('tables.tbl_machine_profile', compact('data'));

    }
    public function workstation_profile($id){
        $permissions = $this->get_user_permitted_operation();
        $list= DB::connection('mysql_mes')
                ->table('workstation as w')
                ->join('operation as op','op.operation_id', 'w.operation_id')
                ->where('w.workstation_id', $id)
                ->select("w.*",'op.operation_name as operation')
                ->first();
        $machine= DB::connection('mysql_mes')
                ->table('machine')
                ->get();
        $process_list = DB::connection('mysql_mes')
                ->table('process')
                ->orderBy('process_name', 'Asc')
                ->get();

                return view('workstation_profile', compact('list','machine','process_list', 'permissions'));

    }
    public function getNextIdxMachineWorkstation($id)
        {
            $lastOrder = DB::connection('mysql_mes')->table('workstation_machine')
            ->where('workstation', $id)
            ->orderBy('created_at', 'desc')
            ->select('idx')
            ->first();

            if (empty($lastOrder->idx)){
                $number = 0;
            }else{
                $number = $lastOrder->idx;
            }
            return ($number) + 1;
        }

    public function insert_machineToworkstation(Request $request){
        $machine_nametbl = DB::connection('mysql_mes')->table('machine')->where('machine_id', $request->machine_id)->select('machine_name', 'machine_code')->first();
        $now = Carbon::now();
            $values1 = [
                'name' => uniqid(),
                'creation' => $now->toDateTimeString(),
                'modified' => $now->toDateTimeString(),
                'modified_by' => Auth::user()->email,
                'owner' => Auth::user()->email,
                'docstatus' => 0,
                'parent' => $request->workstation_name,
                'parentfield' => 'workstation_machine',
                'parenttype' => 'Workstation',
                'idx' => $this->getNextIdxMachineWorkstation($request->workstation_name),
                'machine_name' => $machine_nametbl->machine_name,
                'machine_code' => $machine_nametbl->machine_code,
            ];
            $values2 = [
                    'workstation' => $request->workstation_name,
                    'workstation_id' => $request->workstation_id,
                    'machine_id' => $request->machine_id,
                    'idx' => $this->getNextIdxMachineWorkstation($request->workstation_name),
                    'machine_name' =>  $machine_nametbl->machine_name,
                    'machine_code' => $machine_nametbl->machine_code,
                    'created_at' => $now->toDateTimeString(),
                    'created_by' => Auth::user()->email,
                    'last_modified_by' => Auth::user()->email

                    
                ];

            // DB::connection('mysql')->table('tabWorkstation Machine')->insert($values1);
            DB::connection('mysql_mes')->table('workstation_machine')->insert($values2);


        return redirect()->back();

    }
    public function delete_machineToworkstation(Request $request){
        // $me=$request->all();
        // dd($me);
        // DB::connection('mysql')->table('tabWorkstation Machine')->where('parent', $request->workstation)->where('machine_code', $request->machine_code)->delete();
        DB::connection('mysql_mes')->table('workstation_machine')->where("workstation_machine_id", $request->machine_id)->delete();
        return redirect()->back();
        // return redirect()->back()->with(['message' => 'Item code - <b>' . $itemissued->item_code . '</b>  has been successfully deleted!']);
    }
    public function get_machine_to_select(){
        $machine= DB::connection('mysql_mes')
                ->table('machine')
                ->get();
        return response()->json($details);

    }
    public function save_workstation(Request $request){
                // $machine_nametbl = DB::connection('mysql_mes')->table('machine')->where('id', $request->machine_id)->select('machine_name', 'machine_code')->first();
            // dd($request->all());
         $now = Carbon::now();
            if (DB::connection('mysql')->table('tabWorkstation')
                ->where('name', '=', $request->workstation_name)
                ->exists()){
            }else{
                $values2 = [
                    'name' => $request->workstation_name,
                    'creation' => $now->toDateTimeString(),
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->email,
                    'owner' => Auth::user()->email,
                    'docstatus' => 0,
                    'parent' => null,
                    'parentfield' => null,
                    'parenttype' => null,
                    'hour_rate_rent' => 0,
                    'hour_rate' => 0,
                    'hour_rate_consumable' => 0,
                    'hour_rate_electricity' => 0,
                    'idx' => 0,
                    'order_no' => $request->order_no,
                    'operation' => $request->operation,
                    'workstation_name' => $request->workstation_name
                ];
                DB::connection('mysql')->table('tabWorkstation')->insert($values2);
            }

            if (DB::connection('mysql_mes')->table('workstation')
                ->where('workstation_name', '=', $request->workstation_name)
                ->where('operation_id', $request->operation)
                ->exists()){
                return response()->json(['success' => 0, 'message' => 'Workstation - <b>' . $request->workstation_name . '</b> already exists']);            
            }
            else{
           
                $values1 = [
                    'idx' => 0,
                    'order_no' => $request->order_no,
                    'operation_id' => $request->operation,
                    // 'modified' => $now->toDateTimeString(),
                    'last_modified_by' => Auth::user()->email,
                    'created_by' => Auth::user()->email,
                    'created_at' => $now->toDateTimeString(),
                    'workstation_name' => $request->workstation_name

                    
                ];
                
            DB::connection('mysql_mes')->table('workstation')->insert($values1);

            return response()->json(['success' => 1, 'message' => 'Workstation successfully Added.']);
            }

    }
    public function edit_workstation(Request $request){
            $operation_name=  DB::connection('mysql_mes')->table('operation')->where('operation_id', $request->operation)->select('operation_name')->first(); 
        // $machine_nametbl = DB::connection('mysql_mes')->table('machine')->where('id', $request->machine_id)->select('machine_name', 'machine_code')->first();
            if (DB::connection('mysql')->table('tabWorkstation')
                ->where('name', '=', $request->workstation_name)
                ->exists()){
            }else{
            $now = Carbon::now();
                $values1 = [
                    'idx' => 0,
                    'order_no' => $request->order_no,
                    'operation_id' => $request->operation,
                    // 'modified' => $now->toDateTimeString(),
                    'last_modified_by' => Auth::user()->email,
                    'created_by' => Auth::user()->email,
                    'workstation_name' => $request->workstation_name

                    
                ];
                $values2 = [
                    'name' => $request->workstation_name,
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->email,
                    'docstatus' => 0,
                    'parent' => null,
                    'parentfield' => null,
                    'parenttype' => null,
                    'hour_rate_rent' => 0,
                    'hour_rate' => 0,
                    'hour_rate_consumable' => 0,
                    'hour_rate_electricity' => 0,
                    'idx' => 0,
                    'order_no' => $request->order_no,
                    'operation' => $operation_name->operation_name,
                    'workstation_name' => $request->workstation_name
                ];
            DB::connection('mysql')->table('tabWorkstation')->where("name", $request->workstation_orig)->update($values2);

            }

            if (DB::connection('mysql_mes')->table('workstation')
                ->where('workstation_name', '=', $request->workstation_name)
                ->where('operation_id', $request->operation)
                ->exists()){
                    if(strtoupper($request->workstation_orig) == strtoupper($request->workstation_name) && $request->operation_orig == $request->operation){
                        $now = Carbon::now();
                            $values1 = [
                                'idx' => 0,
                                'order_no' => $request->order_no,
                                'operation_id' => $request->operation,
                                // 'modified' => $now->toDateTimeString(),
                                'last_modified_by' => Auth::user()->email,
                                'created_by' => Auth::user()->email,
                                'workstation_name' => $request->workstation_name
                                
                            ];
                        DB::connection('mysql_mes')->table('workstation')->where("workstation_id", $request->workstation_id)->update($values1);

                        return response()->json(['success' => 1, 'message' => 'Workstation successfully Updated.']);

                    }else{
                        return response()->json(['success' => 0, 'message' => 'Workstation - <b>' . $request->workstation_name . '</b> already exists']);            

                    }
            }
            else{
            $now = Carbon::now();
                $values1 = [
                    'idx' => 0,
                    'order_no' => $request->order_no,
                    'operation_id' => $request->operation,
                    // 'modified' => $now->toDateTimeString(),
                    'last_modified_by' => Auth::user()->email,
                    'created_by' => Auth::user()->email,
                    'workstation_name' => $request->workstation_name

                    
                ];
            DB::connection('mysql_mes')->table('workstation')->where("workstation_id", $request->workstation_id)->update($values1);

            return response()->json(['success' => 1, 'message' => 'Workstation successfully Updated.']);

        }
    }
    public function delete_workstation(Request $request){
                // $machine_nametbl = DB::connection('mysql_mes')->table('machine')->where('id', $request->machine_id)->select('machine_name', 'machine_code')->first();

            DB::connection('mysql_mes')->table('workstation')->where("workstation_id", $request->workstation_id)->delete();
            DB::connection('mysql')->table('tabWorkstation')->where("name", $request->workstation_name)->delete();

            return response()->json(['success' => 1, 'message' => 'Workstation successfully Deleted.']);

    }
    public function getNextIdxProcessWorkstation($id)
        {
            $lastOrder = DB::connection('mysql_mes')->table('workstation_process')
            ->where('workstation_id', $id)
            ->orderBy('created_at', 'desc')
            ->select('idx')
            ->first();

            if (empty($lastOrder->idx)){
                $number = 0;
            }else{
                $number = $lastOrder->idx;
            }
            return $number + 1;
        }
        public function getNextIdxProcessWorkstation_erp($id)
        {
            $lastOrder = DB::connection('mysql')->table('tabWorkstation Process')
            ->where('parent', $id)
            ->orderBy('creation', 'desc')
            ->select('idx')
            ->first();

            if (empty($lastOrder->idx)){
                $number = 0;
            }else{
                $number = $lastOrder->idx;
            }
            return $number + 1;
        }
    public function save_process_workstation(Request $request){
            $workstation_name=DB::connection('mysql_mes')->table('workstation')->where('id', $request->workstation_id)->select('workstation_name')->first();
            $process=DB::connection('mysql_mes')->table('process')->where('id', $request->process_id)->select('process')->first();
            $now = Carbon::now();
                $values1 = [
                    'process_id' => $request->process_id,
                    'idx' => $this->getNextIdxProcessWorkstation($request->workstation_id),
                    'workstation_id' => $request->workstation_id,
                    'last_modified_by' => Auth::user()->email,
                    'created_by' => Auth::user()->email,
                    'created_at' => $now->toDateTimeString()
                ];
                $values2 = [
                    'name' => uniqid(),
                    'creation' => $now->toDateTimeString(),
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->email,
                    'owner' => Auth::user()->email,
                    'docstatus' => 0,
                    'parent' => $workstation_name->workstation_name,
                    'parentfield' => "workstation_process",
                    'parenttype' => "Workstation",
                    'process' => $process->process,
                    'idx' => $this->getNextIdxProcessWorkstation_erp($workstation_name->workstation_name),
                    'remarks' => $request->remarks
                ];
            DB::connection('mysql_mes')->table('workstation_process')->insert($values1);
            DB::connection('mysql')->table('tabWorkstation Process')->insert($values2);

            // return response()->json(['success' => 1, 'message' => 'Workstation successfully Added.']);
        return redirect()->back();

        }
    public function get_tbl_workstation_process(Request $request){
        $data=[];
        $process_list= DB::connection('mysql_mes')
        ->table('process_assignment')
        ->where('workstation_id', $request->workstation)
        ->select('process_id')
        ->groupBy('process_id')
        ->get();
        foreach ($process_list as $row) {
            $process_list1= DB::connection('mysql_mes')
            ->table('process')
            ->where('process_id', $row->process_id)
            ->first();
            $data[]=[
                'id' =>  $row->process_id,
                'process'=> $process_list1->process_name
            ];
        }


        // dd($data);
                // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
     
        // Create a new Laravel collection from the array data
        $itemCollection = collect($data);
     
        // Define how many items we want to be visible in each page
        $perPage = 10;
     
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
     
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
     
        // set url path for generted links
        $paginatedItems->setPath($request->url());

        $data = $paginatedItems;
        // dd($process_list);
        return view('tables.tbl_process_workstation_list', compact('data'));

    }
    public function get_tbl_workstation_machine(Request $request){
      
        $machine_list= DB::connection('mysql_mes')->table('workstation_machine')
        ->where('workstation', $request->workstation)
        ->orderBy('idx')
        ->get();

                // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
     
        // Create a new Laravel collection from the array data
        $itemCollection = collect($machine_list);
     
        // Define how many items we want to be visible in each page
        $perPage = 5;
     
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
     
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
     
        // set url path for generted links
        $paginatedItems->setPath($request->url());

        $data = $paginatedItems;
        // dd($process_list);
        return view('tables.tbl_machine_workstation_list', compact('data'));

    }
    public function delete_process_workstation(Request $request){
       
        DB::connection('mysql_mes')->table('process_assignment')->where("process_assignment_id", $request->delete_id)->delete();
        return response()->json(['success' => 1, 'message' => 'Workstation - Machine  successfully deleted!']);

    }
    public function getNextIdxMachineProcess($id)
        {
            $lastOrder = DB::connection('mysql_mes')->table('machine_process')
            ->where('machine_code', $id)
            ->orderBy('created_at', 'desc')
            ->select('idx')
            ->first();

            if (empty($lastOrder->idx)){
                $number = 0;
            }else{
                $number = $lastOrder->idx;
            }
            return $number + 1;
        }
    public function save_machine_process(Request $request){
         $now = Carbon::now();
                $values1 = [
                    'idx' => $this->getNextIdxMachineProcess($request->machine_code),
                    'process_id' => $request->process,
                    'machine_code' => $request->machine_code,
                    'last_modified_by' => Auth::user()->email,
                    'created_by' => Auth::user()->email,
                    'created_at' => $now->toDateTimeString()
                ];
            DB::connection('mysql_mes')->table('machine_process')->insert($values1);

        return redirect()->back();

    }
    public function delete_machine_process(Request $request){
        DB::connection('mysql_mes')->table('machine_process')->where("id", $request->process_id)->delete();
        return redirect()->back();
        // return redirect()->back()->with(['message' => 'Item code - <b>' . $itemissued->item_code . '</b>  has been successfully deleted!']);
    }
    public function get_workstation_process_jquery($workstation){
        $output="";
            if ($workstation != null) {
            $process =DB::connection('mysql_mes')->table('workstation_process')
            ->join('process', 'process.id', 'workstation_process.process_id')
            ->where('workstation_id', $workstation)
            ->select('workstation_process.*', 'process.process as process_name')
            ->get();

            foreach($process as $row)
                 {
                $output .= '<option value="'.$row->process_id.'">'.$row->process_name.'</option>';
                 }
            }

            // dd($output);
        return $output;
    }
    public function tbl_ppcstaff_overrride($jt_no){
        $data= DB::connection('mysql_mes')->table('job_ticket')->where('id', $jt_no)->first();

        return view('tables.tbl_ppc_staff_override', compact('data')); 
    }
    public function tbl_transfer_task_to_machine($jt_no, $workstation){
        $data= DB::connection('mysql_mes')->table('job_ticket')
        ->where('production_order', $jt_no)
        ->where('workstation', $workstation)
        ->where('status', 'Accepted')->first();

        if (!$data) {
            return response()->json(['success' => 0]);
        }else{

        $machine_for_workstation= DB::connection('mysql_mes')->table('workstation_machine')
        ->where('workstation',  $workstation)->get();

        return view('tables.tbl_transfer_task_to_machine', compact('data', 'machine_for_workstation')); 
        }
    }
    public function confirmOverride(Request $request){
        $now = Carbon::now();

        if (!$request->inspected_by) {
            return response()->json(['success' => 0, 'message' => 'Please tap Authorized PPc Staff Employee ID.']);
        }

        $qa_user = DB::connection('mysql_essex')->table('users')
            ->where('user_id', $request->inspected_by)->orWhere('id_security_key', $request->inspected_by)
            ->first();

        if (!$qa_user) {
            return response()->json(['success' => 0, 'message' => 'QA Employee ID not found.']);
        }else{

            $update = [
            'from_time' => ($request->status == 'In Progress') ? $request->from_time: null,
            'qty_accepted' => $request->accepted,
            'reject' => $request->reject,
            'good' => $request->good,
            'rework' => $request->rework,
            'status' => $request->status,
            ];

            DB::connection('mysql_mes')->table('job_ticket')->where('id', $request->jt_no)->update($update);
        }
        // if ($qa_user->designation_id != '38') {
        //     return response()->json(['success' => 0, 'message' => 'Please tap Authorized PPC Staff Employee ID.']);
        

        return response()->json(['success' => 1, 'message' => 'Task Updated']);
    }
    
    // /settings_module
    public function settings_module(){
        $permissions = $this->get_user_permitted_operation();

        $uom_list = DB::connection('mysql_mes')->table('uom')->get();

        $material_types = DB::connection('mysql')->table('tabItem Attribute Value')
            ->where('parent', 'like', '%materials%')->distinct()->pluck('attribute_value');

        $list=  DB::connection('mysql_mes')->table('workstation')->paginate(10);
        $operation = DB::connection('mysql')->table('tabOperation as top')->get();
        $item_classification = DB::connection('mysql')->table('tabItem Classification as item_class')->get();
        $item_group = DB::connection('mysql')->table('tabItem Group as item_group')->get();
        $warehouse = DB::connection('mysql')->table('tabWarehouse')->where('disabled', 0)->where('is_group', 0)
        ->where('company', 'FUMACO Inc.')->get();
        $warehouse_wip = DB::connection('mysql')->table('tabWarehouse')->where('company', 'FUMACO Inc.')->where('disabled', 0)->where('is_group', 0)->get();
        $machine_process= DB::connection('mysql_mes')->table('machine')->paginate(10);

        $process_list= DB::connection('mysql_mes')->table('process')->get();
        $operation_list=DB::connection('mysql_mes')->table('operation')->get();
        $shift_list=DB::connection('mysql_mes')->table('shift')
            ->join('operation', 'operation.operation_id', 'shift.operation_id')
            ->where('shift.shift_type','!=', 'Regular Shift')->get();

        $workstation_list= DB::connection('mysql_mes')
                ->table('workstation')->orderBy('order_no','asc')->get();
                
        $employees = DB::connection('mysql_essex')->table('users')->where('user_type', 'Employee')
            ->where('status', 'Active')->get();
        $module= DB::connection('mysql_mes')->table('user_group')->groupBy('module')->select('module')->get();

        $reject_category = DB::connection('mysql_mes')->table('reject_category')->get();
        $operations = DB::connection('mysql_mes')->table('operation')->get();
        $mes_users = DB::connection('mysql_mes')->table('user')->pluck('employee_name', 'user_access_id');
        return view('settings_module', compact('item_group','permissions', 'warehouse_wip','module','item_classification', 'warehouse','list','operation','machine_process', 'process_list','workstation_list', 'employees', 'operations', 'operation_list', 'shift_list','reject_category', 'uom_list', 'material_types', 'mes_users'));
    }
    public function save_process(Request $request){
            $now = Carbon::now();
            if (DB::connection('mysql_mes')->table('process')
                ->where('process_name', '=', $request->process_name)
                ->exists()){
                return response()->json(['success' => 0, 'message' => 'Process already exists']);
            }else{
                $values1 = [
                    'process_name' => $request->process_name,
                    'color_legend' => $request->color_legend,
                    'remarks' => $request->remarks,
                    'last_modified_by' => Auth::user()->email,
                    'created_by' => Auth::user()->email,
                    'created_at' => $now->toDateTimeString()
                ];

            DB::connection('mysql_mes')->table('process')->insert($values1);
            return response()->json(['success' => 1, 'message' => 'Process successfully Added.']);
            }
        }
    public function process_profile($id){
        // $list= DB::connection('mysql_mes')
        //         ->table('workstation')
        //         ->where('id', $id)
        //         ->first();
        $machine= DB::connection('mysql_mes')
                ->table('machine')
                ->get();
        $process = DB::connection('mysql_mes')
                ->table('process')
                ->where('id', $id)
                ->first();
        $workstation= DB::connection('mysql_mes')
                ->table('workstation')
                ->where('operation', 'Fabrication')
                ->get();

        return view('process_profile', compact('machine','process','workstation'));

    }
    public function get_tbl_workstation_list(Request $request){
        $list = DB::connection('mysql_mes')->table('workstation as w')
            ->join('operation as op', 'op.operation_id', 'w.operation_id')
            ->where(function($q) use ($request) {
                    $q->where('op.operation_name', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('w.workstation_name', 'LIKE', '%'.$request->search_string.'%');
            })
            ->select('w.*', "op.operation_name as operation")
            ->orderBy('w.workstation_id', 'desc')->paginate(15);
        
        return view('tables.tbl_workstation_process_list', compact('list'));
    }

    public function get_tbl_assigned_machine_process(Request $request){
      
        $process_list= DB::connection('mysql_mes')->table('machine_process')
        ->join('machine', 'machine.machine_code', 'machine_process.machine_code')
        ->where('process_id', $request->process_id)
        ->select('machine_process.*','machine.machine_name')
        ->orderBy('idx')
        ->get();

                // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
     
        // Create a new Laravel collection from the array data
        $itemCollection = collect($process_list);
     
        // Define how many items we want to be visible in each page
        $perPage = 5;
     
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
     
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
     
        // set url path for generted links
        $paginatedItems->setPath($request->url());

        $data = $paginatedItems;
        // dd($process_list);
        return view('tables.tbl_machine_assigned_process', compact('data'));
        
    }
    public function get_machine_assignment_jquery($workstation){
        $output="";
            if ($workstation != null) {
            $process =DB::connection('mysql_mes')->table('workstation_machine')
            // ->join('machine', 'machine.id', 'workstation_machine.workstation_id')
            ->where('workstation_machine.workstation_id', $workstation)
            ->select('workstation_machine.*')
            ->get();

            foreach($process as $row)
                 {
                $output .= '<option value="'.$row->machine_id.'">'.$row->machine_code.' - '.$row->machine_name.'</option>';
                 }
            }

            // dd($output);
        return $output;
    }
    public function process_assignment(Request $request){
        $machine_nametbl = DB::connection('mysql_mes')->table('machine')->where('machine_id', $request->machine_id)->select('machine_name', 'machine_code')->first();
        $now = Carbon::now();
            if (DB::connection('mysql_mes')->table('process_assignment')
                ->where('process_id', '=', $request->assign_process_id)
                ->where('machine_id', '=', $request->machifne_assignment)
                ->where('workstation_id', '=', $request->workstation_id_assign)
                ->exists()){
                    return response()->json(['success' => 0, 'message' => 'Workstation and Machine already exists in the process']);
            }else{
                $values2 = [
                    'workstation_id' => $request->workstation_id_assign,
                    // 'idx' => $this->getNextIdxMachineWorkstation($request->workstation_name),
                    'machine_id' =>  $request->machine_assignment,
                    'process_id' => $request->assign_process_id,
                    'created_at' => $now->toDateTimeString(),
                    'created_by' => Auth::user()->email,
                    'last_modified_by' => Auth::user()->email

                ];
                DB::connection('mysql_mes')->table('process_assignment')->insert($values2);
                return response()->json(['success' => 1, 'message' => 'Workstation and Machine successfully submitted.']);
            }
            // $values1 = [
            //     'name' => uniqid(),
            //     'creation' => $now->toDateTimeString(),
            //     'modified' => $now->toDateTimeString(),
            //     'modified_by' => Auth::user()->email,
            //     'owner' => Auth::user()->email,
            //     'docstatus' => 0,
            //     'parent' => $request->workstation_name,
            //     'parentfield' => 'workstation_machine',
            //     'parenttype' => 'Workstation',
            //     'idx' => $this->getNextIdxMachineWorkstation($request->workstation_name),
            //     'machine_name' => $machine_nametbl->machine_name,
            //     'machine_code' => $machine_nametbl->machine_code,
            // ];

            // DB::connection('mysql')->table('tabWorkstation Machine')->insert($values1);


        // return redirect()->back();

    }
    public function tbl_assigned_machine_process(Request $request,$id){
        $assignment_machine= DB::connection('mysql_mes')
                ->table('process_assignment')
                ->join('workstation', 'workstation.workstation_id', 'process_assignment.workstation_id')
                ->join('machine', 'machine.machine_id', 'process_assignment.machine_id')
                ->where('process_assignment.process_id', $id)
                ->select('process_assignment.*', 'workstation.workstation_name as workstation_name', 'machine.machine_code', 'machine.machine_name')
                ->orderBy('workstation_name', 'asc')
                ->get();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
     
        // Create a new Laravel collection from the array data
        $itemCollection = collect($assignment_machine);
     
        // Define how many items we want to be visible in each page
        $perPage = 10;
     
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
     
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
     
        // set url path for generted links
        $paginatedItems->setPath($request->url());

        $data = $paginatedItems;

        return view('tables.tbl_assigned_machine_process', compact('data'));

    }
    public function tbl_process_setup_list(Request $request){
        $process_list = DB::connection('mysql_mes')->table('process')
            ->where(function($q) use ($request) {
                    $q->orWhere('process_name', 'LIKE', '%'.$request->search_string.'%');
            })
            ->orderBy('last_modified_at', 'desc')->paginate(15);

        return view('tables.tbl_process_setup', compact('process_list'));
    }
    public function tbl_machine_setup_list(Request $request){

        $process_list= DB::connection('mysql_mes')
                ->table('process')
                ->get();
                        // dd($process_list);
        $workstation_list= DB::connection('mysql_mes')
                ->table('workstation')->orderBy('order_no','asc')->get();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
     
        // Create a new Laravel collection from the array data
        $itemCollection = collect($process_list);
     
        // Define how many items we want to be visible in each page
        $perPage = 10;
     
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
     
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
     
        // set url path for generted links
        $paginatedItems->setPath($request->url());

        $data = $paginatedItems;

        return view('tables.tbl_process_setup', compact('data','workstation_list'));


    }
    public function Update_process_setup_list(Request $request){
        $now = Carbon::now();

            $update = [
            'process_name' => $request->edit_process_name,
            'color_legend' => $request->color_legend,
            'remarks' => $request->edit_remarks,
            'last_modified_by' => Auth::user()->email

            ];

            DB::connection('mysql_mes')->table('process')->where('process_id', $request->edit_process_id)->update($update);

            return response()->json(['success' => 1, 'message' => 'Process successfully updated.']);
    }
    public function Delete_process_setup_list(Request $request){
        $process = DB::connection('mysql_mes')->table('process')->where('process_id', $request->delete_process_id)->select('process_name')->first();
        if(DB::connection('mysql_mes')->table('job_ticket')
        ->where('process_id', '=', $request->delete_process_id)
        ->exists()){
            return response()->json(['success' => 0, 'message' => 'Unable to process request. <b>'.$process->process_name.'</b> has already existing transaction.']);

        }else{
            DB::connection('mysql_mes')->table('process')->where("process_id", $request->delete_process_id)->delete();
            DB::connection('mysql_mes')->table('process_assignment')->where("process_id", $request->delete_process_id)->delete();
            return response()->json(['success' => 1, 'message' => 'Process successfully deleted.']);
        } 
    }
    public function machineKanban_tbl($workstation_id, $schedule_date){
        $now = Carbon::now();
        $current_date = $now->toDateTimeString();
        $workstation_details = DB::connection('mysql_mes')->table('workstation')->where('workstation_id', $workstation_id)->first();

        $line= $this->line_workstation($workstation_details->workstation_name, $schedule_date, $workstation_details->workstation_id);
        $data = [
            'production_line' => $workstation_details->workstation_name,
            'workstation_id' => $workstation_details->workstation_id,
            'order_no' => $workstation_details->order_no,
            'line' => $line,
        ];
        
        $scheduleDate= $schedule_date;
        // dd($data);
        return view('tables.tbl_unassigned_maachineKanban', compact('data','scheduleDate', 'current_date'));
    }
    public function countUnassignedTasksForOperator($workstation, $scheduleDate){
        $now = Carbon::now();
        $current_date = $now->toDateString();
        if ($scheduleDate <= $current_date) {
           $orders = DB::connection('mysql_mes')->table('production_order as prod')
            ->join('job_ticket as tsd','tsd.production_order','=','prod.production_order')
           ->where('tsd.workstation', $workstation)
            ->where('prod.status', 'Not Started')
            ->whereNull('tsd.machine')
            ->whereNotNull('tsd.process')
            ->whereDate('prod.planned_start_date',"<=", $scheduleDate)
            ->get(); 
        }else{
            $orders = DB::connection('mysql_mes')->table('production_order as prod')
            ->join('job_ticket as tsd','tsd.production_order','=','prod.production_order')
            ->where('tsd.workstation', $workstation)
            ->where('prod.status', 'Not Started')
            ->whereNull('tsd.machine')
            ->whereNotNull('tsd.process')
            ->whereDate('prod.planned_start_date', $scheduleDate)
            ->get();
        }
        $totals = collect($orders)->count();

        return $totals;
    }

        public function confirmTransferTask(Request $request){
        $now = Carbon::now();

        if (!$request->inspected_by) {
            return response()->json(['success' => 0, 'message' => 'Please tap Authorized PPC Staff Employee ID.']);
        }

        $qa_user = DB::connection('mysql_essex')->table('users')
            ->where('user_id', $request->inspected_by)->orWhere('id_security_key', $request->inspected_by)
            ->first();

        if (!$qa_user) {
            return response()->json(['success' => 0, 'message' => 'PPC Employee ID not found.']);
        }else{
            $machine_name= DB::connection('mysql_mes')->table('machine')->where('id', $request->machine_id)->first();

            $update = [
            'machine' => $machine_name->machine_code,
            'machine_name' => $machine_name->machine_name,
            'last_modified_by' => $qa_user->employee_name,
            'process' => $request->process_id
            ];

            DB::connection('mysql_mes')->table('job_ticket')->where('id', $request->jt_no)->update($update);
        }
        // if ($qa_user->designation_id != '38') {
        //     return response()->json(['success' => 0, 'message' => 'Please tap Authorized PPC Staff Employee ID.']);
        

        return response()->json(['success' => 1, 'message' => 'Task Updated']);
        }

        public function get_AssignProcessinMachine_jquery($machine, $workstation_id){
        $output="";
            $process =DB::connection('mysql_mes')->table('process_assignment')
            ->join('process', 'process.id', 'process_assignment.process_id')
            ->where('process_assignment.machine_id', $machine)
            ->where('process_assignment.workstation_id', $workstation_id)
            ->select('process_assignment.*', 'process.process as process_name' )
            ->get();

            foreach($process as $row)
                 {
                $output .= '<option value="'.$row->process_id.'">'.$row->process_name.'</option>';
                 }

            // dd($output);
        return $output;
    }
    public function machineKanban_view_machineList($process_id, $workstation_id, $schedule_date){
        $now = Carbon::now();
          $current_date = $now->toDateString();
          $scheduleDate  = $schedule_date;
          $machine= DB::connection('mysql_mes')->table('process_assignment AS pm')->join('machine AS m', 'm.machine_id','pm.machine_id')
          ->where('pm.process_id', $process_id)->where('pm.workstation_id', $workstation_id)->select('pm.*','m.machine_name','m.machine_code')->orderBy('m.machine_code', 'asc')->get();
          $workstation_name= DB::connection('mysql_mes')->table('workstation')
          ->where('workstation_id', $workstation_id)->select('workstation_name')->first();
          $machine_loads=[];
          foreach ($machine as $row) {

            if ($schedule_date <= $current_date){
                // $completed_Query= DB::connection('mysql_mes')->table('production_order as prod')
                // ->join('job_ticket as tsd','tsd.production_order','=','prod.production_order')
                // ->join('process as p', 'p.process_id', 'tsd.process_id')
                // ->join('time_logs', 'time_logs.job_ticket_id', 'tsd.job_ticket_id')
                // ->where('tsd.workstation', $workstation_name->workstation_name)
                // ->where('tsd.process_id', $process_id)
                // ->where('time_logs.machine_code', $row->machine_code)
                // ->where('prod.is_scheduled' , 1)
                // ->where('prod.status', 'Not Started')
                // ->whereDate('prod.planned_start_date', $schedule_date)
                // ->where('tsd.status', 'Completed')
                // ->select('prod.*', 'tsd.workstation as workstation_plot','time_logs.machine_code as machine','time_logs.job_ticket_id as jtname', 'p.process_name',"tsd.status as stat",'time_logs.good as good_qty', 'time_logs.operator_name', 'time_logs.from_time', 'time_logs.to_time', 'time_logs.duration');

                $completed_query= DB::connection('mysql_mes')->table('job_ticket as jt')
                ->leftJoin('time_logs as tl', 'jt.job_ticket_id', 'tl.job_ticket_id')
                ->join('production_order as prod', 'prod.production_order', 'jt.production_order')
                ->join('process as p', 'p.process_id', 'jt.process_id')
                ->where('tl.machine_code', $row->machine_code)
                ->whereDate('jt.planned_start_date', $schedule_date)
                // ->where('jt.status', 'Pending')
                // ->where('prod.status', '!=', 'Cancelled')
                ->whereNotIn('prod.status', ['Cancelled', 'Closed'])
                ->where('jt.status', 'Completed')
                ->where('prod.is_scheduled' , 1)
                ->where('jt.workstation', $workstation_name->workstation_name)
                ->where('jt.process_id', $process_id)
                ->select('prod.*', 'jt.workstation as workstation_plot','tl.machine_code as machine','tl.job_ticket_id as jtname', 'p.process_name',"jt.status as stat",'tl.good as good_qty', 'tl.operator_name', 'tl.from_time', 'tl.to_time', 'tl.duration', 'tl.time_log_id','tl.machine_name', 'jt.remarks');
                
                $late_completed_query= DB::connection('mysql_mes')->table('job_ticket as jt')
                ->leftJoin('time_logs as tl', 'jt.job_ticket_id', 'tl.job_ticket_id')
                ->join('production_order as prod', 'prod.production_order', 'jt.production_order')
                ->join('process as p', 'p.process_id', 'jt.process_id')
                ->where('tl.machine_code', $row->machine_code)
                ->whereDate('tl.to_time', $current_date)
                // ->where('jt.status', 'Pending')
                // ->where('prod.status', '!=', 'Cancelled')
                ->whereNotIn('prod.status', ['Cancelled', 'Closed'])
                ->where('jt.status', 'Completed')
                ->where('prod.is_scheduled' , 1)
                ->where('jt.workstation', $workstation_name->workstation_name)
                ->where('jt.process_id', $process_id)
                ->select('prod.*', 'jt.workstation as workstation_plot','tl.machine_code as machine','tl.job_ticket_id as jtname', 'p.process_name',"jt.status as stat",'tl.good as good_qty', 'tl.operator_name', 'tl.from_time', 'tl.to_time', 'tl.duration', 'tl.time_log_id','tl.machine_name', 'jt.remarks');
                


                // $completed_Late_Query= DB::connection('mysql_mes')->table('production_order as prod')
                // ->join('job_ticket as tsd','tsd.production_order','=','prod.production_order')
                // ->join('process as p', 'p.process_id', 'tsd.process_id')
                // ->join('time_logs', 'time_logs.job_ticket_id', 'tsd.job_ticket_id')
                // ->where('tsd.workstation', $workstation_name->workstation_name)
                // ->where('tsd.process_id', $process_id)
                // ->where('time_logs.machine_code', $row->machine_code)
                // ->where('prod.is_scheduled' , 1)
                // ->where('prod.status', 'Not Started')
                // ->where('tsd.status', 'Completed')
                // ->whereDate('time_logs.to_time', $current_date)
                // ->select('prod.*', 'tsd.workstation as workstation_plot','time_logs.machine_code as machine','time_logs.job_ticket_id as jtname', 'p.process_name',"tsd.status as stat",'time_logs.good as good_qty', 'time_logs.operator_name', 'time_logs.from_time', 'time_logs.to_time', 'time_logs.duration');
    
                // $orders = DB::connection('mysql_mes')->table('production_order as prod')
                // ->join('job_ticket as tsd','tsd.production_order','=','prod.production_order')
                // ->join('process as p', 'p.process_id', 'tsd.process_id')
                // ->join('time_logs', 'time_logs.job_ticket_id', 'tsd.job_ticket_id')
                // ->where('tsd.workstation', $workstation_name->workstation_name)
                // ->where('tsd.process_id', $process_id)
                // ->where('time_logs.machine_code', $row->machine_code)
                // ->where('prod.status', 'Not Started')
                // ->where('tsd.status', 'In Progress')
                // ->where('prod.is_scheduled' , 1)
                // ->whereDate('prod.planned_start_date','<=', $schedule_date)
                // ->union($completed_query)
                // ->union($late_completed_query)
                // ->select('prod.*', 'tsd.workstation as workstation_plot', DB::raw('(SELECT machine_code FROM time_logs WHERE job_ticket_id = tsd.job_ticket_id) AS machine'), DB::raw('(SELECT job_ticket_id FROM time_logs WHERE job_ticket_id = tsd.job_ticket_id) AS jtname'),'p.process_name', "tsd.status as stat", DB::raw('(SELECT good FROM time_logs WHERE job_ticket_id = tsd.job_ticket_id) AS good_qty'), DB::raw('(SELECT operator_name FROM time_logs WHERE job_ticket_id = tsd.job_ticket_id) AS operator_name'), DB::raw('(SELECT from_time FROM time_logs WHERE job_ticket_id = tsd.job_ticket_id) AS from_time'),DB::raw('(SELECT to_time FROM time_logs WHERE job_ticket_id = tsd.job_ticket_id) AS to_time'), DB::raw('(SELECT duration FROM time_logs WHERE job_ticket_id = tsd.job_ticket_id) AS duration') )
                // ->get(); 


                $orders= DB::connection('mysql_mes')->table('time_logs as tl')
                ->join('job_ticket as jt', 'jt.job_ticket_id', 'tl.job_ticket_id')
                ->join('production_order as prod', 'prod.production_order', 'jt.production_order')
                ->join('process as p', 'p.process_id', 'jt.process_id')
                ->where('tl.machine_code', $row->machine_code)
                ->whereDate('jt.planned_start_date','<=', $schedule_date)
                // ->where('jt.status', 'Pending')
                // ->where('prod.status', '!=', 'Cancelled')
                ->whereNotIn('prod.status', ['Cancelled', 'Closed'])
                ->where('tl.status', 'In Progress')
                ->where('prod.is_scheduled' , 1)
                ->where('jt.workstation', $workstation_name->workstation_name)
                ->where('jt.process_id', $process_id)
                ->union($completed_query)
                ->union($late_completed_query)
                ->select('prod.*', 'jt.workstation as workstation_plot','tl.machine_code as machine','tl.job_ticket_id as jtname', 'p.process_name',"jt.status as stat",'tl.good as good_qty', 'tl.operator_name', 'tl.from_time', 'tl.to_time', 'tl.duration', 'tl.time_log_id','tl.machine_name', 'jt.remarks')
                ->get();
                

                }else{
    
                // $orders = DB::connection('mysql_mes')->table('production_order as prod')
                // ->join('job_ticket as tsd','tsd.production_order','=','prod.production_order')
                // ->join('process as p', 'p.process_id', 'tsd.process_id')
                // ->where('tsd.workstation', $workstation_name->workstation_name)
                // ->where('tsd.process_id', $process_id)
                // ->where('tsd.machine_code', $row->machine_code)
                // ->where('prod.status', 'Not Started')
                // ->where('prod.is_scheduled' , 1)
                // ->whereDate('prod.planned_start_date', $schedule_date)
                // ->select('prod.*', 'tsd.workstation as workstation_plot','tsd.machine_code as machine','tsd.job_ticket_id as jtname','tsd.completed_qty as com_qty', 'p.process_name', "tsd.status as stat",'prod.qty_to_manufacture as accpt_qty', 'tsd.item_feedback as item_feed','tsd.good as good_qty', 'tsd.operator_name', 'tsd.from_time', 'tsd.to_time', 'tsd.hours', 'tsd.qa_inspection_status', 'tsd.qa_inspection_date', 'tsd.qa_staff_name as qa_inspected_by', 'tsd.machine_name', 'tsd.qc_type', 'tsd.remarks')
                // ->get();

                $orders= DB::connection('mysql_mes')->table('time_logs as tl')
                ->join('job_ticket as jt', 'jt.job_ticket_id', 'tl.job_ticket_id')
                ->join('production_order as prod', 'prod.production_order', 'jt.production_order')
                ->join('process as p', 'p.process_id', 'jt.process_id')
                ->where('tl.machine_code', $row->machine_code)
                ->whereDate('jt.planned_start_date', $schedule_date)
                // ->where('prod.status', '!=', 'Cancelled')
                ->whereNotIn('prod.status', ['Cancelled', 'Closed'])
                ->where('tl.status', 'In Progress')
                ->where('prod.is_scheduled' , 1)
                ->where('jt.workstation', $workstation_name->workstation_name)
                ->where('jt.process_id', $process_id)
                ->select('prod.*', 'jt.workstation as workstation_plot','tl.machine_code as machine','tl.job_ticket_id as jtname', 'p.process_name',"jt.status as stat",'tl.good as good_qty', 'tl.operator_name', 'tl.from_time', 'tl.to_time', 'tl.duration', 'tl.time_log_id','tl.machine_name', 'jt.remarks')
                ->get();
              }
            $process_name = DB::connection('mysql_mes')->table('process')->where('process_id', $process_id)->first()->process_name;


            $machine_nametbl = DB::connection('mysql_mes')->table('machine')->where('machine_code', $row->machine_code)->select('machine_name')->first();

            $machine_loads[]=[
                'machine_code' => $row->machine_code,
                'machine_name' => $machine_nametbl->machine_name,
                'machine_process' => $process_name,
                'machine_load' => $orders,
                'workstation_name' =>$workstation_name->workstation_name,
                'workstation_id' => $workstation_id
            ];
          
        }
        return view('tables.tbl_machineKanban_view_machine', compact('machine_loads', 'current_date', 'scheduleDate'));
    }
    public function reset_task(Request $request){
        try {
            if ($request->id) {
                $jt_details = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $request->id)->where('status','!=','Completed')->first();
                if ($jt_details) {
                    return response()->json(['success' => 0, 'message' => 'Task is already reset.']);
                }

                $now = Carbon::now();
                    $update = [
                        'from_time' => null,
                        'to_time' => null,
                        'hours' => null,
                        'status' => 'Pending',
                        'operator_id' => null,
                        'operator_name' => null,
                        'last_modified_at' => $now->toDateTimeString(),
                        'last_modified_by' => Auth::user()->employee_name,
                    ];

                DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $request->id)->update($update);

                     return response()->json(['success' => 1, 'message' => 'Task has been reset.']);
            }
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
        
    }
     public function mark_as_done_task(Request $request){
        DB::connection('mysql')->beginTransaction();
        DB::connection('mysql_mes')->beginTransaction();
        try {
            $now = Carbon::now();
            if ($request->id) {
                $job_ticket_details = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $request->id)->first();
                
                $qty_to_manufacture = DB::connection('mysql_mes')->table('production_order')->where('production_order', $job_ticket_details->production_order)->sum('qty_to_manufacture');
                if ($job_ticket_details->workstation != 'Spotwelding') {
                    $tl_total_qty =  DB::connection('mysql_mes')->table('time_logs')
                        ->where('job_ticket_id', $job_ticket_details->job_ticket_id)->where('status', 'Completed')->sum('good');

                    if ($tl_total_qty == $qty_to_manufacture) {
                        return response()->json(['success' => 0, 'message' => 'Task already Completed']);
                    }
                } else {
                    if($job_ticket_details->status == 'Completed'){
                        return response()->json(['success' => 0, 'message' => 'Task already Completed']);
                    }
                }
                
                $pending = $qty_to_manufacture - $job_ticket_details->completed_qty;

                $logs_table = $request->workstation == 'Spotwelding' ? 'spotwelding_qty' : 'time_logs';
                if ($request->logid) {
                    $logs = DB::connection('mysql_mes')->table($logs_table)
                        ->where('time_log_id', $request->logid)->where('status', 'In Progress')->first();
                } else {
                    $logs = DB::connection('mysql_mes')->table($logs_table)
                        ->where('job_ticket_id', $request->id)->where('status', 'In Progress')->first();
                }

                if(!$logs){
                    return response()->json(['success' => 0, 'message' => 'Task not found. Please reload the page.']);
                }

                // update and save timelogs
                $from_time = Carbon::parse($logs->from_time);
                $duration = $from_time->diffInSeconds($now);
                $duration = $duration / 3600;

                $values = [
                    'last_modified_by' => Auth::user()->employee_name,
                    'last_modified_at' => $now->toDateTimeString(),
                    'status' => 'Completed',
                    'to_time' => $now->toDateTimeString(),
                    'good' => $pending,
                    'duration' => $duration
                ];

                if ($request->logid) {
                    DB::connection('mysql_mes')->table($logs_table)->where('time_log_id', $logs->time_log_id)
                        ->where('status', 'In Progress')->update($values);
                } else {
                    DB::connection('mysql_mes')->table($logs_table)->where('job_ticket_id', $job_ticket_details->job_ticket_id)
                        ->where('status', 'In Progress')->update($values);
                }

                DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_details->job_ticket_id)->update(['remarks' => 'Override']);

                $update_job_ticket = $this->update_job_ticket($job_ticket_details->job_ticket_id);

                if(!$update_job_ticket){
                    DB::connection('mysql')->rollback();
                    DB::connection('mysql_mes')->rollback();

                    return response()->json(['success' => 0, 'message' => 'An error occured. Please try again.']);
                }

                DB::connection('mysql')->commit();
                DB::connection('mysql_mes')->commit();

                return response()->json(['success' => 1, 'message' => 'Task Overridden.']);
            }
        } catch (Exception $e) {
            DB::connection('mysql')->rollback();
            DB::connection('mysql_mes')->rollback();

            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function get_process_list($workstation){
        return DB::connection('mysql_mes')->table('process_assignment')->join('process', 'process.process_id', 'process_assignment.process_id')->where('process_assignment.workstation_id', $workstation)->select('process.process_id', 'process.process_name')->distinct()->get();
    }
    public function update_process(Request $request){
        try {
            if ($request->id) {
                $jt_details = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $request->id)
                    ->where('status', '!=', 'Pending')->first();
                if ($jt_details) {
                    return response()->json(['success' => 0, 'message' => 'Task already Completed / In Progress.']);
                }

                DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $request->id)->update(['process_id' => $request->process, 'status' => 'Pending']);

                return response()->json(['success' => 1, 'message' => 'Task updated.']);
            }
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }
    public function get_AssignMachineinProcess_jquery($jt_id, $workstation_id){
        $output="";
        $jobticket_details =DB::connection('mysql_mes')->table('job_ticket')
        ->where('job_ticket_id', $jt_id)->select('process_id')->first();

            $process =DB::connection('mysql_mes')->table('process_assignment')
            ->join('machine', 'machine.machine_id', 'process_assignment.machine_id')
            ->where('process_assignment.process_id', $jobticket_details->process_id)
            ->where('process_assignment.workstation_id', $workstation_id)
            ->select('process_assignment.*', 'machine.machine_name', 'machine.machine_code' )
            ->get();


            foreach($process as $row)
                 {
                $output .= '<option value="'.$row->machine_id.'">'.$row->machine_code.'-'.$row->machine_name.'</option>';
                 }

            // dd($output);
        return $output;
    }

        // revised - Jae
        public function printJobTickets($scheduled_date){
            $prod_orders = DB::connection('mysql_mes')->table('production_order')
                ->whereDate('planned_start_date', $scheduled_date)
                ->whereNotIn('status', ['Completed', 'Cancelled', 'Closed'])->get();
 
         $jobtickets = [];
         foreach ($prod_orders as $pro) {
             $sales_order = DB::connection('mysql')->table('tabSales Order')->where('name', $pro->sales_order)->first();
             $table = $this->subquery_printTimesheet($pro->production_order);
             $sales_type = ($sales_order && $sales_order->sales_type) ? $sales_order->sales_type : '';
 
             $jobtickets[] = [
                 'production_order' => $pro->production_order,
                 'customer' => $pro->customer,
                 'material_request' => $pro->material_request,
                 'sales_order' => $pro->sales_order,
                 'project' => $pro->project,
                 'item_code' => $pro->item_code,
                 'description' => $pro->description,
                 'qty' => $pro->qty_to_manufacture,
                 'model' => $pro->parent_item_code,
                 // 'cutting_size' => $pro->actual_cutting_size,
                 'sched_date' => $scheduled_date,
                 'sales_type' => $sales_type,
                 'workstation' => $table,
             ];
         }
 
         return view('print_job_ticket', compact('jobtickets'));
     }
 
 
     public function single_printJobTickets($production_order){
        $prod_orders = DB::connection('mysql_mes')->table('production_order AS pro')
            // ->join('job_ticket as t', 't.production_order', 'pro.production_order')
            ->where('pro.production_order', $production_order)
            ->get();

        $jobtickets = [];
        foreach ($prod_orders as $pro) {
           //  $sales_order = DB::connection('mysql')->table('tabSales Order')->where('name', $pro->sales_order)->first();
            $table = $this->subquery_printTimesheet($pro->production_order);
            $jobtickets[] = [
               //  "count_same_workstation"=> collect($table)->where('workstation', )
                'production_order' => $pro->production_order,
                'customer' => $pro->customer,
                'sales_order' => $pro->sales_order,
                'project' => $pro->project,
                'item_code' => $pro->item_code,
                'description' => $pro->description,
                'qty' => $pro->qty_to_manufacture,
                'material_request' => $pro->material_request,
                'model' => $pro->parent_item_code,
                // 'jobticket' => $pro->jtname,
                // 'cutting_size' => $pro->actual_cutting_size,
                'sched_date' => date('Y-m-d', strtotime($pro->planned_start_date)),
                'sales_type' => $pro->classification,
                'workstation' => $table,
            ];
        }
       //  dd($jobtickets);
        return view('print_job_ticket', compact('jobtickets'));
    }
 
     public function subquery_printTimesheet($prod_name){

        $workstation_jbticket=DB::connection('mysql_mes')->table('job_ticket as jt')
            ->where('production_order', $prod_name)
            ->groupBy('workstation')
            ->select('workstation')->orderBy('idx', 'ASC')->get();
        $data = [];
        foreach($workstation_jbticket as $row){
            $workstation_jbtickets=DB::connection('mysql_mes')->table('job_ticket as jt')
            // ->join('time_logs as tl', 'jt.job_ticket_id', 'tl.job_ticket_id')
            ->where('production_order', $prod_name)
            ->distinct('process_id')
            ->get();
            $data[]=[
                "workstation" => $row->workstation,
                'count' => collect($this->subquery_printTimesheets($prod_name, $row->workstation))->count(),
                'jobticket_details'=> $this->subquery_printTimesheets($prod_name, $row->workstation)
            ];

        }
 
         return $data;
    }
    public function workstation_distinct($workstation){
        $workstation =  DB::connection('mysql_mes')->table('workstation')->where('workstation_name', $workstation)->first();
        
        return $workstation->workstation_name;
    }
    public function subquery_printTimesheets($prod_name, $workstation){
        $prod_qty =DB::connection('mysql_mes')->table('production_order')
        ->where('production_order', $prod_name)
        ->sum('qty_to_manufacture');

        $workstations = DB::connection('mysql_mes')->table('job_ticket')
        ->where('production_order', $prod_name)
        ->join('process', 'job_ticket.process_id', 'process.process_id')
        ->where('job_ticket.workstation', $workstation)
        ->select('job_ticket.workstation', 'process.process_name', 'job_ticket.job_ticket_id', 'job_ticket.idx')
        ->groupBy('job_ticket.workstation', 'process.process_name', 'job_ticket.job_ticket_id', 'job_ticket.idx')->orderBy('idx','asc')->get(); 
        // dd($workstations);
        $data = [];
        foreach ($workstations as $row) {
            if(DB::connection('mysql_mes')->table('time_logs')
            ->where('job_ticket_id', '=', $row->job_ticket_id)
            ->exists()){
                $workstationss = DB::connection('mysql_mes')->table('time_logs')
                ->where('job_ticket_id', $row->job_ticket_id)
                ->selectRaw('machine_code, SUM(good) AS good, SUM(reject) AS reject, GROUP_CONCAT(DISTINCT operator_name) AS operators')
                ->groupBy('job_ticket_id', 'machine_code')->first(); 
                $completed_qty =  DB::connection('mysql_mes')->table('job_ticket as jt')->where('job_ticket_id', $row->job_ticket_id)->select('jt.completed_qty')->first();

                $machine_details = DB::connection('mysql_mes')->table('machine')->where('machine_code', $workstationss->machine_code)->first();
                $rejects = $workstationss->reject;
                $bals = $prod_qty - $completed_qty->completed_qty;
                $good = $completed_qty->completed_qty;
                $reject = ($workstationss->good == $prod_qty) ? 0 : $rejects;
                $balance= ($workstationss->good == $prod_qty) ? 0 : $bals;
                $operator = $workstationss->operators;
                $machine = $workstationss->machine_code;
                $machine_name= ($machine_details) ? $machine_details->machine_name : null;
                if(DB::connection('mysql_mes')->table('scrap_used')
                ->where('production_order', '=', $prod_name)
                ->exists()){
                    $remark= "with Scrap";
                }else{
                    $remark="";
                }
                

            }elseif(DB::connection('mysql_mes')->table('scrap_used')
            ->where('production_order', '=', $prod_name)
            ->exists()){

                $completed_qty =  DB::connection('mysql_mes')->table('job_ticket as jt')->where('job_ticket_id', $row->job_ticket_id)->select('jt.completed_qty')->first();


                $bals = $prod_qty - $completed_qty->completed_qty;
                $good = $completed_qty->completed_qty;
                $reject = 0;
                $balance= $bals;
                $operator = null;
                $machine = null;
                $machine_name= null;
                $remark= "with Scrap";
                
            }else{
                $good = "";
                $reject = 0;
                $balance= 0;
                $operator = null;
                $machine = null;
                $machine_name= null;
                $remark="";

            }
            
            $data[]=[
                'qty'=> $prod_qty,
                'workstation' => $row->workstation,
                'process' => $row->process_name,
                'good' => $good,
                'reject' => $reject,
                'bal' => $balance,
                'operator' => $operator,
                'machine' => $machine,
                'machine_name' => $machine_name,
                'status' => null,
                'remark' =>  $remark
                // 'status' => ($operator->qa_inspection_status == 'Pending' || $operator->qa_inspection_status == 'Completed') ? '': $operator->qa_inspection_status
            ];
        }

        return $data;
    }


    // revised - Patrick
    public function productionKanban(){
        $unscheduled_prod = DB::connection('mysql_mes')->table('production_order')
            ->whereNotIn('status', ['Completed', 'Stopped', 'Cancelled', 'Closed'])->where('is_scheduled', 0)
            ->orderBy('created_at', 'desc')->get();

        $unscheduled = [];
        foreach ($unscheduled_prod as $row) {
            // $erp_prod = DB::connection('mysql')->table('tabWork Order')
            //  ->where('name', $row->production_order)->first();

            // $erp_prod_status = ($erp_prod) ? $erp_prod->docstatus : -1;
            // if ($erp_prod_status == 1) {
                $unscheduled[] = [
                    'id' => $row->production_order,
                    'status' => $row->status,
                    'name' => $row->production_order,
                    'order_no' => $row->order_no,
                    'customer' => $row->customer,
                    'delivery_date' => $row->delivery_date,
                    'production_item' => $row->item_code,
                    'production_order' => $row->production_order,
                    'description' => $row->description,
                    'qty' => $row->qty_to_manufacture,
                    'stock_uom' => $row->stock_uom,
                    'produced_qty' => $row->produced_qty,
                    'classification' => $row->classification,
                    // 'batch' => ($batch) ? 'Batch No.: <b>'.$batch->idx.'</b>' : null,
                    'batch' => null,
                ];
            // }

            // $batch = DB::connection('mysql')->table('tabWork Batch Table')->where('production_order', $row->name)->first();

            
        }

        return view('production_kanban', compact('unscheduled'));
    }

    // revised - Jae
    public function tbl_production_scheduling(){
        $period = CarbonPeriod::create(now()->subDays(1), now()->addDays(6));

        // Iterate over the period->subDays(1)
        $scheduled = [];
        foreach ($period as $date) {
            // $
            // if ($) {
            //     # code...
            // }else{
            //     $operation= DB::connection('mysql_mes')->table('operation')
            //     ->where('operation_name', 'FABRICATION')->first();

            //     $regular_shift= DB::connection('mysql_mes')->table('shift')
            //     ->where('operation_id', $operation->operation_id)->first();
            //     $max_qty=$regular_shift->regular_shift;

            // }
            $shift = $this->shift_details($date->format('Y-m-d'));
            $overtime_details= $this->overtime_details($date->format('Y-m-d'), $shift->time_in, $shift->time_out);
            $overtime=$shift->qty_capacity + $overtime_details;
            
            
            $max_qty=$overtime;
            $orders = $this->getScheduledProdOrders_proScheduling($date->format('Y-m-d'));
            $get_percentage= ((collect($orders)->sum('qty'))/$max_qty)*100;
            $load_status = $max_qty >= (collect($orders)->sum('qty')) ? 'free':'overloaded';
            $scheduled[] = [
                'schedule' => $date->format('Y-m-d'),
                'orders' => $orders,
                'total_onqueue' => count($orders),
                'total_qty' => collect($orders)->sum('qty'),
                'percentage' => round($get_percentage, 2),
                'load_status' => $load_status,
                'max_qty'=> $max_qty,
                'remark' => $overtime_details

            ];
        }
        // dd($scheduled);
        return view('tables.tbl_production_scheduling', compact('scheduled'));
    }
    public function shift_details($sched_date){
        $special_shift = DB::connection('mysql_mes')->table('shift_schedule')
                ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
                ->where('shift.shift_type', "Special Shift")
                ->where('shift_schedule.date',  $sched_date)
                ->select('shift_schedule.*', 'shift.qty_capacity', 'shift.time_in', 'shift.time_out')
                ->first();

        if (empty($special_shift)) {
                $operation= DB::connection('mysql_mes')->table('operation')
                ->where('operation_name', 'FABRICATION')->first();

                $regular_shift= DB::connection('mysql_mes')->table('shift')
                ->where('operation_id', $operation->operation_id)->first();
           
        }

        $shift_detail = empty($special_shift) ? $regular_shift : $special_shift;
            
        return $shift_detail;
    }
        public function overtime_details($sched_date, $time_in, $time_out){
        $data = [];
        $overtime_shift = DB::connection('mysql_mes')->table('shift_schedule')
                ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
                ->where('shift.shift_type', "Overtime Shift")
                ->where('shift_schedule.date',  $sched_date)
                ->select('shift_schedule.*', 'shift.qty_capacity', 'shift.time_in', 'shift.time_out')
                ->first();
        if(empty($overtime_shift)){
            $shift_detail=0;
        }else{
            if ((strtotime($time_in) < strtotime($overtime_shift->time_out)) && (strtotime($time_out) > strtotime($overtime_shift->time_in))){
                $shift_detail=$overtime_shift->qty_capacity;
            }else{
                $shift_detail=$overtime_shift->qty_capacity;
            }

        }

            
        return $shift_detail;
    }
    public function getScheduledProdOrders_proScheduling($schedule_date){
        $orders = DB::connection('mysql_mes')->table('production_order')
            ->whereNotIn('status', ['Completed', 'Cancelled', 'Closed'])->where('is_scheduled', 1)
            ->whereDate('planned_start_date', $schedule_date)
            ->orderBy('order_no', 'asc')->orderBy('order_no', 'asc')->orderBy('created_at', 'desc')
            ->get();

        $scheduled = [];
        foreach($orders as $row){
            $status = $this->prodJtStatus_proScheduling($row->production_order);
            // $batch = DB::connection('mysql')->table('tabWork Batch Table')->where('production_order', $row->name)->first();
            $scheduled[] = [
                'id' => $row->production_order,
                'name' => $row->production_order,
                'status' => $status,
                'order_no' => $row->order_no,
                'customer' => $row->customer,
                'delivery_date' => $row->delivery_date,
                'production_item' => $row->item_code,
                'description' => $row->description,
                'qty' => $row->qty_to_manufacture,
                'stock_uom' => $row->stock_uom,
                'produced_qty' => $row->produced_qty,
                'classification' => $row->classification,
                'production_order' => $row->production_order,
                // 'batch' => ($batch) ? 'Batch No.: <b>'.$batch->idx.'</b>' : null,
                'batch' => null,
            ];
        }

        return $scheduled;
    }

    // revised - Jae
    public function prodJtStatus_proScheduling($prodno){
        // $req = DB::connection('mysql_mes')->table('production_order')->where('production_order', $prodno)->first()->qty_to_manufacture;
        $prod = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $prodno)->get();

        // $qty_done = collect($prod)->sum('good');

        $total_workstation = collect($prod)->count();
        $total_unassigned = collect($prod)->where('status', 'Unassigned')->count();
        $total_inprocess = collect($prod)->where('status', 'In Progress')->count();
        $total_accepted = collect($prod)->where('status', 'Accepted')->count();
        $total_completed = collect($prod)->where('status', 'Completed')->count();

        if ($total_workstation == ($total_unassigned + $total_accepted)) {
            return 'Not Started';
        }

        if ($total_inprocess > 0) {
            return 'In Process';
        }

        if ($total_completed == $total_workstation) {
            return 'Completed';
        }

        return 'Not Started';

    }

    // /get_painting_production_order_list/{date}
    public function get_production_order_list(Request $request, $schedule_date){
        if($request->operation == 1){
            $permitted_workstation = DB::connection('mysql_mes')->table('workstation')
                ->where('operation_id', $request->operation)
                ->whereNotIn('workstation_name', ['Painting'])->distinct()
                ->pluck('workstation_name')->toArray();
        }elseif($request->operation == 2){
            $permitted_workstation = DB::connection('mysql_mes')->table('workstation')
                ->where('workstation_name', 'Painting')->distinct()
                ->pluck('workstation_name')->toArray();
        }else{
            $permitted_workstation = DB::connection('mysql_mes')->table('workstation')
                ->where('operation_id', $request->operation)->distinct()
                ->pluck('workstation_name')->toArray();
        }

        $orders_1 = DB::connection('mysql_mes')->table('time_logs')
            ->join('job_ticket as tsd', 'time_logs.job_ticket_id', 'tsd.job_ticket_id')
            ->join('production_order as prod','tsd.production_order', 'prod.production_order')
            ->join('process as p', 'p.process_id', 'tsd.process_id')
            ->whereNotIn('prod.status', ['Cancelled', 'Closed'])
            ->join('workstation as work','work.workstation_name','tsd.workstation')
            ->whereIn('tsd.workstation', $permitted_workstation)
            ->where('time_logs.status', 'In Progress')
            ->select('prod.production_order','prod.qty_to_manufacture','tsd.workstation as workstation_plot','time_logs.machine_code as machine','time_logs.job_ticket_id as jtname', 'p.process_name', "tsd.status as stat", 'tsd.item_feedback as item_feed', 'time_logs.operator_name', 'time_logs.operator_id', 'time_logs.from_time', 'time_logs.to_time', 'time_logs.machine_code', 'work.workstation_id', 'time_logs.time_log_id', 'tsd.job_ticket_id', 'time_logs.machine_name');

        if($request->operation != 2){
            $orders = DB::connection('mysql_mes')->table('spotwelding_qty')
                ->join('job_ticket as tsd', 'spotwelding_qty.job_ticket_id', 'tsd.job_ticket_id')
                ->join('production_order as prod','tsd.production_order','=','prod.production_order')
                ->join('process as p', 'p.process_id', 'tsd.process_id')
                ->whereNotIn('prod.status', ['Cancelled', 'Closed'])
                ->join('workstation as work','work.workstation_name','tsd.workstation')
                ->whereIn('tsd.workstation', $permitted_workstation)
                ->where('spotwelding_qty.status', 'In Progress')
                ->select('prod.production_order','prod.qty_to_manufacture','tsd.workstation as workstation_plot','spotwelding_qty.machine_code as machine','spotwelding_qty.job_ticket_id as jtname', 'p.process_name', "tsd.status as stat", 'tsd.item_feedback as item_feed', 'spotwelding_qty.operator_name', 'spotwelding_qty.operator_id', 'spotwelding_qty.from_time', 'spotwelding_qty.to_time', 'spotwelding_qty.machine_code', 'work.workstation_id', 'spotwelding_qty.time_log_id', 'tsd.job_ticket_id', 'spotwelding_qty.machine_name')
                ->union($orders_1)->get();
        }else{
            $orders = $orders_1->get();
        }

        $time_log_array = [];
        if($request->operation == 2){
            $time_log_qry = DB::connection('mysql_mes')->table('job_ticket')
                ->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
                ->join('process', 'process.process_id', 'job_ticket.process_id')
                ->whereIn('job_ticket.production_order', collect($orders)->pluck('production_order'))
                ->orderBy('time_logs.created_at', 'desc')
                ->get();

            foreach($time_log_qry as $tl){
                $time_log_array[$tl->production_order][$tl->process_name][] = [
                    'from_time' => $tl->from_time,
                    'to_time' => $tl->to_time,
                    'operator' => $tl->operator_name
                ];
            }
        }

        $result = [];
        foreach($orders as $row){
            $reference_type = ($row->workstation_plot == 'Spotwelding') ? 'Spotwelding' : 'Time Logs';
            $reference_id = ($row->workstation_plot == 'Spotwelding') ? $row->jtname : $row->time_log_id;
            $qa_table = DB::connection('mysql_mes')->table('quality_inspection')
                ->where('reference_type', $reference_type)->where('reference_id', $reference_id)->first();

            if(!empty($qa_table)){
                $qa_em= DB::connection('mysql_essex')->table('users')->where('user_id', $qa_table->qa_staff_id)
                    ->select('employee_name')->first();
            }

            $loading_time = $loading_operator = null;
            $loading_qty = 0;
            if(isset($time_log_array[$row->production_order]['Loading'])){
                $loading_time =  collect($time_log_array[$row->production_order]['Loading'])->min('from_time');
                $loading_time = Carbon::parse($loading_time)->format('M-d-Y h:i A');
    
                $loading_operator = $time_log_array[$row->production_order]['Loading'][0]['operator'];
            }
            
            $unloading_time = $unloading_operator = null;
            $unloading_qty = 0;
            if(isset($time_log_array[$row->production_order]['Unloading'])){
                $unloading_time = collect($time_log_array[$row->production_order]['Unloading'])->max('to_time');
                $unloading_time = Carbon::parse($unloading_time)->format('M-d-Y h:i A');

                $unloading_operator = $time_log_array[$row->production_order]['Unloading'][0]['operator'];
            }

            $helpers = DB::connection('mysql_mes')->table('helper')->where('time_log_id', $row->time_log_id)->distinct()->pluck('operator_name');

            $result[]=[
                'workstation_plot'=> $row->workstation_plot,
                'machine' => $row->machine_name,
                'jtname' => $row->jtname,
                'process_name' => $row->process_name,
                'stat' => $row->stat,
                'item_feed' => $row->item_feed,
                'operator_name' => $row->operator_name,
                'from_time' => ($row->from_time == null)? '-' : Carbon::parse($row->from_time)->format('M-d-Y h:i A'),
                'to_time' => ($row->to_time == null)? '-' : Carbon::parse($row->to_time)->format('M-d-Y h:i A'),
                'time_log_id'=>$row->time_log_id,
                'production_order' => $row->production_order,
                'job_ticket_id' => $row->job_ticket_id,
                'timelogs_id' => $row->time_log_id,
                'qty_to_manufacture' => $row->qty_to_manufacture,
                'loading_time' => $loading_time,
                'loading_operator' => $loading_operator,
                'unloading_time' => $unloading_time,
                'unloading_operator' => $unloading_operator,
                'qty_accepted' => $row->qty_to_manufacture,
                'workstation_id' =>  $row->workstation_id,
                'operator_id' =>  $row->operator_id,
                'helpers' => $helpers,
            ];
        }

        $current_date = $schedule_date;
        $operation = $request->operation;
        
        return view('tables.tbl_production_order_list_maindashboard', compact('result', 'current_date', 'operation'));
    }

    public function qa_monitoring_summary(Request $request, $schedule_date){
        $data = [];
        if($request->operation == 1){
            // spotwelding qa inspection
            $query = DB::connection('mysql_mes')->table('quality_inspection as qi')
                ->join('job_ticket as jt', 'jt.job_ticket_id', 'qi.reference_id')
                ->join('production_order as po', 'jt.production_order', 'po.production_order')
                ->whereDate('qi.qa_inspection_date', $schedule_date)
                ->where('qi.reference_type', 'Spotwelding')->where('po.operation_id', $request->operation)
                ->select('jt.production_order', 'qi.qa_staff_id', 'qi.actual_qty_checked', 'qi.status', 'qi.qa_inspection_type', 'qi.rejected_qty', 'qi.created_by')->paginate(10);

            foreach($query as $row){
                $qa_staff_details = DB::connection('mysql_essex')->table('users')->where('user_id', $row->qa_staff_id)->first();

                $data[] = [
                    'inspection_type' => $row->qa_inspection_type,
                    'inspected_by' => $qa_staff_details->employee_name,
                    'production_order' => $row->production_order,
                    'quantity' => $row->actual_qty_checked,
                    'rejected_qty' => $row->rejected_qty,
                    'status' => $row->status,
                    'operator_name' => $row->created_by
                ];
            }
        }

       $query = DB::connection('mysql_mes')->table('quality_inspection as qi')
            ->join('time_logs as tl', 'tl.time_log_id', 'qi.reference_id')
            ->join('job_ticket as jt', 'jt.job_ticket_id', 'tl.job_ticket_id')
            ->join('production_order as po', 'jt.production_order', 'po.production_order')
            ->whereDate('qi.qa_inspection_date', $schedule_date)
            ->where('qi.reference_type', 'Time Logs')->where('po.operation_id', $request->operation)
            ->when($request->operation == 2, function($q){
				return $q->where('jt.workstation', 'Painting');
			})
            ->when($request->operation != 2, function($q){
				return $q->where('jt.workstation', '!=', 'Painting');
			})
            ->select('qi.qa_inspection_date', 'jt.production_order', 'qi.qa_staff_id', 'qi.actual_qty_checked', 'qi.status', 'qi.qa_inspection_type', 'qi.rejected_qty', 'tl.operator_name')->paginate(10);

        foreach($query as $row){
            $qa_staff_details = DB::connection('mysql_essex')->table('users')->where('user_id', $row->qa_staff_id)->first();

            $data[] = [
                'inspection_type' => $row->qa_inspection_type,
                'inspected_by' => $qa_staff_details->employee_name,
                'production_order' => $row->production_order,
                'quantity' => $row->actual_qty_checked,
                'rejected_qty' => $row->rejected_qty,
                'status' => $row->status,
                'operator_name' => $row->operator_name
            ];
        }

        $inspectors = array_unique(array_column($data, 'inspected_by'));

        $per_inspector = [];
        foreach($inspectors as $inspector){
            $logs = collect($data)->where('inspected_by', $inspector);
            $per_inspector[] = [
                'inspector' => $inspector,
                'production_order' => $logs->count(),
                'qty' => $logs->sum('quantity')
            ];
        }

        $quality_inspection = collect($data)->filter(function ($value, $key) {
            return (in_array($value['inspection_type'], ['Quality Check', 'Random Inspection']));
        });

        $rejection = collect($data)->filter(function ($value, $key) {
            return (in_array($value['inspection_type'], ['Reject Confirmation']));
        });

        return view('tables.tbl_qa_monitoring', compact('quality_inspection', 'rejection', 'per_inspector'));
    }

    public function get_production_order_count_totals($collection, $operation_id){
        $operation_id = ($operation_id == 2) ? 1 : $operation_id;
        $filtered_collection = collect($collection)->filter(function ($value, $key) use ($operation_id) {
            return ($value->operation_id == $operation_id);
        });

        $filtered_collection = collect($filtered_collection);

        $planned_collection = $filtered_collection->filter(function ($value, $key) {
            return ($value->status == 'Not Started');
        });

        $wip_collection = $filtered_collection->filter(function ($value, $key) {
            return ($value->status == 'In Progress');
        });
        
        $done_collection = $filtered_collection->filter(function ($value, $key) {
            return ($value->status == 'In Progress' && $value->produced_qty > 0);
        });
        
        $for_feedback_collection = $filtered_collection->filter(function ($value, $key) {
            return ($value->status != 'Not Started' && $value->for_feedback > 0);
        });
        
        $planned_collection = collect($planned_collection);
        $wip_collection = collect($wip_collection);
        $done_collection = collect($done_collection);
        $for_feedback_collection = collect($for_feedback_collection);

        return [
            'planned_count' => number_format($planned_collection->count()),
            'planned_qty' => number_format($planned_collection->sum('qty_to_manufacture')),
            'wip_count' => number_format($wip_collection->count()),
            'wip_qty' => number_format($wip_collection->sum('wip_qty')),
            'done_count' => number_format($done_collection->count()),
            'done_qty' => number_format($done_collection->sum('produced_qty')),
            'for_feedback_count' => number_format($for_feedback_collection->count()),
            'for_feedback_qty' => number_format($for_feedback_collection->sum('for_feedback'))
        ];
    }
    
    public function count_current_production_order($schedule_date){
        $production_orders = DB::connection('mysql_mes')->table('production_order')
            ->where('planned_start_date', $schedule_date)->whereNotIn('status', ['Cancelled', 'Closed'])
            ->selectRaw('operation_id, qty_to_manufacture, status, produced_qty, (qty_to_manufacture - produced_qty) as wip_qty, (produced_qty - feedback_qty) as for_feedback')
            ->get();
            
        $fabrication = $this->get_production_order_count_totals($production_orders, 1);
        $assembly = $this->get_production_order_count_totals($production_orders, 3);
        
        $for_feedback_production_orders = DB::connection('mysql_mes')->table('production_order')
            ->whereRaw('(feedback_qty < produced_qty)')->whereNotIn('status', ['Cancelled', 'Closed'])
            ->where('produced_qty', '>', 0)
            ->selectRaw('operation_id, qty_to_manufacture, status, produced_qty, (qty_to_manufacture - produced_qty) as wip_qty, (produced_qty - feedback_qty) as for_feedback')
            ->get();

        $for_feedback_fabrication = $this->get_production_order_count_totals($for_feedback_production_orders, 1);
        $for_feedback_assembly = $this->get_production_order_count_totals($for_feedback_production_orders, 3);

        // get scheduled painting production orders from job ticket 
        $scheduled_painting_production_orders = DB::connection('mysql_mes')->table('job_ticket')
            ->where('workstation', 'Painting')->where('planned_start_date', $schedule_date)
            ->distinct()->pluck('production_order');
        // get painting production orders
        $scheduled_painting_production_orders = DB::connection('mysql_mes')->table('production_order')
            ->whereIn('production_order', $scheduled_painting_production_orders)->whereNotIn('status', ['Cancelled', 'Closed'])
            ->selectRaw('operation_id, qty_to_manufacture, status, produced_qty, (qty_to_manufacture - produced_qty) as wip_qty, (produced_qty - feedback_qty) as for_feedback')
            ->get();

        $painting = $this->get_production_order_count_totals($scheduled_painting_production_orders, 2);

        // get painting production orders ready for feedback
        $for_feedback_painting_production_orders = DB::connection('mysql_mes')->table('production_order as po')
            ->join('job_ticket as jt', 'jt.production_order', 'po.production_order')->where('jt.workstation', 'Painting')
            ->whereRaw('(po.feedback_qty < po.produced_qty)')->whereNotIn('po.status', ['Cancelled', 'Closed'])
            ->where('po.produced_qty', '>', 0)->distinct()->pluck('po.production_order');
        // get painting production orders
        $for_feedback_painting_production_orders = DB::connection('mysql_mes')->table('production_order')
            ->whereIn('production_order', $for_feedback_painting_production_orders)->where('produced_qty', '>', 0)
            ->whereNotIn('status', ['Cancelled', 'Closed'])->whereRaw('(feedback_qty < produced_qty)')
            ->selectRaw('operation_id, qty_to_manufacture, status, produced_qty, (qty_to_manufacture - produced_qty) as wip_qty, (produced_qty - feedback_qty) as for_feedback')
            ->get();

        $for_feedback_painting = $this->get_production_order_count_totals($for_feedback_painting_production_orders, 2);

        $user_permitted_operations = DB::connection('mysql_mes')->table('user')
            ->join('operation', 'operation.operation_id', 'user.operation_id')
            ->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
            ->where('module', 'Production')->where('user_access_id', Auth::user()->user_id)
            ->select('user.operation_id', 'operation_name')->orderBy('user.operation_id', 'asc')
            ->distinct()->pluck('operation_id');
            
        $start = Carbon::parse($schedule_date)->startOfDay();
        $end = Carbon::parse($schedule_date)->endOfDay();

        // pending production orders scheduled today
        $pending_production_orders = DB::connection('mysql_mes')->table('production_order')
            ->whereDate('planned_start_date', $schedule_date)
            ->whereIn('operation_id', $user_permitted_operations)
            ->whereIn('status', ['Not Started', 'In Progress'])->get();

        $pending_production_count = count($pending_production_orders);
        $pending_qty_count = collect($pending_production_orders)->sum('qty_to_manufacture');
        
        // in progress production orders scheduled today
        $in_progress_production_orders = DB::connection('mysql_mes')->table('production_order')
            ->where('planned_start_date', $schedule_date)->where('status', 'In Progress')
            ->whereIn('operation_id', $user_permitted_operations)
            ->selectRaw('(qty_to_manufacture - produced_qty) as wip_qty')->get();

        $in_progress_production_count = count($in_progress_production_orders);
        $in_progress_qty_count = collect($in_progress_production_orders)->sum('wip_qty');

        // get total rejected qty based on scheduled date
        $reject_qty = DB::connection('mysql_mes')->table('quality_inspection')
            ->whereBetween('qa_inspection_date', [$start, $end])
            ->whereIn('status', ['QC Failed', 'QC Passed'])
            ->sum('rejected_qty');
        
        // count all ready for feedback
        $for_feedback_production_orders = DB::connection('mysql_mes')->table('production_order AS po')
            ->leftJoin('delivery_date', function($join)
            {
                $join->on( DB::raw('IFNULL(po.sales_order, po.material_request)'), '=', 'delivery_date.reference_no');
                $join->on('po.parent_item_code','=','delivery_date.parent_item_code');
            })
            ->where('po.produced_qty', '>', 0)
			->whereRaw('po.produced_qty > feedback_qty')
            ->whereNotIn('po.status', ['Cancelled', 'Closed'])
            ->whereIn('po.operation_id', $user_permitted_operations)
            ->selectRaw('(po.produced_qty - po.feedback_qty) as for_feedback')->get();

        $for_feedback_production_count = count($for_feedback_production_orders);
        $for_feedback_qty_count = collect($for_feedback_production_orders)->sum('for_feedback');

        return [
            'fab_planned' => $fabrication['planned_count'],
            'fab_planned_qty' => $fabrication['planned_qty'],
            'fab_wip' => $fabrication['wip_count'],
            'fab_wip_qty' => $fabrication['wip_qty'],
            'fab_done' => $fabrication['done_count'],
            'fab_done_qty' => $fabrication['done_qty'],
            'fab_for_feedback' => $for_feedback_fabrication['for_feedback_count'],
            'fab_for_feedback_qty' => $for_feedback_fabrication['for_feedback_qty'],

            'wa_planned' => $assembly['planned_count'],
            'wa_planned_qty' => $assembly['planned_qty'],
            'wa_wip' => $assembly['wip_count'],
            'wa_wip_qty' => $assembly['wip_qty'],
            'wa_done' => $assembly['done_count'],
            'wa_done_qty' => $assembly['done_qty'],
            'wa_for_feedback' => $for_feedback_assembly['for_feedback_count'],
            'wa_for_feedback_qty' => $for_feedback_assembly['for_feedback_qty'],

            'pa_planned' => $painting['planned_count'],
            'pa_planned_qty' => $painting['planned_qty'],
            'pa_wip' => $painting['wip_count'],
            'pa_wip_qty' => $painting['wip_qty'],
            'pa_done' => $painting['done_count'],
            'pa_done_qty' => $painting['done_qty'],
            'pa_for_feedback' => $for_feedback_painting['for_feedback_count'],
            'pa_for_feedback_qty' => $for_feedback_painting['for_feedback_qty'],

            'pending' => number_format($pending_production_count),
            'pending_qty' => number_format($pending_qty_count),
            'inProgress' => number_format($in_progress_production_count),
            'inProgress_qty' => number_format($in_progress_qty_count),
            'completed' => number_format($for_feedback_production_count),
            'completed_qty' => number_format($for_feedback_qty_count),
            'reject' =>  number_format($reject_qty),
        ];

        // $user_permitted_operations = DB::connection('mysql_mes')->table('user')
		// 	->join('operation', 'operation.operation_id', 'user.operation_id')
		// 	->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
		// 	->where('module', 'Production')->where('user_access_id', Auth::user()->user_id)
		// 	->select('user.operation_id', 'operation_name')->orderBy('user.operation_id', 'asc')
        //     ->distinct()->pluck('operation_id');
            
        // $start = Carbon::parse($schedule_date)->startOfDay();
        // $end = Carbon::parse($schedule_date)->endOfDay();

        // // pending production orders scheduled today
        // $pending_production_orders = DB::connection('mysql_mes')->table('production_order')
        //     ->where('planned_start_date', $schedule_date)
        //     ->whereIn('operation_id', $user_permitted_operations)
        //     ->where('status', 'Not Started')->get();

        // $pending_production_count = count($pending_production_orders);
        // $pending_qty_count = collect($pending_production_orders)->sum('qty_to_manufacture');
        
        // // in progress production orders scheduled today
        // $in_progress_production_orders = DB::connection('mysql_mes')->table('production_order')
        //     ->where('planned_start_date', $schedule_date)->where('status', 'In Progress')
        //     ->whereIn('operation_id', $user_permitted_operations)
        //     ->selectRaw('(qty_to_manufacture - produced_qty) as wip_qty')->get();

        // $in_progress_production_count = count($in_progress_production_orders);
        // $in_progress_qty_count = collect($in_progress_production_orders)->sum('wip_qty');

        // // get total rejected qty based on scheduled date
        // $reject_qty = DB::connection('mysql_mes')->table('quality_inspection')
        //     ->whereBetween('qa_inspection_date', [$start, $end])
        //     ->whereIn('status', ['QC Failed', 'QC Passed'])
        //     ->sum('rejected_qty');
        
        // // count all ready for feedback
        // $for_feedback_production_orders = DB::connection('mysql_mes')->table('production_order')
        //     ->whereRaw('(feedback_qty < produced_qty)')->where('status', '!=', 'Cancelled')
        //     ->where('produced_qty', '>', 0)
        //     ->whereIn('operation_id', $user_permitted_operations)
        //     ->selectRaw('(produced_qty - feedback_qty) as for_feedback')->get();

        // $for_feedback_production_count = count($for_feedback_production_orders);
        // $for_feedback_qty_count = collect($for_feedback_production_orders)->sum('for_feedback');

        // return [
        //     'pending' => number_format($pending_production_count),
        //     'pending_qty' => number_format($pending_qty_count),
        //     'inProgress' => number_format($in_progress_production_count),
        //     'inProgress_qty' => number_format($in_progress_qty_count),
        //     'completed' => number_format($for_feedback_production_count),
        //     'completed_qty' => number_format($for_feedback_qty_count),
        //     'reject' =>  number_format($reject_qty),
        // ];
    }

    public function add_shift(Request $request){
        $check_if_exit = DB::connection('mysql_mes')->table('shift')->where('shift_type', '=' ,'Regular Shift')->where('operation_id', $request->operation )->first();
        $data= $request->all();  
        $now = Carbon::now();
  
        if(empty($request->shiftcategory)){
            return response()->json(['success' => 0, 'message' => 'Please Insert Breaktime']);            
        }else{
            if (empty($check_if_exit)){
                // dd("hi");
                $arr = $request->shiftcategory;

                $ar=array_unique( array_diff_assoc( $arr, array_unique( $arr ) ) );
                if(!empty($ar)){
                    foreach($ar as $i => $r){
                        $row= $i +1;
                        return response()->json(['success' => 0, 'message' => 'Please check DUPLICATE '.$r.' at ROW '.$row ]);

                    }
                    
                }else{
                   $values1 = [
                       'time_in' => $request->time_in,
                       'time_out' => $request->time_out,
                       'breaktime_in_mins' => $request->breaktime_in_min,
                       'remarks' => $request->remarks,
                       'shift_type' => $request->shift_type,
                       'operation_id' =>$request->operation,
                       'last_modified_by' => Auth::user()->employee_name,
                       'created_by' => Auth::user()->employee_name,
                       'created_at' => $now->toDateTimeString(),
                       'last_modified_at' => $now->toDateTimeString()
                   ];
                   DB::connection('mysql_mes')->table('shift')->insert($values1);
                   $id_shift = DB::connection('mysql_mes')->table('shift')->orderBy('shift_id', 'desc')->first();
                    // dd($id_shift);
                    foreach($request->shiftcategory as $i => $row){
                        $start = Carbon::parse($request->timein[$i]);
                        $end = Carbon::parse($request->timeout[$i]);
                        $totalDuration = $end->diffInMinutes($start);

                        $breaktimelist[] = [
                            'shift_id' => $id_shift->shift_id,
                            'category' => $row,
                            'time_from' => date("H:i:s", strtotime($request->timein[$i])),
                            'time_to' => date("H:i:s", strtotime($request->timeout[$i])),
                            'breaktime_in_mins' => $totalDuration,
                            'last_modified_by' => Auth::user()->email,
                            'created_by' => Auth::user()->email,
                            'created_at' => $now->toDateTimeString()
                            ];
                   }
                   
                   
                   DB::connection('mysql_mes')->table('breaktime')->insert($breaktimelist);
                   return response()->json(['success' => 1, 'message' => 'Shift successfully added']);
                }

           }elseif ($check_if_exit->shift_type == $request->shift_type) {
               return response()->json(['success' => 0, 'message' => 'Shift already exists']);            
           }else{
                $arr = $request->shiftcategory;

                $ar=array_unique( array_diff_assoc( $arr, array_unique( $arr ) ) );
                if(!empty($ar)){
                    foreach($ar as $i => $r){
                        $row= $i +1;
                        return response()->json(['success' => 0, 'message' => 'Please check DUPLICATE '.$r.' at ROW '.$row ]);

                    }
                    
                }else{
                    $values1 = [
                        'time_in' => $request->time_in,
                        'time_out' => $request->time_out,
                        'breaktime_in_mins' => $request->breaktime_in_min,
                        'remarks' => $request->remarks,
                        'shift_type' => $request->shift_type,
                        'operation_id' =>$request->operation,
                        'last_modified_by' => Auth::user()->employee_name,
                        'created_by' => Auth::user()->employee_name,
                        'created_at' => $now->toDateTimeString(),
                        'last_modified_at' => $now->toDateTimeString()
                    ];
                    DB::connection('mysql_mes')->table('shift')->insert($values1);
                    $id_shift = DB::connection('mysql_mes')->table('shift')->orderBy('shift_id', 'desc')->first();
                    // dd($id_shift);
                    foreach($request->shiftcategory as $i => $row){
                           
                        $start = Carbon::parse($request->timein[$i]);
                        $end = Carbon::parse($request->timeout[$i]);
                        $totalDuration = $end->diffInMinutes($start);

                        $breaktimelist[] = [
                            'shift_id' => $id_shift->shift_id,
                            'category' => $row,
                            'time_from' => date("H:i:s", strtotime($request->timein[$i])),
                            'time_to' => date("H:i:s", strtotime($request->timeout[$i])),
                            'breaktime_in_mins' => $totalDuration,
                            'last_modified_by' => Auth::user()->email,
                            'created_by' => Auth::user()->email,
                            'created_at' => $now->toDateTimeString()
                            ];
                  }
                  
                  
                  DB::connection('mysql_mes')->table('breaktime')->insert($breaktimelist);
                    return response()->json(['success' => 1, 'message' => 'Shift successfully added']);
                }
               
           }
        }
        
    }
    public function edit_shift(Request $request){
        $now = Carbon::now();
        $check_if_exit = DB::connection('mysql_mes')->table('shift')->where('shift_type', '=' ,'Regular Shift')->where('operation_id', $request->operation )->first();
       //insert if no regular shift existing in database in particular operation
        if(empty($check_if_exit)){
            
            // for delete
            if ($request->old_break) {
                $delete_break= DB::connection('mysql_mes')
                    ->table('breaktime')
                    ->where('shift_id', $request->shift_id)
                    ->whereIn('id', $request->old_break)
                    ->whereNotIn('id', $request->oldshiftbreakid)
                    ->delete();
            }
            // for insert
            if ($request->newshiftcategory) {
                foreach($request->newshiftcategory as $i => $row){
                    $start = Carbon::parse($request->newtimein[$i]);
                    $end = Carbon::parse($request->newtimeout[$i]);
                    $totalDuration = $end->diffInMinutes($start);

                    $new_breaktime[] = [
                        'shift_id'=> $request->shift_id,
                        'category' => $row,
                        'time_from' => date("H:i:s", strtotime($request->newtimein[$i])),
                        'time_to' => date("H:i:s", strtotime($request->newtimeout[$i])),
                        'breaktime_in_mins' => $totalDuration,
                        'last_modified_by' => Auth::user()->email,
                        'created_by' => Auth::user()->email,
                        'created_at' => $now->toDateTimeString()
                    ];
                }

                DB::connection('mysql_mes')->table('breaktime')->insert($new_breaktime);
            }
            //update
            if ($request->oldshiftcategory) {
                foreach($request->oldshiftcategory as $i => $row){
                    $start = Carbon::parse($request->oldtimein[$i]);
                    $end = Carbon::parse($request->oldtimeout[$i]);
                    $totalDuration = $end->diffInMinutes($start);

                    $update_breaktime= [
                        'category' => $row,
                        'time_from' => date("H:i:s", strtotime($request->oldtimein[$i])),
                        'time_to' => date("H:i:s", strtotime($request->oldtimeout[$i])),
                        'breaktime_in_mins' => $totalDuration,
                        'last_modified_by' => Auth::user()->email
                    ];
                    $shift_id_forupdate= $request->oldshiftbreakid[$i];
                    DB::connection('mysql_mes')->table('breaktime')->where('id',$shift_id_forupdate)->update($update_breaktime);

                }

            }
            $breaktime_in_minutes=DB::connection('mysql_mes')->table('breaktime')->where('shift_id', $request->shift_id)->max('breaktime_in_mins');
            if($breaktime_in_minutes != null){
                $start = Carbon::parse($request->time_in);
                $end = Carbon::parse($request->time_out);
                $totalDuration = $end->diffInMinutes($start);

                if($breaktime_in_minutes >= 60){
                    $hrs_of_work= round((($totalDuration - $breaktime_in_minutes)/ 60), 2);
                }else{
                    $hrs_of_work= round((($totalDuration)/ 60), 2);
                }   
            }else{
                $start = Carbon::parse($request->time_in);
                $end = Carbon::parse($request->time_out);
                $totalDuration = $end->diffInMinutes($start);
                $hrs_of_work= round((($totalDuration)/ 60), 2);
            }
            $values1 = [
                'time_in' => $request->time_in,
                'time_out' => $request->time_out,
                'hrs_of_work' => $hrs_of_work,
                'operation_id' =>$request->operation,
                'remarks' => $request->remarks,
                'shift_type' => $request->shift_type,
                'last_modified_by' => Auth::user()->employee_name,
                'last_modified_at' => $now->toDateTimeString()
            ];
            DB::connection('mysql_mes')->table('shift')->where('shift_id', $request->shift_id)->update($values1);
            return response()->json(['success' => 1, 'message' => 'Shift successfully updated']);
            
        }else{//check if no changes in shift and operation
            if($request->shift_type == $request->old_shift_type && $request->old_operation_id == $request->operation){
                
                // for delete
                if ($request->old_break) {
                    $delete_break= DB::connection('mysql_mes')
                        ->table('breaktime')
                        ->where('shift_id', $request->shift_id)
                        ->whereIn('id', $request->old_break)
                        ->whereNotIn('id', $request->oldshiftbreakid)
                        ->delete();
                }
                // for insert
                if ($request->newshiftcategory) {
                    foreach($request->newshiftcategory as $i => $row){
                        $start = Carbon::parse($request->newtimein[$i]);
                        $end = Carbon::parse($request->newtimeout[$i]);
                        $totalDuration = $end->diffInMinutes($start);

                        $new_breaktime[] = [
                            'shift_id'=> $request->shift_id,
                            'category' => $row,
                            'time_from' => date("H:i:s", strtotime($request->newtimein[$i])),
                            'time_to' => date("H:i:s", strtotime($request->newtimeout[$i])),
                            'breaktime_in_mins' => $totalDuration,
                            'last_modified_by' => Auth::user()->email,
                            'created_by' => Auth::user()->email,
                            'created_at' => $now->toDateTimeString()
                        ];
                    }

                    DB::connection('mysql_mes')->table('breaktime')->insert($new_breaktime);
                }
                //update
                if ($request->oldshiftcategory) {
                    foreach($request->oldshiftcategory as $i => $row){
                        $start = Carbon::parse($request->oldtimein[$i]);
                        $end = Carbon::parse($request->oldtimeout[$i]);
                        $totalDuration = $end->diffInMinutes($start);

                        $update_breaktime= [
                            'category' => $row,
                            'time_from' => date("H:i:s", strtotime($request->oldtimein[$i])),
                            'time_to' => date("H:i:s", strtotime($request->oldtimeout[$i])),
                            'breaktime_in_mins' => $totalDuration,
                            'last_modified_by' => Auth::user()->email
                        ];
                        $shift_id_forupdate= $request->oldshiftbreakid[$i];
                        DB::connection('mysql_mes')->table('breaktime')->where('id',$shift_id_forupdate)->update($update_breaktime);

                    }

                }
                $breaktime_in_minutes=DB::connection('mysql_mes')->table('breaktime')->where('shift_id', $request->shift_id)->max('breaktime_in_mins');
                if($breaktime_in_minutes != null){
                    $start = Carbon::parse($request->time_in);
                    $end = Carbon::parse($request->time_out);
                    $totalDuration = $end->diffInMinutes($start);

                    if($breaktime_in_minutes >= 60){
                        $hrs_of_work= round((($totalDuration - $breaktime_in_minutes)/ 60), 2);
                    }else{
                        $hrs_of_work= round((($totalDuration)/ 60), 2);
                    }   
                }else{
                    $start = Carbon::parse($request->time_in);
                    $end = Carbon::parse($request->time_out);
                    $totalDuration = $end->diffInMinutes($start);
                    $hrs_of_work= round((($totalDuration)/ 60), 2);
                }

                $values1 = [
                    'time_in' => $request->time_in,
                    'time_out' => $request->time_out,
                    'hrs_of_work' => $hrs_of_work,
                    'operation_id' =>$request->operation,
                    'remarks' => $request->remarks,
                    'shift_type' => $request->shift_type,
                    'last_modified_by' => Auth::user()->employee_name,
                    'last_modified_at' => $now->toDateTimeString()
                ];
                DB::connection('mysql_mes')->table('shift')->where('shift_id', $request->shift_id)->update($values1);
                return response()->json(['success' => 1, 'message' => 'Shift successfully updated']);
                
            }elseif($check_if_exit->shift_type == $request->shift_type)  {
                //if the there is existing regular shift
                return response()->json(['success' => 0, 'message' => 'Shift already exists']);  
            }
            //changes with no conflicts
            else{

               
                // for delete
                if ($request->old_break) {
                    $delete_break=DB::connection('mysql_mes')
                        ->table('breaktime')
                        ->where('shift_id', $request->shift_id)
                        ->whereIn('id', $request->old_break)
                        ->whereNotIn('id', $request->oldshiftbreakid)
                        ->delete();
                }
                // for insert
                if ($request->newshiftcategory) {
                    foreach($request->newshiftcategory as $i => $row){
                        $start = Carbon::parse($request->newtimein[$i]);
                        $end = Carbon::parse($request->newtimeout[$i]);
                        $totalDuration = $end->diffInMinutes($start);

                        $new_breaktime[] = [
                            'shift_id'=> $request->shift_id,
                            'category' => $row,
                            'time_from' => date("H:i:s", strtotime($request->newtimein[$i])),
                            'time_to' => date("H:i:s", strtotime($request->newtimeout[$i])),
                            'breaktime_in_mins' => $totalDuration,
                            'last_modified_by' => Auth::user()->email,
                            'created_by' => Auth::user()->email,
                            'created_at' => $now->toDateTimeString()
                        ];
                    }

                    DB::connection('mysql_mes')->table('breaktime')->insert($new_breaktime);
                }
                //update
                if ($request->oldshiftcategory) {
                    foreach($request->oldshiftcategory as $i => $row){
                        $start = Carbon::parse($request->oldtimein[$i]);
                        $end = Carbon::parse($request->oldtimeout[$i]);
                        $totalDuration = $end->diffInMinutes($start);

                        $update_breaktime= [
                            'category' => $row,
                            'time_from' => date("H:i:s", strtotime($request->oldtimein[$i])),
                            'time_to' => date("H:i:s", strtotime($request->oldtimeout[$i])),
                            'breaktime_in_mins' => $totalDuration,
                            'last_modified_by' => Auth::user()->email
                        ];
                        $shift_id_forupdate= $request->oldshiftbreakid[$i];
                        DB::connection('mysql_mes')->table('breaktime')->where('id',$shift_id_forupdate)->update($update_breaktime);

                    }
                }
                $breaktime_in_minutes=DB::connection('mysql_mes')->table('breaktime')->where('shift_id', $request->shift_id)->max('breaktime_in_mins');
                if($breaktime_in_minutes != null){
                    $start = Carbon::parse($request->time_in);
                    $end = Carbon::parse($request->time_out);
                    $totalDuration = $end->diffInMinutes($start);

                    if($breaktime_in_minutes >= 60){
                        $hrs_of_work= round((($totalDuration - $breaktime_in_minutes)/ 60), 2);
                    }else{
                        $hrs_of_work= round((($totalDuration)/ 60), 2);
                    }   
                }else{
                    $start = Carbon::parse($request->time_in);
                    $end = Carbon::parse($request->time_out);
                    $totalDuration = $end->diffInMinutes($start);
                    $hrs_of_work= round((($totalDuration)/ 60), 2);
                }
                $values1 = [
                    'time_in' => $request->time_in,
                    'time_out' => $request->time_out,
                    'hrs_of_work' => $hrs_of_work,
                    'operation_id' =>$request->operation,
                    'remarks' => $request->remarks,
                    'shift_type' => $request->shift_type,
                    'last_modified_by' => Auth::user()->employee_name,
                    'last_modified_at' => $now->toDateTimeString()
                ];
                DB::connection('mysql_mes')->table('shift')->where('shift_id', $request->shift_id)->update($values1);

                return response()->json(['success' => 1, 'message' => 'Shift successfully updated']);
            }
        }
    }
    public function delete_shift(Request $request){

            DB::connection('mysql_mes')->table('shift')->where('shift_id', $request->shift_id)->delete();

        return response()->json(['success' => 1, 'message' => 'Shift successfully updated']);

    }
    public function tbl_shift_list(Request $request){
        $shift_list= DB::connection('mysql_mes')->table('shift')
                ->join('operation', 'operation.operation_id', 'shift.operation_id')
                ->select('shift.*','operation.operation_name')->paginate(15);

        return view('tables.tbl_shift_list', compact('shift_list'));
    }
    public function add_operation(Request $request){
         $now = Carbon::now();
         if (DB::connection('mysql_mes')->table('operation')
                ->where('operation_name', '=', $request->operation_name)
                ->exists()){
                return response()->json(['success' => 0, 'message' => 'Operation already exists']);            
            }
            else{
                $values1 = [
                    'operation_name' => $request->operation_name,
                    'description' => $request->operation_desc,
                    'last_modified_by' => Auth::user()->employee_name,
                    'created_by' => Auth::user()->employee_name,
                    'created_at' => $now->toDateTimeString(),
                    'last_modified_at' => $now->toDateTimeString()
                ];
                $values2 = [
                    'name' => $request->operation_name,
                    'description' =>  $request->operation_desc,
                    'creation' =>$now->toDateTimeString(),
                    'modified' =>$now->toDateTimeString(),
                    'modified_by' => Auth::user()->email,
                    'owner' => Auth::user()->email,
                    'idx' => 0,
                    'workstation'=> null

                ];

            DB::connection('mysql_mes')->table('operation')->insert($values1);
            DB::connection('mysql')->table('tabOperation')->insert($values2);
        }
        return response()->json(['success' => 1, 'message' => 'Operation successfully added']);

    }
    public function edit_operation(Request $request){
         $now = Carbon::now();
                $values1 = [
                    'operation_name' => $request->operation_name,
                    'description' => $request->operation_desc,
                    'last_modified_by' => Auth::user()->employee_name,
                    'last_modified_at' => $now->toDateTimeString()
                ];
                $values2 = [
                    'name' => $request->operation_name,
                    'description' =>  $request->operation_desc,
                    'modified' =>$now->toDateTimeString(),
                    'modified_by' => Auth::user()->email,
                    'idx' => 0,
                    'workstation'=> null

                ];

            DB::connection('mysql_mes')->table('operation')->where('operation_id', $request->operation_id)->update($values1);
            DB::connection('mysql')->table('tabOperation')->where('name', $request->old_operation)->update($values2);
        return response()->json(['success' => 1, 'message' => 'Operation successfully updated']);
    }
 
    public function tbl_operation_list(Request $request){
        $shift_list = DB::connection('mysql_mes')->table('operation')
            ->where(function($q) use ($request) {
                    $q->where('operation_name', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('description', 'LIKE', '%'.$request->search_string.'%');
            })
            ->orderBy('operation_id', 'desc')->paginate(15);

        return view('tables.tbl_operation_list', compact('shift_list'));
    }
    public function add_shift_schedule(Request $request){
        $now = Carbon::now();

        //schedule_planned_start_date
        if(!empty($request->prodname)){
            if($request->planned_start_datepicker == null){
                return response()->json(['success' => 0, 'message' => 'Please Select Planned Start Date ']);
            }else{
                if($request->operation_id == 2){
                    $prod = [
                        'planned_start_date' => $request->planned_start_datepicker,
                    ];
                    // DB::connection('mysql_mes')->table('job_ticket')->where('production_order',$request->prodname)->where('workstation','Painting')->update($val_sched);
                    foreach($request->prodname as $i => $row){
                        if(DB::connection('mysql_mes')->table('job_ticket as jt')
							->join('time_logs as tl', 'jt.job_ticket_id','tl.job_ticket_id')
							->where('jt.production_order', $row)
							->where('tl.status', "In Progress")
							->select('tl.status as stat')
							->exists()){
						}else{
							DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $row)->where('workstation','Painting')->update($prod);   
						}
                    }
                }else{
                    $prods = [
                        'is_scheduled' =>  1,
                        'planned_start_date' => $request->planned_start_datepicker,
                        'last_modified_by' => Auth::user()->email
                    ];
                    foreach($request->prodname as $i => $row){
                        if(DB::connection('mysql_mes')->table('job_ticket as jt')
							->join('spotwelding_qty as spotpart', 'spotpart.job_ticket_id','jt.job_ticket_id')
							->where('jt.production_order', $row)
							->where('spotpart.status', "In Progress")
							->exists()){
						}else{
							if(DB::connection('mysql_mes')->table('job_ticket as jt')
							->join('time_logs as tl', 'jt.job_ticket_id','tl.job_ticket_id')
							->where('jt.production_order', $row)
							->where('tl.status', "In Progress")
							->exists()){
							}else{
								DB::connection('mysql_mes')->table('production_order')->where('production_order', $row)->update($prods);
							}
						}
                    }
                }
            }
        }
        //add_shift_schedule
        if(empty($request->shifttype)){
            $arr= [];
            return response()->json(['success' => 0, 'message' => 'No shift selected' ]);
        }else{
            $arr=$request->shifttype;
        }
       $data= (array_count_values($arr));
       $special_shift = array_key_exists('Special Shift', $data) ? $data['Special Shift'] : 0;
        if($special_shift > 1){
            return response()->json(['success' => 0, 'message' => 'Please check DUPLICATE Special Shift' ]);
                
        }else{
            if ($request->old_shift_sched) {
                if($request->oldshift_sched_id == null ){
                    DB::connection('mysql_mes')
                    ->table('shift_schedule')
                    ->whereDate('date',$request->date)->delete();
                }else{
                    $delete_shift=DB::connection('mysql_mes')
                    ->table('shift_schedule')
                    ->whereIn('shift_schedule_id', $request->old_shift_sched)
                    ->whereNotIn('shift_schedule_id', $request->oldshift_sched_id)
                    ->delete();
                }
            }
            // for insert
            if ($request->newshift) {
                foreach($request->newshift as $i => $row){
                    $new_shift_sched[] = [
                        'shift_id'=> $row,
                        'date' =>  $request->date,
                        'scheduled_by' => Auth::user()->employee_name,
                        'last_modified_by' => Auth::user()->email,
                        'created_by' => Auth::user()->email,
                        'created_at' => $now->toDateTimeString()
                    ];
                }
                DB::connection('mysql_mes')->table('shift_schedule')->insert($new_shift_sched);
            }
            //update
            if ($request->oldshift) {
                foreach($request->oldshift as $i => $row){
                    $update_shift= [
                        'shift_id'=> $row,
                        'date' =>  $request->date,
                        'last_modified_by' => Auth::user()->email,
                    ];
                    $shift_id_forupdate= $request->oldshift_sched_id[$i];
                    DB::connection('mysql_mes')->table('shift_schedule')->where('shift_schedule_id',$shift_id_forupdate)->update($update_shift);
                }
            }   
        }

        if($request->pagename == "calendar"){
            $get_data=$this->get_production_schedule_calendar($request->operation_id);
             return $get_data;
        }else{
            return response()->json(['success' => 1, 'message' => 'Successfully updated', "reload_tbl" => $request->date_reload_tbl]);
        }
    }

    public function get_tbl_shiftsched_list(Request $request){
        $operation= ($request->operation == 0) ? 2 : $request->operation;
        $shift_sched_list= DB::connection('mysql_mes')
                ->table('shift_schedule')
                ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
                ->join('operation', 'operation.operation_id', 'shift.operation_id')
                ->where('operation.operation_id','like','%'. $operation.'%')
                ->whereDate('shift_schedule.date','like','%'. $request->date_sched.'%')
                ->select('shift_schedule.*', 'operation.operation_name', 'shift.shift_type', 'shift.time_in', 'shift.time_out')
                ->get();
        $shift_list=DB::connection('mysql_mes')
                ->table('shift')
                ->join('operation', 'operation.operation_id', 'shift.operation_id')
                ->where('shift.shift_type','!=', 'Regular Shift')
                ->where('shift.operation_id', $operation)
                ->get();
        return response()->json(['success' => 1, 'shift' => $shift_sched_list, 'shift_type' =>$shift_list]); 
    }
    public function edit_shift_schedule(Request $request){
         $now = Carbon::now();
                $values1 = [
                    'shift_id' => $request->shift_id,
                    'date' => $request->sched_date,
                    'scheduled_by' => Auth::user()->employee_name,
                    'remarks' => $request->remarks,
                    'last_modified_by' => Auth::user()->employee_name,
                    'last_modified_at' => $now->toDateTimeString()
                ];
            DB::connection('mysql_mes')->table('shift_schedule')->where('shift_schedule_id', $request->shift_sched_id)->update($values1);

        return response()->json(['success' => 1, 'message' => 'Shift schedule successfully updated']);

    }
    public function delete_shift_sched(Request $request){

            DB::connection('mysql_mes')->table('shift_schedule')->where('shift_schedule_id', $request->shift_sched_id)->delete();

        return response()->json(['success' => 1, 'message' => 'Shift schedule successfully deleted']);

    }
    public function get_shift_details($shift_sched_id){
        $shift_details = DB::connection('mysql_mes')->table('shift')
        ->where('shift_id', $shift_sched_id)->first();

        return response()->json($shift_details);

    }
    
    public function shift_page(){

        $operation_list=DB::connection('mysql_mes')
                ->table('operation')
                ->get();
        $shift_list=DB::connection('mysql_mes')
                ->table('shift')
                ->join('operation', 'operation.operation_id', 'shift.operation_id')
                ->where('shift.shift_type','!=', 'Regular Shift')
                ->get();
        $date = new Carbon();


        $date->addDays(93);
        $calendar =DB::connection('mysql_essex')->table('holidays')
            ->whereBetween('holiday_date',[new Carbon(),$date])->get();

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

        return view('shift', compact('operation_list', 'shift_list', 'out_today', 'calendar'));
    }
    public function get_shift_list_option(Request $request){
        $output = '<option value=""></option>';
            $operation= ($request->operation == 0) ? 2 : $request->operation;
            $shift_list=DB::connection('mysql_mes')
                ->table('shift')
                ->join('operation', 'operation.operation_id', 'shift.operation_id')
                ->where('shift.shift_type','!=', 'Regular Shift')
                ->where('shift.operation_id', $operation)
                ->get();

            foreach($shift_list as $row)
                 {
                $output .= '<option value="'.$row->shift_id.'">'.$row->shift_type.'-'.$row->operation_name.'</option>';
                 }

            // dd($output);
        return $output;
    }
    public function item_status_tracking_page(){

        return view('item_status_tracking');
    }
      
    public function get_bom($bom, $guide_id, $item_code, $parent_item_code){
        try {
            $bom1 = DB::connection('mysql')->table('tabBOM Item as bom')
            ->join('tabItem as item', 'item.name', 'bom.item_code')
            ->whereNotIn('item.item_group', ['Raw Material', 'Factory Supplies'])
            // ->whereIn('item.item_classification', ['RA - REFLECTOR ASSEMBLY', 'SA - Sub Assembly', 'HO - Housing', 'DI - Diffuser','FA - FRAME ASSEMBLY','FR - FRAME','FPA - FRONT PLATE ASSEMBLY','WA - WIREGUARD ASSEMBLY'])
            ->whereNotIn('item.item_classification', ['BP - Battery Pack', 'WW - Wall Washer Luminaire', 'WL - Wall Lights'])
            ->where('bom.docstatus', '<', 2)
            ->where('bom.parent', $bom)
            ->select('bom.*', 'item.parts_category')
            ->orderBy('bom.idx', 'asc')->get();

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
                ->whereNotIn('po.status', ['Cancelled', 'Closed'])
                ->where(function($q) use ($guide_id) {
                        $q->Where('po.sales_order', $guide_id)
                            ->orWhere('po.material_request', $guide_id);
                    })
                ->where('item_code', $item->item_code)
                ->where('sub_parent_item_code', $item_code)
                ->where('parent_item_code', $parent_item_code)
                ->select('production_order', 'planned_start_date', 'qty_to_manufacture', 'produced_qty','status','produced_qty','qty_to_manufacture', 'parts_category')
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
                        foreach($jt_details1 as $row){
                            if(DB::connection('mysql_mes')
                            ->table('time_logs')
                            ->where('job_ticket_id', $row->job_ticket_id)
                            ->exists()){

                                $jt_details =  DB::connection('mysql_mes')->table('job_ticket as jt')
                                ->join('time_logs as tl', 'tl.job_ticket_id', 'jt.job_ticket_id')
                                ->where('jt.production_order', $production->production_order)->get();


                                    $jt_details23 =  DB::connection('mysql_mes')->table('job_ticket as jt')
                                    ->join('time_logs as tl', 'tl.job_ticket_id', 'jt.job_ticket_id')
                                    ->where('jt.production_order', $production->production_order)
                                    ->where('tl.to_time', '!=', null)
                                    ->where('jt.job_ticket_id', $row->job_ticket_id)
                                    ->select(DB::raw('MAX(tl.to_time) as to_time'), DB::raw('MIN(tl.from_time) as from_time'))
                                    ->first();
                                    $end_datee= $jt_details23->to_time;
                                    $start_datee= $jt_details23->from_time;


                            }else{
                                if($row->workstation == "Spotwelding"){
                                    $jt_details23 =  DB::connection('mysql_mes')->table('job_ticket as jt')
                                    ->leftJoin('spotwelding_part as spart', 'spart.housing_production_order', 'jt.production_order')
                                    ->leftJoin('spotwelding_qty as qpart', 'qpart.job_ticket_id', 'jt.job_ticket_id')
                                    ->where('spart.housing_production_order', $production->production_order)
                                    ->where('qpart.to_time', '!=', null)
                                    ->where('qpart.job_ticket_id',  $row->job_ticket_id)
                                    ->select(DB::raw('MAX(qpart.to_time) as to_time'), DB::raw('MIN(qpart.from_time) as from_time'))
                                    ->first();
                                    $end_datee= $jt_details23->to_time;
                                    $start_datee= $jt_details23->from_time;
                                }else{
                                    $end_datee= null;
                                    $start_datee= null;
                                    // $jt_details='hello';
                                    $jt_details23='hello';
                                }
                                
    
                                // dd($jt_details);
                            }
                            $data2[] = [
                                'date_from' => $start_datee,
                                'date_to' => $end_datee,
                                'workstation' => $row->workstation

                            ];
                            
                            
                        }

                        $start_date = collect($data2)->min('date_from');
                        $end_date = collect($data2)->max('date_to');
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
                        
                        $jt = DB::connection('mysql_mes')->table('job_ticket as jt')
                            ->where('production_order',  $production->production_order)->get();
                        
                        $produced = DB::connection('mysql_mes')->table('production_order')->where('production_order',  $production->production_order)->select('produced_qty')->first();
                        $total_workstation = collect($jt)->count();
                        $total_unassigned = collect($jt)->where('status', 'Pending')->count();
                        $total_inprocess = collect($jt)->where('status', '!=', 'Completed')->count();
    
                        if ($total_workstation == $total_unassigned) {
                            $stat= 'Not Started';
                            $done= 0;
                        }else{
                            if ($total_inprocess > 0) {
                                $stat= 'In Progress';
                                $done= $produced->produced_qty;
                            }else{
                                $stat= 'Completed';
                                $done= $produced->produced_qty;
                            }
    
                        }
    
    
                        // $job_ticket_status = DB::connection('mysql_mes')->table('job_ticket')
                        // ->where('production_order', $production->production_order)
                    
                        $production_order_no= $production->production_order;
                        $planned_start_date= $production->planned_start_date;
                        
                        // $duration=null;
                        // $duration=collect($jt_details)->sum('duration');
    
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
    public function production_mes($item_code, $sales_order){
      $production= DB::connection('mysql_mes')->table('production_order AS po')
            ->whereNotIn('po.status', ['Cancelled', 'Closed'])
            ->where(function($q) use ($sales_order) {
                    $q->Where('po.sales_order', $sales_order)
                        ->orWhere('po.material_request', $sales_order);
                })
            ->where('item_code', $item_code)
            ->select('production_order', 'planned_start_date', 'qty_to_manufacture', 'produced_qty')
            ->first(); 


            
            
            if ($production != null) {
                $jt_details =  DB::connection('mysql_mes')->table('job_ticket')
                ->where('production_order', $production->production_order)->get();

                $jt_details1 =  DB::connection('mysql_mes')->table('job_ticket')
                ->where('production_order', $production->production_order)
                ->select(DB::raw('MAX(to_time) as to_time'))
                ->first();
                $prod_jtticket=[];

                $prod_jtticket[]=[
                    'production_order' => $production->production_order,
                    'planned_start_date' => $production->planned_start_date,
                    'end_date' => $jt_details1->to_time,
                    'collect' => collect($jt_details)->sum('hours')

                ];
                    $production1 = $prod_jtticket ;



            }else{
                $production1= null;
            }

    return $production1; 

    }
    
    public function production_schedule_calendar($operation_id){
        $permissions = $this->get_user_permitted_operation();
        switch ($operation_id) {
            case 1:
                $operation_name = 'Fabrication';
                break;
            case 2:
                $operation_name = 'Painting';
                break;
            default:
                $operation_name = 'Wiring and Assembly';
                break;
        }

        return view('production_schedule_calendar', compact('operation_id','operation_name', 'permissions'));
    }

    public function get_production_schedule_calendar($operation_id){
        if($operation_id == 2){
            $prod = DB::connection('mysql_mes')->table('job_ticket as jt')
                ->join('production_order as pro','pro.production_order','jt.production_order')
                ->leftJoin('delivery_date', function($join){
                    $join->on( DB::raw('IFNULL(pro.sales_order, pro.material_request)'), '=', 'delivery_date.reference_no');
                    $join->on('pro.parent_item_code','=','delivery_date.parent_item_code');
                })
                // ->where('pro.status','!=', 'Cancelled')
                ->whereNotIn('pro.status', ['Cancelled', 'Closed'])
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
        }else{
            $prod = DB::connection('mysql_mes')->table('production_order')
                ->leftJoin('delivery_date', function($join){
                    $join->on( DB::raw('IFNULL(production_order.sales_order, production_order.material_request)'), '=', 'delivery_date.reference_no');
                    $join->on('production_order.parent_item_code','=','delivery_date.parent_item_code');
                })
                ->where('production_order.status','!=', 'Cancelled')
                ->where('production_order.planned_start_date','!=', null)
                ->distinct('production_order.customer','production_order.sales_order','production_order.material_request', 'production_order.production_order')
                ->where('production_order.operation_id', $operation_id)
                ->select('production_order.customer', 'production_order.sales_order', 'production_order.planned_start_date', 'production_order.material_request','production_order.delivery_date', 'production_order.production_order','production_order.status','production_order.item_code','production_order.qty_to_manufacture','production_order.description','production_order.stock_uom','production_order.parent_item_code', 'delivery_date.rescheduled_delivery_date')
                ->get();
            // dd($prod);

            $data = array();
            foreach ($prod as $rows) {
                $guide_id = ($rows->sales_order == null) ? $rows->material_request : $rows->sales_order;

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

                $date = date('Y-m-d', strtotime($rows->planned_start_date));

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
                    'delivery_date' => ($rows->rescheduled_delivery_date == null)? $rows->delivery_date: $rows->rescheduled_delivery_date, //show new reschedule delivery date or the current delivery date based on validation
                );
            }
        }
        return $data;
    }

    public function get_production_painting(){
        $mes_user_operations = DB::connection('mysql_mes')->table('user')
            ->join('operation', 'operation.operation_id', 'user.operation_id')
            ->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
            ->where('module', 'Production')
            ->where('user_access_id', Auth::user()->user_id)->pluck('operation_name')->toArray();

        $jobtickets_production=DB::connection('mysql_mes')->table('job_ticket as jt')
                    ->join('production_order as pro','pro.production_order', 'jt.production_order')
                    ->where('jt.planned_start_date', null)
                    ->where('pro.status', '!=', 'Cancelled')
                    ->where('jt.workstation', 'Painting')
                    ->select('pro.production_order', 'jt.workstation', 'pro.customer', 'pro.delivery_date','pro.description', 'pro.qty_to_manufacture','pro.item_code','pro.stock_uom','pro.project','pro.classification','pro.parts_category', 'pro.sales_order', 'pro.material_request', 'pro.produced_qty')
                    ->distinct('pro.production_order','pro.customer', 'pro.delivery_date','pro.description', 'pro.qty_to_manufacture','pro.item_code','pro.stock_uom','pro.project','pro.classification','pro.parts_category', 'pro.sales_order', 'pro.material_request',  'pro.produced_qty')
                    ->whereNotIn('pro.status', ['Completed', 'Stopped', 'Cancelled'])
                    ->orderBy('pro.created_at', 'desc')
                    ->get();

        $unscheduled = [];
        foreach ($jobtickets_production as $row) {

                    $jt = DB::connection('mysql_mes')->table('job_ticket as jt')
                            ->where('production_order',  $row->production_order)->get();

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
                        
                        $total_workstation = collect($jt)->where('workstation','!=', 'Painting')->count();
                        $total_completed = collect($jt)->where('status', 'Completed')->count();
                        $total_completed_except = collect($jt)->where('status', 'Completed')->where('workstation','!=', 'Painting')->count();
    
                        if ($total_workstation == $total_completed) {
                            $stat= 'Ready';
                        }else{
                            
                            $spotlogs=DB::connection('mysql_mes')->table('job_ticket as jt')
                                ->leftJoin('spotwelding_qty as spotpart', 'spotpart.job_ticket_id','jt.job_ticket_id')
                                ->join('process as p','p.process_id','jt.process_id')
                                ->where('jt.production_order', $row->production_order)
                                ->orderBy('spotpart.last_modified_at', 'desc')
                                ->select(DB::raw('(SELECT MAX(last_modified_at) FROM spotwelding_qty WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS last_modified_at'),'p.process_name','jt.production_order','jt.job_ticket_id','jt.workstation');  

                            $timelogs=DB::connection('mysql_mes')->table('job_ticket as jt')
                                ->leftJoin('time_logs as tl', 'jt.job_ticket_id','tl.job_ticket_id')
                                ->join('process as p','p.process_id','jt.process_id')
                                ->where('jt.production_order', $row->production_order)
                                ->select('tl.last_modified_at','p.process_name','jt.production_order','jt.job_ticket_id','jt.workstation')
                                ->union($spotlogs);                         
                           
                            
                            $groupby_log = DB::connection('mysql_mes')->query()->fromSub($timelogs,'logs')
                                ->select('last_modified_at', 'process_name', 'job_ticket_id','workstation')
                                ->orderBy('last_modified_at', 'DESC')->first();
                            $stat=($groupby_log->last_modified_at == null)? 'Not Started': $groupby_log->workstation;
                        }

                        $production_order_status=DB::connection('mysql_mes')->table('production_order as pro')
                        ->join('job_ticket as jt','pro.production_order','jt.production_order')
                        ->where('jt.workstation','Painting')
                        ->where('pro.production_order', $row->production_order)
                        ->select('pro.status','jt.sequence')
                        ->first();

                        $stripfromcomma =strtok($row->description, ",");

                        $timelogs_inprogress=DB::connection('mysql_mes')->table('job_ticket as jt')
                                ->leftJoin('time_logs as tl', 'jt.job_ticket_id','tl.job_ticket_id')
                                ->join('process as p','p.process_id','jt.process_id')
                                ->where('jt.production_order', $row->production_order)
                                ->where('tl.status', "In Progress")
                                ->where('jt.workstation', 'Painting')
                                ->select('tl.status as stat')->first();
                    
                        $drag = empty($timelogs_inprogress->stat)? "move":"not_move";

                    $unscheduled[] = [
                        'id' => $row->production_order,
                        'status' => $status,
                        'name' => $row->production_order,
                        'customer' => $row->customer,
                        'delivery_date' => $row->delivery_date,
                        'production_item' => $row->item_code,
                        'production_order' => $row->production_order,
                        'description' => $row->description,
                        'strip' => $stripfromcomma,
                        'parts_category' => $row->parts_category,
                        'qty' => $row->qty_to_manufacture,
                        'stock_uom' => $row->stock_uom,
                        'produced_qty'=> $row->produced_qty,
                        'sales_order' =>($row->sales_order == null) ? $row->material_request:$row->sales_order,
                        'classification' => $row->classification,
                        'prod_status' => $production_order_status->status,
                        'process_stat' =>$stat,
                        'order_no' =>$production_order_status->sequence,
                        'drag' => $drag
                        ];
                
            
        }
        // dd($unscheduled);

        $period = CarbonPeriod::create(now()->subDays(1), now()->addDays(6));

        // Iterate over the period->subDays(1)
        $scheduled = [];
        foreach ($period as $date) {
            $orders = $this->getScheduledProdOrders1($date->format('Y-m-d'));
            $shift_sched = $this->get_prod_shift_sched($date->format('Y-m-d'));
            $scheduled[] = [
                'shift'=> $shift_sched,
                'schedule' => $date->format('Y-m-d'),
                'orders' => $orders,
            ];
        }

        return view('production_schedule_painting', compact('unscheduled', 'scheduled', 'mes_user_operations'));

    }
    public function getScheduledProdOrders1($schedule_date){
        $orders = DB::connection('mysql_mes')->table('production_order as pro')
            ->join('job_ticket as jt', 'pro.production_order','jt.production_order')
            ->whereNotIn('pro.status', ['Completed', 'Cancelled'])
            ->where('jt.workstation', 'Painting')
            ->whereDate('jt.planned_start_date', $schedule_date)
            ->distinct('pro.production_order', 'pro.customer', 'pro.delivery_date', 'pro.item_code', 'pro.description', 'pro.qty_to_manufacture', 'pro.stock_uom', 'pro.produced_qty','pro.classification','pro.parts_category', 'pro.sales_order', 'pro.material_request','pro.status')
            ->select('pro.production_order', 'pro.customer', 'pro.delivery_date', 'pro.item_code', 'pro.description', 'pro.qty_to_manufacture', 'pro.stock_uom', 'pro.produced_qty','pro.classification','pro.parts_category', 'pro.sales_order', 'pro.material_request', 'pro.status')
            ->orderBy('jt.sequence', 'asc')
            ->get();



        $scheduled = [];
        foreach($orders as $row){
                        $jt = DB::connection('mysql_mes')->table('job_ticket as jt')
                            ->where('production_order',  $row->production_order)->get();

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
                        
                        $total_workstation = collect($jt)->where('workstation','!=', 'Painting')->count();
                        $total_completed = collect($jt)->where('status', 'Completed')->count();
                        $total_completed_except = collect($jt)->where('status', 'Completed')->where('workstation','!=', 'Painting')->count();
    
                        if ($total_workstation == $total_completed_except) {
                            $stat= 'Ready';
                        }else{
                            
                            $spotlogs=DB::connection('mysql_mes')->table('job_ticket as jt')
                                ->leftJoin('spotwelding_qty as spotpart', 'spotpart.job_ticket_id','jt.job_ticket_id')
                                ->join('process as p','p.process_id','jt.process_id')
                                ->where('jt.production_order', $row->production_order)
                                ->orderBy('spotpart.last_modified_at', 'desc')
                                ->select(DB::raw('(SELECT MAX(last_modified_at) FROM spotwelding_qty WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS last_modified_at'),'p.process_name','jt.production_order','jt.job_ticket_id','jt.workstation');  

                            $timelogs=DB::connection('mysql_mes')->table('job_ticket as jt')
                                ->leftJoin('time_logs as tl', 'jt.job_ticket_id','tl.job_ticket_id')
                                ->join('process as p','p.process_id','jt.process_id')
                                ->where('jt.production_order', $row->production_order)
                                ->select('tl.last_modified_at','p.process_name','jt.production_order','jt.job_ticket_id','jt.workstation')
                                ->union($spotlogs);                         
                           
                            
                            $groupby_log = DB::connection('mysql_mes')->query()->fromSub($timelogs,'logs')
                                ->select('last_modified_at', 'process_name', 'job_ticket_id','workstation')
                                ->orderBy('last_modified_at', 'DESC')->first();
                            $stat=($groupby_log->last_modified_at == null)? 'Not Started': $groupby_log->workstation;
                            // dd($stat);
                        }


            // $current_load=DB::connection('mysql_mes')->table('job_ticket as jt')
            //         ->join('process as p','p.process_id', 'jt.process_id')
            //         ->where('jt.production_order', $row->production_order)
            //         ->where('jt.status', 'In Progress')
            //         ->select('p.process_name', 'jt.workstation')
            //         ->distinct('jt.workstation')
            //         ->get();
        $production_order_status=DB::connection('mysql_mes')->table('production_order as pro')
            ->join('job_ticket as jt','pro.production_order','jt.production_order')
            ->where('jt.workstation','Painting')
            ->where('pro.production_order', $row->production_order)
            ->select('pro.status','jt.sequence')
            ->first();
        $stripfromcomma =strtok($row->description, ",");

        $timelogs_inprogress=DB::connection('mysql_mes')->table('job_ticket as jt')
            ->leftJoin('time_logs as tl', 'jt.job_ticket_id','tl.job_ticket_id')
            ->join('process as p','p.process_id','jt.process_id')
            ->where('jt.production_order', $row->production_order)
            ->where('tl.status', "In Progress")
            ->where('jt.workstation', 'Painting')
            ->select('tl.status as stat')->first();
                
        $drag = empty($timelogs_inprogress->stat)? "move":"not_move";
            $scheduled[] = [
                'id' => $row->production_order,
                'name' => $row->production_order,
                'status' => $status,
                'customer' => $row->customer,
                'delivery_date' => $row->delivery_date,
                'production_item' => $row->item_code,
                'description' => $row->description,
                'strip' => $stripfromcomma,
                'parts_category' => $row->parts_category,
                'qty' => $row->qty_to_manufacture,
                'stock_uom' => $row->stock_uom,
                'produced_qty' => $row->produced_qty,
                'classification' => $row->classification,
                'production_order' => $row->production_order,
                'sales_order' =>($row->sales_order == null) ? $row->material_request:$row->sales_order,
                'prod_status' => $production_order_status->status,
                'process_stat' => $stat,
                'order_no' => $production_order_status->sequence,
                'drag' => $drag 
            ];
        }

        return $scheduled;
    }
        // revised - Jae
    public function reorder_production_painting(Request $request){
            try {
                $val = [];
                if ($request->positions) {
                    foreach ($request->positions as $value) {
                        $name = $value[0];
                        $position = $value[1];
                        $schedule = $value[2];
                        $prod = $value[3];
                        $val_mes = [
                            'sequence' => $position,
                            'planned_start_date' => ($schedule == 'unscheduled') ? null : $schedule,
                        ];
    
    
                        DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $name)->where('workstation','Painting')->update($val_mes);
                    }
                }
            } catch (Exception $e) {
                return response()->json(["error" => $e->getMessage()]);
            }   
    }
    public function selected_printJobTickets($prods){
        $myArray = explode(',', $prods);
        $prod_orders = DB::connection('mysql_mes')->table('production_order')
            ->whereIn('production_order', $myArray)
            ->get();
        // dd($prod_orders);
     $jobtickets = [];
     foreach ($prod_orders as $pro) {
        //  $sales_order = DB::connection('mysql')->table('tabSales Order')->where('name', $pro->sales_order)->first();
         $table = $this->subquery_printTimesheet($pro->production_order, $pro->item_code);
        //  $sales_type = ($sales_order && $sales_order->sales_type) ? $sales_order->sales_type : '';
        if($pro->operation_id == "3"){
            $operation_name="WIRING AND ASSEMBLY";
        }elseif($pro->operation_id == "2"){
            $operation_name="PAINTING";
        }else{
            $operation_name="FABRICATION";
        }
         $jobtickets[] = [
             'production_order' => $pro->production_order,
             'customer' => $pro->customer,
             'material_request' => $pro->material_request,
             'sales_order' => $pro->sales_order,
             'project' => $pro->project,
             'item_code' => $pro->item_code,
             'description' => $pro->description,
             'qty' => $pro->qty_to_manufacture,
             'model' => $pro->parent_item_code,
             'operation' => $operation_name,
             // 'cutting_size' => $pro->actual_cutting_size,
             'sched_date' => $pro->planned_start_date,
             'sales_type' => $pro->classification,
             'workstation' => $table,
         ];
         DB::connection('mysql_mes')->table('production_order')->where('production_order', $pro->production_order)->update(['job_ticket_print' => '1']);

     }

     return view('print_job_ticket', compact('jobtickets'));
    }
    public function operator_item_produced_report(){
        $workstation= DB::connection('mysql_mes')->table('workstation AS w')
                    ->select("workstation_name", "workstation_id")
                    ->where('operation','Fabrication')
                    ->orderBy('workstation_name', 'asc')
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

        return view('reports.operator_report', compact('workstation', 'process', 'parts','sacode'));
    }
    public function tbl_operator_item_produced_report($date_from, $date_to, $workstation, $process, $parts, $item_code){
        $workstation= ($workstation =="All")?'': $workstation;
        $process = ($process=="All")?'': $process;
        $parts = ($parts =="All")? '': $parts;
        $item_code= ($item_code =="All")? '': $item_code;
        
        $get_all_operator=  DB::connection('mysql_mes')->table('job_ticket as jt')
        ->join('time_logs as tl', 'jt.job_ticket_id', 'tl.job_ticket_id')
        ->join('production_order as po', 'po.production_order', 'jt.production_order')
        ->join('process', 'process.process_id', 'jt.process_id')
        ->where('tl.operator_id','!=', null)
        ->where('tl.operator_name','!=', null)
        ->whereDate('tl.from_time', '>=', $date_from)
        ->whereDate('tl.to_time', '<=', $date_to)
        ->where('po.parts_category','like','%'.$parts.'%')
        ->where('jt.workstation','like','%'.$workstation.'%')
        ->where('jt.process_id','like','%'.$process.'%')
        ->where('po.item_code','like','%'.$item_code.'%')
        ->select('tl.operator_name', 'tl.operator_id', 'jt.workstation', 'jt.process_id', 'po.parts_category', 'process.process_name','po.item_code')
        ->distinct('tl.operator_name', 'process.process_id', 'po.parts_category', 'po.item_code')
        ->groupBy('tl.operator_name', 'tl.operator_id', 'jt.workstation', 'jt.process_id', 'po.parts_category', 'process.process_name','po.item_code')
        ->get();
       
        $jobtickets= [];
        foreach($get_all_operator as $rss){
            $timelogs= DB::connection('mysql_mes')->table('job_ticket as jt')
            ->join('time_logs as tl', 'jt.job_ticket_id', 'tl.job_ticket_id')
            ->join('process', 'process.process_id', 'jt.process_id')
            ->join('production_order as po', 'po.production_order', 'jt.production_order')
            ->whereDate('tl.from_time', '>=', $date_from)
            ->whereDate('tl.to_time', '<=', $date_to)
            ->where('jt.workstation', $rss->workstation)
            ->where('jt.process_id', $rss->process_id)
            ->where('po.item_code', $rss->item_code)
            ->where('po.parts_category', $rss->parts_category)
            ->where('tl.duration', '<=', '15')
            ->where('tl.operator_id', $rss->operator_id)
            ->select('tl.operator_name','jt.workstation', 'process.process_name','po.parts_category','tl.good','tl.duration','tl.reject')
            ->get();

                $good_qty= collect($timelogs)->sum('good');
                    $reject= collect($timelogs)->sum('reject');
                    if($good_qty == 0){
                        $var = 1;
                    }else{
                        $var= $good_qty;
                    }

                    $rate =  round(($reject/$var)*100, 2);
                    $duration=collect($timelogs)->sum('duration');
                    $duration_in_sec=$duration * 3600;
                
                    $jobtickets[]=[
                    'operator_name' => $rss->operator_name,
                    'workstation' => $rss->workstation,
                    'process_name'=> $rss->process_name,
                    'parts_category' => $rss->parts_category,
                    'item_code' => $rss->item_code,
                    'operator_id' => $rss->operator_id,
                    'duration' => round($duration_in_sec/$var, 2),
                    'process'=>  $rss->process_id,
                    'quantity' => collect($timelogs)->sum('good'),
                    'cycle_time' => $this->seconds2human($duration_in_sec/$var),
                    'total_rejects' => collect($timelogs)->sum('reject'),
                    'reject_rate' => $rate.'%'
                    ];
        }        
        
        return view('reports.tbl_operator_report', compact('jobtickets'));
    }
    
    public function getchangeover($date_from, $date_to, $operator_id, $workstation, $process_id, $parts){
        $jobticketssss= [];
        $period = CarbonPeriod::create($date_from, $date_to);
        foreach ($period as $date) {
            $dates= $date->format('Y-m-d');
            $get_all_operator=  DB::connection('mysql_mes')->table('time_logs as tl')
            ->join('job_ticket as jt', 'jt.job_ticket_id', 'tl.job_ticket_id')
            ->join('production_order as po', 'po.production_order', 'jt.production_order')
            ->join('process', 'process.process_id', 'jt.process_id')
            ->whereDate('tl.from_time', '=', $dates)
            ->where('jt.workstation', $workstation)
            ->where('jt.process_id', $process_id)
            ->where('po.parts_category', $parts)
            ->where('tl.duration', '<=', '15')
            ->where('tl.operator_id', $operator_id)
            ->distinct('tl.operator_name', 'process.process_id', 'po.parts_category')
            ->orderBy('from_time', 'asc')
            ->select('tl.*')
            ->get();
            $min_from= new Carbon(collect($get_all_operator)->min('from_time'));
            $max_to= new Carbon(collect($get_all_operator)->max('to_time'));
            $duration = collect($get_all_operator)->sum('duration');
            $diffinhrs= $min_from->diffInSeconds($max_to) / 3600;
            $changeover = $diffinhrs - $duration;
            $jobticketssss[] = [
                'from_time' => collect($get_all_operator)->min('from_time'),
                'to_time' => collect($get_all_operator)->max('to_time'),
                'duration' =>$duration,
                'diff_hrs' => $diffinhrs,
                'changeover' => $changeover
                
            ];

            $change_over=collect($jobticketssss)->sum('changeover');
        
          
            
        }



        return $change_over;


    }
    public function getprocess_query($workstation){
        $output="<option value='All'>All</option>";
            if ($workstation != "All") {
                $workstation = DB::connection('mysql_mes')->table('workstation')
                ->where('workstation_name', $workstation)
                ->select('workstation_id')
                ->first();


            $process =DB::connection('mysql_mes')->table('process_assignment')
            ->join('process', 'process.process_id', 'process_assignment.process_id')
            ->where('workstation_id', $workstation->workstation_id)
            ->groupBy('process_assignment.process_id' , 'process.process_name')
            ->select('process_assignment.process_id', 'process.process_name')
            ->orderBy('process.process_name', 'asc')
            ->get();
            // dd($process);

            foreach($process as $row)
                 {
                $output .= '<option value="'.$row->process_id.'">'.$row->process_name.'</option>';
                 }
            }else{
                $process =DB::connection('mysql_mes')->table('process_assignment')
                ->join('process', 'process.process_id', 'process_assignment.process_id')
                ->groupBy('process_assignment.process_id' , 'process.process_name')
                ->select('process_assignment.process_id', 'process.process_name')
                ->orderBy('process.process_name', 'asc')
                ->get();
                // dd($process);
    
                foreach($process as $row)
                     {
                    $output .= '<option value="'.$row->process_id.'">'.$row->process_name.'</option>';
                     }

            }

            // dd($output);
        return $output;
    }
    public function export_view($date_from, $date_to, $workstation, $process, $parts, $item_code){
        $get_all_operator=  DB::connection('mysql_mes')->table('job_ticket as jt')
        ->join('time_logs as tl', 'jt.job_ticket_id', 'tl.job_ticket_id')
        ->join('production_order as po', 'po.production_order', 'jt.production_order')
        ->join('process', 'process.process_id', 'jt.process_id')
        ->where('tl.operator_id','!=', null)
        ->where('tl.operator_name','!=', null)
        ->whereDate('tl.from_time', '>=', $date_from)
        ->whereDate('tl.to_time', '<=', $date_to)
        ->select('tl.operator_name', 'tl.operator_id', 'jt.workstation', 'jt.process_id', 'po.parts_category', 'process.process_name','po.item_code')
        ->distinct('tl.operator_name', 'process.process_id', 'po.parts_category', 'po.item_code')
        ->groupBy('tl.operator_name', 'tl.operator_id', 'jt.workstation', 'jt.process_id', 'po.parts_category', 'process.process_name','po.item_code')
        ->get();
        // dd($get_all_operator);
        // $try= collect($get_all_operator)->where('workstation', 'Shearing');
        
        if($workstation =="All" && $process=="All" && $parts =="All" && $item_code =="All"){
            $query= collect($get_all_operator);
        }elseif($workstation !="All" && $process=="All" && $parts =="All" && $item_code =="All"){
            $query= collect($get_all_operator)->where('workstation', $workstation);
        }elseif($workstation =="All" && $process !="All" && $parts =="All" && $item_code =="All"){
            $query= collect($get_all_operator)->where('process_id', $process);
        }elseif($workstation =="All" && $process=="All" && $parts !="All" && $item_code =="All"){
            $query= collect($get_all_operator)->where('parts_category', $parts);
        }elseif($workstation =="All" && $process=="All" && $parts =="All" && $item_code !="All"){
            $query= collect($get_all_operator)->where('item_code', $item_code);
        }elseif($workstation !="All" && $process =="All" && $parts !="All" && $item_code =="All"){
           //x,check,x,check
            $query= collect($get_all_operator)->where('workstation', $workstation)->where('parts_category', $parts);
        }elseif($workstation =="All" && $process !="All" && $parts =="All" && $item_code !="All"){
            //check,x,check,x
             $query= collect($get_all_operator)->where('process_id', $process)->where('item_code', $item_code);
         
        }elseif($workstation =="All" && $process!="All" && $parts !="All" && $item_code !="All"){
            //new
            $query= collect($get_all_operator)->where('process_id', $process)->where('parts_category', $parts)->where('item_code', $item_code);
        }elseif($workstation !="All" && $process=="All" && $parts !="All" && $item_code !="All"){
            $query= collect($get_all_operator)->where('workstation', $workstation)->where('parts_category', $parts)->where('item_code', $item_code);
        }elseif($workstation !="All" && $process!="All" && $parts =="All" && $item_code !="All"){
            $query= collect($get_all_operator)->where('workstation', $workstation)->where('process_id', $process)->where('item_code', $item_code);
        }elseif($workstation !="All" && $process!="All" && $parts !="All" && $item_code =="All"){
            $query= collect($get_all_operator)->where('workstation', $workstation)->where('process_id', $process)->where('parts_category', $parts);
        //new
        }elseif($workstation =="All" && $process=="All" && $parts !="All" && $item_code !="All"){
            $query= collect($get_all_operator)->where('parts_category', $parts)->where('item_code', $item_code);
        }elseif($workstation !="All" && $process!="All" && $parts =="All" && $item_code =="All"){
            $query= collect($get_all_operator)->where('workstation', $workstation)->where('process_id', $process);
        }elseif($workstation =="All" && $process!="All" && $parts !="All" && $item_code =="All"){
            $query= collect($get_all_operator)->where('process_id', $process)->where('parts_category', $parts);
        }elseif($workstation !="All" && $process=="All" && $parts =="All" && $item_code !="All"){
            $query= collect($get_all_operator)->where('workstation', $workstation)->where('item_code', $item_code);
        }else{
            $query= collect($get_all_operator)->where('workstation', $workstation)->where('process_id', $process)->where('parts_category', $parts)->where('item_code', $item_code);
        }



        $jobtickets= [];

        foreach($query as $rss){
            $timelogs= DB::connection('mysql_mes')->table('job_ticket as jt')
            ->join('time_logs as tl', 'jt.job_ticket_id', 'tl.job_ticket_id')
            ->join('process', 'process.process_id', 'jt.process_id')
            ->join('production_order as po', 'po.production_order', 'jt.production_order')
            ->whereDate('tl.from_time', '>=', $date_from)
            ->whereDate('tl.to_time', '<=', $date_to)
            ->where('jt.workstation', $rss->workstation)
            ->where('jt.process_id', $rss->process_id)
            ->where('po.parts_category', $rss->parts_category)
            ->where('po.item_code', $rss->item_code)
            ->where('tl.duration', '<=', '15')
            ->where('tl.operator_id', $rss->operator_id)
            ->select('tl.operator_name','jt.workstation', 'process.process_name','po.parts_category','tl.good','tl.duration','tl.reject')
            ->get();

                $good_qty= collect($timelogs)->sum('good');
                    $reject= collect($timelogs)->sum('reject');
                    if($good_qty == 0){
                        $var = 1;
                    }else{
                        $var= $good_qty;
                    }

                    $rate =  round(($reject/$var)*100, 2);
                    $duration=collect($timelogs)->sum('duration');
                    $duration_in_sec=$duration * 3600;
                    $changeover= $this->getchangeover($date_from, $date_to,$rss->operator_id,$rss->workstation, $rss->process_id, $rss->parts_category);
                
                    $jobtickets[]=[
                    'operator_name' => $rss->operator_name,
                    'workstation' => $rss->workstation,
                    'process_name'=> $rss->process_name,
                    'parts_category' => $rss->parts_category,
                    'item_code' => $rss->item_code,
                    'operator_id' => $rss->operator_id,
                    'duration' => $duration_in_sec/$var,
                    'process'=>  $rss->process_id,
                    'quantity' => collect($timelogs)->sum('good'),
                    'cycle_time' => $this->seconds2human($duration_in_sec/$var),
                    'change_over' =>  $this->seconds2human($changeover),
                    'total_rejects' => collect($timelogs)->sum('reject'),
                    'reject_rate' => $rate.'%'
                    ];
        }
        return Excel::download(new ExportDataexcel($jobtickets), "OperatorList.xlsx");
    }
    public function hidereject_notif_dash(Request $request){
        if($request->frm_table ==  'spotwelding'){
            DB::connection('mysql_mes')->table('spotwelding_qty')->where('time_log_id', $request->timelog_id)->update(['is_hide' => '1']);
        }else{
            DB::connection('mysql_mes')->table('time_logs')->where('time_log_id', $request->timelog_id)->update(['is_hide' => '1']);

        }
        return response()->json(['success' => 1, 'message' => 'Updated']);


    }

    // /get_tbl_setting_machine_list
    public function get_tbl_setting_machine_list(Request $request){
        $machine_list = DB::connection('mysql_mes')->table('machine')
            ->where(function($q) use ($request) {
                $q->where('reference_key', 'LIKE', '%'.$request->search_string.'%')
                ->orWhere('machine_code', 'LIKE', '%'.$request->search_string.'%')
                ->orWhere('machine_name', 'LIKE', '%'.$request->search_string.'%')
                ->orWhere('status', 'LIKE', '%'.$request->search_string.'%')
                ->orWhere('type', 'LIKE', '%'.$request->search_string.'%')
                ->orWhere('model', 'LIKE', '%'.$request->search_string.'%');
            })
            ->orderBy('machine_id', 'desc')->paginate(15);

        return view('tables.tbl_settings_machine_list', compact('machine_list'));
    }
    public function stock_adjustment_entries_page(){
        $attributes = $this->get_item_attributes();

        return view('stock_adjustment_entries_page', compact('attributes'));
    }
    public function get_balanceqty_stock_adjustment_entries($item_code){
        if($item_code == "default"){
            $data=[];
            $data=[
                'balance' => "",
                'planned' => "",
                'actual' => "",
                'description' => "",
                'entry_type' => ""
            ];

        }else{
        $production_order=DB::connection('mysql_mes')->table('production_order')->where('item_code', $item_code)->get();
        $planned_qty= collect($production_order)->where('status', 'Not Started')->sum('qty_to_manufacture');
        $actual_qty= collect($production_order)->where('status', 'In Progress')->sum('qty_to_manufacture');
        $production_orders= DB::connection('mysql_mes')->table('production_order as po')
            ->select('po.description')
            ->where('po.item_code', $item_code)
            ->groupBy('po.description')
            ->first();
        $data=[];
        if (DB::connection('mysql_mes')
        ->table('fabrication_inventory')
        ->where('item_code', $item_code)
        ->exists()){
            $get_item_code_details= DB::connection('mysql_mes')->table('fabrication_inventory')->where('item_code', $item_code)->first();
            $balanced_qty = $get_item_code_details->balance_qty;
            $data=[
                'balance' => $balanced_qty,
                'planned' => $planned_qty,
                'actual' => $actual_qty,
                'description' => $production_orders->description,
                'entry_type' => "Stock Adjustment"

            ];
            
        }else{
            $balanced_qty = "";
            $data=[
                'balance' => $balanced_qty,
                'planned' => $planned_qty,
                'actual' => $actual_qty,
                'description' => $production_orders->description,
                'entry_type' => "New Entry"
            ];
        }

        }
        
        
        
        return response()->json(['qty' => $data]);            

    }
    public function get_item_code_stock_adjustment_entries(){
        $production_orders= DB::connection('mysql_mes')->table('production_order as po')
        ->select('po.item_code', 'po.description')
        ->where('po.operation_id', '1')
        ->groupBy('po.item_code','po.description')
        ->orderBy('po.item_code')
        ->get();

        $output="<option value='default'>Select Item Code</option>";

        foreach($production_orders as $row)
             {
            $output .= '<option value="'.$row->item_code.'">'.$row->item_code.' - '.$row->description.'</option>';
             }

        // dd($output);
    return $output;
    }

    public function getNextOrderNumberfortransaction()
        {
            // Get the last created order
            $lastOrder = DB::connection('mysql_mes')->table('inventory_transaction')->orderBy('transaction_no', 'desc')->select('transaction_no')->first();

            if (empty($lastOrder->transaction_no)){
                // We get here if there is no order at all
                // If there is no number set it to 0, which will be 1 at the end.

                $number = 0;
            }else{
                $number1 = $lastOrder->transaction_no;
                $number = preg_replace("/[^0-9]{1,4}/", '', $number1); // return 1234
            }

            // If we have ORD000001 in the database then we only want the number
            // So the substr returns this 000001

            // Add the string in front and higher up the number.
            // the %05d part makes sure that there are always 6 numbers in the string.
            // so it adds the missing zero's when needed.
         
            return sprintf('%05d', intval($number) + 1);
        }
    public function submit_stock_entries_adjustment(Request $request){
        if (DB::connection('mysql_mes')
        ->table('fabrication_inventory')
        ->where('item_code', $request->item_code)
        ->exists()){
            $now = Carbon::now();
            $update = [

                'balance_qty' => $request->balance_qty,
                'last_modified_at' => $now->toDateTimeString(),
                'last_modified_by' => Auth::user()->employee_name
                ];
                DB::connection('mysql_mes')->table('fabrication_inventory')->where('item_code', $request->item_code)->update($update);
                $users_id = Auth::user()->user_id;
                $get_operation_id= DB::connection('mysql_mes')->table('user')->where('user_access_id', $users_id)->first();

                $trans = [
                    'operation_id' => $get_operation_id->operation_id,
                    'item_code' => $request->item_code,
                    'adjusted_qty' => $request->balance_qty,
                    'previous_qty' => $request->orig_balance_qty,
                    'remarks' => "",
                    'entry_type' => $request->entry_type_box,
                    'created_by' => Auth::user()->employee_name,
                    'created_at' => $now->toDateTimeString(),
                    'last_modified_by' => Auth::user()->employee_name,
                    'last_modified_at' => $now->toDateTimeString()


                ];

            DB::connection('mysql_mes')->table('inventory_transaction')->insert($trans);
            return response()->json(['success' => 1, 'message' => 'Stock Adjustment Successfully Updated!']);


        }else{
            $now = Carbon::now();
                $values1 = [
                    'description' => $request->item_description_input,
                    'item_code' => $request->item_code,
                    'balance_qty' => $request->balance_qty,
                    'created_by' => Auth::user()->employee_name,
                    'created_at' => $now->toDateTimeString(),
                    'last_modified_by' => Auth::user()->employee_name

                ];

            DB::connection('mysql_mes')->table('fabrication_inventory')->insert($values1);
            $users_id = Auth::user()->user_id;
            $get_operation_id= DB::connection('mysql_mes')->table('user')->where('user_access_id', $users_id)->first();

            $trans = [
                'operation_id' => $get_operation_id->operation_id,
                'item_code' => $request->item_code,
                'adjusted_qty' => $request->balance_qty,
                'previous_qty' => ($request->orig_balance_qty == null) ? 0 : $request->orig_balance_qty,
                'entry_type' => $request->entry_type_box,
                'remarks' => "",
                'created_by' => Auth::user()->employee_name,
                'created_at' => $now->toDateTimeString(),
                'last_modified_by' => Auth::user()->employee_name,
                'last_modified_at' => $now->toDateTimeString()


            ];

            DB::connection('mysql_mes')->table('inventory_transaction')->insert($trans);
            return response()->json(['success' => 1, 'message' => 'Stock Entry Successfully Inserted!']);

        }
                
        
    }

    public function get_tbl_stock_adjustment_entry(Request $request){
        $inv_qry = DB::connection('mysql_mes')->table('fabrication_inventory')
            ->when($request->q, function ($query) use ($request) {
                return $query->where(function($q) use ($request) {
                    $q->where('item_code', 'LIKE', '%'.$request->q.'%')
                        ->orWhere('description', 'LIKE', '%'.$request->q.'%');
                });
            })
            ->when($request->filters, function ($query) use ($request) {
                foreach ($request->filters as $f) {
                    $query->where('description', 'LIKE', "%".$f."%");
                }

                return $query;
            })
            ->get();

        $inventory_list = [];
        foreach ($inv_qry as $row) {
            $planned_qty = DB::connection('mysql_mes')->table('production_order')->where('item_code', $row->item_code)
                ->where('status', 'Not Started')->sum('qty_to_manufacture');

            $in_process_qty = DB::connection('mysql_mes')->table('production_order')->where('item_code', $row->item_code)
                ->where('status', 'In Progress')->sum('qty_to_manufacture');

            $inventory_list[] = [
                'item_code' => $row->item_code,
                'description' => $row->description,
                'planned_qty' => $planned_qty,
                'in_process_qty' => $in_process_qty,
                'balance_qty' => $row->balance_qty
            ];
        }

        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // Create a new Laravel collection from the array data
        $itemCollection = collect($inventory_list);
        // Define how many items we want to be visible in each page
        $perPage = 20;
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
        // set url path for generted links
        $paginatedItems->setPath($request->url());
        $data = $paginatedItems;

        return view('tables.tbl_stockadjustment_entry', compact('data'));
    }

    public function get_fabrication_inventory_history_list(Request $request){
        $data = DB::connection('mysql_mes')->table('inventory_transaction as it')
            ->join('operation as op','op.operation_id', 'it.operation_id' )
            ->select('it.*','op.operation_name')
            ->where(function($q) use ($request) {
                    $q->whereDate('it.created_at', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('it.item_code', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('it.last_modified_by', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('op.operation_name', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('it.adjusted_qty', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('it.previous_qty', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('it.entry_type', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('it.transaction_no', 'LIKE', '%'.$request->search_string.'%');
            })
            ->orderBy('it.transaction_no', 'desc')->paginate(10);

        return view('tables.tbl_fabrication_inventory_history_list', compact('data'));
    }

    public function get_item_attributes(){
        $mes_inv_items = DB::connection('mysql_mes')->table('fabrication_inventory')->pluck('item_code');

        $attributes_arr = [];
        $attr_values = DB::connection('mysql')->table('tabItem Variant Attribute')
            ->whereIn('parent', $mes_inv_items)
            ->where('attribute', 'CUTTING SIZE')
            ->distinct()->pluck('attribute_value');

        $length_arr = [];
        $width_arr = [];
        $thickness_arr = [];
        foreach ($attr_values as $val) {
            $cutting_size = strtoupper($val);
            $cutting_size = str_replace(' ', '', preg_replace('/\s+/', '', $cutting_size));
            $cutting_size = explode("X", $cutting_size);

            array_push($length_arr,$cutting_size[0]);
            array_push($width_arr,$cutting_size[1]);
            array_push($thickness_arr,$cutting_size[2]);
        }

        $attributes_arr[] = [
            'attribute' => 'Length',
            'values' => array_unique($length_arr)
        ];

        $attributes_arr[] = [
            'attribute' => 'Width',
            'values' => array_unique($width_arr)
        ];

        $attributes_arr[] = [
            'attribute' => 'Thickness',
            'values' => array_unique($thickness_arr)
        ];

        $attributes = DB::connection('mysql')->table('tabItem Variant Attribute')
            ->whereIn('parent', $mes_inv_items)->distinct()->pluck('attribute');
        
        foreach ($attributes as $attr){
            $attr_values = DB::connection('mysql')->table('tabItem Variant Attribute')
                ->whereIn('parent', $mes_inv_items)->where('attribute', $attr)
                ->distinct()->pluck('attribute_value');

            if ($attr != 'CUTTING SIZE') {
                $attributes_arr[] = [
                    'attribute' => $attr,
                    'values' => $attr_values
                ];
            }
        }        

        return $attributes_arr;
    }

    public function update_planned_start_date(Request $request){
        $scheduledTime = $request->input('scheduledtime');
        $prodid = $request->input('prodid');
        $val = [];
            $val = [
                'planned_start_date' => $scheduledTime,
                'last_modified_by' => Auth::user()->email
            ];

        $val1 = [];
            $val1 = [
                'planned_start_date' => $scheduledTime,
                'last_modified_by' => Auth::user()->email
            ];
                 
            DB::connection('mysql_mes')->table('production_order')->where('production_order', $prodid)->update($val);
            DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $prodid)->update($val);  
        return response()->json(['success' => 1, 'message' => 'Successfully Scheduled!']);

    }
    public function update_planned_start_date_by_click(Request $request){   
            $val = [];
                $val = [
                    'planned_start_date' => $request->start_time,
                    'last_modified_by' => Auth::user()->email
                ];

            DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->prod_id)->update($val);
            DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->prod_id)->update($val);  
            $get_data=$this->get_production_schedule_calendar($request->operation_id);
            return $get_data;
    }


    public function spotwelding_exploded_production_order_search($jt){
        $time_logs = DB::connection('mysql_mes')->table('spotwelding_qty')->where('job_ticket_id', $jt)->first();
        $prod = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $jt)->first();

        if (!$time_logs) {
            $task_list_qry = DB::connection('mysql_mes')->table('production_order AS po')
                ->join('job_ticket AS jt', 'po.production_order', 'jt.production_order')
                ->where('po.production_order', $prod->production_order)
                ->where('jt.workstation', $prod->workstation)
                ->where('jt.job_ticket_id', $jt)
                ->select('po.item_code', 'jt.job_ticket_id', DB::raw('(SELECT process_name FROM process WHERE process_id = jt.process_id) AS process_name'), 'po.production_order', 'po.description', 'po.sales_order', 'po.material_request', 'po.customer', 'po.qty_to_manufacture', 'po.stock_uom', 'po.project', 'jt.process_id')
                ->orderBy('jt.last_modified_at', 'desc')->first();
        }else{
            $task_list_qry = DB::connection('mysql_mes')->table('production_order AS po')
                ->join('job_ticket AS jt', 'po.production_order', 'jt.production_order')
                ->where('po.production_order', $prod->production_order)
                ->where('jt.workstation', $prod->workstation)
                ->where('jt.job_ticket_id', $jt)
                ->select('po.item_code','jt.job_ticket_id',  DB::raw('(SELECT process_name FROM process WHERE process_id = jt.process_id) AS process_name'), 'po.production_order', 'po.description', 'po.sales_order', 'po.material_request',  'po.customer', 'po.qty_to_manufacture', DB::raw('(SELECT SUM(good) FROM spotwelding_qty WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS total_good'),  DB::raw('(SELECT SUM(reject) FROM spotwelding_qty WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS total_reject'), 'po.stock_uom', 'po.project', 'jt.process_id', 'jt.completed_qty', 'jt.status')->first();
        }

        $task_list = [];
        $task_list[] = [
            'item_code' => $task_list_qry->item_code,
            'job_ticket_id' => $task_list_qry->job_ticket_id,
            'production_order' => $task_list_qry->production_order,
            'description' => strtok($task_list_qry->description, ","),
            'sales_order' => empty($task_list_qry->sales_order)? $task_list_qry->material_request:$task_list_qry->sales_order,                
            'material_request' => $task_list_qry->material_request,
            'status' => ($time_logs) ? $task_list_qry->status : 'Pending',
            'customer' => $task_list_qry->customer,
            'qty_to_manufacture' => $task_list_qry->qty_to_manufacture,
            'completed_qty' => ($time_logs) ? $task_list_qry->completed_qty : 0,
            'total_good' => ($time_logs) ? $task_list_qry->total_good : 0,
            'total_reject' => ($time_logs) ? $task_list_qry->total_reject : 0,
            'stock_uom' => $task_list_qry->stock_uom,
            'project' => $task_list_qry->project,
        ];


        $timelogs = DB::connection('mysql_mes')->table('spotwelding_qty')
            ->join('job_ticket', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
            ->where('job_ticket.job_ticket_id', $jt)
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
                'reject' => $log->reject ? $log->reject : 0,
                'duration' => $dur_days .' '. $dur_hours . ' '. $dur_minutes .' '. $dur_seconds,
                'status' => $log->status,
                'machine' => $log->machine_code,
                'operator_id' => $log->operator_id,
                'operator_name' => $log->operator_name
            ];
        }
        $total_rejects = $prod->reject ? $prod->reject : 0;
        return view('tables.tbl_spotwelding_production_order_search', compact('logs','task_list', 'total_rejects'));
    }

    public function production_schedule_monitoring($date){
        $date_format= date('F d, Y', strtotime($date));
        $shift_sched = $this->get_prod_shift_sched($date);
        $shift_sched = $this->get_prod_shift_sched($date);
        $machine_name= DB::connection('mysql_mes')->table('machine')->where('machine_code','M00200')->first();
        
        

        return view('painting.production_schedule_monitoring', compact('date_format', 'shift_sched','machine_name','date'));
    }
    public function edit_cpt_status_qty(Request $request){
        try {
            $now = Carbon::now();
            $jt_details_loading = DB::connection('mysql_mes')->table('job_ticket')
                ->join('process', 'job_ticket.process_id', 'process.process_id')
                ->where('production_order', $request->prod_no)
                ->where('process.process_name', 'Loading')->first();
            $jt_details_unloading = DB::connection('mysql_mes')->table('job_ticket')
                ->join('process', 'job_ticket.process_id', 'process.process_id')
                ->where('production_order', $request->prod_no)
                ->where('process.process_name', 'Unloading')->first();
        //Update data from timelogs for unloading and loading 
            if(DB::connection('mysql_mes')->table('time_logs')
                ->where('job_ticket_id', '=', $jt_details_loading->job_ticket_id)
                ->exists()){
                $values_tl_loading = [
                    'status' => $request->loading_status,
                    'good' => $request->loading_cpt,
                    'last_modified_by' => Auth::user()->employee_name,
                    'last_modified_at' => $now->toDateTimeString()
                ];
                DB::connection('mysql_mes')->table('time_logs')->where('job_ticket_id', $jt_details_loading->job_ticket_id)->update($values_tl_loading);
            }
            if(DB::connection('mysql_mes')->table('time_logs')
                ->where('job_ticket_id', '=', $jt_details_unloading->job_ticket_id)
                ->exists()){
                $values_tl_unloading = [
                    'status' => $request->unloading_status,
                    'good' => $request->unloading_cpt,
                    'last_modified_by' => Auth::user()->employee_name,
                    'last_modified_at' => $now->toDateTimeString()
                ];
                DB::connection('mysql_mes')->table('time_logs')->where('job_ticket_id', $jt_details_unloading->job_ticket_id)->update($values_tl_unloading);
            }   
        //Update data from job_tickets for unloading and loading 
            $values_jt_loading = [
                'status' => $request->loading_status,
                'completed_qty' => $request->loading_cpt,
                'last_modified_by' => Auth::user()->employee_name,
                'last_modified_at' => $now->toDateTimeString()
            ];
            DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $jt_details_loading->job_ticket_id)->update($values_jt_loading);
            $values_jt_unloading = [
                'status' => $request->unloading_status,
                'completed_qty' => $request->unloading_cpt,
                'last_modified_by' => Auth::user()->employee_name,
                'last_modified_at' => $now->toDateTimeString()
            ];
            DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $jt_details_unloading->job_ticket_id)->update($values_jt_unloading);        
            $values_prod_table = [
                'status' => $request->status_overall,
                'produced_qty' => $request->cpt_overall,
                'last_modified_by' => Auth::user()->employee_name,
                'last_modified_at' => $now->toDateTimeString()
            ];
            DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->prod_no)->update($values_prod_table);
            return response()->json(['success' => 1, 'message' => ''.$request->prod_no.'- Successfully Updated.']);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function get_production_schedule_monitoring_list(Request $request,$schedule_date){
        $orders = DB::connection('mysql_mes')->table('production_order as prod')
            ->join('job_ticket as tsd','tsd.production_order','=','prod.production_order')
            ->whereNotIn('prod.status', ['Cancelled', 'Closed'])
            ->where('tsd.planned_start_date', $schedule_date)
            ->where('tsd.workstation', 'Painting')
            ->when($request->customer && $request->customer != 'Select All', function ($q) use ($request){
                return $q->where('prod.customer', 'like', '%'.$request->customer.'%');
            })
            ->when($request->reference && $request->reference != 'Select All', function ($q) use ($request){
                return $q->where(function ($x) use ($request){
                    $x->where('prod.sales_order', 'like', '%'.$request->reference.'%')->orWhere('prod.material_request', 'like', '%'.$request->reference.'%');
                });
            })
            ->when($request->parent && $request->parent != 'Select All', function ($q) use ($request){
                return $q->where('prod.parent_item_code', 'like', '%'.$request->parent.'%');
            })
            ->distinct('prod.production_order', 'tsd.sequence')
            ->select('prod.*','tsd.sequence', 'tsd.planned_start_date as planned_start')
            ->orderBy('tsd.sequence','asc')
            ->get();

        $data = [];
        foreach($orders as $row){
            $reference_no = ($row->sales_order) ? $row->sales_order : $row->material_request;
            
            $delivery_details = DB::connection('mysql_mes')->table('delivery_date')
                ->where('reference_no', $reference_no)->where('parent_item_code', $row->parent_item_code)
                ->first();

            if ($delivery_details) {
                $delivery_date = ($delivery_details->rescheduled_delivery_date) ? $delivery_details->rescheduled_delivery_date : $delivery_details->delivery_date;
            }else{
                $delivery_date = $row->delivery_date;
            }

            $is_backlog = (Carbon::parse($row->planned_start)->format('Y-m-d') < Carbon::now()->format('Y-m-d')) ? 1 : 0;

            $data[]=[
                'delivery_date' => $delivery_date,
                'actual_start_date' => (!in_array($row->status, ['Not Started', 'Pending'])) ? Carbon::parse($row->actual_start_date)->format('Y-m-d h:i:A') : null,
                'customer' => $row->customer,
                'reference_no' => ($row->sales_order) ? $row->sales_order : $row->material_request,
                'item_code' => $row->item_code,
                'parent_item_code' => $row->parent_item_code,
                'planned_start_date' => $row->planned_start,
                'is_backlog' => $is_backlog,
                'item_description'=> strtok($row->description, ","),
                'stock_uom' => $row->stock_uom,
                'balance_qty' => ($row->qty_to_manufacture - $row->produced_qty),
                'completed_qty'=> $row->produced_qty,
                'feedback_qty'=> $row->feedback_qty,
                'qty'=> $row->qty_to_manufacture, 
                'production_order' => $row->production_order,
                'remarks' => $row->notes,
                'sequence' => $row->sequence,
                'duration' =>$this->duration_for_completed_painting($row->production_order),
                'feedback_qty' => ($row->feedback_qty == null)? 0 : $row->feedback_qty,
                'job_ticket'=> $this->get_jt_details($row->production_order),
                'prod_status'=> $row->status,
                'reject' => $this->get_reject_production_sched_monitoring($row->production_order)
            ];
        }

        $current_date = $schedule_date;
        $filters = [
            'customers' => array_unique(array_column($data, 'customer')),
            'reference_nos' => array_unique(array_column($data, 'reference_no')),
            'parent_item_codes' => array_unique(array_column($data, 'parent_item_code'))
        ];

        return view('painting.tbl_production_schedule_monitoring', compact('data', 'current_date', 'filters'));
    }
    public function duration_for_completed_painting($prod){
        $orders = DB::connection('mysql_mes')->table('job_ticket as tsd')
        ->leftJoin('time_logs', 'time_logs.job_ticket_id','tsd.job_ticket_id')
        ->join('process as p', 'p.process_id', 'tsd.process_id')
        ->where('tsd.production_order',$prod)
        ->where('tsd.workstation','Painting')
        ->select(DB::raw('MAX(time_logs.to_time) as to_time'), DB::raw('MIN(time_logs.from_time) as from_time'))
        ->first();


        $start = Carbon::parse($orders->from_time);
        $end = Carbon::parse($orders->to_time);
        $totalDuration = $end->diffInSeconds($start);
        $op_hrs= $this->format_operating_hrs($totalDuration);

        return $op_hrs;
    }
    public function get_jt_details($prodno){
        $orders = DB::connection('mysql_mes')->table('job_ticket as tsd')
        ->join('process as p', 'p.process_id', 'tsd.process_id')
        ->where('tsd.production_order', $prodno)
        ->where('tsd.workstation','Painting')
        ->join('workstation as work','work.workstation_name','tsd.workstation')
        ->select('p.process_name','tsd.status','tsd.completed_qty')
        ->get();
        return $orders;
    }
    public function get_reject_production_sched_monitoring($prodno){
        $orders = DB::connection('mysql_mes')->table('job_ticket as tsd')
        ->leftJoin('time_logs', 'time_logs.job_ticket_id', 'tsd.job_ticket_id')
        ->join('process as p', 'p.process_id', 'tsd.process_id')
        ->where('tsd.production_order', $prodno)
        ->where('tsd.workstation','Painting')
        ->join('workstation as work','work.workstation_name','tsd.workstation')
        ->select('p.process_name','tsd.status', 'time_logs.reject')
        ->get();

        $reject= collect($orders)->sum('reject');
        // dd($reject);

        return $reject;
    }
    public function get_production_schedule_monitoring_list_backlogs($schedule_date){
        $orders = DB::connection('mysql_mes')->table('production_order as prod')
            ->join('job_ticket as tsd','tsd.production_order','=','prod.production_order')
            ->whereNotIn('prod.status', ['Cancelled', 'Completed'])
            ->where('tsd.status', '!=', 'Completed')
            ->join('workstation as work','work.workstation_name','tsd.workstation')
            ->where('tsd.planned_start_date','<', $schedule_date)
            ->where('tsd.workstation', 'Painting')
            ->distinct('prod.production_order', 'tsd.sequence')
            ->select('prod.*','tsd.sequence','tsd.planned_start_date')
            ->orderBy('tsd.sequence','asc')
            ->get();
        
            $data = [];
            foreach($orders as $row){
               
    
                $data[]=[
                    'customer' => $row->customer,
                    'item_code' => $row->item_code,
                    'item_description'=> strtok($row->description, ","),
                    'stock_uom' => $row->stock_uom,
                    'balance_qty' => ($row->qty_to_manufacture - $row->produced_qty),
                    'completed_qty'=> $row->produced_qty,
                    'qty'=> $row->qty_to_manufacture, 
                    'production_order' => $row->production_order,
                    'remarks' => $row->notes,
                    'sequence' => $row->sequence,
                    'planned_start_date' => Carbon::parse($row->planned_start_date)->format('F d, Y'),
                    'job_ticket'=> $this->get_jt_details($row->production_order),
                    'reject' => $this->get_reject_production_sched_monitoring($row->production_order)
                ];
            }
            $current_date= $schedule_date;
        return view('painting.tbl_production_schedule_backlog_list', compact('data','current_date'));
 
    }
    public function count_current_painting_production_schedule_monitoring($schedule_date){
        $orders_rejects = DB::connection('mysql_mes')->table('production_order as prod')
            ->join('job_ticket as tsd','tsd.production_order','=','prod.production_order')
            ->join('time_logs as tl', 'tl.job_ticket_id', 'tsd.job_ticket_id')
            ->join('process as p', 'p.process_id', 'tsd.process_id')
            ->where('tsd.workstation', 'Painting')
            ->whereDate('tl.last_modified_at',$schedule_date)
            // ->where('prod.status', 'Not Started')
            ->where('prod.is_scheduled' , 1)
            ->select("tsd.status","tl.reject")->get();

            $orders_inprogress = DB::connection('mysql_mes')->table('production_order as prod')
            ->join('job_ticket as tsd','tsd.production_order','=','prod.production_order')
            ->whereDate('tsd.planned_start_date', $schedule_date)
            ->where('tsd.workstation', 'Painting')
            ->where('tsd.status', 'In Progress')
            ->groupBy('prod.production_order','prod.qty_to_manufacture')
            ->distinct('prod.production_order', 'prod.qty_to_manufacture')
            ->select('prod.production_order', 'prod.qty_to_manufacture')
            ->get();

            
        
            $orders_completed = DB::connection('mysql_mes')->table('production_order as prod')
            ->join('job_ticket as tsd','tsd.production_order','prod.production_order')
            ->where('tsd.workstation','Painting')
            ->whereNotIn('prod.status', ['Cancelled'])
            ->whereDate('tsd.last_modified_at', $schedule_date)
            ->groupBy('prod.production_order','prod.status','prod.qty_to_manufacture')
            ->select('prod.production_order','prod.status','prod.qty_to_manufacture')
            ->get();

            $orders_pending_po = DB::connection('mysql_mes')->table('production_order as prod')
            ->join('job_ticket as tsd','tsd.production_order','prod.production_order')
            ->where('tsd.workstation','Painting')
            ->whereNotIn('prod.status', ['Cancelled'])
            ->whereDate('tsd.planned_start_date', $schedule_date)
            ->groupBy('prod.production_order','prod.status','prod.qty_to_manufacture')
            ->select('prod.production_order','prod.status','prod.qty_to_manufacture')
            ->get();

        $scheduled = [];
 
        $count_pending = collect($orders_pending_po)->count();
        $count_pending_qty = collect($orders_pending_po)->sum('qty_to_manufacture');
        $count_inprogress_qty = collect($orders_inprogress)->sum('qty_to_manufacture');
        $count_inprogress = collect($orders_inprogress)->count();
        $count_completed = collect($orders_completed)->where('status', 'Completed')->count();
        $count_reject = collect($orders_rejects)->where('reject','!=', '0')->sum('reject');
        $scheduled = [
                'pending' => $count_pending ,
                'inProgress' => $count_inprogress,
                'completed' => $count_completed,
                'reject' =>  $count_reject,
                'qty_pending' => $count_pending_qty,
                'qty_inprogress' =>$count_inprogress_qty
            ];
        return $scheduled;

    }
        
    public function move_today_task(Request $request)
    {

        $val = [];
            $val = [
                'planned_start_date' => $request->prod_date_today,
                'last_modified_by' => Auth::user()->email
            ];
                 
            DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->prod_moved_today)->where('workstation','Painting')->update($val);  
    return response()->json(['success' => 1, 'message' => 'Scheduled Date Successfully Updated!']);

    }
    public function add_notes_task(Request $request)
    {
        $val = [];
            $val = [
                'notes' => $request->remarks_field,
                'last_modified_by' => Auth::user()->email,
            ];
            DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->prod_no)->update($val);  
    return response()->json(['success' => 1, 'message' => 'Remarks Successfully Added!']);

    }

    function format_operating_hrs($ss) {
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
               $format= "$m $mm";
            }elseif($d == 0) {
               $format= "$h $hh, $m $mm";
            }else{
                $format="$d $dd,$h $hh, $m $mm";
            }
            return $format;
            
    }
    public function get_water_discharged_modal_details(Request $request){
        $transaction_date = Carbon::now();
        if($request->transaction_date){
            $transaction_date = Carbon::parse($request->transaction_date);
        }

        $formatted_transaction_date = $transaction_date->format('Y-m-d');
        $operating_hrs = DB::connection('mysql_mes')->table('painting_operation_logs')
            ->whereDate('operation_date', $formatted_transaction_date)->get();

        $previous = DB::connection('mysql_mes')->table('water_discharged_monitoring')
            ->orderBy('created_by', 'desc')->first();
        
        $min = collect($operating_hrs)->min('operation_date');
        $max = collect($operating_hrs)->max('operation_date');

        $start = Carbon::parse($min);
        $end = Carbon::parse($max);

        $totalDuration = $end->diffInSeconds($start);
        $op_hrs= $this->format_operating_hrs($totalDuration);
        $previous_wd = ($previous == null)? 0: $previous->previous;

        $formatted_transaction_date = $transaction_date->format('M d, Y');
    
        return view('painting_operator.tbl_water_discharge_tab', compact('op_hrs', 'previous_wd', 'formatted_transaction_date'));               
    }
    
    public function submit_water_discharge_monitoring(Request $request){
        if(!$request->water_date){
            return response()->json(['success' => 0, 'message' => 'Please enter date.']);
        }

        if(!$request->inspected_by){
            return response()->json(['success' => 0, 'message' => 'Please enter employee ID.']);
        }

        $email= DB::connection('mysql_essex')->table('users')
            ->where('user_id', $request->inspected_by)->select('users.email')->first();

        if (!$email) {
            return response()->json(['success' => 0, 'message' => 'Employee ID not found.']);
        }

        $now = Carbon::now();
        $existing_record = DB::connection('mysql_mes')->table('water_discharged_monitoring')
            ->whereDate('date', $now->format('Y-m-d'))->exists();
        
        if ($existing_record){
            return response()->json(['success' => 0, 'message' => 'Water discharge record for today already exists.']);
        }
        
        $wd_monitoring = [
            'operating_hrs' => $request->operating_hrs,
            'previous' => $request->previous_inputs,
            'present' => $request->present_inputs,
            'incoming_water_discharged' => $request->incoming_water_discharged,
            'operator_id' => $request->inspected_by,
            'date' =>  Carbon::parse($request->water_date)->format('Y-m-d'),
            'created_by' => $email->email,
            'created_at' => $now->toDateTimeString()
        ];

        DB::connection('mysql_mes')->table('water_discharged_monitoring')->insert($wd_monitoring);
        
        return response()->json(['success' => 1,'message' => 'Water discharge today successfully inserted.']);
    }

    public function get_chemical_records_modal_details(){
        $now = Carbon::now();
        if (DB::connection('mysql_mes')
            ->table('chemical_monitoring')
            ->whereDate('date', $now->format('Y-m-d'))
            ->exists()){
                $note = '<b>Note:</b>'.'     '.'&nbsp; <i>Painting chemical monitoring for today already exist.</i>';
        }else{
            $note ="";
        }
            return view('painting_operator.tbl_chemical_records', compact('note'));
                            
    }
    public function submit_painting_chemical_records(Request $request){
        $now = Carbon::now();
        if (DB::connection('mysql_mes')
                        ->table('chemical_monitoring')
                        ->whereDate('date', $now->format('Y-m-d'))
                        ->exists()){
                        return response()->json(['success' => 0, 'message' => 'Painting chemical record for today already exist.']);
            }else{
                $email= DB::connection('mysql_essex')
                        ->table('users')
                        ->where('users.user_id', $request->inspected_by)
                        ->select('users.email')
                        ->first();
                    $data=[];
                    $chem_monitoring=[];
                        $chem_monitoring = [
                        'date' =>  $now->toDateTimeString(),
                        'degreasing_freealkali' => $request->deg_freealkali,
                        'degrasing_point' =>  $request->degreasing_type,
                        'degreasing_increase_type' => ($request->degreasing_type_input == null)? "": $request->degreasing_type_input,
                        'degreasing_status' => "Good",
                        'phospating_acid' => $request->replenshing,
                        'phospating_acid_point' => $request->replenshing_type,
                        'phospating_increase_type' => ($request->replenshing_type_input == null)? "":$request->replenshing_type_input,
                        'phospating_acid_status' => "Good",
                        'phospating_accelerator' => $request->accelerator,
                        'accelerator_increase_point' => $request->accelerator_type,
                        'accelerator_increase_type' => ($request->accelerator_type_input == null)? "": $request->accelerator_type_input,
                        'phospating_accelerator_status' => "Good",
                        'last_modified_by' => $email->email,
                        'created_by' => $email->email,
                        'created_at' => $now->toDateTimeString()
                    ];
    
                    DB::connection('mysql_mes')->table('chemical_monitoring')->insert($chem_monitoring);
                                    
                    return response()->json(['success' => 1,'message' => 'Painting chemical record for today successfully inserted.']);
            }
        }
    public function get_tbl_qa_visual(Request $request){
        $sampling_plan = DB::connection('mysql_mes')->table('qa_sampling_plan as qa_sp')
            ->join('reject_category as rc','qa_sp.reject_category_id', 'rc.reject_category_id')
            ->where('qa_sp.reject_category_id', 1)
            ->orderBy('lot_size_min', 'asc')->paginate(15);

        return view('tables.tbl_qa_sampling_plan_visual', compact('sampling_plan'));
    }

    public function get_tbl_qa_variable(Request $request){
        $sampling_plan = DB::connection('mysql_mes')->table('qa_sampling_plan as qa_sp')
            ->join('reject_category as rc','qa_sp.reject_category_id', 'rc.reject_category_id')
            ->where('qa_sp.reject_category_id', 2)
            ->orderBy('lot_size_min', 'asc')->paginate(15);

        return view('tables.tbl_qa_sampling_plan_variable', compact('sampling_plan'));
    }

    public function get_tbl_qa_reliability(Request $request){
        $sampling_plan = DB::connection('mysql_mes')->table('qa_sampling_plan as qa_sp')
            ->join('reject_category as rc','qa_sp.reject_category_id', 'rc.reject_category_id')
            ->where('qa_sp.reject_category_id', 3)
            ->orderBy('lot_size_min', 'asc')->paginate(15);

        return view('tables.tbl_qa_sampling_plan_reliability', compact('sampling_plan'));
    }
    public function save_sampling_plan(Request $request){
        $now = Carbon::now();
        $data=[];
            $reject_category=[];
                if (DB::connection('mysql_mes')
                    ->table('qa_sampling_plan')
                    ->where('reject_category_id', $request->sp_category)
                    ->where('lot_size_min', $request->lot_min)
                    ->where('lot_size_max', $request->lot_max)
                    ->where('sample_size', $request->spl_size)
                    ->where('acceptance_level', $request->accpt_lvl)
                    ->where('reject_level', $request->rjt_lvl)
                    ->exists()){

                    return response()->json(['success' => 0, 'message' => 'Sampling plan already exist.']);
                }else{
                    $getmin_max_plan= DB::connection('mysql_mes')
                    ->table('qa_sampling_plan')
                    ->where('reject_category_id', $request->sp_category)->get();
                    $max= collect($getmin_max_plan)->max('lot_size_max');
                    if($max < $request->lot_min){
                        if($max < $request->lot_max ){
                            $sampling_plan = [
                            'reject_category_id' => $request->sp_category,
                            'lot_size_min' => $request->lot_min,
                            'lot_size_max' => $request->lot_max,
                            'sample_size' => $request->spl_size,
                            'acceptance_level' => $request->accpt_lvl,
                            'reject_level' => $request->rjt_lvl,
                            'last_modified_by' => Auth::user()->email,
                            'created_by' => Auth::user()->email,
                            'created_at' => $now->toDateTimeString()
                            ];

                            $data=[
                                'success' =>1,
                                'message' => 'New Sampling plan is successfully added!',
                                'val'=> $request->sp_category

                            ];
                            DB::connection('mysql_mes')->table('qa_sampling_plan')->insert($sampling_plan);
                            return $data; 
                        }else{
                            return response()->json(['success' => 0,'message' => 'Lot max size must be greater than Lot min size.']);
                        }
                    }else{
                            return response()->json(['success' => 0,'message' => 'Lot min size must be greater than '.$max.'.']); 
                    }
                }
    }
    public function delete_sampling_plan(Request $request){
            DB::connection('mysql_mes')->table('qa_sampling_plan')->where('sampling_plan_id', $request->delete_sampling_plan_id)->delete();
            return response()->json(['success' => 1, 'message' => 'Sampling Plan successfully deleted!','category'=>$request->delete_sampling_plan_category]);

    }
    public function get_max_for_min_sampling_plan($id){
        $getmin_max_plan= DB::connection('mysql_mes')
            ->table('qa_sampling_plan')
            ->where('reject_category_id', $id)->get();
        $max= (collect($getmin_max_plan)->max('lot_size_max'))+ 1;
        return $max;

    }
    public function get_reject_category_for_add_reject_modal(){
        $ouput_material_type='<option value="">Select Material Type</option>';
        $output_category= '<option value="">Select Category</option>';
        $output_operation='<option value="">Select Operation</option>';
        $category= DB::connection('mysql_mes')->table('reject_category')->get();
        $get_operation=DB::connection('mysql_mes')->table('operation')->get();
        $get_material_type=DB::connection('mysql_mes')->table('reject_material_type')->get();

        foreach($category as $row){
            $output_category .= '<option value="'.$row->reject_category_id.'">'.$row->reject_category_name.'</option>';
        }
        foreach($get_operation as $row){
            $output_operation .= '<option value="'.$row->operation_id.'">'.$row->operation_name.'</option>';
        }
        foreach($get_material_type as $row){
            $ouput_material_type .= '<option value="'.$row->reject_material_type_id.'">'.$row->material_type.'</option>';
        }
        return response()->json(['success' => 1, 'operation' => $output_operation,'category'=>$output_category, 'material_type' => $ouput_material_type ]);
    }
    public function save_checklist(Request $request){
        $now = Carbon::now();
        $arr = $request->new_checklist_r_desc;
        $ar=array_unique(array_diff_assoc($arr, array_unique( $arr ) ) );
        if(!empty($ar)){
            foreach($ar as $i => $r){
                $reject_desc =DB::connection('mysql_mes')->table('reject_list')
                ->where('reject_list_id', $r)
                ->first();
                $row= $i +1;

                $workstation= DB::connection('mysql_mes')->table('workstation')
                ->where('workstation_id', $request->workstation_id)
                ->first(); 
                return response()->json(['success' => 0, 'message' => 'Please check DUPLICATE '.$reject_desc->reject_checklist.' at ROW '.$row ]);

            }
            
        }else{
            if ($request->new_checklist_r_desc) {   
                foreach($request->new_checklist_r_desc as $i => $row){
                    if (DB::connection('mysql_mes')
                        ->table('qa_checklist')
                        ->where('workstation_id', $request->workstation_id)
                        ->where('reject_list_id', $request->new_checklist_r_desc[$i])
                        ->where('process_id', $request->new_checklist_r_process[$i])
                        ->exists()){

                        $reject_desc =DB::connection('mysql_mes')->table('reject_list')
                        ->where('reject_list_id', $request->new_checklist_r_desc[$i])
                        ->first();

                        $workstation= DB::connection('mysql_mes')->table('workstation')
                        ->where('workstation_id', $request->workstation_id)
                        ->first();

                        return response()->json(['success' => 0, 'message' => 'Checklist '.$reject_desc->reject_checklist.' is already exist in '.$workstation->workstation_name ]);
                    }else{
                      $checklist[] = [
                        'workstation_id' => $request->workstation_id,
                        'process_id' => $request->new_checklist_r_process[$i],
                        'reject_list_id' => $request->new_checklist_r_desc[$i],
                        'last_modified_by' => Auth::user()->email,
                        'created_by' => Auth::user()->email,
                        'created_at' => $now->toDateTimeString()
                        ];
                    } 
                }
                DB::connection('mysql_mes')->table('qa_checklist')->insert($checklist);
                
            }
        }
            

        return response()->json(['message' => 'New checklist has been created.']);
    }

    public function get_tbl_checklist_list_fabrication(Request $request){
        $check_list = DB::connection('mysql_mes')->table('qa_checklist as qc')
            ->join('workstation as w','w.workstation_id', 'qc.workstation_id')
            ->join('reject_list as rl','rl.reject_list_id', 'qc.reject_list_id')
            ->join('reject_category as rc','rl.reject_category_id', 'rc.reject_category_id')
            ->join('operation as op', 'op.operation_id', 'w.operation_id')
            ->where('op.operation_name', 'Fabrication')
            ->where('w.workstation_name','!=','Painting')
            ->when($request->search_string, function ($query) use ($request){
                $query->where(function($q) use ($request) {
                    return $q->where('w.workstation_name', 'like', '%'.$request->search_string.'%')
                        ->orWhere('rc.reject_category_name', 'like', '%'.$request->search_string.'%')
                        ->orWhere('rl.reject_reason', 'like', '%'.$request->search_string.'%')
                        ->orWhere('rl.reject_checklist', 'like', '%'.$request->search_string.'%');
                });
            })
            ->select('w.workstation_name', 'qc.*','rc.reject_category_name','rl.reject_reason', 'rl.reject_checklist','op.operation_name')
            ->orderBy('qa_checklist_id', 'desc')->paginate(15);

        return view('tables.tbl_check_list_fabrication', compact('check_list'));

    }
    public function delete_checklist(Request $request){
            DB::connection('mysql_mes')->table('qa_checklist')->where('qa_checklist_id', $request->check_list_id)->delete();
            return response()->json(['success' => 1, 'message' => 'Checklist Successfully deleted!']);

    }
    public function get_tbl_checklist_list_painting(Request $request){
        $check_list = DB::connection('mysql_mes')->table('qa_checklist as qc')
            ->leftJoin('process', 'process.process_id', 'qc.process_id')
            ->join('workstation as w','w.workstation_id', 'qc.workstation_id')
            ->join('reject_list as rl','rl.reject_list_id', 'qc.reject_list_id')
            ->join('reject_category as rc','rl.reject_category_id', 'rc.reject_category_id')
            ->join('operation as op', 'op.operation_id', 'w.operation_id')
            ->where('w.workstation_name','=','Painting')
            ->when($request->search_string, function ($query) use ($request){
                $query->where(function($q) use ($request) {
                    return $q->where('w.workstation_name', 'like', '%'.$request->search_string.'%')
                        ->orWhere('rc.reject_category_name', 'like', '%'.$request->search_string.'%')
                        ->orWhere('rl.reject_reason', 'like', '%'.$request->search_string.'%')
                        ->orWhere('rl.reject_checklist', 'like', '%'.$request->search_string.'%')
                        ->orWhere('process.process_name', 'like', '%'.$request->search_string.'%');
                });
            })
            ->select('w.workstation_name', 'qc.*','rc.reject_category_name','rl.reject_reason', 'rl.reject_checklist','w.workstation_name as operation_name', 'process.process_name')
            ->orderBy('qa_checklist_id', 'desc')->paginate(15);

        return view('tables.tbl_check_list_painting', compact('check_list'));
    }
    public function get_tbl_checklist_list_assembly(Request $request){
        $check_list = DB::connection('mysql_mes')->table('qa_checklist as qc')
            ->join('workstation as w','w.workstation_id', 'qc.workstation_id')
            ->join('reject_list as rl','rl.reject_list_id', 'qc.reject_list_id')
            ->join('reject_category as rc','rl.reject_category_id', 'rc.reject_category_id')
            ->join('operation as op', 'op.operation_id', 'w.operation_id')
            ->where('op.operation_name', 'Wiring and Assembly')
            ->when($request->search_string, function ($query) use ($request){
                $query->where(function($q) use ($request) {
                    return $q->where('w.workstation_name', 'like', '%'.$request->search_string.'%')
                        ->orWhere('rc.reject_category_name', 'like', '%'.$request->search_string.'%')
                        ->orWhere('rl.reject_reason', 'like', '%'.$request->search_string.'%')
                        ->orWhere('rl.reject_checklist', 'like', '%'.$request->search_string.'%');
                });
            })
            ->select('w.workstation_name', 'qc.*','rc.reject_category_name','rl.reject_reason', 'rl.reject_checklist','w.workstation_name as operation_name')
            ->orderBy('qa_checklist_id', 'desc')->paginate(15);

        return view('tables.tbl_check_list_assembly', compact('check_list'));

    }
    public function get_workstation_list_from_checklist($id){
        $output="";
            if ($id == "Painting") {
                $workstation= DB::connection('mysql_mes')->table('workstation as w')
                ->join('operation as op','op.operation_id', 'w.operation_id')
                ->where('w.workstation_name', "Painting")
                ->select('op.operation_name as operation','w.workstation_id', 'w.workstation_name')
                ->get();
            }else{
                $operation = ($id == "Fabrication")? '1':'3';
                $workstation= DB::connection('mysql_mes')->table('workstation as w')
                ->join('operation as op','op.operation_id', 'w.operation_id')
                ->where('w.operation_id', $operation)
                ->where('w.workstation_name', '!=', "Painting")
                ->select('op.operation_name as operation','w.workstation_id', 'w.workstation_name')
                ->get();
            }
            
            foreach($workstation as $row)
                 {
                $output .= '<option value="'.$row->workstation_id.'">'.$row->workstation_name.'</option>';
                 }
            
        return $output;
    }
    public function save_reject_list(Request $request){

        $now = Carbon::now();
        $data = $request->all();
        $reason= $data['reject_reason'];
        $responsible = $data['responsible'];
        $action= $data['r_action'];
        $m_type=$data['m_type'];
        $r_owner= $data['reject_owner'];
        if($r_owner == "Operator"){
            $checklist=null;
        }else{
            $checklist=$data['reject_checklist'];
        }
                foreach($reason as $i => $row){
                    if (DB::connection('mysql_mes')
                        ->table('reject_list')
                        ->where('reject_category_id', $request->reject_category)
                        ->where('reject_checklist', $checklist[$i])
                        ->where('reject_material_type_id', $request->m_type[$i])
                        ->where('reject_reason', $row)
                        ->where('owner',$r_owner)
                        ->where('operation_id',$request->op_operation)
                        ->where('responsible', $responsible[$i])
                        ->where('recommended_action', $action[$i])
                        ->where('owner', $request->reject_owner)
                        ->exists()){

                        return response()->json(['success' => 0, 'message' => 'Reject - <b>'.$request->reject_checklist.'</b> is already exist']);
                    }else{
                        $rejectlist[] = [
                            'reject_category_id' => $request->reject_category,
                            'reject_checklist' => $checklist[$i],
                            'reject_material_type_id' => $m_type[$i],
                            'reject_reason' => $row,
                            'owner' => $r_owner,
                            'operation_id' => $request->op_operation,
                            'responsible' => $responsible[$i],
                            'recommended_action' => $action[$i],
                            'last_modified_by' => Auth::user()->email,
                            'created_by' => Auth::user()->email,
                            'created_at' => $now->toDateTimeString()
                            ];
                    } 
            }
            DB::connection('mysql_mes')->table('reject_list')->insert($rejectlist);
            return response()->json(['success' => 1,'message' => 'Reject list is successfully inserted.', 'reloadtbl' => $r_owner ]);
 
    }
    public function get_tbl_qa_reject_list(Request $request){
        $reject_list = DB::connection('mysql_mes')->table('reject_list as rl')
            ->leftJoin('reject_material_type', 'reject_material_type.reject_material_type_id', 'rl.reject_material_type_id')
            ->join('reject_category as rc','rl.reject_category_id', 'rc.reject_category_id')
            ->whereIn('rl.owner', ['Quality Assurance','null'])
            ->where(function($q) use ($request) {
                $q->where('rl.reject_checklist', 'LIKE', '%'.$request->search_string.'%')
                    ->where('rl.reject_checklist', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rl.reject_reason', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rl.recommended_action', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rc.reject_category_name', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('reject_material_type.material_type', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rl.responsible', 'LIKE', '%'.$request->search_string.'%');
            })
            ->select('rl.*','rc.reject_category_name', 'reject_material_type.material_type')
            ->orderBy('reject_list_id', 'desc')->paginate(15);
            
        return view('tables.tbl_reject_list', compact('reject_list'));

    }
    public function get_tbl_op_reject_list(Request $request){
        $reject_list = DB::connection('mysql_mes')->table('reject_list as rl')
            ->join('reject_category as rc','rl.reject_category_id', 'rc.reject_category_id')
            ->leftJoin('reject_material_type', 'reject_material_type.reject_material_type_id', 'rl.reject_material_type_id')
            ->leftJoin('operation', 'operation.operation_id', 'rl.operation_id')->where('rl.owner', 'Operator')
            ->where(function($q) use ($request) {
                $q->where('rl.reject_reason', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rl.recommended_action', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rc.reject_category_name', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('reject_material_type.material_type', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('operation.operation_name', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rl.responsible', 'LIKE', '%'.$request->search_string.'%');
            })
            ->select('rl.*','rc.reject_category_name', 'reject_material_type.material_type', 'operation.operation_name')
            ->orderBy('reject_list_id', 'desc')->paginate(15);
            
        return view('tables.tbl_reject_op_list', compact('reject_list'));
    }

    public function update_reject_list(Request $request){
        $now = Carbon::now();
        // dd($request->all());
        $rejectlist = [];
            if (DB::connection('mysql_mes')->table('reject_list')
                ->where('reject_category_id', $request->edit_reject_category)
                ->where('reject_checklist', $request->edit_reject_checklist)
                ->where('reject_reason', $request->edit_reject_reason)
                ->where('responsible', $request->edit_reject_responsible)
                ->where('recommended_action', $request->edit_r_action)
                ->where('owner', $request->edit_reject_owner)
                ->where('reject_material_type_id', $request->edit_material_type)
                ->exists()){

                    if(strtoupper($request->orig_reject_category) == strtoupper($request->edit_reject_category) && strtoupper($request->orig_reject_checklist) == strtoupper($request->edit_reject_checklist) && strtoupper($request->orig_reject_reason) == strtoupper($request->edit_reject_reason) && strtoupper($request->edit_reject_responsible) == strtoupper($request->orig_reject_responsible) && strtoupper($request->edit_r_action) == strtoupper($request->orig_r_action) && strtoupper($request->orig_material_type) == strtoupper($request->edit_material_type) && strtoupper($request->orig_reject_operation) == strtoupper($request->edit_reject_operation)){
                        $rejectlist = [
                        'reject_category_id' => $request->edit_reject_category,
                        'reject_checklist' => $request->edit_reject_checklist,
                        'reject_reason' => $request->edit_reject_reason,
                        'owner'=> $request->edit_reject_owner,
                        'responsible' => $request->edit_reject_responsible,
                        'recommended_action' => $request->edit_r_action,
                        'reject_material_type_id' => $request->edit_material_type,
                        'last_modified_by' => Auth::user()->email,
                        'operation_id' => $request->edit_reject_operation,
                        ];
                        DB::connection('mysql_mes')->table('reject_list')->where('reject_list_id', $request->edit_id_reject)->update($rejectlist);
                        return response()->json(['success' => 1,'message' => 'Reject Checklist is successfully updated.','reloadtbl' => $request->reloadtbl_edit]);
                    }else{
                        return response()->json(['success' => 0, 'message' => 'Reject - <b>'.$request->edit_reject_checklist.'</b> is already exist']);           

                    }
            }else{
                        $rejectlist = [
                        'reject_category_id' => $request->edit_reject_category,
                        'reject_checklist' => $request->edit_reject_checklist,
                        'reject_reason' => $request->edit_reject_reason,
                        'owner'=> $request->edit_reject_owner,
                        'responsible' => $request->edit_reject_responsible,
                        'recommended_action' => $request->edit_r_action,
                        'reject_material_type_id' => $request->edit_material_type,
                        'last_modified_by' => Auth::user()->email,
                        'operation_id' => $request->edit_reject_operation,

                        ];
                        DB::connection('mysql_mes')->table('reject_list')->where('reject_list_id', $request->edit_id_reject)->update($rejectlist);
                        return response()->json(['success' => 1,'message' => 'Reject Checklist is successfully updated.','reloadtbl' => $request->reloadtbl_edit]);
            }
        
    }
    public function delete_rejectlist(Request $request){
        if (DB::connection('mysql_mes')->table('qa_checklist')->where('reject_list_id', $request->delete_rejectlist_id)
        ->exists()){
            return response()->json(['success' => 0, 'message' => 'Unable to Delete Reject list. Reject list already asigned QA checklist.']);

        }else{
            DB::connection('mysql_mes')->table('reject_list')->where('reject_list_id', $request->delete_rejectlist_id)->delete();
            return response()->json(['success' => 1, 'message' => 'Reject Checklist Successfully deleted!', 'reloadtbl' => $request->delete_reloadtbl]);
        }
          

    }
    public function save_reject_category(Request $request){
        $now = Carbon::now();
            $reject_category=[];
                    if (DB::connection('mysql_mes')
                        ->table('reject_category')
                        ->where('type', $request->reject_ctgtype)
                        ->where('reject_category_name', $request->reject_category)
                        ->where('category_description', $request->reject_ctgdesc)
                        ->exists()){

                        return response()->json(['success' => 0, 'message' => 'Reject category - <b>'.$request->reject_category.'</b> is already exist']);
                    }else{
                      $reject_category= [
                        'type' => $request->reject_ctgtype,
                        'reject_category_name' => $request->reject_category,
                        'category_description' => $request->reject_ctgdesc,
                        'last_modified_by' => Auth::user()->email,
                        'created_by' => Auth::user()->email,
                        'created_at' => $now->toDateTimeString()
                        ];
                        DB::connection('mysql_mes')->table('reject_category')->insert($reject_category);
                        return response()->json(['message' => 'Reject category is successfully inserted.']);
                    } 

        
    }

    public function get_tbl_reject_category(Request $request){
        $reject_category = DB::connection('mysql_mes')->table('reject_category as rc')
            ->where(function($q) use ($request) {
                $q->Where('rc.reject_category_name', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rc.type', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rc.type', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rc.category_description', 'LIKE', '%'.$request->search_string.'%');
            })
            ->orderBy('reject_category_id', 'desc')->paginate(15);

        return view('tables.tbl_reject_category', compact('reject_category'));
    }

    public function update_reject_category(Request $request){
        $now = Carbon::now();
        $reject_category = [];
            if (DB::connection('mysql_mes')
                ->table('reject_category')
                ->where('type', $request->edit_type)
                ->where('reject_category_name', $request->edit_category)
                ->where('category_description', $request->edit_reject_ctgdesc)
                ->exists()){

                    if(strtoupper($request->orig_reject_ctgtype) == strtoupper($request->edit_type) && strtoupper($request->orig_reject_category) == strtoupper($request->edit_category) && strtoupper($request->orig_reject_ctgdesc) == strtoupper($request->edit_reject_ctgdesc)){
                        
                        $reject_category= [
                        'type' => $request->edit_type,
                        'reject_category_name' => $request->edit_category,
                        'category_description' => $request->edit_reject_ctgdesc,
                        'last_modified_by' => Auth::user()->email,
                        ];
                        DB::connection('mysql_mes')->table('reject_category')->where('reject_category_id', $request->ctg_id)->update($reject_category);
                        return response()->json(['message' => 'Reject category is successfully updated.']);
                    }else{
                        return response()->json(['success' => 0, 'message' => 'Reject category - <b>'.$request->edit_category.'</b> is already exist']);           

                    }
            }
            else{
                        $reject_category= [
                        'type' => $request->edit_type,
                        'reject_category_name' => $request->edit_category,
                        'category_description' => $request->edit_reject_ctgdesc,
                        'last_modified_by' => Auth::user()->email
                        ];
                        DB::connection('mysql_mes')->table('reject_category')->where('reject_category_id', $request->ctg_id)->update($reject_category);
                        return response()->json(['message' => 'Reject category is successfully updated.']);
            }
    }
    public function delete_reject_category(Request $request){
            DB::connection('mysql_mes')->table('reject_category')->where('reject_category_id', $request->delete_reject_category_id)->delete();
        return response()->json(['success' => 1, 'message' => 'Reject category is successfully deleted!']);
    }
    public function get_user_role_by_module($module){
        $output = '<option value="">Select User Role</option>';
        
            $shift_list=DB::connection('mysql_mes')
                ->table('user_group')
                ->where('module',$module)
                ->groupBy('user_role', 'user_group_id')
                ->select('user_role',"user_group_id")
                ->get();

            foreach($shift_list as $row)
                 {
                $output .= '<option value="'.$row->user_group_id.'">'.$row->user_role.'</option>';
                 }

            // dd($output);
        return $output;
    }
    public function get_users_group(Request $request){
        $list = DB::connection('mysql_mes')->table('user_group')
            ->orderBy('user_group_id', 'desc')->paginate(8);
        
        
        return view('tables.tbl_user_group', compact('list'));
    }
    public function save_user_group(Request $request){
        DB::connection('mysql_mes')->beginTransaction();
        try {
            $now = Carbon::now();
            $checker = DB::connection('mysql_mes')->table('user_group')->where('module', $request->add_user_group)->where('user_role', $request->add_user_role)->exists();

            if($checker){
                return response()->json(['success' => 0,'message' => 'User Group already exists.']);
            }

            $data = [
                'module' => $request->add_user_group,
                'user_role' => $request->add_user_role,
                'created_by' => Auth::user()->email,
                'created_at' => $now->toDateTimeString(),
                'last_modified_by' => Auth::user()->email,
                'last_modified_at' => $now->toDateTimeString()
            ];
            
            DB::connection('mysql_mes')->table('user_group')->insert($data);

            DB::connection('mysql_mes')->commit();
            return response()->json(['success' => 1,'message' => 'User Group has been saved.']);
        } catch (\Throwable $th) {
            //throw $th;
            DB::connection('mysql_mes')->rollback();
            return response()->json(['success' => 0,'message' => 'An error occured. Please try again later.']);
        }
    }
    public function update_user_group(Request $request){
        DB::connection('mysql_mes')->beginTransaction();
        try {
            $now = Carbon::now();
            $checker = DB::connection('mysql_mes')->table('user_group')->where('module', $request->edit_user_group_regis)->where('user_role', $request->edit_user_role_regis)->exists();
            
            if($checker){
                return response()->json(['success' => 0, 'message' => 'User Group already exists.']);
            }

            $data = [
                'module' => $request->edit_user_group_regis,
                'user_role' => $request->edit_user_role_regis,
                'last_modified_by' => Auth::user()->employee_name,
                'last_modified_at' => $now->toDateTimeString()
            ];

            DB::connection('mysql_mes')->table('user_group')->where('user_group_id', $request->edit_user_group_regis_id)->update($data);

            DB::connection('mysql_mes')->commit();
            return response()->json(['success' => 1,'message' => 'User Group has been updated.']);
        } catch (\Throwable $th) {
            //throw $th;
            DB::connection('mysql_mes')->rollback();
            return response()->json(['success' => 0,'message' => 'An error occured. Please try again later.']);
        }
    }
    public function delete_user_group(Request $request){
        DB::connection('mysql_mes')->table('user_group')->where('user_group_id', $request->delete_user_group_id)->delete();

        return response()->json(['success' => 1,'message' => 'User group successfully deleted.']);
    }
    public function reject_operator_list(){
        $check_list = DB::connection('mysql_mes')->table('qa_checklist as qc')
            ->join('workstation as w','w.workstation_id', 'qc.workstation_id')
            ->join('reject_list as rl','rl.reject_list_id', 'qc.reject_list_id')
            ->join('reject_category as rc','rl.reject_category_id', 'rc.reject_category_id')
            ->join('operation as op', 'op.operation_id', 'w.operation_id')
            // ->where('rl.owner', 'Operator')
            // ->where('op.operation_name', 'Fabrication')
            // ->where('w.workstation_name','!=','Painting')
            ->select('w.workstation_name', 'qc.*','rc.reject_category_name','rl.reject_reason', 'rl.reject_checklist','op.operation_name', "rl.responsible", 'rl.recommended_action')
            ->orderBy('qa_checklist_id', 'desc')->paginate(9);
        // dd($check_list);

        return view('tables.tbl_check_list_operator', compact('check_list'));

    }
    public function get_reject_type_desc(Request $request){
        return  DB::connection('mysql_mes')->table('reject_category')->get();
    }
    public function get_reject_desc($reject_type, $id, $operation){
        $output="";
        $operation_id= ($operation == "Painting")? '2':'1';
        if($id == "Operator"){
            $reject_desc =DB::connection('mysql_mes')->table('reject_list')
            ->where('reject_category_id', $reject_type)
            ->where('owner', $id)
            ->where('operation_id', $operation_id)
            ->get();
            
            foreach($reject_desc as $row){
                $output .= '<option value="'.$row->reject_list_id.'">'.$row->reject_reason.'</option>';
            }

        }else{
            $reject_desc =DB::connection('mysql_mes')->table('reject_list')
            ->where('reject_category_id', $reject_type)
            ->where('owner', $id)
            ->get();
            
            foreach($reject_desc as $row){
                $output .= '<option value="'.$row->reject_list_id.'">'.$row->reject_reason.'</option>';
            }
        }
        return $output;
    }
    public function item_classification_warehouse_tbl_fabrication(Request $request){
        $item_classification = DB::connection('mysql_mes')->table('item_classification_warehouse as icw')
            ->join('operation', 'operation.operation_id','icw.operation_id')
            ->select('icw.*', 'operation.operation_name')
            ->where('operation.operation_name', 'LIKE', '%'."Fabrication".'%') 
            ->when($request->search_string, function ($query) use ($request) {
                return $query->where(function($search_string) use ($request) {
                $search_string->where('icw.item_classification', 'LIKE', '%'.$request->search_string.'%')
                ->orwhere('icw.warehouse', 'LIKE', '%'.$request->search_string.'%')
                ->orwhere('operation.operation_name', 'LIKE', '%'.$request->search_string.'%');
                });
            })
            ->orderBy('icw.item_classification_warehouse_id', 'desc')->paginate(15);

        return view('inventory.tbl_item_classification_warehouse_fabrication', compact('item_classification'));
    }

    public function item_classification_warehouse_tbl_painting(Request $request){
        $item_classification = DB::connection('mysql_mes')->table('item_classification_warehouse as icw')
            ->join('operation', 'operation.operation_id','icw.operation_id')
            ->select('icw.*', 'operation.operation_name')
            ->where('operation.operation_name', 'LIKE', '%'."Painting".'%')  
            ->when($request->search_string, function ($query) use ($request) {
                return $query->where(function($search_string) use ($request) {
                $search_string->where('icw.item_classification', 'LIKE', '%'.$request->search_string.'%')
                ->orwhere('icw.warehouse', 'LIKE', '%'.$request->search_string.'%')
                ->orwhere('operation.operation_name', 'LIKE', '%'.$request->search_string.'%');
                });
            })
            ->orderBy('icw.item_classification_warehouse_id', 'desc')->paginate(15);

        return view('inventory.tbl_item_classification_warehouse_painting', compact('item_classification'));
    }

    public function item_classification_warehouse_tbl_assembly(Request $request){
        $item_classification = DB::connection('mysql_mes')->table('item_classification_warehouse as icw')
            ->join('operation', 'operation.operation_id','icw.operation_id')
            ->select('icw.*', 'operation.operation_name')
            ->where('operation.operation_name', 'LIKE', '%'."Assembly".'%')
            ->when($request->search_string, function ($query) use ($request) {
                return $query->where(function($search_string) use ($request) {
                $search_string->where('icw.item_classification', 'LIKE', '%'.$request->search_string.'%')
                ->orwhere('icw.warehouse', 'LIKE', '%'.$request->search_string.'%')
                ->orwhere('operation.operation_name', 'LIKE', '%'.$request->search_string.'%');
                });
            })
            ->orderBy('icw.item_classification_warehouse_id', 'desc')->paginate(15);

        return view('inventory.tbl_item_classification_warehouse_assembly', compact('item_classification'));
    }
    
    public function insert_item_classification_warehouse(Request $request){

        $now = Carbon::now();
        $data = $request->all();
        // $igroup = $data['new_item_group'];
        $iclass = $data['new_item_class'];
        $ssource = $data['new_source_warehouse'];
        // $tsource = $data['new_target_warehouse'];

     // return $data;
            // if ($request->new_item_group) { 
               foreach($iclass as $i => $row){
                   $rclass=$iclass[$i];
                   $rsware=$ssource[$i];
                //    $rtware=$tsource[$i];

                    if (DB::connection('mysql_mes')
                        ->table('item_classification_warehouse')
                        ->where('operation_id', $request->operation)
                        ->when(!empty($rclass), function ($query1) use($row, $rsware, $rclass){
                            // return $query1->where('item_group', $row)
                            return $query1->where('item_classification',  $rclass)
                                            ->where('warehouse',$rsware);
                                            // ->where('target_warehouse',$rtware);
                                            })
                        ->when(empty($rclass), function ($query1) use($row, $rsware, $rclass){
                            // return $query1->where('item_group', $row)
                            return $query1->where('warehouse',$rsware);
                                        //   ->where('target_warehouse',$rtware);
                            })
                        ->exists()){
                            return response()->json(['success' => 0, 'message' => 'Source Warehouse and Target Warehouse for '.$row.' is already exist.']);
                        
                    }elseif($row == 'none'){
                            return response()->json(['success' => 0, 'message' => 'Please Select Item Group']);

                
                    }elseif($ssource[$i] == 'none'){
                            return response()->json(['success' => 0, 'message' => 'Please Select Source Warehouse']);

                    }
                    // elseif($tsource[$i] == 'none'){
                    //         return response()->json(['success' => 0, 'message' => 'Please Select Target Warehouse']);

                    // }
                    else{
                        // if ($ssource[$i] == $tsource[$i]) {
                        //     return response()->json(['success' => 0, 'message' => 'Source Warehouse must not be the same as Target Warehouse for '.$row]);
                        // }else{
                            $data = [
                            'operation_id' => $request->operation,
                            // 'item_group' => $row,
                            'item_classification' => $iclass[$i],
                            'warehouse' => $ssource[$i],
                            // 'target_warehouse' => $tsource[$i],
                            'last_modified_by' => Auth::user()->email,
                            'created_by' => Auth::user()->email,
                            'created_at' => $now->toDateTimeString()
                            ];
                            DB::connection('mysql_mes')->table('item_classification_warehouse')->insert($data);
                        // }
                    } 
                }
            // }

        return response()->json(['message' => 'New checklist has been created.']);
    }
    
    public function update_item_classification_warehouse(Request $request){
        $now = Carbon::now();
        $reject_category = [];
            $egroup=$request->edit_item_group;
            $eclass=$request->edit_item_classification;
            $esware=$request->edit_s_warehouse;
            $etware=$request->edit_t_warehouse;
            if (DB::connection('mysql_mes')
                    ->table('item_classification_warehouse')
                    ->where('operation_id', $request->edit_operation)
                    ->when(!empty($eclass), function ($query1) use($egroup, $esware, $etware, $eclass){
                        // return $query1->where('item_group', $egroup)
                        return $query1->where('item_classification', $eclass)
                                        ->where('warehouse',$esware);
                                        // ->where('target_warehouse',$etware);
                                        })
                    ->when(empty($eclass), function ($query1) use($egroup, $esware, $etware){
                        // return $query1->where('item_group', $egroup)
                        return $query1->where('warehouse',$esware);
                                    //   ->where('target_warehouse',$etware);
                        })
                    ->exists()){

                    if(strtoupper($request->orig_item_class) == strtoupper($eclass) && strtoupper($request->orig_operation) == strtoupper($request->edit_operation) && strtoupper($request->orig_source_w) == strtoupper($esware) && strtoupper($request->orig_target_w) == strtoupper($etware) && strtoupper($request->orig_item_group) == strtoupper($egroup)){
                        
                        $data=[];
                            $data = [
                            // 'item_group' => $egroup,
                            'item_classification' => $eclass,
                            'operation_id' => $request->edit_operation,
                            'warehouse' => $esware,
                            // 'target_warehouse' =>  $etware,
                            'last_modified_by' => Auth::user()->employee_name,
                            'last_modified_at' => $now->toDateTimeString()
                        ];

                        DB::connection('mysql_mes')->table('item_classification_warehouse')->where('item_classification_warehouse_id', $request->icw_id)->update($data);
                        return response()->json(['success' => 1,'message' => 'Source Warehouse and Target Warehouse for '.$eclass.''.' is successfully updated.']);
                    }else{
                        return response()->json(['success' => 0, 'message' => 'Source Warehouse and Target Warehouse for '.$eclass.''.' is already exist.']);        

                    }
            }
            else{
                        $data=[];
                            $data = [
                            // 'item_group' => $request->edit_item_group,
                            'item_classification' => $request->edit_item_classification,
                            'operation_id' => $request->edit_operation,
                            'warehouse' => $request->edit_s_warehouse,
                            // 'target_warehouse' =>  $request->edit_t_warehouse,
                            'last_modified_by' => Auth::user()->employee_name,
                            'last_modified_at' => $now->toDateTimeString()
                        ];
                        DB::connection('mysql_mes')->table('item_classification_warehouse')->where('item_classification_warehouse_id', $request->icw_id)->update($data);
                        return response()->json(['success' => 1,'message' => 'Source Warehouse and Target Warehouse for '.$request->edit_item_classification.''.' is successfully updated.']);
            }
    }
    public function delete_item_classification_warehouse(Request $request){
            DB::connection('mysql_mes')->table('item_classification_warehouse')->where('item_classification_warehouse_id', $request->delete_icw_id)->delete();
        return response()->json(['success' => 1, 'message' => 'Item Classification Warehouse is successfully deleted!']);
    }
    public function get_selection_box_in_item_class_warehouse(Request $request){
        $item_group = DB::connection('mysql')->table('tabItem Group as item_group')->get();
        $item_class = DB::connection('mysql')->table('tabItem Classification as item_class')->get();
        $warehouse = DB::connection('mysql')->table('tabWarehouse')->where('disabled', 0)->where('is_group', 0)
        ->where('company', 'FUMACO Inc.')->get();
        return response()->json(['item_class' => $item_class,'warehouse' =>$warehouse , 'item_group' =>$item_group]);

    }
    public function get_item_class_based_on_item_group($item_group){
        $output='<option value=""></option>';
            $item_list = DB::connection('mysql')->table('tabItem as item')
            ->where('item.has_variants',"!=", 1)
            ->where('item.item_group', $item_group)
            ->groupBy('item.item_classification')
            ->select('item.item_classification')
            ->get();
            // dd($item_list);
           
                foreach($item_list as $row)
                 {
                $output .= '<option value="'.$row->item_classification.'">'.$row->item_classification.'</option>';
                 }
        
            
            
            // dd($output);
        return $output;
    }
    public function get_powder_coat_chart(){
        $data=[];
        $item_list = DB::connection('mysql_mes')->table('fabrication_inventory as stock')
            ->join('operation as op','op.operation_id', 'stock.operation_id' )
            ->where('op.operation_name', 'Painting')
            ->where('stock.description', 'LIKE', '%'."powder".'%')
            ->select('stock.*','op.operation_name')
            ->orderBy('stock.item_code', 'desc')->get();
        // dd($item_list);

        foreach ($item_list as $row) {
            $min_level= DB::connection('mysql')->table('tabItem Reorder')->where('parent', $row->item_code)->where('material_request_type', 'Transfer')->select('warehouse_reorder_qty','warehouse_reorder_level')->first();
            $actual=$row->balance_qty;
            
            $background_planned='#ff8300  ';
            $background_minimum='#00838F';
            if ((empty($actual)? 0 : $actual) == 0) {
                $status="changecolor";
                $minimum= 0;             
                $background_actual='#558B2F';
                $actual_bar=empty($actual)? 0 : $actual;

            }elseif ((empty($actual)? 0 : $actual) <= (empty($min_level->warehouse_reorder_level)? 0:$min_level->warehouse_reorder_level)) {
                $status="changecolor";
                $minimum= 0;             
                $background_actual='red';
                $actual_bar=empty($actual)? 0 : $actual;

                
            }else{
                $status="nochange";
                $background_actual='#558B2F';
                $actual_bar=(empty($actual)? 0 : $actual)-(empty($min_level->warehouse_reorder_level)? 0:$min_level->warehouse_reorder_level); 
                $minimum= empty($min_level->warehouse_reorder_level)? 0:$min_level->warehouse_reorder_level;
            }
           
            $sheets =0;

               $data[]=[
                'item_code' => $row->item_code,
                'decsription' => $row->description,
                'actual_bar' => round($actual_bar,2),
                'status' => $status, 
                'actual_qty' => round(empty($actual)? 0 : $actual,2),
                'minimum' => round($minimum,2),
                'sheets'=> round($sheets,2),
                'c_actual' => $background_actual,
                'c_planned'=> $background_planned,
                'c_minimum'=>$background_minimum,
                'minimum_label' => round(empty($min_level->warehouse_reorder_level)? 0:$min_level->warehouse_reorder_level,2)
            ];
            
        }
        $chart_data=[
           'chart_data' => $data ];           

        return $chart_data;
    }
    public function tbl_poweder_coat_consumed_list(Request $request){
        
        $data=[];

            $powder_data= DB::connection('mysql_mes')
            ->table('powder_coating')
            ->join('shift', 'shift.shift_id','powder_coating.operating_hrs')
            ->when($request->q, function ($query) use ($request) {
                return $query->where(function($q) use ($request) {
                $q->where('powder_coating.item_code', 'LIKE', '%'.$request->q.'%')
                ->orwhere('powder_coating.date', 'LIKE', '%'.$request->q.'%')
                ->orwhere('shift.shift_type', 'LIKE', '%'.$request->q.'%')
                ->orwhere('powder_coating.operator', 'LIKE', '%'.$request->q.'%');
                });
            })
            ->when($request->daterange, function ($query) use ($request) {
                $str = explode(' - ',$request->daterange);
               
                    $query->whereBetween('powder_coating.date', [$str[0],$str[1]]);
               
                return $query;
            })
            ->when($request->filters, function ($query) use ($request) {
                foreach ($request->filters as $f) {
                    $query->where('powder_coating.item_code', 'LIKE', '%'.$f.'%');
                }

                return $query;
            })
            ->when($request->operator, function ($query) use ($request) {
                foreach ($request->operator as $o) {
                    $query->where('powder_coating.operator', 'LIKE', '%'.$o.'%');
                }

                return $query;
            })->orderBy('powder_coating_id','desc')->get();


             foreach ($powder_data as $row) {


                    $shift=DB::connection('mysql_mes')
                    ->table('shift')
                    ->join('operation as op','op.operation_id','shift.operation_id')
                    ->where('op.operation_name','Painting')
                    ->where('shift.shift_id',  $row->operating_hrs)
                    ->select('shift.*')
                    ->first();
                    $item_details = DB::connection('mysql_mes')->table('fabrication_inventory as stock')
                    ->where('stock.item_code',$row->item_code)
                    ->select('stock.uom')->first();

                    $data[]=[
                    'date' =>  date('F d, Y', strtotime($row->date)),
                    'operating_hrs' => $shift->shift_type.':'.$shift->time_in.'-'.$shift->time_out,
                    'current_qty' => ($row->current_qty == null)? '0':$row->current_qty,
                    'consumed_qty' => ($row->consumed_qty == null)? '0':$row->consumed_qty,
                    'balance_qty' => $row->balance_qty,
                    'item_code' => $row->item_code,
                    'operator_name' => $row->operator,
                    'uom'=>empty($item_details->uom) ? "":$item_details->uom

                    ];

            }
            $count=collect($data)->sum('consumed_qty');
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
     
            // Create a new Laravel collection from the array data
            $itemCollection = collect($data);
         
            // Define how many items we want to be visible in each page
            $perPage = 10;
         
            // Slice the collection to get the items to display in current page
            $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
         
            // Create our paginator and pass it to the view
            $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
         
            // set url path for generted links
            $paginatedItems->setPath($request->url());
            $data = $paginatedItems;
    
            
            // dd($count);
                    
                
        // }  
        return view('inventory.tbl_powder_coat_consumed_list', compact('data', 'count'));
    }
    public function get_powder_coat_item(){
        

        $output="";
          
        $item_list = DB::connection('mysql')->table('tabItem as item')
        ->where('item.has_variants',"!=", 1)
        ->whereIn('item.item_group', ['Raw Material'])
        ->where('item.item_classification', ['PA - Paints'])
        ->where('item.item_name','like', '%powder%')
        ->orderBy('item.modified', 'desc')
        ->select('item.name', 'item.item_name')
        ->get();

            foreach($item_list as $row)
                 {
                $output .= '<option value="'.$row->name.'">'.$row->name.'</option>';
                 }
        

        return $item_list;

            
    }
    public function get_powder_coat_desc($item){

          
        $item_list = DB::connection('mysql')->table('tabItem as item')
        ->where('item.name',$item)
        ->select('item.item_name','item.name')->first();

        $current = DB::connection('mysql_mes')->table('fabrication_inventory')->where('item_code', $item)->select('balance_qty')->first();
        
        if(!empty($current)){
            $data=[];
            $data=[
                'desc'=> $item_list->name.'-'.$item_list->item_name,
                'current' => $current->balance_qty,

            ];
            return response()->json(['success' => 1, 'desc' => $item_list->name.'-'.$item_list->item_name, 'current' => $current->balance_qty]);

        }else{
            return response()->json(['success' => 0, 'message' => 'No Stock for this Item', 'desc' => $item_list->name.'-'.$item_list->item_name, 'current' => ""]);

        }



            
    }
    
    public function tbl_painting_stock(Request $request){
        
        $data = DB::connection('mysql_mes')->table('fabrication_inventory as stock')
            ->join('operation as op','op.operation_id', 'stock.operation_id' )
            ->where('op.operation_name', 'Painting')
            ->when($request->q, function ($query) use ($request) {
                return $query->where(function($q) use ($request) {
                    $q->where('stock.item_code', 'LIKE', '%'.$request->q.'%')
                    ->orwhere('stock.uom', 'LIKE', '%'.$request->q.'%')
                    ->orwhere('stock.description', 'LIKE', '%'.$request->q.'%')
                    ->orwhere('stock.item_classification', 'LIKE', '%'.$request->q.'%');
                });
            })
            ->when($request->filters, function ($query) use ($request) {
                foreach ($request->filters as $f) {
                    $query->where('stock.item_code', 'LIKE', '%'.$f.'%');
                }

                return $query;
            })->select('stock.*')->orderBy('stock.inventory_id', 'desc')->paginate(10);

           
        // dd($data);
        return view('inventory.tbl_stock_painting', compact('data'));
    }
    public function tbl_comsumed_filter_box(Request $request){
        $data=[];
        $powder_data= DB::connection('mysql_mes')
        ->table('powder_coating')
        ->join('shift', 'shift.shift_id','powder_coating.operating_hrs')
        ->whereBetween(DB::raw('DATE_FORMAT(powder_coating.date, "%Y-%m-%d")'),[$request->start,$request->end])
        ->where('powder_coating.item_code', 'LIKE', '%'.$request->item_code.'%')
        ->where('powder_coating.operator', 'LIKE', '%'.$request->operator.'%')
        ->orderBy('powder_coating_id','desc')->get();
        foreach ($powder_data as $row) {


            $shift=DB::connection('mysql_mes')
            ->table('shift')
            ->join('operation as op','op.operation_id','shift.operation_id')
            ->where('op.operation_name','Painting')
            ->where('shift.shift_id',  $row->operating_hrs)
            ->select('shift.*')
            ->first();
            $item_details = DB::connection('mysql_mes')->table('fabrication_inventory as stock')
            ->where('stock.item_code',$row->item_code)
            ->select('stock.uom')->first();

            $data[]=[
            'date' =>  date('F d, Y', strtotime($row->date)),
            'operating_hrs' => $shift->shift_type.':'.$shift->time_in.'-'.$shift->time_out,
            'current_qty' => ($row->current_qty == null)? '0':$row->current_qty,
            'consumed_qty' => ($row->consumed_qty == null)? '0':$row->consumed_qty,
            'balance_qty' => $row->balance_qty,
            'item_code' => $row->item_code,
            'operator_name' => $row->operator,
            'uom'=>empty($item_details->uom) ? "":$item_details->uom

            ];

    }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
     
        // Create a new Laravel collection from the array data
        $itemCollection = collect($data);
     
        // Define how many items we want to be visible in each page
        $perPage = 10;
     
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
     
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
     
        // set url path for generted links
        $paginatedItems->setPath($request->url());
        $data = $paginatedItems;

        $count=  collect($data)->count();
        return view('inventory.tbl_powder_coat_consumed_list', compact('data'));

    }
    public function get_item_code_stock_adjustment_entries_painting(){
        $item_list = DB::connection('mysql')->table('tabItem as item')
        ->where('item.has_variants',"!=", 1)
        ->whereIn('item.item_group', ['Raw Material'])
        ->where('item.item_classification', ['PA - Paints'])
        ->where('item.item_name','like', '%powder%')
        ->orderBy('item.modified', 'desc')
        ->select('item.name', 'item.item_name')
        ->get();

        $output="<option value='default'>Select Item Code</option>";

        foreach($item_list as $row)
             {
            $output .= '<option value="'.$row->name.'">'.$row->name.' - '.$row->item_name.'</option>';
             }

        // dd($output);
    return $output;
    }
    public function get_balanceqty_stock_adjustment_entries_painting($item_code){
        if($item_code == "default"){
            $data=[];
            $data=[
                'balance' => "",
                'description' => "",
                'entry_type' => ""
            ];

        }else{
            $item_list = DB::connection('mysql')->table('tabItem')
            ->where('name', $item_code)
            ->select('name', 'item_name')->first();
        
        $data=[];
        if (DB::connection('mysql_mes')
        ->table('fabrication_inventory')
		->where('item_code', $item_code)
        ->exists()){
            $get_item_code_details= DB::connection('mysql_mes')->table('fabrication_inventory')->where('item_code', $item_code)->first();
            $balanced_qty = $get_item_code_details->balance_qty;
            $data=[
                'balance' => $balanced_qty,
                'description' => $item_list->item_name,
                'entry_type' => "Stock Adjustment"

            ];
            
        }else{
            $balanced_qty = "";
            $data=[
                'balance' => $balanced_qty,
                'description' => $item_list->item_name,
                'entry_type' => "New Entry"
            ];
        }

        }
        
        
        
        return response()->json(['qty' => $data]);            

    }
    public function submit_stock_entries_adjustment_painting(Request $request){
        $operation_id=DB::connection('mysql_mes')->table('operation')->where('operation_name', 'LIKE', '%'."Painting".'%')->select('operation_id')->first();    
        $item_details= $item_list = DB::connection('mysql')->table('tabItem as item')->where('name', $request->item_code )->select('item_classification', 'stock_uom')->first();
        if (DB::connection('mysql_mes')
        ->table('fabrication_inventory')
		->where('item_code', $request->item_code)
        ->exists()){
            $now = Carbon::now();

            // if (DB::connection('mysql_mes')
            //     ->table('item_specification')
            //     ->where('item_code', $request->item_code)
            //     ->exists()){
            //     // DB::connection('mysql_mes')->table('item_specification')->insertGetId($specs);
            // }else{
            //     DB::connection('mysql_mes')->table('item_specification')->insert($this->get_item_specs($request->item_code));
            // }

            $update = [
                // 'inventory_type' =>'Stock Item',
                'balance_qty' => $request->balance_qty,
                'last_modified_at' => $now->toDateTimeString(),
                'last_modified_by' => Auth::user()->employee_name
                ];
                DB::connection('mysql_mes')->table('fabrication_inventory')->where('item_code', $request->item_code)->update($update);
                $users_id = Auth::user()->user_id;
                $get_operation_id= DB::connection('mysql_mes')->table('user')->where('user_access_id', $users_id)->first();

                $trans = [
                    'operation_id' => $operation_id->operation_id,
                    'item_code' => $request->item_code,
                    'adjusted_qty' => $request->balance_qty,
                    'previous_qty' => empty($request->orig_balance_qty)? 0:$request->orig_balance_qty,
                    'remarks' => "",
                    'entry_type' => $request->entry_type_box,
                    'created_by' => Auth::user()->employee_name,
                    'created_at' => $now->toDateTimeString(),
                    'last_modified_by' => Auth::user()->employee_name,
                    'last_modified_at' => $now->toDateTimeString()


                ];

            DB::connection('mysql_mes')->table('inventory_transaction')->insert($trans);
            if (DB::connection('mysql_mes')
                ->table('item_specification')
                ->where('item_code', $request->item_code)
                ->exists()){
                // DB::connection('mysql_mes')->table('item_specification')->insertGetId($specs);
            }else{
                DB::connection('mysql_mes')->table('item_specification')->insert($this->get_item_specs($request->item_code));
            }
            return response()->json(['success' => 1, 'message' => 'Stock Adjustment Successfully Updated!']);

        }else{
            $now = Carbon::now();
            // if (DB::connection('mysql_mes')
            //     ->table('item_specification')
            //     ->where('item_code', $request->item_code)
            //     ->exists()){
            //     // DB::connection('mysql_mes')->table('item_specification')->insertGetId($specs);
            // }else{
            //     DB::connection('mysql_mes')->table('item_specification')->insert($this->get_item_specs($request->item_code));
            // }
                $values1 = [
                    'operation_id' => $operation_id->operation_id,
                    // 'inventory_type' =>'Stock Item',
                    'description' => $request->item_description_input,
                    'item_classification' => $item_details->item_classification,
                    'uom' => $item_details->stock_uom,
                    'item_code' => $request->item_code,
                    'balance_qty' => $request->balance_qty,
                    'created_by' => Auth::user()->employee_name,
                    'created_at' => $now->toDateTimeString(),
                    'last_modified_by' => Auth::user()->employee_name

                ];

            DB::connection('mysql_mes')->table('fabrication_inventory')->insert($values1);
            $users_id = Auth::user()->user_id;
            $get_operation_id= DB::connection('mysql_mes')->table('user')->where('user_access_id', $users_id)->first();

            $trans = [
                'operation_id' => $operation_id->operation_id,
                'item_code' => $request->item_code,
                'adjusted_qty' => $request->balance_qty,
                'previous_qty' => ($request->orig_balance_qty == null) ? 0 : $request->orig_balance_qty,
                'entry_type' => $request->entry_type_box,
                'remarks' => "",
                'created_by' => Auth::user()->employee_name,
                'created_at' => $now->toDateTimeString(),
                'last_modified_by' => Auth::user()->employee_name,
                'last_modified_at' => $now->toDateTimeString()


            ];

            DB::connection('mysql_mes')->table('inventory_transaction')->insert($trans);
            
            return response()->json(['success' => 1, 'message' => 'Stock Entry Successfully Inserted!']);

        }
                
        
    }
    public function get_stock_painting_filters(){
        $item_list = DB::connection('mysql')->table('tabItem as item')
        ->where('item.has_variants',"!=", 1)
        ->whereIn('item.item_group', ['Raw Material'])
        ->where('item.item_classification', ['PA - Paints'])
        ->where('item.item_name','like', '%powder%')
        ->orderBy('item.modified', 'desc')
        ->select('item.name', 'item.item_name')
        ->get();

        foreach($item_list as $row){
            $data[]=[
                'item_code'=>$row->name,
                'desc' => $row->item_name

            ];

        }
        return $data;
    }
    public function get_consume_painting_filters(){
        $data=[];
        $item_list = DB::connection('mysql_mes')->table('powder_coating')->groupBy('operator', 'operator_id')->select('operator','operator_id')->get();

        foreach($item_list as $row){
            $data[]=[
                'operator_id'=>$row->operator_id,
                'operator' => $row->operator

            ];

        }
        return $data;
    }
    public function tbl_filter_stock_inventory_box(Request $request){
        $data = DB::connection('mysql_mes')->table('fabrication_inventory as stock')
        ->join('operation as op','op.operation_id', 'stock.operation_id' )
        ->where('op.operation_name', 'Painting')
        ->where('stock.item_code', 'LIKE', '%'.$request->item_code.'%')
        ->select('stock.*')->orderBy('stock.inventory_id', 'desc')->paginate(10);
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
    public function count_production_notification($operation_id){
        $now = Carbon::now();
        $current_date = $now->toDateString();
        $last_date=$now->subDays(7);
        if($operation_id == "0"){
          $operation_id="1"; 
          $prod= DB::connection('mysql_mes')->table('production_order as prod')
          ->join('job_ticket as jt','jt.production_order', 'prod.production_order')
          ->where('jt.workstation','Painting')
          ->whereNotIn('prod.status',[ "Cancelled", 'Completed'])
          ->where('jt.status', 'Pending')
          ->whereDate('jt.planned_start_date', '<', $current_date)
          ->where('prod.operation_id', $operation_id)
          ->select('prod.production_order')
          ->distinct('prod.production_order')
          ->get();
          $prod_inprogress= DB::connection('mysql_mes')->table('production_order as prod')
          ->join('job_ticket as jt','jt.production_order', 'prod.production_order')
          ->whereNotIn('prod.status',[ "Cancelled", 'Completed'])
          ->where('jt.workstation','Painting')
          ->where('jt.status', 'In Progress')
          ->whereDate('jt.planned_start_date', '<', $last_date)
          ->where('prod.operation_id', $operation_id)
          ->select('prod.production_order')
          ->distinct('prod.production_order')
          ->get();
  
        }else{
          $prod= DB::connection('mysql_mes')->table('production_order as prod')
          ->where('status', 'Not Started')
          ->whereDate('planned_start_date', '<', $current_date)
          ->where('operation_id', $operation_id)
          ->get(); 
          $prod_inprogress= DB::connection('mysql_mes')->table('production_order as prod')
          ->where('status', 'In Progress')
          ->whereDate('planned_start_date', '<', $last_date)
          ->where('operation_id', $operation_id)
          ->get(); 
        }
        $count= count($prod) + count($prod_inprogress);
        return $count;
        
        
      }
      public function get_all_prod_notif(Request $request, $operation_id){
          $now = Carbon::now();
          $data=[];
          $current_date = $now->toDateString();
          if($operation_id == 0){
              $datas= DB::connection('mysql_mes')->table('job_ticket as jt')
              ->join('production_order as prod', 'jt.production_order','prod.production_order')
              ->whereNotIn('prod.status',[ "Cancelled", 'Completed'])
              ->where('jt.workstation','Painting')
              ->where('jt.status', 'Pending')
              ->whereDate('jt.planned_start_date', '<', $current_date)
              ->where('prod.operation_id', 1) 
              ->when($request->customer_notif, function ($query) use ($request) {
                  $query->where('prod.customer', 'LIKE', '%'.$request->customer_notif.'%');
  
                  return $query;
              })
              ->when($request->sales_order_notif, function ($query) use ($request) {
                      $query->where('prod.sales_order', 'LIKE', '%'.$request->sales_order_notif.'%');
  
                  return $query;
              })
              ->orderBy('prod.planned_start_date','DESC')
              ->select('prod.production_order','prod.sales_order','prod.material_request','jt.planned_start_date','prod.customer','prod.stock_uom', 'prod.qty_to_manufacture','jt.status','prod.parent_item_code','prod.description','prod.item_code') 
              ->distinct('prod.production_order')
              ->get();
          }else{
              $datas= DB::connection('mysql_mes')->table('production_order as prod')
              ->where('status', 'Not Started')
              ->whereDate('planned_start_date', '<', $current_date)
              ->where('operation_id', $operation_id)
              ->when($request->customer_notif, function ($query) use ($request) {
                  $query->where('customer', 'LIKE', '%'.$request->customer_notif.'%');
  
                  return $query;
              })
              ->when($request->sales_order_notif, function ($query) use ($request) {
                      $query->where('sales_order', 'LIKE', '%'.$request->sales_order_notif.'%');
  
                  return $query;
              })
              ->orderBy('planned_start_date','DESC')
              ->get();
          }
          
  
          foreach($datas as $row){
            $prod_details = DB::connection('mysql')->table('tabWork Order')
                ->where('name', $row->production_order)->first();
            
            $status = $row->status;
            if($prod_details){
                if($prod_details->docstatus == 2 && $row->status != 'Cancelled'){
                    $status = 'Unknown Status';
                }else if($prod_details->docstatus == 1 && $row->status == 'Cancelled'){
                    $status = 'Unknown Status';
                }else{
                    $status = $row->status;
                }
            }
            
            $data[]=[
                'production_order' => $row->production_order,
                'sales_order' => $row->sales_order,
                'material_request' => $row->material_request,
                'planned_start_date' =>$row->planned_start_date,
                'customer' => $row->customer,
                'stock_uom' => $row->stock_uom,
                'qty_to_manufacture' => $row->qty_to_manufacture,
                // 'material_status' => $this->material_status_stockentry($row->production_order,$row->status),
                'status' => $status,
                'description' => $row->description,
                'parent_item_code' =>$row->parent_item_code,
                'item_code' =>$row->item_code
            ];
  
          }

          return view('tables.tbl_get_prod_notif_under_fab', compact('data'));
      }
      public function reschedule_production_from_notif(Request $request){   
              $val = [];
                  $val = [
                      'planned_start_date' => $request->start_time,
                      'last_modified_by' => Auth::user()->email
                  ];
              if($request->resched_operation_id == "0"){
                  DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->prod_id)->where('workstation','Painting')->update($val);  
                  return response()->json(['success' => 1, 'message' => 'Production Order Successfully Reschedule']); 
              }else{
                  DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->prod_id)->update($val);
                  DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->prod_id)->update($val);  
                  return response()->json(['success' => 1, 'message' => 'Production Order Successfully Reschedule']); 
              }
                    
      }
      public function get_notif_filters($operation_id){
          $now = Carbon::now();
          $current_date = $now->toDateString();
          $last_date=$now->subDays(7);
          if($operation_id == 0){
              $operation_id="1";
              $prod= DB::connection('mysql_mes')->table('production_order as prod')
              ->join('job_ticket as jt', 'jt.production_order','prod.production_order')
              ->where('jt.workstation','Painting')
              ->whereNotIn('prod.status',[ "Cancelled", 'Completed', 'Closed'])
              ->whereDate('jt.planned_start_date', '<', $current_date)
              ->where('prod.operation_id', $operation_id)
              ->where('jt.status', "Pending")
              ->select('prod.sales_order', 'customer', 'prod.production_order')
              ->orderBy('prod.planned_start_date','DESC')
              ->distinct('prod.production_order')
              ->get();
              $prod_inprogress= DB::connection('mysql_mes')->table('production_order as prod')
              ->join('job_ticket as jt', 'jt.production_order','prod.production_order')
              ->whereNotIn('prod.status',[ "Cancelled", 'Completed', 'Closed'])
              ->where('jt.workstation','Painting')
              ->where('jt.status', 'In Progress')
              ->whereDate('jt.planned_start_date', '<', $last_date)
              ->where('prod.operation_id', $operation_id)
              ->select('prod.sales_order', 'customer','prod.production_order')
              ->orderBy('prod.planned_start_date','DESC')
              ->distinct('prod.production_order')
              ->get();
          }else{
              $prod= DB::connection('mysql_mes')->table('production_order as prod')
              ->where('status', 'Not Started')
              ->whereDate('planned_start_date', '<', $current_date)
              ->where('operation_id', $operation_id)
              ->select('sales_order', 'customer')
              ->orderBy('planned_start_date','DESC')
              ->get();
              $prod_inprogress= DB::connection('mysql_mes')->table('production_order as prod')
              ->where('status', 'In Progress')
              ->whereDate('planned_start_date', '<', $last_date)
              ->where('operation_id', $operation_id)
              ->select('sales_order', 'customer')
              ->orderBy('planned_start_date','DESC')
              ->get(); 
          }
          
  
          return [
              'so' => collect($prod)->unique('sales_order')->pluck('sales_order'),
              'customer' => collect($prod)->unique('customer')->pluck('customer'),
              'so_inprog' =>collect($prod_inprogress)->unique('sales_order')->pluck('sales_order'),
              'customer_inprog' =>collect($prod_inprogress)->unique('customer')->pluck('customer')
          ];
      }
      public function cancel_production_from_notif(Request $request){   
        try {
            $now = Carbon::now();
            // check for task in progress
            $task_in_progress = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->production_order)
                ->where('status', 'In Progress')->count();
            if ($task_in_progress > 0) {
                return response()->json(['success' => 0, 'message' => 'Please stop all task for Production Order ' . $request->production_order]);
            }

            DB::connection('mysql')->table('tabWork Order')->where('name', $request->production_order)
                ->where('docstatus', 1)->where('status', '!=', 'Completed')
                ->update(['docstatus' => 2, 'status' => 'Cancelled', 'modified' => $now->toDateTimeString(), 'modified_by' => Auth::user()->email]);

            DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)
                ->where('status', '!=', 'Completed')->update(['status' => 'Cancelled', 'last_modified_at' => $now->toDateTimeString(), 'last_modified_by' => Auth::user()->email]);

            $production_order_details = DB::connection('mysql_mes')->table('production_order')
                ->where('production_order', $request->production_order)->first();

                return response()->json(['success' => 1, 'message' => 'Production Order Successfully Cancelled', 'tabselect' => $request->tabselected]);  
            } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }        
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
      public function show_edit_shift_break_time($shift){
          $shift_break= DB::connection('mysql_mes')->table('breaktime')
          ->where('shift_id',$shift)
          ->get();
          // dd($shift_break);
  
          if(count($shift_break) == 0){
              return response()->json(['success' => 0, 'shift_break' => $shift_break]); 
          }else{
              return response()->json(['success' => 1, 'shift_break' => $shift_break]); 
          }
  
      }
      public function get_all_prod_notif_inprogress(Request $request, $operation_id){
          $data=[];
          $now = Carbon::now();
          $current_date = $now->toDateString();
          $last_date=$now->subDays(7);
          // dd($last_date);
          if($operation_id == 0){
              $operation_id="1";
              $datas= DB::connection('mysql_mes')->table('production_order as prod')
              ->join('job_ticket as jt', 'jt.production_order', 'prod.production_order')
              ->where('jt.workstation','Painting')
              ->whereNotIn('prod.status',[ "Cancelled", 'Completed'])
              ->where('jt.status', 'In Progress')
              ->whereDate('prod.planned_start_date', '<', $last_date)
              ->where('prod.operation_id', $operation_id)
              ->when($request->customer_notif, function ($query) use ($request) {
                  $query->where('prod.customer', 'LIKE', '%'.$request->customer_notif.'%');
  
                  return $query;
              })
              ->when($request->sales_order_notif, function ($query) use ($request) {
                      $query->where('prod.sales_order', 'LIKE', '%'.$request->sales_order_notif.'%');
  
                  return $query;
              })
              ->select('prod.production_order','prod.sales_order','prod.material_request','jt.planned_start_date','prod.customer','prod.stock_uom', 'prod.qty_to_manufacture','jt.status','prod.parent_item_code','prod.description','prod.item_code') 
              ->distinct('prod.production_order')
              ->orderBy('jt.planned_start_date','DESC')
              ->get();
          }else{
              $datas= DB::connection('mysql_mes')->table('production_order as prod')
              ->where('status', 'In Progress')
              ->whereDate('planned_start_date', '<', $last_date)
              ->where('operation_id', $operation_id)
              ->when($request->customer_notif, function ($query) use ($request) {
                  $query->where('customer', 'LIKE', '%'.$request->customer_notif.'%');
      
                  return $query;
              })
              ->when($request->sales_order_notif, function ($query) use ($request) {
                      $query->where('sales_order', 'LIKE', '%'.$request->sales_order_notif.'%');
      
                  return $query;
              })
              ->orderBy('planned_start_date','DESC')
              ->get();   
          }
          
  
          foreach($datas as $row){
            $prod_details = DB::connection('mysql')->table('tabWork Order')
                ->where('name', $row->production_order)->first();
            
            $status = $row->status;
            if($prod_details){
                if($prod_details->docstatus == 2 && $row->status != 'Cancelled'){
                    $status = 'Unknown Status';
                }else if($prod_details->docstatus == 1 && $row->status == 'Cancelled'){
                    $status = 'Unknown Status';
                }else{
                    $status = $row->status;
                }
            }

              $data[]=[
                  'production_order' => $row->production_order,
                  'sales_order' => $row->sales_order,
                  'material_request' => $row->material_request,
                  'planned_start_date' =>$row->planned_start_date,
                  'customer' => $row->customer,
                  'stock_uom' => $row->stock_uom,
                  'qty_to_manufacture' => $row->qty_to_manufacture,
                  // 'material_status' => $this->material_status_stockentry($row->production_order,$row->status),
                  'status' => $status,
                  'description' => $row->description,
                  'parent_item_code' =>$row->parent_item_code,
                  'item_code' =>$row->item_code
              ];
  
          }
          // dd($datas);
          return view('tables.tbl_get_prod_notif_under_fab_inprogress', compact('data'));
      }
      public function complete_production_from_notif(Request $request){
           
          if($request->complete_operation_id == "0"){
              $orders=DB::connection('mysql_mes')->table('job_ticket as jt')
              ->leftJoin('time_logs as tl', 'jt.job_ticket_id','tl.job_ticket_id')
              ->join('production_order as prod','jt.production_order','=','prod.production_order')
              ->join('process as p','p.process_id','jt.process_id')
              ->join('workstation as work','work.workstation_name','jt.workstation')
              ->where('tl.status', 'In Progress')
              ->whereNotIn('prod.status', ['Cancelled'])
              ->where('jt.production_order', $request->prod_id)
              ->select('prod.qty_to_manufacture','jt.production_order','jt.workstation as workstation_plot','tl.machine_code as machine','tl.job_ticket_id as jtname', 'p.process_name', "jt.status as stat", 'jt.item_feedback as item_feed', 'tl.operator_name', 'tl.from_time', 'tl.to_time', 'tl.machine_code', 'work.workstation_id', 'tl.time_log_id', 'jt.job_ticket_id')
              ->get();
              if(count($orders) == 0){
                  // dd('hi');
                  $prod_details= DB::connection('mysql_mes')->table('production_order')
                  ->where('production_order', $request->prod_id)
                  ->select('qty_to_manufacture')
                  ->first();
                  $val=[
                      "status" => "Completed",
                      'remarks' => 'Mark as Completed',
                      'completed_qty' =>$prod_details->qty_to_manufacture,
                      'last_modified_by' => Auth::user()->email
                  ];
                  
  
                  DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->prod_id)->where('workstation','Painting')->update($val);
                  return response()->json(['success' => 1, 'message' => 'Production Order Successfully Completed']);        
  
              }else{
                  // dd('hello');
                  return response()->json(['success' => 0, 'message' => 'Unable to process request. Production Order is currently in process.']);        
  
              }
          }else{
              $spotlogs=DB::connection('mysql_mes')->table('job_ticket as jt')
              ->leftJoin('spotwelding_qty as spotpart', 'spotpart.job_ticket_id','jt.job_ticket_id')
              ->join('production_order as prod','jt.production_order','=','prod.production_order')
              ->join('process as p','p.process_id','jt.process_id')
              ->join('workstation as work','work.workstation_name','jt.workstation')
              ->where('spotpart.status', 'In Progress')
              ->where('jt.production_order', $request->prod_id)
              ->select('prod.qty_to_manufacture','jt.production_order','jt.workstation as workstation_plot','spotpart.machine_code as machine','spotpart.job_ticket_id as jtname', 'p.process_name', "jt.status as stat", 'jt.item_feedback as item_feed', 'spotpart.operator_name', 'spotpart.from_time', 'spotpart.to_time', 'spotpart.machine_code', 'work.workstation_id', 'spotpart.time_log_id', 'jt.job_ticket_id');
  
              $orders=DB::connection('mysql_mes')->table('job_ticket as jt')
              ->leftJoin('time_logs as tl', 'jt.job_ticket_id','tl.job_ticket_id')
              ->join('production_order as prod','jt.production_order','=','prod.production_order')
              ->join('process as p','p.process_id','jt.process_id')
              ->join('workstation as work','work.workstation_name','jt.workstation')
              ->where('tl.status', 'In Progress')
              ->whereNotIn('prod.status', ['Cancelled'])
              ->where('jt.production_order', $request->prod_id)
              ->select('prod.qty_to_manufacture','jt.production_order','jt.workstation as workstation_plot','tl.machine_code as machine','tl.job_ticket_id as jtname', 'p.process_name', "jt.status as stat", 'jt.item_feedback as item_feed', 'tl.operator_name', 'tl.from_time', 'tl.to_time', 'tl.machine_code', 'work.workstation_id', 'tl.time_log_id', 'jt.job_ticket_id')
              ->union($spotlogs)
              ->get();
              // dd($orders);
              if(count($orders) == 0){
                  // dd('hi');
                  $prod_details= DB::connection('mysql_mes')->table('production_order')
                  ->where('production_order', $request->prod_id)
                  ->select('qty_to_manufacture')
                  ->first();
                  $val=[
                      "status" => "Completed",
                      'remarks' => 'Mark as Completed',
                      'completed_qty' =>$prod_details->qty_to_manufacture,
                      'last_modified_by' => Auth::user()->email
                  ];
                  $val1=[
                      "status" => "Completed",
                      'produced_qty' =>$prod_details->qty_to_manufacture,
                      'last_modified_by' => Auth::user()->email
                  ];
  
                  DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->prod_id)->update($val);
                  DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->prod_id)->update($val1);
                  return response()->json(['success' => 1, 'message' => 'Production Order Successfully Completed']);        
  
              }else{
                  // dd('hello');
                  return response()->json(['success' => 0, 'message' => 'Unable to process request. Production Order is currently in process.']);        
  
              }
          }
              
  
      }
      public function unschedule_production_from_notif(Request $request){   
          $val = [];
          if($request->unsched_operation_id =="0"){
              $val1 = [
                  'planned_start_date' => NULL,
                  'last_modified_by' => Auth::user()->email
              ];
  
              DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->prod_id)->where('workstation', 'Painting')->update($val1);  
              return response()->json(['success' => 1, 'message' => 'Production Order Successfully Unschedule']); 
          }else{
              $val = [
                  'planned_start_date' => NULL,
                  'is_scheduled' => 0,
                  'last_modified_by' => Auth::user()->email
              ];
              $val1 = [
                  'planned_start_date' => NULL,
                  'last_modified_by' => Auth::user()->email
              ];
  
              DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->prod_id)->update($val);
              DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->prod_id)->update($val1);  
              return response()->json(['success' => 1, 'message' => 'Production Order Successfully Unschedule']);        
          }
              
      }

    public function get_employee_email(){
        $employees = DB::connection('mysql_essex')->table('users')->where('user_type', 'Employee')
            ->where('status', 'Active')->where('email','!=',null)->select('email')->get();
        return response()->json(['email' => $employees]);
    }
    public function save_add_email_trans(Request $request){
        // dd($request->all());
        $now = Carbon::now();

        $arr = $request->emailtrans;
                $ar=array_unique( array_diff_assoc( $arr, array_unique( $arr ) ) );
                if(!empty($ar)){
                    foreach($ar as $i => $r){
                        $row= $i +1;
                        return response()->json(['success' => 0, 'message' => 'Please check DUPLICATE '.$r.' at ROW '.$row ]);

                    }
                    
                }else{
                    foreach($request->emailtrans as $i => $row){
                        if (DB::connection('mysql_mes')
                            ->table('email_trans_recipient')
                            ->where('email_trans', $request->etranstype[$i])
                            ->where('email', $row)
                            ->exists()){

                            return response()->json(['success' => 0, 'message' => 'Email '.$row.' is already exist in '.$request->etrans_type[$i] ]);
                        }else{
                            $data[] = [
                                'email_trans' => $request->etranstype[$i],
                                'email' => $row,
                                'last_modified_by' => Auth::user()->email,
                                'created_by' => Auth::user()->email,
                                'created_at' => $now->toDateTimeString()
                            ];
                        }
                   }
                   
                   
                   DB::connection('mysql_mes')->table('email_trans_recipient')->insert($data);
                   return response()->json(['success' => 1, 'message' => 'New email transaction recipient successfully added']);
                }
    }
    public function get_tbl_email_trans_list(Request $request){
            $data = DB::connection('mysql_mes')->table('email_trans_recipient')
                ->where(function($q) use ($request) {
                    $q->where('email', 'LIKE', '%'.$request->search_string.'%')
                    ->orwhere('email_trans', 'LIKE', '%'.$request->search_string.'%');
                })
                ->orderBy('email_trans_recipient_id', 'desc')->paginate(15);
            
            return view('tables.tbl_email_trans', compact('data'));


    }
    public function delete_email_recipient(Request $request){
        DB::connection('mysql_mes')->table('email_trans_recipient')->where('email_trans_recipient_id', $request->email_id)->delete();

        return response()->json(['success' => 1,'message' => 'Email Transaction Recipient successfully deleted.']);
    }

    // /get_feedback_logs/{prod}
    public function get_feedbacked_log($prod){
        $feedbacked_log= DB::connection('mysql_mes')->table('feedbacked_logs')->where('production_order', $prod)->get();

		return view('tables.tbl_feedbacked_log', compact('feedbacked_log'));
    }
    public function save_operator_checklist(Request $request){
        $now = Carbon::now();
        $arr = $request->operator_new_checklist_r_desc;
        $ar=array_unique( array_diff_assoc($arr, array_unique($arr)));
        
        if ($request->operator_new_checklist_r_desc) {   
            foreach($request->operator_new_checklist_r_desc as $i => $row){
                if (DB::connection('mysql_mes')
                    ->table('operator_reject_list_setup')
                    ->where('workstation_id', $request->workstation_id)
                    ->where('process_id', $request->operator_new_checklist_r_process[$i])
                    ->where('reject_list_id', $row)
                    ->exists()){
                    $reject_desc =DB::connection('mysql_mes')->table('reject_list')
                        ->where('reject_list_id', $row)
                        ->first();
                    $workstation= DB::connection('mysql_mes')->table('workstation')
                        ->where('workstation_id', $request->workstation_id)
                        ->first();

                    return response()->json(['success' => 0, 'message' => 'Operator reject list setup '.$reject_desc->reject_reason.' is already exist in '.$workstation->workstation_name ]);
                }else{
                    $checklist[] = [
                        'workstation_id' => $request->workstation_id,
                        'reject_list_id' => $row,
                        'process_id' => $request->operator_new_checklist_r_process[$i],
                        'last_modified_by' => Auth::user()->email,
                        'created_by' => Auth::user()->email,
                        'created_at' => $now->toDateTimeString()
                    ];
                } 
            }
            DB::connection('mysql_mes')->table('operator_reject_list_setup')->insert($checklist);  
        }

        return response()->json(['success' => 1,'message' => 'New operator reject list setup has been created.', 'reloadtbl' => $request->reload_operator_checklist,]);
    }
    public function get_tbl_opchecklist_list_fabrication(Request $request){
        $check_list = DB::connection('mysql_mes')->table('operator_reject_list_setup as oc')
            ->join('workstation as w','w.workstation_id', 'oc.workstation_id')
            ->leftJoin('process', 'process.process_id', 'oc.process_id')
            ->join('reject_list as rl','rl.reject_list_id', 'oc.reject_list_id')
            ->join('reject_category as rc','rl.reject_category_id', 'rc.reject_category_id')
            ->join('operation as op', 'op.operation_id', 'w.operation_id')
            ->where('w.operation_id', 1)
            ->where(function($q) use ($request) {
                $q->where('rl.reject_checklist', 'LIKE', '%'.$request->search_string.'%')
                    ->orwhere('w.workstation_name', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rl.reject_reason', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rl.recommended_action', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rc.reject_category_name', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('process.process_name', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rl.responsible', 'LIKE', '%'.$request->search_string.'%');
            })
            ->where('w.workstation_name','!=','Painting')
            ->select('w.workstation_name', 'oc.*','rc.reject_category_name','rl.reject_reason', 'rl.reject_checklist','op.operation_name', 'process.process_name')
            ->orderBy('operator_reject_list_setup_id', 'desc')->paginate(15);

        return view('tables.tbl_operator_check_list_fabrication', compact('check_list'));

    }
    public function get_tbl_opchecklist_list_assembly(Request $request){
        $check_list = DB::connection('mysql_mes')->table('reject_list as rl')
            ->join('reject_category as rc','rl.reject_category_id', 'rc.reject_category_id')
            ->leftJoin('reject_material_type as rmt','rmt.reject_material_type_id', 'rl.reject_material_type_id')
            ->where('rl.operation_id', 3)
            ->where(function($q) use ($request) {
                $q->where('rl.reject_checklist', 'LIKE', '%'.$request->search_string.'%')
                ->where('rl.reject_checklist', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rl.reject_reason', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rl.recommended_action', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rc.reject_category_name', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rl.responsible', 'LIKE', '%'.$request->search_string.'%');
            })
            ->select('rl.*','rc.reject_category_name', 'rmt.material_type')
            ->orderBy('reject_list_id', 'desc')->paginate(15);

        return view('tables.tbl_operator_check_list_assembly', compact('check_list'));
    }

    public function get_tbl_opchecklist_list_painting(Request $request){
        $check_list = DB::connection('mysql_mes')->table('operator_reject_list_setup as oc')
            ->join('workstation as w','w.workstation_id', 'oc.workstation_id')
            ->leftJoin('process', 'process.process_id', 'oc.process_id')
            ->join('reject_list as rl','rl.reject_list_id', 'oc.reject_list_id')
            ->join('reject_category as rc','rl.reject_category_id', 'rc.reject_category_id')
            ->join('operation as op', 'op.operation_id', 'w.operation_id')
            ->where('w.workstation_name','=','Painting')
            ->where(function($q) use ($request) {
                $q->where('rl.reject_checklist', 'LIKE', '%'.$request->search_string.'%')
                    ->orwhere('w.workstation_name', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rl.reject_reason', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rl.recommended_action', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rc.reject_category_name', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('process.process_name', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('rl.responsible', 'LIKE', '%'.$request->search_string.'%');
            })
            ->select('w.workstation_name', 'oc.*','rc.reject_category_name','rl.reject_reason', 'rl.reject_checklist','w.workstation_name as operation_name', 'process.process_name')
            ->orderBy('operator_reject_list_setup_id', 'desc')->paginate(15);

        return view('tables.tbl_operator_check_list_painting', compact('check_list'));
    }

    public function delete_operator_checklist(Request $request){
        DB::connection('mysql_mes')->table('operator_reject_list_setup')->where('operator_reject_list_setup_id', $request->check_list_id)->delete();
        return response()->json(['success' => 1, 'message' => 'Operator reject list setup successfully deleted!', 'reloadtbl' => $request->delete_op_reloadtbl]);

    }
    public function save_late_delivery_reason(Request $request){
        //save late delivery reason from form to database
        $now = Carbon::now();
        $data = $request->all();
        $reason= $data['late_delivery'];
        $list=[];
                foreach($reason as $i => $row){
                    if (DB::connection('mysql_mes')
                        ->table('delivery_reschedule_reason')
                        ->where('reschedule_reason', $row)
                        ->exists()){
                        return response()->json(['success' => 0, 'message' => 'Late Delivery Reason - <b>'.$row.'</b> is already exist']);// validate if already exist in database
                    }else{
                        $list[] = [
                            'reschedule_reason' => $row
                            ];
                    } 
            }
            DB::connection('mysql_mes')->table('delivery_reschedule_reason')->insert($list);
            return response()->json(['message' => 'New Late Delivery Reason is successfully inserted.']);
    }
    public function get_tbl_late_delivery(Request $request){
        //show late delivery reason to table in setting module
        $list = DB::connection('mysql_mes')->table('delivery_reschedule_reason')
            ->where(function($q) use ($request) {
                $q->where('reschedule_reason', 'LIKE', '%'.$request->search_string.'%');
            })
            ->orderBy('reschedule_reason_id', 'desc')->paginate(15);
            
        return view('tables.tbl_late_delivery_reason', compact('list'));

    }
    public function reschedule_prod_details($prod){
        //get production order details and join in delivery table to get the latest delivery date
        $prod_details= DB::connection('mysql_mes')->table('production_order')
        ->leftJoin('delivery_date', function($join){
            $join->on( DB::raw('IFNULL(production_order.sales_order, production_order.material_request)'), '=', 'delivery_date.reference_no');
            $join->on('production_order.parent_item_code','=','delivery_date.parent_item_code');
        })
        ->where('production_order',$prod )
        ->select('production_order.*', 'delivery_date.rescheduled_delivery_date', 'delivery_date.delivery_date as deli')
        ->first();
        
        $reference_no=($prod_details->sales_order)? $prod_details->sales_order : $prod_details->material_request;
        //get_reschedule_log and be used in submission in erp
        $delivery_id=DB::connection('mysql_mes')->table('delivery_date_reschedule_logs')
        ->join('delivery_date', 'delivery_date_reschedule_logs.delivery_date_id', 'delivery_date.delivery_date_id')
        ->join('delivery_reschedule_reason', 'delivery_reschedule_reason.reschedule_reason_id', 'delivery_date_reschedule_logs.reschedule_reason_id')
        ->where(function($q) use ($reference_no) {
            $q->where('delivery_date.reference_no', 'LIKE', '%'.$reference_no.'%');
        })
        ->where('delivery_date.parent_item_code', $prod_details->parent_item_code)
        ->select('delivery_date_reschedule_logs.*', 'delivery_reschedule_reason.reschedule_reason')
        ->orderBy('delivery_date_reschedule_logs.reschedule_log_id', 'desc')
        ->get();

        $data=[];
        foreach($delivery_id as $row){
            $previous_row=DB::connection('mysql_mes')->table('delivery_date_reschedule_logs')->where('delivery_date_id', $row->delivery_date_id)->where('reschedule_log_id', '>', $row->reschedule_log_id)->orderby('reschedule_log_id', 'asc')->first();
            $data[]=[
                'delivery_date'=> (empty($previous_row))? Carbon::parse($prod_details->rescheduled_delivery_date)->format('M-d-Y'): Carbon::parse($previous_row->previous_delivery_date)->format('M-d-Y'),                
                'delivery_reason' => $row->reschedule_reason,
                'remarks' => $row->remarks
            ];
        }
        $reason= DB::connection('mysql_mes')->table('delivery_reschedule_reason')->get();
        return view('reports.tbl_reschedule_delivery_date', compact('prod_details','reason', 'data'));

    }
    public function delete_late_delivery_reason(Request $request){
        DB::connection('mysql_mes')->beginTransaction();
        try {
            $checker = DB::connection('mysql_mes')->table('delivery_reschedule_reason')->where('reschedule_reason', $request->late_deli_reason)->exists();
            if ($checker){
                DB::connection('mysql_mes')->table('delivery_reschedule_reason')->where('reschedule_reason', $request->late_deli_reason)->delete();
            }else{
                return response()->json(['success' => 0, 'message' => 'Reschedule delivery reason does already removed.']);
            }

            DB::connection('mysql_mes')->commit();
            return response()->json(['success' => 1, 'message' => 'Reschedule delivery reason successfully removed.']);
        } catch (\Throwable $th) {
            //throw $th;
            DB::connection('mysql_mes')->rollback();
            return response()->json(['success' => 0, 'message' => 'An error occured. Please try again later.']);
        }
    }
    public function update_late_delivery(Request $request){
        if (DB::connection('mysql_mes')->table('delivery_reschedule_reason')
            ->where('reschedule_reason', $request->edit_late_deli_reason)
            ->exists()){

                if(strtoupper($request->edit_late_deli_reason) == strtoupper($request->orig_late_deli_reason)){
                    
                    $list = [
                    'reschedule_reason' => $request->edit_late_deli_reason,
                    'last_modified_by' => Auth::user()->email,
                    ];
                    DB::connection('mysql_mes')->table('delivery_reschedule_reason')->where('reschedule_reason_id', $request->transid)->update($list);
                    return response()->json(['message' => 'Reschedule Delivery Reason is successfully updated.']);
                }else{
                    return response()->json(['success' => 0, 'message' => 'Reschedule Delivery Reason - <b>'.$request->edit_reject_category.'</b> already exists']);           

                }
        }else{
            $list = [
                'reschedule_reason' => $request->edit_late_deli_reason,
                'last_modified_by' => Auth::user()->email,
                ];
                DB::connection('mysql_mes')->table('delivery_reschedule_reason')->where('reschedule_reason_id', $request->transid)->update($list);
                return response()->json(['message' => 'Reschedule Delivery Reason is successfully updated.']);

        }
    }
    public function get_scheduled_for_painting($date){
        $sched_date= Carbon::parse($date);
		$start = $sched_date->startOfDay()->toDateTimeString();
		$end = $sched_date->endOfDay()->toDateTimeString();

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
        $sched_format= Carbon::parse($date)->format('F d, Y');

		return view('painting.print_production_schedule', compact('scheduled_arr','sched_format'));
	}
    public function get_production_details_for_edit($prod){

        $production_order = DB::connection('mysql_mes')->table('production_order')
            ->where('production_order', $prod)->select('production_order.*')->first();
        $order = [];
            $process_loading = DB::connection('mysql_mes')->table('job_ticket')
                ->join('process', 'job_ticket.process_id', 'process.process_id')
                ->where('job_ticket.production_order', $prod)
                ->where('job_ticket.workstation', 'Painting')
                ->where('process.process_name','Loading')
                ->select('job_ticket.status', 'job_ticket.completed_qty', 'job_ticket.sequence','process.process_name')
                ->first();
            $process_unloading = DB::connection('mysql_mes')->table('job_ticket')
                ->join('process', 'job_ticket.process_id', 'process.process_id')
                ->where('job_ticket.production_order', $prod)
                ->where('job_ticket.workstation', 'Painting')
                ->where('process.process_name','Unloading')
                ->select('job_ticket.status', 'job_ticket.completed_qty', 'job_ticket.sequence','process.process_name')
                ->first();
            $order = [
                'completed' => $production_order->produced_qty,
                'status' => $production_order->status,
                'loading_cpt' => $process_loading->completed_qty,
                'loading_status' => $process_loading->status,
                'unloading_cpt' =>  $process_unloading->completed_qty,
                'unloading_status' => $process_unloading->status
            ];
        return $order;
    }
    public function get_reload_tbl_change_code(){
        $notifs = [];
        $now = Carbon::now();
        $start_of_the_week= Carbon::now()->startOfWeek();
        $end_of_the_week= Carbon::now()->endOfWeek();
        $get_prod_sched_today=DB::connection('mysql_mes')
            ->table('production_order')
            ->where(function($q) use ($start_of_the_week,$end_of_the_week){
                $q->whereBetween('planned_start_date', [$start_of_the_week, $end_of_the_week])
                    ->orWhereNull('planned_start_date');
            })
            ->groupBy('parent_item_code', 'sales_order', 'material_request')
            ->select('parent_item_code', 'sales_order', 'material_request')->get();
        $notifications=[];
		foreach($get_prod_sched_today as $row){
			$reference= ($row->sales_order == null)? $row->material_request: $row->sales_order;
			$tbl_reference= ($row->sales_order == null)? "tabMaterial Request Item": "tabSales Order Item";
			$get_delivery_date=DB::connection('mysql_mes')->table('delivery_date')->where('reference_no', $reference)->where('parent_item_code',  $row->parent_item_code)->first();
			if(!empty($get_delivery_date)){
                $erp_sales_order=DB::connection('mysql')->table($tbl_reference)->where('name', $get_delivery_date->erp_reference_id)->select('item_code')->first();
                if(!empty($erp_sales_order)){
                    if($erp_sales_order->item_code != $row->parent_item_code){
					$notifications[] = [	
						'type' => $reference,
						'message' => 'Parent item code was change from <b>'.$row->parent_item_code.'</b> to <b>'.$erp_sales_order->item_code.'</b>',
						'created' => $now->toDateTimeString(),
						'timelog_id' =>	"",
						'table' => 'ERP'
					];
                    }
                }
            }
        }
        if($notifications){
            return view('tables.tbl_production_change_code', compact('notifications'));
        }else{
            return $notifications;
        }
    }
    public function tbl_op_fabrication_list(){

        return view('tables.tbl_operator_fabrication', compact('prod_details','reason', 'data')); 
    }
    public function get_material_type(){
        $get_reject_material_type=DB::connection('mysql_mes')->table('reject_material_type')->get();
        return response()->json(['material_type' => $get_reject_material_type]);

    }
    public function save_material_type(Request $request){
        //save late delivery reason from form to database
        $now = Carbon::now();
        $data = $request->all();
        $reason= $data['material_type'];
                foreach($reason as $i => $row){
                    if (DB::connection('mysql_mes')
                        ->table('reject_material_type')
                        ->where('material_type', $row)
                        ->exists()){
                        return response()->json(['success' => 0, 'message' => 'Material Type - <b>'.$row.'</b> is already exist']);// validate if already exist in database
                    }else{
                        $list[] = [
                            'material_type' => $row,
                            'last_modified_by' => Auth::user()->email,
                            'created_by' => Auth::user()->email,
                            'created_at' => $now->toDateTimeString()
                            ];
                    } 
            }
            DB::connection('mysql_mes')->table('reject_material_type')->insert($list);
            return response()->json(['message' => 'New Material Type is successfully inserted.']);
    }
    public function get_material_type_tbl(Request $request){
        //show late delivery reason to table in setting module
        $list = DB::connection('mysql_mes')->table('reject_material_type')
            ->where(function($q) use ($request) {
                $q->where('material_type', 'LIKE', '%'.$request->search_string.'%');
            })
            ->orderBy('reject_material_type_id', 'desc')->paginate(15);
            
        return view('tables.tbl_material_type', compact('list'));
    }

    public function update_material_type(Request $request){
        if (DB::connection('mysql_mes')->table('reject_material_type')
            ->where('material_type', $request->edit_material_type)
            ->exists()){

                if(strtoupper($request->edit_material_type) == strtoupper($request->orig_material_type)){
                    
                    $list = [
                    'material_type' => $request->edit_material_type,
                    'last_modified_by' => Auth::user()->email,
                    ];
                    DB::connection('mysql_mes')->table('reject_material_type')->where('reject_material_type_id', $request->mtypeid)->update($list);
                    return response()->json(['message' => 'Material Type is successfully updated.']);
                }else{
                    return response()->json(['success' => 0, 'message' => 'Material Type - <b>'.$request->edit_material_type.'</b> is already exist']);           

                }
        }else{
                $list = [
                'material_type' => $request->edit_material_type,
                'last_modified_by' => Auth::user()->email,
                ];
                DB::connection('mysql_mes')->table('reject_material_type')->where('reject_material_type_id', $request->mtypeid)->update($list);
                return response()->json(['message' => 'Material Type is successfully updated.']);

        }

    }
  
    public function save_reason_for_cancellation(Request $request){
        //save late reason of cancellation to database
        $now = Carbon::now();
        $data = $request->all();
        $reason= $data['reasonofcancel'];
        $duplicate_row=array_unique(array_diff_assoc($reason,array_unique($reason)));
        if(!empty($duplicate_row)){
            foreach($duplicate_row as $i => $r){
                $row= $i +1;
                return response()->json(['success' => 0, 'message' => 'Please check DUPLICATE '.$r.' at ROW '.$row ]);
            }
        }else{
            foreach($reason as $i => $row){
                if (DB::connection('mysql_mes')
                    ->table('reason_for_cancellation_po')
                    ->where('reason_for_cancellation', $row)
                    ->exists()){
                        return response()->json(['success' => 0, 'message' => 'Reason for Cancellation - <b>'.$row.'</b> is already exist']);// validate if already exist in database
                }else{
                    $list[] = [
                        'reason_for_cancellation' => $row,
                        'last_modified_by' => Auth::user()->email,
                        'created_by' => Auth::user()->email,
                        'created_at' => $now->toDateTimeString()
                    ];
                } 
            }
            DB::connection('mysql_mes')->table('reason_for_cancellation_po')->insert($list);
        return response()->json(['message' => 'New reason for cancellation is successfully inserted.']);
        }
    }
    public function tbl_reason_for_cancellation_po(Request $request){
        //show late reason of cancellation to table in setting module
        $list = DB::connection('mysql_mes')->table('reason_for_cancellation_po')
            ->where(function($q) use ($request) {
                $q->where('reason_for_cancellation', 'LIKE', '%'.$request->search_string.'%');
            })
            ->orderBy('reason_for_cancellation_id', 'desc')->paginate(15);
            
        return view('tables.tbl_reason_for_cancellation', compact('list'));
    }
    public function update_reason_for_cancellation(Request $request){
        if (DB::connection('mysql_mes')->table('reason_for_cancellation_po')
            ->where('reason_for_cancellation', $request->edit_reason_for_cancellation)
            ->exists()){

                if(strtoupper($request->edit_reason_for_cancellation) == strtoupper($request->orig_reason_for_cancellation)){
                    $list = [
                    'reason_for_cancellation' => $request->edit_reason_for_cancellation,
                    'last_modified_by' => Auth::user()->email,
                    ];
                    DB::connection('mysql_mes')->table('reason_for_cancellation_po')->where('reason_for_cancellation_id', $request->edit_reason_for_cancellation_id)->update($list);
                    return response()->json(['message' => 'Reason for Cancellation is successfully updated.']);
                }else{
                    return response()->json(['success' => 0, 'message' => 'Reason for Cancellation - <b>'.$request->edit_reason_for_cancellation.'</b> is already exist']);           

                }
        }else{
                $list = [
                'reason_for_cancellation' => $request->edit_reason_for_cancellation,
                'last_modified_by' => Auth::user()->email,
                ];
                DB::connection('mysql_mes')->table('reason_for_cancellation_po')->where('reason_for_cancellation_id', $request->edit_reason_for_cancellation_id)->update($list);
                return response()->json(['message' => 'Reason for Cancellation is successfully updated.']);

        }

    }
    public function delete_reason_for_cancellation(Request $request){
        if(DB::connection('mysql_mes')->table('production_order')
        ->where('remarks', '=', $request->delete_reason_cancellation)
        ->exists()){
            return response()->json(['success' => 0, 'message' => 'Unable to process request. <b>'.$request->delete_reason_cancellation.'</b> has already existing transaction.']);
        }else{
            DB::connection('mysql_mes')->table('reason_for_cancellation_po')->where("reason_for_cancellation_id", $request->delete_reason_cancellation_id)->delete();
            return response()->json(['success' => 1, 'message' => 'Reason for cancellation successfully deleted.']);
        } 
    }
    public function reverse_mark_as_done_task(Request $request){
        try {
            $now = Carbon::now();
            // dd($request->all());
                $prod = DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->reset_prod)->first();
                if(empty($request->reset_job_ticket_id)){
                    // dd($request->all());
                    $jt_details = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->reset_prod)->get();
                    foreach($jt_details as $row){
                        if($row->workstation == "Spotwelding"){
                            DB::connection('mysql_mes')->table('spotwelding_qty')->where('job_ticket_id', $row->job_ticket_id)->delete();
                            DB::connection('mysql_mes')->table('quality_inspection')->where('reference_type', 'Spotwelding')->where('reference_id', $row->job_ticket_id)->delete();
                            DB::connection('mysql_mes')->table('reject_reason')->where('job_ticket_id', $row->job_ticket_id)->delete();
                            DB::connection('mysql_mes')->table('spotwelding_part')->where('housing_production_order', $request->reset_prod)->delete();
                            DB::connection('mysql_mes')->table('spotwelding_reject')->where('job_ticket_id', $row->job_ticket_id)->delete();
                        }else{
                            DB::connection('mysql_mes')->table('time_logs')->where('job_ticket_id', $row->job_ticket_id)->delete();
                            DB::connection('mysql_mes')->table('quality_inspection')->where('reference_id', $row->job_ticket_id)->delete();
                            DB::connection('mysql_mes')->table('reject_reason')->where('job_ticket_id', $row->job_ticket_id)->delete();
                        }
                        $values = [
                            'status' => 'Pending',
                            'remarks' => '',
                            'completed_qty' => 0,
                            'reject' => 0,
                            'good' => 0,
                            'last_modified_by' => Auth::user()->employee_name,
                            'last_modified_at' => $now->toDateTimeString()
                        ];
                        DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $row->job_ticket_id)->update($values);
                    }
                    $values1 = [
                        'status' => 'Not Started',
                        'produced_qty' => 0,
                        'feedback_qty' => 0,
                        'actual_start_date' => null,
                        'actual_end_date' => null,
                        'last_modified_by' => Auth::user()->employee_name,
                        'last_modified_at' => $now->toDateTimeString()
                    ];
                }else{
                    $jt = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $request->reset_job_ticket_id)->first();
                    if($jt->workstation == "Spotwelding"){
                        DB::connection('mysql_mes')->table('spotwelding_qty')->where('job_ticket_id', $request->reset_job_ticket_id)->delete();
                        DB::connection('mysql_mes')->table('quality_inspection')->where('reference_type', 'Spotwelding')->where('reference_id', $request->reset_job_ticket_id)->delete();
                        DB::connection('mysql_mes')->table('reject_reason')->where('job_ticket_id', $request->reset_job_ticket_id)->delete();
                        DB::connection('mysql_mes')->table('spotwelding_part')->where('housing_production_order', $request->reset_prod)->delete();
                        DB::connection('mysql_mes')->table('spotwelding_reject')->where('job_ticket_id', $request->reset_job_ticket_id)->delete();
                    }else{
                        DB::connection('mysql_mes')->table('time_logs')->where('job_ticket_id', $request->reset_job_ticket_id)->delete();
                        DB::connection('mysql_mes')->table('quality_inspection')->where('reference_id', $request->reset_job_ticket_id)->delete();
                        DB::connection('mysql_mes')->table('reject_reason')->where('job_ticket_id', $request->reset_job_ticket_id)->delete();
                    }
                    $values = [
                        'status' => 'Pending',
                        'remarks' => '',
                        'completed_qty' => 0,
                        'reject' => 0,
                        'good' => 0,
                        'last_modified_by' => Auth::user()->employee_name,
                        'last_modified_at' => $now->toDateTimeString()
                    ];
                    DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $request->reset_job_ticket_id)->update($values);
                    $jt_workstation_details = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->reset_prod)->get();
                    $count_pending=collect($jt_workstation_details)->where('status', 'Pending')->count();
                    $count_workstation= collect($jt_workstation_details)->count();
                    if($count_pending == $count_workstation){
                        $values1 = [
                            'status' => 'Not Started',
                            'produced_qty' => 0,
                            'feedback_qty' => 0,
                            'actual_start_date' => null,
                            'actual_end_date' => null,
                            'last_modified_by' => Auth::user()->employee_name,
                            'last_modified_at' => $now->toDateTimeString()
                        ];
                    }else{
                        $values1 = [
                            'status' => 'In Progress',
                            'produced_qty' => 0,
                            'feedback_qty' => 0,
                            'actual_end_date' => null,
                            'last_modified_by' => Auth::user()->employee_name,
                            'last_modified_at' => $now->toDateTimeString()
                        ];
                    }
                }
                DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->reset_prod)->update($values1);
                return response()->json(['success' => 1, 'message' => 'Task successfully updated', 'prod'=> $request->reset_prod, 'reload_tbl' => $request->reload_tbl]);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }
    public function get_tbl_reset_workstation($prod){
        $list= DB::connection('mysql_mes')->table('job_ticket')->join('process', 'process.process_id', 'job_ticket.process_id')->where('production_order', $prod)->select('job_ticket.*', 'process.process_name')->get();
        // dd($jt);
        return view('tables.tbl_reset_workstation', compact('list'));
    }
  
    public function get_reject_categ_and_process(Request $request){
        $caterory = DB::connection('mysql_mes')->table('reject_category')->get();
        $process_list= DB::connection('mysql_mes')
        ->table('process_assignment')
        ->join('process', 'process.process_id','process_assignment.process_id')
        ->where('process_assignment.workstation_id', $request->workstation)
        ->select('process_assignment.process_id', 'process.process_name')
        ->groupBy('process_assignment.process_id', 'process.process_name')
        ->get();

        return response()->json(['category' => $caterory, 'process'=> $process_list]);

    }
    public function get_warning_notif_for_custom_shift($operation){
        $now = Carbon::now();
        $to=$now;
        $from=Carbon::now()->subDays(30);
        $period = CarbonPeriod::create($from, $to);
        $data=[];
        foreach ($period as $date) {
            $prod= DB::connection('mysql_mes')->table('production_order')
                ->whereDate('production_order.planned_start_date', $date)
                ->where('production_order.operation_id', $operation)->groupBy('production_order.production_order')->select('production_order.production_order')->pluck('production_order.production_order');
            $shift_sched= DB::connection('mysql_mes')->table('shift_schedule')
            ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
            ->whereDate('shift_schedule.date', $date)
            ->where('shift.operation_id', $operation)
            ->max('time_out');
            if(empty($shift_sched)){
                $shift_sched= DB::connection('mysql_mes')->table('shift')->where('operation_id', $operation)->where('shift_type', 'Regular Shift')->max('time_out');
            }
            $shift_sched= date('H:i:s', strtotime($shift_sched));
            $timelogs=DB::connection('mysql_mes')->table('time_logs')
                ->join('job_ticket as jt','jt.job_ticket_id', 'time_logs.job_ticket_id')
                ->join('production_order as p','p.production_order', 'jt.production_order')
                ->join('process', 'process.process_id', 'jt.process_id')
                ->where('jt.workstation', '!=', 'Painting')
                ->whereIn('jt.production_order', $prod)
                ->whereDate('time_logs.to_time',$date )
                ->selectRaw('p.status, jt.workstation, time_logs.operator_name,process.process_name,jt.production_order, MAX(to_time) as to_time')
                ->groupBy('jt.workstation', 'time_logs.operator_name', 'process.process_name', 'jt.production_order', 'p.status')
                ->orderBy('to_time', 'desc')
                ->first();
            // $max= collect($timelogs)->max('to_time');
            // $max_works= collect($timelogs)->max('to_time')->get();
            if($timelogs){
                $timelogss = date('H:i:s', strtotime($timelogs->to_time));
                if($shift_sched < $timelogss) {
                    $prod_details = DB::connection('mysql')->table('tabWork Order')
					    ->where('name', $timelogs->production_order)->first();

                    if($prod_details->docstatus == 2 && $timelogs->status != 'Cancelled'){
                        $status = 'Unknown Status';
                    }else if($prod_details->docstatus == 1 && $timelogs->status == 'Cancelled'){
                        $status = 'Unknown Status';
                    }else{
                        $status = null;
                    }
                    
                    $operator_out = date('h:i A', strtotime($timelogs->to_time));
                    $shift_out = date('h:i A', strtotime($shift_sched));
                    $data[]=[
                        'date'=> date('Y-m-d', strtotime($date)),
                        'data' => $timelogs,
                        'shift' => $shift_sched,
                        'timelogs' => $timelogs->to_time,
                        'shift_out' =>$shift_out,
                        'operator_out' =>$operator_out,
                        'status' => $status
                        // 'workstation' =>$max_works
                    ];
                }
            }
        }
        // dd($data);
        return view('tables.tbl_get_prod_sched_shift_warning', compact('data'));
    }
    public function shift_sched_details(Request $request){
        if($request->shift){
            $shift=DB::connection('mysql_mes')->table('shift')->where('shift_id', $request->shift)->first();
            return response()->json($shift);
        }
    }
    public function schedule_prod_calendar_details(Request $request){
        //get production order details and join in delivery table to get the latest delivery date
        $forpage= $request->forpage;
        $planned_date=$request->date;
        $operation=$request->operation;
        if($request->operation == 2){
            $prod_details = DB::connection('mysql_mes')->table('job_ticket as jt')
                ->join('production_order as pro','pro.production_order','jt.production_order')
                ->leftJoin('delivery_date', function($join){
                    $join->on( DB::raw('IFNULL(pro.sales_order, pro.material_request)'), '=', 'delivery_date.reference_no');
                    $join->on('pro.parent_item_code','=','delivery_date.parent_item_code');
                })
                ->where('pro.operation_id', 1)
                ->whereDate('jt.planned_start_date', $request->date)
                ->whereNotIn('pro.status', ['Cancelled', 'Completed', 'Closed'])
                ->where('jt.workstation', 'Painting')
                ->where('jt.planned_start_date','!=', null)
                ->distinct( 'delivery_date.rescheduled_delivery_date','pro.customer', 'pro.sales_order', 'pro.material_request','pro.delivery_date as deli', 'pro.production_order','pro.status','pro.item_code','pro.qty_to_manufacture','pro.description','pro.stock_uom')
                ->select( 'delivery_date.rescheduled_delivery_date','pro.customer', 'pro.sales_order', 'pro.material_request','pro.delivery_date as deli', 'pro.production_order','pro.status','pro.item_code','pro.qty_to_manufacture','pro.description','pro.stock_uom', 'pro.produced_qty')
                ->get();
        }else{
            $prod_details= DB::connection('mysql_mes')->table('production_order')
                ->leftJoin('delivery_date', function($join){
                    $join->on( DB::raw('IFNULL(production_order.sales_order, production_order.material_request)'), '=', 'delivery_date.reference_no');
                    $join->on('production_order.parent_item_code','=','delivery_date.parent_item_code');
                })
                ->where('production_order.operation_id', $request->operation)
                ->whereDate('production_order.planned_start_date', $request->date)
                ->whereNotIn('production_order.status', ['Cancelled', 'Completed', 'Closed'])
                ->select('production_order.*', 'delivery_date.rescheduled_delivery_date', 'delivery_date.delivery_date as deli')
                ->get();
        }
        
        return view('tables.tbl_prod_list_calendar', compact('prod_details', 'forpage', 'planned_date', 'operation'));
    }
    public function get_assembly_prod_calendar(Request $request){
        $myArray = explode(',', $request->prod_list);
        $prod_empty=[];
        $prod_orders = DB::connection('mysql_mes')->table('production_order')
            ->leftJoin('delivery_date', function($join){
                $join->on( DB::raw('IFNULL(production_order.sales_order, production_order.material_request)'), '=', 'delivery_date.reference_no');
                $join->on('production_order.parent_item_code','=','delivery_date.parent_item_code');
            })
            ->whereIn('production_order.production_order', $myArray)
            ->when($request->operation == 3, function ($q) use ($request){
                $q->whereDate( DB::raw('IFNULL(delivery_date.rescheduled_delivery_date, delivery_date.delivery_date)'), '<', $request->planned)
                    ->whereRaw('(production_order.item_code = delivery_date.parent_item_code)');
            })
            ->select('production_order.*', 'delivery_date.delivery_date as deli', 'delivery_date.delivery_date_id',DB::raw('IFNULL(delivery_date.rescheduled_delivery_date, delivery_date.delivery_date) as rescheduled_delivery_date') )
            ->get();
        $prod= collect($prod_orders)->pluck('production_order');
        $prod_implode=$prod->implode(',');
        $data=[];
        $trans_history=[];
        $planned_start_date= $request->planned;
        foreach($prod_orders as $row){
            //get_reschedule_log and be used in submission in erp
            $delivery_id=DB::connection('mysql_mes')->table('delivery_date_reschedule_logs')
            ->join('delivery_date', 'delivery_date_reschedule_logs.delivery_date_id', 'delivery_date.delivery_date_id')
            ->join('delivery_reschedule_reason', 'delivery_reschedule_reason.reschedule_reason_id', 'delivery_date_reschedule_logs.reschedule_reason_id')
            ->where('delivery_date.parent_item_code', $row->parent_item_code)
            ->where('delivery_date_reschedule_logs.delivery_date_id', $row->delivery_date_id)
            ->select('delivery_date_reschedule_logs.*', 'delivery_reschedule_reason.reschedule_reason')
            ->orderBy('delivery_date_reschedule_logs.reschedule_log_id', 'desc')
            ->get();
            foreach($delivery_id as $rows){
                $previous_row=DB::connection('mysql_mes')->table('delivery_date_reschedule_logs')->where('delivery_date_id', $rows->delivery_date_id)->where('reschedule_log_id', '>', $rows->reschedule_log_id)->orderby('reschedule_log_id', 'asc')->first();
                $data[]=[
                    'delivery_date'=> (empty($previous_row))? Carbon::parse($row->rescheduled_delivery_date)->format('M-d-Y'): Carbon::parse($previous_row->previous_delivery_date)->format('M-d-Y'),                
                    'delivery_reason' => $rows->reschedule_reason,
                    'remarks' => $rows->remarks
                ];
            }

            $trans_history[]=[
                    'prod'=> $row,                
                    'data' => $data,
            ];
        }
        // return $trans_history;
        $reason= DB::connection('mysql_mes')->table('delivery_reschedule_reason')->get();

        if(count($prod_orders) == 0){
            return response()->json(['success' => 0, 'message' => 'Reschedule production order', 'prod'=> $prod_orders]);
        }else{
            return view('reports.tbl_reschedule_delivery_date_and_planstartdate', compact('prod_orders','reason', 'trans_history', 'prod_implode', 'planned_start_date'));
        }

    }

    public function get_machines_pending_for_maintenance(Request $request, $operation_id){
        $operation = DB::connection('mysql_mes')->table('operation')->where('operation_id', $operation_id)->pluck('operation_name')->first();
        
        $machines = DB::connection('mysql_mes')->table('machine')->where('operation_id', $operation_id)->pluck('machine_code');

		$machine_breakdown = DB::connection('mysql_mes')->table('machine as m')
			->join('machine_breakdown as mb', 'm.machine_code', 'mb.machine_id')
			->whereIn('m.status', ['Unavailable', 'On-going Maintenance'])->whereIn('m.machine_code', $machines)->where('mb.status', '!=','Done')
            ->when($operation != 'Painting', function ($q){
                return $q->where('m.machine_name', '!=', 'Painting Machine');
            })
			->select('m.*', 'mb.category', 'mb.date_reported','mb.type', 'mb.breakdown_reason', 'mb.corrective_reason', 'mb.findings', 'mb.work_done', 'mb.date_resolved', 'mb.machine_breakdown_id', 'mb.status as breakdown_status')->orderByRaw("FIELD(mb.status, 'In Process', 'Pending', 'On Hold') ASC")
			->get();

        return view('tables.tbl_machines_pending_for_maintenance', compact('machine_breakdown', 'operation'));
    }
}