<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Activities - Bagus Guest House</title>
    @fonts
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-white text-gray-900 font-sans">
    @include('components.navbar')

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-gray-900 to-gray-800 py-16 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold mb-2">Activities & Experiences</h1>
            <p class="text-gray-300">Discover amazing adventures during your stay</p>
        </div>
    </section>

    <!-- Activities Grid -->
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @php
                    $activities = [
                        [
                            'icon' => 'directions_car',
                            'title' => 'Mount Batur Jeep Tour',
                            'desc' => 'Experience a thrilling jeep tour through the Kuta volcanic landscape. Explore mountain terrain with stunning natural views.',
                            'price' => 500000,
                            'duration' => '4-5 hours'
                        ],
                        [
                            'icon' => 'hiking',
                            'title' => 'Sunrise Trekking on Mount Batur',
                            'desc' => 'A guided jeep-assisted sunrise trek to Mount Batur\'s summit. Reach around 1,350m elevation for breathtaking dawn views.',
                            'price' => 750000,
                            'duration' => '6-7 hours'
                        ],
                        [
                            'icon' => 'grass',
                            'title' => 'Rice Terrace Hiking',
                            'desc' => 'Walk through beautiful rice paddies and local villages. Learn about traditional farming from local guides.',
                            'price' => 400000,
                            'duration' => '3-4 hours'
                        ],
                        [
                            'icon' => 'photo_camera',
                            'title' => 'Valley Photography Tour',
                            'desc' => 'Capture stunning landscapes and natural scenery with professional photography guides.',
                            'price' => 600000,
                            'duration' => '5 hours'
                        ],
                        [
                            'icon' => 'restaurant',
                            'title' => 'Cooking Class',
                            'desc' => 'Learn to prepare authentic local cuisine with our professional chefs. Perfect for food enthusiasts.',
                            'price' => 350000,
                            'duration' => '3 hours'
                        ],
                        [
                            'icon' => 'spa',
                            'title' => 'Spa & Wellness',
                            'desc' => 'Relax with traditional Balinese massage and spa treatments at our wellness center.',
                            'price' => 300000,
                            'duration' => '1.5-2 hours'
                        ],
                    ];
                @endphp
                
                @foreach ($activities as $activity)
                <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-400 to-orange-600 p-8 flex items-center justify-center h-32">
                        <span class="material-symbols-outlined text-white text-6xl select-none">{{ $activity['icon'] }}</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold mb-2">{{ $activity['title'] }}</h3>
                        <p class="text-gray-600 mb-4">{{ $activity['desc'] }}</p>
                        
                        <div class="mb-4 space-y-2 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Duration:</span>
                                <span class="font-semibold">{{ $activity['duration'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Price per person:</span>
                                <span class="font-bold text-orange-600">RP{{ number_format($activity['price'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <button onclick="alert('Booking feature coming soon!')" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 rounded-lg font-semibold transition">
                            Reserve Now
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold mb-4">Interested in Any Activity?</h2>
            <p class="text-gray-600 mb-6">Contact us for custom packages and group bookings</p>
            <a href="https://wa.me/6282169911168" target="_blank" class="inline-block bg-orange-600 hover:bg-orange-700 text-white px-8 py-3 rounded-lg font-semibold transition">
                Get In Touch
            </a>
        </div>
    </section>

    @include('components.footer')
</body>
</html>
