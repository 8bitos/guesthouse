<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bagus Guest House - Luxury Accommodation</title>
    @fonts
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            @import 'tailwindcss';
        </style>
    @endif
</head>
<body class="bg-white text-gray-900 font-sans">
    <!-- Navbar -->
    @include('components.navbar')

    <!-- Hero Section -->
    <section class="relative w-full h-[450px] md:h-[650px] bg-gradient-to-r from-gray-950 to-gray-850 flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 opacity-40 bg-cover bg-center transition-transform duration-10000 scale-105 hover:scale-100" style="background-image: url('{{ $heroImage ? asset('storage/' . $heroImage) : asset('images/default_gallery/villa.png') }}')"></div>
        <div class="absolute inset-0 bg-black/45"></div>
        <div class="relative text-center text-white px-4 z-10 max-w-3xl">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-4 tracking-tight animate-fade-in-up leading-tight">Bagus Guest House</h1>
            <p class="text-lg md:text-2xl mb-8 text-gray-200 font-medium animate-fade-in-up-delay">Luxury Accommodation & Dining Experience</p>
            <a href="{{ route('booking') }}" class="inline-block bg-amber-700 hover:bg-amber-800 text-white px-8 py-3.5 rounded-lg font-bold shadow-lg hover:shadow-amber-800/30 hover:scale-[1.03] active:scale-[0.97] transition-all duration-300 animate-fade-in-up-delay-2">
                Book Now
            </a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 md:py-24 bg-gray-50/50 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">{{ $aboutTitle }}</h2>
                <p class="text-base md:text-lg text-gray-650 max-w-3xl mx-auto leading-relaxed">
                    {{ $aboutDesc }}
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200/80 hover:shadow-md transition duration-300 reveal">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-700 font-bold">verified</span>
                        Why Choose Us?
                    </h3>
                    <ul class="space-y-4 text-gray-650 text-sm">
                        @foreach ($aboutWhyList as $point)
                            <li class="flex gap-3 items-start">
                                <span class="text-amber-700 font-extrabold shrink-0">✓</span>
                                <span>{{ $point }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="bg-gradient-to-br from-amber-700 to-amber-900 p-8 rounded-2xl shadow-lg text-white flex flex-col justify-center reveal delay-150">
                    <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined">visibility</span>
                        Our Vision
                    </h3>
                    <p class="leading-relaxed text-amber-50 text-sm md:text-base font-medium">{{ $aboutVision }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms Section -->
    <section id="rooms" class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-2 tracking-tight">Our Rooms & Villas</h2>
                <p class="text-sm font-semibold text-amber-700 uppercase tracking-wider">Choose your perfect Kuta accommodation</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($rooms as $index => $room)
                @php
                    $delayClass = '';
                    if ($index % 3 === 1) $delayClass = 'delay-75';
                    elseif ($index % 3 === 2) $delayClass = 'delay-150';
                @endphp
                <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-350 ease-out border border-gray-200/60 flex flex-col justify-between reveal {{ $delayClass }}">
                    <div>
                        <div class="h-48 bg-gray-100 overflow-hidden relative">
                            @if ($room->image)
                                <img src="{{ asset('storage/' . $room->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" alt="{{ $room->name }}">
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-gray-100 to-gray-200 flex flex-col items-center justify-center text-gray-400 p-4 text-center gap-1.5">
                                    <span class="material-symbols-outlined text-4xl text-gray-500 group-hover:scale-110 transition-transform duration-300">bed</span>
                                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500">{{ $room->name }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-extrabold text-gray-900 mb-2 group-hover:text-amber-700 transition duration-200">{{ $room->name }}</h3>
                            <p class="text-gray-550 text-xs leading-relaxed mb-4 min-h-[48px]">
                                {{ Str::limit($room->description ?? 'Spacious room with modern amenities and stunning Kuta views.', 100) }}
                            </p>
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-amber-700 font-extrabold text-base">From RP{{ number_format($room->price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex gap-4 text-[10px] text-gray-400 font-bold uppercase tracking-wider">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px] leading-none">straighten</span>
                                    @if(isset($room->size))
                                        {{ $room->size }}{{ str_contains($room->size, 'x') ? ' m' : ' m²' }}
                                    @else
                                        {{ $room->capacity >= 4 ? 25 : 9 }} m²
                                    @endif
                                </span>
                                <span>•</span>
                                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[13px] leading-none">group</span>{{ $room->capacity }} Guests</span>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 pb-6">
                        <a href="{{ route('booking', ['room_id' => $room->id]) }}" class="inline-block text-center w-full bg-amber-700 hover:bg-amber-800 text-white py-2.5 rounded-lg font-semibold hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 shadow-sm">
                            Book Now
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="text-center mt-14 reveal">
                <a href="{{ route('rooms') }}" class="inline-block bg-white hover:bg-gray-50 text-gray-800 border border-gray-200 px-8 py-3 rounded-lg font-bold shadow-sm transition hover:scale-[1.02]">
                    Show All Rooms
                </a>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section class="py-16 md:py-24 bg-gray-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-2 tracking-tight">Our Facilities</h2>
                <p class="text-sm font-semibold text-amber-700 uppercase tracking-wider">Everything you need for a comfortable stay</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($facilities as $index => $facility)
                @php
                    $delayClass = '';
                    if ($index % 3 === 1) $delayClass = 'delay-75';
                    elseif ($index % 3 === 2) $delayClass = 'delay-150';

                    // Map old emoji icons to Google Material Symbols
                    $iconMap = [
                        '🏔️' => 'filter_hdr',
                        '⛰️' => 'filter_hdr',
                        '🏞️' => 'landscape',
                        '🏊' => 'pool',
                        '🚴' => 'directions_bike',
                        '🍽️' => 'restaurant',
                        '💆' => 'spa',
                    ];
                    $displayIcon = $facility->icon;
                    if (isset($iconMap[$displayIcon])) {
                        $displayIcon = $iconMap[$displayIcon];
                    }
                @endphp
                <div class="group bg-white p-8 rounded-2xl shadow-sm border border-gray-200/50 text-center hover:shadow-xl hover:-translate-y-1 transition-all duration-350 ease-out flex flex-col items-center justify-center reveal {{ $delayClass }}">
                    <div class="mb-4 select-none group-hover:scale-110 transition-transform duration-300">
                        @if(preg_match('/^[a-z0-9_]+$/i', $displayIcon))
                            <span class="material-symbols-outlined text-5xl text-amber-700 leading-none">{{ $displayIcon }}</span>
                        @else
                            <span class="text-5xl leading-none">{{ $displayIcon }}</span>
                        @endif
                    </div>
                    <h3 class="text-lg font-extrabold text-gray-900 mb-2">{{ $facility->title }}</h3>
                    <p class="text-gray-550 text-xs leading-relaxed">{{ $facility->description }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-2 tracking-tight">Photo Gallery</h2>
                <p class="text-sm font-semibold text-amber-700 uppercase tracking-wider">Moments from Bagus Guest House</p>
            </div>
            
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse ($galleryPhotos as $index => $photo)
                @php
                    $delayClass = 'delay-' . ($index * 75);
                @endphp
                <a href="{{ route('gallery') }}" class="aspect-square bg-gray-100 rounded-2xl overflow-hidden flex items-center justify-center shadow-sm hover:shadow-lg transition relative group border border-gray-200/50 reveal {{ $delayClass }}">
                    <img src="{{ asset('storage/' . $photo->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" alt="{{ $photo->caption }}">
                    @if($photo->caption)
                        <div class="absolute inset-x-0 bottom-0 bg-black/60 p-2.5 text-center text-white text-[10px] truncate opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            {{ $photo->caption }}
                        </div>
                    @endif
                </a>
                @empty
                    <!-- Fallback default gallery photos (generated resort views) -->
                    <a href="{{ route('gallery') }}" class="aspect-square bg-gray-100 rounded-2xl overflow-hidden flex items-center justify-center shadow-sm hover:shadow-lg transition relative group border border-gray-200/50 reveal">
                        <img src="{{ asset('images/default_gallery/bedroom.png') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" alt="Resort Bedroom">
                        <div class="absolute inset-x-0 bottom-0 bg-black/60 p-2.5 text-center text-white text-[10px] opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            Luxury Bedroom
                        </div>
                    </a>
                    <a href="{{ route('gallery') }}" class="aspect-square bg-gray-100 rounded-2xl overflow-hidden flex items-center justify-center shadow-sm hover:shadow-lg transition relative group border border-gray-200/50 reveal delay-75">
                        <img src="{{ asset('images/default_gallery/pool.png') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" alt="Infinity Pool">
                        <div class="absolute inset-x-0 bottom-0 bg-black/60 p-2.5 text-center text-white text-[10px] opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            Infinity Pool View
                        </div>
                    </a>
                    <a href="{{ route('gallery') }}" class="aspect-square bg-gray-100 rounded-2xl overflow-hidden flex items-center justify-center shadow-sm hover:shadow-lg transition relative group border border-gray-200/50 reveal delay-150">
                        <img src="{{ asset('images/default_gallery/restaurant.png') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" alt="Resort Restaurant">
                        <div class="absolute inset-x-0 bottom-0 bg-black/60 p-2.5 text-center text-white text-[10px] opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            Bamboo Restaurant
                        </div>
                    </a>
                    <a href="{{ route('gallery') }}" class="aspect-square bg-gray-100 rounded-2xl overflow-hidden flex items-center justify-center shadow-sm hover:shadow-lg transition relative group border border-gray-200/50 reveal delay-225">
                        <img src="{{ asset('images/default_gallery/villa.png') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" alt="Resort Villa Exterior">
                        <div class="absolute inset-x-0 bottom-0 bg-black/60 p-2.5 text-center text-white text-[10px] opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            Resort Villa
                        </div>
                    </a>
                @endforelse
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 md:py-24 bg-amber-700 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-cover bg-center" style="background-image: url('{{ asset('images/default_gallery/pool.png') }}')"></div>
        <div class="relative max-w-4xl mx-auto text-center px-4 z-10 reveal">
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4 tracking-tight">Ready for Your Stay?</h2>
            <p class="text-lg text-amber-100 mb-8 max-w-lg mx-auto">Book your perfect getaway at Bagus Guest House today</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('booking') }}" class="bg-white hover:bg-gray-50 text-amber-700 px-8 py-3.5 rounded-lg font-bold shadow-md hover:scale-[1.02] transition">
                    Book Now
                </a>
                <a href="https://wa.me/6281916166616" target="_blank" class="bg-transparent border-2 border-white hover:bg-amber-800 text-white px-8 py-3 rounded-lg font-bold transition">
                    Chat on WhatsApp
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('components.footer')

</body>
</html>
