<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Make a Booking - Bagus Guest House</title>
    @fonts
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-white text-gray-900 font-sans">
    @include('components.navbar')

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-gray-900 to-gray-800 py-16 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold mb-2">Book Your Stay</h1>
            <p class="text-gray-300">Secure your reservation at Bagus Guest House</p>
        </div>
    </section>

    <!-- Booking Form -->
    <section class="py-16 md:py-24">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-xl p-8">
                <form class="space-y-6">
                    <h2 class="text-2xl font-bold mb-6">Booking Details</h2>
                    
                    <!-- Guest Info -->
                    <div class="border-b pb-6">
                        <h3 class="font-bold mb-4 text-lg">Guest Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Full Name *</label>
                                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-700" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Email *</label>
                                <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-700" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Phone Number *</label>
                                <input type="tel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-700" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Country *</label>
                                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-700">
                            </div>
                        </div>
                    </div>

                    <!-- Room Selection -->
                    <div class="border-b pb-6">
                        <h3 class="font-bold mb-4 text-lg">Room Selection</h3>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Select Room *</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-700" required>
                                <option value="">-- Choose a room --</option>
                                <option value="family-suite-1">Family Suite 1 - RP910,000/night</option>
                                <option value="family-suite-2">Family Suite 2 - RP1,040,000/night</option>
                                <option value="suite-3">Suite 3 - RP1,200,000/night</option>
                                <option value="suite-4">Suite 4 - RP910,000/night</option>
                                <option value="suite-5">Suite 5 - RP1,040,000/night</option>
                                <option value="potato-1">Potato Room - RP650,000/night</option>
                            </select>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="border-b pb-6">
                        <h3 class="font-bold mb-4 text-lg">Reservation Dates</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Check-in Date *</label>
                                <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-700" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Check-out Date *</label>
                                <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-700" required>
                            </div>
                        </div>
                    </div>

                    <!-- Guests -->
                    <div class="border-b pb-6">
                        <h3 class="font-bold mb-4 text-lg">Number of Guests</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Adults *</label>
                                <input type="number" min="1" value="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-700" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Children</label>
                                <input type="number" min="0" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-700">
                            </div>
                        </div>
                    </div>

                    <!-- Special Requests -->
                    <div class="border-b pb-6">
                        <h3 class="font-bold mb-4 text-lg">Special Requests</h3>
                        <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-700 h-24" placeholder="Any special requests or requirements?"></textarea>
                    </div>

                    <!-- Booking Summary -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="font-bold mb-4">Booking Summary</h3>
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between">
                                <span>Room Rate (per night):</span>
                                <span id="room-rate">RP0</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Number of Nights:</span>
                                <span id="num-nights">0</span>
                            </div>
                            <div class="border-t pt-2 flex justify-between font-bold text-lg">
                                <span>Total Amount:</span>
                                <span id="total-amount" class="text-amber-700">RP0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Terms & Submit -->
                    <div>
                        <label class="flex items-center gap-2 mb-6">
                            <input type="checkbox" required class="w-4 h-4">
                            <span class="text-gray-600">I agree to the terms and conditions</span>
                        </label>
                        <button type="submit" class="w-full bg-amber-700 hover:bg-amber-800 text-white py-3 rounded-lg font-semibold transition text-lg">
                            Complete Booking
                        </button>
                    </div>
                </form>

                <!-- Note -->
                <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <strong>Note:</strong> This is a demo booking form. For actual reservations, please contact us via WhatsApp at +62 821-6991-1168 or call +62 361-9088-221
                    </p>
                </div>
            </div>
        </div>
    </section>

    @include('components.footer')
</body>
</html>
