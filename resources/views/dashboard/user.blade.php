<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Guest Dashboard - Bagus Guest House</title>
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
<body class="bg-gray-50 text-gray-900 font-sans min-h-screen flex flex-col justify-between">
    <!-- Navbar -->
    @include('components.navbar')

    <!-- Dashboard Header -->
    <section class="bg-gradient-to-r from-gray-900 to-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold mb-2">Welcome back, {{ $user->name }}!</h1>
                    <p class="text-gray-300">Manage your reservations and view your account profile.</p>
                </div>
                <div class="mt-4 md:mt-0 flex gap-3">
                    <a href="{{ route('rooms') }}" class="inline-block bg-amber-700 hover:bg-amber-800 text-white px-5 py-2.5 rounded-lg font-semibold transition">
                        Explore Rooms
                    </a>
                    <a href="{{ route('booking') }}" class="inline-block bg-white hover:bg-gray-100 text-amber-900 px-5 py-2.5 rounded-lg font-semibold transition border border-gray-200">
                        Book a Room
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Dashboard Body -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow w-full">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Side: Profile Summary Card -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 h-fit">
                <div class="text-center pb-6 border-b border-gray-100">
                    <div class="w-20 h-20 bg-amber-100 text-amber-800 rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-4">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h3 class="text-xl font-bold">{{ $user->name }}</h3>
                    <span class="inline-block bg-amber-100 text-amber-800 text-xs font-semibold px-2.5 py-0.5 rounded-full mt-2 capitalize">
                        {{ $user->role === 'pelanggan' ? 'Guest' : $user->role }}
                    </span>
                </div>
                
                <div class="py-6 space-y-4">
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Email Address</span>
                        <span class="text-gray-800 font-medium break-all">{{ $user->email }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Phone Number</span>
                        <span class="text-gray-800 font-medium">{{ $user->phone ?? 'Not provided' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Home Address</span>
                        <p class="text-gray-800 font-medium mt-1 text-sm bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                            {{ $user->address ?? 'Not provided' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Side: Booking History -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Bookings Card -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-gray-800">Your Booking History</h2>
                        <span class="text-xs text-gray-500 font-medium">Showing recent activity</span>
                    </div>
                    
                    @if (count($mockBookings) > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach ($mockBookings as $booking)
                                <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 hover:bg-gray-50 transition">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-3">
                                            <span class="text-sm font-semibold text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-md border border-amber-100">
                                                {{ $booking['id'] }}
                                            </span>
                                            <h4 class="font-bold text-gray-900">{{ $booking['room_name'] }}</h4>
                                        </div>
                                        <div class="flex gap-4 text-xs text-gray-500 pt-1">
                                            <span>Check In: <strong class="text-gray-700">{{ date('d M Y', strtotime($booking['check_in'])) }}</strong></span>
                                            <span>Check Out: <strong class="text-gray-700">{{ date('d M Y', strtotime($booking['check_out'])) }}</strong></span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between md:justify-end gap-6">
                                        <div>
                                            <span class="block text-xs text-gray-400 text-right">Total Price</span>
                                            <span class="font-bold text-gray-900">RP{{ number_format($booking['price'], 0, ',', '.') }}</span>
                                        </div>
                                        <div>
                                            @if ($booking['status'] === 'confirmed')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Confirmed
                                                </span>
                                            @elseif ($booking['status'] === 'completed')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> Completed
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-yellow-500"></span> Pending
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <span class="material-symbols-outlined text-gray-400 text-5xl">inbox</span>
                            <h3 class="text-lg font-semibold text-gray-700 mt-4">No reservations yet</h3>
                            <p class="text-gray-500 text-sm mt-1 mb-6">You haven't made any room reservations with us yet.</p>
                            <a href="{{ route('booking') }}" class="inline-block bg-amber-700 hover:bg-amber-800 text-white px-6 py-2.5 rounded-lg font-semibold transition">
                                Start Booking
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Support Box -->
                <div class="bg-gradient-to-br from-amber-600 to-amber-800 rounded-xl p-6 text-white shadow-md flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="space-y-1">
                        <h3 class="text-lg font-bold">Need Help With Your Stay?</h3>
                        <p class="text-amber-100 text-sm">Our 24/7 guest service line is available to help you with booking modifications, requests, and questions.</p>
                    </div>
                    <a href="https://wa.me/6282169911168" target="_blank" class="bg-white hover:bg-gray-100 text-amber-800 px-6 py-3 rounded-lg font-semibold transition text-center shrink-0">
                        Chat on WhatsApp
                    </a>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    @include('components.footer')
</body>
</html>
