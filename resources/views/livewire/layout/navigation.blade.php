<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="flex flex-col h-full justify-between overflow-hidden px-3">
    
    <div class="mb-10 flex items-center h-10" :class="sidebarOpen ? 'justify-between px-2' : 'justify-center'">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 text-brand dark:text-white transition overflow-hidden" wire:navigate>
            <svg class="w-8 h-8 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
            <span x-show="sidebarOpen" x-transition.opacity.duration.300ms class="text-2xl font-bold tracking-tight whitespace-nowrap">Sosmed</span>
        </a>

        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-xl text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 dark:text-gray-400 transition shrink-0" title="Toggle Sidebar" :class="!sidebarOpen && 'hidden'">
            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-xl text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 dark:text-gray-400 transition shrink-0 absolute" x-show="!sidebarOpen" x-cloak>
            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
    </div>

    <nav class="space-y-2 flex-1">
        
        <a href="{{ route('dashboard') }}" class="flex items-center rounded-xl transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-brand font-bold dark:bg-blue-900/20 dark:text-blue-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800/50' }}"
           :class="sidebarOpen ? 'px-4 py-3' : 'justify-center py-3 w-14 mx-auto'" wire:navigate>
            <svg class="w-7 h-7 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span x-show="sidebarOpen" x-transition.opacity.duration.300ms class="ml-4 text-lg whitespace-nowrap overflow-hidden">Home</span>
        </a>

        <a href="{{ route('explore') }}" class="flex items-center rounded-xl transition-all duration-300 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800/50"
           :class="sidebarOpen ? 'px-4 py-3' : 'justify-center py-3 w-14 mx-auto'">
            <svg class="w-7 h-7 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <span x-show="sidebarOpen" x-transition.opacity.duration.300ms class="ml-4 text-lg whitespace-nowrap overflow-hidden">Explore</span>
        </a>

        <a href="{{ route('user.profile', ['username' => auth()->user()->username]) }}" 
           class="flex items-center rounded-xl transition-all duration-300 {{ request()->routeIs('user.profile') && request()->route('username') === auth()->user()->username ? 'bg-blue-50 text-brand font-bold dark:bg-blue-900/20 dark:text-blue-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800/50' }}"
           :class="sidebarOpen ? 'px-4 py-3' : 'justify-center py-3 w-14 mx-auto'" wire:navigate>
            <svg class="w-7 h-7 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span x-show="sidebarOpen" x-transition.opacity.duration.300ms class="ml-4 text-lg whitespace-nowrap overflow-hidden">Profile</span>
        </a>

    </nav>

    <div class="space-y-2 mt-auto">
        
        <button @click="darkMode = !darkMode" class="w-full flex items-center rounded-xl transition-all duration-300 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800/50"
                :class="sidebarOpen ? 'px-4 py-3' : 'justify-center py-3 w-14 mx-auto'">
            <div class="relative w-7 h-7 shrink-0">
                <svg x-show="darkMode" x-transition.opacity class="w-7 h-7 absolute inset-0 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <svg x-show="!darkMode" x-transition.opacity class="w-7 h-7 absolute inset-0 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            </div>
            <span x-show="sidebarOpen" x-transition.opacity.duration.300ms class="ml-4 text-lg whitespace-nowrap overflow-hidden" x-text="darkMode ? 'Light Mode' : 'Dark Mode'"></span>
        </button>

        <button wire:click="logout" class="w-full flex items-center rounded-xl transition-all duration-300 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                :class="sidebarOpen ? 'px-4 py-3' : 'justify-center py-3 w-14 mx-auto'">
            <svg class="w-7 h-7 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            <span x-show="sidebarOpen" x-transition.opacity.duration.300ms class="ml-4 text-lg font-medium whitespace-nowrap overflow-hidden">Logout</span>
        </button>
        
        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center transition-all duration-300" :class="sidebarOpen ? 'px-2' : 'justify-center'">
            
            @if(auth()->user()->avatar_url)
                <img src="{{ auth()->user()->avatar_url }}" alt="Profile" class="w-10 h-10 shrink-0 rounded-full object-cover">
            @else
                <div class="w-10 h-10 shrink-0 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-brand dark:text-blue-300 font-bold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            @endif

            <div x-show="sidebarOpen" x-transition.opacity.duration.300ms class="ml-3 overflow-hidden whitespace-nowrap flex-1">
                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ '@'.auth()->user()->username }}</p>
            </div>
        </div>

    </div>
</div>