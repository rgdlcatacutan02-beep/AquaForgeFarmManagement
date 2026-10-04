<?php

namespace App\Http\Controllers;

use App\Models\BreedingEvent;
use App\Models\Expense;
use App\Models\Livestock;
use App\Models\MaintenanceLog;
use App\Models\OffspringBatch;
use App\Models\Sale;
use App\Models\Species;
use App\Models\Tank;
use App\Models\Task;
use App\Models\WaterLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalLivestock = Livestock::whereNotIn('status', ['DECEASED', 'CULLED', 'SOLD'])->count();
        $totalTanks = Tank::where('status', 'ACTIVE')->count();
        $activeBreeding = BreedingEvent::where('status', 'ACTIVE')->count();

        $availableLivestock = Livestock::where('status', 'AVAILABLE')->count();
        $availableBatchOffspring = (int) OffspringBatch::where('status', '!=', 'SOLD_OUT')->sum('available_count');
        $availableForSale = $availableLivestock + $availableBatchOffspring;

        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        $monthlyRevenue = (float) Sale::where('status', '!=', 'CANCELLED')
            ->whereBetween('sale_date', [$startOfMonth, $endOfMonth])
            ->sum('total');

        $monthlyExpenses = (float) Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // Species Distribution for Chart.js
        $speciesDistribution = Species::where('active', true)->withCount([
            'livestock' => function ($query) {
                $query->whereNotIn('status', ['DECEASED', 'CULLED', 'SOLD']);
            },
        ])->get()->map(function ($s) {
            $batchCount = (int) OffspringBatch::where('species_id', $s->id)->where('status', '!=', 'SOLD_OUT')->sum('current_count');

            return [
                'name' => $s->name,
                'count' => $s->livestock_count + $batchCount,
            ];
        });

        // Tanks Status
        $tanks = Tank::with(['latestWaterLog', 'livestock.species', 'offspringBatches.species'])
            ->orderBy('tank_code')
            ->get();

        // Active Breeding
        $activeBreedingEvents = BreedingEvent::with(['species', 'tank', 'maleLivestock', 'femaleLivestock'])
            ->where('status', 'ACTIVE')
            ->latest('start_date')
            ->take(5)
            ->get();

        // Tasks / Reminders
        $tasks = Task::with('tank')
            ->where('is_completed', false)
            ->orderBy('due_date')
            ->take(5)
            ->get();

        // Recent Activity
        $recentLivestock = Livestock::with(['species', 'tank'])->latest()->take(3)->get()->map(function ($item) {
            return [
                'type' => 'livestock',
                'title' => "Added livestock {$item->livestock_code} ({$item->species?->name})",
                'subtitle' => $item->tank ? "Assigned to {$item->tank->tank_code}" : 'Unassigned',
                'time' => $item->created_at,
            ];
        });

        $recentWaterLogs = WaterLog::with('tank')->latest('recorded_at')->take(3)->get()->map(function ($item) {
            return [
                'type' => 'water',
                'title' => "Water tested for {$item->tank?->tank_code}",
                'subtitle' => "pH {$item->ph} | {$item->temperature}°C | Status: {$item->status}",
                'time' => $item->recorded_at,
            ];
        });

        $recentMaintenance = MaintenanceLog::with('tank')->latest('performed_at')->take(3)->get()->map(function ($item) {
            return [
                'type' => 'maintenance',
                'title' => str_replace('_', ' ', $item->maintenance_type)." on {$item->tank?->tank_code}",
                'subtitle' => $item->water_change_percentage ? "{$item->water_change_percentage}% water change" : ($item->notes ?? 'Maintenance performed'),
                'time' => $item->performed_at,
            ];
        });

        $recentSales = Sale::latest('sale_date')->take(3)->get()->map(function ($item) {
            return [
                'type' => 'sale',
                'title' => 'Sale '.$item->sale_number.' - ₱'.number_format($item->total, 2),
                'subtitle' => "Status: {$item->status} | {$item->payment_status}",
                'time' => $item->created_at,
            ];
        });

        $recentActivity = collect()
            ->concat($recentLivestock)
            ->concat($recentWaterLogs)
            ->concat($recentMaintenance)
            ->concat($recentSales)
            ->sortByDesc('time')
            ->take(6)
            ->values();

        return view('dashboard', compact(
            'totalLivestock',
            'totalTanks',
            'activeBreeding',
            'availableForSale',
            'monthlyRevenue',
            'monthlyExpenses',
            'speciesDistribution',
            'tanks',
            'activeBreedingEvents',
            'tasks',
            'recentActivity'
        ));
    }
}