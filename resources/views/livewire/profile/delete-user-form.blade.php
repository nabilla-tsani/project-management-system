<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use App\Models\ProyekUser;
use App\Models\ProyekFiturUser;

new class extends Component
{
    public string $password = '';
    public bool $showPasswordInput = false; // state untuk menampilkan input

    public function confirmDelete()
    {
        $this->showPasswordInput = true;
    }

    public function deleteUser(Logout $logout)
    {
        try {
            $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ], [
            'password.current_password' => 'Kata sandi yang Anda masukkan salah.',
        ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Reset input password jika validasi gagal
            $this->reset('password');
            throw $e; // lempar lagi supaya Livewire tetap menampilkan error
        }

        $userId = Auth::id();

        // ===============================
        // CEK KETERLIBATAN USER
        // ===============================
        $terlibatProyek = ProyekUser::where('user_id', $userId)->exists();
        $terlibatFitur  = ProyekFiturUser::where('user_id', $userId)->exists();

        if ($terlibatProyek || $terlibatFitur) {
            // Tambahkan error
            $this->addError(
                'password',
                'Akun tidak dapat dihapus karena masih terlibat dalam proyek atau fitur.'
            );
            $this->reset('password'); // kosongkan input password
            return;
        }

        // ===============================
        // LOGOUT & HAPUS AKUN
        // ===============================
        tap(Auth::user(), $logout(...))->delete();

        return redirect('/');
    }

    public function cancelDelete()
    {
        $this->reset([
            'password',
            'showPasswordInput',
        ]);

        $this->resetErrorBag();      // hapus semua error
        $this->clearValidation();    // pastikan validasi bersih
    }

};

?>

<section>
    <header class="mb-6">
        <h2 class="text-sm font-semibold text-gray-900">
            Hapus Akun
        </h2>

        <p class="mt-1 text-xs text-gray-600">
            Setelah akun dihapus, seluruh data akan dihapus secara permanen.
            Masukkan kata sandi untuk mengonfirmasi penghapusan akun.
        </p>
    </header>

    <form wire:submit.prevent="deleteUser" class="space-y-5">

        {{-- Tombol Hapus --}}
        @if(!$showPasswordInput)
            <button
                type="button"
                wire:click="confirmDelete"
                class="px-5 py-2 bg-red-600 text-white text-xs font-medium rounded-full hover:bg-red-700 transition-colors"
            >
                Hapus Akun
            </button>
        @endif

        {{-- Password --}}
        @if($showPasswordInput)
        <div>
            <label for="password" class="block text-xs font-medium text-gray-700 mb-1">
                Kata Sandi
            </label>

            <input
                wire:model.defer="password"
                id="password"
                type="password"
                placeholder="Masukkan kata sandi Anda"
                class="w-full px-4 py-2 text-xs border
                    @error('password') border-red-500 @else border-gray-300 @enderror
                    rounded-full bg-gray-50 focus:ring-1 focus:ring-red-500 focus:border-red-500"
            >

            {{-- Error --}}
            @error('password')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
            @enderror

            <div class="flex items-center gap-3 mt-3">
                <button
                    type="submit"
                    class="px-5 py-2 bg-red-600 text-white text-xs font-medium rounded-full hover:bg-red-700 transition-colors"
                >
                    Konfirmasi Hapus
                </button>

                <button
                    type="button"
                    wire:click="cancelDelete"
                    class="px-5 py-2 bg-gray-300 text-gray-700 text-xs font-medium rounded-full hover:bg-gray-400 transition-colors"
                >
                    Batal
                </button>


            </div>
        </div>
        @endif

    </form>
</section>
