<?php

namespace App\Models\ERP;

use Illuminate\Support\Collection;

class AthenaTransaction extends ErpModel
{
    protected $table = 'tabAthena Transactions';

    protected $primaryKey = 'name';

    /**
     * Issued qty grouped by item_code for packing/picking slips in draft delivery notes.
     *
     * @param array $itemCodes
     * @param string $warehouse
     * @return Collection grouped by item_code
     */
    public static function issuedTotalsGroupedByItem(array $itemCodes, $warehouse)
    {
        return static::query()
            ->from('tabAthena Transactions as at')
            ->join('tabPacking Slip as ps', 'ps.name', '=', 'at.reference_parent')
            ->join('tabPacking Slip Item as psi', 'ps.name', '=', 'psi.parent')
            ->join('tabDelivery Note as dr', 'ps.delivery_note', '=', 'dr.name')
            ->whereIn('at.reference_type', ['Packing Slip', 'Picking Slip'])
            ->where('dr.docstatus', 0)
            ->where('ps.docstatus', '<', 2)
            ->where('psi.status', 'Issued')
            ->whereIn('at.item_code', $itemCodes)
            ->whereIn('psi.item_code', $itemCodes)
            ->where('at.source_warehouse', $warehouse)
            ->selectRaw('SUM(at.issued_qty) as total_issued, at.item_code')
            ->groupBy('at.item_code', 'at.source_warehouse')
            ->get()
            ->groupBy('item_code');
    }
}
