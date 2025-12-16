<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    // Password
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
{
    $user = Auth::user();

    $validated = $this->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => [
            'required', 'string', 'lowercase', 'email', 'max:255',
            Rule::unique(User::class)->ignore($user->id)
        ],
    ]);

    $user->fill($validated);

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    $user->save();

    // Flash message
    session()->flash('success', 'Profile updated successfully!');

    $this->dispatch('profile-updated', name: $user->name);
}


    public function updatePassword(): void
{
    $validated = $this->validate([
        'current_password' => ['required', 'string', 'current_password'],
        'password' => ['required', 'string', \Illuminate\Validation\Rules\Password::defaults(), 'confirmed'],
    ]);

    Auth::user()->update([
        'password' => Hash::make($validated['password']),
    ]);

    // Kosongkan semua input password
    $this->reset('current_password', 'password', 'password_confirmation');

    // Flash message
    session()->flash('success', 'Password updated successfully!');

    $this->dispatch('password-updated');
}

}; ?>

 @if (session()->has('message'))
            <div 
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 1000)"
                x-show="show"
                x-transition.duration.500ms
                class="text-xs p-2 rounded bg-green-100 text-green-700 border border-green-300"
            >
                {{ session('message') }}
            </div>
        @endif
<section>
    <!-- Header Profile -->
    <div class="flex items-center justify-between -mt-16 mb-10">
        <div class="flex items-center space-x-4">
            <div class="w-20 h-20 rounded-full bg-gray-300 overflow-hidden shadow-lg ring-4 ring-white">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&size=80&background=6366f1&color=fff" 
                     alt="{{ auth()->user()->name }}" 
                     class="w-full h-full object-cover">
            </div>

            <div>
                <h3 class="text-base font-semibold text-gray-900">{{ auth()->user()->name }}</h3>
                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 px-4 py-3 rounded-md bg-green-100 text-green-800 text-sm shadow">
            {{ session('success') }}
        </div>
    @endif

    <!-- GRID 2 KOLOM: PROFILE LEFT + PASSWORD RIGHT -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- ========================= LEFT: PROFILE FORM ========================= --}}
        <div class="space-y-6">
            <h2 class="text-sm font-semibold text-gray-900">Profile Information</h2>

            <form wire:submit="updateProfileInformation" class="space-y-4">

                <!-- Full Name -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Full Name</label>
                    <input wire:model="name" id="name" type="text"
                        class="w-full px-3 py-2 text-xs border border-gray-200 rounded-full bg-gray-50 focus:ring-blue-500"
                        required>
                    <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs" />
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
                    <input wire:model="email" id="email" type="email"
                        class="w-full px-3 py-2 text-xs border border-gray-200 rounded-full bg-gray-50 focus:ring-blue-500"
                        required>
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                </div>

                <!-- Save -->
                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        disabled
                        wire:dirty.remove.attr="disabled"
                        wire:dirty.class.remove="opacity-50 cursor-not-allowed"
                        wire:target="name,email"
                        class="px-4 py-2 bg-[#5ca9ff] text-white text-xs rounded-full
                            opacity-50 cursor-not-allowed transition"
                    >
                        Save Changes
                    </button>

                    <x-action-message on="profile-updated" class="text-xs text-green-600">
                        Saved successfully!
                    </x-action-message>
                </div>
            </form>
        </div>


        {{-- ========================= RIGHT: PASSWORD FORM ========================= --}}
        <div class="space-y-6">
            <h2 class="text-sm font-semibold text-gray-900">Update Password</h2>

            <form wire:submit="updatePassword" class="space-y-4">

                <!-- Current Password -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Current Password</label>
                    <input wire:model="current_password" type="password"
                        class="w-full px-3 py-2 text-xs border border-gray-200 rounded-full bg-gray-50 focus:ring-blue-500">
                    <x-input-error :messages="$errors->get('current_password')" class="mt-1 text-xs" />
                </div>

                <!-- New Password -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">New Password</label>
                    <input wire:model="password" type="password"
                        class="w-full px-3 py-2 text-xs border border-gray-200 rounded-full bg-gray-50 focus:ring-blue-500">
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input wire:model="password_confirmation" type="password"
                        class="w-full px-3 py-2 text-xs border border-gray-200 rounded-full bg-gray-50 focus:ring-blue-500">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
                </div>

                <!-- Save -->
                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-4 py-2 bg-[#5ca9ff] text-white text-xs rounded-full hover:bg-blue-700">
                        Update Password
                    </button>

                    <x-action-message on="password-updated" class="text-xs text-green-600">
                        Password updated successfully!
                    </x-action-message>
                </div>
            </form>
        </div>

    </div>
</section>
