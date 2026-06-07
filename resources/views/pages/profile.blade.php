<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile Settings - Bagus Guest House</title>
    @fonts
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            @import 'tailwindcss';
        </style>
    @endif
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .animate-fade-in-up-delay {
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.15s forwards;
            opacity: 0;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans min-h-screen flex flex-col justify-between">
    @include('components.navbar')

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-gray-950 to-gray-850 py-16 text-white text-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-cover bg-center" style="background-image: url('{{ asset('images/default_gallery/bedroom.png') }}')"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight animate-fade-in-up">Profile Settings</h1>
            <p class="text-sm md:text-base text-gray-300 mt-2 font-medium animate-fade-in-up-delay">Update your personal information and security details</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex-grow w-full">
        <!-- Success and Error Notifications -->
        @if (session('success'))
            <div class="max-w-4xl mx-auto mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-sm font-semibold flex items-center gap-2 shadow-sm animate-fade-in-up">
                <span class="material-symbols-outlined text-emerald-600 text-base">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="max-w-4xl mx-auto mb-6 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl p-4 text-sm font-semibold flex flex-col gap-1 shadow-sm animate-fade-in-up">
                @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-rose-600 text-base">error</span>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Left Side: Profile Info Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 flex flex-col justify-between h-fit reveal">
                <div class="text-center pb-6 border-b border-gray-100">
                    <div class="w-20 h-20 bg-amber-100 text-amber-800 rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-4 select-none shadow-inner">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 leading-snug">{{ $user->name }}</h3>
                    <span class="inline-block bg-amber-50 text-amber-800 text-[10px] font-extrabold uppercase tracking-widest px-3 py-1 rounded-full mt-2 border border-amber-100">
                        {{ $user->role === 'pelanggan' ? 'Guest' : $user->role }}
                    </span>
                </div>

                <div class="py-6 space-y-4 text-left">
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Email Address</span>
                        <span class="text-gray-800 text-xs font-semibold break-all">{{ $user->email }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Phone Number</span>
                        <span class="text-gray-800 text-xs font-semibold">{{ $user->phone ?? 'Not provided' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Home Address</span>
                        <p class="text-gray-700 text-xs font-semibold mt-1 bg-gray-50 p-3 rounded-xl border border-gray-150/40 leading-relaxed min-h-[64px]">
                            {{ $user->address ?? 'Not provided' }}
                        </p>
                    </div>
                </div>

                @if($user->role === 'pelanggan')
                    <div class="pt-4 border-t border-gray-100">
                        <a href="{{ route('dashboard') }}" class="w-full bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 py-2.5 px-4 rounded-xl font-bold text-xs uppercase tracking-wider transition-all duration-200 block text-center shadow-sm hover:scale-[1.02] active:scale-[0.98]">
                            Back to Dashboard
                        </a>
                    </div>
                @else
                    <div class="pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.dashboard') }}" class="w-full bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 py-2.5 px-4 rounded-xl font-bold text-xs uppercase tracking-wider transition-all duration-200 block text-center shadow-sm hover:scale-[1.02] active:scale-[0.98]">
                            Back to Dashboard
                        </a>
                    </div>
                @endif
            </div>

            <!-- Right Side: Edit Form Card -->
            <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200/80 p-8 reveal delay-75">
                <h3 class="text-base font-extrabold text-gray-800 uppercase tracking-wider mb-6 flex items-center gap-2 border-b border-gray-100 pb-4">
                    <span class="material-symbols-outlined text-amber-700">edit_note</span>
                    <span>Edit Profile Details</span>
                </h3>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div class="space-y-1.5">
                        <label for="name" class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-xs text-gray-800 transition focus:bg-white focus:border-amber-700 focus:outline-none shadow-sm font-semibold">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Email (Disabled/Read-only) -->
                        <div class="space-y-1.5 opacity-70">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Email Address</label>
                            <input type="text" value="{{ $user->email }}" disabled
                                   class="w-full bg-gray-100 border border-gray-200 rounded-xl px-4 py-3 text-xs text-gray-500 font-semibold cursor-not-allowed">
                            <span class="text-[9px] text-gray-400">Email addresses cannot be changed.</span>
                        </div>

                        <!-- Phone -->
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Phone Number</label>
                            <div class="flex gap-2">
                                <select name="country_code" class="w-24 shrink-0 bg-gray-50 border border-gray-200 rounded-xl px-2 py-3 text-[11px] text-gray-700 transition focus:bg-white focus:border-amber-700 focus:outline-none shadow-sm font-bold cursor-pointer">
                                    @php
                                        $countries = config('countries.countries');
                                    @endphp
                                    @foreach($countries as $c)
                                        <option value="{{ $c['dial'] }}" {{ old('country_code', $countryCode) === $c['dial'] ? 'selected' : '' }}>
                                            {{ $c['flag'] }} {{ $c['code'] }} ({{ $c['dial'] }})
                                        </option>
                                    @endforeach
                                </select>
                                <input id="phone" type="text" name="phone" value="{{ old('phone', $phoneNumber) }}"
                                       class="flex-1 min-w-0 bg-transparent border border-gray-200 rounded-xl px-4 py-3 text-xs text-gray-800 transition focus:bg-white focus:border-amber-700 focus:outline-none shadow-sm font-semibold"
                                       placeholder="8123456789">
                            </div>
                        </div>
                    </div>

                    <!-- Home Address -->
                    <div class="space-y-1.5 relative">
                        <label for="address" class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Home Address</label>
                        <div class="relative">
                            <input id="address" type="text" name="address" value="{{ old('address', $user->address) }}" autocomplete="off"
                                   class="w-full bg-transparent border border-gray-200 rounded-xl px-4 py-3 text-xs text-gray-800 transition focus:bg-white focus:border-amber-700 focus:outline-none shadow-sm font-semibold"
                                   placeholder="Search and select address (worldwide)..." oninput="searchAddress(this.value)">
                            <!-- Search loader/indicator spinner -->
                            <div id="address-loader" class="absolute right-4 top-3.5 hidden">
                                <svg class="animate-spin h-4 w-4 text-amber-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        <!-- Dropdown list results -->
                        <div id="address-results" class="absolute left-0 right-0 mt-1 bg-white border border-gray-250 rounded-xl shadow-xl z-50 max-h-60 overflow-y-auto hidden divide-y divide-gray-150"></div>
                    </div>

                    <!-- Divider -->
                    <hr class="border-t border-gray-100 my-6">

                    <!-- Password Section -->
                    <div>
                        <h4 class="text-xs font-bold text-amber-800 flex items-center gap-1">
                            <span class="material-symbols-outlined text-base">lock</span>
                            <span>Change Password (Optional)</span>
                        </h4>
                        <p class="text-[10px] text-gray-400 mt-0.5 font-medium">To change your password, you must enter your current password first.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <!-- Current Password -->
                        <div class="space-y-1.5">
                            <label for="current_password" class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Current Password</label>
                            <input id="current_password" type="password" name="current_password" autocomplete="current-password"
                                   class="w-full bg-transparent border border-gray-200 rounded-xl px-4 py-3 text-xs text-gray-800 transition focus:bg-white focus:border-amber-700 focus:outline-none shadow-sm font-semibold"
                                   placeholder="Enter current password">
                        </div>

                        <!-- Password -->
                        <div class="space-y-1.5">
                            <label for="password" class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">New Password</label>
                            <input id="password" type="password" name="password" autocomplete="new-password"
                                   class="w-full bg-transparent border border-gray-200 rounded-xl px-4 py-3 text-xs text-gray-800 transition focus:bg-white focus:border-amber-700 focus:outline-none shadow-sm font-semibold"
                                   placeholder="Min 8 characters">
                        </div>

                        <!-- Confirm Password -->
                        <div class="space-y-1.5">
                            <label for="password_confirmation" class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Confirm Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"
                                   class="w-full bg-transparent border border-gray-200 rounded-xl px-4 py-3 text-xs text-gray-800 transition focus:bg-white focus:border-amber-700 focus:outline-none shadow-sm font-semibold"
                                   placeholder="Repeat new password">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4 pt-4 justify-end border-t border-gray-100">
                        @if($user->role === 'pelanggan')
                            <a href="{{ route('dashboard') }}" class="px-6 py-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-500 font-bold text-xs uppercase tracking-wider transition cursor-pointer select-none text-center">
                                Cancel
                            </a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="px-6 py-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-500 font-bold text-xs uppercase tracking-wider transition cursor-pointer select-none text-center">
                                Cancel
                            </a>
                        @endif
                        <button type="submit"
                                class="px-6 py-2.5 rounded-xl bg-amber-700 hover:bg-amber-800 hover:scale-[1.02] active:scale-[0.98] text-white font-bold text-xs uppercase tracking-wider transition cursor-pointer select-none shadow-sm">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    @include('components.footer')

    <!-- Scroll reveal & Nominatim Address Search implementation -->
    <script>
        let addressTimeout = null;

        function searchAddress(query) {
            const resultsContainer = document.getElementById('address-results');
            const loader = document.getElementById('address-loader');

            if (!query || query.trim().length < 3) {
                resultsContainer.innerHTML = '';
                resultsContainer.classList.add('hidden');
                return;
            }

            clearTimeout(addressTimeout);
            loader.classList.remove('hidden');

            addressTimeout = setTimeout(() => {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`)
                    .then(response => response.json())
                    .then(data => {
                        loader.classList.add('hidden');
                        resultsContainer.innerHTML = '';
                        
                        if (data.length > 0) {
                            resultsContainer.classList.remove('hidden');
                            data.forEach(item => {
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.className = 'w-full text-left px-4 py-3.5 text-xs hover:bg-amber-50/50 text-gray-700 font-semibold transition flex items-start gap-2.5 border-b border-gray-100 last:border-0 focus:outline-none cursor-pointer';
                                btn.innerHTML = `
                                    <span class="material-symbols-outlined text-amber-700 text-base shrink-0 mt-0.5 select-none">pin_drop</span>
                                    <span>${item.display_name}</span>
                                `;
                                btn.onclick = () => {
                                    document.getElementById('address').value = item.display_name;
                                    resultsContainer.innerHTML = '';
                                    resultsContainer.classList.add('hidden');
                                };
                                resultsContainer.appendChild(btn);
                            });
                        } else {
                            resultsContainer.classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching address:', error);
                        loader.classList.add('hidden');
                    });
            }, 600);
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            const resultsContainer = document.getElementById('address-results');
            const addressInput = document.getElementById('address');
            if (resultsContainer && addressInput && e.target !== addressInput && !resultsContainer.contains(e.target)) {
                resultsContainer.classList.add('hidden');
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        observer.unobserve(entry.target);
                    }
                });
            }, { rootMargin: '0px 0px -40px 0px' });

            document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
        });
    </script>
</body>
</html>
