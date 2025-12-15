<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header class="mb-6">
        <h2 class="text-sm font-semibold text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-xs text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form wire:submit="updatePassword" class="space-y-4">
        <div class="grid grid-cols-1 gap-4">
            <!-- Current Password -->
            <div>
                <label for="update_password_current_password" class="block text-xs font-medium text-gray-700 mb-1">
                    {{ __('Current Password') }}
                </label>
                <input wire:model="current_password" 
                       id="update_password_current_password" 
                       name="current_password" 
                       type="password" 
                       placeholder="Enter your current password"
                       class="w-full px-3 py-2 text-xs border border-gray-200 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-gray-50" 
                       autocomplete="current-password">
                <x-input-error :messages="$errors->get('current_password')" class="mt-1 text-xs" />
            </div>

            <!-- New Password -->
            <div>
                <label for="update_password_password" class="block text-xs font-medium text-gray-700 mb-1">
                    {{ __('New Password') }}
                </label>
                <input wire:model="password" 
                       id="update_password_password" 
                       name="password" 
                       type="password" 
                       placeholder="Enter your new password"
                       class="w-full px-3 py-2 text-xs border border-gray-200 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-gray-50" 
                       autocomplete="new-password">
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="update_password_password_confirmation" class="block text-xs font-medium text-gray-700 mb-1">
                    {{ __('Confirm Password') }}
                </label>
                <input wire:model="password_confirmation" 
                       id="update_password_password_confirmation" 
                       name="password_confirmation" 
                       type="password" 
                       placeholder="Confirm your new password"
                       class="w-full px-3 py-2 text-xs border border-gray-200 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-gray-50" 
                       autocomplete="new-password">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700 transition-colors">
                {{ __('Update Password') }}
            </button>

            <x-action-message class="text-xs text-green-600" on="password-updated">
                {{ __('Password updated successfully!') }}
            </x-action-message>
        </div>
    </form>
</section>