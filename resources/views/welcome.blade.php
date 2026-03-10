<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sosmed Masterpiece - Connect the World</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .gradient-text {
            background: linear-gradient(135deg, #2864f0, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .bg-grid-pattern {
            background-image: radial-gradient(circle at 1px 1px, #e2e8f0 1px, transparent 0);
            background-size: 40px 40px;
        }
        @media (prefers-color-scheme: dark) {
            .bg-grid-pattern {
                background-image: radial-gradient(circle at 1px 1px, #334155 1px, transparent 0);
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-white dark:bg-[#0a0a0a] text-gray-900 dark:text-gray-100 overflow-x-hidden selection:bg-blue-500 selection:text-white">

    <nav class="fixed w-full z-50 top-0 transition-all duration-300 backdrop-blur-md bg-white/70 dark:bg-[#0a0a0a]/70 border-b border-gray-200/50 dark:border-gray-800/50" data-aos="fade-down" data-aos-duration="1000">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-purple-600 rounded-xl flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                </div>
                <span class="text-2xl font-black tracking-tight">Sosmed</span>
            </div>
            
            <div class="flex gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 rounded-full font-bold bg-gray-900 text-white dark:bg-white dark:text-gray-900 hover:scale-105 transition-transform shadow-lg">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-6 py-2.5 rounded-full font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">Log in</a>
                    <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-full font-bold bg-blue-600 hover:bg-blue-700 text-white transition-all hover:shadow-[0_0_20px_rgba(37,99,235,0.4)] hover:-translate-y-0.5">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <section class="relative min-h-screen flex items-center justify-center pt-20 overflow-hidden">
        <div class="absolute inset-0 bg-grid-pattern opacity-50"></div>
        
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500/20 rounded-full blur-[100px] animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/20 rounded-full blur-[100px] animate-pulse" style="animation-delay: 2s;"></div>

        <div class="relative z-10 text-center px-4 max-w-5xl mx-auto">
            <h1 class="text-5xl md:text-7xl lg:text-8xl font-black tracking-tighter mb-8 leading-tight" data-aos="zoom-in-up" data-aos-duration="1000">
                Express Yourself, <br>
                <span class="gradient-text">Masterpiece Style.</span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400 mb-12 max-w-2xl mx-auto font-medium" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                Bergabunglah dengan platform sosial modern di mana setiap momen, cerita, dan kreasi Anda dihargai layaknya sebuah mahakarya.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-5 justify-center" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-8 py-4 rounded-full font-bold text-lg bg-blue-600 text-white hover:bg-blue-700 transition shadow-xl hover:shadow-blue-500/30 flex items-center justify-center gap-2">
                        Buka Dashboard <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="px-8 py-4 rounded-full font-bold text-lg bg-blue-600 text-white hover:bg-blue-700 transition shadow-xl hover:shadow-blue-500/30 flex items-center justify-center gap-2">
                        Mulai Sekarang <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                    <a href="{{ route('login') }}" class="px-8 py-4 rounded-full font-bold text-lg bg-white text-gray-900 border border-gray-200 hover:border-gray-300 dark:bg-[#121212] dark:text-white dark:border-gray-800 dark:hover:border-gray-700 transition flex items-center justify-center">
                        Sudah punya akun?
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <section class="py-24 bg-gray-50 dark:bg-[#121212] relative z-10">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-black mb-4">Fitur Tanpa Batas</h2>
                <p class="text-xl text-gray-500 dark:text-gray-400">Desain minimalis, performa maksimalis.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-[#1a1a1a] p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 hover:-translate-y-2 transition duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400 mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Explore Visual</h3>
                    <p class="text-gray-600 dark:text-gray-400">Jelajahi karya orang lain dalam tampilan Grid Masonry layaknya Instagram, lengkap dengan fitur Like & Komen Instan.</p>
                </div>
                <div class="bg-white dark:bg-[#1a1a1a] p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 hover:-translate-y-2 transition duration-300" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-14 h-14 bg-purple-100 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center text-purple-600 dark:text-purple-400 mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Performa Cloud</h3>
                    <p class="text-gray-600 dark:text-gray-400">Ditenagai oleh arsitektur Serverless Neon.tech dan Cloud Storage S3, memastikan akses super cepat tanpa delay.</p>
                </div>
                <div class="bg-white dark:bg-[#1a1a1a] p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 hover:-translate-y-2 transition duration-300" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-14 h-14 bg-pink-100 dark:bg-pink-900/30 rounded-2xl flex items-center justify-center text-pink-600 dark:text-pink-400 mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Dark Mode Native</h3>
                    <p class="text-gray-600 dark:text-gray-400">Mata lelah? Beralih ke Dark Mode hanya dengan satu klik. Pengaturan tema akan tersimpan otomatis di perangkat Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({ once: true, duration: 800 });
        });
    </script>
</body>
</html>