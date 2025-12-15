<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="h-full w-full">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <!-- Header Section with Gradient -->
                <div class="h-32 bg-gradient-to-r from-blue-200 via-blue-100 to-yellow-100"></div>
                
                <!-- Profile Content -->
                <div class="px-8 pb-8">
                    <livewire:profile.update-profile-information-form />
                    
                    <!-- Divider -->
                    <div class="my-8 border-t border-gray-200"></div>
                    
                    <!-- Divider -->
                    <div class="my-8 border-t border-gray-200"></div>
                    
                    <!-- Delete Account Section -->
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>