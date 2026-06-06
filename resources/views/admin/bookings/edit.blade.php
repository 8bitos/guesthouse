<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Reservation - Bagus Guest House</title>
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
                    <span class="bg-amber-600 text-[10px] font-extrabold uppercase px-2 py-0.5 rounded">Manage Invoice</span>
                    <h1 class="text-2xl sm:text-3xl font-bold">Edit Reservation</h1>
                </div>
                <p class="text-gray-400 text-xs sm:text-sm mt-1">Review contact details and update check-in/out status logs for {{ $booking->invoice_no }}.</p>
            </div>
            
            <a href="{{ route('admin.bookings.index') }}" class="bg-white/10 hover:bg-white/15 text-white border border-white/20 px-4 py-2 rounded-lg font-semibold text-xs transition inline-block text-center flex items-center justify-center gap-1.5 self-start sm:self-auto">
                <span class="material-symbols-outlined text-sm font-bold">arrow_back</span>
                <span>Back to List</span>
            </a>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow w-full">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200/80 bg-gray-50/50 flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-700 text-sm leading-none">edit_note</span>
                <h2 class="text-base font-bold text-gray-800">Reservation Form</h2>
            </div>

            <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST" class="p-6 space-y-5">
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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Guest Name -->
                    <div class="space-y-1">
                        <label for="guest_name" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Guest Full Name *</label>
                        <input type="text" id="guest_name" name="guest_name" required value="{{ old('guest_name', $booking->guest_name) }}"
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition">
                    </div>

                    <!-- Guest Email -->
                    <div class="space-y-1">
                        <label for="guest_email" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Guest Email Address *</label>
                        <input type="email" id="guest_email" name="guest_email" required value="{{ old('guest_email', $booking->guest_email) }}"
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Guest Phone -->
                    <div class="space-y-1">
                        <label for="guest_phone" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Guest Phone Number *</label>
                        <input type="text" id="guest_phone" name="guest_phone" required value="{{ old('guest_phone', $booking->guest_phone) }}"
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition">
                    </div>

                    <!-- Guest Country -->
                    <div class="space-y-1">
                        <label for="guest_country" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Origin Country / Address *</label>
                        <input type="text" id="guest_country" name="guest_country" required value="{{ old('guest_country', $booking->guest_country) }}"
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition">
                    </div>
                </div>

                <!-- Room details (non-editable here for calculation integrity, but shown) -->
                <div class="p-4 bg-gray-50 border border-gray-150 rounded-xl space-y-2 text-xs">
                    <h4 class="font-bold text-gray-700 uppercase tracking-wide text-[10px]">Stay Summary (Read Only)</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <span class="block text-gray-400 font-semibold text-[9px] uppercase">Room Booked</span>
                            <strong class="text-gray-800">{{ $booking->room ? $booking->room->name : 'Deleted' }}</strong>
                        </div>
                        <div>
                            <span class="block text-gray-400 font-semibold text-[9px] uppercase">Nights</span>
                            <strong class="text-gray-800">{{ $booking->nights }} night(s)</strong>
                        </div>
                        <div>
                            <span class="block text-gray-400 font-semibold text-[9px] uppercase">Stay Range</span>
                            <strong class="text-gray-800">{{ date('d M', strtotime($booking->check_in)) }} - {{ date('d M Y', strtotime($booking->check_out)) }}</strong>
                        </div>
                        <div>
                            <span class="block text-gray-400 font-semibold text-[9px] uppercase">Total Cost Paid</span>
                            <strong class="text-amber-700 font-black">RP {{ number_format($booking->total_price, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Status Select -->
                <div class="space-y-1">
                    <label for="status" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Reservation Status *</label>
                    <select name="status" id="status" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition">
                        <option value="pending" {{ old('status', $booking->status) === 'pending' ? 'selected' : '' }}>Pending (Waiting payment verification)</option>
                        <option value="confirmed" {{ old('status', $booking->status) === 'confirmed' ? 'selected' : '' }}>Confirmed (Payment verified & stay secured)</option>
                        <option value="completed" {{ old('status', $booking->status) === 'completed' ? 'selected' : '' }}>Completed (Guest check-out finalized)</option>
                        <option value="cancelled" {{ old('status', $booking->status) === 'cancelled' ? 'selected' : '' }}>Cancelled (Booking revoked)</option>
                        <option value="rejected" {{ old('status', $booking->status) === 'rejected' ? 'selected' : '' }}>Rejected (Payment proof invalid/declined)</option>
                    </select>
                </div>

                <!-- Special Requests -->
                <div class="space-y-1">
                    <label for="special_requests" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Special Requests</label>
                    <textarea id="special_requests" name="special_requests" rows="3"
                              class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition resize-none"
                              placeholder="Any comments, requests... (e.g. late check in)">{{ old('special_requests', $booking->special_requests) }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.bookings.index') }}" 
                       class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-bold px-4 py-2.5 rounded-lg text-xs transition select-none">
                        Cancel
                    </a>
                    
                    <button type="submit" 
                            class="bg-amber-700 hover:bg-amber-800 text-white font-bold px-5 py-2.5 rounded-lg text-xs shadow-md shadow-amber-700/10 transition cursor-pointer select-none">
                        Save Reservation Details
                    </button>
                </div>
            </form>
        </div>

    </main>

    <!-- Footer -->
    @include('components.footer')
</body>
</html>
