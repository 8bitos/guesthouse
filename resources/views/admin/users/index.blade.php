<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Customers - Bagus Guest House</title>
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
                    <h1 class="text-2xl sm:text-3xl font-bold">Manage Customer Profiles</h1>
                </div>
                <p class="text-gray-400 text-xs sm:text-sm mt-1">Review guest details, contact information, and delete inactive profiles.</p>
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
                    <span class="material-symbols-outlined text-amber-700 text-sm leading-none">group</span>
                    <span>Registered Customers</span>
                </h2>
                <span class="text-xs text-gray-500 font-semibold">{{ $users->total() }} customer(s) total</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100/50 text-[10px] font-extrabold text-gray-500 uppercase tracking-wider border-b border-gray-200/80">
                            <th class="px-6 py-4">Customer ID / Name</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Phone Number</th>
                            <th class="px-6 py-4">Address / Origin</th>
                            <th class="px-6 py-4">Joined Date</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200/60 text-xs">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50/30 transition">
                                <!-- ID / Name -->
                                <td class="px-6 py-4">
                                    <span class="block font-bold text-gray-900">{{ $user->name }}</span>
                                    <span class="text-[10px] text-gray-400 font-semibold uppercase">ID: BGH-USR-{{ $user->id }}</span>
                                </td>
                                <!-- Email -->
                                <td class="px-6 py-4 font-semibold text-gray-700">{{ $user->email }}</td>
                                <!-- Phone -->
                                <td class="px-6 py-4 text-gray-600 font-semibold">{{ $user->phone ?? '-' }}</td>
                                <!-- Address -->
                                <td class="px-6 py-4 text-gray-500 max-w-xs truncate">{{ $user->address ?? '-' }}</td>
                                <!-- Joined Date -->
                                <td class="px-6 py-4 text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="text-amber-700 hover:text-amber-800 bg-amber-50 hover:bg-amber-100/70 border border-amber-200/50 font-bold px-3 py-1.5 rounded transition text-[11px] inline-flex items-center gap-1 select-none">
                                            <span class="material-symbols-outlined text-xs leading-none">edit</span>
                                            <span>Edit</span>
                                        </a>
                                        
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this customer account?')">
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
                                    <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">group_off</span>
                                    <h3 class="font-bold text-gray-600">No customers found</h3>
                                    <p class="text-xs text-gray-400">There are currently no registered customer accounts in the database.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            @if ($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

    </main>

    <!-- Footer -->
    @include('components.footer')
</body>
</html>
