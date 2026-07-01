<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us - Bagus Guest House</title>
    @fonts
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-white text-gray-900 font-sans">
    @include('components.navbar')

    <!-- Page Header -->
    <section class="relative bg-gradient-to-r from-gray-950 to-gray-850 py-16 text-white overflow-hidden flex items-center justify-center">
        <div class="absolute inset-0 opacity-25 bg-cover bg-center transition-transform duration-10000 scale-105" style="background-image: url('{{ asset('images/default_gallery/restaurant.png') }}')"></div>
        <div class="absolute inset-0 bg-black/45"></div>
        <div class="relative max-w-xl mx-auto px-4 text-center z-10">
            <h1 class="text-3xl md:text-4xl font-extrabold mb-3 tracking-tight animate-fade-in-up">Contact Us</h1>
            <p class="text-sm md:text-base text-gray-300 font-medium animate-fade-in-up-delay">Get in touch with our team for inquiries, special bookings, or local travel guidance.</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-12 md:py-16 bg-gray-50/30">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">
                <!-- Contact Form -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-250/50 p-6 md:p-7 lg:col-span-3 reveal">
                    <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-700 font-bold select-none">edit_note</span>
                        Send Us a Message
                    </h2>
                    <form action="{{ route('contact.send') }}" method="POST" class="space-y-4">
                        @csrf

                        @if (session('success'))
                            <div class="p-4 text-sm text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center gap-2" role="alert">
                                <span class="material-symbols-outlined text-emerald-700 text-lg leading-none select-none">check_circle</span>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="p-4 text-sm text-rose-800 bg-rose-50 border border-rose-200 rounded-lg flex flex-col gap-1.5" role="alert">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-rose-700 text-lg leading-none select-none">error</span>
                                    <span class="font-bold">Please correct the following errors:</span>
                                </div>
                                <ul class="list-disc list-inside text-xs pl-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div>
                            <label class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1.5">Full Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:border-amber-750 focus:ring-2 focus:ring-amber-500/10 text-sm font-medium transition duration-200" placeholder="Your name" required>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1.5">Email Address</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:border-amber-750 focus:ring-2 focus:ring-amber-500/10 text-sm font-medium transition duration-200" placeholder="your@email.com" required>
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1.5">Phone Number</label>
                                <input type="tel" name="phone" value="{{ old('phone') }}" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:border-amber-750 focus:ring-2 focus:ring-amber-500/10 text-sm font-medium transition duration-200" placeholder="+62 XXX-XXXX-XXXX" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1.5">Subject</label>
                            <input type="text" name="subject" value="{{ old('subject') }}" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:border-amber-750 focus:ring-2 focus:ring-amber-500/10 text-sm font-medium transition duration-200" placeholder="Booking / Tour Inquiry" required>
                        </div>
                        <div>
                            <label class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1.5">Message</label>
                            <textarea name="message" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:border-amber-750 focus:ring-2 focus:ring-amber-500/10 h-28 text-sm font-medium transition duration-200 resize-none" placeholder="Your message details..." required>{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="w-full bg-amber-700 hover:bg-amber-800 text-white py-2.5 rounded-lg font-bold shadow-sm hover:shadow-amber-800/10 hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 text-sm">
                            Send Message
                        </button>
                    </form>
                </div>

                <!-- Contact Info -->
                <div class="space-y-6 lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-250/50 p-6 md:p-7 reveal delay-75">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-amber-700 font-bold select-none">contact_phone</span>
                            Contact Info
                        </h2>
                        
                        <div class="space-y-6">
                            <div class="flex gap-3.5 items-start">
                                <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center text-amber-700 shrink-0 select-none border border-amber-100/60">
                                    <span class="material-symbols-outlined text-[20px] font-bold">pin_drop</span>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-gray-950 text-xs mb-0.5 uppercase tracking-wider text-gray-400">Address</h3>
                                    <p class="text-gray-650 text-sm leading-relaxed font-medium">
                                        Jl. Majapahit Gg. Muria, Kuta<br>
                                        Kec. Kuta, Kabupaten Badung<br>
                                        Bali 80361, Indonesia
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-3.5 items-start">
                                <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center text-amber-700 shrink-0 select-none border border-amber-100/60">
                                    <span class="material-symbols-outlined text-[20px] font-bold">phone_in_talk</span>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-gray-950 text-xs mb-0.5 uppercase tracking-wider text-gray-400">Phone & WhatsApp</h3>
                                    <p class="text-gray-650 text-sm leading-relaxed">
                                        <a href="tel:+6281916166616" class="hover:text-amber-700 font-bold transition">+62 819-1616-6616</a>
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-3.5 items-start">
                                <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center text-amber-700 shrink-0 select-none border border-amber-100/60">
                                    <span class="material-symbols-outlined text-[20px] font-bold">mail</span>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-gray-950 text-xs mb-0.5 uppercase tracking-wider text-gray-400">Email Address</h3>
                                    <p class="text-gray-650 text-sm leading-relaxed">
                                        <a href="mailto:bagusguesthouse01@gmail.com" class="hover:text-amber-700 font-bold transition">bagusguesthouse01@gmail.com</a>
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-3.5 items-start">
                                <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center text-amber-700 shrink-0 select-none border border-amber-100/60">
                                    <span class="material-symbols-outlined text-[20px] font-bold">schedule</span>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-gray-950 text-xs mb-0.5 uppercase tracking-wider text-gray-400">Working Hours</h3>
                                    <p class="text-gray-650 text-sm leading-relaxed font-medium">
                                        Monday - Sunday: 07:00 - 22:00 WITA<br>
                                        <span class="text-xs text-amber-700 font-bold">Available 24/7 for online bookings</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="bg-amber-50/30 border border-amber-100 rounded-xl p-6 reveal delay-150">
                        <h3 class="font-extrabold text-gray-900 mb-4 text-sm flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-amber-700 select-none text-lg">quick_reference_all</span>
                            Quick Contact Options
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <a href="https://wa.me/6281916166616" target="_blank" class="flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded-lg text-center text-xs font-bold shadow-sm active:scale-[0.98] transition-all duration-200">
                                WhatsApp
                            </a>
                            <a href="https://facebook.com/bagusguesthouse" target="_blank" class="flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-center text-xs font-bold shadow-sm active:scale-[0.98] transition-all duration-200">
                                Facebook
                            </a>
                            <a href="https://instagram.com/bagusguesthouse" target="_blank" class="flex items-center justify-center bg-gradient-to-r from-purple-600 via-pink-500 to-orange-500 hover:opacity-90 text-white py-2 rounded-lg text-center text-xs font-bold shadow-sm active:scale-[0.98] transition-all duration-200">
                                Instagram
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="py-12 bg-white border-t border-gray-100">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 reveal">
            <h2 class="text-xl md:text-2xl font-extrabold text-gray-950 mb-6 text-center">Our Location</h2>
            <div class="w-full h-72 md:h-80 rounded-xl overflow-hidden shadow-sm border border-gray-200">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3943.8967912440316!2d115.17474297451246!3d-8.712592888802875!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd24761c389c3b7%3A0x153ba8a02256a908!2sBagus%20Guest%20House!5e0!3m2!1sen!2sid!4v1717650000000!5m2!1sen!2sid" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            <div class="text-center mt-4">
                <a href="https://maps.app.goo.gl/s1uf4jKDqPyzXSWp9" target="_blank" class="inline-flex items-center gap-1 text-amber-700 hover:text-amber-800 font-bold group text-xs">
                    Open in Google Maps
                    <span class="material-symbols-outlined text-xs leading-none group-hover:translate-x-0.5 transition-transform duration-200">arrow_right_alt</span>
                </a>
            </div>
        </div>
    </section>

    @include('components.footer')
</body>
</html>
