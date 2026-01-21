@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center">
        <a href="{{ route('categories.index') }}" class="text-emerald-600 hover:text-emerald-800 mr-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-emerald-900">Edit Kategori</h1>
            <p class="mt-1 text-sm text-emerald-700">Perbarui informasi kategori yang ada.</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('categories.update', $category) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Nama Kategori -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name', $category->name) }}"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                            placeholder="Contoh: Elektronik, Makanan, Pakaian"
                            required
                        >
                    </div>

                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Gambar Kategori (Opsional)</label>
                    <p class="mt-1 text-xs text-gray-500">Format: JPG/PNG. Maksimal 2MB.</p>

                    @if (!empty($category->image_path))
                        <div class="mt-3 flex items-center gap-3">
                            <img src="{{ asset('storage/' . $category->image_path) }}" alt="{{ $category->name }}" class="h-14 w-14 rounded-full object-cover border border-gray-200">
                            <div class="text-xs text-gray-500">Gambar saat ini</div>
                        </div>
                    @endif

                    <div class="mt-2">
                        <input
                            type="file"
                            name="image"
                            id="image"
                            accept="image/*"
                            class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100"
                        >
                    </div>
                    @error('image')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Pilih Produk (Opsional)</label>
                    <p class="mt-1 text-xs text-gray-500">Centang produk yang ingin dimasukkan ke kategori ini.</p>

                    <div class="mt-3 max-h-64 overflow-auto border border-gray-200 rounded-lg">
                        @php
                            $oldSelected = old('product_ids', $selectedProductIds ?? []);
                        @endphp

                        @if ($products->count() > 0)
                            <div class="divide-y divide-gray-100">
                                @foreach ($products as $product)
                                    <label class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50">
                                        <input
                                            type="checkbox"
                                            name="product_ids[]"
                                            value="{{ $product->id }}"
                                            class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                            {{ in_array($product->id, $oldSelected) ? 'checked' : '' }}
                                        >
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <div class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</div>
                                                @if ($product->category && $product->category->id !== $category->id)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-100 text-amber-800">
                                                        Saat ini: {{ $product->category->name }}
                                                    </span>
                                                @elseif ($product->category && $product->category->id === $category->id)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-100 text-emerald-800">
                                                        Sudah di kategori ini
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500">Stok: {{ $product->stock }} | Satuan: {{ strtoupper($product->unit_type) }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div class="px-4 py-6 text-sm text-gray-500">
                                Belum ada produk. Silakan tambahkan produk terlebih dahulu.
                            </div>
                        @endif
                    </div>

                    @error('product_ids')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('product_ids.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Produk -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Kategori ini saat ini memiliki <strong>{{ $category->products_count }}</strong> produk terkait.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-between">
                    <div>
                        @if ($category->products_count == 0)
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 border border-red-300 rounded-lg shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <i class="fas fa-trash mr-2"></i>
                                    Hapus Kategori
                                </button>
                            </form>
                        @else
                            <button type="button" disabled class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-400 bg-gray-100 cursor-not-allowed" title="Tidak dapat menghapus kategori yang memiliki produk">
                                <i class="fas fa-trash mr-2"></i>
                                Hapus Kategori
                            </button>
                        @endif
                    </div>
                    
                    <div class="space-x-3">
                        <a href="{{ route('categories.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            Batal
                        </a>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            <i class="fas fa-save mr-2"></i>
                            Perbarui Kategori
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
