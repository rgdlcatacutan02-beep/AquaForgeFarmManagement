<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryItemController extends Controller
{
    public function index(Request $request): View
    {
        $query = InventoryItem::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('supplier', 'like', "%{$s}%")
                  ->orWhere('notes', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->boolean('low_stock')) {
            $query->whereColumn('quantity', '<=', 'minimum_quantity');
        }

        $items = $query->orderBy('name')->paginate(15)->withQueryString();
        $lowStockCount = InventoryItem::whereColumn('quantity', '<=', 'minimum_quantity')->count();

        return view('inventory.index', compact('items', 'lowStockCount'));
    }

    public function create(): View
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|string',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'minimum_quantity' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        InventoryItem::create($validated);

        return redirect()->route('inventory.index')->with('success', 'Inventory item added successfully.');
    }

    public function edit(InventoryItem $inventory): View
    {
        return view('inventory.edit', ['item' => $inventory]);
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|string',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'minimum_quantity' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $inventory->update($validated);

        return redirect()->route('inventory.index')->with('success', 'Inventory item updated successfully.');
    }

    public function destroy(InventoryItem $inventory)
    {
        $name = $inventory->name;
        $inventory->delete();

        return redirect()->route('inventory.index')->with('success', "Item '{$name}' deleted successfully.");
    }
}
