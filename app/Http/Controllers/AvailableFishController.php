<?php

namespace App\Http\Controllers;

use App\Models\Livestock;
use App\Models\OffspringBatch;
use App\Models\Species;
use App\Models\Tank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AvailableFishController extends Controller
{
    /**
     * Display available fish management dashboard and catalog toggles.
     */
    public function index(Request $request): View
    {
        $tab = $request->get('tab', 'catalog'); // 'catalog' (AVAILABLE only), 'all', 'batches'
        $search = $request->get('search');
        $speciesId = $request->get('species_id');

        $query = Livestock::with(['species', 'tank']);

        if ($tab === 'catalog') {
            $query->where('status', 'AVAILABLE');
        } elseif ($tab === 'not_listed') {
            $query->where('status', '!=', 'AVAILABLE');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('variety', 'like', "%{$search}%")
                  ->orWhere('livestock_code', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($speciesId) {
            $query->where('species_id', $speciesId);
        }

        $livestock = $query->latest()->paginate(20)->withQueryString();

        // KPI Counts
        $catalogCount = Livestock::where('status', 'AVAILABLE')->count();
        $catalogValue = Livestock::where('status', 'AVAILABLE')->sum('purchase_price');
        $totalLivestockCount = Livestock::count();
        $activeBatchesCount = OffspringBatch::where('status', 'ACTIVE')->where('current_count', '>', 0)->count();

        // Active batches for the fry / batch catalog tab
        $batches = OffspringBatch::with(['species', 'tank'])
            ->where('status', 'ACTIVE')
            ->where('current_count', '>', 0)
            ->latest()
            ->get();

        $speciesList = Species::where('active', true)->orderBy('name')->get();
        $tanks = Tank::orderBy('tank_code')->get();

        return view('available-fish.index', compact(
            'livestock',
            'catalogCount',
            'catalogValue',
            'totalLivestockCount',
            'activeBatchesCount',
            'batches',
            'speciesList',
            'tanks',
            'tab'
        ));
    }

    /**
     * Show form to add a fish directly to the available catalog.
     */
    public function create(): View
    {
        $species = Species::where('active', true)->orderBy('name')->get();
        $tanks = Tank::where('status', 'ACTIVE')->orderBy('tank_code')->get();

        // Auto-generate a suggested code
        $randomCode = 'AF-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4)) . '-' . rand(100, 999);

        return view('available-fish.create', compact('species', 'tanks', 'randomCode'));
    }

    /**
     * Store new fish and publish directly to available catalog.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'livestock_code' => 'required|string|max:50|unique:livestock,livestock_code',
            'species_id' => 'required|exists:species,id',
            'variety' => 'nullable|string|max:100',
            'sex' => 'required|in:MALE,FEMALE,UNKNOWN',
            'purchase_price' => 'required|numeric|min:0',
            'status' => 'required|string',
            'tank_id' => 'nullable|exists:tanks,id',
            'grade' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'photo' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,gif,bmp|max:20480',
        ], [
            'purchase_price.required' => 'Please provide an asking price for this available fish.',
            'purchase_price.min' => 'Asking price cannot be negative.',
            'photo.max' => 'Specimen photo size must not exceed 20MB.',
        ]);

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $validated['photo_path'] = $request->file('photo')->store('livestock_photos', 'public');
        }

        $validated['date_acquired'] = now()->toDateString();

        $fish = Livestock::create($validated);

        $statusMsg = $fish->status === 'AVAILABLE' 
            ? "Specimen [{$fish->livestock_code}] has been published to your live customer catalog!" 
            : "Specimen [{$fish->livestock_code}] saved successfully.";

        return redirect()->route('available-fish.index')->with('success', $statusMsg);
    }

    /**
     * 1-Click toggle to add or remove any fish from the public catalog.
     */
    public function toggleCatalog(Request $request, Livestock $livestock): RedirectResponse
    {
        if ($livestock->status === 'AVAILABLE') {
            // Remove from catalog: change to BREEDER or GROWOUT
            $newStatus = $request->get('new_status', 'BREEDER');
            $livestock->update(['status' => $newStatus]);
            $msg = "Specimen [{$livestock->livestock_code}] removed from the public catalog (status set to {$newStatus}).";
        } else {
            // Put on catalog
            $livestock->update(['status' => 'AVAILABLE']);
            $msg = "Specimen [{$livestock->livestock_code}] is now LIVE and visible on your public customer catalog!";
        }

        return back()->with('success', $msg);
    }

    /**
     * Quick price update directly from the available fish manager.
     */
    public function updatePrice(Request $request, Livestock $livestock): RedirectResponse
    {
        $validated = $request->validate([
            'purchase_price' => 'required|numeric|min:0',
        ]);

        $livestock->update(['purchase_price' => $validated['purchase_price']]);

        return back()->with('success', "Asking price for [{$livestock->livestock_code}] updated to ?" . number_format($livestock->purchase_price, 2) . ".");
    }
}
