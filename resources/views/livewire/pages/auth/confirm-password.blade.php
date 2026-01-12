<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $password = '';

    /**
     * Konfirmasi kata sandi pengguna saat ini.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => 'Kata sandi yang Anda masukkan salah.',
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(
            default: route('dashboard', absolute: false),
            navigate: true
        );
    }
};
?>

<div class="card-container w-full max-w-md mx-auto">
    <div class="mb-4 text-sm text-gray-600">
        Demi keamanan, silakan konfirmasi kata sandi Anda sebelum melanjutkan.
    </div>

    <form wire:submit.prevent="confirmPassword" class="space-y-4">

        {{-- Kata Sandi --}}
        <div>
            <label for="password" class="text-sm text-gray-700">
                Kata Sandi
            </label>

            <input
                wire:model="password"
                id="password"
                type="password"
                class="column-input mt-1"
                placeholder="Masukkan kata sandi Anda"
                required
                autocomplete="current-password"
            >

            @error('password')
                <div class="text-red-500 text-sm mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-submit px-6">
                Konfirmasi
            </button>
        </div>
    </form>
</div>
