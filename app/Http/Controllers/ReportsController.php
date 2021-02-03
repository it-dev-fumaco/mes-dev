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

class ReportsController extends Controller
{
    use GeneralTrait;
    
    public function fabrication_daily_report(Request $request){
        $now = Carbon::now();
        $to=$request->end_date;
        $from=$request->start_date;
        $period = CarbonPeriod::create($from, $to);
        $day=[];
        foreach ($period as $date) {
            $overtime_hour=0;
            $production_order= DB::connection('mysql_mes')->table('production_order')->whereDate('planned_start_date', $date)->where('operation_id', 1)->where('parts_category', 'SA - Housing')->where('item_classification', 'HO - Housing')->get();
            $over_time_shift= DB::connection('mysql_mes')->table('shift_schedule')
            ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
            ->whereDate('shift_schedule.date', $date)
            ->where('shift.operation_id', 1)
            ->where('shift.shift_type', "Overtime Shift")
            ->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();

            $special_shift= DB::connection('mysql_mes')->table('shift_schedule')
            ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
            ->whereDate('shift_schedule.date', $date)->where('shift.operation_id', 1)->where('shift.shift_type', "Special Shift")->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();
           
            $pluck_pro= collect($production_order)->pluck('production_order');
            $job_ticket1=DB::connection('mysql_mes')->table('job_ticket as jt')->whereIn('production_order', $pluck_pro)
            ->join('time_logs as tl', 'tl.job_ticket_id', 'jt.job_ticket_id')
            // ->whereDate('tl.from_time', $date)
            ->groupBy('tl.operator_name')
            ->select('tl.operator_name as op');
            
            $spot1=DB::connection('mysql_mes')->table('job_ticket as jt')->whereIn('production_order', $pluck_pro)
            ->join('spotwelding_qty as spot', 'spot.job_ticket_id', 'jt.job_ticket_id')
            // ->whereDate('spot.from_time', $date)
            ->groupBy('spot.operator_name')
            ->select('spot.operator_name as op');
            
            $spot2=DB::connection('mysql_mes')->table('job_ticket as jt')->whereIn('production_order', $pluck_pro)
            ->join('spotwelding_qty as spot', 'spot.job_ticket_id', 'jt.job_ticket_id')
            ->join('helper', 'spot.time_log_id', 'helper.time_log_id')
            // ->whereDate('spot.from_time', $date)
            ->select('helper.operator_name as op')
            ->distinct('op')
            ->groupBy('op');

            $job_ticket=DB::connection('mysql_mes')->table('job_ticket as jt')->whereIn('production_order', $pluck_pro)
            ->join('time_logs as tl', 'tl.job_ticket_id', 'jt.job_ticket_id')
            ->join('helper', 'tl.time_log_id', 'helper.time_log_id')
            // ->whereDate('tl.from_time', $date)
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
                $reg_shift= DB::connection('mysql_mes')->table('shift')->where('shift.operation_id', 1)->where('shift.shift_type', "Regular Shift")->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();
                $reg_hours = $reg_shift->hrs_of_work;
            }
            if($over_time_shift != null){
                $overtime_hour = $over_time_shift->hrs_of_work; 
            }
            if(collect($production_order)->sum('produced_qty') == 0){
                $produced_qty=0;
            }else{
                $produced_qty=collect($production_order)->sum('produced_qty');
            }
            if(collect($production_order)->sum('qty_to_manufacture') == 0){
                $output_qty=1;
            }else{
                $output_qty= collect($production_order)->sum('qty_to_manufacture');

            }
            if($this->overallStatus($date) == "Sunday" || $this->overallStatus($date) == "Holiday"){
                if($overtime_hour == 0){
                    $per_day_planned=0;
                    $per_day_produced=0;
                    $per_reg_shift=0;
                    $per_man_hr=0;
                    $per_realization_rate =0;

                }else{
                    $per_day_planned=collect($production_order)->sum('qty_to_manufacture');
                    $per_day_produced=collect($production_order)->sum('produced_qty');
                    $per_reg_shift=$reg_hours;
                    $per_man_hr=count($job_ticket);
                    $per_realization_rate = round(($produced_qty / $output_qty)*100, 2);
                }
            }else{
                $per_day_planned=collect($production_order)->sum('qty_to_manufacture');
                $per_day_produced=collect($production_order)->sum('produced_qty');
                $per_reg_shift=$reg_hours;
                $per_man_hr=count($job_ticket);
                $per_realization_rate = round(($produced_qty / $output_qty)*100, 2);



                

            }
            $day[]=[
                'date' =>  date('d', strtotime($date)),
                'stat' => ($this->overallStatus($date) != null) ? $overtime_hour: null,
                'stat_sunday' => $this->overallStatus($date)
            ];
            $planned_qty[]=[
                'value'=>  $per_day_planned,
                'value_display'=> $per_day_planned,
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
            ];
            $produced[]=[
                'value'=> $per_day_produced,
                'value_display'=> $per_day_produced,
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
            ];
            $shift_overtimeshif[]=[
                'value'=> $overtime_hour,
                'value_display'=> $overtime_hour,
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
            ];

            $shift_regshif[]=[
                'value'=> $per_reg_shift,
                'value_display'=> $per_reg_shift,
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
            ];
            $total_man_power[]=[
                'value'=>  $per_man_hr,
                'value_display'=> $per_man_hr,
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,
            ];
            
            $total_realization_rate[]=[
                'value'=>  $per_realization_rate,
                'value_display'=>  $per_realization_rate.'%',
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,

            ];
            $avg_man_power[]=[
                'value'=>  ($produced_qty == 0) ? 0 : round((($overtime_hour + $per_reg_shift) *count($job_ticket))/(($produced_qty == 0)? 1 : $produced_qty), 2),
                'value_display'=>  ($produced_qty == 0) ? 0 : round((($overtime_hour + $per_reg_shift) *count($job_ticket))/(($produced_qty == 0)? 1 : $produced_qty), 2),
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,

            ];
            
            $man_hr_with_overtime[]=[
                'value'=>  count($this->get_operator_with_ot($date, $pluck_pro)),
                'value_display'=>  count($this->get_operator_with_ot($date, $pluck_pro)),
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($overtime_hour != 0) ? $overtime_hour: null,

            ];
        }
        // return $planned_qty;

