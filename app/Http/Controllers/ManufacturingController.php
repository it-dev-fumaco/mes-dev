<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Auth;
use DB;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail_feedbacking;
use App\Traits\GeneralTrait;

class ManufacturingController extends Controller
{
    use GeneralTrait;

    public function wizard(){
        $permissions = $this->get_user_permitted_operation();

        $mes_user_operations = DB::connection('mysql_mes')->table('user')
					->join('operation', 'operation.operation_id', 'user.operation_id')
					->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
                    ->where('module', 'Production')
                    ->where('user_access_id', Auth::user()->user_id)->pluck('operation_name')->toArray();

        $module_permissions = collect($permissions['permitted_module_operation'])
            ->where('module', 'Production')->pluck('operation_name')->toArray();

        if(in_array('Fabrication', $module_permissions)){
            return view('wizard.index', compact('permissions', 'module_permissions', 'mes_user_operations'));
        }elseif(in_array('Wiring and Assembly', $module_permissions)){
            return redirect('/assembly/wizard');
        }else{
            return redirect('/');
        }
    }  

    public function get_warehouses($operation, $item_classification){
        $operation_details = DB::connection('mysql_mes')->table('operation')->where('operation_name', $operation)->first();
        if($operation_details){
            return DB::connection('mysql_mes')->table('item_classification_warehouse')
                ->select('warehouse')->distinct()->get();
        }

        return [];
    }

