<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Development Team</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #0a0a0a;
            color: #e0e0e0;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Dynamic constellation background */
        .constellation-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: 
                radial-gradient(circle at 20% 30%, rgba(30, 144, 255, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(70, 130, 180, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 40% 80%, rgba(100, 149, 237, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 60% 20%, rgba(65, 105, 225, 0.1) 0%, transparent 40%);
            animation: cosmicFlow 30s ease-in-out infinite;
        }

        @keyframes cosmicFlow {
            0%, 100% { 
                background-position: 0% 0%, 100% 100%, 50% 0%, 0% 100%;
                opacity: 0.7;
            }
            25% { 
                background-position: 30% 20%, 70% 80%, 80% 30%, 20% 70%;
                opacity: 0.9;
            }
            50% { 
                background-position: 60% 40%, 40% 60%, 20% 60%, 80% 40%;
                opacity: 0.8;
            }
            75% { 
                background-position: 20% 80%, 80% 20%, 60% 90%, 40% 10%;
                opacity: 1;
            }
        }

        /* Floating particles system */
        .particle-system {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            background: rgba(135, 206, 250, 0.3);
            border-radius: 50%;
            animation: particleFloat 12s ease-in-out infinite;
        }

        .particle:nth-child(1) { width: 4px; height: 4px; top: 15%; left: 10%; animation-delay: 0s; animation-name: particleDrift1; }
        .particle:nth-child(2) { width: 6px; height: 6px; top: 25%; left: 85%; animation-delay: 2s; animation-name: particleDrift2; }
        .particle:nth-child(3) { width: 3px; height: 3px; top: 45%; left: 20%; animation-delay: 4s; animation-name: particleDrift3; }
        .particle:nth-child(4) { width: 5px; height: 5px; top: 65%; left: 75%; animation-delay: 6s; animation-name: particleDrift4; }
        .particle:nth-child(5) { width: 4px; height: 4px; top: 80%; left: 30%; animation-delay: 8s; animation-name: particleDrift5; }
        .particle:nth-child(6) { width: 7px; height: 7px; top: 35%; left: 60%; animation-delay: 10s; animation-name: particleDrift6; }

        @keyframes particleDrift1 {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.3; }
            50% { transform: translate(30px, -40px) scale(1.5); opacity: 0.8; }
        }

        @keyframes particleDrift2 {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.4; }
            50% { transform: translate(-25px, -35px) scale(1.2); opacity: 0.9; }
        }

        @keyframes particleDrift3 {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.2; }
            50% { transform: translate(40px, -20px) scale(1.8); opacity: 0.7; }
        }

        @keyframes particleDrift4 {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.5; }
            50% { transform: translate(-35px, -45px) scale(1.3); opacity: 0.8; }
        }

        @keyframes particleDrift5 {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.3; }
            50% { transform: translate(20px, -30px) scale(1.6); opacity: 0.9; }
        }

        @keyframes particleDrift6 {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.4; }
            50% { transform: translate(-40px, -25px) scale(1.4); opacity: 0.8; }
        }

        /* Header with cosmic theme */
        .cosmic-header {
            background: rgba(10, 10, 20, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 2px solid rgba(30, 144, 255, 0.3);
            padding: 30px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.8);
            animation: headerGlow 4s ease-in-out infinite;
        }

        @keyframes headerGlow {
            0%, 100% { box-shadow: 0 10px 40px rgba(0, 0, 0, 0.8), 0 0 20px rgba(30, 144, 255, 0.1); }
            50% { box-shadow: 0 10px 40px rgba(0, 0, 0, 0.8), 0 0 40px rgba(30, 144, 255, 0.3); }
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cosmic-title {
            font-size: 36px;
            font-weight: 800;
            background: linear-gradient(45deg, #1E90FF, #4169E1, #6495ED, #87CEEB);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: titleShimmer 3s ease-in-out infinite;
            text-shadow: 0 0 30px rgba(30, 144, 255, 0.5);
            position: relative;
        }

        @keyframes titleShimmer {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .cosmic-title::before {
            content: '✦';
            position: absolute;
            left: -40px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 24px;
            color: #4169E1;
            animation: starTwinkle 2s ease-in-out infinite;
        }

        @keyframes starTwinkle {
            0%, 100% { opacity: 0.5; transform: translateY(-50%) scale(1); }
            50% { opacity: 1; transform: translateY(-50%) scale(1.3); }
        }

        .back-portal {
            padding: 15px 30px;
            background: linear-gradient(135deg, rgba(30, 144, 255, 0.2), rgba(65, 105, 225, 0.1));
            border: 2px solid rgba(30, 144, 255, 0.4);
            border-radius: 25px;
            color: #87CEEB;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .back-portal::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s ease;
        }

        .back-portal:hover::before {
            left: 100%;
        }

        .back-portal:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 30px rgba(30, 144, 255, 0.4);
            border-color: rgba(30, 144, 255, 0.8);
        }

        /* Main cosmic container */
        .cosmic-container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 80px 20px;
            position: relative;
            z-index: 10;
        }

        /* Hero constellation */
        .hero-constellation {
            text-align: center;
            margin-bottom: 120px;
            position: relative;
            animation: heroRise 2s ease-out;
        }

        @keyframes heroRise {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .cosmic-hero-title {
            font-size: 64px;
            font-weight: 900;
            margin-bottom: 30px;
            background: linear-gradient(45deg, #1E90FF, #4169E1, #6495ED, #87CEEB, #1E90FF);
            background-size: 400% 400%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: cosmicPulse 4s ease-in-out infinite;
            text-shadow: 0 0 50px rgba(30, 144, 255, 0.6);
            position: relative;
        }

        @keyframes cosmicPulse {
            0%, 100% { 
                background-position: 0% 50%;
                filter: brightness(1) drop-shadow(0 0 30px rgba(30, 144, 255, 0.4));
            }
            50% { 
                background-position: 100% 50%;
                filter: brightness(1.3) drop-shadow(0 0 60px rgba(30, 144, 255, 0.8));
            }
        }

        .cosmic-subtitle {
            font-size: 24px;
            color: rgba(135, 206, 250, 0.9);
            margin-bottom: 40px;
            animation: subtitleGlow 3s ease-in-out infinite;
        }

        @keyframes subtitleGlow {
            0%, 100% { text-shadow: 0 0 20px rgba(135, 206, 250, 0.3); }
            50% { text-shadow: 0 0 40px rgba(135, 206, 250, 0.6); }
        }

        /* Developer constellation layout */
        .developer-constellation {
            position: relative;
            height: 1000px; /* Increased height to provide more vertical space */
            margin: 100px 0;
        }

        .developer-node {
            position: absolute;
            width: 320px;
            height: 400px;
            background: rgba(15, 25, 45, 0.9);
            backdrop-filter: blur(15px);
            border: 2px solid rgba(30, 144, 255, 0.3);
            border-radius: 30px;
            padding: 30px;
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            animation: nodeFloat 8s ease-in-out infinite;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.6);
        }

        .developer-node:nth-child(1) {
            top: 500px; /* Moved to bottom left */
            left: 10%;
            animation-delay: 0s;
        }

        .developer-node:nth-child(2) {
            top: 100px; /* Kept at top center */
            left: 50%;
            transform: translateX(-50%);
            animation-delay: 2s;
        }

        .developer-node:nth-child(3) {
            top: 500px; /* Positioned at bottom right */
            right: 10%;
            left: auto;
            animation-delay: 4s;
        }

        /* Connection lines between nodes */
        .constellation-line {
            position: absolute;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(30, 144, 255, 0.4), transparent);
            animation: lineGlow 6s ease-in-out infinite;
            z-index: -1;
        }

        .line-1 {
            top: 250px;
            left: 25%;
            width: 300px;
            transform: rotate(25deg);
            animation-delay: 0s;
        }

        .line-2 {
            top: 400px;
            right: 25%;
            width: 250px;
            transform: rotate(-30deg);
            animation-delay: 2s;
        }

        /* Custom avatars */
        .cosmic-avatar {
            width: 120px;
            height: 120px;
            margin: 0 auto 25px;
            position: relative;
            border-radius: 50%;
            background: linear-gradient(135deg, #1E90FF, #4169E1, #6495ED);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: 800;
            color: #fff;
            animation: avatarPulse 6s ease-in-out infinite;
            box-shadow: 0 0 30px rgba(30, 144, 255, 0.5);
        }

        .cosmic-avatar::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            background: linear-gradient(45deg, #1E90FF, #4169E1, #6495ED, #87CEEB);
            border-radius: 50%;
            z-index: -1;
            opacity: 0;
            animation: avatarRing 4s ease-in-out infinite;
        }

        @keyframes avatarPulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 30px rgba(30, 144, 255, 0.5); }
            50% { transform: scale(1.1); box-shadow: 0 0 50px rgba(30, 144, 255, 0.8); }
        }

        @keyframes avatarRing {
            0%, 100% { opacity: 0; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.2); }
        }

        /* Developer info styling */
        .dev-name {
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #e0e0e0, #ffffff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: nameShimmer 3s ease-in-out infinite;
        }

        @keyframes nameShimmer {
            0%, 100% { filter: brightness(1) drop-shadow(0 0 10px rgba(255, 255, 255, 0.3)); }
            50% { filter: brightness(1.3) drop-shadow(0 0 20px rgba(255, 255, 255, 0.6)); }
        }

        .dev-id {
            font-size: 16px;
            color: #4169E1;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            letter-spacing: 1px;
            animation: idGlow 4s ease-in-out infinite;
        }

        @keyframes idGlow {
            0%, 100% { text-shadow: 0 0 10px rgba(65, 105, 225, 0.5); }
            50% { text-shadow: 0 0 20px rgba(65, 105, 225, 0.8); }
        }

        .dev-role {
            background: rgba(30, 144, 255, 0.2);
            border: 1px solid rgba(30, 144, 255, 0.5);
            color: #87CEEB;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 25px;
            animation: roleShimmer 5s ease-in-out infinite;
        }

        @keyframes roleShimmer {
            0%, 100% { box-shadow: 0 0 15px rgba(30, 144, 255, 0.3); }
            50% { box-shadow: 0 0 30px rgba(30, 144, 255, 0.6); }
        }

        .dev-skills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
        }

        .skill-orb {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(135, 206, 250, 0.3);
            color: rgba(255, 255, 255, 0.9);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            animation: orbFloat 4s ease-in-out infinite;
        }

        .skill-orb:nth-child(odd) { animation-delay: 0.5s; }
        .skill-orb:nth-child(even) { animation-delay: 1.5s; }

        @keyframes orbFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }

        .skill-orb:hover {
            background: rgba(30, 144, 255, 0.3);
            border-color: rgba(30, 144, 255, 0.6);
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 5px 15px rgba(30, 144, 255, 0.4);
        }

        /* Connection lines between nodes */
        .constellation-line, .line-1, .line-2 {
            display: none;
        }

        /* Project info cosmic panel */
        .cosmic-info-panel {
            background: rgba(10, 20, 40, 0.9);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(30, 144, 255, 0.4);
            border-radius: 40px;
            padding: 60px;
            margin-top: 120px;
            position: relative;
            overflow: hidden;
            animation: panelGlow 8s ease-in-out infinite;
        }

        @keyframes panelGlow {
            0%, 100% { 
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8), 0 0 40px rgba(30, 144, 255, 0.2);
                border-color: rgba(30, 144, 255, 0.4);
            }
            50% { 
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8), 0 0 80px rgba(30, 144, 255, 0.5);
                border-color: rgba(30, 144, 255, 0.8);
            }
        }

        .cosmic-info-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #1E90FF, #4169E1, #6495ED, #87CEEB, #1E90FF);
            background-size: 300% 100%;
            animation: cosmicBorder 4s linear infinite;
        }

        @keyframes cosmicBorder {
            0% { background-position: 0% 0%; }
            100% { background-position: 300% 0%; }
        }

        .info-title {
            font-size: 36px;
            font-weight: 800;
            text-align: center;
            margin-bottom: 40px;
            background: linear-gradient(135deg, #1E90FF, #4169E1, #6495ED);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: infoTitleGlow 5s ease-in-out infinite;
        }

        @keyframes infoTitleGlow {
            0%, 100% { 
                filter: brightness(1) drop-shadow(0 0 30px rgba(30, 144, 255, 0.4));
                transform: scale(1);
            }
            50% { 
                filter: brightness(1.2) drop-shadow(0 0 50px rgba(30, 144, 255, 0.7));
                transform: scale(1.02);
            }
        }

        .info-content {
            font-size: 18px;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.9);
            text-align: center;
            max-width: 1000px;
            margin: 0 auto;
            animation: contentGlow 6s ease-in-out infinite;
        }

        @keyframes contentGlow {
            0%, 100% { text-shadow: 0 0 20px rgba(255, 255, 255, 0.1); }
            50% { text-shadow: 0 0 30px rgba(255, 255, 255, 0.3); }
        }

        /* Responsive design adjustments */
        @media (max-width: 1200px) {
            .developer-constellation {
                height: auto;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 40px;
            }

            .developer-node {
                position: static;
                transform: none !important;
                margin-bottom: 20px;
            }

            .constellation-line {
                display: none;
            }
        }

        @media (max-width: 1000px) {
            .developer-constellation {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 30px;
                padding: 30px 15px;
            }
            
            .developer-node {
                height: 360px;
                padding: 25px;
            }
        }

        @media (max-width: 768px) {
            .cosmic-hero-title {
                font-size: 42px;
            }

            .developer-node {
                width: 280px;
                height: 360px;
                padding: 25px;
            }

            .cosmic-info-panel {
                padding: 40px 30px;
            }

            .info-title {
                font-size: 28px;
            }

            .info-content {
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .cosmic-hero-title {
                font-size: 32px;
            }

            .developer-node {
                max-width: 250px;
                height: 340px;
                padding: 20px;
            }

            .cosmic-avatar {
                width: 100px;
                height: 100px;
                font-size: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="constellation-bg"></div>
    
    <div class="particle-system">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <header class="cosmic-header">
        <div class="header-content">
            <h1 class="cosmic-title">Cosmic Developers</h1>
            <nav>
                <a href="javascript:history.back()" class="back-portal">← Return to Base</a>
            </nav>
        </div>
    </header>

    <main class="cosmic-container">
        <section class="hero-constellation">
            <h1 class="cosmic-hero-title">The Digital Architects</h1>
            <p class="cosmic-subtitle">Crafting Tomorrow's Solutions Today</p>
        </section>

        <section class="developer-constellation">
            
            
            <div class="developer-node">
                <div class="cosmic-avatar">🚀</div>
                <h3 class="dev-name">Happyeaster Nathanael Maindoka</h3>
                <div class="dev-role">230211060120t</div>
                <div class="dev-skills">
                </div>
            </div>

            <div class="developer-node">
                <div class="cosmic-avatar">🎨</div>
                <h3 class="dev-name">Karyn Marchya Putri</h3>
                <div class="dev-role">230211060067
                </div>
            </div>

            <div class="developer-node">
                <div class="cosmic-avatar">🔬</div>
                <h3 class="dev-name">Kezia Floresita Ngama</h3>
                <div class="dev-role">230211060084</div>
                <div class="dev-skills">
                </div>
            </div>
        </section>

        <section class="cosmic-info-panel">
            <h2 class="info-title">Mission: Student Report Management System</h2>
            <div class="info-content">
                <p>
                    Our digital ecosystem represents a revolutionary approach to student report management, 
                    designed to bridge the communication gap between students and educational institutions. 
                    This sophisticated web platform transforms traditional paper-based reporting into a 
                    streamlined, secure, and efficient digital experience.
                </p>
                <br>
                <p>
                    <strong>Core Functionality:</strong> The system empowers students to submit reports 
                    with complete anonymity protection while providing administrators with comprehensive 
                    tracking and management capabilities. Features include real-time status updates, 
                    role-based access control, and an intuitive dashboard that visualizes report 
                    progression from submission to resolution.
                </p>
                <br>
                <p>
                    <strong>Why We Built This:</strong> Recognizing the need for transparent, accessible 
                    communication channels in educational environments, we developed this platform to 
                    eliminate barriers that prevent students from voicing concerns. Our solution ensures 
                    confidentiality, promotes accountability, and creates a digital paper trail that 
                    benefits both students and administration.
                </p>
                <br>
                <p>
                    <strong>Technical Excellence:</strong> Built with PHP and MySQL, featuring responsive 
                    design, secure authentication, and modern web standards. The platform demonstrates 
                    our commitment to creating meaningful technology that serves real-world needs while 
                    maintaining the highest standards of security and user experience.
                </p>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced parallax effect for particles
            document.addEventListener('mousemove', function(e) {
                const particles = document.querySelectorAll('.particle');
                const x = e.clientX / window.innerWidth;
                const y = e.clientY / window.innerHeight;
                
                particles.forEach((particle, index) => {
                    const speed = (index % 4 + 1) * 0.8;
                    const xOffset = (x - 0.5) * speed * 30;
                    const yOffset = (y - 0.5) * speed * 30;
                    
                    particle.style.transform = `translate(${xOffset}px, ${yOffset}px)`;
                });
            });

            // Interactive developer nodes
            const nodes = document.querySelectorAll('.developer-node');
            nodes.forEach((node, index) => {
                node.addEventListener('mouseenter', function() {
                    // Create cosmic ripple effect
                    const ripple = document.createElement('div');
                    ripple.style.cssText = `
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        width: 0;
                        height: 0;
                        background: radial-gradient(circle, rgba(30, 144, 255, 0.4) 0%, transparent 70%);
                        border-radius: 50%;
                        transform: translate(-50%, -50%);
                        animation: cosmicRipple 1.5s ease-out;
                        pointer-events: none;
                        z-index: 0;
                    `;
                    
                    this.appendChild(ripple);
                    
                    // Enhanced glow effect
                    this.style.boxShadow = '0 25px 60px rgba(30, 144, 255, 0.4), 0 0 100px rgba(30, 144, 255, 0.2)';
                    
                    setTimeout(() => {
                        if (ripple.parentNode) {
                            ripple.parentNode.removeChild(ripple);
                        }
                    }, 1500);
                });
                
                node.addEventListener('mouseleave', function() {
                    this.style.boxShadow = '0 15px 40px rgba(0, 0, 0, 0.6)';
                });

                // Staggered entrance animation
                node.style.animationDelay = (index * 0.3) + 's';
            });

            // Avatar interaction effects
            const avatars = document.querySelectorAll('.cosmic-avatar');
            avatars.forEach(avatar => {
                avatar.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.2)';
                    this.style.boxShadow = '0 0 60px rgba(30, 144, 255, 0.8), 0 0 100px rgba(30, 144, 255, 0.4)';
                });
                
                avatar.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                    this.style.boxShadow = '0 0 30px rgba(30, 144, 255, 0.5)';
                });
            });

            // Scroll-triggered animations
            const observerOptions = {
                threshold: 0.2,
                rootMargin: '0px 0px -100px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                        entry.target.classList.add('visible');
                    }
                });
            }, observerOptions);

            // Observe animated elements
            const animatedElements = document.querySelectorAll('.developer-node, .cosmic-info-panel');
            animatedElements.forEach(el => observer.observe(el));

            // Dynamic title typing effect
            const heroTitle = document.querySelector('.cosmic-hero-title');
            const originalText = heroTitle.textContent;
            heroTitle.textContent = '';
            
            let i = 0;
            const typeWriter = () => {
                if (i < originalText.length) {
                    heroTitle.textContent += originalText.charAt(i);
                    i++;
                    setTimeout(typeWriter, 80);
                }
            };
            
            setTimeout(typeWriter, 1500);

            // Constellation line animation trigger
            const lines = document.querySelectorAll('.constellation-line');
            setTimeout(() => {
                lines.forEach((line, index) => {
                    line.style.opacity = '1';
                    line.style.animationDelay = (index * 1) + 's';
                });
            }, 3000);
        });

        // Add CSS for cosmic ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes cosmicRipple {
                0% {
                    width: 0;
                    height: 0;
                    opacity: 1;
                }
                100% {
                    width: 400px;
                    height: 400px;
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
