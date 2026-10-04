<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Expense::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('description', 'like', "%{$s}%")
                  ->orWhere('notes', 'like', "%{$s}%");
            });
        }

        $expenses = $query->latest('expense_date')->paginate(15)->withQueryString();

        $totalExpenses = (float) Expense::sum('amount');
        $monthExpenses = (float) Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $categories = [
            'FEED' => 'Feed & Nutrition',
            'LIVESTOCK' => 'Livestock Acquisition',
            'EQUIPMENT' => 'Equipment & Hardware',
            'ELECTRICITY' => 'Power & Electricity',
            'WATER' => 'Water & Utilities',
            'MEDICATION' => 'Medication & Treatments',
            'PACKAGING' => 'Shipping & Packaging',
            'MAINTENANCE' => 'Repairs & Maintenance',
            'OTHER' => 'General / Other',
        ];

        return view('expenses.index', compact('expenses', 'totalExpenses', 'monthExpenses', 'categories'));
    }

    public function create(): View
    {
        $categories = [
            'FEED' => 'Feed & Nutrition',
            'LIVESTOCK' => 'Livestock Acquisition',
            'EQUIPMENT' => 'Equipment & Hardware',
            'ELECTRICITY' => 'Power & Electricity',
            'WATER' => 'Water & Utilities',
            'MEDICATION' => 'Medication & Treatments',
            'PACKAGING' => 'Shipping & Packaging',
            'MAINTENANCE' => 'Repairs & Maintenance',
            'OTHER' => 'General / Other',
        ];

        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'description' => 'required|string|max:150',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        Expense::create($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense logged successfully.');
    }

    public function edit(Expense $expense): View
    {
        $categories = [
            'FEED' => 'Feed & Nutrition',
            'LIVESTOCK' => 'Livestock Acquisition',
            'EQUIPMENT' => 'Equipment & Hardware',
            'ELECTRICITY' => 'Power & Electricity',
            'WATER' => 'Water & Utilities',
            'MEDICATION' => 'Medication & Treatments',
            'PACKAGING' => 'Shipping & Packaging',
            'MAINTENANCE' => 'Repairs & Maintenance',
            'OTHER' => 'General / Other',
        ];

        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'description' => 'required|string|max:150',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense record deleted.');
    }
}
