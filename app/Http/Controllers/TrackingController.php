<?php

namespace App\Http\Controllers;

use App\Traits\GeneralTrait;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    use GeneralTrait;

	public function getOngoingOrders(Request $request) {
		$erp_db = ENV('DB_DATABASE_ERP');
		$mes_db = ENV('DB_DATABASE_MES');

		$requested_reference = $request->reference;
        $requested_customer = $request->customer;
		$requested_item_code = $request->item_code;

		$search_order = false;
		if ($requested_reference || $requested_item_code || $requested_customer) {
			$search_order = true;
		}

		$material_requests = DB::table($erp_db.'.tabMaterial Request as mr')
			->join($erp_db.'.tabMaterial Request Item as mri', 'mr.name', 'mri.parent')
            ->when(!$search_order, function ($query) use ($mes_db) {
				return $query->join($mes_db.'.production_order as p', function($join) {
                    $join->on('p.item_code', 'mri.item_code');
                    $join->on(DB::raw('IFNULL(p.sales_order, p.material_request)'), 'mr.name');
                })
                ->whereNotIn('p.status', ['Cancelled', 'Closed'])->whereRaw('p.qty_to_manufacture > p.feedback_qty');
			})
            ->where('mr.docstatus', 1)->where('mr.status', '!=', 'Stopped')
			->when($search_order, function ($query) use ($requested_reference) {
				return $query->where('mr.name', 'LIKE', '%'.$requested_reference.'%');
			})
			->when($search_order && $requested_item_code, function ($query) use ($requested_item_code) {
				return $query->where('mri.item_code', 'LIKE', '%'.$requested_item_code.'%');
			})
            ->when($search_order && $requested_customer, function ($query) use ($requested_customer) {
				return $query->where('mr.customer', 'LIKE', '%'.$requested_customer.'%');
			})
			->select('mr.customer', 'mr.name', 'mri.item_code', 'mri.description', 'mri.qty', 'mri.stock_uom', 'mri.idx', 'mri.schedule_date as delivery_date', 'mri.ordered_qty as delivered_qty', 'mri.reschedule_delivery', 'mri.rescheduled_delivery_date', 'mri.name as child', 'mr.status');

		$order_list = DB::table($erp_db.'.tabSales Order as so')
			->join($erp_db.'.tabSales Order Item as soi', 'so.name', 'soi.parent')
			->where('so.docstatus', 1)->whereIn('so.sales_type', ['Regular Sales', 'Sales DR'])
			->where('so.status', '!=', 'Closed')
            ->when(!$search_order, function ($query) use ($mes_db) {
				return $query->join($mes_db.'.production_order as p', function($join) {
                    $join->on('p.item_code', 'soi.item_code');
                    $join->on(DB::raw('IFNULL(p.sales_order, p.material_request)'), 'so.name');
                })
                ->whereNotIn('p.status', ['Cancelled', 'Closed'])->whereRaw('soi.qty > soi.delivered_qty');
			})
			->when($search_order && $requested_reference, function ($query) use ($requested_reference) {
				return $query->where('so.name', 'LIKE', '%'.$requested_reference.'%');
			})
			->when($search_order && $requested_item_code, function ($query) use ($requested_item_code) {
				return $query->where('soi.item_code', 'LIKE', '%'.$requested_item_code.'%');
			})
            ->when($search_order && $requested_customer, function ($query) use ($requested_customer) {
				return $query->where('so.customer', 'LIKE', '%'.$requested_customer.'%');
			})
			->select('so.customer', 'so.name', 'soi.item_code', 'soi.description', 'soi.qty', 'soi.stock_uom', 'soi.idx', 'soi.delivery_date', 'soi.delivered_qty', 'soi.reschedule_delivery', 'soi.rescheduled_delivery_date', 'soi.name as child', 'so.status')
			->unionAll($material_requests)
            ->orderByRaw('IFNULL(rescheduled_delivery_date, delivery_date) DESC')->paginate(25);
            
        $references = collect($order_list->items())->pluck('name');
        $item_codes = collect($order_list->items())->pluck('item_code')->unique();
    
        $production_orders = DB::connection('mysql_mes')->table('production_order as p')
            ->join('job_ticket as j', 'p.production_order', 'j.production_order')
            ->join('process as w', 'w.process_id', 'j.process_id')
            ->whereIn(DB::raw('IFNULL(p.sales_order, p.material_request)'), $references)
            ->whereIn('p.parent_item_code', $item_codes)
            ->whereNotIn('p.status', ['Cancelled', 'Closed'])
            ->selectRaw('p.production_order, item_code, IFNULL(sales_order, material_request) as reference, qty_to_manufacture, feedback_qty, p.status as p_status, produced_qty, parent_item_code, j.workstation, j.status as j_status, w.process_name, p.feedback_qty, j.job_ticket_id')
            ->orderByRaw("FIELD(j.status, 'In Progress', 'In Process', 'Pending', 'Completed')")
            ->get()->groupBy(['reference', 'parent_item_code']);

		$items_production_orders = [];
		foreach ($production_orders as $reference => $parent_item_codes) {
            $row = [];
            foreach ($parent_item_codes as $parent_item_code => $rows) {
                $status = 'Not Started';
                $feedbacked_qty = 0;
                $item_prod = collect($rows)->where('item_code', $parent_item_code)->first();
                if ($item_prod) {
                    $feedbacked_qty = $item_prod->feedback_qty;
                    if ($item_prod->qty_to_manufacture == $feedbacked_qty) {
                        $status = 'Feedbacked';
                    }
                }
                
                // number of production process
                $noOfProcess = collect($rows)->count();
                $noCompletedProcess = collect($rows)->where('j_status', 'Completed')->count();
                $noPendingProcess = collect($rows)->where('j_status', 'Pending')->count();

                $currentProcess = [];
                if ($status != 'Feedbacked') {
                    if ($noPendingProcess < $noOfProcess) {
                        $status = 'Idle';
                    }

                    $has_wip = $this->hasInProgressProcess($rows);
                    if (!$has_wip) {
                        $status ='Idle';
                    }
    
                    if ($noOfProcess == $noCompletedProcess) {
                        $status = 'Ready for Feedback';
                    }

                    $currentProcess = collect($rows)->where('j_status', 'In Progress')->first();
                    if ($currentProcess) {
                        if (in_array($currentProcess->process_name, ['Loading', 'Unloading'])) {
                            $status = 'Painting';
                        } else {
                            $status = $currentProcess->process_name;
                        }
                    }
                }

                $row[$parent_item_code] = [
                    'no_of_process' => $noOfProcess,
                    'no_of_completed_process' => $noCompletedProcess,
                    'current_process' => $currentProcess,
                    'status' => $status,
                    'feedbacked_qty' => $feedbacked_qty
                ];
            }
           
            $items_production_orders[$reference] = $row;
		}

		return view('ongoing_orders', compact('order_list', 'items_production_orders'));
	}

    public function viewOrderDetails($id, Request $request) {
		$dashboard_request = $request->dashboard;
		$ref_type = explode("-", $id)[0];

		if ($ref_type == 'MREQ') {
			$details = DB::connection('mysql')->table('tabMaterial Request as mr')->where('name', $id)
				->select('mr.name', 'mr.creation', 'mr.customer', 'mr.project', 'mr.delivery_date', 'mr.custom_purpose as order_type', 'mr.status', 'mr.modified as date_approved', DB::raw('CONCAT(mr.address_line, " ", mr.address_line2, " ", mr.city_town)  as shipping_address'), 'mr.owner', 'mr.notes00 as notes', 'mr.sales_person', 'mr.delivery_date as reschedule_delivery_date', DB::raw('IFNULL(mr.delivery_date, 0) as reschedule_delivery'), 'mr.company', 'mr.modified', 'mr.per_ordered as delivery_percentage')->first();

			$item_list = DB::connection('mysql')->table('tabMaterial Request Item')->where('parent', $id)
				->select('item_code', 'description', 'qty', 'stock_uom', 'idx', 'parent', 'schedule_date as delivery_date', 'name', 'ordered_qty as delivered_qty', 'item_code as item_note', 'warehouse')
				->orderBy('idx', 'asc')->get();

			$item_codes = collect($item_list)->pluck('item_code')->unique();

			$actual_delivery_date_per_item = DB::connection('mysql')->table('tabStock Entry as ste')->join('tabStock Entry Detail as sted', 'ste.name', 'sted.parent')
				->join('tabMaterial Request Item as mri', 'mri.item_code', 'sted.item_code')
				->where('mri.parent', $id)->where('ste.material_request', $id)->where('ste.docstatus', 1)->whereIn('sted.item_code', $item_codes)
				->where('ste.stock_entry_type', 'Material Transfer')
				->select('ste.name', 'sted.item_code', DB::raw('SUM(sted.qty) as delivered_qty'), 'ste.delivery_date as actual_delivery_date', 'ste.reference_no as dr_ref_no', 'mri.qty as ordered_qty', 'mri.stock_uom', 'sted.date_modified', 'ste.posting_date', 'ste.owner', 'sted.session_user')
				->groupBy('ste.name', 'sted.item_code', 'ste.delivery_date', 'ste.reference_no', 'mri.qty', 'mri.stock_uom', 'sted.date_modified', 'ste.posting_date', 'ste.owner', 'sted.session_user')->get();

			$picking_slip_arr = [];
			foreach ($actual_delivery_date_per_item as $ps_row) {
				$picking_slip_arr[$ps_row->name][$ps_row->item_code]['date_picked'] = Carbon::parse($ps_row->date_modified ? $ps_row->date_modified : $ps_row->posting_date)->format('M. d, Y');
				$picking_slip_arr[$ps_row->name][$ps_row->item_code]['user'] = $ps_row->session_user;
			}
		} else {
			$details = DB::connection('mysql')->table('tabSales Order as so')->where('name', $id)
				->select('so.name', 'so.creation', 'so.customer', 'so.project', 'so.delivery_date', 'so.sales_type as order_type', 'so.status', 'so.date_approved', 'so.shipping_address', 'so.owner', 'so.notes', 'so.sales_person', 'so.reschedule_delivery_date', 'so.reschedule_delivery', 'so.company', 'so.modified', 'so.per_delivered as delivery_percentage')
				->first();
			
			$item_list = DB::connection('mysql')->table('tabSales Order Item')->where('parent', $id)
				->select('item_code', 'description', 'qty', 'stock_uom', 'idx', 'parent', 'delivery_date', 'name', 'delivered_qty', 'item_note', 'warehouse')
				->orderBy('idx', 'asc')->get();

			$item_codes = collect($item_list)->pluck('item_code')->unique();

			$actual_delivery_date_per_item = DB::connection('mysql')->table('tabDelivery Note as dr')
				->join('tabDelivery Note Item as dri', 'dr.name', 'dri.parent')->join('tabSales Order Item as soi', 'soi.item_code', 'dri.item_code')
				->where('dr.reference', $id)->where('soi.parent', $id)->where('dr.docstatus', 1)->whereIn('dri.item_code', $item_codes)
				->select('dr.name', 'dri.item_code', DB::raw('SUM(dri.qty) as delivered_qty'), 'dr.delivery_date as actual_delivery_date', 'dr.dr_ref_no', 'soi.qty as ordered_qty', 'dri.stock_uom', 'dr.owner')
				->groupBy('dr.name', 'dri.item_code', 'dr.delivery_date', 'dr.dr_ref_no', 'soi.qty', 'dri.stock_uom', 'dr.owner')->get();

			$picking_slips = DB::connection('mysql')->table('tabPacking Slip as ps')->join('tabPacking Slip Item as psi', 'ps.name', 'psi.parent')
				->whereIn('ps.delivery_note', collect($actual_delivery_date_per_item)->pluck('name'))
				->whereIn('psi.item_code', $item_codes)->select('ps.delivery_note', 'psi.item_code', 'psi.date_modified', 'ps.modified', 'psi.session_user')->get();

			$picking_slip_arr = [];
			foreach ($picking_slips as $ps_row) {
				$picking_slip_arr[$ps_row->delivery_note][$ps_row->item_code]['date_picked'] = Carbon::parse($ps_row->date_modified ? $ps_row->date_modified : $ps_row->modified)->format('M. d, Y');
				$picking_slip_arr[$ps_row->delivery_note][$ps_row->item_code]['user'] = $ps_row->session_user;
			}
		}

		if (!$dashboard_request) {
			$current_inventory = DB::connection('mysql')->table('tabBin')->whereIn('item_code', collect($item_list)->pluck('item_code'))->whereIn('warehouse', collect($item_list)->pluck('warehouse'))->get();
			$current_inventory_arr = [];
			foreach($current_inventory as $ci){
				$current_inventory_arr[$ci->item_code][$ci->warehouse] = $ci->actual_qty;
			}

			$default_boms = DB::connection('mysql')->table('tabBOM')
				->whereIn('item', $item_codes)->where('docstatus', 1)->where('is_active', 1)
				->select('item', 'is_default', 'name')->orderBy('is_default', 'desc')
				->orderBy('creation', 'desc')->get();

			$default_boms = collect($default_boms)->groupBy('item')->toArray();

			$seen_order_logs = DB::connection('mysql_mes')->table('activity_logs')
				->where('reference', $id)->where('action', 'View Order')->orderBy('created_at', 'desc')->get();
			$seen_logs_per_order = collect($seen_order_logs)->groupBy('reference')->toArray();

			$files = DB::connection('mysql')->table('tabFile')->where('attached_to_doctype', $ref_type == 'SO' ? 'Sales Order' : 'Material Request')
				->where('attached_to_name', $id)->get();
		}

		$actual_delivery_date_per_item = collect($actual_delivery_date_per_item)->groupBy('item_code')->toArray();

		$item_images = DB::connection('mysql')->table('tabItem Images')->whereIn('parent', $item_codes)->pluck('image_path', 'parent')->toArray();

		$item_list = collect($item_list)->groupBy('parent')->toArray();

		$item_production_order_qty = DB::connection('mysql_mes')->table('production_order')
			->where(DB::raw('IFNULL(sales_order, material_request)'), $id)->where('status', '!=', 'Cancelled')
			->whereIn('item_code', $item_codes)
			->selectRaw('SUM(qty_to_manufacture) as qty_to_manufacture, SUM(produced_qty) as produced_qty, SUM(feedback_qty) as feedback_qty, item_code')
			->groupBy(['item_code'])->get()->groupBy('item_code')->toArray();

		$production_orders = DB::connection('mysql_mes')->table('production_order')
			->whereIn('item_code', $item_codes)->where(DB::raw('IFNULL(sales_order, material_request)'), $id)
			->where('status', '!=', 'Cancelled')
			->select('production_order', 'item_code', DB::raw('IFNULL(sales_order, material_request) as reference'), 'qty_to_manufacture', 'feedback_qty', 'status', 'produced_qty', 'created_at', 'created_by')
			->orderBy('created_at', 'desc')->get();

		$items_production_orders = [];
		foreach ($production_orders as $r) {
			$p_status = $r->produced_qty > $r->feedback_qty ? 'Ready for Feedback' : $r->status;
			$p_status = $r->qty_to_manufacture == $r->feedback_qty ? 'Feedbacked' : $p_status;
			$items_production_orders[$r->reference][$r->item_code][] = [
				'production_order' => $r->production_order,
				'status' => $p_status,
				'produced_qty' => $r->produced_qty,
				'qty_to_manufacture' => $r->qty_to_manufacture,
				'created_at' => $r->created_at,
				'created_by' => $r->created_by
			];
		}

		$operation_status = [];
		if ($dashboard_request) {
			$production_orders = DB::connection('mysql_mes')->table('production_order as po')
				->join('job_ticket as jt', 'jt.production_order', 'po.production_order')
				->where(DB::raw('IFNULL(sales_order, material_request)'), $id)->where('po.status', '!=', 'Cancelled')
				->select('po.production_order', 'item_code', 'qty_to_manufacture', 'feedback_qty', 'po.status as po_status', 'jt.status as jt_status', 'produced_qty', 'jt.workstation', 'po.operation_id', 'po.parent_item_code', 'jt.good', 'po.actual_start_date', 'po.actual_end_date', 'po.planned_start_date', 'jt.job_ticket_id')
				->orderBy('po.created_at', 'desc')->get()->groupBy('parent_item_code');

			foreach($production_orders as $item_code => $workstations) {
				$not_painting_workstations = collect($workstations)->filter(function ($value) {
					return $value->workstation != 'Painting';
				});
		
				$has_fabrication = collect($not_painting_workstations)->where('operation_id', 1);
				if ($has_fabrication && collect($has_fabrication)->count()) {
                    $operation_status[$item_code]['fabrication'] = $this->orderItemProductionStatus($has_fabrication);
				}
		
				$has_assembly = collect($not_painting_workstations)->where('operation_id', 3);
				if ($has_assembly && collect($has_assembly)->count()) {
					$operation_status[$item_code]['assembly'] = $this->orderItemProductionStatus($has_assembly);
				}
		
				// workstations excluding painting
				$has_painting = collect($workstations)->filter(function ($value) {
					return $value->workstation == 'Painting';
				});
		
				if ($has_painting && collect($has_painting)->count()) {
					$operation_status[$item_code]['painting'] = $this->orderItemProductionStatus($has_painting);
				}
			}
		}

		$comments = DB::connection('mysql')->table('tabComment')->where('reference_name', $id)
			->where('comment_type', 'Comment')->select('creation', 'comment_by', 'content')
			->orderBy('creation', 'desc')->get();

		if ($dashboard_request) {
			return view('modals.view_order_details', compact('details', 'ref_type', 'item_list', 'item_images', 'comments', 'actual_delivery_date_per_item', 'picking_slip_arr', 'operation_status', 'item_production_order_qty'));
		}

		return view('modals.view_order_modal_content', compact('details', 'ref_type', 'items_production_orders', 'item_list', 'default_boms', 'item_images', 'seen_logs_per_order', 'comments', 'actual_delivery_date_per_item', 'picking_slip_arr', 'files', 'current_inventory_arr'));
	}

    public function orderItemProductionStatus($collection) {
        $operation_status = [];
        $details = collect($collection)->groupBy('production_order')->map(function ($group) {
            return [
                'produced_qty' => $group->min('produced_qty'),
                'feedback_qty' => $group->max('feedback_qty'),
            ];
        });
       
        $produced_qty = collect($details)->min('produced_qty');
        $feedback_qty = collect($details)->min('feedback_qty');

        $status = 'not_started';
        if (collect($collection)->where('jt_status', 'In Progress')->count() > 0) {
            $status = 'active';
        }

        $status = 'not_started';
        if (collect($collection)->where('jt_status', 'In Progress')->count() > 0) {
            $status = 'active';
        }

        $completed_jt = collect($collection)->where('jt_status', 'Completed')->count();
        if ($completed_jt > 0 && $completed_jt < collect($collection)->count()) {
            $status = 'active';
        }

        if (collect($collection)->count() > 0 && $status != 'active') {
            if ($produced_qty > $feedback_qty) {
                $status = 'for_feedback';
            }

            if ($produced_qty <= $feedback_qty && $feedback_qty > 0) {
                $status = 'completed';
            }
        }

        if ($status == 'active') {
            $hasInProgressProcess = $this->hasInProgressProcess($collection);
            if (!$hasInProgressProcess) {
                $status = 'idle';
            }
        }

        if ($status != 'not_started') {
            $ctual_start_date = collect($collection)->min('actual_start_date');
            $actual_end_date = collect($collection)->max('actual_end_date');

            $ctual_start_date = $ctual_start_date ? Carbon::parse($ctual_start_date) : null;
            $actual_end_date = $actual_end_date ? Carbon::parse($actual_end_date) : null;
            $duration = null;

            if ($ctual_start_date) {
                $operation_status['start'] = Carbon::parse($ctual_start_date)->format('M. d, Y - h:i A');
            }

            if ($ctual_start_date && $actual_end_date) {
                if ($status != 'idle') {
                    $duration = $this->seconds2human($ctual_start_date->diffInSeconds($actual_end_date));
                    $operation_status['end'] = Carbon::parse($actual_end_date)->format('M. d, Y - h:i A');
                    $operation_status['duration'] = $duration;
                }
            }
        }

        $operation_status['status'] = $status;

        return $operation_status;
    }

    public function onGoingQtyPerOperation() {
		$production_orders = DB::connection('mysql_mes')->table('production_order as p')
            ->join('job_ticket as j', 'p.production_order', 'j.production_order')
            ->whereNotIn('p.status', ['Cancelled', 'Closed'])
            ->where('j.status', 'In Progress')
            ->whereRaw('p.qty_to_manufacture > p.feedback_qty')
            ->selectRaw('item_code, qty_to_manufacture, produced_qty, feedback_qty, operation_id, workstation, (qty_to_manufacture - produced_qty) as remaining')
            ->get();

        $not_painting_workstations = collect($production_orders)->filter(function ($value) {
            return $value->workstation != 'Painting';
        });

        $fabrication = collect($not_painting_workstations)->where('operation_id', 1)->sum('remaining');
        $assembly = collect($not_painting_workstations)->where('operation_id', 3)->sum('remaining');
        $painting = collect($production_orders)->where('workstation', 'Painting')->sum('remaining');

        return response()->json([
            'fabrication' => number_format($fabrication),
            'painting' => number_format($painting),
            'assembly' => number_format($assembly),
        ]);
    }

    public function item_status_tracking_page()
    {
        return view('item_status_tracking');
    }

    public function get_item_status_tracking(Request $request)
    {
        $query = DB::connection('mysql_mes')->table('delivery_date as dd')
            ->join('production_order as po', DB::raw('IFNULL(po.sales_order, po.material_request)'), 'dd.reference_no')
            ->whereNotIn('po.status', ['Cancelled'])
            ->where('dd.parent_item_code', '!=', null)
            ->where(function ($q) use ($request) {
                $q->Where('po.customer', 'LIKE', '%' . $request->search_string . '%')
                    ->orWhere('po.sales_order', 'LIKE', '%' . $request->search_string . '%')
                    ->orWhere('po.material_request', 'LIKE', '%' . $request->search_string . '%')
                    ->orWhere('po.parent_item_code', 'LIKE', '%' . $request->search_string . '%')
                    ->orWhere('po.project', 'LIKE', '%' . $request->search_string . '%');
            })
            ->selectRaw('dd.reference_no, po.customer, dd.parent_item_code, po.project, CAST(dd.delivery_date AS CHAR) as delivery_date, CAST(dd.rescheduled_delivery_date AS CHAR) as rescheduled_delivery_date, dd.erp_reference_id')
            ->groupBy('dd.reference_no', 'po.customer', 'dd.parent_item_code', 'po.project', 'dd.delivery_date', 'dd.rescheduled_delivery_date', 'dd.erp_reference_id')
            ->orderBy('po.created_at', 'desc')->paginate(10);

        if ($request->get_total) {
            return ['div' => '#item-tracking-total', 'total' => number_format($query->total())];
        }

        $erp_reference_ids = array_column($query->items(), 'erp_reference_id');

        $sales_order_item = DB::connection('mysql')->table('tabSales Order Item')
            ->whereIn('name', $erp_reference_ids)
            ->select('description', 'qty', 'name')
            ->get()->toArray();

        $material_request_item = DB::connection('mysql')->table('tabMaterial Request Item')
            ->whereIn('name', $erp_reference_ids)
            ->select('description', 'qty', 'name')
            ->get()->toArray();

        $sales_order_items = collect($sales_order_item)->groupBy('name')->toArray();
        $material_request_items = collect($material_request_item)->groupBy('name')->toArray();

        $references = array_unique(array_column($query->items(), 'reference_no'));
        $order_items = array_column($query->items(), 'parent_item_code');

        $production_order_detail = DB::connection('mysql_mes')->table('production_order')
            ->where(function ($q) use ($references) {
                $q->whereIn('sales_order', $references)
                    ->orWhereIn('material_request', $references);
            })
            ->whereIn('item_code', $order_items)
            ->selectRaw('CONCAT(IFNULL(sales_order, material_request), item_code) as id, production_order, bom_no')
            ->get();

        $production_order_detail = collect($production_order_detail)->groupBy('id')->toArray();

        $production_order_list = [];
        foreach ($query as $row) {
            $item_description = $production_order = $bom_no = null;
            $qty = 0;
            $reference_prefix = explode('-', $row->reference_no)[0];
            if ($reference_prefix == 'SO') {
                if (array_key_exists($row->erp_reference_id, $sales_order_items)) {
                    $item_description = $sales_order_items[$row->erp_reference_id][0]->description;
                    $qty = $sales_order_items[$row->erp_reference_id][0]->qty;
                }
            } else {
                if (array_key_exists($row->erp_reference_id, $material_request_items)) {
                    $item_description = $material_request_items[$row->erp_reference_id][0]->description;
                    $qty = $material_request_items[$row->erp_reference_id][0]->qty;
                }
            }

            $prod_key = $row->reference_no . $row->parent_item_code;
            if (array_key_exists($prod_key, $production_order_detail)) {
                $production_order = $production_order_detail[$prod_key][0]->production_order;
                $bom_no = $production_order_detail[$prod_key][0]->bom_no;
            }

            $production_order_list[] = [
                'reference_no' => $row->reference_no,
                'item_code' => $row->parent_item_code,
                'description' => $item_description,
                'customer' => $row->customer,
                'delivery_date' => ($row->rescheduled_delivery_date == null) ? $row->delivery_date : $row->rescheduled_delivery_date,
                'qty' => $qty,
                'project' => $row->project,
                'erp_reference_no' => $row->erp_reference_id,
                'production_order' => $production_order,
                'bom_no' => $bom_no,
            ];
        }

        return view('tables.tbl_item_list_for_tracking', compact('query', 'production_order_list'));
    }

    public function get_search_information_details(Request $request)
    {
        try {
            $query = DB::connection('mysql_mes')->table('delivery_date as dd')
                ->join('production_order as po', DB::raw('IFNULL(po.sales_order, po.material_request)'), 'dd.reference_no')
                ->whereNotIn('po.status', ['Cancelled'])
                ->where('dd.parent_item_code', '!=', null)
                ->where(function ($q) use ($request) {
                    $q->Where('po.customer', 'LIKE', '%' . $request->search_string . '%')
                        ->orWhere('po.sales_order', 'LIKE', '%' . $request->search_string . '%')
                        ->orWhere('po.material_request', 'LIKE', '%' . $request->search_string . '%')
                        ->orWhere('po.parent_item_code', 'LIKE', '%' . $request->search_string . '%')
                        ->orWhere('po.project', 'LIKE', '%' . $request->search_string . '%');
                })
                ->selectRaw('dd.reference_no, po.customer, dd.parent_item_code, po.project, CAST(dd.delivery_date AS CHAR) as delivery_date, CAST(dd.rescheduled_delivery_date AS CHAR) as rescheduled_delivery_date, dd.erp_reference_id')
                ->groupBy('dd.reference_no', 'po.customer', 'dd.parent_item_code', 'po.project', 'dd.delivery_date', 'dd.rescheduled_delivery_date', 'dd.erp_reference_id')
                ->orderBy('po.created_at', 'desc')->paginate(10);

            $erp_reference_ids = array_column($query->items(), 'erp_reference_id');

            $sales_order_item = DB::connection('mysql')->table('tabSales Order Item')
                ->whereIn('name', $erp_reference_ids)
                ->select('description', 'qty', 'name')
                ->get()->toArray();

            $material_request_item = DB::connection('mysql')->table('tabMaterial Request Item')
                ->whereIn('name', $erp_reference_ids)
                ->select('description', 'qty', 'name')
                ->get()->toArray();

            $sales_order_items = collect($sales_order_item)->groupBy('name')->toArray();
            $material_request_items = collect($material_request_item)->groupBy('name')->toArray();

            $references = array_unique(array_column($query->items(), 'reference_no'));
            $order_items = array_column($query->items(), 'parent_item_code');

            $production_order_detail = DB::connection('mysql_mes')->table('production_order')
                ->where(function ($q) use ($references) {
                    $q->whereIn('sales_order', $references)
                        ->orWhereIn('material_request', $references);
                })
                ->whereIn('item_code', $order_items)
                ->selectRaw('CONCAT(IFNULL(sales_order, material_request), item_code) as id, production_order, bom_no')
                ->get();

            $production_order_detail = collect($production_order_detail)->groupBy('id')->toArray();

            $production_order_list = [];
            foreach ($query as $row) {
                $item_description = $production_order = $bom_no = null;
                $qty = 0;
                $reference_prefix = explode('-', $row->reference_no)[0];
                if ($reference_prefix == 'SO') {
                    if (array_key_exists($row->erp_reference_id, $sales_order_items)) {
                        $item_description = $sales_order_items[$row->erp_reference_id][0]->description;
                        $qty = $sales_order_items[$row->erp_reference_id][0]->qty;
                    }
                } else {
                    if (array_key_exists($row->erp_reference_id, $material_request_items)) {
                        $item_description = $material_request_items[$row->erp_reference_id][0]->description;
                        $qty = $material_request_items[$row->erp_reference_id][0]->qty;
                    }
                }

                $prod_key = $row->reference_no . $row->parent_item_code;
                if (array_key_exists($prod_key, $production_order_detail)) {
                    $production_order = $production_order_detail[$prod_key][0]->production_order;
                    $bom_no = $production_order_detail[$prod_key][0]->bom_no;
                }

                $production_order_list[] = [
                    'reference_no' => $row->reference_no,
                    'item_code' => $row->parent_item_code,
                    'description' => $item_description,
                    'customer' => $row->customer,
                    'delivery_date' => ($row->rescheduled_delivery_date == null) ? $row->delivery_date : $row->rescheduled_delivery_date,
                    'qty' => $qty,
                    'project' => $row->project,
                    'erp_reference_no' => $row->erp_reference_id,
                    'production_order' => $production_order,
                    'bom_no' => $bom_no,
                ];
            }

            return view('tables.tbl_item_list_for_tracking', compact('query', 'production_order_list'));
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function get_bom_tracking(Request $request)
    {
        $reference_order_no = $request->guideid;
        $ref_type = explode("-", $reference_order_no)[0];

        $reference_erp_table = ($ref_type == 'SO') ? 'tabSales Order' : 'tabMaterial Request';
        
        $reference_details = DB::connection('mysql')->table($reference_erp_table . ' as parent')
            ->join($reference_erp_table . ' Item as child', 'parent.name', 'child.parent')
            ->join('tabItem as item', 'child.item_code', 'item.name')
            ->where('parent.name', $reference_order_no)->where('child.name', $request->erp_reference_id)
            ->first();

        $item_image = DB::connection('mysql')->table('tabItem Images')->where('parent', $reference_details->item_code)->pluck('image_path')->first();

        $img = 'http://athenaerp.fumaco.local/storage';
        $img .= $item_image ? "/img/" . $item_image : "/icon/no_img.png";

        $item_details = [
            'item_code' => $reference_details->item_code,
            'description' => $reference_details->description,
            'image' => $img,
            'qty' => $reference_details->qty,
            'stock_uom' => $reference_details->stock_uom,
            'delivered_qty' => $reference_details->delivered_qty ?? $reference_details->ordered_qty,
            'parts_category' => $reference_details->parts_category,
        ];

        $production_orders = DB::connection('mysql_mes')->table('production_order as po')
            ->join('job_ticket as jt', 'jt.production_order', 'po.production_order')
            ->where(DB::raw('IFNULL(sales_order, material_request)'), $reference_order_no)->where('po.status', '!=', 'Cancelled')
            ->where('parent_item_code', $item_details['item_code'])
            ->select('po.production_order', 'item_code', 'qty_to_manufacture', 'feedback_qty', 'po.status as po_status', 'jt.status as jt_status', 'produced_qty', 'jt.workstation', 'po.operation_id', 'jt.idx', 'po.description', 'po.actual_start_date', 'po.actual_end_date', 'jt.job_ticket_id', 'jt.good')
            ->orderBy('po.created_at', 'desc')->orderBy('jt.idx', 'asc')->get();

        $production_per_item_query = DB::connection('mysql_mes')->table('production_order')
            ->where(DB::raw('IFNULL(sales_order, material_request)'), $reference_order_no)->where('status', '!=', 'Cancelled')
            ->where('parent_item_code', $item_details['item_code'])
            ->select('production_order', 'item_code', 'qty_to_manufacture', 'feedback_qty', 'status', 'produced_qty', 'description', 'actual_start_date', 'actual_end_date', 'planned_start_date', 'bom_no')
            ->orderByRaw("FIELD(status, 'Feedbacked', 'Completed', 'Ready for Feedback', 'Partially Feedbacked', 'In Progress', 'Not Started', 'Closed') ASC")->get();

        $item_details['feedback_qty'] = collect($production_per_item_query)->where('item_code', $item_details['item_code'])->sum('feedback_qty');

        $production_per_item_query = collect($production_per_item_query)->groupBy('item_code')->toArray();

        $production_per_item = [];
        foreach ($production_per_item_query as $item_code => $pos) {
            $production_orders_array = [];
            foreach ($pos as $po) {
                $duration = null;
                if ($po->actual_start_date && $po->actual_end_date) {
                    $actual_start_date = Carbon::parse($po->actual_start_date);
                    $actual_end_date = Carbon::parse($po->actual_end_date);
                    $duration = $this->seconds2human($actual_start_date->diffInSeconds($actual_end_date));
                }
                $production_orders_array[] = [
                    'production_order' => $po->production_order,
                    'bom_no' => $po->bom_no ? $po->bom_no : 'No BOM',
                    'item_code' => $po->item_code,
                    'qty_to_manufacture' => $po->qty_to_manufacture,
                    'produced_qty' => $po->produced_qty,
                    'feedback_qty' => $po->feedback_qty,
                    'status' => $po->status,
                    'description' => $po->description,
                    'actual_start_date' => $po->actual_start_date ? Carbon::parse($po->actual_start_date)->format('M. d, Y - h:i A') : null,
                    'actual_end_date' => $po->actual_end_date ? Carbon::parse($po->actual_end_date)->format('M. d, Y - h:i A') : null,
                    'planned_start_date' => $po->planned_start_date ? Carbon::parse($po->planned_start_date)->format('M. d, Y') : 'Unscheduled',
                    'duration' => $duration,
                ];
            }

            $production_per_item[$item_code] = $production_orders_array;
        }

        $operation_status = [];
        $not_painting_workstations = collect($production_orders)->filter(function ($value) {
            return $value->workstation != 'Painting';
        });

        $has_fabrication = collect($not_painting_workstations)->where('operation_id', 1);
        if ($has_fabrication && collect($has_fabrication)->count()) {
            $operation_status['fabrication'] = $this->orderItemProductionStatus($has_fabrication);
        }

        $has_assembly = collect($not_painting_workstations)->where('operation_id', 3);
        if ($has_assembly && collect($has_assembly)->count()) {
            $operation_status['assembly'] = $this->orderItemProductionStatus($has_assembly);
        }

        // workstations excluding painting
        $has_painting = collect($production_orders)->filter(function ($value) {
            return $value->workstation == 'Painting';
        });

        if ($has_painting && collect($has_painting)->count()) {
            $operation_status['painting'] = $this->orderItemProductionStatus($has_painting);
        }

        $production_order_workstations_query = collect($production_orders)->groupBy('production_order')->toArray();
        $production_order_workstations = $idle_production_orders = [];
        foreach ($production_order_workstations_query as $prod => $w) {
            $workstation_array = $workstation_statuses = [];
            $total_good = collect($w)->sum('good');
            foreach ($w as $row) {
                $workstation_status = $row->jt_status;
                if ($total_good > 0 && $row->good < $row->qty_to_manufacture && $row->feedback_qty < $row->qty_to_manufacture) {
                    $row_arr[] = $row;
                    $has_wip = $this->hasInProgressProcess(collect($row_arr));
                    $workstation_status = $has_wip ? $row->jt_status : 'Idle';
                    $row_arr = [];
                }

                $workstation_statuses[] = $workstation_status;
               
                $workstation_array[$row->workstation][] = $workstation_status;
            }

            if (in_array('Idle', collect($workstation_statuses)->unique()->values()->toArray())) {
                $idle_production_orders[$prod] = collect($workstation_statuses)->unique()->values();
            }
            
            $production_order_workstations[$prod] = $workstation_array;
        }
        
        $item_bom = DB::connection('mysql')->table('tabBOM')->where('item', $item_details['item_code'])
            ->where('docstatus', '<', 2)->where('is_default', 1)->select('name', 'is_default', 'rf_drawing_no', 'item as item_code', 'description')
            ->orderBy('modified', 'desc')->first();

        if (!$item_bom) {
            $item_bom = DB::connection('mysql')->table('tabBOM')->where('item', $item_details['item_code'])->where('docstatus', '<', 2)
                ->select('name', 'is_default', 'rf_drawing_no', 'item as item_code', 'description', 'docstatus')
                ->orderBy('modified', 'desc')->first();
        }

        if ($item_bom != null) {
            $parts = $this->get_bom($item_bom->name, $reference_order_no, $item_details['item_code'], $item_details['item_code']);
        } else {
            $parts = [];
        }

        $per_item_idle_production_orders = collect($production_per_item_query)->map(function ($production_orders) use ($idle_production_orders){
            return collect($production_orders)->pluck('production_order')->filter(function ($value) use ($idle_production_orders){
                if (array_key_exists($value, $idle_production_orders)) {
                    return true;
                }
            });
        })->filter(function ($value) {
            return count($value) > 0;
        })->toArray();

        return view('tracking_flowchart', compact('operation_status', 'item_details', 'production_per_item', 'parts', 'production_order_workstations', 'idle_production_orders', 'per_item_idle_production_orders'));
    }

    private function hasInProgressProcess($collection) {
        if ($collection->count() > 0) {
            $spotwelding_workstations = collect($collection)->filter(function ($value) {
                return $value->workstation == 'Spotwelding';
            });

            $has_wip = false;
            if (collect($spotwelding_workstations)->count() > 0) {
                $job_ticket_ids = $spotwelding_workstations->pluck('job_ticket_id');
                $has_wip = DB::connection('mysql_mes')->table('spotwelding_qty')->whereIn('job_ticket_id', $job_ticket_ids)
                    ->whereIn('status', ['In Process', 'In Progress'])->exists();
            } 

            if (!$has_wip) {
                $job_ticket_ids = $collection->pluck('job_ticket_id');
                $has_wip = DB::connection('mysql_mes')->table('time_logs')->whereIn('job_ticket_id', $job_ticket_ids)
                    ->whereIn('status', ['In Process', 'In Progress'])->exists();
            }
        
            return $has_wip;
        }
    }

    public function get_bom($bom, $guide_id, $item_code, $parent_item_code)
    {
        try {
            $excluded_item_classifications = ['BP - Battery Pack', 'WW - Wall Washer Luminaire', 'WL - Wall Lights'];
            $bom_item_query = DB::connection('mysql')->table('tabBOM Item as bom')
                ->join('tabItem as item', 'item.name', 'bom.item_code')
                ->whereNotIn('item.item_group', ['Raw Material', 'Factory Supplies'])
                ->whereNotIn('item.item_classification', $excluded_item_classifications)
                ->where('bom.docstatus', '<', 2)->where('bom.parent', $bom)->select('bom.item_code', 'bom.bom_no', 'bom.qty', 'bom.uom', 'item.parts_category')
                ->orderBy('bom.idx', 'asc')->get();

            $bom_items = array_column($bom_item_query->toArray(), 'item_code');
            if (count($bom_items) > 0) {
                $default_boms = DB::connection('mysql')->table('tabBOM')->where('docstatus', '<', 2)
                    ->where('is_default', 1)->whereIn('item', $bom_items)->pluck('name', 'item')->toArray();

                $item_descriptions = DB::connection('mysql')->table('tabItem')->whereIn('name', $bom_items)
                    ->pluck('description', 'name')->toArray();
            }

            $materials = [];
            foreach ($bom_item_query as $item) {
                $bom_item_code = $item->item_code;
                $default_bom = array_key_exists($bom_item_code, $default_boms) ? $default_boms[$bom_item_code] : null;
                $item_description = array_key_exists($bom_item_code, $item_descriptions) ? $item_descriptions[$bom_item_code] : null;
                $child_bom = ($default_bom) ? $default_bom : $item->bom_no;

                $materials[] = [
                    'item_code' => $item->item_code,
                    'description' => $item_description,
                    'qty' => $item->qty,
                    'uom' => $item->uom,
                    'parts_category' => $item->parts_category,
                    'bom_no' => $default_bom,
                    'child_nodes' => $this->get_bom($child_bom, $guide_id, $item->item_code, $parent_item_code),
                ];
            }

            return $materials;
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function getTimesheetProcess($prod_no)
    {
        $req = DB::connection('mysql_mes')->table('production_order')->where('production_order', $prod_no)
            ->first()->qty_to_manufacture;

        $workstations = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $prod_no)
            ->join('process as p', 'p.process_id', 'job_ticket.process_id')
            ->selectRaw('job_ticket.process_id, GROUP_CONCAT(status) as status, p.process_name, job_ticket.workstation')
            ->orderBy('idx', 'asc')->groupBy('job_ticket.process_id', 'p.process_name', 'job_ticket.workstation')->get();

        $data = [];
        foreach ($workstations as $row) {
            $completed = DB::connection('mysql_mes')->table('job_ticket')->where('production_order', $prod_no)->where('process_id', $row->process_id)->sum('completed_qty');
            if ((strpos($row->status, 'Pending') > -1) && (strpos($row->status, 'Complet') > -1)) {
                $status = 'active';
            } elseif (strpos($row->status, 'Progress') > -1) {
                $status = 'active';
            } elseif (strpos($row->status, 'Pending') > -1) {
                $status = '';
            } else {
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
    public function seconds2human($ss)
    {
        $s = $ss % 60;
        $m = floor(($ss % 3600) / 60);
        $h = floor(($ss % 86400) / 3600);
        $d = floor(($ss % 2592000) / 86400);
        $ss = "s";
        $mm = "m";
        $dd = "d";
        $hh = "h";

        if ($d == 0 and $h == 0 and $m == 0 and $s == 0) {
            return "$s$ss";
        } elseif ($d == 0 and $h == 0 and $m == 0) {
            return "$s$ss";
        } elseif ($d == 0 and $h == 0) {
            return "$m$mm";
        } elseif ($d == 0) {
            return "$h$hh $m$mm";
        } else {
            return "$d$dd $h$hh $m$mm";
        }
    }

    public function productionKanban()
    {
        $permissions = $this->get_user_permitted_operation();
        $mes_user_operations = DB::connection('mysql_mes')->table('user')
            ->join('operation', 'operation.operation_id', 'user.operation_id')
            ->join('user_group', 'user_group.user_group_id', 'user.user_group_id')
            ->where('module', 'Production')
            ->where('user_access_id', Auth::user()->user_id)->pluck('operation_name')->toArray();

        $unscheduled_prod = DB::connection('mysql_mes')->table('production_order')
            ->whereNotIn('status', ['Stopped', 'Cancelled'])
            ->where('feedback_qty', 0)
            ->where('is_scheduled', 0)
            ->where("operation_id", '1')
            ->orderBy('sales_order', 'desc')
            ->orderBy('material_request', 'desc')->get();

        $unscheduled = [];
        $max = [];
        foreach ($unscheduled_prod as $row) {
            $stripfromcomma = strtok($row->description, ",");

            $spotlogs_inprogress = DB::connection('mysql_mes')->table('job_ticket as jt')
                ->leftJoin('spotwelding_qty as spotpart', 'spotpart.job_ticket_id', 'jt.job_ticket_id')
                ->join('process as p', 'p.process_id', 'jt.process_id')
                ->where('jt.production_order', $row->production_order)
                ->where('spotpart.status', "In Progress")
                ->select('spotpart.status as stat');

            $timelogs_inprogress = DB::connection('mysql_mes')->table('job_ticket as jt')
                ->leftJoin('time_logs as tl', 'jt.job_ticket_id', 'tl.job_ticket_id')
                ->join('process as p', 'p.process_id', 'jt.process_id')
                ->where('jt.production_order', $row->production_order)
                ->where('tl.status', "In Progress")
                ->select('tl.status as stat')
                ->union($spotlogs_inprogress);

            $groupby_logs = DB::connection('mysql_mes')->query()->fromSub($timelogs_inprogress, 'logss')
                ->select('stat')->first();

            $drag = empty($groupby_logs->stat) ? "move" : "not_move";

            if ($drag == "not_move") {
                $prod_status = "In Progress";
            } else {
                if ($row->status == "Completed") {
                    $prod_status = "Completed";

                } else {
                    if ($row->status == "Not Started") {
                        $prod_status = "Not Started";

                    } else {
                        $prod_status = "In Progress- On Que";
                    }

                }
            }

            $unscheduled[] = [
                'id' => $row->production_order,
                'status' => $prod_status,
                'name' => $row->production_order,
                'order_no' => $row->order_no,
                'customer' => $row->customer,
                'delivery_date' => $row->delivery_date,
                'production_item' => $row->item_code,
                'production_order' => $row->production_order,
                'description' => $row->description,
                'parts_category' => $row->parts_category,
                'parent_item_code' => $row->parent_item_code,
                'strip' => $stripfromcomma,
                'qty' => $row->qty_to_manufacture,
                'stock_uom' => $row->stock_uom,
                'produced_qty' => $row->produced_qty,
                'classification' => $row->classification,
                'sales_order' => ($row->sales_order == null) ? $row->material_request : $row->sales_order,
                'batch' => null,
                'process_stat' => $this->material_status_stockentry($row->production_order, $row->status),
                'drag' => $drag,
                'cycle_time' => $this->compute_item_cycle_time($row->item_code, $row->qty_to_manufacture),
                'cycle_in_seconds' => $this->compute_item_cycle_time_seconds_format($row->item_code, $row->qty_to_manufacture)
            ];
        }

        $period = CarbonPeriod::create(now()->subDays(1), now()->addDays(6));

        // Iterate over the period->subDays(1)
        $scheduled = [];
        foreach ($period as $date) {
            $orders = $this->getScheduledProdOrders($date->format('Y-m-d'));
            $shift_sched = $this->get_prod_shift_sched($date->format('Y-m-d'));
            $total_seconds = collect($orders)->sum('cycle_in_seconds');
            $scheduled[] = [
                'shift' => $shift_sched,
                'schedule' => $date->format('Y-m-d'),
                'orders' => $orders,
                'estimates' => $this->format_for_estimates($total_seconds),
                'estimates_in_seconds' => $total_seconds
            ];
        }
        
        return view('tbl_reload_production_kanban', compact('unscheduled', 'scheduled', 'mes_user_operations', 'permissions'));
    }

    public function material_status_stockentry($production_order, $stat)
    {
        $is_transferred = DB::connection('mysql')->table('tabStock Entry')
            ->where('purpose', 'Material Transfer for Manufacture')
            ->where('production_order', $production_order)
            ->where('docstatus', 1)->first();

        if ($is_transferred) {
            $status = 'Material Issued';
        } else {
            $status = 'Material For Issue';
        }

        $spotlogs = DB::connection('mysql_mes')->table('job_ticket as jt')
            ->leftJoin('spotwelding_qty as spotpart', 'spotpart.job_ticket_id', 'jt.job_ticket_id')
            ->join('process as p', 'p.process_id', 'jt.process_id')
            ->where('jt.production_order', $production_order)
            ->orderBy('spotpart.last_modified_at', 'desc')
            ->select(DB::raw('(SELECT MAX(last_modified_at) FROM spotwelding_qty WHERE job_ticket_id = jt.job_ticket_id GROUP BY job_ticket_id) AS last_modified_at'), 'p.process_name', 'jt.production_order', 'jt.job_ticket_id', 'jt.workstation');

        $timelogs = DB::connection('mysql_mes')->table('job_ticket as jt')
            ->leftJoin('time_logs as tl', 'jt.job_ticket_id', 'tl.job_ticket_id')
            ->join('process as p', 'p.process_id', 'jt.process_id')
            ->where('jt.production_order', $production_order)
            ->select('tl.last_modified_at', 'p.process_name', 'jt.production_order', 'jt.job_ticket_id', 'jt.workstation')
            ->union($spotlogs);
        $groupby_log = DB::connection('mysql_mes')->query()->fromSub($timelogs, 'logs')
            ->select('last_modified_at', 'process_name', 'job_ticket_id', 'workstation')
            ->orderBy('last_modified_at', 'DESC')->first();

        if (!empty($groupby_log)) {
            if ($groupby_log->last_modified_at != null) {
                $status = $groupby_log->workstation;
            }
        }

        if ($stat == "Completed") {
            $status = 'Ready For Feedback';
        }

        return $status;
    }

    public function format_for_estimates($cycle_time_in_seconds)
    {
        $dur_hours = floor($cycle_time_in_seconds / 3600);
        $dur_minutes = floor(($cycle_time_in_seconds / 60) % 60);
        $dur_seconds = $cycle_time_in_seconds % 60;

        $dur_hours = ($dur_hours > 0) ? $dur_hours . 'h' : null;
        $dur_minutes = ($dur_minutes > 0) ? $dur_minutes . 'm' : null;
        $dur_seconds = ($dur_seconds > 0) ? $dur_seconds . 's' : null;

        return $dur_hours . ' ' . $dur_minutes . ' ' . $dur_seconds;
    }

    public function get_prod_shift_sched($date)
    {
        if (
            DB::connection('mysql_mes')
                ->table('shift_schedule')
                ->where('date', $date)
                ->exists()
        ) {

            $shift_sched = DB::connection('mysql_mes')
                ->table('shift_schedule')
                ->where('date', $date)->get();
            foreach ($shift_sched as $r) {
                $shift_sched = DB::connection('mysql_mes')
                    ->table('shift')
                    ->where('shift_id', $r->shift_id)
                    ->first();
                $scheduled1[] = [
                    'time_in' => $shift_sched->time_in,
                    'time_out' => $shift_sched->time_out,
                    'shift_type' => $shift_sched->shift_type,
                ];
            }
        } else {
            $scheduled1 = [];
        }

        return $scheduled1;
    }

    public function get_customer_reference_no($customer)
    {
        return DB::connection('mysql_mes')->table('production_order')->whereNotIn('status', ['Completed', 'Cancelled'])
            ->where('customer', $customer)->selectRaw('IFNULL(sales_order, material_request) as reference')
            ->distinct()->orderBy('reference', 'asc')->pluck('reference');
    }

    public function get_customers()
    {
        return DB::connection('mysql_mes')->table('production_order')->whereNotIn('status', ['Completed', 'Cancelled'])
            ->whereNotNull('customer')->distinct()->orderBy('customer', 'asc')->pluck('customer');
    }

    public function get_reference_production_items(Request $request, $reference)
    {
        if ($request->item_type == 'parent') {
            return DB::connection('mysql_mes')->table('production_order')->whereNotIn('status', ['Completed', 'Cancelled'])
                ->where(function ($q) use ($reference) {
                    $q->where('sales_order', $reference)
                        ->orWhere('material_request', $reference);
                })
                ->distinct()->orderBy('parent_item_code', 'asc')->pluck('parent_item_code');
        }

        if ($request->item_type == 'sub-parent') {
            return DB::connection('mysql_mes')->table('production_order')->whereNotIn('status', ['Completed', 'Cancelled'])
                ->where(function ($q) use ($reference) {
                    $q->where('sales_order', $reference)
                        ->orWhere('material_request', $reference);
                })
                ->where('parent_item_code', $request->parent_item)
                ->whereNotNull('sub_parent_item_code')
                ->distinct()->orderBy('sub_parent_item_code', 'asc')->pluck('sub_parent_item_code');
        }

        return DB::connection('mysql_mes')->table('production_order')->whereNotIn('status', ['Completed', 'Cancelled'])
            ->where(function ($q) use ($reference) {
                $q->where('sales_order', $reference)
                    ->orWhere('material_request', $reference);
            })
            ->where('parent_item_code', $request->parent_item)
            ->when($request->sub_parent_item, function ($q) use ($request) {
                return $q->where('sub_parent_item_code', $request->sub_parent_item);
            })
            ->distinct()->orderBy('item_code', 'asc')->pluck('item_code');
    }

    public function getScheduledProdOrders($schedule_date)
    {
        $orders = DB::connection('mysql_mes')->table('production_order')
            ->whereNotIn('status', ['Cancelled'])->where('is_scheduled', 1)
            ->whereDate('planned_start_date', $schedule_date)->where("operation_id", '1')
            ->where('feedback_qty', 0)->orderBy('order_no', 'asc')->orderBy('order_no', 'asc')
            ->orderBy('created_at', 'desc')->get();

        $scheduled = [];
        foreach ($orders as $row) {
            $stripfromcomma = strtok($row->description, ",");

            $spotlogs_inprogress = DB::connection('mysql_mes')->table('job_ticket as jt')
                ->leftJoin('spotwelding_qty as spotpart', 'spotpart.job_ticket_id', 'jt.job_ticket_id')
                ->join('process as p', 'p.process_id', 'jt.process_id')->where('jt.production_order', $row->production_order)
                ->where('spotpart.status', "In Progress")->select('spotpart.status as stat');

            $timelogs_inprogress = DB::connection('mysql_mes')->table('job_ticket as jt')
                ->leftJoin('time_logs as tl', 'jt.job_ticket_id', 'tl.job_ticket_id')
                ->join('process as p', 'p.process_id', 'jt.process_id')
                ->where('jt.production_order', $row->production_order)
                ->where('tl.status', "In Progress")->select('tl.status as stat')
                ->union($spotlogs_inprogress);

            $groupby_logs = DB::connection('mysql_mes')->query()->fromSub($timelogs_inprogress, 'logss')
                ->select('stat')->first();

            $drag = empty($groupby_logs->stat) ? "move" : "not_move";
            if ($drag == "not_move") {
                $prod_status = "In Progress";
            } else {
                if ($row->status == "Completed") {
                    $prod_status = "Completed";
                } else {
                    if ($row->status == "Not Started") {
                        $prod_status = "Not Started";

                    } else {
                        $prod_status = "In Progress- On Que";
                    }
                }
            }

            $scheduled[] = [
                'id' => $row->production_order,
                'name' => $row->production_order,
                'status' => $prod_status,
                'order_no' => $row->order_no,
                'customer' => $row->customer,
                'delivery_date' => $row->delivery_date,
                'production_item' => $row->item_code,
                'description' => $row->description,
                'parts_category' => $row->parts_category,
                'parent_item_code' => $row->parent_item_code,
                'strip' => $stripfromcomma,
                'qty' => $row->qty_to_manufacture,
                'stock_uom' => $row->stock_uom,
                'produced_qty' => $row->produced_qty,
                'classification' => $row->classification,
                'production_order' => $row->production_order,
                'sales_order' => ($row->sales_order == null) ? $row->material_request : $row->sales_order,
                'batch' => null,
                'process_stat' => $this->material_status_stockentry($row->production_order, $row->status),
                'drag' => $drag,
                'cycle_time' => $this->compute_item_cycle_time($row->item_code, $row->qty_to_manufacture),
                'cycle_in_seconds' => $this->compute_item_cycle_time_seconds_format($row->item_code, $row->qty_to_manufacture)
            ];
        }

        return $scheduled;
    }
}