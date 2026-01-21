@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Produk</h1>
            <p class="mt-1 text-sm text-gray-500">Perbarui detail produk.</p>
        </div>
        <a href="{{ route('products.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">&larr; Kembali ke Data Produk</a>
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

        <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Produk</label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
            </div>

            <div>
                <label for="unit_type" class="block text-sm font-medium text-gray-700">Satuan</label>
                <select name="unit_type" id="unit_type" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                    <option value="" disabled {{ old('unit_type', $product->unit_type) ? '' : 'selected' }}>Pilih satuan</option>
                    <option value="ml" {{ old('unit_type', $product->unit_type) == 'ml' ? 'selected' : '' }}>Mililiter (ml)</option>
                    <option value="cm" {{ old('unit_type', $product->unit_type) == 'cm' ? 'selected' : '' }}>Centimeter (cm)</option>
                    <option value="meter" {{ old('unit_type', $product->unit_type) == 'meter' ? 'selected' : '' }}>Meter (m)</option>
                    <option value="liter" {{ old('unit_type', $product->unit_type) == 'liter' ? 'selected' : '' }}>Liter (L)</option>
                    <option value="pcs" {{ old('unit_type', $product->unit_type) == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                </select>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Produk</label>
                <textarea name="description" id="description" rows="4"
                          class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">{{ old('description', $product->description) }}</textarea>
            </div>

            <div>
                <label for="purchase_price" class="block text-sm font-medium text-gray-700">Harga Beli</label>
                <input type="text" name="purchase_price" id="purchase_price" inputmode="numeric" value="{{ old('purchase_price', (string) ((int) $product->purchase_price)) }}" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                <p class="mt-1 text-xs text-gray-500">Contoh input: 300000 atau 300.000</p>
            </div>

            <div>
                <label for="selling_price" class="block text-sm font-medium text-gray-700">Harga Jual</label>
                <input type="text" name="selling_price" id="selling_price" inputmode="numeric" value="{{ old('selling_price', (string) ((int) $product->selling_price)) }}" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                <p class="mt-1 text-xs text-gray-500">Contoh input: 300000 atau 300.000</p>
            </div>

            <div>
                <label for="stock" class="block text-sm font-medium text-gray-700">Stok</label>
                <input type="number" name="stock" id="stock" min="0" value="{{ old('stock', $product->stock) }}" required
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</a>
                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
