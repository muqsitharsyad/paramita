<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramita - Sistem Informasi Pendistribusian Bahan Ajar</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Enhanced Animated Background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        /* Animated Grid */
        .grid-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 120%;
            height: 120%;
            background-image: 
                linear-gradient(rgba(0, 255, 136, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 255, 136, 0.1) 1px, transparent 1px);
            background-size: 60px 60px;
            animation: gridMove 25s linear infinite;
        }

        @keyframes gridMove {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(60px, 60px) rotate(360deg); }
        }

        /* Enhanced Floating Particles */
        .particle {
            position: absolute;
            border-radius: 50%;
            animation: particleFloat 12s ease-in-out infinite;
            pointer-events: none;
        }

        .particle:nth-child(odd) {
            background: radial-gradient(circle, #00ff88, transparent);
            box-shadow: 0 0 20px #00ff88;
        }

        .particle:nth-child(even) {
            background: radial-gradient(circle, #00d4ff, transparent);
            box-shadow: 0 0 20px #00d4ff;
        }

        @keyframes particleFloat {
            0%, 100% { 
                transform: translateY(100vh) translateX(0px) rotate(0deg) scale(0);
                opacity: 0;
            }
            10% { opacity: 1; transform: translateY(90vh) translateX(10px) rotate(36deg) scale(1); }
            50% { 
                transform: translateY(-10vh) translateX(100px) rotate(180deg) scale(1.5);
                opacity: 0.8;
            }
            90% { opacity: 1; transform: translateY(-20vh) translateX(50px) rotate(324deg) scale(1); }
        }

        /* Enhanced Blockchain Nodes */
        .blockchain-node {
            position: absolute;
            border-radius: 50%;
            animation: nodeFloat 8s ease-in-out infinite;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .blockchain-node:hover {
            transform: scale(2) !important;
            z-index: 100;
        }

        .blockchain-node:nth-child(odd) {
            background: radial-gradient(circle, #00ff88, #00cc6a);
            box-shadow: 0 0 40px #00ff88, inset 0 0 15px rgba(255, 255, 255, 0.3);
        }

        .blockchain-node:nth-child(even) {
            background: radial-gradient(circle, #00d4ff, #0099cc);
            box-shadow: 0 0 40px #00d4ff, inset 0 0 15px rgba(255, 255, 255, 0.3);
        }

        @keyframes nodeFloat {
            0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); opacity: 0.8; }
            25% { transform: translateY(-20px) rotate(90deg) scale(1.1); opacity: 1; }
            50% { transform: translateY(-40px) rotate(180deg) scale(1.2); opacity: 0.9; }
            75% { transform: translateY(-20px) rotate(270deg) scale(1.1); opacity: 1; }
        }

        /* Dynamic Connection Lines */
        .connection-line {
            position: absolute;
            height: 3px;
            background: linear-gradient(90deg, transparent, #00ff88, #00d4ff, #ff6b6b, transparent);
            animation: linePulse 6s ease-in-out infinite;
            border-radius: 3px;
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.5);
        }

        @keyframes linePulse {
            0%, 100% { 
                opacity: 0.3; 
                transform: scaleX(0.3) scaleY(0.5);
            }
            50% { 
                opacity: 1; 
                transform: scaleX(1.2) scaleY(1);
            }
        }

        /* Hexagonal Pattern */
        .hex-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 80 80'%3E%3Cg fill='none' stroke='rgba(0,255,136,0.08)' stroke-width='2'%3E%3Cpath d='M40 0l35 20v40L40 80 5 60V20z'/%3E%3C/g%3E%3C/svg%3E");
            animation: hexRotate 40s linear infinite;
        }

        @keyframes hexRotate {
            0% { transform: rotate(0deg) scale(1); }
            50% { transform: rotate(180deg) scale(1.1); }
            100% { transform: rotate(360deg) scale(1); }
        }

        /* Enhanced Glowing Orbs */
        .glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(3px);
            animation: orbFloat 15s ease-in-out infinite;
        }

        .glow-orb:nth-child(1) {
            width: 120px;
            height: 120px;
            background: radial-gradient(circle, rgba(0, 255, 136, 0.4), transparent);
            top: 15%;
            left: 10%;
            animation-delay: 0s;
        }

        .glow-orb:nth-child(2) {
            width: 80px;
            height: 80px;
            background: radial-gradient(circle, rgba(0, 212, 255, 0.4), transparent);
            top: 60%;
            right: 10%;
            animation-delay: 5s;
        }

        .glow-orb:nth-child(3) {
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.3), transparent);
            bottom: 10%;
            left: 5%;
            animation-delay: 10s;
        }

        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.4; }
            33% { transform: translate(30px, -30px) scale(1.2); opacity: 0.7; }
            66% { transform: translate(-30px, 30px) scale(0.8); opacity: 0.5; }
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 15px 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            background: linear-gradient(45deg, #00ff88, #00d4ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: logoGlow 3s ease-in-out infinite alternate;
        }

        @keyframes logoGlow {
            from { text-shadow: 0 0 10px rgba(0, 255, 136, 0.3); }
            to { text-shadow: 0 0 20px rgba(0, 255, 136, 0.6); }
        }

        .nav-links {
            display: flex;
            gap: 30px;
            list-style: none;
        }

        .nav-links a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: #00ff88;
            transform: translateY(-2px);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(45deg, #00ff88, #00d4ff);
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .cta-button {
            background: linear-gradient(45deg, #00ff88, #00d4ff);
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 255, 136, 0.3);
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 255, 136, 0.5);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 100px 20px 50px;
            position: relative;
        }

        .hero-content {
            max-width: 900px;
            z-index: 10;
            position: relative;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: bold;
            background: linear-gradient(45deg, #00ff88, #00d4ff, #ff6b6b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            animation: titleGlow 4s ease-in-out infinite alternate;
            text-shadow: 0 0 30px rgba(0, 255, 136, 0.3);
        }

        @keyframes titleGlow {
            from { 
                filter: brightness(1);
                transform: scale(1);
            }
            to { 
                filter: brightness(1.2);
                transform: scale(1.02);
            }
        }

        .hero-subtitle {
            font-size: 1.5rem;
            color: #ffffff;
            margin-bottom: 25px;
            font-weight: 600;
            line-height: 1.4;
            opacity: 0.95;
        }

        .hero-description {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            margin-bottom: 40px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 50px;
        }

        .btn-primary {
            background: linear-gradient(45deg, #00ff88, #00d4ff);
            color: white;
            padding: 18px 35px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(0, 255, 136, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 255, 136, 0.5);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding: 18px 35px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        /* Features Section */
        .features {
            padding: 100px 20px;
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .features-title {
            text-align: center;
            font-size: 3rem;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #00ff88, #00d4ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .features-subtitle {
            text-align: center;
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 60px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 255, 136, 0.2);
            border-color: rgba(0, 255, 136, 0.5);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(45deg, #00ff88, #00d4ff);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, #00ff88, #00d4ff);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
            animation: iconFloat 3s ease-in-out infinite;
        }

        @keyframes iconFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 15px;
        }

        .feature-description {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
        }

        /* Stats Section */
        .stats {
            padding: 80px 20px;
            background: rgba(255, 255, 255, 0.05);
        }

        .stats-container {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            text-align: center;
        }

        .stat-item {
            padding: 30px;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 30px rgba(0, 255, 136, 0.2);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            background: linear-gradient(45deg, #00ff88, #00d4ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            font-weight: 500;
        }

        /* Footer */
        .footer {
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(20px);
            padding: 60px 20px 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-section h3 {
            color: #ffffff;
            font-size: 1.3rem;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #00ff88, #00d4ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .footer-section p,
        .footer-section a {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: #00ff88;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.2rem;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
        }

        /* Interactive Elements */
        .interactive-element {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .interactive-element:hover {
            transform: scale(1.1);
        }

        /* Scroll Animations */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- Enhanced Animated Background -->
    <div class="bg-animation">
        <!-- Animated Grid -->
        <div class="grid-overlay"></div>
        
        <!-- Hexagonal Pattern -->
        <div class="hex-pattern"></div>
        
        <!-- Enhanced Glowing Orbs -->
        <div class="glow-orb"></div>
        <div class="glow-orb"></div>
        <div class="glow-orb"></div>
        
        <!-- Enhanced Floating Particles -->
        @for ($i = 0; $i < 20; $i++)
            <div class="particle" style="
                left: {{ rand(0, 100) }}%; 
                width: {{ rand(4, 12) }}px; 
                height: {{ rand(4, 12) }}px;
                animation-delay: {{ $i * 0.5 }}s;
                animation-duration: {{ rand(10, 15) }}s;
            "></div>
        @endfor
        
        <!-- Enhanced Blockchain Nodes -->
        @for ($i = 0; $i < 15; $i++)
            <div class="blockchain-node interactive-element" style="
                top: {{ rand(10, 90) }}%; 
                left: {{ rand(5, 95) }}%; 
                width: {{ rand(15, 25) }}px; 
                height: {{ rand(15, 25) }}px;
                animation-delay: {{ $i * 0.3 }}s;
            "></div>
        @endfor
        
        <!-- Enhanced Connection Lines -->
        @for ($i = 0; $i < 10; $i++)
            <div class="connection-line" style="
                top: {{ rand(10, 90) }}%; 
                left: {{ rand(5, 80) }}%; 
                width: {{ rand(100, 300) }}px; 
                transform: rotate({{ rand(-45, 45) }}deg);
                animation-delay: {{ $i * 0.6 }}s;
            "></div>
        @endfor
    </div>

    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <div class="logo">Paramita</div>
            <ul class="nav-links">
                <li><a href="#home">Beranda</a></li>
                <li><a href="#features">Fitur</a></li>
                <li><a href="#about">Tentang</a></li>
                <li><a href="#contact">Kontak</a></li>
            </ul>
            <a href="{{url('/login')}}" class="cta-button">Masuk</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content fade-in">
            <h1 class="hero-title">Paramita</h1>
            <h2 class="hero-subtitle">Sistem Informasi Pendistribusian Bahan Ajar Langsung Kepada Mahasiswa Secara Tepat dan Akurat</h2>
            <p class="hero-description">
                Platform digital inovatif yang menggunakan teknologi blockchain untuk memastikan distribusi bahan ajar yang aman, transparan, dan efisien kepada seluruh mahasiswa Universitas Terbuka.
            </p>
            <div class="hero-buttons">
                <a href="#features" class="btn-primary">Jelajahi Fitur</a>
                <a href="#demo" class="btn-secondary">Lihat Demo</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="features-container">
            <h2 class="features-title fade-in">Fitur Unggulan</h2>
            <p class="features-subtitle fade-in">Teknologi blockchain terdepan untuk pendistribusian bahan ajar yang revolusioner</p>
            
            <div class="features-grid">
                <div class="feature-card fade-in">
                    <div class="feature-icon">🔒</div>
                    <h3 class="feature-title">Keamanan Blockchain</h3>
                    <p class="feature-description">Sistem keamanan tingkat tinggi dengan teknologi blockchain yang memastikan integritas dan keaslian setiap bahan ajar yang didistribusikan.</p>
                </div>
                
                <div class="feature-card fade-in">
                    <div class="feature-icon">⚡</div>
                    <h3 class="feature-title">Distribusi Real-time</h3>
                    <p class="feature-description">Pengiriman bahan ajar secara langsung dan real-time kepada mahasiswa dengan sistem notifikasi otomatis dan tracking yang akurat.</p>
                </div>
                
                <div class="feature-card fade-in">
                    <div class="feature-icon">📊</div>
                    <h3 class="feature-title">Analytics & Reporting</h3>
                    <p class="feature-description">Dashboard komprehensif dengan analitik mendalam untuk memantau distribusi, engagement, dan efektivitas bahan ajar secara real-time.</p>
                </div>
                
                <div class="feature-card fade-in">
                    <div class="feature-icon">🌐</div>
                    <h3 class="feature-title">Multi-Platform Access</h3>
                    <p class="feature-description">Akses mudah melalui berbagai platform dan device dengan sinkronisasi otomatis dan offline capability untuk fleksibilitas maksimal.</p>
                </div>
                
                <div class="feature-card fade-in">
                    <div class="feature-icon">🎯</div>
                    <h3 class="feature-title">Personalisasi Konten</h3>
                    <p class="feature-description">AI-powered content recommendation yang menyesuaikan bahan ajar berdasarkan profil belajar dan kebutuhan individual mahasiswa.</p>
                </div>
                
                <div class="feature-card fade-in">
                    <div class="feature-icon">🔄</div>
                    <h3 class="feature-title">Auto-Update System</h3>
                    <p class="feature-description">Sistem pembaruan otomatis yang memastikan mahasiswa selalu mendapatkan versi terbaru dari bahan ajar dengan notifikasi perubahan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="stats-container">
            <div class="stat-item fade-in">
                <div class="stat-number" data-target="50000">0</div>
                <div class="stat-label">Mahasiswa Aktif</div>
            </div>
            <div class="stat-item fade-in">
                <div class="stat-number" data-target="1000">0</div>
                <div class="stat-label">Bahan Ajar</div>
            </div>
            <div class="stat-item fade-in">
                <div class="stat-number" data-target="99">0</div>
                <div class="stat-label">% Uptime</div>
            </div>
            <div class="stat-item fade-in">
                <div class="stat-number" data-target="24">0</div>
                <div class="stat-label">Jam Support</div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>Paramita</h3>
                <p>Platform digital inovatif untuk pendistribusian bahan ajar dengan teknologi blockchain yang aman dan transparan.</p>
            </div>
            <div class="footer-section">
                <h3>Fitur</h3>
                <p><a href="#security">Keamanan Blockchain</a></p>
                <p><a href="#realtime">Distribusi Real-time</a></p>
                <p><a href="#analytics">Analytics & Reporting</a></p>
            </div>
            <div class="footer-section">
                <h3>Dukungan</h3>
                <p><a href="#help">Pusat Bantuan</a></p>
                <p><a href="#docs">Dokumentasi</a></p>
                <p><a href="#contact">Hubungi Kami</a></p>
            </div>
            <div class="footer-section">
                <h3>Kontak</h3>
                <p>Email: info@paramita.ac.id</p>
                <p>Telepon: (021) 123-4567</p>
                <p>Alamat: Jakarta, Indonesia</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Paramita. Semua hak dilindungi. Dikembangkan dengan ❤️ untuk Universitas Terbuka.</p>
        </div>
    </footer>

    <script>
        // Enhanced Interactive Features
        document.addEventListener('DOMContentLoaded', function() {
            // Navbar scroll effect
            const navbar = document.getElementById('navbar');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            // Smooth scrolling for navigation links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Fade in animation on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.fade-in').forEach(el => {
                observer.observe(el);
            });

            // Animated counter for stats
            function animateCounter(element, target, duration = 2000) {
                let start = 0;
                const increment = target / (duration / 16);
                
                function updateCounter() {
                    start += increment;
                    if (start < target) {
                        element.textContent = Math.floor(start);
                        requestAnimationFrame(updateCounter);
                    } else {
                        element.textContent = target;
                    }
                }
                updateCounter();
            }

            // Trigger counter animation when stats section is visible
            const statsObserver = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const statNumbers = entry.target.querySelectorAll('.stat-number');
                        statNumbers.forEach(stat => {
                            const target = parseInt(stat.getAttribute('data-target'));
                            animateCounter(stat, target);
                        });
                        statsObserver.unobserve(entry.target);
                    }
                });
            });

            const statsSection = document.querySelector('.stats');
            if (statsSection) {
                statsObserver.observe(statsSection);
            }

            // Enhanced mouse interaction for background elements
            document.addEventListener('mousemove', (e) => {
                const mouseX = e.clientX / window.innerWidth;
                const mouseY = e.clientY / window.innerHeight;
                
                // Move grid based on mouse position
                const gridOverlay = document.querySelector('.grid-overlay');
                if (gridOverlay) {
                    gridOverlay.style.transform = `translate(${mouseX * 20}px, ${mouseY * 20}px) rotate(${mouseX * 10}deg)`;
                }
                
                // Move hexagonal pattern
                const hexPattern = document.querySelector('.hex-pattern');
                if (hexPattern) {
                    hexPattern.style.transform = `rotate(${mouseX * 360}deg) scale(${1 + mouseY * 0.1})`;
                }
                
                // Move glowing orbs
                const orbs = document.querySelectorAll('.glow-orb');
                orbs.forEach((orb, index) => {
                    const factor = (index + 1) * 0.8;
                    orb.style.transform = `translate(${mouseX * 30 * factor}px, ${mouseY * 30 * factor}px) scale(${1 + mouseY * 0.2})`;
                });

                // Move blockchain nodes slightly
                const nodes = document.querySelectorAll('.blockchain-node');
                nodes.forEach((node, index) => {
                    const factor = (index % 3 + 1) * 0.3;
                    const currentTransform = node.style.transform || '';
                    if (!currentTransform.includes('scale(2)')) {
                        node.style.transform = `translate(${mouseX * 10 * factor}px, ${mouseY * 10 * factor}px)`;
                    }
                });
            });

            // Enhanced click interaction for particles explosion
            document.addEventListener('click', (e) => {
                createParticleExplosion(e.clientX, e.clientY);
            });

            function createParticleExplosion(x, y) {
                for (let i = 0; i < 8; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    particle.style.left = x + 'px';
                    particle.style.top = y + 'px';
                    particle.style.position = 'fixed';
                    particle.style.zIndex = '1000';
                    particle.style.width = '6px';
                    particle.style.height = '6px';
                    particle.style.pointerEvents = 'none';
                    
                    const randomX = (Math.random() - 0.5) * 300;
                    const randomY = (Math.random() - 0.5) * 300;
                    const randomColor = ['#00ff88', '#00d4ff', '#ff6b6b', '#ffd93d'][Math.floor(Math.random() * 4)];
                    
                    particle.style.background = `radial-gradient(circle, ${randomColor}, transparent)`;
                    particle.style.boxShadow = `0 0 20px ${randomColor}`;
                    particle.style.animation = `particleExplode 1.5s ease-out forwards`;
                    particle.style.setProperty('--randomX', randomX + 'px');
                    particle.style.setProperty('--randomY', randomY + 'px');
                    
                    document.body.appendChild(particle);
                    
                    setTimeout(() => {
                        particle.remove();
                    }, 1500);
                }
            }

            // Dynamic color changing for blockchain nodes
            setInterval(() => {
                const nodes = document.querySelectorAll('.blockchain-node');
                nodes.forEach(node => {
                    if (!node.matches(':hover')) {
                        const colors = ['#00ff88', '#00d4ff', '#ff6b6b', '#ffd93d', '#6bcf7f'];
                        const randomColor = colors[Math.floor(Math.random() * colors.length)];
                        node.style.boxShadow = `0 0 40px ${randomColor}, inset 0 0 15px rgba(255, 255, 255, 0.3)`;
                    }
                });
            }, 4000);

            // Keyboard interactions
            document.addEventListener('keydown', (e) => {
                if (e.key === ' ') {
                    e.preventDefault();
                    // Space bar creates a wave effect
                    const nodes = document.querySelectorAll('.blockchain-node');
                    nodes.forEach((node, index) => {
                        setTimeout(() => {
                            node.style.transform = 'scale(1.5)';
                            node.style.boxShadow = '0 0 60px #00ff88, inset 0 0 20px rgba(255, 255, 255, 0.5)';
                            setTimeout(() => {
                                node.style.transform = 'scale(1)';
                                node.style.boxShadow = '0 0 40px #00ff88, inset 0 0 15px rgba(255, 255, 255, 0.3)';
                            }, 300);
                        }, index * 50);
                    });
                }
            });

            // Add particle explosion keyframe
            const style = document.createElement('style');
            style.textContent = `
                @keyframes particleExplode {
                    0% {
                        transform: translate(0, 0) scale(1);
                        opacity: 1;
                    }
                    100% {
                        transform: translate(var(--randomX), var(--randomY)) scale(0);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);

            // Feature cards hover effect enhancement
            document.querySelectorAll('.feature-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.background = 'rgba(0, 255, 136, 0.1)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.background = 'rgba(255, 255, 255, 0.1)';
                });
            });

            // Interactive blockchain nodes click effect
            document.querySelectorAll('.blockchain-node').forEach(node => {
                node.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    // Create ripple effect
                    const ripple = document.createElement('div');
                    ripple.style.position = 'absolute';
                    ripple.style.borderRadius = '50%';
                    ripple.style.background = 'rgba(0, 255, 136, 0.6)';
                    ripple.style.transform = 'scale(0)';
                    ripple.style.animation = 'ripple 0.6s linear';
                    ripple.style.left = '50%';
                    ripple.style.top = '50%';
                    ripple.style.width = '100px';
                    ripple.style.height = '100px';
                    ripple.style.marginLeft = '-50px';
                    ripple.style.marginTop = '-50px';
                    ripple.style.pointerEvents = 'none';
                    
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            // Add ripple animation
            const rippleStyle = document.createElement('style');
            rippleStyle.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(rippleStyle);
        });
    </script>
</body>
</html>