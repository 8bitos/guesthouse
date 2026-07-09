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
            <p class="text-gray-400 text-sm mt-1 sm:text-base">Secure your reservation at Bagus Guest House, Kuta.</p>
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

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
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

                <!-- Apply Stay Action -->
                <div class="space-y-1.5 flex flex-col justify-between h-full">
                    <div>
                        <div class="flex items-center gap-1.5 mb-1.5">
                            <span class="material-symbols-outlined text-amber-700 text-sm">check_circle</span>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider">Action</label>
                        </div>
                        <button type="button" id="btn-apply-stay"
                                class="w-full bg-amber-700 hover:bg-amber-800 text-white rounded-lg px-4 py-2 text-xs font-bold transition cursor-pointer select-none shadow-sm flex items-center justify-center gap-1.5">
                            <span class="material-symbols-outlined text-xs leading-none">done</span>
                            <span>Apply Stay</span>
                        </button>
                    </div>
                    <span class="block text-[10px] text-gray-400 font-semibold mt-1">Apply dates and check availability</span>
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
                             data-room-capacity="{{ $room->capacity }}"
                             data-room-type="{{ $room->type }}"
                             data-room-size="{{ $room->size ?? ($room->capacity >= 4 ? 25 : 15) }}"
                             data-room-addons="{{ json_encode($room->addons ?? []) }}"
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
                                        {{ $room->description ?? 'Beautiful luxury room offering stunning scenery, modern amenities, and ultimate comfort in Kuta.' }}
                                    </p>
                                    
                                    <!-- Badges -->
                                    <div class="flex flex-wrap gap-2 mt-3 text-[10px] text-gray-500">
                                        <span class="flex items-center gap-1 bg-gray-50 border border-gray-100 px-2 py-1 rounded">
                                            <span class="material-symbols-outlined text-xs leading-none text-gray-400">group</span>
                                            <span>Max {{ $room->capacity }} Guests</span>
                                        </span>
                                        <span class="flex items-center gap-1 bg-gray-50 border border-gray-100 px-2 py-1 rounded">
                                            <span class="material-symbols-outlined text-xs leading-none text-gray-400">aspect_ratio</span>
                                            <span>
                                                @if(isset($room->size))
                                                    {{ $room->size }}{{ str_contains($room->size, 'x') ? ' m' : ' m²' }}
                                                @else
                                                    {{ $room->capacity >= 4 ? 25 : 15 }} m²
                                                @endif
                                            </span>
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
                            <!-- Selected rooms list container -->
                            <div id="summary-rooms-list" class="space-y-3 hidden">
                                <!-- Dynamically generated room cards go here -->
                            </div>
                        </div>

                        <!-- Date Configuration inside Sidebar -->
                        <div class="grid grid-cols-2 gap-3 text-xs border-b border-gray-100 pb-4">
                            <div>
                                <label for="check-in-sidebar" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-amber-700 text-xs">login</span>
                                    <span>Check In</span>
                                </label>
                                <input type="date" id="check-in-sidebar" 
                                       class="w-full bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:outline-none focus:border-amber-700 transition">
                            </div>
                            <div>
                                <label for="check-out-sidebar" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-amber-700 text-xs">logout</span>
                                    <span>Check Out</span>
                                </label>
                                <input type="date" id="check-out-sidebar" 
                                       class="w-full bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:outline-none focus:border-amber-700 transition">
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
                            <div class="flex justify-between text-gray-600 hidden" id="summary-other-addons-row">
                                <span>Other Extras:</span>
                                <span class="font-semibold text-gray-900" id="summary-other-addons-amount">RP 0</span>
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



                        <!-- Agreement Checkbox -->
                        <label class="flex items-start gap-2 cursor-pointer select-none text-[10px] text-gray-500 leading-tight">
                            <input type="checkbox" required class="mt-0.5 w-3.5 h-3.5 border border-gray-300 rounded text-amber-700 focus:ring-amber-500">
                            <span>I agree to the reservation terms, guest capacity restrictions, and Kuta Guesthouse booking regulations.</span>
                        </label>

                        <!-- Booking Availability Alert Hint -->
                        <div id="booking-availability-alert" class="hidden bg-red-50 border border-red-200 text-red-700 p-3.5 rounded-xl text-xs space-y-1">
                            <div class="flex items-center gap-1.5 font-bold">
                                <span class="material-symbols-outlined text-sm text-red-600">error</span>
                                <span>Kamar Tidak Tersedia / Overlapping Booking</span>
                            </div>
                            <p class="text-[10px] text-gray-500 leading-normal">Beberapa kamar yang Anda pilih sudah dibooking pada tanggal tersebut. Silakan ubah tanggal atau hapus kamar yang tidak tersedia dari summary.</p>
                        </div>

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

    <!-- Custom Alert Modal -->
    <div id="custom-alert-modal" class="fixed inset-0 z-[100] overflow-y-auto hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeCustomAlert()"></div>
        
        <!-- Modal Wrapper -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <!-- Alert Box (1x1 square layout: w-72 h-72) -->
            <div class="relative w-72 h-72 bg-white rounded-2xl text-left shadow-2xl border border-gray-100 p-6 mx-auto my-8 flex flex-col justify-between items-center">
                <div class="flex flex-col items-center text-center space-y-3 my-auto">
                    <!-- Icon Container with warm amber tone -->
                    <div class="flex items-center justify-center h-14 w-14 rounded-full bg-amber-50 text-amber-700 border border-amber-100/50 shadow-inner mb-1">
                        <span class="material-symbols-outlined text-3xl font-bold">warning</span>
                    </div>
                    <!-- Title -->
                    <h3 class="text-base font-black text-gray-900 tracking-tight" id="custom-alert-title">Reservation System</h3>
                    <!-- Message -->
                    <p class="text-xs text-gray-500 font-semibold leading-relaxed px-1" id="custom-alert-message">
                        You cannot perform checkout because you are logged in as an admin.
                    </p>
                </div>
                <!-- Button -->
                <button type="button" onclick="closeCustomAlert()"
                        class="w-full bg-gradient-to-r from-amber-700 to-amber-800 hover:from-amber-800 hover:to-amber-900 text-white py-2.5 rounded-xl font-bold text-xs tracking-wide shadow-md shadow-amber-700/20 transition duration-200 transform active:scale-95 cursor-pointer select-none">
                    OK
                </button>
            </div>
        </div>
    </div>


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
                            <strong class="text-gray-900" id="modal-guests">2 guests</strong>
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
                        <div class="flex justify-between py-1 border-b border-gray-200/50 hidden" id="modal-other-addons-row">
                            <span class="text-gray-500">Other Extras:</span>
                            <strong class="text-gray-900" id="modal-other-addons-val">Yes</strong>
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

                <!-- STEP 2: Online Payment -->
                <div id="modal-step-payment" class="p-6 sm:p-8 space-y-6 hidden animate-fade-in-up">
                    <div class="text-center space-y-2">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-50 text-blue-700 mb-3">
                            <span class="material-symbols-outlined text-3xl font-bold">credit_card</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Online Payment</h3>
                        <p class="text-xs text-gray-500">Pay securely using QRIS, Virtual Account, or GoPay via Midtrans.</p>
                    </div>

                    <!-- Block 2: Midtrans Details Wrapper -->
                    <div id="midtrans-payment-details" class="space-y-4 animate-fade-in-up">
                        <div class="bg-blue-50/70 border border-blue-150 rounded-xl p-3.5 text-xs text-blue-800 space-y-2 leading-relaxed">
                            <div class="flex items-center gap-1.5 font-bold text-blue-950">
                                <span class="material-symbols-outlined text-sm">payment</span>
                                <span>Midtrans Sandbox Checkout</span>
                            </div>
                            <p class="font-medium">
                                You are choosing Midtrans Sandbox. Upon clicking "Pay with Midtrans", the official Sandbox checkout popup will open. You can simulate success using Midtrans Sandbox credentials or simulator.
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col gap-2">
                        <button type="button" id="btn-pay-now" onclick="submitBooking()"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold text-xs sm:text-sm tracking-wide shadow-lg shadow-blue-750/20 transition flex items-center justify-center gap-2 cursor-pointer select-none">
                            <span>Pay with Midtrans Sandbox</span>
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
                                <div style="font-size: 10px; color: #9ca3af; margin-top: 2px;">Kuta, Bali &bull; +62 821-6991-1168</div>
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
                        <button type="button" id="btn-bypass-payment" onclick="bypassPaymentDirectly()"
                                class="w-full bg-emerald-650 hover:bg-emerald-700 text-white py-3 rounded-xl font-bold text-xs sm:text-sm tracking-wide shadow-md transition flex items-center justify-center gap-2 cursor-pointer select-none">
                            <span class="material-symbols-outlined text-sm font-bold">bolt</span>
                            <span>Verify Payment Status</span>
                        </button>
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

    <!-- Mock Midtrans Snap Modal -->
    <div id="mock-snap-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[9999] hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl border border-gray-250 shadow-2xl max-w-md w-full overflow-hidden animate-fade-in-up">
            <!-- Header -->
            <div class="bg-blue-600 text-white p-4.5 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">payment</span>
                    <span class="text-xs font-bold uppercase tracking-wider">Midtrans Snap Sandbox</span>
                </div>
                <button type="button" onclick="closeMockSnapModal()" class="text-white/80 hover:text-white transition focus:outline-none cursor-pointer">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
            </div>
            <!-- Body -->
            <div class="p-6 space-y-4.5">
                <div class="text-center space-y-1.5">
                    <div style="font-size: 11px; font-weight: 800; color: #92400e; text-transform: uppercase; letter-spacing: 0.05em;">Bagus Guest House</div>
                    <h3 class="text-lg font-black text-gray-900 tracking-tight" id="mock-snap-invoice">BGH-202607-1234</h3>
                </div>
                
                <div class="bg-gray-50 border border-gray-150 rounded-xl p-4.5 flex justify-between items-center text-xs">
                    <span class="text-gray-500 font-medium">Total Payment Amount:</span>
                    <strong class="text-base font-black text-blue-600 font-mono" id="mock-snap-amount">RP 0</strong>
                </div>

                <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-3 text-[10px] text-blue-800 leading-normal flex items-start gap-2">
                    <span class="material-symbols-outlined text-xs leading-none shrink-0 mt-0.5">info</span>
                    <span>This is a mock sandbox simulation modal because Midtrans API keys are not configured. Click "Simulate Success" below to verify the checkout flow.</span>
                </div>

                <!-- Simulation actions -->
                <div class="flex flex-col gap-2 pt-2">
                    <button type="button" onclick="simulateSuccessMockSnap()"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-bold text-xs sm:text-sm tracking-wide shadow-md transition flex items-center justify-center gap-2 cursor-pointer select-none">
                        <span class="material-symbols-outlined text-sm font-bold">check_circle</span>
                        <span>Simulate Successful Payment</span>
                    </button>
                    <button type="button" onclick="closeMockSnapModal()"
                            class="w-full bg-white hover:bg-gray-50 border border-gray-200 text-gray-550 py-2.5 rounded-xl font-bold text-xs transition cursor-pointer select-none">
                        Cancel Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- HTML2Canvas Pro Library for Receipt Printing (supports OKLCH colors used in Tailwind v4) -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas-pro@1.5.8/dist/html2canvas-pro.js"></script>

    <script>
        window.userRole = "{{ auth()->user() ? auth()->user()->role : '' }}";
    </script>

    <!-- Midtrans Snap.js Library -->
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <!-- Booking Interaction Logic (external to avoid HTML-parser conflicts) -->
    <script src="{{ asset('js/booking.js') }}"></script>

</body>
</html>
