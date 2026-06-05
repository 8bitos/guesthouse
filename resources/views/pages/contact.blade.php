<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us - Bagus Guest House</title>
    @fonts
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-white text-gray-900 font-sans">
    @include('components.navbar')

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-gray-900 to-gray-800 py-16 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold mb-2">Contact Us</h1>
            <p class="text-gray-300">Get in touch with our team for inquiries and bookings</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold mb-6">Send Us a Message</h2>
                    <form class="space-y-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Full Name</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-700" placeholder="Your name" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Email</label>
                            <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-700" placeholder="your@email.com" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Phone Number</label>
                            <input type="tel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-700" placeholder="+62 XXX-XXXX-XXXX" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Subject</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-700" placeholder="Subject" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Message</label>
                            <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-700 h-32" placeholder="Your message"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-amber-700 hover:bg-amber-800 text-white py-3 rounded-lg font-semibold transition">
                            Send Message
                        </button>
                    </form>
                </div>

                <!-- Contact Info -->
                <div class="space-y-8">
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <h2 class="text-2xl font-bold mb-6">Contact Information</h2>
                        
                        <div class="space-y-6">
                            <div class="flex gap-4">
                                <div class="text-3xl">📍</div>
                                <div>
                                    <h3 class="font-bold mb-2">Address</h3>
                                    <p class="text-gray-600">
                                        Jalan Bukit Payang No.88<br>
                                        Desa Batur Tengah<br>
                                        Kintamani, Bali 80652<br>
                                        Indonesia
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="text-3xl">📞</div>
                                <div>
                                    <h3 class="font-bold mb-2">Phone</h3>
                                    <p class="text-gray-600">
                                        <a href="tel:+6236109088221" class="hover:text-amber-700">+62 361-9088-221</a><br>
                                        <a href="tel:+6282169911168" class="hover:text-amber-700">+62 821-6991-1168</a>
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="text-3xl">✉️</div>
                                <div>
                                    <h3 class="font-bold mb-2">Email</h3>
                                    <p class="text-gray-600">
                                        <a href="mailto:info@bagusguesthouse.com" class="hover:text-amber-700">info@bagusguesthouse.com</a>
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="text-3xl">🕒</div>
                                <div>
                                    <h3 class="font-bold mb-2">Working Hours</h3>
                                    <p class="text-gray-600">
                                        Monday - Sunday<br>
                                        07:00 - 22:00 WIB<br>
                                        Available 24/7 for bookings
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="bg-amber-50 rounded-lg p-8">
                        <h3 class="font-bold mb-4">Quick Contact Options</h3>
                        <div class="space-y-3">
                            <a href="https://wa.me/6282169911168" target="_blank" class="block bg-green-500 hover:bg-green-600 text-white p-3 rounded-lg text-center font-semibold transition">
                                Chat on WhatsApp
                            </a>
                            <a href="https://facebook.com/bagusguesthouse" target="_blank" class="block bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-lg text-center font-semibold transition">
                                Facebook
                            </a>
                            <a href="https://instagram.com/bagusguesthouse" target="_blank" class="block bg-pink-600 hover:bg-pink-700 text-white p-3 rounded-lg text-center font-semibold transition">
                                Instagram
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold mb-8 text-center">Our Location</h2>
            <div class="w-full h-96 bg-gradient-to-br from-gray-300 to-gray-400 rounded-lg flex items-center justify-center">
                <span class="text-gray-600">📍 Google Map Integration Coming Soon</span>
            </div>
            <div class="text-center mt-4">
                <a href="https://maps.app.goo.gl/Nn5N41gLRi2CPnnz6" target="_blank" class="text-amber-700 hover:text-amber-800 font-semibold">
                    Open in Google Maps
                </a>
            </div>
        </div>
    </section>

    @include('components.footer')
</body>
</html>
