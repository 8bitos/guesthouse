<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Facility - Bagus Guest House</title>
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
<body class="bg-[#F8FAFC] text-gray-900 font-sans min-h-screen flex flex-col justify-between">
    <!-- Navbar -->
    @include('components.navbar')

    <!-- Header -->
    <section class="bg-gray-900 text-white py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">Edit Facility: {{ $facility->title }}</h1>
                <p class="text-gray-400 text-sm mt-1">Modify the icon, title, or description of this facility.</p>
            </div>
            <a href="{{ route('admin.cms.facilities.index') }}" class="text-sm bg-white/10 hover:bg-white/15 text-white border border-white/20 px-4 py-2 rounded-lg font-semibold transition">
                ← Back to List
            </a>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow w-full">
        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <form method="POST" action="{{ route('admin.cms.facilities.update', $facility) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Facility Title -->
                    <div class="space-y-1">
                        <label for="title" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Facility Title</label>
                        <input id="title" type="text" name="title" value="{{ old('title', $facility->title) }}" required autofocus
                               class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300"
                               placeholder="e.g. Swimming Pool">
                    </div>

                    <!-- Icon / Google Icons Picker -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Select Icon</label>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Left: Selected Preview & Input -->
                            <div class="md:col-span-1 p-4 bg-gray-50 rounded-xl border border-gray-200 flex flex-col items-center justify-center text-center">
                                <div class="w-20 h-20 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 mb-3 select-none" id="icon-preview-box">
                                    <span class="material-symbols-outlined text-5xl" id="preview-icon-symbol">{{ old('icon', $facility->icon) }}</span>
                                </div>
                                <div class="w-full">
                                    <label for="icon" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Icon Identifier</label>
                                    <input id="icon" type="text" name="icon" value="{{ old('icon', $facility->icon) }}" required
                                           class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-center text-sm font-mono text-gray-800 focus:outline-none focus:border-blue-500 transition"
                                           placeholder="e.g. wifi">
                                </div>
                            </div>

                            <!-- Right: Icon Table Picker -->
                            <div class="md:col-span-2 border border-gray-200 rounded-xl overflow-hidden bg-white flex flex-col justify-between">
                                <div class="p-3 border-b border-gray-100 bg-gray-50/50 flex gap-2">
                                    <input type="text" id="icon-search" placeholder="Search curated or global Google icons..."
                                           class="w-full text-xs bg-white border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                </div>
                                <div class="overflow-x-auto max-h-80 overflow-y-auto">
                                    <table class="w-full border-collapse border border-gray-100 table-fixed text-xs">
                                        <tbody class="divide-y divide-gray-100" id="icon-table-body">
                                            <!-- Rows and cells will be inserted here via JS -->
                                        </tbody>
                                    </table>
                                </div>
                                <div id="more-container" class="p-3 bg-gray-50 border-t border-gray-100 text-center hidden">
                                    <button type="button" id="btn-show-more" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-bold transition cursor-pointer">
                                        <span>Show More Curated Icons</span>
                                        <span class="material-symbols-outlined text-xs font-bold leading-none">expand_more</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-400">Choose one of the curated Google Icons from the grid, search to filter them, or type any valid name from <a href="https://fonts.google.com/icons" target="_blank" class="text-blue-500 hover:underline font-semibold">Google Fonts Icons</a> (e.g. <code>ac_unit</code>) directly in the input field. Emojis are also supported!</p>
                    </div>

                    <!-- Description -->
                    <div class="space-y-1">
                        <label for="description" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Description</label>
                        <textarea id="description" name="description" rows="4" required
                                  class="w-full bg-transparent border-b-2 border-gray-200 focus:border-blue-500 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300 resize-none"
                                  placeholder="Enter facility description...">{{ old('description', $facility->description) }}</textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 items-center pt-4 border-t border-gray-100">
                    <button type="submit" 
                            class="px-8 py-3 rounded-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-bold text-sm tracking-wide shadow-lg shadow-blue-500/20 transition cursor-pointer">
                        Update Facility
                    </button>
                    
                    <a href="{{ route('admin.cms.facilities.index') }}" 
                       class="px-8 py-3 rounded-full border border-gray-200 hover:bg-gray-50 text-gray-500 hover:text-gray-700 font-bold text-sm tracking-wide transition inline-block text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    @include('components.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const curatedIcons = [
                { name: 'wifi', label: 'WiFi' },
                { name: 'pool', label: 'Pool' },
                { name: 'restaurant', label: 'Dining' },
                { name: 'spa', label: 'Spa' },
                { name: 'directions_bike', label: 'Bike' },
                { name: 'landscape', label: 'Mountain' },
                { name: 'ac_unit', label: 'AC' },
                { name: 'tv', label: 'TV' },
                { name: 'local_parking', label: 'Parking' },
                { name: 'local_cafe', label: 'Coffee' },
                { name: 'bathtub', label: 'Bath' },
                { name: 'hot_tub', label: 'Hot Tub' },
                { name: 'room_service', label: 'Service' },
                { name: 'dry_cleaning', label: 'Laundry' },
                { name: 'fitness_center', label: 'Gym' },
                { name: 'kitchen', label: 'Kitchen' },
                { name: 'balcony', label: 'Balcony' },
                { name: 'smoke_free', label: 'No Smoke' },
                { name: 'pets', label: 'Pets' },
                { name: 'hiking', label: 'Hiking' },
                { name: 'local_bar', label: 'Bar' },
                { name: 'luggage', label: 'Luggage' },
                { name: 'bed', label: 'Bed' },
                { name: 'lock', label: 'Safe' }
            ];

            const tableBody = document.getElementById('icon-table-body');
            const searchInput = document.getElementById('icon-search');
            const iconInput = document.getElementById('icon');
            const previewSymbol = document.getElementById('preview-icon-symbol');

            let allIcons = [];
            let showAllCurated = false;

            // Fetch the full list of icons
            fetch('{{ asset('js/material-symbols.json') }}')
                .then(response => response.json())
                .then(data => {
                    allIcons = data;
                    if (searchInput.value.trim()) {
                        renderGrid(searchInput.value);
                    }
                })
                .catch(err => {
                    console.error('Failed to load Google Icons list:', err);
                    allIcons = curatedIcons.map(c => c.name);
                });

            // Function to render grid/table
            function renderGrid(filter = '') {
                tableBody.innerHTML = '';
                const moreContainer = document.getElementById('more-container');
                
                let iconsToRender = [];
                
                if (!filter) {
                    // Predefined items: show 10 if showAllCurated is false, otherwise show all 24
                    const limit = showAllCurated ? curatedIcons.length : 10;
                    for (let i = 0; i < limit; i++) {
                        iconsToRender.push(curatedIcons[i].name);
                    }
                    
                    if (!showAllCurated && curatedIcons.length > 10) {
                        moreContainer.classList.remove('hidden');
                    } else {
                        moreContainer.classList.add('hidden');
                    }
                } else {
                    moreContainer.classList.add('hidden');
                    let count = 0;
                    const maxResults = 40; // 8 rows of 5 columns
                    
                    for (let i = 0; i < allIcons.length; i++) {
                        const iconName = allIcons[i];
                        if (iconName.toLowerCase().includes(filter.toLowerCase())) {
                            iconsToRender.push(iconName);
                            count++;
                            if (count >= maxResults) {
                                break;
                            }
                        }
                    }
                }
                
                if (iconsToRender.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="py-8 text-center text-xs text-gray-400">No matching icons found.</td></tr>';
                    return;
                }
                
                // Chunk icons into rows of 5 columns
                const cols = 5;
                for (let i = 0; i < iconsToRender.length; i += cols) {
                    const rowHtml = document.createElement('tr');
                    rowHtml.className = 'border-b border-gray-100';
                    
                    for (let j = 0; j < cols; j++) {
                        const cell = document.createElement('td');
                        cell.className = 'border-r border-gray-100 p-0 w-1/5 relative';
                        
                        const iconIndex = i + j;
                        if (iconIndex < iconsToRender.length) {
                            const iconName = iconsToRender[iconIndex];
                            const isSelected = iconInput.value.trim().toLowerCase() === iconName.toLowerCase();
                            
                            const activeClass = isSelected 
                                ? 'bg-blue-50 text-blue-600 font-bold ring-2 ring-inset ring-blue-500' 
                                : 'bg-white hover:bg-gray-50 text-gray-700';
                                
                            cell.innerHTML = `
                                <button type="button" class="w-full py-3 flex items-center justify-center transition cursor-pointer select-none ${activeClass}" 
                                        title="${iconName}" data-icon="${iconName}">
                                    <span class="material-symbols-outlined text-2xl">${iconName}</span>
                                </button>
                            `;
                            
                            const btn = cell.querySelector('button');
                            btn.addEventListener('click', function() {
                                iconInput.value = iconName;
                                updatePreview();
                                
                                // Update selection states across the table
                                document.querySelectorAll('#icon-table-body button').forEach(b => {
                                    const bName = b.getAttribute('data-icon');
                                    if (bName && bName.toLowerCase() === iconName.toLowerCase()) {
                                        b.className = 'w-full py-3 flex items-center justify-center transition cursor-pointer select-none bg-blue-50 text-blue-600 font-bold ring-2 ring-inset ring-blue-500';
                                    } else {
                                        b.className = 'w-full py-3 flex items-center justify-center transition cursor-pointer select-none bg-white hover:bg-gray-50 text-gray-700';
                                    }
                                });
                            });
                        } else {
                            // Empty cell to fill row
                            cell.innerHTML = '<div class="py-3 bg-gray-50/20"></div>';
                        }
                        
                        rowHtml.appendChild(cell);
                    }
                    
                    tableBody.appendChild(rowHtml);
                }
            }

            // Function to update preview
            function updatePreview() {
                const val = iconInput.value.trim();
                if (val) {
                    if (/^[a-z0-9_]+$/i.test(val)) {
                        previewSymbol.className = "material-symbols-outlined text-5xl";
                        previewSymbol.textContent = val;
                    } else {
                        previewSymbol.className = "text-5xl";
                        previewSymbol.textContent = val;
                    }
                } else {
                    previewSymbol.className = "material-symbols-outlined text-5xl";
                    previewSymbol.textContent = 'help';
                }
            }

            // Event listeners
            searchInput.addEventListener('input', (e) => renderGrid(e.target.value));
            iconInput.addEventListener('input', () => {
                updatePreview();
                // Update selection states in table
                document.querySelectorAll('#icon-table-body button').forEach(btn => {
                    const bName = btn.getAttribute('data-icon');
                    if (!bName) return;
                    if (iconInput.value.trim().toLowerCase() === bName.toLowerCase()) {
                        btn.className = 'w-full py-3 flex items-center justify-center transition cursor-pointer select-none bg-blue-50 text-blue-600 font-bold ring-2 ring-inset ring-blue-500';
                    } else {
                        btn.className = 'w-full py-3 flex items-center justify-center transition cursor-pointer select-none bg-white hover:bg-gray-50 text-gray-700';
                    }
                });
            });

            document.getElementById('btn-show-more').addEventListener('click', function() {
                showAllCurated = true;
                renderGrid(searchInput.value);
            });

            // Initialize
            renderGrid();
            updatePreview();
        });
    </script>
</body>
</html>
