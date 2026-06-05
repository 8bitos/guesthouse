<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Facilities - Bagus Guest House</title>
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

    <!-- Header -->
    <section class="bg-gray-900 text-white py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold">Manage Facilities</h1>
                <p class="text-gray-400 text-sm mt-1">Configure facilities, icons, and descriptions displayed on the home page.</p>
            </div>
            <a href="{{ route('admin.cms.facilities.create') }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white px-5 py-2.5 rounded-lg font-semibold text-sm transition text-center shrink-0">
                + Add Facility
            </a>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow w-full">
        <!-- Success Alert -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-semibold flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700 font-bold">&times;</button>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Facilities List</span>
                <span class="text-xs text-gray-500">Total: {{ $facilities->total() }} items</span>
            </div>

            @if ($facilities->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100/50 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                <th class="px-6 py-4 w-24">Icon</th>
                                <th class="px-6 py-4">Title</th>
                                <th class="px-6 py-4">Description</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm">
                            @foreach ($facilities as $facility)
                                <tr class="hover:bg-gray-50/50 transition">
                                     <td class="px-6 py-4 text-center select-none shrink-0">
                                         @if(preg_match('/^[a-z0-9_]+$/i', $facility->icon))
                                             <span class="material-symbols-outlined text-3xl text-blue-600 leading-none">{{ $facility->icon }}</span>
                                         @else
                                             <span class="text-3xl leading-none">{{ $facility->icon }}</span>
                                         @endif
                                     </td>
                                    <!-- Title -->
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $facility->title }}</td>
                                    <!-- Description -->
                                    <td class="px-6 py-4 text-gray-600">{{ $facility->description }}</td>
                                    <!-- Actions -->
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('admin.cms.facilities.edit', $facility) }}" class="text-blue-600 hover:text-blue-800 font-bold transition">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.cms.facilities.destroy', $facility) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this facility?')" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 font-bold transition cursor-pointer">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $facilities->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <span class="material-symbols-outlined text-gray-400 text-5xl">inbox</span>
                    <h3 class="text-lg font-bold text-gray-700 mt-4">No facilities registered</h3>
                    <p class="text-gray-500 text-sm mt-1 mb-6">Create a facility listing to get started.</p>
                    <a href="{{ route('admin.cms.facilities.create') }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white px-6 py-2.5 rounded-lg font-semibold transition">
                        + Create First Facility
                    </a>
                </div>
            @endif
        </div>
    </main>

    <!-- Footer -->
    @include('components.footer')
</body>
</html>
