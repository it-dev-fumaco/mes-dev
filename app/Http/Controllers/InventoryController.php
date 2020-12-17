<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Mail\SendMail_material_request;
use Carbon\Carbon;
use DB;

use App\Traits\GeneralTrait;

class InventoryController extends Controller
{

    use GeneralTrait;
    
	public function material_request(){
        $item_list = DB::connection('mysql')->table('tabItem as item')
        ->join('tabWarehouse as w', 'item.default_warehouse', 'w.name')
        ->where('w.disabled', 0)
        ->where('w.is_group', 0)
        ->where('w.company', 'FUMACO Inc.')
        ->where('w.department', 'Fabrication')
        ->whereIn('item.item_group', ['Raw Material'])
        ->select('item.name', 'item.description')
        ->orderBy('item.modified', 'desc')->get();

        $warehouse_list = DB::connection('mysql')->table('tabWarehouse')->where('disabled', 0)->where('is_group', 0)
            ->where('company', 'FUMACO Inc.')->where('department', 'Fabrication')->pluck('name');
            
        $customer=  DB::connection('mysql')->table('tabSales Order')->where('docstatus', 1)->where('per_delivered', '<', 100)
                ->where('company', 'FUMACO Inc.')->select('customer_name')->groupBy('customer_name')->get();

        $so_list = DB::connection('mysql')->table('tabSales Order')->where('docstatus', 1)->where('per_delivered', '<', 100)
                ->where('company', 'FUMACO Inc.')->orderBy('modified', 'desc')->get();
        $project=  DB::connection('mysql')->table('tabSales Order')->where('docstatus', 1)->where('per_delivered', '<', 100)
                ->where('company', 'FUMACO Inc.')->select('project')->groupBy('project')->get();
        $mreq_stat=DB::connection('mysql')->table('tabMaterial Request')
        ->where('material_request_type', 'Purchase')
        ->where('docstatus','!=',0 )
        ->select('status')->groupBy('status')->get();


        return view('inventory.material_request.material_request', compact('item_list','warehouse_list','so_list','customer', 'mreq_stat','project'));
    }
    public function save_material_purchase(Request $request){
        $now = Carbon::now();
        $data = $request->all();
        $operation = $data['operation'];
        $purchase_type = $data['purchase_type'];
        $sales_order = $data['sales_order'];
        $customer = $data['customer'];
        $required_date_all = $data['required_date_all'];
        $project = $data['project'];
        $new_item_code = $data['new_item_code'];
        $qty = $data['qty'];
        $new_warehouse = $data['new_warehouse'];
        $required_date = $data['required_date'];

        try {

            if ($request->new_item_code) { 
                $order_details = DB::connection('mysql')->table("tabSales Order")->where('name', $sales_order)->first();
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
                    'naming_series' => 'PREQ-',
                    'title' => 'Purchase',
                    'transaction_date' => $now->toDateTimeString(),
                    'status' => 'Pending',
                    'company' => 'FUMACO Inc.',
                    'schedule_date' => $required_date_all,
                    'material_request_type' => 'Purchase',
                    'delivery_date' => empty($order_details)? null : $order_details->delivery_date,
                    'customer_name' => empty($order_details)? null : $order_details->customer,
                    'sales_order' => $sales_order,
                    'project' => empty($order_details)? null : $order_details->project,
                    'purchase_request' => $purchase_type,
                    
                ];
               foreach($new_item_code as $i => $row){
                    if ($now->format('Y-m-d') > $required_date_all){
                                return response()->json(['success' => 0, 'message' => 'Date cannot be before Transaction Date']);
                    }elseif($now->format('Y-m-d') > $required_date[$i]){
                                return response()->json(['success' => 0, 'message' => 'Date cannot be before Transaction Date']);
                    }elseif($row == 'none'){
                                return response()->json(['success' => 0, 'message' => 'Please Select Item Code']);
                    }else{
                    $item_details = DB::connection('mysql')->table('tabItem')->where('name', $row)->first();
                    $actual_qty = $this->get_actual_qty($row, $new_warehouse[$i]);
                    $mr_item = [
                        'name' => 'mes'.uniqid(),
                        'creation' => $now->toDateTimeString(),
                        'modified' => $now->toDateTimeString(),
                        'modified_by' => Auth::user()->email,
                        'owner' => Auth::user()->email,
                        'docstatus' => 1,
                        'parent' => $new_id,
                        'parentfield' => 'items',
                        'parenttype' => 'Material Request',
                        'idx' => $i + 1,
                        'stock_qty' => abs($qty[$i] * 1),
                        'qty' => abs($qty[$i]),
                        'actual_qty' => $actual_qty,
                        'schedule_date' => $required_date[$i],
                        'item_name' => $item_details->item_name,
                        'stock_uom' => $item_details->stock_uom,
                        'warehouse' => $new_warehouse[$i],
                        'uom' => $item_details->stock_uom,
                        'description' => $item_details->description,
                        'conversion_factor' => 1,
                        'item_code' => $row,
                        'sales_order' => $sales_order,
                        'item_group' => $item_details->item_group,
                        'project' => empty($order_details)? null : $order_details->project,
                        
                    ];
                }
                    DB::connection('mysql')->table('tabMaterial Request Item')->insert($mr_item); 
            }
                DB::connection('mysql')->table('tabMaterial Request')->insert($mr);
                
                return response()->json(['success' => 1, 'message' => 'successfully inserted']);
            }       
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }
    public function get_actual_qty($item_code, $warehouse){
        return DB::connection('mysql')->table('tabBin')->where('item_code', $item_code)
            ->where('warehouse', $warehouse)->sum('actual_qty');
    }
    public function list_material_purchase(Request $request){
        $purchase_lists= DB::connection('mysql')->table('tabMaterial Request as mt')
        ->join('tabMaterial Request Item as imt', 'imt.parent', 'mt.name')
        ->whereBetween(DB::raw('DATE_FORMAT(mt.creation, "%Y-%m-%d")'),[$request->from,$request->end])
        ->where('mt.customer_name', 'LIKE', '%'.$request->customer.'%')
        ->where('mt.project',   'LIKE', '%'.$request->project.'%')
        ->where('mt.sales_order', 'LIKE', '%'.$request->so.'%')
        ->Where('mt.status', 'LIKE', '%'.$request->status.'%')
        ->Where('imt.item_code', 'LIKE', '%'.$request->item_code.'%')
        ->where('mt.docstatus','!=',0 )->orderBy('mt.modified', 'desc')
        ->distinct('mt.name')
        ->select('mt.*')
        ->get();
          // Get current page form url e.x. &page=1
          $currentPage = LengthAwarePaginator::resolveCurrentPage();
     
          // Create a new Laravel collection from the array data
          $itemCollection = collect($purchase_lists);
       
          // Define how many items we want to be visible in each page
          $perPage = 10;
       
          // Slice the collection to get the items to display in current page
          $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
       
          // Create our paginator and pass it to the view
          $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
       
          // set url path for generted links
          $paginatedItems->setPath($request->url());
          $purchase_list = $paginatedItems;
  
          $count=  collect($purchase_lists)->count();
        return view('inventory.material_request.tbl_material_request_purchase', compact('purchase_list', 'count'));
    }
    public function get_selection_box_in_item_code_warehouse(Request $request){
        $item_list = DB::connection('mysql')->table('tabItem as item')
        ->join('tabWarehouse as w', 'item.default_warehouse', 'w.name')
        ->where('w.disabled', 0)
        ->where('w.is_group', 0)
        ->where('w.company', 'FUMACO Inc.')
        ->where('w.department', 'Fabrication')
        ->whereIn('item.item_group', ['Raw Material'])
        ->select('item.name', 'item.description')
        ->orderBy('item.modified', 'desc')->get();

        $warehouse_list = DB::connection('mysql')->table('tabWarehouse')->where('disabled', 0)->where('is_group', 0)
            ->where('company', 'FUMACO Inc.')->where('department', 'Fabrication')->pluck('name');
        return response()->json(['item_list' => $item_list,'warehouse' =>$warehouse_list ]);

    }
    public function cancel_material_purchase_request(Request $request){
        try {
            $now = Carbon::now();

            DB::connection('mysql')->table('tabMaterial Request')->where('name', $request->purchase_id)
                ->where('docstatus', 1)->where('status', '!=', 'Ordered')
                ->update(['docstatus' => 2, 'status' => 'Cancelled', 'modified' => $now->toDateTimeString(), 'modified_by' => Auth::user()->email]);
            
            DB::connection('mysql')->table('tabMaterial Request Item')->where('parent', $request->purchase_id)
                ->where('docstatus', 1)
                ->update(['docstatus' => 2, 'modified' => $now->toDateTimeString(), 'modified_by' => Auth::user()->email]);


            return response()->json(['success' => 1, 'message' => 'Material Request for Purchase <b>' . $request->purchase_id . '</b> has been cancelled.']);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }
    public function get_material_request_for_purchase($id){
        $purchase_list=  DB::connection('mysql')->table('tabMaterial Request')->where('name', $id)->first();


        $purchase_list_item=  DB::connection('mysql')->table('tabMaterial Request Item')->where('parent', $id)->orderBy('idx', 'asc')->get();

        
        return view('inventory.material_request.tbl_material_request_purchase_view', compact('purchase_list', 'purchase_list_item'));

    }
    public function tbl_filter_material_purchase_request(Request $request, $from, $end){
        $purchase_lists=  DB::connection('mysql')->table('tabMaterial Request as mt')
        ->join('tabMaterial Request Item as imt', 'imt.parent', 'mt.name')
        ->whereBetween(DB::raw('DATE_FORMAT(mt.schedule_date, "%Y-%m-%d")'),[$from,$end])
        ->where('mt.customer_name', 'LIKE', '%'.$request->customer.'%')
        ->where('mt.project',   'LIKE', '%'.$request->project.'%')
        ->where('mt.sales_order', 'LIKE', '%'.$request->so.'%')
        ->Where('mt.status', 'LIKE', '%'.$request->status.'%')
        ->Where('imt.item_code', 'LIKE', '%'.$request->item_code.'%')
        ->where('mt.docstatus','!=',0 )->orderBy('mt.modified', 'desc')
        ->distinct('mt.name')
        ->select('mt.*')
        ->get();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
     
        // Create a new Laravel collection from the array data
        $itemCollection = collect($purchase_lists);
     
        // Define how many items we want to be visible in each page
        $perPage = 10;
     
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
     
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
     
        // set url path for generted links
        $paginatedItems->setPath($request->url());
        $purchase_list = $paginatedItems;

        $count=  collect($purchase_lists)->count();
        return view('inventory.material_request.tbl_material_request_purchase', compact('purchase_list', 'count'));

    }
    public function get_uom_item_selected_in_purchase($item_code){
         $item_list = DB::connection('mysql')->table('tabItem as item')
            ->where('item.name', $item_code)
            ->select('item.stock_uom')
            ->first();
        return $item_list->stock_uom;
    }
    public function save_wip(Request $request){
            $now = Carbon::now();
            if (DB::connection('mysql_mes')->table('wip_setup')
                ->where('operation_id', '=', $request->icw_wip_operation)
                // ->where('wip_name', '=', $request->icw_workinprogress)
                ->exists()){
                return response()->json(['success' => 0, 'message' => 'Work in Progress already exists']);
            }else{
                $values1 = [
                    'operation_id' => $request->icw_wip_operation,
                    'warehouse' => $request->icw_workinprogress,
                    'last_modified_by' => Auth::user()->email,
                    'created_by' => Auth::user()->email,
                    'created_at' => $now->toDateTimeString()
                ];

            DB::connection('mysql_mes')->table('wip_setup')->insert($values1);
            return response()->json(['success' => 1, 'message' => 'Work in Progress successfully Added.']);
            }
    }
    public function tbl_wip_list(){
        $wip_list=  DB::connection('mysql_mes')->table('wip_setup as wip')
        ->join('operation as op', 'op.operation_id', 'wip.operation_id')->select('wip.*','op.operation_name')->orderBy('wip_id', 'desc')->paginate(10);
        return view('inventory.tbl_wip_list', compact('wip_list'));
    }
    public function edit_wip(Request $request){
        $now = Carbon::now();

        if (DB::connection('mysql_mes')
            ->table('wip_setup')
            ->where('operation_id', '=', $request->edit_icw_wip_operation)
            ->where('warehouse', '=', $request->edit_icw_workinprogress)
            ->exists()){


            if(strtoupper($request->orig_icw_wip_operation) == strtoupper($request->edit_icw_wip_operation) && strtoupper($request->orig_icw_workinprogress) == strtoupper($request->edit_icw_workinprogress)){
                $data = [
                    'operation_id' => $request->edit_icw_wip_operation,
                    'warehouse' => $request->edit_icw_workinprogress,
                    'last_modified_by' => Auth::user()->employee_name,
                    'last_modified_at' => $now->toDateTimeString()
                ];

                    DB::connection('mysql_mes')->table('wip_setup')->where('wip_id', $request->wip_id)->update($data);
                    return response()->json(['success' => 1,'message' => 'Work In Progress successfully updated.']);


            }else{
                return response()->json(['success' => 0,'message' => 'Work In Progress already exists.']);
            }

        }else{
            $data = [
                    'operation_id' => $request->edit_icw_wip_operation,
                    'warehouse' => $request->edit_icw_workinprogress,
                    'last_modified_by' => Auth::user()->employee_name,
                    'last_modified_at' => $now->toDateTimeString()
                ];

                    DB::connection('mysql_mes')->table('wip_setup')->where('wip_id', $request->wip_id)->update($data);
                    return response()->json(['success' => 1,'message' => 'Work In Progress successfully updated.']);

        }
    }
    public function delete_wip(Request $request){
        DB::connection('mysql_mes')->table('wip_setup')->where('wip_id', $request->delete_wip_id)->delete();

        return response()->json(['success' => 1,'message' => 'Work In Progress successfully deleted.']);
    }

    
    

    // J
    public function inventory_index(){
        $permissions = $this->get_user_permitted_operation();

        $item_list = DB::connection('mysql')->table('tabItem as item')
        ->join('tabWarehouse as w', 'item.default_warehouse', 'w.name')
        ->where('w.disabled', 0)
        ->where('w.is_group', 0)
        ->where('w.company', 'FUMACO Inc.')
        ->where('w.department', 'Fabrication')
        ->whereIn('item.item_group', ['Raw Material'])
        ->select('item.name', 'item.description')
        ->orderBy('item.modified', 'desc')->get();

        $warehouse_list = DB::connection('mysql')->table('tabWarehouse')->where('disabled', 0)->where('is_group', 0)
            ->where('company', 'FUMACO Inc.')->where('department', 'Fabrication')->pluck('name');
        $customer=  DB::connection('mysql')->table('tabSales Order')->where('docstatus', 1)->where('per_delivered', '<', 100)
                ->where('company', 'FUMACO Inc.')->select('customer_name')->groupBy('customer_name')->get();

        $so_list = DB::connection('mysql')->table('tabSales Order')->where('docstatus', 1)->where('per_delivered', '<', 100)
                ->where('company', 'FUMACO Inc.')->orderBy('modified', 'desc')->get();
        $mreq_list = DB::connection('mysql')->table('tabMaterial Request')->where('docstatus', 1)
                ->where('company', 'FUMACO Inc.')->orderBy('modified', 'desc')->get();
        $mreq_list_transfer = DB::connection('mysql')->table('tabMaterial Request')->where('docstatus', 1)->where('material_request_type', 'Manufacture')
                ->where('company', 'FUMACO Inc.')->orderBy('modified', 'desc')->get();
        $project=  DB::connection('mysql')->table('tabSales Order')->where('docstatus', 1)->where('per_delivered', '<', 100)
                ->where('company', 'FUMACO Inc.')->select('project')->groupBy('project')->get();
        $ste_list=  DB::connection('mysql')->table('tabStock Entry')->select('name')->whereDate('creation','>=','2020-07-01')->get();

        $mreq_stat=DB::connection('mysql')->table('tabMaterial Request')
        ->where('material_request_type', 'Purchase')
        ->where('docstatus','!=',0 )
        ->select('status')->groupBy('status')->get();

        return view('inventory.index', compact('item_list','customer','warehouse_list', 'project','so_list', 'mreq_list', 'ste_list', 'mreq_stat', 'permissions', 'mreq_list_transfer'));
    }

    public function get_inventory_list(Request $request, $operation){
        $inv_qry = DB::connection('mysql_mes')->table('fabrication_inventory as fab')
            ->join('operation as op', 'op.operation_id', 'fab.operation_id')
            ->where('op.operation_name', 'LIKE', '%Fabrication%')
            ->whereNotNull('fab.description')->where('fab.description', '!=', '')
            ->when($request->q, function ($query) use ($request) {
                return $query->where(function($q) use ($request) {
                    $q->where('fab.item_code', 'LIKE', '%'.$request->q.'%')
                        ->orWhere('fab.description', 'LIKE', '%'.$request->q.'%');
                });
            })
            ->when($request->filters, function ($query) use ($request) {
                foreach ($request->filters as $f) {
                    $query->where('fab.description', 'LIKE', "%".$f."%");
                }

                return $query;
            })
            ->select('fab.*')
            ->paginate(10);

        $inventory_list = [];
        foreach ($inv_qry as $row) {
            $planned_qty = DB::connection('mysql_mes')->table('production_order')->where('item_code', $row->item_code)
                ->where('status', 'Not Started')->sum('qty_to_manufacture');

            $in_process_qty = DB::connection('mysql_mes')->table('production_order')->where('item_code', $row->item_code)
                ->where('status', 'In Progress')->sum('qty_to_manufacture');
            $qty = 1;
            $inventory_list[] = [
                'item_code' => $row->item_code,
                'description' => $row->description,
                'planned_qty' => $planned_qty,
                'in_process_qty' => $in_process_qty,
                'balance_qty' => $row->balance_qty,
                'cycle_time' => $this->compute_item_cycle_time($row->item_code, $qty)
            ];
        }

        $data = $inventory_list;

        // // Get current page form url e.x. &page=1
        // $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // // Create a new Laravel collection from the array data
        // $itemCollection = collect($inventory_list);
        // // Define how many items we want to be visible in each page
        // $perPage = 10;
        // // Slice the collection to get the items to display in current page
        // $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        // // Create our paginator and pass it to the view
        // $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
        // // set url path for generted links
        // $paginatedItems->setPath($request->url());
        // $data = $paginatedItems;

        return view('inventory.tbl_inventory_list', compact('data', 'inv_qry'));
    }

    public function get_transaction_history(Request $request, $operation){
        // $operation_details = DB::connection('mysql_mes')->table('operation')->where('operation_name', $operation)->first();
  
        $data = DB::connection('mysql_mes')->table('inventory_transaction as it')
            ->join('operation', 'operation.operation_id', 'it.operation_id')
            // ->when($request->material, function($q) use ($request){
            //     $q->where('item_specification.material', $request->material);
            // })
            // ->when($request->length, function($q) use ($request){
            //     $q->where('item_specification.length', $request->length);
            // })
            // ->when($request->width, function($q) use ($request){
            //     $q->where('item_specification.width', $request->width);
            // })
            // ->when($request->thickness, function($q) use ($request){
            //     $q->where('item_specification.thickness', $request->thickness);
            // })
            ->when($request->entry_type, function($q) use ($request){
                $q->where('it.entry_type', $request->entry_type);
            })
            ->when($request->q, function($q) use ($request){
                $q->where(function($r) use ($request){
                    // where('item_specification.material', 'LIKE', '%' . $request->q . '%')
                    //     ->orWhere('item_specification.length', 'LIKE', '%' . $request->q . '%')
                    //     ->orWhere('item_specification.width', 'LIKE', '%' . $request->q . '%')
                    //     ->orWhere('item_specification.thickness', 'LIKE', '%' . $request->q . '%')
                        $r->orWhere('it.item_code', 'LIKE', '%'.$request->q.'%')
                        ->orWhere('it.last_modified_by', 'LIKE', '%'.$request->q.'%')
                        ->orWhere('it.adjusted_qty', 'LIKE', '%'.$request->q.'%')
                        ->orWhere('it.previous_qty', 'LIKE', '%'.$request->q.'%')
                        ->orWhere('it.entry_type', 'LIKE', '%'.$request->q.'%')
                        ->orWhere('it.transaction_no', 'LIKE', '%'.$request->q.'%');
                });
            })
            ->orderBy('it.transaction_no', 'desc')->paginate(10);

        return view('tables.tbl_fabrication_inventory_history_list', compact('data'));
    }

    public function get_scrap_inventory(Request $request, $operation){
        $q = DB::connection('mysql_mes')->table('scrap')
            ->join('usable_scrap', 'usable_scrap.scrap_id', 'scrap.scrap_id')
            ->when($request->material, function($q) use ($request){
                $q->where('scrap.material', $request->material);
            })
            ->when($request->length, function($q) use ($request){
                $q->where('usable_scrap.length', $request->length);
            })
            ->when($request->width, function($q) use ($request){
                $q->where('usable_scrap.width', $request->width);
            })
            ->when($request->thickness, function($q) use ($request){
                $q->where('scrap.thickness', $request->thickness);
            })
            ->when($request->q, function($q) use ($request){
                $q->where(function($r) use ($request){
                    $r->where('scrap.material', 'LIKE', '%' . $request->q . '%')
                        ->orWhere('usable_scrap.length', 'LIKE', '%' . $request->q . '%')
                        ->orWhere('usable_scrap.width', 'LIKE', '%' . $request->q . '%')
                        ->orWhere('scrap.thickness', 'LIKE', '%' . $request->q . '%');
                });
            })
            ->paginate(10);

        return view('inventory.tbl_scrap', compact('q'));
    }

    public function get_withdrawal_slips(Request $request, $operation){
        $production_orders = DB::connection('mysql_mes')->table('production_order')
            ->where('status', '!=', 'Cancelled')
            ->when($request->production_order, function($q) use ($request){
                $q->where('production_order', 'like', '%'.$request->production_order.'%');
            })
            ->when($request->customer, function($q) use ($request){
                $q->where('customer', 'like', '%'.$request->customer.'%');
            })
            ->when($request->q, function($q) use ($request){
                $q->where(function($r) use ($request){
                    $r->where('production_order', 'LIKE', '%' . $request->q . '%')
                        ->orWhere('sales_order', 'LIKE', '%' . $request->q . '%')
                        ->orWhere('material_request', 'LIKE', '%' . $request->q . '%')
                        ->orWhere('customer', 'LIKE', '%' . $request->q . '%')
                        ->orWhere('bom_no', 'LIKE', '%' . $request->q . '%');
                });
            })
            ->pluck('production_order');

        $query = DB::connection('mysql')->table('tabStock Entry as ste')
            ->when($request->source_warehouse, function($q) use ($request){
                $q->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
                    ->where('s_warehouse', $request->source_warehouse);
            })
            ->where('ste.purpose', 'Material Transfer for Manufacture')
            ->whereIn('ste.production_order', $production_orders)
            ->when($request->production_order, function($q) use ($request){
                $q->where('ste.production_order', 'like', '%'.$request->production_order.'%');
            })
            ->when($request->ste_no, function($q) use ($request){
                $q->where('ste.name', 'like', '%'.$request->ste_no.'%');
            })
            ->when($request->customer, function($q) use ($request){
                $q->where('ste.so_customer_name', 'like', '%'.$request->customer.'%');
            })
            ->when($request->status, function($q) use ($request){
                $q->where('ste.docstatus', $request->status);
            })
            ->when($request->q, function($q) use ($request){
                $q->where(function($r) use ($request){
                    $r->where('ste.name', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('ste.production_order', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('ste.sales_order_no', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('ste.material_request', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('ste.so_customer_name', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('ste.bom_no', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('ste.item_status', 'LIKE', '%' . $request->q . '%');
                });
            })
            ->select('ste.*')
            ->orderBy('ste.modified', 'desc')->get();

        $list_arr = [];
        foreach ($query as $row) {
            $sted = DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $row->name)->orderBy('date_modified', 'desc')->first();
            $list_arr[] = [
                'name' => $row->name,
                'production_order' => $row->production_order,
                'sales_order_no' => $row->sales_order_no,
                'so_customer_name' => $row->so_customer_name,
                'bom_no' => $row->bom_no,
                'item_status' => $row->item_status,
                'issued_by' => ($sted) ? $sted->session_user : null,
                'warehouse' => ($sted) ? $sted->s_warehouse : null,
            ];
        }

         // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // Create a new Laravel collection from the array data
        $itemCollection = collect($list_arr);
        // Define how many items we want to be visible in each page
        $perPage = 10;
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
        // set url path for generted links
        $paginatedItems->setPath($request->url());
        $list = $paginatedItems;

        return view('inventory.tbl_withdrawal_slips', compact('list'));
    }

    public function get_withdrawal_slip_filters($operation){
        $q = DB::connection('mysql_mes')->table('production_order')->orderBy('production_order', 'desc')->get();

        $warehouses = DB::connection('mysql_mes')->table('item_classification_warehouse')
            ->distinct()->pluck('warehouse');

        return [
            'production_orders' => collect($q)->pluck('production_order'),
            'customer' => collect($q)->unique('customer')->pluck('customer'),
            'warehouse' => $warehouses
        ];
    }

    public function get_scrap_filters($operation){
        $data=[];
        return $data;
    }

    public function get_inventory_filters($operation){
        $item_specs = $this->get_material_dimenstion();

        $part_categories = DB::connection('mysql')->table('tabItem')
            ->where('has_variants', 0)->where('disabled', 0)
            ->whereNotNull('parts_category')->where('parts_category', '!=', '')
            ->distinct()->orderBy('parts_category', 'asc')->pluck('parts_category');

        $q = DB::connection('mysql_mes')->table('fabrication_inventory')
            ->whereNotNull('description')->distinct()->pluck('description');

        $item_names = collect($q)->map(function ($item, $key) {
            return ['item_name' => strtok($item, ',')];
        })->unique('item_name')->pluck('item_name');

        return array_merge($item_specs, ['item_names' => $item_names, 'part_categories' => $part_categories]);
    }

    public function get_transaction_filters($operation){
        return $this->get_material_dimenstion();
    }

    public function get_material_dimenstion(){
        $q=null;
        $materials = collect($q)->map(function($item, $key) {
            return ['material' => strtoupper($item->material)];
        });
   
        return [
            'material' => collect($materials)->unique('material')->pluck('material'),
            'length' => collect($q)->unique('length')->pluck('length'),
            'width' => collect($q)->unique('width')->pluck('width'),
            'thickness' => collect($q)->unique('thickness')->pluck('thickness')
        ];
    }

    public function add_scrap(Request $request){
        try{
            if($request->qty <= 0){
                return response()->json(['success' => 1, 'message' => 'Qty cannot be less than or equal to 0.']);
            }
    
            $uom_details_kg = DB::connection('mysql_mes')->table('uom')->where('uom_name', 'like', '%kilogram%')->first();
            if(!$uom_details_kg){
                return response()->json(['success' => 1, 'message' => 'UoM "Kilogram" not found.']);
            }
    
            $uom_details_cubic = DB::connection('mysql_mes')->table('uom')->where('uom_name', 'like', '%kilogram%')->first();
            if(!$uom_details_cubic){
                return response()->json(['success' => 1, 'message' => 'UoM "Cubic MM" not found.']);
            }
    
            // get uom conversion id
            $uom_conversion = DB::connection('mysql_mes')->table('uom_conversion')
                ->whereIn('uom_id', [$uom_details_kg->uom_id, $uom_details_cubic->uom_id])
                ->distinct('uom_conversion_id')->first();
            
            if(!$uom_conversion){
                return response()->json(['success' => 1, 'message' => 'UoM conversion not found.']);
            }
    
            $existing_scrap = DB::connection('mysql_mes')->table('scrap')
                ->where('material', $request->material)
                ->where('thickness', $request->thickness)
                ->first();
            
            // insert to usable
            if($request->scrap_type == 'Usable'){
                if($existing_scrap){
                    $scrap_id = $existing_scrap->scrap_id;
                }else{
                    $values = [
                        'uom_conversion_id' => $uom_conversion->uom_conversion_id,
                        'uom_id' => $uom_details_kg->uom_id,
                        'material' => $request->material,
                        'thickness' => $request->thickness,
                        'scrap_qty' => 0,
                        'created_by' => Auth::user()->employee_name,
                    ];
        
                    $scrap_id = DB::connection('mysql_mes')->table('scrap')->insertGetId($values);
                }

                $usable_scrap = DB::connection('mysql_mes')->table('usable_scrap')
                    ->where('length', (float)$request->length)
                    ->where('width', (float)$request->width)
                    ->first();
    
                if($usable_scrap){
                    $values = [
                        'usable_scrap_qty' => $usable_scrap->usable_scrap_qty + $request->qty,
                        'last_modified_by' => Auth::user()->employee_name,
                    ];
                    
                    DB::connection('mysql_mes')->table('usable_scrap')
                        ->where('usable_scrap_id', $usable_scrap->usable_scrap_id)->update($values);
                }else{
                    $values = [
                        'uom_conversion_id' => $uom_conversion->uom_conversion_id,
                        'scrap_id' => $scrap_id,
                        'length' => $request->length,
                        'width' => $request->width,
                        'usable_scrap_qty' => $request->qty,
                        'created_by' => Auth::user()->employee_name,
                    ];
                    
                    DB::connection('mysql_mes')->table('usable_scrap')->insert($values);
                }
            }

            if($request->scrap_type == 'Unusable'){
                if($existing_scrap){
                    $values = [
                        'scrap_qty' => $existing_scrap->scrap_qty + $request->qty,
                        'last_modified_by' => Auth::user()->employee_name,
                    ];
                    
                    DB::connection('mysql_mes')->table('scrap')
                        ->where('scrap_id', $existing_scrap->scrap_id)->update($values);
                }else{
                    $values = [
                        'uom_conversion_id' => $uom_conversion->uom_conversion_id,
                        'uom_id' => $uom_details_kg->uom_id,
                        'material' => $request->material,
                        'thickness' => $request->thickness,
                        'scrap_qty' => $request->qty,
                        'created_by' => Auth::user()->employee_name,
                    ];
                    
                    DB::connection('mysql_mes')->table('scrap')->insert($values);
                }
            }
    
            return response()->json(['success' => 1, 'message' => 'Scrap added to inventory.']);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function get_scrap_to_process(Request $request, $workstation){
        $q = DB::connection('mysql_mes')->table('scrap')
            ->where('scrap_qty', '>', 0)->get();

        return view('tables.tbl_select_scrap_to_process', compact('q'));
    }

    public function get_process(Request $request, $workstation){
        $q = DB::connection('mysql_mes')->table('process_assignment')
            ->join('workstation', 'workstation.workstation_id', 'process_assignment.workstation_id')
            ->join('process', 'process_assignment.process_id', 'process.process_id')
            ->where('workstation.workstation_name', $workstation)
            ->distinct()->pluck('process_name', 'process.process_id');

        return view('tables.tbl_select_process_for_scrap', compact('q'));
    }
   

    public function save_material_transfer(Request $request){

    $now = Carbon::now();
    $data = $request->all();
    // return $data;
    $item_code = $data['new_item_code'];
    $ssource = $data['new_s_warehouse'];
    $tsource = $data['new_t_warehouse'];
    $qty = $data['qty'];


    $latest_ste = DB::connection('mysql')->table('tabStock Entry')->max('name');
    $latest_ste_exploded = explode("-", $latest_ste);
    $new_id = $latest_ste_exploded[1] + 1;
    $new_id = str_pad($new_id, 5, '0', STR_PAD_LEFT);
    $new_id = 'STEM-'.$new_id;

    try{
    $stock_entry_detail = [];
     // return $data;
            if ($request->new_item_code) { 
               foreach($item_code as $i => $row){
                    if($row == 'none'){
                            return response()->json(['success' => 0, 'message' => 'Please Select Item Code']);

                
                    }elseif($ssource[$i] == 'none'){
                            return response()->json(['success' => 0, 'message' => 'Please Select Source Warehouse']);

                    }elseif($tsource[$i] == 'none'){
                            return response()->json(['success' => 0, 'message' => 'Please Select Target Warehouse']);

                    }else{
                        if ($ssource[$i] == $tsource[$i]) {
                            return response()->json(['success' => 0, 'message' => 'Source Warehouse must not be the same as Target Warehouse for '.$row]);
                        }else{

                            $item_details = DB::connection('mysql')->table('tabItem')->where('name', $row)->first();
                            $conversion = DB::connection('mysql')->table('tabUOM Conversion Detail')->where('parent', $row)->first();
                            $bin_qry = DB::connection('mysql')->table('tabBin')
                                ->where('warehouse',  $tsource[$i])
                                ->where('item_code', $row)->first();
                                // dd($$bin_qry->valuation_rate);
                
                            $stock_entry_detail[] = [
                                'name' =>  uniqid(),
                                'creation' => $now->toDateTimeString(),
                                'modified' => $now->toDateTimeString(),
                                'modified_by' => Auth::user()->email,
                                'owner' => Auth::user()->email,
                                'docstatus' => 0,
                                'parent' => $new_id,
                                'parentfield' => 'items',
                                'parenttype' => 'Stock Entry',
                                'idx' => $i + 1,
                                't_warehouse' => $tsource[$i],
                                'transfer_qty' => $qty[$i],
                                'serial_no' => null,
                                'expense_account' => 'Cost of Goods Sold - FI',
                                'cost_center' => 'Main - FI',
                                'actual_qty' => 0,
                                's_warehouse' => $ssource[$i],
                                'item_name' => $row,
                                'image' => null,
                                'additional_cost' => 0,
                                'stock_uom' => $item_details->stock_uom,
                                'basic_amount' => (empty($bin_qry->valuation_rate)?0:$bin_qry->valuation_rate) * $qty[$i],
                                'sample_quantity' => 0,
                                'uom' => $item_details->stock_uom,
                                'basic_rate' => (empty($bin_qry->valuation_rate)?0:$bin_qry->valuation_rate),
                                'description' => $item_details->description,
                                'barcode' => null,
                                'conversion_factor' => $conversion->conversion_factor,
                                'item_code' => $row,
                                'retain_sample' => 0,
                                'qty' => $qty[$i],
                                'bom_no' => 0,
                                'allow_zero_valuation_rate' => 0,
                                'material_request_item' => null,
                                'amount' => (empty($bin_qry->valuation_rate)?0:$bin_qry->valuation_rate) * $qty[$i],
                                'batch_no' => null,
                                'valuation_rate' => (empty($bin_qry->valuation_rate)?0:$bin_qry->valuation_rate),
                                'material_request' => null,
                                't_warehouse_personnel' => null,
                                's_warehouse_personnel' => null,
                                'target_warehouse_location' => null,
                                'source_warehouse_location' => null,
                                // 'date_modified' => ,
                                'status' => "For Checking",
                                // 'session_user' => ,
                                // 'remarks' => ,
                                // 'validate_item_code' => ,
                                // 'issued_qty' => ,
                                // 'item_note' => ,
                            ];

                        }

                    } 
                }
                DB::connection('mysql')->table('tabStock Entry Detail')->insert($stock_entry_detail);
                $so_list = DB::connection('mysql')->table('tabSales Order')->where('name', $request->sales_order)->first();
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
                    'fg_completed_qty' => 0,
                    'letter_head' => null,
                    '_liked_by' => null,
                    'purchase_receipt_no' => null,
                    'posting_time' => $now->format('H:i:s'),
                    'customer_name' => null,
                    'to_warehouse' => $request->t_warehouse,
                    'title' => 'Material Transfer',
                    '_comments' => null,
                    'from_warehouse' => $request->s_warehouse,
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
                    'bom_no' => null,
                    'amended_from' => null,
                    'total_amount' =>collect($stock_entry_detail)->sum('basic_amount'),
                    'total_incoming_value' =>  collect($stock_entry_detail)->sum('basic_amount'),
                    'project' => $request->project,
                    '_assign' => null,
                    'select_print_heading' => null,
                    'posting_date' => $now->format('Y-m-d'),
                    'target_address_display' => null,
                    'production_order' => null,
                    'purpose' => 'Material Transfer',
                    'shipping_address_contact_person' => null,
                    'customer_1' => null,
                    'material_request' => $request->material_request,
                    'reference_no' => null,
                    'delivery_date' => null,
                    'delivery_address' => null,
                    'city' => null,
                    'address_line_2' => null,
                    'address_line_1' => null,
                    'item_status' => 'For Checking',
                    'sales_order_no' => $request->sales_order,
                    'transfer_as' => 'Internal Transfer',
                    'workflow_state' => null,
                    'item_classification' => null,
                    'bom_repack' => null,
                    'qty_repack' => 0,
                    'issue_as' => null,
                    'receive_as' => null,
                    'so_customer_name' => $request->customer,
                    'order_type' => $so_list->order_type_1,
                ];

                DB::connection('mysql')->table('tabStock Entry')->insert($stock_entry_data);
                // $this->update_bin($new_id);
                // $this->create_stock_ledger_entry($new_id);
                // $this->create_gl_entry($new_id);
                return response()->json(['message' => 'Material Transfer no '.$new_id.' has been created.']);
            }

            } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }
     public function create_stock_ledger_entry($stock_entry){
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
                'stock_value' => $bin_qry->actual_qty * $bin_qry->valuation_rate,
                '_comments' => null,
                'incoming_rate' => 0,
                'voucher_detail_no' => $row->name,
                'stock_uom' => $row->stock_uom,
                'warehouse' => $row->s_warehouse,
                '_liked_by' => null,
                'company' => 'FUMACO Inc.',
                '_assign' => null,
                'item_code' => $row->item_code,
                'valuation_rate' => $bin_qry->valuation_rate,
                'project' => $stock_entry_qry->project,
                'voucher_no' => $row->parent,
                'outgoing_rate' => 0,
                'is_cancelled' => 'No',
                'qty_after_transaction' => $bin_qry->actual_qty,
                '_user_tags' => null,
                'batch_no' => $row->batch_no,
                'stock_value_difference' => ($row->qty * $row->valuation_rate) * -1,
                'posting_date' => $now->format('Y-m-d'),
            ];
            
            $bin_qry = DB::connection('mysql')->table('tabBin')->where('warehouse', $row->t_warehouse)
                ->where('item_code', $row->item_code)->first();
            
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
                'stock_value' => $bin_qry->actual_qty * $bin_qry->valuation_rate,
                '_comments' => null,
                'incoming_rate' => $row->basic_rate,
                'voucher_detail_no' => $row->name,
                'stock_uom' => $row->stock_uom,
                'warehouse' => $row->t_warehouse,
                '_liked_by' => null,
                'company' => 'FUMACO Inc.',
                '_assign' => null,
                'item_code' => $row->item_code,
                'valuation_rate' => $bin_qry->valuation_rate,
                'project' => $stock_entry_qry->project,
                'voucher_no' => $row->parent,
                'outgoing_rate' => 0,
                'is_cancelled' => 'No',
                'qty_after_transaction' => $bin_qry->actual_qty,
                '_user_tags' => null,
                'batch_no' => $row->batch_no,
                'stock_value_difference' => $row->qty * $row->valuation_rate,
                'posting_date' => $now->format('Y-m-d'),
            ];
        }

        $stock_ledger_entry = array_merge($s_data, $t_data);

        DB::connection('mysql')->table('tabStock Ledger Entry')->insert($stock_ledger_entry);
    }

    public function update_bin($stock_entry){
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
                
                $new_id = $new_id + 1;
                $new_id = str_pad($new_id, 7, '0', STR_PAD_LEFT);
                $id = 'BINM/'.$new_id;

                if (!$bin_qry) {
                    $bin = [
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
                        'warehouse' => $s_warehouse,
                        'stock_value' => $row->valuation_rate * $row->transfer_qty,
                        '_user_tags' => null,
                        'valuation_rate' => $row->valuation_rate,
                    ];

                    DB::connection('mysql')->table('tabBin')->insert($bin);
                }else{
                    $bin = [
                        'modified' => $now->toDateTimeString(),
                        'modified_by' => Auth::user()->email,
                        'actual_qty' => $bin_qry->actual_qty - abs($row->transfer_qty),
                        'stock_value' => $bin_qry->valuation_rate * $row->transfer_qty,
                        'valuation_rate' => $bin_qry->valuation_rate,
                    ];
    
                    DB::connection('mysql')->table('tabBin')->where('name', $bin_qry->name)->update($bin);
                }
                
            }

            if($row->t_warehouse){
                $bin_qry = DB::connection('mysql')->table('tabBin')->where('warehouse', $row->t_warehouse)
                    ->where('item_code', $row->item_code)->first();
                
                $new_id = $new_id + 1;
                $new_id = str_pad($new_id, 7, '0', STR_PAD_LEFT);
                $id = 'BINM/'.$new_id;

                if (!$bin_qry) {
                    $bin = [
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
                        'actual_qty' => $bin_qry->actual_qty - abs($row->transfer_qty),
                        'stock_value' => $bin_qry->valuation_rate * $row->transfer_qty,
                        'valuation_rate' => $bin_qry->valuation_rate,
                    ];
    
                    DB::connection('mysql')->table('tabBin')->where('name', $bin_qry->name)->update($bin);
                }
            }
        }
    }
    
    public function create_gl_entry($stock_entry){
        $now = Carbon::now();
        $stock_entry_qry = DB::connection('mysql')->table('tabStock Entry')->where('name', $stock_entry)->first();
        $stock_entry_detail = DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $stock_entry)->get();
        
        $latest_name = DB::connection('mysql')->table('tabGL Entry')->max('name');
        $latest_name_exploded = explode("L", $latest_name);
        $new_id = $latest_name_exploded[1] + 1;

        $id = [];
        $credit_data = [];
        $debit_data = [];

        foreach ($stock_entry_detail as $row) {
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
    }
   
    public function list_material_transfer(Request $request){
        $query2=$request->from;
        $query3= $request->end;
        $trans_lists=  DB::connection('mysql')->table('tabStock Entry as ste')
        ->join('tabStock Entry Detail as iste', 'iste.parent', 'ste.name')
        ->when($query2 != "", function ($query1) use($query2, $query3){
            return $query1->whereBetween(DB::raw('DATE_FORMAT(ste.creation, "%Y-%m-%d")'),[$query2,$query3]);
            })
        ->where('ste.title', 'Material Transfer')
        ->where('ste.so_customer_name', 'LIKE', '%'.$request->customer.'%')
        ->where('ste.project',   'LIKE', '%'.$request->project.'%')
        ->where('ste.sales_order_no', 'LIKE', '%'.$request->so.'%')
        ->Where('ste.item_status', 'LIKE', '%'.$request->status.'%')
        ->Where('ste.name', 'LIKE', '%'.$request->ste.'%')
        ->Where('iste.item_code', 'LIKE', '%'.$request->item_code.'%')
        ->orderBy('ste.modified', 'desc')
        ->distinct('ste.name')
        ->select('ste.*')
        ->get();
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
     
        // Create a new Laravel collection from the array data
        $itemCollection = collect($trans_lists);
     
        // Define how many items we want to be visible in each page
        $perPage = 10;
     
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
     
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
     
        // set url path for generted links
        $paginatedItems->setPath($request->url());
        $trans_list = $paginatedItems;

        $count=  collect($trans_lists)->count();
        // dd($trans_list);

        // dd($count);

        return view('inventory.tbl_material_transfer', compact('trans_list', 'count'));
    }

    public function get_material_tranfer($id){
        $transfer_list=  DB::connection('mysql')->table('tabStock Entry')->where('name', $id)->first();
        $transfer_item=  DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $id)->orderBy('idx', 'asc')->get();


        
        return view('inventory.tbl_material_transfer_view', compact('transfer_list', 'transfer_item'));

    }

    public function inventory_index_1(){
        $item_list = DB::connection('mysql')->table('tabItem as item')
            ->join('tabWarehouse as w', 'item.default_warehouse', 'w.name')
            ->where('w.disabled', 0)
            ->where('w.is_group', 0)
            ->where('w.company', 'FUMACO Inc.')
            ->where('w.department', 'Fabrication')
            ->whereIn('item.item_group', ['Raw Material'])
            ->select('item.name', 'item.description')
            ->orderBy('item.modified', 'desc')->get();

        $warehouse_list = DB::connection('mysql')->table('tabWarehouse')->where('disabled', 0)->where('is_group', 0)
            ->where('company', 'FUMACO Inc.')->where('department', 'Fabrication')->pluck('name');
        $customer=  DB::connection('mysql')->table('tabSales Order')->where('docstatus', 1)->where('per_delivered', '<', 100)
                ->where('company', 'FUMACO Inc.')->select('customer_name')->groupBy('customer_name')->get();

        $so_list = DB::connection('mysql')->table('tabSales Order')->where('docstatus', 1)->where('per_delivered', '<', 100)
                ->where('company', 'FUMACO Inc.')->orderBy('modified', 'desc')->get();
        $mreq_list = DB::connection('mysql')->table('tabMaterial Request')->where('docstatus', 1)
                ->where('company', 'FUMACO Inc.')->orderBy('modified', 'desc')->get();
        $project=  DB::connection('mysql')->table('tabSales Order')->where('docstatus', 1)->where('per_delivered', '<', 100)
                ->where('company', 'FUMACO Inc.')->select('project')->groupBy('project')->get();

        return view('inventory.index_1', compact('item_list','customer','warehouse_list', 'project','so_list', 'mreq_list'));
    }
    public function cancel_material_transfer(Request $request){
        try {
            $now = Carbon::now();

            DB::connection('mysql')->table('tabStock Entry')->where('name', $request->transfer_id)
                ->where('docstatus', 1)
                ->update(['docstatus' => 2, 'modified' => $now->toDateTimeString(), 'modified_by' => Auth::user()->email]);
            DB::connection('mysql')->table('tabStock Ledger Entry')->where('voucher_detail_no', $request->transfer_id)
                ->where('docstatus', 1)->delete();
            DB::connection('mysql')->table('tabGL Entry')->where('voucher_no', $request->transfer_id)
                ->where('docstatus', 1)->delete();
            


            return response()->json(['success' => 1, 'message' => 'Material Request for Transfer <b>' . $request->transfer_id . '</b> has been cancelled.']);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }
    public function confirmed_material_transfer(Request $request){
        try {
            $now = Carbon::now();

            DB::connection('mysql')->table('tabStock Entry')->where('name', $request->transfer_id_confirm)
                ->where('docstatus', 0)
                ->update(['docstatus' => 1, 'modified' => $now->toDateTimeString(), 'modified_by' => Auth::user()->email]);
            DB::connection('mysql')->table('tabStock Entry Detail')->where('parent', $request->transfer_id_confirm)
                ->where('docstatus', 0)
                ->update(['docstatus' => 1, 'modified' => $now->toDateTimeString(), 'modified_by' => Auth::user()->email]);
            $this->update_bin($request->transfer_id_confirm);
            $this->create_stock_ledger_entry($request->transfer_id_confirm);
            $this->create_gl_entry($request->transfer_id_confirm);


            return response()->json(['success' => 1, 'message' => 'Material Request for Transfer <b>' . $request->transfer_id . '</b> has been submitted.']);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }
    public function delete_material_transfer(Request $request){
        try {
            $now = Carbon::now();

            DB::connection('mysql')->table('tabStock Entry')->where('name', $request->transfer_id_delete)
                ->where('docstatus', 0)->where('item_status', 'For Checking')
                ->delete();
            DB::connection('mysql')->table('tabStock Entry Detail')->where('docstatus', 0)->where('parent', $request->transfer_id_delete) ->delete();


            return response()->json(['success' => 1, 'message' => 'Material Request for Transfer <b>' . $request->transfer_id . '</b> has been deleted.']);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function tbl_filter_material_transfer(Request $request, $from, $end){
        $trans_lists=  DB::connection('mysql')->table('tabStock Entry as ste')
        ->join('tabStock Entry Detail as iste', 'iste.parent', 'ste.name')
        ->whereBetween(DB::raw('DATE_FORMAT(ste.posting_date, "%Y-%m-%d")'),[$from,$end])
        ->where('ste.title', 'Material Transfer')
        ->when($request->customer, function($q) use ($request){
            $q->where('ste.so_customer_name', $request->customer);
        })
        ->when($request->project, function($q) use ($request){
            $q->where('ste.project', $request->project);
        })
        ->where('ste.sales_order_no', 'LIKE', '%'.$request->so.'%')
        ->Where('ste.item_status', 'LIKE', '%'.$request->status.'%')
        ->Where('ste.name', 'LIKE', '%'.$request->ste.'%')
        ->Where('iste.item_code', 'LIKE', '%'.$request->item_code.'%')
        ->orderBy('ste.modified', 'desc')
        ->distinct('ste.name')
        ->select('ste.*')
        ->get();
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
     
        // Create a new Laravel collection from the array data
        $itemCollection = collect($trans_lists);
     
        // Define how many items we want to be visible in each page
        $perPage = 10;
     
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
     
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
     
        // set url path for generted links
        $paginatedItems->setPath($request->url());
        $trans_list = $paginatedItems;

        $count=  collect($trans_lists)->count();
        return view('inventory.tbl_material_transfer', compact('trans_list', 'count'));

    }

    public function get_pending_inventory_transactions(){
        $start = Carbon::now()->startOfDay()->toDateTimeString();
        $end = Carbon::now()->endOfDay()->toDateTimeString();

        $material_transfer_q = DB::connection('mysql')->table('tabStock Entry as ste')
            ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
            ->where('purpose', 'Material Transfer')
            ->whereBetween('ste.creation', [$start, $end])
            // ->where('mes_operation', 'Fabrication')
            ->select('ste.name', 'sted.item_code', 'ste.docstatus')
            ->get();

        $count_material_transfer = collect($material_transfer_q)->unique('name')->count();
        $count_material_transfer_items = collect($material_transfer_q)->count();

        $material_request_q = DB::connection('mysql')->table('tabMaterial Request as mr')
            ->join('tabMaterial Request Item as mri', 'mr.name', 'mri.parent')
            ->where('mr.material_request_type', 'Purchase')
            ->whereBetween('mr.creation', [$start, $end])
            ->where('mr.docstatus', 1)
            // ->where('mes_operation', 'Fabrication')
            ->select('mr.name', 'mri.item_code')
            ->get();

        $count_material_request = collect($material_request_q)->unique('name')->count();
        $count_material_request_items = collect($material_request_q)->count();

        $count_pending_material_transfer = collect($material_transfer_q)->where('docstatus', 0)->unique('name')->count();
        $count_pending_material_transfer_items = collect($material_transfer_q)->where('docstatus', 0)->count();

        $pending_withdrawal_q = DB::connection('mysql')->table('tabStock Entry as ste')
            ->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
            ->where('purpose', 'Material Transfer for Manufacture')
            ->where('ste.item_status', 'For Checking')
            ->where('ste.docstatus', 0)
            ->whereBetween('ste.creation', [$start, $end])
            // ->where('mes_operation', 'Fabrication')
            ->select('ste.name', 'sted.item_code', 'ste.docstatus', 'sted.status')
            ->get();

        $count_pending_withdrawal = collect($pending_withdrawal_q)->unique('name')->count();
        $count_pending_withdrawal_items = collect($pending_withdrawal_q)->where('status', 'For Checking')->count();

        return [
            'material_transfer' => number_format($count_material_transfer),
            'material_transfer_items' => number_format($count_material_transfer_items),
            'material_request' => number_format($count_material_request),
            'material_request_items' => number_format($count_material_request_items),
            'pending_issue' => number_format($count_pending_withdrawal),
            'pending_issue_items' => number_format($count_pending_withdrawal_items),
            'pending_receive' => number_format($count_pending_material_transfer),
            'pending_receive_items' => number_format($count_pending_material_transfer_items),
        ];
    }

    public function get_inspection_logs(){
        $start = Carbon::now()->startOfDay()->toDateTimeString();
        $end = Carbon::now()->endOfDay()->toDateTimeString();

        $spotwelding_qa_logs = DB::connection('mysql_mes')->table('production_order')
            ->join('job_ticket', 'job_ticket.production_order', 'production_order.production_order')
            ->join('spotwelding_qty', 'spotwelding_qty.job_ticket_id', 'job_ticket.job_ticket_id')
            ->join('quality_inspection', 'quality_inspection.reference_id', 'job_ticket.job_ticket_id')
            ->where('job_ticket.workstation', 'Spotwelding')->where('quality_inspection.reference_type', 'Spotwelding')
            ->whereIn('quality_inspection.status', ['QC Passed', 'QC Failed'])
            ->select(DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process'), 'production_order.item_code', 'production_order.description', 'production_order.production_order', 'job_ticket.workstation', 'spotwelding_qty.good', 'spotwelding_qty.reject', 'quality_inspection.actual_qty_checked', 'quality_inspection.status', 'qa_inspection_type', 'qa_inspection_date', 'qa_staff_id', 'operator_name', 'machine_code');

        $logs = DB::connection('mysql_mes')->table('production_order')
            ->join('job_ticket', 'job_ticket.production_order', 'production_order.production_order')
            ->join('time_logs', 'time_logs.job_ticket_id', 'job_ticket.job_ticket_id')
            ->join('quality_inspection', 'quality_inspection.reference_id', 'time_logs.time_log_id')
            ->where('job_ticket.workstation', '!=', 'Spotwelding')->where('quality_inspection.reference_type', 'Time Logs')
            ->whereIn('quality_inspection.status', ['QC Passed', 'QC Failed'])
            ->whereBetween('qa_inspection_date', [$start, $end])
            ->select(DB::raw('(SELECT process_name FROM process WHERE process_id = job_ticket.process_id) AS process'), 'production_order.item_code', 'production_order.description', 'production_order.production_order', 'job_ticket.workstation', 'time_logs.good', 'time_logs.reject', 'quality_inspection.actual_qty_checked', 'quality_inspection.status', 'qa_inspection_type', 'qa_inspection_date', 'qa_staff_id', 'operator_name', 'machine_code')->union($spotwelding_qa_logs)
            ->get();

        $inspection_logs = [];
        foreach($logs as $row){
            $qa_staff_name = DB::connection('mysql_essex')->table('users')->where('user_id', $row->qa_staff_id)->first()->employee_name;
            $inspection_logs[] = [
                'production_order' => $row->production_order,
                'item_code' => $row->item_code,
                'workstation' => $row->workstation,
                'process' => $row->process,
                'good' => $row->good,
                'reject' => $row->reject,
                'actual_qty_checked' => $row->actual_qty_checked,
                'status' => $row->status,
                'machine_code' => $row->machine_code,
                'operator_name' => $row->operator_name,
                'qa_staff_name' => $qa_staff_name,
                'qa_inspection_date' => $row->qa_inspection_date,
            ];
        }

        return view('tables.tbl_inspection_logs', compact('inspection_logs'));
    }

    public function get_scrap_per_material($material_type){
        $usable_scrap_in_cubic_mm = $this->get_total_usable_scrap_per_material_type($material_type);
        $unusable_scrap_in_kg = $this->get_total_unusable_scrap_per_material_type($material_type);

        $uom_cubic_mm = DB::connection('mysql_mes')->table('uom')->where('uom_name', 'like', '%cubic m%')->first();
        if(!$uom_cubic_mm){
            return [
                'usable_scrap_in_cubic_mm' => number_format((float)$usable_scrap_in_cubic_mm, 2, '.', ''),
                'usable_scrap_in_kg' => number_format((float)0, 2, '.', ''),
                'unusable_scrap' => number_format((float)$unusable_scrap_in_kg, 2, '.', ''),
            ];
        }

        $uom_cubic_kg = DB::connection('mysql_mes')->table('uom')->where('uom_name', 'like', '%kilogram%')->first();
        if(!$uom_cubic_kg){
            return [
                'usable_scrap_in_cubic_mm' => number_format((float)$usable_scrap_in_cubic_mm, 2, '.', ''),
                'usable_scrap_in_kg' => number_format((float)0, 2, '.', ''),
                'unusable_scrap' => number_format((float)$unusable_scrap_in_kg, 2, '.', ''),
            ];
        }

        $allowed_materials = ['CRS', 'ALUMINUM', 'DIFFUSER'];
        if(!in_array(strtoupper($material_type), $allowed_materials)){
            return [
                'usable_scrap_in_cubic_mm' => number_format((float)$usable_scrap_in_cubic_mm, 2, '.', ''),
                'usable_scrap_in_kg' => number_format((float)0, 2, '.', ''),
                'unusable_scrap' => number_format((float)$unusable_scrap_in_kg, 2, '.', ''),
            ];
        }

        $conversion_id = DB::connection('mysql_mes')->table('uom_conversion')
            ->whereIn('uom_id', [$uom_cubic_mm->uom_id, $uom_cubic_kg->uom_id])
            ->where('material', $material_type)
            ->select(DB::raw('COUNT(uom_id) as count'), 'uom_conversion_id')
            ->groupBy('uom_conversion_id')->first();
        
        if (!$conversion_id) {
            return [
                'usable_scrap_in_cubic_mm' => number_format((float)$usable_scrap_in_cubic_mm, 2, '.', ''),
                'usable_scrap_in_kg' => number_format((float)0, 2, '.', ''),
                'unusable_scrap' => number_format((float)$unusable_scrap_in_kg, 2, '.', ''),
            ];
        }

        $uom_2_conversion_factor = DB::connection('mysql_mes')->table('uom_conversion')
            ->where('uom_id', $uom_cubic_kg->uom_id)
            ->where('material', $material_type)
            ->where('uom_conversion_id', $conversion_id->uom_conversion_id)
            ->sum('conversion_factor');

        $usable_scrap_in_kg = $usable_scrap_in_cubic_mm * $uom_2_conversion_factor;
        
        return [
            'usable_scrap_in_cubic_mm' => number_format((float)$usable_scrap_in_cubic_mm, 2, '.', ''),
            'usable_scrap_in_kg' => number_format((float)$usable_scrap_in_kg, 2, '.', ''),
            'unusable_scrap' => number_format((float)$unusable_scrap_in_kg, 2, '.', ''),
        ];
    }

    public function get_total_usable_scrap_per_material_type($material_type){
        return DB::connection('mysql_mes')->table('usable_scrap')
            ->join('scrap', 'scrap.scrap_id', 'usable_scrap.scrap_id')
            ->where('material', strtolower($material_type))
            ->where('usable_scrap_qty', '>', 0)->sum('usable_scrap_qty');
    }

    public function get_total_unusable_scrap_per_material_type($material_type){
        return DB::connection('mysql_mes')->table('scrap')
            ->where('material', strtolower($material_type))
            ->where('scrap_qty', '>', 0)->sum('scrap_qty');
    }

    public function tbl_filter_out_of_stock(Request $request){
            $item_list = DB::connection('mysql')->table('tabItem as item')
            ->join('tabWarehouse as w', 'item.default_warehouse', 'w.name')
            ->where('w.disabled', 0)
            ->where('w.is_group', 0)
            ->where('w.company', 'FUMACO Inc.')
            // ->where('stock_uom', 'Piece(s)')
            ->where('has_variants',"!=", 1)
            ->where('w.department', 'Fabrication')
            ->whereIn('item.item_group', ['Raw Material'])
            ->where('item.item_classification', $request->material)
            ->select('item.name', 'item.item_name')
            ->orderBy('item.modified', 'desc')->get();
            $data=[];
            foreach ($item_list as $row) {
                $q = DB::connection('mysql_mes')->table('item_specification')
                ->where('item_code', $row->name)
                ->when($request->length, function($q) use ($request){
                    $q->where('item_specification.length', $request->length);
                })
                ->when($request->width, function($q) use ($request){
                    $q->where('item_specification.width', $request->width);
                })
                ->when($request->thickness, function($q) use ($request){
                    $q->where('item_specification.thickness', $request->thickness);
                })
                ->when($request->q, function($q) use ($request){
                    $q->where(function($r) use ($request){
                        $r->where('item_specification.material', 'LIKE', '%' . $request->q . '%')
                            ->orWhere('item_specification.length', 'LIKE', '%' . $request->q . '%')
                            ->orWhere('item_specification.width', 'LIKE', '%' . $request->q . '%')
                            ->orWhere('item_specification.thickness', 'LIKE', '%' . $request->q . '%');
                    });
                })->get();
                foreach($q as $rows){
                    $actual= DB::connection('mysql')->table('tabBin')->where('item_code', $row->name)->where('warehouse', 'like',  '%Fabrication - FI%')->select('actual_qty')->first();
                    if (empty($actual)) {
                       $data[]=[
        
                        'item_code' => $rows->item_code,
                        'description' => $row->item_name
                    ];
                    }
                }
    
            }

            return view('inventory.tbl_outofstock_rawmaterial', compact('data'));
        }
        public function raw_material_monitoring_data_diff(){
            $item_list = DB::connection('mysql')->table('tabItem as item')
            ->join('tabWarehouse as w', 'item.default_warehouse', 'w.name')
            ->where('w.disabled', 0)
            ->where('w.is_group', 0)
            ->where('w.company', 'FUMACO Inc.')
            ->where('item.description', 'LIKE', '%Acrylic Diffuser%')
            ->where('stock_uom', 'Piece(s)')
            ->where('has_variants',"!=", 1)
            ->where('w.department', 'Fabrication')
            ->whereIn('item.item_group', ['Raw Material'])
            ->whereIn('item.item_classification', ['DI - Diffuser'])
            ->select('item.name', 'item.item_name')
            ->orderBy('item.modified', 'desc')->get();
            $data=[];
            foreach ($item_list as $row) {
                $min_level= DB::connection('mysql')->table('tabItem Reorder')->where('parent', $row->name)->where('material_request_type', 'Transfer')->select('warehouse_reorder_qty','warehouse_reorder_level')->first();
                $actual= DB::connection('mysql')->table('tabBin')->where('item_code', $row->name)->where('warehouse', 'like',  '%Fabrication - FI%')->select('actual_qty')->first();
                $prod = DB::connection('mysql_mes')->table('production_order as pro')
                ->where('pro.status', 'Not Started')
                ->distinct()
                ->pluck('production_order');
                
                $planned= DB::connection('mysql')->table('tabProduction Order Item as pri')
                    ->where('pri.source_warehouse', "Fabrication  - FI")
                    ->whereIn('pri.parent', $prod)
                    ->where('pri.item_code',$row->name)
                    ->sum('required_qty');
                $background_planned='#ff8300  ';
                $background_minimum='#00838F';
                if ((empty($actual->actual_qty)? 0 : $actual->actual_qty) == 0) {
                    $status="changecolor";
                    $minimum= 0;             
                    $background_actual='#558B2F';
                    $actual_bar=empty($actual->actual_qty)? 0 : $actual->actual_qty;
    
                }elseif ((empty($actual->actual_qty)? 0 : $actual->actual_qty) <= (empty($min_level->warehouse_reorder_level)? 0:$min_level->warehouse_reorder_level)) {
                    $status="changecolor";
                    $minimum= 0;             
                    $background_actual='red';
                    $actual_bar=empty($actual->actual_qty)? 0 : $actual->actual_qty;
    
                    
                }else{
                    $status="nochange";
                    $background_actual='#558B2F';
                    $actual_bar=(empty($actual->actual_qty)? 0 : $actual->actual_qty)-(empty($min_level->warehouse_reorder_level)? 0:$min_level->warehouse_reorder_level); 
                    $minimum= empty($min_level->warehouse_reorder_level)? 0:$min_level->warehouse_reorder_level;
                }
               
                $planneds=empty($actual->actual_qty)? 0: $actual->actual_qty;
                $sheets = 0;
                if (!empty($actual) && $actual->actual_qty > 0) {
                   $data[]=[
                    'item_code' => $row->name,
                    'decsription' => $row->item_name,
                    'actual_bar' => round($actual_bar,2),
                    'status' => $status, 
                    'actual_qty' => round(empty($actual->actual_qty)? 0 : $actual->actual_qty,2),
                    'minimum' => round($minimum,2),
                    'sheets'=> round($sheets,2),
                    'c_actual' => $background_actual,
                    'c_planned'=> $background_planned,
                    'c_minimum'=>$background_minimum,
                    'minimum_label' => round(empty($min_level->warehouse_reorder_level)? 0:$min_level->warehouse_reorder_level,2),
                    'planned' => round(empty($planned)? 0: $planned,2)
                ];
                }
    
            }
            $chart_data=[
               'chart_data' => $data ];           
    
            return $chart_data;
        }
        public function raw_material_monitoring_data_crs(){
            $item_list = DB::connection('mysql')->table('tabItem as item')
            ->join('tabWarehouse as w', 'item.default_warehouse', 'w.name')
            ->where('w.disabled', 0)
            ->where('w.is_group', 0)
            ->where('w.company', 'FUMACO Inc.')
            ->where('has_variants',"!=", 1)
            ->where('w.department', 'Fabrication')
            ->whereIn('item.item_group', ['Raw Material'])
            ->whereIn('item.item_classification', ['CS - Crs Steel Coil'])
            ->select('item.name', 'item.item_name')
            ->orderBy('item.modified', 'desc')->get();
            $data=[];
            foreach ($item_list as $row) {
                $min_level= DB::connection('mysql')->table('tabItem Reorder')->where('parent', $row->name)->where('material_request_type', 'Transfer')->select('warehouse_reorder_qty','warehouse_reorder_level')->first();
                $actual= DB::connection('mysql')->table('tabBin')->where('item_code', $row->name)->where('warehouse', 'like',  '%Fabrication - FI%')->select('actual_qty')->first();
                $prod = DB::connection('mysql_mes')->table('production_order as pro')
                ->where('pro.status', 'Not Started')
                ->distinct()
                ->pluck('production_order');
                
                $planned= DB::connection('mysql')->table('tabProduction Order Item as pri')
                    ->where('pri.source_warehouse', "Fabrication  - FI")
                    ->whereIn('pri.parent', $prod)
                    ->where('pri.item_code',$row->name)
                    ->sum('required_qty');
                $background_planned='#ff8300  ';
                $background_minimum='#00838F';
                if ((empty($actual->actual_qty)? 0 : $actual->actual_qty) == 0) {
                    $status="changecolor";
                    $minimum= 0;             
                    $background_actual='#558B2F';
                    $actual_bar=empty($actual->actual_qty)? 0 : $actual->actual_qty;
    
                }elseif ((empty($actual->actual_qty)? 0 : $actual->actual_qty) <= (empty($min_level->warehouse_reorder_level)? 0:$min_level->warehouse_reorder_level)) {
                    $status="changecolor";
                    $minimum= 0;             
                    $background_actual='red';
                    $actual_bar=empty($actual->actual_qty)? 0 : $actual->actual_qty;
    
                    
                }else{
                    $status="nochange";
                    $background_actual='#558B2F';
                    $actual_bar=(empty($actual->actual_qty)? 0 : $actual->actual_qty)-(empty($min_level->warehouse_reorder_level)? 0:$min_level->warehouse_reorder_level); 
                    $minimum= empty($min_level->warehouse_reorder_level)? 0:$min_level->warehouse_reorder_level;
                }
               
                $planneds=empty($actual->actual_qty)? 0: $actual->actual_qty;
                $sheets = $this->get_no_of_sheets($row->name, $planneds);
    
                if (!empty($actual) && $actual->actual_qty > 0) {
                   $data[]=[
                    'item_code' => $row->name,
                    'decsription' => $row->item_name,
                    'actual_bar' => round($actual_bar,2),
                    'status' => $status, 
                    'actual_qty' => round(empty($actual->actual_qty)? 0 : $actual->actual_qty,2),
                    'minimum' => round($minimum,2),
                    'sheets'=> round($sheets,2),
                    'c_actual' => $background_actual,
                    'c_planned'=> $background_planned,
                    'c_minimum'=>$background_minimum,
                    'minimum_label' => round(empty($min_level->warehouse_reorder_level)? 0:$min_level->warehouse_reorder_level,2),
                    'planned' => round(empty($planned)? 0: $planned,2)
                ];
                }
                
            }
            $chart_data=[
               'chart_data' => $data ];           
    
            return $chart_data;
        }
        public function raw_material_monitoring_data_alum(){
            $item_list = DB::connection('mysql')->table('tabItem as item')
            ->join('tabWarehouse as w', 'item.default_warehouse', 'w.name')
            ->where('w.disabled', 0)
            ->where('w.is_group', 0)
            ->where('w.company', 'FUMACO Inc.')
            ->where('has_variants',"!=", 1)
            ->where('w.department', 'Fabrication')
            ->whereIn('item.item_group', ['Raw Material'])
            ->whereIn('item.item_classification', ['AS - Aluminum Sheets'])
            ->select('item.name', 'item.item_name')
            ->orderBy('item.modified', 'desc')->get();
            $data=[];
            foreach ($item_list as $row) {
                $min_level= DB::connection('mysql')->table('tabItem Reorder')->where('parent', $row->name)->where('material_request_type', 'Transfer')->select('warehouse_reorder_qty','warehouse_reorder_level')->first();
                $actual= DB::connection('mysql')->table('tabBin')->where('item_code', $row->name)->where('warehouse', 'like',  '%Fabrication - FI%')->select('actual_qty')->first();
                $prod = DB::connection('mysql_mes')->table('production_order as pro')
                ->where('pro.status', 'Not Started')
                ->distinct()
                ->pluck('production_order');
                
                $planned= DB::connection('mysql')->table('tabProduction Order Item as pri')
                    ->where('pri.source_warehouse', "Fabrication  - FI")
                    ->whereIn('pri.parent', $prod)
                    ->where('pri.item_code',$row->name)
                    ->sum('required_qty');
                $background_planned='#ff8300  ';
                $background_minimum='#00838F';
                if ((empty($actual->actual_qty)? 0 : $actual->actual_qty) == 0) {
                    $status="changecolor";
                    $minimum= 0;             
                    $background_actual='#558B2F';
                    $actual_bar=empty($actual->actual_qty)? 0 : $actual->actual_qty;
    
                }elseif ((empty($actual->actual_qty)? 0 : $actual->actual_qty) <= (empty($min_level->warehouse_reorder_level)? 0:$min_level->warehouse_reorder_level)) {
                    $status="changecolor";
                    $minimum= 0;             
                    $background_actual='red';
                    $actual_bar=empty($actual->actual_qty)? 0 : $actual->actual_qty;
    
                    
                }else{
                    $status="nochange";
                    $background_actual='#558B2F';
                    $actual_bar=(empty($actual->actual_qty)? 0 : $actual->actual_qty)-(empty($min_level->warehouse_reorder_level)? 0:$min_level->warehouse_reorder_level); 
                    $minimum= empty($min_level->warehouse_reorder_level)? 0:$min_level->warehouse_reorder_level;
                }
               
                // $minimum=0;
                $planneds=empty($actual->actual_qty)? 0: $actual->actual_qty;
                $sheets = 0;
                if (!empty($actual) && $actual->actual_qty > 0) {
                   $data[]=[
                    'item_code' => $row->name,
                    'decsription' => $row->item_name,
                    'actual_bar' => round($actual_bar,2),
                    'status' => $status, 
                    'actual_qty' => round(empty($actual->actual_qty)? 0 : $actual->actual_qty,2),
                    'minimum' => round($minimum,2),
                    'sheets'=> round($sheets,2),
                    'c_actual' => $background_actual,
                    'c_planned'=> $background_planned,
                    'c_minimum'=>$background_minimum,
                    'minimum_label' => round(empty($min_level->warehouse_reorder_level)? 0:$min_level->warehouse_reorder_level,2),
                    'planned' => round(empty($planned)? 0: $planned,2)
                ];
                }
    
            }
            $chart_data=[
               'chart_data' => $data ];           
    
            return $chart_data;
        }

        public function get_no_of_sheets($item_code, $qty){
            $conversion = DB::connection('mysql')->table('tabUOM Conversion Detail')->where('parent', "CS00023")->where('uom', 'Sheet(s)')->first();
            if ($conversion) {
                return ($qty / $conversion->conversion_factor);
            }
        }
        public function alum_out_of_stock(Request $request){
        $item_list = DB::connection('mysql')->table('tabItem as item')
        ->join('tabWarehouse as w', 'item.default_warehouse', 'w.name')
        ->where('w.disabled', 0)
        ->where('w.is_group', 0)
        ->where('w.company', 'FUMACO Inc.')
        // ->where('stock_uom', 'Piece(s)')
        ->where('has_variants',"!=", 1)
        ->where('w.department', 'Fabrication')
        ->whereIn('item.item_group', ['Raw Material'])
        ->where('item.item_classification', $request->id)
        ->select('item.name', 'item.item_name', 'item.item_classification', 'item.default_warehouse')
        ->orderBy('item.modified', 'desc')->get();
        $data=[];
        foreach ($item_list as $row) {
        
        $actual= DB::connection('mysql')->table('tabBin')->where('item_code', $row->name)->where('warehouse', 'like',  '%Fabrication - FI%')->select('actual_qty')->first();
        $min_level= DB::connection('mysql')->table('tabItem Reorder')->where('parent', $row->name)->where('material_request_type', 'Transfer')->select('warehouse_reorder_qty','warehouse_reorder_level')->first();
            if (empty($actual)) {
               $data[]=[

                'item_code' => $row->name,
                'description' => $row->item_name,
                'item_class' => $row->item_classification,
                'default_warehouse' => $row->default_warehouse,
                'minimum' => empty($min_level->warehouse_reorder_level)? 0:$min_level->warehouse_reorder_level

            ];
            }

        }

        return view('inventory.tbl_outofstock_rawmaterial', compact('data'));
    }


   

    public function insert_scrap_job_ticket(Request $request){
        try {
            $production_order = 'SC-' . $request->scrap_id;
            $operator = DB::connection('mysql_essex')->table('users')->where('user_id', $request->operator_id)->first();
            if (!$operator) {
                return response()->json(['success' => 0, 'message' => 'Operator not found.']);
            }

            $in_progress_operator_machine = DB::connection('mysql_mes')->table('time_logs')
                ->whereNotNull('operator_id')
                ->where('operator_id', '!=', $request->operator_id)
                ->where('machine_code', $request->machine_code)
                ->where('status', 'In Progress')->exists();
        
            if ($in_progress_operator_machine) {
                return response()->json(['success' => 0, 'message' => "Machine is in use by another operator."]);
            }

            $operator_in_progress_task = DB::connection('mysql_mes')->table('job_ticket')
                ->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
                ->where('job_ticket.production_order', '!=', $production_order)
                ->where('time_logs.operator_id', $request->operator_id)
                ->where('time_logs.status', 'In Progress')->first();

            $operator_in_progress_spotwelding = DB::connection('mysql_mes')->table('job_ticket')
                ->join('spotwelding_qty', 'job_ticket.job_ticket_id', 'spotwelding_qty.job_ticket_id')
                ->where('job_ticket.production_order', '!=', $production_order)
                ->where('spotwelding_qty.operator_id', $request->operator_id)
                ->where('spotwelding_qty.status', 'In Progress')->first();

            if ($operator_in_progress_task) {
                return response()->json(['success' => 0, 'message' => "Operator has in-progress task. " . $operator_in_progress_task->production_order]);
            }

            if ($operator_in_progress_spotwelding) {
                return response()->json(['success' => 0, 'message' => "Operator has in-progress task. " . $operator_in_progress_spotwelding->production_order]);
            }

            // production order = prefix 'SC' + scrap_id
            $values = [
                'production_order' => $production_order,
                'workstation' => $request->workstation,
                'process_id' => $request->process_id,
                'idx' => 1,
                'sequence' => 0,
                'created_by' => $operator->employee_name,
                'bom_operation_id' => '-'
            ];

            $prod_in_progress = DB::connection('mysql_mes')->table('job_ticket')
                ->join('time_logs', 'job_ticket.job_ticket_id', 'time_logs.job_ticket_id')
                ->where('job_ticket.production_order', $production_order)
                ->where('time_logs.operator_id', $request->operator_id)
                ->where('time_logs.status', 'In Progress')->exists();

            if(!$prod_in_progress){
                DB::connection('mysql_mes')->table('job_ticket')->insert($values);
            }

            $values = array_merge($values, ['machine_code' => $request->machine_code, 'operator_id' => $request->operator_id]);

            return response()->json(['success' => 1, 'message' => 'Task created.', 'details' => $values]);
            
        } catch (Exception $e) {
            return response()->json(["success" => 0, "message" => $e->getMessage()]);
        }
    }

    public function submit_uom_conversion(Request $request){
        if ($request->uom[0] == $request->uom[1]) {
            return response()->json(['success' => 0, 'message' => 'UoM cannot be the same.']);
        }

        if ($request->conversion_factor[0] <= 0 || $request->conversion_factor[1] <= 0) {
            return response()->json(['success' => 0, 'message' => 'Conversion must greater than 0.']);
        }

        if ($request->conversion_id <= 0) {
            $uom_conversion_ids = DB::connection('mysql_mes')->table('uom_conversion')
                ->whereIn('uom_id', $request->uom)->where('material', $request->material_type)
                ->distinct()->pluck('uom_conversion_id');

            foreach ($uom_conversion_ids as $id) {
                $count_uom = DB::connection('mysql_mes')->table('uom_conversion')
                    ->where('uom_conversion_id', $id)->whereIn('uom_id', $request->uom)
                    ->count();

                if ($count_uom > 1) {
                    return response()->json(['success' => 0, 'message' => 'UoM Conversion already exists.']);
                }
            }

            $max_conversion_id = DB::connection('mysql_mes')->table('uom_conversion')->max('uom_conversion_id');
            $values = [];
            foreach ($request->uom as $i => $uom_id) {

                $values[] = [
                    'material' => $request->material_type,
                    'uom_conversion_id' => $max_conversion_id + 1,
                    'uom_id' => $uom_id,
                    'conversion_factor' => $request->conversion_factor[$i],
                ];
            }

            DB::connection('mysql_mes')->table('uom_conversion')->insert($values);

            return response()->json(['success' => 1, 'message' => 'UoM Conversion added.']);
        }

        if ($request->conversion_id > 0) {
            foreach ($request->id as $i => $id) {
                $values = [
                    'material' => $request->material_type,
                    'uom_id' => $request->uom[$i],
                    'conversion_factor' => $request->conversion_factor[$i],
                ];
                
                DB::connection('mysql_mes')->table('uom_conversion')->where('id', $id)->update($values);
            }

            return response()->json(['success' => 1, 'message' => 'UoM Conversion updated.']);
        }
    }

    public function get_uom_conversion_list(Request $request){
        $uom_conversion = DB::connection('mysql_mes')->table('uom_conversion')
            ->distinct()->pluck('uom_conversion_id');

        $list = [];
        foreach ($uom_conversion as $id) {
            $uom_list = DB::connection('mysql_mes')->table('uom_conversion')
                ->join('uom', 'uom_conversion.uom_id', 'uom.uom_id')
                ->where('uom_conversion_id', $id)->get();

            $list[] = [
                'uom_conversion_id' => $id,
                'uom_list' => $uom_list
            ];
        }

        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $itemCollection = collect($list);
        // Define how many items we want to be visible in each page
        $perPage = 10;
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        // Create our paginator and pass it to the view
        $paginatedItems = new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
        // set url path for generted links
        $paginatedItems->setPath($request->url());
        $list = $paginatedItems;

        return view('tables.tbl_uom_conversion', compact('list'));
    }

    public function delete_uom_conversion(Request $request){
        if($request->uom_conversion_id){
            DB::connection('mysql_mes')->table('uom_conversion')
                ->where('uom_conversion_id', $request->uom_conversion_id)->delete();

            return response()->json(['success' => 1, 'message' => 'UoM Conversion deleted.']);
        }
    }
    public function get_inventory_transaction_history_painting(Request $request){  
        $data = DB::connection('mysql_mes')->table('inventory_transaction as it')
            ->join('operation', 'operation.operation_id', 'it.operation_id')
           ->where('operation_name', "like", "%Painting%")
            ->when($request->entry_type, function($q) use ($request){
                $q->where('it.entry_type', $request->entry_type);
            })
            ->when($request->q, function($q) use ($request){
                $q->where(function($r) use ($request){
                        $r->orWhere('it.item_code', 'LIKE', '%'.$request->q.'%')
                        ->orWhere('it.last_modified_by', 'LIKE', '%'.$request->q.'%')
                        ->orWhere('it.adjusted_qty', 'LIKE', '%'.$request->q.'%')
                        ->orWhere('it.previous_qty', 'LIKE', '%'.$request->q.'%')
                        ->orWhere('it.entry_type', 'LIKE', '%'.$request->q.'%')
                        ->orWhere('it.transaction_no', 'LIKE', '%'.$request->q.'%');
                });
            })
            ->orderBy('it.transaction_no', 'desc')->paginate(5);

        return view('inventory.tbl_inventory_transactions_painting', compact('data'));
    }

    public function tbl_material_request_list(Request $request, $operation){
        
        $data=[];
        if($operation =="Painting"){
            $purchase_lists= DB::connection('mysql')->table('tabMaterial Request as mt')
            ->join('tabMaterial Request Item as imt', 'imt.parent', 'mt.name')
            ->where('mt.mes_operation', $operation)
            ->when($request->q, function ($query) use ($request) {
                return $query->where(function($q) use ($request) {
                $q->where('mt.customer_name', 'LIKE', '%'.$request->q.'%')
                ->orwhere('mt.project',   'LIKE', '%'.$request->q.'%')
                ->orwhere('mt.sales_order', 'LIKE', '%'.$request->q.'%')
                ->orWhere('mt.status', 'LIKE', '%'.$request->q.'%')
                ->orWhere('imt.item_code', 'LIKE', '%'.$request->q.'%');
                });
            })
            ->when($request->daterange, function ($query) use ($request) {
                $str = explode(' - ',$request->daterange);
               
                    $query->whereBetween(DB::raw('DATE_FORMAT(mt.schedule_date, "%Y-%m-%d")'), [$str[0],$str[1]]);
               
                return $query;
            })
            ->when($request->customer, function ($query) use ($request) {
                    $query->where('mt.customer_name', 'LIKE', '%'.$request->customer.'%');

                return $query;
            })
            ->when($request->project, function ($query) use ($request) {
                    $query->where('mt.project', 'LIKE', '%'.$request->project.'%');

                return $query;
            })
            ->when($request->sales_order, function ($query) use ($request) {
                    $query->where('mt.sales_order', 'LIKE', '%'.$request->sales_order.'%');

                return $query;
            })
            ->when($request->status, function ($query) use ($request) {
                    $query->where('mt.status', 'LIKE', '%'.$request->status.'%');

                return $query;
            })
            ->when($request->item_code, function ($query) use ($request) {
                    $query->where('imt.item_code', 'LIKE', '%'.$request->item_code.'%');

                return $query;
            })
            ->where('mt.docstatus','!=',0 )->orderBy('mt.modified', 'desc')
            ->distinct('mt.name')
            ->select('mt.*')
            ->get();

           
        }else{
            $purchase_lists= DB::connection('mysql')->table('tabMaterial Request as mt')
            ->join('tabMaterial Request Item as imt', 'imt.parent', 'mt.name')
            ->where('mt.mes_operation','!=', 'Painting')
            ->when($request->q, function ($query) use ($request) {
                return $query->where(function($q) use ($request) {
                $q->where('mt.customer_name', 'LIKE', '%'.$request->q.'%')
                ->orwhere('mt.project',   'LIKE', '%'.$request->q.'%')
                ->orwhere('mt.sales_order', 'LIKE', '%'.$request->q.'%')
                ->orWhere('mt.status', 'LIKE', '%'.$request->q.'%')
                ->orWhere('imt.item_code', 'LIKE', '%'.$request->q.'%');
                });
            })
            ->when($request->daterange, function ($query) use ($request) {
                $str = explode(' - ',$request->daterange);
               
                    $query->whereBetween(DB::raw('DATE_FORMAT(mt.schedule_date, "%Y-%m-%d")'), [$str[0],$str[1]]);
               
                return $query;
            })
            ->when($request->customer, function ($query) use ($request) {
                    $query->where('mt.customer_name', 'LIKE', '%'.$request->customer.'%');

                return $query;
            })
            ->when($request->project, function ($query) use ($request) {
                    $query->where('mt.project', 'LIKE', '%'.$request->project.'%');

                return $query;
            })
            ->when($request->sales_order, function ($query) use ($request) {
                    $query->where('mt.sales_order', 'LIKE', '%'.$request->sales_order.'%');

                return $query;
            })
            ->when($request->status, function ($query) use ($request) {
                    $query->where('mt.status', 'LIKE', '%'.$request->status.'%');

                return $query;
            })
            ->when($request->item_code, function ($query) use ($request) {
                    $query->where('imt.item_code', 'LIKE', '%'.$request->item_code.'%');

                return $query;
            })
            ->where('mt.docstatus','!=',0 )->orderBy('mt.modified', 'desc')
            ->distinct('mt.name')
            ->select('mt.*')
            ->get();
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
     
        // Create a new Laravel collection from the array data
        $itemCollection = collect($purchase_lists);
     
        // Define ho w many items we want to be visible in each page
        $perPage = 10;
     
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
     
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
     
        // set url path for generted links
        $paginatedItems->setPath($request->url());
        $purchase_list = $paginatedItems;

        $count=  collect($purchase_lists)->count();
        return view('inventory.material_request.tbl_material_request_purchase_painting', compact('purchase_list', 'count'));
            
    }
    
    public function generate_material_request(Request $request){
        DB::connection('mysql')->beginTransaction();
        try {
            $now = Carbon::now();
            if (count($request->item_code) <= 0) {
                return response()->json(['success' => 0, 'message' => 'No Material Request created.']);
            }

            $reference_pref = preg_replace('/[0-9]+/', null, $request->reference_no[0]);
            $reference_pref = str_replace("-", "", $reference_pref);
            $ref_table = ($reference_pref == 'SO') ? 'tabSales Order' : 'tabMaterial Request';
            $order_details = DB::connection('mysql')->table($ref_table)->where('name', $request->reference_no[0])->first();

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
                'schedule_date' => $request->schedule_date,
                'material_request_type' => 'Purchase',
                'delivery_date' => $order_details->delivery_date,
                'customer_name' => $order_details->customer,
                'sales_order' => ($reference_pref == 'SO') ? $request->reference_no[0] : null,
                'project' => $order_details->project,
                'purchase_request' => $request->purchase_request,
            ];

            $mr_item = [];
            foreach ($request->item_code as $i => $item) {
                $item_details = DB::connection('mysql')->table('tabItem')->where('name', $item)->first();
              
                $actual_qty = $this->get_actual_qty($item, $request->warehouse[$i]);

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
                    'stock_qty' => abs($request->required_qty[$i] * 1),
                    'qty' => abs($request->required_qty[$i]),
                    'actual_qty' => $actual_qty,
                    'schedule_date' => $request->required_date[$i],
                    'item_name' => $item_details->item_name,
                    'stock_uom' => $item_details->stock_uom,
                    'warehouse' => $request->warehouse[$i],
                    'uom' => $item_details->stock_uom,
                    'description' => $item_details->description,
                    'conversion_factor' => 1,
                    'item_code' => $item_details->item_code,
                    'sales_order' => ($reference_pref == 'SO') ? $request->reference_no[0] : null,
                    'item_group' => $item_details->item_group,
                    'project' => $order_details->project,
                ];
            }

            if(count($mr_item) > 0){
                DB::connection('mysql')->table('tabMaterial Request')->insert($mr);
                DB::connection('mysql')->table('tabMaterial Request Item')->insert($mr_item);   

                DB::connection('mysql_mes')->transaction(function() use ($request){
                    $production_orders = array_unique($request->production_order);
                    DB::connection('mysql_mes')->table('production_order')
                        ->whereIn('production_order', $production_orders)->update(['material_requested' => 1]);
                });
                $so_details= ($reference_pref == 'SO') ? $request->reference_no[0] : null;
                $this->send_material_request_email($mr_item,$new_id, $so_details,  $order_details->customer,$request->purchase_request, $order_details->project);

                DB::connection('mysql')->commit();
            
                return response()->json(['success' => 1, 'message' => 'Material Request has been created.', 'id' => $new_id]);
            }

            return response()->json(['success' => 2, 'message' => 'No Material Request created.']);
        } catch (Exception $e) {
            DB::connection('mysql')->rollback();
            return response()->json(['success' => 0, 'message' => $e->getMessage()]);
        }
    }
    public function send_material_request_email($items, $id, $so, $customer, $purchase_request, $project){
        $data = array(
            'mreq'           => $id,
            'sales_order_no'=> $so,
            'items'         => $items,
            'customer'		=> $customer,
            'project'		=> $project,
            'purchase_request' => $purchase_request,
            'created_by' => Auth::user()->email
        );
        if($purchase_request == "Imported"){
            $recipient= DB::connection('mysql_mes')
            ->table('email_trans_recipient')
            ->where('email_trans', "Material Request-Imported")
            ->where('email', 'like','%@fumaco.local%')
            ->select('email')
            ->get();
        }else{
            $recipient= DB::connection('mysql_mes')
            ->table('email_trans_recipient')
            ->where('email_trans', "Material Request-Local")
            ->where('email', 'like','%@fumaco.local%')
            ->select('email')
            ->get();
        }
        
        if(count($recipient) > 0){
                foreach ($recipient as $row) {
                    Mail::to($row->email)->send(new SendMail_material_request($data));
                }	
        }
    }

    public function item_stock(){
        $items = DB::connection('mysql')->table('tabItem')->whereIn('name', ['AC00081', 'AC00083', 'AC00090', 'AC00091', 'AC00095', 'AC00112', 'AC00117', 'AC00118', 'AC00119', 'AC00138', 'AC00143', 'AC00150', 'AC00194', 'AC00234', 'AC00262', 'AC00335', 'AC00355', 'AC00356', 'AC00367', 'AC00380', 'AC00455', 'AC00500', 'AC00544', 'AC00545', 'AC00546', 'AC00548', 'AC00855', 'AC01036', 'AS00021', 'AS00023', 'AS00044', 'AS00046', 'AS00057', 'AS00059', 'AS00067', 'AS00068', 'BA00054', 'BA00428', 'BL00015', 'BP00168', 'BT00557', 'BT00562', 'CA00002', 'CB00120', 'CB00127', 'CB00130', 'CB00131', 'CB00133', 'CB00134', 'CB00136', 'CB00139', 'CB00141', 'CB00144', 'CB00146', 'CB00147', 'CB00148', 'CB00149', 'CB00165', 'CB00166', 'CB00175', 'CS00023', 'CS00024', 'CS00025', 'CS00026', 'CS00027', 'CT00029', 'DI00009', 'DI00012', 'DI00015', 'DI00016', 'DI00055', 'DI00057', 'DI00068', 'EX00027', 'EX00118', 'EX00147', 'FA00027', 'FA00034', 'FA00136', 'FA00143', 'FA00145', 'FA00213', 'FA00317', 'FG13921', 'FG15966', 'FG63955', 'FG64083', 'FG64629', 'FG64847', 'FG65172', 'FG65241', 'FG65799', 'FG66549', 'FG66641', 'FG71555', 'FG73966', 'FG74279', 'FG74366', 'FG75062', 'FG75227', 'FG75861', 'FG76435', 'FG76461', 'FG76525', 'FG76540', 'FG76619', 'FG76621', 'FG76628', 'FG76629', 'FG76630', 'FG76632', 'FG76635', 'FG76646', 'FR00124', 'FR00127', 'FR00129', 'FR00137', 'FR00141', 'FR00167', 'FR00394', 'FR00398', 'GI00007', 'HI00056', 'HI00057', 'HO01979', 'HO02509', 'HO02538', 'HO02552', 'HO02762', 'HO02769', 'HO02782', 'HO02869', 'HO02902', 'HO03171', 'HO03574', 'HO03619', 'HO03645', 'HO03713', 'HO03714', 'HO03946', 'HO03993', 'HO04192', 'HO04196', 'HO04200', 'HO04791', 'HO04957', 'HO05080', 'HO05154', 'HO05156', 'HO05367', 'HO05462', 'HO05463', 'HO05470', 'HO05473', 'HO06078', 'HO06093', 'HO06095', 'HO06111', 'HO06179', 'HO06191', 'HO06194', 'HO06253', 'HO06267', 'HO06309', 'HO06329', 'HO06330', 'HO06331', 'HO06342', 'HO06358', 'HO06361', 'HO06369', 'HO06371', 'HO06378', 'HO06386', 'HO06399', 'HO06407', 'HO06412', 'HO06422', 'HO06424', 'HO06430', 'HO06433', 'HO06443', 'HO06457', 'HO06462', 'HO06463', 'HO06470', 'HO06477', 'HO06500', 'HO06514', 'HO06519', 'HO06524', 'HO06536', 'HO06537', 'HO06538', 'HO06541', 'HO06569', 'HO06571', 'HO06574', 'HO06578', 'HO06580', 'HO06588', 'HO06589', 'HO06592', 'HO06593', 'HO06594', 'HO06597', 'HO06598', 'HO06599', 'HO06601', 'HO06602', 'HO06603', 'HO06606', 'HO06608', 'HO06611', 'HO06612', 'HO06615', 'HO06627', 'HO06628', 'IP00200', 'IP00246', 'LC00089', 'LC00268', 'LC00271', 'LH00040', 'LH00058', 'LH00060', 'LH00072', 'LH00101', 'LH00122', 'LH00151', 'LH00155', 'LH00156', 'LH00176', 'LH00219', 'LH00255', 'LH00301', 'LM00066', 'LM00073', 'LM00141', 'LR00012', 'LR00046', 'LR00163', 'LR00274', 'LR00293', 'LR00383', 'LR00398', 'LR00404', 'LR00411', 'LR00428', 'LR00448', 'LT00722', 'LT00767', 'LT01060', 'LT01360', 'LT01364', 'LT01414', 'LT01423', 'LT01424', 'PK00002', 'PK00006', 'PN00003', 'RA00071', 'RA00080', 'RA00088', 'RA00091', 'RA00094', 'RA00095', 'RA00096', 'RA00100', 'RA00114', 'RA00389', 'RA00476', 'RA00507', 'RA00508', 'RA00528', 'RA00536', 'RA00539', 'RA00558', 'RA00591', 'RA00593', 'RL00058', 'RL00124', 'SA00278', 'SA00279', 'SA00281', 'SA00282', 'SA00283', 'SA00299', 'SA00305', 'SA00306', 'SA00749', 'SA04484', 'SA04489', 'SA04532', 'SA04545', 'SA04546', 'SA05191', 'SA05635', 'SA05796', 'SA05847', 'SA05967', 'SA06563', 'SA06577', 'SA06578', 'SA06601', 'SA06721', 'SA06725', 'SA06726', 'SA06727', 'SA06728', 'SA06736', 'SA06744', 'SA06787', 'SA06829', 'SA06842', 'SA06843', 'SA06845', 'SA06857', 'SA06858', 'SA06859', 'SA06873', 'SA06874', 'SA06875', 'SA06876', 'SA06877', 'SA06879', 'SA06880', 'SA06883', 'SA06892', 'SA06893', 'SA06894', 'SA06895', 'SA06896', 'SA06904', 'SA06905', 'SA06907', 'SA06908', 'SA06918', 'SA06929', 'SA07000', 'SA07001', 'SA07026', 'SA07027', 'SA07057', 'SA07058', 'SA07059', 'SA07060', 'SA07124', 'SA07172', 'SA07315', 'SA07320', 'SA07325', 'SA07507', 'SA07547', 'SA07693', 'SA07712', 'SA07714', 'SA07720', 'SA07748', 'SA07752', 'SA07754', 'SA07756', 'SA07759', 'SA07830', 'SA08092', 'SA08131', 'SA08211', 'SA08286', 'SA08329', 'SA08334', 'SA08335', 'SA08351', 'SA08352', 'SA08500', 'SA08502', 'SA08534', 'SA08908', 'SA08937', 'SA08940', 'SA08941', 'SA08981', 'SA09096', 'SA09097', 'SA09098', 'SA09099', 'SA09104', 'SA09137', 'SA09142', 'SA09150', 'SA09155', 'SA09163', 'SA09193', 'SA09254', 'SA09421', 'SA09475', 'SA09478', 'SA09480', 'SA09481', 'SA09484', 'SA09487', 'SA09489', 'SA09495', 'SA09676', 'SA09692', 'SA09713', 'SA09714', 'SA09715', 'SA09773', 'SA09801', 'SA09865', 'SA09873', 'SA09879', 'SA09890', 'SA09893', 'SA10014', 'SA10169', 'SA10335', 'SA10345', 'SA10524', 'SA10525', 'SA10526', 'SA10553', 'SA10686', 'SA10688', 'SA10705', 'SA10706', 'SA10710', 'SA10714', 'SA10764', 'SA10768', 'SA10772', 'SA10916', 'SA10917', 'SA10961', 'SA10998', 'SA11056', 'SA11057', 'SA11138', 'SA11142', 'SA11145', 'SA11147', 'SA11158', 'SA11190', 'SA11207', 'SA11212', 'SA11277', 'SA11300', 'SA11355', 'SA11404', 'SA11443', 'SA11456', 'SA11457', 'SA11463', 'SA11551', 'SA11566', 'SA11600', 'SA11601', 'SA11668', 'SA11669', 'SA11728', 'SA11729', 'SA11770', 'SA11794', 'SA11802', 'SA11898', 'SA11965', 'SA11968', 'SA11969', 'SA12019', 'SA12063', 'SA12294', 'SA12300', 'SA12367', 'SA12395', 'SA12397', 'SA12493', 'SA12494', 'SA12495', 'SA12818', 'SA12960', 'SA13203', 'SA13212', 'SA13292', 'SA13349', 'SA13431', 'SA13432', 'SA13433', 'SA13976', 'SA13977', 'SA13982', 'SA13985', 'SA14024', 'SA14025', 'SA14026', 'SA14045', 'SA14078', 'SA14101', 'SA14104', 'SA14146', 'SA14147', 'SA14234', 'SA14249', 'SA14251', 'SA14255', 'SA14264', 'SA14289', 'SA14301', 'SA14787', 'SA15045', 'SA15821', 'SA16339', 'SA16343', 'SA16466', 'SA16547', 'SA16779', 'SA16780', 'SA16781', 'SA16782', 'SA16794', 'SA16866', 'SA16870', 'SA16875', 'SA16927', 'SA17025', 'SA17197', 'SA17208', 'SA17231', 'SA17233', 'SA17282', 'SA17495', 'SA17531', 'SA17598', 'SA17604', 'SA17614', 'SA17658', 'SA17659', 'SA17672', 'SA17674', 'SA17677', 'SA17679', 'SA17683', 'SA17698', 'SA17734', 'SA17760', 'SA17761', 'SA17762', 'SA17774', 'SA17788', 'SA17790', 'SA17798', 'SA17808', 'SA17835', 'SA17846', 'SA17855', 'SA17859', 'SA17860', 'SA17861', 'SA17862', 'SA17893', 'SA17918', 'SA17919', 'SA17955', 'SA17965', 'SA17978', 'SA17980', 'SA17981', 'SA17983', 'SA17986', 'SA18067', 'SA18069', 'SA18097', 'SA18107', 'SA18121', 'SA18135', 'SA18136', 'SA18137', 'SA18139', 'SA18140', 'SA18141', 'SA18142', 'SA18148', 'SA18149', 'SA18155', 'SA18168', 'SA18176', 'SA18190', 'SA18198', 'SA18202', 'SA18203', 'SA18204', 'SA18205', 'SA18215', 'SA18266', 'SA18267', 'SA18268', 'SA18278', 'SA18290', 'SA18296', 'SA18328', 'SA18353', 'SA18357', 'SA18358', 'SA18390', 'SA18391', 'SA18392', 'SA18395', 'SA18396', 'SA18398', 'SA18400', 'SA18401', 'SA18402', 'SA18406', 'SA18408', 'SA18409', 'SA18411', 'SA18412', 'SA18413', 'SA18414', 'SA18415', 'SA18421', 'SA18422', 'SA18424', 'SA18428', 'SA18429', 'SA18431', 'SA18432', 'SA18435', 'SA18436', 'SA18441', 'SA18443', 'SA18444', 'SA18446', 'SA18447', 'SA18461', 'SA18462', 'SA18463', 'SA18464', 'SA18465', 'SA18466', 'SA18467', 'SA18476', 'SA18478', 'SA18479', 'SA18481', 'SA18483', 'SA18486', 'SA18487', 'SA18488', 'SA18495', 'SA18496', 'SA18497', 'SA18498', 'SA18499', 'SA18502', 'SA18503', 'SA18504', 'SA18505', 'SA18506', 'SA18507', 'SA18508', 'SA18509', 'SA18511', 'SA18512', 'SA18513', 'SA18527', 'SA18536', 'SA18537', 'SA18538', 'SA18540', 'SA18541', 'SA18542', 'SA18543', 'SA18544', 'SA18545', 'SA18546', 'SA18551', 'SA18559', 'SA18567', 'SF00259', 'ST00013', 'ST00014', 'SX00002', 'TB00019', 'TB00022', 'TL00209', 'TL00241', 'TL00255', 'TR00023', 'TT00429', 'WA00002', 'WC00017', 'WC00018', 'WC00019', 'WC00020', 'WC00023', 'WC00038', 'WC00070', 'WC00071', 'WC00072', 'WC00073', 'WC00086'])->get();

        foreach($items as $i){
            // $stock_level = DB::connection('mysql')->table('tabBin')->where('item_code', $i->name)->where('actual_qty', '>', 0)->get();
            $ledger = DB::connection('mysql')->table('tabStock Ledger Entry')
                ->where('fiscal_year', 2020)
                ->whereRaw("posting_time > CAST('00:00:00' AS TIME) AND posting_time < CAST('06:00:00' AS TIME)")
                ->where('item_code', $i->name)->distinct()->pluck('posting_date');

            $ledger_1 = DB::connection('mysql')->table('tabStock Ledger Entry')
                ->whereIn('posting_date', $ledger)->where('item_code', $i->name)
                ->where('fiscal_year', 2020)
                ->select('creation', 'posting_date', 'posting_time', 'warehouse', 'voucher_type', 'voucher_no')->orderBy('creation', 'asc')->get();
            $arr[] = [
                'item_code' => $i->name,
                'description' => $i->description,
                'item_classification' => $i->item_classification,
                // 'stock_level' => $stock_level
                'ledger' => $ledger_1
            ];
        }

        // return $arr;

        return view('testing', compact('arr'));
    }
}