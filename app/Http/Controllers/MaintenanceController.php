<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use App\Exports\ExportDataQaInspectionLog; 
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\GeneralTrait;

use DB;

class MaintenanceController extends Controller
{
    use GeneralTrait;
	public function index(){
        $user_details = DB::connection('mysql_essex')->table('users')
            ->join('designation', 'users.designation_id', '=', 'designation.des_id')
            ->join('departments', 'users.department_id', '=', 'departments.department_id')
            ->where('user_id', Auth::user()->user_id)->first();
        $maintence_staff=DB::connection('mysql_mes')
        ->table('user')
        ->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
        ->where('user_group.module','Maintenance')
        ->groupBy("user.user_access_id", 'user.employee_name')
        ->select("user.user_access_id", 'user.employee_name')
        ->get();
        $machine=DB::connection('mysql_mes')->table('machine')->get();
        $operation=DB::connection('mysql_mes')->table('operation')->get();
        return view('maintenance.maintenance_dashboard', compact('user_details', 'maintence_staff', 'machine', 'operation'));

    }
    public function get_pending_maintenance_request(Request $request){
        $main= Db::connection('mysql_mes')
            ->table('machine_breakdown')
            ->leftJoin('machine', 'machine.machine_code', 'machine_breakdown.machine_id')
            ->leftJoin('workstation as w', 'w.workstation_id', 'machine_breakdown.workstation_id')
            ->leftJoin('operation as op', 'op.operation_id', 'w.operation_id')
            ->where('machine_breakdown.status', 'Pending')
            ->where(function($q) use ($request) {
                $q->where('machine_breakdown.machine_id', 'LIKE', '%'.$request->search_string.'%')
                    ->orwhere('machine_breakdown.machine_breakdown_id', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('machine_breakdown.date_reported', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('machine_breakdown.type', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('machine_breakdown.corrective_reason', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('machine_breakdown.breakdown_reason', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('machine_breakdown.category', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('machine.machine_name', 'LIKE', '%'.$request->search_string.'%');
            })
            ->select('machine.machine_name', 'machine_breakdown.*', 'op.operation_name')
            ->distinct('machine_breakdown_id')
            ->orderBy('machine_breakdown.machine_breakdown_id', 'desc')
            ->get();
            $data=[];
            foreach($main as $rw){
                $assigned_main_staff= Db::connection('mysql_mes')
                    ->table('assigned_maintenance_staff')
                    ->where('assigned_maintenance_staff.reference_id', $rw->machine_breakdown_id)
                    ->select('assigned_maintenance_staff.employee_name')
                    ->get();
                $data[]=[
                    'machine_breakdown_id' => $rw->machine_breakdown_id,
                    'machine_id'=> $rw->machine_id,
                    'date_reported' =>$rw->date_reported,
                    'type' => $rw->type,
                    'corrective_reason' =>$rw->corrective_reason,
                    'breakdown_reason' => $rw->breakdown_reason,
                    'category'=> $rw->category,
                    'machine_name'=>$rw->machine_name,
                    'main_staff' => $assigned_main_staff,
                    'reported_by' => $rw->reported_by,
                    'status' => $rw->status,
                    'operation_name' =>$rw->operation_name
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

        $maintenance = $paginatedItems;

        return view('maintenance.tables.tbl_maintenance_request_pending', compact('maintenance'));
    }
    public function get_completed_maintenance_request(Request $request){
        $main= Db::connection('mysql_mes')
            ->table('machine_breakdown')
            ->leftJoin('machine', 'machine.machine_code', 'machine_breakdown.machine_id')
            ->leftJoin('workstation as w', 'w.workstation_id', 'machine_breakdown.workstation_id')
            ->leftJoin('operation as op', 'op.operation_id', 'w.operation_id')
            ->where('machine_breakdown.status', 'Completed')
            ->where(function($q) use ($request) {
                $q->where('machine_breakdown.machine_id', 'LIKE', '%'.$request->search_string.'%')
                    ->orwhere('machine_breakdown.machine_breakdown_id', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('machine_breakdown.date_reported', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('machine_breakdown.type', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('machine_breakdown.corrective_reason', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('machine_breakdown.breakdown_reason', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('machine_breakdown.category', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('machine.machine_name', 'LIKE', '%'.$request->search_string.'%');
            })
            ->select('machine.machine_name', 'machine_breakdown.*', 'op.operation_name')
            ->distinct('machine_breakdown_id')
            ->orderBy('machine_breakdown.machine_breakdown_id', 'desc')
            ->get();
            $data=[];
            foreach($main as $rw){
                $assigned_main_staff= Db::connection('mysql_mes')
                    ->table('assigned_maintenance_staff')
                    ->where('assigned_maintenance_staff.reference_id', $rw->machine_breakdown_id)
                    ->select('assigned_maintenance_staff.employee_name')
                    ->get();
                $data[]=[
                    'machine_breakdown_id' => $rw->machine_breakdown_id,
                    'machine_id'=> $rw->machine_id,
                    'date_reported' =>$rw->date_reported,
                    'type' => $rw->type,
                    'corrective_reason' =>$rw->corrective_reason,
                    'breakdown_reason' => $rw->breakdown_reason,
                    'category'=> $rw->category,
                    'machine_name'=>$rw->machine_name,
                    'main_staff' => $assigned_main_staff,
                    'reported_by' => $rw->reported_by,
                    'status' => $rw->status,
                    'operation_name' =>$rw->operation_name,
                    'work_done' =>$rw->work_done,
                    'findings' =>$rw->findings,
                    'date_resolved' =>$rw->date_resolved,
                    'duration' =>$rw->duration,

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

        $maintenance = $paginatedItems;

        return view('maintenance.tables.tbl_maintenance_request_done', compact('maintenance'));

    }
    public function set_assigned_maintenance_staff(Request $request){
        $now = Carbon::now();
        $old_staff= (empty($request->oldstaff))? []: $request->oldstaff;
        $new_staff= (empty($request->newstaff))? []: $request->newstaff;
        $mergedArray = array_merge($old_staff, $new_staff);
        $ar=array_unique(array_diff_assoc($mergedArray, array_unique( $mergedArray ) ) );
        if(!empty($ar)){
            foreach($ar as $i => $r){
                $user =DB::connection('mysql_mes')->table('user')
                ->where('user_access_id', $r)
                ->first();
                $row= $i +1;
                return response()->json(['success' => 0, 'message' => 'Please check DUPLICATE '.$user->employee_name.' at ROW '.$row ]);

            }
        }else{
            if ($request->old_staff_main) {
                if($request->oldstaff_main_id == null){
                    DB::connection('mysql_mes')
                    ->table('assigned_maintenance_staff')
                    ->where('reference_id',$request->maintenance_request_id)->delete();
                }else{
                    $delete_shift=DB::connection('mysql_mes')
                    ->table('assigned_maintenance_staff')
                    ->whereIn('assigned_maintenance_staff_id', $request->old_staff_main)
                    ->whereNotIn('assigned_maintenance_staff_id', $request->oldstaff_main_id)
                    ->delete();
                }
            }
            // for insert
            if ($request->newstaff) {
                foreach($request->newstaff as $i => $row){
                    $user =DB::connection('mysql_mes')->table('user')
                        ->where('user_access_id', $row)
                        ->first();
                    $new_staff_main[] = [
                        'reference_id'=> $request->maintenance_request_id,
                        'user_access_id' => $row,
                        'employee_name' => $user->employee_name,
                        'created_by' => Auth::user()->email,
                        'created_at' => $now->toDateTimeString()
                    ];
                }
                DB::connection('mysql_mes')->table('assigned_maintenance_staff')->insert($new_staff_main);
            }
            // update
            if ($request->oldstaff) {
                foreach($request->oldstaff as $i => $row){
                    $user =DB::connection('mysql_mes')->table('user')
                        ->where('user_access_id', $row)
                        ->first();
                    $update= [
                        'user_access_id' => $row,
                        'employee_name' =>$user->employee_name,
                    ];
                    $id_forupdate= $request->oldstaff_main_id[$i];
                    DB::connection('mysql_mes')->table('assigned_maintenance_staff')->where('assigned_maintenance_staff_id',$id_forupdate)->update($update);
                }
            }
        }
        return response()->json(['message' => 'Assign Maintenance Staff is successfully updated.']);

    }
    public function get_maintenance_request_details(Request $request){
        $main= Db::connection('mysql_mes')
            ->table('machine_breakdown')
            ->leftJoin('machine', 'machine.machine_code', 'machine_breakdown.machine_id')
            ->leftJoin('user', 'user.user_access_id', 'machine_breakdown.assigned_maintenance_staff')
            ->where('machine_breakdown.machine_breakdown_id', $request->id)
            ->select('machine.machine_name', 'machine_breakdown.*', 'user.employee_name', 'machine.image')
            ->first();
        $assigned_main_staff= Db::connection('mysql_mes')
        ->table('assigned_maintenance_staff')
        ->where('assigned_maintenance_staff.reference_id', $request->id)
        ->select('assigned_maintenance_staff.employee_name')
        ->get();
        return view('maintenance.tables.tbl_maintenance_request_details', compact('main', 'assigned_main_staff'));
   
    }
    public function save_maintenance_request(Request $request){
        $list = [
            'findings' => $request->findings,
            'work_done'=> $request->work_done,
            'date_resolved' =>Carbon::parse($request->date_resolve)->format('Y-m-d H:i:s'),
            'status' => $request->maintenance_status,
            'duration' => $request->t_duration,

        ];
        // return $request->all();

        DB::connection('mysql_mes')->table('machine_breakdown')->where('machine_breakdown_id', $request->breakdown_id)->update($list);
        return response()->json(['message' => 'Maintenance Request is successfully updated.']);
    }
    public function get_maintenance_staff(){
        $maintence_staff=DB::connection('mysql_mes')
        ->table('user')
        ->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
        ->where('user_group.module','Maintenance')
        ->groupBy("user.user_access_id", 'user.employee_name')
        ->select("user.user_access_id", 'user.employee_name')
        ->get();
        // return $maintence_staff;
        return response()->json(['maintenance' => $maintence_staff]);

    }
    public function get_current_assigned_maintenance(Request $request){
        $assigned= DB::connection('mysql_mes')->table('assigned_maintenance_staff')->where('reference_id', $request->id)->get();
        $maintence_staff=DB::connection('mysql_mes')
        ->table('user')
        ->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
        ->where('user_group.module','Maintenance')
        ->groupBy("user.user_access_id", 'user.employee_name')
        ->select("user.user_access_id", 'user.employee_name')
        ->get();
        return response()->json(['old_staff' => $assigned, 'maintenance_staff' => $maintence_staff]);
    }
    public function print_maintenance_form(Request $request){
        $data= Db::connection('mysql_mes')
            ->table('machine_breakdown')
            ->leftJoin('machine', 'machine.machine_code', 'machine_breakdown.machine_id')
            ->leftJoin('workstation as w', 'w.workstation_id', 'machine_breakdown.workstation_id')
            ->leftJoin('operation as op', 'op.operation_id', 'w.operation_id')
            ->select('machine.machine_name', 'machine_breakdown.*', 'op.operation_name')
            ->where('machine_breakdown.machine_breakdown_id', $request->id)
            ->distinct('machine_breakdown_id')
            ->first();

            $main=[];
            $assigned_main_staff= Db::connection('mysql_mes')
                ->table('assigned_maintenance_staff')
                ->where('assigned_maintenance_staff.reference_id', $request->id)
                ->select('assigned_maintenance_staff.employee_name')
                ->get();

                $main=[
                    'machine_breakdown_id' => $data->machine_breakdown_id,
                    'machine_id'=> $data->machine_id,
                    'date_reported' =>$data->date_reported,
                    'type' => $data->type,
                    'corrective_reason' =>$data->corrective_reason,
                    'breakdown_reason' => $data->breakdown_reason,
                    'category'=> $data->category,
                    'machine_name'=>$data->machine_name,
                    'main_staff' => $assigned_main_staff,
                    'reported_by' => $data->reported_by,
                    'status' => $data->status,
                    'findings' =>$data->findings,
                    'work_done' =>$data->work_done,
                    'date_resolved' =>$data->date_resolved,
                    'operation' => ($data->workstation_id == 10)? 'Painting' :$data->operation_name
                ];
            // return $main;
            return view('maintenance.tbl_print_maintenance_form', compact('main'));
    }
    public function add_maintenance_sched_type(Request $request){
        $now = Carbon::now();
        $new=[];
        if (DB::connection('mysql_mes')
                ->table('maintenance_schedule_type')
                ->where('maintenance_schedule_type', $request->add_sched_type)
                ->where('operation_id', $request->add_op_sched_type)
                ->exists()){
                $get_operation=DB::connection('mysql_mes')->table('operation')->where('operation_id', $request->add_op_sched_type)->first();
                return response()->json(['success' => 0, 'message' => $request->add_sched_type.' Maintenance Schedule type for '.$get_operation->operation_name.' already exist.']);
        }else{
            $new= [
                'maintenance_schedule_type'=> $request->add_sched_type,
                'operation_id' => $request->add_op_sched_type,
                'start_date' => $request->add_sched_date,
                'created_by' => Auth::user()->email,
                'created_at' => $now->toDateTimeString()
            ];
            DB::connection('mysql_mes')->table('maintenance_schedule_type')->insert($new);
            return response()->json(['success' => 1, 'message' => 'New Maintenance Schedule Type Successfully Added']);	

        }
    }
    public function get_list_maintenance_sched_type(Request $request){
        $list= DB::connection('mysql_mes')
        ->table('maintenance_schedule_type as mst')
        ->leftJoin('operation as op', 'op.operation_id','mst.operation_id')
        ->where(function($q) use ($request) {
            $q->where('mst.maintenance_schedule_type', 'LIKE', '%'.$request->search_string.'%')
                ->orwhere('mst.start_date', 'LIKE', '%'.$request->search_string.'%')
                ->orWhere('op.operation_name', 'LIKE', '%'.$request->search_string.'%');
        })
        ->select('mst.*', 'op.operation_name')->orderBy('maintenance_schedule_type_id', 'desc')->paginate(10);

        return view('maintenance.tables.tbl_maintenance_sched_type_list', compact('list'));

    }
    public function edit_maintenance_sched_type(Request $request){
        $now = Carbon::now();
        $new=[];
        if (DB::connection('mysql_mes')
                ->table('maintenance_schedule_type')
                ->where('maintenance_schedule_type', $request->edit_sched_type)
                ->where('operation_id', $request->edit_op_sched_type)
                ->exists()){
                    if( $request->edit_sched_type ==  $request->orig_edit_sched_type && $request->edit_op_sched_type ==  $request->orig_edit_op_sched_type){
                        $update= [
                            'maintenance_schedule_type'=> $request->edit_sched_type,
                            'operation_id' => $request->edit_op_sched_type,
                            'start_date' => $request->edit_sched_date,
                            'last_modified_by' => Auth::user()->email,
                        ];
                        DB::connection('mysql_mes')->table('maintenance_schedule_type')->where('maintenance_schedule_type_id', $request->id_mtype)->update($update);
                        return response()->json(['success' => 1,'message' => 'Maintenance Schedule Type Successfully Updated.','reloadtbl' => $request->reloadtbl_edit]);
                    }else{
                        $get_operation=DB::connection('mysql_mes')->table('operation')->where('operation_id', $request->edit_op_sched_type)->first();
                        return response()->json(['success' => 0, 'message' => $request->edit_sched_type.' Maintenance Schedule type for '.$get_operation->operation_name.' already exist.']);

                    }
        }else{
            $new= [
                'maintenance_schedule_type'=> $request->edit_sched_type,
                'operation_id' => $request->edit_op_sched_type,
                'start_date' => $request->edit_sched_date,
                'last_modified_by' => Auth::user()->email,
            ];
            DB::connection('mysql_mes')->table('maintenance_schedule_type')->where('maintenance_schedule_type_id', $request->id_mtype)->update($update);
            return response()->json(['success' => 1,'message' => 'Maintenance Schedule Type Successfully Updated.','reloadtbl' => $request->reloadtbl_edit]);

        }
    }
    public function delete_maintenance_sched_type(Request $request){
            // if (DB::connection('mysql_mes')->table('qa_checklist')->where('reject_list_id', $request->delete_rejectlist_id)
            // ->exists()){
            //     return response()->json(['success' => 0, 'message' => 'Unable to Delete Reject list. Reject list already asigned QA checklist.']);
    
            // }else{
                DB::connection('mysql_mes')->table('maintenance_schedule_type')->where('maintenance_schedule_type_id', $request->del_id_mtype)->delete();
                return response()->json(['success' => 1, 'message' => 'Maintenance Schedule Type Successfully Deleted!']);
            // }
    }
    public function get_maintenance_type_by_operation(Request $request){
        $output = '<option value="">Select Maintenance Sched Type</option>';
        $list= DB::connection('mysql_mes')
        ->table('maintenance_schedule_type as mst')
        ->leftJoin('operation as op', 'op.operation_id','mst.operation_id')
        ->where('op.operation_id', $request->id)
        ->select('mst.*', 'op.operation_name')->get();

        foreach($list as $row)
                 {
                $output .= '<option value="'.$row->maintenance_schedule_type_id.'">'.$row->maintenance_schedule_type.'</option>';
                 }
        return $output;
    }
    public function get_task_list_for_add_pm(){
        $pmt= DB::connection('mysql_mes')->table('preventive_maintenance_task')->get();
       
        return response()->json(['pmt' => $pmt]);
    }
    public function get_task_desc_for_add_pm(Request $request){
        $pmt= DB::connection('mysql_mes')->table('preventive_maintenance_task')->where('preventive_maintenance_task_id', $request->id)->first();
        
        return response()->json(['pmt' => $pmt->preventive_maintenance_desc]);
    } 
    public function get_order_preventive_maintenance(){
            // Get the last created order
            $lastOrder = DB::connection('mysql_mes')->table('preventive_maintenance')->orderBy('preventive_maintenance_id', 'desc')->select('preventive_maintenance_id')->first();

            if (empty($lastOrder->preventive_maintenance_id)){
                // We get here if there is no order at all
                // If there is no number set it to 0, which will be 1 at the end.

                $number = 0;
            }else{
                $number1 = $lastOrder->preventive_maintenance_id;
                $number = preg_replace("/[^0-9]{1,4}/", '', $number1); // return 1234
            }

            // If we have ORD000001 in the database then we only want the number
            // So the substr returns this 000001

            // Add the string in front and higher up the number.
            // the %05d part makes sure that there are always 6 numbers in the string.
            // so it adds the missing zero's when needed.
         
            return 'PM-' . sprintf('%05d', intval($number) + 1);
    } 
    public function save_preventive_maintenance_request(Request $request){
        // return $request->all();
        $now = Carbon::now();
        $newtaskassign= (empty($request->newtaskassign))? []: $request->newtaskassign;
        $new_staff= (empty($request->newstaff))? []: $request->newstaff;
        $ar=array_unique(array_diff_assoc($newtaskassign, array_unique( $newtaskassign ) ) );
        $arr=array_unique(array_diff_assoc($new_staff, array_unique( $new_staff ) ) );

        if(!empty($ar)){
            foreach($ar as $i => $r){
                $row= $i +1;
                return response()->json(['success' => 0, 'message' => 'Please check DUPLICATE '.$r.' at ROW '.$row ]);
            }
        }
        if(!empty($arr)){
            foreach($arr as $i => $rr){
                $user =DB::connection('mysql_mes')->table('user')
                ->where('user_access_id', $rr)
                ->first();
                $row= $i +1;
                return response()->json(['success' => 0, 'message' => 'Please check DUPLICATE '.$user->employee_name.' at ROW '.$row ]);
            }
        }
        
            $id=$this->get_order_preventive_maintenance();
            $machine= DB::connection('mysql_mes')->table('machine')->where('machine_id', $request->pm_machine)->first();
            $new= [
                'preventive_maintenance_id'=>  $id,
                'operation_id'=> $request->pm_op,
                'maintenance_schedule_type_id' => $request->pm_mst,
                'machine_id' => $request->pm_machine,
                'machine_code'=> $machine->machine_code,
                'machine_name' => $machine->machine_name,
                'created_by' => Auth::user()->email,
                'created_at' => $now->toDateTimeString()
            ];
            $new_machine=[];
            if ($request->newtaskassign) {
                foreach($request->newtaskassign as $i => $row){
                    $new_machine[] = [
                        'reference_no'=> $id,
                        'preventive_maintenance_task_id' => $row,
                        'created_by' => Auth::user()->email,
                        'created_at' => $now->toDateTimeString()
                    ];
                }
                DB::connection('mysql_mes')->table('assigned_preventive_maintenance_task')->insert($new_machine);
            }
            if ($request->newstaff) {
                foreach($request->newstaff as $i => $row){
                    $user =DB::connection('mysql_mes')->table('user')
                        ->where('user_access_id', $row)
                        ->first();
                    $new_staff_main[] = [
                        'reference_id'=> $id,
                        'user_access_id' => $row,
                        'employee_name' => $user->employee_name,
                        'created_by' => Auth::user()->email,
                        'created_at' => $now->toDateTimeString()
                    ];
                }
                DB::connection('mysql_mes')->table('assigned_maintenance_staff')->insert($new_staff_main);
            }

            DB::connection('mysql_mes')->table('preventive_maintenance')->insert($new);
            return response()->json(['success' => 1, 'message' => $id.' successfully created.']);	

    } 
    public function add_preventive_maintenance_task(Request $request){
        // dd($request->all());
        $now = Carbon::now();
        $netask= (empty($request->pm_task))? []: $request->pm_task;
        $ar=array_unique(array_diff_assoc($netask, array_unique( $netask )) );
        if(!empty($ar)){
            foreach($ar as $i => $r){
                $row= $i +1;
                return response()->json(['success' => 0, 'message' => 'Please check DUPLICATE '.$r.' at ROW '.$row ]);
            }
        }
        if ($request->pm_task) {
            foreach($request->pm_task as $i => $row){
                if (DB::connection('mysql_mes')
                ->table('preventive_maintenance_task')
                ->where('preventive_maintenance_task','like', '%'.$row.'%')
                ->where('preventive_maintenance_desc','like', '%'.$request->pm_task_desc[$i].'%')
                ->exists()){
                    return response()->json(['success' => 0, 'message' => $row.' - '.$request->pm_task_desc[$i].' already exist.']);
                }else{
                        $new= [
                            'preventive_maintenance_task' => $row,
                            'preventive_maintenance_desc' => $request->pm_task_desc[$i],
                            'created_by' => Auth::user()->email,
                            'created_at' => $now->toDateTimeString()
                        ];
                        DB::connection('mysql_mes')->table('preventive_maintenance_task')->insert($new);
                }
            }
            return response()->json(['success' => 1, 'message' => 'Preventive Maintenance task successfully created.']);	
        }
        
    }
    public function get_preventive_maintenance_task(Request $request){
        $list= DB::connection('mysql_mes')
        ->table('preventive_maintenance_task as pmt')
        ->where(function($q) use ($request) {
            $q->where('pmt.preventive_maintenance_task', 'LIKE', '%'.$request->search_string.'%')
                ->orwhere('pmt.preventive_maintenance_desc', 'LIKE', '%'.$request->search_string.'%');
        })
        ->select('pmt.*')->orderBy('preventive_maintenance_task_id', 'desc')->paginate(10);

        return view('maintenance.tables.tbl_preventive_maintenance_task', compact('list'));

    }
    public function get_pm_pending_list(Request $request){
        $list= DB::connection('mysql_mes')
        ->table('preventive_maintenance as pm')
        ->join('assigned_preventive_maintenance_task as apmt', 'apmt.reference_no', 'pm.preventive_maintenance_id')
        ->join('preventive_maintenance_task as pmt', 'pmt.preventive_maintenance_task_id', 'apmt.preventive_maintenance_task_id')
        ->join('maintenance_schedule_type as mst', 'mst.maintenance_schedule_type_id', 'pm.maintenance_schedule_type_id')
        ->join('operation as op', 'op.operation_id', 'pm.operation_id')
        ->where(function($q) use ($request) {
            $q->where('pm.preventive_maintenance_id', 'LIKE', '%'.$request->search_string.'%')
                ->orwhere('pm.machine_id', 'LIKE', '%'.$request->search_string.'%')
                ->orwhere('pm.machine_code', 'LIKE', '%'.$request->search_string.'%')
                ->orwhere('pm.machine_name', 'LIKE', '%'.$request->search_string.'%')
                ->orwhere('mst.maintenance_schedule_type', 'LIKE', '%'.$request->search_string.'%')
                ->orwhere('pmt.preventive_maintenance_task', 'LIKE', '%'.$request->search_string.'%')
                ->orwhere('op.operation_name', 'LIKE', '%'.$request->search_string.'%')
                ->orwhere('pmt.preventive_maintenance_desc', 'LIKE', '%'.$request->search_string.'%');
        })
        ->distinct('pm.preventive_maintenance_id')
        ->select('pm.*','mst.*', 'op.operation_name')
        ->get();
        $data=[];
        foreach($list as $r){
            $task= DB::connection('mysql_mes')->table('preventive_maintenance as pm')
            ->join('assigned_preventive_maintenance_task as apmt', 'apmt.reference_no', 'pm.preventive_maintenance_id')
            ->join('preventive_maintenance_task as pmt', 'pmt.preventive_maintenance_task_id', 'apmt.preventive_maintenance_task_id')
            ->where('apmt.reference_no', $r->preventive_maintenance_id)
            ->get();
            $assigned_main_staff= Db::connection('mysql_mes')
            ->table('assigned_maintenance_staff')
            ->where('assigned_maintenance_staff.reference_id', $r->preventive_maintenance_id)
            ->select('assigned_maintenance_staff.employee_name')
            ->get();
            $data[]=[
                'preventive_maintenance_id'=>$r->preventive_maintenance_id,
                'operation_id'=>$r->operation_id,
                'maintenance_schedule_type_id'=>$r->maintenance_schedule_type_id,
                'machine_id'=>$r->machine_id,
                'machine_code'=>$r->machine_code,
                'machine_name'=>$r->machine_name,
                'maintenance_schedule_type'=>$r->maintenance_schedule_type,
                'start_date'=>$r->start_date,
                'operation_name'=>$r->operation_name,
                'task'=>$task,
                'staff'=>$assigned_main_staff

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

        $maintenance = $paginatedItems;
        // return $data;
        // ->where(function($q) use ($request) {
        //     $q->where('pmt.preventive_maintenance_task', 'LIKE', '%'.$request->search_string.'%')
        //         ->orwhere('pmt.preventive_maintenance_desc', 'LIKE', '%'.$request->search_string.'%');
        // })
        // ->select('pmt.*')->orderBy('preventive_maintenance_task_id', 'desc')->paginate(10);

        return view('maintenance.tables.tbl_pending_pm', compact('maintenance'));

    }
     
       
}