    public function get_material_request_details($id){
        try {
            $mr = DB::connection('mysql')->table('tabMaterial Request')->where('name', $id)->first();
            if (!$mr) {
                return response()->json(['message' => 'Material Request <b>' . $id . '</b> not found.']);
            }

            $so_items = DB::connection('mysql')->table('tabMaterial Request Item')->where('parent', $id)
                ->select(DB::raw('parent, (qty - ordered_qty) as pending_qty'), 'item_code', 'warehouse', 'description', 'uom', 'qty', 'idx', 'ordered_qty', 'name', 'schedule_date as delivery_date')->orderBy('idx', 'asc')->get();

            $item_list = [];
            foreach ($so_items as $item) {
                $item_details = DB::connection('mysql')->table('tabItem')->where('name', $item->item_code)->first();
                $bom = DB::connection('mysql')->table('tabBOM')->where('item', $item->item_code)->where('docstatus', '<', 2)->select('name', 'is_default', 'rf_drawing_no')->orderBy('modified', 'desc')->get();

                $stock = DB::connection('mysql')->table('tabBin')->where('item_code', $item->item_code)->where('warehouse', $item->warehouse)->sum('actual_qty');

                $default_bom = collect($bom)->where('is_default', 1)->first();
                $default_bom_no = ($default_bom) ? $default_bom->name : '-- No BOM --';
                $rfd_no = ($default_bom) ? $default_bom->rf_drawing_no : null;

                $item_list[] = [
                    'id' => $item->name,
                    'idx' => $item->idx,
                    'sales_order' => $item->parent,
                    'item_code' => $item->item_code,
                    'description' => $item->description,
                    'ordered_qty' => $item->ordered_qty,
                    'item_classification' => $item_details->item_classification,
                    'uom' => $item->uom,
                    'qty' => $item->pending_qty,
                    'bom' => $default_bom_no,
                    'bom_list' => $bom,
                    'rfd_no' => $rfd_no,
                    'stock' => $stock,
                    'delivery_date' => $item->delivery_date
                ];
            }

            return view('wizard.tbl_mreq_details', compact('mr', 'item_list'));
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function get_sales_order_details($id){
        try {
            $so = DB::connection('mysql')->table('tabSales Order')->where('name', $id)->first();
            if (!$so) {
                return response()->json(['message' => 'Sales Order <b>' . $id . '</b> not found.']);
            }

            $so_items =  DB::connection('mysql')->table('tabSales Order Item')->where('parent', $id)
                ->select(DB::raw('parent, (qty - delivered_qty) as pending_qty'), 'item_code', 'warehouse', 'description', 'uom', 'qty', 'idx', 'item_classification', 'delivered_qty', 'name', 'delivery_date')->orderBy('idx', 'asc')->get();

            $item_list = [];
            foreach ($so_items as $item) {
                $bom = DB::connection('mysql')->table('tabBOM')->where('item', $item->item_code)->where('docstatus', '<', 2)->select('name', 'is_default', 'rf_drawing_no')->orderBy('modified', 'desc')->get();

                $stock = DB::connection('mysql')->table('tabBin')->where('item_code', $item->item_code)->where('warehouse', $item->warehouse)->sum('actual_qty');

                $default_bom = collect($bom)->where('is_default', 1)->first();
                $default_bom_no = ($default_bom) ? $default_bom->name : '-- No BOM --';
                $rfd_no = ($default_bom) ? $default_bom->rf_drawing_no : null;
                $delivery_date_tbl= DB::connection('mysql_mes')->table('delivery_date')->where('erp_reference_id', $item->name)->first();
                $match= "";
                $new_code= "";
                $origl_code= "";
                if($delivery_date_tbl){
                    if($delivery_date_tbl->parent_item_code == $item->item_code){
                        $match= "true";
                       
                    }else{
                        $match = "false";
                        $origl_code = $delivery_date_tbl->parent_item_code;
                        $new_code= $item->item_code;
                    }
                }
                $item_list[] = [
                    'id' => $item->name,
                    'idx' => $item->idx,
                    'sales_order' => $item->parent,
                    'item_code' => $item->item_code,
                    'description' => $item->description,
                    'delivered_qty' => $item->delivered_qty,
                    'item_classification' => $item->item_classification,
                    'uom' => $item->uom,
                    'qty' => $item->pending_qty,
                    'bom' => $default_bom_no,
                    'bom_list' => $bom,
                    'rfd_no' => $rfd_no,
                    'stock' => $stock,
                    'delivery_date' => $item->delivery_date,
                    'match'=> $match,
                    'origl_code' => $origl_code,
                    'new_code' => $new_code
                ];
            }

            return view('wizard.tbl_so_details', compact('so', 'item_list'));
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }
    public function view_bom($bom){
        $bom = $this->get_bom($bom);

        return view('wizard.tbl_bom', compact('bom'));
    }

    public function get_bom($bom){
        try {
            $bom = DB::connection('mysql')->table('tabBOM Item')->where('docstatus', '<', 2)->where('parent', $bom)->orderBy('idx', 'asc')->get();

            $materials = [];
            foreach ($bom as $item) {
                $default_bom = DB::connection('mysql')->table('tabBOM')->where('docstatus', '<', 2)
                    ->where('is_default', 1)->where('item', $item->item_code)->first();

                $item_details = DB::connection('mysql')->table('tabItem')->where('name', $item->item_code)->first();
                $item_description = ($item_details) ? $item_details->description : '';
                $item_classification = ($item_details) ? $item_details->item_classification : '';
                $item_group = ($item_details) ? $item_details->item_group : '';
                $child_bom = ($default_bom) ? $default_bom->name : $item->bom_no;

                // $bom_operation = DB::connection('mysql')->table('tabBOM Operation')->where('parent', $child_bom)->first();
                // $operation_name = ($bom_operation) ? $bom_operation->operation : null;

                $materials[] = [
                    'item_code' => $item->item_code,
                    'description' => $item_description,
                    'item_classification' => $item_classification,
                    'item_group' => $item_group,
                    'qty' => $item->qty,
                    'bom_no' => $child_bom,
                    'uom' => $item->uom,
                    // 'operation_name' => $operation_name,
                    'child_nodes' => $this->get_bom($child_bom)
                ];
            }

            return $materials;
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function get_production_req_items(Request $request){
        try {
            $items = DB::table('tabProduction Order Item')->whereIn('parent', $request->production_orders)
                ->orderBy('parent', 'asc')->orderBy('idx', 'asc')->get();

            $req_items = [];
            foreach ($items as $item) {
                $prod = DB::table('tabProduction Order')->where('name', $item->parent)->first();

                $arr = ['length', 'width', 'height', 'thickness', 'cutting size'];

                $item_attr = DB::connection('mysql')->table('tabItem Variant Attribute')
                ->where('parent', $prod->production_item)
                    ->Where(function ($q) use ($arr) {
                        for ($i = 0; $i < count($arr); $i++){
                            $q->orWhere('attribute', 'like',  '%' . $arr[$i] .'%');
                        }      
                })->select(DB::raw("GROUP_CONCAT(attribute_value ORDER BY idx ASC SEPARATOR ' x ') AS attr"))
                ->first();

                $mr = DB::table('tabMaterial Request Item')->where('docstatus', 1)->where('item_code', $item->item_code)
                    ->where('sales_order', $prod->sales_order_no)->first();

                $item_details = DB::table('tabItem')->where('name', $item->item_code)->first();

                $ste = DB::table('tabStock Entry AS se')->join('tabStock Entry Detail AS sed', 'se.name', 'sed.parent')
                    ->where('se.docstatus', '<', 2)->where('se.production_order', $item->parent)
                    ->where('se.sales_order_no', $prod->sales_order_no)
                    ->where('sed.item_code', $item->item_code)->select('se.*')->first();

                $warehouse = ($mr) ? $mr->warehouse : $item->source_warehouse;
                $qty_source_warehouse = $this->get_actual_qty($item->item_code, $warehouse);

                $item_cm = $this->calculate_item_cubic_mm($prod->production_item, $prod->qty);
                
                $sheets = $this->get_no_of_sheets($item->item_code, $item_cm);
                $raw_cm = $this->calculate_item_cubic_mm($item->item_code, $sheets);
                
                $current_stock_in_sheets = $this->get_no_of_sheets($item->item_code, $qty_source_warehouse);
                $balance_in_sheets = $this->get_no_of_sheets($item->item_code, ($qty_source_warehouse - $item->required_qty));

                $s_warehouses = $this->get_warehouses('Fabrication', $item_details->item_classification);

                $s_warehouses = array_column($s_warehouses->toArray(), 'warehouse');
                if(!in_array($warehouse, $s_warehouses)){
                    array_push($s_warehouses, $warehouse);
                }

                $projected_scrap = $raw_cm - $item_cm;

                $mes_prod = DB::connection('mysql_mes')->table('production_order')->where('production_order', $item->parent)->first();
                if($ste){
                    $projected_scrap = $mes_prod->projected_scrap;
                }

                $material = strtok($item->item_name, ' ');

                $projected_scrap_in_kg = $this->cubic_mm_to_kg($material, $projected_scrap);

                $available_scrap = $this->get_available_scrap($prod->production_item);

                $req_items[] = [
                    'production_order' => $item->parent,
                    'production_item' => $prod->production_item,
                    'production_item_description' => $prod->description,
                    'attr' => ($item_attr) ? $item_attr->attr : null,
                    'item_code' => $item->item_code,
                    'description' => $item->description,
                    'item_image' => $item_details->item_image_path,
                    'item_classification' => $item_details->item_classification,
                    'item_group' => $item_details->item_group,
                    'stock_uom' => $item->stock_uom,
                    'sales_order' => $prod->sales_order_no,
                    'material_request' => $prod->material_request,
                    'source_warehouse' => $warehouse,
                    'required_qty' => $item->required_qty,
                    'no_of_sheets' => ($item_details->item_classification == 'CS - Crs Steel Coil') ? $sheets .' Sheet(s)' : 'N/A',
                    'qty_source_warehouse' => $qty_source_warehouse,
                    'wip_warehouse' => $prod->wip_warehouse,
                    'balance_qty' => ($qty_source_warehouse - $item->required_qty),
                    'mreq' => ($mr) ? $mr->parent : null,
                    'ste' => ($ste) ? $ste->name : null,
                    's_warehouses' => $s_warehouses,
                    'current_stock_in_sheets' => ($item_details->item_classification == 'CS - Crs Steel Coil') ? $current_stock_in_sheets .' Sheet(s)' : 'N/A',
                    'balance_in_sheets' => ($item_details->item_classification == 'CS - Crs Steel Coil') ? $balance_in_sheets .' Sheet(s)' : 'N/A',
                    'projected_scrap' => ($item_details->item_classification == 'CS - Crs Steel Coil') ? $projected_scrap : null,
                    'projected_scrap_in_kg' => ($item_details->item_classification == 'CS - Crs Steel Coil') ? $projected_scrap_in_kg .' Kg' : 'N/A',
                    'available_scrap_count' => collect($available_scrap)->sum('usable_scrap_qty'),
                ];
            }

            $url = $request->fullUrl();

            return view('wizard.tbl_req_items', compact('req_items', 'url'));
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function get_parts(Request $request){
        try {
            $not_allowed_item_classification = ['RA - REFLECTOR ASSEMBLY', 'FA - FRAM1E ASSEMBLY'];
            $parts = [];
            foreach ($request->bom as $idx => $bom) {
                $item_reference_id = $request->item_reference_id[$idx];
                $delivery_date = $request->delivery_date[$idx];
                
                // $bom_parts = [];
                $bom_details = DB::connection('mysql')->table('tabBOM')->where('name', $bom)->first();

                // $bom_operation = DB::connection('mysql')->table('tabBOM Operation')->where('parent', $bom)->first();
                // $operation_name = ($bom_operation) ? $bom_operation->operation : null;

                $item_details = DB::connection('mysql')->table('tabItem')->where('name', $bom_details->item)->first();

                $item_description = ($item_details) ? $item_details->description : '';
                $item_classification = ($item_details) ? $item_details->item_classification : '';
                $item_group = ($item_details) ? $item_details->item_group : '';

                // $bom_parts[] = [
                //     'item_code' => $bom_details->item,
                //     'description' => $item_description,
                //     'item_classification' => $item_classification,
                //     'item_group' => $item_group,
                //     'qty' => $bom_details->quantity,
                //     'bom_no' => $bom_details->name,
                //     'uom' => $bom_details->uom,
                //     // 'operation_name' => $operation_name,
                //     'child_nodes' => $this->get_bom($bom),
                // ];

                $bom_parts = $this->get_bom($bom);

                $reference_pref = preg_replace('/[0-9]+/', null, $request->so[$idx]);
                $reference_pref = str_replace("-", "", $reference_pref);
                foreach ($bom_parts as $parent_part) {
                    $reference_no = $request->so[$idx];
                    if(!in_array($parent_part['item_classification'], $not_allowed_item_classification) && !in_array($parent_part['item_group'], ['Raw Material', 'Factory Supplies'])){
                        $parent_default_bom = DB::connection('mysql')->table('tabBOM')->where('docstatus', '<', 2)
                            ->where('is_default', 1)->where('item', $parent_part['item_code'])->first();

                        $available_stock_parent = DB::connection('mysql_mes')->table('fabrication_inventory')
                            ->where('item_code', $parent_part['item_code'])->sum('balance_qty');

                        if ($parent_default_bom) {
                            $existing_prod1 = DB::connection('mysql')->table('tabProduction Order')
                                ->where('docstatus', 1)->where('company', 'FUMACO Inc.')
                                ->when($reference_pref == 'SO', function ($query) use ($reference_no){
                                    return $query->where('sales_order_no', $reference_no);
                                })
                                ->when($reference_pref == 'MREQ', function ($query) use ($reference_no){
                                    return $query->where('material_request', $reference_no);
                                })
                                ->where('production_item', $parent_part['item_code'])
                                ->where('parent_item_code', $bom_details->item)
                                // ->where('qty', $parent_part['qty'] * $request->qty[$idx])
                                ->first();

                            $s_warehouse = null;
                            if ($existing_prod1) {
                                $planned_start_date1 = Carbon::parse($existing_prod1->planned_start_date)->format('Y-m-d');
                                $s_warehouse = DB::connection('mysql')->table('tabProduction Order Item')->where('parent', $existing_prod1->name)->first()->source_warehouse;
                            }

                            $parts[] = [
                                'item_reference_id' => $item_reference_id,
	                            'delivery_date' => $delivery_date,
                                'parent_item' => $bom_details->item,
                                'sub_parent_item' => $bom_details->item,
                                'item_code' => $parent_part['item_code'],
                                'description' => $parent_part['description'],
                                'item_classification' => $parent_part['item_classification'],
                                'bom' => $parent_default_bom->name,
                                'bom_reviewed' => $parent_default_bom->is_reviewed,
                                'planned_qty' => $parent_part['qty'] * $request->qty[$idx],
                                'reference_no' => $reference_no,
                                'planned_start_date' => ($existing_prod1) ? $planned_start_date1 : null,
                                'production_order' => ($existing_prod1) ? $existing_prod1->name : null,
                                's_warehouse' => $s_warehouse,
                                'wip_warehouse' => ($existing_prod1) ? $existing_prod1->wip_warehouse : null,
                                'fg_warehouse' => ($existing_prod1) ? $existing_prod1->fg_warehouse : null,
                                'available_stock' => $available_stock_parent,
                                'cycle_time' => $this->compute_item_cycle_time($parent_part['item_code'], $parent_part['qty'] * $request->qty[$idx]),
                                // 'operation_name' => $parent_part['operation_name']
                            ];
                        }else{
                            $parts[] = [
                                'item_reference_id' => $item_reference_id,
	                            'delivery_date' => $delivery_date,
                                'parent_item' => $bom_details->item,
                                'sub_parent_item' => $bom_details->item,
                                'item_code' => $parent_part['item_code'],
                                'description' => $parent_part['description'],
                                'item_classification' => $parent_part['item_classification'],
                                'bom' => null,
                                'bom_reviewed' => 0,
                                'planned_qty' => $parent_part['qty'] * $request->qty[$idx],
                                'reference_no' => $reference_no,
                                'planned_start_date' => null,
                                'production_order' => null,
                                's_warehouse' => null,
                                'wip_warehouse' => null,
                                'fg_warehouse' => null,
                                'available_stock' => $available_stock_parent,
                                'cycle_time' => $this->compute_item_cycle_time($parent_part['item_code'], $parent_part['qty'] * $request->qty[$idx]),
                                // 'operation_name' => $parent_part['operation_name']
                            ];
                        }

                    }
                    

                    foreach ($parent_part['child_nodes'] as $child_part) {
                        $child_default_bom = DB::connection('mysql')->table('tabBOM')->where('docstatus', '<', 2)
                            ->where('is_default', 1)->where('item', $child_part['item_code'])->first();

                        $available_stock_child = DB::connection('mysql_mes')->table('fabrication_inventory')
                            ->where('item_code', $child_part['item_code'])->sum('balance_qty');

                        if ($child_default_bom) {
                            $existing_prod2 = DB::connection('mysql')->table('tabProduction Order')
                                ->where('docstatus', 1)->where('company', 'FUMACO Inc.')
                                ->when($reference_pref == 'SO', function ($query) use ($reference_no){
                                    return $query->where('sales_order_no', $reference_no);
                                })
                                ->when($reference_pref == 'MREQ', function ($query) use ($reference_no){
                                    return $query->where('material_request', $reference_no);
                                })
                                ->where('production_item', $child_part['item_code'])
                                ->where('parent_item_code', $bom_details->item)
                                // ->where('qty', $child_part['qty'] * $request->qty[$idx])
                                ->first();

                            $s_warehouse = null;
                            if ($existing_prod2) {
                                $planned_start_date2 = Carbon::parse($existing_prod2->planned_start_date)->format('Y-m-d');
                                $s_warehouse = DB::connection('mysql')->table('tabProduction Order Item')->where('parent', $existing_prod2->name)->first()->source_warehouse;
                            }
                    
                            $parts[] = [
                                'item_reference_id' => $item_reference_id,
	                            'delivery_date' => $delivery_date,
                                'parent_item' => $bom_details->item,
                                'sub_parent_item' => $parent_part['item_code'],
                                'item_code' => $child_part['item_code'],
                                'description' => $child_part['description'],
                                'item_classification' => $child_part['item_classification'],
                                'bom' => $child_default_bom->name,
                                'bom_reviewed' => $child_default_bom->is_reviewed,
                                'planned_qty' => $child_part['qty'] * $request->qty[$idx],
                                'reference_no' => $reference_no,
                                'planned_start_date' => ($existing_prod2) ? $planned_start_date2 : null,
                                'production_order' => ($existing_prod2) ? $existing_prod2->name : null,
                                's_warehouse' => $s_warehouse,
                                'wip_warehouse' => ($existing_prod2) ? $existing_prod2->wip_warehouse : null,
                                'fg_warehouse' => ($existing_prod2) ? $existing_prod2->fg_warehouse : null,
                                'available_stock' => $available_stock_child,
                                'cycle_time' => $this->compute_item_cycle_time($child_part['item_code'], $child_part['qty'] * $request->qty[$idx]),
                                // 'operation_name' => $child_part['operation_name']
                            ];
                        }

                        foreach ($child_part['child_nodes'] as $child_part2) {
                            $child_default_bom = DB::connection('mysql')->table('tabBOM')->where('docstatus', '<', 2)
                                ->where('is_default', 1)->where('item', $child_part2['item_code'])->first();
                            
                            $available_stock_child1 = DB::connection('mysql_mes')->table('fabrication_inventory')
                                ->where('item_code', $child_part2['item_code'])->sum('balance_qty');

                            if ($child_default_bom) {
                                $existing_prod3 = DB::connection('mysql')->table('tabProduction Order')
                                    ->where('docstatus', 1)->where('company', 'FUMACO Inc.')
                                    ->when($reference_pref == 'SO', function ($query) use ($reference_no){
                                        return $query->where('sales_order_no', $reference_no);
                                    })
                                    ->when($reference_pref == 'MREQ', function ($query) use ($reference_no){
                                        return $query->where('material_request', $reference_no);
                                    })
                                    ->where('production_item', $child_part2['item_code'])
                                    ->where('parent_item_code', $bom_details->item)
                                    // ->where('qty', $child_part2['qty'] * $request->qty[$idx])
                                    ->first();

                                $s_warehouse = null;
                                if ($existing_prod3) {
                                    $planned_start_date3 = Carbon::parse($existing_prod3->planned_start_date)->format('Y-m-d');
                                    $s_warehouse = DB::connection('mysql')->table('tabProduction Order Item')
                                        ->where('parent', $existing_prod3->name)->first()->source_warehouse;
                                }

                                $parts[] = [
                                    'item_reference_id' => $item_reference_id,
	                                'delivery_date' => $delivery_date,
                                    'parent_item' => $bom_details->item,
                                    'sub_parent_item' => $child_part['item_code'],
                                    'item_code' => $child_part2['item_code'],
                                    'description' => $child_part2['description'],
                                    'item_classification' => $child_part2['item_classification'],
                                    'bom' => $child_default_bom->name,
                                    'bom_reviewed' => $child_default_bom->is_reviewed,
                                    'planned_qty' => $child_part2['qty'] * $request->qty[$idx],
                                    'reference_no' => $request->so[$idx],
                                    'planned_start_date' => ($existing_prod3) ? $planned_start_date3 : null,
                                    'production_order' => ($existing_prod3) ? $existing_prod3->name : null,
                                    's_warehouse' => $s_warehouse,
                                    'wip_warehouse' => ($existing_prod3) ? $existing_prod3->wip_warehouse : null,
                                    'fg_warehouse' => ($existing_prod3) ? $existing_prod3->fg_warehouse : null,
                                    'available_stock' => $available_stock_child1,
                                    'cycle_time' => $this->compute_item_cycle_time($child_part2['item_code'], $child_part2['qty'] * $request->qty[$idx]),
                                    // 'operation_name' => $child_part2['operation_name']
                                ];
                            }

                            foreach ($child_part2['child_nodes'] as $child_part3) {
                                $child_default_bom = DB::connection('mysql')->table('tabBOM')->where('docstatus', '<', 2)
                                    ->where('is_default', 1)->where('item', $child_part3['item_code'])->first();
                                
                                $available_stock_child1 = DB::connection('mysql_mes')->table('fabrication_inventory')
                                    ->where('item_code', $child_part3['item_code'])->sum('balance_qty');

                                if ($child_default_bom) {
                                    $existing_prod3 = DB::connection('mysql')->table('tabProduction Order')
                                        ->where('docstatus', 1)->where('company', 'FUMACO Inc.')
                                        ->when($reference_pref == 'SO', function ($query) use ($reference_no){
                                            return $query->where('sales_order_no', $reference_no);
                                        })
                                        ->when($reference_pref == 'MREQ', function ($query) use ($reference_no){
                                            return $query->where('material_request', $reference_no);
                                        })
                                        ->where('production_item', $child_part3['item_code'])
                                        ->where('parent_item_code', $bom_details->item)
                                        // ->where('qty', $child_part2['qty'] * $request->qty[$idx])
                                        ->first();

                                    $s_warehouse = null;
                                    if ($existing_prod3) {
                                        $planned_start_date3 = Carbon::parse($existing_prod3->planned_start_date)->format('Y-m-d');
                                        $s_warehouse = DB::connection('mysql')->table('tabProduction Order Item')
                                            ->where('parent', $existing_prod3->name)->first()->source_warehouse;
                                    }

                                    $parts[] = [
                                        'item_reference_id' => $item_reference_id,
	                                    'delivery_date' => $delivery_date,
                                        'parent_item' => $bom_details->item,
                                        'sub_parent_item' => $child_part['item_code'],
                                        'item_code' => $child_part3['item_code'],
                                        'description' => $child_part3['description'],
                                        'item_classification' => $child_part3['item_classification'],
                                        'bom' => $child_default_bom->name,
                                        'bom_reviewed' => $child_default_bom->is_reviewed,
                                        'planned_qty' => $child_part3['qty'] * $request->qty[$idx],
                                        'reference_no' => $request->so[$idx],
                                        'planned_start_date' => ($existing_prod3) ? $planned_start_date2 : null,
                                        'production_order' => ($existing_prod3) ? $existing_prod3->name : null,
                                        's_warehouse' => $s_warehouse,
                                        'wip_warehouse' => ($existing_prod3) ? $existing_prod3->wip_warehouse : null,
                                        'fg_warehouse' => ($existing_prod3) ? $existing_prod3->fg_warehouse : null,
                                        'available_stock' => $available_stock_child1,
                                        'cycle_time' => $this->compute_item_cycle_time($child_part3['item_code'], $child_part3['qty'] * $request->qty[$idx]),
                                        // 'operation_name' => $child_part2['operation_name']
                                    ];
                                }

                            }
                        }
                    }
                }
            }

            return view('wizard.tbl_parts', compact('parts'));
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function view_bom_for_review(Request $request, $bom){
        try {
            $details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production)->first();
            if($bom == "No BOM"){
                $workstations = DB::connection('mysql_mes')->table('workstation')
                    ->join('operation', 'operation.operation_id', 'workstation.operation_id')->get();
                
                $existing_workstation= DB::connection('mysql_mes')->table('job_ticket')
                    ->where('production_order', $request->production)->get();

                $workstation_process = DB::connection('mysql_mes')->table('process')
                    ->join('process_assignment', 'process.process_id', 'process_assignment.process_id')
                    ->join('workstation', 'workstation.workstation_id', 'process_assignment.workstation_id')
                    ->select('workstation.workstation_name', 'process.process_name', 'process.process_id')
                    ->distinct('workstation.workstation_name', 'process.process_name', 'process.process_id')
                    ->orderBy('process.process_name', 'asc')
                    ->get();

                return view('reports.tbl_update_no_bom', compact('workstation_process', 'workstations', 'existing_workstation', 'details'));
            }else{
                if(!$request->operation_name){
                    if(!$details){
                        // get user permitted operation ids
                        $operation_ids = DB::connection('mysql_mes')->table('user')
                            ->join('operation', 'operation.operation_id', 'user.operation_id')
                            ->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
                            ->where('user_access_id', Auth::user()->user_id)->where('module', 'Production')
                            ->select('user.operation_id', 'operation_name')->distinct()->pluck('user.operation_id');
                    }else{
                        $operation_ids = [$details->operation_id];
                    }
                }else{
                    $operation_details = DB::connection('mysql_mes')->table('operation')
                        ->where('operation_name', 'like', '%'. $request->operation_name .'%')->first();

                    $operation_ids = [$operation_details->operation_id];
                }
                
                $workstations = DB::connection('mysql_mes')
                    ->table('workstation as w')->join('operation as op', 'op.operation_id','w.operation_id')
                    ->whereIn('op.operation_id', $operation_ids)->get();

                $workstation_process = DB::connection('mysql_mes')->table('process')
                    ->join('process_assignment', 'process.process_id', 'process_assignment.process_id')
                    ->join('workstation', 'workstation.workstation_id', 'process_assignment.workstation_id')
                    ->select('workstation.workstation_name', 'process.process_name', 'process.process_id')
                    ->distinct('workstation.workstation_name', 'process.process_name', 'process.process_id')
                    ->orderBy('process.process_name', 'asc')->get();
                
                $bom_details = DB::table('tabBOM')->where('name', $bom)->first();
                $bom_operations = DB::table('tabBOM Operation')->where('parent', $bom)->orderBy('idx', 'asc')->get();
                $bom_materials_q = DB::table('tabBOM Item')->where('parent', $bom)->orderBy('idx', 'asc')->get();

                $bom_materials = $items_with_different_uom = [];
                foreach ($bom_materials_q as $row) {
                    $item_details = DB::connection('mysql')->table('tabItem')->where('name', $row->item_code)->first();
                    if ($row->uom != $item_details->stock_uom) {
                        array_push($items_with_different_uom, $row->item_code);
                    }

                    if($item_details){
                        $bom_materials[] = [
                            'idx' => $row->idx,
                            'item_code' => $row->item_code,
                            'description' => $row->description,
                            'qty' => $row->qty,
                            'uom' => $row->uom,
                        ];
                    }
                }

                return view('wizard.tbl_bom_review', compact('workstation_process', 'workstations', 'bom_details', 'bom_operations', 'bom_materials', 'items_with_different_uom'));
            }
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function get_workstation_process($workstation){
        $arr = [];
        $workstation_details = DB::connection('mysql_mes')->table('workstation')->where('workstation_id', $workstation)->first();
        if ($workstation_details->workstation_name == 'Painting') {
            $arr[] = [
                 "workstation_name" => "Painting",
                 "process_name" => "Painting",
                 "process_id" => 0
            ];

            return $arr;
        }
        
        return DB::connection('mysql_mes')->table('process')
                ->join('process_assignment', 'process.process_id', 'process_assignment.process_id')
                ->join('workstation', 'workstation.workstation_id', 'process_assignment.workstation_id')
                ->where('workstation.workstation_id', $workstation)
                ->select('workstation.workstation_name', 'process.process_name', 'process.process_id')
                ->distinct('workstation.workstation_name', 'process.process_name', 'process.process_id')
                ->orderBy('process.process_name', 'asc')
                ->get();
    }

    public function get_scheduled_production(Request $request){
        $item_details = DB::connection('mysql')->table('tabItem')->where('name', $request->item_code)->first();

        $operations = DB::connection('mysql')->table('tabBOM Operation')->where('parent', $request->bom)
            ->select('workstation', 'process')->orderBy('idx', 'asc')->get();

        $plucked_operations = collect($operations)->pluck('workstation');
        $planned_date = Carbon::parse($request->planned_date);
        $period = CarbonPeriod::create($request->planned_date, $planned_date->addDays(2));

        $schedules = [];
        foreach ($period as $date) {
            $schedules[] = [
                'date' => $date->format('M-d-Y')
            ];
        }

        $scheduled_production_orders = [];
        foreach ($plucked_operations as $operation) {
            $production_order_list = [];
            foreach ($period as $date) {
                $scheduled_prod = DB::connection('mysql_mes')->table('production_order AS po')
                    ->join('job_ticket AS poo', 'po.production_order', 'poo.production_order')
                    ->whereNotIn('po.status', ['Stopped', 'Cancelled'])
                    ->whereDate('po.planned_start_date', $date->format('Y-m-d'))
                    ->where('poo.workstation', $operation)
                    ->select('poo.workstation', 'po.*')->get();

                $production_orders = [];
                foreach ($scheduled_prod as $row) {
                    $production_orders[] = [
                        'production_order' => $row->production_order,
                        'item_code' => $row->item_code,
                        'qty' => $row->qty_to_manufacture,
                        'workstation' => $row->workstation,
                        'sales_order' => $row->sales_order,
                        'customer' => $row->customer,
                        'project' => $row->project,
                        'delivery_date' => $row->delivery_date,
                        'description' => $row->description,
                        'stock_uom' => $row->stock_uom,
                        'status' => $this->get_production_status_workstation($row->production_order, $row->workstation),
                        'time_details' => $this->get_workstation_time_details($row->production_order, $row->workstation),
                    ];
                }

                $production_order_list[] = [
                    'schedule' => $date->format('Y-m-d'),
                    'production_orders' => $production_orders
                ];
            }

            $scheduled_production_orders[] = [
                'workstation' => $operation,
                'scheduled_production_orders' => $production_order_list
            ];
        }

        return view('wizard.view_scheduled_task', compact('operations', 'item_details', 'scheduled_production_orders', 'schedules'));
    }

    public function get_workstation_time_details($production_order, $workstation){
        $jt = DB::connection('mysql_mes')->table('job_ticket')
            ->where('production_order', $production_order)->where('workstation', $workstation)->get();

        // $total_duration_in_seconds = collect($jt)->sum('hours') * 3600
        $total_duration_in_minutes = collect($jt)->sum('hours') * 60;

        $d = floor($total_duration_in_minutes / 1440);
        $h = floor (($total_duration_in_minutes - $d * 1440) / 60);
        $m = floor($total_duration_in_minutes - ($d * 1440) - ($h * 60));

        $dur_days = ($d > 0) ? $d .'d' : null;
        $dur_hours = ($h > 0) ? $h .'h' : null;
        $dur_minutes = ($m > 0) ? $m .'m' : null;

        $duration = $dur_days .' '. $dur_hours . ' '. $dur_minutes;

        $details = [
            'start_time' => collect($jt)->max('from_time'),
            'end_time' => collect($jt)->max('to_time'),
            'duration' => $duration,
        ];

        return $details;
    }

    public function get_production_status_workstation($production_order, $workstation){
        $jt = DB::connection('mysql_mes')->table('job_ticket')
            ->where('production_order', $production_order)->where('workstation', $workstation)->get();

        $total_workstation = collect($jt)->count();
        $total_unassigned = collect($jt)->where('status', 'Unassigned')->count();
        $total_inprocess = collect($jt)->where('status', '!=', 'Completed')->count();

        if ($total_workstation == $total_unassigned) {
            return 'Not Started';
        }

        return ($total_inprocess > 0) ? 'In Process' : 'Completed';
    }

    // wizard / manual create production order / bom crud
    public function submit_bom_review(Request $request, $bom){
        $now = Carbon::now();
        if($bom == "no_bom"){
            $bom_value= "nobom";
        }else{
            $bom_value= "withbom";

        }
        if (is_numeric($request->operation)){
            $operation_name=DB::connection('mysql_mes')->table('operation')->where('operation_id', $request->operation)->first();
            $operation=$operation_name->operation_name;

        }else{
            $operation=$request->operation;

        }
        try {
            // operations for delete
            if ($request->id) {
                $bom_operations = DB::connection('mysql')->table('tabBOM Operation')
                    ->where('parent', $bom)->whereNotIn('name', array_filter($request->id))->delete();
            }

            if ($request->production_order) {
                DB::connection('mysql_mes')->table('job_ticket')
                    ->where('production_order', $request->production_order)
                    ->when($bom_value == 'nobom', function ($query) use ($request){
                        return $query->whereNotIn('job_ticket_id', array_filter($request->id));
                    })
                    ->when($bom_value == 'withbom', function ($query) use ($request){
                        return $query->whereNotIn('bom_operation_id', array_filter($request->id));
                    })->delete();
            }

            if ($request->workstation) {
                foreach ($request->workstation as $index => $workstation) {
                    if ($request->id[$index]) {
                        if ($workstation != 'Painting') {
                            DB::connection('mysql')->table('tabBOM Operation')->where('name', $request->id[$index])
                            ->update(['process' => $request->wprocess[$index], 'idx' => $index + 1]);

                            if ($request->production_order) {
                                DB::connection('mysql_mes')->table('job_ticket')
                                    ->where('production_order', $request->production_order)
                                    ->where('workstation', $workstation)
                                    ->where('status', 'Pending')
                                    ->when($bom_value == 'nobom', function ($query) use ($request, $index){
                                        return $query->where('job_ticket_id', $request->id[$index]);
                                    })
                                    ->when($bom_value == 'withbom', function ($query) use ($request, $index){
                                        return $query->where('bom_operation_id', $request->id[$index]);
                                    })->update([
                                        'process_id' => $request->wprocess[$index],
                                        'idx' => $index + 1,
                                        'status' => 'Pending',
                                        'last_modified_by' => $request->user,
                                    ]);
                            }
                        }else{
                            DB::connection('mysql')->table('tabBOM Operation')->where('name', $request->id[$index])
                                ->update(['idx' => $index + 1]);

                            if ($request->production_order) {
                                $painting_processes = DB::connection('mysql_mes')->table('process_assignment')
                                    ->join('workstation', 'workstation.workstation_id', 'process_assignment.workstation_id')
                                    ->where('workstation.workstation_name', 'Painting')
                                    ->orderBy('process_assignment.process_id', 'asc')
                                    ->pluck('process_assignment.process_id');

                                foreach ($painting_processes as $i => $painting_process) {
                                    DB::connection('mysql_mes')->table('job_ticket')
                                        ->where('production_order', $request->production_order)
                                        ->where('workstation', $workstation)
                                        ->where('status', 'Pending')
                                        ->where('process_id', $painting_process)
                                        ->update([
                                            'bom_operation_id' => $request->id[$index],
                                            'process_id' => $painting_process,
                                            'idx' => $index + $i + 1,
                                            'status' => 'Pending',
                                            'last_modified_by' => $request->user,
                                        ]);
                                }
                            }
                        }
                    }else{
                        if($operation == null){
                            $operation_db=db::connection('mysql_mes')
                            ->table('workstation')
                            ->join('operation', 'operation.operation_id', 'workstation.operation_id')
                            ->where('workstation_name',$request->workstation[$index])
                            ->select('operation.operation_name')->first();
                            $operation=$operation_db->operation_name;
                        }
                        if ($workstation != 'Painting') {
                            $name = uniqid();
                            $values = [
                                'name' => $name,
                                'creation' => $now->toDateTimeString(),
                                'modified' => $now->toDateTimeString(),
                                'modified_by' => $request->user,
                                'owner' => $request->user,
                                'docstatus' => 1,
                                'parent' => $bom,
                                'parentfield' => 'operations',
                                'parenttype' => 'BOM',
                                'idx' => $index + 1,
                                'operation' => $operation,
                                'workstation' => $request->workstation[$index],
                                'process' => $request->wprocess[$index],
                            ];

                            DB::connection('mysql')->table('tabBOM Operation')->insert($values);

                            if ($request->production_order) {
                                $insert = [
                                    'production_order' => $request->production_order,
                                    'workstation' => $workstation,
                                    'process_id' => $request->wprocess[$index],
                                    'idx' => $index + 1,
                                    'bom_operation_id' => $name,
                                    'created_by' => Auth::user()->employee_name,
                                    'last_modified_by' => Auth::user()->employee_name,
                                ];
                            
                                DB::connection('mysql_mes')->table('job_ticket')->insert($insert);
                            }
                        }else{
                            $name = uniqid();
                            $values = [
                                'name' => $name,
                                'creation' => $now->toDateTimeString(),
                                'modified' => $now->toDateTimeString(),
                                'modified_by' => $request->user,
                                'owner' => $request->user,
                                'docstatus' => 1,
                                'parent' => $bom,
                                'parentfield' => 'operations',
                                'parenttype' => 'BOM',
                                'idx' => $index + 1,
                                'operation' => $operation,
                                'workstation' => $request->workstation[$index],
                                // 'process' => $request->wprocess[$index],
                            ];

                            DB::connection('mysql')->table('tabBOM Operation')->insert($values);

                            if ($request->production_order) {
                                $painting_processes = DB::connection('mysql_mes')->table('process_assignment')
                                ->join('workstation', 'workstation.workstation_id', 'process_assignment.workstation_id')
                                ->where('workstation.workstation_name', 'Painting')
                                ->orderBy('process_assignment.process_id', 'asc')
                                ->pluck('process_assignment.process_id');

                                foreach ($painting_processes as $i => $painting_process) {
                                    $insert = [
                                        'production_order' => $request->production_order,
                                        'workstation' => $workstation,
                                        'process_id' => $painting_process,
                                        'idx' => $index + $i + 1,
                                        'bom_operation_id' => $name,
                                        'created_by' => Auth::user()->employee_name,
                                        'last_modified_by' => Auth::user()->employee_name,
                                    ];
                                
                                    DB::connection('mysql_mes')->table('job_ticket')->insert($insert);
                                }
                            }
                        }
                    }
                }
            }
            if ($request->production_order) {
                $pending_job_ticket = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->production_order)
                    ->where('status', '=', 'Completed')->count();
                if ($pending_job_ticket > 0) {
                    DB::connection('mysql_mes')->table('production_order')
                        ->where('production_order', $request->production_order)
                        ->update(['status' => 'In Progress', 'produced_qty' => 0]);
                }
            }
            DB::connection('mysql')->table('tabBOM')->where('name', $bom)->update(['is_reviewed' => 1, 'reviewed_by' => $request->user, 'last_date_reviewed' => $now->toDateTimeString()]);
            
            return response()->json(['message' => 'BOM updated and reviewed.']);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }
   
    public function create_production_order(Request $request){
        DB::connection('mysql_mes')->beginTransaction();
        try {
            $now = Carbon::now();

            if ($request->reference_type) {
                if ($request->qty <= 0) {
                    return response()->json(['success' => 0, 'message' => 'Qty cannot be less than or equal to 0.']);
                }

                $item = DB::connection('mysql')->table('tabItem')->where('name', $request->item_code)->first();
                if (!$item) {
                    return response()->json(['success' => 0, 'message' => 'Item ' .$request->item_code. ' not found.']);
                }

                $bom = DB::connection('mysql')->table('tabBOM')->where('name', $request->bom)->first();
                if (!$bom) {
                    return response()->json(['success' => 0, 'message' => 'BOM ' .$request->bom. ' not found.']);
                }

                if ($bom->is_reviewed == 0) {
                    return response()->json(['success' => 0, 'message' => 'Please review and update BOM.']);
                }
            }

            $latest_pro = DB::connection('mysql')->table('tabProduction Order')->max('name');
            $latest_pro_exploded = explode("-", $latest_pro);
            $new_id = $latest_pro_exploded[1] + 1;
            $new_id = str_pad($new_id, 5, '0', STR_PAD_LEFT);
            $new_id = 'PROM-'.$new_id;

            $existing_id = DB::connection('mysql_mes')->table('production_order')->where('production_order', $new_id)->first();
            if ($existing_id) {
                return response()->json(['success' => 0, 'message' => 'Production Order <b>' . $new_id . '</b> already exist.']);
            }

            if ($request->reference_type) {
                $sales_order = $request->sales_order;
                $material_request = $request->material_request;
                $customer = $request->customer;
                $delivery_date = $request->delivery_date;
                $project = $request->project;
                $classification = $request->classification;
            }else{
                $reference_pref = preg_replace('/[0-9]+/', null, $request->reference_no);
                $reference_pref = str_replace("-", "", $reference_pref);
                if ($reference_pref == 'SO') {
                    $so_details = DB::connection('mysql')->table('tabSales Order')->where('name', $request->reference_no)->first();

                    $sales_order = $request->reference_no;
                    $material_request = null;
                    $customer = ($so_details) ? $so_details->customer : null;
                    $delivery_date = ($so_details) ? $so_details->delivery_date : null;
                    $project = ($so_details) ? $so_details->project : null;
                    $classification = ($so_details) ? ($so_details->sales_type == 'Sample') ? 'Sample' : 'Customer Order' : null;
                }else{
                    $mr_details = DB::connection('mysql')->table('tabMaterial Request')->where('name', $request->reference_no)->first();
                    $sales_order = null;
                    $material_request = $request->reference_no;
                    $customer = ($mr_details) ? $mr_details->customer : null;
                    $delivery_date = ($mr_details) ? $mr_details->delivery_date : null;
                    $project = ($mr_details) ? $mr_details->project : null;
                    $classification = 'Customer Order';
                }
            }

            $item_details = DB::connection('mysql')->table('tabItem')->where('name', $request->item_code)->first();

            $item_classification = ($request->item_classification) ? $request->item_classification : $item_details->item_classification;
            $stock_uom = ($request->stock_uom) ? $request->stock_uom : $item_details->stock_uom;
            $item_name = ($request->item_name) ? $request->item_name : $item_details->item_name;

            $bom = DB::connection('mysql')->table('tabBOM Item as bom')
                ->join('tabItem as item', 'item.name', 'bom.item_code')
                ->whereNotIn('item.item_group', ['Raw Material', 'Factory Supplies'])
                ->where('bom.docstatus', '<', 2)
                ->where('bom.item_code', $request->item_code)
                ->select('bom.*', 'item.parts_category')
                ->orderBy('bom.modified', 'desc')->first();
            if(!empty($bom)){
                $default_bom = DB::connection('mysql')->table('tabBOM')
                ->where('docstatus', '<', 2)
                ->orderBy('modified', 'desc')
                ->where('name', $bom->parent)
                ->first();
            }

            $parent_item_code = ($request->parent_code) ? $request->parent_code : $request->item_code;
            $sub_parent_item_code = ($request->sub_parent_code) ? $request->sub_parent_code : $request->item_code;

            $parent_item_details = DB::connection('mysql')->table('tabItem')->where('name', $parent_item_code)->first();
            if (!$parent_item_details) {
                return response()->json(['success' => 0, 'message' => 'Parent Item ' .$parent_item_code. ' not found.']);
            }

            $sub_parent_item_details = DB::connection('mysql')->table('tabItem')->where('name', $sub_parent_item_code)->first();
            if (!$sub_parent_item_details) {
                return response()->json(['success' => 0, 'message' => 'Sub Parent Item ' .$sub_parent_item_code. ' not found.']);
            }

            $operation_details = DB::connection('mysql_mes')->table('operation')
                ->where('operation_name', 'like', '%'.$request->operation.'%')->first();

            if (!$operation_details) {
                return response()->json(['success' => 0, 'message' => 'Operation ' . $request->operation . ' not found.']);
            }

            $wip_wh = $this->get_operation_wip_warehouse($operation_details->operation_id);
            if ($wip_wh['success'] < 1) {
                return response()->json(['success' => 0, 'message' => $wip['message']]);
            }

            $wip = $wip_wh['message'];

            $data = [
                'name' => $new_id,
                'creation' => $now->toDateTimeString(),
                'modified' => $now->toDateTimeString(),
                'modified_by' => Auth::user()->email,
                'owner' => Auth::user()->email,
                'docstatus' => 1,
                'idx' => 0,
                'qty' => $request->qty,
                'fg_warehouse' => $request->target,
                'use_multi_level_bom' => 0,
                'material_transferred_for_manufacturing' => 0,
                'stock_uom' => $stock_uom,
                'naming_series' => 'PRO-',
                'status' => 'Not Started',
                'description' => $request->description,
                'company' => 'FUMACO Inc.',
                'production_item' => strtoupper($request->item_code),
                'sales_order_item' => null,
                'bom_no' => $request->bom,
                'wip_warehouse' => $wip,
                'project' => $project,
                'scrap_warehouse' => 'Scrap Warehouse P1 - FI',
                'item_classification' => $item_classification,
                'delivery_date' => $delivery_date,
                'item_name' => $item_name,
                'customer' => $customer,
                'sales_order_no' => $sales_order,
                'material_request' => $material_request,
                'scheduled' => ($request->planned_date) ? 1 : 0,
                'order_no' => 0,
                'priority' => 'Normal',
                'classification' => $classification,
                'parent_item_code' => $request->parent_code,
                'planned_start_date' => ($request->planned_date) ? $request->planned_date : null,
            ];

            $params = DB::connection('mysql')->table('tabItem Variant Attribute')->where('parent', $request->item_code)
                ->where('attribute', 'LIKE', '%cutting size%')->first();
            
            $bom_sub_parent = ($bom)? $default_bom->item : null;
            
            $data_mes = [
                'production_order' => $new_id,
                'parent_item_code' => strtoupper($request->parent_code),
                'sub_parent_item_code' => strtoupper(($request->sub_parent_code)? $request->sub_parent_code : $bom_sub_parent),
                'item_code' => strtoupper($request->item_code),
                'description' => $request->description,
                'parts_category' => $item_details->parts_category,
                'item_classification' => $item_classification,
                'qty_to_manufacture' => $request->qty,
                'classification' => $classification,
                'order_no' => 0,
                'cutting_size' => ($params) ? $params->attribute_value : null,
                'is_scheduled' => ($request->planned_date) ? 1 : 0,
                'planned_start_date' => ($request->planned_date) ? $request->planned_date : null,
                'project' => $project,
                'bom_no' => $request->bom,
                'sales_order' => $sales_order,
                'material_request' => $material_request,
                'delivery_date' => $delivery_date,
                'status' => 'Not Started',
                'stock_uom' => $stock_uom,
                'customer' => $customer,
                'wip_warehouse' => $wip,
                'fg_warehouse' => $request->target,
                'last_modified_at' => $now->toDateTimeString(),
                'last_modified_by' => Auth::user()->email,
                'created_by' => Auth::user()->email,
                'created_at' => $now->toDateTimeString(),
                'operation_id' => $operation_details->operation_id
            ];

            DB::connection('mysql_mes')->table('production_order')->insert($data_mes);
            $this->save_production_operations($new_id, $request->bom, ($request->planned_date) ? $request->planned_date : null, 'mes');

            $del_data = [
                'erp_reference_id' => $request->item_reference_id,
                'reference_no' => ($sales_order) ? $sales_order : $material_request,
                'parent_item_code' => strtoupper($request->parent_code),
                'delivery_date' => $request->delivery_date
            ];

            $existing_del_data = DB::connection('mysql_mes')->table('delivery_date')
                ->where('erp_reference_id', $request->item_reference_id)->where('parent_item_code', $request->parent_code)
                ->exists();

            if(!$existing_del_data){
                DB::connection('mysql_mes')->table('delivery_date')->insert($del_data);
            }

            DB::connection('mysql')->beginTransaction();
            try{
                DB::connection('mysql')->table('tabProduction Order')->insert($data);
                $required_items = $this->save_production_req_items($new_id, $request->bom, $request->qty, $request->operation);
                $this->save_production_operations($new_id, $request->bom, ($request->planned_date) ? $request->planned_date : null, 'erp');

                if($required_items['error'] == 1){
                    return response()->json(["success" => 0, 'message' => $required_items['message']]);
                }

                DB::connection('mysql')->commit();
            } catch (Exception $e) {
                DB::connection('mysql')->rollback();
                return response()->json(["success" => 0, 'message' => 'There was a problem creating production order.']);
            }
                   
            DB::connection('mysql_mes')->commit();
              
            return response()->json(["success" => 1, 'message' => $new_id]);
        } catch (Exception $e) {
            DB::connection('mysql_mes')->rollback();
            return response()->json(["success" => 0, 'message' => 'There was a problem creating production order.']);
        }
    }

    public function save_production_req_items($parent, $bom, $qty, $operation){
        try {
            $now = Carbon::now();
            $bom_qty = DB::connection('mysql')->table('tabBOM')->where('name', $bom)->sum('quantity');
            $bom_items = DB::connection('mysql')->table('tabBOM Item')->where('parent', $bom)->orderBy('idx', 'asc')->get();

            $req_items = [];
            foreach ($bom_items as $item) {
                $item_details = DB::connection('mysql')->table('tabItem')->where('name', $item->item_code)->first();
                if(!$item_details){
                    return ['error' => 1, 'message' => 'Item ' . $item->item_code . ' not found.'];
                }

                $item_warehouse_setup = DB::connection('mysql_mes')->table('item_classification_warehouse')
                    ->where('item_classification', $item_details->item_classification)->first();

                $source_warehouse = ($item_warehouse_setup) ? $item_warehouse_setup->warehouse : null;

                $default_warehouse = ($source_warehouse) ? $source_warehouse : $item_details->default_warehouse;

                if(!$default_warehouse){
                    return ['error' => 1, 'message' => 'No assigned source warehouse for item ' . $item->item_code . ' not found.'];
                }

                $required_qty = ($item->qty / $bom_qty) * $qty;
                $req_items[] = [
                    'name' => 'mes'.uniqid(),
                    'creation' => $now->toDateTimeString(),
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->email,
                    'owner' => Auth::user()->email,
                    'docstatus' => 1,
                    'parent' => $parent,
                    'parentfield' => 'required_items',
                    'parenttype' => 'Production Order',
                    'idx' => $item->idx,
                    'description' => $item->description,
                    'item_name' => $item->item_name,
                    'item_code' => $item->item_code,
                    'required_qty' => $required_qty,
                    'transferred_qty' => 0,
                    'available_qty_at_source_warehouse' => 0,
                    'available_qty_at_wip_warehouse' => 0,
                    'source_warehouse' => $default_warehouse,
                    'stock_uom' => $item->stock_uom
                ];
            }

            DB::connection('mysql')->table('tabProduction Order Item')->insert($req_items);

            return ['error' => 0, 'message' => 'No error(s)'];
        } catch (Exception $e) {
            return ['error' => 1, 'message' => $e->getMessage()];
        }
    }

    public function save_production_operations($parent, $bom, $planned_start_date, $db_site){
        try {
            $now = Carbon::now();
            $bom_operations = DB::connection('mysql')->table('tabBOM Operation')->where('parent', $bom)->orderBy('idx', 'asc')->get();
            $operations = [];
            $mes_operations = [];

            $painting_processes = DB::connection('mysql_mes')->table('process_assignment')
                ->join('workstation', 'workstation.workstation_id', 'process_assignment.workstation_id')
                ->join('operation as op', 'op.operation_id', 'workstation.operation_id')
                ->where('workstation.workstation_name', 'Painting')
                // ->where('op.operation_name', 'Fabrication')
                ->orderBy('process_assignment.process_id', 'asc')
                ->pluck('process_assignment.process_id');

            foreach ($bom_operations as $operation) {
                $operations[] = [
                    'name' => 'mes'.uniqid(),
                    'creation' => $now->toDateTimeString(),
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->email,
                    'owner' => Auth::user()->email,
                    'docstatus' => 1,
                    'parent' => $parent,
                    'parentfield' => 'operations',
                    'parenttype' => 'Production Order',
                    'idx' => $operation->idx,
                    'status' => 'Pending',
                    'actual_start_time' => null,
                    'workstation' => $operation->workstation,
                    'completed_qty' => 0,
                    'planned_operating_cost' => 0,
                    'description' => $operation->description,
                    'actual_end_time' => null,
                    'actual_operating_cost' => 0,
                    'hour_rate' => 0,
                    'planned_start_time' => null,
                    'bom' => $bom,
                    'actual_operation_time' => 0,
                    'operation' => $operation->operation,
                    'planned_end_time' => null,
                    'time_in_mins' => 1,
                    'process' => $operation->process,
                ];

                if ($operation->workstation != 'Painting') {
                    $mes_operations[] = [
                        'production_order' => $parent,
                        'idx' => $operation->idx,
                        'workstation' => $operation->workstation,
                        'process_id' => $operation->process,
                        'planned_start_date' => $planned_start_date,
                        'created_by' => Auth::user()->employee_name,
                        'created_at' => $now->toDateTimeString(),
                        'last_modified_by' => Auth::user()->employee_name,
                        'last_modified_at' => $now->toDateTimeString(),
                        'bom_operation_id' => $operation->name,
                    ];
                }else{
                    foreach ($painting_processes as $painting_process) {
                        $mes_operations[] = [
                            'production_order' => $parent,
                            'idx' => $operation->idx,
                            'workstation' => $operation->workstation,
                            'process_id' => $painting_process,
                            'planned_start_date' => $planned_start_date,
                            'created_by' => Auth::user()->employee_name,
                            'created_at' => $now->toDateTimeString(),
                            'last_modified_by' => Auth::user()->employee_name,
                            'last_modified_at' => $now->toDateTimeString(),
                            'bom_operation_id' => $operation->name,
                        ];                   
                    }
                }
            }

            if($db_site == 'mes'){
                DB::connection('mysql_mes')->table('job_ticket')->insert($mes_operations);
            }else{
                DB::connection('mysql')->table('tabProduction Order Operation')->insert($operations);
            }
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage(), "id" => $parent]);
        }
    }

    public function cancel_production_order(Request $request){
        DB::connection('mysql')->beginTransaction();
        try {
            $now = Carbon::now();
            // check for task in progress
            $task_in_progress = DB::connection('mysql_mes')->table('job_ticket')
                ->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
                ->where('production_order', $request->production_order)
                ->where('time_logs.status', 'In Progress')->count();

            if ($task_in_progress > 0) {
                return response()->json(['success' => 0, 'message' => 'Cannot cancel production order with on-going task by operator.' . $request->production_order]);
            }

            // get sum total of feedback qty in production order
            $feedbacked_qty = DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)->sum('feedback_qty');
            if($feedbacked_qty > 0){
                return response()->json(['success' => 0, 'message' => 'Cannot cancel' . $request->production_order . '. Production Order has been partially feedbacked.']);
            }

            // get pending material transfer for manufacture stock entries of production order
            $pending_withdrawal_slips = DB::connection('mysql')->table('tabStock Entry')
                ->where('production_order', $request->production_order)
                ->where('purpose', 'Material Transfer for Manufacture')
                ->where('docstatus', 0)->distinct()->pluck('name');

            // delete all "for checking" stock entry detail
            DB::connection('mysql')->table('tabStock Entry Detail')
                ->whereIn('parent', $pending_withdrawal_slips)
                ->where('status', 'For Checking')->delete();
            
            // get stock entries with issued items after deleting "for checking" items
            $issued_item_requests = DB::connection('mysql')->table('tabStock Entry Detail')
                ->whereIn('parent', $pending_withdrawal_slips)->where('status', 'Issued')
                ->where('docstatus', 0)->distinct()->pluck('parent');

            // get stock entries without child items
            $empty_ste = array_diff($pending_withdrawal_slips->toArray(), $issued_item_requests->toArray());

            if(count($empty_ste) > 0){
                // delete empty stock entries if any
                DB::connection('mysql')->table('tabStock Entry')
                    ->where('production_order', $request->production_order)
                    ->where('purpose', 'Material Transfer for Manufacture')
                    ->where('docstatus', 0)->whereIn('name', $empty_ste)->delete();
            }

            // recalculate stock entry total incoming / outgoing values and total amount
            if (count($issued_item_requests) > 0) {
                foreach ($issued_item_requests as $stock_entry) {
                    $ste_detail = DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $stock_entry)->get();
                    // check if ste item is not empty
                    if(count($ste_detail) > 0){
                        // set status
                        $for_checking = collect($ste_detail)->where('status', '!=', 'Issued')->count();
                        $item_status = ($for_checking > 0) ? 'For Checking' : 'Issued';
                        
                        $stock_entry_data = [
                            'modified' => $now->toDateTimeString(),
                            'modified_by' => Auth::user()->email,
                            'posting_time' => $now->format('H:i:s'),
                            'total_outgoing_value' => collect($ste_detail)->sum('basic_amount'),
                            'total_amount' => collect($ste_detail)->sum('basic_amount'),
                            'total_incoming_value' => collect($ste_detail)->sum('basic_amount'),
                            'posting_date' => $now->format('Y-m-d'),
                            'item_status' => $item_status,
                        ];
                        // update stock entry
                        DB::connection('mysql')->table('tabStock Entry')->where('name', $stock_entry)->where('docstatus', 0)->update($stock_entry_data);
                    }else{
                        // delete stock entry
                        DB::connection('mysql')->table('tabStock Entry')->where('name', $stock_entry)->where('docstatus', 0)->delete();
                    }
                }
            }

            DB::table('tabProduction Order')->where('name', $request->production_order)
                ->where('docstatus', 1)->where('status', '!=', 'Completed')
                ->update(['docstatus' => 2, 'status' => 'Cancelled', 'modified' => $now->toDateTimeString(), 'modified_by' => Auth::user()->email]);

            DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)
                ->where('status', '!=', 'Completed')->update(['status' => 'Cancelled', 'last_modified_at' => $now->toDateTimeString(), 'last_modified_by' => Auth::user()->email]);

            DB::connection('mysql')->commit();

            return response()->json(['success' => 1, 'message' => 'Production Order <b>' . $request->production_order . '</b> and its pending withdrawal request has been cancelled.']);
        } catch (Exception $e) {
            DB::connection('mysql')->rollback();
            return response()->json(['success' => 0, 'message' => 'There was a problem creating transaction.']);
        }
    }

    public function get_production_order_items($production_order){
        $details = DB::connection('mysql_mes')->table('production_order')
            ->leftJoin('delivery_date', function($join)
            {
                $join->on( DB::raw('IFNULL(production_order.sales_order, production_order.material_request)'), '=', 'delivery_date.reference_no');
                $join->on('production_order.parent_item_code','=','delivery_date.parent_item_code');
            })
            ->where('production_order.production_order', $production_order)
            ->select('production_order.*', 'delivery_date.rescheduled_delivery_date')
            ->first();
            
        if (!$details) {
            return response()->json(['success' => 0, 'message' => 'Production Order not found.']);
        }

        $production_order_items = DB::connection('mysql')->table('tabProduction Order Item')->where('parent', $production_order)->get();
        $components = $parts = [];
        foreach ($production_order_items as $item) {
            $item_details = DB::connection('mysql')->table('tabItem')->where('name', $item->item_code)->first();
            // get item stock based on feedbacked qty for housing and other items with sub assemblies
            $has_production_order = DB::connection('mysql_mes')->table('production_order')
                ->where('item_code', $item->item_code)->where('parent_item_code', $details->parent_item_code)
                ->where('sales_order', $details->sales_order)
                ->where('material_request', $details->material_request)
                ->where('sub_parent_item_code', $details->item_code)->first();

            $available_qty_at_wip = $this->get_actual_qty($item->item_code, $details->wip_warehouse);

            $has_pending_ste_for_issue = DB::connection('mysql')->table('tabStock Entry as ste')
                ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                ->where('ste.production_order', $production_order)->where('ste.purpose', 'Material Transfer for Manufacture')
                ->where('ste.docstatus', 0)->where('sted.item_code', $item->item_code)->exists();

            // get stock entry transferred qty
			$transferred_qty = DB::connection('mysql')->table('tabStock Entry as ste')
                ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                ->where('ste.docstatus', 1)->where('ste.production_order', $production_order)
                ->where('sted.item_code', $item->item_code)->where('ste.purpose', 'Material Transfer for Manufacture')
                ->sum('qty');

            $item_status = 'For Checking';
            if($has_pending_ste_for_issue == false){
                $has_submitted_ste = DB::connection('mysql')->table('tabStock Entry as ste')
                    ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                    ->where('ste.production_order', $production_order)->where('ste.purpose', 'Material Transfer for Manufacture')
                    ->where('ste.docstatus', 1)->where('sted.item_code', $item->item_code)->exists();
                
                if($has_submitted_ste == true){
                    $item_status = 'Issued';
                }
            }

            $references = DB::connection('mysql')->table('tabStock Entry as ste')
                ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                ->where('ste.production_order', $production_order)->where('ste.purpose', 'Material Transfer for Manufacture')
                ->where('ste.docstatus', 1)->where('sted.item_code', $item->item_code)
                ->select('ste.name', 'sted.date_modified', 'sted.session_user', 'sted.qty')->get();

            if($has_production_order){
                $parts[] = [
                    'name' => $item->name,
                    'idx' => $item->idx,
                    'item_code' => $item->item_code,
                    'item_name' => $item_details->item_name,
                    'description' => $item->description,
                    'item_image' => $item_details->item_image_path,
                    'item_classification' => $item_details->item_classification,
                    'source_warehouse' => $item->source_warehouse,
                    'required_qty' => $item->required_qty,
                    'stock_uom' => $item->stock_uom,
                    'transferred_qty' => $transferred_qty,
                    'actual_qty' => $this->get_actual_qty($item->item_code, $item->source_warehouse),
                    'production_order' => $has_production_order->production_order,
                    'available_qty_at_wip' => $available_qty_at_wip,
                    'has_pending_ste_for_issue' => $has_pending_ste_for_issue,
                    'status' => $has_production_order->status,
                    'item_status' => $item_status,
                    'references' => $references
                ];
            }else{
                $components[] = [
                    'name' => $item->name,
                    'idx' => $item->idx,
                    'item_code' => $item->item_code,
                    'item_name' => $item_details->item_name,
                    'description' => $item->description,
                    'item_image' => $item_details->item_image_path,
                    'item_classification' => $item_details->item_classification,
                    'source_warehouse' => $item->source_warehouse,
                    'required_qty' => $item->required_qty,
                    'stock_uom' => $item->stock_uom,
                    'transferred_qty' => $transferred_qty,
                    'actual_qty' => $this->get_actual_qty($item->item_code, $item->source_warehouse),
                    'production_order' => null,
                    'available_qty_at_wip' => $available_qty_at_wip,
                    'has_pending_ste_for_issue' => $has_pending_ste_for_issue,
                    'status' => null,
                    'item_status' => $item_status,
                    'references' => $references
                ];
            }
        }

        $required_items = array_merge($components, $parts);

        // get returned / for return items linked with production order (stock entry material transfer)
        $item_returns = DB::connection('mysql')->table('tabStock Entry as ste')
            ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
            ->where('ste.purpose', 'Material Transfer')->where('ste.transfer_as', 'For Return')
            ->where('ste.production_order', $production_order)
            ->where('ste.docstatus', '<', 2)->select('sted.*', 'ste.docstatus')->get();

        $items_return = [];
        foreach ($item_returns as $ret) {
            $item_details = DB::connection('mysql')->table('tabItem')->where('name', $ret->item_code)->first();
            $item_classification = $item_details->item_classification;

            $items_return[] = [
                'ste_name' => $ret->parent,
                'sted_name' => $ret->name,
                'sted_status' => $ret->status,
                'ste_docstatus' => $ret->docstatus,
                'idx' => $ret->idx,
                'item_code' => $ret->item_code,
                'description' => $ret->description,
                'item_image' => $item_details->item_image_path,
                'item_classification' => $item_classification,
                'target_warehouse' => $ret->t_warehouse,
                'requested_qty' => $ret->qty,
                'stock_uom' => $ret->stock_uom,
                'received_qty' => $ret->issued_qty,
            ];
        }

        $issued_qty = DB::connection('mysql')->table('tabStock Entry as ste')
            ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
            ->where('ste.docstatus', 0)->where('ste.production_order', $production_order)
            ->where('sted.status', 'Issued')->where('ste.purpose', 'Material Transfer for Manufacture')
            ->sum('qty');

        $feedbacked_logs = DB::connection('mysql_mes')->table('feedbacked_logs')->where('production_order', $production_order)->get();

        return view('tables.tbl_production_order_items', compact('required_items', 'details', 'components', 'parts', 'items_return', 'issued_qty', 'feedbacked_logs'));
    }

    public function create_material_transfer_for_return(Request $request){
        DB::connection('mysql')->beginTransaction();
        try {
            $now = Carbon::now();
            $production_order_details = DB::connection('mysql')->table('tabProduction Order')
                ->where('name', $request->production_order)->first();
            
            if (!$production_order_details) {
                return response()->json(['status' => 0, 'message' => 'Production Order ' . $request->production_order . ' not found.']);
            }

            if($production_order_details->status == 'Completed'){
                return response()->json(['status' => 2, 'message' => 'Production Order ' . $request->production_order . ' is already Completed.']);
            }

            if($request->qty_to_return > $request->qty){
                return response()->json(['status' => 0, 'message' => 'Quantity cannot be greater than ' . $request->qty]);
            }
            
            // copy values from stock entry detail
            $stock_entry_details = DB::connection('mysql')->table('tabStock Entry as ste')
				->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
				->where('ste.docstatus', 1)->where('ste.production_order', $request->production_order)
				->where('sted.item_code', $request->item_code)
				->where('ste.purpose', 'Material Transfer for Manufacture')->first();
            
            if (!$stock_entry_details) {
                return response()->json(['status' => 0, 'message' => 'Stock entry item ' . $request->item_code . ' not found.']);
            }

            $latest_ste = DB::connection('mysql')->table('tabStock Entry')->max('name');
            $latest_ste_exploded = explode("-", $latest_ste);
            $new_id = $latest_ste_exploded[1] + 1;
            $new_id = str_pad($new_id, 5, '0', STR_PAD_LEFT);
            $new_id = 'STEM-'.$new_id;

            $base_rate = $stock_entry_details->basic_rate;

            $actual_qty = DB::connection('mysql')->table('tabBin')
                ->where('item_code', $request->item_code)->where('warehouse', $request->target_warehouse)
                ->sum('actual_qty');

            if(in_array($request->target_warehouse, ['Fabrication - FI', 'Spotwelding Warehouse - FI'])){
                $item_status = 'Issued';
            }else{
                $item_status = 'For Checking';
            }

            if($item_status == 'For Checking'){
                $item_classification = DB::connection('mysql')->table('tabItem')->where('name', $request->item_code)->first()->item_classification;
                if(in_array($item_classification, ['FA - FRAME ASSEMBLY', 'RA - REFLECTOR ASSEMBLY'])){
                    $item_status = 'Issued';
                }
            }

            $docstatus = ($actual_qty >= $request->qty_to_return) ? 1 : 0;
            $docstatus = ($item_status == 'Issued') ? $docstatus : 0;

            $stock_entry_detail = [
                'name' =>  uniqid(),
                'creation' => $now->toDateTimeString(),
                'modified' => $now->toDateTimeString(),
                'modified_by' => Auth::user()->email,
                'owner' => Auth::user()->email,
                'docstatus' => $docstatus,
                'parent' => $new_id,
                'parentfield' => 'items',
                'parenttype' => 'Stock Entry',
                'idx' => 1,
                't_warehouse' => $request->source_warehouse,
                'transfer_qty' => $request->qty_to_return,
                'serial_no' => null,
                'expense_account' => 'Cost of Goods Sold - FI',
                'cost_center' => 'Main - FI',
                'actual_qty' => $actual_qty,
                's_warehouse' => $request->target_warehouse,
                'item_name' => $stock_entry_details->item_name,
                'image' => null,
                'additional_cost' => 0,
                'stock_uom' => $stock_entry_details->stock_uom,
                'basic_amount' => $base_rate * $request->qty_to_return,
                'sample_quantity' => 0,
                'uom' => $stock_entry_details->stock_uom,
                'basic_rate' => $base_rate,
                'description' => $stock_entry_details->description,
                'barcode' => null,
                'conversion_factor' => $stock_entry_details->conversion_factor,
                'item_code' => $stock_entry_details->item_code,
                'retain_sample' => 0,
                'qty' => $request->qty_to_return,
                'bom_no' => null,
                'allow_zero_valuation_rate' => 0,
                'material_request_item' => null,
                'amount' => $base_rate * $request->qty_to_return,
                'batch_no' => null,
                'valuation_rate' => $base_rate,
                'material_request' => null,
                't_warehouse_personnel' => null,
                's_warehouse_personnel' => null,
                'target_warehouse_location' => null,
                'source_warehouse_location' => null,
                'status' => $item_status,
                'date_modified' => ($item_status == 'Issued') ? $now->toDateTimeString() : null,
                'session_user' => ($item_status == 'Issued') ? Auth::user()->employee_name : null,
                'remarks' => ($item_status == 'Issued') ? 'MES' : null,
            ];

            $stock_entry_data = [
                'name' => $new_id,
                'creation' => $now->toDateTimeString(),
                'modified' => $now->toDateTimeString(),
                'modified_by' => Auth::user()->email,
                'owner' => Auth::user()->email,
                'docstatus' => $docstatus,
                'parent' => null,
                'parentfield' => null,
                'parenttype' => null,
                'idx' => 0,
                'use_multi_level_bom' => 0,
                'delivery_note_no' => null,
                'naming_series' => 'STE-',
                'fg_completed_qty' => 0,
                'letter_head' => null,
                '_liked_by' => null,
                'purchase_receipt_no' => null,
                'posting_time' => $now->format('H:i:s'),
                'customer_name' => null,
                'to_warehouse' => null,
                'title' => 'Material Transfer',
                '_comments' => null,
                'from_warehouse' => null,
                'set_posting_time' => 0,
                'purchase_order' => null,
                'from_bom' => 0,
                'supplier_address' => null,
                'supplier' => null,
                'source_address_display' => null,
                'address_display' => null,
                'source_warehouse_address' => null,
                'value_difference' => 0,
                'credit_note' => null,
                'sales_invoice_no' => null,
                'company' => 'FUMACO Inc.',
                'target_warehouse_address' => null,
                'customer_address' => null,
                'total_outgoing_value' => collect($stock_entry_detail)->sum('basic_amount'),
                'supplier_name' => null,
                'remarks' => null,
                '_user_tags' => null,
                'total_additional_costs' => 0,
                'customer' => null,
                'bom_no' => null,
                'amended_from' => null,
                'total_amount' => collect($stock_entry_detail)->sum('basic_amount'),
                'total_incoming_value' => collect($stock_entry_detail)->sum('basic_amount'),
                'project' => $production_order_details->project,
                '_assign' => null,
                'select_print_heading' => null,
                'posting_date' => $now->format('Y-m-d'),
                'target_address_display' => null,
                'production_order' => $production_order_details->name,
                'purpose' => 'Material Transfer',
                'shipping_address_contact_person' => null,
                'customer_1' => null,
                'material_request' => $production_order_details->material_request,
                'reference_no' => null,
                'delivery_date' => null,
                'delivery_address' => null,
                'city' => null,
                'address_line_2' => null,
                'address_line_1' => null,
                'item_status' => 'For Checking',
                'sales_order_no' => $production_order_details->sales_order,
                'transfer_as' => 'For Return',
                'workflow_state' => null,
                'item_classification' => $production_order_details->item_classification,
                'bom_repack' => null,
                'qty_repack' => 0,
                'issue_as' => null,
                'receive_as' => null,
                'so_customer_name' => $production_order_details->customer,
                'order_type' => $production_order_details->classification,
            ];

            DB::connection('mysql')->table('tabStock Entry Detail')->insert($stock_entry_detail);
            DB::connection('mysql')->table('tabStock Entry')->insert($stock_entry_data);

            if ($docstatus == 1) {
                $this->update_bin($new_id);
                $this->create_stock_ledger_entry($new_id);
                $this->create_gl_entry($new_id);

                // update production order item transferred qty - return
                $production_order_item = DB::connection('mysql')->table('tabProduction Order Item')->where('name', $request->id)->first();
                if($production_order_item){
                    $transferred_qty = $production_order_item->transferred_qty - $request->qty_to_return;
                    DB::connection('mysql')->table('tabProduction Order Item')->where('name', $request->id)->update(['transferred_qty' => $transferred_qty]);
                }
            }

            DB::connection('mysql')->commit();

            return response()->json(['status' => 1, 'message' => 'Stock Entry has been created.']);
        } catch (Exception $e) {
            DB::connection('mysql')->rollback();
            return response()->json(['status' => 0, 'message' => 'There was a problem creating stock entries.']);
        }
    }

    public function get_items_for_return($production_order, Request $request){
        $details = DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)->first();
        if (!$details) {
            return response()->json(['success' => 0, 'message' => 'Production Order not found.']);
        }

        $q = DB::connection('mysql')->table('tabStock Entry as ste')
            ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
            ->where('ste.purpose', 'Material Transfer for Manufacture')->where('ste.production_order', $production_order)
            ->where('ste.docstatus', 1)->select('sted.*', 'ste.docstatus')->get();

        $items = [];
        foreach ($q as $item) {
            $item_details = DB::connection('mysql')->table('tabItem')->where('name', $item->item_code)->first();
            $item_classification = $item_details->item_classification;
            $items[] = [
                'ste_name' => $item->parent,
                'sted_name' => $item->name,
                'sted_status' => $item->status,
                'ste_docstatus' => $item->docstatus,
                'idx' => $item->idx,
                'item_code' => $item->item_code,
                'description' => $item->description,
                'item_image' => $item_details->item_image_path,
                'item_classification' => $item_classification,
                'source_warehouse' => $item->s_warehouse,
                'target_warehouse' => $item->t_warehouse,
                'qty' => $item->qty,
                'stock_uom' => $item->stock_uom,
            ];
        }

        return view('tables.tbl_items_for_return', compact('items', 'details'));
    }

    public function get_items($item_classification, Request $request){
        return DB::table('tabItem')->where('is_stock_item', 1)->where('disabled', 0)
            ->where('has_variants', 0)->where('item_classification', $item_classification)
            ->where('name', 'like', '%'.$request->term.'%')
            ->select('name as value', 'description as id', 'name as label', 'item_name')->orderBy('modified', 'desc')->limit(5)->get();
    }

    public function get_mes_warehouse(){
        return DB::connection('mysql_mes')->table('item_classification_warehouse')
            ->distinct('warehouse')->orderBy('warehouse', 'asc')->pluck('warehouse');
    }

    // function to submit change / replacement of item code
    public function update_ste_detail(Request $request){
        DB::connection('mysql')->beginTransaction();
        try {
			$now = Carbon::now();
            $production_order_details = DB::connection('mysql')->table('tabProduction Order')->where('name', $request->production_order)->first();

            // get production order item transferred qty
            $transferred_qty = DB::connection('mysql')->table('tabProduction Order Item')
                ->where('parent', $request->production_order)->where('item_code', $request->old_item_code)->sum('transferred_qty');

            if($transferred_qty > 0){
                return response()->json(['status' => 0, 'message' => 'Item has been already issued. Click "Add Item" button below to add items for issue.']);
            }

            $item_details = DB::connection('mysql')->table('tabItem')->where('name', $request->item_code)->first();
            if(!$item_details){
                return response()->json(['status' => 0, 'message' => 'Item <b>'. $request->item_code.'</b> not found.']);
            }

            // get stock entry transferred qty
			$transferred_qty = DB::connection('mysql')->table('tabStock Entry as ste')
                ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                ->where('ste.docstatus', 1)->where('ste.production_order', $request->production_order)
                ->where('sted.item_code', $request->old_item_code)->where('ste.purpose', 'Material Transfer for Manufacture')
                ->sum('qty');

            if($transferred_qty > 0){
                return response()->json(['status' => 0, 'message' => 'Item has been already issued. Click "Add Item" button below to add items for issue.']);
            }

			// get all pending stock entries based on item code production order
			$pending_stock_entries = DB::connection('mysql')->table('tabStock Entry as ste')
				->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
				->where('ste.docstatus', 0)->where('ste.production_order', $request->production_order)
				->where('sted.item_code', $request->old_item_code)
				->where('ste.purpose', 'Material Transfer for Manufacture')
				->select('sted.name as sted_name', 'ste.name as ste_name')
                ->get()->toArray();
            
            if(count($pending_stock_entries) > 0){
                if(in_array($request->source_warehouse, ['Fabrication - FI', 'Spotwelding Warehouse - FI'])){
                    $item_status = 'Issued';
                }else{
                    $item_status = 'For Checking';
                }

                // update each pending stock entry detail with new item code
                $sted_names = array_column($pending_stock_entries, 'sted_name');
                foreach ($sted_names as $sted_name) {
                    $values = [
                        'item_code' => strtoupper($request->item_code),
                        'item_name' => $request->item_name,
                        'description' => $request->description,
                        'qty' => $request->quantity,
                        'transfer_qty' => $request->quantity,
                        's_warehouse' => $request->source_warehouse,
                        'item_note' => $request->remarks,
                        'status' => $item_status,
                        'date_modified' => ($item_status == 'Issued') ? $now->toDateTimeString() : null,
                        'session_user' => ($item_status == 'Issued') ? Auth::user()->employee_name : null,
                        'remarks' => ($item_status == 'Issued') ? 'MES' : null,
                        'issued_qty' => ($item_status == 'Issued') ? $request->quantity : 0,
                    ];
        
                    DB::connection('mysql')->table('tabStock Entry Detail')->where('name', $sted_name)->update($values);
                }
            }

            $production_order_item = [
                'modified' => $now->toDateTimeString(),
                'modified_by' => Auth::user()->email,
                'item_code' => strtoupper($request->item_code),
                'item_name' => $request->item_name,
                'description' => $request->description,
                'required_qty' => $request->quantity,
                'available_qty_at_source_warehouse' => 0,
                'available_qty_at_wip_warehouse' => 0,
                'source_warehouse' => $request->source_warehouse
            ];

            DB::connection('mysql')->table('tabProduction Order Item')
                ->where('parent', $request->production_order)->where('item_code', $request->old_item_code)
                ->update($production_order_item);
            
            DB::connection('mysql')->commit();

            return response()->json(['status' => 1, 'message' => 'Stock entry item has been changed.']);
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'message' => 'There was a problem updating stock entry.']);
            DB::connection('mysql')->rollback();
        }
    }

    public function add_ste_items(Request $request){
        DB::connection('mysql')->beginTransaction();
        try {
            if(count($request->item_code) < 1){
                return response()->json(['status' => 2, 'message' => 'Please enter items to be added.']);
            }
            $now = Carbon::now();
            $mes_production_order_details = DB::connection('mysql_mes')->table('production_order')
                ->where('production_order', $request->production_order)->first();

            $stock_entry_data = [];
            foreach ($request->item_code as $id => $item_code) {
                $item_details = DB::connection('mysql')->table('tabItem')->where('name', $item_code)->first();

                $qty = $request->quantity[$id];

                // insert items to production order item table in erp
                $production_order_required_item_id = 'prid' . uniqid();
                $production_order_item = [
                    'name' => $production_order_required_item_id,
                    'creation' => $now->toDateTimeString(),
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->email,
                    'owner' => Auth::user()->email,
                    'docstatus' => 1,
                    'parent' => $mes_production_order_details->production_order,
                    'parentfield' => 'required_items',
                    'parenttype' => 'Production Order',
                    'idx' => 0,
                    'description' => $item_details->description,
                    'item_name' => $item_details->item_name,
                    'stock_uom' => $item_details->stock_uom,
                    'item_code' => $item_details->item_code,
                    'required_qty' => $qty,
                    'transferred_qty' => 0,
                    'available_qty_at_source_warehouse' => 0,
                    'available_qty_at_wip_warehouse' => 0, 
                    'source_warehouse' => $request->source_warehouse[$id],
                ];

                DB::connection('mysql')->table('tabProduction Order Item')->insert($production_order_item);

                $latest_ste = DB::connection('mysql')->table('tabStock Entry')->max('name');
                $latest_ste_exploded = explode("-", $latest_ste);
                $new_id = $latest_ste_exploded[1] + 1;
                $new_id = str_pad($new_id, 5, '0', STR_PAD_LEFT);
                $new_id = 'STEM-'.$new_id;

                $qty = $request->quantity[$id];

                $actual_qty = $valuation_rate = 0;

                $bin_qry = DB::connection('mysql')->table('tabBin')->where('warehouse', $request->source_warehouse[$id])
                    ->where('item_code', $item_code)->first();
                
                if ($bin_qry) {
                    $actual_qty = $bin_qry->actual_qty;
                    $valuation_rate = $bin_qry->valuation_rate;
                }

                if(in_array($request->source_warehouse[$id], ['Fabrication - FI', 'Spotwelding Warehouse - FI']) && $mes_production_order_details->operation_id == 1){
                    $item_status = 'Issued';
                }else{
                    $item_status = 'For Checking';
                }

                $stock_entry_detail = [
                    'name' =>  uniqid(),
                    'creation' => $now->toDateTimeString(),
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->email,
                    'owner' => Auth::user()->email,
                    'docstatus' => 0,
                    'parent' => $new_id,
                    'parentfield' => 'items',
                    'parenttype' => 'Stock Entry',
                    'idx' => 1,
                    't_warehouse' => $mes_production_order_details->wip_warehouse,
                    'transfer_qty' => $qty,
                    'serial_no' => null,
                    'expense_account' => 'Cost of Goods Sold - FI',
                    'cost_center' => 'Main - FI',
                    'actual_qty' => $actual_qty,
                    's_warehouse' => $request->source_warehouse[$id],
                    'item_name' => $item_details->item_name,
                    'image' => null,
                    'additional_cost' => 0,
                    'stock_uom' => $item_details->stock_uom,
                    'basic_amount' => $valuation_rate * $qty,
                    'sample_quantity' => 0,
                    'uom' => $item_details->stock_uom,
                    'basic_rate' => $valuation_rate,
                    'description' => $item_details->description,
                    'barcode' => null,
                    'conversion_factor' => 1,
                    'item_code' => $item_details->item_code,
                    'retain_sample' => 0,
                    'qty' => $qty,
                    'bom_no' => null,
                    'allow_zero_valuation_rate' => 0,
                    'material_request_item' => null,
                    'amount' => $valuation_rate * $qty,
                    'batch_no' => null,
                    'valuation_rate' => $valuation_rate,
                    'material_request' => null,
                    't_warehouse_personnel' => null,
                    's_warehouse_personnel' => null,
                    'target_warehouse_location' => null,
                    'source_warehouse_location' => null,
                    'status' => 'For Checking',
                    'date_modified' => null,
                    'session_user' => null,
                    'remarks' => null,
                    'status' => $item_status,
                    'date_modified' => ($item_status == 'Issued') ? $now->toDateTimeString() : null,
                    'session_user' => ($item_status == 'Issued') ? Auth::user()->employee_name : null,
                    'remarks' => ($item_status == 'Issued') ? 'MES' : null,
                    'issued_qty' => ($item_status == 'Issued') ? $qty : 0,
                ];

                $stock_entry_data = [
                    'name' => $new_id,
                    'creation' => $now->toDateTimeString(),
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->email,
                    'owner' => Auth::user()->email,
                    'docstatus' => 0,
                    'parent' => null,
                    'parentfield' => null,
                    'parenttype' => null,
                    'idx' => 0,
                    'use_multi_level_bom' => 1,
                    'delivery_note_no' => null,
                    'naming_series' => 'STE-',
                    'fg_completed_qty' => $mes_production_order_details->qty_to_manufacture,
                    'letter_head' => null,
                    '_liked_by' => null,
                    'purchase_receipt_no' => null,
                    'posting_time' => $now->format('H:i:s'),
                    'customer_name' => null,
                    'to_warehouse' => $mes_production_order_details->wip_warehouse,
                    'title' => 'Material Transfer for Manufacture',
                    '_comments' => null,
                    'from_warehouse' => null,
                    'set_posting_time' => 0,
                    'purchase_order' => null,
                    'from_bom' => 1,
                    'supplier_address' => null,
                    'supplier' => null,
                    'source_address_display' => null,
                    'address_display' => null,
                    'source_warehouse_address' => null,
                    'value_difference' => 0,
                    'credit_note' => null,
                    'sales_invoice_no' => null,
                    'company' => 'FUMACO Inc.',
                    'target_warehouse_address' => null,
                    'customer_address' => null,
                    'total_outgoing_value' => collect($stock_entry_detail)->sum('basic_amount'),
                    'supplier_name' => null,
                    'remarks' => null,
                    '_user_tags' => null,
                    'total_additional_costs' => 0,
                    'customer' => null,
                    'bom_no' => $mes_production_order_details->bom_no,
                    'amended_from' => null,
                    'total_amount' => collect($stock_entry_detail)->sum('basic_amount'),
                    'total_incoming_value' => collect($stock_entry_detail)->sum('basic_amount'),
                    'project' => $mes_production_order_details->project,
                    '_assign' => null,
                    'select_print_heading' => null,
                    'posting_date' => $now->format('Y-m-d'),
                    'target_address_display' => null,
                    'production_order' => $request->production_order,
                    'purpose' => 'Material Transfer for Manufacture',
                    'shipping_address_contact_person' => null,
                    'customer_1' => null,
                    'material_request' => $mes_production_order_details->material_request,
                    'reference_no' => null,
                    'delivery_date' => null,
                    'delivery_address' => null,
                    'city' => null,
                    'address_line_2' => null,
                    'address_line_1' => null,
                    'item_status' => 'For Checking',
                    'sales_order_no' => $mes_production_order_details->sales_order,
                    'transfer_as' => 'Internal Transfer',
                    'workflow_state' => null,
                    'item_classification' => $mes_production_order_details->item_classification,
                    'bom_repack' => null,
                    'qty_repack' => 0,
                    'issue_as' => null,
                    'receive_as' => null,
                    'so_customer_name' => $mes_production_order_details->customer,
                    'order_type' => $mes_production_order_details->classification,
                ];

                DB::connection('mysql')->table('tabStock Entry')->insert($stock_entry_data);
                DB::connection('mysql')->table('tabStock Entry Detail')->insert($stock_entry_detail);
            }
            
            DB::connection('mysql')->commit();

            return response()->json(['status' => 1, 'message' => 'Stock Entry has been created.']);
        } catch (Exception $e) {
            DB::connection('mysql')->rollback();
            return response()->json(['status' => 0, 'message' => 'There was a problem creating stock entries.']);
        }
    }

    public function update_production_projected_scrap($production_order, $projected_scrap){
        try {
            // projected_scrap in cubic mm
            DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order)
                ->update(['projected_scrap' => $projected_scrap]);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function cubic_mm_to_kg($material_type, $material_cubic_mm){
        $uom_cubic_mm = DB::connection('mysql_mes')->table('uom')->where('uom_name', 'like', '%cubic m%')->first();
        if(!$uom_cubic_mm){
            return 0;
        }

        $uom_cubic_kg = DB::connection('mysql_mes')->table('uom')->where('uom_name', 'like', '%kilogram%')->first();
        if(!$uom_cubic_kg){
            return 0;
        }

        $allowed_materials = ['CRS', 'ALUMINUM', 'DIFFUSER'];
        if(!in_array($material_type, $allowed_materials)){
            return 0;
        }

        $conversion_id = DB::connection('mysql_mes')->table('uom_conversion')
            ->whereIn('uom_id', [$uom_cubic_mm->uom_id, $uom_cubic_kg->uom_id])
            ->where('material', $material_type)
            ->select(DB::raw('COUNT(uom_id) as count'), 'uom_conversion_id')
            ->groupBy('uom_conversion_id')->first();
        
        if (!$conversion_id) {
            return 0;
        }

        $uom_2_conversion_factor = DB::connection('mysql_mes')->table('uom_conversion')
            ->where('uom_id', $uom_cubic_kg->uom_id)
            ->where('material', $material_type)
            ->where('uom_conversion_id', $conversion_id->uom_conversion_id)
            ->sum('conversion_factor');

        return $material_cubic_mm * $uom_2_conversion_factor;
    }

    public function get_available_scrap($item_code){
        $specs = $this->get_item_specs($item_code);

        return DB::connection('mysql_mes')->table('usable_scrap')
            ->join('scrap', 'scrap.scrap_id', 'usable_scrap.scrap_id')
            ->where('scrap.material', $specs['material'])
            ->where('scrap.thickness', $specs['thickness'])
            ->where('usable_scrap.length', '>=', (float)$specs['length'])
            ->where('usable_scrap.width', '>=', (float)$specs['width'])
            ->where('usable_scrap.usable_scrap_qty', '>', 0)
            ->get();
    }

    public function get_item_specs($item_code){
        $cutting_size_details = DB::connection('mysql')->table('tabItem Variant Attribute')
            ->where('parent', $item_code)->where('attribute', 'like', '%cutting size%')->first();

        $material_details = DB::connection('mysql')->table('tabItem Variant Attribute')
            ->where('parent', $item_code)->where('attribute', 'like', '%material%')->first();

        $material = $length = $width = $thickness = null;
        if($cutting_size_details){
            $attribte_arr = explode(" ", strtolower($cutting_size_details->attribute));
            if(!in_array('circular', $attribte_arr)){
                $dimension = strtolower(str_replace(' ', '', $cutting_size_details->attribute_value));
                $dimension_arr = explode("x", $dimension);
                $length = (array_key_exists(0, $dimension_arr)) ? preg_replace("/[^0-9,.]/", "", ($dimension_arr[0])) : 0;
                $width = (array_key_exists(1, $dimension_arr)) ? preg_replace("/[^0-9,.]/", "", ($dimension_arr[1])) : 0;
                $thickness = (array_key_exists(2, $dimension_arr)) ? preg_replace("/[^0-9,.]/", "", ($dimension_arr[2])) : 0;
            }
        }

        if($material_details){
            $material = $material_details->attribute_value;
        }

        return [
            'material' => $material,
            'length' => $length,
            'width' => $width,
            'thickness' => $thickness
        ];
    }

    public function calculate_item_cubic_mm($item_code, $qty){
        $item_details = DB::connection('mysql')->table('tabItem')->where('name', $item_code)->first();
        if($item_details->item_classification == 'CS - Crs Steel Coil'){
            $attr_values = DB::connection('mysql')->table('tabItem Variant Attribute')
                ->where('parent', $item_code)
                ->where(function($q){
                    $q->where('attribute', 'LIKE', '%thickness%')
                        ->orWhere('attribute', 'LIKE', '%length%')
                        ->orWhere('attribute', 'LIKE', '%width%');
                })->pluck('attribute_value');
            
            $cubic_mm = 1;
            foreach($attr_values as $value){
                $val = strtolower(str_replace(' ', '', $value));
                $val = preg_replace("/[^0-9,.]/", "", ($val));
                $cubic_mm *= $val;
            }

            return $cubic_mm * $qty;
        }

        $cutting_size_details = DB::connection('mysql')->table('tabItem Variant Attribute')
            ->where('parent', $item_code)->where('attribute', 'like', '%cutting size%')->first();

        $length = $width = $thickness = 0;
        if($cutting_size_details){
            $attribte_arr = explode(" ", strtolower($cutting_size_details->attribute));
            if(!in_array('circular', $attribte_arr)){
                $dimension = strtolower(str_replace(' ', '', $cutting_size_details->attribute_value));
                $dimension_arr = explode("x", $dimension);
                $length = (array_key_exists(0, $dimension_arr)) ? preg_replace("/[^0-9,.]/", "", ($dimension_arr[0])) : 0;
                $width = (array_key_exists(1, $dimension_arr)) ? preg_replace("/[^0-9,.]/", "", ($dimension_arr[1])) : 0;
                $thickness = (array_key_exists(2, $dimension_arr)) ? preg_replace("/[^0-9,.]/", "", ($dimension_arr[2])) : 0;
            }
        }

        $cubic_mm = ($length * $width * $thickness);

        return $cubic_mm * $qty;
    }

    public function get_no_of_sheets($item_code, $qty){
        $conversion = DB::connection('mysql')->table('tabUOM Conversion Detail')->where('parent', $item_code)->where('uom', 'Sheet(s)')->first();

        if ($conversion) {
            return ceil($qty / $conversion->conversion_factor);
        }
    }

    public function get_actual_qty($item_code, $warehouse){
        return DB::connection('mysql')->table('tabBin')->where('item_code', $item_code)
            ->where('warehouse', $warehouse)->sum('actual_qty');
    }

    public function create_material_request(Request $request){
        DB::connection('mysql')->beginTransaction();
        try {
            $now = Carbon::now();
            if (count($request->production_orders) <= 0) {
                return response()->json(['success' => 0, 'message' => 'No Material Request created.']);
            }

            $reference_no = DB::connection('mysql_mes')->table('production_order')
                ->where('production_order', $request->production_orders[0])->first();

            if (!$reference_no) {
                return response()->json(['success' => 0, 'message' => 'Production order reference not found.']);
            }

            $reference_no = ($reference_no->sales_order) ? $reference_no->sales_order : $reference_no->material_request;

            $reference_pref = preg_replace('/[0-9]+/', null, $reference_no);
            $reference_pref = str_replace("-", "", $reference_pref);
            $ref_table = ($reference_pref == 'SO') ? 'tabSales Order' : 'tabMaterial Request';
            $order_details = DB::table($ref_table)->where('name', $reference_no)->first();

            $latest_mr = DB::connection('mysql')->table('tabMaterial Request')->max('name');
            $latest_mr_exploded = explode("-", $latest_mr);
            $new_id = $latest_mr_exploded[1] + 1;
            $new_id = str_pad($new_id, 5, '0', STR_PAD_LEFT);
            $new_id = 'PREQ-'.$new_id;

            $mr = [
                'name' => $new_id,
                'creation' => $now->toDateTimeString(),
                'modified' => $now->toDateTimeString(),
                'modified_by' => Auth::user()->email,
                'owner' => Auth::user()->email,
                'docstatus' => 1,
                'naming_series' => 'MREQ-',
                'title' => 'Purchase',
                'transaction_date' => $now->toDateTimeString(),
                'status' => 'Pending',
                'company' => 'FUMACO Inc.',
                'schedule_date' => $now->format('Y-m-d'),
                'material_request_type' => 'Purchase',
                'delivery_date' => $order_details->delivery_date,
                'customer_name' => $order_details->customer,
                'sales_order' => ($reference_pref == 'SO') ? $reference_no : null,
                'project' => $order_details->project,
                'purchase_request' => 'Local',
            ];

            $items = DB::connection('mysql')->table('tabProduction Order Item')
                ->whereIn('parent', array_unique($request->production_orders))
                ->where('docstatus', 1)
                ->select('item_code', DB::raw('SUM(required_qty) as required_qty'))
                ->groupBy('item_code')->get();

            $mr_item = [];
            foreach ($items as $item) {
                $item_details = DB::connection('mysql')->table('tabItem')->where('name', $item->item_code)->first();
                if(!$item_details->default_warehouse){
                    return response()->json(['success' => 0, 'message' => 'Default warehouse for item ' . $item->item_code . ' not found.']);
                }

                $actual_qty = $this->get_actual_qty($item->item_code, $item_details->default_warehouse);

                if($actual_qty <= $item->required_qty){
                    $item_classes = ['HO - Housing', 'SA - Sub Assembly', 'RA - REFLECTOR ASSEMBLY'];
                    if(!in_array($item_details->item_classification, $item_classes) && strpos($item_details->item_classification, 'SA -') === false){
                        $required_qty = $item->required_qty - $actual_qty;
                        $mr_item[] = [
                            'name' => 'mes'.uniqid(),
                            'creation' => $now->toDateTimeString(),
                            'modified' => $now->toDateTimeString(),
                            'modified_by' => Auth::user()->email,
                            'owner' => Auth::user()->email,
                            'docstatus' => 1,
                            'parent' => $new_id,
                            'parentfield' => 'items',
                            'parenttype' => 'Material Request',
                            'idx' => 1,
                            'stock_qty' => abs($required_qty * 1),
                            'qty' => abs($required_qty),
                            'actual_qty' => $actual_qty,
                            'schedule_date' => $now->format('Y-m-d'),
                            'item_name' => $item_details->item_name,
                            'stock_uom' => $item_details->stock_uom,
                            'warehouse' => $item_details->default_warehouse,
                            'uom' => $item_details->stock_uom,
                            'description' => $item_details->description,
                            'conversion_factor' => 1,
                            'item_code' => $item_details->item_code,
                            'sales_order' => ($reference_pref == 'SO') ? $reference_no : null,
                            'item_group' => $item_details->item_group,
                            'project' => $order_details->project,
                        ];
                    }
                }
            }

            if(count($mr_item) > 0){
                DB::connection('mysql')->table('tabMaterial Request')->insert($mr);
                DB::connection('mysql')->table('tabMaterial Request Item')->insert($mr_item);   

                DB::connection('mysql_mes')->transaction(function() use ($request){
                    $production_orders = array_unique($request->production_orders);
                    DB::connection('mysql_mes')->table('production_order')
                        ->whereIn('production_order', $production_orders)->update(['material_requested' => 1]);
                });
                
                DB::connection('mysql')->commit();
            
                return response()->json(['success' => 1, 'message' => 'Material Request has been created.', 'id' => $new_id]);
            }

            return response()->json(['success' => 2, 'message' => 'No Material Request created.']);
        } catch (Exception $e) {
            DB::connection('mysql')->rollback();
            return response()->json(['success' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function manual_create_production_order(Request $request){
        DB::connection('mysql_mes')->beginTransaction();
        try {
            $now = Carbon::now();

            if($request->reference_type == 'SO' && !$request->sales_order){
                return response()->json(['success' => 0, 'message' => 'Please enter reference Sales Order.']);
            }

            if($request->reference_type == 'MREQ' && !$request->material_request){
                return response()->json(['success' => 0, 'message' => 'Please enter reference Material Request.']);
            }

            if ($request->qty <= 0) {
                return response()->json(['success' => 0, 'message' => 'Qty cannot be less than or equal to 0.']);
            }

            $reference_table = ($request->reference_type == 'SO') ? 'tabSales Order' : 'tabMaterial Request';
            $reference_name = ($request->reference_type == 'SO') ? $request->sales_order : $request->material_request;
            $reference_details = DB::connection('mysql')->table($reference_table)->where('name', $reference_name)->first();
            if(!$reference_details){
                return response()->json(['success' => 0, 'message' => $reference_name . ' does not exist.']);
            }
            
            $per_status = ($request->reference_type == 'SO') ? $reference_details->per_delivered : $reference_details->per_ordered;
            if($reference_details->docstatus > 1){
                return response()->json(['success' => 0, 'message' => $reference_name . ' was CANCELLED']);
            }

            if ($per_status >= 100) {
                return response()->json(['success' => 0, 'message' => $reference_name . ' was already COMPLETED']);
            }

            $item = DB::connection('mysql')->table('tabItem')->where('name', $request->item_code)->first();
            if (!$item) {
                return response()->json(['success' => 0, 'message' => 'Item ' .$request->item_code. ' not found.']);
            }

            $parent_item_code = ($request->parent_code) ? $request->parent_code : $request->item_code;
            $sub_parent_item_code = ($request->sub_parent_code) ? $request->sub_parent_code : $request->item_code;

            $parent_item_details = DB::connection('mysql')->table('tabItem')->where('name', $parent_item_code)->first();
            if (!$parent_item_details) {
                return response()->json(['success' => 0, 'message' => 'Parent Item ' .$parent_item_code. ' not found.']);
            }

            $sub_parent_item_details = DB::connection('mysql')->table('tabItem')->where('name', $sub_parent_item_code)->first();
            if (!$sub_parent_item_details) {
                return response()->json(['success' => 0, 'message' => 'Sub Parent Item ' .$sub_parent_item_code. ' not found.']);
            }

            $operation_details = DB::connection('mysql_mes')->table('operation')
                    ->where('operation_id', $request->operation_id)->first();

            $operation_id = $request->operation_id;
            if(!$request->custom_bom){
                if($request->is_stock_item){
                    $bom = DB::connection('mysql')->table('tabBOM')->where('name', $request->bom)->first();
                    if (!$bom) {
                        return response()->json(['success' => 0, 'message' => 'BOM ' .$request->bom. ' not found.']);
                    }

                    $bom_operations = DB::connection('mysql')->table('tabBOM Operation')->where('parent', $bom->name)->orderBy('idx', 'asc')->first();
                    $operation_details = DB::connection('mysql_mes')->table('operation')
                        ->where('operation_name', 'like', '%'.$bom_operations->operation.'%')->first();

                    if (!$operation_details) {
                        return response()->json(['success' => 0, 'message' => 'Operation ' . $request->operation . ' not found.']);
                    }

                    $operation_id = $operation_details->operation_id;

                    if ($request->is_reviewed == 0) {
                        return response()->json(['success' => 0, 'message' => 'Please review and update BOM.']);
                    }
                }
            }
            
            $wip_wh = $this->get_operation_wip_warehouse($operation_id);
            if ($wip_wh['success'] < 1) {
                return response()->json(['success' => 0, 'message' => $wip_wh['message']]);
            }

            $wip = $wip_wh['message'];

            $latest_pro = DB::connection('mysql')->table('tabProduction Order')->max('name');
            $latest_pro_exploded = explode("-", $latest_pro);
            $new_id = $latest_pro_exploded[1] + 1;
            $new_id = str_pad($new_id, 5, '0', STR_PAD_LEFT);
            $new_id = 'PROM-'.$new_id;

            $existing_id = DB::connection('mysql_mes')->table('production_order')->where('production_order', $new_id)->first();
            if ($existing_id) {
                return response()->json(['success' => 0, 'message' => 'Production Order <b>' . $new_id . '</b> already exist.']);
            }

            $item_details = DB::connection('mysql')->table('tabItem')->where('name', $request->item_code)->first();

            $data = [
                'name' => $new_id,
                'creation' => $now->toDateTimeString(),
                'modified' => $now->toDateTimeString(),
                'modified_by' => Auth::user()->email,
                'owner' => Auth::user()->email,
                'docstatus' => 1,
                'idx' => 0,
                'qty' => $request->qty,
                'fg_warehouse' => $request->target,
                'use_multi_level_bom' => 0,
                'material_transferred_for_manufacturing' => 0,
                'stock_uom' => $request->stock_uom,
                'naming_series' => 'PRO-',
                'status' => 'Not Started',
                'description' => $request->description,
                'company' => 'FUMACO Inc.',
                'production_item' => strtoupper($request->item_code),
                'sales_order_item' => null,
                'bom_no' => (!$request->custom_bom) ? $request->bom : null,
                'wip_warehouse' => $wip,
                'project' => $request->project,
                'scrap_warehouse' => 'Scrap Warehouse P1 - FI',
                'item_classification' => $request->item_classification,
                'delivery_date' => $request->delivery_date,
                'item_name' => $item_details->item_name,
                'customer' => $request->customer,
                'sales_order_no' => $request->sales_order,
                'sales_order' => $request->sales_order,
                'material_request' => $request->material_request,
                'scheduled' => ($request->planned_date) ? 1 : 0,
                'order_no' => 0,
                'priority' => 'Normal',
                'classification' => $request->classification,
                'parent_item_code' => strtoupper($parent_item_code),
                'planned_start_date' => ($request->planned_date) ? $request->planned_date : null,
            ];

            $params = DB::connection('mysql')->table('tabItem Variant Attribute')->where('parent', $request->item_code)
                ->where('attribute', 'LIKE', '%cutting size%')->first();

            $data_mes = [
                'production_order' => $new_id,
                'parent_item_code' => strtoupper($parent_item_code),
                'sub_parent_item_code' => strtoupper(($request->sub_parent_code) ? $request->sub_parent_code : $request->item_code),
                'item_code' => strtoupper($request->item_code),
                'description' => $request->description,
                'parts_category' => $item_details->parts_category,
                'item_classification' => $request->item_classification,
                'qty_to_manufacture' => $request->qty,
                'classification' => $request->classification,
                'order_no' => 0,
                'cutting_size' => ($params) ? $params->attribute_value : null,
                'is_scheduled' => ($request->planned_date) ? 1 : 0,
                'planned_start_date' => ($request->planned_date) ? $request->planned_date : null,
                'project' => $request->project,
                'bom_no' => (!$request->custom_bom) ? $request->bom : null,
                'sales_order' => $request->sales_order,
                'material_request' => $request->material_request,
                'delivery_date' => $request->delivery_date,
                'status' => 'Not Started',
                'stock_uom' => $request->stock_uom,
                'customer' => $request->customer,
                'wip_warehouse' => $wip,
                'fg_warehouse' => $request->target,
                'last_modified_at' => $now->toDateTimeString(),
                'last_modified_by' => Auth::user()->email,
                'created_by' => Auth::user()->email,
                'created_at' => $now->toDateTimeString(),
                'operation_id' => $operation_id,
                'is_stock_item' => $request->is_stock_item
            ];

            DB::connection('mysql_mes')->table('production_order')->insert($data_mes);
            if($request->custom_bom){
                $mes_custom_operations = [];
                foreach($request->workstation_id as $p => $w_id){
                    $workstation_details = DB::connection('mysql_mes')->table('workstation')->where('workstation_id', $w_id)->first();
                    $mes_custom_operations[] = [
                        'production_order' => $new_id,
                        'idx' => $p + 1,
                        'workstation' => $workstation_details->workstation_name,
                        'process_id' => $request->process_id[$p],
                        'planned_start_date' => ($request->planned_date) ? $request->planned_date : null,
                        'created_by' => Auth::user()->employee_name,
                        'created_at' => $now->toDateTimeString(),
                        'last_modified_by' => Auth::user()->employee_name,
                        'last_modified_at' => $now->toDateTimeString(),
                        'bom_operation_id' => null,
                    ];
                }

                DB::connection('mysql_mes')->table('job_ticket')->insert($mes_custom_operations);
            }else{
                $this->save_production_operations($new_id, $request->bom, ($request->planned_date) ? $request->planned_date : null, 'mes');
            }

            $reference_child_table = ($request->reference_type == 'SO') ? 'tabSales Order Item' : 'tabMaterial Request Item';
            $reference_parent = ($request->reference_type == 'SO') ? $request->sales_order : $request->material_request;
            $reference_child_details = DB::connection('mysql')->table($reference_child_table)
                ->where('parent', $reference_name)->where('item_code', $parent_item_code)->first();

            if($reference_child_details){
                $del_data = [
                    'erp_reference_id' => $reference_child_details->name,
                    'reference_no' => $reference_parent,
                    'parent_item_code' => $parent_item_code,
                    'delivery_date' => ($request->reference_type == 'SO') ? $reference_child_details->delivery_date : $reference_child_details->schedule_date
                ];

                $existing_del_data = DB::connection('mysql_mes')->table('delivery_date')
                    ->where('erp_reference_id', $reference_child_details->name)->where('parent_item_code', $parent_item_code)
                    ->exists();
                
                if(!$existing_del_data){
                    DB::connection('mysql_mes')->table('delivery_date')->insert($del_data);
                }
            }

            DB::connection('mysql')->beginTransaction();
            try{
                DB::connection('mysql')->table('tabProduction Order')->insert($data);

                if($request->custom_bom){
                    $raw_required_items = [];
                    if($request->is_stock_item > 0){
                        $req_item_detail = DB::connection('mysql')->table('tabItem')
                            ->where('name', $request->item_code)->first();
    
                        $raw_required_items = [
                            'name' => 'mes'.uniqid(),
                            'creation' => $now->toDateTimeString(),
                            'modified' => $now->toDateTimeString(),
                            'modified_by' => Auth::user()->email,
                            'owner' => Auth::user()->email,
                            'docstatus' => 1,
                            'parent' => $new_id,
                            'parentfield' => 'required_items',
                            'parenttype' => 'Production Order',
                            'idx' => 1,
                            'description' => $req_item_detail->description,
                            'item_name' => $req_item_detail->item_name,
                            'item_code' => $req_item_detail->item_code,
                            'required_qty' => $request->qty,
                            'transferred_qty' => 0,
                            'available_qty_at_source_warehouse' => 0,
                            'available_qty_at_wip_warehouse' => 0,
                            'source_warehouse' => $req_item_detail->default_warehouse,
                            'stock_uom' => $req_item_detail->stock_uom
                        ];
                    }else{
                        $bundle_items = DB::connection('mysql')->table('tabProduct Bundle Item')->where('parent', $request->item_code)->get();
                        foreach ($bundle_items as $k => $v) {
                            $req_item_detail = DB::connection('mysql')->table('tabItem')
                                ->where('name', $v->item_code)->first();

                            $raw_required_items[] = [
                                'name' => 'mes'.uniqid(),
                                'creation' => $now->toDateTimeString(),
                                'modified' => $now->toDateTimeString(),
                                'modified_by' => Auth::user()->email,
                                'owner' => Auth::user()->email,
                                'docstatus' => 1,
                                'parent' => $new_id,
                                'parentfield' => 'required_items',
                                'parenttype' => 'Production Order',
                                'idx' => $k + 1,
                                'description' => $req_item_detail->description,
                                'item_name' => $req_item_detail->item_name,
                                'item_code' => $req_item_detail->item_code,
                                'required_qty' => $v->qty * $request->qty,
                                'transferred_qty' => 0,
                                'available_qty_at_source_warehouse' => 0,
                                'available_qty_at_wip_warehouse' => 0,
                                'source_warehouse' => $req_item_detail->default_warehouse,
                                'stock_uom' => $v->uom
                            ];
                        }
                    }

                    DB::connection('mysql')->table('tabProduction Order Item')->insert($raw_required_items);

                    $custom_operations = [];
                    foreach($request->workstation_id as $p => $w_id){
                        $workstation_details = DB::connection('mysql_mes')->table('workstation')->where('workstation_id', $w_id)->first();
                        $process_details = DB::connection('mysql_mes')->table('process')->where('process_id', $request->process_id[$p])->first();
                        $custom_operations[] = [
                            'name' => 'mes'.uniqid(),
                            'creation' => $now->toDateTimeString(),
                            'modified' => $now->toDateTimeString(),
                            'modified_by' => Auth::user()->email,
                            'owner' => Auth::user()->email,
                            'docstatus' => 1,
                            'parent' => $new_id,
                            'parentfield' => 'operations',
                            'parenttype' => 'Production Order',
                            'idx' => $p + 1,
                            'status' => 'Pending',
                            'actual_start_time' => null,
                            'workstation' => $workstation_details->workstation_name,
                            'completed_qty' => 0,
                            'planned_operating_cost' => 0,
                            'description' => $workstation_details->workstation_name,
                            'actual_end_time' => null,
                            'actual_operating_cost' => 0,
                            'hour_rate' => 0,
                            'planned_start_time' => null,
                            'bom' => null,
                            'actual_operation_time' => 0,
                            'operation' => $operation_details->operation_name,
                            'planned_end_time' => null,
                            'time_in_mins' => 1,
                            'process' => $process_details->process_name,
                        ];
                    }
    
                    DB::connection('mysql')->table('tabProduction Order Operation')->insert($custom_operations);
                }else{
                    $required_items = $this->save_production_req_items($new_id, $request->bom, $request->qty, $request->operation);
                    $this->save_production_operations($new_id, $request->bom, ($request->planned_date) ? $request->planned_date : null, 'erp');

                    if($required_items['error'] == 1){
                        return response()->json(["success" => 0, 'message' => $required_items['message']]);
                    }
                }
                
                DB::connection('mysql')->commit();
            } catch (Exception $e) {
                DB::connection('mysql')->rollback();
                return response()->json(["success" => 0, 'message' => 'There was a problem creating production order.']);
            }
                   
            DB::connection('mysql_mes')->commit();
              
            return response()->json(["success" => 1, 'message' => $new_id]);
        } catch (Exception $e) {
            DB::connection('mysql_mes')->rollback();
            return response()->json(["success" => 0, 'message' => 'There was a problem creating production order.']);
        }
    }

    public function generate_stock_entry($production_order, Request $request){
        DB::connection('mysql')->beginTransaction();
        try {
            $now = Carbon::now();
            $mes_production_order_details = DB::connection('mysql_mes')->table('production_order')
                ->where('production_order', $production_order)->first();

            // get raw materials from production order items in erp
            $production_order_items = DB::connection('mysql')->table('tabProduction Order Item')
                ->where('parent', $production_order)->orderBy('idx', 'asc')->get();

            foreach ($production_order_items as $index => $row) {
                if($request->s_warehouses){
                    $source_warehouse = $request->s_warehouses[$index];
                }else{
                    $source_warehouse = $row->source_warehouse;
                }

                $pending_ste = DB::connection('mysql')->table('tabStock Entry Detail as sted')
                    ->join('tabStock Entry as ste', 'ste.name', 'sted.parent')
                    ->where('sted.item_code', $row->item_code)->where('ste.production_order', $row->parent)
                    ->where('ste.docstatus', 0)->first();

                if(!$pending_ste){
                    $remaining_qty = $row->required_qty - $row->transferred_qty;

                    $issued_qty = DB::connection('mysql')->table('tabStock Entry as ste')
                        ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                        ->where('ste.production_order', $row->parent)
                        ->where('sted.item_code', $row->item_code)
                        ->where('sted.s_warehouse', $source_warehouse)
                        ->where('ste.docstatus', 0)
                        ->where('sted.status', 'Issued')->sum('sted.qty');

                    $remaining_qty = $remaining_qty - $issued_qty;
                    if($remaining_qty > 0){
                        $latest_ste = DB::connection('mysql')->table('tabStock Entry')->max('name');
                        $latest_ste_exploded = explode("-", $latest_ste);
                        $new_id = $latest_ste_exploded[1] + 1;
                        $new_id = str_pad($new_id, 5, '0', STR_PAD_LEFT);
                        $new_id = 'STEM-'.$new_id;
                        
                        $bom_material = DB::connection('mysql')->table('tabBOM Item')
                            ->where('parent', $mes_production_order_details->bom_no)
                            ->where('item_code', $row->item_code)->first();

                        if(!$bom_material){
                            $valuation_rate = DB::connection('mysql')->table('tabBin')
                                ->where('item_code', $row->item_code)
                                ->where('warehouse', $source_warehouse)
                                ->sum('valuation_rate');
                        }

                        $base_rate = ($bom_material) ? $bom_material->base_rate : $valuation_rate;

                        $actual_qty = DB::connection('mysql')->table('tabBin')
                            ->where('item_code', $row->item_code)->where('warehouse', $source_warehouse)
                            ->sum('actual_qty');

                        if(in_array($source_warehouse, ['Fabrication - FI', 'Spotwelding Warehouse - FI']) && $mes_production_order_details->operation_id == 1){
                            $item_status = 'Issued';
                        }else{
                            $item_status = 'For Checking';
                        }

                        $docstatus = ($actual_qty >= $row->required_qty) ? 1 : 0;
                        $docstatus = ($item_status == 'Issued') ? $docstatus : 0;
            
                        $stock_entry_detail = [
                            'name' =>  uniqid(),
                            'creation' => $now->toDateTimeString(),
                            'modified' => $now->toDateTimeString(),
                            'modified_by' => Auth::user()->email,
                            'owner' => Auth::user()->email,
                            'docstatus' => $docstatus,
                            'parent' => $new_id,
                            'parentfield' => 'items',
                            'parenttype' => 'Stock Entry',
                            'idx' => $index + 1,
                            't_warehouse' => $mes_production_order_details->wip_warehouse,
                            'transfer_qty' => $remaining_qty,
                            'serial_no' => null,
                            'expense_account' => 'Cost of Goods Sold - FI',
                            'cost_center' => 'Main - FI',
                            'actual_qty' => $actual_qty,
                            's_warehouse' => $source_warehouse,
                            'item_name' => $row->item_name,
                            'image' => null,
                            'additional_cost' => 0,
                            'stock_uom' => $row->stock_uom,
                            'basic_amount' => $base_rate * $remaining_qty,
                            'sample_quantity' => 0,
                            'uom' => $row->stock_uom,
                            'basic_rate' => $base_rate,
                            'description' => $row->description,
                            'barcode' => null,
                            'conversion_factor' => ($bom_material) ? $bom_material->conversion_factor : 1,
                            'item_code' => $row->item_code,
                            'retain_sample' => 0,
                            'qty' => $remaining_qty,
                            'bom_no' => null,
                            'allow_zero_valuation_rate' => 0,
                            'material_request_item' => null,
                            'amount' => $base_rate * $remaining_qty,
                            'batch_no' => null,
                            'valuation_rate' => $base_rate,
                            'material_request' => null,
                            't_warehouse_personnel' => null,
                            's_warehouse_personnel' => null,
                            'target_warehouse_location' => null,
                            'source_warehouse_location' => null,
                            'status' => $item_status,
                            'date_modified' => ($item_status == 'Issued') ? $now->toDateTimeString() : null,
                            'session_user' => ($item_status == 'Issued') ? Auth::user()->employee_name : null,
                            'remarks' => ($item_status == 'Issued') ? 'MES' : null,
                            'production_order_req_item_id' => $row->name,
                            'issued_qty' => ($item_status == 'Issued') ? $remaining_qty : 0,
                        ];

                        $stock_entry_data = [
                            'name' => $new_id,
                            'creation' => $now->toDateTimeString(),
                            'modified' => $now->toDateTimeString(),
                            'modified_by' => Auth::user()->email,
                            'owner' => Auth::user()->email,
                            'docstatus' => $docstatus,
                            'parent' => null,
                            'parentfield' => null,
                            'parenttype' => null,
                            'idx' => 0,
                            'use_multi_level_bom' => 1,
                            'delivery_note_no' => null,
                            'naming_series' => 'STE-',
                            'fg_completed_qty' => $mes_production_order_details->qty_to_manufacture,
                            'letter_head' => null,
                            '_liked_by' => null,
                            'purchase_receipt_no' => null,
                            'posting_time' => $now->format('H:i:s'),
                            'customer_name' => null,
                            'to_warehouse' => $mes_production_order_details->wip_warehouse,
                            'title' => 'Material Transfer for Manufacture',
                            '_comments' => null,
                            'from_warehouse' => null,
                            'set_posting_time' => 0,
                            'purchase_order' => null,
                            'from_bom' => 1,
                            'supplier_address' => null,
                            'supplier' => null,
                            'source_address_display' => null,
                            'address_display' => null,
                            'source_warehouse_address' => null,
                            'value_difference' => 0,
                            'credit_note' => null,
                            'sales_invoice_no' => null,
                            'company' => 'FUMACO Inc.',
                            'target_warehouse_address' => null,
                            'customer_address' => null,
                            'total_outgoing_value' => collect($stock_entry_detail)->sum('basic_amount'),
                            'supplier_name' => null,
                            'remarks' => null,
                            '_user_tags' => null,
                            'total_additional_costs' => 0,
                            'customer' => null,
                            'bom_no' => $mes_production_order_details->bom_no,
                            'amended_from' => null,
                            'total_amount' => collect($stock_entry_detail)->sum('basic_amount'),
                            'total_incoming_value' => collect($stock_entry_detail)->sum('basic_amount'),
                            'project' => $mes_production_order_details->project,
                            '_assign' => null,
                            'select_print_heading' => null,
                            'posting_date' => $now->format('Y-m-d'),
                            'target_address_display' => null,
                            'production_order' => $production_order,
                            'purpose' => 'Material Transfer for Manufacture',
                            'shipping_address_contact_person' => null,
                            'customer_1' => null,
                            'material_request' => $mes_production_order_details->material_request,
                            'reference_no' => null,
                            'delivery_date' => null,
                            'delivery_address' => null,
                            'city' => null,
                            'address_line_2' => null,
                            'address_line_1' => null,
                            'item_status' => $item_status,
                            'sales_order_no' => $mes_production_order_details->sales_order,
                            'transfer_as' => 'Internal Transfer',
                            'workflow_state' => null,
                            'item_classification' => $mes_production_order_details->item_classification,
                            'bom_repack' => null,
                            'qty_repack' => 0,
                            'issue_as' => null,
                            'receive_as' => null,
                            'so_customer_name' => $mes_production_order_details->customer,
                            'order_type' => $mes_production_order_details->classification,
                        ];
            
                        DB::connection('mysql')->table('tabStock Entry Detail')->insert($stock_entry_detail);
                        DB::connection('mysql')->table('tabStock Entry')->insert($stock_entry_data);
                        
                        if ($docstatus == 1) {
                            $production_order_item = [
                                'transferred_qty' => $row->required_qty
                            ];
            
                            DB::connection('mysql')->table('tabProduction Order Item')->where('name', $row->name)->update($production_order_item);

                            if($mes_production_order_details->status == 'Not Started'){
                                DB::connection('mysql')->table('tabProduction Order')
                                    ->where('name', $mes_production_order_details->production_order)
                                    ->update(['status' => 'In Process', 'material_transferred_for_manufacturing' => $mes_production_order_details->qty_to_manufacture]);
                            }
                
                            $this->update_bin($new_id);
                            $this->create_stock_ledger_entry($new_id);
                            $this->create_gl_entry($new_id);
                        }
                    }
                }
            }

            DB::connection('mysql')->commit();

            return response()->json(['success' => 1, 'message' => 'Stock Entry has been created.']);
        } catch (Exception $e) {
            DB::connection('mysql')->rollback();
            return response()->json(['success' => 0, 'message' => 'There was a problem creating stock entries.']);
        }
    }

    // stock ledger for material transfer for manufacture
    public function create_stock_ledger_entry($stock_entry){
        try {
            $now = Carbon::now();
            $latest_id = DB::connection('mysql')->table('tabStock Ledger Entry')->max('name');
            $latest_id_exploded = explode("/", $latest_id);
            $new_id = $latest_id_exploded[1] + 1;

            $stock_entry_qry = DB::connection('mysql')->table('tabStock Entry')->where('name', $stock_entry)->first();

            $stock_entry_detail = DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $stock_entry)->get();

            $s_data = [];
            $t_data = [];
            foreach ($stock_entry_detail as $row) {
                $new_id = $new_id + 1;
                $new_id = str_pad($new_id, 8, '0', STR_PAD_LEFT);
                $id = 'SLEM/'.$new_id;
                
                $bin_qry = DB::connection('mysql')->table('tabBin')->where('warehouse', $row->s_warehouse)
                    ->where('item_code', $row->item_code)->first();
                
                if ($bin_qry) {
                    $actual_qty = $bin_qry->actual_qty;
                    $valuation_rate = $bin_qry->valuation_rate;
                }
                    
                $s_data[] = [
                    'name' => $id,
                    'creation' => $now->toDateTimeString(),
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->email,
                    'owner' => Auth::user()->email,
                    'docstatus' => 1,
                    'parent' => null,
                    'parentfield' => null,
                    'parenttype' => null,
                    'idx' => 0,
                    'serial_no' => $row->serial_no,
                    'fiscal_year' => $now->format('Y'),
                    'voucher_type' => 'Stock Entry',
                    'posting_time' => $now->format('H:i:s'),
                    'actual_qty' => $row->qty * -1,
                    'stock_value' => $actual_qty * $valuation_rate,
                    '_comments' => null,
                    'incoming_rate' => 0,
                    'voucher_detail_no' => $row->name,
                    'stock_uom' => $row->stock_uom,
                    'warehouse' => $row->s_warehouse,
                    '_liked_by' => null,
                    'company' => 'FUMACO Inc.',
                    '_assign' => null,
                    'item_code' => $row->item_code,
                    'valuation_rate' => $valuation_rate,
                    'project' => $stock_entry_qry->project,
                    'voucher_no' => $row->parent,
                    'outgoing_rate' => 0,
                    'is_cancelled' => 'No',
                    'qty_after_transaction' => $actual_qty,
                    '_user_tags' => null,
                    'batch_no' => $row->batch_no,
                    'stock_value_difference' => ($row->qty * $row->valuation_rate) * -1,
                    'posting_date' => $now->format('Y-m-d'),
                ];
                
                $bin_qry = DB::connection('mysql')->table('tabBin')->where('warehouse', $row->t_warehouse)
                    ->where('item_code', $row->item_code)->first();

                if ($bin_qry) {
                    $actual_qty = $bin_qry->actual_qty;
                    $valuation_rate = $bin_qry->valuation_rate;
                }
                
                $new_id = $new_id + 1;
                $new_id = str_pad($new_id, 8, '0', STR_PAD_LEFT);
                $id = 'SLEM/'.$new_id;

                $t_data[] = [
                    'name' => $id,
                    'creation' => $now->toDateTimeString(),
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->email,
                    'owner' => Auth::user()->email,
                    'docstatus' => 1,
                    'parent' => null,
                    'parentfield' => null,
                    'parenttype' => null,
                    'idx' => 0,
                    'serial_no' => $row->serial_no,
                    'fiscal_year' => $now->format('Y'),
                    'voucher_type' => 'Stock Entry',
                    'posting_time' => $now->format('H:i:s'),
                    'actual_qty' => $row->qty,
                    'stock_value' => $actual_qty * $valuation_rate,
                    '_comments' => null,
                    'incoming_rate' => $row->basic_rate,
                    'voucher_detail_no' => $row->name,
                    'stock_uom' => $row->stock_uom,
                    'warehouse' => $row->t_warehouse,
                    '_liked_by' => null,
                    'company' => 'FUMACO Inc.',
                    '_assign' => null,
                    'item_code' => $row->item_code,
                    'valuation_rate' => $valuation_rate,
                    'project' => $stock_entry_qry->project,
                    'voucher_no' => $row->parent,
                    'outgoing_rate' => 0,
                    'is_cancelled' => 'No',
                    'qty_after_transaction' => $actual_qty,
                    '_user_tags' => null,
                    'batch_no' => $row->batch_no,
                    'stock_value_difference' => $row->qty * $row->valuation_rate,
                    'posting_date' => $now->format('Y-m-d'),
                ];
            }

            $stock_ledger_entry = array_merge($s_data, $t_data);

            DB::connection('mysql')->table('tabStock Ledger Entry')->insert($stock_ledger_entry);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage(), 'id' => $stock_entry]);
        }
    }

    public function update_bin($stock_entry){
        try {
            $now = Carbon::now();

            $stock_entry_detail = DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $stock_entry)->get();

            $latest_id = DB::connection('mysql')->table('tabBin')->max('name');
            $latest_id_exploded = explode("/", $latest_id);
            $new_id = $latest_id_exploded[1] + 1;

            $stock_entry_qry = DB::connection('mysql')->table('tabStock Entry')->where('name', $stock_entry)->first();

            $stock_entry_detail = DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $stock_entry)->get();
            
            $s_data_insert = [];
            $d_data = [];
            foreach($stock_entry_detail as $row){
               
                    if($row->s_warehouse){
                        $bin_qry = DB::connection('mysql')->table('tabBin')->where('warehouse', $row->s_warehouse)
                        ->where('item_code', $row->item_code)->first();
                    if (!$bin_qry) {
                               
                        $new_id = $new_id + 1;
                        $new_id = str_pad($new_id, 7, '0', STR_PAD_LEFT);
                        $id = 'BINM/'.$new_id;

                        $bin = [
                            'name' => $id,
                            'creation' => $now->toDateTimeString(),
                            'modified' => $now->toDateTimeString(),
                            'modified_by' => Auth::user()->email,
                            'owner' => Auth::user()->email,
                            'docstatus' => 0,
                            'parent' => null,
                            'parentfield' => null,
                            'parenttype' => null,
                            'idx' => 0,
                            'reserved_qty_for_production' => 0,
                            '_liked_by' => null,
                            'fcfs_rate' => 0,
                            'reserved_qty' => 0,
                            '_assign' => null,
                            'planned_qty' => 0,
                            'item_code' => $row->item_code,
                            'actual_qty' => $row->transfer_qty,
                            'projected_qty' => $row->transfer_qty,
                            'ma_rate' => 0,
                            'stock_uom' => $row->stock_uom,
                            '_comments' => null,
                            'ordered_qty' => 0,
                            'reserved_qty_for_sub_contract' => 0,
                            'indented_qty' => 0,
                            'warehouse' => $row->s_warehouse,
                            'stock_value' => $row->valuation_rate * $row->transfer_qty,
                            '_user_tags' => null,
                            'valuation_rate' => $row->valuation_rate,
                        ];

                        DB::connection('mysql')->table('tabBin')->insert($bin);
                    }else{
                        $bin = [
                            'modified' => $now->toDateTimeString(),
                            'modified_by' => Auth::user()->email,
                            'actual_qty' => $bin_qry->actual_qty - $row->transfer_qty,
                            'stock_value' => $bin_qry->valuation_rate * $row->transfer_qty,
                            'valuation_rate' => $bin_qry->valuation_rate,
                        ];
        
                        DB::connection('mysql')->table('tabBin')->where('name', $bin_qry->name)->update($bin);
                    }
                    
                }

                if($row->t_warehouse){
                    $bin_qry = DB::connection('mysql')->table('tabBin')->where('warehouse', $row->t_warehouse)
                        ->where('item_code', $row->item_code)->first();
                    if (!$bin_qry) {
                        
                        $new_id = $new_id + 1;
                        $new_id = str_pad($new_id, 7, '0', STR_PAD_LEFT);
                        $id = 'BINM/'.$new_id;

                        $bin = [
                            'name' => $id,
                            'creation' => $now->toDateTimeString(),
                            'modified' => $now->toDateTimeString(),
                            'modified_by' => Auth::user()->email,
                            'owner' => Auth::user()->email,
                            'docstatus' => 0,
                            'parent' => null,
                            'parentfield' => null,
                            'parenttype' => null,
                            'idx' => 0,
                            'reserved_qty_for_production' => 0,
                            '_liked_by' => null,
                            'fcfs_rate' => 0,
                            'reserved_qty' => 0,
                            '_assign' => null,
                            'planned_qty' => 0,
                            'item_code' => $row->item_code,
                            'actual_qty' => $row->transfer_qty,
                            'projected_qty' => $row->transfer_qty,
                            'ma_rate' => 0,
                            'stock_uom' => $row->stock_uom,
                            '_comments' => null,
                            'ordered_qty' => 0,
                            'reserved_qty_for_sub_contract' => 0,
                            'indented_qty' => 0,
                            'warehouse' => $row->t_warehouse,
                            'stock_value' => $row->valuation_rate * $row->transfer_qty,
                            '_user_tags' => null,
                            'valuation_rate' => $row->valuation_rate,
                        ];

                        DB::connection('mysql')->table('tabBin')->insert($bin);
                    }else{
                        $bin = [
                            'modified' => $now->toDateTimeString(),
                            'modified_by' => Auth::user()->email,
                            'actual_qty' => $bin_qry->actual_qty + $row->transfer_qty,
                            'stock_value' => $bin_qry->valuation_rate * $row->transfer_qty,
                            'valuation_rate' => $bin_qry->valuation_rate,
                        ];
        
                        DB::connection('mysql')->table('tabBin')->where('name', $bin_qry->name)->update($bin);
                    }
                }
            }
            
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage(), 'id' => $stock_entry]);
        }
    }
    
