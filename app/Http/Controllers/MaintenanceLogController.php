<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceLog;
use App\Models\Tank;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = MaintenanceLog::with('tank');

        if ($request->filled('tank_id')) {
            $query->where('tank_id', $request->tank_id);
        }

        $logs = $query->latest('performed_at')->paginate(20)->withQueryString();
        $tanks = Tank::orderBy('tank_code')->get();

        return view('maintenance_logs.index', compact('logs', 'tanks'));
    }

    public function create(Request $request): View
    {
        $tanks = Tank::orderBy('tank_code')->get();
        $selectedTankId = $request->query('tank_id');

        return view('maintenance_logs.create', compact('tanks', 'selectedTankId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tank_id' => 'required|exists:tanks,id',
            'maintenance_type' => 'required|string',
            'performed_at' => 'required|date',
            'water_change_percentage' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        MaintenanceLog::create($validated);

        return redirect()->route('tanks.show', $validated['tank_id'])->with('success', 'Maintenance recorded successfully.');
    }

    public function destroy(MaintenanceLog $maintenance)
    {
        $maintenance->delete();

        return redirect()->back()->with('success', 'Maintenance log removed successfully.');
    }
}
