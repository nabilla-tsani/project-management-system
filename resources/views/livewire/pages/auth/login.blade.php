<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login()
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();
        return redirect(route('dashboard'));
       // $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

    <div class="w-full max-w-md space-y-8">
        <!-- Logo / Title -->
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                {{ __('Welcome Back') }}
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                {{ __('Please sign in to continue') }}
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Form -->
        <form wire:submit="login" class="mt-8 space-y-6 bg-white p-8 shadow-lg rounded-2xl">
            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="block text-sm font-medium text-gray-700" />
                <x-text-input wire:model="form.email" id="email"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    type="email" name="email" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('form.email')" class="mt-2 text-red-500 text-sm" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" class="block text-sm font-medium text-gray-700" />
                <x-text-input wire:model="form.password" id="password"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('form.password')" class="mt-2 text-red-500 text-sm" />
            </div>

            <!-- Remember Me + Forgot Password -->
            <div class="flex items-center justify-between">
                <label for="remember" class="flex items-center text-sm text-gray-600">
                    <input wire:model="form.remember" id="remember" type="checkbox"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="ml-2">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <div>
                <x-primary-button
                    class="w-full flex justify-center py-2 px-4 rounded-xl bg-indigo-600 text-white font-semibold shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('Sign In') }}
                </x-primary-button>
            </div>

            <!-- Register Redirect -->
            <div class="text-center mt-4 text-sm text-gray-600">
                {{ __("Don't have an account?") }}
                <a href="{{ route('register') }}" wire:navigate
                    class="font-medium text-indigo-600 hover:text-indigo-500">
                    {{ __('Sign up') }}
                </a>
            </div>
        </form>
        
</div>