    public function create_gl_entry($stock_entry){
        try {
            $now = Carbon::now();
            $stock_entry_qry = DB::connection('mysql')->table('tabStock Entry')->where('name', $stock_entry)->first();
            $credit_qry = DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $stock_entry)
                ->select('s_warehouse', DB::raw('SUM(basic_amount) as basic_amount'), 'parent', 'cost_center', 'expense_account')
                ->groupBy('s_warehouse', 'parent', 'cost_center', 'expense_account')
                ->get();

            $debit_qry = DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $stock_entry)
                ->select('t_warehouse', DB::raw('SUM(basic_amount) as basic_amount'), 'parent', 'cost_center', 'expense_account')
                ->groupBy('t_warehouse', 'parent', 'cost_center', 'expense_account')
                ->get();
            
            $latest_name = DB::connection('mysql')->table('tabGL Entry')->max('name');
            $latest_name_exploded = explode("L", $latest_name);
            $new_id = $latest_name_exploded[1] + 1;

            $id = [];
            $credit_data = [];
            $debit_data = [];

            foreach ($credit_qry as $row) {
                $new_id = $new_id + 1;
                $new_id = str_pad($new_id, 7, '0', STR_PAD_LEFT);

                $credit_data[] = [
                    'name' => 'MGL'.$new_id,
                    'creation' => $now->toDateTimeString(),
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->email,
                    'owner' => Auth::user()->email,
                    'docstatus' => 1,
                    'parent' => null,
                    'parentfield' => null,
                    'parenttype' => null,
                    'idx' => 0,
                    'fiscal_year' => $now->format('Y'),
                    'voucher_no' => $row->parent,
                    'cost_center' => $row->cost_center,
                    'credit' => $row->basic_amount,
                    'party_type' => null,
                    'transaction_date' => null,
                    'debit' => 0,
                    'party' => null,
                    '_liked_by' => null,
                    'company' => 'FUMACO Inc.',
                    '_assign' => null,
                    'voucher_type' => 'Stock Entry',
                    '_comments' => null,
                    'is_advance' => 'No',
                    'remarks' => 'Accounting Entry for Stock',
                    'account_currency' => 'PHP',
                    'debit_in_account_currency' => 0,
                    '_user_tags' => null,
                    'account' => $row->s_warehouse,
                    'against_voucher_type' => null,
                    'against' => $row->expense_account,
                    'project' => $stock_entry_qry->project,
                    'against_voucher' => null,
                    'is_opening' => 'No',
                    'posting_date' => $stock_entry_qry->posting_date,
                    'credit_in_account_currency' => $row->basic_amount,
                    'total_allocated_amount' => 0,
                    'reference_no' => null,
                    'mode_of_payment' => null,
                    'order_type' => null,
                    'po_no' => null,
                    'reference_date' => null,
                    'cr_ref_no' => null,
                    'or_ref_no' => null,
                    'dr_ref_no' => null,
                    'pr_ref_no' => null,
                ];
            }

