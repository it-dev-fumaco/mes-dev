<?php namespace App\Traits;

use Auth;
use DB;
use Carbon\Carbon;

trait GeneralTrait
{
    public function update_job_ticket($job_ticket_id){
        // get job ticket detail
        $job_ticket_detail = DB::connection('mysql_mes')->table('job_ticket')
            ->join('production_order', 'production_order.production_order', 'job_ticket.production_order')
            ->where('job_ticket_id', $job_ticket_id)->select('job_ticket.*', 'production_order.qty_to_manufacture', 'production_order.status as production_order_status')->first();

        if(!$job_ticket_id){
            return 0;
        }

        $time_logs_table = ($job_ticket_detail->workstation == 'Spotwelding') ? 'spotwelding_qty' : 'time_logs';
        // get job_ticket operator time logs
        $logs = DB::connection('mysql_mes')->table($time_logs_table)->where('job_ticket_id', $job_ticket_id)->get();
        $logs = collect($logs);

        $total_good_spotwelding = DB::connection('mysql_mes')->table('spotwelding_qty')
			->where('job_ticket_id', $job_ticket_id)->selectRaw('SUM(good) as total_good')->groupBy('spotwelding_part_id')->get();

        // get total good, total reject, actual start and end date
        $total_good = ($job_ticket_detail->workstation == 'Spotwelding') ? $total_good_spotwelding->min('total_good') : $logs->sum('good');
        $total_reject = ($job_ticket_detail->workstation == 'Spotwelding') ? $job_ticket_detail->reject : $logs->sum('reject');
        $job_ticket_actual_start_date = $logs->min('from_time');
        $job_ticket_actual_end_date = $logs->min('to_time');

        // set job ticket status
        if($job_ticket_detail->qty_to_manufacture <= $total_good){
            $job_ticket_status = 'Completed';
        }else if(count($logs) > 0){
            $job_ticket_status = 'In Progress';
        }else{
            $job_ticket_status = $job_ticket_detail->status;
        }
        // update job ticket details
        $job_ticket_values = [
            'completed_qty' => $total_good,
            'good' => $total_good,
            'reject' => $total_reject,
            'status' => $job_ticket_status,
            'actual_start_date' => $job_ticket_actual_start_date,
            'actual_end_date' => $job_ticket_actual_end_date
        ];

        DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $job_ticket_id)->update($job_ticket_values);

        // update production order operation in ERP
        $actual_start = Carbon::parse($job_ticket_actual_start_date);
        $actual_end = Carbon::parse($job_ticket_actual_end_date);
        $operation_time = $actual_end->diffInSeconds($actual_start);
        $operation_time = $operation_time / 60;

        $production_order_operation_values = [
            'status' => ($job_ticket_detail->qty_to_manufacture <= $total_good) ? "Completed" : "Pending",
            'completed_qty' => $total_good,
            'actual_start_time' => $job_ticket_actual_start_date,
            'actual_end_time' => $job_ticket_actual_end_date,
            'actual_operation_time' => $operation_time,
        ];
        
        if ($job_ticket_detail->bom_operation_id) {
            DB::connection('mysql')->table('tabWork Order Operation')
                ->where('bom_operation_id', $job_ticket_detail->bom_operation_id)
                ->where('parent', $job_ticket_detail->production_order)->update($production_order_operation_values);
        } else {
            DB::connection('mysql')->table('tabWork Order Operation')
                ->where('parent', $job_ticket_detail->production_order)->where('workstation', $job_ticket_detail->workstation)
                ->where('process', $job_ticket_detail->process_id)->update($production_order_operation_values);
        }

        // get production order produced qty
        $produced_qty = DB::connection('mysql_mes')->table('job_ticket')
			->where('production_order', $job_ticket_detail->production_order)->min('completed_qty');

        // set production order status
        if($job_ticket_detail->qty_to_manufacture == $produced_qty){
            $production_order_status = 'Completed';
        }else if(count($logs) > 0){
            $production_order_status = 'In Progress';
        }else{
            $production_order_status = $job_ticket_detail->production_order_status;
        }
        // update production order status and produced qty
		DB::connection('mysql_mes')->table('production_order')
            ->where('production_order', $job_ticket_detail->production_order)->update(['produced_qty' => $produced_qty, 'status' => $production_order_status]);

