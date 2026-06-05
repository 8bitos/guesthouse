<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Room - Bagus Guest House</title>
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
                <h1 class="text-3xl font-bold">Edit Room: {{ $room->name }}</h1>
                <p class="text-gray-400 text-sm mt-1">Update details for this accommodation listing.</p>
            </div>
            <a href="{{ route('admin.rooms.index') }}" class="text-sm bg-white/10 hover:bg-white/15 text-white border border-white/20 px-4 py-2 rounded-lg font-semibold transition">
                ← Back to List
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
            <form method="POST" action="{{ route('admin.rooms.update', $room) }}" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-8">
                    <!-- Room Name -->
                    <div class="space-y-1">
                        <label for="name" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Room Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $room->name) }}" required autofocus
                               class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300"
                               placeholder="e.g. Suite 3">
                    </div>

                    <!-- Room Type -->
                    <div class="space-y-1">
                        <label for="type" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Room Type</label>
                        <input id="type" type="text" name="type" value="{{ old('type', $room->type) }}" required
                               class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300"
                               placeholder="e.g. Suite, Villa, Deluxe">
                    </div>

                    <!-- Price per Night -->
                    <div class="space-y-1">
                        <label for="price" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Price per Night (RP)</label>
                        <input id="price" type="number" name="price" value="{{ old('price', (int)$room->price) }}" required
                               class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300"
                               placeholder="e.g. 1200000">
                    </div>

                    <!-- Capacity -->
                    <div class="space-y-1">
                        <label for="capacity" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Capacity (Guests)</label>
                        <input id="capacity" type="number" name="capacity" value="{{ old('capacity', $room->capacity) }}" required
                               class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300"
                               placeholder="e.g. 2">
                    </div>

                    <!-- Status -->
                    <div class="space-y-1">
                        <label for="status" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Operational Status</label>
                        <select id="status" name="status" required
                                class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-700 transition">
                            <option value="tersedia" {{ old('status', $room->status) === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="dipesan" {{ old('status', $room->status) === 'dipesan' ? 'selected' : '' }}>Dipesan</option>
                            <option value="perbaikan" {{ old('status', $room->status) === 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                        </select>
                    </div>

                    <!-- Image Upload -->
                    <div class="space-y-1">
                        <label for="image" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Room Image (Leave blank to keep current)</label>
                        <div class="flex flex-col sm:flex-row gap-4 items-start">
                            @if ($room->image)
                                <div class="w-20 h-16 rounded bg-gray-100 border border-gray-200 overflow-hidden shrink-0 flex items-center justify-center">
                                    <img src="{{ asset('storage/' . $room->image) }}" class="w-full h-full object-cover" alt="{{ $room->name }}">
                                </div>
                            @endif
                            <input id="image" type="file" name="image" accept="image/*"
                                   class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-1.5 text-gray-600 transition">
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-1">
                    <label for="description" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Room Description</label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300 resize-none"
                              placeholder="Enter room features, views, and included services...">{{ old('description', $room->description) }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 items-center pt-4 border-t border-gray-100">
                    <button type="submit" 
                            class="px-8 py-3 rounded-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-bold text-sm tracking-wide shadow-lg shadow-blue-500/20 transition cursor-pointer">
                        Update Room
                    </button>
                    
                    <a href="{{ route('admin.rooms.index') }}" 
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
