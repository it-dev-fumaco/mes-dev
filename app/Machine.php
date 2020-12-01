<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $connection = 'mysql_mes';
    protected $table = 'machine';
    protected $primaryKey = 'machine_id';
}
