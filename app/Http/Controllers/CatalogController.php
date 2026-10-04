<?php

namespace App\Http\Controllers;

use App\Models\Livestock;
use App\Models\OffspringBatch;
use App\Models\Species;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * Display the public showcase / available fish catalog.
     */
    public function index(Request $request): View
    {
        $owner = User::where('role', 'admin')->first() ?? User::first();

        $query = Livestock::with(['species', 'tank'])
            ->where('status', 'AVAILABLE');

        if ($request->filled('species_id')) {
            $query->where('species_id', $request->species_id);
        }

        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        if ($request->filled('sex')) {
            $query->where('sex', $request->sex);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('variety', 'like', "%{$search}%")
                  ->orWhere('livestock_code', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        if ($sort === 'price_asc') {
            $query->orderBy('purchase_price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('purchase_price', 'desc');
        } else {
            $query->latest();
        }

        $livestock = $query->paginate(24)->withQueryString();
        $speciesList = Species::orderBy('name')->get();

        // Also get saleable batches
        $batches = OffspringBatch::with(['species', 'tank'])
            ->where('status', 'ACTIVE')
            ->where('current_count', '>', 0)
            ->latest()
            ->take(8)
            ->get();

        return view('catalog.index', compact('owner', 'livestock', 'speciesList', 'batches'));
    }

    /**
     * Display a specific available fish specimen.
     */
    public function show(Livestock $livestock): View
    {
        $owner = User::where('role', 'admin')->first() ?? User::first();
        $livestock->load(['species', 'tank.photos', 'tankPhotos']);

        // Related specimens of same species
        $related = Livestock::with('species')
            ->where('species_id', $livestock->species_id)
            ->where('id', '!=', $livestock->id)
            ->where('status', 'AVAILABLE')
            ->take(4)
            ->get();

        return view('catalog.show', compact('owner', 'livestock', 'related'));
    }
}
