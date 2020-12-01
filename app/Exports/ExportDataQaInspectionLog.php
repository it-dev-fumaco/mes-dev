<?php

namespace App\Exports;

use App\Testing;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportDataQaInspectionLog implements FromView
{
    protected $id;

    function __construct($colspan_variable,$colspan_visual, $header_variable, $count_header_variable, $header_visual, $count_header_visual, $quality_check, $data, $width, $header) {
            $this->colspan_variable = $colspan_variable;
            $this->header_variable = $header_variable;
            $this->count_header_variable = $count_header_variable;
            $this->header_visual = $header_visual;
            $this->count_header_visual = $count_header_visual;
            $this->quality_check = $quality_check;
            $this->data = $data;
            $this->width = $width;
            $this->header = $header;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {   
               return view('quality_inspection.tbl_export_inspection_log_sheet', [
            'datcolspan_variablea' => $this->colspan_variable,'header_variable' => $this->header_variable,'count_header_variable' => $this->count_header_variable,'header_visual' => $this->header_visual,'count_header_visual' => $this->count_header_visual,'quality_check' => $this->quality_check,'data' => $this->data,'width' => $this->width,'header' => $this->header]);

    }
}
