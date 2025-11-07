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

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Project Management</title>
    <!-- Use Tailwind CDN for standalone rendering; in production the app uses compiled assets -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
        /* Warna dasar gradasi */
        background: linear-gradient(135deg, #9c62ff 0%, #edeaff 40%, #5ca9ff 100%);
        
        /* Tambahan efek glow blur dengan warna ungu dan cyan */
        position: relative;
        overflow: hidden;
        min-height: 100vh;
        margin: 0;
        }

        body::before {
        content: "";
        position: absolute;
        top: -100px;
        left: -150px;
        width: 600px;
        height: 500px;
        background: radial-gradient(circle, #9c62ff 100%);
        filter: blur(120px);
        opacity: 0.7;
        z-index: 0;
        }

        body::after {
        content: "";
        position: absolute;
        bottom: -100px;
        right: -150px;
        width: 600px;
        height: 500px;
        background: radial-gradient(circle, #5ca9ff 100%);
        filter: blur(120px);
        opacity: 0.7;
        z-index: 0;
        }

        .text-purple {
        font-size: 13px; 
        font-weight: 500;   
        color: #9c62ff;      
        }
        .column-input {
        margin-top: 5px;        
        display: block;            
        width: 100%;                
        border-radius: 25px;     
        border: 1px solid #d1d5db;  
        box-shadow: 0 1px 2px rgba(0,0,0,0.05); 
        font-size: 0.875rem;        
        padding: 0.5rem 0.75rem;    
        }
        .column-input:focus {
        border-color: #9c62ff;
        outline: none;
        box-shadow: 0 0 0 1px #9c62ff;
        }

        .btn-submit {
        width: 100%;
        display: flex;
        justify-content: center;
        padding: 0.5rem 1rem; 
        border-radius: 25px; 
        background-color: #77b6ffff; 
        color: white;
        font-weight: 600; 
        box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
        }
        .btn-submit:hover {
        background-color: #469cffff; 
        }
        .btn-submit:focus {
        outline: none; 
        box-shadow:
            0 0 0 2px white,        
            0 0 0 4px #6366F1;      
        }
        .card-container {
            width: 100%;
            max-width: 24rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem; /* gap lebih kecil */
            background-color: rgba(255, 255, 255, 0.3);
            padding: 1.5rem; /* kurang dari 2rem */
            border-radius: 1rem;
            box-shadow: 0 25px 50px rgba(0,0,0,0.25);
            position: relative;
            z-index: 10;
        }

        .card-container h2 {
            margin-top: 0; /* hilangkan mt-2 */
        }

        </style>
    </head>
<body class="flex items-center justify-center py-12">

<div class="card-container">
        <div class="text-center">
            <h2 class="text-3xl text-gray-900">
                Welcome Back
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Please sign in to continue
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Form -->
        <form wire:submit="login" class="mt-2 space-y-4">
            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="block text-sm font-medium text-gray-700" />
                <x-text-input wire:model="form.email" id="email"
                    class="column-input"
                    type="email" name="email" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('form.email')" class="text-red-500 text-sm" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" class="block text-sm font-medium text-gray-700" />
                <x-text-input wire:model="form.password" id="password"
                    class="column-input"
                    type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('form.password')" class="text-red-500 text-sm" />
            </div>

            <!-- Remember Me + Forgot Password -->
            <div class="flex items-center justify-between">
                <label for="remember" class="flex items-center text-sm text-gray-600">
                    <input wire:model="form.remember" id="remember" type="checkbox"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="ml-2">Remember me</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate
                        class="text-purple">
                        Forgot password?
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <div>
                <x-primary-button
                    class="btn-submit">
                    Sign In
                </x-primary-button>
            </div>

            <!-- Register Redirect -->
            <div class="text-center mt-4 text-sm text-gray-600">
                {{ __("Don't have an account?") }}
                <a href="{{ route('register') }}" wire:navigate
                    class="text-purple">
                    Sign up
                </a>
            </div>
    </form>

</div>

</body>
</html>
