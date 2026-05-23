<?php

namespace App\Models\ERP;

class WorkOrderItem extends ErpModel
{
    protected $table = 'tabWork Order Item';

    protected $primaryKey = 'name';

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'parent', 'name');
    }
}
