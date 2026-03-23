<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ApplyFlow')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Navbar Styles */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            z-index: 1000;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 15px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .navbar.hidden {
            transform: translateY(-120%);
            opacity: 0;
        }

        .navbar.showing {
            transform: translateY(0);
            opacity: 1;
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .navbar-brand:hover {
            color: #8b5cf6;
            transform: scale(1.05);
        }

        .navbar-brand::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #8b5cf6, #06b6d4);
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .navbar-brand:hover::after {
            width: 100%;
        }

        .navbar-nav {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .navbar-nav a {
            color: #333;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .navbar-nav a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(139, 92, 246, 0.1), transparent);
            transition: left 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .navbar-nav a:hover::before {
            left: 100%;
        }

        .navbar-nav a:hover {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            color: #8b5cf6;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.15);
        }

        .navbar-nav .logout-btn {
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .navbar-nav .logout-btn:hover {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #dc3545;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.15);
            border-color: rgba(220, 53, 69, 0.3);
        }

        .lang-switcher {
            border-right: 1px solid rgba(0,0,0,0.1);
            padding-right: 15px;
            margin-right: 10px;
            display: flex;
            gap: 10px;
        }

        .lang-switcher a {
            text-decoration: none;
            font-weight: normal;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 6px 8px;
            border-radius: 6px;
        }

        .lang-switcher a:hover {
            background: rgba(139, 92, 246, 0.1);
            transform: scale(1.1);
        }

        .lang-switcher a.active {
            font-weight: bold;
            color: #8b5cf6;
            background: rgba(139, 92, 246, 0.1);
        }

        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
            transform: scale(0.8) translateY(20px);
        }

        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
            transform: scale(1) translateY(0);
        }

        .back-to-top:hover {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            transform: scale(1.1) translateY(-2px);
            box-shadow: 0 12px 35px rgba(139, 92, 246, 0.4);
        }

        .back-to-top:active {
            transform: scale(0.95) translateY(0);
        }

        /* Pulse animation for back to top */
        @keyframes pulse {
            0% { box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3); }
            50% { box-shadow: 0 8px 25px rgba(139, 92, 246, 0.5); }
            100% { box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3); }
        }

        .back-to-top.visible {
            animation: pulse 2s infinite;
        }

        /* Content padding to account for fixed navbar */
        .content-wrapper {
            padding-top: 80px;
        }

        /* Logout form */
        #logout-form {
            display: none;
        }

        /* Loading states */
        .navbar-nav a.loading {
            opacity: 0.7;
            pointer-events: none;
        }

        /* Mobile responsiveness improvements */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: #333;
            font-size: 24px;
            cursor: pointer;
            margin-left: auto;
        }

        @media (max-width: 1024px) {
            .navbar-container {
                justify-content: space-between;
            }
        }

        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }

            .navbar-nav {
                position: fixed;
                top: 68px;
                right: 0;
                left: 0;
                z-index: 999;
                flex-direction: column;
                background: rgba(255, 255, 255, 0.98);
                border-top: 1px solid #e5e7eb;
                gap: 0;
                padding: 15px 20px;
                transform: translateY(-120%);
                opacity: 0;
                pointer-events: none;
                transition: transform 0.25s ease-out, opacity 0.2s ease;
            }

            .navbar-nav.open {
                transform: translateY(0);
                opacity: 1;
                pointer-events: auto;
            }

            .navbar-nav > * {
                width: 100%;
                margin-bottom: 10px;
            }

            .navbar-nav a {
                width: 100%;
                padding: 10px 14px;
                font-size: 14px;
                border-radius: 6px;
            }

            .lang-switcher {
                width: 100%;
                display: flex;
                justify-content: space-between;
                padding-right: 0;
                margin-right: 0;
                border-bottom: 1px solid #e5e7eb;
                padding-bottom: 10px;
                margin-bottom: 10px;
            }

            .lang-switcher a {
                padding: 8px 10px;
                font-size: 13px;
            }

            .content-wrapper {
                padding-top: 120px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar" id="navbar">
        <div class="navbar-container">
            <a href="/dashboard" class="navbar-brand">ApplyFlow</a>

            <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle navigation">☰</button>

            <div class="navbar-nav" id="navbarNav">
                <div class="lang-switcher">
                    <a href="/idioma/pt" class="{{ session('locale', 'pt') == 'pt' ? 'active' : '' }}">🇧🇷 PT</a>
                    <a href="/idioma/en" class="{{ session('locale') == 'en' ? 'active' : '' }}">🇺🇸 EN</a>
                    <a href="/idioma/es" class="{{ session('locale') == 'es' ? 'active' : '' }}">🇪🇸 ES</a>
                </div>
                
                <a href="/profile">{{ __('Editar Perfil') }}</a>
                <a href="/meu-curriculo">{{ __('Ver CV') }}</a>
                <a href="/resume/download">{{ __('Baixar CV') }}</a>
                <a href="#" onclick="document.getElementById('logout-form').submit();" class="logout-btn">{{ __('Sair') }}</a>
            </div>
        </div>
    </nav>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" onclick="scrollToTop()">
        ↑
    </button>

    <!-- Logout Form -->
    <form id="logout-form" action="/logout" method="POST">
        @csrf
    </form>

    <!-- Main Content -->
    <div class="content-wrapper">
        @yield('content')
    </div>

    <script>
        // Enhanced navbar hide/show on scroll with smooth animations
        let lastScrollTop = 0;
        let scrollTimeout;
        let isScrolling = false;
        const navbar = document.getElementById('navbar');
        const backToTop = document.getElementById('backToTop');

        // Debounced scroll handler for better performance
        function debounceScroll() {
            if (scrollTimeout) {
                clearTimeout(scrollTimeout);
            }

            scrollTimeout = setTimeout(() => {
                handleScroll();
            }, 16); // ~60fps
        }

        function handleScroll() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollDelta = Math.abs(scrollTop - lastScrollTop);

            // Only trigger animations if scroll delta is significant
            if (scrollDelta < 5) return;

            // Prevent rapid toggling
            if (isScrolling) return;
            isScrolling = true;

            setTimeout(() => {
                isScrolling = false;
            }, 150);

            // Enhanced navbar logic
            if (scrollTop > lastScrollTop && scrollTop > 150) {
                // Scrolling down and past 150px - hide navbar
                navbar.classList.add('hidden');
                navbar.classList.remove('showing');
            } else if (scrollTop < lastScrollTop) {
                // Scrolling up - show navbar
                navbar.classList.remove('hidden');
                navbar.classList.add('showing');
            }

            // Enhanced back to top button logic
            if (scrollTop > 400) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }

            lastScrollTop = scrollTop;
        }

        // Throttled scroll listener
        let ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(function() {
                    handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });

        // Enhanced smooth scroll to top with easing
        function scrollToTop() {
            const startPosition = window.pageYOffset;
            const targetPosition = 0;
            const distance = startPosition - targetPosition;
            const duration = Math.min(800, Math.abs(distance) * 0.8); // Dynamic duration based on distance
            let startTime = null;

            function easeInOutCubic(t) {
                return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
            }

            function animation(currentTime) {
                if (startTime === null) startTime = currentTime;
                const timeElapsed = currentTime - startTime;
                const progress = Math.min(timeElapsed / duration, 1);

                const easeProgress = easeInOutCubic(progress);
                const currentPosition = startPosition - (distance * easeProgress);

                window.scrollTo(0, currentPosition);

                if (progress < 1) {
                    requestAnimationFrame(animation);
                }
            }

            requestAnimationFrame(animation);
        }

        // Add loading states to navigation links
        document.querySelectorAll('.navbar-nav a:not(.logout-btn)').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!this.classList.contains('loading')) {
                    this.classList.add('loading');
                    // Remove loading class after navigation (fallback)
                    setTimeout(() => {
                        this.classList.remove('loading');
                    }, 1000);
                }
            });
        });

        // Enhanced hover effects for better UX
        document.querySelectorAll('.navbar-nav a').forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px) scale(1.02)';
            });

            link.addEventListener('mouseleave', function() {
                this.style.transform = '';
            });
        });

        // Initialize navbar state on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Force reflow to ensure initial state
            navbar.offsetHeight;

            // Add initial showing class
            navbar.classList.add('showing');

            // Check initial scroll position
            if (window.pageYOffset > 400) {
                backToTop.classList.add('visible');
            }

            // Navbar mobile toggle
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const navbarNav = document.getElementById('navbarNav');

            if (mobileMenuToggle && navbarNav) {
                mobileMenuToggle.addEventListener('click', function() {
                    navbarNav.classList.toggle('open');
                });

                window.addEventListener('resize', function() {
                    if (window.innerWidth > 768) {
                        navbarNav.classList.remove('open');
                    }
                });
            }
        });
    </script>

    @yield('scripts')
</body>
</html>