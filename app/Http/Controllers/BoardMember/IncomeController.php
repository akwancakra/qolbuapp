<?php

namespace App\Http\Controllers\BoardMember;

use App\Exports\IncomeExport;
use App\Http\Controllers\Controller;
use App\Models\Ambassador;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response as FacadesResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Browsershot\Browsershot;

class IncomeController extends Controller
{
    private const DATE_FORMATS = [
        'daily' => 'd M y',
        'monthly' => 'M Y',
        'yearly' => 'Y',
    ];

    /**
     * Format data chart menjadi array dengan label dan nilai.
     *
     * @param \Illuminate\Support\Collection $data
     * @param string $dateFormat
     * @return \Illuminate\Support\Collection
     */
    private function formatChartData($data, $dateFormat)
    {
        return $data->map(function ($item) use ($dateFormat) {
            $label = isset($item->date)
                ? Carbon::parse($item->date)->translatedFormat($dateFormat)
                : (isset($item->month) && isset($item->year)
                    ? Carbon::create($item->year, $item->month, 1)->translatedFormat($dateFormat)
                    : ($item->year ?? ''));

            return [
                'label' => $label,
                'value' => (int) $item->total_amount,
            ];
        });
    }

    /**
     * Ambil data visualisasi chart berdasarkan field group dan select.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param array $groupByFields
     * @param array|null $fields
     * @return \Illuminate\Support\Collection
     */
    private function getVisualizationChartData($query, $groupByFields, $fields = null)
    {
        $query = $query->selectRaw(implode(', ', $groupByFields) . ', SUM(amount) as total_amount');

        if ($fields) {
            $query->groupBy($fields)->orderByRaw(implode(', ', $fields));
        }

        return $query->get();
    }

    /**
     * Dapatkan data chart pendapatan berdasarkan tipe chart.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $chartType
     * @return \Illuminate\Support\Collection
     */
    public function getIncomeChartData($query, $chartType)
    {
        $groupByFields = [];
        $fields = [];
        $dateFormat = self::DATE_FORMATS[$chartType] ?? '';

        switch ($chartType) {
            case 'daily':
                $groupByFields = ['DATE(transfer_date) as date'];
                $fields = ['date'];
                break;

            case 'monthly':
                $groupByFields = ['YEAR(transfer_date) as year', 'MONTH(transfer_date) as month'];
                $fields = ['year', 'month'];
                break;

            case 'yearly':
                $groupByFields = ['YEAR(transfer_date) as year'];
                $fields = ['year'];
                break;

            default:
                throw new InvalidArgumentException("Unsupported chart type: $chartType");
        }

        $incomeData = $this->getVisualizationChartData($query, $groupByFields, $fields);
        return $this->formatChartData($incomeData, $dateFormat);
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
        $chart_type = $request->input('chart_type') ?? 'daily';
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
        $newIncomesQuery = (clone $incomesQuery)
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

        $chartData = $this->getIncomeChartData(clone $newIncomesQuery, $chart_type);

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

    public function export(Request $request)
    {
        $type = $request->input('type') ?? 'pdf';
        $ids = $request->input('ids') ?? null;

        $filters = [
            'name' => $request->input('name') ?? 'default',
            'team_code' => $request->input('team_code') ?? 'default',
            'time' => $request->input('time') ?? 'default',
            'start_range' => $request->input('start_range'),
            'end_range' => $request->input('end_range'),
            'week' => $request->input('week'),
            'month' => $request->input('month'),
            'year' => $request->input('year'),
        ];

        if ($filters['team_code'] === 'default') {
            $filters['team_code'] = null;
        }

        if ($filters['time'] === 'default') {
            $filters['time'] =
                $filters['start_range'] =
                $filters['end_range'] =
                $filters['week'] =
                $filters['month'] =
                $filters['year'] = null;
        }

        if ($filters['name'] === 'default') {
            $filters['name'] = null;
        } else {
            $filters['name'] = substr($filters['name'], strpos($filters['name'], ' ') + 1);
        }

        if ($ids && count($ids) > 0) {
            $incomes = Income::with('ambassador')->whereIn('id', $ids)->get();
        } else {
            // CREATE BASE CODE FOR QUERY
            $incomes = Income::with('ambassador')
                ->when($filters['name'], function ($query, $name) {
                    return $query->whereHas('ambassador', function ($query) use ($name) {
                        $query->where('name', 'LIKE', '%' . $name . '%');
                    });
                })
                ->when($filters['team_code'], function ($query, $teamCode) {
                    return $query->whereHas('ambassador', function ($query) use ($teamCode) {
                        $query->where('code', 'LIKE', $teamCode . '%');
                    });
                })
                ->when($filters['time'] === 'custom' && $filters['start_range'] && $filters['end_range'], function ($query) use ($filters) {
                    return $query->whereBetween('transfer_date', [$filters['start_range'], $filters['end_range']]);
                })
                ->when($filters['time'] === 'weekly' && $filters['week'], function ($query) use ($filters) {
                    $start = Carbon::parse($filters['week']);
                    $end = $start->copy()->addDays(6);
                    return $query->whereBetween('transfer_date', [$start, $end]);
                })
                ->when($filters['time'] === 'monthly' && $filters['month'] && $filters['year'], function ($query) use ($filters) {
                    return $query->whereMonth('transfer_date', $filters['month'])->whereYear('transfer_date', $filters['year']);
                })
                ->when($filters['time'] === 'yearly' && $filters['year'], function ($query) use ($filters) {
                    return $query->whereYear('transfer_date', $filters['year']);
                })
                ->orderByDesc('created_at')
                ->get();
        }

        // SETTING UP TEMPLATE FOR REPORT
        $template = view('exports.incomes', ['incomes' => $incomes])->render();

        // EXPORT REPORT BASED ON TYPE
        $filename = Carbon::now();

        if ($type === 'pdf') {
            $pdfData = Browsershot::html($template)
                ->setPaper('a4')
                ->pdf();

            return FacadesResponse::make($pdfData, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.' . $type . '"',
            ]);
        } else if ($type === 'jpeg') {
            $jpegData = Browsershot::html($template)
                ->setScreenshotType('jpeg')
                ->screenshot();

            return FacadesResponse::make($jpegData, 200, [
                'Content-Type' => 'image/jpeg',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.' . $type . '"',
            ]);
        } else if ($type === 'xlsx') {
            return Excel::download(new IncomeExport($incomes), $filename . '.' . $type);
        }

        return response()->json(['error' => 'Invalid type'], 400);
    }
}
