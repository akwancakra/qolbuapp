<?php

namespace App\Http\Controllers\BoardMember;

use App\Http\Controllers\Controller;
use App\Models\Ambassador;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IncomeController extends Controller
{
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
        ['label' => 'Zakat', 'value' => 'Wakaf'],
        ['label' => 'Lainnya', 'value' => 'Lainnya'],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        // ========================================================
        // VALIDATING AND GETTING DATA FOR FILTERING AND SEARCHING
        // ========================================================
        $name = $request->input('name') ?? 'default';
        $teamCode = $request->input('teamCode') ?? 'default';
        $time = $request->input('time') ?? 'default';
        $startRange = $request->input('startRange');
        $endRange = $request->input('endRange');
        $week = $request->input('week');
        $month = $request->input('month');
        $year = $request->input('year');

        if ($teamCode === 'default') {
            $teamCode = null;
        }

        if ($time === 'default') {
            $time = null;
            $startRange = null;
            $endRange = null;
            $week = null;
            $month = null;
            $year = null;
        }

        if ($name === 'default') {
            $name = null;
        }
        // ========================================================
        // VALIDATING AND GETTING DATA FOR FILTERING AND SEARCHING
        // ========================================================

        $topTenAmbassadors = Ambassador::topTenByIncome();
        $availableTeamCodes = Ambassador::select('code')
            ->distinct()
            ->orderBy('code')
            ->get()
            ->pluck('code')
            ->toArray();
        $availableAmbassadors = Ambassador::select('id', 'name')->get();
        $availableAmbassadors = $availableAmbassadors->map(function ($ambassador) {
            return [
                'label' => $ambassador->name,
                'value' => $ambassador->id,
            ];
        });
        $availableMonths = [
            ['value' => '01', 'label' => 'Januari'],
            ['value' => '02', 'label' => 'Februari'],
            ['value' => '03', 'label' => 'Maret'],
            ['value' => '04', 'label' => 'April'],
            ['value' => '05', 'label' => 'Mei'],
            ['value' => '06', 'label' => 'Juni'],
            ['value' => '07', 'label' => 'Juli'],
            ['value' => '08', 'label' => 'Agustus'],
            ['value' => '09', 'label' => 'September'],
            ['value' => '10', 'label' => 'Oktober'],
            ['value' => '11', 'label' => 'November'],
            ['value' => '12', 'label' => 'Desember'],
        ];

        $incomesQuery = Income::with('ambassador')
            ->when($name, function ($query, $name) {
                return $query->whereHas('ambassador', function ($query) use ($name) {
                    $query->where('name', 'like', '%' . $name . '%');
                });
            })
            ->when($teamCode, function ($query, $teamCode) {
                return $query->whereHas('ambassador', function ($query) use ($teamCode) {
                    $query->where('code', $teamCode);
                });
            })
            ->orderByDesc('created_at');

        $allIncomes = $incomesQuery->get();
        $incomes = $incomesQuery
            ->when($startRange && $endRange, function ($query) use ($startRange, $endRange) {
                return $query->whereBetween('transfer_date', [$startRange, $endRange]);
            })
            ->when($time === 'weekly' && $week, function ($query) use ($week) {
                $start = Carbon::parse($week);
                $end = $start->copy()->addDays(6);
                return $query->whereBetween('transfer_date', [$start, $end]);
            })
            ->when($time === 'monthly' && $month && $year, function ($query) use ($month, $year) {
                return $query->whereMonth('transfer_date', $month)->whereYear('transfer_date', $year);
            })
            ->when($time === 'yearly' && $year, function ($query) use ($year) {
                return $query->whereYear('transfer_date', $year);
            })->paginate(10);

        // Proses pengelompokan minggu
        $incomeDates = Income::selectRaw('MIN(transfer_date) as start_date, MAX(transfer_date) as end_date')->first();
        $startDate = $incomeDates->start_date;
        $endDate = $incomeDates->end_date;
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        $incomesInWeeks = [];
        $availableYears = [];
        $currentStartDate = $startDate->copy();
        $currentEndDate = $currentStartDate->copy()->addDays(6);

        // ========================================================
        // GETTING DATA FOR FIILTERING AND SEARCHING
        // ========================================================
        while ($currentStartDate->lte($endDate)) {
            $weeklyData = $allIncomes->filter(function ($income) use ($currentStartDate, $currentEndDate) {
                $incomeDate = Carbon::parse($income->transfer_date);
                return $incomeDate->between($currentStartDate, $currentEndDate);
            });

            if ($weeklyData->isNotEmpty()) {
                $incomesInWeeks[] = [
                    'label' => $currentStartDate->format('M') . ' Min ' . ceil($currentStartDate->day / 7) . ' (' . $currentStartDate->format('d M Y') . ' - ' . $currentEndDate->format('d M Y') . ')',
                    'value' => $currentStartDate->format('Y-m-d'),
                    // 'start' => $currentStartDate->format('Y-m-d'),
                    // 'end' => $currentEndDate->format('Y-m-d'),
                    // 'data' => $weeklyData,
                ];
            }

            $currentStartDate->addDays(7);
            $currentEndDate->addDays(7);
        }

        foreach ($allIncomes as $income) {
            $year = (string) Carbon::parse($income->transfer_date)->year;
            if (!in_array($year, $availableYears)) {
                $availableYears[] = $year;
            }
        }
        // ========================================================
        // GETTING DATA FOR FIILTERING AND SEARCHING
        // ========================================================

        return Inertia::render('BoardMember/Income/Index', [
            'incomes' => $incomes,
            'topTenAmbassadors' => $topTenAmbassadors,
            'availableYears' => $availableYears,
            'availableMonths' => $availableMonths,
            'availableTeamCodes' => $availableTeamCodes,
            'availableAmbassadors' => $availableAmbassadors,
            'incomesInWeeks' => $incomesInWeeks,
            'filters' => [
                'name' => $name,
                'teamCode' => $teamCode,
                'time' => $time,
                'startRange' => $startRange,
                'endRange' => $endRange,
                'week' => $week,
                'month' => $month,
                'year' => $year,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $availableAmbassadors = Ambassador::select('id', 'name')->get();
        $availableAmbassadors = $availableAmbassadors->map(function ($ambassador) {
            return [
                'label' => $ambassador->name,
                'value' => $ambassador->id,
            ];
        });

        return Inertia::render('BoardMember/Income/Create', [
            'availableAmbassadors' => $availableAmbassadors,
            'paymentMethods' => $this->paymentMethods,
            'donationTypes' => $this->donationTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ambassador' => 'required|integer',
            'transfer_date' => 'required|date',
            'amount' => 'required|numeric|gt:500',
            'donor' => 'required|string|min:1',
            'team' => 'nullable|integer',
            'payment_method' => 'required|string',
            'type' => 'required|string',
            'on_behalf_of' => 'nullable|string|min:1',
        ]);

        Income::create([
            'ambassador_id' => $request->ambassador,
            'transfer_date' => $request->transfer_date,
            'amount' => $request->amount,
            'donor' => $request->donor,
            'team' => $request->team,
            'payment_method' => $request->payment_method,
            'type' => $request->type,
            'on_behalf_of' => $request->on_behalf_of,
        ]);

        return to_route('board_member.incomes.index')->with('message', 'Berhasil menambah pendapatan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Income $income)
    {
        $availableAmbassadors = Ambassador::select('id', 'name')->get();
        $availableAmbassadors = $availableAmbassadors->map(function ($ambassador) {
            return [
                'label' => $ambassador->name,
                'value' => $ambassador->id,
            ];
        });

        return Inertia::render('BoardMember/Income/Edit', [
            'income' => $income,
            'ambassador' => $income->ambassador,
            'availableAmbassadors' => $availableAmbassadors,
            'paymentMethods' => $this->paymentMethods,
            'donationTypes' => $this->donationTypes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Income $income)
    {
        $request->validate([
            'ambassador' => 'required|integer',
            'transfer_date' => 'required|date',
            'amount' => 'required|numeric|gt:500',
            'donor' => 'required|string|min:1',
            'team' => 'nullable|integer',
            'payment_method' => 'required|string',
            'type' => 'required|string',
            'on_behalf_of' => 'nullable|string|min:1',
        ]);

        $income->update([
            'ambassador_id' => $request->ambassador,
            'transfer_date' => $request->transfer_date,
            'amount' => $request->amount,
            'donor' => $request->donor,
            'team' => $request->team,
            'payment_method' => $request->payment_method,
            'type' => $request->type,
            'on_behalf_of' => $request->on_behalf_of,
        ]);

        return to_route('board_member.incomes.index')->with('message', 'Berhasil mengubah pendapatan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Income $income)
    {
        $income->delete();
        return to_route('board_member.incomes.index')->with('message', 'Berhasil menghapus pendapatan');
    }

    public function destroyMultiple(Request $request): RedirectResponse
    {
        $ids = $request->input('ids');
        Income::whereIn('id', $ids)->delete();
        return to_route('board_member.incomes.index')->with('message', 'Berhasil menghapus beberapa pendapatan');
    }
}
