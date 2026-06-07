<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Our Rooms - Bagus Guest House</title>
    @fonts
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-white text-gray-900 font-sans">
    @include('components.navbar')

    <!-- Page Header -->
    <section class="relative bg-gradient-to-r from-gray-950 to-gray-850 py-24 text-white overflow-hidden flex items-center justify-center">
        <div class="absolute inset-0 opacity-25 bg-cover bg-center transition-transform duration-10000 scale-105" style="background-image: url('{{ asset('images/default_gallery/bedroom.png') }}')"></div>
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4 tracking-tight animate-fade-in-up">Our Luxury Rooms & Villas</h1>
            <p class="text-lg md:text-xl text-gray-300 font-medium max-w-2xl mx-auto animate-fade-in-up-delay">Find your sanctuary in our range of meticulously designed accommodations overlooking Kintamani.</p>
        </div>
    </section>

    <!-- Rooms Grid -->
    <section class="py-16 md:py-24 bg-gray-50/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($rooms as $index => $room)
                @php
                    $delayClass = '';
                    if ($index % 3 === 1) $delayClass = 'delay-75';
                    elseif ($index % 3 === 2) $delayClass = 'delay-150';
                @endphp
                <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-350 ease-out border border-gray-200/60 flex flex-col justify-between reveal {{ $delayClass }}">
                    <div>
                        <div class="h-56 bg-gray-100 overflow-hidden relative">
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
                            <h3 class="text-lg font-extrabold text-gray-900 mb-2 group-hover:text-amber-700 transition duration-205">{{ $room->name }}</h3>
                            <p class="text-gray-550 text-xs leading-relaxed mb-6 min-h-[48px]">
                                {{ $room->description ?? 'Spacious room with modern amenities and stunning views of the surrounding hills.' }}
                            </p>
                            
                            <div class="grid grid-cols-3 gap-2.5 mb-5">
                                <div class="bg-gray-50 rounded-xl p-2 text-center border border-gray-150/50">
                                    <span class="material-symbols-outlined text-amber-700 text-lg block mb-0.5 select-none">straighten</span>
                                    <span class="text-[9px] text-gray-400 font-bold uppercase block tracking-wider">Size</span>
                                    <span class="text-xs font-bold text-gray-800 block mt-0.5">{{ $room->capacity >= 4 ? 25 : 9 }} m²</span>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-2 text-center border border-gray-150/50">
                                    <span class="material-symbols-outlined text-amber-700 text-lg block mb-0.5 select-none">group</span>
                                    <span class="text-[9px] text-gray-400 font-bold uppercase block tracking-wider">Capacity</span>
                                    <span class="text-xs font-bold text-gray-800 block mt-0.5">{{ $room->capacity }} Guests</span>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-2 text-center border border-gray-150/50">
                                    <span class="material-symbols-outlined text-amber-700 text-lg block mb-0.5 select-none">bed</span>
                                    <span class="text-[9px] text-gray-400 font-bold uppercase block tracking-wider">Beds</span>
                                    <span class="text-[10px] font-bold text-gray-800 block mt-1 truncate" title="{{ $room->capacity >= 4 ? '3 Beds' : ($room->capacity == 2 ? '1 King' : '1 Single') }}">
                                        {{ $room->capacity >= 4 ? '3 Beds' : ($room->capacity == 2 ? '1 King' : '1 Single') }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex items-baseline justify-between border-t border-gray-100 pt-4">
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Price per night</span>
                                <span class="text-amber-700 font-extrabold text-base">RP{{ number_format($room->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-6 pb-6">
                        <a href="{{ route('booking', ['room_id' => $room->id]) }}" class="inline-block text-center w-full bg-amber-700 hover:bg-amber-800 text-white py-3 rounded-xl font-bold shadow-sm hover:shadow-amber-800/10 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                            Book This Room
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 md:py-24 bg-amber-700 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-cover bg-center" style="background-image: url('{{ asset('images/default_gallery/pool.png') }}')"></div>
        <div class="relative max-w-4xl mx-auto text-center px-4 z-10 reveal">
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4 tracking-tight">Need Help Choosing?</h2>
            <p class="text-lg text-amber-100 mb-8 max-w-lg mx-auto">Get in touch with us to find the perfect room configuration for your stay.</p>
            <a href="https://wa.me/6281916166616" target="_blank" class="inline-block bg-white hover:bg-gray-50 text-amber-700 px-8 py-3.5 rounded-lg font-bold shadow-md hover:scale-[1.02] transition">
                Contact Us on WhatsApp
            </a>
        </div>
    </section>

    @include('components.footer')
</body>
</html>
