<?php

namespace App\Models\MES;

class JobTicket extends MesModel
{
    protected $table = 'job_ticket';

    protected $primaryKey = 'job_ticket_id';

    public $incrementing = true;

    public const STATUS_COMPLETED = 'Completed';

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order', 'production_order');
    }

    public function scopeForProductionOrder($query, $productionOrder)
    {
        return $query->where('production_order', $productionOrder);
    }

    public function scopeNotCompleted($query)
    {
        return $query->where('status', '!=', self::STATUS_COMPLETED);
    }
}
