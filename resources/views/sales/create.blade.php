@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Transaksi Penjualan</h1>
            <p class="mt-1 text-sm text-gray-500">Catat barang keluar dan stok akan otomatis dikurangi.</p>
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

        @php
            $items = old('items');
            if (!is_array($items) || count($items) < 1) {
                $items = [
                    ['product_id' => null, 'quantity' => 1],
                ];
            }
        @endphp

        <form action="{{ route('sales.store') }}" method="POST" class="space-y-5" id="saleForm">
            @csrf

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="transaction_date" class="block text-sm font-medium text-gray-700">Tanggal Transaksi</label>
                    <input type="date" name="transaction_date" id="transaction_date"
                           value="{{ old('transaction_date', date('Y-m-d')) }}" required
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                </div>

                <div>
                    <label for="discount_percent" class="block text-sm font-medium text-gray-700">Potongan</label>
                    <select name="discount_percent" id="discount_percent"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                        <option value="0" {{ (string) old('discount_percent', '0') === '0' ? 'selected' : '' }}>Tanpa potongan</option>
                        <option value="20" {{ (string) old('discount_percent') === '20' ? 'selected' : '' }}>20%</option>
                        <option value="21" {{ (string) old('discount_percent') === '21' ? 'selected' : '' }}>21%</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Potongan dihitung dari total seluruh item dalam 1 transaksi.</p>
                </div>
            </div>

            <div class="space-y-3" id="itemsContainer">
                @foreach ($items as $i => $item)
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 sale-item" data-index="{{ $i }}">
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-12 md:items-end">
                            <div class="md:col-span-4">
                                <label class="block text-sm font-medium text-gray-700">Pilih Produk</label>
                                <select name="items[{{ $i }}][product_id]" required
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm product-select">
                                    <option value="" disabled {{ !($item['product_id'] ?? null) ? 'selected' : '' }}>Pilih produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                                data-stock="{{ $product->stock }}"
                                                data-price="{{ $product->selling_price }}"
                                                data-unit="{{ strtoupper($product->unit_type) }}"
                                                {{ (string) ($item['product_id'] ?? '') === (string) $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} ({{ strtoupper($product->unit_type) }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 stock-text">Stok tersedia: <span class="stock-value">-</span></p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                                <input type="number" min="1" value="{{ (int) ($item['quantity'] ?? 1) }}" required
                                       name="items[{{ $i }}][quantity]"
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm qty-input">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Harga</label>
                                <div class="mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 price-text whitespace-nowrap tabular-nums text-right">Rp&nbsp;0</div>
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-sm font-medium text-gray-700">Total</label>
                                <div class="relative mt-1">
                                    <div class="w-full rounded-lg border border-gray-200 bg-white pl-3 pr-12 py-2 text-sm font-semibold text-gray-900 line-total-text whitespace-nowrap tabular-nums text-right">Rp&nbsp;0</div>
                                    <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 inline-flex items-center justify-center h-8 w-8 text-red-700 bg-red-50 rounded-lg hover:bg-red-100 remove-item" title="Hapus Item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end">
                <button type="button" id="addItemBtn" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100">
                    + Tambah Item
                </button>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                <textarea name="notes" id="notes" rows="3"
                          class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">{{ old('notes') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Tambahkan catatan atau keterangan transaksi</p>
            </div>

            <div class="rounded-xl border border-gray-100 bg-white p-4">
                <div class="flex items-center justify-between text-sm">
                    <div class="text-gray-600">Subtotal</div>
                    <div class="font-semibold text-gray-900" id="subtotalText">Rp 0</div>
                </div>
                <div class="mt-2 flex items-center justify-between text-sm">
                    <div class="text-gray-600">Potongan (<span id="discountPercentText">0</span>%)</div>
                    <div class="font-semibold text-gray-900" id="discountText">Rp 0</div>
                </div>
                <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-3">
                    <div class="text-sm font-semibold text-gray-700">Total</div>
                    <div class="text-base font-bold text-emerald-700" id="grandTotalText">Rp 0</div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('sales.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</a>
                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

<template id="saleItemTemplate">
    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 sale-item" data-index="__INDEX__">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-12 md:items-end">
            <div class="md:col-span-4">
                <label class="block text-sm font-medium text-gray-700">Pilih Produk</label>
                <select name="items[__INDEX__][product_id]" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm product-select">
                    <option value="" disabled selected>Pilih produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"
                                data-stock="{{ $product->stock }}"
                                data-price="{{ $product->selling_price }}"
                                data-unit="{{ strtoupper($product->unit_type) }}">
                            {{ $product->name }} ({{ strtoupper($product->unit_type) }})
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500 stock-text">Stok tersedia: <span class="stock-value">-</span></p>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                <input type="number" min="1" value="1" required
                       name="items[__INDEX__][quantity]"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm qty-input">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Harga</label>
                <div class="mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 price-text whitespace-nowrap tabular-nums text-right">Rp&nbsp;0</div>
            </div>

            <div class="md:col-span-4">
                <label class="block text-sm font-medium text-gray-700">Total</label>
                <div class="relative mt-1">
                    <div class="w-full rounded-lg border border-gray-200 bg-white pl-3 pr-12 py-2 text-sm font-semibold text-gray-900 line-total-text whitespace-nowrap tabular-nums text-right">Rp&nbsp;0</div>
                    <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 inline-flex items-center justify-center h-8 w-8 text-red-700 bg-red-50 rounded-lg hover:bg-red-100 remove-item" title="Hapus Item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemsContainer = document.getElementById('itemsContainer');
    const addItemBtn = document.getElementById('addItemBtn');
    const template = document.getElementById('saleItemTemplate');

    const discountSelect = document.getElementById('discount_percent');
    const subtotalText = document.getElementById('subtotalText');
    const discountText = document.getElementById('discountText');
    const grandTotalText = document.getElementById('grandTotalText');
    const discountPercentText = document.getElementById('discountPercentText');

    const formatRupiah = (value) => {
        const n = Number(value || 0);
        return 'Rp\u00A0' + Math.round(n).toLocaleString('id-ID');
    };

    const recalc = () => {
        let subtotal = 0;

        itemsContainer.querySelectorAll('.sale-item').forEach((row) => {
            const productSelect = row.querySelector('.product-select');
            const qtyInput = row.querySelector('.qty-input');
            const stockValue = row.querySelector('.stock-value');
            const priceText = row.querySelector('.price-text');
            const lineTotalText = row.querySelector('.line-total-text');

            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const stock = selectedOption ? Number(selectedOption.getAttribute('data-stock') || 0) : 0;
            const price = selectedOption ? Number(selectedOption.getAttribute('data-price') || 0) : 0;
            const qty = Number(qtyInput.value || 0);

            if (selectedOption && selectedOption.value) {
                stockValue.textContent = stock.toLocaleString('id-ID');
                qtyInput.max = stock > 0 ? String(stock) : '';
                if (stock > 0 && qty > stock) {
                    qtyInput.value = stock;
                }
            } else {
                stockValue.textContent = '-';
                qtyInput.max = '';
            }

            priceText.textContent = formatRupiah(price);
            const lineTotal = qty * price;
            lineTotalText.textContent = formatRupiah(lineTotal);

            subtotal += lineTotal;
        });

        const discountPercent = Number(discountSelect.value || 0);
        const discountAmount = subtotal * (discountPercent / 100);
        const grandTotal = subtotal - discountAmount;

        discountPercentText.textContent = String(discountPercent);
        subtotalText.textContent = formatRupiah(subtotal);
        discountText.textContent = formatRupiah(discountAmount);
        grandTotalText.textContent = formatRupiah(grandTotal);
    };

    const bindRow = (row) => {
        const productSelect = row.querySelector('.product-select');
        const qtyInput = row.querySelector('.qty-input');
        const removeBtn = row.querySelector('.remove-item');

        productSelect.addEventListener('change', recalc);
        qtyInput.addEventListener('input', recalc);
        removeBtn.addEventListener('click', function() {
            const allRows = itemsContainer.querySelectorAll('.sale-item');
            if (allRows.length <= 1) {
                return;
            }
            row.remove();
            recalc();
        });
    };

    itemsContainer.querySelectorAll('.sale-item').forEach(bindRow);
    discountSelect.addEventListener('change', recalc);

    addItemBtn.addEventListener('click', function() {
        const existing = Array.from(itemsContainer.querySelectorAll('.sale-item'));
        const maxIndex = existing.reduce((max, el) => {
            const idx = Number(el.getAttribute('data-index') || 0);
            return idx > max ? idx : max;
        }, -1);
        const nextIndex = maxIndex + 1;
        const html = template.innerHTML.replaceAll('__INDEX__', String(nextIndex));
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();
        const newRow = wrapper.firstElementChild;
        itemsContainer.appendChild(newRow);
        bindRow(newRow);
        recalc();
    });

    recalc();
});
</script>
@endsection
