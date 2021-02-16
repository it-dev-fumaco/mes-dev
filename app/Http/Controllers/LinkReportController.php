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
    public function index(){
        return view('reports.report_index');

    }
    public function painting_report_page(){
        $parts_category= DB::connection('mysql_mes')->table('production_order')->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')->where('jt.workstation', 'Painting')->where('production_order.operation_id',1 )->whereNotNull('production_order.parts_category')->groupBy('production_order.parts_category')->select('production_order.parts_category')->get();       
        // dd($parts_category);
        return view('link_report.painting_report', compact('parts_category'));

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
            $production_order=($request->parts_category != "All")? (DB::connection('mysql_mes')->table('production_order')->whereDate('planned_start_date', $date)->where('operation_id', $operation)->where('parts_category', 'like','%'.$request->parts_category.'%')->get()):(DB::connection('mysql_mes')->table('production_order')->whereDate('planned_start_date', $date)->where('operation_id', $operation)->get());
            $over_time_shift= DB::connection('mysql_mes')->table('shift_schedule')
            ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
            ->whereDate('shift_schedule.date', $date)
            ->where('shift.operation_id', $operation)
            ->where('shift.shift_type', "Overtime Shift")
            ->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();

            $special_shift= DB::connection('mysql_mes')->table('shift_schedule')
            ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
            ->whereDate('shift_schedule.date', $date)->where('shift.operation_id', $operation)->where('shift.shift_type', "Special Shift")->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();
           
            $pluck_pro= collect($production_order)->pluck('production_order');
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
            
            if($special_shift){
                $reg_hours = $special_shift->hrs_of_work; 
            }else{
                $reg_shift= DB::connection('mysql_mes')->table('shift')->where('shift.operation_id', $operation)->where('shift.shift_type', "Regular Shift")->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();
                $reg_hours = $reg_shift->hrs_of_work;
            }

            $overtime_hour = ($over_time_shift != null) ?  $over_time_shift->hrs_of_work: 0;
            if($request->parts_category == 'All'){
                if($operation == 3){
                    $production=DB::connection('mysql_mes')->table('production_order')->whereDate('planned_start_date', $date)->where('operation_id', $operation)->whereRaw('parent_item_code = item_code')->get();
                    $produced_qty= collect($production)->sum('feedback_qty');
                    $output_qty = (collect($production)->sum('qty_to_manufacture') == 0)?  1 :collect($production)->sum('qty_to_manufacture');            
                    $per_day_planned= collect($production)->sum('qty_to_manufacture');
                    $per_day_produced= collect($production)->sum('feedback_qty');

                }else{
                    $produced_qty=  collect($production_order)->where('parts_category', 'SA - Housing')->where('item_classification', 'HO - Housing')->sum('produced_qty');
                    $output_qty = (collect($production_order)->where('parts_category', 'SA - Housing')->where('item_classification', 'HO - Housing')->sum('qty_to_manufacture') == 0)?  1 :collect($production_order)->where('parts_category', 'SA - Housing')->where('item_classification', 'HO - Housing')->sum('qty_to_manufacture');
                    $per_day_planned= collect($production_order)->where('parts_category', 'SA - Housing')->where('item_classification', 'HO - Housing')->sum('qty_to_manufacture');
                    $per_day_produced= collect($production_order)->where('parts_category', 'SA - Housing')->where('item_classification', 'HO - Housing')->sum('produced_qty');

                }
            }else{
                $produced_qty= ($operation == 3)? collect($production_order)->sum('feedback_qty'):collect($production_order)->sum('produced_qty');
                $output_qty = (collect($production_order)->sum('qty_to_manufacture') == 0) ? 1 : collect($production_order)->sum('qty_to_manufacture');
                $per_day_planned=  collect($production_order)->sum('qty_to_manufacture');
                $per_day_produced= ($operation == 3)? collect($production_order)->sum('feedback_qty'):collect($production_order)->sum('produced_qty');

            }

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
        // dd($parts);
        return view('link_report.fabrication_report',  compact('workstation', 'process', 'parts','sacode', 'parts_category'));
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
            $production_order=($request->parts_category != "All")? (DB::connection('mysql_mes')->table('production_order')->whereDate('planned_start_date', $date)->where('operation_id', $operation)->where('parts_category', 'like','%'.$request->parts_category.'%')->get()):(DB::connection('mysql_mes')->table('production_order')->whereDate('planned_start_date', $date)->where('operation_id', $operation)->get());
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
            $produced_qty = (($operation == 3)? collect($production_order)->sum('feedback_qty'):collect($production_order)->sum('produced_qty'));
            $output_qty = (collect($production_order)->sum('qty_to_manufacture') == 0) ? 1 : collect($production_order)->sum('qty_to_manufacture');
            
            if($special_shift){
                $reg_hours = $special_shift->hrs_of_work; 
            }else{
                $reg_shift= DB::connection('mysql_mes')->table('shift')->where('shift.operation_id', $operation)->where('shift.shift_type', "Regular Shift")->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();
                $reg_hours = $reg_shift->hrs_of_work;
            }

            $overtime_hour = ($over_time_shift != null) ?  $over_time_shift->hrs_of_work: 0;
            if($request->parts_category == 'All'){
                if($operation == 3){
                    $production=DB::connection('mysql_mes')->table('production_order')->whereDate('planned_start_date', $date)->where('operation_id', $operation)->whereRaw('parent_item_code = item_code')->get();
                    $per_day_planned= collect($production)->sum('qty_to_manufacture');
                    $per_day_produced= collect($production)->sum('feedback_qty');

                }else{
                    $per_day_planned= collect($production_order)->where('parts_category', 'SA - Housing')->where('item_classification', 'HO - Housing')->sum('qty_to_manufacture');
                    $per_day_produced= collect($production_order)->where('parts_category', 'SA - Housing')->where('item_classification', 'HO - Housing')->sum('produced_qty');

                }
            }else{
                $per_day_planned=  collect($production_order)->sum('qty_to_manufacture');
                $per_day_produced= ($operation == 3)? collect($production_order)->sum('feedback_qty'):collect($production_order)->sum('produced_qty');

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
        $parts_category= DB::connection('mysql_mes')->table('production_order')->where('operation_id', 3)->whereNotNull('parts_category')->groupBy('parts_category')->select('parts_category')->get();       

        return view('link_report.assembly_report', compact('parts_category'));
    }
    
    public function qa_report(){
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
        // dd($fab_process);

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
                "name" => $emp_name->employee_name,
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

        return view('link_report.qa_report', compact('process_painting','item_code','customer','production_order', 'qc_name', 'operators','fab_workstation','assem_workstation','pain_workstation','fab_process', "assem_process"));
    } 
    
    public function painting_output_report(Request $request){
        $operation= 1;
        $now = Carbon::now();
        $to=$request->end_date;
        $from=$request->start_date;
        $operation= 1;
        $period = CarbonPeriod::create($from, $to);
        $day=[];
        foreach ($period as $date) {
            $overtime_hour=0;
            $production_order=($request->parts_category != "All")? (DB::connection('mysql_mes')->table('production_order')->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')->where('jt.workstation', 'Painting')->whereDate('jt.planned_start_date', $date)->where('production_order.operation_id', $operation)->where('production_order.parts_category', 'like','%'.$request->parts_category.'%')->groupBy('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->select('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->get()):(DB::connection('mysql_mes')->table('production_order')->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')->where('jt.workstation', 'Painting')->whereDate('jt.planned_start_date', $date)->where('production_order.operation_id', $operation)->groupBy('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->select('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->get());
            $over_time_shift= DB::connection('mysql_mes')->table('shift_schedule')
            ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
            ->whereDate('shift_schedule.date', $date)
            ->where('shift.operation_id', $operation)
            ->where('shift.shift_type', "Overtime Shift")
            ->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();
            $special_shift= DB::connection('mysql_mes')->table('shift_schedule')
            ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
            ->whereDate('shift_schedule.date', $date)->where('shift.operation_id', $operation)->where('shift.shift_type', "Special Shift")->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();
           
            $pluck_pro= collect($production_order)->pluck('production_order');
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
            $produced_qty= collect($production_order)->sum('produced_qty');
            $output_qty = (collect($production_order)->sum('qty_to_manufacture') == 0) ? 1 : collect($production_order)->sum('qty_to_manufacture');
            $per_day_planned=  collect($production_order)->sum('qty_to_manufacture');
            $per_day_produced= collect($production_order)->sum('produced_qty');

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
            $production_order=($request->parts_category != "All")? (DB::connection('mysql_mes')->table('production_order')->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')->where('jt.workstation', 'Painting')->whereDate('jt.planned_start_date', $date)->where('production_order.operation_id', $operation)->where('production_order.parts_category', 'like','%'.$request->parts_category.'%')->groupBy('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->select('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->get()):(DB::connection('mysql_mes')->table('production_order')->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')->where('jt.workstation', 'Painting')->whereDate('jt.planned_start_date', $date)->where('production_order.operation_id', $operation)->groupBy('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->select('production_order.production_order', 'production_order.produced_qty', 'production_order.feedback_qty', 'production_order.qty_to_manufacture')->get());
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
            $produced_qty= collect($production_order)->sum('produced_qty');
            $output_qty = (collect($production_order)->sum('qty_to_manufacture') == 0) ? 1 : collect($production_order)->sum('qty_to_manufacture');
            $per_day_planned=  collect($production_order)->sum('qty_to_manufacture');
            $per_day_produced= collect($production_order)->sum('produced_qty');

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
}