<?php

namespace App\Http\Controllers\BoardMember;

use App\Http\Controllers\Controller;
use App\Models\Ambassador;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class IncomeController extends Controller
{
    private function getIncomeChartData($incomesQuery, $time, $week, $month, $year, $startRange, $endRange)
    {
        $chartData = [];
        $data = [];
        if ($time === 'weekly' && $week) {
            $start = Carbon::parse($week);
            $end = $start->copy()->addDays(6);

            // Ambil data pendapatan per hari
            $data = (clone $incomesQuery)
                ->selectRaw('DATE(transfer_date) as date, SUM(amount) as total_amount')
                ->whereBetween('transfer_date', [$start, $end])
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Format hasil ke chart data
            $chartData = $data->map(function ($item) {
                $label = Carbon::parse($item->date)->translatedFormat('d D');
                return [
                    'label' => $label,
                    'value' => (int) $item->total_amount,
                ];
            });
        } elseif ($time === 'yearly' && $year) {
            // Ambil data pendapatan per bulan
            $data = (clone $incomesQuery)
                ->selectRaw('MONTH(transfer_date) as month, SUM(amount) as total_amount')
                ->whereYear('transfer_date', $year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Format hasil ke chart data
            $chartData = $data->map(function ($item) {
                $label = Carbon::create(null, $item->month)->translatedFormat('M');
                return [
                    'label' => $label,
                    'value' => (int) $item->total_amount,
                ];
            });
        } elseif ($time === 'default' || $time === 'monthly' || $time === 'custom') {
            if ($time === 'default') {
                // Ambil data pendapatan per bulan
                $data = (clone $incomesQuery)
                    ->selectRaw('DATE_FORMAT(transfer_date, "%b %y") as period, SUM(amount) as total_amount')
                    ->groupBy('period')
                    ->orderByRaw('MIN(transfer_date)')
                    ->get();

                // format hasil
                $chartData = $data->map(function ($item) {
                    return [
                        'label' => $item->period,
                        'value' => (int) $item->total_amount,
                    ];
                });
            } elseif ($time === 'monthly' && $month && $year) {
                // Ambil data pendapatan per hari dalam bulan tersebut
                $data = (clone $incomesQuery)
                    ->selectRaw('DATE(transfer_date) as date, SUM(amount) as total_amount')
                    ->whereMonth('transfer_date', $month)
                    ->whereYear('transfer_date', $year)
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();

                // format hasil
                $chartData = $data->map(function ($item) {
                    return [
                        'label' => Carbon::parse($item->date)->translatedFormat('d M'),
                        'value' => (int) $item->total_amount,
                    ];
                });
            } elseif ($time === 'custom' && $startRange && $endRange) {
                $start = Carbon::parse($startRange);
                $end = Carbon::parse($endRange);

                if ($start->diffInDays($end) <= 31) {
                    // Ambil data pendapatan per hari
                    $data = (clone $incomesQuery)
                        ->selectRaw('DATE(transfer_date) as date, SUM(amount) as total_amount')
                        ->whereBetween('transfer_date', [$start, $end])
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get();

                    // Format hasil
                    $chartData = $data->map(function ($item) {
                        return [
                            'label' => Carbon::parse($item->date)->translatedFormat('d M'),
                            'value' => (int) $item->total_amount,
                        ];
                    });
                } else if ($start->diffInDays($end) >= 730) {
                    // Ambil data pendapatan per tahun
                    $data = (clone $incomesQuery)
                        ->selectRaw('YEAR(transfer_date) as year, SUM(amount) as total_amount')
                        ->whereBetween('transfer_date', [$start, $end])
                        ->groupBy('year')
                        ->orderBy('year')
                        ->get();

                    // Format hasil
                    $chartData = $data->map(function ($item) {
                        return [
                            'label' => $item->year,
                            'value' => (int) $item->total_amount,
                        ];
                    });
                } else {
                    // Ambil data pendapatan per bulan
                    $data = (clone $incomesQuery)
                        ->selectRaw('DATE_FORMAT(transfer_date, "%b %y") as period, SUM(amount) as total_amount')
                        ->whereBetween('transfer_date', [$start, $end])
                        ->groupBy('period')
                        ->orderByRaw('MIN(transfer_date)')
                        ->get();

                    // Format hasil
                    $chartData = $data->map(function ($item) {
                        return [
                            'label' => $item->period,
                            'value' => (int) $item->total_amount,
                        ];
                    });
                }
            }
        }

        return $chartData;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        // ========================================================
        // VALIDATING AND GETTING DATA FOR FILTERING AND SEARCHING
        // ========================================================
        $name = $request->input('name') ?? 'default';
        $teamCode = $request->input('team_code') ?? 'default';
        $time = $request->input('time') ?? 'default';
        $startRange = $request->input('start_range');
        $endRange = $request->input('end_range');
        $week = $request->input('week');
        $month = $request->input('month');
        $year = $request->input('year');
        $countPerPage = $request->input('count_per_page');

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
        } else {
            $name = substr($name, strpos($name, ' ') + 1);
        }

        // ========================================================
        // VALIDATING AND GETTING DATA FOR FILTERING AND SEARCHING
        // ========================================================
        $availableTeamCodes = Ambassador::select('code')
            ->distinct()
            ->orderBy('code')
            ->get()
            ->pluck('code')
            ->toArray();
        $availableTeamCodes = array_map(function ($code) {
            return substr($code, 0, 2);
        }, $availableTeamCodes);
        $availableTeamCodes = array_unique($availableTeamCodes);
        $availableAmbassadors = Ambassador::select('id', 'name', 'code')->get();
        $availableAmbassadors = $availableAmbassadors->map(function ($ambassador) {
            return [
                'label' => $ambassador->code . " " . $ambassador->name,
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

        // CREATE BASE CODE FOR QUERY
        $incomesQuery = Income::with('ambassador')
            ->when($name, function ($query, $name) {
                return $query->whereHas('ambassador', function ($query) use ($name) {
                    $query->where('name', 'like', '%' . $name . '%');
                });
            })
            ->when($teamCode, function ($query, $teamCode) {
                return $query->whereHas('ambassador', function ($query) use ($teamCode) {
                    $query->where('code', 'like', $teamCode . '%');
                });
            });

        // GET INCOME BY FILTERING
        $countPerPage = (int)($request->count_per_page ?? 10);
        $newIncomesQuery = $incomesQuery
            ->when($time === 'custom' && $startRange && $endRange, function ($query) use ($startRange, $endRange) {
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
            });

        $incomeTotalAmount = (clone $newIncomesQuery)
            ->sum('amount');

        // GET INCOME WITH PAGINATE
        $incomes = (clone $newIncomesQuery)
            ->orderByDesc('created_at')
            ->paginate($countPerPage);

        // GET TOP 10 AMBASSADORS BY AMOUNT
        $topTenAmbassadors = (clone $newIncomesQuery)
            ->selectRaw('ambassador_id, SUM(amount) as total_amount')
            ->groupBy('ambassador_id')
            ->orderByDesc('total_amount')
            ->take(10)
            ->get();

        // GET TOP 10 DONORS BY AMOUNT
        $topTenDonors = (clone $newIncomesQuery)
            ->selectRaw('ambassador_id, donor, SUM(amount) as total_amount')
            ->groupBy('ambassador_id', 'donor')
            ->orderByDesc('total_amount')
            ->take(10)
            ->get();

        $chartData = $this->getIncomeChartData(clone $newIncomesQuery, $time ?? 'default', $week, $month, $year, $startRange, $endRange);

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
        // PROCESSING INCOME DATA TO GET WEEKLY DATA
        // ========================================================
        $allIncomes = (clone $incomesQuery)->orderByDesc('created_at')->get();
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

        return Inertia::render('BoardMember/Income/Index', [
            'incomes' => $incomes,
            'topTenAmbassadors' => $topTenAmbassadors,
            'topTenDonors' => $topTenDonors,
            'availableYears' => $availableYears,
            'availableMonths' => $availableMonths,
            'availableTeamCodes' => $availableTeamCodes,
            'availableAmbassadors' => $availableAmbassadors,
            'incomesInWeeks' => $incomesInWeeks,
            'incomeTotalAmount' => $incomeTotalAmount,
            'chartData' => $chartData,
            'filters' => $request->all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $availableAmbassadors = Ambassador::select('id', 'name', 'code')->get();
        $availableAmbassadors = $availableAmbassadors->map(function ($ambassador) {
            return [
                'label' => $ambassador->code . " " . $ambassador->name,
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
            'proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:1024',
        ]);

        if ($request->hasFile('proof')) {
            $proof = $request->file('proof');
            $donor = str_replace(' ', '-', $request->donor);
            $fileName = $request->transfer_date . '-' . $donor . '-' . substr(md5(mt_rand()), 0, 10) . '.' . $proof->getClientOriginalExtension();

            $proof->storeAs('images/proof', $fileName, 'public');
        }

        Income::create([
            'ambassador_id' => $request->ambassador,
            'transfer_date' => $request->transfer_date,
            'amount' => $request->amount,
            'donor' => $request->donor,
            'team' => $request->team,
            'payment_method' => $request->payment_method,
            'type' => $request->type,
            'on_behalf_of' => $request->on_behalf_of,
            'proof' => isset($fileName) ? $fileName : null,
        ]);

        return to_route('board_member.incomes.index')->with('message', 'Berhasil menambah data pendapatan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Income $income)
    {
        $availableAmbassadors = Ambassador::select('id', 'name', 'code')->get();
        $availableAmbassadors = $availableAmbassadors->map(function ($ambassador) {
            return [
                'label' => $ambassador->code . " " . $ambassador->name,
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
            'proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:1024',
        ]);

        if ($request->hasFile('proof')) {
            if ($income->proof) {
                $filePath = 'images/proof/' . $income->proof;
                Storage::disk('public')->delete($filePath);
            }

            $proof = $request->file('proof');
            $donor = str_replace(' ', '-', $request->donor);
            $fileName = $request->transfer_date . '-' . $donor . '-' . substr(md5(mt_rand()), 0, 10) . '.' . $proof->getClientOriginalExtension();

            $proof->storeAs('images/proof', $fileName, 'public');
            $income->proof = $fileName;
        }

        $income->update([
            'ambassador_id' => $request->ambassador,
            'transfer_date' => $request->transfer_date,
            'amount' => $request->amount,
            'donor' => $request->donor,
            'team' => $request->team,
            'payment_method' => $request->payment_method,
            'type' => $request->type,
            'on_behalf_of' => $request->on_behalf_of,
            'proof' => isset($fileName) ? $fileName : $income->proof,
        ]);

        return to_route('board_member.incomes.index')->with('message', 'Berhasil mengubah data pendapatan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Income $income)
    {
        if ($income->proof) {
            $filePath = 'images/proof/' . $income->proof;
            Storage::disk('public')->delete($filePath);
        }
        $income->delete();

        return to_route('board_member.incomes.index')->with('message', 'Berhasil menghapus data pendapatan');
    }

    public function destroyMultiple(Request $request): RedirectResponse
    {
        $ids = $request->input('ids');
        $incomes = Income::whereIn('ids', $ids)->get();

        foreach ($incomes as $income) {
            if ($income->proof) {
                $filePath = 'images/proof/' . $income->proof;
                Storage::disk('public')->delete($filePath);
            }

            if ($income->proof) {
                $filePath = 'images/proof/' . $income->proof;
                Storage::disk('public')->delete($filePath);
            }

            if ($income->proof) {
                $filePath = 'images/proof/' . $income->proof;
                Storage::disk('public')->delete($filePath);
            }

            $income->delete();
        }

        return to_route('board_member.incomes.index')->with('message', 'Berhasil menghapus beberapa data pendapatan');
    }
}