        // return $planned_qty;
        $data2=[
            'data' =>$planned_qty,
            'row_name' =>"PLANNED QUANTITY",
            'total' => collect($planned_qty)->sum('value')
        ];
        $data3=[
            'data' =>$produced,
            'row_name' =>"TOTAL OUTPUT",
            'total' => collect($produced)->sum('value')
        ];
        $data4=[
            'data' =>$shift_overtimeshif,
            'row_name' =>"TOTAL HRS. OVERTIME",
            'total' => collect($shift_overtimeshif)->sum('value')
        ];
        $data5=[
            'data' =>$shift_regshif,
            'row_name' =>"TOTAL HRS. REGULAR TIME",
            'total' => collect($shift_regshif)->sum('value')
        ];
        $data5=[
            'data' =>$shift_regshif,
            'row_name' =>"TOTAL HRS. REGULAR TIME",
            'total' => collect($shift_regshif)->sum('value')
        ];
        $data6=[
            'data' =>$total_man_power,
            'row_name' =>"TOTAL MAN HR",
            'total' => collect($total_man_power)->sum('value')
        ];
        $data7=[
            'data' =>$total_realization_rate,
            'row_name' =>"REALIZATION RATE",
            'total' => collect($total_realization_rate)->where('stat',null)->count('value')
        ];
        $data8=[
            'data' =>$avg_man_power,
            'row_name' =>"AVERAGE MAN-HR UTILIZATION",
            'total' => collect($avg_man_power)->where('stat',null)->count('value')
        ];
        $data9=[
            'data' =>$man_hr_with_overtime,
            'row_name' =>"TOTAL MANPOWER W/ OVERTIME",
            'total' => collect($man_hr_with_overtime)->where('stat',null)->sum('value')
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
        // return $data;
        return view('tables.tbl_fab_daily_report', compact('data', "date_column", 'colspan_date'));
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
    public function attendance_in_logs($date){
        $production_order= DB::connection('mysql_mes')->table('production_order')
        ->join('job_ticket as jt', 'jt.production_order', 'production_order.production_order')
        ->join('time_logs as tl', 'tl.job_ticket_id', 'jt.job_ticket_id')
        ->whereDate('production_order.planned_start_date', $date)->where('production_order.operation_id', 1)->where('production_order.parts_category', 'SA - Housing')
        ->groupBy('tl.operator_name')->select('tl.operator_name')->get();
    }
    public function get_operator_with_ot($date, $prod){
        $shift_sched= DB::connection('mysql_mes')->table('shift_schedule')
        ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
        ->whereDate('shift_schedule.date', $date)
        ->where('shift.operation_id', 1)
        ->max('time_out');
        if(empty($shift_sched)){
            $shift_sched= DB::connection('mysql_mes')->table('shift')->where('operation_id',1)->where('shift_type', 'Regular Shift')->max('time_out');
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
    public function fabrication_daily_report_page(){
        return view('reports.fabrication_daily_report');
    }
    public function fabrication_daily_chart(Request $request){
        $now = Carbon::now();
        $to=$request->end_date;
        $from=$request->start_date;
        $period = CarbonPeriod::create($from, $to);
        $day=[];
        $hours=0;
        foreach ($period as $date) {
            $production_order= DB::connection('mysql_mes')->table('production_order')->whereDate('planned_start_date', $date)->where('operation_id', 1)->where('parts_category', 'SA - Housing')->get();
            $over_time_shift= DB::connection('mysql_mes')->table('shift_schedule')
            ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
            ->whereDate('shift_schedule.date', $date)->where('shift.operation_id', 1)->where('shift.shift_type', "Overtime Shift")->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();
            $special_shift= DB::connection('mysql_mes')->table('shift_schedule')
            ->join('shift', 'shift.shift_id', 'shift_schedule.shift_id')
            ->whereDate('shift_schedule.date', $date)->where('shift.operation_id', 1)->where('shift.shift_type', "Special Shift")->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();
           
            if($special_shift){
                $reg_hours = $special_shift->hrs_of_work; 
            }else{
                $reg_shift= DB::connection('mysql_mes')->table('shift')->where('shift.operation_id', 1)->where('shift.shift_type', "Regular Shift")->select('shift.time_in', 'shift.time_out', 'shift.hrs_of_work')->first();
                $reg_hours = $reg_shift->hrs_of_work;
            }
            if($over_time_shift){
                $hours = $over_time_shift->hrs_of_work; 
            }
            $day[]=[
                'date' =>  date('d', strtotime($date)),
                'stat' => ($this->overallStatus($date) != null) ? $hours: null,
                'stat_sunday' => $this->overallStatus($date)
            ];
            $planned_qty[]=[
                'value'=>  ($this->overallStatus($date) != null)? "0": collect($production_order)->sum('qty_to_manufacture'),
                'value_display'=>  ($this->overallStatus($date) != null)? "0": collect($production_order)->sum('qty_to_manufacture'),
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($hours != 0) ? $hours: null,
            ];
            $produced[]=[
                'value'=> ($this->overallStatus($date) != null)? "0": collect($production_order)->sum('produced_qty'),
                'value_display'=> ($this->overallStatus($date) != null)? "0": collect($production_order)->sum('produced_qty'),
                'stat' => $this->overallStatus($date),
                'stat_ot' => ($hours != 0) ? $hours: null,
            ];
            
            
           
        }
        // return $planned_qty;
        // $data2=[
        //     'data' =>$planned_qty,
        //     'row_name' =>"PLANNED QUANTITY",
        //     'total' => collect($planned_qty)->sum('value')
        // ];
        // $data3=[
        //     'data' =>$produced,
        //     'row_name' =>"TOTAL OUTPUT",
        //     'total' => collect($produced)->sum('value')
        // ];
        
    
        $planned_data= $planned_qty;
        $produce_data= $produced;


        $date_column= $day;
        $colspan_date = count($day);
        // return $date_column;
        return response()->json(['per_day' => $date_column, 'planned' => $planned_data, 'produced' => $produce_data]);

        // return view('tables.tbl_fab_daily_report', compact('data', "date_column", 'planned', 'produced'));
    }
}