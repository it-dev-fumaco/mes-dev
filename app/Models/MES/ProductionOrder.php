<?php

namespace App\Models\MES;

class ProductionOrder extends MesModel
{
    protected $table = 'production_order';

    protected $primaryKey = 'production_order';

    protected $keyType = 'string';

    public $incrementing = false;

    public function jobTickets()
    {
        return $this->hasMany(JobTicket::class, 'production_order', 'production_order');
    }

    public function operation()
    {
        return $this->belongsTo(Operation::class, 'operation_id', 'operation_id');
    }

    public function feedbackedLogs()
    {
        return $this->hasMany(FeedbackedLog::class, 'production_order', 'production_order');
    }

    public static function findByProductionOrder($productionOrder)
    {
        return static::where('production_order', $productionOrder)->first();
    }

    public function scopeForPartHierarchy($query, $itemCode, $parentItemCode, $salesOrder, $materialRequest, $subParentItemCode)
    {
        return $query->where('item_code', $itemCode)
            ->where('parent_item_code', $parentItemCode)
            ->where('sales_order', $salesOrder)
            ->where('material_request', $materialRequest)
            ->where('sub_parent_item_code', $subParentItemCode);
    }
}
