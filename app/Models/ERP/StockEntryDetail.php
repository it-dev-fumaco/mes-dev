<?php

namespace App\Models\ERP;

class StockEntryDetail extends ErpModel
{
    protected $table = 'tabStock Entry Detail';

    protected $primaryKey = 'name';

    public const STATUS_ISSUED = 'Issued';

    public function stockEntry()
    {
        return $this->belongsTo(StockEntry::class, 'parent', 'name');
    }

    public function scopeIssued($query)
    {
        return $query->where('status', self::STATUS_ISSUED);
    }

    public function scopeDraft($query)
    {
        return $query->where('docstatus', 0);
    }

    public function scopeForItemsInSourceWarehouse($query, array $itemCodes, $warehouse)
    {
        return $query->whereIn('item_code', $itemCodes)->where('s_warehouse', $warehouse);
    }

    /**
     * @param array $itemCodes
     * @param string $warehouse
     * @return \Illuminate\Support\Collection
     */
    public static function issuedTotalsGroupedByItem(array $itemCodes, $warehouse)
    {
        return static::query()
            ->draft()
            ->issued()
            ->forItemsInSourceWarehouse($itemCodes, $warehouse)
            ->selectRaw('SUM(qty) as total_issued, item_code')
            ->groupBy('item_code', 's_warehouse')
            ->get()
            ->groupBy('item_code');
    }
}
