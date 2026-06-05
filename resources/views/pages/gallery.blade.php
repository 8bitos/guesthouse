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
                @forelse ($photos as $photo)
                <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition relative group">
                    <img src="{{ asset('storage/' . $photo->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $photo->caption }}">
                    @if($photo->caption)
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center p-4">
                            <div class="text-center text-white">
                                <p class="font-bold text-sm leading-snug">{{ $photo->caption }}</p>
                            </div>
                        </div>
                    @endif
                </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-500">
                        <span class="text-4xl block mb-2">📷</span>
                        No gallery photos uploaded yet.
                    </div>
                @endforelse
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