            foreach ($debit_qry as $row) {
                $new_id = $new_id + 1;
                $new_id = str_pad($new_id, 7, '0', STR_PAD_LEFT);

                $debit_data[] = [
                    'name' => 'MGL'.$new_id,
                    'creation' => $now->toDateTimeString(),
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->email,
                    'owner' => Auth::user()->email,
                    'docstatus' => 1,
                    'parent' => null,
                    'parentfield' => null,
                    'parenttype' => null,
                    'idx' => 0,
                    'fiscal_year' => $now->format('Y'),
                    'voucher_no' => $row->parent,
                    'cost_center' => $row->cost_center,
                    'credit' => 0,
                    'party_type' => null,
                    'transaction_date' => null,
                    'debit' => $row->basic_amount,
                    'party' => null,
                    '_liked_by' => null,
                    'company' => 'FUMACO Inc.',
                    '_assign' => null,
                    'voucher_type' => 'Stock Entry',
                    '_comments' => null,
                    'is_advance' => 'No',
                    'remarks' => 'Accounting Entry for Stock',
                    'account_currency' => 'PHP',
                    'debit_in_account_currency' => $row->basic_amount,
                    '_user_tags' => null,
                    'account' => $row->t_warehouse,
                    'against_voucher_type' => null,
                    'against' => $row->expense_account,
                    'project' => $stock_entry_qry->project,
                    'against_voucher' => null,
                    'is_opening' => 'No',
                    'posting_date' => $stock_entry_qry->posting_date,
                    'credit_in_account_currency' => 0,
                    'total_allocated_amount' => 0,
                    'reference_no' => null,
                    'mode_of_payment' => null,
                    'order_type' => null,
                    'po_no' => null,
                    'reference_date' => null,
                    'cr_ref_no' => null,
                    'or_ref_no' => null,
                    'dr_ref_no' => null,
                    'pr_ref_no' => null,
                ];
            }

