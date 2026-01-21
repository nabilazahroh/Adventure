@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Data Produk</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola daftar produk di nabilatuzzahroh project.</p>
        </div>
        <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            + Tambah Produk
        </a>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 p-4">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ strtoupper($product->unit_type) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $product->stock }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-md">{{ $product->description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center justify-center h-8 w-8 text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" data-swal data-swal-title="Hapus produk ini?" data-swal-text="Produk akan dihapus permanen." data-swal-confirm="Hapus" data-swal-cancel="Batal">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center h-8 w-8 text-red-700 bg-red-50 rounded-lg hover:bg-red-100" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                            Belum ada data produk. Klik tombol <span class="font-semibold">"Tambah Produk"</span> untuk menambahkan produk baru.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
