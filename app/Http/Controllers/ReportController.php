<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Livestock;
use App\Models\OffspringBatch;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Species;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        // 1. Species & Livestock Distribution (Section 33)
        $livestockBySpecies = Species::withCount([
            'livestock' => fn ($q) => $q->whereIn('status', ['AVAILABLE', 'BREEDER', 'HOLD']),
            'offspringBatches' => fn ($q) => $q->where('status', 'ACTIVE'),
        ])->withSum([
            'offspringBatches as active_fry_count' => fn ($q) => $q->where('status', 'ACTIVE')
        ], 'current_count')->get();

        $totalActiveAdults = Livestock::whereIn('status', ['AVAILABLE', 'BREEDER', 'HOLD'])->count();
        $totalActiveFry = (int) OffspringBatch::where('status', 'ACTIVE')->sum('current_count');

        // 2. Farm Financial Overview (Section 31)
        $totalRevenue = (float) Sale::where('status', '!=', 'CANCELLED')->sum('total');
        $totalExpenses = (float) Expense::sum('amount');
        $netResult = $totalRevenue - $totalExpenses;

        $monthRevenue = (float) Sale::where('status', '!=', 'CANCELLED')
            ->whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->sum('total');

        $monthExpenses = (float) Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $monthNet = $monthRevenue - $monthExpenses;

        // 3. Sales Metrics
        $totalOrders = Sale::where('status', '!=', 'CANCELLED')->count();
        $totalUnitsSold = (int) SaleItem::whereHas('sale', fn ($sq) => $sq->where('status', '!=', 'CANCELLED'))->sum('quantity');
        $averageOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        // 4. Breeding & Fry Survival Performance (Section 32)
        $batches = OffspringBatch::with('species')->get();
        $totalInitialFry = (int) $batches->sum('initial_count');
        $totalDeaths = (int) $batches->sum('death_count');
        $totalCulls = (int) $batches->sum('cull_count');
        $survivalRate = $totalInitialFry > 0 ? round((($totalInitialFry - $totalDeaths) / $totalInitialFry) * 100, 1) : 0;

        // 5. Expense Breakdown by Category (Section 34)
        $expensesByCategory = Expense::selectRaw('category, sum(amount) as total_amount, count(*) as count')
            ->groupBy('category')
            ->orderByDesc('total_amount')
            ->get();

        return view('reports.index', compact(
            'livestockBySpecies',
            'totalActiveAdults',
            'totalActiveFry',
            'totalRevenue',
            'totalExpenses',
            'netResult',
            'monthRevenue',
            'monthExpenses',
            'monthNet',
            'totalOrders',
            'totalUnitsSold',
            'averageOrderValue',
            'totalInitialFry',
            'totalDeaths',
            'totalCulls',
            'survivalRate',
            'expensesByCategory'
        ));
    }
}
