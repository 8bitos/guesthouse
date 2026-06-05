<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Our Rooms - Bagus Guest House</title>
    @fonts
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-white text-gray-900 font-sans">
    @include('components.navbar')

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-gray-900 to-gray-800 py-16 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold mb-2">Our Rooms & Villas</h1>
            <p class="text-gray-300">Explore our collection of luxury accommodations</p>
        </div>
    </section>

    <!-- Rooms Grid -->
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    $all_rooms = [
                        ['name' => 'Family Suite 1', 'size' => 25, 'guests' => 4, 'price' => 910000, 'beds' => '1 King + 2 Others', 'desc' => 'Perfect for families with stunning mountain views and modern amenities.'],
                        ['name' => 'Family Suite 2', 'size' => 25, 'guests' => 4, 'price' => 1040000, 'beds' => '1 King + 2 Others', 'desc' => 'Spacious family accommodation with separate living area.'],
                        ['name' => 'Suite 3', 'size' => 25, 'guests' => 2, 'price' => 1200000, 'beds' => '1 King', 'desc' => 'Luxurious suite with premium furnishings and valley views.'],
                        ['name' => 'Suite 4', 'size' => 25, 'guests' => 2, 'price' => 910000, 'beds' => '1 King', 'desc' => 'Comfortable suite ideal for couples and honeymooners.'],
                        ['name' => 'Suite 5', 'size' => 25, 'guests' => 2, 'price' => 1040000, 'beds' => '1 King', 'desc' => 'Modern suite with elegant design and mountain vistas.'],
                        ['name' => 'Potato Room 1', 'size' => 9, 'guests' => 2, 'price' => 650000, 'beds' => '1 Queen', 'desc' => 'Cozy shared bathroom room with valley views.'],
                        ['name' => 'Potato Room 2', 'size' => 9, 'guests' => 2, 'price' => 650000, 'beds' => '1 Queen', 'desc' => 'Budget-friendly accommodation with shared facilities.'],
                        ['name' => 'Potato Room 3', 'size' => 9, 'guests' => 2, 'price' => 650000, 'beds' => '1 Queen', 'desc' => 'Intimate room perfect for budget travelers.'],
                    ];
                @endphp
                
                @foreach ($all_rooms as $room)
                <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition">
                    <div class="h-48 bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center">
                        <span class="text-gray-500 text-lg">🏠 {{ $room['name'] }}</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">{{ $room['name'] }}</h3>
                        <p class="text-gray-600 text-sm mb-4">{{ $room['desc'] }}</p>
                        
                        <div class="mb-4 space-y-2 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Size:</span>
                                <span class="font-semibold">{{ $room['size'] }} m²</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Capacity:</span>
                                <span class="font-semibold">{{ $room['guests'] }} Adults</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Beds:</span>
                                <span class="font-semibold">{{ $room['beds'] }}</span>
                            </div>
                        </div>
                        
                        <div class="border-t pt-4 mb-4">
                            <div class="text-amber-700 font-bold text-lg">From RP{{ number_format($room['price'], 0, ',', '.') }} / night</div>
                        </div>
                        
                        <button onclick="alert('Booking feature coming soon!')" class="w-full bg-amber-700 hover:bg-amber-800 text-white py-2 rounded-lg font-semibold transition">
                            Book This Room
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 bg-amber-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Need Help Choosing?</h2>
            <a href="https://wa.me/6282169911168" target="_blank" class="inline-block bg-white hover:bg-gray-100 text-amber-700 px-8 py-3 rounded-lg font-semibold transition">
                Contact Us on WhatsApp
            </a>
        </div>
    </section>

    @include('components.footer')
</body>
</html>
