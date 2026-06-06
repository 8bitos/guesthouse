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
                                    
                                    <div class="flex items-center justify-between md:justify-end gap-4 sm:gap-6">
                                        <div class="text-right">
                                            <span class="block text-xs text-gray-400">Total Price</span>
                                            <span class="font-bold text-gray-900">RP{{ number_format($booking['price'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex flex-col sm:flex-row items-end sm:items-center gap-2">
                                            <div>
                                                @if ($booking['status'] === 'confirmed')
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
            
            var guestsText = booking.adults + ' Adult(s)';
            if (booking.children > 0) {
                guestsText += ', ' + booking.children + ' Child(ren)';
            }
            document.getElementById('receipt-guests').textContent = guestsText;
            document.getElementById('receipt-payment-method').textContent = booking.payment_method || 'Bank Transfer';
            
            var subtotalVal = parseFloat(booking.subtotal);
            var discountVal = parseFloat(booking.discount);
            var taxVal = parseFloat(booking.tax);
            var totalVal = parseFloat(booking.total_price);

            document.getElementById('receipt-subtotal').textContent = 'RP ' + (isNaN(subtotalVal) ? booking.subtotal : subtotalVal.toLocaleString('id-ID'));
            
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
            
            if (statusStr === 'confirmed') {
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
    </script>
</body>
</html>