            $gl_entry = array_merge($credit_data, $debit_data);

            DB::connection('mysql')->table('tabGL Entry')->insert($gl_entry);
            
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage(), 'id' => $stock_entry]);
        }
    }

    public function insert_scrap_used(Request $request){
        try {
            $production_order_details = DB::connection('mysql_mes')->table('production_order')
                ->where('production_order', $request->production_order)->first();

            // check existing
            $existing_scrap_id = DB::connection('mysql_mes')->table('scrap_used')
                ->where('production_order', $request->production_order)->pluck('usable_scrap_id')->toArray();
            if ($production_order_details) {
                $scrap_used = [];
                foreach ($request->usable_scrap_id as $i => $id) {
                    if (!in_array($id, $existing_scrap_id)) {
                        if ($request->qty_scrap[$i] > 0) {
                            $scrap_used[] = [
                                'production_order' => $request->production_order,
                                'item_code' => $production_order_details->item_code,
                                'usable_scrap_id' => $id,
                                'qty' => $request->qty_scrap[$i]
                            ];
                        }
                    }
                }

                DB::connection('mysql_mes')->table('scrap_used')->insert($scrap_used);
            }
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function update_scrap(Request $request){
        try{
            foreach ($request->usable_scrap_id as $i => $id) {
                if($request->qty_scrap[$i] > $request->available_qty[$i]){
                    return response()->json(['success' => 0, 'message' => 'Scrap qty cannot be greater than available scrap.']);
                }
            }

            $projected_scrap_in_cubic_mm = $request->projected_scrap;
            foreach ($request->usable_scrap_id as $i => $id) {
                if ($request->qty_scrap[$i] > 0) {
                    $projected_scrap_in_cubic_mm += ($request->per_qty_in_cubic_mm[$i] - $request->per_item_cubic_mm) * $request->qty_scrap[$i];

                    $usable_scrap = $request->qty_scrap[$i] * $request->per_qty_in_cubic_mm[$i];

                    $usable_scrap_qty_after_transaction = $request->usable_scrap_qty[$i] - $usable_scrap;
                    
                    $data = [
                        'usable_scrap_qty' => $usable_scrap_qty_after_transaction,
                    ];
    
                    DB::connection('mysql_mes')->table('usable_scrap')->where('usable_scrap_id', $id)->update($data);
                }
            }

            $this->update_production_projected_scrap($request->production_order, $projected_scrap_in_cubic_mm);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }
    
    public function display_available_scrap($production_order){
        $production_order_details = DB::connection('mysql_mes')->table('production_order')
            ->where('production_order', $production_order)->first();

        $available_scrap_query = $this->get_available_scrap($production_order_details->item_code);

        $available_scrap = [];
        foreach($available_scrap_query as $row){
            $per_qty_in_cubic_mm = $row->length * $row->width * $row->thickness;
            $qty = round($row->usable_scrap_qty / $per_qty_in_cubic_mm);
            if($qty > 0){
                $available_scrap[] = [
                    'usable_scrap_id' => $row->usable_scrap_id,
                    'usable_scrap_qty' => $row->usable_scrap_qty,
                    'material' => $row->material,
                    'length' => $row->length,
                    'width' => $row->width,
                    'thickness' => $row->thickness,
                    'qty' => $qty,
                    'per_qty_in_cubic_mm' => $per_qty_in_cubic_mm
                ];
            }
        }

        $item_cm = $this->calculate_item_cubic_mm($production_order_details->item_code, 1);

        return view('wizard.tbl_select_available_scrap', compact('available_scrap', 'item_cm', 'production_order_details'));
    }

    public function production_planning_summary(Request $request){
        $production_orders = DB::connection('mysql')->table('tabProduction Order')->whereIn('name', $request->production_orders)
            ->where('docstatus', 1)->where('company', 'FUMACO Inc.')->orderBy('name', 'asc')->get();

        $production_order_list = [];
        foreach ($production_orders as $prod) {
            $production_order_list[] = [
                'production_order' => $prod->name,
                'parent_code' => $prod->parent_item_code,
                'item_code' => $prod->production_item,
                'description' => $prod->description,
                'bom_no' => $prod->bom_no,
                'qty' => $prod->qty,
                'stock_uom' => $prod->stock_uom,
                'planned_start_date' => $prod->planned_start_date,
                'is_scheduled' => $prod->scheduled
            ];
        }

        return view('wizard.tbl_planning_summary', compact('production_order_list'));
    }

    public function print_withdrawals(Request $request){
        $myArray = explode(',', $request->production_orders);
        $now = Carbon::now();
        $ste = DB::connection('mysql')->table('tabStock Entry')
            ->where('purpose', 'Material Transfer for Manufacture')
            ->whereIn('production_order', $myArray)
            ->where('docstatus',"<", 2)
            ->selectRaw('production_order,sales_order_no,material_request,so_customer_name,project,GROUP_CONCAT(name ORDER BY production_order SEPARATOR ",") as ste_name')
            ->groupBy('production_order', 'sales_order_no','material_request','so_customer_name','project')
            ->get();  //get parent_ste based on given production order
        $stock_entries=[];
        foreach ($ste as $row) {
            $ste_name = explode(',', $row->ste_name);// merge into one page/ withdrawal slip all ste with same production order
            $items = DB::connection('mysql')->table('tabStock Entry Detail')->whereIn('parent', $ste_name)->get();
            $stock_entries[] = [
                'sales_order' => $row->sales_order_no,
                'material_request' => $row->material_request,
                'production_order' => $row->production_order,
                'customer' => $row->so_customer_name,
                'project' => $row->project,
                'posting_date' => $now->format('y-m-d'),
                'items' => $items
            ];

            DB::connection('mysql_mes')->table('production_order')->where('production_order', $row->production_order)->update(['withdrawal_slip_print' => '1']);

        }
        if(empty($stock_entries)){ //validation if with no ste found
            return response()->json(['success' => 0, 'message' => 'No withdrawal slip(s) created']);
        }
        return view('selected_print_withdrawal', compact('stock_entries'));
    }
    // NEW (FOR BOM CRUD)
    public function view_bom_list(){
        return view('bom.index');
    }

    public function get_bom_list(Request $request){
        $bom_list = DB::connection('mysql')->table('tabBOM')
            ->when($request->search_string, function ($query) use ($request) {
                return $query->where('name', 'LIKE', '%'.$request->search_string.'%')
                    ->orWhere('item', 'LIKE', '%'.$request->search_string.'%');
            })
            ->orderBy('modified', 'desc')->paginate(10);

        return view('bom.table', compact('bom_list'));
    }

    public function get_bom_details($bom){
        try {
            $workstations = DB::connection('mysql_mes')
            ->table('workstation as w')
            ->join('operation as op', 'op.operation_id', 'w.operation_id')
            ->where('op.operation_name', 'Fabrication')->get();

            $workstation_process = DB::connection('mysql_mes')->table('process')
                ->join('process_assignment', 'process.process_id', 'process_assignment.process_id')
                ->join('workstation', 'workstation.workstation_id', 'process_assignment.workstation_id')
                ->select('workstation.workstation_name', 'process.process_name', 'process.process_id')
                ->distinct('workstation.workstation_name', 'process.process_name', 'process.process_id')
                ->orderBy('process.process_name', 'asc')->get();

            $bom_details = DB::connection('mysql')->table('tabBOM')->where('name', $bom)->first();
            $bom_operations = DB::connection('mysql')->table('tabBOM Operation')->where('parent', $bom)->orderBy('idx', 'asc')->get();
            $bom_materials = DB::connection('mysql')->table('tabBOM Item')->where('parent', $bom)->orderBy('idx', 'asc')->get();

            return view('bom.bom_details_tbl', compact('workstation_process', 'workstations', 'bom_details', 'bom_operations', 'bom_materials'));
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function submit_stock_entries($production_order){
        DB::connection('mysql')->beginTransaction();
        try {
            $now = Carbon::now();
            $pending_ste = DB::connection('mysql')->table('tabStock Entry')
                ->where('production_order', $production_order)->where('purpose', 'Material Transfer for Manufacture')
                ->where('docstatus', 0)->pluck('name');

            if(count($pending_ste) <= 0){
                return response()->json(['success' => 0, 'message' => 'No pending withdrawal slip(s) to submit.']);
            }

            foreach ($pending_ste as $ste) {
                $ste_d = DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $ste)->get();
                foreach ($ste_d as $row) {
                    $actual_qty = DB::connection('mysql')->table('tabBin')->where('item_code', $row->item_code)
                        ->where('warehouse', $row->s_warehouse)->sum('actual_qty');

                    if(!in_array($row->s_warehouse, ['Fabrication - FI', 'Spotwelding Warehouse - FI'])){
                        if($row->status != 'Issued'){
                            return response()->json(['success' => 0, 'message' => 'All item(s) must be issued.']);
                        }
                    }

                    if($row->qty > $actual_qty){
                        return response()->json(['success' => 0, 'message' => 'Insufficient stock for ' . $row->item_code . ' in ' . $row->s_warehouse]);
                    }
                }

                $values = [
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->email,
                    'docstatus' => 1
                ];

                DB::connection('mysql')->table('tabStock Entry')->where('name', $ste)->update($values);
                DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $ste)->update($values);

                $this->update_bin($ste);
                $this->create_stock_ledger_entry($ste);
                $this->create_gl_entry($ste);
            }

            $update_production_order_transferred_qty = $this->update_production_order_transferred_qty($production_order);
            if($update_production_order_transferred_qty['status'] == 0){
                return response()->json(['success' => 0, 'message' => 'Error updating production order transferred qty']);
            }

            DB::connection('mysql')->commit();

            return response()->json(['success' => 1, 'message' => 'Stock Entry has been submitted.']);
        } catch (Exception $e) {
            DB::connection('mysql')->rollback();
            return response()->json(['success' => 0, 'message' => 'There was a problem submitting stock entry.']);
        }
    }

    public function create_production_feedback_for_item_bundle($production_order, $fg_completed_qty){
        DB::connection('mysql')->beginTransaction();
		try {
			$existing_ste_transfer = DB::connection('mysql')->table('tabStock Entry')
				->where('production_order', $production_order)
				->where('purpose', 'Material Transfer for Manufacture')
				->where('docstatus', 1)->exists();

			if(!$existing_ste_transfer){
				return response()->json(['success' => 0, 'message' => 'Materials unavailable.']);
			}

			$production_order_details = DB::connection('mysql')->table('tabProduction Order')
				->where('name', $production_order)->first();

			$produced_qty = $production_order_details->produced_qty + $fg_completed_qty;
			if($produced_qty >= (int)$production_order_details->qty && $production_order_details->material_transferred_for_manufacturing > 0){
				$pending_mtfm_count = DB::connection('mysql')->table('tabStock Entry as ste')
					->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
					->where('ste.production_order', $production_order)->where('purpose', 'Material Transfer for Manufacture')
					->where('ste.docstatus', 0)->count();
				
				if($pending_mtfm_count > 0){
					return response()->json(['success' => 0, 'message' => 'There are pending material request for issue.']);
				}
			}

			$mes_production_order_details = DB::connection('mysql_mes')->table('production_order')
				->where('production_order', $production_order)->first();

			$now = Carbon::now();

			$latest_pro = DB::connection('mysql')->table('tabStock Entry')->max('name');
			$latest_pro_exploded = explode("-", $latest_pro);
			$new_id = $latest_pro_exploded[1] + 1;
			$new_id = str_pad($new_id, 5, '0', STR_PAD_LEFT);
			$new_id = 'STEM-'.$new_id;

			$production_order_items = DB::connection('mysql')->table('tabProduction Order Item')
				->where('parent', $production_order)->orderBy('idx', 'asc')->get();

			$receiving_warehouse = ['P2 - Housing Temporary - FI1'];
			$docstatus = (in_array($mes_production_order_details->fg_warehouse, $receiving_warehouse)) ? 0 : 1;

			if(count($production_order_items) < 1){
				return response()->json(['success' => 0, 'message' => 'Materials unavailable.']);
			}

			$stock_entry_detail = [];
			foreach ($production_order_items as $index => $row) {
                $base_rate = DB::connection('mysql')->table('tabBin')
                    ->where('item_code', $row->item_code)
                    ->where('warehouse', $production_order_details->wip_warehouse)
                    ->sum('valuation_rate');

				$qty_per_item = $row->required_qty / $mes_production_order_details->qty_to_manufacture;
				
				$qty = $qty_per_item * $fg_completed_qty;

				$actual_qty = DB::connection('mysql')->table('tabBin')->where('item_code', $row->item_code)
					->where('warehouse', $production_order_details->wip_warehouse)->sum('actual_qty');				

                if($qty > $actual_qty){
                    return response()->json(['success' => 0, 'message' => 'Insufficient stock for ' . $row->item_code . ' in ' . $production_order_details->wip_warehouse]);
                }
				
				$stock_entry_detail[] = [
					'name' =>  uniqid(),
					'creation' => $now->toDateTimeString(),
					'modified' => $now->toDateTimeString(),
					'modified_by' => Auth::user()->email,
					'owner' => Auth::user()->email,
					'docstatus' => 1,
					'parent' => $new_id,
					'parentfield' => 'items',
					'parenttype' => 'Stock Entry',
					'idx' => $index + 1,
					't_warehouse' => $production_order_details->fg_warehouse,
					'transfer_qty' => $qty,
					'serial_no' => null,
					'expense_account' => 'Cost of Goods Sold - FI',
					'cost_center' => 'Main - FI',
					'actual_qty' => 0,
					's_warehouse' => $production_order_details->wip_warehouse,
					'item_name' => $row->item_name,
					'image' => null,
					'additional_cost' => 0,
					'stock_uom' => $row->stock_uom,
					'basic_amount' => $base_rate * $qty,
					'sample_quantity' => 0,
					'uom' => $row->stock_uom,
					'basic_rate' => $base_rate,
					'description' => $row->description,
					'barcode' => null,
					'conversion_factor' => 1,
					'item_code' => $row->item_code,
					'retain_sample' => 0,
					'qty' => $qty,
					'bom_no' => null,
					'allow_zero_valuation_rate' => 0,
					'material_request_item' => null,
					'amount' => $base_rate * $qty,
					'batch_no' => null,
					'valuation_rate' => $base_rate,
					'material_request' => null,
					't_warehouse_personnel' => null,
					's_warehouse_personnel' => null,
					'target_warehouse_location' => null,
                    'source_warehouse_location' => null,
                    'status' => 'Issued',
                    'issued_qty' => $qty,
                    'date_modified' => $now->toDateTimeString(),
                    'session_user' => Auth::user()->employee_name,
                    'remarks' => 'MES',
				];
			}

			DB::connection('mysql')->table('tabStock Entry Detail')->insert($stock_entry_detail);

			$stock_entry_data = [
				'name' => $new_id,
				'creation' => $now->toDateTimeString(),
				'modified' => $now->toDateTimeString(),
				'modified_by' => Auth::user()->email,
				'owner' => Auth::user()->email,
				'docstatus' => 1,
				'parent' => null,
				'parentfield' => null,
				'parenttype' => null,
				'idx' => 0,
				'use_multi_level_bom' => 1,
				'delivery_note_no' => null,
				'naming_series' => 'STE-',
				'fg_completed_qty' => $fg_completed_qty,
				'letter_head' => null,
				'_liked_by' => null,
				'purchase_receipt_no' => null,
				'posting_time' => $now->format('H:i:s'),
				'customer_name' => null,
				'to_warehouse' => $production_order_details->fg_warehouse,
				'title' => 'Material Transfer',
				'_comments' => null,
				'from_warehouse' => null,
				'set_posting_time' => 0,
				'purchase_order' => null,
				'from_bom' => 1,
				'supplier_address' => null,
				'supplier' => null,
				'source_address_display' => null,
				'address_display' => null,
				'source_warehouse_address' => null,
				'value_difference' => 0,
				'credit_note' => null,
				'sales_invoice_no' => null,
				'company' => 'FUMACO Inc.',
				'target_warehouse_address' => null,
				'customer_address' => null,
				'total_outgoing_value' => collect($stock_entry_detail)->sum('basic_amount'),
				'supplier_name' => null,
				'remarks' => null,
				'_user_tags' => null,
				'total_additional_costs' => 0,
				'customer' => null,
				'bom_no' => $production_order_details->bom_no,
				'amended_from' => null,
				'total_amount' => collect($stock_entry_detail)->sum('basic_amount'),
				'total_incoming_value' => collect($stock_entry_detail)->sum('basic_amount'),
				'project' => $production_order_details->project,
				'_assign' => null,
				'select_print_heading' => null,
				'posting_date' => $now->format('Y-m-d'),
				'target_address_display' => null,
				'production_order' => $production_order,
				'purpose' => 'Material Transfer',
				'shipping_address_contact_person' => null,
				'customer_1' => null,
				'material_request' => $production_order_details->material_request,
				'reference_no' => null,
				'delivery_date' => null,
				'delivery_address' => null,
				'city' => null,
				'address_line_2' => null,
				'address_line_1' => null,
				'item_status' => 'Issued',
				'sales_order_no' => $mes_production_order_details->sales_order,
				'transfer_as' => 'Internal Transfer',
				'workflow_state' => null,
				'item_classification' => $production_order_details->item_classification,
				'bom_repack' => null,
				'qty_repack' => 0,
				'issue_as' => null,
				'receive_as' => null,
				'so_customer_name' => $mes_production_order_details->customer,
				'order_type' => $mes_production_order_details->classification,
			];

			DB::connection('mysql')->table('tabStock Entry')->insert($stock_entry_data);

			$produced_qty = $production_order_details->produced_qty + $fg_completed_qty;
			
            $production_data = [
                'modified' => $now->toDateTimeString(),
                'modified_by' => Auth::user()->email,
                'produced_qty' => $produced_qty,
                'status' => ($produced_qty == $production_order_details->qty) ? 'Completed' : $production_order_details->status
            ];

            DB::connection('mysql')->table('tabProduction Order')->where('name', $production_order)->update($production_data);

            $this->update_bin($new_id);
            $this->create_stock_ledger_entry($new_id);
            $this->create_gl_entry($new_id);
            
            DB::connection('mysql_mes')->transaction(function() use ($now, $fg_completed_qty, $production_order_details){
                $production_data_mes = [
                    'last_modified_at' => $now->toDateTimeString(),
                    'last_modified_by' => Auth::user()->email,
                    'feedback_qty' => $production_order_details->produced_qty + $fg_completed_qty,
                ];
    
                DB::connection('mysql_mes')->table('production_order')->where('production_order', $production_order_details->name)->update($production_data_mes);
            });

			$data = array(
                'posting_date'  => $now->format('Y-m-d'),
                'posting_time'  => $now->format('H:i:s'),
                'ste'           => $new_id,
				'sales_order_no'=> $mes_production_order_details->sales_order,
				'mreq'			=> $production_order_details->material_request,
                'item_code'     => $production_order_details->production_item,
				'item_name'     => $production_order_details->item_name,
				'customer'		=> $mes_production_order_details->customer,
				'feedbacked_by' => Auth::user()->email,
				'completed_qty' => $fg_completed_qty, 
				'uom'			=> $production_order_details->stock_uom
            );
            
			$recipient= DB::connection('mysql_mes')
                ->table('email_trans_recipient')
				->where('email_trans', "Feedbacking")
				->where('email', 'like','%@fumaco.local%')
                ->select('email')
                ->get();
			if(count($recipient) > 0){
				if($mes_production_order_details->parent_item_code == $mes_production_order_details->sub_parent_item_code && $mes_production_order_details->sub_parent_item_code == $mes_production_order_details->item_code){
					foreach ($recipient as $row) {
						Mail::to($row->email)->send(new SendMail_feedbacking($data));
					}	
				}
			}
			$feedbacked_timelogs = [
                'production_order'  => $mes_production_order_details->production_order,
                'ste_no'           => $new_id,
                'item_code'     => $production_order_details->production_item,
				'item_name'     => $production_order_details->item_name,
				'feedbacked_qty' => $fg_completed_qty, 
				'from_warehouse'=> $production_order_details->wip_warehouse,
				'to_warehouse' => $mes_production_order_details->fg_warehouse,
				'transaction_date'=>$now->format('Y-m-d'),
				'transaction_time' =>$now->format('G:i:s'),
				'created_at'  => $now->toDateTimeString(),
				'created_by'  =>  Auth::user()->email,
			];
			DB::connection('mysql_mes')->table('feedbacked_logs')->insert($feedbacked_timelogs);
			DB::connection('mysql')->commit();

			return response()->json(['success' => 1, 'message' => 'Stock Entry has been created.']);
		} catch (Exception $e) {
			DB::connection('mysql')->rollback();
			return response()->json(['success' => 0, 'message' => 'There was a problem create stock entry']);
		}
    }

    public function view_bundle_components($item_code){
        $bundle_details = DB::connection('mysql')->table('tabProduct Bundle as pb')
            ->join('tabItem as i', 'i.name', 'pb.name')->where('pb.name', $item_code)->first();

        if(!$bundle_details){
            return response()->json(['status' => 0, 'message' => 'No Product Bundle found for item ' . $item_code]);
        }

        $components = DB::connection('mysql')->table('tabProduct Bundle Item')->where('parent', $item_code)->orderBy('idx', 'asc')->get();
        $components_arr = [];
        foreach ($components as $row) {
            $item_details = DB::connection('mysql')->table('tabItem')->where('name', $row->item_code)->first();
            $image_src = 'http://athenaerp.fumaco.local/storage/';
            $image_path = ($item_details->item_image_path) ? $item_details->item_image_path : 'icon/no_img.png';
           
            $components_arr[] = [
                'idx' => $row->idx,
                'item_code' => $row->item_code,
                'image' => $image_src . $image_path,
                'description' => $row->description,
                'qty' => $row->qty,
                'uom' => $row->uom,
            ];
        }

        return view('tables.tbl_bundle_components', compact('bundle_details', 'components_arr'));
    }

    public function get_available_warehouse_qty($item_code){
        $inventory_stock = DB::connection('mysql')->table('tabBin')->where('item_code', $item_code)->where('actual_qty', '>', 0)->get();

        return view('tables.tbl_item_inventory', compact('inventory_stock'));
    }

    public function get_reason_for_cancellation(){
		return DB::connection('mysql_mes')->table('reason_for_cancellation_po')->orderBy('reason_for_cancellation', 'asc')->get();
    }
    
    public function cancel_production_order_feedback($stock_entry){
        DB::connection('mysql')->beginTransaction();
        try {
            $now = Carbon::now();
            $stock_entry_detail = DB::connection('mysql')->table('tabStock Entry')
                ->where('name', $stock_entry)->where('docstatus', 1)->where('purpose', 'Manufacture')->first();
            // check if stock entry (manufacture) exists
            if(!$stock_entry_detail){
                return response()->json(['status' => 0, 'message' => 'Production Order Feedback not found. Ref. No: <b>' . $stock_entry . '</b>']);
            }
            // get production order details
            $production_order_detail = DB::connection('mysql')->table('tabProduction Order')->where('name', $stock_entry_detail->production_order)->first();
            // check if production order exists
            if(!$production_order_detail){
                return response()->json(['status' => 0, 'message' => 'Production Order <b>' . $stock_entry . '</b> not found.']);
            }
            // get production order reference order no
            if(!$production_order_detail->material_request){
                $sales_order = ($production_order_detail->sales_order) ? $production_order_detail->sales_order : $production_order_detail->sales_order_no;
                // get sales order detail
                $sales_order_detail = DB::connection('mysql')->table('tabSales Order')->where('name', $sales_order)->first();
                // check if sales order exists
                if(!$sales_order_detail){
                    return response()->json(['status' => 0, 'message' => 'Sales Order <b>' . $sales_order . '</b> not found.']);
                }
                // check sales order if fully delivered
                if($sales_order_detail->per_delivered >= 100){
                    return response()->json(['status' => 0, 'message' => 'Unable to cancel feedback. <b>' . $sales_order . '</b> has been fully delivered.']);
                }
                // get total delivered qty per parent item code
                $delivered_qty = DB::connection('mysql')->table('tabSales Order Item')
                    ->where('parent', $sales_order)->where('item_code', $production_order_detail->parent_item_code)
                    ->sum('delivered_qty');
                // validate delivered qty per parent item code
                if($delivered_qty > $stock_entry_detail->fg_completed_qty){
                    return response()->json(['status' => 0, 'message' => 'Unable to cancel feedback. <b>' . $production_order_detail->parent_item_code . '</b> has been fully delivered.']);
                }
            }
            // get stock entry items
            $stock_entry_items = DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $stock_entry)->get();

            $bin = [];
            foreach ($stock_entry_items as $row) {
                if ($row->s_warehouse) {
                    $bin_qry = DB::connection('mysql')->table('tabBin')->where('warehouse', $row->s_warehouse)
                        ->where('item_code', $row->item_code)->first();

                    $actual_qty = $bin_qry->actual_qty + $row->transfer_qty;

                    $bin = [
                        'modified' => $now->toDateTimeString(),
                        'modified_by' => Auth::user()->email,
                        'actual_qty' => $actual_qty,
                        'stock_value' => $bin_qry->valuation_rate * $actual_qty,
                        'valuation_rate' => $bin_qry->valuation_rate,
                    ];

                    // update bin for stock entry item (raw materials)
                    DB::connection('mysql')->table('tabBin')->where('name', $bin_qry->name)->update($bin);
                }

                if ($row->t_warehouse) {
                    $bin_qry = DB::connection('mysql')->table('tabBin')->where('warehouse', $row->t_warehouse)
                        ->where('item_code', $row->item_code)->first();

                    $actual_qty = $bin_qry->actual_qty - $row->transfer_qty;

                    if($actual_qty < 0){
                        return response()->json(['status' => 0, 'message' => '<b>' . abs($actual_qty) . ' units of item <b>' . $row->item_code . '</b> in warehouse <b>' . $row->t_warehouse . '</b> to complete this transaction.']);
                    }

                    $bin = [
                        'modified' => $now->toDateTimeString(),
                        'modified_by' => Auth::user()->email,
                        'actual_qty' => $actual_qty,
                        'stock_value' => $bin_qry->valuation_rate * $actual_qty,
                        'valuation_rate' => $bin_qry->valuation_rate,
                    ];

                    // update bin for stock entry item (finished good)
                    DB::connection('mysql')->table('tabBin')->where('name', $bin_qry->name)->update($bin);
                }
            }

            // update stock entry parent and child table as cancelled
            $log = [
                'modified' => $now->toDateTimeString(),
                'modified_by' => Auth::user()->email,
                'docstatus' => 2
            ];

            DB::connection('mysql')->table('tabStock Entry')->where('name', $stock_entry)->update($log);
            DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $stock_entry)->update($log);
            // delete stock ledger and gl entries
            DB::connection('mysql')->table('tabGL Entry')->where('voucher_no', $stock_entry)->delete();
            DB::connection('mysql')->table('tabStock Ledger Entry')->where('voucher_no', $stock_entry)->delete();

            // get production order remaining feedbacked qty 
            $remaining_feedbacked_qty = $production_order_detail->produced_qty - $stock_entry_detail->fg_completed_qty;
            // update production order produced qty and status in ERP
            DB::connection('mysql')->table('tabProduction Order')
                ->where('name', $stock_entry_detail->production_order)->update(['produced_qty' => $remaining_feedbacked_qty, 'modified' => $now->toDateTimeString(),
                'modified_by' => Auth::user()->email, 'status' => 'In Process']);

            DB::connection('mysql_mes')->beginTransaction();
            // update production order feedbacked qty  in MES
            DB::connection('mysql_mes')->table('production_order')->where('production_order', $stock_entry_detail->production_order)
                ->update(['feedback_qty' => $remaining_feedbacked_qty, 'last_modified_at' => $now->toDateTimeString(), 'last_modified_by' => Auth::user()->email]);
            
             // update feedback logs as cancelled in MES
            DB::connection('mysql_mes')->table('feedbacked_logs')
                ->where('ste_no', $stock_entry)->update(['status' => 'Cancelled', 'last_modified_at' => $now->toDateTimeString(), 'last_modified_by' => Auth::user()->email]);

            DB::connection('mysql_mes')->commit();

            DB::connection('mysql')->commit();

            return response()->json(['status' => 1, 'message' => 'Production Order Feedback has been cancelled.']);
        } catch (Exception $th) {
            DB::connection('mysql_mes')->rollback();

            DB::connection('mysql')->rollback();

            return response()->json(['status' => 0, 'message' => 'There was a problem cancelling production order feedback.']);
        }
    }
}
