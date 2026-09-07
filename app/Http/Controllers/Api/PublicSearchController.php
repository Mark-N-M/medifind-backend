<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Pharmacy;
use Illuminate\Http\Request;

class PublicSearchController extends Controller
{
    public function searchMedicines(Request $request)
    {
        $query = $request->query('q');
        $category = $request->query('category');
        $inStockOnly = $request->boolean('in_stock_only');

        $medicines = Medicine::with(['stocks.pharmacy'])
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('generic_name', 'like', "%{$query}%");
            })
            ->when($category && $category !== 'All categories', function ($q) use ($category) {
                $q->where('category', $category);
            })
            ->get()
            ->map(function ($med) use ($inStockOnly) {
                $stocks = $med->stocks;
                if ($inStockOnly) {
                    $stocks = $stocks->where('in_stock', true);
                }

                $lowestPrice = $stocks->min('price');
                $availablePharmaciesCount = $stocks->where('in_stock', true)->count();

                return [
                    'id' => $med->id,
                    'name' => $med->name,
                    'generic_name' => $med->generic_name,
                    'category' => $med->category,
                    'lowest_price' => $lowestPrice,
                    'available_pharmacies_count' => $availablePharmaciesCount,
                    'pharmacies' => $stocks->map(fn($s) => [
                        'pharmacy_name' => $s->pharmacy->name ?? 'Pharmacy',
                        'location' => $s->pharmacy->location ?? 'Nairobi',
                        'price' => $s->price,
                        'in_stock' => (bool) $s->in_stock,
                    ])->values()
                ];
            });

        return response()->json($medicines);
    }

    public function getPharmacies()
    {
        $pharmacies = Pharmacy::withCount('stocks')->get();
        return response()->json($pharmacies);
    }
}