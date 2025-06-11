<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="ENSA Al Hoceima - Système de Gestion des Affectations">
    <title>ENSA Al Hoceima - Connexion</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <style>
        :root {
            /* Logo-matching colors with darker variants */
            --logo-blue: #1e40af;
            --logo-blue-dark: #1e3a8a;
            --logo-green: #059669;
            --logo-green-dark: #047857;
            --logo-accent: #0ea5e9;
            --logo-accent-dark: #0284c7;

            /* Gradients based on logo colors */
            --primary-gradient: linear-gradient(135deg, var(--logo-blue) 0%, var(--logo-blue-dark) 100%);
            --secondary-gradient: linear-gradient(135deg, var(--logo-green) 0%, var(--logo-green-dark) 100%);
            --accent-gradient: linear-gradient(135deg, var(--logo-accent) 0%, var(--logo-accent-dark) 100%);
            --fire-gradient: linear-gradient(135deg, #ff6b35 0%, #f7931e 50%, #ffcc02 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #000000;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* Dynamic Background with MANY Moving Circles */
        .dynamic-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
            overflow: hidden;
        }

        .moving-circle {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: moveAndFade 30s infinite ease-in-out;
            will-change: transform, opacity;
        }

        /* Large Circles */
        .moving-circle:nth-child(1) {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, var(--logo-blue) 0%, transparent 60%);
            top: -200px;
            left: -200px;
            animation-delay: 0s;
            animation-duration: 30s;
        }

        .moving-circle:nth-child(2) {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, var(--logo-green) 0%, transparent 60%);
            top: 10%;
            right: -175px;
            animation-delay: 5s;
            animation-duration: 35s;
        }

        .moving-circle:nth-child(3) {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, var(--logo-accent) 0%, transparent 60%);
            bottom: -250px;
            left: 20%;
            animation-delay: 10s;
            animation-duration: 40s;
        }

        /* Medium Circles */
        .moving-circle:nth-child(4) {
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, var(--logo-blue-dark) 0%, transparent 60%);
            top: 50%;
            right: 10%;
            animation-delay: 15s;
            animation-duration: 28s;
        }

        .moving-circle:nth-child(5) {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, var(--logo-green-dark) 0%, transparent 60%);
            top: 30%;
            left: -150px;
            animation-delay: 20s;
            animation-duration: 32s;
        }

        .moving-circle:nth-child(6) {
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, var(--logo-accent-dark) 0%, transparent 60%);
            bottom: 20%;
            right: -100px;
            animation-delay: 25s;
            animation-duration: 26s;
        }

        /* Small Circles */
        .moving-circle:nth-child(7) {
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, var(--logo-blue) 0%, transparent 70%);
            top: 80%;
            left: 10%;
            animation-delay: 2s;
            animation-duration: 22s;
        }

        .moving-circle:nth-child(8) {
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, var(--logo-green) 0%, transparent 70%);
            top: 5%;
            left: 60%;
            animation-delay: 7s;
            animation-duration: 24s;
        }

        .moving-circle:nth-child(9) {
            width: 120px;
            height: 120px;
            background: radial-gradient(circle, var(--logo-accent) 0%, transparent 70%);
            bottom: 60%;
            left: 80%;
            animation-delay: 12s;
            animation-duration: 20s;
        }

        .moving-circle:nth-child(10) {
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, var(--logo-blue-dark) 0%, transparent 70%);
            top: 70%;
            right: 30%;
            animation-delay: 17s;
            animation-duration: 27s;
        }

        /* Extra Small Circles for density */
        .moving-circle:nth-child(11) {
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, var(--logo-green-dark) 0%, transparent 80%);
            top: 15%;
            left: 30%;
            animation-delay: 3s;
            animation-duration: 18s;
        }

        .moving-circle:nth-child(12) {
            width: 80px;
            height: 80px;
            background: radial-gradient(circle, var(--logo-accent-dark) 0%, transparent 80%);
            bottom: 40%;
            right: 60%;
            animation-delay: 8s;
            animation-duration: 16s;
        }

        .moving-circle:nth-child(13) {
            width: 160px;
            height: 160px;
            background: radial-gradient(circle, var(--logo-blue) 0%, transparent 75%);
            top: 40%;
            left: 70%;
            animation-delay: 13s;
            animation-duration: 21s;
        }

        .moving-circle:nth-child(14) {
            width: 90px;
            height: 90px;
            background: radial-gradient(circle, var(--logo-green) 0%, transparent 80%);
            bottom: 10%;
            left: 40%;
            animation-delay: 18s;
            animation-duration: 19s;
        }

        .moving-circle:nth-child(15) {
            width: 130px;
            height: 130px;
            background: radial-gradient(circle, var(--logo-accent) 0%, transparent 75%);
            top: 25%;
            right: 80%;
            animation-delay: 23s;
            animation-duration: 23s;
        }

        @keyframes moveAndFade {
            0%, 100% {
                transform: translate(0, 0) scale(1);
                opacity: 0.1;
            }
            25% {
                transform: translate(100px, -50px) scale(1.2);
                opacity: 0.3;
            }
            50% {
                transform: translate(-50px, 100px) scale(0.8);
                opacity: 0.2;
            }
            75% {
                transform: translate(150px, 50px) scale(1.1);
                opacity: 0.4;
            }
        }

        /* Main Container */
        .login-container {
            position: relative;
            z-index: 10;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
        }

        /* Logo Animation Container */
        .logo-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 100;
            opacity: 1;
        }

        /* Skip Animation Classes */
        .skip-entrance .logo-container {
            animation: logoSequence 0.5s ease-out forwards !important;
        }

        .skip-entrance .explosion-container {
            animation: explosionEffect 0.3s ease-out 0.4s forwards !important;
        }

        .skip-entrance .login-form {
            animation: formExplosiveReveal 0.4s ease-out 0.6s forwards !important;
        }

        .skip-entrance .form-title {
            animation: titleExplosiveSlide 0.3s ease-out 0.8s forwards !important;
        }

        .skip-entrance .form-inputs .input-group:nth-child(1) {
            animation: inputExplosiveSlide 0.2s ease-out 0.9s forwards !important;
        }
        .skip-entrance .form-inputs .input-group:nth-child(2) {
            animation: inputExplosiveSlide 0.2s ease-out 1s forwards !important;
        }
        .skip-entrance .form-inputs .input-group:nth-child(3) {
            animation: inputExplosiveSlide 0.2s ease-out 1.1s forwards !important;
        }
        .skip-entrance .form-inputs .input-group:nth-child(4) {
            animation: inputExplosiveSlide 0.2s ease-out 1.2s forwards !important;
        }

        /* Skip Indicator */
        .skip-indicator {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 107, 53, 0.3);
            border-radius: 15px;
            padding: 15px 25px;
            color: white;
            font-size: 0.9rem;
            font-weight: 500;
            z-index: 200;
            opacity: 1;
            animation: skipIndicatorPulse 2s ease-in-out infinite;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .skip-indicator:hover {
            background: rgba(255, 107, 53, 0.2);
            border-color: rgba(255, 107, 53, 0.6);
            transform: scale(1.05);
        }

        .skip-indicator.hidden {
            opacity: 0;
            pointer-events: none;
        }

        @keyframes skipIndicatorPulse {
            0%, 100% {
                box-shadow: 0 0 20px rgba(255, 107, 53, 0.3);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 0 30px rgba(255, 107, 53, 0.5);
                transform: scale(1.02);
            }
        }

        .logo-image {
            width: 150px;
            height: 150px;
            border-radius: 20px;
            box-shadow: 0 0 50px rgba(30, 64, 175, 0.5);
            animation: logoSequence 6s ease-in-out forwards;
            position: relative;
            overflow: hidden;
        }

        .logo-image::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.4), transparent);
            transform: rotate(45deg);
            animation: logoShine 1.5s ease-in-out infinite;
        }

        .logo-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 20px;
        }

        /* Explosion Effect */
        .explosion-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            height: 300px;
            opacity: 0;
            animation: explosionEffect 1s ease-out 5s forwards;
            pointer-events: none;
            z-index: 99;
        }

        .explosion-particle {
            position: absolute;
            width: 8px;
            height: 8px;
            background: var(--fire-gradient);
            border-radius: 50%;
            opacity: 0;
        }

        /* Logo Animation Sequence */
        @keyframes logoSequence {
            /* Zoom Out Phase */
            0% {
                transform: scale(1);
                opacity: 1;
            }
            15% {
                transform: scale(0.3);
                opacity: 0.8;
            }
            /* Zoom In Phase */
            30% {
                transform: scale(0.3);
                opacity: 0.8;
            }
            60% {
                transform: scale(2.5);
                opacity: 1;
                box-shadow: 0 0 100px rgba(30, 64, 175, 0.8);
            }
            /* Explosion Preparation */
            80% {
                transform: scale(2.8);
                opacity: 1;
                box-shadow: 0 0 150px rgba(255, 107, 53, 0.9);
            }
            /* Explosion */
            85% {
                transform: scale(3.2);
                opacity: 0.8;
                box-shadow: 0 0 200px rgba(255, 204, 2, 1);
            }
            100% {
                transform: scale(0);
                opacity: 0;
            }
        }

        @keyframes logoShine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        @keyframes explosionEffect {
            0% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(0);
            }
            50% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(2);
            }
            100% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(4);
            }
        }

        /* Large Professional Login Form (65% of window) - PERFECTLY CENTERED */
        .login-form {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            width: 65vw;
            max-width: 800px;
            min-width: 500px;
            height: auto;
            min-height: 600px;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            backdrop-filter: blur(25px);
            border-radius: 30px;
            padding: 60px;
            box-shadow:
                0 50px 100px rgba(0, 0, 0, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            opacity: 0;
            animation: formExplosiveReveal 1.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) 6s forwards;
            border: 2px solid rgba(30, 64, 175, 0.3);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
            z-index: 1000;
        }

        .login-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg,
                rgba(30, 64, 175, 0.1) 0%,
                rgba(5, 150, 105, 0.1) 50%,
                rgba(14, 165, 233, 0.1) 100%);
            border-radius: 30px;
            z-index: -1;
        }

        .login-form::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
            animation: formShine 3s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes formExplosiveReveal {
            0% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0) rotate(180deg);
                filter: blur(20px);
            }
            50% {
                opacity: 0.8;
                transform: translate(-50%, -50%) scale(1.1) rotate(0deg);
                filter: blur(5px);
            }
            100% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1) rotate(0deg);
                filter: blur(0px);
            }
        }

        @keyframes formShine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .form-title {
            text-align: center;
            margin-bottom: 40px;
            opacity: 0;
            animation: titleExplosiveSlide 1s ease-out 7s forwards;
        }

        .form-title h1 {
            background: linear-gradient(135deg, var(--logo-blue) 0%, var(--logo-accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
            font-size: 3rem;
            margin-bottom: 10px;
            text-shadow: 0 0 30px rgba(30, 64, 175, 0.3);
        }

        .form-title h2 {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            font-size: 1.4rem;
            margin-bottom: 5px;
        }

        .form-title p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
            font-weight: 400;
        }

        @keyframes titleExplosiveSlide {
            0% {
                opacity: 0;
                transform: translateY(-50px) scale(0.5);
                filter: blur(10px);
            }
            60% {
                opacity: 1;
                transform: translateY(10px) scale(1.1);
                filter: blur(2px);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
                filter: blur(0px);
            }
        }

        /* Centered Form Content */
        .form-content {
            width: 100%;
            max-width: 500px;
            display: flex;
            flex-direction: column;
            gap: 30px;
            align-items: center;
        }

        .form-inputs {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .input-group {
            position: relative;
            width: 100%;
            margin-bottom: 0;
            opacity: 0;
            animation: inputExplosiveSlide 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
        }

        .form-inputs .input-group:nth-child(1) { animation-delay: 7.5s; }
        .form-inputs .input-group:nth-child(2) { animation-delay: 7.8s; }
        .form-inputs .input-group:nth-child(3) { animation-delay: 8.1s; }
        .form-inputs .input-group:nth-child(4) { animation-delay: 8.4s; }

        @keyframes inputExplosiveSlide {
            0% {
                opacity: 0;
                transform: translateX(-100px) scale(0.5);
                filter: blur(10px);
            }
            60% {
                opacity: 1;
                transform: translateX(10px) scale(1.05);
                filter: blur(2px);
            }
            100% {
                opacity: 1;
                transform: translateX(0) scale(1);
                filter: blur(0px);
            }
        }

        @keyframes logoFadeIn {
            from {
                opacity: 0;
                transform: scale(0.8) rotate(-10deg);
            }
            to {
                opacity: 0.8;
                transform: scale(1) rotate(0deg);
            }
        }

        .form-control {
            width: 100%;
            padding: 20px 25px 20px 60px;
            border: 3px solid rgba(30, 64, 175, 0.2);
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            font-size: 1.1rem;
            color: white;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            font-weight: 500;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
            font-weight: 400;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--logo-blue);
            background: rgba(255, 255, 255, 0.15);
            box-shadow:
                0 0 30px rgba(30, 64, 175, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            transform: translateY(-3px) scale(1.02);
        }

        .input-icon {
            position: absolute;
            left: 22px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--logo-accent);
            font-size: 1.3rem;
            z-index: 2;
            transition: all 0.3s ease;
            animation: iconPulse 3s ease-in-out infinite;
            will-change: transform, opacity;
        }

        .input-group:focus-within .input-icon {
            color: #ff6b35;
            transform: translateY(-50%) scale(1.2);
            animation: iconFirePulse 2s ease-in-out infinite;
        }

        /* Optimized icon animations - GPU accelerated */
        @keyframes iconPulse {
            0%, 100% {
                transform: translateY(-50%) scale(1);
                opacity: 0.8;
            }
            50% {
                transform: translateY(-50%) scale(1.1);
                opacity: 1;
            }
        }

        @keyframes iconFirePulse {
            0%, 100% {
                transform: translateY(-50%) scale(1.2);
                opacity: 1;
            }
            50% {
                transform: translateY(-50%) scale(1.3);
                opacity: 0.9;
            }
        }

        /* Fire Login Button */
        .btn-login {
            width: 100%;
            padding: 20px;
            background: linear-gradient(135deg, var(--logo-blue) 0%, var(--logo-accent) 50%, var(--logo-green) 100%);
            border: none;
            border-radius: 15px;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 30px rgba(30, 64, 175, 0.4);
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s ease;
        }

        .btn-login::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
            transform: translate(-50%, -50%);
            transition: all 0.6s ease;
            border-radius: 50%;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover::after {
            width: 300px;
            height: 300px;
        }

        .btn-login:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow:
                0 20px 60px rgba(30, 64, 175, 0.6),
                0 0 50px rgba(14, 165, 233, 0.4);
            background: linear-gradient(135deg, var(--logo-blue-dark) 0%, var(--logo-accent-dark) 50%, var(--logo-green-dark) 100%);
        }

        .btn-login:active {
            transform: translateY(-2px) scale(0.98);
        }

        /* Remember Me */
        .remember-me {
            display: flex;
            align-items: center;
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }

        .remember-me input[type="checkbox"] {
            margin-right: 12px;
            transform: scale(1.3);
            accent-color: var(--logo-blue);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-form {
                width: 90vw;
                min-width: 350px;
                padding: 40px 30px;
            }

            .form-title h1 {
                font-size: 2.2rem;
            }

            .form-title h2 {
                font-size: 1.2rem;
            }

            .form-control {
                padding: 18px 22px 18px 55px;
                font-size: 1rem;
            }

            .btn-login {
                padding: 18px;
                font-size: 1.1rem;
            }
        }

        @media (max-width: 480px) {
            .login-form {
                width: 95vw;
                min-width: 300px;
                padding: 30px 20px;
            }

            .form-title h1 {
                font-size: 1.8rem;
            }

            .form-content {
                max-width: 100%;
            }
        }

        /* Explosion Particle Animations */
        @keyframes explode1 {
            0% { transform: translate(0, 0) scale(1); opacity: 1; }
            100% { transform: translate(-100px, -100px) scale(0); opacity: 0; }
        }

        @keyframes explode2 {
            0% { transform: translate(0, 0) scale(1); opacity: 1; }
            100% { transform: translate(150px, -80px) scale(0); opacity: 0; }
        }

        @keyframes explode3 {
            0% { transform: translate(0, 0) scale(1); opacity: 1; }
            100% { transform: translate(-80px, 120px) scale(0); opacity: 0; }
        }

        @keyframes explode4 {
            0% { transform: translate(0, 0) scale(1); opacity: 1; }
            100% { transform: translate(200px, 150px) scale(0); opacity: 0; }
        }

        @keyframes explode5 {
            0% { transform: translate(0, 0) scale(1); opacity: 1; }
            100% { transform: translate(-150px, -200px) scale(0); opacity: 0; }
        }

        /* Additional optimized explosion animations */
        @keyframes explode6 {
            0% { transform: translate(0, 0) scale(1); opacity: 1; }
            100% { transform: translate(120px, -90px) scale(0); opacity: 0; }
        }

        @keyframes explode7 {
            0% { transform: translate(0, 0) scale(1); opacity: 1; }
            100% { transform: translate(-90px, 130px) scale(0); opacity: 0; }
        }

        @keyframes explode8 {
            0% { transform: translate(0, 0) scale(1); opacity: 1; }
            100% { transform: translate(180px, -60px) scale(0); opacity: 0; }
        }

        @keyframes explode9 {
            0% { transform: translate(0, 0) scale(1); opacity: 1; }
            100% { transform: translate(-110px, 160px) scale(0); opacity: 0; }
        }

        @keyframes explode10 {
            0% { transform: translate(0, 0) scale(1); opacity: 1; }
            100% { transform: translate(0px, -180px) scale(0); opacity: 0; }
        }

        @keyframes explode11 {
            0% { transform: translate(0, 0) scale(1); opacity: 1; }
            100% { transform: translate(0px, 170px) scale(0); opacity: 0; }
        }

        @keyframes explode12 {
            0% { transform: translate(0, 0) scale(1); opacity: 1; }
            100% { transform: translate(-190px, 0px) scale(0); opacity: 0; }
        }

        @keyframes explode13 {
            0% { transform: translate(0, 0) scale(1); opacity: 1; }
            100% { transform: translate(200px, 0px) scale(0); opacity: 0; }
        }
    </style>
