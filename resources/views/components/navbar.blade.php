<nav class="bg-white/80 backdrop-blur-md border-b border-gray-150/40 sticky top-0 z-50 animate-fade-in-down">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-1.5 group select-none">
                    <span class="material-symbols-outlined text-amber-700 text-2xl group-hover:rotate-12 transition-transform duration-300">holiday_village</span>
                    <span class="text-xl font-black text-amber-800 tracking-tight">Bagus <span class="text-gray-800 font-light">Guest House</span></span>
                </a>
            </div>
            
            <!-- Menu -->
            <div class="hidden md:flex gap-8">
                <a href="{{ route('home') }}" class="nav-link text-xs uppercase font-extrabold tracking-wider transition-colors duration-200 {{ request()->routeIs('home') ? 'text-amber-700 active' : 'text-gray-600 hover:text-amber-700' }}">Home</a>
                <a href="{{ route('rooms') }}" class="nav-link text-xs uppercase font-extrabold tracking-wider transition-colors duration-200 {{ request()->routeIs('rooms') ? 'text-amber-700 active' : 'text-gray-600 hover:text-amber-700' }}">Rooms</a>
                <a href="{{ route('gallery') }}" class="nav-link text-xs uppercase font-extrabold tracking-wider transition-colors duration-200 {{ request()->routeIs('gallery') ? 'text-amber-700 active' : 'text-gray-600 hover:text-amber-700' }}">Gallery</a>
                <a href="{{ route('contact') }}" class="nav-link text-xs uppercase font-extrabold tracking-wider transition-colors duration-200 {{ request()->routeIs('contact') ? 'text-amber-700 active' : 'text-gray-600 hover:text-amber-700' }}">Contact</a>
            </div>
            
            <!-- CTA Buttons -->
            <div class="flex gap-4 items-center">
                @auth
                    <!-- User Dropdown Menu -->
                    <div class="relative group select-none cursor-pointer" onclick="toggleUserDropdown(event)">
                        <button class="flex items-center gap-1 text-xs uppercase font-extrabold tracking-wider text-gray-700 hover:text-amber-700 focus:outline-none transition duration-200 select-none">
                            <span class="material-symbols-outlined text-lg leading-none">account_circle</span>
                            <span>{{ auth()->user()->name }}</span>
                            <span id="user-dropdown-arrow" class="material-symbols-outlined text-base leading-none transition-transform duration-200">expand_more</span>
                        </button>
                        
                        <!-- Dropdown Menu items -->
                        <div id="user-dropdown-menu" class="absolute right-0 mt-2.5 w-44 rounded-xl bg-white border border-gray-200/80 shadow-lg py-1.5 z-50 origin-top-right transition-all duration-200 scale-95 opacity-0 pointer-events-none group-hover:scale-100 group-hover:opacity-100 group-hover:pointer-events-auto">
                            <!-- Dashboard -->
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-[10px] font-extrabold uppercase tracking-wider text-gray-700 hover:bg-amber-50 hover:text-amber-700 transition duration-150">
                                    <span class="material-symbols-outlined text-base leading-none">dashboard</span>
                                    <span>Dashboard</span>
                                </a>
                            @else
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-[10px] font-extrabold uppercase tracking-wider text-gray-700 hover:bg-amber-50 hover:text-amber-700 transition duration-150">
                                    <span class="material-symbols-outlined text-base leading-none">dashboard</span>
                                    <span>Dashboard</span>
                                </a>
                            @endif
                            
                            <!-- Profile Settings -->
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-[10px] font-extrabold uppercase tracking-wider text-gray-700 hover:bg-amber-50 hover:text-amber-700 transition duration-150">
                                <span class="material-symbols-outlined text-base leading-none">manage_accounts</span>
                                <span>Profile Settings</span>
                            </a>
                            
                            <hr class="border-t border-gray-100 my-1">
                            
                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}" class="block w-full">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-[10px] font-extrabold uppercase tracking-wider text-red-600 hover:bg-red-50 transition duration-150 cursor-pointer select-none">
                                    <span class="material-symbols-outlined text-base leading-none">logout</span>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-xs uppercase font-extrabold tracking-wider text-gray-600 hover:text-amber-700 transition duration-200 select-none">Login</a>
                    <a href="{{ route('register') }}" class="bg-amber-700 hover:bg-amber-800 hover:scale-[1.03] text-white px-4 py-2 rounded-lg font-bold text-xs uppercase tracking-wider transition duration-300 shadow-sm hover:shadow-md select-none">Register</a>
                @endauth
                <a href="https://wa.me/6281916166616" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 hover:scale-[1.03] text-white px-4 py-2.5 rounded-lg text-xs uppercase font-extrabold tracking-wider transition duration-300 shadow-sm hover:shadow-md flex items-center gap-1.5 select-none shrink-0">
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.963C16.528 2.017 14.077.99 11.452.99 6.012.99 1.587 5.36 1.583 10.79c-.001 1.645.43 3.254 1.248 4.667l-1.02 3.723 3.836-1.027zM18.06 14.86c-.33-.164-1.942-.954-2.242-1.063-.3-.11-.518-.165-.735.163-.218.328-.842 1.063-1.03 1.28-.19.219-.377.247-.707.083-.33-.164-1.393-.51-2.653-1.631-.98-.871-1.642-1.948-1.835-2.275-.192-.328-.02-.505.145-.668.148-.147.33-.382.495-.572.164-.19.218-.328.327-.546.11-.218.055-.41-.027-.573-.082-.164-.735-1.77-.993-2.396-.251-.611-.508-.528-.695-.538-.178-.01-.382-.01-.587-.01-.205 0-.538.077-.82.383-.28.307-1.072 1.043-1.072 2.544s1.09 2.946 1.241 3.149c.15.203 2.146 3.27 5.197 4.578.726.311 1.293.498 1.735.638.73.23 1.397.198 1.925.12.587-.087 1.942-.792 2.215-1.528.273-.736.273-1.363.19-1.528-.083-.163-.306-.245-.636-.409z"/>
                    </svg>
                    <span>WhatsApp</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
    if (window.self !== window.top) {
        document.body.classList.add('in-iframe');
    }

    // Toggle dropdown function
    function toggleUserDropdown(event) {
        event.stopPropagation();
        const dropdown = document.getElementById('user-dropdown-menu');
        const arrow = document.getElementById('user-dropdown-arrow');
        
        if (dropdown.classList.contains('scale-100')) {
            dropdown.classList.remove('scale-100', 'opacity-100', 'pointer-events-auto');
            dropdown.classList.add('scale-95', 'opacity-0', 'pointer-events-none');
            arrow.classList.remove('rotate-180');
        } else {
            dropdown.classList.remove('scale-95', 'opacity-0', 'pointer-events-none');
            dropdown.classList.add('scale-100', 'opacity-100', 'pointer-events-auto');
            arrow.classList.add('rotate-180');
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', () => {
        const dropdown = document.getElementById('user-dropdown-menu');
        const arrow = document.getElementById('user-dropdown-arrow');
        if (dropdown && dropdown.classList.contains('scale-100')) {
            dropdown.classList.remove('scale-100', 'opacity-100', 'pointer-events-auto');
            dropdown.classList.add('scale-95', 'opacity-0', 'pointer-events-none');
            arrow.classList.remove('rotate-180');
        }
    });
