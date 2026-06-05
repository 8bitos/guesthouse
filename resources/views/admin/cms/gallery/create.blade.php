<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Upload Photo - Bagus Guest House</title>
    @fonts
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            @import 'tailwindcss';
        </style>
    @endif
</head>
<body class="bg-[#F8FAFC] text-gray-900 font-sans min-h-screen flex flex-col justify-between">
    <!-- Navbar -->
    @include('components.navbar')

    <!-- Header -->
    <section class="bg-gray-900 text-white py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">Upload Photo</h1>
                <p class="text-gray-400 text-sm mt-1">Select an image to add to your guesthouse gallery.</p>
            </div>
            <a href="{{ route('admin.cms.gallery.index') }}" class="text-sm bg-white/10 hover:bg-white/15 text-white border border-white/20 px-4 py-2 rounded-lg font-semibold transition">
                ← Back to Gallery
            </a>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow w-full">
        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <form method="POST" action="{{ route('admin.cms.gallery.store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- Image Upload -->
                <div class="space-y-1">
                    <label for="image" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Select Image (Max 5MB)</label>
                    <input id="image" type="file" name="image" accept="image/*" required
                           class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-1.5 text-gray-600 transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <!-- Caption -->
                    <div class="space-y-1 sm:col-span-2">
                        <label for="caption" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Caption / Short Description</label>
                        <input id="caption" type="text" name="caption" value="{{ old('caption') }}"
                               class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300"
                               placeholder="e.g. Batur mountain sunrise from room balconey">
                    </div>

                    <!-- Order Index -->
                    <div class="space-y-1 sm:col-span-1">
                        <label for="order_index" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Order Index (Sort Order)</label>
                        <input id="order_index" type="number" name="order_index" value="{{ old('order_index', 0) }}" required
                               class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300"
                               placeholder="e.g. 0">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 items-center pt-4 border-t border-gray-100">
                    <button type="submit" 
                            class="px-8 py-3 rounded-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-bold text-sm tracking-wide shadow-lg shadow-blue-500/20 transition cursor-pointer">
                        Upload Photo
                    </button>
                    
                    <a href="{{ route('admin.cms.gallery.index') }}" 
                       class="px-8 py-3 rounded-full border border-gray-200 hover:bg-gray-50 text-gray-500 hover:text-gray-700 font-bold text-sm tracking-wide transition inline-block text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    @include('components.footer')
</body>
</html>
