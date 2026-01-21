<x-guest-layout>
    <div class="bg-amber-50 min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="bg-amber-100 rounded-xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-amber-500 py-6 px-8 text-center">
                    <h1 class="text-2xl font-bold text-white">Inventory System</h1>
                    <p class="text-amber-50 mt-1">Silakan masuk ke akun Anda</p>
                </div>

                <!-- Form Login (VERSI BARU) -->
                <form method="POST" action="{{ route('login') }}" class="p-8">
                    @csrf
                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-4 text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Username -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="username">
                            Username (versi baru)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input
                                id="username"
                                type="text"
                                name="username"
                                value="{{ old('username') }}"
                                required
                                autofocus
                                autocomplete="username"
                                class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition duration-200"
                                placeholder="Masukkan username Anda">
                        </div>
                        @error('username')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-gray-700 text-sm font-medium" for="password">
                                Password
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">
                                    Lupa Password?
                                </a>
                            @endif
                        </div>
                        <!-- Container flex agar input dan icon mata jadi satu kotak -->
                        <div class="flex rounded-lg border border-gray-300 focus-within:ring-2 focus-within:ring-amber-500 focus-within:border-transparent overflow-hidden bg-amber-50">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    class="w-full pl-10 pr-3 py-3 text-sm border-0 focus:ring-0 focus:outline-none"
                                    placeholder="Masukkan password Anda (baru)">
                            </div>
                            <!-- Tombol icon mata untuk lihat/sembunyikan password -->
                            <button
                                type="button"
                                id="togglePassword"
                                class="w-12 flex items-center justify-center border-l border-gray-200 bg-gray-50 text-gray-400 hover:text-gray-600 hover:bg-gray-100 focus:outline-none">
                                <!-- Pakai SVG supaya tidak tergantung CDN icon -->
                                <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-4">
                        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                            <i class="fas fa-sign-in-alt mr-2"></i> Masuk ke Dashboard
                        </button>
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-6 flex items-center">
                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                            Ingat Saya
                        </label>
                    </div>
                </form>

                <!-- Footer -->
                <div class="bg-amber-50 px-8 py-4 text-center border-t border-amber-100">
                    <p class="text-sm text-gray-700">
                        Belum punya akun?
                        <a href="#" class="text-amber-600 hover:underline font-medium">Hubungi Admin</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Script sederhana untuk toggle icon mata & respons tombol login -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');

            if (passwordInput && togglePassword) {
                togglePassword.addEventListener('click', function () {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                });
            }
        });
    </script>
</x-guest-layout>
