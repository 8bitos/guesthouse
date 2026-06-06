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
                <a href="{{ route('admin.reports.export') }}" class="bg-white/10 hover:bg-white/15 text-white border border-white/20 px-3.5 py-1.5 rounded-lg font-semibold text-xs transition inline-block text-center flex items-center justify-center">
                    Export Reports
                </a>
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
                                    <div class="overflow-x-auto pb-4 scrollbar-thin">
                                        @php
                                            $N = count($monthlyTrends);
                                            $chartWidth = max(600, $N * 80);
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
                                        <div class="relative" style="width: {{ $chartWidth }}px; height: {{ $chartHeight + 30 }}px;">
                                            <!-- SVG Graph -->
                                            <svg class="absolute inset-0 pointer-events-none" width="{{ $chartWidth }}" height="{{ $chartHeight }}" viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}">
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
                                            <div class="absolute inset-0" style="width: {{ $chartWidth }}px; height: {{ $chartHeight }}px;">
                                                @foreach ($monthlyTrends as $index => $trend)
                                                    @php
                                                        $pt = $bookingsPoints[$index];
                                                        $colWidth = ($N > 1) ? ($chartWidth - (2 * $paddingX)) / ($N - 1) : $chartWidth;
                                                        $left = ($N > 1) ? $pt['x'] - ($colWidth / 2) : 0;
                                                    @endphp
                                                    <div class="absolute group cursor-pointer" style="left: {{ $left }}px; width: {{ $colWidth }}px; top: 0; bottom: 0;">
                                                        <!-- Vertical hover line indicator -->
                                                        <div class="absolute inset-y-0 w-px bg-gray-200 opacity-0 group-hover:opacity-100 transition pointer-events-none left-1/2 -translate-x-1/2"></div>
                                                        
                                                        <!-- Tooltip -->
                                                        <div class="absolute bottom-full mb-3 bg-gray-900 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded opacity-0 group-hover:opacity-100 transition duration-200 pointer-events-none text-center whitespace-nowrap z-10 shadow-lg leading-relaxed left-1/2 -translate-x-1/2">
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
                                                    @endphp
                                                    <span class="absolute text-[9px] font-bold text-gray-400 uppercase tracking-wider text-center block w-20 -ml-10" style="left: {{ $pt['x'] }}px;">
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

        @if (session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-xs font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600 text-sm">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Columns: Bookings Table -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
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
                                        <span class="block font-semibold text-gray-700">{{ $booking['room'] }}</span>
                                        @if ($booking['include_breakfast'] || $booking['include_extra_bed'] || $booking['late_checkout'])
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @if ($booking['include_breakfast'])
                                                    <span class="bg-blue-50 text-blue-700 border border-blue-100 text-[8px] font-extrabold uppercase px-1 py-0.5 rounded">Breakfast</span>
                                                @endif
                                                @if ($booking['include_extra_bed'])
                                                    <span class="bg-indigo-50 text-indigo-700 border border-indigo-100 text-[8px] font-extrabold uppercase px-1 py-0.5 rounded">Extra Bed</span>
                                                @endif
                                                @if ($booking['late_checkout'])
                                                    <span class="bg-purple-50 text-purple-700 border border-purple-100 text-[8px] font-extrabold uppercase px-1 py-0.5 rounded">Late CO</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <!-- Dates -->
                                    <td class="px-4 py-3 text-[11px] text-gray-650">{{ $booking['dates'] }}</td>
                                    <!-- Status -->
                                    <td class="px-4 py-3">
                                        @if ($booking['status'] === 'confirmed')
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
                                        <div class="flex items-center justify-end gap-1">
                                            @if ($booking['status'] === 'pending')
                                                <form action="{{ route('admin.bookings.approve', $booking['id']) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-white rounded shadow transition hover:scale-105 cursor-pointer select-none" title="Approve" style="background-color: #10b981; border: 1px solid #059669; width: 26px; height: 26px; display: inline-flex; justify-content: center; align-items: center;" onmouseover="this.style.backgroundColor='#059669'" onmouseout="this.style.backgroundColor='#10b981'">
                                                        <span class="material-symbols-outlined text-sm font-bold leading-none" style="color: #ffffff;">check</span>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.bookings.reject', $booking['id']) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-white rounded shadow transition hover:scale-105 cursor-pointer select-none" title="Reject" style="background-color: #f43f5e; border: 1px solid #e11d48; width: 26px; height: 26px; display: inline-flex; justify-content: center; align-items: center;" onmouseover="this.style.backgroundColor='#e11d48'" onmouseout="this.style.backgroundColor='#f43f5e'">
                                                        <span class="material-symbols-outlined text-sm font-bold leading-none" style="color: #ffffff;">close</span>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-[10px] text-gray-400 italic font-semibold">Processed</span>
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
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 space-y-3">
                    <h3 class="font-bold text-gray-800 text-sm">Quick Guesthouse Controls</h3>
                    
                    <div class="space-y-1.5">
                        <a href="{{ route('admin.rooms.index') }}" class="w-full text-left px-3.5 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-between text-xs transition inline-block">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-500 text-base leading-none">hotel</span>
                                <span class="font-semibold text-gray-700">Manage Rooms & Villas</span>
                            </div>
                            <span class="text-[10px] text-gray-400 font-semibold uppercase">View All {{ $stats['total_rooms'] }}</span>
                        </a>

                        <a href="{{ route('admin.cms.about') }}" class="w-full text-left px-3.5 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-between text-xs transition inline-block">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-500 text-base leading-none">edit_note</span>
                                <span class="font-semibold text-gray-700">Customize "About Us" Content</span>
                            </div>
                            <span class="text-[10px] text-gray-400 font-semibold uppercase">Manage Texts</span>
                        </a>

                        <a href="{{ route('admin.cms.facilities.index') }}" class="w-full text-left px-3.5 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-between text-xs transition inline-block">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-500 text-base leading-none">pool</span>
                                <span class="font-semibold text-gray-700">Customize "Our Facilities"</span>
                            </div>
                            <span class="text-[10px] text-gray-400 font-semibold uppercase">Configure Offerings</span>
                        </a>

                        <a href="{{ route('admin.cms.gallery.index') }}" class="w-full text-left px-3.5 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-between text-xs transition inline-block">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-500 text-base leading-none">photo_camera</span>
                                <span class="font-semibold text-gray-700">Customize "Photo Gallery"</span>
                            </div>
                            <span class="text-[10px] text-gray-400 font-semibold uppercase">Manage Photos</span>
                        </a>
                        
                        <a href="{{ route('admin.bookings.index') }}" class="w-full text-left px-3.5 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-between text-xs transition inline-block">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-500 text-base leading-none">credit_card</span>
                                <span class="font-semibold text-gray-700">Manage Reservasi & Pembayaran</span>
                            </div>
                            <span class="text-[10px] text-amber-700 font-bold bg-amber-50 px-2 py-0.5 rounded uppercase">Verify / Modify</span>
                        </a>
                        
                        <a href="{{ route('admin.complaints.index') }}" class="w-full text-left px-3.5 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-between text-xs transition inline-block">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-500 text-base leading-none">forum</span>
                                <span class="font-semibold text-gray-700">Customer Complaint Logs</span>
                            </div>
                            <span class="text-[10px] text-gray-400 font-semibold uppercase">View Tickets</span>
                        </a>

                        <a href="{{ route('admin.users.index') }}" class="w-full text-left px-3.5 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-between text-xs transition inline-block">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-500 text-base leading-none">group</span>
                                <span class="font-semibold text-gray-700">Manage Customers (Guests)</span>
                            </div>
                            <span class="text-[10px] text-gray-400 font-semibold uppercase">CRUD Users</span>
                        </a>
                    </div>
                </div>

                <!-- Database Info Card -->
                <div class="bg-gray-900 text-white rounded-xl p-5 shadow-sm border border-gray-800 space-y-2.5">
                    <h4 class="font-bold text-[10px] text-gray-400 uppercase tracking-wider">System Information</h4>
                    <ul class="text-[11px] space-y-1.5 text-gray-300">
                        <li class="flex justify-between">
                            <span>Database Driver:</span>
                            <strong class="text-white font-semibold">SQLite (MySQL Local Dev)</strong>
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
    </script>
</body>
</html>
