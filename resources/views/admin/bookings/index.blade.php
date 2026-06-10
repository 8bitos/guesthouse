<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Reservations - Bagus Guest House</title>
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="bg-amber-600 text-[10px] font-extrabold uppercase px-2 py-0.5 rounded">Admin Control</span>
                    <h1 class="text-2xl sm:text-3xl font-bold">Manage Reservations</h1>
                </div>
                <p class="text-gray-400 text-xs sm:text-sm mt-1">Review check-in lists, modify reservation statuses, cancel bookings, and verify records.</p>
            </div>
            
            <a href="{{ route('admin.dashboard') }}" class="bg-white/10 hover:bg-white/15 text-white border border-white/20 px-4 py-2 rounded-lg font-semibold text-xs transition inline-block text-center flex items-center justify-center gap-1.5 self-start sm:self-auto">
                <span class="material-symbols-outlined text-sm font-bold">arrow_back</span>
                <span>Back to Dashboard</span>
            </a>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow w-full space-y-6">
        
        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-xs font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600 text-sm">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter Bar Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
            <form action="{{ route('admin.bookings.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <!-- Status Filter -->
                <div class="space-y-1">
                    <label for="status" class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider">Filter Status</label>
                    <select name="status" id="status" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition">
                        <option value="">-- All Statuses --</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div class="space-y-1">
                    <label for="date" class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider">Active on Date</label>
                    <input type="date" name="date" id="date" value="{{ request('date') }}"
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition">
                </div>

                <!-- Buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-amber-700 hover:bg-amber-800 text-white font-bold py-2 rounded-lg text-xs tracking-wide shadow transition cursor-pointer select-none">
                        Apply Filters
                    </button>
                    @if(request()->filled('status') || request()->filled('date'))
                        <a href="{{ route('admin.bookings.index') }}" class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-bold px-3 py-2 rounded-lg text-xs transition inline-block text-center flex items-center justify-center select-none">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Bookings List Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200/80 flex justify-between items-center bg-gray-50/50">
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-700 text-sm leading-none">calendar_month</span>
                    <span>Reservations Logs</span>
                </h2>
                <span class="text-xs text-gray-500 font-semibold">{{ $bookings->total() }} reservation(s) total</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100/50 text-[10px] font-extrabold text-gray-500 uppercase tracking-wider border-b border-gray-200/80">
                            <th class="px-6 py-4">Booking ID / Guest</th>
                            <th class="px-6 py-4">Room Type</th>
                            <th class="px-6 py-4">Dates / Nights</th>
                            <th class="px-6 py-4">Total Paid</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200/60 text-xs">
                        @forelse ($bookings as $booking)
                            <tr class="hover:bg-gray-50/30 transition">
                                <!-- ID / Guest -->
                                <td class="px-6 py-4">
                                    <span class="block font-bold text-gray-900">{{ $booking->guest_name }}</span>
                                    <span class="text-[10px] text-amber-700 font-semibold">{{ $booking->invoice_no }}</span>
                                    <span class="block text-[10px] text-gray-400 font-semibold truncate max-w-xs mt-0.5">{{ $booking->guest_email }}</span>
                                </td>
                                <!-- Room -->
                                <td class="px-6 py-4">
                                    <span class="block font-semibold text-gray-700">{{ $booking->room ? $booking->room->name : 'Deleted Room' }}</span>
                                    @if ($booking->include_breakfast || $booking->include_extra_bed || $booking->late_checkout)
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @if ($booking->include_breakfast)
                                                <span class="bg-blue-50 text-blue-700 border border-blue-100 text-[8px] font-extrabold uppercase px-1.5 py-0.5 rounded">Sarapan</span>
                                            @endif
                                            @if ($booking->include_extra_bed)
                                                <span class="bg-indigo-50 text-indigo-700 border border-indigo-100 text-[8px] font-extrabold uppercase px-1.5 py-0.5 rounded">Kasur</span>
                                            @endif
                                            @if ($booking->late_checkout)
                                                <span class="bg-purple-50 text-purple-700 border border-purple-100 text-[8px] font-extrabold uppercase px-1.5 py-0.5 rounded">Late CO</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <!-- Dates -->
                                <td class="px-6 py-4">
                                    <span class="block font-semibold text-gray-700">{{ date('d M Y', strtotime($booking->check_in)) }} - {{ date('d M Y', strtotime($booking->check_out)) }}</span>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $booking->nights }} night(s) &bull; {{ $booking->guests }} guest(s)</span>
                                </td>
                                <!-- Price -->
                                <td class="px-6 py-4 font-black text-amber-700">RP {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                <!-- Status -->
                                <td class="px-6 py-4">
                                    @if ($booking->status === 'confirmed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-green-50 text-green-700 border border-green-200">Confirmed</span>
                                    @elseif ($booking->status === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-gray-100 text-gray-700 border border-gray-200">Completed</span>
                                    @elseif ($booking->status === 'cancelled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-rose-50 text-rose-700 border border-rose-200">Cancelled</span>
                                    @elseif ($booking->status === 'rejected')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-red-50 text-red-700 border border-red-200">Rejected</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-yellow-50 text-yellow-700 border border-yellow-200">Pending</span>
                                    @endif
                                </td>
                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Edit button -->
                                        <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="text-amber-700 hover:text-amber-800 bg-amber-50 hover:bg-amber-100/70 border border-amber-200/50 font-bold px-3 py-1.5 rounded transition text-[11px] inline-flex items-center gap-1 select-none">
                                            <span class="material-symbols-outlined text-xs leading-none">edit</span>
                                            <span>Manage / Status</span>
                                        </a>

                                        <!-- Cancel button (only if confirmed or pending) -->
                                        @if(in_array($booking->status, ['pending', 'confirmed']))
                                            <form action="{{ route('admin.bookings.cancel', $booking->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                                @csrf
                                                <button type="submit" class="text-rose-700 hover:text-rose-800 bg-rose-50 hover:bg-rose-100/70 border border-rose-200/50 font-bold px-3 py-1.5 rounded transition text-[11px] inline-flex items-center gap-1 cursor-pointer select-none">
                                                    <span class="material-symbols-outlined text-xs leading-none">cancel</span>
                                                    <span>Cancel</span>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <!-- Delete button -->
                                        <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this reservation record from the database?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-700 hover:text-red-800 bg-red-50 hover:bg-red-100/70 border border-red-200/50 font-bold px-3 py-1.5 rounded transition text-[11px] inline-flex items-center gap-1 cursor-pointer select-none">
                                                <span class="material-symbols-outlined text-xs leading-none">delete</span>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">calendar_today</span>
                                    <h3 class="font-bold text-gray-600">No reservations found</h3>
                                    <p class="text-xs text-gray-400">There are no reservation records matching the specified filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            @if ($bookings->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>

    </main>

    <!-- Footer -->
    @include('components.footer')
</body>
</html>
