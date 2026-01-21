@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Transaksi Penjualan</h1>
            <p class="mt-1 text-sm text-gray-500">Perbarui transaksi penjualan, stok akan disesuaikan otomatis.</p>
        </div>
        <a href="{{ route('sales.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">&larr; Kembali</a>
    </div>

    <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6 space-y-6">
        @if ($errors->any())
            <div class="rounded-lg bg-red-50 border border-red-200 p-4 mb-4">
                <h2 class="text-sm font-semibold text-red-700 mb-2">Terjadi kesalahan:</h2>
                <ul class="text-sm text-red-600 list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('sales.update', $sale) }}" method="POST" class="space-y-5" id="saleForm">
            @csrf
            @method('PUT')

            <div>
                <label for="transaction_date" class="block text-sm font-medium text-gray-700">Tanggal Transaksi</label>
                <input type="date" name="transaction_date" id="transaction_date"
                       value="{{ old('transaction_date', $sale->transaction_date->format('Y-m-d')) }}" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
            </div>

            <div>
                <label for="product_id" class="block text-sm font-medium text-gray-700">Pilih Produk</label>
                <select name="product_id" id="product_id" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                    <option value="" disabled {{ old('product_id', $sale->product_id) ? '' : 'selected' }}>Pilih produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"
                                data-stock="{{ $product->stock }}"
                                data-price="{{ $product->selling_price }}"
                                {{ (string) old('product_id', $sale->product_id) === (string) $product->id ? 'selected' : '' }}>
                            {{ $product->name }} ({{ strtoupper($product->unit_type) }})
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Catatan: stok akan disesuaikan otomatis berdasarkan perubahan transaksi.</p>
            </div>

            <div id="stockInfo" class="hidden p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium text-blue-800">Stok Saat Ini: <span id="availableStock">0</span></span>
                </div>
            </div>

            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700">Jumlah Keluar</label>
                <input type="number" name="quantity" id="quantity" min="1" value="{{ old('quantity', $sale->quantity) }}" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
            </div>

            <div>
                <label for="selling_price" class="block text-sm font-medium text-gray-700">Harga Jual (Opsional)</label>
                <input type="text" name="selling_price" id="selling_price" inputmode="numeric" value="{{ old('selling_price', (string) ((int) $sale->selling_price)) }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                <p class="mt-1 text-xs text-gray-500">Kosongkan untuk menggunakan harga jual default produk</p>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                <textarea name="notes" id="notes" rows="3"
                          class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">{{ old('notes', $sale->notes) }}</textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('sales.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</a>
                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const stockInfo = document.getElementById('stockInfo');
    const availableStock = document.getElementById('availableStock');
    const sellingPriceInput = document.getElementById('selling_price');

    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const stock = selectedOption.getAttribute('data-stock');
        const price = selectedOption.getAttribute('data-price');

        if (stock) {
            availableStock.textContent = stock;
            stockInfo.classList.remove('hidden');

            if (price && !sellingPriceInput.value) {
                sellingPriceInput.placeholder = 'Rp ' + parseFloat(price).toLocaleString('id-ID');
            }
        } else {
            stockInfo.classList.add('hidden');
        }
    });

    if (productSelect.value) {
        productSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
