<?php

namespace App\Exports;

use App\Testing;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportDataChemicalMonitoringExcel implements FromView
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
               return view('painting.painting_chem_export', [
            'data' => $this->id]);

    }
}
