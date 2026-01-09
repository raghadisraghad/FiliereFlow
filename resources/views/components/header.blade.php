<header class="header">
    <div class="container">
        <!-- Logo -->
        <a href="/" class="logo-link">
            <h2>FiliereFlow</h2>
        </a>

        <!-- Hamburger Menu Button -->
        <button class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- Desktop Navigation -->
        <div class="desktop-nav">
            <div class="nav-menu">
                <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">Home</a>
                
                <!-- Dashboard (for authenticated users) -->
                @auth
                    <a href="/dashboard" class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}">Dashboard</a>
                
                <!-- Filieres (for all users) -->
                    <a href="/filieres" class="nav-link {{ request()->is('filieres*') ? 'active' : '' }}">Filieres</a>
                
                <!-- Admin Only Links -->
                    @if(auth()->user()->type === 'admin')
                        <!-- Users Management (admin only) -->
                        <a href="/users" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">Users</a>                        
                    @endif
                @endauth
                
                <!-- Profile (for authenticated users) -->
                @auth
                    <a href="/profile" class="nav-link {{ request()->is('profile*') ? 'active' : '' }}">Profile</a>
                @endauth
            </div>

            <!-- Auth Buttons -->
            @if (Route::has('login'))
                @auth
                    <!-- Logout button -->
                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit" class="btn btn-secondary">Log Out</button>
                    </form>
                @else
                    <!-- Login and Register buttons for guests -->
                    <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-secondary">Register</a>
                    @endif
                @endauth
            @endif
        </div>

        <!-- Mobile Navigation Menu -->
        <div class="mobile-nav" id="mobileNav">
            <button class="close-menu" id="closeMenu">&times;</button>
            <div class="mobile-nav-links">
                <!-- Home (for all) -->
                <a href="/" class="mobile-nav-link {{ request()->is('/') ? 'active' : '' }}">Home</a>
                
                <!-- Dashboard (authenticated only) -->
                @auth
                    <a href="/dashboard" class="mobile-nav-link {{ request()->is('dashboard*') ? 'active' : '' }}">Dashboard</a>
                @endauth
                
                <!-- Filieres (for all) -->
                <a href="/filieres" class="mobile-nav-link {{ request()->is('filieres*') ? 'active' : '' }}">Filieres</a>
                
                <!-- Admin Only Links -->
                @auth
                    @if(auth()->user()->type === 'admin')
                        <!-- Users Management (admin only) -->
                        <a href="/users" class="mobile-nav-link {{ request()->is('users*') ? 'active' : '' }}">Users</a>
                        
                        <!-- Students Management (admin only) -->
                        <a href="/students" class="mobile-nav-link {{ request()->is('students*') ? 'active' : '' }}">Students</a>
                    @endif
                @endauth
                
                <!-- Profile (authenticated only) -->
                @auth
                    <a href="/profile" class="mobile-nav-link {{ request()->is('profile*') ? 'active' : '' }}">Profile</a>
                @endauth
                
                <!-- Auth buttons -->
                @auth
                    <!-- Logout button -->
                    <form method="POST" action="{{ route('logout') }}" class="logout-form" style="width: 100%;">
                        @csrf
                        <button type="submit" class="btn btn-secondary" style="width: 100%; margin-top: 1rem;">Log Out</button>
                    </form>
                @else
                    <!-- Login and Register for guests -->
                    <a href="{{ route('login') }}" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-secondary" style="width: 100%; margin-top: 0.5rem;">Register</a>
                    @endif
                @endauth
            </div>
        </div>
        
        <!-- Mobile Overlay -->
        <div class="mobile-overlay" id="mobileOverlay"></div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.getElementById('hamburger');
    const mobileNav = document.getElementById('mobileNav');
    const closeMenu = document.getElementById('closeMenu');
    const mobileOverlay = document.getElementById('mobileOverlay');
    const mobileLinks = document.querySelectorAll('.mobile-nav-link, .mobile-nav .btn, .mobile-nav .logout-form button');
    
    function openMenu() {
        hamburger.classList.add('active');
        mobileNav.classList.add('active');
        if (mobileOverlay) mobileOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeMobileMenu() {
        hamburger.classList.remove('active');
        mobileNav.classList.remove('active');
        if (mobileOverlay) mobileOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    hamburger.addEventListener('click', openMenu);
    closeMenu.addEventListener('click', closeMobileMenu);
    
    // Close menu when clicking overlay
    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', closeMobileMenu);
    }
    
    mobileLinks.forEach(link => {
        link.addEventListener('click', closeMobileMenu);
    });
    
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && mobileNav.classList.contains('active')) {
            closeMobileMenu();
        }
    });
    
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            closeMobileMenu();
        }
    });
});
</script>