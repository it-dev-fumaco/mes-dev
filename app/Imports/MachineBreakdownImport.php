<?php

namespace App\Imports;

use App\MachineBreakdownImportModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use Session;

class MachineBreakdownImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    * Documentation - https://docs.laravel-excel.com/3.1/imports/heading-row.html
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    
    public function model(array $row)
    {
        return new MachineBreakdownImportModel([
            'machine_breakdown_id' => 'XR-',
            'machine_id' => $row['machine_id'],
            'status' => $row['status'],
            'hold_reason' => $row['hold_reason'],
            'reported_by' => $row['reported_by'],
            'date_reported' => $row['date_reported'] ? Carbon::parse($row['date_reported'])->format('Y-m-d h:i:s') : null,
            'work_started' => $row['work_started'] ? Carbon::parse($row['work_started'])->format('Y-m-d h:i:s') : null,
            'remarks' => $row['remarks'],
            'date_resolved' => $row['date_resolved'] ? Carbon::parse($row['date_resolved'])->format('Y-m-d h:i:s') : null,
            'work_done' => $row['work_done'],
            'findings' => $row['findings'],
            'assigned_maintenance_staff' => $row['assigned_maintenance_staff'],
            'type' => $row['type'],
            'corrective_reason' => $row['corrective_reason'],
            'breakdown_reason' => $row['breakdown_reason'],
            'category' => $row['category'],
            'created_by' => Auth::user()->employee_name,
            'created_at' => Carbon::now()->toDateTimeString(),
            'last_modified_by' => Auth::user()->employee_name,
            'last_modified_at' => Carbon::now()->toDateTimeString()
        ]);
    }
}
