<?php

namespace App\Http\Controllers;

use App\Models\Livestock;
use App\Models\Species;
use App\Models\Tank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LivestockController extends Controller
{
    public function index(Request $request): View
    {
        $query = Livestock::with(['species', 'tank']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('livestock_code', 'like', "%{$s}%")
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

        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        $livestock = $query->latest()->paginate(15)->withQueryString();
        $species = Species::where('active', true)->orderBy('name')->get();
        $tanks = Tank::orderBy('tank_code')->get();

        return view('livestock.index', compact('livestock', 'species', 'tanks'));
    }

    public function show(Livestock $livestock): View
    {
        $livestock->load(['species', 'tank', 'maleBreedingEvents', 'femaleBreedingEvents', 'feedingLogs']);

        return view('livestock.show', compact('livestock'));
    }

    public function create(): View
    {
        $species = Species::where('active', true)->orderBy('name')->get();
        $tanks = Tank::where('status', 'ACTIVE')->orderBy('tank_code')->get();

        return view('livestock.create', compact('species', 'tanks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'livestock_code' => 'required|string|max:50|unique:livestock,livestock_code',
            'species_id' => 'required|exists:species,id',
            'variety' => 'nullable|string|max:100',
            'sex' => 'required|in:MALE,FEMALE,UNKNOWN',
            'date_of_birth' => 'nullable|date',
            'date_acquired' => 'nullable|date',
            'source' => 'nullable|string|max:100',
            'purchase_price' => 'nullable|numeric|min:0',
            'status' => 'required|string',
            'tank_id' => 'nullable|exists:tanks,id',
            'grade' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('livestock_photos', 'public');
        }

        if ($request->has('grading_scores')) {
            $validated['grading_scores'] = array_filter($request->input('grading_scores', []));
        }

        $item = Livestock::create($validated);

        return redirect()->route('livestock.show', $item)->with('success', 'Livestock added successfully.');
    }

    public function edit(Livestock $livestock): View
    {
        $species = Species::where('active', true)->orderBy('name')->get();
        $tanks = Tank::orderBy('tank_code')->get();

        return view('livestock.edit', compact('livestock', 'species', 'tanks'));
    }

    public function update(Request $request, Livestock $livestock)
    {
        $validated = $request->validate([
            'livestock_code' => 'required|string|max:50|unique:livestock,livestock_code,' . $livestock->id,
            'species_id' => 'required|exists:species,id',
            'variety' => 'nullable|string|max:100',
            'sex' => 'required|in:MALE,FEMALE,UNKNOWN',
            'date_of_birth' => 'nullable|date',
            'date_acquired' => 'nullable|date',
            'source' => 'nullable|string|max:100',
            'purchase_price' => 'nullable|numeric|min:0',
            'status' => 'required|string',
            'tank_id' => 'nullable|exists:tanks,id',
            'grade' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('photo')) {
            if ($livestock->photo_path) {
                Storage::disk('public')->delete($livestock->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('livestock_photos', 'public');
        }

        if ($request->has('grading_scores')) {
            $validated['grading_scores'] = array_filter($request->input('grading_scores', []));
        }

        $livestock->update($validated);

        return redirect()->route('livestock.show', $livestock)->with('success', 'Livestock updated successfully.');
    }

    public function destroy(Livestock $livestock)
    {
        $livestock->delete();

        return redirect()->route('livestock.index')->with('success', 'Livestock archived successfully.');
    }
}
