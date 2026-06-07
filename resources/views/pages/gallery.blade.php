<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gallery - Bagus Guest House</title>
    @fonts
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            @import 'tailwindcss';
        </style>
    @endif
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .animate-fade-in-up-delay {
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.15s forwards;
            opacity: 0;
        }
    </style>
</head>
<body class="bg-white text-gray-900 font-sans min-h-screen flex flex-col justify-between">
    @include('components.navbar')

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-gray-950 to-gray-850 py-16 text-white text-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-cover bg-center" style="background-image: url('{{ asset('images/default_gallery/pool.png') }}')"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight animate-fade-in-up">Photo Gallery</h1>
            <p class="text-sm md:text-base text-gray-300 mt-2 font-medium animate-fade-in-up-delay">View our beautiful property and guest experiences</p>
        </div>
    </section>

    <!-- Gallery Grid -->
    <section class="py-16 md:py-20 flex-grow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($photos as $photo)
                <div class="aspect-square bg-gray-100 rounded-2xl overflow-hidden hover:shadow-xl transition-shadow duration-300 relative group border border-gray-200/50">
                    <img src="{{ asset('storage/' . $photo->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" alt="{{ $photo->caption }}">
                    @if($photo->caption)
                        <div class="absolute inset-0 bg-black/65 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center p-4">
                            <div class="text-center text-white">
                                <p class="font-bold text-xs md:text-sm leading-snug">{{ $photo->caption }}</p>
                            </div>
                        </div>
                    @endif
                </div>
                @empty
                    <!-- Fallback default gallery photos (generated resort views) -->
                    <div class="aspect-square bg-gray-100 rounded-2xl overflow-hidden hover:shadow-xl transition-shadow duration-300 relative group border border-gray-200/50">
                        <img src="{{ asset('images/default_gallery/bedroom.png') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" alt="Resort Bedroom">
                        <div class="absolute inset-0 bg-black/65 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center p-4">
                            <div class="text-center text-white">
                                <p class="font-bold text-xs md:text-sm leading-snug">Luxury Bedroom</p>
                            </div>
                        </div>
                    </div>
                    <div class="aspect-square bg-gray-100 rounded-2xl overflow-hidden hover:shadow-xl transition-shadow duration-300 relative group border border-gray-200/50">
                        <img src="{{ asset('images/default_gallery/pool.png') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" alt="Infinity Pool">
                        <div class="absolute inset-0 bg-black/65 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center p-4">
                            <div class="text-center text-white">
                                <p class="font-bold text-xs md:text-sm leading-snug">Infinity Pool View</p>
                            </div>
                        </div>
                    </div>
                    <div class="aspect-square bg-gray-100 rounded-2xl overflow-hidden hover:shadow-xl transition-shadow duration-300 relative group border border-gray-200/50">
                        <img src="{{ asset('images/default_gallery/restaurant.png') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" alt="Resort Restaurant">
                        <div class="absolute inset-0 bg-black/65 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center p-4">
                            <div class="text-center text-white">
                                <p class="font-bold text-xs md:text-sm leading-snug">Bamboo Restaurant</p>
                            </div>
                        </div>
                    </div>
                    <div class="aspect-square bg-gray-100 rounded-2xl overflow-hidden hover:shadow-xl transition-shadow duration-300 relative group border border-gray-200/50">
                        <img src="{{ asset('images/default_gallery/villa.png') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" alt="Resort Villa Exterior">
                        <div class="absolute inset-0 bg-black/65 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center p-4">
                            <div class="text-center text-white">
                                <p class="font-bold text-xs md:text-sm leading-snug">Resort Villa</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Photo Collection Info -->
    <section class="py-12 bg-gray-50/50 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Share Your Memories</h2>
            <p class="text-gray-650 text-sm mb-6 max-w-md mx-auto">Tag us on Instagram @bagusguesthouse to have your photos featured in our gallery!</p>
            <a href="https://instagram.com" target="_blank" class="inline-block bg-amber-700 hover:bg-amber-800 text-white px-8 py-3 rounded-lg font-bold hover:scale-[1.02] active:scale-[0.98] transition">
                Follow on Instagram
            </a>
        </div>
    </section>

    @include('components.footer')
</body>
</html>
