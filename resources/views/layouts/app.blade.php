<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: true }" 
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" 
      x-bind:class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sosmed Masterpiece') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
        
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

        <style>
            [x-cloak] { display: none !important; }
            /* Tambahan agar scrollbar lebih elegan */
            ::-webkit-scrollbar { width: 6px; }
            ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
            .dark ::-webkit-scrollbar-thumb { background: #334155; }
        </style>
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    
    <body class="font-sans antialiased bg-white text-gray-900 transition-colors duration-300 dark:bg-[#121212] dark:text-gray-100">
        
        <div class="min-h-screen w-full flex">
            
            <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="hidden md:flex flex-col sticky top-0 h-screen py-6 border-r border-gray-100 dark:border-gray-800 transition-all duration-300 ease-in-out bg-white dark:bg-[#121212] z-20">
                <livewire:layout.navigation />
            </aside>

            <main class="flex-1 flex justify-center bg-gray-50/50 dark:bg-[#121212]/50">
                <div class="w-full max-w-2xl px-4 sm:px-6 py-8">
                    {{ $slot }}
                </div>
            </main>

            <aside class="w-80 hidden lg:block sticky top-0 h-screen py-6 px-6 border-l border-gray-100 dark:border-gray-800 bg-white dark:bg-[#121212]">
                <livewire:suggested-users />
            </aside>

        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                AOS.init({ once: true, offset: 50 });
            });
        </script>
    </body>
</html>