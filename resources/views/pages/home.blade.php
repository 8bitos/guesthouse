<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bagus Guest House - Luxury Accommodation</title>
    @fonts
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
                <h2 class="text-4xl font-bold mb-4">About Us</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Experience luxury hospitality with breathtaking mountain and valley views. Bagus Guest House offers modern facilities, comfortable accommodations, and world-class dining in a serene natural setting.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h3 class="text-2xl font-bold mb-4">Why Choose Us?</h3>
                    <ul class="space-y-4 text-gray-600">
                        <li class="flex gap-3">
                            <span class="text-amber-700 font-bold">✓</span>
                            <span>Spectacular mountain and valley views</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="text-amber-700 font-bold">✓</span>
                            <span>Modern luxury accommodations</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="text-amber-700 font-bold">✓</span>
                            <span>World-class dining and cafe</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="text-amber-700 font-bold">✓</span>
                            <span>Professional and friendly staff</span>
                        </li>
                    </ul>
                </div>
                <div class="bg-gradient-to-br from-amber-600 to-amber-800 p-8 rounded-lg shadow-lg text-white">
                    <h3 class="text-2xl font-bold mb-4">Our Vision</h3>
                    <p class="mb-4">To be the most preferred luxury accommodation destination in the region, offering unforgettable experiences and exceptional hospitality.</p>
                    <p>Every stay at Bagus Guest House is crafted to create lasting memories with attention to detail and personalized service.</p>
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
                @php
                    $rooms = [
                        ['name' => 'Family Suite 1', 'size' => '25', 'guests' => 4, 'price' => 910000],
                        ['name' => 'Family Suite 2', 'size' => '25', 'guests' => 4, 'price' => 1040000],
                        ['name' => 'Suite 3', 'size' => '25', 'guests' => 2, 'price' => 1200000],
                        ['name' => 'Suite 4', 'size' => '25', 'guests' => 2, 'price' => 910000],
                        ['name' => 'Suite 5', 'size' => '25', 'guests' => 2, 'price' => 1040000],
                        ['name' => 'Potato Room', 'size' => '9', 'guests' => 2, 'price' => 650000],
                    ];
                @endphp
                
                @foreach ($rooms as $room)
                <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition">
                    <div class="h-48 bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center">
                        <span class="text-gray-500 text-lg">🏠 {{ $room['name'] }}</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">{{ $room['name'] }}</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Spacious room with modern amenities and stunning views
                        </p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-amber-700 font-bold text-lg">From RP{{ number_format($room['price'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex gap-4 text-sm text-gray-500 mb-4">
                            <span>{{ $room['size'] }} m²</span>
                            <span>•</span>
                            <span>{{ $room['guests'] }}-{{ $room['guests'] }} Guests</span>
                        </div>
                        <button class="w-full bg-amber-700 hover:bg-amber-800 text-white py-2 rounded-lg font-semibold transition">
                            View Details
                        </button>
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
                @php
                    $facilities = [
                        ['icon' => '🏔️', 'title' => 'Mountain View', 'desc' => 'Breathtaking views of surrounding mountains and Batur'],
                        ['icon' => '🏞️', 'title' => 'Valley View', 'desc' => 'Scenic valley panoramas and peaceful settings'],
                        ['icon' => '🏊', 'title' => 'Swimming Pool', 'desc' => 'Mountain-side infinity pool with panoramic views'],
                        ['icon' => '🚴', 'title' => 'Activities', 'desc' => 'Hiking, trekking, jeep tours and adventures'],
                        ['icon' => '🍽️', 'title' => 'Fine Dining', 'desc' => 'Restaurant and cafe with local cuisine'],
                        ['icon' => '💆', 'title' => 'Spa Services', 'desc' => 'Wellness and relaxation treatments'],
                    ];
                @endphp
                
                @foreach ($facilities as $facility)
                <div class="bg-white p-8 rounded-lg shadow-lg text-center hover:shadow-xl transition">
                    <div class="text-5xl mb-4">{{ $facility['icon'] }}</div>
                    <h3 class="text-xl font-bold mb-2">{{ $facility['title'] }}</h3>
                    <p class="text-gray-600">{{ $facility['desc'] }}</p>
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
                @for ($i = 0; $i < 8; $i++)
                <a href="{{ route('gallery') }}" class="aspect-square bg-gradient-to-br from-gray-300 to-gray-400 rounded-lg flex items-center justify-center hover:shadow-lg transition">
                    <span class="text-gray-600 font-semibold">Gallery {{ $i + 1 }}</span>
                </a>
                @endfor
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
