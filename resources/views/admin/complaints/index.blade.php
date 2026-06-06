<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Complaint Logs - Bagus Guest House</title>
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
                    <h1 class="text-2xl sm:text-3xl font-bold">Customer Complaint Logs</h1>
                </div>
                <p class="text-gray-400 text-xs sm:text-sm mt-1">Review feedback, resolve issues, and log resolutions for guest feedback tickets.</p>
            </div>
            
            <a href="{{ route('admin.dashboard') }}" class="bg-white/10 hover:bg-white/15 text-white border border-white/20 px-4 py-2 rounded-lg font-semibold text-xs transition inline-block text-center flex items-center justify-center gap-1.5 self-start sm:self-auto">
                <span class="material-symbols-outlined text-sm font-bold">arrow_back</span>
                <span>Back to Dashboard</span>
            </a>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow w-full">
        
        @if (session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-xs font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600 text-sm">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200/80 flex justify-between items-center bg-gray-50/50">
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-700 text-sm leading-none">forum</span>
                    <span>Active Feedback Tickets</span>
                </h2>
                <span class="text-xs text-gray-500 font-semibold">{{ $complaints->total() }} ticket(s) total</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100/50 text-[10px] font-extrabold text-gray-500 uppercase tracking-wider border-b border-gray-200/80">
                            <th class="px-6 py-4">Ticket ID</th>
                            <th class="px-6 py-4">Customer</th>
                            <th class="px-6 py-4">Linked Booking</th>
                            <th class="px-6 py-4">Subject</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Date Filed</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200/60 text-xs">
                        @forelse ($complaints as $ticket)
                            <tr class="hover:bg-gray-50/30 transition">
                                <!-- ID -->
                                <td class="px-6 py-4 font-bold text-gray-900">#BGH-TKT-{{ $ticket->id }}</td>
                                <!-- Customer -->
                                <td class="px-6 py-4">
                                    <span class="block font-semibold text-gray-800">{{ $ticket->user ? $ticket->user->name : 'Deleted User' }}</span>
                                    <span class="text-[9px] text-gray-400 font-semibold">{{ $ticket->user ? $ticket->user->email : '' }}</span>
                                </td>
                                <!-- Linked Booking -->
                                <td class="px-6 py-4 font-semibold text-gray-600">
                                    @if($ticket->booking)
                                        <span class="text-amber-700">{{ $ticket->booking->invoice_no }}</span>
                                    @else
                                        <span class="text-gray-400 italic">General Feedback</span>
                                    @endif
                                </td>
                                <!-- Subject -->
                                <td class="px-6 py-4 font-bold text-gray-700 max-w-xs truncate">{{ $ticket->subject }}</td>
                                <!-- Status -->
                                <td class="px-6 py-4">
                                    @if($ticket->status === 'resolved')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-green-50 text-green-700 border border-green-200">Resolved</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-yellow-50 text-yellow-700 border border-yellow-200">Pending</span>
                                    @endif
                                </td>
                                <!-- Date Filed -->
                                <td class="px-6 py-4 text-gray-500">{{ $ticket->created_at->format('d M Y H:i') }}</td>
                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.complaints.show', $ticket->id) }}" class="text-amber-700 hover:text-amber-800 bg-amber-50 hover:bg-amber-100/70 border border-amber-200/50 font-bold px-3 py-1.5 rounded transition text-[11px] inline-flex items-center gap-1 select-none">
                                        <span class="material-symbols-outlined text-xs leading-none">visibility</span>
                                        <span>View &amp; Resolve</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">forum</span>
                                    <h3 class="font-bold text-gray-600">No complaints logged</h3>
                                    <p class="text-xs text-gray-400">All customer feedback tickets have been resolved, or no tickets exist.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            @if ($complaints->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                    {{ $complaints->links() }}
                </div>
            @endif
        </div>

    </main>

    <!-- Footer -->
    @include('components.footer')
</body>
</html>
