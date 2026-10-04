<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use Illuminate\Http\Request;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TankController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tank::with(['latestWaterLog', 'livestock.species', 'offspringBatches.species']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('tank_code', 'like', "%{$s}%")
                  ->orWhere('name', 'like', "%{$s}%")
                  ->orWhere('location', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('purpose')) {
            $query->where('purpose', $request->purpose);
        }

        $tanks = $query->orderBy('tank_code')->paginate(15)->withQueryString();

        return view('tanks.index', compact('tanks'));
    }

    public function scan(): View
    {
        return view('tanks.scan');
    }

    public function lookup(string $code)
    {
        $tank = Tank::where('tank_code', $code)
            ->orWhere('id', $code)
            ->first();

        if ($tank) {
            return redirect()->route('tanks.show', $tank);
        }

        return redirect()->route('tanks.index')->with('error', "Tank '{$code}' could not be found.");
    }

    public function show(Tank $tank): View
    {
        $tank->load([
            'livestock.species',
            'offspringBatches.species',
            'latestWaterLog',
            'waterLogs' => fn ($q) => $q->latest('recorded_at')->take(10),
            'lastWaterChange',
            'maintenanceLogs' => fn ($q) => $q->latest('performed_at')->take(10),
            'feedingLogs' => fn ($q) => $q->latest('fed_at')->take(10),
            'breedingEvents' => fn ($q) => $q->where('status', 'ACTIVE')->with(['species', 'maleLivestock', 'femaleLivestock']),
        ]);

        $qrCodeSvg = QrCode::size(140)->generate(route('tanks.show', $tank));

        return view('tanks.show', compact('tank', 'qrCodeSvg'));
    }

    public function create(): View
    {
        return view('tanks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tank_code' => 'required|string|max:50|unique:tanks,tank_code',
            'name' => 'required|string|max:100',
            'tank_type' => 'required|string',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'volume_liters' => 'required|numeric|min:0',
            'location' => 'nullable|string|max:100',
            'purpose' => 'required|string',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $tank = Tank::create($validated);

        return redirect()->route('tanks.show', $tank)
            ->with('success', "Tank '{$tank->tank_code}' registered successfully!")
            ->with('show_qr_print_modal', true);
    }

    public function edit(Tank $tank): View
    {
        return view('tanks.edit', compact('tank'));
    }

    public function update(Request $request, Tank $tank)
    {
        $validated = $request->validate([
            'tank_code' => 'required|string|max:50|unique:tanks,tank_code,' . $tank->id,
            'name' => 'required|string|max:100',
            'tank_type' => 'required|string',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'volume_liters' => 'required|numeric|min:0',
            'location' => 'nullable|string|max:100',
            'purpose' => 'required|string',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $tank->update($validated);

        return redirect()->route('tanks.show', $tank)->with('success', 'Tank updated successfully.');
    }

    public function destroy(Tank $tank)
    {
        $tank->delete();

        return redirect()->route('tanks.index')->with('success', 'Tank archived successfully.');
    }
    public function printLabel(Tank $tank): View
    {
        $qrCodeSvg = QrCode::size(170)->generate(route('tanks.show', $tank));
        $volumeGallons = round($tank->volume_liters * 0.264172, 1);

        return view('tanks.print_label', compact('tank', 'qrCodeSvg', 'volumeGallons'));
    }
}
