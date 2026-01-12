<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\User;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }

    public function login()
    {
        // 🔥 PENTING: reset error sebelumnya
        $this->resetErrorBag();

        $this->validate();

        // 1️⃣ Jika email belum terdaftar
        if (! User::where('email', $this->email)->exists()) {
            $this->addError('login', 'Kamu belum terdaftar.');
            return;
        }

        // 2️⃣ Jika email ada tapi password salah
        if (! Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember)) {
            $this->addError('login', 'Email atau kata sandi salah.');
            return;
        }

        // 3️⃣ Login sukses
        Session::regenerate();

        return redirect()->route('dashboard');
    }
};
?>

<div class="card-container w-full">

    {{-- ===== HEADER ===== --}}
    <div class="text-center">
        <h2 class="text-2xl text-gray-900">Selamat Datang Kembali</h2>
        <p class="mt-2 text-sm text-gray-600">Silakan masuk untuk melanjutkan</p>
    </div>

    {{-- ===== ERROR MESSAGE (SATU SAJA) ===== --}}
    @error('login')
    <div
        wire:key="login-error-{{ now() }}"
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 1500)"
        x-show="show"
        x-transition
        class="text-red-500 text-sm text-center mt-3"
    >
        {{ $message }}
    </div>
    @enderror

    {{-- ===== FORM ===== --}}
    <form wire:submit.prevent="login" class="space-y-4 mt-4">

        <div>
            <label class="text-sm text-gray-700">Email</label>
            <input
                type="email"
                wire:model.defer="email"
                class="column-input"
                placeholder="Masukkan email"
            >
        </div>

        <div>
            <label class="text-sm text-gray-700">Kata Sandi</label>
            <input
                type="password"
                wire:model.defer="password"
                class="column-input"
                placeholder="Masukkan kata sandi"
            >
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center text-sm text-gray-600">
                <input
                    type="checkbox"
                    wire:model="remember"
                    class="rounded border-gray-300"
                >
                <span class="ml-2">Ingat saya</span>
            </label>

            <a href="{{ route('password.request') }}" class="text-purple">
                Lupa kata sandi?
            </a>
        </div>

        <button type="submit" class="btn-submit w-full">
            Masuk
        </button>

        <div class="text-center text-sm text-gray-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-purple">
                Daftar
            </a>
        </div>
    </form>
</div>
