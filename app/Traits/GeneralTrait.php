<?php namespace App\Traits;

use Auth;
use DB;
use Carbon\Carbon;

trait GeneralTrait
{
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

        // $q = DB::connection('mysql_mes')->table('production_order')
        //     ->join('job_ticket', 'production_order.production_order', 'job_ticket.production_order')
        //     ->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
        //     ->where('production_order.status', 'Completed')
        //     ->where('production_order.item_code', $item_code)
        //     ->where('job_ticket.remarks', '!=', 'Override')
        //     ->select('production_order.production_order', 'production_order.qty_to_manufacture', DB::raw('SUM(time_logs.duration) as duration'))
        //     ->groupBy('production_order.production_order', 'production_order.qty_to_manufacture')
        //     ->get();
        
        // $total_qty = collect($q)->sum('qty_to_manufacture');
        // $total_duration_in_hours = collect($q)->sum('duration');

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
        
        $is_transferred = DB::connection('mysql')->table('tabStock Entry')
            ->where('purpose', 'Material Transfer for Manufacture')
            ->where('production_order', $production_order)
            ->where('docstatus', 1)->first();

        if ($is_transferred) {
            $status = 'Material Issued';
        }else{
            $status = 'Material For Issue';
        }

        if($stat == "In Progress"){
            $current_process=DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $production_order)->where('status', 'In Progress')->orderBy('last_modified_at', 'desc')->select('workstation')->first();
            if(!empty($current_process)){
                if($current_process->workstation != null){
                    $status = $current_process->workstation;
                }
            }
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

    public function update_production_actual_start_end($production_order){
		$q = DB::connection('mysql_mes')->table('job_ticket as jt')
			->join('time_logs as tl', 'jt.job_ticket_id', 'tl.job_ticket_id')
			->where('jt.production_order', $production_order)
			->where('jt.workstation', '!=', 'Spotwelding')
			->select('workstation', 'tl.from_time', 'tl.to_time');

		$q = DB::connection('mysql_mes')->table('job_ticket as jt')
			->join('spotwelding_qty as tl', 'jt.job_ticket_id', 'tl.job_ticket_id')
			->where('jt.production_order', $production_order)
			->select('workstation', 'tl.from_time', 'tl.to_time')
			->union($q)->get();
		
		// get time logs min start time
		$actual_start_date = collect($q)->min('from_time');
		// get item logs max end time
		$actual_end_date = collect($q)->max('to_time');

		DB::connection('mysql_mes')->table('production_order')
			->where('production_order', $production_order)->whereNotIn('status', ['Completed', 'Cancelled'])
			->update(['actual_start_date' => $actual_start_date, 'actual_end_date' => $actual_end_date]);
    }
    
    public function update_production_order_transferred_qty($production_order){
        DB::connection('mysql')->beginTransaction();
        try {
            $production_details = DB::connection('mysql')->table('tabProduction Order')->where('name', $production_order)->first();

            // get production order qty to manufacture
            $production_req_qty = $production_details->qty;

            // get total materials transferred for manufacturing in production order's stock entries
            $transferred_for_manufacturing = DB::connection('mysql')->table('tabStock Entry')
                ->where('production_order', $production_order)->where('purpose', 'Material Transfer for Manufacture')
                ->where('docstatus', 1)->sum('fg_completed_qty');

            $values = [
                'material_transferred_for_manufacturing' => $transferred_for_manufacturing,
            ];
            
            if($transferred_for_manufacturing > $production_req_qty){
                $transferred_for_manufacturing = $production_req_qty;
                $values = [
                    'material_transferred_for_manufacturing' => $transferred_for_manufacturing,
                ];
            }

            if($production_details->status == 'Not Started'){
                $values = [
                    'material_transferred_for_manufacturing' => $transferred_for_manufacturing,
                    'status' => 'In Process'
                ];
            }

            DB::connection('mysql')->table('tabProduction Order')->where('name', $production_order)->update($values);

            $production_order_items = DB::connection('mysql')->table('tabProduction Order Item')->where('parent', $production_order)->get();
            foreach ($production_order_items as $row) {
                // get item code transferred_qty
                $transferred_qty = DB::connection('mysql')->table('tabStock Entry as ste')
                    ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                    ->where('ste.production_order', $production_order)->where('ste.purpose', 'Material Transfer for Manufacture')
                    ->where('ste.docstatus', 1)->where('sted.item_code', $row->item_code)->sum('sted.qty');

                DB::connection('mysql')->table('tabProduction Order Item')
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
}