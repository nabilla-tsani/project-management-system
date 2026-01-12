<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="w-full lg:w-[100%] mx-auto">
        
        <div class="h-full w-full">
            <div class="bg-white shadow overflow-hidden">
                <!-- Header Section with Gradient -->
                <div class="h-24 bg-gradient-to-r from-cyan-400 to-purple-600"></div>
                
                <!-- Profile Content -->
                <div class="px-8 pb-6">
                    <div class="w-full lg:w-[70%] mx-auto">

                        <livewire:profile.update-profile-information-form />

                        <!-- Divider -->
                        <div class="border-t border-gray-200"></div>
                        

                        <!-- Delete Account Section -->
                        <livewire:profile.delete-user-form />


                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>