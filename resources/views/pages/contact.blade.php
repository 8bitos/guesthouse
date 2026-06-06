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
                                        Jl. Majapahit Gg. Muria<br>
                                        Kuta, Kec. Kuta<br>
                                        Kabupaten Badung, Bali 80361<br>
                                        Indonesia
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="text-3xl">📞</div>
                                <div>
                                    <h3 class="font-bold mb-2">Phone</h3>
                                    <p class="text-gray-600">
                                        <a href="tel:+6281916166616" class="hover:text-amber-700">+62 819-1616-6616</a>
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="text-3xl">✉️</div>
                                <div>
                                    <h3 class="font-bold mb-2">Email</h3>
                                    <p class="text-gray-600">
                                        <a href="mailto:bagusguesthouse01@gmail.com" class="hover:text-amber-700">bagusguesthouse01@gmail.com</a>
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="text-3xl">🕒</div>
                                <div>
                                    <h3 class="font-bold mb-2">Working Hours</h3>
                                    <p class="text-gray-600">
                                        Monday - Sunday<br>
                                        07:00 - 22:00 WITA<br>
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
                            <a href="https://wa.me/6281916166616" target="_blank" class="block bg-green-500 hover:bg-green-600 text-white p-3 rounded-lg text-center font-semibold transition">
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
            <div class="w-full h-96 rounded-xl overflow-hidden shadow-md border border-gray-200">
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
                <a href="https://maps.app.goo.gl/s1uf4jKDqPyzXSWp9" target="_blank" class="text-amber-700 hover:text-amber-800 font-semibold">
                    Open in Google Maps
                </a>
            </div>
        </div>
    </section>

    @include('components.footer')
</body>
</html>
