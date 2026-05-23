<?php

namespace App\Models\ERP;

class Bin extends ErpModel
{
    protected $table = 'tabBin';

    protected $primaryKey = 'name';

    public function scopeForItem($query, $itemCode)
    {
        return $query->where('item_code', $itemCode);
    }

    public function scopeInWarehouse($query, $warehouse)
    {
        return $query->where('warehouse', $warehouse);
    }

    public function scopeForItemsInWarehouse($query, array $itemCodes, $warehouse)
    {
        return $query->whereIn('item_code', $itemCodes)->where('warehouse', $warehouse);
    }
}
