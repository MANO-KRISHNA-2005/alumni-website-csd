<?php
// memories.php - Main memories page with database integration
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Memory Wall - Fixed Sticky Notes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Your existing CSS styles */
        :root {
            --bg: #1a0a0a;
            --red: #ff3333;
            --accent: #ff5555;
            --note-bg: #fff5f5;
            --note-shadow: #ffeeee;
            --pin-size: 16px;
            --wall-width: 3200px;
            --wall-height: 2400px;
            --sidebar-width: 300px;
            --minimap-scale: 0.15;
            /* Changed from red to yellow */
            --text-yellow: #ffcc00;
            --light-yellow: #ffdd33;
            --dark-yellow: #cc9900;
            /* Navigation variables - changed to yellow */
            --neon-yellow: #ffcc00;
            --neon-yellow-glow: rgba(255, 204, 0, 0.6);
            --ivory-white: #fffff0;
            --transition: all 0.3s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
            background: #1a0a0a;
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            color: var(--text-yellow); /* Changed from red to yellow */
            overflow: hidden;
            position: relative;
        }

        /* Loading indicator styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(255, 204, 0, 0.3);
            border-top: 5px solid #ffcc00;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* ===== NAVIGATION BAR ===== */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 12px 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(15px);
            border-bottom: 2px solid var(--neon-yellow); /* Changed to yellow */
            z-index: 1000;
            transition: var(--transition);
            box-shadow: 0 0 10px var(--neon-yellow-glow); /* Changed to yellow */
        }

        .navbar.scrolled {
            padding: 8px 0;
            background: rgba(0, 0, 0, 0.9);
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo-img {
            height: 32px;
            width: auto;
            filter: drop-shadow(0 0 4px var(--neon-yellow-glow)); /* Changed to yellow */
        }

        .nav-menu {
            display: flex;
            list-style: none;
            align-items: center;
            gap: 25px;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            text-decoration: none;
            color: var(--ivory-white);
            font-weight: 600;
            font-size: 0.9rem;
            transition: var(--transition);
            position: relative;
            font-family: 'Oxanium', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 8px 0;
        }

        .nav-link:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--neon-yellow); /* Changed to yellow */
            transition: var(--transition);
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--neon-yellow); /* Changed to yellow */
            text-shadow: 0 0 4px var(--neon-yellow-glow); /* Changed to yellow */
        }

        .nav-link:hover:after,
        .nav-link.active:after {
            width: 100%;
            box-shadow: 0 0 4px var(--neon-yellow-glow); /* Changed to yellow */
        }

        .hamburger {
            display: none;
            flex-direction: column;
            justify-content: space-around;
            width: 2rem;
            height: 2rem;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            z-index: 1001;
        }

        .hamburger span {
            width: 2rem;
            height: 0.25rem;
            background: var(--neon-yellow);
            border-radius: 10px;
            transition: all 0.3s linear;
            position: relative;
            transform-origin: 1px;
        }

        /* Red technology background with image */
        .wall-texture {
            position: fixed;
            inset: 0;
            background-image: url('https://picsum.photos/seed/psgtech/1920/1080.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -2;
        }

        .wall-texture::after {
            content: "";
            position: absolute;
            inset: 0;
            background: 
                /* Base wall color - dark red with tech pattern */
                linear-gradient(135deg, rgba(26, 10, 10, 0.85) 0%, rgba(42, 10, 10, 0.85) 50%, rgba(58, 10, 10, 0.85) 100%),
                /* Tech pattern */
                repeating-linear-gradient(
                    0deg,
                    rgba(255,51,51,0.05) 0px,
                    transparent 1px,
                    transparent 2px,
                    rgba(255,51,51,0.05) 3px,
                    transparent 4px
                ),
                repeating-linear-gradient(
                    90deg,
                    rgba(255,51,51,0.04) 0px,
                    transparent 1px,
                    transparent 2px,
                    rgba(255,51,51,0.04) 3px,
                    transparent 4px
                ),
                /* Circuit pattern */
                repeating-linear-gradient(
                    45deg,
                    rgba(255,51,51,0.03) 0px,
                    transparent 2px,
                    transparent 4px,
                    rgba(255,51,51,0.03) 6px,
                    transparent 8px
                );
            /* Wall imperfections */
            background-blend-mode: normal, multiply, multiply, screen;
            box-shadow: inset 0 0 100px rgba(0,0,0,0.3);
            z-index: 0;
        }

        /* Wall texture overlay for more realism */
        .wall-texture::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(255,51,51,0.08) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(0,0,0,0.05) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(255,51,51,0.06) 0%, transparent 50%);
            background-size: 500px 500px, 600px 600px, 400px 400px;
            opacity: 0.7;
            pointer-events: none;
            z-index: 1;
        }

        /* Wall cracks and imperfections */
        .wall-imperfections {
            position: fixed;
            inset: 0;
            background-image: 
                linear-gradient(45deg, transparent 48%, rgba(255,51,51,0.03) 49%, rgba(255,51,51,0.03) 51%, transparent 52%),
                linear-gradient(-45deg, transparent 48%, rgba(255,51,51,0.02) 49%, rgba(255,51,51,0.02) 51%, transparent 52%),
                linear-gradient(90deg, transparent 48%, rgba(255,51,51,0.01) 49%, rgba(255,51,51,0.01) 51%, transparent 52%);
            background-size: 200px 200px, 300px 300px, 150px 150px;
            opacity: 0.5;
            pointer-events: none;
            z-index: 1;
        }

        /* Updated header with integrated navigation */
        header {
            position: fixed;
            top: 65px; /* Adjusted to account for navbar */
            left: 0;
            right: 0;
            z-index: 40;
            background: linear-gradient(120deg, rgba(255,204,0,0.2), rgba(255,180,0,0.15), rgba(255,150,0,0.18)); /* Changed to yellow */
            backdrop-filter: blur(12px) saturate(1.4);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 15px;
            font-weight: 800;
            color: var(--text-yellow); /* Changed to yellow */
            text-shadow: 0 3px 16px rgba(0,0,0,0.7), 0 1px 3px rgba(255,204,0,0.4); /* Changed to yellow */
            box-shadow: 
                0 4px 24px rgba(0,0,0,0.6),
                inset 0 1px 0 rgba(255,255,255,0.1);
            user-select: none;
            border-bottom: 1px solid rgba(255,204,0,0.3); /* Changed to yellow */
            letter-spacing: 0.5px;
            height: 65px;
        }

        .header-title {
            font-size: 16px;
            text-align: center;
            flex: 1;
            line-height: 1.2;
            padding: 0 10px;
        }

        /* Enhanced Game Button */
        .game-btn {
            position: relative;
            background: linear-gradient(135deg, #ffcc00, #ffaa00, #ff8800);
            color: #fff;
            border: none;
            border-radius: 30px;
            padding: 10px 20px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 
                0 4px 15px rgba(255, 204, 0, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'Oxanium', sans-serif;
            overflow: hidden;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .game-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
            z-index: -1;
        }

        .game-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s;
            z-index: -1;
        }

        .game-btn:hover::before {
            left: 100%;
        }

        .game-btn:hover::after {
            opacity: 1;
        }

        .game-btn:hover {
            transform: translateY(-3px);
            box-shadow: 
                0 8px 25px rgba(255, 204, 0, 0.6),
                0 0 20px rgba(255, 204, 0, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.4);
        }

        .game-btn:active {
            transform: translateY(-1px);
            box-shadow: 
                0 4px 15px rgba(255, 204, 0, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }

        .game-btn-icon {
            display: inline-block;
            width: 18px;
            height: 18px;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M21,6H3C2.4,6,2,6.4,2,7v10c0,0.6,0.4,1,1,1h18c0.6,0,1-0.4,1-1V7C22,6.4,21.6,6,21,6z M20,16H4V8h16V16z"/><path d="M6,10h2v2H6V10z M9,10h2v2H9V10z M12,10h2v2H12V10z M15,10h2v2H15V10z"/></svg>');
            background-repeat: no-repeat;
            background-position: center;
        }

        .controls {
            position: fixed;
            right: 14px;
            top: 140px; /* Adjusted to account for navbar + header */
            z-index: 60;
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .ctrl-btn {
            background: linear-gradient(135deg, #ffcc00, #ffaa00, #ff8800); /* Changed to yellow */
            border: none;
            padding: 8px 14px;
            border-radius: 10px;
            box-shadow: 
                0 6px 16px rgba(0,0,0,0.5),
                inset 0 1px 0 rgba(255,255,255,0.4),
                inset 0 -1px 0 rgba(0,0,0,0.1);
            cursor: pointer;
            font-weight: 700;
            color: #fff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 13px;
            letter-spacing: 0.3px;
        }

        .ctrl-btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 
                0 10px 24px rgba(0,0,0,0.6),
                inset 0 1px 0 rgba(255,255,255,0.5);
        }

        .ctrl-btn:active {
            transform: translateY(-1px) scale(1.02);
        }

        .board-wrap {
            width: calc(100% - var(--sidebar-width));
            height: calc(100vh - 130px); /* Adjusted for navbar + header */
            overflow: auto;
            position: relative;
            -webkit-overflow-scrolling: touch;
            margin-left: var(--sidebar-width);
            margin-top: 130px; /* Adjusted for navbar + header */
            transition: transform 0.5s ease;
            /* Remove horizontal scrollbar and hide vertical scrollbar */
            overflow-x: hidden;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .board-wrap::-webkit-scrollbar {
            display: none;
        }

        .board {
            width: var(--wall-width);
            height: var(--wall-height);
            background: 
                /* Wall texture for the board - adjusted for red tech theme */
                linear-gradient(135deg, rgba(26, 10, 10, 0.85) 0%, rgba(42, 10, 10, 0.85) 50%, rgba(58, 10, 10, 0.85) 100%),
                repeating-linear-gradient(
                    0deg,
                    rgba(255,51,51,0.05) 0px,
                    transparent 1px,
                    transparent 2px,
                    rgba(255,51,51,0.05) 3px,
                    transparent 4px
                ),
                repeating-linear-gradient(
                    90deg,
                    rgba(255,51,51,0.04) 0px,
                    transparent 1px,
                    transparent 2px,
                    rgba(255,51,51,0.04) 3px,
                    transparent 4px
                ),
                repeating-linear-gradient(
                    45deg,
                    rgba(255,51,51,0.03) 0px,
                    transparent 2px,
                    transparent 4px,
                    rgba(255,51,51,0.03) 6px,
                    transparent 8px
                );
            background-blend-mode: normal, multiply, multiply, screen;
            border-radius: 24px;
            box-shadow: 
                0 0 80px rgba(0, 0, 0, 0.3),
                inset 0 0 100px rgba(0,0,0,0.2);
            border: 3px solid #3a0a0a;
            position: relative;
            transform-origin: 0 0;
            transition: transform 0.5s ease;
        }

        .board.zoomed {
            transform: scale(1);
        }

        .board::after {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 35% 65%, rgba(0, 0, 0, 0.05), transparent 50%);
            pointer-events: none;
            border-radius: 24px;
        }

        .wall-boundary {
            position: absolute;
            inset: 0;
            border: 2px dashed rgba(58, 10, 10, 0.5);
            border-radius: 18px;
            pointer-events: none;
            z-index: 5;
        }

        .svg-wrap {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 10;
        }

        /* Enhanced bulb with yellow glow */
        .bulb {
            position: absolute;
            width: var(--pin-size);
            height: var(--pin-size);
            border-radius: 50%;
            box-shadow: 
                0 0 24px 8px rgba(255,204,0,1), /* Changed to yellow */
                0 0 12px 4px rgba(255,180,0,0.8), /* Changed to yellow */
                inset 0 0 6px rgba(255,255,200,0.9), /* Changed to yellow */
                inset 0 2px 4px rgba(255,255,255,0.6);
            background: radial-gradient(circle at 30% 25%, #fff5f5, #ffcc00 40%, #ff8800 80%); /* Changed to yellow */
            transform: translate(-50%, -50%);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 15;
            filter: brightness(1.1);
            animation: bulbGlow 3s ease-in-out infinite alternate;
        }

        @keyframes bulbGlow {
            0% {
                box-shadow: 
                    0 0 24px 8px rgba(255,204,0,1), /* Changed to yellow */
                    0 0 12px 4px rgba(255,180,0,0.8); /* Changed to yellow */
                filter: brightness(1.1) hue-rotate(0deg);
            }
            25% {
                box-shadow: 
                    0 0 28px 10px rgba(255,210,0,1), /* Changed to yellow */
                    0 0 14px 6px rgba(255,220,0,0.9); /* Changed to yellow */
                filter: brightness(1.2) hue-rotate(10deg);
            }
            50% {
                box-shadow: 
                    0 0 32px 12px rgba(255,220,0,1), /* Changed to yellow */
                    0 0 16px 8px rgba(255,230,0,0.9); /* Changed to yellow */
                filter: brightness(1.3) hue-rotate(20deg);
            }
            75% {
                box-shadow: 
                    0 0 28px 10px rgba(255,210,0,1), /* Changed to yellow */
                    0 0 14px 6px rgba(255,220,0,0.9); /* Changed to yellow */
                filter: brightness(1.2) hue-rotate(10deg);
            }
            100% {
                box-shadow: 
                    0 0 24px 8px rgba(255,204,0,1), /* Changed to yellow */
                    0 0 12px 4px rgba(255,180,0,0.8); /* Changed to yellow */
                filter: brightness(1.1) hue-rotate(0deg);
            }
        }

        .bulb::before {
            content: "";
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 2.5px;
            height: 10px;
            background: linear-gradient(to top, rgba(255,204,0,0.9), rgba(255,204,0,0.3)); /* Changed to yellow */
            border-radius: 2px;
            box-shadow: 0 0 6px rgba(255,204,0,0.4); /* Changed to yellow */
        }

        .bulb:hover {
            transform: translate(-50%, -50%) scale(1.4);
            box-shadow: 
                0 0 32px 12px rgba(255,210,0,1), /* Changed to yellow */
                0 0 16px 6px rgba(255,220,0,0.9); /* Changed to yellow */
            filter: brightness(1.3);
            animation: none;
        }

        .note {
            position: absolute;
            z-index: 20;
            width: clamp(160px, 40vw, 200px);
            min-height: clamp(140px, 35vw, 160px);
            padding: 16px 14px 18px;
            background:
                linear-gradient(135deg, #fff5f5, var(--note-bg) 50%, var(--note-shadow)),
                repeating-linear-gradient(
                    135deg,
                    rgba(255, 204, 0, 0.15), /* Changed to yellow */
                    rgba(255, 204, 0, 0.15) 1px, /* Changed to yellow */
                    rgba(255, 204, 0, 0.05) 2px, /* Changed to yellow */
                    transparent 4px,
                    transparent 8px
                );
            background-blend-mode: soft-light, normal;
            border-radius: 10px;
            box-shadow: 
                3px 6px 12px rgba(0,0,0,0.25),
                0 12px 24px rgba(0,0,0,0.15),
                inset 0 1px 2px rgba(255,255,255,0.6),
                inset 0 0 20px rgba(255,255,200,0.5); /* Changed to yellow */
            color: var(--dark-yellow); /* Changed to yellow */
            font-size: clamp(13px, 3vw, 15px);
            line-height: 1.4;
            display: flex;
            flex-direction: column;
            gap: 8px;
            word-break: break-word;
            /* Notes are fixed - no pointer events for dragging */
            cursor: default;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-top: 2px solid rgba(255, 255, 255, 0.95);
            border-left: 1.5px solid rgba(255, 255, 255, 0.9);
            touch-action: manipulation;
            font-weight: 500;
            pointer-events: auto; /* Enable pointer events for notes */
        }

        .note::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                linear-gradient(90deg, rgba(255,204,0,0.05) 1px, transparent 1px), /* Changed to yellow */
                linear-gradient(0deg, rgba(255,204,0,0.05) 1px, transparent 1px); /* Changed to yellow */
            background-size: 24px 24px;
            pointer-events: none;
            opacity: 0.3;
            border-radius: 10px;
            box-shadow: inset 0 4px 8px rgba(255, 255, 255, 0.7);
        }

        .note::after {
            content: "";
            position: absolute;
            left: 50%;
            top: -12px;
            width: 22px;
            height: 22px;
            background: 
                radial-gradient(circle at 40% 35%, #fff5f5 40%, #ffcc00 70%, #ff8800 100%); /* Changed to yellow */
            border-radius: 50%;
            box-shadow:
                0 6px 12px rgba(0,0,0,0.25),
                inset 0 2px 6px rgba(255,255,255,0.8),
                inset 0 -2px 4px rgba(0,0,0,0.1);
            transform: translateX(-50%) rotate(5deg);
            z-index: 2;
            border: 1px solid rgba(255,255,200,0.8); /* Changed to yellow */
        }

        .note .thread-connector {
            position: absolute;
            top: -18px;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 18px;
            background: linear-gradient(to top, rgba(255,204,0,0.6), transparent); /* Changed to yellow */
            z-index: 1;
        }

        .note:hover {
            box-shadow: 
                0 12px 24px rgba(0,0,0,0.2),
                0 18px 36px rgba(0,0,0,0.18),
                inset 0 1px 2px rgba(255,255,255,0.7),
                0 0 0 1px rgba(0,0,0,0.08);
            transform: translateY(-4px) rotate(1deg) scale(1.02);
        }

        .note .meta {
            font-weight: 700;
            font-size: clamp(12px, 2.8vw, 14px);
            color: var(--dark-yellow); /* Changed to yellow */
            border-bottom: 1px solid rgba(204,153,0,0.3); /* Changed to yellow */
            padding-bottom: 4px;
            position: relative;
            z-index: 1;
        }

        .note .txt {
            font-size: clamp(12px, 2.8vw, 14px);
            color: var(--dark-yellow); /* Changed to yellow */
            opacity: 0.95;
            flex-grow: 1;
            position: relative;
            z-index: 1;
        }

        /* Sidebar styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 65px; /* Adjusted to account for navbar */
            bottom: 0;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, rgba(26, 10, 10, 0.95), rgba(42, 10, 10, 0.9));
            backdrop-filter: blur(12px) saturate(1.4);
            z-index: 50;
            display: flex;
            flex-direction: column;
            padding: 85px 20px 20px;
            box-shadow: 4px 0 20px rgba(0,0,0,0.4);
            border-right: 1px solid rgba(58, 10, 10, 0.5);
            overflow-y: auto;
            /* Remove left scrollbar and hide vertical scrollbar */
            overflow-x: hidden;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .sidebar::-webkit-scrollbar {
            display: none;
        }

        .sidebar-title {
            color: var(--text-yellow); /* Changed to yellow */
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
            text-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        .input-box {
            background: linear-gradient(135deg, #fff5f5, #ffeeee, #ffe5e5);
            padding: 20px;
            border-radius: 16px;
            box-shadow: 
                0 8px 28px rgba(0,0,0,0.45),
                inset 0 1px 0 rgba(255,255,255,0.6),
                0 0 0 1px solid rgba(255,204,0,0.3); /* Changed to yellow */
            display: flex;
            flex-direction: column;
            gap: 16px;
            align-items: stretch;
            margin-bottom: 30px;
            border: 2px solid rgba(255,204,0,0.3); /* Changed to yellow */
        }

        .input-box input, .input-box textarea {
            border: 2px solid rgba(255,204,0,0.2); /* Changed to yellow */
            padding: 12px 14px;
            border-radius: 10px;
            outline: none;
            font-family: inherit;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 16px;
            background: rgba(255,255,255,0.9);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
            width: 100%;
            color: var(--dark-yellow); /* Changed to yellow */
        }

        .input-box input {
            font-weight: 600;
        }

        .input-box textarea {
            resize: none;
            font-weight: 500;
            min-height: 100px;
        }

        .input-box input:focus, .input-box textarea:focus {
            border-color: var(--text-yellow); /* Changed to yellow */
            box-shadow: 
                0 0 0 3px rgba(255, 204, 0, 0.2), /* Changed to yellow */
                inset 0 2px 4px rgba(0,0,0,0.05);
            transform: scale(1.02);
        }

        /* Changed to yellow */
        .input-box button {
            background: var(--text-yellow); /* Yellow color */
            padding: 12px 16px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-weight: 700;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 16px;
            white-space: nowrap;
            box-shadow: 
                0 4px 12px rgba(0,0,0,0.3),
                inset 0 1px 0 rgba(255,255,255,0.4);
            letter-spacing: 0.3px;
            color: #fff;
        }

        .input-box button:hover {
            background: var(--light-yellow); /* Lighter yellow on hover */
            transform: translateY(-3px) scale(1.05);
            box-shadow: 
                0 8px 20px rgba(0,0,0,0.4),
                inset 0 1px 0 rgba(255,255,255,0.5);
        }

        .input-box button:active {
            transform: translateY(-1px) scale(1.02);
        }

        .sidebar-hint {
            color: var(--light-yellow); /* Changed to yellow */
            font-size: 14px;
            line-height: 1.5;
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background: rgba(255,204,0,0.1); /* Changed to yellow */
            border-radius: 10px;
            border: 1px solid rgba(255,204,0,0.2); /* Changed to yellow */
        }

        /* Mini-map styles - REDUCED SIZE and square viewport */
        .minimap-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 200px; /* Reduced from 300px */
            height: 150px; /* Reduced from 225px */
            background: rgba(26, 10, 10, 0.9);
            border-radius: 12px;
            border: 2px solid rgba(255,204,0,0.3); /* Changed to yellow */
            box-shadow: 0 8px 32px rgba(0,0,0,0.6);
            z-index: 100;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .minimap-container:hover {
            transform: scale(1.05);
            border-color: rgba(255,204,0,0.5); /* Changed to yellow */
        }

        .minimap-container.expanded {
            width: 90%;
            height: 80%;
            bottom: 50%;
            right: 50%;
            transform: translate(50%, 50%);
        }

        .minimap {
            width: 100%;
            height: 100%;
            position: relative;
            background: rgba(42, 10, 10, 0.7);
            border-radius: 10px;
            overflow: hidden;
        }

        .minimap-board {
            width: 100%;
            height: 100%;
            position: relative;
            transform: scale(var(--minimap-scale));
            transform-origin: 0 0;
        }

        .minimap-note {
            position: absolute;
            width: 14px; /* Reduced from 20px */
            height: 12px; /* Reduced from 16px */
            background: #ffeeee;
            border-radius: 3px;
            transform: translate(-50%, -50%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.8);
            z-index: 10;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .minimap-note:hover {
            background: #ffe5e5;
            transform: translate(-50%, -50%) scale(1.2);
        }

        .minimap-bulb {
            position: absolute;
            width: 3px; /* Reduced from 4px */
            height: 3px; /* Reduced from 4px */
            background: var(--text-yellow); /* Changed to yellow */
            border-radius: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 0 6px var(--text-yellow); /* Changed to yellow */
            z-index: 5;
        }

        /* Changed to square viewport */
        .minimap-viewport {
            position: absolute;
            border: 2px solid rgba(255,204,0,0.8); /* Changed to yellow */
            background: rgba(255,204,0,0.2); /* Changed to yellow */
            z-index: 15;
            pointer-events: none;
            width: 80px; /* Square dimensions */
            height: 80px; /* Square dimensions */
        }

        .minimap-title {
            position: absolute;
            top: 6px; /* Adjusted for smaller container */
            left: 6px; /* Adjusted for smaller container */
            color: var(--text-yellow); /* Changed to yellow */
            font-weight: 700;
            font-size: 11px; /* Reduced from 14px */
            text-shadow: 0 1px 3px rgba(0,0,0,0.7);
            z-index: 20;
        }
        
        /* Added close button for mini-map */
        .minimap-close {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 20px;
            height: 20px;
            background: rgba(255,204,0,0.8); /* Changed to yellow */
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            cursor: pointer;
            z-index: 25;
            transition: all 0.2s ease;
        }
        
        .minimap-close:hover {
            background: rgba(255,204,0,1); /* Changed to yellow */
            transform: scale(1.1);
        }
        
        .minimap-container.expanded .minimap-close {
            display: flex;
        }

        .zoom-controls {
            position: fixed;
            bottom: 20px;
            left: calc(var(--sidebar-width) + 20px);
            display: flex;
            gap: 10px;
            z-index: 80;
        }

        /* Reduced button size */
        .zoom-btn {
            background: linear-gradient(135deg, #ffcc00, #ffaa00, #ff8800); /* Changed to yellow */
            border: none;
            width: 40px; /* Reduced from 50px */
            height: 40px; /* Reduced from 50px */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px; /* Reduced from 24px */
            font-weight: bold;
            color: #fff;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
            transition: all 0.3s ease;
        }

        .zoom-btn:hover {
            transform: scale(1.1);
        }

        .notification {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(135deg, #fff5f5, #ffeeee, #ffe5e5);
            padding: 18px 24px;
            border-radius: 14px;
            box-shadow: 
                0 12px 40px rgba(0,0,0,0.5),
                inset 0 1px 0 rgba(255,255,255,0.6);
            z-index: 100;
            text-align: center;
            max-width: 320px;
            width: calc(100% - 40px);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            font-weight: 600;
            color: var(--dark-yellow); /* Changed to yellow */
            border: 2px solid rgba(255,204,0,0.4); /* Changed to yellow */
        }

        .notification.show {
            opacity: 1;
        }

        .thread-anchor {
            position: absolute;
            width: 6px;
            height: 6px;
            background: rgba(255,204,0,0.8); /* Changed to yellow */
            border-radius: 50%;
            transform: translate(-50%, -50%);
            z-index: 16;
            box-shadow: 0 0 8px rgba(255,204,0,0.6); /* Changed to yellow */
        }

        /* Mobile menu button */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 100;
            background: linear-gradient(135deg, #ffcc00, #ffaa00, #ff8800); /* Changed to yellow */
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        }

        .mobile-menu-btn span {
            display: block;
            width: 20px;
            height: 2px;
            background: #fff;
            border-radius: 1px;
            transition: all 0.3s ease;
        }

        .mobile-menu-btn.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .mobile-menu-btn.active span:nth-child(2) {
            opacity: 0;
        }

        .mobile-menu-btn.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        /* Improved mobile responsiveness */
        @media (max-width: 768px) {
            :root {
                --sidebar-width: 100%;
            }
            
            .navbar {
                padding: 8px 0;
            }
            
            .nav-menu {
                position: fixed;
                top: 0;
                right: -100%;
                width: 80%;
                height: 100vh;
                background: rgba(0, 0, 0, 0.95);
                backdrop-filter: blur(20px);
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: 30px;
                transition: right 0.3s ease;
                border-left: 2px solid var(--neon-yellow); /* Changed to yellow */
            }
            
            .nav-menu.active {
                right: 0;
            }
            
            .hamburger {
                display: flex;
            }
            
            .nav-link {
                font-size: 1.1rem;
                padding: 12px 20px;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
                bottom: 0;
                top: auto;
                padding: 12px;
                max-height: 35vh;
                overflow-y: auto;
                border-right: none;
                border-top: 1px solid rgba(58, 10, 10, 0.5);
                box-shadow: 0 -4px 20px rgba(0,0,0,0.4);
            }
            
            .board-wrap {
                margin-left: 0;
                margin-top: 130px;
                width: 100%;
                height: calc(65vh - 65px);
            }
            
            .zoom-controls {
                left: 20px;
                bottom: calc(35vh + 10px);
            }
            
            .minimap-container {
                display: none; /* Hide mini-map on mobile */
            }
            
            .controls {
                right: 10px;
                top: 140px;
                flex-direction: row;
                gap: 6px;
            }
            
            .ctrl-btn {
                padding: 6px 10px;
                font-size: 11px;
            }
            
            header {
                flex-direction: column;
                height: auto;
                padding: 8px 10px;
                gap: 8px;
                min-height: 65px;
            }
            
            .header-title {
                font-size: 14px;
                order: 1;
                padding: 0;
                line-height: 1.3;
                margin-top: 5px;
            }
            
            .mobile-menu-btn {
                display: flex;
                position: absolute;
                top: 12px;
                right: 10px;
            }
            
            .input-box {
                padding: 12px;
                margin-bottom: 10px;
            }
            
            .input-box input, .input-box textarea {
                padding: 8px 10px;
                font-size: 14px;
            }
            
            .input-box textarea {
                min-height: 60px;
            }
            
            .input-box button {
                padding: 8px 12px;
                font-size: 14px;
            }
            
            .sidebar-hint {
                font-size: 11px;
                padding: 8px;
                margin-top: 8px;
                line-height: 1.4;
            }
            
            .sidebar-title {
                font-size: 16px;
                margin-bottom: 12px;
            }
            
            /* Ensure notes are visible on mobile */
            .note {
                width: 140px;
                min-height: 120px;
                padding: 12px 10px 14px;
                font-size: 12px;
            }
            
            .note .meta {
                font-size: 12px;
            }
            
            .note .txt {
                font-size: 11px;
            }
            
            /* Square viewport on mobile */
            .minimap-viewport {
                width: 60px;
                height: 60px;
            }
            
            /* Enhanced game button for mobile */
            .game-btn {
                padding: 8px 16px;
                font-size: 12px;
            }
            
            .game-btn-icon {
                width: 16px;
                height: 16px;
            }
        }

        @media (max-width: 480px) {
            .zoom-btn {
                width: 35px;
                height: 35px;
                font-size: 16px;
            }
            
            .note {
                width: 130px;
                min-height: 110px;
                padding: 10px 8px 12px;
            }
            
            .sidebar {
                max-height: 35vh;
            }
            
            .board-wrap {
                height: calc(65vh - 65px);
            }
            
            .zoom-controls {
                bottom: calc(35vh + 10px);
            }
            
            .input-box {
                padding: 10px;
            }
            
            .input-box input, .input-box textarea {
                padding: 6px 8px;
                font-size: 13px;
            }
            
            .input-box textarea {
                min-height: 50px;
            }
            
            .nav-link {
                font-size: 12px;
                padding: 5px 8px;
            }
            
            .controls {
                gap: 4px;
            }
            
            .ctrl-btn {
                padding: 5px 8px;
                font-size: 10px;
            }
            
            .header-title {
                font-size: 13px;
                padding: 0 40px 0 0;
            }
            
            /* Enhanced game button for smaller mobile */
            .game-btn {
                padding: 6px 12px;
                font-size: 11px;
            }
            
            .game-btn-icon {
                width: 14px;
                height: 14px;
            }
        }

        .note, .ctrl-btn {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        /* Game page styles */
        .game-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                /* Red technology background for game page */
                linear-gradient(135deg, rgba(26, 10, 10, 0.85) 0%, rgba(42, 10, 10, 0.85) 50%, rgba(58, 10, 10, 0.85) 100%),
                repeating-linear-gradient(
                    0deg,
                    rgba(255,51,51,0.05) 0px,
                    transparent 1px,
                    transparent 2px,
                    rgba(255,51,51,0.05) 3px,
                    transparent 4px
                ),
                repeating-linear-gradient(
                    90deg,
                    rgba(255,51,51,0.04) 0px,
                    transparent 1px,
                    transparent 2px,
                    rgba(255,51,51,0.04) 3px,
                    transparent 4px
                ),
                repeating-linear-gradient(
                    45deg,
                    rgba(255,51,51,0.03) 0px,
                    transparent 2px,
                    transparent 4px,
                    rgba(255,51,51,0.03) 6px,
                    transparent 8px
                );
            background-blend-mode: normal, multiply, multiply, screen;
            color: var(--text-yellow); /* Changed to yellow */
            overflow-y: auto;
            z-index: 1000;
        }
        
        .game-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1001;
            background: linear-gradient(120deg, rgba(255,204,0,0.2), rgba(255,180,0,0.15), rgba(255,150,0,0.18)); /* Changed to yellow */
            backdrop-filter: blur(12px) saturate(1.4);
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 10px 15px;
            font-weight: 800;
            color: var(--text-yellow); /* Changed to yellow */
            text-shadow: 0 3px 16px rgba(0,0,0,0.7), 0 1px 3px rgba(255,204,0,0.4); /* Changed to yellow */
            box-shadow: 
                0 4px 24px rgba(0,0,0,0.6),
                inset 0 1px 0 rgba(255,255,255,0.1);
            user-select: none;
            border-bottom: 1px solid rgba(255,204,0,0.3); /* Changed to yellow */
            letter-spacing: 0.5px;
            height: 65px;
        }
        
        .game-title {
            font-size: 16px;
            text-align: center;
            flex: 1;
            line-height: 1.2;
            padding: 0 10px;
        }
        
        .back-btn {
            background: linear-gradient(135deg, #ffcc00, #ffaa00, #ff8800); /* Changed to yellow */
            border: none;
            padding: 8px 14px;
            border-radius: 10px;
            box-shadow: 
                0 6px 16px rgba(0,0,0,0.5),
                inset 0 1px 0 rgba(255,255,255,0.4),
                inset 0 -1px 0 rgba(0,0,0,0.1);
            cursor: pointer;
            font-weight: 700;
            color: #fff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 13px;
            letter-spacing: 0.3px;
        }
        
        .back-btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 
                0 10px 24px rgba(0,0,0,0.6),
                inset 0 1px 0 rgba(255,255,255,0.5);
        }
        
        .back-btn:active {
            transform: translateY(-1px) scale(1.02);
        }
        
        .game-content {
            padding: 85px 20px 20px;
            max-width: 900px;
            margin: 0 auto;
        }
        
        /* Quiz styles from the second HTML */
        .quiz-container {
            background: rgba(26, 10, 10, 0.6);
            backdrop-filter: blur(5px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            position: relative;
            margin-bottom: 30px;
        }
        
        .quiz-container::before {
            content: "";
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            border: 1px solid rgba(255, 204, 0, 0.3); /* Changed to yellow */
            border-radius: 10px;
            pointer-events: none;
        }
        
        .question {
            margin-bottom: 25px;
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .question::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: var(--text-yellow); /* Changed to yellow */
        }
        
        .question:hover {
            background: rgba(0, 0, 0, 0.3);
            transform: translateY(-3px);
        }
        
        .question-text {
            font-size: 1.3rem;
            margin-bottom: 15px;
            font-weight: 600;
            color: var(--text-yellow); /* Changed to yellow */
        }
        
        .options {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .option {
            background: rgba(139, 19, 19, 0.5);
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            flex: 1;
            min-width: 120px;
            text-align: center;
            border: 1px solid rgba(255, 204, 0, 0.3); /* Changed to yellow */
            position: relative;
            overflow: hidden;
            color: var(--light-yellow); /* Changed to yellow */
        }
        
        .option::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 204, 0, 0.2), transparent); /* Changed to yellow */
            transition: left 0.5s;
        }
        
        .option:hover::before {
            left: 100%;
        }
        
        .option:hover {
            background: rgba(139, 19, 19, 0.7);
            transform: scale(1.05);
            box-shadow: 0 0 10px rgba(255, 204, 0, 0.3); /* Changed to yellow */
        }
        
        .image-container {
            margin-top: 20px;
            text-align: center;
            display: none;
        }
        
        .result-image {
            max-width: 100%;
            max-height: 300px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1002;
            padding: 20px;
        }
        
        .popup-content {
            background: rgba(26, 10, 10, 0.95);
            color: var(--text-yellow); /* Changed to yellow */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            text-align: center;
            animation: pop 0.5s ease;
            border: 2px solid var(--text-yellow); /* Changed to yellow */
            max-width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }
        
        .wrong-popup .popup-content {
            max-width: 400px;
        }
        
        .correct-popup .popup-content {
            max-width: 600px;
            width: 100%;
        }
        
        .correct-popup .popup-content::before {
            content: "";
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><path d="M0,0 L100,0 L100,100 L0,100 Z" fill="none" stroke="rgba(255,204,0,0.5)" stroke-width="1" stroke-dasharray="5,5"/></svg>'); /* Changed to yellow */
            background-size: 100px 100px;
            z-index: -1;
        }
        
        .popup h3 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: var(--text-yellow); /* Changed to yellow */
        }
        
        .popup p {
            font-size: 1.2rem;
            margin-bottom: 20px;
            font-style: italic;
        }
        
        .image-frame {
            position: relative;
            margin: 20px auto;
            width: 100%;
            max-width: 500px;
        }
        
        .image-frame::before {
            content: "";
            position: absolute;
            top: -15px;
            left: -15px;
            right: -15px;
            bottom: -15px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><rect width="20" height="20" fill="none" stroke="%23ffcc00" stroke-width="1"/></svg>'); /* Changed to yellow */
            background-size: 20px 20px;
            z-index: -1;
        }
        
        .popup img {
            width: 100%;
            max-height: 50vh;
            object-fit: cover;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            filter: sepia(20%);
            transition: filter 0.5s;
        }
        
        .popup img:hover {
            filter: sepia(0%);
        }
        
        .memory-caption {
            margin-top: 15px;
            font-size: 1rem;
            color: var(--light-yellow); /* Changed to yellow */
            font-style: italic;
        }
        
        @keyframes pop {
            0% { transform: scale(0.5); opacity: 0; }
            70% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .close-popup {
            margin-top: 20px;
            padding: 12px 24px;
            background: rgba(255, 204, 0, 0.3); /* Changed to yellow */
            border: 1px solid var(--text-yellow); /* Changed to yellow */
            border-radius: 5px;
            color: var(--text-yellow); /* Changed to yellow */
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
        }
        
        .close-popup:hover {
            background: rgba(255, 204, 0, 0.5); /* Changed to yellow */
        }
        
        .progress {
            margin-top: 20px;
            text-align: center;
            font-size: 1.1rem;
            padding: 15px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            position: relative;
            color: var(--text-yellow); /* Changed to yellow */
        }
        
        .progress::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: calc(var(--progress) * 10%);
            background: rgba(255, 204, 0, 0.3); /* Changed to yellow */
            border-radius: 10px;
            z-index: -1;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 15px;
            font-size: 0.9rem;
            opacity: 0.8;
            font-style: italic;
            color: var(--light-yellow); /* Changed to yellow */
        }
        
        .completion-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(26, 10, 10, 0.95);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1003;
            padding: 20px;
        }
        
        .completion-content {
            text-align: center;
            max-width: 600px;
            padding: 30px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            border: 1px solid rgba(255, 204, 0, 0.3); /* Changed to yellow */
        }
        
        .completion-content h2 {
            font-size: 2.5rem;
            color: var(--text-yellow); /* Changed to yellow */
            margin-bottom: 20px;
        }
        
        .completion-content p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            line-height: 1.6;
            color: var(--light-yellow); /* Changed to yellow */
        }
        
        .restart-btn {
            padding: 12px 24px;
            background: rgba(255, 204, 0, 0.3); /* Changed to yellow */
            border: 1px solid var(--text-yellow); /* Changed to yellow */
            border-radius: 5px;
            color: var(--text-yellow); /* Changed to yellow */
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .restart-btn:hover {
            background: rgba(255, 204, 0, 0.5); /* Changed to yellow */
        }
        
        .memory-badge {
            display: inline-block;
            background: rgba(255, 204, 0, 0.2); /* Changed to yellow */
            border: 1px solid var(--text-yellow); /* Changed to yellow */
            border-radius: 50%;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            margin: 0 5px;
            font-weight: bold;
            color: var(--text-yellow); /* Changed to yellow */
        }
        
        /* Mobile Responsive Styles for Game */
        @media (max-width: 768px) {
            .game-content {
                padding: 75px 10px 10px;
            }
            
            .quiz-container {
                padding: 20px;
            }
            
            .question-text {
                font-size: 1.1rem;
            }
            
            .options {
                flex-direction: column;
            }
            
            .option {
                width: 100%;
                padding: 15px;
                font-size: 1rem;
            }
            
            .popup-content {
                padding: 20px;
                margin: 10px;
            }
            
            .popup h3 {
                font-size: 1.5rem;
            }
            
            .popup p {
                font-size: 1rem;
            }
            
            .popup img {
                max-height: 40vh;
            }
            
            .close-popup {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
            
            .progress {
                font-size: 1rem;
                padding: 10px;
            }
            
            .memory-badge {
                width: 30px;
                height: 30px;
                line-height: 30px;
                font-size: 0.9rem;
                margin: 0 3px;
            }
            
            .completion-content h2 {
                font-size: 2rem;
            }
            
            .completion-content p {
                font-size: 1rem;
            }
            
            .restart-btn {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 480px) {
            .question-text {
                font-size: 1rem;
            }
            
            .option {
                padding: 12px;
                font-size: 0.9rem;
            }
            
            .popup h3 {
                font-size: 1.3rem;
            }
            
            .popup p {
                font-size: 0.9rem;
            }
            
            .memory-caption {
                font-size: 0.9rem;
            }
            
            .completion-content h2 {
                font-size: 1.8rem;
            }
            
            .completion-content p {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Loading overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <img src="assets/images/logo.png" alt="PSG Tech Logo" class="logo-img">
            </div>
        <ul class="nav-menu">
            <!-- UPDATED LINKS -->
            <li class="nav-item"><a href="index.html#home" class="nav-link">Home</a></li>
            <li class="nav-item"><a href="about.html" class="nav-link">About</a></li>
            <li class="nav-item"><a href="index.html#reboot-explanation" class="nav-link">REBOOT 40</a></li>
            <li class="nav-item"><a href="memories.php" class="nav-link active">Memories</a></li>
            <li class="nav-item"><a href="gallery.html" class="nav-link">Gallery</a></li>
            <li class="nav-item"><a href="index.html#schedule" class="nav-link">Schedule</a></li>
            <li class="nav-item"><a href="index.html#contact" class="nav-link">Contact</a></li>
            <li class="nav-item"><a href="register.php" class="nav-link btn">Register</a></li>
        </ul>
            <button class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <div class="wall-texture"></div>
    <div class="wall-imperfections"></div>
    
    <!-- Updated header with integrated navigation -->
    <header>
        <div class="header-title">Memory Wall - Create and Explore Your Sticky Notes</div>
        <button class="game-btn" id="gameLink">
            <span class="game-btn-icon"></span>
            Game
        </button>
    </header>

    <div class="sidebar">
        <div class="sidebar-title">Add Your Memory</div>
        <div class="input-box" id="inputBox">
            <input id="name" placeholder="Your name" />
            <textarea id="message" placeholder="Write a short memory" rows="3"></textarea>
            <button id="addBtn">Add Note</button>
        </div>
        <div class="sidebar-hint">
            <strong>Navigation Tips:</strong><br>
            • Use the mini-map for overview<br>
            • Click on mini-map to zoom to area<br>
            • Notes are fixed and cannot be moved<br>
            • Use zoom controls to adjust view
        </div>
    </div>

    <div class="zoom-controls">
        <button class="zoom-btn" id="zoomInBtn">+</button>
        <button class="zoom-btn" id="zoomOutBtn">-</button>
        <button class="zoom-btn" id="zoomResetBtn">⟲</button>
    </div>

    <div class="minimap-container" id="minimapContainer">
        <div class="minimap-title">Mini-map - Click to Zoom</div>
        <div class="minimap-close" id="minimapClose">×</div>
        <div class="minimap" id="minimap">
            <div class="minimap-board" id="minimapBoard"></div>
            <div class="minimap-viewport" id="minimapViewport"></div>
        </div>
    </div>

    <div class="board-wrap" id="wrap">
        <div class="board" id="board">
            <div class="wall-boundary"></div>
            <div class="svg-wrap" id="svgWrap">
                <svg id="wires" viewBox="0 0 3200 2400" preserveAspectRatio="none" width="3200" height="2400"></svg>
            </div>
            <div id="notesLayer" style="position:absolute; inset:0; z-index:30; pointer-events:auto;"></div>
        </div>
    </div>

    <div class="notification" id="notification"></div>

    <!-- Game Container -->
    <div class="game-container" id="gameContainer">
        <div class="game-header">
            <button class="back-btn" id="backBtn">Back to Memory Wall</button>
            <div class="game-title">PSG Tech - Memory Lane Quiz</div>
            <div></div>
        </div>
        
        <div class="game-content">
            <div class="quiz-container">
                <div class="question" id="question1">
                    <div class="question-text">1. Which iconic bridge connects different blocks of PSG Tech campus?</div>
                    <div class="options">
                        <div class="option" data-correct="true">Skywalk</div>
                        <div class="option" data-correct="false">Golden Gate</div>
                        <div class="option" data-correct="false">Tech Bridge</div>
                        <div class="option" data-correct="false">PSG Link</div>
                    </div>
                    <div class="image-container">
                        <img src="https://picsum.photos/seed/skywalk/500/300.jpg" alt="PSG Tech Campus">
                    </div>
                </div>
                
                <div class="question" id="question2">
                    <div class="question-text">2. Which is the central landmark with water features in front of the main building?</div>
                    <div class="options">
                        <div class="option" data-correct="false">PSG Pond</div>
                        <div class="option" data-correct="true">Fountain</div>
                        <div class="option" data-correct="false">Water Garden</div>
                        <div class="option" data-correct="false">Tech Lake</div>
                    </div>
                    <div class="image-container">
                        <img src="https://picsum.photos/seed/fountain/500/300.jpg" alt="Fountain" class="result-image">
                    </div>
                </div>
                
                <div class="question" id="question3">
                    <div class="question-text">3. Which building houses a vast collection of books and journals at PSG Tech?</div>
                    <div class="options">
                        <div class="option" data-correct="false">Knowledge Center</div>
                        <div class="option" data-correct="false">Study Hall</div>
                        <div class="option" data-correct="true">Library</div>
                        <div class="option" data-correct="false">Book Tower</div>
                    </div>
                    <div class="image-container">
                        <img src="https://picsum.photos/seed/library/500/300.jpg" alt="Library" class="result-image">
                    </div>
                </div>
                
                <div class="question" id="question4">
                    <div class="question-text">4. What is the name of the main auditorium in PSG Tech?</div>
                    <div class="options">
                        <div class="option" data-correct="false">Tech Auditorium</div>
                        <div class="option" data-correct="false">PSG Hall</div>
                        <div class="option" data-correct="true">IMS Auditorium</div>
                        <div class="option" data-correct="false">Main Stage</div>
                    </div>
                    <div class="image-container">
                        <img src="https://picsum.photos/seed/auditorium/500/300.jpg" alt="Auditorium" class="result-image">
                    </div>
                </div>
                
                <div class="question" id="question5">
                    <div class="question-text">5. Which department is known for its association with the textile industry?</div>
                    <div class="options">
                        <div class="option" data-correct="false">Computer Science</div>
                        <div class="option" data-correct="true">Textile Technology</div>
                        <div class="option" data-correct="false">Mechanical Engineering</div>
                        <div class="option" data-correct="false">Electronics</div>
                    </div>
                    <div class="image-container">
                        <img src="https://picsum.photos/seed/textile/500/300.jpg" alt="Textile Technology" class="result-image">
                    </div>
                </div>
                
                <div class="question" id="question6">
                    <div class="question-text">6. What is the name of the annual technical symposium of PSG Tech?</div>
                    <div class="options">
                        <div class="option" data-correct="false">Tech Fest</div>
                        <div class="option" data-correct="false">PSG Expo</div>
                        <div class="option" data-correct="true">Pragyan</div>
                        <div class="option" data-correct="false">Innovation Summit</div>
                    </div>
                    <div class="image-container">
                        <img src="https://picsum.photos/seed/pragyan/500/300.jpg" alt="Pragyan" class="result-image">
                    </div>
                </div>
                
                <div class="question" id="question7">
                    <div class="question-text">7. Which sports facility is prominent at PSG Tech?</div>
                    <div class="options">
                        <div class="option" data-correct="false">Swimming Pool</div>
                        <div class="option" data-correct="false">Tennis Court</div>
                        <div class="option" data-correct="false">Cricket Ground</div>
                        <div class="option" data-correct="true">PSG Tech Stadium</div>
                    </div>
                    <div class="image-container">
                        <img src="https://picsum.photos/seed/stadium/500/300.jpg" alt="Stadium" class="result-image">
                    </div>
                </div>
                
                <div class="question" id="question8">
                    <div class="question-text">8. What is the name of the student activity center?</div>
                    <div class="options">
                        <div class="option" data-correct="false">Student Hub</div>
                        <div class="option" data-correct="true">PSG STEP</div>
                        <div class="option" data-correct="false">Activity Block</div>
                        <div class="option" data-correct="false">Youth Center</div>
                    </div>
                    <div class="image-container">
                        <img src="https://picsum.photos/seed/studentcenter/500/300.jpg" alt="Student Center" class="result-image">
                    </div>
                </div>
                
                <div class="question" id="question9">
                    <div class="question-text">9. Which is the oldest block in PSG Tech?</div>
                    <div class="options">
                        <div class="option" data-correct="true">Main Building</div>
                        <div class="option" data-correct="false">New Block</div>
                        <div class="option" data-correct="false">Tech Tower</div>
                        <div class="option" data-correct="false">Administration Block</div>
                    </div>
                    <div class="image-container">
                        <img src="https://picsum.photos/seed/mainbuilding/500/300.jpg" alt="Main Building" class="result-image">
                    </div>
                </div>
                
                <div class="question" id="question10">
                    <div class="question-text">10. What is the name of the PSG Tech alumni association?</div>
                    <div class="options">
                        <div class="option" data-correct="false">PSG Alumni</div>
                        <div class="option" data-correct="false">Tech Graduates</div>
                        <div class="option" data-correct="true">PSG Tech Alumni Association</div>
                        <div class="option" data-correct="false">PSG Former Students</div>
                    </div>
                    <div class="image-container">
                        <img src="https://picsum.photos/seed/alumni/500/300.jpg" alt="Alumni" class="result-image">
                    </div>
                </div>
                
                <div class="progress" style="--progress: 0">
                    Score: <span id="score">0</span>/10
                    <div style="margin-top: 10px;">
                        Memories Unlocked: 
                        <span id="memoryBadges"></span>
                    </div>
                </div>
            </div>
            
            <div class="footer">
                <p>"Every corner of PSG Tech holds a story, every path a memory..."</p>
            </div>
        </div>
    </div>
    
    <div class="popup wrong-popup" id="wrongPopup">
        <div class="popup-content">
            <h3>Not quite right...</h3>
            <p>Try again. The memory is still there, waiting to be unlocked!</p>
            <button class="close-popup">Close</button>
        </div>
    </div>
    
    <div class="popup correct-popup" id="correctPopup">
        <div class="popup-content">
            <h3>Memory Unlocked!</h3>
            <p>Relive the moments spent in this cherished place...</p>
            <div class="image-frame">
                <img id="correctImage" src="" alt="Correct answer image">
            </div>
            <p class="memory-caption" id="memoryCaption"></p>
            <button class="close-popup">Continue Journey</button>
        </div>
    </div>
    
    <div class="completion-screen" id="completionScreen">
        <div class="completion-content">
            <h2>Journey Complete!</h2>
            <p>You've unlocked <span id="finalScore">0</span> memories from your time at PSG Tech. Each place holds a special story, a moment frozen in time. These memories will forever remain etched in your heart, connecting you to the institution that shaped your future.</p>
            <p>Thank you for walking down memory lane with us!</p>
            <button class="restart-btn" id="restartBtn">Begin New Journey</button>
        </div>
    </div>

    <script>
        // Config & defaults
        const BOARD_W = 3200, BOARD_H = 2400;
        const THREADS = 12; // Increased to create more rows
        const NOTES_PER_THREAD = 8; // Increased to create more columns
        const NOTES_LAYER = document.getElementById('notesLayer');
        const SVG = document.getElementById('wires');
        const BOARD = document.getElementById('board');
        const WRAP = document.getElementById('wrap');
        const NOTIFICATION = document.getElementById('notification');
        const MINIMAP_BOARD = document.getElementById('minimapBoard');
        const MINIMAP_VIEWPORT = document.getElementById('minimapViewport');
        const MINIMAP_CONTAINER = document.getElementById('minimapContainer');
        const MINIMAP_CLOSE = document.getElementById('minimapClose');
        const GAME_CONTAINER = document.getElementById('gameContainer');
        const GAME_LINK = document.getElementById('gameLink');
        const BACK_BTN = document.getElementById('backBtn');
        const HAMBURGER = document.getElementById('hamburger');
        const NAV_MENU = document.querySelector('.nav-menu');

        let notes = [];
        let newNoteData = null;
        let isAddingNote = false;
        let zoomLevel = 1;
        let isMinimapExpanded = false;

        // Show/hide loading overlay
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }
        
        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        // FIX #1: Define the missing showNotification function
        function showNotification(message) {
            NOTIFICATION.textContent = message;
            NOTIFICATION.classList.add('show');
            
            setTimeout(() => {
                NOTIFICATION.classList.remove('show');
            }, 3000);
        }

        // FIX #2: Correct the fetchNotes function to handle the API response properly
        function fetchNotes() {
            showLoading();
            
            // Explicitly ask for the 'get_notes' action
            fetch('memories_api.php?action=get_notes')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Check for success and the 'data' property in the response
                if (data.success && data.data) {
                    renderNotes(data.data);
                } else {
                    console.error('API returned an error:', data.error);
                    showNotification('Failed to load notes: ' + (data.error || 'Unknown error'));
                }
                hideLoading();
            })
            .catch(error => {
                console.error('Error fetching notes:', error);
                showNotification('Failed to load notes. Check console for details.');
                hideLoading();
            });
        }

        // Render notes on the board
        function renderNotes(notesData) {
            NOTES_LAYER.innerHTML = '';
            notes = [];
            
            notesData.forEach(noteData => {
                const note = createNoteFromData(noteData);
                notes.push(note);
            });
            
            // Update mini-map after rendering notes
            createMinimapNotes();
        }

        // FIX #3: Correct createNoteFromData to use the right property names from the API
        function createNoteFromData(data) {
            const el = document.createElement('div');
            el.className = 'note';
            el.dataset.id = data.id;

            const randomRotation = (Math.random() * 8 - 4).toFixed(2);
            el.style.transform = `translate(-50%, -50%) rotate(${randomRotation}deg)`;
            // API returns 'x_position' and 'y_position', not 'position_x'
            el.style.left = data.x_position + 'px';
            el.style.top = data.y_position + 'px';

            el.innerHTML = `
                <div class="thread-connector"></div>
                <div class="meta">${data.name}</div>
                <div class="txt">${data.message}</div>
            `;

            NOTES_LAYER.appendChild(el);

            const obj = { 
                el, 
                id: data.id,
                name: data.name, 
                text: data.message, 
                thread: data.thread, // API returns 'thread'
                manual: data.manual 
            };

            return obj;
        }

        function updateMinimapViewport() {
            if (!MINIMAP_VIEWPORT) return;
            
            const scrollLeft = WRAP.scrollLeft;
            const scrollTop = WRAP.scrollTop;
            const viewportWidth = WRAP.clientWidth;
            const viewportHeight = WRAP.clientHeight;
            
            const scale = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--minimap-scale'));
            
            // Center the square viewport
            MINIMAP_VIEWPORT.style.left = (scrollLeft * scale) + 'px';
            MINIMAP_VIEWPORT.style.top = (scrollTop * scale) + 'px';
            
            // Keep the viewport square
            const squareSize = Math.min(viewportWidth, viewportHeight) * scale;
            MINIMAP_VIEWPORT.style.width = squareSize + 'px';
            MINIMAP_VIEWPORT.style.height = squareSize + 'px';
        }

        function createMinimapNotes() {
            // Don't create mini-map notes on mobile
            if (window.innerWidth <= 768) return;
            
            MINIMAP_BOARD.innerHTML = '';
            
            // Add mini-map bulbs
            document.querySelectorAll('.bulb').forEach(bulb => {
                const miniBulb = document.createElement('div');
                miniBulb.className = 'minimap-bulb';
                miniBulb.style.left = bulb.style.left;
                miniBulb.style.top = bulb.style.top;
                MINIMAP_BOARD.appendChild(miniBulb);
            });
            
            // Add mini-map notes
            notes.forEach(note => {
                const miniNote = document.createElement('div');
                miniNote.className = 'minimap-note';
                miniNote.style.left = note.el.style.left;
                miniNote.style.top = note.el.style.top;
                MINIMAP_BOARD.appendChild(miniNote);
                
                // Click on mini-map note to zoom to it
                miniNote.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const x = parseInt(note.el.style.left) - (window.innerWidth / 2);
                    const y = parseInt(note.el.style.top) - (window.innerHeight / 2);
                    
                    WRAP.scrollTo({
                        left: x,
                        top: y,
                        behavior: 'smooth'
                    });
                    
                    showNotification(`Zoomed to ${note.name}'s note`);
                });
            });
        }

        function zoomToArea(x, y) {
            // Don't allow mini-map zoom on mobile
            if (window.innerWidth <= 768) return;
            
            // Convert mini-map coordinates to main board coordinates
            const scale = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--minimap-scale'));
            const boardX = x / scale;
            const boardY = y / scale;
            
            // Center the view on this position
            const targetX = boardX - (WRAP.clientWidth / 2);
            const targetY = boardY - (WRAP.clientHeight / 2);
            
            WRAP.scrollTo({
                left: targetX,
                top: targetY,
                behavior: 'smooth'
            });
            
            showNotification('Zoomed to selected area');
        }

        function zoomIn() {
            if (zoomLevel < 2) {
                zoomLevel += 0.25;
                updateZoom();
            }
        }

        function zoomOut() {
            if (zoomLevel > 0.5) {
                zoomLevel -= 0.25;
                updateZoom();
            }
        }

        function resetZoom() {
            zoomLevel = 1;
            updateZoom();
        }

        function updateZoom() {
            BOARD.style.transform = `scale(${zoomLevel})`;
            WRAP.style.transform = 'scale(1)';
            showNotification(`Zoom: ${Math.round(zoomLevel * 100)}%`);
            updateMinimapViewport();
        }

        function makeThreads() {
            SVG.innerHTML = '';
            Array.from(document.querySelectorAll('.thread-anchor')).forEach(a => a.remove());

            const gap = BOARD_H / (THREADS + 1);
            for (let t = 0; t < THREADS; t++) {
                const y = gap * (t + 1);
                const freq = 0.0008 + Math.random() * 0.0012;
                const amp = 45 + Math.random() * 35;
                const smooth = 240 + Math.random() * 140;

                const p = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                p.setAttribute('id', 'thread-' + t);
                p.setAttribute('stroke', 'rgba(204, 153, 0, 0.4)'); // Changed to yellow
                p.setAttribute('stroke-width', '4.5');
                p.setAttribute('fill', 'none');
                p.setAttribute('stroke-linecap', 'round');
                p.dataset.amp = amp;
                p.dataset.freq = freq;
                p.dataset.y = y;
                p.dataset.smooth = smooth;
                SVG.appendChild(p);

                const leftAnchor = document.createElement('div');
                leftAnchor.className = 'thread-anchor';
                leftAnchor.style.left = '0px';
                leftAnchor.style.top = y + 'px';
                BOARD.appendChild(leftAnchor);

                const rightAnchor = document.createElement('div');
                rightAnchor.className = 'thread-anchor';
                rightAnchor.style.left = BOARD_W + 'px';
                rightAnchor.style.top = y + 'px';
                BOARD.appendChild(rightAnchor);

                const bulbCount = 14 + Math.floor(Math.random() * 10);
                for (let b = 0; b < bulbCount; b++) {
                    const bulb = document.createElement('div');
                    bulb.className = 'bulb';
                    bulb.dataset.thread = t;
                    bulb.dataset.index = b;
                    BOARD.appendChild(bulb);
                }
            }
        }

        function updateThreadsAndBulbs() {
            // Update thread paths
            document.querySelectorAll('svg path').forEach(path => {
                const amp = parseFloat(path.dataset.amp);
                const freq = parseFloat(path.dataset.freq);
                const y = parseFloat(path.dataset.y);
                const smooth = parseFloat(path.dataset.smooth);
                
                let d = `M 0 ${y} `;
                for (let x = smooth; x <= BOARD_W; x += smooth) {
                    const wave = Math.sin(x * freq) * amp;
                    d += `L ${x} ${y + wave} `;
                }
                path.setAttribute('d', d);
            });

            // Update bulb positions
            document.querySelectorAll('.bulb').forEach(bulb => {
                const thread = parseInt(bulb.dataset.thread);
                const index = parseInt(bulb.dataset.index);
                const path = document.getElementById(`thread-${thread}`);
                
                if (!path) return;
                
                const totalBulbs = document.querySelectorAll(`.bulb[data-thread="${thread}"]`).length;
                const progress = (index + 1) / (totalBulbs + 1);
                const x = BOARD_W * progress;
                
                // Calculate position along the curved path
                const amp = parseFloat(path.dataset.amp);
                const freq = parseFloat(path.dataset.freq);
                const yBase = parseFloat(path.dataset.y);
                const wave = Math.sin(x * freq) * amp;
                const y = yBase + wave;
                
                bulb.style.left = x + 'px';
                bulb.style.top = y + 'px';
            });
        }

        function checkNoteCollision(newNote, existingNotes) {
            const newRect = {
                left: parseInt(newNote.el.style.left) - 100, // Note width is ~200px
                top: parseInt(newNote.el.style.top) - 80,   // Note height is ~160px
                right: parseInt(newNote.el.style.left) + 100,
                bottom: parseInt(newNote.el.style.top) + 80
            };

            for (const existingNote of existingNotes) {
                if (existingNote === newNote) continue;
                
                const existingRect = {
                    left: parseInt(existingNote.el.style.left) - 100,
                    top: parseInt(existingNote.el.style.top) - 80,
                    right: parseInt(existingNote.el.style.left) + 100,
                    bottom: parseInt(existingNote.el.style.top) + 80
                };

                // Check for collision
                if (newRect.left < existingRect.right &&
                    newRect.right > existingRect.left &&
                    newRect.top < existingRect.bottom &&
                    newRect.bottom > existingRect.top) {
                    return true; // Collision detected
                }
            }
            return false; // No collision
        }

        function findAvailablePosition(thread, existingNotes) {
            const gap = BOARD_H / (THREADS + 1);
            const yBase = gap * (thread + 1);
            const noteSpacing = 250; // Space between notes
            
            // Get all notes in this thread
            const threadNotes = existingNotes.filter(note => note.thread === thread);
            
            if (threadNotes.length === 0) {
                // No notes in this thread yet, place first note at a reasonable position
                return { x: 400, y: yBase };
            }
            
            // Try to place note after the last note in thread
            const lastNote = threadNotes[threadNotes.length - 1];
            let lastX = parseInt(lastNote.el.style.left);
            let lastY = parseInt(lastNote.el.style.top);
            
            // Try positions to the right, then below, then above
            const positions = [
                { x: lastX + noteSpacing, y: lastY }, // Right
                { x: lastX, y: lastY + noteSpacing }, // Below
                { x: lastX - noteSpacing, y: lastY }, // Left
                { x: lastX, y: lastY - noteSpacing }, // Above
                { x: lastX + noteSpacing, y: lastY + noteSpacing }, // Bottom-right
                { x: lastX + noteSpacing, y: lastY - noteSpacing }, // Top-right
                { x: lastX - noteSpacing, y: lastY + noteSpacing }, // Bottom-left
                { x: lastX - noteSpacing, y: lastY - noteSpacing }  // Top-left
            ];
            
            // Try each position
            for (const position of positions) {
                // Check boundaries
                if (position.x >= 200 && position.x <= BOARD_W - 200 &&
                    position.y >= 100 && position.y <= BOARD_H - 100) {
                    
                    // Create a temporary note to check collision
                    const tempNote = {
                        el: { style: { left: position.x + 'px', top: position.y + 'px' } }
                    };
                    
                    if (!checkNoteCollision(tempNote, existingNotes)) {
                        return position;
                    }
                }
            }
            
            // If all positions are occupied, find a random position
            for (let attempts = 0; attempts < 50; attempts++) {
                const randomX = 200 + Math.random() * (BOARD_W - 400);
                const randomY = 100 + Math.random() * (BOARD_H - 200);
                
                const tempNote = {
                    el: { style: { left: randomX + 'px', top: randomY + 'px' } }
                };
                
                if (!checkNoteCollision(tempNote, existingNotes)) {
                    return { x: randomX, y: randomY };
                }
            }
            
            // Fallback: place near the thread line
            return { x: 400 + Math.random() * (BOARD_W - 800), y: yBase };
        }

        // FIX #4: Correct the addNewNote function to send the right data to the API
        function addNewNote(name, message) {
            if (!name.trim() || !message.trim()) {
                showNotification('Please enter both name and message');
                return;
            }

            // Find the thread with the fewest notes
            let threadCounts = new Array(THREADS).fill(0);
            notes.forEach(note => {
                if (note.thread !== null) {
                    threadCounts[note.thread]++;
                }
            });
            
            // Find the thread with the minimum count
            let minThread = 0;
            let minCount = threadCounts[0];
            for (let i = 1; i < threadCounts.length; i++) {
                if (threadCounts[i] < minCount) {
                    minCount = threadCounts[i];
                    minThread = i;
                }
            }
            
            // Find available position for the new note
            const position = findAvailablePosition(minThread, notes);
            
            // Send data to server with correct field names
            showLoading();
            
            fetch('memories_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name: name.trim(),
                    message: message.trim(),
                    thread: minThread, // API expects 'thread'
                    ratio: 1.0, // API expects 'ratio'
                    x_position: position.x, // API expects 'x_position'
                    y_position: position.y, // API expects 'y_position'
                    manual: 1 // API expects 'manual'
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                hideLoading();
                
                if (data.success) {
                    // Clear input fields
                    document.getElementById('name').value = '';
                    document.getElementById('message').value = '';
                    
                    // Refresh notes
                    fetchNotes();
                    
                    showNotification('New memory added!');
                    
                    // Scroll to the new note
                    setTimeout(() => {
                        const x = position.x - (window.innerWidth / 2);
                        const y = position.y - (window.innerHeight / 2);
                        
                        WRAP.scrollTo({
                            left: x,
                            top: y,
                            behavior: 'smooth'
                        });
                    }, 100);
                } else {
                    showNotification(data.error || 'Failed to add memory');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error adding note:', error);
                showNotification('Failed to add memory. Check console for details.');
            });
        }

        function centerView() {
            WRAP.scrollTo({
                left: (BOARD_W * zoomLevel - WRAP.clientWidth) / 2,
                top: (BOARD_H * zoomLevel - WRAP.clientHeight) / 2,
                behavior: 'smooth'
            });
        }

        function resetBoard() {
            if (confirm('Reset all notes to their original positions?')) {
                fetchNotes();
                centerView();
                showNotification('Board reset');
            }
        }

        // Navigation functionality
        function setupNavigation() {
            // Hamburger menu toggle
            HAMBURGER.addEventListener('click', () => {
                NAV_MENU.classList.toggle('active');
                HAMBURGER.classList.toggle('active');
            });

            // Close mobile menu when clicking on a link
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', () => {
                    NAV_MENU.classList.remove('active');
                    HAMBURGER.classList.remove('active');
                });
            });

            // Navbar scroll effect
            window.addEventListener('scroll', () => {
                const navbar = document.querySelector('.navbar');
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        }

        // Game functionality
        function showGame() {
            GAME_CONTAINER.style.display = 'block';
            initQuiz();
        }

        function hideGame() {
            GAME_CONTAINER.style.display = 'none';
        }

        function initQuiz() {
            const options = document.querySelectorAll('.option');
            const wrongPopup = document.getElementById('wrongPopup');
            const correctPopup = document.getElementById('correctPopup');
            const correctImage = document.getElementById('correctImage');
            const memoryCaption = document.getElementById('memoryCaption');
            const closePopup = document.querySelectorAll('.close-popup');
            const scoreElement = document.getElementById('score');
            const progressElement = document.querySelector('.progress');
            const memoryBadges = document.getElementById('memoryBadges');
            const completionScreen = document.getElementById('completionScreen');
            const finalScore = document.getElementById('finalScore');
            const restartBtn = document.getElementById('restartBtn');
            
            let score = 0;
            let answeredQuestions = new Set();
            
            // Reset score and badges
            scoreElement.textContent = '0';
            memoryBadges.innerHTML = '';
            progressElement.style.setProperty('--progress', '0');
            
            // Reset all options
            options.forEach(option => {
                option.style.pointerEvents = 'auto';
                option.style.background = 'rgba(139, 19, 19, 0.5)';
                option.style.borderColor = 'rgba(255, 204, 0, 0.3)'; // Changed to yellow
                option.style.opacity = '1';
            });
            
            // Memory captions for each question
            const memoryCaptions = {
                question1: "Countless steps taken, conversations shared, and friendships forged on this iconic bridge.",
                question2: "The sound of water, the backdrop of countless photos, and the centerpiece of campus life.",
                question3: "Where knowledge was sought, dreams were nurtured, and futures were shaped.",
                question4: "Witness to countless cultural events, lectures, and moments of inspiration.",
                question5: "Where tradition met innovation, and craftsmanship was celebrated.",
                question6: "The excitement of innovation, the thrill of competition, and the joy of learning.",
                question7: "Where champions were made, records were broken, and spirits soared.",
                question8: "The hub of creativity, collaboration, and student initiatives.",
                question9: "The foundation of it all, where the journey began for generations.",
                question10: "A network that extends beyond campus, connecting PSGians across the world."
            };
            
            // Remove existing event listeners
            options.forEach(option => {
                const newOption = option.cloneNode(true);
                option.parentNode.replaceChild(newOption, option);
            });
            
            // Add new event listeners
            document.querySelectorAll('.option').forEach(option => {
                option.addEventListener('click', function() {
                    const isCorrect = this.getAttribute('data-correct') === 'true';
                    const questionContainer = this.closest('.question');
                    const questionId = questionContainer.id;
                    const imageContainer = questionContainer.querySelector('.image-container');
                    const imageSrc = imageContainer.querySelector('img').src;
                    
                    if (answeredQuestions.has(questionId)) {
                        return; // Already answered this question
                    }
                    
                    if (isCorrect) {
                        // Set the image source in the popup
                        correctImage.src = imageSrc;
                        
                        // Set the memory caption
                        memoryCaption.textContent = memoryCaptions[questionId] || "A special place in your PSG Tech journey.";
                        
                        // Show the correct answer popup
                        correctPopup.style.display = 'flex';
                        
                        // Update score
                        score++;
                        scoreElement.textContent = score;
                        
                        // Update progress bar
                        progressElement.style.setProperty('--progress', score);
                        
                        // Add memory badge
                        const badge = document.createElement('span');
                        badge.className = 'memory-badge';
                        badge.textContent = '✓';
                        memoryBadges.appendChild(badge);
                        
                        // Mark question as answered
                        answeredQuestions.add(questionId);
                        
                        // Disable all options in this question
                        const allOptions = questionContainer.querySelectorAll('.option');
                        allOptions.forEach(opt => {
                            opt.style.pointerEvents = 'none';
                            if (opt.getAttribute('data-correct') === 'true') {
                                opt.style.background = 'rgba(255, 204, 0, 0.5)'; // Changed to yellow
                                opt.style.borderColor = '#ffcc00'; // Changed to yellow
                            } else {
                                opt.style.opacity = '0.6';
                            }
                        });
                        
                        // Check if quiz is complete
                        if (answeredQuestions.size === 10) {
                            setTimeout(() => {
                                finalScore.textContent = score;
                                completionScreen.style.display = 'flex';
                            }, 1000);
                        }
                    } else {
                        // Show wrong answer popup
                        wrongPopup.style.display = 'flex';
                    }
                });
            });
            
            // Close popup buttons
            closePopup.forEach(button => {
                button.addEventListener('click', function() {
                    wrongPopup.style.display = 'none';
                    correctPopup.style.display = 'none';
                });
            });
            
            // Restart button
            restartBtn.addEventListener('click', function() {
                initQuiz();
            });
            
            // Close popup if clicked outside
            window.addEventListener('click', function(event) {
                if (event.target === wrongPopup) {
                    wrongPopup.style.display = 'none';
                }
                if (event.target === correctPopup) {
                    correctPopup.style.display = 'none';
                }
            });
        }

        // Initialize everything
        function init() {
            // Fetch notes from database on page load
            fetchNotes();
            
            // Create threads and bulbs
            makeThreads();
            
            // Set up navigation
            setupNavigation();
            
            // Event listeners
            WRAP.addEventListener('scroll', updateMinimapViewport);
            window.addEventListener('resize', updateMinimapViewport);
            
            document.getElementById('addBtn').addEventListener('click', () => {
                const name = document.getElementById('name').value;
                const message = document.getElementById('message').value;
                addNewNote(name, message);
            });
            
            document.getElementById('zoomInBtn').addEventListener('click', zoomIn);
            document.getElementById('zoomOutBtn').addEventListener('click', zoomOut);
            document.getElementById('zoomResetBtn').addEventListener('click', resetZoom);
            
            // Mini-map close button
            MINIMAP_CLOSE.addEventListener('click', (e) => {
                e.stopPropagation();
                if (isMinimapExpanded) {
                    isMinimapExpanded = false;
                    MINIMAP_CONTAINER.classList.remove('expanded');
                }
            });
            
            // Game link
            GAME_LINK.addEventListener('click', (e) => {
                e.preventDefault();
                showGame();
            });
            
            // Back button
            BACK_BTN.addEventListener('click', () => {
                hideGame();
            });
            
            // Mini-map click to zoom
            MINIMAP_CONTAINER.addEventListener('click', (e) => {
                if (e.target === MINIMAP_CONTAINER || e.target.classList.contains('minimap')) {
                    const rect = MINIMAP_CONTAINER.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    
                    zoomToArea(x, y);
                }
            });
            
            // Allow Enter key to submit
            document.getElementById('message').addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    document.getElementById('addBtn').click();
                }
            });
            
            // Initial mini-map viewport
            updateMinimapViewport();
            
            // Animation loop
            function animate() {
                updateThreadsAndBulbs();
                requestAnimationFrame(animate);
            }
            animate();
        }

        // Start the application
        init();
    </script>
</body>
</html>