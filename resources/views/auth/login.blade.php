<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramita - Login</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated Background */
        .bg-animation {
            position: absolute;
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
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(0, 255, 136, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 255, 136, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: gridMove 20s linear infinite;
        }

        @keyframes gridMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        /* Floating Particles */
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: #00ff88;
            border-radius: 50%;
            animation: particleFloat 8s ease-in-out infinite;
            box-shadow: 0 0 10px #00ff88;
        }

        .particle:nth-child(odd) {
            background: #00d4ff;
            box-shadow: 0 0 10px #00d4ff;
        }

        @keyframes particleFloat {
            0%, 100% { 
                transform: translateY(100vh) translateX(0px) rotate(0deg);
                opacity: 0;
            }
            10% { opacity: 1; }
            90% { opacity: 1; }
            50% { 
                transform: translateY(-50vh) translateX(50px) rotate(180deg);
                opacity: 0.8;
            }
        }

        /* Blockchain Nodes - Enhanced */
        .blockchain-node {
            position: absolute;
            width: 12px;
            height: 12px;
            background: radial-gradient(circle, #00ff88, #00cc6a);
            border-radius: 50%;
            animation: nodeFloat 6s ease-in-out infinite;
            box-shadow: 0 0 30px #00ff88, inset 0 0 10px rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .blockchain-node:hover {
            transform: scale(1.5) !important;
            box-shadow: 0 0 50px #00ff88, inset 0 0 15px rgba(255, 255, 255, 0.5);
        }

        .blockchain-node:nth-child(even) {
            background: radial-gradient(circle, #00d4ff, #0099cc);
            box-shadow: 0 0 30px #00d4ff, inset 0 0 10px rgba(255, 255, 255, 0.3);
        }

        .blockchain-node:nth-child(even):hover {
            box-shadow: 0 0 50px #00d4ff, inset 0 0 15px rgba(255, 255, 255, 0.5);
        }

        @keyframes nodeFloat {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.7; }
            25% { transform: translateY(-15px) rotate(90deg); opacity: 1; }
            50% { transform: translateY(-30px) rotate(180deg); opacity: 0.9; }
            75% { transform: translateY(-15px) rotate(270deg); opacity: 1; }
        }

        /* Dynamic Connecting Lines */
        .connection-line {
            position: absolute;
            height: 2px;
            background: linear-gradient(90deg, transparent, #00ff88, #00d4ff, transparent);
            animation: linePulse 4s ease-in-out infinite;
            border-radius: 2px;
        }

        .connection-line:nth-child(odd) {
            background: linear-gradient(90deg, transparent, #00d4ff, #00ff88, transparent);
        }

        @keyframes linePulse {
            0%, 100% { 
                opacity: 0.2; 
                transform: scaleX(0.5);
            }
            50% { 
                opacity: 1; 
                transform: scaleX(1);
            }
        }

        /* Hexagonal Pattern */
        .hex-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 60 60'%3E%3Cg fill='none' stroke='rgba(0,255,136,0.1)' stroke-width='1'%3E%3Cpath d='M30 0l26 15v30L30 60 4 45V15z'/%3E%3C/g%3E%3C/svg%3E");
            animation: hexRotate 30s linear infinite;
        }

        @keyframes hexRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Glowing Orbs */
        .glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(2px);
            animation: orbFloat 10s ease-in-out infinite;
        }

        .glow-orb:nth-child(1) {
            width: 80px;
            height: 80px;
            background: radial-gradient(circle, rgba(0, 255, 136, 0.3), transparent);
            top: 10%;
            left: 20%;
            animation-delay: 0s;
        }

        .glow-orb:nth-child(2) {
            width: 60px;
            height: 60px;
            background: radial-gradient(circle, rgba(0, 212, 255, 0.3), transparent);
            top: 60%;
            right: 15%;
            animation-delay: 3s;
        }

        .glow-orb:nth-child(3) {
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.2), transparent);
            bottom: 20%;
            left: 10%;
            animation-delay: 6s;
        }

        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.3; }
            33% { transform: translate(20px, -20px) scale(1.1); opacity: 0.6; }
            66% { transform: translate(-20px, 20px) scale(0.9); opacity: 0.4; }
        }

        /* Matrix Rain Effect */
        .matrix-rain {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .matrix-column {
            position: absolute;
            top: -100%;
            font-family: monospace;
            font-size: 10px;
            color: rgba(0, 255, 136, 0.6);
            animation: matrixFall 8s linear infinite;
        }

        @keyframes matrixFall {
            0% { top: -100%; opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }

        /* Main Container */
        .login-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-title {
            font-size: 2.5rem;
            font-weight: bold;
            background: linear-gradient(45deg, #00ff88, #00d4ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from { text-shadow: 0 0 10px rgba(0, 255, 136, 0.3); }
            to { text-shadow: 0 0 20px rgba(0, 255, 136, 0.6); }
        }

        .logo-description {
            color: #ffffff;
            font-size: 0.9rem;
            line-height: 1.5;
            opacity: 0.9;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-size: 16px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-input:focus {
            outline: none;
            border-color: #00ff88;
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.3);
            transform: translateY(-2px);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #00ff88, #00d4ff);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0, 255, 136, 0.4);
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(255, 255, 255, 0.3);
        }

        .divider span {
            background: rgba(255, 255, 255, 0.1);
            padding: 0 20px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
        }

        .sso-btn {
            width: 100%;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: #ffffff;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            backdrop-filter: blur(10px);
        }

        .sso-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .microsoft-icon {
            width: 20px;
            height: 20px;
            background: linear-gradient(45deg, #00a1f1, #0078d4);
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }

        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }

        .forgot-password a {
            color: #00ff88;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #00d4ff;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                margin: 20px;
                padding: 30px 25px;
            }
            
            .logo-title {
                font-size: 2rem;
            }
            
            .logo-description {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="bg-animation">
        <!-- Animated Grid -->
        <div class="grid-overlay"></div>
        
        <!-- Hexagonal Pattern -->
        <div class="hex-pattern"></div>
        
        <!-- Glowing Orbs -->
        <div class="glow-orb"></div>
        <div class="glow-orb"></div>
        <div class="glow-orb"></div>
        
        <!-- Matrix Rain -->
        <div class="matrix-rain" id="matrixRain"></div>
        
        <!-- Floating Particles -->
        <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
        <div class="particle" style="left: 20%; animation-delay: 1s;"></div>
        <div class="particle" style="left: 30%; animation-delay: 2s;"></div>
        <div class="particle" style="left: 40%; animation-delay: 3s;"></div>
        <div class="particle" style="left: 50%; animation-delay: 4s;"></div>
        <div class="particle" style="left: 60%; animation-delay: 5s;"></div>
        <div class="particle" style="left: 70%; animation-delay: 6s;"></div>
        <div class="particle" style="left: 80%; animation-delay: 7s;"></div>
        <div class="particle" style="left: 90%; animation-delay: 8s;"></div>
        
        <!-- Enhanced Blockchain Nodes -->
        <div class="blockchain-node" style="top: 10%; left: 10%; animation-delay: 0s;"></div>
        <div class="blockchain-node" style="top: 20%; left: 80%; animation-delay: 1s;"></div>
        <div class="blockchain-node" style="top: 70%; left: 20%; animation-delay: 2s;"></div>
        <div class="blockchain-node" style="top: 80%; left: 70%; animation-delay: 3s;"></div>
        <div class="blockchain-node" style="top: 40%; left: 90%; animation-delay: 4s;"></div>
        <div class="blockchain-node" style="top: 60%; left: 5%; animation-delay: 5s;"></div>
        <div class="blockchain-node" style="top: 25%; left: 45%; animation-delay: 1.5s;"></div>
        <div class="blockchain-node" style="top: 75%; left: 85%; animation-delay: 2.5s;"></div>
        
        <!-- Enhanced Connection Lines -->
        <div class="connection-line" style="top: 15%; left: 10%; width: 70%; transform: rotate(15deg);"></div>
        <div class="connection-line" style="top: 65%; left: 5%; width: 60%; transform: rotate(-20deg);"></div>
        <div class="connection-line" style="top: 35%; left: 25%; width: 50%; transform: rotate(45deg);"></div>
        <div class="connection-line" style="top: 55%; left: 35%; width: 40%; transform: rotate(-45deg);"></div>
        <div class="connection-line" style="top: 25%; left: 50%; width: 35%; transform: rotate(30deg);"></div>
    </div>

    <div class="login-container">
        <div class="logo-section">

            <h1 class="logo-title">Login</h1>
        </div>

        @if ($errors->any())
            <div style="color: #ff6b6b; background: #fff3f3; border-radius: 8px; padding: 10px; margin-bottom: 15px;">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="login-form" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <input type="email" name="email" class="form-input" placeholder="Email Address" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-input" placeholder="Password" required>
            </div>
            <button type="submit" class="login-btn">
                Login
            </button>
        </form>

        <div class="divider">
            <span>atau</span>
        </div>

        <button class="sso-btn" onclick="handleMicrosoftLogin()">
            <div class="microsoft-icon">M</div>
            Login dengan Microsoft
        </button>

        {{-- <div class="forgot-password">
            <a href="#" onclick="handleForgotPassword()">Lupa Password?</a>
        </div> --}}
    </div>

    <script>
        // ...existing code...

        // Microsoft SSO handler
        function handleMicrosoftLogin() {
            const ssoBtn = document.querySelector('.sso-btn');
            ssoBtn.innerHTML = '<div class="microsoft-icon">M</div> Connecting...';
            ssoBtn.style.background = 'rgba(0, 161, 241, 0.2)';
            
            setTimeout(() => {
                alert('Microsoft SSO integration akan segera tersedia!');
                ssoBtn.innerHTML = '<div class="microsoft-icon">M</div> Login dengan Microsoft';
                ssoBtn.style.background = 'rgba(255, 255, 255, 0.1)';
            }, 1000);
        }

        // Forgot password handler
        function handleForgotPassword() {
            alert('Fitur reset password akan segera tersedia!');
        }

        // Add dynamic hover effects
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Dynamic background animation
        setInterval(() => {
            const nodes = document.querySelectorAll('.blockchain-node');
            nodes.forEach(node => {
                const randomX = Math.random() * 2 - 1;
                const randomY = Math.random() * 2 - 1;
                node.style.transform = `translate(${randomX}px, ${randomY}px)`;
            });
        }, 2000);

        // Create Matrix Rain Effect
        function createMatrixRain() {
            const matrixContainer = document.getElementById('matrixRain');
            const chars = '01アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲン';
            
            for (let i = 0; i < 15; i++) {
                const column = document.createElement('div');
                column.className = 'matrix-column';
                column.style.left = Math.random() * 100 + '%';
                column.style.animationDelay = Math.random() * 8 + 's';
                column.style.animationDuration = (Math.random() * 5 + 5) + 's';
                
                let text = '';
                for (let j = 0; j < 20; j++) {
                    text += chars[Math.floor(Math.random() * chars.length)] + '<br>';
                }
                column.innerHTML = text;
                
                matrixContainer.appendChild(column);
            }
        }

        // Initialize Matrix Rain
        createMatrixRain();

        // Add mouse interaction for background elements
        document.addEventListener('mousemove', (e) => {
            const mouseX = e.clientX / window.innerWidth;
            const mouseY = e.clientY / window.innerHeight;
            
            // Move grid based on mouse position
            const gridOverlay = document.querySelector('.grid-overlay');
            gridOverlay.style.transform = `translate(${mouseX * 10}px, ${mouseY * 10}px)`;
            
            // Move hexagonal pattern
            const hexPattern = document.querySelector('.hex-pattern');
            hexPattern.style.transform = `rotate(${mouseX * 360}deg) scale(${1 + mouseY * 0.1})`;
            
            // Move glowing orbs
            const orbs = document.querySelectorAll('.glow-orb');
            orbs.forEach((orb, index) => {
                const factor = (index + 1) * 0.5;
                orb.style.transform = `translate(${mouseX * 20 * factor}px, ${mouseY * 20 * factor}px)`;
            });
        });

        // Add click interaction for particles
        document.addEventListener('click', (e) => {
            // Create explosion effect at click position
            for (let i = 0; i < 5; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = e.clientX + 'px';
                particle.style.top = e.clientY + 'px';
                particle.style.position = 'fixed';
                particle.style.zIndex = '1000';
                particle.style.animation = 'particleExplode 1s ease-out forwards';
                
                const randomX = (Math.random() - 0.5) * 200;
                const randomY = (Math.random() - 0.5) * 200;
                particle.style.setProperty('--randomX', randomX + 'px');
                particle.style.setProperty('--randomY', randomY + 'px');
                
                document.body.appendChild(particle);
                
                setTimeout(() => {
                    particle.remove();
                }, 1000);
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

        // Dynamic color changing for blockchain nodes
        setInterval(() => {
            const nodes = document.querySelectorAll('.blockchain-node');
            nodes.forEach(node => {
                const colors = ['#00ff88', '#00d4ff', '#ff6b6b', '#ffd93d', '#6bcf7f'];
                const randomColor = colors[Math.floor(Math.random() * colors.length)];
                node.style.boxShadow = `0 0 30px ${randomColor}, inset 0 0 10px rgba(255, 255, 255, 0.3)`;
            });
        }, 3000);

        // Add keyboard interaction
        document.addEventListener('keydown', (e) => {
            if (e.key === ' ') {
                // Space bar creates a wave effect
                const nodes = document.querySelectorAll('.blockchain-node');
                nodes.forEach((node, index) => {
                    setTimeout(() => {
                        node.style.transform = 'scale(2)';
                        node.style.boxShadow = '0 0 50px #00ff88, inset 0 0 15px rgba(255, 255, 255, 0.5)';
                        setTimeout(() => {
                            node.style.transform = 'scale(1)';
                            node.style.boxShadow = '0 0 30px #00ff88, inset 0 0 10px rgba(255, 255, 255, 0.3)';
                        }, 200);
                    }, index * 100);
                });
            }
        });
    </script>
</body>
</html>