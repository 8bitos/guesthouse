<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Room - Bagus Guest House</title>
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
                <h1 class="text-3xl font-bold">Add New Room</h1>
                <p class="text-gray-400 text-sm mt-1">Fill in the details to list a new accommodation room.</p>
            </div>
            <a href="{{ route('admin.rooms.index') }}" class="text-sm bg-white/10 hover:bg-white/15 text-white border border-white/20 px-4 py-2 rounded-lg font-semibold transition">
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
            <form method="POST" action="{{ route('admin.rooms.store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-8">
                    <!-- Room Name -->
                    <div class="space-y-1">
                        <label for="name" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Room Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                               class="w-full bg-transparent border-b-2 border-gray-200 focus:border-amber-700 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300"
                               placeholder="e.g. Suite 3">
                    </div>

                    <!-- Room Type -->
                    <div class="space-y-1">
                        <label for="type" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Room Type</label>
                        <select id="type" name="type" required
                                class="w-full bg-transparent border-b-2 border-gray-200 focus:border-amber-700 focus:outline-none py-2 text-gray-700 transition">
                            <option value="" disabled {{ old('type') ? '' : 'selected' }}>Select Room Type</option>
                            <option value="Standard Double Room" {{ old('type') === 'Standard Double Room' ? 'selected' : '' }}>Standard Double Room</option>
                            <option value="Deluxe Double Room" {{ old('type') === 'Deluxe Double Room' ? 'selected' : '' }}>Deluxe Double Room</option>
                            <option value="Budget Double Room" {{ old('type') === 'Budget Double Room' ? 'selected' : '' }}>Budget Double Room</option>
                            <option value="Superior King Room" {{ old('type') === 'Superior King Room' ? 'selected' : '' }}>Superior King Room</option>
                        </select>
                    </div>

                    <!-- Price per Night -->
                    <div class="space-y-1">
                        <label for="price" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Price per Night (RP)</label>
                        <input id="price" type="number" name="price" value="{{ old('price') }}" required
                               class="w-full bg-transparent border-b-2 border-gray-200 focus:border-amber-700 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300"
                               placeholder="e.g. 1200000">
                    </div>

                    <!-- Capacity -->
                    <div class="space-y-1">
                        <label for="capacity" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Capacity (Guests)</label>
                        <input id="capacity" type="number" name="capacity" value="{{ old('capacity') }}" required
                               class="w-full bg-transparent border-b-2 border-gray-200 focus:border-amber-700 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300"
                               placeholder="e.g. 2">
                    </div>

                    <!-- Room Size Format & Inputs -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Room Size Format</label>
                        <select id="size_format" onchange="toggleSizeFields()"
                                class="w-full bg-transparent border-b-2 border-gray-200 focus:border-amber-700 focus:outline-none py-2 text-gray-700 transition">
                            <option value="area">Total Area (m²)</option>
                            <option value="dimensions">Dimensions (e.g. 3x2, 4x4)</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Room Size Value</label>
                        <!-- Area input -->
                        <div id="size_area_wrapper">
                            <input id="size_area" type="number" min="1" oninput="updateSizeValue()"
                                   class="w-full bg-transparent border-b-2 border-gray-200 focus:border-amber-700 focus:outline-none py-1.5 text-gray-800 transition"
                                   placeholder="e.g. 25">
                        </div>

                        <!-- Dimensions inputs -->
                        <div id="size_dimensions_wrapper" class="hidden flex items-center gap-2 pt-0.5">
                            <input id="size_width" type="number" min="1" step="0.1" oninput="updateSizeValue()"
                                   class="w-16 bg-transparent border-b-2 border-gray-200 focus:border-amber-700 focus:outline-none py-1 text-gray-800 transition text-center"
                                   placeholder="Width">
                            <span class="text-gray-400 text-xs font-bold select-none">x</span>
                            <input id="size_length" type="number" min="1" step="0.1" oninput="updateSizeValue()"
                                   class="w-16 bg-transparent border-b-2 border-gray-200 focus:border-amber-700 focus:outline-none py-1 text-gray-800 transition text-center"
                                   placeholder="Length">
                            <span class="text-gray-500 text-xs select-none">meters</span>
                        </div>

                        <!-- Hidden input to submit the actual calculated/formatted size string -->
                        <input type="hidden" id="size" name="size" value="{{ old('size', 25) }}">
                    </div>

                    <!-- Status -->
                    <div class="space-y-1">
                        <label for="status" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Operational Status</label>
                        <select id="status" name="status" required
                                class="w-full bg-transparent border-b-2 border-gray-200 focus:border-amber-700 focus:outline-none py-2 text-gray-700 transition">
                            <option value="tersedia" {{ old('status') === 'tersedia' ? 'selected' : '' }}>Available</option>
                            <option value="dipesan" {{ old('status') === 'dipesan' ? 'selected' : '' }}>Booked</option>
                            <option value="perbaikan" {{ old('status') === 'perbaikan' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>

                    <!-- Image Upload -->
                    <div class="space-y-1">
                        <label for="image" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Room Image</label>
                        <input id="image" type="file" name="image" accept="image/*"
                               class="w-full bg-transparent border-b-2 border-gray-200 focus:border-amber-700 focus:outline-none py-1.5 text-gray-600 transition">
                    </div>
                </div>

                <!-- Room Extras & Add-ons -->
                <div class="border-t border-gray-100 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 uppercase tracking-widest">Room Extras & Add-ons</h4>
                            <p class="text-xs text-gray-400">Configure custom optional services for this room.</p>
                        </div>
                        <button type="button" onclick="addAddon()"
                                class="px-4 py-2 bg-amber-700 hover:bg-amber-800 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 cursor-pointer select-none shadow-sm">
                            <span class="material-symbols-outlined text-sm font-bold">add</span>
                            <span>Add Custom Addon</span>
                        </button>
                    </div>

                    <div id="addons-container" class="space-y-4">
                        <!-- Addon items will be generated here -->
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-1">
                    <label for="description" class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Room Description</label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full bg-transparent border-b-2 border-gray-200 focus:border-amber-700 focus:outline-none py-2 text-gray-800 transition placeholder-gray-300 resize-none"
                              placeholder="Enter room features, views, and included services...">{{ old('description') }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 items-center pt-4 border-t border-gray-100">
                    <button type="submit" 
                            class="px-8 py-3 rounded-full bg-gradient-to-r from-amber-700 to-amber-800 hover:from-amber-800 hover:to-amber-900 text-white font-bold text-sm tracking-wide shadow-lg shadow-amber-700/20 transition cursor-pointer">
                        Save Room
                    </button>
                    
                    <a href="{{ route('admin.rooms.index') }}" 
                       class="px-8 py-3 rounded-full border border-gray-200 hover:bg-gray-50 text-gray-500 hover:text-gray-700 font-bold text-sm tracking-wide transition inline-block text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    @include('components.footer')

    <!-- Addons Javascript Helper -->
    <script>
        let addonIndex = 0;

        function addAddon(name = '', price = '', type = 'flat_fee', description = '') {
            const container = document.getElementById('addons-container');
            const index = addonIndex++;

            const addonHtml = `
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-5 relative shadow-sm" id="addon-row-${index}">
                    <button type="button" onclick="removeAddon(${index})"
                            class="absolute top-4 right-4 p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition cursor-pointer select-none border border-transparent hover:border-red-250"
                            title="Delete Addon">
                        <span class="material-symbols-outlined text-sm block">delete</span>
                    </button>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mr-8">
                        <div class="space-y-1">
                            <label class="block text-[10.5px] font-bold text-gray-400 uppercase tracking-widest">Addon Name</label>
                            <input type="text" name="addons[${index}][name]" value="${name}" required
                                   class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-xs focus:border-amber-700 focus:outline-none transition font-semibold"
                                   placeholder="e.g. Breakfast">
                        </div>
                        
                        <div class="space-y-1">
                            <label class="block text-[10.5px] font-bold text-gray-400 uppercase tracking-widest">Price (RP)</label>
                            <input type="number" name="addons[${index}][price]" value="${price}" required min="0"
                                   class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-xs focus:border-amber-700 focus:outline-none transition font-semibold"
                                   placeholder="e.g. 50000">
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[10.5px] font-bold text-gray-400 uppercase tracking-widest">Pricing Model</label>
                            <select name="addons[${index}][type]" required
                                    class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-xs focus:border-amber-700 focus:outline-none transition font-semibold">
                                <option value="flat_fee" ${type === 'flat_fee' ? 'selected' : ''}>Flat Fee</option>
                                <option value="per_night" ${type === 'per_night' ? 'selected' : ''}>Per Night</option>
                                <option value="per_guest_per_night" ${type === 'per_guest_per_night' ? 'selected' : ''}>Per Guest Per Night</option>
                            </select>
                        </div>

                        <div class="md:col-span-3 space-y-1">
                            <label class="block text-[10.5px] font-bold text-gray-400 uppercase tracking-widest">Description</label>
                            <input type="text" name="addons[${index}][description]" value="${description}"
                                   class="w-full bg-white border border-gray-200 rounded-lg px-3.5 py-2.5 text-xs focus:border-amber-700 focus:outline-none transition font-semibold"
                                   placeholder="e.g. Delicious morning buffet with hot coffee, tea, and fresh local fruits">
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', addonHtml);
        }

        function removeAddon(index) {
            const row = document.getElementById(`addon-row-${index}`);
            if (row) {
                row.remove();
            }
        }

        function toggleSizeFields() {
            const format = document.getElementById('size_format').value;
            const areaWrapper = document.getElementById('size_area_wrapper');
            const dimWrapper = document.getElementById('size_dimensions_wrapper');

            if (format === 'area') {
                areaWrapper.classList.remove('hidden');
                dimWrapper.classList.add('hidden');
            } else {
                areaWrapper.classList.add('hidden');
                dimWrapper.classList.remove('hidden');
            }
            updateSizeValue();
        }

        function updateSizeValue() {
            const format = document.getElementById('size_format').value;
            const hiddenInput = document.getElementById('size');

            if (format === 'area') {
                const areaVal = document.getElementById('size_area').value.trim();
                hiddenInput.value = areaVal;
            } else {
                const widthVal = document.getElementById('size_width').value.trim();
                const lengthVal = document.getElementById('size_length').value.trim();
                if (widthVal && lengthVal) {
                    hiddenInput.value = `${widthVal}x${lengthVal}`;
                } else {
                    hiddenInput.value = '';
                }
            }
        }

        // Pre-populate with standard addons
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize room size inputs from the hidden input's value
            const hiddenInput = document.getElementById('size');
            const rawValue = hiddenInput.value || '25';
            
            if (rawValue.includes('x')) {
                document.getElementById('size_format').value = 'dimensions';
                const parts = rawValue.split('x');
                document.getElementById('size_width').value = parts[0] || '';
                document.getElementById('size_length').value = parts[1] || '';
            } else {
                document.getElementById('size_format').value = 'area';
                document.getElementById('size_area').value = rawValue;
            }
            toggleSizeFields();

            addAddon('Breakfast', 50000, 'per_guest_per_night', 'Enable breakfast addon');
            addAddon('Extra Bed', 150000, 'per_night', 'Enable extra bed');
            addAddon('Late Check-out', 100000, 'flat_fee', 'Enable late check-out');
        });
    </script>
</body>
</html>
