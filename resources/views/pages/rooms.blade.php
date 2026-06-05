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
                                {{ $room->description ?? 'Spacious room with modern amenities and stunning views.' }}
                            </p>
                            
                            <div class="mb-4 space-y-2 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span>Size:</span>
                                    <span class="font-semibold">{{ $room->capacity >= 4 ? 25 : 9 }} m²</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Capacity:</span>
                                    <span class="font-semibold">{{ $room->capacity }} Adults</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Beds:</span>
                                    <span class="font-semibold">{{ $room->capacity >= 4 ? '1 King + 2 Others' : ($room->capacity == 2 ? '1 King' : '1 Single') }}</span>
                                </div>
                            </div>
                            
                            <div class="border-t pt-4 mb-4">
                                <div class="text-amber-700 font-bold text-lg">From RP{{ number_format($room->price, 0, ',', '.') }} / night</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-6 pb-6">
                        <a href="{{ route('booking', ['room_id' => $room->id]) }}" class="inline-block text-center w-full bg-amber-700 hover:bg-amber-800 text-white py-2 rounded-lg font-semibold transition">
                            Book This Room
                        </a>
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
