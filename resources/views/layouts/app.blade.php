<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LANTANA ADVENTURE')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body class="bg-emerald-900 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-emerald-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
<div class="flex-shrink-0 flex items-center">
    <img src="{{ asset('images/logo-lantana.png') }}"
         alt="Lantana Adventure"
         class="h-10 w-auto object-contain">
</div>

                <!-- Desktop Menu -->
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="{{ route('dashboard') }}" class="border-amber-400 text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>
                    <a href="{{ route('products.index') }}" class="border-transparent text-emerald-100 hover:border-emerald-300 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        <i class="fas fa-boxes mr-2"></i> Produk
                    </a>
                    <a href="{{ route('categories.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        <i class="fas fa-tags mr-2"></i> Kategori
                    </a>
                    <a href="{{ route('sales.index') }}" class="border-transparent text-emerald-100 hover:border-emerald-300 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        <i class="fas fa-shopping-cart mr-2"></i> Penjualan
                    </a>
                </div>

                <!-- Right Side Of Navbar -->
                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    <div class="ml-3 relative">
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <button type="button" class="p-1 rounded-full text-emerald-100 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-400">
                                <span class="sr-only">View notifications</span>
                                <i class="h-6 w-6 fas fa-bell"></i>
                            </button>
                            
                            <!-- Profile dropdown -->
                            <div class="ml-3 relative">
                                <div class="flex items-center">
                                    <div class="text-right mr-3">
                                        <div class="text-sm font-medium text-emerald-50">{{ Auth::user()->name ?? 'Guest' }}</div>
                                        <div class="text-xs text-emerald-200">Admin</div>
                                    </div>
                                    <div class="h-8 w-8 rounded-full bg-amber-400 flex items-center justify-center text-emerald-900">
                                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Logout -->
                            @auth
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-emerald-100 hover:text-white text-sm">
                                    <i class="fas fa-sign-out-alt mr-1"></i> Keluar
                                </button>
                            </form>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-emerald-100 hover:text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-amber-400" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu, show/hide based on menu state. -->
        <div class="sm:hidden" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="{{ route('dashboard') }}" class="bg-emerald-700 border-amber-400 text-white block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                <a href="{{ route('products.index') }}" class="border-transparent text-emerald-100 hover:bg-emerald-800 hover:border-emerald-400 hover:text-white block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    <i class="fas fa-boxes mr-2"></i> Produk
                </a>
                <a href="{{ route('categories.index') }}" class="border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    <i class="fas fa-tags mr-2"></i> Kategori
                </a>
                <a href="{{ route('sales.index') }}" class="border-transparent text-emerald-100 hover:bg-emerald-800 hover:border-emerald-400 hover:text-white block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    <i class="fas fa-shopping-cart mr-2"></i> Penjualan
                </a>
                @auth
                <form method="POST" action="{{ route('logout') }}" class="border-t border-emerald-700 pt-2 mt-2">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-amber-300 hover:bg-emerald-800">
                        <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                    </button>
                </form>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex gap-4 lg:gap-6">
                <!-- Sidebar kiri -->
                <aside class="hidden md:flex md:w-72 shrink-0 flex-col bg-emerald-800 rounded-xl shadow-lg overflow-hidden text-emerald-50">
                    <div class="bg-emerald-900 px-6 py-5 flex items-center space-x-3">
                        <div class="h-10 w-10 rounded-full bg-amber-400 flex items-center justify-center text-emerald-900 text-xl font-bold">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div class="text-sm font-semibold leading-tight">{{ Auth::user()->name ?? 'Guest' }}</div>
                            <div class="text-[11px] text-emerald-200">{{ Auth::user()->username ?? Auth::user()->email ?? '' }}</div>
                        </div>
                    </div>

                    <div class="flex-1 bg-[url('https://images.unsplash.com/photo-1524592743502-032d6c33c9da?auto=format&fit=crop&w=600&q=60')] bg-cover bg-center bg-no-repeat bg-blend-overlay bg-emerald-900/95 px-4 py-5 space-y-2 text-sm">
                        <a href="{{ route('dashboard') }}" class="flex items-center justify-between px-3 py-2 rounded-md hover:bg-emerald-700/70 transition">
                            <span class="flex items-center space-x-3">
                                <i class="fas fa-tachometer-alt text-emerald-100"></i>
                                <span>Dashboard</span>
                            </span>
                            <i class="fas fa-chevron-right text-xs text-emerald-200"></i>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center justify-between px-3 py-2 rounded-md hover:bg-emerald-700/70 transition">
                            <span class="flex items-center space-x-3">
                                <i class="fas fa-user text-emerald-100"></i>
                                <span>Profil Saya</span>
                            </span>
                            <i class="fas fa-chevron-right text-xs text-emerald-200"></i>
                        </a>
                        <a href="{{ route('products.index') }}" class="flex items-center justify-between px-3 py-2 rounded-md hover:bg-emerald-700/70 transition">
                            <span class="flex items-center space-x-3">
                                <i class="fas fa-boxes text-emerald-100"></i>
                                <span>Data Produk</span>
                            </span>
                            <i class="fas fa-chevron-right text-xs text-emerald-200"></i>
                        </a>
                        <a href="{{ route('categories.index') }}" class="flex items-center justify-between px-3 py-2 rounded-md hover:bg-emerald-700/70 transition">
                            <span class="flex items-center space-x-3">
                                <i class="fas fa-tags text-emerald-100"></i>
                                <span>Kategori</span>
                            </span>
                            <i class="fas fa-chevron-right text-xs text-emerald-200"></i>
                        </a>
                        <a href="{{ route('sales.index') }}" class="flex items-center justify-between px-3 py-2 rounded-md hover:bg-emerald-700/70 transition">
                            <span class="flex items-center space-x-3">
                                <i class="fas fa-shopping-cart text-emerald-100"></i>
                                <span>Transaksi Penjualan</span>
                            </span>
                            <i class="fas fa-chevron-right text-xs text-emerald-200"></i>
                        </a>
                    </div>
                </aside>

                <!-- Konten utama -->
                <div class="flex-1">
                    <div class="bg-gray-100 rounded-xl shadow-lg p-4 sm:p-6 min-h-[calc(100vh-8rem)]">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-emerald-800 border-t border-emerald-700 mt-0">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-emerald-100">
                &copy; {{ date('Y') }} nabilatuzzahroh project. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('button[aria-expanded]');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    const expanded = this.getAttribute('aria-expanded') === 'true' || false;
                    this.setAttribute('aria-expanded', !expanded);
                    mobileMenu.classList.toggle('hidden', expanded);
                });
            }
        });

        document.addEventListener('submit', function(e) {
            const form = e.target;

            if (!(form instanceof HTMLFormElement)) {
                return;
            }

            if (!form.hasAttribute('data-swal')) {
                return;
            }

            e.preventDefault();

            const title = form.getAttribute('data-swal-title') || 'Hapus data?';
            const text = form.getAttribute('data-swal-text') || 'Data akan dihapus.';
            const confirmText = form.getAttribute('data-swal-confirm') || 'Hapus';
            const cancelText = form.getAttribute('data-swal-cancel') || 'Batal';

            Swal.fire({
                title,
                html: `
                    <div class="flex flex-col items-center gap-3">
                        <div class="h-16 w-16 rounded-full bg-red-50 flex items-center justify-center">
                            <i class="fas fa-trash text-red-600 text-2xl"></i>
                        </div>
                        <div class="text-sm text-gray-600">${text}</div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: confirmText,
                cancelButtonText: cancelText,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                reverseButtons: true,
                focusCancel: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }, true);
    </script>
    @stack('scripts')
</body>
</html>