</head>
<body>
    <!-- Dynamic Background with MANY Moving Circles -->
    <div class="dynamic-background">
        <div class="moving-circle"></div>
        <div class="moving-circle"></div>
        <div class="moving-circle"></div>
        <div class="moving-circle"></div>
        <div class="moving-circle"></div>
        <div class="moving-circle"></div>
        <div class="moving-circle"></div>
        <div class="moving-circle"></div>
        <div class="moving-circle"></div>
        <div class="moving-circle"></div>
        <div class="moving-circle"></div>
        <div class="moving-circle"></div>
        <div class="moving-circle"></div>
        <div class="moving-circle"></div>
        <div class="moving-circle"></div>
    </div>

    <!-- Skip Indicator -->
    <div class="skip-indicator" id="skipIndicator">
        <i class="fas fa-forward me-2"></i>
        Cliquez pour passer l'animation
    </div>

    <!-- Main Container -->
    <div class="login-container">
        <!-- Logo Animation -->
        <div class="logo-container">
            <div class="logo-image">
                <img src="{{ asset('images/logo.png') }}" alt="ENSA Al Hoceima Logo">
            </div>
        </div>

        <!-- Explosion Effect with MORE FIRE PARTICLES -->
        <div class="explosion-container">
            <!-- Main explosion particles -->
            <div class="explosion-particle" style="top: 50%; left: 50%; animation: explode1 0.8s ease-out;"></div>
            <div class="explosion-particle" style="top: 30%; left: 30%; animation: explode2 0.8s ease-out 0.1s;"></div>
            <div class="explosion-particle" style="top: 70%; left: 70%; animation: explode3 0.8s ease-out 0.2s;"></div>
            <div class="explosion-particle" style="top: 20%; left: 80%; animation: explode4 0.8s ease-out 0.3s;"></div>
            <div class="explosion-particle" style="top: 80%; left: 20%; animation: explode5 0.8s ease-out 0.4s;"></div>

            <!-- Additional fire particles for more explosion -->
            <div class="explosion-particle" style="top: 40%; left: 60%; animation: explode6 0.8s ease-out 0.15s;"></div>
            <div class="explosion-particle" style="top: 60%; left: 40%; animation: explode7 0.8s ease-out 0.25s;"></div>
            <div class="explosion-particle" style="top: 35%; left: 65%; animation: explode8 0.8s ease-out 0.35s;"></div>
            <div class="explosion-particle" style="top: 65%; left: 35%; animation: explode9 0.8s ease-out 0.45s;"></div>
            <div class="explosion-particle" style="top: 25%; left: 50%; animation: explode10 0.8s ease-out 0.2s;"></div>
            <div class="explosion-particle" style="top: 75%; left: 50%; animation: explode11 0.8s ease-out 0.3s;"></div>
            <div class="explosion-particle" style="top: 50%; left: 25%; animation: explode12 0.8s ease-out 0.4s;"></div>
            <div class="explosion-particle" style="top: 50%; left: 75%; animation: explode13 0.8s ease-out 0.5s;"></div>
        </div>

        <!-- Large Professional Login Form - CENTERED -->
        <div class="login-form">
            <div class="form-title">
                <h1>ENSA Al Hoceima</h1>
                <h2>École Nationale des Sciences Appliquées</h2>
                <p>Système de Gestion des Affectations d'Enseignement</p>
            </div>

            <div class="form-content">
                <form method="POST" action="{{ route('login') }}" id="loginForm" class="form-inputs">
                    @csrf

                    <div class="input-group">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autocomplete="email"
                               autofocus
                               placeholder="Adresse email professionnelle">
                        @error('email')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <div class="input-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="Mot de passe sécurisé">
                        @error('password')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <div class="input-group">
                        <div class="remember-me">
                            <input type="checkbox"
                                   name="remember"
                                   id="remember"
                                   {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember">Se souvenir de moi sur cet appareil</label>
                        </div>
                    </div>

                    <div class="input-group">
                        <button type="submit" class="btn-login" aria-label="Se connecter au système">
                            <i class="fas fa-rocket me-2"></i>
                            Accéder au Système
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize page animations
        initializeAnimations();

        // Skip animation functionality
        initializeSkipFunction();

        // Handle form submission
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                const btn = this.querySelector('.btn-login');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Connexion...';

                // Add loading animation
                btn.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
                btn.style.transform = 'translateY(-1px)';
            });
        }
    });

    function initializeAnimations() {
        // Add entrance animation class to body
        document.body.classList.add('entrance-animation');

        // Initialize moving circles with optimized delays
        const circles = document.querySelectorAll('.moving-circle');
        circles.forEach((circle, index) => {
            circle.style.animationDelay = `${index * 4}s`;
            circle.style.animationDuration = `${30 + Math.random() * 15}s`;
            // Enable hardware acceleration
            circle.style.willChange = 'transform, opacity';
        });

        // Optimized input interactions with requestAnimationFrame
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                requestAnimationFrame(() => {
                    this.parentElement.style.transform = 'translateY(-3px) scale(1.02)';
                });
            });

            input.addEventListener('blur', function() {
                requestAnimationFrame(() => {
                    this.parentElement.style.transform = 'translateY(0) scale(1)';
                });
            });
        });

        // Optimized explosion particles trigger
        setTimeout(() => {
            requestAnimationFrame(() => {
                const explosionParticles = document.querySelectorAll('.explosion-particle');
                explosionParticles.forEach(particle => {
                    particle.style.opacity = '1';
                });
            });
        }, 5000);
    }

    function initializeSkipFunction() {
        let animationSkipped = false;
        const skipIndicator = document.getElementById('skipIndicator');
        const body = document.body;

        // Function to skip animation
        function skipAnimation() {
            if (animationSkipped) return;

            animationSkipped = true;

            // Add skip class to body for fast animations
            body.classList.add('skip-entrance');

            // Hide skip indicator
            skipIndicator.classList.add('hidden');

            // Remove skip indicator after animation
            setTimeout(() => {
                if (skipIndicator) {
                    skipIndicator.remove();
                }
            }, 1500);

            console.log('🔥 Animation skipped - Fast entrance activated!');
        }

        // Click anywhere to skip (during first 8 seconds)
        function handleSkipClick(e) {
            // Don't skip if clicking on form elements after they appear
            if (e.target.closest('.login-form') && animationSkipped) {
                return;
            }
            skipAnimation();
        }

        // Add click listener to entire document
        document.addEventListener('click', handleSkipClick);

        // Add specific click listener to skip indicator
        if (skipIndicator) {
            skipIndicator.addEventListener('click', function(e) {
                e.stopPropagation();
                skipAnimation();
            });
        }

        // Auto-hide skip indicator after entrance completes
        setTimeout(() => {
            if (!animationSkipped && skipIndicator) {
                skipIndicator.classList.add('hidden');
                setTimeout(() => {
                    if (skipIndicator) {
                        skipIndicator.remove();
                    }
                }, 500);
            }
            // Remove global click listener after entrance
            document.removeEventListener('click', handleSkipClick);
        }, 9000); // Hide after 9 seconds (full entrance duration)
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set a unique page visit identifier
        const visitId = Date.now();
        sessionStorage.setItem('loginPageVisitId', visitId);

        // Handle any kind of navigation (back, forward, refresh)
        window.addEventListener('pageshow', function(event) {
            // Handle back/forward navigation from cache
            if (event.persisted) {
                console.log("Page accessed from back/forward cache");
                forceSessionReset();
            }
        });

        // Additional navigation detection
        if (performance && performance.navigation) {
            if (performance.navigation.type === 2) { // Back/forward button navigation
                console.log("Back/forward navigation detected");
                forceSessionReset();
            }
        }

        // Normal page load handling
        initializeLoginPage();
    });

    function initializeLoginPage() {
        // Check if already handled a logout in this session
        const hasJustLoggedOut = sessionStorage.getItem('just_logged_out');

        if (hasJustLoggedOut) {
            sessionStorage.removeItem('just_logged_out');
            resetFormAndUI();
        } else {
            // Perform server-side logout
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            fetch('{{ route("logout") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Cache-Control': 'no-cache, no-store, must-revalidate'
                },
                credentials: 'same-origin'
            }).then(response => {
                console.log('Logout response status:', response.status);
                return response.text();
            }).then(() => {
                sessionStorage.setItem('just_logged_out', 'true');

                // Add cache-busting to prevent cached redirects
                window.location.href = window.location.pathname + '?nocache=' + Date.now();
            }).catch(error => {
                console.error('Logout error:', error);
                resetFormAndUI();
            });
        }
    }

    function forceSessionReset() {
        // Clear browser storage
        clearAuthData();

        // Force a clean reload to ensure fresh state
        window.location.href = window.location.pathname + '?nocache=' + Date.now();
    }

    function clearAuthData() {
        // Clear authentication-related data
        if (window.localStorage) {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
        }

        // Clear relevant session data but keep our control flags
        const justLoggedOut = sessionStorage.getItem('just_logged_out');
        const visitId = sessionStorage.getItem('loginPageVisitId');

        // Only clear auth-related session items
        sessionStorage.removeItem('auth_state');
        sessionStorage.removeItem('user_session');

        // Restore our control flags
        if (justLoggedOut) sessionStorage.setItem('just_logged_out', justLoggedOut);
        if (visitId) sessionStorage.setItem('loginPageVisitId', visitId);

        // Clear auth-related cookies but preserve CSRF
        clearAuthCookies();
    }

    function clearAuthCookies() {
        // Get all cookies
        document.cookie.split(';').forEach(function(c) {
            const cookieName = c.trim().split('=')[0];

            // Skip CSRF-related cookies
            if (!cookieName.includes('XSRF') && !cookieName.includes('csrf') &&
                cookieName !== 'laravel_session') {
                document.cookie = cookieName + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;';
            }
        });
    }

    function resetFormAndUI() {
        // Reset form elements but preserve CSRF token
        const form = document.getElementById('loginForm');
        if (form) {
            const inputs = form.querySelectorAll('input:not([name="_token"])');
            inputs.forEach(input => {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    input.checked = false;
                } else if (input.type !== 'hidden') {
                    input.value = '';
                }
            });
        }

        // Reset button state
        const loginBtn = document.querySelector('.btn-login');
        if (loginBtn) {
            loginBtn.disabled = false;
            loginBtn.innerHTML = '<i class="fas fa-rocket me-2"></i> Accéder au Système';
            loginBtn.style.background = 'linear-gradient(135deg, var(--logo-blue) 0%, var(--logo-accent) 50%, var(--logo-green) 100%)';
            loginBtn.style.transform = 'translateY(0)';
        }
    }
</script>
</body>
</html>
