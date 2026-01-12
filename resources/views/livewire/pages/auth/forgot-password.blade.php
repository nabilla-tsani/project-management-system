<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Hilangkan error saat field diubah
     */
    public function updated($property): void
    {
        $this->resetErrorBag($property);
    }

    /**
     * Kirim link reset password
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status !== Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
};
?>

<div class="card-container w-full max-w-md mx-auto">
    <div class="text-center mb-2">
        <h2 class="text-lg text-gray-900">Lupa Kata Sandi</h2>
        <p class="mt-2 text-sm text-gray-600 pb-4">
            Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi
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

    <form wire:submit.prevent="sendPasswordResetLink" class="space-y-4">

        {{-- Email --}}
        <div>
            <label class="text-sm text-gray-700">Email</label>
            <input
                type="email"
                wire:model.lazy="email"
                class="column-input"
                autofocus
                placeholder="Masukkan email terdaftar"
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

        <button type="submit" class="btn-submit w-full text-sm">
            Kirim Tautan Reset Kata Sandi
        </button>

        <div class="text-center text-sm text-gray-600">
            Sudah ingat kata sandi?
            <a href="{{ route('login') }}" class="text-purple hover:underline">
                Masuk
            </a>
        </div>
    </form>
</div>
