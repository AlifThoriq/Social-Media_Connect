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
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function updated($property)
    {
        // Validasi akan langsung berjalan setiap kali user selesai mengetik di kolom tertentu
        $this->validateOnly($property, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
    }

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            // Validasi unik untuk username ada di sini:
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $event = event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="min-h-screen flex items-center justify-center bg-[#2864f0]">
    
    <div x-data="{ showForm: false }" class="w-full max-w-sm p-6 text-white">
        
        <div class="flex justify-center mb-6">
            <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
            </svg>
        </div>

        <h2 class="text-3xl font-bold text-center mb-10 tracking-wide">Sign up</h2>

        <div class="relative min-h-[420px]">
            
            <div x-show="!showForm" 
                 x-transition:enter="transition ease-out duration-300 delay-100"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="absolute inset-x-0 top-0 space-y-4">
                 
                <button type="button" class="w-full flex items-center justify-center gap-3 py-3 px-4 bg-white text-gray-800 rounded-full hover:bg-gray-100 transition duration-300 font-semibold shadow-md">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-5 h-5">
                    Continue with Google
                </button>

                <button type="button" class="w-full flex items-center justify-center gap-3 py-3 px-4 bg-black text-white rounded-full hover:bg-gray-800 transition duration-300 font-semibold shadow-md border border-gray-700">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.04 2.26-.79 3.59-.76 1.63.06 2.8.76 3.63 1.9-3.24 1.95-2.68 6.01.44 7.22-.72 1.76-1.6 3.4-2.74 3.81zm-3.66-14.8c.17-1.48-1.02-2.82-2.47-3.03-.23 1.54 1.25 2.89 2.47 3.03z"/></svg>
                    Continue with Apple
                </button>

                <button @click="showForm = true" type="button" class="w-full flex items-center justify-center gap-3 py-3 px-4 bg-transparent border border-white text-white rounded-full hover:bg-white/10 transition duration-300 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Use phone/email/username
                </button>
                <p class="text-sm text-white/90 text-center" >
                        Already have an account? 
                        <a href="{{ route('login') }}" class="font-bold text-white hover:underline transition">Log in</a>
                    </p>
            </div>
            

            

            <div x-show="showForm" style="display: none;"
                 x-transition:enter="transition ease-out duration-300 delay-100"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="absolute inset-x-0 top-0">
                 
                <form wire:submit="register" class="space-y-4">
                    
                    <div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input wire:model="name" id="name" type="text" required autofocus placeholder="FULL NAME" class="block w-full pl-10 pr-3 py-2.5 border border-white bg-transparent text-white placeholder-white/80 focus:outline-none focus:ring-1 focus:ring-white focus:border-white sm:text-sm transition duration-150 rounded-sm uppercase tracking-wide">
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-1 text-red-300 text-xs" />
                    </div>

                    <div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                            </div>
                            <input wire:model.live.debounce.500ms="username" id="username" type="text" required placeholder="USERNAME" class="block w-full pl-10 pr-3 py-2.5 border border-white bg-transparent text-white placeholder-white/80 focus:outline-none focus:ring-1 focus:ring-white focus:border-white sm:text-sm transition duration-150 rounded-sm uppercase tracking-wide">
                        </div>
                        <x-input-error :messages="$errors->get('username')" class="mt-1 text-red-300 text-xs font-bold" />
                    </div>

                    <div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <input wire:model="email" id="email" type="email" required placeholder="EMAIL" class="block w-full pl-10 pr-3 py-2.5 border border-white bg-transparent text-white placeholder-white/80 focus:outline-none focus:ring-1 focus:ring-white focus:border-white sm:text-sm transition duration-150 rounded-sm uppercase tracking-wide">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-300 text-xs" />
                    </div>

                    <div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input wire:model="password" id="password" type="password" required placeholder="PASSWORD" class="block w-full pl-10 pr-3 py-2.5 border border-white bg-transparent text-white placeholder-white/80 focus:outline-none focus:ring-1 focus:ring-white focus:border-white sm:text-sm transition duration-150 rounded-sm uppercase tracking-wide">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-300 text-xs" />
                    </div>

                    <div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <input wire:model="password_confirmation" id="password_confirmation" type="password" required placeholder="CONFIRM PASSWORD" class="block w-full pl-10 pr-3 py-2.5 border border-white bg-transparent text-white placeholder-white/80 focus:outline-none focus:ring-1 focus:ring-white focus:border-white sm:text-sm transition duration-150 rounded-sm uppercase tracking-wide">
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-red-300 text-xs" />
                    </div>
                    

                    <div class="pt-4">
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                wire:target="register"
                                class="w-full flex justify-center items-center py-3 px-4 border border-transparent text-sm font-bold text-blue-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition duration-150 rounded-sm uppercase tracking-wider shadow-md disabled:opacity-75 disabled:cursor-wait">
                            
                            <span wire:loading.remove wire:target="register">SIGN UP</span>
                            
                            <div wire:loading wire:target="register" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>MENDAFTAR...</span>
                            </div>
                            
                        </button>
                    </div>
                    <p class="text-sm text-white/90">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="font-bold text-white hover:underline transition">Log in</a>
                    </p>
                    <div class="flex justify-start mt-4">
                        <button @click="showForm = false" type="button" class="text-xs text-white/80 hover:text-white hover:underline transition">
                            &larr; Back to options
                        </button>
                    </div>
                </form>
            </div>
        </div>

        
        
    </div>
</div>