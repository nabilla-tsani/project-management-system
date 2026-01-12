<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Hilangkan error saat field diubah
     */
    public function updated($property): void
    {
        $this->resetErrorBag($property);
    }

    /**
     * Proses registrasi
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(
            $user = User::create($validated)
        ));

        Auth::login($user);

$this->redirect(route('verification.notice'), navigate: true);
    }
};
?>

<div class="card-container w-full">
    <div class="text-center mb-2">
        <h2 class="text-2xl text-gray-900">Buat Akun Baru</h2>
        <p class="mt-2 text-sm text-gray-600">
            Silakan lengkapi formulir untuk mendaftar
        </p>
    </div>

    <form wire:submit.prevent="register" class="space-y-4">

        {{-- Nama --}}
        <div>
            <label class="text-sm text-gray-700">Nama</label>
            <input
                type="text"
                wire:model.lazy="name"
                class="column-input"
                placeholder="Masukkan nama lengkap"
            >

            @error('name')
                <div
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 2000)"
                    x-show="show"
                    x-transition.opacity
                    class="text-red-500 text-sm mt-1"
                >
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="text-sm text-gray-700">Email</label>
            <input
                type="email"
                wire:model.lazy="email"
                class="column-input"
                placeholder="Masukkan email"
            >

            @error('email')
                <div
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 2000)"
                    x-show="show"
                    x-transition.opacity
                    class="text-red-500 text-sm mt-1"
                >
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Kata Sandi --}}
        <div>
            <label class="text-sm text-gray-700">Kata Sandi</label>
            <input
                type="password"
                wire:model.lazy="password"
                class="column-input"
                placeholder="Masukkan kata sandi"
            >

            @error('password')
                <div
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 2000)"
                    x-show="show"
                    x-transition.opacity
                    class="text-red-500 text-sm mt-1"
                >
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Konfirmasi Kata Sandi --}}
        <div>
            <label class="text-sm text-gray-700">Konfirmasi Kata Sandi</label>
            <input
                type="password"
                wire:model.lazy="password_confirmation"
                class="column-input"
                placeholder="Ulangi kata sandi"
            >
        </div>

        <button type="submit" class="btn-submit w-full">
            Daftar
        </button>

        <div class="text-center text-sm text-gray-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-purple hover:underline">
                Masuk
            </a>
        </div>
    </form>
</div>
