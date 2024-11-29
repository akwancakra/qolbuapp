<?php

namespace App\Http\Controllers;

abstract class Controller
{
    // DATA FOR BENEFICIARIES
    public $educationList = [
        ['label' => 'Tidak Sekolah', 'value' => 'Tidak Sekolah'],
        ['label' => 'TK (Taman Kanak-kanak)', 'value' => 'TK'],
        ['label' => 'SD (Sekolah Dasar)', 'value' => 'SD'],
        ['label' => 'SMP (Sekolah Menengah Pertama)', 'value' => 'SMP'],
        ['label' => 'SMA (Sekolah Menengah Atas)', 'value' => 'SMA'],
        ['label' => 'D1 (Diploma 1)', 'value' => 'D1'],
        ['label' => 'D2 (Diploma 2)', 'value' => 'D2'],
        ['label' => 'D3 (Diploma 3)', 'value' => 'D3'],
        ['label' => 'S1 (Sarjana)', 'value' => 'S1'],
    ];

    public $statusList = [
        ['label' => 'Yatim', 'value' => 'Yatim'],
        ['label' => 'Piatu', 'value' => 'Piatu'],
        ['label' => 'Yatim Piatu', 'value' => 'Yatim Piatu'],
        ['label' => 'Dhuafa', 'value' => 'Dhuafa'],
    ];

    // DATA FOR INCOMES
    public $paymentMethods = [
        ['label' => 'BCA', 'value' => 'BCA'],
        ['label' => 'BNI', 'value' => 'BNI'],
        ['label' => 'BRI', 'value' => 'BRI'],
        ['label' => 'BJB', 'value' => 'BJB'],
        ['label' => 'Mandiri', 'value' => 'Mandiri'],
        ['label' => 'Tunai', 'value' => 'Tunai'],
    ];

    public $donationTypes = [
        ['label' => 'Sedekah', 'value' => 'Sedekah'],
        ['label' => 'Infaq', 'value' => 'Infaq'],
        ['label' => 'Wakaf', 'value' => 'Wakaf'],
        ['label' => 'Zakat', 'value' => 'Zakat'],
        ['label' => 'Lainnya', 'value' => 'Lainnya'],
    ];
}
