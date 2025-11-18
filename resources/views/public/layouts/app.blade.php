<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Trang chủ | KÝ TÚC XÁ')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-blue: #0066CC;
            --dark-blue: #003d7a;
            --red-accent: #DC143C;
            --text-900: #111827;
            --text-700: #374151;
            --text-500: #6B7280;
            --surface: #ffffff;
            --surface-weak: #f8fafc;
            --border: #e5e7eb;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-900);
            background: var(--surface-weak);
        }
        
        .top-banner {
            background-color: var(--primary-blue);
            height: 5px;
        }
        
        .main-header {
            background-color: var(--dark-blue);
            color: white;
            padding: 14px 0;
            position: sticky;
            top: 0;
            z-index: 1020;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
        }
        
        .logo-section {
            display: flex;
            align-items: center;
        }
        
        .logo-text {
            background-color: var(--primary-blue);
            color: white;
            font-size: 24px;
            font-weight: 700;
            padding: 8px 16px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            letter-spacing: 0.3px;
            line-height: 1;
        }
        
        .logo-text .tdtu {
            border-bottom: 3px solid var(--red-accent);
            display: inline-block;
            padding-bottom: 2px;
        }
        
        .logo-img {
            height: 36px;
            width: auto;
            display: block;
            filter: drop-shadow(0 1px 2px rgba(0,0,0,0.12));
        }
        
        .main-nav {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 24px;
            flex-wrap: wrap;
        }
        
        .main-nav a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            position: relative;
            padding: 6px 2px;
            transition: color 0.2s ease;
            white-space: nowrap;
        }
        
        .main-nav a:hover {
            color: #e9f2ff;
        }
        
        .main-nav a i {
            font-size: 18px;
        }

        .nav-icon-button {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .nav-icon-button:hover {
            opacity: 0.8;
        }

        .nav-dropdown {
            position: relative;
        }

        .nav-dropdown__toggle {
            background: none;
            border: none;
            color: white;
            font-size: 15px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            transition: opacity 0.2s;
            padding: 6px 0;
        }

        .nav-dropdown__toggle:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .nav-dropdown__menu {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            background-color: var(--surface);
            color: var(--text-900);
            min-width: 200px;
            border-radius: 6px;
            border: 1px solid var(--border);
            box-shadow: 0 20px 40px rgba(2, 6, 23, 0.12);
            padding: 10px 0;
            z-index: 1000;
        }

        .nav-dropdown__menu a {
            display: block;
            padding: 10px 18px;
            color: var(--text-900);
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.2s, color 0.2s;
        }

        .nav-dropdown__menu a:hover {
            background-color: rgba(0, 102, 204, 0.08);
            color: var(--dark-blue);
        }

        .nav-dropdown:hover .nav-dropdown__menu,
        .nav-dropdown:focus-within .nav-dropdown__menu {
            display: block;
        }

        .nav-dropdown--pending .nav-dropdown__toggle {
            color: rgba(255, 255, 255, 0.7);
        }

        .nav-dropdown--pending .nav-dropdown__notice {
            padding: 10px 18px;
            color: #d9534f;
            font-weight: 500;
        }

        .nav-dropdown--pending .nav-dropdown__menu a:not(:last-child) {
            pointer-events: none;
            opacity: 0.6;
        }
        
        .hero-section {
            width: 100%;
            height: 380px;
            background-size: cover;
            background-position: center;
            background-color: #1a1a2e;
            position: relative;
            overflow: hidden;
        }
        
        .hero-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transform: scale(1.05);
            transition: opacity 1s ease, transform 1.5s ease;
        }
        
        .hero-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .hero-slide.is-active {
            opacity: 1;
            transform: scale(1);
            z-index: 0;
        }
        
        .hero-section::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.6) 100%);
            z-index: 1;
        }
        
        .hero-content {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            z-index: 2;
            padding: 30px 20px;
        }
        
        .hero-content > div {
            max-width: 700px;
            animation: fadeInUp 0.8s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .hero-title {
            font-size: 36px;
            font-weight: 900;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            margin-bottom: 12px;
            line-height: 1.2;
            transition: opacity 0.3s ease;
        }
        
        .hero-subtitle {
            font-size: 16px;
            font-weight: 500;
            opacity: 0.95;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
            line-height: 1.5;
            letter-spacing: 0.3px;
            transition: opacity 0.3s ease;
        }
        
        .hero-slider-controls {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            justify-content: center;
            gap: 10px;
            z-index: 3;
        }
        
        .hero-slider-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.6);
            background: transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0;
        }
        
        .hero-slider-dot:hover {
            border-color: rgba(255, 255, 255, 0.9);
            transform: scale(1.2);
        }
        
        .hero-slider-dot.is-active {
            background: #fff;
            border-color: #fff;
            transform: scale(1.3);
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }
        
        .hero-slider-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.8);
            background: rgba(0, 0, 0, 0.3);
            color: #fff;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 3;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }
        
        .hero-slider-nav:hover {
            background: rgba(0, 0, 0, 0.6);
            border-color: #fff;
            transform: translateY(-50%) scale(1.1);
        }
        
        .hero-slider-nav:active {
            transform: translateY(-50%) scale(0.95);
        }
        
        .hero-slider-nav.prev {
            left: 20px;
        }
        
        .hero-slider-nav.next {
            right: 20px;
        }
        
        @media (max-width: 768px) {
            .hero-section {
                height: 300px;
            }
            
            .hero-title {
                font-size: 26px;
                letter-spacing: 1px;
                margin-bottom: 10px;
            }
            
            .hero-subtitle {
                font-size: 14px;
            }
            
            .hero-content {
                padding: 20px 15px;
            }
            
            .hero-slider-nav {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }
            
            .hero-slider-nav.prev {
                left: 12px;
            }
            
            .hero-slider-nav.next {
                right: 12px;
            }
            
            .hero-slider-controls {
                bottom: 15px;
            }
            
            .hero-slider-dot {
                width: 7px;
                height: 7px;
            }
        }
        
        .content-section {
            padding: 56px 0;
            background-color: var(--surface);
        }
        
        .section-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-900);
            margin-bottom: 26px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .section-title::after {
            content: "";
            display: block;
            width: 48px;
            height: 3px;
            background: var(--primary-blue);
            border-radius: 2px;
            margin-top: 10px;
        }
        
        .panel {
            background: #fff;
            border-radius: 14px;
            padding: 22px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .panel:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 40px rgba(2, 6, 23, 0.10);
        }
        
        .news-item {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 1px solid var(--border);
        }
        
        .news-item:last-child {
            border-bottom: none;
        }
        
        .news-thumbnail {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .news-detail-content {
            line-height: 1.8;
            color: var(--text-700);
            font-size: 16px;
        }

        .news-detail-content img,
        .news-detail-content figure img {
            width: 100%;
            max-width: 900px;
            height: auto;
            border-radius: 12px;
            margin: 0 auto 24px;
            display: block;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
        }

        .news-detail-content table {
            width: 100%;
            overflow-x: auto;
            display: block;
        }

        .news-content h6 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-900);
            margin-bottom: 8px;
        }
        
        .news-content p {
            font-size: 14px;
            color: var(--text-700);
            margin-bottom: 5px;
        }
        
        .news-date {
            font-size: 12px;
            color: var(--text-500);
        }
        
        .announcement-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }
        
        .announcement-item:last-child {
            border-bottom: none;
        }
        
        .announcement-icon {
            width: 40px;
            height: 40px;
            background-color: transparent;
            border: 1px solid var(--border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .announcement-content {
            flex: 1;
        }
        
        .announcement-content h6 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-900);
            margin-bottom: 5px;
        }
        
        .announcement-date {
            font-size: 12px;
            color: var(--text-500);
        }
        
        .intro-section {
            margin-top: 50px;
        }
        
        .intro-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .intro-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-900);
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        
        .intro-text {
            color: var(--text-700);
            margin-bottom: 15px;
            line-height: 1.8;
        }
        
        .intro-text p {
            margin-bottom: 8px;
        }
        
        .register-button {
            background-color: var(--primary-blue);
            color: white;
            padding: 18px 50px;
            font-size: 20px;
            font-weight: 800;
            border: none;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 6px 14px rgba(2, 6, 23, 0.10);
        }
        
        .register-button:hover {
            background-color: var(--dark-blue);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 12px 22px rgba(2, 6, 23, 0.18);
        }
        
        .register-button i {
            font-size: 22px;
        }
        
        .guide-section {
            margin-top: 30px;
        }
        
        .guide-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .view-more-link {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: gap 0.2s, color 0.2s;
        }
        
        .view-more-link:hover {
            text-decoration: none;
            color: var(--dark-blue);
            gap: 8px;
        }
        
        .main-footer {
            background-color: var(--dark-blue);
            color: white;
            padding: 50px 0 20px;
        }
        
        .footer-column h5 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .footer-column ul {
            list-style: none;
            padding: 0;
        }
        
        .footer-column ul li {
            margin-bottom: 10px;
        }
        
        .footer-column ul li a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-column ul li a:hover {
            color: white;
        }
        
        .footer-copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            margin-top: 30px;
            color: rgba(255, 255, 255, 0.7);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Top Banner -->
    <div class="top-banner"></div>
    
    <!-- Header -->
    @include('public.partials.header')
    
    <!-- Notifications -->
    @if(session('success') || session('warning') || session('status') || $errors->any())
        <div class="container mt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('status'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    @endif

    <!-- Hero Image -->
    @yield('hero')
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    @include('public.partials.footer')
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    @stack('scripts')
</body>
</html>