</script>
<style>
    .nav-link {
        position: relative;
        padding-bottom: 4px;
    }
    .nav-link::after {
        content: '';
        position: absolute;
        width: 100%;
        transform: scaleX(0);
        height: 2px;
        bottom: 0;
        left: 0;
        background-color: #b45309;
        transform-origin: bottom right;
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .nav-link:hover::after {
        transform: scaleX(1);
        transform-origin: bottom left;
    }
    .nav-link.active::after {
        transform: scaleX(1);
        background-color: #b45309;
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in-down {
        animation: fadeInDown 0.65s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* Iframe adaptive mode styles */
    body.in-iframe nav,
    body.in-iframe footer {
        display: none !important;
    }
    body.in-iframe section.bg-gray-900 {
        padding-top: 1.25rem !important;
        padding-bottom: 1.25rem !important;
        background-color: #f9fafb !important;
        color: #111827 !important;
        border-bottom: 1px solid #e5e7eb !important;
    }
    body.in-iframe section.bg-gray-900 h1,
    body.in-iframe section.bg-gray-900 h2 {
        color: #111827 !important;
        font-size: 1.35rem !important;
    }
    body.in-iframe section.bg-gray-900 p {
        color: #4b5563 !important;
        font-size: 0.75rem !important;
    }
    body.in-iframe main {
        margin-top: 0 !important;
        padding-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
        max-width: 100% !important;
        width: 100% !important;
    }
    body.in-iframe {
        background-color: #ffffff !important;
    }
</style>
