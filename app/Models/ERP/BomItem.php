<?php

namespace App\Models\ERP;

class BomItem extends ErpModel
{
    protected $table = 'tabBOM Item';

    protected $primaryKey = 'name';

    public function scopeForBom($query, $bomNo)
    {
        return $query->where('parent', $bomNo);
    }

    public function scopeForItem($query, $itemCode)
    {
        return $query->where('item_code', $itemCode);
    }
}
