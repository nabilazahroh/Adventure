@extends('layouts.app')

@section('title', 'Manajemen Kategori')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-emerald-900">Manajemen Kategori</h1>
            <p class="mt-1 text-sm text-emerald-700">Klik kotak kategori untuk melihat produk di dalamnya.</p>
        </div>

        <div class="flex sm:justify-end">
            <a href="{{ route('categories.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                <i class="fas fa-plus mr-2"></i>
                Tambah Kategori
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Data Kategori (Kotak Bergambar) -->
    @if ($categories->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
            @foreach ($categories as $category)
                <div class="group relative bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden">
                    <a href="{{ route('categories.show', $category) }}" class="block p-4">
                        <div class="flex flex-col items-center text-center">
                            <div class="h-16 w-16 rounded-full overflow-hidden bg-gray-100 border border-gray-200 flex items-center justify-center">
                                @if (!empty($category->image_path))
                                    <img src="{{ asset('storage/' . $category->image_path) }}" alt="{{ $category->name }}" class="h-full w-full object-cover">
                                @else
                                    <i class="fas fa-tags text-gray-400 text-xl"></i>
                                @endif
                            </div>
                            <div class="mt-2 text-sm font-medium text-gray-800 truncate w-full">{{ $category->name }}</div>
                            <div class="mt-1 text-xs text-gray-500">{{ $category->products_count }} produk</div>
                        </div>
                    </a>

                    <div class="absolute top-2 right-2 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                        <a href="{{ route('categories.edit', $category) }}" class="inline-flex items-center justify-center h-8 w-8 text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100" title="Edit">
                            <i class="fas fa-pen text-xs"></i>
                        </a>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center h-8 w-8 text-red-700 bg-red-50 rounded-lg hover:bg-red-100" title="Hapus">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl shadow border border-gray-100 p-8 text-center">
            <div class="mx-auto h-12 w-12 text-gray-400">
                <i class="fas fa-tags text-4xl"></i>
            </div>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada kategori</h3>
            <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan kategori pertama.</p>
            <div class="mt-6">
                <a href="{{ route('categories.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Kategori Pertama
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
