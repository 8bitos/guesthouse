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
    <section class="bg-gray-900 text-white py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="bg-amber-600 text-[10px] font-extrabold uppercase px-2 py-0.5 rounded">Admin Panel</span>
                    <h1 class="text-2xl font-bold tracking-tight">Manager Control Centre</h1>
                </div>
                <p class="text-gray-400 text-xs mt-1">Hello, {{ $user->name }}! Monitor real-time guesthouse performance and reservation requests.</p>
            </div>
            
            <div class="flex gap-2.5">
                <button onclick="openExportModal()" class="bg-white/10 hover:bg-white/15 text-white border border-white/20 px-3.5 py-1.5 rounded-lg font-semibold text-xs transition inline-flex items-center gap-1.5 justify-center cursor-pointer">
                    <span class="material-symbols-outlined text-[14px]">download</span>
                    Export Reports
                </button>
                <a href="{{ route('admin.rooms.create') }}" class="bg-amber-600 hover:bg-amber-700 text-white px-3.5 py-1.5 rounded-lg font-semibold text-xs transition inline-block text-center flex items-center justify-center">
                    + Add New Room
                </a>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-grow w-full space-y-6">
        
        <!-- Stats Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Stat 1: Total Guests -->
            <div class="bg-white rounded-xl p-4.5 shadow-sm border border-gray-200/80 flex items-center gap-4 hover:shadow-md transition">
                <div class="p-2.5 rounded-lg bg-amber-50 text-amber-700 shrink-0 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">group</span>
                </div>
                <div class="space-y-0.5">
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Guests</span>
                    <span class="text-xl font-extrabold text-gray-955">{{ number_format($stats['total_users']) }}</span>
                </div>
            </div>

            <!-- Stat 2: Total Rooms -->
            <div class="bg-white rounded-xl p-4.5 shadow-sm border border-gray-200/80 flex items-center gap-4 hover:shadow-md transition">
                <div class="p-2.5 rounded-lg bg-blue-50 text-blue-700 shrink-0 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">bed</span>
                </div>
                <div class="space-y-0.5">
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Rooms</span>
                    <span class="text-xl font-extrabold text-gray-955">{{ $stats['total_rooms'] }}</span>
                </div>
            </div>

            <!-- Stat 3: Active Bookings -->
            <div class="bg-white rounded-xl p-4.5 shadow-sm border border-gray-200/80 flex items-center gap-4 hover:shadow-md transition">
                <div class="p-2.5 rounded-lg bg-green-50 text-green-700 shrink-0 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">calendar_month</span>
                </div>
                <div class="space-y-0.5">
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Active Bookings</span>
                    <span class="text-xl font-extrabold text-gray-955">{{ $stats['active_bookings'] }}</span>
                </div>
            </div>

            <!-- Stat 4: Revenue -->
            <div class="bg-white rounded-xl p-4.5 shadow-sm border border-gray-200/80 flex items-center gap-4 hover:shadow-md transition">
                <div class="p-2.5 rounded-lg bg-emerald-50 text-emerald-700 shrink-0 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">payments</span>
                </div>
                <div class="space-y-0.5">
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Monthly Revenue</span>
                    <span class="text-xl font-extrabold text-gray-955">RP{{ number_format($stats['revenue'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Success/Alert Message -->
        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-xs font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600 text-sm">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Tab Navigation -->
        <div class="flex border-b border-gray-200/80 gap-1.5 overflow-x-auto pb-px">
            <button onclick="switchTab('requests')" id="tab-requests" class="py-2.5 px-4.5 text-xs font-bold uppercase tracking-wider border-b-2 border-amber-600 text-amber-700 outline-none transition duration-200 flex items-center gap-2 shrink-0 cursor-pointer select-none">
                <span class="material-symbols-outlined text-base">receipt_long</span>
                Booking Requests
                @php
                    $pendingCount = collect($recentBookings)->where('status', 'pending')->count();
                @endphp
                @if ($pendingCount > 0)
                    <span class="bg-amber-600 text-white text-[9px] font-extrabold px-1.5 py-0.5 rounded-full leading-none">{{ $pendingCount }}</span>
                @endif
            </button>
            <button onclick="switchTab('analytics')" id="tab-analytics" class="py-2.5 px-4.5 text-xs font-bold uppercase tracking-wider border-b-2 border-transparent text-gray-500 hover:text-gray-700 outline-none transition duration-200 flex items-center gap-2 shrink-0 cursor-pointer select-none">
                <span class="material-symbols-outlined text-base">monitoring</span>
                Analytics & Trends
            </button>
            <button onclick="switchTab('rooms-status')" id="tab-rooms-status" class="py-2.5 px-4.5 text-xs font-bold uppercase tracking-wider border-b-2 border-transparent text-gray-500 hover:text-gray-700 outline-none transition duration-200 flex items-center gap-2 shrink-0 cursor-pointer select-none">
                <span class="material-symbols-outlined text-base">meeting_room</span>
                Room Status
            </button>
            <button onclick="switchTab('management')" id="tab-management" class="py-2.5 px-4.5 text-xs font-bold uppercase tracking-wider border-b-2 border-transparent text-gray-500 hover:text-gray-700 outline-none transition duration-200 flex items-center gap-2 shrink-0 cursor-pointer select-none">
                <span class="material-symbols-outlined text-base">settings_accessibility</span>
                Management & CMS
            </button>
        </div>

        <!-- Tab Content: Analytics & Trends -->
        <div id="content-analytics" class="tab-content hidden space-y-6">
            <!-- Trends & Occupancy Analytics Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Occupancy & Revenue Trends (lg:col-span-2) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Occupancy & Revenue Trends -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-200/80 flex justify-between items-center bg-gray-50 cursor-pointer select-none" onclick="toggleCollapse('trends-collapse-target', 'trends-collapse-icon')">
                        <div class="flex items-center gap-4 flex-wrap">
                            <div class="flex items-center gap-2">
                                <h2 class="text-sm font-bold text-gray-800">{{ $trendsTitle }}</h2>
                                <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider hidden sm:inline">Real-time room occupancy patterns</span>
                            </div>
                            <div onclick="event.stopPropagation()" class="inline-flex items-center">
                                <select onchange="window.location.search = '?trend_filter=' + this.value" class="bg-white border border-gray-200 rounded px-2.5 py-1 text-xs font-semibold text-gray-700 outline-none focus:border-amber-700 focus:ring-1 focus:ring-amber-700 cursor-pointer shadow-sm">
                                    <option value="today" {{ $trendFilter === 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="7days" {{ $trendFilter === '7days' ? 'selected' : '' }}>7 Days</option>
                                    <option value="1month" {{ $trendFilter === '1month' ? 'selected' : '' }}>1 Month</option>
                                    <option value="6months" {{ $trendFilter === '6months' ? 'selected' : '' }}>6 Months</option>
                                    <option value="1year" {{ $trendFilter === '1year' ? 'selected' : '' }}>1 Year</option>
                                </select>
                            </div>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-gray-600 transition flex items-center justify-center">
                            <span id="trends-collapse-icon" class="material-symbols-outlined text-lg font-bold transition-transform duration-300">keyboard_arrow_up</span>
                        </button>
                    </div>
                    <div id="trends-collapse-target" class="transition-all duration-300 overflow-hidden max-h-[800px] opacity-100">
                        <div class="p-5">
                            @if (count($monthlyTrends) > 0)
                                <div class="space-y-6">
                                    <!-- Chart Legend -->
                                    <div class="flex items-center gap-4 text-[10px] font-extrabold uppercase tracking-wider text-gray-400">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-3 h-1.5 bg-indigo-500 rounded-sm"></span>
                                            <span>Reservations</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-3 h-1.5 bg-amber-500 rounded-sm"></span>
                                            <span>Revenue</span>
                                        </div>
                                    </div>

                                    <!-- Visual Line Chart representation -->
                                    <div class="pb-4">
                                        @php
                                            $N = count($monthlyTrends);
                                            $chartWidth = 1000;
                                            $chartHeight = 160;
                                            $paddingX = 40;
                                            $paddingY = 20;
                                            
                                            $maxRevenue = collect($monthlyTrends)->max('revenue') ?: 1;
                                            $maxBookings = collect($monthlyTrends)->max('bookings_count') ?: 1;
                                            
                                            $revenuePoints = [];
                                            $bookingsPoints = [];
                                            $baseline = $chartHeight - $paddingY;
                                            
                                            for ($i = 0; $i < $N; $i++) {
                                                $trend = $monthlyTrends[$i];
                                                
                                                $x = ($N > 1) 
                                                    ? $paddingX + ($i * (($chartWidth - (2 * $paddingX)) / ($N - 1))) 
                                                    : $chartWidth / 2;
                                                    
                                                $yRevenue = $chartHeight - $paddingY - (($trend['revenue'] / $maxRevenue) * ($chartHeight - (2 * $paddingY)));
                                                $yBookings = $chartHeight - $paddingY - (($trend['bookings_count'] / $maxBookings) * ($chartHeight - (2 * $paddingY)));
                                                
                                                $revenuePoints[] = ['x' => $x, 'y' => $yRevenue];
                                                $bookingsPoints[] = ['x' => $x, 'y' => $yBookings];
                                            }
                                            
                                            $revenueLinePath = '';
                                            $bookingsLinePath = '';
                                            $revenueAreaPath = '';
                                            $bookingsAreaPath = '';
                                            
                                            if ($N > 0) {
                                                $revenueLinePath = 'M ' . $revenuePoints[0]['x'] . ' ' . $revenuePoints[0]['y'];
                                                $bookingsLinePath = 'M ' . $bookingsPoints[0]['x'] . ' ' . $bookingsPoints[0]['y'];
                                                
                                                $revenueAreaPath = 'M ' . $revenuePoints[0]['x'] . ' ' . $baseline . ' L ' . $revenuePoints[0]['x'] . ' ' . $revenuePoints[0]['y'];
                                                $bookingsAreaPath = 'M ' . $bookingsPoints[0]['x'] . ' ' . $baseline . ' L ' . $bookingsPoints[0]['x'] . ' ' . $bookingsPoints[0]['y'];
                                                
                                                for ($i = 1; $i < $N; $i++) {
                                                    $revenueLinePath .= ' L ' . $revenuePoints[$i]['x'] . ' ' . $revenuePoints[$i]['y'];
                                                    $bookingsLinePath .= ' L ' . $bookingsPoints[$i]['x'] . ' ' . $bookingsPoints[$i]['y'];
                                                    
                                                    $revenueAreaPath .= ' L ' . $revenuePoints[$i]['x'] . ' ' . $revenuePoints[$i]['y'];
                                                    $bookingsAreaPath .= ' L ' . $bookingsPoints[$i]['x'] . ' ' . $bookingsPoints[$i]['y'];
                                                }
                                                
                                                $revenueAreaPath .= ' L ' . $revenuePoints[$N-1]['x'] . ' ' . $baseline . ' Z';
                                                $bookingsAreaPath .= ' L ' . $bookingsPoints[$N-1]['x'] . ' ' . $baseline . ' Z';
                                            }
                                        @endphp
                                        <div class="relative w-full" style="height: {{ $chartHeight + 30 }}px;">
                                            <!-- SVG Graph -->
                                            <svg class="absolute inset-0 pointer-events-none w-full h-full" viewBox="0 0 1000 160" preserveAspectRatio="none">
                                                <defs>
                                                    <!-- Revenue Gradient -->
                                                    <linearGradient id="revenueAreaGrad" x1="0" y1="0" x2="0" y2="1">
                                                        <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.15" />
                                                        <stop offset="100%" stop-color="#f59e0b" stop-opacity="0.0" />
                                                    </linearGradient>
                                                    <!-- Bookings Gradient -->
                                                    <linearGradient id="bookingsAreaGrad" x1="0" y1="0" x2="0" y2="1">
                                                        <stop offset="0%" stop-color="#6366f1" stop-opacity="0.15" />
                                                        <stop offset="100%" stop-color="#6366f1" stop-opacity="0.0" />
                                                    </linearGradient>
                                                </defs>

                                                <!-- Grid Lines (Horizontal Background Lines) -->
                                                <line x1="{{ $paddingX }}" y1="{{ $paddingY }}" x2="{{ $chartWidth - $paddingX }}" y2="{{ $paddingY }}" stroke="#f3f4f6" stroke-width="1" />
                                                <line x1="{{ $paddingX }}" y1="{{ ($chartHeight / 2) }}" x2="{{ $chartWidth - $paddingX }}" y2="{{ ($chartHeight / 2) }}" stroke="#f3f4f6" stroke-width="1" />
                                                <line x1="{{ $paddingX }}" y1="{{ $baseline }}" x2="{{ $chartWidth - $paddingX }}" y2="{{ $baseline }}" stroke="#e5e7eb" stroke-width="1" />

                                                @if ($N > 0)
                                                    <!-- Area Fills -->
                                                    <path d="{{ $bookingsAreaPath }}" fill="url(#bookingsAreaGrad)" />
                                                    <path d="{{ $revenueAreaPath }}" fill="url(#revenueAreaGrad)" />

                                                    <!-- Lines -->
                                                    <path d="{{ $bookingsLinePath }}" fill="none" stroke="#6366f1" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="{{ $revenueLinePath }}" fill="none" stroke="#f59e0b" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

                                                    <!-- Circles / Points -->
                                                    @foreach ($bookingsPoints as $index => $pt)
                                                        <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="4" fill="#6366f1" stroke="#ffffff" stroke-width="2" />
                                                    @endforeach
                                                    @foreach ($revenuePoints as $index => $pt)
                                                        <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="4" fill="#f59e0b" stroke="#ffffff" stroke-width="2" />
                                                    @endforeach
                                                @endif
                                            </svg>

                                            <!-- Interactive Hover Hotspots & Tooltips -->
                                            <div class="absolute inset-0 w-full h-full">
                                                @foreach ($monthlyTrends as $index => $trend)
                                                    @php
                                                        $pt = $bookingsPoints[$index];
                                                        $colWidth = ($N > 1) ? ($chartWidth - (2 * $paddingX)) / ($N - 1) : $chartWidth;
                                                        $left = ($N > 1) ? $pt['x'] - ($colWidth / 2) : 0;
                                                        
                                                        $percentLeft = ($left / $chartWidth) * 100;
                                                        $percentColWidth = ($colWidth / $chartWidth) * 100;
                                                    @endphp
                                                    <div class="absolute group cursor-pointer" style="left: {{ $percentLeft }}%; width: {{ $percentColWidth }}%; top: 0; bottom: 0;">
                                                        <!-- Vertical hover line indicator -->
                                                        <div class="absolute inset-y-0 w-px bg-gray-200 opacity-0 group-hover:opacity-100 transition pointer-events-none left-1/2 -translate-x-1/2"></div>
                                                        
                                                        <!-- Tooltip -->
                                                        <div class="absolute top-2 bg-gray-900 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded opacity-0 group-hover:opacity-100 transition duration-200 pointer-events-none text-center whitespace-nowrap z-10 shadow-lg leading-relaxed left-1/2 -translate-x-1/2">
                                                            <span class="block text-gray-300 font-bold uppercase text-[9px] mb-0.5">{{ $trend['label'] }}</span>
                                                            <span class="block text-indigo-300 font-extrabold">{{ $trend['bookings_count'] }} bookings</span>
                                                            <span class="block text-amber-400 font-extrabold">RP{{ number_format($trend['revenue'], 0, ',', '.') }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <!-- X-Axis Labels -->
                                            <div class="absolute bottom-0 left-0 right-0 h-6">
                                                @foreach ($monthlyTrends as $index => $trend)
                                                    @php
                                                        $pt = $bookingsPoints[$index];
                                                        $percentX = ($pt['x'] / $chartWidth) * 100;
                                                    @endphp
                                                    <span class="absolute text-[9px] font-bold text-gray-400 uppercase tracking-wider text-center block w-20 -ml-10" style="left: {{ $percentX }}%;">
                                                        {{ $trend['label'] }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Table Representation -->
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-left border-collapse text-xs">
                                            <thead>
                                                <tr class="text-gray-400 font-bold uppercase border-b border-gray-200/80 pb-2">
                                                    <th class="py-2 px-2">{{ $trendsColumnHeader }}</th>
                                                    <th class="py-2 px-2 text-center">Reservations Completed</th>
                                                    <th class="py-2 px-2 text-right">Revenue</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-150 text-gray-700">
                                                @foreach ($monthlyTrends as $trend)
                                                    <tr class="hover:bg-gray-50/50">
                                                        <td class="py-2.5 px-2 font-bold text-gray-900">{{ $trend['label'] }}</td>
                                                        <td class="py-2.5 px-2 text-center text-indigo-600 font-bold">{{ $trend['bookings_count'] }} bookings</td>
                                                        <td class="py-2.5 px-2 text-right font-extrabold text-gray-955">RP{{ number_format($trend['revenue'], 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="p-8 text-center text-gray-500 bg-gray-50 rounded-xl border border-gray-150">
                                    <span class="material-symbols-outlined text-4xl text-gray-400">analytics</span>
                                    <p class="text-xs mt-2 font-semibold">No booking trends data available yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Guest Origin + Popular Rooms (lg:col-span-1) -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Guest Origin Trends -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-200/80 flex justify-between items-center bg-gray-50 cursor-pointer select-none" onclick="toggleCollapse('origin-collapse-target', 'origin-collapse-icon')">
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-bold text-gray-800">Guest Origin Trends</h2>
                            <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider hidden sm:inline">Top guest origins</span>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-gray-600 transition flex items-center justify-center">
                            <span id="origin-collapse-icon" class="material-symbols-outlined text-lg font-bold transition-transform duration-300">keyboard_arrow_up</span>
                        </button>
                    </div>
                    <div id="origin-collapse-target" class="transition-all duration-300 overflow-hidden max-h-[800px] opacity-100">
                        <div class="p-5">
                            @if (count($guestOrigins) > 0)
                                <div class="space-y-3">
                                    @php
                                        $totalOriginBookings = collect($guestOrigins)->sum('count') ?: 1;
                                        $maxOriginCount = collect($guestOrigins)->max('count') ?: 1;
                                    @endphp
                                    @foreach ($guestOrigins as $index => $origin)
                                        @php
                                            $percentage = ($origin['count'] / $totalOriginBookings) * 100;
                                            $barWidth = ($origin['count'] / $maxOriginCount) * 100;
                                        @endphp
                                        <div class="p-2.5 bg-gray-50 rounded-lg border border-gray-150/30 hover:bg-gray-100/50 transition space-y-2">
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <!-- Rank number badge -->
                                                    <span class="w-5 h-5 flex items-center justify-center rounded-full text-[10px] font-bold @if($index == 0) bg-amber-100 text-amber-800 @elseif($index == 1) bg-slate-100 text-slate-800 @else bg-gray-100 text-gray-500 @endif shrink-0">
                                                        {{ $index + 1 }}
                                                    </span>
                                                    <span class="font-semibold text-gray-900 text-xs truncate" title="{{ $origin['country'] }}">{{ $origin['country'] }}</span>
                                                </div>
                                                <div class="text-right shrink-0">
                                                    <span class="inline-block bg-amber-50 text-amber-800 border border-amber-200/30 px-2 py-0.5 rounded text-[10px] font-bold">
                                                        {{ $origin['count'] }} bookings ({{ round($percentage) }}%)
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="w-full bg-gray-200/60 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-gradient-to-r from-amber-600 to-amber-500 h-full rounded-full transition-all duration-500" style="width: {{ $barWidth }}%;"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-8 text-center text-gray-500 bg-gray-50 rounded-xl border border-gray-150">
                                    <span class="material-symbols-outlined text-4xl text-gray-400">public</span>
                                    <p class="text-xs mt-2 font-semibold">No guest origin data available yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Most Popular Rooms -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-200/80 flex justify-between items-center bg-gray-50 cursor-pointer select-none" onclick="toggleCollapse('popular-collapse-target', 'popular-collapse-icon')">
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-bold text-gray-800">Most Popular Rooms</h2>
                            <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider hidden sm:inline">Favorite accommodations</span>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-gray-600 transition flex items-center justify-center">
                            <span id="popular-collapse-icon" class="material-symbols-outlined text-lg font-bold transition-transform duration-300">keyboard_arrow_up</span>
                        </button>
                    </div>
                    <div id="popular-collapse-target" class="transition-all duration-300 overflow-hidden max-h-[800px] opacity-100">
                        <div class="p-5">
                            @if (count($favoriteRooms) > 0)
                                <div class="space-y-3">
                                    @foreach ($favoriteRooms as $index => $room)
                                        <div class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg border border-gray-150/30 hover:bg-gray-100/50 transition">
                                            <div class="flex items-center gap-2.5">
                                                <!-- Rank number badge -->
                                                <span class="w-5 h-5 flex items-center justify-center rounded-full text-[10px] font-bold @if($index == 0) bg-amber-100 text-amber-800 @elseif($index == 1) bg-slate-100 text-slate-800 @else bg-gray-100 text-gray-500 @endif shrink-0">
                                                    {{ $index + 1 }}
                                                </span>
                                                <div class="min-w-0">
                                                    <span class="block font-semibold text-gray-900 text-xs truncate">{{ $room->name }}</span>
                                                    <span class="text-[9px] text-gray-400 font-medium tracking-wider uppercase truncate">{{ $room->type }} &bull; RP{{ number_format($room->price, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <span class="inline-block bg-amber-50 text-amber-800 border border-amber-200/30 px-2 py-0.5 rounded text-[10px] font-bold">
                                                    {{ $room->bookings_count }} bookings
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-8 text-center text-gray-500 bg-gray-50 rounded-xl border border-gray-150">
                                    <span class="material-symbols-outlined text-4xl text-gray-400">grade</span>
                                    <p class="text-xs mt-2 font-semibold">No popular rooms data available yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div> <!-- Close Analytics & Trends Tab Content -->

        <!-- Tab Content: Booking Requests -->
        <div id="content-requests" class="tab-content space-y-6">
            <!-- Bookings Table (Full Width) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden w-full">
                <div class="px-5 py-3.5 border-b border-gray-200/80 flex justify-between items-center bg-gray-50">
                    <h2 class="text-sm font-bold text-gray-800">Recent Booking Requests</h2>
                    <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">Verify guest payment transfers</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100/50 text-[10px] font-extrabold text-gray-500 uppercase tracking-wider border-b border-gray-200/80">
                                <th class="px-4 py-3">Booking ID / Guest</th>
                                <th class="px-4 py-3">Room</th>
                                <th class="px-4 py-3">Stay Dates</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Payment</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200/60 text-xs">
                            @foreach ($recentBookings as $booking)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <!-- ID / Guest -->
                                    <td class="px-4 py-3">
                                        <span class="block font-bold text-gray-900">{{ $booking['guest'] }}</span>
                                        <span class="text-[10px] text-amber-700 font-semibold">{{ $booking['invoice_no'] }}</span>
                                        @if ($booking['payment_proof'])
                                            <div class="mt-1">
                                                <a href="{{ $booking['payment_proof'] }}" target="_blank" class="inline-flex items-center gap-1 text-[9px] font-bold text-amber-700 bg-amber-50 border border-amber-200/50 hover:bg-amber-100/70 px-2 py-0.5 rounded transition select-none">
                                                    <span class="material-symbols-outlined text-[10px] leading-none">receipt_long</span>
                                                    <span>View Proof</span>
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                    <!-- Room -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-semibold text-gray-700">{{ $booking['room'] }}</span>
                                            @if ($booking['room'] !== 'Deleted Room')
                                                @if (($booking['room_status'] ?? '') === 'dipesan')
                                                    <span class="bg-blue-50 text-blue-700 border border-blue-150 text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded flex items-center gap-1">
                                                        <span class="w-1 h-1 rounded-full bg-blue-600 animate-pulse"></span>
                                                        <span>Occupied</span>
                                                    </span>
                                                @elseif (($booking['room_status'] ?? '') === 'tersedia')
                                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-150 text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded flex items-center gap-1">
                                                        <span class="w-1 h-1 rounded-full bg-emerald-600"></span>
                                                        <span>Vacant</span>
                                                    </span>
                                                @else
                                                    <span class="bg-red-50 text-red-700 border border-red-150 text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded flex items-center gap-1">
                                                        <span class="w-1 h-1 rounded-full bg-red-600"></span>
                                                        <span>Maintenance</span>
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                        @if ($booking['include_breakfast'] || $booking['include_extra_bed'] || $booking['late_checkout'] || (isset($booking['addons']) && is_array($booking['addons']) && count($booking['addons']) > 0))
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @if (isset($booking['addons']) && is_array($booking['addons']) && count($booking['addons']) > 0)
                                                    @foreach ($booking['addons'] as $addonName)
                                                        <span class="bg-blue-50 text-blue-700 border border-blue-100 text-[8px] font-extrabold uppercase px-1.5 py-0.5 rounded">{{ $addonName }}</span>
                                                    @endforeach
                                                @else
                                                    @if ($booking['include_breakfast'])
                                                        <span class="bg-blue-50 text-blue-700 border border-blue-100 text-[8px] font-extrabold uppercase px-1 py-0.5 rounded">Breakfast</span>
                                                    @endif
                                                    @if ($booking['include_extra_bed'])
                                                        <span class="bg-indigo-50 text-indigo-700 border border-indigo-100 text-[8px] font-extrabold uppercase px-1 py-0.5 rounded">Extra Bed</span>
                                                    @endif
                                                    @if ($booking['late_checkout'])
                                                        <span class="bg-purple-50 text-purple-700 border border-purple-100 text-[8px] font-extrabold uppercase px-1 py-0.5 rounded">Late CO</span>
                                                    @endif
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <!-- Dates -->
                                    <td class="px-4 py-3 text-[11px] text-gray-650">{{ $booking['dates'] }}</td>
                                    <!-- Status -->
                                    <td class="px-4 py-3">
                                        @if ($booking['status'] === 'checked_in')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-blue-50 text-blue-700 border border-blue-200 animate-pulse">Checked In</span>
                                        @elseif ($booking['status'] === 'confirmed')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-green-50 text-green-700 border border-green-200">Confirmed</span>
                                        @elseif ($booking['status'] === 'completed')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-gray-100 text-gray-700 border border-gray-200">Completed</span>
                                        @elseif ($booking['status'] === 'rejected')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-red-100 text-red-805 border border-red-200">Rejected</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-yellow-50 text-yellow-700 border border-yellow-200">Pending</span>
                                        @endif
                                    </td>
                                    <!-- Payment -->
                                    <td class="px-4 py-3">
                                        @if ($booking['payment'] === 'verified')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">Verified</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-orange-50 text-orange-700 border border-orange-200">Waiting</span>
                                        @endif
                                    </td>
                                    <!-- Actions -->
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if ($booking['status'] === 'pending')
                                                <form action="{{ route('admin.bookings.approve', $booking['id']) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" data-loading-text="icon-only" class="text-white rounded shadow transition hover:scale-105 cursor-pointer select-none" title="Approve" style="background-color: #10b981; border: 1px solid #059669; width: 26px; height: 26px; display: inline-flex; justify-content: center; align-items: center;" onmouseover="this.style.backgroundColor='#059669'" onmouseout="this.style.backgroundColor='#10b981'">
                                                        <span class="material-symbols-outlined text-sm font-bold leading-none" style="color: #ffffff;">check</span>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.bookings.reject', $booking['id']) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" data-loading-text="icon-only" class="text-white rounded shadow transition hover:scale-105 cursor-pointer select-none" title="Reject" style="background-color: #f43f5e; border: 1px solid #e11d48; width: 26px; height: 26px; display: inline-flex; justify-content: center; align-items: center;" onmouseover="this.style.backgroundColor='#e11d48'" onmouseout="this.style.backgroundColor='#f43f5e'">
                                                        <span class="material-symbols-outlined text-sm font-bold leading-none" style="color: #ffffff;">close</span>
                                                    </button>
                                                </form>
                                            @elseif ($booking['status'] === 'confirmed')
                                                <!-- Check In button -->
                                                <form action="{{ route('admin.bookings.checkin', $booking['id']) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" data-loading-text="Checking In..." class="text-white bg-emerald-600 hover:bg-emerald-700 font-bold px-2.5 py-1 rounded transition text-[10px] inline-flex items-center gap-0.5 cursor-pointer select-none shadow-sm" title="Check In">
                                                        <span class="material-symbols-outlined text-xs leading-none">login</span>
                                                        <span>Check In</span>
                                                    </button>
                                                </form>
                                            @elseif ($booking['status'] === 'checked_in')
                                                <!-- Check Out button -->
                                                <form action="{{ route('admin.bookings.checkout', $booking['id']) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" data-loading-text="Checking Out..." class="text-white bg-blue-600 hover:bg-blue-700 font-bold px-2.5 py-1 rounded transition text-[10px] inline-flex items-center gap-0.5 cursor-pointer select-none shadow-sm" title="Check Out">
                                                        <span class="material-symbols-outlined text-xs leading-none">logout</span>
                                                        <span>Check Out</span>
                                                    </button>
                                                </form>
                                            @elseif ($booking['status'] === 'completed')
                                                <span class="text-[10px] text-gray-400 font-bold uppercase">Completed</span>
                                            @elseif ($booking['status'] === 'cancelled')
                                                <span class="text-[10px] text-rose-500 font-bold uppercase">Cancelled</span>
                                            @else
                                                <span class="text-[10px] text-red-500 font-bold uppercase">Rejected</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div> <!-- Close Bookings Table container -->
        </div> <!-- Close Booking Requests Tab Content -->

        <!-- Tab Content: Room Status -->
        <div id="content-rooms-status" class="tab-content hidden space-y-6">
            <!-- Filter & Toolbar -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-4.5 rounded-xl border border-gray-200/80 shadow-sm">
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Room Status Grid</h2>
                    <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider mt-0.5">Real-time overview of vacant, occupied, and maintenance rooms</p>
                </div>
                
                <!-- Filter buttons -->
                <div class="flex flex-wrap gap-1.5">
                    <button onclick="filterRooms('all')" id="btn-filter-all" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition bg-amber-600 text-white shadow-sm border border-amber-700/10 cursor-pointer">
                        All ({{ count($rooms) }})
                    </button>
                    <button onclick="filterRooms('tersedia')" id="btn-filter-tersedia" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition bg-gray-50 text-gray-655 hover:bg-gray-100 hover:text-gray-800 border border-gray-200/60 cursor-pointer">
                        Vacant / Tidak Laku ({{ $rooms->where('status', 'tersedia')->count() }})
                    </button>
                    <button onclick="filterRooms('dipesan')" id="btn-filter-dipesan" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition bg-gray-50 text-gray-655 hover:bg-gray-100 hover:text-gray-800 border border-gray-200/60 cursor-pointer">
                        Occupied / Laku ({{ $rooms->where('status', 'dipesan')->count() }})
                    </button>
                    <button onclick="filterRooms('pemeliharaan')" id="btn-filter-pemeliharaan" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition bg-gray-50 text-gray-655 hover:bg-gray-100 hover:text-gray-800 border border-gray-200/60 cursor-pointer">
                        Maintenance ({{ $rooms->where('status', 'pemeliharaan')->count() }})
                    </button>
                </div>
            </div>

            <!-- Room Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" id="rooms-status-grid">
                @foreach ($rooms as $room)
                    @php
                        // Get active booking (checked_in or confirmed)
                        $activeBkg = $room->bookings->whereIn('status', ['checked_in', 'confirmed'])->sortByDesc('created_at')->first();
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden hover:shadow-md transition duration-300 flex flex-col justify-between room-status-card" data-status="{{ $room->status }}">
                        <!-- Image Container -->
                        <div class="relative h-40 bg-gray-100 shrink-0">
                            @if ($room->image)
                                <img src="{{ asset('storage/' . $room->image) }}" class="w-full h-full object-cover" alt="{{ $room->name }}">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 gap-1 bg-amber-50/30">
                                    <span class="material-symbols-outlined text-3xl">bed</span>
                                    <span class="text-[10px] font-semibold uppercase">No Photo Available</span>
                                </div>
                            @endif
                            
                            <!-- Badges -->
                            <div class="absolute top-3 left-3">
                                <span class="bg-gray-900/70 backdrop-blur-xs text-white text-[9px] font-extrabold uppercase px-2 py-0.5 rounded shadow-sm">
                                    {{ $room->type }}
                                </span>
                            </div>

                            <div class="absolute top-3 right-3">
                                @if ($room->status === 'dipesan')
                                    <span class="bg-rose-600 text-white text-[9px] font-extrabold uppercase px-2.5 py-0.5 rounded-full shadow-sm flex items-center gap-1.5 animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                        <span>OCCUPIED (LAKU)</span>
                                    </span>
                                @elseif ($room->status === 'tersedia')
                                    <span class="bg-emerald-600 text-white text-[9px] font-extrabold uppercase px-2.5 py-0.5 rounded-full shadow-sm flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                        <span>VACANT (TIDAK LAKU)</span>
                                    </span>
                                @else
                                    <span class="bg-amber-600 text-white text-[9px] font-extrabold uppercase px-2.5 py-0.5 rounded-full shadow-sm flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                        <span>MAINTENANCE</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 flex-grow flex flex-col justify-between space-y-4">
                            <div class="space-y-1">
                                <h3 class="text-sm font-black text-gray-900 tracking-tight">{{ $room->name }}</h3>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">
                                    {{ $room->size }} m² &bull; {{ $room->capacity }} Guests Max
                                </p>
                            </div>

                            <!-- Booking details if occupied/laku -->
                            @if ($room->status === 'dipesan' && $activeBkg)
                                <div class="bg-rose-50 border border-rose-100 rounded-lg p-2.5 space-y-1">
                                    <span class="block text-[8px] font-extrabold text-rose-800 uppercase tracking-wider">Current Occupant</span>
                                    <div class="flex items-center justify-between text-[10px] text-gray-750 font-semibold gap-2">
                                        <span class="truncate font-bold text-gray-900">{{ $activeBkg->guest_name }}</span>
                                        <span class="shrink-0 text-amber-700 bg-amber-50 border border-amber-100 px-1 py-0.2 rounded text-[8px] font-bold">{{ $activeBkg->invoice_no }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[9px] text-gray-500 font-medium pt-1 border-t border-rose-200/40">
                                        <span>Stay Dates:</span>
                                        <span class="font-bold text-rose-700">
                                            {{ date('d M', strtotime($activeBkg->check_in)) }} - {{ date('d M Y', strtotime($activeBkg->check_out)) }}
                                        </span>
                                    </div>
                                </div>
                            @elseif ($room->status === 'dipesan')
                                <div class="bg-rose-50 border border-rose-100 rounded-lg p-2.5 flex items-center gap-1.5 text-[10px] text-rose-750 font-semibold">
                                    <span class="material-symbols-outlined text-xs leading-none">info</span>
                                    <span>Occupied (Active session in progress)</span>
                                </div>
                            @elseif ($room->status === 'tersedia')
                                <div class="bg-emerald-50/50 border border-emerald-100 rounded-lg p-2.5 flex items-center gap-1.5 text-[10px] text-emerald-750 font-semibold">
                                    <span class="material-symbols-outlined text-xs leading-none">check_circle</span>
                                    <span>Ready for new bookings</span>
                                </div>
                            @else
                                <div class="bg-amber-50/50 border border-amber-100 rounded-lg p-2.5 flex items-center gap-1.5 text-[10px] text-amber-750 font-semibold">
                                    <span class="material-symbols-outlined text-xs leading-none">construction</span>
                                    <span>Room is undergoing maintenance</span>
                                </div>
                            @endif

                            <!-- Price per night -->
                            <div class="flex justify-between items-center pt-3 border-t border-gray-150/50">
                                <span class="text-[9px] text-gray-400 font-extrabold uppercase tracking-wider">Price/Night</span>
                                <span class="text-xs font-black text-amber-700">RP {{ number_format($room->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Tab Content: Management & CMS -->
        <div id="content-management" class="tab-content hidden space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left 2 Columns: Quick Controls -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 space-y-4">
                    <div>
                        <h3 class="font-extrabold text-gray-900 text-sm tracking-tight">Quick Guesthouse Controls</h3>
                        <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider mt-0.5">Click any panel to manage resources inside a popup without leaving the dashboard</p>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Card 1: Rooms & Villas -->
                        <div onclick="openCrudModal('{{ route('admin.rooms.index') }}', 'Manage Rooms & Villas')" class="bg-gray-50/60 hover:bg-white border border-gray-200/80 hover:border-amber-600/30 rounded-xl p-4 flex flex-col justify-between gap-4 cursor-pointer transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 group">
                            <div class="space-y-2.5">
                                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center group-hover:bg-amber-100 transition shrink-0">
                                    <span class="material-symbols-outlined text-xl">hotel</span>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-bold text-gray-900 text-xs tracking-tight">Rooms & Villas</h4>
                                    <p class="text-[10px] text-gray-500 leading-normal">Configure guest rooms, pricing rates, details, and room availability.</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-150/40 pt-2.5 mt-auto">
                                <span class="text-[9px] text-gray-400 font-extrabold uppercase tracking-wider">Total: {{ $stats['total_rooms'] }}</span>
                                <span class="text-[9px] text-amber-700 font-bold bg-amber-50 px-2 py-0.5 rounded group-hover:bg-amber-100 transition">Manage &rarr;</span>
                            </div>
                        </div>

                        <!-- Card 2: About Us -->
                        <div onclick="openCrudModal('{{ route('admin.cms.about') }}', 'Customize About Us')" class="bg-gray-50/60 hover:bg-white border border-gray-200/80 hover:border-blue-600/30 rounded-xl p-4 flex flex-col justify-between gap-4 cursor-pointer transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 group">
                            <div class="space-y-2.5">
                                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center group-hover:bg-blue-100 transition shrink-0">
                                    <span class="material-symbols-outlined text-xl">edit_note</span>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-bold text-gray-900 text-xs tracking-tight">About Us Content</h4>
                                    <p class="text-[10px] text-gray-500 leading-normal">Update landing page descriptions, stories, contact info, and upload hero photos.</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-150/40 pt-2.5 mt-auto">
                                <span class="text-[9px] text-gray-400 font-extrabold uppercase tracking-wider">CMS Settings</span>
                                <span class="text-[9px] text-blue-700 font-bold bg-blue-50 px-2 py-0.5 rounded group-hover:bg-blue-100 transition">Manage &rarr;</span>
                            </div>
                        </div>

                        <!-- Card 3: Facilities -->
                        <div onclick="openCrudModal('{{ route('admin.cms.facilities.index') }}', 'Customize Our Facilities')" class="bg-gray-50/60 hover:bg-white border border-gray-200/80 hover:border-emerald-600/30 rounded-xl p-4 flex flex-col justify-between gap-4 cursor-pointer transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 group">
                            <div class="space-y-2.5">
                                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center group-hover:bg-emerald-100 transition shrink-0">
                                    <span class="material-symbols-outlined text-xl">pool</span>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-bold text-gray-900 text-xs tracking-tight">Our Facilities</h4>
                                    <p class="text-[10px] text-gray-500 leading-normal">Add, edit, or remove guesthouse facilities, icons, and features.</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-150/40 pt-2.5 mt-auto">
                                <span class="text-[9px] text-gray-400 font-extrabold uppercase tracking-wider">Amenities List</span>
                                <span class="text-[9px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded group-hover:bg-emerald-100 transition">Manage &rarr;</span>
                            </div>
                        </div>

                        <!-- Card 4: Photo Gallery -->
                        <div onclick="openCrudModal('{{ route('admin.cms.gallery.index') }}', 'Customize Photo Gallery')" class="bg-gray-50/60 hover:bg-white border border-gray-200/80 hover:border-orange-600/30 rounded-xl p-4 flex flex-col justify-between gap-4 cursor-pointer transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 group">
                            <div class="space-y-2.5">
                                <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-700 flex items-center justify-center group-hover:bg-orange-100 transition shrink-0">
                                    <span class="material-symbols-outlined text-xl">photo_camera</span>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-bold text-gray-900 text-xs tracking-tight">Photo Gallery</h4>
                                    <p class="text-[10px] text-gray-500 leading-normal">Upload and manage visual photos of the guesthouse property, pool, and rooms.</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-150/40 pt-2.5 mt-auto">
                                <span class="text-[9px] text-gray-400 font-extrabold uppercase tracking-wider">CMS Media</span>
                                <span class="text-[9px] text-orange-700 font-bold bg-orange-50 px-2 py-0.5 rounded group-hover:bg-orange-100 transition">Manage &rarr;</span>
                            </div>
                        </div>

                        <!-- Card 5: Reservasi & Pembayaran -->
                        <div onclick="openCrudModal('{{ route('admin.bookings.index') }}', 'Manage Reservasi & Pembayaran')" class="bg-gray-50/60 hover:bg-white border border-gray-200/80 hover:border-indigo-600/30 rounded-xl p-4 flex flex-col justify-between gap-4 cursor-pointer transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 group">
                            <div class="space-y-2.5">
                                <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center group-hover:bg-indigo-100 transition shrink-0">
                                    <span class="material-symbols-outlined text-xl">credit_card</span>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-bold text-gray-900 text-xs tracking-tight">Reservations & Bills</h4>
                                    <p class="text-[10px] text-gray-500 leading-normal">Track all guest reservations, manual bank payments, history, and edit details.</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-150/40 pt-2.5 mt-auto">
                                <span class="text-[9px] text-gray-400 font-extrabold uppercase tracking-wider">Full Bookings Logs</span>
                                <span class="text-[9px] text-indigo-700 font-bold bg-indigo-50 px-2 py-0.5 rounded group-hover:bg-indigo-100 transition font-semibold">Verify / Modify</span>
                            </div>
                        </div>

                        <!-- Card 6: Support Tickets -->
                        <div onclick="openCrudModal('{{ route('admin.complaints.index') }}', 'Customer Complaint Logs')" class="bg-gray-50/60 hover:bg-white border border-gray-200/80 hover:border-purple-600/30 rounded-xl p-4 flex flex-col justify-between gap-4 cursor-pointer transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 group">
                            <div class="space-y-2.5">
                                <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center group-hover:bg-purple-100 transition shrink-0">
                                    <span class="material-symbols-outlined text-xl">forum</span>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-bold text-gray-900 text-xs tracking-tight">Complaints & Tickets</h4>
                                    <p class="text-[10px] text-gray-500 leading-normal">Read customer support ticket reports and record official resolution notes.</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-150/40 pt-2.5 mt-auto">
                                <span class="text-[9px] text-gray-400 font-extrabold uppercase tracking-wider">Support Log</span>
                                <span class="text-[9px] text-purple-700 font-bold bg-purple-50 px-2 py-0.5 rounded group-hover:bg-purple-100 transition">Manage &rarr;</span>
                            </div>
                        </div>

                        <!-- Card 7: Guests Accounts (Col-Span 2 on medium+ for layout balance) -->
                        <div onclick="openCrudModal('{{ route('admin.users.index') }}', 'Manage Customers (Guests)')" class="bg-gray-50/60 hover:bg-white border border-gray-200/80 hover:border-rose-600/30 rounded-xl p-4 flex flex-col justify-between gap-4 cursor-pointer transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 group sm:col-span-2">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center group-hover:bg-rose-100 transition shrink-0">
                                    <span class="material-symbols-outlined text-xl">group</span>
                                </div>
                                <div class="space-y-0.5">
                                    <h4 class="font-bold text-gray-900 text-xs tracking-tight">Guest Accounts</h4>
                                    <p class="text-[10px] text-gray-500 leading-normal">Manage customer accounts, search phone numbers, update details, or delete registered guest profiles.</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-150/40 pt-2.5 mt-1">
                                <span class="text-[9px] text-gray-400 font-extrabold uppercase tracking-wider">Total Guests: {{ $stats['total_users'] }}</span>
                                <span class="text-[9px] text-rose-700 font-bold bg-rose-50 px-2 py-0.5 rounded group-hover:bg-rose-100 transition font-semibold">CRUD Users &rarr;</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right 1 Column: System Info -->
                <div class="lg:col-span-1">
                    <!-- Database Info Card -->
                    <div class="bg-gray-900 text-white rounded-xl p-5 shadow-sm border border-gray-800 space-y-2.5">
                    <h4 class="font-bold text-[10px] text-gray-400 uppercase tracking-wider">System Information</h4>
                    <ul class="text-[11px] space-y-1.5 text-gray-300">
                        <li class="flex justify-between">
                            <span>Database Driver:</span>
                            <strong class="text-white font-semibold">MySQL</strong>
                        </li>
                        <li class="flex justify-between">
                            <span>Laravel Framework:</span>
                            <strong class="text-white font-semibold">v13.x</strong>
                        </li>
                        <li class="flex justify-between">
                            <span>Tailwind CSS:</span>
                            <strong class="text-white font-semibold">v4.x CSS-First</strong>
                        </li>
                        <li class="flex justify-between">
                            <span>Host Environment:</span>
                            <strong class="text-white font-semibold">Windows Server</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        </div>
    </main>

    <!-- Footer -->
    @include('components.footer')

    <script>
        function toggleCollapse(targetId, iconId) {
            const target = document.getElementById(targetId);
            const icon = document.getElementById(iconId);
            
            if (target.classList.contains('max-h-0')) {
                // Expand
                target.classList.remove('max-h-0', 'opacity-0');
                target.classList.add('max-h-[800px]', 'opacity-100');
                icon.classList.remove('rotate-180');
            } else {
                // Collapse
                target.classList.remove('max-h-[800px]', 'opacity-100');
                target.classList.add('max-h-0', 'opacity-0');
                icon.classList.add('rotate-180');
            }
        }

        function switchTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            // Show target tab content
            document.getElementById('content-' + tabId).classList.remove('hidden');

            // Reset tab button states
            const tabButtons = ['analytics', 'requests', 'management', 'rooms-status'];
            tabButtons.forEach(btnId => {
                const btn = document.getElementById('tab-' + btnId);
                if (btn && btnId === tabId) {
                    btn.classList.remove('border-transparent', 'text-gray-500');
                    btn.classList.add('border-amber-600', 'text-amber-700');
                } else if (btn) {
                    btn.classList.remove('border-amber-600', 'text-amber-700');
                    btn.classList.add('border-transparent', 'text-gray-500');
                }
            });

            // Persist tab choice in localStorage
            localStorage.setItem('admin_dashboard_active_tab', tabId);
        }

        function filterRooms(status) {
            // Update filter button colors
            const statuses = ['all', 'tersedia', 'dipesan', 'pemeliharaan'];
            statuses.forEach(s => {
                const btn = document.getElementById('btn-filter-' + s);
                if (btn) {
                    if (s === status) {
                        btn.className = 'px-3.5 py-1.5 rounded-lg text-xs font-bold transition bg-amber-600 text-white shadow-sm border border-amber-700/10 cursor-pointer';
                    } else {
                        btn.className = 'px-3.5 py-1.5 rounded-lg text-xs font-bold transition bg-gray-50 text-gray-655 hover:bg-gray-100 hover:text-gray-800 border border-gray-200/60 cursor-pointer';
                    }
                }
            });

            // Show/hide cards
            document.querySelectorAll('.room-status-card').forEach(card => {
                if (status === 'all' || card.getAttribute('data-status') === status) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        }

        // Initialize active tab from localStorage
        document.addEventListener('DOMContentLoaded', () => {
            const activeTab = localStorage.getItem('admin_dashboard_active_tab') || 'requests';
            switchTab(activeTab);
        });

        // CRUD Popup Modal Handlers
        function openCrudModal(url, title) {
            const modal = document.getElementById('crud-modal');
            const container = document.getElementById('crud-modal-container');
            const iframe = document.getElementById('crud-iframe');
            const spinner = document.getElementById('crud-iframe-spinner');
            const titleEl = document.getElementById('crud-modal-title');
            
            titleEl.textContent = title;
            iframe.src = url;
            iframe.classList.add('opacity-0');
            spinner.classList.remove('opacity-0', 'hidden');
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            setTimeout(() => {
                container.classList.remove('scale-95', 'opacity-0');
                container.classList.add('scale-100', 'opacity-100');
            }, 10);
            
            document.body.classList.add('overflow-hidden');
        }

        function closeCrudModal() {
            const modal = document.getElementById('crud-modal');
            const container = document.getElementById('crud-modal-container');
            const iframe = document.getElementById('crud-iframe');
            
            container.classList.remove('scale-100', 'opacity-100');
            container.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                iframe.src = "";
                window.location.reload();
            }, 300);
            
            document.body.classList.remove('overflow-hidden');
        }

        function refreshCrudIframe() {
            const iframe = document.getElementById('crud-iframe');
            const spinner = document.getElementById('crud-iframe-spinner');
            
            iframe.classList.add('opacity-0');
            spinner.classList.remove('opacity-0', 'hidden');
            iframe.contentWindow.location.reload();
        }

        function onIframeLoaded() {
            const iframe = document.getElementById('crud-iframe');
            const spinner = document.getElementById('crud-iframe-spinner');
            
            if (iframe.src && iframe.src !== "" && iframe.src !== window.location.href) {
                spinner.classList.add('opacity-0');
                setTimeout(() => {
                    spinner.classList.add('hidden');
                }, 300);
                iframe.classList.remove('opacity-0');
            }
        }

        // Export Modal Handlers
        function openExportModal() {
            const modal = document.getElementById('export-modal');
            const container = document.getElementById('export-modal-container');
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            setTimeout(() => {
                container.classList.remove('scale-95', 'opacity-0');
                container.classList.add('scale-100', 'opacity-100');
            }, 10);
            
            document.body.classList.add('overflow-hidden');
        }

        function closeExportModal() {
            const modal = document.getElementById('export-modal');
            const container = document.getElementById('export-modal-container');
            
            container.classList.remove('scale-100', 'opacity-100');
            container.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
            
            document.body.classList.remove('overflow-hidden');
        }
    </script>

    <!-- Beautiful CRUD Modal -->
    <div id="crud-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div onclick="closeCrudModal()" class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity duration-300"></div>
        
        <!-- Modal Container -->
        <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-6xl flex flex-col overflow-hidden transition-all duration-300 scale-95 opacity-0 transform" id="crud-modal-container" style="height: 90vh;">
            <!-- Header -->
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-600 animate-pulse"></span>
                    <h3 id="crud-modal-title" class="font-extrabold text-gray-900 text-sm tracking-tight">Manage Content</h3>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Refresh Button -->
                    <button onclick="refreshCrudIframe()" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition flex items-center justify-center cursor-pointer select-none" title="Refresh Panel">
                        <span class="material-symbols-outlined text-lg">refresh</span>
                    </button>
                    <!-- Close Button -->
                    <button onclick="closeCrudModal()" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition flex items-center justify-center cursor-pointer select-none" title="Close Panel">
                        <span class="material-symbols-outlined text-lg font-bold">close</span>
                    </button>
                </div>
            </div>
            
            <!-- Iframe Area -->
            <div class="flex-grow w-full relative bg-gray-50">
                <!-- Loading Spinner -->
                <div id="crud-iframe-spinner" class="absolute inset-0 flex items-center justify-center bg-gray-50 z-10 transition duration-300">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-9 h-9 border-4 border-amber-600 border-t-transparent rounded-full animate-spin"></div>
                        <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Loading panel...</span>
                    </div>
                </div>
                <!-- The Iframe -->
                <iframe id="crud-iframe" class="absolute inset-0 w-full h-full border-none opacity-0 transition-opacity duration-300" src="" onload="onIframeLoaded()"></iframe>
            </div>
        </div>
    </div>

    <!-- Elegant Export Reports Modal -->
    <div id="export-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div onclick="closeExportModal()" class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity duration-300"></div>
        
        <!-- Modal Container -->
        <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-lg flex flex-col overflow-hidden transition-all duration-300 scale-95 opacity-0 transform" id="export-modal-container">
            <!-- Header -->
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-700 shrink-0 flex items-center justify-center">
                        <span class="material-symbols-outlined text-lg font-bold">description</span>
                    </span>
                    <div>
                        <h3 class="font-extrabold text-gray-900 text-sm tracking-tight">Export Reservations Report</h3>
                        <p class="text-[10px] text-gray-500 font-medium">Download styled spreadsheet (XLS) with filters</p>
                    </div>
                </div>
                <button onclick="closeExportModal()" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition flex items-center justify-center cursor-pointer select-none">
                    <span class="material-symbols-outlined text-lg font-bold">close</span>
                </button>
            </div>
            
            <!-- Form -->
            <form action="{{ route('admin.reports.export') }}" method="GET" onsubmit="closeExportModal(); return true;">
                <div class="p-5 space-y-4">
                    <!-- Status Filter -->
                    <div class="space-y-1">
                        <label for="export-status" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider">Status</label>
                        <select id="export-status" name="status" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-amber-500/30 focus:border-amber-600 transition">
                            <option value="all">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="rejected">Rejected</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <!-- Room Filter -->
                    <div class="space-y-1">
                        <label for="export-room" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider">Room / Villa</label>
                        <select id="export-room" name="room_id" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-amber-500/30 focus:border-amber-600 transition">
                            <option value="all">All Rooms & Villas</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Type Filter -->
                    <div class="space-y-1">
                        <label for="export-date-type" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider">Date Type Filter</label>
                        <select id="export-date-type" name="date_type" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-amber-500/30 focus:border-amber-600 transition">
                            <option value="check_in">Check-In Date</option>
                            <option value="check_out">Check-Out Date</option>
                            <option value="created_at">Booking Date (Created At)</option>
                        </select>
                    </div>

                    <!-- Date Ranges -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label for="export-start-date" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider">Start Date</label>
                            <input type="date" id="export-start-date" name="start_date" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-amber-500/30 focus:border-amber-600 transition">
                        </div>
                        <div class="space-y-1">
                            <label for="export-end-date" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider">End Date</label>
                            <input type="date" id="export-end-date" name="end_date" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-amber-500/30 focus:border-amber-600 transition">
                        </div>
                    </div>
                </div>

                <!-- Footer / Action Buttons -->
                <div class="px-5 py-3.5 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeExportModal()" class="px-4 py-2 border border-gray-200 text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-lg text-xs font-semibold transition cursor-pointer select-none">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition flex items-center gap-1.5 cursor-pointer select-none">
                        <span class="material-symbols-outlined text-[15px]">download</span>
                        Download XLS
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
