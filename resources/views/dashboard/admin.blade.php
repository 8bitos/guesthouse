<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Bagus Guest House</title>
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

    <!-- Dashboard Header -->
    <section class="bg-gray-900 text-white py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="bg-amber-600 text-xs font-extrabold uppercase px-2.5 py-1 rounded">Admin Panel</span>
                    <h1 class="text-3xl font-bold">Manager Control Centre</h1>
                </div>
                <p class="text-gray-400 text-sm mt-1">Hello, {{ $user->name }}! Monitor real-time guesthouse performance and reservation requests.</p>
            </div>
            
            <div class="flex gap-3">
                <button onclick="alert('Export functionality coming soon')" class="bg-white/10 hover:bg-white/15 text-white border border-white/20 px-4 py-2 rounded-lg font-semibold text-sm transition">
                    Export Reports
                </button>
                <a href="{{ route('admin.rooms.create') }}" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition inline-block text-center flex items-center justify-center">
                    + Add New Room
                </a>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow w-full space-y-8">
        
        <!-- Stats Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Stat 1: Total Guests -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200/80 flex items-center gap-5 hover:shadow-md transition">
                <div class="p-3.5 rounded-lg bg-amber-50 text-amber-700 shrink-0 flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl">group</span>
                </div>
                <div class="space-y-1">
                    <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Guests</span>
                    <span class="text-2xl font-black text-gray-950">{{ number_format($stats['total_users']) }}</span>
                </div>
            </div>

            <!-- Stat 2: Total Rooms -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200/80 flex items-center gap-5 hover:shadow-md transition">
                <div class="p-3.5 rounded-lg bg-blue-50 text-blue-700 shrink-0 flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl">bed</span>
                </div>
                <div class="space-y-1">
                    <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Rooms</span>
                    <span class="text-2xl font-black text-gray-950">{{ $stats['total_rooms'] }}</span>
                </div>
            </div>

            <!-- Stat 3: Active Bookings -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200/80 flex items-center gap-5 hover:shadow-md transition">
                <div class="p-3.5 rounded-lg bg-green-50 text-green-700 shrink-0 flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl">calendar_month</span>
                </div>
                <div class="space-y-1">
                    <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Active Bookings</span>
                    <span class="text-2xl font-black text-gray-950">{{ $stats['active_bookings'] }}</span>
                </div>
            </div>

            <!-- Stat 4: Revenue -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200/80 flex items-center gap-5 hover:shadow-md transition">
                <div class="p-3.5 rounded-lg bg-emerald-50 text-emerald-700 shrink-0 flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl">payments</span>
                </div>
                <div class="space-y-1">
                    <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Monthly Revenue</span>
                    <span class="text-2xl font-black text-gray-950">RP{{ number_format($stats['revenue'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-xs font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600 text-sm">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Columns: Bookings Table -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200/80 flex justify-between items-center bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-800">Recent Booking Requests</h2>
                    <span class="text-xs text-gray-500 font-medium">Verify guest payment transfers</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100/50 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200/80">
                                <th class="px-6 py-4">Booking ID / Guest</th>
                                <th class="px-6 py-4">Room</th>
                                <th class="px-6 py-4">Stay Dates</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Payment</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200/60 text-sm">
                            @foreach ($recentBookings as $booking)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <!-- ID / Guest -->
                                    <td class="px-6 py-4">
                                        <span class="block font-semibold text-gray-900">{{ $booking['guest'] }}</span>
                                        <span class="text-xs text-amber-700 font-medium">{{ $booking['invoice_no'] }}</span>
                                        @if ($booking['payment_proof'])
                                            <div class="mt-1">
                                                <a href="{{ $booking['payment_proof'] }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200/50 hover:bg-amber-100/70 px-2 py-0.5 rounded transition select-none">
                                                    <span class="material-symbols-outlined text-[12px] leading-none">receipt_long</span>
                                                    <span>View Proof</span>
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                    <!-- Room -->
                                    <td class="px-6 py-4 font-medium text-gray-700">{{ $booking['room'] }}</td>
                                    <!-- Dates -->
                                    <td class="px-6 py-4 text-xs text-gray-600">{{ $booking['dates'] }}</td>
                                    <!-- Status -->
                                    <td class="px-6 py-4">
                                        @if ($booking['status'] === 'confirmed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Confirmed</span>
                                        @elseif ($booking['status'] === 'completed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">Completed</span>
                                        @elseif ($booking['status'] === 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">Rejected</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">Pending</span>
                                        @endif
                                    </td>
                                    <!-- Payment -->
                                    <td class="px-6 py-4">
                                        @if ($booking['payment'] === 'verified')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">Verified</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">Waiting</span>
                                        @endif
                                    </td>
                                    <!-- Actions -->
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if ($booking['status'] === 'pending')
                                                <form action="{{ route('admin.bookings.approve', $booking['id']) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-white rounded-lg shadow transition hover:scale-105 cursor-pointer select-none" title="Approve" style="background-color: #10b981; border: 1px solid #059669; width: 32px; height: 32px; display: inline-flex; justify-content: center; align-items: center;" onmouseover="this.style.backgroundColor='#059669'" onmouseout="this.style.backgroundColor='#10b981'">
                                                        <span class="material-symbols-outlined text-lg font-bold leading-none" style="color: #ffffff;">check</span>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.bookings.reject', $booking['id']) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-white rounded-lg shadow transition hover:scale-105 cursor-pointer select-none" title="Reject" style="background-color: #f43f5e; border: 1px solid #e11d48; width: 32px; height: 32px; display: inline-flex; justify-content: center; align-items: center;" onmouseover="this.style.backgroundColor='#e11d48'" onmouseout="this.style.backgroundColor='#f43f5e'">
                                                        <span class="material-symbols-outlined text-lg font-bold leading-none" style="color: #ffffff;">close</span>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-400 italic font-medium">Processed</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column: Quick Action Cards -->
            <div class="space-y-6">
                <!-- Quick Controls -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6 space-y-4">
                    <h3 class="font-bold text-gray-800 text-base">Quick Guesthouse Controls</h3>
                    
                    <div class="space-y-2">
                        <a href="{{ route('admin.rooms.index') }}" class="w-full text-left px-4 py-3 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-between text-sm transition inline-block">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-500 text-lg leading-none">hotel</span>
                                <span class="font-semibold text-gray-700">Manage Rooms & Villas</span>
                            </div>
                            <span class="text-xs text-gray-400">View All {{ $stats['total_rooms'] }}</span>
                        </a>

                        <a href="{{ route('admin.cms.about') }}" class="w-full text-left px-4 py-3 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-between text-sm transition inline-block">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-500 text-lg leading-none">edit_note</span>
                                <span class="font-semibold text-gray-700">Customize "About Us" Content</span>
                            </div>
                            <span class="text-xs text-gray-400">Manage Texts</span>
                        </a>

                        <a href="{{ route('admin.cms.facilities.index') }}" class="w-full text-left px-4 py-3 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-between text-sm transition inline-block">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-500 text-lg leading-none">pool</span>
                                <span class="font-semibold text-gray-700">Customize "Our Facilities"</span>
                            </div>
                            <span class="text-xs text-gray-400">Configure Offerings</span>
                        </a>

                        <a href="{{ route('admin.cms.gallery.index') }}" class="w-full text-left px-4 py-3 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-between text-sm transition inline-block">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-500 text-lg leading-none">photo_camera</span>
                                <span class="font-semibold text-gray-700">Customize "Photo Gallery"</span>
                            </div>
                            <span class="text-xs text-gray-400">Manage Photos</span>
                        </a>
                        
                        <button onclick="alert('Payment verification panel is under construction!')" class="w-full text-left px-4 py-3 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-between text-sm transition cursor-pointer">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-500 text-lg leading-none">credit_card</span>
                                <span class="font-semibold text-gray-700">Payment Verification Center</span>
                            </div>
                            <span class="text-xs text-orange-600 font-bold bg-orange-50 px-2 py-0.5 rounded">2 Pending</span>
                        </button>
                        
                        <button onclick="alert('Customer complaint boards are under construction!')" class="w-full text-left px-4 py-3 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-between text-sm transition cursor-pointer">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-500 text-lg leading-none">forum</span>
                                <span class="font-semibold text-gray-700">Customer Complaint Logs</span>
                            </div>
                            <span class="text-xs text-gray-400">0 Open</span>
                        </button>
                    </div>
                </div>

                <!-- Database Info Card -->
                <div class="bg-gray-900 text-white rounded-xl p-6 shadow-sm border border-gray-800 space-y-3">
                    <h4 class="font-bold text-sm text-gray-400 uppercase tracking-wider">System Information</h4>
                    <ul class="text-xs space-y-2 text-gray-300">
                        <li class="flex justify-between">
                            <span>Database Driver:</span>
                            <strong class="text-white">SQLite (MySQL Local Dev)</strong>
                        </li>
                        <li class="flex justify-between">
                            <span>Laravel Framework:</span>
                            <strong class="text-white">v13.x</strong>
                        </li>
                        <li class="flex justify-between">
                            <span>Tailwind CSS:</span>
                            <strong class="text-white">v4.x CSS-First</strong>
                        </li>
                        <li class="flex justify-between">
                            <span>Host Environment:</span>
                            <strong class="text-white">Windows Server</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    @include('components.footer')
</body>
</html>
