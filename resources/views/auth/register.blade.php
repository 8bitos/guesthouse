<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up - Bagus Guest House</title>
    @fonts
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            @import 'tailwindcss';
        </style>
    @endif
</head>
<body class="bg-[#F8FAFC] text-gray-900 font-sans min-h-screen flex flex-col lg:flex-row overflow-x-hidden">

    <!-- Left Side: Brand and Info Panel -->
    <div class="w-full lg:w-[40%] bg-gradient-to-br from-[#0052D4] via-[#4364F7] to-[#6FB1FC] text-white flex flex-col justify-between p-8 lg:p-12 relative z-10 shrink-0">
        <!-- SVG Wave Overlays (Desktop Only) -->
        <div class="absolute right-0 top-0 bottom-0 w-24 h-full pointer-events-none overflow-hidden hidden lg:block select-none">
            <svg class="absolute right-6 top-0 h-full w-24 text-blue-400/20" preserveAspectRatio="none" viewBox="0 0 100 100" fill="currentColor">
                <path d="M60 0 C25 25, 75 75, 40 100 L100 100 L100 0 Z" />
            </svg>
            <svg class="absolute right-3 top-0 h-full w-24 text-blue-300/30" preserveAspectRatio="none" viewBox="0 0 100 100" fill="currentColor">
                <path d="M50 0 C75 30, 25 70, 60 100 L100 100 L100 0 Z" />
            </svg>
            <svg class="absolute right-0 top-0 h-full w-24 text-[#F8FAFC]" preserveAspectRatio="none" viewBox="0 0 100 100" fill="currentColor">
                <path d="M40 0 C20 40, 60 60, 40 100 L100 100 L100 0 Z" />
            </svg>
        </div>

        <!-- Header -->
        <div class="flex items-center justify-between lg:justify-start">
            <a href="{{ route('home') }}" class="text-xl font-bold tracking-tight text-white hover:opacity-90 transition">
                Bagus Guest House
            </a>
            <a href="{{ route('home') }}" class="lg:hidden text-xs bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-full font-medium transition">
                ← Home
            </a>
        </div>

        <!-- Centered Welcome Info -->
        <div class="my-auto py-12 lg:py-0 text-center flex flex-col items-center">
            <h2 class="text-lg uppercase tracking-widest text-blue-100 font-semibold mb-6">Welcome to</h2>
            
            <!-- Logo Circle -->
            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-xl shadow-blue-900/30 mb-4 animate-bounce duration-1000">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-[#4364F7]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            
            <h1 class="text-3xl font-extrabold mb-4">Guesthouse System</h1>
            <p class="text-sm text-blue-100/80 max-w-xs leading-relaxed">
                Create an account to browse luxury rooms, make reservations, and access your personal guest dashboard.
            </p>
        </div>

        <!-- Footer -->
        <div class="text-xs text-blue-200/60 flex justify-between items-center pt-6 lg:pt-0">
            <span>Bagus Guest House</span>
            <span>CREATOR HERE | DESIGNER HERE</span>
        </div>
    </div>

    <!-- Right Side: Forms Panel -->
    <div class="w-full lg:flex-grow bg-[#F8FAFC] flex flex-col justify-center relative min-h-[500px]">
        <!-- Horizontal Wave (Mobile Only) -->
        <div class="relative w-full lg:hidden h-16 pointer-events-none overflow-hidden -mt-16 z-20 select-none">
            <svg class="absolute left-0 bottom-4 w-full h-16 text-blue-400/20" preserveAspectRatio="none" viewBox="0 0 100 100" fill="currentColor">
                <path d="M0 60 C30 25, 70 75, 100 40 L100 100 L0 100 Z" />
            </svg>
            <svg class="absolute left-0 bottom-2 w-full h-16 text-blue-300/30" preserveAspectRatio="none" viewBox="0 0 100 100" fill="currentColor">
                <path d="M0 50 C25 75, 75 25, 100 60 L100 100 L0 100 Z" />
            </svg>
            <svg class="absolute left-0 bottom-0 w-full h-16 text-[#F8FAFC]" preserveAspectRatio="none" viewBox="0 0 100 100" fill="currentColor">
                <path d="M0 40 C30 60, 70 20, 100 40 L100 100 L0 100 Z" />
            </svg>
        </div>

        <!-- Form Wrapper -->
        <div class="w-full max-w-xl mx-auto px-6 py-12 lg:py-16">
            <div class="mb-10">
                <h3 class="text-2xl lg:text-3xl font-extrabold text-gray-900 mb-2">Create your account</h3>
                <p class="text-sm text-gray-400">Complete the form below to register.</p>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="mb-8 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-8">
                    <!-- Name -->
                    <div class="space-y-1">
                        <label for="name" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Name</label>
                        <div class="relative">
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                                   class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300 pr-8"
                                   placeholder="Enter your name">
                            <span class="absolute right-0 bottom-3 text-blue-500 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- E-mail Address -->
                    <div class="space-y-1">
                        <label for="email" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">E-mail Address</label>
                        <div class="relative">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                                   class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300 pr-8"
                                   placeholder="Enter your mail">
                            <span class="absolute right-0 bottom-3 text-blue-500 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="space-y-1">
                        <label for="phone" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Phone Number</label>
                        <div class="relative">
                            <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                                   class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300 pr-8"
                                   placeholder="Enter phone number">
                            <span class="absolute right-0 bottom-3 text-blue-500 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Home Address -->
                    <div class="space-y-1">
                        <label for="address" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Home Address</label>
                        <div class="relative">
                            <input id="address" type="text" name="address" value="{{ old('address') }}"
                                   class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300 pr-8"
                                   placeholder="Enter your address">
                            <span class="absolute right-0 bottom-3 text-blue-500 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-1">
                        <label for="password" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                   class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300 pr-8"
                                   placeholder="Enter your password">
                            <span class="absolute right-0 bottom-3 text-blue-500 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-1">
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Confirm Password</label>
                        <div class="relative">
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                   class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300 pr-8"
                                   placeholder="Confirm password">
                            <span class="absolute right-0 bottom-3 text-blue-500 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Terms & Agreement checkbox -->
                <div class="flex items-start pt-2">
                    <input id="terms" type="checkbox" required
                           class="h-4 w-4 mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:outline-none cursor-pointer">
                    <label for="terms" class="ml-2.5 text-sm text-gray-500 select-none cursor-pointer leading-tight">
                        By Signing Up, I Agree with <a href="#" class="text-blue-600 hover:underline">Terms & Conditions</a>
                    </label>
                </div>

                <!-- Buttons Layout -->
                <div class="flex gap-4 items-center pt-4">
                    <button type="submit" 
                            class="px-8 py-3 rounded-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-bold text-sm tracking-wide shadow-lg shadow-blue-500/20 transition cursor-pointer">
                        Sign Up
                    </button>
                    
                    <a href="{{ route('login') }}" 
                       class="px-8 py-3 rounded-full border border-gray-200 hover:border-blue-500 hover:bg-blue-50/30 text-gray-500 hover:text-blue-600 font-bold text-sm tracking-wide transition inline-block text-center">
                        Sign In
                    </a>
                </div>
            </form>
        </div>
        
        <div class="hidden lg:block absolute right-12 bottom-8 text-xs text-gray-400">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Back to Homepage</a>
        </div>
    </div>

</body>
</html>
