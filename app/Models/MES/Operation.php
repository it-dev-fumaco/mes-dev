<?php

namespace App\Models\MES;

class Operation extends MesModel
{
    protected $table = 'operation';

    protected $primaryKey = 'operation_id';

    public $incrementing = false;

    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class, 'operation_id', 'operation_id');
    }

    public function isAssembly()
    {
        return strpos(strtolower((string) $this->operation_name), 'assembly') !== false;
    }
}
