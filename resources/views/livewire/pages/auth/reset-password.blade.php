<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Str;

new #[Layout('layouts.guest')] class extends Component
{
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    /**
     * Hilangkan error saat field diubah
     */
    public function updated($property): void
    {
        $this->resetErrorBag($property);
    }

    /**
     * Proses reset password
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            $this->addError('email', __($status));
            return;
        }

        session()->flash('status', 'Password berhasil direset. Silakan login.');

        $this->redirectRoute('login');
    }
};
?>

<div class="card-container w-full max-w-md mx-auto">
    <div class="text-center mb-2">
        <h2 class="text-lg text-gray-900">Atur Ulang Kata Sandi</h2>
        <p class="mt-2 text-sm text-gray-600 pb-4">
            Silakan masukkan kata sandi baru Anda di bawah ini
        </p>
    </div>

    {{-- Status Session --}}
    @if (session('status'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 2000)"
            x-show="show"
            x-transition.opacity
            class="mb-4 text-sm text-green-600 text-center"
        >
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit.prevent="resetPassword" class="space-y-4">

        {{-- Email --}}
        <div>
            <label class="text-sm text-gray-700">Email</label>
            <input
                type="email"
                wire:model.lazy="email"
                class="column-input bg-gray-100"
                readonly
            >
        </div>

        {{-- Kata Sandi Baru --}}
        <div>
            <label class="text-sm text-gray-700">Kata Sandi Baru</label>
            <input
                type="password"
                wire:model.lazy="password"
                class="column-input"
                placeholder="Minimal 8 karakter"
            >

            @error('password')
                <div class="text-red-500 text-sm mt-1">
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

        <button type="submit" class="btn-submit w-full text-sm">
            Atur Ulang Kata Sandi
        </button>

        <div class="text-center text-sm text-gray-600">
            Sudah ingat kata sandi?
            <a href="{{ route('login') }}" class="text-purple hover:underline">
                Masuk
            </a>
        </div>
    </form>
</div>
