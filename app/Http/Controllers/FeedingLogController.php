<?php

namespace App\Http\Controllers;

use App\Models\FeedingLog;
use App\Models\Tank;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedingLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = FeedingLog::with(['tank', 'livestock', 'offspringBatch']);

        if ($request->filled('tank_id')) {
            $query->where('tank_id', $request->tank_id);
        }

        $logs = $query->latest('fed_at')->paginate(20)->withQueryString();
        $tanks = Tank::orderBy('tank_code')->get();

        return view('feeding_logs.index', compact('logs', 'tanks'));
    }

    public function create(Request $request): View
    {
        $tanks = Tank::with(['livestock', 'offspringBatches'])->orderBy('tank_code')->get();
        $selectedTankId = $request->query('tank_id');

        return view('feeding_logs.create', compact('tanks', 'selectedTankId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tank_id' => 'required|exists:tanks,id',
            'food' => 'required|string|max:100',
            'quantity' => 'nullable|string|max:50',
            'fed_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        FeedingLog::create($validated);

        return redirect()->route('tanks.show', $validated['tank_id'])->with('success', 'Feeding logged successfully.');
    }

    public function destroy(FeedingLog $feeding)
    {
        $feeding->delete();

        return redirect()->back()->with('success', 'Feeding record removed successfully.');
    }
}
