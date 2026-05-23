<?php

namespace App\Models\MES;

class FeedbackedLog extends MesModel
{
    protected $table = 'feedbacked_logs';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $guarded = [];

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order', 'production_order');
    }
}
