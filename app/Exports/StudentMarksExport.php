<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class StudentMarksExport implements FromView
{
    public $paper_name;
    public $data;
    
    public function __construct($paper_name,$data)
    {   $this->paper_name=$paper_name;
        $this->data=$data;
        
    }

    public function view(): View
    {
        return view('exports.student_mark', [
            'paper_name' => $this->paper_name,
            'data'    => $this->data,
        ]);
    }
}
