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
    <section class="relative w-full h-96 md:h-[600px] bg-gradient-to-r from-gray-900 to-gray-800 flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 opacity-30 bg-cover bg-center" style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 1200 600%22><rect fill=%22%23374151%22 width=%221200%22 height=%22600%22/></svg>')"></div>
        <div class="relative text-center text-white px-4 z-10">
            <h1 class="text-4xl md:text-6xl font-bold mb-4 animate-fade-in">Bagus Guest House</h1>
            <p class="text-lg md:text-2xl mb-8 text-gray-200">Luxury Accommodation & Dining Experience</p>
            <a href="#rooms" class="inline-block bg-amber-700 hover:bg-amber-800 text-white px-8 py-3 rounded-lg font-semibold transition">
                Book Now
            </a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 md:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4">{{ $aboutTitle }}</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    {{ $aboutDesc }}
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h3 class="text-2xl font-bold mb-4">Why Choose Us?</h3>
                    <ul class="space-y-4 text-gray-600">
                        @foreach ($aboutWhyList as $point)
                            <li class="flex gap-3">
                                <span class="text-amber-700 font-bold">✓</span>
                                <span>{{ $point }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="bg-gradient-to-br from-amber-600 to-amber-800 p-8 rounded-lg shadow-lg text-white">
                    <h3 class="text-2xl font-bold mb-4">Our Vision</h3>
                    <p class="leading-relaxed">{{ $aboutVision }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms Section -->
    <section id="rooms" class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-2">Our Rooms & Villas</h2>
                <p class="text-gray-600">Choose your perfect accommodation</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($rooms as $room)
                <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition flex flex-col justify-between">
                    <div>
                        <div class="h-48 bg-gray-100 border-b overflow-hidden flex items-center justify-center relative">
                            @if ($room->image)
                                <img src="{{ asset('storage/' . $room->image) }}" class="w-full h-full object-cover" alt="{{ $room->name }}">
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-gray-200 to-gray-300 flex flex-col items-center justify-center text-gray-400 p-4 text-center gap-1.5">
                                    <span class="material-symbols-outlined text-4xl text-gray-500">bed</span>
                                    <span class="text-sm font-bold uppercase tracking-wider text-gray-500">{{ $room->name }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-2">{{ $room->name }}</h3>
                            <p class="text-gray-600 text-sm mb-4">
                                {{ Str::limit($room->description ?? 'Spacious room with modern amenities and stunning Kintamani views.', 85) }}
                            </p>
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-amber-700 font-bold text-lg">From RP{{ number_format($room->price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex gap-4 text-sm text-gray-500 mb-4">
                                <span>{{ $room->capacity >= 4 ? 25 : 9 }} m²</span>
                                <span>•</span>
                                <span>{{ $room->capacity }} Guests</span>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 pb-6">
                        <a href="{{ route('booking', ['room_id' => $room->id]) }}" class="inline-block text-center w-full bg-amber-700 hover:bg-amber-800 text-white py-2 rounded-lg font-semibold transition">
                            Book Now
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="text-center mt-12">
                <a href="{{ route('rooms') }}" class="inline-block bg-amber-700 hover:bg-amber-800 text-white px-8 py-3 rounded-lg font-semibold transition">
                    Show All Rooms
                </a>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-2">Our Facilities</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($facilities as $facility)
                <div class="bg-white p-8 rounded-lg shadow-lg text-center hover:shadow-xl transition flex flex-col items-center justify-center">
                    <div class="mb-4 select-none">
                        @if(preg_match('/^[a-z0-9_]+$/i', $facility->icon))
                            <span class="material-symbols-outlined text-5xl text-amber-700 leading-none">{{ $facility->icon }}</span>
                        @else
                            <span class="text-5xl leading-none">{{ $facility->icon }}</span>
                        @endif
                    </div>
                    <h3 class="text-xl font-bold mb-2">{{ $facility->title }}</h3>
                    <p class="text-gray-600">{{ $facility->description }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-2">Photo Gallery</h2>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @forelse ($galleryPhotos as $photo)
                <a href="{{ route('gallery') }}" class="aspect-square bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center hover:shadow-lg transition relative group">
                    <img src="{{ asset('storage/' . $photo->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $photo->caption }}">
                    @if($photo->caption)
                        <div class="absolute inset-x-0 bottom-0 bg-black/60 p-2 text-center text-white text-[10px] truncate opacity-0 group-hover:opacity-100 transition duration-200">
                            {{ $photo->caption }}
                        </div>
                    @endif
                </a>
                @empty
                    @for ($i = 0; $i < 8; $i++)
                    <a href="{{ route('gallery') }}" class="aspect-square bg-gradient-to-br from-gray-300 to-gray-400 rounded-lg flex items-center justify-center hover:shadow-lg transition">
                        <span class="text-gray-600 font-semibold">Gallery {{ $i + 1 }}</span>
                    </a>
                    @endfor
                @endforelse
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 md:py-24 bg-amber-700">
        <div class="max-w-4xl mx-auto text-center px-4">
            <h2 class="text-4xl font-bold text-white mb-4">Ready for Your Stay?</h2>
            <p class="text-xl text-amber-100 mb-8">Book your perfect getaway at Bagus Guest House today</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('booking') }}" class="bg-white hover:bg-gray-100 text-amber-700 px-8 py-3 rounded-lg font-semibold transition">
                    Book Now
                </a>
                <a href="https://wa.me/6282169911168" target="_blank" class="bg-transparent border-2 border-white hover:bg-amber-800 text-white px-8 py-3 rounded-lg font-semibold transition">
                    Chat on WhatsApp
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('components.footer')
</body>
</html>
