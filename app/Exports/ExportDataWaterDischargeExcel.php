<?php

namespace App\Exports;

use App\Testing;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportDataWaterDischargeExcel implements FromView
{
    protected $id;

    function __construct($id) {
           $this->id = $id;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {   
               return view('painting.tbl_report_painting_water_discharged', [
            'data' => $this->id]);

    }
}
