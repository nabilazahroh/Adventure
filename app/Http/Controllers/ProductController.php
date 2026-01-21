<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private function parseMoneyToNumber($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        $raw = str_ireplace(['rp', 'idr'], '', $raw);
        $raw = preg_replace('/\s+/', '', $raw);
        $digits = preg_replace('/[^0-9]/', '', $raw);

        if ($digits === '' || !ctype_digit($digits)) {
            return null;
        }

        return $digits;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->get();

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit_type' => ['required', 'in:ml,cm,meter,liter,pcs'],
            'description' => ['nullable', 'string'],
            'purchase_price' => ['required'],
            'selling_price' => ['required'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $purchasePrice = $this->parseMoneyToNumber($validated['purchase_price']);
        $sellingPrice = $this->parseMoneyToNumber($validated['selling_price']);

        if ($purchasePrice === null) {
            return back()->withInput()->withErrors(['purchase_price' => 'Format harga beli tidak valid. Contoh: 300000 atau 300.000']);
        }

        if ($sellingPrice === null) {
            return back()->withInput()->withErrors(['selling_price' => 'Format harga jual tidak valid. Contoh: 300000 atau 300.000']);
        }

        $validated['purchase_price'] = $purchasePrice;
        $validated['selling_price'] = $sellingPrice;

        Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit_type' => ['required', 'in:ml,cm,meter,liter,pcs'],
            'description' => ['nullable', 'string'],
            'purchase_price' => ['required'],
            'selling_price' => ['required'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $purchasePrice = $this->parseMoneyToNumber($validated['purchase_price']);
        $sellingPrice = $this->parseMoneyToNumber($validated['selling_price']);

        if ($purchasePrice === null) {
            return back()->withInput()->withErrors(['purchase_price' => 'Format harga beli tidak valid. Contoh: 300000 atau 300.000']);
        }

        if ($sellingPrice === null) {
            return back()->withInput()->withErrors(['selling_price' => 'Format harga jual tidak valid. Contoh: 300000 atau 300.000']);
        }

        $validated['purchase_price'] = $purchasePrice;
        $validated['selling_price'] = $sellingPrice;

        $product->update($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
