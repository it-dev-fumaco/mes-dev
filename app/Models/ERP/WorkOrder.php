<?php

namespace App\Models\ERP;

class WorkOrder extends ErpModel
{
    protected $table = 'tabWork Order';

    protected $primaryKey = 'name';

    public function items()
    {
        return $this->hasMany(WorkOrderItem::class, 'parent', 'name');
    }

    public function stockEntries()
    {
        return $this->hasMany(StockEntry::class, 'work_order', 'name');
    }
}
