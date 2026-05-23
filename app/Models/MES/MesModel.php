<?php

namespace App\Models\MES;

use Illuminate\Database\Eloquent\Model;

abstract class MesModel extends Model
{
    protected $connection = 'mysql_mes';

    public $timestamps = false;
}
