<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section>
    <header class="mb-6">
        <h2 class="text-sm font-semibold text-gray-900">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-xs text-gray-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="px-4 py-2 bg-red-600 text-white text-xs font-medium rounded-full hover:bg-red-700 transition-colors">
        {{ __('Delete Account') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6">

            <h2 class="text-base font-semibold text-gray-900 mb-3">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="text-xs text-gray-600 mb-6">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mb-6">
                <label for="password" class="block text-xs font-medium text-gray-700 mb-1">
                    {{ __('Password') }}
                </label>

                <input wire:model="password"
                       id="password"
                       name="password"
                       type="password"
                       placeholder="{{ __('Enter your password') }}"
                       class="w-full px-3 py-2 text-xs border border-gray-200 rounded-full focus:ring-1 focus:ring-red-500 focus:border-red-500 bg-gray-50">

                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
            </div>

            <div class="flex justify-end gap-3">
                <button type="button"
                        x-on:click="$dispatch('close')"
                        class="px-4 py-2 bg-gray-200 text-gray-700 text-xs font-medium rounded-full hover:bg-gray-300 transition-colors">
                    {{ __('Cancel') }}
                </button>

                <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white text-xs font-medium rounded-full hover:bg-red-700 transition-colors">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>