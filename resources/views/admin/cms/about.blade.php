<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit About Us - Bagus Guest House</title>
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
                <h1 class="text-3xl font-bold">Customize "About Us"</h1>
                <p class="text-gray-400 text-sm mt-1">Configure texts, bullet points, and vision statement on the landing page.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="text-sm bg-white/10 hover:bg-white/15 text-white border border-white/20 px-4 py-2 rounded-lg font-semibold transition">
                ← Dashboard
            </a>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow w-full">
        <!-- Success Alert -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-semibold flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700 font-bold">&times;</button>
            </div>
        @endif

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
            <form method="POST" action="{{ route('admin.cms.about') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- Hero Background Image -->
                <div class="space-y-2">
                    <label for="hero_image" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Hero Background Image</label>
                    @if ($heroImage)
                        <div class="mb-3">
                            <span class="block text-[10px] text-gray-400 font-bold uppercase mb-1">Current Background:</span>
                            <div class="w-full max-w-md h-32 rounded-lg overflow-hidden border border-gray-200 shadow-sm relative">
                                <img src="{{ asset('storage/' . $heroImage) }}" class="w-full h-full object-cover" alt="Hero Background">
                            </div>
                        </div>
                    @endif
                    <input id="hero_image" type="file" name="hero_image" accept="image/*"
                           class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition cursor-pointer">
                    <span class="text-xs text-gray-400 block mt-1">Recommended size: 1920x1080px. Supported formats: JPG, PNG, WEBP (Max 2MB).</span>
                </div>

                <!-- About Us Section Title -->
                <div class="space-y-1">
                    <label for="about_title" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Section Title</label>
                    <input id="about_title" type="text" name="about_title" value="{{ old('about_title', $aboutTitle) }}" required
                           class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300"
                           placeholder="e.g. About Us">
                </div>

                <!-- About Us Description -->
                <div class="space-y-1">
                    <label for="about_desc" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Description Text</label>
                    <textarea id="about_desc" name="about_desc" rows="5" required
                              class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300 resize-none"
                              placeholder="Enter About Us main text...">{{ old('about_desc', $aboutDesc) }}</textarea>
                </div>

                <!-- Why Choose Us bullet points -->
                <div class="space-y-1">
                    <label for="about_why_list" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Why Choose Us (One bullet point per line)</label>
                    <textarea id="about_why_list" name="about_why_list" rows="5" required
                              class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-800 font-mono text-sm transition placeholder-gray-300 resize-none"
                              placeholder="Spectacular views&#10;Modern luxury rooms&#10;Fine dining cafe">{{ old('about_why_list', $aboutWhyList) }}</textarea>
                    <span class="text-xs text-gray-400 block mt-1">Press Enter to add a new bullet point list item.</span>
                </div>

                <!-- Our Vision Statement -->
                <div class="space-y-1">
                    <label for="about_vision" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Our Vision Statement</label>
                    <textarea id="about_vision" name="about_vision" rows="4" required
                              class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300 resize-none"
                              placeholder="Enter guesthouse vision text...">{{ old('about_vision', $aboutVision) }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 items-center pt-4 border-t border-gray-100">
                    <button type="submit" 
                            class="px-8 py-3 rounded-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-bold text-sm tracking-wide shadow-lg shadow-blue-500/20 transition cursor-pointer">
                        Save Content
                    </button>
                    
                    <a href="{{ route('admin.dashboard') }}" 
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
