<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Photo Gallery - Bagus Guest House</title>
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
<body class="bg-gray-100 text-gray-900 font-sans min-h-screen flex flex-col justify-between">
    <!-- Navbar -->
    @include('components.navbar')

    <!-- Header -->
    <section class="bg-gray-900 text-white py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold">Manage Photo Gallery</h1>
                <p class="text-gray-400 text-sm mt-1">Upload and manage the photo slideshow displayed in Kintamani gallery.</p>
            </div>
            <a href="{{ route('admin.cms.gallery.create') }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white px-5 py-2.5 rounded-lg font-semibold text-sm transition text-center shrink-0">
                + Upload Photo
            </a>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow w-full">
        <!-- Success Alert -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-semibold flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700 font-bold">&times;</button>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
            <div class="flex items-center justify-between pb-6 border-b mb-6 border-gray-100">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Gallery Photos</span>
                <span class="text-xs text-gray-500">Total: {{ $photos->total() }} photos</span>
            </div>

            @if ($photos->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($photos as $photo)
                        <div class="bg-gray-50 rounded-xl overflow-hidden border border-gray-200 flex flex-col justify-between group hover:shadow-md transition">
                            <div class="aspect-square bg-gray-100 overflow-hidden flex items-center justify-center relative">
                                <img src="{{ asset('storage/' . $photo->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $photo->caption }}">
                                <div class="absolute top-2 right-2">
                                    <form action="{{ route('admin.cms.gallery.destroy', $photo) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this photo?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white rounded-full p-2 shadow-lg hover:shadow-red-500/30 transition cursor-pointer" title="Delete Photo">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="p-3 bg-white border-t border-gray-100 flex flex-col justify-between flex-grow">
                                <p class="text-xs text-gray-700 font-medium truncate" title="{{ $photo->caption }}">{{ $photo->caption ?? 'No Caption' }}</p>
                                <span class="text-[10px] text-gray-400 mt-1 uppercase font-semibold">Order Index: {{ $photo->order_index }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8 pt-6 border-t border-gray-100">
                    {{ $photos->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <span class="material-symbols-outlined text-gray-400 text-5xl">image_search</span>
                    <h3 class="text-lg font-bold text-gray-700 mt-4">No photos uploaded</h3>
                    <p class="text-gray-500 text-sm mt-1 mb-6">Upload photos of your rooms, cafe, or Kintamani scenery.</p>
                    <a href="{{ route('admin.cms.gallery.create') }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white px-6 py-2.5 rounded-lg font-semibold transition">
                        + Upload First Photo
                    </a>
                </div>
            @endif
        </div>
    </main>

    <!-- Footer -->
    @include('components.footer')
</body>
</html>
