<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Customer - Bagus Guest House</title>
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

    <!-- Header Banner -->
    <section class="bg-gray-900 text-white py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="bg-amber-600 text-[10px] font-extrabold uppercase px-2 py-0.5 rounded">Edit Profile</span>
                    <h1 class="text-2xl sm:text-3xl font-bold">Edit Customer</h1>
                </div>
                <p class="text-gray-400 text-xs sm:text-sm mt-1">Modify account credentials and billing particulars for BGH-USR-{{ $user->id }}.</p>
            </div>
            
            <a href="{{ route('admin.users.index') }}" class="bg-white/10 hover:bg-white/15 text-white border border-white/20 px-4 py-2 rounded-lg font-semibold text-xs transition inline-block text-center flex items-center justify-center gap-1.5 self-start sm:self-auto">
                <span class="material-symbols-outlined text-sm font-bold">arrow_back</span>
                <span>Back to List</span>
            </a>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow w-full">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200/80 bg-gray-50/50 flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-700 text-sm leading-none">person</span>
                <h2 class="text-base font-bold text-gray-800">Customer Details Form</h2>
            </div>

            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-4 text-xs font-semibold">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Full Name -->
                <div class="space-y-1">
                    <label for="name" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Full Name *</label>
                    <input type="text" id="name" name="name" required value="{{ old('name', $user->name) }}"
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition">
                </div>

                <!-- Email Address -->
                <div class="space-y-1">
                    <label for="email" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Email Address *</label>
                    <input type="email" id="email" name="email" required value="{{ old('email', $user->email) }}"
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition">
                </div>

                <!-- Phone Number -->
                <div class="space-y-1">
                    <label for="phone" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition"
                           placeholder="e.g. +62 821-xxxx-xxxx">
                </div>

                <!-- Address -->
                <div class="space-y-1">
                    <label for="address" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Origin Address / Country</label>
                    <textarea id="address" name="address" rows="4"
                              class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition resize-none"
                              placeholder="e.g. Ubud, Bali, Indonesia">{{ old('address', $user->address) }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.users.index') }}" 
                       class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-bold px-4 py-2.5 rounded-lg text-xs transition select-none">
                        Cancel
                    </a>
                    
                    <button type="submit" 
                            class="bg-amber-700 hover:bg-amber-800 text-white font-bold px-5 py-2.5 rounded-lg text-xs shadow-md shadow-amber-700/10 transition cursor-pointer select-none">
                        Save Profile Changes
                    </button>
                </div>
            </form>
        </div>

    </main>

    <!-- Footer -->
    @include('components.footer')
</body>
</html>
