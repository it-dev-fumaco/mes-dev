<?php

namespace App\Models\ERP;

class StockEntry extends ErpModel
{
    protected $table = 'tabStock Entry';

    protected $primaryKey = 'name';

    public const MATERIAL_TRANSFER_FOR_MANUFACTURE = 'Material Transfer for Manufacture';

    public const PURPOSE_MANUFACTURE = 'Manufacture';

    public function stockEntryDetails()
    {
        return $this->hasMany(StockEntryDetail::class, 'parent', 'name');
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'work_order', 'name');
    }

    public function scopeForWorkOrder($query, $workOrder)
    {
        return $query->where('work_order', $workOrder);
    }

    public function scopePurpose($query, $purpose)
    {
        return $query->where('purpose', $purpose);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('docstatus', 1);
    }

    public function scopeDraft($query)
    {
        return $query->where('docstatus', 0);
    }

    public function scopeMaterialTransferForManufacture($query)
    {
        return $query->where('purpose', self::MATERIAL_TRANSFER_FOR_MANUFACTURE);
    }

    public function scopeManufacture($query)
    {
        return $query->where('purpose', self::PURPOSE_MANUFACTURE);
    }

    /**
     * First pending MTFM line used for validation error messaging.
     *
     * @param string $workOrder
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public static function firstPendingMaterialTransferLine($workOrder)
    {
        return static::query()
            ->select('sted.item_code', 'sted.t_warehouse')
            ->from('tabStock Entry as ste')
            ->join('tabStock Entry Detail as sted', 'ste.name', '=', 'sted.parent')
            ->where('ste.work_order', $workOrder)
            ->where('ste.purpose', self::MATERIAL_TRANSFER_FOR_MANUFACTURE)
            ->where('ste.docstatus', 0)
            ->first();
    }
}
