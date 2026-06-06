<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reservation - Bagus Guest House</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
<body class="bg-[#F8FAFC] text-gray-900 font-sans min-h-screen flex flex-col justify-between">
    <!-- Navbar -->
    @include('components.navbar')

    <!-- Header Banner -->
    <section class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center sm:text-left">
            <h1 class="text-3xl sm:text-4xl font-bold">Book Your Luxury Stay</h1>
            <p class="text-gray-400 text-sm mt-1 sm:text-base">Secure your reservation at Bagus Guest House, Kintamani.</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow w-full">
        <!-- Top Availability Check Widget -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 mb-8">
            <!-- Header Keterangan Detail Pemesanan -->
            <div class="flex items-center gap-2 border-b border-gray-100 pb-3 mb-5">
                <span class="material-symbols-outlined text-amber-700 text-lg leading-none">info</span>
                <span class="text-xs font-black uppercase tracking-wider text-gray-700">Detail Pemesanan / Stay Configuration</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                <!-- Check-in Date -->
                <div class="space-y-1.5 flex flex-col justify-between h-full">
                    <div>
                        <div class="flex items-center gap-1.5 mb-1.5">
                            <span class="material-symbols-outlined text-amber-700 text-sm">login</span>
                            <label for="check-in-input" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider">Check-in Date</label>
                        </div>
                        <div class="relative">
                            <input type="date" id="check-in-input" 
                                   class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition">
                        </div>
                    </div>
                    <span class="block text-[10px] text-gray-400 font-semibold mt-1">Check-in: 14:00 - 22:00</span>
                </div>

                <!-- Check-out Date -->
                <div class="space-y-1.5 flex flex-col justify-between h-full">
                    <div>
                        <div class="flex items-center gap-1.5 mb-1.5">
                            <span class="material-symbols-outlined text-amber-700 text-sm">logout</span>
                            <label for="check-out-input" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider">Check-out Date</label>
                        </div>
                        <div class="relative">
                            <input type="date" id="check-out-input" 
                                   class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition">
                        </div>
                    </div>
                    <span class="block text-[10px] text-gray-400 font-semibold mt-1">Check-out: 08:00 - 12:00</span>
                </div>

                <!-- Adults Count -->
                <div class="space-y-1.5 flex flex-col justify-between h-full">
                    <div>
                        <div class="flex items-center gap-1.5 mb-1.5">
                            <span class="material-symbols-outlined text-amber-700 text-sm">person</span>
                            <label for="adults-input" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider">Adults</label>
                        </div>
                        <div class="relative">
                            <input type="number" id="adults-input" min="1" value="2" 
                                   class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition">
                        </div>
                    </div>
                    <span class="block text-[10px] text-gray-400 font-semibold mt-1">Age 13 or above</span>
                </div>

                <!-- Children Count -->
                <div class="space-y-1.5 flex flex-col justify-between h-full">
                    <div>
                        <div class="flex items-center gap-1.5 mb-1.5">
                            <span class="material-symbols-outlined text-amber-700 text-sm">child_care</span>
                            <label for="children-input" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider">Children</label>
                        </div>
                        <div class="relative">
                            <input type="number" id="children-input" min="0" value="0" 
                                   class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition">
                        </div>
                    </div>
                    <span class="block text-[10px] text-gray-400 font-semibold mt-1">Age 12 or below</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Rooms List -->
            <div class="lg:col-span-2 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b pb-3 border-gray-200 gap-4">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-700">hotel</span>
                        <span>Select Your Accommodation</span>
                    </h2>
                    
                    <!-- Availability Toggle -->
                    <label class="relative inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" id="toggle-available-only" class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-700"></div>
                        <span class="ms-3 text-xs font-bold text-gray-700">Tampilkan Kamar Tersedia Saja</span>
                    </label>
                </div>

                <div class="space-y-6">
                    @forelse($rooms as $room)
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col sm:flex-row transition duration-300 hover:shadow-md room-card cursor-pointer" 
                             id="room-card-{{ $room->id }}" data-room-id="{{ $room->id }}" data-room-name="{{ $room->name }}" data-room-price="{{ $room->price }}"
                             data-allow-breakfast="{{ $room->allow_breakfast ? 1 : 0 }}"
                             data-allow-extra-bed="{{ $room->allow_extra_bed ? 1 : 0 }}"
                             data-allow-late-checkout="{{ $room->allow_late_checkout ? 1 : 0 }}"
                             onclick="selectRoom({{ $room->id }})">
                            <!-- Room Image -->
                            <div class="sm:w-1/3 aspect-[4/3] sm:aspect-auto bg-gray-100 overflow-hidden flex items-center justify-center shrink-0 relative border-b sm:border-b-0 sm:border-r border-gray-100">
                                @if ($room->image)
                                    <img src="{{ asset('storage/' . $room->image) }}" class="w-full h-full object-cover" alt="{{ $room->name }}">
                                @else
                                    <div class="absolute inset-0 bg-gradient-to-br from-gray-100 to-gray-200 flex flex-col items-center justify-center text-gray-400 p-4 text-center gap-1.5">
                                        <span class="material-symbols-outlined text-4xl text-gray-500">bed</span>
                                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">{{ $room->name }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Room Info -->
                            <div class="p-6 flex-grow flex flex-col justify-between space-y-4">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <h3 class="text-lg font-bold text-gray-900 leading-tight">{{ $room->name }}</h3>
                                        <span class="bg-amber-50 text-amber-800 border border-amber-100 text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md">
                                            {{ $room->type }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 leading-relaxed">
                                        {{ $room->description ?? 'Beautiful luxury room offering stunning scenery, modern amenities, and ultimate comfort in Kintamani.' }}
                                    </p>
                                    
                                    <!-- Badges -->
                                    <div class="flex flex-wrap gap-2 mt-3 text-[10px] text-gray-500">
                                        <span class="flex items-center gap-1 bg-gray-50 border border-gray-100 px-2 py-1 rounded">
                                            <span class="material-symbols-outlined text-xs leading-none text-gray-400">group</span>
                                            <span>Max {{ $room->capacity }} Guests</span>
                                        </span>
                                        <span class="flex items-center gap-1 bg-gray-50 border border-gray-100 px-2 py-1 rounded">
                                            <span class="material-symbols-outlined text-xs leading-none text-gray-400">aspect_ratio</span>
                                            <span>{{ $room->capacity >= 4 ? 25 : 15 }} m²</span>
                                        </span>
                                        <span class="flex items-center gap-1 bg-gray-50 border border-gray-100 px-2 py-1 rounded">
                                            <span class="material-symbols-outlined text-xs leading-none text-gray-400">king_bed</span>
                                            <span>{{ $room->capacity >= 4 ? '1 King + Sofa Bed' : '1 King Bed' }}</span>
                                        </span>
                                    </div>

                                    <!-- Booked Status Notice -->
                                    <div class="room-booked-status-container hidden mt-3">
                                        <div class="inline-flex items-center gap-1.5 bg-red-50 border border-red-200 text-red-700 px-3 py-1.5 rounded-lg text-[11px] font-bold">
                                            <span class="material-symbols-outlined text-sm leading-none text-red-600">error</span>
                                            <span>Sudah dibooking sampai : <span class="room-booked-until-date">-</span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-end justify-between border-t border-gray-50 pt-4 gap-4">
                                    <div>
                                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Price per night</span>
                                        <span class="text-lg font-black text-amber-700">RP {{ number_format($room->price, 0, ',', '.') }}</span>
                                        <span class="block text-[9px] text-gray-400 font-semibold mt-0.5 room-card-duration-price hidden" id="duration-price-{{ $room->id }}" data-base-price="{{ $room->price }}">
                                            Price for 0 night(s): RP 0
                                        </span>
                                    </div>

                                    <button type="button" class="btn-select-room border border-amber-700 text-amber-700 hover:bg-amber-50 px-5 py-2 rounded-lg text-xs font-bold tracking-wide transition cursor-pointer select-none"
                                            onclick="event.stopPropagation(); selectRoom({{ $room->id }})">
                                        Select Room
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center bg-white border border-gray-200 rounded-2xl shadow-sm">
                            <span class="material-symbols-outlined text-gray-400 text-5xl">inbox</span>
                            <h3 class="text-lg font-bold text-gray-700 mt-4">No rooms available</h3>
                            <p class="text-gray-500 text-sm mt-1">Check back later or contact customer support.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right Side: Sticky Checkout Summary & Form -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">
                    <!-- Summary Card -->
                    <form id="booking-form" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-6">
                        <h3 class="text-lg font-bold text-gray-800 border-b pb-3 border-gray-100 flex items-center gap-2">
                            <span class="material-symbols-outlined text-amber-700">receipt_long</span>
                            <span>Booking Summary</span>
                        </h3>

                        <!-- Selected Room Details -->
                        <div class="space-y-4" id="summary-room-section">
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex items-center justify-between" id="summary-no-room">
                                <p class="text-xs text-gray-500 italic">No room selected yet. Choose a room from the left to calculate details.</p>
                            </div>
                            <div class="bg-amber-50/20 border border-amber-100/50 p-4 rounded-xl hidden" id="summary-room-details">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="text-xs font-bold text-amber-800 uppercase tracking-wider block" id="summary-room-type">SUITE</span>
                                        <h4 class="text-sm font-bold text-gray-900" id="summary-room-name">Family Suite</h4>
                                    </div>
                                    <span class="text-xs font-black text-amber-700" id="summary-room-rate">RP 910.000</span>
                                </div>
                            </div>
                        </div>

                        <!-- Date Summary -->
                        <div class="grid grid-cols-2 gap-4 text-xs border-b border-gray-100 pb-4">
                            <div>
                                <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Check In</span>
                                <span class="font-bold text-gray-800" id="summary-check-in">-</span>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Check Out</span>
                                <span class="font-bold text-gray-800" id="summary-check-out">-</span>
                            </div>
                        </div>

                        <!-- Cost Summary -->
                        <div class="space-y-2.5 text-xs text-gray-600 border-b border-gray-100 pb-4">
                            <div class="flex justify-between">
                                <span>Duration:</span>
                                <span class="font-semibold" id="summary-nights">0 night(s)</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Room Subtotal:</span>
                                <span class="font-semibold text-gray-900" id="summary-subtotal">RP 0</span>
                            </div>
                            <!-- Extras rows -->
                            <div class="flex justify-between text-gray-600 hidden" id="summary-breakfast-row">
                                <span>Breakfast (Sarapan):</span>
                                <span class="font-semibold text-gray-900" id="summary-breakfast-amount">RP 0</span>
                            </div>
                            <div class="flex justify-between text-gray-600 hidden" id="summary-extra-bed-row">
                                <span>Extra Bed (Kasur):</span>
                                <span class="font-semibold text-gray-900" id="summary-extra-bed-amount">RP 0</span>
                            </div>
                            <div class="flex justify-between text-gray-600 hidden" id="summary-late-checkout-row">
                                <span>Late Check-out:</span>
                                <span class="font-semibold text-gray-900" id="summary-late-checkout-amount">RP 0</span>
                            </div>
                            <div class="flex justify-between text-green-700 hidden" id="summary-discount-row">
                                <span id="summary-discount-label">Discount (10%):</span>
                                <span class="font-semibold" id="summary-discount-amount">-RP 0</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tax & Service (10%):</span>
                                <span class="font-semibold text-gray-900" id="summary-tax">RP 0</span>
                            </div>
                            <div class="flex justify-between text-sm font-black text-gray-900 pt-2">
                                <span>Total Amount:</span>
                                <span class="text-base text-amber-700" id="summary-total">RP 0</span>
                            </div>
                        </div>

                        <!-- Guest Information Fields inside Card -->
                        <div class="space-y-4 border-t border-gray-100 pt-6">
                            <h4 class="font-bold text-gray-800 text-sm flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-gray-500 text-base leading-none">person</span>
                                <span>Guest Details</span>
                            </h4>

                            <div class="space-y-3">
                                <div class="space-y-1">
                                    <label for="guest-name" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Full Name *</label>
                                    <input type="text" id="guest-name" required
                                           class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition"
                                           placeholder="e.g. John Doe"
                                           value="{{ auth()->user()->name ?? '' }}">
                                </div>
                                <div class="space-y-1">
                                    <label for="guest-email" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Email Address *</label>
                                    <input type="email" id="guest-email" required
                                           class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition"
                                           placeholder="e.g. john@example.com"
                                           value="{{ auth()->user()->email ?? '' }}">
                                </div>
                                <div class="space-y-1">
                                    <label for="guest-phone" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Phone Number *</label>
                                    <input type="tel" id="guest-phone" required
                                           class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition"
                                           placeholder="e.g. +62 821-xxxx-xxxx"
                                           value="{{ auth()->user()->phone ?? '' }}">
                                </div>
                                <div class="space-y-1">
                                    <label for="guest-country" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Country / Origin *</label>
                                    <input type="text" id="guest-country" required
                                           class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition"
                                           placeholder="e.g. Indonesia"
                                           value="{{ auth()->user()->address ?? '' }}">
                                </div>
                                <div class="space-y-1">
                                    <label for="guest-requests" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Special Requests</label>
                                    <textarea id="guest-requests" rows="3"
                                              class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition resize-none"
                                              placeholder="e.g. extra bed, early check-in, etc."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Add-ons & Extras -->
                        <div class="space-y-4 border-t border-gray-100 pt-6">
                            <h4 class="font-bold text-gray-800 text-sm flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-gray-500 text-base leading-none">add_box</span>
                                <span>Extras & Add-ons</span>
                            </h4>

                            <div class="space-y-3">
                                <!-- Breakfast Option -->
                                <div id="opt-breakfast" onclick="toggleAddon('breakfast')" class="group flex items-center justify-between p-3 rounded-xl border border-gray-200 hover:border-amber-700 bg-white hover:bg-amber-50/10 cursor-pointer transition select-none">
                                    <div class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-amber-700 text-lg">restaurant</span>
                                        <div>
                                            <span class="block text-xs font-bold text-gray-800">Breakfast (Sarapan)</span>
                                            <span class="block text-[10px] text-gray-500">+RP 50.000 / guest / night</span>
                                        </div>
                                    </div>
                                    <div class="addon-status-indicator flex items-center justify-center w-6 h-6 rounded-full border border-gray-200 bg-gray-50 text-gray-300 group-hover:text-gray-400 group-hover:border-gray-300 transition font-bold" id="indicator-breakfast" style="display: flex;">
                                        <span class="material-symbols-outlined text-xs font-bold leading-none">close</span>
                                    </div>
                                    <input type="checkbox" id="addon-breakfast-checkbox" class="hidden" name="include_breakfast" value="1">
                                </div>

                                <!-- Extra Bed Option -->
                                <div id="opt-extra-bed" onclick="toggleAddon('extra-bed')" class="group flex items-center justify-between p-3 rounded-xl border border-gray-200 hover:border-amber-700 bg-white hover:bg-amber-50/10 cursor-pointer transition select-none">
                                    <div class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-amber-700 text-lg">bed</span>
                                        <div>
                                            <span class="block text-xs font-bold text-gray-800">Extra Bed (Kasur Tambahan)</span>
                                            <span class="block text-[10px] text-gray-500">+RP 150.000 / night</span>
                                        </div>
                                    </div>
                                    <div class="addon-status-indicator flex items-center justify-center w-6 h-6 rounded-full border border-gray-200 bg-gray-50 text-gray-300 group-hover:text-gray-400 group-hover:border-gray-300 transition font-bold" id="indicator-extra-bed" style="display: flex;">
                                        <span class="material-symbols-outlined text-xs font-bold leading-none">close</span>
                                    </div>
                                    <input type="checkbox" id="addon-extra-bed-checkbox" class="hidden" name="include_extra_bed" value="1">
                                </div>

                                <!-- Late Check-out Option -->
                                <div id="opt-late-checkout" onclick="toggleAddon('late-checkout')" class="group flex items-center justify-between p-3 rounded-xl border border-gray-200 hover:border-amber-700 bg-white hover:bg-amber-50/10 cursor-pointer transition select-none">
                                    <div class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-amber-700 text-lg">schedule</span>
                                        <div>
                                            <span class="block text-xs font-bold text-gray-800">Late Check-out</span>
                                            <span class="block text-[10px] text-gray-500">+RP 100.000 flat</span>
                                        </div>
                                    </div>
                                    <div class="addon-status-indicator flex items-center justify-center w-6 h-6 rounded-full border border-gray-200 bg-gray-50 text-gray-300 group-hover:text-gray-400 group-hover:border-gray-300 transition font-bold" id="indicator-late-checkout" style="display: flex;">
                                        <span class="material-symbols-outlined text-xs font-bold leading-none">close</span>
                                    </div>
                                    <input type="checkbox" id="addon-late-checkout-checkbox" class="hidden" name="late_checkout" value="1">
                                </div>
                            </div>
                        </div>

                        <!-- Agreement Checkbox -->
                        <label class="flex items-start gap-2 cursor-pointer select-none text-[10px] text-gray-500 leading-tight">
                            <input type="checkbox" required class="mt-0.5 w-3.5 h-3.5 border border-gray-300 rounded text-amber-700 focus:ring-amber-500">
                            <span>I agree to the reservation terms, guest capacity restrictions, and Kintamani Guesthouse booking regulations.</span>
                        </label>

                        <!-- Submit Button -->
                        <button type="submit" id="btn-submit-booking"
                                class="w-full bg-amber-700 hover:bg-amber-800 text-white py-3 rounded-xl font-bold text-sm tracking-wide shadow-md shadow-amber-700/20 transition cursor-pointer select-none">
                            Complete Reservation
                        </button>
                    </form>

                    <!-- Contact/Support Info Box -->
                    <div class="bg-amber-50/50 border border-amber-100 rounded-2xl p-5 text-center flex flex-col items-center">
                        <span class="material-symbols-outlined text-amber-700 text-4xl mb-2">support_agent</span>
                        <h4 class="font-bold text-gray-900 text-sm">Need Help With Your Stay?</h4>
                        <p class="text-xs text-gray-500 mt-1 mb-3">Reach out to our customer service line on WhatsApp for custom inquiries or groups.</p>
                        <a href="https://wa.me/6282169911168" target="_blank"
                           class="inline-block bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-bold px-4 py-2 rounded-lg text-xs transition">
                            Chat on WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Success Booking Confirmation Modal (Multi-Step Checkout Flow) -->
    <div id="success-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay background -->
            <div class="fixed inset-0 transition-opacity bg-black/60" onclick="closeModal()"></div>
            <!-- Center elements -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <!-- Modal box -->
            <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                
                <!-- STEP 1: Reservation Details -->
                <div id="modal-step-details" class="p-6 sm:p-8 space-y-6">
                    <div class="text-center space-y-2">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-amber-50 text-amber-600 mb-3">
                            <span class="material-symbols-outlined text-3xl">info</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Reservation Details</h3>
                        <p class="text-xs text-gray-500">Please review your stay and contact information before proceeding to payment.</p>
                    </div>

                    <!-- Booking Data Details List -->
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 space-y-3 text-xs">
                        <div class="flex justify-between py-1 border-b border-gray-200/50">
                            <span class="text-gray-500">Guest Name:</span>
                            <strong class="text-gray-900" id="modal-guest-name">John Doe</strong>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-200/50">
                            <span class="text-gray-500">Selected Room:</span>
                            <strong class="text-gray-900" id="modal-room-name">Family Suite</strong>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-200/50">
                            <span class="text-gray-500">Stay Dates:</span>
                            <strong class="text-gray-900" id="modal-stay-dates">05 Jun 2026 - 06 Jun 2026</strong>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-200/50">
                            <span class="text-gray-500">Length of Stay:</span>
                            <strong class="text-gray-900" id="modal-nights">1 night(s)</strong>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-200/50">
                            <span class="text-gray-500">Guests:</span>
                            <strong class="text-gray-900" id="modal-guests">2 adults</strong>
                        </div>
                        <!-- Modal details rows for add-ons -->
                        <div class="flex justify-between py-1 border-b border-gray-200/50 hidden" id="modal-breakfast-row">
                            <span class="text-gray-500">Breakfast (Sarapan):</span>
                            <strong class="text-gray-900" id="modal-breakfast-val">Yes</strong>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-200/50 hidden" id="modal-extra-bed-row">
                            <span class="text-gray-500">Extra Bed (Kasur):</span>
                            <strong class="text-gray-900" id="modal-extra-bed-val">Yes</strong>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-200/50 hidden" id="modal-late-checkout-row">
                            <span class="text-gray-500">Late Check-out:</span>
                            <strong class="text-gray-900" id="modal-late-checkout-val">Yes</strong>
                        </div>
                        <div class="flex justify-between py-1 pt-2">
                            <span class="text-sm font-bold text-gray-900">Total Price:</span>
                            <strong class="text-sm text-amber-700" id="modal-total-price">RP 1,001,000</strong>
                        </div>
                    </div>

                    <!-- Call To Action -->
                    <div class="flex flex-col gap-2">
                        <button type="button" onclick="goToPaymentStep()"
                                class="w-full bg-amber-700 hover:bg-amber-800 text-white py-3 rounded-xl font-bold text-xs sm:text-sm tracking-wide shadow-lg shadow-amber-700/20 transition flex items-center justify-center gap-2 cursor-pointer select-none">
                            <span>Proceed to Payment</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </button>
                        <button type="button" onclick="closeModal()"
                                class="w-full bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 py-2.5 rounded-xl font-bold text-xs transition cursor-pointer select-none">
                            Cancel
                        </button>
                    </div>
                </div>

                <!-- STEP 2: Choose Payment Method -->
                <!-- STEP 2: Bank Transfer Payment -->
                <div id="modal-step-payment" class="p-6 sm:p-8 space-y-6 hidden">
                    <div class="text-center space-y-2">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-amber-50 text-amber-700 mb-3">
                            <span class="material-symbols-outlined text-3xl">account_balance</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Bank Transfer Payment</h3>
                        <p class="text-xs text-gray-500">Transfer the exact amount to the account below and upload your receipt.</p>
                    </div>

                    <!-- Bank Account Info -->
                    <div class="bg-amber-50/50 border border-amber-100 rounded-xl p-4 space-y-2.5 text-xs">
                        <div class="flex justify-between border-b border-amber-200/20 pb-2">
                            <span class="text-gray-500">Bank Name:</span>
                            <strong class="text-gray-900 font-bold uppercase">BCA (Bank Central Asia)</strong>
                        </div>
                        <div class="flex justify-between border-b border-amber-200/20 pb-2">
                            <span class="text-gray-500">Account Number:</span>
                            <div class="flex items-center gap-1.5">
                                <strong class="text-gray-900 font-bold font-mono">123-456-7890</strong>
                                <button type="button" onclick="navigator.clipboard.writeText('123-456-7890'); alert('Account number copied!');" class="text-[10px] text-amber-700 hover:underline font-bold focus:outline-none cursor-pointer">Copy</button>
                            </div>
                        </div>
                        <div class="flex justify-between border-b border-amber-200/20 pb-2">
                            <span class="text-gray-500">Account Owner:</span>
                            <strong class="text-gray-900 font-bold uppercase">Bagus Guest House</strong>
                        </div>
                        <div class="flex justify-between pt-1 font-bold text-gray-900">
                            <span>Transfer Amount:</span>
                            <strong class="text-sm text-amber-700" id="transfer-amount-display">RP 0</strong>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Upload Transfer Receipt *</label>
                        <div class="border-2 border-dashed border-gray-200 hover:border-amber-700/50 rounded-xl p-4 transition text-center relative bg-gray-50/30">
                            <input type="file" id="payment-proof-input" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="handleFileSelect(event)">
                            <div class="space-y-1" id="upload-placeholder">
                                <span class="material-symbols-outlined text-3xl text-gray-400">upload_file</span>
                                <p class="text-[11px] font-semibold text-gray-700">Click or drag receipt image here</p>
                                <p class="text-[9px] text-gray-400">PNG, JPG, or JPEG up to 2MB</p>
                            </div>
                            <div class="hidden space-y-1" id="upload-success-preview">
                                <span class="material-symbols-outlined text-3xl text-emerald-600">check_circle</span>
                                <p class="text-[11px] font-bold text-emerald-800" id="upload-filename">receipt.jpg</p>
                                <p class="text-[9px] text-gray-400">Selected. Click to replace.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col gap-2">
                        <button type="button" id="btn-pay-now" onclick="submitBooking()" disabled
                                class="w-full bg-gray-300 text-white py-3 rounded-xl font-bold text-xs sm:text-sm tracking-wide transition flex items-center justify-center gap-2 cursor-not-allowed select-none">
                            <span>Submit Payment Proof</span>
                        </button>
                        <button type="button" onclick="goToStep('details')"
                                class="w-full bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 py-2.5 rounded-xl font-bold text-xs transition cursor-pointer select-none">
                            Back to Details
                        </button>
                    </div>
                </div>

                <!-- STEP 2.5: Processing Loader -->
                <div id="modal-step-processing" class="p-6 sm:p-8 space-y-6 hidden text-center select-none">
                    <div class="py-12 flex flex-col items-center justify-center space-y-4">
                        <div class="w-12 h-12 border-4 border-amber-700 border-t-transparent rounded-full animate-spin"></div>
                        <h3 class="text-lg font-bold text-gray-900">Uploading Payment Proof...</h3>
                        <p class="text-xs text-gray-500">Please do not refresh while we submit your booking request.</p>
                    </div>
                </div>

                <!-- STEP 3: Receipt / Nota -->
                <div id="modal-step-receipt" class="p-6 sm:p-8 space-y-6 hidden">
                    <div class="text-center space-y-2">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-50 text-yellow-600 mb-3">
                            <span class="material-symbols-outlined text-3xl font-bold">schedule</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Booking Pending Verification!</h3>
                        <p class="text-xs text-gray-500">We are verifying your transfer. Here is your temporary guesthouse receipt.</p>
                    </div>
                    <!-- Official Invoice / Receipt printable styling -->
                    <div id="receipt-printable" style="font-family: 'Inter', 'Segoe UI', sans-serif; background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; overflow: hidden; width: 100%; box-sizing: border-box;">

                        <!-- Top Status Banner -->
                        <div id="receipt-watermark" style="background-color: #ca8a04; color: #fff; text-align: center; padding: 8px 16px; font-size: 10px; font-weight: 800; letter-spacing: 0.2em; text-transform: uppercase;">
                            PENDING
                        </div>

                        <!-- Branding Row -->
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px 12px; border-bottom: 1px solid #f3f4f6;">
                            <div>
                                <div style="font-size: 13px; font-weight: 800; color: #92400e; text-transform: uppercase; letter-spacing: 0.05em; line-height: 1.2;">Bagus Guest House</div>
                                <div style="font-size: 10px; color: #9ca3af; margin-top: 2px;">Kintamani, Bali &bull; +62 821-6991-1168</div>
                            </div>
                            <span id="receipt-status-badge" style="background-color: #fef9c3; color: #854d0e; border: 1px solid #fde68a; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 3px 8px; border-radius: 999px; white-space: nowrap;">⏳ Pending</span>
                        </div>

                        <!-- Invoice Number & Date -->
                        <div style="display: flex; background: #f9fafb; border-bottom: 1px solid #f3f4f6;">
                            <div style="flex: 1; padding: 10px 20px; border-right: 1px solid #f3f4f6;">
                                <div style="font-size: 9px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600; margin-bottom: 2px;">Invoice No.</div>
                                <div style="font-size: 11px; font-weight: 700; color: #1f2937; font-family: monospace;" id="receipt-invoice-no">-</div>
                            </div>
                            <div style="flex: 1; padding: 10px 20px;">
                                <div style="font-size: 9px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600; margin-bottom: 2px;">Date Issued</div>
                                <div style="font-size: 11px; font-weight: 700; color: #1f2937; font-family: monospace;" id="receipt-date">-</div>
                            </div>
                        </div>

                        <!-- Reservation Details -->
                        <div style="padding: 14px 20px; border-bottom: 1px solid #f3f4f6;">
                            <div style="font-size: 9px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; margin-bottom: 10px;">Reservation Details</div>
                            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                                <tr>
                                    <td style="padding: 3px 0; color: #6b7280; width: 38%;">Guest Name</td>
                                    <td style="padding: 3px 0; color: #111827; font-weight: 600;" id="receipt-guest-name">-</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px 0; color: #6b7280;">Room</td>
                                    <td style="padding: 3px 0; color: #111827; font-weight: 600;" id="receipt-room-name">-</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px 0; color: #6b7280;">Check-In</td>
                                    <td style="padding: 3px 0; color: #111827; font-weight: 600; font-family: monospace;" id="receipt-check-in">-</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px 0; color: #6b7280;">Check-Out</td>
                                    <td style="padding: 3px 0; color: #111827; font-weight: 600; font-family: monospace;" id="receipt-check-out">-</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px 0; color: #6b7280;">Duration</td>
                                    <td style="padding: 3px 0; color: #111827; font-weight: 600;" id="receipt-nights">-</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px 0; color: #6b7280;">Guests</td>
                                    <td style="padding: 3px 0; color: #111827; font-weight: 600;" id="receipt-guests">-</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px 0; color: #6b7280;">Payment Via</td>
                                    <td style="padding: 3px 0; color: #111827; font-weight: 600; text-transform: uppercase;" id="receipt-payment-method">-</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Pricing -->
                        <div style="padding: 14px 20px;">
                            <div style="font-size: 9px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; margin-bottom: 10px;">Payment Summary</div>
                            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                                <tr>
                                    <td style="padding: 3px 0; color: #6b7280;">Room Rate Subtotal</td>
                                    <td style="padding: 3px 0; color: #1f2937; font-weight: 600; font-family: monospace; text-align: right;" id="receipt-subtotal">RP 0</td>
                                </tr>
                                <!-- Extras rows for receipt -->
                                <tr id="receipt-breakfast-row" style="display: none;">
                                    <td style="padding: 3px 0; color: #6b7280;">Breakfast (Sarapan)</td>
                                    <td style="padding: 3px 0; color: #1f2937; font-weight: 600; font-family: monospace; text-align: right;" id="receipt-breakfast-amount">RP 0</td>
                                </tr>
                                <tr id="receipt-extra-bed-row" style="display: none;">
                                    <td style="padding: 3px 0; color: #6b7280;">Extra Bed (Kasur)</td>
                                    <td style="padding: 3px 0; color: #1f2937; font-weight: 600; font-family: monospace; text-align: right;" id="receipt-extra-bed-amount">RP 0</td>
                                </tr>
                                <tr id="receipt-late-checkout-row" style="display: none;">
                                    <td style="padding: 3px 0; color: #6b7280;">Late Check-out</td>
                                    <td style="padding: 3px 0; color: #1f2937; font-weight: 600; font-family: monospace; text-align: right;" id="receipt-late-checkout-amount">RP 0</td>
                                </tr>
                                <tr id="receipt-discount-row" style="display: none;">
                                    <td style="padding: 3px 0; color: #16a34a;" id="receipt-discount-label">Discount</td>
                                    <td style="padding: 3px 0; color: #16a34a; font-weight: 600; font-family: monospace; text-align: right;" id="receipt-discount-amount">-RP 0</td>
                                </tr>
                                <tr>
                                    <td style="padding: 3px 0; color: #6b7280;">Tax &amp; Service (10%)</td>
                                    <td style="padding: 3px 0; color: #1f2937; font-weight: 600; font-family: monospace; text-align: right;" id="receipt-tax">RP 0</td>
                                </tr>
                            </table>
                            <!-- Total Row -->
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px; padding: 10px 12px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px;">
                                <span style="font-size: 10px; font-weight: 700; color: #92400e; text-transform: uppercase; letter-spacing: 0.08em;">Total Amount</span>
                                <span style="font-size: 14px; font-weight: 800; color: #b45309; font-family: monospace;" id="receipt-total">RP 0</span>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div style="text-align: center; padding: 12px 20px 16px; border-top: 1px dashed #e5e7eb;">
                            <p style="font-size: 10px; color: #9ca3af; margin: 0 0 10px; font-style: italic;">Thank you for choosing Bagus Guest House!</p>
                            <div style="display: flex; align-items: flex-end; justify-content: center; gap: 1px; height: 24px; opacity: 0.4;">
                                <div style="width:1px;height:100%;background:#374151;"></div><div style="width:2px;height:80%;background:#374151;"></div><div style="width:1px;height:100%;background:#374151;"></div><div style="width:3px;height:60%;background:#374151;"></div><div style="width:1px;height:100%;background:#374151;"></div><div style="width:2px;height:100%;background:#374151;"></div><div style="width:1px;height:70%;background:#374151;"></div><div style="width:3px;height:100%;background:#374151;"></div><div style="width:1px;height:85%;background:#374151;"></div><div style="width:2px;height:100%;background:#374151;"></div><div style="width:1px;height:100%;background:#374151;"></div><div style="width:2px;height:65%;background:#374151;"></div><div style="width:3px;height:100%;background:#374151;"></div><div style="width:1px;height:90%;background:#374151;"></div><div style="width:2px;height:100%;background:#374151;"></div><div style="width:1px;height:75%;background:#374151;"></div><div style="width:3px;height:100%;background:#374151;"></div><div style="width:1px;height:100%;background:#374151;"></div><div style="width:2px;height:80%;background:#374151;"></div><div style="width:1px;height:100%;background:#374151;"></div>
                            </div>
                            <div style="font-size: 8px; font-family: monospace; color: #9ca3af; letter-spacing: 0.15em; margin-top: 4px; text-transform: uppercase;">★ BGH-RESERVATION ★</div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col gap-2">
                        <button type="button" onclick="printReceipt()"
                                class="w-full bg-gray-900 hover:bg-gray-800 text-white py-3 rounded-xl font-bold text-xs sm:text-sm tracking-wide shadow-md transition flex items-center justify-center gap-2 cursor-pointer select-none">
                            <span class="material-symbols-outlined text-sm font-bold">print</span>
                            <span>Print Receipt</span>
                        </button>
                        <a href="{{ route('dashboard') }}"
                           class="w-full bg-amber-700 hover:bg-amber-800 text-white py-3 rounded-xl font-bold text-xs sm:text-sm tracking-wide shadow-md transition flex items-center justify-center gap-2 text-center select-none">
                            <span class="material-symbols-outlined text-sm font-bold">dashboard</span>
                            <span>Go to Dashboard</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer -->
    @include('components.footer')

    <!-- HTML2Canvas Pro Library for Receipt Printing (supports OKLCH colors used in Tailwind v4) -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas-pro@1.5.8/dist/html2canvas-pro.js"></script>

    <!-- Booking Interaction Logic (external to avoid HTML-parser conflicts) -->
    <script src="{{ asset('js/booking.js') }}"></script>

</body>
</html>
