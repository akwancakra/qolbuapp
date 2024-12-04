<?php

namespace App\Exports;

use App\Models\Beneficiary;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BeneficiaryExport implements FromView
{
    protected $beneficiaries;

    public function __construct($beneficiaries = null)
    {
        $this->beneficiaries = $beneficiaries ?? Beneficiary::all();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    // public function collection()
    // {
    //     return $this->beneficiaries;
    // }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function view(): View
    {
        return view('exports.beneficiaries', [
            'beneficiaries' => $this->beneficiaries,
        ]);
    }
}
