<?php

namespace App\Http\Controllers;

use App\Exports\StockOpnameExport;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class SaleController extends Controller
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
        $from = request('from');
        $to = request('to');

        $salesQuery = Sale::with('product', 'user')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc');

        if (!empty($from)) {
            $salesQuery->whereDate('transaction_date', '>=', $from);
        }

        if (!empty($to)) {
            $salesQuery->whereDate('transaction_date', '<=', $to);
        }

        $totalSales = (clone $salesQuery)->sum('total');
        $totalProfit = (clone $salesQuery)->sum('profit');

        $sales = $salesQuery->get();

        return view('sales.index', compact('sales', 'totalSales', 'totalProfit', 'from', 'to'));
    }

    public function exportStockOpname(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $filenameParts = ['stock-opname'];
        if (!empty($from)) {
            $filenameParts[] = $from;
        }
        if (!empty($to)) {
            $filenameParts[] = $to;
        }
        $filenameParts[] = now()->format('His');
        $filename = implode('-', $filenameParts) . '.xlsx';

        return Excel::download(new StockOpnameExport($from, $to), $filename);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        return view('sales.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => ['required', 'date'],
            'discount_percent' => ['nullable', 'integer', 'in:0,20,21'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $discountPercent = (int) ($validated['discount_percent'] ?? 0);

        DB::beginTransaction();

        try {
            $checkoutCode = 'CO-' . now()->format('YmdHis') . '-' . random_int(1000, 9999);

            foreach ($validated['items'] as $index => $item) {
                $product = Product::whereKey($item['product_id'])->lockForUpdate()->first();

                if (!$product) {
                    DB::rollBack();
                    return back()->withInput()->withErrors([
                        "items.{$index}.product_id" => 'Produk tidak ditemukan.',
                    ]);
                }

                if ($product->stock < (int) $item['quantity']) {
                    DB::rollBack();
                    return back()->withInput()->withErrors([
                        "items.{$index}.quantity" => "Stok tidak mencukupi untuk {$product->name}. Stok tersedia: {$product->stock}",
                    ]);
                }

                $baseSellingPrice = $product->selling_price;
                if ($baseSellingPrice === null) {
                    DB::rollBack();
                    return back()->withInput()->withErrors([
                        "items.{$index}.product_id" => "Harga jual produk {$product->name} belum diisi.",
                    ]);
                }

                $qty = (int) $item['quantity'];
                $sellingPrice = (float) $baseSellingPrice;
                if ($discountPercent > 0) {
                    $sellingPrice = round(((100 - $discountPercent) / 100) * $sellingPrice, 2);
                }

                $total = $qty * $sellingPrice;
                $profit = $qty * ($sellingPrice - (float) $product->purchase_price);

                Sale::create([
                    'transaction_date' => $validated['transaction_date'],
                    'checkout_code' => $checkoutCode,
                    'product_id' => (int) $item['product_id'],
                    'quantity' => $qty,
                    'selling_price' => $sellingPrice,
                    'total' => $total,
                    'profit' => $profit,
                    'notes' => $validated['notes'] ?? null,
                    'discount_percent' => $discountPercent,
                    'user_id' => Auth::id(),
                ]);

                $product->decrement('stock', $qty);
            }

            DB::commit();

            return redirect()
                ->route('sales.index')
                ->with('success', 'Transaksi penjualan berhasil disimpan dan stok telah dikurangi.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan transaksi: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        $sale->load('product', 'user');
        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        $sale->load('product');

        $products = Product::orderBy('name')->get();

        return view('sales.edit', compact('sale', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'transaction_date' => ['required', 'date'],
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'selling_price' => ['nullable'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if (($validated['selling_price'] ?? null) !== null && trim((string) $validated['selling_price']) !== '') {
            $parsed = $this->parseMoneyToNumber($validated['selling_price']);
            if ($parsed === null) {
                return back()->withInput()->withErrors(['selling_price' => 'Format harga jual tidak valid. Contoh: 300000 atau 300.000']);
            }
            $validated['selling_price'] = $parsed;
        } else {
            $validated['selling_price'] = null;
        }

        DB::beginTransaction();

        try {
            $sale->refresh();

            $oldProduct = Product::whereKey($sale->product_id)->lockForUpdate()->first();
            $newProduct = Product::whereKey($validated['product_id'])->lockForUpdate()->first();

            if (!$oldProduct || !$newProduct) {
                DB::rollBack();
                return back()->withInput()->withErrors(['product_id' => 'Produk tidak ditemukan.']);
            }

            $oldProduct->increment('stock', $sale->quantity);

            if ($newProduct->stock < $validated['quantity']) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->withErrors(['quantity' => "Stok tidak mencukupi. Stok tersedia: {$newProduct->stock}"]);
            }

            $sellingPrice = $validated['selling_price'] ?? $newProduct->selling_price;
            $total = $validated['quantity'] * $sellingPrice;
            $profit = $validated['quantity'] * ($sellingPrice - $newProduct->purchase_price);

            $sale->update([
                'transaction_date' => $validated['transaction_date'],
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'selling_price' => $sellingPrice,
                'total' => $total,
                'profit' => $profit,
                'notes' => $validated['notes'],
            ]);

            $newProduct->decrement('stock', $validated['quantity']);

            DB::commit();

            return redirect()
                ->route('sales.index')
                ->with('success', 'Transaksi penjualan berhasil diperbarui dan stok telah disesuaikan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui transaksi: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        DB::beginTransaction();

        try {
            $sale->refresh();

            if (!empty($sale->checkout_code)) {
                $items = Sale::where('checkout_code', $sale->checkout_code)
                    ->lockForUpdate()
                    ->get();

                foreach ($items as $item) {
                    $product = Product::whereKey($item->product_id)->lockForUpdate()->first();

                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }

                Sale::where('checkout_code', $sale->checkout_code)->delete();
            } else {
                $product = Product::whereKey($sale->product_id)->lockForUpdate()->first();

                if ($product) {
                    $product->increment('stock', $sale->quantity);
                }

                $sale->delete();
            }

            DB::commit();

            return redirect()
                ->route('sales.index')
                ->with('success', 'Transaksi penjualan berhasil dihapus dan stok telah dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus transaksi: ' . $e->getMessage()]);
        }
    }
}
