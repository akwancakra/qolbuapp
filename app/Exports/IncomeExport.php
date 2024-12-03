<?php

namespace App\Exports;

use App\Models\Income;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
// use Maatwebsite\Excel\Concerns\FromCollection;

class IncomeExport implements FromView
{
    protected $incomes;

    public function __construct($incomes = null)
    {
        $this->incomes = $incomes ?? Income::all();
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     return $this->incomes;
    // }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function view(): View
    {
        return view('exports.incomes', [
            'incomes' => $this->incomes,
        ]);
    }

}
