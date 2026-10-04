<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Livestock;
use App\Models\OffspringBatch;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Sale::with(['customer', 'items']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('sale_number', 'like', "%{$s}%")
                  ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $sales = $query->latest('sale_date')->paginate(15)->withQueryString();

        $totalRevenue = (float) Sale::where('status', '!=', 'CANCELLED')->sum('total');
        $paidSalesCount = Sale::where('payment_status', 'PAID')->count();
        $pendingSalesCount = Sale::where('payment_status', 'UNPAID')->count();

        return view('sales.index', compact('sales', 'totalRevenue', 'paidSalesCount', 'pendingSalesCount'));
    }

    public function show(Sale $sale): View
    {
        $sale->load(['customer', 'items.livestock.species', 'items.livestock.tank', 'items.offspringBatch.species', 'items.offspringBatch.tank']);

        return view('sales.show', compact('sale'));
    }

    public function create(): View
    {
        $customers = Customer::orderBy('name')->get();
        $availableLivestock = Livestock::with(['species', 'tank'])
            ->where('status', 'AVAILABLE')
            ->orderBy('livestock_code')
            ->get();
        $availableBatches = OffspringBatch::with(['species', 'tank'])
            ->where('available_count', '>', 0)
            ->orderBy('batch_code')
            ->get();

        $suggestedSaleNumber = 'SALE-' . date('Ymd') . '-' . str_pad((string)(Sale::count() + 1), 4, '0', STR_PAD_LEFT);

        return view('sales.create', compact('customers', 'availableLivestock', 'availableBatches', 'suggestedSaleNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_number' => 'required|string|unique:sales,sale_number|max:50',
            'customer_id' => 'nullable|exists:customers,id',
            'sale_date' => 'required|date',
            'status' => 'required|string|in:PENDING,COMPLETED,CANCELLED',
            'payment_status' => 'required|string|in:PAID,UNPAID,PARTIAL',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.livestock_id' => 'nullable|exists:livestock,id',
            'items.*.offspring_batch_id' => 'nullable|exists:offspring_batches,id',
        ]);

        DB::transaction(function () use ($validated) {
            $sale = Sale::create([
                'sale_number' => $validated['sale_number'],
                'customer_id' => $validated['customer_id'] ?? null,
                'sale_date' => $validated['sale_date'],
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
                'subtotal' => $validated['subtotal'],
                'discount' => $validated['discount'] ?? 0,
                'total' => $validated['total'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];

                $sale->items()->create([
                    'item_description' => $item['item_description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                    'livestock_id' => $item['livestock_id'] ?? null,
                    'offspring_batch_id' => $item['offspring_batch_id'] ?? null,
                ]);

                if (!empty($item['livestock_id'])) {
                    Livestock::where('id', $item['livestock_id'])->update(['status' => 'SOLD']);
                }

                if (!empty($item['offspring_batch_id'])) {
                    $b = OffspringBatch::find($item['offspring_batch_id']);
                    if ($b) {
                        $b->available_count = max(0, $b->available_count - $item['quantity']);
                        $b->current_count = max(0, $b->current_count - $item['quantity']);
                        if ($b->available_count === 0 && $b->current_count === 0) {
                            $b->status = 'SOLD_OUT';
                        }
                        $b->save();
                    }
                }
            }
        });

        return redirect()->route('sales.index')->with('success', 'Sale order recorded successfully.');
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();

        return redirect()->route('sales.index')->with('success', 'Sale record removed.');
    }
}
