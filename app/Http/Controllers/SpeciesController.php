<?php

namespace App\Http\Controllers;

use App\Models\Species;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpeciesController extends Controller
{
    public function index(Request $request): View
    {
        $query = Species::withCount(['livestock', 'offspringBatches', 'breedingEvents']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('scientific_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->status === 'active');
        }

        $species = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('species.index', compact('species'));
    }

    public function create(): View
    {
        return view('species.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:species,name',
            'scientific_name' => 'nullable|string|max:150',
            'description' => 'nullable|string',
            'active' => 'nullable|boolean',
        ]);

        $validated['active'] = $request->boolean('active');
        $species = Species::create($validated);

        return redirect()->route('species.index')->with('success', "Species '{$species->name}' created successfully.");
    }

    public function show(Species $species): View
    {
        $species->loadCount(['livestock', 'offspringBatches', 'breedingEvents']);
        $livestock = $species->livestock()->with('tank')->latest()->take(10)->get();
        $batches = $species->offspringBatches()->with('tank')->latest()->take(10)->get();

        return view('species.show', compact('species', 'livestock', 'batches'));
    }

    public function edit(Species $species): View
    {
        return view('species.edit', compact('species'));
    }

    public function update(Request $request, Species $species)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:species,name,' . $species->id,
            'scientific_name' => 'nullable|string|max:150',
            'description' => 'nullable|string',
            'active' => 'nullable|boolean',
        ]);

        $validated['active'] = $request->boolean('active');
        $species->update($validated);

        return redirect()->route('species.index')->with('success', "Species '{$species->name}' updated successfully.");
    }

    public function destroy(Species $species)
    {
        if ($species->livestock()->exists() || $species->offspringBatches()->exists() || $species->breedingEvents()->exists()) {
            return back()->with('error', "Cannot delete species '{$species->name}' because active livestock, batches, or breeding events are assigned to it.");
        }

        $name = $species->name;
        $species->delete();

        return redirect()->route('species.index')->with('success', "Species '{$name}' deleted successfully.");
    }
}
