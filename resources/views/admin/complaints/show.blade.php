<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resolve Ticket - Bagus Guest House</title>
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
                    <span class="bg-amber-600 text-[10px] font-extrabold uppercase px-2 py-0.5 rounded">Issue Resolution</span>
                    <h1 class="text-2xl sm:text-3xl font-bold">Review Complaint</h1>
                </div>
                <p class="text-gray-400 text-xs sm:text-sm mt-1">Investigate customer concern ticket #BGH-TKT-{{ $complaint->id }}.</p>
            </div>
            
            <a href="{{ route('admin.complaints.index') }}" class="bg-white/10 hover:bg-white/15 text-white border border-white/20 px-4 py-2 rounded-lg font-semibold text-xs transition inline-block text-center flex items-center justify-center gap-1.5 self-start sm:self-auto">
                <span class="material-symbols-outlined text-sm font-bold">arrow_back</span>
                <span>Back to Logs</span>
            </a>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow w-full space-y-6">
        
        <!-- Ticket Information Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6 space-y-6">
            <div class="flex justify-between items-start border-b border-gray-100 pb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-950">{{ $complaint->subject }}</h2>
                    <p class="text-xs text-gray-400 mt-1">Submitted on {{ $complaint->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    @if($complaint->status === 'resolved')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase bg-green-50 text-green-700 border border-green-200">Resolved</span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase bg-yellow-50 text-yellow-700 border border-yellow-200">Pending Investigation</span>
                    @endif
                </div>
            </div>

            <!-- Details Fields -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs border-b border-gray-100 pb-6">
                <div class="space-y-1">
                    <span class="block text-gray-400 font-bold uppercase tracking-wider text-[9px]">Submitted By</span>
                    <strong class="text-gray-900 text-sm">{{ $complaint->user ? $complaint->user->name : 'Deleted User' }}</strong>
                    <span class="block text-gray-500">{{ $complaint->user ? $complaint->user->email : '' }}</span>
                    <span class="block text-gray-500">{{ $complaint->user ? $complaint->user->phone : '' }}</span>
                </div>
                <div class="space-y-1">
                    <span class="block text-gray-400 font-bold uppercase tracking-wider text-[9px]">Linked Stay</span>
                    @if($complaint->booking)
                        <strong class="text-gray-900 text-sm block">{{ $complaint->booking->room ? $complaint->booking->room->name : 'Deleted Room' }}</strong>
                        <span class="block text-amber-700 font-semibold font-mono">{{ $complaint->booking->invoice_no }}</span>
                        <span class="block text-gray-500">{{ date('d M', strtotime($complaint->booking->check_in)) }} - {{ date('d M Y', strtotime($complaint->booking->check_out)) }}</span>
                    @else
                        <strong class="text-gray-400 italic">General/System Feedback</strong>
                    @endif
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <span class="block text-gray-400 font-bold uppercase tracking-wider text-[9px]">Complaint Description</span>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-150 text-sm text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $complaint->description }}</div>
            </div>

            <!-- Resolution Form -->
            <form action="{{ route('admin.complaints.update', $complaint->id) }}" method="POST" class="pt-6 border-t border-gray-100 space-y-4">
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

                <!-- Resolution Field -->
                <div class="space-y-1">
                    <label for="resolution" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Resolution Action Details *</label>
                    <textarea id="resolution" name="resolution" rows="4" required
                              class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition resize-none"
                              placeholder="Detail the steps taken to resolve this customer issue...">{{ old('resolution', $complaint->resolution) }}</textarea>
                </div>

                <!-- Status Update -->
                <div class="space-y-1 max-w-xs">
                    <label for="status" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Complaint Status *</label>
                    <select name="status" id="status" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-amber-700 transition">
                        <option value="pending" {{ old('status', $complaint->status) === 'pending' ? 'selected' : '' }}>Pending (Still active)</option>
                        <option value="resolved" {{ old('status', $complaint->status) === 'resolved' ? 'selected' : '' }}>Resolved (Issue addressed & closed)</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.complaints.index') }}" 
                       class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-bold px-4 py-2.5 rounded-lg text-xs transition select-none">
                        Cancel
                    </a>
                    
                    <button type="submit" 
                            class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-5 py-2.5 rounded-lg text-xs shadow-md shadow-emerald-700/10 transition cursor-pointer select-none">
                        Submit Resolution
                    </button>
                </div>
            </form>
        </div>

    </main>

    <!-- Footer -->
    @include('components.footer')
</body>
</html>
