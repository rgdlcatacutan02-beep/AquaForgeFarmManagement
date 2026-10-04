<?php

namespace App\Http\Controllers;

use App\Models\BreedingEvent;
use App\Models\OffspringBatch;
use App\Models\Species;
use App\Models\Tank;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OffspringBatchController extends Controller
{
    public function index(Request $request): View
    {
        $query = OffspringBatch::with(['species', 'tank', 'breedingEvent.maleLivestock', 'breedingEvent.femaleLivestock']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('batch_code', 'like', "%{$s}%")
                  ->orWhere('variety', 'like', "%{$s}%")
                  ->orWhere('notes', 'like', "%{$s}%");
            });
        }

        if ($request->filled('species_id')) {
            $query->where('species_id', $request->species_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tank_id')) {
            $query->where('tank_id', $request->tank_id);
        }

        $batches = $query->latest('birth_or_hatch_date')->paginate(15)->withQueryString();
        $species = Species::where('active', true)->orderBy('name')->get();
        $tanks = Tank::orderBy('tank_code')->get();

        return view('batches.index', compact('batches', 'species', 'tanks'));
    }

    public function show(OffspringBatch $batch): View
    {
        $batch->load([
            'species',
            'tank',
            'breedingEvent.maleLivestock',
            'breedingEvent.femaleLivestock',
            'breedingEvent.tank',
            'batchLogs' => fn ($q) => $q->latest('log_date'),
            'feedingLogs',
        ]);

        return view('batches.show', compact('batch'));
    }

    public function create(Request $request): View
    {
        $species = Species::where('active', true)->orderBy('name')->get();
        $tanks = Tank::where('status', 'ACTIVE')->orderBy('tank_code')->get();
        
        $breedingQuery = BreedingEvent::with(['maleLivestock', 'femaleLivestock', 'species']);
        if ($request->filled('breeding_id')) {
            $breedingQuery->where('id', $request->breeding_id);
        }
        $breedings = $breedingQuery->latest('start_date')->get();

        return view('batches.create', compact('species', 'tanks', 'breedings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'batch_code' => 'required|string|max:50|unique:offspring_batches,batch_code',
            'species_id' => 'required|exists:species,id',
            'breeding_event_id' => 'nullable|exists:breeding_events,id',
            'variety' => 'nullable|string|max:100',
            'birth_or_hatch_date' => 'nullable|date',
            'initial_count' => 'required|integer|min:0',
            'tank_id' => 'nullable|exists:tanks,id',
            'status' => 'required|string',
            'grade' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $validated['current_count'] = $validated['initial_count'];
        $validated['death_count'] = 0;
        $validated['cull_count'] = 0;
        $validated['available_count'] = $validated['initial_count'];

        $batch = OffspringBatch::create($validated);

        return redirect()->route('batches.show', $batch)->with('success', 'Offspring batch registered successfully.');
    }

    public function edit(OffspringBatch $batch): View
    {
        $species = Species::where('active', true)->orderBy('name')->get();
        $tanks = Tank::orderBy('tank_code')->get();
        $breedings = BreedingEvent::with(['maleLivestock', 'femaleLivestock'])->where('species_id', $batch->species_id)->latest('start_date')->get();

        return view('batches.edit', compact('batch', 'species', 'tanks', 'breedings'));
    }

    public function update(Request $request, OffspringBatch $batch)
    {
        $validated = $request->validate([
            'batch_code' => 'required|string|max:50|unique:offspring_batches,batch_code,' . $batch->id,
            'species_id' => 'required|exists:species,id',
            'breeding_event_id' => 'nullable|exists:breeding_events,id',
            'variety' => 'nullable|string|max:100',
            'birth_or_hatch_date' => 'nullable|date',
            'tank_id' => 'nullable|exists:tanks,id',
            'status' => 'required|string',
            'grade' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $batch->update($validated);

        return redirect()->route('batches.show', $batch)->with('success', 'Offspring batch updated successfully.');
    }

    public function destroy(OffspringBatch $batch)
    {
        $code = $batch->batch_code;
        $batch->delete();

        return redirect()->route('batches.index')->with('success', "Batch '{$code}' archived successfully.");
    }

    public function recordLog(Request $request, OffspringBatch $batch)
    {
        $validated = $request->validate([
            'type' => 'required|in:MORTALITY,CULLING',
            'quantity' => 'required|integer|min:1',
            'log_date' => 'required|date',
            'reason' => 'nullable|string|max:150',
            'notes' => 'nullable|string',
        ]);

        if ($validated['quantity'] > $batch->current_count) {
            return back()->with('error', "Quantity ({$validated['quantity']}) cannot exceed current population ({$batch->current_count}).");
        }

        if ($validated['type'] === 'MORTALITY') {
            $batch->recordMortality($validated['quantity'], $validated['log_date'], $validated['reason'], $validated['notes']);
            $msg = "Recorded {$validated['quantity']} mortality in batch {$batch->batch_code}.";
        } else {
            $batch->recordCulling($validated['quantity'], $validated['log_date'], $validated['reason'], $validated['notes']);
            $msg = "Recorded {$validated['quantity']} culls in batch {$batch->batch_code}.";
        }

        return redirect()->route('batches.show', $batch)->with('success', $msg);
    }
}
