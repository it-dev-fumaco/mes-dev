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
use Session;

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
            if(!Auth::user()) {
                return response()->json(['message' => 'Session Expired. Please refresh the page and login to continue.']);
            }

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
            if(!Auth::user()) {
                return response()->json(['message' => 'Session Expired. Please refresh the page and login to continue.']);
            }
            
            $so = DB::connection('mysql')->table('tabSales Order')->where('name', $id)->where('docstatus', 1)->first();
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

            $item_details = DB::connection('mysql')->table('tabItem')->whereIn('name', collect($bom)->pluck('item_code'))->get();

            $sub_assemblies = collect($item_details)->map(function ($q){
                if(false !== stripos($q->item_classification, 'SA - ')){
                    return $q->name;
                }
            })->filter()->values()->all();
            
            $item_details = collect($item_details)->groupBy('name');

            $default_bom_arr = DB::connection('mysql')->table('tabBOM')->where('docstatus', '<', 2)->where('is_default', 1)->whereIn('item', collect($bom)->pluck('item_code'))->get();
            $default_bom_arr = collect($default_bom_arr)->groupBy('item');

            $attributes_arr = [];
            if($sub_assemblies){
                $attributes_query = DB::connection('mysql')->table('tabItem Variant Attribute')->whereIn('parent', $sub_assemblies)->orderBy('idx', 'asc')->get();
                foreach ($attributes_query as $attribute) {
                    $attributes_arr[$attribute->parent][$attribute->attribute] = $attribute->attribute_value;
                }
            }

            $materials = [];
            foreach ($bom as $item) {
                $default_bom = isset($default_bom_arr[$item->item_code]) ? $default_bom_arr[$item->item_code][0] : [];

                $item_detail = isset($item_details[$item->item_code]) ? $item_details[$item->item_code][0] : [];
                $item_description = ($item_detail) ? $item_detail->description : '';
                $item_classification = ($item_detail) ? $item_detail->item_classification : '';
                $item_group = ($item_detail) ? $item_detail->item_group : '';
                $child_bom = ($default_bom) ? $default_bom->name : $item->bom_no;

                $materials[] = [
                    'item_code' => $item->item_code,
                    'description' => $item_description,
                    'item_classification' => $item_classification,
                    'item_group' => $item_group,
                    'qty' => $item->qty,
                    'bom_no' => $child_bom,
                    'uom' => $item->uom,
                    'attributes' => isset($attributes_arr[$item->item_code]) ? $attributes_arr[$item->item_code] : [],
                    'child_nodes' => $this->get_bom($child_bom)
                ];
            }

            return $materials;
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    // /get_production_req_items
    public function get_production_req_items(Request $request){
        try {
            if(!Auth::user()) {
                return response()->json(['message' => 'Session Expired. Please refresh the page and login to continue.']);
            }

            $production_orders = $request->production_orders ? $request->production_orders : [];

            $items = DB::table('tabWork Order Item')->whereIn('parent', $production_orders)
                ->orderBy('parent', 'asc')->orderBy('idx', 'asc')->get();

            $req_items = [];
            foreach ($items as $item) {
                $prod = DB::table('tabWork Order')->where('name', $item->parent)->first();

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
                    ->where('se.docstatus', '<', 2)->where('se.work_order', $item->parent)
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

    // /get_parts
    public function get_parts(Request $request){
        try {
            if(!Auth::user()) {
                return response()->json(['message' => 'Session Expired. Please refresh the page and login to continue.']);
            }

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
                            $existing_prod1 = DB::connection('mysql')->table('tabWork Order')
                                ->where('docstatus', 1)->where('company', 'FUMACO Inc.')
                                ->when($reference_pref == 'SO', function ($query) use ($reference_no){
                                    return $query->where('sales_order_no', $reference_no);
                                })
                                ->when($reference_pref == 'MREQ', function ($query) use ($reference_no){
                                    return $query->where('material_request', $reference_no);
                                })
                                ->where('production_item', $parent_part['item_code'])
                                ->where('parent_item_code', $bom_details->item)
                                ->select('planned_start_date', 'name', 'qty', 'wip_warehouse', 'fg_warehouse')
                                // ->where('qty', $parent_part['qty'] * $request->qty[$idx])
                                ->get();
                            
                            $po_ref_qty = collect($existing_prod1)->pluck('qty', 'name');
                            $production_references = collect($existing_prod1)->implode('name', ',');
                            $total_production_order_qty = collect($existing_prod1)->sum('qty');
                            $existing_prod1 = collect($existing_prod1)->first();

                            $s_warehouse = null;
                            if ($existing_prod1) {
                                $planned_start_date1 = Carbon::parse($existing_prod1->planned_start_date)->format('Y-m-d');
                                $s_warehouse = DB::connection('mysql')->table('tabWork Order Item')->where('parent', $existing_prod1->name)->first()->source_warehouse;
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
                                'production_order_qty' => ($existing_prod1) ? $total_production_order_qty : 0,
                                'production_references' => ($existing_prod1) ? $production_references : null,
                                'po_ref_qty' => $po_ref_qty,
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
                                'production_order_qty' => 0,
                                'production_references' => null,
                                'po_ref_qty' => [],
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
                            $existing_prod2 = DB::connection('mysql')->table('tabWork Order')
                                ->where('docstatus', 1)->where('company', 'FUMACO Inc.')
                                ->when($reference_pref == 'SO', function ($query) use ($reference_no){
                                    return $query->where('sales_order_no', $reference_no);
                                })
                                ->when($reference_pref == 'MREQ', function ($query) use ($reference_no){
                                    return $query->where('material_request', $reference_no);
                                })
                                ->where('production_item', $child_part['item_code'])
                                ->where('parent_item_code', $bom_details->item)
                                ->select('planned_start_date', 'name', 'qty', 'wip_warehouse', 'fg_warehouse')
                                // ->where('qty', $child_part['qty'] * $request->qty[$idx])
                                ->get();
                            
                            $po_ref_qty = collect($existing_prod2)->pluck('qty', 'name');
                            $production_references = collect($existing_prod2)->implode('name', ',');
                            $total_production_order_qty = collect($existing_prod2)->sum('qty');
                            $existing_prod2 = collect($existing_prod2)->first();

                            $s_warehouse = null;
                            if ($existing_prod2) {
                                $planned_start_date2 = Carbon::parse($existing_prod2->planned_start_date)->format('Y-m-d');
                                $s_warehouse = DB::connection('mysql')->table('tabWork Order Item')->where('parent', $existing_prod2->name)->first()->source_warehouse;
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
                                'planned_qty' => ($child_part['qty'] * $request->qty[$idx]) * $parent_part['qty'],
                                'reference_no' => $reference_no,
                                'planned_start_date' => ($existing_prod2) ? $planned_start_date2 : null,
                                'production_order' => ($existing_prod2) ? $existing_prod2->name : null,
                                'production_order_qty' => ($existing_prod2) ? $total_production_order_qty : 0,
                                'production_references' => ($existing_prod2) ? $production_references : null,
                                'po_ref_qty' => $po_ref_qty,
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
                                $existing_prod3 = DB::connection('mysql')->table('tabWork Order')
                                    ->where('docstatus', 1)->where('company', 'FUMACO Inc.')
                                    ->when($reference_pref == 'SO', function ($query) use ($reference_no){
                                        return $query->where('sales_order_no', $reference_no);
                                    })
                                    ->when($reference_pref == 'MREQ', function ($query) use ($reference_no){
                                        return $query->where('material_request', $reference_no);
                                    })
                                    ->where('production_item', $child_part2['item_code'])
                                    ->where('parent_item_code', $bom_details->item)
                                    ->select('planned_start_date', 'name', 'qty', 'wip_warehouse', 'fg_warehouse')
                                    // ->where('qty', $child_part2['qty'] * $request->qty[$idx])
                                    ->get();
                                
                                $po_ref_qty = collect($existing_prod3)->pluck('qty', 'name');
                                $production_references = collect($existing_prod3)->implode('name', ',');
                                $total_production_order_qty = collect($existing_prod3)->sum('qty');
                                $existing_prod3 = collect($existing_prod3)->first();

                                $s_warehouse = null;
                                if ($existing_prod3) {
                                    $planned_start_date3 = Carbon::parse($existing_prod3->planned_start_date)->format('Y-m-d');
                                    $s_warehouse = DB::connection('mysql')->table('tabWork Order Item')
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
                                    'planned_qty' => ($child_part2['qty'] * $request->qty[$idx]) * $child_part['qty'],
                                    'reference_no' => $request->so[$idx],
                                    'planned_start_date' => ($existing_prod3) ? $planned_start_date3 : null,
                                    'production_order' => ($existing_prod3) ? $existing_prod3->name : null,
                                    'production_order_qty' => ($existing_prod3) ? $total_production_order_qty : 0,
                                    'production_references' => ($existing_prod3) ? $production_references : null,
                                    'po_ref_qty' => $po_ref_qty,
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
                                    $existing_prod4 = DB::connection('mysql')->table('tabWork Order')
                                        ->where('docstatus', 1)->where('company', 'FUMACO Inc.')
                                        ->when($reference_pref == 'SO', function ($query) use ($reference_no){
                                            return $query->where('sales_order_no', $reference_no);
                                        })
                                        ->when($reference_pref == 'MREQ', function ($query) use ($reference_no){
                                            return $query->where('material_request', $reference_no);
                                        })
                                        ->where('production_item', $child_part3['item_code'])
                                        ->where('parent_item_code', $bom_details->item)
                                        ->select('planned_start_date', 'name', 'qty', 'wip_warehouse', 'fg_warehouse')
                                        // ->where('qty', $child_part2['qty'] * $request->qty[$idx])
                                        ->get();

                                    $po_ref_qty = collect($existing_prod4)->pluck('qty', 'name');                                        
                                    $production_references = collect($existing_prod4)->implode('name', ',');
                                    $total_production_order_qty = collect($existing_prod4)->sum('qty');
                                    $existing_prod4 = collect($existing_prod4)->first();

                                    $s_warehouse = null;
                                    if ($existing_prod4) {
                                        $planned_start_date3 = Carbon::parse($existing_prod4->planned_start_date)->format('Y-m-d');
                                        $s_warehouse = DB::connection('mysql')->table('tabWork Order Item')
                                            ->where('parent', $existing_prod4->name)->first()->source_warehouse;
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
                                        'planned_qty' => ($child_part3['qty'] * $request->qty[$idx]) * $child_part2['qty'],
                                        'reference_no' => $request->so[$idx],
                                        'planned_start_date' => ($existing_prod4) ? $planned_start_date2 : null,
                                        'production_order' => ($existing_prod4) ? $existing_prod4->name : null,
                                        'production_order_qty' => ($existing_prod4) ? $total_production_order_qty : 0,
                                        'production_references' => ($existing_prod4) ? $production_references : null,
                                        'po_ref_qty' => $po_ref_qty,
                                        's_warehouse' => $s_warehouse,
                                        'wip_warehouse' => ($existing_prod4) ? $existing_prod4->wip_warehouse : null,
                                        'fg_warehouse' => ($existing_prod4) ? $existing_prod4->fg_warehouse : null,
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

            $jtno = $request->production;

            if(!Auth::user()) {
                return response()->json(['message' => 'Session Expired. Please refresh the page and login to continue.']);
            }
            
            $jtno = $request->production;
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

                // !!
                $tbl_display = "";
                $tbl_display2 = "";
                $production_order = ($details) ? $details->production_order : null;
                $process_arr = DB::connection('mysql_mes')->table('job_ticket')
                    ->where('production_order', $production_order)
                    ->select(DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process'), 'workstation', 'process_id', 'job_ticket_id', 'status', 'completed_qty', 'reject')
                    ->get();

                $operation_list = [];
                foreach ($process_arr as $row) {
                    $operations_arr = [];
                    if($row->workstation == "Spotwelding"){
                        $operations =  DB::connection('mysql_mes')->table('spotwelding_qty as qpart')
                        ->where('qpart.job_ticket_id',  $row->job_ticket_id)->get();
                        $total_rejects =$row->reject;
                        $min_count= collect($operations)->min('from_time');
                        $max_count=collect($operations)->max('to_time');
                        $status = collect($operations)->where('status', 'In Progress');
                        $operations_arr[] = [
                            'machine_code' => "",
                            'operator_name' => "",
                            'from_time' => $min_count,
                            'to_time' => ($row->status == "In Progress") ? '' : $max_count,
                            'status' => (count($status) == 0 )? 'Not started': "In Progress",
                            'qa_inspection_status' => "",
                            'good' => $row->completed_qty,
                            'reject' => $total_rejects,
                            'remarks' => "",
                        ];
                    }else{
                        $operations = DB::connection('mysql_mes')->table('job_ticket AS jt')
                        ->join('time_logs', 'time_logs.job_ticket_id', 'jt.job_ticket_id')
                        ->where('time_logs.job_ticket_id', $row->job_ticket_id)
                        ->where('workstation','!=', 'Spotwelding')
                        ->select('jt.*', 'time_logs.*')
                        ->orderBy('idx', 'asc')->get();

                        foreach ($operations as $d) {
                            $reference_type = ($d->workstation == 'Spotwelding') ? 'Spotwelding' : 'Time Logs';
                            $reference_id = ($d->workstation == 'Spotwelding') ? $d->job_ticket_id : $d->time_log_id;
                            // $qa_inspection_status = $this->get_qa_inspection_status($reference_type, $reference_id);

                            if ($d->cycle_time_in_seconds > 0 && $d->good > 0) {
                                $cycle_time_in_seconds = $d->cycle_time_in_seconds / $d->good;

                                $dur_hours = floor($cycle_time_in_seconds / 3600);
                                $dur_minutes = floor(($cycle_time_in_seconds / 60) % 60);
                                $dur_seconds = $cycle_time_in_seconds % 60;
                    
                                $dur_hours = ($dur_hours > 0) ? $dur_hours .'h' : null;
                                $dur_minutes = ($dur_minutes > 0) ? $dur_minutes .'m' : null;
                                $dur_seconds = ($dur_seconds > 0) ? $dur_seconds .'s' : null;
                    
                                $cycle_time_per_log = $dur_hours . ' '. $dur_minutes . ' ' . $dur_seconds;
                            }else{
                                $cycle_time_per_log = '-';
                            }

                            $helpers = DB::connection('mysql_mes')->table('helper')
                                ->where('time_log_id', $d->time_log_id)->orderBy('operator_name', 'asc')
                                ->distinct()->pluck('operator_name');

                            $operations_arr[] = [
                                'machine_code' => $d->machine_code,
                                'operator_name' => $d->operator_name,
                                'helpers' => $helpers,
                                'from_time' => ($d->from_time) ? Carbon::parse($d->from_time)->format('M-d-Y h:i A') : '',
                                'to_time' => ($d->to_time) ? Carbon::parse($d->to_time)->format('M-d-Y h:i A') : '',
                                'status' => $d->status,
                                // 'qa_inspection_status' => $qa_inspection_status,
                                'good' => $d->good,
                                'reject' => $d->reject,
                                'remarks' => $d->remarks,
                                'cycle_time_per_log' => $cycle_time_per_log
                            ];
                        }

                        $collection2 = collect($operations_arr);
                        $collection2->contains('status', 'In Progress') ? $tbl_display2 = "show" : $tbl_display2 = "hide";

                    }

                    $operation_list[] = [
                        'production_order' => $jtno,
                        'workstation' => $row->workstation,
                        'process' => $row->process,
                        'job_ticket' => $row->job_ticket_id,
                        'count_good' => (count($operations_arr) <= 1) ? '' : "Total: ".collect($operations_arr)->sum('good'),
                        'count' => (count($operations_arr) > 0) ? count($operations_arr) : 1,
                        'operations' => $operations_arr,
                        'display' => $tbl_display2,
                        'cycle_time' => $this->compute_item_cycle_time_per_process($details->item_code, $details->qty_to_manufacture, $row->workstation, $row->process_id)
                    ];
                    
                }

                $collection = collect($operation_list);
                $collection->contains('display', "show") ? $tbl_display = "show" : $tbl_display = "hide";

                return view('wizard.tbl_bom_review', compact('workstation_process', 'workstations', 'bom_details', 'bom_operations', 'bom_materials', 'items_with_different_uom', 'operation_list', 'tbl_display'));
            }
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function time_log_delete(Request $request){
        $process_id = DB::connection('mysql_mes')->table('process')->where('process_name', $request->process)->pluck('process_id')->first();

        $time_log = DB::connection('mysql_mes')->table('time_logs')->where('job_ticket_id', $request->jtid)->where('operator_name', $request->operator);

        $time_log_qty = $time_log->pluck('good')->first();
        $jt_qty = DB::connection('mysql_mes')->table('job_ticket')->where('job_ticket_id', $request->jtid)->pluck('completed_qty')->first();
        $new_qty = $jt_qty - $time_log_qty;

        $time_log_id = $time_log->pluck('time_log_id')->first();
        $log_msg = "Job ticket ID: ".$request->jtid.", Workstation: ".$request->workstation.", Process: ".$request->process.", Good: ".$request->tbl_good.", Reject: ".$request->tbl_reject.", Machine: ".$request->machine.", Start Time: ".$request->from_time.", End Time: ".$request->to_time.", Operator Name: ".$request->operator;
        
        $logs = [
            'action' => 'Delete Time Log',
            'message' => $log_msg,
            'created_at' => Carbon::now(),
            'created_by' => Auth::user()->employee_name
        ];
        
        $mes_delete = $time_log->delete();

        $jt_stat = DB::connection('mysql_mes')->table('time_logs')->where('job_ticket_id', $request->jtid)->where('status', 'In Progress')->get();
        $jt_val = [
            'status' => count($jt_stat) > 0 ? 'In Progress' : 'Pending',
            'good' => $new_qty,
            'completed_qty' => $new_qty
        ];

        $jt_update = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->prod_order)->where('workstation', $request->workstation)
            ->where('process_id', $process_id)->update($jt_val);

        $jt_status = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->prod_order)->select('status')->get();
        $collection = collect($jt_status);
        if(!$collection->contains('status', 'In Progress')){
            $mes_update = DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->prod_order)->update(['status' => 'Not Started']);
        }

        $act_log = DB::connection('mysql_mes')->table('activity_logs')->insert($logs);

        return redirect()->back()->with('deleted', 'Task updated');
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
        try {
            $logs = [];
            // validate if process is empty
            if(!$request->wprocess) {
                return response()->json(['status' => 0, 'message' => 'Workstation / Process cannot be empty.']);
            }
            // validate for duplicate process
            $process_arr = [];
            foreach ($request->wprocess as $i => $process_id) {
                if(!in_array($process_id, $process_arr)){
                    array_push($process_arr, $process_id);
                }else{
                    return response()->json(['status' => 0, 'message' => 'Duplicate process was selected for workstation <b>' . $request->workstation[$i] . '</b>.']);
                }
            }
            // validate requested production order if partially feedbacked
            if ($request->production_order) {
                $production_order_details = DB::connection('mysql_mes')->table('production_order')
                    ->where('production_order', $request->production_order)->first();
                    
                if($production_order_details && $production_order_details->feedback_qty > 0) {
                    return response()->json(['status' => 0, 'message' => 'BOM cannot be updated. Production Order has been partially feedbacked.']);
                }
            }

            $now = Carbon::now();
            if ($request->id) {
                if ($request->production_order) {
                    // check for in progress or completed job ticket
                    $existing_not_pending_job_ticket = DB::connection('mysql_mes')->table('job_ticket')
                        ->where('production_order', $request->production_order)->where('status', '!=', 'Pending')->first();
                    if ($existing_not_pending_job_ticket) {
                        $bom_operation_id_index = array_search($existing_not_pending_job_ticket->bom_operation_id, $request->id);
                        if ($request->wprocess[$bom_operation_id_index] != $existing_not_pending_job_ticket->process_id) {
                            return response()->json(['status' => 0, 'message' => 'BOM cannot be updated. <b>' . $existing_not_pending_job_ticket->workstation .'</b> is currently in progress.']);
                        }
                        // validate if existing_not_pending_job_ticket bom operation id not in $request->id
                        if (!in_array($existing_not_pending_job_ticket->bom_operation_id, array_filter($request->id))) {
                            return response()->json(['status' => 0, 'message' => 'BOM cannot be updated. <b>' . $existing_not_pending_job_ticket->workstation .'</b> is currently in progress.']);
                        }
                    }
                    // delete production order operation
                    DB::connection('mysql')->table('tabWork Order Operation')
                        ->where('parent', $request->production_order)->whereNotIn('bom_operation_id', array_filter($request->id))->delete();
                    // delete job ticket
                    $removed_process = DB::connection('mysql_mes')->table('job_ticket')
                        ->where('production_order', $request->production_order)
                        ->whereNotIn('bom_operation_id', array_filter($request->id));

                    $removed_process_query = $removed_process->get();
                    foreach($removed_process_query as $row) {
                        $logs[] = [
                            'delete' => [
                                'workstation' => $row->workstation,
                                'process' => $row->process_id
                            ]
                        ];
                    }

                    $removed_process->delete();
                }
                
                // delete bom operation
                DB::connection('mysql')->table('tabBOM Operation')
                    ->where('parent', $bom)->whereNotIn('name', array_filter($request->id))->delete();

                $operation = $request->operation;
                foreach ($request->id as $x => $row) {
                    // get operation name
                    if (is_numeric($request->operation)){
                        $operation_query = DB::connection('mysql_mes')->table('operation')->where('operation_id', $request->operation)->first();
                        $operation = ($operation_query) ? $operation_query->operation_name : null;
                    }else{
                        $operation_query = DB::connection('mysql_mes')->table('workstation')
                            ->join('operation', 'operation.operation_id', 'workstation.operation_id')
                            ->where('workstation_name',$request->workstation[$x])->first();
        
                        $operation = ($operation_query) ? $operation_query->operation_name : null;
                    }
                    // update existing records in bom operation, production order operation and job ticket table

                    $username = Auth::user()->email ? Auth::user()->email : Auth::user()->employee_name;
                    if ($request->id[$x]) {
                        // update existing bom operation
                        DB::connection('mysql')->table('tabBOM Operation')
                            ->where('name', $request->id[$x])->where('parent', $bom)
                            ->where(function($q) use ($request, $x) {
                                $q->where('process', '!=', $request->wprocess[$x])->orWhereNull('process');
                            })
                            ->update(['process' => $request->wprocess[$x], 'idx' => $x + 1, 'modified' => $now->toDateTimeString(), 'modified_by' => $username]);

                        if ($request->production_order) {
                            // check if bom_operation_id exists in production order operation table
                            $existing_production_order_operation = DB::connection('mysql')->table('tabWork Order Operation')
                                ->where('parent', $request->production_order)->where('bom_operation_id', $request->id[$x])->exists();
                            if (!$existing_production_order_operation) {
                                // insert workstation in production order operation table
                                DB::connection('mysql')->table('tabWork Order Operation')->insert([
                                    'name' => 'mes'.uniqid(),
                                    'creation' => $now->toDateTimeString(),
                                    'modified' => $now->toDateTimeString(),
                                    'modified_by' => $username,
                                    'owner' => $username,
                                    'docstatus' => 1,
                                    'parent' => $request->production_order,
                                    'parentfield' => 'operations',
                                    'parenttype' => 'Work Order',
                                    'idx' => $x + 1,
                                    'status' => 'Pending',
                                    'actual_start_time' => null,
                                    'workstation' => $request->workstation[$x],
                                    'completed_qty' => 0,
                                    'planned_operating_cost' => 0,
                                    'description' => $request->workstation[$x],
                                    'actual_end_time' => null,
                                    'actual_operating_cost' => 0,
                                    'hour_rate' => 0,
                                    'planned_start_time' => null,
                                    'bom' => $bom,
                                    'actual_operation_time' => 0,
                                    'operation' => $operation,
                                    'planned_end_time' => null,
                                    'time_in_mins' => 1,
                                    'process' => $request->wprocess[$x],
                                    'bom_operation_id' => $request->id[$x]
                                ]);
                            }
                            // update existing production order operation
                            DB::connection('mysql')->table('tabWork Order Operation')
                                ->where('bom_operation_id', $request->id[$x])
                                ->where('parent', $request->production_order)->where('status', 'Pending')
                                ->where(function($q) use ($request, $x) {
                                    $q->where('process', '!=', $request->wprocess[$x])->orWhereNull('process');
                                })
                                ->update(['process' => $request->wprocess[$x], 'idx' => $x + 1, 'modified' => $now->toDateTimeString(), 'modified_by' => $username]);

                            // check if bom operation id exists in job ticket table filtered by production order
                            $existing_job_ticket = DB::connection('mysql_mes')->table('job_ticket')
                                ->where('bom_operation_id', $request->id[$x])->where('production_order', $request->production_order)->exists();
                            if (!$existing_job_ticket) {
                                if ($request->workstation[$x] != 'Painting') {
                                    // insert workstation in job ticket table
                                    DB::connection('mysql_mes')->table('job_ticket')->insert([
                                        'production_order' => $request->production_order,
                                        'workstation' => $request->workstation[$x],
                                        'process_id' => $request->wprocess[$x],
                                        'idx' => $x + 1,
                                        'bom_operation_id' => $request->id[$x],
                                        'created_by' => $username,
                                        'last_modified_by' => $username,
                                    ]);

                                    $logs[] = [
                                        'add' => [
                                            'workstation' => $request->workstation[$x],
                                            'process' => $request->wprocess[$x],
                                        ]
                                    ];
                                } else {
                                    // get painting processes
                                    $painting_processes = DB::connection('mysql_mes')->table('process_assignment')
                                        ->join('workstation', 'workstation.workstation_id', 'process_assignment.workstation_id')
                                        ->where('workstation.workstation_name', 'Painting')->orderBy('process_assignment.process_id', 'asc')
                                        ->pluck('process_assignment.process_id');
                                    // insert painting process
                                    foreach ($painting_processes as $i => $painting_process) {
                                        DB::connection('mysql_mes')->table('job_ticket')->insert([
                                            'production_order' => $request->production_order,
                                            'workstation' => $request->workstation[$x],
                                            'process_id' => $painting_process,
                                            'idx' => $x + $i + 1,
                                            'bom_operation_id' => $request->id[$x],
                                            'created_by' => $username,
                                            'last_modified_by' => $username,
                                        ]);

                                        $logs[] = [
                                            'add' => [
                                                'workstation' => $request->workstation[$x],
                                                'process' => $painting_process,
                                            ]
                                        ];
                                    }
                                }
                            }
                            // update existing job ticket
                            $jt_query = DB::connection('mysql_mes')->table('job_ticket')
                                ->where('production_order', $request->production_order)->where('bom_operation_id', $request->id[$x])
                                ->where(function($q) use ($request, $x) {
                                    $q->where('process_id', '!=', $request->wprocess[$x])->orWhereNull('process_id');
                                })
                                ->where('status', 'Pending');

                            $jt = $jt_query->first();
                            if ($jt) {
                                if($jt->process_id != $request->wprocess[$x]) {
                                    $logs[] = [
                                        'update' => [
                                            'workstation' => $request->workstation[$x],
                                            'old_process' => $jt->process_id,
                                            'new_process' => $request->wprocess[$x],
                                        ]
                                    ];
    
                                    $jt_query->update(['process_id' => $request->wprocess[$x], 'idx' => $x + 1, 'last_modified_at' => $now->toDateTimeString(), 'last_modified_by' => $username]);
                                }
                            }
                        }
                    } else {
                        // insert new workstation in bom operation table
                        $new_bom_operation_id = 'mes'. uniqid();
                        DB::connection('mysql')->table('tabBOM Operation')->insert([
                            'name' => $new_bom_operation_id,
                            'creation' => $now->toDateTimeString(),
                            'modified' => $now->toDateTimeString(),
                            'modified_by' => $username,
                            'owner' => $username,
                            'docstatus' => 1,
                            'parent' => $bom,
                            'parentfield' => 'operations',
                            'parenttype' => 'BOM',
                            'idx' => $x + 1,
                            'operation' => $operation,
                            'workstation' => $request->workstation[$x],
                            'process' => $request->wprocess[$x],
                        ]);
                        if ($request->production_order) {
                            // check if bom_operation_id exists in production order operation table
                            $existing_production_order_operation = DB::connection('mysql')->table('tabWork Order Operation')
                                ->where('parent', $request->production_order)->where('bom_operation_id', $new_bom_operation_id)->exists();
                            if (!$existing_production_order_operation) {
                                // insert workstation in production order operation table
                                DB::connection('mysql')->table('tabWork Order Operation')->insert([
                                    'name' => 'mes'.uniqid(),
                                    'creation' => $now->toDateTimeString(),
                                    'modified' => $now->toDateTimeString(),
                                    'modified_by' => $username,
                                    'owner' => $username,
                                    'docstatus' => 1,
                                    'parent' => $request->production_order,
                                    'parentfield' => 'operations',
                                    'parenttype' => 'Work Order',
                                    'idx' => $x + 1,
                                    'status' => 'Pending',
                                    'actual_start_time' => null,
                                    'workstation' => $request->workstation[$x],
                                    'completed_qty' => 0,
                                    'planned_operating_cost' => 0,
                                    'description' => $request->workstation[$x],
                                    'actual_end_time' => null,
                                    'actual_operating_cost' => 0,
                                    'hour_rate' => 0,
                                    'planned_start_time' => null,
                                    'bom' => $bom,
                                    'actual_operation_time' => 0,
                                    'operation' => $operation,
                                    'planned_end_time' => null,
                                    'time_in_mins' => 1,
                                    'process' => $request->wprocess[$x],
                                    'bom_operation_id' => $new_bom_operation_id
                                ]);
                            }
                            // update existing production order operation
                            DB::connection('mysql')->table('tabWork Order Operation')
                                ->where('bom_operation_id', $new_bom_operation_id)
                                ->where('parent', $request->production_order)->where('status', 'Pending')
                                ->where(function($q) use ($request, $x) {
                                    $q->where('process', '!=', $request->wprocess[$x])->orWhereNull('process');
                                })
                                ->update(['process' => $request->wprocess[$x], 'idx' => $x + 1, 'modified' => $now->toDateTimeString(), 'modified_by' => $username]);

                            // update existing job ticket
                            $jt_query = DB::connection('mysql_mes')->table('job_ticket')
                                ->where('production_order', $request->production_order)->where('bom_operation_id', $new_bom_operation_id)
                                ->where(function($q) use ($request, $x) {
                                    $q->where('process_id', '!=', $request->wprocess_id[$x])->orWhereNull('process_id');
                                })
                                ->where('status', 'Pending');

                            $jt = $jt_query->first();
                            if ($jt) {
                                if($jt->process_id != $request->wprocess[$x]) {
                                    $logs[] = [
                                        'update' => [
                                            'workstation' => $workstation,
                                            'old_process' => $jt->process_id,
                                            'new_process' => $request->wprocess[$x],
                                        ]
                                    ];
    
                                    $jt_query->update(['process_id' => $request->wprocess[$x], 'idx' => $x + 1, 'last_modified_at' => $now->toDateTimeString(), 'last_modified_by' => $username]);
                                }
                            }

                            // check if bom operation id exists in job ticket table filtered by production order
                            $existing_job_ticket = DB::connection('mysql_mes')->table('job_ticket')
                                ->where('bom_operation_id', $new_bom_operation_id)->where('production_order', $request->production_order)->exists();
                            if (!$existing_job_ticket) {
                                if ($request->workstation[$x] != 'Painting') {
                                    // insert workstation in job ticket table
                                    DB::connection('mysql_mes')->table('job_ticket')->insert([
                                        'production_order' => $request->production_order,
                                        'workstation' => $request->workstation[$x],
                                        'process_id' => $request->wprocess[$x],
                                        'idx' => $x + 1,
                                        'bom_operation_id' => $new_bom_operation_id,
                                        'created_by' => $username,
                                        'last_modified_by' => $username,
                                    ]);

                                    $logs[] = [
                                        'add' => [
                                            'workstation' => $request->workstation[$x],
                                            'process' => $request->wprocess[$x],
                                        ]
                                    ];
                                } else {
                                    // get painting processes
                                    $painting_processes = DB::connection('mysql_mes')->table('process_assignment')
                                        ->join('workstation', 'workstation.workstation_id', 'process_assignment.workstation_id')
                                        ->where('workstation.workstation_name', 'Painting')->orderBy('process_assignment.process_id', 'asc')
                                        ->pluck('process_assignment.process_id');
                                    // insert painting process
                                    foreach ($painting_processes as $i => $painting_process) {
                                        $painting_process;
                                        DB::connection('mysql_mes')->table('job_ticket')->insert([
                                            'production_order' => $request->production_order,
                                            'workstation' => $request->workstation[$x],
                                            'process_id' => $painting_process,
                                            'idx' => $x + $i + 1,
                                            'bom_operation_id' => $new_bom_operation_id,
                                            'created_by' => $username,
                                            'last_modified_by' => $username,
                                        ]);

                                        $logs[] = [
                                            'add' => [
                                                'workstation' => $request->workstation[$x],
                                                'process' => $painting_process,
                                            ]
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // delete not existing bom operation id or null bom operation id in job ticket
            if ($bom != 'no_bom') {
                $updated_bom_operation_id = DB::connection('mysql')->table('tabBOM Operation')->where('parent', $bom)->pluck('name');
            } else {
                $updated_bom_operation_id = DB::connection('mysql')->table('tabWork Order Operation')
                    ->where('parent', $request->production_order)->pluck('bom_operation_id');
            }
            
            DB::connection('mysql_mes')->table('job_ticket')
                ->where('production_order', $request->production_order)->where('status', 'Pending')
                ->where(function($q) use ($updated_bom_operation_id) {
                    $q->whereNotIn('bom_operation_id', $updated_bom_operation_id)->orWhereNull('bom_operation_id');
                })->delete();

            if ($request->production_order) {
                // get production order job ticket
                $jt = DB::connection('mysql_mes')->table('job_ticket')
                    ->where('production_order', $request->production_order)->get();
                $count_jt = collect($jt)->count();
                $pending_jt = collect($jt)->where('status', 'Pending')->count();
                $completed_jt = collect($jt)->where('status', 'Completed')->count();
                // set status of production order
                $status = ($count_jt == $pending_jt) ? 'Not Started' : 'In Progress';
                $status = ($count_jt == $completed_jt) ? 'Completed' : $status;
                // update status of production order in mes
                DB::connection('mysql_mes')->table('production_order')
                    ->where('production_order', $request->production_order)
                    ->update(['status' => $status, 'last_modified_at' => $now->toDateTimeString(), 'last_modified_by' => $username]);
                // set status of production order in erp
                $status = (in_array($status, ['In Progress', 'Completed'])) ? 'In Process' : $status;
                // update status of production order in erp
                DB::connection('mysql')->table('tabWork Order')
                    ->where('name', $request->production_order)
                    ->update(['status' => $status, 'modified' => $now->toDateTimeString(), 'modified_by' => $username]);

                $this->update_production_order_produced_qty($request->production_order);

                $logs[] = ['production_order' => $request->production_order, 'user' => $username];

                // insert activity logs
                if (isset($logs[0]['delete']) || isset($logs[0]['update']) || isset($logs[0]['add'])) {
                    DB::connection('mysql_mes')->table('activity_logs')->insert([
                        'action' => 'BOM Update',
                        'message' => json_encode($logs),
                        'created_by' => $username
                    ]);
                }
            }

            $this->update_job_card($request->production_order);
            // update bom as reviewed
            DB::connection('mysql')->table('tabBOM')->where('name', $bom)->update(['is_reviewed' => 1, 'reviewed_by' => $username, 'last_date_reviewed' => $now->toDateTimeString()]);
           
            return response()->json(['status' => 1, 'message' => 'BOM updated and reviewed.']);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }
   
    public function create_production_order(Request $request){
        DB::connection('mysql_mes')->beginTransaction();
        try {
            if(!Auth::user()) {
                return response()->json(['success' => 0, 'message' => 'Session Expired. Please refresh the page and login to continue.']);
            }

            $now = Carbon::now();

            if (!$request->qty) {
                return response()->json(['success' => 0, 'message' => 'Qty cannot be less than or equal to 0.']);
            }

            if ($request->qty <= 0) {
                return response()->json(['success' => 0, 'message' => 'Qty cannot be less than or equal to 0.']);
            }

            if ($request->reference_type) {
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

            $latest_pro = DB::connection('mysql')->table('tabWork Order')->max('name');
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
                    $classification = 'Customer Order';
                }else{
                    $mr_details = DB::connection('mysql')->table('tabMaterial Request')->where('name', $request->reference_no)->first();
                    $sales_order = null;
                    $material_request = $request->reference_no;
                    $customer = ($mr_details) ? $mr_details->customer : null;
                    $delivery_date = ($mr_details) ? $mr_details->delivery_date : null;
                    $project = ($mr_details) ? $mr_details->project : null;
                    $classification = ($mr_details) ? $mr_details->custom_purpose : null;
                }
            }

            $item_details = DB::connection('mysql')->table('tabItem')->where('name', $request->item_code)->first();

            if ($item_details->is_stock_item == 0) {
                return response()->json(['success' => 0, 'message' => 'Item <b>' . $request->item_code . '</b> is not a stock item.']);
            }

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
                'sales_order' => $sales_order,
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

            $check_existing_production_order = DB::connection('mysql_mes')->table('production_order')
                ->where('parent_item_code', $data_mes['parent_item_code'])
                ->where('sub_parent_item_code', $data_mes['sub_parent_item_code'])
                ->where('item_code', $data_mes['item_code'])
                ->where('qty_to_manufacture', $data_mes['qty_to_manufacture'])
                ->where('sales_order', $data_mes['sales_order'])
                ->where('material_request', $data_mes['material_request'])
                ->where('status', '!=', 'Cancelled')
                ->first();
            
            if ($check_existing_production_order) {
                return response()->json(['success' => 0, 'message' => 'Production Order for this item already exists. (' . $check_existing_production_order->production_order . ')']);
            }

            DB::connection('mysql_mes')->table('production_order')->insert($data_mes);
            $this->save_production_operations($new_id, $request->bom, ($request->planned_date) ? $request->planned_date : null, 'mes');

            $del_data = [
                'erp_reference_id' => $request->item_reference_id,
                'reference_no' => ($sales_order) ? $sales_order : $material_request,
                'parent_item_code' => strtoupper($request->parent_code),
                'delivery_date' => $request->delivery_date,
                'created_by' => Auth::user()->email
            ];

            $existing_del_data = DB::connection('mysql_mes')->table('delivery_date')
                ->where('erp_reference_id', $request->item_reference_id)->where('parent_item_code', $request->parent_code)
                ->exists();

            if(!$existing_del_data){
                DB::connection('mysql_mes')->table('delivery_date')->insert($del_data);
            }

            DB::connection('mysql')->beginTransaction();
            try{
                DB::connection('mysql')->table('tabWork Order')->insert($data);
                $required_items = $this->save_production_req_items($new_id, $request->bom, $request->qty, $request->operation);
                $this->save_production_operations($new_id, $request->bom, ($request->planned_date) ? $request->planned_date : null, 'erp');

                $this->insert_job_card($new_id);

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

                $item_default_warehouse = DB::connection('mysql')->table('tabItem Default')->where('parent', $item->item_code)->where('company', 'FUMACO Inc.')->first();
                $item_default_warehouse = ($item_default_warehouse) ? $item_default_warehouse->default_warehouse : null;

                $default_warehouse = ($source_warehouse) ? $source_warehouse : $item_default_warehouse;

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
                    'parenttype' => 'Work Order',
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

            DB::connection('mysql')->table('tabWork Order Item')->insert($req_items);

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
                    'parenttype' => 'Work Order',
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
                    'bom_operation_id' => $operation->name,
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
                            'planned_start_date' => null,
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
                DB::connection('mysql')->table('tabWork Order Operation')->insert($operations);
            }
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage(), "id" => $parent]);
        }
    }

    // /close_production_order
    public function close_production_order(Request $request){
        DB::connection('mysql')->beginTransaction();
        DB::connection('mysql_mes')->beginTransaction();
        try {
            if(!Auth::user()) {
                return response()->json(['success' => 0, 'message' => 'Session Expired. Please login to continue.']);
            }

            $now = Carbon::now();

            // check for task in progress
            $task_in_progress = DB::connection('mysql_mes')->table('job_ticket')
                ->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
                ->where('production_order', $request->production_order)
                ->where('time_logs.status', 'In Progress')->count();

            if ($task_in_progress > 0) {
                return response()->json(['success' => 0, 'message' => 'Cannot close production order with on-going task by operator. ' . $request->production_order]);
            }

            $stock_entries = DB::connection('mysql')->table('tabStock Entry')->where('work_order', $request->production_order)->where('docstatus', 0)->orWhere('item_status', 'For Checking')->where('work_order', $request->production_order)->get();

            if($stock_entries){
                $draft_stes = collect($stock_entries)->pluck('name');

                DB::connection('mysql')->table('tabStock Entry')->whereIn('name', $draft_stes)->delete();
                DB::connection('mysql')->table('tabStock Entry Detail')->whereIn('parent', $draft_stes)->delete();
            }

            DB::connection('mysql')->table('tabWork Order')
                ->where('name', $request->production_order)->where('docstatus', 1)->where('status', '!=', 'Completed')
                ->update([
                    'status' => 'Stopped',
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->email
                ]);

            DB::connection('mysql_mes')->table('production_order')
                ->where('production_order', $request->production_order)
                ->update([
                    'status' => 'Closed',
                    'last_modified_at' => $now->toDateTimeString(),
                    'last_modified_by' => Auth::user()->email,
                    'remarks' => $request->reason
                ]);

            DB::connection('mysql_mes')->table('activity_logs')->insert([
                'action' => 'Production Order Closed',
                'message' => 'Production Order '.$request->production_order.' has been closed by '.Auth::user()->employee_name.' at '.Carbon::now()->toDateTimeString() .'<br>Reason: ' . $request->reason,
                'reference' => $request->production_order,
                'created_at' => Carbon::now()->toDateTimeString(),
                'created_by' => Auth::user()->email
            ]);

            DB::connection('mysql')->commit();
            DB::connection('mysql_mes')->commit();
            return response()->json(['success' => 1, 'message' => 'Production Order <b>' . $request->production_order . '</b> has been closed.']);
        } catch (Exception $e) {
            DB::connection('mysql')->rollback();
            DB::connection('mysql_mes')->rollback();
            return response()->json(['success' => 0, 'message' => 'There was a problem creating transaction.']);
        }
    }

    // re-open production order
    public function reopen_production_order(Request $request){
        DB::connection('mysql')->beginTransaction();
        DB::connection('mysql_mes')->beginTransaction();
        try {
            if(!Auth::user()) {
                return response()->json(['success' => 0, 'message' => 'Session Expired. Please login to continue.']);
            } // *PROM-47219

            $now = Carbon::now();

            $production_order = DB::connection('mysql_mes')->table('production_order')->where('status', 'Closed')->where('production_order', $request->production_order)->first();
            $work_order_details = DB::connection('mysql')->table('tabWork Order')->where('status', 'Stopped')->where('name', $request->production_order)->first();
            if(!$production_order || !$work_order_details){
                return response()->json(['success' => 0, 'message' => 'Production Order not found.']);
            }

            $in_progress_job_ticket = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->production_order)->where('status', '!=', 'Pending')->get();

            $mes_status = $erp_status = 'Not Started';
            // EPR Status
            if($work_order_details->material_transferred_for_manufacturing > 0 || $work_order_details->produced_qty > 0){
                $erp_status = 'In Process';
            }

            // MES Status
            if($production_order->produced_qty > 0 && $production_order->produced_qty < $production_order->qty_to_manufacture || count($in_progress_job_ticket) > 0){
                $mes_status = 'In Progress';
            }

            if($production_order->feedback_qty == 0 && $production_order->produced_qty == $production_order->qty_to_manufacture){
                $mes_status = 'Ready for Feedback';
            }

            if($production_order->feedback_qty > 0 && $production_order->feedback_qty < $production_order->qty_to_manufacture){
                $mes_status = 'Partially Feedbacked';
            }

            DB::connection('mysql')->table('tabWork Order')
                ->where('name', $request->production_order)
                ->where('docstatus', 1)->where('status', '!=', 'Completed')
                ->update([
                    'status' => $erp_status,
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->email
                ]);

            DB::connection('mysql_mes')->table('production_order')
                ->where('production_order', $request->production_order)
                ->update([
                    'status' => $mes_status,
                    'last_modified_at' => $now->toDateTimeString(),
                    'last_modified_by' => Auth::user()->email,
                ]);

            DB::connection('mysql_mes')->table('activity_logs')->insert([
                'action' => 'Production Order Re-opened',
                'message' => 'Production Order '.$request->production_order.' has been re-opened by '.Auth::user()->employee_name.' at '.Carbon::now()->toDateTimeString(),
                'reference' => $request->production_order,
                'created_at' => $now->toDateTimeString(),
                'created_by' => Auth::user()->email
            ]);

            DB::connection('mysql')->commit();
            DB::connection('mysql_mes')->commit();
            return response()->json(['success' => 1, 'message' => 'Production Order <b>' . $request->production_order . '</b> has been re-opened.']);
        } catch (Exception $e) {
            DB::connection('mysql')->rollback();
            DB::connection('mysql_mes')->rollback();
            return response()->json(['success' => 0, 'message' => 'There was a problem creating transaction.']);
        }
    }

    // /cancel_production_order
    public function cancel_production_order(Request $request){
        DB::connection('mysql')->beginTransaction();
        DB::connection('mysql_mes')->beginTransaction();
        try {
            if(!Auth::user()) {
                return response()->json(['success' => 0, 'message' => 'Session Expired. Please login to continue.']);
            }

            $now = Carbon::now();

            // get returned items reference stock entry
            $returned_stes = DB::connection('mysql')->table('tabStock Entry as ste')
                ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                ->where('ste.purpose', 'Material Transfer')
                ->where('ste.transfer_as', 'For Return')
                ->where('ste.docstatus', 1)->where('ste.work_order', $request->production_order)
                ->distinct()->pluck('return_reference');

            // get submitted stock entries
            $s_warehouses = ['Fabrication - FI', 'Assembly Warehouse - FI', 'Spotwelding Warehouse - FI'];
            $submitted_ste = DB::connection('mysql')->table('tabStock Entry as ste')
                ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                ->whereNotIn('sted.s_warehouse', $s_warehouses)
                ->whereNotIn('ste.name', $returned_stes)
                ->where('work_order', $request->production_order)
                ->where('ste.docstatus', 1)->where('purpose', 'Material Transfer for Manufacture')
                ->count();

            if($submitted_ste > 0){
                return response()->json(['success' => 0, 'message' => 'Please return issued items before cancelling production order.']);
            }
            // check for task in progress
            $task_in_progress = DB::connection('mysql_mes')->table('job_ticket')
                ->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
                ->where('production_order', $request->production_order)
                ->where('time_logs.status', 'In Progress')->count();

            if ($task_in_progress > 0) {
                return response()->json(['success' => 0, 'message' => 'Cannot cancel production order with on-going task by operator. ' . $request->production_order]);
            }
            // get sum total of feedback qty in production order
            $feedbacked_qty = DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->production_order)->sum('feedback_qty');
            if($feedbacked_qty > 0){
                return response()->json(['success' => 0, 'message' => 'Cannot cancel ' . $request->production_order . '. Production Order has been partially feedbacked.']);
            }
            // get pending material transfer for manufacture stock entries of production order
            $pending_withdrawal_slips = DB::connection('mysql')->table('tabStock Entry')
                ->where('work_order', $request->production_order)
                ->where('purpose', 'Material Transfer for Manufacture')
                ->where('docstatus', 0)->distinct()->pluck('name');

            // delete stock entry detail with source warehouse "fabrication, assembly'"
            DB::connection('mysql')->table('tabStock Entry Detail')
                ->whereIn('parent', $pending_withdrawal_slips)
                ->whereIn('s_warehouse', $s_warehouses)->delete();

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
                    ->where('work_order', $request->production_order)
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

            // get submitted stock entry with source warehouse fabrication and assembly
            $submitted_ste_by_wh = DB::connection('mysql')->table('tabStock Entry as ste')
                ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                ->whereIn('sted.s_warehouse', $s_warehouses)
                ->where('work_order', $request->production_order)
                ->where('ste.docstatus', 1)->where('purpose', 'Material Transfer for Manufacture')
                ->distinct()->pluck('ste.name');

            foreach($submitted_ste_by_wh as $stock_entry){
                // get stock entry items
                $stock_entry_items = DB::connection('mysql')->table('tabStock Entry Detail')
                    ->where('parent', $stock_entry)->get();

                foreach ($stock_entry_items as $row) {
                    if ($row->s_warehouse) {
                        $bin_qry = DB::connection('mysql')->table('tabBin')
                            ->where('warehouse', $row->s_warehouse)
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
                        DB::connection('mysql')->table('tabBin')
                            ->where('name', $bin_qry->name)->update($bin);
                    }

                    if ($row->t_warehouse) {
                        $bin_qry = DB::connection('mysql')->table('tabBin')
                            ->where('warehouse', $row->t_warehouse)
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
                        DB::connection('mysql')->table('tabBin')
                            ->where('name', $bin_qry->name)->update($bin);
                    }
                }

                  // update stock entry parent and child table as cancelled
                $log = [
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->email,
                    'docstatus' => 2
                ];

                DB::connection('mysql')->table('tabStock Entry')
                    ->where('name', $stock_entry)->update($log);
                DB::connection('mysql')->table('tabStock Entry Detail')
                    ->where('parent', $stock_entry)->update($log);
                // delete stock ledger and gl entries
                DB::connection('mysql')->table('tabGL Entry')
                    ->where('voucher_no', $stock_entry)->delete();
                DB::connection('mysql')->table('tabStock Ledger Entry')
                    ->where('voucher_no', $stock_entry)->delete();
            }

            // update transferred qty per production order item to 0
            DB::connection('mysql')->table('tabWork Order Item')
                ->where('parent', $request->production_order)->update(['transferred_qty' => 0]);

            DB::connection('mysql')->table('tabWork Order')
                ->where('name', $request->production_order)
                ->where('docstatus', 1)->where('status', '!=', 'Completed')
                ->update([
                    'docstatus' => 2,
                    'status' => 'Cancelled',
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->email,
                    'material_transferred_for_manufacturing' => 0
                ]);

            DB::connection('mysql_mes')->table('production_order')
                ->where('production_order', $request->production_order)
                ->update([
                    'status' => 'Cancelled',
                    'last_modified_at' => $now->toDateTimeString(),
                    'last_modified_by' => Auth::user()->email,
                    'remarks' => $request->reason_for_cancellation
                ]);

            DB::connection('mysql')->commit();
            DB::connection('mysql_mes')->commit();

            return response()->json(['success' => 1, 'message' => 'Production Order <b>' . $request->production_order . '</b> and its pending withdrawal request has been cancelled.']);
        } catch (Exception $e) {
            DB::connection('mysql')->rollback();
            DB::connection('mysql_mes')->rollback();
            return response()->json(['success' => 0, 'message' => 'There was a problem creating transaction.']);
        }
    }

    // /get_production_order_items/{production_order}
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

        // get production order stock entries
        $stock_entry_arr = DB::connection('mysql')->table('tabStock Entry')
            ->where('purpose', 'Material Transfer for Manufacture')
            ->where('work_order', $production_order)
            ->where('docstatus', '<', 2)->pluck('name');

        $production_order_items = DB::connection('mysql')->table('tabWork Order Item as poi')->where('parent', $production_order)->get();

        $item_codes = array_column($production_order_items->toArray(), 'item_code');

        $s_warehouses = DB::connection('mysql')->table('tabStock Entry Detail')
            ->whereIn('parent', $stock_entry_arr)->whereIn('item_code', $item_codes)
            ->where('docstatus', '<', 2)->pluck('s_warehouse');

        $stock_reservation = DB::connection('mysql')->table('tabStock Reservation')->whereIn('item_code', $item_codes)
            ->whereIn('warehouse', $s_warehouses)->where('status', 'Active')
            ->selectRaw('SUM(reserve_qty) as total_reserved_qty, SUM(consumed_qty) as total_consumed_qty, CONCAT(item_code, "-", warehouse) as item')
            ->groupBy('item_code', 'warehouse')->get();
        $stock_reservation = collect($stock_reservation)->groupBy('item')->toArray();

        $ste_total_issued = DB::connection('mysql')->table('tabStock Entry Detail')->where('docstatus', 0)->where('status', 'Issued')
            ->whereIn('item_code', $item_codes)->whereIn('s_warehouse', $s_warehouses)
            ->selectRaw('SUM(qty) as total_issued, CONCAT(item_code, "-", s_warehouse) as item')
            ->groupBy('item_code', 's_warehouse')->get();
        $ste_total_issued = collect($ste_total_issued)->groupBy('item')->toArray();

        $at_total_issued = DB::connection('mysql')->table('tabAthena Transactions as at')
            ->join('tabPacking Slip as ps', 'ps.name', 'at.reference_parent')
            ->join('tabPacking Slip Item as psi', 'ps.name', 'psi.parent')
            ->join('tabDelivery Note as dr', 'ps.delivery_note', 'dr.name')
            ->whereIn('at.reference_type', ['Packing Slip', 'Picking Slip'])
            ->where('dr.docstatus', 0)->where('ps.docstatus', '<', 2)
            ->where('psi.status', 'Issued')->whereIn('at.item_code', $item_codes)
            ->whereIn('psi.item_code', $item_codes)->whereIn('at.source_warehouse', $s_warehouses)
            ->selectRaw('SUM(at.issued_qty) as total_issued, CONCAT(at.item_code, "-", at.source_warehouse) as item')
            ->groupBy('at.item_code', 'at.source_warehouse')
            ->get();

        $at_total_issued = collect($at_total_issued)->groupBy('item')->toArray();
            
        $components = $parts = [];
        foreach ($production_order_items as $item) {
            $item_details = DB::connection('mysql')->table('tabItem')->where('name', $item->item_code)->first();
            // get item stock based on feedbacked qty for housing and other items with sub assemblies
            $has_production_order = DB::connection('mysql_mes')->table('production_order')
                ->where('item_code', $item->item_code)->where('parent_item_code', $details->parent_item_code)
                ->where('sales_order', $details->sales_order)->where('status', '!=', 'Cancelled')
                ->where('material_request', $details->material_request)
                ->where('sub_parent_item_code', $details->item_code)->first();

            $references = DB::connection('mysql')->table('tabStock Entry as ste')
                ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                ->where('ste.work_order', $production_order)->where('ste.purpose', 'Material Transfer for Manufacture')
                ->where('ste.docstatus', 1)->where('sted.item_code', $item->item_code)
                ->select('ste.name', 'sted.date_modified', 'sted.session_user', 'sted.qty')->get();

            $item_withdrawals = DB::connection('mysql')->table('tabStock Entry Detail')
                ->whereIn('parent', $stock_entry_arr)->where('item_code', $item->item_code)
                ->where('docstatus', 1)
                ->selectRaw('SUM(qty) as qty, s_warehouse, status, SUM(issued_qty) as issued_qty, GROUP_CONCAT(DISTINCT parent) as ste_names, docstatus, GROUP_CONCAT(DISTINCT remarks) as remarks')
                ->groupBy('s_warehouse', 'status', 'docstatus')
                ->get();

            $pending_item_withdrawals = DB::connection('mysql')->table('tabStock Entry Detail')
                ->whereIn('parent', $stock_entry_arr)->where('item_code', $item->item_code)
                ->where('docstatus', 0)
                ->selectRaw('qty, s_warehouse, status, issued_qty, name, docstatus, parent, remarks')
                ->get();

            $transferred_qty = ($item->transferred_qty - $item->returned_qty);

            $returned_qty = $item->returned_qty;

            $has_pending_item_withdrawals = count($pending_item_withdrawals) > 0 ? true : false;

            $withdrawals = [];
            foreach ($item_withdrawals as $i) {
                $reserved_qty = 0;
                if (array_key_exists($item->item_code . '-' . $i->s_warehouse, $stock_reservation)) {
                    $reserved_qty = $stock_reservation[$item->item_code . '-' . $i->s_warehouse][0]->total_reserved_qty;
                }
    
                $consumed_qty = 0;
                if (array_key_exists($item->item_code . '-' . $i->s_warehouse, $stock_reservation)) {
                    $consumed_qty = $stock_reservation[$item->item_code . '-' . $i->s_warehouse][0]->total_consumed_qty;
                }
    
                $reserved_qty = $reserved_qty - $consumed_qty;
    
                $issued_qty = 0;
                if (array_key_exists($item->item_code . '-' . $i->s_warehouse, $ste_total_issued)) {
                    $issued_qty = $ste_total_issued[$item->item_code . '-' . $i->s_warehouse][0]->total_issued;
                }
    
                if (array_key_exists($item->item_code . '-' . $i->s_warehouse, $at_total_issued)) {
                    $issued_qty += $at_total_issued[$item->item_code . '-' . $i->s_warehouse][0]->total_issued;
                }
    
                $actual_qty = DB::connection('mysql')->table('tabBin')->where('item_code', $item->item_code)->where('warehouse', $i->s_warehouse)->sum('actual_qty');
    
                $actual_qty = ($actual_qty - $issued_qty) - $reserved_qty;
                $actual_qty = $actual_qty < 0 ? 0 : $actual_qty;

                $istatus = ($i->docstatus == 1 && $transferred_qty > 0) ? 'Issued' : 'For Checking';
                $irequested_qty = ($i->docstatus == 1 && $transferred_qty > 0) ? $i->qty : $transferred_qty;
                if ($has_pending_item_withdrawals) {
                    $istatus = ($i->docstatus == 1) ? 'Issued' : 'For Checking';
                    $irequested_qty = ($i->docstatus == 1) ? $i->qty : $transferred_qty;
                }

                $withdrawals[] = [
                    'id' => null,
                    'source_warehouse' => $i->s_warehouse,
                    'actual_qty' => $actual_qty,
                    'qty' => ($i->docstatus == 1) ? ($i->qty - $item->returned_qty) : 0,
                    'issued_qty' => ($i->docstatus == 1 && $transferred_qty > 0) ? ($i->issued_qty - $item->returned_qty) : 0,
                    'status' => $istatus,
                    'ste_names' => $i->ste_names,
                    'ste_docstatus' => $i->docstatus,
                    'requested_qty' => $irequested_qty,
                    'remarks' => $i->remarks
                ];
            }

            foreach ($pending_item_withdrawals as $i) {
                $reserved_qty = 0;
                if (array_key_exists($item->item_code . '-' . $i->s_warehouse, $stock_reservation)) {
                    $reserved_qty = $stock_reservation[$item->item_code . '-' . $i->s_warehouse][0]->total_reserved_qty;
                }
    
                $consumed_qty = 0;
                if (array_key_exists($item->item_code . '-' . $i->s_warehouse, $stock_reservation)) {
                    $consumed_qty = $stock_reservation[$item->item_code . '-' . $i->s_warehouse][0]->total_consumed_qty;
                }
    
                $reserved_qty = $reserved_qty - $consumed_qty;
    
                $issued_qty = 0;
                if (array_key_exists($item->item_code . '-' . $i->s_warehouse, $ste_total_issued)) {
                    $issued_qty = $ste_total_issued[$item->item_code . '-' . $i->s_warehouse][0]->total_issued;
                }
    
                if (array_key_exists($item->item_code . '-' . $i->s_warehouse, $at_total_issued)) {
                    $issued_qty += $at_total_issued[$item->item_code . '-' . $i->s_warehouse][0]->total_issued;
                }
    
                $actual_qty = DB::connection('mysql')->table('tabBin')->where('item_code', $item->item_code)->where('warehouse', $i->s_warehouse)->sum('actual_qty');
    
                $actual_qty = ($actual_qty - $issued_qty) - $reserved_qty;
                $actual_qty = $actual_qty < 0 ? 0 : $actual_qty;

                $withdrawals[] = [
                    'id' => $i->name,
                    'source_warehouse' => $i->s_warehouse,
                    'actual_qty' => $actual_qty,
                    'qty' => ($i->docstatus == 1) ? $i->qty : 0,
                    'issued_qty' => ($i->docstatus == 1 && $transferred_qty > 0 && $returned_qty <= 0) ? $i->issued_qty : 0,
                    'status' => ($i->docstatus == 1 && $transferred_qty > 0 && $returned_qty <= 0) ? 'Issued' : 'For Checking',
                    'ste_names' => $i->parent,
                    'ste_docstatus' => $i->docstatus,
                    'requested_qty' => $i->qty,
                    'remarks' => $i->remarks
                ];
            }

            $available_qty_at_wip = $this->get_actual_qty($item->item_code, $details->wip_warehouse);

            $remaining_available_qty_at_wip = $transferred_qty - $item->consumed_qty;
            if($available_qty_at_wip > $remaining_available_qty_at_wip) {
                $available_qty_at_wip = $remaining_available_qty_at_wip;
            }

            $is_alternative = ($item->item_alternative_for && $item->item_alternative_for != 'new_item') ? 1 : 0;

            if($has_production_order && $details->bom_no != null){
                $parts[] = [
                    'name' => $item->name,
                    'idx' => $item->idx,
                    'item_code' => $item->item_code,
                    'item_name' => $item_details->item_name,
                    'description' => $item->description,
                    'item_image' => $item_details->item_image_path,
                    'item_classification' => $item_details->item_classification,
                    'withdrawals' => $withdrawals,
                    'source_warehouse' => $item->source_warehouse,
                    'required_qty' => $item->required_qty,
                    'stock_uom' => $item->stock_uom,
                    'transferred_qty' => $transferred_qty,
                    'actual_qty' => $this->get_actual_qty($item->item_code, $item->source_warehouse),
                    'production_order' => $has_production_order->production_order,
                    'available_qty_at_wip' => $available_qty_at_wip < 0 ? 0 : $available_qty_at_wip,
                    'status' => $has_production_order->status,
                    'references' => $references,
                    'is_alternative' => $is_alternative,
                    'item_alternative_for' => $item->item_alternative_for,
                    'planned_start_date' => $has_production_order->planned_start_date ? Carbon::parse($has_production_order->planned_start_date)->format('M. d, Y') : 'Unscheduled'
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
                    'withdrawals' => $withdrawals,
                    'source_warehouse' => $item->source_warehouse,
                    'required_qty' => $item->required_qty,
                    'stock_uom' => $item->stock_uom,
                    'transferred_qty' => $transferred_qty,
                    'actual_qty' => $this->get_actual_qty($item->item_code, $item->source_warehouse),
                    'production_order' => null,
                    'available_qty_at_wip' => $available_qty_at_wip < 0 ? 0 : $available_qty_at_wip,
                    'status' => null,
                    'references' => $references,
                    'is_alternative' => $is_alternative,
                    'item_alternative_for' => $item->item_alternative_for,
                    'planned_start_date' => null
                ];
            }
        }

        $required_items = array_merge($components, $parts);

        // get returned / for return items linked with production order (stock entry material transfer)
        $item_returns = DB::connection('mysql')->table('tabStock Entry as ste')
            ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
            ->where('ste.purpose', 'Material Transfer')->where('ste.transfer_as', 'For Return')
            ->where('ste.work_order', $production_order)
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
            ->where('ste.docstatus', 0)->where('ste.work_order', $production_order)
            ->where('sted.status', 'Issued')->where('ste.purpose', 'Material Transfer for Manufacture')
            ->sum('qty');

        $feedbacked_logs = DB::connection('mysql_mes')->table('feedbacked_logs')->where('production_order', $production_order)->get();

        $time_logs_qry = DB::connection('mysql_mes')->table('job_ticket')
        	->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
        	->where('job_ticket.production_order', $production_order)->where('job_ticket.status', 'Completed')
        	->get();

        $from_time = collect($time_logs_qry)->min('from_time');
        $to_time = collect($time_logs_qry)->max('to_time');

        $actual_start_date = Carbon::parse($from_time);
        $actual_end_date = Carbon::parse($to_time);

        $days = $actual_start_date->diffInDays($actual_end_date);
        $hours = $actual_start_date->copy()->addDays($days)->diffInHours($actual_end_date);
        $minutes = $actual_start_date->copy()->addDays($days)->addHours($hours)->diffInMinutes($actual_end_date);
        $seconds = $actual_start_date->copy()->addDays($days)->addHours($hours)->addMinutes($minutes)->diffInSeconds($actual_end_date);
        $dur_days = ($days > 0) ? $days .'d' : null;
        $dur_hours = ($hours > 0) ? $hours .'h' : null;
        $dur_minutes = ($minutes > 0) ? $minutes .'m' : null;
        $dur_seconds = ($seconds > 0) ? $seconds .'s' : null;

        $start_date = ($from_time) ? Carbon::parse($from_time)->format('m-d-Y h:i A') : '--';
        $end_date = ($from_time) ? Carbon::parse($to_time)->format('m-d-Y h:i A') : '--';
        $duration = $dur_days .' '. $dur_hours . ' '. $dur_minutes . ' '. $dur_seconds;

        $fast_issuance_warehouse = DB::connection('mysql_mes')->table('fast_issuance_warehouse')->pluck('warehouse')->toArray();
        $is_fast_issuance_user = DB::connection('mysql_mes')->table('fast_issuance_user')->where('user_access_id', Auth::user()->user_id)->exists();

        $ste_transferred_qty = DB::connection('mysql')->table('tabStock Entry as ste')
            ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
            ->where('ste.work_order', $production_order)->where('ste.purpose', 'Material Transfer for Manufacture')
            ->where('ste.docstatus', 1)->sum('sted.qty');

        $activity_logs = DB::connection('mysql_mes')->table('activity_logs')->where('reference', $production_order)->orderBy('created_at', 'desc')->get();

        // collect item codes
        $required_item_codes = collect($required_items)->sortBy('item_code')->pluck('item_code');
        
        // get item codes and qty from submitted withdrawal slips
        $item_codes_with_submitted_ste = DB::connection('mysql')->table('tabStock Entry as ste')
            ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
            ->where('ste.work_order', $production_order)->where('ste.purpose', 'Material Transfer for Manufacture')
            ->where('ste.docstatus', 1)
            ->selectRaw('sted.item_code, sum(sted.issued_qty) as qty')->groupBy('sted.item_code')->get();

        // get qty of returned items
        $item_codes_with_submitted_returns = DB::connection('mysql')->table('tabStock Entry as ste')
            ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
            ->where('ste.work_order', $production_order)->where('ste.purpose', 'Material Transfer')->where('ste.transfer_as', 'For Return')
            ->where('ste.docstatus', 1)->selectRaw('sted.item_code, sum(sted.issued_qty) as qty')->groupBy('sted.item_code')->get();
        $returns = collect($item_codes_with_submitted_returns)->groupBy('item_code');

        // deduct returned qty from the transferred qty per item
        $required_items_grouped = collect($required_items)->groupBy('item_code');
        $item_codes_with_issued_ste = [];
        foreach($item_codes_with_submitted_ste as $stes){
            $req_qty = isset($required_items_grouped[$stes->item_code]) ? $required_items_grouped[$stes->item_code][0]['required_qty'] : 0;
            $returned_qty = isset($returns[$stes->item_code]) ? $returns[$stes->item_code][0]->qty : 0;
            $diff = $stes->qty - $returned_qty;
            if($diff > 0 && $req_qty == $diff){
                array_push($item_codes_with_issued_ste, $stes->item_code);
            }
        }

        // deduct total returned qty from the total transferred qty
        $ste_transferred_qty = (float)$ste_transferred_qty - (float)collect($item_codes_with_submitted_returns)->sum('qty');

        $diff1 = array_diff($required_item_codes->toArray(), $item_codes_with_issued_ste);
        $diff2 = array_diff($item_codes_with_issued_ste, $required_item_codes->toArray());
        $all_items_has_transferred_qty = count(array_merge($diff1, $diff2)) <= 0 ? 1 : 0;

        $checker = 1;
        $qty_checker = collect($required_items)->map(function ($q){
            return $q['transferred_qty'] == $q['required_qty'] ? 1 : 0;
        })->min();

        if($ste_transferred_qty > 0 && $all_items_has_transferred_qty == 1){
            $ste_transferred = (float)number_format($ste_transferred_qty, 10);
            $mes_transferred = (float)number_format(collect($required_items)->sum('transferred_qty'), 10);

            if($ste_transferred != $mes_transferred || $qty_checker == 0){
                $checker = 0;
            }
        }

        $spotwelding_qa = DB::connection('mysql_mes')->table('job_ticket')
            ->join('spotwelding_qty', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
            ->join('quality_inspection as qa', 'qa.reference_id', 'job_ticket.job_ticket_id')
            ->where('job_ticket.production_order', $production_order)
            ->select('job_ticket.job_ticket_id', 'job_ticket.workstation', 'qa.qa_inspection_type', 'qa.qa_inspection_date', 'qa.status as qa_status', 'qa.actual_qty_checked', 'qa.reject_type', 'qa.qc_remarks', 'qa.created_by as qa_owner', 'qa.rejected_qty', 'spotwelding_qty.good', 'spotwelding_qty.reject', 'spotwelding_qty.operator_name', 'qa.created_at');

        $qa_array = DB::connection('mysql_mes')->table('job_ticket')
            ->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
            ->join('quality_inspection as qa', 'qa.reference_id', 'time_logs.time_log_id')
            ->where('job_ticket.production_order', $production_order)
            ->select('job_ticket.job_ticket_id', 'job_ticket.workstation', 'qa.qa_inspection_type', 'qa.qa_inspection_date', 'qa.status as qa_status', 'qa.actual_qty_checked', 'qa.reject_type', 'qa.qc_remarks', 'qa.created_by as qa_owner', 'qa.rejected_qty', 'time_logs.good', 'time_logs.reject', 'time_logs.operator_name', 'qa.created_at')
            ->union($spotwelding_qa)->get();

        $qa_array = collect($qa_array)->sortByDesc(function($col) {
            return sprintf('%s', $col->created_at);
        })->values()->all();

        $reject_reason = [];
        if(in_array('Reject Confirmation', collect($qa_array)->pluck('qa_inspection_type')->toArray())){
            $reject_reason_qry = DB::connection('mysql_mes')->table('reject_reason')
                ->join('reject_list', 'reject_list.reject_list_id', 'reject_reason.reject_list_id')
                ->whereIn('reject_reason.job_ticket_id', collect($qa_array)->pluck('job_ticket_id')->toArray())
                ->get();

            $reject_reason = collect($reject_reason_qry)->groupBy('job_ticket_id');
        }

        return view('tables.tbl_production_order_items', compact('required_items', 'details', 'components', 'parts', 'items_return', 'issued_qty', 'feedbacked_logs', 'start_date', 'end_date', 'duration', 'fast_issuance_warehouse', 'is_fast_issuance_user', 'ste_transferred_qty', 'activity_logs', 'checker', 'all_items_has_transferred_qty', 'qa_array', 'reject_reason'));
    }

    public function create_material_transfer_for_return(Request $request){
        DB::connection('mysql')->beginTransaction();
        try {
            if(!Auth::user()) {
                return response()->json(['status' => 0, 'message' => 'Session Expired. Please login to continue.']);
            }

            $now = Carbon::now();
            $production_order_details = DB::connection('mysql')->table('tabWork Order')
                ->where('name', $request->production_order)->first();
            
            if (!$production_order_details) {
                return response()->json(['status' => 0, 'message' => 'Production Order ' . $request->production_order . ' not found.']);
            }

            if($production_order_details->status == 'Completed'){
                return response()->json(['status' => 2, 'message' => 'Production Order ' . $request->production_order . ' is already Completed.']);
            }

            if($request->qty_to_return <= 0){
                return response()->json(['status' => 0, 'message' => 'Quantity should be greater than 0']);
            }

            if($request->qty_to_return > $request->qty){
                return response()->json(['status' => 0, 'message' => 'Quantity cannot be greater than ' . $request->qty]);
            }

            // check if there are existing request for return
            $pending_returns = DB::connection('mysql')->table('tabStock Entry as ste')
                ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                ->where('ste.docstatus', 0)->where('ste.work_order', $request->production_order)->where('ste.purpose', 'Material Transfer')->where('sted.item_code', $request->item_code)->sum('transfer_qty');

            // if return qty + existing pending for return exceeds item qty
            if(($pending_returns + $request->qty_to_return) > $request->qty_to_return){
                return response()->json(['status' => 0, 'message' => 'Request for return for item <b>'.$request->item_code.'</b> already exists']);
            }
            
            // copy values from stock entry detail
            $stock_entry_details = DB::connection('mysql')->table('tabStock Entry as ste')
				        ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
				        ->where('ste.docstatus', 1)->where('ste.work_order', $request->production_order)
                ->where('ste.purpose', 'Material Transfer for Manufacture')->whereIn('ste.name', explode(',', $request->ste_names))
                ->where('sted.item_code', $request->item_code)
                ->select('ste.*', 'sted.*', 'ste.name as ste_name')->first();

            if (!$stock_entry_details) {
                return response()->json(['status' => 0, 'message' => 'Stock entry item ' . $request->item_code . ' not found.']);
            }

            $latest_ste = DB::connection('mysql')->table('tabStock Entry')->where('name', 'like', '%step%')->max('name');
            $latest_ste_exploded = explode("-", $latest_ste);
            $new_id = (($latest_ste) ? $latest_ste_exploded[1] : 0) + 1;
            $new_id = str_pad($new_id, 6, '0', STR_PAD_LEFT);
            $new_id = 'STEP-'.$new_id;

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
                $item_classification = DB::connection('mysql')->table('tabItem')->where('name', $request->item_code)->pluck('item_classification')->first();
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
                'return_reference' => $stock_entry_details->ste_name
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
                // 'customer_name' => null,
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
                // 'customer_address' => null,
                'total_outgoing_value' => collect($stock_entry_detail)->sum('basic_amount'),
                'supplier_name' => null,
                'remarks' => null,
                '_user_tags' => null,
                'total_additional_costs' => 0,
                // 'customer' => null,
                'bom_no' => null,
                'amended_from' => null,
                'total_amount' => collect($stock_entry_detail)->sum('basic_amount'),
                'total_incoming_value' => collect($stock_entry_detail)->sum('basic_amount'),
                'project' => $production_order_details->project,
                '_assign' => null,
                'select_print_heading' => null,
                'posting_date' => $now->format('Y-m-d'),
                'target_address_display' => null,
                'work_order' => $production_order_details->name,
                'purpose' => 'Material Transfer',
                'stock_entry_type' => 'Material Transfer',
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
                $update_bin_res = $this->update_bin($new_id);
                if ($update_bin_res['status'] == 0) {
                    return response()->json(['status' => 0, 'message' => $update_bin_res['message']]);
                }
                $this->create_stock_ledger_entry($new_id);
                $this->create_gl_entry($new_id);

                // update production order item transferred qty - return
                $production_order_item = DB::connection('mysql')->table('tabWork Order Item')->where('name', $request->id)->first();
                if($production_order_item){
                    $transferred_qty = $production_order_item->transferred_qty - $request->qty_to_return;
                    DB::connection('mysql')->table('tabWork Order Item')->where('name', $request->id)->update(['transferred_qty' => $transferred_qty]);
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
            ->where('ste.purpose', 'Material Transfer for Manufacture')
            ->where('ste.work_order', $production_order)
            ->where('ste.docstatus', 1)->whereNotIn('sted.s_warehouse', ['Fabrication - FI', 'Spotwelding Warehouse - FI'])
            ->selectRaw('sted.item_code, sted.s_warehouse, sted.t_warehouse, GROUP_CONCAT(DISTINCT ste.name) as ste_names, SUM(sted.qty) as qty')
            ->groupBy('sted.item_code', 'sted.s_warehouse', 'sted.t_warehouse')->get();

        $items = [];
        foreach ($q as $item) {
            $item_details = DB::connection('mysql')->table('tabItem')->where('name', $item->item_code)->first();
            $item_classification = $item_details->item_classification;

             // get returned / for return items linked with production order (stock entry material transfer)
            $item_return = DB::connection('mysql')->table('tabStock Entry as ste')
                ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                ->where('ste.purpose', 'Material Transfer')->where('ste.transfer_as', 'For Return')
                ->where('ste.work_order', $production_order)
                ->where('sted.item_code', $item->item_code)->where('ste.docstatus', '<', 2)
                ->select('sted.*', 'ste.docstatus')->first();

            $status = 'For Return';
            if($item_return){
                if($item_return->docstatus < 1) {
                    $status = 'Pending';
                } else {
                    $status = 'Returned';
                }
            }

            $items[] = [
                'ste_names' => $item->ste_names,
                'item_code' => $item->item_code,
                'description' => $item_details->description,
                'item_image' => $item_details->item_image_path,
                'item_classification' => $item_classification,
                'source_warehouse' => $item->s_warehouse,
                'target_warehouse' => $item->t_warehouse,
                'qty' => $item->qty,
                'stock_uom' => $item_details->stock_uom,
                'status' => $status,
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

    // update_ste_detail
    // function to submit change / replacement of item code
    public function update_ste_detail(Request $request){
        DB::connection('mysql')->beginTransaction();
        try {
            if(!Auth::user()) {
                return response()->json(['status' => 0, 'message' => 'Session Expired. Please login to continue.']);
            }

			$now = Carbon::now();
            $production_order_details = DB::connection('mysql')->table('tabWork Order')->where('name', $request->production_order)->first();

            $is_valid = false;
            $production_order_item_detail = DB::connection('mysql')->table('tabWork Order Item')->where('parent', $request->production_order)
                ->where('name', $request->production_order_item_id)->first();

            if ($production_order_item_detail) {
                if ($production_order_item_detail->transferred_qty > 0 && ($production_order_item_detail->transferred_qty - $production_order_item_detail->returned_qty) <= 0) {
                    $is_valid = true;
                }
            }

            if (!$is_valid) {
                 // get stock entry transferred qty
                $ste_transferred_qty = DB::connection('mysql')->table('tabStock Entry as ste')
                    ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                    ->where('ste.docstatus', 1)->where('ste.work_order', $request->production_order)
                    ->where('sted.item_code', $request->old_item_code)->where('ste.purpose', 'Material Transfer for Manufacture')
                    ->sum('qty');

                if($request->old_item_code != $request->item_code){
                    // get production order item transferred qty
                    $transferred_qty = DB::connection('mysql')->table('tabWork Order Item')
                        ->where('parent', $request->production_order)->where('item_code', $request->old_item_code)->sum('transferred_qty');

                    if($transferred_qty > 0){
                        return response()->json(['status' => 0, 'message' => 'Item has been already issued. Click "Add Item" button below to add items for issue.']);
                    }

                    if($ste_transferred_qty > 0){
                        return response()->json(['status' => 0, 'message' => 'Item has been already issued. Click "Add Item" button below to add items for issue.']);
                    }
                }
            }
           
            $item_details = DB::connection('mysql')->table('tabItem')->where('name', $request->item_code)->first();
            if(!$item_details){
                return response()->json(['status' => 0, 'message' => 'Item <b>'. $request->item_code.'</b> not found.']);
            }

			// get all pending stock entries based on item code production order
			$pending_stock_entries = DB::connection('mysql')->table('tabStock Entry as ste')
				->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
				->where('ste.docstatus', 0)->where('ste.work_order', $request->production_order)
				->where('sted.item_code', $request->old_item_code)->whereIn('ste.name', explode(',', $request->ste_names))
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
                        'qty' => $request->requested_quantity,
                        'transfer_qty' => $request->requested_quantity,
                        's_warehouse' => $request->source_warehouse,
                        'item_note' => $request->remarks,
                        'status' => $item_status,
                        'date_modified' => ($item_status == 'Issued') ? $now->toDateTimeString() : null,
                        'session_user' => ($item_status == 'Issued') ? Auth::user()->employee_name : null,
                        'remarks' => ($item_status == 'Issued') ? 'MES' : null,
                        'issued_qty' => ($item_status == 'Issued') ? $request->requested_quantity : 0,
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
                // 'required_qty' => $request->quantity,
                'available_qty_at_source_warehouse' => 0,
                'available_qty_at_wip_warehouse' => 0,
                'source_warehouse' => $request->source_warehouse
            ];

            DB::connection('mysql')->table('tabWork Order Item')
                ->where('parent', $request->production_order)->where('item_code', $request->old_item_code)
                ->update($production_order_item);

            $reference = null;
            $message = null;
            if($request->old_item_code != $request->item_code){
                $reference = 'Changed Code';
                $message = 'Changed item code from '.$request->old_item_code.' to '.$request->item_code.' from '.$request->source_warehouse;
            }else if($production_order_item_detail->source_warehouse != $request->source_warehouse){
                $reference = 'Changed Source Warehouse';
                $message = 'Changed source warehouse of '.$request->item_code.' from '.$production_order_item_detail->source_warehouse.' to '.$request->source_warehouse;
            }else if($request->old_requested_quantity != $request->requested_quantity){
                $reference = 'Changed Requested Qty';
                $message = 'Changed requested qty of '.$request->item_code.' from '.$request->old_requested_quantity.' '.$production_order_item_detail->stock_uom.' to '.$request->requested_quantity.' '.$production_order_item_detail->stock_uom;
            }

            $message = $message.' for '.$request->production_order.' by '.Auth::user()->employee_name.' at '.Carbon::now()->toDateTimeString();

            $activity_logs = [
                'action' => $reference,
                'message' => $message,
                'reference' => $request->production_order,
                'created_at' => Carbon::now()->toDateTimeString(),
                'created_by' => Auth::user()->email
            ];

            if($reference && $message){
                DB::connection('mysql_mes')->table('activity_logs')->insert($activity_logs);
            }
            
            DB::connection('mysql')->commit();
            DB::connection('mysql_mes')->commit();

            return response()->json(['status' => 1, 'message' => 'Stock entry item has been changed.']);
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'message' => 'There was a problem updating stock entry.']);
            DB::connection('mysql')->rollback();
        }
    }

    // /update_production_order_item_required_qty
    public function update_production_order_item_required_qty(Request $request){
        if(!Auth::user()) {
            return response()->json(['status' => 0, 'message' => 'Session Expired. Please login to continue.']);
        }

        $production_order_item = DB::connection('mysql')->table('tabWork Order Item as poi')
            ->join('tabWork Order as po', 'poi.parent', 'po.name')->where('poi.name', $request->production_order_item_id)
            ->select('poi.item_code', 'po.status', 'po.name as production_order', 'po.produced_qty', 'po.qty', 'poi.stock_uom')->first();

        if (!$production_order_item) {
            return response()->json(['status' => 0, 'message' => 'Record not found.']);
        }

        if ($production_order_item->produced_qty == $production_order_item->qty) {
            return response()->json(['status' => 0, 'message' => 'Production Order <b>' . $production_order_item->production_order .'</b> is already Completed.']);
        }
        // get transferred qty
        $transferred_qty = DB::connection('mysql')->table('tabStock Entry as ste')
            ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
            ->where('ste.docstatus', 1)->where('ste.work_order', $production_order_item->production_order)
            ->where('sted.item_code', $production_order_item->item_code)->where('ste.purpose', 'Material Transfer for Manufacture')
            ->sum('qty');

        if((float)$request->qty < (float)$transferred_qty){
            return response()->json(['status' => 0, 'message' => 'Quantity cannot be less than transferred qty (' . $transferred_qty . ')']);
        }

        DB::connection('mysql')->table('tabWork Order Item')->where('name', $request->production_order_item_id)->update(['required_qty' => $request->qty]);

        $activity_logs = [
            'action' => 'Changed Required Qty',
            'message' => 'Changed required qty of '.$production_order_item->item_code.' from '.$request->required_qty.' '.$production_order_item->stock_uom.' to '.$request->qty.' '.$production_order_item->stock_uom.' for '.$production_order_item->production_order.' by '.Auth::user()->employee_name.' at '.Carbon::now()->toDateTimeString(),
            'reference' => $production_order_item->production_order,
            'created_at' => Carbon::now()->toDateTimeString(),
            'created_by' => Auth::user()->email
        ];

        if($request->required_qty != $request->qty){
            DB::connection('mysql_mes')->table('activity_logs')->insert($activity_logs);
        }

        return response()->json(['status' => 1, 'message' => 'Required qty has been updated.', 'production_order' => $production_order_item->production_order]);
    }

    public function add_ste_items(Request $request){
        DB::connection('mysql')->beginTransaction();
        try {
            if(!Auth::user()) {
                return response()->json(['status' => 0, 'message' => 'Session Expired. Please login to continue.']);
            }

            if(count($request->item_code) < 1){
                return response()->json(['status' => 2, 'message' => 'Please enter items to be added.']);
            }
            $now = Carbon::now();
            $mes_production_order_details = DB::connection('mysql_mes')->table('production_order')
                ->where('production_order', $request->production_order)->first();

            $item_alts = collect($request->item_as)->map(function ($q){
                if($q != 'new_item'){
                    return $q;
                }
            });

            $item_alts = $item_alts->filter(function ($q){
                return !is_null($q);
            });

            $main_item_uoms = [];
            $main_item_uom = [];
            if(count($item_alts) > 0){
                $main_item_oums = DB::connection('mysql')->table('tabItem')->whereIn('name', $item_alts)->select('name', 'stock_uom')->get();
                $main_item_uom = collect($main_item_oums)->groupBy('name');
            }

            $stock_entry_data = [];
            foreach ($request->item_code as $id => $item_code) {
                $item_details = DB::connection('mysql')->table('tabItem')->where('name', $item_code)->first();

                // Check if item alternatives and the main item has the same UOM
                if(isset($main_item_uom[$request->item_as[$id]]) and $main_item_uom[$request->item_as[$id]][0]->stock_uom != $item_details->stock_uom){
                    return response()->json(['status' => 0, 'message' => 'Cannot add '.$item_code.' as an alternative for '.$request->item_as[$id].'. UOM does not match.']);
                }

                $qty = $request->quantity[$id];

                if($qty <= 0) {
                    return response()->json(['status' => 0, 'message' => 'Qty for item ' . $item_code . ' should be greater than 0.']);
                }

                $existing_production_item = DB::connection('mysql')->table('tabWork Order Item')
                    ->where('parent', $request->production_order)->where('item_code', $item_code)->first();

                if($existing_production_item) {
                    return response()->json(['status' => 2, 'message' => 'Item Code ' . $item_code . ' already exist.']);
                }

                if(!$existing_production_item){
                    // get remaining required qty if item is an alternative
                    $message = 'Added '.$request->quantity[$id].' '.$item_details->stock_uom.' of '.$item_details->item_code.' from '.$request->source_warehouse[$id].' for '.$request->production_order.' by '.Auth::user()->employee_name.' at '.Carbon::now()->toDateTimeString();
                    $reference = 'New Item';
                    if($request->item_as[$id] != 'new_item'){
                        $alternative_for = DB::connection('mysql')->table('tabWork Order Item')
                            ->where('parent', $request->production_order)->where('item_code', $request->item_as[$id])
                            ->first();
                        
                        // validate qty vs remaining required qty
                        $remaining_required_qty = $alternative_for->required_qty - ($alternative_for->transferred_qty - $alternative_for->returned_qty);
                        if($remaining_required_qty <= $qty){
                            return response()->json(['status' => 0, 'message' => 'Qty cannot be greater than or equal to <b>' . $remaining_required_qty . '</b> for <b>' . $item_code .'</b>.']);
                        }

                        if (($remaining_required_qty) <= 0) {
                            return response()->json(['status' => 0, 'message' => 'Qty cannot be greater than or equal to <b>' . $remaining_required_qty . '</b> for <b>' . $item_code .'</b>.']);
                        }

                        $st_entries = DB::connection('mysql')->table('tabStock Entry as ste')
                            ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                            ->where('ste.purpose', 'Material Transfer for Manufacture')->where('ste.docstatus', 0)
                            ->where('ste.work_order', $request->production_order)->where('sted.item_code', $alternative_for->item_code)->pluck('ste.name');

                        DB::connection('mysql')->table('tabStock Entry Detail')
                            ->whereIn('parent', $st_entries)->where('item_code', $alternative_for->item_code)
                            ->update(['qty' => ($remaining_required_qty - $qty), 'transfer_qty' => ($remaining_required_qty - $qty)]);
                            
                        DB::connection('mysql')->table('tabWork Order Item')
                            ->where('parent', $request->production_order)->where('item_code', $alternative_for->item_code)
                            ->update(['required_qty' => ($alternative_for->required_qty - $qty)]);

                        $message = 'Added '.$request->quantity[$id].' '.$item_details->stock_uom.' of '.$item_details->item_code.' as an alternative for '.$alternative_for->item_code.' from '.$request->source_warehouse[$id].' for '.$request->production_order.' by '.Auth::user()->employee_name.' at '.Carbon::now()->toDateTimeString();
                        $reference = 'Item Alternative';
                    }

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
                        'parenttype' => 'Work Order',
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
                        'item_alternative_for' => $request->item_as[$id]
                    ];

                    DB::connection('mysql')->table('tabWork Order Item')->insert($production_order_item);

                    $activity_logs = [
                        'action' => $reference,
                        'message' => $message,
                        'reference' => $request->production_order,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'created_by' => Auth::user()->email
                    ];

                    DB::connection('mysql_mes')->table('activity_logs')->insert($activity_logs);
                }else{
                    // update required_qty for additional 
                    $production_order_item = [
                        'modified' => $now->toDateTimeString(),
                        'modified_by' => Auth::user()->email,
                    ];

                    DB::connection('mysql')->table('tabWork Order Item')->where('name', $existing_production_item->name)->update($production_order_item);
                }

                $latest_ste = DB::connection('mysql')->table('tabStock Entry')->where('name', 'like', '%step%')->max('name');
                $latest_ste_exploded = explode("-", $latest_ste);
                $new_id = (($latest_ste) ? $latest_ste_exploded[1] : 0) + 1;
                $new_id = str_pad($new_id, 6, '0', STR_PAD_LEFT);
                $new_id = 'STEP-'.$new_id;

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
                    // 'customer_name' => null,
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
                    // 'customer_address' => null,
                    'total_outgoing_value' => collect($stock_entry_detail)->sum('basic_amount'),
                    'supplier_name' => null,
                    'remarks' => null,
                    '_user_tags' => null,
                    'total_additional_costs' => 0,
                    // 'customer' => null,
                    'bom_no' => $mes_production_order_details->bom_no,
                    'amended_from' => null,
                    'total_amount' => collect($stock_entry_detail)->sum('basic_amount'),
                    'total_incoming_value' => collect($stock_entry_detail)->sum('basic_amount'),
                    'project' => $mes_production_order_details->project,
                    '_assign' => null,
                    'select_print_heading' => null,
                    'posting_date' => $now->format('Y-m-d'),
                    'target_address_display' => null,
                    'work_order' => $request->production_order,
                    'purpose' => 'Material Transfer for Manufacture',
                    'stock_entry_type' => 'Material Transfer for Manufacture',
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

                $pending_ste = DB::connection('mysql')->table('tabStock Entry Detail as sted')
                    ->join('tabStock Entry as ste', 'ste.name', 'sted.parent')->where('ste.purpose', 'Material Transfer for Manufacture')
                    ->where('sted.item_code', $item_details->item_code)->where('ste.work_order', $request->production_order)
                    ->where('ste.docstatus', 0)->first();

                if (!$pending_ste) {
                    DB::connection('mysql')->table('tabStock Entry')->insert($stock_entry_data);
                    DB::connection('mysql')->table('tabStock Entry Detail')->insert($stock_entry_detail);
                }
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
            if(!Auth::user()) {
                return response()->json(['success' => 0, 'message' => 'Session Expired. Please login to continue.']);
            }

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

            $items = DB::connection('mysql')->table('tabWork Order Item')
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

            if(!Auth::user()) {
                return response()->json(['success' => 0, 'message' => 'Session Expired. Please login to continue.']);
            }

            if($request->reference_type == 'SO' && !$request->sales_order){
                return response()->json(['success' => 0, 'message' => 'Please enter reference Sales Order.']);
            }

            if($request->reference_type == 'MREQ' && !$request->material_request){
                return response()->json(['success' => 0, 'message' => 'Please enter reference Material Request.']);
            }

            if (!$request->qty) {
                return response()->json(['success' => 0, 'message' => 'Qty cannot be less than or equal to 0.']);
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

            // if ($per_status >= 100) {
            //     return response()->json(['success' => 0, 'message' => $reference_name . ' was already COMPLETED']);
            // }

            $item = DB::connection('mysql')->table('tabItem')->where('name', $request->item_code)->first();
            if (!$item) {
                return response()->json(['success' => 0, 'message' => 'Item ' .$request->item_code. ' not found.']);
            }

            if(!$request->custom_bom && $item->is_stock_item == 0){
                return response()->json(['success' => 0, 'message' => 'Item ' .$request->item_code. ' is not a stock item.']);
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

            $latest_pro = DB::connection('mysql')->table('tabWork Order')->max('name');
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
            
            if (!$data_mes['bom_no']) {
                if ($request->reference_type == 'SO') {
                    $ordered_item = DB::connection('mysql')->table('tabSales Order Item')->where('parent', $reference_name)->where('item_code', $data_mes['item_code'])->first();
                } else {
                    $ordered_item = DB::connection('mysql')->table('tabMaterial Request Item')->where('parent', $reference_name)->where('item_code', $data_mes['item_code'])->first();
                }
    
                if (!$ordered_item) {
                    return response()->json(['success' => 0, 'message' => 'Item code ' . $data_mes['item_code'] . ' does not exist in ' . (($request->reference_type == 'SO') ? 'Sales Order' : 'Material Request') . ' items. (' . $reference_name . ')']);
                }

                $check_existing_production_order = DB::connection('mysql_mes')->table('production_order')
                    ->where('parent_item_code', $data_mes['parent_item_code'])
                    ->where('sub_parent_item_code', $data_mes['sub_parent_item_code'])
                    ->where('item_code', $data_mes['item_code'])
                    ->where('sales_order', $data_mes['sales_order'])
                    ->where('material_request', $data_mes['material_request'])
                    ->where('status', '!=', 'Cancelled')->get();

                $existing_prod_qty = collect($check_existing_production_order)->sum('qty_to_manufacture');
                $existing_prods = '<br><br>';
                $existing_pros = [];
                foreach($check_existing_production_order as $cepo) {
                    $existing_pros[$cepo->production_order] = $cepo->qty_to_manufacture;
                    if ($cepo->production_order != $data_mes['production_order']) {
                        $existing_prods .= $cepo->production_order . ' - ' . $cepo->qty_to_manufacture . ' ' . $cepo->stock_uom . '<br>';
                    }
                }

                unset($existing_pros[$data_mes['production_order']]);
                if ((float)$existing_prod_qty > (float)$ordered_item->qty) {
                    return response()->json(['success' => 0, 'message' => 'Qty to produce for <b>' . $data_mes['item_code'] . '</b> cannot be greater than <b>' . (float)$ordered_item->qty . '</b> ' . $ordered_item->stock_uom . $existing_prods]);
                }
            }
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
                        'bom_operation_id' => 'op' . uniqid(),
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
                    'delivery_date' => ($request->reference_type == 'SO') ? $reference_child_details->delivery_date : $reference_child_details->schedule_date,
                    'created_by' => Auth::user()->email
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
                DB::connection('mysql')->table('tabWork Order')->insert($data);

                if($request->custom_bom){
                    $raw_required_items = [];
                    if($request->is_stock_item > 0){
                        $req_item_detail = DB::connection('mysql')->table('tabItem')
                            ->where('name', $request->item_code)->first();

                        $item_default_warehouse = DB::connection('mysql')->table('tabItem Default')->where('parent', $req_item_detail->name)
                            ->where('company', 'FUMACO Inc.')->first();
                        $item_default_warehouse = ($item_default_warehouse) ? $item_default_warehouse->default_warehouse : null;
    
                        $raw_required_items = [
                            'name' => 'mes'.uniqid(),
                            'creation' => $now->toDateTimeString(),
                            'modified' => $now->toDateTimeString(),
                            'modified_by' => Auth::user()->email,
                            'owner' => Auth::user()->email,
                            'docstatus' => 1,
                            'parent' => $new_id,
                            'parentfield' => 'required_items',
                            'parenttype' => 'Work Order',
                            'idx' => 1,
                            'description' => $req_item_detail->description,
                            'item_name' => $req_item_detail->item_name,
                            'item_code' => $req_item_detail->item_code,
                            'required_qty' => $request->qty,
                            'transferred_qty' => 0,
                            'available_qty_at_source_warehouse' => 0,
                            'available_qty_at_wip_warehouse' => 0,
                            'source_warehouse' => $item_default_warehouse,
                            'stock_uom' => $req_item_detail->stock_uom
                        ];
                    }else{
                        $bundle_items = DB::connection('mysql')->table('tabProduct Bundle Item')->where('parent', $request->item_code)->get();
                        foreach ($bundle_items as $k => $v) {
                            $req_item_detail = DB::connection('mysql')->table('tabItem')
                                ->where('name', $v->item_code)->first();

                            $item_default_warehouse = DB::connection('mysql')->table('tabItem Default')->where('parent', $req_item_detail->name)
                                ->where('company', 'FUMACO Inc.')->first();
                            $item_default_warehouse = ($item_default_warehouse) ? $item_default_warehouse->default_warehouse : null;

                            $raw_required_items[] = [
                                'name' => 'mes'.uniqid(),
                                'creation' => $now->toDateTimeString(),
                                'modified' => $now->toDateTimeString(),
                                'modified_by' => Auth::user()->email,
                                'owner' => Auth::user()->email,
                                'docstatus' => 1,
                                'parent' => $new_id,
                                'parentfield' => 'required_items',
                                'parenttype' => 'Work Order',
                                'idx' => $k + 1,
                                'description' => $req_item_detail->description,
                                'item_name' => $req_item_detail->item_name,
                                'item_code' => $req_item_detail->item_code,
                                'required_qty' => $v->qty * $request->qty,
                                'transferred_qty' => 0,
                                'available_qty_at_source_warehouse' => 0,
                                'available_qty_at_wip_warehouse' => 0,
                                'source_warehouse' => $item_default_warehouse,
                                'stock_uom' => $v->uom
                            ];
                        }
                    }

                    DB::connection('mysql')->table('tabWork Order Item')->insert($raw_required_items);

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
                            'parenttype' => 'Work Order',
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
                            'bom_operation_id' => $mes_custom_operations[$p]['bom_operation_id']
                        ];
                    }
    
                    DB::connection('mysql')->table('tabWork Order Operation')->insert($custom_operations);
                }else{
                    $required_items = $this->save_production_req_items($new_id, $request->bom, $request->qty, $request->operation);
                    $this->save_production_operations($new_id, $request->bom, ($request->planned_date) ? $request->planned_date : null, 'erp');

                    if($required_items['error'] == 1){
                        return response()->json(["success" => 0, 'message' => $required_items['message']]);
                    }
                }

                $this->insert_job_card($new_id);
                
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

    // /generate_stock_entry/{production_order}
    public function generate_stock_entry($production_order, Request $request){
        DB::connection('mysql')->beginTransaction();
        try {
            if(!Auth::user()) {
                return response()->json(['success' => 0, 'message' => 'Session Expired. Please refresh the page and login to continue.']);
            }

            $new_id = null;
            $now = Carbon::now();
            $mes_production_order_details = DB::connection('mysql_mes')->table('production_order')
                ->where('production_order', $production_order)->first();

            // get raw materials from production order items in erp
            $production_order_items = DB::connection('mysql')->table('tabWork Order Item')
                ->where('parent', $production_order)->orderBy('idx', 'asc')->get();

            $remaining_qty = 0;
            foreach ($production_order_items as $index => $row) {
                if($request->s_warehouses){
                    $source_warehouse = $request->s_warehouses[$index];
                }else{
                    $source_warehouse = $row->source_warehouse;
                }

                $pending_ste = DB::connection('mysql')->table('tabStock Entry Detail as sted')
                    ->join('tabStock Entry as ste', 'ste.name', 'sted.parent')->where('ste.purpose', 'Material Transfer for Manufacture')
                    ->where('sted.item_code', $row->item_code)->where('ste.work_order', $row->parent)
                    ->where('ste.docstatus', 0)->first();
                
                $actual_qty = DB::connection('mysql')->table('tabBin')
                    ->where('item_code', $row->item_code)->where('warehouse', $source_warehouse)
                    ->sum('actual_qty');

                if(in_array($source_warehouse, ['Fabrication - FI', 'Spotwelding Warehouse - FI']) && $mes_production_order_details->operation_id == 1){
                    $item_status = 'Issued';
                    $validate_item_code = $row->item_code;
                } else {
                    $item_status = 'For Checking';
                    $validate_item_code = null;
                }

                $docstatus = ($actual_qty >= $row->required_qty) ? 1 : 0;
                $docstatus = ($item_status == 'Issued') ? $docstatus : 0;

                $reference = null;
                if(!$pending_ste){
                    $remaining_qty = $row->required_qty - ($row->transferred_qty - $row->returned_qty);

                    $issued_qty = DB::connection('mysql')->table('tabStock Entry as ste')
                        ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                        ->where('ste.work_order', $row->parent)
                        ->where('sted.item_code', $row->item_code)
                        ->where('sted.s_warehouse', $source_warehouse)
                        ->where('ste.docstatus', 0)
                        ->where('sted.status', 'Issued')->sum('sted.qty');

                    $remaining_qty = $remaining_qty - $issued_qty;
                    if($remaining_qty > 0){
                        $latest_ste = DB::connection('mysql')->table('tabStock Entry')->where('name', 'like', '%step%')->max('name');
                        $latest_ste_exploded = explode("-", $latest_ste);
                        $new_id = (($latest_ste) ? $latest_ste_exploded[1] : 0) + 1;
                        $new_id = str_pad($new_id, 6, '0', STR_PAD_LEFT);
                        $new_id = 'STEP-'.$new_id;
                        
                        $reference = $new_id;

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
                            'validate_item_code' => $validate_item_code
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
                            'fg_completed_qty' => ($index == 0) ? $mes_production_order_details->qty_to_manufacture : 0,
                            'letter_head' => null,
                            '_liked_by' => null,
                            'purchase_receipt_no' => null,
                            'posting_time' => $now->format('H:i:s'),
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
                            'total_outgoing_value' => collect($stock_entry_detail)->sum('basic_amount'),
                            'supplier_name' => null,
                            'remarks' => null,
                            '_user_tags' => null,
                            'total_additional_costs' => 0,
                            'bom_no' => $mes_production_order_details->bom_no,
                            'amended_from' => null,
                            'total_amount' => collect($stock_entry_detail)->sum('basic_amount'),
                            'total_incoming_value' => collect($stock_entry_detail)->sum('basic_amount'),
                            'project' => $mes_production_order_details->project,
                            '_assign' => null,
                            'select_print_heading' => null,
                            'posting_date' => $now->format('Y-m-d'),
                            'target_address_display' => null,
                            'work_order' => $production_order,
                            'purpose' => 'Material Transfer for Manufacture',
                            'stock_entry_type' => 'Material Transfer for Manufacture',
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
                                'transferred_qty' => ($row->transferred_qty + $remaining_qty)
                            ];
            
                            DB::connection('mysql')->table('tabWork Order Item')->where('name', $row->name)->update($production_order_item);

                            if($mes_production_order_details->status == 'Not Started'){
                                $values = [
                                    'status' => 'In Process',
                                    'material_transferred_for_manufacturing' => $mes_production_order_details->qty_to_manufacture
                                ];
                            }else{
                                $values = [
                                    'material_transferred_for_manufacturing' => $mes_production_order_details->qty_to_manufacture
                                ];
                            }
                            
                            DB::connection('mysql')->table('tabWork Order')
                                ->where('name', $mes_production_order_details->production_order)
                                ->update($values);

                            $update_bin_res = $this->update_bin($new_id);
                            if ($update_bin_res['status'] == 0) {
                                return response()->json(['success' => 0, 'message' => $update_bin_res['message']]);
                            }
                            $this->create_stock_ledger_entry($new_id);
                            $this->create_gl_entry($new_id);
                        }
                    }
                } else {
                    if ($docstatus == 1) {
                        $production_order_item = [
                            'transferred_qty' => ($row->transferred_qty + $remaining_qty)
                        ];
        
                        DB::connection('mysql')->table('tabWork Order Item')->where('name', $row->name)->update($production_order_item);

                        if($mes_production_order_details->status == 'Not Started'){
                            $values = [
                                'status' => 'In Process',
                                'material_transferred_for_manufacturing' => $mes_production_order_details->qty_to_manufacture
                            ];
                        }else{
                            $values = [
                                'material_transferred_for_manufacturing' => $mes_production_order_details->qty_to_manufacture
                            ];
                        }
                        
                        DB::connection('mysql')->table('tabWork Order')
                            ->where('name', $mes_production_order_details->production_order)
                            ->update($values);

                        $update_bin_res = $this->update_bin($pending_ste->name);
                        if ($update_bin_res['status'] == 0) {
                            return response()->json(['success' => 0, 'message' => $update_bin_res['message']]);
                        }
                        $this->create_stock_ledger_entry($pending_ste->name);
                        $this->create_gl_entry($pending_ste->name);

                        $remaining_qty = $mes_production_order_details->qty_to_manufacture;
                        $reference = $pending_ste->name;
                    }
                }

                $activity_logs = [
                    'action' => 'Created Withdrawal Slip',
                    'message' => 'Created withdrawal slip '.$reference.' for '.$remaining_qty.' '.$row->stock_uom.' of '.$row->item_code.' for '.$production_order.' by '.Auth::user()->employee_name.' at '.Carbon::now()->toDateTimeString(),
                    'reference' => $reference,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'created_by' => Auth::user()->email
                ];
        
                DB::connection('mysql_mes')->table('activity_logs')->insert($activity_logs);
            }

            DB::connection('mysql')->commit();

            return response()->json(['success' => 1, 'message' => 'Stock Entry has been created.', 'id' => $new_id]);
        } catch (Exception $e) {
            DB::connection('mysql')->rollback();
            return response()->json(['success' => 0, 'message' => 'There was a problem creating stock entries.']);
        }
    }

    // stock ledger for material transfer for manufacture
    public function create_stock_ledger_entry($stock_entry){
        try {
            $now = Carbon::now();
            $stock_entry_qry = DB::connection('mysql')->table('tabStock Entry')->where('name', $stock_entry)->first();
            if ($stock_entry_qry && $stock_entry_qry->docstatus == 1) {
                $stock_entry_detail = DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $stock_entry)->get();

                $s_data = [];
                $t_data = [];
                foreach ($stock_entry_detail as $row) {              
                    $bin_qry = DB::connection('mysql')->table('tabBin')->where('warehouse', $row->s_warehouse)
                        ->where('item_code', $row->item_code)->first();
                    
                    if ($bin_qry) {
                        $actual_qty = $bin_qry->actual_qty;
                        $valuation_rate = $bin_qry->valuation_rate;
                    }
                        
                    $s_data[] = [
                        'name' => 'mes' . uniqid(),
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
                        'is_cancelled' => 0,
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

                    $t_data[] = [
                        'name' => 'mes' . uniqid(),
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
                        'is_cancelled' => 0,
                        'qty_after_transaction' => $actual_qty,
                        '_user_tags' => null,
                        'batch_no' => $row->batch_no,
                        'stock_value_difference' => $row->qty * $row->valuation_rate,
                        'posting_date' => $now->format('Y-m-d'),
                    ];
                }

                $stock_ledger_entry = array_merge($s_data, $t_data);

                DB::connection('mysql')->table('tabStock Ledger Entry')->insert($stock_ledger_entry);
                
            }
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage(), 'id' => $stock_entry]);
        }
    }

    public function update_bin($stock_entry){
        try {
            $now = Carbon::now();

            $latest_id = DB::connection('mysql')->table('tabBin')->where('name', 'like', '%BINM%')->max('name');
            $latest_id = ($latest_id) ? $latest_id : 0;
            $latest_id_exploded = explode("/", $latest_id);
            $new_id = (array_key_exists(1, $latest_id_exploded)) ? $latest_id_exploded[1] + 1 : 1;

            $stock_entry_qry = DB::connection('mysql')->table('tabStock Entry')->where('name', $stock_entry)->first();
            if ($stock_entry_qry && $stock_entry_qry->docstatus == 1) {
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
                            $qty_after_transaction = $bin_qry->actual_qty - $row->transfer_qty;
                            if ($qty_after_transaction < 0) {
                                return ['status' => 0, 'message' => 'Insufficient stock for ' . $row->item_code . ' in ' . $row->s_warehouse];
                            }
    
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
                            $qty_after_transaction = $bin_qry->actual_qty + $row->transfer_qty;
                            if ($qty_after_transaction <= 0) {
                                return ['status' => 0, 'message' => 'Qty cannot be less than or equal to zero for ' . $row->item_code . ' in ' . $row->t_warehouse];
                            }
    
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
    
                return ['status' => 1, 'message' => 'Bin updated.'];
            }           
        } catch (Exception $e) {
            return ['status' => 0, 'message' => 'Error creating transaction. Please try again.'];
        }
    }
    
    public function create_gl_entry($stock_entry){
        try {
            $now = Carbon::now();
            $stock_entry_qry = DB::connection('mysql')->table('tabStock Entry')->where('name', $stock_entry)->first();
            if ($stock_entry_qry && $stock_entry_qry->docstatus == 1) {
                $credit_qry = DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $stock_entry)
                    ->select('s_warehouse', DB::raw('SUM(basic_amount) as basic_amount'), 'parent', 'cost_center', 'expense_account')
                    ->groupBy('s_warehouse', 'parent', 'cost_center', 'expense_account')
                    ->get();

                $debit_qry = DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $stock_entry)
                    ->select('t_warehouse', DB::raw('SUM(basic_amount) as basic_amount'), 'parent', 'cost_center', 'expense_account')
                    ->groupBy('t_warehouse', 'parent', 'cost_center', 'expense_account')
                    ->get();

                $id = [];
                $credit_data = [];
                $debit_data = [];

                foreach ($credit_qry as $row) {
                    $credit_data[] = [
                        'name' => 'MGL'. uniqid(),
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
                    $debit_data[] = [
                        'name' => 'MGL'. uniqid(),
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
            }
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

    // /production_planning_summary
    public function production_planning_summary(Request $request){
        if(!Auth::user()) {
            return response()->json(['message' => 'Session Expired. Please refresh the page and login to continue.']);
        }

        $production_orders = DB::connection('mysql_mes')->table('production_order')->whereIn('production_order', $request->production_orders)->whereNotIn('status', ['Cancelled', 'Closed'])->orderBy('production_order', 'asc')->get();

        $somr_arr = collect($production_orders)->map(function ($q){
            return $q->sales_order ? $q->sales_order : $q->material_request;
        });

        $production_item_codes = collect($production_orders)->pluck('item_code')->unique();
        $sub_parent_item_codes = collect($production_orders)->pluck('sub_parent_item_code')->unique();

        $parent_item_code = [];
        $sub_parent_bom_array = [];
        foreach($production_orders as $q){
            if($q->parent_item_code == $q->sub_parent_item_code){
                $parent_item_code[] = $q->parent_item_code;
                $sub_parent_bom_array[$q->item_code] = [$q->bom_no];
            }
        }

        $production_orders_arr = DB::connection('mysql_mes')->table('production_order')
            ->where(function ($q) use ($somr_arr){
                return $q->whereIn('sales_order', $somr_arr)->orWhereIn('material_request', $somr_arr);
            })->whereNotIn('status', ['Cancelled', 'Closed'])->whereNotIn('production_order', collect($request->production_orders)->filter()->values()->all())->get();

        $production_orders_arr = collect($production_orders_arr)->groupBy('item_code');

        // get bom of parent item codes
        $parent_bom = DB::connection('mysql')->table('tabBOM')->whereIn('item', $parent_item_code)->where('is_default', 1)->where('is_active', 1)->where('docstatus', 1)->select('item', 'name', 'quantity')->get();
        $parent_bom_array = collect($parent_bom)->pluck('name');

        // get bom of sub parent item codes
        $bom_array = collect($production_orders)->pluck('bom_no');
        $sub_parent_bom = DB::connection('mysql')->table('tabBOM Item')
            ->whereIn('item_code', $sub_parent_item_codes)->whereIn('parent', $parent_bom_array)
            ->orWhereIn('bom_no', $bom_array)->whereIn('parent', $sub_parent_bom_array)
            ->select('item_code', 'qty', 'parent', 'bom_no')->get();

        $production_item_bom = DB::connection('mysql')->table('tabBOM Item')
            ->whereIn('item_code', $production_item_codes)->whereIn('parent', collect($sub_parent_bom)->pluck('bom_no'))
            ->select('item_code', 'qty', 'parent', 'bom_no')->get();

        $bom_reference = collect($sub_parent_bom)->merge($production_item_bom)->groupBy('item_code');

        $sales_order = collect($production_orders)->pluck('sales_order')->unique();
        $sales_order_items = DB::connection('mysql')->table('tabSales Order Item')->whereIn('parent', $sales_order)->get();
        $sales_order_item = collect($sales_order_items)->groupBy('item_code');

        $planned_production_orders = DB::connection('mysql_mes')->table('production_order')->whereIn('sales_order', $sales_order)->whereNotIn('status', ['Cancelled', 'Closed'])->selectRaw('item_code, SUM(qty_to_manufacture) as qty')->groupBy('item_code')->get();
        $planned_production_orders_qty = collect($planned_production_orders)->groupBy('item_code');

        $production_order_list = [];
        foreach ($production_orders as $prod) {
            $conversion_qty = isset($bom_reference[$prod->item_code]) ? $bom_reference[$prod->item_code][0]->qty * 1 : 1;
            if($conversion_qty <= 0){
                $conversion_qry = DB::connection('mysql')->table('tabBOM as bom')
                    ->join('tabBOM Item as item', 'bom.name', 'item.parent')
                    ->where('bom.item', $prod->parent_item_code)->where('item.item_code', $prod->item_code)->where('item.bom_no', $prod->bom_no)->pluck('item.qty');
                $conversion_qty = $conversion_qry ? $conversion_qry->qty * 1 : 1;
            }

            $so_order_qty = isset($sales_order_item[$prod->parent_item_code]) ? $sales_order_item[$prod->parent_item_code][0]->qty * 1 : 0;

            $total_planned = isset($planned_production_orders_qty[$prod->item_code]) ? $planned_production_orders_qty[$prod->item_code][0]->qty : 0;

            $production_order_list[] = [
                'production_order' => $prod->production_order,
                'parent_code' => $prod->parent_item_code,
                'sub_parent_code' => $prod->sub_parent_item_code,
                'item_code' => $prod->item_code,
                'description' => $prod->description,
                'bom_no' => $prod->bom_no,
                'qty' => $prod->qty_to_manufacture,
                'unplanned_qty' => ($so_order_qty * $conversion_qty) - $total_planned,
                'planned_production_orders' => isset($production_orders_arr[$prod->item_code]) ? $production_orders_arr[$prod->item_code] : [],
                'stock_uom' => $prod->stock_uom,
                'planned_start_date' => $prod->planned_start_date,
                'is_scheduled' => $prod->is_scheduled
            ];
        }

        return view('wizard.tbl_planning_summary', compact('production_order_list'));
    }

    public function print_withdrawals(Request $request){
        if(!Auth::user()) {
            return response()->json(['success' => 0, 'message' => 'Session Expired. Please refresh the page and login to continue.']);
        }
        $myArray = explode(',', $request->production_orders);
        $now = Carbon::now();
        $ste = DB::connection('mysql')->table('tabStock Entry')
            ->where('purpose', 'Material Transfer for Manufacture')
            ->whereIn('work_order', $myArray)
            ->where('docstatus',"<", 2)
            ->selectRaw('work_order,sales_order_no,material_request,so_customer_name,project,GROUP_CONCAT(name ORDER BY work_order SEPARATOR ",") as ste_name')
            ->groupBy('work_order', 'sales_order_no','material_request','so_customer_name','project')
            ->get();  //get parent_ste based on given production order
        $stock_entries=[];
        foreach ($ste as $row) {
            $ste_name = explode(',', $row->ste_name);// merge into one page/ withdrawal slip all ste with same production order
            $items = DB::connection('mysql')->table('tabStock Entry Detail')->whereIn('parent', $ste_name)->get();
            $stock_entries[] = [
                'sales_order' => $row->sales_order_no,
                'material_request' => $row->material_request,
                'production_order' => $row->work_order,
                'customer' => $row->so_customer_name,
                'project' => $row->project,
                'posting_date' => $now->format('y-m-d'),
                'items' => $items
            ];

            DB::connection('mysql_mes')->table('production_order')->where('production_order', $row->work_order)->update(['withdrawal_slip_print' => '1']);

        }
        if(empty($stock_entries)){ //validation if with no ste found
            return response()->json(['success' => 0, 'message' => 'No withdrawal slip(s) created']);
        }
        return view('selected_print_withdrawal', compact('stock_entries'));
    }
    // NEW (FOR BOM CRUD)
    public function view_bom_list(){
        $permissions = $this->get_user_permitted_operation();

        return view('bom.index', compact('permissions'));
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
                ->where('work_order', $production_order)->where('purpose', 'Material Transfer for Manufacture')
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

                $update_bin_res = $this->update_bin($ste);
				if ($update_bin_res['status'] == 0) {
					return response()->json(['success' => 0, 'message' => $update_bin_res['message']]);
				}
                $this->create_stock_ledger_entry($ste);
                $this->create_gl_entry($ste);
            }

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
                    ->where('name', $row->name)->update(['transferred_qty' => $transferred_qty]);
            }

            $material_transferred_for_manufacturing = DB::connection('mysql')->table('tabWork Order')->where('name', $production_order)->sum('material_transferred_for_manufacturing');
            if ($material_transferred_for_manufacturing <= 0) {
                return response()->json(['success' => 0, 'message' => 'Error updating production order transferred qty. Please try again.']);
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

            if(!Auth::user()) {
                return response()->json(['success' => 0, 'message' => 'Session Expired. Please login to continue.']);
            }

			$existing_ste_transfer = DB::connection('mysql')->table('tabStock Entry')
				->where('work_order', $production_order)
				->where('purpose', 'Material Transfer for Manufacture')
				->where('docstatus', 1)->exists();

			if(!$existing_ste_transfer){
				return response()->json(['success' => 0, 'message' => 'Materials unavailable.']);
			}

			$production_order_details = DB::connection('mysql')->table('tabWork Order')
				->where('name', $production_order)->first();

			$produced_qty = $production_order_details->produced_qty + $fg_completed_qty;
			if($produced_qty >= (int)$production_order_details->qty && $production_order_details->material_transferred_for_manufacturing > 0){
				$pending_mtfm_count = DB::connection('mysql')->table('tabStock Entry as ste')
					->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
					->where('ste.work_order', $production_order)->where('purpose', 'Material Transfer for Manufacture')
					->where('ste.docstatus', 0)->count();
				
				if($pending_mtfm_count > 0){
					return response()->json(['success' => 0, 'message' => 'There are pending material request for issue.']);
				}
			}

			$mes_production_order_details = DB::connection('mysql_mes')->table('production_order')
				->where('production_order', $production_order)->first();

			$now = Carbon::now();

			$latest_pro = DB::connection('mysql')->table('tabStock Entry')->where('name', 'like', '%step%')->max('name');
			$latest_pro_exploded = explode("-", $latest_pro);
            $new_id = (($latest_pro) ? $latest_pro_exploded[1] : 0) + 1;
			$new_id = str_pad($new_id, 6, '0', STR_PAD_LEFT);
			$new_id = 'STEP-'.$new_id;

			$production_order_items = DB::connection('mysql')->table('tabWork Order Item')
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
				// 'customer_name' => null,
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
				// 'customer_address' => null,
				'total_outgoing_value' => collect($stock_entry_detail)->sum('basic_amount'),
				'supplier_name' => null,
				'remarks' => null,
				'_user_tags' => null,
				'total_additional_costs' => 0,
				// 'customer' => null,
				'bom_no' => $production_order_details->bom_no,
				'amended_from' => null,
				'total_amount' => collect($stock_entry_detail)->sum('basic_amount'),
				'total_incoming_value' => collect($stock_entry_detail)->sum('basic_amount'),
				'project' => $production_order_details->project,
				'_assign' => null,
				'select_print_heading' => null,
				'posting_date' => $now->format('Y-m-d'),
				'target_address_display' => null,
				'work_order' => $production_order,
				'purpose' => 'Material Transfer',
                'stock_entry_type' => 'Material Transfer',
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

            DB::connection('mysql')->table('tabWork Order')->where('name', $production_order)->update($production_data);

            $update_bin_res = $this->update_bin($new_id);
            if ($update_bin_res['status'] == 0) {
                return response()->json(['success' => 0, 'message' => $update_bin_res['message']]);
            }
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

        $s_warehouses = array_column($inventory_stock->toArray(), 'warehouse');

        $stock_reservation = DB::connection('mysql')->table('tabStock Reservation')->where('item_code', $item_code)
            ->whereIn('warehouse', $s_warehouses)->where('status', 'Active')
            ->selectRaw('SUM(reserve_qty) as total_reserved_qty, SUM(consumed_qty) as total_consumed_qty, CONCAT(item_code, "-", warehouse) as item')
            ->groupBy('item_code', 'warehouse')->get();
        $stock_reservation = collect($stock_reservation)->groupBy('item')->toArray();

        $ste_total_issued = DB::table('tabStock Entry Detail')->where('docstatus', 0)->where('status', 'Issued')
            ->where('item_code', $item_code)->whereIn('s_warehouse', $s_warehouses)
            ->selectRaw('SUM(qty) as total_issued, CONCAT(item_code, "-", s_warehouse) as item')
            ->groupBy('item_code', 's_warehouse')->get();
        $ste_total_issued = collect($ste_total_issued)->groupBy('item')->toArray();

        $at_total_issued = DB::table('tabAthena Transactions as at')
            ->join('tabPacking Slip as ps', 'ps.name', 'at.reference_parent')
            ->join('tabPacking Slip Item as psi', 'ps.name', 'psi.parent')
            ->join('tabDelivery Note as dr', 'ps.delivery_note', 'dr.name')
            ->whereIn('at.reference_type', ['Packing Slip', 'Picking Slip'])
            ->where('dr.docstatus', 0)->where('ps.docstatus', '<', 2)
            ->where('psi.status', 'Issued')->where('at.item_code', $item_code)
            ->where('psi.item_code', $item_code)->whereIn('at.source_warehouse', $s_warehouses)
            ->selectRaw('SUM(at.issued_qty) as total_issued, CONCAT(at.item_code, "-", at.source_warehouse) as item')
            ->groupBy('at.item_code', 'at.source_warehouse')
            ->get();

        $at_total_issued = collect($at_total_issued)->groupBy('item')->toArray();

        $stocks = [];
        foreach ($inventory_stock as $row) {
            $reserved_qty = 0;
            if (array_key_exists($item_code . '-' . $row->warehouse, $stock_reservation)) {
                $reserved_qty = $stock_reservation[$item_code . '-' . $row->warehouse][0]->total_reserved_qty;
            }
    
            $consumed_qty = 0;
            if (array_key_exists($item_code . '-' . $row->warehouse, $stock_reservation)) {
                $consumed_qty = $stock_reservation[$item_code . '-' . $row->warehouse][0]->total_consumed_qty;
            }
    
            $reserved_qty = $reserved_qty - $consumed_qty;
    
            $issued_qty = 0;
            if (array_key_exists($item_code . '-' . $row->warehouse, $ste_total_issued)) {
                $issued_qty = $ste_total_issued[$item_code . '-' . $row->warehouse][0]->total_issued;
            }
    
            if (array_key_exists($item_code . '-' . $row->warehouse, $at_total_issued)) {
                $issued_qty += $at_total_issued[$item_code . '-' . $row->warehouse][0]->total_issued;
            }
    
            $actual_qty = $row->actual_qty;
    
            $available_qty = $actual_qty - ($issued_qty + $reserved_qty);
            $available_qty = $available_qty < 0 ? 0 : $available_qty;

            $stocks[] = [
                'warehouse' => $row->warehouse,
                'available_qty' => $available_qty
            ];
        }

        return view('tables.tbl_item_inventory', compact('stocks'));
    }

    public function get_reason_for_cancellation(){
		  return DB::connection('mysql_mes')->table('reason_for_cancellation_po')->orderBy('reason_for_cancellation', 'asc')->get();
    }
    
    // /cancel_production_order_feedback/{stock_entry}
    public function cancel_production_order_feedback($stock_entry){
        DB::connection('mysql')->beginTransaction();
        try {
            if(!Auth::user()) {
                return response()->json(['status' => 0, 'message' => 'Session Expired. Please login to continue.']);
            }

            $now = Carbon::now();
            $stock_entry_detail = DB::connection('mysql')->table('tabStock Entry')
                ->where('name', $stock_entry)->where('docstatus', 1)->where('purpose', 'Manufacture')->first();
            // check if stock entry (manufacture) exists
            if(!$stock_entry_detail){
                return response()->json(['status' => 0, 'message' => 'Production Order Feedback not found. Ref. No: <b>' . $stock_entry . '</b>']);
            }
            // get production order details
            $production_order_detail = DB::connection('mysql')->table('tabWork Order')->where('name', $stock_entry_detail->work_order)->first();

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

                    $work_order_item = DB::connection('mysql')->table('tabWork Order Item')
                        ->where('parent', $stock_entry_detail->work_order)->where('item_code', $row->item_code)->first();
                    if ($work_order_item) {
                        DB::connection('mysql')->table('tabWork Order Item')
                            ->where('parent', $stock_entry_detail->work_order)->where('item_code', $row->item_code)->update(['consumed_qty' => ($work_order_item->consumed_qty - $row->transfer_qty)]);
                    }

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
            if ($remaining_feedbacked_qty <= 0) {
                $jtstatus = 'Pending';
            } else {
                $jtstatus = 'In Progress';
            }

            DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $stock_entry_detail->work_order)->where('remarks', 'Override')
                ->update([
                    'completed_qty' => $remaining_feedbacked_qty,
                    'remarks' => null,
                    'status' => $jtstatus,
                    'remarks' => $remaining_feedbacked_qty > 0 ? "Override" : null
                ]);

            $produced_qty = DB::connection('mysql_mes')->table('job_ticket')
                ->where('production_order', $stock_entry_detail->work_order)->min('completed_qty');

            // update production order produced qty and status in ERP
            DB::connection('mysql')->table('tabWork Order')
                ->where('name', $stock_entry_detail->work_order)->update(['produced_qty' => $remaining_feedbacked_qty, 'modified' => $now->toDateTimeString(),
                'modified_by' => Auth::user()->email, 'status' => 'In Process']);

            DB::connection('mysql_mes')->beginTransaction();
            // update production order feedbacked qty  in MES
            if($produced_qty > 0){
                $po_status = $remaining_feedbacked_qty > 0 ? 'Partially Feedbacked' : 'Ready for Feedback';
            }else{
                $po_status = 'Not Started';
            }

            DB::connection('mysql_mes')->table('production_order')->where('production_order', $stock_entry_detail->work_order)->update([
                'feedback_qty' => $remaining_feedbacked_qty,
                'produced_qty' => $produced_qty,
                'status' => $po_status,
                'last_modified_at' => $now->toDateTimeString(),
                'last_modified_by' => Auth::user()->email
            ]);
            
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

    public function wizardNoBom() {
        $permissions = $this->get_user_permitted_operation();

        return view('wizard_no_bom.index', compact('permissions'));
    }

    public function create_production_order_without_bom(Request $request){
        DB::connection('mysql_mes')->beginTransaction();
        try {
            $now = Carbon::now();
            if(!Auth::user()) {
                return response()->json(['success' => 0, 'message' => 'Session Expired. Please login to continue.']);
            }

            if($request->reference_type == 'Sales Order' && !$request->reference_no){
                return response()->json(['success' => 0, 'message' => 'Please enter reference Sales Order.']);
            }

            if($request->reference_type == 'Material Request' && !$request->reference_no){
                return response()->json(['success' => 0, 'message' => 'Please enter reference Material Request.']);
            }

            if ($request->qty <= 0) {
                return response()->json(['success' => 0, 'message' => 'Qty cannot be less than or equal to 0.']);
            }

            $reference_table = ($request->reference_type == 'Sales Order') ? 'tabSales Order' : 'tabMaterial Request';
            $reference_name = $request->reference_no;
            $reference_details = DB::connection('mysql')->table($reference_table)->where('name', $reference_name)->first();

            if(!$reference_details){
                return response()->json(['success' => 0, 'message' => $reference_name . ' does not exist.']);
            }

            $per_status = ($request->reference_type == 'Sales Order') ? $reference_details->per_delivered : $reference_details->per_ordered;
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

            if($item->is_stock_item == 0){
                return response()->json(['success' => 0, 'message' => 'Item ' .$request->item_code. ' is not a stock item.']);
            }

            $parent_item_code = $request->item_code;
            $sub_parent_item_code = $request->item_code;

            $operation_details = DB::connection('mysql_mes')->table('operation')
                ->where('operation_name', 'like', '%wiring%')->first();

            $operation_id = ($operation_details) ? $operation_details->operation_id : 3;
            
            $wip_wh = $this->get_operation_wip_warehouse($operation_id);
            if ($wip_wh['success'] < 1) {
                return response()->json(['success' => 0, 'message' => $wip_wh['message']]);
            }
            
            $wip = $wip_wh['message'];
      
            $latest_pro = DB::connection('mysql')->table('tabWork Order')->max('name');
            $latest_pro_exploded = explode("-", $latest_pro);
            $new_id = $latest_pro_exploded[1] + 1;
            $new_id = str_pad($new_id, 5, '0', STR_PAD_LEFT);
            $new_id = 'PROM-'.$new_id;
            
            $existing_id = DB::connection('mysql_mes')->table('production_order')
                ->where('production_order', $new_id)->first();
            if ($existing_id) {
                return response()->json(['success' => 0, 'message' => 'Production Order <b>' . $new_id . '</b> already exist.']);
            }

            $item_details = DB::connection('mysql')->table('tabItem')->where('name', $request->item_code)->first();

            $somr_qry = DB::connection('mysql')->table($reference_table.' Item')->where('parent', $request->reference_no)->where('item_code', $request->item_code)->where('docstatus', '<', 2)->select('qty', 'stock_uom')->first();
            $qty_to_manufacture = $somr_qry ? $somr_qry->qty : 0;
            $somr_uom = $somr_qry ? $somr_qry->stock_uom : null;

            $ref_col = $request->reference_type == 'Sales Order' ? 'sales_order' : 'material_request';
            $mes_total = DB::connection('mysql_mes')->table('production_order')
                ->where($ref_col, $request->reference_no)->where('item_code', $request->item_code)->whereNotIn('status', ['Cancelled', 'Closed'])
                ->selectRaw($ref_col.', item_code, sum(qty_to_manufacture) as qty_to_manufacture')
                ->groupBy($ref_col, 'item_code')->first();

            $requested_qty_to_manufacture = $request->qty_to_manufacture + ($mes_total ? $mes_total->qty_to_manufacture : 0);
            if($requested_qty_to_manufacture > $qty_to_manufacture){
                return response()->json(['success' => 0, 'message' => 'Qty to manufacture cannot exceed '.(float)$qty_to_manufacture.' '.$somr_uom.'.']);
            }

            $classification = ($request->reference_type == 'Sales Order') ? (($reference_details->sales_type == 'Sample') ? 'Sample' : 'Customer Order') : $reference_details->purpose;

            $data = [
                'name' => $new_id,
                'creation' => $now->toDateTimeString(),
                'modified' => $now->toDateTimeString(),
                'modified_by' => Auth::user()->email,
                'owner' => Auth::user()->email,
                'docstatus' => 1,
                'idx' => 0,
                'qty' => $request->qty_to_manufacture,
                'fg_warehouse' => $request->target,
                'use_multi_level_bom' => 0,
                'material_transferred_for_manufacturing' => 0,
                'stock_uom' => $item_details->stock_uom,
                'naming_series' => 'PRO-',
                'status' => 'Not Started',
                'description' => $request->description,
                'company' => 'FUMACO Inc.',
                'production_item' => strtoupper($request->item_code),
                'sales_order_item' => ($request->reference_type == 'Sales Order') ? $request->item_reference_id : null,
                'bom_no' => null,
                'wip_warehouse' => $wip,
                'project' => $reference_details->project,
                'scrap_warehouse' => 'Scrap Warehouse P1 - FI',
                'item_classification' => $item_details->item_classification,
                'delivery_date' => ($request->reference_type == 'Sales Order') ? $reference_details->delivery_date : $reference_details->schedule_date,
                'item_name' => $item_details->item_name,
                'customer' => $reference_details->customer,
                'sales_order_no' => ($request->reference_type == 'Sales Order') ? $reference_details->name : null,
                'sales_order' => ($request->reference_type == 'Sales Order') ? $reference_details->name : null,
                'material_request' => ($request->reference_type == 'Material Request') ? $reference_details->name : null,
                'scheduled' => ($request->planned_date) ? 1 : 0,
                'order_no' => 0,
                'priority' => 'Normal',
                'classification' => $classification,
                'parent_item_code' => strtoupper($parent_item_code),
                'planned_start_date' => ($request->planned_date) ? $request->planned_date : null,
            ];

            $params = DB::connection('mysql')->table('tabItem Variant Attribute')->where('parent', $request->item_code)
                ->where('attribute', 'LIKE', '%cutting size%')->first();

            $data_mes = [
                'production_order' => $new_id,
                'parent_item_code' => strtoupper($parent_item_code),
                'sub_parent_item_code' => strtoupper($sub_parent_item_code),
                'item_code' => strtoupper($request->item_code),
                'description' => $request->description,
                'parts_category' => $item_details->parts_category,
                'item_classification' => $item_details->item_classification,
                'qty_to_manufacture' => $request->qty_to_manufacture,
                'classification' => $classification,
                'order_no' => 0,
                'cutting_size' => ($params) ? $params->attribute_value : null,
                'is_scheduled' => ($request->planned_date) ? 1 : 0,
                'planned_start_date' => ($request->planned_date) ? $request->planned_date : null,
                'project' => $reference_details->project,
                'bom_no' => null,
                'sales_order' => ($request->reference_type == 'Sales Order') ? $reference_details->name : null,
                'material_request' => ($request->reference_type == 'Material Request') ? $reference_details->name : null,
                'delivery_date' => ($request->reference_type == 'Sales Order') ? $reference_details->delivery_date : $reference_details->schedule_date,
                'status' => 'Not Started',
                'stock_uom' => $item_details->stock_uom,
                'customer' => $reference_details->customer,
                'wip_warehouse' => $wip,
                'fg_warehouse' => $request->target,
                'last_modified_at' => $now->toDateTimeString(),
                'last_modified_by' => Auth::user()->email,
                'created_by' => Auth::user()->email,
                'created_at' => $now->toDateTimeString(),
                'operation_id' => $operation_id,
                'is_stock_item' => $item_details->is_stock_item
            ];

            DB::connection('mysql_mes')->table('production_order')->insert($data_mes);

            $default_workstations = [25, 28, 27];
            $mes_custom_operations = [];
            $custom_operations = [];
            foreach($default_workstations as $p => $w_id){
                $workstation_details = DB::connection('mysql_mes')->table('workstation')->where('workstation_id', $w_id)->first();
                $bom_operation_id = 'nbop' . uniqid();
                $mes_custom_operations[] = [
                    'production_order' => $new_id,
                    'idx' => $p + 1,
                    'workstation' => $workstation_details->workstation_name,
                    'process_id' => null,
                    'planned_start_date' => ($request->planned_date) ? $request->planned_date : null,
                    'created_by' => Auth::user()->employee_name,
                    'created_at' => $now->toDateTimeString(),
                    'last_modified_by' => Auth::user()->employee_name,
                    'last_modified_at' => $now->toDateTimeString(),
                    'bom_operation_id' => $bom_operation_id
                ];

                $custom_operations[] = [
                    'name' => 'mes'.uniqid(),
                    'creation' => $now->toDateTimeString(),
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->email,
                    'owner' => Auth::user()->email,
                    'docstatus' => 1,
                    'parent' => $new_id,
                    'parentfield' => 'operations',
                    'parenttype' => 'Work Order',
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
                    'process' => null,
                    'bom_operation_id' => $bom_operation_id
                ];
            }

            DB::connection('mysql_mes')->table('job_ticket')->insert($mes_custom_operations);
            DB::connection('mysql')->table('tabWork Order Operation')->insert($custom_operations);

            $reference_child_table = ($request->reference_type == 'Sales Order') ? 'tabSales Order Item' : 'tabMaterial Request Item';
            $reference_parent = $reference_details->name;
            $reference_child_details = DB::connection('mysql')->table($reference_child_table)
                ->where('name', $request->item_reference_id)->first();

            if($reference_child_details){
                $del_data = [
                    'erp_reference_id' => $reference_child_details->name,
                    'reference_no' => $reference_parent,
                    'parent_item_code' => $parent_item_code,
                    'delivery_date' => ($request->reference_type == 'Sales Order') ? $reference_child_details->delivery_date : $reference_child_details->schedule_date,
                    'created_by' => Auth::user()->email
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
                DB::connection('mysql')->table('tabWork Order')->insert($data);
                $raw_required_items = [];
                if($item_details->is_stock_item > 0){
                    $req_item_detail = DB::connection('mysql')->table('tabItem')
                        ->where('name', $request->item_code)->first();

                    $item_default_warehouse = DB::connection('mysql')->table('tabItem Default')->where('parent', $req_item_detail->name)
                        ->where('company', 'FUMACO Inc.')->first();
                    $item_default_warehouse = ($item_default_warehouse) ? $item_default_warehouse->default_warehouse : null;

                    $raw_required_items = [
                        'name' => 'mes'.uniqid(),
                        'creation' => $now->toDateTimeString(),
                        'modified' => $now->toDateTimeString(),
                        'modified_by' => Auth::user()->email,
                        'owner' => Auth::user()->email,
                        'docstatus' => 1,
                        'parent' => $new_id,
                        'parentfield' => 'required_items',
                        'parenttype' => 'Work Order',
                        'idx' => 1,
                        'description' => $req_item_detail->description,
                        'item_name' => $req_item_detail->item_name,
                        'item_code' => $req_item_detail->item_code,
                        'required_qty' => $request->qty_to_manufacture,
                        'transferred_qty' => 0,
                        'available_qty_at_source_warehouse' => 0,
                        'available_qty_at_wip_warehouse' => 0,
                        'source_warehouse' => $item_default_warehouse,
                        'stock_uom' => $req_item_detail->stock_uom
                    ];
                }else{
                    $bundle_items = DB::connection('mysql')->table('tabProduct Bundle Item')->where('parent', $request->item_code)->get();
                    foreach ($bundle_items as $k => $v) {
                        $req_item_detail = DB::connection('mysql')->table('tabItem')
                            ->where('name', $v->item_code)->first();

                        $item_default_warehouse = DB::connection('mysql')->table('tabItem Default')->where('parent', $req_item_detail->name)
                            ->where('company', 'FUMACO Inc.')->first();
                        $item_default_warehouse = ($item_default_warehouse) ? $item_default_warehouse->default_warehouse : null;

                        $raw_required_items[] = [
                            'name' => 'mes'.uniqid(),
                            'creation' => $now->toDateTimeString(),
                            'modified' => $now->toDateTimeString(),
                            'modified_by' => Auth::user()->email,
                            'owner' => Auth::user()->email,
                            'docstatus' => 1,
                            'parent' => $new_id,
                            'parentfield' => 'required_items',
                            'parenttype' => 'Work Order',
                            'idx' => $k + 1,
                            'description' => $req_item_detail->description,
                            'item_name' => $req_item_detail->item_name,
                            'item_code' => $req_item_detail->item_code,
                            'required_qty' => $v->qty * $request->qty_to_manufacture,
                            'transferred_qty' => 0,
                            'available_qty_at_source_warehouse' => 0,
                            'available_qty_at_wip_warehouse' => 0,
                            'source_warehouse' => $item_default_warehouse,
                            'stock_uom' => $v->uom
                        ];
                    }
                }

                DB::connection('mysql')->table('tabWork Order Item')->insert($raw_required_items);

                $this->insert_job_card($new_id);
                
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

    public function viewAddOperationsWizard(Request $request) {
        $production_orders = DB::connection('mysql')->table('tabWork Order as wo')->join('tabItem as i', 'wo.production_item', 'i.name')
            ->whereIn('wo.name', $request->production_orders)->select('wo.name', 'wo.production_item', 'wo.item_name', 'i.item_classification', 'wo.description', 'i.item_image_path')->get();

        return view('wizard_no_bom.add_operations', compact('production_orders'));
    }

    // /submit_withdrawal_slip
    public function submit_withdrawal_slip(Request $request){
        DB::beginTransaction();
        
        try {
            $steDetails = DB::connection('mysql')->table('tabStock Entry as se')->join('tabStock Entry Detail as sed', 'se.name', 'sed.parent')->where('sed.name', $request->child_tbl_id)
                ->select('se.name as parent_se', 'se.*', 'sed.*', 'sed.status as per_item_status', 'se.docstatus as se_status')->first();
            if(!$steDetails){
                return response()->json(['status' => 0, 'message' => 'Record not found.']);
            }

            if(in_array($steDetails->per_item_status, ['Issued', 'Returned'])){
                return response()->json(['status' => 0, 'message' => 'Item already ' . $steDetails->per_item_status . '.']);
            }

            if($steDetails->se_status == 1){
                return response()->json(['status' => 0, 'message' => 'Item already issued.']);
            }

            $itemDetails = DB::table('tabItem')->where('name', $steDetails->item_code)->first();
            if(!$itemDetails){
                return response()->json(['status' => 0, 'message' => 'Item  <b>' . $steDetails->item_code . '</b> not found.']);
            }     
 
            if($itemDetails->is_stock_item == 0){
                return response()->json(['status' => 0, 'message' => 'Item  <b>' . $steDetails->item_code . '</b> is not a stock item.']);
            }

            if($steDetails->qty <= 0){
                return response()->json(['status' => 0, 'message' => 'Qty cannot be less than or equal to 0.']);
            }

            $available_qty = $this->get_available_qty($steDetails->item_code, $steDetails->s_warehouse);
            if($steDetails->purpose != 'Material Receipt'){
                if($steDetails->qty > $available_qty){
                    return response()->json(['status' => 0, 'message' => 'Qty not available for <b> ' . $steDetails->item_code . '</b> in <b>' . $steDetails->s_warehouse . '</b><
                    br><br>Available qty is <b>' . $available_qty . '</b>, you need <b>' . $steDetails->qty . '</b>.']);
                }
            }

            $status = $steDetails->status;
            if($steDetails->purpose == 'Material Receipt' && $steDetails->receive_as == 'Sales Return') {
                $status = 'Returned';
            }else {
                $status = 'Issued';
            }

            $values = [
                'session_user' => Auth::user()->employee_name,
                'status' => $status, 
                'transfer_qty' => $steDetails->qty, 
                'qty' => $steDetails->qty, 
                'issued_qty' => $steDetails->qty, 
                'validate_item_code' => $steDetails->item_code, 
                'date_modified' => Carbon::now()->toDateTimeString(),
                'remarks' => 'Fast Issued'
            ];
                                                  
            DB::connection('mysql')->table('tabStock Entry Detail')->where('name', $request->child_tbl_id)->update($values);
            
            $this->insert_transaction_log('Stock Entry', $request->child_tbl_id);

            $status_result = $this->update_pending_ste_item_status();

            if ($steDetails->purpose == 'Material Transfer for Manufacture') {
                $cancelled_production_order = DB::table('tabWork Order')
                    ->where('name', $steDetails->work_order)->where('docstatus', 2)->first();

                if($cancelled_production_order){
                    return response()->json(['status' => 0, 'message' => 'Production Order ' . $cancelled_production_order->name . ' was cancelled. Please reload the page.']);
                }

                $this->submit_stock_entry($steDetails->parent_se);
            }

            // get item code transferred_qty
            $transferred_qty = DB::connection('mysql')->table('tabStock Entry as ste')
                ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                ->where('ste.work_order', $steDetails->work_order)->where('ste.purpose', 'Material Transfer for Manufacture')
                ->where('ste.docstatus', 1)->where('sted.item_code', $steDetails->item_code)->sum('sted.qty');

            if ($transferred_qty <= 0) {
                return response()->json(['status' => 0, 'message' => 'Unable to issue items. Please try again.']);
            }

            $this->update_production_order_items($steDetails->work_order);

            DB::commit();
            return response()->json(['status' => 1, 'message' => 'Item <b>' . $steDetails->item_code . '</b> has been issued.']);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['status' => 0, 'message' => 'Error creating transaction. Please contact your system administrator.']);
        }
    }

    public function get_available_qty($item_code, $warehouse){
        $reserved_qty = $this->get_reserved_qty($item_code, $warehouse);
        $actual_qty = $this->get_actual_qty($item_code, $warehouse);
        $issued_qty = $this->get_issued_qty($item_code, $warehouse);

        $available_qty = ($actual_qty - $issued_qty);
        $available_qty = ($available_qty - $reserved_qty);

        return ($available_qty < 0) ? 0 : $available_qty;
    }

    public function get_reserved_qty($item_code, $warehouse){
        $reserved_qty_for_website = 0;

        $stock_reservation_qty = DB::connection('mysql')->table('tabStock Reservation')->where('item_code', $item_code)
            ->where('warehouse', $warehouse)->whereIn('type', ['In-house', 'Consignment', 'Website Stocks'])->whereIn('status', ['Active', 'Partially Issued'])->sum('reserve_qty');

        $consumed_qty = DB::connection('mysql')->table('tabStock Reservation')->where('item_code', $item_code)
            ->where('warehouse', $warehouse)->whereIn('type', ['In-house', 'Consignment', 'Website Stocks'])->whereIn('status', ['Active', 'Partially Issued'])->sum('consumed_qty');

        return ($reserved_qty_for_website + $stock_reservation_qty) + $consumed_qty;
    }

    public function get_issued_qty($item_code, $warehouse){
        $total_issued = DB::connection('mysql')->table('tabStock Entry Detail')->where('docstatus', 0)->where('status', 'Issued')
            ->where('item_code', $item_code)->where('s_warehouse', $warehouse)->sum('qty');

        $total_issued += DB::connection('mysql')->table('tabAthena Transactions as at')
            ->join('tabPacking Slip as ps', 'ps.name', 'at.reference_parent')
            ->join('tabPacking Slip Item as psi', 'ps.name', 'ps.parent')
            ->join('tabDelivery Note as dr', 'ps.delivery_note', 'dr.name')
            ->whereIn('at.reference_type', ['Packing Slip', 'Picking Slip'])
            ->where('dr.docstatus', 0)->where('ps.docstatus', '<', 2)
            ->where('psi.status', 'Issued')
            ->where('at.item_code', $item_code)->where('at.source_warehouse', $warehouse)
            ->sum('at.issued_qty');

        return $total_issued;
    }

    public function insert_transaction_log($transaction_type, $id){
        if($transaction_type == 'Picking Slip'){
            $q = DB::connection('mysql')->table('tabPacking Slip as ps')
                ->join('tabPacking Slip Item as psi', 'ps.name', 'psi.parent')
                ->join('tabDelivery Note Item as dri', 'dri.parent', 'ps.delivery_note')
                ->join('tabDelivery Note as dr', 'dri.parent', 'dr.name')
                ->whereRaw(('dri.item_code = psi.item_code'))->where('ps.item_status', 'For Checking')->where('dri.docstatus', 0)->where('psi.name', $id)
                ->select('psi.name', 'psi.parent', 'psi.item_code', 'psi.description', 'ps.delivery_note', 'dri.warehouse', 'psi.qty', 'psi.barcode', 'psi.session_user', 'psi.stock_uom')
                ->first();
            $type = 'Check Out - Delivered';
            $purpose = 'Picking Slip';
            $barcode = $q->barcode;
            $remarks = null;
            $s_warehouse = $q->warehouse;
            $t_warehouse = null;
            $reference_no = $q->delivery_note;
        } else if($transaction_type == 'Delivery Note') {
            $q = DB::connection('mysql')->table('tabDelivery Note as dn')
                ->join('tabDelivery Note Item as dni', 'dn.name', 'dni.parent')
                ->where('dni.name', $id)->select('dni.name', 'dni.parent', 'dni.item_code', 'dni.description', 'dn.name as delivery_note', 'dni.warehouse', 'dni.qty', 'dni.barcode', 'dni.session_user', 'dni.stock_uom')
                ->first();

            $type = 'Check In - Received';
            $purpose = 'Sales Return';
            $barcode = $q->barcode;
            $remarks = null;
            $s_warehouse = null;
            $t_warehouse = $q->warehouse;
            $reference_no = $q->delivery_note;
        } else {
            $q = DB::connection('mysql')->table('tabStock Entry as ste')
                ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')->where('sted.name', $id)
                ->select('sted.*', 'ste.sales_order_no', 'ste.material_request', 'ste.purpose', 'ste.transfer_as', 'ste.issue_as', 'ste.receive_as')
                ->first();

            $type = null;
            if($q->purpose == 'Manufacture') {
                $type = 'Check In - Received';
            }

            if($q->purpose == 'Material Transfer for Manufacture') {
                $type = 'Check Out - Issued';
            }

            if($q->purpose == 'Material Transfer' && $q->transfer_as == 'Internal Transfer') {
                $type = 'Check Out - Transferred';
            }

            if($q->purpose == 'Material Transfer' && in_array($q->transfer_as, ['Consignment', 'Sample Item'])) {
                $type = 'Check Out - Delivered';
            }

            if($q->purpose == 'Material Transfer' && $q->transfer_as == 'For Return') {
                $type = 'Check In - Returned';
            }

            if($q->purpose == 'Material Issue' && $q->issue_as == 'Customer Replacement') {
                $type = 'Check Out - Replaced';
            }

            if($q->purpose == 'Material Issue' && $q->issue_as != 'Customer Replacement') {
                $type = 'Check Out - Issued';
            }

            if($q->purpose == 'Material Receipt' && $q->receive_as == 'Sales Return') {
                $type = 'Check In - Received';
            }

            $purpose = $q->purpose;
            $barcode = $q->validate_item_code;
            $remarks = $q->remarks;
            $s_warehouse = $q->s_warehouse;
            $t_warehouse = $q->t_warehouse;
            $reference_no = ($q->sales_order_no) ? $q->sales_order_no : $q->material_request;
        }
       
        $now = Carbon::now();
        
        $values = [
            'name' => uniqid(date('mdY')),
            'reference_type' => $transaction_type,
            'reference_name' => $q->name,
            'reference_parent' => $q->parent,
            'item_code' => $q->item_code,
            'qty' => $q->qty,
            'barcode' => $barcode,
            'transaction_date' => $now->toDateTimeString(),
            'warehouse_user' => $q->session_user,
            'issued_qty' => $q->qty,
            'remarks' => $remarks,
            'source_warehouse' => $s_warehouse,
            'target_warehouse' => $t_warehouse,
            'description' => $q->description,
            'reference_no' => $reference_no,
            'creation' => $now->toDateTimeString(),
            'modified' => $now->toDateTimeString(),
            'modified_by' => Auth::user()->wh_user,
            'owner' => Auth::user()->wh_user,
            'uom' => $q->stock_uom,
            'purpose' => $purpose,
            'transaction_type' => $type
        ];

        $existing_log = DB::connection('mysql')->table('tabAthena Transactions')
            ->where('reference_name', $q->name)->where('reference_parent', $q->parent)
            ->exists();

        if(!$existing_log){
            DB::connection('mysql')->table('tabAthena Transactions')->insert($values);
        }
    }

    public function update_pending_ste_item_status(){
        DB::beginTransaction();
        try {
            $for_checking_ste = DB::connection('mysql')->table('tabStock Entry')
                ->where('item_status', 'For Checking')->where('docstatus', 0)
                ->select('name', 'transfer_as', 'receive_as')->get();

            $item_status = null;
            foreach($for_checking_ste as $ste){
                $items_for_checking = DB::connection('mysql')->table('tabStock Entry Detail')
                    ->where('parent', $ste->name)->where('status', 'For Checking')->exists();

                if(!$items_for_checking){
                    if($ste->receive_as == 'Sales Return'){
                        DB::connection('mysql')->table('tabStock Entry')->where('name', $ste->name)->where('docstatus', 0)->update(['item_status' => 'Returned']);
                    }else{
                        $item_status = ($ste->transfer_as == 'For Return') ? 'Returned' : 'Issued';
                        DB::connection('mysql')->table('tabStock Entry')->where('name', $ste->name)->where('docstatus', 0)->update(['item_status' => $item_status]);
                    }
                }
            }

            DB::commit();

            return $item_status;
        } catch (Exception $e) {
            DB::rollback();
        }
    }

    public function submit_stock_entry($id){
        try {
            $now = Carbon::now();
            $draft_ste = DB::connection('mysql')->table('tabStock Entry')->where('name', $id)->where('docstatus', 0)->first();
            if($draft_ste){
                if ($draft_ste->purpose != 'Manufacture') {
                     // check if all items are issued
                    $count_not_issued_items = DB::connection('mysql')->table('tabStock Entry Detail')->whereNotIn('status', ['Issued', 'Returned'])->where('parent', $draft_ste->name)->count();
                    if($count_not_issued_items > 0){
                        return response()->json(['success' => 0, 'message' => 'All item(s) must be issued.']);
                    }
                }

                if($draft_ste->purpose == 'Material Transfer for Manufacture'){
                    $production_order_details = DB::connection('mysql')->table('tabWork Order')->where('name', $draft_ste->work_order)->first();

                    // get total "for quantity" (submitted)
                    $transferred_qty = DB::connection('mysql')->table('tabStock Entry')
                        ->where('work_order', $draft_ste->work_order)->where('docstatus', 1)
                        ->where('purpose', 'Material Transfer for Manufacture')->sum('fg_completed_qty');
                    
                    $total_transferred_qty = $transferred_qty + $draft_ste->fg_completed_qty;
                    if ($total_transferred_qty > $production_order_details->qty) {
                        $fg_completed_qty = $production_order_details->qty - $transferred_qty;
                    }else{
                        $fg_completed_qty = $draft_ste->fg_completed_qty;
                    }

                    $material_transferred_for_manufacturing = $transferred_qty + $fg_completed_qty;

                    DB::connection('mysql')->table('tabWork Order')->where('name', $draft_ste->work_order)
                        ->update(['status' => 'In Process', 'material_transferred_for_manufacturing' => $material_transferred_for_manufacturing]);
                
                    $values = [
                        'modified' => $now->toDateTimeString(),
                        'modified_by' => Auth::user()->wh_user,
                        'docstatus' => 1,
                        'fg_completed_qty' => $fg_completed_qty
                    ];
                }else{
                    $values = [
                        'modified' => $now->toDateTimeString(),
                        'modified_by' => Auth::user()->wh_user,
                        'docstatus' => 1
                    ];
                }
               
                DB::connection('mysql')->table('tabStock Entry')->where('name', $id)->update($values);
                DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $id)->update([
                    'modified' => $now->toDateTimeString(),
                    'modified_by' => Auth::user()->wh_user,
                    'docstatus' => 1
                ]);

                if($draft_ste->purpose == 'Material Transfer for Manufacture'){
                    $this->update_production_order_items($production_order_details->name);

                    if($production_order_details->status == 'Not Started'){
                        $values = [
                            'status' => 'In Process',
                            'material_transferred_for_manufacturing' => $production_order_details->qty
                        ];
                    }else{
                        $values = [
                            'material_transferred_for_manufacturing' => $production_order_details->qty
                        ];
                    }
    
                    DB::connection('mysql')->table('tabWork Order')
                        ->where('name', $production_order_details->name)
                        ->update($values);
                }

                $this->update_bin($id);
                $this->create_stock_ledger_entry($id);
                $this->create_gl_entry($id);
            }
        } catch (Exception $e) {
            
        }
    }

    public function update_production_order_items($production_order){
        $production_order_items = DB::connection('mysql')->table('tabWork Order Item')->where('parent', $production_order)->get();
        foreach ($production_order_items as $row) {
            $transferred_qty = DB::connection('mysql')->table('tabStock Entry as ste')
                ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                ->where('ste.work_order', $production_order)->where('ste.purpose', 'Material Transfer for Manufacture')
                ->where('ste.docstatus', 1)->where('item_code', $row->item_code)->sum('qty');
            
                
                DB::connection('mysql')->table('tabWork Order Item')
                    ->where('parent', $production_order)
                    ->where('item_code', $row->item_code)->update(['transferred_qty' => $transferred_qty]);
        }
    }

    public function sync_production_order_items($production_order) {
        DB::connection('mysql')->beginTransaction();
        try {
            $production_order_items = DB::connection('mysql')->table('tabWork Order Item')
                ->where('parent', $production_order)->select('name', 'item_code', 'transferred_qty', 'parent')->get();

            foreach ($production_order_items as $row) {
                // get item code transferred_qty
                $transferred_qty = DB::connection('mysql')->table('tabStock Entry as ste')
                    ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                    ->where('ste.work_order', $row->parent)->where('ste.purpose', 'Material Transfer for Manufacture')
                    ->where('ste.docstatus', 1)->where('sted.item_code', $row->item_code)->sum('sted.qty');

                DB::connection('mysql')->table('tabWork Order Item')
                    ->where('name', $row->name)->update(['transferred_qty' => $transferred_qty]);
            }

            DB::connection('mysql')->commit();
            
            return response()->json(['status' => 1, 'message' => 'Production Order Item for <b>' . $production_order . '</b> has been updated.']);
        } catch (Exception $e) {
            DB::connection('mysql')->rollback();

            return response()->json(['status' => 0, 'message' => 'An error occured. Please contact your system administrator.']);
        }
    }
}
