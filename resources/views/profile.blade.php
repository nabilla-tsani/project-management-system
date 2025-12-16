<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="w-full lg:w-[85%] mx-auto">
        
        <div class="h-full w-full">
            <div class="bg-white shadow overflow-hidden">
                <!-- Header Section with Gradient -->
                <div class="h-32 bg-gradient-to-r from-cyan-200 to-purple-300"></div>
                
                <!-- Profile Content -->
                <div class="px-8 pb-8">
                    <livewire:profile.update-profile-information-form />

                    
                    <!-- Divider -->
                    <div class="my-8 border-t border-gray-200"></div>
                    
                    <!-- Delete Account Section -->
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>