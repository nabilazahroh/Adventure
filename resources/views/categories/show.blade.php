@extends('layouts.app')

@section('title', 'Detail Kategori')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('categories.index') }}" class="text-emerald-600 hover:text-emerald-800 mr-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 rounded-full overflow-hidden bg-gray-100 border border-gray-200 flex items-center justify-center flex-shrink-0">
                    @if (!empty($category->image_path))
                        <img src="{{ asset('storage/' . $category->image_path) }}" alt="{{ $category->name }}" class="h-full w-full object-cover">
                    @else
                        <i class="fas fa-tags text-gray-400"></i>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h1>
                    <p class="mt-1 text-sm text-gray-500">Total {{ $category->products_count }} produk dalam kategori ini.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('categories.edit', $category) }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100">
                <i class="fas fa-pen mr-2"></i>
                Edit Kategori
            </a>
        </div>
    </div>

    <!-- Produk Dalam Kategori -->
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Produk Dalam Kategori</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar produk yang Anda masukkan ke kategori ini.</p>
        </div>

        @if ($category->products->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($category->products as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ strtoupper($product->unit_type) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $product->stock }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center justify-center h-8 w-8 text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100" title="Edit Produk">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-10 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <i class="fas fa-box-open text-4xl"></i>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada produk di kategori ini</h3>
                <p class="mt-1 text-sm text-gray-500">Silakan edit kategori dan pilih produk yang ingin dimasukkan.</p>
                <div class="mt-6">
                    <a href="{{ route('categories.edit', $category) }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-emerald-600 rounded-lg shadow hover:bg-emerald-700">
                        <i class="fas fa-pen mr-2"></i>
                        Edit Kategori
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
