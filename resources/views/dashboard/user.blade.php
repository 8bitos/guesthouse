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
        @if (session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-sm font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600 text-base">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-850 rounded-xl p-4 text-sm font-semibold flex flex-col gap-1">
                @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-rose-650 text-base">error</span>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif
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
                <!-- Bookings & Complaints Card -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                    <!-- Tab Headers -->
                    <div class="flex border-b border-gray-100 bg-gray-50">
                        <button type="button" id="tab-bookings-btn" onclick="switchTab('bookings')" class="flex-1 py-4 px-6 text-sm font-bold text-center border-b-2 border-amber-600 text-amber-700 focus:outline-none transition cursor-pointer select-none">
                            Booking History
                        </button>
                        <button type="button" id="tab-complaints-btn" onclick="switchTab('complaints')" class="flex-1 py-4 px-6 text-sm font-semibold text-center border-b-2 border-transparent text-gray-500 hover:text-amber-700 hover:border-amber-500 focus:outline-none transition cursor-pointer select-none">
                            Complaints & Feedback
                        </button>
                    </div>
                    
                    <!-- Tab Content: Bookings -->
                    <div id="tab-bookings-content" class="block">
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
                                        
                                        <div class="flex items-center justify-between md:justify-end gap-4 sm:gap-6">
                                            <div class="text-right">
                                                <span class="block text-xs text-gray-400">Total Price</span>
                                                <span class="font-bold text-gray-900">RP{{ number_format($booking['price'], 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex flex-col sm:flex-row items-end sm:items-center gap-2">
                                                <div>
                                                    @if ($booking['status'] === 'checked_in')
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200 animate-pulse">
                                                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span> Checked In
                                                        </span>
                                                    @elseif ($booking['status'] === 'confirmed')
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Confirmed
                                                        </span>
                                                    @elseif ($booking['status'] === 'completed')
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> Completed
                                                        </span>
                                                    @elseif ($booking['status'] === 'rejected')
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-800 border border-rose-200">
                                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Rejected
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                            <span class="h-1.5 w-1.5 rounded-full bg-yellow-500"></span> Pending
                                                        </span>
                                                    @endif
                                                </div>

                                                <button type="button" 
                                                        onclick="showReceiptModal({{ json_encode($booking) }})"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 transition cursor-pointer select-none">
                                                    <span class="material-symbols-outlined text-[14px] font-bold">receipt_long</span>
                                                    <span>Receipt</span>
                                                </button>
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

                    <!-- Tab Content: Complaints -->
                    <div id="tab-complaints-content" class="hidden p-6 space-y-8">
                        <!-- Submit Complaint Form -->
                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-700">report_problem</span>
                                Submit New Complaint or Feedback
                            </h3>
                            
                            <form action="{{ route('complaints.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="booking_id" class="block text-xs font-bold text-gray-600 uppercase mb-1">Related Booking (Optional)</label>
                                    <select name="booking_id" id="booking_id" class="w-full bg-white rounded-lg border border-gray-200 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/25 focus:border-amber-600 transition">
                                        <option value="">-- Not Related to a Specific Booking --</option>
                                        @foreach ($mockBookings as $booking)
                                            <option value="{{ $booking['db_id'] }}">{{ $booking['room_name'] }} ({{ $booking['invoice_no'] }} &bull; Check-in: {{ date('d M Y', strtotime($booking['check_in'])) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="subject" class="block text-xs font-bold text-gray-600 uppercase mb-1">Subject</label>
                                    <input type="text" name="subject" id="subject" required placeholder="e.g. Broken AC, Missing towels, Refund query..." class="w-full bg-white rounded-lg border border-gray-200 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/25 focus:border-amber-600 transition">
                                </div>
                                
                                <div>
                                    <label for="description" class="block text-xs font-bold text-gray-600 uppercase mb-1">Detailed Description</label>
                                    <textarea name="description" id="description" rows="4" required placeholder="Please describe the issue in detail so our staff can assist you." class="w-full bg-white rounded-lg border border-gray-200 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/25 focus:border-amber-600 transition"></textarea>
                                </div>
                                
                                <button type="submit" class="bg-amber-700 hover:bg-amber-800 text-white font-semibold py-2.5 px-5 rounded-lg text-sm transition shadow-sm w-full md:w-auto">
                                    Submit Ticket
                                </button>
                            </form>
                        </div>

                        <!-- Complaints History -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-bold text-gray-800">Your Support Tickets</h3>
                            
                            @if (count($complaints) > 0)
                                <div class="overflow-x-auto border border-gray-150 rounded-xl">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="bg-gray-50 border-b border-gray-150 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                <th class="py-3 px-4">Date</th>
                                                <th class="py-3 px-4">Subject</th>
                                                <th class="py-3 px-4">Booking</th>
                                                <th class="py-3 px-4">Status</th>
                                                <th class="py-3 px-4 text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 text-sm">
                                            @foreach ($complaints as $complaint)
                                                <tr class="hover:bg-gray-50/50">
                                                    <td class="py-3 px-4 font-medium text-gray-550 whitespace-nowrap">
                                                        {{ $complaint->created_at->format('d M Y') }}
                                                    </td>
                                                    <td class="py-3 px-4">
                                                        <div class="font-bold text-gray-900">{{ $complaint->subject }}</div>
                                                        <p class="text-xs text-gray-550 mt-1 max-w-md truncate">{{ $complaint->description }}</p>
                                                    </td>
                                                    <td class="py-3 px-4 text-xs font-semibold text-gray-600 whitespace-nowrap">
                                                        {{ $complaint->booking ? $complaint->booking->invoice_no : 'N/A' }}
                                                    </td>
                                                    <td class="py-3 px-4 whitespace-nowrap">
                                                        @if ($complaint->status === 'resolved')
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-150 text-green-800 border border-green-200">
                                                                Resolved
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-150 text-yellow-800 border border-yellow-250">
                                                                Pending
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 px-4 text-right whitespace-nowrap">
                                                        @php
                                                            $complaintData = [
                                                                'id' => $complaint->id,
                                                                'date' => $complaint->created_at->format('d M Y'),
                                                                'subject' => $complaint->subject,
                                                                'description' => $complaint->description,
                                                                'booking_invoice' => $complaint->booking ? $complaint->booking->invoice_no : 'N/A',
                                                                'status' => $complaint->status,
                                                                'resolution' => $complaint->resolution
                                                            ];
                                                        @endphp
                                                        <button type="button" 
                                                                onclick="showTicketModal({{ json_encode($complaintData) }})"
                                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 transition cursor-pointer select-none">
                                                            <span class="material-symbols-outlined text-[14px] font-bold">visibility</span>
                                                            <span>View Details</span>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-8 bg-gray-50 rounded-xl border border-gray-100">
                                    <span class="material-symbols-outlined text-gray-400 text-4xl">confirmation_number</span>
                                    <h4 class="text-xs font-semibold text-gray-700 mt-2">No support tickets</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">You haven't filed any complaints or feedback yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
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

    <!-- Receipt Modal -->
    <div id="receipt-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/55 backdrop-blur-sm transition-opacity" onclick="closeReceiptModal()"></div>
        
        <!-- Modal Wrapper -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100 p-6 space-y-6">
                <!-- Close Button -->
                <button type="button" onclick="closeReceiptModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition cursor-pointer">
                    <span class="material-symbols-outlined font-bold text-xl">close</span>
                </button>

                <div class="text-center space-y-2">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-amber-50 text-amber-800 mb-2">
                        <span class="material-symbols-outlined text-3xl font-bold">receipt_long</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Your Booking Receipt</h3>
                    <p class="text-xs text-gray-500">Review your booking transaction details below.</p>
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
                    <div style="display: flex; gap: 0; background: #f9fafb; border-bottom: 1px solid #f3f4f6;">
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
                            <tr id="receipt-other-addons-row" style="display: none;">
                                <td style="padding: 3px 0; color: #6b7280;">Other Add-ons</td>
                                <td style="padding: 3px 0; color: #1f2937; font-weight: 600; font-family: monospace; text-align: right;" id="receipt-other-addons-amount">RP 0</td>
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
                <div class="flex flex-col gap-2 pt-2">
                    <button type="button" onclick="printReceipt()"
                            class="w-full bg-gray-900 hover:bg-gray-800 text-white py-3 rounded-xl font-bold text-xs sm:text-sm tracking-wide shadow-md transition flex items-center justify-center gap-2 cursor-pointer select-none">
                        <span class="material-symbols-outlined text-sm font-bold">print</span>
                        <span>Download Receipt (PNG)</span>
                    </button>
                    <button type="button" onclick="closeReceiptModal()"
                            class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl font-bold text-xs sm:text-sm tracking-wide transition flex items-center justify-center gap-2 cursor-pointer select-none">
                        <span>Close</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Ticket Modal -->
    <div id="ticket-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/55 backdrop-blur-sm transition-opacity" onclick="closeTicketModal()"></div>
        
        <!-- Modal Wrapper -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 animate-fade-in-up">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100 p-6 space-y-6">
                <!-- Close Button -->
                <button type="button" onclick="closeTicketModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition cursor-pointer">
                    <span class="material-symbols-outlined font-bold text-xl">close</span>
                </button>

                <div class="text-center space-y-2">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-amber-50 text-amber-800 mb-2">
                        <span class="material-symbols-outlined text-3xl font-bold">confirmation_number</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900" id="ticket-modal-title">Ticket Details</h3>
                    <p class="text-xs text-gray-500 font-medium" id="ticket-modal-date">Submitted on -</p>
                </div>

                <div class="space-y-4 text-left">
                    <!-- Related Booking -->
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Related Booking</span>
                        <span class="text-xs font-bold text-gray-800 bg-gray-50 px-2.5 py-1 rounded-md border border-gray-150 inline-block mt-1" id="ticket-modal-booking">-</span>
                    </div>

                    <!-- Status -->
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</span>
                        <div id="ticket-modal-status" class="mt-1"></div>
                    </div>

                    <!-- Description -->
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Your Complaint/Feedback</span>
                        <p class="text-xs text-gray-700 mt-1 bg-gray-50 p-3.5 rounded-lg border border-gray-150 leading-relaxed font-semibold" id="ticket-modal-description">-</p>
                    </div>

                    <!-- Staff Resolution -->
                    <div id="ticket-modal-resolution-section">
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Staff Resolution</span>
                        <p class="text-xs mt-1 p-3.5 rounded-lg border leading-relaxed font-semibold" id="ticket-modal-resolution">-</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="pt-2">
                    <button type="button" onclick="closeTicketModal()"
                            class="w-full bg-gray-900 hover:bg-gray-850 text-white py-3 rounded-xl font-bold text-xs sm:text-sm tracking-wide shadow-md transition cursor-pointer select-none">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- HTML2Canvas Pro Library for Receipt Printing (supports OKLCH colors used in Tailwind v4) -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas-pro@1.5.8/dist/html2canvas-pro.js"></script>

    <!-- Receipt Interaction Logic -->
    <script>
        function showReceiptModal(booking) {
            document.getElementById('receipt-invoice-no').textContent = booking.invoice_no;
            document.getElementById('receipt-date').textContent = booking.date;
            document.getElementById('receipt-guest-name').textContent = booking.guest_name;
            document.getElementById('receipt-room-name').textContent = booking.room_name;
            document.getElementById('receipt-check-in').textContent = formatDateDisplay(booking.check_in);
            document.getElementById('receipt-check-out').textContent = formatDateDisplay(booking.check_out);
            document.getElementById('receipt-nights').textContent = booking.nights + ' night(s)';
            
            var guestsText = booking.guests + ' Guest(s)';
            document.getElementById('receipt-guests').textContent = guestsText;
            document.getElementById('receipt-payment-method').textContent = booking.payment_method || 'Bank Transfer';
            
            var subtotalVal = parseFloat(booking.subtotal);
            var discountVal = parseFloat(booking.discount);
            var taxVal = parseFloat(booking.tax);
            var totalVal = parseFloat(booking.total_price);

            document.getElementById('receipt-subtotal').textContent = 'RP ' + (isNaN(subtotalVal) ? booking.subtotal : subtotalVal.toLocaleString('id-ID'));
            
            // Populating extras in dashboard user receipt modal
            var recBreakfastRow = document.getElementById('receipt-breakfast-row');
            if (booking.include_breakfast && booking.include_breakfast != 0) {
                var bCost = booking.breakfast_cost !== undefined ? booking.breakfast_cost : (50000 * parseInt(booking.guests) * parseInt(booking.nights));
                document.getElementById('receipt-breakfast-amount').textContent = 'RP ' + bCost.toLocaleString('id-ID');
                recBreakfastRow.style.display = '';
            } else {
                recBreakfastRow.style.display = 'none';
            }

            var recExtraBedRow = document.getElementById('receipt-extra-bed-row');
            if (booking.include_extra_bed && booking.include_extra_bed != 0) {
                var ebCost = booking.extra_bed_cost !== undefined ? booking.extra_bed_cost : (150000 * parseInt(booking.nights));
                document.getElementById('receipt-extra-bed-amount').textContent = 'RP ' + ebCost.toLocaleString('id-ID');
                recExtraBedRow.style.display = '';
            } else {
                recExtraBedRow.style.display = 'none';
            }

            var recLateCheckoutRow = document.getElementById('receipt-late-checkout-row');
            if (booking.late_checkout && booking.late_checkout != 0) {
                var lcCost = booking.late_checkout_cost !== undefined ? booking.late_checkout_cost : 100000;
                document.getElementById('receipt-late-checkout-amount').textContent = 'RP ' + lcCost.toLocaleString('id-ID');
                recLateCheckoutRow.style.display = '';
            } else {
                recLateCheckoutRow.style.display = 'none';
            }

            var recOtherAddonsRow = document.getElementById('receipt-other-addons-row');
            if (booking.other_addons_cost && booking.other_addons_cost != 0) {
                var oaCost = parseFloat(booking.other_addons_cost);
                document.getElementById('receipt-other-addons-amount').textContent = 'RP ' + oaCost.toLocaleString('id-ID');
                recOtherAddonsRow.style.display = '';
            } else {
                recOtherAddonsRow.style.display = 'none';
            }

            var recDiscountRow = document.getElementById('receipt-discount-row');
            if (!isNaN(discountVal) && discountVal > 0) {
                document.getElementById('receipt-discount-label').textContent = 'Discount:';
                document.getElementById('receipt-discount-amount').textContent = '-RP ' + discountVal.toLocaleString('id-ID');
                recDiscountRow.style.display = '';
            } else {
                recDiscountRow.style.display = 'none';
            }
            
            document.getElementById('receipt-tax').textContent = 'RP ' + (isNaN(taxVal) ? booking.tax : taxVal.toLocaleString('id-ID'));
            document.getElementById('receipt-total').textContent = 'RP ' + (isNaN(totalVal) ? booking.total_price : totalVal.toLocaleString('id-ID'));
            
            // Update watermark with inline styles (Tailwind JIT won't generate classes set via JS)
            var watermark = document.getElementById('receipt-watermark');
            var statusBadge = document.getElementById('receipt-status-badge');
            var statusStr = booking.status || 'pending';
            watermark.textContent = statusStr.toUpperCase();
            
            if (statusStr === 'checked_in') {
                watermark.style.backgroundColor = '#2563eb';
                statusBadge.style.backgroundColor = '#dbeafe';
                statusBadge.style.color = '#1e40af';
                statusBadge.style.borderColor = '#bfdbfe';
                statusBadge.innerHTML = '🛎️ Checked In';
            } else if (statusStr === 'confirmed') {
                watermark.style.backgroundColor = '#16a34a';
                statusBadge.style.backgroundColor = '#dcfce7';
                statusBadge.style.color = '#166534';
                statusBadge.style.borderColor = '#bbf7d0';
                statusBadge.innerHTML = '✅ Confirmed';
            } else if (statusStr === 'completed') {
                watermark.style.backgroundColor = '#4b5563';
                statusBadge.style.backgroundColor = '#f3f4f6';
                statusBadge.style.color = '#1f2937';
                statusBadge.style.borderColor = '#d1d5db';
                statusBadge.innerHTML = '✔️ Completed';
            } else if (statusStr === 'rejected') {
                watermark.style.backgroundColor = '#e11d48';
                statusBadge.style.backgroundColor = '#ffe4e6';
                statusBadge.style.color = '#9f1239';
                statusBadge.style.borderColor = '#fecdd3';
                statusBadge.innerHTML = '❌ Rejected';
            } else {
                watermark.style.backgroundColor = '#ca8a04';
                statusBadge.style.backgroundColor = '#fef9c3';
                statusBadge.style.color = '#854d0e';
                statusBadge.style.borderColor = '#fde68a';
                statusBadge.innerHTML = '⏳ Pending';
            }

            // Show modal
            document.getElementById('receipt-modal').classList.remove('hidden');
        }

        function closeReceiptModal() {
            document.getElementById('receipt-modal').classList.add('hidden');
        }

        function formatDateDisplay(dateStr) {
            if (!dateStr) return '-';
            var d = new Date(dateStr);
            if (isNaN(d.getTime())) return dateStr;
            var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return ('0' + d.getDate()).slice(-2) + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        }

        function printReceipt() {
            var receiptEl = document.getElementById('receipt-printable');
            if (!receiptEl) {
                alert('Receipt element not found.');
                return;
            }
            // html2canvas-pro may expose as html2canvas or html2canvas.default
            var h2c = (typeof html2canvas !== 'undefined') ? (html2canvas.default || html2canvas) : null;
            if (!h2c) {
                alert('html2canvas library is missing. Please check your internet connection and reload.');
                return;
            }
            h2c(receiptEl, { scale: 2, useCORS: true }).then(function (canvas) {
                var imgData = canvas.toDataURL('image/png');
                var link = document.createElement('a');
                link.href = imgData;
                var now = new Date();
                var timestamp = now.getFullYear() + ('0' + (now.getMonth() + 1)).slice(-2) + ('0' + now.getDate()).slice(-2) + '_' + ('0' + now.getHours()).slice(-2) + ('0' + now.getMinutes()).slice(-2);
                link.download = 'receipt_' + timestamp + '.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }).catch(function (err) {
                console.error('Error generating receipt image:', err);
                alert('Failed to generate receipt image. Error: ' + err.message);
            });
        }

        function showTicketModal(ticket) {
            document.getElementById('ticket-modal-title').textContent = ticket.subject;
            document.getElementById('ticket-modal-date').textContent = 'Submitted on ' + ticket.date;
            document.getElementById('ticket-modal-booking').textContent = ticket.booking_invoice;
            document.getElementById('ticket-modal-description').textContent = ticket.description;
            
            const statusContainer = document.getElementById('ticket-modal-status');
            const resolutionSection = document.getElementById('ticket-modal-resolution-section');
            const resolutionText = document.getElementById('ticket-modal-resolution');
            
            if (ticket.status === 'resolved') {
                statusContainer.innerHTML = `
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Resolved
                    </span>
                `;
                resolutionSection.classList.remove('hidden');
                resolutionText.textContent = ticket.resolution || 'No resolution notes provided.';
                resolutionText.className = "text-xs mt-1 p-3.5 rounded-lg border leading-relaxed font-semibold bg-green-50 border-green-200 text-green-800";
            } else {
                statusContainer.innerHTML = `
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-250">
                        <span class="h-1.5 w-1.5 rounded-full bg-yellow-500"></span> Pending Staff Review
                    </span>
                `;
                resolutionSection.classList.remove('hidden');
                resolutionText.textContent = 'Our support staff is currently reviewing your ticket. Thank you for your patience.';
                resolutionText.className = "text-xs mt-1 p-3.5 rounded-lg border leading-relaxed font-semibold bg-yellow-50 border-yellow-200 text-yellow-800";
            }
            
            document.getElementById('ticket-modal').classList.remove('hidden');
        }

        function closeTicketModal() {
            document.getElementById('ticket-modal').classList.add('hidden');
        }

        function switchTab(tab) {
            var bookingsBtn = document.getElementById('tab-bookings-btn');
            var complaintsBtn = document.getElementById('tab-complaints-btn');
            var bookingsContent = document.getElementById('tab-bookings-content');
            var complaintsContent = document.getElementById('tab-complaints-content');

            if (tab === 'bookings') {
                bookingsBtn.className = "flex-1 py-4 px-6 text-sm font-bold text-center border-b-2 border-amber-600 text-amber-700 focus:outline-none transition cursor-pointer select-none";
                complaintsBtn.className = "flex-1 py-4 px-6 text-sm font-semibold text-center border-b-2 border-transparent text-gray-500 hover:text-amber-700 hover:border-amber-500 focus:outline-none transition cursor-pointer select-none";
                bookingsContent.classList.remove('hidden');
                bookingsContent.classList.add('block');
                complaintsContent.classList.remove('block');
                complaintsContent.classList.add('hidden');
            } else {
                bookingsBtn.className = "flex-1 py-4 px-6 text-sm font-semibold text-center border-b-2 border-transparent text-gray-500 hover:text-amber-700 hover:border-amber-500 focus:outline-none transition cursor-pointer select-none";
                complaintsBtn.className = "flex-1 py-4 px-6 text-sm font-bold text-center border-b-2 border-amber-600 text-amber-700 focus:outline-none transition cursor-pointer select-none";
                bookingsContent.classList.remove('block');
                bookingsContent.classList.add('hidden');
                complaintsContent.classList.remove('hidden');
                complaintsContent.classList.add('block');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if($errors->any() || (session('success') && str_contains(strtolower(session('success')), 'complaint')))
                switchTab('complaints');
            @endif
        });
    </script>
</body>
</html>
