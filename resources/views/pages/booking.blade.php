<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reservation - Bagus Guest House</title>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                <div class="space-y-1">
                    <label for="check-in-input" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Check-in Date</label>
                    <div class="relative">
                        <input type="date" id="check-in-input" 
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition">
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="check-out-input" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Check-out Date</label>
                    <div class="relative">
                        <input type="date" id="check-out-input" 
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="space-y-1">
                        <label for="adults-input" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Adults</label>
                        <input type="number" id="adults-input" min="1" value="2" 
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition">
                    </div>
                    <div class="space-y-1">
                        <label for="children-input" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Children</label>
                        <input type="number" id="children-input" min="0" value="0" 
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition">
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="promo-input" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Promo Code</label>
                    <div class="flex gap-2">
                        <input type="text" id="promo-input" placeholder="e.g. WELCOME10"
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 uppercase tracking-wider transition">
                        <button type="button" id="btn-apply-promo" 
                                class="bg-gray-900 hover:bg-gray-800 text-white px-3 py-2 rounded-lg text-xs font-bold transition cursor-pointer">
                            Apply
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Rooms List -->
            <div class="lg:col-span-2 space-y-6">
                <h2 class="text-xl font-bold text-gray-800 border-b pb-3 border-gray-200 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-700">hotel</span>
                    <span>Select Your Accommodation</span>
                </h2>

                <div class="space-y-6">
                    @forelse($rooms as $room)
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col sm:flex-row transition duration-300 hover:shadow-md room-card cursor-pointer" 
                             id="room-card-{{ $room->id }}" data-room-id="{{ $room->id }}" data-room-name="{{ $room->name }}" data-room-price="{{ $room->price }}"
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
                                </div>

                                <div class="flex items-end justify-between border-t border-gray-50 pt-4 gap-4">
                                    <div>
                                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Price per night</span>
                                        <span class="text-lg font-black text-amber-700">RP {{ number_format($room->price, 0, ',', '.') }}</span>
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
                <div id="modal-step-payment" class="p-6 sm:p-8 space-y-6 hidden">
                    <div class="text-center space-y-2">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-50 text-blue-600 mb-3">
                            <span class="material-symbols-outlined text-3xl">payments</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Select Payment Method</h3>
                        <p class="text-xs text-gray-500">Choose a dummy payment option below to finalize your booking.</p>
                    </div>

                    <!-- Payment Options -->
                    <div class="space-y-3">
                        <!-- Virtual Account -->
                        <div class="payment-option border border-gray-200 rounded-xl p-4 flex items-center justify-between cursor-pointer hover:bg-gray-50 transition" onclick="selectPaymentMethod('va')">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-2xl text-blue-600">account_balance</span>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-900">Virtual Account Transfer</h4>
                                    <p class="text-[10px] text-gray-500">BCA, Mandiri, BNI, or BRI</p>
                                </div>
                            </div>
                            <div class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center relative" id="pay-radio-va">
                                <div class="w-2.5 h-2.5 rounded-full bg-amber-700 absolute hidden" id="pay-dot-va"></div>
                            </div>
                        </div>

                        <!-- E-Wallet -->
                        <div class="payment-option border border-gray-200 rounded-xl p-4 flex items-center justify-between cursor-pointer hover:bg-gray-50 transition" onclick="selectPaymentMethod('ewallet')">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-2xl text-green-600">qr_code_2</span>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-900">E-Wallet / QRIS</h4>
                                    <p class="text-[10px] text-gray-500">GoPay, OVO, Dana, or LinkAja</p>
                                </div>
                            </div>
                            <div class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center relative" id="pay-radio-ewallet">
                                <div class="w-2.5 h-2.5 rounded-full bg-amber-700 absolute hidden" id="pay-dot-ewallet"></div>
                            </div>
                        </div>

                        <!-- Credit Card -->
                        <div class="payment-option border border-gray-200 rounded-xl p-4 flex items-center justify-between cursor-pointer hover:bg-gray-50 transition" onclick="selectPaymentMethod('cc')">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-2xl text-purple-600">credit_card</span>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-900">Credit / Debit Card</h4>
                                    <p class="text-[10px] text-gray-500">Visa, Mastercard, or JCB</p>
                                </div>
                            </div>
                            <div class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center relative" id="pay-radio-cc">
                                <div class="w-2.5 h-2.5 rounded-full bg-amber-700 absolute hidden" id="pay-dot-cc"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col gap-2">
                        <button type="button" id="btn-pay-now" onclick="processPayment()" disabled
                                class="w-full bg-gray-300 text-white py-3 rounded-xl font-bold text-xs sm:text-sm tracking-wide transition flex items-center justify-center gap-2 cursor-not-allowed select-none">
                            <span>Pay Now</span>
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
                        <h3 class="text-lg font-bold text-gray-900">Processing Payment...</h3>
                        <p class="text-xs text-gray-500">Please do not refresh the page while we authenticate your transaction.</p>
                    </div>
                </div>

                <!-- STEP 3: Receipt / Nota -->
                <div id="modal-step-receipt" class="p-6 sm:p-8 space-y-6 hidden">
                    <div class="text-center space-y-2">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-50 text-green-600 mb-3">
                            <span class="material-symbols-outlined text-3xl font-bold">check_circle</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Payment Successful!</h3>
                        <p class="text-xs text-gray-500">Thank you for booking with us. Here is your official guesthouse receipt.</p>
                    </div>

                    <!-- Official Invoice / Receipt printable styling -->
                    <div class="border border-gray-200 rounded-xl p-5 bg-gray-50/50 space-y-4 text-xs font-mono relative overflow-hidden" id="receipt-printable">
                        <!-- Paid Watermark -->
                        <div class="absolute -right-6 -top-2 bg-green-600 text-white font-sans font-black tracking-widest text-[9px] px-6 py-1.5 uppercase rotate-45 select-none">
                            PAID / LUNAS
                        </div>

                        <div class="text-center border-b border-dashed border-gray-300 pb-3">
                            <h4 class="font-sans font-black text-sm text-amber-700 uppercase tracking-wide">Bagus Guest House</h4>
                            <p class="text-[10px] text-gray-400 font-sans mt-0.5">Kintamani, Bali • +62 821-6991-1168</p>
                        </div>

                        <!-- Invoice Header -->
                        <div class="flex justify-between text-[10px] text-gray-500 border-b border-gray-100 pb-2">
                            <span>INVOICE: <strong class="text-gray-800" id="receipt-invoice-no">BGH-2026-X102</strong></span>
                            <span>DATE: <strong class="text-gray-800" id="receipt-date">-</strong></span>
                        </div>

                        <!-- Guest & Stay Details -->
                        <div class="space-y-1.5">
                            <div>GUEST: <span class="text-gray-800 font-bold" id="receipt-guest-name">-</span></div>
                            <div>ROOM: <span class="text-gray-800 font-bold" id="receipt-room-name">-</span></div>
                            <div>CHECK-IN: <span class="text-gray-800 font-bold" id="receipt-check-in">-</span></div>
                            <div>CHECK-OUT: <span class="text-gray-800 font-bold" id="receipt-check-out">-</span></div>
                            <div>DURATION: <span class="text-gray-800 font-bold" id="receipt-nights">-</span></div>
                            <div>GUESTS: <span class="text-gray-800 font-bold" id="receipt-guests">-</span></div>
                            <div>PAYMENT VIA: <span class="text-gray-800 font-bold uppercase" id="receipt-payment-method">-</span></div>
                        </div>

                        <!-- Pricing Breakdown -->
                        <div class="border-t border-dashed border-gray-300 pt-3 space-y-1">
                            <div class="flex justify-between">
                                <span>Room Rate Subtotal:</span>
                                <span class="text-gray-900 font-bold" id="receipt-subtotal">RP 0</span>
                            </div>
                            <div class="flex justify-between text-green-700 hidden" id="receipt-discount-row">
                                <span id="receipt-discount-label">Discount:</span>
                                <span class="font-bold" id="receipt-discount-amount">-RP 0</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tax & Service (10%):</span>
                                <span class="text-gray-900 font-bold" id="receipt-tax">RP 0</span>
                            </div>
                            <div class="flex justify-between font-black text-gray-900 border-t border-gray-200 pt-2 text-sm">
                                <span>TOTAL PAID:</span>
                                <span class="text-amber-700 font-bold" id="receipt-total">RP 0</span>
                            </div>
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

    <!-- Booking Interaction Logic (external to avoid HTML-parser conflicts) -->
    <script src="{{ asset('js/booking.js') }}"></script>

</body>
</html>
