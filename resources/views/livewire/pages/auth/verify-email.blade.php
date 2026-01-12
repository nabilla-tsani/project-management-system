<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    public function goToLogin(): void
{
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    $this->redirect(route('login'), navigate: true);
}

}; ?>

<div class="card-container w-full max-w-md mx-auto">
    <div class="text-center mb-4">
        <h2 class="text-lg text-gray-900">Verifikasi Email</h2>
        <p class="mt-2 text-sm text-gray-600">
            Terima kasih telah mendaftar! Silakan verifikasi alamat email Anda
            dengan mengklik tautan yang baru saja kami kirimkan ke email Anda.
        </p>
    </div>

    {{-- Status --}}
    @if (session('status') == 'verification-link-sent')
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition.opacity
            class="mb-4 text-sm text-green-600 text-center"
        >
            Tautan verifikasi baru telah dikirim ke alamat email Anda.
        </div>
    @endif

    <div class="space-y-4">

        {{-- Kirim Ulang Verifikasi --}}
        <button
            wire:click="sendVerification"
            class="btn-submit w-full text-sm"
        >
            Kirim Ulang Email Verifikasi
        </button>

        {{-- Keluar --}}
        <button
    wire:click="goToLogin"
    class="block w-full text-sm text-center text-gray-600 hover:text-gray-900 underline"
>
    Halaman Login
</button>


    </div>
</div>
