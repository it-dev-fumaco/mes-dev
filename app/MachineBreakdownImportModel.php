<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class MachineBreakdownImportModel extends Model
{
    protected $primaryKey = 'machine_breakdown_id';
    protected $fillable = [
        'machine_breakdown_id',
        'machine_id',
        'status',
        'hold_reason',
        'reported_by',
        'date_reported',
        'work_started',
        'remarks',
        'date_resolved',
        'work_done',
        'findings',
        'assigned_maintenance_staff',
        'type',
        'corrective_reason',
        'breakdown_reason',
        'category',
        'created_by',
        'created_at',
        'last_modified_by',
        'last_modified_at'
    ];
    protected $table = 'mes.machine_breakdown';
    protected $keyType = 'string';

    public $incrementing = true;
    public const CREATED_AT = null;
    public const UPDATED_AT = null;
}
