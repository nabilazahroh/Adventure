@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Transaksi Penjualan</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola daftar transaksi penjualan produk (barang keluar).</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('sales.exportStockOpname', ['from' => $from ?? null, 'to' => $to ?? null]) }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100">
                <i class="fas fa-file-excel mr-2"></i>
                Download Excel
            </a>
            <a href="{{ route('sales.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg shadow hover:bg-green-700">
                + Tambah Transaksi
            </a>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('sales.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4 md:items-end">
            <div>
                <label for="from" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                <input type="date" name="from" id="from" value="{{ $from ?? '' }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
            </div>
            <div>
                <label for="to" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                <input type="date" name="to" id="to" value="{{ $to ?? '' }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-emerald-700 rounded-lg shadow hover:bg-emerald-800">
                    Filter
                </button>
                <a href="{{ route('sales.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Reset
                </a>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-lg border border-emerald-100 bg-emerald-50 p-3">
                    <div class="text-xs text-emerald-700">Total Penjualan</div>
                    <div class="text-sm font-bold text-emerald-900">Rp {{ number_format($totalSales ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="rounded-lg border border-amber-100 bg-amber-50 p-3">
                    <div class="text-xs text-amber-700">Total Keuntungan</div>
                    <div class="text-sm font-bold text-amber-900">Rp {{ number_format($totalProfit ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </form>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 p-4 mb-6">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
        @if($sales->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada transaksi</h3>
                <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan transaksi penjualan pertama.</p>
                <div class="mt-6">
                    <a href="{{ route('sales.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg shadow hover:bg-green-700">
                        + Tambah Transaksi
                    </a>
                </div>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keuntungan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($sales as $index => $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $sale->transaction_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $sale->product->name }}</div>
                                <div class="text-xs text-gray-500">{{ strtoupper($sale->product->unit_type) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sale->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($sale->selling_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">Rp {{ number_format($sale->profit, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $sale->notes ? Str::limit($sale->notes, 30) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('sales.edit', $sale) }}" class="inline-flex items-center justify-center h-8 w-8 text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('sales.destroy', $sale) }}" method="POST" data-swal data-swal-title="Hapus transaksi ini?" data-swal-text="Stok akan dikembalikan otomatis" data-swal-confirm="Hapus" data-swal-cancel="Batal">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center h-8 w-8 text-red-700 bg-red-50 rounded-lg hover:bg-red-100" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
