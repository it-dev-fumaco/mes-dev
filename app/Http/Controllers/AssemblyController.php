<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Auth;
use DB;
use Exception;

use App\Traits\GeneralTrait;

class AssemblyController extends Controller
{
    use GeneralTrait;

    public function wizard(){
        $permissions = $this->get_user_permitted_operation();

        $mes_user_operations = DB::connection('mysql_mes')->table('user')
					->join('operation', 'operation.operation_id', 'user.operation_id')
					->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
                    ->where('module', 'Production')
                    ->where('user_access_id', Auth::user()->user_id)->pluck('operation_name')->toArray();

        return view('assembly_wizard.index', compact('permissions', 'mes_user_operations'));
    }

    public function get_reference_details($reference_type, $id, Request $request){
        try {
            if(!Auth::user()) {
                return response()->json(['message' => 'Session Expired. Please refresh the page and login to continue.']);
            }

            $reference_details = DB::connection('mysql')->table('tab' . $reference_type)->where('name', $id)->where('docstatus', 1)->first();
            if (!$reference_details) {
                return response()->json(['message' => $reference_type .' <b>' . $id . '</b> not found.']);
            }

            if ($reference_type == 'Sales Order') {
                $items = DB::connection('mysql')->table('tabSales Order Item')->where('parent', $id)
                    ->when($request->item, function ($query) use ($request){
                        return $query->whereIn('item_code', $request->item);
                    })
                    ->select(DB::raw('parent, (qty - delivered_qty) as pending_qty'), 'item_code', 'warehouse', 'description', 'uom', 'qty', 'idx', 'item_classification', 'delivered_qty', 'name', 'delivery_date')
                    ->orderBy('idx', 'asc')->get();
            }else{
                $items = DB::connection('mysql')->table('tabMaterial Request Item')->where('parent', $id)
                    ->when($request->item, function ($query) use ($request){
                        return $query->whereIn('item_code', $request->item);
                    })
                   ->select(DB::raw('parent, (qty - ordered_qty) as pending_qty'), 'item_code', 'warehouse', 'description', 'uom', 'qty', 'idx', 'ordered_qty', 'name', 'item_classification', 'schedule_date as delivery_date')
                   ->orderBy('idx', 'asc')->get();
            }

            $requested_bom = $request->bom ? $request->bom : [];

            $existing_production_orders = [];
            if($request->no_bom) {
                $reference_order_items = array_column($items->toArray(), 'item_code');
                $reference_sales_order = ($reference_type == 'Sales Order') ? $reference_details->name : null;
                $reference_material_request = ($reference_type == 'Material Request') ? $reference_details->name : null;
                $existing_production_orders = DB::connection('mysql')->table('tabWork Order')->whereIn('production_item', $reference_order_items)
                    ->where('sales_order', $reference_sales_order)->where('material_request', $reference_material_request)
                    ->where('docstatus', 1)->select('name', 'production_item', 'qty', 'planned_start_date', 'fg_warehouse', 'status')->orderBy('creation', 'desc')->get();

                $existing_production_orders = collect($existing_production_orders)->groupBy('production_item')->toArray();
            }

            $item_list = [];
            foreach ($items as $item) {
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

                $production_orders = [];
                $production_order = $planned_start_date = $fg_warehouse = null;
                if($request->no_bom){
                    if(isset($existing_production_orders[$item->item_code])){
                        foreach ($existing_production_orders[$item->item_code] as $d) {
                            if(!in_array($d->status, ['Cancelled', 'Closed'])){
                                $production_orders[$d->name] = [
                                    'qty' => $d->qty,
                                    'status' => $d->status
                                ];
                            }
                        }
                    }
                }else{
                    $production_order = array_key_exists($item->item_code, $existing_production_orders) ? $existing_production_orders[$item->item_code][0]->name : null;
                    $planned_start_date = array_key_exists($item->item_code, $existing_production_orders) ? $existing_production_orders[$item->item_code][0]->planned_start_date : null;
                    $fg_warehouse = array_key_exists($item->item_code, $existing_production_orders) ? $existing_production_orders[$item->item_code][0]->fg_warehouse : null;
                    $requested_bom = array_key_exists($item->item_code, $requested_bom) ? $requested_bom[$item->item_code] : [];
                }

                $item_list[] = [
                    'id' => $item->name,
                    'idx' => $item->idx,
                    'reference' => $item->parent,
                    'item_code' => $item->item_code,
                    'description' => $item->description,
                    'delivered_qty' => ($reference_type == 'Sales Order') ? $item->delivered_qty : $item->ordered_qty,
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
                    'new_code' => $new_code,
                    'production_order' => $request->no_bom ? $production_orders : $production_order,
                    'planned_start_date' => $planned_start_date,
                    'fg_warehouse' => $fg_warehouse,
                    'requested_bom' => $requested_bom
                ];
            }

            $reference_details = [
                'name' => $reference_details->name,
                'customer' => $reference_details->customer,
                'transaction_date' => $reference_details->transaction_date,
                'status' => $reference_details->status,
                'sales_type' => ($reference_type == 'Sales Order') ? $reference_details->sales_type : null,
                'delivery_date' => $reference_details->delivery_date,
                'project' => $reference_details->project,
                'notes' => ($reference_type == 'Sales Order') ? $reference_details->notes : $reference_details->notes00
            ];

            $item_classifications = array_unique(array_column($item_list, 'item_classification'));

            $view = ($request->no_bom) ? 'wizard_no_bom.tbl_reference_details' : 'assembly_wizard.tbl_reference_details';

            $item_warehouses = DB::connection('mysql_mes')->table('item_classification_warehouse')
                ->whereIn('item_classification', $item_classifications)->distinct()->pluck('warehouse');

            $item_warehouses = array_values($item_warehouses->toArray());
            if (!in_array('Finished Goods - FI', $item_warehouses)) {
                array_push($item_warehouses, 'Finished Goods - FI');
            }

            if (!in_array('Consignment Warehouse - FI', $item_warehouses)) {
                array_push($item_warehouses, 'Consignment Warehouse - FI');
            }

            return view($view, compact('reference_details', 'item_list', 'item_warehouses'));
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function get_warehouses($operation, $item_classification){
        $operation_details = DB::connection('mysql_mes')->table('operation')->where('operation_name', $operation)->first();
        if($operation_details){
            return DB::connection('mysql_mes')->table('item_classification_warehouse')
                ->where('operation_id', $operation_details->operation_id)
                ->where('item_classification', $item_classification)->get();
        }

        return [];
    }

    // /assembly/get_parts
    public function get_parts(Request $request){
        try {
            if(!Auth::user()) {
                return response()->json(['message' => 'Session Expired. Please refresh the page and login to continue.']);
            }

            if(!$request->bom){
                return response()->json(['message' => "No BOM found. Please use 'Wizard - Item without BOM'"]);
            }

            $existing_po_from_so = DB::connection('mysql_mes')->table('production_order')->whereIn('sales_order', $request->so)->whereIn('bom_no', $request->bom)->where('status', '!=', 'Cancelled')->selectRaw('sales_order as id, bom_no, sum(qty_to_manufacture) as qty')->groupBy('id', 'bom_no')->get();

            $existing_po_from_mreq = DB::connection('mysql_mes')->table('production_order')->whereIn('material_request', $request->so)->whereIn('bom_no', $request->bom)->where('status', '!=', 'Cancelled')->selectRaw('material_request as id, bom_no, sum(qty_to_manufacture) as qty')->groupBy('id', 'bom_no')->get();

            $existing_po = collect($existing_po_from_so)->merge($existing_po_from_mreq)->groupBy('id');
 
            $parts = [];
            foreach ($request->bom as $idx => $bom) {
                $item_reference_id = $request->item_reference_id[$idx];
                $delivery_date = $request->delivery_date[$idx];

                $bom_parts = [];
                $bom_details = DB::connection('mysql')->table('tabBOM')->where('name', $bom)->first();

                if(!$bom_details){
                    return response()->json(['message' => 'BOM not found. Please remove item(s) without BOM.']);
                }

                $bom_operation = DB::connection('mysql')->table('tabBOM Operation')->where('parent', $bom)->first();
                $operation_name = ($bom_operation) ? $bom_operation->operation : null;

                $item_details = DB::connection('mysql')->table('tabItem')->where('name', $bom_details->item)->first();

                $item_description = ($item_details) ? $item_details->description : '';
                $item_classification = ($item_details) ? $item_details->item_classification : '';
                $item_group = ($item_details) ? $item_details->item_group : '';

                $bom_parts[] = [
                    'item_code' => $bom_details->item,
                    'description' => $item_description,
                    'item_classification' => $item_classification,
                    'item_group' => $item_group,
                    'qty' => $bom_details->quantity,
                    'bom_no' => $bom_details->name,
                    'uom' => $bom_details->uom,
                    'operation_name' => $operation_name,
                    'child_nodes' => $this->get_bom($bom),
                ];

                $reference_pref = preg_replace('/[0-9]+/', null, $request->so[$idx]);
                $reference_pref = str_replace("-", "", $reference_pref);
                foreach ($bom_parts as $parent_part) {
                    $reference_no = $request->so[$idx];

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
                            ->where('docstatus', 1)
                            ->where('production_item', $parent_part['item_code'])
                            ->where('parent_item_code', $bom_details->item)
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

                        $available_qty = ($parent_part['qty'] * $request->qty[$idx]) - (isset($existing_po[$reference_no]) ? $existing_po[$reference_no][0]->qty : 0);
                        $available_qty = $available_qty > 0 ? $available_qty : 0;

                        $parts[] = [
                            'item_reference_id' => $item_reference_id,
                            'delivery_date' => $delivery_date,
                            'parent_item' => $bom_details->item,
                            'sub_parent_item' => $bom_details->item,
                            'item_code' => $parent_part['item_code'],
                            'description' => $parent_part['description'],
                            'item_classification' => $parent_part['item_classification'],
                            'item_group' => $parent_part['item_group'],
                            'bom' => $parent_part['bom_no'],
                            'bom_reviewed' => $parent_default_bom->is_reviewed,
                            'planned_qty' => $parent_part['qty'] * $request->qty[$idx],
                            'available_qty' => $available_qty,
                            'reference_no' => $reference_no,
                            'planned_start_date' => ($existing_prod1) ? $planned_start_date1 : null,
                            'production_order' => ($existing_prod1) ? $existing_prod1->name : null,
                            'production_order_qty' => ($existing_prod1) ? $total_production_order_qty : 0,
                            'production_references' => ($existing_prod1) ? $production_references : null,
                            'po_ref_qty' => [],
                            's_warehouse' => $s_warehouse,
                            'wip_warehouse' => ($existing_prod1) ? $existing_prod1->wip_warehouse : null,
                            'fg_warehouse' => ($existing_prod1) ? $existing_prod1->fg_warehouse : null,
                            'available_stock' => $available_stock_parent,
                            'cycle_time' => $this->compute_item_cycle_time($parent_part['item_code'], $available_qty),
                            'operation_name' => $parent_part['operation_name']
                        ];
                        
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
                                ->first();

                            $s_warehouse = null;
                            if ($existing_prod2) {
                                $planned_start_date2 = Carbon::parse($existing_prod2->planned_start_date)->format('Y-m-d');
                                $s_warehouse = DB::connection('mysql')->table('tabWork Order Item')->where('parent', $existing_prod2->name)->first()->source_warehouse;
                            }

                            $child_bom_no = $child_part['bom_no'];
                            $child_existing_po_qty = DB::connection('mysql_mes')->table('production_order')
                                ->when($reference_pref == 'SO', function ($query) use ($reference_no){
                                    return $query->where('sales_order', $reference_no);
                                })
                                ->when($reference_pref == 'MREQ', function ($query) use ($reference_no){
                                    return $query->where('material_request', $reference_no);
                                })
                                ->where('bom_no', $child_default_bom->name)->where('status', '!=', 'Cancelled')->where('item_code', $child_part['item_code'])
                                ->sum('qty_to_manufacture');
                            
                            $child_available_qty = ($child_part['qty'] * $request->qty[$idx]) - $child_existing_po_qty;
                            $child_available_qty = $child_available_qty > 0 ? $child_available_qty : 0;
                    
                            $parts[] = [
                                'item_reference_id' => $item_reference_id,
                                'delivery_date' => $delivery_date,
                                'parent_item' => $bom_details->item,
                                'sub_parent_item' => $parent_part['item_code'],
                                'item_code' => $child_part['item_code'],
                                'description' => $child_part['description'],
                                'item_classification' => $child_part['item_classification'],
                                'item_group' => $child_part['item_group'],
                                'bom' => $child_default_bom->name,
                                'bom_reviewed' => $child_default_bom->is_reviewed,
                                'planned_qty' => $child_part['qty'] * $request->qty[$idx],
                                'available_qty' => $child_available_qty,
                                'reference_no' => $reference_no,
                                'planned_start_date' => ($existing_prod2) ? $planned_start_date2 : null,
                                'production_order' => ($existing_prod2) ? $existing_prod2->name : null,
                                'production_order_qty' => 0,
                                'production_references' => null,
                                'po_ref_qty' => [],
                                's_warehouse' => $s_warehouse,
                                'wip_warehouse' => ($existing_prod2) ? $existing_prod2->wip_warehouse : null,
                                'fg_warehouse' => ($existing_prod2) ? $existing_prod2->fg_warehouse : null,
                                'available_stock' => $available_stock_child,
                                'cycle_time' => $this->compute_item_cycle_time($child_part['item_code'], $child_available_qty),
                                'operation_name' => $child_part['operation_name']
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
                                    // ->where('qty', $child_part2['qty'] * $request->qty[$idx])
                                    ->first();

                                $s_warehouse = null;
                                if ($existing_prod3) {
                                    $planned_start_date3 = Carbon::parse($existing_prod3->planned_start_date)->format('Y-m-d');
                                    $s_warehouse = DB::connection('mysql')->table('tabWork Order Item')
                                        ->where('parent', $existing_prod3->name)->first()->source_warehouse;
                                }

                                $child2_bom_no = $child_part2['bom_no'];
                                $child2_existing_po_qty = DB::connection('mysql_mes')->table('production_order')
                                    ->when($reference_pref == 'SO', function ($query) use ($reference_no){
                                        return $query->where('sales_order', $reference_no);
                                    })
                                    ->when($reference_pref == 'MREQ', function ($query) use ($reference_no){
                                        return $query->where('material_request', $reference_no);
                                    })
                                    ->where('bom_no', $child_default_bom->name)->where('status', '!=', 'Cancelled')->where('item_code', $child_part2['item_code'])
                                    ->sum('qty_to_manufacture');
                                
                                $child2_available_qty = ($child_part2['qty'] * $request->qty[$idx]) - $child2_existing_po_qty;
                                $child2_available_qty = $child2_available_qty > 0 ? $child2_available_qty : 0;

                                $parts[] = [
                                    'item_reference_id' => $item_reference_id,
                                    'delivery_date' => $delivery_date,
                                    'parent_item' => $bom_details->item,
                                    'sub_parent_item' => $child_part['item_code'],
                                    'item_code' => $child_part2['item_code'],
                                    'description' => $child_part2['description'],
                                    'item_classification' => $child_part2['item_classification'],
                                    'item_group' => $child_part2['item_group'],
                                    'bom' => $child_default_bom->name,
                                    'bom_reviewed' => $child_default_bom->is_reviewed,
                                    'planned_qty' => $child_part2['qty'] * $request->qty[$idx],
                                    'available_qty' => $child2_available_qty,
                                    'reference_no' => $request->so[$idx],
                                    'planned_start_date' => ($existing_prod3) ? $planned_start_date3 : null,
                                    'production_order' => ($existing_prod3) ? $existing_prod3->name : null,
                                    'production_order_qty' => 0,
                                    'production_references' => null,
                                    'po_ref_qty' => [],
                                    's_warehouse' => $s_warehouse,
                                    'wip_warehouse' => ($existing_prod3) ? $existing_prod3->wip_warehouse : null,
                                    'fg_warehouse' => ($existing_prod3) ? $existing_prod3->fg_warehouse : null,
                                    'available_stock' => $available_stock_child1,
                                    'cycle_time' => $this->compute_item_cycle_time($child_part2['item_code'], $child2_available_qty),
                                    'operation_name' => $child_part2['operation_name']
                                ];
                            }
                        }
                    }
                }
            }

            $parts = collect($parts)->filter(function ($value, $key) {
                $allowed_operation = ['Wiring and Assembly'];
                return in_array($value['operation_name'], $allowed_operation);
            });

            return view('wizard.tbl_parts', compact('parts'));
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
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

                $bom_operation = DB::connection('mysql')->table('tabBOM Operation')->where('parent', $child_bom)->first();
                $operation_name = ($bom_operation) ? $bom_operation->operation : null;

                $materials[] = [
                    'item_code' => $item->item_code,
                    'description' => $item_description,
                    'item_classification' => $item_classification,
                    'item_group' => $item_group,
                    'qty' => $item->qty,
                    'bom_no' => $child_bom,
                    'uom' => $item->uom,
                    'operation_name' => $operation_name,
                    'child_nodes' => $this->get_bom($child_bom),
                ];
            }

            return $materials;
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    // /assembly/get_production_req_items
    public function get_production_req_items(Request $request){
        try {
            if(!Auth::user()) {
                return response()->json(['message' => 'Session Expired. Please refresh the page and login to continue.']);
            }

            $production_orders = $request->production_orders ? $request->production_orders : [];

            $items = DB::connection('mysql')->table('tabWork Order Item')->whereIn('parent', $production_orders)
                ->orderBy('parent', 'asc')->orderBy('idx', 'asc')->get();

            $req_items = [];
            foreach ($items as $item) {
                $prod = DB::connection('mysql')->table('tabWork Order')->where('name', $item->parent)->first();
                $mr = DB::connection('mysql')->table('tabMaterial Request Item')->where('docstatus', 1)->where('item_code', $item->item_code)
                    ->where('sales_order', $prod->sales_order_no)->first();

                $item_details = DB::connection('mysql')->table('tabItem')->where('name', $item->item_code)->first();

                $ste = DB::connection('mysql')->table('tabStock Entry AS se')->join('tabStock Entry Detail AS sed', 'se.name', 'sed.parent')
                    ->where('se.docstatus', '<', 2)->where('se.work_order', $item->parent)
                    ->where('se.sales_order_no', $prod->sales_order_no)
                    ->where('sed.item_code', $item->item_code)->select('se.*')->first();

                $warehouse = ($mr) ? $mr->warehouse : $item->source_warehouse;
                $qty_source_warehouse = $this->get_actual_qty($item->item_code, $warehouse);
                
                $s_warehouses = $this->get_warehouses('Wiring and Assembly', $item_details->item_classification)->toArray();
                $s_warehouses = array_column($s_warehouses, 'warehouse');
                if(!in_array($item->source_warehouse, $s_warehouses)){
                    array_push($s_warehouses, $item->source_warehouse);
                }

                $req_items[] = [
                    'id' => $item->name,
                    'production_order' => $item->parent,
                    'production_item' => $prod->production_item,
                    'production_item_name' => strtok($prod->item_name, '-'),
                    'item_code' => $item->item_code,
                    'item_name' => $item->item_name,
                    'description' => $item->description,
                    'item_image' => $item_details->item_image_path,
                    'item_classification' => $item_details->item_classification,
                    'item_group' => $item_details->item_group,
                    'stock_uom' => $item->stock_uom,
                    'sales_order' => $prod->sales_order_no,
                    'material_request' => $prod->material_request,
                    'source_warehouse' => $warehouse,
                    'required_qty' => $item->required_qty,
                    'no_of_sheets' => ($item_details->item_classification == 'CS - Crs Steel Coil') ? $item->parts_per_sheet .' Sheet(s)' : 'N/A',
                    'qty_source_warehouse' => $qty_source_warehouse,
                    'wip_warehouse' => $prod->wip_warehouse,
                    'balance_qty' => ($qty_source_warehouse - $item->required_qty),
                    'mreq' => ($mr) ? $mr->parent : null,
                    'ste' => ($ste) ? $ste->name : null,
                    's_warehouses' => $s_warehouses,
                ];
            }

            $url = $request->fullUrl();

            $view = ($request->no_bom) ? 'wizard_no_bom.tbl_req_items' : 'assembly_wizard.tbl_req_items';

            return view($view, compact('req_items', 'url'));
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function get_actual_qty($item_code, $warehouse){
        return DB::connection('mysql')->table('tabBin')->where('item_code', $item_code)
            ->where('warehouse', $warehouse)->sum('actual_qty');
    }

    public function get_raw_materials_item($item_classification, Request $request){
        if($request->autocomplete){
            return $q = DB::connection('mysql')->table('tabItem')
                ->where('item_group', 'Raw Material')
                ->where('item_classification', $item_classification)
                ->where('has_variants', 0)->where('disabled', 0)
                ->where('name', 'like', '%' . $request->q . '%')
                ->limit(5)->get();
        }

        return DB::connection('mysql')->table('tabItem')->where('item_group', 'Raw Material')
            ->where('item_classification', $item_classification)
            ->where('has_variants', 0)->where('disabled', 0)
            ->select('description', 'item_code')->get();
    }

    public function submit_change_raw_material(Request $request){
        try {
            if(!Auth::user()) {
                return response()->json(['success' => 0, 'message' => 'Session Expired. Please refresh the page and login to continue.']);
            }

            $now = Carbon::now();
            $production_order_item_details = DB::connection('mysql')->table('tabWork Order Item')
                ->where('name', $request->production_order_item_id)->first();

            if ($production_order_item_details->item_code == $request->item_code_replacecment) {
                return response()->json(['success' => 0, 'message' => 'No changes made.']);
            }

            $item_code_details = DB::connection('mysql')->table('tabItem')
                ->where('name', $request->item_code_replacecment)->first();

            if(!$item_code_details){
                return response()->json(['success' => 0, 'message' => 'Item <b>'.$request->item_code_replacecment.'</b> not found.']);
            }

            $values = [
                'parent' => $production_order_item_details->parent,
                'item_code' => $item_code_details->item_code,
                'item_name' => $item_code_details->item_name,
                'description' => $item_code_details->description,
                'stock_uom' => $item_code_details->stock_uom,
                'source_warehouse' => $item_code_details->default_warehouse,
                'modified_by' => Auth::user()->employee_name,
                'modified' => $now->toDateTimeString(),
            ];

             DB::connection('mysql')->table('tabWork Order Item')
                ->where('name', $request->production_order_item_id)->update($values);

            $details = [
                'parent' => $production_order_item_details->parent,
                'item_code' => $item_code_details->item_code,
                'item_name' => $item_code_details->item_name,
                'description' => $item_code_details->description,
                'stock_uom' => $item_code_details->stock_uom,
                'source_warehouse' => $item_code_details->default_warehouse,
                'item_classification' => $item_code_details->item_classification,
            ];

            return response()->json(['success' => 1, 'message' => 'Item Code has been changed.', 'values' => $details]);            
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function get_prod_shift_sched($date, $operation_id){
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
                ->where('operation_id', $operation_id)
                // ->operat
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

    public function production_schedule_monitoring_assembly($date){
        $date_format= date('F d, Y', strtotime($date));
        $shift_sched = $this->get_prod_shift_sched($date, "3");
        
        $scheduled_date = $date;
        $permissions = $this->get_user_permitted_operation();

        $mes_user_operations = DB::connection('mysql_mes')->table('user')
					->join('operation', 'operation.operation_id', 'user.operation_id')
					->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
                    ->where('module', 'Production')
                    ->where('user_access_id', Auth::user()->user_id)->pluck('operation_name')->toArray();

        $operation = 'Wiring and Assembly';
        
        $operation_details = DB::connection('mysql_mes')->table('operation')->where('operation_name', $operation)->first();
     
        $operation_id = ($operation_details) ? $operation_details->operation_id : 0;

        $assigned_production = DB::connection('mysql_mes')->table('assembly_conveyor_assignment')->get();

        $unassigned_production = DB::connection('mysql_mes')->table('production_order')
            ->where('operation_id', $operation_id)
            ->whereNotIn('production_order', array_column($assigned_production->toArray(), 'production_order'))
            ->where('planned_start_date', $scheduled_date)->get();

        $machines = DB::connection('mysql_mes')->table('machine')
            ->where('operation_id', $operation_id)->orderBy('order_no', 'asc')->get();

        $start = Carbon::parse($scheduled_date)->startOfDay();
        $end = Carbon::parse($scheduled_date)->endOfDay();

        $assigned_production_orders = [];
        foreach($machines as $machine){
            // get scheduled production order against $scheduled_date
            $q = DB::connection('mysql_mes')->table('assembly_conveyor_assignment as aca')
                ->join('production_order as po', 'aca.production_order', 'po.production_order')
                ->whereNotIn('po.status', ['Cancelled'])
                ->whereDate('scheduled_date', $scheduled_date)->where('machine_code', $machine->machine_code)
                ->select('aca.*', 'po.sales_order', 'po.material_request', 'po.sales_order', 'po.material_request', 'po.qty_to_manufacture', 'po.item_code', 'po.stock_uom', 'po.status', 'po.description')
                ->orderBy('aca.order_no', 'asc')->orderBy('aca.scheduled_date', 'asc');

            // get scheduled production order before $scheduled_date
            $q1 = DB::connection('mysql_mes')->table('assembly_conveyor_assignment as aca')
                ->join('production_order as po', 'aca.production_order', 'po.production_order')
                ->whereIn('po.status', ['In Progress', 'Not Started'])
                ->whereDate('scheduled_date', '<', $scheduled_date)->where('machine_code', $machine->machine_code)
                ->select('aca.*', 'po.sales_order', 'po.material_request', 'po.sales_order', 'po.material_request', 'po.qty_to_manufacture', 'po.item_code', 'po.stock_uom', 'po.status', 'po.description')
                ->orderBy('aca.order_no', 'asc')->orderBy('aca.scheduled_date', 'asc');

            // get scheduled production order before $scheduled_date
            $assigned_production_q = DB::connection('mysql_mes')->table('assembly_conveyor_assignment as aca')
                ->join('production_order as po', 'aca.production_order', 'po.production_order')
                ->whereIn('po.status', ['Completed'])
                ->whereBetween('po.actual_end_date', [$start, $end])
                ->whereDate('scheduled_date', '<', $scheduled_date)->where('machine_code', $machine->machine_code)
                ->select('aca.*', 'po.sales_order', 'po.material_request', 'po.sales_order', 'po.material_request', 'po.qty_to_manufacture', 'po.item_code', 'po.stock_uom', 'po.status', 'po.description')
                ->orderBy('aca.order_no', 'asc')->orderBy('aca.scheduled_date', 'asc')
                ->union($q)->union($q1)->get();

            $assigned_production_orders[] = [
                'machine_code' => $machine->machine_code,
                'machine_name' => $machine->machine_name,
                'production_orders' => $assigned_production_q
            ];
        }

        return view('assembly.production_schedule_monitoring_assembly', compact('date_format', 'shift_sched','date', 'scheduled_date', 'machines', 'unassigned_production', 'assigned_production_orders', 'mes_user_operations', 'permissions'));
    }
    
    public function get_production_schedule_monitoring_list_assembly(Request $request,$schedule_date){
        $orders = DB::connection('mysql_mes')->table('production_order as prod')
            ->whereNotIn('prod.status', ['Cancelled', 'Closed'])
            ->whereDate('prod.planned_start_date', $schedule_date)
            ->where('prod.operation_id', "3")
            ->where(function($q) use ($request) {
                $q->where('prod.production_order', 'LIKE', '%'.$request->search_string.'%')
                ->orWhere('prod.item_code', 'LIKE', '%'.$request->search_string.'%')
                ->orWhere('prod.customer', 'LIKE', '%'.$request->search_string.'%');
            })
            // ->distinct('prod.production_order')
            ->select('prod.*')
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
                'duration' =>$this->duration_for_completed_assembly($row->production_order),
                'feedback_qty' => ($row->feedback_qty == null)? 0 : $row->feedback_qty,
                'prod_status'=> $row->status,
                'reject' => $this->get_reject_production_sched_monitoring($row->production_order)
            ];
        }
        
        $current_date= $schedule_date;
        return view('assembly.tbl_production_sched_assembly', compact('data','current_date'));
 
    }
    public function duration_for_completed_assembly($prod){
        $orders = DB::connection('mysql_mes')->table('job_ticket as tsd')
        ->leftJoin('time_logs', 'time_logs.job_ticket_id','tsd.job_ticket_id')
        ->join('process as p', 'p.process_id', 'tsd.process_id')
        ->where('tsd.production_order',$prod)
        ->select(DB::raw('MAX(time_logs.to_time) as to_time'), DB::raw('MIN(time_logs.from_time) as from_time'))
        ->first();


        $start = Carbon::parse($orders->from_time);
        $end = Carbon::parse($orders->to_time);
        $totalDuration = $end->diffInSeconds($start);
        $op_hrs= $this->format_operating_hrs($totalDuration);

        return $op_hrs;
    }
    public function format_operating_hrs($ss){
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
    public function get_jt_details($prodno){
        $orders = DB::connection('mysql_mes')->table('job_ticket as tsd')
        ->join('process as p', 'p.process_id', 'tsd.process_id')
        ->join('production_order as po', 'po.production_order','tsd.production_order')
        ->where('tsd.production_order', $prodno)
        ->join('workstation as work','work.workstation_name','tsd.workstation')
        ->select('p.process_name','tsd.status','tsd.completed_qty', 'po.status as prod_status')
        ->orderBy('tsd.idx')
        ->get();
        return $orders;
    }
    public function get_reject_production_sched_monitoring($prodno){
        $orders = DB::connection('mysql_mes')->table('job_ticket as tsd')
        ->leftJoin('time_logs', 'time_logs.job_ticket_id', 'tsd.job_ticket_id')
        ->join('process as p', 'p.process_id', 'tsd.process_id')
        ->where('tsd.production_order', $prodno)
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
            ->where('prod.operation_id', '3')
            ->join('workstation as work','work.workstation_name','tsd.workstation')
            ->where('tsd.planned_start_date','<', $schedule_date)
            // ->distinct('prod.production_order')
            ->select('prod.*','tsd.planned_start_date')
            // ->orderBy('tsd.sequence','asc')
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
                    // 'sequence' => $row->sequence,
                    'planned_start_date' => Carbon::parse($row->planned_start_date)->format('F d, Y'),
                    // 'job_ticket'=> $this->get_jt_details($row->production_order),
                    'reject' => $this->get_reject_production_sched_monitoring($row->production_order)
                ];
            }
            $current_date= $schedule_date;
        return view('assembly.tbl_production_sched_assembly_backlog', compact('data','current_date'));
 
    }
    public function get_reject_assembly_production_order(Request $request, $schedule_date){
		
		$scheduled = DB::connection('mysql_mes')->table('production_order')
            ->where('operation_id', '3')->where('planned_start_date', $schedule_date)
			->where('status', '!=', 'Cancelled')->select('production_order.*')->get();

		$reject_prod_arr = [];
		foreach ($scheduled as $i => $row) {
			$reference_no = ($row->sales_order) ? $row->sales_order : $row->material_request;

			$process = DB::connection('mysql_mes')->table('job_ticket')
				->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
				->join('process', 'job_ticket.process_id', 'process.process_id')
				->where('job_ticket.production_order', $row->production_order)
				->where('time_logs.reject', '>', 0)
				->select('job_ticket.status', 'time_logs.reject', 'job_ticket.sequence')
				->get();

			$reject_qty = collect($process)->min('reject');
			if ($reject_qty > 0) {
				$sequence = collect($process)->min('sequence');
			
				$reject_prod_arr[] = [
					'production_order' => $row->production_order,
					'reference_no' => $reference_no,
					'customer' => $row->customer,
					'item_code' => $row->item_code,
					'item_description'=> strtok($row->description, ","),
					'required_qty' => $row->qty_to_manufacture,
					'sequence' => $sequence,
					'reject_qty' => $reject_qty,
					'stock_uom' => $row->stock_uom,
				];
			}
		}

		$reject_prod_arr = collect($reject_prod_arr)->sortBy('sequence')->toArray();

		return view('assembly.tbl_assembly_reject', compact('reject_prod_arr'));
	}
    public function get_feedbacked_production_order_assembly(Request $request, $schedule_date){
        $orders = DB::connection('mysql_mes')->table('production_order as prod')
            ->join('job_ticket as tsd','tsd.production_order','=','prod.production_order')
            ->whereNotIn('prod.status', ['Cancelled'])
            ->join('workstation as work','work.workstation_name','tsd.workstation')
            ->where('tsd.planned_start_date', $schedule_date)
            ->where('prod.operation_id','3')
            ->where(function($q) use ($request) {
                $q->where('prod.production_order', 'LIKE', '%'.$request->search_string.'%')
                ->orWhere('prod.item_code', 'LIKE', '%'.$request->search_string.'%')
                ->orWhere('prod.customer', 'LIKE', '%'.$request->search_string.'%');
            })
            ->where('prod.feedback_qty', '>', 0)
            // ->distinct('prod.production_order', 'tsd.sequence')
            ->select('prod.*','tsd.sequence')
            ->orderBy('tsd.sequence','asc')
            ->get();
        
        $data = [];
        foreach($orders as $row){
            if ($row->produced_qty == $row->feedback_qty) {
                $status = 'Full Feedback';
            }else{
                $status = 'Partial Feedback';
            }
            $status = 
            $data[]=[
                'production_order' => $row->production_order,
                'customer' => $row->customer,
                'item_code' => $row->item_code,
                'item_description'=> strtok($row->description, ","),
                'stock_uom' => $row->stock_uom,
                'qty'=> $row->feedback_qty, 
                'status' => $status
            ];
        }
        
        return view('assembly.tbl_feedback_po', compact('data'));
    }
    public function count_current_assembly_production_schedule_monitoring($schedule_date){
        $orders_rejects = DB::connection('mysql_mes')->table('production_order as prod')
            ->leftjoin('job_ticket as tsd','tsd.production_order','=','prod.production_order')
            ->leftjoin('time_logs as tl', 'tl.job_ticket_id', 'tsd.job_ticket_id')
            ->where('prod.operation_id','3')
            ->whereDate('tl.last_modified_at',$schedule_date)
            ->whereDate('tsd.planned_start_date', $schedule_date)
            // ->where('prod.status', 'Not Started')
            ->where('prod.is_scheduled' , 1)
            ->select("tsd.status","tl.reject",'prod.production_order','tsd.job_ticket_id')->get();

            $orders_inprogress = DB::connection('mysql_mes')->table('production_order as prod')
            ->leftjoin('job_ticket as tsd','tsd.production_order','=','prod.production_order')
            ->whereDate('tsd.planned_start_date', $schedule_date)
            ->where('prod.operation_id','3')
            ->where('tsd.status', 'In Progress')
            ->groupBy('prod.production_order','prod.qty_to_manufacture')
            // ->distinct('prod.production_order', 'prod.qty_to_manufacture')
            ->select('prod.production_order', 'prod.qty_to_manufacture')
            ->get();
        
            $orders_completed = DB::connection('mysql_mes')->table('production_order as prod')
            ->join('job_ticket as tsd','tsd.production_order','prod.production_order')
            ->where('prod.operation_id','3')
            ->whereDate('tsd.last_modified_at', $schedule_date)
            ->whereDate('tsd.planned_start_date', $schedule_date)
            ->whereIn('prod.status',['Completed','In Progress'])
            ->where('prod.produced_qty', '>', 0)
            ->groupBy('prod.production_order','prod.status','prod.qty_to_manufacture','prod.feedback_qty','prod.produced_qty')
            ->select('prod.production_order','prod.status','prod.qty_to_manufacture','prod.feedback_qty','prod.produced_qty')
            ->get();
            $cpt_qty=[];
            foreach($orders_completed as $row){
                if ($row->feedback_qty >= $row->qty_to_manufacture) {

                }elseif($row->feedback_qty > 0 ){
                    $cpt_qty[]=[
                        'feedback_qty' => $row->feedback_qty,
                        'produced_qty' => 0,
                        'production_order' => $row->production_order
                    ];
                }else{
                    $cpt_qty[]=[
                        'feedback_qty' => $row->feedback_qty,
                        'produced_qty' => $row->produced_qty,
                        'production_order' => $row->production_order
                    ];
                }
            }
            

            $orders_pending_po = DB::connection('mysql_mes')->table('production_order as prod')
            ->join('job_ticket as tsd','tsd.production_order','prod.production_order')
            ->where('prod.operation_id','3')
            ->whereNotIn('prod.status', ['Cancelled'])
            ->whereDate('tsd.planned_start_date', $schedule_date)
            ->groupBy('prod.production_order','prod.status','prod.qty_to_manufacture')
            ->select('prod.production_order','prod.status','prod.qty_to_manufacture')
            ->get();
            // dd($orders_pending_po);

        $scheduled = [];
 
        $count_pending = collect($orders_pending_po)->count();
        $count_pending_qty = collect($orders_pending_po)->sum('qty_to_manufacture');
        $count_inprogress_qty = collect($orders_inprogress)->sum('qty_to_manufacture');
        $count_inprogress = collect($orders_inprogress)->count();
        $count_completed = collect($cpt_qty)->count();
        $count_completed_qty= collect($cpt_qty)->sum('produced_qty');
        $count_reject = collect($orders_rejects)->where('reject','!=', '0')->sum('reject');
        $scheduled = [
                'pending' => $count_pending ,
                'inProgress' => $count_inprogress,
                'completed' => $count_completed,
                'reject' =>  $count_reject,
                'qty_pending' => $count_pending_qty,
                'qty_inprogress' =>$count_inprogress_qty,
                'qty_completed' =>$count_completed_qty
            ];
        return $scheduled;

    }
        
    public function move_today_task(Request $request){

        $val = [];
            $val = [
                'planned_start_date' => $request->prod_date_today,
                'last_modified_by' => Auth::user()->email
            ];
                 
            DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $request->prod_moved_today)->update($val);  
            DB::connection('mysql_mes')->table('production_order')->where('production_order', $request->prod_moved_today)->update($val);  

    return response()->json(['success' => 1, 'message' => 'Scheduled Date Successfully Updated!']);

    }
    
    public function get_scheduled_production_order($operation_id, $scheduled_date){
        $scheduled_date = Carbon::parse($scheduled_date);
		$start = $scheduled_date->startOfDay()->toDateTimeString();
        $end = $scheduled_date->endOfDay()->toDateTimeString();
        
        $operation_details = DB::connection('mysql_mes')->table('operation')->where('operation_id', $operation_id)->first();

        if($operation_details->operation_name == 'Painting'){
            $scheduled = DB::connection('mysql_mes')->table('production_order as prod')
                ->join('job_ticket as tsd', 'tsd.production_order', 'prod.production_order')
                ->whereNotIn('prod.status', ['Cancelled'])->whereBetween('tsd.planned_start_date', [$start, $end])
                ->where('tsd.workstation', 'Painting')//->distinct('prod.production_order', 'tsd.sequence')
                ->select('prod.*')->orderBy('tsd.sequence','asc')->get();
        }else{
            $scheduled = DB::connection('mysql_mes')->table('production_order')
                ->where('operation_id', $operation_id)->whereBetween('planned_start_date', [$start, $end])
                ->where('status', '!=', 'Cancelled')->select('production_order.*')->get();
        }

		$scheduled_arr = [];
		foreach ($scheduled as $i => $row) {
            $reference_no = ($row->sales_order) ? $row->sales_order : $row->material_request;
            			
			$scheduled_arr[] = [
				'production_order' => $row->production_order,
				'reference_no' => $reference_no,
				'customer' => $row->customer,
				'item_code' => $row->item_code,
				'description' => $row->description,
				'required_qty' => $row->qty_to_manufacture,
                'completed_qty' => $row->produced_qty,
                'sequence' => $row->order_no,
				'balance_qty' => $row->qty_to_manufacture - $row->produced_qty
			];
        }
        
        $scheduled_arr = collect($scheduled_arr)->sortBy('sequence')->toArray();
        $sched_format= Carbon::parse($scheduled_date)->format('F d, Y');

		return view('assembly.print_production_schedule_assembly', compact('scheduled_arr','sched_format', 'operation_details'));
    }
    
    public function get_production_sched_assembly_view_process($prod){
        $status =DB::connection('mysql_mes')->table('production_order')->where('production_order', $prod)->select('status')->first();
        $data= $this->get_jt_details($prod);
        // dd($data);
        $count= count($this->get_jt_details($prod));
        $col= 100/ $count;
        $duration = $this->duration_for_completed_assembly($prod);

        return view('assembly.tbl_view_process', compact('data', 'col', 'status', 'duration', 'count'));
 
    }
    
    public function assembly_conveyor_assignment($scheduled_date){
        $permissions = $this->get_user_permitted_operation();

        $mes_user_operations = DB::connection('mysql_mes')->table('user')
					->join('operation', 'operation.operation_id', 'user.operation_id')
					->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
                    ->where('module', 'Production')
                    ->where('user_access_id', Auth::user()->user_id)->pluck('operation_name')->toArray();

        $operation = 'Wiring and Assembly';
        
        $operation_details = DB::connection('mysql_mes')->table('operation')->where('operation_name', $operation)->first();
     
        $operation_id = ($operation_details) ? $operation_details->operation_id : 0;

        $assigned_production = DB::connection('mysql_mes')->table('assembly_conveyor_assignment')
            ->where('scheduled_date', $scheduled_date)->get();

        $unassigned_production = DB::connection('mysql_mes')->table('production_order')
            ->where('operation_id', $operation_id)
            ->whereNotIn('production_order', array_column($assigned_production->toArray(), 'production_order'))
            ->where('planned_start_date', $scheduled_date)->get();

        $machines = DB::connection('mysql_mes')->table('machine')
            ->where('operation_id', $operation_id)->orderBy('order_no', 'asc')->get();

        $assigned_production_orders = [];
        foreach($machines as $machine){
            $assigned_production_q = DB::connection('mysql_mes')->table('assembly_conveyor_assignment as aca')
                ->join('production_order as po', 'aca.production_order', 'po.production_order')
                ->where('scheduled_date', $scheduled_date)->where('machine_code', $machine->machine_code)
                ->select('aca.*', 'po.sales_order', 'po.material_request', 'po.sales_order', 'po.material_request', 'po.qty_to_manufacture', 'po.item_code', 'po.stock_uom')
                ->orderBy('aca.order_no', 'asc')->get();

            $assigned_production_orders[] = [
                'machine_code' => $machine->machine_code,
                'machine_name' => $machine->machine_name,
                'production_orders' => $assigned_production_q
            ];
        }

        return view('assembly.view_conveyor_assignment', compact('machines', 'unassigned_production', 'scheduled_date', 'assigned_production_orders', 'permissions', 'mes_user_operations'));
    }

    public function print_assembly_machine_schedule($scheduled_date, $machine_code){
        $machine_details = DB::connection('mysql_mes')->table('machine')->where('machine_code', $machine_code)->first();
        $scheduled_production = DB::connection('mysql_mes')->table('assembly_conveyor_assignment as aca')
            ->join('production_order as po', 'aca.production_order', 'po.production_order')
            ->where('scheduled_date', $scheduled_date)->where('machine_code', $machine_code)
            ->select('aca.*', 'po.sales_order', 'po.material_request', 'po.sales_order', 'po.material_request', 'po.qty_to_manufacture', 'po.item_code', 'po.stock_uom', 'po.delivery_date', 'po.project', 'po.customer', 'po.description')
            ->orderBy('aca.order_no', 'asc')->get();
       
        return view('assembly.print_machine_schedule', compact('scheduled_production', 'scheduled_date', 'machine_details'));
    }

    public function update_conveyor_assignment(Request $request){
        $values = [];
        if($request->list){
            foreach($request->list as $pos){
                $q_id = DB::connection('mysql_mes')->table('assembly_conveyor_assignment')
                    ->where('production_order', $pos[0])->first();

                $id = (!$q_id) ? 0 : $q_id->assembly_conveyor_assignment_id;
            
                if($pos[2] != 'unassigned'){
                    $values = [
                        'production_order' => $pos[0],
                        'order_no' => $pos[1],
                        'machine_code' => $pos[2],
                        'scheduled_date' => $request->scheduled_date,
                        'created_by' => Auth::user()->employee_name,
                    ];
                }else{
                    DB::connection('mysql_mes')->table('assembly_conveyor_assignment')
                        ->where('assembly_conveyor_assignment_id', $id)->delete();
                }

                if($id > 0){
                    $value = [
                        'order_no' => $pos[1],
                        'machine_code' => $pos[2],
                        'last_modified_by' => Auth::user()->employee_name,
                    ];

                    DB::connection('mysql_mes')->table('assembly_conveyor_assignment')
                        ->where('assembly_conveyor_assignment_id', $id)->update($value);
                }else{
                    $existing_aca = DB::connection('mysql_mes')->table('assembly_conveyor_assignment')
                        ->where('production_order', $pos[0])->exists();
                    
                    if(!$existing_aca){
                        DB::connection('mysql_mes')->table('assembly_conveyor_assignment')->insert($values);
                    }   
                }
            }
        }
    }
	
}