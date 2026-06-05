<nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-amber-700">
                    Bagus Guest House
                </a>
            </div>
            
            <!-- Menu -->
            <div class="hidden md:flex gap-8">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-amber-700">Home</a>
                <a href="{{ route('rooms') }}" class="text-gray-700 hover:text-amber-700">Rooms</a>
                <a href="{{ route('gallery') }}" class="text-gray-700 hover:text-amber-700">Gallery</a>
                <a href="{{ route('contact') }}" class="text-gray-700 hover:text-amber-700">Contact</a>
            </div>
            
            <!-- CTA Buttons -->
            <div class="flex gap-4 items-center">
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-gray-700 hover:text-orange-600">Dashboard</a>
                    <form method="POST" action="{{ url('/logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-orange-600">Logout</button>
                    </form>
                @else
                    <a href="#" onclick="alert('Login feature coming soon')" class="text-gray-700 hover:text-amber-700">Login</a>
                    <a href="#" onclick="alert('Register feature coming soon')" class="bg-amber-700 hover:bg-amber-800 text-white px-4 py-2 rounded-lg">Register</a>
                @endauth
                <a href="https://wa.me/6282169911168" target="_blank" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg hidden sm:inline-block">
                    WhatsApp
                </a>
            </div>
        </div>
    </div>
</nav>