        // update production order status in ERP
        $production_order_status = ($production_order_status == 'In Progress') ? 'In Process' : $production_order_status;
        if($production_order_status != 'Completed') {
            DB::connection('mysql')->table('tabWork Order')->where('name', $job_ticket_detail->production_order)->update(['status' => $production_order_status]);
        }

        // get job ticket actual start and end time 
        $production_order_logs = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $job_ticket_detail->production_order)->get();
        $actual_start_date = collect($production_order_logs)->min('actual_start_date');
        $actual_end_date = collect($production_order_logs)->max('actual_end_date');

        if(in_array($production_order_status, ['Completed'])){
            $values = [
                'actual_start_date' => $actual_start_date,
                'actual_end_date' => $actual_end_date
            ];
        }else{
            $values = [
                'actual_start_date' => $actual_start_date,
            ];
        }

        DB::connection('mysql_mes')->table('production_order')->where('production_order', $job_ticket_detail->production_order)
            ->update($values);

        DB::connection('mysql')->table('tabWork Order')->where('name', $job_ticket_detail->production_order)
            ->update($values);

        return 1;
    }

    public function update_production_order_produced_qty($production_order){
        // production order details 
        $production_order_details = DB::connection('mysql_mes')->table('production_order')
            ->where('production_order', $production_order)->first();
        // get in progress job ticket
        $logs = DB::connection('mysql_mes')->table('job_ticket')
            ->where('production_order', $production_order)
            ->whereIn('status', ['In Progress'])->get();
        $produced_qty = 0;
         // get production order produced qty
         $produced_qty += DB::connection('mysql_mes')->table('job_ticket')
            ->where('production_order', $production_order)->min('completed_qty');
        // set production order status
        if($production_order_details->qty_to_manufacture <= $produced_qty){
            $production_order_status = 'Completed';
        }else if(count($logs) > 0){
            $production_order_status = 'In Progress';
        }else{
            $production_order_status = $production_order_details->status;
        }
        // update production order status and produced qty
        DB::connection('mysql_mes')->table('production_order')
            ->where('production_order', $production_order)->update(['produced_qty' => $produced_qty, 'status' => $production_order_status]);

        // update production order status in ERP
        $production_order_status = ($production_order_status == 'In Progress') ? 'In Process' : $production_order_status;
        DB::connection('mysql')->table('tabWork Order')
            ->where('name', $production_order)
            ->update(['status' => $production_order_status]);

        // get job ticket actual start and end time 
        $production_order_logs = DB::connection('mysql_mes')->table('job_ticket')
            ->where('production_order', $production_order)->get();
        $actual_start_date = collect($production_order_logs)->min('actual_start_date');
        $actual_end_date = collect($production_order_logs)->max('actual_end_date');

        if(in_array($production_order_status, ['Completed'])){
            $values = [
                'actual_start_date' => $actual_start_date,
                'actual_end_date' => $actual_end_date
            ];
        }else{
            $values = [
                'actual_start_date' => $actual_start_date,
            ];
        }

        DB::connection('mysql_mes')->table('production_order')
            ->where('production_order', $production_order)
            ->update($values);

        DB::connection('mysql')->table('tabWork Order')
            ->where('name', $production_order)
            ->update($values);
    }

    public function get_user_permitted_operation(){
		$q = DB::connection('mysql_mes')->table('user')
			->join('operation', 'operation.operation_id', 'user.operation_id')
			->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
            ->where('user_access_id', Auth::user()->user_id)
            ->select('module', 'operation_name')
            ->get();
        
        return [
            'permitted_modules' => collect($q)->unique('module')->pluck('module')->toArray(),
            'permitted_operations' => collect($q)->unique('operation_name')->pluck('operation_name')->toArray(),
            'permitted_module_operation' => collect($q)->toArray()
        ];
    }

    public function compute_item_cycle_time($item_code, $qty){
        $total_qty = DB::connection('mysql_mes')->table('production_order')
            ->join('job_ticket', 'production_order.production_order', 'job_ticket.production_order')
            ->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
            ->where('production_order.status', 'Completed')
            ->where('production_order.item_code', $item_code)
            ->where('job_ticket.remarks', '!=', 'Override')
            ->groupBy('production_order.production_order')
            ->sum('qty_to_manufacture');

        $total_duration_in_hours_1 = DB::connection('mysql_mes')->table('production_order')
            ->join('job_ticket', 'production_order.production_order', 'job_ticket.production_order')
            ->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
            ->where('production_order.status', 'Completed')
            ->where('production_order.item_code', $item_code)
            ->where('job_ticket.remarks', '!=', 'Override')
            ->groupBy('production_order.production_order')
            ->sum('time_logs.duration');

        $total_duration_in_hours_2 = DB::connection('mysql_mes')->table('production_order')
            ->join('job_ticket', 'production_order.production_order', 'job_ticket.production_order')
            ->join('spotwelding_qty', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
            ->where('production_order.status', 'Completed')
            ->where('production_order.item_code', $item_code)
            ->where('job_ticket.remarks', '!=', 'Override')
            ->groupBy('production_order.production_order')
            ->sum('spotwelding_qty.duration');

        $total_duration_in_hours = $total_duration_in_hours_1 + $total_duration_in_hours_2;

        if ($total_qty > 0) {
            $cycle_time_in_seconds = ($total_duration_in_hours / $total_qty) * 3600;
            $cycle_time_in_seconds = $cycle_time_in_seconds * $qty;

            if ($cycle_time_in_seconds <= 5) {
                return '-';
            }

            $dur_hours = floor($cycle_time_in_seconds / 3600);
            $dur_minutes = floor(($cycle_time_in_seconds / 60) % 60);
            $dur_seconds = $cycle_time_in_seconds % 60;

            $dur_hours = ($dur_hours > 0) ? $dur_hours .'h' : null;
            $dur_minutes = ($dur_minutes > 0) ? $dur_minutes .'m' : null;
            $dur_seconds = ($dur_seconds > 0) ? $dur_seconds .'s' : null;

            return $dur_hours . ' '. $dur_minutes . ' ' . $dur_seconds;
        }

        return '-';
    }

    public function compute_item_cycle_time_seconds_format($item_code, $qty){
        $q = DB::connection('mysql_mes')->table('production_order')
            ->join('job_ticket', 'production_order.production_order', 'job_ticket.production_order')
            ->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
            ->where('production_order.status', 'Completed')
            ->where('production_order.item_code', $item_code)
            ->select('production_order.production_order', 'production_order.qty_to_manufacture', DB::raw('SUM(time_logs.duration) as duration'))
            ->groupBy('production_order.production_order', 'production_order.qty_to_manufacture')
            ->get();
        
        $total_qty = collect($q)->sum('qty_to_manufacture');
        $total_duration_in_hours = collect($q)->sum('duration');

        if ($total_qty > 0) {
            $cycle_time_in_seconds = ($total_duration_in_hours / $total_qty) * 3600;
            $cycle_time_in_seconds = $cycle_time_in_seconds * $qty;

            $dur_seconds = $cycle_time_in_seconds;

            return $dur_seconds;
        }

        return '0';
    }

    public function insert_production_scrap($production_order, $qty){
        try {
            $production_order_details = DB::connection('mysql_mes')->table('production_order')
                ->where('production_order', $production_order)->first();
            if (!$production_order_details) {
                return response()->json(['success' => 0, 'message' => 'Production Order ' . $production_order . ' not found.']);
            }

            $bom_scrap_details = DB::connection('mysql')->table('tabBOM Scrap Item')->where('parent', $production_order_details->bom_no)->first();
            if (!$bom_scrap_details) {
                return response()->json(['success' => 0, 'message' => 'BOM ' . $production_order_details->bom_no . ' not found.']);
            }

            $uom_details = DB::connection('mysql_mes')->table('uom')->where('uom_name', 'Kilogram')->first();
            if (!$uom_details) {
                return response()->json(['success' => 0, 'message' => 'UoM Kilogram not found.']);
            }

            $thickness = DB::connection('mysql')->table('tabItem Variant Attribute')
                ->where('parent', $bom_scrap_details->item_code)->where('attribute', 'like', '%thickness%')->first();

            if($thickness){
                $thickness = $thickness->attribute_value;

                $thickness = str_replace(' ', '', preg_replace("/[^0-9,.]/", "", ($thickness)));

                $material = strtok($bom_scrap_details->item_name, ' ');

                $scrap_qty = $qty * $bom_scrap_details->stock_qty;

                if($material == 'CRS'){
                    // get uom conversion
                    $uom_arr_1 = DB::connection('mysql_mes')->table('uom_conversion')->join('uom', 'uom.uom_id', 'uom_conversion.uom_id')
                        ->where('uom.uom_name', $bom_scrap_details->stock_uom)->pluck('uom_conversion_id')->toArray();

                    $uom_arr_2 = DB::connection('mysql_mes')->table('uom_conversion')
                        ->where('uom_id', $uom_details->uom_id)->pluck('uom_conversion_id')->toArray();

                    $uom_conversion_id = array_intersect($uom_arr_1, $uom_arr_2);

                    $uom_1_conversion_factor = DB::connection('mysql_mes')->table('uom_conversion')
                        ->where('uom_conversion_id', $uom_conversion_id[0])
                        ->where('uom_id', '!=', $uom_details->uom_id)->sum('conversion_factor');

                    $uom_2_conversion_factor = DB::connection('mysql_mes')->table('uom_conversion')
                        ->where('uom_conversion_id', $uom_conversion_id[0])
                        ->where('uom_id', $uom_details->uom_id)->sum('conversion_factor');

                    // calculate scrap qty
                    $conversion_factor = $uom_2_conversion_factor / $uom_1_conversion_factor;

                    $scrap_qty = $scrap_qty * $conversion_factor;

                    // get scrap id
                    $existing_scrap = DB::connection('mysql_mes')->table('scrap')
                        ->where('material', $material)->where('uom_id', $uom_details->uom_id)
                        ->where('thickness', $thickness)->first();

                    if ($existing_scrap) {
                        $scrap_qty = $scrap_qty + $existing_scrap->scrap_qty;
                        $values = [
                            'scrap_qty' => $scrap_qty,
                            'last_modified_by' => Auth::user()->employee_name,
                        ];

                        DB::connection('mysql_mes')->table('scrap')->where('scrap_id', $existing_scrap->scrap_id)->update($values);

                        $scrap_id = $existing_scrap->scrap_id;
                    }else{
                        $values = [
                            'uom_conversion_id' => $uom_conversion_id[0],
                            'uom_id' => $uom_details->uom_id,
                            'material' => $material,
                            'thickness' => $thickness,
                            'scrap_qty' => $scrap_qty,
                            'created_by' => Auth::user()->employee_name,
                        ];
        
                        $scrap_id = DB::connection('mysql_mes')->table('scrap')->insertGetId($values);
                    }

                    $existing_scrap_reference = DB::connection('mysql_mes')->table('scrap_reference')
                        ->where('reference_type', 'Production Order')->where('reference_id', $production_order)
                        ->where('scrap_id', $scrap_id)->first();

                    if ($existing_scrap_reference) {
                        $scrap_qty = $scrap_qty + $existing_scrap->scrap_qty;
                        $values = [
                            'scrap_qty' => $scrap_qty,
                            'last_modified_by' => Auth::user()->employee_name,
                        ];

                        DB::connection('mysql_mes')->table('scrap_reference')
                            ->where('scrap_id', $existing_scrap_reference->scrap_reference_id)->update($values);
                    }else{
                        $values = [
                            'reference_type' => 'Production Order',
                            'reference_id' => $production_order,
                            'uom_id' => $uom_details->uom_id,
                            'scrap_id' => $scrap_id,
                            'scrap_qty' => $scrap_qty,
                            'created_by' => Auth::user()->employee_name,
                        ];
        
                        DB::connection('mysql_mes')->table('scrap_reference')->insert($values);
                    }
                }
            }

            

            

        // if($material_type == 'ALUMINUM'){
        //     $material_density = 7.85 * 0.000001;
        // }

        // if($material_type == 'OPAL'){
        //     $material_density = 1.19 * 0.000001;
        // }

        // if($material_type == 'PRISMATIC'){
        //     $material_density = 1.1163 * 0.000001;
        // }

        // return $material_cubic_mm * $material_density;

            
        } catch (Exception $e) {

        }
    }

    public function get_operation_wip_warehouse($operation_id){
        $wip_wh = DB::connection('mysql_mes')->table('wip_setup')->where('operation_id', $operation_id)->first();
        if(!$wip_wh){
            return ['success' => 0, 'message'=> 'Work in Progress not found.'];
        }

        return ['success' => 1, 'message'=> $wip_wh->warehouse];
    }

    public function compute_item_cycle_time_per_process($item_code, $qty, $workstation, $process_id){
        $q = DB::connection('mysql_mes')->table('production_order')
            ->join('job_ticket', 'production_order.production_order', 'job_ticket.production_order')
            ->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
            ->where('production_order.status', 'Completed')
            ->where('job_ticket.process_id', $process_id)
            ->where('job_ticket.workstation', $workstation)
            ->where('production_order.item_code', $item_code)
            ->where('job_ticket.remarks', '!=', 'Override')
            ->select('production_order.production_order', 'production_order.qty_to_manufacture', DB::raw('SUM(time_logs.duration) as duration'))
            ->groupBy('production_order.production_order', 'production_order.qty_to_manufacture')
            ->get();

        if($workstation == 'Spotwelding'){
            $q = DB::connection('mysql_mes')->table('production_order')
                ->join('job_ticket', 'production_order.production_order', 'job_ticket.production_order')
                ->join('spotwelding_qty', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
                ->where('production_order.status', 'Completed')
                ->where('job_ticket.process_id', $process_id)
                ->where('job_ticket.workstation', $workstation)
                ->where('production_order.item_code', $item_code)
                ->where('job_ticket.remarks', '!=', 'Override')
                ->select('production_order.production_order', 'production_order.qty_to_manufacture', DB::raw('SUM(spotwelding_qty.duration) as duration'))
                ->groupBy('production_order.production_order', 'production_order.qty_to_manufacture')
                ->get();
        }
        
        $total_qty = collect($q)->sum('qty_to_manufacture');
        $total_duration_in_hours = collect($q)->sum('duration');

        if ($total_qty > 0) {
            $cycle_time_in_seconds = ($total_duration_in_hours / $total_qty) * 3600;
            $cycle_time_in_seconds = $cycle_time_in_seconds * $qty;

            if ($cycle_time_in_seconds <= 5) {
                return '-';
            }

            $dur_hours = floor($cycle_time_in_seconds / 3600);
            $dur_minutes = floor(($cycle_time_in_seconds / 60) % 60);
            $dur_seconds = $cycle_time_in_seconds % 60;

            $dur_hours = ($dur_hours > 0) ? $dur_hours .'h' : null;
            $dur_minutes = ($dur_minutes > 0) ? $dur_minutes .'m' : null;
            $dur_seconds = ($dur_seconds > 0) ? $dur_seconds .'s' : null;

            return $dur_hours . ' '. $dur_minutes . ' ' . $dur_seconds;
        }

        return '-';
    }

    public function count_duplicate_production_items_per_schedule($production_orders){
        $duplicates = array_count_values(array_column($production_orders, 'production_item'));
        $filtered = collect($duplicates)->filter(function ($value, $key) {
            return $value > 1;
        });

        return count($filtered);
    }

    public function material_status_stockentry($production_order, $stat, $manufacture, $feedback_qty, $produced){
        if (!in_array($stat, ['In Progress', 'Cancelled', 'Completed'])) {
            $is_transferred = DB::connection('mysql')->table('tabStock Entry')
                ->where('purpose', 'Material Transfer for Manufacture')
                ->where('work_order', $production_order)
                ->where('docstatus', 1)->first();

            if ($is_transferred) {
                $status = 'Material Issued';
            }else{
                $status = 'Material For Issue';
            }
        }
        
        if($stat == "In Progress"){
            $current_process = DB::connection('mysql_mes')->table('job_ticket')
                ->where('production_order', $production_order)->where('status', 'In Progress')
                ->orderBy('last_modified_at', 'desc')->select('workstation')->first();

            $status = $stat;
            if(!empty($current_process)){
                if($current_process->workstation != null){
                    $status = $current_process->workstation;
                }
            }
        }

        if ($stat == "Cancelled") {
            $status = 'Cancelled';
        }

        if ($stat == "Completed") {
            $status = 'Ready For Feedback';
        }

        if($feedback_qty > 0){
            $status = 'Partial Feedbacked';
        }

        if($manufacture <= $feedback_qty){
            $status = 'Feedbacked';
        }

        return $status;
    }

    public function get_breaktime($start, $end, $operation_id){
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);

        // get special shift id between start and end
        $shift_id = DB::connection('mysql_mes')->table('shift_schedule')
            ->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->distinct()->pluck('shift_id');

        if(count($shift_id) <= 0){
            // get regular shift
            $shift_id = DB::connection('mysql_mes')->table('shift')
                ->where('shift_type', 'Regular Shift')->where('operation_id', $operation_id)
                ->distinct()->pluck('shift_id');
        }

        // get sum of breaktime in mins
        return DB::connection('mysql_mes')->table('breaktime')
            ->whereBetween('time_from', [$start->format('H:i:s'), $end->format('H:i:s')])
            ->whereBetween('time_to', [$start->format('H:i:s'), $end->format('H:i:s')])
            ->whereIn('shift_id', $shift_id)->sum('breaktime_in_mins');
    }

    public function check_essex_connection(){
        if ($latency = \Ping::execute('https://10.0.0.5/')) {
            return [
                'response' => 1,
                'message' => 'Connected to ESSEX.'
            ];
        }

        return [
            'response' => 0,
            'message' => 'No connection.'
        ];
    }

    public function update_production_order_transferred_qty($production_order){
        DB::connection('mysql')->beginTransaction();
        try {
            $production_details = DB::connection('mysql')->table('tabWork Order')->where('name', $production_order)->first();

            // get production order qty to manufacture
            $production_req_qty = $production_details->qty;

            // get total materials transferred for manufacturing in production order's stock entries
            $transferred_for_manufacturing = DB::connection('mysql')->table('tabStock Entry')
                ->where('work_order', $production_order)->where('purpose', 'Material Transfer for Manufacture')
                ->where('docstatus', 1)->sum('fg_completed_qty');

            $transferred_for_manufacturing = ($transferred_for_manufacturing > $production_req_qty) ? $production_req_qty : $transferred_for_manufacturing;
            
            $values = [
                'material_transferred_for_manufacturing' => $transferred_for_manufacturing,
            ];

            if($production_details->status == 'Not Started'){
                $values = [
                    'material_transferred_for_manufacturing' => $transferred_for_manufacturing,
                    'status' => 'In Process'
                ];
            }

            DB::connection('mysql')->table('tabWork Order')->where('name', $production_order)->update($values);

            $production_order_items = DB::connection('mysql')->table('tabWork Order Item')->where('parent', $production_order)->get();
            foreach ($production_order_items as $row) {
                // get item code transferred_qty
                $transferred_qty = DB::connection('mysql')->table('tabStock Entry as ste')
                    ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                    ->where('ste.work_order', $production_order)->where('ste.purpose', 'Material Transfer for Manufacture')
                    ->where('ste.docstatus', 1)->where('sted.item_code', $row->item_code)->sum('sted.qty');

                DB::connection('mysql')->table('tabWork Order Item')
                    ->where('parent', $production_order)->where('item_code', $row->item_code)
                    ->update(['transferred_qty' => $transferred_qty]);
            }

            DB::connection('mysql')->commit();

            return ['status' => 1];
        } catch (Exception $e) {
            DB::connection('mysql')->rollback();
            return ['status' => 0];
        }
    }

    public function erp_change_code_validation($erp_reference_id, $item_code){
        $delivery_date_tbl= DB::connection('mysql_mes')->table('delivery_date')->where('erp_reference_id',$erp_reference_id)->first();
        $change_code[]=["match" => "" ];
        if($delivery_date_tbl){
            if($delivery_date_tbl->parent_item_code == $item_code){
                $change_code[]=[
                    "match" => "true"
                ];
            }else{
                $change_code[]=[ 
                    "match" => "false",
                    "original_item" => $delivery_date_tbl->parent_item_code,
                    'new_item' => $item_code
                ];
            }
        }
        return $change_code;
    }

    public function production_status_with_stockentry($production_order, $stat, $manufacture, $feedback_qty, $produced){
        
        $is_transferred = DB::connection('mysql')->table('tabStock Entry')
            ->where('purpose', 'Material Transfer for Manufacture')
            ->where('work_order', $production_order)
            ->where('docstatus', 1)->first();

        if ($is_transferred) {
            $status = 'Material Issued';
        }else{
            $status = 'Material For Issue';
        }
        if($stat == "In Progress"){
           $status = "In Progress";
        }
        if ($stat == "Cancelled") {
            $status = 'Cancelled';
        }
        if ($stat == "Completed") {
            $status = 'Ready For Feedback';
        }
        if($feedback_qty > 0){
            $status = 'Partial Feedbacked';

        }
        if($manufacture <= $feedback_qty){
            $status = 'Feedbacked';

        }

        return $status;
    }

    public function feedback_production_order_items($production_order, $qty_to_manufacture, $fg_completed_qty){
        $production_order_items_qry = DB::connection('mysql')->table('tabWork Order Item')
            ->where('parent', $production_order)
            ->where(function($q) {
                $q->where('item_alternative_for', 'new_item')
                ->orWhereNull('item_alternative_for');
            })
            ->orderBy('idx', 'asc')->get();

        $arr = [];
        foreach ($production_order_items_qry as $index => $row) {
            $item_required_qty = $row->required_qty;
            $item_required_qty += DB::connection('mysql')->table('tabWork Order Item')
                ->where('parent', $production_order)
                ->where('item_alternative_for', $row->item_code)
                ->whereNotNull('item_alternative_for')
                ->sum('required_qty');

            $consumed_qty = $row->consumed_qty;

            $balance_qty = ($row->transferred_qty - $consumed_qty);

            $remaining_required_qty = ($fg_completed_qty - $balance_qty);

            if($balance_qty <= 0 || $fg_completed_qty > $balance_qty){
                $alternative_items_qry = $this->get_alternative_items($production_order, $row->item_code, $remaining_required_qty);
            }else{
                $alternative_items_qry = [];
            }

            $qty_per_item = $item_required_qty / $qty_to_manufacture;
            $per_item = $qty_per_item * $fg_completed_qty;

            $required_qty = ($balance_qty > $per_item) ? $per_item : $balance_qty;

            foreach ($alternative_items_qry as $ai_row) {
                if ($ai_row['required_qty'] > 0) {
                    $arr[] = [
                        'item_code' => $ai_row['item_code'],
                        'item_name' => $ai_row['item_name'],
                        'description' => $ai_row['description'],
                        'stock_uom' => $ai_row['stock_uom'],
                        'required_qty' => $ai_row['required_qty'],
                        'transferred_qty' => $ai_row['transferred_qty'],
                        'consumed_qty' => $ai_row['consumed_qty'],
                        'balance_qty' => $ai_row['balance_qty'],
                    ];
                }
            }

            if($balance_qty > 0){
                $arr[] = [
                    'item_code' => $row->item_code,
                    'item_name' => $row->item_name,
                    'description' => $row->description,
                    'stock_uom' => $row->stock_uom,
                    'required_qty' => $required_qty,
                    'transferred_qty' => $row->transferred_qty,
                    'consumed_qty' => $consumed_qty,
                    'balance_qty' => $balance_qty,
                ];
            }
        }

        return $arr;
    }

    public function get_alternative_items($production_order, $item_code, $remaining_required_qty){
        $q = DB::connection('mysql')->table('tabWork Order Item')
			->where('parent', $production_order)->where('item_alternative_for', $item_code)
            ->orderBy('required_qty', 'asc')->get();

        $remaining = $remaining_required_qty;
        $arr = [];
        foreach ($q as $row) {
            if($remaining > 0){
                $consumed_qty = $row->consumed_qty;

                $balance_qty = ($row->transferred_qty - $consumed_qty);
                    
                $required_qty = ($balance_qty > $remaining) ? $remaining : $balance_qty;
                $arr[] = [
                    'item_code' => $row->item_code,
                    'required_qty' => $required_qty,
                    'item_name' => $row->item_name,
                    'description' => $row->description,
                    'stock_uom' => $row->stock_uom,
                    'transferred_qty' => $row->transferred_qty,
                    'consumed_qty' => $consumed_qty,
                    'balance_qty' => $balance_qty
                ];

                $remaining = $remaining - $balance_qty;
            }
        }

        return $arr;
    }

    public function insert_job_card($production_order){
        $existing_jcs = DB::connection('mysql')->table('tabJob Card')->where('work_order', $production_order)->where('docstatus', 0)->pluck('operation_id');

        $work_order_operations = DB::connection('mysql')->table('tabWork Order as w')
            ->join('tabWork Order Operation as wo', 'w.name', 'wo.parent')
            ->where('w.name', $production_order)->whereNotIn('wo.name', $existing_jcs)
            ->select('wo.name as operation_id', 'w.*', 'wo.*', 'w.name as work_order')->get();

        $latest_jc = DB::connection('mysql')->table('tabJob Card')->where('name', 'like', '%MPO-JOB%')->max('name');
        $new_id = preg_replace("/[^0-9]/", "", $latest_jc);
        
        $now = Carbon::now();
        $job_cards = [];
        foreach($work_order_operations as $row) {
            $new_id = $new_id + 1;
            $new_jc_id = str_pad($new_id, 5, '0', STR_PAD_LEFT);
            $jc_name = 'MPO-JOB' . $new_jc_id;
            $job_cards[] = [
                'name' => $jc_name,
                'creation' => $now->toDateTimeString(),
                'modified' => $now->toDateTimeString(),
                'modified_by' => Auth::user()->email,
                'owner' => Auth::user()->email,
                'docstatus' => 0,
                'parent' => null,
                'parentfield' => null,
                'parenttype' => null,
                'idx' => 0,
                'naming_series' => 'PO-JOB.#####',
                'work_order' => $row->work_order,
                'bom_no' => $row->bom_no,
                'posting_date' => $now->format('Y-m-d'),
                'company' => 'FUMACO Inc.',
                'production_item' => $row->production_item,
                'item_name' => $row->item_name,
                'for_quantity' => $row->qty,
                'serial_no' => null,
                'wip_warehouse' => $row->wip_warehouse,
                'quality_inspection' => null,
                'project' => $row->project,
                'batch_no' => null,
                'operation' => $row->operation,
                'operation_row_number' => null,
                'workstation' => $row->workstation,
                'total_completed_qty' => 0,
                'total_time_in_mins' => 0,
                'for_job_card' => null,
                'is_corrective_job_card' => 0,
                'hour_rate' => 0,
                'for_operation' => null,
                'operation_id' => $row->operation_id,
                'sequence_id' => 0,
                'transferred_qty' => 0,
                'requested_qty' => 0,
                'status' => 'Open',
                'remarks' => 'MES Generated',
                'barcode' => null,
                'job_started' => 0,
                'started_time' => null,
                'current_time' => 0,
                'amended_from' => null,
                '_user_tags' => null,
                '_comments' => null,
                '_assign' => null,
                '_liked_by' => null,
            ];
        }

        DB::connection()->table('tabJob Card')->insert($job_cards);
    }

    public function update_job_card($production_order) {
        // get production order operation id
        $work_order_operations = DB::connection('mysql')->table('tabWork Order Operation')->where('parent', $production_order)->pluck('name');
        // query production order job cards
        $job_cards = DB::connection('mysql')->table('tabJob Card')->where('work_order', $production_order)
            ->whereNotIn('operation_id', $work_order_operations)->where('docstatus', 0)->get();
        foreach ($job_cards as $jc) {
            // get job card logs
            $has_existing_logs = DB::connection('mysql')->table('tabJob Card Time Log')
                ->where('parent', $jc->name)->exists();
            // delete not existing work order operation in job card (pending only)
            if (!$has_existing_logs) {
               DB::connection('mysql')->table('tabJob Card')->where('name', $jc->name)->where('docstatus', 0)->delete();
            }
        }
        
        $this->insert_job_card($production_order);
    }

    public function update_job_card_status($job_card_id) {
        $job_card = DB::connection('mysql')->table('tabJob Card')->where('name', $job_card_id)->first();
        if ($job_card) {
            $job_card_time_log = DB::connection('mysql')->table('tabJob Card Time Log')->where('parent', $job_card_id)->get();
            $total_completed_qty = collect($job_card_time_log)->sum('completed_qty');
            $total_time_in_mins = collect($job_card_time_log)->sum('time_in_mins');
            $status = 'Open';
            if (count($job_card_time_log) > 0) {
                if ($total_completed_qty == $job_card->for_quantity) {
                    $status = 'Completed';

                    $val = [
                        'docstatus' => 1,
                        'modified_by' => Auth::user()->employee_name,
                        'modified' => Carbon::now()->toDateTimeString(),
                    ];

                    DB::connection('mysql')->table('tabJob Card')->where('name', $job_card_id)->update($val);
                    DB::connection('mysql')->table('tabJob Card Time Log')->where('parent', $job_card_id)->update($val);
                }

                if ($status != 'Completed') {
                    $in_progress = collect($job_card_time_log)->where('to_time', null)->count();
                    if ($in_progress > 0) {
                        $status = 'Work In Progress';
                    } else {
                        $status = 'On Hold';
                    }
                }
            }

            DB::connection('mysql')->table('tabJob Card')->where('name', $job_card_id)->update([
                'status' => $status,
                'total_completed_qty' => $total_completed_qty,
                'total_time_in_mins' => $total_time_in_mins,
            ]);
        }
    }
}