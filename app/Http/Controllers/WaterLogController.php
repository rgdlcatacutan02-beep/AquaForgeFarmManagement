<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use App\Models\WaterLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WaterLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = WaterLog::with('tank');

        if ($request->filled('tank_id')) {
            $query->where('tank_id', $request->tank_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->latest('recorded_at')->paginate(20)->withQueryString();
        $tanks = Tank::orderBy('tank_code')->get();

        return view('water_logs.index', compact('logs', 'tanks'));
    }

    public function create(Request $request): View
    {
        $tanks = Tank::orderBy('tank_code')->get();
        $selectedTankId = $request->query('tank_id');

        return view('water_logs.create', compact('tanks', 'selectedTankId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tank_id' => 'required|exists:tanks,id',
            'recorded_at' => 'required|date',
            'temperature' => 'nullable|numeric',
            'ph' => 'nullable|numeric',
            'ammonia' => 'nullable|numeric',
            'nitrite' => 'nullable|numeric',
            'nitrate' => 'nullable|numeric',
            'tds' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = WaterLog::calculateStatus(
            $validated['ph'] ?? null,
            $validated['ammonia'] ?? null,
            $validated['nitrite'] ?? null,
            $validated['nitrate'] ?? null
        );

        WaterLog::create($validated);

        return redirect()->route('tanks.show', $validated['tank_id'])->with('success', 'Water test recorded successfully.');
    }

    public function destroy(WaterLog $waterLog)
    {
        $tankId = $waterLog->tank_id;
        $waterLog->delete();

        return redirect()->back()->with('success', 'Water test log removed successfully.');
    }
}
