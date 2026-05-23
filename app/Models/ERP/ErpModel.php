<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Model;

abstract class ErpModel extends Model
{
    protected $connection = 'mysql';

    public $timestamps = false;

    protected $keyType = 'string';

    public $incrementing = false;
}
