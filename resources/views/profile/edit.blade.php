@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
        <div>
            <h1 class="text-2xl font-bold text-emerald-900">Profil Pengguna</h1>
            <p class="mt-1 text-sm text-emerald-700">Kelola informasi akun, email, dan keamanan password Anda.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informasi Profil + Password -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow p-4 sm:p-6 space-y-8">
            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Informasi Profil</h2>
                <p class="text-sm text-gray-500 mb-4">Perbarui nama dan alamat email yang digunakan pada sistem.</p>
                <div class="max-w-xl space-y-4">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

        <!-- Riwayat Aktivitas -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow p-4 sm:p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Riwayat Aktivitas</h2>
                <p class="text-sm text-gray-500 mb-4">Ringkasan aktivitas akun ini (contoh data sementara).</p>
                <div class="space-y-3 text-sm">
                    <div class="flex items-start space-x-3">
                        <div class="h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700">
                            <i class="fas fa-sign-in-alt text-xs"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Login ke sistem</p>
                            <p class="text-gray-500 text-xs">Berhasil login ke dashboard</p>
                            <p class="text-[11px] text-gray-400 mt-1">Baru saja</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700">
                            <i class="fas fa-user-edit text-xs"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Perbarui profil</p>
                            <p class="text-gray-500 text-xs">Mengubah informasi nama atau email</p>
                            <p class="text-[11px] text-gray-400 mt-1">Hari ini</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="h-8 w-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-700">
                            <i class="fas fa-key text-xs"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Reset password</p>
                            <p class="text-gray-500 text-xs">Mengganti password akun</p>
                            <p class="text-[11px] text-gray-400 mt-1">Minggu ini</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-4 sm:p-6 border border-red-100">
                <h2 class="text-lg font-semibold text-red-600 mb-2">Hapus Akun</h2>
                <p class="text-sm text-gray-500 mb-4">Tindakan ini bersifat permanen dan tidak dapat dibatalkan.</p>
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
