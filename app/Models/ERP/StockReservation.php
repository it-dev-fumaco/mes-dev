<?php

namespace App\Models\ERP;

class StockReservation extends ErpModel
{
    protected $table = 'tabStock Reservation';

    protected $primaryKey = 'name';

    public const STATUS_ACTIVE = 'Active';

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeForItemsInWarehouse($query, array $itemCodes, $warehouse)
    {
        return $query->whereIn('item_code', $itemCodes)->where('warehouse', $warehouse);
    }

    /**
     * @param array $itemCodes
     * @param string $warehouse
     * @return \Illuminate\Support\Collection
     */
    public static function totalsGroupedByItem(array $itemCodes, $warehouse)
    {
        return static::query()
            ->forItemsInWarehouse($itemCodes, $warehouse)
            ->active()
            ->selectRaw('SUM(reserve_qty) as total_reserved_qty, SUM(consumed_qty) as total_consumed_qty, item_code')
            ->groupBy('item_code', 'warehouse')
            ->get()
            ->groupBy('item_code');
    }
}
