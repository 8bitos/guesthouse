<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gallery - Bagus Guest House</title>
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
            <h1 class="text-4xl font-bold mb-2">Photo Gallery</h1>
            <p class="text-gray-300">View our beautiful property and guest experiences</p>
        </div>
    </section>

    <!-- Gallery Grid -->
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @php
                    $gallery_items = [
                        ['title' => 'Mountain View', 'category' => 'Landscape'],
                        ['title' => 'Valley Sunrise', 'category' => 'Landscape'],
                        ['title' => 'Luxury Suite', 'category' => 'Rooms'],
                        ['title' => 'Pool Area', 'category' => 'Facilities'],
                        ['title' => 'Family Suite', 'category' => 'Rooms'],
                        ['title' => 'Restaurant View', 'category' => 'Dining'],
                        ['title' => 'Sunset View', 'category' => 'Landscape'],
                        ['title' => 'Spa Area', 'category' => 'Facilities'],
                        ['title' => 'Guest Room', 'category' => 'Rooms'],
                        ['title' => 'Outdoor Seating', 'category' => 'Facilities'],
                        ['title' => 'Night View', 'category' => 'Landscape'],
                        ['title' => 'Cafe Corner', 'category' => 'Dining'],
                    ];
                @endphp
                
                @foreach ($gallery_items as $index => $item)
                <div class="aspect-square bg-gradient-to-br from-gray-300 to-gray-400 rounded-lg overflow-hidden hover:shadow-lg transition cursor-pointer group">
                    <div class="w-full h-full flex items-center justify-center relative">
                        <span class="text-gray-600 font-semibold text-center group-hover:opacity-0 transition">{{ $item['title'] }}</span>
                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            <div class="text-center text-white">
                                <p class="font-bold">{{ $item['title'] }}</p>
                                <p class="text-sm text-gray-300">{{ $item['category'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Photo Collection Info -->
    <section class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold mb-4">Share Your Memories</h2>
            <p class="text-gray-600 mb-6">Tag us on Instagram @bagusguesthouse to have your photos featured in our gallery!</p>
            <a href="https://instagram.com" target="_blank" class="inline-block bg-amber-700 hover:bg-amber-800 text-white px-8 py-3 rounded-lg font-semibold transition">
                Follow on Instagram
            </a>
        </div>
    </section>

    @include('components.footer')
</body>
</html>
