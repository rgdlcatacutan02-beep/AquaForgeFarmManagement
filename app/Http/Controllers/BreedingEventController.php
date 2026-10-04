<?php

namespace App\Http\Controllers;

use App\Models\BreedingEvent;
use App\Models\Livestock;
use App\Models\Species;
use App\Models\Tank;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BreedingEventController extends Controller
{
    public function index(Request $request): View
    {
        $query = BreedingEvent::with(['species', 'tank', 'maleLivestock', 'femaleLivestock', 'offspringBatches']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('breeding_code', 'like', "%{$s}%")
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

        $events = $query->latest('start_date')->paginate(15)->withQueryString();
        $species = Species::where('active', true)->orderBy('name')->get();
        $tanks = Tank::orderBy('tank_code')->get();

        return view('breeding.index', compact('events', 'species', 'tanks'));
    }

    public function show(BreedingEvent $breeding): View
    {
        $breeding->load([
            'species',
            'tank',
            'maleLivestock.tank',
            'femaleLivestock.tank',
            'offspringBatches.tank',
            'offspringBatches.batchLogs',
        ]);

        return view('breeding.show', compact('breeding'));
    }

    public function create(Request $request): View
    {
        $species = Species::where('active', true)->orderBy('name')->get();
        $tanks = Tank::where('status', 'ACTIVE')->orderBy('tank_code')->get();
        
        $maleQuery = Livestock::where('sex', 'MALE')->whereIn('status', ['BREEDER', 'GROWOUT', 'DISPLAY', 'AVAILABLE']);
        $femaleQuery = Livestock::where('sex', 'FEMALE')->whereIn('status', ['BREEDER', 'GROWOUT', 'DISPLAY', 'AVAILABLE']);

        if ($request->filled('species_id')) {
            $maleQuery->where('species_id', $request->species_id);
            $femaleQuery->where('species_id', $request->species_id);
        }

        $males = $maleQuery->orderBy('livestock_code')->get();
        $females = $femaleQuery->orderBy('livestock_code')->get();

        return view('breeding.create', compact('species', 'tanks', 'males', 'females'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'breeding_code' => 'required|string|max:50|unique:breeding_events,breeding_code',
            'species_id' => 'required|exists:species,id',
            'tank_id' => 'nullable|exists:tanks,id',
            'male_livestock_id' => 'nullable|exists:livestock,id',
            'female_livestock_id' => 'nullable|exists:livestock,id',
            'start_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'actual_birth_or_hatch_date' => 'nullable|date',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $event = BreedingEvent::create($validated);

        return redirect()->route('breeding.show', $event)->with('success', 'Breeding event registered successfully.');
    }

    public function edit(BreedingEvent $breeding): View
    {
        $species = Species::where('active', true)->orderBy('name')->get();
        $tanks = Tank::orderBy('tank_code')->get();
        $males = Livestock::where('sex', 'MALE')->where('species_id', $breeding->species_id)->orderBy('livestock_code')->get();
        $females = Livestock::where('sex', 'FEMALE')->where('species_id', $breeding->species_id)->orderBy('livestock_code')->get();

        return view('breeding.edit', compact('breeding', 'species', 'tanks', 'males', 'females'));
    }

    public function update(Request $request, BreedingEvent $breeding)
    {
        $validated = $request->validate([
            'breeding_code' => 'required|string|max:50|unique:breeding_events,breeding_code,' . $breeding->id,
            'species_id' => 'required|exists:species,id',
            'tank_id' => 'nullable|exists:tanks,id',
            'male_livestock_id' => 'nullable|exists:livestock,id',
            'female_livestock_id' => 'nullable|exists:livestock,id',
            'start_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'actual_birth_or_hatch_date' => 'nullable|date',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $breeding->update($validated);

        return redirect()->route('breeding.show', $breeding)->with('success', 'Breeding event updated successfully.');
    }

    public function destroy(BreedingEvent $breeding)
    {
        if ($breeding->offspringBatches()->exists()) {
            return back()->with('error', 'Cannot delete breeding event that has recorded offspring batches.');
        }

        $code = $breeding->breeding_code;
        $breeding->delete();

        return redirect()->route('breeding.index')->with('success', "Breeding event '{$code}' deleted successfully.");
    }
}
