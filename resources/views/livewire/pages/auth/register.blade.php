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

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);
        redirect()->route('dashboard');
    }
}; 
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register - Project Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #77b6ffff 0%, #edeaff 40%, ##ac7bffff 100%);
            position: relative;
            overflow: hidden;
            min-height: 100vh;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
        }
        body::before {
            content: "";
            position: absolute;
            bottom: -100px;
            right: -150px;
            width: 600px;
            height: 500px;
            background: radial-gradient(circle, #ac7bffff 100%);
            filter: blur(120px);
            opacity: 0.7;
            z-index: 0;
        }
        body::after {
            content: "";
            position: absolute;
            top: -100px;
            left: -150px;
            width: 600px;
            height: 500px;
            background: radial-gradient(circle, #77b6ffff 100%);
            filter: blur(120px);
            opacity: 0.7;
            z-index: 0;
        }

        .text-purple {
            font-size: 13px;
            font-weight: 500;
            color: #ac7bffff;
        }

        .column-input {
            margin-top: 5px;
            display: block;
            width: 100%;
            border-radius: 25px;
            border: 1px solid #d1d5db;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            font-size: 12px;
            padding: 0.5rem 0.75rem;
        }

        .column-input:focus {
            border-color: #ac7bffff;
            outline: none;
            box-shadow: 0 0 0 1px #ac7bffff;
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
            font-size: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .btn-submit:hover {
            background-color: #469cffff;
        }

        .btn-submit:focus {
            outline: none;
            box-shadow: 0 0 0 2px white, 0 0 0 4px #6366F1;
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


        p, label, span, a {
            font-size: 12px;
        }


        p {
            color: #4B5563; /* text-gray-600 */
        }

    </style>
</head>
<body class="flex items-center justify-center py-12">

<div class="card-container">
    <div class="text-center">
        <h2 class="text-3xl text-gray-900">
            Create an Account
        </h2>
        <p class="mt-2 text-sm text-gray-600">
            Please fill in the form to register
        </p>
    </div>

    <!-- Form -->
    <form wire:submit="register" class="mt-2 space-y-4">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" class="block text-gray-700" />
            <x-text-input wire:model="name" id="name"
                class="column-input"
                type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="text-red-500" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="block text-gray-700" />
            <x-text-input wire:model="email" id="email"
                class="column-input"
                type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="text-red-500" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="block text-gray-700" />
            <x-text-input wire:model="password" id="password"
                class="column-input"
                type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="text-red-500" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="block text-gray-700" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation"
                class="column-input"
                type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="text-red-500" />
        </div>

        <!-- Submit -->
        <div>
            <x-primary-button class="btn-submit">
                {{ __('Register') }}
            </x-primary-button>
        </div>

        <!-- Login Redirect -->
        <div class="text-center mt-2 text-gray-600">
            {{ __('Already registered?') }}
            <a href="{{ route('login') }}" wire:navigate class="text-purple">
                {{ __('Sign in') }}
            </a>
        </div>
    </form>
</div>

</body>
</html>
