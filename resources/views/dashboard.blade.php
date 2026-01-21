@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6 relative">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
        <div>
            <h1 class="text-2xl font-bold text-emerald-900">Selamat Datang, {{ Auth::user()->name }}!</h1>
            <p class="mt-1 text-sm text-emerald-700">Ringkasan singkat kondisi inventory dan aktivitas sistem hari ini.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 focus:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> Transaksi Baru
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 bg-emerald-800 text-white flex items-center justify-between">
            <h2 class="text-lg font-semibold">Ringkasan Inventory</h2>
            <span class="text-xs uppercase tracking-widest bg-emerald-700 px-3 py-1 rounded-full">Dashboard</span>
        </div>
        <div class="p-6 bg-emerald-50">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <!-- Total Produk -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-4 pt-3 pb-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-gray-700">Total Produk</h3>
                        <span class="text-xs text-gray-400">Semua data</span>
                    </div>
                    <div class="p-4 flex flex-col items-center justify-center space-y-3">
                        <div class="text-3xl font-bold text-emerald-700">{{ $totalProducts ?? 0 }}</div>
                        <div class="text-xs text-gray-500 text-center">Jumlah seluruh produk yang terdaftar di sistem.</div>
                        <div class="mt-3 flex space-x-2 text-[11px] text-gray-400">
                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-emerald-50 text-emerald-700"><i class="fas fa-box mr-1"></i>Produk aktif</span>
                        </div>
                    </div>
                </div>

                <!-- Total Kategori -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-4 pt-3 pb-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-gray-700">Total Kategori</h3>
                        <span class="text-xs text-gray-400">Struktur produk</span>
                    </div>
                    <div class="p-4 flex flex-col items-center justify-center space-y-4">
                        <div class="relative h-32 w-32">
                            <div class="absolute inset-0 rounded-full border-[10px] border-emerald-100"></div>
                            <div class="absolute inset-0 rounded-full border-[10px] border-emerald-500 border-t-transparent border-l-transparent rotate-45"></div>
                            <div class="absolute inset-4 rounded-full bg-white flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-emerald-700">{{ $totalCategories ?? 0 }}</div>
                                    <div class="text-xs text-gray-500">Kategori</div>
                                </div>
                            </div>
                        </div>
                        <div class="w-full flex justify-center text-xs text-gray-500 mt-2">
                            <span>Pengelompokan produk berdasarkan kategori.</span>
                        </div>
                    </div>
                </div>

                <!-- Total Penjualan Hari Ini -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-4 pt-3 pb-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-gray-700">Penjualan Hari Ini</h3>
                        <span class="text-xs text-gray-400">{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <div class="p-4 flex flex-col items-center justify-center space-y-4">
                        <div class="relative h-32 w-32">
                            <div class="absolute inset-0 rounded-full border-[10px] border-amber-100"></div>
                            <div class="absolute inset-0 rounded-full border-[10px] border-amber-400"></div>
                            <div class="absolute inset-4 rounded-full bg-white flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-amber-500">Rp {{ number_format($todaySales ?? 0, 0, ',', '.') }}</div>
                                    <div class="text-[11px] text-gray-500">Total transaksi</div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center text-xs text-gray-600">
                            Ringkasan nilai penjualan yang tercatat pada hari ini.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Produk Terlaris</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Daftar produk dengan penjualan tertinggi</p>
        </div>
        <div class="bg-white shadow overflow-hidden sm:rounded-b-lg">
            <div class="flow-root">
                <ul class="divide-y divide-gray-200">
                    @forelse($topProducts as $product)
                    <li class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-md bg-gray-200 flex items-center justify-center">
                                @if($product->image_path)
                                <img class="h-10 w-10 rounded-md object-cover" src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}">
                                @else
                                <i class="fas fa-box text-gray-400"></i>
                                @endif
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $product->name }}</h4>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $product->total_sold }} Terjual
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-1 flex items-center justify-between">
                                    <p class="text-sm text-gray-500">{{ strtoupper($product->unit_type) }} • Stok: {{ $product->stock }}</p>
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="px-6 py-4 text-center text-gray-500">
                        Tidak ada data produk
                    </li>
                    @endforelse
                </ul>
            </div>
            <div class="bg-gray-50 px-6 py-3 text-right text-sm">
                <a href="{{ route('products.index') }}" class="font-medium text-blue-600 hover:text-blue-500">Lihat semua produk</a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <h3 class="text-lg leading-6 font-medium text-emerald-900 mb-4">Ringkasan Cepat</h3>
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-4 py-2 bg-slate-100 border-b border-gray-200 text-sm font-semibold text-gray-700">Data Master</div>
                <div class="p-4 space-y-2 text-sm text-gray-700">
                    <div class="flex justify-between"><span>Total Produk</span><span class="font-semibold text-emerald-600">{{ $totalProducts ?? 0 }}</span></div>
                    <div class="flex justify-between"><span>Total Kategori</span><span class="font-semibold text-emerald-600">{{ $totalCategories ?? 0 }}</span></div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-4 py-2 bg-slate-100 border-b border-gray-200 text-sm font-semibold text-gray-700">Stok</div>
                <div class="p-4 space-y-2 text-sm text-gray-700">
                    <div class="flex justify-between"><span>Produk stok rendah</span><span class="font-semibold text-emerald-600">{{ $lowStockCount ?? 0 }}</span></div>
                    <div class="flex justify-between"><span>Perlu dicek ulang</span><span class="font-semibold text-emerald-600">{{ $lowStockCount > 0 ? 'Ya' : 'Tidak' }}</span></div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-4 py-2 bg-slate-100 border-b border-gray-200 text-sm font-semibold text-gray-700">Penjualan</div>
                <div class="p-4 space-y-2 text-sm text-gray-700">
                    <div class="flex justify-between"><span>Nilai hari ini</span><span class="font-semibold text-emerald-600">Rp {{ number_format($todaySales ?? 0, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span>Trend singkat</span><span class="font-semibold text-emerald-600">-</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification panel -->
    @if($lowStockCount > 0)
    <div class="hidden md:block fixed bottom-6 right-8 space-y-3 z-20">
        <div class="w-80 bg-red-500 text-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-4 py-2 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span class="font-semibold text-sm">Peringatan Stok Rendah</span>
                </div>
                <button onclick="this.closest('.w-80').remove()" class="text-white/80 hover:text-white text-xs">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-4 py-3 bg-red-400/90">
                <div class="text-xs mb-2">
                    <span class="font-semibold">{{ $lowStockCount }} produk</span> memiliki stok ≤ 5 unit
                </div>
                <div class="space-y-1 max-h-32 overflow-y-auto">
                    @foreach($lowStockProducts->take(3) as $product)
                    <div class="text-xs flex justify-between items-center bg-white/10 px-2 py-1 rounded">
                        <span>{{ Str::limit($product->name, 20) }}</span>
                        <span class="font-bold">{{ $product->stock }} {{ strtoupper($product->unit_type) }}</span>
                    </div>
                    @endforeach
                </div>
                @if($lowStockCount > 3)
                <div class="text-xs mt-2 text-center">
                    <a href="{{ route('products.index') }}" class="underline hover:text-white/80">+{{ $lowStockCount - 3 }} lainnya</a>
                </div>
                @endif
            </div>
        </div>

        <div class="w-80 bg-emerald-500 text-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-4 py-2 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-chart-line"></i>
                    <span class="font-semibold text-sm">Penjualan Hari Ini</span>
                </div>
                <button onclick="this.closest('.w-80').remove()" class="text-white/80 hover:text-white text-xs">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-4 py-3 text-xs bg-emerald-400/90 flex justify-between items-center">
                <span>Total: Rp {{ number_format($todaySales ?? 0, 0, ',', '.') }}</span>
                <span class="ml-2 inline-flex items-center justify-center h-6 w-6 rounded-full bg-white/20 text-[10px] font-bold">{{ now()->format('d/m') }}</span>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
